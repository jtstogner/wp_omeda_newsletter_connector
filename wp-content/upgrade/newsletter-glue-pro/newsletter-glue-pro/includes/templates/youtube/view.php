<?php
/**
 * YouTube.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<table width="100%" cellpadding="0" cellspacing="0" class="ng-block-youtube">
	<tbody>
		<tr>
			<td class="ng-block-td" style="padding:20px;">
				<table width="100%" cellpadding="0" cellspacing="0" style="table-layout: fixed !important;">
					<tbody>
						<tr>
							<td class="ng-block-td" style="width:40px !important;vertical-align: middle;" width="40">
								<img src="<?php echo esc_url( NGL_PLUGIN_URL . 'assets/images/social/logo-youtube.png' ); ?>" class="ng-image" width="30" height="30" alt="" style="width: 30px;height: auto;display: block;" />
							</td>
							<td class="ng-block-td" style="vertical-align:middle;width: 100%;">
								<strong><a href="<?php echo esc_url( $data->author_url ); ?>" rel="noopener" target="_blank" style="font-size:14px;"><?php echo wp_kses_post( $data->author_name ); ?></a></strong>
							</td>
						</tr>
						<tr>
							<td class="ng-block-td ngl-ignore-mrkp" colspan="2" style="padding-top:15px;">
								<a href="<?php echo esc_url( $url ); ?>" rel="noopener" target="_blank" style="margin:0!important;"><img src="<?php echo esc_url( $image_url ); ?>" style="display: block;margin:0 !important;" /></a>
							</td>
						</tr>
						<tr>
							<td class="ng-block-td ngl-ignore-mrkp" style="padding-top:10px;" colspan="2">
								<a href="<?php echo esc_url( $url ); ?>" rel="noopener" target="_blank"><?php echo wp_kses_post( $data->title ); ?></a>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>