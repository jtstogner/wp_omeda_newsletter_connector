<?php
/**
 * ActiveCampaign
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Class.
 */
class NGL_Activecampaign extends NGL_Abstract_Integration {

	/**
	 * App name.
	 *
	 * @var string
	 */
	public $app     = 'activecampaign';

	/**
	 * API URL.
	 *
	 * @var string
	 */
	public $api_url = null;

	/**
	 * API Key.
	 *
	 * @var string
	 */
	public $api_key = null;

	/**
	 * API connection.
	 *
	 * @var object
	 */
	public $api     = null;

	/**
	 * API version tracking - for method-by-method migration
	 */
	private $_use_v3_api = true; // Master switch for API v3.

	/**
	 * List of methods that should use API v3.
	 *
	 * @var array
	 */
	private $_v3_methods = array(
		'get_segments' => true, // Enable API v3 for get_segments method.
	// 'get_lists' => true,
	// 'send_newsletter' => true,
	);
	private $_debug_mode = false;

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Include needed files.
		include_once 'lib/ActiveCampaign.class.php';

		$this->get_api_key();

		add_filter( 'newsletterglue_email_content_activecampaign', array( $this, 'newsletterglue_email_content_activecampaign' ), 10, 3 );

		add_filter( 'newsltterglue_activecampaign_html_content', array( $this, 'html_content' ), 10, 2 );

