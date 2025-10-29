<?php
/**
 * Automation.
 *
 * @package Newsletter Glue
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * NGL_Automation class.
 */
class NGL_Automation {

	/**
	 * Used to assign the automation post ID.
	 *
	 * @var $id
	 */
	public $id = 0;

	public $data = '';

	public $exists = '';

	public $settings = '';

	/**
	 * Constructor.
	 *
	 * @param integer $post_id A post id (automation ID).
	 */
	public function __construct( $post_id = 0 ) {

		$this->id = $post_id;

		$settings = newsletterglue_get_data( $this->id );

		$this->data = get_post( $this->id );

		if ( isset( $this->data->ID ) ) {
			$this->exists = true;
		} else {
			$this->exists = false;
		}

		$this->settings = $settings;
	}

	/**
	 * Is valid?
	 */
	public function is_valid() {
		return $this->exists;
	}

	/**
	 * Get post.
	 */
	public function get_post() {
		return $this->data;
	}

	/**
	 * Get settings.
	 */
	public function get_settings() {
		return $this->settings;
	}

	/**
	 * Get status.
	 */
	public function get_post_status() {

		return isset( $this->data ) && isset( $this->data->post_status ) ? $this->data->post_status : '';
	}

	/**
	 * Is enabled?
	 */
	public function is_enabled() {
		$status = get_post_meta( $this->id, '_status', true );

		if ( 'on' === $status ) {
			return true;
		}

		return false;
	}

	/**
	 * Get status of an automation.
	 */
	public function get_status() {

		$status = get_post_meta( $this->id, '_status', true );

		return apply_filters( 'ngl_automation_status', $status, $this->id );
	}

	/**
	 * Disables an automation.
	 */
	public function disable() {

		update_post_meta( $this->id, '_status', 'off' );

		wp_clear_scheduled_hook( 'newsletterglue_trigger_automated_email', array( $this->id ) );
	}

	/**
	 * We will add/substract that from timestamp.
	 */
	public function get_offset() {
		$offset   = get_option( 'gmt_offset' );
		$sign     = $offset <= 0 ? '+' : '-';
		$hours    = 1 == $offset ? 'hour' : 'hours';
		$absmin   = $sign . abs( $offset ) . ' ' . $hours;

		return $absmin;
	}

