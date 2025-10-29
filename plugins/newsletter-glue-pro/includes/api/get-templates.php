<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Get_Templates class.
 */
class NGL_REST_API_Get_Templates {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/get_templates', array(
			'methods' 	=> 'GET',
			'callback' 	=> array( $this, 'response' ),
			'permission_callback' => array( 'NGL_REST_API', 'authenticate' ),
		) );

	}

	/**
	 * Response.
	 */
	public function response( $request ) {

		$category_id = absint( $request->get_param( 'category_id' ) );
		$query		 = sanitize_text_field( $request->get_param( 'query' ) );


		$args =	array(
			'posts_per_page' 	=> -1,
			'post_type' 		=> 'ngl_template',
			'post_status'		=> 'publish',
			'orderby'			=> 'title',
			'order'				=> 'asc',
		);

		if ( $category_id ) {
			$args[ 'tax_query' ] = array( // phpcs:ignore
				array(
					'taxonomy' 	=> 'ngl_template_category',
					'field' 	=> 'term_id',
					'terms' 	=> $category_id,
				)
			);
		}

		if ( $query ) {
			$args[ 's' ] = esc_attr( $query );
		}

		if ( ! empty( $request->get_param( 'automations_only' ) ) ) {
			$term = get_term_by( 'slug', 'automations', 'ngl_template_category' );
			if ( $term && ! empty( $term->term_id ) ) {
				$args[ 'tax_query' ] = array( // phpcs:ignore
					array(
						'taxonomy' 	=> 'ngl_template_category',
						'field' 	=> 'term_id',
						'terms' 	=> $term->term_id,
					)
				);
			}
		}

		$results = get_posts( $args );

		$previews = array();

		if ( $results ) {
			foreach( $results as $result ) {
				$previews[ $result->ID ] = add_query_arg( 'iframe', 'true', newsletterglue_generate_web_link( $result->ID ) );
			}
		}
	
		return rest_ensure_response( array( 'category_id' => $category_id, 'query' => $query, 'results' => $results, 'previews' => $previews, 'count' => count( $results ) ) );

	}

}

return new NGL_REST_API_Get_Templates();