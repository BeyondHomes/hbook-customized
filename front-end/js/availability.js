jQuery( document ).ready( function( $ ) {
	'use strict';

	$( '.hb-availability-calendar' ).datepick( hb_datepicker_calendar_options );

	var today = new Date(),
		hb_dp_min_date = false,
		hb_dp_max_date = false;

	today.setHours( 0, 0, 0, 0 );

	if ( hb_min_date != '0' ) {
		hb_dp_min_date = hb_date_str_2_obj( hb_min_date );
	} else {
		hb_dp_min_date = 0;
	}
	if ( hb_max_date != '0' ) {
		hb_dp_max_date = hb_date_str_2_obj( hb_max_date );
	}

	$( '.hb-availability-calendar' ).each( function() {
		var hb_status_days = $( this ).data( 'status-days' );
		$( this ).datepick( 'option', {
			minDate: hb_dp_min_date,
			maxDate: hb_dp_max_date,

			onDate: function ( date_noon, date_is_in_current_month ) {
				var date = new Date( date_noon.getTime() );
					date.setHours( 0, 0, 0, 0 );
				var day = date.getDate(),
					str_date = hb_date_obj_2_str( date ),
					on_date_returned = {};

				on_date_returned['selectable'] = false;
				on_date_returned['dateClass'] = 'hb-dp-date-' + str_date;

				if ( ! date_is_in_current_month ) {
					on_date_returned['dateClass'] += ' hb-dp-day-not-current-month';
				} else if ( date < today ) {
					on_date_returned['title'] = hb_availability_text.legend_past;
					on_date_returned['dateClass'] += ' hb-dp-day-past';
				} else if ( hb_dp_min_date && date < hb_dp_min_date ) {
					on_date_returned['title'] = hb_availability_text.legend_closed;
					on_date_returned['dateClass'] += ' hb-dp-day-closed';
				} else if ( hb_dp_max_date && date > hb_dp_max_date ) {
					on_date_returned['title'] = hb_availability_text.legend_closed;
					on_date_returned['dateClass'] += ' hb-dp-day-closed';
				} else if ( hb_status_days[ str_date ] ) {
					switch ( hb_status_days[ str_date ] ) {
						case 'hb-day-fully-taken' : on_date_returned['title'] = hb_availability_text.legend_occupied; break;
						case 'hb-day-taken-start' : on_date_returned['title'] = hb_availability_text.legend_check_out_only; break;
						case 'hb-day-taken-end' : on_date_returned['title'] = hb_availability_text.legend_check_in_only; break;
					}
					on_date_returned['dateClass'] += ' ' + hb_status_days[ str_date ];
					on_date_returned['content'] = '<span class="hb-day-taken-content">' + day + '</span>'
				} else {
					on_date_returned['title'] = hb_availability_text.legend_available;
					on_date_returned['dateClass'] += ' hb-day-available';
				}
				return on_date_returned;
			},
			onChangeMonthYear: function( year, month ) {
				$( this ).data( 'current-shown-month', month );
				$( this ).data( 'current-shown-year', year );
				if ( calendar_resize_timer ) {
					clearInterval( calendar_resize_timer );
					calendar_resize_timer = setInterval( calendar_resize, 2000 );
				}
			}
		});

	});

	function calendar_resize() {
		$( '.hb-availability-calendar' ).each( function() {
			var $calendar = $( this ),
				calendar_sizes = $( this ).data( 'calendar-sizes' ),
				calendar_widths = [],
				current_shown_month = $( this ).data( 'current-shown-month' ),
				current_shown_year = $( this ).data( 'current-shown-year' ),
				wrapper_saved_width = $( this ).data( 'wrapper-width' ),
				wrapper_width = $calendar.parents( '.hb-availability-calendar-wrapper' ).width();

			if ( wrapper_width != wrapper_saved_width ) {
				$( this ).data( 'wrapper-width', wrapper_width );
				$calendar.parents( '.hb-availability-calendar-centered' ).width( 'auto' );
				for ( var i = 0; i < calendar_sizes.length; i++ ) {
					$calendar.datepick( 'option', 'monthsToShow', parseInt( calendar_sizes[i].cols ) );
					calendar_widths[ calendar_sizes[i].cols ] = $calendar.find( '.hb-datepick-wrapper' ).width();
				}
				for ( var i = 0; i < calendar_sizes.length; i++ ) {
					var available_width = $calendar.width();
					if ( calendar_widths[ calendar_sizes[i].cols ] <= available_width ) {
						$calendar.datepick( 'option', 'monthsToShow', [ parseInt( calendar_sizes[i].rows ), parseInt( calendar_sizes[i].cols ) ] );
						if ( calendar_sizes[i].rows > 1 ) {
							$calendar.datepick( 'option', 'monthsToStep', parseInt( calendar_sizes[i].cols ) );
						} else {
							$calendar.datepick( 'option', 'monthsToStep', 1 );
						}
						$calendar.datepick( 'showMonth', current_shown_year, current_shown_month );
						$calendar.parents( '.hb-availability-calendar-centered' ).width( $calendar.find( '.hb-datepick-wrapper' ).width() );
						return;
					}
				}
				$calendar.datepick( 'option', 'monthsToShow', 1 );
				$calendar.datepick( 'option', 'monthsToStep', 1 );
				$calendar.datepick( 'showMonth', current_shown_year, current_shown_month );
				$calendar.parents( '.hb-availability-calendar-centered' ).width( $calendar.find( '.hb-datepick-wrapper' ).width() );
			}
		});
	}

	calendar_resize();
	var calendar_resize_timer = setInterval( calendar_resize, 2000 );
});