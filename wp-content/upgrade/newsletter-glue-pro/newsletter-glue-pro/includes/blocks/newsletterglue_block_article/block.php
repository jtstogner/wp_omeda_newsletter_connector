<?php
/**
 * Gutenberg.
 */

if ( ! class_exists( 'NGL_Abstract_Block', false ) ) {
	include_once NGL_PLUGIN_DIR . 'includes/abstract-block.php';
}

class NGL_Block_Article extends NGL_Abstract_Block {

	public $id = 'newsletterglue_block_article';

	public $asset_id;

	/**
	 * Construct.
	 */
	public function __construct() {

		$this->asset_id = str_replace( '_', '-', $this->id );

		if ( $this->use_block() === 'yes' ) {
			add_action( 'init', array( $this, 'register_block' ), 10 );
			add_action( 'newsletterglue_add_block_styles', array( $this, 'email_css' ) );

			// Ajax hooks.
			add_action( 'wp_ajax_newsletterglue_ajax_add_article', array( $this, 'embed_article' ) );
			add_action( 'wp_ajax_nopriv_newsletterglue_ajax_add_article', array( $this, 'embed_article' ) );

			add_action( 'wp_ajax_newsletterglue_ajax_update_labels', array( $this, 'update_labels' ) );
			add_action( 'wp_ajax_nopriv_newsletterglue_ajax_update_labels', array( $this, 'update_labels' ) );

			add_action( 'wp_ajax_newsletterglue_ajax_update_excerpt', array( $this, 'update_excerpt' ) );
			add_action( 'wp_ajax_nopriv_newsletterglue_ajax_update_excerpt', array( $this, 'update_excerpt' ) );

			add_action( 'wp_ajax_newsletterglue_ajax_update_title', array( $this, 'update_title' ) );
			add_action( 'wp_ajax_nopriv_newsletterglue_ajax_update_title', array( $this, 'update_title' ) );

			add_action( 'wp_ajax_newsletterglue_save_article_image', array( $this, 'save_article_image' ) );
			add_action( 'wp_ajax_nopriv_newsletterglue_save_article_image', array( $this, 'save_article_image' ) );

			add_action( 'wp_ajax_newsletterglue_ajax_search_articles', array( $this, 'search_articles' ) );
			add_action( 'wp_ajax_nopriv_newsletterglue_ajax_search_articles', array( $this, 'search_articles' ) );

			add_action( 'wp_ajax_newsletterglue_ajax_remove_article', array( $this, 'remove_article' ) );
			add_action( 'wp_ajax_nopriv_newsletterglue_ajax_remove_article', array( $this, 'remove_article' ) );

			add_action( 'wp_ajax_newsletterglue_ajax_order_articles', array( $this, 'order_articles' ) );
			add_action( 'wp_ajax_nopriv_newsletterglue_ajax_order_articles', array( $this, 'order_articles' ) );

			add_action( 'wp_ajax_newsletterglue_ajax_clear_cache', array( $this, 'clear_cache' ) );
			add_action( 'wp_ajax_nopriv_newsletterglue_ajax_clear_cache', array( $this, 'clear_cache' ) );

			add_action( 'wp_ajax_newsletterglue_ajax_update_url', array( $this, 'update_url' ) );
			add_action( 'wp_ajax_nopriv_newsletterglue_ajax_update_url', array( $this, 'update_url' ) );

			add_filter( 'newsletterglue_article_embed_content', array( $this, 'remove_div' ), 50, 2 );
		}

	}

	/**
	 * Demo URL.
	 */
	public function get_demo_url() {
		return 'https://www.youtube.com/embed/dqfwzZbGp5U?autoplay=1&modestbranding=1&autohide=1&showinfo=0&controls=0';
	}

	/**
	 * Block icon.
	 */
	public function get_icon_svg() {
		return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 40.625" class="ngl-block-svg-icon">
			<path d="M7.813,34.625H1.563A1.562,1.562,0,0,0,0,36.188v6.25A1.563,1.563,0,0,0,1.562,44h6.25a1.563,1.563,0,0,0,1.563-1.562v-6.25A1.562,1.562,0,0,0,7.813,34.625Zm0-31.25H1.563A1.563,1.563,0,0,0,0,4.938v6.25A1.562,1.562,0,0,0,1.562,12.75h6.25a1.562,1.562,0,0,0,1.563-1.562V4.938A1.563,1.563,0,0,0,7.813,3.375ZM7.813,19H1.563A1.563,1.563,0,0,0,0,20.563v6.25a1.562,1.562,0,0,0,1.562,1.563h6.25a1.562,1.562,0,0,0,1.563-1.562v-6.25A1.563,1.563,0,0,0,7.813,19ZM48.438,36.188H17.188a1.563,1.563,0,0,0-1.562,1.563v3.125a1.563,1.563,0,0,0,1.563,1.563h31.25A1.563,1.563,0,0,0,50,40.875V37.75A1.563,1.563,0,0,0,48.438,36.188Zm0-31.25H17.188A1.562,1.562,0,0,0,15.625,6.5V9.625a1.562,1.562,0,0,0,1.563,1.563h31.25A1.563,1.563,0,0,0,50,9.625V6.5A1.563,1.563,0,0,0,48.438,4.938Zm0,15.625H17.188a1.562,1.562,0,0,0-1.562,1.562V25.25a1.562,1.562,0,0,0,1.563,1.563h31.25A1.562,1.562,0,0,0,50,25.25V22.125A1.563,1.563,0,0,0,48.438,20.563Z" transform="translate(0 -3.375)"/>
		</svg>';
	}

	/**
	 * Block label.
	 */
	public function get_label() {
		return __( 'Post embeds', 'newsletter-glue' );
	}

	/**
	 * Block label.
	 */
	public function get_description() {
		return __( 'Bulk embed articles and customise their layout.', 'newsletter-glue' );
	}

	/**
	 * Get defaults.
	 */
	public function get_defaults() {

		return array(
			'show_in_blog' 	=> true,
			'show_in_email' => true,
		);

	}

