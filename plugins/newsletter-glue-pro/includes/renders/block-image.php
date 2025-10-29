<?php
/**
 * Image block render.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_Render_Image class.
 */
class NGL_Render_Image {

	public $app;

	/**
	 * Constructor.
	 */
	public function __construct() {

		add_filter( 'render_block', array( $this, 'render_block_email' ), 50, 3 );

		$this->app = newsletterglue_default_connection();
	}

	/**
	 * Render.
	 */
	public function render_block_email( $block_content, $block ) {

		if ( ! defined( 'NGL_IN_EMAIL' ) )
			return $block_content;

		if ( "newsletterglue/image" !== $block['blockName'] )
			return $block_content;

		if ( isset( $block[ 'attrs' ][ 'show_in_email' ] ) ) {
			return null;
		}

		$attrs = $block['attrs'];
		$keep_size = ! empty( $attrs['mobile_keep_size'] ) ? true : false;
		$mobile_w = ! empty( $attrs['mobile_width'] ) ? esc_attr( $attrs['mobile_width'] ) : false;

		$html = $block_content;

		$output = new simple_html_dom();
		$output->load( $html, true, false );

		$replace = 'img';
		foreach( $output->find( $replace ) as $key => $element ) {
			if ( ! $keep_size && empty( $mobile_w ) ) {
				$element->class .= ' ng-standard-img';
			}
		}

		$output->save();

		$block_content = (string) $output;

		return $block_content;
	}

}

return new NGL_Render_Image;
