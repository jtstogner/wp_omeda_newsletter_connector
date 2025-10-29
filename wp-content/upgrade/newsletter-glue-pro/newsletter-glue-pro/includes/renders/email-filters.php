<?php
/**
 * Email filters.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fix most email client issues here.
 */
add_filter( 'newsletterglue_generated_html_output', 'newsletterglue_generated_html_output_hook1', 1, 3 );
function newsletterglue_generated_html_output_hook1( $html, $post_id, $app ) {

	$output = new simple_html_dom();
	$output->load( $html, true, false );

	$settings = newsletterglue_get_data( $post_id );

	$postdata = get_post( $post_id );

	// UTM Support.
	$replace = 'a';
	foreach ( $output->find( $replace ) as $key => $element ) {
		if ( $element->href ) {
			$href = $element->href;
			if ( ! strstr( $href, 'http://' ) && ! strstr( $href, 'https://' ) ) {
				continue;
			}
			if ( isset( $settings->utm_campaign ) ) {
				if ( $settings->utm_campaign == '[none]' ) {
					$utm_campaign = '';
				} else {
					$utm_campaign = $settings->utm_campaign;
				}
			} else {
				$utm_campaign = newsletterglue_get_option( 'utm_campaign', 'global' );
			}

			if ( isset( $settings->utm_source ) ) {
				if ( $settings->utm_source == '[none]' ) {
					$utm_source = '';
				} else {
					$utm_source = $settings->utm_source;
				}
			} else {
				$utm_source = newsletterglue_get_option( 'utm_source', 'global' );
			}

			if ( isset( $settings->utm_medium ) ) {
				if ( $settings->utm_medium == '[none]' ) {
					$utm_medium = '';
				} else {
					$utm_medium = $settings->utm_medium;
				}
			} else {
				$utm_medium = newsletterglue_get_option( 'utm_medium', 'global' );
			}

			if ( isset( $settings->utm_content ) ) {
				if ( $settings->utm_content == '[none]' ) {
					$utm_content = '';
				} else {
					$utm_content = $settings->utm_content;
				}
			} else {
				$utm_content = newsletterglue_get_option( 'utm_content', 'global' );
			}

			if ( $utm_source && ! strstr( $href, 'utm_source' ) ) {
				$href = add_query_arg( 'utm_source', $utm_source, $href );
			}

			if ( $utm_campaign && ! strstr( $href, 'utm_campaign' ) ) {
				if ( strstr( $utm_campaign, '{{newsletter_title}}' ) ) {
					$utm_campaign = str_replace( '{{newsletter_title}}', esc_html( $postdata->post_title ), $utm_campaign );
				}
				$href = add_query_arg( 'utm_campaign', $utm_campaign, $href );
			}

			if ( $utm_medium && ! strstr( $href, 'utm_medium' ) ) {
				$href = add_query_arg( 'utm_medium', $utm_medium, $href );
			}

			if ( $utm_content && ! strstr( $href, 'utm_content' ) ) {
				$href = add_query_arg( 'utm_content', $utm_content, $href );
			}

			$element->href = apply_filters( 'newsletterglue_email_link_filter', $href, $post_id );
		}
	}

	$replace = '.ng-block-td div';
	foreach ( $output->find( $replace ) as $key => $element ) {
		if ( strstr( $element->class, 'ng-div' ) ) {
			continue;
		}
		if ( strstr( $element->class, 'ngl-form' ) ) {
			continue;
		}
		if ( strstr( $element->class, 'ng-form' ) ) {
			continue;
		}
		if ( strstr( $element->class, 'ngl-lp-content' ) ) {
			continue;
		}
		$element->outertext = $element->innertext;
	}

	$replace = '.ng-block-td .ngl-share-wrap p';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$element->outertext = $element->innertext;
	}

	// Fix border-radius.
	$replace = '.is-style-rounded';
	foreach ( $output->find( $replace ) as $key => $element ) {
		foreach ( $element->find( 'img' ) as $a => $b ) {
			$b->style = $b->style . 'border-radius: 999px !important;';
		}
	}

	// Spacers.
	$replace = '.wp-block-spacer';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$s       = $element->style;
		$results = array();
		$styles  = explode( ';', $s );

		foreach ( $styles as $style ) {
			$properties = explode( ':', $style );
			if ( 2 === count( $properties ) ) {
				$results[ trim( $properties[0] ) ] = trim( $properties[1] );
			}
		}
		if ( ! empty( $results['height'] ) ) {
			$clean_height       = absint( $results['height'] );
			$element->outertext = $clean_height;
			$element->outertext = '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="ngl-table ngl-table-spacer"><tr><td height="' . $clean_height . '" style="height: ' . $clean_height . 'px; padding: 0 !important; font-size: 0px; line-height: 100%;">&nbsp;</td></tr></table>';
		}
	}

	// Outline style.
	$replace = '.is-style-outline';
	foreach ( $output->find( $replace ) as $key => $element ) {
		foreach ( $element->find( 'a' ) as $a => $b ) {
			$b->class = $b->class . ' is-style-outlined';
		}
	}

	// Fix figures. direct images.
	$replace = 'figure.aligncenter, figure.alignleft, figure.alignright, figure.wp-block-image';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$align = 'center';
		if ( strstr( $element->class, 'aligncenter' ) ) {
			$align = 'center';
		}
		if ( strstr( $element->class, 'alignleft' ) ) {
			$align = 'left';
		}
		if ( strstr( $element->class, 'alignright' ) ) {
			$align = 'right';
		}
		foreach ( $element->find( 'img' ) as $a => $b ) {
			if ( ! $b->class ) {
				$b->class = 'wp-image wp-image-' . $align;
			}
			if ( $b->class && strstr( $b->class, 'wp-image-' ) ) {
				$b->class = $b->class . ' wp-image wp-image-' . $align;
			}
			$b->{ 'data-align' } = $align;
		}
		$element->outertext = $element->innertext;
	}

	// Force image width.
	$replace = 'figure.wp-block-image img';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$element->class = ! empty( $element->class ) ? $element->class . ' wp-image' : 'wp-image';
		$style          = rtrim( $element->style, ';' ) . ';';
		$element->style = $style . 'min-width: 10px; margin-bottom:0 !important;';
	}

	// Fix figures/images.
	$replace = 'figure.aligncenter, figure.alignleft, figure.alignright, figure.wp-block-image';
	foreach ( $output->find( $replace ) as $key => $element ) {
		if ( $element->find( 'img', 0 ) ) {
			if ( $element->class ) {
				$class = str_replace( 'wp-block-image', '', $element->class );
				$class = trim( $class ) . ' ';
			} else {
				$class = '';
			}
			$element->find( 'img', 0 )->class = $class . $element->find( 'img', 0 )->class;
		}
		if ( $element->find( 'figcaption', 0 ) ) {
			if ( $element->find( 'figcaption', 0 )->find( 'a' ) ) {
				$element->find( 'figcaption', 0 )->find( 'a', 0 )->class = 'caption-link';
			}
		}
		if ( $element->find( 'a' ) && ! strstr( $element->find( 'a', 0 )->class, 'caption-link' ) ) {
			$element->find( 'img', 0 )->{'data-href'} = $element->find( 'a', 0 )->href;
			$element->find( 'a', 0 )->outertext       = $element->innertext;
		}
		if ( $element->find( 'figcaption', 0 ) ) {
			$element->outertext = $element->find( 'img', 0 )->outertext . $element->find( 'figcaption', 0 )->outertext;
		} else {
			$element->outertext = $element->find( 'img', 0 )->outertext;
		}
	}

	// Output column.
	$replace = '.wp-block-columns .wp-block-column';
	foreach ( $output->find( $replace ) as $key => $element ) {

		$col_count = count( $output->find( $replace, $key )->parent()->find( 'div.wp-block-column' ) );

		$width = '';

		// Has style.
		if ( $output->find( $replace, $key )->style ) {

			if ( $element->parent->parent && strstr( $element->parent->parent->class, 'wp-block-group' ) ) {
				$the_width = 560;
			} else {
				$the_width = 600;
			}

			// Fix the padding gaps - do not let email container go beyond 600px.
			$find_parent = $element->parent()->parent()->style ? $element->parent()->parent()->style : false;

			if ( $find_parent ) {
				$inner_r  = array();
				$inner_ss = explode( ';', $find_parent );

				foreach ( $inner_ss as $inner_s ) {
					$inner_props = explode( ':', $inner_s );
					if ( 2 === count( $inner_props ) ) {
						$inner_r[ trim( $inner_props[0] ) ] = trim( $inner_props[1] );
					}
				}
				if ( isset( $inner_r['padding'] ) ) {
					$split     = explode( ' ', $inner_r['padding'] );
					$split_n   = absint( $split[1] );
					$the_width = $the_width - absint( $split_n ) * 2;
				} else {
					if ( isset( $inner_r['padding-left'] ) ) {
						$split     = explode( ' ', $inner_r['padding-left'] );
						$split_n   = absint( $split[0] );
						$the_width = $the_width - absint( $split_n );
					}
					if ( isset( $inner_r['padding-right'] ) ) {
						$split     = explode( ' ', $inner_r['padding-right'] );
						$split_n   = absint( $split[0] );
						$the_width = $the_width - absint( $split_n );
					}
				}
			}

			$find_parent2 = $element->parent()->parent()->parent()->parent()->parent()->parent()->style ? $element->parent()->parent()->parent()->parent()->parent()->parent()->style : false;
			if ( $find_parent2 ) {
				$inner_r  = array();
				$inner_ss = explode( ';', $find_parent2 );

				foreach ( $inner_ss as $inner_s ) {
					$inner_props = explode( ':', $inner_s );
					if ( 2 === count( $inner_props ) ) {
						$inner_r[ trim( $inner_props[0] ) ] = trim( $inner_props[1] );
					}
				}
				if ( ! empty( $inner_r['padding'] ) ) {
					$split     = explode( ' ', $inner_r['padding'] );
					$split_n   = absint( $split[1] );
					$the_width = $the_width - absint( $split_n ) * 2;
				} else {
					if ( isset( $inner_r['padding-left'] ) ) {
						$split     = explode( ' ', $inner_r['padding-left'] );
						$split_n   = absint( $split[0] );
						$the_width = $the_width - absint( $split_n );
					}
					if ( isset( $inner_r['padding-right'] ) ) {
						$split     = explode( ' ', $inner_r['padding-right'] );
						$split_n   = absint( $split[0] );
						$the_width = $the_width - absint( $split_n );
					}
				}
			}

			$s       = $output->find( $replace, $key )->style;
			$results = array();
			$styles  = explode( ';', $s );

			foreach ( $styles as $style ) {
				$properties = explode( ':', $style );
				if ( 2 === count( $properties ) ) {
					$results[ trim( $properties[0] ) ] = trim( $properties[1] );
				}
			}
			if ( isset( $results['flex-basis'] ) ) {
				if ( strstr( $results['flex-basis'], 'px' ) ) {
					$width = absint( $results['flex-basis'] );
				} else {
					$width = absint( $results['flex-basis'] ) / 100 * $the_width;
				}
			} else {
				$width = $the_width / $col_count;
			}
		} else {

		}

		$valign = 'top';

		if ( strstr( $output->find( $replace, $key )->outertext, 'is-vertically-aligned-center' ) ) {
			$valign = 'middle';
		}
		if ( strstr( $output->find( $replace, $key )->outertext, 'is-vertically-aligned-bottom' ) ) {
			$valign = 'bottom';
		}

		if ( ! empty( $element->parent->class ) && strstr( $element->parent->class, 'ngl-no-wrap' ) ) {
			$td_class = 'column-no-wrap';
		} else {
			$td_class = 'column';
		}

		$width_output = is_numeric( $width ) ? floor( $width ) : $width;

		$output->find( $replace, $key )->outertext = '<td class="' . $td_class . '" width="' . $width_output . '" style="vertical-align: ' . $valign . ';" valign="' . $valign . '">' . $element->innertext . '</td>';
	}

	// Add columns wrapper as a table.
	$replace = '.wp-block-columns';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$class                                     = str_replace( 'wp-block-columns', '', $element->class );
		$class                                     = trim( $class );
		$output->find( $replace, $key )->outertext = '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="ngl-table-columns ' . $class . '"><tr class="root-tr">' . $element->innertext . '</tr></table>';
	}

	// Change all figures.
	$replace = 'figure.wp-block-table';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$output->find( $replace, $key )->outertext = '<div class="wp-block-table">' . $element->innertext . '</div>';
	}

	// Convert embed metadata to table.
	$replace = '.ngl-embed-metadata';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$output->find( $replace, $key )->outertext = '<table width="100%" border="0" cellpadding="20" cellspacing="0"><tr><td width="50%" align="left" valign="top" style="vertical-align: top;margin:0 !important;">' . $element->outertext . '</td>';
	}

	$replace = '.ngl-embed-icon';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$output->find( $replace, $key )->outertext = '<td width="50%" align="right" valign="top" style="vertical-align: top;margin:0 !important;text-align: right !important;">' . $element->outertext . '</td></tr></table>';
	}

	// Gallery block.
	$replace = '.blocks-gallery-grid';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$class = $element->parent()->class;
		$cols  = 1;
		if ( strstr( $class, 'columns-4' ) ) {
			$cols = 4;
		}
		if ( strstr( $class, 'columns-3' ) ) {
			$cols = 3;
		}
		if ( strstr( $class, 'columns-2' ) ) {
			$cols = 2;
		}
		$html = '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>';
		$i    = 0;
		foreach ( $element->find( 'li' ) as $item => $list ) {
			++$i;
			$width = ( 600 / $cols ) - 20;
			$image = '<img src="' . $list->find( 'img', 0 )->src . '" alt="" width="' . $width . '" style="margin: 0;display: block; max-width: 100%; min-width: 50px; width: 100%;" />';
			$html .= '<td valign="top" style="vertical-align: top;margin:0;">' . $image . '</td>';
			if ( $i % $cols == 0 ) {
				$html .= '</tr>';
			}
		}
		$html                                     .= '</table>';
		$output->find( $replace, $key )->outertext = $html;
	}

	// v3
	$replace = 'table';
	foreach ( $output->find( $replace ) as $key => $element ) {
		if ( empty( $element->getAttribute( 'ng-editor' ) ) ) {
			continue;
		}
		$attributes = $element->getAttribute( 'ng-editor' );
		$exploded   = explode( '!!', $attributes );
		$styles     = '';
		$level      = null;
		foreach ( $exploded as $attr ) {
			$split_attr = explode( ':', $attr );
			if ( $split_attr[0] === 'color' ) {
				$styles .= 'color: ' . $split_attr[1] . ';';
			}
			if ( $split_attr[0] === 'fontsize' ) {
				$font_size = $split_attr[1];
				$font_size = strstr( $font_size, 'px' ) ? $font_size : $font_size . 'px';
				$styles   .= 'font-size: ' . $font_size . ';';
			}
			if ( $split_attr[0] === 'lineheight' ) {
				$styles .= 'line-height: ' . $split_attr[1] . ';';
			}
			if ( $split_attr[0] === 'fontweight' ) {
				$styles .= 'font-weight: ' . $split_attr[1] . ';';
			}
			if ( $split_attr[0] === 'alignment' ) {
				$styles .= 'text-align: ' . $split_attr[1] . ';';
			}
			if ( $split_attr[0] === 'level' ) {
				$level = $split_attr[1];
			}
		}

		foreach ( $element->find( 'p' ) as $sub_e => $sub_el ) {
			$sub_el->style = $sub_el->style . $styles;
		}

		if ( ! empty( $level ) ) {
			foreach ( $element->find( 'h' . $level . '.ng-block' ) as $sub_e => $sub_el ) {
				$sub_el->style = $styles;
			}
		}
	}

	$output->save();

	return (string) $output;
}

