# Troubleshooting: Workflow Not Triggering

## Issue Description
When saving a Newsletter Glue campaign with a deployment type assigned, the workflow logs do not populate and the deployment is not created in Omeda.

## Root Cause
The workflow hooks were only registered for the standard WordPress 'post' post type. Newsletter Glue uses a custom post type called `newsletterglue` for campaigns, which was not included in the supported post types list.

## Symptoms
- No workflow logs appear when saving a Newsletter Glue campaign
- Omeda meta box shows but workflow never triggers
- No TrackID is generated
- No deployment created in Omeda
- Regular WordPress posts work correctly

## Solution (Fixed in v1.6.1)
Updated `Omeda_Hooks::get_supported_post_types()` to include both post types:

```php
private function get_supported_post_types() {
    // Define which post types this integration supports
    // 'post' for regular posts, 'newsletterglue' for Newsletter Glue campaigns
    return ['post', 'newsletterglue'];
}
```

## How to Verify the Fix

### 1. Check Plugin Version
- Go to: Plugins → Installed Plugins
- Find "Omeda WordPress Integration"
- Verify version is 1.6.1 or higher

### 2. Test Newsletter Glue Campaign
1. Create or edit a Newsletter Glue campaign
2. In the sidebar, find the "Omeda Deployment" meta box
3. Select a deployment type
4. Save as draft
5. Check for workflow logs in the meta box
6. Look for: "Workflow Initiated: Post saved as draft..."

### 3. Check for TrackID
After saving draft, you should see:
- "Omeda TrackID: [number]" in the meta box
- Multiple log entries showing the 4-step process
- No error messages in red

### 4. Verify in Omeda
- Log into Omeda platform
- Navigate to your deployments
- Search for the TrackID shown in WordPress
- Confirm deployment exists with correct subject and content

## Debugging Steps (If Still Not Working)

### Check Post Type
Use WP-CLI to verify the post type:
```bash
wp-env run cli wp post get [POST_ID] --field=post_type
```

Expected output for Newsletter Glue: `newsletterglue`

### Check Available Post Types
```bash
wp-env run cli wp post-type list --fields=name,label
```

Look for:
- `newsletterglue` - Campaigns
- `ngl_template` - Templates
- `ngl_pattern` - Patterns

### Enable Debug Logging
In `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Check `wp-content/debug.log` for errors.

### View Raw Workflow Logs
Using WP-CLI:
```bash
wp-env run cli wp post meta get [POST_ID] _omeda_workflow_log
```

This shows the raw JSON log entries.

### Check Hook Registration
Add to a debugging plugin or theme's functions.php:
```php
add_action('init', function() {
    $hooks = new Omeda_Hooks(omeda_wp_integration()->workflow_manager);
    error_log('Supported post types: ' . print_r($hooks->get_supported_post_types(), true));
}, 999);
```

Check debug.log for: `Supported post types: Array ( [0] => post [1] => newsletterglue )`

## Related Files
- `src/omeda-newsletter-connector/includes/class-omeda-hooks.php` - Hook registration
- `src/omeda-newsletter-connector/includes/class-omeda-workflow-manager.php` - Workflow execution
- `wp-content/plugins/omeda-newsletter-connector/` - Active plugin directory

## Newsletter Glue Post Types
Newsletter Glue creates several custom post types:

| Post Type | Label | Purpose |
|-----------|-------|---------|
| `newsletterglue` | Campaigns | Email campaigns (main content) |
| `ngl_template` | Templates | Email templates |
| `ngl_pattern` | Patterns | Reusable content blocks |
| `ngl_automation` | Automated emails | Automated workflows |
| `ngl_log` | Email log | Send history |

**Note:** Currently only `newsletterglue` (Campaigns) and `post` (regular posts) are supported for Omeda integration.

## Common Mistakes

### 1. Using Template Instead of Campaign
Templates (`ngl_template`) are designs, not content. The workflow only triggers for:
- Regular Posts (`post`)
- Newsletter Glue Campaigns (`newsletterglue`)

### 2. Not Selecting Deployment Type
The workflow won't trigger if:
- No deployment type selected in meta box
- Selected "-- Do Not Deploy / Auto-Detect --"
- No matching deployment type configuration

### 3. Missing API Credentials
Check: Omeda Integration → Settings
Required fields:
- API App ID
- Brand Abbreviation
- Environment (Staging/Production)

### 4. Nonce Verification Failure
If you're editing the post via an automated script:
- Ensure the nonce field is present: `omeda_post_meta_nonce`
- Use proper nonce value: `wp_create_nonce('omeda_save_post_meta')`

## Expected Workflow Sequence

### Draft Save (First Time)
1. User selects deployment type
2. User saves post as draft
3. Hook triggers: `save_post` → `handle_post_save()`
4. Workflow manager: `create_and_assign_audience()`
5. Log: "Workflow Initiated: Post saved as draft..."
6. Step 1: Create deployment → TrackID generated
7. Step 2: Assign audience
8. Step 3: Send content and subject
9. Step 4: Initial setup complete

### Draft Save (Subsequent)
1. User edits post
2. User saves draft
3. Hook triggers: `save_post` → `handle_post_save()`
4. Workflow manager: `update_content()`
5. Log: "Content updated successfully in Omeda."

### Publish/Schedule
1. User publishes or schedules post
2. Hook triggers: `transition_post_status` → `handle_status_transition()`
3. Workflow manager: `schedule_and_send_test()`
4. Update content (latest version)
5. Send test email
6. Schedule deployment for publish date

## Contact & Support
- GitHub Issues: Report bugs with full error messages
- Include: WordPress version, PHP version, plugin version
- Provide: Post ID, post type, deployment type ID
- Attach: Debug log excerpt showing the issue

## Version History
- v1.6.1 (2025-10-29): Fixed - Added `newsletterglue` post type support
- v1.6.0 (2025-10-29): Only supported `post` post type
- v1.5.0 and earlier: Only supported `post` post type

---

**Last Updated:** 2025-10-29  
**Plugin Version:** 1.6.1+
