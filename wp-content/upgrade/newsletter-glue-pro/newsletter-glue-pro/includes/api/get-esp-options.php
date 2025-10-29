<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Get_ESP_Options class.
 */
class NGL_REST_API_Get_ESP_Options {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/get_esp_options', array(
			'methods' 	=> 'GET',
			'callback' 	=> array( $this, 'response' ),
			'permission_callback' => array( 'NGL_REST_API', 'authenticate' ),
		) );

	}

	/**
	 * Response.
	 */
	public function response( $request ) {

		$esp = newsletterglue_default_connection();

		if ( ! $esp ) {
			return rest_ensure_response( array( 'newsletterglue_no_esp' => __( 'No ESP connected.', 'newsletter-glue' ) ) );
		}

		// Load ESP and connect to it.
		include_once newsletterglue_get_path( $esp ) . '/init.php';
		$classname 	= 'NGL_' . ucfirst( $esp );
		$class		= new $classname();
		$api		= $class->connect();

		// Get current ESP settings.
		$name  		= newsletterglue_get_option( 'from_name', $esp );
		$email 		= newsletterglue_get_option( 'from_email', $esp );

		$data = array(
			'esp' 			=> $esp,
			'name'			=> $name,
			'email'			=> $email,
			'email_help' 	=> $class->get_email_verify_help(),
			'account'		=> get_option( 'newsletterglue_' . $esp ),
			'is_validated'	=> true,
		);

		// Get options array per ESP.
		$defaults = array();
		foreach( $class->option_array() as $key => $info ) {

			if ( $info[ 'type' ] == 'text' ) {
				$data[ 'options' ][ $key ] = $this->get_text_input_data( $key, $info, $esp );
				continue;
			}

			if ( isset( $info[ 'param' ] ) ) {
				$dft = isset( $defaults[ $info[ 'param' ] ] ) ? $defaults[ $info[ 'param' ] ] : newsletterglue_get_option( $info[ 'param' ], $esp );
				$items = call_user_func_array( array( $class, $info[ 'callback' ] ), array( $dft ) );
			} else {
				$items = call_user_func( array( $class, $info[ 'callback' ] ) );
			}

			$value = newsletterglue_get_option( $key, $esp );

			$default = array_keys( $items );
			$default = $default[0];

			$defaults[ $key ] = $value ? $value : $default;

			$values = array();
			if ( isset( $info[ 'is_multi' ] ) ) {
				if ( ! empty( $value ) ) {
					$value = explode( ',', $value );
					foreach( $value as $new_key => $new_value ) {
						$values[] = array(
							'value' => $new_value,
							'label'	=> $items[ $new_value ],
						);
					}
				} else {
					$values = null;
				}
			} else {
				if ( $value && isset( $items[ $value ] ) ) {
					$values = array(
						'value' => $value,
						'label'	=> $items[ $value ],
					);
				} else {
					$values = null;
				}
			}

			$default_value = array();

			if ( ! empty( $default ) ) {
				$default_value = array(
					'value' => $default,
					'label'	=> $items[ $default ],
				);
			}

			$_items = array();
			foreach( $items as $i => $c ) {
				$final_item_arr = array(
					'value' => $i,
					'label' => $c,
				);
				if ( strstr( $i, 'optgroup' ) ) {
					$final_item_arr[ 'disabled' ] = true;
				}
				$_items[] = $final_item_arr;
			}

			$show_default_value = ! isset( $info[ 'is_multi' ] ) ? $default_value : '';

			$data[ 'options' ][ $key ] = array(
				'type'	   => $info[ 'type' ],
				'title'	   => $info[ 'title' ],
				'help'     => $info[ 'help' ],
				'is_multi' => isset( $info[ 'is_multi' ] ),
				'items'	   => $_items,
				'default'  => $default_value,
				'value'    => $values ? $values : $show_default_value,
				'onchange' => isset( $info[ 'onchange' ] ) ? $info[ 'onchange' ] : null,
				'callback' => isset( $info[ 'callback' ] ) ? $info[ 'callback' ] : null,
				'placeholder' => isset( $info[ 'placeholder' ] ) ? $info[ 'placeholder' ] : __( 'Select...', 'newsletter-glue' ),
			);

		}

		return rest_ensure_response( $data );
	}

	/**
	 * Get array for simple input text.
	 */
	public function get_text_input_data( $key, $info, $esp ) {

		$data = array(
			'type'	   => $info[ 'type' ],
			'title'	   => $info[ 'title' ],
			'help'     => $info[ 'help' ],
			'default'  => isset( $info[ 'default' ] ) ? $info[ 'default' ] : '',
			'value'    => newsletterglue_get_option( $key, $esp ),
		);

		return $data;

	}

}

return new NGL_REST_API_Get_ESP_Options();