	/**
	 * Enables an automation.
	 */
	public function enable() {

		update_post_meta( $this->id, '_status', 'on' );

		$schedule = $this->get_schedule();

		$datetime = new DateTime();
		$timezone = wp_timezone();
		$datetime->setTimezone( $timezone );

		$gmt_offset = $this->get_offset();

		if ( ! empty( $schedule['frequency'] ) ) {
			if ( 'daily' == $schedule['frequency'] ) {
				$time = $schedule['time'];
				$day_exceptions = $this->get_day_exceptions();

				// If there are no day exceptions, schedule for tomorrow
				if ( empty( $day_exceptions ) ) {
					$datetime->modify( "tomorrow {$time}" );
				} else {
					// Start with tomorrow
					$datetime->modify( "tomorrow {$time}" );
					
					// Get the day number (1-7, where 1 is Monday and 7 is Sunday)
					$day_num = $datetime->format('N');
					if ($day_num == 7) $day_num = 0; // Convert to 0-6 format where 0 is Sunday
					
					// Check if tomorrow is in the exceptions list
					$days_to_add = 0;
					while ( in_array( (string)$day_num, $day_exceptions ) ) {
						// If it's excluded, add one more day and check again
						$days_to_add++;
						$datetime->modify( "+1 day" );
						$day_num = $datetime->format('N');
						if ($day_num == 7) $day_num = 0; // Convert to 0-6 format
						
						// Safety check to avoid infinite loop (maximum 7 days)
						if ( $days_to_add >= 7 ) {
							break;
						}
					}
					
					// Reset the time since modify might have changed it
					$datetime->modify( $time );
				}
				
				$next_run_date = $datetime->format( 'Y-m-d H:i:s' );
				$next_run = strtotime( $gmt_offset, strtotime( $next_run_date ) );
				update_post_meta( $this->id, '_next_run', $next_run_date );
			}

			if ( 'weekly' == $schedule['frequency'] ) {
				$day            = $this->get_day( $schedule['day'] );
				$time           = $schedule['time'];
				$datetime->modify( "next {$day} {$time}" );
				$next_run_date  = $datetime->format( 'Y-m-d H:i:s' );
				$next_run       = strtotime( $gmt_offset, strtotime( $next_run_date ) );
				update_post_meta( $this->id, '_next_run', $next_run_date );
			}

			if ( 'two_mins' == $schedule['frequency'] ) {
				$datetime->modify( '+2 minutes' );
				$next_run_date  = $datetime->format( 'Y-m-d H:i:s' );
				$next_run       = time() + 120;
				update_post_meta( $this->id, '_next_run', $next_run_date );
			}

			if ( 'two_weeks' == $schedule['frequency'] ) {
				$day            = $this->get_day( $schedule['day'] );
				$time           = $schedule['time'];
				$datetime->modify( "next {$day} + 1 week {$time}" );
				$next_run_date  = $datetime->format( 'Y-m-d H:i:s' );
				$next_run       = strtotime( $gmt_offset, strtotime( $next_run_date ) );
				update_post_meta( $this->id, '_next_run', $next_run_date );
			}

			if ( 'monthly' == $schedule['frequency'] ) {
				$day            = $this->get_day( $schedule['day'] );
				$time           = $schedule['time'];
				if ( 7 == $day ) {
					$day = 'day';
				}
				$datetime->modify( "first {$day} of next month {$time}" );
				$next_run_date  = $datetime->format( 'Y-m-d H:i:s' );
				$next_run       = strtotime( $gmt_offset, strtotime( $next_run_date ) );
				update_post_meta( $this->id, '_next_run', $next_run_date );
			}
		}

		wp_clear_scheduled_hook( 'newsletterglue_trigger_automated_email', array( $this->id ) );

		if ( ! empty( $next_run ) ) {
			if ( get_option( 'ng_schedule_' . $next_run ) ) {
				$next_run = $next_run + 60;
			}
			wp_schedule_single_event( $next_run, 'newsletterglue_trigger_automated_email', array( $this->id ) );
			update_option( 'ng_schedule_' . $next_run, $next_run );
		}
	}

	/**
	 * Checks if automation is scheduled.
	 */
	public function is_scheduled() {

		$schedule = get_post_meta( $this->id, '_schedule', true );

		return ! empty( $schedule ) ? true : false;
	}

	/**
	 * Get schedule.
	 */
	public function get_schedule() {
		return get_post_meta( $this->id, '_schedule', true );
	}

	/**
	 * Get day exceptions.
	 * 
	 * @return array Array of days to exclude from automation.
	 */
	public function get_day_exceptions() {
		$schedule = $this->get_schedule();
		return isset( $schedule['day_exception'] ) ? (array) $schedule['day_exception'] : array();
	}

	/**
	 * Set schedule.
	 *
	 * @param array $schedule An array containing the schedule info.
	 */
	public function set_schedule( $schedule = array() ) {

		if ( ! empty( $schedule ) ) {
			update_post_meta( $this->id, '_schedule', $schedule );
		}
	}

	public function newsletterglue_append_s( $day ) {
		return $day . 's';
	}

