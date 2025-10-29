<?php

/**
 * Broadstreet integration for Newsletter Glue
 *
 * @package Newsletter Glue
 */

require_once NGL_PLUGIN_DIR . 'includes/ad-inserter/integrations/api/broadstreet.php';

class NGL_Broadstreet_Integration extends NGL_Ad_Integration
{

    private $access_token;
    private $broadstreet_instance;

    public function __construct()
    {
        $this->id = 'broadstreet';
        $this->name = __('Broadstreet', 'newsletter-glue');
        parent::__construct();

        add_action('rest_api_init', array($this, 'register_endpoints'));

        $this->access_token = get_option( 'ngl_broadstreet_access_token', '' );
        $this->broadstreet_instance = new NGL_Broadstreet_API_Integration($this->access_token);
        
        // Only initialize the connection if we have a valid access token
        if (!empty($this->access_token)) {
            $this->broadstreet_instance->initialize_connection();
        }

        $this->set_forbidden_ad_types();

        add_action('save_post', array($this, 'refresh_ad_zone_data'), 10, 3);
    }

    public function set_forbidden_ad_types()
    {
        define(
            'FORBIDDEN_AD_TYPES',
            array(
                'sneaker',
                'video',
                'vimeo-ad',
                'instant-instagram',
                'healthcare-featured-articles-14-st'
            )
        );
    }

    public function register_endpoints() {
        $this->test_connection_api_endpoint();
        $this->save_access_token_api_endpoint();
        $this->get_network_name_endpoint();
        $this->verify_connection_endpoint();
        $this->remove_connection_endpoint();
        $this->get_ad_zones_endpoint();
        $this->get_ad_zone_advertisements_endpoint();
        $this->save_ad_zone_options_endpoint();
        $this->get_ad_zone_options_endpoint();
    }

    public function test_connection_api_endpoint() {
        register_rest_route('newsletter-glue/v1', '/broadstreet/test-connection', array(
            'methods' => 'POST',
            'callback' => array($this->broadstreet_instance, 'test_connection'),
            'permission_callback' => '__return_true',
        ));
    }
    
    public function save_access_token_api_endpoint() {
        register_rest_route('newsletter-glue/v1', '/broadstreet/save-access-token', array(
            'methods' => 'POST',
            'callback' => function($request) {
                $access_token = sanitize_text_field($request['access_token']);
                update_option('ngl_broadstreet_access_token', $access_token);
                
                // Update the instance with the new token
                $this->access_token = $access_token;
                $this->broadstreet_instance = new NGL_Broadstreet_API_Integration($access_token);
                
                return array(
                    'success' => true,
                    'message' => __('Access token saved successfully', 'newsletter-glue')
                );
            },
            'permission_callback' => '__return_true',
        ));
    }

    public function get_ad_zones_endpoint() {
        register_rest_route('newsletter-glue/v1', '/broadstreet/get-ad-zones', array(
            'methods' => 'POST',
            'callback' => array($this->broadstreet_instance, 'get_ad_zones'),
            'permission_callback' => '__return_true',
        ));
    }

    public function get_ad_zone_advertisements_endpoint() {
        register_rest_route('newsletter-glue/v1', '/broadstreet/get-ad-zone-advertisements', array( 
            'methods' => 'POST',
            'callback' => array($this->broadstreet_instance, 'get_ad_zone_rss'),
            'permission_callback' => '__return_true',
        ));
    }

    public function get_network_name_endpoint() {
        register_rest_route('newsletter-glue/v1', '/broadstreet/get-network-name', array(
            'methods' => 'GET',
            'callback' => array($this->broadstreet_instance, 'get_network_name'),
            'permission_callback' => '__return_true',
        ));
    }
    
    public function verify_connection_endpoint() {
        register_rest_route('newsletter-glue/v1', '/broadstreet/verify-connection', array(
            'methods' => 'GET',
            'callback' => array($this->broadstreet_instance, 'verify_connection'),
            'permission_callback' => '__return_true',
        ));
    }
    
