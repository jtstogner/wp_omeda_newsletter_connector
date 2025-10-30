# Deployment Content Assignment Fix - October 29, 2025

## Issue Summary

The plugin was failing to assign content to Omeda email deployments. Deployments were being created and audiences were being assigned successfully, but the content step was failing silently with retries scheduled indefinitely.

## Root Cause

The XML payload sent to the Omeda `/omail/deployment/content/*` API endpoint included an invalid `<FromEmail>` element that is not part of the API specification. According to the Omeda API documentation, the content API only accepts a `<Mailbox>` element (the part before the @ symbol), not a full email address element.

### Incorrect Payload (Before Fix)
```xml
<Deployment>
    <TrackId>TRACK123</TrackId>
    <UserId>testuser</UserId>
    <Splits>
        <Split>
            <SplitNumber>1</SplitNumber>
            <FromName><![CDATA[Newsletter Name]]></FromName>
            <FromEmail>newsletters@example.com</FromEmail>  <!-- INVALID -->
            <Mailbox>newsletters</Mailbox>
            <Subject><![CDATA[Subject Line]]></Subject>
            <ReplyTo>reply@example.com</ReplyTo>
            <HtmlContent><![CDATA[...]]></HtmlContent>
        </Split>
    </Splits>
</Deployment>
```

### Correct Payload (After Fix)
```xml
<Deployment>
    <TrackId>TRACK123</TrackId>
    <UserId>testuser</UserId>
    <Splits>
        <Split>
            <SplitNumber>1</SplitNumber>
            <FromName><![CDATA[Newsletter Name]]></FromName>
            <Mailbox>newsletters</Mailbox>  <!-- CORRECT -->
            <Subject><![CDATA[Subject Line]]></Subject>
            <ReplyTo>reply@example.com</ReplyTo>
            <HtmlContent><![CDATA[...]]></HtmlContent>
        </Split>
    </Splits>
</Deployment>
```

## Changes Made

### 1. Fixed API Client (`class-omeda-api-client.php`)

**File**: `/src/omeda-newsletter-connector/includes/class-omeda-api-client.php`

**Changes**:
- Removed the invalid `<FromEmail>{$from_email}</FromEmail>` line from the XML payload
- Modified mailbox extraction to use email's username part: `explode('@', $from_email)[0]`
- Added return statement to capture API response for better error handling

**Code Changes**:
```php
// Before
$mailbox = $config['MailboxName'] ?? get_option('omeda_default_mailbox', 'newsletters');
$from_email = $config['FromEmail'];
// XML included: <FromEmail>{$from_email}</FromEmail>

// After
$from_email = $config['FromEmail'];
$mailbox = $config['MailboxName'] ?? explode('@', $from_email)[0];
// XML now only includes: <Mailbox>{$mailbox}</Mailbox>
```

### 2. Enhanced Error Logging (`class-omeda-async-jobs.php`)

**File**: `/src/omeda-newsletter-connector/includes/class-omeda-async-jobs.php`

**Changes in `handle_add_content` method**:
- Added content length logging for debugging
- Enhanced error parsing to extract and display Omeda API errors
- Added support for displaying API warnings from successful responses
- Improved error message formatting

**New Logging Features**:
```php
// Log content size
$content_length = isset($config['HtmlContent']) ? strlen($config['HtmlContent']) : 0;
$this->workflow_manager->log_status($post_id, 
    sprintf('Sending content (%d chars) to Omeda...', $content_length));

// Parse and display Omeda warnings
if (is_array($result) && isset($result['Warnings'])) {
    $warning_count = count($result['Warnings']);
    $this->workflow_manager->log_warning($post_id, 
        sprintf('Content added with %d warning(s) from Omeda.', $warning_count));
    foreach ($result['Warnings'] as $warning) {
        $this->workflow_manager->log_warning($post_id, '  - ' . $warning);
    }
}

// Enhanced error details
$error_data = json_decode($e->getMessage(), true);
if (is_array($error_data) && isset($error_data['response_body'])) {
    // Display each error from Omeda API
    if (isset($error_data['response_body']['Errors'])) {
        foreach ($error_data['response_body']['Errors'] as $err) {
            if (isset($err['Error'])) {
                $this->workflow_manager->log_error($post_id, '  - ' . $err['Error']);
            }
        }
    }
}
```

### 3. Version Bump

**File**: `/src/omeda-newsletter-connector/omeda-wp-integration.php`

Updated plugin version from `1.9.0` to `1.9.1`

### 4. Documentation Updates

**File**: `/CHANGELOG.md`

Added new section for version 1.9.1 documenting the bug fix

## API Documentation Reference

According to the Omeda Email Deployment Content API documentation at:
`https://knowledgebase.omeda.com/omedaclientkb/email-deployment-content`

### Required Fields for Content API

| Field | Type | Description |
|-------|------|-------------|
| `UserId` | string | UserId of the omail account authorized for this deployment |
| `TrackId` | string | Unique tracking number for the deployment |
| `SplitNumber` | integer | Sequential number of split (usually 1) |
| `FromName` | string | Text displayed in recipient's "From" section |
| `Mailbox` | string | Mailbox portion of the From email (e.g., "newsletters" from "newsletters@example.com") |
| `Subject` | string | Subject line of the email |
| `HtmlContent` | html | HTML code for the email |

### Optional Fields

| Field | Type | Description |
|-------|------|-------------|
| `TextContent` | string | Plain text version |
| `ReplyTo` | string | Reply-to email address |
| `Preheader` | string | Preview text after subject line |

**Note**: The documentation explicitly does NOT include a `FromEmail` field. Only `Mailbox` is used.

## Testing Recommendations

1. **Create New Deployment**: Save a draft newsletter with a deployment type assigned
2. **Check Workflow Logs**: Navigate to Omeda → Workflow Logs in WordPress admin
3. **Verify Content Assignment**: Confirm log shows "Step 3/3 Complete: Initial content added"
4. **Check Omeda Portal**: Verify deployment appears with content in Omeda's platform
5. **Test Warning Display**: If Omeda returns warnings (e.g., missing unsubscribe link), verify they appear in logs

## Monitoring Points

- Check that deployments progress through all 3 steps without retries
- Verify content character count is logged
- Confirm no "Retry X/3 scheduled for add_content" messages appear
- Ensure warnings from Omeda API are visible in workflow logs

## Rollback Instructions

If issues occur with this fix:

1. Revert `class-omeda-api-client.php` to include `<FromEmail>` element
2. Update version back to 1.9.0
3. Report specific error messages from workflow logs

## Related Files

- `/src/omeda-newsletter-connector/includes/class-omeda-api-client.php`
- `/src/omeda-newsletter-connector/includes/class-omeda-async-jobs.php`
- `/src/omeda-newsletter-connector/omeda-wp-integration.php`
- `/CHANGELOG.md`

## API Endpoint

```
POST https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/content/*
POST https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/content/*
```

**Note**: The trailing `/*` is REQUIRED per the API documentation.

## Success Indicators

After deploying this fix, you should see:
1. ✅ "Step 3/3 Complete: Initial content added" in workflow logs
2. ✅ No retry messages for content assignment
3. ✅ Content visible in Omeda deployment portal
4. ✅ Deployment marked as "ready" for testing
5. ✅ Character count logged (e.g., "Sending content (15234 chars) to Omeda...")
