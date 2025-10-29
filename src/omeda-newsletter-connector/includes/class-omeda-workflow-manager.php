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
        delete_post_meta($post_id, '_omeda_workflow_log');
        $this->log_status($post_id, 'Workflow Initiated: Post saved as draft with a Deployment Type.');

        // Use a placeholder date far in the future. Omeda requires a date on creation.
        // This will be updated to the real date when the post is scheduled/published.
        $config = $this->prepare_configuration($post_id, $config_id, '2099-01-01 12:00');
        if (!$config) {
            $this->log_error($post_id, 'Failed to prepare configuration. Workflow aborted.');
            return;
        }

        try {
            // Step 1: Create the deployment
            $track_id = $this->api_client->step1_create_deployment($config);
            update_post_meta($post_id, '_omeda_track_id', $track_id);
            $this->log_status($post_id, "Step 1/3 Complete: Deployment created with TrackID: {$track_id}");

            // Step 2: Assign the audience
            $this->api_client->step2_assign_audience($track_id, $config);
            $this->log_status($post_id, 'Step 2/3 Complete: Audience assigned.');

            // Step 3: Add the initial content
            $this->update_content($post_id, $track_id, $config_id);

        } catch (Exception $e) {
            $this->log_error($post_id, 'Initial deployment creation failed: ' . $e->getMessage());
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
                $this->log_error($post_id, 'Could not prepare configuration for content update.');
                return;
            }
        }

        try {
            $this->api_client->step3_add_content($track_id, $config);
            $this->log_status($post_id, 'Content updated successfully in Omeda.');
        } catch (Exception $e) {
            $this->log_error($post_id, 'Failed to update content in Omeda: ' . $e->getMessage());
        }
    }

    /**
     * Sends a test email and schedules the final deployment.
     */
    public function schedule_and_send_test($post_id, $track_id, $config_id) {
        $this->log_status($post_id, 'Post Published/Scheduled. Finalizing deployment...');

        $config = $this->prepare_configuration($post_id, $config_id);
        if (!$config) {
            $this->log_error($post_id, 'Could not prepare configuration for final scheduling.');
            return;
        }

        try {
            // Step 1: Update the content to ensure the latest version is used
            $this->update_content($post_id, $track_id, $config_id, $config);

            // Step 2: Send the test email
            $this->send_test($post_id, $track_id, $config, false); // `false` to prevent duplicate logging

            // Step 3: Schedule the deployment with the final, calculated date
            $this->api_client->step5_schedule_deployment($track_id, $config);
            $this->log_status($post_id, "Deployment successfully scheduled for: {$config['ScheduleDate']} (UTC).");
            $this->log_status($post_id, 'Workflow Complete.');

        } catch (Exception $e) {
            $this->log_error($post_id, 'Failed to schedule or send test: ' . $e->getMessage());
        }
    }

    /**
     * Sends a test email for an existing deployment.
     * @param array $config The prepared configuration array.
     * @param bool  $log_status Whether to write a standalone log entry for this action.
     */
    public function send_test($post_id, $track_id, $config, $log_status = true) {
        if ($log_status) {
            $this->log_status($post_id, 'Sending new test email due to post update...');
        }

        if (!$config) {
            $this->log_error($post_id, 'Could not send test: Configuration was not provided.');
            return;
        }

        try {
            $this->api_client->step4_send_test($track_id, $config);
            $this->log_status($post_id, 'Test email sent successfully.');
        } catch (Exception $e) {
            // Log a warning instead of an error, as a failed test is not a critical workflow failure.
            $this->log_warning($post_id, 'Failed to send test email (check Omeda tester configuration): ' . $e->getMessage());
        }
    }

     /**
     * Merges global settings, deployment type settings, and post data.
     * @param int $post_id The ID of the WordPress post.
     * @param int $config_id The ID of the Deployment Type configuration post.
     * @param string|null $override_schedule_date Optional. A specific date string to use instead of the post's date.
     * @return array|null The merged configuration array or null on failure.
     */
    private function prepare_configuration($post_id, $config_id, $override_schedule_date = null) {
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
        $config['DeploymentName'] = $post->post_title;

        // Determine Subject
        if (isset($config['SubjectFormat']) && !empty($config['SubjectFormat'])) {
            $config['Subject'] = $config['SubjectFormat'];
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

    // --- Logging Helpers ---
    private function log_status($post_id, $message) {
        $this->add_to_workflow_log($post_id, $message, 'INFO');
    }

    private function log_warning($post_id, $message) {
        $this->add_to_workflow_log($post_id, $message, 'WARN');
    }

    private function log_error($post_id, $error_message, $context = null) {
        // Check if the error message is a JSON string from our API client
        $decoded_error = json_decode($error_message, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded_error)) {
            // It's a structured error from our API client
            $message = $decoded_error['summary'];
            $context = [
                'endpoint' => $decoded_error['endpoint'] ?? 'N/A',
                'payload' => $decoded_error['payload'] ?? null,
                'response_body' => $decoded_error['response_body'] ?? null
            ];
        } else {
            // It's a standard text error message
            $message = $error_message;
        }

        $this->add_to_workflow_log($post_id, $message, 'ERROR', $context);
        error_log("Omeda Workflow Error (Post ID {$post_id}): {$message}");
    }

    private function add_to_workflow_log($post_id, $message, $level, $context = null) {
        $log_entry = [
            'timestamp' => current_time('Y-m-d H:i:s'),
            'level' => $level,
            'message' => $message,
            'context' => $context
        ];

        // Store as a JSON string in post meta
        add_post_meta($post_id, '_omeda_workflow_log', json_encode($log_entry));
    }
}
