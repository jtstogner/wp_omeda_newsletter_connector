<?php
/**
 * Block API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * NGL_Block_API class.
 */
class NGL_Block_API {

	/**
	 * Constructor.
	 */
	public function __construct() {

		add_action( 'newsletterglue_email_styles', array( $this, 'email_styles' ), 50 );

		add_filter( 'render_block', array( $this, 'render_web' ), 200, 3 );
		add_filter( 'render_block', array( $this, 'render_email' ), 200, 3 );

		add_action( 'init', array( $this, 'register_meta' ), 9 );

		add_action( 'newsletterglue_email_before_closing_body', array( $this, 'compile_css' ) );
		add_action( 'wp_footer', array( $this, 'compile_css' ) );

		add_action( 'wp_head', array( $this, 'wp_head' ), 999 );
		add_action( 'wp_head', array( $this, 'remove_default_css' ), 1000 );
		add_action( 'admin_head', array( $this, 'admin_head' ), 999 );
	}

	/**
	 * This is processed by the email renderer. CSS gets applied inline.
	 */
	public function email_styles() {
		?>
		.ng-block-td p {
			margin: 0 0 10px;
		}

		.ng-block-td p:last-child {
			margin: 0;
		}

		.ng-block ul ul {
			list-style-type: revert;
		}

		ul:not(.wp-block-newsletterglue-list),
		ol:not(.wp-block-newsletterglue-list) {
			margin-left: 20px !important;
		}

		ul.wp-block-newsletterglue-list,
		ol.wp-block-newsletterglue-list {
			margin: 0;
			padding: 0;
		}

		ul.wp-block-newsletterglue-list ul,
		ol.wp-block-newsletterglue-list ol {
			margin: 0;
			padding: 0 0 0 10px;
		}

		ul.wp-block-newsletterglue-list li,
		ol.wp-block-newsletterglue-list li {
			margin: 0 0 0 15px;
		}

		.wp-block-newsletterglue-heading a {
			text-decoration: none;
		}

		.wp-block-newsletterglue-image img {
			max-width: 100%;
			height: auto;
		}

		.wp-block-newsletterglue-image td.ng-block-caption {
			padding-top: 10px;
		}

		.ng-block-button__link {
			display: block;
			text-decoration: none;
			box-sizing: border-box;
			mso-hide: all;
			cursor: pointer;
		}

		#template_inner td.ng-block-td p {
			color: inherit;
		}

