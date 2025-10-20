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
        return ['post'];
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

        echo '<option value="">-- Do Not Deploy --</option>';
        foreach ($deployment_types as $type) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($type->ID),
                selected($selected_config_id, $type->ID, false),
                esc_html($type->post_title)
            );
        }
        echo '</select>';

        if ($is_locked) {
            // Add a hidden field to ensure the value is still submitted,
            // as disabled fields are not.
            printf('<input type="hidden" name="_omeda_config_id" value="%s" />', esc_attr($selected_config_id));
            echo '<p class="description"><em>Deployment type is locked because a deployment has already been created in Omeda.</em></p>';
        }

        echo '<hr>';

        if ($track_id) {
            echo '<p><strong>Omeda TrackID:</strong> ' . esc_html($track_id) . '</p>';
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
                    '<div style="color:%s; margin-bottom: 3px;" title="%s">[%s] %s</div>',
                    $color,
                    esc_attr($log_data['timestamp']),
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

        // Always update the config ID in post meta to keep the UI selection consistent.
        update_post_meta($post_id, '_omeda_config_id', $config_id);

        if (empty($config_id)) {
            // User selected "-- Do Not Deploy --" or cleared the selection.
            // Future enhancement: could add logic here to cancel an existing deployment.
            return;
        }

        if (empty($track_id)) {
            // SCENARIO: CREATE DEPLOYMENT
            // This is the first time a deployment type is being saved for this post.
            // Create the deployment, assign the audience, and add the initial content.
            $this->workflow_manager->create_and_assign_audience($post_id, $config_id);

        } else {
            // SCENARIO: UPDATE CONTENT
            // A deployment already exists, so just update the content.
            $this->workflow_manager->update_content($post_id, $track_id, $config_id);

            // Also, trigger a new test if the post is already published.
            if (get_post_status($post_id) === 'publish') {
                $this->workflow_manager->send_test($post_id, $track_id, $config_id);
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
            $this->workflow_manager->schedule_and_send_test($post->ID, $track_id, $config_id);
        }
    }
}