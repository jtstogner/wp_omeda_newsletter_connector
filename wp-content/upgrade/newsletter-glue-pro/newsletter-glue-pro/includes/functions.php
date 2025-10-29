<?php

/**
 * Functions.
 */

// Exit if accessed directly
if (! defined('ABSPATH')) exit;

/**
 * Disable auto unsub link by default.
 */
function newsletterglue_auto_unsub_link_default($bool, $app)
{
	return false;
}
add_filter('newsletterglue_auto_unsub_link', 'newsletterglue_auto_unsub_link_default', 1, 2);

/**
 * Get props from style.
 */
function newsletterglue_get_props_from_style($style)
{
	$s = $style;
	$results = [];
	$styles = explode(';', $s);

	foreach ($styles as $style) {
		$properties = explode(':', $style);
		if (2 === count($properties)) {
			$results[trim($properties[0])] = trim($properties[1]);
		}
	}

	return $results;
}

/**
 * Get a prop from styles.
 */
function newsletterglue_get_style_prop($style, $prop)
{
	$s = $style;
	$results = [];
	$styles = explode(';', $s);

	foreach ($styles as $style) {
		$properties = explode(':', $style);
		if (2 === count($properties)) {
			$results[trim($properties[0])] = trim($properties[1]);
		}
	}

	if (! empty($results[$prop])) {
		return $results[$prop];
	}

	return null;
}

/**
 * Get theme options.
 */
function newsletterglue_get_theme_options($post_id = 0, $mobile = false)
{

	$keys = array(
		'h1_font',
		'h2_font',
		'h3_font',
		'h4_font',
		'h5_font',
		'h6_font',
		'p_font',
		'h1_size',
		'h1_colour',
		'h2_size',
		'h2_colour',
		'h3_size',
		'h3_colour',
		'h4_size',
		'h4_colour',
		'h5_size',
		'h5_colour',
		'h6_size',
		'h6_colour',
		'p_size',
		'p_colour',
		'a_colour',
		'btn_radius',
		'btn_colour',
		'btn_width',
		'btn_bg',
		'container_bg',
		'email_bg',
		'btn_border',
		'container_padding1',
		'container_padding2',
		'container_margin1',
		'container_margin2',
	);

	if ($post_id) {
		$post_styles = get_post_meta($post_id, '_newsletterglue_theme', true);
	}

	foreach ($keys as $key) {
		if ($mobile && in_array($key, array('h1_size', 'h2_size', 'h3_size', 'h4_size', 'h5_size', 'h6_size', 'p_size', 'btn_width', 'container_padding1', 'container_padding2', 'container_margin1', 'container_margin2'))) {
			$option_id = 'mobile_' . $key;
		} else {
			$option_id = $key;
		}
		$value = newsletterglue_get_theme_option($option_id);
		$theme[$key] = $value;
		if (! $value) {
			if (strstr($key, 'radius') || strstr($key, 'margin') || strstr($key, 'padding')) {
				$theme[$key] = 0;
			}
		}
		if (strstr($key, 'padding') || strstr($key, 'margin')) {
			if (! strstr($value, 'px')) {
				$value = absint($value) . 'px';
				$theme[$key] = $value;
			}
		}
		if (in_array($key, array('h1_size', 'h2_size', 'h3_size', 'h4_size', 'h5_size', 'h6_size', 'p_size'))) {
			$theme[$key . '_default'] = newsletterglue_get_theme_default($key);
			if (strstr($value, 'px')) {
				$theme[$key] = absint(str_replace('px', '', $value));
			}
			$theme[$key] = absint($value);
		}
	}

	if (empty($theme['font'])) {
		$theme['font'] = newsletterglue_get_theme_option('font') ? newsletterglue_get_theme_option('font') : '';
	}

	$fonts = newsletterglue_get_email_fonts();

	$fontkeys = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p');
	foreach ($fontkeys as $ss) {
		if (! empty($theme[$ss . '_font'])) {
			if ($theme[$ss . '_font'] == 'inherit') {
				$theme[$ss . '_font'] = 'helvetica';
			}
			foreach ($fonts as $key => $value) {
				if ($theme[$ss . '_font'] === $key) {
					$theme['defaultFont' . $ss] = array('key' => $key, 'name' => $value);
				}
			}
		} else {
			foreach ($fonts as $key => $value) {
				if ($key === $theme['font']) {
					$theme['defaultFont' . $ss] = array('key' => $key, 'name' => $value);
				}
			}
		}
	}

	$theme['styles'] = newsletterglue_get_quick_styles();

	if ($post_id) {
		if (is_array($post_styles)) {
			$theme = array_merge($theme, $post_styles);
		}
	}

	foreach ($fonts as $key => $value) {
		if (isset($theme['font'])) {
			if ($theme['font'] === $key) {
				$theme['defaultFont'] = array('key' => $key, 'name' => $value);
			}
		}
	}

	$theme['defaultFontWeight'] = array('key' => 'normal', 'name' => 'Regular');
	$theme['defaultFontWeightH'] = array('key' => 'normal', 'name' => 'Regular');
	$theme['defaultLineHeight'] = 1.6;
	$theme['defaultPadding'] = apply_filters('newsletterglue_default_block_padding', newsletterglue_get_default('p_padding'));
	$theme['defaultListPadding'] = array('top' => '0px', 'bottom' => '10px', 'left' => '0px', 'right' => '0px');
	$theme['buttonPadding'] = array('top' => '10px', 'bottom' => '10px', 'left' => '20px', 'right' => '20px');
	$theme['singleButtonPadding'] = array('top' => '8px', 'bottom' => '8px', 'left' => '20px', 'right' => '20px');
	$theme['defaultMainListPadding'] = array('top' => '15px', 'bottom' => '15px', 'left' => '40px', 'right' => '20px');
	$theme['defaultHeadingPadding'] = array('top' => '15px', 'bottom' => '0px', 'left' => '20px', 'right' => '20px');
	$theme['defaultSpacerHeight'] = '20px';
	$theme['defaultQuoteSize'] = '20px';

	$theme['h1_padding'] = newsletterglue_get_default('h1_padding');
	$theme['h2_padding'] = newsletterglue_get_default('h2_padding');
	$theme['h3_padding'] = newsletterglue_get_default('h3_padding');
	$theme['h4_padding'] = newsletterglue_get_default('h4_padding');
	$theme['h5_padding'] = newsletterglue_get_default('h5_padding');
	$theme['h6_padding'] = newsletterglue_get_default('h6_padding');

	$theme['quotePadding'] = array('top' => '20px', 'bottom' => '20px', 'left' => '20px', 'right' => '20px');

	$theme['defaultImagePadding'] = array('top' => '0px', 'bottom' => '0px', 'left' => '0px', 'right' => '0px');

	$theme['defaultContainerPadding'] = array('top' => '20px', 'bottom' => '20px', 'left' => '20px', 'right' => '20px');

	$theme['defaultInnerPadding'] = array('top' => '8px', 'bottom' => '0px', 'left' => '0px', 'right' => '0px');

	$theme['columnsPadding'] = array('top' => '10px', 'bottom' => '10px', 'left' => '0px', 'right' => '0px');
	$theme['mobilecolumnsPadding'] = array('top' => '10px', 'bottom' => '10px', 'left' => '0px', 'right' => '0px');
	$theme['columnPadding'] = array('top' => '10px', 'bottom' => '10px', 'left' => '0px', 'right' => '0px');
	$theme['mobilecolumnPadding'] = array('top' => '10px', 'bottom' => '10px', 'left' => '0px', 'right' => '0px');

	$theme['mobile_h1_padding'] = newsletterglue_get_default('h1_padding', true);
	$theme['mobile_h2_padding'] = newsletterglue_get_default('h2_padding', true);
	$theme['mobile_h3_padding'] = newsletterglue_get_default('h3_padding', true);
	$theme['mobile_h4_padding'] = newsletterglue_get_default('h4_padding', true);
	$theme['mobile_h5_padding'] = newsletterglue_get_default('h5_padding', true);
	$theme['mobile_h6_padding'] = newsletterglue_get_default('h6_padding', true);

	$theme['mobile_p_padding'] = newsletterglue_get_default('p_padding', true);

	$theme['mobile_list_padding'] = array('top' => '0px', 'bottom' => '5px', 'left' => '0px', 'right' => '0px');
	$theme['mobile_main_list_padding'] = array('top' => '8px', 'bottom' => '8px', 'left' => '0px', 'right' => '0px');

	$theme['MobileQuoteSize'] = '18px';
	$theme['MobileQuoteCiteSize'] = '15px';
	$theme['MobileQuotePadding'] = array('top' => '15px', 'bottom' => '15px', 'left' => '20px', 'right' => '20px');

	return apply_filters('newsletterglue_get_theme_options', $theme, $mobile);
}

