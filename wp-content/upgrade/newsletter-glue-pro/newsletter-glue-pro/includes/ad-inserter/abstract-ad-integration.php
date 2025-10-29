<?php
/**
 * Abstract Ad Integration Class
 *
 * @package Newsletter_Glue
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract class for ad manager integrations
 */
abstract class NGL_Ad_Integration {

	/**
	 * Unique identifier for the integration
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * Human-readable name for the integration
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Option name for storing ad data
	 *
	 * @var string
	 */
	protected $option_name;

	/**
	 * Flag to track if initial sync has been performed
	 *
	 * @var bool
	 */
	protected $has_synced = false;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->option_name = 'ngl_ad_data_' . $this->id;
		$this->register_hooks();
	}

	/**
	 * Get the integration ID
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get the integration name
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Get the option name used to store ad data
	 *
	 * @return string
	 */
	public function get_option_name() {
		return $this->option_name;
	}

	/**
	 * Register hooks for the integration
	 */
	protected function register_hooks() {
		// Hook to update ad data when a post is saved
		add_action( 'save_post', array( $this, 'handle_save_post' ), 10, 3 );
	}

	/**
	 * Handle save post event to update ad data
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @param bool    $update  Whether this is an existing post being updated.
	 */
	public function handle_save_post( $post_id, $post, $update ) {
		// Implement logic to check if this post type is relevant to the integration
		if ( $this->is_relevant_post_type( $post->post_type ) ) {
			$this->sync_single_ad( $post_id, $post );
		}
	}

	/**
	 * Check if the post type is relevant to this integration
	 *
	 * @param string $post_type Post type.
	 * @return bool
	 */
	abstract protected function is_relevant_post_type( $post_type );

	/**
	 * Get ads data
	 *
	 * @param array $args Optional arguments for filtering ads.
	 * @return array Array of ad data.
	 */
	abstract public function get_ads( $args = array() );

	/**
	 * Sync all ads for this integration
	 */
	public function sync_all_ads() {
		$ads = $this->get_ads( array( 'posts_per_page' => -1 ) );
		update_option( $this->option_name, wp_json_encode( $ads ) );
		$this->has_synced = true;
	}

	/**
	 * Sync a single ad
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Optional post object.
	 */
	public function sync_single_ad( $post_id, $post = null ) {
		if ( ! $post ) {
			$post = get_post( $post_id );
		}

		if ( ! $post || ! $this->is_relevant_post_type( $post->post_type ) ) {
			return;
		}

		// Get current ads data
		$current_ads_json = get_option( $this->option_name, '' );
		$current_ads = $current_ads_json ? json_decode( $current_ads_json, true ) : array();

		// Get data for this ad
		$ad_data = $this->format_ad_data( $post );

		if ( $ad_data ) {
			// Update or add the ad data
			$found = false;
			for ( $i = 0; $i < count( $current_ads ); $i++ ) {
				if ( $current_ads[ $i ]['id'] === $ad_data['id'] ) {
					$current_ads[ $i ] = $ad_data;
					$found = true;
					break;
				}
			}

			if ( ! $found ) {
				$current_ads[] = $ad_data;
			}

			// Save updated ads data
			update_option( $this->option_name, wp_json_encode( $current_ads ) );
		}
	}

	/**
	 * Format ad data from a post
	 *
	 * @param WP_Post $post Post object.
	 * @return array|null Formatted ad data or null if not applicable.
	 */
	abstract protected function format_ad_data( $post );

	/**
	 * Check if the integration is available (e.g., required plugin is active)
	 *
	 * @return bool
	 */
	abstract public function is_available();

	abstract public function set_available( $available );

	/**
	 * Defer initial sync to ensure WordPress environment is fully loaded.
	 *
	 * @since [version]
	 */
	public function defer_initial_sync() {
		if ( ! $this->has_synced ) {
			add_action( 'wp_loaded', array( $this, 'sync_all_ads' ) );
			error_log( 'Deferred initial sync for ' . $this->name . ' until wp_loaded.' );
		}
	}
}