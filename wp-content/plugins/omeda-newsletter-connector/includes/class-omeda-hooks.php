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

        echo '<hr>';

        if ($track_id) {
            echo '<p><strong>Omeda TrackID:</strong> ' . esc_html($track_id) . '</p>';
        }

        // Show pending jobs if Action Scheduler is available
        if (function_exists('as_get_scheduled_actions')) {
            $async_jobs = omeda_wp_integration()->async_jobs;
            if ($async_jobs) {
                $pending_jobs = $async_jobs->get_pending_jobs($post->ID);
                if (!empty($pending_jobs)) {
                    echo '<p><strong>Pending Jobs:</strong></p>';
                    echo '<ul style="margin: 0; padding-left: 20px; font-size: 0.9em;">';
                    foreach ($pending_jobs as $job) {
                        $job_name = str_replace('omeda_async_', '', $job['hook']);
                        $scheduled_time = $job['scheduled']->format('Y-m-d H:i:s');
                        echo '<li>' . esc_html(ucwords(str_replace('_', ' ', $job_name))) . ' - ' . esc_html($scheduled_time) . '</li>';
                    }
                    echo '</ul>';
                }
            }
        }

        if ($workflow_logs) {
            echo '<h4>Workflow Log:</h4>';
            echo '<div style="max-height: 200px; overflow-y: scroll; background: #f9f9f9; padding: 5px; font-size: 0.9em; font-family: monospace; border: 1px solid #eee;">';
            foreach (array_reverse($workflow_logs) as $log_json) {
                $log_data = json_decode($log_json, true);
                if (!$log_data) continue;

                $color = 'black';
                if ($log_data['level'] === 'ERROR') $color = '#dc3232';
                if ($log_data['level'] === 'WARN') $color = '#ffb900';
                if (strpos($log_data['message'], 'Complete') !== false) $color = '#46b450';
                
                printf(
                    '<div style="color:%s; margin-bottom: 3px;">[%s] [%s] %s</div>',
                    $color,
                    esc_html($log_data['timestamp']),
                    esc_html($log_data['level']),
                    esc_html($log_data['message'])
                );
            }
            echo '</div>';
        } else if ($selected_config_id) {
             echo '<p><em>Save this draft to create the deployment in Omeda.</em></p>';
        }
    }

    public function handle_post_save($post_id) {
        // Security checks and basic validation
        if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !isset($_POST['omeda_post_meta_nonce']) || !wp_verify_nonce($_POST['omeda_post_meta_nonce'], 'omeda_save_post_meta') || !current_user_can('edit_post', $post_id) || !in_array(get_post_type($post_id), $this->get_supported_post_types())) {
            return;
        }

        $config_id = isset($_POST['_omeda_config_id']) ? sanitize_text_field($_POST['_omeda_config_id']) : '';
        $track_id = get_post_meta($post_id, '_omeda_track_id', true);

        // If no explicit config selected, try auto-detection
        if (empty($config_id)) {
            $config_id = Omeda_Deployment_Types::find_config_for_post($post_id);
        }

        // Always update the config ID in post meta to keep the UI selection consistent.
        update_post_meta($post_id, '_omeda_config_id', $config_id);

        if (empty($config_id)) {
            // User selected "-- Do Not Deploy --" or no matching config found.
            return;
        }

        // Get async jobs handler
        $async_jobs = omeda_wp_integration()->async_jobs;
        
        // Check if Action Scheduler is available
        $use_async = function_exists('as_schedule_single_action');

        if (empty($track_id)) {
            // SCENARIO: CREATE DEPLOYMENT
            if ($use_async && $async_jobs) {
                // Async: Schedule creation job with debouncing
                $async_jobs->schedule_create_deployment($post_id, $config_id, 300); // 5 minutes
            } else {
                // Fallback: Synchronous execution
                $this->workflow_manager->create_and_assign_audience($post_id, $config_id);
            }

        } else {
            // SCENARIO: UPDATE CONTENT
            if ($use_async && $async_jobs) {
                // Async: Schedule update job with debouncing
                $async_jobs->schedule_update_content($post_id, $track_id, $config_id, 60); // 1 minute
            } else {
                // Fallback: Synchronous execution
                $this->workflow_manager->update_content($post_id, $track_id, $config_id);
            }

            // Also, trigger a new test if the post is already published (only in sync mode)
            if (!$use_async && get_post_status($post_id) === 'publish') {
                $config = $this->workflow_manager->prepare_configuration($post_id, $config_id);
                if ($config) {
                    $this->workflow_manager->send_test($post_id, $track_id, $config);
                }
            }
        }
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
}