# Version 1.1.0 Release Summary

**Release Date:** 2025-10-29  
**Previous Version:** 1.0.0  
**Current Version:** 1.1.0  
**Type:** Minor Release (Features + Bug Fixes)

---

## Critical Bug Fix

### Issue: Fatal Error on Add New Deployment Type Page
**Severity:** CRITICAL  
**Impact:** Users could not create new deployment type configurations

**Root Cause:**
```
Fatal error: Class "Omeda_Data_Manager" not found
```

The `class-omeda-data-manager.php` file was created but never included in the main plugin file.

**Fix Applied:**
```php
// Added to omeda-wp-integration.php line 25
require_once OMEDA_WP_PLUGIN_DIR . 'includes/class-omeda-data-manager.php';
```

**Status:** ✅ FIXED - Page now loads successfully

---

## Additional Error Handling Improvements

### API Credentials Missing
**Before:** Fatal exception crash  
**After:** Graceful error message

**Implementation:**
- Added `Throwable` catch block in `Omeda_Data_Manager::get_deployment_types()`
- Added try-catch wrapper in `Omeda_Deployment_Types::render_omeda_deployment_dropdown()`
- Error message displayed: "Error: API Credentials, Brand Abbreviation, or Default User ID are missing."

**User Experience:**
- ✅ Page loads without crash
- ✅ Clear error message shown
- ✅ Other fields remain editable
- ✅ Can save configuration even without API credentials

---

## New Features

### 1. Newsletter Glue Integration
**Status:** COMPLETE

Based on source code analysis of Newsletter Glue Pro v4.0.3.3:

**Post Types Detected:**
- `ngl_template` - Newsletter templates
- `ngl_pattern` - Reusable patterns
- `ngl_automation` - Automated newsletters

**Taxonomy Detected:**
- `ngl_template_category` - Template categories

**Assignment Types Available:**
1. **Regular Post Type** (`post_type:post`)
   - Triggers for all posts of that type
   - Works with or without Newsletter Glue

2. **NG-Enabled Post Type** (`ng_post_type:post`)
   - Only triggers for posts sent via Newsletter Glue
   - Checks for `newsletterglue_did_sent` or `newsletterglue_to_send` meta
   - Best for mixed usage sites

3. **NG Template** (`ng_template:123`)
   - Triggers for specific Newsletter Glue template
   - Different templates can have different Omeda configs

4. **NG Category** (`ng_category:1`)
   - Triggers for all templates in a category
   - Group multiple templates with same deployment needs

### 2. Omeda Deployment Types Dropdown
**Status:** COMPLETE

**Features:**
- Fetches deployment types from Omeda API
- 24-hour caching to reduce API calls
- Manual refresh button
- Error handling for missing credentials
- Shows deployment type ID and name

**Behavior:**
- With credentials: Populates from API
- Without credentials: Shows error message
- Cached: Loads instantly from transient

### 3. Auto-Detection System
**Status:** TESTED & WORKING

**Test Results:**
```
Post ID: 10
Auto-detected Config: 4 ✅
Config Title: Test Newsletter Deployment
```

**Priority Order:**
1. Manual selection (highest)
2. Post type match
3. NG post type match
4. NG template match
5. NG category match

---

## Files Modified

### Core Files
1. **omeda-wp-integration.php**
   - Version: 1.0.0 → 1.1.0
   - Added: `class-omeda-data-manager.php` include ⭐ CRITICAL FIX
   - Updated: Version constant

2. **includes/class-omeda-data-manager.php**
   - Added: Throwable catch block
   - Improved: Error handling for API failures

3. **includes/class-omeda-deployment-types.php**
   - Added: `get_newsletter_glue_templates()` method
   - Added: `get_newsletter_glue_categories()` method
   - Added: `get_newsletter_glue_post_types()` method
   - Updated: `render_post_type_dropdown()` with NG integration
   - Updated: `find_config_for_post()` with NG matching
   - Fixed: Error handling in `render_omeda_deployment_dropdown()`

### Documentation Files
1. **CHANGELOG.md** (NEW)
   - Complete version history
   - Semantic versioning guide
   - Migration notes

2. **docs/project/NEWSLETTER_GLUE_INTEGRATION.md** (NEW)
   - Complete NG integration documentation
   - Use cases and examples
   - Testing checklist
   - 360+ lines

3. **docs/project/CRITICAL_ERROR_FIX.md** (NEW)
   - Error resolution guide
   - Root cause analysis
   - Fix explanation

---

## Testing Checklist

### Pre-Release Tests
- [x] Plugin activates without errors
- [x] Version shows as 1.1.0
- [x] Classes load correctly
- [x] Add New Deployment Type page loads
- [x] Deployment type can be created via CLI
- [x] Auto-detection works
- [x] Action Scheduler integration works

### User Testing Required
- [ ] Visit: http://localhost:8889/wp-admin/post-new.php?post_type=omeda_deploy_type
- [ ] Verify: Page loads without error
- [ ] Verify: Deployment type dropdown shows error (no credentials)
- [ ] Verify: Post type dropdown shows options
- [ ] Verify: Newsletter Glue options appear (if NG active)
- [ ] Verify: Can save deployment type configuration
- [ ] Add API credentials in settings
- [ ] Verify: Deployment types populate from Omeda
- [ ] Verify: Refresh button works

---

## Semantic Versioning Guide

Going forward, version updates will follow this pattern:

### Major Version (X.0.0)
**When to increment:**
- New feature sets
- Breaking changes
- Major architectural updates
- Database schema changes

**Examples:**
- Adding multi-brand support
- Changing API structure
- Removing deprecated features

### Minor Version (1.X.0)
**When to increment:**
- New features
- Enhancements to existing features
- Non-breaking changes
- New integrations

**Examples:**
- Newsletter Glue integration (this release)
- Adding deployment analytics
- New dropdown options
- Enhanced logging

### Patch Version (1.1.X)
**When to increment:**
- Bug fixes
- Minor improvements
- Documentation updates
- Performance optimizations

**Examples:**
- Fixing dropdown display issues
- Correcting error messages
- Updating documentation
- Cache optimization

---

## Rollback Plan

If issues arise:

1. **Deactivate Plugin:**
   ```
   wp plugin deactivate omeda-newsletter-connector
   ```

2. **Restore Previous Version:**
   ```
   # Checkout v1.0.0 from git
   git checkout v1.0.0 -- src/omeda-newsletter-connector/
   ```

3. **Reactivate:**
   ```
   wp plugin activate omeda-newsletter-connector
   ```

**Data Safety:** No database migrations in this release, safe to rollback.

---

## Next Steps

1. **Test in Browser:**
   - Visit deployment type creation page
   - Verify error handling
   - Test Newsletter Glue dropdowns

2. **Configure API Credentials:**
   - Add valid Omeda credentials in Settings
   - Test deployment type fetching
   - Verify caching works

3. **Production Deployment:**
   - Set `WP_ENV=production` in wp-config.php
   - Verify WP-Cron is used (not Action Scheduler)
   - Test with real Omeda account

4. **Monitor:**
   - Check error logs
   - Verify deployments create successfully
   - Monitor API call frequency

---

**Release Status:** ✅ Ready for Testing  
**Breaking Changes:** None  
**Migration Required:** No  
**Backward Compatible:** Yes

---

## Quick Reference

**Current Version:** 1.1.0  
**Plugin Name:** Omeda WordPress Integration  
**Main File:** omeda-wp-integration.php  
**Test URL:** http://localhost:8889/wp-admin/post-new.php?post_type=omeda_deploy_type  
**Credentials:** admin / password
