<?php
/**
 * Admin Scripts.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get theme defaults.
 */
function newsletterglue_get_theme_options_defaults( $post_id = 0, $mobile = false ) {

	$theme = array(
		'h1_colour'          => '#333333',
		'h2_colour'          => '#333333',
		'h3_colour'          => '#333333',
		'h4_colour'          => '#333333',
		'h5_colour'          => '#333333',
		'h6_colour'          => '#333333',
		'p_colour'           => '#666666',
		'h1_size'            => 32,
		'h2_size'            => 28,
		'h3_size'            => 24,
		'h4_size'            => 22,
		'h5_size'            => 20,
		'h6_size'            => 18,
		'p_size'             => 16,
		'h1_align'           => 'left',
		'h2_align'           => 'left',
		'h3_align'           => 'left',
		'h4_align'           => 'left',
		'h5_align'           => 'left',
		'h6_align'           => 'left',
		'p_align'            => 'left',
		'email_bg'           => '#f9f9f9',
		'container_bg'       => '#ffffff',
		'accent'             => '#0088A0',
		'a_colour'           => '#0088A0',
		'btn_bg'             => '#0088A0',
		'btn_colour'         => '#ffffff',
		'btn_radius'         => 0,
		'btn_border'         => '#0088A0',
		'btn_width'          => 150,
		'container_padding1' => 0,
		'container_padding2' => 0,
		'container_margin1'  => 10,
		'container_margin2'  => 0,
		'max_logo_w'         => 0,
	);

	if ( $mobile ) {
		$theme = array(
			'mobile_h1_size'            => 28,
			'mobile_h2_size'            => 24,
			'mobile_h3_size'            => 22,
			'mobile_h4_size'            => 20,
			'mobile_h5_size'            => 18,
			'mobile_h6_size'            => 16,
			'mobile_p_size'             => 16,
			'mobile_container_padding1' => 0,
			'mobile_container_padding2' => 0,
			'mobile_container_margin1'  => 10,
			'mobile_container_margin2'  => 0,
			'mobile_btn_width'          => 150,
			'mobile_max_logo_w'         => 0,
		);
	}

	return apply_filters( 'newsletterglue_get_theme_options_defaults', $theme, $mobile );
}

/**
 * Enqueues the required admin scripts.
 */
