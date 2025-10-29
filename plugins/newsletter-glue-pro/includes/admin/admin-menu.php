<?php
/**
 * Admin Menu.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Creates the admin menu links.
 */
function newsletterglue_add_admin_menu() {
	global $menu;

	$ngicon = base64_encode(
		'<svg id="Group_76" data-name="Group 76" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 196.005 124.099">
			<g id="Group_47" data-name="Group 47" transform="translate(0 0)">
				<path id="Path_29" data-name="Path 29" d="M71.7,79.352l2.14-1.03a78.958,78.958,0,0,1-8.774-36.987q0-15.659,7.762-26.593C78,7.452,82.26,2.787,93.206.622s21.368,1.726,23.4,5.46S109,10.179,102.9,18.744s-6.309,9.32-9.7,18.068c-2.223,7.105-3.172,9.842-4.083,19.248-.428,9.314.821,14.809,2.941,24.421,2.335,9.378,4.273,13.454,7.153,17.684a42.2,42.2,0,0,1-10.934,5.4,46.312,46.312,0,0,1-12.891,1.485A30.365,30.365,0,0,1,50.553,92.7,51.629,51.629,0,0,1,43.6,78.861q-4.995-14.444-6.074-36.717h-2.16q-6.209,28.078-6.209,39.282t4.05,17.684q-8.1,6.074-16.874,6.074T3.78,100.122Q0,95.06,0,85c0-6.7,7.9-52.117,8.5-57.033S1.35,13.527,1.35,13.527q15.119-9.719,27-9.719T46.3,7.925a21.635,21.635,0,0,1,8.5,12.081,102.88,102.88,0,0,1,3.577,16.806q1.147,8.842,4.05,21.261C64.367,66.352,68.189,72.692,71.7,79.352Z" transform="translate(0 0)" fill="#9ca2a7"/>
			</g>
			<g id="Group_46" data-name="Group 46" transform="matrix(0.999, -0.035, 0.035, 0.999, 101.15, 3.897)">
				<path id="Path_29-2" data-name="Path 29" d="M61.825,71.949,54.94,57.775q14.444-9.584,26.053-9.584,9.719,0,9.719,10.934,0,3.1-3.712,18.966t-3.712,24.231q0,8.369,3.645,12.419a26.362,26.362,0,0,1-15.794,5.535q-8.234,0-11.677-5.4T56.02,99.082a52.938,52.938,0,0,1-15.119,2.16q-18.359,0-29.63-11.339T0,56.425A50.071,50.071,0,0,1,4.05,37.189,64.331,64.331,0,0,1,15.524,19.033a55.835,55.835,0,0,1,19.371-13.7A59.994,59.994,0,0,1,59.6,0Q72.354,0,79.576,4.725A14.839,14.839,0,0,1,86.8,17.819q0,8.369-5.467,13.026A18.713,18.713,0,0,1,68.844,35.5a26.049,26.049,0,0,1-12.621-3.037,23.158,23.158,0,0,1-8.977-8.707q-7.289,3.51-12.554,13.769A46.41,46.41,0,0,0,29.428,58.99q0,11.2,4.387,16.806a13.44,13.44,0,0,0,11,5.6Q54.4,81.4,61.825,71.949Z" transform="translate(0 0)" fill="#9ca2a7"/>
			</g>
		</svg>'
	);

	if ( current_user_can( 'edit_newsletterglue' ) ) {
		$permission = 'edit_newsletterglue';
	} else if ( current_user_can( 'add_newsletterglue' ) ) {
		$permission = 'add_newsletterglue';
	} else {
		$permission = 'manage_newsletterglue';
	}

	$admin_page			= add_menu_page( __( 'Newsletters', 'newsletter-glue' ), __( 'Newsletters', 'newsletter-glue' ), $permission, 'newsletter-glue', null, 'data:image/svg+xml;base64,' . $ngicon, '25.5471' );

	$issues 			= add_submenu_page( 'newsletter-glue', __( 'Emails', 'newsletter-glue' ), __( 'Emails', 'newsletter-glue' ), 'edit_newsletterglue', 'edit.php?post_type=newsletterglue' );
	$new_template_page 	= add_submenu_page( 'newsletter-glue', __( 'Templates & Styles', 'newsletter-glue' ), __( 'Templates & Styles', 'newsletter-glue' ), 'manage_newsletterglue_patterns', 'edit.php?post_type=ngl_template' );
	$settings_page 		= add_submenu_page( 'newsletter-glue', __( 'Settings', 'newsletter-glue' ), __( 'Settings', 'newsletter-glue' ), 'manage_newsletterglue', 'ngl-settings', 'newsletterglue_settings_page' );

	$theme_page 		= add_submenu_page( '__newsletterglue', __( 'Global styles', 'newsletter-glue' ), __( 'Global styles', 'newsletter-glue' ), 'manage_newsletterglue_patterns', 'ngl-theme', 'newsletterglue_theme_page' );

	// Add a hidden menu item for onboarding.
	add_submenu_page( 
        '_newsletterglue',
        __( 'Set up Newsletter Glue', 'newlsetter-glue' ),
        __( 'Set up Newsletter Glue', 'newlsetter-glue' ),
        'manage_newsletterglue',
        'ngl-welcome-wizard',
        'newsletterglue_welcome_wizard'
    );

	// Add a hidden menu item for template wizard.
	add_submenu_page( 
        '_newsletterglue',
        __( 'Customize patterns wizard', 'newlsetter-glue' ),
        __( 'Customize patterns wizard', 'newlsetter-glue' ),
        'manage_newsletterglue',
        'ngl-template-wizard',
        'newsletterglue_template_wizard'
    );

}
add_action( 'admin_menu', 'newsletterglue_add_admin_menu', 10 );