	/**
	 * Get schedule text.
	 */
	public function get_schedule_text() {
		global $wp_locale;

		$schedule = get_post_meta( $this->id, '_schedule', true );

		if ( ! empty( $schedule ) ) {

			$frequency = $schedule['frequency'];
			$day       = $schedule['day'];
			$time      = $schedule['time'];

			if ( 'daily' === $frequency ) {
				$time     = str_replace( 'am', ':00 AM', $time );
				$time     = str_replace( 'pm', ':00 PM', $time );
				$details  = sprintf( __( 'day at %s', 'newsletter-glue' ), $time );
			}
			if ( ! empty( $this->get_day_exceptions() ) ) {
				$days = array_map( array( $this, 'get_day' ), $this->get_day_exceptions() );
				$days = array_map( array( $this, 'newsletterglue_append_s' ), $days );

				$details .= sprintf( __( '&nbsp;<span style="color:#ff7800;font-weight:bold;">except</span> %s', 'newsletter-glue' ), implode( ', ', $days ) );
			}

			if ( 'weekly' === $frequency ) {
				$parent   = $wp_locale->get_weekday( $day );
				$time     = str_replace( 'am', ':00 AM', $time );
				$time     = str_replace( 'pm', ':00 PM', $time );
				$details  = sprintf( __( '%1$s, %2$s', 'newsletter-glue' ), $parent, $time );
			}

			if ( 'two_mins' === $frequency ) {
				$details = __( 'every two minutes', 'newsletter-glue' );
			}

			if ( 'two_weeks' === $frequency ) {
				$parent   = $wp_locale->get_weekday( $day );
				$time     = str_replace( 'am', ':00 AM', $time );
				$time     = str_replace( 'pm', ':00 PM', $time );
				$details  = sprintf( __( 'two weeks on %1$s, %2$s', 'newsletter-glue' ), $parent, $time );
			}

			if ( 'monthly' === $frequency ) {
				if ( 7 == $day ) {
					$parent = false;
				} else {
					$parent = $wp_locale->get_weekday( $day );
				}
				$time = str_replace( 'am', ':00 AM', $time );
				$time = str_replace( 'pm', ':00 PM', $time );
				if ( $parent ) {
					$details = sprintf( __( 'month on the first %1$s, %2$s', 'newsletter-glue' ), $parent, $time );
				} else {
					$details = sprintf( __( 'first day of the month %s', 'newsletter-glue' ), $time );
				}
			}

			$text = empty( $this->get_send_type() ) || $this->get_send_type() === 'draft' ? '<span class="ngl-muted">' . __( 'Creates drafts every', 'newsletter-glue' ) . '</span>' : '<span class="ngl-muted">' . __( 'Sends every', 'newsletter-glue' ) . '</span>';
			$text .= '<span class="ngl-regular"> ';
			$text .= $details;
			$text .= '</span><br />';

			$is_hidden = $this->is_enabled() ? '' : 'style="display: none;"';

			$text .= '<span class="ngl-next-run" ' . $is_hidden . ' data-id="' . absint( $this->id ) . '"><span class="ngl-muted">' . __( 'Next run:', 'newsletter-glue' ) . '</span>';
			$text .= '<span class="ngl-regular"> ';
			$text .= get_post_meta( $this->id, '_next_run', true );
			$text .= '</span></span>';

			return $text;
		}

		return null;
	}

	/**
	 * Get timestamp from date.
	 *
	 * @param mixed $date A valid date.
	 */
	public function get_timestamp_from_date( $date ) {

		$datetime = new DateTime( $date );

		return $datetime->getTimestamp();
	}

	/**
	 * Get an automation log.
	 */
	public function get_logs() {

		$args = array(
			'meta_query'      => array( // phpcs:ignore
				array(
					'key'    => '_automation_id',
					'value'  => $this->id,
				),
			),
			'post_type'       => 'ngl_log',
			'posts_per_page'  => -1,
		);

		$posts = get_posts( $args );

		return ! empty( $posts ) ? $posts : array();
	}

	/**
	 * Get schedule options.
	 */
	public function get_schedule_options() {

		$options = array(
			'daily'     => __( 'Daily', 'newsletter-glue' ),
			'weekly'    => __( 'Weekly', 'newsletter-glue' ),
			'two_weeks' => __( 'Every 2 weeks', 'newsletter-glue' ),
			'monthly'   => __( 'Monthly', 'newsletter-glue' ),
			'two_mins'  => __( '2 Minutes (test)', 'newsletter-glue' ),
		);

		return $options;
	}

	/**
	 * Get sending types.
	 */
	public function get_send_types() {
		$options = array(
			'send'    => __( 'Send automatically.', 'newsletter-glue' ),
			'draft'   => __( 'Create campaign drafts. This lets you edit, test and add a custom introduction before sending.', 'newsletter-glue' ),
		);

		return $options;
	}

