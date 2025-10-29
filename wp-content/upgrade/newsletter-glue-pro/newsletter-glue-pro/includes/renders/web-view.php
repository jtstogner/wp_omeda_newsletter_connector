<?php
/**
 * Web render.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_Render_Webview class.
 */
class NGL_Render_Webview {

	public $app;

	/**
	 * Constructor.
	 */
	public function __construct() {

		add_filter( 'render_block', array( $this, 'render_webview' ), 10000, 3 );
		add_filter( 'render_block', array( $this, 'maybe_remove_styling' ), 10001, 3 );
	}

	/**
	 * Render.
	 */
	public function render_webview( $block_content, $block ) {

		if ( defined( 'NGL_IN_EMAIL' ) ) {
			return $block_content;
		}

		$name = $block['blockName'];

		if ( ! $name || ! strstr( $name, 'newsletterglue/' ) ) {
			return $block_content;
		}

		if ( $name === 'newsletterglue/table' ) {
			return $block_content;
		}

		$output = new simple_html_dom();
		$output->load( $block_content, true, false );

		$replace = 'table';
		foreach( $output->find( $replace ) as $key => $element ) {
			$element->tag = 'div';
			$element->removeAttribute( 'cellpadding' );
			$element->removeAttribute( 'cellspacing' );
		}

		$replace = 'tbody';
		foreach( $output->find( $replace ) as $key => $element ) {
			$element->tag = 'div';
		}

		$replace = 'tr';
		foreach( $output->find( $replace ) as $key => $element ) {
			$element->tag = 'div';
		}

		$replace = 'td';
		foreach( $output->find( $replace ) as $key => $element ) {
			$element->tag = 'div';
		}

		$replace = '.wp-block-newsletterglue-spacer';
		foreach( $output->find( $replace ) as $key => $element ) {
			$spacer = $element->find( '.ng-block-td', 0 );
			$height = rtrim( $spacer->height, 'px' ) . 'px';
			$spacer->{'data-height'} = $height;
			$spacer->style = ! empty( $element->style ) ? rtrim( $element->style, ';' ) . ';height: ' . $height . ' !important' : 'height: ' . $height . ' !important';
		}

		$output->save();

		return (string) $output;
	}

	/**
	 * Render.
	 */
	public function maybe_remove_styling( $block_content, $block ) {

		if ( defined( 'NGL_IN_EMAIL' ) ) {
			return $block_content;
		}

		$name = $block['blockName'];

		if ( empty( get_option( 'newsletterglue_disable_front_css' ) ) ) {
			return $block_content;
		}

		if ( ! $name || ! strstr( $name, 'newsletterglue/' ) ) {
			return $block_content;
		}

		if ( $name === 'newsletterglue/table' ) {
			return $block_content;
		}

		$output = new simple_html_dom();
		$output->load( $block_content, true, false );

		$replace = '.ng-block';
		foreach( $output->find( $replace ) as $key => $element ) {
			$element->removeAttribute('style');
		}

		$replace = '.ng-block-td';
		foreach( $output->find( $replace ) as $key => $element ) {
			if ( empty( $element->{'data-height'} ) ) {
				$element->removeAttribute('style');
			}
		}

		$replace = '.ng-block-button__link';
		foreach( $output->find( $replace ) as $key => $element ) {
			$s = $element->style;
			$results = [];
			$styles = explode(';', $s);

			foreach ($styles as $style) {
				$properties = explode(':', $style);
				if (2 === count($properties)) {
					$results[trim($properties[0])] = trim($properties[1]);
				}
			}
			if ( ! empty( $results['background-color'] ) ) {
				$bg = $results['background-color'];
			}
			$element->removeAttribute('style');
			$element->style = 'background-color: ' . $bg;
			$element->class = $element->class . ' wp-block-button__link has-background';
			$element->parent->class = $element->parent->class . ' wp-block-button';
		}

		$output->save();

		return (string) $output;
	}

}

return new NGL_Render_Webview;
