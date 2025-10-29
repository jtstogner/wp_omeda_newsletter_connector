<?php
/**
 * Manages fetching and caching of data from the Omeda API.
 */
class Omeda_Data_Manager {

    const DEPLOYMENT_TYPES_TRANSIENT = 'omeda_deployment_types_cache';
    const CACHE_DURATION = DAY_IN_SECONDS; // 24 hours

    /**
     * Get Omeda Deployment Types, utilizing cache unless forced.
     *
     * @param bool $force_refresh Bypass the cache and call the API.
     * @return array|WP_Error Associative array of ID => Name on success, or WP_Error on failure.
     */
    public static function get_deployment_types($force_refresh = false) {
        $cached_types = get_transient(self::DEPLOYMENT_TYPES_TRANSIENT);

        if (false === $cached_types || $force_refresh) {
            try {
                $api_client = new Omeda_API_Client();
                $response = $api_client->get_deployment_types();

                if (is_array($response)) {
                    $formatted_types = self::format_api_response($response);
                    set_transient(self::DEPLOYMENT_TYPES_TRANSIENT, $formatted_types, self::CACHE_DURATION);
                    return $formatted_types;
                } else {
                    // If the response was not an array, it's an unexpected result.
                    return new WP_Error('api_error', 'Invalid response received from Omeda API when fetching deployment types.');
                }

            } catch (Exception $e) {
                $error_message = 'Error fetching Omeda Deployment Types: ' . $e->getMessage();
                error_log($error_message);
                // Return the error to be displayed in the UI
                return new WP_Error('api_exception', $error_message);
            } catch (Throwable $t) {
                // Catch PHP 7+ errors
                $error_message = 'Fatal error fetching Omeda Deployment Types: ' . $t->getMessage();
                error_log($error_message);
                return new WP_Error('api_fatal', $error_message);
            }
        }

        return $cached_types;
    }

    /**
     * Helper to format the raw API response into a simple ID => Name array.
     *
     * Per Omeda API documentation:
     * Response is a single object with a "DeploymentTypes" array:
     * {
     *   "DeploymentTypes": [
     *     {"Id": 2344, "Name": "...", "Description": "...", "StatusCode": 1}
     *   ]
     * }
     *
     * @param array $data The raw data from the API.
     * @return array The formatted data as ID => Name.
     */
    private static function format_api_response($data) {
        $formatted = [];
        
        // Per API docs, deployment types are in a "DeploymentTypes" array
        if (isset($data['DeploymentTypes']) && is_array($data['DeploymentTypes'])) {
            foreach ($data['DeploymentTypes'] as $item) {
                // Use "Name" field per API spec, fall back to "Description" if needed
                $name = $item['Name'] ?? $item['Description'] ?? 'Unknown';
                
                // Only include active deployment types (StatusCode = 1)
                if (isset($item['Id']) && isset($item['StatusCode']) && $item['StatusCode'] == 1) {
                    $formatted[$item['Id']] = $name;
                }
            }
        }
        
        return $formatted;
    }
}
