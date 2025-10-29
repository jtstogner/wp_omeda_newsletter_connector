<?php
/**
 * MailerLite V2.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Class.
 */
class NGL_Mailerlite_V2 extends NGL_Abstract_Integration {

	public $app     = 'mailerlite_v2';
	public $api_key = null;
	public $api     = null;

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Include needed files.
		include_once 'lib/api.php';

		$this->get_api_key();

		add_filter( 'newsletterglue_email_content_mailerlite_v2', array( $this, 'newsletterglue_email_content_mailerlite_v2' ), 10, 3 );

		add_filter( 'newsltterglue_mailerlite_v2_html_content', array( $this, 'html_content' ), 10, 2 );
	}

	/**
	 * Get API Key.
	 */
	public function get_api_key() {

		$integrations = get_option( 'newsletterglue_integrations' );
		$integration  = isset( $integrations[ $this->app ] ) ? $integrations[ $this->app ] : '';

		$this->api_key = isset( $integration['api_key'] ) ? $integration['api_key'] : '';
	}

	/**
	 * Add Integration.
	 */
	public function add_integration( $args = array() ) {

		$args = $this->get_connection_args( $args );

		$api_key = $args['api_key'];
		$api_url = $args['api_url'];

		$this->api = new NGL_Mailerlite_V2_API( $api_key );

		// Check if account is valid.
		$account = $this->api->get( '/account' );

		$valid_account = isset( $account['data']['id'] ) ? true : false;

		if ( ! $valid_account ) {

			$this->remove_integration();

			$result = array( 'response' => 'invalid' );

			delete_option( 'newsletterglue_mailerlite_v2' );

		} else {

			if ( ! $this->already_integrated( $this->app, $api_key ) ) {
				$this->save_integration( $api_key, (array) $account['data'] );
			}

			$result = array( 'response' => 'successful' );

			update_option( 'newsletterglue_mailerlite_v2', (array) $account['data'] );

		}

		return $result;
	}

	/**
	 * Save Integration.
	 */
	public function save_integration( $api_key = '', $account = array() ) {

		// Set these in memory.
		$this->api_key = $api_key;

		delete_option( 'newsletterglue_integrations' );

		$integrations = get_option( 'newsletterglue_integrations' );

		$integrations[ $this->app ]            = array();
		$integrations[ $this->app ]['api_key'] = $api_key;

		$name = isset( $account['sender_name'] ) ? $account['sender_name'] : newsletterglue_get_default_from_name();

		$integrations[ $this->app ]['connection_name'] = sprintf( __( '%1$s – %2$s', 'newsletter-glue' ), $name, newsletterglue_get_name( $this->app ) );

		update_option( 'newsletterglue_integrations', $integrations );

		// Add default options.
		$globals = get_option( 'newsletterglue_options' );

		$options = array(
			'from_name'  => $name,
			'from_email' => isset( $account['sender_email'] ) ? $account['sender_email'] : '',
			'unsub'      => $this->default_unsub(),
		);

		foreach ( $options as $key => $value ) {
			$globals[ $this->app ][ $key ] = $value;
		}

		update_option( 'newsletterglue_options', $globals );

		update_option( 'newsletterglue_admin_name', $name );
	}

	/**
	 * Connect.
	 */
	public function connect() {

		$this->api = new NGL_Mailerlite_V2_API( $this->api_key );
	}

	/**
	 * Get form defaults.
	 */
	public function get_form_defaults() {

		$this->api = new NGL_Mailerlite_V2_API( $this->api_key );

		$defaults = array();

		$defaults['groups']   = $this->get_groups();
		$defaults['segments'] = $this->get_segments();

		return $defaults;
	}

	/**
	 * Get groups.
	 */
	public function get_groups() {
		$_groups = array();

		$groups = $this->api->groups();

		if ( isset( $groups['data'] ) && ! empty( $groups['data'] ) ) {
			foreach ( $groups['data'] as $key => $data ) {
				$_groups[ '_' . $data['id'] ] = $data['name'];
			}
		}

		asort( $_groups );

		return $_groups;
	}

	/**
	 * Get groups.
	 */
	public function get_segments() {
		$_segments = array();

		$segments = $this->api->get( '/segments' );

		if ( isset( $segments['data'] ) && ! empty( $segments['data'] ) ) {
			foreach ( $segments['data'] as $key => $data ) {
				$_segments[ '_' . $data['id'] ] = $data['name'];
			}
		}

		asort( $_segments );

		return $_segments;
	}

	/**
	 * Returns true if test emails are sent by WordPress.
	 */
	public function test_email_by_wordpress() {
		return true;
	}

	/**
	 * Send newsletter.
	 */
	public function send_newsletter( $post_id = 0, $data = array(), $test = false ) {

		if ( defined( 'NGL_SEND_IN_PROGRESS' ) ) {
			return;
		}

		define( 'NGL_SEND_IN_PROGRESS', 'sending' );

		$post = get_post( $post_id );

		// If no data was provided. Get it from the post.
		if ( empty( $data ) ) {
			$data = get_post_meta( $post_id, '_newsletterglue', true );
		}

		$campaignId = 0;

		$subject    = isset( $data['subject'] ) ? ngl_safe_title( $data['subject'] ) : ngl_safe_title( $post->post_title );
		$from_name  = isset( $data['from_name'] ) ? $data['from_name'] : newsletterglue_get_default_from_name();
		$from_email = isset( $data['from_email'] ) ? $data['from_email'] : $this->get_current_user_email();
		$groups     = isset( $data['groups'] ) && ! empty( $data['groups'] ) ? $data['groups'] : '';
		$segments   = isset( $data['segments'] ) && ! empty( $data['segments'] ) ? $data['segments'] : '';
		$schedule   = isset( $data['schedule'] ) ? $data['schedule'] : 'immediately';

		$subject = apply_filters( 'newsletterglue_email_subject_line', $subject, $post, $data, $test, $this );

		// Empty content.
		if ( $test && isset( $post->post_status ) && $post->post_status === 'auto-draft' ) {

			$response['fail'] = $this->nothing_to_send();

			return $response;
		}

		// Do test email.
		if ( $test ) {

			$response = array();

			$test_email     = $data['test_email'];
			$test_email_arr = explode( ',', $test_email );
			$test_emails    = array_map( 'trim', $test_email_arr );
			if ( ! empty( $test_emails ) ) {
				foreach ( $test_emails as $testid ) {
					if ( ! is_email( $testid ) ) {
						$response['fail'] = __( 'Please enter a valid email', 'newsletter-glue' );
					}
				}
			}
			if ( ! empty( $response['fail'] ) ) {
				return $response;
			}

			add_filter( 'wp_mail_content_type', array( $this, 'wp_mail_content_type' ) );

			$body = newsletterglue_generate_content( $post, $subject, $this->app );

			wp_mail( $test_emails, sprintf( __( '[Test] %s', 'newsletter-glue' ), $subject ), $body ); // phpcs:ignore

			$response['success'] = $this->get_test_success_msg();

			return $response;

		}

		$this->api = new NGL_Mailerlite_V2_API( $this->api_key );

		$content = newsletterglue_generate_content( $post, $subject, $this->app );

		// Set campaign data.
		$campaignData = array(
			'name'   => $subject,
			'type'   => 'regular',
			'emails' => array(
				array(
					'subject'   => $subject,
					'from_name' => $from_name,
					'from'      => $from_email,
					'content'   => $content,
				),
			),
		);

		// Add groups and segments.
		if ( ! empty( $groups ) ) {
			$campaignData['groups'] = array_map(
				function ( $group ) {
					return str_replace( '_', '', $group );
				},
				$groups
			);
		}

		if ( ! empty( $segments ) ) {
			$campaignData['segments'] = array_map(
				function ( $segment ) {
					return str_replace( '_', '', $segment );
				},
				$segments
			);
		}

		$campaign = $this->api->post( '/campaigns', $campaignData );

		if ( isset( $campaign['data']['id'] ) ) {
			$campaignId = $campaign['data']['id'];
		}

		// Send it.
		if ( $schedule === 'draft' ) {

			$result = array( 'status' => 'draft' );

		} else {

			$scheduleData = array(
				'delivery' => 'instant',
			);

			$scheduleResponse = $this->api->post( "/campaigns/{$campaignId}/schedule", $scheduleData );

			if ( isset( $scheduleResponse['data']['id'] ) ) {

				$result = array( 'status' => 'sent' );

			} else {

				$result = array( 'status' => 'error' );

			}
		}

		newsletterglue_add_campaign_data( $post_id, $subject, $this->get_status( (array) $result ), $campaignId );

		return $result;
	}

	/**
	 * Add user to this ESP.
	 */
	public function add_user( $data ) {
		extract( $data );

		if ( empty( $email ) ) {
			return -1;
		}

		$this->api = new NGL_Mailerlite_V2_API( $this->api_key );

		$list_id = str_replace( '_', '', $list_id );

		$groups[] = $list_id;

		if ( isset( $extra_list ) && ! empty( $extra_list_id ) ) {
			$groups[] = str_replace( '_', '', $extra_list_id );
		}

		$subscriber = array(
			'email'  => $email,
			'groups' => $groups,
		);

		if ( ! empty( $name ) ) {
			$subscriber['fields'] = array(
				'name' => $name,
			);
		}

		$response = $this->api->post( '/subscribers', $subscriber );

		return empty( $response['data']['id'] ) ? false : true;
	}

	/**
	 * Get connect settings.
	 */
	public function get_connect_settings( $integrations = array() ) {

		$app = $this->app;

		newsletterglue_text_field(
			array(
				'id'          => "ngl_{$app}_key",
				'placeholder' => esc_html__( 'Enter API Key', 'newsletter-glue' ),
				'value'       => isset( $integrations[ $app ]['api_key'] ) ? $integrations[ $app ]['api_key'] : '',
				'helper'      => '<a href="https://dashboard.mailerlite.com/integrations/api" target="_blank" class="ngl-link-inline-svg">' . __( 'Get API key', 'newsletter-glue' ) . ' [externallink]</a>',
				'type'        => 'password',
			)
		);
	}

	/**
	 * Customize content.
	 */
	public function newsletterglue_email_content_mailerlite_v2( $content, $post, $subject ) {

		$filter = apply_filters( 'newsletterglue_auto_unsub_link', true, $this->app );

		if ( ! $filter ) {
			return $content;
		}

		if ( strstr( $content, '{{ unsubscribe_link }}' ) ) {
			return $content;
		}

		$post_id       = $post->ID;
		$data          = get_post_meta( $post_id, '_newsletterglue', true );
		$default_unsub = $this->default_unsub();
		$unsub         = ! empty( $data['unsub'] ) ? $data['unsub'] : $default_unsub;

		if ( empty( $unsub ) ) {
			$unsub = $this->default_unsub();
		}

		$unsub = str_replace( '{{ unsubscribe_link }}', '{$unsubscribe}', $unsub );

		$content .= '<p class="ngl-unsubscribe">' . wp_kses_post( $unsub ) . '</p>';

		return $content;
	}

	/**
	 * Default unsub.
	 */
	public function default_unsub() {
		return '<a href="{{ unsubscribe_link }}">' . __( 'Unsubscribe', 'newsletter-glue' ) . '</a> to stop receiving these emails.';
	}

	/**
	 * Has email verify.
	 */
	public function has_email_verify() {
		return true;
	}

	/**
	 * Verify email address.
	 */
	public function verify_email( $email = '' ) {

		if ( ! $email ) {
			$response = array( 'failed' => __( 'Please enter email', 'newsletter-glue' ) );
		} elseif ( ! is_email( $email ) ) {
			$response = array( 'failed' => __( 'Invalid email', 'newsletter-glue' ) );
		}

		if ( ! empty( $response ) ) {
			return $response;
		}

		$this->api = new NGL_Mailerlite_V2_API( $this->api_key );

		$account = $this->api->get( '/account' );

		// Check if email is a valid sender.
		$verified = ( isset( $account['data']['sender_email'] ) && $account['data']['sender_email'] == $email ) ? true : false;

		if ( $verified ) {

			$response = array(
				'success' => '<strong>' . __( 'Verified', 'newsletter-glue' ) . '</strong>',
			);

		} else {

			$response = array(
				'failed'         => __( 'Not verified', 'newsletter-glue' ),
				'failed_details' => '<a href="https://my.sailthru.com/verify" target="_blank">' . __( 'Verify email now', 'newsletter-glue' ) . ' <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a> <a href="https://newsletterglue.com/docs/email-verification-my-email-is-not-verified/" target="_blank">' . __( 'Learn more', 'newsletter-glue' ) . ' <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a>',
			);

		}

		return $response;
	}

	/**
	 * Get email verify help.
	 */
	public function get_email_verify_help() {
		return 'https://www.mailerlite.com/help/how-to-verify-and-authenticate-your-domain#chapter2';
	}

	/**
	 * Replace universal tags with esp tags.
	 */
	public function html_content( $html, $post_id ) {

		$html = $this->convert_tags( $html, $post_id );

		return $html;
	}

	/**
	 * Code supported tags for this ESP.
	 */
	public function get_tag( $tag, $post_id = 0, $fallback = null ) {

		switch ( $tag ) {
			case 'unsubscribe_link':
				return '{$unsubscribe}';
			break;
			case 'name':
				return ! empty( $fallback ) ? '{$name|default:' . "'" . $fallback . "'" . '}' : '{$name}';
			break;
			case 'email':
				return '{$email}';
			break;
			case 'country':
				return ! empty( $fallback ) ? '{$country|default:' . "'" . $fallback . "'" . '}' : '{$country}';
			break;
			case 'city':
				return ! empty( $fallback ) ? '{$city|default:' . "'" . $fallback . "'" . '}' : '{$city}';
			break;
			case 'state':
				return ! empty( $fallback ) ? '{$state|default:' . "'" . $fallback . "'" . '}' : '{$state}';
			break;
			case 'phone':
				return ! empty( $fallback ) ? '{$phone|default:' . "'" . $fallback . "'" . '}' : '{$phone}';
			break;
			case 'z_i_p':
				return ! empty( $fallback ) ? '{$z_i_p|default:' . "'" . $fallback . "'" . '}' : '{$z_i_p}';
			break;
			case 'last_name':
				return ! empty( $fallback ) ? '{$last_name|default:' . "'" . $fallback . "'" . '}' : '{$last_name}';
			break;
			case 'company':
				return ! empty( $fallback ) ? '{$company|default:' . "'" . $fallback . "'" . '}' : '{$company}';
			break;
			default:
				return apply_filters( "newsletterglue_{$this->app}_custom_tag", '', $tag, $post_id );
			break;
		}

		return false;
	}

	/**
	 * Get lists compat.
	 */
	public function _get_lists_compat() {
		$this->api = new NGL_Mailerlite_V2_API( $this->api_key );
		return $this->get_groups();
	}

	/**
	 * Configure options array for this ESP.
	 */
	public function option_array() {
		return array(
			'groups'   => array(
				'type'     => 'select',
				'is_multi' => true,
				'callback' => 'get_groups',
				'title'    => __( 'Groups', 'newsletter-glue' ),
				'help'     => __( 'Who receives your email.', 'newsletter-glue' ),
			),
			'segments' => array(
				'type'     => 'select',
				'is_multi' => true,
				'callback' => 'get_segments',
				'title'    => __( 'Segments', 'newsletter-glue' ),
				'help'     => sprintf( __( 'A specific group of subscribers. %s', 'newsletter-glue' ), '<a href="https://dashboard.mailerlite.com/segments" target="_blank">' . __( 'Create segment', 'newsletter-glue' ) . '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="components-external-link__icon css-6wogo1-StyledIcon etxm6pv0" role="img" aria-hidden="true" focusable="false"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a>' ),
			),
		);
	}
}
