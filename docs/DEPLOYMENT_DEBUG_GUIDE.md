# Omeda Integration - Deployment Debug Guide

## Version: 1.11.0
## Date: October 29, 2025

## Overview

This guide documents the enhanced logging system added to help diagnose and resolve issues with Omeda email deployment workflows.

## Changes Implemented

### 1. Enhanced API Request/Response Logging

**File:** `class-omeda-api-client.php`

**What Changed:**
- Added comprehensive logging for all API requests and responses
- Logs include: URL, method, headers, request body, status code, and response body
- Structured error handling with detailed error context

**How It Works:**
```php
// When raw logging is enabled, the API client logs:
- Full request URL
- HTTP method
- Request headers (with sanitized credentials)
- Request payload (JSON or XML)
- Response HTTP status code  
- Complete response body
```

**Benefits:**
- Immediately see what data is being sent to Omeda
- See exact error messages from Omeda API
- Diagnose malformed requests or missing fields
- Identify API endpoint issues

### 2. Logging Levels

**File:** `class-omeda-settings.php`

**Three Levels Available:**

#### Basic (Default)
- Main workflow steps (creating deployment, assigning audience, etc.)
- Retry attempts
- Errors with summary messages
- Suitable for production environments

#### Advanced
- Everything in Basic
- Transaction start/stop markers
- Step restart indicators
- Detailed error messages with context
- Suitable for troubleshooting workflows

#### Raw
- Everything in Advanced
- Complete request payloads sent to Omeda
- Full response bodies from Omeda
- API URLs and endpoints
- Data dumps for debugging
- **WARNING:** May contain sensitive data - use only in development

### 3. Enhanced Error Messages

**File:** `class-omeda-async-jobs.php`

**Improvements:**
- Structured error reporting with multiple details:
  - Error summary
  - API endpoint and full URL
  - HTTP status code
  - Request payload that was sent
  - Complete error response from Omeda
- Individual Omeda error messages extracted and logged separately
- Context-aware error formatting

**Example Error Log Entry:**
```
[ERROR] Content assignment failed: Omeda API Error (HTTP 400): Invalid HTML content
  → Omeda Error: HTML content must include unsubscribe link
  → Omeda Error: Subject line exceeds 255 characters
```

### 4. Settings Page Updates

**Location:** Omeda Integration → Settings → Workflow Configuration

**New Setting:**
- **Logging Level Dropdown** with descriptions:
  - Basic - Main steps and errors only
  - Advanced - Full trace with detailed errors
  - Raw - Complete data dumps (request/response)

## How to Use the Enhanced Logging

### Step 1: Enable Raw Logging

1. Navigate to **WP Admin → Omeda Integration → Settings**
2. Scroll to **Workflow Configuration** section
3. Change **Logging Level** to **Raw**
4. Click **Save Settings**

### Step 2: Create or Update a Deployment

1. Create/edit a newsletter post
2. Save as draft or publish
3. The workflow will execute and log all API interactions

### Step 3: View the Logs

**Option A: Workflow Logs Page (Detailed)**
1. Go to **Omeda Integration → Workflow Logs**
2. Find your post in the list
3. Click **View Logs**
4. Expand **View Details** on RAW log entries to see full data

**Option B: Newsletter Glue Interface (Summary)**
1. Edit your newsletter post
2. Scroll to **Omeda Workflow Status** metabox
3. View log entries with clickable details

**Option C: WordPress Debug Log (Technical)**
1. Enable WP_DEBUG_LOG in `wp-config.php`
2. Check `/wp-content/debug.log`
3. Look for entries with "=== Omeda API Request ===" and "=== Omeda API Response ==="

### Step 4: Analyze the Data

**For HTTP 400 Errors:**
- Check the request payload for missing required fields
- Verify data formats (dates, email addresses, etc.)
- Look for validation errors in the response body

**For HTTP 404 Errors:**
- Verify the API endpoint URL is correct
- Check if the TrackID exists in Omeda
- Confirm brand abbreviation is correct

**For Content Assignment Failures:**
- Examine the HTML content being sent
- Verify required fields are present (FromName, Mailbox, Subject, etc.)
- Check for XML special characters that need escaping

## Common Issues and Solutions

### Issue: Content Assignment Returns HTTP 400

**What to Check:**
1. **HTML Content:** 
   - Must include `@{{unsub_url}}@` somewhere
   - Cannot exceed Omeda's size limits
   - Must be valid HTML (no unclosed tags)

2. **Subject Line:**
   - Cannot exceed 255 characters
   - Cannot be empty

3. **Email Addresses:**
   - FromEmail must be valid format
   - ReplyTo must be valid format

**Solution:** Check the RAW log entry to see exactly what was sent and what error Omeda returned.

### Issue: Deployment Not Created

**What to Check:**
1. Required fields in request:
   - DeploymentName
   - DeploymentDate (format: YYYY-MM-DD HH:mm)
   - DeploymentTypeId (must exist in Omeda)
   - UserId (must be valid Omeda user)

2. API credentials:
   - Valid x-omeda-appid
   - Correct brand abbreviation
   - Proper environment (production vs staging)

**Solution:** Enable raw logging and verify all required fields are present in the request payload.

### Issue: Audience Not Assigned

**What to Check:**
1. QueryName must exist in Omeda
2. TrackID must be valid (deployment was created)
3. OutputCriteria must be valid field name
4. UserId must have permission to run queries

**Solution:** Check the response body in RAW logs for specific error messages from Omeda.

## API Endpoint Reference

All endpoints require trailing `/*` per Omeda API documentation:

- **Create Deployment:** `POST omail/deployment/*`
- **Assign Audience:** `POST omail/deployment/audience/add/*`
- **Add Content:** `POST omail/deployment/content/*`
- **Send Test:** `POST omail/deployment/test/sendto/*`
- **Schedule:** `POST omail/deployment/schedule/*`
- **Lookup Deployment:** `GET omail/deployment/lookup/{trackId}/*`
- **Get Deployment Types:** `GET deploymenttypes/*`

## Turning Off Enhanced Logging

**For Production:**
1. Go to **Omeda Integration → Settings**
2. Set **Logging Level** to **Basic**
3. Click **Save Settings**

This reduces log size and protects sensitive data while maintaining error tracking.

## Troubleshooting Tips

### Log Not Appearing
- Verify logging level is set correctly
- Check WordPress permissions for post meta updates
- Ensure Action Scheduler is running (check Omeda Integration → Background Jobs)

### Too Much Data in Logs
- Switch from Raw to Advanced level
- Raw level should only be used temporarily for debugging specific issues
- Consider clearing old logs periodically

### Can't Find Specific Error
- Use browser search (Ctrl+F / Cmd+F) in the Workflow Logs detail page
- Look for keyword: ERROR, HTTP 400, HTTP 500, etc.
- Check the step name to isolate which workflow stage failed

## Version History

**v1.11.0 (October 29, 2025)**
- Added three-level logging system (Basic, Advanced, Raw)
- Enhanced API error reporting with structured data
- Added request/response logging to API client
- Improved error message clarity in workflow logs
- Added settings page control for logging level

## Support

For additional assistance:
1. Review the complete logs with Raw level enabled
2. Check `/wp-content/debug.log` for PHP errors
3. Verify Omeda API documentation matches implementation
4. Test with Omeda staging environment first
