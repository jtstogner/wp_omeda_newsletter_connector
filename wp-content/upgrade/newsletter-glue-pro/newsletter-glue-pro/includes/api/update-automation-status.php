<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Update_Automation_Status class.
 */
class NGL_REST_API_Update_Automation_Status {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/update_automation_status', array(
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

		$post_id = isset( $data[ 'id' ] ) ? absint( $data['id'] ) : '';
		$status  = isset( $data[ 'status' ] ) ? sanitize_text_field( $data[ 'status' ] ) : 0;

		if ( ! $post_id || ! $status ) {
			return;
		}

		$automation = new NGL_Automation( $post_id );

		if ( $status === 'off' ) {
			$automation->disable();
		} else {
			$automation->enable();
		}

		return rest_ensure_response( array( 'id' => $post_id, 'status' => $status ) );

	}

}

return new NGL_REST_API_Update_Automation_Status();