<?php
/**
 * Handles asynchronous job processing using Action Scheduler.
 * 
 * This class registers all Action Scheduler hooks and implements
 * the job handlers for the Omeda deployment workflow.
 */
class Omeda_Async_Jobs {

    private $workflow_manager;

    const GROUP_NAME = 'omeda-deployment';
    const MAX_RETRIES = 3;
    
    // Job hook names
    const HOOK_CREATE_DEPLOYMENT = 'omeda_async_create_deployment';
    const HOOK_ASSIGN_AUDIENCE = 'omeda_async_assign_audience';
    const HOOK_ADD_CONTENT = 'omeda_async_add_content';
    const HOOK_UPDATE_CONTENT = 'omeda_async_update_content';
    const HOOK_SEND_TEST = 'omeda_async_send_test';
    const HOOK_SCHEDULE_DEPLOYMENT = 'omeda_async_schedule_deployment';

    public function __construct(Omeda_Workflow_Manager $workflow_manager) {
        $this->workflow_manager = $workflow_manager;
    }

    public function init() {
        // Register all action hooks (work with both WP-Cron and Action Scheduler)
        add_action(self::HOOK_CREATE_DEPLOYMENT, array($this, 'handle_create_deployment'), 10, 3);
        add_action(self::HOOK_ASSIGN_AUDIENCE, array($this, 'handle_assign_audience'), 10, 4);
        add_action(self::HOOK_ADD_CONTENT, array($this, 'handle_add_content'), 10, 4);
        add_action(self::HOOK_UPDATE_CONTENT, array($this, 'handle_update_content'), 10, 4);
        add_action(self::HOOK_SEND_TEST, array($this, 'handle_send_test'), 10, 4);
        add_action(self::HOOK_SCHEDULE_DEPLOYMENT, array($this, 'handle_schedule_deployment'), 10, 4);
    }

    /**
     * Determine if we should use Action Scheduler or WP-Cron.
     * Action Scheduler for development/staging, WP-Cron for production.
     * 
     * @return string 'action_scheduler', 'wp_cron', or 'sync'
     */
    private function get_scheduling_method() {
        // Check if we're in production
        $is_production = (defined('WP_ENV') && WP_ENV === 'production') || 
                         wp_get_environment_type() === 'production';
        
        if ($is_production) {
            // Production: Use native WP-Cron
            return 'wp_cron';
        } else {
            // Development/Staging: Use Action Scheduler if available
            if (function_exists('as_schedule_single_action')) {
                return 'action_scheduler';
            } else {
                // Fallback to synchronous
                return 'sync';
            }
        }
    }

    /**
     * Schedule the initial deployment creation job.
     * Uses Action Scheduler in dev, WP-Cron in production.
     * 
     * @param int $post_id The post ID.
     * @param int $config_id The deployment type configuration ID.
     * @param int $debounce_seconds How long to wait before execution (default: 0 = immediate).
     */
    public function schedule_create_deployment($post_id, $config_id, $debounce_seconds = 0) {
        // For immediate execution (0 delay), execute synchronously to avoid Action Scheduler delay
        if ($debounce_seconds == 0) {
            $this->workflow_manager->log_status($post_id, 'Creating deployment (synchronous execution)...');
            $this->handle_create_deployment($post_id, $config_id, 0);
            return;
        }

        // For delayed execution, use async methods
        $method = $this->get_scheduling_method();
        $method_name = $method === 'action_scheduler' ? 'Action Scheduler' : 'WP-Cron';
        
        if ($method === 'action_scheduler') {
            $this->schedule_with_action_scheduler(
                self::HOOK_CREATE_DEPLOYMENT,
                array('post_id' => $post_id, 'config_id' => $config_id, 'retry_count' => 0),
                $debounce_seconds
            );
        } elseif ($method === 'wp_cron') {
            $this->schedule_with_wp_cron(
                self::HOOK_CREATE_DEPLOYMENT,
                array('post_id' => $post_id, 'config_id' => $config_id, 'retry_count' => 0),
                $debounce_seconds
            );
        }

        $this->workflow_manager->log_status(
            $post_id, 
            sprintf('Deployment creation scheduled via %s (will execute in %d seconds).', 
                $method_name, 
                $debounce_seconds)
        );
    }

