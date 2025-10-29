<?php
/**
 * Template.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// @codingStandardsIgnoreStart

add_filter( 'ng_default_template_13_title', 'ng_default_template_13_title' );
function ng_default_template_13_title() {
	return 'Monthly recap';
}

add_filter( 'ng_default_template_13_theme', 'ng_default_template_13_theme' );
function ng_default_template_13_theme( $theme ) {
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

add_filter( 'ng_default_template_13_content', 'ng_default_template_13_content' );
function ng_default_template_13_content() {
	return '<!-- wp:newsletterglue/spacer -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="20" style="height:20px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/image {"align":"center","width":144,"height":40,"sizeSlug":"large","padding":{"top":"0px","bottom":"0px","left":"20px","right":"20px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block aligncenter size-large is-resized"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:0px;padding-bottom:0px;padding-left:20px;padding-right:20px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro-v2/assets/images/templates/logoipsum-logo-25.png" alt="" class="ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="144" height="40"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/image -->

<!-- wp:newsletterglue/heading {"h2_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h2_colour":"#333333"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#333333"><tbody><tr><td class="ng-block-td" align="none" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:35px;padding-bottom:15px;padding-left:20px;padding-right:20px;text-align:none;color:#333333"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:none;color:#333333"><strong>ICYMI: Our monthly recap</strong></h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/text {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p>In case you missed it, here’s a curated list of all the posts we published. For your reading pleasure.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/spacer -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="20" style="height:20px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/latest-posts {"containerWidth":"560px","itemBase":"270px","div1":"158px","div2":"382px","dates":{"label":"Last 30 days","value":"last_30"},"posts_num":0,"columns_num":"two","update_posts":4.9530390672539255,"posts":null,"hash":null} /-->

<!-- wp:newsletterglue/spacer {"height":"40px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="40" style="height:40px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/container {"background":"#f9f9f9","color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#666666"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:0px" height="0"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:0px;padding-right:0px;text-align:none;color:#666666;background-color:#f9f9f9;border-radius:0px"><!-- wp:newsletterglue/text {"color":"#707070","link":"#707070","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"13px","align":"center"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="center" style="font-size:13px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:center;color:#707070"><p>Built with <a href="https://newsletterglue.com/?utm_source=newsletter&amp;utm_medium=ng-signature" target="_blank" rel="noopener noreferrer">Newsletter Glue</a>.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/spacer -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="20" style="height:20px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/sections {"layout":"50_50"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-sections ng-block is-stacked-on-mobile"><tbody><tr><td class="ng-columns-wrap" style="padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px"><table width="100%" cellpadding="0" cellspacing="0"><tbody><tr><!-- wp:newsletterglue/section {"width":300,"originalWidth":300} -->
<td width="300" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:300px;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/text {"color":"#707070","fontsize":"12px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="none" style="font-size:12px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#707070"><p>Published with ♥ by {{ admin_name,fallback=Newsletter Glue }}.<br>Here’s where you can find me online:</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/social-icons {"add_description":false,"icon_size":"20px","color":"#666666","padding":{"top":"0px","bottom":"20px","left":"20px","right":"20px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-social-icons ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:0px;padding-bottom:20px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><div class="ngl-share-wrap ng-div" style="line-height:1;font-size:1px"><!-- wp:newsletterglue/social-icon {"service":"x","icon_size":"20px"} -->
<span class="wp-block-newsletterglue-social-icon ng-block ng-social-x" style="display:inline-flex;margin-right:5px;margin-left:0px"><a rel="noopener noreferrer" target="_blank"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro/assets/images/share/round/black/x.png" width="20" height="20" style="width:20px;height:20px" class="ngl-inline-image"/></a></span>
<!-- /wp:newsletterglue/social-icon -->

<!-- wp:newsletterglue/social-icon {"service":"facebook","icon_size":"20px"} -->
<span class="wp-block-newsletterglue-social-icon ng-block ng-social-facebook" style="display:inline-flex;margin-right:5px;margin-left:0px"><a rel="noopener noreferrer" target="_blank"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro/assets/images/share/round/black/facebook.png" width="20" height="20" style="width:20px;height:20px" class="ngl-inline-image"/></a></span>
<!-- /wp:newsletterglue/social-icon -->

<!-- wp:newsletterglue/social-icon {"service":"instagram","icon_size":"20px"} -->
<span class="wp-block-newsletterglue-social-icon ng-block ng-social-instagram" style="display:inline-flex;margin-right:5px;margin-left:0px"><a rel="noopener noreferrer" target="_blank"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro/assets/images/share/round/black/instagram.png" width="20" height="20" style="width:20px;height:20px" class="ngl-inline-image"/></a></span>
<!-- /wp:newsletterglue/social-icon --></div></td></tr></tbody></table>
<!-- /wp:newsletterglue/social-icons --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section {"width":300,"originalWidth":300} -->
<td width="300" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:300px;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/text {"color":"#707070","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"12px","align":"right"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="right" style="font-size:12px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:right;color:#707070"><p>{{ admin_address,fallback=21 Park Road }}</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#707070","link":"#707070","fontsize":"12px","align":"right"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="right" style="font-size:12px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:right;color:#707070"><p><a href="{{ unsubscribe_link }}"><span style="color:#707070" class="has-inline-color">Unsubscribe here.</span></a></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section -->
<td width="auto" class="wp-block-newsletterglue-section ng-block ng-should-remove" valign="top" style="width:auto;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:none;vertical-align:top"><!-- wp:newsletterglue/text {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td>
<!-- /wp:newsletterglue/section --></tr></tbody></table></td></tr></tbody></table>
<!-- /wp:newsletterglue/sections --></td><td class="ng-block-hs ng-block-hs-2" style="width:0px" height="0"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:0px" height="0" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->';
}

// @codingStandardsIgnoreEnd
