<?php
/**
 * Email.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$content_width = 600;
$outer_width   = 600;

$colspan = 1;
if ( ! empty( $padding[ 'left' ] ) ) {
	$colspan += 1;
	$content_width = $content_width - absint( $padding[ 'left' ] );
}
if ( ! empty( $padding[ 'right' ] ) ) {
	$colspan += 1;
	$content_width = $content_width - absint( $padding[ 'right' ] );
}
if ( ! empty( $margin[ 'left' ] ) ) {
	$content_width = $content_width - absint( $margin[ 'left' ] );
	$outer_width = $outer_width - absint( $margin[ 'right' ] );
}
if ( ! empty( $margin[ 'right' ] ) ) {
	$content_width = $content_width - absint( $margin[ 'right' ] );
	$outer_width = $outer_width - absint( $margin[ 'right' ] );
}
if ( ! empty( $border_size ) ) {
	$content_width = $content_width - ( absint( $border_size ) * 2 );
}

$attrs = $block[ 'attrs' ];
$font = isset( $attrs['font']['style']['fontFamily'] ) ? $attrs['font']['style']['fontFamily'] : '';

$divider_size = isset( $attrs['show_divider'] ) ? (isset( $attrs['divider_size'] ) ? absint( $attrs['divider_size'] ) : 1) : null;
$divider_bg = ! empty( $attrs['divider_bg'] ) ? esc_attr( $attrs['divider_bg'] ) : '#eeeeee';

// Calculate the correct colspan for dividers based on table structure
if ($table_ratio === 'full') {
    $is_full = 1; // Full width table has 1 column
} else {
    // For image + content layouts, we have the image column, a spacer column, and the content column
    $is_full = $show_image === 'no-images' ? 1 : 3;
}

// Email parts.
include 'email-top.php';
include 'email-body.php';
include 'email-bottom.php';