	/**
	 * Get week days.
	 *
	 * @param boolean $include_day Whether to include day or not.
	 */
	public function get_weekdays( $include_day = false ) {
		global $wp_locale;

		$days = array();

		if ( $include_day ) {
			$days[7] = __( 'Day of the month', 'newsletter-glue' );
		}

		for ( $day_index = 0; $day_index <= 6; $day_index++ ) :
			$days[ $day_index ] = $wp_locale->get_weekday( $day_index );
		endfor;

		return $days;
	}

	/**
	 * Get day name.
	 *
	 * @param integer $index A numeric index for the day.
	 */
	public function get_day( $index ) {
		global $wp_locale;

		if ( $index <= 6 ) {
			return $wp_locale->get_weekday( $index );
		}

		if ( 7 == $index ) {
			return 'day';
		}
	}

	/**
	 * Get times.
	 */
	public function get_times() {

		$array = array(
			'12pm'      => '12:00 PM',
			'1pm'       => '01:00 PM',
			'2pm'       => '02:00 PM',
			'3pm'       => '03:00 PM',
			'4pm'       => '04:00 PM',
			'5pm'       => '05:00 PM',
			'6pm'       => '06:00 PM',
			'7pm'       => '07:00 PM',
			'8pm'       => '08:00 PM',
			'9pm'       => '09:00 PM',
			'10pm'      => '10:00 PM',
			'11pm'      => '11:00 PM',
			'12am'      => '12:00 AM',
			'1am'       => '01:00 AM',
			'2am'       => '02:00 AM',
			'3am'       => '03:00 AM',
			'4am'       => '04:00 AM',
			'5am'       => '05:00 AM',
			'6am'       => '06:00 AM',
			'7am'       => '07:00 AM',
			'8am'       => '08:00 AM',
			'9am'       => '09:00 AM',
			'10am'      => '10:00 AM',
			'11am'      => '11:00 AM',
		);

		return $array;
	}

	/**
	 * Get send type.
	 */
	public function get_send_type() {
		$settings = $this->get_settings();

		if ( isset( $settings->send_type ) ) {
			return $settings->send_type;
		}

		return null;
	}

