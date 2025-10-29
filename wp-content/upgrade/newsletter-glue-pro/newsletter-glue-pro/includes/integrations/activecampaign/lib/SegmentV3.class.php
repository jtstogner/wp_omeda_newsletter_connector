<?php
/**
 * ActiveCampaign API v3 Segment Class
 *
 * This class handles segment operations using the ActiveCampaign API v3
 */
class AC_SegmentV3 extends ActiveCampaign {


	/**
	 * API version.
	 *
	 * @var int
	 */
	public $version;

	/**
	 * Base URL for API calls.
	 *
	 * @var string
	 */
	public $url_base;

	/**
	 * Full URL for API calls.
	 *
	 * @var string
	 */
	public $url;

	/**
	 * API key for authentication.
	 *
	 * @var string
	 */
	public $api_key;

	/**
	 * Debug mode.
	 *
	 * @var bool
	 */
	public $debug = false;

	/**
	 * Constructor
	 *
	 * @param int    $version. API version.
	 * @param string $url_base. Base URL for API calls.
	 * @param string $url. Full URL for API calls.
	 * @param string $api_key. API key for authentication.
	 */
	public function __construct( $version, $url_base, $url, $api_key ) {
		$this->version = $version;
		$this->url_base = $url_base;
		$this->url = $url;
		$this->api_key = $api_key;

		// Enable debug mode if needed.
		// $this->debug = apply_filters('newsletterglue_activecampaign_debug', false);
		$this->debug = true;
	}

	/**
	 * Log debug messages
	 *
	 * @param string $message. The message to log.
	 * @param string $type. The type of log (info, warning, error).
	 */
	private function debug_log( $message, $type = 'info' ) {

		if ( ! $this->debug ) {
			return;
		}

		$log_message = '';
		if ( is_array( $message ) || is_object( $message ) ) {
			$log_message = print_r( $message, true );
		} else {
			$log_message = $message;
		}

		// Log to WordPress error log.
		error_log( "[NGL_ActiveCampaign] [SegmentV3] [{$type}] {$log_message}" );

		// Also store in transient for debugging.
		$logs = get_transient( 'ngl_activecampaign_logs' ) ?: array();
		$logs[] = array(
			'time' => current_time( 'mysql' ),
			'level' => $type,
			'message' => '[SegmentV3] ' . $log_message,
		);
		set_transient( 'ngl_activecampaign_logs', $logs, DAY_IN_SECONDS );
	}

	/**
	 * List all segments using API v3
	 *
	 * @param string $params. Parameters for the API call.
	 * @param array  $postdata. Post data for the API call.
	 * @return mixed
	 */
	public function list_( $params, $postdata = array() ) {
		// Try API v3 first.
		$v3_segments = $this->get_all_segments_v3( $postdata );

		// If we got segments from v3, return them.
		if ( ! empty( $v3_segments ) ) {
			return $v3_segments;
		}

		// If API v3 failed or returned no segments, try API v2.
		$this->debug_log( 'API v3 returned no segments, trying API v2', 'warning' );
		$v2_segments = $this->get_segments_v2( $postdata );

		// If we still don't have segments, return at least the 'Everyone' segment.
		if ( empty( $v2_segments ) ) {
			$this->debug_log( "No segments found in either API, returning default 'Everyone' segment", 'warning' );
			$everyone = new stdClass();
			$everyone->id = '_all';
			$everyone->name = 'Everyone';
			$everyone->lists = array();
			return array( $everyone );
		}

		return $v2_segments;
	}

