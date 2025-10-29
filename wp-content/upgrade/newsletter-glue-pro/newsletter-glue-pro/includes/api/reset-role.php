<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Reset_Role class.
 */
class NGL_REST_API_Reset_Role {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/reset_role', array(
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

		$role = isset( $data[ 'role' ] ) ? sanitize_text_field( wp_unslash( $data['role'] ) ) : '';

		if ( empty( $role ) ) {
			newsletterglue_assign_caps();
		} else {
			newsletterglue_assign_caps_for_role( $role );
		}

		$roles = newsletterglue_get_editable_roles();

		$permissions = newsletterglue_get_permissions_array( $roles, true );

		return rest_ensure_response( array( 'permissions' => $permissions ) );

	}

}

return new NGL_REST_API_Reset_Role();