    public function remove_connection_endpoint() {
        register_rest_route('newsletter-glue/v1', '/broadstreet/remove-connection', array(
            'methods' => 'POST',
            'callback' => function() {
                // Remove both the connection status and access token from the database
                update_option('ngl_broadstreet_has_connection', false);
                update_option('ngl_broadstreet_access_token', '');
                
                return array(
                    'success' => true,
                    'message' => __('Connection removed successfully', 'newsletter-glue')
                );
            },
            'permission_callback' => '__return_true',
        ));
    }

    public function save_ad_zone_options_endpoint() {
        register_rest_route('newsletter-glue/v1', '/broadstreet/save-ad-zone-options', array(
            'methods' => 'POST',
            'callback' => function($request) {
                $params = $request->get_params();
                
                // Get post ID and zone ID for unique identification
                $post_id = isset($params['post_id']) ? intval($params['post_id']) : 0;
                $zone_id = isset($params['zone_id']) ? sanitize_text_field($params['zone_id']) : '';
                
                if (!$post_id || !$zone_id) {
                    return new WP_Error('missing_identifiers', 'Post ID and zone ID are required', array('status' => 400));
                }
                
                // Create unique option keys for this specific post and zone
                $option_prefix = "ngl_ad_zone_{$post_id}_{$zone_id}_";
                
                // Sanitize and save the zone name
                if (isset($params['zone_name'])) {
                    $zone_name = sanitize_text_field($params['zone_name']);
                    update_option($option_prefix . 'zone_name', $zone_name);
                }
                
                // Sanitize and save the advertisements array
                if (isset($params['advertisements'])) {
                    $advertisements = json_decode($params['advertisements'], true);
                    if (is_array($advertisements)) {
                        update_option($option_prefix . 'advertisements', $advertisements);
                    }
                }
                
                return array(
                    'success' => true,
                    'message' => __('Ad zone options saved successfully', 'newsletter-glue'),
                    'option_prefix' => $option_prefix
                );
            },
            'permission_callback' => function() {
                return current_user_can('edit_posts');
            },
        ));
    }
    
    public function get_ad_zone_options_endpoint() {
        register_rest_route('newsletter-glue/v1', '/broadstreet/get-ad-zone-options', array(
            'methods' => 'GET',
            'callback' => function($request) {
                $params = $request->get_params();
                
                // Get post ID and zone ID for unique identification
                $post_id = isset($params['post_id']) ? intval($params['post_id']) : 0;
                $zone_id = isset($params['zone_id']) ? sanitize_text_field($params['zone_id']) : '';
                
                if (!$post_id || !$zone_id) {
                    return new WP_Error('missing_identifiers', 'Post ID and zone ID are required', array('status' => 400));
                }
                
                // Create unique option keys for this specific post and zone
                $option_prefix = "ngl_ad_zone_{$post_id}_{$zone_id}_";
                
                return array(
                    'zone_id' => $zone_id,
                    'zone_name' => get_option($option_prefix . 'zone_name', ''),
                    'advertisements' => get_option($option_prefix . 'advertisements', array())
                );
            },
            'permission_callback' => function() {
                return current_user_can('edit_posts');
            },
        ));
    }

    protected function is_relevant_post_type($post_type)
    {
        return false; // Not relevant to any post type. Broadstreet is an external integration.
    }

    /**
     * Get available ads
     */
    public function get_ads($args = array())
    {
        $ads = $this->broadstreet_instance->get_advertisements();
        
        // Check if we have cached ad types
        $cached_ad_types = get_transient('ngl_broadstreet_ad_types');
        
        if (false === $cached_ad_types) {
            $cached_ad_types = array();
        }
        
        $ads = $this->format_ad_data($ads, $cached_ad_types);
        
        return $ads;
    }

    public function get_single_ad($id)
    {
        $ad = $this->broadstreet_instance->get_single_advertisement_by_id($id);
        return $ad;
    }


    /**
     * Get ad HTML
     */
    public function get_ad_html($id)
    {
        $html = file_get_contents('https://ad.broadstreetads.com/display/' . $id . '.html');

        // Initialize ad_type variable
        $ad_type = '';

        // Use libxml error handling to suppress warnings
        libxml_use_internal_errors(true);

        $dom = new DOMDocument();
        $dom->loadHTML($html);

        // Clear any errors that occurred during parsing
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $ad_type_elements = $xpath->query('//*[contains(@class, "bsa-ad-type-")]');

        if ($ad_type_elements->length > 0) {
            $ad_type = $ad_type_elements->item(0)->getAttribute('class');
            $ad_type = preg_replace('/^.*bsa-ad-type-(.*)$/', '$1', $ad_type);
        }

        return array('html' => $html, 'type' => $ad_type);
    }


