<?php
/**
 * ConvertKit.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main Class.
 */
class NGL_Convertkit extends NGL_Abstract_Integration {

	public $app 	= 'convertkit';
	public $api_key = null;
	public $api_secret = null;
	public $api 	= null;

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Include needed files.
		include_once 'lib/vendor/autoload.php';
		include_once 'lib/api.php';

		$this->get_api_key();

		add_filter( 'newsletterglue_email_content_convertkit', array( $this, 'newsletterglue_email_content_convertkit' ), 10, 3 );

		add_filter( 'newsltterglue_convertkit_html_content', array( $this, 'html_content' ), 10, 2 );

	}

	/**
	 * Get API Key.
	 */
	public function get_api_key() {

		$integrations = get_option( 'newsletterglue_integrations' );
		$integration  = isset( $integrations[ $this->app ] ) ? $integrations[ $this->app] : '';

		$this->api_key 		= isset( $integration[ 'api_key' ] ) ? $integration[ 'api_key' ] : '';
		$this->api_secret	= isset( $integration[ 'api_secret' ] ) ? $integration[ 'api_secret' ] : '';

	}

	/**
	 * Add Integration.
	 */
	public function add_integration( $args = array() ) {

		$args 		= $this->get_connection_args( $args );

		$api_key 	= $args[ 'api_key' ];
		$api_secret = $args[ 'api_secret' ];

		$this->api = new NG_ConvertKit_API( $api_key, $api_secret );

		$account = ( array ) $this->api->get_account();

		if ( isset( $account[ 'error' ] ) ) {

			$this->remove_integration();

			$result = array( 'response' => 'invalid' );

			delete_option( 'newsletterglue_convertkit' );

		} else {

			if ( ! $this->already_integrated( $this->app, $api_key ) ) {
				$this->save_integration( $api_key, $api_secret, $account );
			}

			$result = array( 'response' => 'successful' );

			update_option( 'newsletterglue_convertkit', $account );

		}

		return $result;
	}

	/**
	 * Save Integration.
	 */
	public function save_integration( $api_key = '', $api_secret = '', $account = array() ) {

		// Set these in memory.
		$this->api_key = $api_key;
		$this->api_secret = $api_secret;

		delete_option( 'newsletterglue_integrations' );

		$integrations = get_option( 'newsletterglue_integrations' );

		$integrations[ $this->app ] = array();
		$integrations[ $this->app ][ 'api_key' ] 		= $api_key;
		$integrations[ $this->app ][ 'api_secret' ] 	= $api_secret;

		update_option( 'newsletterglue_integrations', $integrations );

		// Add default options.
		$globals = get_option( 'newsletterglue_options' );

		$options = array(
			'from_name' 	=> newsletterglue_get_default_from_name(),
			'from_email'	=> isset( $account[ 'primary_email_address' ] ) ? $account[ 'primary_email_address' ] : '',
		);

		foreach( $options as $key => $value ) {
			$globals[ $this->app ][ $key ] = $value;
		}

		update_option( 'newsletterglue_options', $globals );
	}

	/**
	 * Connect.
	 */
	public function connect() {

		$this->api = new NG_ConvertKit_API( $this->api_key, $this->api_secret );

	}

	/**
	 * Get form defaults.
	 */
	public function get_form_defaults() {

		$this->api = new NG_ConvertKit_API( $this->api_key, $this->api_secret );

		$defaults = array();

		$defaults[ 'lists' ] 	= $this->get_lists();

		return $defaults;
	}

	/**
	 * Get lists.
	 */
	public function get_lists() {

		$_lists = array();

		$lists = $this->api->get_forms();

		foreach( $lists[ 'forms' ] as $key => $data ) {
			$_lists[ $data[ 'id' ] ] = $data[ 'name' ];
		}

		asort( $_lists );

		return $_lists;

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

		$subject 		= isset( $data['subject'] ) ? ngl_safe_title( $data[ 'subject' ] ) : ngl_safe_title( $post->post_title );
		$from_name		= isset( $data['from_name'] ) ? $data['from_name'] : newsletterglue_get_default_from_name();
		$from_email		= isset( $data['from_email'] ) ? $data['from_email'] : $this->get_current_user_email();
		$schedule   	= isset( $data['schedule'] ) ? $data['schedule'] : 'immediately';

		$subject = apply_filters( 'newsletterglue_email_subject_line', $subject, $post, $data, $test, $this );

		// Empty content.
		if ( $test && isset( $post->post_status ) && $post->post_status === 'auto-draft' ) {

			$response['fail'] = $this->nothing_to_send();

			return $response;

		}

		// Do test email.
		if ( $test ) {
			$response = array();

            $test_email = $data[ 'test_email' ];
            $test_email_arr = explode( ',', $test_email );
            $test_emails = array_map( 'trim', $test_email_arr );
            if ( ! empty( $test_emails ) ) {
                foreach( $test_emails as $testid ) {
                    if ( ! is_email( $testid ) ) {
                        $response[ 'fail' ] = __( 'Please enter a valid email', 'newsletter-glue' );
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

		$this->api = new NG_ConvertKit_API( $this->api_key, $this->api_secret );

		$params = array(
			'api_secret'			=> $this->api_secret,
			'subject'				=> $subject,
			'description'			=> $subject,
			'content'				=> newsletterglue_generate_content( $post, $subject, $this->app ),
			//'email_layout_template' => 'Newsletter Glue', // Commenting out to use the default Kit template.
			'send_at'				=> $schedule === 'immediately' ? gmdate('Y-m-d\TH:i:s\Z', strtotime('-1 hour')) : null,
		);

		$result = $this->api->create_broadcast( $params );

		// Message ID available.
		if ( isset( $result[ 'broadcast' ] ) && isset( $result[ 'broadcast' ][ 'id' ] ) ) {

			if ( $schedule === 'draft' ) {
				$status = array( 'status' => 'draft' );
			} else {
				$status = array( 'status' => 'sent' );
			}

			newsletterglue_add_campaign_data( $post_id, $subject, $this->prepare_message( ( array ) $status ), $result[ 'broadcast' ][ 'id' ] );

			return $status;

		}

	}

	/**
	 * Prepare result for plugin.
	 */
	public function prepare_message( $result ) {
		$output = array();

		if ( isset( $result['status'] ) ) {

			if ( $result['status'] === 'draft' ) {
				$output[ 'status' ]		= 200;
				$output[ 'type' ]		= 'neutral';
				$output[ 'message' ]    = __( 'Saved as draft', 'newsletter-glue' );
			}

			if ( $result[ 'status' ] === 'sent' ) {
				$output[ 'status' ] 	= 200;
				$output[ 'type'   ] 	= 'success';
				$output[ 'message' ] 	= __( 'Sent', 'newsletter-glue' );
			}

		}

		return $output;

	}

	/**
	 * Customize content.
	 */
	public function newsletterglue_email_content_convertkit( $content, $post, $subject ) {

		$filter = apply_filters( 'newsletterglue_auto_unsub_link', true, $this->app );

		if ( ! $filter ) {
			return $content;
		}

		if ( strstr( $content, '{{ unsubscribe_link }}' ) ) {
			return $content;
		}

		$post_id		= $post->ID;
		$data 			= get_post_meta( $post_id, '_newsletterglue', true );
		$default_unsub  = $this->default_unsub();
		$unsub		 	= ! empty( $data[ 'unsub' ] ) ? $data[ 'unsub' ] : $default_unsub;

		if ( empty( $unsub ) ) {
			$unsub = $this->default_unsub();
		}

		$unsub = str_replace( '{{ unsubscribe_link }}', '{{ unsubscribe_url }}', $unsub );

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
	 * Add user to this ESP.
	 */
	public function add_user( $data ) {
		extract( $data );

		if ( empty( $email ) ) {
			return -1;
		}

		$this->api = new NG_ConvertKit_API( $this->api_key, $this->api_secret );

		if ( ! empty( $list_id ) ) {

			$args = array(
				"api_key" 		=> $this->api_key,
				"email"			=> $email,
				"first_name"	=> isset( $name ) ? $name : '',
			);

			$this->api->add_subscriber( $list_id, $args );

			if ( isset( $extra_list ) && ! empty( $extra_list_id ) ) {
				$this->api->add_subscriber( $extra_list_id, $args );
			}

		}

		return true;

	}

	/**
	 * Get connect settings.
	 */
	public function get_connect_settings( $integrations = array() ) {

		$app = $this->app;

		newsletterglue_text_field( array(
			'id' 			=> "ngl_{$app}_key",
			'placeholder' 	=> esc_html__( 'Enter API Key', 'newsletter-glue' ),
			'value'			=> isset( $integrations[ $app ]['api_key'] ) ? $integrations[ $app ]['api_key'] : '',
			'class'			=> 'ngl-text-margin',
			'type'			=> 'password',
		) );

		newsletterglue_text_field( array(
			'id' 			=> "ngl_{$app}_secret",
			'placeholder' 	=> esc_html__( 'Enter API Secret', 'newsletter-glue' ),
			'value'			=> isset( $integrations[ $app ]['api_secret'] ) ? $integrations[ $app ]['api_secret'] : '',
			'helper'		=> '<a href="https://app.convertkit.com/account_settings/account_info" target="_blank" class="ngl-link-inline-svg">' . __( 'Get API key', 'newsletter-glue' ) . ' [externallink]</a>',
			'type'			=> 'password',
		) );

	}

	/**
	 * Has email verify.
	 */
	public function has_email_verify() {
		return false;
	}

	/**
	 * Get lists compat.
	 */
	public function _get_lists_compat() {
		$this->api = new NG_ConvertKit_API( $this->api_key, $this->api_secret );
		return $this->get_lists();
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
			case 'unsubscribe_link' :
				return '{{ unsubscribe_url }}';
			break;
			default :
				return apply_filters( "newsletterglue_{$this->app}_custom_tag", '', $tag, $post_id );
			break;
		}

		return false;
	}

	/**
	 * Configure options array for this ESP.
	 */
	public function option_array() {
		return array(
			'lists' 	=> array(
				'type'		=> 'select',
				'callback'	=> 'get_lists',
				'title'     => __( 'Default form', 'newsletter-glue' ),
				'help'		=> __( 'This is used for subscribing to a form. ConvertKit API does not support sending to a specific form yet.', 'newsletter-glue' ),
			),
		);
	}

}