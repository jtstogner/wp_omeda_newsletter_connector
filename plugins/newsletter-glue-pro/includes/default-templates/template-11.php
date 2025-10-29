<?php
/**
 * Template.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// @codingStandardsIgnoreStart

add_filter( 'ng_default_template_11_title', 'ng_default_template_11_title' );
function ng_default_template_11_title() {
	return 'Weekly email digest';
}

add_filter( 'ng_default_template_11_theme', 'ng_default_template_11_theme' );
function ng_default_template_11_theme( $theme ) {
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

add_filter( 'ng_default_template_11_content', 'ng_default_template_11_content' );
function ng_default_template_11_content() {

    $admin_address  = '{{ admin_address,fallback=' . get_option( 'newsletterglue_admin_address' ) . ' }}';

	return '<!-- wp:newsletterglue/spacer -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="20" style="height:20px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/image {"align":"left","width":95,"height":45,"sizeSlug":"large","padding":{"top":"20px","bottom":"20px","left":"20px","right":"20px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block alignleft size-large is-resized"><tbody><tr><td class="ng-block-td" align="left" style="padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px"><img src="' . NGL_PLUGIN_URL . 'assets/images/templates/logoipsum-logo-4.png" alt="" class="ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="95" height="45"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/image -->

<!-- wp:newsletterglue/heading {"h2_padding":{"top":"10px","right":"20px","bottom":"15px","left":"20px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#1E1E1E"><tbody><tr><td class="ng-block-td" align="none" style="font-size:28px;font-family:Arial;line-height:1.2;font-weight:normal;padding-top:10px;padding-bottom:15px;padding-left:20px;padding-right:20px;text-align:none;color:#1E1E1E"><h2 style="font-size:28px;font-family:Arial;line-height:1.2;font-weight:normal;text-align:none;color:#1E1E1E"><strong>Our weekly digest</strong></h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/text -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#454545"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#454545"><p>Here are the latest and greatest blog posts we published this week. Enjoy!</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/container {"background":"#f9f9f9"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#454545"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:0px" height="0"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:0px;padding-right:0px;text-align:none;color:#454545;background-color:#f9f9f9;border-radius:0px"><!-- wp:newsletterglue/latest-posts {"containerWidth":"560px","div1":"158px","div2":"382px","filter_cpts":[{"label":"Posts","value":"post"}],"dates":{"label":"Last 7 days","value":"last_7"},"posts_num":0,"update_posts":4.21651290353563,"label_type":"domain","posts":null,"hash":null} /-->

<!-- wp:newsletterglue/text {"padding":{"top":"10px","right":"20px","bottom":"10px","left":"20px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#454545"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#454545"><p></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td><td class="ng-block-hs ng-block-hs-2" style="width:0px" height="0"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:0px" height="0" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->

<!-- wp:newsletterglue/spacer {"height":"60px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="60" style="height:60px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/text {"color":"#707070","link":"#707070","fontsize":"14px","align":"center"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="center" style="font-size:14px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:center;color:#707070"><p>Built with <a href="https://newsletterglue.com/?utm_source=newsletter&amp;utm_medium=ng-signature" target="_blank" rel="noopener noreferrer">Newsletter Glue</a>.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#707070","link":"#707070","fontsize":"14px","align":"center"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="center" style="font-size:14px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:center;color:#707070"><p>' . esc_attr( $admin_address ) . '</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#707070","link":"#707070","fontsize":"14px","align":"center"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="center" style="font-size:14px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:center;color:#707070"><p>If you’d no longer like to receive emails from me, you can <a href="{{ unsubscribe_link }}">unsubscribe here</a>.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/spacer {"height":"40px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="40" style="height:40px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->';
}

// @codingStandardsIgnoreEnd