/**
 * Read defaults.
 */
function newsletterglue_get_default($selector, $mobile = false, $as_string = false)
{

	if (! $mobile) {
		$array = array(
			'h1_padding'		=> array(
				'top'		=> '35px',
				'right'		=> '20px',
				'bottom' 	=> '15px',
				'left'		=> '20px',
			),
			'h2_padding'		=> array(
				'top'		=> '35px',
				'right'		=> '20px',
				'bottom' 	=> '15px',
				'left'		=> '20px',
			),
			'h3_padding'		=> array(
				'top'		=> '25px',
				'right'		=> '20px',
				'bottom' 	=> '0px',
				'left'		=> '20px',
			),
			'h4_padding'		=> array(
				'top'		=> '25px',
				'right'		=> '20px',
				'bottom' 	=> '0px',
				'left'		=> '20px',
			),
			'h5_padding'		=> array(
				'top'		=> '25px',
				'right'		=> '20px',
				'bottom' 	=> '0px',
				'left'		=> '20px',
			),
			'h6_padding'		=> array(
				'top'		=> '25px',
				'right'		=> '20px',
				'bottom' 	=> '0px',
				'left'		=> '20px',
			),
			'p_padding'			=> array(
				'top'		=> '8px',
				'right'		=> '20px',
				'bottom'	=> '10px',
				'left'		=> '20px',
			),
		);
	}

	if ($mobile) {
		$array = array(
			'h1_padding'		=> array(
				'top'		=> '5px',
				'right'		=> '20px',
				'bottom' 	=> '0px',
				'left'		=> '20px',
			),
			'h2_padding'		=> array(
				'top'		=> '35px',
				'right'		=> '20px',
				'bottom' 	=> '0px',
				'left'		=> '20px',
			),
			'h3_padding'		=> array(
				'top'		=> '15px',
				'right'		=> '20px',
				'bottom' 	=> '0px',
				'left'		=> '20px',
			),
			'h4_padding'		=> array(
				'top'		=> '15px',
				'right'		=> '20px',
				'bottom' 	=> '0px',
				'left'		=> '20px',
			),
			'h5_padding'		=> array(
				'top'		=> '15px',
				'right'		=> '20px',
				'bottom' 	=> '0px',
				'left'		=> '20px',
			),
			'h6_padding'		=> array(
				'top'		=> '15px',
				'right'		=> '20px',
				'bottom' 	=> '0px',
				'left'		=> '20px',
			),
			'p_padding'			=> array(
				'top'		=> '8px',
				'right'		=> '20px',
				'bottom'	=> '10px',
				'left'		=> '20px',
			),
		);
	}

	if ($as_string) {
		$return = $array[$selector];
		$return = implode(' ', array_values($return));
		$return = trim($return);
	} else {
		$return = $array[$selector];
	}

	return $return;
}

