<?php
/**
 * Form.
 *
 * @package Newsletter Glue.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * NGL_Abstract_Integration class.
 */
abstract class NGL_Abstract_Integration {

	/**
	 * App name.
	 *
	 * @var $app
	 */
	public $app = '';

	/**
	 * Forced state.
	 *
	 * @var $forced_state
	 */
	public $forced_state = '';

	/**
	 * Get settings.
	 */
	public function get_settings() {

		$settings = new stdclass();

		return $settings;
	}

	/**
	 * Simply testing connection.
	 *
	 * @param string $app     The ESP name.
	 * @param string $api_key API key for ESP.
	 */
	public function already_integrated( $app, $api_key ) {
		$esps = get_option( 'newsletterglue_integrations' );
		if ( isset( $esps ) && isset( $esps[ $app ] ) ) {
			if ( isset( $esps[ $app ]['api_key'] ) && $esps[ $app ]['api_key'] == $api_key ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Set content type as HTML.
	 */
	public function wp_mail_content_type() {
		return 'text/html';
	}

	/**
	 * Get email verify help link.
	 */
	public function get_email_verify_help() {
		return 'https://newsletterglue.com/docs/from-email-use-verified-email-address/';
	}

	/**
	 * Display settings.
	 *
	 * @param array $settings Array of settings.
	 * @param array $defaults Array of defaults.
	 * @param array $post     Post object.
	 */
	public function show_settings( $settings, $defaults, $post ) {
		$this->show_from_options( $settings, $defaults, $post );
		$this->show_test_email( $settings, $defaults, $post );
		$this->show_schedule_and_image_options( $settings, $defaults, $post );
	}

	/**
	 * Show test email options.
	 *
	 * @param array $settings Array of settings.
	 * @param array $defaults Array of defaults.
	 * @param array $post     Post object.
	 */
	public function show_test_email( $settings, $defaults, $post ) {
		$this->test_column( $settings, $defaults, $post );
		include NGL_PLUGIN_DIR . 'includes/admin/metabox/views/send-test.php';
	}

	/**
	 * Show test column.
	 *
	 * @param array $settings Array of settings.
	 * @param array $defaults Array of defaults.
	 * @param array $post     Post object.
	 */
	public function test_column( $settings, $defaults, $post ) {
		?>
		<div class="ngl-metabox-flex ngl-metabox-test-email">

			<div class="ngl-metabox-flex">

				<div class="ngl-metabox-header">
					<label for="ngl_test_email"><?php esc_html_e( 'Send test email', 'newsletter-glue' ); ?></label>
				</div>

				<div class="ngl-field">
					<?php
						newsletterglue_text_field(
							array(
								'id'    => 'ngl_test_email',
								'value' => isset( $settings->test_email ) ? $settings->test_email : $defaults->test_email,
							)
						);
					?>
					<div class="ngl-action">
						<button class="ui primary button ngl-test-email ngl-is-default" data-post_id="<?php echo esc_attr( $post->ID ); ?>"><?php esc_html_e( 'Send', 'newsletter-glue' ); ?></button>
						<button class="ui primary button ngl-test-email ngl-alt ngl-is-sending" data-post_id="<?php echo esc_attr( $post->ID ); ?>"><svg class="ngl-infinite-spinner" stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg><?php esc_html_e( 'Sending', 'newsletter-glue' ); ?></button>
						<button class="ui primary button ngl-test-email ngl-alt ngl-is-valid" data-post_id="<?php echo esc_attr( $post->ID ); ?>"><?php esc_html_e( 'Sent!', 'newsletter-glue' ); ?></button>
						<button class="ui primary button ngl-test-email ngl-alt ngl-is-invalid" data-post_id="<?php echo esc_attr( $post->ID ); ?>"><?php esc_html_e( 'Could not send', 'newsletter-glue' ); ?></button>
					</div>
				</div>

			</div>

			<div class="ngl-metabox-flex">
				<div class="ngl-metabox-flex-link">
					<a href="<?php echo esc_url( add_query_arg( 'preview_email', $post->ID, get_preview_post_link() ) ); ?>" target="_blank" class="ngl-email-preview-button"><?php esc_html_e( 'Preview email in browser', 'newsletter-glue' ); ?><span>(<?php esc_html_e( 'opens in new tab', 'newsletter-glue' ); ?>)</span></a>
				</div>
			</div>

		</div>
		<?php
	}

	/**
	 * Show subject.
	 *
	 * @param array $settings Array of settings.
	 * @param array $defaults Array of defaults.
	 * @param array $post     Post object.
	 */
	public function show_subject( $settings, $defaults, $post ) {
		global $post_type, $automation;

		if ( in_array( $post_type, newsletterglue_get_core_cpts() ) && ! defined( 'NEWSLETTERGLUE_DEMO' ) ) {
			return;
		}

		include NGL_PLUGIN_DIR . 'includes/admin/metabox/views/subject-line.php';
	}

	/**
	 * Show from name/email options.
	 *
	 * @param array $settings Array of settings.
	 * @param array $defaults Array of defaults.
	 * @param array $post     Post object.
	 */
	public function show_from_options( $settings, $defaults, $post ) {
		global $post_type;

		$app = newsletterglue_default_connection();

		if ( in_array( $post_type, newsletterglue_get_core_cpts() ) && ! defined( 'NEWSLETTERGLUE_DEMO' ) ) {
			return;
		}

		if ( defined( 'NEWSLETTERGLUE_DEMO' ) && ( empty( $app ) || 'core' === $app ) ) {
			return;
		}

		include NGL_PLUGIN_DIR . 'includes/admin/metabox/views/send-from-settings.php';
	}

	/**
	 * Show email verification info.
	 */
	public function email_verification_info() {
		if ( $this->has_email_verify() ) :
			?>
		<div class="ngl-label-verification">
			<span class="ngl-process ngl-ajax is-hidden is-waiting">
				<span class="ngl-process-icon"><svg class="ngl-infinite-spinner" stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg></span>
				<span class="ngl-process-text"><strong><?php esc_html_e( 'Verifying...', 'newsletter-glue' ); ?></strong></span>
			</span>

			<span class="ngl-process ngl-ajax is-hidden is-valid">
				<span class="ngl-process-icon"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 1024 1024" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M512 64C264.6 64 64 264.6 64 512s200.6 448 448 448 448-200.6 448-448S759.4 64 512 64zm193.5 301.7l-210.6 292a31.8 31.8 0 0 1-51.7 0L318.5 484.9c-3.8-5.3 0-12.7 6.5-12.7h46.9c10.2 0 19.9 4.9 25.9 13.3l71.2 98.8 157.2-218c6-8.3 15.6-13.3 25.9-13.3H699c6.5 0 10.3 7.4 6.5 12.7z"></path></svg></span>
				<span class="ngl-process-text"></span>
			</span>

			<span class="ngl-process ngl-ajax is-hidden is-invalid">
				<span class="ngl-process-icon"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 16 16" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M16 8A8 8 0 110 8a8 8 0 0116 0zM8 4a.905.905 0 00-.9.995l.35 3.507a.552.552 0 001.1 0l.35-3.507A.905.905 0 008 4zm.002 6a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path></svg></span>
				<span class="ngl-process-text"></span>
			</span>
		</div>
		<div class="ngl-label-more">

		</div>
			<?php
		else :
			?>
		<div class="ngl-label-verification">
			<span class="ngl-process ngl-ajax is-hidden is-waiting">
				<span class="ngl-process-icon"><svg class="ngl-infinite-spinner" stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg></span>
				<span class="ngl-process-text"><strong><?php esc_html_e( 'Saving...', 'newsletter-glue' ); ?></strong></span>
			</span>

			<span class="ngl-process ngl-ajax is-hidden is-valid">
				<span class="ngl-process-icon"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 1024 1024" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M512 64C264.6 64 64 264.6 64 512s200.6 448 448 448 448-200.6 448-448S759.4 64 512 64zm193.5 301.7l-210.6 292a31.8 31.8 0 0 1-51.7 0L318.5 484.9c-3.8-5.3 0-12.7 6.5-12.7h46.9c10.2 0 19.9 4.9 25.9 13.3l71.2 98.8 157.2-218c6-8.3 15.6-13.3 25.9-13.3H699c6.5 0 10.3 7.4 6.5 12.7z"></path></svg></span>
				<span class="ngl-process-text"><?php esc_html_e( 'Saved', 'newsletter-glue' ); ?></span>
			</span>

			<span class="ngl-process ngl-ajax is-hidden is-invalid">
				<span class="ngl-process-icon"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 16 16" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M16 8A8 8 0 110 8a8 8 0 0116 0zM8 4a.905.905 0 00-.9.995l.35 3.507a.552.552 0 001.1 0l.35-3.507A.905.905 0 008 4zm.002 6a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path></svg></span>
				<span class="ngl-process-text"></span>
			</span>
		</div>
		<div class="ngl-label-more">

		</div>
			<?php
		endif;
	}

	/**
	 * Show input verification info.
	 */
	public function input_verification_info() {
		?>
		<div class="ngl-label-verification">
			<span class="ngl-process ngl-ajax is-hidden is-waiting">
				<span class="ngl-process-icon"><svg class="ngl-infinite-spinner" stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg></span>
				<span class="ngl-process-text"><strong><?php esc_html_e( 'Saving...', 'newsletter-glue' ); ?></strong></span>
			</span>

			<span class="ngl-process ngl-ajax is-hidden is-valid">
				<span class="ngl-process-icon"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 1024 1024" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M512 64C264.6 64 64 264.6 64 512s200.6 448 448 448 448-200.6 448-448S759.4 64 512 64zm193.5 301.7l-210.6 292a31.8 31.8 0 0 1-51.7 0L318.5 484.9c-3.8-5.3 0-12.7 6.5-12.7h46.9c10.2 0 19.9 4.9 25.9 13.3l71.2 98.8 157.2-218c6-8.3 15.6-13.3 25.9-13.3H699c6.5 0 10.3 7.4 6.5 12.7z"></path></svg></span>
				<span class="ngl-process-text"><?php esc_html_e( 'Saved', 'newsletter-glue' ); ?></span>
			</span>

			<span class="ngl-process ngl-ajax is-hidden is-invalid">
				<span class="ngl-process-icon"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 16 16" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M16 8A8 8 0 110 8a8 8 0 0116 0zM8 4a.905.905 0 00-.9.995l.35 3.507a.552.552 0 001.1 0l.35-3.507A.905.905 0 008 4zm.002 6a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path></svg></span>
				<span class="ngl-process-text"></span>
			</span>
		</div>
		<div class="ngl-label-more">

		</div>
		<?php
	}

	/**
	 * Show schedule / header image options.
	 *
	 * @param array $settings Array of settings.
	 * @param array $defaults Array of defaults.
	 * @param array $post     Post object.
	 */
	public function show_schedule_and_image_options( $settings, $defaults, $post ) {
		global $post_type;

		if ( in_array( $post_type, newsletterglue_get_core_cpts() ) && ! defined( 'NEWSLETTERGLUE_DEMO' ) ) {
			return;
		}

		include NGL_PLUGIN_DIR . 'includes/admin/metabox/views/send-options.php';
	}

	/**
	 * Nothing to send message.
	 */
	public function nothing_to_send() {
		return __( 'Whoops! There&rsquo;s nothing to send.<br />Please save post as draft first.', 'newsletter-glue' );
	}

	/**
	 * Test success.
	 */
	public function get_test_success_msg() {
		return '&nbsp;';
	}

	/**
	 * Returns true if test emails are sent by WordPress.
	 */
	public function test_email_by_wordpress() {
		return false;
	}

	/**
	 * Verify email address.
	 *
	 * @param string $email An email address string.
	 */
	public function verify_email( $email = '' ) {

		$email = trim( $email );

		if ( ! $email ) {
			$response = array( 'failed' => __( 'Please enter email', 'newsletter-glue' ) );
		} elseif ( ! is_email( $email ) ) {
			$response = array( 'failed' => __( 'Invalid email', 'newsletter-glue' ) );
		} else {
			$response = array( 'success' => '<strong>' . __( 'Verified', 'newsletter-glue' ) . '</strong>' );
		}

		return $response;
	}

	/**
	 * Has email verify.
	 */
	public function has_email_verify() {
		return true;
	}

	/**
	 * Check email address.
	 *
	 * @param string $email An email address.
	 */
	public function is_invalid_email( $email = '' ) {
		$response = array();

		if ( empty( $email ) ) {
			$response['fail'] = __( 'Please enter email', 'newsletter-glue' );
		} elseif ( ! is_email( $email ) ) {
			$response['fail'] = __( 'Invalid email', 'newsletter-glue' );
		}

		if ( ! empty( $response['fail'] ) ) {
			return $response;
		}

		return false;
	}

	/**
	 * Get connect settings.
	 *
	 * @param array $integrations A list of integrations array.
	 */
	public function get_connect_settings( $integrations = array() ) {
	}

	/**
	 * Get schedule options.
	 */
	public function get_schedule_options() {

		$options = array(
			'immediately'    => __( 'Send now', 'newsletter-glue' ),
			'draft'          => sprintf( __( 'Save as draft in %s', 'newsletter-glue' ), newsletterglue_get_name( $this->app ) ),
			'schedule_draft' => sprintf( __( 'Immediately save draft in %s when post scheduled', 'newsletter-glue' ), newsletterglue_get_name( $this->app ) ),
		);

		return apply_filters( 'newsletterglue_schedule_options', $options );
	}

	/**
	 * Remove Integration.
	 */
	public function remove_integration() {

		delete_option( 'newsletterglue_integrations' );

		$response = array( 'successful' => true );

		return $response;
	}

	/**
	 * Get current user email.
	 */
	public function get_current_user_email() {
		global $current_user;

		if ( 'mailchimp' === $this->app ) {
			$info = get_option( 'newsletterglue_mailchimp' );
			return $info['email'];
		}

		return $current_user->user_email;
	}

	/**
	 * Convert merge tags in HTML.
	 *
	 * @param string $html    Email content as HTML.
	 * @param int    $post_id Post ID.
	 */
	public function convert_tags( $html, $post_id = 0 ) {

		preg_match_all( '/{{(.*?)}}/', $html, $matches );

		if ( ! empty( $matches[0] ) ) {
			$results = $matches[0];
			foreach ( $results as $result ) {
				$clean = explode( ',fallback=', $result );
				$tag   = trim( str_replace( array( '{{', '}}' ), '', $clean[0] ) );

				// That's the fallback part.
				if ( isset( $clean[1] ) ) {
					$fallback = str_replace( ' }}', '', $clean[1] );
					$fallback = trim( $fallback );
				} else {
					$fallback = '';
				}

				// Is it ESP tag or global tag?
				if ( $this->get_tag( $tag, $post_id, $fallback ) && ! isset( $_GET[ 'view_newsletter' ] ) && ! isset( $_GET[ 'preview_email' ] ) ) { // phpcs:ignore
					$html = str_replace( $result, $this->get_tag( $tag, $post_id, $fallback ), $html );
				} elseif ( $this->get_global_tag( $tag, $post_id ) ) {
					$html = str_replace( $result, $this->get_global_tag( $tag, $post_id ), $html );
				} elseif ( isset( $clean[1] ) ) {
					$string = str_replace( ' }}', '', $clean[1] );
					$string = trim( $string );
					$html   = str_replace( $result, $string, $html );
				}
			}
		}

		return $html;
	}

	/**
	 * Code supported tags for this ESP.
	 *
	 * @param string $tag      A tag.
	 * @param int    $post_id  The post ID.
	 * @param string $fallback A fallback.
	 */
	public function get_tag( $tag, $post_id = 0, $fallback = null ) {
		return false;
	}

	/**
	 * Global merge tags.
	 *
	 * @param string $tag     A tag.
	 * @param int    $post_id A post ID.
	 */
	public function get_global_tag( $tag, $post_id = 0 ) {

		$post = get_post( $post_id );

		switch ( $tag ) {
			case 'newsletter_title':
				return ! empty( $post->post_title ) ? ngl_safe_title( $post->post_title ) : '';
			break;/*
			case 'newsletter_date_y-m-d':
				return date( 'Y-m-d' );
			break;
			case 'newsletter_date_d-m-Y':
				return date( 'd-m-Y' );
			break;
			case 'newsletter_date_m/d/Y':
				return date( 'm/d/Y' );
			break;
			case 'newsletter_date_d/m/Y':
				return date( 'd/m/Y' );
			break;
			case 'newsletter_date_F j, Y':
				return date( 'F j, Y' );
			break;
			case 'newsletter_date_d F Y':
				return date( 'd F Y' );
			break;
			*/
			case 'latest_post_title_inside':
				return get_post_meta( $post_id, 'newsletterglue_latest_post_title_inside', true );
			break;
			case 'newsletter_date':
				return date( 'm/d/Y' );
			break;
			case 'latest_post_title':
				return newsletterglue_get_latest_post_title();
			break;
			case 'webversion':
				return newsletterglue_generate_web_link( $post_id );
			break;
			case 'blog_post':
			case 'link_to_post':
				return esc_url( get_permalink( $post_id ) );
			break;
		}

		return false;
	}

	/**
	 * Get lists compat.
	 */
	public function _get_lists_compat() {
		return null;
	}

	/**
	 * Get connection args.
	 *
	 * @param array $args An array with args.
	 */
	public function get_connection_args( $args ) {

		// Get API key from input.
		$api_key 	= isset( $_POST['ngl_' . $this->app . '_key'] ) ? sanitize_text_field( wp_unslash( $_POST['ngl_' . $this->app . '_key'] ) ) : ''; // phpcs:ignore
		$api_secret = isset( $_POST['ngl_' . $this->app . '_secret'] ) ? sanitize_text_field( wp_unslash( $_POST['ngl_' . $this->app . '_secret'] ) ) : ''; // phpcs:ignore
		$api_url 	= isset( $_POST['ngl_' . $this->app . '_url'] ) ? untrailingslashit( sanitize_text_field( wp_unslash( $_POST['ngl_' . $this->app . '_url'] ) ) ) : ''; // phpcs:ignore

		// Test mode. no key provided.
		if ( ! $api_key ) {
			$integrations = get_option( 'newsletterglue_integrations' );
			$options      = isset( $integrations[ $this->app ] ) ? $integrations[ $this->app ] : '';
			if ( isset( $options['api_key'] ) ) {
				$api_key = $options['api_key'];
			}
			if ( isset( $options['api_url'] ) ) {
				$api_url = $options['api_url'];
			}
			if ( isset( $options['api_secret'] ) ) {
				$api_secret = $options['api_secret'];
			}
		}

		if ( ! empty( $args ) ) {
			$api_key    = $args['api_key'];
			$api_url    = isset( $args['api_url'] ) ? $args['api_url'] : '';
			$api_secret = isset( $args['api_secret'] ) ? $args['api_secret'] : '';
		}

		$result = array(
			'api_key'    => $api_key,
			'api_url'    => $api_url,
			'api_secret' => $api_secret,
		);

		return $result;
	}

	/**
	 * Get status of newsletter. Used to store campaign status.
	 *
	 * @param array $result An array containing a result.
	 */
	public function get_status( $result ) {
		$output = array();

		if ( isset( $result['status'] ) ) {

			if ( 'error' === $result['status'] ) {
				$output['status']  = 400;
				$output['type']    = 'error';
				$output['message'] = __( 'Failed', 'newsletter-glue' );
			}

			if ( 'draft' === $result['status'] ) {
				$output['status']  = 200;
				$output['type']    = 'neutral';
				$output['message'] = __( 'Saved as draft', 'newsletter-glue' );
			}

			if ( 'sent' === $result['status'] ) {
				$output['status']  = 200;
				$output['type']    = 'success';
				$output['message'] = __( 'Sent', 'newsletter-glue' );
			}
		}

		return $output;
	}

	/**
	 * Called with the integration api key etc to validate.
	 *
	 * @used-by NGL_REST_API_Verify_API::response()
	 * @used-by NGL_REST_API_Verify_Connection::response()
	 * @used-by newsletterglue_ajax_connect_api()
	 *
	 * @param array $args Array with args.
	 */
	public function add_integration( $args = array() ) {
	}

	/**
	 * Initiates connection.
	 *
	 * @used-by NGL_REST_API_Verify_Connection::response()
	 * @used-by newsletterglue_ajax_get_tags()
	 * @used-by NGL_REST_API_Get_ESP_Items::response()
	 * @used-by NGL_REST_API_Get_Settings::get_esp_options()
	 * @used-by NGL_REST_API_Get_ESP_Options::response()
	 *
	 * @return void
	 */
	public function connect() {
	}

	/**
	 * GForce a draft state.
	 */
	public function force_draft() {
		$this->forced_state = 'draft';
	}

	/**
	 * Send the newsletter.
	 *
	 * @param int   $post_id WordPress post id to send.
	 * @param array $data Array of data.
	 * @param bool  $test Sending in test mode.
	 */
	public function send_newsletter( $post_id = 0, $data = array(), $test = false ) {
	}

	/**
	 * The lists|groups|segments|audiences|brand and their settings.
	 *
	 * @used-by NGL_REST_API_Verify_Connection::response()
	 * @used-by NGL_REST_API_Get_ESP_Options::response()
	 * @used-by NGL_REST_API_Get_Settings::get_esp_options()
	 */
	public function option_array() {
	}

	/**
	 * Get custom tags of esp
	 */
	public function get_custom_tags() {
		return array();
	}

	/**
	 * Get custom fields of esp
	 */
	public function get_custom_fields() {
		return array();
	}

	/**
	 * Convert conditional statements of esp.
	 *
	 * @param string $html Email content html.
	 */
	public function convert_conditions( $html ) {
	}
}