    /**
     * Schedule a content update job.
     * 
     * @param int $post_id The post ID.
     * @param string $track_id The Omeda Track ID.
     * @param int $config_id The deployment type configuration ID.
     * @param int $debounce_seconds How long to wait before execution (default: 0 = immediate).
     */
    public function schedule_update_content($post_id, $track_id, $config_id, $debounce_seconds = 0) {
        // For immediate execution (0 delay), execute synchronously
        if ($debounce_seconds == 0) {
            $this->workflow_manager->log_status($post_id, 'Updating content (synchronous execution)...');
            $this->handle_update_content($post_id, $track_id, $config_id, 0);
            return;
        }

        // For delayed execution, use async methods
        $method = $this->get_scheduling_method();
        $method_name = $method === 'action_scheduler' ? 'Action Scheduler' : 'WP-Cron';
        
        if ($method === 'action_scheduler') {
            $this->schedule_with_action_scheduler(
                self::HOOK_UPDATE_CONTENT,
                array('post_id' => $post_id, 'track_id' => $track_id, 'config_id' => $config_id, 'retry_count' => 0),
                $debounce_seconds
            );
        } elseif ($method === 'wp_cron') {
            $this->schedule_with_wp_cron(
                self::HOOK_UPDATE_CONTENT,
                array('post_id' => $post_id, 'track_id' => $track_id, 'config_id' => $config_id, 'retry_count' => 0),
                $debounce_seconds
            );
        }

        $this->workflow_manager->log_status(
            $post_id, 
            sprintf('Content update scheduled via %s (will execute in %d seconds).', 
                $method_name,
                $debounce_seconds)
        );
    }

    /**
     * Schedule the final deployment sequence (test + schedule).
     * 
     * @param int $post_id The post ID.
     * @param string $track_id The Omeda Track ID.
     * @param int $config_id The deployment type configuration ID.
     */
    public function schedule_finalize_deployment($post_id, $track_id, $config_id) {
        $method = $this->get_scheduling_method();
        $method_name = $method === 'action_scheduler' ? 'Action Scheduler' : 'WP-Cron';
        
        if ($method === 'action_scheduler') {
            $this->finalize_with_action_scheduler($post_id, $track_id, $config_id);
        } elseif ($method === 'wp_cron') {
            $this->finalize_with_wp_cron($post_id, $track_id, $config_id);
        }

        $this->workflow_manager->log_status($post_id, 
            sprintf('Final deployment sequence scheduled via %s (update → test → schedule).', 
                $method_name));
    }

    /**
     * Manually trigger a test email send.
     * 
     * @param int $post_id The post ID.
     * @param string $track_id The Omeda Track ID.
     * @param int $config_id The deployment type configuration ID.
     * @return array Result array with 'success' boolean and 'message' string.
     */
    public function send_test_email($post_id, $track_id, $config_id) {
        try {
            $config = $this->workflow_manager->prepare_configuration($post_id, $config_id);
            if (!$config) {
                return ['success' => false, 'message' => 'Failed to prepare configuration.'];
            }

            // First update content to ensure latest version is used
            $api_client = omeda_wp_integration()->get_api_client();
            $api_client->step3_add_content($track_id, $config);
            $this->workflow_manager->log_status($post_id, 'Content updated before sending test.');

            // Send test
            $api_client->step4_send_test($track_id, $config);
            $this->workflow_manager->log_status($post_id, 'Test email sent successfully.');

            // Mark that test has been sent
            update_post_meta($post_id, '_omeda_test_sent', current_time('mysql'));

            return ['success' => true, 'message' => 'Test email sent successfully.'];

        } catch (Exception $e) {
            $error_msg = 'Failed to send test email: ' . $e->getMessage();
            $this->workflow_manager->log_error($post_id, $error_msg);
            return ['success' => false, 'message' => $error_msg];
        }
    }

