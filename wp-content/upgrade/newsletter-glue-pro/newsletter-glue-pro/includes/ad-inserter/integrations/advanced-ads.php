<?php

/**
 * Advanced Ads integration for Newsletter Glue
 *
 * @package Newsletter Glue
 */


/**
 * Advanced Ads integration for Newsletter Glue
 *
 * @since 3.2.1
 */
class NGL_Advanced_Ads_Integration extends NGL_Ad_Integration {

    protected $log_file;
    protected $logging_enabled = false;

    /**
     * Custom logging method for Advanced Ads integration
     */
    protected function log( $message ) {
        if ( ! $this->logging_enabled ) {
            return;
        }
        
        $timestamp = date( 'Y-m-d H:i:s' );
        $log_message = "[$timestamp] $message\n";
        file_put_contents( $this->log_file, $log_message, FILE_APPEND );
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->id = 'advanced-ads';
        $this->name = __( 'Advanced Ads', 'newsletter-glue' );
        parent::__construct();

        // Defer initial sync to ensure WordPress environment is fully loaded

        $this->option_name = 'ngl_ad-inserter_advanced-ads_ads';

        if( ! $this->is_available() ) {
            return;
        }

        if( empty( get_option( $this->option_name ) ) ) {
            $this->defer_initial_sync();
        }

        // Check if logging is enabled via filter
        $this->logging_enabled = apply_filters( 'ngl_advanced_ads_enable_logging', false );
        
        // Only set up the log file if logging is enabled
        if ( $this->logging_enabled ) {
            $this->log_file = WP_CONTENT_DIR . '/advanced-ads-integration.log';
        }

    }
    
    protected function is_relevant_post_type( $post_type ) {
        return $post_type === 'advanced_ads';
    }

    public function get_ads( $args = array() ) {
        // Ensure WordPress environment is fully loaded to prevent errors during activation
        if ( ! function_exists( 'get_userdata' ) ) {
            $this->log( 'WordPress environment not fully loaded, skipping get_ads for Advanced Ads.' );
            return array();
        }

        // Save the current global post to restore it later
        global $post;
        $original_post = $post;
        
        $query_args = array(
            'post_type' => 'advanced_ads',
            'post_status' => 'publish',
            'posts_per_page' => isset( $args['posts_per_page'] ) ? $args['posts_per_page'] : 10,
        );
        $query = new WP_Query( $query_args );
        $ads = array();
        
        // Log the total number of ads found
        $this->log( 'Advanced Ads Integration: Found ' . $query->found_posts . ' ads' );
        
        while ( $query->have_posts() ) {
            $query->the_post();
            $current_post = get_post();
            
            // Log details of each post for debugging
            $this->log( 'Processing ad post ID: ' . $current_post->ID );
            $this->log( 'Post title: ' . get_the_title( $current_post->ID ) );
            $this->log( 'Post meta keys: ' . print_r( array_keys( get_post_meta( $current_post->ID ) ), true ) );
            
            $ad_data = $this->format_ad_data( $current_post );
            if ( $ad_data ) {
                $ads[] = $ad_data;
            }
        }
        
        // Properly restore the original post and reset postdata
        wp_reset_postdata();
        
        // Extra step to ensure the global post is restored to its original state
        $post = $original_post;
        
        // Log the final array of ads
        $this->log( 'Final ads array: ' . print_r( $ads, true ) );
        
        return $ads;
    }

