<?php

/**
 * Sendy External Integration
 */

 class NGL_Sendy_External extends NGL_Sendy {

    public $lists = array();

    public $subscribed_lists = array();

    public $segments = array();

	public $subscribed_segments = array();

    public function __construct() {
		add_action('rest_api_init', array($this, 'register_rest_actions'));
        //parent::__construct();
		$this->get_api_key();
    }

	public function register_rest_actions(){
		register_rest_route( 'newsletter-glue/v1', '/sendy-subscribe', array(
			'methods' => 'GET',
			'callback' => array($this, 'subscribe'),
			'permission_callback' => function () {
				return current_user_can('manage_options');
			}
		) );
	}

    public function get_current_brand(){
		return newsletterglue_get_option( 'brand', $this->app );
	}

    public function get_subscriber($email){

		$subscriber_status = array();

		$brand = $this->get_current_brand();

		$this->get_all_lists();

		if ( ! empty( $this->lists ) ) {
			foreach( $this->lists as $list_id => $name ) {
				$args = array(
					'api_key'  => $this->api_key,
					'email' => $email,
					'list_id'  => $list_id,
				);

				$subscriber_id_response = $this->api->post( '/api/subscribers/get-subscriber-id.php', $args );
				$subscriber_id = $subscriber_id_response;

				$req = $this->api->post( '/api/subscribers/subscription-status.php', $args );

				// Get all available segments for this list
				$this->get_all_segments($list_id);
				$available_segments = $this->segments;
				
				// Get segments the subscriber is actually subscribed to
				$subscribed_segments = array();
				if ($this->give_subscription_feedback($req) === 'Subscribed' && !empty($available_segments)) {
					$subscribed_segments = $this->get_subscriber_segments($list_id, $email);
				}

				$subscriber_data = array(
					'subscriber_id' => $subscriber_id,
					'list_name'  => $name,
					'status' => $this->give_subscription_feedback($req),
					'action' => $this->build_action_url($this->give_subscription_feedback($req)),
				);
				
				// Only add segments if they exist for this list
				if (!empty($available_segments)) {
					$subscriber_data['segments'] = $available_segments;
				}
				
				// Add subscribed segments only if they contain actual data (not empty arrays)
				if (!empty($subscribed_segments) && count(array_filter($subscribed_segments, function($value) { return !empty($value); })) > 0) {
					$subscriber_data['subscribed_segments'] = $subscribed_segments;
				}
				
				$subscriber_status[$list_id] = $subscriber_data;
			}
		}

		asort( $subscriber_status );

        $this->subscribed_lists = $subscriber_status;

		return array($subscriber_status);

    }

    public function get_all_lists() {
        $lists = array();

		$brand = $this->get_current_brand();

		$req = $this->api->post( '/api/lists/get-lists.php', array( 'brand_id' => $brand ) );

		if ( ! empty( $req ) ) {
			$req = json_decode( $req );
			if( ! empty( $req ) ) {
				foreach( $req as $key => $data ) {
					$lists[ $data->id ] = $data->name;
				}
			}
		}

		if ( ! empty( $lists ) ) {
			$this->lists = $lists;
		}

		asort( $lists );

		return $lists;
    }

	public function get_all_segments($list_id){
		$segments = array();

		$brand = $this->get_current_brand();

		$args = array(
			'api_key'  => $this->api_key,
			'brand_id' => $brand,
			'list_id'  => $list_id
			);

		$req = $this->api->post( '/api/segments/get-segments.php', $args );

		if ( ! empty( $req ) ) {
			$req = json_decode( $req );
			if( ! empty( $req ) ) {
				foreach( $req as $key => $data ) {
					$segments[ $data->id ] = $data->name;
				}
			}
		}

		// Reset segments property before assigning new values
		$this->segments = array();
		
		if( ! empty( $segments ) ) {
			$this->segments = $segments;
			asort( $this->segments );
		}

		return $this->segments;
	}

	public function get_subscriber_segments($list_id, $email){
		$segments = array();
		$segment_ids = array();

		$args = array(
			'api_key'  => $this->api_key,
			'email' => $email,
			'list_id'  => $list_id
			);

		$req = $this->api->post( '/api/subscribers/subscriber-segments.php', $args );

		if ( ! empty( $req ) ) {
			$req = json_decode( $req );
			if( ! empty( $req ) && isset($req->status) && $req->status === 'success' && isset($req->segments) && is_array($req->segments) ) {
				// Store segment IDs from the response
				$segment_ids = $req->segments;
			}
		}

		// Reset subscribed_segments property
		$this->subscribed_segments = array();
		
		// If we have segment IDs, get their names from the available segments
		if( ! empty( $segment_ids ) && ! empty( $this->segments ) ) {
			foreach( $segment_ids as $segment_id ) {
				// Check if this segment ID exists in the available segments
				if( isset( $this->segments[$segment_id] ) ) {
					$segments[$segment_id] = $this->segments[$segment_id];
				}
			}
			
			if( ! empty( $segments ) ) {
				$this->subscribed_segments = $segments;
				asort( $this->subscribed_segments );
			}
		}

		return $this->subscribed_segments;
	}

    public function get_subscribed_lists(){
        $subscribed_lists = array();

        foreach( $this->subscribed_lists as $subscriber ) {
            if ( 'Subscribed' === $subscriber['status'] ) {
                $subscribed_lists[] = $subscriber;
            }
        }

        return $subscribed_lists;
    }

	public function give_subscription_feedback($response){

		$response = trim($response);

		$response = strtolower($response);

		switch($response){
			case 'subscribed':
				return 'Subscribed';
			case 'unsubscribed':
				return 'Unsubscribed';
			case 'bounced':
				return 'Bounced';
			case 'soft bounced':
				return 'Soft Bounced';
			case 'unconfirmed':
				return 'Unconfirmed';
			case 'complained':
				return 'Marked as spam';
			case 'email does not exist in list':
				return 'Not subscribed';
			default:
				return 'Unknown';
		}
		return $response;

	}

	public function build_action_url($status){
		switch($status){
			case 'Subscribed':
				return 'https://sendy.co/subscribed';
			case 'Unsubscribed':
				return 'https://sendy.co/unsubscribed';
			case 'Bounced':
				return 'https://sendy.co/bounced';
			case 'Soft Bounced':
				return 'https://sendy.co/soft-bounced';
			case 'Unconfirmed':
				return 'https://sendy.co/unconfirmed';
			case 'Marked as spam':
				return 'https://sendy.co/mark-as-spam';
			case 'Not subscribed':
				return rest_url( 'newsletter-glue/v1/sendy-subscribe' );
			default:
				return 'https://sendy.co/unknown';
		}
	}

	public function subscribe($email, $list_id){
		$req = $this->api->post('/subscribe', array(
			'api_key' => $this->api_key,
			'email' => $email,
			'list_id' => $list_id,
		));

		return $req;
	}

	/**
	 * Format subscriber data into a structured HTML table
	 * 
	 * @param string $email The subscriber email
	 * @param array $subscriber_data The subscriber data from get_subscriber method
	 * @return string Formatted HTML output
	 */
	public function format_subscriber_data($email, $subscriber_data) {
		if (empty($subscriber_data) || !is_array($subscriber_data) || empty($subscriber_data[0])) {
			return '<div class="nglsc-subscriber-error">
				<p>⚠️ Subscriber not found in Sendy.</p>
			</div>';
		}

		$subscriber_lists = $subscriber_data[0];
		
		$output = '<div class="nglsc-subscriber-result">
			<p>✅ Subscriber found in Sendy.</p>
			<h4>Subscription Details:</h4>
			<table class="nglsc-subscriber-table">
				<thead>
					<tr>
						<th>List</th>
						<th>Status</th>
						<th>Segments</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>';
		
		foreach ($subscriber_lists as $list_id => $list_data) {
			$list_name = isset($list_data['list_name']) ? esc_html($list_data['list_name']) : 'Unknown List';
			$status = isset($list_data['status']) ? esc_html($list_data['status']) : 'Unknown';
			$action_url = isset($list_data['action']) ? esc_url($list_data['action']) : '#';
			
			// Format segments
			$segments_html = 'None';
			if (!empty($list_data['segments'])) {
				$segments_html = '<div class="nglsc-segment-checkboxes">';
				
				foreach ($list_data['segments'] as $segment_id => $segment_name) {
					// Check if this segment is in the subscribed segments
					$checked = '';
					if (!empty($list_data['subscribed_segments']) && isset($list_data['subscribed_segments'][$segment_id])) {
						$checked = ' checked="checked"';
					}
					
					$checkbox_id = 'segment-' . $list_id . '-' . $segment_id;
					$segments_html .= '<div class="nglsc-segment-checkbox-item">';
					$segments_html .= '<input type="checkbox" id="' . esc_attr($checkbox_id) . '" value="' . esc_attr($segment_id) . '"' . $checked . '>';
					$segments_html .= '<label for="' . esc_attr($checkbox_id) . '">' . esc_html($segment_name) . '</label>';
					$segments_html .= '</div>';
				}
				
				$segments_html .= '</div>';
			}
			
			// Format action button based on status
			$action_button = '';
			if ($status === 'Not subscribed') {
				$action_button = '<input type="checkbox" id="subscribe-' . esc_attr($list_id) . '-' . esc_attr($email) . '" data-list="' . esc_attr($list_id) . '" data-email="' . esc_attr($email) . '" class="nglsc-action-checkbox">';
				$action_button .= '<label for="subscribe-' . esc_attr($list_id) . '-' . esc_attr($email) . '">Subscribe</label>';
			} elseif ($status === 'Marked as spam') {
				$action_button = '<input type="checkbox" id="notspam-' . esc_attr($list_id) . '-' . esc_attr($email) . '" data-list="' . esc_attr($list_id) . '" data-email="' . esc_attr($email) . '" class="nglsc-action-checkbox">';
				$action_button .= '<label for="notspam-' . esc_attr($list_id) . '-' . esc_attr($email) . '">Mark as not spam</label>';

				$action_button = '<a href="' . $action_url . '" class="button nglsc-action-button" target="_blank">View in Suppression List</a>';
			} else {
				$action_button = '<span class="dashicons dashicons-yes" style="color:green"></span>';
			}
			
			// Status class for styling
			$status_class = strtolower(str_replace(' ', '-', $status));
			
			$output .= '<tr>
				<td>' . $list_name . '</td>
				<td><span class="nglsc-status nglsc-status-' . $status_class . '">' . $status . '</span></td>
				<td>' . $segments_html . '</td>
				<td>' . $action_button . '</td>
			</tr>';
		}
		
		$output .= '</tbody>
			</table>
		</div>';
		
		return $output;
	}
}
