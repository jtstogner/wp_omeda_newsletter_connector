<?php
/**
 * Soundcloud.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<table width="100%" cellpadding="0" cellspacing="0" class="ng-block-soundcloud">
	<tbody>
		<tr>
			<td class="ng-block-td" style="padding:20px;">
				<table width="100%" cellpadding="0" cellspacing="0" style="table-layout: fixed !important;">
					<tbody>
						<tr>
							<td class="ng-block-td ngl-ignore-mrkp" width="140">
								<a href="<?php echo urldecode( trim( $url ) ); ?>" rel="noopener" target="_blank"><img src="<?php echo esc_url( $data->thumbnail_url ); ?>" style="display:block;width: 120px;height: auto;" width="120" height="auto" /></a>
							</td>
							<td class="ng-block-td ngl-ignore-mrkp" style="vertical-align:top;padding-right:15px;">
								<a href="<?php echo urldecode( trim( $url ) ); ?>" rel="noopener" target="_blank"><?php echo esc_html( $data->title ); ?></a><br />
								<span><?php echo esc_html( $data->author_name ); ?></span>
							</td>
							<td class="ng-block-td ngl-ignore-mrkp" style="vertical-align:top;width:30px;" width="30">
								<img src="<?php echo esc_url( NGL_PLUGIN_URL . 'assets/images/social/soundcloud.png' ); ?>" class="ng-image" width="30" height="30" alt="" style="width: 30px;height: auto;display: block;" />
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>