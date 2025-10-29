<?php
/**
 * Twitter.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<table width="100%" cellpadding="0" cellspacing="0" class="ng-block-twitter">
	<tbody>
		<tr>
			<td class="ng-block-td" style="padding:20px;">
				<table width="100%" cellpadding="0" cellspacing="0" style="table-layout: fixed !important;">
					<tbody>
						<tr>
							<td class="ng-block-td" style="width:40px;vertical-align: middle;" width="40">
								<img src="<?php echo esc_url( NGL_PLUGIN_URL . 'assets/images/social/logo-x.png' ); ?>" class="ng-image" width="30" height="30" alt="" style="width: 30px;height: auto;display: block;" />
							</td>
							<td class="ng-block-td" style="vertical-align:middle;width: 100%;">
								<strong><?php echo wp_kses_post( $data->author_name ); ?></strong><br>
								<?php echo wp_kses_post( $username ); ?>
							</td>
						</tr>
						<tr><td colspan="2" style="padding-top:10px;" class="ng-block-td ngl-ignore-mrkp"><?php echo wp_kses_post( $html ); ?></td></tr>
						<tr><td colspan="2" style="padding-top:10px;" class="ng-block-td ngl-ignore-mrkp"><?php echo wp_kses_post( $formatted_date ); ?></td></tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
