# Omeda Newsletter Connector - Workflow Update Summary

**Date:** October 29, 2025  
**Version:** 1.7.0  
**Type:** Minor Release - New Features

## Overview

Updated the Omeda Newsletter Connector workflow to execute deployments immediately upon saving, eliminating artificial delays. Added manual UI controls for sending test emails and scheduling deployments, giving users full control over the deployment lifecycle.

## Changes Implemented

### 1. Immediate Workflow Execution

**Modified Files:**
- `src/omeda-newsletter-connector/includes/class-omeda-hooks.php`
- `src/omeda-newsletter-connector/includes/class-omeda-async-jobs.php`

**Changes:**
- Removed 5-minute debounce delay from deployment creation (was 300s, now 0s)
- Removed 1-minute debounce delay from content updates (was 60s, now 0s)  
- Updated job handlers to schedule next job immediately (`time()` instead of `time() + 30`)
- Jobs now execute in rapid succession: Create → Assign Audience → Add Content

**Impact:**
- Deployments are created and ready within seconds instead of minutes
- Content is synchronized to Omeda immediately after each save
- Reduced time-to-ready significantly improves user experience

### 2. Manual Deployment Control UI

**New Files:**
- `src/omeda-newsletter-connector/assets/js/omeda-admin.js`

**Modified Files:**
- `src/omeda-newsletter-connector/includes/class-omeda-hooks.php` (render_meta_box method)

**Features Added:**

#### Send Test Email Button
- Becomes available once deployment is fully created
- Shows last test sent timestamp
- Updates content before sending test
- Success/failure feedback via alert dialogs

#### Schedule Deployment Section
- HTML5 datetime-local picker for selecting deployment date/time
- Automatic conversion from local time to UTC for API
- Confirmation checkbox required before scheduling
- Button disabled until checkbox is checked
- Clear confirmation dialog showing UTC time before final schedule

#### Unschedule Button
- Available for scheduled deployments
- Shows current scheduled date/time
- Confirmation dialog before unscheduling
- Removes scheduled status from post meta

**Visual Enhancements:**
- Color-coded status indicators (green for success, yellow for warnings)
- Dashicons for visual clarity
- Loading animations during AJAX requests
- Disabled state management for buttons during processing

### 3. AJAX Handlers for Manual Actions

**Modified Files:**
- `src/omeda-newsletter-connector/includes/class-omeda-hooks.php`
- `src/omeda-newsletter-connector/includes/class-omeda-async-jobs.php`

**New AJAX Endpoints:**
1. `omeda_send_test`: Triggers test email send
2. `omeda_schedule_deployment`: Schedules deployment with custom date
3. `omeda_unschedule_deployment`: Cancels scheduled deployment

**Security:**
- Nonce verification on all endpoints
- Capability check (`current_user_can('edit_post', $post_id)`)
- Input sanitization for all parameters
- JSON response format with success/error messaging

**New Public Methods in Omeda_Async_Jobs:**
```php
send_test_email($post_id, $track_id, $config_id)
schedule_deployment($post_id, $track_id, $config_id, $schedule_date)
unschedule_deployment($post_id, $track_id)
```

### 4. State Tracking Meta Fields

**New Post Meta Keys:**
- `_omeda_deployment_ready`: Boolean flag when deployment creation completes
- `_omeda_test_sent`: Timestamp of last test email sent
- `_omeda_deployment_scheduled`: Timestamp when deployment was scheduled
- `_omeda_schedule_date`: The deployment send date/time in UTC format

**Purpose:**
- Enable conditional UI rendering based on workflow state
- Track deployment lifecycle for auditing
- Allow UI to show historical information (last test sent, etc.)

### 5. Workflow Step Count Update

**Changed:**
- Old: Steps 1/5, 2/5, 3/5, 4/5, 5/5
- New: Steps 1/3, 2/3, 3/3

**Rationale:**
- Test and Schedule are now manual actions, not automatic workflow steps
- More accurate representation of automated workflow
- Clearer to users what happens automatically vs. manually

### 6. JavaScript Implementation

**File:** `assets/js/omeda-admin.js`

**Key Features:**
- jQuery-based event handlers for all UI interactions
- AJAX request management with loading states
- Automatic UTC conversion for datetime inputs
- Error handling and user feedback
- Dynamic button state management
- CSS animation for loading spinners

