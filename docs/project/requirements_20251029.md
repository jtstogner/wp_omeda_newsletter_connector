#### 1\. Overview

The primary objective is to develop a WordPress plugin that seamlessly connects Newsletter Glue Pro with the Omeda platform. This integration will automate the creation, content assignment, audience assignment, and testing of Omeda email deployments directly from the WordPress interface when a newsletter is saved.

#### 2\. System Architecture

The plugin will adhere to WordPress coding standards and utilize the following components:

*   **Admin Interface:** A centralized "Omeda Utilities" menu for configuration and monitoring.
    
*   **Omeda API Client:** A robust, encapsulated PHP class to handle all Omeda API interactions, including authentication, request handling, and error management.
    
*   **Newsletter Glue Integration:** Utilizing WordPress hooks (primarily `save_post`) to intercept relevant actions.
    
*   **Asynchronous Processing Framework:** A reliable background processing system for handling sequential API calls.
    
*   **Data Storage:** Utilizing the WordPress Options API for settings, Post Meta API for deployment tracking, and a Custom Post Type (CPT) for robust logging.
    

#### 3\. Omeda Utilities Admin Menu

A top-level menu named "Omeda Utilities" will be established in the WP-Admin dashboard.

*   **3.1. Settings Page:**
    
    *   Secure storage for Omeda API credentials (API Key, Client ID, Brand Abbreviation).
        
    *   Global configuration for test deployments (e.g., default recipient email addresses).
        
    *   Configuration for trigger conditions (e.g., specific status transitions).
        
*   **3.2. Deployment Mappings Page:**
    
    *   An interface to define the relationship between WordPress content and Omeda configurations.
        
    *   Mappings will associate a Newsletter Glue template or "story type" (determined via post type, taxonomy, or meta) with:
        
        *   Omeda Deployment Type ID.
            
        *   Omeda Audience Query ID (the predefined audience segment).
            
*   **3.3. Logs Page:** (See Section 6)
    

#### 4\. Newsletter Glue Integration and Triggering

The integration hinges on detecting when a newsletter is saved and initiating the deployment sequence.

*   **4.1. Trigger Mechanism:** The plugin will hook into the `save_post` action.
    
*   **4.2. Filtering and Validation:** The plugin must verify:
    
    *   The post type matches a Newsletter Glue type.
        
    *   It is not an autosave or revision.
        
    *   A valid mapping exists for the newsletter's template/story type.
        
    *   The trigger conditions (e.g., status transition) are met.
        
*   **4.3. Trigger Conditions and Debouncing (Critical Discussion Point):** Triggering an API sequence on every `save_post` can lead to excessive API calls and unnecessary deployments.
    
    *   **Recommendation:** Implement **debouncing** (waiting for a period of inactivity, e.g., 5 minutes, after the last save) or utilize **explicit triggers** (e.g., only on specific status transitions like Draft to Published, or via a manual "Sync to Omeda" button).
        

#### 5\. Automated Deployment Sequence (Asynchronous)

When triggered, an asynchronous job will manage the deployment sequence.

*   **5.1. Create/Update Deployment:** Call the Omeda API to create a new deployment. If a Track ID already exists (see 5.3), the plugin should update the existing deployment instead.
    
*   **5.2. Content Transformation and Assignment:** Extract the HTML content from Newsletter Glue. A transformation layer must ensure the HTML is compatible with Omeda requirements (e.g., adding Olytics tracking parameters) before assigning it to the deployment.
    
*   **5.3. Track ID Association:** Store the Omeda Deployment Track ID in the WordPress post meta (e.g., `_omeda_track_id`).
    
*   **5.4. Audience Assignment:** Call the Omeda API to initiate the audience calculation using the mapped Audience Query ID.
    
*   **5.5. Send Test:** Initiate a test send to the configured recipients.
    

#### 6\. Logging and Status Monitoring

*   **6.1. Logging Mechanism:** A Custom Post Type (e.g., `omeda_log`) will store detailed logs, integrating well with the native WordPress UI.
    
