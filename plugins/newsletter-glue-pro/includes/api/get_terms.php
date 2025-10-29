<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Get_Terms class.
 */
class NGL_REST_API_Get_Terms {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/get_terms', array(
			'methods' 	=> 'GET',
			'callback' 	=> array( $this, 'response' ),
			'permission_callback' => array( 'NGL_REST_API', 'authenticate' ),
		) );

	}

	/**
	 * Response.
	 */
	public function response( $request ) {

		$taxonomy = sanitize_text_field( $request->get_param( 'taxonomy' ) );

		$terms = get_terms( array(
			'taxonomy' 		=> $taxonomy,
			'hide_empty' 	=> false,
		) );

		$response = array();
		$response[] = array( 'value' => '', 'label' => 'Select...' );
		foreach( $terms as $term ) {
			$response[] = array( 'value' => $term->term_id, 'label' => $term->name );
		}

		return rest_ensure_response( $response );

	}

}

return new NGL_REST_API_Get_Terms();