<?php
/**
 * Misc Functions.
 *
 * @package Newsletter Glue
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Checks if post type is an automation.
 */
function newsletterglue_is_automation() {
	global $post_type;

	return $post_type && 'ngl_automation' === $post_type ? true : false;
}

/**
 * Demo support.
 */
function nglue_demo_init() {

	if ( ! strstr( home_url(), 'instawp.xyz' ) ) {
		delete_option( 'newsletterglue_pro_demo' );
	}

	if ( strstr( home_url(), 'instawp.xyz' ) ) {
		update_option( 'newsletterglue_pro_license', '17ab1a5d8879140daaaad9a8a0c50e76' );
		update_option( 'newsletterglue_pro_license_expires', 'lifetime' );
		update_option( 'newsletterglue_pro_license_priceid', 4 );
		update_option( 'newsletterglue_pro_demo', 'yes' );
	}
}
add_action( 'admin_init', 'nglue_demo_init' );

/**
 * Add a custom demo notice.
 */
function newsletterglue_admin_demo_notice() {
	if ( strstr( home_url(), 'instawp.xyz' ) ) {
		?>

		<div class="notice notice-info ngl-notice">
			<p>This is a free demo site for trialling Newsletter Glue. This WordPress site will expire in 5 days. No data is stored from this site.</p>
			<p><a href="https://newsletterglue.com/pricing/?utm_source=demo" class="button-primary" target="_blank">Buy Newsletter Glue</a></p>
		</div>

		<?php
	}
}
add_action( 'admin_notices', 'newsletterglue_admin_demo_notice', 10 );

/**
 * Global hooks.
 */
add_filter( 'newsletterglue_settings_tab_blocks_save_button', '__return_false' );
add_filter( 'newsletterglue_settings_tab_connect_save_button', '__return_false' );

/**
 * Checks if an integration is locked to a better tier.
 *
 * @param string $app A value for the specified ESP.
 */
function newsletterglue_is_tier_locked( $app ) {

	$list = newsletterglue_get_esp_list();

	foreach ( $list as $key => $data ) {
		if ( $app === $data['value'] ) {
			if ( ! empty( $data['requires'] ) ) {
				return true;
			}
		}
	}

	return false;
}

/**
 * Creates a preview for emails.
 */
function newsletterglue_preview_emails() {

	if ( ! empty( $_GET['preview_email'] ) ) { // phpcs:ignore

		if ( ! current_user_can( 'publish_newsletterglue' ) ) {
			return;
		}

		$post_id = absint( $_GET['preview_email'] ); // phpcs:ignore

		$test = get_post( $post_id );
		if ( ! isset( $test->ID ) ) {
			return;
		}

		ob_start();

		$data = get_post_meta( $test->ID, '_newsletterglue', true );

		$app = isset( $data['app'] ) ? $data['app'] : '';

		if ( $app ) {
			include_once newsletterglue_get_path( $app ) . '/init.php';
			$classname = 'NGL_' . ucfirst( $app );
			$api       = new $classname();
		}

		$preview_url = newsletterglue_generate_web_link( $post_id );
		$content     = newsletterglue_generate_content( $post_id, ! empty( $data['subject'] ) ? $data['subject'] : '', $app );

		$size       = round( mb_strlen( $content ) / 1024 );
		$size_class = 'green';

		if ( $size > 97 ) {
			$size_class = 'red';
		} elseif ( $size <= 97 && $size >= 90 ) {
				$size_class = 'yellow';
		}

		include_once NGL_PLUGIN_DIR . 'includes/admin/views/preview.php';

		$message = ob_get_clean();

		echo $message; // phpcs:ignore

		exit;

	}
}
add_action( 'init', 'newsletterglue_preview_emails', 1000 );

/**
 * View newsletter in web.
 */
function newsletterglue_view_in_web() {
	global $in_iframe;

	if ( ! empty( $_GET['view_newsletter'] ) ) { // phpcs:ignore

		$post_id = ! empty( $_GET['id'] ) ? absint( $_GET['id'] ) : 0; // phpcs:ignore
		$token   = sanitize_text_field( wp_unslash( $_GET['view_newsletter'] ) ); // phpcs:ignore

		$test = get_post( $post_id );
		if ( ! isset( $test->ID ) ) {
			return;
		}

		$current_token = md5( $post_id . home_url() );
		if ( $token !== $current_token ) {
			return;
		}

		ob_start();

		$data = get_post_meta( $test->ID, '_newsletterglue', true );

		$app = isset( $data['app'] ) ? $data['app'] : '';

		if ( $app ) {
			include_once newsletterglue_get_path( $app ) . '/init.php';
			$classname = 'NGL_' . ucfirst( $app );
			$api       = new $classname();
		}

		if ( isset( $_GET['iframe'] ) ) { // phpcs:ignore
			$in_iframe = 'yes';
		}

		echo newsletterglue_generate_content( $post_id, ! empty( $data[ 'subject' ] ) ? $data[ 'subject' ] : '', $app ); // phpcs:ignore

		$message = ob_get_clean();

		echo $message; // phpcs:ignore

		exit;

	}
}
add_action( 'init', 'newsletterglue_view_in_web', 1000 );

/**
 * Generate web link for a post ID.
 *
 * @param integer $post_id A numeric post ID.
 */
function newsletterglue_generate_web_link( $post_id = 0 ) {

	// Get token.
	$token = md5( $post_id . home_url() );

	$view_in_web = add_query_arg( 'view_newsletter', $token, trailingslashit( home_url() ) );
	$view_in_web = add_query_arg( 'id', $post_id, $view_in_web );

	return apply_filters( 'newsletterglue_generate_web_link', $view_in_web, $post_id );
}

/**
 * Checks if post is scheduled.
 *
 * @param integer $post_id A numeric post ID.
 */
function newsletterglue_is_post_scheduled( $post_id ) {
	return get_post_meta( $post_id, '_ngl_future_send', true ) ? true : false;
}

/**
 * Returns true if free version is being used.
 */
function newsletterglue_is_free_version() {

	$plugin_data = get_plugin_data( NGL_PLUGIN_FILE );

	if ( isset( $plugin_data['Name'] ) ) {
		if ( stristr( $plugin_data['Name'], 'PRO' ) ) {
			return false;
		}
	}

	return true;
}

/**
 * Send newsletter when post is finally published.
 *
 * @param integer $post_id A numeric post ID.
 */
function newsletterglue_publish_future_post( $post_id ) {

	$has_newsletter = get_post_meta( $post_id, '_ngl_future_send', true );

	if ( $has_newsletter ) {

		newsletterglue_send( $post_id );

		delete_post_meta( $post_id, '_ngl_future_send' );

	}
}
add_action( 'publish_future_post', 'newsletterglue_publish_future_post' );

/**
 * Send the newsletter and mark as sent.
 *
 * @param integer $post_id A numeric post ID.
 * @param boolean $test Whether we are running test mode or not.
 */
function newsletterglue_send( $post_id = 0, $test = false, $force_draft = false ) {

	$response = null;

	$post = get_post( $post_id );
	$data = get_post_meta( $post_id, '_newsletterglue', true );

	if ( ! $test ) {
		$data['sent'] = true;
	}

	update_post_meta( $post_id, '_newsletterglue', $data );

	$app = $data['app'];

	include_once newsletterglue_get_path( $app ) . '/init.php';

	$classname = 'NGL_' . ucfirst( $app );

	$api = new $classname();

	// Send the newsletter.
	if ( $force_draft ) {
		$api->force_draft();
	}

	$response = $api->send_newsletter( $post_id, $data, $test );

	return $response;
}

/**
 * Mark a newsletter as unsent.
 *
 * @param integer $post_id A numeric post ID.
 */
function newsletterglue_reset_newsletter( $post_id = 0 ) {

	$data = get_post_meta( $post_id, '_newsletterglue', true );

	// Allow campaign to be resent.
	if ( isset( $data['sent'] ) ) {
		unset( $data['sent'] );
	}

	update_post_meta( $post_id, '_newsletterglue', $data );

	delete_post_meta( $post_id, '_ngl_future_send' );

	// campaigns.
	$campaigns = get_post_meta( $post_id, '_ngl_results', true );
	if ( $campaigns && is_array( $campaigns ) ) {
		foreach ( $campaigns as $key => $item ) {
			if ( isset( $item['type'] ) && 'schedule' == $item['type'] ) {
				unset( $campaigns[ $key ] );
			}
		}
	}
	update_post_meta( $post_id, '_ngl_results', $campaigns );
}

/**
 * Get form defaults.
 *
 * @param object $post An object with the post data.
 * @param object $api The class/API for active ESP.
 */
function newsletterglue_get_form_defaults( $post = 0, $api = '' ) {

	$defaults = new stdclass();

	// Subject.
	if ( 'auto-draft' === $post->post_status ) {
		$subject = '';
	} else {
		$subject = get_the_title( $post->ID );
	}

	$app = newsletterglue_default_connection();

	$defaults->from_name    = newsletterglue_get_option( 'from_name', $app );
	$defaults->from_email   = newsletterglue_get_option( 'from_email', $app );
	$defaults->test_email   = newsletterglue_get_option( 'from_email', $app );
	$defaults->schedule     = newsletterglue_get_option( 'schedule', 'global' );
	$defaults->utm_source   = newsletterglue_get_option( 'utm_source', 'global' );
	$defaults->utm_campaign = newsletterglue_get_option( 'utm_campaign', 'global' );
	$defaults->utm_medium   = newsletterglue_get_option( 'utm_medium', 'global' );
	$defaults->utm_content  = newsletterglue_get_option( 'utm_content', 'global' );
	$defaults->subject      = $subject;
	$defaults->preview_text = '';

	// Get options from API.
	if ( method_exists( $api, 'get_form_defaults' ) ) {

		$api_options = $api->get_form_defaults();

		foreach ( $api_options as $key => $value ) {

			$defaults->{$key} = $value;

		}
	}

	return $defaults;
}

