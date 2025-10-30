# Production WP-Cron Implementation Plan

**Date:** 2025-10-29  
**Decision:** Use Action Scheduler for development (wp-env), pure WP-Cron for production

## Rationale

✅ **Makes Perfect Sense:**
- Production has sufficient traffic during operating hours
- WP-Cron will trigger reliably with consistent traffic
- Eliminates external dependencies in production
- Maintains WordPress-native standards
- Action Scheduler only needed for low-traffic dev/test environments

## Implementation Strategy

### Environment-Based Selection

```php
// Detect environment
$is_production = (defined('WP_ENV') && WP_ENV === 'production') || 
                 wp_get_environment_type() === 'production';

if ($is_production) {
    // Use native WP-Cron (production)
    $this->schedule_with_wp_cron($post_id, $config_id);
} else {
    // Use Action Scheduler (development/staging)
    if (function_exists('as_schedule_single_action')) {
        $this->schedule_with_action_scheduler($post_id, $config_id);
    } else {
        // Synchronous fallback
        $this->execute_synchronously($post_id, $config_id);
    }
}
```

## Changes Required

### 1. Add WP-Cron Implementation to Async Jobs Class

**New Methods:**
- `schedule_with_wp_cron()` - Schedule using wp_schedule_single_event()
- `unschedule_wp_cron_jobs()` - Cancel pending jobs for debouncing
- Maintain all existing Action Scheduler methods

### 2. Update Configuration

**Add to Settings Page:**
- Environment detection display
- Manual override option (optional)

### 3. Job Registration

**Register hooks for BOTH systems:**
```php
// These hooks work with both WP-Cron and Action Scheduler
add_action('omeda_async_create_deployment', [...]);
add_action('omeda_async_assign_audience', [...]);
// etc.
```

## Benefits of This Approach

### Development (wp-env)
✅ Action Scheduler ensures reliable execution  
✅ Admin UI for debugging  
✅ No manual cron triggering needed

### Production
✅ Pure WordPress native (WP-Cron)  
✅ No external dependencies  
✅ Works reliably with traffic  
✅ Simpler deployment

## Implementation Time

**Estimated:** 1-2 hours to add WP-Cron path alongside existing Action Scheduler code.

---

**Ready to implement this hybrid approach?**
