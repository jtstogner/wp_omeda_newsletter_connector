<?php
/**
 * Table block render.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_Render_Table class.
 */
class NGL_Render_Table {

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

		if ( "newsletterglue/table" !== $block['blockName'] )
			return $block_content;

		if ( isset( $block[ 'attrs' ][ 'show_in_web' ] ) ) {
			return null;
		}

		$html = $block_content;

		$output = new simple_html_dom();
		$output->load( $html, true, false );

		$replace = 'figure';
		foreach( $output->find( $replace ) as $key => $element ) {
			$element->find( 'table', 0 )->class = $element->class . ' ' . $element->find( 'table', 0 )->class;
			$element->outertext = $element->innertext;
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

		if ( "newsletterglue/table" !== $block['blockName'] )
			return $block_content;

		if ( isset( $block[ 'attrs' ][ 'show_in_email' ] ) ) {
			return null;
		}

		$html = $block_content;

		$output = new simple_html_dom();
		$output->load( $html, true, false );

		$replace = 'figure';
		foreach( $output->find( $replace ) as $key => $element ) {
			$element->find( 'table', 0 )->class = $element->class . ' ' . $element->find( 'table', 0 )->class;
			$element->outertext = $element->innertext;
		}

		$output->save();

		$block_content = (string) $output;

		return $block_content;
	}

}

return new NGL_Render_Table;
