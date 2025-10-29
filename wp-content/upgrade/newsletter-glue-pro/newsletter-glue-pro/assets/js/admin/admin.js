( function( $ ) {
	"use strict";

	var ngl_app;
	var ngl_back_screen;
	var xhr;

	// Close popup.
	function ngl_close_popup() {
		var block_id = $( '.ngl-popup-panel' ).find( '.ngl-popup-settings' ).attr( 'data-block' );
		var block_id_demo = $( '.ngl-popup-panel' ).find( '.ngl-popup-demo' ).attr( 'data-block' );
		$( '.ngl-popup-overlay' ).removeClass( 'ngl-active' );
		$( 'body' ).removeClass( 'ngl-popup-hidden' );
		$( '.ngl-popup-panel .ngl-popup-settings' ).appendTo( $( '.ngl-block[data-block=' + block_id + ']' ) );
		$( '.ngl-popup-panel .ngl-popup-demo' ).appendTo( $( '.ngl-block[data-block=' + block_id_demo + ']' ) );
	}

	// Show different connect screens.
	function ngl_show_first_screen() {
		$( '.ngl-card-add' ).removeClass( 'ngl-hidden' );
		$( '.ngl-card-state, .ngl-card-add2, .ngl-card-view' ).addClass( 'ngl-hidden' );
		if ( $( '.ngl-cards' ).hasClass( 'ngl-cards-free' ) ) {
			$( '.ngl-card-add2.ngl-card-mailchimp' ).removeClass( 'ngl-hidden' );
			$( '.ngl-cards input[type=text]' ).val( '' );
		}
		if ( $( '.ngl-card-license-form' ).length ) {
			$( '.ngl-card-license-form' ).removeClass( 'ngl-hidden' );
		}
		if ( $( '.ngl-is-free' ).length ) {
			$( '.ngl-is-free' ).show();
		}
	}

	function ngl_show_testing_screen() {
		$( '.ngl-card-state.is-testing' ).removeClass( 'ngl-hidden' );
		$( '.ngl-card-state.is-invalid' ).addClass( 'ngl-hidden' );
	}

	function ngl_show_not_connected_screen() {
		$( '.ngl-card-state.is-testing' ).addClass( 'ngl-hidden' );
		$( '.ngl-card-state.is-invalid' ).removeClass( 'ngl-hidden' );
	}

	function ngl_show_connected_screen() {

		$( '.ngl-card-state.is-testing' ).addClass( 'ngl-hidden' );
		$( '.ngl-card-state.is-working' ).removeClass( 'ngl-hidden' );

		if ( $( '.ngl-card-view' ).length ) {
			setTimeout( function() {
				$( '.ngl-card-state, .ngl-card-add2' ).addClass( 'ngl-hidden' );
				if ( $( '.ngl-card-view-' + ngl_app ).length ) {
					$( '.ngl-card-view-' + ngl_app ).removeClass( 'ngl-hidden' );
				} else {
					$( '.ngl-card-view' ).removeClass( 'ngl-hidden' );
				}
			}, 2000 );
		} else {

		}

	}

	function ngl_open_modal( el ) {
		var overlay = $( '.ngl-modal-overlay' );

		overlay.removeClass( 'off' );

		var modal	= $( '.ngl-modal-loader' );
		var post_id = el.attr( 'data-post-id' );
		var data = 'action=newsletterglue_ajax_get_log&security=' + newsletterglue_params.ajaxnonce + '&post_id=' + post_id;

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				overlay.html( '<div class="ngl-modal ngl-modal-loader"><div class="ngl-loading"></div></div>' );
			},
			success: function( response ) {
				overlay.find( '.ngl-modal-loader' ).replaceWith( response );
			}
		} );

	}

	function ngl_close_modal() {
		$( '.ngl-modal-overlay' ).addClass( 'off' );
	}

	// Validates the form and output correct notice.
	function ngl_validate_form() {
		var f = $( '.ngl-metabox' );

		var app = f.find( '#ngl_app' ).val();

		if ( f.length == 0 ) {
			return false;
		}

		if ( $( '.ngl-settings' ).length ) {
			return false;
		}

		var ready = true;

		f.find( 'input[type=text].is-required' ).each( function() {
			if ( $( this ).val() == '' || ( $( this ).attr( 'data-force-unready' ) == '1' ) ) {
				ready = false;
				$( this ).closest( '.ngl-metabox-flex' ).addClass( 'is-error' );
			} else {
				$( this ).closest( '.ngl-metabox-flex' ).removeClass( 'is-error' );
			}
		} );

		f.find( '.dropdown.is-required' ).each( function() {
			if ( $( this ).dropdown( 'get value' ) == '' ) {
				ready = false;
				$( this ).closest( '.ngl-metabox-flex' ).addClass( 'is-error' );
			} else {
				$( this ).closest( '.ngl-metabox-flex' ).removeClass( 'is-error' );
			}
		} );

		if ( f.find( '#ngl_send_newsletter' ).is( ':checked' ) ) {
			$( '#ngl_send_newsletter2' ).prop( 'checked', true );
			$( '#ngl_double_confirm' ).val( 'yes' );
		} else {
			$( '#ngl_send_newsletter2' ).prop( 'checked', false );
			$( '#ngl_double_confirm' ).val( 'no' );
		}

		// Campaign Monitor.
		if ( app === 'campaignmonitor' ) {
			var lists = $( '#ngl_lists' ).parent().dropdown( 'get value' );
			var segments = $( '#ngl_segments' ).parent().dropdown( 'get value' );
			if ( ( ! lists || lists.length == 0 ) && ( ! segments || segments.length == 0 ) ) {
				ready = false;
				$( '#ngl_lists, #ngl_segments' ).parents( '.ngl-metabox-flex' ).addClass( 'is-error' );
			} else {
				$( '#ngl_lists, #ngl_segments' ).parents( '.ngl-metabox-flex' ).removeClass( 'is-error' );
			}
		}

		// Is form ready?
		if ( ready ) {
			$( '.ngl-ready' ).removeClass( 'is-hidden' );
			$( '.ngl-not-ready' ).addClass( 'is-hidden' );
			$( '.ngl-not-ready' ).parents( '.ngl-metabox-flex.alt3' ).removeClass( 'ngl-unready' );
			$( '.ngl-newsletter-errors' ).remove();
			$( '.ngl-top-checkbox' ).removeClass( 'disable-send' );
		} else {
			$( '.ngl-ready' ).addClass( 'is-hidden' );
			$( '.ngl-not-ready' ).removeClass( 'is-hidden' );
			$( '.ngl-not-ready' ).parents( '.ngl-metabox-flex.alt3' ).addClass( 'ngl-unready' );
			if ( $( '.ngl-newsletter-errors' ).length == 0 ) {
        let el = $( '.editor-header__settings' );
				el.prepend( '<span class="ngl-newsletter-errors">' + newsletterglue_params.publish_error + '</span>' );
			}
			$( '.ngl-top-checkbox' ).addClass( 'disable-send' );
			$( '#ngl_double_confirm' ).val( 'no' );
		}
		
		if ( ! $( '#ngl_send_newsletter' ).is( ':checked' ) ) {
			$( '.ngl-newsletter-errors' ).remove();
			$( '.ngl-top-checkbox' ).removeClass( 'disable-send' );
		}
	}

	// validate the email.
	function ngl_validate_email() {

		if ( $( '#ngl_from_email' ).length == 0 ) {
			return false;
		}

		var email_  = $( '#ngl_from_email' );
		var email 	= email_.val();
		var elem    = email_.parent().parent().parent();
		var app 	= $( '#ngl_app' ).val();

		var data = 'action=newsletterglue_ajax_verify_email&security=' + newsletterglue_params.ajaxnonce + '&email=' + email + '&app=' + app;

		if ( elem.parents( '.ngl-metabox-if-checked' ).hasClass( 'ngl-metabox-placeholder' ) ) {
			return false;
		}

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				if ( ! $( '#ngl_from_email' ).hasClass( 'no-support-verify' ) ) {
					elem.find( '.ngl-process' ).addClass( 'is-hidden' );
					elem.find( '.ngl-process.is-waiting' ).removeClass( 'is-hidden' );
					elem.find( '.ngl-label-more' ).empty();
				}
			},
			success: function( response ) {
				elem.find( '.ngl-process' ).addClass( 'is-hidden' );
				if ( response.success || response === true ) {
					if ( response.success ) {
						if ( ! $( '#ngl_from_email' ).hasClass( 'no-support-verify' ) ) {
							elem.find( '.ngl-process.is-valid' ).removeClass( 'is-hidden' );
							elem.find( '.ngl-process.is-valid .ngl-process-text' ).html( response.success );
							setTimeout( function() {
								elem.find( '.ngl-process' ).addClass( 'is-hidden' );
							}, 1500 );
						}
					}
					email_.parent().parent().parent().removeClass( 'is-error' );
					email_.attr( 'data-force-unready', '0' );
				} else {
					elem.find( '.ngl-process.is-invalid' ).removeClass( 'is-hidden' );
					elem.find( '.ngl-process.is-invalid .ngl-process-text' ).html( response.failed );
					if ( response.failed_details ) {
						elem.find( '.ngl-label-more' ).html( response.failed_details );
					}
					email_.parent().parent().parent().addClass( 'is-error' );
					email_.attr( 'data-force-unready', '1' );
				}
				ngl_validate_form();
			}
		} );
	}

	// Init fields.
	$( '.ngl .ui.dropdown, .ngl-metabox .ui.dropdown' ).dropdown( { onChange: function() { ngl_validate_form(); } } );
	$( '.ngl .ui.checkbox' ).checkbox();

	// Save theme colors in Dom.
	var ngl_colors = null;
	var ngl_sizes  = null;
	if ( wp.data && wp.data.select( 'core/editor' ) ) {
		wp.data.subscribe( function() {
			ngl_colors = wp.data.select( 'core/editor' ).getEditorSettings().colors;
			ngl_sizes  = wp.data.select( 'core/editor' ).getEditorSettings().fontSizes;
		} );
	}

	// Frequency changes.
	if ( $( '.ngl-select-frequency' ).length ) {
		var freq = $( '.ngl-select-frequency select' ).val();
		if ( freq == 'monthly' ) {
			$( '.ngl-select-monthday' ).show();
			$( '.ngl-select-weekday' ).hide();
			$( '.ngl-select-monthfr' ).show();
			$( '.ngl-select-day-exception' ).hide();
		} else {
			$( '.ngl-select-monthfr' ).hide();
		}
		if ( freq == 'weekly' ) {
			$( '.ngl-select-monthday' ).hide();
			$( '.ngl-select-weekday' ).show();
			$( '.ngl-select-day-exception' ).hide();
		}
		if ( freq == 'two_weeks' ) {
			$( '.ngl-select-monthday' ).hide();
			$( '.ngl-select-weekday' ).show();
			$( '.ngl-select-day-exception' ).hide();
		}
		if ( freq == 'daily' ) {
			$( '.ngl-select-monthday' ).hide();
			$( '.ngl-select-weekday' ).hide();
			$( '.ngl-select-day-exception' ).show();
		}
		if ( freq == 'two_mins' ) {
			$( '.ngl-select-frequency-on' ).hide();
			$( '.ngl-select-day-exception' ).hide();
		} else {
			$( '.ngl-select-frequency-on' ).show();
			$( '.ngl-select-day-exception' ).show();
		}
	}

	$( document ).on( 'change', '.ngl-select-frequency select', function() {
		var freq = $( '.ngl-select-frequency select' ).val();
		if ( freq == 'monthly' ) {
			$( '.ngl-select-monthday' ).show();
			$( '.ngl-select-weekday' ).hide();
			$( '.ngl-select-monthfr' ).show();
			$( '.ngl-select-day-exception' ).hide();
		} else {
			$( '.ngl-select-monthfr' ).hide();
		}
		if ( freq == 'weekly' ) {
			$( '.ngl-select-monthday' ).hide();
			$( '.ngl-select-weekday' ).show();
			$( '.ngl-select-day-exception' ).hide();
		}
		if ( freq == 'two_weeks' ) {
			$( '.ngl-select-monthday' ).hide();
			$( '.ngl-select-weekday' ).show();
			$( '.ngl-select-day-exception' ).hide();
		}
		if ( freq == 'daily' ) {
			$( '.ngl-select-monthday' ).hide();
			$( '.ngl-select-weekday' ).hide();
			$( '.ngl-select-day-exception' ).show();
		}
		if ( freq == 'two_mins' ) {
			$( '.ngl-select-frequency-on' ).hide();
			$( '.ngl-select-day-exception' ).hide();
		} else {
			$( '.ngl-select-frequency-on' ).show();
		}
	} );

	// When a list is changed for Campaign Monitor.
	$( document ).on( 'change', '.ngl-modal[data-app=campaignmonitor] #ngl_lists', function() {
		var val = $( this ).val();
		var continuethis = false;
		if ( val && val.length ) {
			continuethis = true;
		} else {
			var next_val = $( '.ngl-modal[data-app=campaignmonitor] #ngl_segments' ).parents( '.ui' ).dropdown( 'get value' );
			if ( ! next_val || next_val.length == 0 ) {
				continuethis = false
			}
		}
		if ( continuethis ) {
			$( this ).parents( '.ngl-metabox-flex' ).removeClass( 'is-error' );
			$( '.ngl-modal[data-app=campaignmonitor] #ngl_segments' ).parents( '.ngl-metabox-flex' ).removeClass( 'is-error' );
			$( '.ngl-boarding-next' ).removeClass( 'disabled' ).addClass( 'ready' );
		} else {
			$( this ).parents( '.ngl-metabox-flex' ).addClass( 'is-error' );
			$( '.ngl-modal[data-app=campaignmonitor] #ngl_segments' ).parents( '.ngl-metabox-flex' ).addClass( 'is-error' );
			$( '.ngl-boarding-next' ).addClass( 'disabled' ).removeClass( 'ready' );
		}
	} );

	// When a segment is changed for Campaign Monitor.
	$( document ).on( 'change', '.ngl-modal[data-app=campaignmonitor] #ngl_segments', function() {
		var val = $( this ).val();
		var continuethis = false;
		if ( val && val.length ) {
			continuethis = true;
		} else {
			var next_val = $( '.ngl-modal[data-app=campaignmonitor] #ngl_lists' ).parents( '.ui' ).dropdown( 'get value' );
			if ( ! next_val || next_val.length == 0 ) {
				continuethis = false
			}
		}
		if ( continuethis ) {
			$( this ).parents( '.ngl-metabox-flex' ).removeClass( 'is-error' );
			$( '.ngl-modal[data-app=campaignmonitor] #ngl_lists' ).parents( '.ngl-metabox-flex' ).removeClass( 'is-error' );
			$( '.ngl-boarding-next' ).removeClass( 'disabled' ).addClass( 'ready' );
		} else {
			$( this ).parents( '.ngl-metabox-flex' ).addClass( 'is-error' );
			$( '.ngl-modal[data-app=campaignmonitor] #ngl_lists' ).parents( '.ngl-metabox-flex' ).addClass( 'is-error' );
			$( '.ngl-boarding-next' ).addClass( 'disabled' ).removeClass( 'ready' );
		}
	} );

	// Date and time picker.
	$( '.ngl-date' ).flatpickr( {
		enableTime: true,
		dateFormat: "Y-m-d H:i:s",
		altInput: true,
		enableSeconds: true,
		altFormat: "H:i:s, Y/m/d",
		minDate: "today",
		onChange: function() { ngl_validate_form(); }
	} );

	// When user clicks to add new connection.
	$( document ).on( 'click', '.ngl-card-add', function( event ) {
		$( this ).addClass( 'ngl-hidden' );
		$( '.ngl-card-base' ).removeClass( 'ngl-hidden' );
	} );

	// When a app is selected.
	$( '.ngl-app' ).dropdown( 'setting', 'onChange', function( val ) {
		$( this ).parents( '.ngl-card-base' ).addClass( 'ngl-hidden' );
		$( '.ngl-card-' + val ).removeClass( 'ngl-hidden' );
		ngl_app = val;
		$( '.ngl-card-add2.ngl-card-' + ngl_app ).find( '.ngl-card-link-start' ).show();
	} );

	// Back one screen.
	$( document ).on( 'click', '.ngl-back', function( event ) {
		
		if ( ! ngl_back_screen ) {
			var screen = $( this ).attr( 'data-screen' );
			$( '.ngl-app' ).dropdown( 'clear' );
			$( this ).parent().parent().addClass( 'ngl-hidden' );
			$( '.' + screen ).removeClass( 'ngl-hidden' );
		} else {
			$( this ).parent().parent().addClass( 'ngl-hidden' );
		}
	} );

	// License form.
	$( document ).on( 'submit', '.ngl-license-form', function( event ) {
		event.preventDefault();

		var theform = $( this );
		var data 	= theform.serialize() + '&action=newsletterglue_check_license&security=' + newsletterglue_params.ajaxnonce;

		var stop_form = false;
		theform.find( 'input[type=text]:visible' ).each( function() {
			if ( $( this ).val() == '' ) {
				$( this ).addClass( 'error' ).focus();
				stop_form = true;
			}
		} );

		if ( stop_form ) {
			return false;
		}

		xhr = $.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				ngl_show_testing_screen();
			},
			success: function( result ) {
				setTimeout( function() {
					if ( result.status === 'invalid' ) {
						theform.parents( '.ngl-cards' ).find( '.ngl-card-state.is-invalid .ngl-card-state-text' ).html( result.message );
						ngl_show_not_connected_screen();
						$( '.ngl-license-review' ).show();
					}
					if ( result.status === 'valid' ) {
						ngl_show_connected_screen();
						$( '.ngl-license-review' ).hide();
						$( '.ngl-card-heading-sub' ).html( result.tier_name );
					}
				}, 1000 );

			},
			error: function() {
				ngl_show_not_connected_screen();
			}
		} );

		return false;
	} );

	// Connection form.
	$( document ).on( 'submit', '.ngl-fields form', function( event ) {
		event.preventDefault();

		var theform = $( this );
		var app 	= $( this ).parents( '.ngl-card-add2' ).attr( 'data-app' );
		var data 	= theform.serialize() + '&action=newsletterglue_ajax_connect_api&security=' + newsletterglue_params.ajaxnonce + '&app=' + app;

		ngl_app = app;

		var stop_form = false;
		if ( ! $( '.ngl-card-' + app ).hasClass( 'ngl-hidden' ) ) {
			theform.find( 'input[type=text]:visible' ).each( function() {
				if ( $( this ).val() == '' ) {
					$( this ).addClass( 'error' ).focus();
					stop_form = true;
				}
			} );
		}

		if ( stop_form ) {
			return false;
		}

		xhr = $.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				ngl_show_testing_screen();
			},
			success: function( result ) {
				console.log( result );
				setTimeout( function() {
					if ( result.response === 'invalid' ) {
						ngl_show_not_connected_screen();
					}
					if ( result.response === 'successful' ) {
						ngl_show_connected_screen();
					}
				}, 1000 );
			}
		} );

		return false;
	} );

	// Stop test.
	$( document ).on( 'click', '.ngl-ajax-stop-test', function( event ) {
		event.preventDefault();
		var el = $( this ).parents( '.ngl-card-state' );
		el.addClass( 'ngl-hidden' );
		xhr.abort();
		return false;
	} );

	// Test connection.
	$( document ).on( 'click', '.ngl-ajax-test-connection', function( event ) {
		event.preventDefault();

		ngl_app = $( this ).parents( '.ngl-card-view' ).attr( 'data-app' );

		if ( ngl_app ) {
			$( '.ngl-card-add2.ngl-card-' + ngl_app + ' .ngl-fields form' ).trigger( 'submit' );
		} else {
			$( '.ngl-card-add2 .ngl-license-form' ).trigger( 'submit' );
		}

		return false;

	} );

	// Test again.
	$( document ).on( 'click', '.ngl-ajax-test-again', function( event ) {
		event.preventDefault();
		if ( $( '.ngl-card-add2 .ngl-fields form' ).length ) {
			$( '.ngl-card-add2[data-app="' + ngl_app + '"] .ngl-fields form' ).trigger( 'submit' );
		}
		if ( $( '.ngl-card-add2 .ngl-license-form' ).length ) {
			$( '.ngl-card-add2 .ngl-license-form' ).trigger( 'submit' );
		}
		return false;
	} );

	// Close not connecting test.
	$( document ).on( 'click', '.ngl-ajax-test-close', function( event ) {
		event.preventDefault();
		$( '.ngl-card-state.is-invalid' ).addClass( 'ngl-hidden' );
		return false;
	} );

	// Edit connection details.
	$( document ).on( 'click', '.ngl-ajax-edit-connection', function( event ) {
		event.preventDefault();
		$( '.ngl-card-state.is-invalid' ).addClass( 'ngl-hidden' );
		if ( $( this ).parents( '.ngl-card-view' ).is( ':visible' ) ) {
			ngl_app = $( this ).parents( '.ngl-card-view' ).attr( 'data-app' );
			ngl_back_screen = $( this ).parents( '.ngl-card-view-' + ngl_app );
		}
		if ( ngl_app ) {
			$( '.ngl-card-add2.ngl-card-' + ngl_app ).removeClass( 'ngl-hidden' );
			$( '.ngl-card-add2.ngl-card-' + ngl_app ).find( '.ngl-card-link-start' ).show();
		} else {
			$( '.ngl-card-add2.ngl-hidden' ).removeClass( 'ngl-hidden' );
		}
		return false;
	} );

	// Remove connection.
	$( document ).on( 'click', '.ngl-ajax-remove-connection', function( event ) {
		event.preventDefault();
		ngl_app = $( this ).parents( '.ngl-card-view' ).attr( 'data-app' );
		$( '.ngl-ajax-remove' ).attr( 'data-ngl_app', ngl_app );
		$( '.ngl-card-state.confirm-remove' ).removeClass( 'ngl-hidden' );
		return false;
	} );

	// Confirm remove connection.
	$( document ).on( 'click', '.ngl-ajax-remove', function( event ) {
		event.preventDefault();

		$( '.ngl-card-state.confirm-remove' ).addClass( 'ngl-hidden' );
		$( '.ngl-card-state.is-removed' ).removeClass( 'ngl-hidden' );
		$( '.ngl-app' ).dropdown( 'clear' );

		var app = $( this ).attr( 'data-ngl_app' );
		if ( app ) {
			var action = 'newsletterglue_ajax_remove_api';
		} else {
			var action = 'newsletterglue_deactivate_license';
		}

		if ( app ) {
			var data = 'action=' + action + '&security=' + newsletterglue_params.ajaxnonce + '&app=' + app;
		} else {
			var data = 'action=' + action + '&security=' + newsletterglue_params.ajaxnonce;
		}

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			success: function( result ) {

				if ( ! app ) {
					$( '#newsletterglue_pro_license' ).val( '' );
				}

				setTimeout( function() {
					ngl_show_first_screen();
				}, 2000 );

			}
		} );

		return false;
	} );

	// Toggle metabox options.
	$( document ).on( 'change', '#ngl_send_newsletter', function() {
		if ( $( '.ngl-top-checkbox' ).length == 0 && $( '.ngl-no-connection' ).length == 0 && $( '.ngl-msgbox-wrap:visible' ).length == 0 && $( '.ngl-reset:visible' ).length == 0 ) {
			if ( newsletterglue_params.send_newsletter ) {
        let el = $( '.editor-header__settings' );
				el.prepend( '<div class="ngl-top-checkbox"><label><input type="checkbox" name="ngl_send_newsletter2" id="ngl_send_newsletter2" value="1">' + newsletterglue_params.send_newsletter + '</label></div>' );
			}
		}
		ngl_validate_form();
		if ( ! $( this ).is( ':checked' ) ) {
			if ( $( '.ngl-admin-automation-mb' ).length ) {
				$( '.ngl-admin-automation-mb' ).removeClass( 'is-enabled' ).addClass( 'is-paused' );
				$( '.ngl-admin-automation-mb .ngl-stateful-send-text' ).html( newsletterglue_params.automation_paused );
				$( '.ngl-admin-automation-mb .ngl-field-master-help' ).html( newsletterglue_params.automation_p_help );
			}
			$( '#ngl_send_newsletter2' ).prop( 'checked', false );
		} else {
			if ( $( '.ngl-admin-automation-mb' ).length ) {
				if ( $( '.ngl-automation-state' ).attr( 'data-state' ) === 'ready' ) {
					$( '.ngl-admin-automation-mb .ngl-stateful-send-text' ).html( newsletterglue_params.automation_run );
					$( '.ngl-admin-automation-mb .ngl-field-master-help' ).html( newsletterglue_params.automation_r_help );
				} else {
					$( '.ngl-admin-automation-mb' ).addClass( 'is-enabled' );
					$( '.ngl-admin-automation-mb .ngl-stateful-send-text' ).html( newsletterglue_params.automation_enabled );
					$( '.ngl-admin-automation-mb .ngl-field-master-help' ).html( newsletterglue_params.automation_e_help );
				}
			}
			$( '.ngl-top-checkbox' ).removeClass( 'is-hidden' );
		}
	} );

	// Toggle for top send newsletter checkbox.
	$( document ).on( 'change', '#ngl_send_newsletter2', function() {
		if ( $( this ).is( ':checked' ) ) {
			$( '#ngl_send_newsletter' ).prop( 'checked', true ).trigger( 'change' );
		} else {
			$( '#ngl_send_newsletter' ).prop( 'checked', false ).trigger( 'change' );
		}
	} );

	// Revalidate email.
	$( document ).on( 'change', '#newsletter_glue_metabox #ngl_from_email', function() {
		ngl_validate_email();
	} );

	// Run form validation when user edit metabox fields.
	$( document ).on( 'change', '.ngl-metabox input[type=text]', function() {
		ngl_validate_form();
	} );

	// Copy post title into newsletter subject.
	$( document ).on( 'mouseleave focusout blur', '.editor-post-title__input', function() {
		if ( $( '.ngl-no-connection' ).length == 0 ) {
			var titleContent = $( this ).children().remove().end().text().replace(/\s/g, '');
			if ( titleContent && $( '#ngl_subject' ).length && $( '#ngl_subject' ).val().replace(/\s/g, '') == '' ) {
				$( '#ngl_subject' ).val( $( this ).children().remove().end().text() ).trigger( 'change' );
			}
		}
	} );

	// Reset newsletter.
	$( document ).on( 'click', '.ngl-reset-newsletter', function( event ) {
		event.preventDefault();

		var el = $( this );
		var post_id = $( this ).attr( 'data-post_id' );

		var data = 'action=newsletterglue_ajax_reset_newsletter&security=' + newsletterglue_params.ajaxnonce + '&post_id=' + post_id;

		$( '#ngl_double_confirm' ).val( 'no' );

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				el.addClass( 'loading' );
			},
			success: function( result ) {
				el.removeClass( 'loading' );
				$( '.ngl-reset, .ngl-msgbox-wrap' ).addClass( 'is-hidden' );
				$( '.ngl-send, .ngl-sending-box' ).removeClass( 'is-hidden' );
			}
		} );

		return false;
	} );

	// Pause newsletter.
	$( document ).on( 'click', '.ngl-pause-automation', function( event ) {
		event.preventDefault();

		var el = $( this );
		var post_id = $( this ).attr( 'data-post_id' );

		var data = 'action=newsletterglue_ajax_reset_newsletter&security=' + newsletterglue_params.ajaxnonce + '&post_id=' + post_id;

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				el.addClass( 'loading' );
			},
			success: function( result ) {
				el.removeClass( 'loading' );
				$( '#ngl_send_newsletter' ).prop( 'checked', false );
				$( '.ngl-admin-automation-mb' ).removeClass( 'is-enabled is-automated is-automating' ).addClass( 'is-paused' );
			}
		} );

		return false;
	} );

	// Test newsletter.
	$( document ).on( 'click', '.ngl-test-email', function( event ) {
		event.preventDefault();

		var el = $( this );
		var post_id = $( this ).attr( 'data-post_id' );
		var mb = el.parents( '.ngl-metabox' );

		var data = 'action=newsletterglue_ajax_test_email&security=' + newsletterglue_params.ajaxnonce + '&post_id=' + post_id;

		mb.find( 'input[type=text], select, input[type=hidden], textarea' ).each( function() {
			data = data + '&' + $( this ).attr( 'id' ) + '=' + encodeURIComponent( $( this ).val() );
		} );

		mb.find( 'input[type=checkbox]' ).each( function() {
			if ( $( this ).is( ':checked' ) ) {
				data = data + '&' + $( this ).attr( 'id' ) + '=1';
			}
		} );

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				$( '.ngl-is-default' ).hide();
				$( '.ngl-is-sending' ).show();
				$( '.ngl-test-result' ).hide();
			},
			success: function( response ) {
				console.log( response );
				$( '.ngl-is-sending' ).hide();
				$( '.ngl-action-link' ).show();
				if  ( response && response.success ) {
					$( '.ngl-is-valid' ).show();
					$( '.ngl-test-result.ngl-is-valid' ).show().html( response.success );
				} else {
					$( '.ngl-is-invalid' ).show();
					if ( response && response.fail ) {
						$( '.ngl-test-result.ngl-is-invalid' ).show().html( response.fail );
					} else {
						$( '.ngl-test-result.ngl-is-invalid' ).show().html( newsletterglue_params.unknown_error );
					}
				}

			}
		} );

		return false;
	} );

	// Retest.
	$( document ).on( 'click', '.ngl-retest', function( event ) {
		event.preventDefault();
		$( '.ngl-action-link, .ngl-action button, .ngl-is-valid, .ngl-is-invalid' ).hide();
		$( '.ngl-is-default' ).show();
		return false;
	} );

	// Scroll to newsletter form.
	$( document ).on( 'click', '.ngl-newsletter-errors a', function( event ) {
		event.preventDefault();
		$( '.ngl-metabox .is-error' ).find( 'input:first' ).focus();
		return false;
	} );

	// Show modal.
	$( document ).on( 'click', 'a[href="#ngl-status-log"]', function( event ) {
		event.preventDefault();
		var trigger = $( this );
		var post_id = $( this ).attr( 'data-post_id' );
		ngl_open_modal( trigger );
		return false;
	} );

	// Close modal.
	$( document ).on( 'click', '.ngl-modal-close', function( event ) {
		event.preventDefault();
		ngl_close_modal();
		return false;
	} );

	// When the overlay is clicked.
	$( document ).on( 'click', '.ngl-modal-overlay:not(.onboarding)', function( event ) {
		event.preventDefault();
		ngl_close_modal();
		return false;
	} );

	// When the overlay is clicked.
	$( document ).on( 'click', '.ngl-modal', function( event ) {
		event.stopPropagation();
	} );

	// Trigger newsletter sent message.
	$( document ).on( 'click', '.editor-post-publish-button', function( event ) {

		if ( $( '.ngl-automation-state' ).length ) {
			var currentState = $( '.ngl-automation-state' ).attr( 'data-state' );
			var isRun = $( '#ngl_send_newsletter' ).is( ':checked' );

			if ( currentState === 'ready' ) {
				if ( isRun ) {
					$( '.ngl-automation-state' ).attr( 'data-state', 'enabled' );
					$( '.ngl-admin-automation-mb' ).addClass( 'is-enabled' ).removeClass( 'is-paused' );
					$( '.ngl-admin-automation-mb .ngl-stateful-send-text' ).html( newsletterglue_params.automation_enabled );
					$( '.ngl-admin-automation-mb .ngl-field-master-help' ).html( newsletterglue_params.automation_e_help );
				}
			}

			if ( $( '.block-editor-writing-flow' ).length && isRun ) {
				$( '.ngl-admin-automation-mb' ).addClass( 'is-automating' );
				$( '.ngl-admin-automation-mb' ).prepend( newsletterglue_params.loader2 );

				setTimeout( function() {
					$( '.ngl-loader-automation' ).remove();
					$( '.ngl-admin-automation-mb' ).addClass( 'is-automated' ).removeClass( 'is-paused is-automating' );
				}, 1500 );
			}

		}

		// Add message box.
		if ( $( '#ngl_send_newsletter2' ).is( ':checked' ) ) {
			$( '#ngl_double_confirm' ).val( 'yes' );
			$( '#ngl_send_newsletter, #ngl_send_newsletter2' ).prop( 'checked', false );
			$( '.ngl-msgbox-wrap' ).removeClass( 'is-hidden' );
			$( '.ngl-reset' ).addClass( 'is-hidden' );
			$( '.ngl-top-checkbox' ).addClass( 'is-hidden' );
			$( '.ngl-send, .ngl-sending-box' ).addClass( 'is-hidden' );

			if ( $( '.block-editor-writing-flow' ).length ) {
				$( '.ngl-msgbox-wrap' ).find( 'div' ).css( { opacity: 0, 'pointer-events' : 'none' } );
				$( '.ngl-msgbox-wrap' ).html( newsletterglue_params.loader );
				setTimeout( function() {
					var post_id = $( '#post_ID' ).val();
					var data = 'action=newsletterglue_ajax_get_newsletter_state&security=' + newsletterglue_params.ajaxnonce + '&post_id=' + post_id;
					$.ajax( {
						type : 'post',
						url : newsletterglue_params.ajaxurl,
						data : data,
						success: function( response ) {
							if ( response ) {
								$( '.ngl-msgbox-wrap' ).empty().html( response );
							}
						}
					} );
				}, 3000 );
			}

		} else {
			if ( $( '#ngl_double_confirm' ).val() == 'yes' ) {
				$( '#ngl_double_confirm' ).val( 'no' );
			}
		}

	} );

	// Textarea tab indent.
	$( document ).on( 'keydown', '.ngl-textarea', function(e) {
	  var keyCode = e.keyCode || e.which;

	  if (keyCode == 9) {
		e.preventDefault();
		var start = this.selectionStart;
		var end = this.selectionEnd;

		// set textarea value to: text before caret + tab + text after caret
		$(this).val($(this).val().substring(0, start)
					+ "\t"
					+ $(this).val().substring(end));

		// put caret at right position again
		this.selectionStart =
		this.selectionEnd = start + 1;
	  }
	});

	// Save settings.
	$( document ).on( 'click', '.ngl-save-perms:not(.saved)', function( event ) {
		event.preventDefault();
		var savebtn = $( this );
		var role = $( '#ngl_select_role' ).val();
		var name = $( 'input[type=checkbox][name="ngl_perms_' + role + '"]' );
		var data = 'action=newsletterglue_ajax_save_permissions&security=' + newsletterglue_params.ajaxnonce + '&role=' + role;

		name.each( function() {
			var ischecked = $( this ).is( ':checked' ) ? 1 : 0;
			data = data + '&' + $( this ).val() + '=' + ischecked;
		} );

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				savebtn.html( newsletterglue_params.saving );
			},
			success: function( response ) {
				savebtn.addClass( 'saved' ).html( newsletterglue_params.saved );
				setTimeout( function() {
					savebtn.removeClass( 'saved' ).html( newsletterglue_params.save );
				}, 2000 );
				console.log( response );
			}
		} );

		return false;

	} );

	// Save slugs.
	$( document ).on( 'click', '.ngl-save-slug:not(.saved)', function( event ) {
		event.preventDefault();
		var savebtn = $( this );
		var slug = encodeURIComponent( $( '#newsletterglue_post_type_ep' ).val() );
		var url  = encodeURIComponent( $( '#newsletterglue_home_url' ).val() );
		var data = 'action=newsletterglue_ajax_save_slugs&security=' + newsletterglue_params.ajaxnonce + '&slug=' + slug + '&url=' + url;

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				savebtn.html( newsletterglue_params.saving );
			},
			success: function( response ) {
				savebtn.addClass( 'saved' ).html( newsletterglue_params.saved );
				setTimeout( function() {
					savebtn.removeClass( 'saved' ).html( newsletterglue_params.save );
				}, 2000 );
				console.log( response );
			}
		} );

		return false;

	} );

	// Save settings.
	$( document ).on( 'click', '.ngl-settings-save:not(.saved)', function( event ) {

		event.preventDefault();

		$( '.ngl-settings-main input[type=text], .ngl-settings-main textarea, .ngl-settings-main input[type=checkbox], .ngl-settings-main select' ).trigger( 'change' );

		return false;

	} );

	// AJAX saving.
	$( document ).on( 'change', '.ngl-settings-main input[type=text], .ngl-settings-main textarea, .ngl-settings-main input[type=checkbox], .ngl-settings-main select, .ngl-boarding .ngl-metabox-segment select', function() {

		if ( $( 'body' ).find( '.ngl-theme' ).length ) {
			return;
		}

		var el 		= $( this ).closest( '.ngl-metabox-flex' );
		var savebtn = $( '.ngl-settings-save' );
		var id 		= $( this ).attr( 'id' );
		var value 	= $( this ).val();

		if ( $( this ).is( ':checkbox' ) ) {
			if ( $( this ).is( ':checked' ) ) {
				value = 1;
			} else {
				value = 0;
			}
		}

		var isTextarea = $( this ).is( 'textarea' );

		value = encodeURIComponent( value );

		var data = 'action=newsletterglue_ajax_save_field&security=' + newsletterglue_params.ajaxnonce + '&id=' + id + '&value=' + value;

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				el.find( '.ngl-label-verification .ngl-process' ).addClass( 'is-hidden' );
				el.find( '.ngl-label-verification .ngl-process.is-waiting' ).removeClass( 'is-hidden' );
				if ( ! isTextarea ) {
					el.find( '.ngl-label-more' ).empty();
				}
				savebtn.html( newsletterglue_params.saving );

				if ( id == 'ngl_from_email' ) {
					if ( $( '.ngl-boarding' ).length ) {
						$( '.ngl-boarding-next' ).addClass( 'disabled' );
					}
				}
			},
			success: function( response ) {

				savebtn.addClass( 'saved' ).html( newsletterglue_params.saved );

				setTimeout( function() {
					savebtn.removeClass( 'saved' ).html( newsletterglue_params.save );
				}, 2000 );

				el.find( '.ngl-label-verification .ngl-process' ).addClass( 'is-hidden' );

				if ( response.failed ) {
					el.find( '.ngl-label-verification .ngl-process.is-invalid' ).removeClass( 'is-hidden' );
					el.find( '.ngl-label-verification .ngl-process.is-invalid .ngl-process-text' ).html( response.failed );
					el.addClass( 'is-error' );
					if ( response.failed_details && ! isTextarea ) {
						el.find( '.ngl-label-more' ).html( response.failed_details );
					}
				} else if ( response.success ) {
					el.find( '.ngl-label-verification .ngl-process.is-valid' ).removeClass( 'is-hidden' );
					if ( id == 'ngl_from_email' && $( '#ngl_from_email' ).hasClass( 'no-support-verify' ) ) {
						
					} else {
						el.find( '.ngl-label-verification .ngl-process.is-valid .ngl-process-text' ).html( response.success );
					}
					el.removeClass( 'is-error' );
				} else {
					el.removeClass( 'is-error' );
					el.find( '.ngl-label-verification .ngl-process.is-valid' ).removeClass( 'is-hidden' );
					setTimeout( function() {
						el.find( '.ngl-label-verification .ngl-process' ).addClass( 'is-hidden' );
					}, 1500 );

				}

				if ( ! el.hasClass( 'is-error' ) ) {
					if ( id == 'ngl_from_email' ) {
						if ( $( '.ngl-boarding' ).length ) {
							setTimeout( function() {
								$( '.ngl-boarding-next' ).removeClass( 'disabled' ).addClass( 'ready' );
							}, 1500 );
						}
					}
					setTimeout( function() {
						el.find( '.ngl-label-verification .ngl-process' ).addClass( 'is-hidden' );
					}, 1500 );
				}

				var modal = $( '.ngl-modal[data-app=campaignmonitor]:visible' );
				if ( modal.length ) {
					var selectedLists = modal.find( '#ngl_lists' ).parents( '.ui' ).dropdown( 'get value' );
					var selectedSegments = modal.find( '#ngl_segments' ).parents( '.ui' ).dropdown( 'get value' );
					if ( ( ! selectedLists || selectedLists.length == 0 ) && ( ! selectedSegments || selectedSegments.length == 0 ) ) {
						modal.find( '#ngl_lists' ).parents( '.ngl-metabox-flex' ).addClass( 'is-error' );
						modal.find( '#ngl_segments' ).parents( '.ngl-metabox-flex' ).addClass( 'is-error' );
					} else {
						modal.find( '#ngl_lists' ).parents( '.ngl-metabox-flex' ).removeClass( 'is-error' );
						modal.find( '#ngl_segments' ).parents( '.ngl-metabox-flex' ).removeClass( 'is-error' );
					}
				}

			}
		} );

	} );

	// Toggle the customizer.
	$( document ).on( 'click', '.ngl-customize-toggle', function( event ) {
		event.preventDefault();
		
		if ( $( this ).find( 'i' ).hasClass( 'down' ) ) {
			$( this ).find( 'i' ).removeClass( 'down' ).addClass( 'up' );
			$( '.ngl-customize-preview' ).show();
		} else {
			$( this ).find( 'i' ).removeClass( 'up' ).addClass( 'down' );
			$( '.ngl-customize-preview' ).hide();
		}

		return false;
	} );

	// Use all blocks.
	$( document ).on( 'click', '.ngl-block-useall', function( event ) {
		event.preventDefault();

		$( '.ngl-block' ).each( function() {
			$( this ).removeClass( 'ngl-block-unused' ).addClass( 'ngl-block-used' );
			$( this ).find( 'input[type=checkbox]' ).prop( 'checked', true );
		} );

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : 'action=newsletterglue_ajax_use_all_blocks&security=' + newsletterglue_params.ajaxnonce
		} );

		return false;
	} );

	// Disable all blocks.
	$( document ).on( 'click', '.ngl-block-disableall', function( event ) {
		event.preventDefault();

		$( '.ngl-block' ).each( function() {
			$( this ).removeClass( 'ngl-block-used' ).addClass( 'ngl-block-unused' );
			$( this ).find( 'input[type=checkbox]' ).prop( 'checked', false );
		} );

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : 'action=newsletterglue_ajax_disable_all_blocks&security=' + newsletterglue_params.ajaxnonce
		} );

		return false;
	} );

	// Change block state.
	$( document ).on( 'change', '.ngl-block-use input[type=checkbox]', function( event ) {

		var id = $( this ).attr( 'id' );

		if ( $( this ).is( ':checked' ) ) {
			var value = 'yes';
			$( this ).parents( '.ngl-block' ).removeClass( 'ngl-block-unused' ).addClass( 'ngl-block-used' );
		} else {
			var value = 'no';
			$( this ).parents( '.ngl-block' ).removeClass( 'ngl-block-used' ).addClass( 'ngl-block-unused' );
		}

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : 'action=newsletterglue_ajax_use_block&security=' + newsletterglue_params.ajaxnonce + '&id=' + id + '&value=' + value
		} );

	} );

	// Reset textarea.
	$( document ).on( 'click', '.ngl-textarea-reset', function( event ) {
		event.preventDefault();

		var id = $( '#' + $( this ).attr( 'data-selector' ) );

		id.val( id.attr( 'data-default' ) );

		id.trigger( 'change' );

		return false;

	} );

	// Add tag.
	$( document ).on( 'click', '.ngl-textarea-append', function( event ) {
		event.preventDefault();

		var id = $( '#' + $( this ).attr( 'data-selector' ) );
		var content = $( this ).attr( 'data-value' );

		id.val( id.val() + ' ' + content );

		id.trigger( 'change' );

		return false;

	} );

	// Open block demo.
	$( document ).on( 'click', '.ngl-block-demo', function( event ) {
		event.preventDefault();
		var block_id = $( this ).parents( '.ngl-block' ).attr( 'data-block' );
		$( '.ngl-popup-overlay' ).addClass( 'ngl-active' );
		$( 'body' ).addClass( 'ngl-popup-hidden' );
		$( '.ngl-popup-overlay.ngl-active' ).find( '.ngl-popup-panel' ).empty();
		$( '.ngl-popup-demo[data-block=' + block_id + ']' ).appendTo( $( '.ngl-popup-overlay.ngl-active' ).find( '.ngl-popup-panel' ) );
		$( '.ngl-popup-overlay.ngl-active' ).addClass( 'ngl-popup-overlay-demo' );
		$( '.ngl-popup-overlay.ngl-active' ).removeClass( 'ngl-popup-overlay-settings' );
		return false;
	} );

	// Open block defaults.
	$( document ).on( 'click', '.ngl-block-defaults a', function( event ) {
		event.preventDefault();
		var block_id = $( this ).parents( '.ngl-block' ).attr( 'data-block' );
		$( '.ngl-popup-overlay' ).addClass( 'ngl-active' );
		$( 'body' ).addClass( 'ngl-popup-hidden' );
		$( '.ngl-popup-overlay.ngl-active' ).find( '.ngl-popup-panel' ).empty();
		$( '.ngl-popup-settings[data-block=' + block_id + ']' ).appendTo( $( '.ngl-popup-overlay.ngl-active' ).find( '.ngl-popup-panel' ) );
		$( '.ngl-popup-overlay.ngl-active' ).removeClass( 'ngl-popup-overlay-demo' );
		$( '.ngl-popup-overlay.ngl-active' ).addClass( 'ngl-popup-overlay-settings' );
		return false;
	} );

	// Close popup.
	$( document ).on( 'click', '.ngl-popup-panel', function( event ) {
		event.stopPropagation();
	} );

	// Close popup.
	$( document ).on( 'click', function() {
		ngl_close_popup();
	} );

	// Close popup with icon.
	$( document ).on( 'click', '.ngl-popup-close', function( event ) {
		event.preventDefault();
		ngl_close_popup();
	} );

	// Edit more settings.
	$( document ).on( 'click', '.ngl-edit-more a', function( event ) {
		event.preventDefault();
		var more = $( '.ngl-edit-more-box' );
		if ( more.hasClass( 'is-hidden' ) ) {
			more.removeClass( 'is-hidden' );
			$( this ).find( 'svg' ).replaceWith( '<svg stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>' );
		} else {
			more.addClass( 'is-hidden' );
			$( this ).find( 'svg' ).replaceWith( '<svg stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>' );
		}
		return false;
	} );

	// When block defaults are changed.
	$( document ).on( 'change', '.ngl-popup-settings input[type=checkbox]', function( event ) {

		var id  	= $( this ).parents( '.ngl-popup-settings' ).attr( 'data-block' );
		var data 	= $( this ).parents( 'form' ).serialize() + '&action=newsletterglue_ajax_save_block&security=' + newsletterglue_params.ajaxnonce + '&id=' + id;

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			success: function( response ) {

			}
		} );

	} );

	// Validate license.
	$( document ).on( 'click', '.ngl-license-wrap-flex a.ngl-license-wrap-action', function( event ) {
		event.preventDefault();
		var el 		= $( this ).parents( '.ngl-license-wrap' );
		var target  = el.find( '.ngl-license-wrap-flex a.ngl-license-wrap-action' );
		var field 	= el.find( '#newsletterglue_pro_license' );
		var key 	= field.val();

		if ( ! key ) {
			field.focus();
			return;
		}

		var data = 'action=newsletterglue_check_license&newsletterglue_pro_license=' + key + '&security=' + newsletterglue_params.ajaxnonce;

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				target.addClass( 'is-hidden' );
				el.find( 'a.ngl-verifying-state' ).removeClass( 'is-hidden' );
				field.prop( 'disabled', true );
			},
			success: function( response ) {
				field.prop( 'disabled', false );
				if ( response.status === 'invalid' ) {
					target.addClass( 'is-hidden' );
					el.find( 'a.ngl-invalid-state' ).removeClass( 'is-hidden' );
					el.find( '.ngl-license-try-again' ).removeClass( 'is-hidden' );
				} else {
					// Onboarding.
					if ( $( '.ngl-boarding-change' ).length > 0 && $( '.ngl-boarding' ).is( ':visible' ) ) {
						el.parents( '.ngl-boarding' ).find( '.ngl-boarding-change' ).trigger( 'click' );
						target.addClass( 'is-hidden' );
						el.find( 'a.ngl-base-state' ).removeClass( 'is-hidden' );
					} else {
						// Other places.
						el.find( '.ngl-license-wrap-success' ).removeClass( 'is-hidden' );
						el.find( '.ngl-license-wrap-close' ).removeClass( 'is-hidden' );
						$( '.ngl-license-review' ).remove();
					}
				}
			}
		} );

		return false;
	} );

	// Open reset links.
	$( document ).on( 'click', '.ngl-reset-newsletter-pre', function( event ) {
		event.preventDefault();
		if ( $( '.ngl-unschedule-confirm' ).hasClass( 'is-hidden' ) ) {
			$( '.ngl-unschedule-confirm' ).removeClass( 'is-hidden' );
		}
		return false;
	} );

	// Undo reset links.
	$( document ).on( 'click', '.ngl-unschedule-undo', function( event ) {
		event.preventDefault();
		if ( ! $( '.ngl-unschedule-confirm' ).hasClass( 'is-hidden' ) ) {
			$( '.ngl-unschedule-confirm' ).addClass( 'is-hidden' );
		}
		return false;
	} );

	// Try again.
	$( document ).on( 'click', '.ngl-license-try-again', function( event ) {
		event.preventDefault();
		var el 		= $( this ).parents( '.ngl-license-wrap' );
		$( this ).addClass( 'is-hidden' );
		el.find( '.ngl-license-wrap-action' ).addClass( 'is-hidden' );
		el.find( 'a.ngl-base-state' ).removeClass( 'is-hidden' );
		el.find( 'input[type=text]' ).val( '' ).focus();
		return false;
	} );

	// Close license form.
	$( document ).on( 'click', '.ngl-license-wrap-close', function( event ) {
		event.preventDefault();
		$( this ).parents( '.ngl-license-wrap' ).remove();
		$( '.ngl-license-review' ).remove();
		return false;
	} );

	// Change audience.
	$( document ).on( 'change', '.ngl-settings-mailchimp #ngl_audience, .ngl-mb-mailchimp #ngl_audience', function( event ) {

		var audience 	= $( this ).val();
		var data 		= 'action=newsletterglue_ajax_get_tags&security=' + newsletterglue_params.ajaxnonce + '&app=mailchimp&audience=' + audience;
		var el			= $( '.ngl-metabox-segment' );

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				el.addClass( 'ngl-is-loading' );
			},
			success: function( response ) {
				el.replaceWith( response );
				$( '.ngl-metabox-segment' ).find( '.ui.dropdown' ).dropdown();
			}
		} );

	} );

	// Change lists.
	$( document ).on( 'change', '.ngl-settings-sendy #ngl_brand, .ngl-mb-sendy #ngl_brand', function( event ) {

		var brand 		= $( this ).val();
		var data 		= 'action=newsletterglue_ajax_get_tags&security=' + newsletterglue_params.ajaxnonce + '&app=sendy&audience=' + brand;
		var el			= $( '.ngl-metabox-segment' );

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				el.addClass( 'ngl-is-loading' );
			},
			success: function( response ) {
				el.replaceWith( response );
				$( '.ngl-metabox-segment' ).find( '.ui.dropdown' ).dropdown();
			}
		} );

	} );

	// Change value of tabbed check.
	$( document ).on( 'click', '.ngl-tabbed-check .ui.button', function( event ) {
		event.preventDefault();
		var val = $( this ).attr( 'data-value' );
		$( this ).parents( '.ngl-tabbed-check' ).find( 'input.ngl-value-hidden' ).val( val ).trigger( 'change' );
		$( this ).parents( '.buttons' ).find( '.button' ).removeClass( 'active' );
		$( this ).addClass( 'active' );
		return false;
	} );

	// When everything has finished loading.
	$( window ).on( 'load', function() {

		if ( $( '#ngl_select_role' ).length ) {
			$( '#ngl_select_role' ).dropdown( 'set selected', 'editor' );
		}

		ngl_validate_email();

		// One second after finished loading.
		setTimeout( function() {

			if ( $( '#ngl_send_newsletter' ).length && $( '.ngl-no-connection' ).length == 0 && $( '.ngl-msgbox-wrap:visible' ).length == 0 && $( '.ngl-reset:visible' ).length == 0 ) {
				if ( newsletterglue_params.send_newsletter ) {
          let el = $( '.editor-header__settings' );
					el.prepend( '<div class="ngl-top-checkbox"><label><input type="checkbox" name="ngl_send_newsletter2" id="ngl_send_newsletter2" value="1">' + newsletterglue_params.send_newsletter + '</label></div>' );
				}
			}

			if ( ngl_colors || ngl_sizes ) {
				var colors = ngl_colors ? JSON.stringify( ngl_colors ) : '';
				var sizes  = ngl_sizes ? JSON.stringify( ngl_sizes ) : '';
				var data = 'action=newsletterglue_save_default_colors&colors=' + colors + '&sizes=' + sizes + '&security=' + newsletterglue_params.ajaxnonce;
				$.ajax( {
					type : 'post',
					url : newsletterglue_params.ajaxurl,
					data : data,
					success: function( response ) { }
				} );
			}

		}, 1000 );

	} );

	// Do not allow duplicate sending with ctrl+s.
	$(window).on( 'keydown', function(event) {
		if (event.ctrlKey || event.metaKey) {
			switch (String.fromCharCode(event.which).toLowerCase()) {
			case 's':
				event.preventDefault();
				$( '#ngl_double_confirm' ).val( 'no' );
				break;
			}
		}
	});

	// Close the pop-over.
	function ngl_close_popover() {
		var pop = $( '.ngl-gutenberg-pop' );
		pop.removeClass( 'is-open' );
		pop.css( {
			left: 0,
			top: 0
		} );
		pop.find( '.ngl-fallback' ).hide();
		pop.find( 'button.ngl-submenu-item' ).removeClass( 'is-active' );
	}

	// Fix scroll.
	setTimeout( function() {
		$( '.interface-interface-skeleton__content' ).on( 'scroll', function() {
			var pop = $( '.ngl-gutenberg-pop' );
			if ( pop.hasClass( 'is-open' ) ) {
				if ( $( '.block-editor-block-toolbar' ).hasClass( 'is-showing-movers' ) ) {
					var topgap = $( '.ngl-toolbar-mergetags' ).offset().top + $( '.ngl-toolbar-mergetags' ).height() + 24;
					var leftgap = $( '.block-editor-block-toolbar' ).offset().left;
				} else {
					var topgap = $( '.block-editor-block-toolbar' ).offset().top + $( '.ngl-toolbar-mergetags' ).height() + 24;
					var leftgap = $( '.block-editor-block-toolbar' ).offset().left;
				}
				pop.css( {
					top: topgap + 'px',
				} );
			}
		} );
	}, 1000 );

	// Open merge tags list.
	$( document ).on( 'click', '.ngl-toolbar-mergetags', function() {
		var pop = $( '.ngl-gutenberg-pop' );
		if ( $( this ).parents( '.block-editor-block-toolbar' ).hasClass( 'is-showing-movers' ) ) {
			var topgap = $( this ).offset().top + $( this ).height() + 24;
			var leftgap = $( this ).offset().left;
		} else {
			var topgap = $( this ).parents( '.block-editor-block-toolbar' ).offset().top + $( this ).height() + 24;
			var leftgap = $( this ).offset().left;
		}
		if ( ! pop.hasClass( 'is-open' ) ) {
			pop.addClass( 'is-open' );
			pop.css( {
				left: leftgap + 'px',
				top: topgap + 'px',
			} );
		} else {
			ngl_close_popover();
		}
	} );

	// When clicked in body.
	$( 'body' ).on( 'click', function(event) {
		var pop = $( '.ngl-gutenberg-pop' );
		if ( $( event.target ).parents( '.ngl-toolbar-mergetags' ).length || $( event.target ).hasClass( 'ngl-toolbar-mergetags' ) ) {
			return true;
		}
		if( ! $( event.target ).is( '.ngl-gutenberg-pop' ) && ! $( event.target ).parents( '.ngl-gutenberg-pop' ).length ){
			ngl_close_popover();
		}
	} );

	// Open sub menu.
	$( document ).on( 'click', '.ngl-submenu-trigger', function() {
		if ( ! $( this ).hasClass( 'is-triggered' ) ) {
			$( this ).parents( 'div[role=group]' ).find( '.ngl-submenu-item' ).css( { display: 'inline-flex' } );
			$( this ).parents( 'div[role=group]' ).find( '.ngl-outside-helper' ).css( { display: 'block' } );
			$( this ).addClass( 'is-triggered' );
			$( this ).find( 'svg' ).html( '<path d="M6.5 12.4L12 8l5.5 4.4-.9 1.2L12 10l-4.5 3.6-1-1.2z"></path>' );
		} else {
			$( this ).parents( 'div[role=group]' ).find( '.ngl-submenu-item' ).css( { display: 'none' } );
			$( this ).parents( 'div[role=group]' ).find( '.ngl-outside-helper' ).css( { display: 'none' } );
			$( this ).removeClass( 'is-triggered' );
			$( this ).find( 'svg' ).html( '<path d="M17.5 11.6L12 16l-5.5-4.4.9-1.2L12 14l4.5-3.6 1 1.2z"></path>' );
			$( '.ngl-fallback' ).hide();
			$( '.ngl-submenu-item' ).removeClass( 'is-active' );
		}
	} );

	// Add the tag.
	$( document ).on( 'click', '.ngl-submenu-item', function() {

		var tag 	= $( this ).attr( 'data-ngl-tag' );
		var tag_id 	= $( this ).attr( 'data-tag-id' );
		var fallback = $( 'input[type=text]#__fallback_' + tag_id );
		var $block = wp.data.select( 'core/block-editor' ).getSelectedBlock();
		var btn = $( this );
		var uniqid = Math.round( new Date().getTime() + ( Math.random() * 100 ) );

		if ( btn.attr( 'data-require-fb' ) & btn.attr( 'data-require-fb' ) == 1 && fallback.val() == '' ) {
			btn.find( '.ngl-gutenberg-icon' ).trigger( 'click' );
			fallback.addClass( 'is-mandatory' );
			return false;
		}

		ngl_close_popover();

		// Make tag markup.
		if ( tag_id == 'unsubscribe_link' || tag_id == 'webversion' || tag_id == 'blog_post' || tag_id == 'update_preferences' ) {
			var link_text = fallback.val() ? fallback.val() : $( this ).attr( 'data-default-link-text' );
			tag = '<a href="' + tag + '">' + link_text + '</a><i class="ngl-tag-spacer" id="ngl-tag-spacer-' + uniqid + '">&nbsp;</i>';
		} else {
			if ( fallback.length ) {
				tag = tag.replace( ' }}', ',fallback=' + fallback.val() + ' }}' );
			}
			tag = '<span class="ngl-tag">' + tag + '</span><i class="ngl-tag-spacer" id="ngl-tag-spacer-' + uniqid + '">&nbsp;</i>';
		}

		// Get selection.
		var startIndex = wp.data.select('core/block-editor').getSelectionStart().offset;
		var endIndex   = wp.data.select('core/block-editor').getSelectionEnd().offset;

		// Insert tag at specified caret.
		var html  = $block.attributes.content;
		var value = wp.richText.create( { html } );

		value = wp.richText.insert( value, tag, startIndex, endIndex );

		$block.attributes.content = wp.richText.toHTMLString( { value } ).replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&amp;/g,'&');

		wp.data.dispatch( 'core/block-editor' ).updateBlock( $block.clientId, $block.attributes );

		// Make a dummy element to keep editing.
		var el = document.getElementById( 'ngl-tag-spacer-' + uniqid );
		var range = document.createRange();
		var sel = window.getSelection();
		range.setStartAfter( el, 0 );
		range.collapse( true );
		sel.removeAllRanges();
		sel.addRange( range );
		el.focus();
	} );

	// Open sub menu.
	$( document ).on( 'click', '.ngl-gutenberg-icon', function( event ) {
		event.stopPropagation();
		var id = $( this ).parents( 'button' ).attr( 'data-tag-id' );
		$( '.ngl-fallback:not([data-tag=' + id + '])' ).hide();
		$( 'button.ngl-submenu-item' ).removeClass( 'is-active' );
		if ( $( '.ngl-fallback[data-tag=' + id + ']' ).is( ':hidden' ) ) {
			$( '.ngl-fallback[data-tag=' + id + ']' ).show();
			$( '.ngl-fallback[data-tag=' + id + ']' ).find( 'input' ).focus();
			$( this ).parents( 'button' ).addClass( 'is-active' );
		} else {
			$( '.ngl-fallback[data-tag=' + id + ']' ).hide();
			$( this ).parents( 'button' ).removeClass( 'is-active' );
		}
	} );

	// When clicked enter after fallback input.
	$( document ).on( 'keyup', '.ngl-fallback-input input', function ( e ) {
		if ( e.key === 'Enter' || e.keyCode === 13 ) {
			var id = $( this ).attr( 'data-tag-input-id' );
			var btn = $( '.ngl-submenu-item[data-tag-id=' + id + ']' );
			if ( $( this ).hasClass( 'is-mandatory' ) && $( this ).val() == '' ) {
				return false;
			}
			btn.trigger( 'click' );
		}
	} );

	// When fallback input is changed.
	$( document ).on( 'change', '.ngl-fallback-input input', function( event ) {
		if ( $( this ).val() != '' ) {
			$( this ).removeClass( 'is-mandatory' );
		} else {
			$( this ).addClass( 'is-mandatory' );
		}
	} );

	// Save fallbacks.
	$( document ).on( 'change', '.ngl-fallback-input input[type=text]', function() {
		var id 		= $( this ).attr( 'data-tag-input-id' );
		var val 	= $( this ).val();
		var data 	= 'action=newsletterglue_update_merge_tag&security=' + newsletterglue_params.ajaxnonce + '&id=' + id + '&value=' + encodeURIComponent( val );

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			success: function( ) { }
		} );
	} );

	// Default patterns row actions.
	$( document ).on( 'click', '.type-ngl_pattern span.edit a, .post-type-ngl_pattern a.row-title', function( event ) {
		if ( $( this ).parents( 'td' ).find( '.ngl-pattern-state' ).length ) {
			var row = $( this ).parents( 'td' ).find( '.row-actions' );
			if ( row.find( '.ngl-pattern-row' ).length == 0 ) {
				event.preventDefault();
				row.find( 'span.ngl_duplicate' ).addClass( 'ngl-bold' );
				var html = newsletterglue_params.pattern_edit;
				row.append( html );
				return false;
			}
		}
	} );

	// Default templates row actions.
	$( document ).on( 'click', '.type-ngl_template span.edit a, .post-type-ngl_template a.row-title', function( event ) {
		if ( $( this ).parents( 'td' ).find( '.ngl-pattern-state' ).length ) {
			var row = $( this ).parents( 'td' ).find( '.row-actions' );
			if ( row.find( '.ngl-pattern-row' ).length == 0 ) {
				event.preventDefault();
				row.find( 'span.ngl_duplicate' ).addClass( 'ngl-bold' );
				var html = newsletterglue_params.template_edit;
				row.append( html );
				return false;
			}
		}
	} );

	// Go back - default patterns text.
	$( document ).on( 'click', '.ngl-pattern-bk', function( event ) {
		event.preventDefault();
		var row = $( this ).parents( 'td' ).find( '.row-actions' );
		row.find( 'span.ngl_duplicate' ).removeClass( 'ngl-bold' );
		$( this ).parents( '.ngl-pattern-row' ).remove();
		return false;
	} );

	// Show patterns reset UI on load.
	if ( $( '.ngl-pattern-reset-ui' ).length ) {
		$( '.ngl-pattern-extra-ui' ).insertAfter( '.wp-header-end' );
	}

	// Toggle the reset pattern.
	$( document ).on( 'click', '.ngl-pattern-reset-toggle', function( event ) {
		event.preventDefault();
		var el = $( this ).parents( '.ngl-pattern-reset' ).find( '.ngl-pattern-reset-ui' );
		if ( el.is( ':visible' ) ) {
			el.hide();
		} else {
			el.show();
		}
		return false;
	} );

	// When reset pattern select is changed.
	$( document ).on( 'change', '.ngl-pattern-reset-ui select', function( event ) {
		event.preventDefault();
		var selected = $( this ).find( ':selected' ).attr( 'data-url' );
		$( this ).parents( '.ngl-pattern-reset-ui' ).find( '.ngl-pattern-reset-start' ).attr( 'href', selected );
		return false;
	} );

	// When permissions role is changed.
	$( document ).on( 'change', '#ngl_select_role', function( event ) {
		event.preventDefault();
		var wrap = $( this ).parents( '.ngl-metabox-flex' );
		var role = $( this ).val();
		if ( role === 'administrator' ) {
			$( '.ngl-show-if-administrator' ).show();
			$( '.ngl-save-perms' ).addClass( 'disabled' );
			wrap.find( 'input[type=checkbox]' ).attr( 'disabled', true );
		} else {
			$( '.ngl-show-if-administrator' ).hide();
			$( '.ngl-save-perms' ).removeClass( 'disabled' );
			wrap.find( 'input[type=checkbox]' ).removeAttr( 'disabled', true );
		}
		$( '.ngl-perms-header, .ngl-perms-group' ).hide();
		if ( role ) {
			$( '.ngl-metabox-header-' + role ).show();
			$( '.ngl-input-group-' + role ).show();
			$( '.ngl-save-perms' ).parent().show();
		}
		$( '.ngl-reset-this' ).attr( 'data-role', role );
		return false;
	} );

	// Force the edit checkbox to be checked.
	$( '.ngl-add-newsletter-checkbox' ).on( 'change', function() {
		if ( $( this ).is( ':checked' ) ) {
			$( this ).parents( '.ngl-input-group' ).find( '.ngl-edit-newsletter-checkbox' ).prop( 'checked', true );
		}
	} );

	// Force the edit checkbox to be checked.
	$( '.ngl-edit-newsletter-checkbox' ).on( 'change', function() {
		if ( $( this ).is( ':checked' ) ) {

		} else {
			$( this ).parents( '.ngl-input-group' ).find( '.ngl-add-newsletter-checkbox' ).prop( 'checked', false );
		}
	} );

	// Triggers a confirmation inline pop.
	$( document ).on( 'click', '.ngl-flex-action', function( event ) {
		event.preventDefault();

		var res = $( '.ngl-setting-reset' );

		$( this ).parents( '.ngl-flex-actions' ).find( '.ngl-flex-action' ).not( $( this ) ).addClass( 'ngl-disable-action' );

		if ( res.hasClass( 'is-hidden' ) ) {
			res.removeClass( 'is-hidden' );
			res.find( '.ngl-theme-reset-confirm, .ngl-theme-reset-btns' ).removeClass( 'is-hidden' );
			res.find( '.ngl-process' ).addClass( 'is-hidden' );
			if ( $( this ).attr( 'data-no-role' ) ) {
				res.find( '.ngl-reset-this' ).attr( 'data-role', "" );
			} else {
				res.find( '.ngl-reset-this' ).attr( 'data-role', $( '#ngl_select_role' ).val() );
			}
		} else {
			res.addClass( 'is-hidden' );
			res.find( '.ngl-theme-reset-confirm, .ngl-theme-reset-btns' ).addClass( 'is-hidden' );
			res.find( '.ngl-process' ).addClass( 'is-hidden' );
			$( this ).parents( '.ngl-flex-actions' ).find( '.ngl-flex-action' ).removeClass( 'ngl-disable-action' );
		}

		return false;
	} );

	// Undo reset.
	$( document ).on( 'click', '.ngl-reset-undo', function( event ) {
		event.preventDefault();

		var res = $( '.ngl-setting-reset' );
		res.addClass( 'is-hidden' );
		res.find( '.ngl-theme-reset-confirm, .ngl-theme-reset-btns' ).addClass( 'is-hidden' );
		res.find( '.ngl-process' ).addClass( 'is-hidden' );
		$( this ).parents( '.ngl-flex-actions' ).find( '.ngl-flex-action' ).removeClass( 'ngl-disable-action' );

		return false;
	} );

	// Reset role.
	$( document ).on( 'click', '.ngl-reset-role', function( event ) {
		event.preventDefault();

		var res		= $( this ).parents( '.ngl-setting-reset' );
		var role 	= $( this ).attr( 'data-role' );
		var data 	= 'action=newsletterglue_reset_user_roles&security=' + newsletterglue_params.ajaxnonce + '&role=' + encodeURIComponent( role );

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				res.find( '.ngl-process.is-waiting' ).removeClass( 'is-hidden' );
			},
			success: function( response ) {
				res.find( '.ngl-process.is-waiting' ).addClass( 'is-hidden' );
				res.find( '.ngl-process.is-valid' ).removeClass( 'is-hidden' );
				setTimeout( function() {
					res.addClass( 'is-hidden' );
					res.find( '.ngl-theme-reset-confirm, .ngl-theme-reset-btns' ).addClass( 'is-hidden' );
					res.find( '.ngl-process' ).addClass( 'is-hidden' );
					res.parents( '.ngl-flex-actions' ).find( '.ngl-flex-action' ).removeClass( 'ngl-disable-action' );
				}, 1500 );
				if ( response ) {
					$( '.ngl-perms-group' ).find( 'input[type=checkbox]' ).each( function() {
						var this_role = $( this ).attr( 'name' ).replace( 'ngl_perms_', '' );
						var permission = $( this ).attr( 'id' );
						if ( response[ this_role ] && response[ this_role ][ permission ] ) {
							$( this ).prop( 'checked', true );
						} else {
							$( this ).prop( 'checked', false );
						}
					} );
				}
			}
		} );

		return false;
	} );

	// Set template as default.
	$( document ).on( 'click', 'a.ngl-tpl-make-default', function( event ) {
		event.preventDefault();

		var post_id = $( this ).parent().attr( 'data-post-id' );
		var el   = $( this );
		var div  = el.parent();
		var data = 'action=newsletterglue_set_default_template&security=' + newsletterglue_params.ajaxnonce + '&post_id=' + post_id;

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				$( '.ngl-tpl-default' ).replaceWith( newsletterglue_params.make_default_tpl );
				$( '.ngl-tpl-noshortcut' ).each( function() {
					var d = $( this ).parent().attr( 'data-is-shortcut' );
					if ( d == 1 ) {
						$( this ).replaceWith( newsletterglue_params.shortcut_added );
					} else {
						$( this ).replaceWith( newsletterglue_params.add_shortcut );
					}
				} );
				el.replaceWith( newsletterglue_params.default_tpl );
				div.find( '.ngl-tpl-shortcut, .ngl-tpl-undo-shortcut' ).replaceWith( newsletterglue_params.no_shortcut );
			},
			success: function( response ) {
				$( 'a.page-title-action, .toplevel_page_newsletter-glue .wp-submenu li:nth-child(3) a' ).attr( 'href', response.url );
			}
		} );
	} );

	// Set template as non-default.
	$( document ).on( 'click', 'a.ngl-tpl-default', function( event ) {
		event.preventDefault();

		var post_id = $( this ).parent().attr( 'data-post-id' );
		var el   = $( this );
		var div  = el.parent();
		var data = 'action=newsletterglue_unset_default_template&security=' + newsletterglue_params.ajaxnonce + '&post_id=' + post_id;

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				el.replaceWith( newsletterglue_params.make_default_tpl );
				div.find( '.ngl-tpl-noshortcut' ).replaceWith( newsletterglue_params.add_shortcut );
			},
			success: function( response ) {
				$( 'a.page-title-action, .toplevel_page_newsletter-glue .wp-submenu li:nth-child(3) a' ).attr( 'href', response.url );
			}
		} );
	} );

	// Set template as shortcut.
	$( document ).on( 'click', 'a.ngl-tpl-shortcut', function( event ) {
		event.preventDefault();

		var post_id = $( this ).parent().attr( 'data-post-id' );
		var el   = $( this );
		var div  = el.parent();
		var data = 'action=newsletterglue_set_template_shortcut&security=' + newsletterglue_params.ajaxnonce + '&post_id=' + post_id;

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				el.replaceWith( newsletterglue_params.shortcut_added );
			},
			success: function( response ) {

			}
		} );
	} );

	// Undo template as shortcut.
	$( document ).on( 'click', 'a.ngl-tpl-undo-shortcut', function( event ) {
		event.preventDefault();

		var post_id = $( this ).parent().attr( 'data-post-id' );
		var el   = $( this );
		var div  = el.parent();
		var data = 'action=newsletterglue_unset_template_shortcut&security=' + newsletterglue_params.ajaxnonce + '&post_id=' + post_id;

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				el.replaceWith( newsletterglue_params.add_shortcut );
			},
			success: function( response ) {

			}
		} );
	} );

	// Templates demo video.
	$(document).on( 'click', '.ngd-launch-video', function(e) {
		e.preventDefault();
		var src = $( this ).attr( 'data-src' );
		$( '.ngd-tut-overlay' ).addClass( 'opened' );
		$( '.ngd-tut-overlay div' ).html( '<iframe src="' + src + '" allow="autoplay" frameborder="0" allowfullscreen></iframe>' );
		return false;
	} );

	$(document).on( 'click',function(e) {
		if ( event && $(event.target).attr('class') == 'ngd-tut-overlay opened' ) {
			$( '.ngd-tut-overlay div' ).empty();
			$( '.ngd-tut-overlay' ).removeClass( 'opened' );
		}
	} );

	// Insert tag.
	$( document ).on( 'click', '.ngl-input-tags u', function() {
		var tag = $( this ).text();
		if ( tag ) {
			var id = '#ngl_subject';
			if ( $( this ).parents( '.ngl-input-tags' ).attr( 'data-field' ) ) {
				id = '#' + $( this ).parents( '.ngl-input-tags' ).attr( 'data-field' );
			}
			$( id ).val( $( id ).val() + tag ).focus().trigger( 'change' );
		}
	} );

	// Merge tags modal functionality.
	$( document ).on( 'click', '#ngl-open-merge-tags', function(e) {
		e.preventDefault();
		$( '#ngl-merge-tags-modal' ).show();
	} );

	$( document ).on( 'click', '#ngl-close-merge-tags, #ngl-close-merge-tags-btn', function() {
		$( '#ngl-merge-tags-modal' ).hide();
	} );

	// Close modal when clicking outside.
	$( document ).on( 'click', '#ngl-merge-tags-modal', function(e) {
		if ( e.target === this ) {
			$( this ).hide();
		}
	} );

	// Handle clicks on the whole tag card (ensure it works alongside existing underlined tag handler).
	$( document ).on( 'click', '.ngl-merge-tag-card', function(e) {
		// Only proceed if the click is not on the underlined tag (to avoid double triggering)
		if (!$(e.target).is('u') && !$(e.target).closest('u').length) {
			e.preventDefault();
			var tag = $( this ).attr( 'data-tag' );
			if ( tag ) {
				var subjectField = $( '#ngl_subject' );
				if ( subjectField.val().length > 0 && !subjectField.val().endsWith( ' ' ) ) {
					subjectField.val( subjectField.val() + ' ' );
				}
				subjectField.val( subjectField.val() + tag ).focus().trigger( 'change' );
				$( '#ngl-merge-tags-modal' ).hide();
			}
		}
	} );

	$( document ).on( 'change', 'input[name=ngl_send_type]', function() {
		$( '.ngl-radio-group .field' ).removeClass( 'choice-checked' ).addClass( 'choice-unchecked' );
		$( '.ngl-radio-group .field[data-id=' + $( this ).attr( 'id' ) + ']' ).addClass( 'choice-checked' ).removeClass( 'choice-unchecked' );
	} );

  // Sendgrid fixes.
  if ( $( '.ngl-mb-sendgrid' ).length > 0 ) {
    var el = $( '.ngl-mb-sendgrid' );
    var lists = el.find( '.sg-list' ).find( '.ui.dropdown' ).dropdown( 'get value' );
    if ( lists && lists.length ) {
      el.find( '.sg-segment .ui.dropdown' ).addClass("disabled");
      el.find( '.sg-segment .ui.dropdown' ).dropdown("clear");
      if ( el.find( '.sg-segment .field .ng-sg-warn' ).length == 0 ) {
        el.find( '.sg-segment .field' ).append( '<div class="ng-sg-warn">To select segment, delete list first.</div>' );
      }
    } else {
      el.find( '.sg-segment .ui.dropdown' ).removeClass("disabled");
      el.find( '.sg-segment .field .ng-sg-warn' ).remove();
    }
  }

  $( document ).on( 'change', '.ngl-mb-sendgrid #ngl_lists', function() {
    var el = $( '.ngl-mb-sendgrid' );
    var lists = el.find( '.sg-list' ).find( '.ui.dropdown' ).dropdown( 'get value' );
    if ( lists && lists.length ) {
      el.find( '.sg-segment .ui.dropdown' ).addClass("disabled");
      el.find( '.sg-segment .ui.dropdown' ).dropdown("clear");
      if ( el.find( '.sg-segment .field .ng-sg-warn' ).length == 0 ) {
        el.find( '.sg-segment .field' ).append( '<div class="ng-sg-warn">To select segment, delete list first.</div>' );
      }
    } else {
      el.find( '.sg-segment .ui.dropdown' ).removeClass("disabled");
      el.find( '.sg-segment .field .ng-sg-warn' ).remove();
    }
  } );

} ) ( jQuery );