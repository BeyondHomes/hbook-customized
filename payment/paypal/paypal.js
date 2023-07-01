'use strict';

function hb_paypal_payment_process( $form, callback_func ) {
	$form.addClass( 'submitted' );
	$form.find( 'input[type="submit"]' ).blur().prop( 'disabled', true );
	$form.find( '.hb-saving-resa' ).slideDown();
	$form.find( '.hb-confirm-error' ).slideUp();
	$form.append( jQuery( '<input type="hidden" name="hb-current-url" value="' + document.URL + '" />' ) );
	$form.append( jQuery( '<input type="hidden" name="check-in-formatted" value="' + $form.parents( '.hbook-wrapper' ).find( '.hb-check-in-date' ).val() + '" />' ) );
	$form.append( jQuery( '<input type="hidden" name="check-out-formatted" value="' + $form.parents( '.hbook-wrapper' ).find( '.hb-check-out-date' ).val() + '" />' ) );
	callback_func( $form );
	return true;
}

function hb_paypal_payment_redirection( $form, response ) {
	var current_url = document.URL,
		back_url = '',
		pattern;

	pattern = /&payment_gateway(\=[^&]*)?(?=&|$)|payment_gateway(\=[^&]*)?(&|$)/;
	current_url = current_url.replace( pattern, '' );
	pattern = /&payment_confirm(\=[^&]*)?(?=&|$)|payment_confirm(\=[^&]*)?(&|$)/;
	current_url = current_url.replace( pattern, '' );
	pattern = /&payment_cancel(\=[^&]*)?(?=&|$)|payment_cancel(\=[^&]*)?(&|$)/;
	current_url = current_url.replace( pattern, '' );
	pattern = /&token(\=[^&]*)?(?=&|$)|token(\=[^&]*)?(&|$)/;
	current_url = current_url.replace( pattern, '' );
	pattern = /&PayerID(\=[^&]*)?(?=&|$)|PayerID(\=[^&]*)?(&|$)/;
	current_url = current_url.replace( pattern, '' );

	if ( current_url.indexOf( '#' ) > 0 ) {
		current_url = current_url.substr( 0, current_url.indexOf( '#' ) );
	}

	if ( current_url.slice(-1) != '?' ) {
		if ( current_url.indexOf( '?' ) > 0 ) {
			current_url += '&';
		} else {
			current_url += '?';
		}
	}
	back_url = current_url + 'payment_gateway=paypal&payment_cancel=1&token=' + response['payment_token'];
	try {
		history.pushState( {}, '', back_url );
	} catch ( e ) {}
	window.location = hb_paypal_url + '&useraction=commit&token=' + response['payment_token'];
}