/**
 * Save newsletter options as meta data.
 *
 * @param integer $post_id A post ID.
 * @param array   $data An array with post data.
 */
function newsletterglue_save_data( $post_id, $data ) {

	$meta = array();

	$old_meta = get_post_meta( $post_id, '_newsletterglue', true );

	if ( isset( $old_meta ) && ! empty( $old_meta['sent'] ) ) {
		$meta['sent'] = true;
	}

	foreach ( $data as $key => $value ) {
		if ( strstr( $key, 'ngl_' ) ) {
			$key          = str_replace( 'ngl_', '', $key );
			$meta[ $key ] = $value;

			if ( 'when' === $key ) {
				$timestamp         = strtotime( $value );
				$meta['timestamp'] = $timestamp;
			}
		}
	}

	if ( isset( $meta ) && ! empty( $meta ) ) {
		if ( empty( $meta['brand'] ) ) {
			$meta['brand'] = '';
		}
		if ( empty( $meta['lists'] ) ) {
			$meta['lists'] = '';
		}
		if ( empty( $meta['groups'] ) ) {
			$meta['groups'] = '';
		}
		if ( empty( $meta['segments'] ) ) {
			$meta['segments'] = '';
		}
		if ( empty( $meta['track_opens'] ) ) {
			$meta['track_opens'] = 0;
		}
		if ( empty( $meta['track_clicks'] ) ) {
			$meta['track_clicks'] = 0;
		}
		if ( empty( $meta['unsub_groups'] ) ) {
			$meta['unsub_groups'] = '';
		}
		if ( empty( $meta['app'] ) ) {
			$meta['app'] = newsletterglue_default_connection();
		}
		if ( empty( $meta['utm_source'] ) ) {
			$meta['utm_source'] = '[none]';
		}
		if ( empty( $meta['utm_campaign'] ) ) {
			$meta['utm_campaign'] = '[none]';
		}
		if ( empty( $meta['utm_medium'] ) ) {
			$meta['utm_medium'] = '[none]';
		}
		update_post_meta( $post_id, '_newsletterglue', $meta );
	}

	do_action( 'newsletterglue_save_metadata', $post_id );
}

/**
 * Get newsletter options as meta data.
 *
 * @param integer $post_id A post ID.
 */
function newsletterglue_get_data( $post_id ) {

	$data = get_post_meta( $post_id, '_newsletterglue', true );

	$s = new stdclass();

	if ( is_array( $data ) ) {
		foreach ( $data as $key => $value ) {
			$s->{$key} = $value;
		}
	}

	return $s;
}

/**
 * Check if the plugin has no active api.
 *
 * @param string $selected A selected parameter.
 */
function newsletterglue_has_no_active_api( $selected = '' ) {

	$apis = get_option( 'newsletterglue_integrations' );

	if ( empty( $apis ) ) {
		return true;
	}

	return false;
}

/**
 * Get default API connection.
 */
function newsletterglue_default_connection() {

	$apis = get_option( 'newsletterglue_integrations' );

	if ( empty( $apis ) ) {
		return false;
	}

	$apis = array_keys( $apis );

	return $apis[0];
}

/**
 * Get default ESP data.
 */
function newsletterglue_get_esp_data() {

	$apis = get_option( 'newsletterglue_integrations' );

	if ( empty( $apis ) ) {
		return array();
	}

	$esp = newsletterglue_default_connection();

	return isset( $apis[ $esp ] ) ? array_merge( array( 'provider' => $esp ), $apis[ $esp ] ) : array();
}

/**
 * Add title to newsletter.
 *
 * @param string $title A post title.
 * @param object $post A post object.
 */
function newsletterglue_add_title( $title, $post ) {
	if ( isset( $post->post_type ) && 'ngl_pattern' == $post->post_type ) {
		return false;
	}

	return apply_filters( 'newsletterglue_post_title', '<h1 class="title">' . $title . '</h1>', $post );
}


/**
 * Fix the content.
 *
 * @param string $content Post content.
 */
function newsletterglue_fix_the_content( $content ) {

	$post_id = get_the_ID();

	$content = str_replace( '%7D%7D', '}}', $content );
	$content = str_replace( '%7B%7B', '{{', $content );
	$content = str_replace( '{%7B', '{{', $content );
	$content = str_replace( trailingslashit( admin_url() ) . '%7B%7B%20', '{{ ', $content );
	$content = str_replace( untrailingslashit( admin_url() ) . '%7B%7B%20', '{{ ', $content );
	$content = str_replace( 'http://%7B%7B%20', '{{ ', $content );
	$content = str_replace( 'https://%7B%7B%20', '{{ ', $content );
	$content = str_replace( '%20%7D%7D/', ' }}', $content );
	$content = str_replace( '%20%7D%7D', ' }}', $content );

	if ( ! empty( $post_id ) ) {
		include_once NGL_PLUGIN_DIR . 'includes/integrations/core/init.php';
		$api     = new NGL_Core();
		$content = $api->convert_tags( $content, $post_id );
	}

	return $content;
}
add_filter( 'the_content', 'newsletterglue_fix_the_content', 1 );

/**
 * Prepare block attributes.
 *
 * @param mixed $block The block data.
 * @param mixed $subblock Sub block data.
 */
function newsletterglue_get_attrs( $block, $subblock ) {
	$attrs       = array();
	$block       = trim( $block );
	$block_parts = explode( 'wp:' . $subblock . ' {', $block );
	if ( ! isset( $block_parts[1] ) ) {
		return array();
	}
	$_attrs = str_replace( '} /', '', $block_parts[1] );
	$attrsp = explode( ',', $_attrs );
	if ( ! empty( $attrsp ) ) {
		foreach ( $attrsp as $attrs1 ) {
			$split_attr    = explode( ':', $attrs1 );
			$key           = str_replace( '"', '', $split_attr[0] );
			$value         = str_replace( '"', '', $split_attr[1] );
			$attrs[ $key ] = $value;
		}
	}
	return $attrs;
}

/**
 * Generate email template content from post and subject.
 *
 * @param mixed  $post A post ID or post object.
 * @param string $subject An email subject.
 * @param string $app Email service provider.
 */
