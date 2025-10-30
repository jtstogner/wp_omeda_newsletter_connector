<?php
/**
 * Manages the asynchronous deployment workflow using WP-Cron and status polling.
 */
class Omeda_Workflow_Manager {

    private $api_client;
    const CRON_HOOK = 'omeda_workflow_process_step';
    // Polling interval: How often to check Omeda if prerequisites are not met.
    const POLLING_INTERVAL = 45; // seconds
    // Max retries (e.g., 20 * 45s = 15 minutes total wait time before failure)
    const MAX_RETRIES = 20;

    public function __construct(Omeda_API_Client $api_client) {
        $this->api_client = $api_client;
    }

    public function init() {
        // The old cron-based system is being replaced. No action needed here for now.
    }

    /**
     * Creates the deployment and assigns the audience. Triggered on first draft save.
     */
    public function create_and_assign_audience($post_id, $config_id) {
        // Clear any previous logs for a clean start.
        Omeda_Logger::clear_logs($post_id);
        Omeda_Logger::log($post_id, 'INFO', 'Workflow Initiated: Post saved as draft with a Deployment Type.');

        // Calculate the next nearest hour for the deployment date
        $next_hour_timestamp = ceil(time() / 3600) * 3600; // Round up to next hour
        $next_hour_date = gmdate('Y-m-d H:i', $next_hour_timestamp);
        
        $config = $this->prepare_configuration($post_id, $config_id, $next_hour_date);
        if (!$config) {
            Omeda_Logger::log($post_id, 'ERROR', 'Failed to prepare configuration. Workflow aborted.');
            return;
        }

        try {
            // Step 1: Create the deployment
            $this->api_client->set_post_context($post_id, 'create_deployment');
            Omeda_Logger::log($post_id, 'INFO', 'Executing: Create deployment job...', 'create_deployment');
            Omeda_Logger::log($post_id, 'DEBUG', 'Starting deployment creation transaction...', 'create_deployment');
            
            $track_id = $this->api_client->step1_create_deployment($config);
            update_post_meta($post_id, '_omeda_track_id', $track_id);
            
            Omeda_Logger::log($post_id, 'DEBUG', 'Deployment creation transaction completed successfully.', 'create_deployment');
            Omeda_Logger::log($post_id, 'INFO', "Step 1/4 Complete: Deployment created with TrackID: {$track_id}", 'create_deployment');

            // Step 2: Assign the audience
            $this->api_client->set_post_context($post_id, 'assign_audience');
            Omeda_Logger::log($post_id, 'DEBUG', 'Starting audience assignment transaction...', 'assign_audience');
            Omeda_Logger::log($post_id, 'INFO', 'Executing: Assign audience job...', 'assign_audience');
            
            $this->api_client->step2_assign_audience($track_id, $config);
            
            Omeda_Logger::log($post_id, 'DEBUG', 'Audience assignment transaction completed successfully.', 'assign_audience');
            Omeda_Logger::log($post_id, 'INFO', 'Step 2/4 Complete: Audience assigned.', 'assign_audience');

            // Step 3: Add the initial content
            $this->api_client->set_post_context($post_id, 'add_content');
            Omeda_Logger::log($post_id, 'DEBUG', 'Starting content addition transaction...', 'add_content');
            Omeda_Logger::log($post_id, 'INFO', 'Executing: Add content job...', 'add_content');
            
            $this->api_client->step3_add_content($track_id, $config);
            
            Omeda_Logger::log($post_id, 'DEBUG', 'Content addition transaction completed successfully.', 'add_content');
            Omeda_Logger::log($post_id, 'INFO', 'Step 3/4 Complete: Content and subject sent to Omeda.', 'add_content');

            // Step 4: Log deployment date info
            Omeda_Logger::log($post_id, 'INFO', "Step 4/4 Complete: Initial deployment setup complete. Scheduled for {$next_hour_date} UTC (temporary date - will update when published).");

            // Clear the context
            $this->api_client->clear_post_context();

        } catch (Exception $e) {
            Omeda_Logger::log($post_id, 'ERROR', 'Initial deployment creation failed: ' . $e->getMessage());
            $this->api_client->clear_post_context();
        }
    }

