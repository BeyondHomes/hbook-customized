jQuery( document ).ready( function( $ ) {
	'use strict';

	$( '.hb-rate-days' ).each( function() {
		var days = $( this ).html().split( ',' ),
			days_name = [];
		for ( var i = 0; i < days.length; i++ ) {
			var day_num = parseInt( days[i] ) + 1;
			if ( day_num == 7 ) {
				day_num = 0;
			}
			days_name.push( hb_day_names[ day_num ] );
		}
		days_name = days_name.join( ', ' );
		$( this ).html( days_name );
	});

	hb_format_date();
});