function newsletterglue_generate_content( $post = '', $subject = '', $app = '' ) {
	global $ng_post, $ngl_post_id;

	$log_file = WP_CONTENT_DIR . '/newsletterglue_generate_content.log';

	// If post ID is provided.
	if ( is_numeric( $post ) ) {
		$post_id = $post;
		$post    = get_post( $post_id );
	}

	file_put_contents( $log_file, 'Post: ' . print_r( $post, true ) . PHP_EOL, FILE_APPEND );

	$ngl_post_id = $post->ID;

	file_put_contents( $log_file, 'Post ID: ' . $ngl_post_id . PHP_EOL, FILE_APPEND );

	// No subject.
	if ( empty( $subject ) ) {
		$subject = $post->post_title;
	}

	file_put_contents( $log_file, 'Subject: ' . $subject . PHP_EOL, FILE_APPEND );

	$ng_post = $post;

	// This is intended for email.
	if ( ! defined( 'NGL_IN_EMAIL' ) ) {
		define( 'NGL_IN_EMAIL', true );
	}

	$post_type = isset( $post->post_type ) ? $post->post_type : '';

	file_put_contents( $log_file, 'Post Type: ' . $post_type . PHP_EOL, FILE_APPEND );

	$data         = get_post_meta( $post->ID, '_newsletterglue', true );
	$preview_text = isset( $data['preview_text'] ) ? esc_attr( $data['preview_text'] ) : '';

	// Remove auto embed.
	remove_filter( 'the_content', array( $GLOBALS['wp_embed'], 'autoembed' ), 8 );

	$the_content = '';

	// Add preview text to email.
	if ( ! empty( $preview_text ) ) {
		$the_content .= '<div class="ngl-preview-text">' . $preview_text . '</div>';
	}

	// Hook that runs just before everything.
	$the_content .= '{{ ng.header }}';

	// Add logo.
	if ( apply_filters( 'newsletterglue_show_logo', false, $post ) ) {
		$the_content .= newsletterglue_add_logo();
	}

	// Post content.
	$post_content = $post->post_content;
	file_put_contents( $log_file, 'Post Content: ' . $post_content . PHP_EOL, FILE_APPEND );
	if ( preg_match_all( '/<!--([\s\S]+?)-->/', $post->post_content, $rblocks ) ) {
		if ( isset( $rblocks[1] ) ) {
			foreach ( $rblocks[1] as $index => $block ) {
				if ( strstr( $block, 'wp:post-featured' ) ) {
					$post_content = str_replace( '<!--' . $block . '-->', newsletterglue_get_featured_image_markup( $block, $post ), $post_content );
				}
				if ( strstr( $block, 'wp:post-title' ) ) {
					$post_content = str_replace( '<!--' . $block . '-->', newsletterglue_get_post_title_markup( $block, $post ), $post_content );
				}
			}
		}
	}

	// Filter for legacy header code.
	$has_custom_header = apply_filters( 'newsletterglue_post_title', '', $post );

	if ( $has_custom_header ) {
		if ( apply_filters( 'newsletterglue_post_title_url', true ) ) {
			$the_content .= '<h1 class="title"><a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a></h1>';
		} else {
			$the_content .= '<h1 class="title">' . $post->post_title . '</h1>';
		}
		$the_content .= $has_custom_header;
	}

	$has_featured_image = apply_filters( 'newsletterglue_post_featured', '', $post );

	if ( $has_featured_image ) {
		$the_content .= newsletterglue_add_masthead_image( $post, 'below' );
	}

	$the_post_content = do_shortcode( $post_content );
	file_put_contents( $log_file, 'The Post Content (do_shortcode): ' . $the_post_content . PHP_EOL, FILE_APPEND );
	$the_post_content = do_blocks( $the_post_content );
	file_put_contents( $log_file, 'The Post Content (do_blocks): ' . $the_post_content . PHP_EOL, FILE_APPEND );
	$the_post_content = str_replace( '<br><br>', 'I_am_br_place_holder', $the_post_content );
	$the_post_content = newsletterglue_replace_json_unicode_escape_sequences( $the_post_content );
	file_put_contents( $log_file, 'The Post Content (str_replace): ' . $the_post_content . PHP_EOL, FILE_APPEND );
	$the_post_content = wpautop( $the_post_content );
	file_put_contents( $log_file, 'The Post Content (wpautop): ' . $the_post_content . PHP_EOL, FILE_APPEND );
	$the_content     .= $the_post_content;
	file_put_contents( $log_file, 'The Content: ' . $the_content . PHP_EOL, FILE_APPEND );

	// Hook that runs just before the credits line.
	$the_content .= '{{ ng.footer }}';

	// Credits.
	if ( get_option( 'newsletterglue_credits' ) && 'ngl_pattern' != $post_type ) {
		/* translators: %s: a link to NG site */
		$the_content .= '<p class="ngl-credits">' . sprintf( __( 'Built with %s', 'newsletter-glue' ), '<a href="https://newsletterglue.com/?utm_source=newsletter&utm_medium=ng-signature" target="_blank">' . __( 'Newsletter Glue', 'newsletter-glue' ) . '</a>' ) . '</p>';
	}

	// Allow 3rd party to customize content tag.
	if ( 'ngl_pattern' != $post_type ) {
		$the_content = apply_filters( 'newsletterglue_email_content_' . $app, $the_content, $post, $subject );
	}

	$the_content = apply_filters( 'newsletterglue_email_content', $the_content, $post, $subject, $app );
	file_put_contents( $log_file, 'The Content (apply_filters): ' . $the_content . PHP_EOL, FILE_APPEND );

	// Get the email template including css tags.
	$html = newsletterglue_get_email_template( $post, $subject, $app );
	file_put_contents( $log_file, 'HTML: ' . $html . PHP_EOL, FILE_APPEND );

	// Process content tags.
	$html = str_replace( '{{ ng.title }}', $subject, $html );
	$html = str_replace( '{{ ng.content }}', $the_content, $html );
	$html = str_replace( 'http://{{', '{{', $html );
	$html = str_replace( 'https://{{', '{{', $html );
	$html = str_replace( '<br />', '__NGL_BREAK__', $html );

	// Filter for original content. before email work.
	$html = apply_filters( 'newsletterglue_generate_content', $html, $post );

	// Email compatible emails.
	$html = apply_filters( 'newsletterglue_generated_html_output', $html, $post->ID, $app );

	// Emogrify process.
	$emogrifier_class = '\\Pelago\\Emogrifier';
	if ( ! class_exists( $emogrifier_class ) ) {
		include_once NGL_PLUGIN_DIR . 'includes/libraries/class-emogrifier.php';
	}
	try {
		$emogrifier = new $emogrifier_class( $html );
		$html       = $emogrifier->emogrify();
		$html       = str_replace( '__NGL_BREAK__', '<br />', $html );
	} catch ( Exception $e ) {
		$error = $e->getMessage();
		wp_die( esc_html( $error ) );
	}

	$html = str_replace( array( '%7B', '%7D', '%24', '%5B', '%5D', '*%7C', '%7C*' ), array( '{', '}', '$', '[', ']', '*|', '|*' ), $html );
	$html = str_replace( '@media screen and (max-width:642px) {', '#template_inner .ngl-ignore-mrkp a { color: ' . esc_attr( newsletterglue_get_theme_option( 'a_colour' ) ) . '; } p.ngl-unsubscribe a { color: #707070 !important; } a, #template_inner td a { color: ' . esc_attr( newsletterglue_get_theme_option( 'a_colour' ) ) . '; } @media screen and (max-width:642px) {', $html );
	$html = wp_encode_emoji( $html );
	$html = str_replace( '{{%20', '{{ ', $html );
	$html = str_replace( '%20}}', ' }}', $html );
	$html = str_replace( '{%%20', '{% ', $html );
	$html = str_replace( '%20%}', ' %}', $html );
	$html = str_replace( trailingslashit( admin_url() ) . '{{', '{{', $html );
	$html = str_replace( untrailingslashit( admin_url() ) . '{{', '{{', $html );

	// ESP html filter.
	if ( $app ) {
		$html = apply_filters( "newsltterglue_{$app}_html_content", $html, $post->ID );
	} else {
		include_once NGL_PLUGIN_DIR . 'includes/integrations/core/init.php';
		$api  = new NGL_Core();
		$html = $api->convert_tags( $html, $post->ID );
	}

	// The final HTML content.
	$html = apply_filters( 'newsletterglue_final_html_content', $html );

	$html = str_replace( '{{ ng.header }}', apply_filters( 'newsletterglue_email_content_header', '', $app, $post ), $html );
	$html = str_replace( '{{ ng.footer }}', apply_filters( 'newsletterglue_email_content_footer', '', $app, $post ), $html );
	$html = str_replace( '{{ ng.end }}', apply_filters( "newsletterglue_email_end_{$app}", '', $post ), $html );

	// Find/replace URL.
	$url = get_option( 'newsletterglue_home_url' );
	if ( ! empty( $url ) && ( esc_url_raw( $url ) === $url ) ) {
		$old  = untrailingslashit( home_url() );
		$new  = untrailingslashit( $url );
		$html = str_replace( $old, $new, $html );
	}

	$html = str_replace( '<br>', '', $html );
	$html = str_replace( '@media', "\r\n@media", $html );
	$html = str_replace( '}}</style>', "}\r\n}\r\n</style>", $html );
	$html = str_replace( '{#', "{\r\n#", $html );
	$html = str_replace( '}#', "}\r\n#", $html );

	// Color variables cannot be used in email.
	if ( strstr( $html, 'var(' ) ) {
		$re         = '/var?\((((?>[^()]+)|(?R))*)\)/';
		$colorsvars = preg_match_all( $re, $html, $matches );
		if ( isset( $matches[0] ) ) {
			foreach ( $matches[0] as $colorvar ) {
				$split_color = explode( ',', $colorvar );
				if ( isset( $split_color[1] ) ) {
					$clean_color = str_replace( ')', '', $split_color[1] );
					$html        = str_replace( $colorvar, trim( $clean_color ), $html );
				}
			}
		}
	}

	$html = str_replace( 'I_am_br_place_holder', '<br><br>', $html );
	$html = str_replace( 'font-family: Tahoma', 'font-family: Tahoma, -apple-system, sans-serif', $html );
/*
	// For debugging
	 var_dump($html);
	 exit;
*/
	return $html;
}

/**
 * Direct replacement of JSON Unicode escape sequences with their actual characters.
 *
 * @param string $html The HTML content.
 */
function newsletterglue_replace_json_unicode_escape_sequences( $html ) {

	$search = array(
		'\\u003C', '\\u003c', // < (less than)
		'\\u003E', '\\u003e', // > (greater than)
		'\\u0022', // " (double quote)
		'\\u0027', // ' (single quote)
		'\\u0026', // & (ampersand)
		'\\u002F', '\\u002f' // / (forward slash)
	);

	$replace = array(
		'<', '<',
		'>', '>',
		'"',
		"'",
		'&',
		'/', '/'
	);

	$html = str_replace( $search, $replace, $html );

	return $html;
}


/**
 * Get image widths.
 *
 * @param object  $td An object for the TD cell.
 * @param integer $threshold A number containing the width threshold.
 */
function newsletterglue_get_image_width_by_td( $td, $threshold = 600 ) {

	$count = count( $td->parent()->children() );
	if ( $td->style ) {
		$s       = $td->style;
		$results = array();
		$styles  = explode( ';', $s );

		foreach ( $styles as $style ) {
			$properties = explode( ':', $style );
			if ( 2 === count( $properties ) ) {
				$results[ trim( $properties[0] ) ] = trim( $properties[1] );
			}
		}
		if ( isset( $results['width'] ) ) {
			$width = $results['width'];
		} else {
			$width = '100%';
		}
	} else {
		$width = '100%';
	}
	$clean_width = str_replace( '%', '', $width );
	$clean_width = str_replace( 'px', '', $clean_width );
	if ( ! is_numeric( $clean_width ) ) {
		return false;
	}

	$image_width = ( $clean_width / 100.00 ) * ( $threshold - ( 20 * $count ) - 20 );

	return $image_width;
}

/**
 * Add logo image.
 */