    /**
     * Updates the HTML content of an existing deployment.
     * @param string|array|null $config Optional. The prepared configuration array. If null, it will be generated.
     */
    public function update_content($post_id, $track_id, $config_id, $config = null) {
        if (!$config) {
            $config = $this->prepare_configuration($post_id, $config_id);
            if (!$config) {
                Omeda_Logger::log($post_id, 'ERROR', 'Could not prepare configuration for content update.');
                return;
            }
        }

        try {
            $this->api_client->set_post_context($post_id, 'update_content');
            Omeda_Logger::log($post_id, 'INFO', 'Updating content in Omeda...', 'update_content');
            
            $this->api_client->step3_add_content($track_id, $config);
            
            Omeda_Logger::log($post_id, 'INFO', 'Content updated successfully in Omeda.', 'update_content');
            $this->api_client->clear_post_context();
        } catch (Exception $e) {
            Omeda_Logger::log($post_id, 'ERROR', 'Failed to update content in Omeda: ' . $e->getMessage(), 'update_content');
            $this->api_client->clear_post_context();
        }
    }

    /**
     * Sends a test email and schedules the final deployment.
     */
    public function schedule_and_send_test($post_id, $track_id, $config_id) {
        Omeda_Logger::log($post_id, 'INFO', 'Post Published/Scheduled. Finalizing deployment...');

        $config = $this->prepare_configuration($post_id, $config_id);
        if (!$config) {
            Omeda_Logger::log($post_id, 'ERROR', 'Could not prepare configuration for final scheduling.');
            return;
        }

        try {
            // Step 1: Update the content to ensure the latest version is used
            $this->update_content($post_id, $track_id, $config_id, $config);

            // Step 2: Send the test email
            $this->send_test($post_id, $track_id, $config, false); // `false` to prevent duplicate logging

            // Step 3: Schedule the deployment with the final, calculated date
            $this->api_client->set_post_context($post_id, 'schedule_deployment');
            Omeda_Logger::log($post_id, 'INFO', 'Scheduling deployment...', 'schedule_deployment');
            
            $this->api_client->step5_schedule_deployment($track_id, $config);
            
            Omeda_Logger::log($post_id, 'INFO', "Deployment successfully scheduled for: {$config['ScheduleDate']} (UTC).", 'schedule_deployment');
            Omeda_Logger::log($post_id, 'INFO', 'Workflow Complete.');
            $this->api_client->clear_post_context();

        } catch (Exception $e) {
            Omeda_Logger::log($post_id, 'ERROR', 'Failed to schedule or send test: ' . $e->getMessage());
            $this->api_client->clear_post_context();
        }
    }

    /**
     * Sends a test email for an existing deployment.
     * @param array $config The prepared configuration array.
     * @param bool  $log_status Whether to write a standalone log entry for this action.
     */
    public function send_test($post_id, $track_id, $config, $log_status = true) {
        if ($log_status) {
            Omeda_Logger::log($post_id, 'INFO', 'Sending new test email due to post update...');
        }

        if (!$config) {
            Omeda_Logger::log($post_id, 'ERROR', 'Could not send test: Configuration was not provided.');
            return;
        }

        try {
            $this->api_client->set_post_context($post_id, 'send_test');
            Omeda_Logger::log($post_id, 'INFO', 'Sending test email...', 'send_test');
            
            $this->api_client->step4_send_test($track_id, $config);
            
            Omeda_Logger::log($post_id, 'INFO', 'Test email sent successfully.', 'send_test');
            $this->api_client->clear_post_context();
        } catch (Exception $e) {
            Omeda_Logger::log($post_id, 'WARN', 'Failed to send test email (check Omeda tester configuration): ' . $e->getMessage(), 'send_test');
            $this->api_client->clear_post_context();
        }
    }

