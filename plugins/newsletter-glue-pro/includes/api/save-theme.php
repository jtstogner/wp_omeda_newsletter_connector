<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Save_Theme class.
 */
class NGL_REST_API_Save_Theme {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/save_theme_settings', array(
			'methods' 	=> 'POST',
			'callback' 	=> array( $this, 'response' ),
			'permission_callback' => array( 'NGL_REST_API', 'authenticate' )
		) );

	}

	/**
	 * Response.
	 */
	public function response( $request ) {

		$response = array();

		$data = json_decode( $request->get_body(), true );

		$changes 	= isset( $data[ 'changes' ] ) ? $data[ 'changes' ] : '';
		$css 		= isset( $data[ 'customCSS' ] ) ? nl2br( wp_kses_post( wp_unslash( $data['customCSS'] ) ) ) : '';
		$post_id 	= isset( $data[ 'post_id' ] ) ? $data[ 'post_id' ] : '';

		if ( $post_id ) {
			$theme = get_post_meta( $post_id, '_newsletterglue_theme', true );
			if ( empty( $theme ) ) {
				$theme = array();
			}
		} else {
			$theme = get_option( 'newsletterglue_theme' );
		}

		foreach( $changes as $index => $change ) {
			if ( isset( $change[ 'key' ] ) ) {
				if ( $change[ 'key' ] === 'p_font' ) {
					$theme[ 'font' ] = esc_attr( $change[ 'value' ] );
				}
				$theme[ $change[ 'key' ] ] = esc_attr( $change[ 'value' ] );
			} else {
				foreach( $change as $sub => $subvalue ) {
					$theme[ $subvalue[ 'key' ] ] = esc_attr( $subvalue[ 'value' ] );
				}
			}
		}

		if ( $post_id ) {
			update_post_meta( $post_id, '_newsletterglue_theme', $theme );
			update_post_meta( $post_id, '_newsletterglue_css', $css );
		} else {
			update_option( 'newsletterglue_theme', $theme );
			update_option( 'newsletterglue_css', $css );
		}

		return rest_ensure_response( array( 'changes' => $changes ) );

	}

}

return new NGL_REST_API_Save_Theme();