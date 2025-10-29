<?php
/**
 * Klaviyo.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Class.
 */
class NGL_Klaviyo extends NGL_Abstract_Integration {

	public $app     = 'klaviyo';
	public $api_key = null;
	public $api     = null;

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Include needed files.
		include_once 'lib/vendor/autoload.php';
		include_once 'lib/api.php';

		$this->get_api_key();

		add_filter( 'newsletterglue_email_content_klaviyo', array( $this, 'newsletterglue_email_content_klaviyo' ), 10, 3 );

		add_filter( 'newsltterglue_klaviyo_html_content', array( $this, 'html_content' ), 10, 2 );
	}

	/**
	 * Get API Key.
	 */
	public function get_api_key() {

		$integrations = get_option( 'newsletterglue_integrations' );
		$integration  = isset( $integrations[ $this->app ] ) ? $integrations[ $this->app ] : '';

		$this->api_key = isset( $integration['api_key'] ) ? $integration['api_key'] : '';
	}

	/**
	 * Add Integration.
	 */
	public function add_integration( $args = array() ) {

		$args = $this->get_connection_args( $args );

		$api_key = $args['api_key'];

		$this->api = new NG_Klaviyo_API( $api_key );

		$account = $this->api->get_account();

		if ( empty( $account ) ) {

			$this->remove_integration();

			$result = array( 'response' => 'invalid' );

			delete_option( 'newsletterglue_klaviyo' );

		} else {

			if ( ! $this->already_integrated( $this->app, $api_key ) ) {
				$this->save_integration( $api_key, $account );
			}

			$result = array( 'response' => 'successful' );

			update_option( 'newsletterglue_klaviyo', $account );

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

		$integrations[ $this->app ]            = array();
		$integrations[ $this->app ]['api_key'] = $api_key;

		$integrations[ $this->app ]['connection_name'] = newsletterglue_get_name( $this->app );

		update_option( 'newsletterglue_integrations', $integrations );

		// Add default options.
		$globals = get_option( 'newsletterglue_options' );

		$options = array(
			'from_name'  => ! empty( $account['attributes']['contact_information']['default_sender_name'] ) ? $account['attributes']['contact_information']['default_sender_name'] : newsletterglue_get_default_from_name(),
			'from_email' => ! empty( $account['attributes']['contact_information']['default_sender_email'] ) ? $account['attributes']['contact_information']['default_sender_email'] : get_option( 'admin_email' ),
		);

		foreach ( $options as $key => $value ) {
			$globals[ $this->app ][ $key ] = $value;
		}

		update_option( 'newsletterglue_options', $globals );
	}

	/**
	 * Connect.
	 */
	public function connect() {

		$this->api = new NG_Klaviyo_API( $this->api_key );
	}

	/**
	 * Get form defaults.
	 */
	public function get_form_defaults() {

		$this->api = new NG_Klaviyo_API( $this->api_key );

		$defaults = array();

		$defaults['lists'] = $this->get_lists();

		return $defaults;
	}

	/**
	 * Get lists.
	 */
	public function get_lists() {

		$_lists = array();

		$lists = $this->api->get_lists();

		if ( ! empty( $lists ) && is_array( $lists ) ) {
			foreach ( $lists as $key => $data ) {
				$_lists[ $data['id'] ] = $data['attributes']['name'];
			}
		}

		asort( $_lists );

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

		$subject      = isset( $data['subject'] ) ? ngl_safe_title( $data['subject'] ) : ngl_safe_title( $post->post_title );
		$from_name    = isset( $data['from_name'] ) ? $data['from_name'] : newsletterglue_get_default_from_name();
		$from_email   = isset( $data['from_email'] ) ? $data['from_email'] : $this->get_current_user_email();
		$list         = isset( $data['lists'] ) ? $data['lists'] : '';
		$schedule     = isset( $data['schedule'] ) ? $data['schedule'] : 'immediately';
		$track_opens  = ! empty( $data['track_opens'] ) ? boolval( $data['track_opens'] ) : false;
		$track_clicks = ! empty( $data['track_clicks'] ) ? boolval( $data['track_clicks'] ) : false;
		$utm_source   = ! empty( $data['utm_source'] ) ? $data['utm_source'] : '';
		$utm_campaign = ! empty( $data['utm_campaign'] ) ? $data['utm_campaign'] : '';
		$utm_medium   = ! empty( $data['utm_medium'] ) ? $data['utm_medium'] : '';
		$utm_content  = ! empty( $data['utm_content'] ) ? $data['utm_content'] : '';

		$subject = apply_filters( 'newsletterglue_email_subject_line', $subject, $post, $data, $test, $this );

		// Empty content.
		if ( $test && isset( $post->post_status ) && $post->post_status === 'auto-draft' ) {

			$response['fail'] = $this->nothing_to_send();

			return $response;

		}

		$htmlData = newsletterglue_generate_content( $post, $subject, $this->app );
		$htmlData = str_replace( '<!--%%', '', $htmlData );
		$htmlData = str_replace( '%%-->', '', $htmlData );

		// Do test email.
		if ( $test ) {
			$response = array();

			$test_email     = $data['test_email'];
			$test_email_arr = explode( ',', $test_email );
			$test_emails    = array_map( 'trim', $test_email_arr );
			if ( ! empty( $test_emails ) ) {
				foreach ( $test_emails as $testid ) {
					if ( ! is_email( $testid ) ) {
						$response['fail'] = __( 'Please enter a valid email', 'newsletter-glue' );
					}
				}
			}
			if ( ! empty( $response['fail'] ) ) {
				return $response;
			}

			add_filter( 'wp_mail_content_type', array( $this, 'wp_mail_content_type' ) );

			$body = $htmlData;

			wp_mail( $test_emails, sprintf( __( '[Test] %s', 'newsletter-glue' ), $subject ), $body ); // phpcs:ignore

			$response['success'] = $this->get_test_success_msg();

			return $response;

		}

		$this->api = new NG_Klaviyo_API( $this->api_key );

		// Create template.
		$args = array(
			'data' => array(
				'type'       => 'template',
				'attributes' => array(
					'name'        => sprintf( __( 'Template: %s', 'newsletter-glue' ), $subject ),
					'editor_type' => 'CODE',
					'html'        => $htmlData,
					'text'        => wp_strip_all_tags( $htmlData ),
				),
			),
		);

		$template = $this->api->create_template( $args );

		$template_id = ! empty( $template['id'] ) ? $template['id'] : null;

		if ( empty( $template_id ) ) {
			$status = array( 'status' => 'error' );
			newsletterglue_add_campaign_data( $post_id, $subject, $this->get_status( $status ) );
			return $status;
		}

		// create campaign
		if ( ! empty( $utm_source ) || ! empty( $utm_campaign ) || ! empty( $utm_medium ) ) {
			$tracking_options['is_add_utm']   = true;
			$tracking_options['utm_params'][] = array(
				'name'  => 'utm_source',
				'value' => $utm_source,
			);
			$tracking_options['utm_params'][] = array(
				'name'  => 'utm_campaign',
				'value' => $utm_campaign,
			);
			$tracking_options['utm_params'][] = array(
				'name'  => 'utm_medium',
				'value' => $utm_medium,
			);
		}
		$tracking_options['is_tracking_clicks'] = $track_clicks;
		$tracking_options['is_tracking_opens']  = $track_opens;

		$campaign_args = array(
			'data' => array(
				'type'       => 'campaign',
				'attributes' => array(
					'name'              => $subject,
					'audiences'         => array(
						'included' => array( $list ),
					),
					'campaign-messages' => array(
						'data' => array(
							array(
								'type'       => 'campaign-message',
								'attributes' => array(
									'channel' => 'email',
									'content' => array(
										'subject'    => $subject,
										'from_email' => $from_email,
									),
								),
							),
						),
					),
					'send_options'      => array(
						'use_smart_sending' => false,
					),
					'tracking_options'  => $tracking_options,
				),
			),
		);

		$campaign = $this->api->create_campaign( $campaign_args );

		$campaign_id = ! empty( $campaign['id'] ) ? $campaign['id'] : null;
		$message_id  = ! empty( $campaign['relationships']['campaign-messages']['data'][0]['id'] ) ? $campaign['relationships']['campaign-messages']['data'][0]['id'] : null;

		if ( empty( $campaign_id ) || empty( $message_id ) ) {
			$status = array( 'status' => 'error' );
			newsletterglue_add_campaign_data( $post_id, $subject, $this->get_status( $status ) );
			return $status;
		}

		$content_args = array(
			'data' => array(
				'type'          => 'campaign-message',
				'id'            => $message_id,
				'relationships' => array(
					'template' => array(
						'data' => array(
							'type' => 'template',
							'id'   => $template_id,
						),
					),
				),
			),
		);

		$campaign_content = $this->api->set_campaign_content( $content_args );

		if ( empty( $campaign_content['data']['id'] ) ) {
			$status = array( 'status' => 'error' );
			newsletterglue_add_campaign_data( $post_id, $subject, $this->get_status( $status ) );
			return $status;
		}

		if ( $schedule === 'draft' ) {
			$status = array( 'status' => 'draft' );
		} else {
			$campaign_trigger_args = array(
				'data' => array(
					'type' => 'campaign-send-job',
					'id'   => $campaign_id,
				),
			);

			$send = $this->api->send_campaign( $campaign_trigger_args );

			if ( empty( $send['id'] ) ) {
				$status = array( 'status' => 'error' );
			} else {
				$status = array( 'status' => 'sent' );
			}
		}

		newsletterglue_add_campaign_data( $post_id, $subject, $this->get_status( $status ), $campaign_id );

		return $status;
	}

	/**
	 * Customize content.
	 */
	public function newsletterglue_email_content_klaviyo( $content, $post, $subject ) {

		if ( strstr( $content, '{{ unsubscribe_link }}' ) ) {
			return $content;
		}

		$post_id       = $post->ID;
		$data          = get_post_meta( $post_id, '_newsletterglue', true );
		$default_unsub = $this->default_unsub();
		$unsub         = ! empty( $data['unsub'] ) ? $data['unsub'] : $default_unsub;

		if ( empty( $unsub ) ) {
			$unsub = $this->default_unsub();
		}

		$unsub = str_replace( '{{ unsubscribe_link }}', '{% unsubscribe_link %}', $unsub );

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
	 * Add user to this ESP.
	 */
	public function add_user( $data ) {
		extract( $data );

		if ( empty( $email ) ) {
			return -1;
		}

		$this->api = new NG_Klaviyo_API( $this->api_key );

		if ( ! empty( $list_id ) ) {

			$args = array(
				'email' => $email,
			);

			if ( ! empty( $name ) ) {
				$args['name'] = $name;
			}

			$result = $this->api->add_subscriber( $list_id, $args );

			if ( isset( $extra_list ) && ! empty( $extra_list_id ) ) {
				$result = $this->api->add_subscriber( $extra_list_id, $args );
			}

		}

		return $result;
	}

	/**
	 * Get connect settings.
	 */
	public function get_connect_settings( $integrations = array() ) {

		$app = $this->app;

		newsletterglue_text_field(
			array(
				'id'          => "ngl_{$app}_key",
				'placeholder' => esc_html__( 'Enter API Key', 'newsletter-glue' ),
				'value'       => isset( $integrations[ $app ]['api_key'] ) ? $integrations[ $app ]['api_key'] : '',
				'helper'      => '<a href="https://www.klaviyo.com/account#api-keys-tab" target="_blank" class="ngl-link-inline-svg">' . __( 'Get API key', 'newsletter-glue' ) . ' [externallink]</a>',
				'type'        => 'password',
			)
		);
	}

	/**
	 * Has email verify.
	 */
	public function has_email_verify() {
		return false;
	}

	/**
	 * Get lists compat.
	 */
	public function _get_lists_compat() {
		$this->api = new NG_Klaviyo_API( $this->api_key );
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
			case 'unsubscribe_link':
				return '{% unsubscribe_link %}';
			break;
			case 'email':
				return '{{ email }}';
			break;
			case 'phone':
				return '{{ person.phone_number }}';
			break;
			case 'first_name':
				return ! empty( $fallback ) ? '{{ first_name|default:' . "'" . $fallback . "'" . '}}' : '{{ first_name }}';
			break;
			case 'last_name':
				return ! empty( $fallback ) ? '{{ last_name|default:' . "'" . $fallback . "'" . '}}' : '{{ last_name }}';
			break;
			case 'update_preferences':
				return '{% manage_preferences_link %}';
			break;
			case 'admin_address':
				return '{{ organization.full_address }}';
			break;
			default:
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
			'lists' => array(
				'type'     => 'select',
				'callback' => 'get_lists',
				'title'    => __( 'List', 'newsletter-glue' ),
				'help'     => __( 'Who receives your email.', 'newsletter-glue' ),
			),
		);
	}

	/**
	 * Get custom fields of esp
	 */
	public function get_custom_fields() {

		// Predefined fields
		$_fields = array(
			array(
				'label' => 'First name',
				'value' => 'first_name',
			),
			array(
				'label' => 'Last name',
				'value' => 'last_name',
			),
			array(
				'label' => 'Email address',
				'value' => 'email',
			),
			array(
				'label' => 'Phone number',
				'value' => 'phone_number',
			),
			array(
				'label' => 'Organization name',
				'value' => 'organization',
			),
			array(
				'label' => 'Customer title',
				'value' => 'title',
			),
			array(
				'label' => 'City',
				'value' => 'city',
			),
			array(
				'label' => 'Region',
				'value' => 'region',
			),
			array(
				'label' => 'Country',
				'value' => 'country',
			),
			array(
				'label' => 'Zip',
				'value' => 'zip',
			),
			array(
				'label' => 'Address1',
				'value' => 'address1',
			),
			array(
				'label' => 'Address2',
				'value' => 'address2',
			),
			array(
				'label' => 'Timezone',
				'value' => 'timezone',
			),
			array(
				'label' => 'Source',
				'value' => 'source',
			),
			array(
				'label' => 'Latitude',
				'value' => 'latitude',
			),
			array(
				'label' => 'Longitude',
				'value' => 'longitude',
			),
			array(
				'label' => 'UUID',
				'value' => 'id',
			),
		);

		// no direct api for getting custom fields. We will use manual input here.

		if ( count( $_fields ) ) {
			array_multisort( array_column( $_fields, 'label' ), SORT_ASC, $_fields );
			array_unshift(
				$_fields,
				array(
					'value' => 'manual',
					'label' => 'Set manual condition',
				)
			);
			array_unshift(
				$_fields,
				array(
					'value' => '',
					'label' => 'Select an option',
				)
			);
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
		foreach ( $output->find( $replace ) as $key => $element ) {

			$conditions = json_decode( $element->{ 'data-conditions' } );
			$element->removeAttribute( 'data-conditions' );

			$conditionQuery = '';

			foreach ( $conditions as $condition ) {
				$key          = $condition->key;
				$key_manual   = $condition->key_manual;
				$operator     = $condition->operator;
				$value        = $condition->value;
				$relationship = $condition->relationship;

				$conditionQuery .= $relationship == 'AND' ? ' and ' : ' or ';

				if ( $key == 'manual' && ! empty( $key_manual ) && strpos( $value, 'List:' ) === 0 ) {

					$value  = substr( $value, 5 );
					$values = explode( ',', $value );
					$key    = '"' . $key_manual . '"';

					foreach ( $values as $value ) {
						$value = '"' . trim( $value ) . '"';

						if ( $operator == 'eq' ) {
							$conditionQuery .= "$value in person|lookup:$key and ";
						} elseif ( $operator == 'neq' ) {
							$conditionQuery .= "not $value in person|lookup:$key and ";
						} elseif ( $operator == 'ex' ) {
							$conditionQuery .= "$value in person|lookup:$key or ";
						} elseif ( $operator == 'nex' ) {
							$conditionQuery .= "not $value in person|lookup:$key or ";
						}
					}

					$conditionQuery = rtrim( $conditionQuery, ' and ' );
					$conditionQuery = rtrim( $conditionQuery, ' or ' );

				} else {

					if ( $key == 'manual' && ! empty( $key_manual ) ) {
						$key = $key_manual;
					}

					$key = '"' . $key . '"';

					if ( ! is_numeric( $value ) ) {
						if ( strval( strtolower( $value ) ) === 'true' ) {
							$value = 1;
						} elseif ( strval( strtolower( $value ) ) === 'false' ) {
							$value = 0;
						} elseif ( DateTime::createFromFormat( 'Y-m-d', $value ) !== false ) {
							$key  .= '|format_date_string|date:"Y-m-d"';
							$value = '"' . $value . '"';
						} else {
							$value = '"' . $value . '"';
						}
					}

					if ( $operator == 'eq' ) {
						$conditionQuery .= "person|lookup:$key == $value";
					} elseif ( $operator == 'neq' ) {
						$conditionQuery .= "person|lookup:$key != $value";
					} elseif ( $operator == 'lt' ) {
						$conditionQuery .= "person|lookup:$key < $value";
					} elseif ( $operator == 'gt' ) {
						$conditionQuery .= "person|lookup:$key > $value";
					} elseif ( $operator == 'lte' ) {
						$conditionQuery .= "person|lookup:$key <= $value";
					} elseif ( $operator == 'gte' ) {
						$conditionQuery .= "person|lookup:$key >= $value";
					} elseif ( $operator == 'ex' ) {
						$conditionQuery .= "person|lookup:$key";
					} elseif ( $operator == 'nex' ) {
						$conditionQuery .= "not person|lookup:$key";
					}
				}
			}

			$conditionQuery = ltrim( $conditionQuery, ' and ' );
			$conditionQuery = ltrim( $conditionQuery, ' or ' );

			if ( ! empty( $conditionQuery ) ) {
				$content            = "<!--%%{% if $conditionQuery %}%%-->";
					$content       .= $element->outertext;
				$content           .= '<!--%%{% endif %}%%-->';
				$element->outertext = $content;
			}
		}

		$output->save();

		return (string) $output;
	}
}
