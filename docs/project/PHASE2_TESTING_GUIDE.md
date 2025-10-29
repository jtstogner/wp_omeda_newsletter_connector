# Phase 2 Implementation Summary & Testing Guide
**Date:** 2025-10-29  
**Project:** Omeda Newsletter Connector  
**Environment:** wp-env (port 8888)  
**Credentials:** admin / password

## What Was Implemented

### ✅ Phase 2 Complete: Asynchronous Processing Framework

1. **Action Scheduler Integration**
   - Downloaded and bundled Action Scheduler 3.7.1
   - Location: `src/omeda-newsletter-connector/lib/action-scheduler/`
   - Loaded in main plugin file with conditional check

2. **Async Job Handlers** (`class-omeda-async-jobs.php`)
   - 6 job types covering full deployment workflow:
     - `omeda_async_create_deployment` - Initial creation
     - `omeda_async_assign_audience` - Audience assignment
     - `omeda_async_add_content` - Initial content
     - `omeda_async_update_content` - Content updates
     - `omeda_async_send_test` - Test emails
     - `omeda_async_schedule_deployment` - Final scheduling
   - Automatic retry with exponential backoff (3 attempts)
   - Debouncing to prevent duplicate jobs
   - Chain scheduling for sequential execution

3. **Hooks Integration Updates**
   - Modified `class-omeda-hooks.php` to schedule async jobs
   - Maintains synchronous fallback if Action Scheduler unavailable
   - Automatic detection of Action Scheduler availability

4. **Admin UI Enhancements**
   - Pending jobs display in post meta box
   - Link to Action Scheduler admin UI
   - Real-time job status visibility

5. **Workflow Manager Updates**
   - Made `prepare_configuration()` public for async access
   - Maintains all existing synchronous methods

---

## Architecture Overview

### Job Flow: Draft Save → Deployment Creation

```
User saves draft with Deployment Type selected
        ↓
handle_post_save() detects new deployment needed
        ↓
schedule_create_deployment() called (5 min debounce)
        ↓
[5 minutes later]
        ↓
handle_create_deployment() executes
    - Calls Omeda API: Create Deployment
    - Stores Track ID in post meta
    - Logs: "Step 1/5 Complete"
    - Schedules: assign_audience (+30s)
        ↓
handle_assign_audience() executes
    - Calls Omeda API: Assign Audience
    - Logs: "Step 2/5 Complete"
    - Schedules: add_content (+30s)
        ↓
handle_add_content() executes
    - Calls Omeda API: Add Content
    - Logs: "Step 3/5 Complete: Ready for draft editing"
```

### Job Flow: Publish → Final Deployment

```
User publishes post
        ↓
handle_status_transition() detects publish
        ↓
schedule_finalize_deployment() called
        ↓
Three jobs scheduled simultaneously:
    1. update_content (immediate)
    2. send_test (+30s)
    3. schedule_deployment (+60s)
        ↓
Jobs execute in sequence
        ↓
"✓ Workflow Complete"
```

---

## Testing in wp-env

### Prerequisites

```bash
# Ensure wp-env is installed
npm install -g @wordpress/env

# Start the environment
cd /home/jts/development/NRS/Projects/wp_omeda_newsletter_connector
npx wp-env start
```

### Access Points

- **WordPress Admin:** http://localhost:8888/wp-admin
- **Username:** admin
- **Password:** password
- **Site URL:** http://localhost:8888

### Step-by-Step Testing

#### Test 1: Verify Action Scheduler Installation

```bash
# Check if Action Scheduler is loaded
npx wp-env run cli wp plugin list

# Should show omeda-wp-integration active
```

Expected: Plugin active, no errors in debug log.

#### Test 2: Create Deployment Type

1. Log in to WordPress admin
2. Navigate to **Omeda Integration** → **Deployment Types**
3. Click **Add New**
4. Fill in:
   - Title: "Test Newsletter"
   - Deployment Type ID: (from Omeda)
   - Audience Query ID: (from Omeda)
   - Tester Email Addresses: your@email.com
5. Publish

#### Test 3: Test Async Deployment Creation

1. Navigate to **Posts** → **Add New**
2. Create a test post with content
3. In the **Omeda Deployment** meta box:
   - Select "Test Newsletter"
4. Save as **Draft**
5. Observe meta box:
   - Should show: "Deployment creation scheduled (will execute in 300 seconds)"
   - Should show: "Pending Jobs" list

#### Test 4: Monitor Job Execution

```bash
# Watch Action Scheduler queue
npx wp-env run cli wp action-scheduler list --hook=omeda_async_create_deployment

# Force immediate execution (for testing)
npx wp-env run cli wp action-scheduler run --hooks=omeda_async_create_deployment

# Check logs
npx wp-env run cli wp post meta list [POST_ID] --keys=_omeda_workflow_log
```

#### Test 5: View in Admin UI

1. Navigate to **Omeda Integration** → **Background Jobs**
2. View Action Scheduler admin interface
3. Filter by status: Pending, Complete, Failed
4. Inspect individual jobs

#### Test 6: Test Debouncing

1. Open test post in editor
2. Save draft multiple times rapidly (5 saves in 10 seconds)
3. Check **Pending Jobs** in meta box
4. Should show only ONE pending job (old ones cancelled)

#### Test 7: Test Publish Workflow

1. With deployment created (Track ID exists)
2. Click **Publish**
3. Observe:
   - "Final deployment sequence scheduled"
   - Three jobs in pending list
4. Wait for execution
5. Check log for "✓ Workflow Complete"

#### Test 8: Test Retry Logic

