<?php
/**
 * Plugin Name: Newsletter Glue Pro
 * Plugin URI: https://newsletterglue.com/
 * Description: Email posts to subscribers from the WordPress editor. Works with Mailchimp, MailerLite, Brevo…
 * Author: Newsletter Glue
 * Author URI: https://newsletterglue.com
 * Requires at least: 6.6
 * Requires PHP: 7.4
 * Version: 4.0.3.3
 * Text Domain: newsletter-glue
 * Domain Path: /i18n/languages/
 *
 * @package Newsletter Glue
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Class.
 */
final class Newsletter_Glue {

	/**
	 * Singleton
	 *************************************************************/

	/**
	 * Class instance.
	 *
	 * @var Newsletter_Glue|null
	 */
	private static $instance = null;

	/**
	 * The lists.
	 *
	 * @var $thelists
	 */
	public static $the_lists = null;

	/**
	 * Ad integration manager instance.
	 *
	 * @var NGL_Ad_Integration_Manager|null
	 */
	private $ad_integration_manager = null;

	/**
	 * Main Instance.
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Newsletter_Glue ) ) {
			self::$instance = new Newsletter_Glue();
			self::$instance->setup_constants();

			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );

			self::$instance->includes();
			self::$instance->includes_ad_integrations();
			self::$instance->init_ad_integrations();
		}

		return self::$instance;
	}

	/**
	 * Throw error on object clone.
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'newsletter-glue' ), '1.0.0' );
	}

	/**
	 * Disable unserializing of the class.
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'newsletter-glue' ), '1.0.0' );
	}

	/**
	 * Setup plugin constants.
	 */
	private function setup_constants() {

		// Plugin version.
		if ( ! defined( 'NGL_VERSION' ) ) {
			define( 'NGL_VERSION', '4.0.3.3' );
		}

		// Plugin Folder Path.
		if ( ! defined( 'NGL_PLUGIN_DIR' ) ) {
			define( 'NGL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL.
		if ( ! defined( 'NGL_PLUGIN_URL' ) ) {
			define( 'NGL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Root File.
		if ( ! defined( 'NGL_PLUGIN_FILE' ) ) {
			define( 'NGL_PLUGIN_FILE', __FILE__ );
		}

		// Feedback server.
		if ( ! defined( 'NGL_FEEDBACK_SERVER' ) ) {
			define( 'NGL_FEEDBACK_SERVER', 'https://newsletterglue.com' );
		}
	}

	/**
	 * Include required files.
	 */
	private function includes() {

		if ( ! function_exists( 'file_get_html' ) && ! class_exists( 'simple_html_dom' ) && ! class_exists( 'simple_html_dom_node' ) ) {
			include_once NGL_PLUGIN_DIR . 'includes/libraries/simple_html_dom.php';
		}

		if ( ! class_exists( 'NGL_Abstract_Integration', false ) ) {
			include_once NGL_PLUGIN_DIR . 'includes/abstract-integration.php';
		}

		include_once NGL_PLUGIN_DIR . 'includes/setup-api.php';
		include_once NGL_PLUGIN_DIR . 'includes/setup-options.php';
		include_once NGL_PLUGIN_DIR . 'includes/rest-api.php';
		include_once NGL_PLUGIN_DIR . 'includes/blockapi.php';
		
		// Include Ad Inserter REST API
		include_once NGL_PLUGIN_DIR . 'includes/rest-api/init.php';

		include_once NGL_PLUGIN_DIR . 'includes/ajax-functions.php';
		include_once NGL_PLUGIN_DIR . 'includes/functions.php';
		include_once NGL_PLUGIN_DIR . 'includes/install.php';
		include_once NGL_PLUGIN_DIR . 'includes/core.php';
		include_once NGL_PLUGIN_DIR . 'includes/embeds.php';
		include_once NGL_PLUGIN_DIR . 'includes/compatibility.php';
		include_once NGL_PLUGIN_DIR . 'includes/gutenberg.php';
		include_once NGL_PLUGIN_DIR . 'includes/cpt/cpt.php';
		include_once NGL_PLUGIN_DIR . 'includes/rewrite.php';
		include_once NGL_PLUGIN_DIR . 'includes/pro.php';
		include_once NGL_PLUGIN_DIR . 'includes/automation.php';
		include_once NGL_PLUGIN_DIR . 'includes/log.php';
		include_once NGL_PLUGIN_DIR . 'includes/blockapi.php';
		include_once NGL_PLUGIN_DIR . 'includes/cron.php';

		foreach ( glob( NGL_PLUGIN_DIR . 'includes/renders/*.php' ) as $filename ) {
			include_once $filename;
		}

		if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			include_once NGL_PLUGIN_DIR . 'includes/admin/admin-fields.php';
			include_once NGL_PLUGIN_DIR . 'includes/admin/admin-functions.php';
			include_once NGL_PLUGIN_DIR . 'includes/admin/class-ngl-admin-automations.php';
			include_once NGL_PLUGIN_DIR . 'includes/admin/admin-logs.php';
			include_once NGL_PLUGIN_DIR . 'includes/admin/admin-menu.php';
			include_once NGL_PLUGIN_DIR . 'includes/admin/admin-notices.php';
			include_once NGL_PLUGIN_DIR . 'includes/admin/admin-scripts.php';
			include_once NGL_PLUGIN_DIR . 'includes/admin/admin-templates.php';
			include_once NGL_PLUGIN_DIR . 'includes/admin/admin-patterns.php';
			include_once NGL_PLUGIN_DIR . 'includes/admin/meta-boxes.php';
			include_once NGL_PLUGIN_DIR . 'includes/admin/posts.php';
			include_once NGL_PLUGIN_DIR . 'includes/admin/support.php';
		}

		// Load blocks.
		$blocks = newsletterglue_get_blocks();
		foreach ( $blocks as $block_id => $params ) {
			if ( isset( $params['path'] ) && file_exists( $params['path'] ) ) {
				include_once $params['path'];
			} elseif ( file_exists( NGL_PLUGIN_DIR . 'includes/blocks/' . $block_id . '/block.php' ) ) {
				include_once NGL_PLUGIN_DIR . 'includes/blocks/' . $block_id . '/block.php';
			}
		}

		// Demo templates.
		for ( $i = 1; $i <= 13; $i++ ) {
			if ( file_exists( NGL_PLUGIN_DIR . 'includes/default-templates/template-' . $i . '.php' ) ) {
				include_once NGL_PLUGIN_DIR . 'includes/default-templates/template-' . $i . '.php';
			}
		}

		// Load modules if they exist.
		if ( file_exists( NGL_PLUGIN_DIR . 'modules/loader.php' ) ) {
			include_once NGL_PLUGIN_DIR . 'modules/loader.php';
		}

		// Fire action to indicate Newsletter Glue is fully loaded.
		do_action( 'newsletterglue_loaded' );
	}

	/**
	 * Include ad integrations files
	 */
	public function includes_ad_integrations() {
		require_once NGL_PLUGIN_DIR . 'includes/ad-inserter/abstract-ad-integration.php';
		require_once NGL_PLUGIN_DIR . 'includes/ad-inserter/integration-manager.php';
		require_once NGL_PLUGIN_DIR . 'includes/ad-inserter/integrations/prototype.php';
		require_once NGL_PLUGIN_DIR . 'includes/ad-inserter/integrations/advanced-ads.php';
		require_once NGL_PLUGIN_DIR . 'includes/ad-inserter/integrations/broadstreet.php';
		require_once NGL_PLUGIN_DIR . 'includes/ad-inserter/integrations/render/broadstreet-ad-zone-renderer.php';
	}

	/**
	 * Initialize ad integrations
	 */
	public function init_ad_integrations() {
		$this->ad_integration_manager = new NGL_Ad_Integration_Manager();
		
		// Register integrations
		$this->ad_integration_manager->register_integration( new NGL_Advanced_Ads_Integration() );
		$this->ad_integration_manager->register_integration( new NGL_Prototype_Ad_Integration() );
		$this->ad_integration_manager->register_integration( new NGL_Broadstreet_Integration() );
		
		// Set active integration based on saved option
		$active_integration_id = get_option( 'ngl_active_ad_integration', '' );
		if ( ! empty( $active_integration_id ) ) {
			$this->ad_integration_manager->set_active_integration( $active_integration_id );
		}
		
		// Hook to handle sync during plugin activation
		register_activation_hook( __FILE__, array( $this->ad_integration_manager, 'handle_plugin_activation_sync' ) );
		
		// Move the switching logic to admin_init hook
		add_action( 'admin_init', array( $this, 'handle_integration_switch' ) );
	}

	/**
	 * Handle integration switching on admin_init hook
	 */
	public function handle_integration_switch() {
		if ( isset( $_GET['switch_integration'] ) && current_user_can( 'manage_options' ) ) {
			$integration_id = sanitize_text_field( $_GET['switch_integration'] );
			if ( $this->ad_integration_manager->switch_integration( $integration_id ) ) {
				add_action( 'admin_notices', function() use ( $integration_id ) {
					echo '<div class="notice notice-success is-dismissible"><p>Switched to ' . esc_html( $integration_id ) . ' integration.</p></div>';
				} );
			}
		}
	}

	/**
	 * Get the ad integration manager instance
	 *
	 * @return NGL_Ad_Integration_Manager
	 */
	public function get_ad_manager() {
		return $this->ad_integration_manager;
	}

	/**
	 * Loads the plugin language files.
	 */
	public function load_textdomain() {
		global $wp_version;

		// Set filter for plugin's languages directory.
		$newsletterglue_lang_dir = dirname( plugin_basename( NGL_PLUGIN_FILE ) ) . '/i18n/languages/';
		$newsletterglue_lang_dir = apply_filters( 'newsletterglue_languages_directory', $newsletterglue_lang_dir );

		// Traditional WordPress plugin locale filter.

		$get_locale = get_locale();

		if ( $wp_version >= 4.7 ) {

			$get_locale = get_user_locale();
		}

		unload_textdomain( 'newsletter-glue' );

		/**
		 * Defines the plugin language locale used.
		 *
		 * @var $get_locale The locale to use. Uses get_user_locale()` in WordPress 4.7 or greater,
		 *                  otherwise uses `get_locale()`.
		 */
		$locale = apply_filters( 'plugin_locale', $get_locale, 'newsletter-glue' );
		/* translators: %1$s, %2$s: newsletter-glue, locale */
		$mofile = sprintf( '%1$s-%2$s.mo', 'newsletter-glue', $locale );

		// Look for wp-content/languages/newsletter-glue/newsletter-glue-{lang}_{country}.mo.
		$mofile_global1 = WP_LANG_DIR . '/newsletter-glue/newsletter-glue-' . $locale . '.mo';

		// Look in wp-content/languages/plugins/newsletter-glue.
		$mofile_global2 = WP_LANG_DIR . '/plugins/newsletter-glue/' . $mofile;

		if ( file_exists( $mofile_global1 ) ) {

			load_textdomain( 'newsletter-glue', $mofile_global1 );

		} elseif ( file_exists( $mofile_global2 ) ) {

			load_textdomain( 'newsletter-glue', $mofile_global2 );

		} else {

			// Load the default language files.
			load_plugin_textdomain( 'newsletter-glue', false, $newsletterglue_lang_dir );
		}
	}

	/**
	 * Assets URL.
	 */
	public function assets_url() {
		return untrailingslashit( plugins_url( '/', NGL_PLUGIN_FILE ) ) . '/assets/images';
	}

	/**
	 * Current API version.
	 */
	public function api_version() {
		return 'v1';
	}
}

if ( ! function_exists( 'newsletterglue_get_ad_manager' ) ) {
	/**
	 * Get the ad integration manager instance
	 *
	 * @return NGL_Ad_Integration_Manager
	 */
	function newsletterglue_get_ad_manager() {
		return Newsletter_Glue::instance()->get_ad_manager();
	}
}

/**
 * The main function.
 */
if ( ! function_exists( 'newsletterglue' ) ) {
	/**
	 * Run NG instance.
	 */
	function newsletterglue() {
		return Newsletter_Glue::instance();
	}
}

// Get Running.
newsletterglue();
