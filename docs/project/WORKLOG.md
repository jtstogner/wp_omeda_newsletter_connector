# Omeda Newsletter Connector Debugging and Refactoring Worklog

This document tracks the analysis, debugging, and resolution process for the issues and new requirements outlined in `jules.md` and subsequent discussions.

## Task Index

1.  [Workflow Architecture](#1-workflow-architecture)
2.  [API Payloads](#2-api-payloads)
3.  [Error Handling & Logging](#3-error-handling--logging)
4.  [New Feature Implementation](#4-new-feature-implementation)


---

## 1. Workflow Architecture

*   **Task:** Investigate the reliability of the asynchronous, `WP-Cron`-based workflow and refactor it to meet new, multi-stage requirements.
*   **Process:**
    *   Initially identified that the `WP-Cron` system was unreliable in the local development environment, contributing to the workflow stall.
    *   Based on new requirements from the user, the decision was made to completely refactor the architecture, moving away from a cron-based system entirely.
*   **Obstacles:**
    *   None.
*   **Solution:**
    *   The entire asynchronous, cron-based workflow was removed from `Omeda_Workflow_Manager`.
    *   A new, synchronous, multi-stage workflow was implemented using direct WordPress hooks (`save_post`, `transition_post_status`).
    *   **Files Modified:** `src/omeda-newsletter-connector/includes/class-omeda-hooks.php`, `src/omeda-newsletter-connector/includes/class-omeda-workflow-manager.php`
    *   **Changes:** The logic is now driven by post-state changes. `save_post` handles the initial deployment creation and subsequent content updates. `transition_post_status` handles the final scheduling and testing when a post is published. This new architecture is more reliable, easier to debug, and directly supports the new feature requirements.

---

## 2. API Payloads

*   **Task:** Scrutinize the payload construction for `step2_assign_audience`.
*   **Process:**
    *   Reviewed the `email-deployment-add-audience.md` API documentation.
    *   Compared the documented payload structure with the code implementation.
*   **Obstacles:**
    *   None.
*   **Solution:**
    *   Discovered a critical mismatch in the payload for `step2_assign_audience`. The code was incorrectly nesting the data within an "Audience" array.
    *   **File Modified:** `src/omeda-newsletter-connector/includes/class-omeda-api-client.php`
    *   **Changes:** Corrected the payload to be a flat JSON object as required by the documentation. This was the root cause of the original workflow stall.

---

## 3. Error Handling & Logging

*   **Task:** Improve logging to capture specific HTTP status codes and full API error responses, and format logs as JSON.
*   **Process:**
    *   Refactored the `send_request` method in the API client to throw more detailed exceptions.
    *   Updated the logging methods in the workflow manager to store structured JSON.
*   **Obstacles:**
    *   None.
*   **Solution:**
    *   **File Modified:** `src/omeda-newsletter-connector/includes/class-omeda-api-client.php`
    *   **Changes:** The `send_request` method now throws an exception containing a structured JSON string with the full API error context.
    *   **File Modified:** `src/omeda-newsletter-connector/includes/class-omeda-workflow-manager.php`
    *   **Changes:** The `add_to_workflow_log` method now stores each log entry as a JSON object. The `log_error` method can parse the detailed exception from the API client, providing rich, exportable debug logs. The meta box log display was also updated to parse and render this JSON.

---

## 4. New Feature Implementation

*   **Task:** Implement the new multi-stage workflow, a settings-page delay, and a locking UI.
*   **Process:**
    *   Added a new field to the settings page.
    *   Implemented the core logic for the new workflow in the refactored hook and manager classes.
    *   Modified the post meta box to disable the deployment type selector after creation.
*   **Obstacles:**
    *   None.
*   **Solution:**
    *   **File Modified:** `src/omeda-newsletter-connector/includes/class-omeda-settings.php`
    *   **Changes:** Added a new "Immediate Publish Delay (minutes)" field to the admin settings page.
    *   **File Modified:** `src/omeda-newsletter-connector/includes/class-omeda-hooks.php`
    *   **Changes:** The `render_meta_box` function now disables the deployment type `<select>` element and adds a descriptive message if a `track_id` exists for the post, effectively locking it.
    *   **File Modified:** `src/omeda-newsletter-connector/includes/class-omeda-hooks.php`, `src/omeda-newsletter-connector/includes/class-omeda-workflow-manager.php`
    *   **Changes:** The full logic for the new workflow (create on draft, update on save, schedule on publish) was implemented across these files.