/**
 * Custom admin titles.
 */
function newsletterglue_admin_title( $admin_title, $title ) {
	if ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] === 'ngl-template-wizard' ) { // phpcs:ignore
		return __( 'Customize patterns wizard', 'newlsetter-glue' );
	}

	if ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] === 'ngl-welcome-wizard' ) { // phpcs:ignore
		return __( 'Set up Newsletter Glue', 'newlsetter-glue' );
	}

	return $admin_title;
}
add_action( 'admin_title', 'newsletterglue_admin_title', 100, 2 );

/**
 * Onboarding wizard output.
 */
function newsletterglue_welcome_wizard() {
	require_once NGL_PLUGIN_DIR . 'includes/admin/views/topbar2.php';
	?>
	<div id="nglue-welcome-wizard" class="nglue-setup-wizard"></div>
	<?php
}

/**
 * Template wizard output.
 */
function newsletterglue_template_wizard() {
	require_once NGL_PLUGIN_DIR . 'includes/admin/views/topbar2.php';
	?>
	<div id="nglue-template-wizard" class="nglue-setup-wizard"></div>
	<?php
}

/**
 * Custom menu order.
 */
function newsletterglue_custom_menu_order( $enabled ) {
	return $enabled || current_user_can( 'manage_newsletterglue' );
}
add_filter( 'custom_menu_order', 'newsletterglue_custom_menu_order' );

/**
 * Removes the parent menu item.
 */
function newsletterglue_menu_order_fix() {

	global $submenu;

	if ( isset( $submenu ) && is_array( $submenu ) ) {
		foreach( $submenu as $key => $array ) {
			if ( $key === 'newsletter-glue' ) {
				foreach( $array as $index => $value ) {
					if ( isset( $value[2] ) && $value[2] === 'newsletter-glue' ) {
						unset( $submenu[ 'newsletter-glue' ][ $index ] );
					}
				}
			}
		}
	}

}
add_action( 'admin_menu', 'newsletterglue_menu_order_fix', 1000 );
add_action( 'admin_menu_editor-menu_replaced', 'newsletterglue_menu_order_fix', 1000 );

/**
 * Admin head CSS.
 */