    /**
     * Format ad data
     */
    protected function format_ad_data($post, $cached_ad_types = array())
    {
        $ads = array();
        $types_to_cache = array();
        $need_cache_update = false;
        
        foreach ($post['advertisements'] as $ad) {
            $ad_type = '';
            
            // Check if we have the ad type in cache
            if (isset($cached_ad_types[$ad['id']])) {
                $ad_type = $cached_ad_types[$ad['id']];
            } else {
                // Only fetch the HTML if we don't have the type cached
                $ad_info = $this->get_ad_html($ad['id']);
                $ad_type = isset($ad_info['type']) ? $ad_info['type'] : '';
                
                // Save for caching later
                $types_to_cache[$ad['id']] = $ad_type;
                $need_cache_update = true;
            }
            
            // Skip this ad if its type is in the forbidden list
            if ($ad_type && defined('FORBIDDEN_AD_TYPES') && in_array($ad_type, FORBIDDEN_AD_TYPES)) {
                continue;
            }
            
            $ad_data = array(
                'id' => 'adv-' . $ad['id'],
                'title' => $ad['name'],
                'url' => 'https://ad.broadstreetads.com/click/' . $ad['id'],
                'adImage' => 'https://ad.broadstreetads.com/display/' . $ad['id'],
                'category' => '',
                'placement' => '',
                'group' => 'broadstreet'
            );
            $ads[] = $ad_data;
        }
        
        // Update the cache with any new ad types we found
        if ($need_cache_update) {
            $updated_cache = array_merge($cached_ad_types, $types_to_cache);
            set_transient('ngl_broadstreet_ad_types', $updated_cache, DAY_IN_SECONDS * 7); // Cache for 7 days
        }

        return $ads;
    }

    /**
     * Process blocks recursively to find ad inserter blocks
     * 
     * @param array $blocks Array of blocks
     * @param int $post_id The post ID
     */
    private function process_blocks_recursively($blocks, $post_id) {
        if (!is_array($blocks)) return;
        
        foreach ($blocks as $block) {
            // Check if this is an ad inserter block
            if ($block['blockName'] === 'newsletterglue/ad-inserter') {
                // Extract all necessary data before refreshing
                $attrs = isset($block['attrs']) ? $block['attrs'] : array();
                
                // Try to get zone ID from attributes
                $zone_id = isset($attrs['adZoneId']) ? $attrs['adZoneId'] : '';
                
                // If no zone ID in attributes, try to extract it from innerHTML
                if (!$zone_id && isset($block['innerHTML'])) {
                    // Look for adZoneId in any data attributes
                    if (preg_match('/data-[^=]*="[^"]*adZoneId[^"]*:([^,"]+)/', $block['innerHTML'], $matches)) {
                        $zone_id = trim($matches[1], '"\'');
                        //error_log("[NGL Ad Inserter] Extracted zone ID from block content: {$zone_id}");
                    }
                    
                    // If we still don't have a zone ID, check if it's in a script tag
                    if (empty($zone_id) && preg_match('/<script[^>]*>(.*?)<\/script>/s', $block['innerHTML'], $script_matches)) {
                        $script_content = $script_matches[1];
                        if (preg_match('/adZoneId["\']?\s*:\s*["\']?([^"\',}]+)/', $script_content, $zone_matches)) {
                            $zone_id = trim($zone_matches[1], '"\'');
                            //error_log("[NGL Ad Inserter] Extracted zone ID from script: {$zone_id}");
                        }
                    }
                }
                
                // Only proceed if we found a zone ID
                if ($zone_id) {
                    // Add zone ID to attributes if not already there
                    if (!isset($block['attrs']['adZoneId'])) {
                        $block['attrs']['adZoneId'] = $zone_id;
                    }
                    
                    $this->refresh_block_ad_zone($block, $post_id);
                } else {
                    //error_log("[NGL Ad Inserter] No zone ID found for block in post {$post_id}");
                }
            }
            
            // Process inner blocks if any
            if (!empty($block['innerBlocks'])) {
                $this->process_blocks_recursively($block['innerBlocks'], $post_id);
            }
        }
    }
    
