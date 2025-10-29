<?php
/**
 * Admin Functions.
 * 
 * @package Newsletter Glue
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Checks if a template is a shortcut.
 * 
 * @param integer $post_id The post ID.
 */
function newsletterglue_is_shortcut( $post_id ) {
	$shortcuts = get_option( 'newsletterglue_template_shortcuts' );

	return isset( $shortcuts[ $post_id ] ) ? true : false;
}

/**
 * Add before templates & styles.
 */
function newsletterglue_add_topbar_templates() {

	$screen = get_current_screen();

	if ( in_array( $screen->id, array( 'edit-ngl_template', 'edit-ngl_pattern' ) ) ) {
		add_action( 'all_admin_notices', 'newsletterglue_add_topbar_content' );
	}

	if ( in_array( $screen->id, array( 'edit-newsletterglue', 'edit-ngl_automation', 'edit-ngl_log' ) ) ) {
		add_action( 'all_admin_notices', 'newsletterglue_admin_campaigns_bar' );
	}

}
add_action( 'load-edit.php', 'newsletterglue_add_topbar_templates' );

/**
 * Top bar content.
 */
function newsletterglue_admin_campaigns_bar() {

	require_once NGL_PLUGIN_DIR . 'includes/admin/views/topbar.php';

	$tab1_active = '';
	$tab2_active = '';

	if ( isset( $_GET['post_type'] ) && 'newsletterglue' === $_GET['post_type'] ) { // phpcs:ignore
		$tab1_active = 'nglue-active';
	}

	if ( isset( $_GET['post_type'] ) && in_array( $_GET['post_type'], array( 'ngl_automation', 'ngl_log' ) ) ) { // phpcs:ignore
		$tab2_active = 'nglue-active';
	}

	$is_log = isset( $_GET['post_type'] ) && in_array( $_GET['post_type'], array( 'ngl_log' ) ) ? true : false; // phpcs:ignore

	if ( $is_log ) {
		$id = isset( $_GET['automation_id'] ) ? absint( $_GET['automation_id'] ) : 0; // phpcs:ignore
		if ( $id ) {
			$automation = get_post( $id );
			$title = ! empty( $automation->post_title ) ? $automation->post_title : '';
			 /* translators: %s: title */
			$post_title = sprintf( __( 'Email log: %s', 'newsletter-glue' ), $title );
		} else {
			$post_title = '';
		}
	} else {
		$id = 0;
	}
	?>

	<div class="nglue-main">
		<div class="components-panel__body nglue-tabs is-opened">
			<div class="components-panel__row">
				<ul>
					<li><a class="components-button is-link <?php echo esc_attr( $tab1_active ); ?>" href="<?php echo esc_url( admin_url( 'edit.php?post_type=newsletterglue' ) ); ?>"><?php _e( 'Campaigns', 'newsletter-glue' ); ?></a></li>
					<li><a class="components-button is-link <?php echo esc_attr( $tab2_active ); ?>" href="<?php echo esc_url( admin_url( 'edit.php?post_type=ngl_automation' ) ); ?>"><?php _e( 'Automated emails', 'newsletter-glue' ); ?></a></li>
				</ul>
			</div>
		</div>
	</div>
	<?php if ( $is_log ) : ?>
	<div class="nglue-main ngl-panel-row-sub">
		<div class="components-panel__body nglue-tabs is-opened">
			<div class="components-panel__row">
				<ul>
					<li><a class="components-button is-link" href="<?php echo esc_url( admin_url( 'edit.php?post_type=ngl_automation' ) ); ?>"><?php _e( 'Automated emails', 'newsletter-glue' ); ?></a></li>
					<li class="spacer"><svg stroke="currentColor" fill="none" stroke-width="1.5" viewBox="0 0 24 24" ariaHidden="true" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"></path></svg></li>
					<li><a class="components-button is-link nglue-active" href="<?php echo esc_url( admin_url( 'edit.php?post_type=ngl_log&automation_id=' . $id ) ); ?>"><?php echo esc_html( $post_title ); ?></a></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="wrap"><h1 class="ngl-heading-inline wp-heading-inline"><?php echo esc_html( $post_title ); ?></h1></div>
	<br />
		<?php
	endif;

	if ( empty( get_option( 'newsletterglue_pro_license' ) ) ) {
		?>
<div class="nglue-main"><div class="components-panel__body nglue-alert is-opened"><div class="nglue-alert-icon"><svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg></div><div class="nglue-alert-info"><div class="nglue-alert-title">Add/update your license key</div><div class="nglue-alert-body">Your license key is invalid, expired or missing. Head to Pro license tab to fix.</div><div class="nglue-alert-body">Something not right? <a href="https://newsletterglue.com/contact/" target="_blank">Get help</a>.</div></div><div class="nglue-alert-actions"><a href="<?php echo esc_url( admin_url( 'admin.php?page=ngl-settings#/pro' ) ); ?>" class="components-button is-primary">Go to Pro license tab</a><div class="nglue-alert-link"><a class="components-external-link" href="https://newsletterglue.com/account" target="_blank" rel="external noopener noreferrer">Get license key from My Account<span data-wp-c16t="true" data-wp-component="VisuallyHidden" class="components-visually-hidden css-0 em57xhy0" style="border: 0px; clip: rect(1px, 1px, 1px, 1px); clip-path: inset(50%); height: 1px; margin: -1px; overflow: hidden; padding: 0px; position: absolute; width: 1px; overflow-wrap: normal;">(opens in a new tab)</span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="components-external-link__icon css-bqq7t3 etxm6pv0" role="img" aria-hidden="true" focusable="false"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a></div></div></div></div>
		<?php
	}

}

