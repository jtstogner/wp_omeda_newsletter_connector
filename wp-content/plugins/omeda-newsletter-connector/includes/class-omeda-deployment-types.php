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
        add_action('admin_init', array($this, 'handle_cache_refresh'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    /**
     * Enqueue Select2 for searchable dropdowns.
     */
    public function enqueue_admin_assets($hook) {
        $screen = get_current_screen();
        if (!$screen || $screen->post_type !== self::CPT_SLUG) {
            return;
        }
        
        // Enqueue Select2
        wp_enqueue_style('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), '4.1.0');
        wp_enqueue_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'), '4.1.0', true);
        
        // Enqueue custom script
        wp_add_inline_script('select2', "
            jQuery(document).ready(function($) {
                // Initialize Select2 on deployment type dropdown
                $('#_omeda_deployment_type_id').select2({
                    placeholder: '-- Select Deployment Type --',
                    allowClear: true,
                    width: '100%'
                });
                
                // Initialize Select2 on post type dropdown
                $('#_omeda_assigned_post_type').select2({
                    placeholder: '-- Select Post Type / Template --',
                    allowClear: true,
                    width: '100%'
                });
            });
        ");
    }

    /**
     * Handle manual cache refresh request.
     */
    public function handle_cache_refresh() {
        if (isset($_GET['omeda_refresh']) && $_GET['omeda_refresh'] == '1' && current_user_can('manage_options')) {
            Omeda_Data_Manager::get_deployment_types(true); // Force refresh
            wp_safe_redirect(remove_query_arg('omeda_refresh'));
            exit;
        }
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
            'deployment_type_id' => array(
                'label' => 'Omeda Deployment Type', 
                'type' => 'select_omeda', 
                'required' => true,
                'desc' => 'Select the deployment type from Omeda.'
            ),
            'assigned_post_type' => array(
                'label' => 'Assigned Post Type / Template', 
                'type' => 'select_post_type', 
                'required' => true,
                'desc' => 'Select which post type or Newsletter Glue template will trigger this deployment.'
            ),
            'audience_query_id'  => array(
                'label' => 'Audience Query', 
                'type' => 'text', 
                'required' => true,
                'desc' => 'Enter the exact name of your Audience Builder query in Omeda (case-sensitive).',
                'placeholder' => 'My Audience Builder Query'
            ),
            'from_name'          => array('label' => 'From Name', 'type' => 'text', 'required' => true),
            'from_email'         => array('label' => 'From Email', 'type' => 'email', 'required' => true),
            'reply_to'           => array('label' => 'Reply To Email', 'type' => 'email', 'required' => true),
            'subject_format'     => array(
                'label' => 'Subject Format', 
                'type' => 'text', 
                'required' => false,
                'desc' => 'Use WordPress variables like {post_title}, {post_date}, {site_name}, {author_name}, or Omeda merge tags like @{mv_html_title_subject}@. Leave empty to use post title.',
                'placeholder' => '{post_title} - {site_name}'
            ),
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
            
            // Use default values from settings for new posts
            if (empty($value) && $post->post_status === 'auto-draft') {
                if ($key === 'from_name') {
                    $value = get_option('omeda_default_from_name', get_bloginfo('name'));
                } elseif ($key === 'from_email') {
                    $value = get_option('omeda_default_from_email', get_bloginfo('admin_email'));
                } elseif ($key === 'reply_to') {
                    $value = get_option('omeda_default_reply_to', get_bloginfo('admin_email'));
                }
            }
            
            $required_html = isset($field['required']) && $field['required'] ? '<span style="color:red;">*</span>' : '';

            echo '<tr>';
            echo '<th scope="row"><label for="' . esc_attr($meta_key) . '">' . esc_html($field['label']) . $required_html . '</label></th>';
            echo '<td>';
            
            // Handle special field types
            if ($field['type'] === 'select_omeda') {
                $this->render_omeda_deployment_dropdown($meta_key, $value);
            } elseif ($field['type'] === 'select_post_type') {
                $this->render_post_type_dropdown($meta_key, $value);
            } else {
                $placeholder = isset($field['placeholder']) ? $field['placeholder'] : '';
                echo '<input type="' . esc_attr($field['type']) . '" id="' . esc_attr($meta_key) . '" name="' . esc_attr($meta_key) . '" value="' . esc_attr($value) . '" class="regular-text" placeholder="' . esc_attr($placeholder) . '" />';
            }
            
            if (isset($field['desc'])) {
                echo '<p class="description">' . esc_html($field['desc']) . '</p>';
            }
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    /**
     * Render dropdown of Omeda deployment types from API.
     */
    private function render_omeda_deployment_dropdown($meta_key, $current_value) {
        // Try to get deployment types, handle errors gracefully
        try {
            $deployment_types = Omeda_Data_Manager::get_deployment_types();
        } catch (Exception $e) {
            $deployment_types = new WP_Error('api_exception', $e->getMessage());
        }
        
        echo '<select id="' . esc_attr($meta_key) . '" name="' . esc_attr($meta_key) . '" class="regular-text">';
        echo '<option value="">-- Select Deployment Type --</option>';
        
        if (is_wp_error($deployment_types)) {
            echo '<option value="" disabled>Error: ' . esc_html($deployment_types->get_error_message()) . '</option>';
        } elseif (empty($deployment_types)) {
            echo '<option value="" disabled>No deployment types found. Check API credentials.</option>';
        } else {
            foreach ($deployment_types as $id => $name) {
                printf(
                    '<option value="%s" %s>%s (ID: %s)</option>',
                    esc_attr($id),
                    selected($current_value, $id, false),
                    esc_html($name),
                    esc_html($id)
                );
            }
        }
        echo '</select>';
        
        // Add refresh button
        echo ' <button type="button" class="button" onclick="omedaRefreshDeploymentTypes()">Refresh from Omeda</button>';
        echo '<script>
        function omedaRefreshDeploymentTypes() {
            if (confirm("Refresh deployment types from Omeda API?")) {
                window.location.href = window.location.href + "&omeda_refresh=1";
            }
        }
        </script>';
    }

    /**
     * Render dropdown of available post types and Newsletter Glue templates.
     */
    private function render_post_type_dropdown($meta_key, $current_value) {
        echo '<select id="' . esc_attr($meta_key) . '" name="' . esc_attr($meta_key) . '" class="regular-text">';
        echo '<option value="">-- Select Post Type / Template --</option>';
        
        // Get all public post types
        $post_types = get_post_types(array('public' => true), 'objects');
        
        echo '<optgroup label="Post Types">';
        foreach ($post_types as $post_type) {
            // Skip Newsletter Glue's internal post types
            if (in_array($post_type->name, array('ngl_template', 'ngl_pattern', 'ngl_automation', 'ngl_log'))) {
                continue;
            }
            printf(
                '<option value="post_type:%s" %s>%s</option>',
                esc_attr($post_type->name),
                selected($current_value, 'post_type:' . $post_type->name, false),
                esc_html($post_type->labels->singular_name)
            );
        }
        echo '</optgroup>';
        
        // Check for Newsletter Glue enabled post types
        $ng_post_types = $this->get_newsletter_glue_post_types();
        if (!empty($ng_post_types)) {
            echo '<optgroup label="Newsletter Glue Enabled Post Types">';
            foreach ($ng_post_types as $post_type => $label) {
                printf(
                    '<option value="ng_post_type:%s" %s>%s</option>',
                    esc_attr($post_type),
                    selected($current_value, 'ng_post_type:' . $post_type, false),
                    esc_html($label)
                );
            }
            echo '</optgroup>';
        }
        
        // Check for Newsletter Glue templates
        $ng_templates = $this->get_newsletter_glue_templates();
        if (!empty($ng_templates)) {
            echo '<optgroup label="Newsletter Glue Templates">';
            foreach ($ng_templates as $template_id => $template_name) {
                printf(
                    '<option value="ng_template:%s" %s>%s</option>',
                    esc_attr($template_id),
                    selected($current_value, 'ng_template:' . $template_id, false),
                    esc_html($template_name)
                );
            }
            echo '</optgroup>';
        }
        
        // Check for Newsletter Glue template categories
        $ng_categories = $this->get_newsletter_glue_categories();
        if (!empty($ng_categories)) {
            echo '<optgroup label="Newsletter Glue Template Categories">';
            foreach ($ng_categories as $cat_id => $cat_name) {
                printf(
                    '<option value="ng_category:%s" %s>%s</option>',
                    esc_attr($cat_id),
                    selected($current_value, 'ng_category:' . $cat_id, false),
                    esc_html($cat_name)
                );
            }
            echo '</optgroup>';
        }
        
        echo '</select>';
        echo '<p class="description">Select which posts should automatically use this deployment type.</p>';
    }

    /**
     * Get Newsletter Glue templates if available.
     */
    private function get_newsletter_glue_templates() {
        // Check if Newsletter Glue is active
        if (!class_exists('Newsletter_Glue')) {
            return array();
        }
        
        // Query for Newsletter Glue template posts (actual post type used by NG)
        $templates = get_posts(array(
            'post_type' => 'ngl_template',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC'
        ));
        
        $result = array();
        foreach ($templates as $template) {
            // Mark default/core templates
            $is_core = get_post_meta($template->ID, '_ngl_core_template', true);
            $label = $template->post_title;
            if ($is_core) {
                $label .= ' (Default)';
            }
            $result[$template->ID] = $label;
        }
        
        return $result;
    }

    /**
     * Get Newsletter Glue categories (template categories) if available.
     */
    private function get_newsletter_glue_categories() {
        // Check if Newsletter Glue is active
        if (!class_exists('Newsletter_Glue')) {
            return array();
        }
        
        // Use the actual Newsletter Glue taxonomy
        $taxonomy = 'ngl_template_category';
        
        if (!taxonomy_exists($taxonomy)) {
            return array();
        }
        
        $terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
            'orderby' => 'name',
            'order' => 'ASC'
        ));
        
        if (is_wp_error($terms)) {
            return array();
        }
        
        $result = array();
        foreach ($terms as $term) {
            $result[$term->term_id] = $term->name;
        }
        
        return $result;
    }

    /**
     * Get Newsletter Glue enabled post types.
     */
    private function get_newsletter_glue_post_types() {
        // Check if Newsletter Glue is active
        if (!class_exists('Newsletter_Glue')) {
            return array();
        }
        
        // Get post types that Newsletter Glue is configured to work with
        $saved_types = get_option('newsletterglue_post_types');
        
        $result = array();
        if (!empty($saved_types)) {
            $post_types = explode(',', $saved_types);
            foreach ($post_types as $post_type) {
                $post_type = trim($post_type);
                $post_type_obj = get_post_type_object($post_type);
                if ($post_type_obj) {
                    $result[$post_type] = $post_type_obj->labels->singular_name . ' (Newsletter Glue Enabled)';
                }
            }
        }
        
        return $result;
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
            'assigned_post_type' => 'AssignedPostType',
            'audience_query_id'  => 'QueryName',
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

    /**
     * Find deployment type configuration by post type or template.
     * 
     * @param int $post_id The post ID to check.
     * @return int|null The deployment type config ID, or null if no match.
     */
    public static function find_config_for_post($post_id) {
        $post = get_post($post_id);
        if (!$post) {
            return null;
        }

        // Get all deployment type configurations
        $configs = get_posts(array(
            'post_type' => self::CPT_SLUG,
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));

        // Check for explicit assignment first (manual selection in post meta)
        $explicit_config = get_post_meta($post_id, '_omeda_config_id', true);
        if ($explicit_config) {
            return $explicit_config;
        }

        // Check each config for a match
        foreach ($configs as $config) {
            $assigned = get_post_meta($config->ID, '_omeda_assigned_post_type', true);
            
            if (empty($assigned)) {
                continue;
            }

            // Parse the assignment (format: post_type:post, ng_template:123, ng_category:456)
            list($type, $value) = explode(':', $assigned, 2);

            switch ($type) {
                case 'post_type':
                    if ($post->post_type === $value) {
                        return $config->ID;
                    }
                    break;

                case 'ng_post_type':
                    // Newsletter Glue enabled post types
                    if ($post->post_type === $value) {
                        // Additionally check if post has NG meta data (was sent or scheduled via NG)
                        $ng_sent = get_post_meta($post_id, 'newsletterglue_did_sent', true);
                        $ng_scheduled = get_post_meta($post_id, 'newsletterglue_to_send', true);
                        if ($ng_sent || $ng_scheduled) {
                            return $config->ID;
                        }
                    }
                    break;

                case 'ng_template':
                    // Check if this post uses this Newsletter Glue template
                    // NG doesn't store template ID in post meta, but we can check parent template
                    $template_parent = wp_get_post_parent_id($post_id);
                    if ($template_parent == $value) {
                        return $config->ID;
                    }
                    break;

                case 'ng_category':
                    // Check if post (as a template) has this Newsletter Glue category
                    if (has_term($value, 'ngl_template_category', $post_id)) {
                        return $config->ID;
                    }
                    break;
            }
        }

        return null;
    }
}