# Draft Deployment Workflow

## Overview

Starting in version 1.5.0, the Omeda WordPress Integration creates deployments in Omeda **immediately when saving a draft** (rather than waiting for publish). This provides faster feedback, better testing capabilities, and improved validation before going live.

## How It Works

### Previous Behavior (v1.4.0 and earlier)
1. User saves post as draft → Nothing happens in Omeda
2. User edits and saves multiple times → Still nothing in Omeda
3. User publishes post → **Then** deployment created in Omeda
4. **Problem**: No way to validate deployment until publish

### New Behavior (v1.5.0+)
1. User saves post as draft with deployment type → **Deployment created immediately**
2. User can see TrackID and logs right away
3. User can verify deployment exists in Omeda Email Builder
4. User publishes post → Deployment date updated to actual publish time

## Workflow Steps

### Step 1: Draft Save with Deployment Type

When you save a post as draft and select (or auto-detect) a deployment type:

```
[Draft Save Triggered]
    ↓
1. Calculate next nearest hour for deployment date
    - Current time: 2:30 PM → Next hour: 3:00 PM
    - Current time: 3:00 PM → Next hour: 4:00 PM
    - Uses GMT/UTC timezone for consistency
    ↓
2. Create Deployment in Omeda
    - DeploymentName: Post title
    - DeploymentDate: Next nearest hour (temporary)
    - DeploymentTypeId: From deployment type config
    - All other settings from deployment type
    ↓
3. Assign Audience
    - Uses QueryName from deployment type
    - OutputCriteria: Newsletter_Member_id (default)
    - SplitNumber: 1
    ↓
4. Upload Content and Subject
    - FromName, FromEmail, ReplyTo from config
    - Subject: Parsed with WordPress variables
    - HtmlContent: Post content with filters applied
    ↓
[TrackID saved, deployment ready in Omeda]
```

### Step 2: Subsequent Draft Saves

When you save the draft again after initial creation:

```
[Draft Save Triggered]
    ↓
- Deployment already exists (has TrackID)
    ↓
- Update content only
    - New subject
    - New HTML content
    - Updated from/reply-to if changed
    ↓
[Content updated in Omeda]
```

### Step 3: Publish or Schedule

When you publish or schedule the post:

```
[Publish/Schedule Triggered]
    ↓
1. Update content one final time
    ↓
2. Calculate actual deployment date
    - If scheduled: Use scheduled date
    - If immediate publish: Current time + delay (default 30 min)
    ↓
3. Update deployment date in Omeda
    ↓
4. Send test email (if configured)
    ↓
5. Schedule deployment for final send
    ↓
[Deployment scheduled for actual send time]
```

## Deployment Date Logic

### Next Nearest Hour Calculation

The system rounds up to the next full hour:

| Current Time | Next Nearest Hour | Explanation |
|--------------|-------------------|-------------|
| 2:00 PM | 3:00 PM | Round up to next hour |
| 2:15 PM | 3:00 PM | Round up to next hour |
| 2:30 PM | 3:00 PM | Round up to next hour |
| 2:45 PM | 3:00 PM | Round up to next hour |
| 2:59 PM | 3:00 PM | Round up to next hour |
| 3:00 PM | 4:00 PM | Already at hour, go to next |

**Implementation:**
```php
$next_hour_timestamp = ceil(time() / 3600) * 3600;
$next_hour_date = gmdate('Y-m-d H:i', $next_hour_timestamp);
```

### Why Next Hour?

1. **Realistic**: More meaningful than far-future dates (old: `2099-01-01`)
2. **Testing**: Easy to identify draft deployments in Omeda
3. **Validation**: Can verify deployment timing logic
4. **Safety**: Far enough in future to prevent accidental sends
5. **Clarity**: Makes it obvious this is a temporary date

### Date Update on Publish

When post is published/scheduled, the deployment date is updated to:

- **Scheduled Post**: Exact scheduled date/time in GMT
- **Immediate Publish**: Current time + publish delay (default 30 minutes)

