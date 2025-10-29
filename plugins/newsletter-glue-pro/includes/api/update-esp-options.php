<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Update_ESP_Options class.
 */
class NGL_REST_API_Update_ESP_Options {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/update_esp_options', array(
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

		$app 		= newsletterglue_default_connection();
		$options 	= get_option( 'newsletterglue_options' );

		$name 		= isset( $data[ 'name' ] ) ? sanitize_text_field( wp_unslash( $data[ 'name' ] ) ) : '';
		$email  	= isset( $data[ 'email' ] ) ? sanitize_email( wp_unslash( $data[ 'email' ] ) ) : '';
		$schedule  	= isset( $data[ 'schedule' ] ) ? sanitize_email( wp_unslash( $data[ 'schedule' ] ) ) : 'draft';

		$options[ $app ][ 'from_name' ] 	= $name;
		$options[ $app ][ 'from_email' ] 	= $email;
		$options[ 'global' ][ 'schedule' ]		= $schedule;

		if ( ! empty( $name ) ) {
			update_option( 'newsletterglue_admin_name', $name );
		}

		if ( ! empty( $data[ 'options' ] ) ) {
			foreach( $data[ 'options' ] as $key => $array ) {
				$selected = array();
				if ( is_array( $array[ 'value' ] ) && $array[ 'is_multi' ] ) {
					foreach( $array[ 'value' ] as $index => $item ) {
						$selected[] = $item[ 'value' ];
					}
					$options[ $app ][ $key ] = implode( ',', $selected );
				} else {
					if ( isset( $array[ 'value' ][ 'value' ] ) ) {
						$options[ $app ][ $key ] = $array[ 'value' ][ 'value' ];
					}
				}
				if ( isset( $array[ 'type' ] ) && $array[ 'type' ] == 'text' ) {
					$options[ $app ][ $key ] = $array[ 'value' ];
				}
			}
		}

		update_option( 'newsletterglue_options', $options );

		do_action( 'newsletterglue_update_esp_options', $options, $app );

		return rest_ensure_response( $data );
	}

}

return new NGL_REST_API_Update_ESP_Options();