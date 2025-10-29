# Changelog

All notable changes to the Omeda WordPress Integration plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Version Format
- **Major (X.0.0)**: New feature sets, breaking changes, major architectural updates
- **Minor (1.X.0)**: New features, enhancements, non-breaking changes
- **Patch (1.1.X)**: Bug fixes, minor improvements, documentation updates

---

## [1.7.1] - 2025-10-29

### Fixed
- **Critical Fix**: Action Scheduler queue runner not executing immediately
  - Added manual queue runner trigger for immediate job execution in development environments
  - Deployments now execute immediately upon save as intended
  - Fixed issue where jobs were scheduled but not processed until next WP-Cron trigger
  - Added spawn_cron() calls for WP-Cron to ensure immediate execution in production

### Technical Details
- Implemented `trigger_action_scheduler_queue()` method to manually run Action Scheduler queue
- Queue runner is now triggered automatically when scheduling jobs with 0 delay
- Ensures jobs execute immediately in wp-env and other development environments
- Production WP-Cron also gets immediate trigger via spawn_cron()

---

## [1.7.0] - 2025-10-29

### Added
- **Major Feature**: Immediate deployment workflow execution
  - Deployments now created immediately when draft is saved (no 5-minute delay)
  - Audience assignment and content upload execute immediately in sequence
  - Eliminates artificial delays to minimize time from save to deployment ready
- **UI Enhancement**: Manual deployment control buttons
  - "Send Test Email" button becomes available once deployment is ready
  - "Schedule Deployment" section with datetime picker and confirmation checkbox
  - "Unschedule" button for scheduled deployments with confirmation dialog
  - Real-time UI updates showing deployment status and last test sent time
- **AJAX Handlers**: Added REST-style endpoints for manual actions
  - `omeda_send_test`: Trigger test email send on demand
  - `omeda_schedule_deployment`: Schedule deployment with custom date/time
  - `omeda_unschedule_deployment`: Cancel scheduled deployment
  - All handlers include nonce security and capability checks
- **Admin JavaScript**: Interactive UI for deployment management
  - Real-time button state management (enabled/disabled based on workflow state)
  - Datetime picker with automatic UTC conversion
  - Loading animations during AJAX requests
  - User-friendly success/error dialogs
- **Meta Fields**: New post meta for tracking deployment state
  - `_omeda_deployment_ready`: Boolean flag when deployment is fully created
  - `_omeda_test_sent`: Timestamp of last test email sent
  - `_omeda_deployment_scheduled`: Timestamp when deployment was scheduled
  - `_omeda_schedule_date`: The actual deployment send date/time in UTC

### Changed
- **Workflow Timing**: Removed debouncing delays from deployment creation
  - `schedule_create_deployment()` default changed from 300 seconds to 0
  - `schedule_update_content()` default changed from 60 seconds to 0
  - Job handlers now schedule next job with `time()` instead of `time() + 30`
  - Only delay remaining is for immediate publish scenario (configurable via settings)
- **Job Execution**: Updated step logging from "1/5, 2/5..." to "1/3, 2/3, 3/3"
  - Steps: Create Deployment → Assign Audience → Add Content
  - Test and Schedule are now manual actions, not automatic workflow steps
  - More accurate representation of automated workflow completion
- **Meta Box UI**: Enhanced visual layout and information display
  - Added action buttons section with conditional rendering based on state
  - Improved status indicators with icons and color coding
  - Schedule section with datetime input and confirmation workflow
  - Pending jobs list now handles both Action Scheduler and WP-Cron formats

### Fixed
- **Job Scheduling**: Fixed datetime format inconsistency in pending jobs display
  - WP-Cron jobs now correctly show timestamp via `$job['scheduled']->date`
  - Action Scheduler jobs use `$job['scheduled']->format('Y-m-d H:i:s')`
  - Prevents fatal errors when displaying pending job information

