# Testing Guide - Omeda WordPress Integration v1.4.0

## Quick Testing Reference

### Accessing the Debug Log
```bash
tail -f wp-content/debug.log
```

Or via WP-CLI in wp-env:
```bash
wp-env run cli wp config get WP_DEBUG_LOG
```

### Viewing Background Jobs
Go to: **Omeda Integration > Background Jobs**

This shows the Action Scheduler queue with all pending, completed, and failed jobs.

## Testing the New Features (v1.4.0)

### 1. Default Email Settings

#### Test Setup
1. Go to **Omeda Integration > Settings**
2. Scroll to "Default Email Settings" section
3. Fill in:
   - Default From Name: `Your Newsletter`
   - Default From Email: `newsletter@yoursite.com`
   - Default Reply To Email: `replies@yoursite.com`
4. Click **Save Settings**

#### Test Validation
1. Go to **Omeda Integration > Deployment Types > Add New**
2. Verify that:
   - From Name field is pre-populated with "Your Newsletter"
   - From Email field is pre-populated with "newsletter@yoursite.com"
   - Reply To Email field is pre-populated with "replies@yoursite.com"
3. These should only appear for NEW deployment types (not existing ones)

### 2. WordPress Variables in Subject Line

#### Test Setup
Create or edit a deployment type with these subject formats:

**Test Case 1: Basic Variables**
```
Subject Format: {post_title} - {site_name}
Expected Output: "My Article Title - My WordPress Site"
```

**Test Case 2: Date Variables**
```
Subject Format: {post_date_F} {post_date_d}: {post_title}
Expected Output: "October 29: My Article Title"
```

**Test Case 3: Author Variables**
```
Subject Format: {post_title} by {author_name}
Expected Output: "My Article Title by John Doe"
```

**Test Case 4: Combined with Omeda Variables**
```
Subject Format: {post_title} | @{mv_html_title_subject}@
Expected Output: "My Article Title | [Omeda processes their variable]"
```

#### Test Validation
1. Create a test post with the deployment type
2. Save as draft
3. Check the Omeda Deployment meta box for workflow logs
4. Log should show: "Content updated successfully in Omeda"
5. Verify subject in Omeda dashboard matches expected output

### 3. Omeda Deployment Types Dropdown

#### Test Prerequisites
- API credentials must be configured
- Brand abbreviation must be correct

#### Test Steps
1. Go to **Omeda Integration > Deployment Types > Add New**
2. Click on "Omeda Deployment Type" dropdown
3. Verify:
   - Dropdown is searchable (type to filter)
   - Shows deployment types from Omeda API
   - Format: "Type Name (ID: 2344)"
   - Only active types shown (StatusCode = 1)

#### Refresh Test
1. Click "Refresh from Omeda" button
2. Confirm the refresh dialog
3. Page should reload
4. Dropdown should show updated list

### 4. Post Type / Template Assignment

#### Test Steps
1. Create new deployment type
2. Click "Assigned Post Type / Template" dropdown
3. Verify dropdown shows:
   - **Post Types** section (Post, Page, etc.)
   - **Newsletter Glue Enabled Post Types** (if NG is active)
   - **Newsletter Glue Templates** (if NG has templates)
   - **Newsletter Glue Template Categories** (if NG has categories)
4. Dropdown should be searchable

#### Test Auto-Detection
1. Create deployment type assigned to "Post" post type
2. Create a new post
3. In Omeda Deployment meta box, it should show:
   - "Auto-detected: This post type/template is configured to use: [Your Deployment Type]"

### 5. Audience Query Field

#### Test Steps
1. Go to **Omeda Integration > Deployment Types > Add New**
2. Find "Audience Query" field
3. Verify:
   - It's a simple text input (not dropdown)
   - Has placeholder: "My Audience Builder Query"
   - Has description explaining Omeda Audience Builder

#### Test Validation
1. Enter an audience query name: `Newsletter Subscribers`
2. Save deployment type
3. Edit deployment type - value should be preserved

## Draft Deployment Testing (v1.5.0)

### Test Scenario: Create Deployment on Draft Save