		// Enable debug mode if needed.
		$this->_debug_mode = apply_filters( 'newsletterglue_activecampaign_debug', false );
	}

	/**
	 * Get API Key.
	 */
	public function get_api_key() {

		$integrations = get_option( 'newsletterglue_integrations' );
		$integration  = isset( $integrations[ $this->app ] ) ? $integrations[ $this->app ] : '';

		$this->api_key         = isset( $integration['api_key'] ) ? $integration['api_key'] : '';
		$this->api_url        = isset( $integration['api_url'] ) ? $integration['api_url'] : '';
	}

	/**
	 * Add Integration.
	 */
	public function add_integration( $args = array() ) {

		$args         = $this->get_connection_args( $args );

		$api_key     = $args['api_key'];
		$api_url     = $args['api_url'];

		$this->api = new ActiveCampaign( $api_url, $api_key );

		$account = $this->api->api( 'account/view' );

		if ( ! isset( $account->email ) ) {

			$this->remove_integration();

			$result = array( 'response' => 'invalid' );

			delete_option( 'newsletterglue_activecampaign' );

		} else {

			if ( ! $this->already_integrated( $this->app, $api_key ) ) {
				$this->save_integration( $api_key, $api_url, (array) $account );
			}

			$result = array( 'response' => 'successful' );

			update_option( 'newsletterglue_activecampaign', (array) $account );

		}

		return $result;
	}

	/**
	 * Save Integration.
	 */
	public function save_integration( $api_key = '', $api_url = '', $account = array() ) {

		// Set these in memory.
		$this->api_key = $api_key;
		$this->api_url = $api_url;

		delete_option( 'newsletterglue_integrations' );

		$integrations = get_option( 'newsletterglue_integrations' );

		$integrations[ $this->app ] = array();
		$integrations[ $this->app ]['api_key']         = $api_key;
		$integrations[ $this->app ]['api_url']         = $api_url;

		$integrations[ $this->app ]['connection_name'] = newsletterglue_get_name( $this->app );

		update_option( 'newsletterglue_integrations', $integrations );

		// Add default options.
		$globals = get_option( 'newsletterglue_options' );

		$options = array(
			'from_name'     => newsletterglue_get_default_from_name(),
			'from_email'    => isset( $account['email'] ) ? $account['email'] : '',
		);

		foreach ( $options as $key => $value ) {
			$globals[ $this->app ][ $key ] = $value;
		}

		update_option( 'newsletterglue_options', $globals );
	}

	/**
	 * Debug log helper
	 *
	 * @param string $message The message to log
	 * @param string $level   The log level (info, error, etc)
	 */
	private function debug_log( $message, $level = 'info' ) {
		if ( $this->_debug_mode ) {
			// Store logs in a transient for debugging.
			$logs = get_transient( 'ngl_activecampaign_logs' ) ?: array();
			$logs[] = array(
				'time' => current_time( 'mysql' ),
				'level' => $level,
				'message' => $message,
			);
			set_transient( 'ngl_activecampaign_logs', $logs, DAY_IN_SECONDS );

			// Also log to error log for easier debugging.
			error_log( "[NGL_ActiveCampaign] [{$level}] {$message}" );
		}
	}

	/**
	 * Check if a method should use API v3
	 *
	 * @param  string $method_name The method name to check
	 * @return bool Whether to use API v3 for this method
	 */
	private function should_use_v3( $method_name ) {
		// Master switch must be on AND the specific method must be enabled.
		return $this->_use_v3_api && isset( $this->_v3_methods[ $method_name ] ) && $this->_v3_methods[ $method_name ];
	}

	/**
	 * Connect.
	 */
	public function connect() {
		if ( $this->_debug_mode ) {
			$this->debug_log( 'Connecting to ActiveCampaign API' );
		}

		$this->api = new ActiveCampaign( $this->api_url, $this->api_key );

		return $this->api;
	}

	/**
	 * Connect to API v3.
	 *
	 * This is a separate method to avoid affecting the original implementation.
	 *
	 * @return object The API connection object
	 */
	private function connect_v3() {
		if ( $this->_debug_mode ) {
			$this->debug_log( 'Connecting to ActiveCampaign API v3' );
		}

		// Make sure we have a valid API URL.
		if ( empty( $this->api_url ) ) {
			$this->debug_log( 'API URL is empty', 'error' );
			return false;
		}

		// Make sure the API URL is properly formatted.
		$api_url = $this->api_url;

		// Remove trailing slashes.
		$api_url = rtrim( $api_url, '/' );

		// Debug the API URL.
		if ( $this->_debug_mode ) {
			$this->debug_log( "API v3 URL: {$api_url}" );
			$this->debug_log( 'API Key (truncated): ' . substr( $this->api_key, 0, 5 ) . '...' );
		}

		$api = new ActiveCampaign( $api_url, $this->api_key );
		$api->version( 3 );

		return $api;
	}

	/**
	 * Get form defaults.
	 */
	public function get_form_defaults() {

		$this->api = new ActiveCampaign( $this->api_url, $this->api_key );

		$defaults = array();

		$defaults['lists']     = $this->get_lists();

		return $defaults;
	}

	/**
	 * Get lists.
	 */
	public function get_lists() {
		global $ac_lists;

		$_lists = array();

		$lists = $this->api->api(
			'list_/list_',
			array(
				'ids' => 'all',
				'full' => 0,
			)
		);

		if ( ! empty( $lists ) ) {
			foreach ( $lists as $key => $data ) {
				$array = (array) $data;
				$id = @$array['id'];
				if ( $id ) {
					$_lists[ $id ] = @$array['name'];
				}
			}
		}

		asort( $_lists );

		$ac_lists = $_lists;

		return $_lists;
	}

	/**
	 * Get Segments.
	 */
	public function get_segments() {
		// Check if we should use API v3 for this method.
		if ( $this->_use_v3_api && isset( $this->_v3_methods['get_segments'] ) && $this->_v3_methods['get_segments'] ) {
			if ( $this->_debug_mode ) {
				$this->debug_log( 'Using API v3 for get_segments method' );
			}
			return $this->get_segments_v3();
		}

		// Original implementation starts here - completely unchanged.
		global $ac_lists;

		$_segments = array();

		$segments1 = $this->api->api( 'segment/list_', array( 'page' => 1 ) );
		$segments2 = $this->api->api( 'segment/list_', array( 'page' => 2 ) );
		$segments3 = $this->api->api( 'segment/list_', array( 'page' => 3 ) );

		if ( ! empty( $segments1 ) ) {
			$segments = $segments1;
		}
		if ( ! empty( $segments2 ) ) {
			$segments = array_merge( $segments, $segments2 );
		}
		if ( ! empty( $segments3 ) ) {
			$segments = array_merge( $segments, $segments3 );
		}

		if ( ! empty( $segments ) ) {
			if ( ! empty( $ac_lists ) ) {
				foreach ( $ac_lists as $list_id => $list_name ) {
					$_segments[ 'optgroup1_' . $list_id ] = $list_name;
					foreach ( $segments as $key => $data ) {
						$array = (array) $data;
						$id = @$array['id'];
						if ( $id ) {
							   $thelists = @$array['lists'];
							   $thelists = (array) $thelists;
							   $flist = reset( $thelists );
							if ( $flist == $list_id ) {
								$_segments[ $id ] = @$array['name'];
							}
						}
					}
				}
			} else {
				foreach ( $segments as $key => $data ) {
					$array = (array) $data;
					$id = @$array['id'];
					if ( $id ) {
						$_segments[ $id ] = @$array['name'];
					}
				}
				asort( $_segments );
			}
		}

		return array( '_all' => __( 'Everyone', 'newsletter-glue' ) ) + $_segments;
	}

	/**
	 * Get Segments using API v3.
	 *
	 * This method implements the segments retrieval using ActiveCampaign API v3.
	 *
	 * @return array Array of segments in the same format as the original method.
	 */
	private function get_segments_v3() {
		global $ac_lists;

		if ( $this->_debug_mode ) {
			$this->debug_log( 'Executing get_segments_v3 method' );
		}

		// Check for cached segments to improve performance.
		$cache_key = 'ngl_activecampaign_segments_v3_' . md5( $this->api_key . $this->api_url );
		$cached_segments = get_transient( $cache_key );

		if ( false !== $cached_segments ) {
			if ( $this->_debug_mode ) {
				$this->debug_log( 'Using cached segments (count: ' . count( $cached_segments ) . ')' );
			}
			return $cached_segments;
		}

		// Create a separate API v3 connection without affecting the original.
		$api_v3 = $this->connect_v3();

		$_segments = array();

		try {
			// Get lists first if needed.
			if ( empty( $ac_lists ) ) {
				$this->get_lists();
			}

			// Include the SegmentV3 class if available.
			$segment_v3_file = __DIR__ . '/lib/SegmentV3.class.php';
			if ( file_exists( $segment_v3_file ) ) {
				include_once $segment_v3_file;

				if ( class_exists( 'AC_SegmentV3' ) ) {
					if ( $this->_debug_mode ) {
						$this->debug_log( 'Using AC_SegmentV3 class' );
					}

					// Create a new SegmentV3 instance with the v3 API connection.
					$segment_v3 = new AC_SegmentV3( 3, $api_v3->url_base, $api_v3->url, $api_v3->api_key );
					$segment_v3->debug = $this->_debug_mode; // Pass debug mode to the SegmentV3 class.

					// Get segments using the SegmentV3 class.
					try {
						$segments = $segment_v3->list_( array( 'page' => 1 ) );

						if ( $this->_debug_mode ) {
							$this->debug_log( 'SegmentV3 returned ' . ( is_array( $segments ) ? count( $segments ) : 'unknown' ) . ' segments' );
						}
					} catch ( Exception $e ) {
						if ( $this->_debug_mode ) {
							$this->debug_log( 'Error in SegmentV3->list_: ' . $e->getMessage(), 'error' );
						}
						$segments = array();
					}

					if ( $this->_debug_mode ) {
						$this->debug_log( 'Retrieved ' . ( is_array( $segments ) ? count( $segments ) : 'unknown' ) . ' segments from SegmentV3 class' );
					}

					// Process segments similar to the original method.
					if ( ! empty( $segments ) ) {
						// Track all segments for fallback.
						$all_segments = array();
						foreach ( $segments as $segment ) {
							$id = isset( $segment->id ) ? $segment->id : null;
							$name = isset( $segment->name ) ? $segment->name : '';
							if ( $id && ! empty( $name ) ) {
								$all_segments[ $id ] = $name;
							}
						}

						if ( ! empty( $ac_lists ) ) {
							$segments_added = array(); // Track which segments have been added.

							foreach ( $ac_lists as $list_id => $list_name ) {
								$_segments[ 'optgroup1_' . $list_id ] = $list_name;

								foreach ( $segments as $segment ) {
									   $id = isset( $segment->id ) ? $segment->id : null;
									   $name = isset( $segment->name ) ? $segment->name : '';
									   $lists = isset( $segment->lists ) ? (array) $segment->lists : array();

									if ( $id ) {
										// Check if this segment is associated with the current list.
										// In v3, we need to be more flexible with list associations.
										if ( ! empty( $lists ) && ( in_array( $list_id, $lists ) || reset( $lists ) == $list_id ) ) {
											$_segments[ $id ] = $name;
											$segments_added[ $id ] = true;
										} else if ( $this->_debug_mode ) {
											$this->debug_log( "Segment {$id} ({$name}) not associated with list {$list_id}" );
										}
									}
								}
							}

							// If no segments were added, add all segments to the first list.
							if ( empty( $segments_added ) && ! empty( $all_segments ) ) {
								$this->debug_log( 'No segments were associated with any lists. Adding all segments to ensure UI display.' );
								foreach ( $all_segments as $id => $name ) {
									$_segments[ $id ] = $name;
								}
							}
						} else {
							foreach ( $segments as $segment ) {
								$id = isset( $segment->id ) ? $segment->id : null;
								$name = isset( $segment->name ) ? $segment->name : '';

								if ( $id ) {
									   $_segments[ $id ] = $name;
								}
							}
							asort( $_segments );
						}
					}

					return array( '_all' => __( 'Everyone', 'newsletter-glue' ) ) + $_segments;
				}
			}

			// If SegmentV3 class is not available, use direct API calls.
			if ( $this->_debug_mode ) {
				$this->debug_log( 'SegmentV3 class not available, using direct API calls' );
			}

			// API v3 uses a different endpoint for segments.
			$response = $api_v3->api( 'segments' );

			// Process the response.
			if ( is_object( $response ) && isset( $response->segments ) ) {
				$segments = $response->segments;

				if ( $this->_debug_mode ) {
					$this->debug_log( 'Retrieved ' . count( $segments ) . ' segments from API v3' );
				}

				// Process segments similar to the original method.
				if ( ! empty( $segments ) ) {
					// Track all segments for fallback.
					$all_segments = array();
					foreach ( $segments as $segment ) {
						$segment_id = isset( $segment->id ) ? $segment->id : null;
						$segment_name = isset( $segment->name ) ? $segment->name : '';
						if ( $segment_id && ! empty( $segment_name ) ) {
							   $all_segments[ $segment_id ] = $segment_name;
						}
					}

					if ( ! empty( $ac_lists ) ) {
						// In v3, we need to get the list associations for each segment.
						$segment_list_map = array();
						$segments_added = array(); // Track which segments have been added.

						// Instead of making an API call for each segment, we'll associate all segments with all lists.
						// This significantly improves performance while ensuring all segments are displayed.
						if ( $this->_debug_mode ) {
							$this->debug_log( 'Optimizing segment-list associations to improve performance' );
						}

						// Create a list of all list IDs.
						$all_list_ids = array_keys( $ac_lists );

						// Associate all segments with all lists.
						foreach ( $segments as $segment ) {
							$segment_id = isset( $segment->id ) ? $segment->id : null;
							if ( $segment_id ) {
								$segment_list_map[ $segment_id ] = $all_list_ids;
							}
						}

						// Organize segments by list.
						foreach ( $ac_lists as $list_id => $list_name ) {
							$_segments[ 'optgroup1_' . $list_id ] = $list_name;

							// Add segments that belong to this list.
							foreach ( $segments as $segment ) {
								$segment_id = isset( $segment->id ) ? $segment->id : null;
								$segment_name = isset( $segment->name ) ? $segment->name : '';

								// In v3, we need to be more flexible with list associations.
								if ( $segment_id ) {
									// If we have list associations and this segment is associated with this list.
									if ( isset( $segment_list_map[ $segment_id ] ) && in_array( $list_id, $segment_list_map[ $segment_id ] ) ) {
										$_segments[ $segment_id ] = $segment_name;
										$segments_added[ $segment_id ] = true;
									} else if ( $this->_debug_mode ) {
										$this->debug_log( "Segment {$segment_id} ({$segment_name}) not associated with list {$list_id} in direct API call" );
									}
								}
							}
						}

						// If no segments were added, add all segments to ensure they appear in the UI.
						if ( empty( $segments_added ) && ! empty( $all_segments ) ) {
							$this->debug_log( 'No segments were associated with any lists in direct API call. Adding all segments to ensure UI display.' );
							foreach ( $all_segments as $id => $name ) {
								$_segments[ $id ] = $name;
							}
						}
					} else {
						// If no lists, just add all segments.
						foreach ( $segments as $segment ) {
							$segment_id = isset( $segment->id ) ? $segment->id : null;
							$segment_name = isset( $segment->name ) ? $segment->name : '';

							if ( $segment_id ) {
								$_segments[ $segment_id ] = $segment_name;
							}
						}
						asort( $_segments );
					}
				}
			}
		} catch ( Exception $e ) {
			if ( $this->_debug_mode ) {
				$this->debug_log( 'Error in get_segments_v3: ' . $e->getMessage(), 'error' );
			}
		}

		// If v3 implementation failed or returned no segments, fall back to v2.
		if ( empty( $_segments ) ) {
			if ( $this->_debug_mode ) {
				$this->debug_log( 'API v3 returned no segments, falling back to original implementation', 'warning' );
			}

			// Temporarily disable v3 for get_segments to avoid infinite loop.
			$temp = $this->_v3_methods['get_segments'] ?? false;
			$this->_v3_methods['get_segments'] = false;

			// Use the original implementation.
			$_segments = $this->get_segments();

			// Restore the original setting.
			$this->_v3_methods['get_segments'] = $temp;
		} else {
			// Cache the results for future use (1 hour cache time).
			$cache_key = 'ngl_activecampaign_segments_v3_' . md5( $this->api_key . $this->api_url );
			$result = array( '_all' => __( 'Everyone', 'newsletter-glue' ) ) + $_segments;
			set_transient( $cache_key, $result, HOUR_IN_SECONDS );

			if ( $this->_debug_mode ) {
				$this->debug_log( 'Cached ' . count( $result ) . ' segments for future use' );
			}
		}

		return array( '_all' => __( 'Everyone', 'newsletter-glue' ) ) + $_segments;
	}

	/**
	 * Returns true if test emails are sent by WordPress.
	 */
	public function test_email_by_wordpress() {
		return true;
	}

	/**
	 * Send newsletter.
	 */
	public function send_newsletter( $post_id = 0, $data = array(), $test = false ) {

		if ( defined( 'NGL_SEND_IN_PROGRESS' ) ) {
			return;
		}

		define( 'NGL_SEND_IN_PROGRESS', 'sending' );

		$post = get_post( $post_id );

		// If no data was provided. Get it from the post.
		if ( empty( $data ) ) {
			$data = get_post_meta( $post_id, '_newsletterglue', true );
		}

		$subject         = isset( $data['subject'] ) ? ngl_safe_title( $data['subject'] ) : ngl_safe_title( $post->post_title );
		$from_name        = isset( $data['from_name'] ) ? $data['from_name'] : newsletterglue_get_default_from_name();
		$from_email        = isset( $data['from_email'] ) ? $data['from_email'] : $this->get_current_user_email();
		$lists            = isset( $data['lists'] ) && ! empty( $data['lists'] ) && is_array( $data['lists'] ) ? $data['lists'] : '';
		$segment        = isset( $data['segments'] ) && ! empty( $data['segments'] ) ? $data['segments'] : '';
		$schedule       = isset( $data['schedule'] ) ? $data['schedule'] : 'immediately';

		$subject = apply_filters( 'newsletterglue_email_subject_line', $subject, $post, $data, $test, $this );

		// Empty content.
		if ( $test && isset( $post->post_status ) && $post->post_status === 'auto-draft' ) {

			$response['fail'] = $this->nothing_to_send();

			return $response;

		}

		// Do test email.
		if ( $test ) {
			$response = array();

			$test_email = $data['test_email'];
			$test_email_arr = explode( ',', $test_email );
			$test_emails = array_map( 'trim', $test_email_arr );
			if ( ! empty( $test_emails ) ) {
				foreach ( $test_emails as $testid ) {
					if ( ! is_email( $testid ) ) {
						$response['fail'] = __( 'Please enter a valid email', 'newsletter-glue' );
					}
				}
			}
			if ( ! empty( $response['fail'] ) ) {
				return $response;
			}

			add_filter( 'wp_mail_content_type', array( $this, 'wp_mail_content_type' ) );

			$body = newsletterglue_generate_content( $post, $subject, $this->app );

         wp_mail( $test_emails, sprintf( __( '[Test] %s', 'newsletter-glue' ), $subject ), $body ); // phpcs:ignore

			$response['success'] = $this->get_test_success_msg();

			return $response;

		}

		$this->api = new ActiveCampaign( $this->api_url, $this->api_key );

		if ( empty( $lists ) ) {
			$thelists = $this->get_lists();
			$lists = array_keys( $thelists );
		}

		$params = array(
			'fromemail'            => $from_email,
			'fromname'            => $from_name,
			'subject'            => $subject,
			'format'            => 'mime',
			'reply2'            => $from_email,
			'htmlconstructor'     => 'editor',
			'html'                => newsletterglue_generate_content( $post, $subject, $this->app ),
			'charset'             => 'utf-8',
			'encoding'            => 'quoted-printable',
		);

		foreach ( $lists as $list_id ) {
			$params[ "p[$list_id]" ] = $list_id;
		}

		$result = $this->api->api( 'message/add', $params );

		// Message ID available.
		if ( isset( $result->id ) ) {
			$message_id = $result->id;
			$args = array(
				'name'            => ngl_safe_title( $post->post_title ),
				'status'        => ( $schedule == 'immediately' ) ? 1 : 0,
				'embed_images'    => 1,
			);
			$args[ "m[$message_id]" ] = 100;
			foreach ( $lists as $list_id ) {
				$args[ "p[$list_id]" ] = $list_id;
			}
			if ( $segment && ! strstr( $segment, 'all' ) ) {
				$args['segmentid'] = $segment;
			}

			$send = $this->api->api( 'campaign/create', $args );

			if ( isset( $send->error ) ) {
				$status = array(
					'status' => 'error',
					'error' => $send->error,
				);
				newsletterglue_add_campaign_data( $post_id, $subject, $this->prepare_message( (array) $status ) );

				return $status;
			}

			// Store the status.
			if ( isset( $send->id ) ) {

				if ( $schedule === 'draft' ) {
					$status = array( 'status' => 'draft' );
				} else {
					$status = array( 'status' => 'sent' );
				}

				newsletterglue_add_campaign_data( $post_id, $subject, $this->prepare_message( (array) $status ), $send->id );

				return $status;
			}
		}
	}

	/**
	 * Prepare result for plugin.
	 */
	public function prepare_message( $result ) {
		$output = array();

		if ( isset( $result['status'] ) ) {

			if ( $result['status'] === 'draft' ) {
				$output['status']        = 200;
				$output['type']        = 'neutral';
				$output['message']    = __( 'Saved as draft', 'newsletter-glue' );
			}

			if ( $result['status'] === 'sent' ) {
				$output['status']     = 200;
				$output['type']     = 'success';
				$output['message']     = __( 'Sent', 'newsletter-glue' );
			}

			if ( $result['status'] === 'error' ) {
				$output['status']     = 400;
				$output['type']         = 'error';
				$output['message']    = $result['error'];
			}
		}

		return $output;
	}

	/**
	 * Add user to this ESP.
	 */
	public function add_user( $data ) {
		extract( $data );

		if ( empty( $email ) ) {
			return -1;
		}

		$fname = '';
		$lname = '';

		if ( isset( $name ) ) {
			$name_array = $array = explode( ' ', $name, 2 );
			$fname = $name_array[0];
			$lname = isset( $name_array[1] ) ? $name_array[1] : '';
		}

		$this->api = new ActiveCampaign( $this->api_url, $this->api_key );

		if ( ! empty( $list_id ) ) {

			$args = array(
				"p[$list_id]"     => $list_id,
				'email'            => $email,
				'first_name'    => $fname,
				'last_name'        => $lname,
			);

			if ( isset( $extra_list ) && ! empty( $extra_list_id ) ) {
				$args[ "p[$extra_list_id]" ] = $extra_list_id;
			}

			$this->api->api( 'contact/add', $args );

		}

		return true;
	}

	/**
	 * Get connect settings.
	 */
	public function get_connect_settings( $integrations = array() ) {

		$app = $this->app;

		newsletterglue_text_field(
			array(
				'id'             => "ngl_{$app}_url",
				'placeholder'     => esc_html__( 'Enter API URL', 'newsletter-glue' ),
				'value'            => isset( $integrations[ $app ]['api_url'] ) ? $integrations[ $app ]['api_url'] : '',
				'class'            => 'ngl-text-margin',
			)
		);

		newsletterglue_text_field(
			array(
				'id'             => "ngl_{$app}_key",
				'placeholder'     => esc_html__( 'Enter API Key', 'newsletter-glue' ),
				'helper'        => '<a href="https://www.activecampaign.com/login" target="_blank" class="ngl-link-inline-svg">' . __( 'Get API key', 'newsletter-glue' ) . ' [externallink]</a>',
				'value'            => isset( $integrations[ $app ]['api_key'] ) ? $integrations[ $app ]['api_key'] : '',
			)
		);
	}

	/**
	 * Customize content.
	 */
	public function newsletterglue_email_content_activecampaign( $content, $post, $subject ) {

		$content = str_replace( '%7B%7B%20unsubscribe_link%20%7D%7D', '{{ unsubscribe_link }}', $content );

		$filter = apply_filters( 'newsletterglue_auto_unsub_link', true, $this->app );

		if ( ! $filter ) {
			return $content;
		}

		if ( strstr( $content, '{{ unsubscribe_link }}' ) ) {
			return $content;
		}

		$post_id        = $post->ID;
		$data             = get_post_meta( $post_id, '_newsletterglue', true );
		$default_unsub  = $this->default_unsub();
		$unsub             = ! empty( $data['unsub'] ) ? $data['unsub'] : $default_unsub;

		if ( empty( $unsub ) ) {
			$unsub = $this->default_unsub();
		}

		$unsub = str_replace( '{{ unsubscribe_link }}', '%UNSUBSCRIBELINK%', $unsub );

		$content .= '<p class="ngl-unsubscribe">' . wp_kses_post( $unsub ) . '</p>';

		return $content;
	}

	/**
	 * Default unsub.
	 */
	public function default_unsub() {
		return '<a href="{{ unsubscribe_link }}">' . __( 'Unsubscribe', 'newsletter-glue' ) . '</a> to stop receiving these emails.';
	}

	/**
	 * Has email verify.
	 */
	public function has_email_verify() {
		return false;
	}

	/**
	 * Get email verify help.
	 */
	public function get_email_verify_help() {
		return 'https://help.activecampaign.com/hc/en-us/articles/360015584680-Verify-your-email-domain';
	}

	/**
	 * Get lists compat.
	 */
	public function _get_lists_compat() {
		$this->api = new ActiveCampaign( $this->api_url, $this->api_key );
		return $this->get_lists();
	}

	/**
	 * Replace universal tags with esp tags.
	 */
	public function html_content( $html, $post_id ) {

		$html = $this->convert_tags( $html, $post_id );

		$html = $this->convert_conditions( $html );

		return $html;
	}

	/**
	 * Code supported tags for this ESP.
	 */
	public function get_tag( $tag, $post_id = 0, $fallback = null ) {

		switch ( $tag ) {
			case 'unsubscribe_link':
				return '%UNSUBSCRIBELINK%';
			break;
			case 'phone':
				return '%PHONE%';
			break;
			case 'list':
				return '%LISTNAME%';
			break;
			case 'full_name':
				return '%FULLNAME%';
			break;
			case 'first_name':
				return '%FIRSTNAME%';
			break;
			case 'last_name':
				return '%LASTNAME%';
			break;
			case 'email':
				return '%EMAIL%';
			break;
			case 'update_preferences':
				return '%UPDATELINK%';
			break;
			case 'admin_address':
				return '%SENDER-INFO-SINGLELINE%';
			break;
			default:
				return apply_filters( "newsletterglue_{$this->app}_custom_tag", '', $tag, $post_id );
			break;
		}

		return false;
	}

	/**
	 * Configure options array for this ESP.
	 */
	public function option_array() {
		return array(
			'lists'     => array(
				'type'        => 'select',
				'callback'    => 'get_lists',
				'title'     => __( 'Lists', 'newsletter-glue' ),
				'help'        => __( 'Who receives your email.', 'newsletter-glue' ),
				'is_multi'    => true,
			),
			'segments'    => array(
				'type'        => 'select',
				'callback'     => 'get_segments',
				'title'        => __( 'Segment', 'newsletter-glue' ),
				'help'        => __( 'A specific group of subscribers.', 'newsletter-glue' ),
			),
		);
	}

	/**
	 * Get custom tags of esp
	 */
	public function get_custom_tags() {
		$_tags = array();

		$this->api = new ActiveCampaign( $this->api_url, $this->api_key );
		$response = $this->api->api( 'tags/list' );

		if ( ! empty( $response ) ) {
			$tags = json_decode( $response );
			foreach ( $tags as $tag ) {
				if ( isset( $tag->name ) && ! empty( $tag->name ) ) {
					$_tags[] = array(
						'value' => $tag->name,
						'label' => $tag->name,
					);
				}
			}
		}

		if ( count( $_tags ) ) {
			array_multisort( array_column( $_tags, 'label' ), SORT_ASC, $_tags );
		}

		return $_tags;
	}

	/**
	 * Get custom fields of esp
	 */
	public function get_custom_fields() {
		// predefined fileds
		$_fields = array(
			array(
				'value' => 'FIRSTNAME',
				'label' => 'FIRSTNAME',
			),
			array(
				'value' => 'LASTNAME',
				'label' => 'LASTNAME',
			),
			array(
				'value' => 'EMAIL',
				'label' => 'EMAIL',
			),
			array(
				'value' => 'PHONE',
				'label' => 'PHONE',
			),
			array(
				'value' => 'CONTACT_JOBTITLE',
				'label' => 'CONTACT_JOBTITLE',
			),
		);

		$this->api = new ActiveCampaign( $this->api_url, $this->api_key );
		$response = $this->api->api( 'list/field/view?ids=all' );
		$response = (array) $response;

		if ( isset( $response['http_code'] )
			&& $response['http_code'] == 200
			&& isset( $response['result_code'] )
			&& $response['result_code'] == 1
		) {
			unset( $response['result_code'] );
			unset( $response['result_message'] );
			unset( $response['result_output'] );
			unset( $response['http_code'] );
			unset( $response['success'] );

			foreach ( $response as $field ) {
				if ( isset( $field->perstag ) && ! empty( $field->perstag ) ) {
					$_fields[] = array(
						'value' => $field->perstag,
						'label' => $field->perstag,
					);
				}
			}
		}

		if ( count( $_fields ) ) {
			array_multisort( array_column( $_fields, 'label' ), SORT_ASC, $_fields );
			array_unshift(
				$_fields,
				array(
					'value' => 'tag',
					'label' => 'Select a tag',
				)
			);
			array_unshift(
				$_fields,
				array(
					'value' => '',
					'label' => 'Select an option',
				)
			);
		}

		return $_fields;
	}

	/**
	 * Convert conditional statements of esp
	 */
	public function convert_conditions( $html ) {
		$output = new simple_html_dom();
		$output->load( $html, true, false );

		$replace = '[data-conditions]';
		foreach ( $output->find( $replace ) as $key => $element ) {

			$conditions = json_decode( $element->{ 'data-conditions' } );
			$element->removeAttribute( 'data-conditions' );

			$condition_query = '';

			foreach ( $conditions as $condition ) {
				$key          = $condition->key;
				$operator     = $condition->operator;
				$value        = $condition->value;
				$relationship = $condition->relationship;

				$condition_query .= $relationship == 'AND' ? ' && ' : ' || ';

				// handle tags
				if ( is_array( $value ) ) {
					foreach ( $value as $tag ) {
						if ( $operator == 'eq' ) {
							$condition_query .= 'in_array("' . $tag . '", $TAGS) && ';
						} else if ( $operator == 'neq' ) {
							$condition_query .= '!in_array("' . $tag . '", $TAGS) && ';
						} else if ( $operator == 'ex' ) {
							$condition_query .= 'in_array("' . $tag . '", $TAGS) || ';
						} else if ( $operator == 'nex' ) {
							$condition_query .= '!in_array("' . $tag . '", $TAGS) || ';
						}
					}

					$condition_query = rtrim( $condition_query, ' && ' );
					$condition_query = rtrim( $condition_query, ' || ' );

				} else {

					// handle custom fields
					if ( $operator == 'eq' ) {
						$condition_query .= '$' . $key . ' == "' . $value . '"';
					} else if ( $operator == 'neq' ) {
						$condition_query .= '$' . $key . ' != "' . $value . '"';
					} else if ( $operator == 'lt' ) {
						$condition_query .= '$' . $key . ' < "' . $value . '"';
					} else if ( $operator == 'gt' ) {
						$condition_query .= '$' . $key . ' > "' . $value . '"';
					} else if ( $operator == 'lte' ) {
									$condition_query .= '$' . $key . ' <= "' . $value . '"';
					} else if ( $operator == 'gte' ) {
							  $condition_query .= '$' . $key . ' >= "' . $value . '"';
					} else if ( $operator == 'con' ) {
						$condition_query .= 'in_string("' . $value . '", $' . $key . ')';
					} else if ( $operator == 'ncon' ) {
						$condition_query .= '!in_string("' . $value . '", $' . $key . ')';
					} else if ( $operator == 'ex' ) {
						$condition_query .= '!empty($' . $key . ')';
					} else if ( $operator == 'nex' ) {
						$condition_query .= 'empty($' . $key . ')';
					}
				}
			}

			$condition_query = ltrim( $condition_query, ' && ' );
			$condition_query = ltrim( $condition_query, ' || ' );

			if ( ! empty( $condition_query ) ) {
				$content = '<!--%IF ' . $condition_query . '%-->';
				$content .= $element->outertext;
				$content .= '<!--%/IF%-->';
				$element->outertext = $content;
			}
		}

		$output->save();

		return (string) $output;
	}
}
