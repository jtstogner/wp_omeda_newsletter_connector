<?php
/**
 * Pro.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_Pro class.
 */
class NGL_Pro {

	public $id 			= 'newsletterglue_pro_license';
	public $item_id 	= 1266;
	public $item_name 	= 'Newsletter Glue Pro';

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->includes();

		if ( class_exists( 'NGL_License' ) ) {
			$this->init_license();
		}

		// Add setting tab.
		add_filter( 'newsletterglue_settings_tab_license_save_button', '__return_false' );
		add_action( 'newsletterglue_settings_tab_license', array( $this, 'show_settings' ), 20 );

		// AJAX functions.
		add_action( 'wp_ajax_newsletterglue_check_license', array( $this, 'check_license' ) );
		add_action( 'wp_ajax_nopriv_newsletterglue_check_license', array( $this, 'check_license' ) );

		add_action( 'wp_ajax_newsletterglue_deactivate_license', array( $this, 'deactivate_license' ) );
		add_action( 'wp_ajax_nopriv_newsletterglue_deactivate_license', array( $this, 'deactivate_license' ) );

		// Social embeds.
		add_filter( 'newsletterglue_generate_content', array( $this, 'social_embeds' ), 100, 2 );

		// Custom CSS.
		add_action( 'newsletterglue_email_styles', array( $this, 'embed_css' ), 50 );

		// Add notice.
		add_action( 'newsletterglue_common_action_hook', array( $this, 'add_license_notice' ), 10 );

		add_action( 'newsletterglue_before_admin_connect', array( $this, 'add_license_activation_form' ) );
		add_action( 'newsletterglue_before_admin_defaults', array( $this, 'add_license_activation_form' ) );
		add_action( 'newsletterglue_before_admin_blocks', array( $this, 'add_license_activation_form' ) );
		add_action( 'newsletterglue_onboarding_welcome', array( $this, 'add_license_activation_form' ) );

		// Add setting tabs.
		add_filter( 'newsletterglue_settings_tabs', array( $this, 'newsletterglue_settings_tabs' ), 1, 1 );

