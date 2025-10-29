<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Get_Wizard_Settings class.
 */
class NGL_REST_API_Get_Wizard_Settings {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/pattern_settings', array(
			'methods' 	=> 'GET',
			'callback' 	=> array( $this, 'response' ),
			'permission_callback' => array( 'NGL_REST_API', 'authenticate' ),
		) );

	}

	/**
	 * Response.
	 */
	public function response( $request ) {

		$logo_id 	= get_option( 'newsletterglue_logo_id' );
		$logo_url 	= $logo_id ? wp_get_attachment_url( $logo_id ) : '';

		$data = array(
			'admin_name' 	=> get_option( 'newsletterglue_admin_name' ) ? get_option( 'newsletterglue_admin_name' ) : get_bloginfo( 'name' ),
			'admin_address' => get_option( 'newsletterglue_admin_address' ),
			'logo_id'		=> $logo_id,
			'logo_url'		=> $logo_url,
			'logo_width'    => get_option( 'newsletterglue_logo_width' ),
		);

		// Get social links.
		$socials = array( 'instagram_url', 'tiktok_url', 'twitter_url', 'facebook_url', 'linkedin_url', 'twitch_url', 'youtube_url' );
		foreach( $socials as $social ) {
			$data[ $social ] = get_option( 'newsletterglue_' . $social );
		}

		$data = apply_filters( 'newsletterglue_get_wizard_settings', $data );

		return rest_ensure_response( $data );
	}

}

return new NGL_REST_API_Get_Wizard_Settings();