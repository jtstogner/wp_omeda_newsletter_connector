<?php
/**
 * Default Patterns.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_Default_Patterns class.
 */
class NGL_Default_Patterns {

	/**
	 * Constructor.
	 */
	public function __construct() {

	}

	/**
	 * Pattern exists?
	 */
	public function post_by_meta_exists( $meta_key, $value ) {
		$posts = get_posts( array(
			'post_type'		=> 'ngl_pattern',
			'post_status'	=> get_post_stati(),
			'meta_key'  	=> $meta_key, // phpcs:ignore
			'meta_value' 	=> $value, // phpcs:ignore
			'number'		=> 1,
		) );

		return ! empty( $posts ) ? $posts[0]->ID : false;
	}

	/**
	 * Create.
	 */
	public function create( $include = false ) {

		global $current_user;

		$defaults = $this->get_patterns();

		$found_post = 0;

		foreach( $defaults as $key => $pattern ) {

			if ( $include && isset( $defaults[ $include ] ) && $key != $include ) {
				continue;
			}

			$content = $pattern['content'];
			$content = str_replace( 'http://localhost/wp-content/plugins/newsletter-glue-pro/assets', NGL_PLUGIN_URL . 'assets', $content );
            $content = str_replace( 'http://localhost/wp-content/plugins/newsletter-glue-pro-v2/assets', NGL_PLUGIN_URL . 'assets', $content );
			$content = str_replace( 'http://0.gravatar.com/avatar/045ef9284c033798e402b62c6e28a4b7?s=96\u0026d=mm\u0026r=g', '', $content );
			$content = str_replace( 'http://0.gravatar.com/avatar/045ef9284c033798e402b62c6e28a4b7?s=96&amp;d=mm&amp;r=g', '', $content );

			$args = array(
				'post_type' 	=> 'ngl_pattern',
				'post_status'	=> 'publish',
				'post_author'	=> $current_user->ID,
				'post_title'	=> $pattern[ 'title' ],
				'post_content'	=> $content,
			);

			$found_post = $this->post_by_meta_exists( '_ngl_core_pattern', $key );

			if ( $found_post ) {
				wp_update_post( array_merge( array( 'ID' => $found_post ), $args ) );
				continue;
			}

			$post_id = wp_insert_post( $args );

			wp_set_object_terms( $post_id, $pattern[ 'category' ], 'ngl_pattern_category' );

			update_post_meta( $post_id, '_ngl_core_pattern', $key );
		}
	}

	/**
	 * Create social links markup for block use.
	 */
	public function create_social_links_markup( $size = 18, $shape = 'default', $color = 'white' ) {
		$links = array();
		$output = '';

		$socials = array( 'instagram', 'tiktok', 'twitter', 'facebook', 'linkedin', 'twitch', 'youtube' );

		foreach( $socials as $social ) {
			$links[ $social ] = get_option( 'newsletterglue_' . $social . '_url' );
			if ( ! empty( $links[ $social ] ) ) {
				$output .= '<!-- wp:newsletterglue/share-link {"service":"' . $social . '","url":"' . $links[ $social ] . '","icon_size":' . $size . ',"icon_shape":"' . $shape . '","icon_color":"' . $color . '"} -->
<a class="wp-block-newsletterglue-share-link ngl-social-link ngl-social-link-' . $social . '" href="' . $links[ $social ] . '" target="_blank" rel="noopener noreferrer"><img src="' . NGL_PLUGIN_URL . 'assets/images/share/' . $shape . '/' . $color . '/' . $social . '.png" width="' . $size . '" height="' . $size . '" style="width:' . $size . 'px;height:' . $size . 'px" class="ngl-inline-image"/></a>
<!-- /wp:newsletterglue/share-link -->';

			}
		}

		return $output;
	}

