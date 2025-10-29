<?php
/**
 * Newsletter Metabox.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$attributes = null;

?>

<?php do_action( 'newsletterglue_edit_more_settings', $this->app, $settings, false ); ?>

<h4 class="ngl-edit-more">
	<a href="#"><?php _e( 'Edit more settings', 'newsletter-glue' ); ?> <svg stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></a>
</h4>

<div class="ngl-metabox-flex ngl-edit-more-box is-hidden">

	<div class="ngl-metabox-flex">
		<?php if ( ! newsletterglue_is_automation() ) : ?>
		<div class="ngl-metabox-header">
			<label for="ngl_schedule"><?php esc_html_e( 'Send now or save for later', 'newsletter-glue' ); ?></label>
		</div>
		<div class="ngl-field">
			<?php
				$schedule = isset( $settings->schedule ) ? $settings->schedule : newsletterglue_get_option( 'schedule', 'global' );
			if ( empty( $schedule ) ) {
				$schedule = 'draft';
			}
				newsletterglue_select_field(
					array(
						'id'      => 'ngl_schedule',
						'options' => $this->get_schedule_options(),
						'default' => $schedule,
						'legacy'  => true,
						'class'   => 'is-required',
					)
				);
			?>
		</div>
		<?php endif; ?>
	</div>

	<div class="ngl-metabox-flex">
	</div>

</div>

<div class="ngl-metabox-flex ngl-edit-more-box is-hidden">

	<div class="ngl-subsection-title">
		<h4><?php echo esc_html__( 'UTM builder', 'newsletter-glue' ); ?></h4>
		<span><?php printf( esc_html__( 'Generate UTM codes to track subscribers when they click on links in your emails. %s', 'newsletter-glue' ), '<a href="#">' . esc_html__( 'Learn more.', 'newsletter-glue' ) . '</a>' ); ?></span>
		<span><?php printf( esc_html__( 'Important: If you already have Google Analytics connected to your %s account, you will need to turn it off first to use this feature.', 'newsletter-glue' ), esc_attr( newsletterglue_get_name( newsletterglue_default_connection() ) ) ); ?></span>
	</div>

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<label for="ngl_utm_source"><?php esc_html_e( 'UTM Source', 'newsletter-glue' ); ?></label>
		</div>
		<div class="ngl-field">
			<?php
				$utm_source = isset( $settings->utm_source ) ? $settings->utm_source : $defaults->utm_source;
				$utm_source = $utm_source == '[none]' ? '' : $utm_source;
				newsletterglue_text_field(
					array(
						'id'     => 'ngl_utm_source',
						'helper' => __( 'e.g. newsletter, ng', 'newsletter-glue' ),
						'value'  => apply_filters( 'ngl_utm_source', $utm_source ),
					)
				);
				?>
		</div>
	</div>

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<label for="ngl_utm_campaign"><?php esc_html_e( 'UTM Campaign', 'newsletter-glue' ); ?></label>
		</div>
		<div class="ngl-field">
			<?php
				$utm_campaign = isset( $settings->utm_campaign ) ? $settings->utm_campaign : $defaults->utm_campaign;

				$campaign_help = __( 'e.g. weekly_update, ', 'newsletter-glue' );

				$tags_array     = array( '<u>{{newsletter_title}}</u>' );
				$tags           = implode( ', ', $tags_array );
				$campaign_help .= ' <span class="ngl-input-tags" data-field="ngl_utm_campaign">' . $tags . '</span>';

				$utm_campaign = $utm_campaign == '[none]' ? '' : $utm_campaign;

				newsletterglue_text_field(
					array(
						'id'     => 'ngl_utm_campaign',
						'helper' => $campaign_help,
						'value'  => apply_filters( 'ngl_utm_campaign', $utm_campaign ),
					)
				);
				?>
		</div>
	</div>

</div>

<div class="ngl-metabox-flex ngl-edit-more-box is-hidden">

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<label for="ngl_utm_medium"><?php esc_html_e( 'UTM Medium', 'newsletter-glue' ); ?></label>
		</div>
		<div class="ngl-field">
			<?php
				$utm_medium = isset( $settings->utm_medium ) ? $settings->utm_medium : $defaults->utm_medium;
				$utm_medium = $utm_medium == '[none]' ? '' : $utm_medium;
				newsletterglue_text_field(
					array(
						'id'     => 'ngl_utm_medium',
						'helper' => __( 'e.g. email', 'newsletter-glue' ),
						'value'  => apply_filters( 'ngl_utm_medium', $utm_medium ),
					)
				);
				?>
		</div>
	</div>

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<label for="ngl_utm_content"><?php esc_html_e( 'UTM Content', 'newsletter-glue' ); ?></label>
		</div>
		<div class="ngl-field">
			<?php
				$utm_content = isset( $settings->utm_content ) ? $settings->utm_content : $defaults->utm_content;
				$utm_content = $utm_content == '[none]' ? '' : $utm_content;
				newsletterglue_text_field(
					array(
						'id'     => 'ngl_utm_content',
						'helper' => '',
						'value'  => apply_filters( 'ngl_utm_content', $utm_content ),
					)
				);
				?>
		</div>
	</div>

</div>