function newsletterglue_add_logo() {

	$id = get_option( 'newsletterglue_logo_id' );

	if ( ! $id ) {
		return null;
	}

	$logo          = wp_get_attachment_url( $id );
	$data          = wp_get_attachment_image_src( $id, 'full' );
	$width         = $data[1];
	$height        = $data[2];
	$logo_url      = get_option( 'newsletterglue_logo_url' );
	$logo_position = get_option( 'newsletterglue_position_logo' );

	$max_logo_w = newsletterglue_get_theme_option( 'max_logo_w' );

	if ( $max_logo_w && $width > $max_logo_w ) {
		$ratio    = $width / $height;
		$n_width  = $max_logo_w;
		$n_height = ceil( $max_logo_w / $ratio );
	} else {
		$n_width  = $width;
		$n_height = $height;
	}

	if ( ! $logo_position ) {
		$logo_position = 'center';
	}

	if ( $logo ) {
		if ( esc_url( $logo_url ) ) {
			return '<div class="ngl-logo ngl-logo-' . esc_attr( $logo_position ) . '"><a href="' . esc_url( $logo_url ) . '" target="_blank" style="display: inline-block;" class="logo"><img class="logo-image" data-w="' . esc_attr( $n_width ) . '" data-h="' . esc_attr( $n_height ) . '" src="' . esc_url( $logo ) . '" /></a></div>';
		} else {
			return '<div class="ngl-logo ngl-logo-' . esc_attr( $logo_position ) . '"><img class="logo-image" data-w="' . esc_attr( $n_width ) . '" data-h="' . esc_attr( $n_height ) . '" src="' . esc_url( $logo ) . '" /></div>';
		}
	}

	return null;
}

/**
 * Add masthead image.
 */
function newsletterglue_add_masthead_image( $post, $position = 'below' ) {

	$post_id      = $post->ID;
	$data         = get_post_meta( $post_id, '_newsletterglue', true );
	$use_image    = isset( $data['add_featured'] ) ? sanitize_text_field( wp_unslash( $data['add_featured'] ) ) : get_option( 'newsletterglue_add_featured' );
	$use_image    = true;
	$return_str   = '';
	$show_caption = false;

	$link_featured_image = get_option( 'newsletterglue_link_featured' );

	// Use of featured image.
	if ( $use_image ) {
		$url = wp_get_attachment_url( get_post_thumbnail_id( $post_id ) );
		if ( $url ) {
			if ( $link_featured_image && $link_featured_image === 'yes' ) {
				$return_str = '<div class="ngl-masthead ngl-masthead-' . $position . '"><a href="' . esc_url( get_permalink( $post_id ) ) . '" class="masthead-link"><img src="' . $url . '" class="masthead" /></a></div>';
			} else {
				$return_str = '<div class="ngl-masthead ngl-masthead-' . $position . '"><img src="' . esc_url( $url ) . '" class="masthead" /></div>';
			}
		}
	}

	if ( apply_filters( 'newsletterglue_show_masthead_caption', $show_caption ) ) {
		$return_str .= '<table cellpadding="10"> <tr><td> <small><center> ' . get_the_post_thumbnail_caption( $post_id ) . '</center><small></td></tr></table>';
	}

	return $return_str;
}

/**
 * Get email template html.
 *
 * @param object $post The post object.
 * @param string $subject The email subject.
 * @param string $app The email service provider.
 */
function newsletterglue_get_email_template( $post, $subject, $app ) {
	global $in_iframe;

	ob_start();

	include NGL_PLUGIN_DIR . 'includes/admin/views/email-styles.php';

	return ob_get_clean();
}

/**
 * Get theme option.
 *
 * @param string $id This is theme option ID.
 * @param array  $theme This is an array containing the theme.
 */
function newsletterglue_get_theme_option( $id = '', $theme = null ) {
	global $post, $ngl_post_id;

	// Set post styles if we are viewing a post.
	if ( ! empty( $ngl_post_id ) ) {
		$post_styles = get_post_meta( $ngl_post_id, '_newsletterglue_theme', true );
		if ( isset( $_GET['debug-ng-theme'] ) ) { // phpcs:ignore
			die( wp_kses_post( $post_styles ) );
		}
	} elseif ( ! empty( $post ) && ! empty( $post->ID ) ) {
			$post_styles = get_post_meta( $post->ID, '_newsletterglue_theme', true );
	}

	// Check if no theme was provided.
	if ( ! $theme ) {
		$theme = get_option( 'newsletterglue_theme' );
	}

	// We should use the post theme.
	if ( ( ! empty( $ngl_post_id ) || ! empty( $post ) ) && ! empty( $post_styles ) ) {
		if ( is_array( $theme ) ) {
			$theme = array_merge( $theme, $post_styles );
		} else {
			$theme = $post_styles;
		}
	}

	// Get theme option.
	if ( isset( $theme[ $id ] ) ) {

		if ( empty( $theme[ $id ] ) ) {
			if ( in_array( $id, array( 'email_bg', 'container_bg', 'btn_border' ) ) ) {
				return 'transparent';
			}
			if ( in_array( $id, array( 'h1_colour', 'h2_colour', 'h3_colour', 'h4_colour', 'h5_colour', 'h6_colour', 'p_colour', 'a_colour' ) ) ) {
				return 'inherit';
			}
			if ( in_array( $id, array( 'btn_bg' ) ) ) {
				return '#32373c';
			}
			if ( in_array( $id, array( 'btn_colour' ) ) ) {
				return '#fff';
			}
		}

		$return = $theme[ $id ];

		if ( strstr( $id, '_size' ) && strstr( $return, 'px' ) ) {
			$return = str_replace( 'px', '', $return );
		}

		return $return;
	}

	// Get default value.
	$default = newsletterglue_get_theme_default( $id );

	return $default ? $default : false;
}

/**
 * Get a default value for a theme option.
 *
 * @param string $key A theme option key.
 */
function newsletterglue_get_theme_default( $key ) {

	$keys = array(
		'h1_colour'                 => '#333333',
		'h2_colour'                 => '#333333',
		'h3_colour'                 => '#333333',
		'h4_colour'                 => '#333333',
		'h5_colour'                 => '#333333',
		'h6_colour'                 => '#333333',
		'p_colour'                  => '#666666',
		'h1_size'                   => 32,
		'h2_size'                   => 28,
		'h3_size'                   => 24,
		'h4_size'                   => 22,
		'h5_size'                   => 20,
		'h6_size'                   => 18,
		'p_size'                    => 16,
		'h1_align'                  => 'left',
		'h2_align'                  => 'left',
		'h3_align'                  => 'left',
		'h4_align'                  => 'left',
		'h5_align'                  => 'left',
		'h6_align'                  => 'left',
		'p_align'                   => 'left',
		'email_bg'                  => '#f9f9f9',
		'container_bg'              => '#ffffff',
		'accent'                    => '#0088A0',
		'a_colour'                  => '#0088A0',
		'btn_bg'                    => '#0088A0',
		'btn_colour'                => '#ffffff',
		'btn_radius'                => 0,
		'btn_border'                => '#0088A0',
		'btn_width'                 => 150,
		'container_padding1'        => 5,
		'container_padding2'        => 15,
		'container_margin1'         => 0,
		'container_margin2'         => 0,
		'max_logo_w'                => 0,
		// Mobile.
		'mobile_h1_size'            => 28,
		'mobile_h2_size'            => 24,
		'mobile_h3_size'            => 22,
		'mobile_h4_size'            => 20,
		'mobile_h5_size'            => 18,
		'mobile_h6_size'            => 16,
		'mobile_p_size'             => 16,
		'mobile_container_padding1' => 5,
		'mobile_container_padding2' => 15,
		'mobile_container_margin1'  => 0,
		'mobile_container_margin2'  => 0,
		'mobile_btn_width'          => 150,
		'mobile_max_logo_w'         => 0,
		'font'                      => 'helvetica',
	);

	return isset( $keys[ $key ] ) ? $keys[ $key ] : '';
}

/**
 * Max logo width.
 *
 * @param boolean $mobile Whether we are getting settings for mobile only.
 */
function ngl_get_max_logo_width( $mobile = false ) {
	$var = $mobile ? 'mobile_max_logo_w' : 'max_logo_w';
	$max = newsletterglue_get_theme_option( $var );

	return $max ? $max . 'px' : '100%';
}

/**
 * Email fonts.
 */
function newsletterglue_get_email_fonts() {

	$fonts = array(
		'arial'           => 'Arial',
		'helvetica'       => 'Helvetica',
		'times_new_roman' => 'Times New Roman',
		'verdana'         => 'Verdana',
		'courier_new'     => 'Courier New',
		'courier'         => 'Courier',
		'tahoma'          => 'Tahoma',
		'georgia'         => 'Georgia',
		'palatino'        => 'Palatino',
		'trebuchet_ms'    => 'Trebuchet MS',
		'geneva'          => 'Geneva',
		'inter'           => 'Inter',
		'inherit'         => 'inherit',
	);

	$fonts = apply_filters( 'newsletterglue_get_email_fonts', $fonts );

	asort( $fonts );

	return array_merge( array( 'inherit' => __( 'Default', 'newsletter-glue' ) ), $fonts );
}

/**
 * Get font name.
 *
 * @param string $font A font short name.
 */
function newsletterglue_get_font_name( $font = '' ) {

	$fonts = newsletterglue_get_email_fonts();

	return apply_filters( 'newsletterglue_get_font_name', isset( $fonts[ $font ] ) ? $fonts[ $font ] : '', $font );
}

/**
 * Get post types.
 */
function newsletterglue_get_post_types() {

	$post_types  = get_post_types();
	$unsupported = array( 'attachment', 'revision', 'nav_menu_item', 'custom_css', 'customize_changeset', 'oembed_cache', 'user_request', 'wp_block', 'scheduled-action', 'newsletterglue', 'ngl_pattern' );

	if ( is_array( $post_types ) ) {
		foreach ( $post_types as $post_type ) {
			$object = get_post_type_object( $post_type );
			if ( ! in_array( $post_type, apply_filters( 'newsletterglue_unsupported_post_types', $unsupported ) ) ) {
				$types[ $post_type ] = $object->labels->name;
			}
		}
	}

	return apply_filters( 'newsletterglue_get_post_types', $types );
}

/**
 * Get post types.
 *
 * @param string  $content A string containing the content.
 * @param integer $words_per_minute Number of words per minute.
 */
