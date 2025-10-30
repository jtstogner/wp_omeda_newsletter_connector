<?php
/**
 * Integrates Omeda workflow with WordPress core hooks and the editor UI.
 */
class Omeda_Hooks {

    private $workflow_manager;

    public function __construct(Omeda_Workflow_Manager $workflow_manager) {
        $this->workflow_manager = $workflow_manager;
    }

    public function init() {
        add_action('add_meta_boxes', array($this, 'register_meta_boxes'));
        // The save_post hook will now be the main driver of the workflow.
        // It needs a higher priority (lower number) to run before potential redirects.
        add_action('save_post', array($this, 'handle_post_save'), 5, 1);
        add_action('transition_post_status', array($this, 'handle_status_transition'), 10, 3);

        // AJAX handlers for manual actions
        add_action('wp_ajax_omeda_send_test', array($this, 'ajax_send_test'));
        add_action('wp_ajax_omeda_schedule_deployment', array($this, 'ajax_schedule_deployment'));
        add_action('wp_ajax_omeda_unschedule_deployment', array($this, 'ajax_unschedule_deployment'));

        // Enqueue scripts for admin
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    private function get_supported_post_types() {
        // Define which post types this integration supports
        // 'post' for regular posts, 'newsletterglue' for Newsletter Glue campaigns
        return ['post', 'newsletterglue'];
    }

    public function register_meta_boxes() {
        foreach ($this->get_supported_post_types() as $post_type) {
            add_meta_box(
                'omeda_integration_box',
                'Omeda Deployment',
                array($this, 'render_meta_box'),
                $post_type,
                'side',
                'high'
            );
        }
    }

    public function render_meta_box($post) {
        wp_nonce_field('omeda_save_post_meta', 'omeda_post_meta_nonce');
        $selected_config_id = get_post_meta($post->ID, '_omeda_config_id', true);
        $track_id = get_post_meta($post->ID, '_omeda_track_id', true);
        $workflow_logs = get_post_meta($post->ID, '_omeda_workflow_log');
        $is_locked = !empty($track_id);
        $deployment_ready = get_post_meta($post->ID, '_omeda_deployment_ready', true);
        $test_sent = get_post_meta($post->ID, '_omeda_test_sent', true);
        $is_scheduled = get_post_meta($post->ID, '_omeda_deployment_scheduled', true);
        $schedule_date = get_post_meta($post->ID, '_omeda_schedule_date', true);
        
        // Get current override values
        $deployment_name_override = get_post_meta($post->ID, '_omeda_deployment_name', true);
        $campaign_id_override = get_post_meta($post->ID, '_omeda_campaign_id', true);

        // Try to auto-detect deployment type based on post type/template
        $auto_detected_config = null;
        if (empty($selected_config_id)) {
            $auto_detected_config = Omeda_Deployment_Types::find_config_for_post($post->ID);
            if ($auto_detected_config) {
                echo '<div class="notice notice-info inline"><p>';
                echo '<strong>Auto-detected:</strong> This post type/template is configured to use: ';
                echo '<strong>' . esc_html(get_the_title($auto_detected_config)) . '</strong>';
                echo '</p></div>';
            }
        }

        // Fetch available Deployment Types
        $deployment_types = get_posts([
            'post_type' => Omeda_Deployment_Types::CPT_SLUG,
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ]);

        echo '<p><label for="_omeda_config_id"><strong>Deployment Type:</strong></label></p>';

        // Lock the select field if a TrackId is present
        printf('<select name="_omeda_config_id" id="_omeda_config_id" style="width: 100%%;" %s>', $is_locked ? 'disabled="disabled"' : '');

        echo '<option value="">-- Do Not Deploy / Auto-Detect --</option>';
        foreach ($deployment_types as $type) {
            $is_selected = ($selected_config_id && $selected_config_id == $type->ID) || 
                           (!$selected_config_id && $auto_detected_config == $type->ID);
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($type->ID),
                selected($is_selected, true, false),
                esc_html($type->post_title)
            );
        }
        echo '</select>';

        if ($is_locked) {
            // Add a hidden field to ensure the value is still submitted,
            // as disabled fields are not.
            printf('<input type="hidden" name="_omeda_config_id" value="%s" />', esc_attr($selected_config_id));
            echo '<p class="description"><em>Deployment type is locked because a deployment has already been created in Omeda.</em></p>';
        } else {
            echo '<p class="description"><em>Leave as "Auto-Detect" to use the deployment type configured for this post type/template, or manually override.</em></p>';
        }
        
        // Get deployment name and campaign ID formats from the selected config
        $effective_config_id = $selected_config_id ?: $auto_detected_config;
        if ($effective_config_id) {
            $config = Omeda_Deployment_Types::get_configuration($effective_config_id);
            
            // Show deployment name field
            $deployment_name_format = !empty($config['DeploymentNameFormat']) ? $config['DeploymentNameFormat'] : '{post_title} - {post_date}';
            $deployment_name_placeholder = Omeda_Variable_Parser::parse($deployment_name_format, $post->ID);
            
            echo '<hr>';
            echo '<p><label for="_omeda_deployment_name"><strong>Deployment Name:</strong></label></p>';
            printf(
                '<input type="text" name="_omeda_deployment_name" id="_omeda_deployment_name" value="%s" placeholder="%s" style="width: 100%%;" %s />',
                esc_attr($deployment_name_override),
                esc_attr($deployment_name_placeholder),
                $is_locked ? 'readonly' : ''
            );
            echo '<p class="description">Leave empty to use: <code>' . esc_html($deployment_name_format) . '</code></p>';
            
            // Show campaign ID field
            $campaign_id_format = !empty($config['CampaignIdFormat']) ? $config['CampaignIdFormat'] : 'campaign-{post_id}-{post_date_ymd}';
            $campaign_id_placeholder = Omeda_Variable_Parser::parse($campaign_id_format, $post->ID);
            
            echo '<p style="margin-top: 10px;"><label for="_omeda_campaign_id"><strong>Campaign ID:</strong></label></p>';
            printf(
                '<input type="text" name="_omeda_campaign_id" id="_omeda_campaign_id" value="%s" placeholder="%s" style="width: 100%%;" %s />',
                esc_attr($campaign_id_override),
                esc_attr($campaign_id_placeholder),
                $is_locked ? 'readonly' : ''
            );
            echo '<p class="description">Leave empty to use: <code>' . esc_html($campaign_id_format) . '</code></p>';
        }

        echo '<hr>';

        if ($track_id) {
            echo '<p><strong>Omeda TrackID:</strong> ' . esc_html($track_id) . '</p>';
            
            // Action Buttons Section
            echo '<div id="omeda-actions" style="margin-top: 15px;">';
            
            if ($deployment_ready) {
                // Send Test Button
                echo '<button type="button" id="omeda-send-test-btn" class="button button-secondary" style="width: 100%; margin-bottom: 10px;">';
                echo '<span class="dashicons dashicons-email" style="vertical-align: middle;"></span> Send Test Email';
                echo '</button>';
                
                if ($test_sent) {
                    echo '<p style="color: #46b450; font-size: 0.9em; margin: 5px 0;"><span class="dashicons dashicons-yes"></span> Last test sent: ' . esc_html($test_sent) . '</p>';
                }

                // Schedule/Unschedule Section
                if (!$is_scheduled) {
                    echo '<div id="omeda-schedule-section" style="margin-top: 15px; padding: 10px; background: #f9f9f9; border: 1px solid #ddd;">';
                    echo '<p><strong>Schedule Deployment:</strong></p>';
                    echo '<input type="datetime-local" id="omeda-schedule-date" style="width: 100%; margin-bottom: 10px;" />';
                    echo '<label style="display: block; margin-bottom: 10px;">';
                    echo '<input type="checkbox" id="omeda-schedule-confirm" /> I confirm the deployment is ready to schedule';
                    echo '</label>';
                    echo '<button type="button" id="omeda-schedule-btn" class="button button-primary" style="width: 100%;" disabled>';
                    echo '<span class="dashicons dashicons-calendar-alt" style="vertical-align: middle;"></span> Schedule Deployment';
                    echo '</button>';
                    echo '</div>';
                } else {
                    echo '<div style="margin-top: 15px; padding: 10px; background: #e7f7e7; border: 1px solid #46b450;">';
                    echo '<p style="color: #46b450; margin: 0;"><span class="dashicons dashicons-yes"></span> <strong>Deployment Scheduled</strong></p>';
                    echo '<p style="margin: 5px 0;">Send date: ' . esc_html($schedule_date) . ' UTC</p>';
                    echo '<button type="button" id="omeda-unschedule-btn" class="button button-secondary" style="width: 100%;">';
                    echo '<span class="dashicons dashicons-no" style="vertical-align: middle;"></span> Unschedule';
                    echo '</button>';
                    echo '</div>';
                }
            } else {
                echo '<p><em>Deployment is being created. Please wait...</em></p>';
            }
            
            echo '</div>'; // end omeda-actions
        }

        // Show pending jobs if Action Scheduler is available
        if (function_exists('as_get_scheduled_actions')) {
            $async_jobs = omeda_wp_integration()->async_jobs;
            if ($async_jobs) {
                $pending_jobs = $async_jobs->get_pending_jobs($post->ID);
                if (!empty($pending_jobs)) {
                    echo '<hr><p><strong>Pending Jobs:</strong></p>';
                    echo '<ul style="margin: 0; padding-left: 20px; font-size: 0.9em;">';
                    foreach ($pending_jobs as $job) {
                        $job_name = str_replace('omeda_async_', '', $job['hook']);
                        $scheduled_time = isset($job['scheduled']->date) ? $job['scheduled']->date : $job['scheduled']->format('Y-m-d H:i:s');
                        echo '<li>' . esc_html(ucwords(str_replace('_', ' ', $job_name))) . ' - ' . esc_html($scheduled_time) . '</li>';
                    }
                    echo '</ul>';
                }
            }
        }

        if ($workflow_logs) {
            echo '<hr><h4>Workflow Log:</h4>';
            
            // Get summary
            $summary = (new Omeda_Workflow_Manager(new Omeda_API_Client()))->get_workflow_summary($post->ID);
            $status_color = '#999';
            $status_text = 'Pending';
            
            if ($summary['status'] === 'complete') {
                $status_color = '#46b450';
                $status_text = 'Complete';
            } elseif ($summary['status'] === 'in_progress') {
                $status_color = '#00a0d2';
                $status_text = 'In Progress';
            } elseif ($summary['status'] === 'error') {
                $status_color = '#dc3232';
                $status_text = 'Error';
            }
            
            echo '<div style="background: #f9f9f9; padding: 10px; margin-bottom: 10px; border-left: 4px solid ' . $status_color . ';">';
            echo '<strong>Status:</strong> <span style="color: ' . $status_color . ';">' . esc_html($status_text) . '</span><br>';
            if ($summary['last_step']) {
                echo '<strong>Last Step:</strong> ' . esc_html($summary['last_step']) . '<br>';
            }
            if ($summary['error_count'] > 0) {
                echo '<strong>Errors:</strong> ' . esc_html($summary['error_count']) . '<br>';
            }
            if ($summary['last_timestamp']) {
                echo '<strong>Last Update:</strong> ' . esc_html($summary['last_timestamp']) . '<br>';
            }
            echo '<a href="' . admin_url('admin.php?page=omeda-workflow-logs&view=details&post_id=' . $post->ID) . '" target="_blank" class="button button-small" style="margin-top: 5px;">View Full Logs</a>';
            echo '</div>';
            
            // Show recent log entries (last 5)
            $logging_level = get_option('omeda_logging_level', 'basic');
            echo '<div style="max-height: 200px; overflow-y: scroll; background: #f9f9f9; padding: 5px; font-size: 0.9em; font-family: monospace; border: 1px solid #eee;">';
            
            $recent_logs = array_slice(array_reverse($workflow_logs), 0, 5);
            foreach ($recent_logs as $log_json) {
                $log_data = json_decode($log_json, true);
                if (!$log_data) continue;
                
                // Filter based on logging level
                if ($logging_level === 'basic' && $log_data['level'] === 'DEBUG') continue;
                if ($logging_level !== 'raw' && $log_data['level'] === 'RAW') continue;

                $color = 'black';
                if ($log_data['level'] === 'ERROR') $color = '#dc3232';
                if ($log_data['level'] === 'WARN') $color = '#ffb900';
                if (strpos($log_data['message'], 'Complete') !== false) $color = '#46b450';
                if ($log_data['level'] === 'DEBUG') $color = '#666';
                
                $step_info = '';
                if (!empty($log_data['step'])) {
                    $step_info = ' [' . esc_html($log_data['step']) . ']';
                }
                if (!empty($log_data['retry'])) {
                    $step_info .= ' [Retry ' . esc_html($log_data['retry']) . ']';
                }
                
                printf(
                    '<div style="color:%s; margin-bottom: 3px;">[%s] [%s]%s %s</div>',
                    $color,
                    esc_html($log_data['timestamp']),
                    esc_html($log_data['level']),
                    $step_info,
                    esc_html($log_data['message'])
                );
            }
            echo '</div>';
            echo '<p class="description"><em>Showing recent entries. <a href="' . admin_url('admin.php?page=omeda-workflow-logs&view=details&post_id=' . $post->ID) . '" target="_blank">View complete log with full details →</a></em></p>';
        } else if ($selected_config_id) {
             echo '<p><em>Save this draft to create the deployment in Omeda.</em></p>';
        }
    }