function newsletterglue_admin_head_css() {
	if ( ( isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] == 'newsletterglue' && ! current_user_can( 'add_newsletterglue' ) ) || ( isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] == 'ngl_pattern' && ! current_user_can( 'manage_newsletterglue_patterns' ) ) ) { // phpcs:ignore
	?>
	<style>
	.page-title-action {
		display: none;
	}
	</style>
	<?php
	}
}
add_action( 'admin_head', 'newsletterglue_admin_head_css', 1000 );

/**
 * Prevent admin areas for specific roles.
 */
function newsletterglue_prevent_role_access() {
	global $pagenow;

	if ( $pagenow === 'post-new.php' ) {
		if ( isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] === 'newsletterglue' ) { // phpcs:ignore
			if ( ! current_user_can( 'add_newsletterglue' ) ) {
				wp_die( esc_html__( 'You are not allowed to create new newsletters.', 'newsletter-glue' ) );
			}
		}
	}

	if ( isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] === 'ngl_pattern' ) { // phpcs:ignore
		if ( ! current_user_can( 'manage_newsletterglue_patterns' ) ) {
			wp_die( esc_html__( 'You are not allowed to access newsletter patterns.', 'newsletter-glue' ) );
		}
	}

}
add_action( 'admin_init', 'newsletterglue_prevent_role_access' );

/**
 * Theme designer.
 */
function newsletterglue_theme_page() {

	require_once NGL_PLUGIN_DIR . 'includes/admin/views/topbar.php';

	$tab1_active = '';
	$tab2_active = '';
	$tab3_active = '';

	if ( isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] === 'ngl_pattern' ) { // phpcs:ignore
		$tab1_active = 'nglue-active';
	}

	if ( isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] === 'ngl_template' ) { // phpcs:ignore
		$tab3_active = 'nglue-active';
	}

	if ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'ngl-theme' ) { // phpcs:ignore
		$tab2_active = 'nglue-active';
	}
	?>

	<div class="nglue-main">
		<div class="components-panel__body nglue-tabs is-opened">
			<div class="components-panel__row">
				<ul>
					<li><a class="components-button is-link <?php echo esc_attr( $tab3_active ); ?>" href="<?php echo esc_url( admin_url( 'edit.php?post_type=ngl_template' ) ); ?>"><?php _e( 'Templates', 'newsletter-glue' ); ?></a></li>
					<li><a class="components-button is-link <?php echo esc_attr( $tab1_active ); ?>" href="<?php echo esc_url( admin_url( 'edit.php?post_type=ngl_pattern' ) ); ?>"><?php _e( 'Patterns', 'newsletter-glue' ); ?></a></li>
					<li><a class="components-button is-link <?php echo esc_attr( $tab2_active ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=ngl-theme' ) ); ?>"><?php _e( 'Global styles', 'newsletter-glue' ); ?></a></li>
				</ul>
			</div>
		</div>
	</div>

	<?php if ( empty( get_option( 'newsletterglue_pro_license' ) ) ) { ?>
<div class="nglue-main"><div class="components-panel__body nglue-alert is-opened"><div class="nglue-alert-icon"><svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg></div><div class="nglue-alert-info"><div class="nglue-alert-title">Add/update your license key</div><div class="nglue-alert-body">Your license key is invalid, expired or missing. Head to Pro license tab to fix.</div><div class="nglue-alert-body">Something not right? <a href="https://newsletterglue.com/contact/" target="_blank">Get help</a>.</div></div><div class="nglue-alert-actions"><a href="<?php echo esc_url( admin_url( 'admin.php?page=ngl-settings#/pro' ) ); ?>" class="components-button is-primary">Go to Pro license tab</a><div class="nglue-alert-link"><a class="components-external-link" href="https://newsletterglue.com/account" target="_blank" rel="external noopener noreferrer">Get license key from My Account<span data-wp-c16t="true" data-wp-component="VisuallyHidden" class="components-visually-hidden css-0 em57xhy0" style="border: 0px; clip: rect(1px, 1px, 1px, 1px); clip-path: inset(50%); height: 1px; margin: -1px; overflow: hidden; padding: 0px; position: absolute; width: 1px; overflow-wrap: normal;">(opens in a new tab)</span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="components-external-link__icon css-bqq7t3 etxm6pv0" role="img" aria-hidden="true" focusable="false"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a></div></div></div></div>
	<?php
	}
	?>
	<div id="ngl-global-styles"></div>
	<?php
}

