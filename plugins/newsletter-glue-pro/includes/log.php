<?php
/**
 * Email log.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * NGL_Log class.
 */
class NGL_Log {

	public $id = 0;

	public $data = null;

	/**
	 * Constructor.
	 */
	public function __construct( $post_id = 0 ) {

		$this->id = $post_id;

		$this->data = get_post( $this->id );
	}

	/**
	 * Get post.
	 */
	public function get_post() {
		return $this->data;
	}

	/**
	 * Get post title.
	 */
	public function get_post_title() {

		$post = $this->get_post();

		return ! empty( $post->post_title ) ? $post->post_title : '';
	}

	/**
	 * Get date.
	 */
	public function get_date() {

		return sprintf( __( '%1$s at %2$s', 'newsletter-glue' ), get_the_time( 'Y/m/d', $this->id ), get_the_time( 'g:ia', $this->id ) );
	}

	/**
	 * Get status.
	 */
	public function get_status() {

		$status = get_post_meta( $this->id, '_result', true );

		return $status;
	}

	/**
	 * Get preview link.
	 */
	public function get_preview_link() {

		$url = add_query_arg( 'preview_email', $this->id, home_url() );

		return $url;
	}

	/**
	 * Get trash link.
	 */
	public function get_trash_link() {

		$post_id = $this->id;

		return wp_nonce_url( admin_url( "post.php?action=trash&post=$post_id" ), "trash-post_$post_id" );
	}
}
