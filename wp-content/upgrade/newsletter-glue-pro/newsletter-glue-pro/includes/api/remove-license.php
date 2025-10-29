<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Remove_License class.
 */
class NGL_REST_API_Remove_License {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/remove_license', array(
			'methods' 	=> 'GET',
			'callback' 	=> array( $this, 'response' ),
			'permission_callback' => array( 'NGL_REST_API', 'authenticate' ),
		) );

	}

	/**
	 * Response.
	 */
	public function response( $request ) {

		$data = array();

		if ( ! class_exists( 'NGL_License' ) ) {
			include_once NGL_PLUGIN_DIR . 'includes/libraries/license-handler.php';
		}

		$id				= 'newsletterglue_pro_license';
		$item_id		= 1266;
		$item_name		= 'Newsletter Glue Pro';
		$ngl_license 	= new NGL_License( $id, NGL_VERSION, $item_id, $item_name, NGL_PLUGIN_FILE );
		$result			= $ngl_license->_deactivate();

		delete_option( $id );
		delete_option( $id . '_expires' );
		delete_option( 'newsletterglue_license_info' );

		return rest_ensure_response( $result );

	}

}

return new NGL_REST_API_Remove_License();