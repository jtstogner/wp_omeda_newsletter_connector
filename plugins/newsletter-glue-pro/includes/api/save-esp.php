<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * NGL_REST_API_Save_ESP class.
 */
class NGL_REST_API_Save_ESP {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route(
			'newsletterglue/' . newsletterglue()->api_version(),
			'/save_esp',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'response' ),
				'permission_callback' => array( 'NGL_REST_API', 'authenticate' ),
			)
		);
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

		$options = get_option( 'newsletterglue_options' );

		$app          = isset( $data['esp'] ) ? sanitize_text_field( wp_unslash( $data['esp'] ) ) : '';
		$name         = isset( $data['from_name'] ) ? sanitize_text_field( wp_unslash( $data['from_name'] ) ) : '';
		$email        = isset( $data['from_email'] ) ? sanitize_text_field( wp_unslash( $data['from_email'] ) ) : '';
		$schedule     = isset( $data['schedule'] ) ? sanitize_text_field( wp_unslash( $data['schedule'] ) ) : '';
		$utm_source   = isset( $data['utm_source'] ) ? sanitize_text_field( wp_unslash( $data['utm_source'] ) ) : '';
		$utm_campaign = isset( $data['utm_campaign'] ) ? sanitize_text_field( wp_unslash( $data['utm_campaign'] ) ) : '';
		$utm_medium   = isset( $data['utm_medium'] ) ? sanitize_text_field( wp_unslash( $data['utm_medium'] ) ) : '';
		$utm_content  = isset( $data['utm_content'] ) ? sanitize_text_field( wp_unslash( $data['utm_content'] ) ) : '';

		include_once newsletterglue_get_path( $app ) . '/init.php';

		$classname = 'NGL_' . ucfirst( $app );
		$api       = new $classname();
		$result    = $api->verify_email( $email );

		if ( isset( $result['failed'] ) ) {
			$response['is_invalid_email'] = true;
			$email                        = null;
		}

		if ( ! empty( $email ) ) {
			$options[ $app ]['from_email'] = $email;
		}

		if ( ! empty( $name ) ) {
			$options[ $app ]['from_name'] = $name;
			update_option( 'newsletterglue_admin_name', $name );
		}

		if ( ! empty( $schedule ) ) {
			$options['global']['schedule'] = $schedule;
		}

		$options['global']['utm_source']   = $utm_source;
		$options['global']['utm_campaign'] = $utm_campaign;
		$options['global']['utm_medium']   = $utm_medium;
		$options['global']['utm_content']  = $utm_content;

		if ( empty( $name ) ) {
			$response['is_invalid_name'] = true;
		}

		if ( ! empty( $data['options'] ) ) {
			foreach ( $data['options'] as $key => $array ) {
				$selected = array();
				if ( is_array( $array['value'] ) && $array['is_multi'] ) {
					foreach ( $array['value'] as $index => $item ) {
						$selected[] = $item['value'];
					}
					$options[ $app ][ $key ] = implode( ',', $selected );
				} elseif ( isset( $array['value']['value'] ) ) {
						$options[ $app ][ $key ] = $array['value']['value'];
				}
				if ( isset( $array['type'] ) && $array['type'] == 'text' ) {
					$options[ $app ][ $key ] = $array['value'];
				}
			}
		}

		update_option( 'newsletterglue_options', $options );

		do_action( 'newsletterglue_update_esp_options', $options, $app );

		return rest_ensure_response( $response );
	}
}

return new NGL_REST_API_Save_ESP();
