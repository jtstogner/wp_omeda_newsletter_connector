<?php
/**
 * Newsletter Metabox.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

global $post_type;

$draft_option = newsletterglue_get_option( 'schedule', 'global' ) === 'draft' ? true : false;

$draft = isset( $settings->schedule ) && $settings->schedule === 'draft' ? true : $draft_option;

if ( newsletterglue_is_automation() ) {
	if ( isset( $settings->send_type ) && $settings->send_type === 'draft' ) {
		$draft = true;
	} else {
		if ( ! isset( $settings->send_type ) ) {
			$draft = true;
			$state = 'ready';
		}
	}
	if ( ! empty( $settings->send_type ) ) {
		$state = $automation->is_enabled() ? 'enabled' : 'paused';
	}
}
?>

	<?php $api->show_settings( $settings, $defaults, $post ); ?>

	</div>

</div>

<?php if ( ! in_array( $post_type, newsletterglue_get_core_cpts() ) || defined( 'NEWSLETTERGLUE_DEMO' ) ) : ?>
<div class="ngl-metabox ngl-metabox-flex alt3 ngl-sending-box <?php if ( ! $hide ) echo 'is-hidden'; ?>">

	<div class="ngl-metabox-flex ngl-metabox-flex-toggle">

		<div class="ngl-field ngl-field-master">
			<input type="hidden" name="ngl_double_confirm" id="ngl_double_confirm" value="no" />
			<?php if ( newsletterglue_is_automation() ) : ?>
			<input type="checkbox" name="ngl_send_newsletter" id="ngl_send_newsletter" value="1" <?php checked( true, ( $automation->is_enabled()  ) ); ?> />
			<?php else : ?>
			<input type="checkbox" name="ngl_send_newsletter" id="ngl_send_newsletter" value="1" />
			<?php endif; ?>
			<label for="ngl_send_newsletter">

				<?php if ( newsletterglue_is_automation() ) : ?>

				<?php
					echo '<span class="ngl-automation-state" data-state="' . esc_attr( $state ) . '"></span>';
				?>

				<?php if ( ! isset( $settings->send_type ) ) : ?>
				<span class="ngl-stateful-send-text"><?php echo esc_html__( 'Run automation', 'newsletter-glue' ); ?></span>
				<?php echo '<span class="ngl-field-master-help">' . esc_html__( '(when post is published/updated)', 'newsletter-glue' ) . '</span>'; ?>
				<?php else : ?>
				<span class="ngl-stateful-send-text"><?php echo $automation->is_enabled() ? esc_html__( 'Run automation', 'newsletter-glue' ) : esc_html__( 'Run automation', 'newsletter-glue' ); ?></span>
				<?php echo $automation->is_enabled() ? '<span class="ngl-field-master-help">' . esc_html__( '(when post is published/updated)', 'newsletter-glue' ) . '</span>' : '<span class="ngl-field-master-help">' . esc_html__( '(when post is published/updated)', 'newsletter-glue' ) . '</span>'; ?>
				<?php endif; ?>

				<?php else : ?>
				<span class="ngl-stateful-send-text"><?php echo $draft ? sprintf( esc_html__( 'Save as draft in %s', 'newsletter-glue' ), esc_attr( newsletterglue_get_name( $app ) ) ) : esc_html__( 'Send as newsletter', 'newsletter-glue' ); ?></span>
				<?php endif; ?>

				<?php if ( ! newsletterglue_is_automation() ) : ?>
				<span class="ngl-field-master-help"><?php echo esc_html__( '(when post is published/updated)', 'newsletter-glue' ); ?></span>
				<?php endif; ?>

			</label>
		</div>

	</div>

	<div class="ngl-metabox-flex">
		<div class="ngl-not-ready is-hidden">
			<div class="ngl-metabox-msg is-error"><?php _e( 'Almost ready. Just fill in the blank red boxes.' ,'newsletter-glue' ); ?></div>
		</div>
	</div>

</div>
<?php endif; ?>