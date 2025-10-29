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
			<label for="ngl_from_name"><?php esc_html_e( 'From name', 'newsletter-glue' ); ?></label>
		</div>
		<div class="ngl-field">
			<?php
				$ngl_fromname_value = isset( $settings->from_name ) ? $settings->from_name : $defaults->from_name;
				newsletterglue_text_field( array(
					'id' 			=> 'ngl_from_name',
					'helper'		=> __( 'Your subscribers will see this name in their inboxes.', 'newsletter-glue' ),
					'value'			=> apply_filters( 'ngl_from_name', $ngl_fromname_value ),
					'class'			=> 'is-required',
				) );
			?>
		</div>
	</div>

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<label for="ngl_from_email"><?php esc_html_e( 'From email', 'newsletter-glue' ); ?></label>
			<?php $this->email_verification_info(); ?>
		</div>
		<div class="ngl-field">
			<?php
				$verify = ! $this->has_email_verify() ? 'no-support-verify' : '';
				$ngl_email = isset( $settings->from_email ) ? $settings->from_email : $defaults->from_email;
				newsletterglue_text_field( array(
					'id' 			=> 'ngl_from_email',
					'helper'		=> __( 'Subscribers will see and reply to this email address.', 'newsletter-glue' ),
					'value'			=> apply_filters( 'ngl_email', $ngl_email ),
					'class'			=> 'is-required ' . $verify,
				) );
			?>
			<?php if ( ! $this->has_email_verify() ) { ?>
			<div class="ngl-helper">
				<?php
					if ( $this->get_email_verify_help() ) {
						echo sprintf( esc_html__( 'Only use verified email addresses. %s', 'newsletter-glue' ), '<a href="' . esc_url( $this->get_email_verify_help() ) . '" target="_blank" class="ngl-link-inline-svg">' . esc_html__( 'Learn more', 'newsletter-glue' ) . ' <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a>' );
					}
				?>
			</div>
			<?php } ?>
		</div>
	</div>

</div>