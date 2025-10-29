# Testing Guide: Draft Deployment Creation (v1.5.0)

**Quick Reference for Testing the New Draft Deployment Feature**

---

## Pre-Test Setup

### 1. Verify Prerequisites
```bash
# Check plugin version
# WordPress Admin → Plugins → Omeda WordPress Integration
# Should show: Version 1.5.0
```

### 2. Configure Settings
1. Go to **Settings → Omeda Integration**
2. Verify API credentials are set:
   - App ID: ✓
   - Brand Abbreviation: ✓
   - Default User ID: ✓
   - Environment: Staging or Production

3. Verify default email settings (optional):
   - Default From Name
   - Default From Email
   - Default Reply To

### 3. Create Deployment Type
1. Go to **Omeda Deploy Types → Add New**
2. Enter deployment configuration:
   - Title: "Test Newsletter"
   - Omeda Deployment Type: Select from dropdown
   - Audience Query: Enter query name (e.g., "Test_Audience")
   - From Name, From Email, Reply To
   - Subject Format: `{post_title} - {site_name}`
3. Save deployment type

---

## Test 1: Create New Draft with Deployment

### Steps
1. Go to **Posts → Add New**
2. Enter post details:
   - Title: "Test Article for Draft Deployment"
   - Content: Add some test content
3. In **Omeda Deployment** meta box:
   - Select deployment type OR verify auto-detection
4. Click **Save Draft**
5. Wait 2-5 seconds for processing

### Expected Results
✓ Page refreshes/reloads  
✓ Meta box shows:
- Omeda TrackID: `ABC123...` (12-character ID)
- Workflow Log with 4 entries:
  - `[INFO] Step 1/4 Complete: Deployment created with TrackID: ABC123...`
  - `[INFO] Step 2/4 Complete: Audience assigned.`
  - `[INFO] Step 3/4 Complete: Content and subject sent to Omeda.`
  - `[INFO] Step 4/4 Complete: Initial deployment setup complete. Scheduled for YYYY-MM-DD HH:00 UTC (temporary date - will update when published).`

### Verify in Omeda
1. Log into Omeda Email Builder
2. Search for deployment: "Test Article for Draft Deployment"
3. Verify:
   - ✓ Deployment exists
   - ✓ TrackID matches WordPress
   - ✓ Deployment date is next hour
   - ✓ Audience is assigned
   - ✓ Content is present
   - ✓ Subject line is correct

### Test Variations

**Variation A: Different Current Time**
- Test at :00 (3:00 PM) → Should schedule for 4:00 PM
- Test at :15 (3:15 PM) → Should schedule for 4:00 PM
- Test at :30 (3:30 PM) → Should schedule for 4:00 PM
- Test at :45 (3:45 PM) → Should schedule for 4:00 PM

**Variation B: No Deployment Type**
- Don't select deployment type
- Save draft
- Expected: No TrackID, no deployment created
- Meta box should show "-- Do Not Deploy / Auto-Detect --"

---

## Test 2: Update Existing Draft

### Steps
1. Open draft from Test 1 (has TrackID)
2. Edit content:
   - Change title to "Updated Test Article"
   - Modify content
3. Click **Update**
4. Wait 2-5 seconds

### Expected Results
✓ Page refreshes  
✓ Meta box shows:
- Same TrackID (unchanged)
- New log entry:
  - `[INFO] Content updated successfully in Omeda.`
- Deployment type dropdown is **locked** (disabled)

### Verify in Omeda
1. Reload deployment in Omeda Email Builder
2. Verify:
   - ✓ Content updated
   - ✓ Title updated
   - ✓ TrackID same as before
   - ✓ Deployment date unchanged (still next hour)

---

## Test 3: Publish Draft

### Steps
1. Open draft from Test 2 (has TrackID)
2. Click **Publish**
3. Wait 5-10 seconds

### Expected Results
✓ Post published successfully  
✓ Meta box shows:
- Same TrackID
- New log entries:
  - `[INFO] Post Published/Scheduled. Finalizing deployment...`
  - `[INFO] Content updated successfully in Omeda.`
  - `[INFO] Test email sent successfully.` (if testers configured)
  - `[INFO] Deployment successfully scheduled for: YYYY-MM-DD HH:MM (UTC).`
  - `[INFO] Workflow Complete.`

### Verify in Omeda
1. Reload deployment in Omeda Email Builder
2. Verify:
   - ✓ Deployment date **changed** from next-hour to actual time
   - ✓ Deployment is scheduled
   - ✓ Content is final version
   - ✓ Ready to send

### Calculate Expected Date
- If published immediately: Current time + 30 minutes (default delay)
- If scheduled: Exact scheduled date/time

---

## Test 4: Error Scenarios

### Test 4A: Invalid API Credentials
1. Go to Settings → Omeda Integration
2. Change App ID to invalid value
3. Save settings
4. Create new post, select deployment type
5. Click Save Draft

**Expected**: Error in workflow log showing API authentication failed

