<?php
/**
 * Email.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<a href="<?php echo esc_url( $item[ 'permalink' ] ); ?>" target="_blank" style="display: block;padding: 2px 0 !important;"><img src="<?php echo esc_url( $item[ 'featured_image' ] ); ?>" alt="" width="<?php echo esc_attr( $td_image ); ?>" height="auto" style="max-width: <?php echo esc_attr( $td_image ); ?>px !important;border-radius: <?php echo absint( $image_radius ); ?>px !important;" class="ngl-core-image" /></a>