/**
 * After all emogrify is done.
 */
add_filter( 'newsletterglue_final_html_content', 'newsletterglue_final_html_content2', 20, 1 );
function newsletterglue_final_html_content2( $html ) {

	$output = new simple_html_dom();
	$output->load( $html, true, false );

	// Fix image size.
	$replace = '#template_inner img';
	foreach ( $output->find( $replace ) as $key => $element ) {
		if ( strstr( $element->class, 'ng-image' ) ) {
			continue;
		}
		if ( $element->{ 'data-forcew' } ) {
			continue;
		}
		if ( $element->style ) {
			$s       = $element->style;
			$results = array();
			$styles  = explode( ';', $s );

			foreach ( $styles as $style ) {
				$properties = explode( ':', $style );
				if ( 2 === count( $properties ) ) {
					$results[ trim( $properties[0] ) ] = trim( $properties[1] );
				}
			}
			if ( isset( $results['width'] ) ) {
				$w = absint( $results['width'] );
				if ( $w && ( $results['width'] != '100%' ) && $element->width && $element->width > 500 && $w < 500 ) {
					$element->width       = $w;
					$results['max-width'] = $results['width'];
					$element->style       = '';
					foreach ( $results as $key => $value ) {
						$element->style .= "$key: $value; ";
					}
					$element->style = rtrim( $element->style );
				}
			}
		}
	}

	// Do not include the wp block image div.
	$replace = '.wp-block-image';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$element->outertext = $element->innertext;
	}

	// Remove unwanted group block.
	$replace = '.wp-block-newsletterglue-group';
	foreach ( $output->find( $replace ) as $key => $element ) {

		$padding = $element->{ 'data-padding' } ? explode( ',', $element->{ 'data-padding' } ) : 0;
		$margin  = $element->{ 'data-margin' } ? explode( ',', $element->{ 'data-margin' } ) : 0;

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
		} elseif ( $l || $r ) {
			$cols = 3;
		} else {
			$cols = 2;
		}

		$element->parent->style = "padding: $t $r $b $l !important;";

		$html = '';

		if ( $mt ) {
			$html .= '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td height="' . $mt . '"></td></tr></table>';
		}

		$html .= $element->innertext;

		if ( $mb ) {
			$html .= '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td height="' . $mb . '"></td></tr></table>';
		}

		$element->outertext = $html;
	}

	// Remove unwanted class junk.
	$replace = '#template_inner td';
	foreach ( $output->find( $replace ) as $key => $element ) {
		if ( $element->height && $element->height <= 10 ) {
			$element->style = $element->style . 'padding:0!important;';
		}
	}

	// Remove unwanted class junk.
	$replace = 'table.ngl-table, #template_inner img, table.ngl-table-callout, table.wp-block-newsletterglue-callout, td.ngl-callout-content, a.ngl-metadata-permalink, .wp-block-button__link, div.wp-block-buttons, .wp-block-button, h1.title, p.has-drop-cap, p.has-text-color, a.logo, tr.root-tr, .has-inline-color, .has-text-align-left, .has-text-align-center, .ngl-social-link, .ngl-share-description';
	foreach ( $output->find( $replace ) as $key => $element ) {
		if ( strstr( $element->class, 'ng-image' ) ) {
			continue;
		}
		if ( ! strstr( $element->class, 'ngl-table-ngl-unsubscribe' ) && ! strstr( $element->class, 'logo-image' ) ) {
			if ( $element->tag === 'img' && $element->class ) {
				$classes  = explode( ' ', $element->class );
				$_classes = array();
				foreach ( $classes as $class ) {
					if ( in_array( $class, array( 'wp-image', 'wp-image-center', 'wp-image-left', 'wp-image-right', 'is-resized', 'size-large', 'size-small' ) ) ) {
						continue;
					}
					$_classes[] = $class;
				}
				$element->class = implode( ' ', $_classes );
			} else {

			}
			$element->removeAttribute( 'alt' );
			$element->removeAttribute( 'data-gap' );
			$element->removeAttribute( 'data-href' );
			$element->removeAttribute( 'data-align' );
			$element->removeAttribute( 'data-boxed-gap' );
		}
	}

	// Replace font-family.
	if ( apply_filters( 'newsletterglue_force_font_family', false ) ) {
		$replace = 'body, td, div, p';
		foreach ( $output->find( $replace ) as $key => $element ) {
			if ( $element->style ) {
				$s       = $element->style;
				$results = array();
				$styles  = explode( ';', $s );

				foreach ( $styles as $style ) {
					$properties = explode( ':', $style );
					if ( 2 === count( $properties ) ) {
						$results[ trim( $properties[0] ) ] = trim( $properties[1] );
					}
				}
				if ( ! empty( $results['font-family'] ) ) {
					$default = apply_filters( 'newsletterglue_default_font_family', '' );
					if ( ! empty( $default ) ) {
						$element->style = str_replace( $results['font-family'], $default, $element->style );
					}
				}
			}
		}
	}

	$output->save();

	return (string) $output;
}

