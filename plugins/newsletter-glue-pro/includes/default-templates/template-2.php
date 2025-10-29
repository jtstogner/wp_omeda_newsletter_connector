<?php
/**
 * Template.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// @codingStandardsIgnoreStart

add_filter( 'ng_default_template_2_title', 'ng_default_template_2_title' );
function ng_default_template_2_title() {
	return 'Design';
}

add_filter( 'ng_default_template_2_theme', 'ng_default_template_2_theme' );
function ng_default_template_2_theme( $theme ) {
    $theme[ 'email_bg' ] = '#1E1E1E';
    $theme[ 'container_bg' ] = '#1E1E1E';
    $theme[ 'h1_colour' ] = '#c89ee3';
    $theme[ 'btn_border' ] = '#c56eff';
    $theme[ 'btn_bg' ] = '#c56eff';
    $theme[ 'a_colour' ] = '#c56eff';
    $theme[ 'h1_font' ] = 'arial';
    $theme[ 'h2_font' ] = 'arial';
    $theme[ 'h3_font' ] = 'arial';
    $theme[ 'h4_font' ] = 'arial';
    $theme[ 'h5_font' ] = 'arial';
    $theme[ 'h6_font' ] = 'arial';
    $theme[ 'p_font' ] = 'arial';
    $theme[ 'h2_colour' ] = '#ceb0e1';
    $theme[ 'h3_colour' ] = '#eaeaea';
    $theme[ 'h4_colour' ] = '#eaeaea';
    $theme[ 'h5_colour' ] = '#eaeaea';
    $theme[ 'h6_colour' ] = '#eaeaea';
    $theme[ 'p_colour' ] = '#c3c3c3';
    $theme[ 'quickstyle' ] = 1;
    $theme[ 'btn_colour' ] = '#FFFFFF';
	return $theme;
}

add_filter( 'ng_default_template_2_content', 'ng_default_template_2_content' );
function ng_default_template_2_content() {

    $admin_name     = '{{ admin_name,fallback=' . get_option( 'newsletterglue_admin_name', get_bloginfo( 'name' ) ) . ' }}';
    $admin_address  = '{{ admin_address,fallback=' . get_option( 'newsletterglue_admin_address' ) . ' }}';

	return '<!-- wp:newsletterglue/container {"background":"#181818"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#666666"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:0px" height="0"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:0px;padding-right:0px;text-align:none;color:#666666;background-color:#181818;border-radius:0px"><!-- wp:newsletterglue/image {"width":560,"height":420,"sizeSlug":"large","color":"#ffffff"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block size-large is-resized"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:0px;padding-bottom:0;padding-left:0px;padding-right:0px"><img src="' . NGL_PLUGIN_URL . 'assets/images/templates/design-1.jpg" alt="" class="ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="560" height="420"/></td></tr><tr><td class="ng-block-caption" align="center" style="padding-left:0px;padding-right:0px;padding-bottom:0px;line-height:1.5;font-size:14px;font-family:Helvetica"><span style="color:#ffffff;font-size:14px;font-family:Helvetica;font-weight:normal"><mark style="background-color:rgba(0, 0, 0, 0);color:#ffffff" class="has-inline-color">Photo by</mark><mark style="background-color:rgba(0, 0, 0, 0)" class="has-inline-color has-background-color"> </mark><a href="https://unsplash.com/@jimmyhun?utm_source=unsplash&amp;utm_medium=referral&amp;utm_content=creditCopyText"><mark style="background-color:rgba(0, 0, 0, 0);color:#c56eff" class="has-inline-color">Imre Magyar</mark></a><mark style="background-color:rgba(0, 0, 0, 0)" class="has-inline-color has-background-color"> </mark><mark style="background-color:rgba(0, 0, 0, 0);color:#ffffff" class="has-inline-color">on</mark><mark style="background-color:rgba(0, 0, 0, 0)" class="has-inline-color has-background-color"> </mark><a href="https://unsplash.com/?utm_source=unsplash&amp;utm_medium=referral&amp;utm_content=creditCopyText"><mark style="background-color:rgba(0, 0, 0, 0);color:#c56eff" class="has-inline-color">Unsplash</mark></a></span></td></tr></tbody></table>
<!-- /wp:newsletterglue/image -->

<!-- wp:newsletterglue/heading {"h2_padding":{"top":"40px","right":"20px","bottom":"15px","left":"20px"},"h2_colour":"#ffffff"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#ffffff"><tbody><tr><td class="ng-block-td" align="none" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:40px;padding-bottom:15px;padding-left:20px;padding-right:20px;text-align:none;color:#ffffff"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:none;color:#ffffff"><strong>ACME on the streets</strong></h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/text {"color":"#ffffff"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#ffffff"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#ffffff"><p>Creativity is the secret sauce.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#ffffff"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#ffffff"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#ffffff"><p>Creativity can be a key differentiator for your company and give you the competitive edge. At ACME, we’ll help you discover how good design can impact your bottom line.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#ffffff"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#ffffff"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#ffffff"><p>"Design" in today’s context means building cool stuff; but it also includes all the little details that make up creativity itself.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#ffffff"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#ffffff"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#ffffff"><p>Like using ACME to bring together beautiful illustrations into three-dimensional art on large paper creations with great color contrast. We’re looking forward by investing heavily at an exciting time where digital artists will have access not only to ACME but many other powerful visual applications.<br>Best,</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/post-author {"author_name":"\u003cmark style=\u0022background-color:rgba(0, 0, 0, 0);color:#ffffff\u0022 class=\u0022has-inline-color\u0022\u003eLuna Cattermeister\u003c/mark\u003e","author_bio":"\u003cmark style=\u0022background-color:rgba(0, 0, 0, 0);color:#ffffff\u0022 class=\u0022has-inline-color\u0022\u003eFounder of ACME\u003c/mark\u003e","button_text":"Follow","profile_pic":""} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-post-author ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" style="font-size:14px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px;color:#666666"><table width="auto" cellpadding="0" cellspacing="0"><tbody><tr><td style="width:50px;padding:0 12px 0 0;vertical-align:top"><img src="http://0.gravatar.com/avatar/045ef9284c033798e402b62c6e28a4b7?s=96&amp;d=mm&amp;r=g" width="50" height="50" class="ng-image" style="width:50px;height:50px;display:inline-block;margin:0;vertical-align:top;border-radius:50px"/></td><td style="vertical-align:top"><div class="ng-div" style="font-weight:bold;margin-bottom:2px"><mark style="background-color:rgba(0, 0, 0, 0);color:#ffffff" class="has-inline-color">Luna Cattermeister</mark></div><div class="ng-div" style="margin-bottom:8px"><mark style="background-color:rgba(0, 0, 0, 0);color:#ffffff" class="has-inline-color">Founder of ACME</mark></div><div class="ng-div ng-block-button"><a rel="nofollow" style="padding:2px 12px;display:inline-block;border-radius:5px;color:#fff;border:2px solid #1DA1F2;background-color:#1DA1F2"><img src="' . NGL_PLUGIN_URL . 'assets/images/ui//social/twitter.png" width="16" height="16" class="ng-image" style="width:16px;height:16px;display:inline-block;margin:0 2px 0 0;border-radius:16px;vertical-align:sub"/><span>Follow</span></a></div></td></tr></tbody></table></td></tr></tbody></table>
<!-- /wp:newsletterglue/post-author -->

<!-- wp:newsletterglue/spacer -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="20" style="height:20px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/container {"background":"#3f0063","margin":{"top":"20px","bottom":"20px","left":"20px","right":"20px"},"radius":"14px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#666666"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:20px" height="20" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:20px" height="20"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:0px;padding-right:0px;text-align:none;color:#666666;background-color:#3f0063;border-radius:14px"><!-- wp:newsletterglue/text {"color":"#ffffff"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#ffffff"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#ffffff"><p><strong>Sign up for ACME LIVE</strong></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#ffffff","padding":{"top":"0px","right":"20px","bottom":"10px","left":"20px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#ffffff"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:0px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#ffffff"><p>Register by October 15 for a chance to win<br>one-of-a-kind items designed by members of the<br>community.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/buttons -->
<table width="100%" cellpadding="0" cellspacing="0" class="ng-block"><tbody><tr><td style="padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px"><div style="gap:20px;justify-content:left" class="wp-block-newsletterglue-buttons"><!-- wp:newsletterglue/button {"background":"#f3f3f3","color":"#373737","radius":"0px","className":""} -->
<div class="wp-block-newsletterglue-button" style="flex-basis:auto"><a class="ng-block-button__link" style="font-family:Helvetica;font-size:16px;line-height:1.5;font-weight:normal;padding-top:8px;padding-bottom:8px;padding-left:20px;padding-right:20px;text-align:center;width:auto;background-color:#f3f3f3;color:#373737;border-width:2px;border-style:solid;border-color:#f3f3f3;border-radius:0px;box-sizing:border-box">Register Now</a></div>
<!-- /wp:newsletterglue/button --></div></td></tr></tbody></table>
<!-- /wp:newsletterglue/buttons --></td><td class="ng-block-hs ng-block-hs-2" style="width:20px" height="20"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:20px" height="20" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->

<!-- wp:newsletterglue/spacer -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="20" style="height:20px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer --></td><td class="ng-block-hs ng-block-hs-2" style="width:0px" height="0"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:0px" height="0" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->

<!-- wp:newsletterglue/container {"background":"#ffffff"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#666666"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:0px" height="0"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:0px;padding-right:0px;text-align:none;color:#666666;background-color:#ffffff;border-radius:0px"><!-- wp:newsletterglue/sections {"layout":"50_50"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-sections ng-block is-stacked-on-mobile"><tbody><tr><td class="ng-columns-wrap" style="padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px"><table width="100%" cellpadding="0" cellspacing="0"><tbody><tr><!-- wp:newsletterglue/section {"width":300,"originalWidth":300} -->
<td width="300" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:300px;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/text {"color":"#707070","fontsize":"12px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="none" style="font-size:12px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#707070"><p>Published with ♥ by ' . esc_attr( $admin_name ) . '.<br>Here’s where you can find me online:</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/social-icons {"add_description":false,"icon_shape":"default","icon_color":"color","icon_size":"18px","gap":"8px","padding":{"top":"0px","bottom":"20px","left":"20px","right":"20px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-social-icons ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:0px;padding-bottom:20px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><div class="ngl-share-wrap ng-div" style="line-height:1;font-size:1px"><!-- wp:newsletterglue/social-icon {"service":"x","icon_size":"18px","gap":"8px","icon_shape":"default","icon_color":"color"} -->
<span class="wp-block-newsletterglue-social-icon ng-block ng-social-x" style="display:inline-flex;margin-right:8px;margin-left:0px"><a rel="noopener noreferrer" target="_blank"><img src="' . NGL_PLUGIN_URL . 'assets/images/share/default/color/x.png" width="18" height="18" style="width:18px;height:18px" class="ngl-inline-image"/></a></span>
<!-- /wp:newsletterglue/social-icon -->

<!-- wp:newsletterglue/social-icon {"service":"instagram","icon_size":"18px","gap":"8px","icon_shape":"default","icon_color":"color"} -->
<span class="wp-block-newsletterglue-social-icon ng-block ng-social-instagram" style="display:inline-flex;margin-right:8px;margin-left:0px"><a rel="noopener noreferrer" target="_blank"><img src="' . NGL_PLUGIN_URL . 'assets/images/share/default/color/instagram.png" width="18" height="18" style="width:18px;height:18px" class="ngl-inline-image"/></a></span>
<!-- /wp:newsletterglue/social-icon -->

<!-- wp:newsletterglue/social-icon {"service":"facebook","icon_size":"18px","gap":"8px","icon_shape":"default","icon_color":"color"} -->
<span class="wp-block-newsletterglue-social-icon ng-block ng-social-facebook" style="display:inline-flex;margin-right:8px;margin-left:0px"><a rel="noopener noreferrer" target="_blank"><img src="' . NGL_PLUGIN_URL . 'assets/images/share/default/color/facebook.png" width="18" height="18" style="width:18px;height:18px" class="ngl-inline-image"/></a></span>
<!-- /wp:newsletterglue/social-icon --></div></td></tr></tbody></table>
<!-- /wp:newsletterglue/social-icons --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section {"width":300,"originalWidth":300} -->
<td width="300" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:300px;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/text {"color":"#707070","fontsize":"12px","align":"right"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="right" style="font-size:12px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:right;color:#707070"><p>' . esc_attr( $admin_address ) . '</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#707070","link":"#707070","fontsize":"12px","align":"right"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="right" style="font-size:12px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:right;color:#707070"><p><a href="{{ unsubscribe_link }}">Unsubscribe here</a>.</p></td></tr></tbody></table>
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
