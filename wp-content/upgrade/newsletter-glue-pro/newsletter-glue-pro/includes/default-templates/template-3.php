<?php
/**
 * Template.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// @codingStandardsIgnoreStart

add_filter( 'ng_default_template_3_title', 'ng_default_template_3_title' );
function ng_default_template_3_title() {
	return 'Marketing consultant';
}

add_filter( 'ng_default_template_3_theme', 'ng_default_template_3_theme' );
function ng_default_template_3_theme( $theme ) {
    $theme[ 'email_bg' ] = '#FCF7F5';
    $theme[ 'container_bg' ] = '#FCF7F5';
    $theme[ 'btn_border' ] = '#623100';
    $theme[ 'btn_bg' ] = '#623100';
    $theme[ 'a_colour' ] = '#623100';
    $theme[ 'h1_font' ] = 'georgia';
    $theme[ 'h2_font' ] = 'georgia';
    $theme[ 'h3_font' ] = 'georgia';
    $theme[ 'h4_font' ] = 'georgia';
    $theme[ 'h5_font' ] = 'georgia';
    $theme[ 'h6_font' ] = 'georgia';
    $theme[ 'p_font' ] = 'arial';
    $theme[ 'h1_colour' ] = '#340707';
    $theme[ 'h2_colour' ] = '#340707';
    $theme[ 'h3_colour' ] = '#340707';
    $theme[ 'h4_colour' ] = '#340707';
    $theme[ 'h5_colour' ] = '#340707';
    $theme[ 'h6_colour' ] = '#340707';
    $theme[ 'p_colour' ] = '#340707';
    $theme[ 'quickstyle' ] = 2;
	return $theme;
}

add_filter( 'ng_default_template_3_content', 'ng_default_template_3_content' );
function ng_default_template_3_content() {
	return '<!-- wp:newsletterglue/image {"width":600,"height":329,"sizeSlug":"large"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block size-large is-resized"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro-v2/assets/images/templates/marketing-1.png" alt="" class="ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="600" height="329"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/image -->

<!-- wp:newsletterglue/text {"color":"#340707","padding":{"top":"20px","right":"20px","bottom":"20px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"align":"center"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#340707"><tbody><tr><td class="ng-block-td" align="center" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px;text-align:center;color:#340707"><p><strong><span style="text-decoration: underline;"><mark style="background-color:rgba(0, 0, 0, 0);color:#d04a02" class="has-inline-color">View portfolio</mark></span></strong> | <strong><span style="text-decoration: underline;"><mark style="background-color:rgba(0, 0, 0, 0);color:#d04a02" class="has-inline-color">Explore my process</mark></span></strong> | <strong><span style="text-decoration: underline;"><mark style="background-color:rgba(0, 0, 0, 0);color:#d04a02" class="has-inline-color">Book a call</mark></span></strong></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#340707","padding":{"top":"8px","right":"20px","bottom":"20px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"align":"left"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#340707"><tbody><tr><td class="ng-block-td" align="left" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:20px;padding-left:20px;padding-right:20px;text-align:left;color:#340707"><p>Marketing can be a complex and daunting task for small business owners, especially when you’re starting from scratch.To help simplify the process, I’ve developed a four-step marketing framework that covers the essential components of any successful marketing strategy: audience research, messaging &amp; positioning, content strategy, and tactical execution.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/heading {"textAlign":"left","h2_padding":{"top":"5px","right":"20px","bottom":"15px","left":"20px"},"h2_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h2_colour":"#340707"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#340707"><tbody><tr><td class="ng-block-td" align="left" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:5px;padding-bottom:15px;padding-left:20px;padding-right:20px;text-align:left;color:#340707"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:left;color:#340707"><strong>1. Audience research</strong></h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/text {"color":"#340707","padding":{"top":"8px","right":"20px","bottom":"20px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"align":"left"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#340707"><tbody><tr><td class="ng-block-td" align="left" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:20px;padding-left:20px;padding-right:20px;text-align:left;color:#340707"><p>Creating content that resonates with your audience is essential, but it’s not always easy to figure out what your audience wants. Luckily, there are a number of different techniques you can use to research your audience and find out what they care about.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#340707","padding":{"top":"8px","right":"20px","bottom":"20px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"align":"left"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#340707"><tbody><tr><td class="ng-block-td" align="left" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:20px;padding-left:20px;padding-right:20px;text-align:left;color:#340707"><p>One of the best ways to get started is by using social media. Look at which topics your followers are talking about and see which ones generate the most engagement. You can also use social media to survey your audience and find out what kind of content they’d like to see from you. Another great way to research your audience is by conducting surveys.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/buttons -->
<table width="100%" cellpadding="0" cellspacing="0" class="ng-block"><tbody><tr><td style="padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px"><div style="gap:20px;justify-content:left" class="wp-block-newsletterglue-buttons"><!-- wp:newsletterglue/button {"background":"#461a03","color":"#ffffff","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"className":""} -->
<div class="wp-block-newsletterglue-button" style="flex-basis:auto"><a class="ng-block-button__link" style="font-family:Helvetica;font-size:16px;line-height:1.5;font-weight:normal;padding-top:8px;padding-bottom:8px;padding-left:20px;padding-right:20px;text-align:center;width:auto;background-color:#461a03;color:#ffffff;border-width:2px;border-style:solid;border-color:#461a03;border-radius:16px;box-sizing:border-box">Learn more</a></div>
<!-- /wp:newsletterglue/button --></div></td></tr></tbody></table>
<!-- /wp:newsletterglue/buttons -->

<!-- wp:newsletterglue/heading {"textAlign":"left","h2_padding":{"top":"15px","right":"20px","bottom":"15px","left":"20px"},"h2_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h2_colour":"#340707"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#340707"><tbody><tr><td class="ng-block-td" align="left" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:15px;padding-bottom:15px;padding-left:20px;padding-right:20px;text-align:left;color:#340707"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:left;color:#340707"><strong>Next email: Messaging &amp; positioning</strong></h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/text {"color":"#340707","padding":{"top":"8px","right":"20px","bottom":"20px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"align":"left"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#340707"><tbody><tr><td class="ng-block-td" align="left" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:20px;padding-left:20px;padding-right:20px;text-align:left;color:#340707"><p>That’s it from me today. I try to keep each email short to make them easy to read and understand.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#340707","padding":{"top":"8px","right":"20px","bottom":"20px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"align":"left"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#340707"><tbody><tr><td class="ng-block-td" align="left" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:20px;padding-left:20px;padding-right:20px;text-align:left;color:#340707"><p>In the next email, you’ll learn about messaging &amp; positioning. <em>What</em> you say is just as important as when and where you say it.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/container {"background":"#ffffff","color":"#3b3b3b","margin":{"top":"0px","bottom":"0px","left":"20px","right":"20px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#3b3b3b"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:20px" height="20"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:0px;padding-right:0px;text-align:none;color:#3b3b3b;background-color:#ffffff;border-radius:0px"><!-- wp:newsletterglue/heading {"h2_padding":{"top":"0px","right":"20px","bottom":"8px","left":"20px"},"h2_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h2_size":"18px","h2_colour":"#340707"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#340707"><tbody><tr><td class="ng-block-td" align="none" style="font-size:18px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:0px;padding-bottom:8px;padding-left:20px;padding-right:20px;text-align:none;color:#340707"><h2 style="font-size:18px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:none;color:#340707"><strong>Need an extra pair of marketing hands?</strong></h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/text {"color":"#340707","padding":{"top":"0px","right":"20px","bottom":"20px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"align":"left"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#340707"><tbody><tr><td class="ng-block-td" align="left" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:0px;padding-bottom:20px;padding-left:20px;padding-right:20px;text-align:left;color:#340707"><p>You have a thousand ideas, but only two hands, and not enough time to do it all. If you’d like me to help you out, get in touch. Share your business, your marketing plans, and where you need the most help.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/buttons -->
<table width="100%" cellpadding="0" cellspacing="0" class="ng-block"><tbody><tr><td style="padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px"><div style="gap:20px;justify-content:left" class="wp-block-newsletterglue-buttons"><!-- wp:newsletterglue/button {"background":"#461a03","color":"#ffffff","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"className":""} -->
<div class="wp-block-newsletterglue-button" style="flex-basis:auto"><a class="ng-block-button__link" style="font-family:Helvetica;font-size:16px;line-height:1.5;font-weight:normal;padding-top:8px;padding-bottom:8px;padding-left:20px;padding-right:20px;text-align:center;width:auto;background-color:#461a03;color:#ffffff;border-width:2px;border-style:solid;border-color:#461a03;border-radius:16px;box-sizing:border-box">I need marketing help</a></div>
<!-- /wp:newsletterglue/button --></div></td></tr></tbody></table>
<!-- /wp:newsletterglue/buttons --></td><td class="ng-block-hs ng-block-hs-2" style="width:20px" height="20"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:0px" height="0" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->

<!-- wp:newsletterglue/container {"background":"#fcf7f5","color":"#340707"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#340707"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:0px" height="0"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:0px;padding-right:0px;text-align:none;color:#340707;background-color:#fcf7f5;border-radius:0px"><!-- wp:newsletterglue/text {"color":"#340707","link":"#340707","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"13px","align":"center"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#340707"><tbody><tr><td class="ng-block-td" align="center" style="font-size:13px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:center;color:#340707"><p>Built with <a href="https://newsletterglue.com/?utm_source=newsletter&amp;utm_medium=ng-signature" target="_blank" rel="noopener noreferrer">Newsletter Glue</a>.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#340707","link":"#340707","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"13px","align":"center"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#340707"><tbody><tr><td class="ng-block-td" align="center" style="font-size:13px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:center;color:#340707"><p>{{ admin_address,fallback=21 Park Road }} | <a href="{{ unsubscribe_link }}">Unsubscribe</a></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td><td class="ng-block-hs ng-block-hs-2" style="width:0px" height="0"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:0px" height="0" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->';
}

// @codingStandardsIgnoreEnd