	/**
	 * Register the block.
	 */
	public function register_block() {

		$defaults = get_option( $this->id );

		if ( ! $defaults ) {
			$defaults = array(
				'show_in_blog'	=> true,
				'show_in_email'	=> true,
			);
		}

		$js_dir    	= NGL_PLUGIN_URL . 'includes/blocks/' . $this->id . '/js/';
		$css_dir   	= NGL_PLUGIN_URL . 'includes/blocks/' . $this->id . '/css/';

		$suffix  = '';

		$defaults[ 'name' ]			= __( 'NG: Post embeds', 'newsletter-glue' );
		$defaults[ 'description' ] 	= __( 'Bulk embed articles and customise their layout.', 'newsletter-glue' );

		// Post dates.
		$formats = $this->get_date_formats();
		$date_formats = array();
		foreach( $formats as $format ) {
			$date_formats[] = array( 'value' => $format, 'label' => gmdate( $format, current_time( 'timestamp' ) ) );
		}
		$defaults[ 'date_formats' ] = $date_formats;

		wp_register_script( $this->asset_id, $js_dir . 'block' . $suffix . '.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ), time() );
		wp_localize_script( $this->asset_id, $this->id, $defaults );

		wp_register_style( $this->asset_id . '-style', $css_dir . 'block-ui' . $suffix . '.css', array(), time() );

		register_block_type( 'newsletterglue/article', array(
			'editor_script'   => $this->asset_id,
			'editor_style'    => $this->asset_id . '-style',
			'render_callback' => array( $this, 'render_block' ),
			'attributes'	  => array(
				'show_in_blog' => array(
					'type' 		=> 'boolean',
					'default' 	=> $defaults[ 'show_in_blog' ],
				),
				'show_in_email' => array(
					'type' 		=> 'boolean',
					'default' 	=> $defaults[ 'show_in_email' ],
				),
				'block_id'		=> array(
					'type'		=> 'string',
				),
				'scope'			=> array(
					'type'		=> 'string',
				),
				'border_color'	=> array(
					'type'		=> 'string',
				),
				'background_color'	=> array(
					'type'		=> 'string',
				),
				'text_color'	=> array(
					'type'		=> 'string',
				),
				'link_color'	=> array(
					'type'		=> 'string',
				),
				'border_radius'	=> array(
					'type'		=> 'number',
					'default'	=> 0,
				),
				'border_size'	=> array(
					'type'		=> 'number',
					'default'	=> 0,
				),
				'font_size_title' => array(
					'type'		=> 'number',
					'default'	=> 18,
				),
				'font_size_text' => array(
					'type'		=> 'number',
					'default'	=> 14,
				),
				'border_style'	=> array(
					'type'		=> 'string',
					'default'	=> 'solid',
				),
				'show_image'	=> array(
					'type'		=> 'boolean',
					'default'	=> true,
				),
				'show_date'		=> array(
					'type'		=> 'boolean',
					'default'	=> true,
				),
				'show_labels'	=> array(
					'type'		=> 'boolean',
					'default'	=> true,
				),
				'show_excerpt'	=> array(
					'type'		=> 'boolean',
					'default'	=> true,
				),
				'read_more_text' => array(
					'type' 		=> 'string',
					'default'   => '',
				),
				'show_more_link'	=> array(
					'type'		=> 'boolean',
					'default'	=> false,
				),
				'image_radius'	=> array(
					'type'		=> 'number',
					'default'	=> 0,
				),
				'date_format'	=> array(
					'type'		=> 'string',
				),
				'new_window'	=> array(
					'type'		=> 'boolean',
					'default'	=> false,
				),
				'nofollow'		=> array(
					'type'		=> 'boolean',
					'default'	=> false,
				),
				'image_position'	=> array(
					'type'		=> 'string',
					'default'	=> 'left',
				),
				'table_ratio'	=> array(
					'type'		=> 'string',
					'default'	=> 'full',
				),
				'top_margin'	=> array(
					'type'		=> 'string',
					'default'	=> '0px',
				),
				'bottom_margin'	=> array(
					'type'		=> 'string',
					'default'	=> '0px',
				),
			),
		) );

	}

	/**
	 * Render the block.
	 */
	public function render_block( $attributes, $content ) {

		ob_start();

		$defaults = get_option( $this->id );

		if ( ! $defaults ) {
			$defaults = array(
				'show_in_blog'	=> true,
				'show_in_email'	=> true,
			);
		}

		$show_in_blog  = isset( $attributes[ 'show_in_blog' ] ) ? $attributes[ 'show_in_blog' ] : $defaults[ 'show_in_blog' ];
		$show_in_email = isset( $attributes[ 'show_in_email' ] ) ? $attributes[ 'show_in_email' ] : $defaults[ 'show_in_email' ];

		// Hidden from blog.
		if ( ! defined( 'NGL_IN_EMAIL' ) && ! $show_in_blog ) {
			if ( ! defined( 'REST_REQUEST' ) ) {
				echo '';
				return ob_get_clean();
			}
		}

		// Hidden from email.
		if ( defined( 'NGL_IN_EMAIL' ) && ! $show_in_email ) {
			if ( ! defined( 'REST_REQUEST' ) ) {
				echo '';
				return ob_get_clean();
			}
		}

		$block_id 			= isset( $attributes[ 'block_id' ] ) ? str_replace( '-', '', $attributes[ 'block_id' ] ) : '';
		$table_ratio 		= isset( $attributes[ 'table_ratio' ] ) ? $attributes[ 'table_ratio' ] : 'full';
		$date_format    	= isset( $attributes[ 'date_format' ] ) ? $attributes[ 'date_format' ] : $this->get_default_date_format();
		$image_position    	= isset( $attributes[ 'image_position' ] ) ? $attributes[ 'image_position' ] : 'left';
		$show_labels   		= isset( $attributes[ 'show_labels' ] ) ? $attributes[ 'show_labels' ] : '';
		$show_excerpt   	= isset( $attributes[ 'show_excerpt' ] ) ? $attributes[ 'show_excerpt' ] : '';
		$show_more_link   	= isset( $attributes[ 'show_more_link' ] ) ? $attributes[ 'show_more_link' ] : '';
		$read_more_text   	= ! empty( $attributes[ 'read_more_text' ] ) ? $attributes[ 'read_more_text' ] : __( 'Read more.', 'newsletter-glue' );
		$show_date   		= isset( $attributes[ 'show_date' ] ) ? $attributes[ 'show_date' ] : '';
		$show_image   		= isset( $attributes[ 'show_image' ] ) ? $attributes[ 'show_image' ] : '';
		$image_radius   	= isset( $attributes[ 'image_radius' ] ) ? $attributes[ 'image_radius' ] : 0;
		$border_radius   	= isset( $attributes[ 'border_radius' ] ) ? $attributes[ 'border_radius' ] : 0;
		$border_size   		= isset( $attributes[ 'border_size' ] ) ? $attributes[ 'border_size' ] : '';
		$border_style   	= isset( $attributes[ 'border_style' ] ) ? $attributes[ 'border_style' ] : 'solid';
		$border_color   	= isset( $attributes[ 'border_color' ] ) ? $attributes[ 'border_color' ] : 'transparent';
		$background_color   = isset( $attributes[ 'background_color' ] ) ? $attributes[ 'background_color' ] : 'transparent';
		$text_color   		= isset( $attributes[ 'text_color' ] ) ? $attributes[ 'text_color' ] : newsletterglue_get_theme_option( 'p_colour' );
		$link_color   		= isset( $attributes[ 'link_color' ] ) ? $attributes[ 'link_color' ] : newsletterglue_get_theme_option( 'a_colour' );
		$font_size_title   	= ! empty( $attributes[ 'font_size_title' ] ) ? $attributes[ 'font_size_title' ] : 18;
		$font_size_text   	= ! empty( $attributes[ 'font_size_text' ] ) ? $attributes[ 'font_size_text' ] : 14;
		$new_window   		= ! empty( $attributes[ 'new_window' ] ) ? '_blank' : '_self';
		$nofollow   		= ! empty( $attributes[ 'nofollow' ] ) ? 'nofollow' : '';
		$top_margin   		= isset( $attributes[ 'top_margin' ] ) ? $attributes[ 'top_margin' ] : '';
		$bottom_margin   	= isset( $attributes[ 'bottom_margin' ] ) ? $attributes[ 'bottom_margin' ] : '';

		if ( $text_color ) {
			$text_color = "color: $text_color;";
		}

		if ( $link_color ) {
			$link_color = "color: $link_color !important;";
		}

		if ( $border_color == 'transparent' && $border_size ) {
			$border_color = '#ddd';
		}

		if ( ! $border_size && $border_radius && ( $border_color == 'transparent' ) ) {
			if ( $background_color == 'transparent' ) {
				$border_size = 1;
				$border_color = '#ddd';
			}
		}

		if ( $border_size || $border_radius ) {
			$padding = '20px';
		} else {
			$padding = '0px';
		}

		delete_option( 'ngl_articles_' );

		$articles = get_option( 'ngl_articles_' . $block_id );

		include( NGL_PLUGIN_DIR . 'includes/blocks/' . $this->id . '/templates/embed.php' );

		$content = ob_get_clean();
		
		if ( defined( 'NGL_IN_EMAIL' ) && $content ) {
			$content = $this->tableize( $content, $attributes );
		}

		return $content;
	}

	/**
	 * CSS.
	 */
	public function email_css() {
		?>
.ngl-articles {
	padding: 0;
	margin: 0;
}

.ngl-article img {
	display: block;
	overflow: hidden;
}

.ngl-article {
	margin: 0;
	padding: 0 !important;
	color: <?php echo esc_attr( newsletterglue_get_theme_option( 'p_colour' ) ); ?>;
}

.ngl-article-title {
	margin: 0 0 8px;
	line-height: 150%;
}

.ngl-article-title a {
	text-decoration: none !important;
}

.ngl-article-title span {
	font-size: inherit !important;
	line-height: 120%;
}

.ngl-article-excerpt {
	line-height: 150%;
}

.ngl-article-excerpt * {
	font-size: inherit !important;
}

.ngl-article-featured {
	margin: 0 0 14px;
}

.ngl-table-article .ngl-article-featured {
	margin: 0;
}

.ngl-article-featured a {
	display: block;
}

.ngl-article-featured img {
	margin: 0 !important;
}

.ngl-article-date {
	margin: 8px 0 0;
	font-size: 0.95em;
	opacity: 0.7;
}

.ngl-articles .components-placeholder.components-placeholder {
	min-height: 100px;
}

.ngl-articles input[type=text] {
	padding: 6px 8px;
    box-shadow: 0 0 0 transparent;
    transition: box-shadow 0.1s linear;
    border-radius: 2px;
    border: 1px solid #757575;
    margin: 0 8px 0 0;
    flex: 1 1 auto;
	font-size: 13px;
    line-height: normal;
}

.ngl-article-labels {
	display: block;
	margin: 0 0 6px;
	font-size: 0.95em;
	opacity: 0.8;
}

.ngl-articles-add {
	width: 100%;
}

#template_inner td table.ngl-articles-table {
	border: none;
}

#template_inner td table.ngl-articles-table th,
#template_inner td table.ngl-articles-table td {
	border: none;
	padding: 0;
}

.ngl-articles table {
	border: none;
}

.ngl-articles-full img {
	width: 100%;
	height: auto;
}

.ngl-article-left { display: inline-block; width: 49.5%; vertical-align: top; box-sizing: border-box !important; }
.ngl-article-right { display: inline-block; width: 49.5%; vertical-align: top; padding-left: 20px; box-sizing: border-box !important; }

.ngl-articles-30_70 .ngl-article-left { display: inline-block; width: 30%; vertical-align: top; }
.ngl-articles-30_70 .ngl-article-right { display: inline-block; width: 69%; vertical-align: top; }

.ngl-articles-70_30 .ngl-article-left { display: inline-block; width: 69%; vertical-align: top; }
.ngl-articles-70_30 .ngl-article-right { display: inline-block; width: 30%; vertical-align: top; }

.ngl-article-right .ngl-article-featured { margin: 0; }
.ngl-article-left .ngl-article-featured { margin: 0; }

.ngl-article-mobile {
	display: none !important;
	overflow: hidden;
	mso-hide: all;
	margin: 0;
	font-size: 0;
	max-height: 0;
}

.ngl-article-mobile * {
	display: none !important;
	overflow: hidden;
	mso-hide: all;
	max-height: 0;
	font-size: 0;
}

@media only screen and (max-width:642px) {

	.ngl-article-mobile,
	.ngl-article-mobile * {
		display: block !important;
		max-height: 100% !important;
		font-size: <?php echo esc_attr( newsletterglue_get_theme_option( 'mobile_p_size' ) ); ?>px !important;
	}

	.ngl-article-mobile div.ngl-article-excerpt * {
		display: inline !important;
	}

	.ngl-article-img-full,
	.ngl-article-img-left,
	.ngl-article-img-right {
		display: none !important;
		overflow: hidden;
		mso-hide: all;
		margin: 0;
		font-size: 0;
		max-height: 0;
	}

}

