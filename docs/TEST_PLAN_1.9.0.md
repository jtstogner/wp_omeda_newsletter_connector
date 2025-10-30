# Test Plan - Version 1.9.0
## Deployment Name & Campaign ID Format Feature

### Test Environment
- WordPress wp-env (http://localhost:8889)
- Admin credentials: admin:password
- Plugin: Omeda WordPress Integration v1.9.0

---

## Test Suite 1: Deployment Type Configuration

### Test 1.1: Add Formats to New Deployment Type
**Steps:**
1. Go to Omeda Integration → Deployment Types
2. Click "Add New"
3. Enter title: "Test Newsletter Format"
4. Select Omeda Deployment Type from dropdown
5. Enter Deployment Name Format: `{site_name} - {post_title} ({post_date_ymd})`
6. Enter Campaign ID Format: `test-{post_id}-{post_date_ymd}`
7. Fill other required fields
8. Click "Publish"

**Expected:**
- ✓ Deployment type saves successfully
- ✓ Format fields visible and saved
- ✓ Can edit and update formats

### Test 1.2: Edit Existing Deployment Type
**Steps:**
1. Go to Omeda Integration → Deployment Types
2. Edit "Just Josh" deployment type
3. Add Deployment Name Format: `{post_title} - {author_name}`
4. Add Campaign ID Format: `jj-{post_id}`
5. Click "Update"

**Expected:**
- ✓ Formats save to existing deployment type
- ✓ No errors on save
- ✓ Formats persist after refresh

### Test 1.3: Empty Formats (Backward Compatibility)
**Steps:**
1. Create deployment type without entering formats
2. Leave both format fields empty
3. Save deployment type

**Expected:**
- ✓ Saves successfully
- ✓ No errors or warnings
- ✓ Will use post title as deployment name (old behavior)

---

## Test Suite 2: Newsletter Post Creation

### Test 2.1: New Post with Format
**Steps:**
1. Create new Newsletter Glue post
2. Select deployment type that has formats configured
3. Check Omeda Deployment metabox
4. Observe Deployment Name field placeholder
5. Observe Campaign ID field placeholder

**Expected:**
- ✓ Deployment Name shows parsed placeholder (e.g., "My Site - My Post (20251029)")
- ✓ Campaign ID shows parsed placeholder (e.g., "test-112-20251029")
- ✓ Description shows format being used
- ✓ Both fields empty (using format)

### Test 2.2: Save Draft - Auto Format
**Steps:**
1. Continue from Test 2.1
2. Leave both format fields empty
3. Add post title: "Halloween Newsletter Special"
4. Save as Draft
5. Check workflow logs
6. Check Omeda dashboard

**Expected:**
- ✓ Deployment created in Omeda
- ✓ Deployment name follows format with post title
- ✓ Campaign ID generated from format
- ✓ No errors in workflow log

### Test 2.3: Manual Override Before Save
**Steps:**
1. Create new post with deployment type
2. Enter custom Deployment Name: "CUSTOM: Special Edition"
3. Enter custom Campaign ID: "custom-special-2025"
4. Save as Draft

**Expected:**
- ✓ Deployment created with custom name (not format)
- ✓ Campaign ID uses custom value (not format)
- ✓ Values saved to post meta
- ✓ Appears correctly in Omeda

### Test 2.4: Fields Locked After Creation
**Steps:**
1. Continue from Test 2.2 or 2.3
2. Refresh post edit page
3. Try to edit Deployment Name field
4. Try to edit Campaign ID field

**Expected:**
- ✓ Both fields are readonly (locked)
- ✓ Cannot change values after deployment created
- ✓ Values displayed correctly
- ✓ No edit possible

---

## Test Suite 3: Variable Parsing

### Test 3.1: New Variables - Post ID
**Setup Format:** `newsletter-{post_id}-{post_date_ymd}`

**Expected Parse:**
- Post ID 112 → `newsletter-112-20251029`
- ✓ Numeric ID included
- ✓ No leading zeros or formatting

### Test 3.2: New Variables - Post Slug
**Setup Format:** `{site_name}/{post_slug}`

**Expected Parse:**
- Post slug "halloween-special" → `My Site/halloween-special`
- ✓ Slug correctly retrieved
- ✓ URL-safe characters maintained

### Test 3.3: New Variables - Date YMD
**Setup Format:** `{post_date_ymd}-{post_title}`

**Expected Parse:**
- October 29, 2025 → `20251029-Post Title`
- ✓ Format is YYYYMMDD
- ✓ No dashes or spaces in date
- ✓ Consistent format

### Test 3.4: Combined Variables
**Setup Format:** `{author_name} - {post_title} ({post_date_F} {post_date_d}, {post_date_Y})`

**Expected Parse:**
- `Josh Stogner - Halloween Special (October 29, 2025)`
- ✓ All variables parse correctly
- ✓ Formatting preserved
- ✓ No parsing errors

---

## Test Suite 4: Edge Cases

### Test 4.1: Empty Post Title
**Steps:**
1. Create post with no title
2. Use format with `{post_title}`
3. Save draft

**Expected:**
- ✓ Deployment name includes empty space for title
- ✓ Other variables still parse
- ✓ No fatal errors

### Test 4.2: Special Characters in Format
**Setup Format:** `{post_title} | {site_name} • {post_date}`

**Expected:**
- ✓ Special characters (|, •) preserved
- ✓ Variables parse around special chars
- ✓ Deployment name appears correctly

### Test 4.3: Missing Variable Data
**Setup Format:** `{post_title} by {author_first_name}`

**Test:** Post with author who has no first name set

**Expected:**
- ✓ Missing data shows as empty string
- ✓ Format still applies
- ✓ No PHP errors

### Test 4.4: Very Long Format Result
**Setup Format:** `{site_name} - {post_title} - {post_date_F} {post_date_d}, {post_date_Y} - {category} - {author_name}`

**Expected:**
- ✓ Full string generated
- ✓ Omeda API accepts long name
- ✓ No truncation errors

---

## Test Suite 5: Integration Tests

### Test 5.1: Full Workflow with Formats
**Steps:**
1. Create deployment type with formats
2. Create new post
3. Save draft (deployment created)
4. Update content
5. Send test email
6. Schedule deployment

**Expected:**
- ✓ All workflow steps complete
- ✓ Deployment name consistent throughout
- ✓ Campaign ID maintained
- ✓ Test email received
- ✓ Scheduling works

### Test 5.2: Multiple Posts, Same Format
**Steps:**
1. Create 3 posts using same deployment type
2. Use format with `{post_id}`
3. Save all as drafts

**Expected:**
- ✓ Each gets unique deployment name
- ✓ Post IDs differentiate deployments
- ✓ All appear separately in Omeda
- ✓ No collisions or duplicates

### Test 5.3: Format Update Does Not Affect Existing
**Steps:**
1. Create post, save draft (deployment created)
2. Edit deployment type format
3. Check existing post
4. Create new post with updated format

**Expected:**
- ✓ Existing post unchanged
- ✓ Deployment name locked at creation time
- ✓ New post uses updated format
- ✓ Clear separation

---

## Test Suite 6: Backward Compatibility

### Test 6.1: Existing Deployments Unchanged
**Steps:**
1. Check existing deployments (pre-1.9.0)
2. Verify deployment names
3. Edit existing posts

**Expected:**
- ✓ Old deployments unaffected
- ✓ No retroactive format application
- ✓ Can still edit content
- ✓ Workflow continues normally

### Test 6.2: Deployment Type Without Formats
**Steps:**
1. Use old deployment type (no formats configured)
2. Create new post
3. Save draft

**Expected:**
- ✓ Uses post title as deployment name (old behavior)
- ✓ No campaign ID sent (Omeda generates)
- ✓ Works exactly as before 1.9.0
- ✓ No breaking changes

---

## Test Suite 7: UI/UX Tests

### Test 7.1: Placeholder Values Display
**Steps:**
1. Select deployment type with formats
2. Check placeholder text in fields
3. Change post title
4. Refresh page

**Expected:**
- ✓ Placeholder shows actual parsed value
- ✓ Placeholder updates with post data
- ✓ Helps user understand what will be generated
- ✓ Clear and readable

### Test 7.2: Field Descriptions
**Steps:**
1. Check deployment type edit page
2. Read field descriptions
3. Check post edit metabox descriptions

**Expected:**
- ✓ Clear explanation of variables
- ✓ Examples provided
- ✓ Format shown in description
- ✓ User understands purpose

### Test 7.3: Readonly State Visual
**Steps:**
1. Create deployment
2. Check field appearance after locked

**Expected:**
- ✓ Fields visually appear readonly
- ✓ Cannot focus/edit locked fields
- ✓ Values still visible
- ✓ Clear indication of locked state

---

## Test Suite 8: Error Handling

### Test 8.1: Invalid Variable Syntax
**Setup Format:** `{post-title}` (dash instead of underscore)

**Expected:**
- ✓ Variable not parsed (literal string)
- ✓ No PHP errors
- ✓ Deployment name includes literal `{post-title}`
- ✓ User sees issue in result

### Test 8.2: Omeda API Rejection
**Setup:** Campaign ID too long or invalid characters

**Expected:**
- ✓ Workflow logs show API error
- ✓ Clear error message
- ✓ Deployment creation fails gracefully
- ✓ User notified

### Test 8.3: Empty Format Result
**Setup Format:** `{author_first_name}` (when author has no first name)

**Expected:**
- ✓ Empty string sent to API
- ✓ Falls back to post title or Omeda default
- ✓ No fatal errors
- ✓ Workflow continues

---

## Test Suite 9: Performance Tests

### Test 9.1: Variable Parsing Speed
**Test:** Parse format with 10+ variables

**Expected:**
- ✓ Completes in < 100ms
- ✓ No noticeable delay
- ✓ Page loads normally

### Test 9.2: Bulk Operations
**Test:** Create 50 posts with formats simultaneously

**Expected:**
- ✓ All deployments created
- ✓ Unique names generated
- ✓ No timeouts or failures
- ✓ Server handles load

---

## Regression Tests

### Regression 1: Deployment Creation
**Test:** Ensure deployment creation still works

**Expected:**
- ✓ Draft save creates deployment
- ✓ TrackID assigned
- ✓ Workflow completes

### Regression 2: Content Updates
**Test:** Update post content after deployment created

**Expected:**
- ✓ Content syncs to Omeda
- ✓ Deployment name unchanged
- ✓ No duplicate deployments

### Regression 3: Scheduling
**Test:** Schedule deployment with custom date

**Expected:**
- ✓ Schedule button works
- ✓ Date updates in Omeda
- ✓ Unschedule button appears

---

## Acceptance Criteria

### Must Pass (Blockers)
- [ ] All Test Suite 1 tests (Configuration)
- [ ] All Test Suite 2 tests (Creation)
- [ ] All Test Suite 3 tests (Variables)
- [ ] All Test Suite 6 tests (Compatibility)
- [ ] All Regression tests

### Should Pass (High Priority)
- [ ] All Test Suite 4 tests (Edge Cases)
- [ ] All Test Suite 5 tests (Integration)
- [ ] Test Suite 7 tests (UX)

### Nice to Pass (Medium Priority)
- [ ] Test Suite 8 tests (Error Handling)
- [ ] Test Suite 9 tests (Performance)

---

## Test Results Template

```
Test: [Test ID]
Date: [Date]
Tester: [Name]
Environment: [wp-env/staging/production]
Result: [PASS/FAIL]
Notes: [Any observations]
Screenshots: [If applicable]
```

---

## Sign-Off

### Development Sign-Off
- [ ] All features implemented
- [ ] Code reviewed
- [ ] Documentation complete
- [ ] Version numbers updated

### Testing Sign-Off  
- [ ] All must-pass tests passed
- [ ] All high-priority tests passed
- [ ] Edge cases documented
- [ ] Regression tests passed

### Deployment Sign-Off
- [ ] Changelog updated
- [ ] Release notes prepared
- [ ] Migration guide reviewed
- [ ] Ready for production

---

**Version:** 1.9.0  
**Date:** 2025-10-29  
**Status:** Ready for Testing
