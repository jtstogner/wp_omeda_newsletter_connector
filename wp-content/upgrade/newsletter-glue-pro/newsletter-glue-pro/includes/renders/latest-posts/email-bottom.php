<?php
/**
 * Email.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<?php if ( ! empty( $margin ) && ! empty( $margin[ 'bottom' ] ) && $margin[ 'bottom' ] != '0px' ) : ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
	<tbody>
		<tr>
			<td height="<?php echo absint( $margin[ 'bottom' ] ); ?>" style="height: <?php echo absint( $margin[ 'bottom' ] ); ?>px;"> </td>
		</tr>
	</tbody>
</table>
<?php endif; ?>