    /**
     * Schedule the deployment for a specific date/time.
     * 
     * @param int $post_id The post ID.
     * @param string $track_id The Omeda Track ID.
     * @param int $config_id The deployment type configuration ID.
     * @param string $schedule_date The deployment date in 'Y-m-d H:i' format (UTC).
     * @return array Result array with 'success' boolean and 'message' string.
     */
    public function schedule_deployment($post_id, $track_id, $config_id, $schedule_date) {
        try {
            $config = $this->workflow_manager->prepare_configuration($post_id, $config_id, $schedule_date);
            if (!$config) {
                return ['success' => false, 'message' => 'Failed to prepare configuration.'];
            }

            // Update content one more time before scheduling
            $api_client = omeda_wp_integration()->get_api_client();
            $api_client->step3_add_content($track_id, $config);
            $this->workflow_manager->log_status($post_id, 'Content updated before scheduling.');

            // Schedule the deployment
            $api_client->step5_schedule_deployment($track_id, $config);
            $this->workflow_manager->log_status($post_id, "Deployment scheduled for: {$schedule_date} (UTC).");

            // Mark deployment as scheduled
            update_post_meta($post_id, '_omeda_deployment_scheduled', current_time('mysql'));
            update_post_meta($post_id, '_omeda_schedule_date', $schedule_date);

            return ['success' => true, 'message' => 'Deployment scheduled successfully.'];

        } catch (Exception $e) {
            $error_msg = 'Failed to schedule deployment: ' . $e->getMessage();
            $this->workflow_manager->log_error($post_id, $error_msg);
            return ['success' => false, 'message' => $error_msg];
        }
    }

    /**
     * Unschedule a deployment in Omeda.
     * 
     * @param int $post_id The post ID.
     * @param string $track_id The Omeda Track ID.
     * @return array Result array with 'success' boolean and 'message' string.
     */
    public function unschedule_deployment($post_id, $track_id) {
        try {
            // Call API to cancel/unschedule deployment
            $api_client = omeda_wp_integration()->get_api_client();
            // Note: You'll need to implement this method in the API client
            // $api_client->cancel_deployment($track_id);
            
            $this->workflow_manager->log_status($post_id, 'Deployment unscheduled.');

            // Remove scheduled status
            delete_post_meta($post_id, '_omeda_deployment_scheduled');
            delete_post_meta($post_id, '_omeda_schedule_date');

            return ['success' => true, 'message' => 'Deployment unscheduled successfully.'];

        } catch (Exception $e) {
            $error_msg = 'Failed to unschedule deployment: ' . $e->getMessage();
            $this->workflow_manager->log_error($post_id, $error_msg);
            return ['success' => false, 'message' => $error_msg];
        }
    }

    // ============================================================================
    // ACTION SCHEDULER METHODS (Development/Staging)
    // ============================================================================

    /**
     * Schedule a job using Action Scheduler.
     */
    private function schedule_with_action_scheduler($hook, $args, $delay_seconds) {
        // Cancel any existing pending jobs (debouncing)
        $this->cancel_action_scheduler_jobs($hook, array('post_id' => $args['post_id']));

        // Schedule new job
        as_schedule_single_action(
            time() + $delay_seconds,
            $hook,
            $args,
            self::GROUP_NAME
        );

        // If immediate execution, manually trigger the queue runner
        if ($delay_seconds == 0) {
            $this->trigger_action_scheduler_queue();
        }
    }

    /**
     * Schedule finalization sequence with Action Scheduler.
     */
    private function finalize_with_action_scheduler($post_id, $track_id, $config_id) {
        // Cancel any existing pending jobs
        $this->cancel_action_scheduler_jobs(self::HOOK_UPDATE_CONTENT, ['post_id' => $post_id]);
        $this->cancel_action_scheduler_jobs(self::HOOK_SEND_TEST, ['post_id' => $post_id]);
        $this->cancel_action_scheduler_jobs(self::HOOK_SCHEDULE_DEPLOYMENT, ['post_id' => $post_id]);

        // Schedule jobs
        as_schedule_single_action(
            time(),
            self::HOOK_UPDATE_CONTENT,
            array('post_id' => $post_id, 'track_id' => $track_id, 'config_id' => $config_id, 'retry_count' => 0),
            self::GROUP_NAME
        );

        as_schedule_single_action(
            time() + 30,
            self::HOOK_SEND_TEST,
            array('post_id' => $post_id, 'track_id' => $track_id, 'config_id' => $config_id, 'retry_count' => 0),
            self::GROUP_NAME
        );

        as_schedule_single_action(
            time() + 60,
            self::HOOK_SCHEDULE_DEPLOYMENT,
            array('post_id' => $post_id, 'track_id' => $track_id, 'config_id' => $config_id, 'retry_count' => 0),
            self::GROUP_NAME
        );

        // Trigger queue runner for immediate execution
        $this->trigger_action_scheduler_queue();
    }

    /**
     * Cancel Action Scheduler jobs.
     */
    private function cancel_action_scheduler_jobs($hook, $args_subset) {
        if (function_exists('as_unschedule_all_actions')) {
            as_unschedule_all_actions($hook, $args_subset, self::GROUP_NAME);
        }
    }