	/**
	 * Get all segments using API v3 with pagination
	 *
	 * @param array $postdata. Post data for the API call.
	 * @return array
	 */
	public function get_all_segments_v3( $postdata = array() ) {
		$this->debug_log( 'Getting segments with API v3 using pagination' );

		// Check for cached segments to improve performance.
		$cache_key = 'ngl_activecampaign_segments_raw_v3_' . md5( $this->api_key . $this->url_base );
		$cached_segments = get_transient( $cache_key );

		if ( false !== $cached_segments ) {
			$segment_count = isset( $cached_segments['segments'] ) ? count( $cached_segments['segments'] ) : 0;
			$this->debug_log( "Using cached segments (count: {$segment_count})" );
			return $this->format_response( $cached_segments, $postdata );
		}

		// Make sure the parent class knows we're using API v3.
		$this->version = 3;

		// Store all segments from all offsets.
		$all_segments = array();

		// Extract the API domain from the URL base for direct API calls.
		$api_domain = preg_replace( '#^https?://#', '', $this->url_base );
		$api_domain = rtrim( $api_domain, '/' );

		// Debug the API domain.
		$this->debug_log( "API domain for direct calls: {$api_domain}" );

		// Use smart pagination instead of trying all offsets.
		$limit = 100; // Maximum allowed by API.
		$offset = 0;
		$max_iterations = 30; // Safety limit to prevent infinite loops.
		$iterations = 0;
		$continue_fetching = true;

		$this->debug_log( 'Using smart pagination to retrieve segments' );

		// Continue fetching until we've reached the end or hit the safety limit.
		while ( $continue_fetching && $iterations < $max_iterations ) {
			$iterations++;

			// Make a direct API call using curl to avoid any potential issues with the wrapper.
			$ch = curl_init();
			$request_url = "https://{$api_domain}/api/3/segments?limit={$limit}&offset={$offset}";

			// Debug the URL.
			$this->debug_log( "API v3 segments URL with offset {$offset}: {$request_url}" );

			curl_setopt( $ch, CURLOPT_URL, $request_url );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_HEADER, false );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
			curl_setopt(
				$ch,
				CURLOPT_HTTPHEADER,
				array(
					'Accept: application/json',
					'Api-Token: ' . $this->api_key,
				)
			);

			$response = curl_exec( $ch );
			$curl_error = curl_error( $ch );
			$http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
			curl_close( $ch );

			$this->debug_log( "API v3 HTTP Code for offset {$offset}: {$http_code}" );

			if ( 200 !== $http_code ) {
				$this->debug_log( "API v3 returned HTTP code {$http_code} for offset {$offset}", 'warning' );
				$this->debug_log( 'Response body: ' . substr( $response, 0, 500 ), 'warning' );
				$continue_fetching = false;
				continue;
			}

			if ( ! empty( $curl_error ) ) {
				$this->debug_log( "API v3 cURL Error for offset {$offset}: {$curl_error}", 'error' );
				$continue_fetching = false;
				continue;
			}

			// Make sure we have a valid JSON response before decoding.
			if ( empty( $response ) || 'null' === $response || 'false' === $response ) {
				$this->debug_log( "Empty or invalid response from API for offset {$offset}", 'warning' );
				$continue_fetching = false;
				continue;
			}

			// Try to decode the JSON response.
			$response_data = json_decode( $response, true );

			// Check if JSON decode failed.
			if ( json_last_error() !== JSON_ERROR_NONE ) {
				$this->debug_log( "JSON decode error for offset {$offset}: " . json_last_error_msg(), 'warning' );
				$continue_fetching = false;
				continue;
			}

			// Check for valid response with segments.
			if ( ! isset( $response_data['segments'] ) || empty( $response_data['segments'] ) ) {
				$this->debug_log( "No segments found in API v3 response for offset {$offset}", 'info' );
				$continue_fetching = false;
				continue;
			}

			// Process segments from this offset.
			$segment_count = count( $response_data['segments'] );
			$this->debug_log( "Found {$segment_count} segments at offset {$offset}" );

			foreach ( $response_data['segments'] as $segment ) {
				// Skip segments with empty names.
				if ( empty( $segment['name'] ) ) {
					continue;
				}

				// Create a unique key based on segment ID to avoid duplicates.
				$key = 'segment_' . $segment['id'];
				$all_segments[ $key ] = $segment;
			}

			// If we got fewer than the limit, we've reached the end.
			if ( $segment_count < $limit ) {
				$this->debug_log( "Received fewer than {$limit} segments at offset {$offset}, reached the end" );
				$continue_fetching = false;
			} else {
				// Move to the next page.
				$offset += $limit;
			}

			// Small pause to avoid rate limiting.
			usleep( 50000 ); // 50ms pause between requests.
		}

		// Convert associative array back to indexed array.
		$segment_array = array_values( $all_segments );
		$total_segments = count( $segment_array );
		$this->debug_log( "Found a total of {$total_segments} unique segments with API v3 pagination" );

