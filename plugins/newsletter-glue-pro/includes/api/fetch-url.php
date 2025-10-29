<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * NGL_REST_API_Fetch_URL class.
 */
class NGL_REST_API_Fetch_URL {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route(
			'newsletterglue/' . newsletterglue()->api_version(),
			'/fetch_url',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'response' ),
				'permission_callback' => array( 'NGL_REST_API', 'authenticate' ),
			)
		);
	}

	/**
	 * Response.
	 */
	public function response( $request ) {

		$results = array();

		$result = null;

		$data = json_decode( $request->get_body(), true );

		$id = isset( $data['url'] ) ? sanitize_text_field( wp_unslash( $data['url'] ) ) : '';

		if ( is_numeric( $id ) ) {
			$item = get_post( $id );
		} else {
			$post_id = url_to_postid( $id ); // phpcs:ignore
			$item    = get_post( $post_id );
		}

		if ( ! isset( $item->ID ) || empty( $item->ID ) ) {
			$id = strpos( $id, 'http' ) !== 0 ? "https://$id" : $id;
			if ( filter_var( $id, FILTER_VALIDATE_URL ) ) {
				$result = $this->get_remote_url( $id );
				if ( empty( $result->title ) ) {
					return rest_ensure_response( array( 'error' => __( 'Invalid URL.', 'newsletter-glue' ) ) );
				}
			} else {
				return rest_ensure_response( array( 'error' => __( 'Invalid post.', 'newsletter-glue' ) ) );
			}
		} else {
			$result = $this->get_local_post( $item );
		}

		return rest_ensure_response(
			array(
				'id'   => $id,
				'item' => $result,
			)
		);
	}

	/**
	 * Get a local URL.
	 */
	public function get_local_post( $item ) {

		$url    = get_permalink( $item->ID );
		$array  = parse_url( $url );
		$domain = ! empty( $array['host'] ) ? $array['host'] : '';

		$thumb_size = apply_filters( 'newsletterglue_post_embed_thumbnail_size', 'full' );

		$image = has_post_thumbnail( $item ) ? wp_get_attachment_url( get_post_thumbnail_id( $item->ID ), $thumb_size ) : $this->default_thumb_url();

		$content = apply_filters( 'newsletterglue_article_embed_content', apply_filters( 'the_content', $item->post_content ), $item->ID );

		$result = array(
			'domain'       => $domain,
			'post_id'      => $item->ID,
			'ID'           => $url,
			'is_remote'    => 0,
			'title'        => $item->post_title,
			'image_url'    => $image,
			'post_content' => $this->get_content( $item->ID, $content ),
			'author'       => $this->get_author( $item ),
			'categories'   => $this->get_categories_text( $item->ID ),
			'tags'         => $this->get_categories_text( $item->ID, 'post_tag' ),
		);

		return $result;
	}

	/**
	 * Get categories as label.
	 */
	public function get_categories_text( $post_id, $taxonomy = 'category' ) {
		$output_array = array();
		$categories   = wp_get_object_terms( $post_id, $taxonomy );
		if ( ! empty( $categories ) ) {
			foreach ( $categories as $category ) {
				$output_array[] = $category->name;
			}
		}
		return implode( ', ', $output_array );
	}

	/**
	 * Get post author.
	 */
	public function get_author( $post ) {
		return sprintf( __( 'By %s', 'newsletter-glue' ), get_the_author_meta( 'display_name', $post->post_author ) );
	}

	/**
	 * Get a post excerpt.
	 */
	public function get_content( $post_id, $content ) {
		$thepost = get_post( $post_id );
		$content = ! empty( $thepost->post_excerpt ) ? $thepost->post_excerpt : $content;
		$content = apply_filters( 'newsletterglue_default_post_embed_excerpt', $content, $post_id );

		if ( apply_filters( 'newsletterglue_post_embed_trim_words', true ) ) {
			$excerpt = wp_trim_words( $content, $this->excerpt_words() );
			$excerpt = wp_kses( $excerpt, newsletterglue_allowed_tags_for_excerpt() );
		} else {
			$excerpt = trim( $content );
			$excerpt = wp_kses( $excerpt, newsletterglue_allowed_tags_for_excerpt() );
		}

		return $excerpt;
	}

	/**
	 * Exerpt length by words.
	 */
	public function excerpt_words() {
		return apply_filters( 'newsletterglue_post_embed_words', 30 );
	}

	/**
	 * Get a remote URL.
	 */
	public function get_remote_url( $url ) {

		$url = untrailingslashit( $url );

		$html = false;

		if ( false === $html ) {
			$html = wp_remote_retrieve_body( wp_remote_get( $url ) ); // phpcs:ignore
			if ( $html ) {
				// set_transient( 'ngl_' . md5( $url ), $html, 2628000 );
			} else {
				$html = file_get_contents( $url ); // phpcs:ignore
				if ( $html ) {
					// set_transient( 'ngl_' . md5( $url ), $html, 2628000 );
				} else {
					$data = new stdclass();
					return $data;
				}
			}
		}

		if ( ! $html ) {
			$data = new stdclass();
			return $data;
		}

		$data = new stdclass();

		$data->is_remote = true;
		$data->favicon   = 'https://www.google.com/s2/favicons?sz=32&domain_url=' . $url;
		$data->ID        = $url;

		$doc = new DOMDocument();
		libxml_use_internal_errors( true );
		$doc->loadHTML( mb_convert_encoding( $html, 'HTML-ENTITIES', 'UTF-8' ) );
		libxml_clear_errors();
		$nodes       = $doc->getElementsByTagName( 'title' );
		$data->title = htmlspecialchars_decode( mb_convert_encoding( htmlentities( $nodes->item( 0 )->nodeValue, ENT_COMPAT, 'utf-8', false ), 'UTF-8', mb_list_encodings() ) );

		$metas  = $doc->getElementsByTagName( 'meta' );
		$images = array();
		for ( $i = 0; $i < $metas->length; $i++ ) {
			$meta = $metas->item( $i );
			if ( $meta->getAttribute( 'name' ) == 'description' ) {
				$content = htmlspecialchars_decode( mb_convert_encoding( htmlentities( $meta->getAttribute( 'content' ), ENT_COMPAT, 'utf-8', false ), 'UTF-8', mb_list_encodings() ) );
				if ( ! empty( $content ) ) {
					$data->post_content = $content;
				}
			}
			if ( $meta->getAttribute( 'property' ) == 'og:description' ) {
				$content = htmlspecialchars_decode( mb_convert_encoding( htmlentities( $meta->getAttribute( 'content' ), ENT_COMPAT, 'utf-8', false ), 'UTF-8', mb_list_encodings() ) );
				if ( ! empty( $content ) ) {
					$data->post_content = $content;
				}
			}
			if ( $meta->getAttribute( 'property' ) == 'og:title' ) {
				$title = htmlspecialchars_decode( mb_convert_encoding( htmlentities( $meta->getAttribute( 'content' ), ENT_COMPAT, 'utf-8', false ), 'UTF-8', mb_list_encodings() ) );
				if ( ! empty( $title ) && apply_filters( 'newsletterglue_embed_uses_seo_title', true ) ) {
					$data->title = $title;
				}
			}
			if ( $meta->getAttribute( 'property' ) == 'og:image' ) {
				$images[] = $meta->getAttribute( 'content' );
			}
		}
		if ( empty( $data->post_content ) ) {
			$data->post_content = __( 'No description found.', 'newsletter-glue' );
		}
		if ( ! empty( $images ) ) {
			foreach ( $images as $image ) {
				if ( ! empty( $data->image_url ) ) {
					continue;
				}
				$data->image_url = $image;
			}
		}
		if ( empty( $data->image_url ) ) {
			$data->image_url = $this->default_thumb_url();
		}

		$array        = parse_url( $url );
		$data->domain = ! empty( $array['host'] ) ? $array['host'] : '';

		return $data;
	}

	/**
	 * Get a default thumbnail.
	 */
	public function default_thumb_url() {
		return apply_filters( 'newsletterglue_default_thumb_url', NGL_PLUGIN_URL . 'assets/images/placeholder.png' );
	}
}

return new NGL_REST_API_Fetch_URL();
