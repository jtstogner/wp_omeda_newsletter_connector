<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Verify_Email class.
 */
class NGL_REST_API_Verify_Email {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/verify_email', array(
			'methods' 	=> 'POST',
			'callback' 	=> array( $this, 'response' ),
			'permission_callback' => array( 'NGL_REST_API', 'authenticate' ),
		) );

	}

	/**
	 * Response.
	 */
	public function response( $request ) {

		$data = json_decode( $request->get_body(), true );

		$email   = isset( $data[ 'email' ] ) ? $data[ 'email' ] : '';
		$esp 	 = isset( $data[ 'esp' ] ) ? esc_attr( $data[ 'esp' ] ) : '';

		include_once newsletterglue_get_path( $esp ) . '/init.php';

		$classname 	= 'NGL_' . ucfirst( $esp );
		$api		= new $classname();
		$response 	= $api->verify_email( $email );

		return rest_ensure_response( $response );
	}

}

return new NGL_REST_API_Verify_Email();