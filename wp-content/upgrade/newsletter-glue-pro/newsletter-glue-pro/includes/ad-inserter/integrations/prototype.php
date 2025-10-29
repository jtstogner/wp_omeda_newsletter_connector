<?php
/**
 * Prototype Ad Integration Class
 *
 * @package Newsletter_Glue
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include the abstract integration class
require_once NGL_PLUGIN_DIR . 'includes/ad-inserter/abstract-ad-integration.php';

/**
 * Prototype ad integration class
 */
class NGL_Prototype_Ad_Integration extends NGL_Ad_Integration {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id = 'prototype';
		$this->name = __( 'Prototype Ads', 'newsletter-glue' );
		parent::__construct();
		// Note: Removed the exit statement that was added for testing	
        }


	/**
	 * Check if the post type is relevant to this integration
	 *
	 * @param string $post_type Post type.
	 * @return bool
	 */
	protected function is_relevant_post_type( $post_type ) {
		// For prototype, we don't use a specific post type
		return false;
	}

	/**
	 * Get ads data
	 *
	 * @param array $args Optional arguments for filtering ads.
	 * @return array Array of ad data.
	 */
	public function get_ads( $args = array() ) {
		// For prototype, return the data from our option if it exists
		$ads_json = get_option( $this->option_name, '' );
		if ( $ads_json ) {
			return json_decode( $ads_json, true );
		}

		// If no data exists, return the initial mock data
		return array(
			array(
				'id' => 'ad001',
				'title' => 'The PrototypeSummer Sale Banner',
				'url' => 'https://example.com/summer-sale',
				'adImage' => 'https://blog.lipsumhub.com/wp-content/uploads/2024/09/what-is-a-placeholder-in-advertising-lipsumhub.jpg',
				'category' => 'promotional',
				'placement' => 'header',
				'group' => 'seasonal'
			),
			array(
				'id' => 'ad002',
				'title' => 'Product Spotlight Prototype',
				'url' => 'https://example.com/product-spotlight',
				'adImage' => 'https://adshares.net/uploads/articles/_2023-05/c0e5105265cf5053.png',
				'category' => 'product',
				'placement' => 'sidebar',
				'group' => 'featured'
			),
			array(
				'id' => 'ad003',
				'title' => 'Newsletter Signup',
				'url' => 'https://example.com/newsletter-signup',
				'adImage' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTbYEyxYFPsKFY9uIZ9wZfa5EDItoWu_dQ4bg&s',
				'category' => 'engagement',
				'placement' => 'footer',
				'group' => 'conversion'
			),
			array(
				'id' => 'ad004',
				'title' => 'Tacos Prototype',
				'url' => 'https://example.com/holiday-special',
				'adImage' => 'https://www.emedevents.com/organizer-profile/_next/image?url=https%3A%2F%2Fstatic.emedevents.com%2Fuploads%2Forganizers%2F200%2F3025771e3d7fd7fb884be7e7039f4e14.jpg&w=256&q=70',
				'category' => 'promotional',
				'placement' => 'content',
				'group' => 'seasonal'
			),
			array(
				'id' => 'ad005',
				'title' => 'Free Shipping Promo',
				'url' => 'https://example.com/free-shipping',
				'adImage' => 'https://via.placeholder.com/468x60?text=Free+Shipping+Promo',
				'category' => 'promotional',
				'placement' => 'header',
				'group' => 'conversion'
			)
		);
	}

	/**
	 * Format ad data from a post
	 *
	 * @param WP_Post $post Post object.
	 * @return array|null Formatted ad data or null if not applicable.
	 */
	protected function format_ad_data( $post ) {
		// Not used in prototype integration
		return null;
	}

	/**
	 * Check if the integration is available
	 *
	 * @return bool
	 */
	public function is_available() {
		// Prototype integration is always available
		return apply_filters( 'ngl_ad_inserter_prototype_integration_available', false );
	}

	public function set_available( $available ) {
        if($this->is_available()) {
            update_option( $this->option_name . '_available', $available );
        } else {
            update_option( $this->option_name . '_available', false );
        }
    }

	/**
	 * Sync all ads for this integration
	 */
	public function sync_all_ads() {
		// Check if we already have data in the option
		$existing_data = get_option( $this->option_name, '' );
		if ( empty( $existing_data ) ) {
			// If no data exists, populate with our mock data
			$ads = $this->get_ads();
			update_option( $this->option_name, wp_json_encode( $ads ) );
		}
		// If data exists, we don't overwrite it to preserve any manual changes
	}
}
