# Quick Reference - Omeda WordPress Integration

## Version 1.5.0 | October 29, 2025

---

## Quick Commands

### View Debug Log
```bash
tail -f wp-content/debug.log
```

### Clear Cache
```bash
wp-env run cli wp transient delete omeda_deployment_types_cache
```

### Test API Connection
```bash
wp-env run cli wp eval "print_r((new Omeda_API_Client())->get_deployment_types());"
```

### List Deployment Types
```bash
wp-env run cli wp post list --post_type=omeda_deploy_type
```

---

## Admin URLs

| Page | URL |
|------|-----|
| Settings | `/wp-admin/admin.php?page=omeda-integration` |
| Deployment Types | `/wp-admin/edit.php?post_type=omeda_deploy_type` |
| Add Deployment Type | `/wp-admin/post-new.php?post_type=omeda_deploy_type` |
| Background Jobs | `/wp-admin/admin.php?page=action-scheduler` |

---

## WordPress Variables

Use these in the "Subject Format" field:

```
{post_title}           Post title
{post_date}            Formatted date
{post_date_Y}          Year (2025)
{post_date_m}          Month (10)
{post_date_d}          Day (29)
{post_date_F}          Month name (October)
{post_date_M}          Short month (Oct)
{author_name}          John Doe
{author_first_name}    John
{author_last_name}     Doe
{site_name}            Site title
{site_tagline}         Site description
{category}             First category
{categories}           All categories (comma-separated)
{tags}                 All tags (comma-separated)
{excerpt}              Post excerpt (100 chars)
```

### Examples
```
{post_title} - {site_name}
→ "My Article - My Website"

{post_date_F} {post_date_d}: {post_title}
→ "October 29: My Article"

{post_title} by {author_name}
→ "My Article by John Doe"
```

---

## Configuration Options

### Global Settings
```php
omeda_app_id                    // API credentials
omeda_brand_abbreviation        // Brand code (e.g., MTGMCD)
omeda_environment               // 'production' or 'staging'
omeda_default_user_id           // Default owner/approver
omeda_default_mailbox           // Default mailbox name
omeda_default_output_criteria   // Default output criteria
omeda_publish_delay             // Minutes (default: 30)
omeda_default_from_name         // Default sender name
omeda_default_from_email        // Default sender email
omeda_default_reply_to          // Default reply-to email
```

### Post Meta
```php
_omeda_config_id                // Deployment type ID
_omeda_track_id                 // Omeda TrackID
_omeda_workflow_log             // JSON array of logs
```

---

## Workflow Steps

### Draft Save (NEW in v1.5.0)
1. ✓ Create deployment (next hour date)
2. ✓ Assign audience
3. ✓ Upload content & subject
4. ✓ Store TrackID

### Publish
1. ✓ Update content
2. ✓ Send test email
3. ✓ Update deployment date
4. ✓ Schedule in Omeda

### Schedule for Future
1. ✓ Update content
2. ✓ Send test email
3. ✓ Set exact publish date
4. ✓ Schedule in Omeda

---

## Deployment Type Assignment

```php
post_type:post              // All posts
post_type:page              // All pages
ng_post_type:post           // NG-enabled posts only
ng_template:123             // Specific NG template
ng_category:1               // NG template category
```

---

## API Endpoints

### Base URLs
```
Production: https://ows.omeda.com/webservices/rest/brand/{brand}/
Staging:    https://ows.omedastaging.com/webservices/rest/brand/{brand}/
```

### Endpoints
```
GET  deploymenttypes/*                                      List types
POST omail/deployment                                       Create
POST omail/deployment/{trackId}/assignaudience              Assign audience
POST omail/deployment/{trackId}/addcontent                  Upload content
POST omail/deployment/{trackId}/sendtest                    Send test
POST omail/deployment/{trackId}/schedule                    Schedule
GET  omail/deployment/lookup/{trackId}/*                    Check status
```

---

## Deployment Date Logic

### Draft Save (v1.5.0)
```php
$current_time = current_time('timestamp');
$next_hour = ceil($current_time / 3600) * 3600;
$date = gmdate('Y-m-d H:i', $next_hour);

// Example: Saved at 2:30 PM → Date set to 3:00 PM
```

### Publish (Immediate)
```php
$delay = get_option('omeda_publish_delay', 30); // minutes
$date = gmdate('Y-m-d H:i', time() + ($delay * 60));
```

### Publish (Scheduled)
```php
$date = get_gmt_from_date($post->post_date, 'Y-m-d H:i');
```

---

## Error Handling

