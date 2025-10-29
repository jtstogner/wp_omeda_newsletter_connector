<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Save_Custom_Slug class.
 */
class NGL_REST_API_Save_Custom_Slug {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/save_custom_slug', array(
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

		$custom_slug = isset( $data[ 'ngSlug' ] ) ? sanitize_text_field( wp_unslash( $data['ngSlug'] ) ) : '';

		if ( ! empty( $custom_slug ) ) {
			update_option( 'newsletterglue_post_type_ep', $custom_slug );
			delete_option( 'newsletterglue_flushed_rewrite' );
		}

		return rest_ensure_response( array( 'done' => true ) );

	}

}

return new NGL_REST_API_Save_Custom_Slug();