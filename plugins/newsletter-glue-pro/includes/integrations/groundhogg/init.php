<?php
/**
 * Groundhogg.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main Class.
 */
class NGL_Groundhogg extends NGL_Abstract_Integration {

	public $app 	= 'groundhogg';
	public $api_key = null;
	public $api_secret 	= null;
	public $api 	= null;

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Include needed files.
		include_once 'lib/api.php';

		$this->get_api_key();

		add_filter( 'newsletterglue_email_content_groundhogg', array( $this, 'newsletterglue_email_content_groundhogg' ), 10, 3 );

		add_filter( 'newsltterglue_groundhogg_html_content', array( $this, 'html_content' ), 10, 2 );
	}

	/**
	 * Get API Key.
	 */
	public function get_api_key() {

		$integrations = get_option( 'newsletterglue_integrations' );
		$integration  = isset( $integrations[ $this->app ] ) ? $integrations[ $this->app ] : '';

		$this->api_key 		= isset( $integration[ 'api_key' ] ) ? $integration[ 'api_key' ] : '';
		$this->api_secret	= isset( $integration[ 'api_secret' ] ) ? $integration[ 'api_secret' ] : '';
		$this->api_url		= isset( $integration[ 'api_url' ] ) ? $integration[ 'api_url' ] : '';
	}

	/**
	 * Add Integration.
	 */
	public function add_integration( $args = array() ) {

		$args 		= $this->get_connection_args( $args );

		$api_key 	= $args[ 'api_key' ];
		$api_secret = $args[ 'api_secret' ];
		$api_url 	= $args[ 'api_url' ];

		$this->api  = new NGL_Groundhogg_API( $api_key, $api_secret, $api_url );

		$contacts = $this->api->get( '/contacts' );

		if ( isset( $contacts[ 'code' ] ) ) {
			$account = -1;
		} else {
			$account = array(
				'from_name'		=> newsletterglue_get_default_from_name()
			);
		}

		if ( $account === -1 ) {

			$this->remove_integration();

			$result = array( 'response' => 'invalid' );

			delete_option( 'newsletterglue_groundhogg' );

		} else {

			if ( ! $this->already_integrated( $this->app, $api_key ) ) {
				$this->save_integration( $api_key, $api_secret, $api_url, $account );
			}

			$this->api_key 		= $api_key;
			$this->api_secret 	= $api_secret;
			$this->api_url 		= $api_url;

			$result = array( 'response' => 'successful' );

			update_option( 'newsletterglue_groundhogg', $account );

		}

		return $result;
	}

	/**
	 * Save Integration.
	 */
	public function save_integration( $api_key = '', $api_secret = '', $api_url = '', $account = array() ) {

		delete_option( 'newsletterglue_integrations' );

		$integrations = get_option( 'newsletterglue_integrations' );

		$integrations[ $this->app ] = array();
		$integrations[ $this->app ][ 'api_key' ] = $api_key;
		$integrations[ $this->app ][ 'api_secret' ] = $api_secret;
		$integrations[ $this->app ][ 'api_url' ] = $api_url;

		$name = isset( $account[ 'from_name' ] ) ? $account[ 'from_name' ] : newsletterglue_get_default_from_name();

		$integrations[ $this->app ][ 'connection_name' ] = sprintf( __( '%s – %s', 'newsletter-glue' ), $name, newsletterglue_get_name( $this->app ) );

		update_option( 'newsletterglue_integrations', $integrations );

		// Add default options.
		$globals = get_option( 'newsletterglue_options' );

		$options = array(
			'from_name' 	=> $name,
			'from_email'	=> isset( $account[ 'from_email' ] ) ? $account[ 'from_email' ] : get_option( 'admin_email' ),
			'unsub'			=> $this->default_unsub(),
		);

		foreach( $options as $key => $value ) {
			$globals[ $this->app ][ $key ] = $value;
		}

		update_option( 'newsletterglue_options', $globals );

		update_option( 'newsletterglue_admin_name', $name );

		update_option( 'newsletterglue_admin_address', isset( $account[ 'address' ] ) ? $account[ 'address' ] : '' );
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

		$this->api = new NGL_Groundhogg_API( $this->api_key, $this->api_secret, $this->api_url );

	}

	/**
	 * Get connect settings.
	 */
	public function get_connect_settings( $integrations = array() ) {

		$app = $this->app;

		newsletterglue_text_field( array(
			'id' 			=> "ngl_{$app}_key",
			'placeholder' 	=> esc_html__( 'Enter Groundhogg public key', 'newsletter-glue' ),
			'value'			=> isset( $integrations[ $app ]['api_key'] ) ? $integrations[ $app ]['api_key'] : '',
			'helper'		=> '<a href="https://help.groundhogg.io/article/146-rest-authentication#api" target="_blank" class="ngl-link-inline-svg">' . __( 'Get API key', 'newsletter-glue' ) . ' [externallink]</a>',
			'type'			=> 'password',
		) );

		newsletterglue_text_field( array(
			'id' 			=> "ngl_{$app}_secret",
			'placeholder' 	=> esc_html__( 'Enter Groundhogg token', 'newsletter-glue' ),
			'value'			=> isset( $integrations[ $app ]['api_secret'] ) ? $integrations[ $app ]['api_secret'] : '',
			'type'			=> 'password',
		) );

		newsletterglue_text_field( array(
			'id' 			=> "ngl_{$app}_url",
			'placeholder' 	=> esc_html__( 'Enter Groundhogg API URL', 'newsletter-glue' ),
			'value'			=> isset( $integrations[ $app ]['api_url'] ) ? $integrations[ $app ]['api_url'] : '',
			'class'			=> 'ngl-text-margin',
		) );

	}

	/**
	 * Configure options array for this ESP.
	 */
	public function option_array() {
		return array(
			'lists' 	=> array(
				'type'		=> 'select',
				'is_multi'	=> true,
				'callback'	=> 'get_lists',
				'title'     => __( 'Tags', 'newsletter-glue' ),
				'help'		=> __( 'Who receives your email.', 'newsletter-glue' ),
			),
		);
	}

	/**
	 * Get form defaults.
	 */
	public function get_form_defaults() {

		$this->api = new NGL_Groundhogg_API( $this->api_key, $this->api_secret, $this->api_url );

		$defaults = array();

		return $defaults;

	}

	/**
	 * Get lists compat.
	 */
	public function _get_lists_compat() {
		$this->api = new NGL_Groundhogg_API( $this->api_key, $this->api_secret, $this->api_url );

		return $this->get_lists();
	}

	/**
	 * Get Lists.
	 */
	public function get_lists() {
		$_lists = array();

		$request = $this->api->get( '/tags' );

		if ( isset( $request[ 'items' ] ) ) {
			foreach( $request[ 'items' ] as $key => $value ) {
				$_lists[ $value[ 'data' ][ 'tag_id' ] ] = $value[ 'data' ][ 'tag_name' ];
			}
		}

		return $_lists;
	}

	/**
	 * Customize content.
	 */
	public function newsletterglue_email_content_groundhogg( $content, $post, $subject ) {

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

		$unsub = str_replace( '{{ unsubscribe_link }}', '{unsubscribe_link}', $unsub );

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
		return 'https://newsletterglue.com/docs/from-email-use-verified-email-address/';
	}

	/**
	 * Code supported tags for this ESP.
	 */
	public function get_tag( $tag, $post_id = 0, $fallback = null ) {

		switch ( $tag ) {
			case 'unsubscribe_link' :
				return '{unsubscribe_link}';
			break;
			default :
				return apply_filters( "newsletterglue_{$this->app}_custom_tag", '', $tag, $post_id );
			break;
		}

		return false;
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
		$lists			= isset( $data['lists'] ) && ! empty( $data['lists'] ) ? $data['lists'] : '';
		$schedule   	= isset( $data['schedule'] ) ? $data['schedule'] : 'immediately';

		$subject = apply_filters( 'newsletterglue_email_subject_line', $subject, $post, $data, $test, $this );

		if ( ! empty( $lists ) ) {
			if ( ! is_array( $lists ) ) {
				$lists = explode( ',', $lists );
			}
		}

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

		$this->api = new NGL_Groundhogg_API( $this->api_key, $this->api_secret, $this->api_url );

		$info = array(
			'data' => array(
				'title'			=> $subject,
				'subject'		=> $subject,
				'content'		=> newsletterglue_generate_content( $post, $subject, $this->app ),
				'from_user'		=> 1,
				'status'		=> $schedule === 'immediately' ? 'ready' : 'draft',
			),
			'meta' => array(
				'type'	=> 'html',
			),
		);

		// Create the email.
		$email = $this->api->post( '/emails', $info );

		if ( isset( $email[ 'item' ][ 'ID' ] ) ) {
			$email_id = $email[ 'item' ][ 'ID' ];
		}

		$bc = array(
			'object_type'		=> 'email',
			'object_id'			=> $email_id,
		);

		// Immediate vs draft.
		if ( $schedule === 'immediately' ) {
			$bc[ 'send_now' ] = true;
			$bc[ 'send' ] = true;
			$result = array( 'status' => 'sent' );
		} else {
			$result = array( 'status' => 'draft' );
		}

		// Add tags.
		if ( ! empty( $lists ) ) {
			$bc[ 'query' ][ 'tags_include' ] = $lists;
		}

		if ( isset( $email_id ) ) {

			if ( $schedule === 'immediately' ) {
				$broadcast = $this->api->post( '/broadcasts', $bc );
			}

			newsletterglue_add_campaign_data( $post_id, $subject, $this->prepare_message( $result ), $email_id );

			return $email_id;
		}
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

			if ( $result['status'] == 'sent' ) {
				$output[ 'status' ] 	= 200;
				$output[ 'type'   ] 	= 'success';
				$output[ 'message' ] 	= __( 'Sent', 'newsletter-glue' );
			}

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

		$this->api = new NGL_Sendgrid_API( $this->api_key );

		if ( ! empty( $list_id ) ) {

			$list_ids = array();
			$contacts = array();

			$contact = array(
				'email'	=> $email,
			);

			if ( isset( $name ) ) {
				$fname = '';
				$lname = '';
				$name_array = $array = explode( ' ', $name, 2 );
				$fname = $name_array[0];
				$lname = isset( $name_array[1] ) ? $name_array[1] : '';
				$contact[ 'first_name' ] = $fname;
				$contact[ 'last_name' ]  = $lname;
			}

			$contacts[] = $contact;

			$list_ids[] = $list_id;

			if ( isset( $extra_list ) && ! empty( $extra_list_id ) ) {
				$list_ids[] = $extra_list_id;
			}

			$args = array(
				'list_ids'	=> $list_ids,
				'contacts'	=> $contacts,
			);

			$result = $this->api->put( '/marketing/contacts', $args );

		}

		return $result;

	}

}
