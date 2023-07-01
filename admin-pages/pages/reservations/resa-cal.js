'use strict';

function hb_date_to_str( date ) {
	var year = date.getFullYear(),
		month = date.getMonth() + 1,
		day = date.getDate();
	day = day + '';
	if ( day.length == 1 ) {
		day = '0' + day;
	}
	month = month + '';
	if ( month.length == 1 ) {
		month = '0' + month;
	}
	var str = year + '-' + month + '-' + day;
	return str;
}

function hb_date_str_to_obj( str_date ) {
	var array_date = str_date.split( '-' );
	return new Date( array_date[0], array_date[1] - 1, array_date[2] );
}

function hb_get_day_tds( type, the_day, accom_id, accom_num ) {

	var tds = '',
		today = hb_date_to_str( new Date() );

	var yesterday = new Date();
	yesterday.setDate( yesterday.getDate() -1 );

	var current_day = hb_date_str_to_obj( the_day );
	current_day.setDate( current_day.getDate() - 7 );
	current_day.setTime( current_day.getTime() + 1000*3600*2 );

	var first_day = hb_date_str_to_obj ( the_day );
	first_day.setDate( first_day.getDate() - 7 );

	var last_day = hb_date_str_to_obj( the_day );
	last_day.setDate( last_day.getDate() + 28 );

	while ( current_day <= last_day ) {

		switch ( type ) {

			case 'days-table':
				var day_label_class = '';
				if ( hb_date_to_str( current_day ) == today ) {
					day_label_class = 'hb-resa-day-today';
				} else if ( hb_date_to_str( current_day ) == hb_date_to_str( yesterday ) ) {
					day_label_class = 'hb-resa-day-yesterday';
				}
				tds += '<td colspan="2" class="hb-resa-day-label ' + day_label_class + '">' +
						'<span><b>' + days_short_name[ current_day.getDay() ] + '</b>' +
							'<span>' + current_day.getDate() + '<br/>' + month_short_name[ current_day.getMonth() ] + '<br/>' + '</span>' +
						'</span>' +
					'</td>';
			break;

			case 'days-cal':
				var data = 'data-accom-id="' + accom_id + '" data-accom-num="' + accom_num + '" data-day="' + hb_date_to_str( current_day ) + '"';
				var resa_day_class = '';
				if ( hb_date_to_str( current_day ) == today ) {
					resa_day_class = 'hb-resa-day-today-line ';
				} else if ( hb_date_to_str( current_day ) == hb_date_to_str( yesterday ) ) {
					resa_day_class = 'hb-resa-day-today-line ';
				}
				var param = accom_id + '-' + accom_num + '-'  + hb_date_to_str( current_day );
				tds += '<td class="hbio hbio-' + param + '"></td>' +
						'<td class="hbd ' + resa_day_class + 'hbd-' + param + '"></td>';
			break;

		}

		current_day.setDate( current_day.getDate() + 1 );
	}
	return tds;
}

function hb_get_accom_trs( day, accom, accom_id ) {
	var trs = ''
	jQuery.each( accom.num_name, function( accom_num, num_name ) {
		trs += '<tr><td> ';
		if ( accom.short_name ) {
			trs += accom.short_name;
		} else {
			trs += accom.name;
		}
		if ( num_name ) {
			trs += ' (' + num_name + ')';
		}
		trs += '</td></tr>';
	});
	return trs;
}

function hb_get_cal_trs( day, accom, accom_id ) {
	var trs = ''
	jQuery.each( accom.num_name, function( accom_num, num_name ) {
		trs += '<tr>' + hb_get_day_tds( 'days-cal', day, accom_id, accom_num ) + '</tr>';
	});
	return trs;
}