function newsletterglue_load_admin_scripts( $hook ) {
	global $wp_scripts, $post_type, $post, $ngl_post_id, $pagenow;

	$app = newsletterglue_default_connection();

	$post_id = isset( $post->ID ) ? $post->ID : 0;

	$ngl_post_id = $post_id;

	$screen    = get_current_screen();
	$screen_id = $screen ? $screen->id : '';

	$js_dir  = NGL_PLUGIN_URL . 'assets/js/';
	$css_dir = NGL_PLUGIN_URL . 'assets/css/';

	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	// Beta.
	$asset_file = include NGL_PLUGIN_DIR . 'build/index.asset.php';

	wp_register_script( 'nglue-backend', NGL_PLUGIN_URL . 'build/index.js', $asset_file['dependencies'], $asset_file['version'], true );
	wp_enqueue_script( 'nglue-backend' );

	$first_name = get_user_meta( get_current_user_id(), 'first_name', true );
	$edit_demo  = get_edit_post_link( absint( get_option( 'newsletterglue_demo_post' ) ) );

	$quickstyle = absint( newsletterglue_get_theme_option( 'quickstyle' ) );

	$current_offset = get_option( 'gmt_offset' );
	$tzstring       = get_option( 'timezone_string' );

	$check_zone_info = true;

	// Remove old Etc mappings. Fallback to gmt_offset.
	if ( false !== strpos( $tzstring, 'Etc/GMT' ) ) {
		$tzstring = '';
	}

	if ( empty( $tzstring ) ) { // Create a UTC+- zone if no timezone string exists.
		$check_zone_info = false;
		if ( 0 == $current_offset ) {
			$tzstring = 'UTC+0';
		} elseif ( $current_offset < 0 ) {
			$tzstring = 'UTC' . $current_offset;
		} else {
			$tzstring = 'UTC+' . $current_offset;
		}
	}

	$timezone_format = _x( 'H:i:s', 'timezone date format' );

	$custom_css = get_option( 'newsletterglue_css' );
	if ( $post_id ) {
		$post_css = get_post_meta( $post_id, '_newsletterglue_css', true );
		if ( $post_css && ! is_array( $post_css ) ) {
			$custom_css = $post_css;
		}
	}

	// Main args.
	$args = array(
		'user_id'              => get_current_user_id(),
		'api_version'          => newsletterglue()->api_version(),
		'api_key'              => get_user_meta( get_current_user_id(), 'newsletterglue_api_key', true ),
		'template_page'        => admin_url( 'edit.php?post_type=ngl_pattern' ),
		'first_name'           => $first_name ? $first_name : __( 'friend', 'newsletter-glue' ),
		'demo_post'            => $edit_demo ? $edit_demo : admin_url( 'edit.php?post_type=newsletterglue' ),
		'skip_onboarding'      => admin_url( 'edit.php?post_type=newsletterglue&skip_onboarding=true' ),
		'esp_list'             => newsletterglue_get_esp_list(),
		'license_tier'         => newsletterglue_get_tier(),
		'newsroom_upg'         => 'https://newsletterglue.com/newsroom-pricing-contact/',
		'upgrade_link'         => 'https://newsletterglue.com/account/',
		'no_license'           => empty( get_option( 'newsletterglue_pro_license' ) ),
		'images_uri'           => NGL_PLUGIN_URL . 'assets/images/ui/',
		'share_uri'            => NGL_PLUGIN_URL . 'assets/images/share',
		'custom_css'           => wp_kses_post( wp_strip_all_tags( $custom_css ) ),
		'disable_css'          => absint( get_option( 'newsletterglue_disable_plugin_css' ) ),
		'wizard_uri'           => esc_url( admin_url( 'admin.php?page=ngl-welcome-wizard' ) ),
		'home_url'             => untrailingslashit( home_url() ),
		'is_demo'              => ! empty( get_option( 'newsletterglue_pro_demo' ) ),
		'theme_r'              => newsletterglue_get_theme_options( $post_id ),
		'theme_m'              => newsletterglue_get_theme_options( $post_id, true ),
		'theme_rd'             => newsletterglue_get_theme_options_defaults( $post_id ),
		'theme_md'             => newsletterglue_get_theme_options_defaults( $post_id, true ),
		'quickstyle'           => $quickstyle,
		'global_styles_link'   => admin_url( 'admin.php?page=ngl-theme' ),
		'template_styles_link' => admin_url( 'edit.php?post_type=ngl_template' ),
		'additional_settings'  => admin_url( 'admin.php?page=ngl-settings#/additional' ),
		'users_url'            => admin_url( 'users.php' ),
		'logo_url'             => esc_url( NGL_PLUGIN_URL ) . 'assets/images/top-bar-logo.svg',
		'wp_general'           => esc_url( admin_url( 'options-general.php' ) ),
		'wp_time'              => date_i18n( $timezone_format ) . ' (' . $tzstring . ')',
		'css_moved'            => sprintf( __( 'Custom CSS has been moved to global styles. %s.', 'newsletter-glue' ), '<a href="' . admin_url( 'admin.php?page=ngl-theme' ) . '">' . __( 'Access it here', 'newsletter-glue' ) . '</a>' ),
	);

	// Theme.json
	if ( file_exists( get_template_directory() . '/theme.json' ) ) {
		$request = @file_get_contents( get_template_directory() . '/theme.json' );
	} else {
		$request = null;
	}
	if ( $request ) {
		$data                = json_decode( $request, true );
		$args['themeColors'] = $data['settings']['color']['palette'];
	} else {
		$args['themeColors'] = '';
	}

	// Get quick style colors.
	$themes      = newsletterglue_get_quick_styles();
	$colorsArray = array( 'heading', 'bg', 'content', 'p', 'button' );
	foreach ( $themes as $index => $theme ) {
		$selectedcolors = array();
		foreach ( $colorsArray as $element ) {
			if ( ! isset( $theme['ignore'] ) || ! in_array( $element, $theme['ignore'] ) ) {
				$selectedcolors[] = array(
					'name'  => '',
					'color' => $theme[ $element ],
				);
			}
		}
		$args['ngColors'][ $index ] = $selectedcolors;
	}

	// Get esp options.
	$esps = newsletterglue_get_supported_apps();
	foreach ( $esps as $key => $value ) {
		$args['esp_icons'][ $key ] = NGL_PLUGIN_URL . 'assets/images/iconset/' . $key . '.png';
	}

	// Block data.
	$blocks = newsletterglue_get_blocks();
	foreach ( $blocks as $block_id => $params ) {
		$classname = ucfirst( str_replace( 'newsletterglue_block_', 'NGL_Block_', $block_id ) );
		if ( ! class_exists( $classname ) ) {
			continue;
		}
		$block                       = new $classname();
		$args['blocks'][ $block_id ] = array(
			'id'          => esc_attr( $block_id ),
			'label'       => esc_html( $block->get_label() ),
			'description' => esc_html( $block->get_description() ),
			'icon'        => $block->get_icon_svg(),
			'url'         => $block->get_demo_url(),
			'in_use'      => $block->use_block() === 'yes' ? 1 : 0,
		);
	}

	// License data.
	$code = get_option( 'newsletterglue_pro_license', '' );
	if ( $code ) {
		$expiry = get_option( 'newsletterglue_pro_license_expires' );
		if ( $expiry === 'lifetime' ) {
			$expires = __( 'Never expires', 'newsletter-glue' );
		} else {
			$expires = wp_date( 'F j, Y', strtotime( $expiry ) );
		}
	}

	$license = array(
		'license_code'    => $code,
		'license_status'  => $code ? 1 : '',
		'license_test'    => $code ? true : false,
		'license_on'      => $code ? true : false,
		'license_expires' => $code ? $expires : '',
		'license_name'    => $code ? sprintf( __( '%s license', 'newsletter-glue' ), newsletterglue_get_tier_name() ) : '',
	);

	foreach ( $license as $key => $value ) {
		$args[ $key ] = $value;
	}

	// Post types
	$post_types        = newsletterglue_get_post_types();
	$selectedposttypes = get_option( 'newsletterglue_post_types' );
	$selectedtypes     = explode( ',', $selectedposttypes );
	$types             = array();
	foreach ( $post_types as $key => $value ) {
		$types[] = array(
			'value' => $key,
			'label' => $value,
		);
		if ( in_array( $key, $selectedtypes ) ) {
			$args['selectedPostTypes'][] = array(
				'value' => $key,
				'label' => $value,
			);
		}
	}
	$args['post_types'] = $types;

	// Roles.
	$roles    = newsletterglue_get_editable_roles();
	$js_roles = array();
	foreach ( $roles as $role_name => $role_label ) {
		$js_roles[] = array(
			'value' => $role_name,
			'label' => $role_label,
		);
		if ( $role_name == 'editor' ) {
			$args['selectedRole'] = array(
				'value' => $role_name,
				'label' => $role_label,
			);
		}
	}
	$args['js_roles']    = $js_roles;
	$args['permissions'] = newsletterglue_get_permissions_array( $roles );

	// Ad Inserter
	$ad_inserter_integrations = newsletterglue_get_registered_ad_integrations();
	
	$active_ad_inserter_integrations = array();
	foreach( $ad_inserter_integrations as $integration_id => $integration_object ) {
		// Extract the name from the integration object using the get_name method
		$integration_name = $integration_object->get_name();
		$active_ad_inserter_integrations[] = array(
			'value' => $integration_id,
			'label' => $integration_name,
		);
	}
	
	$args['ad_inserter_integration'] = $active_ad_inserter_integrations;
	
	// Broadstreet integration data
	$args['broadstreet_access_token'] = get_option('ngl_broadstreet_access_token', '');
	$args['broadstreet_has_connection'] = get_option('ngl_broadstreet_has_connection', false);

	// Get active integration and format it properly
	$active_integration = newsletterglue_get_active_ad_integration();
	if ($active_integration) {
		$args['ad_inserter_active_integration'] = array(
			'id' => $active_integration->get_id(),
			'name' => $active_integration->get_name()
		);
	} else {
		$args['ad_inserter_active_integration'] = null;
	}

	// Get ad inserter placeholder image
	$args['ad_inserter_placeholder_image'] = newsletterglue_get_ad_inserter_placeholder_image();

	// Get advanced ads available
	$args['advanced_ads_available'] = get_option('ngl_advanced-ads_available', false);

	// Get broadstreet available
	$args['broadstreet_available'] = get_option('ngl_broadstreet_available', false);

	// Get broadstreet access token
	$args['broadstreet_access_token'] = get_option('ngl_broadstreet_access_token', '');

	// Misc.
	$args['ngSlug']         = get_option( 'newsletterglue_post_type_ep', 'newsletter' );
	$args['ngDomain']       = get_option( 'newsletterglue_home_url', '' );
	$args['removeCSSFront'] = get_option( 'newsletterglue_disable_front_css', false );

	// Email fonts.
	$fonts              = newsletterglue_get_email_fonts();
	$args['font_names'] = array_merge(
		$fonts,
		array(
			'default' => 'Arial',
			'inherit' => 'Arial',
		)
	);
	foreach ( $fonts as $font_key => $font_value ) {
		if ( $font_key == 'inherit' ) {
			$font_value = __( 'Default', 'newsletter-glue' );
		}
		$args['email_fonts'][] = array(
			'label' => $font_value,
			'value' => $font_key,
		);
	}

	$use_blocks = get_option( 'newsletterglue_use_blocks' );
	if ( $use_blocks ) {
		$args['use_blocks'] = $use_blocks;
	}

	$taxonomies = get_taxonomies();
	$taxes      = array();
	foreach ( $taxonomies as $tax => $name ) {
		$taxes[] = $tax;
	}
	$args['tax_types'] = $taxes;

	$saved_types = get_option( 'newsletterglue_post_types' );
	if ( ! empty( $saved_types ) ) {
		$post_types = explode( ',', $saved_types );
	} else {
		$post_types = apply_filters( 'newsletterglue_supported_core_types', array() );
	}
	$post_types = array_merge( $post_types, array( 'newsletterglue', 'ngl_pattern', 'ngl_template', 'ngl_automation' ) );

	if ( ! empty( $post_type ) && is_array( $post_type ) ) {
		$post_type = $post_type[0];
	}
	if ( is_object( $post_type ) ) {
		$post_type = $post_type->name;
	}

	if ( ! empty( $post_type ) ) {
		$allow_blocks = strstr( $post_type, 'newsletterglue' ) || strstr( $post_type, 'ngl_' );
		$screen       = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! empty( $screen ) ) {
			if ( empty( $screen->base ) || $screen->base !== 'post' ) {
				$allow_blocks = false;
			}
		}
		if ( ! empty( $post_type ) ) {
			$allow_blocks = true;
			if ( ! in_array( $post_type, $post_types ) ) {
				$allow_blocks = false;
			}
			if ( $pagenow === 'edit.php' ) {
				$allow_blocks = false;
			}
		}
		$args['is_allowed_post_type'] = $allow_blocks;
		$args['core_post_type']       = ! strstr( $post_type, 'newsletterglue' ) && ! strstr( $post_type, 'ngl_' ) ? 'yes' : 'no';
	} else {
		$args['core_post_typpe'] = 'yes';
	}

	$args['is_super_admin'] = current_user_can( 'manage_options' );

	wp_localize_script( 'nglue-backend', 'nglue_backend', apply_filters( 'nglue_backend_args', $args ) );

	wp_enqueue_media();

	wp_register_style( 'nglue-backend', NGL_PLUGIN_URL . 'build/index.css', array( 'wp-components' ), NGL_VERSION );
	wp_enqueue_style( 'nglue-backend' );
	// End beta.

	// Register scripts.
	wp_register_script( 'newsletterglue_semantic', $js_dir . 'semantic/semantic' . $suffix . '.js', array( 'jquery' ), NGL_VERSION, true );
	wp_register_script( 'newsletterglue_flatpickr', $js_dir . 'flatpickr/flatpickr' . $suffix . '.js', array( 'jquery' ), NGL_VERSION, true );
	wp_register_script( 'newsletterglue_admin', $js_dir . 'admin/admin' . $suffix . '.js', array( 'jquery' ), NGL_VERSION, true );
	wp_register_script( 'newsletterglue_global', $js_dir . 'admin/global' . $suffix . '.js', array( 'jquery' ), NGL_VERSION, true );
	wp_register_script( 'jquery-spectrum', $js_dir . 'spectrum/spectrum' . $suffix . '.js', array( 'jquery' ), NGL_VERSION, true );
	wp_register_script( 'newsletterglue_theme', $js_dir . 'admin/theme' . $suffix . '.js', array( 'jquery', 'jquery-spectrum' ), NGL_VERSION, true );
	wp_register_script( 'newsletterglue_gutenberg', $js_dir . 'admin/gutenberg' . $suffix . '.js', array( 'jquery' ), NGL_VERSION, true );
	wp_register_script( 'newsletterglue_meta', $js_dir . 'admin/meta' . $suffix . '.js', array( 'jquery' ), NGL_VERSION, true );

	// Sitewide JS.
	wp_enqueue_script( 'newsletterglue_global' );
	wp_enqueue_script( 'newsletterglue_meta' );

	// Register styles.
	wp_register_style( 'newsletterglue_admin_menu_styles', $css_dir . 'menu' . $suffix . '.css', array(), NGL_VERSION );
	wp_register_style( 'newsletterglue_semantic', $css_dir . 'semantic' . $suffix . '.css', array(), NGL_VERSION );
	wp_register_style( 'newsletterglue_admin_styles', $css_dir . 'admin' . $suffix . '.css', array(), NGL_VERSION );
	wp_register_style( 'newsletterglue_cpt', $css_dir . 'cpt' . $suffix . '.css', array(), NGL_VERSION );

	// Sitewide menu CSS.
	wp_enqueue_style( 'newsletterglue_admin_menu_styles' );

	// Post-editor css.
	if ( ! empty( $_GET[ 'page' ] ) && $_GET[ 'page' ] === 'ngl-theme' ) { // phpcs:ignore
		wp_enqueue_style( 'wp-edit-post' );
		wp_enqueue_style( 'wp-edit-site' );
	}

	// Post-editor css.
	if ( ! empty( $post_type ) && in_array( $post_type, array( 'ngl_template', 'ngl_pattern' ) ) ) {
		if ( $pagenow !== 'edit.php' ) {
			wp_enqueue_style( 'wp-edit-post' );
			wp_enqueue_style( 'wp-edit-site' );
		}
	}

	// Admin assets for plugin pages only.
	if ( in_array( $screen_id, newsletterglue_get_screen_ids() ) || strstr( $screen_id, 'ngl-' ) ) {
		if ( ! empty( $screen_id ) && ! in_array( $screen_id, array( 'igmap' ) ) ) {
			wp_enqueue_script( 'newsletterglue_semantic' );
		}
		wp_enqueue_script( 'newsletterglue_flatpickr' );
		wp_enqueue_script( 'newsletterglue_admin' );
	
	// Register and enqueue the dropdown fix script
	wp_register_script( 'newsletterglue_dropdown_fix', $js_dir . 'admin/dropdown-fix.js', array( 'jquery', 'newsletterglue_admin', 'newsletterglue_semantic' ), NGL_VERSION, true );
	wp_enqueue_script( 'newsletterglue_dropdown_fix' );
	
	wp_enqueue_script( 'newsletterglue_gutenberg' );

		$draft = 'immediately';
		if ( isset( $post ) && $post->ID ) {
			$meta = get_post_meta( $post->ID, '_newsletterglue', true );
			if ( isset( $meta ) && ! empty( $meta['schedule'] ) ) {
				if ( $meta['schedule'] === 'draft' ) {
					$draft = 'draft';
				}
				if ( $meta['schedule'] === 'immediately' ) {
					$draft = 'immediately';
				}
			} else {
				$draft = newsletterglue_get_option( 'schedule', 'global' );
			}
		}

		$send_text = ( $draft === 'draft' ) ? sprintf( __( 'Save as draft in %s', 'newsletter-glue' ), newsletterglue_get_name( newsletterglue_default_connection() ) ) : __( 'Send as newsletter', 'newsletter-glue' );

		wp_localize_script(
			'newsletterglue_admin',
			'newsletterglue_params',
			apply_filters(
				'newsletterglue_admin_js_params',
				array(
					'ajaxurl'            => newsletterglue_get_ajax_url(),
					'ajaxnonce'          => wp_create_nonce( 'newsletterglue-ajax-nonce' ),
					'publish_error'      => __( 'Your newsletter is missing important details. <a href="#">Let&rsquo;s fix that.</a>', 'newsletter-glue' ),
					'saving'             => '<svg class="ngl-infinite-spinner" stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>' . __( 'Saving...', 'newsletter-glue' ),
					'saved'              => '<svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 1024 1024" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M512 64C264.6 64 64 264.6 64 512s200.6 448 448 448 448-200.6 448-448S759.4 64 512 64zm193.5 301.7l-210.6 292a31.8 31.8 0 0 1-51.7 0L318.5 484.9c-3.8-5.3 0-12.7 6.5-12.7h46.9c10.2 0 19.9 4.9 25.9 13.3l71.2 98.8 157.2-218c6-8.3 15.6-13.3 25.9-13.3H699c6.5 0 10.3 7.4 6.5 12.7z"></path></svg>' . __( 'Saved', 'newsletter-glue' ),
					'save'               => __( 'Save', 'newsletter-glue' ),
					'image_size'         => __( 'Ideal image width: 1200px', 'newsletter-glue' ),
					'no_featured_image'  => __( 'No featured image was selected.', 'newsletter-glue' ),
					'select_image'       => __( 'Select Image', 'newsletter-glue' ),
					'no_image_set'       => __( 'No image selected', 'newsletter-glue' ),
					'send_draft'         => newsletterglue_is_automation() ? __( 'Save as draft in Email campaigns', 'newsletter-glue' ) : sprintf( __( 'Save as draft in %s', 'newsletter-glue' ), newsletterglue_get_name( newsletterglue_default_connection() ) ),
					'send_now'           => __( 'Send as newsletter', 'newsletter-glue' ),
					'send_newsletter'    => newsletterglue_is_automation() ? '' : '<strong><span class="ngl-stateful-send-text"><a href="#newsletter_glue_metabox">' . $send_text . '</a></span></strong>',
					'no_posts_found'     => __( 'There&rsquo;s nothing here yet. Add your first post above.', 'newsletter-glue' ),
					'write_labels'       => __( 'Add label', 'newsletter-glue' ),
					'refreshing_html'    => __( 'Refreshing...', 'newsletter-glue' ),
					'refreshed_html'     => __( 'Refreshed!', 'newsletter-glue' ),
					'unknown_error'      => __( 'Unknown error occured.', 'newsletter-glue' ),
					'loader'             => '<span class="ngl-state-loader"><img src="' . NGL_PLUGIN_URL . 'assets/images/loading.gif" /><i>' . __( 'Working on your newsletter...', 'newsletter-glue' ) . '</span>',
					'loader2'            => '<span class="ngl-loader-automation ngl-state-loader"><img src="' . NGL_PLUGIN_URL . 'assets/images/loading.gif" /><i>' . __( 'Scheduling your newsletter...', 'newsletter-glue' ) . '</span>',
					'pattern_edit'       => '<div class="ngl-pattern-row" style="margin: 4px 0;color: #333;opacity: 1;display:flex;align-items:center;">' . __( 'You&rsquo;re about to edit a default pattern. Consider duplicating the default pattern instead?', 'newsletter-glue' ) . '<a href="#" class="ngl-pattern-bk" style="margin: 0 0 0 6px;text-decoration:underline !important;">' . __( 'Hide notification', 'newsletter-glue' ) . '</a></div>',
					'template_edit'      => '<div class="ngl-pattern-row" style="margin: 4px 0;color: #333;opacity: 1;display:flex;align-items:center;">' . __( 'You&rsquo;re about to edit a default template. Consider duplicating the default template instead?', 'newsletter-glue' ) . '<a href="#" class="ngl-pattern-bk" style="margin: 0 0 0 6px;text-decoration:underline !important;">' . __( 'Hide notification', 'newsletter-glue' ) . '</a></div>',
					'make_default_tpl'   => '<a href="#" class="ngl-tpl-make-default">' . __( 'Set active template', 'newsletter-glue' ) . '</a>',
					'default_tpl'        => '<a href="#" class="ngl-tpl-default">' . __( 'Active template', 'newsletter-glue' ) . '</a>',
					'no_shortcut'        => '<span class="ngl-tpl-noshortcut">' . __( 'Add to shortcuts', 'newsletter-glue' ) . '</span>',
					'add_shortcut'       => '<a href="#" class="ngl-tpl-shortcut">' . __( 'Add to shortcuts', 'newsletter-glue' ) . '</a>',
					'shortcut_added'     => '<a href="#" class="ngl-tpl-undo-shortcut">' . __( 'Shortcut added', 'newsletter-glue' ) . '</a>',
					'automation_paused'  => __( 'Run automation', 'newsletter-glue' ),
					'automation_p_help'  => __( '(when post is published/updated)', 'newsletter-glue' ),
					'automation_enabled' => __( 'Run automation', 'newsletter-glue' ),
					'automation_e_help'  => __( '(when post is published/updated)', 'newsletter-glue' ),
					'automation_run'     => __( 'Run automation', 'newsletter-glue' ),
					'automation_r_help'  => __( '(when post is published/updated)', 'newsletter-glue' ),
					'default_post_image' => NGL_PLUGIN_URL . 'assets/images/placeholder.png',
					'connect_url'        => esc_url( admin_url( 'admin.php?page=ngl-settings&tab=connect' ) ),
					'connect_esp'        => __( 'Start by connecting your email software &#x21C4;', 'newsletter-glue' ),
				)
			)
		);

		wp_enqueue_style( 'newsletterglue_semantic' );
		wp_enqueue_style( 'newsletterglue_admin_styles' );
	}

	// Add media scripts to settings page.
	if ( strstr( $screen_id, 'ngl-settings' ) || strstr( $screen_id, 'ngl-theme' ) ) {
		wp_enqueue_media();
		wp_enqueue_script( 'newsletterglue_theme' );
		wp_enqueue_style( 'wp-edit-blocks' );
	}

	// Add CPT stuff.
	if ( in_array( $screen_id, array( 'newsletterglue', 'ngl_pattern', 'edit-ngl_pattern', 'ngl_template', 'edit-ngl_template', 'ngl_automation', 'edit-ngl_automation' ) ) ) {
		wp_enqueue_style( 'newsletterglue_cpt' );
	}
}
add_action( 'admin_enqueue_scripts', 'newsletterglue_load_admin_scripts', 100 );

