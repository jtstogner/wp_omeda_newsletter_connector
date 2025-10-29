<?php
/**
 * Template.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// @codingStandardsIgnoreStart

add_filter( 'ng_default_template_10_title', 'ng_default_template_10_title' );
function ng_default_template_10_title() {
	return 'Recipes for summer';
}

add_filter( 'ng_default_template_10_theme', 'ng_default_template_10_theme' );
function ng_default_template_10_theme( $theme ) {
    $theme[ 'email_bg' ] = '#FFFFFF';
    $theme[ 'container_bg' ] = '#FFFFFF';
    $theme[ 'btn_border' ] = '#7E7E7E';
    $theme[ 'btn_bg' ] = '#7E7E7E';
    $theme[ 'a_colour' ] = '#7E7E7E';
    $theme[ 'h1_font' ] = 'arial';
    $theme[ 'h2_font' ] = 'arial';
    $theme[ 'h3_font' ] = 'arial';
    $theme[ 'h4_font' ] = 'arial';
    $theme[ 'h5_font' ] = 'arial';
    $theme[ 'h6_font' ] = 'arial';
	$theme[ 'p_font' ] = 'arial';
    $theme[ 'h1_colour' ] = '#1E1E1E';
    $theme[ 'h2_colour' ] = '#1E1E1E';
    $theme[ 'h3_colour' ] = '#1E1E1E';
    $theme[ 'h4_colour' ] = '#1E1E1E';
    $theme[ 'h5_colour' ] = '#1E1E1E';
    $theme[ 'h6_colour' ] = '#1E1E1E';
    $theme[ 'p_colour' ] = '#454545';
	$theme[ 'quickstyle' ] = 1;
	return $theme;
}

add_filter( 'ng_default_template_10_content', 'ng_default_template_10_content' );
function ng_default_template_10_content() {
	return '<!-- wp:newsletterglue/spacer -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="20" style="height:20px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/image {"align":"left","width":144,"height":40,"sizeSlug":"large","padding":{"top":"0px","bottom":"0px","left":"20px","right":"20px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block alignleft size-large is-resized"><tbody><tr><td class="ng-block-td" align="left" style="padding-top:0px;padding-bottom:0px;padding-left:20px;padding-right:20px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro-v2/assets/images/templates/logo-ipsum12.png" alt="" class="ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="144" height="40"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/image -->

<!-- wp:newsletterglue/heading {"h2_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h2_colour":"#333333"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#333333"><tbody><tr><td class="ng-block-td" align="none" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:35px;padding-bottom:15px;padding-left:20px;padding-right:20px;text-align:none;color:#333333"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:none;color:#333333"><strong>Easy and quick Summer recipes</strong></h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/text {"color":"#333333","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#333333"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#333333"><p>Summer is coming, so we’re sharing with you our top 5 delicious summer time snacks recipes you can make for your entire family in 10 minutes or less. So you can spend more time by the pool.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/spacer -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="20" style="height:20px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/container {"background":"#f3ede6","color":"#333333","margin":{"top":"0px","bottom":"0px","left":"20px","right":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"radius":"15px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#333333"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:20px" height="20"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:0px;padding-right:0px;text-align:none;color:#333333;background-color:#f3ede6;border-radius:15px"><!-- wp:newsletterglue/sections {"layout":"10_90","padding":{"top":"0px","bottom":"0px","left":"0px","right":"0px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-sections ng-block is-stacked-on-mobile"><tbody><tr><td class="ng-columns-wrap" style="padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px"><table width="100%" cellpadding="0" cellspacing="0"><tbody><tr><!-- wp:newsletterglue/section {"width":60,"originalWidth":60,"padding":{"top":"0px","bottom":"0px","left":"0px","right":"0px"}} -->
<td width="60" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:60px;padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/text {"color":"#666666","padding":{"top":"0px","right":"20px","bottom":"10px","left":"20px"},"fontsize":"48px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:48px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:0px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p>🌭</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section {"width":540,"originalWidth":540,"padding":{"top":"0px","bottom":"0px","left":"0px","right":"0px"}} -->
<td width="540" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:540px;padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/heading {"h2_padding":{"top":"0px","right":"20px","bottom":"5px","left":"20px"},"h2_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h2_colour":"#61250f"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#61250f"><tbody><tr><td class="ng-block-td" align="none" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:0px;padding-bottom:5px;padding-left:20px;padding-right:20px;text-align:none;color:#61250f"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:none;color:#61250f"><strong>Hot diggity dogs</strong></h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/text {"color":"#333333","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#333333"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#333333"><p>Create a hot dog buffet for your friends and family. Get a checklist for everything you need to buy.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/buttons -->
<table width="100%" cellpadding="0" cellspacing="0" class="ng-block"><tbody><tr><td style="padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px"><div style="gap:20px;justify-content:left" class="wp-block-newsletterglue-buttons"><!-- wp:newsletterglue/button {"background":"#b01e1e","color":"#ffffff","radius":"0px","className":""} -->
<div class="wp-block-newsletterglue-button" style="flex-basis:auto"><a class="ng-block-button__link" style="font-family:Helvetica;font-size:16px;line-height:1.5;font-weight:normal;padding-top:8px;padding-bottom:8px;padding-left:20px;padding-right:20px;text-align:center;width:auto;background-color:#b01e1e;color:#ffffff;border-width:2px;border-style:solid;border-color:#b01e1e;border-radius:0px;box-sizing:border-box">Get the hot dog checklist</a></div>
<!-- /wp:newsletterglue/button --></div></td></tr></tbody></table>
<!-- /wp:newsletterglue/buttons --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section -->
<td width="auto" class="wp-block-newsletterglue-section ng-block ng-should-remove" valign="top" style="width:auto;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:none;vertical-align:top"><!-- wp:newsletterglue/text {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td>
<!-- /wp:newsletterglue/section --></tr></tbody></table></td></tr></tbody></table>
<!-- /wp:newsletterglue/sections --></td><td class="ng-block-hs ng-block-hs-2" style="width:20px" height="20"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:0px" height="0" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->

<!-- wp:newsletterglue/spacer {"height":"25px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="25" style="height:25px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/container {"background":"#e8f2e6","color":"#333333","margin":{"top":"0px","bottom":"0px","left":"20px","right":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"radius":"15px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#333333"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:20px" height="20"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:0px;padding-right:0px;text-align:none;color:#333333;background-color:#e8f2e6;border-radius:15px"><!-- wp:newsletterglue/sections {"layout":"10_90","padding":{"top":"0px","bottom":"0px","left":"0px","right":"0px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-sections ng-block is-stacked-on-mobile"><tbody><tr><td class="ng-columns-wrap" style="padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px"><table width="100%" cellpadding="0" cellspacing="0"><tbody><tr><!-- wp:newsletterglue/section {"width":60,"originalWidth":60,"padding":{"top":"0px","bottom":"0px","left":"0px","right":"0px"}} -->
<td width="60" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:60px;padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/text {"color":"#666666","padding":{"top":"0px","right":"20px","bottom":"10px","left":"20px"},"fontsize":"48px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:48px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:0px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p>🥗</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section {"width":540,"originalWidth":540,"padding":{"top":"0px","bottom":"0px","left":"0px","right":"0px"}} -->
<td width="540" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:540px;padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/heading {"h2_padding":{"top":"0px","right":"20px","bottom":"5px","left":"20px"},"h2_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h2_colour":"#19610f"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#19610f"><tbody><tr><td class="ng-block-td" align="none" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:0px;padding-bottom:5px;padding-left:20px;padding-right:20px;text-align:none;color:#19610f"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:none;color:#19610f"><strong>Fresh chopped salads</strong></h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/text {"color":"#333333","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#333333"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#333333"><p>5 different chopped salads anyone in your family would be happy to eat. For a delicious and balanced diet.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/buttons -->
<table width="100%" cellpadding="0" cellspacing="0" class="ng-block"><tbody><tr><td style="padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px"><div style="gap:20px;justify-content:left" class="wp-block-newsletterglue-buttons"><!-- wp:newsletterglue/button {"background":"#03770d","color":"#ffffff","radius":"0px","className":""} -->
<div class="wp-block-newsletterglue-button" style="flex-basis:auto"><a class="ng-block-button__link" style="font-family:Helvetica;font-size:16px;line-height:1.5;font-weight:normal;padding-top:8px;padding-bottom:8px;padding-left:20px;padding-right:20px;text-align:center;width:auto;background-color:#03770d;color:#ffffff;border-width:2px;border-style:solid;border-color:#03770d;border-radius:0px;box-sizing:border-box">Get 5 fresh salad recipes</a></div>
<!-- /wp:newsletterglue/button --></div></td></tr></tbody></table>
<!-- /wp:newsletterglue/buttons --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section -->
<td width="auto" class="wp-block-newsletterglue-section ng-block ng-should-remove" valign="top" style="width:auto;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:none;vertical-align:top"><!-- wp:newsletterglue/text {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td>
<!-- /wp:newsletterglue/section --></tr></tbody></table></td></tr></tbody></table>
<!-- /wp:newsletterglue/sections --></td><td class="ng-block-hs ng-block-hs-2" style="width:20px" height="20"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:0px" height="0" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->

<!-- wp:newsletterglue/spacer {"height":"25px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="25" style="height:25px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/container {"background":"#fff5fc","color":"#333333","margin":{"top":"0px","bottom":"0px","left":"20px","right":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"radius":"15px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#333333"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:20px" height="20"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:0px;padding-right:0px;text-align:none;color:#333333;background-color:#fff5fc;border-radius:15px"><!-- wp:newsletterglue/sections {"layout":"10_90","padding":{"top":"0px","bottom":"0px","left":"0px","right":"0px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-sections ng-block is-stacked-on-mobile"><tbody><tr><td class="ng-columns-wrap" style="padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px"><table width="100%" cellpadding="0" cellspacing="0"><tbody><tr><!-- wp:newsletterglue/section {"width":60,"originalWidth":60,"padding":{"top":"0px","bottom":"0px","left":"0px","right":"0px"}} -->
<td width="60" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:60px;padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/text {"color":"#666666","padding":{"top":"0px","right":"20px","bottom":"10px","left":"20px"},"fontsize":"48px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:48px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:0px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p>🍨</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section {"width":540,"originalWidth":540,"padding":{"top":"0px","bottom":"0px","left":"0px","right":"0px"}} -->
<td width="540" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:540px;padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/heading {"h2_padding":{"top":"0px","right":"20px","bottom":"5px","left":"20px"},"h2_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h2_colour":"#ca3359"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#ca3359"><tbody><tr><td class="ng-block-td" align="none" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:0px;padding-bottom:5px;padding-left:20px;padding-right:20px;text-align:none;color:#ca3359"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:none;color:#ca3359"><strong>Not your average sundae</strong></h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/text {"color":"#333333","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#333333"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#333333"><p>Check out these ice cream sundae variations you can make with your pre-school aged kids.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/buttons -->
<table width="100%" cellpadding="0" cellspacing="0" class="ng-block"><tbody><tr><td style="padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px"><div style="gap:20px;justify-content:left" class="wp-block-newsletterglue-buttons"><!-- wp:newsletterglue/button {"background":"#b42347","color":"#ffffff","radius":"0px","className":""} -->
<div class="wp-block-newsletterglue-button" style="flex-basis:auto"><a class="ng-block-button__link" style="font-family:Helvetica;font-size:16px;line-height:1.5;font-weight:normal;padding-top:8px;padding-bottom:8px;padding-left:20px;padding-right:20px;text-align:center;width:auto;background-color:#b42347;color:#ffffff;border-width:2px;border-style:solid;border-color:#b42347;border-radius:0px;box-sizing:border-box">Make sundaes every day</a></div>
<!-- /wp:newsletterglue/button --></div></td></tr></tbody></table>
<!-- /wp:newsletterglue/buttons --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section -->
<td width="auto" class="wp-block-newsletterglue-section ng-block ng-should-remove" valign="top" style="width:auto;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:none;vertical-align:top"><!-- wp:newsletterglue/text {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td>
<!-- /wp:newsletterglue/section --></tr></tbody></table></td></tr></tbody></table>
<!-- /wp:newsletterglue/sections --></td><td class="ng-block-hs ng-block-hs-2" style="width:20px" height="20"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:0px" height="0" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->

<!-- wp:newsletterglue/spacer {"height":"60px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="60" style="height:60px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/text {"color":"#707070","link":"#707070","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"14px","align":"center"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="center" style="font-size:14px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:center;color:#707070"><p>Built with <a href="https://newsletterglue.com/?utm_source=newsletter&amp;utm_medium=ng-signature" target="_blank" rel="noopener noreferrer">Newsletter Glue</a>.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#707070","link":"#707070","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"14px","align":"center"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="center" style="font-size:14px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:center;color:#707070"><p>{{ admin_address,fallback=21 Park Road }}</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#707070","link":"#707070","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"14px","align":"center"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="center" style="font-size:14px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:center;color:#707070"><p>If you’d no longer like to receive emails from me, you can <a href="{{ unsubscribe_link }}">unsubscribe here</a>.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/spacer {"height":"50px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="50" style="height:50px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->';
}

// @codingStandardsIgnoreEnd
