<?php
/**
 * Email.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$labels = apply_filters( 'newsletterglue_post_display_labels', $item[ 'labels' ], $item );

?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" class="ngl-lp-table-data">
	<tbody>

		<?php
		foreach( $order as $index ) :
			if ( $index === 1 && ! empty( $show_label ) ) {
			?>
			<tr>
				<td class="ng-td-spaced" style="padding: 2px 0px;">
					<?php do_action( 'newsletterglue_before_post_label', $item ); ?>
					<span class="ngl-lp-labels" style="<?php echo esc_attr( implode( '; ', $styles[ 'labels' ] ) ); ?>;padding: 0px;"><?php echo wp_kses_post( $labels ); ?></span>
					<?php do_action( 'newsletterglue_after_post_label', $item ); ?>
				</td>
			</tr>
			<?php
			}

			if ( $index === 2 && ! empty( $show_author ) ) {
			?>
			<tr>
				<td class="ng-td-spaced" style="padding: 2px 0px;">
					<span class="ngl-lp-labels ngl-lp-labels-author" style="<?php echo esc_attr( implode( '; ', $styles[ 'author' ] ) ); ?>;padding: 0px;"><?php echo wp_kses_post( $item[ 'author' ] ); ?></span>
				</td>
			</tr>
			<?php
			}

			if ( $index === 3 && ! empty( $show_heading ) ) {
			?>
			<tr>
				<td class="ng-td-spaced" style="padding: 2px 0px;">
					<div class="ngl-lp-title">
						<h3 class="ngl-ignore-mrkp" style="<?php echo esc_attr( implode( '; ', $styles[ 'heading' ] ) ); ?>"><a href="<?php echo esc_url( $item[ 'permalink' ] ); ?>" target="_blank" style="<?php echo esc_attr( implode( '; ', $styles[ 'heading' ] ) ); ?>;padding: 0;"><?php echo wp_kses_post( $item[ 'post_title' ] ); ?></a></h3>
					</div>
				</td>
			</tr>
			<?php
			}

			if ( $index === 4 && ! empty( $show_excerpt ) && ! empty( $item[ 'post_content' ] ) && $item['post_content'] != '<p></p>' ) {
			?>
			<tr>
				<td class="ng-td-spaced" style="padding: 2px 0px;">
					<div class="ngl-lp-content" style="<?php echo esc_attr( implode( '; ', $styles[ 'paragraph' ] ) ); ?>;padding: 0px !important;" img-w="<?php echo esc_attr( $td_data ); ?>">
						<?php echo wp_kses_post( $item[ 'post_content' ] ); ?>
					</div>
				</td>
			</tr>
			<?php
			}

			if ( $index === 5 && ! empty( $show_cta ) ) {
			?>
			<tr>
				<td class="ng-td-spaced" style="padding: 2px 0px;">
					<div class="ngl-lp-cta ngl-lp-cta-<?php echo esc_attr( $cta_type ); ?>">
						<?php if ( $cta_type === 'button' ) : ?>
						<a href="<?php echo esc_url( $item[ 'permalink' ] ); ?>" target="_blank" class="ngl-lp-cta-link wp-block-button__link" style="<?php echo esc_attr( implode( '; ', $styles[ 'links' ] ) ); ?>"><?php echo wp_kses_post( $item[ 'cta_link' ] ); ?></a>
						<?php else : ?>
						<a href="<?php echo esc_url( $item[ 'permalink' ] ); ?>" target="_blank" class="ngl-lp-cta-link" style="<?php echo esc_attr( implode( '; ', $styles[ 'links' ] ) ); ?>"><?php echo wp_kses_post( $item[ 'cta_link' ] ); ?></a>
						<?php endif; ?>
					</div>
				</td>
			</tr>
			<?php
			}

		endforeach;
		?>

	</tbody>
</table>
