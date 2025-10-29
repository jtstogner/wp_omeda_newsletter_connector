<?php
/**
 * WordPress Variable Parser for Omeda Integration.
 * Replaces WordPress variables in subject lines and other fields.
 */
class Omeda_Variable_Parser
{
    /**
     * Parse WordPress variables in a string based on a post context.
     * 
     * Supported variables:
     * - {post_title} - The post title
     * - {post_date} - The post publication date (formatted)
     * - {post_date_Y} - Year (4 digits)
     * - {post_date_m} - Month (2 digits)
     * - {post_date_d} - Day (2 digits)
     * - {post_date_F} - Month name (e.g., January)
     * - {post_date_M} - Short month name (e.g., Jan)
     * - {author_name} - The post author display name
     * - {author_first_name} - The post author first name
     * - {author_last_name} - The post author last name
     * - {site_name} - The site name from WordPress settings
     * - {site_tagline} - The site tagline/description
     * - {category} - Primary category name
     * - {categories} - All category names (comma-separated)
     * - {tags} - All tag names (comma-separated)
     * - {excerpt} - Post excerpt (trimmed to 100 chars)
     * 
     * @param string $template The template string with variables.
     * @param int $post_id The WordPress post ID for context.
     * @return string The parsed string with variables replaced.
     */
    public static function parse($template, $post_id) {
        if (empty($template)) {
            return '';
        }

        $post = get_post($post_id);
        if (!$post) {
            return $template;
        }

        // Get author information
        $author = get_userdata($post->post_author);
        
        // Build replacement map
        $replacements = array(
            '{post_title}'        => get_the_title($post_id),
            '{post_date}'         => get_the_date('', $post_id),
            '{post_date_Y}'       => get_the_date('Y', $post_id),
            '{post_date_y}'       => get_the_date('y', $post_id),
            '{post_date_m}'       => get_the_date('m', $post_id),
            '{post_date_d}'       => get_the_date('d', $post_id),
            '{post_date_F}'       => get_the_date('F', $post_id),
            '{post_date_M}'       => get_the_date('M', $post_id),
            '{post_date_j}'       => get_the_date('j', $post_id),
            '{post_date_n}'       => get_the_date('n', $post_id),
            '{author_name}'       => $author ? $author->display_name : '',
            '{author_first_name}' => $author ? $author->first_name : '',
            '{author_last_name}'  => $author ? $author->last_name : '',
            '{site_name}'         => get_bloginfo('name'),
            '{site_tagline}'      => get_bloginfo('description'),
            '{excerpt}'           => self::get_safe_excerpt($post_id),
        );

        // Add category information
        $categories = get_the_category($post_id);
        if (!empty($categories)) {
            $replacements['{category}'] = $categories[0]->name;
            $replacements['{categories}'] = implode(', ', wp_list_pluck($categories, 'name'));
        } else {
            $replacements['{category}'] = '';
            $replacements['{categories}'] = '';
        }

        // Add tag information
        $tags = get_the_tags($post_id);
        if (!empty($tags)) {
            $replacements['{tags}'] = implode(', ', wp_list_pluck($tags, 'name'));
        } else {
            $replacements['{tags}'] = '';
        }

        // Replace all variables
        $result = str_replace(array_keys($replacements), array_values($replacements), $template);

        // Apply filters for extensibility
        return apply_filters('omeda_parsed_variables', $result, $template, $post_id, $replacements);
    }

    /**
     * Get a safe excerpt (plain text, limited length).
     * 
     * @param int $post_id The post ID.
     * @param int $length Maximum length in characters.
     * @return string The excerpt.
     */
    private static function get_safe_excerpt($post_id, $length = 100) {
        $post = get_post($post_id);
        if (!$post) {
            return '';
        }

        // Use manual excerpt if available
        if (!empty($post->post_excerpt)) {
            $excerpt = $post->post_excerpt;
        } else {
            // Generate from content
            $excerpt = strip_tags($post->post_content);
            $excerpt = strip_shortcodes($excerpt);
        }

        // Trim to length
        if (strlen($excerpt) > $length) {
            $excerpt = substr($excerpt, 0, $length);
            $excerpt = substr($excerpt, 0, strrpos($excerpt, ' ')) . '...';
        }

        return $excerpt;
    }

    /**
     * Get available variables with descriptions for UI display.
     * 
     * @return array Array of variable => description pairs.
     */
    public static function get_available_variables() {
        return array(
            '{post_title}'        => 'Post title',
            '{post_date}'         => 'Post date (formatted)',
            '{post_date_Y}'       => 'Year (4 digits)',
            '{post_date_y}'       => 'Year (2 digits)',
            '{post_date_m}'       => 'Month (2 digits)',
            '{post_date_d}'       => 'Day (2 digits)',
            '{post_date_F}'       => 'Month name (e.g., January)',
            '{post_date_M}'       => 'Short month (e.g., Jan)',
            '{post_date_j}'       => 'Day without leading zero',
            '{post_date_n}'       => 'Month without leading zero',
            '{author_name}'       => 'Author display name',
            '{author_first_name}' => 'Author first name',
            '{author_last_name}'  => 'Author last name',
            '{site_name}'         => 'Website name',
            '{site_tagline}'      => 'Website tagline',
            '{category}'          => 'Primary category',
            '{categories}'        => 'All categories',
            '{tags}'              => 'All tags',
            '{excerpt}'           => 'Post excerpt',
        );
    }
}
