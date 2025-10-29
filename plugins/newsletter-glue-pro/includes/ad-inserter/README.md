# Ad Inserter Backend Documentation

## Overview

The Ad Inserter backend for Newsletter Glue Pro is designed to provide a flexible and extensible architecture for integrating multiple ad manager plugins within WordPress. This system enables dynamic syncing and retrieval of ad data, supporting both current and future ad integrations through an abstract class structure and a management system.

## Components

### 1. Abstract Class: `NGL_Ad_Integration`

- **Purpose**: Serves as the blueprint for all ad manager integrations, enforcing a common interface while allowing flexibility for specific implementations.
- **Location**: `/includes/ad-inserter/abstract-ad-integration.php`
- **Key Methods**:
  - `get_id()`: Returns the unique identifier for the integration.
  - `get_name()`: Returns the human-readable name of the integration.
  - `get_option_name()`: Returns the WordPress option name used to store ad data.
  - `register_hooks()`: Sets up hooks for events like post saving to keep ad data updated.
  - `handle_save_post()`: Handles the `save_post` action to sync individual ads when they are updated.
  - `is_relevant_post_type()`: Abstract method to check if a post type is relevant to the integration.
  - `get_ads()`: Abstract method to retrieve ad data, typically from a specific source or post type.
  - `sync_all_ads()`: Syncs all ads for the integration, storing them in a WordPress option.
  - `sync_single_ad()`: Syncs a single ad based on a post ID.
  - `format_ad_data()`: Abstract method to format ad data from a post object into a standardized array.
  - `is_available()`: Abstract method to check if the integration is available (e.g., if a required plugin is active).

### 2. Manager Class: `NGL_Ad_Integration_Manager`

- **Purpose**: Manages multiple ad integrations, handles the active integration, and provides methods for syncing and retrieving ads.
- **Location**: `/includes/ad-inserter/integration-manager.php`
- **Key Methods**:
  - `register_integration()`: Registers a new integration instance if it's available.
  - `set_active_integration()`: Sets the active integration and triggers a sync of all ads for that integration.
  - `get_active_integration()`: Returns the currently active integration instance.
  - `get_integrations()`: Returns an array of all registered integrations.
  - `get_ads()`: Retrieves ads from the active integration's stored option.
  - `trigger_sync()`: Triggers a sync of all ads for the active integration.
  - `handle_plugin_activation()`: Syncs ads when the plugin is activated.

### 3. Prototype Integration: `NGL_Prototype_Ad_Integration`

- **Purpose**: Provides a basic implementation of an ad integration for testing and demonstration purposes, using mock data.
- **Location**: `/includes/ad-inserter/integrations/prototype.php`
- **Details**:
  - Extends `NGL_Ad_Integration`.
  - Always returns `true` for `is_available()`.
  - Returns mock ad data if no stored data exists in the WordPress option.
  - Does not sync data from actual posts, as it's not tied to a specific post type (`is_relevant_post_type()` returns `false`).

### 4. REST API Controller: `NGL_Ad_Inserter_REST_Controller`

- **Purpose**: Exposes ad data from the active integration via a REST API endpoint for use in the block editor.
- **Location**: `/includes/rest-api/class-ngl-ad-inserter-rest-controller.php`
- **Endpoint**: `/newsletter-glue/v1/ad-inserter/ads`
- **Features**:
  - Fetches ads from the active integration using the manager class.
  - Supports search filtering by title, category, or group.
  - Secured by `edit_posts` capability check.

## How an Integration Works

1. **Initialization**: Integrations are registered with the `NGL_Ad_Integration_Manager` during plugin initialization. The manager checks if each integration is available before adding it to the list.
2. **Active Integration**: One integration is set as active, either by default (first available) or by user selection (stored in a WordPress option).
3. **Ad Data Storage**: Ad data is stored in a WordPress option specific to each integration, typically as a JSON-encoded array. This avoids storing actual ad files and keeps data lightweight.
4. **Syncing**: Ads can be synced on plugin activation, when switching integrations, or on individual post saves (via the `save_post` hook). Syncing updates the stored option with the latest ad data.
5. **Retrieval**: The active integration's ad data is retrieved through the manager class and exposed via the REST API for use in the frontend or block editor.

## Building a New Integration

To create a new ad manager integration, follow these steps:

