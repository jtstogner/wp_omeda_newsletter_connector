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
			<label for="ngl_lists"><?php esc_html_e( 'Default form', 'newsletter-glue' ); ?></label>
			<?php $api->input_verification_info(); ?>
		</div>
		<div class="ngl-field">
			<?php
				$list  = newsletterglue_get_option( 'lists', $app );

				$lists = $api->get_lists();
				if ( ! $list ) {
					$list = array_keys( $lists );
					$list = $list[0];
				}

				newsletterglue_select_field( array(
					'id' 			=> 'ngl_lists',
					'legacy'		=> true,
					'helper'		=> __( 'This is used for subscribing to a form. ConvertKit API does not support sending to a specific form yet.', 'newsletter-glue' ),
					'class'			=> 'ngl-ajax',
					'options'		=> $lists,
					'default'		=> $list,
				) );
			?>
		</div>
	</div>

	<div class="ngl-metabox-flex">
	</div>

</div>