    // ============================================================================
    // WP-CRON METHODS (Production)
    // ============================================================================

    /**
     * Schedule a job using native WP-Cron.
     */
    private function schedule_with_wp_cron($hook, $args, $delay_seconds) {
        // Cancel any existing pending jobs (debouncing)
        $this->cancel_wp_cron_jobs($hook, $args['post_id']);

        // Schedule new job
        wp_schedule_single_event(
            time() + $delay_seconds,
            $hook,
            $args
        );
    }

    /**
     * Schedule finalization sequence with WP-Cron.
     */
    private function finalize_with_wp_cron($post_id, $track_id, $config_id) {
        // Cancel any existing pending jobs
        $this->cancel_wp_cron_jobs(self::HOOK_UPDATE_CONTENT, $post_id);
        $this->cancel_wp_cron_jobs(self::HOOK_SEND_TEST, $post_id);
        $this->cancel_wp_cron_jobs(self::HOOK_SCHEDULE_DEPLOYMENT, $post_id);

        // Schedule jobs
        wp_schedule_single_event(
            time(),
            self::HOOK_UPDATE_CONTENT,
            array('post_id' => $post_id, 'track_id' => $track_id, 'config_id' => $config_id, 'retry_count' => 0)
        );

        wp_schedule_single_event(
            time() + 30,
            self::HOOK_SEND_TEST,
            array('post_id' => $post_id, 'track_id' => $track_id, 'config_id' => $config_id, 'retry_count' => 0)
        );

        wp_schedule_single_event(
            time() + 60,
            self::HOOK_SCHEDULE_DEPLOYMENT,
            array('post_id' => $post_id, 'track_id' => $track_id, 'config_id' => $config_id, 'retry_count' => 0)
        );
    }

    /**
     * Cancel WP-Cron jobs for a specific post.
     */
    private function cancel_wp_cron_jobs($hook, $post_id) {
        $crons = _get_cron_array();
        if (empty($crons)) {
            return;
        }

        // Iterate through all scheduled cron jobs
        foreach ($crons as $timestamp => $cron) {
            if (isset($cron[$hook])) {
                foreach ($cron[$hook] as $signature => $job) {
                    // Check if this job is for our post_id
                    if (isset($job['args'][0]['post_id']) && $job['args'][0]['post_id'] == $post_id) {
                        wp_unschedule_event($timestamp, $hook, $job['args']);
                    }
                }
            }
        }
    }

    /**
     * Job Handler: Create Deployment
     */
    public function handle_create_deployment($post_id, $config_id, $retry_count = 0) {
        $step_name = 'create_deployment';
        try {
            $this->workflow_manager->log_advanced($post_id, 'Starting deployment creation transaction...', $step_name);
            $this->workflow_manager->log_status($post_id, 'Executing: Create deployment job...', $step_name, $retry_count > 0 ? $retry_count : null);
            
            // Check if deployment already exists
            $existing_track_id = get_post_meta($post_id, '_omeda_track_id', true);
            if (!empty($existing_track_id)) {
                $this->workflow_manager->log_status($post_id, 'Deployment already exists. Skipping creation.', $step_name);
                return;
            }

            // Calculate the next nearest hour for the deployment date
            $next_hour_timestamp = ceil(time() / 3600) * 3600;
            $next_hour_date = gmdate('Y-m-d H:i', $next_hour_timestamp);

            // Use workflow manager's existing method
            $config = $this->workflow_manager->prepare_configuration($post_id, $config_id, $next_hour_date);
            if (!$config) {
                throw new Exception('Failed to prepare configuration.');
            }

            // Validate required fields
            if (empty($config['DeploymentTypeId'])) {
                throw new Exception('DeploymentTypeId is required but not configured.');
            }
            
            // Log request if raw logging enabled
            $this->workflow_manager->log_raw($post_id, 'Creating deployment with configuration', $config, $step_name);

            // Create deployment
            $api_client = omeda_wp_integration()->get_api_client();
            $track_id = $api_client->step1_create_deployment($config);
            update_post_meta($post_id, '_omeda_track_id', $track_id);
            
            $this->workflow_manager->log_advanced($post_id, 'Deployment creation transaction completed successfully.', $step_name);
            $this->workflow_manager->log_status($post_id, "Step 1/3 Complete: Deployment created with TrackID: {$track_id}", $step_name);

            // Chain next job: Assign Audience (execute synchronously to ensure immediate execution)
            $this->handle_assign_audience($post_id, $track_id, $config_id, 0);

        } catch (Exception $e) {
            $this->handle_job_error($post_id, $step_name, $e, $retry_count, 
                array('post_id' => $post_id, 'config_id' => $config_id));
        }
    }

