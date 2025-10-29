<?php

/**
 * Broadstreet API integration for Newsletter Glue
 *
 * @package Newsletter Glue
 */
 

class NGL_Broadstreet_API_Integration {

    private $access_token;

    private $base_url = 'https://api.broadstreetads.com/api';

    private $api_version = '0';

    private $request_url;

    public $networks;

    public $network_id;

    public $network_name;

    public $ad_zones; 

    public $advertisers;

    public $advertiser_id;

    public $advertisements;

    public $advertisement_id;

    private $error;

    public function __construct($access_token) {
        $this->access_token = $access_token;
        $this->set_base_url($this->base_url);
        
        // Initialize properties as empty arrays
        $this->networks = array();
        $this->advertisers = array();
    }
    
    /**
     * Initialize the API connection by fetching networks and advertisers
     * This should be called after a successful test_connection
     */
    public function initialize_connection() {
        if (!empty($this->access_token)) {
            $this->get_networks();
            $this->single_out_networks();
            
            // Only get advertisers if we have a network ID
            if (!empty($this->network_id)) {
                $this->get_advertisers();
                $this->single_out_advertisers();
            }
            
            return true;
        }
        
        return false;
    }

    public function test_connection($request = null) {
        // If we have a request with an access token, use that instead of the stored one
        if ($request && isset($request['access_token'])) {
            $this->access_token = sanitize_text_field($request['access_token']);
        }
        
        $url = $this->get_request_url('networks');
        $response = $this->handle_request($url);
        
        // Check if the response is valid
        $success = !empty($response) && !isset($response['error']) && !isset($response['message']) && 404 !== $response['status'];
        
        // If the test was successful, initialize the connection
        if ($success) {
            // Save the access token to the database if it was provided in the request
            if ($request && isset($request['access_token'])) {
                update_option('ngl_broadstreet_access_token', $this->access_token);
                // Also save the connection status
                update_option('ngl_broadstreet_has_connection', true);
            }
            
            // Initialize the connection to fetch networks and advertisers
            $this->initialize_connection();
            
            return [
                'success' => true,
                'message' => __('Connection successful', 'newsletter-glue'),
                'data' => $response
            ];
        }
        
        // If connection failed, ensure connection status is set to false
        update_option('ngl_broadstreet_has_connection', false);
        
        return [
            'success' => false,
            'message' => isset($response['message']) ? $response['message'] : __('Connection failed', 'newsletter-glue')
        ];
    }
    
    /**
     * Verify if the current connection is valid
     * 
     * @return array Connection status
     */
    public function verify_connection() {
        if (empty($this->access_token)) {
            return [
                'success' => false,
                'message' => 'No access token available'
            ];
        }
        
        $url = $this->get_request_url('networks');
        $response = $this->handle_request($url);
        
        // Check if the response is valid
        $success = !empty($response) && !isset($response['error']) && !isset($response['message']) && 404 !== $response['status'];
        
        // Update the connection status in the database
        update_option('ngl_broadstreet_has_connection', $success);
        
        return [
            'success' => $success,
            'data' => $response
        ];
    }

    public function set_base_url($base_url) {
        $this->base_url = $base_url . '/' . $this->api_version;
    }

    public function get_request_url($url) {
        return $this->base_url . '/' . $url . '?access_token=' . $this->access_token;
    }

    public function get_networks() {
        $url = $this->get_request_url('networks');
        $response = $this->handle_request($url);

        $this->networks = $response;

        return $response;
    }

    public function get_network_name() {
        return $this->network_name;
    }

    public function set_network_name($network_name) {
        $this->network_name = $network_name;
    }

    public function get_advertisers() {
        $url = $this->get_request_url('networks/' . $this->network_id . '/advertisers');
        $response = $this->handle_request($url);

        $this->advertisers = $response;

        return $response;
    }

    public function get_ad_zones() {
        $url = $this->get_request_url('networks/' . $this->network_id . '/zones');
        $response = $this->handle_request($url);

        $this->ad_zones = $response;

        $response = $this->parse_ad_zones($response);

        return $response;
    }

    public function parse_ad_zones($ad_zones) {
        $parsed_ad_zones = array();
        foreach ($ad_zones['zones'] as $ad_zone) {
            $parsed_ad_zones[] = array(
                'id' => $ad_zone['id'],
                'name' => $ad_zone['name'],
            );
        }
        return $parsed_ad_zones;
    }

    public function get_ad_zone_by_id($id) {
        if (isset($this->ad_zones['zones'])) {
            foreach ($this->ad_zones['zones'] as $ad_zone) {
                if ($ad_zone['id'] == $id) {
                    return $ad_zone;
                }
            }
        }

        return null;
    }

    public function get_ad_zone_rss($request){
        // Extract zone_id from the request
        $params = $request->get_params();
        $zone_id = isset($params['zone_id']) ? $params['zone_id'] : '';
        
        if (empty($zone_id)) {
            return new WP_Error('missing_zone_id', 'Zone ID is required', array('status' => 400));
        }
        
        $url = $this->get_request_url('networks/' . $this->network_id . '/zones/' . $zone_id . '.rss');
        $response = file_get_contents($url);

        $response = $this->parse_rss($response);

        return $response;
    }

    public function parse_rss($rss){
        $xml = simplexml_load_string($rss);
        $items = $xml->channel->item;
        $parsed_items = array();
        foreach ($items as $item) {
            $link = (string) $item->link;
            $id = (int) basename($link);
            $parsed_items[] = $id;
        }
        return $parsed_items;
    }

    public function get_zone_campaigns(){
        $url = $this->get_request_url('networks/' . $this->network_id . '/advertisers/' . $this->advertiser_id . '/campaigns');
        $response = $this->handle_request($url);

        return $response;
    }

    public function get_advertisements() {
        $url = $this->get_request_url('networks/' . $this->network_id . '/advertisers/' . $this->advertiser_id . '/advertisements');
        $response = $this->handle_request($url);

        $this->advertisements = $response;

        return $response;
    }

    public function get_single_advertisement_by_id($id) {
        $url = $this->get_request_url('networks/' . $this->network_id . '/advertisers/' . $this->advertiser_id . '/advertisements/' . $id);
        $response = $this->handle_request($url);

        return $response;
    }

    public function single_out_networks() {
        // Make sure networks is an array and not empty before processing
        if (is_array($this->networks) && !empty($this->networks)) {
            if (count($this->networks) == 1) {
                $this->networks = current($this->networks);
                // Make sure the expected array structure exists
                if (is_array($this->networks) && isset($this->networks[0]['id'])) {
                    $this->network_id = $this->networks[0]['id'];
                    $this->set_network_name($this->networks[0]['name']);
                }
            }
        } else {
            // Initialize as empty array if not valid
            $this->networks = array();
        }
        return $this->networks;
    }

    public function single_out_advertisers() {
        // Make sure advertisers is an array and not empty before processing
        if (is_array($this->advertisers) && !empty($this->advertisers)) {
            if (count($this->advertisers) == 1) {
                $this->advertisers = current($this->advertisers);
                // Make sure the expected array structure exists
                if (is_array($this->advertisers) && isset($this->advertisers[0]['id'])) {
                    $this->advertiser_id = $this->advertisers[0]['id'];
                }
            }
        } else {
            // Initialize as empty array if not valid
            $this->advertisers = array();
        }
        return $this->advertisers;
    }

    public function handle_request($url, $method = 'GET', $data = array()) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        $response = curl_exec($ch);
        $this->error = curl_error($ch);
        curl_close($ch);
        return json_decode($response, true);
        
    }
    
    

    
}