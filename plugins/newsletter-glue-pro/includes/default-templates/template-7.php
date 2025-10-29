<?php
/**
 * Template.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// @codingStandardsIgnoreStart

add_filter( 'ng_default_template_7_title', 'ng_default_template_7_title' );
function ng_default_template_7_title() {
	return 'Weekly digest';
}

add_filter( 'ng_default_template_7_content', 'ng_default_template_7_content' );
function ng_default_template_7_content() {
	return '<!-- wp:newsletterglue/container {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#666666"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:0px" height="0"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:0px;padding-right:0px;text-align:none;color:#666666;border-radius:0px"><!-- wp:newsletterglue/sections {"layout":"50_50","padding":{"top":"10px","bottom":"10px","left":"20px","right":"20px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-sections ng-block is-stacked-on-mobile"><tbody><tr><td class="ng-columns-wrap" style="padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px"><table width="100%" cellpadding="0" cellspacing="0"><tbody><tr><!-- wp:newsletterglue/section {"width":280,"originalWidth":280} -->
<td width="280" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:280px;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/image {"align":"left","threshold":280,"width":172,"height":40,"sizeSlug":"large"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block alignleft size-large is-resized"><tbody><tr><td class="ng-block-td" align="left" style="padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro-v2/assets/images/templates/logoipsum-logo-44.png" alt="" class="ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="172" height="40"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/image --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section {"width":280,"originalWidth":280,"verticalAlign":"middle"} -->
<td width="280" class="wp-block-newsletterglue-section ng-block" valign="middle" style="width:280px;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:middle"><!-- wp:newsletterglue/meta-data {"color":"#1a4548","link":"#1a4548","padding":{"top":"0px","bottom":"0px","left":"0px","right":"0px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"post_id":9065,"align":"right","show_author":false,"show_issue":false,"show_date":false,"title":"New campaign","url":"Read online","readingtime":"1 mins","readtime":"Reading time:","author_name":"admin","profile_pic":"http://0.gravatar.com/avatar/045ef9284c033798e402b62c6e28a4b7?s=96\u0026d=mm\u0026r=g"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-meta-data ng-block" style="color:#1a4548" data-date-format="F j, Y"><tbody><tr><td class="ng-block-td" align="right" style="font-size:12px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px;text-align:right;color:#1a4548"><span class="ng-block-readtime" style="display:inline-block"><span>Reading time:</span> <span class="ngl-metadata-readtime-ajax">1 mins</span></span><span class="ng-block-url" style="display:inline-block"><span class="ng-sep">   |   </span><a href="{{ blog_post }}">Read online</a></span></td></tr></tbody></table>
<!-- /wp:newsletterglue/meta-data --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section -->
<td width="auto" class="wp-block-newsletterglue-section ng-block ng-should-remove" valign="top" style="width:auto;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:none;vertical-align:top"><!-- wp:newsletterglue/text -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td>
<!-- /wp:newsletterglue/section --></tr></tbody></table></td></tr></tbody></table>
<!-- /wp:newsletterglue/sections -->

<!-- wp:newsletterglue/text {"color":"#666666","padding":{"top":"8px","right":"20px","bottom":"0px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:0px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p><strong>Weekly digest</strong></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/separator {"color":"#bebebe"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-separator ng-block" style="color:#bebebe"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px;color:#bebebe"><hr style="background-color:transparent;color:transparent;margin:0;border:0;border-top:1px solid #bebebe;width:560px;height:0"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/separator --></td><td class="ng-block-hs ng-block-hs-2" style="width:0px" height="0"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:0px" height="0" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->

<!-- wp:newsletterglue/heading {"h2_padding":{"top":"5px","right":"20px","bottom":"15px","left":"20px"},"h2_colour":"#333333"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#333333"><tbody><tr><td class="ng-block-td" align="none" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:5px;padding-bottom:15px;padding-left:20px;padding-right:20px;text-align:none;color:#333333"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:none;color:#333333"><strong>Solarpunk, lost cities, and more</strong></h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/post-embeds {"containerWidth":"560px","div1":"270px","div2":"270px","table_ratio":"50_50","show_cta":false,"embeds":{"1":{"key":1,"id":"https://news.sky.com/story/venice-to-limit-size-of-tour-groups-from-this-week-in-tourism-crackdown-13188194","post_id":0,"title":"Venice to limit size of tour groups from this week in tourism crackdown","content":"Venice is struggling with the effects of \u0022overtourism\u0022. Around 4.9 million visitors reportedly descended on the city centre - which is home to 50,000 people - in 2023.","image":"https://e3.365dm.com/24/04/1600x900/skynews-venice-st-marks_6532730.jpg?20240425111630","author":"","categories":"","tags":"","favicon":"https://www.google.com/s2/favicons?sz=32\u0026domain_url=https://news.sky.com/story/venice-to-limit-size-of-tour-groups-from-this-week-in-tourism-crackdown-13188194","remote":"yes","domain":"news.sky.com","enabled":1,"hidden":0},"2":{"key":2,"id":"https://news.sky.com/story/thunderstorms-and-lightning-bring-risk-of-flooding-to-parts-of-england-and-wales-13187971","post_id":0,"title":"Thunderstorms and lightning bring risk of flooding to parts of England and Wales","content":"The Met Office is warning of gusty winds, large hail, and even the potential for power cuts. Yellow heat health warnings also remain in place for much of England until Friday.","image":"https://e3.365dm.com/24/07/1600x900/skynews-weather-rain_6621107.jpg?20240731131211","author":"","categories":"","tags":"","favicon":"https://www.google.com/s2/favicons?sz=32\u0026domain_url=https://news.sky.com/story/thunderstorms-and-lightning-bring-risk-of-flooding-to-parts-of-england-and-wales-13187971","remote":"yes","domain":"news.sky.com","enabled":1,"hidden":0},"3":{"key":3,"id":"https://news.sky.com/story/beautiful-housing-rule-blocked-development-claims-angela-rayner-13188025","post_id":0,"title":"Beautiful housing rule blocked development, claims Angela Rayner","content":"The deputy prime minister said the Conservative policy was \u0022too subjective\u0022, and there were already rules in place to protect communities.","image":"https://e3.365dm.com/24/07/1600x900/skynews-angela-rayner-downing-street_6618802.jpg?20240731171022","author":"","categories":"","tags":"","favicon":"https://www.google.com/s2/favicons?sz=32\u0026domain_url=https://news.sky.com/story/beautiful-housing-rule-blocked-development-claims-angela-rayner-13188025","remote":"yes","domain":"news.sky.com","enabled":1,"hidden":0}},"embeds_order":[1,2,3],"fontsize_title":"18px"} /-->

<!-- wp:newsletterglue/text {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p><strong>Editor’s picks</strong></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p>As promised, we’re back with the rest of our <a href="https://wbur.us12.list-manage.com/track/click?u=cb5409ffc9d96a00c1fd3cd28&amp;id=6788614aa5&amp;e=23de296cb1" target="_blank" rel="noopener noreferrer">quick reading guides</a>! Whether you’re looking for a short novella, a comic book, or a series of short stories, we’ve got you covered.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/spacer {"height":"40px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="40" style="height:40px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/text {"color":"#666666","link":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"14px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:14px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p>Built with <a href="https://newsletterglue.com/?utm_source=newsletter&amp;utm_medium=ng-signature" target="_blank" rel="noopener noreferrer">Newsletter Glue</a>.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#666666","link":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"14px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:14px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p>{{ admin_address,fallback=21 Park Road }}</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#666666","link":"#666666","fontsize":"14px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:14px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p>If you’d no longer like to receive emails from me, you can <a href="{{ unsubscribe_link }}">unsubscribe here</a>.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/spacer {"height":"40px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="40" style="height:40px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->';

}

// @codingStandardsIgnoreEnd