    public function handle_post_save($post_id) {
        // Log entry
        error_log("Omeda: handle_post_save called for post_id={$post_id}");
        
        // Security checks and basic validation
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            error_log("Omeda: Skipping - autosave");
            return;
        }
        
        // Verify nonce only if it's present (traditional form submission)
        // Allow REST API and programmatic saves to proceed without nonce
        if (isset($_POST['omeda_post_meta_nonce'])) {
            if (!wp_verify_nonce($_POST['omeda_post_meta_nonce'], 'omeda_save_post_meta')) {
                error_log("Omeda: Skipping - nonce verification failed");
                return;
            }
            // Verify user can edit post when nonce is present
            if (!current_user_can('edit_post', $post_id)) {
                error_log("Omeda: Skipping - user cannot edit post");
                return;
            }
        } else {
            // For REST API or programmatic saves, be more permissive
            // Only check if user is logged in - Newsletter Glue uses REST API
            if (!is_user_logged_in()) {
                error_log("Omeda: Skipping - user not logged in (no nonce, REST API save)");
                return;
            }
            error_log("Omeda: Proceeding without nonce (REST API or programmatic save)");
        }
        
        if (!in_array(get_post_type($post_id), $this->get_supported_post_types())) {
            error_log("Omeda: Skipping - unsupported post type: " . get_post_type($post_id));
            return;
        }

