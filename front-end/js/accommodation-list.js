jQuery( document ).ready( function( $ ) {
	'use strict';

	$( '.hb-accom-listing-booking-form' ).hide();
	$( '.hb-listing-book-accom' ).on( 'click', function() {
		if ( $( this ).parents( '.hb-accom-listing-column' ).find( '.hb-accom-listing-booking-form' ).is( ':hidden' ) ) {
			$( this ).parents( '.hb-accom-listing-column' ).find( '.hb-accom-listing-booking-form' ).slideDown();
		} else {
			$( this ).parents( '.hb-accom-listing-column' ).find( '.hb-accom-listing-booking-form' ).slideUp();
		}
		return false;
	});

	$( '.hb-accom-listing-actions-wrapper' ).on( 'click', '.hb-listing-view-accom input', function( e ) {
		e.preventDefault();
		$( this ).blur();
		window.open( $( this ).data( 'accom-url' ), '_blank' );
	});

	$( window ).resize( debouncer( function() {
		if ( $( '.hb-accom-listing-row' ).width() < 650 ) {
			$( 'div.hb-accom-listing-column' ).addClass( 'hb-accom-listing-mobile-view');
		} else {
			$( 'div.hb-accom-listing-column' ).removeClass( 'hb-accom-listing-mobile-view');
		}
	}));

	function debouncer( func ) {
		var timeoutID,
			timeout = 50;
		return function () {
			var scope = this,
				args = arguments;
			clearTimeout( timeoutID );
			timeoutID = setTimeout( function () {
				func.apply( scope, Array.prototype.slice.call( args ) );
			}, timeout );
		}
	}

});