# Diagnosing HTTP 400 Content Assignment Error

**Date:** 2025-10-29  
**Error:** Content assignment failing with "Omeda API Error (HTTP 400)"  
**Status:** Investigating with enhanced logging

## Current Situation

**Symptom:**
- Deployment creation succeeds ✅
- Audience assignment succeeds ✅  
- Content assignment fails with HTTP 400 ❌

**Log Output:**
```
[2025-10-29 17:09:02] [INFO] Retry 1/3 scheduled for add_content (will execute in 60 seconds).
[2025-10-29 17:09:01] [ERROR] Content assignment failed: Omeda API Error (HTTP 400)
[2025-10-29 17:09:01] [INFO] Sending content (14291 chars) to Omeda...
[2025-10-29 17:09:01] [INFO] Executing: Add content job...
```

## What HTTP 400 Means

HTTP 400 (Bad Request) indicates:
- Request syntax is invalid
- Required field missing
- Field format incorrect
- Value validation failed
- Malformed XML/JSON payload

## Next Steps for Diagnosis

### 1. Enable Raw Logging

Raw logging will capture the exact request being sent to Omeda, allowing us to see:
- XML payload structure
- Field values
- Any malformed data
- HTML content being sent
- Subject line format

**Implementation:**
```php
// In handle_add_content() method (already implemented)
$content_info = [
    'track_id' => $track_id,
    'subject' => $config['Subject'] ?? 'Not set',
    'content_length' => $content_length,
    'html_content' => $config['HtmlContent'] ?? 'Not set'
];
$this->workflow_manager->log_raw($post_id, 'Adding content', $content_info, 'add_content');
```

### 2. Check Response Details

Need to capture the Omeda API error response which may contain:
- Specific error message
- Invalid field name
- Validation failure details
- Omeda error code

**Current Issue:** Error handling in API client may be swallowing detailed error response.

**Solution:** Update exception handling to include full response:

```php
// In Omeda_API_Client::step3_add_content()
catch (Exception $e) {
    $error_msg = $e->getMessage();
    // Check if response body available
    if (method_exists($e, 'getResponse')) {
        $response = $e->getResponse();
        $body = wp_remote_retrieve_body($response);
        $error_msg .= "\nResponse: " . $body;
    }
    throw new Exception("Content assignment failed: $error_msg");
}
```

### 3. Common HTTP 400 Causes

Based on Omeda API documentation, check for:

**Missing Required Fields:**
- `HtmlContent` (required)
- `Subject` (required)
- `FromEmail` OR `Mailbox` (required, but not both)

**Field Format Issues:**
- Mailbox must be just username (not full email)
- HTML content must be valid
- Subject length limits
- Special character encoding

**Recent Change:**
- Version 1.9.1 fixed `FromEmail` → `Mailbox` issue
- May have introduced new issue

### 4. Verify API Endpoint

**Documented Endpoint:**
```
POST /webservices/rest/omail/deployment/content/*
```

**What to Check:**
- Trailing `/*` present?
- Correct HTTP method (POST)?
- Content-Type header correct?
- Authentication header included?

### 5. Review Content Being Sent

**Content to Examine:**
- HTML structure (valid HTML?)
- Length (14291 chars - within limits?)
- Special characters (encoded properly?)
- Newsletter Glue template output format
- Any Newsletter Glue-specific tags that Omeda doesn't support

## Implementation Plan

### Phase 1: Capture Full Error Response ⏳

**File:** `includes/class-omeda-api-client.php`  
**Method:** `step3_add_content()`

```php
public function step3_add_content($track_id, $config) {
    try {
        // ... existing code ...
        
        $response = wp_remote_post($url, $args);
        
        if (is_wp_error($response)) {
            throw new Exception('HTTP Error: ' . $response->get_error_message());
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        if ($status_code !== 200 && $status_code !== 201) {
            // Log the full response for debugging
            error_log("Omeda API Error Response (HTTP $status_code): " . $body);
            
            // Try to parse error details
            $parsed = $this->parse_response($body);
            if (isset($parsed['Errors'])) {
                $error_details = print_r($parsed['Errors'], true);
                throw new Exception("Omeda API Error (HTTP $status_code): $error_details");
            }
            
            throw new Exception("Omeda API Error (HTTP $status_code): $body");
        }
        
        return $this->parse_response($body);
        
    } catch (Exception $e) {
        // Enhanced error with full context
        throw new Exception('Content assignment failed: ' . $e->getMessage());
    }
}
```

### Phase 2: Add Request Logging ⏳

**File:** `includes/class-omeda-async-jobs.php`  
**Method:** `handle_add_content()` (already updated)

Logging now includes:
- Request data before API call
- Response data after API call
- Error details in context

### Phase 3: Validate Payload Structure ⏳

**Check:**
1. XML structure matches documentation exactly
2. No extra/invalid fields
3. All required fields present
4. Field values within constraints
5. Proper encoding of HTML content

### Phase 4: Test with Minimal Content ⏳

Create test with:
- Simple HTML: `<html><body><p>Test</p></body></html>`
- Basic subject: "Test"
- Verify deployment/audience work
- Try minimal content
- Gradually add complexity

## Expected Output After Phase 1

With enhanced error logging, we should see:

```
[2025-10-29 17:09:01] [RAW] [add_content] Adding content:
{
  "track_id": "MTGMCD251029008",
  "subject": "Test Newsletter - Oct 29, 2025",
  "content_length": 14291,
  "html_content": "<!DOCTYPE html>..."
}

[2025-10-29 17:09:01] [ERROR] [add_content] Content assignment failed: Omeda API Error (HTTP 400): {
  "Errors": [
    {
      "Error": "Field 'XYZ' is required"
    }
  ]
}
```

This will tell us exactly which field is causing the issue.

## Reference: Omeda Content API

**Endpoint:** `POST /webservices/rest/omail/deployment/content/*`

**Required Fields:**
- TrackID (in URL path)
- HtmlContent
- Subject
- Mailbox (not FromEmail)

**Optional Fields:**
- FromName
- FromEmail (deprecated - causes issues)
- EncryptedCustomerKey (if audience not already assigned)

**Valid Request Example:**
```xml
<?xml version="1.0" encoding="UTF-8"?>
<Add>
    <Content>
        <HtmlContent><![CDATA[<!DOCTYPE html>...]]></HtmlContent>
        <Subject>Newsletter Subject</Subject>
        <Mailbox>noreply</Mailbox>
        <FromName>My Newsletter</FromName>
    </Content>
</Add>
```

## Action Items

- [ ] Review current XML payload being sent
- [ ] Check Omeda API response for specific error
- [ ] Verify Mailbox field extraction logic
- [ ] Test with minimal HTML content
- [ ] Compare working vs failing requests
- [ ] Check for Newsletter Glue specific issues

## Related Files

- `includes/class-omeda-api-client.php` - API client implementation
- `includes/class-omeda-async-jobs.php` - Job handlers with logging
- `includes/class-omeda-workflow-manager.php` - Workflow logging
- `docs/omeda_api_docs/email-builder-add-content-api.md` - API documentation

## Support Contacts

If issue persists after Phase 1:
- Omeda API Support: [Support contact from docs]
- Provide: TrackID, full error response, request payload
- Reference: Content API documentation

---

**Document Version:** 1.0  
**Last Updated:** 2025-10-29  
**Status:** Investigation in Progress
