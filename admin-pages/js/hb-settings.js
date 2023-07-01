'use strict';

jQuery( document ).ready( function( $ ) {
	var close_right_menu_timer;

	$( '#hb-admin-settings-link, #hb-admin-right-menu' ).on( 'mouseenter', function() {
		clearTimeout( close_right_menu_timer );
		$( '#hb-admin-right-menu' ).fadeIn();
	});

	$( '#hb-admin-settings-link, #hb-admin-right-menu' ).on( 'mouseleave', function() {
		close_right_menu_timer = setTimeout( function() { $( '#hb-admin-right-menu' ).fadeOut() }, 500 );
	});

});

function hb_section_toggle( section_name ) {
	var section_id = '#hb-' + section_name,
		section_toggle_id = section_id + '-toggle';
	jQuery( section_toggle_id ).on( 'click', function() {
		jQuery( section_id ).slideToggle( function() {
			if ( jQuery( this ).is(':visible' ) ) {
				jQuery( section_toggle_id + ' .dashicons-arrow-up' ).css( 'display', 'inline-block' );
				jQuery( section_toggle_id + ' .dashicons-arrow-down' ).hide();
				if ( section_name == 'admin-add-resa' ) {
					jQuery( window ).resize();
				}
			} else {
				jQuery( section_toggle_id + ' .dashicons-arrow-down' ).css( 'display', 'inline-block' );
				jQuery( section_toggle_id + ' .dashicons-arrow-up' ).hide();
			}
		});
		return false;
	});
}

function hb_decode_entities( str ) {
	var textarea = document.createElement( 'textarea' );
	textarea.innerHTML = str;
	return textarea.value;
}