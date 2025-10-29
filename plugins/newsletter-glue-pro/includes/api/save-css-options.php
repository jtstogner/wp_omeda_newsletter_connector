<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Save_CSS_Options class.
 */
class NGL_REST_API_Save_CSS_Options {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/save_css_options', array(
			'methods' 	=> 'POST',
			'callback' 	=> array( $this, 'response' ),
			'permission_callback' => array( 'NGL_REST_API', 'authenticate' )
		) );

	}

	/**
	 * Response.
	 */
	public function response( $request ) {

		if ( ! newsletterglue_is_activated() ) {
			return rest_ensure_response( array( 'unverified' => true ) );
		}

		$response = array();

		$data = json_decode( $request->get_body(), true );

		$removeCSSFront = ! empty( $data[ 'removeCSSFront' ] ) ? true : false;

		update_option( 'newsletterglue_disable_front_css', $removeCSSFront );

		return rest_ensure_response( array( 'done' => true ) );

	}

}

return new NGL_REST_API_Save_CSS_Options();