*   **6.2. Log Data:** Logs will include Timestamp, Newsletter Post ID, Omeda Track ID, Action (e.g., "Created", "Audience Assigned", "Error"), and API response details.
    
*   **6.3. Status Monitoring:** The system must track when the deployment is "ready" (audience calculation is complete and the test has been sent). This requires periodic polling of the Omeda API unless Omeda supports webhooks (which should be investigated).
    
*   **6.4. UI Feedback:** Display the current Omeda deployment status within the Newsletter Glue editing interface.
    

### Work Plan

#### Phase 1: Plugin Foundation and Configuration (Est. 1.5 Weeks)

*   **Task 1.1: Plugin Scaffolding and Architecture:** Set up the plugin structure.
    
*   **Task 1.2: Omeda Admin Menu and Settings:** Implement the "Omeda Utilities" menu and "Settings" page using the WordPress Settings API.
    
*   **Task 1.3: Omeda API Client Development:** Develop the core PHP class for authentication and communication with Omeda, including robust error handling.
    
*   **Task 1.4: Deployment Mappings UI:** Create the interface for managing mappings and audience queries.
    

#### Phase 2: Asynchronous Processing Framework (Est. 1 Week)

*   **Task 2.1: Framework Implementation:** Implement the background processing system.
    
    *   **Discussion on WP-Cron vs. Action Scheduler:** While the request specified using the built-in WP-Cron, it is often unreliable for critical, sequential API processes because it depends on site traffic.
        
    *   **Recommendation:** Use the **Action Scheduler** library. It is the industry standard for WordPress background processing (used by WooCommerce). It utilizes WordPress's underlying infrastructure but provides the necessary reliability, sequencing, scalability, and retry mechanisms essential for this integration.
        
*   **Task 2.2: Job Sequencing Logic:** Implement the framework for scheduling and managing the sequential deployment steps.
    

#### Phase 3: Newsletter Glue Integration and Workflow (Est. 2-3 Weeks)

*   **Task 3.1: Trigger Implementation and Debouncing:** Implement the `save_post` hook integration, including the crucial debouncing logic or explicit trigger mechanisms (see Section 4.3).
    
*   **Task 3.2: Content Transformation Layer:** Develop the functions to sanitize and augment Newsletter Glue HTML for Omeda compatibility (e.g., Olytics parameters).
    
*   **Task 3.3: Deployment Sequence Handlers:** Implement the Action Scheduler job handlers for: Create/Update Deployment, Assign Content, Assign Audience, and Send Test.
    
*   **Task 3.4: Track ID Management:** Implement logic to store and manage the Omeda Track ID in post meta and handle updates correctly.
    

#### Phase 4: Logging and Monitoring (Est. 1.5 Weeks)

*   **Task 4.1: Logging CPT Implementation:** Register and implement the `omeda_log` Custom Post Type and logging helper functions.
    
*   **Task 4.2: Logs Interface:** Customize the CPT list table in WP-Admin (`WP_List_Table`) to provide a filterable and searchable log view.
    
*   **Task 4.3: Status Polling/Webhooks:** Implement a recurring Action Scheduler job to poll the Omeda API for deployment status (e.g., audience finalization), or integrate webhooks if available.
    
*   **Task 4.4: UI Feedback:** Display the current Omeda deployment status and Track ID within the Newsletter Glue editor sidebar.
    

#### Phase 5: Testing and Deployment (Est. 1 Week)

*   **Task 5.1: Unit and Integration Testing:** Comprehensive testing with Omeda sandbox environments.
    
*   **Task 5.2: Resilience Testing:** Testing API failures, timeouts, and the reliability of the background processing and retry mechanisms.
    
*   **Task 5.3: UAT and Deployment:** Staging deployment, User Acceptance Testing, and production rollout.
    

### Code Examples (Conceptual PHP)

**1\. Admin Menu Registration (Phase 1)**

PHP

