jQuery( document ).ready( function( $ ) {
	'use strict';

	paypal_api_key();

	function paypal_api_key() {
		if ( $( 'input[name="hb_paypal_mode"]:checked' ).val() == 'live' ) {
			$( '.hb-paypal-mode-live' ).slideDown();
			$( '.hb-paypal-mode-sandbox' ).slideUp();
		} else {
			$( '.hb-paypal-mode-live' ).slideUp();
			$( '.hb-paypal-mode-sandbox' ).slideDown();
		}
	}

	$( 'input[name="hb_paypal_mode"]' ).change( function() {
		paypal_api_key();
	});

});