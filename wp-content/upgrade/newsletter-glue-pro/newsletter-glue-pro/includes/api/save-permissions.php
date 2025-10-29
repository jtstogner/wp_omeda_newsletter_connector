<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Save_Permissions class.
 */
class NGL_REST_API_Save_Permissions {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/save_permissions', array(
			'methods' 	=> 'POST',
			'callback' 	=> array( $this, 'response' ),
			'permission_callback' => array( 'NGL_REST_API', 'authenticate' )
		) );

	}

	/**
	 * Response.
	 */
	public function response( $request ) {

		if ( ! newsletterglue_is_activated() ) {
			return rest_ensure_response( array( 'unverified' => true ) );
		}

		$response = array();

		$data = json_decode( $request->get_body(), true );

		$user_perms		= isset( $data[ 'user_perms' ] ) ? ngl_sanitize_text_field( $data['user_perms'] ) : '';
		$user_role	 	= isset( $data[ 'role' ] ) ? sanitize_text_field( wp_unslash( $data['role'] ) ) : '';

		if ( empty( $user_role ) || empty( $user_perms ) || ( $user_role == 'administrator' ) ) {
			return rest_ensure_response( array( 'unverified' => true ) );
		}

		$perms = $user_perms[ $user_role ];

		$caps = array( 'manage_newsletterglue', 'publish_newsletterglue', 'add_newsletterglue', 'edit_newsletterglue', 'manage_newsletterglue_patterns' );

		$wp_role = get_role( $user_role );

		foreach( $caps as $cap ) {
			if ( isset( $perms[ $cap ] ) && absint( $perms[ $cap ] ) === 1 ) {
				$wp_role->add_cap( $cap );
			} else {
				$wp_role->remove_cap( $cap );
			}
		}

		return rest_ensure_response( $user_perms[ $user_role ] );

	}

}

return new NGL_REST_API_Save_Permissions();