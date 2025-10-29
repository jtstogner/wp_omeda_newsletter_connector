<?php
/**
 * Brevo.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main Class.
 */
class NGL_Brevo extends NGL_Abstract_Integration {

	public $app		= 'brevo';
	public $api_key = null;
	public $api 	= null;

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Include needed files.
		include_once 'lib/BrevoApiClient.php';

		$this->get_api_key();

		add_filter( 'newsltterglue_brevo_html_content', array( $this, 'html_content' ), 10, 2 );
	}

	/**
	 * Get API Key.
	 */
	public function get_api_key() {

		$integrations = get_option( 'newsletterglue_integrations' );
		$integration  = isset( $integrations[ $this->app ] ) ? $integrations[ $this->app] : '';

		$this->api_key 		= isset( $integration[ 'api_key' ] ) ? $integration[ 'api_key' ] : '';

	}

	public function get_subscriber( $subscriber_id ) {
		return $this->api->getUser( $subscriber_id );
	}

	/**
	 * Add Integration.
	 */
	public function add_integration( $args = array() ) {

		$args 		= $this->get_connection_args( $args );

		$api_key 	= $args[ 'api_key' ];
		$api_url 	= $args[ 'api_url' ];

		$this->api       = new NGL_BrevoApiClient( $api_key );

		// Check if account is valid.
		$account_api = $this->api->getAccount();

		$valid_account = isset( $account_api[ 'email' ] ) ? true : false;

		if ( ! $valid_account ) {

			$this->remove_integration();

			$result = array( 'response' => 'invalid' );

			delete_option( 'newsletterglue_brevo' );

		} else {

			if ( ! $this->already_integrated( $this->app, $api_key ) ) {
				$this->save_integration( $api_key, $account_api );
			}

			$result = array( 'response' => 'successful' );

			update_option( 'newsletterglue_brevo', $account_api );

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

		$name = isset( $account[ 'companyName' ] ) ? $account[ 'companyName' ] : newsletterglue_get_default_from_name();

		$integrations[ $this->app ][ 'connection_name' ] = sprintf( __( '%s – %s', 'newsletter-glue' ), $name, newsletterglue_get_name( $this->app ) );

		update_option( 'newsletterglue_integrations', $integrations );

		// Get verified email.
		$senders = $this->get_senders();
		$verified_email = isset( $account[ 'email' ] ) ? $account[ 'email' ] : '';
		if ( ! empty( $senders ) ) {
			foreach( $senders as $key => $data ) {
				if ( isset( $data[ 'email' ] ) ) {
					$verified_email = $data[ 'email' ];
					break;
				}
			}
		}

		// Add default options.
		$globals = get_option( 'newsletterglue_options' );

		$options = array(
			'from_name' 	=> $name,
			'from_email'	=> $verified_email,
		);

		foreach( $options as $key => $value ) {
			$globals[ $this->app ][ $key ] = $value;
		}

		update_option( 'newsletterglue_options', $globals );

		update_option( 'newsletterglue_admin_name', $name );

		update_option( 'newsletterglue_admin_address', isset( $account[ 'address' ][ 'street' ] ) ? $account[ 'address' ][ 'street' ] : '' );
	}

	/**
	 * Connect.
	 */
	public function connect() {

		$this->api = new NGL_BrevoApiClient( $this->api_key );

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

		$this->api = new NGL_BrevoApiClient( $this->api_key );

		$senders = $this->get_senders();

		// Check if email is a valid sender.
		$verified = false;
		foreach( $senders as $key => $data ) {
			if ( isset( $data[ 'email' ] ) ) {
				if ( $email == $data[ 'email' ] && $data['active'] == true ) {
					$verified = true;
				}
			}
		}

		if ( $verified ) {

			$response = array(
				'success'	=> '<strong>' . __( 'Verified', 'newsletter-glue' ) . '</strong>',
			);

		} else {

			$response = array(
				'failed'			=> __( 'Not verified', 'newsletter-glue' ),
				'failed_details'	=> '<a href="https://app.brevo.com/senders/" target="_blank" class="ngl-link-inline-svg">' . __( 'Verify email now', 'newsletter-glue' ) . ' <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a> <a href="https://newsletterglue.com/docs/email-verification-my-email-is-not-verified/" target="_blank" class="ngl-link-inline-svg">' . __( 'Learn more', 'newsletter-glue' ) . ' <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a>',
			);

		}

		return $response;

	}

	/**
	 * Get form defaults.
	 */
	public function get_form_defaults() {

		$this->api = new NGL_BrevoApiClient( $this->api_key );

		$defaults = array();

		return $defaults;
	}

	/**
	 * Get Senders.
	 */
	public function get_senders() {
		$senders = $this->api->getSenders();
		if ( isset( $senders[ 'senders' ] ) ) {
			return $senders[ 'senders' ];
		}
	}

	/**
	 * Get Lists.
	 */
	public function get_lists() {
		$_lists = array();

		$lists = $this->api->getAllLists();

		if ( isset( $lists[ 'lists' ] ) ) {
			foreach( $lists[ 'lists' ] as $key => $data ) {
				$_lists[ $data[ 'id' ] ] = $data[ 'name' ];
			}
		}

		asort( $_lists );

		return $_lists;
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
		$lists      	= isset( $data['lists'] ) && ! empty( $data['lists'] ) && is_array( $data['lists'] ) ? array_map( 'intval', $data['lists'] ) : '';

        // Force draft.
        if ( ! empty( $this->forced_state ) ) {
            if ( $this->forced_state === 'draft' ) {
                $schedule = 'draft';
            }
        }

		$subject = apply_filters( 'newsletterglue_email_subject_line', $subject, $post, $data, $test, $this );

		$this->api  = new NGL_BrevoApiClient( $this->api_key );

		// At least set lists.
		if ( empty( $lists ) ) {
			$_lists 	= $this->get_lists();
			$lists 		= array_keys( $_lists );
		}

		// Empty content.
		if ( $test && isset( $post->post_status ) && $post->post_status === 'auto-draft' ) {

			$response['fail'] = $this->nothing_to_send();

			return $response;
		}

		// If we are sending test, get the email from globals.
		if ( $test && empty( $data['from_email'] ) ) {
			$from_email = newsletterglue_get_option( 'from_email', 'brevo' );
		}

		// Verify domain.
		$senders = $this->get_senders();

		$verified = false;
		foreach( $senders as $key => $sender_info ) {
			if ( isset( $sender_info[ 'email' ] ) ) {
				if ( $from_email == $sender_info[ 'email' ] ) {
					$verified = true;
				}
			}
		}

		if ( ! $verified ) {

			$result = array(
				'fail'	=> __( 'Your <strong>From Email</strong> address isn&rsquo;t verified.', 'newsletter-glue' ) . '<br />' . '<a href="https://app.brevo.com/senders/" target="_blank" class="ngl-link-inline-svg">' . __( 'Verify email now', 'newsletter-glue' ) . ' <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a> <a href="https://newsletterglue.com/docs/email-verification-my-email-is-not-verified/" target="_blank" class="ngl-link-inline-svg">' . __( 'Learn more', 'newsletter-glue' ) . ' <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a>',
			);

			if ( ! $test ) {
				newsletterglue_add_campaign_data( $post_id, $subject, $this->prepare_message( $result ) );
			}

			return $result;

		}

		// Prepare campaign attributes.
		$campaign = array(
			'sender'		=> array(
				'name'	=> ngl_safe_title( $from_name ),
				'email'	=> $from_email,
			),
			'name'			=> $subject,
			'htmlContent'	=> newsletterglue_generate_content( $post, $subject, $this->app ),
			'subject'		=> $subject,
			'replyTo'		=> $from_email,
			'recipients'	=> array(
				'listIds'	=> $lists,
			),
		);

		// Create a campaign.
		$result = $this->api->createCampaign( $campaign );

		// Handle errors with creating this campaign.
		if ( isset( $result[ 'code' ] ) ) {

			if ( $test ) {

				if ( $result[ 'code' ] == 'account_under_validation' ) {
					$errors[ 'fail' ] = sprintf( __( 'Your Brevo account is being validated. You can&rsquo;t create another campaign.<br />%s', 'newsletter-glue' ),
						'<a href="https://help.brevo.com/hc/en-us/articles/209408165-How-to-reactivate-my-marketing-platform-after-a-suspension" target="_blank" class="ngl-link-inline-svg">' . __( 'Learn more', 'newsletter-glue' ) . ' <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a>' );
				} else {
					$errors[ 'fail' ] = $result[ 'message' ];
				}

				return $errors;

			}

		} else {

			// Campaign created.
			$campaign_id = $result[ 'id' ];

			// Send campaign as test then delete it.
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

				// Send campaign to a test email.
				$result = $this->api->sendCampaignTest( $campaign_id, array( 'emailTo' => $test_emails ) );

				// Validate the latest response.
				if ( isset( $result[ 'code' ] ) ) {
					$response[ 'fail' ] = sprintf( __( 'Email address isn&rsquo;t an existing contact.<br />Brevo only sends test emails to existing contacts. %s', 'newsletter-glue' ), 
					'<a href="https://app.brevo.com/contact/list" target="_blank" class="ngl-link-inline-svg">' . __( 'Add new contact', 'newsletter-glue' ) . ' <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a>' );
				} else {
					$response[ 'success' ] = $this->get_test_success_msg();
				}

				// Keep one campaign only for test.
				$last_test_id = get_post_meta( $post_id, '_ngl_last_test', true );
				if ( $last_test_id ) {
					$this->api->deleteCampaign( $last_test_id );
				}
				update_post_meta( $post_id, '_ngl_last_test', $campaign_id );

				return $response;

			} else {

				if ( $schedule === 'draft' ) {

					$result = array(
						'status' => 'draft'
					);

				}

				if ( $schedule === 'immediately' ) {
					$result = $this->api->sendCampaign( $campaign_id );
				}

				newsletterglue_add_campaign_data( $post_id, $subject, $this->prepare_message( $result ), $campaign_id );

				return $result;

			}

		}

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

		} else {

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

		$fname = '';
		$lname = '';

		if ( isset( $name ) ) {
			$name_array = $array = explode( ' ', $name, 2 );
			$fname = $name_array[0];
			$lname = isset( $name_array[1] ) ? $name_array[1] : '';
		}

		$this->api  = new NGL_BrevoApiClient( $this->api_key );

		$attributes = new stdClass();
		$attributes->FIRSTNAME = trim( $fname );
		$attributes->LASTNAME = trim( $lname );

		$user = array(
			'email'				=> $email,
			'updateEnabled'		=> true,
			'attributes'		=> $attributes,
		);

		$listIds = array();

		if ( ! empty( $list_id ) ) {
			$listIds[] = $list_id;
		}
		if ( isset( $extra_list ) && ! empty( $extra_list_id ) ) {
			$listIds[] = $extra_list_id;
		}

		if ( ! empty( $listIds ) ) {
			$user[ 'listIds' ] = $listIds;
		}

		$result = $this->api->createUser( $user );

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
			'helper'		=> '<a href="https://app.brevo.com/settings/keys/api" target="_blank" class="ngl-link-inline-svg">' . __( 'Get API key', 'newsletter-glue' ) . ' [externallink]</a>',
			'type'			=> 'password',
		) );

	}

	/**
	 * Get lists compat.
	 */
	public function _get_lists_compat() {
		$this->api = new NGL_BrevoApiClient( $this->api_key );
		return $this->get_lists();
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
	 * Code supported tags for this ESP.
	 */
	public function get_tag( $tag, $post_id = 0, $fallback = null ) {

		switch ( $tag ) {
			case 'unsubscribe_link' :
				return '{{ unsubscribe }}';
			break;
			case 'first_name' :
				return '{{ contact.FIRSTNAME }}';
			break;
			case 'last_name' :
				return '{{ contact.LASTNAME }}';
			break;
			case 'email' :
				return '{{ contact.EMAIL }}';
			break;
			case 'update_preferences' :
				return '{{ update_profile }}';
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
				'title'     => __( 'Lists', 'newsletter-glue' ),
				'help'		=> __( 'Who receives your email.', 'newsletter-glue' ),
				'is_multi'	=> true,
			),
		);
	}
	
	/**
	 * Get custom fields of esp
	 */
	public function get_custom_fields() {
		$_fields = array();

		$this->api = new NGL_BrevoApiClient( $this->api_key );
		$response = $this->api->getAttributes();

		if( ! empty( $response ) && is_array( $response ) && isset( $response[ 'attributes' ] ) ) {
			foreach( $response[ 'attributes' ] as $attribute ) {
				$_fields[] = array( 'value' => $attribute[ 'name' ], 'label' => $attribute[ 'name' ] );
			}
		}

		if( count( $_fields ) ) {
			array_multisort( array_column( $_fields, 'label' ), SORT_ASC, $_fields );
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

			foreach( $conditions as $c => $condition ) {
				$key          = $condition->key;
				$operator     = $condition->operator;
				$value        = $condition->value;
				$relationship = $condition->relationship;

				if( $c > 0 ) {
					$conditionQuery .= $relationship == "AND" ? " and " : " or ";
				}

				$key = "contact.$key";
				if( ! is_numeric( $value ) ) {
					if( strval( strtolower( $value ) ) === 'true' ) {
						$value = 'true';
					} else if( strval( strtolower( $value ) ) === 'false' ) {
						$value = 'false';
					} else if( DateTime::createFromFormat( "Y-m-d", $value ) !== false ) {
						$key .= ' and ' . $key . ' | time_parse:"02-01-2006"';
						$value = '"' . $value . '" | time_parse:"2006-1-02"';
					} else {
						$value = '"' . $value . '"';
					}
				}

				if( $operator == 'eq' ) {
					$conditionQuery .= "( $key == $value )";
				} else if( $operator == 'neq' ) {
					$conditionQuery .= "( $key != $value )";
				} else if( $operator == 'lt' ) {
					$conditionQuery .= "( $key < $value )";
				} else if( $operator == 'gt' ) {
					$conditionQuery .= "( $key > $value )";
				} else if( $operator == 'lte' ) {
					$conditionQuery .= "( $key <= $value )";
				} else if( $operator == 'gte' ) {
					$conditionQuery .= "( $key >= $value )";
				} else if( $operator == 'con' ) {
					$conditionQuery .= "( $value in $key | join:',' )";
				} else if( $operator == 'ncon' ) {
					$conditionQuery .= "( not ( $value in $key | join:',' ) )";
				} else if( $operator == 'ex' ) {
					$conditionQuery .= "( $key )";
				} else if( $operator == 'nex' ) {
					$conditionQuery .= "( not $key )";
				}
			}

			if( ! empty( $conditionQuery ) ) {
				$content = "<!--{% if $conditionQuery %}-->";
					$content .= $element->outertext;
				$content .= "<!--{% endif %}-->";
				$element->outertext = $content;
			}
		}

		$output->save();

		return (string) $output;
	}

}
