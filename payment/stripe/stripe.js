'use strict';

var stripe = Stripe( hb_stripe_key, { 'locale': hb_stripe_locale } );
var elements = stripe.elements();
var card_element = elements.create( 'card' );
card_element.mount( '.hb-stripe-card-element' );
jQuery( '.hb-payment-form-stripe' ).on( 'click', function() {
	var $form = jQuery( this ).parents( '.hb-booking-details-form' );
	var $card_element = $form.find( '.hb-stripe-card-element' );
	if ( ! $card_element.hasClass( 'StripeElement' ) ) {
		card_element.unmount();
		card_element.mount( $card_element[0] );
		setTimeout( function() { card_element.focus() }, 2000 );
	} else {
		card_element.focus();
	}
});
card_element.on( 'focus', function() {
	jQuery( '.hb-stripe-card-element-bg' ).prop( 'disabled', false );
});
card_element.on( 'blur', function() {
	jQuery( '.hb-stripe-card-element-bg' ).prop( 'disabled', true );
});

function hb_stripe_payment_process( $form, callback_func ) {
	$form.find( '.hb-stripe-error' ).hide();
	try {
		$form.find( 'input[type="submit"]' ).blur().prop( 'disabled', true );
		$form.find( '.hb-saving-resa' ).slideDown();
		stripe.createPaymentMethod( 'card', card_element ).then( function( result ) {
			if ( result.error ) {
				$form.removeClass( 'submitted' );
				$form.find( 'input[type="submit"]' ).prop( 'disabled', false );
				$form.find( '.hb-saving-resa' ).hide();
				$form.find( '.hb-stripe-error' ).html( result.error.message ).slideDown();
			} else {
				$form.append( jQuery( '<input type="hidden" name="hb-stripe-payment-method-id" value="' + result.paymentMethod.id + '"/>' ) );
				callback_func( $form );
			}
		});
		return true;
	} catch ( e ) {
		alert( e.message );
		return false;
	}
}

function hb_stripe_payment_requires_action( $form, response ) {
	if ( response['stripe_action'] == 'payment_intent' ) {
		stripe.handleCardAction( response['client_secret'] ).then( function( result ) {
			stripe_handle_card_action_result( $form, result, 'payment_intent' );
		});
	} else {
		stripe.handleCardSetup( response['client_secret'] ).then( function( result ) {
			stripe_handle_card_action_result( $form, result, 'setup_intent' );
		});
	}
}

function stripe_handle_card_action_result( $form, result, action ) {
	$form.removeClass( 'submitted' );
	if ( result.error ) {
		$form.find( 'input[type="submit"]' ).prop( 'disabled', false );
		$form.find( '.hb-saving-resa' ).slideUp();
		$form.find( '.hb-confirm-error' ).html( result.error.message ).slideDown();
	} else {
		if ( action == 'payment_intent' ) {
			$form.append( jQuery( '<input type="hidden" name="hb-stripe-payment-intent-id" value="' + result.paymentIntent.id + '"/>' ) );
		} else {
			$form.append( jQuery( '<input type="hidden" name="hb-stripe-setup-intent-id" value="' + result.setupIntent.id + '"/>' ) );
		}
		$form.submit();
	}
}