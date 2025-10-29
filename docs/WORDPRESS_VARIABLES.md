# WordPress Variable Support in Omeda Integration

## Overview

Version 1.3.0 introduces WordPress variable support for deployment configuration fields, particularly the Subject Format. This allows you to create dynamic, personalized subject lines that incorporate post data, author information, and site details.

## Subject Format Variables

### Post Information

| Variable | Description | Example Output |
|----------|-------------|----------------|
| `{post_title}` | The post title | "10 Tips for Better SEO" |
| `{post_date}` | Formatted post date | "October 29, 2025" |
| `{post_date_Y}` | Year (4 digits) | "2025" |
| `{post_date_y}` | Year (2 digits) | "25" |
| `{post_date_m}` | Month (2 digits) | "10" |
| `{post_date_d}` | Day (2 digits) | "29" |
| `{post_date_F}` | Full month name | "October" |
| `{post_date_M}` | Short month name | "Oct" |
| `{post_date_j}` | Day without leading zero | "29" |
| `{post_date_n}` | Month without leading zero | "10" |
| `{excerpt}` | Post excerpt (max 100 chars) | "Learn how to improve your..." |

### Author Information

| Variable | Description | Example Output |
|----------|-------------|----------------|
| `{author_name}` | Author's display name | "John Doe" |
| `{author_first_name}` | Author's first name | "John" |
| `{author_last_name}` | Author's last name | "Doe" |

### Site Information

| Variable | Description | Example Output |
|----------|-------------|----------------|
| `{site_name}` | Website name | "My Technology Blog" |
| `{site_tagline}` | Site description/tagline | "Latest Tech News" |

### Taxonomy Information

| Variable | Description | Example Output |
|----------|-------------|----------------|
| `{category}` | Primary category name | "Technology" |
| `{categories}` | All categories (comma-separated) | "Technology, Programming, Web" |
| `{tags}` | All tags (comma-separated) | "PHP, WordPress, Development" |

## Usage Examples

### Basic Examples

```
{post_title}
```
**Result:** "10 Tips for Better SEO"

```
{post_title} - {site_name}
```
**Result:** "10 Tips for Better SEO - My Technology Blog"

```
New Post: {post_title}
```
**Result:** "New Post: 10 Tips for Better SEO"

### Date-Based Subject Lines

```
{post_date_F} {post_date_Y} Newsletter: {post_title}
```
**Result:** "October 2025 Newsletter: 10 Tips for Better SEO"

```
{post_date_M} {post_date_d} - {post_title}
```
**Result:** "Oct 29 - 10 Tips for Better SEO"

### Author-Focused Subject Lines

```
{author_name}: {post_title}
```
**Result:** "John Doe: 10 Tips for Better SEO"

```
Article by {author_first_name} - {post_title}
```
**Result:** "Article by John - 10 Tips for Better SEO"

### Category-Based Subject Lines

```
{category}: {post_title}
```
**Result:** "Technology: 10 Tips for Better SEO"

```
[{category}] {post_title} | {site_name}
```
**Result:** "[Technology] 10 Tips for Better SEO | My Technology Blog"

### Complex Examples

```
📧 {site_name} | {post_date_F} {post_date_Y}: {post_title}
```
**Result:** "📧 My Technology Blog | October 2025: 10 Tips for Better SEO"

```
{author_first_name} from {site_name}: {post_title} ({category})
```
**Result:** "John from My Technology Blog: 10 Tips for Better SEO (Technology)"

## Combining with Omeda Merge Tags

WordPress variables can be combined with Omeda's native merge tags:

```
{post_title} - @{mv_html_title_subject}@
```

```
{post_date_F} Newsletter for @{recipient_first_name}@
```

```
{site_name}: {post_title} | View Online: @{{view_online_url}}@
```

## Best Practices

### 1. Keep Subject Lines Concise
- Aim for 50-60 characters for optimal inbox display
- Use shorter variables when possible
- Test on mobile devices

```
✅ Good: {post_title} - {site_name}
❌ Too Long: {site_tagline} presents {author_first_name} {author_last_name}'s latest article: {post_title} in {categories}
```

