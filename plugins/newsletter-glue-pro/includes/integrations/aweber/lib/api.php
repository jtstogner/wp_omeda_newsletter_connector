<?php

class NGL_Aweber_API {

	const AUTHORIZE_BASE_URL 	= 'https://auth.aweber.com/oauth2/authorize';
	const TOKEN_BASE_URL 		= 'https://auth.aweber.com/oauth2/token';
	const API_BASE_URL 			= 'https://api.aweber.com/1.0';

    const HTTP_METHOD_GET 		= 'GET';
    const HTTP_METHOD_POST 		= 'POST';
	const HTTP_METHOD_PUT 		= 'PUT';
	const HTTP_METHOD_DELETE 	= 'DELETE';

	private $codeVerifierOptions 	= "newsletterglue_aweber_code_verifier";

	private $clientId 	= "5SS2CQVxPUbJ0q1gLJmjRMMbmexNoe8d";
	private $scopes 	= [
		'account.read',
        'list.read',
        'subscriber.read',
        'subscriber.write',
        'email.read',
        'email.write',
	];

	private $apiKey;
	private $accessToken;
	private $refreshToken;
	private $expiresOn;

	private $lastResponseCode;

	/**
     * constructor.
     */
	public function __construct( $api_key = null ) {
		$this->apiKey = $api_key;

		if( $api_key !== null ) {
			$integrations = get_option( 'newsletterglue_integrations' );

			if( isset( $integrations['aweber'] ) && array_key_exists( 'tokens', $integrations['aweber'] ) ) {
				$tokens = $integrations['aweber']['tokens'];
				$this->accessToken  = $tokens['access_token'];
				$this->refreshToken = $tokens['refresh_token'];
				$this->expiresOn    = $tokens['expires_on'];
			} else {
				$this->generateAccessToken();
			}
		}
	}

	public function getCodeVerifier() {
		$codeVerifier = get_option( $this->codeVerifierOptions );
		if( ! $codeVerifier ) {
			$verifier_bytes 	= random_bytes(64);
			$codeVerifier 		= rtrim( strtr( base64_encode( $verifier_bytes ), "+/", "-_" ), "=" );
			update_option( $this->codeVerifierOptions, $codeVerifier );
		}
		return $codeVerifier;
	}

	private function getCodeChallenge() {
		$challengeBytes = hash( 'sha256', $this->getCodeVerifier(), true );
		return rtrim( strtr( base64_encode( $challengeBytes ), "+/", "-_" ), "=" );
	}

	public function getAuthorizeUrl() {
		$query = [
			'response_type'         => 'code',
			'client_id'             => $this->clientId,
			'state'                 => uniqid(),
			'redirect_uri'          => 'urn:ietf:wg:oauth:2.0:oob',
			'scope'  		        => implode( " ", $this->scopes ),
			'code_challenge'        => $this->getCodeChallenge(),
			'code_challenge_method' => 'S256'
		];
		return self::AUTHORIZE_BASE_URL . '?' . http_build_query( $query );
	}

	private function generateAccessToken() {
		$query = array(
			"grant_type"    => "authorization_code",
			"code"          => $this->apiKey,
			"client_id"     => $this->clientId,
			"code_verifier" => $this->getCodeVerifier(),
		);

		$url = self::TOKEN_BASE_URL . "?" . http_build_query( $query );
		$args = [ 'method' => 'POST' ];

		$this->makeTokenRequest( $url, $args );
	}

	private function refreshAccessToken() {
		$query = array(
			'grant_type'    => 'refresh_token',
			'client_id'     => $this->clientId,
			'refresh_token' => $this->refreshToken
		);

		$url = self::TOKEN_BASE_URL . "?" . http_build_query( $query );
		$args = [ 'method' => 'POST' ];

		$this->makeTokenRequest( $url, $args );
	}

	private function isTokenExpired() {
		return $this->expiresOn < time();
	}

	public function getTokens() {
		return array(
			'access_token'  => $this->accessToken,
			'refresh_token' => $this->refreshToken,
			'expires_on'    => $this->expiresOn
		);
	}

	/**
     * @param $url
     * @param array $args
     * @return mixed
     */
    private function makeTokenRequest( $url, $args ) {

		$response = wp_remote_request( $url, $args );
		$this->lastResponseCode = wp_remote_retrieve_response_code( $response );

		if ( is_wp_error( $response ) ) {
			$data = [
				'code'    => $response->get_error_code(),
				'message' => $response->get_error_message()
			];
		} else {
			$data = json_decode( wp_remote_retrieve_body( $response ), true );

			$this->accessToken  = isset( $data['access_token'] ) ? $data['access_token'] : null;
			$this->refreshToken = isset( $data['refresh_token'] ) ? $data['refresh_token'] : null;
			$this->expiresOn    = isset( $data['expires_in'] ) ? ( time() + $data['expires_in'] ) : null;
			$data['expires_on'] = $this->expiresOn;

			if( isset( $data['refresh_token'] ) ) {
				$integrations = get_option( 'newsletterglue_integrations' );
				$integrations['aweber']['tokens'] = $data;
				update_option( 'newsletterglue_integrations', $integrations );
			}
		}

		return $data;
    }

	/**
     * @param $url
     * @return mixed
     */
	private function removeBaseUri( $url ) {
        return str_replace( self::API_BASE_URL, '', $url );
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

		if( $this->isTokenExpired() ) {
			$this->refreshAccessToken();
		}
        
		$url = self::API_BASE_URL . $this->removeBaseUri( $endpoint );

        $args = [
			'timeout' => 3,
            'method' => $method,
            'headers' => [
				'Authorization' => "Bearer {$this->accessToken}",
				'content-type' 	=> 'application/json',
            ],
        ];

        if ( $method != self::HTTP_METHOD_GET && $method != self::HTTP_METHOD_DELETE ) {

			// broadcast endpoints required content-type: application/x-www-form-urlencoded
			if( strpos($endpoint, 'broadcasts') !== false ) {
				$args[ 'body' ] = $body;
				$args['headers']['content-type'] = 'application/x-www-form-urlencoded';
			} else {
				$args[ 'body' ] = wp_json_encode( $body );
			}
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

	public function getLastResponseCode() {
		return $this->lastResponseCode;
	}

	public function me() {
		$accounts = $this->getCollection( '/accounts' );
		
		$data = [];

		if( ! empty( $accounts ) ) {
			foreach( $accounts as $account ) {
				if( isset( $account['self_link'] ) ) {
					$data = $account;
					break;
				}
			}
		}

		return $data;
	}

	public function getCollection( $url ) {
		$collection = array();

		while ( isset( $url ) ) {
			$response = $this->get( $url );
			$collection = array_merge( $response['entries'], $collection );
			$url = isset( $response['next_collection_link'] ) ? $response['next_collection_link'] : null;
		}

		return $collection;
	}
}