    protected function format_ad_data( $post ) {
        // Check if this is an AdSense ad first
        $ad_options = get_post_meta( $post->ID, 'advanced_ads_ad_options', true );
        if ( is_array( $ad_options ) && isset( $ad_options['type'] ) && $ad_options['type'] === 'adsense' ) {
            $this->log( 'Skipping ad ' . $post->ID . ' because it is of type adsense' );
            return null; // Skip AdSense ads completely
        }
        
        // Get the post content
        $content = $post->post_content;
        $ad_image = '';
        
        // Log content check
        $this->log( 'Checking content for ad ' . $post->ID . ' for image tag. Content length: ' . strlen( $content ) );
        // Search for the first <img> tag in the content
        if ( preg_match('/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $content, $matches) ) {
            $ad_image = $matches[1];
            $this->log( 'Found image URL for ad ' . $post->ID . ': ' . $ad_image );
        }
        
        // If no image in content or content is empty, check post meta for image type
        if ( empty( $ad_image ) ) {
            $this->log( 'No image found in content for ad ' . $post->ID . ', checking post meta.' );
            // We already retrieved ad_options at the beginning of the function
            $this->log( 'Checking ad options for ad ' . $post->ID . ': ' . ( is_array( $ad_options ) ? print_r( array_keys( $ad_options ), true ) : 'No options found' ) );
            if ( is_array( $ad_options ) ) {
                $this->log( 'Ad options type for ad ' . $post->ID . ': ' . ( isset( $ad_options['type'] ) ? $ad_options['type'] : 'not set' ) );
                $this->log( 'Ad options image_id for ad ' . $post->ID . ': ' . ( isset( $ad_options['image_id'] ) ? $ad_options['image_id'] : 'not set' ) );
            }
            if ( isset( $ad_options['type'] ) && $ad_options['type'] === 'image' ) {
                $image_id = isset( $ad_options['image_id'] ) ? $ad_options['image_id'] : ( isset( $ad_options['output']['image_id'] ) ? $ad_options['output']['image_id'] : false );
                if ( $image_id ) {
                    $this->log( 'Attempting to retrieve image URL for ad ' . $post->ID . ' with image ID ' . $image_id );
                    $ad_image = wp_get_attachment_url( $image_id );
                    if ( $ad_image ) {
                        $this->log( 'Retrieved image URL for ad ' . $post->ID . ': ' . $ad_image );
                    } else {
                        $this->log( 'No attachment found for ID ' . $image_id . ' for ad ' . $post->ID );
                    }
                } else {
                    $this->log( 'Ad ' . $post->ID . ' is of type image but no image_id is set in options or output sub-array.' );
                    // Fallback to post thumbnail if available
                    $ad_image = get_the_post_thumbnail_url( $post->ID, 'full' );
                    if ( $ad_image ) {
                        $this->log( 'Using post thumbnail for ad ' . $post->ID . ': ' . $ad_image );
                    } else {
                        $this->log( 'No post thumbnail found for ad ' . $post->ID );
                    }
                }
            } else {
                $this->log( 'Ad ' . $post->ID . ' is not of type image. Type: ' . ( isset( $ad_options['type'] ) ? $ad_options['type'] : 'not set' ) );
                // Fallback to post thumbnail if available
                $ad_image = get_the_post_thumbnail_url( $post->ID, 'full' );
                if ( $ad_image ) {
                    $this->log( 'Using post thumbnail for ad ' . $post->ID . ': ' . $ad_image );
                } else {
                    $this->log( 'No post thumbnail found for ad ' . $post->ID );
                }
            }
        }
        
        // Construct the ad URL using home_url/linkout/{post_ID}
        $ad_url = home_url( '/linkout/' . $post->ID );
        
        return array(
            'id' => 'adv-' . $post->ID,
            'title' => get_the_title( $post->ID ),
            'url' => $ad_url,
            'adImage' => $ad_image,
            'category' => 'advertisement',
            'placement' => get_post_meta( $post->ID, 'ad_placement', true ),
            'group' => 'advanced'
        );
    }

    public function is_available() {
        $available = class_exists( 'Advanced_Ads' );
        $this->set_available($available);
        return $available;
    }

    public function set_available( $available ) {
        if($available) {
            update_option( 'ngl_' . $this->id . '_available', $available );
        } else {
            update_option( 'ngl_' . $this->id . '_available', false );
        }
    }

}