/**
 * Last post ID.
 */
function newsletterglue_last_postid() {
	global $wpdb;

	$result = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM $wpdb->posts ORDER BY ID DESC LIMIT 0,%d", 1 ) ); // phpcs:ignore
	$row    = $result[0];
	$id     = $row->ID;

	return $id;
}

/**
 * Add custom meta as JS.
 */
function newsletterglue_js_data() {
	global $post;

	if ( isset( $_GET[ 'postType' ] ) && $_GET[ 'postType' ] === 'wp_template' ) { // phpcs:ignore
		$thepost = get_post( newsletterglue_last_postid() );
	} elseif ( ! empty( $post ) ) {
			$thepost = $post;
	}

	if ( isset( $thepost->ID ) ) {

		$app  = newsletterglue_default_connection();
		$init = newsletterglue_get_path( $app ) . '/init.php';

		$data = array(
			'post_id'       => $thepost->ID,
			'post_perma'    => get_permalink( $thepost->ID ),
			'profile_pic'   => get_avatar_url( $thepost->post_author, 80 ),
			'author_name'   => get_the_author_meta( 'display_name', $thepost->post_author ),
			'author_bio'    => get_the_author_meta( 'description', $thepost->post_author ),
			'post_date'     => gmdate( 'l, j M Y', strtotime( $thepost->post_date ) ),
			'app'           => $app,
			'app_name'      => newsletterglue_get_name( $app ) ? sprintf( __( '%s integration', 'newsletter-glue' ), newsletterglue_get_name( $app ) ) : __( 'Email integration', 'newsletter-glue' ),
			'readtime'      => newsletterglue_content_estimated_reading_time( $thepost->post_content ),
			'locale'        => str_replace( '_', '-', get_locale() ),
			'reset_pattern' => add_query_arg( 'reset-pattern', 'true', get_edit_post_link( $thepost->ID, false ) ),
		);

		if ( $app && ! file_exists( $init ) ) {
			delete_option( 'newsletterglue_integrations' );
			$app = null;
		}

		$get_remote_lists = apply_filters( 'newsletterglue_get_remote_lists_in_footer', false );

		if ( $app && $get_remote_lists ) {
			include_once $init;
			$classname = 'NGL_' . ucfirst( $app );
			$api       = new $classname();
		}

		// Add lists.
		$the_lists = newsletterglue()::$the_lists;
		if ( $app && $get_remote_lists && file_exists( $init ) && empty( $the_lists ) ) {
			newsletterglue()::$the_lists = $api->_get_lists_compat();
			$the_lists                   = newsletterglue()::$the_lists;
		}

		if ( ! empty( $the_lists ) ) {
			$lists = array();
			if ( $app == 'mailerlite' ) {
				$lists[] = array(
					'label' => __( '― No group', 'newsletter-glue' ),
					'value' => '',
				);
			}
			if ( $app == 'brevo' ) {
				$lists[] = array(
					'label' => __( '― No list', 'newsletter-glue' ),
					'value' => '',
				);
			}
			foreach ( $the_lists as $key => $value ) {
				$lists[] = array(
					'value' => $key,
					'label' => $value,
				);
			}
			$data['the_lists'] = $lists;

			$extra_lists[] = array(
				'value' => '',
				'label' => '',
			);
			foreach ( $the_lists as $key => $value ) {
				$extra_lists[] = array(
					'value' => $key,
					'label' => $value,
				);
			}
			$data['extra_lists'] = $extra_lists;
		}

		// Post dates.
		$dates = array(
			gmdate( 'l, j M Y', strtotime( $thepost->post_date ) ),
			gmdate( 'F j, Y', strtotime( $thepost->post_date ) ),
			gmdate( 'd M Y', strtotime( $thepost->post_date ) ),
			gmdate( 'Y-m-d', strtotime( $thepost->post_date ) ),
			gmdate( 'm/d/Y', strtotime( $thepost->post_date ) ),
			gmdate( 'd/m/Y', strtotime( $thepost->post_date ) ),
		);

		$date_formats = array();
		foreach ( $dates as $date ) {
			$date_formats[] = array(
				'value' => $date,
				'label' => $date,
			);
		}
		$data['date_formats'] = $date_formats;

		if ( ! empty( $api ) ) {

			if ( false === ( $_tags = get_transient( $app . '_custom_tags' ) ) ) {
				$_tags = $api->get_custom_tags();
				if ( count( $_tags ) ) {
					set_transient( $app . '_custom_tags', $_tags, DAY_IN_SECONDS );
				} else {
					$_tags = array();
				}
			}

			if ( false === ( $_fields = get_transient( $app . '_custom_fields' ) ) ) {
				$_fields = $api->get_custom_fields();
				if ( count( $_fields ) ) {
					set_transient( $app . '_custom_fields', $_fields, DAY_IN_SECONDS );
				} else {
					$_fields = array();
				}
			}

			$data['custom_tags']   = $_tags;
			$data['custom_fields'] = $_fields;
		}

		wp_localize_script( 'newsletterglue_meta', 'newsletterglue_meta', $data );

	} else {
		wp_localize_script( 'newsletterglue_meta', 'newsletterglue_meta', array() );
	}

	// Newsletter and patterns.
	if ( isset( $thepost->post_type ) && in_array( $thepost->post_type, newsletterglue_get_active_cpts() ) ) {
		$app = newsletterglue_default_connection();
		if ( ! $app || ! file_exists( newsletterglue_get_path( $app ) . '/functions.php' ) ) {
			return;
		}
		include_once newsletterglue_get_path( $app ) . '/functions.php';
		$function = 'newsletterglue_get_' . strtolower( $app ) . '_tags';
		if ( ! function_exists( $function ) ) {
			return;
		}
		?>
		<div class="ngl-gutenberg-pop">
			<?php
				$tags = call_user_func( $function );
			foreach ( (array) $tags as $group_id => $group ) {
				echo '<div class="components-menu-group"><div role="group">';
				echo '<button type="button" role="menuitem" class="components-button components-menu-item__button ngl-submenu-trigger">
								<span class="components-menu-item__item"><strong>' . esc_html( $group['title'] ) . '</strong></span>
								<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="components-menu-items__item-icon" role="img" aria-hidden="true" focusable="false">
									<path d="M17.5 11.6L12 16l-5.5-4.4.9-1.2L12 14l4.5-3.6 1 1.2z"></path>
								</svg>
							</button>';
				foreach ( $group['tags'] as $id => $tag ) {
					$default_link_text = ! empty( $tag['default_link_text'] ) ? $tag['default_link_text'] : '';
					$fb_text           = isset( $tag['require_fallback'] ) ? __( 'Fallback required', 'newsletter-glue' ) . '<span style="color:#cc3000;">*</span>' : __( 'Fallback', 'newsletter-glue' );
					$fallback_label    = ! empty( $default_link_text ) ? __( 'Link text', 'newsletter-glue' ) : $fb_text;
					$show_helper       = ! empty( $default_link_text ) ? '' : sprintf( __( 'Show this if %s doesn&rsquo;t exist.', 'newsletter-glue' ), esc_html( strtolower( $tag['title'] ) ) );
					$helper            = isset( $tag['helper'] ) ? $tag['helper'] : $show_helper;
					$req_fallback      = isset( $tag['require_fallback'] );
					echo '<button type="button" role="menuitem" class="components-button components-menu-item__button ngl-submenu-item" data-default-link-text="' . esc_attr( $default_link_text ) . '" data-tag-id="' . esc_attr( $id ) . '" data-ngl-tag="{{ ' . esc_attr( $id ) . ' }}" data-require-fb="' . esc_attr( $req_fallback ) . '">
								<span class="components-menu-item__item">' . esc_html( $tag['title'] ) . '</span>';

					if ( ! isset( $tag['uneditable'] ) ) {
						echo '<span class="ngl-gutenberg-icon">
									<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 27.004 27.004" class="components-menu-items__item-icon" role="img" aria-hidden="true" focusable="false"><path d="M4.5,25.875V31.5h5.625l16.59-16.59L21.09,9.285ZM31.065,10.56a1.494,1.494,0,0,0,0-2.115l-3.51-3.51a1.494,1.494,0,0,0-2.115,0L22.695,7.68l5.625,5.625,2.745-2.745Z" transform="translate(-4.5 -4.496)"/></svg>
								</span>';
					}
					echo '</button>';
					if ( isset( $tag['uneditable'] ) && ! empty( $tag['helper'] ) ) {
						echo '<div class="ngl-outside-helper" >' . wp_kses_post( $tag['helper'] ) . '</div>';
					}
					if ( ! isset( $tag['uneditable'] ) ) {
						echo '<div class="ngl-fallback" data-tag="' . esc_attr( $id ) . '">
									<div class="ngl-fallback-title"><label for="__fallback_' . esc_attr( $id ) . '">' . wp_kses_post( $fallback_label ) . '</label></div>
									<div class="ngl-fallback-input"><input type="text" value="' . esc_attr( newsletterglue_get_merge_tag_fallback( $id ) ) . '" id="__fallback_' . esc_attr( $id ) . '" data-tag-input-id="' . esc_attr( $id ) . '" placeholder="' . esc_attr( $default_link_text ) . '" /></div>
									<div class="ngl-fallback-helper">' . wp_kses_post( $helper ) . '</div>
								</div>
							';
					}
				}
				echo '</div></div>';
			}
			?>
		</div>
		<?php
	}
}
add_action( 'admin_footer', 'newsletterglue_js_data', 99999 );