1. Temporarily break API credentials:
   - Go to **Omeda Integration** → **Settings**
   - Change App ID to invalid value
2. Trigger deployment
3. Observe:
   - Job fails
   - Retry scheduled with exponential backoff
   - Check Action Scheduler for retry attempts
4. Restore credentials
5. Job should succeed on retry

---

## Manual Testing Commands

### View All Scheduled Jobs
```bash
npx wp-env run cli wp action-scheduler list --format=table
```

### View Jobs for Specific Post
```bash
npx wp-env run cli wp action-scheduler list --hook=omeda_async_create_deployment --format=table
```

### Run All Due Jobs Immediately
```bash
npx wp-env run cli wp action-scheduler run
```

### Clear All Pending Jobs (Reset)
```bash
npx wp-env run cli wp action-scheduler delete --status=pending --hook=omeda_async_create_deployment
```

### View Post Meta (Logs)
```bash
npx wp-env run cli wp post meta get [POST_ID] _omeda_workflow_log --format=json
```

### Check Plugin Status
```bash
npx wp-env run cli wp plugin status omeda-newsletter-connector
```

---

## Debugging

### Enable Verbose Logging

1. Already enabled in `.wp-env.json`:
```json
{
    "WP_DEBUG": true,
    "WP_DEBUG_LOG": true
}
```

2. View logs:
```bash
npx wp-env run cli wp shell
tail -f /var/www/html/wp-content/debug.log
```

### Common Issues

#### Issue: Jobs Not Executing

**Cause:** WP-Cron not triggering in wp-env

**Solution 1: Trigger Manually**
```bash
curl http://localhost:8888/wp-cron.php?doing_wp_cron
```

**Solution 2: Use WP-CLI**
```bash
npx wp-env run cli wp cron event run --due-now
```

**Solution 3: Set up Auto-Trigger**
```bash
# Add to host crontab
*/1 * * * * curl -s http://localhost:8888/wp-cron.php?doing_wp_cron > /dev/null 2>&1
```

#### Issue: Action Scheduler Not Found

**Check:**
```bash
# Verify lib directory exists
ls -la src/omeda-newsletter-connector/lib/action-scheduler/

# Verify it's being loaded
npx wp-env run cli wp eval 'echo function_exists("as_schedule_single_action") ? "YES" : "NO";'
```

#### Issue: Jobs Stuck in Pending

**Check:**
1. WP-Cron is running: `npx wp-env run cli wp cron event list`
2. No PHP errors: Check debug.log
3. Action Scheduler status: Check admin UI

**Fix:**
```bash
# Force run all due jobs
npx wp-env run cli wp action-scheduler run
```

---

## Performance Metrics

### Expected Timings

| Operation | Debounce | Execution | Total |
|-----------|----------|-----------|-------|
| Initial Save → Create | 5 min | ~3 sec | 5 min 3 sec |
| Create → Audience | 30 sec | ~2 sec | 32 sec |
| Audience → Content | 30 sec | ~5 sec | 35 sec |
| **Total Draft→Ready** | - | - | **~6.5 minutes** |
| Publish → Finalize | 1 min | ~10 sec | 1 min 10 sec |

### Debounce Values (Configurable)

- Draft save: 300 seconds (5 minutes)
- Content update: 60 seconds (1 minute)
- Job chain delay: 30 seconds between steps

---

## Configuration Options

### Change Debounce Times

Edit `class-omeda-async-jobs.php`:

```php
// Reduce debounce for faster testing
public function schedule_create_deployment($post_id, $config_id, $debounce_seconds = 30) {
    // Changed from 300 to 30 seconds
}
```

### Disable Async (Use Synchronous)

The code automatically falls back if Action Scheduler unavailable. To force synchronous:

```bash
# Temporarily disable Action Scheduler
npx wp-env run cli wp shell
<?php update_option('active_plugins', array_diff(get_option('active_plugins'), ['action-scheduler/action-scheduler.php'])); ?>
```

Or delete the lib directory:
```bash
rm -rf src/omeda-newsletter-connector/lib/action-scheduler
```

---

## Validation Checklist

Before considering Phase 2 complete, verify:

- [ ] Action Scheduler loads without errors
- [ ] Jobs are scheduled on post save
- [ ] Jobs execute and call Omeda API
- [ ] Logs are recorded correctly
- [ ] Track ID is stored in post meta
- [ ] Debouncing works (multiple saves = one job)
- [ ] Retry logic works on failure
- [ ] Admin UI shows pending jobs
- [ ] Chain scheduling works (create → assign → content)
- [ ] Publish triggers finalization
- [ ] Synchronous fallback works if AS unavailable

---

## Next Steps

### After Phase 2 Validation:

1. **Phase 3: Newsletter Glue Integration**
   - Investigate Newsletter Glue structure
   - Query templates/story types
   - Content transformation (Olytics)

2. **Phase 4: Enhanced Features**
   - Manual "Sync Now" button
   - Status polling
   - Webhooks (if Omeda supports)

3. **Phase 5: Production Hardening**
   - Rate limiting
   - Batch operations
   - Performance optimization

---

## Questions for You

1. **Do you want to keep Action Scheduler or switch to WP-Cron?**
   - Keep AS: Better for wp-env, more reliable
   - Switch to WP-Cron: Simpler, but needs external cron setup

2. **Should I create automated test scripts?**
   - PHPUnit tests for job handlers
   - Integration tests for full workflow

3. **Want me to validate the implementation now?**
   - I can start wp-env and run through all tests
   - Verify everything works end-to-end

Let me know how you'd like to proceed!
