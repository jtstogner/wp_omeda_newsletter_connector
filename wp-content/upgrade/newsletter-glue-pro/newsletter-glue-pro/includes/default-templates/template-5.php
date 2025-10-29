<?php
/**
 * Template.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// @codingStandardsIgnoreStart

add_filter( 'ng_default_template_5_title', 'ng_default_template_5_title' );
function ng_default_template_5_title() {
	return 'Early education. Child’s play.';
}

add_filter( 'ng_default_template_5_theme', 'ng_default_template_5_theme' );
function ng_default_template_5_theme( $theme ) {
	$theme[ 'email_bg' ] = '#FFFFFF';
	return $theme;
}

add_filter( 'ng_default_template_5_content', 'ng_default_template_5_content' );
function ng_default_template_5_content() {
	return '<!-- wp:newsletterglue/spacer -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="20" style="height:20px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/image {"width":171,"height":32,"sizeSlug":"large"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block size-large is-resized"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro-v2/assets/images/templates/logoipsum-logo-52.png" alt="" class="ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="171" height="32"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/image -->

<!-- wp:newsletterglue/spacer {"height":"40px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="40" style="height:40px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/container {"background":"#fffcee","color":"#666666","margin":{"top":"0px","bottom":"0px","left":"20px","right":"20px"},"radius":"10px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#666666"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:20px" height="20"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:0px;padding-right:0px;text-align:none;color:#666666;background-color:#fffcee;border-radius:10px"><!-- wp:newsletterglue/image {"width":560,"height":320,"sizeSlug":"large"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block size-large is-resized"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro-v2/assets/images/templates/childs-1.png" alt="" class="ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="560" height="320"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/image -->

<!-- wp:newsletterglue/heading {"h2_padding":{"top":"5px","right":"20px","bottom":"5px","left":"20px"},"h2_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h2_colour":"#333333"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#333333"><tbody><tr><td class="ng-block-td" align="none" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px;text-align:none;color:#333333"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:none;color:#333333"><strong>Go on an adventure.</strong></h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/text {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p>Rather than staying cooped up inside, introduce them to the great outdoors. It doesn’t matter if you don’t know the name of a tree or whether that’s a squirrel scampering by. Learning together is part of the enjoyment and a great way to motivate your child too.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#666666"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p>What can I learn about each individual bird in my neighbourhood? How many species are there in one area? What kind will be most interesting for my child when they visit this place? Read up! The following list was done based primarily upon what we learned during our trip along Natal Eospeo-Icoja de la Hora Poblacion Grande Arroyo del Parque San Miguel; which spans across three major provinces.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/buttons -->
<table width="100%" cellpadding="0" cellspacing="0" class="ng-block"><tbody><tr><td style="padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px"><div style="gap:20px;justify-content:left" class="wp-block-newsletterglue-buttons"><!-- wp:newsletterglue/button {"radius":"0px","className":""} -->
<div class="wp-block-newsletterglue-button" style="flex-basis:auto"><a class="ng-block-button__link" style="font-family:Helvetica;font-size:16px;line-height:1.5;font-weight:normal;padding-top:8px;padding-bottom:8px;padding-left:20px;padding-right:20px;text-align:center;width:auto;background-color:#0088a0;color:#ffffff;border-width:2px;border-style:solid;border-color:#0088a0;border-radius:0px;box-sizing:border-box">Read online</a></div>
<!-- /wp:newsletterglue/button --></div></td></tr></tbody></table>
<!-- /wp:newsletterglue/buttons --></td><td class="ng-block-hs ng-block-hs-2" style="width:20px" height="20"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:0px" height="0" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->

<!-- wp:newsletterglue/spacer -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="20" style="height:20px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/heading {"h2_colour":"#925aa6"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#925aa6"><tbody><tr><td class="ng-block-td" align="none" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:35px;padding-bottom:15px;padding-left:20px;padding-right:20px;text-align:none;color:#925aa6"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:none;color:#925aa6"><strong>Featured reads</strong></h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/post-embeds {"containerWidth":"560px","div1":"100%","div2":"100%","table_ratio":"full","show_cta":false,"embeds":{"1":{"key":1,"id":"https://lifegoalsmag.com/blogging-tips-enneagram-type","post_id":0,"title":"Business Blogging Tips Based On Your Enneagram Type | Life Goals Mag","content":"Blogging is a powerful tool for businesses to share their expertise, engage with their audience, and establish their brand presence online. By considering your Enneagram type, you can tailor your blogging approach to align with your unique strengths and motivations. In this article, we\u0026rsquo;ll explore specific blogging tips for each Enneagram type, empowering you to create compelling and effective content that resonates with your target audience. Whether you\u0026rsquo;re a perfectionist, helper, achiever, individualist, investigator, loyalist, enthusiast, challenger, or peacemaker, there\u0026rsquo;s [\u0026hellip;]","image":"https://i0.wp.com/lifegoalsmag.com/wp-content/uploads/2023/06/blogging-enneagram-tips.jpeg?fit=900%2C718\u0026ssl=1","author":"","categories":"","tags":"","favicon":"https://www.google.com/s2/favicons?sz=32\u0026domain_url=https://lifegoalsmag.com/blogging-tips-enneagram-type","remote":"yes","domain":"lifegoalsmag.com","enabled":0,"hidden":0},"2":{"key":2,"id":"https://lifegoalsmag.com/day-in-the-life-design-director","post_id":0,"title":"A Successful Design Director\'s Day In The Life | Life Goals Mag","content":"Meredith Cancilla is the Founder and Design Director of Quixotic Design Co. a remote design agency centered around building female-owned brands.","image":"https://i0.wp.com/lifegoalsmag.com/wp-content/uploads/2020/04/IMG_0888-1.jpg?fit=800%2C527\u0026ssl=1","author":"","categories":"","tags":"","favicon":"https://www.google.com/s2/favicons?sz=32\u0026domain_url=https://lifegoalsmag.com/day-in-the-life-design-director","remote":"yes","domain":"lifegoalsmag.com","enabled":1,"hidden":0,"custom":null}},"embeds_order":[1,2],"padding":{"top":"0px","left":"20px","right":"20px","bottom":"0px"}} /-->

<!-- wp:newsletterglue/post-embeds {"containerWidth":"560px","div1":"158px","div2":"382px","show_cta":false,"embeds":{"1":{"key":1,"id":"https://lifegoalsmag.com/unconventional-goal-setting-advice","post_id":0,"title":"Unconventional Goal Setting Advice For When You Struggle To Hit Your Goals | Life Goals Mag","content":"Do you feel like you\u0026rsquo;re an ambitious high achiever who struggles to hit goals? It can be jarring to your identity. It\u0026rsquo;s easy to feel like there\u0026rsquo;s something wrong with you or that you\u0026rsquo;re incapable of following your dreams \u0026ndash; talk about feeling defeated! The good news is that\u0026rsquo;s not the case. The problem is that you are using goal-setting techniques that aren\u0026rsquo;t effective for you and then blame yourself when they fail. It\u0026rsquo;s like trying to cut wood with [\u0026hellip;]","image":"https://i0.wp.com/lifegoalsmag.com/wp-content/uploads/2023/07/goals.jpeg?fit=950%2C768\u0026ssl=1","author":"","categories":"","tags":"","favicon":"https://www.google.com/s2/favicons?sz=32\u0026domain_url=https://lifegoalsmag.com/unconventional-goal-setting-advice","remote":"yes","domain":"lifegoalsmag.com","enabled":1,"hidden":0,"custom":{"content":"\u003cp\u003eDo you feel like you’re an ambitious high achiever who struggles to hit goals? It can be jarring to your identity.\u003c/p\u003e"}},"2":{"key":2,"id":"https://lifegoalsmag.com/screen-time-goals","post_id":0,"title":"10 Screen Time Goals To Get Offline And Feel More Focused | Life Goals Mag","content":"If you feel like you\u0026rsquo;ve been less focused or feel like being on your screen is affecting your mental health, it might be time to make a screen time goal. What is a screen time goal? I\u0026rsquo;m defining a \u0026ldquo;screen time\u0026rdquo; goal as a goal that you set around reducing your time spent on your computer or phone. I\u0026rsquo;m not defining this as time on your computer or phone for work, but more for when you\u0026rsquo;re using it for mindless [\u0026hellip;]","image":"https://i0.wp.com/lifegoalsmag.com/wp-content/uploads/2022/11/girl-phone-screen-time.jpeg?fit=900%2C686\u0026ssl=1","author":"","categories":"","tags":"","favicon":"https://www.google.com/s2/favicons?sz=32\u0026domain_url=https://lifegoalsmag.com/screen-time-goals","remote":"yes","domain":"lifegoalsmag.com","enabled":1,"hidden":0,"custom":{"content":"\u003cp\u003eIf you feel like you’ve been less focused or feel like being on your screen is affecting your mental health, it might be time to make a screen time.\u003c/p\u003e"}}},"embeds_order":[1,2],"fontsize_title":"20px","padding":{"top":"0px","left":"20px","right":"20px","bottom":"0px"}} /-->

<!-- wp:newsletterglue/container {"background":"#e7f6e6","color":"#666666","margin":{"top":"0px","bottom":"0px","left":"20px","right":"20px"},"radius":"10px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#666666"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:20px" height="20"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:0px;padding-right:0px;text-align:none;color:#666666;background-color:#e7f6e6;border-radius:10px"><!-- wp:newsletterglue/image {"width":560,"height":420,"sizeSlug":"large"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block size-large is-resized"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro-v2/assets/images/templates/childs-2.png" alt="" class="ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="560" height="420"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/image -->

<!-- wp:newsletterglue/heading {"h2_padding":{"top":"5px","right":"20px","bottom":"5px","left":"20px"},"h2_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h2_colour":"#333333"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#333333"><tbody><tr><td class="ng-block-td" align="none" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px;text-align:none;color:#333333"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:none;color:#333333"><strong>Baking, confidence and fun</strong></h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/text {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p>Baking with your child is the best way to teach them life skills and also give them confidence. You will get to spend hours together baking each week. The goal of these activities is simple: Bake a beautiful cake and have fun sharing a new skill or creativity through this hobby - try not only making sweet recipes.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#666666"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p>I love creating such great moments because we get our son involved into our little world and create an escape from work</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/buttons -->
<table width="100%" cellpadding="0" cellspacing="0" class="ng-block"><tbody><tr><td style="padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px"><div style="gap:20px;justify-content:left" class="wp-block-newsletterglue-buttons"><!-- wp:newsletterglue/button {"radius":"0px","className":""} -->
<div class="wp-block-newsletterglue-button" style="flex-basis:auto"><a class="ng-block-button__link" style="font-family:Helvetica;font-size:16px;line-height:1.5;font-weight:normal;padding-top:8px;padding-bottom:8px;padding-left:20px;padding-right:20px;text-align:center;width:auto;background-color:#0088a0;color:#ffffff;border-width:2px;border-style:solid;border-color:#0088a0;border-radius:0px;box-sizing:border-box">Learn baking</a></div>
<!-- /wp:newsletterglue/button --></div></td></tr></tbody></table>
<!-- /wp:newsletterglue/buttons --></td><td class="ng-block-hs ng-block-hs-2" style="width:20px" height="20"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:0px" height="0" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->

<!-- wp:newsletterglue/heading {"h2_padding":{"top":"50px","right":"20px","bottom":"15px","left":"20px"},"h2_colour":"#333333"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#333333"><tbody><tr><td class="ng-block-td" align="none" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:50px;padding-bottom:15px;padding-left:20px;padding-right:20px;text-align:none;color:#333333"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:none;color:#333333"><strong>In case you missed it</strong></h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/post-embeds {"containerWidth":"560px","div1":"158px","div2":"382px","show_cta":false,"embeds":{"1":{"key":1,"id":"https://lifegoalsmag.com/how-add-play-workday","post_id":0,"title":"How To Start Adding More Play To Your Workday | Life Goals Mag","content":"Have you ever thought about incorporating play into your workday? Are you having enough fun at work? For some, it might feel like you\u0026rsquo;re working a job that might not feel playful and fun, but the truth is, there are many ways we can add play into our daily work routines.\u0026nbsp; Listen to the episode! \u0026nbsp; â–¶ï¸Ž Listen on Apple Podcasts | Spotify | Google Podcasts | Stitcher Tried and true methods for adding more play into work A quick [\u0026hellip;]","image":"https://i0.wp.com/lifegoalsmag.com/wp-content/uploads/2022/01/play-at-work-making-it-part-of-your-lifestyle.jpeg?fit=1016%2C678\u0026ssl=1","author":"","categories":"","tags":"","favicon":"https://www.google.com/s2/favicons?sz=32\u0026domain_url=https://lifegoalsmag.com/how-add-play-workday","remote":"yes","domain":"lifegoalsmag.com","enabled":1,"hidden":0,"custom":{"content":"\u003cp\u003eHave you ever thought about incorporating play into your workday? Are you having enough fun at work?\u003c/p\u003e"}},"2":{"key":2,"id":"https://lifegoalsmag.com/career-pancake-tree","post_id":0,"title":"When it Comes to Your Career, Are You a Pancake or a Tree? | Life Goals Mag","content":"In one of my previous roles, I used to organize monthly guest lectures for my university students from various active professionals in their target industries. I was working with creative media students, so we focused on showcasing the breadth of different opportunities and potential career paths that exist in industries like animation, music, film, design, and games development. One of the professionals I bought in to do a talk was primarily a graphic designer, but she handed out a piece [\u0026hellip;]","image":"https://i0.wp.com/lifegoalsmag.com/wp-content/uploads/2022/01/pancakes-personality-test.jpeg?fit=1599%2C1051\u0026ssl=1","author":"","categories":"","tags":"","favicon":"https://www.google.com/s2/favicons?sz=32\u0026domain_url=https://lifegoalsmag.com/career-pancake-tree","remote":"yes","domain":"lifegoalsmag.com","enabled":1,"hidden":0,"custom":{"content":"\u003cp\u003eIn one of my previous roles, I used to organize monthly guest lectures for my university students from various active professionals in their target industries...\u003c/p\u003e"}},"3":{"key":3,"id":"https://lifegoalsmag.com/sleep-hygiene-productivity-hack","post_id":0,"title":"10 Sleep Hygiene Habits To Improve Your Slumber (And Productivity) | Life Goals Mag","content":"Since launching my business, Cacti Wellness Collective, about a year ago, let\u0026rsquo;s just say there have been some looooong nights. While I\u0026rsquo;m not glorifying the idea of #teamnosleep, I understand the feeling of always having more work to do and/or not having enough minutes in the day. That being said, after going HARD on late nights \u0026 all nighters for the first six months of business, I reached a point of extreme exhaustion and found myself feeling overly emotional, physically [\u0026hellip;]","image":"https://i0.wp.com/lifegoalsmag.com/wp-content/uploads/2021/05/evening-routine-bath-ritual-scaled-e1620234262645.jpeg?fit=1000%2C667\u0026ssl=1","author":"","categories":"","tags":"","favicon":"https://www.google.com/s2/favicons?sz=32\u0026domain_url=https://lifegoalsmag.com/sleep-hygiene-productivity-hack","remote":"yes","domain":"lifegoalsmag.com","enabled":1,"hidden":0,"custom":{"content":"\u003cp\u003eSince launching my business, Cacti Wellness Collective, about a year ago, let’s just say there have been some looooong nights.\u003c/p\u003e"}}},"embeds_order":[1,2,3],"fontsize_title":"20px","padding":{"top":"0px","left":"20px","right":"20px","bottom":"0px"}} /-->

<!-- wp:newsletterglue/heading {"h2_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h2_colour":"#333333"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#333333"><tbody><tr><td class="ng-block-td" align="none" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:35px;padding-bottom:15px;padding-left:20px;padding-right:20px;text-align:none;color:#333333"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:none;color:#333333"><strong>Featured contributor</strong></h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/container {"background":"#e2f1ff","color":"#666666","padding":{"top":"20px","bottom":"20px","left":"20px","right":"20px"},"margin":{"top":"0px","bottom":"0px","left":"20px","right":"20px"},"radius":"10px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#666666"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:20px" height="20"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px;text-align:none;color:#666666;background-color:#e2f1ff;border-radius:10px"><!-- wp:newsletterglue/sections {"layout":"70_30"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-sections ng-block is-stacked-on-mobile"><tbody><tr><td class="ng-columns-wrap" style="padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px"><table width="100%" cellpadding="0" cellspacing="0"><tbody><tr><!-- wp:newsletterglue/section {"width":420,"originalWidth":420,"padding":{"top":"10px","bottom":"0px","left":"0px","right":"0px"}} -->
<td width="420" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:420px;padding-top:10px;padding-bottom:0px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/text {"color":"#666666","padding":{"top":"0px","right":"20px","bottom":"10px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:0px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p>As a professional photographer, I love going out into the city to capture life and have adventures. The beauty of this lifestyle was captured by John MacLean during his trip in Canada last year on June 25th/26 2016 while he took photos near Black Creek Lake North where you’ll find wildflowers from all over.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/buttons -->
<table width="100%" cellpadding="0" cellspacing="0" class="ng-block"><tbody><tr><td style="padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px"><div style="gap:20px;justify-content:left" class="wp-block-newsletterglue-buttons"><!-- wp:newsletterglue/button {"radius":"0px","className":""} -->
<div class="wp-block-newsletterglue-button" style="flex-basis:auto"><a class="ng-block-button__link" style="font-family:Helvetica;font-size:16px;line-height:1.5;font-weight:normal;padding-top:8px;padding-bottom:8px;padding-left:20px;padding-right:20px;text-align:center;width:auto;background-color:#0088a0;color:#ffffff;border-width:2px;border-style:solid;border-color:#0088a0;border-radius:0px;box-sizing:border-box">Learn more about Tricia</a></div>
<!-- /wp:newsletterglue/button --></div></td></tr></tbody></table>
<!-- /wp:newsletterglue/buttons --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section {"width":180,"originalWidth":180,"padding":{"top":"10px","bottom":"0px","left":"0px","right":"0px"}} -->
<td width="180" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:180px;padding-top:10px;padding-bottom:0px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/image {"threshold":180,"width":104,"height":138,"sizeSlug":"large"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block size-large is-resized"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro-v2/assets/images/templates/childs-3.jpeg" alt="" class="ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="104" height="138"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/image --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section -->
<td width="auto" class="wp-block-newsletterglue-section ng-block ng-should-remove" valign="top" style="width:auto;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:none;vertical-align:top"><!-- wp:newsletterglue/text -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td>
<!-- /wp:newsletterglue/section --></tr></tbody></table></td></tr></tbody></table>
<!-- /wp:newsletterglue/sections --></td><td class="ng-block-hs ng-block-hs-2" style="width:20px" height="20"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:0px" height="0" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->

<!-- wp:newsletterglue/spacer -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="20" style="height:20px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/heading {"h2_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h2_colour":"#333333"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#333333"><tbody><tr><td class="ng-block-td" align="none" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:35px;padding-bottom:15px;padding-left:20px;padding-right:20px;text-align:none;color:#333333"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:none;color:#333333"><strong>Become a contributor</strong></h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/text {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p>Interested in getting interviewed? Have an insight or perspective that needs to be shared with the world?</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/buttons -->
<table width="100%" cellpadding="0" cellspacing="0" class="ng-block"><tbody><tr><td style="padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px"><div style="gap:20px;justify-content:left" class="wp-block-newsletterglue-buttons"><!-- wp:newsletterglue/button {"radius":"0px","className":""} -->
<div class="wp-block-newsletterglue-button" style="flex-basis:auto"><a class="ng-block-button__link" style="font-family:Helvetica;font-size:16px;line-height:1.5;font-weight:normal;padding-top:8px;padding-bottom:8px;padding-left:20px;padding-right:20px;text-align:center;width:auto;background-color:#0088a0;color:#ffffff;border-width:2px;border-style:solid;border-color:#0088a0;border-radius:0px;box-sizing:border-box">Get involved</a></div>
<!-- /wp:newsletterglue/button --></div></td></tr></tbody></table>
<!-- /wp:newsletterglue/buttons -->

<!-- wp:newsletterglue/spacer {"height":"40px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="40" style="height:40px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/image {"align":"left","width":171,"height":32,"sizeSlug":"large","padding":{"top":"0px","bottom":"0px","left":"20px","right":"0px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block alignleft size-large is-resized"><tbody><tr><td class="ng-block-td" align="left" style="padding-top:0px;padding-bottom:0px;padding-left:20px;padding-right:0px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro-v2/assets/images/templates/logoipsum-logo-52.png" alt="" class="ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="171" height="32"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/image -->

<!-- wp:newsletterglue/text {"color":"#666666","padding":{"top":"15px","right":"20px","bottom":"10px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:15px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p>Logoipsum is an early education and childcare newsletter that is delivered to your inbox weekly. For more information visit www.logoipsum.com</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"14px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:14px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p><a href="{{ unsubscribe_link }}">Unsubscribe</a></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/spacer {"height":"40px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="40" style="height:40px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->';

}

// @codingStandardsIgnoreEnd
