<?php
/**
 * Latest posts block render.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * NGL_REST_API_Get_Posts class.
 */
class NGL_Render_Latest_Posts {

	/**
	 * Constructor.
	 */
	public function __construct() {

		add_action( 'newsletterglue_add_block_styles', array( $this, 'block_css' ), 20 );

		add_filter( 'render_block', array( $this, 'render_block_web' ), 50, 3 );
		add_filter( 'render_block', array( $this, 'render_block_mail' ), 99, 3 );
	}

	/**
	 * Centralized logging function that respects the debug filter.
	 *
	 * @param string $message The message to log.
	 * @param string $log_file The log file to write to (optional).
	 */
	private function debug_log( $message, $log_file = null ) {
		// Check if logging is enabled via filter
		if ( ! apply_filters( 'ngl_latest_posts_debug_logging', false ) ) {
			return;
		}

		// Default log file if none specified
		if ( empty( $log_file ) ) {
			$log_file = WP_CONTENT_DIR . '/ngl_latest_posts_debug.log';
		}

		// Add timestamp to message
		$timestamp = date( 'Y-m-d H:i:s' );
		$formatted_message = "[{$timestamp}] {$message}" . PHP_EOL;

		// Write to log file
		file_put_contents( $log_file, $formatted_message, FILE_APPEND );
	}

	/**
	 * CSS.
	 */
	public function block_css() {
		?>
		.ngl-lp-labels {
			font-size: 13px;
			color: #999;
		}
		.ngl-table-latest-posts h3 a {
			line-height: 110%;
			text-decoration: none;
		}
		.ngl-lp-content p,
		.ngl-lp-content td,
		.ngl-lp-content .ngl-quote {
			color: inherit !important;
			font-size: inherit !important;
		}
		.ngl-lp-content > * {
			margin: 0 0 10px !important;
		}
		.ngl-lp-content > h1,
		.ngl-lp-content > h2,
		.ngl-lp-content > h3,
		.ngl-lp-content > h4,
		.ngl-lp-content > h5,
		.ngl-lp-content > h6 {
			padding-top: 10px !important;
		}
		a.ngl-lp-cta-link.wp-block-button__link {
			padding: 8px 15px !important;
			min-width: 0px !important;
		}
		.ngl-lp-content .ngl-quote table td {
			padding: 0 !important;
		}
		<?php
	}

	/**
	 * Get block classes.
	 *
	 * @param array $args The block attributes.
	 *
	 * @return string The block classes.
	 */
	public function get_classes( $args = array() ) {
		extract( $args );

		$class_arr   = array( 'wp-block-newsletterglue-latest-posts' );
		$class_arr[] = "is-{$contentstyle}";
		$class_arr[] = "columns-{$columns_num}";
		$class_arr[] = "images-{$image_position}";
		$class_arr[] = "table-ratio-{$table_ratio}";
		$class_arr[] = "has-{$show_image}";
		$class_arr[] = $stacked;
		$classes     = implode( ' ', $class_arr );

		return $classes;
	}

	/**
	 * Get block styles.
	 *
	 * @param array $args The block attributes.
	 * @param bool  $is_web Whether the styles are for the web or email.
	 *
	 * @return array The block styles.
	 */
	public function get_styles( $args = array(), $is_web = true ) {
		extract( $args );

		$styles = array(
			'container' => array(),
			'images'    => array(),
			'labels'    => array(),
			'author'    => array(),
			'heading'   => array(),
			'paragraph' => array(),
			'links'     => array(),
			'cta'       => array(),
		);

		$styles['heading'][]   = 'font-family: ' . esc_attr( $font_title );
		$styles['paragraph'][] = 'font-family: ' . esc_attr( $font_text );
		$styles['labels'][]    = 'font-family: ' . esc_attr( $font_label );
		$styles['author'][]    = 'font-family: ' . esc_attr( $font_author );
		$styles['links'][]     = 'font-family: ' . esc_attr( $font_button );

		if ( $is_web && ! empty( $font_family ) ) {
			$styles['container'][] = 'font-family: ' . esc_attr( $font_family );
		}

		if ( ! empty( $padding ) && $is_web ) {
			$styles['container'][] = 'padding: ' . $padding['top'] . ' ' . $padding['right'] . ' ' . $padding['bottom'] . ' ' . $padding['left'];
		}

		if ( ! empty( $margin ) && $is_web ) {
			$styles['container'][] = 'margin: ' . $margin['top'] . ' ' . $margin['right'] . ' ' . $margin['bottom'] . ' ' . $margin['left'];
		}

		if ( ! empty( $border_size ) ) {
			$border_color          = $border_color ? $border_color : 'transparent';
			$styles['container'][] = 'border-width: ' . $border_size . 'px';
			$styles['container'][] = 'border-style: ' . $border_style;
			$styles['container'][] = 'border-color: ' . $border_color;
		}

		if ( ! empty( $background_color ) ) {
			$styles['container'][] = 'background-color: ' . $background_color;
		}

		if ( ! empty( $border_radius ) ) {
			$styles['container'][] = 'border-radius: ' . $border_radius . 'px';
		}

		if ( ! empty( $image_radius ) ) {
			$styles['images'][] = 'border-radius: ' . $image_radius . 'px';
		}

		if ( ! empty( $fontsize_label ) ) {
			$styles['labels'][] = 'font-size: ' . $fontsize_label;
		} else {
			$styles['labels'][] = 'font-size: 13px';
		}

		if ( ! empty( $label_color ) ) {
			$styles['labels'][] = 'color: ' . $label_color;
		}
		$styles['labels'][] = 'line-height: 1.5';
		$styles['labels'][] = 'padding: 2px 0';

		if ( ! empty( $fontsize_author ) ) {
			$styles['author'][] = 'font-size: ' . $fontsize_author;
		} else {
			$styles['author'][] = 'font-size: 14px';
		}

		if ( ! empty( $author_color ) ) {
			$styles['author'][] = 'color: ' . $author_color;
		}
		$styles['author'][] = 'line-height: 1.5';
		$styles['author'][] = 'padding: 2px 0';

		if ( ! empty( $fontsize_title ) ) {
			$styles['heading'][] = 'font-size: ' . $fontsize_title;
		} else {
			$styles['heading'][] = 'font-size: 24px';
		}
		if ( ! empty( $title_color ) ) {
			$styles['heading'][] = 'color: ' . $title_color . ' !important';
		}
		$styles['heading'][] = 'line-height: 1.1';
		$styles['heading'][] = 'padding: 2px 0';

		if ( ! empty( $fontsize_text ) ) {
			$styles['paragraph'][] = 'font-size: ' . $fontsize_text;
		} else {
			$styles['paragraph'][] = 'font-size: 14px';
		}
		if ( ! empty( $text_color ) ) {
			$styles['paragraph'][] = 'color: ' . $text_color;
		} else {
			$styles['paragraph'][] = 'color: ' . newsletterglue_get_theme_option( 'p_colour' );
		}
		$styles['paragraph'][] = 'line-height: 1.5';
		$styles['paragraph'][] = 'padding: 2px 0';

		if ( ! empty( $fontsize_text ) ) {
			$styles['links'][] = 'font-size: ' . $fontsize_text;
		} else {
			$styles['links'][] = 'font-size: 14px';
		}

		if ( ! empty( $link ) ) {
			if ( ! $is_web ) {
				$styles['links'][] = $cta_type === 'button' ? 'background-color: ' . $link . ' !important;' : 'color: ' . $link . ' !important;';
				$styles['links'][] = $cta_type === 'button' ? 'border-color: ' . $link . ' !important;' : '';
			} else {
				$styles['links'][] = $cta_type === 'button' ? 'background-color: ' . $link . ';' : 'color: ' . $link . ';';
			}
		} else {
			$styles['links'][] = $cta_type === 'button' ? 'background-color: ' . newsletterglue_get_theme_option( 'btn_bg' ) . ';' : 'color: ' . newsletterglue_get_theme_option( 'btn_bg' );
		}

		if ( $cta_type !== 'button' ) {
			$styles['links'][] = 'line-height: 1.5';
		}

		if ( $cta_type === 'button' ) {
			$styles['links'][] = 'background-color: ' . $button . ' !important;';
			$styles['links'][] = 'border-color: ' . $button . ' !important;';
			$styles['links'][] = 'color: ' . $button_text . ' !important;';
		}

		$styles['cta'][] = 'padding: 2px 0';

		return $styles;
	}

