# Deployment Types API Integration Fix

**Version:** 1.1.1  
**Date:** 2025-10-29  
**Type:** Patch Release (Bug Fix)

---

## Issue

Deployment types dropdown was not populating even with valid API credentials configured.

### Root Cause

The API integration was using incorrect endpoint and response parsing based on assumptions rather than official Omeda API documentation.

**Incorrect Implementation:**
```php
// Wrong endpoint (double asterisk)
$endpoint = 'deploymenttypes/**';

// Wrong response parsing (assumed flat array)
foreach ($data as $item) {
    if (isset($item['Id']) && isset($item['Name'])) {
        $formatted[$item['Id']] = $item['Name'];
    }
}
```

---

## Official API Documentation

**Source:** https://knowledgebase.omeda.com/omedaclientkb/deployment-type-lookup-by-brand-api

### Correct Endpoint

```
Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/deploymenttypes/*
Testing:    https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/deploymenttypes/*
```

**Key Detail:** Single asterisk (`/*`), not double (`/**`)

### Actual Response Structure

```json
{
  "SubmissionId": "C95AE90C-BEC6-41F2-91E2-2BA9168D1D1F",
  "Id": 3000,
  "Description": "AppDev Today",
  "BrandAbbrev": "APPDEV",
  "DeploymentTypes": [
    {
      "Id": 2344,
      "Name": "Framework Building",
      "Description": "Framework Building",
      "LongDescription": "Optional long description",
      "AlternateId": "Frmwk Bldg",
      "StatusCode": 1
    }
  ]
}
```

**Key Details:**
- Response is wrapped in an object with `DeploymentTypes` array
- Each deployment type has `Name` field (primary) and `Description` field (fallback)
- `StatusCode`: 0 = Inactive, 1 = Active
- Only active deployment types should be shown

---

## Fix Applied

### 1. API Client (`class-omeda-api-client.php`)

**Changed:**
```php
/**
 * Get all available Deployment Types for the configured brand.
 * GET /brand/{brandAbbreviation}/deploymenttypes/*
 * 
 * Response structure per Omeda API documentation:
 * {
 *   "DeploymentTypes": [
 *     {"Id": 2344, "Name": "...", "StatusCode": 1}
 *   ]
 * }
 */
public function get_deployment_types() {
    $endpoint = 'deploymenttypes/*';  // ← Fixed: single asterisk
    return $this->send_request($endpoint, 'GET');
}
```

### 2. Data Manager (`class-omeda-data-manager.php`)

**Changed:**
```php
/**
 * Helper to format the raw API response into a simple ID => Name array.
 */
private static function format_api_response($data) {
    $formatted = [];
    
    // Per API docs, deployment types are in a "DeploymentTypes" array
    if (isset($data['DeploymentTypes']) && is_array($data['DeploymentTypes'])) {
        foreach ($data['DeploymentTypes'] as $item) {
            // Use "Name" field per API spec, fall back to "Description"
            $name = $item['Name'] ?? $item['Description'] ?? 'Unknown';
            
            // Only include active deployment types (StatusCode = 1)
            if (isset($item['Id']) && isset($item['StatusCode']) && $item['StatusCode'] == 1) {
                $formatted[$item['Id']] = $name;
            }
        }
    }
    
    return $formatted;
}
```

### 3. Version Bump

**Updated:**
- `omeda-wp-integration.php`: Version 1.1.0 → 1.1.1
- `OMEDA_WP_VERSION` constant: 1.1.0 → 1.1.1
- `CHANGELOG.md`: Added 1.1.1 entry

---

## Testing

### Test with Valid Credentials

**Prerequisites:**
1. Valid Omeda API credentials configured
2. Valid brand abbreviation set
3. Access to staging or production Omeda API

**Test Steps:**
```bash
# 1. Clear cache
wp-env run cli wp transient delete omeda_deployment_types_cache

# 2. Test API call directly
wp-env run cli wp eval "
  \$api = new Omeda_API_Client();
  \$response = \$api->get_deployment_types();
  print_r(\$response);
"

# Expected output:
# Array with "DeploymentTypes" key containing deployment type objects
```

**Expected Results:**
- ✅ API returns 200 OK
- ✅ Response has `DeploymentTypes` array
- ✅ Each item has `Id`, `Name`, `StatusCode`
- ✅ Only active types (StatusCode=1) are shown in dropdown

### Test Without Credentials

**Test Steps:**
```bash
# Visit deployment type creation page
http://localhost:8889/wp-admin/post-new.php?post_type=omeda_deploy_type
```

**Expected Results:**
- ✅ Page loads without fatal error
- ✅ Dropdown shows: "Error: API Credentials, Brand Abbreviation, or Default User ID are missing."
- ✅ Other fields remain editable

---

## API Response Handling

### Success Response

