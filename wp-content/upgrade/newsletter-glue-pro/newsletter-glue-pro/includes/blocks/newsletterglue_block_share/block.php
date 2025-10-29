<?php
/**
 * Gutenberg.
 */

if ( ! class_exists( 'NGL_Abstract_Block', false ) ) {
	include_once NGL_PLUGIN_DIR . 'includes/abstract-block.php';
}

class NGL_Block_Share extends NGL_Abstract_Block {

	public $id = 'newsletterglue_block_share';

	public $asset_id;

	/**
	 * Construct.
	 */
	public function __construct() {

		$this->asset_id = str_replace( '_', '-', $this->id );

		if ( $this->use_block() === 'yes' ) {
			add_action( 'init', array( $this, 'register_block' ), 10 );
			add_action( 'newsletterglue_add_block_styles', array( $this, 'email_css' ) );
		}

	}

	/**
	 * Demo URL.
	 */
	public function get_demo_url() {
		return 'https://www.youtube.com/embed/-LEDM_bAtFg?autoplay=1&modestbranding=1&autohide=1&showinfo=0&controls=0';
	}

	/**
	 * Block icon.
	 */
	public function get_icon_svg() {
		return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 90.545 63.382" class="ngl-block-svg-icon"><path class="a" d="M27.164,33.941A15.845,15.845,0,1,0,11.318,18.1,15.837,15.837,0,0,0,27.164,33.941Zm10.865,4.527H36.855a21.877,21.877,0,0,1-19.382,0H16.3A16.3,16.3,0,0,0,0,54.766v4.075a6.793,6.793,0,0,0,6.791,6.791H47.536a6.793,6.793,0,0,0,6.791-6.791V54.766A16.3,16.3,0,0,0,38.029,38.468Zm29.88-4.527A13.582,13.582,0,1,0,54.327,20.359,13.585,13.585,0,0,0,67.909,33.941ZM74.7,38.468h-.538a17.841,17.841,0,0,1-12.507,0h-.538a15.714,15.714,0,0,0-7.88,2.179,20.7,20.7,0,0,1,5.617,14.119V60.2c0,.311-.071.608-.085.905H83.754a6.793,6.793,0,0,0,6.791-6.791A15.837,15.837,0,0,0,74.7,38.468Z" transform="translate(0 -2.25)"/></svg>';
	}

	/**
	 * Block label.
	 */
	public function get_label() {
		return __( 'Social follow', 'newsletter-glue' );
	}

	/**
	 * Block label.
	 */
	public function get_description() {
		return __( 'Add social links to your newsletter.', 'newsletter-glue' );
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

		$defaults[ 'assets' ]		= NGL_PLUGIN_URL . 'assets/images/share';
		$defaults[ 'name' ]			= __( 'NG: Social follow', 'newsletter-glue' );
		$defaults[ 'description' ] 	= __( 'Add social links to your newsletter.', 'newsletter-glue' );

		$suffix  = '';

		wp_register_script( $this->asset_id, $js_dir . 'block' . $suffix . '.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ), time() );
		wp_localize_script( $this->asset_id, $this->id, $defaults );

		wp_register_style( $this->asset_id . '-style', $css_dir . 'block-ui' . $suffix . '.css', array(), time() );

		register_block_type( 'newsletterglue/share', array(
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
		?>

		<?php
	}

	/**
	 * Tableize.
	 */
	public function tableize( $content, $post_id = 0 ) {

		$output = new simple_html_dom();
		$output->load( $content, true, false );

		$replace = '.wp-block-newsletterglue-share a';
		foreach( $output->find( $replace ) as $key => $element ) {
			if ( ! $element->href ) {
				$element->href = '#';
			}
		}

		$replace = '.wp-block-newsletterglue-share';
		foreach( $output->find( $replace ) as $key => $element ) {

			$padding = $element->{ 'data-padding' } ? explode( ',', $element->{ 'data-padding' } ) : 0;
			$margin = $element->{ 'data-margin' } ? explode( ',', $element->{ 'data-margin' } ) : 0;

			if ( $padding && is_array( $padding ) ) {
				$t = $padding[0];
				$b = $padding[1];
				$l = $padding[2];
				$r = $padding[3];
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

			$html .= '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="ngl-table-share ngl-table-inline ' . $element->class . '" data-padding="' . "{$t} {$r} {$b} {$l}" . '"><tr><td valign="middle" align="">' . $element->innertext . '</td></tr></table>';

			if ( $mb ) {
				$html .= '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td height="' . $mb . '"></td></tr></table>';
			}

			$element->outertext = $html;
		}

		$output->save();

		return ( string ) $output;

	}

}

return new NGL_Block_Share;