	/**
	 * Clean and prepare block attributes.
	 */
	public function setup_attrs( $atts ) {

		$args = array();

		$default_p = ! empty( newsletterglue_get_theme_option( 'p_size' ) ) ? newsletterglue_get_theme_option( 'p_size' ) : 14;
		$p_size    = absint( $default_p ) . 'px';

		$args['insert_rss_posts'] = isset( $atts['insert_rss_posts'] ) ? $atts['insert_rss_posts'] : false;
		$args['rss_feed']         = isset( $atts['rssfeed'] ) ? $atts['rssfeed'] : '';

		$args['posts_num']       = isset( $atts['posts_num'] ) ? absint( $atts['posts_num'] ) : 4;
		$args['words_num']       = isset( $atts['words_num'] ) ? absint( $atts['words_num'] ) : 30;
		$args['offset']          = isset( $atts['offset'] ) ? absint( $atts['offset'] ) : 0;
		$args['fontsize_title']  = ! empty( $atts['fontsize_title'] ) ? esc_attr( $atts['fontsize_title'] ) : '24px';
		$args['fontsize_text']   = ! empty( $atts['fontsize_text'] ) ? esc_attr( $atts['fontsize_text'] ) : $p_size;
		$args['fontsize_label']  = ! empty( $atts['fontsize_label'] ) ? esc_attr( $atts['fontsize_label'] ) : '13px';
		$args['fontsize_author'] = ! empty( $atts['fontsize_author'] ) ? esc_attr( $atts['fontsize_author'] ) : '14px';
		$args['image_radius']    = ! empty( $atts['image_radius'] ) ? absint( $atts['image_radius'] ) : 0;
		$args['border_radius']   = ! empty( $atts['border_radius'] ) ? absint( $atts['border_radius'] ) : 0;
		$args['border_size']     = ! empty( $atts['border_size'] ) ? absint( $atts['border_size'] ) : 0;

		$args['sortby']       = ! empty( $atts['sortby'] ) ? $atts['sortby']['value'] : 'newest';
		$args['border_style'] = ! empty( $atts['border_style'] ) ? $atts['border_style']['value'] : 'solid';

		$args['contentstyle']     = ! empty( $atts['contentstyle'] ) ? esc_attr( $atts['contentstyle'] ) : 'multi';
		$args['columns_num']      = ! empty( $atts['columns_num'] ) ? esc_attr( $atts['columns_num'] ) : 'one';
		$args['image_position']   = ! empty( $atts['image_position'] ) ? esc_attr( $atts['image_position'] ) : 'left';
		$args['table_ratio']      = ! empty( $atts['table_ratio'] ) ? esc_attr( $atts['table_ratio'] ) : '30_70';
		$args['filter']           = ! empty( $atts['filter'] ) ? esc_attr( $atts['filter'] ) : 'include';
		$args['postlength']       = ! empty( $atts['postlength'] ) ? esc_attr( $atts['postlength'] ) : 'excerpt';
		$args['label_type']       = ! empty( $atts['label_type'] ) ? esc_attr( $atts['label_type'] ) : 'category';
		$args['cta_type']         = ! empty( $atts['cta_type'] ) ? esc_attr( $atts['cta_type'] ) : 'link';
		$args['cta_link']         = ! empty( $atts['cta_link'] ) ? esc_attr( $atts['cta_link'] ) : 'Read more';
		$args['background_color'] = ! empty( $atts['background_color'] ) ? esc_attr( $atts['background_color'] ) : '';
		$args['border_color']     = ! empty( $atts['border_color'] ) ? esc_attr( $atts['border_color'] ) : '';
		$args['text_color']       = ! empty( $atts['text_color'] ) ? esc_attr( $atts['text_color'] ) : '';
		$args['label_color']      = ! empty( $atts['label_color'] ) ? esc_attr( $atts['label_color'] ) : '';
		$args['author_color']     = ! empty( $atts['author_color'] ) ? esc_attr( $atts['author_color'] ) : '';
		$args['title_color']      = ! empty( $atts['title_color'] ) ? esc_attr( $atts['title_color'] ) : $this->get_default( 'h3_colour' );
		$args['link']             = ! empty( $atts['link'] ) ? esc_attr( $atts['link'] ) : '';
		$args['button']           = ! empty( $atts['button'] ) ? esc_attr( $atts['button'] ) : newsletterglue_get_theme_option( 'btn_bg' );
		$args['button_text']      = ! empty( $atts['button_text'] ) ? esc_attr( $atts['button_text'] ) : newsletterglue_get_theme_option( 'btn_colour' );

		// Fix for content options in email campaigns - match the behavior of the post-embeds block
		// In the block editor, these are toggle controls where the default state is "on"
		// and toggling them sets the attribute to indicate they should be turned off
		// For most options, presence of attribute means "hide" (except for author and image)
		$args['show_label']   = isset( $atts['show_label'] ) ? false : true;
		$args['show_author']  = isset( $atts['show_author'] ) ? true : false;
		$args['show_heading'] = isset( $atts['show_heading'] ) ? false : true;
		$args['show_excerpt'] = isset( $atts['show_excerpt'] ) ? false : true;
		$args['show_cta']     = isset( $atts['show_cta'] ) ? false : true;
		$args['show_image']   = isset( $atts['show_image'] ) ? 'no-images' : 'images';
		
		// Add logging to debug content options when sending emails
		if (defined('NGL_IN_EMAIL')) {
			$log_file = WP_CONTENT_DIR . '/ngl_get_posts_latest_posts_block_renderer.log';
			$this->debug_log( "\nContent Options in setup_attrs:", $log_file );
			$this->debug_log( "show_label: " . ($args['show_label'] ? 'true' : 'false'), $log_file );
			$this->debug_log( "show_author: " . ($args['show_author'] ? 'true' : 'false'), $log_file );
			$this->debug_log( "show_heading: " . ($args['show_heading'] ? 'true' : 'false'), $log_file );
			$this->debug_log( "show_excerpt: " . ($args['show_excerpt'] ? 'true' : 'false'), $log_file );
			$this->debug_log( "show_cta: " . ($args['show_cta'] ? 'true' : 'false'), $log_file );
			$this->debug_log( "show_image: " . $args['show_image'], $log_file );
			$this->debug_log( "Original attributes: " . print_r($atts, true), $log_file );
		}

		$args['custom_data']       = ! empty( $atts['custom_data'] ) ? $atts['custom_data'] : '';
		$args['filter_authors']    = ! empty( $atts['filter_authors'] ) ? $atts['filter_authors'] : '';
		$args['filter_cpts']       = ! empty( $atts['filter_cpts'] ) ? $atts['filter_cpts'] : '';
		$args['filter_categories'] = ! empty( $atts['filter_categories'] ) ? $atts['filter_categories'] : '';
		$args['filter_tags']       = ! empty( $atts['filter_tags'] ) ? $atts['filter_tags'] : '';
		$args['filter_taxonomies'] = ! empty( $atts['taxonomies'] ) ? $atts['taxonomies'] : '';
		$args['padding']           = ! empty( $atts['padding'] ) ? $atts['padding'] : array(
			'top'    => '10px',
			'bottom' => '10px',
			'left'   => '20px',
			'right'  => '20px',
		);
		$args['margin']            = ! empty( $atts['margin'] ) ? $atts['margin'] : array(
			'top'    => '0px',
			'bottom' => '0px',
			'left'   => '0px',
			'right'  => '0px',
		);

		$args['dates']            = isset( $atts['dates'] ) ? $atts['dates']['value'] : '';
		$args['week_starts']      = isset( $atts['week_starts'] ) ? $atts['week_starts']['value'] : 'Monday';
		$args['two_weeks_starts'] = isset( $atts['two_weeks_starts'] ) ? $atts['two_weeks_starts']['value'] : 'Monday';
		$args['month_starts']     = isset( $atts['month_starts'] ) ? $atts['month_starts']['value'] : 'Monday';
		$args['starts_time']      = isset( $atts['starts_time'] ) ? $atts['starts_time']['value'] : '7pm';

		$args['cached'] = isset( $atts['posts'] ) ? $atts['posts'] : null;

		$args['hidden'] = isset( $atts['hidden_posts'] ) ? $atts['hidden_posts'] : null;

		if ( $args['show_image'] === 'no-images' ) {
			$args['table_ratio'] = 'full';
		}

		$args['font_family'] = isset( $atts['font']['style']['fontFamily'] ) ? $atts['font']['style']['fontFamily'] : newsletterglue_get_font_name( newsletterglue_get_theme_option( 'font' ) );

		$args['font_title']  = isset( $atts['font_title']['style']['fontFamily'] ) ? $atts['font_title']['style']['fontFamily'] : newsletterglue_get_font_name( newsletterglue_get_theme_option( 'font' ) );
		$args['font_text']   = isset( $atts['font_text']['style']['fontFamily'] ) ? $atts['font_text']['style']['fontFamily'] : newsletterglue_get_font_name( newsletterglue_get_theme_option( 'font' ) );
		$args['font_button'] = isset( $atts['font_button']['style']['fontFamily'] ) ? $atts['font_button']['style']['fontFamily'] : newsletterglue_get_font_name( newsletterglue_get_theme_option( 'font' ) );
		$args['font_label']  = isset( $atts['font_label']['style']['fontFamily'] ) ? $atts['font_label']['style']['fontFamily'] : newsletterglue_get_font_name( newsletterglue_get_theme_option( 'font' ) );
		$args['font_author'] = isset( $atts['font_author']['style']['fontFamily'] ) ? $atts['font_author']['style']['fontFamily'] : newsletterglue_get_font_name( newsletterglue_get_theme_option( 'font' ) );

		$args['div1']           = isset( $atts['div1'] ) ? $atts['div1'] : '';
		$args['div2']           = isset( $atts['div2'] ) ? $atts['div2'] : '';
		$args['itemBase']       = isset( $atts['itemBase'] ) ? $atts['itemBase'] : '';
		$args['containerWidth'] = isset( $atts['containerWidth'] ) ? $atts['containerWidth'] : '';
		$args['stacked']        = isset( $atts['stacked_on_mobile'] ) ? 'is-not-stacked' : 'is-stacked';

		$args['order'] = isset( $atts['order'] ) ? $atts['order'] : array( 1, 2, 3, 4, 5 );

		// Add detailed logging for the show_divider attribute
		if (defined('NGL_IN_EMAIL')) {
			$log_file = WP_CONTENT_DIR . '/ngl_divider_debug.log';
			$this->debug_log( "\nDivider Debug:", $log_file );
			$this->debug_log( "show_divider isset: " . (isset($atts['show_divider']) ? 'yes' : 'no'), $log_file );
			if (isset($atts['show_divider'])) {
				$this->debug_log( "show_divider value: " . var_export($atts['show_divider'], true), $log_file );
				$this->debug_log( "show_divider type: " . gettype($atts['show_divider']), $log_file );
			}
			$this->debug_log( "divider_size isset: " . (isset($atts['divider_size']) ? 'yes' : 'no'), $log_file );
			if (isset($atts['divider_size'])) {
				$this->debug_log( "divider_size value: " . var_export($atts['divider_size'], true), $log_file );
			}
		}

		// In the block editor, show_divider is a boolean toggle
		// When it's true, we should show the divider with the specified size or a default
		if (isset($atts['show_divider']) && $atts['show_divider'] == true) {
			// Use isset() instead of !empty() to handle '0' values correctly
			$args['divider_size'] = isset($atts['divider_size']) ? absint($atts['divider_size']) : 1;
			if (defined('NGL_IN_EMAIL')) {
				$this->debug_log( "Setting divider_size to: " . $args['divider_size'], $log_file );
			}
		} else {
			$args['divider_size'] = null;
			if (defined('NGL_IN_EMAIL')) {
				$this->debug_log( "Setting divider_size to null", $log_file );
			}
		}
		$args['divider_bg']   = ! empty( $atts['divider_bg'] ) ? esc_attr( $atts['divider_bg'] ) : '#eeeeee';

		return apply_filters( 'newsletterglue_get_latest_posts_atts', $args, $atts );
	}

