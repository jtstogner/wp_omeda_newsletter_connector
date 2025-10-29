<?php
/**
 * Gutenberg.
 */

if ( ! class_exists( 'NGL_Abstract_Block', false ) ) {
	include_once NGL_PLUGIN_DIR . 'includes/abstract-block.php';
}

class NGL_Block_Latest_Posts extends NGL_Abstract_Block {

	public $id = 'newsletterglue_block_latest_posts';

	public $is_pro = false;

	private $app;

	public $asset_id;

	/**
	 * Construct.
	 */
	public function __construct() {

		$this->asset_id = str_replace( '_', '-', $this->id );

	}

	/**
	 * Demo URL.
	 */
	public function get_demo_url() {
		return 'https://www.youtube.com/embed/yty88VULD_g?autoplay=1&modestbranding=1&autohide=1&showinfo=0&controls=0&start=0';
	}

	/**
	 * Block icon.
	 */
	public function get_icon_svg() {
		return '<svg class="ngl-block-svg-icon" stroke-width="0" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path fill="none" d="M0 0h24v24H0z"></path><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"></path></svg>';
	}

	/**
	 * Block label.
	 */
	public function get_label() {
		return __( 'Latest posts', 'newsletter-glue' );
	}

	/**
	 * Block label.
	 */
	public function get_description() {
		return __( 'Add your latest posts to your newsletters.', 'newsletter-glue' );
	}

}

return new NGL_Block_Latest_Posts;