<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Save_CSS_Settings class.
 */
class NGL_REST_API_Save_CSS_Settings {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/save_css_settings', array(
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

		$css = isset( $data[ 'customCSS' ] ) ? nl2br( wp_kses_post( wp_unslash( $data['customCSS'] ) ) ) : '';

		$disable_css = ! empty( $data[ 'disableCSS' ] ) ? 1 : 0;

		update_option( 'newsletterglue_css', $css );

		update_option( 'newsletterglue_disable_plugin_css', $disable_css );

		do_action( 'newsletterglue_update_css_options', $data );

		return rest_ensure_response( $response );

	}

}

return new NGL_REST_API_Save_CSS_Settings();