### Updated Detailed Requirements Document

#### 3\. Omeda Utilities Admin Menu (Updates)

*   **3.1. Settings Page (Updates):**
    
    *   Add a "Test Connection and Refresh Omeda Data" button. This action will:
        
        *   Validate the provided Omeda API credentials.
            
        *   Trigger a forced refresh of the cached Omeda configuration data (Deployment Types, etc.).
            
        *   Provide immediate UI feedback on the success or failure of the connection.
            
*   **3.2. Deployment Mappings Page (Revised):**
    
    *   This interface defines the relationship between WordPress content and Omeda configurations. It will now dynamically source data from both systems.
        
    *   **Mapping Definition UI:** The interface will allow users to create and manage rules. Each rule will map the following:
        
        *   **Source: Newsletter Glue Template/Identifier:** A dropdown list populated by querying all available Newsletter Glue templates or story types. The plugin must determine how Newsletter Glue structures this data (likely a Custom Post Type or a Custom Taxonomy).
            
        *   **Destination: Omeda Deployment Type:** A dropdown list populated by the data fetched dynamically from the Omeda API (see Section 7).
            
        *   **Destination: Omeda Audience Query ID:** (Remains a manual input for now, but should be designed to accommodate dynamic fetching if the Omeda API supports retrieving saved queries).
            

#### 7\. Dynamic Omeda Resource Management and Caching (New Section)

To ensure the plugin configuration is accurate and performant, it must dynamically fetch and cache Omeda resources.

*   **7.1. Fetching Deployment Types:**
    
    *   The Omeda API Client (defined in Section 2 of the original document) will be extended with a dedicated method (e.g., `getDeploymentTypes()`) to call the relevant Omeda API endpoint for listing deployment types.
        
*   **7.2. Caching Mechanism:**
    
    *   The results from the Omeda API must be cached using the WordPress Transients API. This prevents excessive API calls during WP-Admin page loads.
        
*   **7.3. Cache Expiration and Refresh:**
    
    *   The cache should have a reasonable expiration time (e.g., 12 or 24 hours).
        
    *   The cache must be manually refreshable via the button on the Settings Page (see 3.1).
        
    *   An automatic background refresh should be scheduled (using Action Scheduler or WP-Cron) to ensure data does not become excessively stale.
        

### Updated Work Plan

The primary impact of these changes is on Phase 1, requiring the Omeda API Client to be more developed earlier in the process.

#### Phase 1: Plugin Foundation, Configuration, and Data Fetching (Est. 2 Weeks)

*   **Task 1.1: Plugin Scaffolding and Architecture:** (No change)
    
*   **Task 1.2: Omeda Admin Menu and Settings:** Implement the menu and settings page.
    
    *   **(Update)** Implement the "Test Connection and Refresh Omeda Data" functionality.
        
*   **Task 1.3: Omeda API Client Development (Updated):**
    
    *   Develop core authentication and communication methods.
        
    *   **(New)** Implement the `getDeploymentTypes()` method.
        
*   **Task 1.4: Resource Caching and Management (New):**
    
    *   Implement the caching layer using WordPress Transients.
        
    *   Implement the scheduled background refresh task.
        
*   **Task 1.5: Querying Newsletter Glue Structures (New):**
    
    *   Investigate Newsletter Glue's data structure (CPT or Taxonomy).
        
    *   Develop helper functions to retrieve the list of available templates/story types.
        
*   **Task 1.6: Deployment Mappings UI (Updated):**
    
    *   Create the interface for managing mappings.
        
    *   Populate the Omeda Deployment Type dropdown using the cached API data.
        
    *   Populate the Newsletter Glue Template selector using the results from Task 1.5.
        
    *   Implement CRUD operations for saving the mappings.
        

_(Phases 2 through 5 remain the same as the original work plan, as the core workflow execution logic is unchanged.)_

### Updated Code Examples (Conceptual PHP)

**1\. Omeda API Client Extension and Caching (Phase 1, Task 1.3 & 1.4)**

PHP

