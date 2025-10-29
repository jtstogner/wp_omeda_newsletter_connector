<?php
/**
 * Setup options.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_Setup_Options class.
 */
class NGL_Setup_Options {

	/**
	 * Constructor.
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'register_settings' ), 2 );

	}

	/**
	 * Register settings.
	 */
	public function register_settings() {

		register_setting( 'newsletterglue_wizard', 'newsletterglue_admin_name', array( 'show_in_rest' => true ) );

		register_setting( 'newsletterglue_wizard', 'newsletterglue_admin_address', array( 'show_in_rest' => true, 'default' => '' ) );

		register_setting( 'newsletterglue_wizard', 'newsletterglue_logo_id', array( 'show_in_rest' => true, 'default' => 0 ) );

		register_setting( 'newsletterglue_wizard', 'newsletterglue_logo_width', array( 'show_in_rest' => true, 'default' => 165 ) );

		// Register social links.
		$socials = array( 'instagram_url', 'tiktok_url', 'twitter_url', 'facebook_url', 'linkedin_url', 'twitch_url', 'youtube_url' );
		foreach( $socials as $social ) {
			register_setting( 'newsletterglue_wizard', 'newsletterglue_' . $social, array( 'show_in_rest' => true, 'default' => '' ) );
		}
	}

}

return new NGL_Setup_Options();