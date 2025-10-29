# Action Scheduler Queue Execution Issues

## Problem Description

When using Action Scheduler in development environments (like wp-env), jobs scheduled with immediate execution (`time() + 0`) were not executing immediately. The jobs were being added to the Action Scheduler queue but weren't being processed until the next automatic WP-Cron trigger, which might not happen in development environments with low traffic.

### Symptoms
- Workflow logs showed "Deployment creation scheduled via Action Scheduler (executing immediately)"
- However, no further action occurred (deployment wasn't created in Omeda)
- Jobs appeared in the Action Scheduler queue as "pending"
- Deployments remained in "None" state with "Not created yet" status

## Root Cause

Action Scheduler relies on WordPress's cron system to process its queue. In wp-env and other development environments:

1. WP-Cron doesn't run automatically on every page load like in production
2. Action Scheduler's queue runner waits for WP-Cron to trigger
3. Scheduled jobs with immediate execution (`time() + 0`) sit idle until queue runner executes
4. Without traffic or cron triggers, jobs never execute

## Solution Implemented (v1.7.1)

### For Action Scheduler (Development/Staging)

Added manual queue runner triggering when scheduling jobs with 0 delay:

```php
private function schedule_with_action_scheduler($hook, $args, $delay_seconds) {
    // Cancel any existing pending jobs (debouncing)
    $this->cancel_action_scheduler_jobs($hook, array('post_id' => $args['post_id']));

    // Schedule new job
    as_schedule_single_action(
        time() + $delay_seconds,
        $hook,
        $args,
        self::GROUP_NAME
    );

    // If immediate execution, manually trigger the queue runner
    if ($delay_seconds == 0) {
        $this->trigger_action_scheduler_queue();
    }
}

private function trigger_action_scheduler_queue() {
    if (function_exists('ActionScheduler_QueueRunner') && class_exists('ActionScheduler_QueueRunner')) {
        // Get the queue runner instance
        $runner = ActionScheduler_QueueRunner::instance();
        // Process one batch of actions
        $runner->run();
    }
}
```

### For WP-Cron (Production)

Added `spawn_cron()` calls to trigger immediate execution:

```php
wp_schedule_single_event(
    time(),
    self::HOOK_ASSIGN_AUDIENCE,
    array('post_id' => $post_id, 'track_id' => $track_id, 'config_id' => $config_id, 'retry_count' => 0)
);
spawn_cron(); // Trigger cron immediately
```

## Testing the Fix

1. Save a newsletter draft with a deployment type assigned
2. Check the workflow log - you should immediately see:
   - "Deployment creation scheduled..."
   - "Executing: Create deployment job..."
   - "Step 1/3 Complete: Deployment created with TrackID: XXX"
   - "Executing: Assign audience job..."
   - And so on...

3. Verify the deployment exists in Omeda within seconds

## Alternative Solutions Considered

### 1. Use Synchronous Execution
**Pros**: Guaranteed immediate execution
**Cons**: Blocks the save operation, poor UX for long API calls, no retry capability

### 2. Use AJAX Triggers
**Pros**: Controlled execution timing
**Cons**: Requires JavaScript, adds complexity, user must keep page open

### 3. Increase Polling Frequency
**Pros**: Simpler implementation
**Cons**: Wastes resources, still has delays, doesn't solve root cause

### 4. Manual Queue Trigger (Chosen)
**Pros**: Immediate execution, maintains async benefits, clean implementation
**Cons**: Requires understanding Action Scheduler internals

## Environment-Specific Behavior

| Environment | Method | Trigger Mechanism |
|-------------|--------|-------------------|
| Development (wp-env) | Action Scheduler | Manual queue runner trigger |
| Staging | Action Scheduler | Manual queue runner trigger |
| Production | WP-Cron | spawn_cron() for immediate execution |

The plugin automatically detects the environment using WordPress's `wp_get_environment_type()` and chooses the appropriate method.

## Future Considerations

- Monitor Action Scheduler queue health in production
- Consider adding queue runner health checks
- May want to add admin notice if queue is backed up
- Consider adding WP-CLI command to manually process queue

## Related Files

- `src/omeda-newsletter-connector/includes/class-omeda-async-jobs.php` - Main implementation
- `src/omeda-newsletter-connector/includes/class-omeda-hooks.php` - Hook registration
- `CHANGELOG.md` - Version 1.7.1 changes

## References

- [Action Scheduler Documentation](https://actionscheduler.org/)
- [WordPress Cron System](https://developer.wordpress.org/plugins/cron/)
- [wp-env Documentation](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/)