    /**
     * Job Handler: Assign Audience
     */
    public function handle_assign_audience($post_id, $track_id, $config_id, $retry_count = 0) {
        $step_name = 'assign_audience';
        try {
            $this->workflow_manager->log_advanced($post_id, 'Starting audience assignment transaction...', $step_name);
            $this->workflow_manager->log_status($post_id, 'Executing: Assign audience job...', $step_name, $retry_count > 0 ? $retry_count : null);
            
            $config = $this->workflow_manager->prepare_configuration($post_id, $config_id);
            if (!$config) {
                throw new Exception('Failed to prepare configuration.');
            }
            
            // Log request if raw logging enabled
            $audience_info = [
                'track_id' => $track_id,
                'EncryptedCustomerKey' => $config['EncryptedCustomerKey'] ?? 'Not set'
            ];
            $this->workflow_manager->log_raw($post_id, 'Assigning audience', $audience_info, $step_name);

            // Assign audience
            $api_client = omeda_wp_integration()->get_api_client();
            $api_client->step2_assign_audience($track_id, $config);
            
            $this->workflow_manager->log_advanced($post_id, 'Audience assignment transaction completed successfully.', $step_name);
            $this->workflow_manager->log_status($post_id, 'Step 2/3 Complete: Audience assigned.', $step_name);

            // Chain next job: Add Content (execute synchronously)
            $this->handle_add_content($post_id, $track_id, $config_id, 0);

        } catch (Exception $e) {
            $this->handle_job_error($post_id, $step_name, $e, $retry_count, 
                array('post_id' => $post_id, 'track_id' => $track_id, 'config_id' => $config_id));
        }
    }

    /**
     * Job Handler: Add Content
     */
    public function handle_add_content($post_id, $track_id, $config_id, $retry_count = 0, $step_name = 'add_content') {
        try {
            $this->workflow_manager->log_advanced($post_id, 'Starting content addition transaction...', $step_name);
            $this->workflow_manager->log_status($post_id, 'Executing: Add content job...', $step_name, $retry_count > 0 ? $retry_count : null);
            
            $config = $this->workflow_manager->prepare_configuration($post_id, $config_id);
            if (!$config) {
                throw new Exception('Failed to prepare configuration.');
            }

            // Log content details for debugging
            $content_length = isset($config['HtmlContent']) ? strlen($config['HtmlContent']) : 0;
            $this->workflow_manager->log_status($post_id, sprintf('Sending content (%d chars) to Omeda...', $content_length), $step_name);
            $this->workflow_manager->log_advanced($post_id, 'Content details prepared for submission.', $step_name);
            $this->workflow_manager->log_raw($post_id, 'Content details', $config, $step_name);

            // Log request if raw logging enabled
            $content_info = [
                'track_id' => $track_id,
                'subject' => $config['Subject'] ?? 'Not set',
                'content_length' => $content_length,
                'html_content' => $config['HtmlContent'] ?? 'Not set'
            ];
            $this->workflow_manager->log_raw($post_id, 'Adding content', $content_info, $step_name);

            // Add content
            $api_client = omeda_wp_integration()->get_api_client();
            $result = $api_client->step3_add_content($track_id, $config);
            
            // Log result
            if (is_array($result) && isset($result['Warnings'])) {
                $warning_count = count($result['Warnings']);
                $this->workflow_manager->log_warning($post_id, sprintf('Content added with %d warning(s) from Omeda.', $warning_count), $step_name);
                foreach ($result['Warnings'] as $warning) {
                    $this->workflow_manager->log_warning($post_id, '  - ' . $warning, $step_name);
                }
            }
            
            $this->workflow_manager->log_advanced($post_id, 'Content addition transaction completed successfully.', $step_name);
            $this->workflow_manager->log_status($post_id, 'Step 3/3 Complete: Initial content added. Deployment ready. You may now send a test or schedule the deployment.', $step_name);

            // Mark deployment as ready for testing
            update_post_meta($post_id, '_omeda_deployment_ready', true);

        } catch (Exception $e) {
            $error_data = json_decode($e->getMessage(), true);
            if (is_array($error_data) && isset($error_data['response_body'])) {
                // Enhanced error logging with full details
                $error_summary = $error_data['summary'];
                $this->workflow_manager->log_error($post_id, 'Content assignment failed: ' . $error_summary, null, $step_name, $retry_count > 0 ? $retry_count : null);
                
                // ALWAYS log full error details for debugging (regardless of logging level)
                // This uses log_error_details which doesn't require 'raw' logging level
                $error_details = [
                    'url' => $error_data['url'] ?? 'N/A',
                    'method' => $error_data['method'] ?? 'N/A',
                    'http_code' => $error_data['http_code'] ?? 'N/A',
                    'request_payload' => $error_data['payload'] ?? 'N/A',
                    'response_body' => $error_data['response_body'] ?? 'N/A'
                ];
                $this->workflow_manager->log_error_details($post_id, 'API Request/Response Details', $error_details, $step_name);
                
                // Extract and log individual error messages
                if (isset($error_data['response_body']['Errors']) && is_array($error_data['response_body']['Errors'])) {
                    foreach ($error_data['response_body']['Errors'] as $err) {
                        if (isset($err['Error'])) {
                            $this->workflow_manager->log_error($post_id, '  → Omeda Error: ' . $err['Error'], null, $step_name);
                        }
                    }
                }
            } else {
                // Standard error logging if not structured
                $this->workflow_manager->log_error($post_id, 'Content assignment failed: ' . $e->getMessage(), null, $step_name, $retry_count > 0 ? $retry_count : null);
            }
            
            $this->handle_job_error($post_id, 'add_content', $e, $retry_count, 
                array('post_id' => $post_id, 'track_id' => $track_id, 'config_id' => $config_id));
        }
    }