function newsletterglue_content_estimated_reading_time( $content = '', $words_per_minute = 150 ) {

	$clean_content = strip_shortcodes( $content );
	$clean_content = wp_strip_all_tags( $clean_content );
	$word_count    = str_word_count( $clean_content );
	$time          = ceil( $word_count / $words_per_minute );

	/* translators: %s: time */
	$output = sprintf( __( '%s mins', 'newsletter-glue' ), $time );

	return $output;
}

/**
 * Add theme designer css.
 */
function newsletterglue_add_theme_designer_css() {

	// If theme designer css is disabled.
	if ( get_option( 'newsletterglue_disable_plugin_css' ) == 1 ) {
		return;
	}

	if ( newsletterglue_get_theme_option( 'font' ) ) {
		$email_font = "'" . newsletterglue_get_font_name( newsletterglue_get_theme_option( 'font' ) ) . "'";
	} else {
		$email_font = 'Arial, Helvetica, sans-serif';
	}

	$align = newsletterglue_get_theme_option( 'p_align' );

	$code_size = newsletterglue_get_theme_option( 'p_size' ) - 1;

	if ( $code_size > 14 ) {
		$code_size = 14;
	}
	?>

.ExternalClass {width:100%;}

.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {
	line-height: 100%;
}

body {
	mso-line-height-rule: exactly;
	line-height: 150%;
	-webkit-text-size-adjust: none;
	-ms-text-size-adjust: none;
	margin: 0;
	padding: 0;
	background: <?php echo esc_attr( newsletterglue_get_theme_option( 'email_bg' ) ); ?> !important;
}

body, #wrapper, #template_inner {
	font-family: Arial, Helvetica, sans-serif;
	font-size: <?php echo esc_attr( newsletterglue_get_theme_option( 'p_size' ) ); ?>px;
	color: <?php echo esc_attr( newsletterglue_get_theme_option( 'p_colour' ) ); ?>;
}

#template_inner .wp-block-newsletterglue-embed a img {
	max-width: 100% !important;
}

#template_inner p,
#template_inner ul:not(.wp-block-newsletterglue-list), 
#template_inner ol:not(.wp-block-newsletterglue-list),
#template_inner li:not(.ng-block) {
	font-size: <?php echo esc_attr( newsletterglue_get_theme_option( 'p_size' ) ); ?>px;
}

#template_inner p,
#template_inner div {
	color: <?php echo esc_attr( newsletterglue_get_theme_option( 'p_colour' ) ); ?>;
}

#template_inner .ngl-article div {
	color: inherit;
}

span.yshortcuts { color: #000; background-color:none; border:none;}
span.yshortcuts:hover,
span.yshortcuts:active,
span.yshortcuts:focus {color: #000; background-color:none; border:none;}

table {
	mso-table-lspace: 0pt;
	mso-table-rspace: 0pt;
}

.ngl-table td {
	padding: 10px 20px;
}

.ngl-columns td.column {
	padding: 0;
}

.ngl-columns td.column h1:not(.has-text-color),
.ngl-columns td.column h2:not(.has-text-color),
.ngl-columns td.column h3:not(.has-text-color),
.ngl-columns td.column h4:not(.has-text-color),
.ngl-columns td.column h5:not(.has-text-color),
.ngl-columns td.column h6:not(.has-text-color),
.ngl-columns td.column p:not(.has-text-color) {
	color: inherit !important;
}

.ngl-columns td.column td {
	padding: 0;
}

.ngl-columns td.column > div {
	padding: 0;
}

.ngl-columns td.column > table {
	margin-bottom: 0;
}

.ngl-columns td.column > table:last-child {
	margin-bottom: 0;
}

.ngl-columns .root-tr td.column {
	padding-bottom: 0 !important;
}

.ngl-columns .root-tr .column > div:last-child {
	padding-bottom: 0 !important;
}

.ngl-table td div table td {
	padding: 10px 0;
}

.ngl-table-masthead td {
	padding: 10px 20px;
}

table.ngl-table-wp-block-code pre,
table.ngl-table-wp-block-code code,
#template_inner pre,
#template_inner code {
	font-family: monospace !important;
	white-space: pre-wrap !important;
	font-size: <?php echo esc_attr( $code_size ); ?>px;
	margin: 0;
	padding: 0;
	line-height: 1.2;
}

.ngl-table.ngl-table-wp-block-code td {
	padding: 20px;
}

.ngl-table-inline td {
	padding: 10px 20px;
}

.ngl-table-inline td a {
	display: inline-block;
}

.ngl-table-inline.align-center td td {
	padding: 0 8px !important;
}

.ngl-table-inline.align-left td td {
	padding: 0 12px 0 0 !important;
}

.ngl-quote p {
	margin-bottom: 15px;
}

.ngl-quote p:last-child {
	margin-bottom: 0;
}

.ngl-table-inline.align-right td td {
	padding: 0 0 0 12px !important;
}

.ngl-table-ngl-credits td {
	padding: 20px;
}

.ngl-table-caption td {
	padding-top: 0 !important;
	text-align: center;
	font-size: <?php echo esc_attr( newsletterglue_get_theme_option( 'p_size' ) ) - 3; ?>px;
	opacity: 0.7;
}

.ngl-table-callout .wp-block-newsletterglue-callout td {
	padding: 0;
}

.ngl-table-callout .wp-block-newsletterglue-callout .ngl-callout-content > table td {
	padding-bottom: 10px;
}

.ngl-table-callout .wp-block-newsletterglue-callout td table td {
	padding-bottom: 10px !important;
}

.ngl-table-callout .wp-block-newsletterglue-callout .ngl-callout-content > table.ngl-columns:last-child td {
	padding-bottom: 10px;
}

.ngl-table-callout .wp-block-newsletterglue-callout .ngl-callout-content > table:last-child td {
	padding-bottom: 0;
}

.ngl-table-posts > tr > td {
	padding: 10px 20px;
}

.ngl-article-img-full td {
	padding: 0;
}

a.ngl-metadata-permalink {
	color: inherit !important;
}

.ngl-table-article td {
	padding: 20px 10px !important;
}

.ngl-table-article td:first-child {
	padding-left: 20px !important;
}

.ngl-table-article td:last-child {
	padding-right: 20px !important;
}

.ngl-table-posts-pure .ngl-table-article td:first-child {
	padding-left: 0 !important;
}

.ngl-table-posts-pure .ngl-table-article td:last-child {
	padding-right: 0 !important;
}

.ngl-article-featured img {
	max-width: 100% !important;
	height: auto;
}

.ngl-table-posts-colored .ngl-article-mob-wrap {
	padding: 20px;
	margin: 0 0 10px;
}

.ngl-table-posts-colored .ngl-article {
	margin: 0 0 10px;
}

.ngl-table-posts-colored div.ngl-article-img-full {
	padding: 20px !important;
}

.ngl-table-posts-pure .ngl-article-mob-wrap {
	margin: 0 0 20px;
}

.ngl-table-posts-pure div.ngl-article-img-full {
	margin: 0 0 20px;
}

	<?php
	$sizes = get_option( 'newsletterglue_theme_sizes' );
	if ( ! empty( $sizes ) ) {
		foreach ( $sizes as $key => $size ) {
			$slug  = $size->slug;
			$value = $size->size;
			echo esc_attr( sprintf( '.has-%s-font-size { font-size: %s !important; }', $slug, $value ) );
		}
	}

	$colors = get_option( 'newsletterglue_theme_colors' );
	if ( ! empty( $colors ) ) {
		foreach ( $colors as $key => $color ) {
			$slug  = $color->slug;
			$color = $color->color;
			echo esc_attr( sprintf( '.has-%1$s-color, a.has-%1$s-color { color: %2$s !important; }', $slug, $color ) );
			echo esc_attr( sprintf( '.has-%1$s-background-color { background-color: %2$s !important; }', $slug, $color ) );
			echo esc_attr( sprintf( 'a.has-%1$s-background-color { background-color: %2$s !important; }', $slug, $color ) );
			echo esc_attr( sprintf( 'a.has-%1$s-background-color { border-color: %2$s !important; }', $slug, $color ) );
			echo esc_attr( sprintf( '.wp-block-button.is-style-outline a.has-%1$s-background-color { color: %2$s !important; }', $slug, $color ) );
		}
	}

	echo '.has-luminous-vivid-orange-color { color: #ff6900 !important; }';
	echo '.has-vivid-red-color { color: #cf2e2e !important; }';
	echo '.has-pale-pink-color { color: #f78da7 !important; }';
	echo '.has-cyan-bluish-gray-color { color: #abb8c3 !important; }';
	echo '.has-luminous-vivid-amber-color { color: #fcb900 !important; }';
	echo '.has-light-green-cyan-color { color: #7bdcb5 !important; }';
	echo '.has-vivid-green-cyan-color { color: #00d084 !important; }';
	echo '.has-pale-cyan-blue-color { color: #8ed1fc !important; }';
	echo '.has-vivid-cyan-blue-color { color: #0693e3 !important; }';
	echo '.has-vivid-purple-color { color: #9b51e0 !important; }';

	echo '.has-luminous-vivid-orange-background-color { background-color: #ff6900 !important; }';
	echo '.has-vivid-red-background-color { background-color: #cf2e2e !important; }';
	echo '.has-pale-pink-background-color { background-color: #f78da7 !important; }';
	echo '.has-cyan-bluish-gray-background-color { background-color: #abb8c3 !important; }';
	echo '.has-luminous-vivid-amber-background-color { background-color: #fcb900 !important; }';
	echo '.has-light-green-cyan-background-color { background-color: #7bdcb5 !important; }';
	echo '.has-vivid-green-cyan-background-color { background-color: #00d084 !important; }';
	echo '.has-pale-cyan-blue-background-color { background-color: #8ed1fc !important; }';
	echo '.has-vivid-cyan-blue-background-color { background-color: #0693e3 !important; }';
	echo '.has-vivid-purple-background-color { background-color: #9b51e0 !important; }';

	$credits_font_size = newsletterglue_get_theme_option( 'mobile_p_size' );

	?>

.ngl-table-ngl-unsubscribe td {
	border-top: 1px solid #eee;
	padding: 20px 100px;
}

.ngl-table-columns {
	table-layout: fixed;
}

table.ngl-columns {
	table-layout: auto;
}

a {
	color: #2A5DB0;
	text-decoration: underline;
}

hr {
	margin: 0;
	height: 1px;
	background-color: #ddd;
	color: #ddd;
	font-size: 0;
	border: 0;
}

#wrapper {
	background: <?php echo esc_attr( newsletterglue_get_theme_option( 'email_bg' ) ); ?>;
	padding: 0;
	padding-top: <?php echo absint( newsletterglue_get_theme_option( 'container_margin1' ) ); ?>px;
	padding-bottom: <?php echo absint( newsletterglue_get_theme_option( 'container_margin2' ) ); ?>px;
	<?php if ( newsletterglue_get_theme_option( 'font' ) ) : ?>
	font-family: <?php echo wp_kses_post( $email_font ); ?>;
	<?php endif; ?>
	<?php if ( ! newsletterglue_get_theme_option( 'font' ) && ( isset( $_GET['preview_email'] ) || isset( $_GET['view_newsletter'] ) ) ) : // phpcs:ignore ?>
	font-family: Arial, Helvetica, sans-serif;
	<?php endif; ?>
}

	<?php
	$tags = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p' );
	foreach ( $tags as $tag ) {
		$has_custom_font = newsletterglue_get_theme_option( $tag . '_font' );
		if ( ! empty( $has_custom_font ) ) {
			?>
			#template_inner <?php echo esc_attr( $tag ); ?> {
				font-family: <?php echo esc_attr( newsletterglue_get_font_name( $has_custom_font ) ); ?>;
			}
			<?php
		}
	}
	?>