     /**
     * Merges global settings, deployment type settings, and post data.
     * @param int $post_id The ID of the WordPress post.
     * @param int $config_id The ID of the Deployment Type configuration post.
     * @param string|null $override_schedule_date Optional. A specific date string to use instead of the post's date.
     * @return array|null The merged configuration array or null on failure.
     */
    public function prepare_configuration($post_id, $config_id, $override_schedule_date = null) {
        $post = get_post($post_id);
        if (!$post) return null;

        // Get configuration from the Deployment Type CPT
        $config = Omeda_Deployment_Types::get_configuration($config_id);
        if (!$config) return null;

        $config['PostId'] = $post_id;

        if ($override_schedule_date) {
            $schedule_date = $override_schedule_date;
        } else {
            // Determine Schedule Date (Use GMT/UTC for API consistency)
            if ($post->post_status == 'future') {
                $schedule_date = get_gmt_from_date($post->post_date, 'Y-m-d H:i');
            } else {
                // If it's an immediate publish, calculate the delayed time
                $delay_minutes = (int) get_option('omeda_publish_delay', 30);
                $schedule_date = gmdate('Y-m-d H:i', time() + ($delay_minutes * 60));
            }
        }

        $config['ScheduleDate'] = $schedule_date;
        
        // Determine Deployment Name with override support
        $deployment_name_override = get_post_meta($post_id, '_omeda_deployment_name', true);
        if (!empty($deployment_name_override)) {
            $config['DeploymentName'] = $deployment_name_override;
        } else if (isset($config['DeploymentNameFormat']) && !empty($config['DeploymentNameFormat'])) {
            $config['DeploymentName'] = Omeda_Variable_Parser::parse($config['DeploymentNameFormat'], $post_id);
        } else {
            $config['DeploymentName'] = $post->post_title;
        }
        
        // Determine Campaign ID with override support
        $campaign_id_override = get_post_meta($post_id, '_omeda_campaign_id', true);
        if (!empty($campaign_id_override)) {
                    $config['CampaignId'] = $campaign_id_override;
        } else if (isset($config['CampaignIdFormat']) && !empty($config['CampaignIdFormat'])) {
            $config['CampaignId'] = Omeda_Variable_Parser::parse($config['CampaignIdFormat'], $post_id);
        }
        // If no CampaignId is set, Omeda API will auto-generate one

        // Determine Subject with WordPress variable parsing
        if (isset($config['SubjectFormat']) && !empty($config['SubjectFormat'])) {
            // Parse WordPress variables in the subject format
            $config['Subject'] = Omeda_Variable_Parser::parse($config['SubjectFormat'], $post_id);
        } else {
            $config['Subject'] = $post->post_title;
        }

        // Prepare Content
        // Placeholder: Assumes the post content is the final HTML.
        $content = apply_filters('the_content', $post->post_content);
        // Ensure an unsubscribe link is present (required by Omeda)
        if (strpos($content, '@{{unsub_url}}@') === false) {
            $content .= '<p><a href="@{{unsub_url}}@">Unsubscribe</a></p>';
        }
        $config['HtmlContent'] = $content;

        return $config;
    }
    
    /**
     * Get workflow summary for quick display
     */
    public function get_workflow_summary($post_id) {
        $logs = get_post_meta($post_id, '_omeda_workflow_log');
        $track_id = get_post_meta($post_id, '_omeda_track_id', true);
        
        $summary = [
            'status' => 'pending',
            'last_step' => null,
            'error_count' => 0,
            'last_error' => null,
            'last_timestamp' => null,
            'track_id' => $track_id
        ];
        
        if (empty($logs)) {
            return $summary;
        }
        
        // Parse logs to get summary
        foreach ($logs as $log_json) {
            $log = json_decode($log_json, true);
            if (!$log) continue;
            
            if ($log['level'] === 'ERROR') {
                $summary['error_count']++;
                $summary['last_error'] = $log['message'];
                $summary['status'] = 'error';
            }
            
            if ($log['step']) {
                $summary['last_step'] = $log['step'];
                if ($log['level'] === 'INFO' && strpos($log['message'], 'Complete') !== false) {
                    $summary['status'] = 'in_progress';
                }
            }
            
            $summary['last_timestamp'] = $log['timestamp'];
        }
        
        // Check if deployment is complete
        if ($track_id && $summary['error_count'] === 0 && strpos(end($logs), 'Complete') !== false) {
            $summary['status'] = 'complete';
        }
        
        return $summary;
    }
    
    /**
     * Creates the workflow logs database table.
     * Should be called on plugin activation.
     */
    public static function create_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'omeda_workflow_logs';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            post_id bigint(20) unsigned NOT NULL,
            timestamp datetime NOT NULL,
            level varchar(20) NOT NULL,
            step varchar(50) DEFAULT NULL,
            retry_count int(11) DEFAULT 0,
            message text NOT NULL,
            context longtext DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY post_id (post_id),
            KEY timestamp (timestamp),
            KEY level (level)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
