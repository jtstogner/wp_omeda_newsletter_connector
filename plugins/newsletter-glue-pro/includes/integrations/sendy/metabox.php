<?php
/**
 * Newsletter Metabox.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( isset( $settings->brand ) ) {
	$brand = $settings->brand;
} else {
	$brand = newsletterglue_get_option( 'brand', $app );
}

if ( ! $brand ) {
	$brand = 1;
}

?>

<div class="ngl-metabox-flex">

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<label for="ngl_brand"><?php esc_html_e( 'Brand', 'newsletter-glue' ); ?></label>
		</div>
		<div class="ngl-field">
			<?php
				newsletterglue_select_field( array(
					'id' 			=> 'ngl_brand',
					'legacy'		=> true,
					'helper'		=> __( 'The brand of your Sendy installation.', 'newsletter-glue' ),
					'options'		=> $api->get_brands(),
					'default'		=> $brand,
				) );

			?>
		</div>
	</div>

	<div class="ngl-metabox-flex ngl-metabox-segment">
		<div class="ngl-metabox-header">
			<label for="ngl_lists"><?php esc_html_e( 'List', 'newsletter-glue' ); ?></label>
		</div>
		<div class="ngl-field">
			<?php
				$lists = '';
				if ( isset( $settings->lists ) ) {
					$lists = $settings->lists;
				} else {
					$lists = newsletterglue_get_option( 'lists', $app );
				}

				newsletterglue()::$the_lists = $defaults->lists;
				$the_lists = newsletterglue()::$the_lists;

				newsletterglue_select_field( array(
					'id' 			=> 'ngl_lists',
					'legacy'		=> true,
					'multiple'		=> true,
					'helper'		=> __( 'The mailing list within your brand.', 'newsletter-glue' ),
					'class'			=> 'is-required',
					'options'		=> $the_lists,
					'default'		=> is_array( $lists ) ? $lists : explode( ',', $lists ),
				) );
			?>
		</div>
	</div>

</div>