<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Verify_API class.
 */
class NGL_REST_API_Verify_API {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/verify_api', array(
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

		$api_key = isset( $data[ 'api_key' ] ) ? esc_attr( $data[ 'api_key' ] ) : '';
		$api_url = isset( $data[ 'api_url' ] ) ? esc_url( $data[ 'api_url' ] ) : '';
		$api_secret = isset( $data[ 'api_secret' ] ) ? esc_url( $data[ 'api_secret' ] ) : '';
		$esp 	 = isset( $data[ 'esp' ] ) ? esc_attr( $data[ 'esp' ] ) : '';

		include_once newsletterglue_get_path( $esp ) . '/init.php';

		$classname 	= 'NGL_' . ucfirst( $esp );
		$api		= new $classname();
		$result 	= $api->add_integration( $data );

		if ( $result[ 'response' ] === 'invalid' ) {
			$response = array( 'error' => __( 'Your API key is invalid.', 'newsletter-glue' ) );
		}

		if ( $result[ 'response' ] === 'successful' ) {
			$response = array( 'success' => __( 'Your API key is valid.', 'newsletter-glue' ) );
		}

		return rest_ensure_response( $response );
	}

}

return new NGL_REST_API_Verify_API();