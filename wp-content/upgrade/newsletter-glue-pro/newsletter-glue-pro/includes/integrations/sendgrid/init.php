<?php
/**
 * SendGrid.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main Class.
 */
class NGL_Sendgrid extends NGL_Abstract_Integration {

	public $app 	= 'sendgrid';
	public $api_key = null;
	public $api 	= null;

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Include needed files.
		include_once 'lib/api.php';

		$this->get_api_key();

		add_filter( 'newsletterglue_email_content_sendgrid', array( $this, 'newsletterglue_email_content_sendgrid' ), 10, 3 );

		add_action( 'newsletterglue_edit_more_settings', array( $this, 'newsletterglue_edit_more_settings' ), 50, 3 );

		add_filter( 'newsltterglue_sendgrid_html_content', array( $this, 'html_content' ), 10, 2 );
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

		$this->api  = new NGL_Sendgrid_API( $api_key );

		// Check if account is valid.
		$senders = $this->api->get( '/verified_senders' );

		$account = null;
		if ( ! empty( $senders[ 'results' ] ) ) {
			foreach( $senders[ 'results' ] as $key => $data ) {
				if ( $data[ 'verified' ] == true ) {
					$account = $senders[ 'results' ][ $key ];
					break;
				}
			}
		}

		if ( ! $account ) {

			$this->remove_integration();

			$result = array( 'response' => 'invalid' );

			delete_option( 'newsletterglue_sendgrid' );

		} else {

			$this->api_key = $api_key;

			if ( ! $this->already_integrated( $this->app, $api_key ) ) {
				$this->save_integration( $api_key, $account );
			}

			$result = array( 'response' => 'successful' );

			update_option( 'newsletterglue_sendgrid', $account );

		}