### Technical Details
- **New Public Methods** in `Omeda_Async_Jobs`:
  - `send_test_email($post_id, $track_id, $config_id)`: Manual test trigger
  - `schedule_deployment($post_id, $track_id, $config_id, $schedule_date)`: Manual scheduling
  - `unschedule_deployment($post_id, $track_id)`: Cancel scheduled deployment
  - All return `['success' => bool, 'message' => string]` format
- **Updated Job Handlers**:
  - `handle_create_deployment()`: Sets `_omeda_deployment_ready` meta on completion
  - `handle_add_content()`: Final step logs "Deployment ready" message
  - Removed automatic test sending from job chain
- **AJAX Integration** in `class-omeda-hooks.php`:
  - Added 4 new methods: `ajax_send_test()`, `ajax_schedule_deployment()`, `ajax_unschedule_deployment()`, `enqueue_admin_scripts()`
  - Scripts enqueued only on post edit screens for supported post types
  - Nonce verification and capability checks on all AJAX endpoints
- **Asset Management**:
  - Created `/assets/js/omeda-admin.js` with jQuery handlers for all UI interactions
  - Includes loading animations with CSS keyframe animation
  - UTC conversion for datetime-local input values
  - Confirmation dialogs for destructive actions

### Migration Notes
- **For Existing Deployments**: No migration needed - workflow continues from current state
- **For Development**: Action Scheduler still used in dev/staging environments
- **For Production**: WP-Cron used as configured (no behavior change)
- **UI Changes**: Users will see new buttons once deployment reaches "ready" state

---

## [1.6.2] - 2025-10-29

### Fixed
- **Critical**: Fixed async job execution stalling after scheduling
  - Jobs were scheduled but not executing (workflow stopped after "Deployment creation scheduled" message)
  - Added validation for `DeploymentTypeId` before API call to catch configuration errors early
  - Fixed deployment date calculation - now uses next nearest hour instead of far-future placeholder
  - Updated job handlers to properly schedule next job in chain based on environment (Action Scheduler vs WP-Cron)
  - Jobs now respect environment configuration throughout entire workflow
- **Enhancement**: Added timestamps to workflow log display in UI
  - Log entries now show full timestamp inline: `[2025-10-29 16:42:28] [INFO] message`
  - Previously timestamps only shown on hover
  - Improves readability and debugging experience
- **Enhancement**: Better error messages for missing configuration
  - Clear error when `DeploymentTypeId` is not configured: "DeploymentTypeId is required but not configured"
  - Helps identify configuration issues before API calls fail

### Changed
- Version bumped from 1.6.1 to 1.6.2 (patch release - bug fixes and minor enhancements)
- Deployment date logic updated in async job handler
  - Now calculates next nearest hour: `ceil(time() / 3600) * 3600`
  - Format: `gmdate('Y-m-d H:i', $next_hour_timestamp)`
  - More realistic temporary date for draft deployments

### Technical Details
- Modified `handle_create_deployment()` in `class-omeda-async-jobs.php`
  - Added validation: `if (empty($config['DeploymentTypeId'])) throw new Exception(...)`
  - Updated date calculation from `'2099-01-01 12:00'` to dynamic next-hour calculation
  - Added environment-aware job scheduling using `get_scheduling_method()`
- Modified `handle_assign_audience()` to use environment-aware scheduling
  - Checks for Action Scheduler vs WP-Cron availability
  - Properly chains to next job based on configured method
- Updated `render_meta_box()` in `class-omeda-hooks.php`
  - Changed from `title="%s"` (timestamp on hover) to inline display `[%s]`
  - Log format now: `<div>[timestamp] [level] message</div>`
  - Consistent with production logging best practices

### Root Cause Analysis
- **Issue**: Jobs scheduled via Action Scheduler but subsequent jobs not executing
- **Cause 1**: Hard-coded `as_schedule_single_action()` calls in job handlers
  - Did not check environment configuration
  - Production WP-Cron jobs were never scheduled in chain
- **Cause 2**: Missing `DeploymentTypeId` validation
  - API calls failed silently without clear error messages
  - Workflow appeared to stall without explanation
- **Solution**: Environment-aware scheduling + early validation