	/**
	 * Get the posts.
	 */
	public function get_posts( $args ) {
		static $render_count = array();
		static $cached_results = array();
		static $offset_applied = array(); // Track whether offset has been applied for each block.

		extract( $args );

		$log_file = WP_CONTENT_DIR . '/ngl_get_posts_latest_posts_block_renderer.log';

		// Create a unique key for this block based on its parameters.
			$block_key = md5( serialize( $args ) );

		// Check if we already have cached results for this block.
		if ( isset( $cached_results[ $block_key ] ) ) {
			$this->debug_log( "\n\n======== USING CACHED RESULTS ========", $log_file );
			$this->debug_log( 'Block Key: ' . $block_key . " | Using cached results", $log_file );
			return $cached_results[ $block_key ];
		}

		// Initialize counter for this block if not set.
		if ( ! isset( $render_count[ $block_key ] ) ) {
			$render_count[ $block_key ] = 0;
		}

		// Increment the render count.
		$render_count[ $block_key ]++;

		// Add stack trace and timestamp to identify where this is being called from.
		$debug_backtrace = debug_backtrace( DEBUG_BACKTRACE_PROVIDE_OBJECT, 5 );
		$trace = array();
		foreach ( $debug_backtrace as $trace_item ) {
			$trace_entry = isset( $trace_item['class'] ) ? $trace_item['class'] . '::' . $trace_item['function'] : $trace_item['function'];
			$trace_entry .= ' in ' . ( isset( $trace_item['file'] ) ? basename( $trace_item['file'] ) : 'unknown file' );
			$trace_entry .= ' on line ' . ( isset( $trace_item['line'] ) ? $trace_item['line'] : 'unknown' );
			$trace[] = $trace_entry;
		}

		$this->debug_log( "\n\n======== NEW RENDER ========", $log_file );
		$this->debug_log( 'Block Key: ' . $block_key . ' | Render Count: ' . $render_count[ $block_key ], $log_file );
		$this->debug_log( 'Call Stack: ' . print_r( $trace, true ), $log_file );

		if ( $contentstyle === 'single' ) {
			$posts_num = 1;
		}

		// Store original post_num value.
		$original_posts_num = empty( $posts_num ) ? 99 : $posts_num;

		$this->debug_log( 'Original Posts Num: ' . print_r( $posts_num, true ), $log_file );

		// If offset is set, increase posts_per_page by offset value.
		$adjusted_posts_num = $original_posts_num;
		if ( ! empty( $offset ) && $offset > 0 ) {
			$adjusted_posts_num += $offset;
			$this->debug_log( 'Adjusted Posts Num +1: ' . print_r( $adjusted_posts_num, true ), $log_file );
		}

		$this->debug_log( 'Final Adjusted Posts Num: ' . print_r( $adjusted_posts_num, true ), $log_file );

		$query_args = array(
			'posts_per_page'      => $adjusted_posts_num,
			'post_type'           => apply_filters( 'newsletterglue_latest_posts_post_type', 'post' ),
			'post_status'         => array( 'publish' ),
			'ignore_sticky_posts' => true,
		);

		$this->debug_log( 'Query Args: ' . print_r( $query_args, true ), $log_file );

		// Post type filters.
		if ( ! empty( $filter_cpts ) ) {
			$cpts = array();
			foreach ( $filter_cpts as $key => $value ) {
				$cpts[] = $value['value'];
			}
			if ( ! empty( $cpts ) ) {
				$query_args['post_type'] = $cpts;
			}
		}

		// Add sorting parameter.
		if ( ! empty( $sortby ) ) {
			if ( $sortby === 'oldest' ) {
				$query_args['orderby'] = 'date';
				$query_args['order']   = 'ASC';
			}
			if ( $sortby === 'newest' ) {
				$query_args['orderby'] = 'date';
				$query_args['order']   = 'DESC';
			}
			if ( $sortby === 'alphabetic' ) {
				$query_args['orderby'] = 'title';
				$query_args['order']   = 'ASC';
			}
		}

		// Category filters.
		if ( ! empty( $filter_categories ) ) {
			$cat_terms = array();
			foreach ( $filter_categories as $key => $value ) {
				$cat_terms[] = $value['value'];
			}
			$filter_type = $filter === 'exclude' ? 'category__not_in' : 'category__in';
			if ( ! empty( $cat_terms ) ) {
				$query_args[ $filter_type ] = $cat_terms;
			}
		}

		// Tag filters.
		if ( ! empty( $filter_tags ) ) {
			$tag_terms = array();
			foreach ( $filter_tags as $key => $value ) {
				$tag_terms[] = $value['value'];
			}
			$filter_type = $filter === 'exclude' ? 'tag__not_in' : 'tag__in';
			if ( ! empty( $tag_terms ) ) {
				$query_args[ $filter_type ] = $tag_terms;
			}
		}

		// Taxonomy filters.
		if ( ! empty( $filter_taxonomies ) ) {
			$tax_rules = array( 'relation' => 'OR' );
			foreach ( $filter_taxonomies as $index => $tax ) {
				if ( empty( $tax['term'] ) ) {
					continue;
				}
				$tax_rules[] = array(
					'taxonomy' => $tax['key'],
					'field'    => 'term_id',
					'terms'    => absint( $tax['term'] ),
				);
			}
			$query_args[ 'tax_query' ] = array( $tax_rules ); // phpcs:ignore.
		}

		// Author filters.
		if ( ! empty( $filter_authors ) ) {
			$users = array();
			foreach ( $filter_authors as $key => $value ) {
				$users[] = $value['value'];
			}
			$filter_type = $filter === 'exclude' ? 'author__not_in' : 'author__in';
			if ( ! empty( $users ) ) {
				$query_args[ $filter_type ] = $users;
			}
		}

		// Build date queries.
		if ( ! empty( $dates ) ) {
			if ( $dates === 'last_1' ) {
				$args['date_query'] = array(
					'after' => gmdate( 'm/d/Y g:ia', strtotime( '-24 hours' ) ),
				);
			}

			if ( $dates === 'last_2' ) {
				$args['date_query'] = array(
					'after' => gmdate( 'm/d/Y g:ia', strtotime( '-2 days' ) ),
				);
			}

			if ( $dates === 'last_3' ) {
				$args['date_query'] = array(
					'after' => gmdate( 'm/d/Y g:ia', strtotime( '-3 days' ) ),
				);
			}

			if ( $dates === 'last_4' ) {
				$args['date_query'] = array(
					'after' => gmdate( 'm/d/Y g:ia', strtotime( '-4 days' ) ),
				);
			}

			if ( $dates === 'last_5' ) {
				$args['date_query'] = array(
					'after' => gmdate( 'm/d/Y g:ia', strtotime( '-5 days' ) ),
				);
			}

			if ( $dates === 'last_6' ) {
				$args['date_query'] = array(
					'after' => gmdate( 'm/d/Y g:ia', strtotime( '-6 days' ) ),
				);
			}

			if ( $dates === 'last_7' ) {
				$args['date_query'] = array(
					'after' => gmdate( 'm/d/Y g:ia', strtotime( '-7 days' ) ),
				);
			}

			if ( $dates === 'last_14' ) {
				$args['date_query'] = array(
					'after' => gmdate( 'm/d/Y g:ia', strtotime( '-14 days' ) ),
				);
			}

			if ( $dates === 'last_30' ) {
				$args['date_query'] = array(
					'after' => gmdate( 'm/d/Y g:ia', strtotime( '-30 days' ) ),
				);
			}

			if ( $dates === 'last_60' ) {
				$args['date_query'] = array(
					'after' => gmdate( 'm/d/Y g:ia', strtotime( '-60 days' ) ),
				);
			}

			if ( ! empty( $args['date_query'] ) ) {
				$query_args['date_query'] = array( $args['date_query'] );
			}
		}

		$the_query = new WP_Query();

		if ( ! empty( $args['cached'] ) ) {
			$ids    = array();
			$cached = $args['cached'];
			foreach ( $cached as $n_post ) {
				$ids[] = $n_post['id'];
			}
			$query_args['post__in'] = $ids;
		}

		$posts = $the_query->query( apply_filters( 'newsletterglue_latest_posts_query_args_frontend', $query_args, $args ) );

		// Track whether offset has been applied for this block.
		if ( ! isset( $offset_applied[ $block_key ] ) ) {
			$offset_applied[ $block_key ] = false;
		}

		// Apply offset using array_slice if needed and if not already applied.
		if ( ! empty( $offset ) && $offset > 0 && ! empty( $posts ) && ! $offset_applied[ $block_key ] ) {
			$this->debug_log( "Applying offset for the first time: offset = $offset, original_posts_num = $original_posts_num", $log_file );
			$posts = array_slice( $posts, $offset, $original_posts_num );
			$offset_applied[ $block_key ] = true;
		} else if ( ! empty( $offset ) && $offset > 0 ) {
			$this->debug_log( "Skipping offset application as it was already applied or not needed", $log_file );
		}

		$log_posts = array();
		foreach ( $posts as $post ) {
			$log_posts[] = array(
				'post_id'    => $post->ID,
				'post_author' => $post->post_author,
				'post_date'   => $post->post_date,
				'post_title'  => $post->post_title,
			);
		}
		$this->debug_log( 'Offset with Array_Slice: ' . print_r( $log_posts, true ), $log_file );

		if ( ! empty( $posts ) ) {

			// Prepare custom data.
			$custom = array();
			if ( ! empty( $custom_data ) ) {
				foreach ( $custom_data as $key => $entry ) {
					foreach ( $entry as $subdata => $data_in ) {
						if ( $data_in ) {
							$datatype                       = explode( '_', $subdata );
							$data_is                        = $datatype[0];
							$post_id                        = str_replace( '_', '', $datatype[1] );
							$custom[ $post_id ][ $data_is ] = $data_in;
						}
					}
				}
			}

			$thumb_size = 'full';

			$results = array();
			foreach ( $posts as $post ) {

				if ( ! empty( $args['hidden'] ) && in_array( $post->ID, $args['hidden'] ) ) {
					continue;
				}

				$image_arr = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), apply_filters( 'newsletterglue_latest_posts_thumbnail_size', $thumb_size, $table_ratio, $post ) );
				$featured  = empty( $image_arr ) ? NGL_PLUGIN_URL . 'assets/images/placeholder.png' : $image_arr[0];

				if ( isset( $custom[ $post->ID ]['image'] ) ) {
					$featured = $custom[ $post->ID ]['image'];
				}

				if ( $label_type == 'category' ) {
					$labels = $this->get_categories_text( $post->ID );
				} elseif ( $label_type == 'tag' ) {
					$labels = $this->get_categories_text( $post->ID, 'post_tag' );
				} elseif ( $label_type == 'domain' ) {
					$labels = $this->get_domain();
				} else {
					$labels = $this->get_author( $post );
				}

				$author = $this->get_author( $post );

				$cta_link = $args['cta_link'];
				if ( isset( $custom[ $post->ID ]['ctalink'] ) ) {
					$cta_link = $custom[ $post->ID ]['ctalink'];
				}

				if ( isset( $custom[ $post->ID ]['label'] ) ) {
					$labels = $custom[ $post->ID ]['label'];
				}

				if ( isset( $custom[ $post->ID ]['author'] ) ) {
					$author = $custom[ $post->ID ]['author'];
				}

				if ( isset( $custom[ $post->ID ]['title'] ) ) {
					$title = $custom[ $post->ID ]['title'];
				} else {
					$title = $post->post_title;
				}

				if ( isset( $custom[ $post->ID ]['excerpt'] ) ) {
					$excerpt = $custom[ $post->ID ]['excerpt'];
					$excerpt = wp_trim_words( $excerpt, $words_num, '...' );
					$excerpt = wpautop( $excerpt );
				} else {
					$excerpt = $this->get_excerpt( $post, $words_num, $postlength, $contentstyle );
				}

				$results[] = array(
					'id'             => $post->ID,
					'post_title'     => $title,
					'featured_image' => $featured,
					'thumbnail_id'   => get_post_thumbnail_id( $post->ID ),
					'post_content'   => $excerpt,
					'domain'         => $this->get_domain(),
					'categories'     => $this->get_categories_text( $post->ID ),
					'tags'           => $this->get_categories_text( $post->ID, 'post_tag' ),
					'permalink'      => apply_filters( 'newsletterglue_latest_posts_perma', get_permalink( $post->ID ), $post->ID ),
					'labels'         => $labels,
					'author'         => $author,
					'cta_link'       => $cta_link,
				);
			}

