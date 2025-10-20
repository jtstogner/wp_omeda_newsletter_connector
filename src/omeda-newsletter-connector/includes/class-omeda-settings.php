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

         // Workflow Configuration Section (Informational, as we now use polling)
        add_settings_section('omeda_workflow_section', 'Workflow Configuration', array($this, 'workflow_section_callback'), $this->option_group);
       
    }

    public function defaults_section_callback() {
        echo '<p>Global defaults used if not specified by the specific Deployment Type configuration.</p>';
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
}
