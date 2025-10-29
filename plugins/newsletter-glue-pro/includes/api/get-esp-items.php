<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Get_ESP_Items class.
 */
class NGL_REST_API_Get_ESP_Items {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/get_esp_items', array(
			'methods' 	=> 'GET',
			'callback' 	=> array( $this, 'response' ),
			'permission_callback' => array( 'NGL_REST_API', 'authenticate' ),
		) );

	}

	/**
	 * Response.
	 */
	public function response( $request ) {

		$esp    = $request->get_param( 'esp' );

		// Load ESP and connect to it.
		include newsletterglue_get_path( $esp ) . '/init.php';
		$classname 	= 'NGL_' . ucfirst( $esp );
		$class		= new $classname();
		$class->connect();

		$data = call_user_func_array( array( $class, $request->get_param( 'callback' ) ), array( $request->get_param( 'parameter' ) ) );

		$_items = array();
		foreach( $data as $i => $c ) {
			$_items[] = array(
				'value' => $i,
				'label' => $c
			);
		}

		return rest_ensure_response( $_items );
	}


}

return new NGL_REST_API_Get_ESP_Items();