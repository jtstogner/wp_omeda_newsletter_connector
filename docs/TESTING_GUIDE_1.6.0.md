# Testing Guide: Version 1.6.0 - Workflow Logs Page

## Version Information
- **Version**: 1.6.0
- **Release Date**: 2025-10-29
- **Type**: Minor Release (New Feature)
- **Testing Environment**: wp-env (localhost:8889)
- **Admin Credentials**: admin / password

## What's New in 1.6.0

### New Workflow Logs Page
A comprehensive admin interface for viewing all workflow execution logs without needing to check debug.log or query the database directly.

## Testing Checklist

### 1. Verify Menu Structure ✓
**Location:** WordPress Admin → Omeda Integration

Expected submenu items in order:
1. Settings
2. **Workflow Logs** ← NEW
3. Background Jobs (Action Scheduler)

**Test Steps:**
```
1. Log in to wp-admin (admin/password)
2. Look for "Omeda Integration" in left sidebar
3. Hover over it to see submenu
4. Verify "Workflow Logs" appears second
5. Click "Workflow Logs"
```

**Expected Result:**
- Menu item exists and is clickable
- Redirects to `/wp-admin/admin.php?page=omeda-workflow-logs`
- Page loads without errors

---

### 2. Test Empty State ✓
**When:** No posts have workflow logs yet

**Test Steps:**
```
1. Navigate to Omeda Integration → Workflow Logs
2. Observe the page content
```

**Expected Result:**
- Page shows heading "Workflow Logs"
- Displays message: "No workflow logs found."
- No errors displayed

---

### 3. Create Test Logs ✓
**Purpose:** Generate some workflow logs to test the display

**Test Steps:**
```
1. Go to Posts → Add New
2. Add a title: "Test Workflow Logs"
3. Add some content
4. In the right sidebar, find "Omeda Newsletter Integration" metabox
5. Select a deployment type from dropdown
6. Click "Save Draft"
7. Wait for processing
8. Repeat with 2-3 more posts
```

**Expected Result:**
- Posts save successfully
- Workflow processes in background
- Logs generated in post meta

---

### 4. Test Main Logs List ✓
**When:** After creating test posts with logs

**Test Steps:**
```
1. Navigate to Omeda Integration → Workflow Logs
2. Verify the table displays
3. Check each column
```

**Expected Display:**

| Column | Expected Content |
|--------|------------------|
| Post ID | Numeric ID (e.g., 123) |
| Title | Post title |
| Type | "post" or "ngl_pattern" |
| Status | "draft", "publish", etc. |
| Last Modified | Date/time |
| Actions | "View Logs" and "Edit Post" buttons |

**Validation Points:**
- ✓ All posts with logs appear
- ✓ Most recently modified posts first
- ✓ 20 posts per page max
- ✓ Pagination appears if > 20 posts

---

### 5. Test Log Detail View ✓
**Purpose:** View individual post logs

**Test Steps:**
```
1. From the main logs list
2. Click "View Logs" for any post
3. Scroll down to see the details section
```

**Expected Display:**

**Post Information:**
- Post ID: [number]
- Current Workflow State: created/audience_assigned/content_uploaded/etc.
- Omeda Deployment ID: [TrackID from Omeda]

**Log Entries Table:**

| Timestamp | Level | Message | Context |
|-----------|-------|---------|---------|
| 2025-10-29 18:42:15 | INFO | Step 1/5: Deployment created | {...} |
| 2025-10-29 18:42:20 | INFO | Step 2/5: Audience assigned | {...} |

**Validation Points:**
- ✓ Logs sorted newest first
- ✓ Color coding works:
  - ERROR: Red background
  - WARN: Yellow background
  - INFO: Green background
- ✓ Context displays as formatted data
- ✓ "Back to Logs List" link works

---

### 6. Test Edit Post Links ✓
**Purpose:** Quick access to post editor

**Test Steps:**
```
1. From main logs list, click "Edit Post"
2. From detail view, click "Edit Post" 
```

**Expected Result:**
- Opens post editor in new tab or same window
- Correct post loads
- No errors

---

### 7. Test Pagination ✓
**When:** More than 20 posts have logs

**Test Steps:**
```
1. Create 25+ test posts with workflow logs
2. Go to Workflow Logs page
3. Verify pagination controls appear
4. Click "Next" or page number
```

**Expected Result:**
- Shows only 20 posts per page
- Pagination controls at bottom
- Page numbers clickable
- Next/Previous work correctly
- Current page highlighted

---

### 8. Test Permissions ✓
**Purpose:** Ensure proper access control

**Test Steps:**
```
1. Log out of admin account
2. Log in as a user with Editor role (not Administrator)
3. Try to access: /wp-admin/admin.php?page=omeda-workflow-logs
```

**Expected Result:**
- Access denied
- Message: "You do not have sufficient permissions to access this page."
- No logs visible

---

### 9. Test with Real Workflow ✓
**Purpose:** End-to-end workflow logging test

**Test Steps:**
```
1. Create a new post
2. Assign deployment type with all required fields:
   - Omeda Deployment Type
   - Audience Query ID
   - From Name, From Email, Reply To
   - Subject Format
3. Save as draft
4. Go to Workflow Logs
5. Click "View Logs" for this post
6. Verify log entries show:
   - Step 1/5: Deployment created with ID [number]
   - Step 2/5: Audience assigned
   - Step 3/5: Content uploaded
```