## WordPress Admin Interface

### Meta Box Display During Draft

```
┌─────────────────────────────────────┐
│ Omeda Deployment                    │
├─────────────────────────────────────┤
│ Deployment Type:                    │
│ [My Newsletter ▼]                   │
│                                     │
│ Omeda TrackID: ABC123456789         │
│                                     │
│ Workflow Log:                       │
│ ┌─────────────────────────────────┐ │
│ │ [INFO] Step 1/4 Complete:       │ │
│ │ Deployment created with TrackID │ │
│ │ [INFO] Step 2/4 Complete:       │ │
│ │ Audience assigned.              │ │
│ │ [INFO] Step 3/4 Complete:       │ │
│ │ Content and subject sent        │ │
│ │ [INFO] Step 4/4 Complete:       │ │
│ │ Scheduled for 2025-10-29 15:00  │ │
│ │ (temporary - updates on publish)│ │
│ └─────────────────────────────────┘ │
└─────────────────────────────────────┘
```

### Status Messages

| Step | Message | Meaning |
|------|---------|---------|
| 1/4 | Deployment created with TrackID: {id} | Deployment shell created in Omeda |
| 2/4 | Audience assigned | Query connected to deployment |
| 3/4 | Content and subject sent to Omeda | HTML and metadata uploaded |
| 4/4 | Initial setup complete | Ready for testing/validation |

## Omeda Email Builder View

After draft save, you can check Omeda Email Builder:

1. Log into Omeda Email Builder
2. Search for deployment by name (post title)
3. Find deployment with TrackID matching WordPress
4. Verify:
   - ✓ Deployment exists
   - ✓ Audience assigned
   - ✓ Content uploaded
   - ✓ Subject line correct
   - ✓ From/Reply-to correct
   - ✓ Deployment date shows next hour (temporary)

## Benefits

### 1. Immediate Feedback
- See TrackID instantly after saving draft
- Know deployment was created successfully
- No need to publish to test API connection

### 2. Early Validation
- Verify deployment settings before publish
- Check content rendering in Omeda
- Confirm audience assignment worked
- Test email metadata (from, subject, etc.)

### 3. Iterative Development
- Edit and save multiple times
- Content updates sent to Omeda automatically
- See changes reflected in Omeda immediately
- No need to delete and recreate deployments

### 4. Better Testing
- Test with real deployments before going live
- Send test emails from Omeda during draft phase
- Verify tracking links and merge tags
- Catch issues before publish deadline

### 5. Team Collaboration
- Share TrackID with team for review
- Multiple people can check deployment in Omeda
- Designer can verify layout in Email Builder
- Manager can approve before publish

## Best Practices

### When Creating Drafts

1. **Select Deployment Type First**: Choose or verify auto-detected type before saving
2. **Save Draft**: Click "Save Draft" to trigger deployment creation
3. **Verify TrackID**: Check meta box for TrackID confirmation
4. **Review Logs**: Ensure all 4 steps completed successfully
5. **Check Omeda**: Verify deployment exists in Email Builder

### During Editing

1. **Save Often**: Each save updates content in Omeda
2. **Watch Logs**: Monitor for any errors during updates
3. **Test in Omeda**: Send test emails from Omeda as you edit
4. **Verify Changes**: Ensure content updates reflected in Omeda

### Before Publishing

1. **Final Content Review**: Check content one last time
2. **Verify Settings**: Confirm from/subject/reply-to are correct
3. **Check Audience**: Ensure correct query assigned
4. **Test Email**: Send final test from Omeda
5. **Schedule/Publish**: Set final deployment time

### After Publishing

1. **Verify Date Update**: Check that deployment date changed from temporary
2. **Confirm Schedule**: Ensure deployment scheduled for correct time
3. **Monitor Logs**: Watch for successful scheduling confirmation
4. **Final Test**: Send post-publish test email

## Troubleshooting

### Deployment Not Created on Draft Save

