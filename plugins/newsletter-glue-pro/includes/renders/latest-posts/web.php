<?php
/**
 * Web.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="<?php echo esc_attr( $classes ); ?>">

	<div class="ngl-lp-items" style="<?php echo esc_attr( implode( '; ', $styles[ 'container' ] ) ); ?>">
		<?php foreach( $posts as $item ) : ?>
		<?php $labels = apply_filters( 'newsletterglue_post_display_labels', $item[ 'labels' ], $item ); ?>
		<div class="ngl-lp-item" style="flex-basis: <?php echo $columns_num === 'two' ? esc_attr( $itemBase ) : 'auto'; ?>">

			<?php if ( $show_image !== 'no-images' ) : ?>
			<div class="ngl-lp-image" style="flex-basis: <?php echo esc_attr( $div1 ); ?>;padding: 2px 0;">
				<a href="<?php echo esc_url( $item[ 'permalink' ] ); ?>"><img src="<?php echo esc_url( $item[ 'featured_image' ] ); ?>" style="<?php echo esc_attr( implode( '; ', $styles[ 'images' ] ) ); ?>" /></a>
			</div>
			<?php endif; ?>

			<div class="ngl-lp-data" style="flex-basis: <?php echo esc_attr( $div2 ); ?>">

				<?php
				foreach( $order as $index ) :
					if ( $index === 1 && ! empty( $show_label ) ) {
						?>
						<?php do_action( 'newsletterglue_before_post_label', $item ); ?>
						<div class="ngl-lp-labels" style="<?php echo esc_attr( implode( '; ', $styles[ 'labels' ] ) ); ?>"><?php echo wp_kses_post( $labels ); ?></div>
						<?php do_action( 'newsletterglue_after_post_label', $item ); ?>
						<?php
					}

					if ( $index === 2 && ! empty( $show_author ) ) {
						?>
						<div class="ngl-lp-labels ngl-lp-labels-author" style="<?php echo esc_attr( implode( '; ', $styles[ 'author' ] ) ); ?>"><?php echo wp_kses_post( $item[ 'author' ] ); ?></div>
						<?php
					}

					if ( $index === 3 && ! empty( $show_heading ) ) {
						?>
						<div class="ngl-lp-title">
							<h3 style="<?php echo esc_attr( implode( '; ', $styles[ 'heading' ] ) ); ?>">
                                <?php do_action( 'newsletterglue_before_lp_title_link', $item ); ?>
                                <a href="<?php echo esc_url( $item[ 'permalink' ] ); ?>" style="<?php echo esc_attr( implode( '; ', $styles[ 'heading' ] ) ); ?>;padding: 0px;"><?php do_action( 'newsletterglue_before_lp_title', $item ); ?><?php echo wp_kses_post( $item[ 'post_title' ] ); ?></a>
                            </h3>
						</div>
						<?php
					}

					if ( $index === 4 && ! empty( $show_excerpt ) && ! empty( $item[ 'post_content' ] ) && $item['post_content'] != '<p></p>' ) {
						?>
						<div class="ngl-lp-content" style="<?php echo esc_attr( implode( '; ', $styles[ 'paragraph' ] ) ); ?>">
							<?php echo wp_kses_post( $item[ 'post_content' ] ); ?>
						</div>
						<?php
					}

					if ( $index === 5 && ! empty( $show_cta ) ) {
						?>
						<div class="ngl-lp-cta ngl-lp-cta-<?php echo esc_attr( $cta_type ); ?>" style="<?php echo esc_attr( implode( '; ', $styles[ 'cta' ] ) ); ?>">
							<a href="<?php echo esc_url( $item[ 'permalink' ] ); ?>" class="ngl-lp-cta-link<?php echo $cta_type === 'button' ? ' wp-block-button__link' : ''; ?>" style="<?php echo esc_attr( implode( '; ', $styles[ 'links' ] ) ); ?>"><?php echo wp_kses_post( $item[ 'cta_link' ] ); ?></a>
						</div>
						<?php
					}

				endforeach;
				?>

			</div>

		</div>
		<?php if ( ! empty( $args['divider_size' ] ) ) : ?>
		<div style="background-color: <?php echo esc_attr( $args['divider_bg'] ); ?>;height: <?php echo absint( $args['divider_size'] ); ?>px;margin: 10px 0;"></div>
		<?php endif; ?>

		<?php endforeach; ?>
	</div>

</div>
