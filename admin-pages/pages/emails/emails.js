'use strict';

function EmailTmpl( brand_new, id, name, to_address, reply_to_address, from_address, bcc_address, subject, message, format, media_attachments, lang, sending_type, action, schedules, resa_status, resa_payment_status, accom, multiple_accom, all_accom ) {
	HbSetting.call(this, brand_new, 'email_template', id, name);
	Accom.call( this, accom, all_accom );

	this.to_address = ko.observable( to_address );
	this.reply_to_address = ko.observable( reply_to_address );
	this.from_address = ko.observable( from_address );
	this.bcc_address = ko.observable( bcc_address );
	this.subject = ko.observable( subject );
	this.message = ko.observable( message );
	this.format = ko.observable( format );
	this.media_attachments = ko.observable( media_attachments );
	this.lang = ko.observable( lang );
	this.sending_type = ko.observable( sending_type );
	if ( ! sending_type ) {
		this.sending_type( 'event' );
	}
	this.action = ko.observableArray();
	this.resa_status = ko.observableArray();
	this.resa_payment_status = ko.observableArray();
	if ( action ) {
		this.action( action.split( ',' ) );
	}
	if ( resa_status ) {
		this.resa_status( resa_status.split( ',' ) );
	}
	if ( resa_payment_status ) {
		this.resa_payment_status( resa_payment_status.split( ',' ) );
	}
	this.multiple_accom = ko.observable( false );
	if ( multiple_accom == 1 ) {
		this.multiple_accom( true );
	}
	this.editing_schedule = ko.observable( '' );
	this.schedules = ko.observableArray();
	if ( schedules ) {
		this.schedules( schedules.split( ',' ) );
	}
	this.edit_schedule_days = ko.observable();
	this.edit_schedule_position = ko.observable( 'before' );
	this.edit_schedule_check_in_out = ko.observable( 'in' );
	this.edit_schedule_only_before_out = ko.observable( true );
	this.edit_schedule_only_after_in = ko.observable( true );
	var self = this;

	this.to_address_html = ko.computed( function() {
		return address_processing( self.to_address() );
	});

	this.reply_to_address_html = ko.computed( function() {
		return complete_address_processing( self.reply_to_address() );
	});

	this.from_address_html = ko.computed( function() {
		return complete_address_processing( self.from_address() );
	});

	this.bcc_address_html = ko.computed( function() {
		return address_processing( self.bcc_address() );
	});

	function address_processing( address ) {
		if ( address == '' ) {
			return '';
		}
		if ( address.indexOf( ';' ) != -1 ) {
			return address.replace( /</g, '&lt;' ).replace( />/g, '&gt;' ) + '<br/><b>' + hb_text.invalid_email_address + ' ' + hb_text.invalid_multiple_address + '</b>';
		}

		address = address.replace( /</g, '&lt;' ).replace( />/g, '&gt;' );

		var nb_at = address.split( '@' ).length - 1,
			nb_bracket = address.split( '[' ).length - 1,
			nb_comma = address.split( ',' ).length - 1;

		if ( nb_bracket == 0 && nb_at == 0 ) {
			return address + '<br/><b>' + hb_text.invalid_email_address + '</b>';
		}
		if ( nb_at > 1 && nb_comma != nb_at - 1 + nb_bracket ) {
			return address + '<br/><b>' + hb_text.invalid_email_address + ' ' + hb_text.invalid_multiple_address + '</b>';
		}
		return address;
	}

	function complete_address_processing( address ) {
		if ( address == '' ) {
			return '';
		} else if ( address.indexOf( '<' ) == -1 || address.indexOf( '>' ) == -1 ) {
			var error_text;
			error_text = hb_text.invalid_complete_address.replace( '<', '&lt;' );
			error_text = error_text.replace( '>', '&gt;' );
			return address.replace( /</g, '&lt;' ).replace( />/g, '&gt;' ) + '<br/><b>' + hb_text.invalid_email_address + ' ' + error_text + '</b>';
		} else if ( ( address.indexOf( '[' ) < 0 ) && ( address.indexOf( '@' ) == -1 ) ) {
			return address.replace( /</g, '&lt;' ).replace( />/g, '&gt;' ) + '<br/><b>' + hb_text.invalid_email_address + '</b>';
		}
		return address.replace( /</g, '&lt;' ).replace( />/g, '&gt;' );
	}

	this.media_attachments_list = ko.computed( function() {
		if ( self.media_attachments() ) {
			return hb_media_titles[ self.media_attachments() ];
		} else {
			return '';
		}
	});

	this.message_html = ko.computed( function() {
		var msg = self.message(),
			long_msg = false;
		if ( msg.length > 50 ) {
			long_msg = true;
			msg = msg.substr( 0, 50 );
		}
		msg = msg.replace( /</g, '&lt;' ).replace( />/g, '&gt;' );
		if ( long_msg ) {
			msg = msg + '<b>...</b>';
		}
		msg = msg.replace( /(?:\r\n|\r|\n)/g, '<br/>' );
		if ( msg ) {
			msg = '<b>' + hb_text.message + '</b><br/>' + msg + '<br/><br/>';
		}
		msg += '<b>' + hb_text.format + '</b> ';
		msg += self.format();
		if ( self.media_attachments_list() ) {
			msg += '<br/><b>' + hb_text.attachments + '</b> ';
			msg += self.media_attachments_list();
		}
		return msg;
	});

	this.remove_media_attachment = function( email_tmpl ) {
		if ( email_tmpl && confirm( hb_text.remove_all_attachments ) ) {
			email_tmpl.media_attachments( '' );
		}
	}

	this.actions_text = ko.computed( function() {
		var actions = [];
		for ( var i = 0; i < hb_email_actions.length; i++ ) {
			if ( self.action.indexOf( hb_email_actions[i]['action_value'] ) != -1 ) {
				actions.push( hb_email_actions[i]['action_text'] );
			}
		}
		if ( actions.length ) {
			return actions.join( ', ' );
		} else {
			return '<b class="hb-template-email-inactive-reason">' + hb_text.email_never_sent_no_actions + '</b>';
		}
	});

	this.lang_text = ko.computed( function() {
		for ( var i = 0; i < hb_email_langs.length; i++ ) {
			if ( hb_email_langs[i]['lang_value'] == self.lang() ) {
				return hb_email_langs[i]['lang_name'];
			}
		}
	});

	this.resa_status_text = ko.computed( function() {
		var resa_status = [];
		jQuery.each( hb_resa_status, function( id, text ) {
			if ( self.resa_status.indexOf( id ) != -1 ) {
				resa_status.push( text );
			}
		});
		if ( resa_status.length ) {
			return resa_status.join( ', ' );
		} else {
			return '<b class="hb-template-email-inactive-reason">' + hb_text.email_never_sent_no_resa_status + '</b>';
		}
	});

	this.resa_payment_status_text = ko.computed( function() {
		var resa_payment_status = [];
		jQuery.each( hb_resa_payment_status, function( id, text ) {
			if ( self.resa_payment_status.indexOf( id ) != -1 ) {
				resa_payment_status.push( text );
			}
		});
		if ( resa_payment_status.length ) {
			return resa_payment_status.join( ', ' );
		} else {
			return '<b class="hb-template-email-inactive-reason">' + hb_text.email_never_sent_no_resa_payment_status + '</b>';
		}
	});

	this.accom_list_for_email = ko.computed( function() {
		if ( self.all_accom() ) {
			return hb_text.all;
		}
		if ( ( self.accom().length == 0 ) && ! self.multiple_accom() ) {
			return '<b class="hb-template-email-inactive-reason">' + hb_text.email_never_sent_no_accom + '</b>';
		}
		var accom_name_list = [];
		var reordered_accom = self.accom().sort();
		for ( var i = 0; i < reordered_accom.length; i++ ) {
			accom_name_list[i] = accom_list[reordered_accom[i]];
		}
		return accom_name_list.join( ', ' );
	}, self );

	this.schedule_text = function( schedule ) {
		if ( schedule ) {
			schedule = schedule.split( '-' );
			var returned_text = schedule[0] + ' ';
			if ( schedule[0] == 0 ) {
				if ( schedule[1] == 'in' ) {
					return hb_text.schedule_check_in_day;
				} else {
					return hb_text.schedule_check_out_day;
				}
			} else if ( schedule[0] == 1 ) {
				returned_text += hb_text.schedule_day;
			} else {
				returned_text += hb_text.schedule_days;
			}
			returned_text += ' ' + hb_text[ 'schedule_' + schedule[1] ];
			returned_text += ' ' + hb_text[ 'schedule_' + schedule[2] ];
			return returned_text;
		}
	}

	this.confirm_edit_schedule = function( schedule ) {
		if ( schedule ) {
			var new_schedule = '';
			new_schedule += self.edit_schedule_days() + '-';
			if ( self.edit_schedule_days() != 0 ) {
				new_schedule += self.edit_schedule_position() + '-';
			}
			new_schedule += self.edit_schedule_check_in_out();
			if (
				(
					( self.edit_schedule_check_in_out() == 'in' ) &&
					( self.edit_schedule_position() == 'after' ) &&
					( ! self.edit_schedule_only_before_out() )
				) ||
				(
					( self.edit_schedule_check_in_out() == 'out' ) &&
					( self.edit_schedule_position() == 'before' ) &&
					( ! self.edit_schedule_only_after_in() )
				)
			) {
				new_schedule += '-always';
			}
			if ( self.edit_schedule_days() != parseInt( self.edit_schedule_days() ) ) {
				alert( hb_text.schedule_invalid_days_number );
			} else if (
				( self.editing_schedule() != 'adding' ) &&
				( self.schedules()[ self.editing_schedule() ] == new_schedule )
			) {
				self.editing_schedule( '' );
			} else if ( self.schedules.indexOf( new_schedule ) >= 0 ) {
				alert( hb_text.schedule_already_exists );
			} else {
				if ( ( self.editing_schedule() == 'adding' ) || ( self.schedules().length == 0 ) ) {
					self.schedules.push( new_schedule );
				} else {
					var old_schedule = self.schedules()[ self.editing_schedule() ];
					self.schedules.replace( old_schedule, new_schedule );
				}
				self.editing_schedule( '' );
			}
		}
	}

	this.cancel_edit_schedule = function( schedule ) {
		if ( schedule ) {
			self.editing_schedule( '' );
		}
	}

	this.edit_schedule = function( schedule, event, index ) {
		if ( schedule ) {
			var schedule = self.schedules()[ index ].split( '-' );
			self.edit_schedule_days( schedule[0] );
			self.edit_schedule_position( schedule[1] );
			self.edit_schedule_check_in_out( schedule[2] );
			self.edit_schedule_only_before_out( true );
			self.edit_schedule_only_after_in( true );
			if ( ( schedule.length == 4 ) && ( schedule[3] == 'always' ) ) {
				if ( schedule[2] == 'in' ) {
					self.edit_schedule_only_before_out( false );
				} else {
					self.edit_schedule_only_after_in( false );
				}
			}
			self.editing_schedule( index );
		}
	}

	this.delete_schedule = function( schedule, event, index ) {
		if ( schedule && confirm( hb_text.delete_schedule ) ) {
			self.schedules.splice( index, 1 );
		}
	}

	this.add_schedule = function( schedule ) {
		if ( schedule ) {
			self.edit_schedule_days( '' );
			self.edit_schedule_position( 'before' );
			self.edit_schedule_check_in_out( 'in' );
			self.edit_schedule_only_before_out( true );
			self.edit_schedule_only_after_in( true );
			self.editing_schedule( 'adding' );
		}
	}

	this.select_all_resa_status = function( email_tmpl ) {
		if ( email_tmpl ) {
			self.resa_status( ['new', 'pending', 'confirmed'] );
		}
	}

	this.unselect_all_resa_status = function( email_tmpl ) {
		if ( email_tmpl ) {
			self.resa_status.removeAll();
		}
	}

	this.select_all_resa_payment_status = function( email_tmpl ) {
		if ( email_tmpl ) {
			self.resa_payment_status( ['paid', 'not_fully_paid', 'bond_not_paid', 'unpaid'] );
		}
	}

	this.unselect_all_resa_payment_status = function( email_tmpl) {
		if ( email_tmpl ) {
			self.resa_payment_status.removeAll();
		}
	}

	this.revert = function( email_tmpl ) {
		if ( email_tmpl ) {
			self.name( email_tmpl.name );
			self.to_address( email_tmpl.to_address );
			self.from_address( email_tmpl.from_address );
			self.subject( email_tmpl.subject );
			self.message( email_tmpl.message );
			self.format( email_tmpl.format );
			self.media_attachments( email_tmpl.media_attachments );
			self.accom( email_tmpl.accom );
			self.lang( email_tmpl.lang );
			self.action( email_tmpl.action );
			self.sending_type( email_tmpl.sending_type );
			self.schedules( email_tmpl.schedules );
			self.resa_status( email_tmpl.resa_status );
			self.resa_payment_status( email_tmpl.resa_payment_status );
			self.multiple_accom( email_tmpl.multiple_accom );
		}
	}

	this.sending_type.subscribe( function( value ) {
		if ( value ) {
			if ( value == 'scheduled' ) {
				self.action( ['new_resa', 'new_resa_admin', 'confirmation_resa', 'cancellation_resa'] );
			} else {
				self.action( ['new_resa'] );
			}
		}
	});

	this.before_save = function( email_tmpl ) {
		if ( email_tmpl ) {
			if ( email_tmpl.editing_schedule() == 'adding' ) {
				var new_schedule = '';
				new_schedule += self.edit_schedule_days() + '-';
				if ( self.edit_schedule_days() != 0 ) {
					new_schedule += self.edit_schedule_position() + '-';
				}
				new_schedule += self.edit_schedule_check_in_out();
				if (
					( self.edit_schedule_days() != parseInt( self.edit_schedule_days() ) ) ||
					( self.schedules.indexOf( new_schedule ) >= 0 )
				) {
					return;
				} else {
					self.schedules.push( new_schedule );
					self.editing_schedule( '' );
				}
			}
		}
	}
}

