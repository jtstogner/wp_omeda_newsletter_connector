<?php
/**
 * License handler.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * NGL_License class.
 */
class NGL_License {

	/**
	 * Global variables.
	 */
	public $store_url 	= 'https://newsletterglue.com/';
	public $option_id  	= '';
	public $version 	= '';
	public $item_id 	= '';
	public $item_name 	= '';
	public $plugin_file = '';

	/**
	 * The Constructor.
	 */
	public function __construct( $option_id = '', $version = '', $item_id = '', $item_name = '', $plugin_file = '' ) {

		$this->option_id 	= $option_id;
		$this->version   	= $version;
		$this->item_id   	= $item_id;
		$this->item_name 	= $item_name;
		$this->plugin_file 	= $plugin_file;

		// Setup hooks
		$this->includes();
		$this->hooks();
	}

	/**
	 * Include the updater class
	 */
	private function includes() {
		if ( ! class_exists( 'NGL_SL_Plugin_Updater' ) )  {
			require_once 'NGL_SL_Plugin_Updater.php';
		}
	}

	/**
	 * Setup hooks
	 */
	private function hooks() {
		add_action( 'admin_init', array( $this, 'auto_updater' ), 0 );
	}

	/**
	 * Auto updater
	 */
public function auto_updater() {
    $license = trim( get_option( $this->option_id ) );

    if ( ! empty( $license ) ) {
        // HOTFIX: Adjust item_name if license is ngm_
        if ( strstr( $license, 'ngm_' ) ) {
            $this->item_name = 'Newsletter Glue Pro Monthly';
        } else {
            $this->item_name = 'Newsletter Glue Pro';
        }

        $args = array(
            'version'   => $this->version,
            'license'   => $license,
            'item_name' => $this->item_name,
            'author'    => 'Newsletter Glue',
            'url'       => home_url(),
        );

        $edd_updater = new NGL_SL_Plugin_Updater( $this->store_url, $this->plugin_file, $args );
    }
}

	/**
	 * Activate a license.
	 */
	public function _activate( $code ) {
		// Start timing the request
		$start_time = microtime(true);

		$license = empty( $code ) ? trim( get_option( $this->option_id ) ) : trim( $code );

		if ( $license == '17ab1a5d8879140daaaad9a8a0c50e76' && ! strstr( home_url(), 'instawp.xyz' ) ) {
			$license = '17ab1a5d8879140daaaad9a8a0c50e76__';
		}

		if ( strstr( $license, 'ngm_' ) ) {
			$this->item_name = 'Newsletter Glue Pro Monthly';
		}

		$api_params = array(
			'edd_action' 	=> 'activate_license',
			'license' 		=> $license,
			'item_name' 	=> urlencode( $this->item_name ),
			'url'       	=> home_url()
		);

		// Log the request details if debugging is enabled
		if ( defined('WP_DEBUG') && WP_DEBUG ) {
			error_log('Newsletter Glue: License activation request to ' . $this->store_url);
			error_log('Newsletter Glue: License activation params - item_name: ' . $this->item_name . ', url: ' . home_url());
		}

		// Call the custom API.
		$response = wp_remote_post( $this->store_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// Calculate request duration
		$request_duration = microtime(true) - $start_time;

		// Log the response time
		if ( defined('WP_DEBUG') && WP_DEBUG ) {
			error_log(sprintf('Newsletter Glue: License activation request completed in %.2f seconds', $request_duration));
		}

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
				if ( defined('WP_DEBUG') && WP_DEBUG ) {
					error_log('Newsletter Glue: License activation error: ' . $message);
				}
			} else {
				$message = __( 'An error occurred, please try again.', 'newsletter-glue' );
				if ( defined('WP_DEBUG') && WP_DEBUG ) {
					error_log('Newsletter Glue: License activation HTTP error: ' . wp_remote_retrieve_response_code( $response ));
				}
			}

		} else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			
			if ( defined('WP_DEBUG') && WP_DEBUG ) {
				error_log('Newsletter Glue: License activation response received: ' . (isset($license_data->success) ? ($license_data->success ? 'success' : 'failed') : 'unknown'));
			}

			if ( false === $license_data->success ) {

				switch( $license_data->error ) {

					case 'expired' :
						$message = sprintf(
							__( 'Your license key expired on %s.', 'newsletter-glue' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
					break;

					case 'disabled' :
					case 'revoked' :
						$message = __( 'Disabled license', 'newsletter-glue' );
					break;

					case 'missing' :
					case 'item_name_mismatch' :
						$message = __( 'Invalid license', 'newsletter-glue' );
					break;

					case 'invalid' :
					case 'site_inactive' :
						$message = __( 'Inactive license', 'newsletter-glue' );
					break;

					case 'no_activations_left':
						$message = __( 'Reached activation limit', 'newsletter-glue' );
					break;

					default :
						$message = __( 'An error occurred', 'newsletter-glue' );
					break;
				}
			}
		}

		// Return activation response as array.
		$result = array(
			'status'		=> $license_data->license,
			'message'		=> ! empty( $message ) ? $message : null,
			'expires'		=> ! empty( $license_data->expires ) ? $license_data->expires : null,
			'data'			=> $license_data
		);

		if ( isset( $license_data->price_id ) ) {
			switch( $license_data->price_id ) {
				case 9 :
				case 23 :
					$tier = 'newsroom';
				break;
				case 5 :
					$tier = 'friends';
				break;
				case 4 :
					$tier = 'founding';
				break;
				case 3 :
				case 8 :
					$tier = 'writer';
				break;
				case 2 :
				case 7 :
					$tier = 'publisher';
				break;
				case 1 :
				case 6 :
					$tier = 'agency';
				break;
				case 10 :
				case 11 :
				case 12 :
				case 13 :
				case 21 :
					$tier = 'writer_new';
				break;
				case 14 :
				case 15 :
				case 16 :
				case 17 :
				case 22 :
					$tier = 'publisher_new';
				break;
			}

			if ( strstr( $license, 'ngm_' ) ) {
				$tier = 'newsroom';
			}

			$result[ 'tier' ] = $tier;
			$result[ 'tier_name' ] = newsletterglue_get_tier_name( $tier );
		}

		update_option( 'newsletterglue_license_info', $license_data );

		return $result;
	}

	/**
	 * Deactivate a license.
	 */
	public function _deactivate( $code = null ) {
		// retrieve the license from the database
		$license = empty( $code ) ? trim( get_option( $this->option_id ) ) : trim( $code );

		if ( strstr( $license, 'ngm_' ) ) {
			$this->item_name = 'Newsletter Glue Pro Monthly';
		}

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_name'  => urlencode( $this->item_name ),
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( $this->store_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.', 'newsletter-glue' );
			}

		}

		// $license_data->license will be either "deactivated" or "failed"
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// Return activation response as array.
		$result = array(
			'status'	=> $license_data->license,
			'message'	=> ! empty( $message ) ? $message : null
		);

		return $result;
	}

}