**Expected Log Progression:**
```
INFO  | Step 1/5: Deployment created with ID 12345
INFO  | Step 2/5: Audience query 'test-query' assigned  
INFO  | Step 3/5: Content and subject uploaded
INFO  | Deployment date set to [date] (will update when published)
```

---

### 10. Test Error Logging ✓
**Purpose:** Verify ERROR level logs display correctly

**Test Steps:**
```
1. Create a post with invalid configuration
   (e.g., missing API credentials or bad audience query)
2. Try to save/publish
3. Check Workflow Logs
4. Look for ERROR entries
```

**Expected Result:**
- ERROR entries have red background
- Error messages are clear
- Context shows error details
- Stack traces or API errors visible in context

---

## Common Issues and Solutions

### Issue: Menu Item Not Appearing
**Cause:** Cache or incorrect role
**Solution:**
```
1. Clear WordPress cache
2. Verify logged in as Administrator
3. Check file permissions on class-omeda-settings.php
4. Deactivate and reactivate plugin
```

### Issue: "No Logs Found" When Logs Exist
**Cause:** Database query issue or post meta not saved
**Solution:**
```sql
-- Check if logs exist
SELECT post_id, meta_key, meta_value 
FROM wp_postmeta 
WHERE meta_key = '_omeda_workflow_log'
LIMIT 5;
```

### Issue: Logs Not Sorting Correctly
**Cause:** Timestamp format issue
**Solution:** Logs should be sorted by post_modified in SQL, not log timestamp

### Issue: Context Not Displaying
**Cause:** JSON decode failure
**Solution:** Check that context is valid JSON in database

---

## Performance Testing

### Test Large Log Sets
**Purpose:** Ensure pagination works with many posts

**Test Steps:**
```
1. Create 50+ posts with logs
2. Navigate through pages
3. Check load times
4. Verify memory usage
```

**Expected Performance:**
- Page loads in < 2 seconds
- Pagination smooth
- No timeout errors
- Memory usage reasonable

### Test Large Context Data
**Purpose:** Ensure large API responses display properly

**Test Steps:**
```
1. Create deployment with large HTML content
2. View logs for this post
3. Check context displays
```

**Expected Result:**
- Large context displays without truncation
- Page remains responsive
- No browser slowdown

---

## Regression Testing

Ensure existing functionality still works:

### ✓ Settings Page
- Still accessible at Omeda Integration → Settings
- All settings save correctly
- No broken fields

### ✓ Deployment Types
- CPT still works
- Dropdowns populate
- Meta fields save

### ✓ Background Jobs
- Action Scheduler page still accessible
- Jobs run correctly
- No duplicate job scheduling

### ✓ Workflow Execution
- Posts still trigger workflows
- Deployments created in Omeda
- Content uploads successfully

---

## Browser Compatibility

Test in multiple browsers:

- [ ] Chrome/Edge (Chromium)
- [ ] Firefox
- [ ] Safari (if available)

**Test Points:**
- Table displays correctly
- Pagination works
- Color coding visible
- Links clickable

---

## Documentation Review

### Files to Review:
1. [CHANGELOG.md](../../CHANGELOG.md) - Version 1.6.0 entry
2. [WORKFLOW_LOGS_PAGE.md](../WORKFLOW_LOGS_PAGE.md) - Feature documentation
3. [class-omeda-settings.php](../../src/omeda-newsletter-connector/includes/class-omeda-settings.php) - Implementation

### Validation:
- ✓ Version numbers match
- ✓ Feature descriptions accurate
- ✓ Technical details correct
- ✓ Examples work as documented

---

## Sign-Off Checklist

Before deploying to production:

- [ ] All menu items appear correctly
- [ ] Empty state displays properly
- [ ] Log list populates with data
- [ ] Detail view shows all information
- [ ] Color coding works for all log levels
- [ ] Pagination functions correctly
- [ ] Permissions enforced
- [ ] No PHP errors in debug.log
- [ ] No JavaScript console errors
- [ ] Works in all supported browsers
- [ ] Documentation updated
- [ ] CHANGELOG.md updated
- [ ] Version number incremented

---

## Quick Test Script

For rapid testing, create these test posts:

```bash
# Test Post 1: Success Case
Title: "Workflow Test - Success"
Deployment Type: [Select one]
Audience Query: valid-query-name
Save Draft → Should see INFO logs

# Test Post 2: Warning Case  
Title: "Workflow Test - Warning"
Deployment Type: [Select one]
Audience Query: [leave empty]
Save Draft → Should see WARN logs

# Test Post 3: Error Case
Title: "Workflow Test - Error"
Deployment Type: [Select one]
Audience Query: invalid@query
Save Draft → Should see ERROR logs
```

Then:
1. Go to Workflow Logs
2. Verify all 3 posts appear
3. Check each for appropriate log levels
4. Verify color coding

---

## Support Resources

- **Debug Log Location:** `/wp-content/debug.log`
- **Database Tables:** `wp_posts`, `wp_postmeta`
- **Meta Keys:** `_omeda_workflow_log`, `_omeda_workflow_state`, `_omeda_deployment_id`
- **Admin URL:** `/wp-admin/admin.php?page=omeda-workflow-logs`

---

**Testing Status:** Ready for Testing  
**Version Tested:** 1.6.0  
**Last Updated:** 2025-10-29  
**Tester:** [Your Name]
