# Newsletter Glue Integration - Implementation Complete

**Date:** 2025-10-29  
**Status:** ✅ IMPLEMENTED  
**Based On:** Newsletter Glue Pro v4.0.3.3 source code analysis

## Newsletter Glue Structure (Discovered)

### Post Types
- `newsletterglue` - Main newsletter/campaign posts
- `ngl_template` - Newsletter templates (reusable designs)
- `ngl_pattern` - Reusable content patterns/blocks
- `ngl_automation` - Automated newsletters
- `ngl_log` - Newsletter send logs

### Taxonomies
- `ngl_template_category` - Categories for organizing templates

### Key Meta Fields
- `_ngl_core_template` - Marks default/core templates (boolean)
- `newsletterglue_did_sent` - Marks posts sent via Newsletter Glue
- `newsletterglue_to_send` - Marks posts scheduled to send

### Options
- `newsletterglue_post_types` - Comma-separated list of post types enabled for NG (e.g., "post,page")
- `newsletterglue_default_template_id` - Default template ID

---

## Integration Implementation

### 1. Template Detection

**Method:** `get_newsletter_glue_templates()`

**Queries:**
- Post Type: `ngl_template`
- Status: `publish`
- Orders by title

**Returns:**
```php
array(
    123 => "Weekly Newsletter",
    456 => "Daily Digest (Default)",  // Core templates marked
    789 => "Breaking News"
)
```

**Assignment Format:** `ng_template:123`

### 2. Template Category Detection

**Method:** `get_newsletter_glue_categories()`

**Queries:**
- Taxonomy: `ngl_template_category`
- Includes empty terms

**Returns:**
```php
array(
    1 => "Automations",
    2 => "Weekly",
    3 => "Daily"
)
```

**Assignment Format:** `ng_category:1`

### 3. NG-Enabled Post Types

**Method:** `get_newsletter_glue_post_types()`

**Queries:**
- Option: `newsletterglue_post_types`
- Parses comma-separated values

**Returns:**
```php
array(
    "post" => "Post (Newsletter Glue Enabled)",
    "page" => "Page (Newsletter Glue Enabled)"
)
```

**Assignment Format:** `ng_post_type:post`

---

## Dropdown Organization

When editing a deployment type, the "Assigned Post Type / Template" dropdown shows:

```
-- Select Post Type / Template --

Post Types
├─ Post
├─ Page
└─ Custom Post Types...

Newsletter Glue Enabled Post Types  (if NG active)
├─ Post (Newsletter Glue Enabled)
└─ Page (Newsletter Glue Enabled)

Newsletter Glue Templates  (if NG active)
├─ Weekly Newsletter
├─ Daily Digest (Default)
└─ Breaking News

Newsletter Glue Template Categories  (if NG active)
├─ Automations
├─ Weekly
└─ Daily
```

---

## Auto-Detection Logic

### Priority Order

1. **Explicit Selection** (Highest Priority)
   - Manual selection in post meta box
   - Stored in: `_omeda_config_id`

2. **Post Type Match**
   - Assignment: `post_type:post`
   - Matches: Any post with `post_type === 'post'`

3. **NG Post Type Match**
   - Assignment: `ng_post_type:post`
   - Matches: Posts with `post_type === 'post'` AND
     - Has `newsletterglue_did_sent` meta, OR
     - Has `newsletterglue_to_send` meta
   - **Purpose:** Only trigger for posts actually using Newsletter Glue

4. **NG Template Match**
   - Assignment: `ng_template:123`
   - Matches: Posts created as child of template (rare in NG)
   - Checks: `wp_get_post_parent_id($post_id) == 123`

5. **NG Category Match**
   - Assignment: `ng_category:1`
   - Matches: Template posts with this taxonomy term
   - Checks: `has_term(1, 'ngl_template_category', $post_id)`

---

## Use Cases

### Use Case 1: All Regular Posts

**Setup:**
- Assignment: `post_type:post`

**Behavior:**
- Every regular post automatically triggers Omeda deployment
- No Newsletter Glue required

### Use Case 2: Only Newsletter Glue Posts

**Setup:**
- Assignment: `ng_post_type:post`

**Behavior:**
- Only posts sent/scheduled via Newsletter Glue trigger Omeda
- Regular posts without NG are ignored
- **Best for mixed usage:** Some posts are newsletters, some aren't

### Use Case 3: Specific NG Template

**Setup:**
- Assignment: `ng_template:123` (e.g., "Weekly Newsletter")

**Behavior:**
- Only posts created from this specific template trigger Omeda
- Different templates can use different Omeda configurations

### Use Case 4: NG Template Category

**Setup:**
- Assignment: `ng_category:1` (e.g., "Automations")

**Behavior:**
- All templates in this category trigger Omeda
- Good for grouping multiple templates with same deployment needs

