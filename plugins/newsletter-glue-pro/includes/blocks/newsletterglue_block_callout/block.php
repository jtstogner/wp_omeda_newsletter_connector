<?php
/**
 * Gutenberg.
 */

if ( ! class_exists( 'NGL_Abstract_Block', false ) ) {
	include_once NGL_PLUGIN_DIR . 'includes/abstract-block.php';
}

class NGL_Block_Callout extends NGL_Abstract_Block {

	public $id = 'newsletterglue_block_callout';

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
		return 'https://www.youtube.com/embed/WiAUdHaHeLo?autoplay=1&modestbranding=1&autohide=1&showinfo=0&controls=0';
	}

	/**
	 * Block icon.
	 */
	public function get_icon_svg() {
		return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="ngl-block-svg-icon">
			<path d="M21 15V18H24V20H21V23H19V20H16V18H19V15H21M14 18H3V6H19V13H21V6C21 4.89 20.11 4 19 4H3C1.9 4 1 4.89 1 6V18C1 19.11 1.9 20 3 20H14V18Z"/>
		</svg>';
	}

	/**
	 * Block label.
	 */
	public function get_label() {
		return __( 'Container', 'newsletter-glue' );
	}

	/**
	 * Block label.
	 */
	public function get_description() {
		return __( 'Customise the background and border of this container block to help its content stand out.', 'newsletter-glue' );
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

		$defaults = array_merge( $defaults, array(
			'name'			=> $this->get_label(),
			'description' 	=> $this->get_description(),
		) );

		wp_register_script( $this->asset_id, $js_dir . 'block' . $suffix . '.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ), time() );
		wp_localize_script( $this->asset_id, $this->id, $defaults );

		wp_register_style( $this->asset_id . '-style', $css_dir . 'block-ui' . $suffix . '.css', array(), time() );

		register_block_type( 'newsletterglue/callout', array(
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

		if ( defined( 'NGL_IN_EMAIL' ) ) {
			$content = str_replace( '<section', '<div', $content );
			$content = str_replace( '/section>', '/div>', $content );
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
		.wp-block-newsletterglue-callout {
			padding: 0 !important;
		}

		.wp-block-newsletterglue-callout * {
			text-align: inherit;
		}

		.wp-block-newsletterglue-callout img {
			width: auto;
		}
		<?php
	}

	/**
	 * Tableize.
	 */
	public function tableize( $content ) {

		$output = new simple_html_dom();
		$output->load( $content, true, false );

		// Spacers.
		$replace = '.wp-block-spacer';
		foreach( $output->find( $replace ) as $key => $element ) {
			$s = $element->style;
			$results = [];
			$styles = explode(';', $s);

			foreach ($styles as $style) {
				$properties = explode(':', $style);
				if (2 === count($properties)) {
					$results[trim($properties[0])] = trim($properties[1]);
				}
			}
			if ( ! empty( $results[ 'height' ] ) ) {
				$clean_height = absint( $results[ 'height' ] );
				$element->outertext = $clean_height;
				$element->outertext = '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="ngl-table ngl-table-spacer"><tr><td height="' . $clean_height .'" style="height: ' . $clean_height . 'px; padding: 0 !important; font-size: 0px; line-height: 100%;">&nbsp;</td></tr></table>';
			}
		}

		// Call-out images.
		$replace = '.wp-block-newsletterglue-callout img';
		foreach( $output->find( $replace ) as $key => $element ) {
			if ( $element->class ) {
				$element->class = $element->class . ' callout-img';
			} else {
				$element->class = 'callout-img';
			}
		}

		$hasmargin = 0;

		$replace = 'div.wp-block-newsletterglue-callout';
		$gap = 20;
		$top_gap = 0;
		$bottom_gap = 0;
		$left_gap = 0;
		$right_gap = 0;
		foreach( $output->find( $replace ) as $key => $element ) {
			if ( strstr( $element->class, 'is-full-width' ) ) {
				$hasmargin = 0;
			} else {
				$hasmargin = 1;
			}
			$s = $element->style;
			$results = [];
			$styles = explode(';', $s);

			foreach ($styles as $style) {
				$properties = explode(':', $style);
				if (2 === count($properties)) {
					$results[trim($properties[0])] = trim($properties[1]);
				}
			}
			foreach( $results as $key => $value ) {
				if ( strstr( $key, 'margin' ) ) {
					unset( $results[ $key ] );
				}
				if ( strstr( $key, 'padding' ) ) {
					$gap = absint( $value );
					if ( $key == 'padding-right' ) {
						$gap = absint( $value );
						$right_gap = absint( $value );
					}
					if ( $key == 'padding-left' ) {
						$left_gap = absint( $value );
					}
					if ( $key == 'padding-top' ) {
						$top_gap = absint( $value );
					}
					if ( $key == 'padding-bottom' ) {
						$bottom_gap = absint( $value );
					}
					unset( $results[ $key ] );
				}
			}
			$tbl_styles = '';
			$div_styles = '';
			if ( isset( $results[ 'border-width' ] ) ) {
				$border_width = absint( $results[ 'border-width' ] );
				if ( ! $border_width ) {
					unset( $results[ 'border-style' ] );
					unset( $results[ 'border-color' ] );
					unset( $results[ 'border-width' ] );
				}
			}
			foreach( $results as $key => $value ) {
				$tbl_styles .= "$key: $value;";
				if ( $key != 'border-color' && $key != 'border-style' && $key != 'border-width' ) {
					$div_styles .= "$key: $value;";
				}
			}

			$top = ! empty( $top_gap ) ? $top_gap : 0;
			$bottom = ! empty( $bottom_gap ) ? $bottom_gap : 0;
			$left = ! empty( $left_gap ) ? $left_gap : 0;
			$right = ! empty( $right_gap ) ? $right_gap : 0;

			$original_gap = ( absint( $left ) + absint( $right ) ) / 2;

			$gap = $gap - 10;

			if ( $gap <= 0 ) {
				$gap = 5;
			}

			$color = ! empty( $results[ 'color' ] ) ? $results[ 'color' ] : '';

			if ( ! empty( $color ) ) {
				foreach( $element->find( '*' ) as $child => $el ) {
					if ( ! strstr( $el->style, 'color:' ) ) {
						$el->style = $el->style . ';color: ' . $color . ';';
					}
				}
			}

			$bg = ! empty( $results[ 'background-color' ] ) ? $results[ 'background-color' ] : '';

			if ( ! empty( $bg ) ) {
				$bgcolor = 'bgcolor="' . $bg . '"';
			} else {
				$bgcolor = null;
			}

			$is_bordered = ! empty( $results[ 'border-width' ] ) ? absint( $results[ 'border-width' ] ) * 2 : 0;
			$is_bordered = ! empty( $results[ 'border-style' ] ) && $results[ 'border-style' ] == 'none' ? 0 : $is_bordered;

			if ( $hasmargin ) {
				$container_padding = '0px 20px';
			} else {
				$container_padding  = '0';
			}

			$boxed_gap = $hasmargin ? 40 : 0;

			$image_width_for_callout = floor( 600 - ( $original_gap * 2 ) ) - $boxed_gap - $is_bordered;
			$max = $image_width_for_callout;
	
			foreach( $element->find( 'img' ) as $image_id => $image_el ) {
				if ( $image_el->width && $image_el->width == 600 ) {
					$image_el->{ 'data-forcew' } = 600;
					$image_el->width = 600;
					$image_el->height = 'auto';
				}
				if ( $image_el->width && $image_el->height ) {
					continue;
				}
				if ( strstr( $image_el->class, 'postembed-image' ) ) {
					$image_el->{ 'data-base-w' } = $max;
					continue;
				}
                $styled_width = newsletterglue_get_style_prop( $image_el->style, 'width' );
                if ( $styled_width && $styled_width < $image_width_for_callout ) {
                    $image_el->width = absint( $styled_width );
                } else {
                    $image_el->width = $image_width_for_callout;
                }
				$image_el->removeAttribute( 'height' );
			}

			$html = '';

			$margin = $element->{ 'data-margin' } ? explode( ',', $element->{ 'data-margin' } ) : 0;

			if ( $margin && is_array( $margin ) ) {
				$mt = absint( $margin[0] );
				$mb = absint( $margin[1] );
			} else {
				$mt = 0;
				$mb = 0;
			}

			if ( $mt ) {
				$html .= '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td height="' . $mt . '"></td></tr></table>';
			}

			$html .= '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="ngl-table-callout">
				<tr>
					<td valign="top" style="vertical-align: top;padding: ' . $container_padding . ';">
						<div style="' . $div_styles . '">
						<table ' . $bgcolor . ' class="' . $element->class . '" border="0" width="100%" cellpadding="0" cellspacing="0" style="font-size: inherit !important;mso-table-lspace: 0pt; mso-table-rspace: 0pt;border: 0;width: 100% !important;table-layout: fixed;' . $tbl_styles . '">
							<tr>
								<td class="ngl-callout-content" data-boxed-gap="' . $image_width_for_callout . '" data-gap="' . $original_gap . '" style="border:none; vertical-align: top; font-size: inherit !important;padding-top: ' . $top . 'px;padding-bottom: ' . $bottom . 'px;padding-left: ' . $left . 'px;padding-right: ' . $right . 'px;" valign="top">' . $element->innertext . '</td>
							</tr>
						</table>
						</div>
					</td>
				</tr>
			</table>';

			if ( $mb ) {
				$html .= '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td height="' . $mb . '"></td></tr></table>';
			}

			$element->outertext = $html;

		}

		$output->save();

		return ( string ) $output;

	}

}

return new NGL_Block_Callout;