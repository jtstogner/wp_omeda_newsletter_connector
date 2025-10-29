<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Verify_License_Key class.
 */
class NGL_REST_API_Verify_License_Key {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/verify_license_key', array(
			'methods' 	=> 'POST',
			'callback' 	=> array( $this, 'response' ),
			'permission_callback' => array( 'NGL_REST_API', 'authenticate' ),
		) );

	}

	/**
	 * Response.
	 */
	public function response( $request ) {

		$data = json_decode( $request->get_body(), true );

		if ( ! class_exists( 'NGL_License' ) ) {
			include_once NGL_PLUGIN_DIR . 'includes/libraries/license-handler.php';
		}

		$id				= 'newsletterglue_pro_license';
		$item_id		= 1266;
		$item_name		= 'Newsletter Glue Pro';
		$code			= $data[ 'license_key' ];

		if ( strstr( $code, 'ngm_' ) ) {
			$item_id = 11431;
			$item_name = 'Newsletter Glue Pro Monthly';
		}

		$ngl_license 	= new NGL_License( $id, NGL_VERSION, $item_id, $item_name, NGL_PLUGIN_FILE );
		$result			= $ngl_license->_activate( $code );

		$current_code 	= get_option( $id );
		if ( trim( $current_code ) !== $code ) {
			$ngl_license->_deactivate( $current_code );
		}

		delete_option( $id );
		delete_option( $id . '_expires' );

		if ( isset( $result[ 'status' ] ) ) {
			if ( $result[ 'status' ] === 'valid' ) {
				update_option( $id, $code );
				update_option( $id . '_expires', $result[ 'expires' ] );
			}
		}

		return rest_ensure_response( $result );
	}

}

return new NGL_REST_API_Verify_License_Key();