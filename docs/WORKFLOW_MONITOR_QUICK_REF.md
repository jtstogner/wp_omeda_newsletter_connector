# Workflow Monitor Quick Reference

## Access Points

### 1. Monitor Dashboard
**Path:** Omeda Integration → Workflow Monitor  
**Capability Required:** `manage_options` (Administrator)

**What You See:**
```
┌─────────────────────────────────────────────────────────────┐
│  Omeda Workflow Monitor                                     │
├─────────────────────────────────────────────────────────────┤
│  View all currently scheduled and running workflow tasks    │
├──────┬───────────────┬──────────────────┬──────────┬────────┤
│ Post │ Title         │ Task             │ Time     │ Status │
├──────┼───────────────┼──────────────────┼──────────┼────────┤
│ 123  │ My Article    │ Create Deploy    │ 14:30:00 │ ⏰ 0   │
│ 123  │ My Article    │ Assign Audience  │ 14:30:30 │ ⏰ 0   │
│ 456  │ Other Post    │ Update Content   │ 14:35:00 │ ⏰ 0   │
└──────┴───────────────┴──────────────────┴──────────┴────────┘

Recent Workflow Logs
────────────────────
📄 My Article (ID: 123)
  ├─ 14:25:00 [INFO] Workflow Initiated
  ├─ 14:25:05 [INFO] Step 1/5 Complete: Deployment created
  └─ 14:25:35 [INFO] Step 2/5 Complete: Audience assigned
```

### 2. Post Edit Metabox
**Location:** Post edit screen → Right sidebar  
**Appears On:** Posts with deployment type assigned

**What You See:**
```
┌─────────────────────────────────────┐
│ Omeda Workflow Status               │
├─────────────────────────────────────┤
│ Track ID: ABC123XYZ                 │
│                                     │
│ Pending Tasks:                      │
│ • Update Content - 14:35:00         │
│                                     │
│ Recent Activity:                    │
│ ┌─────────────────────────────────┐ │
│ │ ℹ️ INFO: Deployment created     │ │
│ │   2025-10-29 14:25:00           │ │
│ ├─────────────────────────────────┤ │
│ │ ℹ️ INFO: Audience assigned      │ │
│ │   2025-10-29 14:25:30           │ │
│ └─────────────────────────────────┘ │
└─────────────────────────────────────┘
```

---

## Task Types & Timing

### Draft Save Workflow
```
Save Draft (first time)
    ↓ immediate
[Create Deployment] ────────────────────► Track ID created
    ↓ +30s
[Assign Audience] ──────────────────────► Audience added
    ↓ +30s  
[Add Content] ──────────────────────────► HTML content uploaded
    ↓
✅ Draft ready for editing
```

### Content Update Workflow
```
Save Draft (subsequent)
    ↓ +60s (debounced)
[Update Content] ───────────────────────► HTML content updated
    ↓
✅ Changes synced to Omeda
```

### Publish Workflow
```
Publish/Schedule Post
    ↓ immediate
[Update Content] ───────────────────────► Latest HTML uploaded
    ↓ +30s
[Send Test] ────────────────────────────► Test email sent
    ↓ +30s
[Schedule Deployment] ──────────────────► Deployment scheduled
    ↓
✅ Ready to send at publish time
```

---

## Status Icons

| Icon | Status | Meaning |
|------|--------|---------|
| ⏰ | Pending | Task scheduled, waiting to run |
| ✅ | Complete | Task finished successfully |
| ⚠️ | Warning | Non-critical issue occurred |
| ❌ | Error | Critical failure, retry scheduled |
| 🔄 | Retrying | Failed task being retried (see count) |

---

## Log Levels

### ℹ️ INFO (Blue)
Normal operation messages
- "Workflow Initiated"
- "Step X/5 Complete"
- "Deployment created"
- "Content updated"

### ⚠️ WARN (Orange)
Non-critical warnings
- "Failed to send test email"
- "Retrying..."
- "API rate limit approaching"

### ❌ ERROR (Red)
Critical failures
- "API authentication failed"
- "Invalid configuration"
- "Max retries exceeded"
- "Network error"

---

## Quick Troubleshooting

### Problem: No tasks showing
**Check:**
1. Is post assigned a deployment type?
2. Is post in draft/pending/future status?
3. Is WP-Cron enabled?

**Fix:**
- Create/edit a post with deployment type
- Save as draft to trigger workflow

### Problem: Task stuck at pending
**Check:**
1. Monitor page - is task listed?
2. Metabox - any error messages?
3. Debug log - API errors?

**Fix Development:**
- Tools → Scheduled Actions → Run Now

**Fix Production:**
- Visit any site page (triggers WP-Cron)
- Wait a few minutes

### Problem: High retry count
**Check:**
1. Recent logs for error details
2. API credentials in settings
3. Omeda API status

**Fix:**
- Correct API credentials
- Check network connectivity
- Contact Omeda support if needed

### Problem: Track ID missing
**Check:**
1. Has draft been saved?
2. Is deployment type assigned?
3. Any error in recent activity?

**Fix:**
- Save draft again
- Check for errors in metabox
- Verify API credentials

---

## Common Patterns

### Healthy Deployment
```
✅ INFO: Workflow Initiated
✅ INFO: Step 1/5 Complete: Deployment created with TrackID: ABC123
✅ INFO: Step 2/5 Complete: Audience assigned
✅ INFO: Step 3/5 Complete: Content uploaded
✅ INFO: Content updated successfully
✅ INFO: Test email sent
✅ INFO: Deployment scheduled for 2025-10-29 18:00:00
✅ INFO: Workflow Complete
```

### Retry Pattern
```
ℹ️ INFO: Step 1/5: Creating deployment...
❌ ERROR: API authentication failed
⚠️ WARN: Retry 1/3 scheduled (will execute in 60 seconds)
ℹ️ INFO: Step 1/5: Creating deployment...
✅ INFO: Step 1/5 Complete: Deployment created with TrackID: ABC123
```

### Failed Deployment
```
ℹ️ INFO: Workflow Initiated
❌ ERROR: Invalid deployment type configuration
⚠️ WARN: Retry 1/3 scheduled (will execute in 60 seconds)
❌ ERROR: Invalid deployment type configuration
⚠️ WARN: Retry 2/3 scheduled (will execute in 120 seconds)
❌ ERROR: Invalid deployment type configuration
⚠️ WARN: Retry 3/3 scheduled (will execute in 240 seconds)
❌ ERROR: Invalid deployment type configuration
❌ ERROR: Max retries exceeded. Workflow aborted.
```

---

## Best Practices

### ✅ DO
- Check metabox before publishing
- Monitor retry counts
- Review logs after errors
- Save drafts periodically
- Verify Track ID exists

### ❌ DON'T
- Don't publish with pending tasks
- Don't ignore high retry counts
- Don't skip error log review
- Don't save repeatedly (debouncing)
- Don't delete posts with active deployments

---

**Quick Reference Version:** 1.0  
**Feature Version:** 1.5.0  
**Last Updated:** October 29, 2025