	/**
	 * Get patterns list.
	 */
	public function get_patterns() {

		// @codingStandardsIgnoreStart

		global $current_user;

		$email_bg 		= newsletterglue_get_theme_option( 'email_bg' );
		$container_bg 	= newsletterglue_get_theme_option( 'container_bg' );

		$logo_width     = get_option( 'newsletterglue_logo_width', 165 );

		$logo_url = NGL_PLUGIN_URL . 'assets/images/email/logo-placeholder.png';
		$ratio = 614 / 186;
		$logo_s_w = $logo_width;
		$logo_s_h = ceil( $logo_width / $ratio );

		// Use current logo.
		$id = get_option( 'newsletterglue_logo_id' );
		if ( $id && wp_get_attachment_url( $id ) ) {

			$logo_url 	= wp_get_attachment_url( $id );
			$data  		= wp_get_attachment_image_src( $id, 'full' );
			$width 		= $data[1];
			$height		= $data[2];

			$w_1 = $logo_width;

			if ( $width > $w_1 ) {
				$ratio = $width / $height;
				$logo_s_w = $w_1;
				$logo_s_h = ceil( $w_1 / $ratio );
			}
		}

		$admin_name 	= '{{ admin_name,fallback=' . get_option( 'newsletterglue_admin_name', get_bloginfo( 'name' ) ) . ' }}';
		$admin_address 	= '{{ admin_address,fallback=' . get_option( 'newsletterglue_admin_address' ) . ' }}';

		/********************************/
		$patterns[ 'header_1' ] = array(
			'title'		=> 'Header with Banner + Description',
			'category' 	=> 'ngl_headers',
			'content'	=> '<!-- wp:newsletterglue/container {"background":"#f9f9f9","color":"#666666","padding":{"top":"20px","bottom":"20px","left":"20px","right":"20px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#666666"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:0px" height="0"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px;text-align:none;color:#666666;background-color:#f9f9f9;border-radius:0px"><!-- wp:newsletterglue/spacer {"height":"30px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="30" style="height:30px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/sections {"layout":"30_70","padding":{"top":"0px","bottom":"0px","left":"0px","right":"0px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-sections ng-block is-stacked-on-mobile"><tbody><tr><td class="ng-columns-wrap" style="padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px"><table width="100%" cellpadding="0" cellspacing="0"><tbody><tr><!-- wp:newsletterglue/section {"width":180,"originalWidth":180,"padding":{"top":"0px","bottom":"0px","left":"0px","right":"0px"}} -->
<td width="180" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:180px;padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/image {"id":8962,"threshold":180,"width":165,"height":49,"sizeSlug":"full","linkDestination":"none"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block size-full is-resized"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro/assets/images/email/logo-placeholder.png" alt="" class="wp-image-8962 ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="165" height="49"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/image --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section {"width":420,"originalWidth":420,"padding":{"top":"0px","bottom":"0px","left":"0px","right":"0px"}} -->
<td width="420" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:420px;padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/text {"color":"#707070","padding":{"top":"0px","right":"0px","bottom":"0px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"13px","align":"right"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="right" style="font-size:13px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:0px;padding-bottom:0px;padding-left:20px;padding-right:0px;text-align:right;color:#707070"><p>A weekly newsletter to help you create better products, and understand the broader impact of technology on our work and our lives.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#707070","link":"#707070","padding":{"top":"15px","right":"0px","bottom":"0px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"13px","align":"right"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="right" style="font-size:13px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:15px;padding-bottom:0px;padding-left:20px;padding-right:0px;text-align:right;color:#707070"><p><a href="{{ webversion }}">Read online</a></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section -->
<td width="auto" class="wp-block-newsletterglue-section ng-block ng-should-remove" valign="top" style="width:auto;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:none;vertical-align:top"><!-- wp:newsletterglue/text {"color":"#666666"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td>
<!-- /wp:newsletterglue/section --></tr></tbody></table></td></tr></tbody></table>
<!-- /wp:newsletterglue/sections -->

<!-- wp:newsletterglue/spacer {"height":"30px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="30" style="height:30px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/image {"id":8963,"width":560,"height":186,"sizeSlug":"large","linkDestination":"none"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block size-large is-resized"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro/assets/images/email/asset-3.png" alt="" class="wp-image-8963 ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="560" height="186"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/image --></td><td class="ng-block-hs ng-block-hs-2" style="width:0px" height="0"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:0px" height="0" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->

<!-- wp:newsletterglue/spacer -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="20" style="height:20px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/heading {"h2_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h2_colour":"#333333"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#333333"><tbody><tr><td class="ng-block-td" align="none" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:35px;padding-bottom:15px;padding-left:20px;padding-right:20px;text-align:none;color:#333333"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:none;color:#333333">Add title</h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/text {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->',
		);

		/********************************/
		$patterns[ 'header_2' ] = array(
			'title'		=> 'Header with Banner',
			'category' 	=> 'ngl_headers',
			'content'	=>	'<!-- wp:newsletterglue/container {"background":"#f9f9f9","color":"#666666","padding":{"top":"20px","bottom":"20px","left":"20px","right":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#666666"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:0px" height="0"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px;text-align:none;color:#666666;background-color:#f9f9f9;border-radius:0px"><!-- wp:newsletterglue/text {"color":"#666666","link":"#707070","padding":{"top":"0px","right":"0px","bottom":"12px","left":"0px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"14px","align":"right"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="right" style="font-size:14px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:0px;padding-bottom:12px;padding-left:0px;padding-right:0px;text-align:right;color:#666666"><p><a href="{{ webversion }}">Read online</a></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/image {"id":8980,"width":560,"height":186,"sizeSlug":"large","linkDestination":"none"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block size-large is-resized"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro/assets/images/email/asset-2.png" alt="" class="wp-image-8980 ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="560" height="186"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/image --></td><td class="ng-block-hs ng-block-hs-2" style="width:0px" height="0"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:0px" height="0" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->

<!-- wp:newsletterglue/spacer -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="20" style="height:20px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/heading {"h2_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h2_colour":"#333333"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#333333"><tbody><tr><td class="ng-block-td" align="none" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:35px;padding-bottom:15px;padding-left:20px;padding-right:20px;text-align:none;color:#333333"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:none;color:#333333">Add title</h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/meta-data {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"show_date":false,"show_url":false,"title":"Header with Banner","url":"Read online","readingtime":"1 mins","readtime":"Reading time:","author_name":"admin","profile_pic":"http://0.gravatar.com/avatar/045ef9284c033798e402b62c6e28a4b7?s=96\u0026d=mm\u0026r=g"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-meta-data ng-block" style="color:#666666" data-date-format="F j, Y"><tbody><tr><td class="ng-block-td" align="none" style="font-size:12px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><span class="ng-block-author" style="display:inline-block"><img src="http://0.gravatar.com/avatar/045ef9284c033798e402b62c6e28a4b7?s=96&amp;d=mm&amp;r=g" width="32" height="32" class="ng-image" style="width:32px;height:32px;display:inline-block;margin:0 6px 0 0;vertical-align:middle;border-radius:32px"/><span>admin</span></span><span class="ng-block-issue" style="display:inline-block"><span class="ng-sep">   |   </span><span></span></span><span class="ng-block-readtime" style="display:inline-block"><span class="ng-sep">   |   </span><span>Reading time:</span> <span class="ngl-metadata-readtime-ajax">1 mins</span></span></td></tr></tbody></table>
<!-- /wp:newsletterglue/meta-data -->

<!-- wp:newsletterglue/text {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->',
		);

		/********************************/
		$patterns[ 'header_3' ] = array(
			'title'		=> 'Header Minimal with Logo',
			'category' 	=> 'ngl_headers',
			'content'	=> '<!-- wp:newsletterglue/container {"background":"#f9f9f9","color":"#666666","padding":{"top":"20px","bottom":"20px","left":"20px","right":"20px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#666666"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:0px" height="0"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px;text-align:none;color:#666666;background-color:#f9f9f9;border-radius:0px"><!-- wp:newsletterglue/image {"id":8962,"width":165,"height":50,"sizeSlug":"full","linkDestination":"none"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block size-full is-resized"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro/assets/images/email/logo-placeholder.png" alt="" class="wp-image-8962 ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="165" height="50"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/image --></td><td class="ng-block-hs ng-block-hs-2" style="width:0px" height="0"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:0px" height="0" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->

<!-- wp:newsletterglue/spacer -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="20" style="height:20px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/meta-data {"color":"#666666","link":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"post_id":9093,"align":"right","show_author":false,"show_date":false,"title":"New campaign","url":"Read online","readingtime":"1 mins","readtime":"Reading time:","author_name":"admin","profile_pic":"http://0.gravatar.com/avatar/045ef9284c033798e402b62c6e28a4b7?s=96\u0026d=mm\u0026r=g"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-meta-data ng-block" style="color:#666666" data-date-format="F j, Y"><tbody><tr><td class="ng-block-td" align="right" style="font-size:12px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px;text-align:right;color:#666666"><span class="ng-block-issue" style="display:inline-block"><span></span></span><span class="ng-block-readtime" style="display:inline-block"><span class="ng-sep">   |   </span><span>Reading time:</span> <span class="ngl-metadata-readtime-ajax">1 mins</span></span><span class="ng-block-url" style="display:inline-block"><span class="ng-sep">   |   </span><a href="{{ blog_post }}">Read online</a></span></td></tr></tbody></table>
<!-- /wp:newsletterglue/meta-data -->

<!-- wp:newsletterglue/spacer -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="20" style="height:20px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/heading {"h2_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h2_colour":"#666666"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:35px;padding-bottom:15px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:none;color:#666666">Add title</h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/text {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->',
		);

		/********************************/
		$patterns[ 'header_4' ] = array(
			'title'		=> 'Header with Coloured Bar',
			'category' 	=> 'ngl_headers',
			'content'	=> '<!-- wp:newsletterglue/container {"background":"#0088a0","color":"#ffffff","padding":{"top":"20px","bottom":"20px","left":"20px","right":"20px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#ffffff"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:0px" height="0"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px;text-align:none;color:#ffffff;background-color:#0088a0;border-radius:0px"><!-- wp:newsletterglue/sections {"layout":"30_70","padding":{"top":"0px","bottom":"0px","left":"0px","right":"0px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-sections ng-block is-stacked-on-mobile"><tbody><tr><td class="ng-columns-wrap" style="padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px"><table width="100%" cellpadding="0" cellspacing="0"><tbody><tr><!-- wp:newsletterglue/section {"width":180,"originalWidth":180,"padding":{"top":"0px","bottom":"0px","left":"0px","right":"0px"}} -->
<td width="180" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:180px;padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/image {"id":8962,"threshold":180,"width":165,"height":49,"sizeSlug":"full","linkDestination":"none"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block size-full is-resized"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro/assets/images/email/logo-placeholder.png" alt="" class="wp-image-8962 ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="165" height="49"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/image --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section {"width":420,"originalWidth":420,"padding":{"top":"0px","bottom":"0px","left":"0px","right":"0px"}} -->
<td width="420" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:420px;padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/meta-data {"color":"#ffffff","link":"#ffffff","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"post_id":9096,"align":"right","show_author":false,"show_date":false,"title":"New campaign","issue":"Issue #","url":"Read online","readingtime":"1 mins","readtime":"Reading time:","author_name":"admin","profile_pic":"http://0.gravatar.com/avatar/045ef9284c033798e402b62c6e28a4b7?s=96\u0026d=mm\u0026r=g"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-meta-data ng-block" style="color:#ffffff" data-date-format="F j, Y"><tbody><tr><td class="ng-block-td" align="right" style="font-size:12px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px;text-align:right;color:#ffffff"><span class="ng-block-issue" style="display:inline-block"><span>Issue #</span></span><span class="ng-block-readtime" style="display:inline-block"><span class="ng-sep">   |   </span><span>Reading time:</span> <span class="ngl-metadata-readtime-ajax">1 mins</span></span><span class="ng-block-url" style="display:inline-block"><span class="ng-sep">   |   </span><a href="{{ blog_post }}">Read online</a></span></td></tr></tbody></table>
<!-- /wp:newsletterglue/meta-data --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section -->
<td width="auto" class="wp-block-newsletterglue-section ng-block ng-should-remove" valign="top" style="width:auto;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:none;vertical-align:top"><!-- wp:newsletterglue/text {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td>
<!-- /wp:newsletterglue/section --></tr></tbody></table></td></tr></tbody></table>
<!-- /wp:newsletterglue/sections --></td><td class="ng-block-hs ng-block-hs-2" style="width:0px" height="0"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:0px" height="0" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->

<!-- wp:newsletterglue/spacer -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="20" style="height:20px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/heading {"h2_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h2_colour":"#666666"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:35px;padding-bottom:15px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:none;color:#666666">Add title</h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/text {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->',
		);

		/********************************/
		$patterns[ 'header_5' ] = array(
			'title'		=> 'Header aligned Center',
			'category' 	=> 'ngl_headers',
			'content'	=> '<!-- wp:newsletterglue/spacer {"height":"60px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="60" style="height:60px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/image {"id":8962,"width":165,"height":50,"sizeSlug":"full","linkDestination":"none"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block size-full is-resized"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro/assets/images/email/logo-placeholder.png" alt="" class="wp-image-8962 ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="165" height="50"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/image -->

<!-- wp:newsletterglue/spacer {"height":"30px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="30" style="height:30px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/meta-data {"color":"#666666","link":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"post_id":9098,"align":"center","show_author":false,"show_date":false,"title":"New campaign","issue":"Issue #","url":"Read online","readingtime":"1 mins","readtime":"Reading time:","author_name":"admin","profile_pic":"http://0.gravatar.com/avatar/045ef9284c033798e402b62c6e28a4b7?s=96\u0026d=mm\u0026r=g"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-meta-data ng-block" style="color:#666666" data-date-format="F j, Y"><tbody><tr><td class="ng-block-td" align="center" style="font-size:12px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px;text-align:center;color:#666666"><span class="ng-block-issue" style="display:inline-block"><span>Issue #</span></span><span class="ng-block-readtime" style="display:inline-block"><span class="ng-sep">   |   </span><span>Reading time:</span> <span class="ngl-metadata-readtime-ajax">1 mins</span></span><span class="ng-block-url" style="display:inline-block"><span class="ng-sep">   |   </span><a href="{{ blog_post }}">Read online</a></span></td></tr></tbody></table>
<!-- /wp:newsletterglue/meta-data -->

<!-- wp:newsletterglue/separator {"color":"#eeeeee"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-separator ng-block" style="color:#eeeeee"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px;color:#eeeeee"><hr style="background-color:transparent;color:transparent;margin:0;border:0;border-top:1px solid #eeeeee;width:560px;height:0"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/separator -->

<!-- wp:newsletterglue/heading {"h2_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h2_colour":"#333333"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#333333"><tbody><tr><td class="ng-block-td" align="none" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:35px;padding-bottom:15px;padding-left:20px;padding-right:20px;text-align:none;color:#333333"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:none;color:#333333">Add title</h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/text {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->',
		);

		/********************************/
		$patterns[ 'header_6' ] = array(
			'title'		=> 'Header Minimal with Separator',
			'category' 	=> 'ngl_headers',
			'content'	=> '<!-- wp:newsletterglue/spacer {"height":"40px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="40" style="height:40px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/image {"align":"left","id":8962,"width":165,"height":50,"sizeSlug":"full","linkDestination":"none","padding":{"top":"0px","bottom":"14px","left":"20px","right":"20px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block alignleft size-full is-resized"><tbody><tr><td class="ng-block-td" align="left" style="padding-top:0px;padding-bottom:14px;padding-left:20px;padding-right:20px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro/assets/images/email/logo-placeholder.png" alt="" class="wp-image-8962 ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="165" height="50"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/image -->

<!-- wp:newsletterglue/meta-data {"color":"#666666","link":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"post_id":9100,"show_author":false,"show_date":false,"title":"New campaign","issue":"Issue #","url":"Read online","readingtime":"1 mins","readtime":"Reading time:","author_name":"admin","profile_pic":"http://0.gravatar.com/avatar/045ef9284c033798e402b62c6e28a4b7?s=96\u0026d=mm\u0026r=g"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-meta-data ng-block" style="color:#666666" data-date-format="F j, Y"><tbody><tr><td class="ng-block-td" align="none" style="font-size:12px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><span class="ng-block-issue" style="display:inline-block"><span>Issue #</span></span><span class="ng-block-readtime" style="display:inline-block"><span class="ng-sep">   |   </span><span>Reading time:</span> <span class="ngl-metadata-readtime-ajax">1 mins</span></span><span class="ng-block-url" style="display:inline-block"><span class="ng-sep">   |   </span><a href="{{ blog_post }}">Read online</a></span></td></tr></tbody></table>
<!-- /wp:newsletterglue/meta-data -->

<!-- wp:newsletterglue/spacer -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="20" style="height:20px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/separator {"color":"#0088a0","align":"left","width":"60px","height":"3px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-separator ng-block" style="color:#0088a0"><tbody><tr><td class="ng-block-td" align="left" style="padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px;color:#0088a0"><hr style="background-color:transparent;color:transparent;margin:0;border:0;border-top:3px solid #0088a0;width:60px;height:0"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/separator -->

<!-- wp:newsletterglue/heading {"h2_padding":{"top":"0px","right":"20px","bottom":"15px","left":"20px"},"h2_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h2_colour":"#333333"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#333333"><tbody><tr><td class="ng-block-td" align="none" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:0px;padding-bottom:15px;padding-left:20px;padding-right:20px;text-align:none;color:#333333"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:none;color:#333333">Add title</h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/text {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->',
		);

		/********************************/
		$patterns[ 'header_7' ] = array(
			'title'		=> 'Header inside Coloured Block',
			'category' 	=> 'ngl_headers',
			'content'	=> '<!-- wp:newsletterglue/container {"background":"#0d566c","color":"#ffffff","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#ffffff"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:0px" height="0"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:0px;padding-right:0px;text-align:none;color:#ffffff;background-color:#0d566c;border-radius:0px"><!-- wp:newsletterglue/image {"align":"right","id":8962,"width":165,"height":50,"sizeSlug":"full","linkDestination":"none","padding":{"top":"0px","bottom":"0px","left":"20px","right":"20px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block alignright size-full is-resized"><tbody><tr><td class="ng-block-td" align="right" style="padding-top:0px;padding-bottom:0px;padding-left:20px;padding-right:20px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro/assets/images/email/logo-placeholder.png" alt="" class="wp-image-8962 ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="165" height="50"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/image -->

<!-- wp:newsletterglue/heading {"h1_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h1_colour":"#ffffff","h2_padding":{"top":"20px","right":"20px","bottom":"8px","left":"20px"},"h2_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h2_colour":"#ffffff","h3_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h3_colour":"#ffffff","h4_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h4_colour":"#ffffff","h5_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h5_colour":"#ffffff","h6_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h6_colour":"#ffffff"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h2" style="color:#ffffff"><tbody><tr><td class="ng-block-td" align="none" style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;padding-top:20px;padding-bottom:8px;padding-left:20px;padding-right:20px;text-align:none;color:#ffffff"><h2 style="font-size:28px;font-family:Helvetica;line-height:1.2;font-weight:normal;text-align:none;color:#ffffff">Add title</h2></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/heading {"level":5,"h1_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h1_colour":"#ffffff","h2_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h2_colour":"#ffffff","h3_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h3_colour":"#ffffff","h4_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h4_colour":"#ffffff","h5_padding":{"top":"0px","right":"20px","bottom":"0px","left":"20px"},"h5_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h5_colour":"#ffffff","h6_font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"h6_colour":"#ffffff"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-heading ng-block ng-block-h5" style="color:#ffffff"><tbody><tr><td class="ng-block-td" align="none" style="font-size:20px;font-family:Helvetica;line-height:1.4;font-weight:normal;padding-top:0px;padding-bottom:0px;padding-left:20px;padding-right:20px;text-align:none;color:#ffffff"><h5 style="font-size:20px;font-family:Helvetica;line-height:1.4;font-weight:normal;text-align:none;color:#ffffff">Subheading</h5></td></tr></tbody></table>
<!-- /wp:newsletterglue/heading -->

<!-- wp:newsletterglue/meta-data {"color":"#ffffff","link":"#ffffff","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"post_id":9102,"show_author":false,"show_date":false,"title":"New campaign","issue":"Issue #","url":"Read online","readingtime":"1 mins","readtime":"Reading time:","author_name":"admin","profile_pic":"http://0.gravatar.com/avatar/045ef9284c033798e402b62c6e28a4b7?s=96\u0026d=mm\u0026r=g"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-meta-data ng-block" style="color:#ffffff" data-date-format="F j, Y"><tbody><tr><td class="ng-block-td" align="none" style="font-size:12px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px;text-align:none;color:#ffffff"><span class="ng-block-issue" style="display:inline-block"><span>Issue #</span></span><span class="ng-block-readtime" style="display:inline-block"><span class="ng-sep">   |   </span><span>Reading time:</span> <span class="ngl-metadata-readtime-ajax">1 mins</span></span><span class="ng-block-url" style="display:inline-block"><span class="ng-sep">   |   </span><a href="{{ blog_post }}">Read online</a></span></td></tr></tbody></table>
<!-- /wp:newsletterglue/meta-data --></td><td class="ng-block-hs ng-block-hs-2" style="width:0px" height="0"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:0px" height="0" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->

<!-- wp:newsletterglue/spacer {"height":"40px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="40" style="height:40px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/text {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->',
		);

		/********************************/
		$patterns[ 'footer_1' ] = array(
			'title'		=> 'Footer Minimal outside Container',
			'category' 	=> 'ngl_footers',
			'content'	=> '<!-- wp:newsletterglue/container {"background":"#f9f9f9","color":"#666666"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#666666"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:0px" height="0"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:0px;padding-right:0px;text-align:none;color:#666666;background-color:#f9f9f9;border-radius:0px"><!-- wp:newsletterglue/text {"color":"#666666","link":"#707070","padding":{"top":"0px","right":"20px","bottom":"25px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"13px","align":"center"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="center" style="font-size:13px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:0px;padding-bottom:25px;padding-left:20px;padding-right:20px;text-align:center;color:#666666"><p>Built with <a href="https://newsletterglue.com/?utm_source=newsletter&amp;utm_medium=ng-signature" target="_blank" rel="noopener noreferrer">Newsletter Glue</a>.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#666666","link":"#707070","padding":{"top":"10px","right":"20px","bottom":"10px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"13px","align":"center"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="center" style="font-size:13px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:center;color:#666666"><p>{{ admin_address,fallback=21 Park Road }}</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#666666","link":"#707070","padding":{"top":"0px","right":"20px","bottom":"10px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"13px","align":"center"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="center" style="font-size:13px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:0px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:center;color:#666666"><p>If you’d no longer like to receive emails from me, you can <a href="{{ unsubscribe_link }}">unsubscribe here</a>.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td><td class="ng-block-hs ng-block-hs-2" style="width:0px" height="0"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:0px" height="0" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->',
		);

		/********************************/
		$patterns[ 'footer_2' ] = array(
			'title'		=> 'Footer Minimal with Social Sharing',
			'category' 	=> 'ngl_footers',
			'content'	=> '<!-- wp:newsletterglue/container {"background":"#f9f9f9","color":"#666666"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#666666"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:0px" height="0"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:0px;padding-right:0px;text-align:none;color:#666666;background-color:#f9f9f9;border-radius:0px"><!-- wp:newsletterglue/text {"color":"#707070","link":"#707070","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"13px","align":"center"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="center" style="font-size:13px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:center;color:#707070"><p>Built with <a href="https://newsletterglue.com/?utm_source=newsletter&amp;utm_medium=ng-signature" target="_blank" rel="noopener noreferrer">Newsletter Glue</a>.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/spacer -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="20" style="height:20px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/sections {"layout":"50_50"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-sections ng-block is-stacked-on-mobile"><tbody><tr><td class="ng-columns-wrap" style="padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px"><table width="100%" cellpadding="0" cellspacing="0"><tbody><tr><!-- wp:newsletterglue/section {"width":300,"originalWidth":300} -->
<td width="300" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:300px;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/text {"color":"#707070","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"12px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="none" style="font-size:12px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#707070"><p>Published with ♥ by {{ admin_name,fallback=Newsletter Glue }}.<br>Here’s where you can find me online:</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/social-icons {"add_description":false,"icon_size":"18px","color":"#666666","padding":{"top":"0px","bottom":"0px","left":"20px","right":"20px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-social-icons ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:0px;padding-bottom:0px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><div class="ngl-share-wrap ng-div" style="line-height:1;font-size:1px"><!-- wp:newsletterglue/social-icon {"service":"x","icon_size":"18px"} -->
<span class="wp-block-newsletterglue-social-icon ng-block ng-social-x" style="display:inline-flex;margin-right:5px;margin-left:0px"><a rel="noopener noreferrer" target="_blank"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro/assets/images/share/round/black/x.png" width="18" height="18" style="width:18px;height:18px" class="ngl-inline-image"/></a></span>
<!-- /wp:newsletterglue/social-icon -->

<!-- wp:newsletterglue/social-icon {"service":"facebook","icon_size":"18px"} -->
<span class="wp-block-newsletterglue-social-icon ng-block ng-social-facebook" style="display:inline-flex;margin-right:5px;margin-left:0px"><a rel="noopener noreferrer" target="_blank"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro/assets/images/share/round/black/facebook.png" width="18" height="18" style="width:18px;height:18px" class="ngl-inline-image"/></a></span>
<!-- /wp:newsletterglue/social-icon -->

<!-- wp:newsletterglue/social-icon {"service":"instagram","icon_size":"18px"} -->
<span class="wp-block-newsletterglue-social-icon ng-block ng-social-instagram" style="display:inline-flex;margin-right:5px;margin-left:0px"><a rel="noopener noreferrer" target="_blank"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro/assets/images/share/round/black/instagram.png" width="18" height="18" style="width:18px;height:18px" class="ngl-inline-image"/></a></span>
<!-- /wp:newsletterglue/social-icon --></div></td></tr></tbody></table>
<!-- /wp:newsletterglue/social-icons --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section {"width":300,"originalWidth":300} -->
<td width="300" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:300px;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/text {"color":"#707070","link":"#707070","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"12px","align":"right"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="right" style="font-size:12px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:right;color:#707070"><p>{{ admin_address,fallback=21 Park Road }}<br><a href="{{ unsubscribe_link }}">Unsubscribe here</a></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section -->
<td width="auto" class="wp-block-newsletterglue-section ng-block ng-should-remove" valign="top" style="width:auto;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:none;vertical-align:top"><!-- wp:newsletterglue/text {"color":"#666666","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#666666"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#666666"><p></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td>
<!-- /wp:newsletterglue/section --></tr></tbody></table></td></tr></tbody></table>
<!-- /wp:newsletterglue/sections --></td><td class="ng-block-hs ng-block-hs-2" style="width:0px" height="0"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:0px" height="0" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->'
		);

		/********************************/
		$patterns[ 'footer_3' ] = array(
			'title'		=> 'Footer inside Coloured Block',
			'category' 	=> 'ngl_footers',
			'content'	=> '<!-- wp:newsletterglue/spacer {"height":"40px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="40" style="height:40px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/text {"color":"#707070","link":"#707070","padding":{"top":"10px","right":"20px","bottom":"10px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"13px","align":"center"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="center" style="font-size:13px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:center;color:#707070"><p>Built with <a href="https://newsletterglue.com/?utm_source=newsletter&amp;utm_medium=ng-signature" target="_blank" rel="noopener noreferrer">Newsletter Glue</a>.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/spacer {"height":"30px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="30" style="height:30px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/container {"background":"#0d566c","color":"#ffffff","link":"#ffffff"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#ffffff"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:0px" height="0"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:0px;padding-right:0px;text-align:none;color:#ffffff;background-color:#0d566c;border-radius:0px"><!-- wp:newsletterglue/social-icons {"add_description":false,"icon_color":"white","icon_size":"20px","color":"#ffffff","align":"right"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-social-icons ng-block" style="color:#ffffff"><tbody><tr><td class="ng-block-td" align="right" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px;text-align:right;color:#ffffff"><div class="ngl-share-wrap ng-div" style="line-height:1;font-size:1px"><!-- wp:newsletterglue/social-icon {"service":"x","icon_size":"20px","align":"right","icon_color":"white"} -->
<span class="wp-block-newsletterglue-social-icon ng-block ng-social-x" style="display:inline-flex;margin-right:0px;margin-left:5px"><a rel="noopener noreferrer" target="_blank"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro/assets/images/share/round/white/x.png" width="20" height="20" style="width:20px;height:20px" class="ngl-inline-image"/></a></span>
<!-- /wp:newsletterglue/social-icon -->

<!-- wp:newsletterglue/social-icon {"service":"facebook","icon_size":"20px","align":"right","icon_color":"white"} -->
<span class="wp-block-newsletterglue-social-icon ng-block ng-social-facebook" style="display:inline-flex;margin-right:0px;margin-left:5px"><a rel="noopener noreferrer" target="_blank"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro/assets/images/share/round/white/facebook.png" width="20" height="20" style="width:20px;height:20px" class="ngl-inline-image"/></a></span>
<!-- /wp:newsletterglue/social-icon -->

<!-- wp:newsletterglue/social-icon {"service":"instagram","icon_size":"20px","align":"right","icon_color":"white"} -->
<span class="wp-block-newsletterglue-social-icon ng-block ng-social-instagram" style="display:inline-flex;margin-right:0px;margin-left:5px"><a rel="noopener noreferrer" target="_blank"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro/assets/images/share/round/white/instagram.png" width="20" height="20" style="width:20px;height:20px" class="ngl-inline-image"/></a></span>
<!-- /wp:newsletterglue/social-icon --></div></td></tr></tbody></table>
<!-- /wp:newsletterglue/social-icons -->

<!-- wp:newsletterglue/sections {"layout":"50_50"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-sections ng-block is-stacked-on-mobile"><tbody><tr><td class="ng-columns-wrap" style="padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px"><table width="100%" cellpadding="0" cellspacing="0"><tbody><tr><!-- wp:newsletterglue/section {"width":300,"originalWidth":300} -->
<td width="300" class="wp-block-newsletterglue-section ng-block" valign="top" style="width:300px;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:top"><!-- wp:newsletterglue/text {"color":"#ffffff","padding":{"top":"8px","right":"20px","bottom":"0px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"13px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#ffffff"><tbody><tr><td class="ng-block-td" align="none" style="font-size:13px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:0px;padding-left:20px;padding-right:20px;text-align:none;color:#ffffff"><p>© 2024 {{ admin_name,fallback=Newsletter Glue }}<br>{{ admin_address,fallback=21 Park Road }}</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section {"width":300,"originalWidth":300,"verticalAlign":"bottom"} -->
<td width="300" class="wp-block-newsletterglue-section ng-block" valign="bottom" style="width:300px;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:table-cell;vertical-align:bottom"><!-- wp:newsletterglue/text {"color":"#ffffff","link":"#ffffff","padding":{"top":"8px","right":"20px","bottom":"0px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"14px","align":"right"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#ffffff"><tbody><tr><td class="ng-block-td" align="right" style="font-size:14px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:0px;padding-left:20px;padding-right:20px;text-align:right;color:#ffffff"><p><a href="{{ unsubscribe_link }}">Unsubscribe</a></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td>
<!-- /wp:newsletterglue/section -->

<!-- wp:newsletterglue/section -->
<td width="auto" class="wp-block-newsletterglue-section ng-block ng-should-remove" valign="top" style="width:auto;padding-top:10px;padding-bottom:10px;padding-left:0px;padding-right:0px;display:none;vertical-align:top"><!-- wp:newsletterglue/text {"color":"#ffffff","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#ffffff"><tbody><tr><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#ffffff"><p></p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td>
<!-- /wp:newsletterglue/section --></tr></tbody></table></td></tr></tbody></table>
<!-- /wp:newsletterglue/sections --></td><td class="ng-block-hs ng-block-hs-2" style="width:0px" height="0"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:0px" height="0" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->',
		);

		/********************************/
		$patterns[ 'footer_4' ] = array(
			'title'		=> 'Footer inside Coloured Block with logo',
			'category' 	=> 'ngl_footers',
			'content'	=> '<!-- wp:newsletterglue/spacer {"height":"40px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="40" style="height:40px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/text {"color":"#707070","link":"#707070","padding":{"top":"10px","right":"20px","bottom":"10px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"13px","align":"center"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="center" style="font-size:13px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:center;color:#707070"><p>Built with <a href="https://newsletterglue.com/?utm_source=newsletter&amp;utm_medium=ng-signature" target="_blank" rel="noopener noreferrer">Newsletter Glue</a>.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/spacer {"height":"40px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="40" style="height:40px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/container {"background":"#0d566c","color":"#ffffff","link":"#ffffff","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-container ng-block" style="color:#ffffff"><tbody><tr><td class="ng-block-vs ng-block-vs-1" style="height:0px" height="0" colspan="3"></td></tr><tr><td class="ng-block-hs ng-block-hs-1" style="width:0px" height="0"></td><td class="ng-block-td" align="none" style="font-size:16px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:20px;padding-bottom:20px;padding-left:0px;padding-right:0px;text-align:none;color:#ffffff;background-color:#0d566c;border-radius:0px"><!-- wp:newsletterglue/spacer {"height":"30px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="30" style="height:30px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/image {"align":"left","id":8962,"width":165,"height":50,"sizeSlug":"full","linkDestination":"none","padding":{"top":"0px","bottom":"0px","left":"20px","right":"0px"}} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-image ng-block alignleft size-full is-resized"><tbody><tr><td class="ng-block-td" align="left" style="padding-top:0px;padding-bottom:0px;padding-left:20px;padding-right:0px"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro/assets/images/email/logo-placeholder.png" alt="" class="wp-image-8962 ng-image" style="border-style:none;border-color:transparent;box-sizing:border-box" width="165" height="50"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/image -->

<!-- wp:newsletterglue/spacer {"height":"30px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="30" style="height:30px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/separator {"color":"#ffffff"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-separator ng-block" style="color:#ffffff"><tbody><tr><td class="ng-block-td" align="center" style="padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px;color:#ffffff"><hr style="background-color:transparent;color:transparent;margin:0;border:0;border-top:1px solid #ffffff;width:560px;height:0"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/separator -->

<!-- wp:newsletterglue/text {"color":"#ffffff","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"13px","align":"right"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#ffffff"><tbody><tr><td class="ng-block-td" align="right" style="font-size:13px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:right;color:#ffffff"><p>{{ admin_address,fallback=21 Park Road }}</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#ffffff","link":"#ffffff","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"13px","align":"right"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#ffffff"><tbody><tr><td class="ng-block-td" align="right" style="font-size:13px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:right;color:#ffffff"><p><a href="{{ unsubscribe_link }}">Unsubscribe here</a>.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text --></td><td class="ng-block-hs ng-block-hs-2" style="width:0px" height="0"></td></tr><tr><td class="ng-block-vs ng-block-vs-2" style="height:0px" height="0" colspan="3"></td></tr></tbody></table>
<!-- /wp:newsletterglue/container -->',
		);

		/********************************/
		$patterns[ 'footer_5' ] = array(
			'title'		=> 'Footer Minimal inside Container',
			'category' 	=> 'ngl_footers',
			'content'	=> '<!-- wp:newsletterglue/spacer {"height":"30px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="30" style="height:30px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/text {"color":"#707070","link":"#707070","padding":{"top":"8px","right":"20px","bottom":"5px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"14px","align":"center"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="center" style="font-size:14px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:5px;padding-left:20px;padding-right:20px;text-align:center;color:#707070"><p>Built with <a href="https://newsletterglue.com/?utm_source=newsletter&amp;utm_medium=ng-signature" target="_blank" rel="noopener noreferrer">Newsletter Glue</a>.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#707070","padding":{"top":"15px","right":"20px","bottom":"10px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"14px","align":"center"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="center" style="font-size:14px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:15px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:center;color:#707070"><p>{{ admin_address,fallback=21 Park Road }}</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#707070","link":"#707070","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"14px","align":"center"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="center" style="font-size:14px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:center;color:#707070"><p>If you’d no longer like to receive emails from me, you can <a href="{{ unsubscribe_link }}">unsubscribe here</a>.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/spacer -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="20" style="height:20px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->'
		);

		/********************************/
		$patterns[ 'footer_6' ] = array(
			'title'		=> 'Footer Minimal with Separator',
			'category' 	=> 'ngl_footers',
			'content'	=> '<!-- wp:newsletterglue/spacer {"height":"30px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-spacer ng-block"><tbody><tr><td class="ng-block-td" height="30" style="height:30px"></td></tr></tbody></table>
<!-- /wp:newsletterglue/spacer -->

<!-- wp:newsletterglue/text {"color":"#707070","link":"#707070","padding":{"top":"8px","right":"20px","bottom":"5px","left":"20px"},"font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"14px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="none" style="font-size:14px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:5px;padding-left:20px;padding-right:20px;text-align:none;color:#707070"><p>Built with <a href="https://newsletterglue.com/?utm_source=newsletter&amp;utm_medium=ng-signature" target="_blank" rel="noopener noreferrer">Newsletter Glue</a>.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/separator {"color":"#0088a0","align":"left","width":"60px","height":"3px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-separator ng-block" style="color:#0088a0"><tbody><tr><td class="ng-block-td" align="left" style="padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px;color:#0088a0"><hr style="background-color:transparent;color:transparent;margin:0;border:0;border-top:3px solid #0088a0;width:60px;height:0"/></td></tr></tbody></table>
<!-- /wp:newsletterglue/separator -->

<!-- wp:newsletterglue/text {"color":"#707070","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"14px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="none" style="font-size:14px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#707070"><p>{{ admin_address,fallback=21 Park Road }}</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->

<!-- wp:newsletterglue/text {"color":"#707070","link":"#707070","font":{"key":"helvetica","name":"Helvetica","style":{"fontFamily":"Helvetica"}},"fontsize":"14px"} -->
<table width="100%" cellpadding="0" cellspacing="0" class="wp-block-newsletterglue-text ng-block" style="color:#707070"><tbody><tr><td class="ng-block-td" align="none" style="font-size:14px;font-family:Helvetica;line-height:1.6;font-weight:normal;padding-top:8px;padding-bottom:10px;padding-left:20px;padding-right:20px;text-align:none;color:#707070"><p>If you’d no longer like to receive emails from me, you can <a href="{{ unsubscribe_link }}">unsubscribe here</a>.</p></td></tr></tbody></table>
<!-- /wp:newsletterglue/text -->'
		);

		return $patterns;

		// @codingStandardsIgnoreEnd
	}

}
