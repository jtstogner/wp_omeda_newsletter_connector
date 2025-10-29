<?php
/**
 * Email.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<?php if ( ! empty( $margin ) && ! empty( $margin[ 'top' ] ) && $margin[ 'top' ] != '0px' ) : ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
	<tbody>
		<tr>
			<td height="<?php echo absint( $margin[ 'top' ] ); ?>" style="height: <?php echo absint( $margin[ 'top' ] ); ?>px;"> </td>
		</tr>
	</tbody>
</table>
<?php endif; ?>