```
add_action('admin_menu', 'omeda_utilities_menu');

function omeda_utilities_menu() {
    // Add the top-level menu
    add_menu_page(
        'Omeda Utilities', // Page title
        'Omeda Utilities', // Menu title
        'manage_options',  // Capability
        'omeda-utilities', // Menu slug
        'omeda_settings_page_callback', // Function to display the settings page
        'dashicons-email-alt' // Icon
    );

    // Add submenu pages (Settings, Mappings, Logs CPT)
    // Note: The first submenu often reuses the parent slug.
    add_submenu_page('omeda-utilities', 'Settings', 'Settings', 'manage_options', 'omeda-utilities', 'omeda_settings_page_callback');
    add_submenu_page('omeda-utilities', 'Mappings', 'Mappings', 'manage_options', 'omeda-mappings', 'omeda_mappings_page_callback');
    
    // Logs (assuming a CPT named 'omeda_log') would appear automatically if registered correctly, 
    // or linked manually here.
}
```

**2\. Triggering with Debounce using Action Scheduler (Phase 3)**

This example demonstrates debouncing by canceling any previously scheduled sync for this specific post and scheduling a new one 5 minutes in the future.

PHP

```
add_action('save_post', 'omeda_detect_newsletter_save', 10, 3);

function omeda_detect_newsletter_save($post_id, $post, $update) {
    // Basic checks (autosave, permissions)
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check if the post type is relevant and mapping exists (pseudo-code)
    if (!is_relevant_newsletter_glue_post($post) || !get_omeda_mapping($post)) {
        return;
    }
    
    // Check trigger conditions (e.g., status change)
    // ...

    $action_hook = 'omeda_initiate_deployment_sequence';
    $args = ['post_id' => $post_id];
    $group = 'omeda-deployment-' . $post_id;

    // Debounce: Cancel any existing pending action for this post
    if (function_exists('as_unschedule_all_actions')) {
        as_unschedule_all_actions($action_hook, $args, $group);

        // Schedule the new action 5 minutes (300 seconds) from now
        as_schedule_single_action(time() + 300, $action_hook, $args, $group);
        
        // Optionally log that the sync has been scheduled.
    }
}
```

**3\. Asynchronous Job Handler (Phase 3)**

PHP

```
add_action('omeda_initiate_deployment_sequence', 'omeda_handle_deployment_sequence', 10, 1);

function omeda_handle_deployment_sequence($post_id) {
    $post = get_post($post_id);
    $mapping = get_omeda_mapping($post); // Pseudo-code
    $track_id = get_post_meta($post_id, '_omeda_track_id', true);
    $content = apply_filters('the_content', $post->post_content); // Ensure content is fully rendered

    try {
        // Step 1 & 2: Create/Update Deployment and Assign Content
        // The sync_deployment function handles both creation and updates.
        $new_track_id = Omeda_API_Client::sync_deployment(
            $track_id,
            $post->post_title,
            transform_content_for_omeda($content),
            $mapping['deployment_type_id']
        );

        if ($track_id !== $new_track_id) {
            update_post_meta($post_id, '_omeda_track_id', $new_track_id);
            $track_id = $new_track_id;
            Omeda_Logger::log($post_id, $track_id, "Deployment created/updated.");
        }

        // Step 3: Assign Audience (Optimize: only if configuration changed or audience is stale)
        Omeda_API_Client::assign_audience($track_id, $mapping['audience_query_id']);
        Omeda_Logger::log($post_id, $track_id, "Audience assignment initiated.");

        // Step 4: Send Test
        $test_emails = get_option('omeda_global_test_emails');
        Omeda_API_Client::send_test($track_id, $test_emails);
        Omeda_Logger::log($post_id, $track_id, "Test deployment sent.");


    } catch (Exception $e) {
        Omeda_Logger::error($post_id, $track_id, "Deployment sequence failed: " . $e->getMessage());
        // Implement retry logic if necessary using Action Scheduler capabilities.
    }
}
```