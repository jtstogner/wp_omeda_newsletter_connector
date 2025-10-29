# Quick Test: Newsletter Glue Campaign Workflow

**Version:** 1.6.1+  
**Time Required:** 2-3 minutes

## Prerequisites
- ✅ Plugin version 1.6.1 or higher installed
- ✅ Omeda API credentials configured
- ✅ At least one deployment type created
- ✅ Newsletter Glue plugin active

## Test Steps

### 1. Create or Open Campaign
Navigate to: **Newsletter Glue → Campaigns**

Choose one:
- Click "Add New" for new campaign
- Click existing campaign to edit

### 2. Locate Omeda Meta Box
Look in the **right sidebar** for:
```
┌─────────────────────────────┐
│   Omeda Deployment          │
├─────────────────────────────┤
│ Deployment Type:            │
│ [Dropdown here]             │
└─────────────────────────────┘
```

### 3. Select Deployment Type
- Click the dropdown
- Select your deployment type (e.g., "Just Josh")
- Or leave as "Auto-Detect" if configured

### 4. Save Draft
Click the **"Save Draft"** button (top right)

Wait for page to reload...

### 5. Check Results

#### ✅ Success Indicators

**TrackID Appears:**
```
Omeda TrackID: 123456789
```

**Workflow Logs Show:**
```
[INFO] Workflow Initiated: Post saved as draft with a Deployment Type.
[INFO] Step 1/4 Complete: Deployment created with TrackID: 123456789
[INFO] Step 2/4 Complete: Audience assigned.
[INFO] Step 3/4 Complete: Content and subject sent to Omeda.
[INFO] Step 4/4 Complete: Initial deployment setup complete.
```

#### ❌ Failure Indicators

**No TrackID:**
- Workflow not triggering
- Check plugin version (should be 1.6.1+)

**No Logs:**
- Deployment type not selected
- OR API credentials missing
- OR workflow not triggering

**Error Messages (Red Text):**
```
[ERROR] Failed to create deployment: [error details]
```
- Check API credentials
- Check Omeda API status
- Check debug.log for details

## Quick Commands

### Check Post Type
```bash
wp-env run cli wp post get [POST_ID] --field=post_type
```
**Expected:** `newsletterglue`

### Check for Logs
```bash
wp-env run cli wp post meta get [POST_ID] _omeda_workflow_log
```
**Expected:** JSON array with log entries

### Check for TrackID
```bash
wp-env run cli wp post meta get [POST_ID] _omeda_track_id
```
**Expected:** A numeric ID

### Verify in Omeda
1. Log into Omeda platform
2. Go to Deployments
3. Search for TrackID shown in WordPress
4. Confirm deployment exists

## Troubleshooting

### Problem: Meta box doesn't appear
**Solution:**
1. Check plugin is active: Plugins → Installed Plugins
2. Check version is 1.6.1+
3. Clear cache: `wp cache flush`

### Problem: Dropdown is empty
**Solution:**
1. Go to: Omeda Integration → Settings
2. Verify API credentials are configured
3. Go to: Omeda Integration → Deployment Types
4. Create at least one deployment type

### Problem: No workflow logs appear
**Solution:**
1. Verify deployment type is selected (not "Do Not Deploy")
2. Check plugin version: `wp plugin list | grep omeda`
3. Enable debug logging in wp-config.php:
   ```php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   ```
4. Check `wp-content/debug.log` for errors

### Problem: ERROR in workflow logs
**Solution:**
1. Read the error message carefully
2. Common errors:
   - "API credentials not configured" → Set up in Settings
   - "Deployment type not found" → Check deployment type exists
   - "Failed to create deployment" → Check Omeda API status
   - "Invalid audience query" → Check audience builder query name
3. Check full error context in debug.log

## Expected Behavior

### On First Save (Draft)
1. Deployment created in Omeda ✓
2. TrackID stored in WordPress ✓
3. Audience assigned ✓
4. Content sent to Omeda ✓
5. Temporary deployment date set (next hour) ✓

### On Subsequent Saves (Draft)
1. Content updated in Omeda ✓
2. No new deployment created ✓
3. Same TrackID used ✓

### On Publish/Schedule
1. Content updated (final version) ✓
2. Test email sent ✓
3. Deployment date updated to actual publish date ✓
4. Deployment scheduled in Omeda ✓

## Next Steps

### If Test Passes ✅
1. Test with different deployment types
2. Test publish workflow
3. Test scheduled posts
4. Verify test emails received
5. Confirm deployment goes out on schedule

### If Test Fails ❌
1. Review troubleshooting section above
2. Check `wp-content/debug.log`
3. Verify plugin version is 1.6.1+
4. See detailed guide: `docs/TROUBLESHOOTING_WORKFLOW_NOT_TRIGGERING.md`

## Need Help?

### Documentation
- `docs/FIX_SUMMARY_V1.6.1.md` - What was fixed
- `docs/TROUBLESHOOTING_WORKFLOW_NOT_TRIGGERING.md` - Detailed troubleshooting
- `docs/NEWSLETTER_GLUE_INTEGRATION.md` - Integration overview
- `CHANGELOG.md` - Version history

### Debug Checklist
- [ ] Plugin version 1.6.1+
- [ ] API credentials configured
- [ ] Deployment type exists
- [ ] Deployment type selected in post
- [ ] Post type is `newsletterglue`
- [ ] Debug logging enabled
- [ ] Cache cleared

---

**Last Updated:** 2025-10-29  
**For Plugin Version:** 1.6.1+
