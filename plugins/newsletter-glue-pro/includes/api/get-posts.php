<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Get_Posts class.
 */
class NGL_REST_API_Get_Posts {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/get_posts', array(
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

		$data = json_decode( $request->get_body(), true );

		$insert_rss_posts = isset( $data[ 'insertRssPosts' ] ) ? $data[ 'insertRssPosts' ] : false;
		$rss_feed 			= isset( $data[ 'rssfeed' ] ) ? $data[ 'rssfeed' ] : '';

		$per_page 		= isset( $data[ 'posts_num' ] ) ? absint( $data[ 'posts_num' ] ) : 0;
		$words_num 		= isset( $data[ 'words_num' ] ) ? absint( $data[ 'words_num' ] ) : 30;
		$sortby 		= isset( $data[ 'sortby' ] ) ? $data[ 'sortby' ] : '';
		$contentstyle 	= isset( $data[ 'contentstyle' ] ) ? sanitize_text_field( wp_unslash( $data[ 'contentstyle' ] ) ) : 'multi';
		$postlength 	= isset( $data[ 'postlength' ] ) ? sanitize_text_field( wp_unslash( $data[ 'postlength' ] ) ) : 'excerpt';
		$columns_num 	= isset( $data[ 'columns_num' ] ) ? sanitize_text_field( wp_unslash( $data[ 'columns_num' ] ) ) : 'one';
		$filter 		= isset( $data[ 'filter' ] ) ? sanitize_text_field( wp_unslash( $data[ 'filter' ] ) ) : 'include';
		$authors 		= isset( $data[ 'filter_authors' ] ) ? $data[ 'filter_authors' ] : '';
		$post_types 	= isset( $data[ 'filter_cpts' ] ) ? $data[ 'filter_cpts' ] : '';
		$categories 	= isset( $data[ 'filter_categories' ] ) ? $data[ 'filter_categories' ] : '';
		$tags 			= isset( $data[ 'filter_tags' ] ) ? $data[ 'filter_tags' ] : '';
		$table_ratio    = ! empty( $data[ 'table_ratio' ] ) ? esc_attr( $data[ 'table_ratio' ] ) : '30_70';

		$taxonomies    = isset( $data[ 'taxonomies' ] ) ? $data[ 'taxonomies' ] : '';

		$dates 				= isset( $data[ 'dates' ] ) ? $data[ 'dates' ][ 'value' ] : '';
		$week_starts 		= isset( $data[ 'week_starts' ] ) ? $data[ 'week_starts' ][ 'value' ] : 'Monday';
		$two_weeks_starts 	= isset( $data[ 'two_weeks_starts' ] ) ? $data[ 'two_weeks_starts' ][ 'value' ] : 'Monday';
		$month_starts 		= isset( $data[ 'month_starts' ] ) ? $data[ 'month_starts' ][ 'value' ] : 'Monday';
		$starts_time 		= isset( $data[ 'starts_time' ] ) ? $data[ 'starts_time' ][ 'value' ] : '7pm';

		// Get one post only.
		if ( $contentstyle === 'single' ) {
			$per_page = 1;
		}

		$args = array(
			'posts_per_page'		=> empty( $per_page ) ? 99 : $per_page,
			'post_type'				=> apply_filters( 'newsletterglue_latest_posts_post_type', 'post' ),
			'post_status'			=> array( 'publish' ),
			'ignore_sticky_posts'	=> true,
			'insert_rss_posts'		=> $insert_rss_posts,
			'rss_feed'				=> $rss_feed,
		);

		// Post type filters.
		if ( ! empty( $post_types ) ) {
			$cpts = array();
			foreach( $post_types as $key => $value ) {
				$cpts[] = $value[ 'value' ];
			}
			if ( ! empty( $cpts ) ) {
				$args[ 'post_type' ] = $cpts;
			}
		}

