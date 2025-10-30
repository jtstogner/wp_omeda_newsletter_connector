# Enhanced Logging System Documentation

**Version:** 1.10.0  
**Date:** 2025-10-29

## Overview

The Omeda WordPress Integration plugin now features a comprehensive three-level logging system designed to provide visibility into all workflow operations, from high-level status updates to detailed API request/response data.

## Logging Levels

### 1. Basic (INFO Level)
**Purpose:** High-level workflow status for end users  
**Audience:** Site administrators and content editors  
**Use Case:** Day-to-day monitoring and basic troubleshooting

**What's Logged:**
- Workflow step completion messages
- Status updates ("Step 1/3 Complete: Deployment created")
- Retry attempts ("Retry 2/3 scheduled for add_content")
- Success/failure notifications
- User-facing information

**Example Log Entries:**
```
[2025-10-29 15:54:57] [INFO] [create_deployment] Creating deployment (synchronous execution)...
[2025-10-29 15:54:57] [INFO] [create_deployment] Step 1/3 Complete: Deployment created with TrackID: MTGMCD251029007
[2025-10-29 15:54:58] [INFO] [assign_audience] Step 2/3 Complete: Audience assigned.
```

### 2. Advanced (ADVANCED Level)
**Purpose:** Detailed transaction tracing and debugging  
**Audience:** Developers and technical support  
**Use Case:** Troubleshooting issues, understanding workflow behavior

**What's Logged:**
- Transaction start/complete boundaries
- Step-by-step execution trace
- Configuration validation details
- Detailed error context
- Performance timing information

**Example Log Entries:**
```
[2025-10-29 15:54:57] [ADVANCED] [create_deployment] Starting deployment creation transaction...
[2025-10-29 15:54:57] [ADVANCED] [create_deployment] Deployment creation transaction completed successfully.
[2025-10-29 15:54:58] [ADVANCED] [assign_audience] Starting audience assignment transaction...
[2025-10-29 15:54:58] [ADVANCED] [assign_audience] Audience assignment transaction completed successfully.
```

### 3. Raw (RAW Level)
**Purpose:** Complete API request/response data capture  
**Audience:** Advanced developers and Omeda support  
**Use Case:** Deep debugging, API integration issues, support tickets

**What's Logged:**
- Full API request payloads
- Complete API response data
- Request/response headers (when applicable)
- Configuration arrays
- Sensitive data (should be enabled only when needed)

**Example Log Entries:**
```
[2025-10-29 15:54:57] [RAW] [create_deployment] Creating deployment with configuration:
{
  "DeploymentTypeId": 2344,
  "Subject": "Test Newsletter - Oct 29, 2025",
  "FromName": "Example Site",
  "FromEmail": "noreply@example.com",
  ...
}

[2025-10-29 15:54:58] [RAW] [assign_audience] Assigning audience:
{
  "track_id": "MTGMCD251029007",
  "EncryptedCustomerKey": "ABC123..."
}
```

## Step Names

Each workflow operation has a unique step name that helps identify where in the process logging occurred:

- `create_deployment` - Initial deployment creation in Omeda
- `assign_audience` - Audience query assignment to deployment
- `add_content` - Email content and subject upload
- `update_content` - Content updates after initial creation
- `send_test` - Test email sending
- `schedule_deployment` - Final deployment scheduling

## Retry Tracking

When a step fails and retries are scheduled, the retry count is included in log messages:

```
[2025-10-29 16:09:01] [ERROR] [add_content] [Retry 1/3] Content assignment failed: Omeda API Error (HTTP 400)
[2025-10-29 16:10:01] [INFO] [add_content] [Retry 2/3] Executing: Add content job...
```

**Retry Pattern:**
- First attempt: No retry indicator
- Subsequent attempts: `[Retry X/3]` where X is the attempt number
- Maximum retries: 3 attempts with exponential backoff (60s, 120s, 240s)

## Configuration

### Enabling Logging Levels

Currently, all logging levels are enabled by default. In future versions, you'll be able to control logging via settings:

```php
// Coming in future release
update_option('omeda_log_level', 'basic'); // basic, advanced, or raw
```

### Accessing Logs

**Via WordPress Admin:**
1. Navigate to: **Omeda Integration → Workflow Logs**
2. Find your post in the list
3. Click **"View Logs"** to see detailed log entries
4. All log levels displayed with color coding

**Via Newsletter Glue Editor:**
1. Edit any newsletter post
2. Find **"Omeda Newsletter Deployment"** metabox
3. View recent log entries inline
4. Click **"View Full Logs"** link for complete details

**Via Database (Advanced):**
```sql
SELECT * FROM wp_postmeta 
WHERE meta_key = '_omeda_workflow_log' 
  AND post_id = 123;
```

## Log Entry Structure

Each log entry is stored as a JSON object with the following structure:

```json
{
  "timestamp": "2025-10-29T15:54:57+00:00",
  "level": "INFO",
  "message": "Step 1/3 Complete: Deployment created with TrackID: MTGMCD251029007",
  "context": {
    "track_id": "MTGMCD251029007"
  },
  "step_name": "create_deployment",
  "retry_count": null
}
```