/**
 * Top bar content.
 */
function newsletterglue_add_topbar_content() {

	require_once NGL_PLUGIN_DIR . 'includes/admin/views/topbar.php';

	$tab1_active = '';
	$tab2_active = '';
	$tab3_active = '';

	if ( isset( $_GET['post_type'] ) && 'ngl_pattern' === $_GET['post_type'] ) { // phpcs:ignore
		$tab1_active = 'nglue-active';
	}

	if ( isset( $_GET['post_type'] ) && 'ngl_template' === $_GET['post_type'] ) { // phpcs:ignore
		$tab3_active = 'nglue-active';
	}

	if ( isset( $_GET['page'] ) && 'ngl-theme' == $_GET['page'] ) { // phpcs:ignore
		$tab2_active = 'nglue-active';
	}
	?>

	<div class="nglue-main">
		<div class="components-panel__body nglue-tabs is-opened">
			<div class="components-panel__row">
				<ul>
					<li><a class="components-button is-link <?php echo esc_attr( $tab3_active ); ?>" href="<?php echo esc_url( admin_url( 'edit.php?post_type=ngl_template' ) ); ?>"><?php echo esc_html__( 'Templates', 'newsletter-glue' ); ?></a></li>
					<li><a class="components-button is-link <?php echo esc_attr( $tab1_active ); ?>" href="<?php echo esc_url( admin_url( 'edit.php?post_type=ngl_pattern' ) ); ?>"><?php echo esc_html__( 'Patterns', 'newsletter-glue' ); ?></a></li>
					<li><a class="components-button is-link <?php echo esc_attr( $tab2_active ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=ngl-theme' ) ); ?>"><?php echo esc_html__( 'Global styles', 'newsletter-glue' ); ?></a></li>
				</ul>
			</div>
		</div>
	</div>

	<?php if ( empty( get_option( 'newsletterglue_pro_license' ) ) ) { ?>
<div class="nglue-main"><div class="components-panel__body nglue-alert is-opened"><div class="nglue-alert-icon"><svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg></div><div class="nglue-alert-info"><div class="nglue-alert-title">Add/update your license key</div><div class="nglue-alert-body">Your license key is invalid, expired or missing. Head to Pro license tab to fix.</div><div class="nglue-alert-body">Something not right? <a href="https://newsletterglue.com/contact/" target="_blank">Get help</a>.</div></div><div class="nglue-alert-actions"><a href="<?php echo esc_url( admin_url( 'admin.php?page=ngl-settings#/pro' ) ); ?>" class="components-button is-primary">Go to Pro license tab</a><div class="nglue-alert-link"><a class="components-external-link" href="https://newsletterglue.com/account" target="_blank" rel="external noopener noreferrer">Get license key from My Account<span data-wp-c16t="true" data-wp-component="VisuallyHidden" class="components-visually-hidden css-0 em57xhy0" style="border: 0px; clip: rect(1px, 1px, 1px, 1px); clip-path: inset(50%); height: 1px; margin: -1px; overflow: hidden; padding: 0px; position: absolute; width: 1px; overflow-wrap: normal;">(opens in a new tab)</span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="components-external-link__icon css-bqq7t3 etxm6pv0" role="img" aria-hidden="true" focusable="false"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a></div></div></div></div>
		<?php
	}
}

