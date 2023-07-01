'use strict';

function Option( brand_new, id, name, amount, amount_children, link, apply_to_type, choice_type, choices, accom, all_accom, quantity_max_option, quantity_max, quantity_max_child ) {
	OptionsAndFees.call( this, brand_new, 'option', id, name, amount, amount_children, apply_to_type, accom, all_accom );
	this.link = ko.observable( link );
	this.choice_type = ko.observable( choice_type );
	this.choices = ko.observableArray( choices );
	this.quantity_max_option = ko.observable( quantity_max_option );
	this.quantity_max = ko.observable( quantity_max );
	this.quantity_max_child = ko.observable( quantity_max_child );

	var self = this;

	this.choice_type_text = ko.computed( function() {
		if ( self.apply_to_type() == 'quantity' || self.apply_to_type() == 'quantity-per-day' ) {
			return '-';
		} else if ( self.choice_type() == 'multiple' ) {
			return hb_text.multiple_choice_yes;
		} else {
			return hb_text.multiple_choice_no;
		}
	});

	this.apply_to_types_options = ko.computed( function() {
		if ( self.link() == 'booking' ) {
			return hb_apply_to_types_booking;
		} else {
			return hb_apply_to_types;
		}
	});

	this.link_text = ko.computed( function() {
		return hb_text[ 'link_' + self.link() ];
	});

	this.link.subscribe( function( new_value ) {
		if ( new_value == 'booking' ) {
			this.all_accom( true );
		}
	}, this );

	this.apply_to_type.subscribe( function( new_value ) {
		if ( new_value == 'quantity' || new_value == 'quantity-per-day' ) {
			this.choice_type( 'single' );
		}
		for ( var i = 0; i < self.choices().length; i++ ) {
			self.choices()[ i ].apply_to_type( new_value );
		}
	}, this );

	this.revert = function( option ) {
		if ( option ) {
			self.name( option.name );
			self.amount( option.amount );
			self.amount_children( option.amount_children );
			self.link( option.link );
			self.apply_to_type( option.apply_to_type );
			self.choice_type( option.choice_type );
			self.accom( option.accom );
			self.all_accom( option.all_accom );
			self.quantity_max_option( option.quantity_max_option );
			self.quantity_max( option.quantity_max );
			self.quantity_max_child( option.quantity_max_child );
		}
	}

}

function OptionChoice( brand_new, id, option_id, name, amount, amount_children, apply_to_type ) {
	OptionsAndFees.call( this, brand_new, 'option_choice', id, name, amount, amount_children, apply_to_type );
	this.option_id = option_id;

	var self = this;

	this.revert = function( option_choice ) {
		if ( option_choice ) {
			self.name( option_choice.name );
			self.amount( option_choice.amount );
			self.amount_children( option_choice.amount_children );
		}
	}

}

function OptionsViewModel() {

	var self = this;

	var observable_options = [];
	for ( var i = 0; i < options.length; i++ ) {
		var observable_option_choices = [];
		for ( var j = 0; j < options[i].choices.length; j++ ) {
			observable_option_choices.push(
				new OptionChoice(
					false,
					options[i].choices[j].id,
					options[i].id,
					options[i].choices[j].name,
					options[i].choices[j].amount,
					options[i].choices[j].amount_children,
					options[i].apply_to_type
				)
			);
		}
		observable_options.push(
			new Option(
				false,
				options[i].id,
				options[i].name,
				options[i].amount,
				options[i].amount_children,
				options[i].link,
				options[i].apply_to_type,
				options[i].choice_type,
				observable_option_choices,
				options[i].accom,
				options[i].all_accom,
				options[i].quantity_max_option,
				options[i].quantity_max,
				options[i].quantity_max_child
			)
		);
	}

	ko.utils.extend( this, new HbSettings() );

	this.options = ko.observableArray( observable_options );

	this.create_option = function() {
		var new_option = new Option( true, 0, hb_text.new_option, '0', '0', 'accom', 'per-person', 'single', [], '', true, 'no', 0, 0 );
		self.create_setting( new_option, function( new_option ) {
			self.options.push( new_option );
		});
	}

	this.create_option_choice = function( option ) {
		var new_option_choice = new OptionChoice( true, 0, option.id, hb_text.new_option_choice, '0', '0', option.apply_to_type() );
		self.create_child_setting( option, new_option_choice, function( new_option_choice ) {
			option.choices.push( new_option_choice );
		});
	}

	this.remove = function( setting, event, option ) {
		if ( setting.type == 'option' ) {
			var callback_function = function() {
				self.options.remove( setting );
			}
		} else { // option_choice
			var callback_function = function() {
				option.choices.remove( setting );
			}
		}
		self.delete_setting( setting, callback_function );
	}

}

ko.applyBindings( new OptionsViewModel() );