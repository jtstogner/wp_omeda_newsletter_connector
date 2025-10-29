<?php
/**
 * Omeda Settings Management.
 */
class Omeda_Settings
{
    private $option_group = 'omeda_integration_settings';
    public $menu_slug = 'omeda-integration';

    public function init()
    {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_admin_menu()
    {
        // Create a top-level menu
        add_menu_page(
            'Omeda Integration',
            'Omeda Integration',
            'manage_options',
            $this->menu_slug,
            array($this, 'settings_page_html'),
            'dashicons-email-alt',
            26
        );

         // Add the settings link as the first submenu item
         add_submenu_page(
            $this->menu_slug,
            'Omeda Global Settings',
            'Settings',
            'manage_options',
            $this->menu_slug,
             array($this, 'settings_page_html')
        );

        // Add workflow logs submenu
        add_submenu_page(
            $this->menu_slug,
            'Workflow Logs',
            'Workflow Logs',
            'manage_options',
            'omeda-workflow-logs',
            array($this, 'workflow_logs_page_html')
        );

        // Add link to Action Scheduler admin UI (if available)
        if (function_exists('action_scheduler_register_admin_page')) {
            add_submenu_page(
                $this->menu_slug,
                'Background Jobs',
                'Background Jobs',
                'manage_options',
                'action-scheduler',
                'action_scheduler_render_admin_page'
            );
        }
    }

    public function register_settings()
    {
        // API Configuration Section
        add_settings_section('omeda_api_config_section', 'API Configuration', null, $this->option_group);

        $this->register_setting_field('omeda_app_id', 'Omeda App ID (x-omeda-appid)', 'render_text_field', 'omeda_api_config_section');
        $this->register_setting_field('omeda_brand_abbreviation', 'Brand Abbreviation (e.g., MTGMCD)', 'render_text_field', 'omeda_api_config_section');
        $this->register_setting_field('omeda_environment', 'Environment', 'render_environment_field', 'omeda_api_config_section');

        // Deployment Defaults Section
        add_settings_section('omeda_deployment_defaults_section', 'Deployment Defaults', array($this, 'defaults_section_callback'), $this->option_group);

        // Defaults based on appConfig.json values
        $this->register_setting_field('omeda_default_user_id', 'Default User ID (Owner/Approver)', 'render_text_field', 'omeda_deployment_defaults_section');
        $this->register_setting_field('omeda_default_mailbox', 'Default Mailbox', 'render_text_field', 'omeda_deployment_defaults_section', 'newsletters');
        $this->register_setting_field('omeda_default_output_criteria', 'Default Output Criteria', 'render_text_field', 'omeda_deployment_defaults_section', 'Newsletter_Member_id');
        $this->register_setting_field('omeda_publish_delay', 'Immediate Publish Delay (minutes)', 'render_number_field', 'omeda_deployment_defaults_section', 30);
        
        // Email Defaults Section
        add_settings_section('omeda_email_defaults_section', 'Default Email Settings', array($this, 'email_defaults_section_callback'), $this->option_group);
        
        $this->register_setting_field('omeda_default_from_name', 'Default From Name', 'render_text_field', 'omeda_email_defaults_section', get_bloginfo('name'));
        $this->register_setting_field('omeda_default_from_email', 'Default From Email', 'render_email_field', 'omeda_email_defaults_section', get_bloginfo('admin_email'));
        $this->register_setting_field('omeda_default_reply_to', 'Default Reply To Email', 'render_email_field', 'omeda_email_defaults_section', get_bloginfo('admin_email'));

         // Workflow Configuration Section (Informational, as we now use polling)
        add_settings_section('omeda_workflow_section', 'Workflow Configuration', array($this, 'workflow_section_callback'), $this->option_group);
       
    }

    public function defaults_section_callback() {
        echo '<p>Global defaults used if not specified by the specific Deployment Type configuration.</p>';
    }
    
    public function email_defaults_section_callback() {
        echo '<p>Default email settings that will be used when creating new Deployment Types. These can be overridden per deployment type.</p>';
    }

    public function workflow_section_callback() {
        echo '<p>The workflow utilizes asynchronous WP-Cron processing with active status polling (checking every 45 seconds) to ensure robust execution of the 5-step Omeda deployment process.</p>';
    }

    private function register_setting_field($name, $title, $callback, $section, $default = '')
    {
        register_setting($this->option_group, $name);
        add_settings_field(
            $name, $title, array($this, $callback), $this->option_group, $section,
            array('field_name' => $name, 'default' => $default)
        );
    }

    // --- Rendering Helpers ---
    public function render_text_field($args)
    {
        $name = $args['field_name'];
        $value = get_option($name, $args['default']);
        printf('<input type="text" name="%s" value="%s" class="regular-text" />', esc_attr($name), esc_attr($value));
    }
    
    public function render_email_field($args)
    {
        $name = $args['field_name'];
        $value = get_option($name, $args['default']);
        printf('<input type="email" name="%s" value="%s" class="regular-text" />', esc_attr($name), esc_attr($value));
    }

    public function render_number_field($args)
    {
        $name = $args['field_name'];
        $value = get_option($name, $args['default']);
        printf('<input type="number" name="%s" value="%s" class="small-text" />', esc_attr($name), esc_attr($value));
    }

    public function render_environment_field($args)
    {
        $name = $args['field_name'];
        $value = get_option($name, 'staging');
        ?>
        <select name="<?php echo esc_attr($name); ?>">
            <option value="production" <?php selected($value, 'production'); ?>>Production (ows.omeda.com)</option>
            <option value="staging" <?php selected($value, 'staging'); ?>>Staging (ows.omedastaging.com)</option>
        </select>
        <?php
    }

    public function settings_page_html()
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields($this->option_group);
                do_settings_sections($this->option_group);
                submit_button('Save Settings');
                ?>
            </form>
        </div>
        <?php
    }

