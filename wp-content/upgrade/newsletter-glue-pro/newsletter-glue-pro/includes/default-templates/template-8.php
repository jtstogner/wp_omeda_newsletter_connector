<?php
/**
 * Template.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// @codingStandardsIgnoreStart

add_filter( 'ng_default_template_8_title', 'ng_default_template_8_title' );
function ng_default_template_8_title() {
	return 'Life on Mars';
}

add_filter( 'ng_default_template_8_content', 'ng_default_template_8_content' );
function ng_default_template_8_content() {
	return '<!-- wp:newsletterglue/spacer {"height":"40px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="40" style="height:40px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/image {"width":144,"height":40,"sizeSlug":"large"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block size-large is-resized"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro-v2/assets/images/templates/logo-ipsum12.png" alt="" class="ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="144" height="40"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/image -->

<!-- wp:newsletterglue/meta-data {"color":"#666666","link":"#666666","padding":{"top":"40px","bottom":"5px","left":"20px","right":"20px"},"post_id":9065,"align":"center","show_author":false,"show_date":false,"title":"New campaign","issue":"Issue #","url":"Read online","readingtime":"1 mins","readtime":"Reading time:","author_name":"admin","profile_pic":"http://0.gravatar.com/avatar/045ef9284c033798e402b62c6e28a4b7?s=96\u0026d=mm\u0026r=g"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-meta-data ng-block" style="color:#666666" data-date-format="F j, Y"><tbody><tr><td class="ng-block-td" align="center" style="font-size:12px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:40px;padding-bottom:5px;padding-left:20px;padding-right:20px;text-align:center;color:#666666"><span class="ng-block-issue" style="display:inline-block"><span>Issue #</span></span><span class="ng-block-readtime" style="display:inline-block"><span class="ng-sep">   |   </span><span>Reading time:</span> <span class="ngl-metadata-readtime-ajax">1 mins</span></span><span class="ng-block-url" style="display:inline-block"><span class="ng-sep">   |   </span><a href="{{ blog_post }}">Read online</a></span></td></tr></tbody></table>
<!-- /wp:newsletterglue/meta-data -->

<!-- wp:newsletterglue/separator {"color":"#ececec"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-separator ng-block" style="color:#ececec"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px;color:#ececec"><hr style="background-color:transparent;color:transparent;margin:0;border:0;border-top:1px solid #ececec;width:560px;height:0"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/separator -->

<!-- wp:newsletterglue/image {"width":560,"height":365,"sizeSlug":"large"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block size-large is-resized"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro-v2/assets/images/templates/mars-1.jpeg" alt="" class="ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="560" height="365"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/image -->

<!-- wp:newsletterglue/heading {"h2_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h2_colour":"#333333"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#333333"><tbody><tr><td class="ng-block-td" align="none" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:35px;padding-bottom:15px;padding-left:20px;padding-right:20px;text-align:none;color:#333333"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:none;color:#333333"><strong>Life on Mars: The Ethical Implications of Colonizing the Red Planet</strong></h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/text {"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"15px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:15px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p><a href="https://areomagazine.com/2018/07/02/life-on-mars-the-ethical-implications-of-colonizing-the-red-planet/">Areo Magazine</a></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"16px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p>Human expansion into space is at the periphery of our current, scientific knowledge. There is a long and earnest history of proposals for such a project; it fills canons of science fiction, occupies reams of academic writing, and reposes smiling in the hearts of millions as a hope, and a dream. Today, major institutions, billionaires, and even the <a href="http://www.spaceresources.public.lu/en.html">nation of Luxembourg</a> are investing in ambitious off-Earth plans. Environmental scientists and economists are proposing ways to use space-based resources to improve life on Earth. Slowly, surely, the idea is making its way into the mainstream.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"16px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p><em><strong>Wonder if He’ll Ever Know/ He’s in the Best Selling Show</strong></em></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"16px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p>SpaceX’s Falcon Heavy had its maiden flight in February 2017, igniting a firestorm of media coverage, and a flurry of tweets from the company’s CEO, Elon Musk. The massive rocket thundered off its launchpad, while the accompanying corporate webcast blasted David Bowie. In a double act of remarkable precision, the twin side boosters returned to the launch site, blazing triumphantly down to their landing zones. The central core, however, unfortunately <a href="https://www.engadget.com/2018/02/06/spacex-falcon-heavy-center-core-lost/">slammed into the Atlantic</a> at 480 kilometers per hour. Oops. Well, they’re working on it.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/spacer {"height":"40px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="40" style="height:40px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/sections {"layout":"50_50"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-sections ng-block is-stacked-on-mobile"><tbody><tr><td class="ng-columns-wrap" style="padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px"><table width="100%" cellpadding="0" cellspacing="0"><tbody><tr><!-- wp:newsletterglue/section {"width":300,"originalWidth":300} -->
<td width="300" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:300px;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/text {"color":"#707070","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"12px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="none" style="font-size:12px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#707070"><p>Published with ♥ by {{ admin_name,fallback=Newsletter Glue }}</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section {"width":300,"originalWidth":300} -->
<td width="300" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:300px;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/text {"color":"#707070","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"12px","align":"right"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="right" style="font-size:12px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:right;color:#707070"><p>{{ admin_address,fallback=21 Park Road }}</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#707070","link":"#707070","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"12px","align":"right"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="right" style="font-size:12px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:right;color:#707070"><p><a href="{{ unsubscribe_link }}"><span style="color:#707070" class="has-inline-color">Unsubscribe here.</span></a></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section -->
<td width="auto" class="wp-block-newsletterglue-section ng-block ng-should-remove" valign="top" style="width:auto;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:none;vertical-align:top"><!-- wp:newsletterglue/text -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td>
<!-- /wp:newsletterglue/section --></tr></tbody></table></td></tr></tbody></table>
<!-- /wp:newsletterglue/sections -->';

}

// @codingStandardsIgnoreEnd
