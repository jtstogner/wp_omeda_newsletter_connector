<?php
/**
 * Latest posts block render.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * NGL_Render_Post_Embeds class.
 */
class NGL_Render_Post_Embeds {

	/**
	 * Constructor.
	 */
	public function __construct() {

		add_action( 'newsletterglue_add_block_styles', array( $this, 'block_css' ), 20 );

		add_filter( 'render_block', array( $this, 'render_block_web' ), 50, 3 );
		add_filter( 'render_block', array( $this, 'render_block_mail' ), 99, 3 );
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
		.ngl-table-post-embeds h3 a {
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
			margin: 0 0 10px;
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
	 */
	public function get_classes( $args = array() ) {
		extract( $args );

		$class_arr   = array( 'wp-block-newsletterglue-post-embeds' );
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

		$args['posts_num']       = ! empty( $atts['posts_num'] ) ? absint( $atts['posts_num'] ) : 0;
		$args['words_num']       = isset( $atts['words_num'] ) ? absint( $atts['words_num'] ) : 30;
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
		$args['label_type']       = ! empty( $atts['label_type'] ) ? esc_attr( $atts['label_type'] ) : 'domain';
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

		$args['show_label']   = isset( $atts['show_label'] ) ? false : true;
		$args['show_author']  = isset( $atts['show_author'] ) ? true : false;
		$args['show_heading'] = isset( $atts['show_heading'] ) ? false : true;
		$args['show_excerpt'] = isset( $atts['show_excerpt'] ) ? false : true;
		$args['show_cta']     = isset( $atts['show_cta'] ) ? false : true;
		$args['show_image']   = isset( $atts['show_image'] ) ? 'no-images' : 'images';

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

		$args['order']        = isset( $atts['order'] ) ? $atts['order'] : array( 1, 2, 3, 4, 5 );
		$args['embeds_order'] = isset( $atts['embeds_order'] ) ? $atts['embeds_order'] : array();
		$args['embeds']       = isset( $atts['embeds'] ) ? $atts['embeds'] : array();
		$args['divider_size'] = isset( $atts['show_divider'] ) && ! empty( $atts['divider_size'] ) ? absint( $atts['divider_size'] ) : null;
		$args['divider_bg']   = ! empty( $atts['divider_bg'] ) ? esc_attr( $atts['divider_bg'] ) : '#eeeeee';

		return apply_filters( 'newsletterglue_get_latest_posts_atts', $args, $atts );
	}

	/**
	 * Get the posts.
	 */
	public function get_posts( $args ) {
		extract( $args );

		if ( ! empty( $embeds ) ) {

			$results = array();
			foreach ( $embeds as $index => $item ) {

				if ( ! $item['enabled'] ) {
					continue;
				}

				$is_remote = isset( $item['remote'] ) && $item['remote'] === 'yes' ? true : false;

				$labels = '';
				if ( $label_type == 'category' ) {
					$labels = ! empty( $item['categories'] ) ? $item['categories'] : '';
				}
				if ( $label_type == 'tag' ) {
					$labels = ! empty( $item['tags'] ) ? $item['tags'] : '';
				}
				if ( $label_type == 'domain' ) {
					$labels = ! empty( $item['domain'] ) ? $item['domain'] : '';
				}

				$itemArray = array(
					'id'             => ! $is_remote ? $item['post_id'] : 0,
					'post_title'     => $item['title'],
					'featured_image' => $item['image'],
					'thumbnail_id'   => ! $is_remote ? get_post_thumbnail_id( $item['post_id'] ) : 0,
					'post_content'   => ! strstr( $item['content'], '</p>' ) ? wpautop( $item['content'] ) : $item['content'],
					'domain'         => $item['domain'],
					'categories'     => ! $is_remote ? $this->get_categories_text( $item['post_id'] ) : '',
					'tags'           => ! $is_remote ? $this->get_categories_text( $item['post_id'], 'post_tag' ) : '',
					'permalink'      => $is_remote ? $item['id'] : apply_filters( 'newsletterglue_latest_posts_perma', get_permalink( $item['post_id'] ), $item['post_id'] ),
					'author'         => $is_remote ? '' : $item['author'],
					'labels'         => $labels,
					'cta_link'       => $cta_link,
				);

				if ( ! empty( $item['custom'] ) ) {
					if ( ! empty( $item['custom']['label'] ) ) {
						$itemArray['labels'] = $item['custom']['label'];
					}
					if ( ! empty( $item['custom']['image'] ) ) {
						$itemArray['featured_image'] = $item['custom']['image'];
					}
					if ( ! empty( $item['custom']['title'] ) ) {
						$itemArray['post_title'] = $item['custom']['title'];
					}
					if ( ! empty( $item['custom']['content'] ) ) {
						$itemArray['post_content'] = $item['custom']['content'];
					}
					if ( ! empty( $item['custom']['more'] ) ) {
						$itemArray['cta_link'] = $item['custom']['more'];
					}
					if ( ! empty( $item['custom']['author'] ) ) {
						$itemArray['author'] = $item['custom']['author'];
					}
				}

				$results[ $index ] = $itemArray;
			}

			return $results;
		}

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

		if ( defined( 'NGL_IN_EMAIL' ) ) {
			return $block_content;
		}

		if ( 'newsletterglue/post-embeds' !== $block['blockName'] ) {
			return $block_content;
		}

		if ( isset( $block['attrs']['show_in_web'] ) ) {
			return null;
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

		include NGL_PLUGIN_DIR . 'includes/renders/post-embeds/web.php';

		return ob_get_clean();
	}

	/**
	 * Render: email.
	 */
	public function render_block_mail( $block_content, $block ) {

		if ( ! defined( 'NGL_IN_EMAIL' ) ) {
			return $block_content;
		}

		if ( 'newsletterglue/post-embeds' !== $block['blockName'] ) {
			return $block_content;
		}

		if ( isset( $block['attrs']['show_in_email'] ) ) {
			return null;
		}

		$args  = $this->setup_attrs( $block['attrs'] );
		$posts = $this->get_posts( $args );

		if ( empty( $posts ) ) {
			return null;
		}

		$classes = $this->get_classes( $args );
		$styles  = $this->get_styles( $args, false );

		ob_start();

		extract( $args );

		include NGL_PLUGIN_DIR . 'includes/renders/post-embeds/email.php';

		return ob_get_clean();
	}

	/**
	 * Get default attribute.
	 */
	public function get_default( $attr ) {
		return newsletterglue_get_theme_option( $attr );
	}
}

return new NGL_Render_Post_Embeds();