/**
 * After all emogrify is done.
 */
add_filter( 'newsletterglue_final_html_content', 'newsletterglue_final_html_content3', 200, 1 );
function newsletterglue_final_html_content3( $html ) {

	$output = new simple_html_dom();
	$output->load( $html, true, false );

	// Added after v2.1
	$replace = 'a, img, div, table, p, h1, h2, h3, h4, h5, h6, td, mark, span';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$attrs = $element->attr;
		if ( isset( $attrs ) && ! empty( $attrs ) ) {
			foreach ( $attrs as $id => $val ) {
				if ( strstr( $id, 'data-' ) || strstr( $id, 'ng-editor' ) ) {
					if ( strstr( $id, 'data-conditions' ) ) {
						continue;
					}
					$element->removeAttribute( $id );
				}
			}
		}
		if ( $element->class ) {
			$class = $element->class;
			$class = str_replace( 'has-background-color', '', $class );
			$class = str_replace( 'has-foreground-color', '', $class );
			$class = str_replace( 'has-text-color', '', $class );
			$class = str_replace( 'has-text-align-right', '', $class );
			$class = str_replace( 'has-text-align-center', '', $class );
			$class = str_replace( 'has-text-align-left', '', $class );
			$class = str_replace( 'has-tertiary-color', '', $class );
			$class = str_replace( 'has-css-opacity', '', $class );
			$class = str_replace( 'has-css-opacity', '', $class );
			$class = str_replace( 'has-tertiary-background-color', '', $class );
			$class = str_replace( 'is-style-default', '', $class );
			$class = str_replace( 'has-link-color', '', $class );
			$class = str_replace( 'has-inline-color', '', $class );
			$class = str_replace( 'has-primary-color', '', $class );
			$class = str_replace( 'size-full', '', $class );
			$class = str_replace( 'aligncenter', '', $class );
			$class = str_replace( 'alignwide', '', $class );
			$class = str_replace( 'not-color-set', '', $class );
			$class = str_replace( 'undefined', '', $class );
			$class = str_replace( 'size-large', '', $class );
			$class = str_replace( 'is-resized', '', $class );
			$class = str_replace( 'avatar', '', $class );
			$class = str_replace( 'ngl-author-pic', '', $class );
			$class = str_replace( 'ngl-author-bio-content', '', $class );
			$class = str_replace( 'ngl-author-bio', '', $class );
			$class = str_replace( 'ngl-author-name', '', $class );
			$class = str_replace( 'ngl-author-meta', '', $class );
			$class = str_replace( 'wp-block-newsletterglue-callout', '', $class );
			$class = str_replace( 'ngl-author-cta', '', $class );
			$class = str_replace( 'ngl-inline-image', '', $class );
			$class = str_replace( 'callout-img', '', $class );
			$class = str_replace( 'ngl-table-author', '', $class );
			$class = str_replace( 'ngl-table-form', '', $class );
			$class = trim( $class );
			$class = preg_replace( '!\s+!', ' ', $class );
			if ( empty( $class ) ) {
				$element->removeAttribute( 'class' );
			} else {
				$element->class = $class;
			}
		} else {
			$element->removeAttribute( 'class' );
		}
	}

	$output->save();

	return (string) $output;
}

/**
 * Add table to full width image.
 */
add_filter( 'newsletterglue_generated_html_output', 'newsletterglue_generated_html_output_hook2', 2, 3 );
function newsletterglue_generated_html_output_hook2( $html, $post_id, $app ) {

	if ( newsletterglue_get_theme_option( 'font' ) ) {
		$font = "'" . newsletterglue_get_font_name( newsletterglue_get_theme_option( 'font' ) ) . "', Arial, Helvetica, sans-serif";
	} else {
		$font = 'Arial, Helvetica, sans-serif';
	}

	$output = new simple_html_dom();
	$output->load( $html, true, false );

	$replace = '.wp-block-newsletterglue-group, .wp-block-group';
	foreach ( $output->find( $replace ) as $key => $element ) {
		foreach ( $element->find( 'img' ) as $img_key => $img_element ) {
			$img_element->class = $img_element->class . ' ng-standard-img ng-tableize-img ng-showhide-img';
		}
	}

	// Cite.
	$replace    = 'cite';
	$cite_style = 'font-weight: bold;font-size: 14px;font-style:normal;margin-top:10px;display:block;';
	foreach ( $output->find( $replace ) as $key => $element ) {
		if ( $element->style ) {
			$element->style = $element->style . $cite_style;
		} else {
			$element->style = $cite_style;
		}
	}

	// Quotes.
	$replace = 'blockquote';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$style = $element->style;
		$align = 'left';
		if ( strstr( $style, 'left' ) ) {
			$align = 'left';
		} elseif ( strstr( $style, 'right' ) ) {
			$align = 'right';
		} elseif ( strstr( $style, 'center' ) ) {
			$align = 'center';
		}
		$accent = '#eee';
		if ( newsletterglue_get_theme_option( 'a_colour' ) ) {
			$accent = newsletterglue_get_theme_option( 'a_colour' );
		}
		$output->find( $replace, $key )->outertext = '<div class="ngl-quote" style="margin-left: 0;border-' . esc_attr( $align ) . ': 3px solid ' . $accent . ';padding-' . $align . ': 20px;' . $style . '">' . $element->innertext . '</div>';
	}

	// Set td widths.
	$replace = '.ngl-table-columns';
	$total   = 0;
	foreach ( $output->find( $replace ) as $key => $element ) {
		if ( strstr( $element, 'ngl-columns' ) ) {
			continue;
		}

		$holder = 600;

		if ( strstr( $element->parent->class, 'ngl-callout-content' ) ) {
			$holder = $element->parent->{ 'data-boxed-gap' };
		}

		$col_count = 0;
		$total     = 0;
		foreach ( $element->find( 'td' ) as $a => $td ) {
			$total = $total + absint( $td->width );
			$unset = $holder - $total;
			if ( ! $td->width ) {
				$col_count = $col_count + 1;
			}
		}
		foreach ( $element->find( 'td' ) as $a => $td ) {
			if ( ! $td->width && $unset != $holder ) {
				$td->width = $unset / $col_count;
			}
			if ( ! $td->width && $unset == $holder && $col_count == 2 ) {
				$td->width = $holder / 2;
			}
		}
	}

	// Remove richtext spacers.
	$replace = 'i.ngl-tag-spacer';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$element->outertext = ' ';
	}

	// Content in merge tags.
	$replace = 'span.ngl-tag';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$element->outertext = $element->innertext;
	}

	// Force publish date for some tags.
	$replace = 'span.auto_date';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$format  = $element->{ 'data-date-format' };
		$post_id = $element->{ 'data-post-id' };
		if ( $format && $post_id ) {
			$element->outertext = date_i18n( $format, get_post_timestamp( $post_id ) );
		}
	}

	$output->save();

	return (string) $output;
}