    /**
     * Refresh ad zone data for a specific block
     * 
     * @param array $block The block data
     * @param int $post_id The post ID
     */
    private function refresh_block_ad_zone($block, $post_id) {
        // Get block attributes
        $attrs = isset($block['attrs']) ? $block['attrs'] : array();
        
        // Try to get adZoneId from attributes
        $zone_id = isset($attrs['adZoneId']) ? $attrs['adZoneId'] : '';
        $zone_name = isset($attrs['adZoneName']) ? $attrs['adZoneName'] : '';
        
        // Skip if no zone ID
        if (empty($zone_id)) {
            //error_log("[NGL Ad Inserter] Skipping block - missing zone ID");
            return;
        }
        
        //error_log("[NGL Ad Inserter] Refreshing zone {$zone_id} in post {$post_id}");
        
        // Create unique option keys for this specific post and zone
        $option_prefix = "ngl_ad_zone_{$post_id}_{$zone_id}_";
        
        // Save the zone name
        update_option($option_prefix . 'zone_name', $zone_name);
        
        // Make sure we have a Broadstreet instance
        if (!$this->broadstreet_instance) {
            $this->access_token = get_option('ngl_broadstreet_access_token', '');
            if ($this->access_token) {
                $this->broadstreet_instance = new NGL_Broadstreet_API_Integration($this->access_token);
                $this->broadstreet_instance->initialize_connection();
            } else {
                //error_log("[NGL Ad Inserter] No Broadstreet access token available");
                return;
            }
        }
        
        // Fetch fresh advertisements from the zone
        try {
            // Make sure we have a network ID
            if (!$this->broadstreet_instance->network_id) {
                $networks = $this->broadstreet_instance->get_networks();
                if (!empty($networks) && isset($networks[0]['id'])) {
                    $this->broadstreet_instance->network_id = $networks[0]['id'];
                } else {
                    //error_log("[NGL Ad Inserter] No network ID available");
                    return;
                }
            }
            
            // Get the RSS feed URL directly
            $url = $this->broadstreet_instance->get_request_url('networks/' . $this->broadstreet_instance->network_id . '/zones/' . $zone_id . '.rss');
            //error_log("[NGL Ad Inserter] Fetching RSS from URL: {$url}");
            
            $response = @file_get_contents($url);
            if ($response !== false) {
                $advertisements = $this->broadstreet_instance->parse_rss($response);
                
                if (is_array($advertisements) && !empty($advertisements)) {
                    update_option($option_prefix . 'advertisements', $advertisements);
                    //error_log("[NGL Ad Inserter] Updated {$option_prefix}advertisements with " . count($advertisements) . " ads");
                } else {
                    //error_log("[NGL Ad Inserter] No advertisements found for zone {$zone_id}");
                }
            } else {
                //error_log("[NGL Ad Inserter] Error fetching RSS: " . (error_get_last() ? error_get_last()['message'] : 'Unknown error'));
            }
        } catch (Exception $e) {
            error_log("[NGL Ad Inserter] Error fetching advertisements: " . $e->getMessage());
        }
    }
    
    /**
     * Clean up old options for a post
     * 
     * @param int $post_id The post ID
     */
    private function cleanup_old_options($post_id) {
        global $wpdb;
        
        // Get all options for this post
        $option_pattern = "ngl_ad_zone_{$post_id}_%";
        $sql = $wpdb->prepare(
            "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
            $option_pattern
        );
        
        $old_options = $wpdb->get_col($sql);
        $current_zones = array();
        
        // Get current zones from post content
        $post = get_post($post_id);
        if ($post && has_block('newsletterglue/ad-inserter', $post->post_content)) {
            $blocks = parse_blocks($post->post_content);
            $this->extract_zone_ids($blocks, $current_zones);
        }
        
        // Delete options for zones that no longer exist in this post
        foreach ($old_options as $option_name) {
            // Extract zone ID from option name
            preg_match("/ngl_ad_zone_{$post_id}_(.+?)_/", $option_name, $matches);
            if (empty($matches[1])) continue;
            
            $zone_id = $matches[1];
            
            // If this zone ID is not in current zones, delete the option
            if (!in_array($zone_id, $current_zones)) {
                //error_log("[NGL Ad Inserter] Deleting old option for removed zone: {$option_name}");
                delete_option($option_name);
            }
        }
    }
    
