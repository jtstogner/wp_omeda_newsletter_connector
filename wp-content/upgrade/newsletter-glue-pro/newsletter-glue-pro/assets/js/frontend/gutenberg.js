( function( $ ) {
	"use strict";

	// Block: Form.
	$( document ).on( 'submit', '.ngl-form', function( event ) {
		event.preventDefault();

		var theform  = $( this );
		var app 	 = $( this ).attr( 'data-app' );
		var data 	 = theform.serialize() + '&app=' + app + '&action=newsletterglue_block_form_subscribe&security=' + newsletterglue_gutenberg.ajaxnonce;
		var btn		 = theform.find( 'button' );
		var btn_text = btn.html();

		var xhr = $.ajax( {
			type : 'post',
			url : newsletterglue_gutenberg.ajaxurl,
			data : data,
			beforeSend: function() {
				btn.html( newsletterglue_gutenberg.please_wait ).addClass( 'ngl-working' );
			},
			success: function( response ) {

				console.log( response );

				$( '.ngl-form-errors' ).empty().hide();
				btn.html( btn_text ).removeClass( 'ngl-working' );

				if ( response.success ) {

					theform.find( '> *' ).not( '.ng-form-overlay' ).css( { opacity: 0, 'pointer-events' : 'none' } );
					theform.find( '.ng-form-overlay' ).css( { display: 'flex' } );
					theform.find( 'input[type=text], input[type=email]' ).val( '' );

				} else {

					$( '.ngl-form-errors' ).show().html( response.message );

				}

			}
		} );

		return false;
	} );

} )( jQuery );