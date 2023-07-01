jQuery( document ).ready( function( $ ) {
	'use strict';

	$( 'select[name="hb_resa_page_default_filter"]' ).on( 'change', function() {
		resa_page_default_filter();
	});

	resa_page_default_filter();

	function resa_page_default_filter() {
		$( '.hb-resa-page-default-filter-option' ).slideUp();
		var new_filter_val = $( 'select[name="hb_resa_page_default_filter"]' ).val();
		switch ( new_filter_val ) {
			case 'check_in_date':
			case 'check_out_date':
			case 'check_in_out_date':
			case 'active_resa_date':
				$( '.hb-resa-page-default-filter-' + new_filter_val.replace( '_date', '' ).replaceAll( '_', '-' ) + '-from' ).slideDown();
				$( '.hb-resa-page-default-filter-' + new_filter_val.replace( '_date', '' ).replaceAll( '_', '-' ) + '-to' ).slideDown();
				break;
			case 'accom':
			case 'status':
				$( '.hb-resa-page-default-filter-' + new_filter_val ).slideDown();
				break;
		}
		$( '.hb-toggle-default-filters').addClass( 'hb-view-all-active' );
	}

	$( '.hb-toggle-default-filters-link' ).on( 'click', function() {
		if ( $( '.hb-toggle-default-filters').hasClass( 'hb-view-all-active' ) ) {
			$( '.hb-resa-page-default-filter-option' ).slideDown();
			$( '.hb-toggle-default-filters').removeClass( 'hb-view-all-active' );
		} else {
			resa_page_default_filter();
		}
		return false;
	});

	$( 'input[name="hb_multiple_accom_booking"]' ).on( 'change', function() {
		multiple_accom_booking();
	});

	multiple_accom_booking();

	function multiple_accom_booking() {
		if ( $( 'input[name="hb_multiple_accom_booking"]:checked' ).val() == 'enabled' ) {
			$( '.hb-multiple-accom-booking-options-wrapper' ).slideDown();
		} else {
			$( '.hb-multiple-accom-booking-options-wrapper' ).slideUp();
			$( '#hb_multiple_accom_booking_front_end_disabled' ).prop( 'checked', true );
		}
	}

	$( 'input[name="hb_multiple_accom_booking_suggest_occupancy"]' ).on( 'change', function() {
		suggest_occupancy_type();
	});

	suggest_occupancy_type();

	function suggest_occupancy_type() {
		if ( $( 'input[name="hb_multiple_accom_booking_suggest_occupancy"]:checked' ).val() == 'normal' ) {
			$( '.hb-multiple-accom-booking-avoid-singleton' ).slideDown();
		} else {
			$( '.hb-multiple-accom-booking-avoid-singleton' ).slideUp();
		}
	}

	$( '.hb-import-settings' ).on( 'click', function() {
		$( '.hb-import-settings' ).blur();
		if ( $( '#hb-import-settings-file' ).val() == '' ) {
			alert( hb_text.choose_file );
			return false;
		}
		if ( confirm( hb_text.import_confirm_text ) ) {
			$( '#hb-import-export-action' ).val( 'import-settings' );
			$( '.hb-import-settings-waiting-msg' ).slideDown();
			$( '#hb-settings-form' ).submit();
			return false;
		} else {
			return false;
		}
	});

	$( '.hb-export-settings' ).on( 'click', function() {
		$( this ).blur();
		$( '#hb-import-export-action' ).val( 'export-settings' );
		$( '#hb-settings-form' ).submit();
		$( '#hb-import-export-action' ).val( '' );
		return false;
	});

	$( '.hb-reset-hbook' ).click( function() {
		$( '.hb-reset-hbook' ).blur();
		var action = $( this ).closest('div').attr('id');
		if ( confirm( hb_text[ action + '_confirm_text' ] ) ) {
			$( '#hb-reset-hbook-action' ).val( action );
			$( '#hb-settings-form' ).submit();
		} else {
			return false;
		}
	});
});