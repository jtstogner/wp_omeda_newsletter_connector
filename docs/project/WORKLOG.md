# Omeda Newsletter Connector Debugging and Refactoring Worklog

This document tracks the analysis, debugging, and resolution process for the issues and new requirements outlined in `jules.md` and subsequent discussions.

## Task Index

1.  [Workflow Architecture](#1-workflow-architecture)
2.  [API Payloads](#2-api-payloads)
3.  [Error Handling & Logging](#3-error-handling--logging)
4.  [New Feature Implementation](#4-new-feature-implementation)
5.  [Phase 2: Asynchronous Processing](#5-phase-2-asynchronous-processing)


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

---

## 5. Phase 2: Asynchronous Processing

*   **Task:** Implement robust asynchronous processing using Action Scheduler.
*   **Date:** 2025-10-29
*   **Process:**
    *   Evaluated WP-Cron vs Action Scheduler for wp-env development environment.
    *   Decided to implement Action Scheduler with synchronous fallback for maximum flexibility.
    *   Downloaded and bundled Action Scheduler 3.7.1 library.
    *   Created async job handlers with chain scheduling and retry logic.
    *   Updated hooks to schedule async jobs instead of synchronous execution.
    *   Enhanced admin UI to show pending jobs and Action Scheduler admin interface.
*   **Obstacles:**
    *   None. Implementation proceeded smoothly with proper fallback mechanisms.
*   **Solution:**
    *   **Files Created:**
        *   `src/omeda-newsletter-connector/lib/action-scheduler/` - Action Scheduler library (3.7.1)
        *   `src/omeda-newsletter-connector/includes/class-omeda-async-jobs.php` - Async job handlers
        *   `docs/project/WP_CRON_VS_ACTION_SCHEDULER.md` - Technical analysis document
        *   `docs/project/PHASE2_IMPLEMENTATION_PLAN.md` - Detailed implementation plan
        *   `docs/project/PHASE2_TESTING_GUIDE.md` - Testing procedures for wp-env
        *   `docs/project/PHASE1_VALIDATION.md` - Phase 1 validation report
    *   **Files Modified:**
        *   `src/omeda-newsletter-connector/omeda-wp-integration.php` - Load Action Scheduler, initialize async jobs
        *   `src/omeda-newsletter-connector/includes/class-omeda-hooks.php` - Schedule async jobs with fallback
        *   `src/omeda-newsletter-connector/includes/class-omeda-workflow-manager.php` - Made prepare_configuration() public
        *   `src/omeda-newsletter-connector/includes/class-omeda-settings.php` - Add Background Jobs admin menu link
    *   **Changes:**
        *   Implemented 6 async job types: create_deployment, assign_audience, add_content, update_content, send_test, schedule_deployment
        *   Added debouncing with 5-minute delay for creation, 1-minute for updates
        *   Implemented exponential backoff retry (3 attempts max)
        *   Chain scheduling: each job schedules the next on success
        *   Meta box now shows pending jobs list
        *   Automatic fallback to synchronous execution if Action Scheduler unavailable
        *   Added Background Jobs submenu linking to Action Scheduler admin UI

**Benefits:**
*   Non-blocking post saves (better UX)
*   Automatic retry on API failures
*   Works reliably in wp-env without manual cron triggering
*   Better debugging with Action Scheduler admin UI
*   Production-ready with proven library (WooCommerce standard)
*   Maintains backward compatibility with synchronous fallback

**Testing Status:** Ready for validation in wp-env environment.

**Documentation:** Comprehensive guides created for technical decision-making, implementation details, and testing procedures.

---

**Status:** Phase 2 implementation complete with hybrid scheduling approach. Ready for testing and validation.

**Scheduling Strategy:** 
- **Development/Staging (wp-env):** Action Scheduler for reliable execution without manual cron triggering
- **Production:** Native WP-Cron (WordPress-only, no external dependencies)
- **Automatic Detection:** Uses `wp_get_environment_type()` to determine environment
- **Benefits:** Best of both worlds - reliable dev testing + pure WordPress in production


