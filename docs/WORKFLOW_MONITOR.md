# Workflow Monitor Documentation

## Overview

The Workflow Monitor provides real-time visibility into all Omeda deployment workflow tasks. It shows what's currently running, what's scheduled, and the recent activity logs for all deployments.

**Version**: 1.5.0  
**Location**: Omeda Integration → Workflow Monitor

---

## Features

### 1. System-Wide Task View

The main monitor page displays all pending workflow tasks across your entire WordPress site.

**Information Shown:**
- Post ID (clickable link to edit page)
- Post Title
- Task Type (Create Deployment, Assign Audience, etc.)
- Scheduled Time (when the task will run)
- Status (Pending/Scheduled)
- Retry Count (if task has failed and is retrying)

**Task Types:**
1. **Create Deployment** - Initial deployment creation in Omeda
2. **Assign Audience** - Assigns the audience query to deployment
3. **Add Content** - Adds initial HTML content to deployment
4. **Update Content** - Updates HTML content after draft edits
5. **Send Test** - Sends test email to configured testers
6. **Schedule Deployment** - Finalizes and schedules the deployment

### 2. Per-Post Workflow Status Metabox

Every post with an assigned deployment type gets a workflow status metabox in the sidebar.

**Shows:**
- Omeda Track ID (once deployment is created)
- List of pending tasks for this specific post
- Recent activity log (last 5 entries)
- Color-coded log levels

**Log Level Colors:**
- 🔵 **INFO** (Blue) - Normal operation messages
- 🟠 **WARN** (Orange) - Warnings, non-critical issues
- 🔴 **ERROR** (Red) - Critical errors requiring attention

### 3. Recent Workflow Logs

The monitor page includes a "Recent Workflow Logs" section showing:
- Logs from the last 10 posts with workflow activity
- Full log history per post
- Timestamps for all actions
- Detailed error messages with context

---

## How to Access

### Monitor Page
1. Go to WordPress admin dashboard
2. Navigate to **Omeda Integration** → **Workflow Monitor**
3. View all active and scheduled tasks

### Post Status Metabox
1. Edit any post with an assigned deployment type
2. Look in the right sidebar
3. Find the **"Omeda Workflow Status"** metabox
4. See real-time status for this post

---

## Understanding the Workflow

### Draft Save Workflow

When you save a post as draft (first time with deployment type):

```
Step 1: Create Deployment (immediate)
    ↓
Step 2: Assign Audience (+30 seconds)
    ↓
Step 3: Add Content (+30 seconds)
    ↓
[Draft editing phase - deployment exists in Omeda]
```

### Content Update Workflow

When you update a draft:

```
Update Content (debounced 60 seconds)
    ↓
[Content updated in Omeda]
```

### Publish/Schedule Workflow

When you publish or schedule the post:

```
Step 1: Update Content (immediate)
    ↓
Step 2: Send Test Email (+30 seconds)
    ↓
Step 3: Schedule Deployment (+60 seconds)
    ↓
[Deployment scheduled in Omeda for publish time]
```

---

## Scheduling Methods

The plugin uses different scheduling methods depending on your environment:

### Development/Staging
- **Method**: Action Scheduler
- **Location**: Tools → Scheduled Actions
- **Benefits**: 
  - Web-based UI for viewing jobs
  - Better debugging tools
  - Automatic retry on failure
  - More reliable execution

### Production
- **Method**: Native WP-Cron
- **Benefits**:
  - No additional plugins needed
  - WordPress standard
  - Triggered by site traffic
  - Lightweight and fast

**How it detects environment:**
- Checks `WP_ENV` constant (if set to 'production')
- Checks `wp_get_environment_type()` function
- Defaults to Action Scheduler in development

---

## Troubleshooting

### No Tasks Appearing

**Possible Causes:**
1. No posts have deployment types assigned
2. All tasks have already completed
3. Cron is not running

**Solutions:**
1. Create/edit a post with a deployment type
2. Check if posts are in draft/pending status
3. Verify WP-Cron is enabled: `define('DISABLE_WP_CRON', false);`

### Tasks Stuck in Pending

**Possible Causes:**
1. Cron not executing
2. API credentials incorrect
3. Network connectivity issues

**Solutions:**
1. **Development**: Visit Tools → Scheduled Actions → Run Now
2. **Production**: Visit any page to trigger WP-Cron
3. Check debug log for API errors
4. Verify API credentials in settings
5. Check workflow log in post metabox

### High Retry Count

**Meaning:** Task has failed and is being retried with exponential backoff.

**Retry Schedule:**
- Retry 1: +60 seconds
- Retry 2: +120 seconds (2 minutes)
- Retry 3: +240 seconds (4 minutes)
- Max retries: 3

**What to Check:**
1. View post's workflow log for error details
2. Check Recent Workflow Logs section for patterns
3. Verify API credentials are correct
4. Check Omeda API status
5. Review error context in logs

### Database Connection Errors

If you see: `mysqli_real_connect(): (HY000/2002): Connection refused`

**This is a wp-env issue, not plugin issue:**
1. Restart wp-env: `npm run wp-env stop && npm run wp-env start`
2. Check Docker is running
3. Verify database container health

---

## Log Entry Format

Each log entry contains:

