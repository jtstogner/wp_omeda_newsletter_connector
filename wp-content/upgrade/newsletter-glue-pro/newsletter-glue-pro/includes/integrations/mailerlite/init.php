<?php
/**
 * MailerLite.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main Class.
 */
class NGL_Mailerlite extends NGL_Abstract_Integration {

	public $app 	= 'mailerlite';
	public $api_key = null;
	public $api 	= null;

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Include needed files.
		include_once 'lib/vendor/autoload.php';

		$this->get_api_key();

		add_filter( 'newsletterglue_email_content_mailerlite', array( $this, 'newsletterglue_email_content_mailerlite' ), 10, 3 );

		add_filter( 'newsltterglue_mailerlite_html_content', array( $this, 'html_content' ), 10, 2 );
	}

	/**
	 * Get API Key.
	 */
	public function get_api_key() {

		$integrations = get_option( 'newsletterglue_integrations' );
		$integration  = isset( $integrations[ $this->app ] ) ? $integrations[ $this->app ] : '';

		$this->api_key 		= isset( $integration[ 'api_key' ] ) ? $integration[ 'api_key' ] : '';
	}

	/**
	 * Add Integration.
	 */
	public function add_integration( $args = array() ) {

		$args 		= $this->get_connection_args( $args );

		$api_key 	= $args[ 'api_key' ];
		$api_url 	= $args[ 'api_url' ];

		$this->api       = new \MailerLiteApi\MailerLite( $api_key );

		// Check if account is valid.
		$account_api = $this->api->me()->get();

		$valid_account = isset( $account_api->account ) ? true : false;

		if ( ! $valid_account ) {

			$this->remove_integration();

			$result = array( 'response' => 'invalid' );

			delete_option( 'newsletterglue_mailerlite' );

		} else {

			if ( ! $this->already_integrated( $this->app, $api_key ) ) {
				$this->save_integration( $api_key, ( array ) $account_api->account );
			}

			$result = array( 'response' => 'successful' );

			update_option( 'newsletterglue_mailerlite', ( array ) $account_api->account );

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

		$integrations[ $this->app ] = array();
		$integrations[ $this->app ][ 'api_key' ] 		= $api_key;

		$name = isset( $account[ 'name' ] ) ? $account[ 'name' ] : newsletterglue_get_default_from_name();

		$integrations[ $this->app ][ 'connection_name' ] = sprintf( __( '%s – %s', 'newsletter-glue' ), $name, newsletterglue_get_name( $this->app ) );

		update_option( 'newsletterglue_integrations', $integrations );

		// Add default options.
		$globals = get_option( 'newsletterglue_options' );

		$options = array(
			'from_name' 	=> $name,
			'from_email'	=> isset( $account[ 'from' ] ) ? $account[ 'from' ] : '',
			'unsub'			=> $this->default_unsub(),
		);

		foreach( $options as $key => $value ) {
			$globals[ $this->app ][ $key ] = $value;
		}

		update_option( 'newsletterglue_options', $globals );

		update_option( 'newsletterglue_admin_name', $name );

	}

	/**
	 * Connect.
	 */
	public function connect() {

		$this->api = new \MailerLiteApi\MailerLite( $this->api_key );

	}

	/**
	 * Get form defaults.
	 */
	public function get_form_defaults() {

		$this->api = new \MailerLiteApi\MailerLite( $this->api_key );

		$defaults = array();

		$defaults[ 'groups' ] 	= $this->get_groups();
		$defaults[ 'segments' ] = $this->get_segments();

		return $defaults;
	}

	/**
	 * Get groups.
	 */
	public function get_groups() {
		$_groups = array();

		$groups = $this->api->groups();
		$array  = $groups->get();

		if ( ! empty( $array ) && $array->toArray() ) {
			foreach( $array->toArray() as $key => $data ) {
				$_groups[ $data->id ] = $data->name;
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

		$segments 	= $this->api->segments();
		$array  	= $segments->get();

		if ( ! empty( $array ) && $array->toArray() ) {
			foreach( $array->toArray()[0]->data as $key => $data ) {
				$_segments[ $data->id ] = $data->title;
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

		$subject 		= isset( $data['subject'] ) ? ngl_safe_title( $data[ 'subject' ] ) : ngl_safe_title( $post->post_title );
		$from_name		= isset( $data['from_name'] ) ? $data['from_name'] : newsletterglue_get_default_from_name();
		$from_email		= isset( $data['from_email'] ) ? $data['from_email'] : $this->get_current_user_email();
		$groups			= isset( $data['groups'] ) && ! empty( $data['groups'] ) && is_array( $data['groups'] ) ? array_map( 'intval', $data['groups'] ) : '';
		$segments		= isset( $data['segments'] ) && ! empty( $data['segments'] ) && is_array( $data['segments'] ) ? array_map( 'intval', $data['segments'] ) : '';
		$schedule		= isset( $data['schedule'] ) ? $data['schedule'] : 'immediately';

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

		$this->api = new \MailerLiteApi\MailerLite( $this->api_key );

		// At least set groups.
		if ( empty( $groups ) && empty( $segments ) ) {
			$_groups 	= $this->get_groups();
			$groups 	= array_keys( $_groups );
		}

		$campaignsApi = $this->api->campaigns();

		// Set campaign data.
		$campaignData = array(
			'type' 		=> 'regular',
			'subject'	=> $subject,
			'from_name'	=> $from_name,
			'from'		=> $from_email,
		);

		// Add groups and segments.
		if ( ! empty( $groups ) ) {
			$campaignData[ 'groups' ] = $groups;
		}
		if ( ! empty( $segments ) ) {
			$campaignData[ 'segments' ] = $segments;
		}

		$campaign = $campaignsApi->create( $campaignData );

		if ( isset( $campaign->id ) ) {
			$campaignId = $campaign->id;
		}

		// Add content.
		if ( $campaignId > 0 ) {

			$plain_content = __( 'Your email client does not support HTML emails. Open newsletter here: {$url}. If you do not want to receive emails from us, click here: {$unsubscribe}', 'newsletter-glue' );

			$content = newsletterglue_generate_content( $post, $subject, $this->app );

			$contentData = array(
				'html'	=> $content,
				'plain' => $plain_content,
			);

			$result = $campaignsApi->addContent( $campaignId, $contentData );

		}

		// Send it.
		if ( $schedule === 'draft' ) {

			$result = array( 'status' => 'draft' );

		} else {

			$result = $campaignsApi->send( $campaignId ); 

		}

		newsletterglue_add_campaign_data( $post_id, $subject, $this->prepare_message( (array) $result ), $campaignId );

		return $result;

	}

	/**
	 * Prepare result for plugin.
	 */
	public function prepare_message( $result ) {
		$output = array();

		if ( isset( $result['status'] ) ) {

			if ( $result['status'] == 'draft' ) {
				$output[ 'status' ]		= 200;
				$output[ 'type' ]		= 'neutral';
				$output[ 'message' ]    = __( 'Saved as draft', 'newsletter-glue' );
			}

		}

		if ( isset( $result[ 'id' ] ) ) {
			$output[ 'status' ] 	= 200;
			$output[ 'type'   ] 	= 'success';
			$output[ 'message' ] 	= __( 'Sent', 'newsletter-glue' );
		}

		return $output;

	}

	/**
	 * Add user to this ESP.
	 */
	public function add_user( $data ) {
		extract( $data );

		if ( empty( $email ) ) {
			return -1;
		}

		$this->api = new \MailerLiteApi\MailerLite( $this->api_key );

		$subscriber = array(
			'email' 	=> $email,
			'name' 		=> ! empty( $name ) ? $name : '',
		);

		if ( ! empty( $list_id ) ) {
			$groupsApi	 	= $this->api->groups();
			$result 		= $groupsApi->addSubscriber( $list_id, $subscriber );
		} else {
			$subscribersApi = $this->api->subscribers();
			$result 		= $subscribersApi->create( $subscriber );
		}

		if ( isset( $extra_list ) && ! empty( $extra_list_id ) ) {
			$groupsApi	 	= $this->api->groups();
			$result 		= $groupsApi->addSubscriber( $extra_list_id, $subscriber );
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
			'helper'		=> '<a href="https://app.mailerlite.com/integrations/api/" target="_blank" class="ngl-link-inline-svg">' . __( 'Get API key', 'newsletter-glue' ) . ' [externallink]</a>',
			'type'			=> 'password',
		) );

	}

	/**
	 * Customize content.
	 */
	public function newsletterglue_email_content_mailerlite( $content, $post, $subject ) {

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
		return false;
	}

	/**
	 * Get email verify help.
	 */
	public function get_email_verify_help() {
		return 'https://help.mailerlite.com/article/show/29280-how-to-verify-and-authenticate-your-domain#chapter2';
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
				return '{$unsubscribe}';
			break;
			case 'name' :
				return ! empty( $fallback ) ? '{$name|default:' . "'" . $fallback . "'" . '}' : '{$name}';
			break;
			case 'email' :
				return '{$email}';
			break;
			case 'country' :
				return ! empty( $fallback ) ? '{$country|default:' . "'" . $fallback . "'" . '}' : '{$country}';
			break;
			case 'city' :
				return ! empty( $fallback ) ? '{$city|default:' . "'" . $fallback . "'" . '}' : '{$city}';
			break;
			case 'state' :
				return ! empty( $fallback ) ? '{$state|default:' . "'" . $fallback . "'" . '}' : '{$state}';
			break;
			case 'phone' :
				return ! empty( $fallback ) ? '{$phone|default:' . "'" . $fallback . "'" . '}' : '{$phone}';
			break;
			default :
				return apply_filters( "newsletterglue_{$this->app}_custom_tag", '', $tag, $post_id );
			break;
		}

		return false;
	}

	/**
	 * Get lists compat.
	 */
	public function _get_lists_compat() {
		$this->api = new \MailerLiteApi\MailerLite( $this->api_key );
		return $this->get_groups();
	}

	/**
	 * Configure options array for this ESP.
	 */
	public function option_array() {
		return array(
			'groups' 	=> array(
				'type'		=> 'select',
				'is_multi'	=> true,
				'callback'	=> 'get_groups',
				'title'     => __( 'Groups', 'newsletter-glue' ),
				'help'		=> __( 'Who receives your email.', 'newsletter-glue' ),
			),
			'segments'	=> array(
				'type'		=> 'select',
				'is_multi'	=> true,
				'callback' 	=> 'get_segments',
				'title'		=> __( 'Segments', 'newsletter-glue' ),
				'help'		=> sprintf( __( 'A specific group of subscribers. %s', 'newsletter-glue' ), '<a href="https://app.mailerlite.com/subscribers/segments" target="_blank">' . __( 'Create segment', 'newsletter-glue' ) . '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="components-external-link__icon css-6wogo1-StyledIcon etxm6pv0" role="img" aria-hidden="true" focusable="false"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a>' ),
			)
		);
	}

}