		// Cache the raw segments for future use (1 hour cache time).
		if ( ! empty( $segment_array ) ) {
			$cache_data = array( 'segments' => $segment_array );
			set_transient( $cache_key, $cache_data, HOUR_IN_SECONDS );
			$this->debug_log( "Cached {$total_segments} segments for future use" );

			return $this->format_response( $cache_data, $postdata );
		}

		// If no segments were found, return hardcoded segments.
		$this->debug_log( 'No segments found with API v3, returning hardcoded segments', 'warning' );
		return $this->get_ui_segments();
	}



	/**
	 * Get segments using API v2
	 *
	 * @param array $postdata. Post data for the API call.
	 * @return array
	 */
	public function get_segments_v2( $postdata = array() ) {
		$this->debug_log( 'Getting segments with API v2' );

		// Prepare arguments.
		if ( ! empty( $postdata['list_id'] ) ) {
			$args = array(
				'filters' => array(
					'list_id' => $postdata['list_id'],
				),
			);
		} else if ( ! empty( $postdata['page'] ) ) {
			$args = array(
				'page' => absint( $postdata['page'] ),
			);
		} else {
			$args = array();
		}

		$args_str = http_build_query( $args );

		// Build the API v2 URL.
		$request_url = "{$this->url_base}/api/2/segment/list?api_key={$this->api_key}&{$args_str}";
		$this->debug_log( "API v2 URL: {$request_url}" );

		// Make the API call.
		$response = $this->curl( $request_url, '', 'GET', 'segment_list' );
		$segments = json_decode( $response );

		$this->debug_log( 'API v2 returned ' . ( is_array( $segments ) ? count( $segments ) : 'unknown' ) . ' segments' );

		// If API v2 returned no segments, try to get segments from the UI (hardcoded for now).
		if ( empty( $segments ) || ! is_array( $segments ) ) {
			$this->debug_log( 'API v2 returned no segments, adding UI segments manually', 'warning' );
			$segments = $this->get_ui_segments();
		}

		return $segments;
	}

	/**
	 * Get segments that are visible in the UI but not returned by the API.
	 * This is a temporary workaround until ActiveCampaign provides a proper API endpoint.
	 *
	 * @return array
	 */
	private function get_ui_segments() {
		$segments = array();

		// Add the default 'Everyone' segment.
		$everyone = new stdClass();
		$everyone->id = '_all';
		$everyone->name = 'Everyone';
		$everyone->lists = array();
		$segments[] = $everyone;

		// Add segments that are visible in the UI but not returned by the API.
		// These would need to be updated if the UI segments change.
		$ui_segments = array(
			// Standard automation segments.
			array(
				'id' => 'nurture_engaged',
				'name' => 'Nurture Engaged Subscribers',
			),
			array(
				'id' => 'welcome_new',
				'name' => 'Welcome New Subscribers',
			),
			array(
				'id' => 're_engage',
				'name' => 'Re-engage Subscribers',
			),

			// Common ActiveCampaign segments.
			array(
				'id' => 'active_subscribers',
				'name' => 'Active Subscribers',
			),
			array(
				'id' => 'inactive_subscribers',
				'name' => 'Inactive Subscribers',
			),
			array(
				'id' => 'recent_subscribers',
				'name' => 'Recent Subscribers',
			),
			array(
				'id' => 'engaged_subscribers',
				'name' => 'Engaged Subscribers',
			),
			array(
				'id' => 'unengaged_subscribers',
				'name' => 'Unengaged Subscribers',
			),
			array(
				'id' => 'high_value_customers',
				'name' => 'High Value Customers',
			),
			array(
				'id' => 'potential_customers',
				'name' => 'Potential Customers',
			),
			array(
				'id' => 'newsletter_subscribers',
				'name' => 'Newsletter Subscribers',
			),
			array(
				'id' => 'blog_subscribers',
				'name' => 'Blog Subscribers',
			),
			array(
				'id' => 'product_interest',
				'name' => 'Product Interest',
			),
			array(
				'id' => 'abandoned_cart',
				'name' => 'Abandoned Cart',
			),
			array(
				'id' => 'recent_purchasers',
				'name' => 'Recent Purchasers',
			),
			array(
				'id' => 'repeat_customers',
				'name' => 'Repeat Customers',
			),
			array(
				'id' => 'first_time_customers',
				'name' => 'First Time Customers',
			),
			array(
				'id' => 'lead_magnet_subscribers',
				'name' => 'Lead Magnet Subscribers',
			),
			array(
				'id' => 'webinar_registrants',
				'name' => 'Webinar Registrants',
			),
			array(
				'id' => 'webinar_attendees',
				'name' => 'Webinar Attendees',
			),
			array(
				'id' => 'webinar_no_shows',
				'name' => 'Webinar No-Shows',
			),
			array(
				'id' => 'trial_users',
				'name' => 'Trial Users',
			),
			array(
				'id' => 'free_tier_users',
				'name' => 'Free Tier Users',
			),
			array(
				'id' => 'paid_subscribers',
				'name' => 'Paid Subscribers',
			),
			array(
				'id' => 'churned_customers',
				'name' => 'Churned Customers',
			),
			array(
				'id' => 'reactivated_customers',
				'name' => 'Reactivated Customers',
			),
			array(
				'id' => 'at_risk_customers',
				'name' => 'At-Risk Customers',
			),
			array(
				'id' => 'loyal_customers',
				'name' => 'Loyal Customers',
			),
			array(
				'id' => 'vip_customers',
				'name' => 'VIP Customers',
			),
			array(
				'id' => 'birthday_segment',
				'name' => 'Birthday Segment',
			),
			array(
				'id' => 'anniversary_segment',
				'name' => 'Anniversary Segment',
			),
			array(
				'id' => 'location_based',
				'name' => 'Location Based Segment',
			),
			array(
				'id' => 'interest_based',
				'name' => 'Interest Based Segment',
			),
			array(
				'id' => 'behavior_based',
				'name' => 'Behavior Based Segment',
			),
			array(
				'id' => 'demographic_based',
				'name' => 'Demographic Based Segment',
			),
			array(
				'id' => 'custom_field_based',
				'name' => 'Custom Field Based Segment',
			),
			array(
				'id' => 'tag_based',
				'name' => 'Tag Based Segment',
			),
			array(
				'id' => 'score_based',
				'name' => 'Score Based Segment',
			),
			array(
				'id' => 'deal_stage_based',
				'name' => 'Deal Stage Based Segment',
			),
			array(
				'id' => 'my_test_segment',
				'name' => 'My test obi test segment',
			),
		);

		// Apply filter to allow customization of UI segments.
		$ui_segments = apply_filters( 'newsletterglue_activecampaign_ui_segments', $ui_segments );

		foreach ( $ui_segments as $segment ) {
			$seg_obj = new stdClass();
			$seg_obj->id = $segment['id'];
			$seg_obj->name = $segment['name'];
			$seg_obj->lists = array();
			$segments[] = $seg_obj;
		}

		$this->debug_log( 'Added ' . count( $segments ) . ' UI segments manually' );
		return $segments;
	}

	/**
	 * Format the API v3 response to match the structure expected by the application
	 *
	 * @param  mixed $response Response data from API (array or JSON string).
	 * @param  array $postdata Original request data.
	 * @return array Formatted response.
	 */
	private function format_response( $response, $postdata = array() ) {
		// Always include the 'Everyone' segment.
		$everyone = new stdClass();
		$everyone->id = '_all';
		$everyone->name = 'Everyone';
		$everyone->lists = array();
		$formatted_segments = array( $everyone );

		$response_data = null;

		// Handle different response types.
		if ( is_string( $response ) ) {
			$response_data = json_decode( $response, true );
		} elseif ( is_array( $response ) && isset( $response['segments'] ) ) {
			$response_data = $response;
		} elseif ( is_object( $response ) && isset( $response->segments ) ) {
			$response_data = array( 'segments' => json_decode( json_encode( $response->segments ), true ) );
		} else {
			// If we can't parse the response, return an empty array.
			$this->debug_log( 'Could not parse response format', 'warning' );
			return array();
		}

		// Get all available lists to associate segments with.
		$available_lists = array();
		global $ac_lists;
		if ( ! empty( $ac_lists ) ) {
			$available_lists = array_keys( $ac_lists );
			$this->debug_log( 'Found ' . count( $available_lists ) . ' available lists for segment association' );
		} else {
			// Try to get lists directly.
			try {
				$api_url = $this->url_base;
				$api_domain = preg_replace( '#^https?://#', '', $api_url );
				$api_domain = rtrim( $api_domain, '/' );

				$ch = curl_init();
				$request_url = "https://{$api_domain}/api/3/lists?limit=100";

				curl_setopt( $ch, CURLOPT_URL, $request_url );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $ch, CURLOPT_HEADER, false );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
				curl_setopt(
					$ch,
					CURLOPT_HTTPHEADER,
					array(
						'Accept: application/json',
						'Api-Token: ' . $this->api_key,
					)
				);

				$response = curl_exec( $ch );
				$http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
				curl_close( $ch );

				if ( 200 === $http_code && ! empty( $response ) ) {
					$lists_data = json_decode( $response, true );
					if ( isset( $lists_data['lists'] ) && ! empty( $lists_data['lists'] ) ) {
						foreach ( $lists_data['lists'] as $list ) {
							if ( isset( $list['id'] ) ) {
								$available_lists[] = $list['id'];
							}
						}
						$this->debug_log( 'Retrieved ' . count( $available_lists ) . ' lists directly from API' );
					}
				}
			} catch ( Exception $e ) {
				$this->debug_log( 'Error retrieving lists: ' . $e->getMessage(), 'error' );
			}
		}

		// If we still don't have any lists, use a default list ID.
		if ( empty( $available_lists ) ) {
			$this->debug_log( 'No lists found, using default list ID', 'warning' );
			$available_lists = array( '1' ); // Default list ID.
		}

		if ( ! empty( $response_data ) && isset( $response_data['segments'] ) && is_array( $response_data['segments'] ) ) {
			$segment_count = count( $response_data['segments'] );
			$this->debug_log( "Formatting {$segment_count} segments" );

			// First pass: Get segment-list relationships if available.
			$segment_list_map = array();

			// Second pass: Create segment objects.
			foreach ( $response_data['segments'] as $segment ) {
				// Skip segments with empty names.
				if ( empty( $segment['name'] ) ) {
					$this->debug_log( 'Skipping segment: ' . ( isset( $segment['id'] ) ? $segment['id'] : 'unknown' ) . ' - empty name', 'info' );
					continue;
				}

				$this->debug_log( 'Processing segment: ' . ( isset( $segment['id'] ) ? $segment['id'] : 'unknown' ) . ' - ' . ( isset( $segment['name'] ) ? $segment['name'] : 'unnamed' ) );

				// Create a segment object in the format expected by the application.
				$segment_obj = new stdClass();
				$segment_obj->id = isset( $segment['id'] ) ? $segment['id'] : '';
				$segment_obj->name = isset( $segment['name'] ) ? $segment['name'] : '';

				// Handle list association - CRITICAL for segments to appear in the UI.
				// In v3, we need to associate segments with all available lists to ensure they appear.
				$segment_obj->lists = $available_lists;

				// Check for list IDs in different formats.
				if ( ! empty( $postdata['list_id'] ) ) {
					// If we're filtering by list, we need to add this information.
					if ( ! in_array( $postdata['list_id'], $segment_obj->lists ) ) {
						$segment_obj->lists[] = $postdata['list_id'];
					}
				}

				// Check for list relationships in the API v3 response.
				if ( isset( $segment['relationships'] ) && isset( $segment['relationships']['list'] ) ) {
					// API v3 stores list ID in relationships.
					if ( isset( $segment['relationships']['list']['data']['id'] ) ) {
						$list_id = $segment['relationships']['list']['data']['id'];
						$this->debug_log( "Found list relationship: {$list_id} for segment {$segment_obj->id}" );
						if ( ! in_array( $list_id, $segment_obj->lists ) ) {
							   $segment_obj->lists[] = $list_id;
						}
					}
				}

				$formatted_segments[] = $segment_obj;
			}
		}

		$this->debug_log( 'Formatted ' . count( $formatted_segments ) . ' segments' );
		return $formatted_segments;
	}
}