	/**
	 * Creates a newsletter in WP side.
	 */
	public function create_wp_newsletter() {
		global $wpdb;

		$log_file = WP_CONTENT_DIR . '/ngl-automation-create-wp-newsletter-debug.log';

		$post    = $this->get_post();
		$content = $post->post_content;
		$run_count = 0;
		$first_post_title = '';

		file_put_contents( $log_file, 'Post ID: ' . $post->ID . PHP_EOL, FILE_APPEND );
		file_put_contents( $log_file, 'Post Content: ' . $content . PHP_EOL, FILE_APPEND );

		// Check if content has the post embed block first.
		if ( strstr( $content, '<!-- wp:newsletterglue/latest-posts' ) ) {
			preg_match_all( '<!-- wp:newsletterglue/latest-posts (.*?) /-->', $content, $blocks );
			if ( ! empty( $blocks[1] ) ) {
				$found = $blocks[1];
				foreach ( $found as $key => $data ) {
					if ( strstr( $data, '"posts":[' ) ) {
						$run_count++;
						file_put_contents( $log_file, 'Run Count: ' . $run_count . PHP_EOL, FILE_APPEND );
						$split   = explode( '"posts":[', $data );
						$split2  = explode( ']', $split[1] );
						$content = str_replace( '[' . $split2[0] . ']', 'null', $content );
						file_put_contents( $log_file, 'Content after latest posts: ' . $content . PHP_EOL, FILE_APPEND );
						$class   = new NGL_Render_Latest_Posts();
						$props   = json_decode( $data, true );
						file_put_contents( $log_file, 'Props: ' . $props . PHP_EOL, FILE_APPEND );
						unset( $props['posts'], $props['custom_data'] );
						// Ensure RSS attributes are included
						$props['insert_rss_posts'] = isset( $props['insertRssPosts'] ) ? $props['insertRssPosts'] : false;
						$props['rss_feed'] = isset( $props['rssfeed'] ) ? $props['rssfeed'] : '';
						// Log all properties for debugging
						file_put_contents( $log_file, 'All Props before setup_attrs: ' . print_r( $props, true ) . PHP_EOL, FILE_APPEND );
						$args    = $class->setup_attrs( $props );
						file_put_contents( $log_file, 'Args after setup_attrs: ' . print_r( $args, true ) . PHP_EOL, FILE_APPEND );
						$posts   = $class->get_posts( $args );
						file_put_contents( $log_file, 'Posts: ' . print_r( $posts, true ) . PHP_EOL, FILE_APPEND );
						
						// For full content (words=0), we need to handle newlines properly
						if (isset($args['words_num']) && $args['words_num'] == 0) {
							foreach ($posts as &$post_item) {
								if (isset($post_item['post_content'])) {
									// Remove literal newlines that might be rendered as 'n' in emails
									$post_item['post_content'] = str_replace("\n", "", $post_item['post_content']);
									// Also handle any escaped newlines
									$post_item['post_content'] = str_replace("\\n", "", $post_item['post_content']);
									// Log the cleaned content
									file_put_contents( $log_file, 'Cleaned post content: ' . substr($post_item['post_content'], 0, 100) . '...' . PHP_EOL, FILE_APPEND );
								}
							}
							unset($post_item); // Break the reference
						}
						
						$clean_content = stripslashes( wp_json_encode( $posts, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_QUOT ) );
					
					if($args['words_num'] > 0) {
						// For excerpt mode, strip all tags
						$clean_content = wp_strip_all_tags( $clean_content );
					} else {
						// For full content mode (words_num = 0), preserve HTML but remove Gutenberg comments
						// First, decode the JSON to work with the actual content
						$decoded_content = json_decode($clean_content, true);
						
						if (is_array($decoded_content)) {
							foreach ($decoded_content as &$post_item) {
								if (isset($post_item['post_content'])) {
									// Remove Gutenberg comment blocks while preserving HTML
									$post_item['post_content'] = preg_replace('/<!--\s*wp:.*?-->/', '', $post_item['post_content']);
									$post_item['post_content'] = preg_replace('/<!--\s*\/wp:.*?-->/', '', $post_item['post_content']);
									
									// Log the content after removing Gutenberg comments
									file_put_contents( $log_file, 'Content after removing Gutenberg comments: ' . substr($post_item['post_content'], 0, 100) . '...' . PHP_EOL, FILE_APPEND );
								}
							}
							unset($post_item); // Break the reference
							
							// Re-encode to JSON with HTML preserved
							$clean_content = wp_json_encode($decoded_content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_QUOT | JSON_HEX_TAG);
							
							// Don't strip HTML tags from the content
							// Just clean up any JSON-specific characters that might cause issues
							$clean_content = str_replace('\"', '"', $clean_content);
							$clean_content = str_replace('\\', '\\\\', $clean_content);
						}
					}

					file_put_contents( $log_file, 'Clean Content: ' . $clean_content . PHP_EOL, FILE_APPEND );
					$content = str_replace( '"posts":null', '"posts":' . $clean_content, $content );
					file_put_contents( $log_file, 'Content after latest posts: ' . $content . PHP_EOL, FILE_APPEND );

					}
					if ($run_count == 1) {
						file_put_contents( $log_file, 'Run Count inside condition: ' . $run_count . PHP_EOL, FILE_APPEND );
						file_put_contents( $log_file, 'Posts inside condition: ' . print_r( $posts, true ) . PHP_EOL, FILE_APPEND );
						$first_post_title = wp_kses_post( $posts[0]['post_title'] );
					}
				}
			}
		}

		$send_type = $this->get_send_type();

		file_put_contents( $log_file, 'Send Type: ' . $send_type . PHP_EOL, FILE_APPEND );

		$content = addslashes( $content );

		file_put_contents( $log_file, 'Content after addslashes: ' . $content . PHP_EOL, FILE_APPEND );

		$args = array(
			'comment_status' => $post->comment_status,
			'ping_status'    => $post->ping_status,
			'post_author'    => $post->post_author,
			'post_content'   => $content,
			'post_excerpt'   => $post->post_excerpt,
			'post_name'      => $post->post_name,
			'post_parent'    => $post->post_parent,
			'post_password'  => $post->post_password,
			'post_status'    => 'send' === $send_type ? 'publish' : 'draft',
			'post_title'     => $post->post_title,
			'post_type'      => 'newsletterglue',
			'to_ping'        => $post->to_ping,
			'menu_order'     => $post->menu_order,
		);

		file_put_contents( $log_file, 'Args: ' . $args . PHP_EOL, FILE_APPEND );

		/*
		 * insert the post
		 */
		$new_post_id = wp_insert_post( $args );
		file_put_contents( $log_file, 'New Post ID: ' . $new_post_id . PHP_EOL, FILE_APPEND );

		$new_post = get_post( $new_post_id );
		file_put_contents( $log_file, 'New Post: ' . print_r( $new_post, true ) . PHP_EOL, FILE_APPEND );

		/*
		 * get all current post terms ad set them to the new post draft
		 */
		$taxonomies = get_object_taxonomies( $post->post_type );
		foreach ( $taxonomies as $taxonomy ) {
			$post_terms = wp_get_object_terms( $post->ID, $taxonomy, array( 'fields' => 'slugs' ) );
			wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
		}

		/*
		 * duplicate all post meta just in two SQL queries
		 */
		$post_meta_infos = get_post_meta( $post->ID );
		if ( ! empty( $post_meta_infos ) ) {
			foreach ( $post_meta_infos as $old_key => $old_value ) {
				update_post_meta( $new_post_id, $old_key, get_post_meta( $post->ID, $old_key, true ) );
			}
		}

		if ( ! empty( $first_post_title ) ) {
			update_post_meta( $new_post_id, 'newsletterglue_latest_post_title_inside', $first_post_title );
		}

		// Unsend the cloned newsletter.
		$meta = get_post_meta( $new_post_id, '_newsletterglue', true );
		if ( ! empty( $meta ) && isset( $meta['sent'] ) ) {
			unset( $meta['sent'] );
			update_post_meta( $new_post_id, '_newsletterglue', $meta );
		}

		delete_post_meta( $new_post_id, '_ngl_results' );
		delete_post_meta( $new_post_id, '_ngl_core_pattern' );
		delete_post_meta( $new_post_id, '_ngl_core_template' );

		return $new_post_id;
	}