		// Add sorting parameter.
		if ( ! empty( $sortby[ 'value' ] ) ) {
			if ( $sortby[ 'value' ] === 'oldest' ) {
				$args[ 'orderby' ] = 'date';
				$args[ 'order' ] = 'ASC';
			}
			if ( $sortby[ 'value' ] === 'newest' ) {
				$args[ 'orderby' ] = 'date';
				$args[ 'order' ] = 'DESC';
			}
			if ( $sortby[ 'value' ] === 'alphabetic' ) {
				$args[ 'orderby' ] = 'title';
				$args[ 'order' ] = 'ASC';
			}
		}

		// Category filters.
		if ( ! empty( $categories ) ) {
			$cat_terms = array();
			foreach( $categories as $key => $value ) {
				$cat_terms[] = $value[ 'value' ];
			}
			$filter_type = $filter === 'exclude' ? 'category__not_in' : 'category__in';
			if ( ! empty( $cat_terms ) ) {
				$args[ $filter_type ] = $cat_terms;
			}
		}

		// Tag filters.
		if ( ! empty( $tags ) ) {
			$tag_terms = array();
			foreach( $tags as $key => $value ) {
				$tag_terms[] = $value[ 'value' ];
			}
			$filter_type = $filter === 'exclude' ? 'tag__not_in' : 'tag__in';
			if ( ! empty( $tag_terms ) ) {
				$args[ $filter_type ] = $tag_terms;
			}
		}

		// Taxonomy filters.
		if ( ! empty( $taxonomies ) ) {
			$tax_rules = array( 'relation' => 'OR' );
			foreach( $taxonomies as $index => $tax ) {
				if ( empty( $tax[ 'term' ] ) )
					continue;
				$tax_rules[] = array(
					'taxonomy'	=> $tax[ 'key' ],
					'field'     => 'term_id',
					'terms'		=> absint( $tax[ 'term' ] ),
				);
			}
			$args[ 'tax_query' ] = array( $tax_rules ); // phpcs:ignore
		}

		// Author filters.
		if ( ! empty( $authors ) ) {
			$users = array();
			foreach( $authors as $key => $value ) {
				$users[] = $value[ 'value' ];
			}
			$filter_type = $filter === 'exclude' ? 'author__not_in' : 'author__in';
			if ( ! empty( $users ) ) {
				$args[ $filter_type ] = $users;
			}
		}

		// Build date queries.
		if ( ! empty( $dates ) ) {
			if ( $dates === 'last_1' ) {
				$args[ 'date_query' ] = array(
					'after'     => gmdate( 'm/d/Y g:ia', strtotime( "-24 hours" ) ),
				);
			}

			if ( $dates === 'last_7' ) {
				$args[ 'date_query' ] = array(
					'after'     => gmdate( 'm/d/Y g:ia', strtotime( "-7 days" ) ),
				);
			}

			if ( $dates === 'last_14' ) {
				$args[ 'date_query' ] = array(
					'after'     => gmdate( 'm/d/Y g:ia', strtotime( "-14 days" ) ),
				);
			}

			if ( $dates === 'last_30' ) {
				$args[ 'date_query' ] = array(
					'after'     => gmdate( 'm/d/Y g:ia', strtotime( "-30 days" ) ),
				);
			}

			if ( $dates === 'last_60' ) {
				$args[ 'date_query' ] = array(
					'after'     => gmdate( 'm/d/Y g:ia', strtotime( "-60 days" ) ),
				);
			}

		}

		// Run the query.
		$the_query = new WP_Query(); 

		$posts = $the_query->query( apply_filters( 'newsletterglue_latest_posts_query_args', $args, $data ) );

		if ( ! empty( $posts ) ) {

			$thumb_size = 'full';

			foreach( $posts as $post ) {
				$image_arr	= wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), apply_filters( 'newsletterglue_latest_posts_thumbnail_size', $thumb_size, $table_ratio, $post ) );
				$featured 	= empty( $image_arr ) ? NGL_PLUGIN_URL . 'assets/images/placeholder.png' : $image_arr[0];

