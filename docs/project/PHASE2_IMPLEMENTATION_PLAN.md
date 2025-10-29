# Phase 2 Implementation Plan: Asynchronous Processing Framework
**Date:** 2025-10-29  
**Project:** Omeda Newsletter Connector  
**Phase:** 2 - Asynchronous Processing Framework

## Overview

Phase 2 focuses on implementing a robust asynchronous processing framework using Action Scheduler to replace the current synchronous workflow. This will provide non-blocking execution, automatic retries, sequential job processing, and better scalability for production environments.

---

## Task 2.1: Action Scheduler Implementation

### Objective
Integrate the Action Scheduler library to provide reliable background processing for Omeda API calls.

### Why Action Scheduler?

**Action Scheduler** is the industry-standard WordPress background processing library:
- ✅ Used by WooCommerce (battle-tested at scale)
- ✅ Reliable execution independent of site traffic
- ✅ Built-in retry mechanism
- ✅ Job sequencing and dependencies
- ✅ Database-backed queue (not dependent on WP-Cron)
- ✅ Admin UI for monitoring
- ✅ Scalable and performant

**Why NOT WP-Cron:**
- ❌ Depends on site traffic for execution
- ❌ Unreliable in low-traffic environments
- ❌ No built-in retry mechanism
- ❌ Poor for sequential, dependent tasks
- ❌ Can miss scheduled times

### Implementation Steps

#### Step 1: Install Action Scheduler Library

Action Scheduler can be included as:
1. **Composer Package** (recommended for development)
2. **Bundled Library** (copy into plugin directory)
3. **Dependency on another plugin** (WooCommerce)

**Decision:** Bundle the library for independence.

```bash
# Location: src/omeda-newsletter-connector/lib/action-scheduler/
```

#### Step 2: Initialize Action Scheduler

**File:** `omeda-wp-integration.php`

```php
// Load Action Scheduler if not already loaded
if (!function_exists('as_schedule_single_action')) {
    require_once OMEDA_WP_PLUGIN_DIR . 'lib/action-scheduler/action-scheduler.php';
}
```

#### Step 3: Create Async Job Handlers

**New File:** `includes/class-omeda-async-jobs.php`

This class will contain all Action Scheduler job handlers for the deployment workflow.

**Job Types:**
1. `omeda_async_create_deployment` - Initial deployment creation
2. `omeda_async_assign_audience` - Audience assignment
3. `omeda_async_add_content` - Content upload
4. `omeda_async_send_test` - Test email
5. `omeda_async_schedule_deployment` - Final scheduling

---

## Task 2.2: Job Sequencing Logic

### Objective
Implement a robust system for scheduling sequential, dependent jobs.

### Architecture Design

#### Approach 1: Chain Scheduling (Recommended)

Each job schedules the next job upon successful completion:

```
save_post → Schedule: Create Deployment (immediate)
  ↓ (on success)
  Schedule: Assign Audience (+30 sec)
  ↓ (on success)
  Schedule: Add Content (+30 sec)
  ↓ (complete)
  
publish → Schedule: Update Content (immediate)
  ↓ (on success)
  Schedule: Send Test (+30 sec)
  ↓ (on success)
  Schedule: Final Scheduling (+30 sec)
```

**Advantages:**
- Simple and reliable
- Each step validates previous step
- Natural error isolation
- Easy to debug

#### Approach 2: Central Orchestrator (Alternative)

A single orchestrator job manages all steps:

```
save_post → Schedule: Orchestrator
  Orchestrator checks state, executes next step, reschedules self
```

**Decision:** Use **Chain Scheduling** for clarity and reliability.

### Implementation Details

#### Debouncing Strategy

**Problem:** Multiple rapid saves trigger duplicate deployments.

**Solution:** Cancel pending jobs before scheduling new ones.

```php
// Cancel any pending jobs for this post
as_unschedule_all_actions('omeda_async_create_deployment', [
    'post_id' => $post_id
], 'omeda-deployment');

// Schedule new job after debounce delay (5 minutes)
as_schedule_single_action(
    time() + 300, // 5 minutes
    'omeda_async_create_deployment',
    ['post_id' => $post_id],
    'omeda-deployment'
);
```

