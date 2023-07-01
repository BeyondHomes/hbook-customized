'use strict';

function hb_date_str_2_obj( str_date ) {
	if ( str_date ) {
		var array_date = str_date.split( '-' );
		return new Date( array_date[0], array_date[1] - 1, array_date[2] );
	} else {
		return false;
	}
}

function hb_date_obj_2_str( obj_date ) {
	if ( obj_date ) {
		var y = obj_date.getFullYear(),
			m = obj_date.getMonth() + 1,
			d = obj_date.getDate();
		m = m + '';
		d = d + '';
		if ( m.length == 1 ) {
			m = '0' + m;
		}
		if ( d.length == 1 ) {
			d = '0' + d;
		}
		return y + '-' + m + '-' + d;
	} else {
		return false;
	}
}

function hb_format_date() {
	jQuery( '.hb-format-date' ).each( function() {
		var str_date = jQuery( this ).html();
		if ( str_date.indexOf( '-' ) > -1 ) {
			var date = hb_date_str_2_obj( str_date );
			jQuery( this ).html( jQuery.datepick.formatDate( hb_date_format, date ) ).removeClass( 'hb-format-date' );
		}
	});
}

function hb_get_season_id( date ) {
	var seasons = hb_booking_form_data.seasons,
		nb_day,
		copied_date = new Date( date.valueOf() );

	var selected_accom_id = jQuery('select.search-form-accom-select').val();
	var accom_season_ids = hb_booking_form_data.accom_seasons;
	var season_ids = [];
	var valid_season = true;
	if(selected_accom_id && accom_season_ids && accom_season_ids[selected_accom_id]){
		season_ids = accom_season_ids[selected_accom_id];
	}

	copied_date.setHours( 0, 0, 0, 0 );
	nb_day = date.getDay();
	if ( nb_day == 0 ) {
		nb_day = 6;
	} else {
		nb_day = nb_day - 1;
	}
	nb_day += '';

	var priorities = ['high', '', 'low'];
	for ( var i = 0; i < 3; i++ ) {
		for ( var j = 0; j < seasons.length; j++ ) {
			var start = hb_date_str_2_obj( seasons[ j ]['start_date'] );
			var end = hb_date_str_2_obj( seasons[ j ]['end_date'] );
			start.setHours( 0, 0, 0, 0 );
			end.setHours( 0, 0, 0, 0 );

			if(season_ids.length){
				valid_season = false;
				if(season_ids.indexOf(seasons[ j ]['season_id']) !== -1){
					valid_season = true;
				}
			}

			if (
				( seasons[ j ]['priority'] == priorities[ i ] ) &&
				( copied_date >= start ) &&
				( copied_date <= end ) &&
				( seasons[ j ]['days'].indexOf( nb_day ) != -1 )&&
				( valid_season )
			) {
				return seasons[ j ]['season_id'];
			}
		}
	}
	return false;
}