<?php
/**
 * Omeda API Client (Updated with Lookup Service).
 */
class Omeda_API_Client {

    private $app_id;
    private $brand_abbreviation;
    private $base_url;
    private $default_user_id;

    public function __construct() {
        $this->load_configuration();
    }

    private function load_configuration() {
         $this->app_id = get_option('omeda_app_id');
        $this->brand_abbreviation = get_option('omeda_brand_abbreviation');
        $this->default_user_id = get_option('omeda_default_user_id');

        if (empty($this->app_id) || empty($this->brand_abbreviation) || empty($this->default_user_id)) {
            throw new Exception('API Credentials, Brand Abbreviation, or Default User ID are missing.');
        }

        $environment = get_option('omeda_environment', 'staging');
        $base_host = ($environment === 'production') ? 'ows.omeda.com' : 'ows.omedastaging.com';
        $this->base_url = sprintf('https://%s/webservices/rest/brand/%s/', $base_host, $this->brand_abbreviation);
    }

    /**
     * Sends a request to the Omeda API. (Handles GET correctly)
     */
    private function send_request($endpoint, $method = 'POST', $payload = null) {
        $url = $this->base_url . ltrim($endpoint, '/');

        $content_type = 'application/json;charset=utf-8';
        $body = null;

        // Prepare body only if method is not GET and payload exists
        if ($method !== 'GET' && !is_null($payload)) {
            if (is_array($payload)) {
                $body = json_encode($payload);
            } else if (is_string($payload) && substr(ltrim($payload), 0, 1) === '<') {
                $content_type = 'application/xml;charset=utf-8';
                $body = $payload;
            } else {
                $body = $payload;
            }
        }

        $args = [
            'method'    => $method,
            'headers'   => [
                'x-omeda-appid' => $this->app_id,
                'User-Agent'    => 'Omeda_WP_Integration/' . OMEDA_WP_VERSION,
            ],
            'body'      => $body,
            'timeout'   => 60,
        ];

        // Only set Content-Type if there is a body
        if ($body !== null) {
            $args['headers']['Content-Type'] = $content_type;
        }

        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            throw new Exception('WordPress HTTP Error: ' . $response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);

        // Handle successful responses
        if ($response_code >= 200 && $response_code < 300) {
            $decoded_response = json_decode($response_body, true);
            return (json_last_error() === JSON_ERROR_NONE) ? $decoded_response : $response_body;
        } else {
            // Handle errors
            $error_message = sprintf('Omeda API Error (HTTP %d)', $response_code);
            $decoded_error = json_decode($response_body, true);
            if (is_array($decoded_error) && isset($decoded_error['Errors'][0]['Error'])) {
                 $error_message .= ': ' . $decoded_error['Errors'][0]['Error'];
            }

            error_log('Omeda API Request Failed: ' . $error_message . ' | Endpoint: ' . $endpoint);
            throw new Exception($error_message);
        }
    }

    // --- Status Check Methods ---

    /**
     * Get Deployment Status using the Deployment Lookup API.
     * GET /omail/deployment/lookup/{trackId}/*
     */
    public function get_deployment_lookup($track_id) {
        $endpoint = sprintf('omail/deployment/lookup/%s/*', $track_id);
        try {
            return $this->send_request($endpoint, 'GET');
        } catch (Exception $e) {
            // Handle HTTP 404 specifically, as the deployment might not be immediately available
            if (strpos($e->getMessage(), 'HTTP 404') !== false) {
                return null;
            }
            throw $e;
        }
    }

    /**
     * Checks if Omeda has finished processing the audience assignment by checking RecipientCount.
     */
    public function is_audience_ready($track_id) {
        try {
            $details = $this->get_deployment_lookup($track_id);
            if (is_null($details)) {
                return false; // Deployment not found yet
            }
            
            // When RecipientCount is present (even if 0), processing is complete.
            if (isset($details['RecipientCount'])) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            // Log the error but return false so the workflow can retry.
            error_log('Error checking audience readiness for ' . $track_id . ': ' . $e->getMessage());
            return false;
        }
    }

