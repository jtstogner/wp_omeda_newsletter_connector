<?php
/**
 * Admin support.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_Admin_Patterns class.
 */
class NGL_Admin_Support {

	/**
	 * Constructor.
	 */
	public static function init() {

		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ), 10 );

		add_filter( 'nglue_backend_args', array( __CLASS__, 'add_args' ), 999 );
	}

	/**
	 * Add admin menu.
	 */
	public static function admin_menu() {

		// Add a hidden menu item for support wizard.
		add_submenu_page( 
			'_newsletterglue',
			__( 'Newsletter Glue - Get support', 'newlsetter-glue' ),
			__( 'Newsletter Glue - Get support', 'newlsetter-glue' ),
			'manage_newsletterglue',
			'ngl-support',
			array( __CLASS__, 'show_ui' )
		);

	}

	/**
	 * Show admin UI.
	 */
	public static function show_ui() {
		require_once NGL_PLUGIN_DIR . 'includes/admin/views/topbar.php';

		echo '<div id="nglue-support"></div>';
	}

	/**
	 * Extend js args.
	 */
	public static function add_args( $args = array() ) {
		global $current_user;

		$support = array(
			'admin_email'	=> $current_user->user_email,
			'admin_name'	=> $current_user->user_firstname . ' ' . $current_user->user_lastname,
		);

		$args[ 'support' ] = $support;

		return $args;
	}

}

NGL_Admin_Support::init();
