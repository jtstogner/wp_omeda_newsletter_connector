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
				echo '</tr><tr>';
			}
			endforeach;
		?>

		</tr>

	</tbody>
</table>