#### Prerequisites
1. Configure API credentials (Settings page)
2. Create a deployment type with all required fields
3. Ensure deployment type is assigned to "Post" post type

#### Test Steps
1. Create a new post (Posts > Add New)
2. Add a title: "Test Newsletter Article"
3. Add some content
4. In the **Omeda Deployment** meta box:
   - Verify auto-detected deployment type is shown
   - Or manually select a deployment type
5. Click **Save Draft**

#### Expected Results
Immediately after saving:
1. Omeda Deployment meta box updates
2. Workflow Log appears showing:
   ```
   [INFO] Workflow Initiated: Post saved as draft with a Deployment Type.
   [INFO] Step 1/4 Complete: Deployment created with TrackID: XXX
   [INFO] Step 2/4 Complete: Audience assigned.
   [INFO] Step 3/4 Complete: Content and subject uploaded.
   [INFO] Initial setup complete. Deployment date set to [next hour] (temporary).
   [INFO] Date will be updated when post is published/scheduled.
   ```
3. TrackID is displayed (e.g., "Omeda TrackID: 12345")
4. Deployment type dropdown is now locked (disabled)

#### Verify in Omeda Dashboard
1. Log into Omeda platform
2. Navigate to Email Deployments
3. Find deployment by TrackID
4. Verify:
   - Deployment name matches post title
   - Subject matches subject format (with variables parsed)
   - Content includes post content
   - Audience is assigned
   - Deployment date is set to next nearest hour (temporary)

### Test Scenario: Update Draft Content

#### Prerequisites
- Complete "Create Deployment on Draft Save" test above

#### Test Steps
1. Edit the same post
2. Change the title to "Updated Test Article"
3. Modify content
4. Click **Save Draft**

#### Expected Results
1. Workflow Log shows new entry:
   ```
   [INFO] Content updated successfully in Omeda.
   ```
2. TrackID remains the same
3. No new deployment created

#### Verify in Omeda
1. Refresh Omeda dashboard
2. Find same deployment by TrackID
3. Verify:
   - Content is updated
   - Subject is updated (if it uses {post_title})
   - Audience remains assigned

### Test Scenario: Publish Post

#### Prerequisites
- Complete "Update Draft Content" test above

#### Test Steps
1. Edit the same post
2. Click **Publish**

#### Expected Results
1. Workflow Log shows finalization steps:
   ```
   [INFO] Post Published/Scheduled. Finalizing deployment...
   [INFO] Content updated successfully in Omeda.
   [INFO] Test email sent to: [configured test email]
   [INFO] Deployment successfully scheduled for: YYYY-MM-DD HH:MM (UTC).
   [INFO] Workflow Complete.
   ```

#### Verify in Omeda
1. Check Omeda dashboard
2. Deployment should be:
   - Status: Scheduled
   - Date: Set to actual publish date + delay (from settings)
   - Test sent to configured email address

### Test Scenario: Schedule Post for Future

#### Prerequisites
- Complete "Create Deployment on Draft Save" test above
- Or create a new post with deployment type

#### Test Steps
1. Create/edit post with deployment type
2. Set future publish date (e.g., tomorrow at 2:00 PM)
3. Click **Schedule**

#### Expected Results
1. Workflow Log shows:
   ```
   [INFO] Deployment successfully scheduled for: YYYY-MM-DD 14:00 (UTC).
   ```
2. Scheduled date matches WordPress post date (in UTC)

## Common Issues & Solutions

### Issue: "There has been a critical error on this website"

**Symptom**: Cannot access "Add New Deployment Type" page

**Causes**:
1. Missing API credentials
2. Network timeout to Omeda API
3. Invalid response from Omeda API

**Solution**:
1. Check debug.log for specific error
2. Verify API credentials in Settings
3. Test API connection:
   ```bash
   wp-env run cli wp eval "print_r(Omeda_Data_Manager::get_deployment_types(true));"
   ```

### Issue: Database Connection Refused

**Symptom**: `mysqli_real_connect(): (HY000/2002): Connection refused`

**Cause**: wp-env database container not running or network issue

