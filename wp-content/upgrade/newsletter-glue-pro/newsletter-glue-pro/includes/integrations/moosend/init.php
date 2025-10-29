<?php
/**
 * Moosend.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main Class.
 */
class NGL_Moosend extends NGL_Abstract_Integration {

	public $app 	= 'moosend';
	public $api_key = null;
	public $api 	= null;

	public $saved_lists = null;

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Include needed files.
		include_once 'lib/api.php';

		$this->get_api_key();

		add_filter( 'newsletterglue_email_content_moosend', array( $this, 'newsletterglue_email_content_moosend' ), 10, 3 );

		add_filter( 'newsltterglue_moosend_html_content', array( $this, 'html_content' ), 10, 2 );
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

		$this->api  = new NGL_Moosend_API( $api_key );

		// Check if account is valid.
		$senders = $this->api->get( '/senders/find_all' );

		if ( isset( $senders[ 'Context' ] ) ) {
			foreach( $senders[ 'Context' ] as $key => $data ) {
				if ( $data[ 'IsEnabled' ] == true ) {
					$account = $data;
					break;
				}
			}
		} else {
			$account = false;
		}

		if ( ! isset( $account[ 'ID' ] ) ) {

			$this->remove_integration();

			$result = array( 'response' => 'invalid' );

			delete_option( 'newsletterglue_moosend' );

		} else {

			if ( ! $this->already_integrated( $this->app, $api_key ) ) {
				$this->save_integration( $api_key, $account );
			}

			$result = array( 'response' => 'successful' );

			update_option( 'newsletterglue_moosend', $account );

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

		$name = isset( $account[ 'Name' ] ) ? $account[ 'Name' ] : newsletterglue_get_default_from_name();

		$integrations[ $this->app ][ 'connection_name' ] = sprintf( __( '%s – %s', 'newsletter-glue' ), $name, newsletterglue_get_name( $this->app ) );

		update_option( 'newsletterglue_integrations', $integrations );

		// Add default options.
		$globals = get_option( 'newsletterglue_options' );

		$options = array(
			'from_name' 	=> $name,
			'from_email'	=> isset( $account[ 'Email' ] ) ? $account[ 'Email' ] : get_option( 'admin_email' ),
			'unsub'			=> $this->default_unsub(),
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

		$this->api = new NGL_Moosend_API( $this->api_key );

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
			'helper'		=> '<a href="https://identity.moosend.com/login/" target="_blank" class="ngl-link-inline-svg">' . __( 'Get API key', 'newsletter-glue' ) . ' [externallink]</a>',
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

		$this->api = new NGL_Moosend_API( $this->api_key );

		$response = $this->api->get( '/senders/find_all' );

		// Check if email is a valid sender.
		$verified = false;
		if ( isset( $response[ 'Context' ] ) ) {
			$senders = $response[ 'Context' ];
			foreach( $senders as $key => $data ) {
				if ( isset( $data[ 'Email' ] ) && trim( $email ) === trim( $data[ 'Email' ] ) && $data[ 'IsEnabled' ] == true ) {
					$verified = true;
					break;
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
				'failed_details'	=> '<a href="https://help.moosend.com/hc/en-us/articles/208578545-How-do-I-set-up-a-new-sender-" target="_blank">' . __( 'Verify email now', 'newsletter-glue' ) . ' <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a> <a href="https://newsletterglue.com/docs/email-verification-my-email-is-not-verified/" target="_blank">' . __( 'Learn more', 'newsletter-glue' ) . ' <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a>',
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
	 * Get form defaults.
	 */
	public function get_form_defaults() {

		$this->api = new NGL_Moosend_API( $this->api_key );

		$defaults = array();

		return $defaults;

	}

	/**
	 * Get lists compat.
	 */
	public function _get_lists_compat() {
		$this->api = new NGL_Moosend_API( $this->api_key );

		return $this->get_lists();
	}

	/**
	 * Get Lists.
	 */
	public function get_lists() {
		$_lists = array();

		$request = $this->api->get( '/lists' );

		if ( isset( $request[ 'Context' ][ 'MailingLists' ] ) ) {
			$lists = $request[ 'Context' ][ 'MailingLists' ];
		}

		if ( ! empty( $lists ) ) {
			foreach( $lists as $key => $data ) {
				$_lists[ $data[ 'ID' ] ] = $data[ 'Name' ];
			}
		}

		if ( ! empty( $_lists ) ) {
			$this->saved_lists = $_lists;
		}

		asort( $_lists );

		return $_lists;
	}

	/**
	 * Get Segments.
	 */
	public function get_segments() {
		$_segments = array();

		if ( ! empty( $this->saved_lists ) ) {
			foreach( $this->saved_lists as $list_id => $name ) {
				$request = $this->api->get( "/lists/{$list_id}/segments" );
				if ( isset( $request[ 'Context' ][ 'Segments' ] ) ) {
					foreach( $request[ 'Context' ][ 'Segments' ] as $key => $data ) {
						$_segments[ $list_id . '_' . $data[ 'ID' ] ] = $data[ 'Name' ] . " ($name)";
					}
				}
			}
		}

		asort( $_segments );

		return $_segments;
	}

	/**
	 * Customize content.
	 */
	public function newsletterglue_email_content_moosend( $content, $post, $subject ) {

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

		$unsub = str_replace( '{{ unsubscribe_link }}', '#unsubscribeLink#', $unsub );

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
		return 'https://newsletterglue.com/docs/from-email-use-verified-email-address/';
	}

	/**
	 * Code supported tags for this ESP.
	 */
	public function get_tag( $tag, $post_id = 0, $fallback = null ) {

		switch ( $tag ) {
			case 'unsubscribe_link' :
				return '#unsubscribeLink#';
			break;
			case 'update_preferences' :
				return '#updateProfileLink#';
			break;
			case 'name' :
				return ! empty( $fallback ) ? '#recipient:name|' . $fallback . '#' : '#recipient:name#';
			break;
			case 'email' :
				return '#recipient:email#';
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
		$segments		= isset( $data['segments'] ) && ! empty( $data['segments'] ) ? $data['segments'] : '';
		$schedule   	= isset( $data['schedule'] ) ? $data['schedule'] : 'immediately';

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

		// Create html file.
		$filename	= uniqid() . '.html';
		$uploaddir 	= wp_upload_dir();
		$uploadfile = $uploaddir['path'] . '/' . $filename;
		$htmlurl 	= $uploaddir['url'] . '/' . $filename;
		$handle 	= fopen( $uploadfile, 'w+' ); // phpcs:ignore
		fwrite( $handle, newsletterglue_generate_content( $post, $subject, $this->app ) ); // phpcs:ignore
		fclose( $handle ); // phpcs:ignore

		// Create campaign.
		$this->api = new NGL_Moosend_API( $this->api_key );

		$args = array(
			'Name'			=> ngl_safe_title( $post->post_title ),
			'Subject'		=> $subject,
			'SenderEmail'	=> $from_email,
			'WebLocation'	=> $htmlurl,
		);

		// Prepare sending to mailing lists.
		if ( empty( $lists ) ) {
			$lists = array_keys( $this->get_lists() );
		}

		if ( ! empty( $lists ) ) {
			foreach( $lists as $list_id ) {
				$list_id_array = array(
					'MailingListID' => $list_id,
				);
				if ( ! empty( $segments ) ) {
					foreach( $segments as $segment ) {
						if ( strstr( $segment, $list_id ) ) {
							$segment_id = $segment;
							$list_id_array[ 'SegmentID' ] = str_replace( $list_id . '_', '', $segment_id );
						}
					}
				}
				$args[ 'MailingLists' ][] = $list_id_array;
			}
		}

		// Create a draft campaign.
		$campaign = $this->api->post( '/campaigns/create', $args );

		if ( $campaign && isset( $campaign[ 'Context' ] ) && isset( $campaign[ 'Code' ] ) && $campaign[ 'Code' ] == 0 ) {
			$campaign_id = $campaign[ 'Context' ];
		} else {
			$campaign_id = 0;
		}

		if ( ! $campaign_id ) {
			$status = array( 'status' => 'draft' );
		} else {
			if ( $schedule === 'draft' ) {
				$status = array( 'status' => 'draft' );
			} else {
				$status = array( 'status' => 'sent' );
				$send_campaign = $this->api->post( "/campaigns/{$campaign_id}/send" );
			}
		}

		newsletterglue_add_campaign_data( $post_id, $subject, $this->get_status( $status ), $campaign_id );

		wp_delete_file( $uploadfile );

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

		$this->api = new NGL_Moosend_API( $this->api_key );

		if ( ! empty( $list_id ) ) {

			$args = array(
				'Name'	=> isset( $name ) ? $name : '',
				'Email'	=> $email,
			);

			$result = $this->api->post( "/subscribers/{$list_id}/subscribe", $args );

			if ( isset( $extra_list ) && ! empty( $extra_list_id ) ) {
				$result = $this->api->post( "/subscribers/{$extra_list_id}/subscribe", $args );
			}

		}

		return $result;

	}

	/**
	 * Get custom fields of esp
	 */
	public function get_custom_fields() {
		$_fields = array();

		$this->api = new NGL_Moosend_API( $this->api_key );
		$response = $this->api->get('/lists');
		if ( ! empty( $response ) && is_array( $response ) && isset( $response[ 'Context' ][ 'MailingLists' ] ) ) {
			if( is_array( $response[ 'Context' ][ 'MailingLists' ] ) ) {
				foreach( $response[ 'Context' ][ 'MailingLists' ] as $list ) {
					if( isset( $list[ 'CustomFieldsDefinition' ] ) && is_array( $list[ 'CustomFieldsDefinition' ] ) ) {
						foreach( $list[ 'CustomFieldsDefinition' ] as $field ) {
							if( isset( $field[ 'Name' ] ) && ! empty( $field[ 'Name' ] ) ) {
								$_fields[] = array( 'label' => $list[ 'Name' ] . ': ' . $field[ 'Name' ], 'value' => $field[ 'Name' ] );
							}
						}
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

				if( $operator == "eq" ) {
	
					$contentStart .= " #if:recipient:$key:Is:$value#";
					$contentEnd = " #if:recipient:$key:Is:$value:end#" . $contentEnd;
	
				} else if( $operator == "neq" ) {
	
					$contentStart .= " #if:recipient:$key:IsNot:$value#";
					$contentEnd = " #if:recipient:$key:IsNot:$value:end#" . $contentEnd;
	
				}
			}

			if( ! empty( $contentStart ) && ! empty( $contentEnd ) ) {
				$content  = '<!--' . trim( $contentStart ) . '-->';
				$content .= $element->outertext;
				$content .= '<!--' . trim( $contentEnd ) . '-->';
				$element->outertext = $content;
			}
		}

		$output->save();

		return (string) $output;
	}

}