#### Job Arguments

Each job receives:
```php
[
    'post_id' => 123,
    'config_id' => 456,
    'retry_count' => 0
]
```

#### Error Handling and Retries

Action Scheduler provides automatic retries, but we'll add custom logic:

```php
function handle_create_deployment($post_id, $config_id, $retry_count = 0) {
    try {
        // Execute API call
        $track_id = $this->api_client->step1_create_deployment($config);
        update_post_meta($post_id, '_omeda_track_id', $track_id);
        
        // Schedule next job
        as_schedule_single_action(
            time() + 30,
            'omeda_async_assign_audience',
            ['post_id' => $post_id, 'config_id' => $config_id, 'retry_count' => 0],
            'omeda-deployment'
        );
        
    } catch (Exception $e) {
        // Log error
        $this->workflow_manager->log_error($post_id, $e->getMessage());
        
        // Retry logic
        if ($retry_count < 3) {
            as_schedule_single_action(
                time() + (60 * pow(2, $retry_count)), // Exponential backoff
                'omeda_async_create_deployment',
                ['post_id' => $post_id, 'config_id' => $config_id, 'retry_count' => $retry_count + 1],
                'omeda-deployment'
            );
        } else {
            $this->workflow_manager->log_error($post_id, 'Max retries exceeded for deployment creation.');
        }
    }
}
```

---

## Task 2.3: Update Hooks Integration

### Objective
Modify `class-omeda-hooks.php` to schedule async jobs instead of executing synchronously.

### Changes Required

#### Current Implementation (Synchronous)
```php
public function handle_post_save($post_id) {
    // ... validation ...
    if (empty($track_id)) {
        $this->workflow_manager->create_and_assign_audience($post_id, $config_id);
    } else {
        $this->workflow_manager->update_content($post_id, $track_id, $config_id);
    }
}
```

#### New Implementation (Asynchronous)
```php
public function handle_post_save($post_id) {
    // ... validation ...
    if (empty($track_id)) {
        // Cancel any pending creation jobs (debouncing)
        as_unschedule_all_actions('omeda_async_create_deployment', 
            ['post_id' => $post_id], 'omeda-deployment');
        
        // Schedule new creation job
        as_schedule_single_action(
            time() + 300, // 5 minutes debounce
            'omeda_async_create_deployment',
            ['post_id' => $post_id, 'config_id' => $config_id],
            'omeda-deployment'
        );
        
        $this->workflow_manager->log_status($post_id, 
            'Deployment creation scheduled (will execute in 5 minutes).');
    } else {
        // Cancel any pending update jobs
        as_unschedule_all_actions('omeda_async_update_content', 
            ['post_id' => $post_id], 'omeda-deployment');
        
        // Schedule update job
        as_schedule_single_action(
            time() + 60, // 1 minute debounce
            'omeda_async_update_content',
            ['post_id' => $post_id, 'config_id' => $config_id, 'track_id' => $track_id],
            'omeda-deployment'
        );
        
        $this->workflow_manager->log_status($post_id, 
            'Content update scheduled (will execute in 1 minute).');
    }
}
```

---

## Task 2.4: Admin UI Enhancements

### Objective
Provide visibility into scheduled jobs and their status.

### Features to Add

#### 1. Action Scheduler Admin Menu

Action Scheduler provides its own admin UI. Add a submenu link:

```php
add_submenu_page(
    'omeda-integration',
    'Background Jobs',
    'Background Jobs',
    'manage_options',
    'action-scheduler',
    'action_scheduler_admin_page'
);
```

#### 2. Job Status in Meta Box

Update the meta box to show pending jobs:

```php
$pending_jobs = as_get_scheduled_actions([
    'hook' => 'omeda_async_create_deployment',
    'args' => ['post_id' => $post->ID],
    'status' => ActionScheduler_Store::STATUS_PENDING
], 'ids');

if (!empty($pending_jobs)) {
    echo '<p><strong>Status:</strong> Deployment creation scheduled</p>';
}
```

#### 3. Manual Trigger Button

Add a button to immediately execute pending jobs:

