<?php
/**
 * ConvertKit API class
 *
 * @package ConvertKit
 * @author ConvertKit
 */

use oldmine\RelativeToAbsoluteUrl\RelativeToAbsoluteUrl;
use KubAT\PhpSimple\HtmlDomParser;

/**
 * ConvertKit_API Class
 * Establishes API connection to ConvertKit App
 */
class NG_ConvertKit_API {

	/**
	 * ConvertKit API Key
	 *
	 * @var string
	 */
	protected $api_key;

	/**
	 * ConvertKit API Secret
	 *
	 * @var string
	 */
	protected $api_secret;

	/**
	 * Save debug data to log
	 *
	 * @var  string
	 */
	protected $debug;

	/**
	 * Version of ConvertKit API
	 *
	 * @var string
	 */
	protected $api_version = 'v3';

	/**
	 * ConvertKit API URL
	 *
	 * @var string
	 */
	protected $api_url_base = 'https://api.convertkit.com/';

	/**
	 * API resources
	 *
	 * @var array
	 */
	protected $resources = array();

	/**
	 * Additional markup
	 *
	 * @var array
	 */
	protected $markup = array();

	/**
	 * Constructor for ConvertKitAPI instance
	 *
	 * @param string $api_key ConvertKit API Key.
	 * @param string $api_secret ConvertKit API Secret.
	 * @param string $debug Save data to log.
	 */
	public function __construct( $api_key, $api_secret, $debug = '' ) {
		$this->api_key = $api_key;
		$this->api_secret = $api_secret;
		$this->debug = $debug;
	}

	/**
	 * Make a request to the ConvertKit API
	 *
	 * @param string $request Request string.
	 * @param string $method HTTP Method.
	 * @param array  $args Request arguments.
	 * @return object Response object
	 */
	private function make_request( $request, $method, $args = array() ) {

		$url = $this->api_url_base . $request;

		if ( $method === 'GET' ) {
			foreach( $args as $key => $value ) {
				$url = add_query_arg( $key, $value, $url );
			}
			$settings = array(
				'method'  => $method,
			);
		}

		if ( $method == 'POST' ) {
			$headers = array(
				'Content-Type' => 'application/json; charset=utf-8',
			);

			$settings = array(
				'headers' => $headers,
				'method'  => $method,
				'body'    => wp_json_encode( $args ),
			);
		}

		$result = wp_remote_request( $url, $settings );

		return json_decode( wp_remote_retrieve_body( $result ), true );
	}

	/**
	 * Get account.
	 */
	public function get_account() {
		$request = $this->api_version . sprintf( '/account' );

		$args = array(
			'api_secret' => $this->api_secret,
		);

		return $this->make_request( $request, 'GET', $args );
	}

	/**
	 * Get forms.
	 */
	public function get_forms() {
		$request = $this->api_version . sprintf( '/forms' );

		$args = array(
			'api_key' => $this->api_key,
		);

		return $this->make_request( $request, 'GET', $args );
	}

	/**
	 * Add a subscriber.
	 */
	public function add_subscriber( $form_id, $args = array() ) {
		$request = $this->api_version . sprintf( '/forms/%s/subscribe', $form_id );

		$args = array(
			'api_key' 		=> $this->api_key,
			'email'	  		=> $args[ 'email' ],
			'first_name' 	=> $args[ 'first_name' ],
		);

		return $this->make_request( $request, 'POST', $args );
	}

	/**
	 * Add a subscriber.
	 */
	public function create_broadcast( $args = array() ) {

		$request = $this->api_version . '/broadcasts';

		$result = $this->make_request( $request, 'POST', $args );

		return $result;
	}

}