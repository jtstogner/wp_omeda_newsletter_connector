# Changelog

## [1.13.1] - 2025-10-30
### Fixed
- Fixed nonce verification blocking REST API and programmatic post saves
- Made nonce checking conditional - only enforced for traditional form submissions
- Workflow now properly triggers when saving posts via REST API (Newsletter Glue, Gutenberg)

## [1.13.0] - 2025-10-29
### Added
- Enhanced workflow logging with detailed transaction tracking
- RAW log level for API request/response debugging
- Advanced error context in workflow logs
- Deployment Type and Audience Query dropdowns with searchable selects
- Default From Name, Reply-To, and From Email settings
- Campaign ID and Deployment Name format fields
- Variable parser for WordPress shortcodes in subject lines

### Changed
- Improved nonce field rendering in meta boxes
- Updated API client endpoint handling
- Enhanced error messages with HTTP status codes

## [1.12.0] - 2025-10-29
### Added
- Workflow log viewer in admin interface
- Step-by-step deployment tracking
- Retry mechanism for failed API calls
- Synchronous deployment creation with immediate audience assignment

### Fixed
- API endpoint trailing slash handling
- Content assignment error handling
- Database connection error logging

## [1.11.0] - 2025-10-29
### Added
- Newsletter Glue integration hooks
- Design template assignment
- Deployment type custom post type
- Omeda API client with caching

### Changed
- Switched from WP-Cron to Action Scheduler for production reliability
- Improved error handling and logging

## [1.0.0] - 2025-10-28
### Added
- Initial plugin release
- Basic Omeda API integration
- Settings page for API credentials
