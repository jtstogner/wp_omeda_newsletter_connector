# Quick Reference: Deployment Name & Campaign ID Formats

## What's New in Version 1.9.0

You can now customize how your email deployments are named in Omeda using WordPress variables.

---

## Available Variables

### Post Information
| Variable | Example Output | Description |
|----------|----------------|-------------|
| `{post_id}` | `112` | Post ID number |
| `{post_slug}` | `halloween-special` | URL-friendly post slug |
| `{post_title}` | `Halloween Newsletter` | Post title |
| `{post_date}` | `October 29, 2025` | Formatted post date |
| `{post_date_Y}` | `2025` | 4-digit year |
| `{post_date_y}` | `25` | 2-digit year |
| `{post_date_m}` | `10` | Month with leading zero |
| `{post_date_d}` | `29` | Day with leading zero |
| `{post_date_ymd}` | `20251029` | Compact date format |
| `{post_date_F}` | `October` | Full month name |
| `{post_date_M}` | `Oct` | Short month name |
| `{excerpt}` | `This is the first...` | Post excerpt (100 chars) |

### Author Information
| Variable | Example Output | Description |
|----------|----------------|-------------|
| `{author_name}` | `Josh Stogner` | Author display name |
| `{author_first_name}` | `Josh` | Author first name |
| `{author_last_name}` | `Stogner` | Author last name |

### Site Information
| Variable | Example Output | Description |
|----------|----------------|-------------|
| `{site_name}` | `My Website` | WordPress site name |
| `{site_tagline}` | `Just another WP site` | Site description |

### Taxonomy Information
| Variable | Example Output | Description |
|----------|----------------|-------------|
| `{category}` | `Tech News` | Primary category |
| `{categories}` | `Tech News, Updates` | All categories |
| `{tags}` | `wordpress, omeda` | All tags |

---

## Common Format Examples

### Deployment Name Formats

#### Example 1: Simple Date-Based
```
Format: {post_title} - {post_date}
Output: Halloween Newsletter - October 29, 2025
```

#### Example 2: Site Branding
```
Format: {site_name} | {post_title}
Output: My Website | Halloween Newsletter
```

#### Example 3: Author Attribution
```
Format: {author_name}: {post_title}
Output: Josh Stogner: Halloween Newsletter
```

#### Example 4: Category Organization
```
Format: {category} - {post_title} ({post_date_ymd})
Output: Tech News - Halloween Newsletter (20251029)
```

#### Example 5: Detailed Format
```
Format: {site_name} - {post_title} - {post_date_F} {post_date_d}, {post_date_Y}
Output: My Website - Halloween Newsletter - October 29, 2025
```

### Campaign ID Formats

#### Example 1: Date-Based ID
```
Format: {post_date_ymd}-{post_id}
Output: 20251029-112
```

#### Example 2: Slug-Based ID
```
Format: {post_slug}
Output: halloween-newsletter
```

#### Example 3: Prefixed ID
```
Format: newsletter-{post_id}
Output: newsletter-112
```

#### Example 4: Category Prefix
```
Format: {category}-{post_date_ymd}
Output: tech-news-20251029
```

#### Example 5: Site-Specific
```
Format: {site_name}-{post_id}-{post_date_ymd}
Output: my-website-112-20251029
```

---

## How to Use

### Setting Up Formats (One-Time Setup)

1. **Go to Deployment Types**
   - Navigate to: `Omeda Integration → Deployment Types`
   - Click on existing type or create new one

2. **Add Deployment Name Format**
   ```
   Field: Deployment Name Format
   Example: {post_title} - {post_date}
   ```

3. **Add Campaign ID Format** (optional)
   ```
   Field: Campaign ID Format
   Example: campaign-{post_id}-{post_date_ymd}
   ```

4. **Save**
   - Click "Publish" or "Update"
   - Format is now active for new posts

### Creating Posts with Formats

#### Option A: Use Default Format (Recommended)
1. Create new post/newsletter
2. Select deployment type with format configured
3. **Leave format fields empty**
4. Save draft
5. ✅ Deployment created with formatted name

#### Option B: Custom Override
1. Create new post/newsletter
2. Select deployment type
3. **Enter custom values** in format fields:
   - Deployment Name: `SPECIAL EDITION: Black Friday`
   - Campaign ID: `bf-2025-special`
4. Save draft
5. ✅ Deployment created with custom values

### Checking What Will Be Used