		#template_inner .wp-block-newsletterglue-embed a,
		#template_inner .wp-block-newsletterglue-post-author .ng-block-button a {
			text-decoration: none;
		}

		#template_inner .ngl-lp-content p {
			font-family: inherit !important;
		}
		<?php
	}

	/**
	 * Add the CSS in footer (web view?)
	 */
	public function compile_css() {
		global $newsletterglue_css_rules, $newsletterglue_mq;

		$styles = '<style type="text/css">';

		if ( ! empty( $newsletterglue_css_rules ) ) {
			foreach ( $newsletterglue_css_rules as $rule ) {
				$styles .= $rule['selector'] . ' a { ';
				$styles .= 'color: ' . $rule['css'] . '; }';
			}
		}

		$font_size = newsletterglue_get_theme_option( 'mobile_p_size' );
		$font_size = strstr( $font_size, 'px' ) ? $font_size : $font_size . 'px';

		$styles .= '@media screen and (max-width:642px) {';

		$styles .= '.wp-block-newsletterglue-optin.is-landscape .ngl-form-wrap {
            flex-direction: column !important;
            align-items: initial !important;
        }';

		$styles .= '.wp-block-newsletterglue-sections.is-stacked-on-mobile td.wp-block-newsletterglue-section {
			display: block !important;
			float: none !important;
			width: 100% !important;
			clear: both !important;
			box-sizing: border-box !important;
		}';

		$styles .= 'table.wp-block-newsletterglue-text.ng-block td.ng-block-td { padding: ' . newsletterglue_get_default( 'p_padding', true, true ) . ' !important; }';
		$styles .= 'table.wp-block-newsletterglue-text.ng-block td.ng-block-td { line-height: 1.5 !important; }';

		$styles .= 'table.wp-block-newsletterglue-meta-data.ng-block td.ng-block-td { padding: 10px !important; }';

		$styles .= 'table.wp-block-newsletterglue-table.ng-block td.ng-block-td, table.wp-block-newsletterglue-table.ng-block th { padding: 5px !important; }';

		$styles .= 'div.ng-table-wrapper.ng-block { padding: 10px !important; }';

		$styles .= 'table.wp-block-newsletterglue-list.ng-block td.ng-block-td { padding: 8px 0px !important; font-size: ' . esc_attr( $font_size ) . '; }';
		$styles .= 'table.wp-block-newsletterglue-list.ng-block td.ng-block-td li { font-size: ' . esc_attr( $font_size ) . '; line-height: 1.5 !important; }';
		$styles .= 'table.wp-block-newsletterglue-list.ng-block td.ng-block-td td.ng-block-td { padding: 0px 0px 5px 0px !important; font-size: ' . esc_attr( $font_size ) . ' !important; }';
		$styles .= 'table.wp-block-newsletterglue-list.ng-block td.ng-block-td td.ng-block-td li { font-size: ' . esc_attr( $font_size ) . '; }';

		$styles .= 'table.wp-block-newsletterglue-heading.ng-block-h1 td.ng-block-td { padding: ' . newsletterglue_get_default( 'h1_padding', true, true ) . ' !important; line-height: 1.1 !important; }';
		$styles .= 'table.wp-block-newsletterglue-heading.ng-block-h1 td.ng-block-td h1 { line-height: 1.1 !important; }';

		$styles .= 'table.wp-block-newsletterglue-heading.ng-block-h2 td.ng-block-td { padding: ' . newsletterglue_get_default( 'h2_padding', true, true ) . ' !important; line-height: 1.1 !important; }';
		$styles .= 'table.wp-block-newsletterglue-heading.ng-block-h2 td.ng-block-td h2 { line-height: 1.1 !important; }';

		$styles .= 'table.wp-block-newsletterglue-heading.ng-block-h3 td.ng-block-td { padding: ' . newsletterglue_get_default( 'h3_padding', true, true ) . ' !important; line-height: 1.2 !important; }';
		$styles .= 'table.wp-block-newsletterglue-heading.ng-block-h3 td.ng-block-td h3 { line-height: 1.2 !important; }';

		$styles .= 'table.wp-block-newsletterglue-heading.ng-block-h4 td.ng-block-td { padding: ' . newsletterglue_get_default( 'h4_padding', true, true ) . ' !important; line-height: 1.2 !important; }';
		$styles .= 'table.wp-block-newsletterglue-heading.ng-block-h4 td.ng-block-td h4 { line-height: 1.2 !important; }';

		$styles .= 'table.wp-block-newsletterglue-heading.ng-block-h5 td.ng-block-td { padding: ' . newsletterglue_get_default( 'h5_padding', true, true ) . ' !important; line-height: 1.2 !important; }';
		$styles .= 'table.wp-block-newsletterglue-heading.ng-block-h5 td.ng-block-td h5 { line-height: 1.2 !important; }';

		$styles .= 'table.wp-block-newsletterglue-heading.ng-block-h6 td.ng-block-td { padding: ' . newsletterglue_get_default( 'h6_padding', true, true ) . ' !important; line-height: 1.2 !important; }';
		$styles .= 'table.wp-block-newsletterglue-heading.ng-block-h6 td.ng-block-td h6 { line-height: 1.2 !important; }';

		$styles .= 'table.wp-block-newsletterglue-quote > tbody > tr > td.ng-block-td { padding: 15px 20px 15px 20px !important; }';
		$styles .= 'table.wp-block-newsletterglue-quote td.ng-block-td { font-size: 18px; }';
		$styles .= 'table.wp-block-newsletterglue-quote td.ng-block-td p { font-size: 18px !important; }';
		$styles .= 'table.wp-block-newsletterglue-quote td.ng-block-cite span { font-size: 15px !important; }';

		$styles .= 'table.wp-block-newsletterglue-image td { padding: 0 !important; }';
		$styles .= 'table.wp-block-newsletterglue-image td.ng-block-caption { padding-top: 10px !important; }';
		$styles .= 'table.wp-block-newsletterglue-image img { max-width: 100%; height: auto; }';
		$styles .= 'table.wp-block-newsletterglue-image td.ng-block-caption span { font-size: 12px !important; }';

		$styles .= 'table.wp-block-newsletterglue-sections td.ng-columns-wrap { padding: 10px 0 !important; }';
		$styles .= 'td.wp-block-newsletterglue-section { padding: 10px 0 !important; }';

		$styles .= '.ngl-table-latest-posts .ngl-lp-labels,
		div.wp-block-newsletterglue-latest-posts .ngl-lp-labels,
		div.wp-block-newsletterglue-post-embeds .ngl-lp-labels
		{ font-size: 11px !important }';

		$styles .= '.ngl-table-latest-posts .ngl-lp-labels-author,
		div.wp-block-newsletterglue-latest-posts .ngl-lp-labels-author,
		div.wp-block-newsletterglue-post-embeds .ngl-lp-labels-author
		{ font-size: 13px !important }';

		$styles .= '.ngl-table-latest-posts .ngl-lp-title,
			div.wp-block-newsletterglue-latest-posts .ngl-lp-title,
			div.wp-block-newsletterglue-latest-posts .ngl-lp-title h3,
			div.wp-block-newsletterglue-latest-posts .ngl-lp-title h3 a,
			div.wp-block-newsletterglue-post-embeds .ngl-lp-title,
			div.wp-block-newsletterglue-post-embeds .ngl-lp-title h3,
			div.wp-block-newsletterglue-post-embeds .ngl-lp-title h3 a
			{ font-size: 18px !important }';
		$styles .= '.ngl-table-latest-posts .ngl-lp-content, div.wp-block-newsletterglue-latest-posts .ngl-lp-content { font-size: 13px !important }';
		$styles .= '.ngl-table-latest-posts .ngl-lp-cta a, div.wp-block-newsletterglue-latest-posts .ngl-lp-cta a { font-size: 13px !important }';

		$styles .= '.ngl-table-latest-posts .ngl-lp-content, div.wp-block-newsletterglue-post-embeds .ngl-lp-content { font-size: 13px !important }';
		$styles .= '.ngl-table-latest-posts .ngl-lp-cta a, div.wp-block-newsletterglue-post-embeds .ngl-lp-cta a { font-size: 13px !important }';

		$styles .= '.wp-block-newsletterglue-latest-posts.is-stacked .ngl-lp-item,
					.wp-block-newsletterglue-post-embeds.is-stacked .ngl-lp-item {
			display: block !important;
		}';

		$styles .= '.wp-block-newsletterglue-latest-posts.is-stacked.columns-two .ngl-lp-items,
					.wp-block-newsletterglue-post-embeds.is-stacked.columns-two .ngl-lp-items {
			display: block !important;
		}';

		$styles .= '.ng-posts-wrapper.is-stacked td table.ngl-table-latest-posts > tbody > tr > td:first-child {
			padding-top: 10px !important;
		}';

		$styles .= '.ng-posts-wrapper.is-stacked td table.ngl-table-latest-posts > tbody > tr > td:last-child {
			padding-bottom: 10px !important;
		}';

		$styles .= 'table.wp-block-newsletterglue-separator td { padding: 15px !important; }';
		$styles .= 'table.wp-block-newsletterglue-separator hr { margin: auto !important; }';
		$styles .= 'table.wp-block-newsletterglue-separator hr { width: 300px !important; border-width: 1px !important; }';

		$styles .= 'table.wp-block-newsletterglue-optin .ng-form-header { font-size: 18px !important; }';
		$styles .= 'table.wp-block-newsletterglue-optin .ng-form-description,
                    table.wp-block-newsletterglue-optin .ng-form-overlay-text { font-size: 14px !important; }';
		$styles .= 'table.wp-block-newsletterglue-optin .ngl-form-label,
        table.wp-block-newsletterglue-optin .ngl-form-input,
        table.wp-block-newsletterglue-optin .ng-form-text,
        table.wp-block-newsletterglue-optin .ng-form-checkbox-text,
        table.wp-block-newsletterglue-optin .ng-form-button
        { font-size: 13px !important; }';

		if ( ! empty( $newsletterglue_mq ) ) {
			foreach ( $newsletterglue_mq as $selector => $rules ) {
				foreach ( $rules as $rule ) {
					$styles .= $selector . ' { ';
					$styles .= $rule . ' !important; }';
				}
			}
		}

		$styles .= '}';

		$styles .= '</style>';

		echo $styles;
	}

	/**
	 * Add CSS rules.
	 */
	public function add_css_rules( $id, $attrs, $name, $content ) {
		global $newsletterglue_css_rules, $newsletterglue_mq;

		if ( ! $name ) {
			return;
		}

		if ( isset( $attrs['link'] ) ) {
			$newsletterglue_css_rules[] = array(
				'selector' => ".ng-block.ng-block-{$id}",
				'css'      => $attrs['link'],
			);
		}

		$blockid  = str_replace( '/', '-', $name );
		$cssClass = "table.wp-block-{$blockid}.ng-block.ng-block-{$id}";

		switch ( $name ) {

			case 'newsletterglue/social-icons':
				foreach ( $attrs as $key => $value ) {
					if ( strstr( $key, 'mobile_' ) ) {
						if ( $key === 'mobile_icon_size' && $value ) {
							$newsletterglue_mq[ "{$cssClass} img" ][] = "width: {$value}";
							$newsletterglue_mq[ "{$cssClass} img" ][] = "max-width: {$value}";
							$newsletterglue_mq[ "{$cssClass} img" ][] = "height: {$value}";
						}
                        if ( $key === 'mobile_gap' && $value ) {
                            $newsletterglue_mq[ "{$cssClass} span.wp-block-newsletterglue-social-icon"][] = "margin-right: {$value}";
                        }
					}
				}

				break;

			case 'newsletterglue/optin':
				foreach ( $attrs as $key => $value ) {
					if ( strstr( $key, 'mobile_' ) ) {
						if ( $key === 'mobile_padding' ) {
							$t = esc_attr( $value['top'] );
							$b = esc_attr( $value['bottom'] );
							$l = esc_attr( $value['left'] );
							$r = esc_attr( $value['right'] );
							$newsletterglue_mq[ "{$cssClass} td.ng-block-td" ][] = "padding: {$t} {$r} {$b} {$l}";
						}
						if ( $key === 'mobile_margin' ) {
							$t = esc_attr( $value['top'] );
							$b = esc_attr( $value['bottom'] );
							$l = esc_attr( $value['left'] );
							$r = esc_attr( $value['right'] );
							$newsletterglue_mq[ "{$cssClass} td.ng-block-td .ng-block-form" ][] = "margin: {$t} {$r} {$b} {$l}";
						}
					}
					if ( $key === 'mobile_fontsize_text' ) {
						$newsletterglue_mq[ "{$cssClass} .ng-form-text" ][] = 'font-size: ' . esc_attr( $value );
					}
					if ( $key === 'mobile_fontsize_desc' ) {
						$newsletterglue_mq[ "{$cssClass} .ng-form-description" ][] = 'font-size: ' . esc_attr( $value );
					}
					if ( $key === 'mobile_fontsize_heading' ) {
						$newsletterglue_mq[ "{$cssClass} .ng-form-header" ][] = 'font-size: ' . esc_attr( $value );
					}
					if ( $key === 'mobile_fontsize_button' ) {
						$newsletterglue_mq[ "{$cssClass} .ng-form-button" ][] = 'font-size: ' . esc_attr( $value );
					}
					if ( $key === 'mobile_fontsize_label' ) {
						$newsletterglue_mq[ "{$cssClass} .ngl-form-label" ][] = 'font-size: ' . esc_attr( $value );
					}
					if ( $key === 'mobile_fontsize_input' ) {
						$newsletterglue_mq[ "{$cssClass} .ngl-form-input" ][] = 'font-size: ' . esc_attr( $value );
					}
					if ( $key === 'mobile_fontsize_checkbox' ) {
						$newsletterglue_mq[ "{$cssClass} .ng-form-checkbox-text" ][] = 'font-size: ' . esc_attr( $value );
					}
					if ( $key === 'mobile_fontsize_success' ) {
						$newsletterglue_mq[ "{$cssClass} .ng-form-overlay-text" ][] = 'font-size: ' . esc_attr( $value );
					}
				}

				break;

			case 'newsletterglue/post-embeds':
				$class = ".ng-posts-wrapper.ng-block-{$id} .ngl-table-latest-posts";
				foreach ( $attrs as $key => $value ) {
					if ( strstr( $key, 'mobile_' ) ) {
						if ( $key === 'mobile_fontsize_title' ) {
							$newsletterglue_mq[ "{$class} .ngl-lp-title h3" ][]   = 'font-size: ' . esc_attr( $value );
							$newsletterglue_mq[ "{$class} .ngl-lp-title h3 a" ][] = 'font-size: ' . esc_attr( $value );
							$newsletterglue_mq[ ".wp-block-newsletterglue-post-embeds.ng-block-{$id} .ngl-lp-title h3" ][]   = 'font-size: ' . esc_attr( $value );
							$newsletterglue_mq[ ".wp-block-newsletterglue-post-embeds.ng-block-{$id} .ngl-lp-title h3 a" ][] = 'font-size: ' . esc_attr( $value );
						}
						if ( $key === 'mobile_fontsize_label' ) {
							$newsletterglue_mq[ "{$class} span.ngl-lp-labels" ][] = 'font-size: ' . esc_attr( $value );
						}
						if ( $key === 'mobile_fontsize_author' ) {
							$newsletterglue_mq[ "{$class} span.ngl-lp-labels-author" ][] = 'font-size: ' . esc_attr( $value );
						}
						if ( $key === 'mobile_fontsize_text' ) {
							$newsletterglue_mq[ "{$class} .ngl-lp-content" ][] = 'font-size: ' . esc_attr( $value );
							$newsletterglue_mq[ "{$class} .ngl-lp-cta a" ][]   = 'font-size: ' . esc_attr( $value );
							$newsletterglue_mq[ ".wp-block-newsletterglue-post-embeds.ng-block-{$id} .ngl-lp-content" ][] = 'font-size: ' . esc_attr( $value );
							$newsletterglue_mq[ ".wp-block-newsletterglue-post-embeds.ng-block-{$id} .ngl-lp-cta a" ][]   = 'font-size: ' . esc_attr( $value );
						}
					}
				}
				break;

			case 'newsletterglue/latest-posts':
				$class = ".ng-posts-wrapper.ng-block-{$id} .ngl-table-latest-posts";
				foreach ( $attrs as $key => $value ) {
					if ( strstr( $key, 'mobile_' ) ) {
						if ( $key === 'mobile_fontsize_title' ) {
							$newsletterglue_mq[ "{$class} .ngl-lp-title h3" ][]   = 'font-size: ' . esc_attr( $value );
							$newsletterglue_mq[ "{$class} .ngl-lp-title h3 a" ][] = 'font-size: ' . esc_attr( $value );
							$newsletterglue_mq[ ".wp-block-newsletterglue-latest-posts.ng-block-{$id} .ngl-lp-title h3" ][]   = 'font-size: ' . esc_attr( $value );
							$newsletterglue_mq[ ".wp-block-newsletterglue-latest-posts.ng-block-{$id} .ngl-lp-title h3 a" ][] = 'font-size: ' . esc_attr( $value );
						}
						if ( $key === 'mobile_fontsize_label' ) {
							$newsletterglue_mq[ "{$class} span.ngl-lp-labels" ][] = 'font-size: ' . esc_attr( $value );
						}
						if ( $key === 'mobile_fontsize_author' ) {
							$newsletterglue_mq[ "{$class} span.ngl-lp-labels-author" ][] = 'font-size: ' . esc_attr( $value );
						}
						if ( $key === 'mobile_fontsize_text' ) {
							$newsletterglue_mq[ "{$class} .ngl-lp-content" ][] = 'font-size: ' . esc_attr( $value );
							$newsletterglue_mq[ "{$class} .ngl-lp-cta a" ][]   = 'font-size: ' . esc_attr( $value );
							$newsletterglue_mq[ ".wp-block-newsletterglue-latest-posts.ng-block-{$id} .ngl-lp-content" ][] = 'font-size: ' . esc_attr( $value );
							$newsletterglue_mq[ ".wp-block-newsletterglue-latest-posts.ng-block-{$id} .ngl-lp-cta a" ][]   = 'font-size: ' . esc_attr( $value );
						}
					}
				}
				break;

			case 'newsletterglue/image':
				$has_caption = strstr( $content, 'ng-block-caption' );
				foreach ( $attrs as $key => $value ) {
					if ( strstr( $key, 'mobile_' ) ) {
						if ( strstr( $key, '_size' ) ) {
							$newsletterglue_mq[ "{$cssClass} td.ng-block-caption span" ][] = 'font-size: ' . esc_attr( $value );
						}
						if ( $key === 'mobile_padding' ) {
							$t        = esc_attr( $value['top'] );
							$b        = esc_attr( $value['bottom'] );
							$l        = esc_attr( $value['left'] );
							$r        = esc_attr( $value['right'] );
							$first_td = $has_caption ? 0 : $b;
							$newsletterglue_mq[ "{$cssClass} td.ng-block-td" ][] = "padding: {$t} {$r} {$first_td} {$l}";
							if ( $has_caption ) {
								$newsletterglue_mq[ "{$cssClass} td.ng-block-caption" ][] = "padding: 10px {$r} {$b} {$l}";
							}
						}
						if ( $key === 'mobile_width' && empty( $attrs['mobile_keep_size'] ) ) {
							$mobile_w = esc_attr( rtrim( $value, 'px' ) ) . 'px';
							$newsletterglue_mq[ "{$cssClass} td.ng-block-td img" ][] = "width: {$mobile_w}";
						}
						if ( $key === 'mobile_height' && empty( $attrs['mobile_keep_size'] ) ) {
							$mobile_h = esc_attr( rtrim( $value, 'px' ) ) . 'px';
							$newsletterglue_mq[ "{$cssClass} td.ng-block-td img" ][] = "height: {$mobile_h}";
						}
					}
				}
				break;

			case 'newsletterglue/separator':
				foreach ( $attrs as $key => $value ) {
					if ( strstr( $key, 'mobile_' ) ) {
						if ( $key === 'mobile_padding' ) {
							$t = esc_attr( $value['top'] );
							$b = esc_attr( $value['bottom'] );
							$l = esc_attr( $value['left'] );
							$r = esc_attr( $value['right'] );
							$newsletterglue_mq[ "{$cssClass} td.ng-block-td" ][] = "padding: {$t} {$r} {$b} {$l}";
						}
						if ( $key === 'mobile_width' ) {
							$newsletterglue_mq[ "{$cssClass} td.ng-block-td hr" ][] = 'width: ' . esc_attr( $value );
						}
						if ( $key === 'mobile_height' ) {
							$newsletterglue_mq[ "{$cssClass} td.ng-block-td hr" ][] = 'border-width: ' . esc_attr( $value );
						}
						if ( $key === 'mobile_align' ) {
							if ( $value == 'left' ) {
								$newsletterglue_mq[ "{$cssClass} td.ng-block-td hr" ][] = 'margin: 0';
								$newsletterglue_mq[ "{$cssClass} td.ng-block-td hr" ][] = 'margin-right: auto';
							}
							if ( $value == 'right' ) {
								$newsletterglue_mq[ "{$cssClass} td.ng-block-td hr" ][] = 'margin: 0';
								$newsletterglue_mq[ "{$cssClass} td.ng-block-td hr" ][] = 'margin-left: auto';
							}
						}
					}
				}

				break;

			case 'newsletterglue/text':
				foreach ( $attrs as $key => $value ) {
					if ( strstr( $key, 'mobile_' ) ) {
						if ( $key === 'mobile_p_size' ) {
							$newsletterglue_mq[ "{$cssClass} p" ][] = 'font-size: ' . esc_attr( $value );
						}
						if ( $key === 'mobile_p_padding' ) {
							$t = esc_attr( $value['top'] );
							$b = esc_attr( $value['bottom'] );
							$l = esc_attr( $value['left'] );
							$r = esc_attr( $value['right'] );
							$newsletterglue_mq[ "{$cssClass} td.ng-block-td" ][] = "padding: {$t} {$r} {$b} {$l}";
						}
						if ( $key === 'mobile_p_lineheight' ) {
							$newsletterglue_mq[ "{$cssClass} td.ng-block-td" ][] = 'line-height: ' . esc_attr( $value );
						}
					}
				}

				break;

			case 'newsletterglue/meta-data':
				foreach ( $attrs as $key => $value ) {
					if ( strstr( $key, 'mobile_' ) ) {
						if ( $key === 'mobile_size' ) {
							$newsletterglue_mq[ "{$cssClass} td.ng-block-td" ][] = 'font-size: ' . esc_attr( $value );
						}
						if ( $key === 'mobile_padding' ) {
							$t = esc_attr( $value['top'] );
							$b = esc_attr( $value['bottom'] );
							$l = esc_attr( $value['left'] );
							$r = esc_attr( $value['right'] );
							$newsletterglue_mq[ "{$cssClass} td.ng-block-td" ][] = "padding: {$t} {$r} {$b} {$l}";
						}
						if ( $key === 'mobile_lineheight' ) {
							$newsletterglue_mq[ "{$cssClass} td.ng-block-td" ][] = 'line-height: ' . esc_attr( $value );
						}
					}
				}

				break;

			case 'newsletterglue/table':
				foreach ( $attrs as $key => $value ) {
					if ( strstr( $key, 'mobile_' ) ) {
						if ( $key === 'mobile_size' ) {
							$newsletterglue_mq[ "{$cssClass} th" ][] = 'font-size: ' . esc_attr( $value );
							$newsletterglue_mq[ "{$cssClass} td" ][] = 'font-size: ' . esc_attr( $value );
						}
						if ( $key === 'mobile_margin' ) {
							$t = esc_attr( $value['top'] );
							$b = esc_attr( $value['bottom'] );
							$l = esc_attr( $value['left'] );
							$r = esc_attr( $value['right'] );
							$newsletterglue_mq[ ".ng-table-wrapper.ng-block.ng-block-{$id}" ][] = "padding: {$t} {$r} {$b} {$l}";
						}
						if ( $key === 'mobile_padding' ) {
							$t = esc_attr( $value['top'] );
							$b = esc_attr( $value['bottom'] );
							$l = esc_attr( $value['left'] );
							$r = esc_attr( $value['right'] );
							$newsletterglue_mq[ ".ng-table-wrapper.ng-block.ng-block-{$id} th.ng-block-td" ][] = "padding: {$t} {$r} {$b} {$l}";
							$newsletterglue_mq[ ".ng-table-wrapper.ng-block.ng-block-{$id} td.ng-block-td" ][] = "padding: {$t} {$r} {$b} {$l}";
						}
					}
				}

				break;

			case 'newsletterglue/heading':
				$level = ! empty( $attrs['level'] ) ? 'h' . absint( $attrs['level'] ) : 'h2';
				foreach ( $attrs as $key => $value ) {
					if ( strstr( $key, 'mobile_' ) ) {
						if ( strstr( $key, '_size' ) ) {
							$newsletterglue_mq[ "{$cssClass} .ng-block-td {$level}" ][] = 'font-size: ' . esc_attr( $value );
						}
						if ( strstr( $key, '_padding' ) ) {
							$t = esc_attr( $value['top'] );
							$b = esc_attr( $value['bottom'] );
							$l = esc_attr( $value['left'] );
							$r = esc_attr( $value['right'] );
							$newsletterglue_mq[ "{$cssClass} .ng-block-td" ][] = "padding: {$t} {$r} {$b} {$l}";
						}
						if ( strstr( $key, '_lineheight' ) ) {
							$newsletterglue_mq[ "{$cssClass} .ng-block-td" ][]          = 'line-height: ' . esc_attr( $value );
							$newsletterglue_mq[ "{$cssClass} .ng-block-td {$level}" ][] = 'line-height: ' . esc_attr( $value );
						}
					}
				}

				break;
			case 'newsletterglue/quote':
				foreach ( $attrs as $key => $value ) {
					if ( strstr( $key, 'mobile_' ) ) {
						if ( $key === 'mobile_quote_size' ) {
							$newsletterglue_mq[ "{$cssClass} p" ][] = 'font-size: ' . esc_attr( $value );
						}
						if ( $key === 'mobile_quote_citesize' ) {
							$newsletterglue_mq[ "{$cssClass} .ng-block-cite" ][]      = 'font-size: ' . esc_attr( $value );
							$newsletterglue_mq[ "{$cssClass} .ng-block-cite span" ][] = 'font-size: ' . esc_attr( $value );
						}
						if ( $key === 'mobile_quote_padding' ) {
							$t = esc_attr( $value['top'] );
							$b = esc_attr( $value['bottom'] );
							$l = esc_attr( $value['left'] );
							$r = esc_attr( $value['right'] );
							$newsletterglue_mq[ "{$cssClass} > tbody > tr > td.ng-block-td" ][] = "padding: {$t} {$r} {$b} {$l}";
						}
					}
				}

				break;
			case 'newsletterglue/list':
				foreach ( $attrs as $key => $value ) {
					if ( strstr( $key, 'mobile_' ) ) {
						if ( $key === 'mobile_list_size' ) {
							$newsletterglue_mq[ "{$cssClass} > tbody > tr > td.ng-block-td > ul > li" ][] = 'font-size: ' . esc_attr( $value );
							$newsletterglue_mq[ "{$cssClass} > tbody > tr > td.ng-block-td > ol > li" ][] = 'font-size: ' . esc_attr( $value );
						}
						if ( $key === 'mobile_list_lineheight' ) {
							$newsletterglue_mq[ "{$cssClass} > tbody > tr > td.ng-block-td > ul > li" ][] = 'line-height: ' . esc_attr( $value );
							$newsletterglue_mq[ "{$cssClass} > tbody > tr > td.ng-block-td > ol > li" ][] = 'line-height: ' . esc_attr( $value );
						}
						if ( $key === 'mobile_list_spacing' ) {
							$newsletterglue_mq[ "{$cssClass} > tbody > tr > td.ng-block-td > ul > li" ][] = 'padding-bottom: ' . esc_attr( $value );
							$newsletterglue_mq[ "{$cssClass} > tbody > tr > td.ng-block-td > ol > li" ][] = 'padding-bottom: ' . esc_attr( $value );
						}
						if ( $key === 'mobile_list_padding' ) {
							$t = esc_attr( $value['top'] );
							$b = esc_attr( $value['bottom'] );
							$l = esc_attr( $value['left'] );
							$r = esc_attr( $value['right'] );
							$newsletterglue_mq[ "{$cssClass} > tbody > tr > td.ng-block-td" ][] = "padding: {$t} {$r} {$b} {$l}";
						}
					}
				}

				break;
			case 'newsletterglue/spacer':
				foreach ( $attrs as $key => $value ) {
					if ( strstr( $key, 'mobile_' ) ) {
						if ( $key === 'mobile_height' && $value ) {
							$newsletterglue_mq[ "{$cssClass} .ng-block-td" ] = 'height: ' . esc_attr( $value );
						}
					}
				}

				break;

			case 'newsletterglue/sections':
				foreach ( $attrs as $key => $value ) {
					if ( strstr( $key, 'mobile_' ) ) {
						if ( $key === 'mobile_padding' ) {
							$t = esc_attr( $value['top'] );
							$b = esc_attr( $value['bottom'] );
							$l = esc_attr( $value['left'] );
							$r = esc_attr( $value['right'] );
							$newsletterglue_mq[ "{$cssClass} td.ng-columns-wrap" ][] = "padding: {$t} {$r} {$b} {$l}";
						}
					}
				}

				break;

			case 'newsletterglue/section':
				$cssClass = str_replace( 'table', 'td', $cssClass );
				foreach ( $attrs as $key => $value ) {
					if ( strstr( $key, 'mobile_' ) ) {
						if ( $key === 'mobile_padding' ) {
							$t                                    = esc_attr( $value['top'] );
							$b                                    = esc_attr( $value['bottom'] );
							$l                                    = esc_attr( $value['left'] );
							$r                                    = esc_attr( $value['right'] );
							$newsletterglue_mq[ "{$cssClass}" ][] = "padding: {$t} {$r} {$b} {$l}";
						}
					}
				}

				break;

		}
	}

	/**
	 * Replace first occurence.
	 */
	public function str_replace_first( $search, $replace, $string ) {
		$search = '/' . preg_quote( $search, '/' ) . '/';
		if ( ! $string ) {
			return null;
		}
		return preg_replace( $search, $replace, $string, 1 );
	}

	/**
	 * Render block in web
	 */
	public function render_web( $block_content, $block ) {
		global $newsletterglue_css_rules;

		// This should run in email mode only.
		if ( defined( 'NGL_IN_EMAIL' ) ) {
			return $block_content;
		}

		// Only our blocks are targeted.
		if ( ! empty( $block['blockName'] ) && ! strstr( $block['blockName'], 'newsletterglue/' ) ) {
			return $block_content;
		}

		// This attribute means the default "show" value has been modified.
		if ( isset( $block['attrs']['show_in_web'] ) ) {
			return '';
		}

		$id = bin2hex( random_bytes( 8 ) );

		$block_content = $this->str_replace_first( '<table width="100%" cellpadding="0" cellspacing="0" class="', '<table width="100%" cellpadding="0" cellspacing="0" class="ng-block-' . esc_attr( $id ) . ' ', $block_content );

		$block_content = $this->str_replace_first( '<table width="auto" cellpadding="0" cellspacing="0" class="', '<table width="auto" cellpadding="0" cellspacing="0" class="ng-block-' . esc_attr( $id ) . ' ', $block_content );

		$block_content = $this->str_replace_first( 'wp-block-newsletterglue-section ng-block', 'wp-block-newsletterglue-section ng-block ng-block-' . esc_attr( $id ) . ' ', $block_content );
		$block_content = $this->str_replace_first( 'ng-table-wrapper ng-block', 'ng-table-wrapper ng-block ng-block-' . esc_attr( $id ) . ' ', $block_content );

		$block_content = $this->str_replace_first( 'class="ng-posts-wrapper', 'class="ng-posts-wrapper ng-block-' . esc_attr( $id ), $block_content );
		$block_content = $this->str_replace_first( 'wp-block-newsletterglue-latest-posts', 'wp-block-newsletterglue-latest-posts ng-block-' . esc_attr( $id ), $block_content );
		$block_content = $this->str_replace_first( 'wp-block-newsletterglue-post-embeds', 'wp-block-newsletterglue-post-embeds ng-block-' . esc_attr( $id ), $block_content );

		$attrs = $block['attrs'];

		$this->add_css_rules( $id, $attrs, $block['blockName'], $block_content );

		return $block_content;
	}

	/**
	 * Render block in email
	 */
	public function render_email( $block_content, $block ) {
		global $newsletterglue_css_rules;

		// This should run in email mode only.
		if ( ! defined( 'NGL_IN_EMAIL' ) ) {
			return $block_content;
		}

		// Only our blocks are targeted.
		if ( ! empty( $block['blockName'] ) && ! strstr( $block['blockName'], 'newsletterglue/' ) ) {
			return $block_content;
		}

		// This attribute means the default "show" value has been modified.
		if ( isset( $block['attrs']['show_in_email'] ) && ! strstr( $block['blockName'], 'newsletterglue/optin' ) ) {
			return '';
		} elseif ( isset( $block['attrs']['show_in_email'] ) ) {
				return '';
		}

		$id = bin2hex( random_bytes( 8 ) );

		$block_content = $this->str_replace_first( '<table width="100%" cellpadding="0" cellspacing="0" class="', '<table width="100%" cellpadding="0" cellspacing="0" class="ng-block-' . esc_attr( $id ) . ' ', $block_content );

		$block_content = $this->str_replace_first( '<table width="auto" cellpadding="0" cellspacing="0" class="', '<table width="auto" cellpadding="0" cellspacing="0" class="ng-block-' . esc_attr( $id ) . ' ', $block_content );

		$block_content = $this->str_replace_first( 'wp-block-newsletterglue-section ng-block', 'wp-block-newsletterglue-section ng-block ng-block-' . esc_attr( $id ) . ' ', $block_content );
		$block_content = $this->str_replace_first( 'ng-table-wrapper ng-block', 'ng-table-wrapper ng-block ng-block-' . esc_attr( $id ) . ' ', $block_content );

		$block_content = $this->str_replace_first( 'class="ng-posts-wrapper', 'class="ng-posts-wrapper ng-block-' . esc_attr( $id ), $block_content );

		$attrs = $block['attrs'];

		$this->add_css_rules( $id, $attrs, $block['blockName'], $block_content );

		return $block_content;
	}

	/**
	 * Add custom head for front-end.
	 */
	public static function wp_head() {
		global $post_type, $post;

		if ( empty( $post_type ) || $post_type !== 'newsletterglue' ) {
			return;
		}

		if ( ! empty( get_option( 'newsletterglue_disable_front_css' ) ) ) {
			return;
		}

		$theme = newsletterglue_get_theme_options();

		$font_face = esc_attr( newsletterglue_get_font_name( newsletterglue_get_theme_option( 'p_font' ) ) );

		$font_size = newsletterglue_get_theme_option( 'p_size' );
		$font_size = strstr( $font_size, 'px' ) ? $font_size : $font_size . 'px';

		echo '<style type="text/css">';

		echo 'div.ng-block, h1.ng-block, h2.ng-block, h3.ng-block, h4.ng-block, h5.ng-block, h6.ng-block, table.ng-block, div.wp-block-newsletterglue-latest-posts, div.wp-block-newsletterglue-post-embeds {
			max-width: 600px !important;
			margin-top: 0;
			margin-bottom: 0;
			box-sizing: border-box;
		}';

		echo '.ng-block img {
			max-width: 100%;
		}';

		echo '.ng-block a {
			color: ' . esc_attr( $theme['a_colour'] ) . ';
		}';

		echo '.ng-block cite { font-style: normal; }';

		echo '.wp-block-newsletterglue-buttons {
			display: flex;
		}';

		echo 'table.wp-block-newsletterglue-buttons td {
			white-space: nowrap;
		}';

		echo '.ng-block .wp-block-newsletterglue-buttons .wp-block-newsletterglue-button a {
			display: inline-block;
			background: ' . esc_attr( newsletterglue_get_theme_option( 'a_colour' ) ) . ';
			color: #fff;
			font-size: ' . esc_attr( $font_size ) . ';
			text-align: center;
			box-sizing: border-box;
		}';

		echo '.ng-block img {
			display: block;
		}';

		echo '.ng-block-button__link {
			display: block;
			text-decoration: none;
			box-sizing: border-box;
			cursor: pointer;
		}';

		echo '.ng-block.wp-block-newsletterglue-embed a {
			text-decoration: none;
		}';

		do_action( 'newsletterglue_add_custom_styles', ! empty( $post ) ? $post : null );

		echo '</style>';
	}

	/**
	 * Add custom head for front-end.
	 */
	public static function remove_default_css() {
		global $post_type, $post;

		if ( empty( $post_type ) || $post_type !== 'newsletterglue' ) {
			return;
		}

		if ( ! get_option( 'newsletterglue_disable_front_css' ) ) {
			return;
		}

		?>
		<style type="text/css">
		.ngl-lp-items, .ngl-lp-title, .ngl-lp-title h3, .ngl-lp-item h3 a, .ngl-lp-content, .ngl-lp-cta, .ngl-lp-cta a {
			font-family: inherit !important;
			font-size: inherit !important;
		}

		.table-ratio-30_70 .ngl-lp-image {
			flex-basis: 30% !important;
		}
		.table-ratio-30_70 .ngl-lp-data {
			flex-basis: 70% !important;
		}
		.ngl-lp-title h3 a {
			font-size: 1.5rem!important;
		}
		div.wp-block-newsletterglue-image.ng-block  {
			margin-bottom: 1.2rem !important;
		}
		.wp-block-newsletterglue-image.size-full img {
			width: 100%;
			height: auto;
		}
		div.ngl-article-date {
			font-size: inherit;
		}
		div.ng-block div:has(> .wp-block-newsletterglue-buttons) {
			padding: 0 !important;
			margin: 1.5rem 0;
		}
		.wp-block-newsletterglue-buttons .ng-block-button__link {
			display:inline-block;
		}
		</style>
		<?php
	}

	/**
	 * Admin head.
	 */
	public function admin_head() {
		global $post_type, $post, $ngl_post_id;

		if ( empty( $post_type ) ) {
			return;
		}

		if ( isset( $post ) && ! empty( $post->ID ) ) {
			$ngl_post_id = $post->ID;
		}

		if ( in_array( $post_type, newsletterglue_get_main_cpts() ) ) {

			echo '<style>';

			echo 'table.ng-block div.wp-block-newsletterglue-button .ng-block-button__link,
			table.ng-block div.wp-block-newsletterglue-button .ng-block-button__link:hover,
			table.ng-block div.wp-block-newsletterglue-button .ng-block-button__link:focus { background: ' . esc_attr( newsletterglue_get_theme_option( 'a_colour' ) ) . '; }';

			echo '</style>';
		}
	}

	/**
	 * Register meta
	 */
	public function register_meta() {

		$meta = array(
			'_webview'     => array(
				'type' => 'string',
			),
			'subject_line' => array(
				'type' => 'string',
			),
		);

		foreach ( $meta as $key => $data ) {
			register_post_meta(
				'newsletterglue',
				$key,
				array(
					'show_in_rest'  => true,
					'single'        => true,
					'type'          => $data['type'],
					'default'       => '',
					'auth_callback' => function () {
						return current_user_can( 'edit_newsletterglue' ); },
				)
			);
		}
	}
}

return new NGL_Block_API();
