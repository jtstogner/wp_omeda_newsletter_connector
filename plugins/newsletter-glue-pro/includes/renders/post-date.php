<?php
/**
 * Post date.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Render post date block.
 */
add_filter( 'render_block', 'newsletterglue_render_post_date', 50, 3 );
function newsletterglue_render_post_date( $block_content, $block ) {
	global $ngl_post_id;

	if ( ! defined( 'NGL_IN_EMAIL' ) ) {
		return $block_content;
	}

    if ( "core/post-date" !== $block['blockName'] ) return $block_content;

	$attrs = $block['attrs'];

	$format  = ! empty( $attrs[ 'format' ] ) ? $attrs[ 'format' ] : get_option('date_format');
	$is_link = ! empty( $attrs[ 'isLink' ] ) ? 1 : 0;
	$type	 = ! empty( $attrs[ 'displayType' ] ) ? sanitize_text_field( $attrs[ 'displayType' ] ) : '';
	$align   = ! empty( $attrs[ 'textAlign' ] ) ? sanitize_text_field( $attrs[ 'textAlign' ] ) : 'left';

	$content = get_the_date( $format, $ngl_post_id );

	if ( $type === 'modified' ) {
		$content = get_the_modified_date( $format, $ngl_post_id );
	}

	if ( $is_link ) {
		$content = '<a href="' . get_permalink( $ngl_post_id ) . '">' . $content . '</a>';
	}

	return '<p style="text-align: ' . $align . ';">' . $content . '</p>';
}