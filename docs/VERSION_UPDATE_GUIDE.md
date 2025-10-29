# Version Update Guide

Quick reference for updating plugin version numbers.

## Files to Update

When releasing a new version, update these files:

### 1. Main Plugin File
**File:** `src/omeda-newsletter-connector/omeda-wp-integration.php`

```php
/**
 * Plugin Name: Omeda WordPress Integration
 * Description: Integrates WordPress content lifecycle with Omeda for email deployments.
 * Version: X.X.X          ← UPDATE THIS
 * Author: Your Name
 */

// ...

// Define constants.
define('OMEDA_WP_VERSION', 'X.X.X');  ← UPDATE THIS
```

### 2. Changelog
**File:** `CHANGELOG.md`

Add new version section at the top:

```markdown
## [X.X.X] - YYYY-MM-DD

### Added
- New feature descriptions

### Fixed
- Bug fix descriptions

### Changed
- Change descriptions
```

### 3. Release Documentation (Optional)
**File:** `docs/project/VERSION_X.X.X_RELEASE.md`

Create detailed release notes for major/minor versions.

---

## Version Number Rules

### Semantic Versioning: MAJOR.MINOR.PATCH

```
1.2.3
│ │ │
│ │ └─ Patch: Bug fixes, documentation
│ └─── Minor: New features, non-breaking changes
└───── Major: Breaking changes, new feature sets
```

### When to Increment

#### Patch (1.1.X → 1.1.Y)
**Increment when:**
- Fixing bugs
- Correcting typos
- Updating documentation
- Minor code improvements
- Performance optimizations

**Examples:**
- Fixed dropdown not showing templates
- Corrected error message text
- Updated README
- Optimized database query

**Workflow:**
```bash
# Example: 1.1.0 → 1.1.1
# Fix the bug
# Update version in 2 places (see above)
# Add to CHANGELOG.md
# Commit: "Bugfix v1.1.1: Fixed template dropdown"
```

#### Minor (1.X.0 → 1.Y.0)
**Increment when:**
- Adding new features
- Enhancing existing features
- Adding new integrations
- Non-breaking API changes
- New configuration options

**Examples:**
- Added Newsletter Glue integration
- Added deployment analytics
- New REST API endpoints
- Enhanced logging system

**Workflow:**
```bash
# Example: 1.1.0 → 1.2.0
# Develop new feature
# Update version in 2 places
# Add detailed section to CHANGELOG.md
# Create VERSION_1.2.0_RELEASE.md
# Commit: "Feature v1.2.0: Added deployment analytics"
```

#### Major (X.0.0 → Y.0.0)
**Increment when:**
- Breaking changes
- Major architectural changes
- Removing deprecated features
- Database schema changes
- Major feature sets

**Examples:**
- Multi-brand support (breaking config change)
- New API structure
- Removed WP-Cron support (breaking)
- Changed data storage format

**Workflow:**
```bash
# Example: 1.9.0 → 2.0.0
# Implement major changes
# Create migration guide
# Update version in 2 places
# Add migration notes to CHANGELOG.md
# Create VERSION_2.0.0_RELEASE.md with migration guide
# Commit: "Major v2.0.0: Multi-brand support"
```

---

## Quick Update Checklist

### For ANY Version Update

- [ ] Update `omeda-wp-integration.php` header (Plugin Version)
- [ ] Update `omeda-wp-integration.php` constant (OMEDA_WP_VERSION)
- [ ] Add entry to `CHANGELOG.md`
- [ ] Test plugin activation
- [ ] Verify version shows in `wp plugin list`
- [ ] Commit with version tag

### For Minor/Major Updates

- [ ] All of the above, plus:
- [ ] Create `VERSION_X.X.X_RELEASE.md`
- [ ] Update main README if needed
- [ ] Test all new features
- [ ] Update documentation

### For Major Updates

- [ ] All of the above, plus:
- [ ] Create migration guide
- [ ] Test upgrade path
- [ ] Note breaking changes
- [ ] Update API documentation

---

## Git Workflow

### Tagging Versions

```bash
# After updating version numbers and committing

# Patch update
git tag -a v1.1.1 -m "Bugfix: Fixed template dropdown"
git push origin v1.1.1

# Minor update  
git tag -a v1.2.0 -m "Feature: Added deployment analytics"
git push origin v1.2.0

# Major update
git tag -a v2.0.0 -m "Major: Multi-brand support"
git push origin v2.0.0
```

### Branch Strategy (Recommended)

```
main (production)
  ↑
develop (staging)
  ↑
feature/new-feature (development)
```

**Workflow:**
1. Create feature branch from `develop`
2. Develop and test
3. Merge to `develop` (increment version)
4. Test in staging
5. Merge to `main` (tag release)

---

## Testing Version Updates

### After Updating Version

```bash
# Check version in WordPress
wp-env run cli wp plugin list --fields=name,version

# Expected output:
# name                          version
# omeda-newsletter-connector    1.1.0

# Check constant value
wp-env run cli wp eval "echo OMEDA_WP_VERSION;"

# Expected output: 1.1.0
```

---

## Version History

| Version | Date       | Type  | Description                    |
|---------|------------|-------|--------------------------------|
| 1.1.0   | 2025-10-29 | Minor | Newsletter Glue integration    |
| 1.0.0   | 2025-10-28 | Major | Initial release                |

---

## Example Changelog Entries

### Patch (1.1.1)
```markdown
## [1.1.1] - 2025-10-30

### Fixed
- Fixed Newsletter Glue template dropdown not populating
- Corrected error message when API credentials missing

### Changed
- Improved caching performance for deployment types
```

### Minor (1.2.0)
```markdown
## [1.2.0] - 2025-11-01

### Added
- Deployment analytics dashboard
- Status tracking for Omeda deployments
- Bulk deployment operations
- Enhanced logging with filtering

### Changed
- Improved deployment creation workflow
- Updated error messages for clarity
```

### Major (2.0.0)
```markdown
## [2.0.0] - 2025-12-01

### Added
- Multi-brand support
- Custom field mapping
- Advanced filtering rules
- REST API endpoints

### Changed
- **BREAKING**: New configuration structure for API credentials
- **BREAKING**: Deployment types now use new data format
- Database schema updated (automatic migration)

### Removed
- **BREAKING**: Removed WP-Cron fallback (Action Scheduler only)
- Deprecated legacy deployment format

### Migration
See `docs/MIGRATION_2.0.0.md` for upgrade instructions.
```

---

## Quick Commands

```bash
# Check current version
wp-env run cli wp plugin list --name=omeda-newsletter-connector

# Verify classes load
wp-env run cli wp eval "echo class_exists('Omeda_WP_Integration') ? 'OK' : 'FAIL';"

# Check for PHP errors
php -l src/omeda-newsletter-connector/omeda-wp-integration.php

# View recent changes
git log --oneline -10

# List all version tags
git tag -l "v*"
```

---

**Last Updated:** 2025-10-29  
**Current Version:** 1.1.0  
**Next Planned:** 1.2.0 (Deployment analytics)
