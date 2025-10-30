# Version 1.11.0 - Enhanced Logging and Debugging

## Release Date: October 29, 2025

## Quick Summary

This release adds a comprehensive three-level logging system that makes it much easier to diagnose and fix issues with Omeda email deployments. You can now see exactly what data is being sent to Omeda and what errors are being returned, all without needing to dig into debug logs or database tables.

## What's New

### Three Logging Levels

You can now choose how much detail you want in your logs:

1. **Basic (Default)** - Production-safe logging
   - Main workflow steps
   - Error summaries
   - Retry attempts
   - Perfect for production environments

2. **Advanced** - Detailed troubleshooting
   - Everything in Basic
   - Transaction start/stop markers
   - Detailed error messages
   - Step restart indicators
   - Good for troubleshooting workflows

3. **Raw** - Complete debugging data
   - Everything in Advanced
   - Full request payloads sent to Omeda
   - Complete response bodies from Omeda
   - API URLs and endpoints
   - **Use only in development** - may contain sensitive data

### Where to Configure

Go to: **WP Admin → Omeda Integration → Settings → Workflow Configuration**

Look for the "Logging Level" dropdown and select your preferred level.

### Enhanced Error Messages

When something goes wrong, you now get:

- **What happened**: Clear error summary
- **Where it happened**: API endpoint and full URL
- **What was sent**: The request payload
- **What was returned**: Complete error response from Omeda
- **Individual errors**: Each Omeda error message extracted and displayed

### Example Error Output

**Before (v1.10.0):**
```
[ERROR] Content assignment failed: Omeda API Error (HTTP 400)
```

**After (v1.11.0 with Raw logging):**
```
[ERROR] Content assignment failed: Omeda API Error (HTTP 400): Invalid HTML content
  → Omeda Error: HTML content must include unsubscribe link
  → Omeda Error: Subject line exceeds 255 characters
[RAW] API Error Response:
  URL: https://ows.omedastaging.com/webservices/rest/brand/MTGMCD/omail/deployment/content/*
  Method: POST
  HTTP Code: 400
  Request Payload: {...}
  Response Body: {"Errors": [{"Error": "HTML content must include unsubscribe link"}, ...]}
```

## How to Use

### For Immediate Debugging

1. Navigate to **Omeda Integration → Settings**
2. Scroll to **Workflow Configuration**
3. Change **Logging Level** to **Raw**
4. Click **Save Settings**
5. Create or update a newsletter
6. Go to **Omeda Integration → Workflow Logs**
7. Find your post and click **View Logs**
8. Expand **View Details** on RAW entries to see full data

### For Production

Keep logging level at **Basic** to avoid log bloat and protect sensitive data.

Only switch to **Advanced** or **Raw** when actively troubleshooting issues.

## Files Changed

- `src/omeda-newsletter-connector/includes/class-omeda-api-client.php`
  - Enhanced `send_request()` with request/response logging
  - Structured error responses with full context

- `src/omeda-newsletter-connector/includes/class-omeda-async-jobs.php`
  - Updated `handle_add_content()` with enhanced error handling
  - Extracts and logs individual error messages

- `src/omeda-newsletter-connector/includes/class-omeda-settings.php`
  - Added logging level dropdown to settings page
  - New `render_logging_level_field()` method

- `src/omeda-newsletter-connector/omeda-wp-integration.php`
  - Version bumped to 1.11.0

## Documentation Added

- `docs/DEPLOYMENT_DEBUG_GUIDE.md` - Comprehensive troubleshooting guide
  - How to use each logging level
  - Common issues and solutions
  - Step-by-step debugging instructions
  - API endpoint reference

- `CHANGELOG.md` - Updated with v1.11.0 entry

## Benefits

- **Faster Issue Resolution**: See exactly what's wrong without guessing
- **Better Support**: More information available when helping customers
- **Safer Production**: Basic logging keeps logs clean while debugging available when needed
- **No Code Changes**: Adjust logging level via settings, no PHP edits required
- **Complete Visibility**: Full request/response data available for deep debugging

## Upgrade Notes

- **Safe to upgrade**: No database changes or breaking changes
- **Automatic**: Logging level defaults to "basic" (same as before)
- **Immediate benefit**: Can enable Raw logging right away if debugging needed
- **No configuration required**: Works immediately after upgrade

## Testing Checklist

- [ ] Settings page shows new logging level dropdown
- [ ] Can save logging level selection
- [ ] Basic level shows main steps only
- [ ] Advanced level shows transaction traces
- [ ] Raw level shows request/response data
- [ ] Error messages include all relevant context
- [ ] Workflow logs display properly in UI
- [ ] Debug log receives raw data when level is "raw"

## Support

If you encounter issues:

1. Enable Raw logging level
2. Reproduce the issue
3. Check Workflow Logs for detailed error information
4. Review `docs/DEPLOYMENT_DEBUG_GUIDE.md` for troubleshooting tips
5. Check `/wp-content/debug.log` for raw API transactions

## Next Steps

With this enhanced logging in place, we can:

1. More easily diagnose why content isn't being sent
2. Identify API endpoint issues quickly
3. See exactly what Omeda is rejecting and why
4. Fix data formatting issues before they reach Omeda
5. Validate all workflow steps are executing correctly