**Field Descriptions:**
- `timestamp` - ISO 8601 datetime in UTC
- `level` - Log level (INFO, WARN, ERROR, ADVANCED, RAW)
- `message` - Human-readable log message
- `context` - Optional additional data as key-value pairs
- `step_name` - Workflow step identifier (optional)
- `retry_count` - Retry attempt number if applicable (optional)

## API Methods

### Logging Methods

```php
// Basic status logging
$this->workflow_manager->log_status(
    $post_id, 
    'Step 1/3 Complete', 
    'create_deployment'
);

// Error logging with retry
$this->workflow_manager->log_error(
    $post_id,
    'API call failed',
    $error_context,
    'assign_audience',
    1 // retry count
);

// Warning logging
$this->workflow_manager->log_warning(
    $post_id,
    'Content added with warnings',
    'add_content'
);

// Advanced trace logging
$this->workflow_manager->log_advanced(
    $post_id,
    'Starting transaction...',
    'create_deployment'
);

// Raw request/response logging
$this->workflow_manager->log_raw(
    $post_id,
    'API Request',
    $request_data,
    'assign_audience'
);
```

### Retrieving Logs

```php
// Get all logs for a post
$logs = $this->workflow_manager->get_logs($post_id);

// Get logs by level
$errors = array_filter($logs, function($log) {
    return $log['level'] === 'ERROR';
});

// Get logs by step
$deployment_logs = array_filter($logs, function($log) {
    return $log['step_name'] === 'create_deployment';
});
```

## Best Practices

### For Developers

1. **Use Appropriate Log Levels:**
   - INFO for status updates users need to see
   - ADVANCED for debugging and trace information
   - RAW for API data (be cautious with sensitive data)

2. **Include Step Names:**
   - Always pass step name to log methods
   - Use consistent step name constants
   - Helps filter and analyze logs

3. **Add Context Data:**
   - Include relevant IDs (post_id, track_id, etc.)
   - Add error details in context array
   - Helps reproduce and diagnose issues

4. **Log Transaction Boundaries:**
   ```php
   $this->workflow_manager->log_advanced($post_id, 'Starting transaction...', $step_name);
   // ... do work ...
   $this->workflow_manager->log_advanced($post_id, 'Transaction completed.', $step_name);
   ```

### For Site Administrators

1. **Monitor Workflow Logs Regularly:**
   - Check logs page weekly for errors
   - Review failed deployments
   - Identify patterns in issues

2. **Enable Raw Logging Temporarily:**
   - Only when debugging specific issues
   - Disable after issue resolved
   - May contain sensitive API data

3. **Clean Up Old Logs:**
   - Consider purging logs older than 30 days
   - Keep recent logs for active troubleshooting
   - Future release will include automatic cleanup

## Performance Considerations

- Logs stored in post meta (no separate table)
- Each post can have up to 100 log entries (configurable)
- Old entries auto-purged when limit reached
- Minimal performance impact on workflows
- Raw logging adds slight overhead for data serialization

## Security Considerations

- Raw logs may contain sensitive data (API keys, customer IDs)
- Logs visible only to users with `manage_options` capability
- Consider GDPR implications for logged customer data
- Future release will include log data sanitization options

## Troubleshooting

### Issue: No logs appearing
**Solution:** Check that post has deployment type assigned and workflow has been triggered

### Issue: Logs not showing in UI
**Solution:** Clear WordPress object cache, check user permissions

### Issue: Too many log entries
**Solution:** Logs auto-purge after 100 entries per post

### Issue: Raw logs missing request data
**Solution:** Ensure raw logging enabled in settings (future release)

## Future Enhancements

Planned for future releases:

1. **Log Level Configuration:** UI setting to control which levels are captured
2. **Log Rotation:** Automatic archiving of old logs
3. **Log Export:** Download logs as CSV or JSON
4. **Log Search:** Full-text search across all logs
5. **Log Filtering:** Filter by date range, level, step, or keyword
6. **Log Aggregation:** Dashboard widget showing recent errors across all posts
7. **Email Alerts:** Notify admins of critical errors
8. **Performance Metrics:** Track average transaction times per step

## Related Documentation

- [Workflow Manager Class Documentation](../src/omeda-newsletter-connector/includes/class-omeda-workflow-manager.php)
- [Async Jobs Class Documentation](../src/omeda-newsletter-connector/includes/class-omeda-async-jobs.php)
- [Workflow Logs Page Guide](WORKFLOW_LOGS.md)
- [Troubleshooting Guide](TROUBLESHOOTING.md)

## Support

For logging-related issues:
1. Check logs page for error details
2. Review this documentation
3. Enable advanced/raw logging if needed
4. Contact support with log export if issue persists

---

**Documentation Version:** 1.0  
**Last Updated:** 2025-10-29  
**Plugin Version:** 1.10.0