/**
 * Get quick styles.
 */
function newsletterglue_get_quick_styles()
{
	$styles = array();

	$styles[] = array(
		'id'		=> 1,
		'name'		=> 'Default',
		'heading' 	=> '#333333',
		'p' 		=> '#666666',
		'content'	=> '#ffffff',
		'bg'		=> '#f9f9f9',
		'button'	=> '#0088a0',
		'font_h'	=> 'Helvetica',
		'font_h_s'  => 'helvetica',
		'font_p'	=> 'Helvetica',
		'font_p_s'  => 'helvetica',
	);

	$styles[] = array(
		'id'		=> 2,
		'name'		=> 'Metal',
		'heading' 	=> '#1E1E1E',
		'p' 		=> '#454545',
		'content'	=> '#FFFFFF',
		'bg'		=> '#FFFFFF',
		'button'	=> '#7E7E7E',
		'font_h'	=> 'Arial',
		'font_h_s'  => 'arial',
		'font_p'	=> 'Arial',
		'font_p_s'  => 'arial',
		'ignore'	=> array('bg'),
	);

	$styles[] = array(
		'id'		=> 3,
		'name'		=> 'Earth',
		'heading' 	=> '#340707',
		'p' 		=> '#340707',
		'content'	=> '#FCF7F5',
		'bg'		=> '#FCF7F5',
		'button'	=> '#623100',
		'font_h'	=> 'Courier',
		'font_h_s'	=> 'courier',
		'font_p'	=> 'Arial',
		'font_p_s'  => 'arial',
		'ignore'	=> array('p', 'content'),
	);

	$styles[] = array(
		'id'		=> 4,
		'name'		=> 'Sea',
		'heading' 	=> '#000474',
		'p' 		=> '#1C1E45',
		'content'	=> '#FFFFFF',
		'bg'		=> '#F4F7FA',
		'button'	=> '#368BC9',
		'font_h'	=> 'Arial',
		'font_h_s'  => 'arial',
		'font_p'	=> 'Arial',
		'font_p_s'  => 'arial',
	);

	return apply_filters('newsletterglue_get_quick_styles', $styles);
}

/**
 * Get main custom post types.
 */
function newsletterglue_get_main_cpts()
{
	return apply_filters('newsletterglue_get_main_cpts', array('newsletterglue', 'ngl_pattern', 'ngl_template', 'ngl_automation', 'ngl_log'));
}

/**
 * Get style properties as an array.
 */
function newsletterglue_get_properties_from_html($html)
{
	$output = new simple_html_dom();
	$output->load($html, true, false);
	$replace = 'div.ng-block';
	foreach ($output->find($replace) as $key => $element) {
		$style = newsletterglue_get_properties($element->style);
	}
	return $style;
}

/**
 * Get style properties as an array.
 */
function newsletterglue_get_properties($s)
{
	$results  = array();
	$styles   = explode(';', $s);

	foreach ($styles as $style) {
		$properties = explode(':', $style);
		if (2 === count($properties)) {
			$results[trim($properties[0])] = trim($properties[1]);
		}
	}

	return $results;
}

/**
 * Extend the block attributes based on colors.
 */
function newsletterglue_get_extended_attrs($attrs, $html)
{

	$colors = get_option('newsletterglue_theme_colors');

	if (! empty($colors)) {
		foreach ($colors as $key => $color) {
			$slug   = $color->slug;
			$color  = $color->color;
			if (isset($attrs['backgroundColor']) && $slug === $attrs['backgroundColor']) {
				$attrs['backgroundColor'] = $color;
			}
			if (isset($attrs['textColor']) && $slug === $attrs['textColor']) {
				$attrs['textColor'] = $color;
			}
		}
	}

	return $attrs;
}

/**
 * Get col span.
 */
function newsletterglue_get_colspan($padding)
{
	$colspan = 1;

	if (! empty($padding) && ! empty($padding['left'])) {
		$colspan = $colspan + 1;
	}

	if (! empty($padding) && ! empty($padding['right'])) {
		$colspan = $colspan + 1;
	}

	return $colspan;
}