function hb_resa_cal_tables( day, displayed_accoms ) {
	var accom_trs = '',
		day_tds = '',
		cal_trs = '',
		go_to_previous_month = '',
		go_to_next_month = '',
		c_day = hb_date_str_to_obj( day ),
		two_weeks_before_the_day = hb_date_str_to_obj( day ),
		two_weeks_after_the_day = hb_date_str_to_obj( day );

	for ( var i = 0; i < all_accom_ids.length; i++ ) {
		if ( displayed_accoms[ all_accom_ids[ i ] ] ) {
			accom_trs += hb_get_accom_trs( day, displayed_accoms[ all_accom_ids[ i ] ], all_accom_ids[ i ] );
			cal_trs += hb_get_cal_trs( day, displayed_accoms[ all_accom_ids[ i ] ], all_accom_ids[ i ] );
		}
	}

	two_weeks_after_the_day.setDate( c_day.getDate() + 14 );
	two_weeks_before_the_day.setDate( c_day.getDate() - 14 );

	var first_day = hb_date_str_to_obj ( day );
	first_day.setDate( first_day.getDate() - 7 );

	var last_day = hb_date_str_to_obj( day );
	last_day.setDate( last_day.getDate() + 27 );

	var calendar_range = first_day.getDate() + ' ' + month_short_name[ first_day.getMonth() ] + ' ' + first_day.getFullYear() + ' - ' + last_day.getDate() + ' ' + month_short_name[ last_day.getMonth() ] + ' ' + last_day.getFullYear();
	var calendar_controls = '<a href="#" class="hb-go-to-previous-two-weeks button" data-day="' + hb_date_to_str( two_weeks_before_the_day ) + '">&lsaquo;</a><a href="#" class="hb-display-calendar button"><span class="dashicons dashicons-calendar-alt"></span></a><a href="#" class="hb-go-to-next-two-weeks button" data-day="' + hb_date_to_str( two_weeks_after_the_day ) + '">&rsaquo;</a>';
	jQuery( '#hb-resa-accom-table' ).html(
		'<tr>' +
			'<td id="hb-commands-top" class="hb-resa-cal-commands">' +
				'<span class="calendar-range" data-first-day="' + hb_date_to_str( first_day ) + '" data-last-day="' + hb_date_to_str( last_day ) + '">' +
					calendar_range  +
				'</span>' +
				'<div class="hb-calendar-controls">' +
					calendar_controls +
				'</div>' +
			'</td>' +
		'</tr>' +
		accom_trs +
		'<tr>' +
			'<td id="hb-commands-bottom" class="hb-resa-cal-commands">' +
				'<span class="calendar-range">' +
					calendar_range +
				'</span>' +
				'<div class="hb-calendar-controls bottom">' +
					calendar_controls +
				'</div>' +
			'</td>' +
		'</tr>'
	);
	jQuery( '#hb-resa-cal-table' ).html( '<tr>' + hb_get_day_tds( 'days-table', day ) + '</tr>' + cal_trs + '<tr id="hb-cal-days-bottom">' + hb_get_day_tds( 'days-table', day ) + '</tr>' );
	jQuery( '#hb-resa-cal-table' ).data( 'first-day', day );
}

function hb_date_from_in_range( date ) {
	var first_day_calendar = jQuery( '.calendar-range' ).data( 'first-day' );
	if ( date < first_day_calendar ) {
		date = first_day_calendar;
	}
	return date;
}

function hb_date_to_in_range( date ) {
	var last_day_calendar = hb_date_str_to_obj( jQuery( '.calendar-range' ).data( 'last-day' ) );

	last_day_calendar.setDate( last_day_calendar.getDate() + 1 );
	if ( date == '0000-00-00' ) {
		return hb_date_to_str( last_day_calendar );
	}
	date = hb_date_str_to_obj( date )
	if ( date > last_day_calendar ) {
		date = last_day_calendar;
	}
	return hb_date_to_str( date );
}

function hb_dates_in_range( from_date, to_date ) {
	if ( ( from_date <= jQuery( '.calendar-range' ).data( 'last-day' ) ) && ( to_date >= jQuery( '.calendar-range' ).data( 'first-day' ) ) ) {
		return true;
	} else {
		return false;
	}
}