### Testing Notes
- Save newsletter draft and check workflow logs
- Verify all 5 steps execute: Create → Assign → Add Content → (publish) → Test → Schedule
- Confirm timestamps appear in log display
- Test with both Action Scheduler (dev) and WP-Cron (production) environments
- Verify error message when deployment type not configured

### Benefits
- **Reliability**: Workflow completes all steps without stalling
- **Debugging**: Clear error messages identify configuration issues
- **Visibility**: Timestamps in logs improve troubleshooting
- **Flexibility**: Works correctly in both dev and production environments

---

## [1.6.1] - 2025-10-29

### Fixed
- **Critical**: Added support for `newsletterglue` post type in workflow hooks
  - Workflow was not triggering for Newsletter Glue campaigns (custom post type)
  - Only 'post' post type was supported previously
  - Now supports both 'post' and 'newsletterglue' post types
  - Meta box appears on both post types
  - Save hooks trigger correctly for Newsletter Glue campaigns
  - Status transition hooks work for both types
- **Debugging**: Root cause - `get_supported_post_types()` only returned `['post']`
  - Newsletter Glue campaigns use `newsletterglue` custom post type
  - Hooks were not registering for this post type
  - Workflow logs not appearing because workflow never triggered
  - Fix: Updated to return `['post', 'newsletterglue']`

### Changed
- Version bumped from 1.6.0 to 1.6.1 (patch release - bug fix)

### Technical Details
- Modified `Omeda_Hooks::get_supported_post_types()` method
- Added 'newsletterglue' to supported post types array
- No database changes required
- Existing functionality for regular posts unchanged

### Testing Notes
- Verify meta box appears on Newsletter Glue campaign edit screen
- Confirm workflow logs populate when saving draft
- Check that deployment is created in Omeda
- Validate TrackID is stored in post meta

### Migration Notes
- No migration required
- Existing posts unaffected
- Newsletter Glue campaigns will now work immediately
- No configuration changes needed

---

## [1.6.0] - 2025-10-29

### Added
- **Workflow Logs Page**: New admin page for comprehensive workflow log viewing
  - Accessible via: Omeda Integration → Workflow Logs
  - Lists all posts that have workflow logs with pagination
  - Shows post ID, title, type, status, and last modified date
  - Click "View Logs" to see detailed log entries for any post
  - Log detail view shows:
    - Current workflow state
    - Omeda deployment ID (TrackID)
    - All log entries with timestamp, level, message, and context
    - Color-coded log levels (ERROR: red, WARN: yellow, INFO: green)
    - Sorted by timestamp (most recent first)
  - Quick links to edit post from logs page
  - Replaces need to check debug.log or database directly
  - Better debugging and troubleshooting experience

### Changed
- **Menu Structure**: Reorganized admin menu items
  - Settings remains first submenu item
  - New "Workflow Logs" second submenu item
  - "Background Jobs" (Action Scheduler) moved to third position
  - More logical organization with logs next to main settings
- **Version**: Bumped from 1.5.0 to 1.6.0 (minor release - new feature)
- **Admin Experience**: Improved workflow visibility and debugging
  - No longer need direct database access to view logs
  - All workflow information in one place
  - Better user experience for administrators

### Technical Details
- New `workflow_logs_page_html()` method in `Omeda_Settings` class
- Queries `_omeda_workflow_log` post meta to find posts with logs
- Uses WP_Query for pagination (20 posts per page)
- Displays individual log entries stored as JSON
- Logs sorted by timestamp (descending)
- Context data displayed in readable format with `print_r()`
- Direct SQL queries for efficiency with large datasets
- Maintains compatibility with existing log storage format

### Benefits
- **Better Debugging**: See workflow status without checking debug.log
- **Centralized Logs**: All workflow information in one admin page
- **User Friendly**: No technical knowledge required to view logs
- **Historical View**: See all workflow executions across all posts
- **Quick Access**: Direct links from logs to post editor
- **Professional UI**: Consistent with WordPress admin standards