	<?php
	}

	/**
	 * Get date formats.
	 */
	public function get_date_formats() {
		return array( 'j M Y', 'l, j M Y', 'F j, Y', 'Y-m-d', 'm/d/Y', 'd/m/Y' );
	}

	/**
	 * Get default date format.
	 */
	public function get_default_date_format() {
		$formats = $this->get_date_formats();

		return $formats[ 0 ];
	}

	/**
	 * Remove this block div from article embeds.
	 */
	public function remove_div( $content, $post_id ) {
		$content = newsletterglue_remove_div( $content, 'ngl-articles' );

		return $content;
	}

	/**
	 * Exerpt length by words.
	 */
	public function excerpt_words() {
		return apply_filters( 'newsletterglue_post_embed_words', 30 );
	}

	/**
	 * Update title.
	 */
	public function update_title() {

		check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

		$post_id = isset( $_POST[ 'post_id' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'post_id' ] ) ) : '';
		$title   = isset( $_POST[ 'title' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'title' ] ) ) : '';

		$custom_data = get_option( 'newsletterglue_article_custom_data' );

		if ( empty( $custom_data ) ) {
			$custom_data = array();
		}

		$post_id = strtok( $post_id, '?' );

		$custom_data[ $post_id ][ 'title' ] = $title;

		update_option( 'newsletterglue_article_custom_data', $custom_data );

		wp_die();

	}

	/**
	 * Update labels.
	 */
	public function update_labels() {

		check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

		$post_id = isset( $_POST[ 'post_id' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'post_id' ] ) ) : '';
		$labels  = isset( $_POST[ 'labels' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'labels' ] ) ) : '';

		$custom_data = get_option( 'newsletterglue_article_custom_data' );

		$post_id = strtok( $post_id, '?' );

		if ( empty( $custom_data ) ) {
			$custom_data = array();
		}

		$custom_data[ $post_id ][ 'labels' ] = $labels;

		update_option( 'newsletterglue_article_custom_data', $custom_data );

		wp_die();

	}

	/**
	 * Update excerpt.
	 */
	public function update_excerpt() {

		check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

		$post_id = isset( $_POST[ 'post_id' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'post_id' ] ) ) : '';
		$excerpt = isset( $_POST[ 'excerpt' ] ) ? wp_kses_post( $_POST[ 'excerpt' ] ) : '';

		$custom_data = get_option( 'newsletterglue_article_custom_data' );

		if ( empty( $custom_data ) ) {
			$custom_data = array();
		}

		$post_id = strtok( $post_id, '?' );

		$custom_data[ $post_id ][ 'excerpt' ] = $excerpt;

		update_option( 'newsletterglue_article_custom_data', $custom_data );

		wp_die();

	}

	/**
	 * Display labels.
	 */
	public function get_labels( $post_id, $url = '' ) {

		$custom_data = get_option( 'newsletterglue_article_custom_data' );

		$post_id = strtok( $post_id, '?' );

		if ( ! empty( $custom_data ) && isset( $custom_data[ $post_id ][ 'labels' ] ) ) {
			$labels = stripslashes_deep( $custom_data[ $post_id ][ 'labels' ] );
			if ( ! empty( $labels ) ) {
				return $labels;
			} else {
				if ( ! defined( 'NGL_IN_EMAIL' ) && ( is_admin() || defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
					return $this->get_domain( $url );
				}
			}
		}

		if ( ! defined( 'NGL_IN_EMAIL' ) && ( is_admin() || defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return $this->get_domain( $url );
		}

		return $this->get_domain( $url );

	}

	/**
	 * Get domain only.
	 */
	public function get_domain( $url = '' ) {
		$parse = wp_parse_url( $url );
		if ( isset( $parse[ 'host' ] ) ) {
			return str_replace( 'www.', '', $parse['host'] );
		} else {
			return __( 'Add label', 'newsletter-glue' );
		}
	}

	/**
	 * Set custom image.
	 */
	public function set_custom_image( $post_id, $custom_image  ) {

		$custom_data = get_option( 'newsletterglue_article_custom_data' );

		if ( empty( $custom_data ) ) {
			$custom_data = array();
		}

		$post_id = strtok( $post_id, '?' );

		$custom_data[ $post_id ][ 'custom_image' ] = $custom_image;

		update_option( 'newsletterglue_article_custom_data', $custom_data );

	}

	/**
	 * Get custom image.
	 */
	public function get_custom_image( $post_id ) {

		$custom_data = get_option( 'newsletterglue_article_custom_data' );

		$post_id = strtok( $post_id, '?' );

		if ( ! empty( $custom_data ) && isset( $custom_data[ $post_id ][ 'custom_image' ] ) ) {
			return esc_attr( $custom_data[ $post_id ][ 'custom_image' ] );
		}

		return false;
	}

	/**
	 * Remove custom image.
	 */
	public function remove_custom_image( $post_id ) {

		$custom_data = get_option( 'newsletterglue_article_custom_data' );

		$post_id = strtok( $post_id, '?' );

		if ( empty( $custom_data ) ) {
			return;
		}

		unset( $custom_data[ $post_id ][ 'custom_image' ] );

		update_option( 'newsletterglue_article_custom_data', $custom_data );

	}

	/**
	 * AJAX save article image.
	 */
	public function save_article_image() {

		check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

		if ( ! current_user_can( 'manage_newsletterglue' ) ) {
			wp_die( -1 );
		}

		$key = isset( $_POST[ 'key' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'key' ] ) ) : '';
		$ids = isset( $_POST[ 'ids' ] ) ? absint( $_POST[ 'ids' ] ) : '';

		if ( $ids ) {

			$url = wp_get_attachment_url( $ids );

			// No URL.
			if ( ! $url ) {
				wp_send_json_error();
			}

			$data = array(
				'id'		=> $ids,
				'url'		=> $url,
				'filename'	=> basename( $url ),
			);

			$this->set_custom_image( $key, $url );

			wp_send_json_success( $data );

		} else {	
			$this->remove_custom_image( $key );
		}

		wp_die();

	}

	/**
	 * Display title.
	 */
	public function display_title( $post_id, $post ) {

		$custom_data = get_option( 'newsletterglue_article_custom_data' );

		$post_id = strtok( $post_id, '?' );

		if ( ! empty( $custom_data ) && isset( $custom_data[ $post_id ][ 'title' ] ) ) {
			return stripslashes_deep( $custom_data[ $post_id ][ 'title' ] );
		} else {
			return ! empty( $post->title ) ? $post->title : get_the_title( $post );
		}

	}

	/**
	 * Display excerpt.
	 */
	public function display_excerpt( $post_id, $content ) {

		$custom_data = get_option( 'newsletterglue_article_custom_data' );

		$post_id = strtok( $post_id, '?' );

		if ( ! empty( $custom_data ) && isset( $custom_data[ $post_id ][ 'excerpt' ] ) ) {
			$excerpt = stripslashes_deep( $custom_data[ $post_id ][ 'excerpt' ] );
			$excerpt = wp_kses( $excerpt, newsletterglue_allowed_tags_for_excerpt() );

		} else {
			$thepost = get_post( $post_id );
			$content = ! empty( $thepost->post_excerpt ) ? $thepost->post_excerpt : $content;
			$content = apply_filters( 'newsletterglue_default_post_embed_excerpt', $content, $post_id );

			if ( apply_filters( 'newsletterglue_post_embed_trim_words', true ) ) {
				$excerpt = wp_trim_words( $content, $this->excerpt_words() );
				$excerpt = wp_kses( $excerpt, newsletterglue_allowed_tags_for_excerpt() );
			} else {
				$excerpt = trim( $content );
				$excerpt = wp_kses( $excerpt, newsletterglue_allowed_tags_for_excerpt() );
			}
		}

		if ( ! empty( $excerpt ) ) {
			$output = new simple_html_dom();
			$output->load( $excerpt, true, false );
			$replace = 'a.ngl-article-read-more';
			foreach( $output->find( $replace ) as $key => $element ) {
				if ( $element->innertext ) {
					$custom_data[ $post_id ][ 'learn_more' ] = $element->innertext;
					update_option( 'newsletterglue_article_custom_data', $custom_data );
				}
				$element->outertext = '';
			}
			$output->save();
			$final = (string) $output;
			$final = str_replace( '&nbsp;', '', $final );
			$final = trim( $final );
			return $final;
		}
	}

	/**
	 * Display learn more text.
	 */
	public function display_learn_more( $post_id = 0, $read_more_text = '' ) {

		$custom_data = get_option( 'newsletterglue_article_custom_data' );

		$post_id = strtok( $post_id, '?' );

		if ( ! empty( $custom_data ) && isset( $custom_data[ $post_id ][ 'learn_more' ] ) ) {
			$text = stripslashes_deep( $custom_data[ $post_id ][ 'learn_more' ] );
			$text = wp_kses( $text, newsletterglue_allowed_tags_for_excerpt() );
		} else {
			$read_more_text = isset( $read_more_text ) ? $read_more_text : __( 'Read more.', 'newsletter-glue' );
			$text = apply_filters( 'newsletterglue_article_read_more_text', $read_more_text );
		}

		if ( ! empty( $text ) ) {
			return $text;
		}
	}

	/**
	 * Search articles.
	 */
	public function search_articles() {

		check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

		$term = isset( $_POST[ 'term' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'term' ] ) ) : '';

		if ( ! $term || mb_strlen( $term ) < 3 ) {
			wp_die( -1 );
		}

		add_filter( 'posts_where', array( $this, 'post_title_filter' ), 10, 2 );

		$results = new WP_Query( array(
			'post_type'      	=> array_keys( get_post_types( array( 'public' => true ) ) ),
			'post_status'    	=> 'publish',
			'posts_per_page' 	=> 100,
			'ngl_post_title_s'  => $term, // search post title only
		) );

		remove_filter( 'posts_where', array( $this, 'post_title_filter' ), 10, 2 );

		$html = '';

		if ( ! empty( $results->posts ) ) {
			foreach ( $results->posts as $result ) {
				$html .= '<li><a href="#" data-post-id="' . $result->ID . '" data-permalink="' . esc_url( get_permalink( $result->ID ) ) . '">' . $result->post_title . '</a></li>';
			}
			wp_send_json( array( 'html' => $html ) );
		} else {
			wp_send_json( array( 'no_results' => true ) );
		}

		wp_die();

	}

	/**
	 * Add search to post titles only.
	 */
	public function post_title_filter( $where, $wp_query ) {
		global $wpdb;
		if ( $term = $wp_query->get( 'ngl_post_title_s' ) ) {
			$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . $wpdb->esc_like( $term ) . '%\'';
		}
		return $where;
	}

	/**
	 * Remove article.
	 */
	public function remove_article() {

		check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

		$key 		= isset( $_POST[ 'key' ] ) ? absint( $_POST[ 'key' ] ) : '';
		$block_id 	= isset( $_POST[ 'block_id' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'block_id' ] ) ) : '';

		$articles = get_option( 'ngl_articles_' . $block_id );

		if ( ! empty( $articles ) && isset( $articles[ $key ] ) ) {
			unset( $articles[ $key ] );
			if ( ! empty( $articles ) ) {
				update_option( 'ngl_articles_' . $block_id, $articles );
			} else {
				delete_option( 'ngl_articles_' . $block_id );
			}
		}

		wp_die();

	}

	/**
	 * Order article.
	 */
	public function order_articles() {

		check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

		$block_id 	= isset( $_POST[ 'block_id' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'block_id' ] ) ) : '';
		$keys 		= isset( $_POST[ 'keys' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'keys' ] ) ) : '';
		$values 	= isset( $_POST[ 'values' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'values' ] ) ) : '';
		$is_dup		= isset( $_POST[ 'is_dup' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'is_dup' ] ) ) : 0;

		if ( $keys && $values ) {
			$updated = array();
			if ( ! $is_dup ) {
				$articles = get_option( 'ngl_articles_' . $block_id );
			} else {
				$articles = get_option( 'ngl_articles_' . $is_dup );
			}
			$order = array_combine( explode( ',', $keys ), explode( ',', $values ) );
			foreach( $order as $key => $value ) {
				if ( ! empty( $articles ) ) {
					foreach( $articles as $index => $data ) {
						if ( $data[ 'post_id' ] == $value ) {
							$updated[ $key ] = $data;
						}
					}
				}
			}
			update_option( 'ngl_articles_' . $block_id, $updated );
		}

	}

	/**
	 * Get a remote URL.
	 */
	public function get_remote_url( $url ) {

		$url  = untrailingslashit( $url );

		$html = get_transient( 'ngl_' . md5( $url ) );

		if ( false === $html ) {
			$html = wp_remote_retrieve_body( wp_remote_get( $url ) ); // phpcs:ignore
			if ( $html ) {
				set_transient( 'ngl_' . md5( $url ), $html, 2628000 );
			} else {
				$html = file_get_contents( $url ); // phpcs:ignore
				if ( $html ) {
					set_transient( 'ngl_' . md5( $url ), $html, 2628000 );
				} else {
					$data = new stdclass;
					return $data;
				}
			}
		}

		$data = new stdclass;

		$data->is_remote 	= true;
		$data->favicon 		= 'https://www.google.com/s2/favicons?sz=32&domain_url=' . $url; 
		$data->ID			= $url;

		$doc = new DOMDocument();
		libxml_use_internal_errors( true );
		$doc->loadHTML( mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8') );
		libxml_clear_errors();
		$nodes = $doc->getElementsByTagName( 'title' );
		$data->title = htmlspecialchars_decode(mb_convert_encoding(htmlentities( $nodes->item(0)->nodeValue, ENT_COMPAT, 'utf-8', false), 'UTF-8', mb_list_encodings()));

		$metas = $doc->getElementsByTagName( 'meta' );
		$images = array();
		for ( $i = 0; $i < $metas->length; $i++ ) {
			$meta = $metas->item( $i );
			if ( $meta->getAttribute( 'name' ) == 'description' ) {
				$content = htmlspecialchars_decode(mb_convert_encoding(htmlentities( $meta->getAttribute('content'), ENT_COMPAT, 'utf-8', false), 'UTF-8', mb_list_encodings()));
				if ( ! empty( $content ) ) {
					$data->post_content = $content;
				}
			}
			if ( $meta->getAttribute( 'property' ) == 'og:description' ) { 
				$content = htmlspecialchars_decode(mb_convert_encoding(htmlentities( $meta->getAttribute('content'), ENT_COMPAT, 'utf-8', false), 'UTF-8', mb_list_encodings()));
				if ( ! empty( $content ) ) {
					$data->post_content = $content;
				}
			}
			if ( $meta->getAttribute( 'property' ) == 'og:title' ) { 
				$title = htmlspecialchars_decode(mb_convert_encoding(htmlentities( $meta->getAttribute('content'), ENT_COMPAT, 'utf-8', false), 'UTF-8', mb_list_encodings()));
				if ( ! empty( $title ) ) {
					$data->title = $title;
				}
			}
			if ( $meta->getAttribute( 'property' ) == 'og:image' ) {
				$images[] = $meta->getAttribute('content');
			}
		}
		if ( empty( $data->post_content ) ) {
			$data->post_content = __( 'No description found.', 'newsletter-glue' );
		}
		if ( ! empty( $images ) ) {
			foreach( $images as $image ) {
				if ( ! empty( $data->image_url ) ) continue;
				$data->image_url = $image;
			}
		}
		if ( empty( $data->image_url ) ) {
			$data->image_url = $this->default_image_url();
		}

		return $data;

	}

	/**
	 * Get featured image URL.
	 */
	public function get_featured( $thearticle ) {
		return has_post_thumbnail( $thearticle ) ? wp_get_attachment_url( get_post_thumbnail_id( $thearticle->ID ), 'full' ) : $this->default_image_url();
	}

	/**
	 * Get default image URL.
	 */
	public function default_image_url() {
		return NGL_PLUGIN_URL . 'includes/blocks/' . $this->id . '/img/placeholder.png';
	}

	/**
	 * Get permalink.
	 */
	public function get_permalink( $thearticle ) {
		return ! empty( $thearticle->is_remote ) ? esc_url( $thearticle->ID ) : esc_url( get_permalink( $thearticle->ID ) );
	}

	/**
	 * Get favicon.
	 */
	public function get_favicon( $thearticle ) {
		
		if ( ! empty( $thearticle->favicon ) ) {
			return $thearticle->favicon;
		}

		$remote_addr = ! empty( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : ''; // phpcs:ignore

		if ( $remote_addr && ( $remote_addr == '127.0.0.1' || $remote_addr == '::1' ) ) {
			$url = 'https://newsletterglue.com';
		} else {
			$url = home_url();
		}

		$favicon = 'https://www.google.com/s2/favicons?sz=32&domain_url=' . $url;

		return $favicon;
	}

	/**
	 * AJAX update article.
	 */
	public function update_url() {

		check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

		$block_id 		= isset( $_POST[ 'block_id' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'block_id' ] ) ) : '';
		$key 	        = isset( $_POST[ 'key' ] ) ? absint( $_POST[ 'key' ] ) : '';
		$url 	        = isset( $_POST[ 'url' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'url' ] ) ) : '';
		$date_format 	= isset( $_POST[ 'date_format' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'date_format' ] ) ) : '';

		$url = untrailingslashit( $url );

		if ( ! $key || ! $block_id ) {
			wp_die();
		}

		if ( ! preg_match( "~^(?:f|ht)tps?://~i", $url ) ) {
			$url = 'https://' . $url;
		}

		if ( empty( $url ) || ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
			$error = __( 'Invalid URL.', 'newsletter-glue' );
		}

		if ( ! empty( $error ) ) {
			wp_send_json_error( array( 'error' => $error ) );
		}

		// Try to find out if this is an internal post.
		$post_id  = url_to_postid( $url ); // phpcs:ignore
		if ( $post_id > 0 ) {
			$post = get_post( $post_id );
		} else {
			$post = null;
		}

		$articles = get_option( 'ngl_articles_' . $block_id );

		if ( empty( $post ) || empty( $post->ID ) ) {

			// External.
			$thearticle = $this->get_remote_url( $url );

			if ( empty( $thearticle->title ) ) {
				wp_send_json_error( array( 'error' => __( 'Invalid URL.', 'newsletter-glue' ) ) );
			}

		} else {

			// Local.
			$thearticle = $post;
		}

		// Update current key with new data.
		$embed = array(
			'post_id' 	=> $thearticle->ID,
			'favicon'   => $this->get_favicon( $thearticle ),
		);

		if ( ! empty( $thearticle->is_remote ) ) {
			foreach( $thearticle as $remote_key => $remote_value ) {
				$embed[ $remote_key ] = $remote_value;
			}
		}

		$articles[ $key ] = $embed;

		update_option( 'ngl_articles_' . $block_id, $articles );

		// Show refresh icon.
		if ( ! empty( $thearticle->is_remote ) ) {
			$refresh_icon    = '<a href="#" class="ngl-article-list-refresh"><svg stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>' . __( 'Refresh', 'newsletter-glue' ) . '</a>';
		} else {
			$refresh_icon	 = '';
		}

		$thecontent = apply_filters( 'newsletterglue_article_embed_content', apply_filters( 'the_content', $thearticle->post_content ), $thearticle->ID );

		$result = array(
			'key'				=> $key,
			'block_id'			=> $block_id,
			'post'				=> $thearticle,
			'post_id'			=> $thearticle->ID,
			'excerpt'			=> $this->display_excerpt( $thearticle->ID, $thecontent ),
			'title'				=> $this->display_title( $thearticle->ID, $thearticle ),
			'permalink'			=> $this->get_permalink( $thearticle ),
			'featured_image'	=> $this->get_image_url( $thearticle ),
			'labels'			=> $this->get_labels( $thearticle->ID, $this->get_permalink( $thearticle ) ),
			'embed'				=> $embed,
			'date'				=> ! empty( $thearticle->post_date ) ? date_i18n( $date_format, strtotime( $thearticle->post_date ) ) : '',
		);

		wp_send_json_success( $result );

	}

	/**
	 * AJAX embedding article.
	 */
	public function embed_article() {

		check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

		$block_id 		= isset( $_POST[ 'block_id' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'block_id' ] ) ) : '';
		$thepost 		= isset( $_POST[ 'thepost' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'thepost' ] ) ) : '';
		$date_format 	= isset( $_POST[ 'date_format' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'date_format' ] ) ) : '';
		$key 	        = isset( $_POST[ 'key' ] ) ? absint( $_POST[ 'key' ] ) : 1;

		if ( is_numeric( $thepost ) ) {
			$thearticle = get_post( $thepost );
		} else {
			$post_id 	= url_to_postid( $thepost ); // phpcs:ignore
			if ( $post_id > 0 ) {
				$thearticle	= get_post( $post_id );
			} else {
				$thearticle	= null;
			}
		}

		if ( empty( $thepost ) ) {
			wp_send_json( array( 'error' => __( 'Please search for a post or type some URL.', 'newsletter-glue' ) ) );
		}

		if ( ! isset( $thearticle->ID ) || empty( $thearticle->ID ) ) {
			$thepost = strpos( $thepost, 'http' ) !== 0 ? "https://$thepost" : $thepost;
			if ( filter_var( $thepost, FILTER_VALIDATE_URL ) ) {
				$thearticle = $this->get_remote_url( $thepost );
				if ( empty( $thearticle->title ) ) {
					wp_send_json( array( 'error' => __( 'Invalid URL.', 'newsletter-glue' ) ) );
				}
			} else {
				wp_send_json( array( 'error' => __( 'Invalid post.', 'newsletter-glue' ) ) );
			}
		}

		$articles = get_option( 'ngl_articles_' . $block_id );

		if ( ! empty( $articles ) ) {
			foreach( $articles as $article => $article_data ) {
				foreach( $article_data as $index => $value ) {
					if ( $index == 'post_id' && $value == $thearticle->ID ) {
						wp_send_json( array( 'error' => __( 'This post is already embedded.', 'newsletter-glue' ) ) );
					}
				}
			}
		} else {
			$articles = array();
		}

		$embed = array(
			'post_id' 	=> $thearticle->ID,
			'favicon'   => $this->get_favicon( $thearticle ),
		);

		if ( ! empty( $thearticle->is_remote ) ) {
			foreach( $thearticle as $remote_key => $remote_value ) {
				$embed[ $remote_key ] = $remote_value;
			}
		}

		$articles[ $key ] = $embed;

		update_option( 'ngl_articles_' . $block_id, $articles );

		if ( ! empty( $thearticle->is_remote ) ) {
			$refresh_icon    = '<a href="#" class="ngl-article-list-refresh"><svg stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>' . __( 'Refresh', 'newsletter-glue' ) . '</a>';
		} else {
			$refresh_icon	 = '';
		}

		$thecontent = apply_filters( 'newsletterglue_article_embed_content', apply_filters( 'the_content', $thearticle->post_content ), $thearticle->ID );

		$result = array(
			'key'				=> $key,
			'block_id'			=> $block_id,
			'thepost'			=> $thepost,
			'post'				=> $thearticle,
			'post_id'			=> $thearticle->ID,
			'excerpt'			=> $this->display_excerpt( $thearticle->ID, $thecontent ),
			'title'				=> $this->display_title( $thearticle->ID, $thearticle ),
			'permalink'			=> $this->get_permalink( $thearticle ),
			'date'				=> ! empty( $thearticle->post_date ) ? date_i18n( $date_format, strtotime( $thearticle->post_date ) ) : '',
			'labels'			=> $this->get_labels( $thearticle->ID, $this->get_permalink( $thearticle ) ),
			'featured_image'	=> $this->get_image_url( $thearticle ),
			'embed'				=> $embed,
			'success'			=> __( 'Add another post', 'newsletter-glue' ),
		);

		wp_send_json( $result );

	}

	/**
	 * Clear cache for external links.
	 */
	public function clear_cache() {

		check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

		$thepost 		= isset( $_POST[ 'thepost' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'thepost' ] ) ) : '';
		$block_id 		= isset( $_POST[ 'block_id' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'block_id' ] ) ) : '';
		$key 	        = isset( $_POST[ 'key' ] ) ? absint( $_POST[ 'key' ] ) : 1;

		if ( absint( $thepost ) > 0 ) {
			$thearticle = get_post( $thepost );
		} else {

			// Remove cache.
			delete_transient( 'ngl_' . md5( untrailingslashit( $thepost ) ) );

			$thearticle = $this->get_remote_url( $thepost );

		}

		$custom_data = get_option( 'newsletterglue_article_custom_data' );

		$post_id = untrailingslashit( $thepost );

		if ( ! empty( $custom_data ) && ! empty( $custom_data[ $post_id ] ) ) {
			unset( $custom_data[ $post_id ] );
			update_option( 'newsletterglue_article_custom_data', $custom_data );
		}

		$embed = array(
			'post_id' 	=> $thearticle->ID,
			'favicon'   => $this->get_favicon( $thearticle ),
		);

		if ( ! empty( $thearticle->is_remote ) ) {
			foreach( $thearticle as $remote_key => $remote_value ) {
				$embed[ $remote_key ] = $remote_value;
			}
		}

		$thecontent = apply_filters( 'newsletterglue_article_embed_content', apply_filters( 'the_content', $thearticle->post_content ), $thearticle->ID );

		// Generate html for item.
		$refresh_icon    = '<a href="#" class="ngl-article-list-refresh"><svg stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>' . __( 'Refresh', 'newsletter-glue' ) . '</a>';

		$result = array(
			'key'				=> $key,
			'block_id'			=> $block_id,
			'thepost'			=> $thepost,
			'post'				=> $thearticle,
			'post_id'			=> $thearticle->ID,
			'excerpt'			=> $this->display_excerpt( $thearticle->ID, $thecontent ),
			'title'				=> $this->display_title( $thearticle->ID, $thearticle ),
			'permalink'			=> $this->get_permalink( $thearticle ),
			'featured_image'	=> $this->get_image_url( $thearticle ),
			'labels'			=> $this->get_labels( $thearticle->ID, $this->get_permalink( $thearticle ) ),
			'embed'				=> $embed,
		);

		wp_send_json( $result );

	}

	/**
	 * Get image URL.
	 */
	public function get_image_url( $thearticle ) {

		if ( ! empty( $thearticle->is_remote ) ) {
			$fallback = $thearticle->image_url;
		} else {
			$fallback = $this->get_featured( $thearticle );
		}

		return $this->get_custom_image( $thearticle->ID ) ? esc_url( $this->get_custom_image( $thearticle->ID ) ) : $fallback;

	}

	/**
	 * Get image default URL.
	 */
	public function get_image_default( $thearticle ) {

		if ( ! empty( $thearticle->is_remote ) ) {
			$fallback = $thearticle->image_url;
		} else {
			$fallback = $this->get_featured( $thearticle );
		}

		return $fallback;

	}

	/**
	 * Tableize.
	 */
	public function tableize( $content, $attributes = array() ) {

		$output = new simple_html_dom();
		$output->load( $content, true, false );

		$table_ratio 		= isset( $attributes[ 'table_ratio' ] ) ? $attributes[ 'table_ratio' ] : 'full';
		$image_position 	= isset( $attributes[ 'image_position' ] ) ? $attributes[ 'image_position' ] : 'left';
		$text_color   		= isset( $attributes[ 'text_color' ] ) ? $attributes[ 'text_color' ] : newsletterglue_get_theme_option( 'p_colour' );
		$border_radius   	= isset( $attributes[ 'border_radius' ] ) ? $attributes[ 'border_radius' ] : 0;
		$border_size   		= isset( $attributes[ 'border_size' ] ) ? $attributes[ 'border_size' ] : '';

		if ( $text_color ) {
			$text_color = "color: $text_color; ";
		}

		$bgcolor = isset( $attributes[ 'background_color' ] ) ? $attributes[ 'background_color' ] : 'transparent';

		$width = 'auto';

		if ( $border_size || ( $bgcolor && $bgcolor != 'transparent' ) ) {
			$sub = 20;
			if ( $border_size && $border_size > 0 ) {
				$sub = $sub + $border_size;
			}
		} else {
			$sub = 0;
		}

		$img = '.ngl-article-featured img';
		foreach( $output->find( $img ) as $a => $b ) {
			$b->width = $sub ? 520 : 560;
			$b->style = $b->style . 'display: block; max-width: 100%; min-width: 50px; width: 100%;';
			$b->class = $b->class . ' postembed-image';
		}

		// Left side.
		$replace = 'div.ngl-article.ngl-article-img-left > .ngl-article-left, div.ngl-article.ngl-article-img-right > .ngl-article-left';
		foreach( $output->find( $replace ) as $key => $element ) {
			$base_w = 560;
			if ( $table_ratio == '30_70' ) {
				$width = '30%';
				$img_size = ( 0.30 * $base_w ) - 10 - $sub;
			}
			if ( $table_ratio == '70_30' ) {
				$width = '70%';
				$img_size = ( 0.70 * $base_w ) - 10 - $sub;
			}
			if ( $table_ratio == '50_50' ) {
				$width = '50%';
				$img_size = ( 0.50 * $base_w ) - 10 - $sub;
			}
			$img = '.ngl-article-featured img';
			foreach( $output->find( $img ) as $a => $b ) {
				$b->width = $img_size;
				$b->{ 'data-sub' } = $sub;
				$b->{ 'data-pct' } = absint( $width );
				$b->{ 'data-img-size' } = $img_size;
				$b->style = $b->style . 'display: block; max-width: 100%; min-width: 50px; width: 100%;';
			}
			$td_width = ( ( absint( $width ) / 100 )* 560 ) - $sub - 10;
			$output->find( $replace, $key )->outertext = '<td width="' . $td_width . '" style="width: ' . $td_width . 'px; vertical-align: top; font-size: inherit !important;' . $text_color . '" valign="top">' . $element->innertext . '</td>';
		}

		// Right side.
		$replace = 'div.ngl-article.ngl-article-img-left > .ngl-article-right, div.ngl-article.ngl-article-img-right > .ngl-article-right';
		foreach( $output->find( $replace ) as $key => $element ) {
			$base_w = 560;
			if ( $table_ratio == '30_70' ) {
				$width = '70%';
				$img_size = ( 0.70 * $base_w ) - 10 - $sub;
			}
			if ( $table_ratio == '70_30' ) {
				$width = '30%';
				$img_size = ( 0.30 * $base_w ) - 10 - $sub;
			}
			if ( $table_ratio == '50_50' ) {
				$width = '50%';
				$img_size = ( 0.50 * $base_w ) - 10 - $sub;
			}
			$img = '.ngl-article-featured img';
			foreach( $output->find( $img ) as $a => $b ) {
				$b->width = $img_size;
				$b->{ 'data-sub' } = $sub;
				$b->{ 'data-pct' } = absint( $width );
				$b->{ 'data-img-size' } = $img_size;
				$b->style = $b->style . 'display: block; max-width: 100%; min-width: 50px; width: 100%;';
			}
			$td_width = ( ( absint( $width ) / 100 )* 560 ) - $sub - 10;
			$output->find( $replace, $key )->outertext = '<td width="' . $td_width . '" style="width: ' . $td_width . 'px;vertical-align: top;' . $text_color . '" valign="top">' . $element->innertext . '</td>';
		}

		// Left and Right article wrappers.
		$replace = 'div.ngl-article.ngl-article-img-left, div.ngl-article.ngl-article-img-right';
		foreach( $output->find( $replace ) as $key => $element ) {
			$output->find( $replace, $key )->innertext = '<table class="ngl-table-article" bgcolor="' . $bgcolor . '" border="0" width="100%" cellpadding="0" cellspacing="0" style="border: 0; font-size: inherit !important;border-radius: ' . $border_radius . 'px;"><tr>' . $element->innertext . '</tr></table>';
		}

		// Remove articles div.
		$replace = 'div.ngl-articles';
		foreach( $output->find( $replace ) as $key => $element ) {
			if ( strstr( $element->class, 'pure' ) ) {
				$style = 'pure';
			} else {
				$style = 'colored';
			}
			$element->outertext = '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="ngl-table-posts ngl-table-posts-' . $style . '"><tr><td style="' . $text_color .'">' . $element->innertext . '</td></tr></table>';
		}

		$output->save();

		return ( string ) $output;

	}

}

return new NGL_Block_Article;