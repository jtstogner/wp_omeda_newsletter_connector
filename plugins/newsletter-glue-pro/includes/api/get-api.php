<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Get_API class.
 */
class NGL_REST_API_Get_API {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/get_api', array(
			'methods' 	=> 'GET',
			'callback' 	=> array( $this, 'response' ),
			'permission_callback' => array( 'NGL_REST_API', 'authenticate' ),
		) );

	}

	/**
	 * Response.
	 */
	public function response( $request ) {

		$data 		= array();
		$service 	= newsletterglue_default_connection();
		$api 		= get_option( 'newsletterglue_integrations' );

		$data[ 'service' ] = $service;

		if ( $service ) {
			$data[ 'api_key' ] 		= isset( $api[ $service ][ 'api_key' ] ) ? $api[ $service ][ 'api_key' ] : '';
			$data[ 'api_url' ] 		= isset( $api[ $service ][ 'api_url' ] ) ? $api[ $service ][ 'api_url' ] : '';
			$data[ 'api_secret' ] 	= isset( $api[ $service ][ 'api_secret' ] ) ? $api[ $service ][ 'api_secret' ] : '';
			$data[ 'esp_title' ] 	= newsletterglue_get_name( $service );
		}
       
        $data[ 'esp_list' ] = newsletterglue_get_esp_list();
  
		$data = apply_filters( 'newsletterglue_get_api', $data );

		return rest_ensure_response( $data );
	}

}

return new NGL_REST_API_Get_API();