/**
 * Adjustments.
 */
add_filter( 'newsletterglue_generated_html_output', 'newsletterglue_generated_html_output_hook3', 3, 3 );
function newsletterglue_generated_html_output_hook3( $html, $post_id, $app ) {

	$output = new simple_html_dom();
	$output->load( $html, true, false );

	$base = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'ul', 'ol', 'img' );

	$replaces = array();
	$elements = array(
		'#template_inner h1',
		'#template_inner h2',
		'#template_inner h3',
		'#template_inner h4',
		'#template_inner h5',
		'#template_inner h6',
		'#template_inner p',
		'#template_inner ol',
		'#template_inner ul',
		'#template_inner img',
		'#template_inner .wp-block-code',
		'#template_inner .ngl-quote',
		'#template_inner .wp-block-buttons',
		'#template_inner .ngl-embed-youtube',
		'#template_inner .ngl-embed-twitter',
		'#template_inner .wp-block-table > table',
		'.ngl-article-img-full',
	);

	// Group block.
	foreach ( $base as $inner ) {
		$elements[] = '.wp-block-newsletterglue-group > ' . $inner;
	}

	// Container block.
	foreach ( $base as $inner ) {
		$elements[] = '.ngl-callout-content ' . $inner;
	}

	foreach ( $elements as $el ) {
		$replaces[] = $el;
	}

	$replace = implode( ', ', $replaces );
	foreach ( $output->find( $replace ) as $key => $element ) {
		if ( ! empty( $element->parent->class ) ) {
			if ( strstr( $element->parent->class, 'ng-block-td' ) ) {
				continue;
			}
		}
		if ( ! empty( $element->parent->parent->class ) ) {
			if ( strstr( $element->parent->parent->class, 'ng-block-td' ) ) {
				continue;
			}
			if ( strstr( $element->parent->parent->class, 'ng-div' ) ) {
				continue;
			}
		}
		if ( ! empty( $element->parent->parent->parent->class ) ) {
			if ( strstr( $element->parent->parent->parent->class, 'ng-block' ) ) {
				continue;
			}
		}
		if ( ! empty( $element->parent->parent->parent->parent->class ) ) {
			if ( strstr( $element->parent->parent->parent->parent->class, 'ng-block' ) ) {
				continue;
			}
		}
		if ( ! empty( $element->parent->parent->parent->parent->parent->class ) ) {
			if ( strstr( $element->parent->parent->parent->parent->parent->class, 'ng-block' ) ) {
				continue;
			}
		}
		if ( ! empty( $element->parent->parent->parent->parent->parent->parent->class ) ) {
			if ( strstr( $element->parent->parent->parent->parent->parent->parent->class, 'ng-block' ) ) {
				continue;
			}
		}
		if ( strstr( $element->class, 'ngl-ignore-mrkp' ) ) {
			continue;
		}
		if ( strstr( $element->parent->class, 'ngl-sound' ) ) {
			continue;
		}
		if ( strstr( $element->parent->class, 'ngl-lp-content' ) ) {
			continue;
		}
		if ( strstr( $element->parent->class, 'ngl-ignore-mrkp' ) ) {
			continue;
		}
		$td_align = '';

		if ( strstr( $element->innertext, 'mso]' ) || strstr( $element->innertext, 'endif]' ) ) {
			if ( ! strstr( $element->class, 'wp-block-button' ) ) {
				$element->outertext = $element->innertext;
				continue;
			}
		}
		if ( $element->parent->class === 'ng__column' ) {
			continue;
		}
		if ( strstr( $element->parent->class, 'ng-block' ) ) {
			continue;
		}
		$class = ! empty( $element->class ) ? ' ngl-table-' . $element->class . ' ' . $element->class : '';
		if ( $element->tag == 'img' ) {
			if ( $element->{ 'data-align' } ) {
				$align = $element->{ 'data-align' };
				if ( $align == 'center' ) {
					$element->style = $element->style . 'margin: auto !important;';
					$td_align       = 'align="center"';
				}
				if ( $align == 'right' ) {
					$element->style = $element->style . 'margin-left: auto !important;';
					$td_align       = 'align="right"';
				}
			}
			if ( ( strstr( $element->class, 'ngl-' ) || strstr( $element->class, 'postembed-image' ) ) && ! strstr( $element->class, 'ngl-keep-width' ) ) {
				continue;
			}
			if ( $element->parent->parent->tag === 'td' && ! in_array( $element->class, array( 'logo-image', 'masthead' ) ) ) {
				if ( ! strstr( $element->parent->parent->class, 'ngl-callout-content' ) ) {
					if ( ! strstr( $element->class, 'ng-tableize-img' ) ) {
						continue;
					}
				}
			}
			if ( $element->parent->tag == 'td' && $element->parent->id != 'template_inner' ) {
				if ( $element->parent->class !== 'ngl-callout-content' ) {
					if ( $element->parent->class != 'column' ) {
						continue;
					}
				}
			}
		}
		$element->outertext = '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="ngl-table ngl-table-' . $element->tag . $class . '"><tr><td ' . $td_align . '>' . $element->outertext . '</td></tr></table>';
	}

	$output->save();

	return (string) $output;
}

/**
 * Fix image widths.
 */
add_filter( 'newsletterglue_generated_html_output', 'newsletterglue_generated_html_output_hook4', 4, 3 );
function newsletterglue_generated_html_output_hook4( $html, $post_id, $app ) {

	$output = new simple_html_dom();
	$output->load( $html, true, false );

	$replace = '#template_inner img';
	foreach ( $output->find( $replace ) as $key => $element ) {
		if ( strstr( $element->class, 'ng-image' ) ) {
			continue;
		}

		if ( strstr( $element->class, 'callout-img' ) ) {
			continue;
		}

		if ( strstr( $element->class, 'wp-image' ) ) {
			continue;
		}

		if ( $element->parent()->parent()->tag && $element->parent()->parent()->tag == 'td' ) {
			$td          = $element->parent()->parent();
			$threshold   = strstr( $element->class, 'callout-img' ) ? 528 : 600;
			$threshold   = strstr( $element->class, 'embed-thumb-' ) ? $threshold - 2 : $threshold;
			$image_width = newsletterglue_get_image_width_by_td( $td, $threshold );
			if ( ! strstr( $td->class, 'ngl-td-' ) && ! strstr( $element->class, 'ngl-' ) && $image_width ) {
				if ( $element->width && $element->width < 500 ) {
					continue;
				}
				if ( $element->class && strstr( $element->class, 'ngl-core-image' ) ) {
					continue;
				}
				$element->width = floor( $image_width );
				$element->removeAttribute( 'height' );
			}
		}

		if ( $element->parent()->tag && $element->parent()->tag == 'td' ) {
			$td          = $element->parent();
			$threshold   = strstr( $element->class, 'callout-img' ) ? 528 : 600;
			$threshold   = strstr( $element->class, 'embed-thumb-' ) ? $threshold - 2 : $threshold;
			$image_width = newsletterglue_get_image_width_by_td( $td, $threshold );
			if ( ! strstr( $td->class, 'ngl-td-' ) && ! strstr( $element->class, 'ngl-' ) && $image_width ) {
				if ( $element->width && $element->width == 1 ) {
					$element->style = $element->style . 'display: inline !important;';
					continue;
				}
				$element->width = floor( $image_width );
				$element->removeAttribute( 'height' );
			}
		}
	}

	// ngl-callout-content images.
	$replace = '.ngl-callout-content';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$max_s = $element->{ 'data-boxed-gap' };
		if ( $max_s ) {
			foreach ( $element->find( 'img' ) as $img_i => $img_el ) {
				if ( $img_el->width > $max_s ) {
					if ( strstr( $img_el->class, 'embed-thumb-youtube' ) ) {
						$max_s         = $max_s - 2;
						$img_el->width = $max_s;
					}
				} else {
					$img_el->{ 'data-forcew' } = $img_el->width;
				}
			}
		}
	}

	$output->save();

	return (string) $output;
}

