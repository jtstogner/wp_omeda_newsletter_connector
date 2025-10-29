<?php
/**
 * Post title.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Generate markup in email for post title.
 */
function newsletterglue_get_post_title_markup( $block, $post ) {
	$attrs = newsletterglue_get_attrs( $block, 'post-title' );

	$level 	= isset( $attrs[ 'level' ] ) ? esc_attr( $attrs[ 'level' ] ) : 2;
	$link  	= isset( $attrs[ 'isLink' ] ) ? esc_attr( $attrs[ 'isLink' ] ) : false;
	$target = isset( $attrs[ 'linkTarget' ] ) ? esc_attr( $attrs[ 'linkTarget' ] ) : '_self';
	$align  = isset( $attrs[ 'textAlign' ] ) ? esc_attr( $attrs[ 'textAlign' ] ) : '';

	$html = '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="masthead" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;"><tbody><tr><td align="' . $align . '" style="padding: 10px 20px;">';

	if ( $link ) {
		$html .= '<h' . esc_attr( $level ) . ' class="ngl-ignore-mrkp" style="text-align: ' . $align . ';"><a href="' . esc_url( get_permalink( $post->ID ) ) . '" class="ngl-title-to-post" target="' . $target . '">' . esc_html( $post->post_title ) . '</a></h' . $level . '>';
	} else {
		$html .= '<h' . esc_attr( $level ) . ' class="ngl-ignore-mrkp" style="text-align: ' . $align . ';">' . esc_html( $post->post_title ) . '</h' . $level . '>';
	}

	$html .= '</td></tr></tbody></table>';

	return $html;
}