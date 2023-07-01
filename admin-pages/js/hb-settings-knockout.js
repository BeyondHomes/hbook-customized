'use strict';

function HbSetting( brand_new, type, id, name ) {
	this.brand_new = brand_new;
	this.saving = ko.observable( false );
	this.deleting = ko.observable( false );
	this.adding_child = ko.observable( false );
	this.type = type;
	this.id = id;
	if ( name ) {
		this.name = ko.observable( name );
	} else {
		this.name = ko.observable( '' );
	}
	var self = this;
	this.save_text = ko.computed( function() {
		if ( self.saving() ) {
			return hb_text.saving;
		} else {
			return hb_text.save;
		}
	});
}

function Accom( accom, all_accom ) {
	if ( accom ) {
		this.accom = ko.observableArray( accom.split( ',' ) );
	} else {
		this.accom = ko.observableArray();
	}
	this.all_accom = ko.observable();

	var self = this;

	var all_accom_ids = [];
	for ( var key in accom_list ) {
		all_accom_ids.push( key );
	}

	this.all_accom.subscribe( function( value ) {
		if ( value ) {
			self.accom.removeAll();
			for ( var i = 0; i < all_accom_ids.length; i++ ) {
				self.accom.push( all_accom_ids[i] );
			}
		}
	});

	if ( typeof all_accom == 'string' ) { // wp-localize-script turns all values to string
		all_accom = parseInt( all_accom );
	}
	if ( all_accom ) {
		this.all_accom( true );
	} else {
		this.all_accom( false );
	}

	this.accom_list = ko.computed( function() {
		if ( self.all_accom() ) {
			return hb_text.all;
		}
		if ( self.accom().length == 0 ) {
			return hb_text.no_accom_selected;
		}
		var accom_name_list = [];
		var reordered_accom = self.accom().sort();
		for ( var i = 0; i < reordered_accom.length; i++ ) {
			accom_name_list[i] = accom_list[reordered_accom[i]];
		}
		return accom_name_list.join( ', ' );
	}, self );

	this.select_all_accom = function( setting ) {
		if ( setting ) {
			self.accom.removeAll();
			for ( var i = 0; i < all_accom_ids.length; i++ ) {
				self.accom.push( all_accom_ids[i] );
			}
		}
	}

	this.unselect_all_accom = function( setting ) {
		if ( setting ) {
			self.all_accom( false );
			self.accom.removeAll();
		}
	}

}

function HbSeasons( seasons, all_seasons ) {
	var self = this;

	if ( seasons ) {
		this.seasons = ko.observableArray( seasons.split( ',' ) );
	} else {
		this.seasons = ko.observableArray();
	}

	if ( typeof all_seasons == 'string' ) { // wp-localize-script turns all values to string
		all_seasons = parseInt( all_seasons );
	}
	if ( all_seasons ) {
		this.all_seasons = ko.observable( true );
	} else {
		this.all_seasons = ko.observable( false );
	}

	this.seasons_list = ko.computed( function() {
		if ( self.all_seasons() ) {
			return hb_text.all;
		}
		if ( self.seasons().length == 0 ) {
			return hb_text.no_seasons_selected;
		}
		var seasons_name_list = [];
		var reordered_seasons = self.seasons().sort();
		for ( var i = 0; i < reordered_seasons.length; i++ ) {
			seasons_name_list[i] = seasons_list[reordered_seasons[i]];
		}
		return seasons_name_list.join( ', ' );
	}, self );

	this.unselect_all_seasons = function( value ) {
		if ( value ) {
			self.all_seasons( false );
			self.seasons.removeAll();
		}
	}

	var all_seasons_ids = [];
	for ( var key in seasons_list ) {
		all_seasons_ids.push( key );
	}

	this.all_seasons.subscribe( function( value ) {
		if ( value ) {
			self.seasons.removeAll();
			for ( var i = 0; i < all_seasons_ids.length; i++ ) {
				self.seasons.push( all_seasons_ids[i] );
			}
		}
	});

	this.unselect_all_seasons = function( value ) {
		if ( value ) {
			self.all_seasons( false );
			self.seasons.removeAll();
		}
	}
}

