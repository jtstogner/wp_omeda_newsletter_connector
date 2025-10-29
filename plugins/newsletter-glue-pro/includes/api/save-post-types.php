<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Save_Post_Types class.
 */
class NGL_REST_API_Save_Post_Types {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/save_post_types', array(
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

		$post_types = isset( $data[ 'post_types' ] ) ? $data['post_types'] : '';

		$saved_types = '';
		if ( ! empty( $post_types ) ) {
			foreach( $post_types as $key => $type_data ) {
				$saved_types .= $type_data[ 'value' ] . ',';
			}
			update_option( 'newsletterglue_post_types', rtrim( $saved_types, ',' ) );
		} else {
			update_option( 'newsletterglue_post_types', '' );
		}

		return rest_ensure_response( array( 'res' => rtrim( $saved_types, ',' ) ) );

	}

}

return new NGL_REST_API_Save_Post_Types();