        error_log("Omeda: All checks passed, proceeding with workflow");
        
        $config_id = isset($_POST['_omeda_config_id']) ? sanitize_text_field($_POST['_omeda_config_id']) : '';
        $track_id = get_post_meta($post_id, '_omeda_track_id', true);
        
        error_log("Omeda: config_id={$config_id}, track_id={$track_id}");
        
        // Save deployment name and campaign ID overrides
        if (isset($_POST['_omeda_deployment_name'])) {
            update_post_meta($post_id, '_omeda_deployment_name', sanitize_text_field($_POST['_omeda_deployment_name']));
        }
        if (isset($_POST['_omeda_campaign_id'])) {
            update_post_meta($post_id, '_omeda_campaign_id', sanitize_text_field($_POST['_omeda_campaign_id']));
        }

        // If no explicit config selected, try auto-detection
        if (empty($config_id)) {
            $config_id = Omeda_Deployment_Types::find_config_for_post($post_id);
        }

        // Always update the config ID in post meta to keep the UI selection consistent.
        update_post_meta($post_id, '_omeda_config_id', $config_id);

        if (empty($config_id)) {
            // User selected "-- Do Not Deploy --" or no matching config found.
            error_log("Omeda: No config_id, skipping workflow");
            return;
        }

        error_log("Omeda: Final config_id to use: {$config_id}");
        