```
class Omeda_Data_Manager {
    const DEPLOYMENT_TYPES_TRANSIENT = 'omeda_deployment_types_cache';
    const CACHE_DURATION = DAY_IN_SECONDS; // 24 hours

    /**
     * Get Omeda Deployment Types, utilizing cache unless forced.
     *
     * @param bool $force_refresh Bypass the cache and call the API.
     * @return array Associative array of ID => Name.
     */
    public static function get_deployment_types($force_refresh = false) {
        $types = get_transient(self::DEPLOYMENT_TYPES_TRANSIENT);

        if (false === $types || $force_refresh) {
            try {
                // Assume Omeda_API_Client handles the actual HTTP request and authentication
                $api_client = new Omeda_API_Client();
                
                // Pseudo-code: Replace with the actual endpoint based on Omeda Docs
                $response = $api_client->get('/v2/brand/{{brandAbbreviation}}/deployment/type/*');

                if (is_wp_error($response)) {
                    // Handle error, maybe return stale cache if available
                    return (false !== $types) ? $types : [];
                }

                // Format the response for easy use in a dropdown
                $types = self::format_api_response($response);
                
                // Cache the result
                set_transient(self::DEPLOYMENT_TYPES_TRANSIENT, $types, self::CACHE_DURATION);

            } catch (Exception $e) {
                error_log('Error fetching Omeda Deployment Types: ' . $e->getMessage());
                return (false !== $types) ? $types : [];
            }
        }

        return $types;
    }

    /**
     * Helper to format the raw API response into a simple ID => Name array.
     */
    private static function format_api_response($data) {
        $formatted = [];
        // Adjust 'DeploymentTypes', 'Id', and 'Name' based on the actual Omeda API structure
        if (isset($data['DeploymentTypes']) && is_array($data['DeploymentTypes'])) {
            foreach ($data['DeploymentTypes'] as $item) {
                if (isset($item['Id']) && isset($item['Name'])) {
                    $formatted[$item['Id']] = sanitize_text_field($item['Name']);
                }
            }
        }
        return $formatted;
    }
}
```

**2\. Fetching Newsletter Glue Templates (Phase 1, Task 1.5)**

This example assumes Newsletter Glue stores templates as a Custom Post Type named `ng_template`.

PHP

```
/**
 * Query WordPress for available Newsletter Glue templates.
 *
 * @return array Associative array of Post ID => Post Title.
 */
function omeda_get_newsletter_glue_templates() {
    $args = array(
        'post_type'      => 'ng_template', // CONFIRM: Adjust this slug based on actual Newsletter Glue structure
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
        'fields'         => 'ids', // Optimization: Fetch only IDs
    );

    $template_ids = get_posts($args);
    $templates = [];

    foreach ($template_ids as $id) {
        $templates[$id] = get_the_title($id);
    }

    // If they use taxonomies instead of CPTs, this function needs to use get_terms() instead.
    
    return $templates;
}
```

**3\. Rendering the Mappings UI (Phase 1, Task 1.6)**

PHP

```
function omeda_mappings_page_callback() {
    // Fetch the dynamic data
    $ng_templates = omeda_get_newsletter_glue_templates();
    $omeda_types = Omeda_Data_Manager::get_deployment_types();
    
    // Load existing mappings (assuming they are stored in options)
    $saved_mappings = get_option('omeda_template_mappings', []); 

    echo '<div class="wrap"><h1>Omeda Deployment Mappings</h1>';
    
    // ... (HTML structure for the form/table) ...

    echo '<h2>New Mapping Rule</h2>';
    
    // Selector 1: Newsletter Glue Template
    echo '<label for="new_mapping_ng_template">Newsletter Glue Template:</label>';
    echo '<select id="new_mapping_ng_template" name="new_mapping_ng_template">';
    echo '<option value="">-- Select Source Template --</option>';
    foreach ($ng_templates as $id => $title) {
        echo sprintf('<option value="%s">%s</option>', esc_attr($id), esc_html($title));
    }
    echo '</select>';

    // Selector 2: Omeda Deployment Type
    echo '<label for="new_mapping_omeda_type">Omeda Deployment Type:</label>';
    echo '<select id="new_mapping_omeda_type" name="new_mapping_omeda_type">';
    echo '<option value="">-- Select Destination Type --</option>';
    if (empty($omeda_types)) {
        echo '<option value="" disabled>Error fetching types. Please check Settings and refresh.</option>';
    } else {
        foreach ($omeda_types as $id => $name) {
            echo sprintf('<option value="%s">%s (%s)</option>', esc_attr($id), esc_html($name), esc_attr($id));
        }
    }
    echo '</select>';
    
    // ... (Audience Query ID input and Save button) ...

    echo '</div>';
}
```