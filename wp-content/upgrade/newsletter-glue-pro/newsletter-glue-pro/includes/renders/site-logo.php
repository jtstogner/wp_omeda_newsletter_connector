<?php
/**
 * Site logo.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Render logo block.
 */
add_filter( 'render_block', 'newsletterglue_render_logo_block', 50, 3 );
function newsletterglue_render_logo_block( $block_content, $block ) {

	if ( ! defined( 'NGL_IN_EMAIL' ) ) {
		return $block_content;
	}

    if ( "core/site-logo" !== $block['blockName'] ) return $block_content;

	$block_content = str_replace( 'class="custom-logo', 'class="ngl-ignore-mrkp custom-logo', $block_content );

	$is_link 	= isset( $block[ 'attrs' ][ 'isLink' ] ) ? false : true;
	$align 		= isset( $block[ 'attrs' ][ 'align' ] ) ? esc_attr( $block[ 'attrs' ][ 'align' ] ) : 'left';
	$width 		= isset( $block[ 'attrs' ][ 'width' ] ) ? esc_attr( $block[ 'attrs' ][ 'width' ] ) : '120';
	$target 	= isset( $block[ 'attrs' ][ 'linkTarget' ] ) ? esc_attr( $block[ 'attrs' ][ 'linkTarget' ] ) : '_self';

	$height = 'auto';

	$html = '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="masthead" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;"><tbody><tr><td align="' . $align . '" style="padding: 10px 20px;">';

	$array = array();

	preg_match( '/src="([^"]*)"/i', $block_content, $array );
	
	if ( ! isset( $array[1] ) ) {
		return '';
	}

	$url = $array[1];

	$image = '<img src="' . $url . '" class="masthead ng-standard-img callout-img" width="' . absint( $width ) . '" height="' . $height . '" style="display: block; max-width: ' . $width . 'px; height: ' . $height. ';">';

	if ( $is_link ) {
		$html .= '<a href="' . home_url() . '" target="' . $target . '" style="width: ' . $width . 'px;display: block;">' . $image . '</a>';
	} else {
		$html .= $image;
	}
	

	$html .= '</td></tr></tbody></table>';

    return $html;
}