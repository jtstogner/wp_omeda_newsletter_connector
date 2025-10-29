<?php
/**
 * REST API.
 * 
 * @package Newsletter Glue
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include the Ad Inserter REST API Controller
require_once NGL_PLUGIN_DIR . 'includes/rest-api/class-ngl-ad-inserter-rest-controller.php';

/**
 * Initialize REST API controllers
 *//*
function ngl_init_rest_api() {
	// Initialize the Ad Inserter REST API Controller
	$ad_inserter_controller = new NGL_Ad_Inserter_REST_Controller();
	$ad_inserter_controller->register_routes();
}
add_action( 'rest_api_init', 'ngl_init_rest_api' );*/

/**
 * NGL_REST_API class.
 */
class NGL_REST_API {

	/**
	 * Constructor.
	 */
	public function __construct() {

		add_action( 'rest_api_init', array( __CLASS__, 'create_rest_routes' ) );

	}

	/**
	 * Create rest routes.
	 */
	public static function create_rest_routes() {

		foreach ( glob( NGL_PLUGIN_DIR . 'includes/api/*.php' ) as $filename ) {
			include_once $filename;
		}

	}

	/**
	 * Validate user authentication.
	 * 
	 * @param object $request The request that is being passed to API.
	 */
	public static function authenticate( $request ) {

		$headers = $request->get_headers();

		if ( empty( $headers ) || empty( $headers['newsletterglue_api_key'] ) ) {
			return false;
		}

		$api_key = $headers['newsletterglue_api_key'][0];
		$parts   = explode( '_', $api_key );
		$last    = array_pop( $parts );
		$parts   = array( implode( '_', $parts ), $last );
		$user    = username_exists( $parts[0] );

		if ( $user && user_can( $user, 'edit_posts' ) ) {
			$user_key = get_user_meta( $user, 'newsletterglue_api_key', true );
			if ( strtolower( $user_key ) === strtolower( $api_key ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Validate user admin.
	 * 
	 * @param object $request The request that is being passed to API.
	 */
	public static function authenticate_as_admin( $request ) {

		if ( is_user_logged_in() && current_user_can( 'manage_newsletterglue' ) ) {
			return true;
		}

		return false;
	}

}

return new NGL_REST_API();
