jQuery( document ).ready( function( $ ) {
	'use strict';

	$( '.hb-no-label' ).parent().css( 'margin-top', '-10px' );
	$( '.hb-saved' ).html( hb_text.form_saved );

	$( '.hb-options-save' ).on( 'click', function() {
		$( this ).blur();

		if ( $( 'input[name="action"]' ).val() == 'hb_update_payment_settings' ) {
			var deposit = $( '#hb_deposit_amount' ).val().trim();
			if (
				$( 'input[name="hb_deposit_type"]:checked' ).val() != 'none' &&
				deposit &&
				! $.isNumeric( deposit )
			) {
				alert( hb_text.deposit_not_valid );
				$( '#hb_deposit_amount' ).focus();
				return false;
			}
			var security_bond = $( '#hb_security_bond_amount' ).val().trim();
			if ( $( 'input[name="hb_security_bond"]:checked' ).val() == 'yes' && security_bond && ! $.isNumeric( security_bond ) ) {
				alert( hb_text.security_bond_not_valid );
				$( '#hb_security_bond_amount' ).focus();
				return false;
			}
		}

		if ( $( 'input[name="action"]' ).val() == 'hb_update_misc_settings' ) {
			var lang_settings = {};
			$( '.hb-lang-settings' ).each( function () {
				lang_settings[ $( this ).data( 'locale' ) ] = {
					'first_day': $( this ).find( '.hb-first-day' ).val(),
					'date_format': $( this ).find( '.hb-date-format' ).val()
				}
			});
			$( '#hb_front_end_date_settings' ).val( JSON.stringify( lang_settings ) );

			var min_date_fixed = $( '#hb_min_date_fixed' ).val();
			if ( min_date_fixed && ! min_date_fixed.match( /^\d{4}-\d{2}-\d{2}$/ ) ) {
				alert( hb_text.date_not_valid );
				$( '#hb_min_date_fixed' ).focus();
				return false;
			}
			var max_date_fixed = $( '#hb_max_date_fixed' ).val();
			if ( max_date_fixed && ! max_date_fixed.match( /^\d{4}-\d{2}-\d{2}$/ ) ) {
				alert( hb_text.date_not_valid );
				$( '#hb_max_date_fixed' ).focus();
				return false;
			}
		}

		if ( $( 'input[name="action"]' ).val() == 'hb_update_appearance_settings' ) {
			var calendar_colors = {},
				buttons_options = {},
				inputs_selects_options = {};

			$( '.hb-calendar-color' ).each( function () {
				calendar_colors[ $( this ).attr( 'id' ) ] = $( this ).val();
			});
			$( '.hb-buttons-css-option' ).each( function () {
				if ( $( this ).attr( 'type' ) == 'radio' && $( this ).is( ':checked' ) ) {
					buttons_options[ $( this ).attr( 'name' ) ] = $( this ).val();
				} else {
					buttons_options[ $( this ).attr( 'id' ) ] = $( this ).val();
				}
			});
			$( '.hb-inputs_selects-css-option' ).each( function () {
				if ( $( this ).attr( 'type' ) == 'radio' && $( this ).is( ':checked' ) ) {
					inputs_selects_options[ $( this ).attr( 'id' ) ] = $( this ).val();
				} else {
					inputs_selects_options[ $( this ).attr( 'id' ) ] = $( this ).val();
				}
			});

			$( '#hb_calendar_colors' ).val( JSON.stringify( calendar_colors ) );
			$( '#hb_buttons_css_options' ).val( JSON.stringify( buttons_options ) );
			$( '#hb_inputs_selects_css_options' ).val( JSON.stringify( inputs_selects_options ) );
		}

		if ( $( 'input[name="action"]' ).val() == 'hb_update_ical_settings' ) {
			var frequency = $( '#hb_ical_frequency' ).val().trim();
			if ( frequency < 300 ) {
				alert( hb_text.ical_frequency_not_valid );
				$( '#hb_ical_frequency' ).focus();
				return false;
			}
		}

		var $save_section = $( this ).parent().parent();
		$save_section.find( '.hb-ajaxing' ).css( 'display', 'inline' );
		$( '#hb-nonce' ).val( $( '#hb_nonce_update_db' ).val() );
		$.ajax({
			type: 'POST',
			timeout: hb_ajax_settings.timeout,
			url: ajaxurl,
			data : $( '#hb-settings-form' ).serialize(),
			success: function( ajax_return ) {
				form_saved = true;
				$save_section.find( '.hb-ajaxing' ).css( 'display', 'none' );
				if ( ajax_return.trim() != 'settings saved' ) {
					alert( ajax_return );
				} else {
					$save_section.find( '.hb-saved' ).show();
					setTimeout( function() {
						$save_section.find( '.hb-saved ' ).fadeOut();
					}, 4000 );
				}
			},
			error: function( jqXHR, textStatus, errorThrown ) {
				$save_section.find( '.hb-ajaxing' ).css( 'display', 'none' );
				alert( textStatus + ' (' + errorThrown + ')' )
			}
		});

		return false;
	});

	var form_saved = true;

	$( '#hb-settings-form input:not([type="file"]):not([name="hb-import-settings-modify-id"]), #hb-settings-form select, #hb-settings-form textarea' ).change( function() {
		form_saved = false;
	});

	window.onbeforeunload = function() {
		if ( ! form_saved ) {
			return hb_text.unsaved_warning;
		}
	}
});