#template_inner {
	background: <?php echo esc_attr( newsletterglue_get_theme_option( 'container_bg' ) ); ?>;
	box-sizing: border-box;
	padding-left: 0;
	padding-right: 0;
	padding-top: <?php echo absint( newsletterglue_get_theme_option( 'container_padding1' ) ); ?>px;
	padding-bottom: <?php echo absint( newsletterglue_get_theme_option( 'container_padding2' ) ); ?>px;
}

h1, h2, h3, h4, h5, h6 {
	color: black;
	padding: 0 !important;
	margin: 0;
	line-height: 120%;
}

p {
	mso-line-height-rule: exactly;
}

img.wp-image {
	height: auto;
}

.wp-block-columns {
	margin: 0 !important;
}

.wp-block-columns h1,
.wp-block-columns h2,
.wp-block-columns h3,
.wp-block-columns h4,
.wp-block-columns h5,
.wp-block-columns h6 {
	margin-top: 0 !important;
}

h1, div.ngl-lp-content h1 { font-size: <?php echo esc_attr( newsletterglue_get_theme_option( 'h1_size' ) ); ?>px; color: <?php echo esc_attr( newsletterglue_get_theme_option( 'h1_colour' ) ); ?>; text-align: <?php echo esc_attr( newsletterglue_get_theme_option( 'h1_align' ) ); ?>; }
h2, div.ngl-lp-content h2 { font-size: <?php echo esc_attr( newsletterglue_get_theme_option( 'h2_size' ) ); ?>px; color: <?php echo esc_attr( newsletterglue_get_theme_option( 'h2_colour' ) ); ?>; text-align: <?php echo esc_attr( newsletterglue_get_theme_option( 'h2_align' ) ); ?>; }
h3, div.ngl-lp-content h3 { font-size: <?php echo esc_attr( newsletterglue_get_theme_option( 'h3_size' ) ); ?>px; color: <?php echo esc_attr( newsletterglue_get_theme_option( 'h3_colour' ) ); ?>; text-align: <?php echo esc_attr( newsletterglue_get_theme_option( 'h3_align' ) ); ?>; }
h4, div.ngl-lp-content h4 { font-size: <?php echo esc_attr( newsletterglue_get_theme_option( 'h4_size' ) ); ?>px; color: <?php echo esc_attr( newsletterglue_get_theme_option( 'h4_colour' ) ); ?>; text-align: <?php echo esc_attr( newsletterglue_get_theme_option( 'h4_align' ) ); ?>; }
h5, div.ngl-lp-content h5 { font-size: <?php echo esc_attr( newsletterglue_get_theme_option( 'h5_size' ) ); ?>px; color: <?php echo esc_attr( newsletterglue_get_theme_option( 'h5_colour' ) ); ?>; text-align: <?php echo esc_attr( newsletterglue_get_theme_option( 'h5_align' ) ); ?>; }
h6, div.ngl-lp-content h6 { font-size: <?php echo esc_attr( newsletterglue_get_theme_option( 'h6_size' ) ); ?>px; color: <?php echo esc_attr( newsletterglue_get_theme_option( 'h6_colour' ) ); ?>; text-align: <?php echo esc_attr( newsletterglue_get_theme_option( 'h6_align' ) ); ?>; }

h1 a.ngl-title-to-post {
	text-decoration: none;
	color: <?php echo esc_attr( newsletterglue_get_theme_option( 'h1_colour' ) ); ?> !important;
}

p, ul, ol {
	padding: 0;
	margin: 0;
}

ul li:not(.ng-block),
ol li:not(.ng-block) {
	margin-bottom: 8px !important;
	padding: 0 !important;
}

	<?php if ( $align && 'left' != $align ) { ?>
	p, ul, ol, li {
		text-align: <?php echo esc_attr( newsletterglue_get_theme_option( 'p_align' ) ); ?>;
	}
	<?php } ?>

blockquote {
	margin: 0;
}

blockquote p {
	text-align: inherit !important;
}

a {
	color: <?php echo esc_attr( newsletterglue_get_theme_option( 'a_colour' ) ); ?>;
}

figure {
	margin: 0;
	width: auto !important;
}

figcaption {
	font-size: 14px;
	opacity: 0.7;
	margin-top: 10px;
}

#template_inner img:not(.ng-image) {
	max-width: 100%;
	display: block;
}

#template_inner img.ng-image {
	display: block;
}

h1 img,
h2 img,
h3 img,
h4 img,
h5 img,
h6 img,
p img {
	margin: auto;
	display: inline-block;
}

ul.blocks-gallery-grid {
	list-style-type: none;
}

#template_inner .wp-block-table table {
	width: 100%;
	border: 0;
	border-collapse: collapse;
}

#template_inner .wp-block-table td td {
	border-bottom: 1px solid #eee;
	padding: 10px;
}

#template_inner td table img {
	margin: 0;
}

#template_inner td.ngl-td-auto {
	border: 0;
	font-size: inherit !important;
}

p.ngl-credits,
p.ngl-unsubscribe {
	font-size: <?php echo esc_attr( newsletterglue_get_theme_option( 'p_size' ) ) - 2; ?>px;
	text-align: center;
	color: #707070 !important;
}

p.ngl-credits a,
p.ngl-unsubscribe a {
	color: #707070 !important;
	text-decoration: underline !important;
}

td.column .wp-block-button {
	padding-left: 0 !important;
	padding-right: 0 !important;
}

.wp-block-buttons .wp-block-button {
	display: inline-block !important;
	padding: 0 !important;
}

.wp-block-button.aligncenter,
.wp-block-buttons.aligncenter,
.wp-block-calendar {
	text-align: center;
}

.aligncenter img {
	margin-left: auto !important;
	margin-right: auto !important;
}

.wp-block-button__link {
	mso-hide: all;
	display: inline-block;
	text-align: center !important;
	box-sizing: border-box;
	padding: 11px 20px;
	text-decoration: none;
	color: <?php echo esc_attr( newsletterglue_get_theme_option( 'btn_colour' ) ); ?> !important;
	min-width: <?php echo (int) newsletterglue_get_theme_option( 'btn_width' ); ?>px !important;
	border-width: 1px;
	border-style: solid;
	border-radius: <?php echo (int) newsletterglue_get_theme_option( 'btn_radius' ); ?>px;
}

.wp-block-button__link.has-background {

}

.wp-block-button__link:not(.has-background) {
	background-color: <?php echo esc_attr( newsletterglue_get_theme_option( 'btn_bg' ) ); ?> !important;
	border: 1px solid <?php echo esc_attr( newsletterglue_get_theme_option( 'btn_border' ) ); ?> !important;
}

.wp-block-button.wp-block-button__width-100 {
	width: 100% !important;
	padding: 0 !important;
}

.wp-block-button.wp-block-button__width-100 .wp-block-button__link {
	width: 100% !important;
}

.wp-block-button.is-style-outline .wp-block-button__link {
	background-color: transparent !important;
	border-width: 2px !important;
	padding: 10px 24px;
}

.wp-block-column img {
	width: 100% !important;
	max-width: 100% !important;
	max-height: 100% !important;
	height: auto !important;
}

.ngl-hide-in-email {
	display: none !important;
	visibility: hidden !important;
}

#template_inner img.logo-image {
	margin: 0 !important;
	display: block !important;
}

.is-content-justification-left td { text-align: left; }
.is-content-justification-center td { text-align: center; }
.is-content-justification-right td { text-align: right; }

.wp-block-buttons.is-content-justification-left .wp-block-button,
.wp-block-buttons .wp-block-button {
	margin-right: 10px;
	margin-left: 0;
}