    /**
     * Job Handler: Update Content
     */
    public function handle_update_content($post_id, $track_id, $config_id, $retry_count = 0) {
        $this->handle_add_content($post_id, $track_id, $config_id, $retry_count, 'update_content');
    }

    /**
     * Job Handler: Send Test
     */
    public function handle_send_test($post_id, $track_id, $config_id, $retry_count = 0) {
        try {
            $this->workflow_manager->log_status($post_id, 'Executing: Send test email job...');
            
            $config = $this->workflow_manager->prepare_configuration($post_id, $config_id);
            if (!$config) {
                throw new Exception('Failed to prepare configuration.');
            }

            // Send test
            $api_client = omeda_wp_integration()->get_api_client();
            $api_client->step4_send_test($track_id, $config);
            $this->workflow_manager->log_status($post_id, 'Step 4/5 Complete: Test email sent.');

        } catch (Exception $e) {
            // Test failures are warnings, not critical errors
            $this->workflow_manager->log_warning($post_id, 'Failed to send test email: ' . $e->getMessage());
        }
    }

    /**
     * Job Handler: Schedule Deployment
     */
    public function handle_schedule_deployment($post_id, $track_id, $config_id, $retry_count = 0) {
        try {
            $this->workflow_manager->log_status($post_id, 'Executing: Schedule deployment job...');
            
            $config = $this->workflow_manager->prepare_configuration($post_id, $config_id);
            if (!$config) {
                throw new Exception('Failed to prepare configuration.');
            }

            // Schedule deployment
            $api_client = omeda_wp_integration()->get_api_client();
            $api_client->step5_schedule_deployment($track_id, $config);
            $this->workflow_manager->log_status($post_id, "Step 5/5 Complete: Deployment scheduled for: {$config['ScheduleDate']} (UTC).");
            $this->workflow_manager->log_status($post_id, '✓ Workflow Complete.');

        } catch (Exception $e) {
            $this->handle_job_error($post_id, 'schedule_deployment', $e, $retry_count, 
                array('post_id' => $post_id, 'track_id' => $track_id, 'config_id' => $config_id));
        }
    }

