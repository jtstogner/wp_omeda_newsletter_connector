# Quick Start Guide - Omeda WordPress Integration v1.3.0

## What's New in 1.3.0?

### ✨ WordPress Variables in Subject Lines
Create dynamic, personalized subject lines using post data, author info, and site details.

### 🎯 Enhanced Audience Query Configuration
Better guidance and interface for configuring Omeda Audience Builder queries.

## Quick Setup

### 1. Configure API Credentials
Navigate to: **Omeda Integration > Settings**

Required fields:
- API App ID
- Brand Abbreviation  
- Default User ID
- Environment (Staging/Production)

### 2. Create a Deployment Type
Navigate to: **Omeda Integration > Deployment Types > Add New**

Give it a name (e.g., "Weekly Newsletter")

### 3. Configure Deployment Settings

#### Required Fields:
- **Omeda Deployment Type**: Select from dropdown (fetched from Omeda API)
- **Assigned Post Type**: Choose which posts trigger this deployment
- **Audience Query**: Enter the exact name from Omeda Audience Builder
- **From Name**: Sender name (e.g., "My Tech Blog")
- **From Email**: Sender email (e.g., "news@mytechblog.com")
- **Reply To Email**: Where replies go

#### Subject Format (The New Feature! 🎉):
Use WordPress variables to create dynamic subjects:

**Simple:**
```
{post_title}
```

**With Branding:**
```
{post_title} - {site_name}
```

**With Date:**
```
{post_date_F} Newsletter: {post_title}
```

**With Author:**
```
{author_name}: {post_title}
```

**With Category:**
```
[{category}] {post_title}
```

### 4. Publish a Post
When you publish a post matching your deployment type, it will automatically:
1. Create an Omeda deployment
2. Parse WordPress variables in the subject
3. Schedule the email send
4. Track the deployment status

## Common Variables Reference

### Post Data
| Variable | Output Example |
|----------|----------------|
| `{post_title}` | "How to Improve Your SEO" |
| `{post_date}` | "October 29, 2025" |
| `{post_date_F}` | "October" |
| `{post_date_M}` | "Oct" |
| `{post_date_Y}` | "2025" |
| `{excerpt}` | "Learn the basics of..." |

### People
| Variable | Output Example |
|----------|----------------|
| `{author_name}` | "Jane Smith" |
| `{author_first_name}` | "Jane" |
| `{site_name}` | "My Tech Blog" |

### Categories & Tags
| Variable | Output Example |
|----------|----------------|
| `{category}` | "Technology" |
| `{categories}` | "Tech, News, Tips" |
| `{tags}` | "WordPress, SEO, Marketing" |

## Popular Subject Line Templates

### For Blogs
```
{post_title} | {site_name}
New from {author_name}: {post_title}
{category}: {post_title}
```

### For Newsletters
```
{post_date_F} {post_date_Y} Newsletter
{site_name} Weekly - {post_title}
📧 This Week: {post_title}
```

### For Updates
```
{post_date_M} {post_date_d} Update: {post_title}
[{category}] {post_title}
🔔 New: {post_title}
```

## Combining with Omeda Variables

WordPress variables work alongside Omeda merge tags:

```
{post_title} for @{recipient_first_name}@
{site_name} Newsletter - @{mv_html_title_subject}@
```

## Troubleshooting

### Variable Shows as {post_title}
- **Fix**: Check spelling - variables are case-sensitive
- **Fix**: Ensure curly braces are correct `{variable}` not `{{variable}}`

### Empty Subject
- **Fix**: Make sure the post has a title
- **Fix**: Verify the variable exists for that post type

### API Connection Issues
- **Check**: Settings > Omeda Integration > Test Connection
- **Verify**: API credentials are correct
- **Confirm**: Brand abbreviation matches Omeda account

### Deployment Type Not Triggering
- **Check**: Assigned Post Type matches your post
- **Verify**: Post is published (not draft)
- **Confirm**: API credentials are configured

## Advanced Features

### Custom Variables (Developers)
Add your own variables using WordPress filters:

```php
add_filter('omeda_parsed_variables', function($result, $template, $post_id) {
    $custom = get_post_meta($post_id, 'my_field', true);
    return str_replace('{my_custom}', $custom, $result);
}, 10, 3);
```

### Manual Deployment Trigger
In post editor sidebar: **Omeda Deployment** meta box
- View deployment status
- Manually trigger deployment
- Override default configuration

## Best Practices

### ✅ Do's
- Keep subjects under 60 characters for mobile
- Include your site name for brand recognition
- Test with sample posts first
- Use date variables for newsletters
- Combine multiple variables creatively

### ❌ Don'ts
- Don't use too many variables in one subject
- Don't rely on fields that might be empty
- Don't forget to test email preview
- Don't use special characters excessively

## Support Resources

### Documentation
- Full variable reference: `docs/WORDPRESS_VARIABLES.md`
- Release notes: `docs/RELEASE_1.3.0.md`
- Changelog: `CHANGELOG.md`

### Examples
Check `WORDPRESS_VARIABLES.md` for 30+ examples covering:
- All variable types
- Use case scenarios
- Best practices
- Troubleshooting guides

### Testing
1. Create a test post with all fields filled
2. Assign to a test deployment type
3. Check subject in Omeda dashboard
4. Verify variable replacement worked

## Version Information

**Current Version:** 1.3.0  
**Release Date:** October 29, 2025  
**Key Feature:** WordPress Variable Support  
**Compatibility:** WordPress 5.8+, PHP 7.4+

## Quick Command Reference

### Navigate to Settings
Dashboard > **Omeda Integration** > **Settings**

### Create Deployment Type
Dashboard > **Omeda Integration** > **Deployment Types** > **Add New**

### View Deployments
Dashboard > **Omeda Integration** > **Deployment Types**

### Test Connection
Dashboard > **Omeda Integration** > **Settings** > **Test Connection** button

## What's Next?

### In Your First Hour
1. Configure API credentials
2. Create one test deployment type
3. Use a simple subject: `{post_title} - {site_name}`
4. Publish a test post
5. Verify deployment in Omeda

### In Your First Day
1. Create deployment types for different categories
2. Experiment with different subject formats
3. Review deployment success in Omeda
4. Fine-tune subject lines based on results

### In Your First Week
1. Analyze open rates by subject format
2. Create templates for different content types
3. Train team on variable usage
4. Document your best-performing subjects

## Need Help?

1. **Check Documentation**: Start with `WORDPRESS_VARIABLES.md`
2. **Review Examples**: 30+ examples in docs
3. **Test Locally**: Use wp-env for safe testing
4. **Check Logs**: WordPress debug log for errors
5. **API Status**: Test connection in settings

## That's It! 🎉

You're now ready to create dynamic, personalized email deployments with Omeda and WordPress.

Start simple with `{post_title}` and expand from there!

---

**Quick Reference Card v1.3.0**  
**For:** Omeda WordPress Integration  
**Updated:** October 29, 2025
