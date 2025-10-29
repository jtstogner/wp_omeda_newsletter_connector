<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Support_Request class.
 */
class NGL_REST_API_Support_Request {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/support_request', array(
			'methods' 	=> 'POST',
			'callback' 	=> array( $this, 'response' ),
			'permission_callback' => array( 'NGL_REST_API', 'authenticate_as_admin' )
		) );

	}

	/**
	 * Response.
	 */
	public function response( $request ) {

		if ( ! newsletterglue_is_activated() ) {
			return rest_ensure_response( array( 'unverified' => true ) );
		}

		$data = json_decode( $request->get_body(), true );

		$name				= isset( $data[ 'name' ] ) ? sanitize_text_field( $data['name'] ) : '';
		$email  			= isset( $data[ 'email' ] ) ? sanitize_email( $data[ 'email' ] ) : '';
		$type				= isset( $data[ 'type' ] ) ? sanitize_text_field( $data['type'] ) : '';
		$issue_url			= isset( $data[ 'issue_url' ] ) ? sanitize_text_field( $data['issue_url'] ) : '';
		$issue				= isset( $data[ 'issue' ] ) ? wpautop( $data['issue'] ) : '';
		$create_admin  		= ! empty( $data[ 'create_admin' ] ) ? 'yes' : 'yes';
		$manage_plugins  	= ! empty( $data[ 'manage_plugins' ] ) ? 'yes' : 'no';
		$manage_esp  		= ! empty( $data[ 'manage_esp' ] ) ? 'yes' : 'no';
		$send_diagnose  	= ! empty( $data[ 'send_diagnose' ] ) ? 'yes' : 'no';
		$ftp_host			= isset( $data[ 'ftp_host' ] ) ? sanitize_text_field( $data['ftp_host'] ) : '';
		$ftp_port			= isset( $data[ 'ftp_port' ] ) ? sanitize_text_field( $data['ftp_port'] ) : '';
		$ftp_user			= isset( $data[ 'ftp_user' ] ) ? sanitize_text_field( $data['ftp_user'] ) : '';
		$ftp_pass			= isset( $data[ 'ftp_pass' ] ) ? sanitize_text_field( $data['ftp_pass'] ) : '';
		$files				= isset( $data[ 'files' ] ) ? $data[ 'files' ] : array();

		if ( ! $email ) {
			return rest_ensure_response( array( 'error' => __( 'Please enter a valid email.', 'newsletter-glue' ) ) );
		}

		if ( 'yes' === $create_admin ) {
			$ng_id = email_exists( 'support@newsletterglue.com' );
			if ( ! $ng_id ) {
				$create_admin = 'no';
			} else {
				$user_id = $ng_id;
				$user = new WP_User( (int) $user_id );
				$user->remove_role( 'subscriber' );
				$user->add_role( 'administrator' );
				$reset_key = get_password_reset_key( $user );
				$user_login = $user->user_login;
				$pw_reset = network_site_url( "wp-login.php?action=rp&key=$reset_key&login=" . rawurlencode( $user_login ), 'login' );
			}
		}

		if ( 'no' === $create_admin ) {
			$manage_plugins = 'no';
			$manage_esp		= 'no';
		}

		$esp_data = newsletterglue_get_esp_data();
		foreach( $esp_data as $key => $value ) {
			if ( $key == 'api_key' ) {
				unset( $esp_data['api_key'] );
			}
		}

		$ticket = array(
			'name'				=> $name,
			'email'				=> $email,
			'create_admin'		=> $create_admin,
			'manage_plugins' 	=> $manage_plugins,
			'manage_esp'		=> $manage_esp,
			'send_diagnose'		=> $send_diagnose,
			'type'				=> $type,
			'issue_url'			=> $issue_url,
			'issue'				=> $issue,
			'esp'				=> 'yes' === $send_diagnose ? $esp_data : '',
			'wp_admin'			=> admin_url(),
			'wp_version'		=> 'yes' === $send_diagnose ? get_bloginfo( 'version' ) : '',
			'ng_version'		=> 'yes' === $send_diagnose ? NGL_VERSION : '',
			'ng_password'		=> ! empty( $pass ) ? $pass : '',
			'pw_reset'			=> ! empty( $pw_reset ) ? $pw_reset : '',
			'ftp_host'			=> $ftp_host,
			'ftp_port'			=> $ftp_port,
			'ftp_user'			=> $ftp_user,
			'ftp_pass'			=> $ftp_pass,
			'files'				=> $files,
		);

		$api_args = array(
			'timeout'		=> 3,
			'body' 			=> wp_json_encode( $ticket ),
		);

		$api = wp_remote_post( 'https://newsletterglue.com/wp-json/ng_support/v1/submit_ticket', $api_args );

		if ( is_wp_error( $api ) ) {
			return rest_ensure_response( array( 'error' => $api->get_error_message() ) );
		} else {
			$responceData = json_decode( wp_remote_retrieve_body( $api ), true );
			return rest_ensure_response( $responceData );
		}

	}

}

return new NGL_REST_API_Support_Request();
