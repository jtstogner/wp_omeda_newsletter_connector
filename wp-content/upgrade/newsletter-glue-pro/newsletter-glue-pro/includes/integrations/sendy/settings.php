<?php
/**
 * Sendy.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-metabox-flex">

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<label for="ngl_brand"><?php esc_html_e( 'Brand', 'newsletter-glue' ); ?></label>
			<?php $api->input_verification_info(); ?>
		</div>
		<div class="ngl-field">
			<?php
				newsletterglue_text_field( array(
					'id' 			=> 'ngl_brand',
					'helper'		=> __( 'The brand of your Sendy installation.', 'newsletter-glue' ),
					'value'			=> newsletterglue_get_option( 'brand', $app ),
					'class'			=> 'ngl-ajax',
				) );
			?>
		</div>
	</div>

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<label for="ngl_lists"><?php esc_html_e( 'List', 'newsletter-glue' ); ?></label>
			<?php $api->input_verification_info(); ?>
		</div>
		<div class="ngl-field">
			<?php
				newsletterglue_text_field( array(
					'id' 			=> 'ngl_lists',
					'helper'		=> __( 'The mailing list within your brand.', 'newsletter-glue' ),
					'value'			=> newsletterglue_get_option( 'lists', $app ),
					'class'			=> 'ngl-ajax',
				) );
			?>
		</div>
	</div>

</div>