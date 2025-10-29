<?php
/**
 * Metadata block render.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_Render_Metadata class.
 */
class NGL_Render_Metadata {

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

		if ( "newsletterglue/meta-data" !== $block['blockName'] )
			return $block_content;

		if ( isset( $block[ 'attrs' ][ 'show_in_web' ] ) ) {
			return null;
		}

		$html = $block_content;

		$output = new simple_html_dom();
		$output->load( $html, true, false );

		$replace = 'table.wp-block-newsletterglue-meta-data';
		foreach( $output->find( $replace ) as $key => $element ) {
			$post_id = ! empty( $block['attrs']['post_id'] ) ? $block['attrs']['post_id'] : 0;
			$date_format = $element->{'data-date-format'};
			$post = get_post( $post_id );
			$post_content = ! empty( $post->post_content ) ? $post->post_content : '';
			$read_time 	= newsletterglue_content_estimated_reading_time( $post_content );

			if ( $element->find( '.ngl-metadata-date-ajax', 0 ) ) {
				$element->find( '.ngl-metadata-date-ajax', 0 )->innertext = date_i18n( $date_format, get_post_timestamp( $post_id ) );
			}

			if ( $element->find( '.ngl-metadata-readtime-ajax', 0 ) ) {
				$element->find( '.ngl-metadata-readtime-ajax', 0 )->innertext = ' ' . $read_time;
			}

			if ( $element->find( '.ng-block-url' ) ) {
				$element->find( '.ng-block-url', 0 )->outertext = '';
			}
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

		if ( "newsletterglue/meta-data" !== $block['blockName'] )
			return $block_content;

		if ( isset( $block[ 'attrs' ][ 'show_in_email' ] ) ) {
			return null;
		}

		$html = $block_content;

		$output = new simple_html_dom();
		$output->load( $html, true, false );

		$replace = 'table.wp-block-newsletterglue-meta-data';
		foreach( $output->find( $replace ) as $key => $element ) {
			$post_id = ! empty( $block['attrs']['post_id'] ) ? $block['attrs']['post_id'] : 0;
			$date_format = $element->{'data-date-format'};
			$post = get_post( $post_id );
			$post_content = ! empty( $post->post_content ) ? $post->post_content : '';
			$read_time 	= newsletterglue_content_estimated_reading_time( $post_content );

			if ( $element->find( '.ngl-metadata-date-ajax', 0 ) ) {
				$element->find( '.ngl-metadata-date-ajax', 0 )->innertext = date_i18n( $date_format, get_post_timestamp( $post_id ) );
			}

			if ( $element->find( '.ngl-metadata-readtime-ajax', 0 ) ) {
				$element->find( '.ngl-metadata-readtime-ajax', 0 )->innertext = ' ' . $read_time;
			}
		}

		$output->save();

		$block_content = (string) $output;

		return $block_content;
	}

}

return new NGL_Render_Metadata;