function hb_set_resa_cal( all_resa, all_blocked_accom, all_customers, day, displayed_accoms ) {
	if ( ! jQuery( '.calendar-range' ).length ) {
		return;
	}

	var cal_mark_up = '';
	for ( var i = 0; i < all_accom_ids.length; i++ ) {
		if ( displayed_accoms[ all_accom_ids[ i ] ] ) {
			cal_mark_up += hb_get_cal_trs( day, displayed_accoms[ all_accom_ids[ i ] ], all_accom_ids[ i ] );
		}
	}

	var td_width = 20;

	for ( var i = 0; i < all_resa.length; i++ ) {
		if (
			! all_resa[i].is_parent &&
			all_resa[i].status() != 'cancelled' &&
			all_resa[i].status() != 'pending' &&
			hb_dates_in_range( all_resa[i].check_in(), all_resa[i].check_out() )
		) {
			var from_date = hb_date_from_in_range( all_resa[i].check_in() ),
				to_date = hb_date_to_in_range( all_resa[i].check_out() ),
				customer = ko.utils.arrayFirst( all_customers, function( customer ) {
					return all_resa[i].customer_id() == customer.id;
				}),
				resa_nb_days = hb_nb_days( from_date, to_date ),
				customer_name = '',
				resa_call_details_link;

			if ( customer ) {
				if ( customer.first_name() ) {
					customer_name = customer.first_name();
				}
				if ( customer.last_name() ) {
					if ( customer_name ) {
						customer_name += ' ';
					}
					customer_name += customer.last_name();
				}
			}

			resa_call_details_link = '<a href="#" class="hbdlcd';
			if ( resa_nb_days == 1 && all_resa[i].id >= 1000 ) {
				resa_call_details_link += ' hbdli';
			}
			if ( resa_nb_days > 1 ) {
				resa_call_details_link += ' hbdmn';
				var resa_call_details_link_width = td_width * 2 * resa_nb_days - td_width;
				resa_call_details_link += '" style="width:' + resa_call_details_link_width + 'px"';
			} else {
				resa_call_details_link += '"';
			}
			resa_call_details_link += ' title="' + all_resa[i].id + '. ' + customer_name + '"';
			resa_call_details_link += ' data-resa-id="' + all_resa[i].id + '">';
			resa_call_details_link += all_resa[i].id;
			if ( resa_nb_days > 1 ) {
				resa_call_details_link += '<span>. ' + customer_name + '</span>';
			}
			resa_call_details_link += '</a>';

			var param_accom = all_resa[i].accom_id() + '-' + all_resa[i].accom_num(),
				param_from = param_accom + '-' + from_date;

			if ( from_date != all_resa[i].check_out() ) {
				if ( from_date == all_resa[i].check_in() ) {
					cal_mark_up = cal_mark_up.replace(
						'hbio-' + param_from + '">',
						'hbio-' + param_from + '"><div></div><span></span>'
					);
					cal_mark_up = cal_mark_up.replace(
						'hbio-' + param_from,
						'hbio-' + param_from + ' hbdtci hbdci-' + all_resa[i].status()
					);
				} else {
					cal_mark_up = cal_mark_up.replace(
						'hbio-' + param_from,
						'hbdt hbd-' + all_resa[i].status()
					);
				}
				cal_mark_up = cal_mark_up.replace(
					'hbd-' + param_from + '">',
					'hbdt hbd-' + all_resa[i].status() + '">' + resa_call_details_link
				);
			}

			var current_day = hb_date_str_to_obj( from_date ),
				last_day = hb_date_str_to_obj( to_date );

			last_day.setDate( last_day.getDate() - 1 );

			while ( current_day < last_day ) {
				current_day.setDate( current_day.getDate() + 1 );
				var param_current_day = param_accom + '-' + hb_date_to_str( current_day );
				cal_mark_up = cal_mark_up.replace(
					'hbio-' + param_current_day,
					'hbdt hbd-' + all_resa[i].status()
				);
				cal_mark_up = cal_mark_up.replace(
					'hbd-' + param_current_day,
					'hbdt hbd-' + all_resa[i].status()
				);
			}

			if ( to_date <= jQuery( '.calendar-range' ).data( 'last-day' ) ) {
				var param_to = param_accom + '-' + to_date;
				cal_mark_up = cal_mark_up.replace(
					'hbio-' + param_to + '">',
					'hbio-' + param_to + '"><div></div><span></span>'
				);
				cal_mark_up = cal_mark_up.replace(
					'hbio-' + param_to,
					'hbio-' + param_to + ' hbdtco hbdco-' + all_resa[i].status()
				);
			}
		}
	}

	for ( var i = 0; i < all_blocked_accom.length; i++ ) {
		var is_accom_defined = true;
		if ( all_blocked_accom[i].accom_id ) {
			var defined_accom_ids = Object.keys( accoms );
			if ( defined_accom_ids.indexOf( all_blocked_accom[i].accom_id + '' ) == -1 ) {
				is_accom_defined = false;
			}
		}
		if (
			hb_dates_in_range( all_blocked_accom[i].from_date(), all_blocked_accom[i].to_date() ) &&
			is_accom_defined
		) {
			var from_date = hb_date_from_in_range( all_blocked_accom[i].from_date() ),
				to_date = hb_date_to_in_range( all_blocked_accom[i].to_date() ),
				blocked_nb_days,
				blocked_accom_comment;

			blocked_nb_days = hb_nb_days( from_date, to_date )
			blocked_accom_comment = '<div class="hbdbc"';
			blocked_accom_comment += ' title="' + all_blocked_accom[i].comment + '"';
			if ( blocked_nb_days > 1 ) {
				var blocked_accom_comment_width = td_width * 2 * blocked_nb_days - td_width;
				blocked_accom_comment += '" style="width:' + blocked_accom_comment_width + 'px">';
				blocked_accom_comment += '<span>' + all_blocked_accom[i].comment + '</span>';
			} else {
				blocked_accom_comment += '><span>';
				if ( all_blocked_accom[i].comment.indexOf( '.' ) > 0 ) {
					var linked_blocked_accom_id = all_blocked_accom[i].comment.substring( 0, all_blocked_accom[i].comment.indexOf( '.' ) );
					if ( parseInt( linked_blocked_accom_id ) ==  linked_blocked_accom_id ) {
						blocked_accom_comment += linked_blocked_accom_id;
					} else {
						blocked_accom_comment += '&nbsp;'
					}
				} else {
					blocked_accom_comment += '&nbsp;'
				}
				blocked_accom_comment += '</span>';
			}
			blocked_accom_comment += '</div>';

			var blocked_accom_ids = [];
			var blocked_accom_ids_nums = {};
			if ( all_blocked_accom[i].accom_all_ids ) {
				blocked_accom_ids = Object.keys( accoms );
			} else {
				blocked_accom_ids.push( all_blocked_accom[i].accom_id );
			}
			if ( ! all_blocked_accom[i].accom_all_num ) {
				blocked_accom_ids_nums[ all_blocked_accom[i].accom_id ] = [ all_blocked_accom[i].accom_num ];
			} else {
				for ( var j = 0; j < blocked_accom_ids.length; j++ ) {
					blocked_accom_ids_nums[ blocked_accom_ids[j] ] = Object.keys( accoms[ blocked_accom_ids[j] ]['num_name'] );
				}
			}

			for ( var k = 0; k < blocked_accom_ids.length; k++ ) {
				for ( var l = 0; l < blocked_accom_ids_nums[ blocked_accom_ids[k] ].length; l++ ) {
					var param_accom = blocked_accom_ids[k] + '-' + blocked_accom_ids_nums[ blocked_accom_ids[k] ][l],
						param_from = param_accom + '-' + from_date;

					if ( from_date != all_blocked_accom[i].to_date() ) {
						if ( from_date == all_blocked_accom[i].from_date() ) {
							cal_mark_up = cal_mark_up.replace(
								'hbio-' + param_from + '">',
								'hbio-' + param_from + '"><div></div><span></span>'
							);
							cal_mark_up = cal_mark_up.replace(
								'hbio-' + param_from,
								'hbio-' + param_from + ' hbdtci hbdcib'
							);
						} else {
							cal_mark_up = cal_mark_up.replace(
								'hbio-' + param_from,
								'hbdt hbdb'
							);
						}
						cal_mark_up = cal_mark_up.replace(
							'hbd-' + param_from + '">',
							'hbdt hbdb">' + blocked_accom_comment
						);

						var current_day = hb_date_str_to_obj( from_date ),
							last_day = hb_date_str_to_obj( to_date );

						last_day.setDate( last_day.getDate() - 1 );

						while ( current_day < last_day ) {
							current_day.setDate( current_day.getDate() + 1 );
							var param_current_day = param_accom + '-' + hb_date_to_str( current_day );
							cal_mark_up = cal_mark_up.replace(
								'hbio-' + param_current_day,
								'hbdt hbdb'
							);
							cal_mark_up = cal_mark_up.replace(
								'hbd-' + param_current_day,
								'hbdt hbdb'
							);
						}
					}
					if ( to_date <= jQuery( '.calendar-range' ).data( 'last-day' ) ) {
						var param_to = param_accom + '-' + to_date;
						cal_mark_up = cal_mark_up.replace(
							'hbio-' + param_to + '">',
							'hbio-' + param_to + '"><div></div><span></span>'
						);
						cal_mark_up = cal_mark_up.replace(
							'hbio-' + param_to,
							'hbio-' + param_to + ' hbdtco hbdcob'
						);
					}
				}
			}
		}
	}

	jQuery( '#hb-resa-cal-table' ).html( '<tr>' + hb_get_day_tds( 'days-table', day ) + '</tr>' + cal_mark_up + '<tr id="hb-cal-days-bottom">' + hb_get_day_tds( 'days-table', day ) + '</tr>' );
}