function EmailTmplViewModel() {
	var self = this;

	var observable_email_tmpls = [];
	for ( var i = 0; i < email_tmpls.length; i++ ) {
		observable_email_tmpls.push(
			new EmailTmpl(
				false,
				email_tmpls[i].id,
				email_tmpls[i].name,
				email_tmpls[i].to_address,
				email_tmpls[i].reply_to_address,
				email_tmpls[i].from_address,
				email_tmpls[i].bcc_address,
				email_tmpls[i].subject,
				email_tmpls[i].message,
				email_tmpls[i].format,
				email_tmpls[i].media_attachments,
				email_tmpls[i].lang,
				email_tmpls[i].sending_type,
				email_tmpls[i].action,
				email_tmpls[i].schedules,
				email_tmpls[i].resa_status,
				email_tmpls[i].resa_payment_status,
				email_tmpls[i].accom,
				email_tmpls[i].multiple_accom,
				email_tmpls[i].all_accom
			)
		);
	}

	this.email_tmpls = ko.observableArray( observable_email_tmpls );

	ko.utils.extend( this, new HbSettings() );

	this.create_email_tmpl = function() {
								//EmailTmpl( brand_new, id, name, to_address, reply_to_address, from_address, bcc_address, subject, message, format, media_attachments, lang, sending_type, action, schedules, resa_status, resa_payment_status, accom, multiple_accom, all_accom );
		var new_email_tmpl = new EmailTmpl( true, 0, hb_text.new_email_tmpl, '', '', '', '', '', '', 'TEXT', '', 'all', 'event', 'new_resa', '', 'new,pending,confirmed', 'paid,not_fully_paid,bond_not_paid,unpaid', '', 0, 0 );
		self.create_setting( new_email_tmpl, function( new_email_tmpl ) {
			self.email_tmpls.push( new_email_tmpl );
		});
	}

	this.remove = function( setting ) {
		var callback_function = function() {
			self.email_tmpls.remove( setting );
		}
		self.delete_setting( setting, callback_function );
	}
}

ko.applyBindings( new EmailTmplViewModel() );

jQuery( document ).ready( function( $ ) {
	$( '#hb-delete-email-logs-submit' ).on( 'click', function() {
		$( this ).blur();
		if ( confirm( hb_text.confirm_delete_email_logs ) ) {
			$( '#hb-delete-email-logs-form' ).submit();
		}
		return false;
	});
});