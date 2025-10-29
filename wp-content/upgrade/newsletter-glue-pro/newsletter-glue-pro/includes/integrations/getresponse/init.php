<?php
/**
 * GetResponse.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main Class.
 */
class NGL_Getresponse extends NGL_Abstract_Integration {

	public $app		= 'getresponse';
	public $api_url = null;
	public $api_key = null;
	public $api 	= null;

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Include needed files.
		include_once 'lib/client.php';

		$this->get_api_key();

		add_filter( 'newsltterglue_getresponse_html_content', array( $this, 'html_content' ), 10, 2 );
	}

	/**
	 * Get API Key.
	 */
	public function get_api_key() {

		$integrations = get_option( 'newsletterglue_integrations' );
		$integration  = isset( $integrations[ $this->app ] ) ? $integrations[ $this->app] : '';

		$this->api_key 		= isset( $integration[ 'api_key' ] ) ? $integration[ 'api_key' ] : '';
	}

	/**
	 * Add Integration.
	 */
	public function add_integration( $args = array() ) {

		$args 		= $this->get_connection_args( $args );

		$api_key 	= $args[ 'api_key' ];
		$api_url 	= $args[ 'api_url' ];

		$this->api = new NGL_GetResponse_API( $api_key );

		$account = $this->api->get( '/accounts' );

		if ( ! isset( $account[ 'email' ] ) ) {

			$this->remove_integration();

			$result = array( 'response' => 'invalid' );

			delete_option( 'newsletterglue_getresponse' );

		} else {

			if ( ! $this->already_integrated( $this->app, $api_key ) ) {
				$this->save_integration( $api_key, $account );
			}

			$result = array( 'response' => 'successful' );

			update_option( 'newsletterglue_getresponse', $account );

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
		$integrations[ $this->app ][ 'api_key' ] = $api_key;

		$integrations[ $this->app ][ 'connection_name' ] = newsletterglue_get_name( $this->app );

		update_option( 'newsletterglue_integrations', $integrations );

		// Add default options.
		$globals = get_option( 'newsletterglue_options' );

		$options = array(
			'from_name' 	=> newsletterglue_get_default_from_name(),
			'from_email'	=> isset( $account[ 'email' ] ) ? $account[ 'email' ] : '',
		);

		foreach( $options as $key => $value ) {
			$globals[ $this->app ][ $key ] = $value;
		}

		update_option( 'newsletterglue_options', $globals );

		update_option( 'newsletterglue_admin_address', isset( $account[ 'street' ] ) ? $account[ 'street' ] : '' );
	}

	/**
	 * Connect.
	 */
	public function connect() {

		$this->api = new NGL_GetResponse_API( $this->api_key );

	}

	/**
	 * Get form defaults.
	 */
	public function get_form_defaults() {

		$this->api = new NGL_GetResponse_API( $this->api_key );

		$defaults = array();

		$defaults[ 'lists' ] = $this->get_lists();

		return $defaults;

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

		$this->api = new NGL_GetResponse_API( $this->api_key );

		$senders = $this->api->get( '/from-fields' );

		// Check if email is a valid sender.
		$verified = false;
		if ( $senders ) {
			foreach( $senders as $key => $data ) {
				if ( isset( $data[ 'email' ] ) && trim( $email ) === trim( $data[ 'email' ] ) && $data[ 'isActive' ] == 'true' ) {
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
				'failed_details'	=> '<a href="https://app.getresponse.com/email-addresses/" target="_blank">' . __( 'Verify email now', 'newsletter-glue' ) . ' <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a> <a href="https://newsletterglue.com/docs/email-verification-my-email-is-not-verified/" target="_blank">' . __( 'Learn more', 'newsletter-glue' ) . ' <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a>',
			);

		}

		return $response;

	}

	/**
	 * Get Lists.
	 */
	public function get_lists() {
		$_lists = array();

		$lists = $this->api->get( '/campaigns' );

		if ( isset( $lists ) ) {
			foreach( $lists as $key => $data ) {
				$_lists[ $data[ 'campaignId' ] ] = $data[ 'name' ];
			}
		}

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
		$campaign		= isset( $data['lists'] ) ? $data['lists'] : '';
		$schedule  	 	= isset( $data['schedule'] ) ? $data['schedule'] : 'immediately';
		$fromFieldId    = '';

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

			$body = newsletterglue_generate_content( $post, $subject, 'activecampaign' );

			wp_mail( $test_emails, sprintf( __( '[Test] %s', 'newsletter-glue' ), $subject ), $body ); // phpcs:ignore

			$response['success'] = $this->get_test_success_msg();

			return $response;

		}

		$this->api = new NGL_GetResponse_API( $this->api_key );

		$senders = $this->api->get( '/from-fields' );
		foreach( $senders as $key => $sender ) {
			if ( $sender[ 'email' ] == $from_email ) {
				$fromFieldId = $sender[ 'fromFieldId' ];
			}
		}

		$args = array(
			'content'	=> array(
				'html'	=> newsletterglue_generate_content( $post, $subject, $this->app ),
			),
			'subject'	=> $subject,
			'campaign'	=> array(
				'campaignId'	=> $campaign
			),
			'fromField'	=> array(
				'fromFieldId' 	=> $fromFieldId,
			),
			'replyTo'	=> array(
				'fromFieldId' 	=> $fromFieldId,
			),
			'editor'	=> 'custom',
			'type'		=> $schedule === 'immediately' ? 'broadcast' : 'draft',
			'name'		=> sprintf( __( 'Newsletter Glue - Campaign %s', 'newsletter-glue' ), uniqid() ),
			'sendSettings' => array(
				'selectedCampaigns'	=> array( $campaign ),
			),
		);

		$newsletter = $this->api->post( '/newsletters', $args );

		// Store the status.
		if ( isset( $newsletter[ 'newsletterId' ] ) ) {

			if ( $schedule === 'draft' ) {
				$status = array( 'status' => 'draft' );
			} else {
				$status = array( 'status' => 'sent' );
			}

			newsletterglue_add_campaign_data( $post_id, $subject, $this->prepare_message( ( array ) $status ), $newsletter[ 'newsletterId' ] );

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
	 * Add user to this ESP.
	 */
	public function add_user( $data ) {
		extract( $data );

		if ( empty( $email ) ) {
			return -1;
		}

		$this->api = new NGL_GetResponse_API( $this->api_key );

		if ( ! empty( $list_id ) ) {

			$args = array(
				'campaign'		=> array(
					'campaignId'	=> $list_id
				),
				'email'			=> $email,
			);

			if ( ! empty( $name ) ) {
				$args[ 'name' ] = esc_html( $name );
			}

			$result = $this->api->post( '/contacts', $args );

			if ( isset( $extra_list ) && ! empty( $extra_list_id ) ) {
				$args['campaign']['campaignId'] = $extra_list_id;
				$result = $this->api->post( '/contacts', $args );
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
			'helper'		=> '<a href="https://app.getresponse.com/api" target="_blank" class="ngl-link-inline-svg">' . __( 'Get API key', 'newsletter-glue' ) . ' [externallink]</a>',
			'type'			=> 'password',
		) );

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
				return '[[remove]]';
			break;
			case 'list' :
				return '[[responder]]';
			break;
			case 'name' :
				return ! empty( $fallback ) ? '[[name fallback="' . $fallback . '"]]' : '[[name]]';
			break;
			case 'first_name' :
				return ! empty( $fallback ) ? '[[firstname fallback="' . $fallback . '"]]' : '[[firstname]]';
			break;
			case 'last_name' :
				return ! empty( $fallback ) ? '[[lastname fallback="' . $fallback . '"]]' : '[[lastname]]';
			break;
			case 'email' :
				return '[[email]]';
			break;
			case 'update_preferences' :
				return '[[change]]';
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
				'title'     => __( 'Campaign (List)', 'newsletter-glue' ),
				'help'		=> __( 'Who receives your email.', 'newsletter-glue' ),
			),
		);
	}

	/**
	 * Get custom tags of esp
	 */
	public function get_custom_tags() {
		$_tags = array();

		$this->api = new NGL_GetResponse_API( $this->api_key );
		$response = $this->api->get( '/tags' );

		if( ! empty( $response ) && is_array( $response ) ) {
			foreach( $response as $tag ) {
				if( isset( $tag[ 'name' ] ) && ! empty( $tag[ 'name' ] ) ) {
					$_tags[] = array( 'value' => $tag[ 'name' ], 'label' => $tag[ 'name' ] );
				}
			}
		}

		if( count( $_tags ) ) {
			array_multisort( array_column( $_tags, 'label' ), SORT_ASC, $_tags );
		}

		return $_tags;
	}

	/**
	 * Get custom fields of esp
	 */
	public function get_custom_fields() {
		$_fields = array();

		$this->api = new NGL_GetResponse_API( $this->api_key );
		$response = $this->api->get( '/custom-fields' );

		if( ! empty( $response ) && is_array( $response ) ) {
			foreach( $response as $field ) {
				if( isset( $field[ 'name' ] ) && ! empty( $field[ 'name' ] ) ) {
					$_fields[] = array( 'value' => $field[ 'name' ], 'label' => $field[ 'name' ] );
				}
			}
		}		

		if( count( $_fields ) ) {
			array_multisort( array_column( $_fields, 'label' ), SORT_ASC, $_fields );
			array_unshift( $_fields, array( 'value' => 'tag', 'label' => 'Select a tag' ) );
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
				$key          = strtolower( $condition->key );
				$operator     = $condition->operator;
				$value        = $condition->value;
				$relationship = $condition->relationship;

				if( $c > 0 ) {
					$conditionQuery .= $relationship == "AND" ? " LOGIC_AND " : " LOGIC_OR ";
				}

				// handle tags
				if( is_array( $value ) ) {
					$tagQuery = '';

					foreach( $value as $t => $tag ) {
						if( $operator == 'eq' ) {
							$tagQuery .= " LOGIC_AND (tag STRING_EQI '$tag')";
						} else if( $operator == 'neq' ) {
							$tagQuery .= " LOGIC_AND (tag STRING_NEQI '$tag')";
						} else if( $operator == 'ex' ) {
							$tagQuery .= " LOGIC_OR (tag STRING_EQI '$tag')";
						} else if( $operator == 'nex' ) {
							$tagQuery .= " LOGIC_OR (tag STRING_NEQI '$tag')";
						}
						if( $t > 0 ) {
							$tagQuery = "($tagQuery)";
						} else {
							$tagQuery = ltrim( $tagQuery, " LOGIC_AND " );
							$tagQuery = ltrim( $tagQuery, " LOGIC_OR " );
						}
					}
					
					$conditionQuery .= $tagQuery;

				} else {

					//handle custom fields
					if( $operator == 'eq' ) {
						$op = is_numeric( $value ) ? 'NUMBER_EQ' : 'STRING_EQI';
						$conditionQuery .= "($key $op '$value')";
					} else if( $operator == 'neq' ) {
						$op = is_numeric( $value ) ? 'NUMBER_NEQ' : 'STRING_NEQI';
						$conditionQuery .= "($key $op '$value')";
					} else if( $operator == 'lt' ) {
						$conditionQuery .= "($key NUMBER_LT '$value')";
					} else if( $operator == 'gt' ) {
						$conditionQuery .= "($key NUMBER_GT '$value')";
					} else if( $operator == 'lte' ) {
						$conditionQuery .= "($key NUMBER_LEQ '$value')";
					} else if( $operator == 'gte' ) {
						$conditionQuery .= "($key NUMBER_GEQ '$value')";
					} else if( $operator == 'ex' ) {
						$conditionQuery .= "($key IS_DEFINED)";
					} else if( $operator == 'nex' ) {
						$conditionQuery .= "($key NOT_DEFINED)";
					}
				}

				if( $c > 0 ) {
					$conditionQuery = "($conditionQuery)";
				}
			}

			if( ! empty( $conditionQuery ) ) {
				$content = "<!--{{IF \"$conditionQuery\"}}-->";
					$content .= $element->outertext;
				$content .= "<!--{{ENDIF}}-->";
				$element->outertext = $content;
			}
		}

		$output->save();

		return (string) $output;
	}

}
