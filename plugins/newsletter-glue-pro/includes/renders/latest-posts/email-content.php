<?php
/**
 * Email.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$position = isset( $image_position ) ? $image_position : 'left';

$td_image = round( ( 0.30 * $content_width ) - 10 );
$td_data  = round( ( 0.70 * $content_width ) - 10 );

if ( $position === 'right' ) {
	$td_image = round( ( 0.70 * $content_width ) - 10 );
	$td_data  = round( ( 0.30 * $content_width ) - 10 );
}

if ( $table_ratio === '70_30' ) {
	$td_image = round( ( 0.70 * $content_width ) - 10 );
	$td_data  = round( ( 0.30 * $content_width ) - 10 );
	if ( $position === 'right' ) {
		$td_image = round( ( 0.30 * $content_width ) - 10 );
		$td_data  = round( ( 0.70 * $content_width ) - 10 );
	}
}

if ( $table_ratio === '50_50' ) {
	$td_image = round( ( 0.50 * $content_width ) - 10 );
	$td_data  = round( ( 0.50 * $content_width ) - 10 );
}

if ( $table_ratio === 'full' ) {
	$td_image = $content_width;
	$td_data  = $content_width;
}

if ( $columns_num === 'one' || $contentstyle === 'single' ) :

if ( $position === 'left' ) : ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" class="ngl-table-latest-posts">
	<tbody>

		<?php foreach( $posts as $item ) : ?>
		<tr>

			<?php if ( $table_ratio === 'full' ) : ?>
			<td valign="top" style="padding: 10px 0;">
				<?php if ( $show_image != 'no-images' ) { ?>
				<table width="100%" border="0" cellpadding="0" cellspacing="0" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;"><tbody><tr><td style="padding-bottom: 10px;"><?php include( 'email-markup-image.php' ); ?></td></tr></tbody></table>
				<?php } ?>
				<?php include( 'email-markup-content.php' ); ?>
			</td>
			<?php endif; ?>

			<?php if ( $table_ratio === '30_70' ) : ?>

			<?php if ( $show_image != 'no-images' ) { ?>
			<td align="center" valign="top" width="<?php echo esc_attr( $td_image ); ?>" style="padding: 10px 0;">
				<?php include( 'email-markup-image.php' ); ?>
			</td>
			<?php } ?>

			<td align="center" valign="top" width="20"> </td>
			<td align="left" valign="top" width="<?php echo esc_attr( $td_data ); ?>" style="padding: 10px 0;">
				<?php include( 'email-markup-content.php' ); ?>
			</td>
			<?php endif; ?>

			<?php if ( $table_ratio === '70_30' ) : ?>

			<?php if ( $show_image != 'no-images' ) { ?>
			<td align="center" valign="top" width="<?php echo esc_attr( $td_image ); ?>" style="padding: 10px 0;">
				<?php include( 'email-markup-image.php' ); ?>
			</td>
			<?php } ?>

			<td align="center" valign="top" width="20"> </td>
			<td align="left" valign="top" width="<?php echo esc_attr( $td_data ); ?>" style="padding: 10px 0;">
				<?php include( 'email-markup-content.php' ); ?>
			</td>
			<?php endif; ?>

			<?php if ( $table_ratio === '50_50' ) : ?>

			<?php if ( $show_image != 'no-images' ) { ?>
			<td align="center" valign="top" width="<?php echo esc_attr( $td_image ); ?>" style="padding: 10px 0;">
				<?php include( 'email-markup-image.php' ); ?>
			</td>
			<?php } ?>

			<td align="center" valign="top" width="20"> </td>
			<td align="left" valign="top" width="<?php echo esc_attr( $td_data ); ?>" style="padding: 10px 0;">
				<?php include( 'email-markup-content.php' ); ?>
			</td>
			<?php endif; ?>

		</tr>

		<?php if ( ! empty( $divider_size ) ) : ?>
		<tr>
			<td colspan="<?php echo esc_attr( $is_full ); ?>" style="padding: 0 !important;border-bottom: <?php echo esc_attr( $divider_size ); ?>px solid <?php echo esc_attr( $divider_bg ); ?>;"></td>
		</tr>
		<?php endif; ?>

		<?php endforeach; ?>

	</tbody>
</table>
<?php endif; ?>
<?php if ( $position === 'right' ) : ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" class="ngl-table-latest-posts">
	<tbody>

		<?php foreach( $posts as $item ) : ?>
		<tr>

			<?php if ( $table_ratio === 'full' ) : ?>
			<td valign="top" style="padding: 10px 0;">
				<?php if ( $show_image != 'no-images' ) { ?>
				<table width="100%" border="0" cellpadding="0" cellspacing="0" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;"><tbody><tr><td style="padding-bottom: 10px;"><?php include( 'email-markup-image.php' ); ?></td></tr></tbody></table>
				<?php } ?>
				<?php include( 'email-markup-content.php' ); ?>
			</td>
			<?php endif; ?>

			<?php if ( $table_ratio === '30_70' ) : ?>
			<td align="left" valign="top" width="<?php echo esc_attr( $td_data ); ?>" style="padding: 10px 0;">
				<?php include( 'email-markup-content.php' ); ?>
			</td>
			<td align="center" valign="top" width="20"> </td>
			
			<?php if ( $show_image != 'no-images' ) { ?>
			<td align="center" valign="top" width="<?php echo esc_attr( $td_image ); ?>" style="padding: 10px 0;">
				<?php include( 'email-markup-image.php' ); ?>
			</td>
			<?php } ?>

			<?php endif; ?>

			<?php if ( $table_ratio === '70_30' ) : ?>
			<td align="left" valign="top" width="<?php echo esc_attr( $td_data ); ?>" style="padding: 10px 0;">
				<?php include( 'email-markup-content.php' ); ?>
			</td>
			<td align="center" valign="top" width="20"> </td>
			
			<?php if ( $show_image != 'no-images' ) { ?>
			<td align="center" valign="top" width="<?php echo esc_attr( $td_image ); ?>" style="padding: 10px 0;">
				<?php include( 'email-markup-image.php' ); ?>
			</td>
			<?php } ?>

			<?php endif; ?>

			<?php if ( $table_ratio === '50_50' ) : ?>
			<td align="left" valign="top" width="<?php echo esc_attr( $td_data ); ?>" style="padding: 10px 0;">
				<?php include( 'email-markup-content.php' ); ?>
			</td>
			<td align="center" valign="top" width="20"> </td>

			<?php if ( $show_image != 'no-images' ) { ?>
			<td align="center" valign="top" width="<?php echo esc_attr( $td_image ); ?>" style="padding: 10px 0;">
				<?php include( 'email-markup-image.php' ); ?>
			</td>
			<?php } ?>

			<?php endif; ?>

		</tr>

		<?php if ( ! empty( $divider_size ) ) : ?>
		<tr>
			<td colspan="<?php echo esc_attr( $is_full ); ?>" style="padding: 0 !important;border-bottom: <?php echo esc_attr( $divider_size ); ?>px solid <?php echo esc_attr( $divider_bg ); ?>;"></td>
		</tr>
		<?php endif; ?>

		<?php endforeach; ?>

	</tbody>
</table>
<?php

endif;
endif;

if ( $columns_num === 'two' && $contentstyle === 'multi' ) :
	include( 'email-two-columns.php' );
endif;
