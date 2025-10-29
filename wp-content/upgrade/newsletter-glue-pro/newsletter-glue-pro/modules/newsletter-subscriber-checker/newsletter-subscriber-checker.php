<?php

/**
 * Newsletter Glue Subscriber Checker Module
 *
 * @package Newsletter Glue
 */

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Main Subscriber Checker class
 */
class NGL_Subscriber_Checker
{

    public $sendy_external = null;
    public $esp_instance = null;
    /**
     * Initialize the subscriber checker.
     */
    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        add_action('admin_init', array($this, 'ngl_compatibility_check'));
        add_filter('leaky_paywall_subscribers_columns', array($this, 'ngl_newsletter_check_subscriber_column'));
        add_action('manage_leaky_paywall_subscribers_custom_column', array($this, 'ngl_newsletter_check_subscriber_column_content'), 10, 3);

        add_action('admin_init', array($this, 'sendy_external_compatibility'));

        add_action('admin_init', array($this, 'initialize_esp'));

        // Enqueue scripts and styles for admin
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

        // Add AJAX handler for subscriber check
        add_action('wp_ajax_ngl_check_subscriber', array($this, 'ajax_check_subscriber'));
    }

    /**
     * Maybe halt execution if the user is not authorized
     * 
     * @return bool. True if the user is not authorized, false otherwise.
     */
    public function maybe_halt_execution() {
        $user = wp_get_current_user();

        if (!current_user_can('manage_options')) {
            return true;
        }

        $user_email = $user->user_email;

        if (
            false === strpos($user_email, '@paywallproject.com')
            && false === strpos($user_email, '@newsletterglue.com')
            && false === strpos($user_email, '@machadows.com')
        ) {
            return true;
        }

        return false;
    }

    public function stop_execution_if_not_in_allowed_page() {

        $current_page = isset($_GET['page']) ? $_GET['page'] : '';

        $allowed_pages = array(
            'leaky-paywall-subscribers',
        );

        if( ! isset($current_page) || empty($current_page) || ! in_array($current_page, $allowed_pages) ) {
            
            if (defined('DOING_AJAX') && DOING_AJAX) {
                $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
        
                if ($action === 'ngl_check_subscriber') {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    public function ngl_newsletter_check_subscriber_column($columns)
    {
        if ($this->maybe_halt_execution()) {
            return $columns;
        }
        $columns['ngl_newsletter_check'] = 'Newsletter Check';
        return $columns;
    }

    public function ngl_not_newsletter_check_column($column_name)
    {
        if ($column_name !== 'ngl_newsletter_check') {
            return $column_name;
        }
        return false;
    }

    public function get_current_active_esp()
    {
        $esp = newsletterglue_default_connection();
        return $esp;
    }

    public function sendy_external_compatibility()
    {
        if( $this->stop_execution_if_not_in_allowed_page() ) {
            return;
        }

        $path_is_sendy_external = strpos(newsletterglue_get_path('sendy'), 'wp-content/plugins/newsletter-glue-sendy/sendy') !== false;

        if ($this->get_current_active_esp() !== 'sendy') {
            return;
        }

        if (! $path_is_sendy_external) {
            return;
        }

        $esp = 'sendy';

        $esp_file =  newsletterglue_get_path($esp) . '/init.php';
        if (file_exists($esp_file)) {
            include_once $esp_file;
        }

        $classname = '\NGL_' . ucfirst($esp);

        $api = new $classname();

        // Initialize the API client
        if (method_exists($api, 'connect')) {
            $api->connect();
        }

        include_once plugin_dir_path(__FILE__) . 'esp/sendy-external/sendy.php';

        $this->sendy_external = new NGL_Sendy_External();
        
    }

    public function ngl_newsletter_check_subscriber_column_content($value, $column_name, $subscriber_id)
    {
        if ($this->maybe_halt_execution()) {
            return $value;
        }
        if ($this->ngl_not_newsletter_check_column($column_name)) {
            return $value;
        }

        $user = get_userdata($subscriber_id);
        if (!$user) {
            return 'User not found';
        }

        $email = $user->user_email;

        // Just output a text reference that JavaScript will find and replace with a button
        // Format: ngl_check_[subscriber_id]_[email]
        return 'ngl_check_' . $subscriber_id . '_' . esc_attr($email);
    }

    public function initialize_esp()
    {
        if( $this->stop_execution_if_not_in_allowed_page() ) {
            return;
        }
        
        if($this->maybe_halt_execution()) {
            return;
        }
        
        $esp = $this->get_current_active_esp();

        // Include the ESP class file
        $esp_file =  newsletterglue_get_path($esp) . '/init.php';
        if (file_exists($esp_file)) {
            include_once $esp_file;
        }

        // If sendy external is active, use the external class.
        $classname = $this->sendy_external ? '\NGL_' . ucfirst($esp) . '_External' : '\NGL_' . ucfirst($esp);

        $api = new $classname();

        // Initialize the API client
        if (method_exists($api, 'connect')) {
            $api->connect();
        }

        $this->esp_instance = $api;

    }

    public function check_subscriber($email)
    {
        $subscriber = $this->esp_instance->get_subscriber($email);

        return $subscriber;
    }

    public function ngl_compatibility_check()
    {
        $this->compatibility_with_leaky_paywall_coupons();
        return;
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook)
    {
        // Only enqueue on the subscribers page
        if ($hook !== 'leaky-paywall_page_leaky-paywall-subscribers') {
            return;
        }

        // Create directory paths if they don't exist
        $js_dir = NGL_PLUGIN_DIR . 'modules/newsletter-subscriber-checker/assets/js';
        $css_dir = NGL_PLUGIN_DIR . 'modules/newsletter-subscriber-checker/assets/css';

        if (!file_exists($js_dir)) {
            wp_mkdir_p($js_dir);
        }

        if (!file_exists($css_dir)) {
            wp_mkdir_p($css_dir);
        }

        // Enqueue JavaScript
        wp_enqueue_script(
            'ngl-subscriber-checker',
            plugins_url('assets/js/subscriber-checker.js', __FILE__),
            array('jquery'),
            NGL_VERSION,
            true
        );

        // Localize script with AJAX URL and nonce
        wp_localize_script(
            'ngl-subscriber-checker',
            'ngl_subscriber_checker',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('ngl_subscriber_checker_nonce')
            )
        );

        // Enqueue CSS
        wp_enqueue_style(
            'ngl-subscriber-checker',
            plugins_url('assets/css/subscriber-checker.css', __FILE__),
            array(),
            NGL_VERSION
        );
    }

    /**
     * AJAX handler for checking subscriber status
     */
    public function ajax_check_subscriber()
    {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'ngl_subscriber_checker_nonce')) {
            wp_send_json_error('Security check failed');
        }

        // Get email from request
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';

        if (empty($email)) {
            wp_send_json_error('Email is required');
        }

        // Check subscriber status
        $subscriber = $this->check_subscriber($email);

        // Format the response
        $response = $this->format_subscriber_check_result($email, $subscriber);

        wp_send_json_success($response);
    }

    /**
     * Format the subscriber check result for display
     * 
     * This is the master format method that wraps ESP-specific formatting
     * 
     * @param string $email The subscriber email
     * @param mixed $subscriber The subscriber data returned from the ESP
     * @return string Formatted HTML output
     */
    public function format_subscriber_check_result($email, $subscriber)
    {
        $esp = $this->get_current_active_esp();
        $esp_name = ucfirst($esp);

        // Common header for all ESPs
        $output = '<div class="nglsc-results-container">';
        $output .= '<h3>Subscriber Check Results</h3>';
        $output .= '<p><strong>Email:</strong> ' . esc_html($email) . '</p>';
        $output .= '<p><strong>ESP:</strong> ' . esc_html($esp_name) . '</p>';

        // Check if the ESP instance has a specific formatting method
        if (method_exists($this->esp_instance, 'format_subscriber_data')) {
            // Use the ESP-specific formatting method
            $esp_output = $this->esp_instance->format_subscriber_data($email, $subscriber);
            $output .= $esp_output;
        } else {
            // Fallback to generic formatting
            if (empty($subscriber)) {
                $output .= '<div class="nglsc-subscriber-error">';
                $output .= '<p>⚠️ Subscriber not found in ' . esc_html($esp_name) . '.</p>';
                $output .= '</div>';
            } else {
                $output .= '<div class="nglsc-subscriber-result">';
                $output .= '<p>✅ Subscriber found in ' . esc_html($esp_name) . '.</p>';

                // Add subscriber details if available
                if (is_array($subscriber) || is_object($subscriber)) {
                    $output .= '<h4>Subscriber Details:</h4>';
                    $output .= '<pre>' . print_r($subscriber, true) . '</pre>';
                }

                $output .= '</div>';
            }
        }
        
        $output .= '</div>'; // Close the container

        return $output;
    }

    public function compatibility_with_leaky_paywall_coupons()
    {

        if (! class_exists('Leaky_Paywall_Coupons')) {
            return;
        }

        remove_filter('manage_leaky_paywall_subscribers_custom_column', array($this, 'ngl_newsletter_check_subscriber_column_content'), 10, 3);

        add_filter('manage_leaky_paywall_subscribers_custom_column', array($this, 'ngl_newsletter_check_subscriber_column_content'), 100, 3);
    }
}
