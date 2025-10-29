# Workflow Stalling Issue - Fix Documentation
**Date:** October 29, 2025  
**Version:** 1.6.2  
**Issue:** Workflow stops after "Deployment creation scheduled" message

## Problem Summary

When saving a newsletter draft, the workflow log would show:
```
[INFO] Deployment creation scheduled via Action Scheduler (will execute in 300 seconds).
```

But no further action would occur. The deployment would never be created in Omeda, and subsequent workflow steps would not execute.

## Root Causes

### 1. Environment-Unaware Job Chaining
**Location:** `class-omeda-async-jobs.php` - Job handler methods

**Issue:** Job handlers used hard-coded `as_schedule_single_action()` calls to schedule the next job in the workflow chain. This only worked with Action Scheduler.

**Code Before:**
```php
public function handle_create_deployment($post_id, $config_id, $retry_count = 0) {
    // ... create deployment ...
    
    // Hard-coded Action Scheduler call
    as_schedule_single_action(
        time() + 30,
        self::HOOK_ASSIGN_AUDIENCE,
        array('post_id' => $post_id, 'track_id' => $track_id, ...),
        self::GROUP_NAME
    );
}
```

**Problem:** In production environments or when Action Scheduler wasn't running, the next job would never be scheduled, causing the workflow to stall.

**Code After:**
```php
public function handle_create_deployment($post_id, $config_id, $retry_count = 0) {
    // ... create deployment ...
    
    // Environment-aware scheduling
    $method = $this->get_scheduling_method();
    if ($method === 'action_scheduler') {
        as_schedule_single_action(...);
    } elseif ($method === 'wp_cron') {
        wp_schedule_single_event(...);
    }
}
```

**Solution:** Check environment configuration and use appropriate scheduling method (Action Scheduler for dev, WP-Cron for production).

### 2. Missing Configuration Validation
**Location:** `class-omeda-async-jobs.php` - `handle_create_deployment()`

**Issue:** The job would attempt to create a deployment in Omeda even if required configuration was missing, resulting in a PHP warning that caused silent failure.

**Error Message:**
```
PHP Warning: Undefined array key "DeploymentTypeId" in class-omeda-api-client.php on line 177
```

**Code Before:**
```php
public function handle_create_deployment($post_id, $config_id, $retry_count = 0) {
    $config = $this->workflow_manager->prepare_configuration(...);
    if (!$config) {
        throw new Exception('Failed to prepare configuration.');
    }
    
    // No validation of DeploymentTypeId
    $api_client->step1_create_deployment($config); // Would fail here
}
```

**Code After:**
```php
public function handle_create_deployment($post_id, $config_id, $retry_count = 0) {
    $config = $this->workflow_manager->prepare_configuration(...);
    if (!$config) {
        throw new Exception('Failed to prepare configuration.');
    }
    
    // Validate required fields
    if (empty($config['DeploymentTypeId'])) {
        throw new Exception('DeploymentTypeId is required but not configured.');
    }
    
    $api_client->step1_create_deployment($config);
}
```

**Solution:** Added validation to check for required configuration before attempting API calls, providing clear error messages.

## Additional Improvements

### 3. Timestamp Display in Logs
**Location:** `class-omeda-hooks.php` - `render_meta_box()`

**Issue:** Timestamps were only visible on hover (in `title` attribute), making it difficult to see when each log entry was created.

**Code Before:**
```php
printf(
    '<div style="color:%s; margin-bottom: 3px;" title="%s">[%s] %s</div>',
    $color,
    esc_attr($log_data['timestamp']),  // Hidden unless hovered
    esc_html($log_data['level']),
    esc_html($log_data['message'])
);
```

**Code After:**
```php
printf(
    '<div style="color:%s; margin-bottom: 3px;">[%s] [%s] %s</div>',
    $color,
    esc_html($log_data['timestamp']),  // Always visible
    esc_html($log_data['level']),
    esc_html($log_data['message'])
);
```

**Result:** Timestamps now appear inline: `[2025-10-29 16:42:28] [INFO] message`

### 4. Deployment Date Calculation
**Location:** `class-omeda-async-jobs.php` - `handle_create_deployment()`

**Issue:** Used far-future placeholder date (`2099-01-01 12:00`) which was not realistic for testing.

**Code Before:**
```php
$config = $this->workflow_manager->prepare_configuration($post_id, $config_id, '2099-01-01 12:00');
```