/**
 * Add help tooltips.
 * 
 * @param string $label A label to show in the tooltip.
 */
function newsletterglue_help_tip( $label ) {
	?>
	<div class="ngl-help-tip" aria-label="<?php echo esc_attr( $label ); ?>" data-microtip-position="right" data-microtip-size="large" role="tooltip">
		<svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 16 16" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 16A8 8 0 108 0a8 8 0 000 16zm.93-9.412l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM8 5.5a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
	</div>
	<?php
}

/**
 * Resets a pattern to original.
 */
function newsletterglue_reset_pattern_action() {

	if ( isset( $_GET['reset-pattern'] ) && current_user_can( 'manage_newsletterglue' ) ) { // phpcs:ignore
		$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0; // phpcs:ignore
		if ( $post_id ) {
			require_once( NGL_PLUGIN_DIR . 'includes/cpt/default-patterns.php' );
			$patterns = new NGL_Default_Patterns();
			$list = $patterns->get_patterns();
			$core_pattern = get_post_meta( $post_id, '_ngl_core_pattern', true );
			if ( $core_pattern && isset( $list[ $core_pattern ] ) ) {
				$pattern = $list[ $core_pattern ];
				$args = array(
					'post_type'     => 'ngl_pattern',
					'post_status'   => 'publish',
					'post_author'   => 1,
					'post_title'    => $pattern['title'],
					'post_content'  => $pattern['content'],
				);
				wp_update_post( array_merge( array( 'ID' => $post_id ), $args ) );
				wp_redirect( esc_url_raw( get_edit_post_link( $post_id, false ) ) );
				exit;
			}
		}
	}

}
add_action( 'admin_init', 'newsletterglue_reset_pattern_action', 10 );

/**
 * Creates the admin menu links.
 */
function newsletterglue_get_screen_ids() {

	$screen_ids = array();
	$screen_id  = sanitize_title( __( 'Newsletters', 'newsletter-glue' ) );

	$post_types  = get_post_types();
	$unsupported = array( 'attachment', 'revision', 'nav_menu_item', 'custom_css', 'customize_changeset', 'oembed_cache', 'user_request', 'wp_block' );

	if ( is_array( $post_types ) ) {
		foreach ( $post_types as $post_type ) {
			if ( ! in_array( $post_type, apply_filters( 'newsletterglue_unsupported_post_types', $unsupported ) ) ) {
				$screen_ids[] = $post_type;
				$screen_ids[] = 'edit-' . $post_type;
			}
		}
	}

	$screen_ids[] = 'newsletter-glue';
	$screen_ids[] = $screen_id . '_page_ngl-settings';

	return apply_filters( 'newsletterglue_screen_ids', $screen_ids );
}

/**
 * Plugin action links.
 * 
 * @param array $links This is an array containing the links.
 */
function newsletterglue_plugin_action_links( $links ) {

	$links['settings'] = '<a href="' . admin_url( 'admin.php?page=ngl-settings' ) . '">' . esc_html__( 'Settings', 'newsletter-glue' ) . '</a>';

	return $links;

}
add_filter( 'plugin_action_links_' . plugin_basename( NGL_PLUGIN_FILE ), 'newsletterglue_plugin_action_links', 10, 1 );

/**
 * Add deactivate modal layout.
 */
function newsletterglue_deactivate_modal() {
	global $pagenow;

	if ( 'plugins.php' !== $pagenow ) {
		return;
	}

	require_once NGL_PLUGIN_DIR . 'includes/admin/deactivate.php';

}

/**
 * Send feedback regarding new connections.
 */
function newsletterglue_feedback_modal() {

	global $pagenow;

	if ( 'admin.php' !== $pagenow ) {
		return;
	}

	if ( ! isset( $_GET['page'] ) || 'ngl-settings' != $_GET['page'] ) { // phpcs:ignore
		return;
	}

	require_once NGL_PLUGIN_DIR . 'includes/admin/feedback.php';

}
add_action( 'admin_footer', 'newsletterglue_feedback_modal' );

/**
 * Show support bar modals.
 */
