<?php

class NGL_Mailerlite_V2_API
{

	const API_BASE_URL 			= 'https://connect.mailerlite.com/api';
	const HTTP_METHOD_GET 		= 'GET';
	const HTTP_METHOD_POST 		= 'POST';
	const HTTP_METHOD_PUT 		= 'PUT';
	const HTTP_METHOD_DELETE 	= 'DELETE';

	private $apiKey;
	private $lastResponseCode;

	/**
	 * constructor.
	 */
	public function __construct($api_key)
	{
		$this->apiKey = $api_key;
	}

	/**
	 * @param $endpoint
	 * @param array $parameters
	 * @return mixed
	 */
	public function get($endpoint, $parameters = [])
	{
		if ($parameters) {
			foreach ($parameters as $key => $parameter) {
				if (is_bool($parameter)) {
					// http_build_query converts bool to int
					$parameters[$key] = $parameter ? 'true' : 'false';
				}
			}
			$endpoint .= '?' . http_build_query($parameters);
		}
		return $this->makeHttpRequest(self::HTTP_METHOD_GET, $endpoint);
	}

	/**
	 * @param $endpoint
	 * @param array $data
	 * @return mixed
	 */
	public function post($endpoint, $data = [])
	{
		return $this->makeHttpRequest(self::HTTP_METHOD_POST, $endpoint, $data);
	}

	/**
	 * @param $endpoint
	 * @param array $data
	 * @return mixed
	 */
	public function put($endpoint, $data = [])
	{
		return $this->makeHttpRequest(self::HTTP_METHOD_PUT, $endpoint, $data);
	}

	/**
	 * @param $method
	 * @param $endpoint
	 * @param array $body
	 * @return mixed
	 */
	private function makeHttpRequest($method, $endpoint, $body = [])
	{
		$url = self::API_BASE_URL . $endpoint;

		$args = [
			'timeout' => 10,
			'method' => $method,
			'headers' => [
				'Accept'        => 'application/json',
				'Content-Type'  => 'application/json',
				'Authorization' => "Bearer {$this->apiKey}",
			],
		];

		if ($method != self::HTTP_METHOD_GET && $method != self::HTTP_METHOD_DELETE) {
			$args['body'] = wp_json_encode($body);
		}

		$response = wp_remote_request($url, $args);
		$this->lastResponseCode = wp_remote_retrieve_response_code($response);

		if (is_wp_error($response)) {
			$data = [
				'code' => $response->get_error_code(),
				'message' => $response->get_error_message()
			];
		} else {
			$data = json_decode(wp_remote_retrieve_body($response), true);
		}

		return $data;
	}

	/**
	 * @return int
	 */
	public function getLastResponseCode()
	{
		return $this->lastResponseCode;
	}

	/**
	 * Make legacy request
	 */
	private function make_legacy_request($method, $request, $args = array())
	{
		// @codingStandardsIgnoreStart
		$url = self::API_BASE_URL . $request;
		$header = array(
			"Authorization: Bearer {$this->apiKey}",
			"Accept: application/json",
			"Content-Type: application/json"
		);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		if ($method != self::HTTP_METHOD_GET && $method != self::HTTP_METHOD_DELETE) {
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($args));
		}

		$response = curl_exec($curl);
		$err = curl_error($curl);
		$this->lastResponseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		if ($err) {
			return $err;
		} else {
			return json_decode($response, true);
		}
		// @codingStandardsIgnoreEnd
	}

	public function groups()
	{
		return $this->make_legacy_request(self::HTTP_METHOD_GET, '/groups');
	}
}
