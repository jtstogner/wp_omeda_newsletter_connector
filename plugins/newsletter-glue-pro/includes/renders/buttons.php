<?php
/**
 * Buttons block.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add original alignment options to buttons markup.
 */
add_filter( 'render_block', 'newsletterglue_fix_button_alignment', 10, 3 );
function newsletterglue_fix_button_alignment( $block_content, $block ) {

	if ( ! defined( 'NGL_IN_EMAIL' ) ) {
		return $block_content;
	}

    if ( "core/buttons" !== $block['blockName'] ) return $block_content;

	$align = isset( $block[ 'attrs' ][ 'layout' ][ 'justifyContent' ] ) ? $block[ 'attrs' ][ 'layout' ][ 'justifyContent' ] : '';

	if ( $align ) {
		$block_content = str_replace( 'wp-block-buttons">', 'is-content-justification-' . $align . ' wp-block-buttons">', $block_content );
	}

    return $block_content;
}