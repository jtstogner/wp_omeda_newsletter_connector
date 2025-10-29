<?php
/**
 * Manages Omeda Deployment Types (Configurations) using a Custom Post Type.
 */
class Omeda_Deployment_Types
{
    const CPT_SLUG = 'omeda_deploy_type';

    public function init()
    {
        add_action('init', array($this, 'register_cpt'));
        add_action('admin_menu', array($this, 'add_to_menu'));
        add_action('add_meta_boxes', array($this, 'register_meta_boxes'));
        add_action('save_post_' . self::CPT_SLUG, array($this, 'save_meta_data'));
    }

    public function register_cpt()
    {
        $labels = array(
            'name'          => 'Deployment Types',
            'singular_name' => 'Deployment Type',
            'menu_name'     => 'Deployment Types',
            'add_new_item'  => 'Add New Deployment Type',
        );

        $args = array(
            'labels'             => $labels,
            'public'             => false,
            'show_ui'            => true,
            'show_in_menu'       => false, // We will add it manually to the Omeda menu
            'supports'           => array('title'),
            'capability_type'    => 'post',
        );
        register_post_type(self::CPT_SLUG, $args);
    }

    public function add_to_menu() {
        // Add the CPT management page as a submenu under the main Omeda Integration menu
        $parent_slug = omeda_wp_integration()->settings->menu_slug;
        add_submenu_page(
            $parent_slug,
            'Deployment Types',
            'Deployment Types',
            'manage_options',
            'edit.php?post_type=' . self::CPT_SLUG
        );
    }

    public function register_meta_boxes()
    {
        add_meta_box(
            'omeda_deployment_settings',
            'Configuration Settings',
            array($this, 'render_settings_meta_box'),
            self::CPT_SLUG
        );
    }

    // Define the fields required for each configuration
    private function get_fields()
    {
        return array(
            'deployment_type_id' => array('label' => 'Deployment Type ID (Omeda)', 'type' => 'number', 'required' => true),
            'query_name'         => array('label' => 'Query Name (Omeda Audience)', 'type' => 'text', 'required' => true),
            'from_name'          => array('label' => 'From Name', 'type' => 'text', 'required' => true),
            'from_email'         => array('label' => 'From Email', 'type' => 'email', 'required' => true),
            'reply_to'           => array('label' => 'Reply To Email', 'type' => 'email', 'required' => true),
            'subject_format'     => array('label' => 'Subject Format (Optional)', 'type' => 'text', 'desc' => 'e.g., @{mv_html_title_subject}@ or a static subject. If empty, the WordPress post title will be used.'),
            'mailbox_name'       => array('label' => 'Mailbox (Optional)', 'type' => 'text', 'desc' => 'If empty, the global default will be used.'),
            'output_criteria'    => array('label' => 'Output Criteria (Optional)', 'type' => 'text', 'desc' => 'If empty, the global default will be used.'),
        );
    }

    public function render_settings_meta_box($post)
    {
        wp_nonce_field('omeda_save_deployment_type', 'omeda_deployment_type_nonce');
        $fields = $this->get_fields();

        echo '<table class="form-table">';
        foreach ($fields as $key => $field) {
            $meta_key = '_omeda_' . $key;
            $value = get_post_meta($post->ID, $meta_key, true);
            $required_html = isset($field['required']) && $field['required'] ? '<span style="color:red;">*</span>' : '';

            echo '<tr>';
            echo '<th scope="row"><label for="' . esc_attr($meta_key) . '">' . esc_html($field['label']) . $required_html . '</label></th>';
            echo '<td>';
            echo '<input type="' . esc_attr($field['type']) . '" id="' . esc_attr($meta_key) . '" name="' . esc_attr($meta_key) . '" value="' . esc_attr($value) . '" class="regular-text" />';
            if (isset($field['desc'])) {
                echo '<p class="description">' . esc_html($field['desc']) . '</p>';
            }
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    public function save_meta_data($post_id)
    {
        // Security checks
        if (!isset($_POST['omeda_deployment_type_nonce']) || !wp_verify_nonce($_POST['omeda_deployment_type_nonce'], 'omeda_save_deployment_type') || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save the data
        $fields = $this->get_fields();
        foreach ($fields as $key => $field) {
            $meta_key = '_omeda_' . $key;
            if (isset($_POST[$meta_key])) {
                $value = sanitize_text_field($_POST[$meta_key]);
                update_post_meta($post_id, $meta_key, $value);
            } else {
                delete_post_meta($post_id, $meta_key);
            }
        }
    }

    /**
     * Helper function to retrieve and format settings for a specific deployment type.
     */
    public static function get_configuration($config_id) {
        if (get_post_type($config_id) !== self::CPT_SLUG) {
            return null;
        }

        $meta = get_post_meta($config_id);
        $config = [];

        // Map the stored meta keys to the expected API configuration keys (PascalCase)
        $mapping = [
            'deployment_type_id' => 'DeploymentTypeId',
            'query_name'         => 'QueryName',
            'from_name'          => 'FromName',
            'from_email'         => 'FromEmail',
            'reply_to'           => 'ReplyTo',
            'subject_format'     => 'SubjectFormat',
            'mailbox_name'       => 'MailboxName',
            'output_criteria'    => 'OutputCriteria',
        ];

        foreach ($mapping as $meta_suffix => $config_key) {
            $meta_key = '_omeda_' . $meta_suffix;
            if (isset($meta[$meta_key][0]) && $meta[$meta_key][0] !== '') {
                 $config[$config_key] = $meta[$meta_key][0];
            }
        }

        return $config;
    }
}