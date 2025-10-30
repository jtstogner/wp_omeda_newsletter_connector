# Version 1.9.0 - Deployment Name and Campaign ID Formats

## Release Date
2025-10-29

## Overview
This release adds configurable formats for deployment names and campaign IDs in the Omeda WordPress Integration plugin. Users can now define custom naming conventions using WordPress variables at the deployment type level, with per-post override capabilities.

## Key Features

### 1. Deployment Name Format
- **Location**: Deployment Type settings
- **Purpose**: Define how deployment names appear in Omeda
- **Default**: `{post_title} - {post_date}`
- **Variables Supported**:
  - `{post_id}` - Numeric post ID
  - `{post_slug}` - Post slug/permalink  
  - `{post_title}` - Post title
  - `{post_date}` - Formatted post date
  - `{post_date_ymd}` - Date as YYYYMMDD
  - `{site_name}` - Website name
  - `{author_name}` - Author display name
  - All other existing WordPress variables

### 2. Campaign ID Format
- **Location**: Deployment Type settings
- **Purpose**: Generate consistent campaign IDs for tracking
- **Default**: `campaign-{post_id}-{post_date_ymd}`
- **Variables Supported**: Same as deployment name format
- **Benefit**: WordPress-controlled IDs for better analytics integration

### 3. Per-Post Overrides
- **Location**: Newsletter Glue metabox on post edit screen
- **Deployment Name Field**: Override default format with custom name
- **Campaign ID Field**: Override default format with custom ID
- **Smart Placeholders**: Shows what will be generated based on format
- **Locked After Creation**: Fields become read-only after deployment created

### 4. New WordPress Variables
Added three new variables to the parser:
- `{post_id}` - Post ID number
- `{post_slug}` - Post URL slug
- `{post_date_ymd}` - Compact date format (20251029)

## Technical Implementation

### Files Modified

1. **class-omeda-deployment-types.php**
   - Added `deployment_name_format` field to deployment type settings
   - Added `campaign_id_format` field to deployment type settings
   - Updated `get_configuration()` to include new fields in API config
   - Fields show with helpful descriptions and variable examples

2. **class-omeda-hooks.php**
   - Enhanced `render_meta_box()` to display format fields in UI
   - Added smart placeholders showing parsed variable values
   - Implemented save logic for override meta values
   - Fields locked (readonly) after deployment created
   - Saves to `_omeda_deployment_name` and `_omeda_campaign_id` post meta

3. **class-omeda-variable-parser.php**
   - Added `{post_id}` variable support
   - Added `{post_slug}` variable support
   - Added `{post_date_ymd}` variable for YYYYMMDD format
   - Updated `get_available_variables()` documentation

4. **class-omeda-workflow-manager.php**
   - Updated deployment creation logic to check overrides first
   - Falls back to format parsing if no override exists
   - Falls back to post title if no format configured
   - Includes campaign ID in API calls when configured

5. **omeda-wp-integration.php**
   - Updated plugin version from 1.8.0 to 1.9.0

6. **CHANGELOG.md**
   - Added comprehensive version 1.9.0 entry
   - Documented all changes, additions, and technical details

## User Interface Changes

### Deployment Type Settings Page
New fields appear when creating or editing deployment types:

```
Deployment Name Format
────────────────────────────────────────────────────
[{post_title} - {post_date}                        ]
Format for the deployment name in Omeda. Use variables
like {post_title}, {post_date}, {post_id}, {site_name},
{author_name}.

Campaign ID Format
────────────────────────────────────────────────────
[campaign-{post_id}-{post_date_ymd}                ]
Format for the campaign ID. Use variables like 
{post_id}, {post_slug}, {post_date_ymd}.
```

### Newsletter Glue Metabox
New fields appear on post edit screen:

```
Deployment Name:
────────────────────────────────────────────────────
[                                                   ]
Placeholder: My Article - October 29, 2025
Leave empty to use: {post_title} - {post_date}

Campaign ID:
────────────────────────────────────────────────────
[                                                   ]
Placeholder: campaign-112-20251029
Leave empty to use: campaign-{post_id}-{post_date_ymd}
```

## Usage Examples

### Example 1: Standard Newsletter
**Deployment Type Config:**
- Name Format: `{site_name} Newsletter - {post_date_F} {post_date_Y}`
- Campaign ID: `newsletter-{post_date_ymd}`

**Result:**
- Name: "My Website Newsletter - October 2025"
- Campaign ID: "newsletter-20251029"

### Example 2: Author-Specific
**Deployment Type Config:**
- Name Format: `{author_name}: {post_title}`
- Campaign ID: `author-{post_id}`

**Result:**
- Name: "John Doe: Weekly Update"
- Campaign ID: "author-112"

### Example 3: Category-Based
**Deployment Type Config:**
- Name Format: `{category} - {post_title} ({post_date_ymd})`
- Campaign ID: `{post_slug}`

**Result:**
- Name: "Tech News - New Features Released (20251029)"
- Campaign ID: "new-features-released"

### Example 4: Per-Post Override
**Deployment Type:** Uses default format
**Post Override:** Manual entry in metabox
- Name: "SPECIAL EDITION: Black Friday Sale"
- Campaign ID: "bf-2025-special"

**Result:**
- Uses custom values instead of format
- Format only used when fields left empty

## Migration Guide

### For Existing Sites
1. **No action required** - existing deployments work as before
2. **Optional enhancement** - add formats to deployment types
3. **Format defaults** - empty formats use post title (backward compatible)
4. **No database changes** - pure metadata addition