jQuery( document ).ready( function( $ ) {

	$( '#hb-resa-cal-scroller').on( 'scroll', function() {
		$( '#hb-resa-accom-table' ).css( 'left', $( this ).scrollLeft() );
		$( '#hb-resa-cal-commands' ).css( { 'left': $( this ).scrollLeft(), 'top': $( this ).scrollTop() } );
	});

	function set_month_picker( year ) {
		var months = '',
			year_before = new Date( year - 1, 0, 1 ),
			year_after = new Date( year + 1, 0, 1),
			current_day = new Date( year, 0, 1 );

		$( '.hb-go-to-previous-year' ).data( 'year', year - 1 );
		$( '.hb-go-to-next-year' ).data( 'year', year + 1 );
		for ( var i=1; i <= 12 ; i++ ) {
			var data = 'data-day="' + hb_date_to_str( current_day ) + '"';
			months += '<a href="#" class="hb-month button"' + data + '>' + month_short_name[ current_day.getMonth() ] + ' ' + current_day.getFullYear() + '</a>';
			current_day.setMonth( current_day.getMonth() + 1 );
			current_day.setDate( 1 );
		}
		$( '.hb-months' ).html( months );
	}

	var month_picker_controls = '<a href="#" class="hb-go-to-previous-year button">&lsaquo;</a>';
	month_picker_controls += '<a href="#" class="hb-go-to-next-year button">&rsaquo;</a>';
	var month_picker = '<div class="hb-month-picker"><div class="hb-month-picker-controls">';
	month_picker += month_picker_controls;
	month_picker += '</div><div class="hb-months"></div></div>';
	$( 'body' ).append( month_picker );

	var today = new Date(),
		today_year = today.getFullYear();
	set_month_picker( today_year );

	$( '#hb-resa-cal-wrapper' ).on( 'click', '.hb-display-calendar', function() {
		if ( $( '.hb-month-picker').is( ':visible' ) ) {
			$( '.hb-month-picker').slideUp();
		} else {
			var coordinate = $( this ).offset();
			if ( $( this ).parent().hasClass( 'bottom' ) ) {
				$( '.hb-month-picker').css( 'top', coordinate[ 'top' ] - 210 );
			} else {
				$( '.hb-month-picker').css( 'top', coordinate[ 'top' ] + 37 );
			}
			$( '.hb-month-picker').css( 'left', coordinate[ 'left' ] - 100 );
			$( '.hb-month-picker').slideDown();
		}
	});

	$( 'body' ).on( 'click', '.hb-go-to-previous-year, .hb-go-to-next-year', function() {
		set_month_picker( $( this ).data( 'year' ) );
		return false;
	});

});