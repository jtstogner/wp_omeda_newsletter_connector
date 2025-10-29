<?php
/**
 * RSS source for the Latest Posts block.
 *
 * This module allows the Latest Posts block to display content from RSS feeds
 * instead of WordPress posts.
 */
class NGL_Latest_Posts_RSS_Source {

	/**
	 * RSS feed URLs to use.
	 *
	 * @var array
	 */
	private $feed_urls = array();

	/**
	 * Custom data storage for RSS items to avoid dynamic properties on SimplePie\Item.
	 *
	 * @var array
	 */
	private $item_custom_data = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Hook into the Latest Posts block filter
		add_filter( 'newsletterglue_latest_posts_results', array( $this, 'filter_posts' ), 10, 3 );

		// Load feed URLs from options
		$this->load_feed_urls();
	}

	/**
	 * Load feed URLs from options.
	 */
	private function load_feed_urls() {
		//$feeds = get_option( 'ngl_rss_feeds', array() );
		//$feeds = array('https://feeds.bbci.co.uk/news/rss.xml?edition=uk');
		$feeds = array('https://www.wired.com/feed');
		if ( ! empty( $feeds ) && is_array( $feeds ) ) {
			$this->feed_urls = $feeds;
		}
	}

	/**
	 * Sanitize feeds.
	 *
	 * @param array $feeds The feeds to sanitize.
	 * @return array
	 */
	public function sanitize_feeds( $feeds ) {
		$sanitized_feeds = array();
		if ( ! empty( $feeds ) && is_array( $feeds ) ) {
			foreach ( $feeds as $id => $feed ) {
				if ( ! empty( $feed['url'] ) ) {
					$sanitized_feeds[$id] = array(
						'name' => sanitize_text_field( $feed['name'] ),
						'url'  => esc_url_raw( $feed['url'] ),
					);
				}
			}
		}
		return $sanitized_feeds;
	}

	/**
	 * Filter posts to include RSS feed items.
	 *
	 * @param array $posts The posts to filter.
	 * @param array $args The query arguments.
	 * @param string $block_key The block key.
	 * @return array
	 */
	public function filter_posts( $posts, $args, $block_key ) {

		if($args['insert_rss_posts'] !== true){
			return $posts;
		}

		if($args['rss_feed'] === ''){
			return $posts;
		}

		$this->feed_urls = array($args['rss_feed']);

		// Check if we have any feeds configured
		if ( empty( $this->feed_urls ) ) {
			return $posts;
		}

        // Reset posts array.
        $posts = array();

		// Get RSS items
		$rss_items = $this->get_rss_items();
		if ( empty( $rss_items ) ) {
			return $posts;
		}

		// Log for debugging
		$log_file = WP_CONTENT_DIR . '/ngl-debug.log';
		file_put_contents($log_file, "RSS Source: Found " . count($rss_items) . " RSS items\n", FILE_APPEND);

		// Apply offset and posts_num limits
		$offset = isset( $args['offset'] ) ? absint( $args['offset'] ) : 0;
		$posts_num = isset( $args['posts_num'] ) ? absint( $args['posts_num'] ) : 10;
		$posts_per_page = isset( $args['posts_per_page'] ) ? absint( $args['posts_per_page'] ) : ( isset( $posts_num ) ? absint( $posts_num ) : 10 );
		$rss_items = array_slice( $rss_items, $offset, $posts_per_page );

		// Convert RSS items to post format
		$rss_posts = $this->convert_rss_to_posts( $rss_items );

		return $rss_posts;
	}

	/**
	 * Get RSS items from all configured feeds.
	 *
	 * @return array
	 */
	private function get_rss_items() {
		$items = array();
		//$cache_duration = get_option( 'ngl_rss_cache_duration', $this->cache_duration );

		foreach ( $this->feed_urls as $feed_id => $feed ) {
			// Check cache first
            /*
			$cache_key = 'ngl_rss_' . md5( $feed['url'] );
			$cached_items = get_transient( $cache_key );

			if ( false !== $cached_items ) {
				$items = array_merge( $items, $cached_items );
				continue;
			}*/

            error_log($feed);

			// Fetch the feed
			$rss = fetch_feed( $feed );

			if ( is_wp_error( $rss ) ) {
				error_log( "Error fetching RSS feed from " . $feed . " with fetch_feed: " . $rss->get_error_message() );
				// Fallback to wp_remote_get with browser-like headers
				$response = wp_remote_get( $feed, array( 
					'timeout' => 30,
					'headers' => array(
						'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64; rv:128.0) Gecko/20100101 Firefox/128.0',
						'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
						'Accept-Language' => 'en-US,en;q=0.5',
						'Accept-Encoding' => 'gzip, deflate, br, zstd',
						'Connection' => 'keep-alive',
						'Upgrade-Insecure-Requests' => '1',
						'Sec-Fetch-Dest' => 'document',
						'Sec-Fetch-Mode' => 'navigate',
						'Sec-Fetch-Site' => 'cross-site',
						'Priority' => 'u=0, i'
					)
				) );
				if ( is_wp_error( $response ) ) {
					error_log( "Fallback error fetching RSS feed from " . $feed . " with wp_remote_get: " . $response->get_error_message() );
					continue;
				}

				// Get the body of the response
				$body = wp_remote_retrieve_body( $response );
				if ( empty( $body ) ) {
					error_log( "Fallback error fetching RSS feed from " . $feed . ": Empty response body" );
					continue;
				}

				// Parse the XML using SimplePie
				$rss = new SimplePie();
				$rss->set_raw_data( $body );
				$rss->init();
				if ( $rss->error() ) {
					error_log( "Fallback error parsing RSS feed from " . $feed . ": " . $rss->error() );
					continue;
				}
				error_log( "Successfully fetched and parsed RSS feed from: " . $feed . " using fallback method" );
			}

			$max_items = 50; // Maximum number of items to retrieve per feed
			$rss_items = $rss->get_items( 0, $max_items );

			if ( ! empty( $rss_items ) ) {
				// Add feed source info to each item
				foreach ( $rss_items as $index => $item ) {
					// Store custom data in array instead of dynamic properties.
					$this->item_custom_data[ spl_object_hash( $item ) ] = array(
						'feed_id'   => $feed_id,
						'feed_name' => $feed,
						'feed_url'  => $feed,
					);

					$items[] = $item;
				}

				// Cache the items
				//set_transient( $cache_key, $rss_items, $cache_duration );
			}
		}

		// Sort by date, newest first
		usort( $items, function( $a, $b ) {
			return $b->get_date( 'U' ) - $a->get_date( 'U' );
		} );

		return $items;
	}

	/**
	 * Convert RSS items to the format expected by the Latest Posts block.
	 *
	 * @param array $rss_items The RSS items to convert.
	 * @return array
	 */
	private function convert_rss_to_posts( $rss_items ) {
		$posts = array();

		foreach ( $rss_items as $item ) {
			// Extract thumbnail URL if available
			$thumbnail_url = $this->get_thumbnail_url( $item );
			// Log the thumbnail URL for debugging
			error_log( "Thumbnail URL for item: " . $item->get_title() . " - " . $thumbnail_url );

			// Get custom data from array.
			$item_hash = spl_object_hash( $item );
			$feed_id   = isset( $this->item_custom_data[ $item_hash ]['feed_id'] ) ? $this->item_custom_data[ $item_hash ]['feed_id'] : '';
			$feed_name = isset( $this->item_custom_data[ $item_hash ]['feed_name'] ) ? $this->item_custom_data[ $item_hash ]['feed_name'] : '';
			$feed_url  = isset( $this->item_custom_data[ $item_hash ]['feed_url'] ) ? $this->item_custom_data[ $item_hash ]['feed_url'] : '';

			// Create a post-like object
			$post = array(
				'id'            => md5( $item->get_permalink() ), // Use MD5 of permalink as unique ID
				'post_title'    => $item->get_title(),
				'post_content'  => $item->get_description(),
				'featured_image' => $thumbnail_url ? $thumbnail_url : NGL_PLUGIN_URL . 'assets/images/placeholder.png',
				'thumbnail_id'  => 0, // No real thumbnail ID for RSS items
				'permalink'     => $item->get_permalink(),
				'domain'        => parse_url( $item->get_permalink(), PHP_URL_HOST ),
				'categories'    => $feed_name, // Use feed name as category
				'tags'          => '', // No tags for RSS items
				'author'        => $item->get_author() ? $item->get_author()->get_name() : '',
				'date'          => $item->get_date( 'Y-m-d H:i:s' ),
				'source'        => 'rss', // Mark as RSS source
				'feed_name'     => $feed_name,
			);

			$posts[] = $post;
		}

		return $posts;
	}

	/**
	 * Extract thumbnail URL from an RSS item.
	 *
	 * @param object $item The RSS item.
	 * @return string|false
	 */
	private function get_thumbnail_url( $item ) {
		// Try to get media:thumbnail
		$thumbnails = $item->get_item_tags( 'http://search.yahoo.com/mrss/', 'thumbnail' );
		if ( ! empty( $thumbnails ) && isset( $thumbnails[0]['attribs']['']['url'] ) ) {
			return $thumbnails[0]['attribs']['']['url'];
		}

		// Try to get media:content with medium="image"
		$media_contents = $item->get_item_tags( 'http://search.yahoo.com/mrss/', 'content' );
		if ( ! empty( $media_contents ) ) {
			error_log( 'Media contents found: ' . print_r( $media_contents, true ) );
			foreach ( $media_contents as $content ) {
				if ( isset( $content['attribs']['']['medium'] ) && $content['attribs']['']['medium'] === 'image' && isset( $content['attribs']['']['url'] ) ) {
					return $content['attribs']['']['url'];
				}
			}
		} else {
			error_log( 'No media contents found for item: ' . $item->get_title() );
			// Try a different approach for nested media:content within media:group
			$media_groups = $item->get_item_tags( 'http://search.yahoo.com/mrss/', 'group' );
			if ( ! empty( $media_groups ) ) {
				error_log( 'Media groups found for item: ' . $item->get_title() );
				foreach ( $media_groups as $group ) {
					if ( isset( $group['child']['http://search.yahoo.com/mrss/']['content'] ) ) {
						$contents = $group['child']['http://search.yahoo.com/mrss/']['content'];
						foreach ( $contents as $content ) {
							if ( isset( $content['attribs']['']['medium'] ) && $content['attribs']['']['medium'] === 'image' && isset( $content['attribs']['']['url'] ) ) {
								error_log( 'Found image in media:group for item: ' . $item->get_title() );
								return $content['attribs']['']['url'];
							}
						}
					}
				}
			} else {
				error_log( 'No media groups found for item: ' . $item->get_title() );
			}
		}

		// Try to get enclosure
		$enclosures = $item->get_enclosures();
		foreach ( $enclosures as $enclosure ) {
			if ( $enclosure->get_type() && strpos( $enclosure->get_type(), 'image' ) === 0 ) {
				return $enclosure->get_link();
			}
		}

		// Try to find an image in the content
		$content = $item->get_content();
		preg_match( '/<img[^>]+src=[\'"](https?:\/\/.+?)[\'"]/i', $content, $matches );
		if ( ! empty( $matches[1] ) ) {
			return $matches[1];
		}

		return false;
	}
}