				$results[] = array(
					'id'				=> $post->ID,
					'post_title'		=> $post->post_title,
					'featured_image'	=> $featured,
					'thumbnail_id'		=> get_post_thumbnail_id( $post->ID ),
					'post_content'		=> $this->get_excerpt( $post, $words_num, $postlength, $contentstyle ),
					'domain'			=> $this->get_domain(),
					'categories'		=> $this->get_categories_text( $post->ID ),
					'tags'				=> $this->get_categories_text( $post->ID, 'post_tag' ),
					'permalink'			=> apply_filters( 'newsletterglue_latest_posts_perma', get_permalink( $post->ID ), $post->ID ),
					'author'			=> apply_filters( 'newsletterglue_latest_posts_author', $this->get_author( $post ), $post ),
				);
			}
			
			// Apply the same filter that's used in the get_posts method in the Latest Posts block renderer.
			// This ensures consistency between the editor preview and the email output.
			$block_key = md5(json_encode($data));
			$results = apply_filters('newsletterglue_latest_posts_results', $results, $args, $block_key);
		}

		return rest_ensure_response( array( 'args' => $args, 'posts' => $results, 'hash' => md5( wp_json_encode( $results ) ) ) );

	}

	/**
	 * Get post author.
	 */
	public function get_author( $post ) {
		return sprintf( __( 'By %s', 'newsletter-glue' ), get_the_author_meta( 'display_name', $post->post_author ) );
	}

	/**
	 * Get post content.
	 */
	public function get_excerpt( $post = null, $words = 30, $postlength = 'excerpt', $contentstyle = 'multi' ) {
		$content = $post->post_content;
		$excerpt = $post->post_excerpt;

		$content = strip_shortcodes( html_entity_decode( $content ) );
		$content = apply_filters( 'the_content', $content );
		$content = str_replace(']]>', ']]&gt;', $content);
		$content = preg_replace( '/\[([^\[\]]++|(?R))*+\]/', '', $content );

		if ( ( $postlength === 'full' && $contentstyle === 'single' ) || $words == 0 ) {
			return apply_filters( 'newsletterglue_email_custom_content', wpautop( $content ), $post, $content, $words, $postlength, $contentstyle );
		} else {
			if ( ! empty( $excerpt ) ) {
				$text = $excerpt;
			} else {
				$content = strip_shortcodes( html_entity_decode( $content ) );
				$content = apply_filters( 'the_content', $content );
				$content = str_replace(']]>', ']]&gt;', $content);
				$text = $content;
			}

			$excerpt_length = apply_filters( 'newsletterglue_get_excerpt_length', $words );

			$allowed = array(
				'strong'	=> array(),
				'em'		=> array(),
				'b'			=> array(),
				'i'			=> array(),
				'span'		=> array(),
				'a'			=> array(
					'href' 	=> array(),
				),
			);

			$text = wp_kses( $text, $allowed );

			$text = wp_trim_words( $text, $excerpt_length, '...' );

			$text = '<p>' . wp_kses_post( html_entity_decode( $text ) ) . '</p>';

			return apply_filters( 'newsletterglue_email_custom_excerpt', $text, $post, $content, $words, $postlength, $contentstyle );
		}
	}

	/**
	 * Get domain.
	 */
	public function get_domain() {
		$parse = wp_parse_url( home_url() );
		if ( isset( $parse[ 'host' ] ) ) {
			return str_replace( 'www.', '', $parse['host'] );
		}
		return home_url();
	}

	/**
	 * Get categories as label.
	 */
	public function get_categories_text( $post_id, $taxonomy = 'category' ) {
		$output_array = array();
		$categories = wp_get_object_terms( $post_id, $taxonomy );
		if ( ! empty( $categories ) ) {
			foreach( $categories as $category ) {
				$output_array[] = $category->name;
			}
		}
		return implode( ', ', $output_array );
	}

}

return new NGL_REST_API_Get_Posts();
