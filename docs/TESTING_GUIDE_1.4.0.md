# Testing Guide - Version 1.4.0

**Version:** 1.4.0  
**Test Date:** October 29, 2025  
**Environment:** wp-env (localhost:8888)

## Overview

This guide covers testing procedures for the new features and changes in version 1.4.0, focusing on default email settings and the simplified audience query field.

## Prerequisites

- WordPress site with Omeda Newsletter Connector 1.4.0 installed
- Admin access (username: `admin`, password: `password` for wp-env)
- Omeda API credentials configured (optional for some tests)

## Test Environment Setup

```bash
# Start wp-env
cd /home/jts/development/NRS/Projects/wp_omeda_newsletter_connector
wp-env start

# Access WordPress admin
# URL: http://localhost:8888/wp-admin
# Username: admin
# Password: password
```

## Test Cases

### TC-1: Default Email Settings - Configuration

**Objective:** Verify that default email settings can be configured and saved.

**Steps:**
1. Log in to WordPress admin
2. Navigate to **Omeda Integration** → **Settings**
3. Scroll to the "Default Email Settings" section
4. Verify the following fields are present:
   - Default From Name
   - Default From Email
   - Default Reply To Email
5. Enter test values:
   - From Name: "Test Newsletter"
   - From Email: "newsletter@example.com"
   - Reply To: "replies@example.com"
6. Click "Save Settings"
7. Refresh the page

**Expected Results:**
- ✅ "Default Email Settings" section is visible
- ✅ All three fields are present
- ✅ Settings save successfully
- ✅ Values persist after page refresh
- ✅ Section includes helpful description text

**Status:** [ ] Pass [ ] Fail [ ] Blocked

---

### TC-2: Default Email Settings - New Deployment Type

**Objective:** Verify that new deployment types auto-populate with default email settings.

**Steps:**
1. Ensure default email settings are configured (see TC-1)
2. Navigate to **Omeda Integration** → **Deployment Types**
3. Click "Add New"
4. Check the following fields:
   - From Name
   - From Email
   - Reply To Email

**Expected Results:**
- ✅ From Name is pre-filled with "Test Newsletter"
- ✅ From Email is pre-filled with "newsletter@example.com"
- ✅ Reply To is pre-filled with "replies@example.com"
- ✅ Fields can still be edited/overridden
- ✅ Other fields (deployment type, post type, etc.) are empty

**Status:** [ ] Pass [ ] Fail [ ] Blocked

---

### TC-3: Default Email Settings - Existing Deployment Type

**Objective:** Verify that existing deployment types retain their saved values.

**Steps:**
1. Create a deployment type with custom email values:
   - From Name: "Custom Name"
   - From Email: "custom@example.com"
   - Reply To: "custom-reply@example.com"
2. Save the deployment type
3. Change the global default email settings
4. Re-open the existing deployment type

**Expected Results:**
- ✅ Existing deployment type keeps original values
- ✅ Values are NOT replaced with new defaults
- ✅ New deployment types use the new defaults
- ✅ No data loss occurs

**Status:** [ ] Pass [ ] Fail [ ] Blocked

---

### TC-4: Default Email Settings - Fallback Values

**Objective:** Verify fallback behavior when defaults are not configured.

**Steps:**
1. Delete/clear all default email settings
2. Create a new deployment type
3. Check the email fields

**Expected Results:**
- ✅ From Name falls back to site name (from `get_bloginfo('name')`)
- ✅ From Email falls back to admin email (from `get_bloginfo('admin_email')`)
- ✅ Reply To falls back to admin email (from `get_bloginfo('admin_email')`)
- ✅ No errors or empty fields

**Status:** [ ] Pass [ ] Fail [ ] Blocked

---

### TC-5: Audience Query Field - Simple Text Input

**Objective:** Verify that audience query is a simple text field.

**Steps:**
1. Navigate to **Omeda Integration** → **Deployment Types**
2. Click "Add New" or edit an existing deployment type
3. Locate the "Audience Query" field
4. Check the field type and appearance

**Expected Results:**
- ✅ Field is a standard text input (not a dropdown)
- ✅ Placeholder text shows "My Audience Builder Query"
- ✅ Description text is clear and helpful
- ✅ No Select2 dropdown functionality on this field
- ✅ Field accepts text input normally
- ✅ Value saves correctly

**Status:** [ ] Pass [ ] Fail [ ] Blocked

---

### TC-6: Select2 Functionality - Other Fields

**Objective:** Verify that Select2 still works for deployment type and post type fields.

**Steps:**
1. Navigate to **Omeda Integration** → **Deployment Types**
2. Click "Add New"
3. Click on "Omeda Deployment Type" dropdown
4. Click on "Assigned Post Type / Template" dropdown

**Expected Results:**
- ✅ Deployment Type dropdown has searchable Select2 interface
- ✅ Post Type dropdown has searchable Select2 interface
- ✅ Both dropdowns show search boxes
- ✅ Both dropdowns allow clearing selection
- ✅ Audience Query field does NOT have Select2 (is plain text input)