### Migration Notes
- No database changes required
- Existing logs immediately visible in new interface
- Menu automatically updated on plugin load
- All existing functionality maintained
- New page requires `manage_options` capability

---

## [1.5.0] - 2025-10-29

### Added
- **Workflow Monitor Page**: New admin page to view all running and scheduled tasks
  - Real-time view of pending workflow tasks across all posts
  - Shows task type, scheduled time, status, and retry count
  - Color-coded status indicators (pending, scheduled, running)
  - Links directly to post edit pages for easy access
  - Works with both Action Scheduler (dev) and WP-Cron (production)
  - Located at: Omeda Integration → Workflow Monitor
- **Workflow Status Metabox**: Added to post edit screen sidebar
  - Shows Omeda Track ID when deployment exists
  - Lists all pending tasks for the current post
  - Displays recent activity log (last 5 entries)
  - Color-coded log levels (INFO: blue, WARN: orange, ERROR: red)
  - Real-time status updates as tasks are processed
  - Helps troubleshoot deployment issues
- **Recent Workflow Logs Section**: On monitor page
  - Shows workflow logs from last 10 posts
  - Organized by post with expandable log entries
  - Timestamp and severity level for each log entry
  - Helps identify patterns and recurring issues
- **Draft Deployment Creation**: Deployments now created immediately when saving draft
  - Creates deployment in Omeda on first draft save with deployment type
  - Sets initial deployment date to next nearest hour (temporary placeholder)
  - Assigns audience immediately during draft creation
  - Sends content and subject to Omeda during draft creation
  - Deployment date updated to actual publish/schedule date when post published
  - Improved workflow logging with 5-step process indicator
- **Enhanced Workflow Logging**: Better status messages for deployment tracking
  - Step 1/5: Deployment creation with TrackID
  - Step 2/5: Audience assignment confirmation
  - Step 3/5: Initial content upload confirmation
  - Step 4/5: Test email sent (on publish)
  - Step 5/5: Final deployment scheduled
  - Clear messaging that date will update on publish

### Changed
- **Deployment Date Logic**: Updated from far-future placeholder to next-hour logic
  - Old: Used `2099-01-01 12:00` as placeholder date
  - New: Calculates next nearest hour from current time
  - Example: If saved at 2:30 PM, deployment date set to 3:00 PM
  - More realistic temporary date improves testing and validation
  - Final date still updated when post actually published/scheduled
- **Version**: Bumped from 1.4.0 to 1.5.0 (minor release - new features)
- **Workflow Visibility**: All workflow steps now visible in real-time
  - Monitor page provides system-wide view
  - Metabox provides per-post view
  - Improves debugging and transparency

### Technical Details
- New `Omeda_Workflow_Monitor` class handles all monitoring functionality
- Next hour calculation: `ceil(time() / 3600) * 3600`
- Date formatted as `Y-m-d H:i` in GMT/UTC timezone
- Monitor queries both Action Scheduler and WP-Cron depending on environment
- Metabox appears on all supported post types (post, ngl_pattern)
- Log entries stored as JSON in post meta `_omeda_workflow_log`
- Workflow manager updated to handle immediate content upload
- Maintains compatibility with scheduled/published post workflow

### Benefits
- **Faster Testing**: See deployment appear in Omeda immediately
- **Better Validation**: Verify content and configuration before publishing
- **Improved UX**: Users get immediate feedback that deployment is created
- **Realistic Dates**: Temporary dates are more meaningful than far-future dates
- **Enhanced Debugging**: See exactly what's running and when
- **Transparency**: Clear visibility into workflow status
- **No Breaking Changes**: Existing workflow for publish/schedule unchanged

### Migration Notes
- No database changes required
- Existing deployments unaffected
- New behavior only applies to new draft saves
- All existing functionality maintained
- Monitor page available immediately after update

---

## [1.4.0] - 2025-10-29

### Added
- **Default Email Settings**: New global settings for default email configuration
  - Default From Name (uses site name if not set)
  - Default From Email (uses admin email if not set)
  - Default Reply To Email (uses admin email if not set)
  - These defaults are used when creating new deployment types
  - Can be overridden per deployment type
  - Located in new "Default Email Settings" section on settings page

