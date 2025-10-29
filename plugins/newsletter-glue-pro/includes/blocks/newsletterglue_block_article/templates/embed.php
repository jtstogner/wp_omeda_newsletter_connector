<?php
/**
 * Post embeds.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$editable = false;
$show_edit_controls = false;

if ( ! empty( $attributes[ 'background_color' ] ) || ! empty( $attributes[ 'border_radius' ] ) || ! empty( $attributes[ 'border_size' ] ) ) {
	$pure = 'colored';
} else {
	$pure = 'pure';
}

$scope = ! empty( $attributes[ 'scope' ] ) ? $attributes[ 'scope' ] : 'regular';

?>

<?php if ( absint( $top_margin ) ) { ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;"><tbody><tr><td height="<?php echo absint( $top_margin ); ?>" style="font-family: Arial, Helvetica, sans-serif;"></td></tr></tbody></table>
<?php } ?>

<div class="ngl-articles <?php echo esc_attr( $pure ); ?> ngl-articles-<?php echo esc_attr( $table_ratio ); ?> <?php echo ( is_newsletterglue_gutenberg() ) ? 'ngl-articles-admin' : 'ngl-articles-frontend'; ?>" data-date_format="<?php echo esc_attr( $date_format ); ?>" data-block-id="<?php echo esc_attr( $block_id ); ?>">

	<?php if ( is_newsletterglue_gutenberg() ) : ?>
	<?php if ( ! defined( 'NGL_IN_EMAIL' ) ) : ?>
	<?php
		$editable = 'contenteditable="true"';
		$show_edit_controls = '<span class="ngl-article-featured-edit"><i class="icon image"><svg stroke="currentColor" fill="currentColor" stroke-width="0" version="1.1" viewBox="0 0 16 16" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M14.998 2c0.001 0.001 0.001 0.001 0.002 0.002v11.996c-0.001 0.001-0.001 0.001-0.002 0.002h-13.996c-0.001-0.001-0.001-0.001-0.002-0.002v-11.996c0.001-0.001 0.001-0.001 0.002-0.002h13.996zM15 1h-14c-0.55 0-1 0.45-1 1v12c0 0.55 0.45 1 1 1h14c0.55 0 1-0.45 1-1v-12c0-0.55-0.45-1-1-1v0z"></path><path d="M13 4.5c0 0.828-0.672 1.5-1.5 1.5s-1.5-0.672-1.5-1.5 0.672-1.5 1.5-1.5 1.5 0.672 1.5 1.5z"></path><path d="M14 13h-12v-2l3.5-6 4 5h1l3.5-3z"></path></svg></i><i class="icon trash"><svg stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></i></span>';
	?>
	<div class="components-placeholder wp-block-embed is-large">
		<div class="ngl-articles-add">
			<div class="components-placeholder__label">
				<span class="block-editor-block-icon has-colors" style="color: rgb(29, 161, 242);"><svg width="24" height="24" viewBox="0 0 92.308 75" role="img" aria-hidden="true" focusable="false"><path fill="#0088A0" d="M14.423,61.067H2.885A2.885,2.885,0,0,0,0,63.952V75.49a2.885,2.885,0,0,0,2.885,2.885H14.423a2.885,2.885,0,0,0,2.885-2.885V63.952A2.885,2.885,0,0,0,14.423,61.067Zm0-57.692H2.885A2.885,2.885,0,0,0,0,6.26V17.8a2.885,2.885,0,0,0,2.885,2.885H14.423A2.885,2.885,0,0,0,17.308,17.8V6.26A2.885,2.885,0,0,0,14.423,3.375Zm0,28.846H2.885A2.885,2.885,0,0,0,0,35.106V46.644a2.885,2.885,0,0,0,2.885,2.885H14.423a2.885,2.885,0,0,0,2.885-2.885V35.106A2.885,2.885,0,0,0,14.423,32.221Zm75,31.731H31.731a2.885,2.885,0,0,0-2.885,2.885v5.769a2.885,2.885,0,0,0,2.885,2.885H89.423a2.885,2.885,0,0,0,2.885-2.885V66.837A2.885,2.885,0,0,0,89.423,63.952Zm0-57.692H31.731a2.885,2.885,0,0,0-2.885,2.885v5.769A2.885,2.885,0,0,0,31.731,17.8H89.423a2.885,2.885,0,0,0,2.885-2.885V9.144A2.885,2.885,0,0,0,89.423,6.26Zm0,28.846H31.731a2.885,2.885,0,0,0-2.885,2.885V43.76a2.885,2.885,0,0,0,2.885,2.885H89.423a2.885,2.885,0,0,0,2.885-2.885V37.99A2.885,2.885,0,0,0,89.423,35.106Z" transform="translate(0 -3.375)"></path></svg></span><?php _e( 'Post embed', 'newsletter-glue' ); ?>
				<span class="ngl-ajax-block-id" style="font-size:11px;background:#eee;color:#666;border-radius:3px;margin:0 0 0 15px;opacity:1;word-break: break-all;">ID: <?php echo esc_html( $block_id ); ?></span>
				<span class="ngl-ajax-block-scope" style="font-size:11px;background:#eee;color:#666;border-radius:3px;margin:0 0 0 15px;opacity:1;"><?php echo esc_html( $scope ); ?></span>
			</div>
			<?php if ( ! empty( $block_id ) ) { ?>
			<div class="components-placeholder__fieldset">
				<div class="ngl-article-status"></div>
				<form class="ngl-article-add" action="" method="post" novalidate>
					<div class="ngl-article-box">
						<input type="text" class="components-placeholder__input ngl_article_s" data-post="" placeholder="<?php _e( 'Search for a post or enter URL here…', 'newsletter-glue' ); ?>" value="">
						<ul class="ngl-article-suggest">

						</ul>
					</div>
					<button type="submit" class="components-button is-primary"><?php _e( 'Add', 'newsletter-glue' ); ?></button>
				</form>
			</div>
			<?php } else { ?>
			<span class="ngl-loadit"><img src="<?php echo esc_url( includes_url( 'images/spinner.gif' ) ); ?>" /></span>
			<?php } ?>
		</div>
	</div>

	<?php
		if ( $show_more_link ) {
			$read_more_text = isset( $read_more_text ) ? $read_more_text : __( 'Read more.', 'newsletter-glue' );
			$show_more_text = ' <a href="{permalink}" class="ngl-article-read-more" target="' . $new_window . '">' . apply_filters( 'newsletterglue_article_read_more_text', $read_more_text ) . '</a>';
		} else {
			$show_more_text = '';
		}
		$display_image  	= ( $show_image ) ? '<div class="ngl-article-featured"><a href="{permalink}"><img src="{featured_image}" data-original-src="{featured_image}" style="border-radius: ' . absint( $image_radius ) . 'px;" /></a>' . $show_edit_controls . '</div>' : '';
		$display_labels     = ( $show_labels ) ? '<div class="ngl-article-labels" ' . $editable . '>{labels}</div>' : '';
		$display_title  	= '<div class="ngl-article-title"><a href="{permalink}" style="font-size: ' . $font_size_title . 'px;' . $link_color . '"><span ' . $editable . '>{title}</span></a></div>';
		$display_excerpt 	= $show_excerpt ? '<div class="ngl-article-excerpt" style="' . esc_attr( $text_color ) .'" ' . $editable . '>{excerpt}' . $show_more_text . '</div>' : '';
		$display_date       = ( $show_date ) ? '<div class="ngl-article-date">{date}</div>' : '';
	?>

	<div class="ngl-article ngl-article-img-<?php echo esc_attr( $image_position ); ?> ngl-article-placeholder" data-key="{key}" data-post-id="{post_id}" style="<?php echo esc_attr( $text_color ); ?>background-color: <?php echo esc_attr( $background_color ); ?>; padding: <?php echo esc_attr( $padding ); ?>; border-radius: <?php echo absint( $border_radius ); ?>px; border: <?php echo absint( $border_size ); ?>px <?php echo esc_attr( $border_style ); ?> <?php echo esc_attr( $border_color ); ?>; font-size: <?php echo esc_attr( $font_size_text ); ?>px;">

				<div class="ngl-article-list-move">
					<div class="ngl-article-list-move-up"><a href="#"><svg stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg></a></div>
					<div class="ngl-article-list-move-down"><a href="#"><svg stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></a></div>
				</div>

				<div class="ngl-article-list-layer"></div>
				<div class="ngl-article-list-layer2"></div>

				<a href="#" class="ngl-article-list-link"><svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg></a>
				<a href="#" class="ngl-article-list-refresh"><svg stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg></a>
				<a href="#" class="ngl-article-list-delete"><svg stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></a>

				<div class="ngl-article-state-refreshing"><?php _e( 'Refreshing...', 'newsletter-glue' ); ?></div>
				<a href="#" class="ngl-article-state-remove"><?php _e( 'Confirm remove', 'newsletter-glue' ); ?></a>

				<div class="ngl-article-list-url-edit">
					<span contenteditable="true">{permalink}</span>
					<a href="#"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 20 20" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg></a>
				</div>

				<div class="ngl-article-overlay"></div>

				<?php
					// @codingStandardsIgnoreStart
					if ( $table_ratio == 'full' ) :
						echo $display_image;
						echo $display_labels;
						echo $display_title;
						echo $display_excerpt;
						echo $display_date;
					else :
						if ( $image_position == 'left' ) :
							echo '<div class="ngl-article-left">' . $display_image . '</div>';
							echo '<div class="ngl-article-right">';
								echo $display_labels;
								echo $display_title;
								echo $display_excerpt;
								echo $display_date;
							echo '</div>';
						endif;
						if ( $image_position == 'right' ) :
							echo '<div class="ngl-article-left">';
								echo $display_labels;
								echo $display_title;
								echo $display_excerpt;
								echo $display_date;
							echo '</div>';
							echo '<div class="ngl-article-right">' . $display_image . '</div>';
						endif;
					endif;
					// @codingStandardsIgnoreEnd
				?>

	</div>
	<?php endif; ?>
	<?php endif; ?>

	<div class="ngl-articles-wrap<?php echo ( ! defined( 'NGL_IN_EMAIL' ) ) ? ' ngl-articles-webview' : ''; ?>">
	<?php
	$show_more_text = null;
	if ( ! empty( $articles ) ) :

			krsort( $articles );

			foreach( $articles as $key => $article ) :

				// Internal post.
				if ( ! empty( $article[ 'post_id' ] ) ) :

					if ( ! empty( $article[ 'is_remote' ] ) ) {
						$thearticle = $this->get_remote_url( $article[ 'post_id' ] );
					} else {
						$thearticle = get_post( $article[ 'post_id' ] );
						if ( empty( $thearticle->ID ) ) {
							unset( $articles[ $key ] );
							continue;
						}
					}

					$display_labels = ( $show_labels ) ? '<div class="ngl-article-labels" ' . $editable . '>' . $this->get_labels( $thearticle->ID, $this->get_permalink( $thearticle ) ) . '</div>' : '';
					if ( ! $editable && $show_labels && ! $this->get_labels( $thearticle->ID, $this->get_permalink( $thearticle ) ) ) {
						$display_labels = '';
					}

					if ( ! empty( $thearticle->is_remote ) ) {
						$display_image  	= ( $show_image && ! empty( $thearticle->image_url ) ) ? '<div class="ngl-article-featured"><a href="' . $this->get_permalink( $thearticle ) . '" target="' . $new_window . '" rel="' . $nofollow . '"><img src="' . $this->get_image_url( $thearticle ) . '" data-original-src="' . $this->get_image_default( $thearticle ). '" style="border-radius: ' . absint( $image_radius ) . 'px;" /></a>' . $show_edit_controls . '</div>' : '';
					} else {
						$display_image  	= ( $show_image ) ? '<div class="ngl-article-featured"><a href="' . $this->get_permalink( $thearticle ) . '" target="' . $new_window . '" rel="' . $nofollow . '"><img src="' . $this->get_image_url( $thearticle ) . '" data-original-src="' . $this->get_image_default( $thearticle ). '" style="border-radius: ' . absint( $image_radius ) . 'px;" /></a>' . $show_edit_controls . '</div>' : '';
					}

					if ( $show_more_link ) {
						$show_more_text = ' <a href="' . $this->get_permalink( $thearticle ) . '" class="ngl-article-read-more" target="' . $new_window . '">' . $this->display_learn_more( $thearticle->ID, $read_more_text ) . '</a>';
					}

					$thecontent 		= apply_filters( 'newsletterglue_article_embed_content', strip_shortcodes( $thearticle->post_content ), $thearticle->ID );
					$display_title 		= '<div class="ngl-article-title"><a href="' . $this->get_permalink( $thearticle ) . '" target="' . $new_window . '" rel="' . $nofollow . '" style="font-size: ' . $font_size_title . 'px;' . $link_color . '">';
					$display_title     .= '<span ' . $editable . '>' . $this->display_title( $thearticle->ID, $thearticle ) . '</span></a></div>';
					$display_excerpt 	= $show_excerpt ? '<div class="ngl-article-excerpt" style="' . esc_attr( $text_color ) . '" ' . $editable . '>' . $this->display_excerpt( $thearticle->ID, $thecontent ) . $show_more_text . '</div>' : '';
					$display_date    	= ( $show_date && ! empty( $thearticle->post_date ) ) ? '<div class="ngl-article-date">' . date_i18n( $date_format, strtotime( $thearticle->post_date ) ) . '</div>' : '';

				else :

				endif;

				if ( ! $show_image ) {
					$table_ratio = 'full';
				}

				if ( $table_ratio === 'full' ) {
					$image_position = 'full';
				}

		?>

			<?php if ( ! is_newsletterglue_gutenberg() ) { ?>
			<!--[if !mso]><\!-->
			<div class="ngl-article-mobile">
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td valign="top" style="vertical-align: top;padding: 0;">
							<div class="ngl-article-mob-wrap" style="<?php echo esc_attr( $text_color ); ?>background-color: <?php echo esc_attr( $background_color ); ?>;border-radius: <?php echo absint( $border_radius ); ?>px; border: <?php echo absint( $border_size ); ?>px <?php echo esc_attr( $border_style ); ?> <?php echo esc_attr( $border_color ); ?>; font-size: <?php echo esc_attr( $font_size_text ); ?>px;">
							<?php echo $display_image . $display_labels . $display_title . $display_excerpt . $display_date; // phpcs:ignore ?>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<!-- <![endif]-->
			<?php } ?>

			<div class="ngl-article ngl-article-img-<?php echo esc_attr( $image_position ); ?>" data-key="<?php echo esc_attr( $key ); ?>" data-post-id="<?php echo esc_attr( $thearticle->ID ); ?>" style="<?php echo esc_attr( $text_color ); ?>background-color: <?php echo esc_attr( $background_color ); ?>; padding: <?php echo esc_attr( $padding ); ?>; border-radius: <?php echo absint( $border_radius ); ?>px; border: <?php echo absint( $border_size ); ?>px <?php echo esc_attr( $border_style ); ?> <?php echo esc_attr( $border_color ); ?>; font-size: <?php echo esc_attr( $font_size_text ); ?>px;">

				<?php if ( is_newsletterglue_gutenberg() ) : ?>
				<?php if ( ! defined( 'NGL_IN_EMAIL' ) ) : ?>

					<div class="ngl-article-list-move">
						<div class="ngl-article-list-move-up"><a href="#"><svg stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg></a></div>
						<div class="ngl-article-list-move-down"><a href="#"><svg stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></a></div>
					</div>

					<div class="ngl-article-list-layer"></div>
					<div class="ngl-article-list-layer2"></div>

					<a href="#" class="ngl-article-list-link"><svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg></a>
					<a href="#" class="ngl-article-list-refresh"><svg stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg></a>
					<a href="#" class="ngl-article-list-delete"><svg stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></a>

					<div class="ngl-article-state-refreshing"><?php _e( 'Refreshing...', 'newsletter-glue' ); ?></div>
					<a href="#" class="ngl-article-state-remove"><?php _e( 'Confirm remove', 'newsletter-glue' ); ?></a>

					<div class="ngl-article-list-url-edit">
						<span contenteditable="true"><?php echo esc_html( $this->get_permalink( $thearticle ) ); ?></span>
						<a href="#"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 20 20" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg></a>
					</div>

					<div class="ngl-article-overlay"></div>

				<?php endif; ?>
				<?php endif; ?>

				<?php
					// @codingStandardsIgnoreStart
					if ( $table_ratio == 'full' ) :
						echo $display_image;
                        do_action( 'newsletterglue_before_post_label', $article['post_id'] );
                        echo apply_filters( 'newsletterglue_post_display_labels', $display_labels, $article['post_id'] );
                        do_action( 'newsletterglue_after_post_label', $article['post_id'] );
						echo $display_title;
						echo $display_excerpt;
						echo $display_date;
					else :
						if ( $image_position == 'left' ) :
							echo '<div class="ngl-article-left">' . $display_image . '</div>';
							echo '<div class="ngl-article-right">';
                                do_action( 'newsletterglue_before_post_label', $article['post_id'] );
                                echo apply_filters( 'newsletterglue_post_display_labels', $display_labels, $article['post_id'] );
                                do_action( 'newsletterglue_after_post_label', $article['post_id'] );
								echo $display_title;
								echo $display_excerpt;
								echo $display_date;
							echo '</div>';
						endif;
						if ( $image_position == 'right' ) :
							echo '<div class="ngl-article-left">';
                                do_action( 'newsletterglue_before_post_label', $article['post_id'] );
                                echo apply_filters( 'newsletterglue_post_display_labels', $display_labels, $article['post_id'] );
                                do_action( 'newsletterglue_after_post_label', $article['post_id'] );
								echo $display_title;
								echo $display_excerpt;
								echo $display_date;
							echo '</div>';
							echo '<div class="ngl-article-right">' . $display_image . '</div>';
						endif;
					endif;
					// @codingStandardsIgnoreEnd
				?>

			</div>

		<?php endforeach; ?>

		<?php update_option( 'ngl_articles_' . $block_id, $articles ); ?>

	<?php endif; ?>
	</div>

</div>

<?php if ( absint( $bottom_margin ) ) { ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;"><tbody><tr><td height="<?php echo absint( $bottom_margin ); ?>" style="font-family: Arial, Helvetica, sans-serif;"></td></tr></tbody></table>
<?php } ?>