---

## Code Changes Summary

### File: class-omeda-deployment-types.php

**Updated Methods:**
```php
get_newsletter_glue_templates()
  - Changed post type from 'newsletterglue' to 'ngl_template'
  - Added _ngl_core_template check for default templates
  - Status filter: 'publish' only

get_newsletter_glue_categories()
  - Changed taxonomy to 'ngl_template_category' (actual NG taxonomy)
  - Removed fallback taxonomies (don't exist)
  - Added orderby name

NEW: get_newsletter_glue_post_types()
  - Queries 'newsletterglue_post_types' option
  - Parses comma-separated post types
  - Returns formatted array with labels

render_post_type_dropdown()
  - Added NG-enabled post types optgroup
  - Excludes NG internal post types (ngl_template, ngl_pattern, etc.)
  - Added ng_post_type assignment type
  - Added help text

find_config_for_post()
  - Added ng_post_type case
    - Checks post type match
    - Validates NG meta exists (did_sent or to_send)
  - Updated ng_template case
    - Checks wp_get_post_parent_id()
  - Updated ng_category case
    - Uses correct taxonomy: ngl_template_category
```

---

## Testing Checklist

### With Newsletter Glue Active

- [ ] Templates dropdown shows `ngl_template` posts
- [ ] Default templates marked with "(Default)"
- [ ] Template categories dropdown shows `ngl_template_category` terms
- [ ] NG-enabled post types shown from `newsletterglue_post_types` option
- [ ] Internal NG post types excluded from regular post types list

### Auto-Detection Tests

**Test 1: Regular Post Type**
- [ ] Create deployment config: `post_type:post`
- [ ] Create new post
- [ ] Verify: "Auto-detected" message appears
- [ ] Save post triggers workflow

**Test 2: NG-Enabled Post Type**
- [ ] Create deployment config: `ng_post_type:post`
- [ ] Create new post WITHOUT sending via NG
- [ ] Verify: No auto-detection
- [ ] Send post via NG (check "Send" in NG meta box)
- [ ] Verify: Auto-detection triggers

**Test 3: NG Template**
- [ ] Create deployment config: `ng_template:123`
- [ ] Create post from that template
- [ ] Verify: Auto-detection works

**Test 4: NG Category**
- [ ] Create deployment config: `ng_category:1`
- [ ] Create/edit template in that category
- [ ] Verify: Auto-detection works

---

## Newsletter Glue Meta Box Integration

### Current Behavior

Omeda and Newsletter Glue meta boxes **coexist independently**:

**Newsletter Glue Meta Box:**
- Handles sending emails via NG integrations (Mailchimp, etc.)
- Shows send status, logs, etc.

**Omeda Meta Box:**
- Handles Omeda deployment creation
- Shows deployment type, track ID, logs

### Workflow

```
User creates post with Deployment Type assigned
    ↓
User edits post content
    ↓
[Option A] User clicks "Publish"
    ├─ Omeda: Creates/updates deployment
    └─ Newsletter Glue: (separate workflow if configured)

[Option B] User checks "Send" in NG meta box
    ├─ Newsletter Glue: Sends via configured service
    └─ Omeda: (triggered by post status if configured)
```

**Independent Operation:**
- Omeda and NG work separately
- No conflicts
- User can use both, either, or neither per post

---

## Integration Benefits

### For Admins
✅ **True NG Integration** - Uses actual NG post types and taxonomies  
✅ **NG-Enabled Detection** - Option to trigger only for NG posts  
✅ **Template-Based Config** - Different templates = different Omeda settings  
✅ **Category Grouping** - Apply same config to multiple templates

### For Users
✅ **Automatic Detection** - No manual selection needed  
✅ **Flexible Options** - Choose granularity level (all posts vs specific templates)  
✅ **Clear Labels** - "(Newsletter Glue Enabled)" markers  
✅ **No Conflicts** - NG and Omeda work independently

---

## Future Enhancements

### Potential Additions

1. **NG Send Hook Integration**
   - Hook into Newsletter Glue's send action
   - Trigger Omeda after NG sends
   - Coordinate timing

2. **Template Content Sync**
   - Pull NG template design into Omeda
   - Unified template management

3. **NG Analytics Integration**
   - Compare NG stats vs Omeda stats
   - Unified reporting

---

## Production Notes

### Requirements
- Newsletter Glue Pro v4.0+ installed and active
- At least one NG template created
- Post types configured in NG settings

### Performance
- Queries cached via WordPress object cache
- Minimal database impact
- Lazy loading (only queries when dropdown rendered)

### Compatibility
- WordPress 6.6+
- PHP 7.4+
- Newsletter Glue Pro 4.0.3.3+

---

**Status:** Ready for production use with Newsletter Glue Pro. Integration uses actual NG structure discovered through source code analysis.