### 2. Provide Context
- Include site name for brand recognition
- Add category for content type identification

```
{site_name}: {post_title}
[{category}] {post_title}
```

### 3. Use Fallback Values
- If a variable is empty, the plugin handles it gracefully
- Always test with posts that may have missing data

### 4. Consider Personalization
- Combine WordPress and Omeda variables
- Use author information for byline recognition

```
New from {author_name}: {post_title}
```

### 5. Date Formatting
- Use full month names (`{post_date_F}`) for newsletters
- Use short dates (`{post_date_M} {post_date_d}`) for updates

## Configuration

### Setting Up Subject Format

1. Navigate to **Omeda Integration > Deployment Types**
2. Create or edit a deployment type
3. In the **Subject Format** field, enter your template with variables
4. Example: `{post_title} - {site_name}`
5. Save the deployment type

### Testing Variables

To test how variables will be replaced:

1. Create a test post with all relevant fields filled
2. Ensure author, categories, and tags are set
3. Trigger a deployment (or use test mode)
4. Check the generated subject line in Omeda

## Advanced Usage

### Custom Variable Processing

Developers can filter variable replacements using the `omeda_parsed_variables` filter:

```php
add_filter('omeda_parsed_variables', function($result, $template, $post_id, $replacements) {
    // Add custom variables
    $result = str_replace('{custom_field}', get_post_meta($post_id, 'my_custom_field', true), $result);
    return $result;
}, 10, 4);
```

### Available Variables List

To get a programmatic list of available variables:

```php
$variables = Omeda_Variable_Parser::get_available_variables();
// Returns array of variable => description pairs
```

### Parsing Variables Manually

```php
$subject = Omeda_Variable_Parser::parse('{post_title} - {site_name}', $post_id);
```

## Troubleshooting

### Variable Not Replaced

**Problem:** Variable shows as `{post_title}` in the subject line

**Solutions:**
1. Check variable spelling (case-sensitive)
2. Ensure post has the required field
3. Verify post ID is valid
4. Check for typos (curly braces must match)

### Empty Values

**Problem:** Variable is replaced with empty string

**Solutions:**
1. Verify the post has the required field set
2. For author fields, check user profile is complete
3. For taxonomy fields, ensure categories/tags are assigned
4. Use fallback pattern: `{post_title}` (if empty, falls back to deployment name)

### Special Characters

**Problem:** Special characters appear incorrectly

**Solutions:**
1. Variables are HTML-safe by default
2. Avoid using special characters in variable values
3. Test with Omeda's email preview feature

## Migration from Static Subjects

### Before (Static)
```
Weekly Newsletter
```

### After (Dynamic)
```
{post_title} - {site_name}
```

### Migration Steps
1. Edit existing deployment types
2. Update Subject Format field
3. Test with sample posts
4. Monitor first few deployments
5. Adjust based on results

## Examples by Use Case

### Daily Digest
```
{post_date_F} {post_date_d}: Today's Top Story
```

### Weekly Newsletter
```
{site_name} Weekly - {post_date_F} {post_date_d}, {post_date_Y}
```

### Breaking News
```
🚨 Breaking: {post_title}
```

### Author Series
```
{author_name}'s Corner: {post_title}
```

### Category Digest
```
{category} Update: {post_title}
```

## Performance Notes

- Variable parsing occurs once per deployment
- Parsed values are cached in the deployment configuration
- No performance impact on regular WordPress operations
- Minimal overhead (< 10ms per parsing operation)

## Security

- All variables are sanitized before replacement
- HTML entities are properly encoded
- No user input is directly interpolated
- Variables cannot execute code

## Future Enhancements

Planned for future versions:

- Custom field variable support
- Conditional variable display
- Variable formatting options
- Multi-language variable support
- Variable preview in admin UI

## Support

For questions or issues:
- Check variable spelling and syntax
- Test with a simple variable first (`{post_title}`)
- Review the CHANGELOG for version-specific notes
- Contact support with specific examples

---

**Version:** 1.3.0  
**Last Updated:** 2025-10-29