		return $result;
	}

	/**
	 * Save Integration.
	 */
	public function save_integration( $api_key = '', $account = array() ) {

		delete_option( 'newsletterglue_integrations' );

		$integrations = get_option( 'newsletterglue_integrations' );

		$integrations[ $this->app ] = array();
		$integrations[ $this->app ][ 'api_key' ] = $api_key;

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

		$this->api = new NGL_Sendgrid_API( $this->api_key );

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
			'helper'		=> '<a href="https://app.sendgrid.com/settings/api_keys" target="_blank" class="ngl-link-inline-svg">' . __( 'Get API key', 'newsletter-glue' ) . ' [externallink]</a>',
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

		$this->api = new NGL_Sendgrid_API( $this->api_key );

		$response = $this->api->get( '/verified_senders' );

		// Check if email is a valid sender.
		$verified = false;
		if ( $response && ! empty( $response[ 'results' ] ) ) {
			$senders = $response[ 'results' ];
			foreach( $senders as $key => $data ) {
				if ( isset( $data[ 'from_email' ] ) && trim( $email ) === trim( $data[ 'from_email' ] ) && $data[ 'verified' ] == true ) {
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
				'failed_details'	=> '<a href="https://mc.sendgrid.com/senders" target="_blank">' . __( 'Verify email now', 'newsletter-glue' ) . ' <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a> <a href="https://newsletterglue.com/docs/email-verification-my-email-is-not-verified/" target="_blank">' . __( 'Learn more', 'newsletter-glue' ) . ' <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a>',
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

		$this->api = new NGL_Sendgrid_API( $this->api_key );

		$defaults = array();

		return $defaults;

	}

	/**
	 * Get lists compat.
	 */
	public function _get_lists_compat() {
		$this->api = new NGL_Sendgrid_API( $this->api_key );

		return $this->get_lists();
	}

	/**
	 * Get Lists.
	 */
	public function get_lists() {
		$_lists = array();

		$request = $this->api->get( '/marketing/lists' );

		if ( isset( $request[ 'result' ] ) ) {
			$lists = $request[ 'result' ];
		}

		if ( ! empty( $lists ) ) {
			foreach( $lists as $key => $data ) {
				$_lists[ $data[ 'id' ] ] = $data[ 'name' ];
			}
		}

		asort( $_lists );

		return $_lists;
	}

	/**
	 * Get Segments.
	 */
	public function get_segments() {
		$_segments = array();

		$request = $this->api->get( '/marketing/segments/2.0' );

		if ( isset( $request[ 'results' ] ) ) {
			$segments = $request[ 'results' ];
		}

		if ( ! empty( $segments ) ) {
			foreach( $segments as $key => $data ) {
				$_segments[ $data[ 'id' ] ] = $data[ 'name' ];
			}
		}

		asort( $_segments );

		return $_segments;
	}

	/**
	 * Get Unsubscription Groups.
	 */
	public function get_unsub_groups() {
		$_unsubs = array();

		$request = $this->api->get( '/asm/groups' );

		if ( ! empty( $request ) ) {
			foreach( $request as $key => $data ) {
				$_unsubs[ $data[ 'id' ] ] = $data[ 'name' ];
			}
		}

		return $_unsubs;
	}

	/**
	 * Customize content.
	 */
	public function newsletterglue_email_content_sendgrid( $content, $post, $subject ) {

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

		$unsub = str_replace( '{{ unsubscribe_link }}', '{{{unsubscribe}}}', $unsub ); // phpcs:ignore

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
		return 'https://docs.sendgrid.com/ui/sending-email/sender-verification';
	}

	/**
	 * Code supported tags for this ESP.
	 */
	public function get_tag( $tag, $post_id = 0, $fallback = null ) {

		switch ( $tag ) {
			case 'unsubscribe_link' :
				return '{{{unsubscribe}}}'; // phpcs:ignore
			break;
			case 'email' :
				return '{{email}}';
			break;
			case 'first_name' :
				return ! empty( $fallback ) ? '{{insert first_name "default=' . $fallback . '"}}' : '{{first_name}}';
			break;
			case 'last_name' :
				return ! empty( $fallback ) ? '{{insert last_name "default=' . $fallback . '"}}' : '{{last_name}}';
			break;
			case 'admin_address' :
				return '{{Sender_Address}}';
			break;
			case 'update_preferences' :
				return '{{{unsubscribe_preferences}}}'; // phpcs:ignore
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
	 * Add extra settings to metabox.
	 */
	public function newsletterglue_edit_more_settings( $app, $settings, $ajax = false ) {
		if ( $app === $this->app ) {

			$unsub_groups = $this->get_unsub_groups();

			$keys 		= array_keys( $unsub_groups );
			$default 	= isset( $settings->unsub_groups ) ? $settings->unsub_groups : newsletterglue_get_option( 'unsub_groups', $app );

			if ( empty( $default ) ) {
				$default = ! empty( $keys[0] ) ? $keys[0] : null;
			}
			?>
			<div class="ngl-metabox-flexfull">
				<div class="ngl-metabox-flex">
					<div class="ngl-metabox-flex">
						<div class="ngl-metabox-header">
							<label for="ngl_unsub_groups"><?php esc_html_e( 'Unsubscription Group', 'newsletter-glue' ); ?></label>
							<?php $this->input_verification_info(); ?>
						</div>
						<div class="ngl-field">
						<?php
							newsletterglue_select_field( array(
								'id' 			=> 'ngl_unsub_groups',
								'legacy'		=> true,
								'helper'		=> sprintf( __( 'Unsubscribe Groups allow recipients to opt out of select types of content you send.  %s', 'newsletter-glue' ), '<a href="https://mc.sendgrid.com/unsubscribe-groups" target="_blank" class="ngl-link-inline-svg">' . __( 'Learn more', 'newsletter-glue' ) . ' [externallink]' . '</a>' ),
								'options'		=> $unsub_groups,
								'default'		=> $default,
							) );
						?>
						</div>
					</div>
					<div class="ngl-metabox-flex">
					</div>
				</div>
			</div>
			<?php
		}
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
		$unsub_groups   = isset( $data['unsub_groups'] ) ? $data['unsub_groups'] : '';

        // Force draft.
        if ( ! empty( $this->forced_state ) ) {
            if ( $this->forced_state === 'draft' ) {
                $schedule = 'draft';
            }
        }

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

		// Send a campaign or draft.
		$this->api = new NGL_Sendgrid_API( $this->api_key );

		if ( empty( $lists ) && empty( $segments ) ) {
			$all = true;
		} else {
			$all = false;
		}

		// Get verified sender ID.
		$sender_id = 0;
		$senders = $this->api->get( '/verified_senders' );
		if ( ! empty( $senders[ 'results' ] ) ) {
			foreach( $senders[ 'results' ] as $key => $data ) {
				if ( $data[ 'verified' ] == true && $data[ 'from_email' ] === $from_email ) {
					$sender_id = $data[ 'id' ];
					break;
				}
			}
		}

		// Generate campaign array.
		$args = array(
			'name'			=> ngl_safe_title( $post->post_title ),
			'send_at'		=> $schedule === 'immediately' ? gmdate('Y-m-d\TH:i:s\Z', time() ) : null,
			'send_to'		=> array(
				'all'			=> $all,
			),
			'email_config'	=> array(
				'subject'					=> $subject,
				'html_content'				=> newsletterglue_generate_content( $post, $subject, $this->app ),
				'generate_plain_content'	=> true,
				'sender_id'					=> $sender_id,
			),
		);

		if ( ! empty( $unsub_groups ) ) {
			$args[ 'email_config' ][ 'suppression_group_id' ] = absint( $unsub_groups );
		}

		if ( ! empty( $lists ) ) {
			$args[ 'send_to' ][ 'list_ids' ] = $lists;
		}

		if ( ! empty( $segments ) ) {
			$args[ 'send_to' ][ 'segment_ids' ] = $segments;
		}

		$campaign = $this->api->post( '/marketing/singlesends', $args );

		if ( ! isset( $campaign[ 'id' ] ) ) {
			$status = array( 'status' => 'error' );
			newsletterglue_add_campaign_data( $post_id, $subject, $this->get_status( $status ) );
			return $status;
		}

		$campaign_id = $campaign[ 'id' ];

		if ( $schedule === 'draft' ) {
			$status = array( 'status' => 'draft' );
		} else {
			$status = array( 'status' => 'sent' );
			$this->api->put( '/marketing/singlesends/' . $campaign_id . '/schedule', array( 'send_at' => 'now' ) );
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

	/**
	 * Get custom fields of esp
	 */
	public function get_custom_fields() {
		$_fields = array();
		$fields  = array();

		$this->api = new NGL_Sendgrid_API( $this->api_key );
		$response = $this->api->get( '/marketing/field_definitions' );

		if( isset( $response[ 'reserved_fields' ] ) && is_array( $response[ 'reserved_fields' ] ) && count( $response[ 'reserved_fields' ] ) ) {
			$fields = array_merge( $fields, $response[ 'reserved_fields' ] );
		}

		if( isset( $response[ 'custom_fields' ] ) && is_array( $response[ 'custom_fields' ] ) && count( $response[ 'custom_fields' ] ) ) {
			$fields = array_merge( $fields, $response[ 'custom_fields' ] );
		}

		if( count( $fields ) ) {
			foreach($fields as $field) {
				if( isset( $field[ 'name' ] ) && ! empty( $field[ 'name' ] ) ) {
					$_fields[] = array( 'value' => $field[ 'name' ], 'label' => $field[ 'name' ] );
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

				// value not array since no custom tags available for sendgrid
				if( ! is_numeric( $value ) ) {
					if( strval( strtolower( $value ) ) === 'true' ) {
						$value = 'true';
					} else if( strval( strtolower( $value ) ) === 'false' ) {
						$value = 'false';
					} else if( DateTime::createFromFormat( "Y-m-d", $value ) !== false ) {
						$key = "(formatDate $key 'YYYY-MM-DD')";
						$value = "(formatDate '$value' 'YYYY-MM-DD')";					
					} else {
						if( $operator != 'and' && $operator != 'or' ) {
							$value = '"' . $value . '"';
						}
					}
				}

				if( $operator == "ex" ) {

					$contentStart .= "<!--{{#if $key}}-->";
					$contentEnd = "<!--{{/if}}-->" . $contentEnd;
	
				} else if( $operator == "nex" ) {
					
					$contentStart .= "<!--{{#unless $key}}-->";
					$contentEnd = "<!--{{/unless}}-->" . $contentEnd;
	
				} else if( $operator == "gt" ) {
	
					$contentStart .= "<!--{{#greaterThan $key $value}}-->";
					$contentEnd = "<!--{{/greaterThan}}-->" . $contentEnd;
	
				} else if( $operator == "lt" ) {
	
					$contentStart .= "<!--{{#lessThan $key $value}}-->";
					$contentEnd = "<!--{{/lessThan}}-->" . $contentEnd;
	
				} else if( $operator == "eq" ) {
	
					$contentStart .= "<!--{{#equals $key $value}}-->";
					$contentEnd = "<!--{{/equals}}-->" . $contentEnd;
	
				} else if( $operator == "neq" ) {
	
					$contentStart .= "<!--{{#notEquals $key $value}}-->";
					$contentEnd = "<!--{{/notEquals}}-->" . $contentEnd;
	
				} else if( $operator == "and" ) {
	
					$contentStart .= "<!--{{#and $key $value}}-->";
					$contentEnd = "<!--{{/and}}-->" . $contentEnd;
					
				} else if( $operator == "or" ) {
	
					$contentStart .= "<!--{{#or $key $value}}-->";
					$contentEnd = "<!--{{/or}}-->" . $contentEnd;
	
				}
			}

			if( ! empty( $contentStart ) && ! empty( $contentEnd ) ) {
				$content  = $contentStart;
				$content .= $element->outertext;
				$content .= $contentEnd;
				$element->outertext = $content;
			}
		}

		$output->save();

		return ( string ) $output;
	}
}