    // --- Deployment Steps ---
    // Step 1: Create Deployment
    public function step1_create_deployment($config) {
        $user_id = $config['UserId'] ?? $this->default_user_id;
        $payload = [
            "DeploymentName"        => $config['DeploymentName'],
            "DeploymentDate"        => $config['ScheduleDate'],
            "DeploymentTypeId"      => (int) $config['DeploymentTypeId'],
            "CampaignId"            => $config['CampaignId'] ?? $config['DeploymentName'],
            "OwnerUserId"           => $user_id,
            "FinalApproverUserId"   => $user_id,
            "Splits"                => 1, "TrackLinks" => 1, "TrackOpens" => 1,
            "Notes"                 => $config['Notes'] ?? "Deployed via WordPress integration",
            "ReloadOnqQueryBeforeFinalDeployment" => 1,
            "Testers"               => []
        ];

        $response = $this->send_request('omail/deployment/*', 'POST', $payload);
        if (is_array($response) && isset($response['TrackId'])) {
            return $response['TrackId'];
        } else {
            throw new Exception('Failed to retrieve TrackId after creating deployment.');
        }
    }

    // Step 2: Assign Audience
    public function step2_assign_audience($track_id, $config) {
        $user_id = $config['UserId'] ?? $this->default_user_id;
        $output_criteria = $config['OutputCriteria'] ?? get_option('omeda_default_output_criteria', 'Newsletter_Member_id');

        $payload = [
            "UserId"    => $user_id,
            "TrackId"   => $track_id,
            "Audience"  => [[
                "QueryName"         => $config['QueryName'],
                "OutputCriteria"    => $output_criteria,
                "SplitNumber"       => 1,
                "RemoveDuplicates"  => 1
            ]]
        ];
        $this->send_request('omail/deployment/audience/add/*', 'POST', $payload);
    }

    // Step 3: Add Content
    public function step3_add_content($track_id, $config) {
        $user_id = $config['UserId'] ?? $this->default_user_id;
        $mailbox = $config['MailboxName'] ?? get_option('omeda_default_mailbox', 'newsletters');

        $from_name = $config['FromName'];
        $subject = $config['Subject'];
        $reply_to = $config['ReplyTo'];
        $html_content = $config['HtmlContent'];

        $xml_payload = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Deployment>
    <TrackId>{$track_id}</TrackId>
    <UserId>{$user_id}</UserId>
    <Splits>
        <Split>
            <SplitNumber>1</SplitNumber>
            <FromName><![CDATA[{$from_name}]]></FromName>
            <Mailbox>{$mailbox}</Mailbox>
            <Subject><![CDATA[{$subject}]]></Subject>
            <ReplyTo>{$reply_to}</ReplyTo>
            <HtmlContent><![CDATA[{$html_content}]]></HtmlContent>
        </Split>
    </Splits>
</Deployment>
XML;

        $xml_payload = trim(preg_replace('/>\s+</', '><', $xml_payload));
        $this->send_request('omail/deployment/content/*', 'POST', $xml_payload);
    }

    // Step 4: Send Test
    public function step4_send_test($track_id, $config) {
        $user_id = $config['UserId'] ?? $this->default_user_id;
        $payload = ["UserId" => $user_id, "TrackId" => $track_id];
        $this->send_request('omail/deployment/sendtest/*', 'POST', $payload);
    }

    // Step 5: Schedule Deployment
    public function step5_schedule_deployment($track_id, $config) {
        $user_id = $config['UserId'] ?? $this->default_user_id;
        $payload = ["UserId" => $user_id, "TrackId" => $track_id, "ScheduledDate" => $config['ScheduleDate']];
        $this->send_request('omail/deployment/schedule/*', 'POST', $payload);
    }
}
