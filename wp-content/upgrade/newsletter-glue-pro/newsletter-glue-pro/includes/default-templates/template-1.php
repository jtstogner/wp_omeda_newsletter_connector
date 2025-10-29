<?php
/**
 * Template.
 *
 * @package Newsletter Glue.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// @codingStandardsIgnoreStart

add_filter( 'ng_default_template_1_title', 'ng_default_template_1_title' );
function ng_default_template_1_title() {
	return 'Printing shop';
}

add_filter( 'ng_default_template_1_content', 'ng_default_template_1_content' );
function ng_default_template_1_content() {

	$admin_address 	= '{{ admin_address,fallback=' . get_option( 'newsletterglue_admin_address' ) . ' }}';

	return '<!-- wp:newsletterglue/container {"background":"#f9f9f9"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#666666"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:0px" height="0"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:0px;padding-right:0px;text-align:none;color:#666666;background-color:#f9f9f9;border-radius:0px"><!-- wp:newsletterglue/image {"width":144,"height":40,"sizeSlug":"large"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block size-large is-resized"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px"><img src="' . NGL_PLUGIN_URL . 'assets/images/templates/logoipsum-logo-25.png" alt="" class="ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="144" height="40"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/image --></td><td class="ng-block-hs ng-block-hs-2" style="width:0px" height="0"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:0px" height="0" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->

<!-- wp:newsletterglue/spacer -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="20" style="height:20px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/text -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p>Hi <span class="ngl-tag">{{ first_name,fallback=friend }}</span>, As we close out the year, it’s great to look back at all we’ve accomplished.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/image {"width":560,"height":373,"sizeSlug":"large","fontsize":"13px","color":"#707070"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block size-large is-resized"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:0px;padding-bottom:0;padding-left:0px;padding-right:0px"><img src="' . NGL_PLUGIN_URL . 'assets/images/templates/printing-shop-1.jpg" alt="" class="ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="560" height="373"/></td></tr><tr><td class="ng-block-caption" align="center" style="padding-left:0px;padding-right:0px;padding-bottom:0px;line-height:1.5;font-size:13px;font-family:Helvetica"><span style="color:#707070;font-size:13px;font-family:Helvetica;font-weight:normal">Photo by <a href="https://unsplash.com/@anggakrnwan?utm_source=unsplash&amp;utm_medium=referral&amp;utm_content=creditCopyText">Angga Kurniawan</a> on <a href="https://unsplash.com/?utm_source=unsplash&amp;utm_medium=referral&amp;utm_content=creditCopyText">Unsplash</a></span></td></tr></tbody></table>
<!-- /wp:newsletterglue/image -->

<!-- wp:newsletterglue/text {"padding":{"top":"20px","right":"20px","bottom":"10px","left":"20px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p>Your support and belief were instrumental in so many of our successes this year.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p>From our early days as a fledgling startup to where we are now. Thank you for coming along for the ride.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/heading -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#333333"><tbody><tr><td class="ng-block-td" align="none" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:35px;padding-bottom:15px;padding-left:20px;padding-right:20px;text-align:none;color:#333333"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:none;color:#333333"><strong>Quick look at the year</strong></h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/list {"padding":{"top":"15px","bottom":"15px","left":"40px","right":"20px"},"mobile_list_padding":{"top":"8px","bottom":"8px","left":"0px","right":"0px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-list ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:15px;padding-bottom:15px;padding-left:40px;padding-right:20px;color:#666666"><ul class="wp-block-newsletterglue-list"><!-- wp:newsletterglue/list-item -->
<li class="ng-block" style="padding-bottom:0px">42,593 t-shirts sold!</li>
<!-- /wp:newsletterglue/list-item -->

<!-- wp:newsletterglue/list-item -->
<li class="ng-block" style="padding-bottom:0px">5 of us on the Logoipsum team</li>
<!-- /wp:newsletterglue/list-item -->

<!-- wp:newsletterglue/list-item -->
<li class="ng-block" style="padding-bottom:0px">523 screen printing templates</li>
<!-- /wp:newsletterglue/list-item -->

<!-- wp:newsletterglue/list-item -->
<li class="ng-block" style="padding-bottom:0px">293 happy customers</li>
<!-- /wp:newsletterglue/list-item -->

<!-- wp:newsletterglue/list-item -->
<li class="ng-block" style="padding-bottom:0px">1429 cups of coffee</li>
<!-- /wp:newsletterglue/list-item -->

<!-- wp:newsletterglue/list-item -->
<li class="ng-block" style="padding-bottom:0px">82 late nights spent packing orders</li>
<!-- /wp:newsletterglue/list-item -->

<!-- wp:newsletterglue/list-item -->
<li class="ng-block" style="padding-bottom:0px">2 stray cats adopted</li>
<!-- /wp:newsletterglue/list-item --></ul></td></tr></tbody></table>
<!-- /wp:newsletterglue/list -->

<!-- wp:newsletterglue/heading {"h2_padding":{"top":"20px","right":"20px","bottom":"15px","left":"20px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#333333"><tbody><tr><td class="ng-block-td" align="none" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:20px;padding-bottom:15px;padding-left:20px;padding-right:20px;text-align:none;color:#333333"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:none;color:#333333"><strong>Thank you!</strong></h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/image {"width":560,"height":314,"sizeSlug":"large","fontsize":"13px","color":"#707070"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block size-large is-resized"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:0px;padding-bottom:0;padding-left:0px;padding-right:0px"><img src="' . NGL_PLUGIN_URL . 'assets/images/templates/printing-shop-2.jpg" alt="" class="ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="560" height="314"/></td></tr><tr><td class="ng-block-caption" align="center" style="padding-left:0px;padding-right:0px;padding-bottom:0px;line-height:1.5;font-size:13px;font-family:Helvetica"><span style="color:#707070;font-size:13px;font-family:Helvetica;font-weight:normal">Photo by <a href="https://unsplash.com/@eliottreyna?utm_source=unsplash&amp;utm_medium=referral&amp;utm_content=creditCopyText">Eliott Reyna</a> on <a href="https://unsplash.com/images/people?utm_source=unsplash&amp;utm_medium=referral&amp;utm_content=creditCopyText">Unsplash</a></span></td></tr></tbody></table>
<!-- /wp:newsletterglue/image -->

<!-- wp:newsletterglue/text -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p>Thanks for a great year.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"padding":{"top":"8px","right":"20px","bottom":"0px","left":"20px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:0px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p>Love,</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"padding":{"top":"0px","right":"20px","bottom":"10px","left":"20px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:0px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p>All of us!</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/spacer {"height":"40px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="40" style="height:40px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/container {"background":"#f9f9f9"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#666666"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:0px" height="0"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:0px;padding-right:0px;text-align:none;color:#666666;background-color:#f9f9f9;border-radius:0px"><!-- wp:newsletterglue/text {"color":"#225482"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#225482"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#225482"><p>Shop now</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#225482"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#225482"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#225482"><p>Mens</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#225482"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#225482"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#225482"><p>Womens</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#225482"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#225482"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#225482"><p>Kids</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/spacer {"height":"40px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="40" style="height:40px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/text {"color":"#707070","link":"#707070","fontsize":"13px","align":"center"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="center" style="font-size:13px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:center;color:#707070"><p>Built with <a href="https://newsletterglue.com/?utm_source=newsletter&amp;utm_medium=ng-signature" target="_blank" rel="noopener noreferrer">Newsletter Glue</a>.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/spacer -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="20" style="height:20px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/sections {"layout":"50_50"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-sections ng-block is-stacked-on-mobile"><tbody><tr><td class="ng-columns-wrap" style="padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px"><table width="100%" cellpadding="0" cellspacing="0"><tbody><tr><!-- wp:newsletterglue/section {"width":300,"originalWidth":300} -->
<td width="300" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:300px;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/social-icons {"add_description":false,"icon_shape":"default","icon_color":"grey","icon_size":"18px","gap":"10px","padding":{"top":"0px","bottom":"20px","left":"20px","right":"20px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-social-icons ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:0px;padding-bottom:20px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><div class="ngl-share-wrap ng-div" style="line-height:1;font-size:1px"><!-- wp:newsletterglue/social-icon {"service":"x","icon_size":"18px","gap":"10px","icon_shape":"default","icon_color":"grey"} -->
<span class="wp-block-newsletterglue-social-icon ng-block ng-social-x" style="display:inline-flex;margin-right:10px;margin-left:0px"><a rel="noopener noreferrer" target="_blank"><img src="' . NGL_PLUGIN_URL . 'assets/images/share/default/grey/x.png" width="18" height="18" style="width:18px;height:18px" class="ngl-inline-image"/></a></span>
<!-- /wp:newsletterglue/social-icon -->

<!-- wp:newsletterglue/social-icon {"service":"instagram","icon_size":"18px","gap":"10px","icon_shape":"default","icon_color":"grey"} -->
<span class="wp-block-newsletterglue-social-icon ng-block ng-social-instagram" style="display:inline-flex;margin-right:10px;margin-left:0px"><a rel="noopener noreferrer" target="_blank"><img src="' . NGL_PLUGIN_URL . 'assets/images/share/default/grey/instagram.png" width="18" height="18" style="width:18px;height:18px" class="ngl-inline-image"/></a></span>
<!-- /wp:newsletterglue/social-icon --></div></td></tr></tbody></table>
<!-- /wp:newsletterglue/social-icons --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section {"width":300,"originalWidth":300} -->
<td width="300" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:300px;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/text {"color":"#707070","padding":{"top":"6px","right":"20px","bottom":"10px","left":"20px"},"fontsize":"13px","align":"right"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="right" style="font-size:13px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:6px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:right;color:#707070"><p>' . esc_attr( $admin_address ) . '</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"link":"#707070","fontsize":"13px","align":"right"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="right" style="font-size:13px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:right;color:#666666"><p><a href="{{ unsubscribe_link }}"><span style="color:#707070" class="has-inline-color">Unsubscribe here.</span></a></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section -->
<td width="auto" class="wp-block-newsletterglue-section ng-block ng-should-remove" valign="top" style="width:auto;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:none;vertical-align:top"><!-- wp:newsletterglue/text -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td>
<!-- /wp:newsletterglue/section --></tr></tbody></table></td></tr></tbody></table>
<!-- /wp:newsletterglue/sections --></td><td class="ng-block-hs ng-block-hs-2" style="width:0px" height="0"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:0px" height="0" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->';
}

// @codingStandardsIgnoreEnd
