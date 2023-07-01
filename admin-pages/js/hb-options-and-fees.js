'use strict';
function OptionsAndFees( brand_new, type, id, name, amount, amount_children, apply_to_type, accom, all_accom ) {
	HbSetting.call(this, brand_new, type, id, name);
	Accom.call( this, accom, all_accom );
	this.amount = ko.observable( amount );
	this.amount_children = ko.observable( amount_children );
	this.apply_to_type = ko.observable( apply_to_type );
	var self = this;
	this.amount_text = ko.computed( function() {
		amount = self.amount();
		amount_children = self.amount_children();
		if (
			( self.apply_to_type() == 'accom-percentage' ) ||
			( self.apply_to_type() == 'extras-percentage' ) ||
			( self.apply_to_type() == 'global-percentage' )
		) {
			if ( ! amount ) {
				return '';
			}
			if ( amount % 1 == 0 ) {
				return parseFloat( amount ).toFixed( 0 ) + '%';
			} else {
				return parseFloat( amount ).toFixed( 2 ) + '%';
			}
		} else if ( self.apply_to_type() == 'per-person' || self.apply_to_type() == 'per-person-per-day' ) {
			var formatted_amount_adults,
				formatted_amount_children;
			if ( ! amount ) {
				formatted_amount_adults = hb_format_price( 0 );
			} else {
				formatted_amount_adults = hb_format_price( amount );
			}
			if ( ! amount_children ) {
				formatted_amount_children = hb_format_price( 0 );
			} else {
				formatted_amount_children = hb_format_price( amount_children );
			}
			return hb_text.adults + ' '+ formatted_amount_adults + '<br/>' + hb_text.children + ' ' + formatted_amount_children;
		} else {
			if ( ! amount ) {
				return '';
			} else {
				return hb_format_price( amount );
			}
		}
	});
	this.apply_to_type_text = ko.computed( function() {
		for ( var i = 0; i < hb_apply_to_types.length; i++ ) {
			if ( hb_apply_to_types[i]['option_value'] == self.apply_to_type() ) {
				return hb_apply_to_types[i]['option_text'];
			}
		}
		if ( typeof hb_apply_to_types_booking !== 'undefined' ) {
			for ( var i = 0; i < hb_apply_to_types_booking.length; i++ ) {
				if ( hb_apply_to_types_booking[i]['option_value'] == self.apply_to_type() ) {
					return hb_apply_to_types_booking[i]['option_text'];
				}
			}
		}
	});
	this.is_valid = function( setting ) {
		if ( setting ) {
			return hb_is_valid_price( setting.amount() );
		}
	}
}