<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Verify_License class.
 */
class NGL_REST_API_Verify_License {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/verify_license', array(
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

		$code = isset( $data[ 'licenseKey' ] ) ? sanitize_text_field( wp_unslash( $data[ 'licenseKey' ] ) ) : '';

		if ( ! class_exists( 'NGL_License' ) ) {
			include_once NGL_PLUGIN_DIR . 'includes/libraries/license-handler.php';
		}

		$id				= 'newsletterglue_pro_license';
		$item_id		= 1266;
		$item_name		= 'Newsletter Glue Pro';

		if ( strstr( $code, 'ngm_' ) ) {
			$item_id = 11431;
			$item_name = 'Newsletter Glue Pro Monthly';
		}

		$ngl_license 	= new NGL_License( $id, NGL_VERSION, $item_id, $item_name, NGL_PLUGIN_FILE );
		$result			= $ngl_license->_activate( $code );

		$result[ 'licenseName' ] = '';
		$result[ 'licenseTier' ] = '';

		$current_code 	= get_option( $id );
		if ( trim( $current_code ) !== $code ) {
			$ngl_license->_deactivate( $current_code );
		}

		delete_option( $id );
		delete_option( $id . '_expires' );

		if ( isset( $result[ 'status' ] ) ) {
			if ( $result[ 'status' ] === 'valid' ) {
				$result[ 'licenseName' ] = sprintf( __( '%s license', 'newsletter-glue' ), $result[ 'tier_name' ] );
				$result[ 'licenseTier' ] = $result[ 'tier' ];
				update_option( $id, $code );
				update_option( $id . '_expires', $result[ 'expires' ] );
			}
		}

		if ( isset( $result[ 'expires' ] ) ) {
			if ( $result[ 'expires' ] === 'lifetime' ) {
				$result[ 'licenseRenew' ] = __( 'Never expires', 'newsletter-glue' );
			} else {
				$result[ 'licenseRenew' ] = wp_date( 'F j, Y', strtotime( $result[ 'expires' ] ) );
			}
		}

		return rest_ensure_response( $result );

	}

}

return new NGL_REST_API_Verify_License();