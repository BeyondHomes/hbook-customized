'use strict';

function Fee( brand_new, id, name, amount, amount_children, apply_to_type, accom, all_accom, global, accom_price_per_person_per_night, include_in_price, minimum_amount, maximum_amount, multiply_per ) {
	if ( amount != '' && amount % 1 == 0 ) {
		amount = parseFloat( amount ).toFixed( 0 );
	}
	if ( amount_children != '' && amount_children % 1 == 0 ) {
		amount_children = parseFloat( amount_children ).toFixed( 0 );
	}
	if ( minimum_amount == 0 ) {
		minimum_amount = '';
	} else if ( minimum_amount % 1 == 0 ) {
		minimum_amount = parseFloat( minimum_amount ).toFixed( 0 );
	}
	if ( maximum_amount == 0 ) {
		maximum_amount = '';
	} else if ( maximum_amount % 1 == 0 ) {
		maximum_amount = parseFloat( maximum_amount ).toFixed( 0 );
	}

	OptionsAndFees.call( this, brand_new, 'fee', id, name, amount, amount_children, apply_to_type, accom, all_accom );

	this.global = ko.observable();
	if ( typeof global == 'string' ) { // wp-localize-script turns all values to string
		global = parseInt( global );
	}
	if ( global ) {
		this.global( true );
	} else {
		this.global( false );
	}

	this.include_in_price = ko.observable( include_in_price );

	this.accom_price_per_person_per_night = ko.observable();
	if ( typeof accom_price_per_person_per_night == 'string' ) { // wp-localize-script turns all values to string
		accom_price_per_person_per_night = parseInt( accom_price_per_person_per_night );
	}
	if ( accom_price_per_person_per_night ) {
		this.accom_price_per_person_per_night( true );
	} else {
		this.accom_price_per_person_per_night( false );
	}

	this.amount_type = ko.observable();
	if (
		( apply_to_type == 'accom-percentage' ) ||
		( apply_to_type == 'extras-percentage' ) ||
		( apply_to_type == 'global-percentage' )
	) {
		this.amount_type( 'percent' );
	} else {
		this.amount_type( 'fixed' );
	}

	this.minimum_amount = ko.observable( minimum_amount );

	this.maximum_amount = ko.observable( maximum_amount );

	this.multiply_per = ko.observableArray();
	if ( multiply_per != '' ) {
		multiply_per = multiply_per.split( ',' );
		this.multiply_per( multiply_per );
	}

	this.amount_type.subscribe( function ( new_amount_type ) {
		if ( new_amount_type == 'fixed' ) {
			this.apply_to_type( 'per-person' );
		} else {
			this.apply_to_type( 'accom-percentage' );
		}
	}, this );

	this.apply_to_type.subscribe( function( new_apply_to_type ) {
		if (
			( new_apply_to_type == 'global-percentage' ) ||
			( new_apply_to_type == 'global-fixed' ) ||
			( new_apply_to_type == 'extras-percentage' )
		) {
			this.global( true );
			this.all_accom( true );
		} else {
			this.global( false );
		}
		if ( new_apply_to_type == 'global-fixed' ) {
			this.include_in_price( '0' );
		}
		if ( new_apply_to_type != 'accom-percentage' ) {
			this.accom_price_per_person_per_night( false );
		}
	}, this );

	this.accom_price_per_person_per_night.subscribe( function( new_accom_price_per_person_per_night ) {
		if ( new_accom_price_per_person_per_night === false ) {
			this.maximum_amount( '' );
			this.minimum_amount( '' );
			this.multiply_per( [] );
		} else {
			if ( this.include_in_price() == '2' ) {
				this.include_in_price( '1' );
			}
		}
	}, this );

	this.multiply_per.subscribe( function( new_multiply_per ) {
		if (
			( new_multiply_per.indexOf( 'adults_children' ) != -1 ) &&
			(
				( new_multiply_per.indexOf( 'adults' ) != -1 ) ||
				( new_multiply_per.indexOf( 'children' ) != -1 )
			)
		) {
			if ( new_multiply_per.indexOf( 'nb_nights' ) != -1 ) {
				this.multiply_per( [ 'nb_nights', 'adults_children' ] )
			} else {
				this.multiply_per( [ 'adults_children' ] )
			}
		}
	}, this );

	var self = this;

	this.amount_limits_text = ko.computed( function() {
		var min_amount_text = '';
		var max_amount_text = '';

		if ( self.minimum_amount() != 0 ) {
			min_amount_text = hb_text.min_amount + ' ' + hb_format_price( self.minimum_amount() );
		}
		if ( self.maximum_amount() != 0 ) {
			max_amount_text = hb_text.max_amount + ' ' + hb_format_price( self.maximum_amount() );
		}
		if ( min_amount_text && max_amount_text ) {
			min_amount_text += '<br/>';
		}
		if ( min_amount_text || max_amount_text ) {
			return min_amount_text + max_amount_text;
		} else {
			return '';
		}
	});

	this.multiply_per_text = ko.computed( function() {
		var returned_text = '';

		if ( self.accom_price_per_person_per_night() ) {
			for ( var i = 0; i < self.multiply_per().length; i++ ) {
				if ( returned_text ) {
					returned_text += '<br/>';
				}
				returned_text += 'x ' + hb_text[ 'multiply_per_' + self.multiply_per()[ i ] ];
			}
			if ( returned_text ) {
				returned_text = '---<br/>' + returned_text;
			}
		}
		return returned_text;
	});

	this.include_in_price_text = ko.computed( function() {
		if ( self.include_in_price() == 1 ) {
			if ( ( self.amount_type() == 'fixed' ) || ( self.apply_to_type() == 'accom-percentage' ) ) {
				return hb_text.added_to_accom_price;
			} else if ( self.apply_to_type() == 'extras-percentage' ) {
				return hb_text.added_to_extras_price;
			} else {
				return hb_text.added_to_prices;
			}
		} else if ( self.include_in_price() == 2 ) {
			if ( ( self.amount_type() == 'fixed' ) || ( self.apply_to_type() == 'accom-percentage' ) ) {
				return hb_text.included_in_accom_price;
			} else if ( self.apply_to_type() == 'extras-percentage' ) {
				return hb_text.included_in_extras_price;
			} else {
				return hb_text.included_in_prices;
			}
		} else {
			return hb_text.added_to_final_price;
		}
	});

	this.added_to_price_label = ko.computed( function() {
		if ( self.amount_type() == 'fixed' ) {
			return hb_text.added_to_accom_price;
		}
		if ( self.apply_to_type() == 'accom-percentage' ) {
			return hb_text.added_to_accom_price;
		}
		if ( self.apply_to_type() == 'extras-percentage' ) {
			return hb_text.added_to_extras_price;
		}
		return hb_text.added_to_prices;
	});

	this.included_in_price_label = ko.computed( function() {
		if ( self.amount_type() == 'fixed' ) {
			return hb_text.included_in_accom_price;
		}
		if ( self.apply_to_type() == 'accom-percentage' ) {
			return hb_text.included_in_accom_price;
		}
		if ( self.apply_to_type() == 'extras-percentage' ) {
			return hb_text.included_in_extras_price;
		}
		return hb_text.included_in_prices;
	});

	this.revert = function( fee ) {
		if ( fee ) {
			self.name( fee.name );
			self.amount( fee.amount );
			self.amount_children( fee.amount_children );
			self.amount_type( fee.amount_type );
			self.apply_to_type( fee.apply_to_type );
			self.accom( fee.accom );
			self.all_accom( fee.all_accom );
			self.global( fee.global );
			self.accom_price_per_person_per_night( fee.accom_price_per_person_per_night );
			self.include_in_price( fee.include_in_price );
			self.minimum_amount( fee.minimum_amount );
			self.maximum_amount( fee.maximum_amount );
			self.multiply_per( fee.multiply_per );
		}
	}
}

function FeesViewModel() {

	var self = this;
	var observable_fees = [];
	for ( var i = 0; i < fees.length; i++ ) {
		observable_fees.push(
			new Fee(
				false,
				fees[i].id,
				fees[i].name,
				fees[i].amount,
				fees[i].amount_children,
				fees[i].apply_to_type,
				fees[i].accom,
				fees[i].all_accom,
				fees[i].global,
				fees[i].accom_price_per_person_per_night,
				fees[i].include_in_price,
				fees[i].minimum_amount,
				fees[i].maximum_amount,
				fees[i].multiply_per
			)
		);
	}

	ko.utils.extend( this, new HbSettings() );

	this.fees = ko.observableArray( observable_fees );

	this.create_fee = function() {
		var new_fee = new Fee(
			true,
			0,
			hb_text.new_fee,
			0,
			0,
			'per-person',
			'',
			true,
			false,
			false,
			'1',
			0,
			0,
			''
		);
		self.create_setting( new_fee, function( new_fee ) {
			self.fees.push( new_fee );
		});
	}

	this.remove = function( fee ) {
		var callback_function = function() {
			self.fees.remove( fee );
		}
		self.delete_setting( fee, callback_function );
	}

}

ko.applyBindings( new FeesViewModel() );