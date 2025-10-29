<?php
/**
 * Newsletter Metabox.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="ngl-metabox ngl-msgbox-wrap ngl-metabox-flex alt3 alt5 ngl-metabox-automation-is-running">

	<div class="ngl-msg-contain">
		<div class="ngl-top-msg">
		<svg xmlns="http://www.w3.org/2000/svg" width="62.586" height="44" viewBox="0 0 62.586 44">
		  <g id="Group_114" data-name="Group 114" transform="translate(-1931 -424)">
			<g id="Rectangle_188" data-name="Rectangle 188" transform="translate(1931 424)" fill="#fff" stroke="#17a4c6" stroke-linecap="round" stroke-width="1">
			  <rect width="51" height="34" rx="2" stroke="none"/>
			  <rect x="0.5" y="0.5" width="50" height="33" rx="1.5" fill="none"/>
			</g>
			<line id="Line_33" data-name="Line 33" x1="22.672" y1="19.184" transform="translate(1933.616 426.616)" fill="none" stroke="#17a4c6" stroke-linecap="round" stroke-width="1"/>
			<line id="Line_34" data-name="Line 34" y1="19.184" x2="22.672" transform="translate(1956.289 426.616)" fill="none" stroke="#17a4c6" stroke-linecap="round" stroke-width="1"/>
			<rect id="Rectangle_484" data-name="Rectangle 484" width="27" height="17" transform="translate(1965.84 449.5)" fill="#fff"/>
			<g id="Group_115" data-name="Group 115" transform="translate(-51.664 -1343)">
			  <g id="Icon_ionic-ios-calendar" data-name="Icon ionic-ios-calendar" transform="translate(2012.625 1779.5)">
				<path id="Path_31" data-name="Path 31" d="M29.813,6.75H27V8.438A.564.564,0,0,1,26.438,9H25.313a.564.564,0,0,1-.562-.562V6.75H11.25V8.438A.564.564,0,0,1,10.688,9H9.563A.564.564,0,0,1,9,8.438V6.75H6.188A2.821,2.821,0,0,0,3.375,9.563V28.688A2.821,2.821,0,0,0,6.188,31.5H29.813a2.821,2.821,0,0,0,2.813-2.812V9.563A2.821,2.821,0,0,0,29.813,6.75Zm.563,21.094a1.41,1.41,0,0,1-1.406,1.406H7.031a1.41,1.41,0,0,1-1.406-1.406V15.188a.564.564,0,0,1,.563-.562H29.813a.564.564,0,0,1,.563.563Z" fill="#00778d"/>
				<path id="Path_32" data-name="Path 32" d="M11.25,5.063a.564.564,0,0,0-.562-.562H9.563A.564.564,0,0,0,9,5.063V6.75h2.25Z" fill="#00778d"/>
				<path id="Path_33" data-name="Path 33" d="M27,5.063a.564.564,0,0,0-.562-.562H25.313a.564.564,0,0,0-.562.563V6.75H27Z" fill="#00778d"/>
			  </g>
			  <path id="Icon_feather-check" data-name="Icon feather-check" d="M17.277,9,9.524,16.753,6,13.229" transform="translate(2019.5 1788.5)" fill="none" stroke="#00778d" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/>
			</g>
		  </g>
		</svg>
			<span class="ngl-newsletter-sent"><?php _e( 'Your email automation is running.', 'newsletter-glue' ); ?></span>
		</div>

		<div class="ngl-top-msg-view">
			<a href="<?php echo esc_url( 'edit.php?post_type=ngl_log&automation_id=' . $post->ID ); ?>"><?php echo esc_html__( 'View email log', 'newsletter-glue' ); ?></a>
			<?php
				echo '<a href="#" class="ngl-pause-automation" data-post-id="' . absint( $post->ID ) . '">' . esc_html__( 'Pause and edit', 'newsletter-glue' ) . '</a>';
			?>
		</div>
	</div>

	<div class="ngl-top-msg-right">
		<?php do_action( 'newsletterglue_common_action_hook' ); ?>

		<a href="https://newsletterglue.com/docs/email-deliverability-my-email-was-successfully-sent-but-i-still-havent-received-it/" target="_blank" class="ngl-get-help"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M256 76c48.1 0 93.3 18.7 127.3 52.7S436 207.9 436 256s-18.7 93.3-52.7 127.3S304.1 436 256 436c-48.1 0-93.3-18.7-127.3-52.7S76 304.1 76 256s18.7-93.3 52.7-127.3S207.9 76 256 76m0-28C141.1 48 48 141.1 48 256s93.1 208 208 208 208-93.1 208-208S370.9 48 256 48z"></path><path d="M256.7 160c37.5 0 63.3 20.8 63.3 50.7 0 19.8-9.6 33.5-28.1 44.4-17.4 10.1-23.3 17.5-23.3 30.3v7.9h-34.7l-.3-8.6c-1.7-20.6 5.5-33.4 23.6-44 16.9-10.1 24-16.5 24-28.9s-12-21.5-26.9-21.5c-15.1 0-26 9.8-26.8 24.6H192c.7-32.2 24.5-54.9 64.7-54.9zm-26.3 171.4c0-11.5 9.6-20.6 21.4-20.6 11.9 0 21.5 9 21.5 20.6s-9.6 20.6-21.5 20.6-21.4-9-21.4-20.6z"></path></svg><?php echo esc_html__( 'Get help', 'newsletter-glue' ); ?></a>
	</div>

</div>
