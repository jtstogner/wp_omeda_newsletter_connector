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
        // Register the single WP-Cron hook responsible for processing all steps
        add_action(self::CRON_HOOK, array($this, 'process_step'), 10, 5);
    }

    /**
     * Initiates the workflow (Step 1).
     */
    public function initiate_workflow($post_id, $config_id) {
        // Clear previous logs
        delete_post_meta($post_id, '_omeda_workflow_log');
        
        // Ensure config ID is stored correctly before starting
        update_post_meta($post_id, '_omeda_config_id', $config_id);

        $config = $this->prepare_configuration($post_id, $config_id);
        if (!$config) {
            $this->log_error($post_id, 'Failed to prepare configuration. Workflow aborted.');
            return false;
        }

        try {
            // Step 1: Create Deployment (Synchronous)
            $track_id = $this->api_client->step1_create_deployment($config);

            // Store the TrackId
            update_post_meta($post_id, '_omeda_track_id', $track_id);
            $this->log_status($post_id, 'Step 1 Complete (Create Deployment). TrackID: ' . $track_id);

            // Schedule Step 2 (Asynchronous) with a slight initial delay
            $this->schedule_step($post_id, $track_id, $config_id, 2, 0, 5);
            return true;

        } catch (Exception $e) {
            $this->log_error($post_id, 'Step 1 Failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * The main WP-Cron handler function.
     */
    public function process_step($post_id, $track_id, $config_id, $current_step, $retry_count = 0) {
        
        // Safety check: Ensure the Track ID hasn't changed
        $current_track_id_in_db = get_post_meta($post_id, '_omeda_track_id', true);
        if ($current_track_id_in_db !== $track_id) {
            return;
        }

        $config = $this->prepare_configuration($post_id, $config_id);
        if (!$config) {
             $this->log_error($post_id, "Step {$current_step} failed: Configuration could not be reloaded.");
            return;
        }

        try {
            // 1. Check Prerequisites (Polling)
            $is_ready = $this->check_prerequisites($track_id, $current_step);

            if (!$is_ready) {
                // 2. Handle "Not Ready" state
                if ($retry_count >= self::MAX_RETRIES) {
                    $this->log_error($post_id, "Step {$current_step} Failed: Max retries exceeded waiting for Omeda processing.");
                    return;
                }
                // Reschedule this step using the POLLING_INTERVAL
                $this->schedule_step($post_id, $track_id, $config_id, $current_step, $retry_count + 1, self::POLLING_INTERVAL);
                $this->log_status($post_id, "Step {$current_step} waiting for Omeda. Retrying ({$retry_count}/" . self::MAX_RETRIES . ")...");
                return;
            }

            // 3. Execute the Step
            $this->execute_api_step($current_step, $track_id, $config);
            $step_names = [2 => 'Assign Audience', 3 => 'Add Content', 4 => 'Send Test', 5 => 'Schedule Deployment'];
            $this->log_status($post_id, "Step {$current_step} Complete ({$step_names[$current_step]}).");

            // 4. Schedule the next step immediately
            $next_step = $current_step + 1;
            if ($next_step <= 5) {
                $this->schedule_step($post_id, $track_id, $config_id, $next_step, 0, 0);
            } else {
                $this->log_status($post_id, 'Workflow Complete. Deployment Scheduled successfully.');
            }

        } catch (Exception $e) {
            $this->log_error($post_id, "Step {$current_step} Execution Failed: " . $e->getMessage());
        }
    }

    /**
     * Checks if the prerequisites for a given step are met by polling Omeda.
     */
    private function check_prerequisites($track_id, $step) {
        switch ($step) {
            case 2:
                // Step 2 requires Step 1 to be complete (deployment exists).
                return !is_null($this->api_client->get_deployment_lookup($track_id));
            case 3:
            case 4:
            case 5:
                // Steps 3, 4, and 5 require audience processing (Step 2) to be finished.
                return $this->api_client->is_audience_ready($track_id);
        }
        return false;
    }

    /**
     * Routes the step number to the corresponding API client method.
     */
    private function execute_api_step($step, $track_id, $config) {
        switch ($step) {
            case 2:
                $this->api_client->step2_assign_audience($track_id, $config);
                break;
            case 3:
                $this->api_client->step3_add_content($track_id, $config);
                break;
            case 4:
                // Step 4 (Send Test)
                try {
                     $this->api_client->step4_send_test($track_id, $config);
                } catch (Exception $e) {
                    // Log a warning but continue the workflow, as the test might not be critical.
                    $this->log_warning($config['PostId'], 'Step 4 (Send Test) failed (Check Omeda tester configuration): ' . $e->getMessage());
                }
                break;
            case 5:
                $this->api_client->step5_schedule_deployment($track_id, $config);
                break;
        }
    }

    /**
     * Schedules the WP-Cron event.
     */
    private function schedule_step($post_id, $track_id, $config_id, $step, $retry_count, $delay_seconds) {
        $timestamp = time() + $delay_seconds;
        $args = array($post_id, $track_id, $config_id, $step, $retry_count);

        wp_schedule_single_event($timestamp, self::CRON_HOOK, $args);
    }

     /**
     * Merges global settings, deployment type settings, and post data.
     */
    private function prepare_configuration($post_id, $config_id) {
        $post = get_post($post_id);
        if (!$post) return null;

        // Get configuration from the Deployment Type CPT
        $config = Omeda_Deployment_Types::get_configuration($config_id);
        if (!$config) return null;

        $config['PostId'] = $post_id;

        // Determine Schedule Date (Use GMT/UTC for API consistency)
        if ($post->post_status == 'future') {
            $schedule_date = get_gmt_from_date($post->post_date, 'Y-m-d H:i');
        } else {
            // Use [NOW] for immediate deployment if published
            $schedule_date = '[NOW]';
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

    private function log_error($post_id, $error_message) {
        $this->add_to_workflow_log($post_id, $error_message, 'ERROR');
        error_log("Omeda Workflow Error (Post ID {$post_id}): {$error_message}");
    }

    private function add_to_workflow_log($post_id, $message, $level) {
        $timestamp = current_time('Y-m-d H:i:s');
        $log_entry = "{$timestamp} [{$level}] {$message}";
        add_post_meta($post_id, '_omeda_workflow_log', $log_entry);
    }
}
