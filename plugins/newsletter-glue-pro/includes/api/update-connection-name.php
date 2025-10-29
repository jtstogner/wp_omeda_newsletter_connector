<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Update_Connection_Name class.
 */
class NGL_REST_API_Update_Connection_Name {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/update_connection_name', array(
			'methods' 	=> 'POST',
			'callback' 	=> array( $this, 'response' ),
			'permission_callback' => array( 'NGL_REST_API', 'authenticate' )
		) );

	}

	/**
	 * Response.
	 */
	public function response( $request ) {

		$data = json_decode( $request->get_body(), true );

		$esp 	= isset( $data[ 'esp' ] ) ? sanitize_text_field( wp_unslash( $data[ 'esp' ] ) ) : '';
		$name 	= isset( $data[ 'name' ] ) ? wp_kses_post( wp_unslash( $data[ 'name' ] ) ) : '';

		$integrations = get_option( 'newsletterglue_integrations' );

		if ( isset( $integrations[ $esp ] ) ) {
			$integrations[ $esp ][ 'connection_name' ] = $name;
			update_option( 'newsletterglue_integrations', $integrations );
		}

		return rest_ensure_response( array( 'name' => $name ) );
	}

}

return new NGL_REST_API_Update_Connection_Name();