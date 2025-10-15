## 1\. Project Overview & Context

This is a WordPress plugin designed to integrate the WordPress post publishing lifecycle with the Omeda email marketing platform. Its primary function is to automatically create, configure, and schedule an email deployment in Omeda when a WordPress post is published or scheduled.

### Core Components:

*   **`omeda-wp-integration.php`**: The main plugin entry point. It initializes all components.
    
*   **`class-omeda-settings.php`**: Manages a global settings page for API credentials (`App ID`, `Brand Abbreviation`) and deployment defaults.
    
*   **`class-omeda-deployment-types.php`**: Creates a Custom Post Type (CPT) named "Deployment Types." Each "Deployment Type" post stores a reusable configuration for a newsletter, including the Omeda `Deployment Type ID`, the audience `Query Name`, `From Name`, `Reply To` email, etc.
    
*   **`class-omeda-hooks.php`**: Adds a meta box to the post editor, allowing a user to select a "Deployment Type" for that post. It also hooks into the `transition_post_status` action to trigger the deployment workflow when a post is published or scheduled.
    
*   **`class-omeda-api-client.php`**: A wrapper for all Omeda API interactions. It handles sending requests for each step of the deployment process.
    
*   **`class-omeda-workflow-manager.php`**: The core of the plugin. It manages a 5-step asynchronous deployment process using WordPress's built-in cron system (`WP-Cron`).
    
### API Documentation

Reference Omeda API documentation can be found in the `docs/omeda_api_docs` directory within this project. The documentation is provided in Markdown format.

### Development Environment

The plugin is being developed and tested in a local environment managed by `@wordpress/env` (`wp-env`), which uses Docker. This environment relies on the default traffic-dependent WP-Cron triggering mechanism and does not have a dedicated server-level cron job configured.

### The Asynchronous Workflow (`Omeda_Workflow_Manager`):

The process is designed to be robust by using background processing and polling Omeda for status updates.

1.  **Step 1: Create Deployment (Synchronous)**
    
    *   Triggered when a post is published/scheduled.
        
    *   Calls `step1_create_deployment` in the API client.
        
    *   Successfully receives a `TrackId` from Omeda.
        
    *   Schedules the `process_step` cron job to execute Step 2.
        
2.  **Step 2: Assign Audience (Asynchronous)**
    
    *   Handled by the `process_step` cron job.
        
    *   **Prerequisite Check**: Must confirm the deployment exists in Omeda via `get_deployment_lookup`.
        
    *   Calls `step2_assign_audience`.
        
3.  **Step 3: Add Content (Asynchronous)**
    
    *   Handled by `process_step`.
        
    *   **Prerequisite Check**: Must confirm audience processing is complete by calling `is_audience_ready`, which checks for the existence of the `RecipientCount` field in the deployment lookup response.
        
    *   Calls `step3_add_content`.
        
4.  **Step 4: Send Test (Asynchronous)**
    
    *   Handled by `process_step`.
        
    *   **Prerequisite Check**: Same as Step 3.
        
    *   Calls `step4_send_test`.
        
5.  **Step 5: Schedule Deployment (Asynchronous)**
    
    *   Handled by `process_step`.
        
    *   **Prerequisite Check**: Same as Step 3.
        
    *   Calls `step5_schedule_deployment`.
        

## 2\. The Problem

The workflow successfully completes **Step 1**. A new deployment is created in the Omeda platform, and its `TrackId` is correctly saved to the post meta.

However, the workflow **never proceeds past Step 1**. The subsequent asynchronous steps—Assign Audience (Step 2), Add Content (Step 3), Send Test (Step 4), and Schedule Deployment (Step 5)—never execute successfully. The workflow log in the post meta box shows that Step 1 is complete, but no further logs are generated for the subsequent steps, or it gets stuck in a retry loop.

### What Works:

*   Plugin can be installed and activated.
    
*   API settings can be saved.
    
*   Deployment Type configurations can be created.
    
*   A Deployment Type can be selected on a post.
    
*   When the post is published, `initiate_workflow` is called.
    
*   `step1_create_deployment` is successful, and a `TrackId` is generated and stored.
    

### What Fails:

*   The `process_step` cron job appears to fail or stall before executing `step2_assign_audience`.
    
*   The audience is never assigned to the deployment in Omeda.
    
*   The post content is never added to the deployment.
    
*   No test email is sent.
    
*   The deployment is never scheduled.
    

## 3\. Request for Analysis & Debugging

Please analyze the provided codebase, focusing on the interaction between `class-omeda-workflow-manager.php` and `class-omeda-api-client.php`, to identify the root cause of this failure.

### Key Areas to Investigate:

1.  **WP-Cron Execution**: Is there a possibility that the `wp_schedule_single_event` call in `initiate_workflow` is failing or that the cron job (`omeda_workflow_process_step`) is not firing as expected? Please suggest robust methods for debugging and logging WP-Cron events to verify their execution.
    
2.  **Prerequisite Polling Logic**: Review the `check_prerequisites` method in `Omeda_Workflow_Manager` and the `is_audience_ready` method in `Omeda_API_Client`. The logic in `is_audience_ready` relies on the `RecipientCount` key being present in the API response. If Omeda takes a while to process the audience and this key is absent, the check will continuously fail, eventually timing out after `MAX_RETRIES`. Is this polling logic sound, or is there a more reliable status field to check?
    
3.  **API Payloads**: Scrutinize the payload construction for the failing steps, particularly `step2_assign_audience`. A malformed payload for this first asynchronous step could cause the Omeda API to reject the request in a way that the plugin doesn't correctly interpret, leading to a stalled workflow. Is the nested array structure for the `Audience` parameter correct?
    
4.  **Error Handling & Logging**: The current error handling within the `process_step` `try...catch` block logs a generic message. Suggest improvements to the logging within `Omeda_Workflow_Manager` and `Omeda_API_Client` to capture the specific HTTP status codes and full API error responses from Omeda. This is crucial for understanding why a step might be failing.
    

Please provide a summary of the likely cause and suggest specific code modifications to fix the issue and improve the plugin's resilience and debuggability.

## 4\. Worklog & Task Documentation

As you work through the "Request for Analysis & Debugging," you will maintain a rolling task list and worklog in a separate file named `WORKLOG.md`. For each key area you investigate, your worklog entry must outline:

*   **Task:** The specific section of the request being addressed (e.g., "3.1 WP-Cron Execution").
    
*   **Process:** The diagnostic steps you performed.
    
*   **Obstacles:** Any issues you were unable to resolve or questions that arose during the process.
    
*   **Solution:** The final conclusion, including a diff of any code changes made and a clear explanation of _why_ those changes were necessary to solve the problem.
    

This will provide a clear, auditable trail of the debugging process.