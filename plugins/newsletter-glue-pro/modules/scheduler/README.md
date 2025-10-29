# Newsletter Glue Scheduler Module

This module extends the Newsletter Glue automation system to prevent sending newsletters that have no posts in their latest posts blocks.

## Features

- Automatically checks if a newsletter contains posts in its latest posts blocks before sending
- Prevents empty newsletters from being sent to subscribers
- Creates appropriate log entries when newsletters are canceled due to lack of content
- Integrates seamlessly with the existing Newsletter Glue automation system
- Includes filter hooks to control or disable functionality as needed

## How It Works

The Scheduler module hooks into Newsletter Glue's automation system at priority 9 (before the default handler) and:

1. Removes the default Newsletter Glue automation handler
2. Processes the automation with additional checks for post content
3. Only sends the newsletter if it contains posts in the latest posts blocks
4. Creates appropriate log entries for both successful sends and cancellations

## Technical Implementation

The module uses WordPress action hooks to integrate with Newsletter Glue without modifying any core files:

- Uses `newsletterglue_trigger_automated_email` hook at priority 9 to intercept automation requests
- Creates a custom `ngl_scheduler_process_automated_email` hook to handle the processing
- Maintains all existing Newsletter Glue functionality while adding the empty post check

## Integration

The module is loaded automatically by the Newsletter Glue modules loader and requires no additional configuration.

## Filter Hooks

The module provides several filter hooks that allow developers to control or disable its functionality. These hooks can be used to create a safe-hold mechanism, allowing you to programmatically control the scheduler's behavior in different scenarios.

### 1. ngl_scheduler_enabled

**Purpose:** Completely enable or disable the scheduler module globally.

**Parameters:**
- `$enabled` (boolean): Whether the scheduler is enabled. Default: `true`

**Return value:** Boolean. Return `false` to completely disable the scheduler module.

**Usage scenarios:**
- Temporarily disable the scheduler during site maintenance
- Disable the scheduler in development or staging environments
- Create an admin setting to toggle the scheduler functionality

**Example:**

```php
/**
 * Completely disable the Newsletter Glue Scheduler module.
 */
add_filter( 'ngl_scheduler_enabled', '__return_false' );

// OR conditionally disable based on environment
add_filter( 'ngl_scheduler_enabled', function( $enabled ) {
    // Disable in development environments
    if ( defined( 'WP_ENVIRONMENT_TYPE' ) && WP_ENVIRONMENT_TYPE === 'development' ) {
        return false;
    }
    return $enabled;
});
```

### 2. ngl_scheduler_should_process_email

**Purpose:** Control whether the scheduler should process a specific newsletter automation.

**Parameters:**
- `$should_process` (boolean): Whether the email should be processed by the scheduler. Default: `true`
- `$post_id` (integer): The post ID of the automation being processed

**Return value:** Boolean. Return `false` to bypass the scheduler for specific newsletters.

**Usage scenarios:**
- Skip processing for specific automation types or categories
- Create a whitelist/blacklist of automation IDs
- Bypass the scheduler during specific time periods

**Example:**

```php
/**
 * Bypass the scheduler for specific newsletter automations.
 * 
 * @param bool $should_process Whether the scheduler should process this email
 * @param int $post_id The post ID of the automation
 * @return bool
 */
add_filter( 'ngl_scheduler_should_process_email', function( $should_process, $post_id ) {
    // Example 1: Skip scheduler for specific post IDs
    $bypass_ids = array( 123, 456, 789 );
    if ( in_array( $post_id, $bypass_ids ) ) {
        return false;
    }
    
    // Example 2: Skip scheduler for newsletters with specific category
    if ( has_category( 'urgent-announcements', $post_id ) ) {
        return false;
    }
    
    return $should_process;
}, 10, 2 );
```

### 3. ngl_scheduler_should_check_posts

**Purpose:** Control whether the scheduler should check for posts in a specific newsletter.

**Parameters:**
- `$should_check` (boolean): Whether to check for posts. Default: `true`
- `$post_id` (integer): The post ID of the automation being processed

**Return value:** Boolean. Return `false` to skip the post content check for specific newsletters.

**Usage scenarios:**
- Skip post checking for certain newsletter types
- Allow empty newsletters in specific scenarios
- Create a custom validation system that replaces the built-in check

**Example:**

