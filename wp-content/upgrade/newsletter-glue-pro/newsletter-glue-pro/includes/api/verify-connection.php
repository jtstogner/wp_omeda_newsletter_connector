<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * NGL_REST_API_Verify_Connection class.
 */
class NGL_REST_API_Verify_Connection {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route(
			'newsletterglue/' . newsletterglue()->api_version(),
			'/verify_connection',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'response' ),
				'permission_callback' => array( 'NGL_REST_API', 'authenticate' ),
			)
		);
	}

	/**
	 * Get array for simple input text.
	 */
	public function get_text_input_data( $key, $info, $esp ) {

		$data = array(
			'type'    => $info['type'],
			'title'   => $info['title'],
			'help'    => $info['help'],
			'default' => isset( $info['default'] ) ? $info['default'] : '',
			'value'   => newsletterglue_get_option( $key, $esp ),
		);

		return $data;
	}

	/**
	 * Response.
	 */
	public function response( $request ) {

		$data = json_decode( $request->get_body(), true );

		$esp        = isset( $data['newEsp'] ) ? sanitize_text_field( wp_unslash( $data['newEsp'] ) ) : '';
		$api_key    = isset( $data['espAPIKey'] ) ? sanitize_text_field( wp_unslash( $data['espAPIKey'] ) ) : '';
		$api_secret = isset( $data['espAPISecret'] ) ? sanitize_text_field( wp_unslash( $data['espAPISecret'] ) ) : '';
		$api_url    = isset( $data['espAPIURL'] ) ? sanitize_text_field( wp_unslash( $data['espAPIURL'] ) ) : '';

		include_once newsletterglue_get_path( $esp ) . '/init.php';

		$args = array(
			'api_key'    => $api_key,
			'api_url'    => $api_url,
			'api_secret' => $api_secret,
		);

		$classname = 'NGL_' . ucfirst( $esp );
		$class     = new $classname();
		$result    = $class->add_integration( $args );

		// When connection is successful.
		if ( $result['response'] === 'successful' ) {
			$result['app'] = $esp;

			$integrations = get_option( 'newsletterglue_integrations', array() );
			$integration  = isset( $integrations[ $esp ] ) ? $integrations[ $esp ] : array();

			$result = array_merge( $result, get_option( 'newsletterglue_' . $esp, array() ) );
			$result = array_merge( $result, $integration );

			$result['connection_state'] = __( 'Connected', 'newsletter-glue' );
			$result['connection_icon']  = newsletterglue_get_url( $esp ) . '/assets/icon.png';

			$result['from_name']  = newsletterglue_get_option( 'from_name', $esp );
			$result['from_email'] = newsletterglue_get_option( 'from_email', $esp );

			$result['schedule']     = newsletterglue_get_option( 'schedule', 'global' ) ? newsletterglue_get_option( 'schedule', 'global' ) : 'draft';
			$result['utm_source']   = newsletterglue_get_option( 'utm_source', 'global' );
			$result['utm_campaign'] = newsletterglue_get_option( 'utm_campaign', 'global' );
			$result['utm_medium']   = newsletterglue_get_option( 'utm_medium', 'global' );
			$result['utm_content']  = newsletterglue_get_option( 'utm_content', 'global' );

			// Get ESP options.
			$api = $class->connect();

			// Get options array per ESP.
			$defaults = array();
			foreach ( $class->option_array() as $key => $info ) {

				if ( $info['type'] == 'text' ) {
					$result['options'][ $key ] = $this->get_text_input_data( $key, $info, $esp );
					continue;
				}

				if ( isset( $info['param'] ) ) {
					$dft   = isset( $defaults[ $info['param'] ] ) ? $defaults[ $info['param'] ] : newsletterglue_get_option( $info['param'], $esp );
					$items = call_user_func_array( array( $class, $info['callback'] ), array( $dft ) );
				} else {
					$items = call_user_func( array( $class, $info['callback'] ) );
				}

				$value = newsletterglue_get_option( $key, $esp );

				$default = array_keys( $items );
				$default = isset( $default[0] ) ? $default[0] : '';

				$defaults[ $key ] = $value ? $value : $default;

				$values = array();
				if ( isset( $info['is_multi'] ) ) {
					if ( ! empty( $value ) ) {
						$value = explode( ',', $value );
						foreach ( $value as $new_key => $new_value ) {
							$values[] = array(
								'value' => $new_value,
								'label' => $items[ $new_value ],
							);
						}
					} else {
						$values = null;
					}
				} elseif ( $value && isset( $items[ $value ] ) ) {
						$values = array(
							'value' => $value,
							'label' => $items[ $value ],
						);
				} else {
						$values = null;
				}

				$default_value = array();

				if ( ! empty( $default ) ) {
					$default_value = array(
						'value' => $default,
						'label' => $items[ $default ],
					);
				}

				$_items = array();
				foreach ( $items as $i => $c ) {
					$_items[] = array(
						'value' => $i,
						'label' => $c,
					);
				}

				$show_default_value = ! isset( $info['is_multi'] ) ? $default_value : '';

				$result['options'][ $key ] = array(
					'type'        => $info['type'],
					'title'       => $info['title'],
					'help'        => $info['help'],
					'is_multi'    => isset( $info['is_multi'] ),
					'items'       => $_items,
					'default'     => $default_value,
					'value'       => $values ? $values : $show_default_value,
					'onchange'    => isset( $info['onchange'] ) ? $info['onchange'] : null,
					'callback'    => isset( $info['callback'] ) ? $info['callback'] : null,
					'placeholder' => isset( $info['placeholder'] ) ? $info['placeholder'] : __( 'Select...', 'newsletter-glue' ),
				);

			}
		}

		return rest_ensure_response( $result );
	}
}

return new NGL_REST_API_Verify_Connection();
