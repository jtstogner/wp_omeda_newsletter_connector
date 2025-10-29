<?php
/**
 * Campaign Monitor.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main Class.
 */
class NGL_Campaignmonitor extends NGL_Abstract_Integration {

	public $app 	= 'campaignmonitor';
	public $api_key = null;
	public $api_secret = null;
	public $api 	= null;
	public $lists 	= array();

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Include needed files.
		include_once 'lib/csrest_general.php';
		include_once 'lib/csrest_campaigns.php';
		include_once 'lib/csrest_lists.php';
		include_once 'lib/csrest_clients.php';
		include_once 'lib/csrest_subscribers.php';

		$this->get_api_key();

		add_filter( 'newsletterglue_email_content_campaignmonitor', array( $this, 'newsletterglue_email_content_campaignmonitor' ), 10, 3 );

		add_filter( 'newsltterglue_campaignmonitor_html_content', array( $this, 'html_content' ), 10, 2 );
	}

	/**
	 * Get API Key.
	 */
	public function get_api_key() {
		$integrations = get_option( 'newsletterglue_integrations' );
		$integration  = isset( $integrations[ $this->app ] ) ? $integrations[ $this->app] : '';
		$this->api_key = isset( $integration[ 'api_key' ] ) ? $integration[ 'api_key' ] : '';
		$this->api_secret = isset( $integration[ 'api_secret' ] ) ? $integration[ 'api_secret' ] : '';
	}

	/**
	 * Add Integration.
	 */
	public function add_integration( $args = array() ) {

		$args 		= $this->get_connection_args( $args );

		$api_key 	= $args[ 'api_key' ];
		$api_secret 	= $args[ 'api_secret' ];

		$this->api = new CS_REST_Clients( $api_secret, array( 'api_key' => $api_key ) );

		$result = $this->api->get();

		if ( $result->was_successful() ) {
			$client = json_decode( json_encode( $result->response ), true );
		}

		$valid_account = isset( $client ) && isset( $client[ 'BasicDetails' ][ 'ClientID' ] ) ? true : false;

		if ( ! $valid_account ) {

			$this->remove_integration();

			$result = array( 'response' => 'invalid' );

			delete_option( 'newsletterglue_campaignmonitor' );

		} else {

			$account[ 'Name' ]         = isset( $client[ 'BasicDetails' ][ 'CompanyName' ] ) ? $client[ 'BasicDetails' ][ 'CompanyName' ] : '';
			$account[ 'ClientID' ]     = isset( $client[ 'BasicDetails' ][ 'ClientID' ] ) ? $client[ 'BasicDetails' ][ 'ClientID' ] : '';
			$account[ 'ClientSecret' ] = isset( $client[ 'ApiKey' ] ) ? $client[ 'ApiKey' ] : '';
			$account[ 'EmailAddress' ] = isset( $client[ 'BasicDetails' ][ 'EmailAddress' ] ) ? $client[ 'BasicDetails' ][ 'EmailAddress' ] : '';

			if( empty( $account[ 'EmailAddress' ] ) ) {
				$api = new CS_REST_General( array( 'api_key' => $api_key ) );
				$result = $api->get_primary_contact();
				if ( $result->was_successful() ) {
					$contact = json_decode( json_encode( $result->response ), true );
					$account[ 'EmailAddress' ] = isset( $contact[ 'EmailAddress' ] ) ? $contact[ 'EmailAddress' ] : '';
				}
			}
			
			if ( ! $this->already_integrated( $this->app, $api_key ) ) {
				$this->save_integration( $api_key, $account );
			}

			$result = array( 'response' => 'successful' );

			update_option( 'newsletterglue_campaignmonitor', $account );

		}

		return $result;
	}

	/**
	 * Save Integration.
	 */
	public function save_integration( $api_key = '', $account = '' ) {

		// Set these in memory.
		$this->api_key = $api_key;
		$this->api_secret = $account[ 'ClientID' ];

		delete_option( 'newsletterglue_integrations' );

		$integrations = get_option( 'newsletterglue_integrations' );

		$integrations[ $this->app ] = array();
		$integrations[ $this->app ][ 'api_key' ] = $api_key;
		$integrations[ $this->app ][ 'api_secret' ] = $account[ 'ClientID' ];

		$name = isset( $account[ 'Name' ] ) ? $account[ 'Name' ] : newsletterglue_get_default_from_name();

		$integrations[ $this->app ][ 'connection_name' ] = sprintf( __( '%s – %s', 'newsletter-glue' ), $name, newsletterglue_get_name( $this->app ) );

		update_option( 'newsletterglue_integrations', $integrations );

		// Add default options.
		$globals = get_option( 'newsletterglue_options' );

		$options = array(
			'from_name' 	=> $name,
			'from_email'	=> isset( $account[ 'EmailAddress' ] ) ? $account[ 'EmailAddress' ] : '',
			'unsub'			=> $this->default_unsub(),
		);

		foreach( $options as $key => $value ) {
			$globals[ $this->app ][ $key ] = $value;
		}

		update_option( 'newsletterglue_options', $globals );

		update_option( 'newsletterglue_admin_name', $name );

	}

	/**
	 * Get lists.
	 */
	public function get_lists() {
		$_lists = array();

		$request = new CS_REST_Clients( $this->api_secret, array( 'api_key' => $this->api_key ) );
		$api   = $request->get_lists();
		$resp  = (array) $api->response;

		foreach( $resp as $key => $data ) {
			$_lists[ $data->ListID ] = $data->Name;
		}

		$this->lists = $_lists;

		asort( $_lists );

		return $_lists;
	}

	/**
	 * Get segments.
	 */
	public function get_segments() {
		$_segments = array();

		$request = new CS_REST_Clients( $this->api_secret, array( 'api_key' => $this->api_key ) );
		$api   = $request->get_segments();
		$resp  = (array) $api->response;

		if ( ! $_segments ) {
				foreach( $resp as $key => $data ) {
					$_segments[ $data->SegmentID ] = $data->Title;
				}
		}

		if ( ! empty( $_segments ) ) {
			asort( $_segments );
		}

		return $_segments ? $_segments : array();

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
        $reply_email    = isset( $data['reply_email'] ) ? $data['reply_email'] : $from_email;
		$schedule   	= isset( $data['schedule'] ) ? $data['schedule'] : 'immediately';
		$lists			= isset( $data['lists'] ) && ! empty( $data['lists'] ) ? $data['lists'] : '';
		$segments		= isset( $data['segments'] ) && ! empty( $data['segments'] ) ? $data['segments'] : '';

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

		// Exit early if email is invalid.
		if ( $test ) {

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

		}

		$api  = new CS_REST_Campaigns( '', array( 'api_key' => $this->api_key ) );

		// Create html file.
		$filename	= uniqid() . '.html';
		$uploaddir 	= wp_upload_dir();
		$uploadfile = $uploaddir['path'] . '/' . $filename;
		$htmlurl 	= $uploaddir['url'] . '/' . $filename;
		$handle 	= fopen( $uploadfile, 'w+' ); // phpcs:ignore
		
		$htmlData = newsletterglue_generate_content( $post, $subject, $this->app );
		$htmlData = str_replace( '<!--%%', '', $htmlData );
		$htmlData = str_replace( '%%-->', '', $htmlData );

		fwrite( $handle, $htmlData ); // phpcs:ignore
		fclose( $handle );

		$campaign_info = array(
			'FromName'			=> $from_name,
			'FromEmail'			=> $from_email,
			'ReplyTo'			=> empty( $reply_email ) ? $from_email : $reply_email,
			'Name'				=> ngl_safe_title( $post->post_title ),
			'Subject'			=> $subject,
			'HtmlUrl'			=> $htmlurl,
		);

		// Add segments and/or lists.
		if ( $segments ) {
			$campaign_info[ 'SegmentIDs' ] = $segments;
		} else if ( $lists ) {
			$campaign_info[ 'ListIDs' ] = $lists;
		} else {
			// Add default lists.
			if ( $test ) {
				$lists = $this->get_lists();
				$lists = array_keys( $lists );
				$campaign_info[ 'ListIDs' ] = $lists;
			}
		}

		// Create a campaign.
		$campaign = $api->create( $this->api_secret, $campaign_info );

		$resp = (array) $campaign->response;

		// Errors.
		if ( isset( $resp['Code'] ) ) {

			if ( ! $test ) {

				wp_delete_file( $uploadfile );

				newsletterglue_add_campaign_data( $post_id, $subject, $this->prepare_message( $resp ) );

			} else {

				if ( $resp['Code'] == 310 ) {
					$response = array(
						'fail'		=> __( 'HTML content has to be valid and served via a remote URL.', 'newsletter-glue' ),
					);
				}

				if ( $resp['Code'] == 4202 ) {
					$response = array(
						'fail'		=> __( 'Email content contains JavaScript. Please remove it and try again.', 'newsletter-glue' ),
					);
				}

                if ( $resp['Code'] == 303 ) {
                    $response = array(
                        'fail'      => __( 'Duplicate campaign name. Please rename the title of the newsletter.', 'newsletter-glue' ),
                    );
                }

                if ( $resp['Code'] == 308 ) {
                    $response = array(
                        'fail'      => __( 'A Reply-To email address is required.', 'newsletter-glue' ),
                    );
                }

				wp_delete_file( $uploadfile );

				return $response;

			}

		} else {

			$campaign_id = $resp[0];
			$api  = new CS_REST_Campaigns( $campaign_id, array( 'api_key' => $this->api_key ) );

			if ( ! $test ) {

				if ( $schedule === 'draft' ) {

					$result = array(
						'status' 	=> 'draft'
					);

				}

				if ( $schedule === 'immediately' ) {

					$schedule_options = array(
						'ConfirmationEmail'		=> $from_email,
						'SendDate'				=> 'immediately',
					);

					$api_send = $api->send( $schedule_options );

					$resp = (array) $api_send->response;

					if ( isset( $resp['Code'] ) ) {

						$result = $resp;

					} else {

						$result = array(
							'status'	=> 'success',
						);
					
					}

				}

				newsletterglue_add_campaign_data( $post_id, $subject, $this->prepare_message( $result ), $campaign_id );

				wp_delete_file( $uploadfile );

				return $result;

			} else {

				// Test when campaign is created.
				$response = array();

                $test_email = $data[ 'test_email' ];
                $test_email_arr = explode( ',', $test_email );
                $test_emails = array_map( 'trim', $test_email_arr );

				$api_send_preview = $api->send_preview( $test_emails );

				$send_resp = (array) $api_send_preview->response;

				if ( isset( $send_resp['Code'] ) ) {
					$response['fail'] = __( 'Please enter a valid email address to test your campaign.', 'newsletter-glue' );
				} else {
					$response['success'] = $this->get_test_success_msg();
				}

				// When used for testing, delete the campaign.
				$api->delete();

				wp_delete_file( $uploadfile );

				return $response;

			}

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
			if ( $result['status'] == 'success' ) {
				$output[ 'status' ] 	= 200;
				$output[ 'type'   ] 	= 'success';
				$output[ 'message' ] 	= __( 'Sent', 'newsletter-glue' );
			}
		}

		if ( isset( $result['Code'] ) ) {
			if ( $result['Code'] == 303 ) {
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ] 	= __( 'Duplicate Campaign Name', 'newsletter-glue' );
			}
			if ( $result['Code'] == 304 ) {
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ] 	= __( 'Campaign Subject Required', 'newsletter-glue' );
			}
			if ( $result['Code'] == 305 ) {
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ] 	= __( 'From Name Required', 'newsletter-glue' );
			}
			if ( $result['Code'] == 307 ) {
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ] 	= __( 'Invalid From Email Address', 'newsletter-glue' );
			}
			if ( $result['Code'] == 308 ) {
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ] 	= __( 'Reply-To Address Required', 'newsletter-glue' );
			}
			if ( $result['Code'] == 310 ) {
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ] 	= __( 'HTML Content URL Required', 'newsletter-glue' );
			}
			if ( $result['Code'] == 315 ) {
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ] 	= __( 'List IDs or Segments Required', 'newsletter-glue' );
			}
			if ( $result['Code'] == 319 ) {
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ] 	= __( 'Campaign Name Required', 'newsletter-glue' );
			}
			if ( $result['Code'] == 331 ) {
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ] 	= __( 'Campaign has already been sent', 'newsletter-glue' );
			}
			if ( $result['Code'] == 332 ) {
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ] 	= __( 'No Recipients Added', 'newsletter-glue' );
			}
			if ( $result['Code'] == 333 ) {
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ] 	= __( 'No Test Campaigns Available', 'newsletter-glue' );
			}
			if ( $result['Code'] == 334 ) {
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ] 	= __( 'Campaign Pending Approval', 'newsletter-glue' );
			}
			if ( $result['Code'] == 335 ) {
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ] 	= __( 'Payment Details Required', 'newsletter-glue' );
			}
			if ( $result['Code'] == 336 ) {
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ] 	= __( 'Campaign Payment Failed', 'newsletter-glue' );
			}
			if ( $result['Code'] == 337 ) {
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ] 	= __( 'Delivery Date Cannot be in the Past', 'newsletter-glue' );
			}
			if ( $result['Code'] == 338 ) {
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ] 	= __( 'Confirm Email Required', 'newsletter-glue' );
			}
			if ( $result['Code'] == 339 ) {
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ] 	= __( 'Confirm Email Invalid', 'newsletter-glue' );
			}
			if ( $result['Code'] == 340 ) {
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ] 	= __( 'Recipient Lists Empty', 'newsletter-glue' );
			}
			if ( $result['Code'] == 355 ) {
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ] 	= __( 'Monthly Payment Failedt', 'newsletter-glue' );
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

		$user = array(
			'Name'										=> ! empty( $name ) ? $name : '',
			'EmailAddress'								=> ! empty( $email ) ? $email : '',
			'ConsentToTrack'							=> 'yes',
			'Resubscribe'								=> true,
			'RestartSubscriptionBasedAutoResponders'	=> true,
		);

		if ( $email && ! empty( $list_id ) ) {
			$api 	= new CS_REST_Subscribers( $list_id, array( 'api_key' => $this->api_key ) );
			$result = $api->add( $user );
		}

		if ( $email && isset( $extra_list ) && ! empty( $extra_list_id ) ) {
			$api 	= new CS_REST_Subscribers( $extra_list_id, array( 'api_key' => $this->api_key ) );
			$result = $api->add( $user );
		}

		return true;

	}

	/**
	 * Connect.
	 */
	public function connect() {
		
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
			'helper'		=> '<a href="https://help.campaignmonitor.com/api-keys" target="_blank" class="ngl-link-inline-svg">' . __( 'Get API key', 'newsletter-glue' ) . ' [externallink]</a>',
			'type'			=> 'password',
		) );

	}

	/**
	 * Customize content.
	 */
	public function newsletterglue_email_content_campaignmonitor( $content, $post, $subject ) {

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
		return 'https://help.campaignmonitor.com/permission-settings';
	}

	/**
	 * Replace universal tags with esp tags.
	 */
	public function html_content( $html, $post_id ) {

		$output = new simple_html_dom();
		$output->load( $html, true, false );

		// Author.
		$replace = '#template_inner a';
		foreach( $output->find( $replace ) as $key => $element ) {
			if ( $element->href === '{{ unsubscribe_link }}' ) {
				$element->outertext = '<unsubscribe>' . wp_strip_all_tags( $element->innertext ) . '</unsubscribe>';
			}
		}

		$output->save();

		$html = ( string ) $output;

		$html = str_replace( '{{ unsubscribe_link }}', '<unsubscribe>' . __( 'Unsubscribe', 'newsletter-glue' ) . '</unsubscribe>', $html );

		$html = $this->convert_tags( $html, $post_id );

		$html = $this->convert_conditions( $html );

		return $html;
	}

	/**
	 * Code supported tags for this ESP.
	 */
	public function get_tag( $tag, $post_id = 0, $fallback = null ) {

		switch ( $tag ) {
			case 'fullname' :
				return ! empty( $fallback ) ? '[fullname,fallback=' . $fallback . ']' : '[fullname]';
			break;
			case 'firstname' :
				return ! empty( $fallback ) ? '[firstname,fallback=' . $fallback . ']' : '[firstname]';
			break;
			case 'lastname' :
				return ! empty( $fallback ) ? '[lastname,fallback=' . $fallback . ']' : '[lastname]';
			break;
			case 'email' :
				return ! empty( $fallback ) ? '[email,fallback=' . $fallback . ']' : '[email]';
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
		return $this->get_lists();
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
				'title'     => __( 'Lists', 'newsletter-glue' ),
				'help'		=> __( 'Who receives your email.', 'newsletter-glue' ),
			),
			'segments'	=> array(
				'type'		=> 'select',
				'is_multi'	=> true,
				'callback' 	=> 'get_segments',
				'title'		=> __( 'Segments', 'newsletter-glue' ),
				'help'		=> __( 'A specific group of subscribers.', 'newsletter-glue' ),
			)
		);
	}

	/**
	 * Get custom fields of esp
	 */
	public function get_custom_fields() {
		
		// predefined fileds
		$_fields = array(
			array( 'value' => 'firstname', 'label' => 'First name' ),
			array( 'value' => 'lastname', 'label' => 'Last name' ),
			array( 'value' => 'fullname', 'label' => 'Full name' ),
			array( 'value' => 'email', 'label' => 'Email address' ),
		);

		if( empty( $this->lists ) ) {
			$this->lists = $this->get_lists();
		}

		foreach( $this->lists as $list_id => $name ) {
			$request  = new CS_REST_Lists( $list_id, array( 'api_key' => $this->api_key ) );
			$api = $request->get_custom_fields();
			$response = $api->response;
			if( ! empty( $response ) && is_array( $response ) ) {
				foreach( $response as $key => $data ) {
					if( isset( $data->Key ) && ! empty( $data->Key ) ) {
						$value = str_replace("[", "", $data->Key);
						$value = str_replace("]", "", $value);
						$_fields[] = array( 'label' => $name . ': ' . $value, 'value' => $value );
					}
				}
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

			$contentStart = '';
			$contentEnd   = '';

			foreach( $conditions as $condition ) {
				$key          = $condition->key;
				$operator     = $condition->operator;
				$value        = $condition->value;

				if( $operator == "ex" ) {

					$contentStart .= "[if:$key]";
	
				} else if( $operator == "nex" ) {
					
					$contentStart .= "[if:$key][else]";
	
				} else if( $operator == "eq" ) {
	
					$contentStart .= "[if:$key=$value]";
	
				} else if( $operator == "neq" ) {

					$contentStart .= "[if:$key=$value][else]";	

				}

				$contentEnd = "[endif]$contentEnd";
			}

			if( ! empty( $contentStart ) && ! empty( $contentEnd ) ) {
				$content  = "<!--%%$contentStart%%-->";
				$content .= $element->outertext;
				$content .= "<!--%%$contentEnd%%-->";
				$element->outertext = $content;
			}

		}

		$output->save();

		return ( string ) $output;
	}

}