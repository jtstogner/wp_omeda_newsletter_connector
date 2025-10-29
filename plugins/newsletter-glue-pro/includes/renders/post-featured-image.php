<?php
/**
 * Post title.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Generate markup in email for post image.
 */
function newsletterglue_get_featured_image_markup( $block, $post ) {
	$attrs = newsletterglue_get_attrs( $block, 'post-featured-image' );

	$return_str = '';

	$show_caption = false;

	$use_image = true;

	$position = 'center';

	$post_id = $post->ID;

	$link_featured_image = isset( $attrs[ 'isLink' ] ) && $attrs[ 'isLink' ] == true ? 'yes' : 'no';
	$width  			 = isset( $attrs[ 'width' ] ) && strstr( $attrs[ 'width' ], 'px' ) ? esc_attr( $attrs[ 'width' ] ) : 'original';
	$height  			 = isset( $attrs[ 'height' ] ) && strstr( $attrs[ 'height' ], 'px' ) ? esc_attr( $attrs[ 'height' ] ) : 'auto';
	$align  			 = isset( $attrs[ 'align' ] ) ? esc_attr( $attrs[ 'align' ] ) : '';

	if ( $width == 'original' ) {
		$width = '560px';
	}

	if ( $align === 'full' ) {
		$padding = '0px';
		$width = '600px';
	} else {
		$padding = '10px 20px';
	}

	$return_str = '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="masthead" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;"><tbody><tr><td align="' . $align . '" style="padding: ' . $padding . ';">';

	if ( $use_image ) {
		$url = wp_get_attachment_url( get_post_thumbnail_id( $post_id ) );
		if ( $url ) {
			$image = '<img src="' . $url . '" class="masthead ng-standard-img callout-img" width="' . absint( $width ) . '" height="' . $height . '" style="display: block; max-width: ' . $width . '; height: ' . $height. ';">';
			if ( $link_featured_image && $link_featured_image === 'yes' ) {
				$return_str .= '<a href="' . esc_url( get_permalink( $post_id ) ) . '" class="masthead-link">' . $image . '</a>';
			} else {
				$return_str .= $image;
			}
		}
	}

	$return_str .= '</td></tr></tbody></table>';

	if( apply_filters( 'newsletterglue_show_masthead_caption', $show_caption ) ) {
		$return_str .=  "<table cellpadding=\"10\"> <tr><td> <small><center> " . get_the_post_thumbnail_caption( $post_id ) . "</center><small></td></tr></table>";
	}

	return $return_str;
}