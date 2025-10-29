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

// Debug logging for divider values
$log_file = WP_CONTENT_DIR . '/ngl_latest_posts_divider_debug.log';
file_put_contents($log_file, "\n\n======== LATEST POSTS DIVIDER DEBUG AT " . date('Y-m-d H:i:s') . " ========\n", FILE_APPEND);
file_put_contents($log_file, "show_divider set: " . (isset($attrs['show_divider']) ? 'yes' : 'no') . "\n", FILE_APPEND);
file_put_contents($log_file, "show_divider value: " . (isset($attrs['show_divider']) ? var_export($attrs['show_divider'], true) : 'not set') . "\n", FILE_APPEND);
file_put_contents($log_file, "divider_size set: " . (isset($attrs['divider_size']) ? 'yes' : 'no') . "\n", FILE_APPEND);
file_put_contents($log_file, "divider_size value: " . (isset($attrs['divider_size']) ? var_export($attrs['divider_size'], true) : 'not set') . "\n", FILE_APPEND);

// Fix: Match the Post Embeds implementation exactly
$divider_size = isset( $attrs['show_divider'] ) ? (isset( $attrs['divider_size'] ) ? absint( $attrs['divider_size'] ) : 1) : null;
file_put_contents($log_file, "Final divider_size value: " . var_export($divider_size, true) . "\n", FILE_APPEND);
$divider_bg = ! empty( $attrs['divider_bg'] ) ? esc_attr( $attrs['divider_bg'] ) : '#eeeeee';
$is_full = $table_ratio === 'full' ? 1 : 3;

// Email parts.
include 'email-top.php';
include 'email-body.php';
include 'email-bottom.php';
