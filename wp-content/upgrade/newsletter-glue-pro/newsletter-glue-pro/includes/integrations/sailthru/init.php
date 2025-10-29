<?php
/**
 * Sailthru.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main Class.
 */
class NGL_Sailthru extends NGL_Abstract_Integration {

	public $app 		= 'sailthru';
	public $api_key 	= null;
	public $api_secret 	= null;
	public $api 		= null;

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Include needed files.
		include_once 'lib/vendor/autoload.php';

		$this->get_api_key();

		add_filter( 'newsletterglue_email_content_sailthru', array( $this, 'newsletterglue_email_content_sailthru' ), 10, 3 );

		add_filter( 'newsltterglue_sailthru_html_content', array( $this, 'html_content' ), 10, 2 );

	}

	/**
	 * Get API Key.
	 */
	public function get_api_key() {

		$integrations = get_option( 'newsletterglue_integrations' );
		$integration  = isset( $integrations[ $this->app ] ) ? $integrations[ $this->app ] : '';

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

		$this->api  = new Sailthru_Client( $api_key, $api_secret );

		$account = $this->api->apiGet('settings');

		$valid_account = ! empty( $account ) && isset( $account[ 'customer_id' ] ) ? true : false;

		if ( ! $valid_account ) {

			$this->remove_integration();

			$result = array( 'response' => 'invalid' );

			delete_option( 'newsletterglue_sailthru' );

		} else {

			if ( ! $this->already_integrated( $this->app, $api_key ) ) {
				$this->save_integration( $api_key, $api_secret, $account );
			}

			$result = array( 'response' => 'successful' );

			update_option( 'newsletterglue_sailthru', $account );

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

		$name = newsletterglue_get_default_from_name();

		$integrations[ $this->app ][ 'connection_name' ] = sprintf( __( '%s – %s', 'newsletter-glue' ), $name, newsletterglue_get_name( $this->app ) );

		update_option( 'newsletterglue_integrations', $integrations );

		// Add default options.
		$globals = get_option( 'newsletterglue_options' );

		$options = array(
			'from_name'  => $name,
			'from_email' => ( isset( $account[ 'from_emails' ] ) && is_array( $account[ 'from_emails' ] ) && count( $account[ 'from_emails' ] ) ) ? $account[ 'from_emails' ][0] : get_option( 'admin_email' ),
			'unsub'      => $this->default_unsub(),
		);

		foreach( $options as $key => $value ) {
			$globals[ $this->app ][ $key ] = $value;
		}

		update_option( 'newsletterglue_options', $globals );

		update_option( 'newsletterglue_admin_name', $name );
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

		$this->api = new Sailthru_Client( $this->api_key, $this->api_secret );

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
			'placeholder' 	=> esc_html__( 'Enter Secret Key', 'newsletter-glue' ),
			'value'			=> isset( $integrations[ $app ]['api_secret'] ) ? $integrations[ $app ]['api_secret'] : '',
			'helper'		=> '<a href="https://my.sailthru.com/settings/api_postbacks" target="_blank" class="ngl-link-inline-svg">' . __( 'Get API key', 'newsletter-glue' ) . ' [externallink]</a>',
			'type'			=> 'password',
		) );

	}

	/**
	 * Verify email address.
	 */
	public function verify_email( $email = '' ) {

		if ( ! $email ) {
			$response = array( 'failed' => __( 'Please enter email', 'newsletter-glue' ) );
		} elseif ( ! is_email( $email ) ) {
			$response = array( 'failed'	=> __( 'Invalid email', 'newsletter-glue' ) );
		}

		if ( ! empty( $response ) ) {
			return $response;
		}

		$this->api = new Sailthru_Client( $this->api_key, $this->api_secret );

		$account = $this->api->apiGet('settings');

		// Check if email is a valid sender.
		$verified = ( isset( $account[ 'from_emails' ] ) && is_array( $account[ 'from_emails' ] ) && in_array( trim( $email ), $account[ 'from_emails' ] ) ) ? true : false;

		if ( $verified ) {

			$response = array(
				'success'	=> '<strong>' . __( 'Verified', 'newsletter-glue' ) . '</strong>',
			);

		} else {

			$response = array(
				'failed'			=> __( 'Not verified', 'newsletter-glue' ),
				'failed_details'	=> '<a href="https://my.sailthru.com/verify" target="_blank">' . __( 'Verify email now', 'newsletter-glue' ) . ' <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a> <a href="https://newsletterglue.com/docs/email-verification-my-email-is-not-verified/" target="_blank">' . __( 'Learn more', 'newsletter-glue' ) . ' <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a>',
			);

		}

		return $response;
	}

	/**
	 * Configure options array for this ESP.
	 */
	public function option_array() {
		return array(
			'lists' 	=> array(
				'type'		=> 'select',
				'callback'	=> 'get_lists',
				'title'     => __( 'Contact list', 'newsletter-glue' ),
				'help'		=> sprintf( __( 'Who receives your email. %s', 'newsletter-glue' ), '<a href="https://my.sailthru.com/lists" target="_blank">' . __( 'Manage contact lists', 'newsletter-glue' ) . '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="components-external-link__icon css-6wogo1-StyledIcon etxm6pv0" role="img" aria-hidden="true" focusable="false"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a>' ),
			),
		);
	}

	/**
	 * Get form defaults.
	 */
	public function get_form_defaults() {

		$this->api = new Sailthru_Client( $this->api_key, $this->api_secret );

		$defaults[ 'lists' ] = $this->get_lists();

		return $defaults;

	}

	/**
	 * Get lists compat.
	 */
	public function _get_lists_compat() {
		$this->api = new Sailthru_Client( $this->api_key, $this->api_secret );

		return $this->get_lists();
	}

	/**
	 * Get Lists.
	 */
	public function get_lists() {
		$_lists = array();

		$response = $this->api->getLists();

		$lists = $response[ 'lists' ];

		if ( ! empty( $lists ) ) {
			foreach( $lists as $key => $data ) {
				$_lists[ $data[ 'name' ] ] = $data[ 'name' ];
			}
		}

		return $_lists;
	}

	/**
	 * Customize content.
	 */
	public function newsletterglue_email_content_sailthru( $content, $post, $subject ) {

		// for sailthru tracking
		$content .= '<img src="{beacon_src}" width="1" height="1" style="opacity: 0;">';

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

		$unsub = str_replace( '{{ unsubscribe_link }}', "{optout_confirm_url}", $unsub );

		$content .= '<p class="ngl-unsubscribe">' . wp_kses_post( $unsub ) . '</p>';

		return $content;

	}

	/**
	 * Replace universal tags with esp tags.
	 */
	public function html_content( $html, $post_id ) {

		$html = $this->convert_tags( $html, $post_id );

		$html = $this->convert_conditions( $html );

		return $html;
	}

	/**
	 * Get email verify help.
	 */
	public function get_email_verify_help() {
		return 'https://my.sailthru.com/verify';
	}

	/**
	 * Code supported tags for this ESP.
	 */
	public function get_tag( $tag, $post_id = 0, $fallback = null ) {

		switch ( $tag ) {
			case 'beacon' :
				return "{beacon}";
			break;
			case 'beacon_src' :
				return "{beacon_src}";
			break;
			case 'beacon_ssl' :
				return '{beacon_ssl}';
			break;
			case 'beacon_src_ssl' :
				return '{beacon_src_ssl}';
			break;
			case 'beacon_url' :
				return '{beacon_url}';
			break;
			case 'email' :
				return '{email}';
			break;
			case 'name' :
				return '{name}';
			case 'first_name' :
				return '{first_name}';
			case 'last_name' :
				return '{last_name}';
			break;
			case 'birthday' :
				return '{birthday}';
			break;
			case 'source' :
				return '{source}';
			break;
			case 'emailnum' :
				return '{emailnum}';
			break;
			case 'forward_url' :
				return '{forward_url}';
			break;
			case 'unsubscribe_link' :
				return '{optout_confirm_url}';
			break;
			case 'profile' :
				return '{profile}';
			break;
			case 'public_url' :
				return '{public_url}';
			break;
			case 'signup_confirm_url' :
				return '{signup_confirm_url}';
			break;
			case 'text_url' :
				return '{text_url}';
			break;
			case 'view_url' :
				return '{view_url}';
			break;
			default :
				return apply_filters( "newsletterglue_{$this->app}_custom_tag", '', $tag, $post_id );
			break;
		}

		return false;
	}

	/**
	 * Get Locale.
	 */
	public function get_locale() {
		$options = get_option( 'newsletterglue_sailthru' );

		return isset( $options[ 'Locale' ] ) ? $options[ 'Locale' ] : 'en_US';
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

		$body = newsletterglue_generate_content( $post, $subject, $this->app );

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

			wp_mail( $test_emails, sprintf( __( '[Test] %s', 'newsletter-glue' ), $subject ), $body ); // phpcs:ignore

			$response['success'] = $this->get_test_success_msg();

			return $response;
		}

		$blast_id = null;

		if ( $schedule === 'draft' ) {
			
			$status = array( 'status' => 'draft' );

		} else {
			
			$this->api = new Sailthru_Client( $this->api_key, $this->api_secret );
			
			$name         = $subject;
			$schedule     = $schedule === 'immediately' ? 'now' : 'draft';
			$content_html = $body;
			$content_text = wp_strip_all_tags( $body );
			
			$response = $this->api->scheduleBlast($name, $lists, $schedule, $from_name, $from_email, $subject, $content_html, $content_text);

			if( ! empty( $response ) && isset( $response[ 'blast_id' ] ) ) {
				$blast_id = $response[ 'blast_id' ];
				$status = array( 'status' => 'sent' );
			} else {
				$status = array( 'status' => 'error' );
			}
		}

		newsletterglue_add_campaign_data( $post_id, $subject, $this->get_status( $status ), $blast_id );

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

		if ( ! empty( $list_id ) ) {
			$this->api = new Sailthru_Client( $this->api_key, $this->api_secret );

			$options = [
				'lists' => [
					$list_id => 1
				]
			];

			if( isset( $name ) ) {
				$options[ 'vars' ] = [
					'name' => $name
				];
			}

			$response = $this->api->saveUser($email, $options);
			if( isset( $response[ 'ok' ] ) && $response[ 'ok' ] === true ) {
				return true;
			}
		}

		return -1;

	}

	/**
	 * Get custom fields of esp
	 */
	public function get_custom_fields() {

		// Predefined fields
		$_fields = array(
			array( 'label' => 'First Name', 'value' => 'first_name' ),
			array( 'label' => 'Name', 'value' => 'name' ),
			array( 'label' => 'Email', 'value' => 'email' ),
			array( 'label' => 'Last Name', 'value' => 'last_name' ),
			array( 'label' => 'Birthday', 'value' => 'birthday' ),
			array( 'label' => 'Source', 'value' => 'source' ),
		);

		// no direct api for getting custom fields. We will use manual input here.

		if( count( $_fields ) ) {
			array_multisort( array_column( $_fields, 'label' ), SORT_ASC, $_fields );
			array_unshift( $_fields, array( 'value' => 'manual', 'label' => 'Set manual condition' ) );
			array_unshift( $_fields, array( 'value' => '', 'label' => 'Select an option' ) );
		}

		return $_fields;
	}

	/**
	 * Convert conditional statements of esp
	 */
	public function convert_conditions( $html ) {
		$output = new simple_html_dom();
		$output->load( $html, true, false );

		$replace = '[data-conditions]';
		foreach( $output->find( $replace ) as $key => $element ) {

			$conditions = json_decode( $element->{ 'data-conditions' } );
			$element->removeAttribute( 'data-conditions' );
			
			$conditionQuery = '';

			foreach( $conditions as $condition ) {
				$key          = $condition->key;
				$key_manual   = $condition->key_manual;
				$operator     = $condition->operator;
				$value        = $condition->value;
				$relationship = $condition->relationship;

				$conditionQuery .= $relationship == "AND" ? " && " : " || ";

				if( $key == 'manual' && !empty( $key_manual ) && strpos( $value, 'List:' ) === 0 ) {

					$value = substr($value, 5);
					$values = explode( ",", $value );
					$key = $key_manual;

					foreach( $values as $value ) {
						$value = '"' . trim( $value ) . '"';

						if( $operator == 'eq' ) {
							$conditionQuery .= "contains($key, $value) && ";
						} else if( $operator == 'neq' ) {
							$conditionQuery .= "!contains($key, $value) && ";
						} else if( $operator == 'ex' ) {
							$conditionQuery .= "contains($key, $value) || ";
						} else if( $operator == 'nex' ) {
							$conditionQuery .= "!contains($key, $value) || ";
						}
					}

					$conditionQuery = rtrim( $conditionQuery, " && " );
					$conditionQuery = rtrim( $conditionQuery, " || " );

				} else {

					if( $key == 'manual' && !empty( $key_manual ) ) {
						$key = $key_manual;
					}

					if( ! is_numeric( $value ) ) {
						if( strval( strtolower( $value ) ) === 'true' ) {
							$value = 'true';
						} else if( strval( strtolower( $value ) ) === 'false' ) {
							$value = 'false';
						} else if( DateTime::createFromFormat( "Y-m-d", $value ) !== false ) {
							$key = "date('YYYY-MM-DD', $key)";
							$value = "date('YYYY-MM-DD', $value)";
						} else {
							if( $operator != 'and' && $operator != 'or' ) {
								$value = '"' . $value . '"';
							}
						}
					}

					if( $operator == 'eq' ) {
						$conditionQuery .= $key . ' == ' . $value;
					} else if( $operator == 'neq' ) {
						$conditionQuery .= $key . ' != ' . $value;
					} else if( $operator == 'lt' ) {
						$conditionQuery .= $key . ' < ' . $value;
					} else if( $operator == 'gt' ) {
						$conditionQuery .= $key . ' > ' . $value;
					} else if( $operator == 'lte' ) {
						$conditionQuery .= $key . ' <= ' . $value;
					} else if( $operator == 'gte' ) {
						$conditionQuery .= $key . ' >= ' . $value;
					} else if( $operator == 'con' ) {
						$conditionQuery .= "contains($key, $value)";
					} else if( $operator == 'ncon' ) {
						$conditionQuery .= "!contains($key, $value)";
					} else if( $operator == 'ex' ) {
						$conditionQuery .= $key;
					} else if( $operator == 'nex' ) {
						$conditionQuery .= "!$key";
					} else if( $operator == 'and' ) {
						$conditionQuery .= $key . ' >= ' . $value;
					} else if( $operator == 'or' ) {
						$conditionQuery .= $key . ' >= ' . $value;
					}
				}
			}

			$conditionQuery = ltrim( $conditionQuery, " && " );
			$conditionQuery = ltrim( $conditionQuery, " || " );

			if( ! empty( $conditionQuery ) ) {
				$content = '<!--{if ' . $conditionQuery . ' }-->';
					$content .= $element->outertext;
				$content .= '<!--{/if}-->';
				$element->outertext = $content;
			}
		}

		$output->save();

		return (string) $output;
	}

}
