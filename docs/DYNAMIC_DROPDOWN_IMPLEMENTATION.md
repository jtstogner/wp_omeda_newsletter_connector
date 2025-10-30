# Dynamic Deployment Type Selection - Implementation Complete

**Date:** 2025-10-29  
**Status:** ✅ IMPLEMENTED

## What Was Added

### 1. Omeda Deployment Type Dropdown ✅

**Feature:** When creating/editing a deployment type configuration, you now see a dropdown of actual deployment types from your Omeda brand.

**Implementation:**
- Fetches deployment types from Omeda API via `Omeda_Data_Manager::get_deployment_types()`
- Caches results for 24 hours using WordPress Transients
- "Refresh from Omeda" button to force cache refresh
- Shows deployment type name and ID in dropdown
- Error handling for API failures

**Location:** Deployment Types admin page → Add/Edit Deployment Type

### 2. Post Type / Template Assignment Dropdown ✅

**Feature:** Assign each deployment type to a specific trigger:
- **Post Types** (Post, Page, Custom Post Types)
- **Newsletter Glue Templates** (if Newsletter Glue is active)
- **Newsletter Glue Story Types/Categories** (if available)

**Implementation:**
- Dropdown with optgroups for organization
- Detects Newsletter Glue plugin and queries its templates
- Searches for Newsletter Glue taxonomies (categories/story types)
- Stores selection in format: `post_type:post`, `ng_template:123`, `ng_category:456`

### 3. Automatic Deployment Type Detection ✅

**Feature:** When you save a post, the plugin automatically detects which deployment type to use based on:
1. **Manual selection** in post meta box (highest priority)
2. **Post type match** (e.g., all "Post" types use Config A)
3. **Newsletter Glue template** (posts from Template X use Config B)
4. **Newsletter Glue category** (posts in Story Type Y use Config C)

**Implementation:**
- New method: `Omeda_Deployment_Types::find_config_for_post($post_id)`
- Checks explicit assignment first, then auto-detects
- Updates `handle_post_save()` to use auto-detection
- Meta box shows auto-detected config with info notice

---

## How It Works

### Configuration Workflow

```
Admin creates Deployment Type Config:
    ↓
1. Select Omeda Deployment Type from dropdown
   (Shows: "Daily Newsletter (ID: 12345)")
    ↓
2. Select Assigned Post Type/Template
   Options:
   - Post Types: Post, Page, etc.
   - Newsletter Glue Templates: "Weekly Digest", etc.
   - Newsletter Glue Story Types: "Breaking News", etc.
    ↓
3. Configure other settings (From Name, Email, etc.)
    ↓
4. Save
```

### Automatic Triggering

```
User creates/edits a post:
    ↓
Plugin checks:
    1. Is there a manual deployment type selection?
       YES → Use that
       NO → Continue
    ↓
    2. Does post type match any config?
       (e.g., post type = "post" matches Config A)
       YES → Use Config A
       NO → Continue
    ↓
    3. Was post created from Newsletter Glue template?
       (Check meta: _ng_template_id)
       YES → Use matching config
       NO → Continue
    ↓
    4. Does post have Newsletter Glue category?
       (Check taxonomy terms)
       YES → Use matching config
       NO → No deployment triggered
```

---

## Files Modified

### 1. class-omeda-deployment-types.php
**Changes:**
- Updated `get_fields()` - Added dropdown field types
- New `render_omeda_deployment_dropdown()` - Fetches from API
- New `render_post_type_dropdown()` - Lists post types and NG templates
- New `get_newsletter_glue_templates()` - Queries NG templates
- New `get_newsletter_glue_categories()` - Queries NG taxonomies
- New `handle_cache_refresh()` - Handles manual refresh
- Updated `get_configuration()` - Includes AssignedPostType
- New `find_config_for_post()` - Auto-detection logic

### 2. class-omeda-hooks.php
**Changes:**
- Updated `render_meta_box()` - Shows auto-detected config
- Updated `handle_post_save()` - Calls auto-detection if no manual selection

### 3. class-omeda-data-manager.php
**Existing:** Already had `get_deployment_types()` method - used as-is

### 4. class-omeda-api-client.php
**Existing:** Already had `get_deployment_types()` method - used as-is

---

## Testing in wp-env

### Test 1: View Omeda Deployment Types Dropdown

```bash
# Access: http://localhost:8888/wp-admin
# Navigate to: Omeda Integration → Deployment Types → Add New
# Expected: Dropdown shows deployment types from Omeda API
```

### Test 2: Select Post Type Assignment

```bash
# In same screen, find "Assigned Post Type / Template" dropdown
# Expected: 
#   - Post Types section (Post, Page, etc.)
#   - Newsletter Glue Templates (if plugin active)
#   - Newsletter Glue Story Types (if available)
```

### Test 3: Auto-Detection

```bash
# Create deployment type config:
#   - Omeda Type: "Test Newsletter"
#   - Assigned Post Type: "Post"
#   - Save
# 
# Create new post
# Expected: Meta box shows "Auto-detected: This post type is configured to use: Test Newsletter"
```

---

## Newsletter Glue Integration

### Template Detection

The plugin looks for:
- **Post Type:** `newsletterglue` (standard NG template posts)
- **Post Meta:** `_ng_template_id` (template used to create post)

### Category/Story Type Detection

The plugin searches for taxonomies:
- `newsletter_glue_category` (primary check)
- `ng_category` (fallback)

**Note:** Actual Newsletter Glue structure may vary. The code includes fallbacks and will simply show empty optgroups if NG isn't active or uses different structure.

---

## API Structure

### Omeda Deployment Types Response
```json
[
    {
        "Id": 12345,
        "Name": "Daily Newsletter"
    },
    {
        "Id": 67890,
        "Name": "Weekly Digest"
    }
]
```

### Caching
- **Key:** `omeda_deployment_types_cache`
- **Duration:** 24 hours (DAY_IN_SECONDS)
- **Refresh:** Manual via "Refresh from Omeda" button

---

## Configuration Storage

### Post Meta Structure
```
_omeda_deployment_type_id: "12345"
_omeda_assigned_post_type: "post_type:post"
                         OR "ng_template:123"
                         OR "ng_category:456"
_omeda_query_name: "Audience Query Name"
_omeda_from_name: "Sender Name"
_omeda_from_email: "sender@example.com"
_omeda_reply_to: "reply@example.com"
_omeda_subject_format: "@{mv_html_title_subject}@"
_omeda_mailbox_name: "newsletters"
_omeda_output_criteria: "Newsletter_Member_id"
```

---

## Benefits

### For Users
✅ No manual ID entry - select from dropdown  
✅ See actual deployment type names from Omeda  
✅ Automatic deployment triggering based on post type  
✅ Newsletter Glue integration for templates  
✅ Manual override always available

### For Admins
✅ Configure once per post type/template  
✅ Clear mapping between WP and Omeda  
✅ Easy to update if Omeda types change  
✅ Cache refresh on demand

### For Developers
✅ Extensible framework  
✅ Clean separation of concerns  
✅ Error handling for API failures  
✅ Fallback for missing data

---

## Next Steps

1. **Test with Real Omeda API:**
   - Configure actual API credentials
   - Verify deployment types load
   - Test refresh functionality

2. **Newsletter Glue Investigation:**
   - Install/activate Newsletter Glue Pro
   - Verify template detection
   - Check actual taxonomy names
   - Adjust code if structure differs

3. **Production Deployment:**
   - Test all post types
   - Verify auto-detection
   - Confirm override behavior

---

**Status:** Feature complete and ready for testing with real Omeda API credentials and Newsletter Glue integration.
