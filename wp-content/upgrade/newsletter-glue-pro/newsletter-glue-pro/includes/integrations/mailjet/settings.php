<?php
/**
 * ActiveCampaign.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-metabox-flex">

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<label for="ngl_lists"><?php esc_html_e( 'Contact list', 'newsletter-glue' ); ?></label>
			<?php $api->input_verification_info(); ?>
		</div>
		<div class="ngl-field">
			<?php
				$list  = newsletterglue_get_option( 'lists', $app );
				$lists = $api->get_lists();
				if ( ! $list ) {
					if ( ! empty( $lists ) ) {
						$list = array_keys( $lists );
						$list = $list[0];
					}
				}
				newsletterglue_select_field( array(
					'id' 			=> 'ngl_lists',
					'legacy'		=> true,
					'helper'		=> sprintf( __( 'Who receives your email. %s', 'newsletter-glue' ), '<a href="https://app.mailjet.com/contacts" target="_blank" class="ngl-link-inline-svg">' . __( 'Manage contact lists', 'newsletter-glue' ) . ' [externallink]</a>' ),
					'class'			=> 'ngl-ajax',
					'options'		=> $lists,
					'default'		=> $list,
				) );
			?>
		</div>
	</div>

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<label for="ngl_segments"><?php esc_html_e( 'Segment', 'newsletter-glue' ); ?></label>
			<?php $api->input_verification_info(); ?>
		</div>
		<div class="ngl-field">
			<?php
				$segment = newsletterglue_get_option( 'segments', $app );

				if ( ! $segment ) {
					$segment = '_all';
				}

				newsletterglue_select_field( array(
					'id' 			=> 'ngl_segments',
					'legacy'		=> true,
					'helper'		=> sprintf( __( 'A specific group of subscribers. %s', 'newsletter-glue' ), '<a href="https://app.mailjet.com/segmentation/create" target="_blank" class="ngl-link-inline-svg">' . __( 'Create segment', 'newsletter-glue' ) . ' [externallink]</a>' ),
					'options'		=> $api->get_segments(),
					'default'		=> $segment,
					'class'			=> 'ngl-ajax',
					'placeholder'	=> __( 'Send to a specific segment...', 'newsletter-glue' ),
				) );
			?>
		</div>
	</div>

</div>