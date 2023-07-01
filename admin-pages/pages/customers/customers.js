jQuery( document ).ready( function( $ ) {
	'use strict';

	hb_section_toggle( 'export-customers' );

	$( '#hb-export-customers-select-all' ).on( 'click', function() {
		$( this ).blur();
		$( '#hb-export-customers-form input[type="checkbox"]' ).prop( 'checked', true );
		return false;
	});

	$( '#hb-export-customers-unselect-all' ).on( 'click', function() {
		$( this ).blur();
		$( '#hb-export-customers-form input[type="checkbox"]' ).prop( 'checked', false );
		return false;
	});

	$( '#hb-export-customers-download' ).on( 'click', function() {
		$( this ).blur();
		if ( ! $( 'input[name="hb-customers-data-export[]"]:checked').length ) {
			alert( hb_text.no_export_data_selected );
			return false;
		}
		$( '#hb-export-customers-form' ).submit();
		return false;
	});

	$( '#hb-export-customers-cancel' ).on( 'click', function() {
		$( '#hb-export-customers' ).slideUp( function() {
			$( '#hb-export-customers-toggle .dashicons-arrow-down' ).css( 'display', 'inline-block' );
			$( '#hb-export-customers-toggle .dashicons-arrow-up' ).hide();
		});
		return false;
	});

	function Customer( id, info, nb_resa ) {
		this.id = id;
		this.info = ko.observable( info );
		this.nb_resa = nb_resa;
		this.editing = ko.observable( false );
		this.saving = ko.observable( false );
		this.deleting = ko.observable( false );
		this.anim_class = ko.observable( '' );

		var self = this;

		this.customer_data = ko.computed( function() {
			var data;
			try {
				data = JSON.parse( self.info() );
			} catch ( e ) {
				return [];
			}
			return data;
		});

		this.first_name = ko.computed( function() {
			if ( self.customer_data()['first_name'] ) {
				return self.customer_data()['first_name'];
			} else {
				return '';
			}
		});

		this.last_name = ko.computed( function() {
			if ( self.customer_data()['last_name'] ) {
				return self.customer_data()['last_name'];
			} else {
				return '';
			}
		});

		this.email = ko.computed( function() {
			if ( self.customer_data()['email'] ) {
				return self.customer_data()['email'];
			} else {
				return '';
			}
		});

		this.other_info = ko.computed( function() {
			var non_displayed_info = ['first_name','last_name','email'],
				customer_data = self.customer_data(),
				info_markup = '';
			$.each( customer_data, function( info_id, info_value ) {
				if ( info_value != '' && non_displayed_info.indexOf( info_id ) < 0 ) {
					if ( hb_customer_fields[ info_id ] ) {
						info_markup += '<b>' + hb_customer_fields[ info_id ]['name'] + ':</b> ';
						if ( hb_customer_fields[ info_id ]['type'] == 'textarea' ) {
							info_markup += '<br/>';
						}
					}
					info_markup += info_value.replace( /(?:\r\n|\r|\n)/g, '<br/>' ) + '<br/>';
				}
			});
			if ( self.nb_resa > 0 ) {
				if ( info_markup ) {
					info_markup += '<br/>';
				}
				info_markup += '<a href="' + hb_resa_customer_page_url + self.id + '">';
				if ( self.nb_resa == 1 ) {
					info_markup += hb_text.one_resa;
				} else {
					info_markup += hb_text.several_resa.replace( '%s', nb_resa );
				}
				info_markup += '</a>';
			}

			return info_markup;
		});

		this.name_email_id = ko.computed( function() {
			var name_email_id_raw = self.id + self.first_name() + self.last_name() + self.email();
			return name_email_id_raw.toLowerCase();
		});

		this.customer_info_editing_markup = ko.computed( function() {
			var customer_edit_markup = '';
			$.each( hb_customer_fields, function( field_id, field_info ) {
				if ( ['first_name', 'last_name', 'email'].indexOf( field_id ) != -1 ) {
					customer_edit_markup += hb_text[ field_id ];
				} else {
					customer_edit_markup += field_info['name'];
				}
				customer_edit_markup += '<br/>';
				if ( field_info['type'] == 'textarea' ) {
					customer_edit_markup += '<textarea ';
					customer_edit_markup += 'rows="2" ';
					customer_edit_markup += 'class="hb-textarea-edit-resa hb-input-customer-' + self.id + '" '
					customer_edit_markup += 'data-id="' + field_id + '" ';
					customer_edit_markup += '>';
					if ( self.customer_data()[ field_id ] ) {
						customer_edit_markup += self.customer_data()[ field_id ];
					}
					customer_edit_markup += '</textarea>';
				} else {
					customer_edit_markup += '<input ';
					customer_edit_markup += 'class="hb-input-edit-resa hb-input-customer-' + self.id + '" ';
					customer_edit_markup += 'type="text" ';
					if ( self.customer_data()[ field_id ] ) {
						customer_edit_markup += 'value="' +  self.customer_data()[ field_id ] + '" ';
					}
					customer_edit_markup += 'data-id="' + field_id + '" ';
					customer_edit_markup += '/>';
				}
			});
			return customer_edit_markup;
		});
	}

	function CustomerViewModel() {

		var self = this;

		this.customers_list = ko.observableArray();

		this.filter_customer_search = ko.observable( '' );
		this.customer_sort = ko.observable( hb_saved_sorting );

		this.customer_sort.subscribe( function( sorting ) {
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'hb_save_customers_sorting',
					new_sorting: sorting,
					nonce: $( '#hb_nonce_update_db' ).val()
				},
				timeout: hb_ajax_settings.timeout,
				success: function() {},
				error: function( jqXHR, textStatus, errorThrown ) {
					console.log( jqXHR );
					console.log( jqXHR.responseText );
					console.log( textStatus + ' (' + errorThrown + ')' );
				}
			});
		});

		this.customers_filtered = ko.computed( function() {
			var filter = self.filter_customer_search().toLowerCase().replace( /\s/g, '' );
			var sorted_customers = self.customers_list().sort( function( a, b ) {
				if ( self.customer_sort() == 'last_name_asc' ) {
					return a.last_name().localeCompare( b.last_name() );
				} else if ( self.customer_sort() == 'last_name_desc' ) {
					return b.last_name().localeCompare( a.last_name() );
				} else if ( self.customer_sort() == 'id_asc' ) {
					return a.id - b.id;
				} else if ( self.customer_sort() == 'id_desc' ) {
					return b.id - a.id;
				}
			});
			if ( ! filter ) {
				return sorted_customers;
			} else {
				return ko.utils.arrayFilter(
					sorted_customers,
					function( customer ) {
						if ( customer.name_email_id().indexOf( filter ) >= 0 ) {
							return true;
						} else {
							return false;
						}
					}
				);
			}
		});

		function blur_buttons() {
			$( '.button' ).blur();
		}

		this.customers_per_page = 25;
		this.customers_current_page_number = ko.observable( 1 );

		this.customers_first_page = function() {
			self.customers_current_page_number( 1 );
			blur_buttons();
		}

		this.customers_last_page = function() {
			self.customers_current_page_number( self.customers_total_pages() );
			blur_buttons();
		}

		this.customers_next_page = function() {
			if ( self.customers_current_page_number() != self.customers_total_pages() ) {
				self.customers_current_page_number( self.customers_current_page_number() + 1 );
			}
			blur_buttons();
		}

		this.customers_previous_page = function() {
			if ( self.customers_current_page_number() != 1 ) {
				self.customers_current_page_number( self.customers_current_page_number() - 1 );
			}
			blur_buttons();
		}

		this.customers_total_pages = ko.computed(function() {
			var total = Math.floor( self.customers_filtered().length / self.customers_per_page );
			total += self.customers_filtered().length % self.customers_per_page > 0 ? 1 : 0;
			return total;
		});

		this.customers_paginated = ko.computed( function() {
			if ( self.customers_current_page_number() > self.customers_total_pages() ) {
				self.customers_current_page_number( 1 );
			}
			var first = self.customers_per_page * ( self.customers_current_page_number() - 1 );
			return self.customers_filtered().slice( first, first + self.customers_per_page );
		});

		var observable_customers = [];
		$.each( hb_customers, function( customer_id, customer_info ) {
			observable_customers.push(
				new Customer(
					customer_info.id,
					customer_info.info,
					customer_info.nb_resa,
				)
			);
		});
		this.customers_list( observable_customers );

		this.edit_customer = function( customer ) {
			customer.editing( true );
		}

		this.cancel_edit_customer = function( customer ) {
			customer.editing( false );
		}

		this.save_customer = function( customer ) {
			customer.saving( true );
			var customer_details = {},
				customer_email = '';
			$( '.hb-input-customer-' + customer.id ).each( function() {
				if ( $( this ).val() != '' ) {
					customer_details[ $( this ).data( 'id' ) ] = $( this ).val();
				}
				if ( $( this ).data( 'id' ) == 'email' ) {
					customer_email = $( this ).val();
				}
			});
			customer_details = JSON.stringify( customer_details );
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'hb_update_customer',
					customer_id: customer.id,
					email: customer_email,
					info: customer_details,
					nonce: $( '#hb_nonce_update_db' ).val()
				},
				timeout: hb_ajax_settings.timeout,
				success: function( ajax_return ) {
					customer.saving( false );
					customer.editing( false );
					if ( ajax_return.trim() == 'customer updated' ) {
						customer.info( customer_details );
					} else {
						alert( ajax_return );
					}
				},
				error: function( jqXHR, textStatus, errorThrown ) {
					customer.saving( false );
					alert( textStatus + ' (' + errorThrown + ')' );
				}
			});
		}

		this.delete_customer = function( customer ) {
			if ( confirm( hb_text.confirm_delete_customer ) ) {
				customer.deleting( true );
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						'action': 'hb_delete_customer',
						'customer_id': customer.id,
						'nonce': $( '#hb_nonce_update_db' ).val()
					},
					timeout: hb_ajax_settings.timeout,
					success: function( ajax_return ) {
						if ( ajax_return.trim() == 'customer_deleted' ) {
							customer.anim_class( 'hb-customer-deleting' );
							setTimeout( function() {
								self.customers_list.remove( customer );
							}, 300 );
						} else {
							customer.deleting( false );
							alert( ajax_return );
						}
					},
					error: function( jqXHR, textStatus, errorThrown ) {
						customer.deleting( false );
						alert( textStatus + ' (' + errorThrown + ')' )
					}
				});
			}
		}
	}

	var customerViewModel = new CustomerViewModel();
	ko.applyBindings( customerViewModel );

});