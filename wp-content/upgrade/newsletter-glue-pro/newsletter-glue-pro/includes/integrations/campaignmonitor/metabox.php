<?php
/**
 * Newsletter Metabox.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$attributes = null;

?>

<div class="ngl-metabox-flex" <?php apply_filters( 'ngl_metabox_class_attribute', $attributes );?>>

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<label for="ngl_lists"><?php esc_html_e( 'Lists', 'newsletter-glue' ); ?></label>
		</div>
		<div class="ngl-field">
			<?php
				if ( isset( $settings->lists ) ) {
					$lists = $settings->lists;
				} else {
					$lists = newsletterglue_get_option( 'lists', $app );
				}

				newsletterglue()::$the_lists = $api->get_lists();
				$the_lists = newsletterglue()::$the_lists;
				$the_lists_default_options = is_array( $lists ) ? $lists : explode( ',', $lists );

				newsletterglue_select_field( array(
					'id' 			=> 'ngl_lists',
					'legacy'		=> true,
					'helper'		=> __( 'Who receives your email.', 'newsletter-glue' ),
					'options'		=> $the_lists,
					'default'		=> apply_filters( 'ngl_lists_default_option', $the_lists_default_options ),
					'multiple'		=> true,
					'placeholder'	=> __( 'Select list(s)', 'newsletter-glue' ),
				) );
			?>
		</div>
	</div>

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<label for="ngl_segments"><?php esc_html_e( 'Segments', 'newsletter-glue' ); ?></label>
		</div>
		<div class="ngl-field">
			<?php
				if ( isset( $settings->segments ) ) {
					$segments = $settings->segments;
				} else {
					$segments = newsletterglue_get_option( 'segments', $app );
				}
				$segments_default_option = is_array( $segments ) ? $segments : explode( ',', $segments );

				newsletterglue_select_field( array(
					'id' 			=> 'ngl_segments',
					'legacy'		=> true,
					'helper'		=> __( 'A specific group of subscribers.', 'newsletter-glue' ),
					'options'		=> $api->get_segments(),
					'default'		=> apply_filters( 'ngl_segments_default_option', $segments_default_option ),
					'multiple'		=> true,
					'placeholder'	=> __( 'Select segment(s)', 'newsletter-glue' ),
				) );

			?>
		</div>
	</div>

</div>