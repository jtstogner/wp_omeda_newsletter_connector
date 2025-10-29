<?php
/**
 * Ad Inserter REST API Controller
 *
 * @package Newsletter_Glue
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ad Inserter REST API Controller
 */
class NGL_Ad_Inserter_REST_Controller extends WP_REST_Controller {

	/**
	 * The namespace of this controller's route.
	 *
	 * @var string
	 */
	protected $namespace = 'newsletter-glue/v1';

	/**
	 * The base of this controller's route.
	 *
	 * @var string
	 */
	protected $rest_base = 'ad-inserter';

	/**
	 * Ad Integration Manager instance
	 *
	 * @var NGL_Ad_Integration_Manager
	 */
	private $ad_manager;

	/**
	 * Constructor
	 */
	public function __construct() {
		// Initialize the ad integration manager
		$this->ad_manager = newsletterglue_get_ad_manager();
	}

	/**
	 * Register the routes for this controller.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/ads',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_ads' ),
					'permission_callback' => array( $this, 'get_ads_permissions_check' ),
					'args'                => $this->get_ads_args(),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/set-integration',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'set_integration' ),
					'permission_callback' => array( $this, 'set_integration_permissions_check' ),
					'args'                => $this->set_integration_args(),
				),
			)
		);
	}

	/**
	 * Check if a given request has access to get items.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool True if the request has read access, WP_Error object otherwise.
	 */
	public function get_ads_permissions_check( $request ) {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return new WP_Error( 'rest_forbidden', esc_html__( 'You do not have permission to access this resource.', 'newsletter-glue' ), array( 'status' => 403 ) );
		}
		return true;
	}

	/**
	 * Check if a given request has access to set the integration.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool True if the request has read access, WP_Error object otherwise.
	 */
	public function set_integration_permissions_check( $request ) {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return new WP_Error( 'rest_forbidden', esc_html__( 'You do not have permission to access this resource.', 'newsletter-glue' ), array( 'status' => 403 ) );
		}
		return true;
	}

	/**
	 * Get a collection of ads.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_ads( $request ) {
		$search_term = $request->get_param( 'search' );
		
		// Get ads from the ad integration manager
		$ads = $this->ad_manager->get_ads();

		if ( empty( $ads ) ) {
			return rest_ensure_response( array() );
		}

		// Filter ads based on search term if provided
		if ( ! empty( $search_term ) ) {
			$search_term = strtolower( $search_term );
			$ads = array_filter(
				$ads,
				function( $ad ) use ( $search_term ) {
					$title    = isset( $ad['title'] ) ? strtolower( $ad['title'] ) : '';
					$category = isset( $ad['category'] ) ? strtolower( $ad['category'] ) : '';
					$group    = isset( $ad['group'] ) ? strtolower( $ad['group'] ) : '';

					return strpos( $title, $search_term ) !== false ||
					       strpos( $category, $search_term ) !== false ||
					       strpos( $group, $search_term ) !== false;
				}
			);
		}

		// Re-index the array after filtering
		$ads = array_values( $ads );

		return rest_ensure_response( $ads );
	}

	/**
	 * Get the query params for collections.
	 *
	 * @return array
	 */
	public function get_ads_args() {
		return array(
			'search' => array(
				'description'       => esc_html__( 'Search term to filter ads.', 'newsletter-glue' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => 'rest_validate_request_arg',
				'required'          => false,
			),
		);
	}

	public function set_integration_args() {
		return array(
			'integration_id' => array(
				'description'       => esc_html__( 'Integration ID to set as active.', 'newsletter-glue' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => 'rest_validate_request_arg',
				'required'          => true,
			),
		);
	}

	public function set_integration( $request ) {

		$ad_manager = newsletterglue_get_ad_manager();

		//error_log( $ad_manager->get_active_integration() );

		$integration_id = $request->get_param( 'integration_id' );

		if ( ! $ad_manager->get_integration( $integration_id ) ) {
			return new WP_Error( 'invalid_integration', esc_html__( 'Invalid integration ID.', 'newsletter-glue' ), array( 'status' => 400 ) );
		}

		$ad_manager->set_active_integration( $integration_id );

		return rest_ensure_response( true );
	}
}