function HbSettings() {

	var self = this;
	var saved_js = null;
	var saved_setting = null;

	this.selected_setting = ko.observable( false );

	this.settings = [];

	this.edit_setting = function( setting ) {
	    console.log('tttttttttttttt')
		if ( saved_setting ) {
			saved_setting.brand_new = false;
			saved_setting.revert( saved_js );
		}
		saved_setting = setting;
		saved_js = ko.toJS( setting );
		self.selected_setting( setting );
	};

	this.cancel_edit_setting = function( setting ) {
		setting.revert( saved_js );
		self.selected_setting( false );
		saved_setting = null;
		saved_js = null;
	};

	this.template_to_use = function( setting ) {
		return self.selected_setting() === setting ? 'edit_tmpl' : 'text_tmpl';
	};

	this.child_template_to_use = function( setting ) {
		return self.selected_setting() === setting ? 'child_edit_tmpl' : 'child_text_tmpl';
	};

	this.rule_template_to_use = function( setting, rule_type ) {
		if ( setting.rule_type == rule_type ) {
			return self.selected_setting() === setting ? setting.rule_type + '_rule_edit_tmpl' : setting.rule_type + '_rule_text_tmpl';
		} else {
			return 'empty_tmpl';
		}
	}

	this.rate_template_to_use = function( setting, rate_type ) {
		if ( setting.rate_type == rate_type ) {
			return self.selected_setting() === setting ? 'edit_tmpl' : 'text_tmpl';
		} else {
			return 'empty_tmpl';
		}
	}

	this.discount_template_to_use = function( setting ) {
		return self.selected_setting() === setting ? 'discount_edit_tmpl' : 'discount_text_tmpl';
	}

	this.coupon_template_to_use = function( setting ) {
		return self.selected_setting() === setting ? 'coupon_edit_tmpl' : 'coupon_text_tmpl';
	}

	this.create_setting = function( setting, add_to_observable, spinner_class ) {
		if ( spinner_class ) {
			spinner_class = '.' + spinner_class;
		} else {
			spinner_class = '';
		}
		jQuery( '.hb-add-new.spinner' + spinner_class ).css( 'visibility', 'visible' );
		ajax_update_db( 'create', setting, function( id ) {
			jQuery( '.hb-add-new.spinner' ).css( 'visibility', 'hidden' );
			setting.id = id.trim();
			add_to_observable( setting );
			self.edit_setting( setting );
			jQuery( '.add-new-h2, .add-new-below' ).blur();
		}, function( ajax_return ) {
			jQuery( '.hb-add-new.spinner' ).css( 'visibility', 'hidden' );
			alert( ajax_return );
		});
	}

	this.create_child_setting = function( parent_setting, child_setting, add_to_observable ) {
		parent_setting.adding_child( true );
		ajax_update_db( 'create', child_setting, function( id ) {
			parent_setting.adding_child( false );
			child_setting.id = id;
			add_to_observable( child_setting );
			self.edit_setting( child_setting );
		}, function( ajax_return ) {
			parent_setting.adding_child( false );
			alert( ajax_return );
		});
	}

	this.save_setting = function( setting ) {
		if ( typeof setting.is_valid == 'function' ) {
			if ( ! setting.is_valid( setting ) ) {
				return;
			}
		}
		if ( typeof setting.before_save == 'function' ) {
			setting.before_save( setting );
		}
		setting.saving( true );
		ajax_update_db( 'update', setting, function() {
			setting.saving( false );
			saved_setting = null;
			saved_js = null;
			setting.brand_new = false;
			self.selected_setting( false );
		}, function( ajax_return ) {
			setting.saving( false );
			alert( ajax_return );
		});
	}

	this.delete_setting = function( setting, remove_from_observable ) {
		var delete_setting_text = hb_text.confirm_delete_default;
		if ( setting.name() != '' ) {
			delete_setting_text = hb_text.confirm_delete.replace( '%setting_name', setting.name() );
		}
		if ( confirm( delete_setting_text ) ) {
			setting.deleting( true );
			ajax_update_db( 'delete', setting, function() {
				saved_setting = null;
				saved_js = null;
				self.selected_setting( false );
				remove_from_observable();
			}, function( ajax_return ) {
				setting.deleting( false );
				alert( ajax_return );
			});
		}
	}

	function ajax_update_db( action, object, callback_function_ok, callback_function_error ) {
		jQuery.ajax({
			url: ajaxurl,
			type: 'POST',
			timeout: hb_ajax_settings.timeout,
			data: {
				'action': 'hb_update_db',
				'db_action': action,
				'nonce': jQuery( '#hb_nonce_update_db' ).val(),
				'object': object,
			},
			success: function( ajax_return ) {
				if ( jQuery.isNumeric( ajax_return ) ) {
					callback_function_ok( ajax_return );
				} else {
					callback_function_error( ajax_return );
				}
			},
			error: function( jqXHR, textStatus, errorThrown ) {
				callback_function_error( 'Connection error: ' + textStatus + ' (' + errorThrown + ')' );
			}
		});
	}

	this.hide_setting = function( elem ) {
		if ( elem.nodeType === 1 ) {
			jQuery(elem).addClass( 'hb-setting-deleted' ).fadeOut( 'slow', function() {
				jQuery(elem).remove();
			});
		}
	}

	window.onbeforeunload = function() {
		if ( saved_setting ) {
			return hb_text.unsaved_warning;
		}
	}

}