	/**
	 * Send campaign.
	 *
	 * @param integer $campaign_id A campaign ID.
	 */
	public function send_campaign( $campaign_id = 0 ) {

		if ( ! $campaign_id ) {
			return;
		}

		$result = newsletterglue_send( $campaign_id );

		return $result;
	}

	/**
	 * Remove a post without sending to trash.
	 *
	 * @param integer $post_id A post ID to remove.
	 */
	public function remove_wp_post( $post_id = 0 ) {
		wp_delete_post( $post_id, true );
	}

	/**
	 * Create a log.
	 *
	 * @param integer $campaign_id A campaign Id.
	 * @param array   $result A result to store in the log.
	 */
	public function create_log( $campaign_id = 0, $result = null ) {

		if ( ! $campaign_id ) {
			return;
		}

		update_post_meta( $this->id, '_last_result', $result );

		$post = get_post( $campaign_id );

		$settings = newsletterglue_get_data( $campaign_id );

		$args = array(
			'post_author'    => $post->post_author,
			'post_status'    => 'publish',
			'post_title'     => isset( $settings->subject ) ? $settings->subject : $post->post_title,
			'post_type'      => 'ngl_log',
			'post_content'   => $post->post_content,
		);

		/*
		 * insert the post
		 */
		$log_id = wp_insert_post( $args );

		update_post_meta( $log_id, '_result', $result );
		update_post_meta( $log_id, '_automation_id', $this->id );
		update_post_meta( $log_id, '_campaign_id', $campaign_id );
	}
}
