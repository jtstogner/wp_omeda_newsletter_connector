<?php
/**
 * Template.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// @codingStandardsIgnoreStart

add_filter( 'ng_default_template_4_title', 'ng_default_template_4_title' );
function ng_default_template_4_title() {
	return 'SaaS monthly update';
}

add_filter( 'ng_default_template_4_theme', 'ng_default_template_4_theme' );
function ng_default_template_4_theme( $theme ) {
	$theme[ 'email_bg' ] = '#FFFFFF';
	return $theme;
}

add_filter( 'ng_default_template_4_content', 'ng_default_template_4_content' );
function ng_default_template_4_content() {
	return '<!-- wp:newsletterglue/container {"background":"#f8f8ff","color":"#666666"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#666666"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:0px" height="0"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:0px;padding-right:0px;text-align:none;color:#666666;background-color:#f8f8ff;border-radius:0px"><!-- wp:newsletterglue/image {"width":167,"height":40,"sizeSlug":"large"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block size-large is-resized"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro-v2/assets/images/templates/logoipsum-logo-51.png" alt="" class="ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="167" height="40"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/image -->

<!-- wp:newsletterglue/spacer -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="20" style="height:20px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/heading {"textAlign":"center","h1_colour":"#666666","h2_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h2_colour":"#333333","h3_colour":"#666666","h4_colour":"#666666","h5_colour":"#666666","h6_colour":"#666666"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#333333"><tbody><tr><td class="ng-block-td" align="center" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:35px;padding-bottom:15px;padding-left:20px;padding-right:20px;text-align:center;color:#333333"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:center;color:#333333"><strong>Hello February!</strong></h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/text {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"align":"left"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="left" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:left;color:#666666"><p>We’ve got lots of great stuff in store for you this month. A brand new case study, some articles we shared around our office and a secret promotion. Let’s do this. 💪</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td><td class="ng-block-hs ng-block-hs-2" style="width:0px" height="0"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:0px" height="0" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->

<!-- wp:newsletterglue/spacer {"height":"60px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="60" style="height:60px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/container {"background":"#f8f8ff","color":"#666666"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#666666"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:0px" height="0"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:0px;padding-right:0px;text-align:none;color:#666666;background-color:#f8f8ff;border-radius:0px"><!-- wp:newsletterglue/heading {"textAlign":"left","h1_colour":"#666666","h2_padding":{"top":"0px","right":"20px","bottom":"15px","left":"20px"},"h2_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h2_colour":"#333333","h3_colour":"#666666","h4_colour":"#666666","h5_colour":"#666666","h6_colour":"#666666"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#333333"><tbody><tr><td class="ng-block-td" align="left" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:0px;padding-bottom:15px;padding-left:20px;padding-right:20px;text-align:left;color:#333333"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:left;color:#333333"><strong>💅 We built a new website for ACME. It’s converting 57% better.</strong></h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/image {"width":560,"height":360,"sizeSlug":"large","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"13px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block size-large is-resized"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:0px;padding-bottom:0;padding-left:0px;padding-right:0px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro-v2/assets/images/templates/saas-1.jpg" alt="" class="ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="560" height="360"/></td></tr><tr><td class="ng-block-caption" align="center" style="padding-left:0px;padding-right:0px;padding-bottom:0px;line-height:1.5;font-size:13px;font-family:Helvetica"><span style="color:#666666;font-size:13px;font-family:Helvetica;font-weight:normal">Photo by <a href="https://unsplash.com/@jasongoodman_youxventures?utm_source=unsplash&amp;utm_medium=referral&amp;utm_content=creditCopyText">Jason Goodman</a> on <a href="https://unsplash.com/s/photos/tech-company?utm_source=unsplash&amp;utm_medium=referral&amp;utm_content=creditCopyText">Unsplash</a></span></td></tr></tbody></table>
<!-- /wp:newsletterglue/image -->

<!-- wp:newsletterglue/text {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p>We helped ACME build a new website in conjunction with their brand refresh. New brand, new website, same amazing business. It’s already converting 57% better than the old one.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/buttons {"justify":"center"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="ng-block"><tbody><tr><td style="padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px"><div style="gap:20px;justify-content:center" class="wp-block-newsletterglue-buttons"><!-- wp:newsletterglue/button {"radius":"0px","className":""} -->
<div class="wp-block-newsletterglue-button" style="flex-basis:auto"><a class="ng-block-button__link" style="font-family:Helvetica;font-size:16px;line-height:1.5;font-weight:normal;padding-top:8px;padding-bottom:8px;padding-left:20px;padding-right:20px;text-align:center;width:auto;background-color:#0088a0;color:#ffffff;border-width:2px;border-style:solid;border-color:#0088a0;border-radius:0px;box-sizing:border-box">Learn more</a></div>
<!-- /wp:newsletterglue/button --></div></td></tr></tbody></table>
<!-- /wp:newsletterglue/buttons --></td><td class="ng-block-hs ng-block-hs-2" style="width:0px" height="0"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:0px" height="0" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->

<!-- wp:newsletterglue/spacer {"height":"60px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="60" style="height:60px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/container {"background":"#f8f8ff","color":"#666666"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#666666"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:0px" height="0"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:0px;padding-right:0px;text-align:none;color:#666666;background-color:#f8f8ff;border-radius:0px"><!-- wp:newsletterglue/heading {"h2_padding":{"top":"0px","right":"20px","bottom":"15px","left":"20px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#333333"><tbody><tr><td class="ng-block-td" align="none" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:0px;padding-bottom:15px;padding-left:20px;padding-right:20px;text-align:none;color:#333333"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:none;color:#333333"><strong>👩‍💻 The job board</strong></h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/sections {"layout":"20_80"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-sections ng-block is-stacked-on-mobile"><tbody><tr><td class="ng-columns-wrap" style="padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px"><table width="100%" cellpadding="0" cellspacing="0"><tbody><tr><!-- wp:newsletterglue/section {"width":120,"originalWidth":120} -->
<td width="120" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:120px;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/image {"width":45,"height":45,"sizeSlug":"large"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block size-large is-resized"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro-v2/assets/images/templates/logoipsum-logo-15-1.png" alt="" class="ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="45" height="45"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/image --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section {"width":480,"originalWidth":480} -->
<td width="480" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:480px;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/text {"color":"#666666","padding":{"top":"0px","right":"20px","bottom":"10px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:0px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p><strong><span style="text-decoration: underline;">UX researcher at Beep</span></strong></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p>Understand user and business goals. Work with Product Design Lead on UX Team. Define information architecture, user journeys, and personas and be a strong advocate for UX.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section -->
<td width="auto" class="wp-block-newsletterglue-section ng-block ng-should-remove" valign="top" style="width:auto;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:none;vertical-align:top"><!-- wp:newsletterglue/text -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td>
<!-- /wp:newsletterglue/section --></tr></tbody></table></td></tr></tbody></table>
<!-- /wp:newsletterglue/sections -->

<!-- wp:newsletterglue/sections {"layout":"20_80"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-sections ng-block is-stacked-on-mobile"><tbody><tr><td class="ng-columns-wrap" style="padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px"><table width="100%" cellpadding="0" cellspacing="0"><tbody><tr><!-- wp:newsletterglue/section {"width":120,"originalWidth":120} -->
<td width="120" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:120px;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/image {"threshold":120,"width":45,"height":45,"sizeSlug":"large"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block size-large is-resized"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro-v2/assets/images/templates/logoipsum-logo-35.png" alt="" class="ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="45" height="45"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/image --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section {"width":480,"originalWidth":480} -->
<td width="480" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:480px;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/text {"color":"#666666","padding":{"top":"0px","right":"20px","bottom":"10px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:0px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p><strong><span style="text-decoration: underline;">Senior UI Designer at Screenr</span></strong></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p>We’re looking for a Senior UX/UI Designer focused on accessibility and inclusive design to join our product team.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section -->
<td width="auto" class="wp-block-newsletterglue-section ng-block ng-should-remove" valign="top" style="width:auto;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:none;vertical-align:top"><!-- wp:newsletterglue/text -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td>
<!-- /wp:newsletterglue/section --></tr></tbody></table></td></tr></tbody></table>
<!-- /wp:newsletterglue/sections -->

<!-- wp:newsletterglue/sections {"layout":"20_80"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-sections ng-block is-stacked-on-mobile"><tbody><tr><td class="ng-columns-wrap" style="padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px"><table width="100%" cellpadding="0" cellspacing="0"><tbody><tr><!-- wp:newsletterglue/section {"width":120,"originalWidth":120} -->
<td width="120" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:120px;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/image {"threshold":120,"width":45,"height":45,"sizeSlug":"large"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block size-large is-resized"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro-v2/assets/images/templates/logoipsum-logo-36-1.png" alt="" class="ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="45" height="45"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/image --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section {"width":480,"originalWidth":480} -->
<td width="480" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:480px;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/text {"color":"#666666","padding":{"top":"0px","right":"20px","bottom":"10px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:0px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p><strong><span style="text-decoration: underline;">Engineering Manager at Loading</span></strong></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p>As an Engineering Manager, you will be managing a group of 6-8 engineers. You will ensure they collaborate effectively, and that everyone in the group is excited to contribute.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section -->
<td width="auto" class="wp-block-newsletterglue-section ng-block ng-should-remove" valign="top" style="width:auto;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:none;vertical-align:top"><!-- wp:newsletterglue/text -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td>
<!-- /wp:newsletterglue/section --></tr></tbody></table></td></tr></tbody></table>
<!-- /wp:newsletterglue/sections --></td><td class="ng-block-hs ng-block-hs-2" style="width:0px" height="0"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:0px" height="0" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->

<!-- wp:newsletterglue/spacer {"height":"60px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="60" style="height:60px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/container {"background":"#f8f8ff","color":"#666666"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#666666"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:0px" height="0"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:0px;padding-right:0px;text-align:none;color:#666666;background-color:#f8f8ff;border-radius:0px"><!-- wp:newsletterglue/heading {"textAlign":"left","h2_padding":{"top":"0px","right":"20px","bottom":"15px","left":"20px"},"h2_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h2_colour":"#333333"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#333333"><tbody><tr><td class="ng-block-td" align="left" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:0px;padding-bottom:15px;padding-left:20px;padding-right:20px;text-align:left;color:#333333"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:left;color:#333333"><strong>📰 News bites</strong></h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/text {"color":"#666666","padding":{"top":"8px","right":"20px","bottom":"5px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"align":"left"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="left" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:5px;padding-left:20px;padding-right:20px;text-align:left;color:#666666"><p>Why VCs don’t need to feat a financial slowdown – <strong><a href="https://techcrunch.com/2022/04/05/why-vcs-dont-need-to-fear-a-financial-slowdown/"><span style="text-decoration: underline;">TechCrunch</span></a></strong></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#666666","padding":{"top":"8px","right":"20px","bottom":"5px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"align":"left"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="left" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:5px;padding-left:20px;padding-right:20px;text-align:left;color:#666666"><p>How I’d grow Shopify app, Skio – <strong><span style="text-decoration: underline;"><a href="https://techcrunch.com/2022/04/05/how-to-choose-a-growth-strategy-startup/">TechCrunch</a></span></strong> (possible paywall)</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#666666","padding":{"top":"8px","right":"20px","bottom":"5px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"align":"left"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="left" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:5px;padding-left:20px;padding-right:20px;text-align:left;color:#666666"><p>Twitter is working on an edit button – <a href="https://techcrunch.com/2022/04/05/twitter-edit-button-twitter-blue/"><strong><span style="text-decoration: underline;">TechCrunch</span></strong></a></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td><td class="ng-block-hs ng-block-hs-2" style="width:0px" height="0"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:0px" height="0" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->

<!-- wp:newsletterglue/spacer {"height":"60px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="60" style="height:60px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/container {"background":"#f8f8ff","color":"#666666"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#666666"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:0px" height="0"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:0px;padding-right:0px;text-align:none;color:#666666;background-color:#f8f8ff;border-radius:0px"><!-- wp:newsletterglue/heading {"textAlign":"left","h2_padding":{"top":"0px","right":"20px","bottom":"15px","left":"20px"},"h2_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h2_colour":"#333333"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#333333"><tbody><tr><td class="ng-block-td" align="left" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:0px;padding-bottom:15px;padding-left:20px;padding-right:20px;text-align:left;color:#333333"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:left;color:#333333"><strong>Before you go</strong></h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/text {"color":"#666666","padding":{"top":"8px","right":"20px","bottom":"5px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"align":"left"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="left" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:5px;padding-left:20px;padding-right:20px;text-align:left;color:#666666"><p>Here are a few things you can do if you enjoyed reading this newsletter:</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#666666","padding":{"top":"8px","right":"20px","bottom":"5px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"align":"left"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="left" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:5px;padding-left:20px;padding-right:20px;text-align:left;color:#666666"><p>Explore <a href="/newsletter/archive">past issues</a></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#666666","padding":{"top":"8px","right":"20px","bottom":"5px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"align":"left"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="left" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:5px;padding-left:20px;padding-right:20px;text-align:left;color:#666666"><p>Share <a href="{{ webversion }}">this newsletter</a> with a friend.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#666666","padding":{"top":"8px","right":"20px","bottom":"5px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"align":"left"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="left" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:5px;padding-left:20px;padding-right:20px;text-align:left;color:#666666"><p>Get in touch/share cool stuff: coolstuff@youremail.com</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td><td class="ng-block-hs ng-block-hs-2" style="width:0px" height="0"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:0px" height="0" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->

<!-- wp:newsletterglue/spacer {"height":"40px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="40" style="height:40px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/container {"background":"#ffffff","color":"#666666"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#666666"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:0px" height="0"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:0px;padding-right:0px;text-align:none;color:#666666;background-color:#ffffff;border-radius:0px"><!-- wp:newsletterglue/text {"color":"#707070","link":"#707070","padding":{"top":"8px","right":"20px","bottom":"20px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"13px","align":"center"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="center" style="font-size:13px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:20px;padding-left:20px;padding-right:20px;text-align:center;color:#707070"><p>Built with <a href="https://newsletterglue.com/?utm_source=newsletter&amp;utm_medium=ng-signature" target="_blank" rel="noopener noreferrer">Newsletter Glue</a>.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#707070","link":"#707070","padding":{"top":"15px","right":"20px","bottom":"0px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"13px","align":"center"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="center" style="font-size:13px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:15px;padding-bottom:0px;padding-left:20px;padding-right:20px;text-align:center;color:#707070"><p>{{ admin_address,fallback=21 Park Road }}</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#707070","link":"#707070","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"13px","align":"center"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="center" style="font-size:13px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:center;color:#707070"><p>If you’d no longer like to receive emails from me, you can <a href="{{ unsubscribe_link }}">unsubscribe here</a>.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td><td class="ng-block-hs ng-block-hs-2" style="width:0px" height="0"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:0px" height="0" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->';
}

// @codingStandardsIgnoreEnd
