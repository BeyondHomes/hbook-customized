'use strict';

function Rule( brand_new, id, name, type, check_in_days, check_out_days, minimum_stay, maximum_stay, accom, all_accom, seasons, all_seasons, conditional_type ) {

	HbSetting.call( this, brand_new, 'rule', id );
	Accom.call( this, accom, all_accom );
	HbSeasons.call( this, seasons, all_seasons );

	this.name = ko.observable( name );
	this.rule_type = type;
	if ( check_in_days ) {
		this.check_in_days = ko.observableArray( check_in_days.split( ',' ) );
	} else {
		this.check_in_days = ko.observableArray();
	}
	if ( check_out_days ) {
		this.check_out_days = ko.observableArray( check_out_days.split( ',' ) );
	} else {
		this.check_out_days = ko.observableArray();
	}
	this.minimum_stay = ko.observable( minimum_stay );
	this.maximum_stay = ko.observable( maximum_stay );
	this.conditional_type = ko.observable( conditional_type );

	var self = this;

	this.revert = function( rule ) {
		if ( rule ) {
			self.name( rule.name );
			self.check_in_days( rule.check_in_days );
			self.check_out_days( rule.check_out_days );
			self.minimum_stay( rule.minimum_stay );
			self.maximum_stay( rule.maximum_stay );
			self.accom( rule.accom );
			self.all_accom( rule.all_accom );
			self.conditional_type( rule.conditional_type );
		}
	}

	this.check_in_days_list = ko.computed( function() {
		return days_list( self.check_in_days() );
	}, self );

	this.check_out_days_list = ko.computed( function() {
		return days_list( self.check_out_days() );
	}, self );

	function days_list( days ) {
		if ( days.length == 0 ) {
			return hb_text.no_days_selected;
		} else if ( days.length == 7 ) {
			return hb_text.any;
		} else {
			var days_list = [];
			var reordered_days = days.sort();
			for ( var i = 0; i < reordered_days.length; i++ ) {
				days_list[i] = days_short_name[reordered_days[i]];
			}
			return days_list.join( ', ' );
		}
	}

	this.select_all_check_in_days = function( rule ) {
		if ( rule ) {
			select_all_days( rule.check_in_days );
		}
	}

	this.unselect_all_check_in_days = function( rule ) {
		if ( rule ) {
			unselect_all_days( rule.check_in_days );
		}
	}

	this.select_all_check_out_days = function( rule ) {
		if ( rule ) {
			select_all_days( rule.check_out_days );
		}
	}

	this.unselect_all_check_out_days = function( rule ) {
		if ( rule ) {
			unselect_all_days( rule.check_out_days );
		}
	}

	function select_all_days( check_in_out ) {
		check_in_out( ['0', '1', '2', '3', '4', '5', '6'] );
	}

	function unselect_all_days( check_in_out ) {
		check_in_out( [] );
	}

	this.conditional_type_display = ko.computed( function() {
		return conditional_types[ self.conditional_type() ];
	}, self );

	this.conditional_type.subscribe( function( conditional_type ) {
		if ( conditional_type != 'compulsory' && conditional_type != 'comp_and_rate' ) {
			self.all_accom( true );
			self.all_seasons( true );
		}
	});
}

function RulesViewModel() {

	var self = this;
	var observable_rules = [];
	for ( var i = 0; i < rules.length; i++ ) {
		observable_rules.push( new Rule( false, rules[i].id, rules[i].name, rules[i].type, rules[i].check_in_days, rules[i].check_out_days, rules[i].minimum_stay, rules[i].maximum_stay, rules[i].accom, rules[i].all_accom, rules[i].seasons, rules[i].all_seasons, rules[i].conditional_type ) );
	}

	this.rules = ko.observableArray( observable_rules );

	ko.utils.extend( this, new HbSettings() );

	this.create_rule = function( rule_type ) {
		var name = '',
			check_in_days = '0,1,2,3,4,5,6',
			check_out_days = '0,1,2,3,4,5,6',
			minimum_stay = '',
			maximum_stay = '',
			accom = '',
			all_accom = 0,
			conditional_type = 'compulsory',
			seasons = '',
			all_seasons = 0;

		if ( rule_type == 'check_in_days' ) {
			check_in_days = '';
		} else if ( rule_type == 'check_out_days' ) {
			check_out_days = '';
		} else if ( rule_type == 'conditional' ) {
			name = hb_text.new_rule;
		}
		var css_class = 'hb-add-' + rule_type.replace( /_/g, '-' ),
			new_rule = new Rule( true, 0, name, rule_type, check_in_days, check_out_days, minimum_stay, maximum_stay, accom, all_accom, seasons, all_seasons, conditional_type );
		self.create_setting( new_rule, function( new_rule ) {
			self.rules.push( new_rule );
		}, css_class );
	}

	this.nb_rules = function( rule_type ) {
		var rules = self.rules(),
			nb_rules = 0;
		for ( var i = 0; i < rules.length; i++ ) {
			if ( rules[i].rule_type == rule_type ) {
				nb_rules++;
			}
		}
		return nb_rules;
	}

	this.remove = function( rule ) {
		var callback_function = function() {
			self.rules.remove( rule );
		}
		self.delete_setting( rule, callback_function );
	}

}

ko.applyBindings( new RulesViewModel() );
