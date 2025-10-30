# Nonce Fix Testing Guide

## Issue
The workflow was being blocked with "Skipping - no nonce" error when saving posts via REST API or Newsletter Glue interface.

## Root Cause
The nonce verification was too strict - it required a nonce field even for REST API saves where nonces aren't typically present.

## Fix Applied (v1.13.1)
Modified `class-omeda-hooks.php` to:
1. Make nonce checking conditional
2. Only enforce nonce for traditional form submissions (when `$_POST['omeda_post_meta_nonce']` is present)
3. Allow REST API saves to proceed with just user capability check
4. Log when proceeding without nonce for transparency

## Testing Steps

### 1. Test Traditional Form Save (Should Still Require Nonce)
- Open a newsletter post in WordPress admin: http://localhost:8889/wp-admin/post.php?post=105&action=edit
- Make a change and click "Update"
- Expected: Should save successfully and trigger workflow

### 2. Test REST API Save (Should Work Without Nonce)
- Save a newsletter via Newsletter Glue interface
- Expected: Should save successfully and trigger workflow
- Check logs: Should see "Proceeding without nonce (REST API or programmatic save)"

### 3. Check Debug Log
```bash
tail -f wp-content/debug.log
```
Expected log entries:
- "Omeda: handle_post_save called for post_id=XXX"
- "Omeda: Proceeding without nonce (REST API or programmatic save)"
- "Omeda: Post type: ngl_email"
- Workflow logs should follow

### 4. Verify Workflow Execution
- Navigate to: http://localhost:8889/wp-admin/admin.php?page=omeda-workflow-logs
- Should see new log entries for the saved post
- Should see deployment creation, audience assignment, and content assignment steps

## What Changed

### Before (Broken)
```php
if (!isset($_POST['omeda_post_meta_nonce'])) {
    error_log("Omeda: Skipping - no nonce");
    return;  // ❌ Blocked all REST API saves
}
```

### After (Fixed)
```php
if (isset($_POST['omeda_post_meta_nonce'])) {
    if (!wp_verify_nonce($_POST['omeda_post_meta_nonce'], 'omeda_save_post_meta')) {
        error_log("Omeda: Skipping - nonce verification failed");
        return;
    }
} else {
    // ✅ Allow REST API saves with capability check
    if (!current_user_can('edit_post', $post_id)) {
        error_log("Omeda: Skipping - user cannot edit post (no nonce, REST API save)");
        return;
    }
    error_log("Omeda: Proceeding without nonce (REST API or programmatic save)");
}
```

## Security Note
This change maintains security by:
1. Still requiring nonce for traditional form submissions
2. Checking user capabilities for REST API saves
3. WordPress REST API has its own authentication/nonce system
4. We're only relaxing the check for programmatic saves where the user is already authenticated

