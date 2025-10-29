<?php
/**
 * Gutenberg.
 */

if ( ! class_exists( 'NGL_Abstract_Block', false ) ) {
	include_once NGL_PLUGIN_DIR . 'includes/abstract-block.php';
}

class NGL_Block_Author extends NGL_Abstract_Block {

	public $id = 'newsletterglue_block_author';

	public $asset_id;

	/**
	 * Construct.
	 */
	public function __construct() {

		$this->asset_id = str_replace( '_', '-', $this->id );

		if ( $this->use_block() === 'yes' ) {
			add_action( 'init', array( $this, 'register_block' ), 10 );
			add_action( 'newsletterglue_add_block_styles', array( $this, 'email_css' ) );

			add_filter( 'newsletterglue_article_embed_content', array( $this, 'remove_div' ), 50, 2 );
		}

	}

	/**
	 * Demo URL.
	 */
	public function get_demo_url() {
		return 'https://www.youtube.com/embed/jS7sjfD5Nho?autoplay=1&modestbranding=1&autohide=1&showinfo=0&controls=0';
	}

	/**
	 * Block icon.
	 */
	public function get_icon_svg() {
		return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 42.301 42.301" class="ngl-block-svg-icon">
			<path xmlns="http://www.w3.org/2000/svg"  d="M21.15.563A21.15,21.15,0,1,0,42.3,21.713,21.147,21.147,0,0,0,21.15.563Zm0,8.187a7.5,7.5,0,1,1-7.5,7.5A7.505,7.505,0,0,1,21.15,8.75Zm0,29.338A16.343,16.343,0,0,1,8.656,32.271a9.509,9.509,0,0,1,8.4-5.1,2.087,2.087,0,0,1,.606.094,11.292,11.292,0,0,0,3.488.588,11.249,11.249,0,0,0,3.488-.588,2.087,2.087,0,0,1,.606-.094,9.509,9.509,0,0,1,8.4,5.1A16.343,16.343,0,0,1,21.15,38.087Z" transform="translate(0 -0.563)"/>
		</svg>';
	}

	/**
	 * Block label.
	 */
	public function get_label() {
		return __( 'Author byline', 'newsletter-glue' );
	}

	/**
	 * Block label.
	 */
	public function get_description() {
		return __( 'Add an author byline and follow button to your newsletter.', 'newsletter-glue' );
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

		$defaults[ 'assets_uri' ] 	= NGL_PLUGIN_URL . 'includes/blocks/' . $this->id . '/img/';
		$defaults[ 'name' ]			= __( 'NG: Author byline', 'newsletter-glue' );
		$defaults[ 'description' ] 	= __( 'Add an author byline and follow button to your newsletter.', 'newsletter-glue' );
		$defaults[ 'button_text' ]	= __( 'Follow', 'newsletter-glue' );

		wp_register_script( $this->asset_id, $js_dir . 'block' . $suffix . '.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ), time() );
		wp_localize_script( $this->asset_id, $this->id, $defaults );

		wp_register_style( $this->asset_id . '-style', $css_dir . 'block-ui' . $suffix . '.css', array(), time() );

		register_block_type( 'newsletterglue/author', array(
			'editor_script'   => $this->asset_id,
			'editor_style'    => $this->asset_id . '-style',
			'render_callback' => array( $this, 'render_block' ),
		) );

	}

	/**
	 * Render the block.
	 */
	public function render_block( $attributes, $content ) {

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
			$content = '';
		}

		// Hidden from email.
		if ( defined( 'NGL_IN_EMAIL' ) && ! $show_in_email ) {
			$content = '';
		}

		if ( defined( 'NGL_IN_EMAIL' ) && $content ) {
			$content = $this->tableize( $content );
		}

		return $content;

	}