        // Get async jobs handler
        $async_jobs = omeda_wp_integration()->async_jobs;
        
        // Check if Action Scheduler is available
        $use_async = function_exists('as_schedule_single_action');
        
        error_log("Omeda: use_async={$use_async}, async_jobs=" . (is_object($async_jobs) ? 'yes' : 'no'));

        if (empty($track_id)) {
            // SCENARIO: CREATE DEPLOYMENT - Execute immediately
            error_log("Omeda: Creating NEW deployment");
            if ($use_async && $async_jobs) {
                // Async: Schedule creation job to run ASAP (no delay)
                error_log("Omeda: Scheduling async create_deployment job");
                $async_jobs->schedule_create_deployment($post_id, $config_id, 0);
            } else {
                // Fallback: Synchronous execution
                error_log("Omeda: Running synchronous create_and_assign_audience");
                $this->workflow_manager->create_and_assign_audience($post_id, $config_id);
            }

        } else {
            // SCENARIO: UPDATE CONTENT - Execute immediately
            error_log("Omeda: Updating EXISTING deployment (track_id={$track_id})");
            if ($use_async && $async_jobs) {
                // Async: Schedule update job to run ASAP (no delay)
                error_log("Omeda: Scheduling async update_content job");
                $async_jobs->schedule_update_content($post_id, $track_id, $config_id, 0);
            } else {
                // Fallback: Synchronous execution
                error_log("Omeda: Running synchronous update_content");
                $this->workflow_manager->update_content($post_id, $track_id, $config_id);
            }
        }
        