**Enqueuing:**
- Only loaded on post edit screens (`post.php`, `post-new.php`)
- Only for supported post types (`post`, `newsletterglue`)
- Version 1.0.1 to ensure cache busting
- Localized script data for AJAX URL, post ID, and nonce

### 7. Version Update

**Files Updated:**
- `src/omeda-newsletter-connector/omeda-wp-integration.php`
  - Plugin header version: 1.6.2 → 1.7.0
  - `OMEDA_WP_VERSION` constant: 1.6.2 → 1.7.0
- `CHANGELOG.md`
  - Added comprehensive v1.7.0 entry with all changes documented

## Testing Instructions

### 1. Create a New Deployment

1. Create or edit a newsletter post
2. Select a deployment type in the Omeda Deployment meta box
3. Save as draft
4. Workflow should execute immediately (watch workflow log)
5. Within seconds, you should see "Step 3/3 Complete" message
6. "Send Test Email" button should appear

### 2. Send Test Email

1. Click "Send Test Email" button
2. Button should show loading state
3. Alert should confirm success
4. Page reloads showing "Last test sent" timestamp
5. Check test email inbox (configured in Omeda settings)

### 3. Schedule Deployment

1. Select a date/time in the datetime picker
2. Check the confirmation checkbox
3. Click "Schedule Deployment" button
4. Confirm the UTC time in the dialog
5. Alert should confirm success
6. UI should update to show scheduled state with date

### 4. Unschedule Deployment

1. With a scheduled deployment, click "Unschedule" button
2. Confirm in the dialog
3. Alert should confirm success
4. UI should revert to showing schedule section again

### 5. Content Updates

1. Make changes to newsletter content
2. Save the post
3. Workflow log should show "Content updated"
4. Changes should be reflected in Omeda immediately

## Technical Notes

### Environment Detection

The system continues to use environment-based scheduling:
- **Development/Staging:** Action Scheduler (if available)
- **Production:** Native WP-Cron

Detection is via:
```php
$is_production = (defined('WP_ENV') && WP_ENV === 'production') || 
                 wp_get_environment_type() === 'production';
```

### API Call Sequence

1. **On Draft Save (First Time):**
   - Create Deployment → Assign Audience → Add Content → Mark Ready

2. **On Content Update:**
   - Update Content in Omeda

3. **On Send Test (Manual):**
   - Update Content → Send Test → Log Timestamp

4. **On Schedule (Manual):**
   - Update Content → Schedule Deployment → Log Status

### Error Handling

- All AJAX requests have try-catch blocks
- Errors logged to workflow log with ERROR level
- User-friendly error messages via alert dialogs
- Retry logic remains in async job handlers (3 retries with exponential backoff)

### UI State Management

The UI intelligently shows/hides elements based on:
- Existence of `_omeda_track_id` (deployment created?)
- Value of `_omeda_deployment_ready` (workflow complete?)
- Value of `_omeda_test_sent` (test sent?)
- Value of `_omeda_deployment_scheduled` (scheduled?)

## Migration Impact

- **Existing Deployments:** No impact - continue from current state
- **Backwards Compatibility:** Fully maintained
- **Database Changes:** New meta keys added, no schema changes
- **API Changes:** No changes to external API calls
- **User Interface:** Enhanced, not replaced - all existing functionality remains

## Known Limitations

1. **Unschedule API:** The `unschedule_deployment()` method needs the actual Omeda API implementation
2. **Test Email Config:** Requires Omeda tester list to be configured in deployment type
3. **Datetime Picker:** Uses HTML5 input which may have browser-specific rendering
4. **UTC Display:** Schedule button shows UTC time - users need to be aware of timezone

## Future Enhancements

Possible improvements for future versions:
- Visual timezone indicator next to datetime picker
- Preview deployment before scheduling
- Bulk operations for multiple deployments
- Scheduled deployment queue view
- Email preview modal
- Delivery analytics dashboard

## Files Modified Summary

1. **class-omeda-hooks.php** - UI, AJAX handlers, script enqueuing
2. **class-omeda-async-jobs.php** - Manual action methods, immediate execution
3. **omeda-wp-integration.php** - Version bump
4. **CHANGELOG.md** - Documentation
5. **assets/js/omeda-admin.js** - NEW - JavaScript UI handlers

## Documentation

All changes have been documented in:
- CHANGELOG.md (user-facing changes)
- This summary (technical implementation details)
- Inline code comments (developer context)

Version 1.7.0 is now ready for testing and deployment.
