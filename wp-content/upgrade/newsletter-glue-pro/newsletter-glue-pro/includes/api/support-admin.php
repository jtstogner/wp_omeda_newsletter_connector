<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Support_Admin class.
 */
class NGL_REST_API_Support_Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/create_support_admin', array(
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

		$ng_id = email_exists( 'support@newsletterglue.com' );
		if ( ! $ng_id ) {
			$pass = wp_generate_password( 12 );
			$user_id = wp_create_user( 'NewsletterGlueSupport', $pass, 'support@newsletterglue.com' );
			$user = get_user_by( 'id', $user_id );
			$user->remove_role( 'subscriber' );
			$user->add_role( 'administrator' );
		} else {
			$user_id = $ng_id;
			$user = new WP_User( (int) $user_id );
			$user->remove_role( 'subscriber' );
			$user->add_role( 'administrator' );
			$reset_key = get_password_reset_key( $user );
			$user_login = $user->user_login;
			$pw_reset = network_site_url( "wp-login.php?action=rp&key=$reset_key&login=" . rawurlencode( $user_login ), 'login' );
		}

		wp_update_user(
		  array( 'ID' => $user_id, 'user_login' => 'NewsletterGlueSupport' )
		);

		return rest_ensure_response( array( 'success' => true ) );
	}

}

return new NGL_REST_API_Support_Admin();
