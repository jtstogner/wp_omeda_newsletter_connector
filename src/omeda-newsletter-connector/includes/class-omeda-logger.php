<?php
/**
 * Standalone logger for the Omeda integration.
 * This allows both the API client and workflow manager to log without circular dependencies.
 */
class Omeda_Logger {

    /**
     * Log levels
     */
    const LEVEL_INFO = 'INFO';
    const LEVEL_DEBUG = 'DEBUG';
    const LEVEL_RAW = 'RAW';
    const LEVEL_ERROR = 'ERROR';

    /**
     * Get the current logging level from settings
     */
    private static function get_logging_level() {
        $level = get_option('omeda_logging_level', 'basic');
        return $level; // 'basic', 'advanced', or 'raw'
    }

    /**
     * Check if a log level should be recorded based on current settings
     */
    private static function should_log($level) {
        $current_level = self::get_logging_level();
        
        // Always log errors
        if ($level === self::LEVEL_ERROR) {
            return true;
        }

        // Basic level: Only INFO and ERROR
        if ($current_level === 'basic') {
            return in_array($level, [self::LEVEL_INFO, self::LEVEL_ERROR]);
        }

        // Advanced level: INFO, DEBUG, and ERROR
        if ($current_level === 'advanced') {
            return in_array($level, [self::LEVEL_INFO, self::LEVEL_DEBUG, self::LEVEL_ERROR]);
        }

        // Raw level: Everything
        if ($current_level === 'raw') {
            return true;
        }

        return false;
    }

    /**
     * Log a message to the workflow log for a specific post
     * 
     * @param int $post_id The post ID
     * @param string $level Log level (INFO, DEBUG, RAW, ERROR)
     * @param string $message The message to log
     * @param string $step Optional step identifier (e.g., 'create_deployment')
     * @param array $context Optional context data to store
     * @param int $retry Optional retry number
     */
    public static function log($post_id, $level, $message, $step = null, $context = null, $retry = null) {
        if (!self::should_log($level)) {
            return;
        }

        $log_entries = get_post_meta($post_id, '_omeda_workflow_log', true);
        if (!is_array($log_entries)) {
            $log_entries = [];
        }

        $entry = [
            'timestamp' => current_time('mysql'),
            'level'     => $level,
            'message'   => $message,
        ];

        if ($step !== null) {
            $entry['step'] = $step;
        }

        if ($retry !== null) {
            $entry['retry'] = $retry;
        }

        if ($context !== null) {
            $entry['context'] = $context;
        }

        $log_entries[] = $entry;
        update_post_meta($post_id, '_omeda_workflow_log', $log_entries);

        // Also write to PHP error log if advanced or raw logging is enabled
        if (in_array(self::get_logging_level(), ['advanced', 'raw'])) {
            $log_line = sprintf(
                '[Omeda] [Post %d] [%s] %s%s',
                $post_id,
                $level,
                $step ? "[$step] " : '',
                $message
            );
            error_log($log_line);
        }
    }

    /**
     * Log an API request
     * 
     * @param int|null $post_id Optional post ID for context
     * @param string $method HTTP method
     * @param string $url Full URL
     * @param array $headers Request headers
     * @param mixed $body Request body
     * @param string $step Optional step identifier
     */
    public static function log_api_request($post_id, $method, $url, $headers, $body, $step = null) {
        if (!self::should_log(self::LEVEL_RAW)) {
            return;
        }

        // Sanitize sensitive data
        $sanitized_headers = $headers;
        if (isset($sanitized_headers['x-omeda-appid'])) {
            $app_id = $sanitized_headers['x-omeda-appid'];
            $sanitized_headers['x-omeda-appid'] = substr($app_id, 0, 4) . '...' . substr($app_id, -4);
        }

        $context = [
            'type' => 'request',
            'method' => $method,
            'url' => $url,
            'headers' => $sanitized_headers,
            'body' => is_string($body) ? (strlen($body) > 10000 ? substr($body, 0, 10000) . "\n...[truncated]" : $body) : $body,
        ];

        $message = sprintf('API Request: %s %s', $method, $url);
        
        if ($post_id) {
            self::log($post_id, self::LEVEL_RAW, $message, $step, $context);
        }

        // Always log to error_log for raw level
        error_log('=== Omeda API Request ===');
        error_log('URL: ' . $url);
        error_log('Method: ' . $method);
        error_log('Headers: ' . json_encode($sanitized_headers));
        error_log('Body: ' . (is_string($body) ? (strlen($body) > 1000 ? substr($body, 0, 1000) . '...[truncated]' : $body) : json_encode($body)));
    }

    /**
     * Log an API response
     * 
     * @param int|null $post_id Optional post ID for context
     * @param int $status_code HTTP status code
     * @param string $response_body Response body
     * @param string $step Optional step identifier
     * @param array $headers Optional response headers
     */
    public static function log_api_response($post_id, $status_code, $response_body, $step = null, $headers = []) {
        if (!self::should_log(self::LEVEL_RAW)) {
            return;
        }

        $context = [
            'type' => 'response',
            'status_code' => $status_code,
            'body' => strlen($response_body) > 10000 ? substr($response_body, 0, 10000) . "\n...[truncated]" : $response_body,
        ];

        if (!empty($headers)) {
            $context['headers'] = $headers;
        }

        $message = sprintf('API Response: HTTP %d', $status_code);
        
        if ($post_id) {
            self::log($post_id, self::LEVEL_RAW, $message, $step, $context);
        }

        // Always log to error_log for raw level
        error_log('=== Omeda API Response ===');
        error_log('Status Code: ' . $status_code);
        error_log('Response Body: ' . (strlen($response_body) > 1000 ? substr($response_body, 0, 1000) . '...[truncated]' : $response_body));
    }

    /**
     * Log an API error
     * 
     * @param int|null $post_id Optional post ID for context
     * @param string $error_message Error message
     * @param string $step Optional step identifier
     * @param array $context Optional additional context
     */
    public static function log_api_error($post_id, $error_message, $step = null, $context = []) {
        $context['type'] = 'error';
        $context['error'] = $error_message;

        if ($post_id) {
            self::log($post_id, self::LEVEL_ERROR, $error_message, $step, $context);
        }

        error_log('[Omeda API Error] ' . $error_message);
    }

    /**
     * Clear logs for a specific post
     */
    public static function clear_logs($post_id) {
        delete_post_meta($post_id, '_omeda_workflow_log');
    }

    /**
     * Get all logs for a specific post
     */
    public static function get_logs($post_id) {
        $logs = get_post_meta($post_id, '_omeda_workflow_log', true);
        return is_array($logs) ? $logs : [];
    }

    /**
     * Format context data for display
     */
    public static function format_context($context) {
        if (is_array($context)) {
            return print_r($context, true);
        }
        return (string) $context;
    }
}
