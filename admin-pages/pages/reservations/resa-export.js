jQuery( document ).ready( function( $ ) {
	'use strict';

	$( '#hb-export-resa-cancel' ).on( 'click', function() {
		$( '#hb-export-resa' ).slideUp( function() {
			$( '#hb-export-resa-toggle .dashicons-arrow-down' ).css( 'display', 'inline-block' );
			$( '#hb-export-resa-toggle .dashicons-arrow-up' ).hide();
		});
		return false;
	});

	$( '#hb-export-resa-select-all-accom' ).on( 'click', function() {
		$( this ).blur();
		$( 'input[name="hb-export-resa-accom[]"]' ).prop( 'checked', true );
		return false;
	});

	$( '#hb-export-resa-unselect-all-accom' ).on( 'click', function() {
		$( this ).blur();
		$( 'input[name="hb-export-resa-accom[]"]' ).prop( 'checked', false );
		return false;
	});

	$( '#hb-export-resa-select-all-data' ).on( 'click', function() {
		$( this ).blur();
		$( 'input[name="hb-resa-data-export[]"]' ).prop( 'checked', true );
		return false;
	});

	$( '#hb-export-resa-unselect-all-data' ).on( 'click', function() {
		$( this ).blur();
		$( 'input[name="hb-resa-data-export[]"]' ).prop( 'checked', false );
		return false;
	});

	$( '#hb-export-resa-selection-received-date-from, #hb-export-resa-selection-received-date-to' ).focus( function() {
		$( '#hb-export-resa-selection-received-date' ).prop( 'checked', true );
	});

	$( '#hb-export-resa-selection-check-in-date-from, #hb-export-resa-selection-check-in-date-to' ).focus( function() {
		$( '#hb-export-resa-selection-check-in-date' ).prop( 'checked', true );
	});

	$( '#hb-export-resa-selection-check-out-date-from, #hb-export-resa-selection-check-out-date-to' ).focus( function() {
		$( '#hb-export-resa-selection-check-out-date' ).prop( 'checked', true );
	});

	$( '#hb-export-resa-download' ).on( 'click', function() {
		$( this ).blur();
		if ( ! $( 'input[name="hb-resa-data-export[]"]:checked').length ) {
			alert( hb_text.no_export_data_selected );
			return false;
		}
		if (
			$( 'input[name="hb-export-resa-accom[]"]' ).length &&
			! $( 'input[name="hb-export-resa-accom[]"]:checked').length
		) {
			alert( hb_text.no_export_accom_selected );
			return false;
		}
		$( '#hb-export-resa-form' ).submit();
		return false;
	});

});