<?php
/**
 * Reddit.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<table width="100%" cellpadding="0" cellspacing="0" class="ng-block-reddit">
	<tbody>
		<tr>
			<td class="ng-block-td" style="padding:20px;">
				<table width="100%" cellpadding="0" cellspacing="0" style="table-layout: fixed !important;">
					<tbody>
						<tr>
							<td class="ng-block-td ngl-ignore-mrkp" style="width:40px;vertical-align:middle;" width="40">
								<img src="<?php echo esc_url( NGL_PLUGIN_URL . 'assets/images/social/reddit.png' ); ?>" class="ng-image" width="30" height="30" alt="" style="width: 30px;height: auto;display: block;" />
							</td>
							<td class="ng-block-td ngl-ignore-mrkp" style="width: auto;vertical-align:middle;" width="auto">
								<?php echo wp_kses_post( $clean_html ); ?>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>