/**
 * Manually highlight theme patterns.
 */
add_filter( 'parent_file', 'newsletterglue_menu_highlight_items', 999 );
function newsletterglue_menu_highlight_items() {
	global $plugin_page, $submenu_file, $parent_file;

	if ( 'ngl-theme' == $plugin_page ) {
		$plugin_page = 'edit.php?post_type=ngl_template'; // phpcs:ignore
	}

	if ( 'ngl-support' == $plugin_page ) {
		$parent_file = 'edit.php?post_type=newsletterglue'; // phpcs:ignore
		$plugin_page = 'edit.php?post_type=newsletterglue'; // phpcs:ignore
	}

	if ( in_array( $submenu_file, array( 'edit.php?post_type=ngl_template', 'post-new.php?post_type=ngl_template' ) ) ) {
		$parent_file = 'edit.php?post_type=ngl_template'; // phpcs:ignore
		$plugin_page = 'edit.php?post_type=ngl_template'; // phpcs:ignore
	}

	if ( in_array( $submenu_file, array( 'edit.php?post_type=ngl_pattern', 'post-new.php?post_type=ngl_pattern' ) ) ) {
		$parent_file = 'edit.php?post_type=ngl_template'; // phpcs:ignore
		$plugin_page = 'edit.php?post_type=ngl_template'; // phpcs:ignore
	}

	if ( $submenu_file === 'edit.php?post_type=ngl_automation' ) {
		$parent_file = 'edit.php?post_type=newsletterglue'; // phpcs:ignore
		$plugin_page = 'edit.php?post_type=newsletterglue'; // phpcs:ignore
	}

	if ( $submenu_file === 'edit.php?post_type=ngl_log' ) {
		$parent_file = 'edit.php?post_type=newsletterglue'; // phpcs:ignore
		$plugin_page = 'edit.php?post_type=newsletterglue'; // phpcs:ignore
	}

	if ( in_array( $submenu_file, array( 'edit.php?post_type=newsletterglue', 'post-new.php?post_type=newsletterglue' ) ) ) {
		$parent_file = 'edit.php?post_type=newsletterglue'; // phpcs:ignore
		$plugin_page = 'edit.php?post_type=newsletterglue'; // phpcs:ignore
	}

	return $parent_file;
}

/**
 * Manually highlight theme patterns.
 */
add_filter( 'submenu_file', 'newsletterglue_menu_highlight_item' );
function newsletterglue_menu_highlight_item($submenu_file) {

    if ( $submenu_file === 'edit.php?post_type=ngl_template' ) {
        return 'edit.php?post_type=ngl_template';
    }

    if ( $submenu_file === 'edit.php?post_type=ngl_pattern' ) {
        return 'edit.php?post_type=ngl_template';
    }

    if ( $submenu_file === 'edit.php?post_type=ngl_automation' ) {
        return 'edit.php?post_type=newsletterglue';
    }

    if ( $submenu_file === 'edit.php?post_type=ngl_log' ) {
        return 'edit.php?post_type=newsletterglue';
    }

	if ( in_array( $submenu_file, array( 'post-new.php?post_type=newsletterglue' ) ) ) {
		return 'edit.php?post_type=newsletterglue';
	}

	if ( in_array( $submenu_file, array( 'edit.php?post_type=ngl_template', 'post-new.php?post_type=ngl_template' ) ) ) {
		return 'edit.php?post_type=ngl_template';
	}

	if ( in_array( $submenu_file, array( 'edit.php?post_type=ngl_pattern', 'post-new.php?post_type=ngl_pattern' ) ) ) {
		return 'edit.php?post_type=ngl_template';
	}

    // Don't change anything
    return $submenu_file;
}

/**
 * Admin page view.
 */
function newsletterglue_settings_page() {

	require_once NGL_PLUGIN_DIR . 'includes/admin/views/topbar.php';

	echo '<div id="nglue-settings"></div>';
}
