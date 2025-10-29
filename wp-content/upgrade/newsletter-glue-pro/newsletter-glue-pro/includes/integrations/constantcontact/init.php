<?php
/**
 * Constant Contact.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'NGL_Abstract_Integration', false ) ) {
	include_once NGL_PLUGIN_DIR . 'includes/abstract-integration.php';
}

/**
 * Main Class.
 */
class NGL_Constantcontact extends NGL_Abstract_Integration {

	public $app 	= 'constantcontact';
	public $api_key = null;
	public $api 	= null;


	/**
	 * Constructor.
	 */
	public function __construct() {

		include_once 'lib/api.php';

		$this->get_api_key();

		add_filter( 'newsletterglue_email_content_constantcontact', array( $this, 'newsletterglue_email_content_constantcontact' ), 10, 3 );

		add_filter( 'newsltterglue_constantcontact_html_content', array( $this, 'html_content' ), 10, 2 );
	}

	/**
	 * Get API Key.
	 */
	public function get_api_key() {
		$integrations = get_option( 'newsletterglue_integrations' );
		$integration  = isset( $integrations[ $this->app ] ) ? $integrations[ $this->app ] : '';

		$this->api_key = isset( $integration[ 'api_key' ] ) ? $integration[ 'api_key' ] : '';
	}

	/**
	 * Add Integration.
	 */
	public function add_integration( $args = array() ) {
		$args = $this->get_connection_args( $args );

		$api_key = $args[ 'api_key' ];

		$this->api = new NGL_Constantcontact_API( $api_key );

		// Check if account is valid.
		$account = $this->api->get("/account/summary");

        $valid_account = isset( $account['contact_email'] ) && ! empty( $account['contact_email'] );


		if ( ! $valid_account ) {

			$this->remove_integration();

			$result = array( 'response' => 'invalid' );

			delete_option( 'newsletterglue_constantcontact' );

		} else {

			if ( ! $this->already_integrated( $this->app, $api_key ) ) {
				$this->save_integration( $api_key, $account );
			}

			$result = array( 'response' => 'successful' );

			update_option( 'newsletterglue_constantcontact', $account );

		}

		return $result;
	}