**Symptoms**: No TrackID appears after saving draft

**Possible Causes**:
1. No deployment type selected or auto-detected
2. API credentials missing or invalid
3. Omeda API unavailable
4. Network connection issue

**Solutions**:
1. Verify deployment type is selected in meta box
2. Check API credentials in Settings
3. Review workflow logs for error messages
4. Check browser console for JavaScript errors
5. Try saving again after fixing issues

### TrackID Shows But Errors in Logs

**Symptoms**: TrackID present but workflow shows errors

**Possible Causes**:
1. Audience query name incorrect
2. Invalid from/reply-to email addresses
3. Content contains invalid HTML
4. Omeda API rate limiting

**Solutions**:
1. Verify audience query exists in Omeda Audience Builder
2. Check email addresses in deployment type config
3. Validate HTML content structure
4. Wait a few minutes and try updating again

### Content Not Updating in Omeda

**Symptoms**: Edits in WordPress not reflected in Omeda

**Possible Causes**:
1. Errors during content update step
2. API timeout
3. Content exceeds size limits

**Solutions**:
1. Check workflow logs for specific errors
2. Try simplifying content temporarily
3. Verify content meets Omeda requirements
4. Check for unsubscribe link in content

### Deployment Date Not Updating on Publish

**Symptoms**: Date stays at next-hour time after publish

**Possible Causes**:
1. Publish action didn't trigger workflow
2. API error during date update
3. Deployment already scheduled in Omeda

**Solutions**:
1. Check workflow logs for publish step
2. Verify post actually published (not still draft)
3. Manually check deployment in Omeda
4. Try re-saving published post

## Technical Details

### Date Calculation Code

```php
// Calculate next nearest hour
$next_hour_timestamp = ceil(time() / 3600) * 3600;
$next_hour_date = gmdate('Y-m-d H:i', $next_hour_timestamp);
```

**Explanation**:
- `time()`: Current Unix timestamp
- `/ 3600`: Convert to hours
- `ceil()`: Round up to next integer
- `* 3600`: Convert back to seconds
- `gmdate()`: Format as GMT/UTC date string

### API Calls During Draft Save

1. **POST** `/omail/deployment/*`
   - Creates deployment shell
   - Returns TrackID
   
2. **POST** `/omail/deployment/audience/add/*`
   - Assigns audience query
   - Links to deployment
   
3. **POST** `/omail/deployment/content/*`
   - Uploads HTML content
   - Sets subject, from, reply-to
   - XML payload format

### Metadata Stored

| Meta Key | Value | Purpose |
|----------|-------|---------|
| `_omeda_config_id` | Deployment Type Post ID | Links to configuration |
| `_omeda_track_id` | Omeda TrackID | Links to Omeda deployment |
| `_omeda_workflow_log` | JSON array | Stores workflow status |

## Compatibility

### WordPress Versions
- Requires: WordPress 5.0+
- Tested: WordPress 6.0+

### PHP Versions
- Requires: PHP 7.4+
- Tested: PHP 8.0+

### Omeda API
- Requires: Valid API credentials
- Endpoint: Email Builder Deployment API
- Format: JSON (deployment) + XML (content)

## Future Enhancements

### Planned Features
- Manual date override for draft deployments
- Draft deployment date picker
- Batch update for multiple drafts
- Deployment preview in WordPress
- Content diff viewer (changes since last save)

### Possible Improvements
- Configurable temporary date offset
- Draft deployment naming conventions
- Automatic test send on draft save
- Deployment status indicators
- Live sync between WordPress and Omeda

## Related Documentation

- [Workflow Manager](./WORKFLOW_MANAGER.md)
- [API Client](./API_CLIENT.md)
- [Deployment Types](./DEPLOYMENT_TYPES.md)
- [Changelog](../CHANGELOG.md#150---2025-10-29)

---

**Version**: 1.5.0  
**Last Updated**: 2025-10-29  
**Author**: Josh Stogner