/**
 * Maybe show top space as empty td.
 */
function newsletterglue_maybe_show_top_space($padding)
{
	$colspan = newsletterglue_get_colspan($padding);
	if (! empty($padding) && ! empty($padding['top'])) {
		return '<tr><td height="' . absint($padding['top']) . '" colspan="' . absint($colspan) . '"></td></tr>';
	}
	return '';
}

/**
 * Maybe show bottom space as empty td.
 */
function newsletterglue_maybe_show_bottom_space($padding)
{
	$colspan = newsletterglue_get_colspan($padding);
	if (! empty($padding) && ! empty($padding['bottom'])) {
		return '<tr><td height="' . absint($padding['bottom']) . '" colspan="' . absint($colspan) . '"></td></tr>';
	}
	return '';
}

/**
 * Maybe show left space as empty td.
 */
function newsletterglue_maybe_show_left_space($padding)
{
	if (! empty($padding) && ! empty($padding['left'])) {
		return '<td width="' . absint($padding['left']) . '"></td>';
	}
	return '';
}

/**
 * Maybe show right space as empty td.
 */
function newsletterglue_maybe_show_right_space($padding)
{
	if (! empty($padding) && ! empty($padding['right'])) {
		return '<td width="' . absint($padding['right']) . '"></td>';
	}
	return '';
}

/**
 * Is activated.
 */
function newsletterglue_is_activated()
{
	return get_option('newsletterglue_pro_license', '');
}

/**
 * Allowed blocks list.
 */
function newsletterglue_allowed_block_list($blocks)
{

	foreach (glob(NGL_PLUGIN_DIR . 'src/blocks/*') as $block) {
		$block = basename($block);
		if ($block) {
			$blocks[] = "newsletterglue/{$block}";
		}
	}

	return $blocks;
}
add_filter('newsletterglue_allowed_block_list', 'newsletterglue_allowed_block_list', 50);

/**
 * Core newsletter cpts.
 */
function newsletterglue_get_core_cpts()
{
	return apply_filters('newsletterglue_get_core_cpts', array('ngl_template', 'ngl_pattern'));
}

/**
 * WBUR stuff only.
 */
function newsletterglue_post_embed_trim_words()
{

	if (strstr(home_url(), 'wbur')) {
		return false;
	}

	return true;
}
add_filter('newsletterglue_post_embed_trim_words', 'newsletterglue_post_embed_trim_words');

/**
 * Extend chargebee custom post types.
 */
function newsletterglue_chargebee_post_types($post_types)
{
	if (is_array($post_types)) {
		$post_types = array_merge(array('newsletterglue'), $post_types);
	}
	return $post_types;
}
add_filter('cbm_restrict_post_types', 'newsletterglue_chargebee_post_types');

/**
 * Checks whether the static site feature is enabled.
 */
function newsletterglue_static_site_feature_enabled()
{
	$tier = newsletterglue_get_tier();

	if (in_array($tier, array('newsroom', 'publisher'))) {
		return true;
	}

	return false;
}

/**
 * Exclude core patterns.
 */
function newsletterglue_unregister_core_patterns()
{
	$post_type = isset($_GET['post_type']) ? sanitize_text_field(wp_unslash($_GET['post_type'])) : null; // phpcs:ignore

	$is_edit = isset($_GET['post']) && isset($_GET['action']) ? absint($_GET['post']) : false; // phpcs:ignore

	if (in_array($post_type, array('newsletterglue', 'ngl_pattern', 'ngl_template'))) {
		remove_theme_support('core-block-patterns');
	}

	if ($is_edit) {
		$data = get_post($is_edit);
		if (! empty($data) && isset($data->post_type)) {
			if (in_array($data->post_type, array('newsletterglue', 'ngl_pattern', 'ngl_template'))) {
				remove_theme_support('core-block-patterns');
			}
		}
	}
}
add_action('after_setup_theme', 'newsletterglue_unregister_core_patterns');

/**
 * Get safe title.
 */
function ngl_safe_title($string)
{

	return htmlspecialchars_decode(wp_kses_post($string));
}

/**
 * Get allowed tags for excerpt.
 */
function newsletterglue_allowed_tags_for_excerpt()
{

	$allowed = array(
		'strong'	=> array(),
		'em'		=> array(),
		'b'			=> array(),
		'i'			=> array(),
		'span'		=> array(),
		'a'			=> array(
			'href' 	=> array(),
			'class'	=> array('ngl-article-read-more'),
		),
	);

	return apply_filters('newsletterglue_allowed_tags_for_excerpt', $allowed);
}

/**
 * Get editable roles.
 */
function newsletterglue_get_editable_roles()
{

	$roles = array();

	if (! function_exists('get_editable_roles')) {
		require_once ABSPATH . 'wp-admin/includes/user.php';
	}

	$wp_roles = get_editable_roles();

	foreach ($wp_roles as $role => $role_data) {
		$roles[$role] = $role_data['name'];
	}

	asort($roles);

	return apply_filters('newsletterglue_get_editable_roles', $roles);
}

/**
 * Get permissions array.
 */