**Code After:**
```php
// Calculate next nearest hour
$next_hour_timestamp = ceil(time() / 3600) * 3600;
$next_hour_date = gmdate('Y-m-d H:i', $next_hour_timestamp);
$config = $this->workflow_manager->prepare_configuration($post_id, $config_id, $next_hour_date);
```

**Example:**
- Current time: 2:30 PM → Next hour: 3:00 PM
- Current time: 5:15 PM → Next hour: 6:00 PM

## Testing the Fix

### 1. Create a Test Newsletter
1. Go to Newsletter Glue (or Posts)
2. Create a new newsletter/post
3. Assign a Deployment Type in the Omeda meta box
4. Save as draft

### 2. Verify Workflow Execution
Check the workflow log in the Omeda meta box. You should see:

```
[2025-10-29 16:42:28] [INFO] Workflow Initiated: Post saved as draft with a Deployment Type.
[2025-10-29 16:42:28] [INFO] Deployment creation scheduled via Action Scheduler (will execute in 300 seconds).
```

Wait 5 minutes (or trigger cron manually):
```bash
wp-env run cli wp cron event run --due-now
```

Then refresh the post editor. You should see:

```
[2025-10-29 16:47:30] [INFO] Executing: Create deployment job...
[2025-10-29 16:47:32] [INFO] Step 1/5 Complete: Deployment created with TrackID: ABC123
[2025-10-29 16:47:35] [INFO] Executing: Assign audience job...
[2025-10-29 16:47:36] [INFO] Step 2/5 Complete: Audience assigned.
[2025-10-29 16:47:40] [INFO] Executing: Add content job...
[2025-10-29 16:47:41] [INFO] Step 3/5 Complete: Initial content added. Deployment ready for draft editing.
```

### 3. Trigger Cron Manually (Development)
```bash
# Run all due cron events
wp-env run cli wp cron event run --due-now

# Check Action Scheduler queue
wp-env run cli wp action-scheduler list
```

### 4. Verify in Omeda
1. Log into Omeda platform
2. Navigate to Deployments
3. Find deployment with name matching your post title
4. Verify:
   - Deployment exists
   - Audience is assigned
   - Content/subject are populated
   - Deployment date is set to next nearest hour

## Configuration Requirements

### Required Settings
For workflow to execute successfully, the following must be configured:

#### 1. Global Settings (Omeda Integration → Settings)
- ✅ API credentials (App ID, Input ID, Brand Abbreviation)
- ✅ Environment (Staging/Production)
- ✅ Default User ID (optional, for deployment owner)

#### 2. Deployment Type Configuration
- ✅ Omeda Deployment Type ID (selected from dropdown)
- ✅ Assigned Post Type (what triggers the workflow)
- ✅ Audience Query ID (Omeda Audience Builder query name)
- ✅ From Name
- ✅ From Email
- ✅ Reply To Email
- ✅ Subject Format (can include WordPress variables)

### Troubleshooting

#### Error: "DeploymentTypeId is required but not configured"
**Solution:** Edit your deployment type and ensure "Omeda Deployment Type" dropdown has a selection. Click "Refresh Deployment Types" if list is empty.

#### Error: API connection failed
**Solution:** Check API credentials in Omeda Integration → Settings. Ensure App ID and Input ID are correct.

#### Workflow stops after scheduling
**Solution:** Trigger cron manually to test:
```bash
wp-env run cli wp cron event run --due-now
```

If using Action Scheduler, check:
```bash
wp-env run cli wp action-scheduler list
```

#### Jobs never execute
**Solution:** 
1. Check if Action Scheduler plugin is active (dev environment)
2. Verify WP-Cron is not disabled (`DISABLE_WP_CRON` constant)
3. Test cron execution manually as shown above

## Files Changed

### Modified Files
1. `src/omeda-newsletter-connector/includes/class-omeda-async-jobs.php`
   - Added validation for `DeploymentTypeId`
   - Updated deployment date calculation
   - Made job scheduling environment-aware in all handlers

2. `src/omeda-newsletter-connector/includes/class-omeda-hooks.php`
   - Updated log display to show timestamps inline

3. `src/omeda-newsletter-connector/omeda-wp-integration.php`
   - Version bump: 1.6.1 → 1.6.2

4. `CHANGELOG.md`
   - Added v1.6.2 release notes

## Version History

- **v1.6.1** - Added Newsletter Glue post type support
- **v1.6.2** - Fixed workflow stalling issue (current version)

## Support

For additional issues or questions:
- Check `CHANGELOG.md` for full version history
- Review `docs/project/` directory for project documentation
- Check Action Scheduler admin page: Tools → Action Scheduler
