<?php
/**
 * Buttons block render.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_Render_Buttons class.
 */
class NGL_Render_Buttons {

	/**
	 * Constructor.
	 */
	public function __construct() {

		add_filter( 'render_block', array( $this, 'render_block_email' ), 50, 3 );

	}

	/**
	 * Render: web.
	 */
	public function render_block_email( $block_content, $block ) {

		if ( ! defined( 'NGL_IN_EMAIL' ) )
			return $block_content;

		if ( "newsletterglue/buttons" !== $block['blockName'] )
			return $block_content;

		if ( isset( $block[ 'attrs' ][ 'show_in_email' ] ) ) {
			return null;
		}

		$html = $block_content;

		$output = new simple_html_dom();
		$output->load( $html, true, false );

		$replace = '.wp-block-newsletterglue-buttons';
		foreach( $output->find( $replace ) as $key => $element ) {
			$props = newsletterglue_get_props_from_style( $element->style );
		}

		$align = 'left';
		if ( ! empty( $props[ 'justify-content' ] ) ) {
			if ( $props[ 'justify-content' ] === 'center' ) {
				$align = 'center';
			}
			if ( $props[ 'justify-content' ] === 'right' ) {
				$align = 'right';
			}
		} 

		$replace = '.wp-block-newsletterglue-button';
		$i = 0;
		foreach( $output->find( $replace ) as $key => $element ) {
			$i++;
			$count = count( $output->find( $replace ) );
			$styles = array();
			if ( $i < $count ) {
				$styles[] = 'padding-right: ' . esc_attr( $props[ 'gap' ] );
			}

			$pct = $count > 1 ? 100 / ( $count - 1 ) : 100;

			if ( $i < $count && $props['justify-content'] === 'space-between' ) {
				$element->outertext = '<td class="ng-block-td ng-block-button ' . esc_attr( $element->class ) . '" style="' . implode( ';', $styles ) . '">' . $element->innertext . '</td><td style="width: ' . esc_attr( $pct ) . '%;"></td>';
			} else {
				$element->outertext = '<td class="ng-block-td ng-block-button ' . esc_attr( $element->class ) . '" style="' . implode( ';', $styles ) . '">' . $element->innertext . '</td>';
			}

		}

		$replace = '.wp-block-newsletterglue-buttons';
		foreach( $output->find( $replace ) as $key => $element ) {
			$element->outertext = '<table width="auto" cellpadding="0" cellspacing="0" class="ng-block ' . esc_attr( $element->class ) . '" border="0" align="' . esc_attr( $align ) . '"><tr>' . $element->innertext . '</tr></table>';
		}

		$output->save();

		$block_content = (string) $output;

		return $block_content;
	}

}

return new NGL_Render_Buttons;