function newsletterglue_get_permissions_array($roles)
{
	global $wp_roles;

	$perms = array();

	$tasks = array('manage_newsletterglue', 'publish_newsletterglue', 'add_newsletterglue', 'edit_newsletterglue', 'manage_newsletterglue_patterns');
	foreach ($tasks as $cap) {
		foreach ($roles as $role => $name) {
			$perms[$role][$cap] = 0;
		}
	}

	foreach ($roles as $role => $name) {
		$wp_role = get_role($role);
		foreach ($wp_roles->roles[$role]['capabilities'] as $capability => $allowed) {
			if (in_array($capability, $tasks)) {
				if ($allowed == 1) {
					$perms[$role][$capability] = 1;
				}
			}
		}
	}

	return $perms;
}

/**
 * Get active newsletter cpts.
 */
function newsletterglue_get_active_cpts()
{

	$saved_types = get_option('newsletterglue_post_types');

	if (! empty($saved_types)) {
		$cpts = explode(',', $saved_types);
	} else {
		$cpts = apply_filters('newsletterglue_supported_core_types', array());
	}

	$cpts = array_merge($cpts, array('newsletterglue', 'ngl_pattern', 'ngl_template'));

	return $cpts;
}

/**
 * Get registered ad integrations.
 */
function newsletterglue_get_registered_ad_integrations()
{

	$ad_manager = newsletterglue_get_ad_manager();

	$integrations = $ad_manager->get_integrations();

	return apply_filters('newsletterglue_get_registered_ad_integrations', $integrations);
}

/**
 * Get active ad integration.
 */
function newsletterglue_get_active_ad_integration()
{

	$ad_manager = newsletterglue_get_ad_manager();

	return apply_filters('newsletterglue_get_active_ad_integration', $ad_manager->get_active_integration());
}

/**
 * Recursive sanitation for an array
 */
function ngl_sanitize_text_field($string)
{
	if (is_array($string)) {
		foreach ($string as $key => &$value) {
			if (is_array($value)) {
				$value = ngl_sanitize_text_field($value);
			} else {
				$value = sanitize_text_field($value);
			}
		}
	} else {
		return sanitize_text_field($string);
	}
	return $string;
}

/**
 * Checks if we are in Gutenberg side.
 */
function is_newsletterglue_gutenberg()
{
	return (defined('REST_REQUEST') || is_admin()) && ! defined('NGL_IN_EMAIL');
}

/**
 * Check state of ESP connection + price tier.
 */
function newsletterglue_check_esp_status()
{

	$app = newsletterglue_default_connection();

	if (! empty($_GET['ng-debug-esp']) && current_user_can('manage_newsletterglue')) {
		die(print_r(get_option('newsletterglue_integrations')));
	}

	if ($app && newsletterglue_is_tier_locked($app)) {
		delete_option('newsletterglue_integrations');
	}
}
add_action('init', 'newsletterglue_check_esp_status');

/**
 * Main query.
 */
add_action('pre_get_posts', 'newsletterglue_main_query');
function newsletterglue_main_query($query)
{

	if (! is_admin() && is_tag() && $query->is_main_query()) {
		$types = get_taxonomy('post_tag')->object_type;
		$query->set('post_type', $types);
	}

	if (is_admin() && isset($_GET['post_type']) && isset($_GET['post_tag'])) { // phpcs:ignore
		$post_tag = sanitize_text_field(wp_unslash($_GET['post_tag'])); // phpcs:ignore
		$taxquery = array(
			array(
				'taxonomy' => 'post_tag',
				'field' => 'slug',
				'terms' => array($post_tag),
			)
		);

		$query->set('tax_query', $taxquery); // phpcs:ignore
	}
}

/**
 * Shortcode: Archive.
 */
function newsletterglue_archive($atts)
{
	ob_start();

	if (! empty($atts['sortby']) && $atts['sortby'] == 'latest') {
		$newsletters = get_posts(
			array(
				'posts_per_page' 	=> -1,
				'post_type' 		=> 'newsletterglue',
				'orderby'			=> 'date',
				'order'				=> 'desc',
				'post_status'		=> 'publish',
			)
		);
		if ($newsletters) {
?>
			<div class="newsletterglue-archive">
				<?php foreach ($newsletters as $newsletter) { ?>
					<p class="newsletterglue-archive-item"><a href="<?php echo esc_url(get_permalink($newsletter->ID)); ?>"><?php echo esc_html(get_the_title($newsletter->ID)); ?></a> &mdash; <?php echo get_the_date('M j, Y', $newsletter); ?></p>
				<?php } ?>
			</div>
		<?php
		}
		return ob_get_clean();
	}

	$terms = get_terms(array(
		'taxonomy'		=> 'ngl_newsletter_cat',
		'hide_empty'	=> true,
		'orderby'		=> 'term_id',
		'order'			=> 'asc'
	));

	if ($terms) {
		?>
		<div class="newsletterglue-archive">
			<?php foreach ($terms as $term) { ?>
				<div class="newsletterglue-archive--category" style="margin-bottom: 30px;">
					<h3 class="newsletterglue-archive--category-name"><a href="<?php echo esc_url(get_term_link($term)); // phpcs:ignore 
																				?>"><?php echo esc_html($term->name); ?></a></h3>
					<ul class="newsletterglue-archive--list">
						<?php
						$newsletters = get_posts(
							array(
								'posts_per_page' 	=> -1,
								'post_type' 		=> 'newsletterglue',
								'post_status'		=> 'publish',
								'tax_query' 		=> array( // phpcs:ignore
									array(
										'taxonomy' 	=> 'ngl_newsletter_cat',
										'field' 	=> 'term_id',
										'terms' 	=> $term->term_id,
									)
								)
							)
						);
						foreach ($newsletters as $newsletter) { ?>
							<li><a href="<?php echo esc_url(get_permalink($newsletter->ID)); ?>"><?php echo esc_html(get_the_title($newsletter->ID)); ?></a></li>
						<?php } ?>
					</ul>
				</div>
			<?php } ?>
		</div>
<?php
	}

	return ob_get_clean();
}
add_shortcode('newsletterglue_archive', 'newsletterglue_archive');

