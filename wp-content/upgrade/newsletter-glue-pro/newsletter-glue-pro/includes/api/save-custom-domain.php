<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Save_Custom_Domain class.
 */
class NGL_REST_API_Save_Custom_Domain {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/save_custom_domain', array(
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

		$domain = isset( $data[ 'ngDomain' ] ) ? sanitize_text_field( wp_unslash( $data['ngDomain'] ) ) : '';

		if ( ! empty( $domain ) && ( $domain != 'undefined' ) ) {
			update_option( 'newsletterglue_home_url', esc_url_raw( untrailingslashit( $domain ) ) );
		} else {
			delete_option( 'newsletterglue_home_url' );
		}

		return rest_ensure_response( array( 'done' => true ) );

	}

}

return new NGL_REST_API_Save_Custom_Domain();