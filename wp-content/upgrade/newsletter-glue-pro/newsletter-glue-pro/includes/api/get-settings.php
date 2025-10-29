<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * NGL_REST_API_Get_Settings class.
 */
class NGL_REST_API_Get_Settings {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route(
			'newsletterglue/' . newsletterglue()->api_version(),
			'/get_settings',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'response' ),
				'permission_callback' => array( 'NGL_REST_API', 'authenticate' ),
			)
		);
	}

	/**
	 * Get ESP list.
	 */
	public function get_esp_list() {

		$esps = newsletterglue_get_esp_list();

		$esp_list = array();

		foreach ( $esps as $key => $esp_data ) {

			if ( empty( $esp_data['requires'] ) ) {
				$upgrade = false;
			} else {
				$upgrade = $esp_data['requires'];
			}

			$icon_url = newsletterglue_get_url( $esp_data['value'] ) . '/assets/icon.png';

			$info = array(
				'value'        => $esp_data['value'],
				'label'        => $esp_data['label'],
				'upgrade'      => $upgrade,
				'icon'         => $icon_url,
				'url_field'    => false,
				'secret_field' => false,
				'url_help'     => false,
			);

			if ( ! empty( $esp_data['help'] ) ) {
				$info['api_src'] = $esp_data['help'];
			} else {
				$info['api_src'] = 'none';
			}

			if ( isset( $esp_data['extra_setting'] ) ) {
				if ( $esp_data['extra_setting'] == 'url' ) {
					$info['url_field'] = true;
				}
				if ( $esp_data['extra_setting'] == 'secret' ) {
					$info['secret_field'] = true;
				}
				if ( $esp_data['extra_setting'] == 'both' ) {
					$info['url_field']    = true;
					$info['secret_field'] = true;
				}
			}

			if ( isset( $esp_data['url_help'] ) ) {
				$info['url_help'] = $esp_data['url_help'];
			}

			if ( isset( $esp_data['key_name'] ) ) {
				$info['key_name'] = $esp_data['key_name'];
			}

			if ( isset( $esp_data['secret_name'] ) ) {
				$info['secret_name'] = $esp_data['secret_name'];
			}

			if ( isset( $esp_data['url_name'] ) ) {
				$info['url_name'] = $esp_data['url_name'];
			}

			if ( $esp_data['value'] == 'plugin' ) {
				$info['isDisabled'] = true;
			}

			$esp_list[] = $info;
		}

		return $esp_list;
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
	 * Get current esp options.
	 */
	public function get_esp_options( $esp ) {

		$file = newsletterglue_get_path( $esp ) . '/init.php';

		if ( ! file_exists( $file ) ) {
			delete_option( 'newsletterglue_integrations' );
			$app = null;
		}

		include_once newsletterglue_get_path( $esp ) . '/init.php';

		$classname = 'NGL_' . ucfirst( $esp );
		$class     = new $classname();
		$api       = $class->connect();

		$esp_options = array();

		// Get options array per ESP.
		if ( $esp ) {
			$defaults = array();
			foreach ( $class->option_array() as $key => $info ) {

				if ( $info['type'] == 'text' ) {
					$esp_options[ $key ] = $this->get_text_input_data( $key, $info, $esp );
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
					$final_item_arr = array(
						'value' => $i,
						'label' => $c,
					);
					if ( strstr( $i, 'optgroup' ) ) {
						$final_item_arr['disabled'] = true;
					}
					$_items[] = $final_item_arr;
				}

				$show_default_value = ! isset( $info['is_multi'] ) ? $default_value : '';

				$esp_options[ $key ] = array(
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

		return $esp_options;
	}

	/**
	 * Response.
	 */
	public function response( $request ) {

		$data = array();

		$data['esps'] = $this->get_esp_list();

		$esp = newsletterglue_default_connection();

		if ( ! empty( $esp ) ) {

			$integrations = get_option( 'newsletterglue_integrations' );
			$integration  = $integrations[ $esp ];

			$data['hasConnected']     = $esp;
			$data['newEsp']           = $esp;
			$data['connectionName']   = isset( $integration['connection_name'] ) ? $integration['connection_name'] : newsletterglue_get_name( $esp );
			$data['connectionIcon']   = newsletterglue_get_url( $esp ) . '/assets/icon.png';
			$data['connectionState']  = __( 'Connected', 'newsletter-glue' );
			$data['connectionStatus'] = 1;

			foreach ( $data['esps'] as $key => $array ) {
				if ( $array['value'] === $esp ) {
					$data['selectedEsp']     = $array;
					$data['getAPIURL']       = $array['api_src'];
					$data['showURLfield']    = ! empty( $array['url_field'] ) ? $array['url_field'] : '';
					$data['showSecretfield'] = ! empty( $array['secret_field'] ) ? $array['secret_field'] : '';
				}
			}

			$data['espAPIKey']    = $integration['api_key'];
			$data['espAPIURL']    = isset( $integration['api_url'] ) ? $integration['api_url'] : '';
			$data['espAPISecret'] = isset( $integration['api_secret'] ) ? $integration['api_secret'] : '';

			$data['fromName']  = newsletterglue_get_option( 'from_name', $esp );
			$data['fromEmail'] = newsletterglue_get_option( 'from_email', $esp );

			$data['allowESPinputs'] = true;

			// Get options.
			$data['options'] = $this->get_esp_options( $esp );

		} else {

			$data['hasConnected'] = false;
		}

		$data['schedule']     = newsletterglue_get_option( 'schedule', 'global' );
		$data['utm_source']   = newsletterglue_get_option( 'utm_source', 'global' );
		$data['utm_campaign'] = newsletterglue_get_option( 'utm_campaign', 'global' );
		$data['utm_medium']   = newsletterglue_get_option( 'utm_medium', 'global' );
		$data['utm_content']  = newsletterglue_get_option( 'utm_content', 'global' );

		$data = apply_filters( 'newsletterglue_get_settings', $data );

		return rest_ensure_response( $data );
	}
}

return new NGL_REST_API_Get_Settings();