### Check Logs
```php
$logs = get_post_meta($post_id, '_omeda_workflow_log');
foreach ($logs as $log_json) {
    $log = json_decode($log_json, true);
    echo $log['level'] . ': ' . $log['message'];
}
```

### Common Errors
| Error | Cause | Solution |
|-------|-------|----------|
| `Class "Omeda_Data_Manager" not found` | Missing include | Check plugin activation |
| `API Credentials...are missing` | No settings | Configure API settings |
| `Connection refused` | Database down | Restart wp-env |
| `Invalid response` | API error | Check API credentials |

---

## Hooks & Filters

### Actions
```php
omeda_after_deployment_created      // After TrackID received
omeda_after_audience_assigned       // After audience assigned
omeda_after_content_updated         // After content uploaded
omeda_after_deployment_scheduled    // After final schedule
```

### Filters
```php
omeda_parsed_variables              // Add custom variables
omeda_deployment_config             // Modify deployment config
omeda_content_html                  // Modify HTML before send
omeda_subject_line                  // Modify subject line
```

---

## Class Reference

### Main Classes
```php
Omeda_WP_Integration         // Main singleton
Omeda_API_Client             // API communication
Omeda_Settings               // Settings management
Omeda_Deployment_Types       // CPT management
Omeda_Workflow_Manager       // Deployment workflow
Omeda_Variable_Parser        // Variable parsing
Omeda_Hooks                  // WordPress hooks
Omeda_Async_Jobs             // Async processing
Omeda_Data_Manager           // API data caching
```

### Static Methods
```php
// Get deployment configuration
Omeda_Deployment_Types::get_configuration($config_id)

// Find config for post
Omeda_Deployment_Types::find_config_for_post($post_id)

// Get deployment types from API
Omeda_Data_Manager::get_deployment_types($force_refresh = false)

// Parse variables
Omeda_Variable_Parser::parse($format, $post_id)
```

---

## Debugging Tips

### Enable Debug Mode
```php
// wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### View Workflow Log
1. Edit post with deployment
2. Check "Omeda Deployment" meta box
3. Scroll to "Workflow Log" section
4. Color-coded by severity:
   - **Green**: Success
   - **Orange**: Warning
   - **Red**: Error
   - **Black**: Info

### Check Background Jobs
1. Go to: **Omeda Integration > Background Jobs**
2. Look for `omeda_async_*` actions
3. Status:
   - **Pending**: Scheduled to run
   - **In Progress**: Currently running
   - **Complete**: Successfully finished
   - **Failed**: Error occurred

### Test Deployment
```php
// Create test deployment
$workflow = omeda_wp_integration()->workflow_manager;
$workflow->create_and_assign_audience($post_id, $config_id);

// Update content
$workflow->update_content($post_id, $track_id, $config_id);

// Finalize
$workflow->schedule_and_send_test($post_id, $track_id, $config_id);
```

---

## Performance

### Caching
- Deployment types: 24 hours
- Cache key: `omeda_deployment_types_cache`
- Manual refresh: Click "Refresh from Omeda" button

### Debouncing
- Create deployment: 5 minutes
- Update content: 1 minute
- Prevents duplicate API calls on rapid saves

### Async Processing
- Preferred: Action Scheduler
- Fallback: WP-Cron
- Detection: `function_exists('as_schedule_single_action')`

---

## Security Checklist

- [ ] API credentials stored securely
- [ ] Nonces on all forms
- [ ] Input sanitization
- [ ] Output escaping
- [ ] Capability checks
- [ ] HTTPS only connections
- [ ] Error messages sanitized

---

## Browser Console (for troubleshooting)

### Check Select2 Initialization
```javascript
console.log(jQuery('#_omeda_deployment_type_id').data('select2'));
console.log(jQuery('#_omeda_assigned_post_type').data('select2'));
```

### Force Refresh
```javascript
location.href = location.href + '&omeda_refresh=1';
```

---

## Version History

| Version | Date | Key Feature |
|---------|------|-------------|
| 1.5.0 | 2025-10-29 | Draft deployment creation |
| 1.4.0 | 2025-10-29 | Default email settings |
| 1.3.0 | 2025-10-29 | WordPress variables |
| 1.2.0 | 2025-10-29 | Select2 integration |
| 1.1.0 | 2025-10-29 | Newsletter Glue |
| 1.0.0 | 2025-10-28 | Initial release |

---

## Support

- Documentation: `/docs/` directory
- Testing Guide: `/docs/TESTING_GUIDE.md`
- Implementation: `/docs/IMPLEMENTATION_SUMMARY.md`
- Changelog: `/CHANGELOG.md`

---

**Last Updated:** October 29, 2025  
**Current Version:** 1.5.0  
**Status:** Production Ready
