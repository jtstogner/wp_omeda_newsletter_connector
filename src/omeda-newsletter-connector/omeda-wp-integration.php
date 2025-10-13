<?php
/**
 * Plugin Name: Omeda WordPress Integration
 * Description: Integrates WordPress content lifecycle with Omeda for email deployments.
 * Version: 1.0.0
 * Author: Your Name
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Define constants.
define('OMEDA_WP_VERSION', '1.0.0');
define('OMEDA_WP_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Include necessary files.
require_once OMEDA_WP_PLUGIN_DIR . 'includes/class-omeda-settings.php';
require_once OMEDA_WP_PLUGIN_DIR . 'includes/class-omeda-api-client.php';
require_once OMEDA_WP_PLUGIN_DIR . 'includes/class-omeda-deployment-types.php';
require_once OMEDA_WP_PLUGIN_DIR . 'includes/class-omeda-workflow-manager.php';
require_once OMEDA_WP_PLUGIN_DIR . 'includes/class-omeda-hooks.php';

/**
 * Main Omeda Integration class (Singleton).
 */
class Omeda_WP_Integration
{
    protected static $_instance = null;
    public $settings;
    public $deployment_types;
    public $workflow_manager;
    public $hooks;
    private $api_client = null;

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        // Initialize Settings
        $this->settings = new Omeda_Settings();
        $this->settings->init();

        // Initialize Deployment Types (CPT)
        $this->deployment_types = new Omeda_Deployment_Types();
        $this->deployment_types->init();

        // Initialize API Client
        $this->initialize_api_client();

        // Initialize Workflow and Hooks (only if API client is ready)
        if ($this->api_client) {
            $this->workflow_manager = new Omeda_Workflow_Manager($this->api_client);
            $this->workflow_manager->init();

            $this->hooks = new Omeda_Hooks($this->workflow_manager);
            $this->hooks->init();
        }
    }

    /**
     * Initializes the API client and handles configuration errors.
     */
    private function initialize_api_client() {
        try {
            $this->api_client = new Omeda_API_Client();
        } catch (Exception $e) {
            add_action('admin_notices', function() use ($e) {
                // Ensure the menu slug matches the one defined in Omeda_Settings
                $settings_link = admin_url('admin.php?page=omeda-integration');
                echo '<div class="notice notice-error is-dismissible"><p>';
                echo '<strong>Omeda Integration Error:</strong> ' . esc_html($e->getMessage()) . ' ';
                echo '<a href="' . esc_url($settings_link) . '">Please configure the plugin settings.</a>';
                echo '</p></div>';
            });
            $this->api_client = null;
        }
    }

    /**
     * Helper function to get the API client instance.
     * @return Omeda_API_Client|null
     */
    public function get_api_client() {
        if (is_null($this->api_client)) {
            $this->initialize_api_client();
        }
        return $this->api_client;
    }
}

/**
 * Returns the main instance of Omeda_WP_Integration.
 * @return Omeda_WP_Integration
 */
function omeda_wp_integration()
{
    return Omeda_WP_Integration::instance();
}

// Initialize the plugin.
add_action('plugins_loaded', 'omeda_wp_integration');
