jQuery( document ).ready( function( $ ) {
	'use strict';

	$( '.hb-color-option' ).wpColorPicker();

	$( 'input[name="hb_inputs_selects_style"]' ).on( 'change', function() {
		inputs_selects_style_options();
	});

	inputs_selects_style_options();

	function inputs_selects_style_options() {
		if ( $( 'input[name="hb_inputs_selects_style"]:checked' ).val() != 'custom' ) {
			$( '.hb-inputs-selects-style-options-wrapper' ).slideUp();
		} else {
			$( '.hb-inputs-selects-style-options-wrapper' ).slideDown();
		}
	}

	$( 'input[name="hb_buttons_style"]' ).on( 'change', function() {
		buttons_style_options();
	});

	buttons_style_options();

	function buttons_style_options() {
		if ( $( 'input[name="hb_buttons_style"]:checked' ).val() != 'custom' ) {
			$( '.hb-buttons-style-options-wrapper' ).slideUp();
		} else {
			$( '.hb-buttons-style-options-wrapper' ).slideDown();
		}
	}
});