**Status:** [ ] Pass [ ] Fail [ ] Blocked

---

### TC-7: Plugin Version Update

**Objective:** Verify that plugin version has been updated correctly.

**Steps:**
1. Navigate to **Plugins** in WordPress admin
2. Find "Omeda WordPress Integration"
3. Check the version number
4. Or run: `wp-env run cli wp plugin list`

**Expected Results:**
- ✅ Version shows "1.4.0"
- ✅ Plugin is active
- ✅ No errors in plugin list

**Status:** [ ] Pass [ ] Fail [ ] Blocked

---

### TC-8: Full Deployment Type Creation Workflow

**Objective:** Test complete workflow for creating a deployment type with new features.

**Steps:**
1. Configure default email settings (TC-1)
2. Navigate to **Omeda Integration** → **Deployment Types**
3. Click "Add New"
4. Verify email fields are pre-populated
5. Enter deployment type details:
   - Title: "Test Deployment v1.4"
   - Omeda Deployment Type: Select from dropdown (or leave empty if no API)
   - Assigned Post Type: Select "Post"
   - Audience Query: Enter "Test Audience Query"
   - From Name: Verify pre-filled (or modify)
   - From Email: Verify pre-filled (or modify)
   - Reply To: Verify pre-filled (or modify)
   - Subject Format: "{post_title} - {site_name}"
6. Click "Publish"

**Expected Results:**
- ✅ All fields save correctly
- ✅ Email fields use defaults or custom values as entered
- ✅ Audience query saves as text
- ✅ Deployment type appears in list
- ✅ Can re-open and edit
- ✅ All values persist

**Status:** [ ] Pass [ ] Fail [ ] Blocked

---

### TC-9: Backward Compatibility

**Objective:** Verify no breaking changes from version 1.3.0.

**Steps:**
1. If upgrading from 1.3.0, check existing deployment types
2. Verify all saved data is intact
3. Check that existing configurations still work

**Expected Results:**
- ✅ All existing deployment types load correctly
- ✅ No data loss
- ✅ No errors in WordPress debug log
- ✅ Existing workflows continue to function
- ✅ No database migration required

**Status:** [ ] Pass [ ] Fail [ ] Blocked

---

## Browser Testing

Test in the following browsers:
- [ ] Chrome/Chromium
- [ ] Firefox
- [ ] Safari
- [ ] Edge

## Performance Testing

- [ ] Settings page loads in < 2 seconds
- [ ] Deployment type creation page loads in < 2 seconds
- [ ] No JavaScript errors in console
- [ ] No PHP warnings/errors in debug log

## Edge Cases

### EC-1: Empty Defaults
- [ ] New deployment type with no defaults set uses site/admin fallbacks
- [ ] No errors occur

### EC-2: Invalid Email Format
- [ ] Invalid emails in defaults show validation errors
- [ ] Form prevents saving with invalid emails

### EC-3: Very Long Input
- [ ] From Name with 255+ characters handles gracefully
- [ ] Email with invalid format rejected

### EC-4: Special Characters
- [ ] From Name with special characters (émails, ñews) saves correctly
- [ ] UTF-8 characters handled properly

## Regression Testing

Verify these features from previous versions still work:

- [ ] Omeda deployment type dropdown populates from API
- [ ] Post type dropdown shows all available options
- [ ] Newsletter Glue templates appear if plugin is active
- [ ] Refresh button for deployment types works
- [ ] Subject format supports WordPress variables
- [ ] Variable parser processes {post_title}, {site_name}, etc.
- [ ] Action Scheduler integration functions
- [ ] WP-Cron fallback works

## Test Results Summary

| Test Case | Status | Notes |
|-----------|--------|-------|
| TC-1 | ⬜ | |
| TC-2 | ⬜ | |
| TC-3 | ⬜ | |
| TC-4 | ⬜ | |
| TC-5 | ⬜ | |
| TC-6 | ⬜ | |
| TC-7 | ⬜ | |
| TC-8 | ⬜ | |
| TC-9 | ⬜ | |

**Overall Status:** ⬜ Pass ⬜ Fail ⬜ In Progress

## Issues Found

| ID | Description | Severity | Status |
|----|-------------|----------|--------|
| - | - | - | - |

## Test Environment Details

```
WordPress Version: 6.7.x
PHP Version: 8.x
MySQL Version: 8.x
Plugin Version: 1.4.0
Newsletter Glue: 4.0.3.3
Browser: [Fill in]
OS: [Fill in]
```

## Notes

- Test both with and without Omeda API credentials configured
- Test with Newsletter Glue both active and inactive
- Check WordPress debug.log for any warnings/errors
- Verify database queries are efficient (no N+1 queries)

## Sign-Off

**Tester:** ___________________  
**Date:** ___________________  
**Result:** ⬜ Approved ⬜ Rejected ⬜ Needs Review

---

**Next Steps After Testing:**
1. Document any issues found
2. Create tickets for bugs
3. Update release notes if needed
4. Approve for production deployment
