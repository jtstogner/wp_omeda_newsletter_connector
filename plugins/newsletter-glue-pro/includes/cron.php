<?php
/**
 * Cron.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Write to debug log file.
 *
 * @param mixed  $message The message to log.
 * @param string $prefix Optional prefix for the log entry.
 */
function ngl_debug_log( $message, $prefix = '' ) {
	$log_file = WP_CONTENT_DIR . '/ngl-automation-debug.log';

	// Format message.
	if ( is_array( $message ) || is_object( $message ) ) {
		$message = print_r( $message, true );
	}

	// Add timestamp and prefix.
	$log_entry = '[' . date( 'Y-m-d H:i:s' ) . '] ' . ( $prefix ? "[$prefix] " : '' ) . $message . "\n";

	// Write to log file.
	file_put_contents( $log_file, $log_entry, FILE_APPEND );
}

/**
 * Send automated emails based on schedule.
 */
add_action( 'newsletterglue_trigger_automated_email', 'newsletterglue_trigger_automated_email', 10 );
function newsletterglue_trigger_automated_email( $post_id ) {
	ngl_debug_log( "Starting automated email process for post ID: $post_id", 'TRIGGER' );

	$run = true;

	$automation = new NGL_Automation( $post_id );
	ngl_debug_log( 'Automation object created', 'TRIGGER' );

	// Fail. Post unpublished.
	if ( $automation->get_post_status() !== 'publish' ) {
		ngl_debug_log( "Automation post status is not 'publish'. Status: " . $automation->get_post_status(), 'FAIL' );
		$run = false;
	}

	// Fail. Paused.
	if ( ! $automation->is_enabled() ) {
		ngl_debug_log( 'Automation is not enabled', 'FAIL' );
		$run = false;
	}

	if ( ! $run ) {
		ngl_debug_log( "Clearing scheduled hook for post ID: $post_id", 'HOOK' );
		wp_clear_scheduled_hook( 'newsletterglue_trigger_automated_email', array( $post_id ) );

	} else {
		ngl_debug_log( "Proceeding with automation for post ID: $post_id", 'PROCESS' );

		$send_type = $automation->get_send_type();
		ngl_debug_log( "Send type: $send_type", 'PROCESS' );

		// Log the settings and query parameters for Latest Posts block.
		$settings = newsletterglue_get_data( $post_id );
		ngl_debug_log( 'Automation settings:', 'SETTINGS' );
		ngl_debug_log( $settings, 'SETTINGS' );

		// Create the newsletter.
		ngl_debug_log( 'Creating WordPress newsletter', 'CREATE' );
		$campaign_id = $automation->create_wp_newsletter();
		ngl_debug_log( "Created newsletter with campaign ID: $campaign_id", 'CREATE' );

		// Get the campaign content to check if Latest Posts block is working correctly.
		$campaign_post = get_post( $campaign_id );
		if ( $campaign_post ) {
			ngl_debug_log( "Campaign title: {$campaign_post->post_title}", 'CONTENT' );

			// Check for Latest Posts block in content - can be either 'latest-posts' (with hyphen) or 'latestposts' (without hyphen).
			if ( strpos( $campaign_post->post_content, 'wp:newsletterglue/latest-posts' ) !== false || strpos( $campaign_post->post_content, 'wp:newsletterglue/latestposts' ) !== false ) {
				ngl_debug_log( 'Latest Posts block found in campaign content', 'CONTENT' );

				// Extract and log the Latest Posts block configuration for both variants.
				preg_match_all( '/\<!-- wp:newsletterglue\/latest-posts (.*?) --\>/', $campaign_post->post_content, $matches_hyphen );
				preg_match_all( '/\<!-- wp:newsletterglue\/latestposts (.*?) --\>/', $campaign_post->post_content, $matches_no_hyphen );

				// Process blocks with hyphen.
				if ( ! empty( $matches_hyphen[1] ) ) {
					ngl_debug_log( 'Found ' . count( $matches_hyphen[1] ) . ' Latest Posts blocks (with hyphen)', 'BLOCK' );
					foreach ( $matches_hyphen[1] as $block_config ) {
						ngl_debug_log( "Latest Posts block config (with hyphen): $block_config", 'BLOCK' );
					}
				}

				// Process blocks without hyphen.
				if ( ! empty( $matches_no_hyphen[1] ) ) {
					ngl_debug_log( 'Found ' . count( $matches_no_hyphen[1] ) . ' Latest Posts blocks (without hyphen)', 'BLOCK' );
					foreach ( $matches_no_hyphen[1] as $block_config ) {
						ngl_debug_log( "Latest Posts block config (without hyphen): $block_config", 'BLOCK' );
					}
				}

				// Check if posts array exists and is cached in the block.
				if ( strpos( $campaign_post->post_content, '"posts":[' ) !== false ) {
					ngl_debug_log( 'Found cached posts array in Latest Posts block', 'CONTENT' );
				}
			} else {
				ngl_debug_log( 'No Latest Posts block found in campaign content', 'CONTENT' );
			}
		}

		// Send a campaign?
		if ( 'send' === $send_type ) {
			ngl_debug_log( "Sending campaign ID: $campaign_id", 'SEND' );
			$result = $automation->send_campaign( $campaign_id );
			ngl_debug_log( 'Send result:', 'SEND' );
			ngl_debug_log( $result, 'SEND' );

			ngl_debug_log( "Creating log entry for campaign ID: $campaign_id", 'LOG' );
			$automation->create_log( $campaign_id, $result );

			ngl_debug_log( "Removing WordPress post for campaign ID: $campaign_id", 'CLEANUP' );
			$automation->remove_wp_post( $campaign_id );
		}

		// Re-schedule?
		ngl_debug_log( "Re-enabling automation for post ID: $post_id", 'SCHEDULE' );
		$automation->enable();

		// Log the next scheduled run time.
		$next_run = wp_next_scheduled( 'newsletterglue_trigger_automated_email', array( $post_id ) );
		if ( $next_run ) {
			ngl_debug_log( 'Next scheduled run: ' . date( 'Y-m-d H:i:s', $next_run ), 'SCHEDULE' );
		} else {
			ngl_debug_log( 'No next run scheduled', 'SCHEDULE' );
		}
	}

	ngl_debug_log( "Completed automated email process for post ID: $post_id", 'COMPLETE' );
}
