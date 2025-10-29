<?php
/**
 * Sailthru.
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
					'helper'		=> sprintf( __( 'Who receives your email. %s', 'newsletter-glue' ), '<a href="https://my.sailthru.com/lists" target="_blank" class="ngl-link-inline-svg">' . __( 'Manage contact lists', 'newsletter-glue' ) . ' [externallink]</a>' ),
					'class'			=> 'ngl-ajax',
					'options'		=> $lists,
					'default'		=> $list,
				) );
			?>
		</div>
	</div>
</div>