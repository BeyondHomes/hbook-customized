jQuery( document ).ready( function( $ ) {
	'use strict';

	stripe_api_key();

	function stripe_api_key() {
		if ( $( 'input[name="hb_stripe_mode"]:checked' ).val() == 'live' ) {
			$( '.hb-stripe-mode-live' ).slideDown();
			$( '.hb-stripe-mode-test' ).slideUp();
		} else {
			$( '.hb-stripe-mode-live' ).slideUp();
			$( '.hb-stripe-mode-test' ).slideDown();
		}
	}

	$( 'input[name="hb_stripe_mode"]' ).change( function() {
		stripe_api_key();
	});

});