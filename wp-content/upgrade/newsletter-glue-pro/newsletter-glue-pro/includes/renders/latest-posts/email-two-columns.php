<?php
/**
 * Email.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$two_columns = ( $content_width / 2 ) - 10;
$td_image	 = $two_columns;

$rounded = round( $two_columns );

?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" class="ngl-table-latest-posts">
	<tbody>

		<tr>
		<?php
			$i = 0;
			foreach( $posts as $item ) :
		?>

			<td valign="top" width="<?php echo absint( $rounded ); ?>" style="padding: 10px 0;width: <?php echo absint( $rounded ); ?>px;">
				<?php if ( $show_image != 'no-images' ) { ?>
				<table width="100%" border="0" cellpadding="0" cellspacing="0" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;"><tbody><tr><td style="padding-bottom: 10px;"><?php include( 'email-markup-image.php' ); ?></td></tr></tbody></table>
				<?php } ?>
				<?php include( 'email-markup-content.php' ); ?>
			</td>

		<?php
			$i++;
			if ( $i % 2 == 1 ) {
				echo '<td width="20" style="width:20px;"> </td>';
			}
			if ( $i % 2 == 0 && $i != count( $posts ) ) {
				echo '</tr>';
				// Add divider row between pairs of posts
				if ( isset( $divider_size ) ) : ?>
				<tr>
					<td colspan="3" style="padding: 0 !important;border-bottom: <?php echo esc_attr( $divider_size ); ?>px solid <?php echo esc_attr( $divider_bg ); ?>;"></td>
				</tr>
				<?php endif;
				echo '<tr>';
			}
			endforeach;
		?>

		</tr>

		<?php if ( isset( $divider_size ) && $i == count( $posts ) && count( $posts ) > 1 && $i % 2 == 0 ) : // Add final divider if we have an even number of posts ?>
		<tr>
			<td colspan="3" style="padding: 0 !important;border-bottom: <?php echo esc_attr( $divider_size ); ?>px solid <?php echo esc_attr( $divider_bg ); ?>;"></td>
		</tr>
		<?php endif; ?>

	</tbody>
</table>