/**
 * Get cached image sizes.
 */
function newsletterglue_cached_image_size( $source ) {
	$caches = get_option( 'newsletterglue_image_sizes' );
	$hash   = md5( $source );
	if ( ! empty( $caches ) && isset( $caches[ $hash ] ) ) {
		return $caches[ $hash ];
	}
	return false;
}

/**
 * Set cached image sizes.
 */
function newsletterglue_cached_image_size_set( $source, $image ) {
	$caches          = get_option( 'newsletterglue_image_sizes' );
	$hash            = md5( $source );
	$caches[ $hash ] = $image;
	update_option( 'newsletterglue_image_sizes', $caches );
}

/**
 * Set font-family per td.
 */
add_filter( 'newsletterglue_generated_html_output', 'newsletterglue_generated_html_output_hook5', 5, 3 );
function newsletterglue_generated_html_output_hook5( $html, $post_id, $app ) {

	if ( newsletterglue_get_theme_option( 'font' ) ) {
		$email_font = "'" . newsletterglue_get_font_name( newsletterglue_get_theme_option( 'font' ) ) . "'";
	} else {
		$email_font = 'Arial, Helvetica, sans-serif';
	}

	$email_font = esc_attr( $email_font );

	$output = new simple_html_dom();
	$output->load( $html, true, false );

	// v3
	$replace = '#template_inner, #template_inner td, #template_end, #template_end td';
	foreach ( $output->find( $replace ) as $key => $element ) {
		if ( strstr( $element->class, 'ng-block-td' ) ) {
			continue;
		}
		if ( trim( $element->innertext ) == '' ) {
			continue;
		}
		if ( $element->{ 'data-font' } ) {
			$email_font = newsletterglue_get_font_name( $element->{ 'data-font' } );
			foreach ( $element->find( '*' ) as $sub_e => $sub_el ) {
				if ( $sub_el->style ) {
					$sub_el->style = rtrim( $sub_el->style, ';' ) . ";font-family: $email_font;";
				} else {
					$sub_el->style = "font-family: $email_font;";
				}
			}
		}
		if ( $element->style ) {
			$element->style = rtrim( $element->style, ';' ) . ";font-family: $email_font;";
		} else {
			$element->style = "font-family: $email_font;";
		}
	}

	// Image width/height.
	if ( function_exists( 'getimagesize' ) ) {
		$replace = '#template_inner img.wp-image';
		foreach ( $output->find( $replace ) as $key => $element ) {
			if ( strstr( $element->class, 'ng-image' ) ) {
				continue;
			}
			if ( ! empty( $element->src ) ) {
				$image = newsletterglue_cached_image_size( $element->src ) ? newsletterglue_cached_image_size( $element->src ) : getimagesize( $element->src );
				if ( ! empty( $image[0] ) ) {
					if ( strstr( $element->class, 'ng-showhide-img' ) && $element->width ) {
						$element->width = $element->width - 40;
					}
					if ( $image[0] && ! $element->width ) {
						if ( strstr( $element->class, 'ng-showhide-img' ) ) {
							$has_gap  = true;
							$image[0] = $image[0] < 600 ? $image[0] : $image[0] - 40;
						} else {
							$has_gap = false;
						}
						$element->width  = $image[0];
						$element->height = $image[1];
						if ( $has_gap && $image[0] > 560 ) {
							$element->width = 560;
						}
					} elseif ( $image[0] && $element->width ) {
						if ( $image[0] < $element->width ) {
							$element->width = $image[0];
						}
					}
					newsletterglue_cached_image_size_set( $element->src, $image );
				}
			}
		}

		$replace = '#template_inner img.logo-image';
		foreach ( $output->find( $replace ) as $key => $element ) {
			if ( ! empty( $element->src ) ) {
				$image = newsletterglue_cached_image_size( $element->src ) ? newsletterglue_cached_image_size( $element->src ) : getimagesize( $element->src );
				if ( ! empty( $image[0] ) ) {
					$element->width  = $image[0];
					$element->height = $image[1];
					newsletterglue_cached_image_size_set( $element->src, $image );
				}
			}
		}
	}

	// Logo wrapper.
	$replace = 'div.ngl-logo';
	foreach ( $output->find( $replace ) as $key => $element ) {
		if ( strstr( $element->class, 'right' ) ) {
			$element->find( 'td', 0 )->align = 'right';
		} elseif ( strstr( $element->class, 'left' ) ) {
			$element->find( 'td', 0 )->align = 'left';
		} else {
			$element->find( 'td', 0 )->align = 'center';
		}
		$element->outertext = $element->innertext;
	}

	// Featured wrapper.
	$replace = 'div.ngl-masthead';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$element->outertext = $element->innertext;
	}

	// Meta data.
	$replace = 'div.ngl-metadata';
	foreach ( $output->find( $replace ) as $key => $element ) {
		if ( $element->find( 'table.ngl-table-metadata td', 0 ) ) {
			$s = $element->style;
			if ( $element->find( 'table.ngl-table-metadata td', 0 )->style ) {
				$element->find( 'table.ngl-table-metadata td', 0 )->style = $element->find( 'table.ngl-table-metadata td', 0 )->style . $s;
			} else {
				$element->find( 'table.ngl-table-metadata td', 0 )->style = $s;
			}
			$element->outertext = $element->innertext;
		}
	}

	// Author.
	$replace = 'div.ngl-author';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$element->outertext = $element->innertext;
	}

	// Has background.
	$replace = 'p.has-background, a.has-background';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$td = $element->parent;
		if ( $element->style ) {
			$s       = $element->style;
			$results = array();
			$styles  = explode( ';', $s );

			foreach ( $styles as $style ) {
				$properties = explode( ':', $style );
				if ( 2 === count( $properties ) ) {
					$results[ trim( $properties[0] ) ] = trim( $properties[1] );
				}
			}
			if ( ! empty( $results['background-color'] ) ) {
				if ( $td->tag == 'td' ) {
					$td->style = $td->style . 'background-color:' . $results['background-color'] . ';';
				}
			}
		}
		$td->style = $td->style . 'padding: 20px;';
	}

	// NG-latest-posts-wrapper.
	$replace = 'table.ng-posts-wrapper';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$font = $element->{ 'ng-font' };
		if ( ! empty( $font ) ) {
			foreach ( $element->find( 'td' ) as $td_key => $td_el ) {
				if ( $td_el->style ) {
					$td_el->style = $td_el->style . ';font-family: ' . esc_attr( $font ) . ';';
				} else {
					$td_el->style = 'font-family: ' . esc_attr( $font ) . ';';
				}
			}
		}
	}

	// Apply correct font to paragraphs.
	$replace = 'td.ng-block-td p';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$parent_font = newsletterglue_get_style_prop( $element->parent()->style, 'font-family' );
		$child_font  = newsletterglue_get_style_prop( $element->style, 'font-family' );
		if ( $child_font != $parent_font ) {
			$element->style = rtrim( $element->style, ';' ) . ';font-family: ' . $parent_font;
		}

		$parent_font_size = newsletterglue_get_style_prop( $element->parent()->style, 'font-size' );
		$child_font_size  = newsletterglue_get_style_prop( $element->style, 'font-size' );
		if ( $child_font_size != $parent_font_size ) {
			$element->style = rtrim( $element->style, ';' ) . ';font-size: ' . $parent_font_size;
		}
	}

	$output->save();

	return (string) $output;
}

/**
 * Images.
 */
