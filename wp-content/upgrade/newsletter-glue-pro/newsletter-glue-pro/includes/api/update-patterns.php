<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Update_Default_Patterns class.
 */
class NGL_REST_API_Update_Default_Patterns {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/update_patterns', array(
			'methods' 	=> 'POST',
			'callback' 	=> array( $this, 'response' ),
			'permission_callback' => array( 'NGL_REST_API', 'authenticate' )
		) );

	}

	/**
	 * Response.
	 */
	public function response( $request ) {

		$data = json_decode( $request->get_body(), true );

		// Mark wizard as done.
		update_option( 'newsletterglue_template_wizard_done', 'yes' );
		update_option( 'newsletterglue_onboarding_done', 'yes' );

		$this->update_patterns();

		// May need to recreate demo post.
		if ( isset( $data[ 'recreate_demo' ] ) ) {
			$demo_post = newsletterglue_create_demo_post();
			if ( $demo_post ) {
				$data[ 'redirect_to' ] = get_edit_post_link( $demo_post );
			}
		}

		do_action( 'newsletterglue_update_patterns', $data );

		return rest_ensure_response( $data );
	}

	/**
	 * Update patterns.
	 */
	public function update_patterns() {

		$args = array(
			'numberposts'	=> -1,
			'post_type' 	=> 'ngl_pattern',
			'meta_query' 	=> array( // phpcs:ignore
				array(
					'key'     => '_ngl_core_pattern',
					'value'   => '',
					'compare' => '!='
				),
			),
		);

		$patterns = get_posts( $args );

		include_once NGL_PLUGIN_DIR . 'includes/cpt/default-patterns.php';
		$patterns_api = new NGL_Default_Patterns();

		$pattern_keys = array_keys( $patterns_api->get_patterns() );

		$published = array();

		if ( $patterns ) {
			foreach( $patterns as $pattern ) {
				$is_core = get_post_meta( $pattern->ID, '_ngl_core_pattern', true );
				if ( $is_core ) {
					$published[] = $is_core;
				}
				wp_update_post( array( 'ID' => $pattern->ID, 'post_content' => $this->update_content( $pattern->post_content ) ) );
			}
		}

		foreach( $pattern_keys as $key ) {
			if ( ! in_array( $key, $published ) ) {
				$patterns_api->create( $key );
			}
		}
	}

	/**
	 * Update content.
	 */
	public function update_content( $content ) {

		include_once NGL_PLUGIN_DIR . 'includes/cpt/default-patterns.php';

		$patterns = new NGL_Default_Patterns();

		// Replace the merge tags.
		preg_match_all( "/{{(.*?)}}/", $content, $matches );

		if ( ! empty( $matches[0] ) ) {
			$results = $matches[0];
			foreach( $results as $result ) {

				$clean = explode( ',fallback=', $result );
				$tag   = trim( str_replace( array( '{{', '}}' ), '', $clean[0] ) );

				if ( $tag == 'admin_name' ) {
					$admin_name = get_option( 'newsletterglue_admin_name' );
					$content = str_replace( $result, '{{ admin_name,fallback=' . $admin_name . ' }}', $content );
				}

				if ( $tag == 'admin_address' ) {
					$admin_address = get_option( 'newsletterglue_admin_address' );
					$content = str_replace( $result, '{{ admin_address,fallback=' . $admin_address . ' }}', $content );
				}

			}
		}

		// Replace social links.
		$html = str_get_html( $content );
		foreach( $html->find( '.wp-block-newsletterglue-share.ngl-image-size-18' ) as $key => $element ) {
			$html->find( '.wp-block-newsletterglue-share.ngl-image-size-18', $key )->innertext = $patterns->create_social_links_markup( 18, 'default', 'grey' );;
		}
		foreach( $html->find( '.wp-block-newsletterglue-share.ngl-image-size-20' ) as $key => $element ) {
			$html->find( '.wp-block-newsletterglue-share.ngl-image-size-20', $key )->innertext = $patterns->create_social_links_markup( 20, 'default', 'white' );
		}
		$html->save();
		$content = (string) $html;

		// Replace logo.
		$logo_id 	= get_option( 'newsletterglue_logo_id' );
		$logo    	= wp_get_attachment_url( $logo_id );

		if ( empty( $logo_id ) || ! $logo ) {
			return $content;
		}

		$data  		= wp_get_attachment_image_src( $logo_id, 'full' );
		$width 		= $data[1];
		$height 	= $data[2];
		$max_logo_w = get_option( 'newsletterglue_logo_width' );

		if ( $max_logo_w && $width > $max_logo_w ) {
			$ratio = $width / $height;
			$n_width = $max_logo_w;
			$n_height = ceil( $max_logo_w / $ratio );
		} else {
			$n_width = $width;
			$n_height = $height;
		}

		$html = str_get_html( $content );
		$replaces = array();
		foreach( $html->find( '.newsletterglue-logo img' ) as $key => $element ) {
			$old_w = $html->find( '.newsletterglue-logo img', $key )->width;
			$old_h = $html->find( '.newsletterglue-logo img', $key )->height;
			$html->find( '.newsletterglue-logo img', $key )->src = $logo;
			$html->find( '.newsletterglue-logo img', $key )->width = $n_width;
			$html->find( '.newsletterglue-logo img', $key )->height = $n_height;
			$replaces[ '"width":' . $old_w . ',"height":' . $old_h ] = '"width":' . $n_width . ',"height":' . $n_height;
		}
		$html->save();
		$content = (string) $html;

		if ( ! empty( $replaces ) ) {
			foreach( $replaces as $pattern => $value ) {
				$content = str_replace( $pattern, $value, $content );
			}
		}

		return $content;
	}

}

return new NGL_REST_API_Update_Default_Patterns();