### Changed
- **Audience Query Field**: Reverted to simple text input
  - Removed Select2 search functionality (not needed for text input)
  - Simplified field with clear placeholder and description
  - Better instructions for manual entry of Omeda Audience Builder query names
- **UI Improvements**: Deployment type creation now auto-populates email fields
  - From Name, From Email, and Reply To fields pre-filled from global defaults
  - Only applies to new deployment types (auto-draft status)
  - Existing deployment types retain their saved values
- Version bumped from 1.3.0 to 1.4.0 (minor release - new features)

### Technical Details
- Added `omeda_default_from_name`, `omeda_default_from_email`, `omeda_default_reply_to` options
- Settings page includes new `email_defaults_section` with callback
- New `render_email_field()` method for email input fields with validation
- Deployment types check post status to determine when to use defaults
- Select2 initialization simplified (removed audience query selector)

### Documentation
- Settings page includes helpful descriptions for email defaults
- Clear explanation that defaults can be overridden per deployment type

---

## [1.3.0] - 2025-10-29

### Added
- **WordPress Variable Support**: Subject format now supports WordPress variables
  - `{post_title}` - Post title
  - `{post_date}` - Formatted post date
  - `{post_date_Y}`, `{post_date_m}`, `{post_date_d}` - Date components
  - `{post_date_F}`, `{post_date_M}` - Month names
  - `{author_name}`, `{author_first_name}`, `{author_last_name}` - Author information
  - `{site_name}`, `{site_tagline}` - Site information
  - `{category}`, `{categories}`, `{tags}` - Taxonomy information
  - `{excerpt}` - Post excerpt (trimmed to 100 chars)
  - Example: `{post_title} - {site_name}` becomes "My Article - My Website"
  - Combines with Omeda merge tags like `@{mv_html_title_subject}@`
- **New Variable Parser Class**: `Omeda_Variable_Parser` for consistent variable processing
  - Extensible via `omeda_parsed_variables` filter
  - Safe excerpt generation with length limiting
  - Complete author and taxonomy data extraction
- **Enhanced Audience Query Field**: Improved UI and documentation
  - Searchable dropdown styling (ready for future API support)
  - Clear instructions about Omeda Audience Builder integration
  - Case-sensitive query name guidance
- **Improved Subject Format Field**: 
  - Now a required field (best practice)
  - Better description with examples of both WordPress and Omeda variables
  - Helpful placeholder text showing common pattern

### Changed
- Version bumped from 1.2.0 to 1.3.0 (minor release - new features)
- Field name changed from `query_name` to `audience_query_id` (internal consistency)
- Subject format description enhanced with variable examples
- Audience query now uses enhanced text input (future-ready for API integration)

### Technical Details
- Variable parser supports all WordPress date format tokens
- Variables replaced before sending to Omeda API
- Maintains compatibility with existing Omeda merge tags
- No database migration required (backward compatible meta keys)

### Documentation
- Added comprehensive variable documentation in parser class
- Subject field examples show both WordPress and Omeda variables
- Clear guidance on Omeda Audience Builder query naming

---

## [1.2.0] - 2025-10-29

### Added
- **Select2 Integration**: Searchable dropdowns for deployment types and post types
  - Deployment type dropdown now searchable with Select2
  - Post type/template dropdown now searchable with Select2
  - Improved UX for finding specific items in long lists
- **Enhanced Query Name Field**: Better guidance for Audience Builder queries
  - Added placeholder text with example query name
  - Improved description explaining Omeda Audience Builder integration
  - Note that queries are created in Omeda's Audience Builder interface

### Changed
- Version bumped from 1.1.1 to 1.2.0 (minor release - new features)
- Query name field now includes helpful examples and descriptions

### Technical Details
- Select2 v4.1.0 loaded from CDN
- Searchable dropdowns with clear/reset functionality
- No API endpoint available for listing audience queries (by design)
- Audience queries referenced by exact name string from Omeda Audience Builder