.wp-block-buttons.is-content-justification-center .wp-block-button {
	margin: 0 10px;
}

.wp-block-buttons.is-content-justification-right .wp-block-button {
	margin-left: 10px;
	margin-right: 0;
}

.ngl-table-has-text-align-left td { text-align: left !important; }
.ngl-table-has-text-align-center td { text-align: center !important; }
.ngl-table-has-text-align-right td { text-align: right !important; }

.has-text-align-left { text-align: left !important; }
.has-text-align-center { text-align: center !important; }
.has-text-align-right { text-align: right !important; }

.ngl-table-ngl-embed-social > tr > td {
	padding: 20px;
}

.ngl-embed-meta td {
	padding: 20px !important;
}

@media screen and (max-width:642px) {

	#template_table {
		width: 95% !important;
		max-width: 95% !important;
	}

	#template_inner table.ngl-table-columns {
		max-width: 100% !important;
		width: 100% !important;
	}

	#template_inner img.ng-standard-img,
	#template_inner .jeeng img {
		max-width: 100% !important;
		width: 100% !important;
		height: auto !important;
	}

	#template_inner .ngl-article-featured img.ng-standard-img {
		max-width: 100% !important;
		width: 100% !important;
		height: auto !important;
	}

	#template_inner hr {
		max-width: 100% !important;
		width: auto !important;
	}

	#template_inner {
		width: auto;
		padding-top: <?php echo absint( newsletterglue_get_theme_option( 'mobile_container_padding1' ) ); ?>px !important;
		padding-bottom: <?php echo absint( newsletterglue_get_theme_option( 'mobile_container_padding2' ) ); ?>px !important;
	}

	#wrapper {
		padding-top: <?php echo absint( newsletterglue_get_theme_option( 'mobile_container_margin1' ) ); ?>px !important;
		padding-bottom: <?php echo absint( newsletterglue_get_theme_option( 'mobile_container_margin2' ) ); ?>px !important;
	}

	#template_inner .wp-block-button__link {
		min-width: <?php echo (int) newsletterglue_get_theme_option( 'mobile_btn_width' ); ?>px !important;
	}

	#template_inner img.logo-image {
		max-width: <?php echo esc_attr( ngl_get_max_logo_width( true ) ); ?> !important;
		height: auto !important;
	}

	body, #wrapper, #template_inner {
		font-size: <?php echo esc_attr( newsletterglue_get_theme_option( 'mobile_p_size' ) ); ?>px;
	}

	#template_inner p,
	#template_inner ul, 
	#template_inner ol,
	#template_inner li {
		font-size: <?php echo esc_attr( newsletterglue_get_theme_option( 'mobile_p_size' ) ); ?>px;
	}

	#template_inner p.ngl-credits,
	#template_inner p.ngl-unsubscribe {
		font-size: <?php echo esc_attr( newsletterglue_get_theme_option( 'mobile_p_size' ) ); ?>px !important;
	}

	h1, div.ngl-lp-content h1 { font-size: <?php echo esc_attr( newsletterglue_get_theme_option( 'mobile_h1_size' ) ); ?>px !important; }
	h2, div.ngl-lp-content h2 { font-size: <?php echo esc_attr( newsletterglue_get_theme_option( 'mobile_h2_size' ) ); ?>px !important; }
	h3, div.ngl-lp-content h3 { font-size: <?php echo esc_attr( newsletterglue_get_theme_option( 'mobile_h3_size' ) ); ?>px !important; }
	h4, div.ngl-lp-content h4 { font-size: <?php echo esc_attr( newsletterglue_get_theme_option( 'mobile_h4_size' ) ); ?>px !important; }
	h5, div.ngl-lp-content h5 { font-size: <?php echo esc_attr( newsletterglue_get_theme_option( 'mobile_h5_size' ) ); ?>px !important; }
	h6, div.ngl-lp-content h6 { font-size: <?php echo esc_attr( newsletterglue_get_theme_option( 'mobile_h6_size' ) ); ?>px !important; }

	.ngl-table-columns td.column {
		display: block !important;
		float: none !important;
		width: 100% !important;
		clear: both !important;
		box-sizing: border-box !important;
		padding-bottom: 10px !important;
	}

	.wp-block-newsletterglue-sections.is-stacked-on-mobile td.wp-block-newsletterglue-section {
		display: block !important;
		float: none !important;
		width: 100% !important;
		clear: both !important;
		box-sizing: border-box !important;
	}

	table.ng-posts-wrapper.is-stacked table.ngl-table-latest-posts td {
		display: block !important;
		float: none !important;
		width: 100% !important;
		clear: both !important;
		box-sizing: border-box !important;
		padding-top: 0px !important;
		padding-bottom: 0px !important;
	}
	table.ng-posts-wrapper.is-stacked table.ngl-table-latest-posts td.ng-td-spaced {
		padding-top: 2px !important;
		padding-bottom: 2px !important;
	}

	table.ngl-table-latest-posts td img {
		width: auto !important;
	}

	table.ngl-table-latest-posts table {
		padding-bottom: 0px !important;
	}

	.ngl-table-columns td.column-no-wrap {
		width: auto !important;
		box-sizing: border-box !important;
	}

	.ngl-table-ngl-unsubscribe td {
		padding: 20px !important;
	}

	.wp-block-newsletterglue-optin.is-landscape .ngl-form-wrap {
		flex-direction: column !important;
		align-items: initial !important;
	}

	.ng-block-button__link {
		width: auto !important;
		max-width: 100% !important;
	}
}

	<?php
}
add_action( 'newsletterglue_email_styles', 'newsletterglue_add_theme_designer_css', 10 );

/**
 * Add preview text CSS.
 */
function newsletterglue_add_preview_text_css() {
	?>
	.ngl-preview-text {
		display: none;
		font-size: 1px;
		line-height: 1px;
		max-height: 0px;
		max-width: 0px;
		opacity: 0;
		overflow: hidden;
		mso-hide: all;
		font-family: sans-serif;
	}
	<?php
}
add_action( 'newsletterglue_email_styles', 'newsletterglue_add_preview_text_css', 20 );

/**
 * Add custom CSS.
 *
 * @param object $post This is post object.
 */
function newsletterglue_add_custom_css( $post ) {

	$custom_css = get_option( 'newsletterglue_css' );

	if ( isset( $post->ID ) && empty( $_GET['post_type'] ) ) {
		$has_css = get_post_meta( $post->ID, '_newsletterglue_css', true );
		if ( $has_css ) {
			$custom_css = $has_css;
		}
	}

	$css = wp_strip_all_tags( $custom_css );

	echo wp_kses_post( $css );
}
add_action( 'newsletterglue_add_custom_styles', 'newsletterglue_add_custom_css', 100 );

/**
 * Remove a div by class from html.
 *
 * @param string $html The html provided.
 * @param string $class The class name to be removed.
 */
function newsletterglue_remove_div( $html, $class ) {

	if ( ! $html ) {
		return $html;
	}

	$dom = new \DOMDocument();

	libxml_use_internal_errors( true );

	$html = htmlspecialchars_decode( mb_convert_encoding( htmlentities( $html, ENT_COMPAT, 'utf-8', false ), 'UTF-8', mb_list_encodings() ) );
	$dom->loadHTML( $html );

	libxml_clear_errors();

	$finder = new \DOMXPath( $dom );

	$nodes = $finder->query( "//*[contains(concat(' ', normalize-space(@class), ' '), ' {$class} ')]" );

	foreach ( $nodes as $node ) {
		$node->parentNode->removeChild( $node ); // phpcs:ignore
	}

	return $dom->saveHTML();
}

/**
 * Get tier.
 */
function newsletterglue_get_tier() {

	$tier = false;

	$license = get_option( 'newsletterglue_pro_license' );

	if ( ! $license ) {
		return false;
	}

	$data = get_option( 'newsletterglue_license_info' );

	$tier = get_option( 'newsletterglue_pro_license_priceid' );

	if ( ! isset( $data->price_id ) && empty( $tier ) ) {
		return false;
	}

	if ( $tier ) {
		$price_id = $tier;
	} else {
		$price_id = $data->price_id;
	}

	switch ( $price_id ) {
		case 1:
		case 9:
			$tier = 'new_pro';
			break;
		case 3:
		case 24:
			$tier = 'new_basic';
			break;
		/* MONTHLY */
		case 1:
		case 2:
		case 4:
		case 5:
		case 7:
		case 10:
		case 11:
		case 12:
		/* YEARLY */
		case 9:
		case 24:
		case 27:
		case 28:
			$tier = 'check_new_pro';
			break;
		/* MONTHLY */
		case 3:
		case 6:
		case 13:
		case 24:
		/* YEARLY */
		case 10:
		case 11:
		case 12:
		case 13:
		case 14:
		case 15:
		case 16:
		case 17:
		case 21:
		case 22:
		case 23:
		case 25:
		case 26:
			$tier = 'check_new_basic';
			break;
		case 9:
		case 23:
			$tier = 'newsroom';
			break;
		case 5:
			$tier = 'friends';
			break;
		case 4:
			$tier = 'founding';
			break;
		case 3:
		case 8:
		case 24:
			$tier = 'writer';
			break;
		case 2:
		case 7:
			$tier = 'publisher';
			break;
		case 1:
		case 6:
			$tier = 'agency';
			break;
		case 10:
		case 11:
		case 12:
		case 13:
		case 21:
			$tier = 'writer_new';
			break;
		case 14:
		case 15:
		case 16:
		case 17:
		case 22:
			$tier = 'publisher_new';
			break;
	}

	if ( strstr( $license, 'ngm_' ) ) {
		$tier = 'newsroom';
	}

	return $tier;
}

