<?php

class NGL_Mailjet_API {

    const API_BASE_URL 			= 'https://api.mailjet.com/v3/REST';
    const HTTP_METHOD_GET 		= 'GET';
    const HTTP_METHOD_POST 		= 'POST';
	const HTTP_METHOD_PUT 		= 'PUT';
	const HTTP_METHOD_DELETE 	= 'DELETE';

    private $api_key;
	private $api_secret;
    private $lastResponseCode;

    /**
     * constructor.
     */
    public function __construct( $api_key, $api_secret ) {
        $this->api_key		= $api_key;
		$this->api_secret 	= $api_secret;
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
     * @param $endpoint
     * @param array $data
     * @return mixed
     */
    public function delete($endpoint, $data = []) {
        return $this->makeHttpRequest( self::HTTP_METHOD_DELETE, $endpoint, $data );
    }

    /**
     * @param $method
     * @param $endpoint
     * @param array $body
     * @return mixed
     */
    private function makeHttpRequest( $method, $endpoint, $body = [] ) {
        $url = self::API_BASE_URL . $endpoint;

		$auth = base64_encode( $this->api_key . ':' . $this->api_secret );

        $args = array(
			'timeout' 	=> 3,
            'method' 	=> $method,
            'headers'	=> array(
				'Authorization'	=> "Basic $auth",
				'content-type' 	=> 'application/json'
            ),
        );

        if ( $method != self::HTTP_METHOD_GET && $method != self::HTTP_METHOD_DELETE ) {
			$args[ 'body' ] = wp_json_encode( $body );
        }

		$response = wp_remote_request( $url, $args );
		$this->lastResponseCode = wp_remote_retrieve_response_code( $response );

        if ( is_wp_error( $response ) ) {
			$data = array(
				'code' 		=> $response->get_error_code(),
				'message' 	=> $response->get_error_message()
            );
        } else {
			$data = json_decode( wp_remote_retrieve_body( $response ), true );
        }

        if ( isset( $data[ 'Data' ] ) ) {
			return $data[ 'Data' ];
		} else {
			return 0;
		}
    }

    /**
     * @return int
     */
    public function getLastResponseCode() {
        return $this->lastResponseCode;
    }

}