<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Reset_Theme class.
 */
class NGL_REST_API_Reset_Theme {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/reset_theme_settings', array(
			'methods' 	=> 'POST',
			'callback' 	=> array( $this, 'response' ),
			'permission_callback' => array( 'NGL_REST_API', 'authenticate' )
		) );

	}

	/**
	 * Response.
	 */
	public function response( $request ) {

		delete_option( 'newsletterglue_theme' );
		delete_option( 'newsletterglue_credits' );

		return rest_ensure_response( array( 'success' => 'true' ) );

	}

}

return new NGL_REST_API_Reset_Theme();