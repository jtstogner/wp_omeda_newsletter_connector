<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Get_CSS_Settings class.
 */
class NGL_REST_API_Get_CSS_Settings {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/get_css_settings', array(
			'methods' 	=> 'GET',
			'callback' 	=> array( $this, 'response' ),
			'permission_callback' => array( 'NGL_REST_API', 'authenticate' ),
		) );

	}

	/**
	 * Response.
	 */
	public function response( $request ) {

		$data = array();

		$data[ 'customCSS' ] = wp_kses_post( wp_strip_all_tags( get_option( 'newsletterglue_css' ) ) );
		$data[ 'disableCSS' ] = absint( get_option( 'newsletterglue_disable_plugin_css' ) );

		$data = apply_filters( 'newsletterglue_get_css_settings', $data );

		return rest_ensure_response( $data );
	}

}

return new NGL_REST_API_Get_CSS_Settings();