/**
 * Supported ESPs.
 */
function newsletterglue_get_supported_apps()
{

	$apps = array(
		'activecampaign'	=> __('ActiveCampaign', 'newsletter-glue'),
		'aweber'			=> __('AWeber', 'newsletter-glue'),
		'brevo'		        => __('Brevo', 'newsletter-glue'),
		'campaignmonitor'	=> __('Campaign Monitor', 'newsletter-glue'),
        'constantcontact'	=> __('Constant Contact', 'newsletter-glue'),
		'getresponse'		=> __('GetResponse', 'newsletter-glue'),
		'klaviyo'			=> __('Klaviyo', 'newsletter-glue'),
		'mailchimp'			=> __('Mailchimp', 'newsletter-glue'),
		'mailerlite'		=> __('MailerLite Classic', 'newsletter-glue'),
		'mailerlite_v2'		=> __('MailerLite', 'newsletter-glue'),
		'mailjet'			=> __('Mailjet', 'newsletter-glue'),
		'moosend'			=> __('Moosend', 'newsletter-glue'),
		'sailthru'			=> __('Sailthru', 'newsletter-glue'),
		'sendgrid'			=> __('SendGrid', 'newsletter-glue'),
		'sendy'				=> __('Sendy', 'newsletter-glue'),
		'salesforce'		=> __('Salesforce', 'newsletter-glue'),
	);

	return apply_filters('newsletterglue_get_supported_apps', $apps);
}

/**
 * Supported ESP select.
 */
function newsletterglue_get_supported_apps_select()
{
	$_esps = array();

	$esps = newsletterglue_get_supported_apps();

	foreach ($esps as $esp => $name) {
		if (newsletterglue_is_tier_locked($esp)) {
			$name = $name . '<span class="ngl-upgrade-dd"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 16 16" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><rect width="11" height="9" x="2.5" y="7" rx="2"></rect><path fill-rule="evenodd" d="M4.5 4a3.5 3.5 0 117 0v3h-1V4a2.5 2.5 0 00-5 0v3h-1V4z" clip-rule="evenodd"></path></svg>' . __('Upgrade', 'newsletter-glue') . '</span>';
		}
		$_esps[$esp] = $name;
	}

	return apply_filters('newsletterglue_get_supported_apps_select', $_esps);
}

/**
 * Get API list.
 */