```json
{
    "timestamp": "2025-10-29 18:30:15",
    "level": "INFO",
    "message": "Step 1/5 Complete: Deployment created with TrackID: ABC123",
    "context": {
        "endpoint": "/brand/TEST/deployments",
        "payload": {...},
        "response_body": {...}
    }
}
```

**Fields:**
- `timestamp`: When the event occurred (site timezone)
- `level`: INFO, WARN, or ERROR
- `message`: Human-readable status message
- `context`: Additional debug information (only on errors)

---

## Best Practices

### 1. Regular Monitoring

Check the monitor page regularly to:
- Ensure tasks are completing successfully
- Identify stuck or failing deployments
- Monitor retry counts

### 2. Use the Metabox

The post metabox is your first line of defense:
- Check before publishing important posts
- Verify Track ID exists
- Confirm no pending tasks before deadline

### 3. Debug with Logs

When troubleshooting:
1. Check post metabox for immediate errors
2. Review Recent Workflow Logs for patterns
3. Check WordPress debug log for technical details
4. Use context data in error logs

### 4. Environment Awareness

**Development:**
- Action Scheduler provides better visibility
- Use Tools → Scheduled Actions for debugging
- Can manually run tasks via "Run Now"

**Production:**
- WP-Cron is triggered by site traffic
- Visit the site if tasks aren't running
- Consider real cron if traffic is low

---

## API Endpoints Reference

### Workflow Monitor Page
- **URL**: `/wp-admin/admin.php?page=omeda-workflow-monitor`
- **Permission**: `manage_options` (Administrator)

### Metabox Location
- **Post Types**: `post`, `ngl_pattern` (Newsletter Glue)
- **Position**: Sidebar (side), High priority
- **Title**: "Omeda Workflow Status"

---

## Database Schema

### Post Meta Keys

```php
// Deployment tracking
'_omeda_track_id'              // string - Omeda deployment Track ID
'_omeda_deployment_type_id'    // int    - Deployment type config post ID

// Workflow logs (multiple entries)
'_omeda_workflow_log'          // JSON string - Log entry
```

### Log Storage

- Each log entry is stored as separate post meta
- Allows multiple log entries per post
- Retrieved with `get_post_meta($post_id, '_omeda_workflow_log')`
- Returns array of JSON strings

---

## Performance Considerations

### Monitor Page Load Time

**Optimizations:**
- Limits to 50 pending actions (Action Scheduler)
- Limits to 10 recent logs
- No heavy API calls
- Cached deployment type data

**Expected Load Time:** < 1 second

### Metabox Load Time

**Optimizations:**
- Only queries for current post
- Limits to 5 recent log entries
- No external API calls
- Minimal database queries

**Expected Load Time:** < 100ms

---

## Integration with Other Tools

### Action Scheduler (Development)

When using Action Scheduler, you have access to:
- **Scheduled Actions page**: View all queued jobs
- **Completed Actions log**: See execution history
- **Failed Actions**: Review failures with stack traces
- **Manual execution**: Run jobs immediately via UI

**Access:** Tools → Scheduled Actions

### WP-Cron (Production)

Native WordPress functionality:
- No additional UI
- Triggered by site visits
- Lightweight and standard
- Works with most hosting

**Monitoring:** Use the Workflow Monitor page (this plugin)

---

## Security

### Permissions

- Monitor page: `manage_options` capability (Administrators only)
- Metabox: Visible to anyone who can edit posts
- Logs: Only stored in database, not publicly accessible

### Data Privacy

- Logs contain deployment configuration data
- API credentials never logged
- Track IDs are safe to display
- Consider logs contain post titles and content references

---

## Support & Troubleshooting

### Common Questions

**Q: Why don't I see any tasks?**  
A: Tasks only appear when posts are being processed. Create/edit a post with a deployment type to generate tasks.

**Q: Tasks aren't running. What's wrong?**  
A: Check if WP-Cron is enabled. In development, check Action Scheduler settings.

**Q: How long do tasks stay in the monitor?**  
A: Only pending/scheduled tasks appear. Completed tasks are removed automatically.

**Q: Can I manually run a stuck task?**  
A: In development (Action Scheduler): Yes, via Tools → Scheduled Actions. In production (WP-Cron): Visit the site to trigger cron.

**Q: What if I see database connection errors?**  
A: This is typically a wp-env/Docker issue. Restart your development environment.

### Debug Checklist

When tasks aren't working:

- [ ] Check monitor page for pending tasks
- [ ] Check post metabox for error messages
- [ ] Check Recent Workflow Logs for patterns
- [ ] Verify API credentials in settings
- [ ] Check WordPress debug log
- [ ] Verify WP-Cron is enabled
- [ ] Test with a simple post first
- [ ] Check Omeda API status/connectivity

---

## Future Enhancements

Planned features for future versions:

- [ ] Task cancellation from UI
- [ ] Manual task retry button
- [ ] Email notifications for failures
- [ ] Deployment analytics dashboard
- [ ] Bulk operations (cancel all, retry all)
- [ ] Export logs to CSV
- [ ] Real-time updates with AJAX
- [ ] Task filtering and search

---

**Version:** 1.5.0  
**Last Updated:** October 29, 2025  
**Plugin:** Omeda WordPress Integration
