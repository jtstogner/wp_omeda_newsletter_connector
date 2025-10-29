# Release Notes - Version 1.4.0

**Release Date:** October 29, 2025  
**Type:** Minor Release (Feature Addition)

## Overview

Version 1.4.0 introduces default email settings and improves the user experience when creating deployment types. This release focuses on streamlining the deployment type creation workflow and reducing repetitive data entry.

## What's New

### Default Email Settings

A new "Default Email Settings" section has been added to the main Omeda Integration settings page. This allows administrators to configure default values that will be used when creating new deployment types.

**New Settings:**
- **Default From Name**: The sender name for emails (defaults to your site name)
- **Default From Email**: The sender email address (defaults to your admin email)
- **Default Reply To Email**: The reply-to email address (defaults to your admin email)

**How It Works:**
1. Go to **Omeda Integration** → **Settings**
2. Configure your default email settings in the "Default Email Settings" section
3. Save settings
4. When creating a new deployment type, these fields will be automatically populated
5. You can still override these defaults for specific deployment types

### Improved Audience Query Field

The Audience Query field has been simplified for better usability:

**Changes:**
- Converted from searchable dropdown back to simple text input
- Added clear placeholder text: "My Audience Builder Query"
- Enhanced description with better instructions
- Removed unnecessary Select2 JavaScript for this field

**Why This Change:**
Since Omeda doesn't provide an API to list audience queries, the searchable dropdown wasn't providing value. A simple text field with clear instructions is more appropriate for manually entering query names from the Omeda Audience Builder.

## Technical Details

### Files Modified

1. **class-omeda-settings.php**
   - Added `omeda_email_defaults_section` settings section
   - Added three new settings: `omeda_default_from_name`, `omeda_default_from_email`, `omeda_default_reply_to`
   - Added `render_email_field()` method for email input validation
   - Added `email_defaults_section_callback()` for section description

2. **class-omeda-deployment-types.php**
   - Updated `render_settings_meta_box()` to auto-populate email fields for new deployment types
   - Checks post status (`auto-draft`) to determine when to use defaults
   - Removed Select2 initialization for audience query field
   - Simplified audience query to standard text input
   - Updated field definition for audience query with better placeholder and description

3. **omeda-wp-integration.php**
   - Version bumped from 1.3.0 to 1.4.0
   - Updated `OMEDA_WP_VERSION` constant

4. **CHANGELOG.md**
   - Added comprehensive version 1.4.0 entry
   - Documented all changes and technical details

### Database Changes

**New Options:**
- `omeda_default_from_name` (string)
- `omeda_default_from_email` (string, email)
- `omeda_default_reply_to` (string, email)

**Migration Required:** No  
**Backward Compatible:** Yes

## User Impact

### For Administrators

**Benefits:**
- Faster deployment type creation with pre-filled email fields
- Consistent email settings across all deployment types
- Easy to update defaults globally
- Less repetitive data entry

**What To Do:**
1. Update the plugin to version 1.4.0
2. Navigate to **Omeda Integration** → **Settings**
3. Scroll to the "Default Email Settings" section
4. Configure your default From Name, From Email, and Reply To values
5. Click "Save Settings"
6. Create new deployment types and verify fields are auto-populated

### For End Users

No visible changes for end users. This is an administrative improvement.

## Testing Checklist

- [x] Settings page displays new "Default Email Settings" section
- [x] Default values can be saved and retrieved
- [x] New deployment types auto-populate email fields from defaults
- [x] Existing deployment types retain their saved values
- [x] Empty defaults fall back to site name/admin email
- [x] Audience query field works as simple text input
- [x] Select2 still works for deployment type and post type dropdowns
- [x] Version number updated in plugin header and constant
- [x] CHANGELOG.md updated with release notes

## Known Issues

None identified.

## Upgrade Instructions

### From 1.3.0 to 1.4.0

1. **Backup**: Always backup your database before updating
2. **Update Plugin Files**: Replace plugin files with version 1.4.0
3. **Configure Defaults**: Set your default email settings in **Omeda Integration** → **Settings**
4. **Test**: Create a new deployment type to verify defaults are applied
5. **Verify Existing**: Check that existing deployment types retain their original values

**No database migration required** - This release only adds new options.

## API Compatibility

- Omeda API: No changes
- WordPress: Compatible with WordPress 5.8+
- PHP: Compatible with PHP 7.4+

## Support

For questions or issues:
- Check the plugin documentation
- Review the Omeda API documentation at `/docs/omeda_api_docs/`
- Contact your Omeda Account Manager for Audience Builder questions

## Next Steps

After updating to 1.4.0:

1. **Configure Default Email Settings**
   - Set your standard From Name, From Email, and Reply To
   - These will be used for all new deployment types

2. **Review Existing Deployment Types**
   - Verify that existing deployment types still have correct email settings
   - Update any that need changes

3. **Test Deployment Creation**
   - Create a new deployment type
   - Confirm that email fields are pre-populated
   - Override defaults if needed for specific deployments

4. **Update Documentation**
   - Update any internal documentation about deployment type creation
   - Note the new default settings feature

## Credits

**Developed by:** Josh Stogner  
**Version:** 1.4.0  
**Release Date:** October 29, 2025

---

## Detailed Change Log

### Added
- Default email settings section on main settings page
- Three new global options for email defaults
- Auto-population of email fields for new deployment types
- Email validation for default email fields

### Changed
- Audience query field simplified to text input
- Removed Select2 from audience query field
- Version bumped to 1.4.0
- Updated CHANGELOG with release notes

### Technical
- Added `render_email_field()` method
- Added `email_defaults_section_callback()` method
- Enhanced `render_settings_meta_box()` with default value logic
- Simplified `enqueue_admin_assets()` by removing audience query selector
- Updated field definitions for audience query

---

**For Previous Releases:** See [CHANGELOG.md](../CHANGELOG.md)