			$results = apply_filters( 'newsletterglue_latest_posts_results', $results, $args, $block_key );

			$this->debug_log( 'Results to be returned: ' . print_r( $results, true ), $log_file );

			// Cache the results for future use.
			$cached_results[ $block_key ] = $results;

			return $results;
		}

		// Cache empty result to avoid re-processing.
		$cached_results[ $block_key ] = null;
		return null;
	}

	/**
	 * Get post author.
	 */
	public function get_author( $post ) {
		return sprintf( __( 'By %s', 'newsletter-glue' ), get_the_author_meta( 'display_name', $post->post_author ) );
	}

	/**
	 * Get post content.
	 */
	public function get_excerpt( $post = null, $words = 30, $postlength = 'excerpt', $contentstyle = 'multi' ) {
		$content = $post->post_content;
		$excerpt = $post->post_excerpt;

		$content = strip_shortcodes( html_entity_decode( $content ) );
		$content = apply_filters( 'the_content', $content );
		$content = str_replace( ']]>', ']]&gt;', $content );
		$content = preg_replace( '/\[([^\[\]]++|(?R))*+\]/', '', $content );

		if ( ( $postlength === 'full' && $contentstyle === 'single' ) || $words == 0 ) {
			return apply_filters( 'newsletterglue_email_custom_content', wpautop( $content ), $post, $content, $words, $postlength, $contentstyle );
		} else {
			if ( ! empty( $excerpt ) ) {
				$text = $excerpt;
			} else {
				$content = strip_shortcodes( html_entity_decode( $content ) );
				$content = apply_filters( 'the_content', $content );
				$content = str_replace( ']]>', ']]&gt;', $content );
				$text    = $content;
			}

			$excerpt_length = apply_filters( 'newsletterglue_get_excerpt_length', $words );

			$text = wp_kses( $text, newsletterglue_allowed_tags_for_excerpt() );

			$text = wp_trim_words( $text, $excerpt_length, '...' );

			$text = '<p>' . wp_kses_post( html_entity_decode( $text ) ) . '</p>';

			return apply_filters( 'newsletterglue_email_custom_excerpt', $text, $post, $content, $words, $postlength, $contentstyle );
		}
	}

	/**
	 * Get domain.
	 */
	public function get_domain() {
		$parse = wp_parse_url( home_url() );
		if ( isset( $parse['host'] ) ) {
			return str_replace( 'www.', '', $parse['host'] );
		}
		return home_url();
	}

	/**
	 * Get categories as label.
	 */
	public function get_categories_text( $post_id, $taxonomy = 'category' ) {
		$output_array = array();
		$categories   = wp_get_object_terms( $post_id, $taxonomy );
		if ( ! empty( $categories ) ) {
			foreach ( $categories as $category ) {
				$output_array[] = $category->name;
			}
		}
		return implode( ', ', $output_array );
	}

	/**
	 * Render: web.
	 */
	public function render_block_web( $block_content, $block ) {
		static $render_web_count = array();

		$log_file = WP_CONTENT_DIR . '/ngl_get_posts_latest_posts_block_renderer.log';

		if ( defined( 'NGL_IN_EMAIL' ) ) {
			return $block_content;
		}

		if ( 'newsletterglue/latest-posts' !== $block['blockName'] ) {
			return $block_content;
		}

		// Create a unique key for this block based on its attributes.
		$block_key = md5( serialize( $block['attrs'] ) );

		// Initialize counter for this block if not set.
		if ( ! isset( $render_web_count[ $block_key ] ) ) {
			$render_web_count[ $block_key ] = 0;
		}

		// Increment the render count.
		$render_web_count[ $block_key ]++;

		// Add detailed stack trace to identify where this is being called from.
		$debug_backtrace = debug_backtrace( DEBUG_BACKTRACE_PROVIDE_OBJECT, 15 );
		$trace = array();
		foreach ( $debug_backtrace as $trace_item ) {
			$trace_entry = isset( $trace_item['class'] ) ? $trace_item['class'] . '::' . $trace_item['function'] : $trace_item['function'];
			$trace_entry .= ' in ' . ( isset( $trace_item['file'] ) ? basename( $trace_item['file'] ) : 'unknown file' );
			$trace_entry .= ' on line ' . ( isset( $trace_item['line'] ) ? $trace_item['line'] : 'unknown' );
			$trace[] = $trace_entry;
		}

		$this->debug_log( "\n\n======== NEW WEB RENDER ========", $log_file );
		$this->debug_log( 'Block Key: ' . $block_key . ' | Render Count: ' . $render_web_count[ $block_key ], $log_file );
		$this->debug_log( 'Call Stack: ' . print_r( $trace, true ), $log_file );
		$this->debug_log( 'Block Attributes: ' . print_r( $block['attrs'], true ), $log_file );
		$this->debug_log( 'Block Content Length: ' . strlen( $block_content ), $log_file );

		// Only process the first render for each unique block configuration.
		if ( $render_web_count[ $block_key ] > 1 ) {
			$this->debug_log( "SKIPPING DUPLICATE WEB RENDER FOR THIS BLOCK", $log_file );
			return $block_content; // Return original content instead of null to preserve block structure.
		}

		$args  = $this->setup_attrs( $block['attrs'] );
		$posts = $this->get_posts( $args );

		if ( empty( $posts ) ) {
			return null;
		}

		$classes = $this->get_classes( $args );
		$styles  = $this->get_styles( $args );

		ob_start();

		extract( $args );

		include NGL_PLUGIN_DIR . 'includes/renders/latest-posts/web.php';

		return ob_get_clean();
	}

	/**
	 * Render: email.
	 */
	public function render_block_mail( $block_content, $block ) {
		static $render_mail_count = array();
		static $processed_blocks = array();

		$log_file = WP_CONTENT_DIR . '/ngl_get_posts_latest_posts_block_render_email.log';

		if ( ! defined( 'NGL_IN_EMAIL' ) ) {
			return $block_content;
		}

		if ( 'newsletterglue/latest-posts' !== $block['blockName'] ) {
			return $block_content;
		}

		if ( isset( $block['attrs']['show_in_email'] ) ) {
			return null;
		}
		
		// Ensure divider attributes are correctly set
		if ( isset( $block['attrs']['show_divider'] ) ) {
			// If show_divider is true but divider_size is not set or is '0', set it to 1
			if ( !isset( $block['attrs']['divider_size'] ) || $block['attrs']['divider_size'] === '0' ) {
				$block['attrs']['divider_size'] = '1';
			}
		}

		// Create a unique key for this block based on its attributes.
		$block_key = md5( serialize( $block['attrs'] ) );

		// Check if this block has already been processed in this request.
		if ( isset( $processed_blocks[ $block_key ] ) ) {
			$this->debug_log( "\n\n======== SKIPPING ALREADY PROCESSED BLOCK ========", $log_file );
			$this->debug_log( 'Block Key: ' . $block_key . " | Already processed", $log_file );
			return $processed_blocks[ $block_key ]; // Return the previously rendered content.
		}
/*
		// Initialize counter for this block if not set.
		if ( ! isset( $render_mail_count[ $block_key ] ) ) {
			$render_mail_count[ $block_key ] = 0;
		}

		// Increment the render count.
		$render_mail_count[ $block_key ]++;

		// Add detailed stack trace to identify where this is being called from.
		$debug_backtrace = debug_backtrace( DEBUG_BACKTRACE_PROVIDE_OBJECT, 15 );
		$trace = array();
		foreach ( $debug_backtrace as $trace_item ) {
			$trace_entry = isset( $trace_item['class'] ) ? $trace_item['class'] . '::' . $trace_item['function'] : $trace_item['function'];
			$trace_entry .= ' in ' . ( isset( $trace_item['file'] ) ? basename( $trace_item['file'] ) : 'unknown file' );
			$trace_entry .= ' on line ' . ( isset( $trace_item['line'] ) ? $trace_item['line'] : 'unknown' );
			$trace[] = $trace_entry;
		}

		file_put_contents( $log_file, "\n\n======== NEW EMAIL RENDER AT " . date( 'Y-m-d H:i:s' ) . " ========\n", FILE_APPEND );
		file_put_contents( $log_file, 'Block Key: ' . $block_key . ' | Render Count: ' . $render_mail_count[ $block_key ] . PHP_EOL, FILE_APPEND );
		file_put_contents( $log_file, 'Call Stack: ' . print_r( $trace, true ) . PHP_EOL, FILE_APPEND );
		file_put_contents( $log_file, 'Block Attributes: ' . print_r( $block['attrs'], true ) . PHP_EOL, FILE_APPEND );
		file_put_contents( $log_file, 'Block Content Length: ' . strlen( $block_content ) . PHP_EOL, FILE_APPEND );

		// Only process the first render for each unique block configuration.
		if ( $render_mail_count[ $block_key ] > 1 ) {
			file_put_contents( $log_file, "SKIPPING DUPLICATE EMAIL RENDER FOR THIS BLOCK\n", FILE_APPEND );
			return $block_content; // Return original content instead of null to preserve block structure.
		}*/

		$args = $this->setup_attrs( $block['attrs'] );

		// Check if the block attributes already contain posts data.
		// If so, use that instead of making a new query.
		$posts = null;
		if ( isset( $block['attrs']['posts'] ) && ! empty( $block['attrs']['posts'] ) ) {
			$this->debug_log( "Using posts from block attributes instead of making a new query", $log_file );
			$posts = $block['attrs']['posts'];
			
			// Apply custom_data edits to posts from block attributes
			if ( isset( $block['attrs']['custom_data'] ) && ! empty( $block['attrs']['custom_data'] ) ) {
				$this->debug_log( "Applying custom_data edits to posts from attributes", $log_file );
				
				// Prepare custom data
				$custom = array();
				foreach ( $block['attrs']['custom_data'] as $key => $entry ) {
					foreach ( $entry as $subdata => $data_in ) {
						if ( $data_in ) {
							$datatype = explode( '_', $subdata );
							$data_is = $datatype[0];
							$post_id = str_replace( '_', '', $datatype[1] );
							$custom[ $post_id ][ $data_is ] = $data_in;
							$this->debug_log( "Found custom data: {$data_is} for post {$post_id}", $log_file );
						}
					}
				}
				
				// Apply custom data to posts
				foreach ( $posts as $key => $post ) {
					$post_id = $post['id'];
					
					// Apply title edit
					if ( isset( $custom[ $post_id ]['title'] ) ) {
						$posts[$key]['post_title'] = $custom[ $post_id ]['title'];
						$this->debug_log( "Applied custom title for post {$post_id}: {$custom[ $post_id ]['title']}", $log_file );
					}
					
					// Apply image edit
					if ( isset( $custom[ $post_id ]['image'] ) ) {
						$posts[$key]['featured_image'] = $custom[ $post_id ]['image'];
						$this->debug_log( "Applied custom image for post {$post_id}", $log_file );
					}
					
					// Apply label edit
					if ( isset( $custom[ $post_id ]['label'] ) ) {
						$posts[$key]['labels'] = $custom[ $post_id ]['label'];
						$this->debug_log( "Applied custom label for post {$post_id}: {$custom[ $post_id ]['label']}", $log_file );
					}
					
					// Apply author edit
					if ( isset( $custom[ $post_id ]['author'] ) ) {
						$posts[$key]['author'] = $custom[ $post_id ]['author'];
						$this->debug_log( "Applied custom author for post {$post_id}: {$custom[ $post_id ]['author']}", $log_file );
					}
					
					// Apply excerpt edit
					if ( isset( $custom[ $post_id ]['excerpt'] ) ) {
						$excerpt = $custom[ $post_id ]['excerpt'];
						$excerpt = wp_trim_words( $excerpt, $args['words_num'], '...' );
						$excerpt = wpautop( $excerpt );
						$posts[$key]['post_content'] = $excerpt;
						$this->debug_log( "Applied custom excerpt for post {$post_id}", $log_file );
					}
					
					// Apply CTA link edit
					if ( isset( $custom[ $post_id ]['ctalink'] ) ) {
						$posts[$key]['cta_link'] = $custom[ $post_id ]['ctalink'];
						$this->debug_log( "Applied custom CTA link for post {$post_id}: {$custom[ $post_id ]['ctalink']}", $log_file );
					}
				}
			}

			// Filter out hidden posts if hidden_posts attribute exists
			if ( isset( $args['hidden'] ) && !empty( $args['hidden'] ) ) {
				$this->debug_log( "Filtering out hidden posts from attributes: " . print_r( $args['hidden'], true ), $log_file );
				$filtered_posts = array();
				foreach ( $posts as $post ) {
					if ( !in_array( $post['id'], $args['hidden'] ) ) {
						$filtered_posts[] = $post;
					} else {
						$this->debug_log( "Hiding post ID: " . $post['id'] . " (" . $post['post_title'] . ")", $log_file );
					}
				}
				$posts = $filtered_posts;
			}

			// Process posts from attributes to ensure they have all required keys
			$label_type = isset($args['label_type']) ? $args['label_type'] : 'category';
			$this->debug_log( 'Processing posts from attributes to ensure all required keys exist', $log_file );
			$this->debug_log( 'Label type: ' . $label_type, $log_file );
			
			// Log the first post structure before processing
			if (!empty($posts)) {
				$this->debug_log( 'First post structure before processing: ' . print_r( $posts[0], true ), $log_file );
			}
			
			// Process each post to ensure it has all required keys
			foreach ( $posts as $key => $post ) {
				// Add labels key if missing
				if (!isset($post['labels'])) {
					if ($label_type == 'category' && isset($post['categories'])) {
						$posts[$key]['labels'] = $post['categories'];
					} elseif ($label_type == 'tag' && isset($post['tags'])) {
						$posts[$key]['labels'] = $post['tags'];
					} elseif ($label_type == 'domain' && isset($post['domain'])) {
						$posts[$key]['labels'] = $post['domain'];
					} elseif (isset($post['author'])) {
						$posts[$key]['labels'] = $post['author'];
					} else {
						$posts[$key]['labels'] = '';
					}
				}
				
				// Ensure other required keys exist
				if (!isset($post['author']) && isset($post['id'])) {
					// Try to get author from post ID
					$author_name = get_the_author_meta('display_name', get_post_field('post_author', $post['id']));
					$posts[$key]['author'] = !empty($author_name) ? 'By ' . $author_name : '';
				}
				
				// Ensure cta_link exists
				if (!isset($post['cta_link']) && isset($args['cta_link'])) {
					$posts[$key]['cta_link'] = $args['cta_link'];
				}
				
				// Ensure divider attributes exist if needed
				// Always set divider_size directly from args - this is the key that controls visibility
				$posts[$key]['divider_size'] = isset($args['divider_size']) ? $args['divider_size'] : null;
				
				// Set other divider attributes if they exist in args
				if (!isset($post['show_divider']) && isset($args['show_divider'])) {
					$posts[$key]['show_divider'] = $args['show_divider'];
				}
				if (!isset($post['divider_bg']) && isset($args['divider_bg'])) {
					$posts[$key]['divider_bg'] = $args['divider_bg'];
				}
				if (!isset($post['divider_color']) && isset($args['divider_color'])) {
					$posts[$key]['divider_color'] = $args['divider_color'];
				}
				if (!isset($post['divider']) && isset($args['divider'])) {
					$posts[$key]['divider'] = $args['divider'];
				}
			}
			
			// Log the first post structure after processing
			if (!empty($posts)) {
				$this->debug_log( 'First post structure after processing: ' . print_r( $posts[0], true ), $log_file );
			}

			//$posts = apply_filters( 'newsletterglue_latest_posts_results', $posts, $args, $block_key );
			
			// Log summary of processed posts
			$log_posts = array();
			foreach ( $posts as $post ) {
				$log_posts[] = array(
					'id' => $post['id'],
					'post_title' => $post['post_title'],
					'has_labels' => isset($post['labels']) ? 'yes' : 'no',
					'labels_value' => isset($post['labels']) ? $post['labels'] : 'missing',
					'has_author' => isset($post['author']) ? 'yes' : 'no',
					'has_cta_link' => isset($post['cta_link']) ? 'yes' : 'no',
					'has_show_divider' => isset($post['show_divider']) ? 'yes' : 'no',
					'has_divider_size' => isset($post['divider_size']) ? 'yes' : 'no',
					'has_divider_bg' => isset($post['divider_bg']) ? 'yes' : 'no',
					'has_divider_color' => isset($post['divider_color']) ? 'yes' : 'no',
					'has_divider' => isset($post['divider']) ? 'yes' : 'no',
				);
			}
			$this->debug_log( 'Posts from attributes (after hidden filtering): ' . print_r( $log_posts, true ), $log_file );
		} else {
			// If no posts in attributes, get them using the standard method.
			$this->debug_log( "No posts in block attributes, fetching fresh posts", $log_file );
			$posts = $this->get_posts( $args );
		}

		if ( empty( $posts ) ) {
			$this->debug_log( "No posts found, returning null", $log_file );
			return null;
		}

		$classes = $this->get_classes( $args );
		$styles  = $this->get_styles( $args, false );
		
		// Set up the divider variables needed by the template
		// Force divider to show if show_divider is true in the block attributes
		if (isset($block['attrs']['show_divider']) && $block['attrs']['show_divider'] === true) {
			// If divider is enabled in the block editor, force it to show with a default size if not specified
			$divider_size = isset($block['attrs']['divider_size']) && !empty($block['attrs']['divider_size']) ? $block['attrs']['divider_size'] : 1;
			$this->debug_log( "Forcing divider to show with size: {$divider_size}px", $log_file );
		} else {
			$divider_size = isset($args['divider_size']) ? $args['divider_size'] : null;
		}
		
		$divider_bg = isset($args['divider_bg']) ? $args['divider_bg'] : '#e5e5e5';
		$is_full = $args['table_ratio'] === 'full' ? '1' : '3';
		
		// Add these to args so they're available after extract()
		$args['divider_size'] = $divider_size;
		$args['divider_bg'] = $divider_bg;
		$args['is_full'] = $is_full;
		
		// Additional logging for divider debugging
		$this->debug_log( "\nDivider debug in render_block_mail:", $log_file );
		$this->debug_log( "divider_size (before extract): " . var_export($divider_size, true), $log_file );
		$this->debug_log( "divider_bg (before extract): " . var_export($divider_bg, true), $log_file );
		$this->debug_log( "is_full (before extract): " . var_export($is_full, true), $log_file );

		// Log the content options just before rendering the email
		$this->debug_log( "\nContent Options in render_block_mail before extract:", $log_file );
		$this->debug_log( "show_label: " . ($args['show_label'] ? 'true' : 'false'), $log_file );
		$this->debug_log( "show_author: " . ($args['show_author'] ? 'true' : 'false'), $log_file );
		$this->debug_log( "show_heading: " . ($args['show_heading'] ? 'true' : 'false'), $log_file );
		$this->debug_log( "show_excerpt: " . ($args['show_excerpt'] ? 'true' : 'false'), $log_file );
		$this->debug_log( "show_cta: " . ($args['show_cta'] ? 'true' : 'false'), $log_file );
		$this->debug_log( "show_image: " . $args['show_image'], $log_file );
		$this->debug_log( "divider_size: " . (isset($args['divider_size']) ? $args['divider_size'] : 'null'), $log_file );
		$this->debug_log( "divider_bg: " . $args['divider_bg'], $log_file );
		$this->debug_log( "is_full: " . $args['is_full'], $log_file );

		ob_start();

		extract( $args );

		// Log the content options after extract to verify they're still correct
		$this->debug_log( "\nContent Options in render_block_mail after extract:", $log_file );
		$this->debug_log( "show_label: " . ($show_label ? 'true' : 'false'), $log_file );
		
		// Log divider variables after extract
		$this->debug_log( "divider_size after extract: " . (isset($divider_size) ? var_export($divider_size, true) : 'not set'), $log_file );
		$this->debug_log( "divider_size empty check: " . (!empty($divider_size) ? 'not empty' : 'empty'), $log_file );
		$this->debug_log( "divider_bg after extract: " . (isset($divider_bg) ? $divider_bg : 'not set'), $log_file );
		$this->debug_log( "is_full after extract: " . (isset($is_full) ? $is_full : 'not set'), $log_file );
		$this->debug_log( "show_author: " . ($show_author ? 'true' : 'false'), $log_file );
		$this->debug_log( "show_heading: " . ($show_heading ? 'true' : 'false'), $log_file );
		$this->debug_log( "show_excerpt: " . ($show_excerpt ? 'true' : 'false'), $log_file );
		$this->debug_log( "show_cta: " . ($show_cta ? 'true' : 'false'), $log_file );
		$this->debug_log( "show_image: " . $show_image, $log_file );

		include NGL_PLUGIN_DIR . 'includes/renders/latest-posts/email.php';

		$rendered_content = ob_get_clean();

		// Store the rendered content for this block.
		$processed_blocks[ $block_key ] = $rendered_content;

		return $rendered_content;
	}

	/**
	 * Get default attribute.
	 */
	public function get_default( $attr ) {
		return newsletterglue_get_theme_option( $attr );
	}
}

return new NGL_Render_Latest_Posts();