	/**
	 * CSS.
	 */
	public function email_css() {
		$avatar_image_size = apply_filters( 'newsletterglue_author_avatar_size', 50 );
		$author_weight     = apply_filters( 'newsletterglue_author_name_weight', 'bold' );
		?>
		.wp-block-newsletterglue-author {
			margin: 0 !important;
		}

		.ngl-author {
			display: block;
			padding: 0;
			margin: 0 !important;
		}

		.ngl-author-pic {
			width: <?php echo absint( $avatar_image_size ); ?>px;
			min-width: <?php echo absint( $avatar_image_size ); ?>px;
			margin: 0;
		}

		.ngl-author-pic img {
			margin: 0 !important;
			display: block;
			overflow: hidden;
			border-radius: 999px;
		}

		.ngl-author-name {
			font-weight: <?php echo esc_html( $author_weight ); ?>;
			min-width: 20px;
			padding: 0 0 4px;
			font-size: 14px;
			line-height: 100%;
		}

		.ngl-author-bio {
			margin: 0;
			padding: 0 0 4px;
			font-size: 14px;
			line-height: 150%;
		}

		.ngl-author-btn {
			text-decoration: none !important;
			padding: 6px 10px;
			display: inline-block;
			align-items: center;
			border: 0;
			font-size: 12px;
			line-height: 18px;
			mso-hide: all;
		}

		.ngl-author-btn-text {
			min-width: 20px;
		}

		.ngl-author-cta img {
			display: inline-block !important;
			width: 16px !important;
			height: 16px !important;
			margin: 0 4px 0 0 !important;
			vertical-align: middle;
			position: relative;
			top: -1px;
		}

		.ngl-author-cta .ngl-author-btn-outlined {
			background-color: transparent !important;
			border-width: 2px;
			border-style: solid;
			border-color: transparent;
			padding: 4px 8px;
		}

		.ngl-author-twitter { background-color: #1DA1F2; color: #fff !important; }
		.ngl-author-instagram { background-color: #ed4956; color: #fff !important; }
		.ngl-author-facebook { background-color: #1877F2; color: #fff !important; }
		.ngl-author-twitch { background-color: #9047FF; color: #fff !important; }
		.ngl-author-tiktok { background-color: #fe2c55; color: #fff !important; }
		.ngl-author-youtube { background-color: #FF0000; color: #fff !important; }
		.ngl-author-linkedin { background-color: #0e76a8; color: #fff !important; }

		div.ngl-author-cta a.ngl-author-btn.ngl-author-btn-outlined,
		div.ngl-author-cta a.ngl-author-btn.ngl-author-btn-outlined span,
		.ngl-author-btn-outlined .ngl-author-btn-text { color: #666 !important; }

		.ngl-author-btn-outlined.ngl-author-twitter { border-color: #1DA1F2 !important; }
		.ngl-author-btn-outlined.ngl-author-instagram { border-color: #ed4956 !important; }
		.ngl-author-btn-outlined.ngl-author-facebook { border-color: #1877F2 !important; }
		.ngl-author-btn-outlined.ngl-author-twitch { border-color: #9047FF !important; }
		.ngl-author-btn-outlined.ngl-author-tiktok { border-color: #fe2c55 !important; }
		.ngl-author-btn-outlined.ngl-author-youtube { border-color: #FF0000 !important; }
		.ngl-author-btn-outlined.ngl-author-linkedin { border-color: #0e76a8 !important; }
		<?php
	}

	/**
	 * Remove this block div from article embeds.
	 */
	public function remove_div( $content, $post_id ) {
		$content = newsletterglue_remove_div( $content, 'ngl-author' );

		return $content;
	}

	/**
	 * Tableize.
	 */
	public function tableize( $content ) {

		if ( newsletterglue_get_theme_option( 'font' ) ) {
			$font = "'" . newsletterglue_get_font_name( newsletterglue_get_theme_option( 'font' ) ) . "', Arial, Helvetica, sans-serif";
			} else {
			$font = "Arial, Helvetica, sans-serif";
		}

		$output = new simple_html_dom();
		$output->load( $content, true, false );

		if ( ! strstr( $content, 'ngl-author-cta' ) ) {
			$no_button = true;
		} else {
			$no_button = false;
		}

		$avatar_image_size = apply_filters( 'newsletterglue_author_avatar_size', 50 );

		$valign = apply_filters( 'newsletterglue_author_valign', 'top' );

		$replace = 'div.ngl-author img';
		foreach( $output->find( $replace ) as $key => $element ) {
			$element->class = $element->class . ' ngl-author-block-img';
		}

		// Force image width/height attributes.
		$replace = 'div.ngl-author-cta img';
		foreach( $output->find( $replace ) as $key => $element ) {
			$element->width 	= 16;
			$element->height 	= 16;
		}

		// Force image width/height attributes.
		$replace = 'div.ngl-author-pic img';
		foreach( $output->find( $replace ) as $key => $element ) {
			$element->width 	= $avatar_image_size;
			$element->height 	= $avatar_image_size;
		}

		// Picture cell.
		$replace = 'div.ngl-author-pic';
		foreach( $output->find( $replace ) as $key => $element ) {
			$output->find( $replace, $key )->outertext = '<td width="' . absint( $avatar_image_size ) . '" style="width: ' . absint( $avatar_image_size ) . 'px; max-width: ' . absint( $avatar_image_size ) . 'px !important;vertical-align: ' . $valign . ';padding: 0 20px 0 0;" valign="' . $valign . '" class="ngl-td-auto">' . $element->outertext . '</td>';
		}

		// Meta cell.
		$replace = 'div.ngl-author-meta';
		foreach( $output->find( $replace ) as $key => $element ) {
			$output->find( $replace, $key )->outertext = '<td width="auto" style="vertical-align: ' . $valign . ';padding: 0;" valign="' . $valign . '" class="ngl-td-auto">' . $element->outertext . '</td>';
		}

		// Put every author bio in a table.
		$replace = 'div.wp-block-newsletterglue-author';
		foreach( $output->find( $replace ) as $key => $element ) {
			$padding = $element->{ 'data-padding' } ? explode( ',', $element->{ 'data-padding' } ) : 0;
			$margin = $element->{ 'data-margin' } ? explode( ',', $element->{ 'data-margin' } ) : 0;

			if ( $padding && is_array( $padding ) ) {
				$t = absint( $padding[0] );
				$b = absint( $padding[1] );
				$l = absint( $padding[2] );
				$r = absint( $padding[3] );
			} else {
				$t = 0;
				$b = 0;
				$l = 0;
				$r = 0;
			}

			if ( $margin && is_array( $margin ) ) {
				$mt = absint( $margin[0] );
				$mb = absint( $margin[1] );
			} else {
				$mt = 0;
				$mb = 0;
			}

			if ( $l && $r ) {
				$cols = 4;
			} else if ( $l || $r ) {
				$cols = 3;
			} else {
				$cols = 2;
			}

			$html = '';

			if ( $mt ) {
				$html .= '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td height="' . $mt . '"></td></tr></table>';
			}

			$html .= '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="ngl-table-author">';

			if ( $t ) {
				$html .= '<tr><td height="' . $t. '" colspan="' . $cols . '"></td></tr>';
			}

			$html .= '<tr>';
			if ( $l ) {
				$html .= '<td width="' . $l . '"></td>';
			}
			$html .= $element->innertext;
			if ( $r ) {
				$html .= '<td width="' . $l . '"></td>';
			}
			$html .= '</tr>';

			if ( $b ) {
				$html .= '<tr><td height="' . $b. '" colspan="' . $cols . '"></td></tr>';
			}

			$html .= '</table>';

			if ( $mb ) {
				$html .= '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td height="' . $mb . '"></td></tr></table>';
			}

			$output->find( $replace, $key )->innertext = $html;
		}

		$output->save();

		return ( string ) $output;

	}

}

return new NGL_Block_Author;