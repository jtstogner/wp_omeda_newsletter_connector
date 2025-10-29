<?php
/**
 * Newsletter Glue Modules Loader
 *
 * @package Newsletter Glue
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load all Newsletter Glue modules
 */
function newsletterglue_load_modules() {
	// Load the scheduler module.
	require_once plugin_dir_path( __FILE__ ) . 'scheduler/scheduler.php';

	// Initialize the scheduler.
	global $ngl_scheduler;
	$ngl_scheduler = new NGL_Scheduler();

	// Load the subscriber checker module.
	require_once plugin_dir_path( __FILE__ ) . 'newsletter-subscriber-checker/newsletter-subscriber-checker.php';

	// Initialize the subscriber checker.
	global $ngl_subscriber_checker;
	$ngl_subscriber_checker = new NGL_Subscriber_Checker();

	// Load the latest posts block RSS source module.
	require_once plugin_dir_path( __FILE__ ) . 'latest-posts-block-rss-source/latest-posts-block-rss-source.php';

	// Initialize the latest posts block RSS source.
	global $ngl_latest_posts_block_rss_source;
	$ngl_latest_posts_block_rss_source = new NGL_Latest_Posts_RSS_Source();

	// Hook for other modules to initialize.
	do_action( 'newsletterglue_modules_loaded' );
}

// Initialize modules after Newsletter Glue is fully loaded.
add_action( 'newsletterglue_loaded', 'newsletterglue_load_modules' );
