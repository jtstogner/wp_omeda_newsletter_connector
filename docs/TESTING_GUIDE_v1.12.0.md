# Testing Guide - Enhanced Error Logging (v1.12.0)

## Overview
Version 1.12.0 adds enhanced error logging that captures full API request/response details when errors occur, regardless of the configured logging level.

## What's New
When an API error occurs (such as the HTTP 400 error you're experiencing), the workflow logs will now automatically capture and display:

1. **Error Summary** - High-level error message
2. **Request Details** - What was sent to Omeda:
   - Full URL endpoint
   - HTTP method (POST, GET, etc.)
   - Request payload/body
3. **Response Details** - What Omeda returned:
   - HTTP status code
   - Full response body
   - Individual error messages

## How to Test

### Step 1: Create a Test Newsletter
1. Go to your WordPress admin
2. Create or edit a newsletter using Newsletter Glue
3. Assign a deployment type that has Omeda deployment type mapped
4. Save as draft

### Step 2: View the Workflow Logs
1. Navigate to **Omeda** → **Workflow Logs** in the admin menu
2. Find your newsletter in the list
3. Click **View Details** for the newsletter

### Step 3: Review Error Details
When you see an error (like "Content assignment failed: Omeda API Error (HTTP 400)"), the log will now show:

#### In the Log Table:
- The error message with timestamp
- The step where it failed (e.g., "add_content")
- Retry attempt number if applicable

#### In the Context Column:
- Click **View Details** to expand the error context
- You'll see a RAW log entry labeled "API Request/Response Details" containing:

```
Array
(
    [url] => https://ows.omeda.com/webservices/rest/brand/BRAND/omail/deployment/content/*
    [method] => POST
    [http_code] => 400
    [request_payload] => <?xml version="1.0"?>
                        <Deployment>
                          <TrackId>TRACKID123</TrackId>
                          <UserId>12345</UserId>
                          <Splits>
                            <Split>
                              <SplitNumber>1</SplitNumber>
                              <FromName><![CDATA[Test Name]]></FromName>
                              <Mailbox>test</Mailbox>
                              <Subject><![CDATA[Test Subject]]></Subject>
                              <ReplyTo>reply@test.com</ReplyTo>
                              <HtmlContent><![CDATA[... your content ...]]></HtmlContent>
                            </Split>
                          </Splits>
                        </Deployment>
    [response_body] => Array
        (
            [SubmissionId] => guid-here
            [Errors] => Array
                (
                    [0] => Array
                        (
                            [Error] => Specific error message from Omeda
                        )
                )
        )
)
```

## What to Look For

### Common Issues to Check

1. **Invalid XML Structure**
   - Check if the XML in request_payload is properly formatted
   - Verify all required fields are present
   - Check for special characters that need escaping

2. **Missing Required Fields**
   - Look at the Omeda error messages in response_body
   - Common missing fields: TrackId, UserId, FromName, Subject

3. **Authentication Issues**
   - Verify the URL in the error details is correct
   - Check if the API key is being sent (visible in advanced logs)

4. **Content Issues**
   - Check if HtmlContent is properly encoded in CDATA
   - Verify content isn't too large
   - Check for invalid characters

## Debugging Tips

1. **Compare Working vs Non-Working**
   - Create a simple test newsletter that works
   - Compare its request_payload with the failing one
   - Look for differences in format or content

2. **Check Omeda Documentation**
   - Reference the error message against Omeda API docs
   - Look in `/omeda_api_docs/` folder for endpoint specifications

3. **Test with Minimal Content**
   - Try with plain text content first
   - Gradually add complexity to isolate the issue

4. **Verify Configuration**
   - Check deployment type settings
   - Verify From Name, Reply-To, and From Email are set correctly
   - Ensure deployment type ID from Omeda is correct

## Next Steps After Testing

Once you can see the full error details:

1. **Identify the Root Cause**
   - Look at the specific error message from Omeda
   - Compare the request payload with what Omeda expects
   - Check if any required fields are missing or malformed

2. **Report the Issue**
   - Copy the full error context (request + response)
   - Note the deployment type being used
   - Include the newsletter content if relevant

3. **Apply Fix**
   - Based on the error details, we can modify the code to:
     - Fix XML formatting issues
     - Add missing required fields
     - Properly encode special characters
     - Adjust content handling

## Support Information

If errors persist after reviewing the logs:

1. Export the workflow log details for the failing newsletter
2. Note the specific error messages from Omeda
3. Check the request payload for obvious issues
4. Compare with a working deployment (if any)

The enhanced logging should make it much clearer exactly what's causing the API error.
