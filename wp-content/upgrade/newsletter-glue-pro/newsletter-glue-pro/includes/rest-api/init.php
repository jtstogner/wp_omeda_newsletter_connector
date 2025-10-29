<?php
/**
 * REST API initialization
 *
 * @package Newsletter_Glue
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include the Ad Inserter REST API Controller
require_once plugin_dir_path( __FILE__ ) . 'class-ngl-ad-inserter-rest-controller.php';

/**
 * Initialize REST API controllers
 */
function ngl_init_rest_api() {
	// Initialize the Ad Inserter REST API Controller
	$ad_inserter_controller = new NGL_Ad_Inserter_REST_Controller();
	$ad_inserter_controller->register_routes();
}
add_action( 'rest_api_init', 'ngl_init_rest_api' );