function newsletterglue_support_bar_modals() {

	global $pagenow;

	if ( ( isset( $_GET['page'] ) && strstr( $_GET['page'], 'ngl-' ) ) || ( isset( $_GET['post_type'] ) && 'newsletterglue' === $_GET['post_type'] ) || ( isset( $_GET['post_type'] ) && 'ngl_pattern' === $_GET['post_type'] ) ) { // phpcs:ignore
		if ( ! isset( $_GET['taxonomy'] ) ) { // phpcs:ignore
			require_once NGL_PLUGIN_DIR . 'includes/admin/bug-report.php';
		}
	}

}
add_action( 'admin_footer', 'newsletterglue_support_bar_modals' );

/**
 * Show template styles trigger.
 */
function newsletterglue_trigger_template_styles() {
	global $post_type;
	
	if ( $post_type && in_array( $post_type, array( 'ngl_template' ) ) ) {
		?>
		<div id="ngl-template-styles"></div>
		<?php
	}
}
add_action( 'admin_footer', 'newsletterglue_trigger_template_styles' );

/**
 * Setting: Heading.
 * 
 * @param string $heading This is the setting header.
 * @param string $desc This is the description.
 */
function newsletterglue_setting_heading( $heading, $desc = '' ) {
	if ( strstr( $heading, 'Font size' ) ) {
		$mob_heading = __( 'Font size', 'newsletter-glue' );
	} else {
		$mob_heading = $heading;
	}
	?>
	<h2 class="ngl-desktop">
		<?php echo esc_html( $heading ); ?>
		<?php if ( $desc ) { ?>
		<span><?php echo wp_kses_post( $desc ); ?></span>
		<?php } ?>
	</h2>

	<h2 class="ngl-mobile">
		<?php echo esc_html( $mob_heading ); ?>
		<?php if ( $desc ) { ?>
		<span><?php echo wp_kses_post( $desc ); ?></span>
		<?php } ?>
	</h2>
	<?php
}

/**
 * Show save state.
 */
function newsletterglue_show_save_text() {
	?>
	<span class="ngl-process is-hidden is-waiting">
		<span class="ngl-process-icon"><svg class="ngl-infinite-spinner" stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg></span>
		<span class="ngl-process-text"><strong><?php _e( 'Saving...', 'newsletter-glue' ); ?></strong></span>
	</span>
	<span class="ngl-process is-hidden is-valid">
		<span class="ngl-process-icon"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 1024 1024" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M512 64C264.6 64 64 264.6 64 512s200.6 448 448 448 448-200.6 448-448S759.4 64 512 64zm193.5 301.7l-210.6 292a31.8 31.8 0 0 1-51.7 0L318.5 484.9c-3.8-5.3 0-12.7 6.5-12.7h46.9c10.2 0 19.9 4.9 25.9 13.3l71.2 98.8 157.2-218c6-8.3 15.6-13.3 25.9-13.3H699c6.5 0 10.3 7.4 6.5 12.7z"></path></svg></span>
		<span class="ngl-process-text"><strong><?php _e( 'Saved', 'newsletter-glue' ); ?></strong></span>
	</span>
	<?php
}

/**
 * Setting: Dropdown.
 * 
 * @param string $id Option ID.
 * @param string $title Option title.
 * @param array  $options An array of options.
 * @param string $helper A helper text or description.
 * @param mixed  $option An option for this select.
 */