### Adding Formats to Existing Deployment Types
1. Go to Omeda Integration → Deployment Types
2. Edit an existing deployment type
3. Add format to "Deployment Name Format" field
4. Add format to "Campaign ID Format" field
5. Save changes
6. **Only affects new deployments** - existing ones unchanged

### Testing New Formats
1. Create test deployment type with formats
2. Create new post/newsletter using that type
3. Check placeholder values in metabox
4. Save draft
5. Verify deployment name in Omeda dashboard
6. Override values if needed for specific post

## Benefits

### For Content Teams
- Consistent naming across all deployments
- Easy identification of deployment purpose
- Date-based organization built-in
- Author tracking in deployment names

### For Marketing Teams
- Predictable campaign IDs for analytics
- Better tracking across platforms
- URL-safe campaign identifiers
- Flexible naming for different campaigns

### For Developers
- Variable-based system is extensible
- Clean override pattern
- No breaking changes to existing code
- Format inheritance from type to post

### For System Administrators
- Centralized format configuration
- Per-post override capability
- Smart placeholder system prevents errors
- Clear documentation in UI

## Testing Recommendations

### Test 1: Basic Format
1. Create deployment type with format: `{post_title} - {post_date}`
2. Create new post
3. Verify placeholder shows parsed value
4. Save draft
5. Check Omeda for deployment name

### Test 2: Variable Parsing
1. Test each variable type:
   - `{post_id}` → numeric ID
   - `{post_slug}` → URL slug
   - `{post_date_ymd}` → YYYYMMDD format
   - `{author_name}` → author display name
2. Verify all parse correctly

### Test 3: Override Behavior
1. Leave fields empty → uses format
2. Enter custom value → uses custom value
3. After deployment created → fields readonly
4. Can't change after deployment exists

### Test 4: Empty Format
1. Create deployment type with no format
2. Create post
3. Verify deployment name = post title (backward compatible)
4. Verify campaign ID not sent (Omeda generates)

### Test 5: Special Characters
1. Test format with Unicode characters
2. Test campaign ID with URL-unsafe characters
3. Verify proper encoding/escaping
4. Check Omeda accepts values

## API Integration

### Omeda API Fields
The plugin now sends these additional fields:

```json
{
  "DeploymentName": "Parsed from format or override",
  "CampaignId": "Parsed from format or override",
  ...other fields...
}
```

### Field Priority
1. **First**: Check for post meta override (`_omeda_deployment_name`)
2. **Second**: Parse format from deployment type config
3. **Third**: Fall back to post title (deployment name only)

### Campaign ID Behavior
- If format configured: WordPress generates ID
- If no format: Omeda auto-generates ID
- If override set: Uses override value
- Empty string treated as "no format"

## Troubleshooting

### Deployment name not using format
- **Check**: Deployment type has format configured
- **Check**: Post meta not overriding (delete `_omeda_deployment_name` meta)
- **Check**: Variables parsing correctly (test in parser)

### Campaign ID not appearing in Omeda
- **Check**: Format configured in deployment type
- **Check**: API response includes CampaignId field
- **Check**: Omeda accepts custom campaign IDs

### Variables not parsing
- **Check**: Variable name spelled correctly (case-sensitive)
- **Check**: Curly braces present: `{post_id}` not `post_id`
- **Check**: Post data exists (e.g., post has author for `{author_name}`)

### Override not saving
- **Check**: Post type supported (post, newsletterglue)
- **Check**: Nonce verification passing
- **Check**: User has edit_post capability
- **Check**: Not auto-save event

## Documentation

### Internal Documentation
- Field descriptions in deployment type settings
- Placeholder text in post metabox
- Format examples in UI
- CHANGELOG.md entry

### Developer Documentation
- Variable parser class fully documented
- Filter hooks available: `omeda_parsed_variables`
- Example formats in comments
- Integration guide (this document)

## Future Enhancements

### Potential 1.9.x Features
- Variable preview live in admin
- Format validation before save
- Common format presets/templates
- Import/export deployment type configs

### Potential 2.0 Features
- Conditional variables (if/else logic)
- Custom variable registration API
- Variable transformation functions
- Multi-language variable support

## Version History Context

### Previous Version (1.8.0)
- Focused on workflow execution reliability
- Made deployment creation synchronous
- Fixed Action Scheduler queue issues

### Current Version (1.9.0)
- Adds deployment naming flexibility
- Enhances campaign ID control
- Improves content organization

### Next Version (1.10.0 planned)
- Content template system
- Enhanced scheduling controls
- Deployment analytics integration

## Support

### For Questions
- Check variable documentation in `class-omeda-variable-parser.php`
- Review examples in this document
- Test with development deployment types first

### For Issues
- Check workflow logs for parsing errors
- Verify Omeda API accepts generated names/IDs
- Test variable parsing in isolation

### For Customization
- Use `omeda_parsed_variables` filter to add custom variables
- Extend variable parser class if needed
- Create custom deployment type templates

## Conclusion

Version 1.9.0 provides flexible, variable-based deployment naming while maintaining full backward compatibility. The format system is intuitive for users, extensible for developers, and integrates seamlessly with the existing workflow.

Key achievements:
- ✅ No breaking changes
- ✅ Backward compatible defaults
- ✅ Intuitive UI with smart placeholders
- ✅ Per-post override capability
- ✅ Comprehensive documentation
- ✅ Clean code architecture

The feature is production-ready and can be rolled out immediately.