	/**
	 * Save Integration.
	 */
	public function save_integration( $api_key = '', $account = array() ) {
		
		$this->api_key = $api_key;

		delete_option( 'newsletterglue_integrations' );

		$integrations = get_option( 'newsletterglue_integrations' );

        $integrations[ $this->app ] = array(
            'api_key'  => $api_key,
            'tokens'   => $this->api->getTokens(),
        );
        
        $first_name       = isset( $account['first_name'] ) ? $account['first_name'] : '';
	    $last_name        = isset( $account['last_name'] ) ? $account['last_name'] : '';
	    $organisation     = isset( $account['organization_name'] ) ? $account['organization_name'] : '';

        if ( ! empty( $first_name ) && ! empty( $last_name ) ) {
            $name = $first_name . ' ' . $last_name;
        } elseif ( ! empty( $first_name ) ) {
            $name = $first_name;
        } elseif ( ! empty( $last_name ) ) {
            $name = $last_name;
        } elseif ( ! empty( $organisation ) ) {
            $name = $organisation;
        } else {
            $name = newsletterglue_get_default_from_name();
        }


        $integrations[ $this->app ][ 'connection_name' ] = sprintf( __( '%s – %s', 'newsletter-glue' ), $name, newsletterglue_get_name( $this->app ) );

		update_option( 'newsletterglue_integrations', $integrations );

		// Add default options.
		$globals = get_option( 'newsletterglue_options' );

        if ( ! isset( $globals[ $this->app ] ) ) {
            $globals[ $this->app ] = array();
        }

		$options = array(
			'from_name' 	=> $name,
			'from_email'	=> isset( $account[ 'contact_email' ] ) ? $account[ 'contact_email' ] : get_option( 'admin_email' ),
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
		$this->api = new NGL_Constantcontact_API( $this->api_key );
	}

	/**
	 * Get form defaults.
	 */
	public function get_form_defaults() {

		$this->api = new NGL_Constantcontact_API( $this->api_key );

		$defaults = array();

		return $defaults;
	}

	/**
	 * Get lists.
	 */
	public function get_lists() {
		$_lists = array();
		
		$response = $this->api->get("/contact_lists", array( "limit" => 1000, "status" => "active" ));

        if ( ! empty( $response['lists'] ) && is_array( $response['lists'] ) ) {
            foreach ( $response['lists'] as $list ) {
                if ( isset( $list['list_id'], $list['name'] ) ) {
                    $_lists[ $list['list_id'] ] = $list['name'];
                }
            }
        }

		asort( $_lists );

		return $_lists;
	}

	/**
	 * Get groups.
	 */
	public function get_segments() {
		$_segments = array();

        $response = $this->api->get("/segments", array( "limit" => 1000 ));

		if ( ! empty( $response['segments'] ) && is_array( $response['segments'] ) ) {
            foreach ( $response['segments'] as $segment ) {
                if ( isset( $segment['segment_id'], $segment['name'] ) ) {
                    $_segments[ $segment['segment_id'] ] = $segment['name'];
                }
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

		$preview_text 	= isset( $data['preview_text'] ) ? ngl_safe_title( $data[ 'preview_text' ] ) : '';
		$subject 		= isset( $data['subject'] ) ? ngl_safe_title( $data[ 'subject' ] ) : ngl_safe_title( $post->post_title );
        $from_name      = isset( $data['from_name'] ) ? $data['from_name'] : newsletterglue_get_default_from_name();
		$from_email     = isset( $data['from_email'] ) ? $data['from_email'] : $this->get_current_user_email();
		$lists			= isset( $data['lists'] ) && ! empty( $data['lists'] ) ? $data['lists'] : '';
		$segments		= isset( $data['segments'] ) && ! empty( $data['segments'] ) ? $data['segments'] : '';
		$schedule   	= isset( $data['schedule'] ) ? $data['schedule'] : 'immediately';

		if ( ! empty( $lists ) && ! is_array( $lists ) ) {
			$lists = explode( ',', $lists );
		}

		if ( ! empty( $segments ) && ! is_array( $segments ) ) {
			$segments = explode( ',', $segments );
		}

		// Empty content.
		if ( $test && isset( $post->post_status ) && $post->post_status === 'auto-draft' ) {

			$response['fail'] = $this->nothing_to_send();

			return $response;

		}

		$subject = apply_filters( 'newsletterglue_email_subject_line', $subject, $post, $data, $test, $this );

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

        $this->api = new NGL_Constantcontact_API( $this->api_key );

        // Step 1: Create campaign with activity
        $args = array(
            'name' => $subject . ' - ' . uniqid(),
            'email_campaign_activities' => array(
                array(
                    'format_type'  => 5,
                    'from_name'    => $from_name,
                    'from_email'   => $from_email,
                    'reply_to_email' => $from_email,
                    'subject'      => $subject,
                    'html_content' => newsletterglue_generate_content( $post, $subject, $this->app ),
                    'preheader'    => $preview_text
                )
            )
        );
        
        $activity_response = $this->api->post( "/emails", $args );

        // Step 2: Validate response
        if ( empty( $activity_response ) || isset( $activity_response[0]['error_message'] ) ) {
            $error_msg = isset( $activity_response[0]['error_message'] ) ? $activity_response[0]['error_message'] : 'Failed to create activity';
            $status = array( 'status' => 'error', 'error' => $error_msg );
            newsletterglue_add_campaign_data( $post_id, $subject, $this->prepare_message( $status ) );
            return $status;
        }

        // Step 3: Extract primary activity
        $primary_activity_id = '';
        if ( ! empty( $activity_response['campaign_activities'] ) && is_array( $activity_response['campaign_activities'] ) ) {
            foreach ( $activity_response['campaign_activities'] as $item ) {
                if ( isset( $item['role'] ) && $item['role'] === 'primary_email' && isset( $item['campaign_activity_id'] ) ) {
                    $primary_activity_id = $item['campaign_activity_id'];
                    break;
                }
            }
        }

        if ( empty( $primary_activity_id ) ) {
            $status = array( 'status' => 'error', 'error' => 'No primary_email activity found' );
            newsletterglue_add_campaign_data( $post_id, $subject, $this->prepare_message( $status ) );
            return $status;
        }

        // Step 4: Build update payload
        $args2 = array(
            'from_name'      => $from_name,
            'from_email'     => $from_email,
            'reply_to_email' => $from_email,
            'subject'        => $subject,
        );

        // togather the segment and list not allow
        if ( ! empty( $segments ) ) {
            $args2['segment_ids'] = $segments;
        } elseif ( ! empty( $lists ) ) {
            $args2['contact_list_ids'] = $lists;
        }

        // Step 5: Update campaign activity
        $update_activity = $this->api->put( "/emails/activities/{$primary_activity_id}", $args2 );

        if ( ! isset( $update_activity['campaign_activity_id'] ) ) {
            $error_msg = isset( $update_activity[0]['error_message'] ) ? $update_activity[0]['error_message'] : 'Activity update failed';
            $status = array( 'status' => 'error', 'error' => $error_msg );
            newsletterglue_add_campaign_data( $post_id, $subject, $this->prepare_message( $status ) );
            return $status;
        }


        // Step 6: Schedule or draft
        if ( $schedule === 'draft' || $schedule === 'schedule_draft' ) {
            $status = array( 'status' => 'draft' );
        } else {
            $args3 = array( 'scheduled_date' => '0' );
            $schedule_response = $this->api->post( "/emails/activities/{$primary_activity_id}/schedules", $args3 );

            if ( empty( $schedule_response ) || ! isset( $schedule_response[0]['scheduled_date'] ) ) {
                $error_msg = isset( $schedule_response[0]['error_message'] ) ? $schedule_response[0]['error_message'] : 'Unknown scheduling error';
                $status = array( 'status' => 'error', 'error' => $error_msg );
                newsletterglue_add_campaign_data( $post_id, $subject, $this->prepare_message( $status ) );
                return $status;
            }

            $status = array( 'status' => 'sent' );
        }

        // Log campaign data and return
        newsletterglue_add_campaign_data( $post_id, $subject, $this->prepare_message( $status ), $primary_activity_id );

        return $status;

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

			if ( $result[ 'status' ] === 'error' ) {
				$output[ 'status' ] 	= 400;
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ]	= $result[ 'error' ];
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


		if ( ! empty( $list_id ) ) {
            
            $this->api = new NGL_Constantcontact_API( $this->api_key );

			$data = array(
                'email_address'	=> $email,
                'list_memberships' => array( $list_id ),
			);
            
            // Handle name if provided
            if ( ! empty( $name ) ) {
                $name  = trim( $name );
                $parts = preg_split( '/\s+/', $name );

                $first_name = isset( $parts[0] ) ? $parts[0] : '';
                $last_name  = ( count( $parts ) > 1 ) ? implode( ' ', array_slice( $parts, 1 ) ) : '';

                if ( ! empty( $first_name ) ) {
                    $data['first_name'] = $first_name;
                }
                if ( ! empty( $last_name ) ) {
                    $data['last_name'] = $last_name;
                }
            }

            // Add extra list if available
            if ( ! empty( $extra_list_id ) ) {
                $data['list_memberships'][] = $extra_list_id;
            }
            
            // API v3 not supports double opt-in
			$result = $this->api->post( '/contacts/sign_up_form', $data );

            if( isset( $result['contact_id'] ) ) {
                return true;
            }
			
		}

		return false;
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
			'helper'		=> '<a href="'. $this->api->getAuthorizationURL() .'" target="_blank" class="ngl-link-inline-svg">' . __( 'Get API key', 'newsletter-glue' ) . ' [externallink]</a>',
			'type'			=> 'password',
		) );

	}

	/**
	 * Customize content.
	 */
	public function newsletterglue_email_content_constantcontact( $content, $post, $subject ) {

		// for constant contact tracking
		$content .= '[[trackingImage]]';

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

		$unsub = str_replace( '{{ unsubscribe_link }}', "[[unsubscribeLink]]", $unsub );

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

		$this->api = new NGL_Constantcontact_API( $this->api_key );
		
        $result = $this->api->get( '/account/emails', array( 'confirm_status' => 'CONFIRMED' ) );
        $is_verified = false;

        if ( ! empty( $result ) && is_array( $result ) ) {
            foreach ( $result as $record ) {
                if ( isset( $record['email_address'] ) && $record['email_address'] === $email ) {
                    $is_verified = true;
                    break;
                }
            }
        }


		if ( $is_verified == true ) {

			$response = array(
				'success' => '<strong>' . __( 'Verified', 'newsletter-glue' ) . '</strong>',
			);

		} else {

			$response = array(
				'failed'         => __( 'Not verified', 'newsletter-glue' ),
				'failed_details' => '<a href="'. $this->get_email_verify_help() .'" target="_blank" class="ngl-link-inline-svg">' . __( 'Verify email now', 'newsletter-glue' ) . ' <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a> <a href="https://newsletterglue.com/docs/email-verification-my-email-is-not-verified/" target="_blank" class="ngl-link-inline-svg">' . __( 'Learn more', 'newsletter-glue' ) . ' <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a>',
			);

		}

		return $response;
	}

	/**
	 * Get email verify help.
	 */
	public function get_email_verify_help() {
		return 'https://knowledgebase.constantcontact.com/email-digital-marketing/tutorials/KnowledgeBase/42716-Verify-an-email-address-in-your-account';
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
			case 'first_name':
                return ! empty( $fallback ) ? '[[FIRSTNAME OR " ' .$fallback . '"]]' : '[[FIRSTNAME]]';
                break;
            case 'last_name':
                return ! empty( $fallback ) ? '[[LastName OR " ' .$fallback . '"]]' : '[[LastName]]';
                break;
            case 'company':
                return ! empty( $fallback ) ? '[[CompanyName OR " ' .$fallback . '"]]' : '[[CompanyName]]';
                break;
            case 'job_title':
                return ! empty( $fallback ) ? '[[JobTitle OR " ' .$fallback . '"]]' : '[[JobTitle]]';
                break;
            case 'address_line_1':
                return ! empty( $fallback ) ? '[[AddressLine1 OR " ' .$fallback . '"]]' : '[[AddressLine1]]';
                break;
            case 'address_line_2':
                return ! empty( $fallback ) ? '[[AddressLine2 OR " ' .$fallback . '"]]' : '[[AddressLine2]]';
                break;
            case 'address_line_3':
                return ! empty( $fallback ) ? '[[AddressLine3 OR " ' .$fallback . '"]]' : '[[AddressLine3]]';
                break;
            case 'city':
                return ! empty( $fallback ) ? '[[City OR " ' .$fallback . '"]]' : '[[City]]';
                break;
            case 'state_name':
                return ! empty( $fallback ) ? '[[StateName OR " ' .$fallback . '"]]' : '[[StateName]]';
                break;
            case 'postal_code':
                return ! empty( $fallback ) ? '[[PostalCode OR " ' .$fallback . '"]]' : '[[PostalCode]]';
                break;
            case 'home_phone':
                return ! empty( $fallback ) ? '[[HomePhone OR " ' .$fallback . '"]]' : '[[HomePhone]]';
                break;
            case 'work_phone':
                return ! empty( $fallback ) ? '[[WorkPhone OR " ' .$fallback . '"]]' : '[[WorkPhone]]';
                break;
            case 'birthday':
                return ! empty( $fallback ) ? '[[Birthday OR " ' .$fallback . '"]]' : '[[Birthday]]';
                break;
            case 'anniversary':
                return ! empty( $fallback ) ? '[[Anniversary OR " ' .$fallback . '"]]' : '[[Anniversary]]';
                break;
            case 'email_address':
                return ! empty( $fallback ) ? '[[EmailAddress OR " ' .$fallback . '"]]' : '[[EmailAddress]]';
                break;
            case 'organization_name':
                return ! empty( $fallback ) ? '[[account.OrganizationName OR " ' .$fallback . '"]]' : '[[account.OrganizationName]]';
                break;
            case 'organization_website_address':
                return ! empty( $fallback ) ? '[[account.SiteURL OR " ' .$fallback . '"]]' : '[[account.SiteURL]]';
                break;
            case 'organization_logo_url':
                return ! empty( $fallback ) ? '[[account.LogoURL OR " ' .$fallback . '"]]' : '[[account.LogoURL]]';
                break;
            case 'organization_address_line_1':
                return ! empty( $fallback ) ? '[[account.AddressLine1 OR " ' .$fallback . '"]]' : '[[account.AddressLine1]]';
                break;
            case 'organization_address_line_2':
                return ! empty( $fallback ) ? '[[account.AddressLine2 OR " ' .$fallback . '"]]' : '[[account.AddressLine2]]';
                break;
            case 'organization_address_line_3':
                return ! empty( $fallback ) ? '[[account.AddressLine3 OR " ' .$fallback . '"]]' : '[[account.AddressLine3]]';
                break;
            case 'organization_city':
                return ! empty( $fallback ) ? '[[account.City OR " ' .$fallback . '"]]' : '[[account.City]]';
                break;
            case 'organization_state':
                return ! empty( $fallback ) ? '[[account.State OR " ' .$fallback . '"]]' : '[[account.State]]';
                break;
            case 'organization_us_state':
                return ! empty( $fallback ) ? '[[account.usState OR " ' .$fallback . '"]]' : '[[account.usState]]';
                break;
            case 'organization_country':
                return ! empty( $fallback ) ? '[[account.Country OR " ' .$fallback . '"]]' : '[[account.Country]]';
                break;
            case 'organization_country_code':
                return ! empty( $fallback ) ? '[[account.CountryCode OR " ' .$fallback . '"]]' : '[[account.CountryCode]]';
                break;
            case 'organization_postal_code':
                return ! empty( $fallback ) ? '[[account.PostalCode OR " ' .$fallback . '"]]' : '[[account.PostalCode]]';
                break;
            case 'organization_signature_name':
                return ! empty( $fallback ) ? '[[account.signaturename OR " ' .$fallback . '"]]' : '[[account.signaturename]]';
                break;
            case 'organization_signature_email':
                return ! empty( $fallback ) ? '[[account.signatureemail OR " ' .$fallback . '"]]' : '[[account.signatureemail]]';
                break;
            case 'organization_signature_image_url':
                return ! empty( $fallback ) ? '[[account.signatureImageURL OR " ' .$fallback . '"]]' : '[[account.signatureImageURL]]';
                break;
            case 'webpage_link':
                return '[[viewAsWebpage]]';
                break;
            case 'unsubscribe_link':
                return '[[unsubscribeLink]]';
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
		$this->api = new NGL_Constantcontact_API( $this->api_key );
		return $this->get_lists();
	}

	/**
	 * Configure options array for this ESP.
	 */
	public function option_array() {
		return array(
			'lists' => array(
				'type'		=> 'select',
				'callback'	=> 'get_lists',
				'title'     => __( 'Lists', 'newsletter-glue' ),
				'help'		=> __( 'Who receives your email.', 'newsletter-glue' ),
				'is_multi'	=> true,
			),
			'segments' => array(
				'type'		=> 'select',
				'callback' 	=> 'get_segments',
				'title'		=> __( 'Segments', 'newsletter-glue' ),
				'help'		=> __( 'A specific group of subscribers.', 'newsletter-glue' ),
				'is_multi'	=> true,
			)
		);
	}

	/**
	 * Get custom fields of esp
	 */
	public function get_custom_fields() {

		// Predefined fields
		$_fields = array(
			array('label' => 'First name', 'value' => 'FirstName'),
            array('label' => 'Last name', 'value' => 'LastName'),
            array('label' => 'Company', 'value' => 'CompanyName'),
            array('label' => 'Job title', 'value' => 'JobTitle'),
            array('label' => 'Address line 1', 'value' => 'AddressLine1'),
            array('label' => 'Address line 2', 'value' => 'AddressLine2'),
            array('label' => 'Address line 3', 'value' => 'AddressLine3'),
            array('label' => 'City', 'value' => 'City'),
            array('label' => 'State name', 'value' => 'StateName'),
            array('label' => 'Postal code', 'value' => 'PostalCode'),
            array('label' => 'Home phone', 'value' => 'HomePhone'),
            array('label' => 'Work phone', 'value' => 'WorkPhone'),
            array('label' => 'Birthday', 'value' => 'Birthday'),
            array('label' => 'Anniversary', 'value' => 'Anniversary'),
            array('label' => 'Email address', 'value' => 'EmailAddress'),
            array('label' => 'Organization name', 'value' => 'account.OrganizationName'),
            array('label' => 'Organization Website address', 'value' => 'account.SiteURL'),
            array('label' => 'Organization Logo URL', 'value' => 'account.LogoURL'),
            array('label' => 'Organization Address line 1', 'value' => 'account.AddressLine1'),
            array('label' => 'Organization Address line 2', 'value' => 'account.AddressLine2'),
            array('label' => 'Organization Address line 3', 'value' => 'account.AddressLine3'),
            array('label' => 'Organization City', 'value' => 'account.City'),
            array('label' => 'Organization State name', 'value' => 'account.State'),
            array('label' => 'Organization Two-letter state', 'value' => 'account.usState'),
            array('label' => 'Organization Country', 'value' => 'account.Country'),
            array('label' => 'Organization Country code', 'value' => 'account.CountryCode'),
            array('label' => 'Organization Postal code', 'value' => 'account.PostalCode'),
            array('label' => 'Organization Signature name', 'value' => 'account.signaturename'),
            array('label' => 'Organization Signature email', 'value' => 'account.signatureemail'),
            array('label' => 'Organization Signature image URL', 'value' => 'account.signatureImageURL'),
		);

		$this->api = new NGL_Constantcontact_API( $this->api_key );

		$response = $this->api->get( '/contact_custom_fields', array( 'limit' => 100 ) );
        if ( ! empty( $response ) && isset( $response['custom_fields'] ) && is_array( $response['custom_fields'] ) ) {
            foreach ( $response['custom_fields'] as $field ) {
                if ( isset( $field['name'] ) && ! empty( $field['name'] ) ) {
                    $_fields[] = array(
                        'label' => $field['name'],
                        'value' => 'custom.' . $field['name']
                    );
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
			
			$conditionQuery = '';

			foreach( $conditions as $c => $condition ) {
				$key          = $condition->key;
				$operator     = $condition->operator;
				$value        = $condition->value;
				$relationship = $condition->relationship;

				if( $c > 0 ) {
					$conditionQuery .= $relationship == "AND" ? " && " : " || ";
				}

				if( $operator == 'eq' ) {
					$conditionQuery .= '( {$'.$key.'} == "'.$value.'" )';
				} else if( $operator == 'neq' ) {
					$conditionQuery .= '( {$'.$key.'} != "'.$value.'" )';
				} else if( $operator == 'lt' ) {
					$conditionQuery .= '( {$'.$key.'} < "'.$value.'" )';
				} else if( $operator == 'gt' ) {
					$conditionQuery .= '( {$'.$key.'} > "'.$value.'" )';
				} else if( $operator == 'lte' ) {
					$conditionQuery .= '( {$'.$key.'} <= "'.$value.'" )';
				} else if( $operator == 'gte' ) {
					$conditionQuery .= '( {$'.$key.'} >= "'.$value.'" )';
				}
			}

			if( ! empty( $conditionQuery ) ) {
				$content = "<!--{ #if ( $conditionQuery ) }-->";
					$content .= $element->outertext;
				$content .= "<!--{ #end }-->";
				$element->outertext = $content;
			}
		}

		$output->save();

		return (string) $output;
	}
}
