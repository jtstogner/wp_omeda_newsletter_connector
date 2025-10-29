<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * NGL_REST_API_Embed_URL class.
 */
class NGL_REST_API_Embed_URL {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route(
			'newsletterglue/' . newsletterglue()->api_version(),
			'/embed_url',
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

		$content = '';

		$data = json_decode( $request->get_body(), true );

		$url = isset( $data['url'] ) ? esc_url( $data['url'] ) : '';

		$provider = $this->is_allowed_embed( $url );

		if ( $provider ) {
			$method  = 'newsletterglue_get_' . $provider . '_content';
			$content = call_user_func( $method, $url );
		}

		if ( empty( $content ) ) {
			return rest_ensure_response(
				array(
					'error'    => true,
					'url'      => $url,
					'provider' => $provider,
				)
			);
		}

		return rest_ensure_response(
			array(
				'content'  => $content,
				'url'      => $url,
				'provider' => $provider,
			)
		);
	}

	/**
	 * Check if URL can be embedded.
	 */
	public function is_allowed_embed( $url ) {

		$allowed = apply_filters(
			'newsletterglue_allowed_embed_patterns',
			array(
				'x'          => '/http(?:s)?:\/\/(?:www\.)?x\.com\/([a-zA-Z0-9_]+)/',
				'twitter'    => '/http(?:s)?:\/\/(?:www\.)?twitter\.com\/([a-zA-Z0-9_]+)/',
				'youtube'    => '/(youtube.com|youtu.be)\/(watch)?(\?v=)?(\S+)?/',
				'spotify'    => '/\.spotify\.com/',
				'soundcloud' => '/soundcloud\.com/',
				'reddit'     => '/reddit\.com/',
			)
		);

		foreach ( $allowed as $provider => $regex ) {
			if ( preg_match( $regex, $url ) ) {
				return $provider;
				break;
			}
		}
	}
}

return new NGL_REST_API_Embed_URL();
