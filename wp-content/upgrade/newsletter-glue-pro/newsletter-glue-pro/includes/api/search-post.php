<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Search_Post class.
 */
class NGL_REST_API_Search_Post {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/search_post', array(
			'methods' 	=> 'POST',
			'callback' 	=> array( $this, 'response' ),
			'permission_callback' => array( 'NGL_REST_API', 'authenticate' ),
		) );

	}

	/**
	 * Response.
	 */
	public function response( $request ) {

		$results = array();

		$result = null;

		$data = json_decode( $request->get_body(), true );

		$query = isset( $data[ 'query' ] ) ? sanitize_text_field( wp_unslash( $data[ 'query' ] ) ) : '';

		add_filter( 'posts_where', array( $this, 'post_title_filter' ), 10, 2 );

		$results = new WP_Query( array(
			'post_type'      		=> array_keys( get_post_types( array( 'public' => true ) ) ),
			'post_status'    		=> 'publish',
			'posts_per_page' 		=> 100,
			'ngl_post_title_s'  	=> $query, // search post title only
			'ignore_sticky_posts'	=> true,
		) );

		remove_filter( 'posts_where', array( $this, 'post_title_filter' ), 10, 2 );

		$items = array();

		if ( ! empty( $results->posts ) ) {
			foreach ( $results->posts as $result ) {
				$items[] = array(
					'post_id' 	=> $result->ID,
					'url'		=> get_permalink( $result->ID ),
					'title'		=> $result->post_title,
				);
			}
		}

		return rest_ensure_response( array( 'query' => $query, 'items' => $items ) );
	}

	/**
	 * Add search to post titles only.
	 */
	public function post_title_filter( $where, $wp_query ) {
		global $wpdb;
		if ( $term = $wp_query->get( 'ngl_post_title_s' ) ) {
			$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . $wpdb->esc_like( $term ) . '%\'';
		}
		return $where;
	}

}

return new NGL_REST_API_Search_Post();