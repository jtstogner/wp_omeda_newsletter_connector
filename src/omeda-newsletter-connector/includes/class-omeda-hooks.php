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
        add_action('save_post', array($this, 'save_meta_data'));
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

        // Fetch available Deployment Types
        $deployment_types = get_posts([
            'post_type' => Omeda_Deployment_Types::CPT_SLUG,
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ]);

        echo '<p><label for="_omeda_config_id"><strong>Deployment Type:</strong></label></p>';
        echo '<select name="_omeda_config_id" id="_omeda_config_id" style="width: 100%;">';
        echo '<option value="">-- Do Not Deploy --</option>';
        foreach ($deployment_types as $type) {
            echo '<option value="' . esc_attr($type->ID) . '" ' . selected($selected_config_id, $type->ID, false) . '>';
            echo esc_html($type->post_title);
            echo '</option>';
        }
        echo '</select>';

        echo '<hr>';

        if ($track_id) {
            echo '<p><strong>Omeda TrackID:</strong> ' . esc_html($track_id) . '</p>';
        }

        if ($workflow_logs) {
            echo '<h4>Workflow Log:</h4>';
            // Display logs in reverse order (most recent first)
            echo '<div style="max-height: 200px; overflow-y: scroll; background: #f9f9f9; padding: 5px; font-size: 0.9em; font-family: monospace; border: 1px solid #eee;">';
            foreach (array_reverse($workflow_logs) as $log) {
                $color = 'black';
                if (strpos($log, '[ERROR]') !== false) $color = '#dc3232';
                if (strpos($log, '[WARN]') !== false) $color = '#ffb900';
                if (strpos($log, 'Complete') !== false) $color = '#46b450';
                
                echo '<div style="color:'.$color.'; margin-bottom: 3px;">' . esc_html($log) . '</div>';
            }
            echo '</div>';
        } else if ($selected_config_id) {
             echo '<p><em>Workflow pending initiation upon Publish/Schedule.</em></p>';
        }
    }

    public function save_meta_data($post_id) {
        // Security checks
        if (!isset($_POST['omeda_post_meta_nonce']) || !wp_verify_nonce($_POST['omeda_post_meta_nonce'], 'omeda_save_post_meta') || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save the selected configuration ID
        if (isset($_POST['_omeda_config_id'])) {
            $config_id = sanitize_text_field($_POST['_omeda_config_id']);
            // This is also saved in the Workflow Manager initiate_workflow, but saving here ensures the UI reflects the selection immediately.
            update_post_meta($post_id, '_omeda_config_id', $config_id);
        }
    }

    /**
     * Detects when a post is published or scheduled and initiates the workflow.
     */
    public function handle_status_transition($new_status, $old_status, $post) {
        if (!in_array($post->post_type, $this->get_supported_post_types())) {
            return;
        }

        $config_id = get_post_meta($post->ID, '_omeda_config_id', true);
        if (empty($config_id)) {
            return;
        }

        // Trigger conditions: Publishing or Scheduling
        $is_publishing = ($old_status != 'publish' && $new_status == 'publish');
        $is_scheduling = ($new_status == 'future');

        if ($is_publishing || $is_scheduling) {
            // Prevent re-triggering if already processed
            $existing_track_id = get_post_meta($post->ID, '_omeda_track_id', true);
            if ($existing_track_id) {
                // Future enhancement: Logic to update existing deployment.
                return;
            }

            // Initiate the workflow
            $this->workflow_manager->initiate_workflow($post->ID, $config_id);
        }
    }
}