    public function workflow_logs_page_html()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        
        // Get all posts with workflow logs
        global $wpdb;
        
        // Pagination
        $per_page = 20;
        $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $offset = ($current_page - 1) * $per_page;
        
        // Get posts with workflow logs
        $query = "
            SELECT DISTINCT p.ID, p.post_title, p.post_type, p.post_status, p.post_modified
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE pm.meta_key = '_omeda_workflow_log'
            ORDER BY p.post_modified DESC
            LIMIT %d OFFSET %d
        ";
        
        $posts_with_logs = $wpdb->get_results($wpdb->prepare($query, $per_page, $offset));
        
        // Get total count
        $total_query = "
            SELECT COUNT(DISTINCT p.ID)
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE pm.meta_key = '_omeda_workflow_log'
        ";
        $total_items = $wpdb->get_var($total_query);
        $total_pages = ceil($total_items / $per_page);
        
        ?>
        <div class="wrap">
            <h1>Workflow Logs</h1>
            <p>View workflow execution logs for posts that have been processed through the Omeda deployment workflow.</p>
            
            <?php if (empty($posts_with_logs)) : ?>
                <p>No workflow logs found.</p>
            <?php else : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Post ID</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Last Modified</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts_with_logs as $post) : ?>
                            <tr>
                                <td><?php echo esc_html($post->ID); ?></td>
                                <td><?php echo esc_html($post->post_title); ?></td>
                                <td><?php echo esc_html($post->post_type); ?></td>
                                <td><?php echo esc_html($post->post_status); ?></td>
                                <td><?php echo esc_html($post->post_modified); ?></td>
                                <td>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=omeda-workflow-logs&view=details&post_id=' . $post->ID)); ?>" class="button button-small">View Logs</a>
                                    <a href="<?php echo esc_url(get_edit_post_link($post->ID)); ?>" class="button button-small">Edit Post</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <?php if ($total_pages > 1) : ?>
                    <div class="tablenav">
                        <div class="tablenav-pages">
                            <?php
                            $page_links = paginate_links(array(
                                'base' => add_query_arg('paged', '%#%'),
                                'format' => '',
                                'prev_text' => __('&laquo;'),
                                'next_text' => __('&raquo;'),
                                'total' => $total_pages,
                                'current' => $current_page
                            ));
                            echo $page_links;
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php
            // Show detailed logs for a specific post
            if (isset($_GET['view']) && $_GET['view'] === 'details' && isset($_GET['post_id'])) {
                $post_id = intval($_GET['post_id']);
                $post = get_post($post_id);
                
                if ($post) {
                    $logs = get_post_meta($post_id, '_omeda_workflow_log');
                    $workflow_state = get_post_meta($post_id, '_omeda_workflow_state', true);
                    $deployment_id = get_post_meta($post_id, '_omeda_deployment_id', true);
                    
                    ?>
                    <hr>
                    <h2>Workflow Details for: <?php echo esc_html($post->post_title); ?></h2>
                    <p><strong>Post ID:</strong> <?php echo esc_html($post_id); ?></p>
                    <p><strong>Current Workflow State:</strong> <?php echo esc_html($workflow_state ?: 'None'); ?></p>
                    <p><strong>Omeda Deployment ID:</strong> <?php echo esc_html($deployment_id ?: 'Not created yet'); ?></p>
                    
                    <h3>Log Entries</h3>
                    <?php if (empty($logs)) : ?>
                        <p>No log entries found for this post.</p>
                    <?php else : ?>
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th style="width: 150px;">Timestamp</th>
                                    <th style="width: 80px;">Level</th>
                                    <th>Message</th>
                                    <th style="width: 200px;">Context</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Sort logs by timestamp (most recent first)
                                usort($logs, function($a, $b) {
                                    $a_data = json_decode($a, true);
                                    $b_data = json_decode($b, true);
                                    return strtotime($b_data['timestamp']) - strtotime($a_data['timestamp']);
                                });
                                
                                foreach ($logs as $log_json) : 
                                    $log = json_decode($log_json, true);
                                    if (!$log) continue;
                                    
                                    $level_class = '';
                                    switch ($log['level']) {
                                        case 'ERROR':
                                            $level_class = 'background: #dc3232; color: white; padding: 2px 5px; border-radius: 3px;';
                                            break;
                                        case 'WARN':
                                            $level_class = 'background: #ffb900; color: black; padding: 2px 5px; border-radius: 3px;';
                                            break;
                                        case 'INFO':
                                            $level_class = 'background: #00a32a; color: white; padding: 2px 5px; border-radius: 3px;';
                                            break;
                                    }
                                ?>
                                    <tr>
                                        <td><?php echo esc_html($log['timestamp']); ?></td>
                                        <td><span style="<?php echo $level_class; ?>"><?php echo esc_html($log['level']); ?></span></td>
                                        <td><?php echo esc_html($log['message']); ?></td>
                                        <td><?php echo $log['context'] ? '<pre>' . esc_html(print_r($log['context'], true)) . '</pre>' : '—'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                    
                    <p><a href="<?php echo esc_url(admin_url('admin.php?page=omeda-workflow-logs')); ?>" class="button">&larr; Back to Logs List</a></p>
                    <?php
                }
            }
            ?>
        </div>
        <?php
    }
}
