<?php
/**
 * Setup API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_Setup_API class.
 */
class NGL_Setup_API {

	/**
	 * Constructor.
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'register_api_key' ), 1 );

	}

	/**
	 * Register API key.
	 */
	public function register_api_key() {
		if ( ! is_user_logged_in() || ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		$user_id = get_current_user_id();
		$has_key = get_user_meta( $user_id, 'newsletterglue_api_key_set', true );

		if ( ! $has_key ) {
			update_user_meta( $user_id, 'newsletterglue_api_key', $this->generate_api_key() );
			update_user_meta( $user_id, 'newsletterglue_api_key_set', 'yes' );
		}

	}

	/**
	 * Generate API key.
	 */
	public function generate_api_key() {
		global $current_user;

		return strtolower( $current_user->user_login ) . '_' . bin2hex( random_bytes( 32 ) );
	}

}

return new NGL_Setup_API();