		add_action( 'admin_notices', array( $this, 'admin_notices' ), 100 );
	}

	/**
	 * Init license.
	 */
	public function init_license() {
		$ngl_license = new NGL_License( $this->id, NGL_VERSION, $this->item_id, $this->item_name, NGL_PLUGIN_FILE );
	}

	/**
	 * Includes.
	 */
	public function includes() {
		require_once NGL_PLUGIN_DIR . 'includes/libraries/license-handler.php';
	}

	/**
	 * Check if it has valid license.
	 */
	public function has_valid_license() {
		return get_option( $this->id ) ? true : false;
	}

	/**
	 * Show tab.
	 */
	public function show_settings() {
	?>
		<div class="ui large header">
			<?php esc_html_e( 'Pro License', 'newsletter-glue' ); ?>
			<div class="sub header"><?php echo esc_html__( 'Add your Newsletter Glue Pro license here to receive updates.', 'newsletter-glue' ); ?></div>
		</div>

		<div class="ngl-cards">

			<div class="ngl-card ngl-card-license">

				<!-- License form -->
				<div class="ngl-card-add2 ngl-card-license-form <?php if ( $this->has_valid_license() ) echo 'ngl-hidden'; ?>">
					<div class="ngl-card-heading"><?php echo esc_html__( 'Newsletter Glue Pro', 'newsletter-glue' ); ?></div>
					<div class="ngl-misc-fields">
						<form action="" method="post" class="ngl-license-form">

							<?php
								newsletterglue_text_field( array(
									'id' 			=> $this->id,
									'label'			=> __( 'License key', 'newsletter-glue' ),
									'helper'		=> '<a href="https://newsletterglue.com/account/" target="_blank" class="ngl-link-inline-svg">' . __( 'Get license key', 'newsletter-glue' ) . ' [externallink]</a>',
									'value'			=> get_option( $this->id ),
									'type'			=> 'password',
								) );
							?>

							<div class="ngl-btn">
								<button class="ui primary button" type="submit"><svg stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg><?php esc_html_e( 'Activate', 'newsletter-glue' ); ?></button>
							</div>

						</form>
					</div>
				</div>

				<div class="ngl-card-view <?php if ( ! $this->has_valid_license() ) echo 'ngl-hidden'; ?>">

					<div class="ngl-card-heading"><?php echo esc_html__( 'Newsletter Glue Pro', 'newsletter-glue' ); ?><span class="ngl-card-heading-sub"><?php echo esc_html( newsletterglue_get_tier_name() ); ?></span></div>

					<div class="ngl-btn">
						<button class="ui primary button ngl-ajax-test-connection"><svg stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg><?php esc_html_e( 'test', 'newsletter-glue' ); ?></button>
					</div>

					<div class="ngl-helper">
						<a href="#" class="ngl-ajax-edit-connection"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 16 16" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M13.23 1h-1.46L3.52 9.25l-.16.22L1 13.59 2.41 15l4.12-2.36.22-.16L15 4.23V2.77L13.23 1zM2.41 13.59l1.51-3 1.45 1.45-2.96 1.55zm3.83-2.06L4.47 9.76l8-8 1.77 1.77-8 8z"></path></svg><?php echo esc_html__( 'edit', 'newsletter-glue' ); ?></a>
						<a href="#" class="ngl-ajax-remove-connection"><svg stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg><?php echo esc_html__( 'deactivate', 'newsletter-glue' ); ?></a>
					</div>

				</div>

				<!-- Testing connection -->
				<div class="ngl-card-state is-testing ngl-hidden">
					<div class="ngl-card-state-wrap">
						<div class="ngl-card-state-icon"><svg class="ngl-infinite-spinner" stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg></div>
						<div class="ngl-card-state-text"><?php esc_html_e( 'Verifying license...', 'newsletter-glue' ); ?></div>
					</div>
					<div class="ngl-card-state-alt ngl-helper">
						<a href="#" class="ngl-ajax-stop-test"><?php echo esc_html__( 'Stop verification', 'newsletter-glue' ); ?></a>
					</div>
				</div>

				<!-- Connection working -->
				<div class="ngl-card-state is-working ngl-hidden">
					<div class="ngl-card-state-wrap">
						<div class="ngl-card-state-icon"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 1024 1024" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M512 64C264.6 64 64 264.6 64 512s200.6 448 448 448 448-200.6 448-448S759.4 64 512 64zm193.5 301.7l-210.6 292a31.8 31.8 0 0 1-51.7 0L318.5 484.9c-3.8-5.3 0-12.7 6.5-12.7h46.9c10.2 0 19.9 4.9 25.9 13.3l71.2 98.8 157.2-218c6-8.3 15.6-13.3 25.9-13.3H699c6.5 0 10.3 7.4 6.5 12.7z"></path></svg></div>
						<div class="ngl-card-state-text"><?php esc_html_e( 'Activated!', 'newsletter-glue' ); ?></div>
					</div>
				</div>

				<!-- Connection not working -->
				<div class="ngl-card-state is-invalid ngl-hidden">
					<div class="ngl-card-link-start is-right">
						<a href="#" class="ui basic noborder button ngl-ajax-test-close"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke-miterlimit="10" stroke-width="32" d="M448 256c0-106-86-192-192-192S64 150 64 256s86 192 192 192 192-86 192-192z"></path><path fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M320 320L192 192m0 128l128-128"></path></svg><?php esc_html_e( 'Close', 'newsletter-glue' ); ?></a>
					</div>
					<div class="ngl-card-state-wrap">
						<div class="ngl-card-state-icon"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M11.953,2C6.465,2,2,6.486,2,12s4.486,10,10,10s10-4.486,10-10S17.493,2,11.953,2z M12,20c-4.411,0-8-3.589-8-8 s3.567-8,7.953-8C16.391,4,20,7.589,20,12S16.411,20,12,20z"></path><path d="M11 7H13V14H11zM11 15H13V17H11z"></path></svg></div>
						<div class="ngl-card-state-text"><?php esc_html_e( 'Not connected', 'newsletter-glue' ); ?></div>
					</div>
					<div class="ngl-card-state-alt ngl-helper">
						<a href="#" class="ngl-ajax-test-again"><?php echo esc_html__( 'Test again', 'newsletter-glue' ); ?></a>
						<a href="#" class="ngl-ajax-edit-connection"><?php echo esc_html__( 'Edit license details', 'newsletter-glue' ); ?></a>
					</div>
					<div class="ngl-card-link-end">
						<a href="mailto:support@newsletterglue.com" class="ui basic noborder button" target="_blank"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M256 76c48.1 0 93.3 18.7 127.3 52.7S436 207.9 436 256s-18.7 93.3-52.7 127.3S304.1 436 256 436c-48.1 0-93.3-18.7-127.3-52.7S76 304.1 76 256s18.7-93.3 52.7-127.3S207.9 76 256 76m0-28C141.1 48 48 141.1 48 256s93.1 208 208 208 208-93.1 208-208S370.9 48 256 48z"></path><path d="M256.7 160c37.5 0 63.3 20.8 63.3 50.7 0 19.8-9.6 33.5-28.1 44.4-17.4 10.1-23.3 17.5-23.3 30.3v7.9h-34.7l-.3-8.6c-1.7-20.6 5.5-33.4 23.6-44 16.9-10.1 24-16.5 24-28.9s-12-21.5-26.9-21.5c-15.1 0-26 9.8-26.8 24.6H192c.7-32.2 24.5-54.9 64.7-54.9zm-26.3 171.4c0-11.5 9.6-20.6 21.4-20.6 11.9 0 21.5 9 21.5 20.6s-9.6 20.6-21.5 20.6-21.4-9-21.4-20.6z"></path></svg><?php esc_html_e( 'Get help', 'newsletter-glue' ); ?></a>
					</div>
				</div>

				<!-- Connection removed -->
				<div class="ngl-card-state is-removed ngl-hidden">
					<div class="ngl-card-state-wrap">
						<div class="ngl-card-state-icon"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><rect width="448" height="80" x="32" y="48" rx="32" ry="32"></rect><path d="M74.45 160a8 8 0 00-8 8.83l26.31 252.56a1.5 1.5 0 000 .22A48 48 0 00140.45 464h231.09a48 48 0 0047.67-42.39v-.21l26.27-252.57a8 8 0 00-8-8.83zm248.86 180.69a16 16 0 11-22.63 22.62L256 318.63l-44.69 44.68a16 16 0 01-22.63-22.62L233.37 296l-44.69-44.69a16 16 0 0122.63-22.62L256 273.37l44.68-44.68a16 16 0 0122.63 22.62L278.62 296z"></path></svg></div>
						<div class="ngl-card-state-text"><?php esc_html_e( 'License deactivated', 'newsletter-glue' ); ?></div>
					</div>
				</div>

				<!-- Remove connection -->
				<div class="ngl-card-state confirm-remove ngl-hidden">
					<div class="ngl-card-state-wrap">
						<div class="ngl-card-state-icon"><svg stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></div>
						<div class="ngl-card-state-text"><?php esc_html_e( 'Deactivate license?', 'newsletter-glue' ); ?></div>
					</div>
					<div class="ngl-card-state-alt ngl-helper">
						<a href="#" class="ngl-ajax-remove ngl-helper-alert"><?php echo esc_html__( 'Confirm', 'newsletter-glue' ); ?></a>
						<a href="#" class="ngl-back"><?php echo esc_html__( 'Go back', 'newsletter-glue' ); ?></a>
					</div>
				</div>

			</div>

		</div>
	<?php
	}

	/**
	 * Check license.
	 */
	public function check_license() {

		check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

		if ( ! current_user_can( 'manage_newsletterglue' ) ) {
			wp_die( -1 );
		}

		foreach( $_POST as $key => $value ) {
			if ( strstr( $key, '_license' ) ) {
				$id = $key;
			}
		}

		if ( ! isset( $id ) || ! class_exists( 'NGL_License' ) ) {
			wp_die( -1 );
		}

		$code 			= isset( $_POST[ $id ] ) ? sanitize_text_field( wp_unslash( $_POST[ $id ] ) ) : '';
		$ngl_license 	= new NGL_License( $this->id, NGL_VERSION, $this->item_id, $this->item_name, NGL_PLUGIN_FILE );
		$result			= $ngl_license->_activate( $code );

		// Deactivate current license.
		$current_code 	= get_option( $this->id );
		if ( trim( $current_code ) !== $code ) {
			$ngl_license->_deactivate( $current_code );
		}

		$this->save_license( $code, $result );

		wp_send_json( $result );

	}

	/**
	 * Check license.
	 */
	public function deactivate_license() {

		check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

		if ( ! current_user_can( 'manage_newsletterglue' ) ) {
			wp_die( -1 );
		}

		if ( ! class_exists( 'NGL_License' ) ) {
			wp_die( -1 );
		}

		$current_code 	= get_option( $this->id );
		$ngl_license 	= new NGL_License( $this->id, NGL_VERSION, $this->item_id, $this->item_name, NGL_PLUGIN_FILE );
		$ngl_license->_deactivate( $current_code );

		delete_option( $this->id );
		delete_option( $this->id . '_expires' );

		wp_die();
	}

	/**
	 * Save license.
	 */
	public function save_license( $code, $result ) {

		delete_option( $this->id );
		delete_option( $this->id . '_expires' );

		if ( isset( $result[ 'status' ] ) ) {

			if ( $result[ 'status' ] === 'valid' ) {
				update_option( $this->id, $code );
				update_option( $this->id . '_expires', $result[ 'expires' ] );
			}

		}

	}

	/**
	 * Display social embeds.
	 */
	public function social_embeds( $html, $post ) {

		$output = new simple_html_dom();
		$output->load( $html, true, false );

		$supports = array(
			'.is-provider-twitter',
			'.is-provider-youtube',
			'.is-provider-soundcloud',
			'.is-provider-spotify',
			'.is-provider-reddit',
		);

		foreach( $supports as $el ) {
			foreach( $output->find( $el ) as $key => $element ) {
				$support = str_replace( '.is-provider-', '', $el );
				$class = 'ngl-embed-social ngl-embed-social-div ngl-embed-' . $support;
				$url   = wp_strip_all_tags( $element->innertext );
				$embed = call_user_func_array( array( $this, 'get_' . $support ), array( $url ) );
				$element->outertext = '<div class="' . $class . '">' . $embed . '</div>';
			}
		}

		$output->save();

		return ( string ) $output;
	}

	/**
	 * Get spotify.
	 */
	public function get_spotify( $url ) {

		$url = urlencode( untrailingslashit( trim( $url ) ) );

		$request  = wp_remote_get( 'https://open.spotify.com/oembed?url=' . $url ); // phpcs:ignore
		$response = wp_remote_retrieve_body( $request );

		$data = json_decode( $response );

		if ( empty( $data ) ) {
			return false;
		}

		$html = '<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td>&nbsp;</td>
						<td width="400" align="center" style="padding: 20px;"><div class="ngl-embed-social ngl-embed-spotify">';
						
		$html .= '<a href="' . urldecode( trim( $url ) ) . '" target="_blank" class="ngl-sound" style="box-shadow: 0 1px 4px #aaa;border-radius: 5px;">';
		$html .= '<img src="' . $data->thumbnail_url . '" class="embed-thumb-spotify" />';

		$html .= '<span class="ngl-sound-meta">';
		$html .= '<span class="ngl-sound-p1">';
		$html .= '<span class="ngl-sound-title" style="text-align: left;">' . esc_html( $data->title ) . '</span>';
		$html .= '</span>';
		$html .= '<span class="ngl-sound-icon"><img width="30" height="30" src="' . NGL_PLUGIN_URL . '/assets/images/social/spotify.png" /></span>';
		$html .= '</span>';

		$html .= '</a>';

		$html .= '</div></td>
				<td>&nbsp;</td>
			</tr>
		</table>';

		return $html;

	}

	/**
	 * Get reddit.
	 */
	public function get_reddit( $url ) {

		$url = urlencode( untrailingslashit( trim( $url ) ) );

		$request  = wp_remote_get( 'https://www.reddit.com/oembed?url=' . $url ); // phpcs:ignore
		$response = wp_remote_retrieve_body( $request );

		$data = json_decode( $response );

		if ( empty( $data ) ) {
			return false;
		}
		
		$content = ( string ) trim( $data->html );
		$content = preg_replace( '#<script(.*?)>(.*?)</script>#is', '', $content );

		$x = strrpos( $content, 'from' );

		$split = array( substr( $content, 0, $x ), substr( $content, $x + 4 ) );

		if ( isset( $split[1] ) ) {
			$from = trim( str_replace( 'from', '', $split[1] ) );
			$from = '<a href="https://reddit.com/r/' . wp_strip_all_tags( $from ) . '" target="_blank">/r/' . wp_strip_all_tags( $from ) . '</a>';
		}

		$html = trim( $split[0] );

		$html .= '<div class="ngl-embed-meta">
					<div class="ngl-embed-metadata">
						<strong>' . $from . '</strong><br />
						<a href="https://reddit.com/user/' . esc_attr( $data->author_name ) . '" target="_blank">' . esc_html( $data->author_name ) . '</a>
					</div>
					<div class="ngl-embed-icon">
						<a href="' . esc_url( urldecode( trim( $url ) ) ) . '" target="_blank"><img width="30" height="30" src="' . NGL_PLUGIN_URL . '/assets/images/social/reddit.png" /></a>
					</div>
				</div>';

		return $html;

	}

	/**
	 * Get soundcloud.
	 */
	public function get_soundcloud( $url ) {

		$url = urlencode( untrailingslashit( trim( $url ) ) );

		$request  = wp_remote_get( 'https://soundcloud.com/oembed?format=json&url=' . $url ); // phpcs:ignore
		$response = wp_remote_retrieve_body( $request );

		$data = json_decode( $response );

		if ( empty( $data ) ) {
			return false;
		}

		$html = '<a href="' . urldecode( trim( $url ) ) . '" target="_blank" class="ngl-sound">';
		$html .= '<img src="' . $data->thumbnail_url . '" class="embed-thumb-soundcloud" />';

		$html .= '<span class="ngl-sound-meta">';
		$html .= '<span class="ngl-sound-p1">';
		$html .= '<span class="ngl-sound-title">' . esc_html( $data->title ) . '</span>';
		$html .= '<span class="ngl-sound-author">' . esc_html( $data->author_name ) . '</span>';
		$html .= '</span>';
		$html .= '<span class="ngl-sound-icon"><img width="30" height="30" src="' . NGL_PLUGIN_URL . '/assets/images/social/soundcloud.png" /></span>';
		$html .= '</span>';

		$html .= '</a>';

		return $html;

	}

	/**
	 * Get youtube.
	 */
	public function get_youtube( $url ) {

		$url = urlencode( untrailingslashit( trim( $url ) ) );

		$request  = wp_remote_get( 'https://www.youtube.com/oembed?url=' . $url ); // phpcs:ignore
		$response = wp_remote_retrieve_body( $request );

		$data = json_decode( $response );

		if ( empty( $data ) ) {
			return false;
		}

		$image_url = str_replace( 'hqdefault', 'maxresdefault', $data->thumbnail_url );

		$url = esc_url( urldecode( trim( $url ) ) );

		$html = '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td align="left" valign="top" style="vertical-align: top;margin:0 !important;padding: 0 !important;"><a href="' . $url . '" target="_blank" style="margin:0!important;"><img src="' . $image_url . '" class="embed-thumb-youtube" style="margin:0 !important;" /></a></td></tr></table>';

		$html .= '<div class="ngl-embed-meta">
					<div class="ngl-embed-metadata">
						<a href="' . $url . '" target="_blank">' . $data->title . '</a><br />
						<span class="ngl-embed-light"><a href="' . $data->author_url . '" target="_blank" class="ngl-embed-light-link">' . $data->author_name . '</a></span>
					</div>
					<div class="ngl-embed-icon">
						<a href="' . $url . '" target="_blank"><img width="30" height="30" src="' . NGL_PLUGIN_URL . '/assets/images/social/youtube.png" /></a>
					</div>
				</div>';

		return $html;

	}

	/**
	 * Get tweet.
	 */
	public function get_twitter( $url ) {

		$url = urlencode( untrailingslashit( trim( $url ) ) );

		$request  = wp_remote_get( 'https://publish.twitter.com/oembed?omit_script=true&url=' . $url ); // phpcs:ignore
		$response = wp_remote_retrieve_body( $request );

		$data = json_decode( $response );

		if ( empty( $data->html ) ) {
			return false;
		}

		$html = preg_replace( '#<script(.*?)>(.*?)</script>#is', '', ( string ) trim( $data->html ) );
		$html = str_replace( 'blockquote', 'div', trim( $html ) );

		$stripped = preg_replace( '/<p\b[^>]*>(.*?)<\/p>/i', '', $html );
		preg_match( '#<a(.*?)</a>#i', $stripped, $match );
		$date = wp_strip_all_tags( $match[0] );
		$formatted_date = '<a href="' . urldecode( trim( $url ) ) . '" target="_blank">' . date_i18n( 'M j, Y', strtotime( $date ) ) . '</a>';

		preg_match( '%(<p[^>]*>.*?</p>)%i', $html, $regs );
		$html = $regs[0];

		if ( preg_match("/^https?:\/\/(www\.)?twitter\.com\/(#!\/)?(?<name>[^\/]+)(\/\w+)*$/", $data->author_url, $regs ) ) {
			$username = '<a href="' . $data->author_url . '" target="_blank">@' . $regs[ 'name' ] . '</a>';
		} else {
			$username = '<a href="' . $data->author_url . '" target="_blank">' . $data->author_url . '</a>';
		}

		$html .= '<div class="ngl-embed-date">' . $formatted_date . '</div>';
		$html .= '<div class="ngl-embed-meta">
					<div class="ngl-embed-metadata">
						<strong>' . $data->author_name . '</strong><br>
						' . $username . '
					</div>
					<div class="ngl-embed-icon">
						<a href="' . urldecode( trim( $url ) ) . '" target="_blank"><img width="30" height="30" src="' . NGL_PLUGIN_URL . '/assets/images/social/twitter.png" /></a>
					</div>
				</div>';

		return $html;

	}

	/**
	 * Embed CSS.
	 */
	public function embed_css() {
		?>
		.ngl-embed-social {
			font-size: 13px;
			line-height: 1.4;
			box-sizing: border-box;
		}

		div.ngl-embed-spotify {
			background: #fff;
		}

		div.ngl-embed-twitter {
			border-radius: 6px;
			padding-top: 15px;
		}

		.ngl-embed-social img {
			margin: 0 !important;
		}

		.ngl-embed-social p {
			line-height: 140%;
			font-size: 14px;
			color: #111 !important;
		}

		.ngl-embed-social a {
			color: rgb(27, 149, 224) !important;
		}

		.ngl-embed-meta {
			border-top: 1px solid rgb(204, 214, 221);
			line-height: 1.4;
			padding: 0;
			margin: 12px 0 0;
		}

		.ngl-embed-metadata {
			display: block;
			text-align: left;
		}

		.ngl-embed-metadata strong {
			font-weight: bold;
			color: #111;
		}

		.ngl-embed-icon {
			display: block;
			text-align: right !important;
		}

		.ngl-embed-icon a {
			display: inline-block !important;
		}

		.ngl-embed-icon img {
			width: 30px !important;
			height: 30px !important;
			margin: 0 !important;
			display: inline-block !important;
		}

		.ngl-embed-date {
			margin-left: 20px;
			margin-right: 20px;
		}

		.ngl-embed-date,
		.ngl-embed-date a {
			font-size: 12px;
			color: rgb(91, 112, 131) !important;
			text-decoration: none !important;
		}

		.ngl-embed-light,
		.ngl-embed-youtube .ngl-embed-light a.ngl-embed-light-link {
			color: #888 !important;
			font-size: 13px;
			font-weight: normal;
		}

		div.ngl-embed-twitter {
			background: #fff !important;
			border: 1px solid rgb(204, 214, 221);
			box-shadow: none !important;
			color: #111 !important;
		}

		div.ngl-embed-twitter p {
			margin: 0 20px 8px 20px !important;
		}

		div.ngl-embed-twitter .ngl-embed-metadata a {
			color: rgb(91, 112, 131) !important;
			text-decoration: none !important;
		}

		div.ngl-embed-youtube {
			padding: 0;
			border: 1px solid #ddd;
			border-radius: 6px;
			box-shadow: none !important;
		}

		.ngl-embed-youtube a {
			color: #ff0000 !important;
			text-decoration: none !important;
		}

		.embed-thumb-youtube {
			margin: 0 !important;
			border-radius: 5px 5px 0 0 !important;
		}

		.ngl-embed-youtube .ngl-embed-meta {
			margin: 0 !important;
			border: none;
			padding: 0;
		}

		.ngl-embed-youtube .ngl-embed-metadata {

		}

		.ngl-embed-youtube .ngl-embed-metadata a {
			color: #333 !important;
			font-weight: 600;
		}

		.ngl-embed-reddit {
			font-size: 16px;
			border: 1px solid #ccc;
			box-shadow: none !important;
			border-radius: 10px;
		}

		.ngl-embed-reddit blockquote.reddit-card {
			margin: 0 !important;
		}

		.ngl-embed-reddit a {
			text-decoration: none !important;
			color: #444 !important;
		}

		.ngl-embed-reddit .ngl-embed-meta {
			font-size: 13px;
		}

		.ngl-embed-reddit .ngl-embed-meta a {
			color: #aaa !important;
		}

		.ngl-embed-reddit .ngl-embed-meta strong a {
			color: #444 !important;
		}

		.ngl-embed-soundcloud,
		.ngl-embed-spotify {

		}

		.ngl-embed-soundcloud a.ngl-sound,
		.ngl-embed-spotify a.ngl-sound {
			text-decoration: none !important;
			font-size: 16px;
			color: #333 !important;
			font-weight: bold;
			display: block !important;
			height: 120px !important;
		}

		.ngl-sound img {
			margin: 0 !important;
			border-radius: 5px 0 0 5px;
			width: 120px !important;
			height: 120px !important;
			display: inline-block !important;
		}

		.ngl-sound span.ngl-sound-meta {
			width: 280px !important;
			height: 120px;
			display: inline-block;
			vertical-align: top;
		}

		.ngl-sound-p1 {
			display: block;
			height: 85px;
		}

		.ngl-sound-title {
			display: block;
			line-height: 1.2;
			padding: 15px 15px 4px 15px;
		}

		.ngl-sound-author {
			padding: 0 15px;
			font-size: 13px;
			font-weight: 300;
			color: #999;
		}

		.ngl-sound-icon {
			display: block;
			text-align: right !important;
			padding: 0 15px 0 0;
			height: 35px;
		}

		.ngl-sound-icon img {
			width: 30px !important;
			height: 30px !important;
		}
		<?php
	}

	/**
	 * License notice button.
	 */
	public function add_license_notice() {

		if ( get_option( 'newsletterglue_pro_license' ) ) {
			return;
		}

		?>
		<div class="ngl-review ngl-license-review">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=ngl-settings&tab=license' ) ); ?>" class="ngl-review-link"><span><?php echo esc_html__( 'Activate license key to use Newsletter Glue Pro', 'newsletter-glue' ); ?></span><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M17,8V7c0-2.757-2.243-5-5-5S7,4.243,7,7v3H6c-1.103,0-2,0.897-2,2v8c0,1.103,0.897,2,2,2h12c1.103,0,2-0.897,2-2v-8 c0-1.103-0.897-2-2-2H9V7c0-1.654,1.346-3,3-3s3,1.346,3,3v1H17z M18,12l0.002,8H6v-8H18z"></path></svg></a>
		</div>
		<?php
	}

	/**
	 * Add license activation form.
	 */
	public function add_license_activation_form() {
		if ( ! get_option( 'newsletterglue_pro_license' ) ) {
		?>
		<div class="ngl-review ngl-license-review ngl-license-insettings">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=ngl-settings&tab=license' ) ); ?>" class="ngl-review-link"><span><?php echo esc_html__( 'Activate license key to use Newsletter Glue Pro', 'newsletter-glue' ); ?></span><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M17,8V7c0-2.757-2.243-5-5-5S7,4.243,7,7v3H6c-1.103,0-2,0.897-2,2v8c0,1.103,0.897,2,2,2h12c1.103,0,2-0.897,2-2v-8 c0-1.103-0.897-2-2-2H9V7c0-1.654,1.346-3,3-3s3,1.346,3,3v1H17z M18,12l0.002,8H6v-8H18z"></path></svg></a>
		</div>
		<?php
		}
	}

	/**
	 * Add tabs.
	 */
	public function newsletterglue_settings_tabs( $tabs ) {

		$tabs = array(
			'defaults'		=> __( 'Email Defaults', 'newsletter-glue' ),
			'connect'		=> __( 'Connect', 'newsletter-glue' ),
			'theme'			=> __( 'Newsletter theme designer', 'newsletter-glue' ),
			//'blocks'		=> __( 'Newsletter blocks', 'newsletter-glue' ),
			'css' 			=> __( 'Custom CSS', 'newsletter-glue' ),
			'additional'	=> __( 'Additional', 'newsletter-glue' ),
			'license'		=> __( 'Pro license', 'newsletterg-lue' ),
		);

		return $tabs;
	}

	/**
	 * Admin notice.
	 */
	public function admin_notices() {

		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		if ( ! strstr( $screen_id, 'ngl-' ) && ! strstr( $screen_id, 'ngl_' ) && ! strstr( $screen_id, 'newsletterglue' ) ) {
			return;
		}

		// uncomment for testing only.
		// delete_option( 'newsletterglue_updated_templates_done' );

		if ( get_option( 'newsletterglue_updated_templates_done' ) ) {
			return;
		}

		$link = admin_url( 'edit.php?post_type=ngl_template&recreate-templates-patterns=all' );
		?>
		<div class="notice ngl-notice ngl-upgrade-notice" data-key="templates_notice">
			<a href="#" class="ngl-notice-dismiss"><i class="dashicons dashicons-no-alt"></i></a>
			<p><?php printf( __( 'Default templates and patterns now use v3 blocks. Please <a href="%s">update</a> now.', 'newsletter-glue' ), $link ); ?></p>
			<p style=""><strong><?php _e( 'Important:', 'newsletter-glue' ); ?></strong></p>
			<ul style="margin: 0 0 0 30px;list-style:disc;max-width:600px;">
				<li>This update does not affect templates and patterns you have created yourself.</li>
				<li>If you have altered a default template or pattern, this update will override and remove your changes. Duplicate your default template or pattern first before updating.</li>
			</ul>
			<div class="ngl-upgrade-notice-buttons">
				<a href="<?php echo esc_url( $link ); ?>" class="button button-primary"><?php _e( 'Update default templates and patterns', 'newsletter-glue' ); ?></a>
			</div>
		</div>
		<?php
	}

}

return new NGL_Pro;
