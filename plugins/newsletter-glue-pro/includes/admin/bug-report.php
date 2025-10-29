<?php
/**
 * Bug report.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$newsletterglue_bug_nonce = wp_create_nonce( 'newsletterglue-bug-nonce' ); ?>

<div class="newsletterglue-popup-overlay" data-overlay="bug-report">

	<div class="newsletterglue-serveypanel">
		<form action="#" method="post" id="newsletterglue-bug-report-form">
			<div class="newsletterglue-popup-header">
				<h2><?php _e( 'Request support', 'newsletter-glue' ); ?></h2>
				<div class="newsletterglue-popup-close">
					<svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M405 136.798L375.202 107 256 226.202 136.798 107 107 136.798 226.202 256 107 375.202 136.798 405 256 285.798 375.202 405 405 375.202 285.798 256z"></path></svg>
				</div>
			</div>
			<div class="newsletterglue-popup-notice">
				<div class="newsletterglue-popup-text" style="font-size: 46px;color:#25b169"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 16 16" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"></path></svg></div>
				<div class="newsletterglue-popup-text"><?php _e( 'Support request sent!', 'newsletter-glue' ); ?></div>
				<div class="newsletterglue-popup-text"><?php _e( 'Look out for our response in the next 24-48 hours.', 'newsletter-glue' ); ?></div>
			</div>
			<div class="newsletterglue-popup-body">

				<input type="hidden" class="newsletterglue_bug_nonce" name="newsletterglue_bug_nonce" value="<?php echo esc_attr( $newsletterglue_bug_nonce ); ?>">

				<div class="newsletterglue-popup-field">
					<label for="_bug_details"><?php _e( 'How can we help?*', 'newsletter-glue' ); ?></label>
					<textarea name="_bug_details" id="_bug_details" placeholder="" required ></textarea>
				</div>

				<div class="newsletterglue-popup-field">
					<label for="_bug_name"><?php _e( 'Name*', 'newsletter-glue' ); ?></label>
					<input type="text" name="_bug_name" id="_bug_name" required />
				</div>

				<div class="newsletterglue-popup-field">
					<label for="_bug_email"><?php _e( 'Email*', 'newsletter-glue' ); ?></label>
					<input type="email" name="_bug_email" id="_bug_email" required />
					<span class="newsletterglue-help-d"><?php _e( 'We will follow up via the email you&rsquo;ve provided here.', 'newsletter-glue' ); ?></span>
				</div>

			</div>
			<div class="newsletterglue-popup-footer">
				<div class="action-btns">
					<span class="newsletterglue-spinner"><img src="<?php echo esc_url( admin_url( '/images/spinner.gif' ) ); ?>" alt=""></span>
					<input type="submit" class="button button-primary button-bug-report newsletterglue-popup-allow-bug-report" value="<?php _e( 'Submit', 'newsletter-glue' ); ?>">
					<a href="#" class="button button-secondary newsletterglue-popup-button-close"><?php _e( 'Cancel', 'newsletter-glue' ); ?></a>
				</div>
			</div>
		</form>
	</div>

</div>

<script>
(function( $ ) {

$(function() {

	$(document).on('click', '.ngl-bug-report', function(e){
		e.preventDefault();
		$('.newsletterglue-popup-overlay[data-overlay="bug-report"]').addClass('newsletterglue-active');
		$('body').addClass('newsletterglue-hidden');
		$( '.newsletterglue-popup-body, .newsletterglue-popup-footer' ).show();
		$( '.newsletterglue-popup-notice' ).hide();
		return false;
	});

	$(document).on('click', '.newsletterglue-popup-button-close, .newsletterglue-popup-close', function () {
		close_popup();
	});

	$(document).on('click', ".newsletterglue-serveypanel",function(e){
		e.stopPropagation();
	});

	$( document ).on( 'click', function() {
		close_popup();
	} );

	function close_popup() {
		$('.newsletterglue-popup-overlay[data-overlay="bug-report"]').removeClass('newsletterglue-active');
		$('body').removeClass('newsletterglue-hidden');
	}

	$(document).on('submit', '#newsletterglue-bug-report-form', function(event) {
		event.preventDefault();

		var theform =  $( this );

		var data = theform.serialize() + '&action=newsletterglue_bug_report&security=' + $('.newsletterglue_bug_nonce').val();

		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: data,
			beforeSend: function(){
				$(".newsletterglue-spinner").show();
				$( '.newsletterglue-popup-allow-bug-report' ).attr( 'disabled', 'disabled' );
			}
		}).done(function() {
            $(".newsletterglue-spinner").hide();
			$( '.newsletterglue-popup-allow-bug-report' ).removeAttr( 'disabled' );
			theform.find( 'input[type=text], select, textarea' ).val( '' );
			$( '.newsletterglue-popup-body, .newsletterglue-popup-footer' ).hide();
			$( '.newsletterglue-popup-notice' ).show();
		});

	});

});

})( jQuery );
</script>