### Documentation
- Added `SELECT2_INTEGRATION.md` - Searchable dropdown implementation guide
- Updated field descriptions for better clarity

---

## [1.1.1] - 2025-10-29

### Fixed
- **Critical**: Deployment types API endpoint corrected to match Omeda documentation
  - Changed from `deploymenttypes/**` to `deploymenttypes/*`
- **Critical**: API response parsing updated to handle correct structure
  - Response has `DeploymentTypes` array wrapper per API docs
  - Now uses `Name` field (primary) with `Description` fallback
- **Enhancement**: Only show active deployment types (StatusCode = 1)
- Updated API documentation comments to match actual response structure

### Changed
- Version bumped from 1.1.0 to 1.1.1 (patch release - bug fixes)

---

## [1.1.0] - 2025-10-29

### Added
- Newsletter Glue integration with proper template and category detection
- Dynamic dropdown for Newsletter Glue templates (`ngl_template` post type)
- Dynamic dropdown for Newsletter Glue template categories (`ngl_template_category` taxonomy)
- Newsletter Glue enabled post types detection
- Post type / template assignment dropdown with organized optgroups
- Omeda deployment types dropdown (requires API credentials)
- Refresh button for Omeda deployment types
- Comprehensive error handling for missing API credentials
- 24-hour caching for Omeda API responses
- Auto-detection of deployment configuration based on post type/template
- Support for multiple assignment types:
  - `post_type:post` - All posts of a type
  - `ng_post_type:post` - Only Newsletter Glue enabled posts
  - `ng_template:123` - Specific Newsletter Glue template
  - `ng_category:1` - Newsletter Glue template category

### Fixed
- **Critical**: Missing `class-omeda-data-manager.php` include causing fatal error
- **Critical**: Unhandled exception when API credentials missing
- Graceful error handling for API client initialization
- Proper error messages in deployment type dropdown
- Newsletter Glue internal post types excluded from dropdown

### Changed
- Updated version from 1.0.0 to 1.1.0
- Improved error handling with Throwable catch blocks (PHP 7+ compatibility)
- Enhanced deployment type configuration UI
- Newsletter Glue integration now uses actual NG structure (not guessed)

### Documentation
- Added `NEWSLETTER_GLUE_INTEGRATION.md` - Complete integration guide
- Added `CRITICAL_ERROR_FIX.md` - Error resolution documentation
- Added `CHANGELOG.md` - This file

---

## [1.0.0] - 2025-10-28

### Added
- Initial plugin release
- Omeda API client implementation
- Basic deployment type management
- Post meta box for deployment configuration
- Workflow manager for deployment creation
- Action Scheduler integration for async processing
- WP-Cron fallback support
- Settings page for API credentials
- Basic Newsletter Glue detection (preliminary)

### Features
- Create/update Omeda deployments on post publish
- Track deployment IDs in post meta
- Workflow logging system
- Debouncing to prevent duplicate deployments
- Environment-based configuration (staging/production)
- Manual deployment triggering

---

## Upcoming Features

### Planned for 1.2.0
- Omeda deployment status tracking
- Deployment analytics integration
- Bulk deployment operations
- Enhanced logging with filtering
- Newsletter Glue send hook integration

### Planned for 2.0.0
- Multi-brand support
- Custom field mapping
- Deployment templates
- Scheduled deployments
- Advanced filtering rules
- REST API endpoints

---

## Migration Notes

### From 1.0.0 to 1.1.0
No migration required. This is a feature addition release with bug fixes.

**Action Required:**
1. Update plugin files
2. Configure API credentials if not already set
3. Configure Newsletter Glue post types if using NG integration
4. Test deployment type creation

**Breaking Changes:** None

---

## Support

For issues or questions:
- GitHub Issues: [Create an issue]
- Documentation: See `/docs/project/` directory
- API Docs: See `/docs/omeda-api-reference.md`

---

**Current Version:** 1.6.2  
**Last Updated:** 2025-10-29