    /**
     * Extract zone IDs from blocks array
     * 
     * @param array $blocks Array of blocks
     * @param array &$zone_ids Reference to array to store zone IDs
     */
    private function extract_zone_ids($blocks, &$zone_ids) {
        if (!is_array($blocks)) return;
        
        foreach ($blocks as $block) {
            // Check if this is an ad inserter block
            if ($block['blockName'] === 'newsletterglue/ad-inserter') {
                // Try to get zone ID from attributes
                $zone_id = isset($block['attrs']['adZoneId']) ? $block['attrs']['adZoneId'] : '';
                
                // If no zone ID in attributes, try to extract it from innerHTML
                if (!$zone_id && isset($block['innerHTML'])) {
                    // Look for adZoneId in any data attributes
                    if (preg_match('/data-[^=]*="[^"]*adZoneId[^"]*:([^,"]+)/', $block['innerHTML'], $matches)) {
                        $zone_id = trim($matches[1], '"\'');
                    }
                    
                    // If we still don't have a zone ID, check if it's in a script tag
                    if (empty($zone_id) && preg_match('/<script[^>]*>(.*?)<\/script>/s', $block['innerHTML'], $script_matches)) {
                        $script_content = $script_matches[1];
                        if (preg_match('/adZoneId["\']?\s*:\s*["\']?([^"\',}]+)/', $script_content, $zone_matches)) {
                            $zone_id = trim($zone_matches[1], '"\'');
                        }
                    }
                }
                
                if ($zone_id && !in_array($zone_id, $zone_ids)) {
                    $zone_ids[] = $zone_id;
                    //error_log("[NGL Ad Inserter] Found zone ID: {$zone_id}");
                }
            }
            
            // Process inner blocks if any
            if (!empty($block['innerBlocks'])) {
                $this->extract_zone_ids($block['innerBlocks'], $zone_ids);
            }
        }
    }
    
    /**
     * Refresh ad zone data when a post is saved
     * 
     * @param int $post_id The post ID
     * @param WP_Post $post The post object
     * @param bool $update Whether this is an update
     */
    public function refresh_ad_zone_data($post_id, $post, $update) {
        // Skip auto-saves and revisions
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (wp_is_post_revision($post_id)) return;
        if (wp_is_post_autosave($post_id)) return;
        
        // Only process posts and pages (add other post types as needed)
        //if (!in_array($post->post_type, array('post', 'page'))) return;
        
        // Check if the post content has any ad inserter blocks
        if (!has_block('newsletterglue/ad-inserter', $post->post_content)) {
            //error_log("[NGL Ad Inserter] No ad inserter blocks found in post {$post_id}");
            return;
        }
        
        //error_log("[NGL Ad Inserter] Refreshing ad zone data for post {$post_id}");
        
        // Parse blocks to find ad inserter blocks
        $blocks = parse_blocks($post->post_content);
        $this->process_blocks_recursively($blocks, $post_id);

        //error_log("[NGL Ad Inserter] Found ad zone IDs: " . json_encode($zone_ids));
        
        // Clean up old options for this post
        $this->cleanup_old_options($post_id);
        //error_log("[NGL Ad Inserter] Cleaned up old options for post {$post_id}");
    }

    /**
     * Check if the integration is available
     */
    public function is_available()
    {

        $available = true;
        // Only consider the integration available if we have an access token
        $this->set_available($available);
        return $available;
    }

    public function set_available( $available ) {
        if($available) {
            update_option( 'ngl_' . $this->id . '_available', $available );
        } else {
            update_option( 'ngl_' . $this->id . '_available', false );
        }
    }

    /**
     * Sync all ads
     */
    public function sync_all_ads()
    {

        $existing_data = get_option($this->option_name, '');

        if (empty($existing_data)) {
            $ads = $this->get_ads();
            update_option($this->option_name, wp_json_encode($ads));
        }
    }
}