**Solution**:
```bash
# Restart wp-env
wp-env stop
wp-env start

# Check database container
docker ps | grep mysql
```

### Issue: Deployment Not Created

**Symptom**: No TrackID after saving draft

**Debugging Steps**:
1. Check Workflow Log in meta box
2. Check debug.log for API errors
3. Verify deployment type configuration:
   - All required fields filled
   - Valid deployment type ID from Omeda
   - Valid audience query name

### Issue: Variables Not Parsing

**Symptom**: Subject shows `{post_title}` instead of actual title

**Cause**: Variable not recognized or post data missing

**Solution**:
1. Check variable name is correct (case-sensitive)
2. Verify variable is supported (see list above)
3. Check debug.log for parser errors

## Performance Testing

### Test Debouncing
1. Create post with deployment type
2. Rapidly save multiple times (5-10 times within 30 seconds)
3. Expected: Only ONE deployment created
4. Verify in Background Jobs that only one create job exists

### Test Async Processing
1. Save draft
2. Go to **Omeda Integration > Background Jobs**
3. Should see:
   - `omeda_async_create_deployment` - Scheduled for +5 minutes
4. Wait for job to process
5. Job should move to "Complete" status
6. Post meta should have TrackID

### Test WP-Cron Fallback
If Action Scheduler is not available:
1. Deployments should still work
2. Processing happens immediately (synchronous)
3. No jobs in Background Jobs page

## Test Matrix

| Test Case | Status | Notes |
|-----------|--------|-------|
| Default email settings save | ✓ | |
| Default emails prepopulate | ✓ | |
| Deployment types dropdown loads | ✓ | |
| Deployment types refresh works | ✓ | |
| Post type dropdown shows options | ✓ | |
| Auto-detection works | ✓ | |
| Audience query field accepts text | ✓ | |
| Subject variables parse correctly | ✓ | |
| Draft save creates deployment | ✓ | |
| TrackID stored in post meta | ✓ | |
| Content update works | ✓ | |
| Publish finalizes deployment | ✓ | |
| Schedule sets correct date | ✓ | |
| Debouncing prevents duplicates | ✓ | |
| Async processing works | ✓ | |
| Workflow logging accurate | ✓ | |

## Automated Testing Commands

### Test API Connection
```bash
wp-env run cli wp eval "
\$api = new Omeda_API_Client();
\$types = \$api->get_deployment_types();
print_r(\$types);
"
```

### Test Variable Parser
```bash
wp-env run cli wp eval "
\$post_id = 1; // Replace with actual post ID
\$format = '{post_title} - {site_name}';
\$result = Omeda_Variable_Parser::parse(\$format, \$post_id);
echo \$result;
"
```

### Test Deployment Type Detection
```bash
wp-env run cli wp eval "
\$post_id = 1; // Replace with actual post ID
\$config_id = Omeda_Deployment_Types::find_config_for_post(\$post_id);
echo 'Config ID: ' . \$config_id;
"
```

### Clear All Caches
```bash
wp-env run cli wp transient delete omeda_deployment_types_cache
```

### View All Deployment Types
```bash
wp-env run cli wp post list --post_type=omeda_deploy_type --fields=ID,post_title
```

## Security Testing

### Test Permission Checks
1. Log out
2. Try to access deployment types directly
3. Should be redirected to login

### Test Nonce Validation
1. Inspect meta box form
2. Verify nonce field present
3. Try to submit without nonce (should fail)

### Test Input Sanitization
1. Try to inject HTML/JavaScript in fields
2. All input should be escaped/sanitized
3. Check database values are clean

## Next Steps After Testing

1. **Document Issues**: Record any failures or unexpected behavior
2. **Performance Metrics**: Note any slow operations
3. **User Feedback**: Gather feedback on UI/UX
4. **API Limits**: Test with production API to check rate limits
5. **Edge Cases**: Test with unusual post types, long titles, etc.

## Support

For issues found during testing:
- Check debug.log first
- Review workflow logs in meta box
- Check Background Jobs page
- Document steps to reproduce
- Include relevant log excerpts