```json
{
  "SubmissionId": "C95AE90C-BEC6-41F2-91E2-2BA9168D1D1F",
  "Id": 3000,
  "Description": "Brand Name",
  "BrandAbbrev": "ABBREV",
  "DeploymentTypes": [
    {
      "Id": 100,
      "Name": "Daily Newsletter",
      "Description": "Daily Newsletter",
      "AlternateId": "DAILY",
      "StatusCode": 1
    },
    {
      "Id": 101,
      "Name": "Weekly Digest",
      "Description": "Weekly Digest",
      "AlternateId": "WEEKLY",
      "StatusCode": 1
    },
    {
      "Id": 102,
      "Name": "Deprecated Newsletter",
      "Description": "Old Newsletter",
      "AlternateId": "OLD",
      "StatusCode": 0
    }
  ]
}
```

**Formatted Output (used in dropdown):**
```php
[
  100 => "Daily Newsletter",
  101 => "Weekly Digest"
  // Note: ID 102 excluded (StatusCode = 0, inactive)
]
```

### Error Response (404)

```json
{
  "SubmissionId": "ec0c2ba6-13f4-4934-8efa-74c2ccb33f1d",
  "Errors": [
    {
      "Error": "Brand 12345 was not found."
    }
  ]
}
```

**Handling:**
- Exception thrown by `Omeda_API_Client`
- Caught by `Omeda_Data_Manager`
- Returned as `WP_Error`
- Displayed in dropdown with refresh button

---

## Comparison: Before vs After

### Before (1.1.0)

**Endpoint:**
```
https://ows.omeda.com/webservices/rest/brand/ABBREV/deploymenttypes/**
                                                                    ^^
```
❌ **Result:** 404 Not Found (invalid endpoint)

**Response Parsing:**
```php
foreach ($data as $item) {
    if (isset($item['Id']) && isset($item['Name'])) {
        $formatted[$item['Id']] = $item['Name'];
    }
}
```
❌ **Result:** Empty array (wrong structure)

**User Impact:**
- Deployment types dropdown always empty
- No error message shown
- Confusing for users with valid credentials

### After (1.1.1)

**Endpoint:**
```
https://ows.omeda.com/webservices/rest/brand/ABBREV/deploymenttypes/*
                                                                    ^
```
✅ **Result:** 200 OK (valid endpoint)

**Response Parsing:**
```php
if (isset($data['DeploymentTypes']) && is_array($data['DeploymentTypes'])) {
    foreach ($data['DeploymentTypes'] as $item) {
        $name = $item['Name'] ?? $item['Description'] ?? 'Unknown';
        if (isset($item['StatusCode']) && $item['StatusCode'] == 1) {
            $formatted[$item['Id']] = $name;
        }
    }
}
```
✅ **Result:** Correctly formatted array

**User Impact:**
- ✅ Deployment types populate correctly
- ✅ Only active types shown
- ✅ Clear error messages when credentials missing

---

## HTTP Headers Required

Per Omeda API documentation:

```
x-omeda-appid: {your-app-id}
Content-Type: application/json
```

**Implementation:**
```php
$args = [
    'method'  => 'GET',
    'headers' => [
        'x-omeda-appid' => $this->app_id,
        'User-Agent'    => 'Omeda_WP_Integration/' . OMEDA_WP_VERSION,
    ],
    'timeout' => 60,
];
```

---

## Caching Strategy

**Implementation:**
```php
const DEPLOYMENT_TYPES_TRANSIENT = 'omeda_deployment_types_cache';
const CACHE_DURATION = DAY_IN_SECONDS; // 24 hours
```

**Benefits:**
- Reduces API calls
- Faster page loads
- Respects API rate limits

**Cache Invalidation:**
- Automatic after 24 hours
- Manual via "Refresh" button
- Force refresh: `get_deployment_types(true)`

**Commands:**
```bash
# View cached data
wp-env run cli wp transient get omeda_deployment_types_cache

# Clear cache
wp-env run cli wp transient delete omeda_deployment_types_cache
```

---

## StatusCode Reference

Per Omeda API documentation:

| StatusCode | Description | Show in Dropdown? |
|------------|-------------|-------------------|
| 0          | Inactive    | ❌ No             |
| 1          | Active      | ✅ Yes            |

**Implementation:**
```php
// Only include active deployment types
if (isset($item['StatusCode']) && $item['StatusCode'] == 1) {
    $formatted[$item['Id']] = $name;
}
```

---

## Related Documentation

- **Omeda API Docs:** https://knowledgebase.omeda.com/omedaclientkb/deployment-type-lookup-by-brand-api
- **Plugin Changelog:** `/CHANGELOG.md`
- **Version Update Guide:** `/docs/VERSION_UPDATE_GUIDE.md`
- **API Client Code:** `/src/omeda-newsletter-connector/includes/class-omeda-api-client.php`
- **Data Manager Code:** `/src/omeda-newsletter-connector/includes/class-omeda-data-manager.php`

---

## Rollback Plan

If issues arise with 1.1.1:

```bash
# Checkout previous version
git checkout v1.1.0 -- src/omeda-newsletter-connector/

# Or manually revert changes:
# 1. Change endpoint back to 'deploymenttypes/**'
# 2. Revert format_api_response() method
# 3. Update version to 1.1.0
```

---

**Status:** ✅ Fixed and Tested  
**Version:** 1.1.1  
**Release Type:** Patch (Bug Fix)  
**Breaking Changes:** None  
**Migration Required:** No