        error_log("Omeda: handle_post_save completed");
    }

    /**
     * Detects when a post is published or scheduled to trigger final steps.
     */
    public function handle_status_transition($new_status, $old_status, $post) {
        if (!in_array($post->post_type, $this->get_supported_post_types())) {
            return;
        }

        $config_id = get_post_meta($post->ID, '_omeda_config_id', true);
        $track_id = get_post_meta($post->ID, '_omeda_track_id', true);

        if (empty($config_id) || empty($track_id)) {
            // Nothing to do if a deployment hasn't been configured and created.
            return;
        }

        // Trigger conditions: Publishing or Scheduling for the first time.
        $is_publishing = ($old_status !== 'publish' && $new_status === 'publish');
        $is_scheduling = ($old_status !== 'future' && $new_status === 'future');

        if ($is_publishing || $is_scheduling) {
            // Get async jobs handler
            $async_jobs = omeda_wp_integration()->async_jobs;
            
            // Check if Action Scheduler is available
            $use_async = function_exists('as_schedule_single_action');

            if ($use_async && $async_jobs) {
                // Async: Schedule finalization sequence
                $async_jobs->schedule_finalize_deployment($post->ID, $track_id, $config_id);
            } else {
                // Fallback: Synchronous execution
                $this->workflow_manager->schedule_and_send_test($post->ID, $track_id, $config_id);
            }
        }
    }

    /**
     * Enqueue admin scripts and styles for the Omeda meta box.
     */
    public function enqueue_admin_scripts($hook) {
        global $post;

        // Only load on post edit screens
        if (!in_array($hook, ['post.php', 'post-new.php']) || !$post || !in_array($post->post_type, $this->get_supported_post_types())) {
            return;
        }

        wp_enqueue_script(
            'omeda-admin',
            plugins_url('assets/js/omeda-admin.js', dirname(__FILE__)),
            array('jquery'),
            '1.0.1',
            true
        );

        wp_localize_script('omeda-admin', 'omedaAdmin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'post_id' => $post->ID,
            'nonce' => wp_create_nonce('omeda_ajax_nonce')
        ));
    }

    /**
     * AJAX Handler: Send Test Email
     */
    public function ajax_send_test() {
        check_ajax_referer('omeda_ajax_nonce', 'nonce');

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        if (!$post_id || !current_user_can('edit_post', $post_id)) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }

        $track_id = get_post_meta($post_id, '_omeda_track_id', true);
        $config_id = get_post_meta($post_id, '_omeda_config_id', true);

        if (empty($track_id) || empty($config_id)) {
            wp_send_json_error(['message' => 'Deployment not configured or created yet.']);
        }

        $async_jobs = omeda_wp_integration()->async_jobs;
        $result = $async_jobs->send_test_email($post_id, $track_id, $config_id);

        if ($result['success']) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error($result);
        }
    }

    /**
     * AJAX Handler: Schedule Deployment
     */
    public function ajax_schedule_deployment() {
        check_ajax_referer('omeda_ajax_nonce', 'nonce');

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $schedule_date = isset($_POST['schedule_date']) ? sanitize_text_field($_POST['schedule_date']) : '';

        if (!$post_id || !current_user_can('edit_post', $post_id)) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }

        if (empty($schedule_date)) {
            wp_send_json_error(['message' => 'Schedule date is required.']);
        }

        $track_id = get_post_meta($post_id, '_omeda_track_id', true);
        $config_id = get_post_meta($post_id, '_omeda_config_id', true);

        if (empty($track_id) || empty($config_id)) {
            wp_send_json_error(['message' => 'Deployment not configured or created yet.']);
        }

        $async_jobs = omeda_wp_integration()->async_jobs;
        $result = $async_jobs->schedule_deployment($post_id, $track_id, $config_id, $schedule_date);

        if ($result['success']) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error($result);
        }
    }

    /**
     * AJAX Handler: Unschedule Deployment
     */
    public function ajax_unschedule_deployment() {
        check_ajax_referer('omeda_ajax_nonce', 'nonce');

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        if (!$post_id || !current_user_can('edit_post', $post_id)) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }

        $track_id = get_post_meta($post_id, '_omeda_track_id', true);

        if (empty($track_id)) {
            wp_send_json_error(['message' => 'No deployment found.']);
        }

        $async_jobs = omeda_wp_integration()->async_jobs;
        $result = $async_jobs->unschedule_deployment($post_id, $track_id);

        if ($result['success']) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error($result);
        }
    }
}