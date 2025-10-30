# Quick Test Checklist - v1.12.0

## What Changed
✅ Plugin version updated to 1.12.0  
✅ Error logs now show full API request/response details  
✅ Works regardless of logging level setting  

## Test Steps

### 1. Create/Edit Test Newsletter
- Go to: http://localhost:8889/wp-admin/post.php?post=105&action=edit
- Or create a new newsletter
- Assign deployment type: "Just Josh" (or your configured type)
- Save as draft

### 2. Check Workflow Logs
- Go to: **Omeda** → **Workflow Logs** menu
- Find your newsletter
- Click **View Details**

### 3. What You Should See

#### If Error Still Occurs:
Look for these log entries in sequence:

```
[INFO] Step 1/3 Complete: Deployment created with TrackID: MTGMCD251029XXX
[INFO] Step 2/3 Complete: Audience assigned.
[INFO] Sending content (14291 chars) to Omeda...
[ERROR] Content assignment failed: Omeda API Error (HTTP 400)
[RAW] API Request/Response Details  <-- THIS IS NEW!
```

#### The RAW Log Entry Will Show:
Click "View Details" next to the RAW entry to see:

```php
Array
(
    [url] => https://ows.omeda.com/webservices/rest/brand/BRAND/omail/deployment/content/*
    [method] => POST
    [http_code] => 400
    [request_payload] => <?xml version="1.0"?>
                        <Deployment>
                          <TrackId>MTGMCD251029XXX</TrackId>
                          <UserId>XXXXX</UserId>
                          <Splits>
                            <Split>
                              <SplitNumber>1</SplitNumber>
                              <FromName><![CDATA[From Name Here]]></FromName>
                              <Mailbox>mailbox</Mailbox>
                              <Subject><![CDATA[Subject Here]]></Subject>
                              <ReplyTo>reply@email.com</ReplyTo>
                              <HtmlContent><![CDATA[...full content...]]></HtmlContent>
                            </Split>
                          </Splits>
                        </Deployment>
    [response_body] => Array
        (
            [SubmissionId] => some-guid
            [Errors] => Array
                (
                    [0] => Array
                        (
                            [Error] => The specific error message from Omeda will be here
                        )
                )
        )
)
```

## What to Look For

### 1. In the Request Payload
- [ ] Is TrackId present and correct?
- [ ] Is UserId present?
- [ ] Are FromName, Mailbox, Subject, ReplyTo all filled in?
- [ ] Is HtmlContent present and properly encoded in CDATA?
- [ ] Is the XML structure valid?

### 2. In the Response Body
- [ ] What is the specific error message?
- [ ] Does it mention a missing field?
- [ ] Does it mention invalid format?
- [ ] Does it mention authentication issues?

### 3. Common Issues to Check

#### Missing Required Field
If error says: `"Field 'XXX' is required"`
- Check if that field exists in request_payload
- Verify it has a value (not empty)

#### Invalid XML Format
If error says: `"Invalid XML"` or `"Parse error"`
- Check for unescaped special characters
- Verify CDATA sections are properly closed
- Look for < > & characters outside CDATA

#### Authentication Error
If error says: `"Invalid credentials"` or `"Unauthorized"`
- Check if x-omeda-appid header is being sent
- Verify API key is correct in settings

#### Content Too Large
If error says: `"Content too large"` or similar
- Check content_length in request details
- May need to compress or truncate content

## Reporting Results

When you see the error, please share:

1. **The exact error message** from Omeda (in response_body)
2. **The request_payload** (XML being sent) - can redact sensitive data
3. **Any patterns** you notice (does it happen with all newsletters or specific ones?)

## Example Good Report

```
Error Message: "Field 'UserId' is required"

Request shows:
- TrackId: MTGMCD251029012 ✓
- UserId: (empty!) ✗
- FromName: Test Name ✓
- Subject: Test Subject ✓

Issue: UserId field is not being populated in the deployment configuration.
```

## Next Steps After Test

1. ✅ **If you can now see the error details**: Great! Share them so we can fix the root cause
2. ❌ **If you still don't see the RAW log entry**: Let me know, there may be a caching issue
3. ❓ **If error is unclear**: Share the full context and we'll interpret it together

## Useful Commands

### Check Debug Log
```bash
tail -f /home/jts/development/NRS/Projects/wp_omeda_newsletter_connector/wp-content/debug.log
```

### Clear Cache (if needed)
In WordPress Admin:
- Settings → Omeda Settings
- Click "Clear All Caches"

### View Raw Logs in Database
```sql
SELECT post_id, meta_value 
FROM wp_postmeta 
WHERE meta_key = '_omeda_workflow_log' 
ORDER BY meta_id DESC 
LIMIT 20;
```

---

**Ready to test!** 🚀

The enhanced logging should now show you exactly what's causing the HTTP 400 error.
