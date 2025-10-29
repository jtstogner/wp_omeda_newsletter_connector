( function( $ ) {
	"use strict";

	// Remove a notice.
	$( document ).on( 'click', '.ngl-notice-dismiss, .ngl-notice-dismiss-link', function( event ) {
		event.preventDefault();
		var el = $( this ).parents( '.ngl-upgrade-notice' );
		var key = el.attr( 'data-key' ) ? el.attr( 'data-key' ) : '';
		$.ajax( {
			type : 'post',
			url : ajaxurl,
			data : 'action=newsletterglue_ajax_remove_a_notice&key=' + key + '&security=' + newsletterglue_params.ajaxnonce,
			beforeSend: function() {
				el.hide();
			}
		} );
		return false;
	} );

	// When schedule is changed.
	$( document ).on( 'change', '#ngl_schedule', function( e ) {
		var val = $( '#ngl_schedule' ).val();
		if ( val === 'draft' || val === 'schedule_draft' ) {
			$( 'span.ngl-stateful-send-text' ).html( newsletterglue_params.send_draft );
		} else {
			$( 'span.ngl-stateful-send-text' ).html( newsletterglue_params.send_now );
		}
	} );

	$( window ).on( 'load', function() {
		if ( $( document ).find( '.ngl-notice' ).length ) {
			$( document ).find( '.ngl-notice' ).prependTo( $( '.newsletterglue-wrap' ) );
		}
		$( 'body .ngl-notice' ).show();
		if ( $( '#ngl-template-styles' ).length ) {
      setTimeout(function() {
        let el = '.editor-header__settings';
        $( '#ngl-template-styles' ).prependTo( el );
      }, 1000 );
		}
	} );

} )( jQuery );