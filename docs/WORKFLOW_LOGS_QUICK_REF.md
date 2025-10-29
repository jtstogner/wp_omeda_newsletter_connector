# Quick Reference: Workflow Logs Page - Version 1.6.0

## Access the Logs Page

### Via Admin Menu
```
WordPress Admin → Omeda Integration → Workflow Logs
```

### Direct URL
```
http://localhost:8889/wp-admin/admin.php?page=omeda-workflow-logs
```

## What You'll See

### Main Page
- List of all posts with workflow logs
- Table with: Post ID, Title, Type, Status, Last Modified
- "View Logs" and "Edit Post" buttons for each
- Pagination if more than 20 posts

### Detail View
Click "View Logs" to see:
- Post information (ID, title, workflow state, deployment ID)
- Complete log history for that post
- Color-coded log levels
- API context data

## Log Levels

| Color | Level | Meaning |
|-------|-------|---------|
| 🟢 Green | INFO | Normal operation |
| 🟡 Yellow | WARN | Non-critical issue |
| 🔴 Red | ERROR | Critical failure |

## Common Workflow States

1. `created` - Deployment created in Omeda
2. `audience_assigned` - Audience query set
3. `content_uploaded` - HTML/subject sent
4. `test_sent` - Test email dispatched
5. `scheduled` - Final deployment scheduled

## Typical Log Sequence (Draft Save)

```
INFO | Step 1/5: Deployment created with ID 12345
INFO | Step 2/5: Audience query 'my-query' assigned
INFO | Step 3/5: Content and subject uploaded
INFO | Deployment date set to 2025-10-29 15:00 (will update when published)
```

## Troubleshooting with Logs

### Deployment Not Created
Look for:
- ERROR entries about API credentials
- Missing deployment type configuration
- Invalid API endpoint

### Audience Not Assigned  
Look for:
- WARN about missing audience query
- ERROR about invalid query name
- API response errors

### Content Not Uploaded
Look for:
- ERROR about content format
- Missing subject format
- HTML conversion errors

## Quick Actions

### From Main List
- **View Logs** - See all logs for a post
- **Edit Post** - Go to post editor

### From Detail View
- **Back to Logs List** - Return to main page
- **Edit Post** - Go to post editor

## Tips

### Find Recent Issues
1. Logs sorted by "Last Modified"
2. Most recent posts appear first
3. Look for red ERROR badges

### Check Specific Post
1. Know the Post ID
2. Use browser search (Ctrl+F / Cmd+F)
3. Search for the Post ID
4. Click "View Logs"

### Copy Error Details
1. Go to detail view
2. Find ERROR entry
3. Copy context data
4. Share with support/developer

### Verify Workflow Progress
1. Save post as draft
2. Wait ~30 seconds
3. Refresh Workflow Logs page
4. Look for your post
5. Check log progression

## What NOT to Do

❌ Don't delete the `_omeda_workflow_log` post meta directly  
❌ Don't modify log entries manually  
❌ Don't share logs publicly (may contain sensitive data)  
✅ Do take screenshots for support  
✅ Do check logs before contacting support  
✅ Do monitor ERROR levels regularly

## Debug Checklist

When something goes wrong:

1. ✓ Check Workflow Logs page first
2. ✓ Look for ERROR level entries
3. ✓ Read the error message
4. ✓ Check context data for details
5. ✓ Verify API credentials in Settings
6. ✓ Check deployment type configuration
7. ✓ Review Background Jobs page
8. ✓ Check WordPress debug.log if needed

## Permission Requirements

- Must be logged in as Administrator
- Requires `manage_options` capability
- Editors/Authors cannot access

## Files Location

### Plugin Files
```
wp-content/plugins/omeda-newsletter-connector/
├── includes/
│   └── class-omeda-settings.php (contains logs page)
└── omeda-wp-integration.php (version 1.6.0)
```

### Source Files
```
src/omeda-newsletter-connector/
├── includes/
│   └── class-omeda-settings.php
└── omeda-wp-integration.php
```

## Database Info

### Post Meta Keys
- `_omeda_workflow_log` - Log entries (JSON array)
- `_omeda_workflow_state` - Current state
- `_omeda_deployment_id` - Omeda TrackID

### Log Entry Format
```json
{
  "timestamp": "2025-10-29 18:42:15",
  "level": "INFO",
  "message": "Step 1/5: Deployment created",
  "context": { "deployment_id": 12345 }
}
```

## Related Pages

### Settings Page
```
WordPress Admin → Omeda Integration → Settings
```
Configure: API credentials, defaults, email settings

### Background Jobs
```
WordPress Admin → Omeda Integration → Background Jobs
```
View: Scheduled tasks, action queue, completed jobs

### Deployment Types
```
WordPress Admin → Omeda Deployment Types → All Deployment Types
```
Manage: Post type mappings, deployment configurations

## Support

### Check Logs First
Before reporting issues:
1. Check Workflow Logs page
2. Look for ERROR entries
3. Copy error messages and context
4. Include Post ID and deployment ID

### Share Logs
When asking for help:
1. Take screenshot of log entries
2. Include Post ID
3. Include deployment ID if available
4. Describe what you expected vs. what happened

### Common Questions

**Q: Why don't I see any logs?**  
A: Logs only appear after saving a post with a deployment type assigned.

**Q: How long are logs kept?**  
A: Logs are stored permanently in post meta. They can be cleared by deleting the post.

**Q: Can I export logs?**  
A: Not in v1.6.0, but planned for future release.

**Q: Can I filter by error level?**  
A: Not in v1.6.0, but planned for v1.7.0.

---

## Version Info

**Version:** 1.6.0  
**Added:** 2025-10-29  
**Status:** Production Ready  
**Credentials:** admin / password (wp-env)

---

For detailed documentation, see:
- [WORKFLOW_LOGS_PAGE.md](WORKFLOW_LOGS_PAGE.md) - Complete feature docs
- [TESTING_GUIDE_1.6.0.md](TESTING_GUIDE_1.6.0.md) - Testing procedures
- [CHANGELOG.md](../CHANGELOG.md) - Version history