```php
/**
 * Control whether to check for posts in specific newsletters.
 * 
 * @param bool $should_check Whether to check for posts
 * @param int $post_id The post ID of the automation
 * @return bool
 */
add_filter( 'ngl_scheduler_should_check_posts', function( $should_check, $post_id ) {
    // Example 1: Skip post checking for newsletters with specific category
    if ( has_category( 'important-updates', $post_id ) ) {
        return false; // Don't check for posts, send regardless
    }
    
    // Example 2: Skip post checking on specific days
    $current_day = date('l');
    if ( $current_day === 'Monday' ) {
        // Always send Monday newsletters regardless of content
        return false;
    }
    
    return $should_check;
}, 10, 2 );
```

### 4. ngl_scheduler_has_posts

**Purpose:** Override the result of the post content check.

**Parameters:**
- `$has_posts` (boolean): The original result of the post content check
- `$post_id` (integer): The post ID of the automation being processed

**Return value:** Boolean. Return `true` to force sending the newsletter or `false` to prevent sending.

**Usage scenarios:**
- Force critical newsletters to send regardless of content
- Implement custom logic to determine if a newsletter should be sent
- Add additional validation rules beyond the built-in post check

**Example:**

```php
/**
 * Override the result of the post content check.
 * 
 * @param bool $has_posts Whether the newsletter has posts
 * @param int $post_id The post ID of the automation
 * @return bool
 */
add_filter( 'ngl_scheduler_has_posts', function( $has_posts, $post_id ) {
    // Example 1: Force newsletters in a specific category to always send
    if ( has_category( 'must-send', $post_id ) ) {
        return true;
    }
    
    // Example 2: Implement custom validation logic
    $post = get_post( $post_id );
    // If the post has a custom field indicating it should be sent
    if ( get_post_meta( $post_id, 'force_send', true ) === 'yes' ) {
        return true;
    }
    
    // Example 3: Add additional validation
    // Only send if the newsletter has posts AND meets other criteria
    if ( $has_posts && strlen( $post->post_content ) > 500 ) {
        return true;
    } else if ( $has_posts ) {
        // Has posts but content is too short
        return false;
    }
    
    return $has_posts;
}, 10, 2 );
```

### 5. ngl_scheduler_no_posts_message

**Purpose:** Customize the error message when a newsletter has no posts.

**Parameters:**
- `$message` (string): The default error message
- `$post_id` (integer): The post ID of the automation being processed

**Return value:** String. The custom error message to use.

**Usage scenarios:**
- Provide more detailed error messages
- Customize messages based on newsletter type or category
- Add instructions for users on how to fix the issue

**Example:**

```php
/**
 * Customize the error message when a newsletter has no posts.
 * 
 * @param string $message The default error message
 * @param int $post_id The post ID of the automation
 * @return string
 */
add_filter( 'ngl_scheduler_no_posts_message', function( $message, $post_id ) {
    // Example 1: Basic custom message
    return 'This newsletter was not sent because no relevant posts were found.';
    
    // Example 2: Customize based on post type or category
    $post_type = get_post_type( $post_id );
    if ( $post_type === 'weekly_digest' ) {
        return 'Weekly digest canceled: No new content was published this week.';
    }
    
    // Example 3: Add troubleshooting instructions
    return 'Newsletter sending failed: No posts found in the Latest Posts block. ' . 
           'Please check your Latest Posts block configuration and try again.';
}, 10, 2 );
```

## Combining Multiple Filters

You can combine multiple filters to create sophisticated control systems for your newsletters. Here's an example that implements a comprehensive safe-hold system:

```php
/**
 * Comprehensive Newsletter Glue Scheduler control system.
 */

// 1. Only enable the scheduler in production
function ngl_control_scheduler_environment( $enabled ) {
    if ( defined( 'WP_ENVIRONMENT_TYPE' ) && WP_ENVIRONMENT_TYPE !== 'production' ) {
        return false;
    }
    return $enabled;
}
add_filter( 'ngl_scheduler_enabled', 'ngl_control_scheduler_environment' );

// 2. Bypass processing for specific newsletters
function ngl_control_newsletter_processing( $should_process, $post_id ) {
    // Skip processing for test newsletters
    if ( has_term( 'test', 'newsletter_type', $post_id ) ) {
        return false;
    }
    return $should_process;
}
add_filter( 'ngl_scheduler_should_process_email', 'ngl_control_newsletter_processing', 10, 2 );

// 3. Custom validation logic
function ngl_custom_newsletter_validation( $has_posts, $post_id ) {
    // Always send critical announcements
    if ( has_term( 'critical', 'newsletter_type', $post_id ) ) {
        return true;
    }
    
    // Additional validation for regular newsletters
    if ( $has_posts ) {
        // Only send if the newsletter has a featured image
        return has_post_thumbnail( $post_id );
    }
    
    return $has_posts;
}
add_filter( 'ngl_scheduler_has_posts', 'ngl_custom_newsletter_validation', 10, 2 );
```
