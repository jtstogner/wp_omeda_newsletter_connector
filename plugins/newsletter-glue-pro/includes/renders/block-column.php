<?php
/**
 * Column block render.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_Render_Column class.
 */
class NGL_Render_Column {

	/**
	 * Constructor.
	 */
	public function __construct() {

		add_filter( 'render_block', array( $this, 'render_block_web' ), 50, 3 );
		add_filter( 'render_block', array( $this, 'render_block_email' ), 50, 3 );

	}

	/**
	 * Render.
	 */
	public function render_block_web( $block_content, $block ) {

		if ( defined( 'NGL_IN_EMAIL' ) )
			return $block_content;

		if ( "newsletterglue/section" !== $block['blockName'] )
			return $block_content;

		if ( isset( $block[ 'attrs' ][ 'show_in_web' ] ) ) {
			return null;
		}

		$html = $block_content;

		$output = new simple_html_dom();
		$output->load( $html, true, false );

		if ( strstr( $html, 'ng-should-remove' ) ) {
			return null;
		}

		$output->save();

		$block_content = (string) $output;

		return $block_content;
	}

	/**
	 * Render.
	 */
	public function render_block_email( $block_content, $block ) {

		if ( ! defined( 'NGL_IN_EMAIL' ) )
			return $block_content;

		if ( "newsletterglue/section" !== $block['blockName'] )
			return $block_content;

		if ( isset( $block[ 'attrs' ][ 'show_in_email' ] ) ) {
			return null;
		}

		$html = $block_content;

		$output = new simple_html_dom();
		$output->load( $html, true, false );

		if ( strstr( $html, 'ng-should-remove' ) ) {
			return null;
		}

		$output->save();

		$block_content = (string) $output;

		return $block_content;
	}

}

return new NGL_Render_Column;