1. Select deployment type
2. Look at **placeholder text** in format fields
3. Placeholder shows what will be generated
4. Example:
   ```
   Deployment Name: [                    ]
   Placeholder: My Site - Halloween Newsletter (20251029)
   ```

---

## Tips & Best Practices

### ✅ DO

- **Use descriptive formats** that help identify deployments
- **Include dates** for time-based organization
- **Test formats** with a test deployment type first
- **Keep IDs URL-safe** (letters, numbers, dashes only)
- **Use post_id** for guaranteed unique campaign IDs
- **Document your formats** for team consistency

### ❌ DON'T

- **Don't use special characters** in campaign IDs (@ # $ % etc.)
- **Don't make formats too long** (keep under 100 chars)
- **Don't change formats** retroactively (won't affect existing)
- **Don't rely on missing data** (e.g., author_first_name may be empty)
- **Don't use ambiguous formats** (team can't identify deployment)

### 💡 Pro Tips

1. **Category-Based Formats**: Organize by content type
   ```
   Format: {category} | {post_title}
   Output: Newsletter | Halloween Special
   ```

2. **Year-Month Grouping**: Easy archive organization
   ```
   Format: {post_date_Y}-{post_date_m} - {post_title}
   Output: 2025-10 - Halloween Special
   ```

3. **Unique Campaign IDs**: Avoid collisions
   ```
   Format: {post_date_ymd}-{post_id}
   Output: 20251029-112
   ```

4. **Author Tracking**: Monitor who creates what
   ```
   Format: [{author_name}] {post_title}
   Output: [Josh Stogner] Halloween Special
   ```

---

## Troubleshooting

### Problem: Variables not parsing
**Solution:** Check spelling and curly braces
```
❌ Wrong: {post-title}
✅ Correct: {post_title}

❌ Wrong: post_title
✅ Correct: {post_title}
```

### Problem: Deployment name is post title only
**Solution:** Check if format is configured in deployment type
- Format field may be empty
- Empty format = uses post title (old behavior)

### Problem: Can't edit format fields
**Solution:** Fields lock after deployment created
- This is by design (prevents accidental changes)
- Omeda deployment name set at creation time

### Problem: Placeholder doesn't match output
**Solution:** Post data may have changed
- Placeholder calculated at page load
- Save post to recalculate
- Refresh page to see current placeholder

### Problem: Campaign ID not appearing in Omeda
**Solution:** Check format configuration
- Campaign ID format may be empty
- Empty = Omeda auto-generates
- Enter format to control ID

---

## Migration from Old Deployments

### Existing Deployments
- **Not affected** by this update
- Continue working as before
- Deployment names unchanged

### New Deployments
- Automatically use formats
- Can override per-post if needed
- Better organization going forward

### Adding Formats to Old Types
1. Edit existing deployment type
2. Add formats
3. Save
4. **Only affects new posts**
5. Old posts keep original names

---

## Examples by Use Case

### Use Case: Weekly Newsletter
```
Deployment Name: Weekly Newsletter - {post_date_F} {post_date_d}, {post_date_Y}
Campaign ID: weekly-{post_date_ymd}

Output Name: Weekly Newsletter - October 29, 2025
Output ID: weekly-20251029
```

### Use Case: Product Announcements
```
Deployment Name: [PRODUCT] {post_title}
Campaign ID: product-{post_id}

Output Name: [PRODUCT] New Feature Released
Output ID: product-112
```

### Use Case: Multi-Author Blog
```
Deployment Name: {author_name}: {post_title}
Campaign ID: {author_last_name}-{post_date_ymd}

Output Name: Josh Stogner: Halloween Special
Output ID: stogner-20251029
```

### Use Case: Category Newsletters
```
Deployment Name: {category} Newsletter - {post_date_M} {post_date_Y}
Campaign ID: {category}-{post_date_ymd}

Output Name: Tech News Newsletter - Oct 2025
Output ID: tech-news-20251029
```

---

## Getting Help

### Documentation
- Full guide: `docs/VERSION_1.9.0_SUMMARY.md`
- Test plan: `docs/TEST_PLAN_1.9.0.md`
- Changelog: `CHANGELOG.md`

### Support
- Check workflow logs for errors
- Test with development deployment types
- Verify Omeda API accepts generated names

### Customization
- Developers can add custom variables
- Use filter: `omeda_parsed_variables`
- Extend `Omeda_Variable_Parser` class

---

**Version:** 1.9.0  
**Last Updated:** 2025-10-29  
**Quick Reference Card**
