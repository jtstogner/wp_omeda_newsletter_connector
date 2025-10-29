<?php
/**
 * Sendy.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main Class.
 */
class NGL_Sendy extends NGL_Abstract_Integration {

	public $app		= 'sendy';
	public $api_key = null;
	public $api_url = null;
	public $api 	= null;

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Include needed files.
		include_once 'lib/client.php';

		$this->get_api_key();

		add_filter( 'newsletterglue_email_content_sendy', array( $this, 'newsletterglue_email_content_sendy' ), 10, 3 );

		add_action( 'newsletterglue_edit_more_settings', array( $this, 'newsletterglue_edit_more_settings' ), 50, 3 );

		add_filter( 'newsltterglue_sendy_html_content', array( $this, 'html_content' ), 10, 2 );
	}

	/**
	 * Get API Key.
	 */
	public function get_api_key() {

		$integrations = get_option( 'newsletterglue_integrations' );
		$integration  = isset( $integrations[ $this->app ] ) ? $integrations[ $this->app ] : '';

		$this->api_key 		= isset( $integration[ 'api_key' ] ) ? $integration[ 'api_key' ] : '';
		$this->api_url 		= isset( $integration[ 'api_url' ] ) ? $integration[ 'api_url' ] : '';

	}

	/**
	 * Add Integration.
	 */
	public function add_integration( $args = array() ) {

		$args 		= $this->get_connection_args( $args );

		$api_key 	= $args[ 'api_key' ];
		$api_url 	= $args[ 'api_url' ];

		$this->api = new NGL_Sendy_API( untrailingslashit( $api_url ), $api_key );

		$testconnection = $this->api->post( '/api/campaigns/create.php', array( 'boolean' => true ) );

		if ( strstr( $testconnection, 'Invalid' ) || ! $testconnection ) {

			$this->remove_integration();

			$result = array( 'response' => 'invalid' );

			delete_option( 'newsletterglue_sendy' );

		} else {

			$this->api_key = $api_key;
			$this->api_url = $api_url;

			if ( ! $this->already_integrated( $this->app, $api_key ) ) {
				$this->save_integration( $api_key, $api_url );
			}

			$result = array( 'response' => 'successful' );

			update_option( 'newsletterglue_sendy', array() );

		}

		return $result;

	}

	/**
	 * Save Integration.
	 */
	public function save_integration( $api_key = '', $api_url = '' ) {

		// Set these in memory.
		$this->api_key = $api_key;
		$this->api_url = $api_url;

		delete_option( 'newsletterglue_integrations' );

		$integrations = get_option( 'newsletterglue_integrations' );

		$integrations[ $this->app ] = array();
		$integrations[ $this->app ][ 'api_key' ] = $api_key;
		$integrations[ $this->app ][ 'api_url' ] = $api_url;

		update_option( 'newsletterglue_integrations', $integrations );

		// Add default options.
		$globals = get_option( 'newsletterglue_options' );
		
		$options = array(
			'from_name' 	=> newsletterglue_get_default_from_name(),
			'from_email'	=> get_option( 'admin_email' ),
			'unsub'			=> $this->default_unsub(),
			'track_opens'	=> 1,
			'track_clicks'	=> 1,
		);

		foreach( $options as $key => $value ) {
			$globals[ $this->app ][ $key ] = $value;
		}

		update_option( 'newsletterglue_options', $globals );
	}

	/**
	 * Get connect settings.
	 */
	public function get_connect_settings( $integrations = array() ) {

		$app = $this->app;

		newsletterglue_text_field( array(
			'id' 			=> "ngl_{$app}_url",
			'placeholder' 	=> esc_html__( 'Enter Sendy installation URL', 'newsletter-glue' ),
			'value'			=> isset( $integrations[ $app ]['api_url'] ) ? $integrations[ $app ]['api_url'] : '',
			'class'			=> 'ngl-text-margin',
		) );

		newsletterglue_text_field( array(
			'id' 			=> "ngl_{$app}_key",
			'placeholder' 	=> esc_html__( 'Enter API Key', 'newsletter-glue' ),
			'value'			=> isset( $integrations[ $app ]['api_key'] ) ? $integrations[ $app ]['api_key'] : '',
			'type'			=> 'password',
		) );

	}

	/**
	 * Returns true if test emails are sent by WordPress.
	 */
	public function test_email_by_wordpress() {
		return true;
	}

	/**
	 * Connect.
	 */
	public function connect() {
		$this->api = new NGL_Sendy_API( untrailingslashit( $this->api_url ), $this->api_key );
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
		$lists      	= isset( $data['lists'] ) && ! empty( $data['lists'] ) ? $data['lists'] : '';
		$brand			= ! empty( $data['brand'] ) ? $data['brand'] : 1;
		$schedule   	= isset( $data['schedule'] ) ? $data['schedule'] : 'immediately';
		$track_opens 	= ! empty( $data[ 'track_opens' ] ) ? absint( $data[ 'track_opens' ] ) : 0;
		$track_clicks 	= ! empty( $data[ 'track_clicks' ] ) ? absint( $data[ 'track_clicks' ] ) : 0;

		$subject = apply_filters( 'newsletterglue_email_subject_line', $subject, $post, $data, $test, $this );

		// Send a campaign live or draft.
		$this->api = new NGL_Sendy_API( untrailingslashit( $this->api_url ), $this->api_key );

		// At least set lists.
		if ( empty( $lists ) ) {
			$_lists 	= $this->get_lists( $brand );
			$lists 		= array_keys( $_lists );
			$lists 		= implode( ',', $lists );
		}

		$html_text 	= newsletterglue_generate_content( $post, $subject, $this->app );

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

			$body = $html_text;

			wp_mail( $test_emails, sprintf( __( '[Test] %s', 'newsletter-glue' ), $subject ), $body ); // phpcs:ignore

			$response['success'] = $this->get_test_success_msg();

			return $response;

		}

		$args = array(
			'from_name'		=> $from_name,
			'from_email'	=> $from_email,
			'reply_to'		=> $from_email,
			'boolean'		=> true,
			'title'			=> $subject,
			'subject'		=> $subject,
			'html_text'		=> $html_text,
			'plain_text'	=> wp_strip_all_tags( $html_text ), 
			'send_campaign'	=> ( $schedule === 'immediately' ) ? 1 : 0,
			'track_opens'	=> $track_opens,
			'track_clicks'	=> $track_clicks,
		);

		if ( $schedule != 'immediately' ) {
			$args[ 'brand_id' ] = ( $brand ) ? $brand : 1;
		} else {
			$args[ 'list_ids' ] = is_array( $lists ) ? implode( ',', $lists ) : $lists;
		}

		$campaign = $this->api->post( '/api/campaigns/create.php', $args );

		if ( ! $campaign || ( is_string( $campaign ) && strstr( $campaign, 'Forbidden' ) ) )  {
			$args[ 'html_text' ] = str_replace( 'xmlns="http://www.w3.org/1999/xhtml"', '', $html_text );
			$args[ 'html_text' ] = str_replace( '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">', '', $args[ 'html_text' ] );
			$campaign = $this->api->post( '/api/campaigns/create.php', $args );
		}

		if ( $schedule === 'draft' ) {
			$result = array( 'status' => 'draft' );
		} else {
			$result = array( 'status' => 'sent' );
		}

		newsletterglue_add_campaign_data( $post_id, $subject, $this->prepare_message( ( array ) $result ), '' );

		return $campaign;

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
	 * Get settings.
	 */
	public function get_settings() {
		$settings = new stdclass;

		$settings->unsub 		= newsletterglue_get_option( 'unsub', $this->app );

		$settings->track_clicks = newsletterglue_get_option( 'track_clicks', $this->app );
		$settings->track_opens  = newsletterglue_get_option( 'track_opens', $this->app );

		return $settings;
	}

	/**
	 * Customize content.
	 */
	public function newsletterglue_email_content_sendy( $content, $post, $subject ) {

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

		$unsub = str_replace( '{{ unsubscribe_link }}', '[unsubscribe]', $unsub );

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
	 * Add extra settings to metabox.
	 */
	public function newsletterglue_edit_more_settings( $app, $settings, $ajax = false ) {
		if ( $app === $this->app ) {

			$default_unsub = $this->default_unsub();
			$unsub = ! empty( $settings->unsub ) ? $settings->unsub : newsletterglue_get_option( 'unsub', $app );

			$track_clicks = isset( $settings->track_clicks ) ? $settings->track_clicks : newsletterglue_get_option( 'track_clicks', $app );
			$track_opens  = isset( $settings->track_opens ) ? $settings->track_opens : newsletterglue_get_option( 'track_opens', $app );
			?>
			<div class="ngl-metabox-flexfull">
				<div class="ngl-metabox-flex">
					<div class="ngl-metabox-flex">
						<div class="ngl-metabox-header">
							<?php esc_html_e( 'Sendy tracking', 'newsletter-glue' ); ?>
							<?php $this->input_verification_info(); ?>
						</div>
						<div class="ngl-field">
							<div class="ngl-field ngl-tabbed-check">
								<div class="ngl-label-sub"><?php _e( 'Track clicks:', 'newsletter-glue' ); ?></div>
								<input type="text" class="ngl-value-hidden" name="ngl_track_clicks" id="ngl_track_clicks" value="<?php echo absint( $track_clicks ); ?>" />
								<div class="ui basic buttons">
								  <div class="ui button <?php echo $track_clicks == 1 ? 'active' : ''; ?>" data-value="1"><svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><polyline points="20 6 9 17 4 12"></polyline></svg><?php _e( 'Yes', 'newsletter-glue' ); ?></div>
								  <div class="ui button <?php echo $track_clicks == 0 ? 'active' : ''; ?>" data-value="0"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 16 16" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M8 8.707l3.646 3.647.708-.707L8.707 8l3.647-3.646-.707-.708L8 7.293 4.354 3.646l-.707.708L7.293 8l-3.646 3.646.707.708L8 8.707z"></path></svg><?php _e( 'No', 'newsletter-glue' ); ?></div>
								  <div class="ui button <?php echo $track_clicks == 2 ? 'active' : ''; ?>" data-value="2"><svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><polyline points="20 6 9 17 4 12"></polyline></svg><?php _e( 'Anonymously', 'newsletter-glue' ); ?></div>
								</div>
							</div>
							<div class="ngl-field ngl-tabbed-check">
								<div class="ngl-label-sub"><?php _e( 'Track opens:', 'newsletter-glue' ); ?></div>
								<input type="text" class="ngl-value-hidden" name="ngl_track_opens" id="ngl_track_opens" value="<?php echo absint( $track_opens ); ?>" />
								<div class="ui basic buttons">
								  <div class="ui button <?php echo $track_opens == 1 ? 'active' : ''; ?>" data-value="1"><svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><polyline points="20 6 9 17 4 12"></polyline></svg><?php _e( 'Yes', 'newsletter-glue' ); ?></div>
								  <div class="ui button <?php echo $track_opens == 0 ? 'active' : ''; ?>" data-value="0"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 16 16" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M8 8.707l3.646 3.647.708-.707L8.707 8l3.647-3.646-.707-.708L8 7.293 4.354 3.646l-.707.708L7.293 8l-3.646 3.646.707.708L8 8.707z"></path></svg><?php _e( 'No', 'newsletter-glue' ); ?></div>
								  <div class="ui button <?php echo $track_opens == 2 ? 'active' : ''; ?>" data-value="2"><svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><polyline points="20 6 9 17 4 12"></polyline></svg><?php _e( 'Anonymously', 'newsletter-glue' ); ?></div>
								</div>
							</div>
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
	 * Has email verify.
	 */
	public function has_email_verify() {
		return false;
	}

	/**
	 * Get email verify help.
	 */
	public function get_email_verify_help() {
		return 'https://sendy.co/forum/discussion/13226/how-to-verify-email-addresses-in-ses/p1';
	}

	/**
	 * Add user to this ESP.
	 */
	public function add_user( $data ) {
		extract( $data );

		if ( empty( $email ) ) {
			return -1;
		}

		$name = ! empty( $name ) ? $name : '';

		$this->api = new NGL_Sendy_API( untrailingslashit( $this->api_url ), $this->api_key );

		if ( ! empty( $list_id ) ) {
			$args = array(
				'api_key'	=> $this->api_key,
				'name'		=> $name,
				'email'		=> $email,
				'list'		=> $list_id,
				'boolean' => true,
			);

			$subscribe = $this->api->post( '/subscribe', $args );
		}

		if ( isset( $extra_list ) && ! empty( $extra_list_id ) ) {
			$args = array(
				'api_key'	=> $this->api_key,
				'name'		=> $name,
				'email'		=> $email,
				'list'		=> $extra_list_id,
				'boolean' => true,
			);

			$subscribe = $this->api->post( '/subscribe', $args );
		}

		return true;

	}

	/**
	 * Replace universal tags with esp tags.
	 */
	public function html_content( $html, $post_id ) {

		$html = $this->convert_tags( $html, $post_id );

		return $html;
	}

	/**
	 * Code supported tags for this ESP.
	 */
	public function get_tag( $tag, $post_id = 0, $fallback = null ) {

		switch ( $tag ) {
			case 'unsubscribe_link' :
				return '[unsubscribe]';
			break;
			case 'name' :
				return ! empty( $fallback ) ? '[Name,fallback=' . $fallback . ']' : '[Name]';
			break;
			case 'email' :
				return ! empty( $fallback ) ? '[Email,fallback=' . $fallback . ']' : '[Email]';
			break;
			default :
				return apply_filters( "newsletterglue_{$this->app}_custom_tag", '', $tag, $post_id );
			break;
		}

		return false;
	}

	/**
	 * Get form defaults.
	 */
	public function get_form_defaults() {
		global $post;

		$this->api = new NGL_Sendy_API( untrailingslashit( $this->api_url ), $this->api_key );

		$brand = newsletterglue_get_option( 'brand', 'sendy' );

		if ( isset( $post->ID ) ) {
			$settings   = newsletterglue_get_data( $post->ID );
			if ( isset( $settings->brand ) ) {
				$brand = $settings->brand;
			}
		}

		$defaults[ 'lists' ] = $this->get_lists( $brand ? $brand : 1 );

		return $defaults;
	}

	/**
	 * Get lists.
	 */
	public function get_lists( $brand = 1 ) {
		$lists = array();

		$req = $this->api->post( '/api/lists/get-lists.php', array( 'brand_id' => $brand ) );

		if ( ! empty( $req ) ) {
			$listArray = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $req ), true );
			foreach( $listArray as $key => $data ) {
				$lists[ $data['id'] ] = $data['name'];
			}
		}

		asort( $lists );

		return $lists;
	}

	/**
	 * Get lists compat.
	 */
	public function _get_lists_compat() {
		$this->api = new NGL_Sendy_API( untrailingslashit( $this->api_url ), $this->api_key );
		return $this->get_lists();
	}

	/**
	 * Get brands.
	 */
	public function get_brands() {
		$brands = array();

		$req = $this->api->post( '/api/brands/get-brands.php' );

		if ( ! empty( $req ) ) {
			$req = json_decode( $req );
			foreach( $req as $key => $data ) {
				$brands[ $data->id ] = $data->name;
			}
		}

		asort( $brands );

		return $brands;
	}

	/**
	 * Get segments HTML.
	 */
	public function get_segments_html( $audience_id = '' ) {
		?>
		<div class="ngl-metabox-flex ngl-metabox-segment">
			<div class="ngl-metabox-header">
				<label for="ngl_lists"><?php esc_html_e( 'List', 'newsletter-glue' ); ?></label>
				<?php $this->input_verification_info(); ?>
			</div>
			<div class="ngl-field">
				<?php
					$lists = '';
					if ( isset( $settings->lists ) ) {
						$lists = $settings->lists;
					} else {
						$lists = newsletterglue_get_option( 'lists', 'sendy' );
					}

					newsletterglue_select_field( array(
						'id' 			=> 'ngl_lists',
						'legacy'		=> true,
						'multiple'		=> true,
						'helper'		=> __( 'The mailing list within your brand.', 'newsletter-glue' ),
						'class'			=> 'is-required',
						'options'		=> $this->get_lists( $audience_id ),
						'default'		=> is_array( $lists ) ? $lists : explode( ',', $lists ),
						'class'			=> 'ngl-ajax',
					) );
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Configure options array for this ESP.
	 */
	public function option_array() {
		return array(
			'brand'	=> array(
				'type'		=> 'select',
				'callback' 	=> 'get_brands',
				'onchange'	=> 'lists',
				'title'		=> __( 'Brand', 'newsletter-glue' ),
				'help'		=> __( 'The brand of your Sendy installation.', 'newsletter-glue' ),
			),
			'lists' 	=> array(
				'type'		=> 'select',
				'callback'	=> 'get_lists',
				'title'     => __( 'List', 'newsletter-glue' ),
				'help'		=> __( 'The mailing list within your brand.', 'newsletter-glue' ),
				'is_multi'	=> true,
				'param'		=> 'brand',
			),
		);
	}

}
