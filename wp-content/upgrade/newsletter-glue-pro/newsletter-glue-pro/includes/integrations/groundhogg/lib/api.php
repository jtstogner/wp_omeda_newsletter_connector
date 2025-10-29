<?php

class NGL_Groundhogg_API {

    const HTTP_METHOD_GET 		= 'GET';
    const HTTP_METHOD_POST 		= 'POST';
	const HTTP_METHOD_PUT 		= 'PUT';
	const HTTP_METHOD_DELETE 	= 'DELETE';

    private $apiKey;
	private $apiToken;
	private $apiURL;
    private $lastResponseCode;

    /**
     * constructor.
     */
    public function __construct( $api_key, $api_secret, $api_url = '' ) {
        $this->apiKey = $api_key;
		$this->apiToken = $api_secret;
		$this->apiURL = $api_url;
    }

    /**
     * @param $endpoint
     * @param array $parameters
     * @return mixed
     */
    public function get( $endpoint, $parameters = [] ) {
        if ( $parameters ) {
            foreach ( $parameters as $key => $parameter ) {
                if ( is_bool( $parameter ) ) {
                    // http_build_query converts bool to int
                    $parameters[ $key ] = $parameter ? 'true' : 'false';
                }
            }
            $endpoint .= '?' . http_build_query( $parameters );
        }
        return $this->makeHttpRequest( self::HTTP_METHOD_GET, $endpoint );
    }

    /**
     * @param $endpoint
     * @param array $data
     * @return mixed
     */
    public function post($endpoint, $data = []) {
		return $this->makeHttpRequest( self::HTTP_METHOD_POST, $endpoint, $data );
    }

    /**
     * @param $endpoint
     * @param array $data
     * @return mixed
     */
    public function put($endpoint, $data = []) {
		return $this->makeHttpRequest( self::HTTP_METHOD_PUT, $endpoint, $data );
    }

    /**
     * @param $method
     * @param $endpoint
     * @param array $body
     * @return mixed
     */
    private function makeHttpRequest( $method, $endpoint, $body = [] ) {

		if ( ! empty( $this->apiURL ) ) {
			$url = $this->apiURL . $endpoint;
		} else {
			$url = rest_url( '/gh/v4' ) . $endpoint;
		}

        $args = [
			'timeout' => 3,
            'method' => $method,
            'headers' => [
				'Gh-Token'      => $this->apiToken,
				'Gh-Public-Key' => $this->apiKey
            ],
        ];

        if ( $method != self::HTTP_METHOD_GET && $method != self::HTTP_METHOD_DELETE ) {
			$args[ 'body' ] = wp_json_encode( $body );
			$args[ 'headers' ][ 'Content-type' ] = 'application/json; charset=utf-8';
        }

		$response = wp_remote_request($url, $args);

		$this->lastResponseCode = wp_remote_retrieve_response_code($response);

        if ( is_wp_error( $response ) ) {
			$data = [
				'code' => $response->get_error_code(),
				'message' => $response->get_error_message()
            ];
        } else {
			$data = json_decode( wp_remote_retrieve_body( $response ), true );
        }

        return $data;
    }

    /**
     * @return int
     */
    public function getLastResponseCode() {
        return $this->lastResponseCode;
    }

}