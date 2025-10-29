<?php
/**
 * Columns block.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Setup columns block HTML.
 */
add_filter( 'render_block', 'newsletterglue_render_columns_block', 10, 3 );
function newsletterglue_render_columns_block( $block_content, $block ) {

	if ( ! defined( 'NGL_IN_EMAIL' ) ) {
		return $block_content;
	}

	$origin_size = 600;

    if ( "newsletterglue/columns" !== $block['blockName'] ) return $block_content;

	$output = new simple_html_dom();
	$output->load( $block_content, true, false );

	$replace = 'section';
	foreach( $output->find( $replace ) as $key => $element ) {
		$col_count = $element->{'data-columns'} ? $element->{'data-columns'} : 1;
		if ( $element->{ 'data-left-padding' } ) {
			$origin_size = 	$origin_size - absint( $element->{ 'data-left-padding' } );
		}
		if ( $element->{ 'data-right-padding' } ) {
			$origin_size = 	$origin_size - absint( $element->{ 'data-right-padding' } );
		}
	}

	$replace = '.ng__column';
	$i = 0;
	$unallocated = $origin_size;
	$spots = 0;
	foreach( $output->find( $replace ) as $key => $element ) {
		$i++;
		if ( $i > $col_count ) {
			$element->outertext = '';
			$spots--;
		}
		if ( $element->{ 'data-size' } ) {
			$size = $element->{ 'data-size' };
			if ( strstr( $size, '%' ) ) {
				$size = str_replace( '%', '', $size );
				$element->{ 'data-size' } = ( $size / 100 ) * $origin_size;
				$unallocated = $origin_size - ( $size / 100 ) * $origin_size;
			} else if ( strstr( $size, 'auto' ) ) {
				$spots++;
			} else {
				$size = str_replace( 'px', '', $size );
				$element->{ 'data-size' } = $size;
				$unallocated = $origin_size - $size;
			}
		}
	}

	if ( $unallocated ) {
		foreach( $output->find( $replace ) as $key => $element ) {
			if ( $element->{ 'data-size' } ) {
				$size = $element->{ 'data-size' };
				if ( strstr( $size, 'auto' ) ) {
					$element->{ 'data-size' } = $spots > 0 ? $unallocated / $spots : $unallocated;
				}
			}
		}
	}

	$output->save();

    return ( string ) $output;
}

/**
 * Render columns block for email.
 */
add_filter( 'render_block', 'newsletterglue_render_columns_block_in_email', 50, 3 );
function newsletterglue_render_columns_block_in_email( $block_content, $block ) {

	if ( ! defined( 'NGL_IN_EMAIL' ) ) {
		return $block_content;
	}

    if ( "newsletterglue/columns" !== $block['blockName'] ) return $block_content;

	$output = new simple_html_dom();
	$output->load( $block_content, true, false );

	$replace = 'section';
	foreach( $output->find( $replace ) as $key => $element ) {
		$col_count = $element->{'data-columns'} ? $element->{'data-columns'} : 1;
	}

	$replace = 'section .ng__column';
	foreach( $output->find( $replace ) as $key => $element ) {

		$width = $element->{ 'data-size' };

		$color = $element->parent->{ 'data-text-color' };

		if ( ! empty( $color ) ) {
			$color = 'color: ' . $color . ' !important;';
		}

		$class = 'column';
		if ( $element->class ) {
			$class = trim( 'column' . str_replace( 'ng__column', '', $element->class ) );
		}

		if ( $element->parent->class && strstr( $element->parent->class, 'ngl-no-wrap' ) ) {
			$class = 'column-no-wrap';
		}

		$output->find( $replace, $key )->outertext = '<td class="' . $class . '" width="' . $width . '" style="vertical-align: top;' . $color . '" valign="top" padding-left="' . $element->{ 'data-lpadding' } . '" padding-right="' . $element->{ 'data-rpadding' } . '" padding-top="' . $element->{ 'data-tpadding' } . '" padding-bottom="' . $element->{ 'data-bpadding' } . '">' . $element->innertext . '</td>';
	}

	// Add columns wrapper as a table.
	$replace = '.ng_columns__container';
	foreach( $output->find( $replace ) as $key => $element ) {
		$class = str_replace( 'ng_columns__container', '', $element->class );
		if ( $element->{ 'data-bg-color' } ) {
			$bg_color = 'bgcolor="' . $element->{ 'data-bg-color' } . '"';
		} else {
			$bg_color = '';
		}
		$class = trim( $class );

		$top_padding = absint( $element->{ 'data-top-padding' } );
		$bottom_padding = absint( $element->{ 'data-bottom-padding' } );
		$top_margin = $element->{ 'data-top-margin' } ? absint( $element->{ 'data-top-margin' } ) : 0;
		$bottom_margin = $element->{ 'data-bottom-margin' } ? absint( $element->{ 'data-bottom-margin' } ) : 0;

		$padding_left = $element->{ 'data-left-padding' } ? absint( $element->{ 'data-left-padding' } ) : 0;
		$padding_right = $element->{ 'data-right-padding' } ? absint( $element->{ 'data-right-padding' } ) : 0;
		
		$cols = absint( $element->{ 'data-columns' } );

		if ( $padding_left ) {
			$cols = $cols + 1;
		}

		if ( $padding_right ) {
			$cols = $cols + 1;
		}

		$html = '';

		if ( $top_margin ) {
			$html .= '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td height="' . $top_margin . '"></td></tr></table>';
		}

		$html .= '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="ngl-table-columns ngl-columns ' . $class . '" ' . $bg_color . '" data-base-color="' . $element->{ 'data-text-color' } . '">';

		if ( $top_padding ) {
			$html .= '<tr class="root-tr"><td height="' . $top_padding. '" colspan="' . $cols . '" style="padding:0 !important;"></td></tr>';
		}

		$html .= '<tr class="root-tr">';
		if ( $padding_left ) {
			$html .= '<td width="' . $padding_left . '"></td>';
		}
		$html .= $element->innertext;
		if ( $padding_right ) {
			$html .= '<td width="' . $padding_right . '"></td>';
		}
		$html .= '</tr>';

		if ( $bottom_padding ) {
			$html .= '<tr class="root-tr"><td height="' . $bottom_padding. '" colspan="' . $cols . '" style="padding:0 !important;"></td></tr>';
		}

		$html .= '</table>';

		if ( $bottom_margin ) {
			$html .= '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td height="' . $bottom_margin . '"></td></tr></table>';
		}

		$output->find( $replace, $key )->outertext = $html;

	}

	$output->save();

    return ( string ) $output;
}

/**
 * Render columns block for web.
 */
add_filter( 'render_block', 'newsletterglue_render_columns_block_in_web', 50, 3 );
function newsletterglue_render_columns_block_in_web( $block_content, $block ) {

	if ( defined( 'NGL_IN_EMAIL' ) ) {
		return $block_content;
	}

    if ( "newsletterglue/columns" !== $block['blockName'] ) return $block_content;

	$output = new simple_html_dom();
	$output->load( $block_content, true, false );

	$replace = 'section .ng__column';
	foreach( $output->find( $replace ) as $key => $element ) {
		$width = $element->{ 'data-size' };
		$element->style = $element->style . ';flex-basis: ' . $width;
	}

	$output->save();

    return ( string ) $output;
}