function newsletterglue_get_esp_list()
{

	$tier = newsletterglue_get_tier();

	$list = array();

	$list[] = array(
		'value' 	=> 'activecampaign',
		'label'		=> 'ActiveCampaign',
		'bg'		=> '#356AE6',
		'help'		=> 'https://www.activecampaign.com/login',
		'extra_setting' => 'url',
		'requires'	=> empty($tier) || $tier === 'writer_new' ? 'tier-locked requires-publisher' : '',
	);

	$list[] = array(
		'value' 	=> 'aweber',
		'label'		=> 'AWeber',
		'bg'		=> '#246BE8',
		'help'		=> ngl_aweber_authorize_url(),
		'requires'	=> in_array($tier, array('newsroom', 'publisher', 'publisher_new', 'founding')) ? '' : 'tier-locked requires-publisher',
	);

	$list[] = array(
		'value' 	=> 'brevo',
		'label'		=> 'Brevo',
		'bg'		=> '#0b996e',
		'help'		=> 'https://app.brevo.com/settings/keys/api',
		'requires'	=> empty($tier) || $tier === 'writer_new' ? 'tier-locked requires-publisher' : '',
	);

	$list[] = array(
		'value' 	=> 'campaignmonitor',
		'label'		=> 'Campaign Monitor',
		'bg'		=> '#7856FF',
		'help'		=> 'https://help.campaignmonitor.com/api-keys',
		'requires'	=> empty($tier) || $tier === 'writer_new' ? 'tier-locked requires-publisher' : '',
		'extra_setting' => 'secret',
	);

    $list[] = array(
		'value' 	=> 'constantcontact',
		'label'		=> 'Constant Contact',
		'bg'		=> '#ffffff',
		'help'		=> ngl_constantcontact_authorize_url(),
	);

	$list[] = array(
		'value' 	=> 'getresponse',
		'label'		=> 'GetResponse',
		'bg'		=> '#00A1ED',
		'help'		=> 'https://app.getresponse.com/api',
		'requires'	=> empty($tier) || $tier === 'writer_new' ? 'tier-locked requires-publisher' : '',
	);

	$list[] = array(
		'value' 	=> 'klaviyo',
		'label'		=> 'Klaviyo',
		'bg'		=> '#FFF',
		'help'		=> 'https://www.klaviyo.com/account#api-keys-tab',
		'requires'	=> in_array($tier, array('newsroom', 'publisher_new', 'founding')) ? '' : 'tier-locked requires-publisher',
	);

	$list[] = array(
		'value' 	=> 'mailchimp',
		'label'		=> 'Mailchimp',
		'bg'		=> '#FFE01B',
		'help'		=> 'https://admin.mailchimp.com/account/api-key-popup/',
	);

	$list[] = array(
		'value' 	=> 'mailerlite_v2',
		'label'		=> 'MailerLite',
		'bg'		=> '#21C16C',
		'help'		=> 'https://dashboard.mailerlite.com/integrations/api',
	);

	$list[] = array(
		'value' 	=> 'mailerlite',
		'label'		=> 'MailerLite Classic',
		'bg'		=> '#21C16C',
		'help'		=> 'https://app.mailerlite.com/integrations/api/',
	);

	$list[] = array(
		'value' 	=> 'mailjet',
		'label'		=> 'Mailjet',
		'bg'		=> '#FFF',
		'help'		=> 'https://app.mailjet.com/account/api_keys',
		'extra_setting' => 'secret',
	);

	$list[] = array(
		'value' 	=> 'moosend',
		'label'		=> 'Moosend',
		'bg'		=> '#FFF',
		'help'		=> 'https://identity.moosend.com/login/',
	);

	$list[] = array(
		'value' 	=> 'sailthru',
		'label'		=> 'Sailthru',
		'bg'		=> '#FFF',
		'help'		=> 'https://my.sailthru.com/settings/api_postbacks',
		'extra_setting' => 'secret',
		'requires'	=> $tier && in_array($tier, array('newsroom')) ? '' : 'tier-locked requires-newsroom',
	);

	$list[] = array(
		'value' 	=> 'sendgrid',
		'label'		=> 'SendGrid',
		'bg'		=> '#FFF',
		'help'		=> 'https://app.sendgrid.com/settings/api_keys',
	);

	$list[] = array(
		'value' 	=> 'sendy',
		'label'		=> 'Sendy',
		'bg'		=> '#FFF',
		'extra_setting' => 'url',
	);
	$list[] = array(
		'value' 	=> 'salesforce',
		'label'		=> 'Salesforce Marketing Cloud',
		'bg'			=> '#FFF',
		'help'          => 'https://mc.login.exacttarget.com/hub-cas/login',
		'key_name'      => 'Client ID',
		'secret_name'   => 'Client Secret',
		'url_name'      => 'Auth Base URL',
		'extra_setting' => 'both',
	);

	return apply_filters('newsletterglue_get_esp_list', $list);
}

/**
 * Get app name (Service, or API name)
 */
function newsletterglue_get_name($app)
{

	$apps = newsletterglue_get_supported_apps();

	return isset($apps[$app]) ? $apps[$app] : '';
}

/**
 * Checks if app is integrated.
 */
function newsletterglue_inactive_app($app)
{

	$apps = get_option('newsletterglue_integrations');

	return ! isset($apps[$app]) ? true : false;
}

/**
 * Get the current page URL
 */
function newsletterglue_get_current_page_url()
{
	global $wp;

	if (get_option('permalink_structure')) {

		$base = trailingslashit(home_url($wp->request));
	} else {

		$base = add_query_arg($wp->query_string, '', trailingslashit(home_url($wp->request)));
		$base = remove_query_arg(array('post_type', 'name'), $base);
	}

	$scheme = is_ssl() ? 'https' : 'http';
	$uri    = set_url_scheme($base, $scheme);

	if (is_front_page()) {
		$uri = home_url('/');
	}

	$uri = apply_filters('newsletterglue_get_current_page_url', $uri);

	return $uri;
}

/**
 * Update the campaign result data.
 */
function newsletterglue_add_campaign_data($post_id, $subject = '', $result = '', $id = '')
{

	$results   = (array) get_post_meta($post_id, '_ngl_results', true);
	$time      = time();

	// Remove any scheduled events.
	if (isset($result['type']) && $result['type'] === 'schedule') {
		foreach ($results as $key => $data) {
			if (isset($data['type']) && $data['type'] === 'schedule') {
				unset($results[$key]);
			}
		}
	}

	if ($subject) {
		$result['subject'] = $subject;
	}

	if ($id) {
		$result['campaign_id'] = $id;
	}

	// Add the result to post meta.
	if ($result) {

		$results[$time] = $result;

		update_post_meta($post_id, '_ngl_results', $results);
		update_post_meta($post_id, '_ngl_last_result', $result);

		// Store this as notice.
		if (isset($result['type']) && $result['type'] === 'error') {

			$result['post_id'] = $post_id;
			$result['time']    = $time;

			// Check if the function exists before calling it
			if (function_exists('newsletterglue_add_notice')) {
				newsletterglue_add_notice($result);
			} else {
				// Fallback: store the notice in a transient that can be processed later
				$pending_notices = get_option('newsletterglue_pending_notices', array());
				$pending_notices[] = $result;
				update_option('newsletterglue_pending_notices', $pending_notices);
			}
		}
	}
}

