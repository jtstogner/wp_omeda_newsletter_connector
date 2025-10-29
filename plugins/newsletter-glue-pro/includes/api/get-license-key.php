<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Get_License_Key class.
 */
class NGL_REST_API_Get_License_Key {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/get_license_key', array(
			'methods' 	=> 'GET',
			'callback' 	=> array( $this, 'response' ),
			'permission_callback' => array( 'NGL_REST_API', 'authenticate' ),
		) );

	}

	/**
	 * Response.
	 */
	public function response( $request ) {

		$data = array(
			'license_key' 	=> get_option( 'newsletterglue_pro_license', '' ),
		);

		$data = apply_filters( 'newsletterglue_get_license_key', $data );

		return rest_ensure_response( $data );
	}

}

return new NGL_REST_API_Get_License_Key();