    /**
     * Centralized error handling with exponential backoff retry.
     */
    private function handle_job_error($post_id, $job_name, $exception, $retry_count, $job_args) {
        // Log the error with full context
        $this->workflow_manager->log_error($post_id, $exception->getMessage(), null, $job_name, $retry_count);
        
        // Log advanced details if enabled
        $this->workflow_manager->log_advanced($post_id, 'Transaction failed: ' . $exception->getMessage(), $job_name);

        if ($retry_count < self::MAX_RETRIES) {
            $delay = 60 * pow(2, $retry_count); // Exponential backoff: 60s, 120s, 240s
            $retry_count++;
            $job_args['retry_count'] = $retry_count;

            $hook_name = constant("self::HOOK_" . strtoupper($job_name));
            
            // Schedule retry based on environment
            $method = $this->get_scheduling_method();
            if ($method === 'action_scheduler') {
                as_schedule_single_action(
                    time() + $delay,
                    $hook_name,
                    $job_args,
                    self::GROUP_NAME
                );
            } elseif ($method === 'wp_cron') {
                wp_schedule_single_event(
                    time() + $delay,
                    $hook_name,
                    $job_args
                );
            }

            $this->workflow_manager->log_status(
                $post_id, 
                sprintf('Retry %d/%d scheduled for %s (will execute in %d seconds).', 
                    $retry_count, self::MAX_RETRIES, $job_name, $delay),
                $job_name,
                $retry_count
            );
        } else {
            $this->workflow_manager->log_error($post_id, "Max retries exceeded for {$job_name}. Workflow aborted.", null, $job_name, self::MAX_RETRIES);
        }
    }

    /**
     * Get pending jobs for a specific post.
     * Returns jobs from either Action Scheduler or WP-Cron depending on environment.
     */
    public function get_pending_jobs($post_id) {
        $method = $this->get_scheduling_method();
        
        if ($method === 'action_scheduler' && function_exists('as_get_scheduled_actions')) {
            return $this->get_action_scheduler_jobs($post_id);
        } elseif ($method === 'wp_cron') {
            return $this->get_wp_cron_jobs($post_id);
        }
        
        return [];
    }

    /**
     * Get pending jobs from Action Scheduler.
     */
    private function get_action_scheduler_jobs($post_id) {
        $jobs = [];
        $hooks = [
            self::HOOK_CREATE_DEPLOYMENT,
            self::HOOK_ASSIGN_AUDIENCE,
            self::HOOK_ADD_CONTENT,
            self::HOOK_UPDATE_CONTENT,
            self::HOOK_SEND_TEST,
            self::HOOK_SCHEDULE_DEPLOYMENT
        ];

        foreach ($hooks as $hook) {
            $actions = as_get_scheduled_actions([
                'hook' => $hook,
                'args' => ['post_id' => $post_id],
                'status' => ActionScheduler_Store::STATUS_PENDING,
                'per_page' => 10
            ], 'ARRAY_A');

            foreach ($actions as $action) {
                $jobs[] = [
                    'hook' => $hook,
                    'scheduled' => $action['date'],
                    'args' => $action['args']
                ];
            }
        }

        return $jobs;
    }

    /**
     * Get pending jobs from WP-Cron.
     */
    private function get_wp_cron_jobs($post_id) {
        $jobs = [];
        $crons = _get_cron_array();
        
        if (empty($crons)) {
            return $jobs;
        }

        $hooks = [
            self::HOOK_CREATE_DEPLOYMENT,
            self::HOOK_ASSIGN_AUDIENCE,
            self::HOOK_ADD_CONTENT,
            self::HOOK_UPDATE_CONTENT,
            self::HOOK_SEND_TEST,
            self::HOOK_SCHEDULE_DEPLOYMENT
        ];

        foreach ($crons as $timestamp => $cron) {
            foreach ($hooks as $hook) {
                if (isset($cron[$hook])) {
                    foreach ($cron[$hook] as $signature => $job) {
                        if (isset($job['args'][0]['post_id']) && $job['args'][0]['post_id'] == $post_id) {
                            $jobs[] = [
                                'hook' => $hook,
                                'scheduled' => (object)['date' => date('Y-m-d H:i:s', $timestamp)],
                                'args' => $job['args']
                            ];
                        }
                    }
                }
            }
        }

        return $jobs;
    }

    /**
     * Manually trigger Action Scheduler queue runner.
     * This is needed in development environments where WP-Cron might not be triggered automatically.
     */
    private function trigger_action_scheduler_queue() {
        if (function_exists('ActionScheduler_QueueRunner') && class_exists('ActionScheduler_QueueRunner')) {
            // Get the queue runner instance
            $runner = ActionScheduler_QueueRunner::instance();
            // Process one batch of actions
            $runner->run();
        }
    }
}
