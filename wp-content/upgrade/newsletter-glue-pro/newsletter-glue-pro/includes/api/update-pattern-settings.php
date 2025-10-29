<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Update_Wizard_Settings class.
 */
class NGL_REST_API_Update_Wizard_Settings {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/pattern_settings', array(
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

		if ( isset( $data[ 'admin_name' ] ) ) {
			update_option( 'newsletterglue_admin_name', $data[ 'admin_name' ] );
		}

		if ( isset( $data[ 'admin_address' ] ) ) {
			update_option( 'newsletterglue_admin_address', $data[ 'admin_address' ] );

			$tags = get_option( 'newsletterglue_merge_tag_fallbacks' );
			$tags[ 'admin_address' ] = $data[ 'admin_address' ];
			update_option( 'newsletterglue_merge_tag_fallbacks', $tags );
		}

		if ( isset( $data[ 'logo_id' ] ) ) {
			update_option( 'newsletterglue_logo_id', absint( $data[ 'logo_id' ] ) );
			update_option( 'newsletterglue_logo', esc_url( $data[ 'logo_url' ] ) );
		}

		if ( isset( $data[ 'logo_width' ] ) ) {
			update_option( 'newsletterglue_logo_width', absint( $data[ 'logo_width' ] ) );
		}

		// Social links.
		$socials = array( 'instagram_url', 'tiktok_url', 'twitter_url', 'facebook_url', 'linkedin_url', 'twitch_url', 'youtube_url' );
		foreach( $socials as $social ) {
			if ( isset( $data[ $social ] ) ) {
				update_option( 'newsletterglue_' . $social, esc_url( $data[ $social ] ) );
			}
		}

		do_action( 'newsletterglue_save_template_wizard_settings', $data );

		return rest_ensure_response( $data );
	}

}

return new NGL_REST_API_Update_Wizard_Settings();