# Select2 Searchable Dropdowns Integration

**Version:** 1.2.0  
**Date:** 2025-10-29  
**Feature:** Enhanced UX with searchable dropdowns

## Overview

Version 1.2.0 introduces Select2 searchable dropdowns for the Deployment Type configuration screen, making it easier to find and select from long lists of deployment types and post types/templates.

## What Changed

### Before (v1.1.1)
- Standard HTML `<select>` dropdowns
- No search functionality
- Difficult to find items in long lists
- Basic dropdown styling

### After (v1.2.0)
- Select2-powered searchable dropdowns
- Real-time search/filter as you type
- Clear button to reset selection
- Professional styling
- Better keyboard navigation

## Implementation Details

### Files Modified

1. **class-omeda-deployment-types.php**
   - Added `enqueue_admin_assets()` method
   - Loads Select2 CSS and JS from CDN
   - Initializes Select2 on deployment type and post type dropdowns
   - Added placeholder support for text inputs

### Select2 Configuration

```php
public function enqueue_admin_assets($hook) {
    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== self::CPT_SLUG) {
        return;
    }
    
    // Enqueue Select2 from CDN
    wp_enqueue_style('select2', 
        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', 
        array(), '4.1.0');
    wp_enqueue_script('select2', 
        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', 
        array('jquery'), '4.1.0', true);
    
    // Initialize Select2
    wp_add_inline_script('select2', "
        jQuery(document).ready(function($) {
            $('#_omeda_deployment_type_id').select2({
                placeholder: '-- Select Deployment Type --',
                allowClear: true,
                width: '100%'
            });
            
            $('#_omeda_assigned_post_type').select2({
                placeholder: '-- Select Post Type / Template --',
                allowClear: true,
                width: '100%'
            });
        });
    ");
}
```

## Features

### Deployment Type Dropdown
- **Searchable**: Type to filter deployment types from Omeda API
- **Clear Button**: Reset selection with one click
- **Full Width**: Dropdown spans full available width
- **Placeholder**: Shows helpful placeholder text when empty

### Post Type / Template Dropdown
- **Searchable**: Type to filter through all post types and templates
- **Organized Groups**: Optgroups separate different types
  - Post Types
  - Newsletter Glue Enabled Post Types
  - Newsletter Glue Templates
  - Newsletter Glue Template Categories
- **Clear Button**: Reset selection easily
- **Full Width**: Matches deployment type dropdown width

## User Experience

### Search Functionality
1. Click dropdown to open
2. Start typing to filter options
3. Use arrow keys to navigate results
4. Press Enter or click to select
5. Click × to clear selection

### Keyboard Navigation
- **↓/↑**: Navigate options
- **Enter**: Select highlighted option
- **Esc**: Close dropdown
- **Backspace**: Delete search term
- **Clear (×)**: Reset selection

## Audience Query Field Enhancement

The Query Name field now includes:

```php
'query_name' => array(
    'label' => 'Audience Query Name', 
    'type' => 'text', 
    'required' => true,
    'desc' => 'Enter the exact name of your Audience Builder query in Omeda (case-sensitive). Example: "My Audience Builder Query"',
    'placeholder' => 'My Audience Builder Query'
),
```

### Why Not a Dropdown?

**Omeda API Design**: The Omeda API does not provide an endpoint to list available Audience Builder queries. Per the official API documentation:

- **Deployment Add Audience API** references queries by name string only
- Queries are created in Omeda's Audience Builder interface
- Query names must be entered exactly as configured in Omeda
- No programmatic query discovery available

### Best Practices

1. **Get Query Name from Omeda**: 
   - Log into your Omeda account
   - Navigate to Audience Builder
   - Copy the exact query name

2. **Case Sensitivity**: 
   - Query names are case-sensitive
   - Use exact spelling and spacing

3. **Test First**:
   - Test query in Omeda before adding to WordPress
   - Verify query returns expected audience

4. **Documentation**:
   - Keep list of query names for reference
   - Document query purposes and audiences

## Technical Notes

### CDN vs Local

**Why CDN?**
- Faster load times (likely cached by browser)
- Always up-to-date
- Reduced plugin file size
- Standard practice for popular libraries

**Future Consideration:**
- Could bundle locally if needed for offline dev
- Current implementation prioritizes production performance

### Browser Compatibility

Select2 4.1.0 supports:
- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- IE 11 (with polyfills)

### Performance

- Select2 only loads on deployment type edit screens
- No performance impact on frontend
- Minimal overhead (gzipped: ~20KB CSS + 80KB JS)
- Cached by browser after first load

## API Reference

