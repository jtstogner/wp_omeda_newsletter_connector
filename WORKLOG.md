# Omeda Newsletter Connector Debugging Worklog

This document tracks the analysis, debugging, and resolution process for the issues outlined in `jules.md`.

## Task Index

1.  [WP-Cron Execution](#1-wp-cron-execution)
2.  [Prerequisite Polling Logic](#2-prerequisite-polling-logic)
3.  [API Payloads](#3-api-payloads)
4.  [Error Handling & Logging](#4-error-handling--logging)

---

## 1. WP-Cron Execution

*   **Task:** Investigate the reliability of `wp_schedule_single_event` and the `omeda_workflow_process_step` cron job in the `@wordpress/env` environment.
*   **Process:**
    *   Identified that the default WP-Cron triggering mechanism, which relies on site traffic, is unreliable in a local, low-traffic development environment. This was a likely contributor to the asynchronous steps failing to execute.
*   **Obstacles:**
    *   None.
*   **Solution:**
    *   Implemented a "cron-warming" (loopback) request to ensure cron jobs are triggered reliably.
    *   **File Modified:** `src/omeda-newsletter-connector/includes/class-omeda-workflow-manager.php`
    *   **Changes:** A new `trigger_cron_spawn()` method was added, which makes a non-blocking `wp_remote_post` call to the site's `wp-cron.php` URL. This method is now called immediately after an event is scheduled in `schedule_step`. This action forces the cron runner to check for due events, ensuring the asynchronous workflow proceeds promptly without needing external traffic. As a best practice, `wp_clear_scheduled_hook` was also added before scheduling a new event to prevent duplicates.

---

## 2. Prerequisite Polling Logic

*   **Task:** Review the `check_prerequisites` and `is_audience_ready` methods to determine if the reliance on the `RecipientCount` key is the cause of the workflow stall.
*   **Process:**
    *   Analyzed the logic in `is_audience_ready`. The approach of checking for the existence of the `RecipientCount` key is a valid strategy for determining if audience processing is complete.
    *   Concluded that the workflow was stalling *before* this polling logic could even be a factor, as the initial call to assign the audience was failing.
*   **Obstacles:**
    *   None.
*   **Solution:**
    *   No code changes were necessary for this part. The investigation confirmed that the polling logic was not the root cause of the stall. After fixing the API payload issue (see section 3), the existing polling logic now functions as intended.

---

## 3. API Payloads

*   **Task:** Scrutinize the payload construction for `step2_assign_audience` and other asynchronous steps to ensure they are not being rejected by the Omeda API.
*   **Process:**
    *   Reviewed the `email-deployment-add-audience.md` API documentation provided in the project's `docs/` folder.
    *   Compared the documented payload structure for a *single audience assignment* with the implementation in `Omeda_API_Client::step2_assign_audience`.
*   **Obstacles:**
    *   None.
*   **Solution:**
    *   Discovered a critical mismatch. The code was wrapping the audience query details in an `"Audience": [{...}]` array, a format the Omeda API uses for submitting *multiple* audiences in a single call.
    *   **File Modified:** `src/omeda-newsletter-connector/includes/class-omeda-api-client.php`
    *   **Changes:** The payload for `step2_assign_audience` was corrected to be a flat JSON object, placing `QueryName`, `OutputCriteria`, `SplitNumber`, etc., at the top level, as required by the documentation for a single audience. This was the primary bug causing the workflow to stall.

---

## 4. Error Handling & Logging

*   **Task:** Improve logging in `Omeda_Workflow_Manager` and `Omeda_API_Client` to capture specific HTTP status codes and full API error responses from Omeda.
*   **Process:**
    *   Refactored the error handling in the API client to capture more context.
    *   Refactored the logging methods in the workflow manager to store logs in a structured, exportable format as requested.
*   **Obstacles:**
    *   None.
*   **Solution:**
    *   **File Modified:** `src/omeda-newsletter-connector/includes/class-omeda-api-client.php`
    *   **Changes:** The `send_request` method's `catch` block was updated to throw an exception containing a structured JSON string. This JSON includes the HTTP error, the API endpoint, the payload sent, and the full decoded error response from Omeda.
    *   **File Modified:** `src/omeda-newsletter-connector/includes/class-omeda-workflow-manager.php`
    *   **Changes:** The `add_to_workflow_log` method was updated to store each log entry as a JSON object in the `_omeda_workflow_log` post meta. The `log_error` method can now parse the structured exception from the API client, providing rich, detailed error logs that include the full API response context.
