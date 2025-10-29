<?php
/**
 * Email.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$bgcolor = ! empty( $background_color ) ? 'bgcolor="' . esc_attr( $background_color ) . '"' : '';

?>

<table width="100%" border="0" cellpadding="0" cellspacing="0" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" class="ng-posts-wrapper <?php echo esc_attr( $stacked ); ?>" ng-font="<?php echo esc_attr( $font ); ?>">
	<tbody>

		<tr>
			<?php if ( ! empty( $margin ) && ! empty( $margin[ 'left' ] ) && $margin[ 'left' ] != '0px' ) : ?>
			<td width="<?php echo absint( $margin[ 'left' ] ); ?>" style="width: <?php echo absint( $margin[ 'left' ] ); ?>px;"></td>
			<?php endif; ?>

			<td width="<?php echo absint( $outer_width ); ?>">
				<table width="100%" border="0" cellpadding="0" cellspacing="0" style="<?php echo esc_attr( implode( '; ', $styles[ 'container' ] ) ); ?>" <?php echo $bgcolor; ?>>
					<tbody>
						<?php if ( ! empty( $padding ) && ! empty( $padding[ 'top' ] ) && $padding[ 'top' ] != '0px' ) : ?>
						<tr>
							<td colspan="<?php echo absint( $colspan ); ?>" height="<?php echo absint( $padding[ 'top' ] ); ?>" style="height: <?php echo absint( $padding[ 'top' ] ); ?>px;"> </td>
						</tr>
						<?php endif; ?>
						<tr>
							<?php if ( ! empty( $padding ) && ! empty( $padding[ 'left' ] ) && $padding[ 'left' ] != '0px' ) : ?>
							<td width="<?php echo absint( $padding[ 'left' ] ); ?>" style="width: <?php echo absint( $padding[ 'left' ] ); ?>px;"></td>
							<?php endif; ?>
							<td width="<?php echo absint( $content_width ); ?>"><?php include( 'email-content.php' ); ?></td>
							<?php if ( ! empty( $padding ) && ! empty( $padding[ 'right' ] ) && $padding[ 'right' ] != '0px' ) : ?>
							<td width="<?php echo absint( $padding[ 'right' ] ); ?>" style="width: <?php echo absint( $padding[ 'right' ] ); ?>px;"></td>
							<?php endif; ?>
						</tr>
						<?php if ( ! empty( $padding ) && ! empty( $padding[ 'bottom' ] ) && $padding[ 'bottom' ] != '0px' ) : ?>
						<tr>
							<td colspan="<?php echo absint( $colspan ); ?>" height="<?php echo absint( $padding[ 'bottom' ] ); ?>" style="height: <?php echo absint( $padding[ 'bottom' ] ); ?>px;"> </td>
						</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</td>

			<?php if ( ! empty( $margin ) && ! empty( $margin[ 'right' ] ) && $margin[ 'right' ] != '0px' ) : ?>
			<td width="<?php echo absint( $margin[ 'right' ] ); ?>" style="width: <?php echo absint( $margin[ 'right' ] ); ?>px;"></td>
			<?php endif; ?>
		</tr>

	</tbody>
</table>
