# Changelog - Version 1.12.0

**Release Date:** 2025-10-29  
**Type:** Minor Update - Bug fixes and logging enhancements

## Changes

### 🐛 Bug Fixes
- **Enhanced Error Logging**: Fixed issue where API request/response details were not visible in workflow logs during errors
  - Added `log_error_details()` method to workflow manager that always logs error context regardless of logging level
  - Updated `handle_add_content()` error handler to use new logging method
  - Error details now always include:
    - Request URL and method
    - HTTP status code
    - Request payload (what was sent to Omeda)
    - Response body (what Omeda returned)
    - Individual error messages from Omeda API

### 📝 Logging Improvements
- API error responses are now fully captured and displayed in workflow logs
- Raw request/response data is logged when errors occur, making debugging easier
- Error context is preserved even when logging level is set to "basic" or "advanced"

### 📦 Version Management
- Updated plugin version from 1.11.0 to 1.12.0
- Version now increments following semantic versioning:
  - Major version (X.0.0): New feature sets
  - Minor version (1.X.0): Bug fixes and enhancements
  - Patch version (1.12.X): Critical hotfixes

## Files Modified
1. `omeda-wp-integration.php` - Updated version to 1.12.0
2. `includes/class-omeda-workflow-manager.php` - Added `log_error_details()` method
3. `includes/class-omeda-async-jobs.php` - Updated error logging in `handle_add_content()`

## Testing Notes
- When an API error occurs during content submission, the workflow logs will now show:
  - The error message in the main log list
  - Full request/response details in expandable RAW context section
  - Individual error messages extracted from Omeda's response

## Next Steps
- Continue monitoring the add_content step to identify the specific API error
- The enhanced logging will now show exactly what's being sent to Omeda and what errors are returned
