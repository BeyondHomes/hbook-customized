'use strict';

function hb_nb_days( start_date, end_date ) {
	var start_date_obj = new Date( start_date ),
		end_date_obj = new Date( end_date ),
		utc1 = Date.UTC( start_date_obj.getFullYear(), start_date_obj.getMonth(), start_date_obj.getDate()),
		utc2 = Date.UTC( end_date_obj.getFullYear(), end_date_obj.getMonth(), end_date_obj.getDate()),
		_MS_PER_DAY = 1000 * 60 * 60 * 24;
	return Math.floor( ( utc2 - utc1 ) / _MS_PER_DAY );
}

function hb_valid_date( date ) {
	try {
		date = jQuery.datepick.parseDate( 'yyyy-mm-dd', date );
		return true;
	} catch( e ) {
		return false;
	}
}