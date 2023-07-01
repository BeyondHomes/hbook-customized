'use strict';

function date_to_str( date ) {
	if ( date == '0000-00-00' ) {
		return '';
	} else {
		return date;
	}
}

function Season( brand_new, id, name, priority, dates ) {
	HbSetting.call( this, brand_new, 'season', id, name );
	this.dates = ko.observableArray( dates );
	this.priority = ko.observable( priority );

	var self = this;

	this.revert = function( season ) {
		if ( season ) {
			self.name( season.name );
			self.priority( season.priority );
		}
	}

}

function SeasonDates( brand_new, id, season_id, start_date, end_date, days ) {
	HbSetting.call( this, brand_new, 'season_date', id );
	this.season_id = season_id;
	this.start_date = ko.observable( start_date );
	this.end_date = ko.observable( end_date );
	if ( days ) {
		this.days = ko.observableArray( days.split( ',' ) );
	} else {
		this.days = ko.observableArray();
	}

	var self = this;

	this.start_date_text = ko.computed( function() {
		return date_to_str( self.start_date() );
	});

	this.end_date_text = ko.computed( function() {
		return date_to_str( self.end_date() );
	});

	this.days_list = ko.computed( function() {
		var days = self.days();
		if ( days.length == 0 ) {
			return hb_text.no_days_selected;
		} else if ( days.length == 7 ) {
			return hb_text.all;
		} else {
			var days_list = [];
			var reordered_days = days.sort();
			for ( var i = 0; i < reordered_days.length; i++ ) {
				days_list[i] = days_short_name[reordered_days[i]];
			}
			return days_list.join( ', ' );
		}
	}, self );

	this.revert = function( season_date ) {
		if ( season_date ) {
			self.start_date( season_date.start_date );
			self.end_date( season_date.end_date );
			self.days( season_date.days );
		}
	}

	this.select_all_days = function( season_date ) {
		if ( season_date ) {
			season_date.days( ['0', '1', '2', '3', '4', '5', '6'] );
		}
	}

	this.unselect_all_days = function( season_date ) {
		if ( season_date ) {
			season_date.days( [] );
		}
	}

}

function SeasonsViewModel() {

	var self = this;
	var observable_seasons = [];
	//seasons = seasons.reverse();
	for ( var i = 0; i < seasons.length; i++ ) {
		var observable_dates = [];
		//seasons[i].dates = seasons[i].dates.reverse();
		for ( var j = 0; j < seasons[i].dates.length; j++ ) {
			observable_dates.push( new SeasonDates( false, seasons[i].dates[j].id, seasons[i].dates[j].season_id, seasons[i].dates[j].start_date, seasons[i].dates[j].end_date, seasons[i].dates[j].days ) );
		}
		observable_seasons.push( new Season( false, seasons[i].id, seasons[i].name, seasons[i].priority, observable_dates ) );
	}

	this.seasons = ko.observableArray( observable_seasons );

	ko.utils.extend( this, new HbSettings() );

	this.create_season = function() {
		var new_season = new Season( true, 0, hb_text.new_season, '', [] );
		self.create_setting( new_season, function( new_season ) {
			self.seasons.push( new_season );
		});
	}

	this.create_season_dates = function( season ) {
		var new_season_dates = new SeasonDates( true, 0, season.id, jQuery.datepick.formatDate( 'yyyy-mm-dd', new Date() ), '', '0,1,2,3,4,5,6' );
		self.create_child_setting( season, new_season_dates, function( new_season_dates ) {
			season.dates.push( new_season_dates );
		});
	}

	this.remove = function( setting, event, season ) {
		if ( setting.type == 'season' ) {
			var callback_function = function() {
				self.seasons.remove( setting );
			}
		} else {
			var callback_function = function() {
				season.dates.remove( setting );
			}
		}
		self.delete_setting( setting, callback_function );
	}

	this.season_render = function() {
		jQuery( '.hb-season-date' ).datepick( hb_datepicker_calendar_options );
		jQuery( '.hb-season-date' ).datepick( 'option', {
			dateFormat : 'yyyy-mm-dd',
			onSelect: function() {
				jQuery( this ).change();
			}
		});
		jQuery( '.hb-season-date-start' ).change( function () {
			var start_date = jQuery( this ).datepick( 'getDate' )[0],
				$end_date_input = jQuery( this ).parents( '.hb-season-dates-row' ).find( '.hb-season-date-end' ),
				end_date = $end_date_input.datepick( 'getDate' )[0];
			if ( start_date && end_date && ( start_date.getTime() >= end_date.getTime() ) ) {
				$end_date_input.datepick( 'setDate', null );
			}
			if ( start_date ) {
				$end_date_input.datepick( 'option', 'minDate', start_date );
			}
		});
	}

	this.nb_rows = ko.computed( function() {
		var nb_rows = 0;
		for ( var i = 0; i < self.seasons().length; i++ ) {
			nb_rows++;
			nb_rows += self.seasons()[ i ].dates().length;
		}
		return nb_rows;
	});
}

ko.applyBindings( new SeasonsViewModel() );