add_filter( 'newsletterglue_generated_html_output', 'newsletterglue_generated_html_output_hook6', 6, 3 );
function newsletterglue_generated_html_output_hook6( $html, $post_id, $app ) {

	$output = new simple_html_dom();
	$output->load( $html, true, false );

	// Set width/height.
	$replace = '#template_inner img[data-w]';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$element->width  = $element->{ 'data-w' };
		$element->height = $element->{ 'data-h' };
		$element->removeAttribute( 'data-w' );
		$element->removeAttribute( 'data-h' );
	}

	// Images.
	$replace = '#template_inner img';
	foreach ( $output->find( $replace ) as $key => $element ) {
		if ( strstr( $element->class, 'ng-image' ) ) {
			continue;
		}
		if ( $element->width ) {
			$max_width = $element->width . 'px';
			if ( $element->width >= 600 ) {
				$element->width = 560;
				$max_width      = '100%';
				$element->removeAttribute( 'height' );
			}
			if ( $element->style ) {
				$style          = rtrim( $element->style, ';' ) . ';';
				$element->style = $style . 'max-width: ' . $max_width;
			} else {
				$element->style = 'max-width: ' . $max_width;
			}
		}
	}

	// Images inside columns td.
	$replace = '#template_inner .ngl-table-columns td';
	foreach ( $output->find( $replace ) as $key => $element ) {
		if ( $element->width && is_numeric( $element->width ) && ! strstr( $element->class, 'ngl-td-auto' ) ) {
			$max_w = $element->width - 40;
			foreach ( $element->find( 'img' ) as $image_id => $el ) {
				if ( $el->width && $el->width > $max_w ) {
					if ( $max_w <= 0 ) {
						$max_w = 20;
					}
					$el->style = $el->style . 'max-width: ' . $max_w . 'px;';
					$el->width = $max_w;
					$el->removeAttribute( 'height' );
				}
			}
		}
	}

	// Empty divs = brs.
	$replace = '#template_inner div';
	foreach ( $output->find( $replace ) as $key => $element ) {
		if ( $element->innertext == '' ) {
			$element->outertext = '<br /><br />';
		}
	}

	// Find all images that could have links.
	$replace = '#template_inner img[data-href]';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$element->outertext = '<a href="' . $element->{'data-href'} . '">' . $element->outertext . '</a>';
	}

	// Figcaption.
	$replace = '#template_inner figcaption';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$element->outertext = '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="ngl-table ngl-table-caption"><tr><td>' . $element->innertext . '</td></tr></table>';
	}

	// Fix font family for latest posts.
	$replace = '.ngl-lp-content p';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$font = newsletterglue_get_style_prop( $element->parent()->style, 'font-family' );
		if ( ! empty( $font ) ) {
			$element->style = rtrim( $element->style, ';' ) . ';font-family: ' . esc_attr( $font ) . ' !important;';
		}
	}

	// Tables containing inline elements.
	$replace = '.ngl-table-inline';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$align = 'left';
		if ( strstr( $element->class, '-center' ) ) {
			$align = 'center';
		} elseif ( strstr( $element->class, '-right' ) ) {
			$align = 'right';
		}

		if ( $element->find( '.ngl-share-description' ) ) {
			$saved_text = $element->find( '.ngl-share-description', 0 )->outertext;
			$element->find( '.ngl-share-description', 0 )->outertext = '';
		} else {
			$saved_text = '';
		}

		$inner_html = strip_tags( $element->innertext, '<a><img>' ); // phpcs:ignore
		$inner_html = str_replace( '<a', '<td><a', $inner_html );
		$inner_html = str_replace( '</a>', '</a></td>', $inner_html );
		$inner_html = '<table border="0" cellpadding="0" cellspacing="0" align="' . $align . '"><tr>' . $inner_html . '</tr></table>';

		if ( $saved_text ) {
			$saved_text = '<table width="100%" border="0" cellpadding="0" cellspacing="0" align="' . $align . '"><tr><td valign="middle" align="' . $align . '" style="text-align: ' . $align . ';padding-bottom: 10px !important;">' . $saved_text . '</td></tr></table>';
		}

		$padding = '';
		if ( $element->{ 'data-padding' } ) {
			$padding = 'padding: ' . $element->{ 'data-padding' } . ' !important;';
		}

		$element->outertext = '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="ngl-table ' . str_replace( 'undefined', '', $element->class ) . ' align-' . $align . '"><tr><td valign="middle" align="' . $align . '" style="' . $padding . '">' . $saved_text . $inner_html . '</td></tr></table>';

	}

	// Alignment.
	$replace = 'table.wp-image-center td';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$element->align = 'center';
	}

	$replace = 'table.wp-image-left td';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$element->align = 'left';
	}

	$replace = 'table.wp-image-right td';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$element->align = 'right';
	}

	/* Separator */
	$replace = '.wp-block-separator';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$hr_bg   = '#dddddd';
		$height  = 1;
		$width   = '100%';
		$classes = $element->class;
		$results = array();
		$styles  = explode( ';', $element->style );
		foreach ( $styles as $style ) {
			$properties = explode( ':', $style );
			if ( 2 === count( $properties ) ) {
				$results[ trim( $properties[0] ) ] = trim( $properties[1] );
			}
		}

		if ( isset( $results['color'] ) ) {
			$hr_bg = $results['color'];
		}

		if ( strstr( $classes, 'thick' ) ) {
			$height = 3;
		}

		if ( strstr( $classes, 'is-short' ) ) {
			$width = 60;
		}

		if ( strstr( $classes, 'thickness-' ) ) {
			$array = explode( ' ', $classes );
			foreach ( $array as $a => $b ) {
				if ( strstr( $b, 'thickness-' ) ) {
					$h = str_replace( 'thickness-', '', $b );
					break;
				}
			}
			if ( isset( $h ) ) {
				$height = $h;
			}
		}

		$width  = apply_filters( 'newsletterglue_divider_global_width', $width );
		$height = apply_filters( 'newsletterglue_divider_global_height', $height );

		$align = 'left';
		if ( strstr( $classes, 'aligncenter' ) ) {
			$align = 'center';
		}
		if ( strstr( $classes, 'alinright' ) ) {
			$align = 'right';
		}
		if ( strstr( $classes, 'alignleft' ) ) {
			$align = 'left';
		}

		if ( strstr( $classes, 'has-text-color' ) && strstr( $classes, '-background-color' ) ) {
			$colors = get_option( 'newsletterglue_theme_colors' );
			if ( ! empty( $colors ) ) {
				foreach ( $colors as $key => $color ) {
					$slug  = $color->slug;
					$color = $color->color;
					if ( strstr( $classes, 'has-' . $slug . '-color' ) ) {
						$hr_bg = $color;
					}
				}
			}
		}

		$block_classes = str_replace( array( 'wp-block-separator', 'has-text-color', 'has-background', 'is-style-twentytwentyone-separator-thick' ), '', $classes );
		$block_classes = trim( $block_classes );

		if ( $align == 'center' ) {
			$margin_for_align = 'auto';
		} else {
			$margin_for_align = '0';
		}

		$element->outertext = '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="ngl-table"><tr><td>
			<table width="' . $width . '" style="margin:' . $margin_for_align . ';" border="0" cellpadding="0" cellspacing="0" class="ngl-table"><tr><td align="' . $align . '" width="' . $width . '" class="' . $block_classes . '" style="background: ' . $hr_bg . ';padding: 0 !important;height: ' . $height . 'px;"></td></tr></table>
		</td></tr></table>';
	}

	// Kill permalink arrows.
	$replace = 'img.ngl-metadata-permalink-arrow';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$element->outertext = '';
	}

	// Let's kill the embed social wrappers.
	$replace = '.ngl-embed-social-div.ngl-embed-spotify';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$element->outertext = $element->innertext;
	}

	// Custom css classes for images.
	$replace = '.wp-block-image';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$class                            = str_replace( 'wp-block-image', '', $element->class );
		$element->find( 'img', 0 )->class = $element->find( 'img', 0 )->class . ' ' . trim( $class );
	}

	$output->save();

	return (string) $output;
}

/**
 * Fix wrong td widths.
 */
