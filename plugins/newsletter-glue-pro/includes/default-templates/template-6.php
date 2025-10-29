<?php
/**
 * Template.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// @codingStandardsIgnoreStart

add_filter( 'ng_default_template_6_title', 'ng_default_template_6_title' );
function ng_default_template_6_title() {
	return 'Curated gear and drinks digest';
}

add_filter( 'ng_default_template_6_theme', 'ng_default_template_6_theme' );
function ng_default_template_6_theme( $theme ) {
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

add_filter( 'ng_default_template_6_content', 'ng_default_template_6_content' );
function ng_default_template_6_content() {
	return '<!-- wp:newsletterglue/spacer {"height":"40px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="40" style="height:40px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/image {"width":159,"height":36,"sizeSlug":"large"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block size-large is-resized"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro-v2/assets/images/templates/logoipsum-logo-10.png" alt="" class="ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="159" height="36"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/image -->

<!-- wp:newsletterglue/spacer -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="20" style="height:20px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/text {"color":"#333333","link":"#333333","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"align":"center"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#333333"><tbody><tr><td class="ng-block-td" align="center" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:center;color:#333333"><p>High quality gear. Delicious drinks. Excellent books. Epic adventures. A curated digest for the coolest cats.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/spacer {"height":"30px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="30" style="height:30px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/image {"width":560,"height":372,"sizeSlug":"large","fontsize":"13px","color":"#707070"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block size-large is-resized"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:0px;padding-bottom:0;padding-left:0px;padding-right:0px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro-v2/assets/images/templates/gear-1.jpg" alt="" class="ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="560" height="372"/></td></tr><tr><td class="ng-block-caption" align="center" style="padding-left:0px;padding-right:0px;padding-bottom:0px;line-height:1.5;font-size:13px;font-family:Helvetica"><span style="color:#707070;font-size:13px;font-family:Helvetica;font-weight:normal">"Vintage Car" by madrones is marked with CC BY-ND 2.0.</span></td></tr></tbody></table>
<!-- /wp:newsletterglue/image -->

<!-- wp:newsletterglue/spacer {"height":"40px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="40" style="height:40px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/image {"width":560,"height":420,"sizeSlug":"large","fontsize":"13px","color":"#707070"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block size-large is-resized"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:0px;padding-bottom:0;padding-left:0px;padding-right:0px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro-v2/assets/images/templates/gear-2.jpg" alt="" class="ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="560" height="420"/></td></tr><tr><td class="ng-block-caption" align="center" style="padding-left:0px;padding-right:0px;padding-bottom:0px;line-height:1.5;font-size:13px;font-family:Helvetica"><span style="color:#707070;font-size:13px;font-family:Helvetica;font-weight:normal">Photo by Adam Anderson on Unsplash</span></td></tr></tbody></table>
<!-- /wp:newsletterglue/image -->

<!-- wp:newsletterglue/heading {"h2_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h2_colour":"#333333"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#333333"><tbody><tr><td class="ng-block-td" align="none" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:35px;padding-bottom:15px;padding-left:20px;padding-right:20px;text-align:none;color:#333333"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:none;color:#333333"><strong>The ultimate home bar</strong></h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/text {"color":"#333333","link":"#333333","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"align":"left"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#333333"><tbody><tr><td class="ng-block-td" align="left" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:left;color:#333333"><p>No house is complete without the ultimate home bar.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#333333","link":"#333333","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"align":"left"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#333333"><tbody><tr><td class="ng-block-td" align="left" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:left;color:#333333"><p>The best way to set up an in-home cocktail bar. The must have cocktails, spirits, liquors and more. From craft vodka concoctions to whiskey sours, mix yourself something interesting every day!</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/spacer -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="20" style="height:20px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/image {"width":560,"height":370,"sizeSlug":"large","fontsize":"13px","color":"#707070"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block size-large is-resized"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:0px;padding-bottom:0;padding-left:0px;padding-right:0px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro-v2/assets/images/templates/gear-3.jpg" alt="" class="ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="560" height="370"/></td></tr><tr><td class="ng-block-caption" align="center" style="padding-left:0px;padding-right:0px;padding-bottom:0px;line-height:1.5;font-size:13px;font-family:Helvetica"><span style="color:#707070;font-size:13px;font-family:Helvetica;font-weight:normal">Photo by <a href="https://unsplash.com/@sammoqadam?utm_source=unsplash&amp;utm_medium=referral&amp;utm_content=creditCopyText">Sam Moqadam</a> on <a href="https://unsplash.com/?utm_source=unsplash&amp;utm_medium=referral&amp;utm_content=creditCopyText">Unsplash</a></span></td></tr></tbody></table>
<!-- /wp:newsletterglue/image -->

<!-- wp:newsletterglue/heading {"h2_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h2_colour":"#333333"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#333333"><tbody><tr><td class="ng-block-td" align="none" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:35px;padding-bottom:15px;padding-left:20px;padding-right:20px;text-align:none;color:#333333"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:none;color:#333333"><strong>The mixed zone</strong></h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/text {"color":"#333333","link":"#333333","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"align":"left"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#333333"><tbody><tr><td class="ng-block-td" align="left" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:left;color:#333333"><p>The mosh pit is dense with people. Jumping, screaming, crying. Strobe lights blazing and music blaring. This place is actually called a ’mixed zone’. It means that it takes its cues from all over the world - we’ve got places in London where bands play once an hour; Las Vegas has their nightclubs for three nights at night, which are open every day to everyone without paying anything…</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/spacer -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="20" style="height:20px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/text {"color":"#333333","link":"#333333","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"align":"center"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#333333"><tbody><tr><td class="ng-block-td" align="center" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:center;color:#333333"><p><strong>Today’s link roundup</strong></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#333333","link":"#333333","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"align":"left"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#333333"><tbody><tr><td class="ng-block-td" align="left" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:left;color:#333333"><p>This week we’ve got <span style="text-decoration: underline;">a new park opening</span>. The best new <span style="text-decoration: underline;">Mexican-Korean restaurant</span> in town. A <span style="text-decoration: underline;">whisky festival</span> with world class, award-winning single malt whiskies.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#333333","link":"#333333","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"align":"center"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#333333"><tbody><tr><td class="ng-block-td" align="center" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:center;color:#333333"><p><strong>Our pick of the week</strong></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/image {"width":560,"height":420,"sizeSlug":"large","fontsize":"13px","color":"#707070","padding":{"top":"20px","bottom":"0px","left":"0px","right":"0px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block size-large is-resized"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:20px;padding-bottom:0;padding-left:0px;padding-right:0px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro-v2/assets/images/templates/gear-4.jpg" alt="" class="ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="560" height="420"/></td></tr><tr><td class="ng-block-caption" align="center" style="padding-left:0px;padding-right:0px;padding-bottom:0px;line-height:1.5;font-size:13px;font-family:Helvetica"><span style="color:#707070;font-size:13px;font-family:Helvetica;font-weight:normal">Photo by <a href="https://unsplash.com/@joshappel?utm_source=unsplash&amp;utm_medium=referral&amp;utm_content=creditCopyText">Josh Appel</a> on <a href="https://unsplash.com/?utm_source=unsplash&amp;utm_medium=referral&amp;utm_content=creditCopyText">Unsplash</a></span></td></tr></tbody></table>
<!-- /wp:newsletterglue/image -->

<!-- wp:newsletterglue/text {"color":"#333333","link":"#333333","padding":{"top":"25px","right":"20px","bottom":"10px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"align":"left"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#333333"><tbody><tr><td class="ng-block-td" align="left" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:25px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:left;color:#333333"><p>Chivas Regal Premium Scotch Whisky. Now at 20% off in your nearest bottle shop.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/spacer {"height":"60px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="60" style="height:60px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/text {"color":"#707070","link":"#707070","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"14px","align":"center"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="center" style="font-size:14px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:center;color:#707070"><p>Built with <a href="https://newsletterglue.com/?utm_source=newsletter&amp;utm_medium=ng-signature" target="_blank" rel="noopener noreferrer">Newsletter Glue</a> | {{ admin_address,fallback=21 Park Road }}</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#707070","link":"#707070","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"14px","align":"center"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="center" style="font-size:14px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:center;color:#707070"><p><a href="{{ unsubscribe_link }}">Unsubscribe here</a></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/spacer {"height":"40px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="40" style="height:40px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->';

}

// @codingStandardsIgnoreEnd
