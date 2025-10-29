<?php
/**
 * Mailjet.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Class.
 */
class NGL_Mailjet extends NGL_Abstract_Integration {

	public $app        = 'mailjet';
	public $api_key    = null;
	public $api_secret = null;
	public $api        = null;

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Include needed files.
		include_once 'lib/api.php';

		$this->get_api_key();

		add_filter( 'newsletterglue_email_content_mailjet', array( $this, 'newsletterglue_email_content_mailjet' ), 10, 3 );

		add_filter( 'newsltterglue_mailjet_html_content', array( $this, 'html_content' ), 10, 2 );
	}

	/**
	 * Get API Key.
	 */
	public function get_api_key() {

		$integrations = get_option( 'newsletterglue_integrations' );
		$integration  = isset( $integrations[ $this->app ] ) ? $integrations[ $this->app ] : '';

		$this->api_key    = isset( $integration['api_key'] ) ? $integration['api_key'] : '';
		$this->api_secret = isset( $integration['api_secret'] ) ? $integration['api_secret'] : '';
	}

	/**
	 * Add Integration.
	 */
	public function add_integration( $args = array() ) {

		$args = $this->get_connection_args( $args );

		$api_key    = $args['api_key'];
		$api_secret = $args['api_secret'];

		$this->api = new NGL_Mailjet_API( $api_key, $api_secret );

		// Check if account is valid.
		$myprofile = $this->api->get( '/myprofile' );

		if ( ! $myprofile ) {

			$this->remove_integration();

			$result = array( 'response' => 'invalid' );

			delete_option( 'newsletterglue_mailjet' );

		} else {

			$user = $this->api->get( '/user' );

			$myprofile[0]['Email']  = $user[0]['Email'];
			$myprofile[0]['Locale'] = $user[0]['Locale'];

			$account = $myprofile[0];

			if ( ! $this->already_integrated( $this->app, $api_key ) ) {
				$this->save_integration( $api_key, $api_secret, $account );
			}

			$result = array( 'response' => 'successful' );

			update_option( 'newsletterglue_mailjet', $account );

		}

		return $result;
	}

	/**
	 * Save Integration.
	 */
	public function save_integration( $api_key = '', $api_secret = '', $account = array() ) {

		// Set these in memory.
		$this->api_key    = $api_key;
		$this->api_secret = $api_secret;

		delete_option( 'newsletterglue_integrations' );

		$integrations = get_option( 'newsletterglue_integrations' );

		$integrations[ $this->app ]               = array();
		$integrations[ $this->app ]['api_key']    = $api_key;
		$integrations[ $this->app ]['api_secret'] = $api_secret;

		$name = isset( $account['CompanyName'] ) ? $account['CompanyName'] : newsletterglue_get_default_from_name();

		$integrations[ $this->app ]['connection_name'] = sprintf( __( '%1$s – %2$s', 'newsletter-glue' ), $name, newsletterglue_get_name( $this->app ) );

		update_option( 'newsletterglue_integrations', $integrations );

		// Add default options.
		$globals = get_option( 'newsletterglue_options' );

		$options = array(
			'from_name'  => $name,
			'from_email' => isset( $account['Email'] ) ? $account['Email'] : get_option( 'admin_email' ),
			'unsub'      => $this->default_unsub(),
		);

		foreach ( $options as $key => $value ) {
			$globals[ $this->app ][ $key ] = $value;
		}

		update_option( 'newsletterglue_options', $globals );

		update_option( 'newsletterglue_admin_name', $name );

		update_option( 'newsletterglue_admin_address', isset( $account['AddressStreet'] ) && ! empty( $account['AddressStreet'] ) ? $account['AddressStreet'] : '' );
	}

	/**
	 * Default unsub.
	 */
	public function default_unsub() {
		return '<a href="{{ unsubscribe_link }}">' . __( 'Unsubscribe', 'newsletter-glue' ) . '</a> to stop receiving these emails.';
	}

	/**
	 * Connect.
	 */
	public function connect() {

		$this->api = new NGL_Mailjet_API( $this->api_key, $this->api_secret );
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
				'class'       => 'ngl-text-margin',
				'type'        => 'password',
			)
		);

		newsletterglue_text_field(
			array(
				'id'          => "ngl_{$app}_secret",
				'placeholder' => esc_html__( 'Enter Secret Key', 'newsletter-glue' ),
				'value'       => isset( $integrations[ $app ]['api_secret'] ) ? $integrations[ $app ]['api_secret'] : '',
				'helper'      => '<a href="https://app.mailjet.com/account/api_keys" target="_blank" class="ngl-link-inline-svg">' . __( 'Get API key', 'newsletter-glue' ) . ' [externallink]</a>',
				'type'        => 'password',
			)
		);
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

		$this->api = new NGL_Mailjet_API( $this->api_key, $this->api_secret );

		$response = $this->api->get( '/sender' );

		// Check if email is a valid sender.
		$verified = false;
		if ( $response && ! empty( $response[0] ) ) {
			$senders = $response;
			foreach ( $senders as $key => $data ) {
				if ( isset( $data['Email'] ) && trim( $email ) === trim( $data['Email'] ) && $data['Status'] == 'Active' ) {
					$verified = true;
					break;
				}
			}
		}

		if ( $verified ) {

			$response = array(
				'success' => '<strong>' . __( 'Verified', 'newsletter-glue' ) . '</strong>',
			);

		} else {

			$response = array(
				'failed'         => __( 'Not verified', 'newsletter-glue' ),
				'failed_details' => '<a href="https://app.mailjet.com/account/sender" target="_blank">' . __( 'Verify email now', 'newsletter-glue' ) . ' <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a> <a href="https://newsletterglue.com/docs/email-verification-my-email-is-not-verified/" target="_blank">' . __( 'Learn more', 'newsletter-glue' ) . ' <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a>',
			);

		}

		return $response;
	}

	/**
	 * Configure options array for this ESP.
	 */
	public function option_array() {
		return array(
			'lists'    => array(
				'type'     => 'select',
				'callback' => 'get_lists',
				'title'    => __( 'Contact list', 'newsletter-glue' ),
				'help'     => sprintf( __( 'Who receives your email. %s', 'newsletter-glue' ), '<a href="https://app.mailjet.com/contacts" target="_blank">' . __( 'Manage contact lists', 'newsletter-glue' ) . '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="components-external-link__icon css-6wogo1-StyledIcon etxm6pv0" role="img" aria-hidden="true" focusable="false"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a>' ),
			),
			'segments' => array(
				'type'     => 'select',
				'callback' => 'get_segments',
				'title'    => __( 'Segment', 'newsletter-glue' ),
				'help'     => sprintf( __( 'A specific group of subscribers. %s', 'newsletter-glue' ), '<a href="https://app.mailjet.com/segmentation/create" target="_blank">' . __( 'Create segment', 'newsletter-glue' ) . '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="components-external-link__icon css-6wogo1-StyledIcon etxm6pv0" role="img" aria-hidden="true" focusable="false"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a>' ),
			),
		);
	}

	/**
	 * Get form defaults.
	 */
	public function get_form_defaults() {

		$this->api = new NGL_Mailjet_API( $this->api_key, $this->api_secret );

		$defaults = array();

		return $defaults;
	}

	/**
	 * Get lists compat.
	 */
	public function _get_lists_compat() {
		$this->api = new NGL_Mailjet_API( $this->api_key, $this->api_secret );

		return $this->get_lists();
	}

	/**
	 * Get Lists.
	 */
	public function get_lists() {
		$_lists = array();

		$lists = $this->api->get( '/contactslist' );

		if ( ! empty( $lists ) ) {
			foreach ( $lists as $key => $data ) {
				$_lists[ $data['ID'] ] = $data['Name'];
			}
		}

		return $_lists;
	}

	/**
	 * Get Segments.
	 */
	public function get_segments() {

		$_segments = array( '_all' => __( 'Everyone', 'newsletter-glue' ) );

		$segments = $this->api->get( '/contactfilter?Limit=200' );

		if ( ! empty( $segments ) ) {
			foreach ( $segments as $key => $data ) {
				$_segments[ $data['ID'] ] = $data['Name'];
			}
		}

		return $_segments;
	}

	/**
	 * Customize content.
	 */
	public function newsletterglue_email_content_mailjet( $content, $post, $subject ) {

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

		$locale = explode( '_', $this->get_locale() );
		$locale = $locale[0];
		$locale = strtoupper( $locale );

		$unsub = str_replace( '{{ unsubscribe_link }}', "[[UNSUB_LINK_{$locale}]]", $unsub );

		$content .= '<p class="ngl-unsubscribe">' . wp_kses_post( $unsub ) . '</p>';

		return $content;
	}

	/**
	 * Replace universal tags with esp tags.
	 */
	public function html_content( $html, $post_id ) {

		$html = $this->convert_tags( $html, $post_id );

		return $html;
	}

	/**
	 * Get email verify help.
	 */
	public function get_email_verify_help() {
		return 'https://documentation.mailjet.com/hc/en-us/articles/360042561594-How-to-validate-an-entire-sending-domain-';
	}

	/**
	 * Code supported tags for this ESP.
	 */
	public function get_tag( $tag, $post_id = 0, $fallback = null ) {

		$locale = explode( '_', $this->get_locale() );
		$locale = $locale[0];
		$locale = strtoupper( $locale );

		switch ( $tag ) {
			case 'unsubscribe_link':
				return "[[UNSUB_LINK_{$locale}]]";
			break;
			case 'email':
				return '[[EMAIL_TO]]';
			break;
			case 'first_name':
				return ! empty( $fallback ) ? '[[data:firstname:"' . $fallback . '"]]' : '[[firstname]]';
			break;
			case 'name':
				return ! empty( $fallback ) ? '[[data:name:"' . $fallback . '"]]' : '[[name]]';
			break;
			case 'city':
				return ! empty( $fallback ) ? '[[data:city:"' . $fallback . '"]]' : '[[city]]';
			break;
			default:
				return apply_filters( "newsletterglue_{$this->app}_custom_tag", '', $tag, $post_id );
			break;
		}

		return false;
	}

	/**
	 * Get Locale.
	 */
	public function get_locale() {
		$options = get_option( 'newsletterglue_mailjet' );

		return isset( $options['Locale'] ) ? $options['Locale'] : 'en_US';
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

		$subject    = isset( $data['subject'] ) ? ngl_safe_title( $data['subject'] ) : ngl_safe_title( $post->post_title );
		$from_name  = isset( $data['from_name'] ) ? $data['from_name'] : newsletterglue_get_default_from_name();
		$from_email = isset( $data['from_email'] ) ? $data['from_email'] : $this->get_current_user_email();
		$lists      = isset( $data['lists'] ) && ! empty( $data['lists'] ) ? $data['lists'] : '';
		$segments   = isset( $data['segments'] ) && ! empty( $data['segments'] ) ? $data['segments'] : '';
		$schedule   = isset( $data['schedule'] ) ? $data['schedule'] : 'immediately';

		if ( ! empty( $lists ) && ! is_array( $lists ) ) {
			$lists = explode( ',', $lists );
		}

		if ( ! empty( $segments ) && ! is_array( $segments ) ) {
			$segments = explode( ',', $segments );
		}

		$subject = apply_filters( 'newsletterglue_email_subject_line', $subject, $post, $data, $test, $this );

		// Empty content.
		if ( $test && isset( $post->post_status ) && $post->post_status === 'auto-draft' ) {

			$response['fail'] = $this->nothing_to_send();

			return $response;

		}

		// API Instance.
		$this->api = new NGL_Mailjet_API( $this->api_key, $this->api_secret );

		// Sender info.
		$sender_id = 0;
		$senders   = $this->api->get( '/sender' );
		if ( ! empty( $senders ) ) {
			foreach ( $senders as $key => $info ) {
				if ( $info['Status'] == 'Active' && $info['Email'] === $from_email ) {
					$sender_id = $info['ID'];
					break;
				}
			}
		}

		// Build newsletter args.
		$args = array(
			'Subject'        => $subject,
			'Title'          => ! empty( ngl_safe_title( $post->post_title ) ) ? ngl_safe_title( $post->post_title ) : $subject,
			'Locale'         => $this->get_locale(),
			'Sender'         => $sender_id,
			'SenderEmail'    => $from_email,
			'SenderName'     => $from_name,
			'ContactsListID' => ! empty( $lists[0] ) ? absint( $lists[0] ) : '',
			'SegmentationID' => ! empty( $segments[0] ) ? absint( $segments[0] ) : '',
		);

		$response = $this->api->post( '/campaigndraft', $args );

		if ( isset( $response[0]['ID'] ) ) {
			$campaign_id = $response[0]['ID'];
		} else {
			$campaign_id = 0;
		}

		$content = array(
			'Headers'   => 'object',
			'Html-part' => newsletterglue_generate_content( $post, $subject, $this->app ),
		);

		$campaign_content = $this->api->post( '/campaigndraft/' . $campaign_id . '/detailcontent', $content );

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

			$to_send_test = array();
			foreach ( $test_emails as $test_email_id ) {
				$to_send_test[] = array(
					'Email' => sanitize_email( $test_email_id ),
					'Name'  => 'Test email',
				);
			}

			$test_args = array(
				'Recipients' => $to_send_test,
			);

			$test_campaign = $this->api->post( '/campaigndraft/' . $campaign_id . '/test', $test_args );

			$response['success'] = $this->get_test_success_msg();

			return $response;
		}

		if ( $schedule === 'draft' ) {
			$status = array( 'status' => 'draft' );
		} else {
			$status = array( 'status' => 'sent' );
			$this->api->post( '/campaigndraft/' . $campaign_id . '/send' );
		}

		newsletterglue_add_campaign_data( $post_id, $subject, $this->get_status( $status ), $campaign_id );

		return $status;
	}

	/**
	 * Add user to this ESP.
	 */
	public function add_user( $data ) {
		extract( $data );

		if ( empty( $email ) ) {
			return -1;
		}

		$this->api = new NGL_Mailjet_API( $this->api_key, $this->api_secret );

		if ( ! empty( $list_id ) ) {

			$args = array(
				'Name'   => isset( $name ) ? $name : '',
				'Email'  => $email,
				'Action' => 'addforce',
			);

			$result = $this->api->post( '/contactslist/' . $list_id . '/managecontact', $args );

			if ( isset( $extra_list ) && ! empty( $extra_list_id ) ) {
				$result = $this->api->post( '/contactslist/' . $extra_list_id . '/managecontact', $args );
			}
		}

		return $result;
	}
}