add_filter( 'newsletterglue_generated_html_output', 'newsletterglue_generated_html_output_hook7', 6, 3 );
function newsletterglue_generated_html_output_hook7( $html, $post_id, $app ) {

	$output = new simple_html_dom();
	$output->load( $html, true, false );

	$replace = '#template_inner > .ngl-table-columns .root-tr';
	$count   = 0;
	$width   = 0;
	foreach ( $output->find( $replace ) as $key => $element ) {
		foreach ( $element->find( 'td' ) as $a => $b ) {
			if ( $b->parent()->tag == 'tr' && strstr( $b->parent()->class, 'root-tr' ) ) {
				$count = $count + 1;
				if ( $b->width ) {
					$width = $width + $b->width;
				}
			}
		}
		if ( $width < 600 ) {
			$new_width = 600 / $count;
			foreach ( $element->find( 'td' ) as $a => $b ) {
				if ( $b->parent()->tag == 'tr' && strstr( $b->parent()->class, 'root-tr' ) ) {
					$b->width = $new_width;
				}
			}
		}
	}

	$replace = '#template_inner img.ngl-keep-width';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$w = '';
		if ( $element->width ) {
			$w = $element->width;
		}
		if ( ! empty( $w ) ) {
			$style          = rtrim( $element->style, ';' ) . ';';
			$element->style = $style . 'width: ' . $w . 'px !important;';
		}
	}

	// Modify images inside a callout container - postembed block.
	$replace = '#template_inner .ngl-callout-content img.postembed-image';
	foreach ( $output->find( $replace ) as $key => $element ) {
		if ( $element->{ 'data-base-w' } ) {
			$base_w = $element->{ 'data-base-w' };
			$sub    = $element->{ 'data-sub' };
			$pct    = $element->{ 'data-pct' };
			if ( $pct ) {
				$img_size = ( ( $pct / 100 ) * $base_w ) - 10 - $sub;
			} else {
				$img_size = $base_w - $sub;
			}
			$element->width = $img_size;
		}
	}

	// Fix class inside image.
	$replace = '.wp-block-image';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$class = $element->class;
		if ( ! empty( $class ) ) {
			$element->find( 'img', 0 )->class = $element->find( 'img', 0 )->class . ' ' . $class;
		}
	}

	$replace = 'td.column';
	$i       = 0;
	foreach ( $output->find( $replace ) as $key => $element ) {
		$i = 0;
		if ( $element->{ 'padding-left' } ) {
			$left           = absint( $element->{ 'padding-left' } );
			$element->style = $element->style . 'padding-left: ' . $left . 'px;';
			$i              = $i + $left;
		}
		if ( $element->{ 'padding-right' } ) {
			$right          = absint( $element->{ 'padding-right' } );
			$element->style = $element->style . 'padding-right: ' . $right . 'px;';
			$i              = $i + $right;
		}
		if ( $element->{ 'padding-top' } ) {
			$top            = absint( $element->{ 'padding-top' } );
			$element->style = $element->style . 'padding-top: ' . $top . 'px;';
		}
		if ( $element->{ 'padding-bottom' } ) {
			$bottom         = absint( $element->{ 'padding-bottom' } );
			$element->style = $element->style . 'padding-bottom: ' . $bottom . 'px !important;';
		}
		if ( $i ) {
			$element->width = $element->width - $i;
		}
		$element->removeAttribute( 'padding-left' );
		$element->removeAttribute( 'padding-right' );
		$element->removeAttribute( 'padding-top' );
		$element->removeAttribute( 'padding-bottom' );
	}

	$output->save();

	return (string) $output;
}

/**
 * Generic code.
 */
add_filter( 'newsletterglue_generated_html_output', 'newsletterglue_generated_html_output_hook8', 8, 3 );
function newsletterglue_generated_html_output_hook8( $html, $post_id, $app ) {

	$output = new simple_html_dom();
	$output->load( $html, true, false );

	$replace = '.ngl-lp-content';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$width = $element->{ 'img-w' };
		foreach ( $element->find( 'img' ) as $img => $img_el ) {
			if ( $img_el->style ) {
				$results = array();
				$props   = array();
				$styles  = explode( ';', $img_el->style );
				foreach ( $styles as $style ) {
					$props = explode( ':', $style );
					if ( 2 === count( $props ) ) {
						$results[ trim( $props[0] ) ] = trim( $props[1] );
					}
				}
				if ( isset( $results['width'] ) ) {
					$width_n = str_replace( 'px', '', $results['width'] );
					if ( $width_n < $width ) {
						$width = $width_n;
					}
				}
			}
			$img_el->style  = "width: {$width}px;max-width: {$width}px;margin-left: auto;margin-right: auto;margin-bottom: 10px !important;display: block;";
			$img_el->width  = $width;
			$img_el->height = 'auto';
		}
	}

	// Fix user error unsubscribe links.
	$replace = '#template_inner a';
	foreach ( $output->find( $replace ) as $key => $element ) {
		if ( ! empty( $element->href ) ) {
			if ( strstr( $element->href, 'unsubscribe_link' ) ) {
				$element->href = '{{ unsubscribe_link }}';
			}
		}
	}

	$replace = '#template_inner > div';
	foreach ( $output->find( $replace ) as $key => $element ) {
		if ( ! empty( $element->class ) && strstr( $element->class, 'wp-block-site-logo' ) ) {
			continue;
		}
		if ( $element->parent->id !== 'template_inner' ) {
			continue;
		}
		if ( $element->class && $element->class === 'ngl-preview-text' ) {
			continue;
		}
		if ( $element->class && strstr( $element->class, 'ng-block' ) ) {
			continue;
		}
		$class = ! empty( $element->class ) ? ' ngl-table-' . $element->class : '';

		$conditions = '';
		if ( $conditions = $element->{ 'data-conditions' } ) {
			$element->outertext = $element->innertext;
			$conditions         = "data-conditions='$conditions'";
		}

		$element->outertext = '<table ' . $conditions . ' width="100%" border="0" cellpadding="0" cellspacing="0" class="ngl-table ngl-table-' . $element->tag . $class . '"><tr><td align="left">' . $element->outertext . '</td></tr></table>';
	}

	// Add common class to all images.
	$replace = '#template_inner img';
	foreach ( $output->find( $replace ) as $key => $element ) {
		if ( strstr( $element->class, 'ng-image' ) ) {
			continue;
		}
		if ( strstr( $element->class, 'ngl-inline-image' ) ) {
			if ( $element->width && $element->width <= 50 ) {
				continue;
			}
		}
		if ( empty( $element->{ 'data-forcew' } ) ) {
			$element->class = $element->class . ' ng-standard-img';
		} else {
			$forcew = $element->{ 'data-forcew' };
			if ( $forcew > 400 ) {
				$element->class = $element->class . ' ng-standard-img';
			}
		}
	}

	$output->save();

	return (string) $output;
}

/**
 * After all emogrify is done.
 */