1. **Create a New Class**: Extend `NGL_Ad_Integration` in a new file under `/includes/ad-inserter/integrations/`.
2. **Define Properties**:
   - Set `$id` to a unique identifier for your integration.
   - Set `$name` to a human-readable name.
3. **Implement Abstract Methods**:
   - `is_relevant_post_type($post_type)`: Return `true` for post types your integration handles (e.g., a custom post type for ads).
   - `get_ads($args)`: Define logic to fetch ads, potentially using `WP_Query` for post-based ads or an external API for non-CPT integrations.
   - `format_ad_data($post)`: Convert a post or data source into a standardized ad array with fields like `id`, `title`, `url`, `adImage`, `category`, `placement`, and `group`.
   - `is_available()`: Check if the integration can be used (e.g., verify if a required plugin is active).
4. **Register the Integration**: Add your class to the `init_ad_integrations()` method in `newsletter-glue.php` using `$this->ad_manager->register_integration( new Your_Integration_Class() );`.
5. **Testing**: Ensure your integration syncs ads correctly on post saves and plugin activation, and that the data is accessible via the REST API.

**Example**:
```php
class NGL_Advanced_Ads_Integration extends NGL_Ad_Integration {
    public function __construct() {
        $this->id = 'advanced-ads';
        $this->name = __( 'Advanced Ads', 'newsletter-glue' );
        parent::__construct();
    }

    protected function is_relevant_post_type( $post_type ) {
        return $post_type === 'advanced_ads';
    }

    public function get_ads( $args = array() ) {
        $query_args = array(
            'post_type' => 'advanced_ads',
            'post_status' => 'publish',
            'posts_per_page' => isset( $args['posts_per_page'] ) ? $args['posts_per_page'] : 10,
        );
        $query = new WP_Query( $query_args );
        $ads = array();
        while ( $query->have_posts() ) {
            $query->the_post();
            $ad_data = $this->format_ad_data( get_post() );
            if ( $ad_data ) {
                $ads[] = $ad_data;
            }
        }
        wp_reset_postdata();
        return $ads;
    }

    protected function format_ad_data( $post ) {
        return array(
            'id' => 'adv-' . $post->ID,
            'title' => get_the_title( $post->ID ),
            'url' => get_post_meta( $post->ID, 'ad_url', true ),
            'adImage' => get_the_post_thumbnail_url( $post->ID, 'full' ),
            'category' => 'advertisement',
            'placement' => get_post_meta( $post->ID, 'ad_placement', true ),
            'group' => 'advanced'
        );
    }

    public function is_available() {
        return class_exists( 'Advanced_Ads' );
    }
}
```

## Considerations

- **Performance**: Syncing large numbers of ads can be resource-intensive. Consider implementing background processing for `sync_all_ads()` if dealing with many ads.
- **Data Format**: Ensure the ad data format returned by `format_ad_data()` matches the expected structure for the block editor and REST API.
- **Security**: The REST API is secured by capability checks (`edit_posts`), but ensure any custom endpoints or data handling in new integrations sanitizes inputs.
- **Compatibility**: Check for plugin conflicts or version requirements when building integrations for specific ad managers.

## Upcoming Features

- **Admin UI**: A planned admin page to select the active integration, view available integrations, and manually trigger sync operations.
- **Background Syncing**: Implementation of a background process for syncing ads asynchronously to avoid timeouts during large sync operations.
- **Non-CPT Integrations**: Enhanced support for ad managers that do not use WordPress custom post types, potentially integrating with external APIs or data sources.
- **Advanced Filtering**: Adding more filtering options in the REST API for ad retrieval based on categories, placements, or groups.

## Known Issues

- **Sync Overwrite**: Currently, `sync_all_ads()` in some integrations may not preserve manual changes to ad data in the option if called repeatedly. This is intentional in the prototype to ensure fresh data, but custom integrations may need to handle this differently.
- **Large Data Sets**: Syncing a large number of ads on plugin activation or integration switch can cause performance issues, which will be addressed with background processing in future updates.

## Support and Feedback

For issues, feature requests, or contributions to the Ad Inserter backend, please contact the Newsletter Glue support team or submit feedback through the plugin's support channels. If you're developing a custom integration and encounter challenges, consider reaching out for guidance on best practices or potential plugin conflicts.

---
*Last Updated: June 2025*
