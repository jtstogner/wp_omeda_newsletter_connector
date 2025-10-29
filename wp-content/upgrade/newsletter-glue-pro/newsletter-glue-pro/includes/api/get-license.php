<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Get_License class.
 */
class NGL_REST_API_Get_License {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/get_license', array(
			'methods' 	=> 'GET',
			'callback' 	=> array( $this, 'response' ),
			'permission_callback' => array( 'NGL_REST_API', 'authenticate' ),
		) );

	}

	/**
	 * Response.
	 */
	public function response( $request ) {

		$key = get_option( 'newsletterglue_pro_license', '' );

		if ( $key ) {
			$expiry = get_option( 'newsletterglue_pro_license_expires' );
			if ( $expiry === 'lifetime' ) {
				$expires = __( 'Never expires', 'newsletter-glue' );
			} else {
				$expires = date_i18n( 'F j, Y', $expiry );
			}
		}

		$data = array(
			'licenseKey' 			=> $key,
			'licenseStatus'			=> $key ? 1 : '',
			'licenseTest' 			=> $key ? true : false,
			'licenseConnected'		=> $key ? true : false,
			'licenseRenew'			=> $key ? $expires : '',
			'licenseName'			=> $key ? sprintf( __( '%s license', 'newsletter-glue' ), ucfirst( newsletterglue_get_tier() ) ) : '',
		);

		return rest_ensure_response( $data );
	}

}

return new NGL_REST_API_Get_License();