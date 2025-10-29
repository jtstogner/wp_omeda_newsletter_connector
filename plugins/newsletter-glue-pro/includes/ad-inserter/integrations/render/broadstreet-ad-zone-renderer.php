<?php
/**
 * Ad Zone Renderer
 * 
 * Handles the rendering of ad zones in email content
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Process the HTML content and replace ad-inserter blocks with random ads from their selected ad zones
 */
function ngl_process_ad_zone_blocks( $html, $post_id, $app = null ) {
    // Enable debugging
    $debug = true;
    $debug_log = array();
    $debug_log[] = "Starting ad zone processing for post ID: $post_id";
    
    // Create a DOM parser
    $dom = str_get_html( $html );
    if ( ! $dom ) {
        error_log("Failed to create DOM from HTML");
        return $html;
    }
    
    // Find all ad inserter blocks
    $ad_blocks = $dom->find('div.ng-ad-inserter');
    $debug_log[] = "Found " . count($ad_blocks) . " ad inserter blocks";
    
    // Debug the HTML structure
    if ($debug && count($ad_blocks) > 0) {
        $first_block = $ad_blocks[0];
        $debug_log[] = "First block HTML: " . htmlspecialchars(substr($first_block->outertext, 0, 300));
        $debug_log[] = "First block classes: " . $first_block->getAttribute('class');
        
        // List all attributes of the first block
        $attrs = array();
        foreach ($first_block->attr as $name => $value) {
            $attrs[$name] = $value;
        }
        $debug_log[] = "First block attributes: " . json_encode($attrs);
    }

    foreach ( $ad_blocks as $index => $block ) {
        // Check if this block has ad zone data attributes
        $zone_id = $block->getAttribute('data-ad-zone-id');
        
        $debug_log[] = "Block #$index - Zone ID: " . ($zone_id ?: 'none');
        
        // If we don't have the zone ID, try to extract it from the block content
        if (empty($zone_id)) {
            // Try to extract zone ID from the block's content
            $block_content = $block->innertext;
            
            // Look for adZoneId in any data attributes
            if (preg_match('/data-[^=]*="[^"]*adZoneId[^"]*:([^,"]+)/', $block_content, $matches)) {
                $zone_id = trim($matches[1], '"\'');
                $debug_log[] = "Extracted zone ID from block content: $zone_id";
            }
            
            // If we still don't have a zone ID, check if it's in a script tag
            if (empty($zone_id) && preg_match('/<script[^>]*>(.*?)<\/script>/s', $block_content, $script_matches)) {
                $script_content = $script_matches[1];
                if (preg_match('/adZoneId["\']?\s*:\s*["\']?([^"\',}]+)/', $script_content, $zone_matches)) {
                    $zone_id = trim($zone_matches[1], '"\'');
                    $debug_log[] = "Extracted zone ID from script: $zone_id";
                }
            }
        }
        
        // If we have the zone ID, proceed with ad replacement
        if (!empty($zone_id)) {
            // Get the advertisements for this ad zone using the zone ID
            $option_prefix = "ngl_ad_zone_{$post_id}_{$zone_id}_";
            $advertisements = get_option($option_prefix . 'advertisements', array());
            
            $debug_log[] = "Option name: {$option_prefix}advertisements";
            $debug_log[] = "Found " . count($advertisements) . " advertisements";


            //error_log("Advertisements: " . json_encode($advertisements));
            
            // If we have advertisements, select one randomly
            if (!empty($advertisements) && is_array($advertisements)) {
                // Debug the advertisement structure
                $debug_log[] = "Advertisement structure: " . json_encode(array_slice($advertisements, 0, 1));
                
                //error_log("Advertisements 2nd Time: " . json_encode($advertisements));
                
                // Get a random advertisement
                $random_index = array_rand($advertisements);
                $ad = $advertisements[$random_index];

                // Extract the ad ID and title - handle different possible structures
                $ad_id = '';
                $ad_title = 'Advertisement';
                
                if (is_array($ad)) {
                    // Check for different possible key names for the ID
                    if (isset($ad['id'])) {
                        $ad_id = $ad['id'];
                    } elseif (isset($ad['adv_id'])) {
                        $ad_id = $ad['adv_id'];
                    } elseif (isset($ad['advId'])) {
                        $ad_id = $ad['advId'];
                    }
                    
                    // Check for different possible key names for the title
                    if (isset($ad['title'])) {
                        $ad_title = $ad['title'];
                    } elseif (isset($ad['name'])) {
                        $ad_title = $ad['name'];
                    } elseif (isset($ad['adv_name'])) {
                        $ad_title = $ad['adv_name'];
                    }
                } elseif (is_string($ad) || is_numeric($ad)) {
                    // If the ad is just a string or number, use it as the ID
                    $ad_id = $ad;
                }
                
                $debug_log[] = "Selected ad ID: $ad_id, Title: $ad_title";
                $debug_log[] = "Full ad data: " . json_encode($ad);
                
                if (!empty($ad_id)) {
                    // Create the ad HTML
                    $ad_url = "https://ad.broadstreetads.com/click/{$ad_id}";
                    $ad_image = "https://ad.broadstreetads.com/display/{$ad_id}";
                    
                    // Get width and height from the existing image if available
                    $width = '';
                    $height = '';
                    $style = '';
                    $class = '';
                    if ($img = $block->find('img', 0)) {
                        $width = $img->getAttribute('width');
                        $height = $img->getAttribute('height');
                        $style = $img->getAttribute('style');
                        $class = $img->getAttribute('class');
                        $debug_log[] = "Found image with width: $width, height: $height, class: $class";
                    } else {
                        $debug_log[] = "No image found in the block";
                        // Default dimensions if no image found
                        $width = '600';
                        $height = '400';
                    }
                    
                    // Create the new ad HTML - use a direct img tag without figure for email compatibility
                    $ad_html = '<a href="' . esc_url($ad_url) . '" target="_blank" rel="noopener noreferrer">';
                    $ad_html .= '<img src="' . esc_url($ad_image) . '" alt="' . $ad_title . '" title="' . $ad_title . '"';
                    
                    if (!empty($width)) {
                        $ad_html .= ' width="' . esc_attr($width) . '"';
                    }
                    
                    if (!empty($height)) {
                        $ad_html .= ' height="' . esc_attr($height) . '"';
                    }
                    
                    if (!empty($class)) {
                        $ad_html .= ' class="' . esc_attr($class) . '"';
                    } else {
                        $ad_html .= ' class="ng-standard-img"';
                    }
                    
                    if (!empty($style)) {
                        $ad_html .= ' style="' . esc_attr($style) . '"';
                    } else {
                        $ad_html .= ' style="display: block; width: 100%; height: auto; max-width: 100%; margin-bottom: 0;"';
                    }
                    
                    $ad_html .= '>';
                    $ad_html .= '</a>';
                    
                    $debug_log[] = "Created ad HTML: " . htmlspecialchars(substr($ad_html, 0, 100)) . "...";
                    
                    // Replace the block content with our ad
                    if ($figure = $block->find('figure', 0)) {
                        $figure->outertext = $ad_html;
                        $debug_log[] = "Replaced figure content";
                    } else {
                        $block->innertext = $ad_html;
                        $debug_log[] = "Replaced block inner content (no figure found)";
                    }
                    
                    $debug_log[] = "Final block HTML: " . htmlspecialchars(substr($block->outertext, 0, 100)) . "...";
                } else {
                    $debug_log[] = "Invalid ad ID";
                }
            } else {
                $debug_log[] = "No advertisements found for option: {$option_prefix}advertisements";
            }
        } else {
            $debug_log[] = "Missing zone ID after extraction attempts";
        }
    }
    
    // Save the modified HTML
    $modified_html = $dom->save();
    
    // Write debug log to error log
    if ($debug) {
        //error_log("=== AD ZONE RENDERER DEBUG LOG ===");
        foreach ($debug_log as $log) {
            //error_log($log);
        }
        //error_log("=== END AD ZONE RENDERER DEBUG LOG ===");
    }
    
    return $modified_html;
}

// Add the filter to process ad zones before email sending
add_filter( 'newsletterglue_generated_html_output', 'ngl_process_ad_zone_blocks', 7, 3 );