/**
 * Get tier.
 *
 * @param string $tier The license tier short name.
 */
function newsletterglue_get_tier_name( $tier = '' ) {

	if ( empty( $tier ) ) {
		$tier = newsletterglue_get_tier();
	}

	if ( strstr( $tier, '_new' ) ) {
		$tier = str_replace( '_new', '', $tier );
	}

	$tier_name = ucfirst( $tier );

	return $tier_name;
}

/**
 * Duplicate a custom post or item.
 *
 * @param object  $post This is a post object.
 * @param integer $post_id This is a post ID.
 */
function newsletterglue_duplicate_a_pattern( $post = null, $post_id = 0 ) {
	global $wpdb;

	$content = $post->post_content;

	// Check if content has the post embed block first.
	if ( strstr( $content, '<!-- wp:newsletterglue/article' ) ) {
		preg_match_all( '<!-- wp:newsletterglue/article (.*?) /-->', $content, $blocks );
		if ( ! empty( $blocks[1] ) ) {
			$found = $blocks[1];
			foreach ( $found as $key => $data ) {
				$split    = explode( '"block_id":"', $data );
				$split2   = explode( '"', $split[1] );
				$code     = str_replace( '-', '', $split2[0] );
				$embed    = get_option( 'ngl_articles_' . $code );
				$new_code = uniqid();
				update_option( 'ngl_articles_' . $new_code, $embed );
				$content = str_replace( $split2[0], $new_code, $content );
			}
		}
	}

	$content = apply_filters( 'newsletterglue_duplicate_post_content', $content, $post );

	$args = array(
		'comment_status' => $post->comment_status,
		'ping_status'    => $post->ping_status,
		'post_author'    => $post->post_author,
		'post_content'   => $content,
		'post_excerpt'   => $post->post_excerpt,
		'post_name'      => $post->post_name,
		'post_parent'    => $post->post_parent,
		'post_password'  => $post->post_password,
		'post_status'    => $post->post_status,
		'post_title'     => $post->post_title,
		'post_type'      => $post->post_type,
		'to_ping'        => $post->to_ping,
		'menu_order'     => $post->menu_order,
	);

	/*
	 * insert the post
	 */
	$new_post_id = wp_insert_post( $args );

	/*
	 * get all current post terms ad set them to the new post draft
	 */
	$taxonomies = get_object_taxonomies( $post->post_type );
	foreach ( $taxonomies as $taxonomy ) {
		$post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
		wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
	}

	/*
	 * duplicate all post meta just in two SQL queries
	 */
	$post_meta_infos = get_post_meta( $post_id );
	if ( ! empty( $post_meta_infos ) ) {
		foreach ( $post_meta_infos as $old_key => $old_value ) {
			update_post_meta( $new_post_id, $old_key, get_post_meta( $post_id, $old_key, true ) );
		}
	}

	// Unsend the cloned newsletter.
	$meta = get_post_meta( $new_post_id, '_newsletterglue', true );
	if ( ! empty( $meta ) && isset( $meta['sent'] ) ) {
		unset( $meta['sent'] );
		update_post_meta( $new_post_id, '_newsletterglue', $meta );
	}

	delete_post_meta( $new_post_id, '_ngl_results' );

	delete_post_meta( $new_post_id, '_ngl_core_pattern' );
	delete_post_meta( $new_post_id, '_ngl_core_template' );

	do_action( 'newsletterglue_after_pattern_duplicate', $new_post_id, $post_id );

	return $new_post_id;
}

/**
 * Duplicate a custom post or item.
 *
 * @param object  $post This is a post object.
 * @param integer $post_id This is a post ID.
 */
function newsletterglue_duplicate_item( $post = null, $post_id = 0 ) {
	global $wpdb;

	$content = $post->post_content;

	// Check if content has the post embed block first.
	if ( strstr( $content, '<!-- wp:newsletterglue/article' ) ) {
		preg_match_all( '<!-- wp:newsletterglue/article (.*?) /-->', $content, $blocks );
		if ( ! empty( $blocks[1] ) ) {
			$found = $blocks[1];
			foreach ( $found as $key => $data ) {
				$split    = explode( '"block_id":"', $data );
				$split2   = explode( '"', $split[1] );
				$code     = str_replace( '-', '', $split2[0] );
				$embed    = get_option( 'ngl_articles_' . $code );
				$new_code = uniqid();
				update_option( 'ngl_articles_' . $new_code, $embed );
				$content = str_replace( $split2[0], $new_code, $content );
			}
		}
	}

	$content = apply_filters( 'newsletterglue_duplicate_post_content', $content, $post );

	$args = array(
		'comment_status' => $post->comment_status,
		'ping_status'    => $post->ping_status,
		'post_author'    => $post->post_author,
		'post_content'   => $content,
		'post_excerpt'   => $post->post_excerpt,
		'post_name'      => $post->post_name,
		'post_parent'    => $post->post_parent,
		'post_password'  => $post->post_password,
		'post_status'    => $post->post_status,
		/* translators: %s: post title */
		'post_title'     => sprintf( __( 'Copy of %s', 'newsletter-glue' ), $post->post_title ),
		'post_type'      => $post->post_type,
		'to_ping'        => $post->to_ping,
		'menu_order'     => $post->menu_order,
	);

	/*
	 * insert the post
	 */
	$new_post_id = wp_insert_post( $args );

	/*
	 * get all current post terms ad set them to the new post draft
	 */
	$taxonomies = get_object_taxonomies( $post->post_type );
	foreach ( $taxonomies as $taxonomy ) {
		$post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
		wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
	}

	/*
	 * duplicate all post meta just in two SQL queries
	 */
	$post_meta_infos = get_post_meta( $post_id );
	if ( ! empty( $post_meta_infos ) ) {
		foreach ( $post_meta_infos as $old_key => $old_value ) {
			update_post_meta( $new_post_id, $old_key, get_post_meta( $post_id, $old_key, true ) );
		}
	}

	// Unsend the cloned newsletter.
	$meta = get_post_meta( $new_post_id, '_newsletterglue', true );
	if ( ! empty( $meta ) && isset( $meta['sent'] ) ) {
		unset( $meta['sent'] );
		update_post_meta( $new_post_id, '_newsletterglue', $meta );
	}

	delete_post_meta( $new_post_id, '_ngl_results' );

	delete_post_meta( $new_post_id, '_ngl_core_pattern' );
	delete_post_meta( $new_post_id, '_ngl_core_template' );

	return $new_post_id;
}

/**
 * Get rgb from hex.
 *
 * @param string $color A color value.
 */
function newsletterglue_rgb_from_hex( $color ) {
	$color = str_replace( '#', '', $color );
	// Convert shorthand colors to full format, e.g. "FFF" -> "FFFFFF".
	$color = preg_replace( '~^(.)(.)(.)$~', '$1$1$2$2$3$3', $color );

	$rgb      = array();
	$rgb['R'] = hexdec( $color[0] . $color[1] );
	$rgb['G'] = hexdec( $color[2] . $color[3] );
	$rgb['B'] = hexdec( $color[4] . $color[5] );

	return $rgb;
}

/**
 * Darker hex color.
 *
 * @param string  $color The color value.
 * @param integer $factor This is the differential factor.
 */
function newsletterglue_hex_darker( $color, $factor = 30 ) {
	$base  = newsletterglue_rgb_from_hex( $color );
	$color = '#';

	foreach ( $base as $k => $v ) {
		$amount      = $v / 100;
		$amount      = round( $amount * $factor );
		$new_decimal = $v - $amount;

		$new_hex_component = dechex( $new_decimal );
		if ( strlen( $new_hex_component ) < 2 ) {
			$new_hex_component = '0' . $new_hex_component;
		}
		$color .= $new_hex_component;
	}

	return $color;
}

/**
 * Send test emails with empty subject lines.
 *
 * @param string  $subject The email subject line.
 * @param object  $post The post object.
 * @param array   $data the data array.
 * @param boolean $test Whether this is a test email.
 * @param object  $email The email object.
 */
function newsletterglue_allow_empty_subject_in_test( $subject, $post, $data, $test, $email ) {

	if ( empty( $subject ) && $test ) {
		$subject = 'Subject line';
	}

	// Convert tags in subject.
	if ( method_exists( $email, 'convert_tags' ) ) {
		$subject = $email->convert_tags( $subject, $post->ID );
	}

	return $subject;
}
add_filter( 'newsletterglue_email_subject_line', 'newsletterglue_allow_empty_subject_in_test', 20, 5 );

/**
 * Get the template assets URI.
 */
function newsletterglue_template_assets() {
	return apply_filters( 'newsletterglue_template_assets', NGL_PLUGIN_URL . 'assets/images/templates' );
}

/**
 * Get the social assets URI.
 */
function newsletterglue_social_assets() {
	return apply_filters( 'newsletterglue_template_assets', NGL_PLUGIN_URL . 'assets/images/share' );
}

add_action( 'save_post', 'newsletterglue_save_post_title', 10, 3 );

function newsletterglue_save_post_title( $post_id, $post, $update ) {
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}

	if ( has_block( 'newsletterglue/latest-posts', $post->post_content ) ) {
		$blocks = parse_blocks( $post->post_content );
		$first_post_title = '';
		foreach ( $blocks as $block ) {
			if ( isset( $block['blockName'] ) && $block['blockName'] === 'newsletterglue/latest-posts' && isset( $block['attrs']['posts'][0]['post_title'] ) ) {
				$first_post_title = wp_kses_post( $block['attrs']['posts'][0]['post_title'] );
				break;
			}
		}
		update_post_meta( $post_id, 'newsletterglue_latest_post_title_inside', $first_post_title );
	}
}