add_filter( 'newsletterglue_final_html_content', 'newsletterglue_final_html_content', 10, 1 );
function newsletterglue_final_html_content( $html ) {

	if ( newsletterglue_get_theme_option( 'font' ) ) {
		$font = "'" . newsletterglue_get_font_name( newsletterglue_get_theme_option( 'font' ) ) . "', Arial, Helvetica, sans-serif";
	} else {
		$font = 'Arial, Helvetica, sans-serif';
	}

	$output = new simple_html_dom();
	$output->load( $html, true, false );

	// Image.
	$replace = '#template_inner img';
	foreach ( $output->find( $replace ) as $key => $element ) {
		if ( strstr( $element->class, 'ng-image' ) ) {
			continue;
		}
		$s = $element->style;
		if ( $s ) {
			$results = array();
			$styles  = explode( ';', $s );
			foreach ( $styles as $style ) {
				$properties = explode( ':', $style );
				if ( 2 === count( $properties ) ) {
					$results[ trim( $properties[0] ) ] = trim( $properties[1] );
				}
			}
			if ( $results ) {
				if ( isset( $results['max-width'] ) && strstr( $results['max-width'], 'px' ) ) {
					$number = absint( $results['max-width'] );
					if ( $number == $element->width ) {
						$style          = rtrim( $element->style, ';' ) . ';';
						$element->style = $style . 'width: ' . $results['max-width'] . ';';
					}
				}
			}
		}
	}

	// Featured images in post embed.
	$img = '.ngl-article-featured img';
	foreach ( $output->find( $img ) as $a => $b ) {
		if ( absint( $b->width ) > 0 ) {
			$b->style = str_replace( 'max-width: 100%', 'max-width: ' . $b->width . 'px', $b->style );
			$b->style = str_replace( 'width: 100%', 'width: ' . $b->width . 'px', $b->style );
		}
	}

	// Outlook rect for button.
	$replace = 'a.wp-block-button__link, a.ng-block-button__link';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$s       = $element->style;
		$results = array();
		$styles  = explode( ';', $s );

		foreach ( $styles as $style ) {
			$properties = explode( ':', $style );
			if ( 2 === count( $properties ) ) {
				$results[ trim( $properties[0] ) ] = trim( $properties[1] );
			}
		}

		$color      = ! empty( $results['color'] ) ? $results['color'] : 'inherit';
		$background = ! empty( $results['background-color'] ) ? $results['background-color'] : '#0088A0';
		$font_size  = newsletterglue_get_theme_option( 'p_size' ) . 'px';
		$innertext  = wp_strip_all_tags( $element->innertext );
		$href       = $element->href;
		$length     = ( mb_strlen( $innertext ) * 10 ) + 25;
		$radius     = ! empty( $results['border-radius'] ) ? $results['border-radius'] : '0px';
		$radius     = str_replace( 'px', '', $radius );
		$radius     = $radius * 2 . '%';

		if ( strstr( $element->class, 'is-style-outlined' ) ) {
			if ( $element->style ) {
				$element->style = $element->style . 'color: ' . $background . ' !important;';
			}
		}

		if ( strstr( $element->class, 'is-style-outlined' ) ) {

			$element->innertext = '<span style="color: ' . $color . ';">' . $element->innertext . '</span>';
			$element->outertext = '<!--[if mso]>
									<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="' . $href . '" style="v-text-anchor:middle; width: ' . $length . 'px; height:49px; " arcsize="' . $radius . '" strokecolor="' . $color . '" strokeweight="1pt" fillcolor="' . $background . '" o:button="true" o:allowincell="true" o:allowoverlap="false">
									<v:textbox inset="2px,2px,2px,2px"><center style="font-family: ' . $font . '; color: ' . $color . '; font-size: ' . $font_size . '; line-height: 1.1;mso-color-alt: auto;">' . $innertext . '</center></v:textbox>
									</v:roundrect>
									<![endif]-->' . $element->outertext;
		} else {

			$element->innertext = '<span style="color: ' . $color . ';">' . $element->innertext . '</span>';
			$element->outertext = '<!--[if mso]>
									<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="' . $href . '" style="v-text-anchor:middle; width: ' . $length . 'px; height:49px; " arcsize="' . $radius . '" strokecolor="' . $background . '" strokeweight="0pt" fillcolor="' . $background . '" o:button="true" o:allowincell="true" o:allowoverlap="false">
									<v:textbox inset="2px,2px,2px,2px"><center style="font-family: ' . $font . '; color: ' . $color . '; font-size: ' . $font_size . '; line-height: 1.1;mso-color-alt: auto;">' . $innertext . '</center></v:textbox>
									</v:roundrect>
									<![endif]-->' . $element->outertext;
		}
	}

	// Remove extra padding.
	$replace = 'td.ngl-callout-content table tr td';
	$ret     = $output->find( $replace, -1 );
	if ( $ret && $ret->style ) {
		$ret->style = str_replace( '0 0 10px;', '0;', $ret->style );
	}

	// Author bio button.
	$replace = 'div.ngl-author-cta a';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$s       = $element->style;
		$results = array();
		$styles  = explode( ';', $s );

		foreach ( $styles as $style ) {
			$properties = explode( ':', $style );
			if ( 2 === count( $properties ) ) {
				$results[ trim( $properties[0] ) ] = trim( $properties[1] );
			}
		}

		$color      = ! empty( $results['color'] ) ? $results['color'] : '#ffffff';
		$background = ! empty( $results['background-color'] ) ? $results['background-color'] : '#0088A0';
		$font_size  = '12px';
		$innertext  = wp_strip_all_tags( $element->innertext );
		$href       = $element->href;
		$length     = ( mb_strlen( $innertext ) * 10 ) + 25;
		$radius     = ! empty( $results['border-radius'] ) ? $results['border-radius'] : '0px';
		$radius     = str_replace( 'px', '', $radius );
		$radius     = $radius * 2 . '%';

		if ( ! empty( $results['border-width'] ) && $results['border-width'] == '2px' ) {

			if ( isset( $results['border-color'] ) ) {
				$strokecolor = $results['border-color'];
			} else {
				$strokecolor = $color;
			}

			$element->innertext = '<span style="font-family: ' . $font . '; color: ' . $color . '; font-size: ' . $font_size . ';">' . $element->innertext . '</span>';
			$element->outertext = '<!--[if mso]>
										<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="' . $href . '" style="v-text-anchor:middle; width: ' . $length . 'px; height:30px; " arcsize="' . $radius . '" strokecolor="' . $strokecolor . '" strokeweight="1pt" fillcolor="' . $background . '" o:button="true" o:allowincell="true" o:allowoverlap="false">
										<v:textbox inset="2px,2px,2px,2px"><center style="font-family: ' . $font . '; color: ' . $color . '; font-size: ' . $font_size . '; line-height: 1.1;mso-color-alt: auto;">' . $innertext . '</center></v:textbox>
										</v:roundrect>
										<![endif]-->' . $element->outertext;
		} else {

			$element->innertext = '<span style="font-family: ' . $font . '; color: ' . $color . '; font-size: ' . $font_size . ';">' . $element->innertext . '</span>';
			$element->outertext = '<!--[if mso]>
										<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="' . $href . '" style="v-text-anchor:middle; width: ' . $length . 'px; height:30px; " arcsize="' . $radius . '" strokecolor="' . $background . '" strokeweight="0pt" fillcolor="' . $background . '" o:button="true" o:allowincell="true" o:allowoverlap="false">
										<v:textbox inset="2px,2px,2px,2px"><center style="font-family: ' . $font . '; color: ' . $color . '; font-size: ' . $font_size . '; line-height: 1.1;mso-color-alt: auto;">' . $innertext . '</center></v:textbox>
										</v:roundrect>
										<![endif]-->' . $element->outertext;
		}
	}

	// Fix weird markup.
	$replace = '.wp-block-image table figure table';
	foreach ( $output->find( $replace ) as $key => $element ) {
		if ( $element->find( 'img' ) ) {
			$theimage           = $element->find( 'img', 0 )->outertext;
			$element->outertext = '';
			if ( $element->parent()->find( 'a' ) ) {
				$element->parent()->find( 'a', 0 )->innertext = $theimage;
			} else {
				$element->parent()->innertext = $theimage;
			}
		}
	}

	// Inline colored links.
	$replace = 'span.has-inline-color';
	foreach ( $output->find( $replace ) as $key => $element ) {
		if ( $element->style ) {
			$s       = $element->style;
			$results = array();
			$styles  = explode( ';', $s );

			foreach ( $styles as $style ) {
				$properties = explode( ':', $style );
				if ( 2 === count( $properties ) ) {
					$results[ trim( $properties[0] ) ] = trim( $properties[1] );
				}
			}
			if ( ! empty( $results['color'] ) ) {
				if ( $element->parent->tag == 'a' ) {
					$element->parent->style = rtrim( $element->parent->style, ';' ) . '; color: ' . $results['color'] . ' !important;';
				}
			}
		}
	}

	// Inline colored links.
	$replace = 'mark';
	foreach ( $output->find( $replace ) as $key => $element ) {
		if ( $element->style ) {
			$s       = $element->style;
			$results = array();
			$styles  = explode( ';', $s );

			foreach ( $styles as $style ) {
				$properties = explode( ':', $style );
				if ( 2 === count( $properties ) ) {
					$results[ trim( $properties[0] ) ] = trim( $properties[1] );
				}
			}
			if ( ! empty( $results['color'] ) ) {
				if ( $element->find( 'a', 0 ) ) {
					$element->find( 'a', 0 )->style = rtrim( $element->find( 'a', 0 )->style, ';' ) . '; color: ' . $results['color'] . ' !important;';
				}
				if ( $element->parent->tag == 'a' ) {
					$element->parent->style = rtrim( $element->parent->style, ';' ) . '; color: ' . $results['color'] . ' !important;';
				}
			}
		}
	}

	// Auto.
	$replace = '.ngl-lp-cta';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$element->removeAttribute( 'style' );
	}

	// Unsubscribe link fix for MailerLite.
	$replace = 'a[href={$unsubscribe}]';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$element->style = 'text-decoration: underline';
		if ( $element->parent->class && strstr( $element->parent->class, 'ngl-unsubscribe' ) ) {
			$style = 'color: #707070; text-decoration: underline;';
		} else {
			$style = '';
		}
		$element->outertext = '<a href="{$unsubscribe}" style="' . esc_attr( $style ) . '">' . strip_tags( $element->innertext ) . '</a>'; // phpcs:ignore
	}

	// Remove unwanted class junk.
	$replace = 'table.ngl-table, #template_inner img, table.ngl-table-callout, table.wp-block-newsletterglue-callout, td.ngl-callout-content, a.ngl-metadata-permalink, .wp-block-button__link, div.wp-block-buttons, .wp-block-button, h1.title, p.has-drop-cap, p.has-text-color, a.logo, tr.root-tr, .has-inline-color, .has-text-align-left, .has-text-align-center, .ngl-social-link, .ngl-share-description';
	foreach ( $output->find( $replace ) as $key => $element ) {
		if ( strstr( $element->class, 'ng-image' ) ) {
			continue;
		}
		if ( ! strstr( $element->class, 'ngl-table-ngl-unsubscribe' ) && ! strstr( $element->class, 'logo-image' ) ) {
			if ( $element->tag === 'img' && $element->class ) {
				$classes  = explode( ' ', $element->class );
				$_classes = array();
				foreach ( $classes as $class ) {
					if ( in_array( $class, array( 'wp-image', 'wp-image-center', 'wp-image-left', 'wp-image-right', 'is-resized', 'size-large', 'size-small' ) ) ) {
						continue;
					}
					$_classes[] = $class;
				}
				$element->class = implode( ' ', $_classes );
			} else {
				$classes  = explode( ' ', $element->class );
				$_classes = array();
				foreach ( $classes as $class ) {
					if ( strstr( $class, 'ngl' ) || strstr( $class, 'wp-image' ) ) {
						continue;
					}
					$_classes[] = $class;
				}
				$element->class = implode( ' ', $_classes );
			}
			$element->removeAttribute( 'data-gap' );
			$element->removeAttribute( 'data-href' );
			$element->removeAttribute( 'data-align' );
			$element->removeAttribute( 'data-boxed-gap' );
		}
	}

	$output->save();

	return (string) $output;
}