```php
// In meta box
if ($track_id && current_user_can('manage_options')) {
    echo '<button type="button" class="button" id="omeda-sync-now">Sync Now</button>';
}

// AJAX handler to trigger immediate execution
add_action('wp_ajax_omeda_sync_now', 'handle_sync_now');
```

---

## Task 2.5: Testing and Validation

### Test Cases

#### TC1: Initial Draft Save
1. Create new post
2. Select Deployment Type
3. Save as draft
4. Verify job scheduled
5. Wait for execution
6. Check Track ID created
7. Verify log entries

#### TC2: Rapid Multiple Saves (Debouncing)
1. Save post multiple times rapidly
2. Verify only one job remains scheduled
3. Verify previous jobs were cancelled

#### TC3: API Failure and Retry
1. Temporarily break API credentials
2. Trigger job
3. Verify retry scheduled
4. Restore credentials
5. Verify eventual success

#### TC4: Publish Workflow
1. Save draft (creates deployment)
2. Publish post
3. Verify test email sent
4. Verify deployment scheduled

#### TC5: Update Existing Deployment
1. Publish post with deployment
2. Edit content
3. Save
4. Verify content update job scheduled
5. Verify test email sent

---

## Implementation Timeline

| Task | Estimated Time | Priority |
|------|---------------|----------|
| 2.1.1: Bundle Action Scheduler | 30 min | P0 |
| 2.1.2: Initialize in plugin | 15 min | P0 |
| 2.1.3: Create async jobs class | 2 hours | P0 |
| 2.2.1: Implement job handlers | 3 hours | P0 |
| 2.2.2: Error handling & retries | 1 hour | P0 |
| 2.2.3: Debouncing logic | 1 hour | P1 |
| 2.3: Update hooks integration | 1 hour | P0 |
| 2.4: Admin UI enhancements | 1.5 hours | P2 |
| 2.5: Testing | 2 hours | P0 |
| **Total** | **12 hours** | |

---

## Code Structure Changes

### New Files
```
src/omeda-newsletter-connector/
├── lib/
│   └── action-scheduler/         [NEW] - Action Scheduler library
├── includes/
│   ├── class-omeda-async-jobs.php [NEW] - Async job handlers
│   ├── class-omeda-hooks.php      [MODIFIED] - Schedule jobs instead of sync execution
│   └── class-omeda-workflow-manager.php [MODIFIED] - Support async operations
└── omeda-wp-integration.php       [MODIFIED] - Load Action Scheduler
```

---

## Backwards Compatibility

**Strategy:** Maintain both sync and async paths during transition.

**Configuration Option:**
```php
$use_async = get_option('omeda_use_async_processing', true);

if ($use_async && function_exists('as_schedule_single_action')) {
    // Use Action Scheduler
} else {
    // Fall back to synchronous
}
```

This allows:
- Safe rollback if issues arise
- Testing in development before production
- Gradual rollout

---

## Risk Assessment

| Risk | Probability | Impact | Mitigation |
|------|-------------|--------|------------|
| Action Scheduler conflicts | Low | Medium | Bundle specific version, namespace if needed |
| Job execution delays | Medium | Low | Acceptable trade-off for reliability |
| Debugging complexity | Medium | Medium | Enhanced logging, admin UI visibility |
| Retry exhaustion | Low | Medium | Alert users, provide manual sync button |

---

## Success Criteria

Phase 2 will be considered successful when:

1. ✅ Action Scheduler is integrated and functional
2. ✅ All deployment steps execute asynchronously
3. ✅ Debouncing prevents duplicate jobs
4. ✅ Failed jobs retry automatically
5. ✅ Admin UI shows job status
6. ✅ All test cases pass
7. ✅ Performance improves (non-blocking saves)
8. ✅ No regressions in existing functionality

---

## Next Steps

1. **Download and bundle Action Scheduler**
2. **Create `class-omeda-async-jobs.php`**
3. **Implement job handlers with chain scheduling**
4. **Update hooks to schedule jobs**
5. **Add admin UI enhancements**
6. **Comprehensive testing**
7. **Update documentation**

---

**Ready to proceed with implementation.**
