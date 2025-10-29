<?php
/**
 * Newsletter Glue Scheduler Module
 *
 * @package Newsletter Glue
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Scheduler class
 */
class NGL_Scheduler {


	/**
	 * Constructor
	 */
	public function __construct() {
		// Initialize the scheduler.
		$this->init();
	}

	/**
	 * Initialize the scheduler module.
	 */
	public function init() {
		// Allow completely disabling the scheduler module.
		$is_enabled = apply_filters( 'ngl_scheduler_enabled', true );

		if ( ! $is_enabled ) {
			return;
		}

		// Hook into the automation system.
		add_action( 'newsletterglue_trigger_automated_email', array( $this, 'pre_process_automated_email' ), 9 );

		// Register our custom trigger function.
		add_action( 'ngl_scheduler_process_automated_email', array( $this, 'process_automated_email' ) );
	}

	/**
	 * Pre-process automated email to check if it should be sent.
	 *
	 * @param int $post_id Post ID of the automation.
	 */
	public function pre_process_automated_email( $post_id ) {
		// Allow bypassing the scheduler for specific post IDs.
		$should_process = apply_filters( 'ngl_scheduler_should_process_email', true, $post_id );

		if ( ! $should_process ) {
			// If we shouldn't process this email, let the default handler take over.
			return;
		}

		// Remove the default Newsletter Glue action.
		remove_action( 'newsletterglue_trigger_automated_email', 'newsletterglue_trigger_automated_email' );

		// Clear the original scheduled hook.
		wp_clear_scheduled_hook( 'newsletterglue_trigger_automated_email', array( $post_id ) );

		// Trigger our custom processing.
		do_action( 'ngl_scheduler_process_automated_email', $post_id );
	}

	/**
	 * Process the automated email with additional checks.
	 *
	 * @param int $post_id Post ID of the automation.
	 */
	public function process_automated_email( $post_id ) {
		$run = true;

		$automation = new NGL_Automation( $post_id );

		// Fail. Post unpublished.
		if ( $automation->get_post_status() !== 'publish' ) {
			$run = false;
		}

		// Fail. Paused.
		if ( ! $automation->is_enabled() ) {
			$run = false;
		}

		if ( ! $run ) {
			// Clear scheduled hook.
			wp_clear_scheduled_hook( 'newsletterglue_trigger_automated_email', array( $post_id ) );
		} else {
			$send_type = $automation->get_send_type();
			$campaign_id = $automation->create_wp_newsletter();

			// Send a campaign?
			if ( 'send' === $send_type ) {
				// Allow bypassing the post content check.
				$should_check_posts = apply_filters( 'ngl_scheduler_should_check_posts', true, $post_id );

				// Default to true if we're skipping the check.
				$has_posts = true;

				if ( $should_check_posts ) {
					// Check if campaign has posts before sending.
					$has_posts = $this->post_block_has_posts( $post_id );
					// Allow overriding the result of the post check.
					$has_posts = apply_filters( 'ngl_scheduler_has_posts', $has_posts, $post_id );
				}

				if ( $has_posts ) {
					$result = $automation->send_campaign( $campaign_id );
				} else {
					// Allow customizing the error message.
					$error_message = apply_filters(
						'ngl_scheduler_no_posts_message',
						__( 'Canceled: Newsletter had no posts', 'newsletter-glue' ),
						$post_id
					);

					$result = array(
						'status'  => 400,
						'type'    => 'error',
						'message' => $error_message,
					);
				}

				$automation->create_log( $campaign_id, $result );
				$automation->remove_wp_post( $campaign_id );
			}

			// Re-schedule?
			$automation->enable();
		}
	}

	/**
	 * Check if a post block exists and has content.
	 *
	 * @param  int $post_id Post ID to check.
	 * @return bool Whether the post has content in its latest posts block.
	 */
	public function post_block_has_posts( $post_id ) {
		$has_posts = false;

		$post = get_post( $post_id );
		$content = $post->post_content;

		// Check if content has the post embed block.
		if ( strstr( $content, '<!-- wp:newsletterglue/latest-posts' ) ) {
			preg_match_all( '/<!-- wp:newsletterglue\/latest-posts (.*?) \/-->/', $content, $blocks );
			if ( ! empty( $blocks[1] ) ) {
				$found = $blocks[1];
				foreach ( $found as $key => $data ) {
					if ( strstr( $data, '"posts":[' ) ) {
						$split   = explode( '"posts":[', $data );
						$split2  = explode( ']', $split[1] );
						$content = str_replace( '[' . $split2[0] . ']', 'null', $content );
						$class   = new NGL_Render_Latest_Posts();
						$props   = json_decode( $data, true );
						unset( $props['posts'], $props['custom_data'] );
						$args    = $class->setup_attrs( $props );
						$posts   = $class->get_posts( $args );
						if ( ! empty( $posts ) && count( $posts ) ) {
							$has_posts = true;
						}
					}
				}
			}
		}

		return $has_posts;
	}
}