function newsletterglue_setting_dropdown( $id = '', $title = '', $options = array(), $helper = '', $option = null ) {
	$selected = newsletterglue_get_theme_option( $id );
	if ( strstr( $id, 'ngl_' ) ) {
		$selected = $option;
	}
	?>
	<div class="components-base-control ngl-desktop" data-option="<?php echo esc_attr( $id ); ?>">
		<div class="components-base-control__field">
			<div>
				<label class="components-base-control__label" for="ngl_theme_<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $title ); ?></label>
			</div>
			<div>
				<div class="ui selection dropdown ngl-theme-input">
					<input type="hidden" name="<?php echo esc_attr( $id ); ?>" id="<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $selected ); ?>">
					<div class="default text"><?php echo esc_html( $selected ); ?></div>
					<svg class="ngl-dropdown-arrow" stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
					<div class="menu">
						<?php foreach ( $options as $key => $value ) { ?>
						<div class="item" data-value="<?php echo esc_attr( $key ); ?>">
							<?php if ( 'ngl_position_logo' == $id ) { ?>
								<img class="ui avatar image" src="<?php echo esc_url( NGL_PLUGIN_URL ) . 'assets/images/' . esc_attr( $key ) . '.png'; ?>" style="width:12px;height:12px;margin-top:0;">
							<?php } ?>
							<?php echo esc_html( $value ); ?>
						</div>
						<?php } ?>
					</div>
				</div>

				<?php if ( $helper ) { ?>
				<p id="" class="components-base-control__help"><?php echo newsletterglue_kses_post( $helper ); // phpcs:ignore ?></p>
				<?php } ?>
			</div>
			<div>
				<?php newsletterglue_show_save_text(); ?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Setting: Font colour and size.
 * 
 * @param string $id This is the option ID.
 * @param string $title This is the option title.
 */
function newsletterglue_setting_colour_size( $id = '', $title = '' ) {
	$options = array(
		'left'      => __( 'Align left', 'newsletter-glue' ),
		'center'    => __( 'Align center', 'newsletter-glue' ),
		'right'     => __( 'Align right', 'newsletter-glue' ),
	);

	$selected = newsletterglue_get_theme_option( $id . '_align' );

	?>
	<div class="components-base-control" data-option="<?php echo esc_attr( $id ); ?>_align">
		<div class="components-base-control__field">
			<div>
				<label class="components-base-control__label" for="ngl_theme_<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $title ); ?></label>
			</div>
			<div class="ngl-theme-color ngl-desktop">
				<input type="text" class="ngl-theme-input ngl-color-field" value='<?php echo esc_attr( newsletterglue_get_theme_option( $id . '_colour' ) ); ?>' data-option="<?php echo esc_attr( $id ); ?>_colour" />
			</div>
			<div>
				<input class="components-font-size-picker__number ngl-theme-input ngl-desktop" type="number" min="1" value="<?php echo esc_attr( newsletterglue_get_theme_option( $id . '_size' ) ); ?>" data-option="<?php echo esc_attr( $id ); ?>_size" >
				<input class="components-font-size-picker__number ngl-theme-input ngl-mobile" type="number" min="1" value="<?php echo esc_attr( newsletterglue_get_theme_option( 'mobile_' . $id . '_size' ) ); ?>" data-option="mobile_<?php echo esc_attr( $id ); ?>_size" >
			</div>
			<div class="ngl-alignment-container">
				<div class="ui selection dropdown ngl-theme-input">
					<input type="hidden" name="<?php echo esc_attr( $id ); ?>_align" id="<?php echo esc_attr( $id ); ?>_align" value="<?php echo esc_attr( $selected ); ?>">
					<div class="default text"><?php echo esc_html( $selected ); ?></div>
					<svg class="ngl-dropdown-arrow" stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
					<div class="menu">
						<?php foreach ( $options as $key => $value ) { ?>
						<div class="item" data-value="<?php echo esc_attr( $key ); ?>">
							<img class="ui avatar image" src="<?php echo esc_url( NGL_PLUGIN_URL ) . 'assets/images/' . esc_attr( $key ) . '.png'; ?>" style="width:12px;height:12px;margin-top:0;">
							<?php echo esc_html( $value ); ?>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
			<div>
				<?php newsletterglue_show_save_text(); ?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Setting: Colour.
 * 
 * @param string $id Option ID.
 * @param string $title Option title.
 */
function newsletterglue_setting_colour( $id = '', $title = '' ) {
	?>
	<div class="components-base-control ngl-desktop">
		<div class="components-base-control__field">
			<div>
				<label class="components-base-control__label" for="ngl_theme_<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $title ); ?></label>
			</div>
			<div class="ngl-theme-color">
				<input type="text" class="ngl-theme-input ngl-color-field" value='<?php echo esc_attr( newsletterglue_get_theme_option( $id ) ); ?>' data-option="<?php echo esc_attr( $id ); ?>" />
			</div>
			<div>
				<?php newsletterglue_show_save_text(); ?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Setting: Text input.
 * 
 * @param string $id Option ID.
 * @param string $title Option title.
 */
function newsletterglue_setting_text( $id = '', $title = '' ) {
	$class = 'ngl-' . str_replace( '_', '-', $id );
	?>
	<div class="components-base-control <?php echo esc_attr( $class ); ?>">
		<div class="components-base-control__field">
			<div>
				<label class="components-base-control__label" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $title ); ?></label>
			</div>
			<div class="">
				<input class="components-text-control__input ngl-theme-input ngl-desktop" id="<?php echo esc_attr( $id ); ?>" type="text" value="<?php echo esc_attr( get_option( $id ) ); ?>" data-option="<?php echo esc_attr( $id ); ?>" >
			</div>
			<div>
				<?php newsletterglue_show_save_text(); ?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Setting: Size.
 * 
 * @param string  $id Option ID.
 * @param string  $title Option title.
 * @param integer $max If a maximum number for the size is allowed.
 */
function newsletterglue_setting_size( $id = '', $title = '', $max = 999 ) {
	$class = 'ngl-' . str_replace( '_', '-', $id );

	$label = false;

	if ( 'container_padding1' == $id || 'container_margin1' == $id ) {
		$label = '<div style="margin: 0 0 1px;font-size:12px;">' . __( 'Top', 'newsletter-glue' ) . '</div>';
	}
	?>
	<div class="components-base-control <?php echo esc_attr( $class ); ?>">
		<div class="components-base-control__field">
			<div>
				<label class="components-base-control__label" for="ngl_theme_<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $title ); ?></label>
			</div>
			<div class="ngl-theme-px">
				<?php echo wp_kses_post( $label ); ?>
				<input class="components-font-size-picker__number ngl-theme-input ngl-desktop" id="ngl_theme_<?php echo esc_attr( $id ); ?>" type="number" min="0" max="<?php echo esc_attr( $max ); ?>" value="<?php echo (int) newsletterglue_get_theme_option( $id ); ?>" data-option="<?php echo esc_attr( $id ); ?>" >
				<input class="components-font-size-picker__number ngl-theme-input ngl-mobile" id="ngl_theme_<?php echo esc_attr( $id ); ?>_mobile" type="number" min="0" max="<?php echo esc_attr( $max ); ?>" value="<?php echo (int) newsletterglue_get_theme_option( 'mobile_' . $id ); ?>" data-option="mobile_<?php echo esc_attr( $id ); ?>" >
				<span class="ngl-px <?php echo $label ? 'ngl-px-with-label' : ''; ?>">px</span>
			</div>

			<?php
			if ( 'container_padding1' == $id ) {
				$id = 'container_padding2';
				?>
			<div class="ngl-theme-px">
				<?php echo '<div style="margin: 0 0 1px;font-size:12px;">' . esc_html__( 'Bottom', 'newsletter-glue' ) . '</div>'; ?>
				<input class="components-font-size-picker__number ngl-theme-input ngl-desktop" id="ngl_theme_<?php echo esc_attr( $id ); ?>" type="number" min="0" max="<?php echo esc_attr( $max ); ?>" value="<?php echo (int) newsletterglue_get_theme_option( $id ); ?>" data-option="<?php echo esc_attr( $id ); ?>" >
				<input class="components-font-size-picker__number ngl-theme-input ngl-mobile" id="ngl_theme_<?php echo esc_attr( $id ); ?>_mobile" type="number" min="0" max="<?php echo esc_attr( $max ); ?>" value="<?php echo (int) newsletterglue_get_theme_option( 'mobile_' . $id ); ?>" data-option="mobile_<?php echo esc_attr( $id ); ?>" >
				<span class="ngl-px <?php echo $label ? 'ngl-px-with-label' : ''; ?>">px</span>
			</div>
			<?php } ?>

			<?php
			if ( 'container_margin1' == $id ) {
				$id = 'container_margin2';
				?>
			<div class="ngl-theme-px">
				<?php echo '<div style="margin: 0 0 1px;font-size:12px;">' . esc_html__( 'Bottom', 'newsletter-glue' ) . '</div>'; ?>
				<input class="components-font-size-picker__number ngl-theme-input ngl-desktop" id="ngl_theme_<?php echo esc_attr( $id ); ?>" type="number" min="0" max="<?php echo esc_attr( $max ); ?>" value="<?php echo (int) newsletterglue_get_theme_option( $id ); ?>" data-option="<?php echo esc_attr( $id ); ?>" >
				<input class="components-font-size-picker__number ngl-theme-input ngl-mobile" id="ngl_theme_<?php echo esc_attr( $id ); ?>_mobile" type="number" min="0" max="<?php echo esc_attr( $max ); ?>" value="<?php echo (int) newsletterglue_get_theme_option( 'mobile_' . $id ); ?>" data-option="mobile_<?php echo esc_attr( $id ); ?>" >
				<span class="ngl-px <?php echo $label ? 'ngl-px-with-label' : ''; ?>">px</span>
			</div>
			<?php } ?>

			<div>
				<?php newsletterglue_show_save_text(); ?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Add a setting checkbox.
 * 
 * @param string  $id This is the option ID.
 * @param string  $title This is the option title.
 * @param string  $text This is the checkbox text.
 * @param string  $option Checkbox option.
 * @param boolean $not_boolean Whether we expect a boolean value from this checkbox.
 */
function newsletterglue_setting_checkbox( $id, $title, $text, $option = null, $not_boolean = false ) {

	if ( $not_boolean ) {
		$value = 'yes';
	} else {
		$value = 1;
	}
	?>
	<div class="components-base-control ngl-desktop">
		<div class="components-base-control__field">
			<div>
				<label class="components-base-control__label"><?php echo esc_html( $title ); ?></label>
			</div>
			<div class="ngl-theme-checkbox">
				<div class="ngl-theme-checkbox-state"><?php newsletterglue_show_save_text(); ?></div>
				<div class="ngl-theme-checkbox-input"><input type="checkbox" name="<?php echo esc_attr( $id ); ?>" id="<?php echo esc_attr( $id ); ?>" value="1" class="ngl-theme-input" <?php checked( $value, $option ); ?> data-option="<?php echo esc_attr( $id ); ?>" /></div>
				<div class="ngl-theme-checkbox-text"><?php echo wp_kses_post( $text ); ?></div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Adds one or more classes to the body tag in the dashboard.
 *
 * @param  String $classes Current body classes.
 * @return String          Altered body classes.
 */
function newsletterglue_admin_body_class( $classes ) {
	global $pagenow, $plugin_page, $post_type;

	if ( ! empty( $post_type ) ) {
		if ( strstr( $post_type, 'newsletterglue' ) || strstr( $post_type, 'ngl_' ) ) {
			$classes = "$classes newsletterglue-ui";
			return $classes;
		}
	}

	if ( ! empty( $plugin_page ) && strstr( $plugin_page, 'ngl-' ) ) {
		$classes = "$classes ngl-admin-ui";
		if ( strstr( $plugin_page, '-wizard' ) ) {
			$classes = "$classes ngl-setup-wizard";
		}
		return $classes;
	}

    return $classes;
}

add_filter( 'admin_body_class', 'newsletterglue_admin_body_class' );

/**
 * Add an upload button.
 * 
 * @param string $id This is the option ID.
 * @param string $title This is the option title.
 */
function newsletterglue_setting_upload( $id, $title ) {
	?>
	<div class="components-base-control ngl-desktop">
		<div class="components-base-control__field">
			<div>
				<label class="components-base-control__label"><?php echo esc_html( $title ); ?></label>
			</div>
			<div class="ngl-theme-upload" data-id="<?php echo esc_attr( $id ); ?>">
				<span class="ngl-theme-upload-button"><a href="#" class="ui button primary"><?php _e( 'Select image', 'newsletter-glue' ); ?></a></span>
				<span class="ngl-theme-upload-name">
					<?php
					if ( get_option( $id ) ) {
						$url = esc_attr( get_option( $id ) );
						$baseurl = basename( $url );
						echo '<a href="#" target="_blank" class="ngl-image-trigger">' . esc_html( $baseurl ) . '</a><a href="' . esc_url( $url ) . '" target="_blank" class="ngl-image-icon ngl-link-inline-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a><a href="#" class="ngl-image-remove">' . esc_html__( 'remove', 'newsletter-glue' ) . '</a>';
					} else {
						_e( 'No image selected', 'newsletter-glue' );
					}
					?>
				</span>
				<input type="hidden" name="<?php echo esc_attr( $id ); ?>" id="<?php echo esc_attr( $id ); ?>" value="<?php echo esc_url( get_option( $id ) ); ?>" />
				<input type="hidden" name="<?php echo esc_attr( $id ); ?>_id" id="<?php echo esc_attr( $id ); ?>_id" value="<?php echo absint( get_option( $id . '_id' ) ); ?>" />
			</div>
		</div>
	</div>
	<?php
}