### Test 4B: Invalid Audience Query
1. Create deployment type with invalid query name: "NonExistent_Query"
2. Create post with this deployment type
3. Save draft

**Expected**: 
- Step 1/4 succeeds (deployment created)
- Step 2/4 fails (audience assignment error)
- Error in logs

### Test 4C: Missing Required Fields
1. Create deployment type without From Email
2. Create post with this deployment type
3. Save draft

**Expected**: Error in Step 3/4 (content upload fails due to missing email)

---

## Test 5: Multiple Saves

### Steps
1. Create new draft with deployment type
2. Save draft → Wait for TrackID
3. Edit content
4. Save draft → Wait for update
5. Edit again
6. Save draft → Wait for update
7. Repeat 2-3 more times

### Expected Results
✓ TrackID remains constant  
✓ Multiple "Content updated" log entries  
✓ No duplicate deployments created  
✓ Latest content always in Omeda  
✓ No errors in logs

---

## Test 6: Auto-Detection

### Steps
1. Create deployment type with Post Type assignment: "post"
2. Create new post (don't select deployment type)
3. Save draft

### Expected Results
✓ Meta box shows "Auto-detected:" message  
✓ Deployment type auto-selected  
✓ TrackID created  
✓ All 4 steps complete

---

## Test 7: Date Calculation Accuracy

### Manual Verification
```php
// Run in WordPress debug/test environment
$current_time = time();
$next_hour = ceil($current_time / 3600) * 3600;
$current_formatted = gmdate('Y-m-d H:i:s', $current_time);
$next_formatted = gmdate('Y-m-d H:i:s', $next_hour);

echo "Current time (GMT): " . $current_formatted . "\n";
echo "Next hour (GMT): " . $next_formatted . "\n";
echo "Difference (minutes): " . (($next_hour - $current_time) / 60) . "\n";
```

### Expected
- Difference should be 1-60 minutes
- Next hour should always be on :00 minutes
- Should be in GMT/UTC timezone

---

## Troubleshooting

### Issue: No TrackID After Save
**Check**:
1. Deployment type selected?
2. API credentials valid?
3. Network connectivity?
4. Check browser console for JS errors
5. Check PHP error logs

**Solution**: Fix the identified issue and try saving again

### Issue: Wrong Deployment Date
**Check**:
1. Server timezone settings
2. WordPress timezone settings
3. Current time in GMT
4. Calculation formula

**Solution**: Verify server time is correct, check timezone settings

### Issue: Content Not Updating
**Check**:
1. Workflow logs for errors
2. Content size (too large?)
3. HTML validity
4. API rate limits

**Solution**: Simplify content, check logs, verify HTML

---

## Performance Benchmarks

### Expected Timing
- Draft save (new deployment): 2-5 seconds
- Draft save (update content): 1-3 seconds
- Publish: 3-7 seconds

### If Slower
**Possible causes**:
- Slow network connection
- Omeda API latency
- Large content size
- Server processing power

**Mitigation**:
- Use wp-env or local dev for testing
- Optimize content size
- Check network speed

---

## Success Criteria

### All Tests Pass When:
✓ TrackID appears after draft save  
✓ All 4 steps log successfully  
✓ Deployment exists in Omeda  
✓ Content matches WordPress  
✓ Date calculated correctly (next hour)  
✓ Updates work without creating duplicates  
✓ Publish updates date correctly  
✓ No PHP errors in logs  
✓ No JavaScript errors in console  
✓ Performance acceptable (< 5 sec for draft)

---

## Test Environment Info

### Using wp-env
```bash
# Start environment
cd /home/jts/development/NRS/Projects/wp_omeda_newsletter_connector
wp-env start

# Access site
URL: http://localhost:8889
Admin: http://localhost:8889/wp-admin
Username: admin
Password: password

# Check logs
wp-env run cli wp plugin list
wp-env run cli tail -f wp-content/debug.log
```

### Manual Testing Checklist
- [ ] Test 1: Create new draft ✓
- [ ] Test 2: Update draft ✓
- [ ] Test 3: Publish draft ✓
- [ ] Test 4A: Invalid API ✓
- [ ] Test 4B: Invalid query ✓
- [ ] Test 4C: Missing fields ✓
- [ ] Test 5: Multiple saves ✓
- [ ] Test 6: Auto-detection ✓
- [ ] Test 7: Date calculation ✓

---

## Automated Testing (Future)

### PHPUnit Tests to Add
```php
// Test date calculation
testNextHourCalculation()
testNextHourAtExactHour()
testNextHourBeforeMidnight()

// Test workflow
testDraftSaveCreatesDeployment()
testDraftUpdatePreservesTrackId()
testPublishUpdatesDate()

// Test error handling
testInvalidApiCredentials()
testInvalidAudienceQuery()
testMissingRequiredFields()
```

---

**Version**: 1.5.0  
**Last Updated**: October 29, 2025  
**Tested By**: Josh Stogner  
**Test Duration**: ~30 minutes for full suite