### Omeda Deployment Type Lookup API

**Endpoint:**
```
GET https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/deploymenttypes/*
```

**Response Structure:**
```json
{
  "SubmissionId": "...",
  "Id": 3000,
  "Description": "Brand Name",
  "BrandAbbrev": "ABBREV",
  "DeploymentTypes": [
    {
      "Id": 2344,
      "Name": "Newsletter Name",
      "Description": "Newsletter Name",
      "StatusCode": 1
    }
  ]
}
```

**Status Codes:**
- `0` = Inactive (filtered out)
- `1` = Active (shown in dropdown)

### Omeda Audience Builder

**Reference:** [Email Deployment Add Audience API](https://knowledgebase.omeda.com/omedaclientkb/email-deployment-add-audience)

**Query Name Usage:**
```json
{
    "UserId": "omailAccount1",
    "TrackId": "FOO0102003002",
    "QueryName": "My Audience Builder Query",
    "OutputCriteria": "OmailOutput1",
    "SplitNumber": 1
}
```

**Important:**
- `QueryName` must match exactly as created in Audience Builder
- No API to list available queries
- Queries created/managed in Omeda interface only

## Testing

### Manual Testing Steps

1. **Test Deployment Type Dropdown**
   ```
   1. Navigate to Omeda Integration → Deployment Types
   2. Click "Add New"
   3. Click "Omeda Deployment Type" dropdown
   4. Verify Select2 interface loads
   5. Type search term
   6. Verify filtering works
   7. Select an option
   8. Click × to clear
   ```

2. **Test Post Type Dropdown**
   ```
   1. On same page, click "Assigned Post Type" dropdown
   2. Verify Select2 interface loads
   3. Verify optgroups are organized
   4. Test search functionality
   5. Verify Newsletter Glue items appear (if NG active)
   ```

3. **Test Query Name Field**
   ```
   1. Check for placeholder text
   2. Verify description shows helpful text
   3. Enter sample query name
   4. Save and verify persistence
   ```

### Expected Behavior

**With Valid API Credentials:**
- Deployment types populate from Omeda API
- Search filters results immediately
- Selections save correctly

**Without API Credentials:**
- Dropdown shows error message
- Error is clear and actionable
- Save still works (validation elsewhere)

**With Newsletter Glue Active:**
- NG templates appear in dropdown
- NG categories appear if taxonomy exists
- NG enabled post types show with label

**Without Newsletter Glue:**
- Only standard post types appear
- No NG-specific options
- Gracefully handles missing plugin

## Troubleshooting

### Select2 Not Loading

**Symptoms:**
- Dropdowns look like standard HTML selects
- No search functionality

**Solutions:**
1. Check browser console for errors
2. Verify CDN is accessible
3. Clear browser cache
4. Check for JavaScript conflicts

### Search Not Working

**Symptoms:**
- Dropdown opens but search doesn't filter
- Typing has no effect

**Solutions:**
1. Verify Select2 initialized correctly
2. Check for jQuery conflicts
3. Inspect element to verify Select2 classes applied

### Deployment Types Empty

**Symptoms:**
- Dropdown is searchable but has no options
- Shows "No deployment types found" message

**Solutions:**
1. Verify API credentials configured
2. Check API endpoint accessibility
3. Click "Refresh from Omeda" button
4. Check error logs for API issues

## Future Enhancements

### Potential v1.3.0 Features

1. **Query Name Autocomplete**
   - Store previously used query names
   - Suggest from history
   - Local storage for persistence

2. **Query Validation**
   - Test query against Omeda API before save
   - Show expected audience count
   - Validate output criteria compatibility

3. **Custom Select2 Styling**
   - Match WordPress admin theme
   - Custom colors and branding
   - Responsive enhancements

4. **Ajax Loading**
   - Load deployment types on-demand
   - Paginated results for large lists
   - Faster initial page load

## Related Documentation

- [Deployment Types API Fix](DEPLOYMENT_TYPES_API_FIX.md)
- [Newsletter Glue Integration](NEWSLETTER_GLUE_INTEGRATION.md)
- [Critical Error Fix](CRITICAL_ERROR_FIX.md)
- [Omeda API Documentation](../omeda_api_docs/)

## Version History

- **1.2.0** (2025-10-29): Select2 integration added
- **1.1.1** (2025-10-29): Deployment types API fixed
- **1.1.0** (2025-10-29): Newsletter Glue integration added
- **1.0.0** (2025-10-28): Initial release

---

**Last Updated:** 2025-10-29  
**Plugin Version:** 1.2.0  
**Select2 Version:** 4.1.0-rc.0