/**
 * Get option.
 */
function newsletterglue_get_option($option_id = '', $app = 'global')
{

	$options = get_option('newsletterglue_options');

	if (isset($options[$app][$option_id])) {
		return stripslashes_deep($options[$app][$option_id]);
	}

	if (! isset($options[$app][$option_id])) {
		if ($option_id === 'schedule') {
			return 'immediately';
		}
	}

	return '';
}

/**
 * Get default from name.
 */
function newsletterglue_get_default_from_name()
{

	return apply_filters('newsletterglue_get_default_from_name', get_bloginfo('name'));
}

/**
 * Get application url.
 */
function newsletterglue_get_url($app)
{

	$path = NGL_PLUGIN_URL . 'includes/integrations/' . $app;

	// Allow this path to be modified using WordPress filters.
	return apply_filters('newsletterglue_get_url', $path, $app);
}

/**
 * Get application path.
 */
function newsletterglue_get_path($app)
{

	$path = NGL_PLUGIN_DIR . 'includes/integrations/' . $app;

	$file = $path . '/init.php';
	if (! file_exists($file)) {
		delete_option('newsletterglue_integrations');
		$app = null;
	}

	// Allow this path to be modified using WordPress filters.
	return apply_filters('newsletterglue_get_path', $path, $app);
}

/**
 * Get onboarding post.
 */
function newsletterglue_get_onboarding_post()
{
	ob_start();

	include_once(NGL_PLUGIN_DIR . 'includes/cpt/default-patterns.php');

	include_once('admin/views/welcome.php');

	return ob_get_clean();
}

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 */
function newsletterglue_sanitize($var)
{
	if (is_array($var)) {
		return array_map('newsletterglue_sanitize', $var);
	} else {
		return is_scalar($var) ? wp_kses_post($var) : $var;
	}
}

/**
 * Get a merge tag fallback.
 */
function newsletterglue_get_merge_tag_fallback($tag = '')
{

	$tags = get_option('newsletterglue_merge_tag_fallbacks');

	$value = isset($tags[$tag]) ? $tags[$tag] : '';

	return apply_filters('newsletterglue_get_merge_tag_fallback', $value, $tag);
}

/**
 * Remove unwanted stuff from the feed.
 */
function newsletterglue_content_feed($content)
{

	$content = str_replace('<!--[if !mso]><\!-->', '', $content);
	$content = str_replace('<!-- <![endif]-->', '', $content);

	$output = new simple_html_dom();
	$output->load($content, true, false);

	$replace = '.ngl-article';
	foreach ($output->find($replace) as $key => $element) {
		$element->outertext = '';
	}

	$output->save();

	return (string) $output;
}
add_filter('the_content_feed', 'newsletterglue_content_feed', 500);

/**
 * Convert external link to SVG.
 */
function newsletterglue_kses_post($html)
{

	$html = str_replace('[externallink]', '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg>', wp_kses_post($html));

	return $html;
}

/**
 * Generate authorization url for AWeber
 */
function ngl_aweber_authorize_url()
{
	include_once('integrations/aweber/lib/api.php');
	if (class_exists('NGL_AWeber_API')) {
		$api = new NGL_AWeber_API();
		return $api->getAuthorizeUrl();
	}
}

/**
 * Generate authorization url for Constant Contact
 */
function ngl_constantcontact_authorize_url()
{
	include_once('integrations/constantcontact/lib/api.php');
	if (class_exists('NGL_Constantcontact_API')) {
		$api = new NGL_Constantcontact_API();
		return $api->getAuthorizationURL();
	}
}

/**
 * Get ad inserter placeholder image.
 */
function newsletterglue_get_ad_inserter_placeholder_image()
{
	return untrailingslashit(plugins_url('/', NGL_PLUGIN_FILE)) . '/assets/images/ad-inserter/ad-inserter-placeholder-background.png';
}

/**
 * Get latest post title.
 */
function newsletterglue_get_latest_post_title()
{
	$post = wp_get_recent_posts(array(
		'numberposts' => 1,
		'post_status' => 'publish'
	));

	return $post[0]['post_title'];
}

/**
 * Parse day exceptions.
 * 
 * @param array $data The data from the POST request.
 */
function newsletterglue_parse_frequency_day_exceptions($data)
{
	$day_exception = array();

	if (isset($data['ngl_frequency_day_exception']) && is_array($data['ngl_frequency_day_exception'])) {
		foreach ($data['ngl_frequency_day_exception'] as $exception) {
			// Split by comma if it contains commas
			if (strpos($exception, ',') !== false) {
				$values = explode(',', $exception);
				foreach ($values as $value) {
					$value = trim(sanitize_text_field($value));
					// Explicitly allow '0' as a valid value, since empty() considers '0' as empty
					if ($value === '0' || ! empty($value)) {
						$day_exception[] = $value;
					}
				}
			} else {
				$value = trim(sanitize_text_field($exception));
				// Explicitly allow '0' as a valid value, since empty() considers '0' as empty
				if ($value === '0' || ! empty($value)) {
					$day_exception[] = $value;
				}
			}
		}
		// Remove duplicates
		$day_exception = array_unique($day_exception);
	}
	return $day_exception;
}
