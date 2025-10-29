<?php
/**
 * Aweber.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'NGL_Abstract_Integration', false ) ) {
	include_once NGL_PLUGIN_DIR . 'includes/abstract-integration.php';
}

/**
 * Main Class.
 */
class NGL_Aweber extends NGL_Abstract_Integration {

	public $app 	= 'aweber';
	public $api_key = null;
	public $api 	= null;

	private $saved_lists = null;


	/**
	 * Constructor.
	 */
	public function __construct() {

		include_once 'lib/api.php';

		$this->get_api_key();

		add_filter( 'newsletterglue_email_content_aweber', array( $this, 'newsletterglue_email_content_aweber' ), 10, 3 );

		add_filter( 'newsltterglue_aweber_html_content', array( $this, 'html_content' ), 10, 2 );
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

		$this->api = new NGL_Aweber_API( $api_key );

		// Check if account is valid.
		$account = $this->api->me();

		if ( empty( $account ) ) {

			$this->remove_integration();

			$result = array( 'response' => 'invalid' );

			delete_option( 'newsletterglue_aweber' );

		} else {

			if ( ! $this->already_integrated( $this->app, $api_key ) ) {
				$this->save_integration( $api_key, $account );
			}

			$result = array( 'response' => 'successful' );

			update_option( 'newsletterglue_aweber', $account );

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

		$integrations[ $this->app ] = array();
		$integrations[ $this->app ][ 'api_key' ] = $api_key;
		$integrations[ $this->app ][ 'tokens' ] = $this->api->getTokens();

		$name = isset( $account[ 'name' ] ) ? $account[ 'name' ] : newsletterglue_get_default_from_name();

		$integrations[ $this->app ][ 'connection_name' ] = sprintf( __( '%s – %s', 'newsletter-glue' ), $name, newsletterglue_get_name( $this->app ) );

		update_option( 'newsletterglue_integrations', $integrations );

		// Add default options.
		$globals = get_option( 'newsletterglue_options' );

		$options = array(
			'from_name' 	=> $name,
			'from_email'	=> isset( $account[ 'from' ] ) ? $account[ 'from' ] : get_option( 'admin_email' ),
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
		$this->api = new NGL_Aweber_API( $this->api_key );
	}

	/**
	 * Get form defaults.
	 */
	public function get_form_defaults() {

		$this->api = new NGL_Aweber_API( $this->api_key );

		$defaults = array();

		return $defaults;
	}

	/**
	 * Get lists.
	 */
	public function get_lists() {
		$_lists = array();
		
		$account = $this->api->me();
		
		if( isset( $account['self_link'] ) ) {
			
			$lists = $this->api->getCollection( $account['lists_collection_link'] );

			foreach( $lists as $list ) {
				$_lists[ $list[ 'id' ] ] = $list[ 'name' ];
			}
		}

		if ( ! empty( $_lists ) ) {
			$this->saved_lists = $lists;
		}

		asort( $_lists );

		return $_lists;
	}

	/**
	 * Get groups.
	 */
	public function get_segments() {
		$_segments = array();

		if ( ! empty( $this->saved_lists ) ) {
			
			foreach( $this->saved_lists as $list ) {
				$segments = $this->api->getCollection( $list['segments_collection_link'] );
				foreach( $segments as $segment ) {
					if( count( $this->saved_lists ) > 1 ) {
						$_segments[ $list['id'] . '_' . $segment[ 'id' ] ] = $segment[ 'name' ] . " (". $list['name'] .")";
					} else {
						$_segments[ $list['id'] . '_' . $segment[ 'id' ] ] = $segment[ 'name' ];
					}
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

		$subject 		= isset( $data['subject'] ) ? ngl_safe_title( $data[ 'subject' ] ) : ngl_safe_title( $post->post_title );
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

		$this->api = new NGL_Aweber_API( $this->api_key );
			$account = $this->api->me();

			$args = array(
				'body_html' => newsletterglue_generate_content( $post, $subject, $this->app ),
				'body_text' => __( 'Your email client does not support HTML emails. Open newsletter here: {!archive_url}. If you do not want to receive emails from us, click here: {!remove_web}', 'newsletter-glue' ),
				'subject' => $subject,
			);

			$integrations = $this->api->getCollection( $account['integrations_collection_link'] );

			foreach ($integrations as $integration) {
				if (strtolower($integration['service_name']) == 'facebook') {
					$args['facebook_integration'] = $integration['self_link'];
				}
				if (strtolower($integration['service_name']) == 'twitter') {
					$args['twitter_integration'] = $integration['self_link'];
				}
			}

			if ( empty( $lists ) ) {
				$lists = array_keys( $this->get_lists() );
			}

			if ( ! empty( $lists ) ) {

				foreach( $lists as $list_id ) {

					if ( ! empty( $segments ) ) {
						
						foreach( $segments as $segment ) {
							if ( strstr( $segment, $list_id ) ) {
								$segment_id = str_replace( $list_id . '_', '', $segment );
								$args['segment_link'] = $account['lists_collection_link'] . "/{$list_id}/segments/{$segment_id}";
							}
						}
					}
				}

			}

			$broadcastsUrl = $account['lists_collection_link'] . "/{$lists[0]}/broadcasts";
			$broadcast = $this->api->post( $broadcastsUrl, $args );
			$broadcastId = isset( $broadcast['id'] ) ? $broadcast['id'] : '';

		
			if ( $schedule === 'draft' ) {

				$result = array( 'status' => 'draft' );

			} else {

				$timestamp = new DateTime('now', new DateTimeZone('UTC'));

				$args = array(
					'scheduled_for' => $timestamp->format(DateTime::ATOM)
				);

				$scheduleUrl = $broadcast['self_link'] . '/schedule';

				$result = $this->api->post( $scheduleUrl, $args );
			}

			newsletterglue_add_campaign_data( $post_id, $subject, $this->prepare_message( (array) $result ), $broadcastId );

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

		$this->api = new NGL_Aweber_API( $this->api_key );

		if ( ! empty( $list_id ) ) {

			$account = $this->api->me();

			if( isset( $account['self_link'] ) ) {

				$params = array(
					'ws.op' => 'find',
					'email' => $email,
				);

				$findUrl = $account['lists_collection_link'] . "/{$list_id}/subscribers?" . http_build_query( $params );
				$foundSubscriber = $this->api->getCollection( $findUrl );

				if( ! isset( $foundSubscriber['self_link'] ) ) {
					$url = $account['lists_collection_link'] . "/{$list_id}/subscribers?ws.op=create";
					$data = array( 'email' => $email );
					$this->api->post( $url, $data );
				}
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
			'helper'		=> '<a href="'. $this->api->getAuthorizeUrl() .'" target="_blank" class="ngl-link-inline-svg">' . __( 'Get API key', 'newsletter-glue' ) . ' [externallink]</a>',
			'type'			=> 'password',
		) );

	}

	/**
	 * Customize content.
	 */
	public function newsletterglue_email_content_aweber( $content, $post, $subject ) {

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

		$unsub = str_replace( '{{ unsubscribe_link }}', '{!remove_web}', $unsub );

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
	 * Get email verify help.
	 */
	public function get_email_verify_help() {
		return 'https://help.aweber.com/hc/en-us/articles/360043593813-How-do-I-add-and-verify-a-From-address-in-List-Settings';
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
			case 'contact_address' :
				return '{!contact_address}';
			break;	
			case 'full_name' :
				return '{!name_fix}';
				break;
			case 'first_name' :
				return '{!firstname_fix}';
				break;
			case 'last_name' :
				return '{!lastname_fix}';
				break;
			case 'email' :
				return '{!email}';
				break;
			case 'add_tracking' :
				return '{!ad_tracking}';
				break;
			case 'ip_address' :
				return '{!add_ip}';
				break;
			case 'signup_url' :
				return '{!add_url}';
				break;
			case 'signup_date' :
				return '{!signdate long}';
				break;
			case 'country' :
				return '{!geog_country}';
				break;
			case 'region' :
				return '{!geog_region}';
				break;
			case 'city' :
				return '{!geog_city}';
				break;
			case 'postal' :
				return '{!geog_postal}';
				break;
			case 'lat' :
				return '{!geog_lat}';
				break;
			case 'lon' :
				return '{!geog_lon}';
				break;
			case 'dma' :
				return '{!geog_dma_code}';
				break;
			case 'archive_web' :
				return '{!archive_url}';
				break;
			case 'signature' :
				return '{!signature}';
				break;
			case 'unsubscribe_link' :
				return '{!remove_web}';
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
		$this->api = new NGL_Aweber_API( $this->api_key );
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
			),
			'segments' => array(
				'type'		=> 'select',
				'callback' 	=> 'get_segments',
				'title'		=> __( 'Segments', 'newsletter-glue' ),
				'help'		=> __( 'A specific group of subscribers.', 'newsletter-glue' ),
			)
		);
	}

	/**
	 * Get custom tags of esp
	 */
	public function get_custom_tags() {
		$_tags = array();

		$this->api = new NGL_Aweber_API( $this->api_key );
		$account = $this->api->me();
		if( ! empty( $account ) && isset( $account[ 'lists_collection_link' ] ) ) {
			$lists = $this->api->getCollection( $account[ 'lists_collection_link' ] );
			if( ! empty( $lists ) && isset( $lists[0][ 'self_link' ] ) ) {
				$tags = $this->api->get( $lists[0][ 'self_link' ] . '/tags' );
				if( is_array( $tags ) ) {
					foreach( $tags as $tag ) {
						$_tags[] = array( 'label' => $tag, 'value' => $tag );
					}
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

		// Predefined fields
		$_fields = array(
			array( 'label' => 'Full name', 'value' => 'subscriber.name' ),
			array( 'label' => 'First name', 'value' => 'subscriber.first_name' ),
			array( 'label' => 'Last name', 'value' => 'subscriber.last_name' ),
			array( 'label' => 'Email address', 'value' => 'subscriber.email' ),
			array( 'label' => 'Identifiers', 'value' => 'subscriber.id' ),
			array( 'label' => 'UUID', 'value' => 'subscriber.uuid' ),
			array( 'label' => 'Unsubscribe link', 'value' => 'subscriber.unsubscribe_link' ),
			array( 'label' => 'Signup: Date', 'value' => 'subscriber.signup.date' ),
			array( 'label' => 'Signup: URL', 'value' => 'subscriber.signup.url' ),
			array( 'label' => 'Signup: City', 'value' => 'subscriber.signup.city' ),
			array( 'label' => 'Signup: State', 'value' => 'subscriber.signup.state' ),
			array( 'label' => 'Signup: Postal code', 'value' => 'subscriber.signup.postal_code' ),
			array( 'label' => 'Signup: Area code', 'value' => 'subscriber.signup.area_code' ),
			array( 'label' => 'Signup: Country', 'value' => 'subscriber.signup.country' ),
			array( 'label' => 'Signup: Region', 'value' => 'subscriber.signup.region' ),
			array( 'label' => 'Signup: DMA', 'value' => 'subscriber.signup.dma' ),
			array( 'label' => 'Signup: Longitude', 'value' => 'subscriber.signup.longitude' ),
			array( 'label' => 'Signup: Latitude', 'value' => 'subscriber.signup.latitude' ),
			array( 'label' => 'Archive URL', 'value' => 'message.archive_url' ),
			array( 'label' => 'List: Signature', 'value' => 'list.signature' ),
			array( 'label' => 'List: Company name', 'value' => 'list.company_name' ),
			array( 'label' => 'List: Contact address', 'value' => 'list.contact_address' ),
			array( 'label' => 'List: Identifier', 'value' => 'list.id' ),
			array( 'label' => 'List: UUID', 'value' => 'list.uuid' ),
		);

		$this->api = new NGL_Aweber_API( $this->api_key );
		$account = $this->api->me();
		if( ! empty( $account ) && isset( $account[ 'lists_collection_link' ] ) ) {
			$lists = $this->api->getCollection( $account[ 'lists_collection_link' ] );
			if( ! empty( $lists ) && isset( $lists[0][ 'custom_fields_collection_link' ] ) ) {
				$custom_fields = $this->api->getCollection( $lists[0][ 'custom_fields_collection_link' ] );
				if( is_array( $custom_fields ) ) {
					foreach( $custom_fields as $field ) {
						if( isset( $field[ 'name' ] ) && ! empty( $field[ 'name' ] ) ) {
							$_fields[] = array( 'label' => $field[ 'name' ], 'value' => 'subscriber.custom_field["' . $field[ "name" ] . '"]' );
						}
					}
				}
			}
		}

		if( count( $_fields ) ) {
			array_multisort( array_column( $_fields, 'label' ), SORT_ASC, $_fields );
			array_unshift( $_fields, array( 'value' => 'tag', 'label' => 'Select tag' ) );
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
				$operator     = $condition->operator;
				$value        = $condition->value;
				$relationship = $condition->relationship;

				$conditionQuery .= $relationship == "AND" ? " and " : " or ";

				// handle tags
				if( is_array( $value ) ) {
					foreach( $value as $tag ) {
						if( $operator == 'eq' ) {
							$conditionQuery .= '"' . $tag . '" in subscriber.tags and ';
						} else if( $operator == 'ex' ) {
							$conditionQuery .= '"' . $tag . '" in subscriber.tags or ';
						}
					}
					
					$conditionQuery = rtrim( $conditionQuery, " and " );
					$conditionQuery = rtrim( $conditionQuery, " or " );

				} else {

					$value = '"' . $value . '"';

					//handle custom fields
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
					}
				}
			}

			$conditionQuery = ltrim( $conditionQuery, " and " );
			$conditionQuery = ltrim( $conditionQuery, " or " );

			if( ! empty( $conditionQuery ) ) {
				$content = '<!--{% if ' . $conditionQuery . ' %}-->';
					$content .= $element->outertext;
				$content .= '<!--{% endif %}-->';
				$element->outertext = $content;
			}
		}

		$output->save();

		return (string) $output;
	}
}