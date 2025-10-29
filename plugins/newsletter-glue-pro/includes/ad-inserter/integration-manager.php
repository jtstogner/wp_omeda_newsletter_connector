<?php
/**
 * Ad Integration Manager Class
 *
 * @package Newsletter_Glue
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manager class for ad integrations
 */
class NGL_Ad_Integration_Manager {

	/**
	 * Array of registered integrations
	 *
	 * @var array
	 */
	private $integrations = array();

	/**
	 * Active integration instance
	 *
	 * @var NGL_Ad_Integration|null
	 */
	private $active_integration = null;

	/**
	 * Option name for storing the active integration ID
	 *
	 * @var string
	 */
	private $active_integration_option = 'ngl_active_ad_integration';

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->load_active_integration();
		$this->register_hooks();
	}

	/**
	 * Register hooks
	 */
	private function register_hooks() {
		// Hook for plugin activation to sync ads
		register_activation_hook( NGL_PLUGIN_FILE, array( $this, 'handle_plugin_activation' ) );
	}

	/**
	 * Handle plugin activation
	 */
	public function handle_plugin_activation() {
		$this->handle_plugin_activation_sync();
	}

	/**
	 * Handle plugin activation sync for all available integrations.
	 *
	 * @since [version]
	 */
	public function handle_plugin_activation_sync() {
		foreach ( $this->integrations as $integration ) {
			if ( $integration->is_available() ) {
				$integration->defer_initial_sync();
				error_log( 'Scheduled sync for ' . $integration->get_name() . ' during plugin activation.' );
			}
		}
	}

	/**
	 * Load the active integration from options
	 */
	private function load_active_integration() {
		$active_id = get_option( $this->active_integration_option, '' );
		if ( $active_id && isset( $this->integrations[ $active_id ] ) ) {
			$this->active_integration = $this->integrations[ $active_id ];
		}
	}

	/**
	 * Register a new integration
	 *
	 * @param NGL_Ad_Integration $integration Integration instance.
	 */
	public function register_integration( NGL_Ad_Integration $integration ) {
		if ( $integration->is_available() ) {
			$this->integrations[ $integration->get_id() ] = $integration;
			// If no active integration is set, use the first available one
			if ( ! $this->active_integration ) {
				$active_id = get_option( $this->active_integration_option, '' );
				if ( ! $active_id ) {
					$this->set_active_integration( $integration->get_id() );
				} elseif ( $active_id === $integration->get_id() ) {
					$this->active_integration = $integration;
				}
			}
		}
	}

	/**
	 * Set the active integration
	 *
	 * @param string $integration_id Integration ID.
	 * @return bool True if successful, false otherwise.
	 */
	public function set_active_integration( $integration_id ) {
		$integration = isset( $this->integrations[ $integration_id ] ) ? $this->integrations[ $integration_id ] : null;
		if ( $integration && $integration->is_available() ) {
			update_option( 'ngl_active_ad_integration', $integration_id );
			$this->active_integration = $integration; // Store the object, not the ID
			//error_log( 'Active ad integration set to: ' . $integration_id );
			// Defer sync only for this integration activation
			$integration->defer_initial_sync( true );
		} else {
			//error_log( 'Failed to set active integration to ' . $integration_id . ' - unavailable or not registered.' );
		}
	}

	/**
	 * Get the active integration
	 *
	 * @return NGL_Ad_Integration|null Active integration instance or null if not set.
	 */
	public function get_active_integration() {
		return $this->active_integration;
	}

	/**
	 * Get all registered integrations
	 *
	 * @return array Array of NGL_Ad_Integration instances.
	 */
	public function get_integrations() {
		return $this->integrations;
	}

	/**
	 * Get a specific integration by ID
	 *
	 * @param string $integration_id Integration ID.
	 * @return NGL_Ad_Integration|null Integration instance or null if not found.
	 */
	public function get_integration( $integration_id ) {
		return isset( $this->integrations[ $integration_id ] ) ? $this->integrations[ $integration_id ] : null;
	}

	/**
	 * Get ads from the active integration
	 *
	 * @return array Array of ads.
	 */
	public function get_ads() {
		if ( $this->active_integration ) {
			$option_name = $this->active_integration->get_option_name();
			$ads_json = get_option( $option_name, '' );
			return $ads_json ? json_decode( $ads_json, true ) : array();
		}
		return array();
	}

	/**
	 * Trigger sync for the active integration.
	 */
	public function trigger_sync() {
		if ( $this->active_integration && is_object( $this->active_integration ) ) {
			$this->active_integration->defer_initial_sync( true );
			error_log( 'Triggered sync for active integration.' );
		} else {
			error_log( 'No active integration object to sync.' );
		}
	}

	/**
	 * Switch integration based on URL parameter.
	 *
	 * @param string $integration_id The ID of the integration to switch to.
	 */
	public function switch_integration( $integration_id ) {
		if ( ! empty( $integration_id ) && isset( $this->integrations[ $integration_id ] ) ) {
			$this->set_active_integration( $integration_id );
			error_log( 'Switched integration to ' . $integration_id . ' via URL parameter.' );
		}
	}
}