jQuery( document ).ready( function( $ ) {
	'use strict';

	/* external payment thank you page */

	if ( $( '#hb-resa-payment-thank-you-page-redirect' ).length ) {
		if ( typeof window['hbook_reservation_done'] == 'function' ) {
			window['hbook_reservation_done']();
		}
		$( '#hb-resa-payment-thank-you-page-redirect' ).submit();
	}

	/* end external payment thank you page */

	/* ------------------------------------------------------------------------------------------- */

	/* padding top */

	var page_padding_top;
	if ( typeof hb_booking_form_data !== 'undefined' ) {
		page_padding_top = hb_booking_form_data.page_padding_top;
	} else {
		page_padding_top = hb_payment_confirmation_padding_top;
	}

	var adminbar_height = 0;
	if ( $( '#wpadminbar' ).length ) {
		adminbar_height = $( '#wpadminbar' ).height();
		page_padding_top = parseInt( page_padding_top ) + adminbar_height;
	}

	/* end padding top */

	/* ------------------------------------------------------------------------------------------- */

	/* id and for attributes */

	if ( ( typeof hb_booking_form_data !== 'undefined' ) && ( hb_booking_form_data.is_admin != 'yes' ) ) {
		var form_nb = 1;
		$( '.hbook-wrapper' ).each( function() {
			$( this ).find( 'label' ).each( function() {
				var new_attr_for = 'hb-form-' + form_nb + '-' + $( this ).attr( 'for' );
				$( this ).attr( 'for', new_attr_for );
			});
			$( this ).find( 'input, select, textarea' ).each( function() {
				var attr_id = $( this ).attr( 'id' );
				if ( attr_id ) {
					var new_attr_id = 'hb-form-' + form_nb + '-' + attr_id;
					$( this ).attr( 'id', new_attr_id );
				}
			});
			$( this ).find( '.hb-booking-search-form' ).addClass( 'hb-form-' + form_nb );
			$( this ).find( '.hb-multi-accom-people-selection-wrapper' ).attr( 'data-form-num', form_nb );
			form_nb++;
		});
	}

	/* end id and for attributes */

	/* ------------------------------------------------------------------------------------------- */

	/* booking search */

	$( 'body' ).append( '<div class="hb-people-popup-wrapper"></div>' );
	if ( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test( navigator.userAgent ) ) {
		$( '.hb-people-popup-wrapper-close' ).css( 'display', 'block' );
	}

	$( '.hb-accom-number' ).change( function() {
		show_people_popup( $( this ) );
	});

	$( 'html' ).click( function( e ) {
		if (
			! $( e.target ).is( '.hb-people-popup-wrapper' ) &&
			! $( e.target ).is( '.hb-accom-number' ) &&
			! $( e.target ).is( '.hb-accom-number option' ) &&
			! $( e.target ).is( '.hb-multi-accom-adults' ) &&
			! $( e.target ).is( '.hb-multi-accom-adults option' ) &&
			! $( e.target ).is( '.hb-multi-accom-children' ) &&
			! $( e.target ).is( '.hb-multi-accom-children option' ) &&
			$( '.hb-people-popup-wrapper' ).hasClass( 'hb-people-popup-wrapper-visible' )
		) {
			hide_people_popup();
		}
		if ( $( e.target ).is( '.hb-people-popup-wrapper-close' ) ) {
			e.preventDefault();
			hide_people_popup();
		}
	});

	function show_people_popup( $accom_people_select ) {
		if ( ! $( '.hb-people-popup-wrapper' ).hasClass( 'hb-people-popup-wrapper-visible' ) ) {
			$( '.hb-people-popup-wrapper' ).addClass(' hb-people-popup-wrapper-visible' ).html( '' );
			var $accom_people_selection = $accom_people_select.parents( '.hb-booking-search-form' ).find( '.hb-multi-accom-people-selection-wrapper' );
			$accom_people_selection.show();
			$( '.hb-people-popup-wrapper' ).append( $accom_people_selection );
			if ( ! $accom_people_select.parents( '.hb-booking-search-form' ).hasClass( 'hb-search-form-no-people' ) ) {
				$( '.hb-people-popup-wrapper' ).show();
			}
		}
		var multi_accom_num = $accom_people_select.val();
		if ( multi_accom_num == 'any' ) {
			multi_accom_num = 1;
		}
		$( '.hb-people-popup-wrapper .hb-multi-accom-people-selection' ).hide();
		for ( var i = 1; i <= multi_accom_num; i++ ) {
			$( '.hb-people-popup-wrapper .hb-multi-accom-people-selection-accom-' + i ).show();
		}
		if ( multi_accom_num > 1 ) {
			$( '.hb-people-popup-wrapper .hb-multi-accom-people-title' ).show();
		} else {
			$( '.hb-people-popup-wrapper .hb-multi-accom-people-title' ).hide();
		}
		if ( $accom_people_select.parents( '.hb-booking-search-form' ).hasClass( 'hb-search-form-no-children' ) ) {
			$( '.hb-multi-accom-children-wrapper' ).hide();
		} else {
			$( '.hb-multi-accom-children-wrapper' ).show();
		}
		reposition_people_popup();
	}

	function hide_people_popup() {
		if ( $( '.hb-people-popup-wrapper' ).hasClass( 'hb-people-popup-wrapper-visible' ) ) {
			$( '.hb-people-popup-wrapper' ).hide().removeClass( 'hb-people-popup-wrapper-visible' );
			var $accom_people_selection = $( '.hb-people-popup-wrapper .hb-multi-accom-people-selection-wrapper' );
			$accom_people_selection.hide();
			if ( hb_booking_form_data.is_admin == 'yes' ) {
				var $form = $( '.hb-booking-search-form' );
			} else {
				var $form = $( '.hb-booking-search-form.hb-form-' + $accom_people_selection.data( 'form-num' ) );
			}
			$form.append( $accom_people_selection );
			update_accom_people( $form );
		}
	}

	function update_accom_people( $form ) {
		var accom_people = [];
		var total_accom_people = 0;
		var has_children = false;
		var new_accom_number = $form.find( '.hb-accom-number' ).val();
		if ( new_accom_number == 'any' ) {
			new_accom_number = 1;
			$form.find( '.hb-accom-people-any').val( 'yes' );
		} else {
			$form.find( '.hb-accom-people-any').val( 'no' );
		}
		if ( new_accom_number > 0 ) {
			for ( var i = 1; i <= new_accom_number; i++ ) {
				var accom_adults = $form.find( '.hb-multi-accom-people-selection-accom-' + i + ' .hb-multi-accom-adults' ).val();
				var accom_children = $form.find( '.hb-multi-accom-people-selection-accom-' + i + ' .hb-multi-accom-children' ).val();
				if ( accom_children > 0 ) {
					has_children = true;
				}
				accom_people.push( accom_adults + '-' + accom_children );
				total_accom_people += parseInt( accom_adults ) + parseInt( accom_children );
			}
			if ( accom_people.length ) {
				accom_people = accom_people.join( ',' );
			} else {
				accom_people = '';
			}
			$form.find( '.hb-accom-people' ).val( accom_people );
			if ( has_children ) {
				var option_text = hb_text.chosen_persons_in_accom_number.replace( '%nb_persons', total_accom_people );
			} else if ( total_accom_people > 1 ) {
				var option_text = hb_text.chosen_adults_in_accom_number.replace( '%nb_adults', total_accom_people );
			} else {
				var option_text = hb_text.one_adult_chosen_in_accom_number;
			}
			option_text = $form.find( '.hb-accom-number option:selected' ).html() + ' ' + option_text;
			if ( $form.find( '.hb-accom-number option[value="0"]' ).length ) {
				$form.find( '.hb-accom-number option[value="0"]' ).html( option_text );
				$form.find( '.hb-accom-number' ).val( 0 );
			}
		}
	}

	$( '.hb-booking-search-form' ).on( 'submit', function() {
		var $form = $( this ),
			$booking_wrapper = $form.parents( '.hbook-wrapper' );
		$form.find( 'input[type="submit"]' ).blur();
		if ( ! $form.hasClass( 'submitted' ) ) {
			$booking_wrapper.find( '.hb-booking-details-form' ).hide();
			$form.find( '.hb-search-error' ).slideUp();
			var check_in = $form.find( '.hb-check-in-date' ).val(),
				check_out = $form.find( '.hb-check-out-date' ).val(),
				adults = $form.find( 'select.hb-adults' ).val(),
				children = $form.find( 'select.hb-children' ).val(),
				admin_accom_id = '',
				admin_search_type = '',
				people_and_date_validation;
			if ( $form.find( '.hb-admin-search-type' ).length ) {
				admin_search_type = $form.find( '.hb-admin-search-type' ).val();
			}
			if ( admin_search_type == 'single_accom' ) {
				admin_accom_id = $form.find( '.hb-accom' ).val();
			}
			if ( ! children || $form.hasClass( 'hb-search-form-no-children' ) ) {
				$form.find( 'select.hb-children' ).val( 0 );
				children = 0;
			}
			if ( $form.hasClass( 'hb-search-form-no-people' ) ) {
				$form.find( 'select.hb-adults' ).val( 1 );
				adults = 1;
			}
			if ( ! adults ) {
				$form.find( 'select.hb-adults option:nth-child(2)' ).prop( 'selected', true );
				adults = $form.find( 'select.hb-adults' ).val();
			}
			var booking_rules = $booking_wrapper.data( 'booking-rules' );
			people_and_date_validation = validate_people_and_check_dates( check_in, check_out, adults, children, booking_rules );
			if ( ! people_and_date_validation.success ) {
				search_show_error( $form, people_and_date_validation.error_msg );
			} else {
				disable_form_submission( $form );
                                if ( ( check_in == '' ) && ( check_out == '' ) ) {
                                        $form.find( '.hb-check-in-hidden' ).val( '' );
                                        $form.find( '.hb-check-out-hidden' ).val( '' );
                                }else{
                                        $form.find( '.hb-check-in-hidden' ).val( date_to_string( people_and_date_validation.check_in ) );
                                        $form.find( '.hb-check-out-hidden' ).val( date_to_string( people_and_date_validation.check_out ) );
                                }
				if ( $form.data( 'search-only' ) == 'yes' || $form.data( 'booking-details-redirection' ) == 'yes' ) {
					return true;
				}
				$form.find( '.hb-search-no-result' ).slideUp();
                                if($form.closest('.hb-villa-result-details').length > 0){
                                    $form.closest('.hb-villa-result-details').find( '.loader-wrapper' ).removeClass('hb-hide');
                                }else{
                                    $form.find( '.loader-wrapper' ).removeClass('hb-hide');
                                }
				var accom_people = '';
				if ( ( admin_search_type == 'multiple_accom' ) || $form.hasClass( 'hb-search-form-multiple-accom' ) ) {
					accom_people = $form.find( '.hb-accom-people' ).val();
					if ( $form.find( '.hb-accom-people-any').val() == 'yes' ) {
						accom_people = accom_people.split( '-' );
						adults = accom_people[0];
						children = accom_people[1];
						accom_people = '';
					}
				}
                                
                                var action = 'hb_get_available_accom',
                                    region_filter_val = '',
                                    price_min_rang = '',
                                    price_max_rang = '',
                                    bedrooms_filter_val = '',
                                    bathrooms_filter_val = '',
                                    beds_filter_val = '',
                                    beachfront_filter_val = '',
                                    paged = 1;
                                if( $form.find( '.hb-region-filter-hidden' ).length > 0 ) {
                                    var action = 'hb_get_available_villa',
                                        region_filter_val = $form.find( '.hb-region-filter-hidden' ).val();
                                    $booking_wrapper.find( '.hb-accom-list' ).html('');
                                }
                                
                                if( $form.find( '.hb-price-search .price-min-range' ).length > 0 && $form.find( '.hb-price-search .price-max-range' ).length > 0 ) {
                                    price_min_rang = $form.find( '.hb-price-search .price-min-range' ).val();
                                    price_max_rang = $form.find( '.hb-price-search .price-max-range' ).val();
                                }
                                
                                if( $form.find( '.hb-bedrooms-filter' ).length > 0 ) {
                                    bedrooms_filter_val = $form.find( '.hb-bedrooms-filter' ).val(); 
                                }
                                
                                if( $form.find( '.hb-bathrooms-filter' ).length > 0 ) {
                                    bathrooms_filter_val = $form.find( '.hb-bathrooms-filter' ).val(); 
                                }
                                
                                if( $form.find( '.hb-beds-filter' ).length > 0 ) {
                                    beds_filter_val = $form.find( '.hb-beds-filter' ).val(); 
                                }
                                
                                if( $form.find( '.hb-beachfront-filter' ).length > 0 && $form.find( '.hb-beachfront-filter' ).is(":checked")) {
                                    beachfront_filter_val = 1; 
                                }
                                
                                if($form.find('.hb-page-num').length > 0){
                                    paged = $form.find('.hb-page-num').val();
                                }
                                
				$.ajax({
					url: hb_booking_form_data.ajax_url,
					type: 'POST',
					timeout: hb_booking_form_data.ajax_timeout,
					data: {
						'action': action,
						'check_in': $form.find( '.hb-check-in-hidden' ).val(),
						'check_out': $form.find( '.hb-check-out-hidden' ).val(),
						'adults': adults,
						'children': children,
						'results_show_only_accom_id': $form.find( '.hb-results-show-only-accom-id' ).val(),
						'page_accom_id': $booking_wrapper.data( 'page-accom-id' ),
						'current_page_id': $booking_wrapper.data( 'current-page-id' ),
						'exists_main_booking_form': $booking_wrapper.data( 'exists-main-booking-form' ),
						'force_display_thumb': $booking_wrapper.data( 'force-display-thumb' ),
						'force_display_desc': $booking_wrapper.data( 'force-display-desc' ),
						'chosen_options': $form.find( '.hb-chosen-options' ).val(),
						'chosen_accom_num': $form.find( '.hb-chosen-accom-num' ).val(),
						'is_admin': hb_booking_form_data.is_admin,
						'admin_accom_id': admin_accom_id,
						'admin_search_type': admin_search_type,
						'accom_people': accom_people,
                                                'region_filter_val': region_filter_val,
                                                'price_min_rang': price_min_rang,
                                                'price_max_rang': price_max_rang,
                                                'bedrooms_filter_val': bedrooms_filter_val,
                                                'bathrooms_filter_val': bathrooms_filter_val,
                                                'beds_filter_val': beds_filter_val,
                                                'beachfront_filter_val': beachfront_filter_val,
                                                'paged' : paged
					},
					success: function( response ) {
						search_show_response( response, $form, $booking_wrapper, action );
                                                $(document).trigger("hb_ajax_search_response");
					},
					error: function( jqXHR, textStatus, errorThrown ) {
                                                if($form.closest('.hb-villa-result-details').length > 0){
                                                    $form.closest('.hb-villa-result-details').find( '.loader-wrapper' ).addClass('hb-hide');
                                                }else{
                                                    $form.find( '.loader-wrapper' ).addClass('hb-hide');
                                                }
						enable_form_submission( $form );
						search_show_error( $form, hb_text.connection_error );
						console.log( jqXHR );
						console.log( jqXHR.responseText );
						console.log( textStatus );
						console.log( errorThrown );
					}
				});
			}
		}
		return false;
	});

	function validate_people_and_check_dates( check_in, check_out, adults, children, booking_rules ) {
		if ( ( check_in == '' ) && ( check_out == '' ) ) {
                        return { success: true, check_in: check_in_date, check_out: check_out_date };
//			return { success: false, error_msg: hb_text.no_check_in_out_date };
		} else if ( check_in == '' ) {
			return { success: false, error_msg: hb_text.no_check_in_date };
		} else if ( check_out == '' ) {
			return { success: false, error_msg: hb_text.no_check_out_date };
		} else if ( adults != parseInt( adults, 10 ) && children != parseInt( children, 10 ) ) {
			return { success: false, error_msg: hb_text.no_adults_children };
		} else if ( adults != parseInt( adults, 10 ) ) {
			return { success: false, error_msg: hb_text.no_adults };
		} else if ( children != parseInt( children, 10 ) ) {
			return { success: false, error_msg: hb_text.no_children };
		}
		var check_in_date,
			check_out_date;
		try {
			check_in_date = $.datepick.parseDate( hb_date_format, check_in );
		} catch( e ) {
			check_in_date = false;
		}
		try {
			check_out_date = $.datepick.parseDate( hb_date_format, check_out );
		} catch( e ) {
			check_out_date = false;
		}
		if ( ! check_in_date && ! check_out_date ) {
			return { success: false, error_msg: hb_text.invalid_check_in_out_date };
		} else if ( ! check_in_date ) {
			return { success: false, error_msg: hb_text.invalid_check_in_date };
		} else if ( ! check_out_date ) {
			return { success: false, error_msg: hb_text.invalid_check_out_date };
		}

		if ( hb_booking_form_data.is_admin == 'yes' ) {
			if ( check_out_date <= check_in_date ) {
				return { success: false, error_msg: hb_text.check_out_before_check_in };
			}
			return { success: true, check_in: check_in_date, check_out: check_out_date };
		}

		if ( hb_min_date != '0' ) {
			var min_date = hb_date_str_2_obj( hb_min_date ),
				txt_min_date = $.datepick.formatDate( hb_date_format, min_date );
			if ( check_in_date < min_date ) {
				return { success: false, error_msg: hb_text.check_in_date_before_date.replace( '%date', txt_min_date ) };
			}
		}
		if ( hb_max_date != '0' ) {
			var max_date = hb_date_str_2_obj( hb_max_date ),
				txt_max_date = $.datepick.formatDate( hb_date_format, max_date );
			max_date.setDate( max_date.getDate() + 1 );
			if ( check_out_date > max_date ) {
				return { success: false, error_msg: hb_text.check_out_date_after_date.replace( '%date', txt_max_date ) };
			}
		}

		var yesterday = new Date();
		yesterday.setDate( yesterday.getDate() - 1 );
		yesterday.setHours( 23, 59, 59 );
		if ( check_in_date < yesterday ) {
			return { success: false, error_msg: hb_text.check_in_date_past };
		} else if ( check_out_date <= check_in_date ) {
			return { success: false, error_msg: hb_text.check_out_before_check_in };
		}

		var check_in_day = day_of_week( check_in_date ),
			check_out_day = day_of_week( check_out_date ),
			nb_nights = date_diff( check_out_date, check_in_date ),
			check_in_date_season = hb_get_season_id( check_in_date ),
			check_out_date_season = hb_get_season_id( check_out_date );

		if ( booking_rules.allowed_check_in_days != 'all' ) {
			var allowed_check_in_days = booking_rules.allowed_check_in_days.split( ',' );
			if ( allowed_check_in_days.indexOf( check_in_day ) < 0 ) {
				var allowed_days = day_name_list( allowed_check_in_days );
				return { success: false, error_msg: hb_text.check_in_day_not_allowed.replace( '%check_in_days', allowed_days ) };
			}
		}
		if ( booking_rules.allowed_check_out_days != 'all' ) {
			var allowed_check_out_days = booking_rules.allowed_check_out_days.split( ',' );
			if ( allowed_check_out_days.indexOf( check_out_day ) < 0 ) {
				var allowed_days = day_name_list( allowed_check_out_days );
				return { success: false, error_msg: hb_text.check_out_day_not_allowed.replace( '%check_out_days', allowed_days ) };
			}
		}
		if (
			booking_rules.seasonal_allowed_check_in_days[ check_in_date_season ] &&
			booking_rules.seasonal_allowed_check_in_days[ check_in_date_season ].split( ',' ).indexOf( check_in_day ) < 0
		) {
			return { success: false, error_msg: hb_text.check_in_day_not_allowed_seasonal };
		}
		if (
			booking_rules.seasonal_allowed_check_out_days[ check_out_date_season ] &&
			booking_rules.seasonal_allowed_check_out_days[ check_out_date_season ].split( ',' ).indexOf( check_out_day ) < 0
		) {
			return { success: false, error_msg: hb_text.check_out_day_not_allowed_seasonal };
		}
		if ( booking_rules.seasonal_minimum_stay[ check_in_date_season ] ) {
			if ( nb_nights < booking_rules.seasonal_minimum_stay[ check_in_date_season ] ) {
				return { success: false, error_msg: hb_text.minimum_stay_seasonal };
			}
		} else if ( nb_nights < booking_rules.minimum_stay ) {
			return { success: false, error_msg: hb_text.minimum_stay.replace( '%nb_nights', booking_rules.minimum_stay ) };
		}
		if ( booking_rules.seasonal_maximum_stay[ check_in_date_season ] ) {
			if ( nb_nights > booking_rules.seasonal_maximum_stay[ check_in_date_season ] ) {
				return { success: false, error_msg: hb_text.maximum_stay_seasonal };
			}
		} else if ( nb_nights > booking_rules.maximum_stay ) {
			return { success: false, error_msg: hb_text.maximum_stay.replace( '%nb_nights', booking_rules.maximum_stay ) };
		}
		if ( booking_rules.conditional_booking_rules.length ) {
			for ( var i = 0; i < booking_rules.conditional_booking_rules.length; i++ ) {
				var rule = booking_rules.conditional_booking_rules[ i ];
				if ( rule.check_in_days.indexOf( check_in_day ) > -1 ) {
					if ( rule.check_out_days.indexOf( check_out_day ) < 0 ) {
						if ( rule['all_seasons'] == 1 ) {
							return {
								success: false,
								error_msg: hb_text.check_out_day_not_allowed_for_check_in_day
									.replace( '%check_in_day', day_name( check_in_day ) )
									.replace( '%check_out_days', day_name_list( rule.check_out_days.split( ',' ) ) )
							};
						} else if ( rule['seasons'].split( ',' ).indexOf( check_in_date_season ) > -1 ) {
							return { success: false, error_msg: hb_text.check_out_day_not_allowed_for_check_in_day_seasonal };
						}
					}
					if ( nb_nights < rule.minimum_stay ) {
						if ( rule['all_seasons'] == 1 ) {
							return {
								success: false,
								error_msg: hb_text.minimum_stay_for_check_in_day
									.replace( '%nb_nights', rule.minimum_stay )
									.replace( '%check_in_day', day_name( check_in_day ) )
							};
						} else if ( rule['seasons'].split( ',' ).indexOf( check_in_date_season ) > -1 ) {
							return { success: false, error_msg: hb_text.minimum_stay_for_check_in_day_seasonal };
						}
					}
					if ( nb_nights > rule.maximum_stay ) {
						if ( rule['all_seasons'] == 1 ) {
							return {
								success: false,
								error_msg: hb_text.maximum_stay_for_check_in_day
									.replace( '%nb_nights', rule.maximum_stay )
									.replace( '%check_in_day', day_name( check_in_day ) )
							};
						} else if ( rule['seasons'].split( ',' ).indexOf( check_in_date_season ) > -1 ) {
							return { success: false, error_msg: hb_text.maximum_stay_for_check_in_day_seasonal };
						}
					}
				}
			}
		}
		return { success: true, check_in: check_in_date, check_out: check_out_date };
	}

	function day_of_week( date ) {
		var day = date.getDay();
		if ( day == 0 ) {
			day = 6;
		} else {
			day = day - 1;
		}
		return day + '';
	}

	function day_name( day ) {
		if ( day == 6 ) {
			day = 0;
		} else {
			day++;
		}
		return hb_day_names[ day ];
	}

	function day_name_list( days ) {
		var days_name = [];
		for ( var i = 0; i < days.length; i++ ) {
			days_name.push( day_name( days[ i ] ) );
		}
		return days_name.join( ', ' );
	}

	function date_diff( check_out_date, check_in_date ) {
		return Math.round( ( check_out_date - check_in_date ) / 1000 / 60 / 60 / 24 );
	}

	function date_to_string( date ) {
		var y = date.getFullYear();
		var m = date.getMonth() + 1;
		m = m + '';
		if ( m.length == 1 ) {
			m = '0' + m;
		}
		var d = date.getDate();
		d = d + '';
		if ( d.length == 1 ) {
			d = '0' + d;
		}
		return y + '-' + m + '-' + d;
	}

	function search_show_error( $form, msg ) {
		if ( $form.find( '.hb-search-error' ).is( ':visible' ) ) {
			$form.find( '.hb-search-error' ).slideUp( function() {
				$( this ).html( msg ).slideDown();
			});
		} else {
			$form.find( '.hb-search-error' ).html( msg ).slideDown();
		}
	}

	function search_show_response( response_text, $form, $booking_wrapper, action ) {
                if($form.closest('.hb-villa-result-details').length > 0){
                    $form.closest('.hb-villa-result-details').find( '.loader-wrapper' ).addClass('hb-hide');
                }else{
                    $form.find( '.loader-wrapper' ).addClass('hb-hide');
                }
		enable_form_submission( $form );
		try {
			var response = JSON.parse( response_text );
		} catch ( e ) {
			$form.find( '.hb-search-error' ).html( 'An error occured. ' + response_text ).slideDown();
			return false;
		}
		if ( ! response.success ) {
			if ( response.msg ) {
				$form.find( '.hb-search-no-result' ).html( response.msg ).slideDown();
			} else {
				$form.find( '.hb-search-error' ).html( response_text ).slideDown();
			}
		} else {
//                        if(action != 'hb_get_available_villa'){ 
                            if($form.find('.hb-chosen-region-filter').length > 0){ 
                                $form.find('.hb-chosen-region-filter span').html($form.find('.hb-region-filter').val());
                            }
                            $form.find( '.hb-chosen-check-in-date span' ).html( $form.find( '.hb-check-in-date' ).val() );
                            $form.find( '.hb-chosen-check-out-date span' ).html( $form.find( '.hb-check-out-date' ).val() );
                            $form.find( '.hb-chosen-accom-number span' ).html( $form.find( '.hb-accom-number option:selected' ).html() );
                            $form.find( '.hb-chosen-admin-search-type span' ).html( $form.find( '.hb-admin-search-type option:selected' ).html() );
                            $form.find( '.hb-chosen-adults span' ).html( $form.find( 'select.hb-adults' ).val() );
                            $form.find( '.hb-chosen-children span' ).html( $form.find( 'select.hb-children' ).val() );
                            $form.find( '.hb-chosen-accom span' ).html( $form.find( '.hb-accom option:selected' ).text() );
                            $form.find( '.hb-search-fields-and-submit' ).slideUp( function() {});
                            $form.find( '.hb-searched-summary' ).removeClass('hb-hide');
//                        }
			$booking_wrapper.find( '.hb-accom-list' ).html( response.mark_up );
                        $('html, body').animate({ scrollTop: $('.hb-accom-list').offset().top }, 1500);

			var is_accom_selected = false;
			if ( ( $form.find( '.hb-accom' ).length ) && ( $form.find( '.hb-accom' ).val() != 'all' ) ) {
				set_selected_accom( $booking_wrapper, $form.find( '.hb-accom' ).val() );
				is_accom_selected = true;
			}
			$booking_wrapper.find( '.hb-multi-accom-choices' ).each( function() {
				if ( $( this ).find( '.hb-accom' ).length == 1 ) {
					set_selected_accom( $( this ), $( this ).find( '.hb-accom' ).data( 'accom-id' ) );
					is_accom_selected = true;
				}
			});
			if ( ! is_accom_selected && ( $booking_wrapper.find( '.hb-multi-accom-choices' ).length == 1 ) ) {
				$booking_wrapper.find( '.hb-next-step-1' ).hide();
			}
			if ( $booking_wrapper.data( 'results-show-only-accom-id' ) != '' ) {
				var accom_id = $booking_wrapper.data( 'results-show-only-accom-id' );
				if ( $booking_wrapper.find( '.hb-accom-id-' + accom_id ).length != 0 ) {
					$booking_wrapper.find( '.hb-search-result-title-section' ).hide();
					$booking_wrapper.find( '.hb-accom' ).hide();
					$booking_wrapper.find( '.hb-accom-id-' + accom_id ).show();
					set_selected_accom( $booking_wrapper, accom_id );
					next_step_1( $booking_wrapper );
				}
			}
			accom_left( $booking_wrapper );
			$booking_wrapper.find( '.hb-accom-list' ).slideDown();
			if ( typeof window['hbook_show_accom_list'] == 'function' ) {
				window['hbook_show_accom_list']();
			}
			hb_format_date();
			resize_price_caption();
		}
	}

	function change_search( $booking_wrapper, show_accom_number ) {
		$booking_wrapper.data( 'results-show-only-accom-id', '' );
		$booking_wrapper.find( '.hb-chosen-options' ).val( '' );
		$booking_wrapper.find( '.hb-chosen-accom-num' ).val( '' );
		$booking_wrapper.find( '.hb-accom-list' ).slideUp();
		$booking_wrapper.find( '.hb-booking-details-form' ).slideUp();
		$booking_wrapper.find( '.hb-coupon-code' ).val( '' );
		$booking_wrapper.find( '.hb-pre-validated-coupon-id' ).val( '' );
		$booking_wrapper.find( '.hb-coupon-amount' ).html( '0' );
		$booking_wrapper.find( '.hb-coupon-msg, .hb-coupon-error' ).slideUp();
		$( '#hb-global-discount-amount' ).val( 0 );
		$( '#hb-global-discount-amount-type-fixed' ).prop( 'checked', true );
//		$booking_wrapper.find( '.hb-searched-summary' ).slideUp( function() {
//			if ( show_accom_number ) {
//				$booking_wrapper.find( '.hb-booking-search-form' ).addClass( 'hb-search-form-multiple-accom' );
//			}
//			$booking_wrapper.find( '.hb-search-fields-and-submit' ).slideDown();
//		});
                $booking_wrapper.find( '.hb-searched-summary' ).addClass('hb-hide');
                if ( show_accom_number ) {
                        $booking_wrapper.find( '.hb-booking-search-form' ).addClass( 'hb-search-form-multiple-accom' );
                }
                $booking_wrapper.find( '.hb-search-fields-and-submit' ).slideDown();
	}

	$( '.hb-change-search-wrapper input' ).on( 'click', function( e ) {
		e.preventDefault();
		change_search( $( this ).parents( '.hbook-wrapper' ), false );
	});

	$( '.hbook-wrapper' ).on( 'click', '.hb-search-specific-accom-number a', function( e ) {
		e.preventDefault();
		var $booking_wrapper = $( this ).parents( '.hbook-wrapper' );
		var $search_form = $booking_wrapper.find( '.hb-booking-search-form' );
		if ( ! $search_form.hasClass('hb-search-form-multiple-accom') ) {
			var adults = $search_form.find( '.hb-adults' ).val();
			var children = $search_form.find( '.hb-children' ).val();
			var accom_adults = [];
			var accom_children = [];
			for ( var i = 0; i < 2; i++ ) {
				accom_adults[ i ] = Math.floor( adults / 2 );
				accom_children[ i ] = Math.floor( children / 2 );
			}
			accom_adults[0] += adults % 2;
			accom_children[0] += children % 2;
			$search_form.find( '.hb-accom-number' ).val( 2 );
			for ( var i = 1; i <= 2; i++ ) {
				$search_form.find( '.hb-multi-accom-people-selection-accom-' + i + ' .hb-multi-accom-adults' ).val( accom_adults[ i - 1 ] );
				$search_form.find( '.hb-multi-accom-people-selection-accom-' + i + ' .hb-multi-accom-children' ).val( accom_children[ i - 1 ] );
			}
			update_accom_people( $search_form );
		}
		change_search( $booking_wrapper, true );
	});

	$( '.hbook-wrapper' ).on( 'click', '.hb-other-search', function() {
		var $form = $( this ).parents( '.hb-booking-search-form' );
		$form.data( 'search-only', 'yes' );
		$form.submit();
		return false;
	});

	/* end booking search */

	/* ------------------------------------------------------------------------------------------- */

	/* accom selection */

	$( '.hb-accom-list' ).on( 'click', '.hb-view-price-breakdown', function() {
		var $self = $( this );
		$self.blur();
		$self.parents( '.hb-accom' ).find( '.hb-price-breakdown' ).slideToggle( function() {
			if ( $( this ).is( ':visible' ) ) {
				$self.find( '.hb-price-bd-hide-text' ).show();
				$self.find( '.hb-price-bd-show-text' ).hide();
			} else {
				$self.find( '.hb-price-bd-hide-text' ).hide();
				$self.find( '.hb-price-bd-show-text' ).show();
			}
		});
		return false;
	});

	$( '.hb-accom-list' ).on( 'click', '.hb-view-accom input', function( e ) {
		e.preventDefault();
		$( this ).blur();
		window.open( $( this ).data( 'accom-url' ), '_blank' );
	});

	$( '.hb-accom-list' ).on( 'click', '.hb-select-accom input', function( e ) {
		e.preventDefault();
		$( this ).blur();

		var accom_id = $( this ).parents( '.hb-accom' ).data( 'accom-id' );
		var $booking_wrapper = $( this ).parents( '.hbook-wrapper' );
		var $current_accom_choices = $( this ).parents( '.hb-multi-accom-choices' );
		var $all_accom_choices = $booking_wrapper.find( '.hb-multi-accom-choices' );
		var clicked_nb = $all_accom_choices.index( $current_accom_choices );
		var clicked_last = ( clicked_nb == $all_accom_choices.length - 1 );

		$( '.hb-multi-accom-no-accom-selected' ).hide();
		set_selected_accom( $current_accom_choices, accom_id );
		accom_left( $booking_wrapper );
		if ( $booking_wrapper.find( '.hb-accom-selected' ).length < $booking_wrapper.find( '.hb-multi-accom-choices' ).length ) {
			setTimeout( function() {
				select_next_accom_scroll( $booking_wrapper );
			}, 800 );
		} else if (
			clicked_last ||
			(
				( $booking_wrapper.find( '.hb-multi-accom-choices' ).length == 1 ) &&
				( $booking_wrapper.find( '.hb-multi-accom-choices .hb-accom' ).length > 1 )
			)
		) {
			next_step_1( $booking_wrapper );
		}
	});

	function set_selected_accom( $accom_choices, accom_id ) {
		$accom_choices.find( '.hb-accom' ).removeClass( 'hb-accom-selected' );
		$accom_choices.find( '.hb-accom-id-' + accom_id ).addClass( 'hb-accom-selected' );
	}

	function select_next_accom_scroll( $booking_wrapper ) {
		$booking_wrapper.find( '.hb-multi-accom-choices' ).each( function() {
			var $hb_multi_accom_choices = $( this );
			if ( ! $hb_multi_accom_choices.find( '.hb-accom-selected' ).length ) {
				var top = $hb_multi_accom_choices.offset().top - page_padding_top;
				var show_select_accom_msg = false;
				if ( $( window ).scrollTop() > top ) {
					show_select_accom_msg = true;
				}
				$( 'html, body' ).animate({ scrollTop: top }, function() {
					if ( show_select_accom_msg ) {
						$hb_multi_accom_choices.find( '.hb-multi-accom-no-accom-selected' ).slideDown();
					}
				});
				return false;
			}
		});
	}

	function accom_left( $booking_wrapper ) {
		var selected_accom_ids = [];
		$booking_wrapper.find( '.hb-multi-accom-choices' ).each( function() {
			if ( $( this ).find( '.hb-accom-selected' ).length ) {
				selected_accom_ids.push( $( this ).find( '.hb-accom-selected' ).data( 'accom-id' ) );
			}
		});
		$( '.hb-accom-none-left' ).removeClass( 'hb-accom-none-left' );
		$booking_wrapper.find( '.hb-accom-quantity' ).each( function() {
			var accom_id = $( this ).data( 'accom-id' );
			var $accom = $booking_wrapper.find( '.hb-accom.hb-accom-id-' + accom_id );
			$accom.each( function() {
				if ( $( this ).hasClass( 'hb-accom-selected' ) ) {
					$( this ).find( '.hb-accom-selected-left-wrapper' ).show();
				} else {
					$( this ).find( '.hb-accom-selected-left-wrapper' ).hide();
				}
			});
			var nb_selected = 0;
			for ( var i = 0; i < selected_accom_ids.length; i++ ) {
				if ( selected_accom_ids[ i ] == accom_id ) {
					nb_selected++;
				}
			}
			$accom.find( '.hb-nb-accom-selected-nb' ).html( nb_selected );
			var updated_quantity = $( this ).data( 'quantity' ) - nb_selected;
			$accom.find( '.hb-accom-left, .hb-nb-accom-selected' ).hide();
			if ( updated_quantity == 0 ) {
				$accom.each( function() {
					$( this ).addClass( 'hb-accom-none-left' );
					if ( ! $( this ).hasClass( 'hb-accom-selected' ) ) {
						$( this ).find( '.hb-nb-accom-selected, .hb-no-accom-left, .hb-accom-selected-left-wrapper' ).show();
					}
				});
			} else if ( ( hb_booking_form_data.is_admin != 'yes' ) && ( updated_quantity <= hb_booking_form_data.display_accom_left_threshold ) ) {
				$accom.find( '.hb-accom-selected-left-wrapper' ).show();
				if ( updated_quantity == 1 ) {
					$accom.find( '.hb-one-accom-left' ).show();
				} else {
					$accom.find( '.hb-multiple-accom-left' ).show();
					$accom.find( '.hb-accom-left-nb' ).html( updated_quantity );
				}
			}
		});
	}

	/* end accom selection */

	/* ------------------------------------------------------------------------------------------- */

	/* steps */

	function init_step( $booking_wrapper ) {
		var top = $booking_wrapper.find( '.hb-accom-list' ).offset().top;
		if ( top ) {
			top -= page_padding_top;
			$( 'html, body' ).animate({ scrollTop: top }, function() {
				$booking_wrapper.find( '.hb-step-wrapper' ).slideUp();
			});
		} else {
			$booking_wrapper.find( '.hb-step-wrapper' ).hide();
		}
	}

	$( '.hb-accom-list' ).on( 'click', '.hb-next-step-1 input', function() {
		$( this ).blur();
		next_step_1( $( this ).parents( '.hbook-wrapper' ) );
	});

	function next_step_1( $booking_wrapper ) {
		var $form = $booking_wrapper.find( '.hb-booking-search-form' );
		if ( $form.attr( 'action' ) != '#' ) {
			$form.data( 'booking-details-redirection', 'yes' );
			var accom_id = $booking_wrapper.find( '.hb-accom-selected' ).data( 'accom-id' );
			$form.find( '.hb-results-show-only-accom-id' ).val( accom_id );
			$form.submit();
			return;
		}

		if ( $booking_wrapper.find( '.hb-accom-selected' ).length < $booking_wrapper.find( '.hb-multi-accom-choices' ).length ) {
			if ( $booking_wrapper.find( '.hb-multi-accom-choices' ).length == 1 ) {
				var top = $booking_wrapper.find( '.hb-search-result-title-section .hb-title-select' ).offset().top;
				top -= page_padding_top;
				$( 'html, body' ).animate({ scrollTop: top });
				return;
			} else {
				select_next_accom_scroll( $booking_wrapper );
			}
			return;
		}
		$booking_wrapper.find( '.hb-filter-accom-list-wrapper' ).slideUp();
		init_step( $booking_wrapper );

		$booking_wrapper.find( '.hb-coupon-code' ).val( '' );
		$booking_wrapper.find( '.hb-coupon-amount' ).html( '0' );

		calculate_options_price( $booking_wrapper );

		var accom_ids = [];
		$booking_wrapper.find( '.hb-multi-accom-choices' ).each( function() {
			accom_ids.push( $( this ).find( '.hb-accom-selected' ).data( 'accom-id' ) );
		});
		set_details_form_info( $booking_wrapper, accom_ids.join( '-' ) );
		toggle_accom_not_available_msg( $booking_wrapper, accom_ids[0] );
		$booking_wrapper.find( '.hb-policies-error, .hb-confirm-error, .hb-option, .hb-options-form, .hb-select-accom-num, .hb-booking-details-form, .hb-summary-wrapper' ).hide();

		var show_intermediate_step = false;
		if ( $booking_wrapper.find( '.hb-select-accom-num-form' ).length ) {
			var has_multiple_num_choice = false;
			for ( var i = 1; i < accom_ids.length + 1; i++ ) {
				var select_wrapper_class = '.hb-select-multi-accom-num-accom-' + i + ' .hb-select-accom-num-accom-' + accom_ids[ i - 1 ];
				if ( $( select_wrapper_class ).find( 'option' ).length > 2 ) {
					has_multiple_num_choice = true;
				}
				$booking_wrapper.find( select_wrapper_class ).show();
			}
			if ( has_multiple_num_choice ) {
				$booking_wrapper.find( '.hb-select-accom-num-form' ).show();
				show_intermediate_step = true;
			} else {
				$booking_wrapper.find( '.hb-select-accom-num-form' ).hide();
				$booking_wrapper.find( '.hb-select-accom-num-form option:nth-child(2)' ).prop( 'selected', true );
			}
		}

		var show_options_form = false;
		for ( var i = 1; i < accom_ids.length + 1; i++ ) {
			var option_class = '.hb-options-multi-accom-' + i + ' .hb-option-accom-' + accom_ids[ i - 1 ];
			if ( $booking_wrapper.find( option_class ).length ) {
				show_options_form = true;
				show_intermediate_step = true;
				$booking_wrapper.find( option_class ).show();
				$booking_wrapper.find( '.hb-options-multi-accom-' + i + ' .hb-options-gap' ).show();
			} else {
				$booking_wrapper.find( '.hb-options-multi-accom-' + i + ' .hb-no-options' ).show();
				$booking_wrapper.find( '.hb-options-multi-accom-' + i + ' .hb-options-gap' ).hide();
			}
		}
		if ( $booking_wrapper.find( '.hb-options-multi-accom-global' ).length ) {
			$booking_wrapper.find( '.hb-options-multi-accom-global .hb-option' ).show();
			show_options_form = true;
			show_intermediate_step = true;
		}
		if ( show_options_form ) {
			$booking_wrapper.find( '.hb-options-form' ).show();
		} else {
			$booking_wrapper.find( '.hb-options-form' ).hide();
		}

		if ( show_intermediate_step ) {
			$booking_wrapper.addClass( 'has-intermediate-step' );
			setTimeout( function( ) {
				$booking_wrapper.find( '.hb-intermediate-step-wrapper' ).slideDown( function() {
					scroll_to_accom_list_top( $booking_wrapper );
				});
			}, 800 );
		} else {
			$booking_wrapper.removeClass( 'has-intermediate-step' );
			set_summary( $booking_wrapper );
			setTimeout( function( ) {
				$booking_wrapper.find( '.hb-booking-details-form' ).slideDown( function() {
					resize_forms();
					scroll_to_accom_list_top( $booking_wrapper );
				});
			}, 800 );
		}

		return false;
	}

	$( '.hb-accom-list' ).on( 'click', '.hb-next-step-2 input', function() {
		$( this ).blur();
		var $booking_wrapper = $( this ).parents( '.hbook-wrapper' );
		init_step( $booking_wrapper );
		if ( $booking_wrapper.find( '.hb-select-accom-num-form' ).length ) {
			var accom_ids = $booking_wrapper.find( '.hb-details-accom-ids' ).val().split( '-' );
			var selected_accom_nums = {};
			for ( var i = 1; i < accom_ids.length + 1; i++ ) {
				selected_accom_nums[ accom_ids[ i - 1 ] ] = [];
			}
			for ( var i = 1; i < accom_ids.length + 1; i++ ) {
				var $select_num_wrapper = $booking_wrapper.find( '.hb-select-multi-accom-num-accom-' + i + ' .hb-select-accom-num-accom-' + accom_ids[ i - 1 ] );
				var selected_num = $select_num_wrapper.find( 'select' ).val();
				if ( selected_num != 0 ) {
					selected_accom_nums[ accom_ids[ i - 1 ] ].push( selected_num );
				}
			}
			for ( var i = 1; i < accom_ids.length + 1; i++ ) {
				var $select_num_wrapper = $booking_wrapper.find( '.hb-select-multi-accom-num-accom-' + i + ' .hb-select-accom-num-accom-' + accom_ids[ i - 1 ] );
				var selected_num = $select_num_wrapper.find( 'select' ).val();
				if ( selected_num == 0 ) {
					for ( var j = 2; j <= $select_num_wrapper.find( 'option' ).length; j++ ) {
						var $option_num = $select_num_wrapper.find( 'option:nth-child(' + j + ')' );
						var candidate_num = $option_num.val();
						if ( selected_accom_nums[ accom_ids[ i - 1 ] ].indexOf( candidate_num ) < 0 ) {
							$option_num.prop( 'selected', true );
							selected_accom_nums[ accom_ids[ i - 1 ] ].push( candidate_num );
							break;
						}
					}
				}
			}
		}
		set_summary( $booking_wrapper );
		setTimeout( function() {
			$booking_wrapper.find( '.hb-booking-details-form' ).slideDown( function() {
				resize_forms();
				var top = $booking_wrapper.find( '.hb-booking-details-form' ).offset().top - page_padding_top;
				$( 'html, body' ).animate({ scrollTop: top });
			});
		}, 800 );
		return false;
	});

	$( '.hb-accom-list' ).on( 'click', '.hb-previous-step-1 input', function() {
		$( this ).blur();
		var $booking_wrapper = $( this ).parents( '.hbook-wrapper' );
		$booking_wrapper.find( '.hb-next-step-1' ).show();
		init_step( $booking_wrapper );
		setTimeout( function() {
			$booking_wrapper.find( '.hb-accom-step-wrapper, .hb-filter-accom-list-wrapper' ).slideDown( function() {
				scroll_to_accom_list_top( $booking_wrapper );
			});
		}, 800 );
		return false;
	});

	$( '.hb-previous-step-2 input' ).on( 'click', function() {
		$( this ).blur();
		var $booking_wrapper = $( this ).parents( '.hbook-wrapper' );
		init_step( $booking_wrapper );
		setTimeout( function() {
			if ( $booking_wrapper.hasClass( 'has-intermediate-step' ) ) {
				$booking_wrapper.find( '.hb-intermediate-step-wrapper' ).slideDown( function() {
					scroll_to_accom_list_top( $booking_wrapper );
				});
			} else {
				$booking_wrapper.find( '.hb-accom-step-wrapper' ).slideDown( function() {
					scroll_to_accom_list_top( $booking_wrapper );
				});
				$booking_wrapper.find( '.hb-next-step-1' ).show();
			}
		}, 800 );
		return false;
	});

	function scroll_to_accom_list_top( $booking_wrapper ) {
		var top = $booking_wrapper.find( '.hb-accom-list' ).offset().top - page_padding_top;
		$( 'html, body' ).animate({ scrollTop: top });
	}

	/* end steps */

	/* ------------------------------------------------------------------------------------------- */

	/* accom num selection */

	$( '.hb-accom-list' ).on( 'change', '.hb-select-accom-num-form select', function() {
		var accom_id = $( this ).data( 'accom-id' );
		var select_id = $( this ).attr( 'id' );
		var select_val = $( this ).val();
		$( this ).parents( '.hb-select-accom-num-form' ).find( '.hb-select-accom-num-accom-' + accom_id + ' select' ).each( function() {
			if (
				( $( this ).attr( 'id' ) != select_id ) &&
				( $( this ).val() == select_val )
			) {
				$( this ).val( '0' ).focus();
			}
		});
	});

	/* end accom num selection */

	/* ------------------------------------------------------------------------------------------- */

	/* options selection */

	$( '.hb-accom-list' ).on( 'click', '.hb-option', function() {
		var $booking_wrapper = $( this ).parents( '.hbook-wrapper' );
		verify_option_max( $booking_wrapper );
		calculate_options_price( $booking_wrapper );
	});

	$( '.hb-accom-list' ).on( 'keyup', '.hb-option input', function() {
		var $booking_wrapper = $( this ).parents( '.hbook-wrapper' );
		verify_option_max( $booking_wrapper );
		calculate_options_price( $booking_wrapper );
	});

	function verify_option_max( $booking_wrapper ) {
		$booking_wrapper.find( '.hb-option' ).each( function() {
			if ( $( this ).hasClass( 'hb-quantity-option' ) && $( this ).find( 'input' ).attr( 'max' ) ) {
				if ( parseInt( $( this ).find( 'input' ).val() ) > parseInt( $( this ).find( 'input' ).attr( 'max' ) ) ) {
					$( this ).find( 'input' ).val( $( this ).find( 'input' ).attr( 'max' ) );
				}
			}
		});
	}

	function calculate_options_price( $booking_wrapper ) {
		var options_price = 0;
		var accom_ids = [];

		$booking_wrapper.find( '.hb-multi-accom-choices' ).each( function() {
			accom_ids.push( $( this ).find( '.hb-accom-selected' ).data( 'accom-id' ) );
		});
		accom_ids.push( 'global' );

		for ( var i = 1; i < accom_ids.length + 1; i++ ) {
			var $options_wrapper = $booking_wrapper.find( '.hb-options-multi-accom-' + i );
			if ( accom_ids[ i - 1 ] == 'global' ) {
				$options_wrapper = $booking_wrapper.find( '.hb-options-multi-accom-global' );
			}
			$options_wrapper.find( '.hb-option' ).each( function() {
				if (
					$( this ).hasClass( 'hb-option-accom-' + accom_ids[ i - 1 ] ) ||
					$( this ).hasClass( 'hb-option-global' )
				) {
					if ( $( this ).hasClass( 'hb-quantity-option' ) ) {
						if ( $( this ).find( 'input' ).val() < 0 ) {
							$( this ).find( 'input' ).val( 0 );
						}
						options_price += parseFloat( $( this ).find( 'input' ).data( 'price' ) * $( this ).find( 'input' ).val() );
					} else if ( $( this ).hasClass( 'hb-multiple-option' ) && $( this ).find( 'input:checked' ).length ) {
						options_price += parseFloat( $( this ).find( 'input:checked' ).data( 'price' ) );
					} else if ( $( this ).hasClass( 'hb-single-option' ) && $( this ).find( 'input' ).is(':checked' ) ) {
						options_price += parseFloat( $( this ).find( 'input' ).data( 'price' ) );
					}
				}
			});
		}

		if ( options_price != 0 ) {
			options_price = format_price( options_price );
			if ( options_price < 0 ) {
				options_price = format_price( options_price * -1 );
				$booking_wrapper.find( '.hb-options-total-price .hb-price-placeholder-minus' ).css( 'display', 'inline' );
			} else {
				$booking_wrapper.find( '.hb-options-total-price .hb-price-placeholder-minus' ).css( 'display', 'none' );
			}
			$booking_wrapper.find( '.hb-options-total-price .hb-price-placeholder' ).html( options_price );
			$booking_wrapper.find( '.hb-options-total-price' ).show();
		} else {
			$booking_wrapper.find( '.hb-options-total-price' ).hide();
		}

	}

	/* end options selection */

	/* ------------------------------------------------------------------------------------------- */

	/* coupons */

	$( '.hb-apply-coupon' ).on( 'click', function() {
		var $form = $( this ).parents( '.hb-booking-details-form' );
		$form.find( '.hb-coupon-error, .hb-coupon-msg' ).slideUp();
		if ( ! $form.find( '.hb-coupon-code' ).val() ) {
			$form.find( '.hb-coupon-msg' ).html( hb_text.no_coupon ).slideDown();
			$form.find( '.hb-pre-validated-coupon-id' ).val( '' );
			set_summary( $form.parents( '.hbook-wrapper' ) );
			return false;
		}
		$( this ).prop( 'disabled', true ).blur();
		$form.find( '.hb-processing-coupon' ).show();
		$.ajax({
			data: {
				'action': 'hb_verify_coupon',
				'check_in': $form.find( '.hb-details-check-in' ).val(),
				'check_out': $form.find( '.hb-details-check-out' ).val(),
				'accom_ids': $form.find( '.hb-details-accom-ids' ).val(),
				'coupon_code': $form.find( '.hb-coupon-code' ).val(),
			},
			success: function( response ) {
				coupon_verify_result( response, $form );
			},
			type : 'POST',
			timeout: hb_booking_form_data.ajax_timeout,
			url: hb_booking_form_data.ajax_url,
			error: function( jqXHR, textStatus, errorThrown ) {
				$form.find( '.hb-processing-coupon' ).hide();
				$form.find( '.hb-coupon-error' ).html( hb_text.connection_error ).slideDown();
				$form.find( '.hb-apply-coupon' ).prop( 'disabled', false );
				console.log( jqXHR );
				console.log( textStatus );
				console.log( errorThrown );
			}
		});
		return false;
	});

	function coupon_verify_result( response_text, $form ) {
		$form.find( '.hb-apply-coupon' ).prop( 'disabled', false );
		$form.find( '.hb-processing-coupon' ).hide();
		try {
			var response = JSON.parse( response_text );
		} catch ( e ) {
			$form.find( '.hb-coupon-error' ).html( 'An error occured. ' + response_text ).slideDown();
			return false;
		}
		if ( response['success'] ) {
			$form.find( '.hb-pre-validated-coupon-id' ).val( response['coupon_id'] );
		} else {
			$form.find( '.hb-pre-validated-coupon-id' ).val( '' );
		}
		$form.find( '.hb-coupon-msg' ).html( response['msg'] ).slideDown();
		set_summary( $form.parents( '.hbook-wrapper' ) );
	}

	/* end coupons */

	/* ------------------------------------------------------------------------------------------- */

	/* admin global discount */

	$( '#hb-global-discount-amount, input[name="hb-global-discount-amount-type"]' ).on( 'change', function() {
		summary_on_discount_change();
	});

	$( '#hb-global-discount-amount' ).on( 'keyup', function() {
		summary_on_discount_change();
	});

	var summary_reload_timer;

	function summary_on_discount_change() {
		var $booking_wrapper =  $( '.hbook-wrapper' );
		$booking_wrapper.find( '.hb-summary-wrapper' ).hide();
		$booking_wrapper.find( '.hb-loading-summary' ).show();
		clearTimeout( summary_reload_timer );
		summary_reload_timer = setTimeout( function() {
			set_summary_on_discount_change( $booking_wrapper );
		}, 500 );
	}

	/* end admin global discount */

	/* ------------------------------------------------------------------------------------------- */

	/* update payment info */

	function update_payment_info( $booking_wrapper ) {
		var $payment_data = $booking_wrapper.find( '.hb-payment-data-summary' );
		if ( $payment_data.data( 'null-price' ) == 'yes' ) {
			$booking_wrapper.find( '.hb-payment-info-wrapper' ).hide();
			$booking_wrapper.find( 'input.hb-payment-type-null-price' ).prop( 'checked', true );
		} else {
			var charged_total_price = $payment_data.data( 'charged-total-price' );
			var charged_deposit = $payment_data.data( 'charged-deposit' );
			var charged_total_minus_deposit = $payment_data.data( 'charged-total-minus-deposit' );
			$booking_wrapper.find( '.hb-payment-info-wrapper' ).show();
			$booking_wrapper.find( '.hb-payment-type-explanation-full_amount' ).html( charged_total_price );
			$booking_wrapper.find( '.hb-payment-type-explanation-deposit_amount' ).html( charged_deposit );
			$booking_wrapper.find( '.hb-payment-type-explanation-full_minus_deposit_amount' ).html( charged_total_minus_deposit );
			if (
				( charged_total_price == charged_deposit ) &&
				( $booking_wrapper.find( '.hb-payment-type-deposit-wrapper, .hb-payment-type-full-wrapper' ).length == 2 )
			) {
				$booking_wrapper.find( '.hb-payment-type-deposit-wrapper' ).hide();
				if ( $booking_wrapper.find( 'input[name="hb-payment-type"]:checked' ).val() == 'deposit' ) {
					$booking_wrapper.find( 'input[name="hb-payment-type"]' ).each( function() {
						if ( $( this ).val() != 'deposit' ) {
							$( this ).prop( 'checked', true );
							return false;
						}
					});
				}
				if ( $booking_wrapper.find( '.hb-payment-type-wrapper' ).length == 2 ) {
					$booking_wrapper.find( '.hb-payment-type-multiple-choice' ).hide();
				}
			} else {
				$booking_wrapper.find( 'input[name="hb-payment-type"]' ).first().prop( 'checked', true );
				$booking_wrapper.find( '.hb-payment-type-multiple-choice, .hb-payment-type-deposit-wrapper' ).show();
			}
			hide_show_payment_explanation( $booking_wrapper.find( '.hb-booking-details-form' ) );
		}
	}

	/* end update payment info */

	/* ------------------------------------------------------------------------------------------- */

	/* summary */

	function set_summary_on_discount_change( $booking_wrapper ) {
		set_summary( $booking_wrapper, true );
	}

	function set_summary( $booking_wrapper, on_discount_change ) {
		$booking_wrapper.find( '.hb-summary-wrapper' ).hide();
		$booking_wrapper.find( '.hb-loading-summary' ).show();
		var accom_ids = $booking_wrapper.find( '.hb-details-accom-ids' ).val().split( '-' );
		if ( accom_ids.length > 1 ) {
			$( '.hb-global-discount-wrapper' ).css( 'display', 'none' );
		} else {
			$( '.hb-global-discount-wrapper' ).css( 'display', 'block' );
		}
		var $details_form = $booking_wrapper.find( '.hb-booking-details-form' );
		var $options_form = $booking_wrapper.find( '.hb-options-form' );
		var $accom_num_form = $booking_wrapper.find( '.hb-select-accom-num-form' );
		var $forms;
		$details_form.find( 'input[name="action"]' ).val( 'hb_get_summary');
		$forms = $details_form.add( $options_form );
		$forms = $forms.add( $accom_num_form );
		$.ajax({
			data: $forms.serialize(),
			success: function( response ) {
				$booking_wrapper.find( '.hb-loading-summary' ).hide();
				$booking_wrapper.find( '.hb-summary-wrapper' ).html( response );
				hb_format_date();
				$booking_wrapper.find( '.hb-summary-wrapper' ).slideDown();
				if ( hb_booking_form_data.is_admin != 'yes' ) {
					update_payment_info( $booking_wrapper );
				} else {
					if ( ! on_discount_change ) {
						if ( $booking_wrapper.find( '.hb-discount-data-summary' ).length ) {
							$( '#hb-global-discount-amount' ).val( $booking_wrapper.find( '.hb-discount-data-summary' ).data( 'discount-amount' ) );
							if ( $booking_wrapper.find( '.hb-discount-data-summary' ).data( 'discount-amount-type' ) == 'fixed' ) {
								$( '#hb-global-discount-amount-type-fixed' ).prop( 'checked', true );
							} else {
								$( '#hb-global-discount-amount-type-percent' ).prop( 'checked', true );
							}
						} else {
							$( '#hb-global-discount-amount' ).val( 0 );
							$( '#hb-global-discount-amount-type-fixed' ).prop( 'checked', true );
						}
					}
				}
				$details_form.find( 'input[name="action"]' ).val( 'hb_create_resa' );
			},
			type : 'POST',
			timeout: hb_booking_form_data.ajax_timeout,
			url: hb_booking_form_data.ajax_url,
			error: function( jqXHR, textStatus, errorThrown ) {
				$booking_wrapper.find( '.hb-loading-summary' ).hide();
				$details_form.find( 'input[name="action"]' ).val( 'hb_create_resa' );
				alert( hb_text.connection_error );
				console.log( jqXHR );
				console.log( jqXHR.responseText );
				console.log( textStatus );
				console.log( errorThrown );
			}
		});
	}

	$( '.hb-summary-wrapper' ).on( 'click', '.hb-summary-view-price-breakdown', function() {
		var $self = $( this );
		$self.blur();
		$self.parents( '.hb-summary-multi-accom-accom' ).find( '.hb-summary-price-details' ).slideToggle( function() {
			if ( $( this ).is( ':visible' ) ) {
				$self.find( '.hb-summary-price-breakdown-hide-text' ).show();
				$self.find( '.hb-summary-price-breakdown-show-text' ).hide();
			} else {
				$self.find( '.hb-summary-price-breakdown-hide-text' ).hide();
				$self.find( '.hb-summary-price-breakdown-show-text' ).show();
			}
		});
		return false;
	});

	/* end summary */

	/* ------------------------------------------------------------------------------------------- */

	/* details form info */

	function toggle_accom_not_available_msg( $booking_wrapper, accom_id ) {
		if (
			( hb_booking_form_data.is_admin != 'yes' ) ||
			( $booking_wrapper.find( '.hb-admin-search-type' ).val() == 'multiple_accom' ) ||
			$booking_wrapper.find( '.hb-accom-id-' + accom_id ).hasClass( 'hb-accom-available' )
		) {
			$( '#hb-resa-customer-details-wrap, .hb-confirm-area' ).slideDown();
			$( '.hb-accom-not-available-msg' ).slideUp();
		} else {
			$( '#hb-resa-customer-details-wrap, .hb-confirm-area' ).slideUp();
			$( '.hb-accom-not-available-msg' ).slideDown();
		}
	}

	function set_details_form_info( $booking_wrapper, accom_ids ) {
		$booking_wrapper.find( '.hb-details-check-in' ).val( $booking_wrapper.find( '.hb-check-in-hidden' ).val() );
		$booking_wrapper.find( '.hb-details-check-out' ).val( $booking_wrapper.find( '.hb-check-out-hidden' ).val() );
		$booking_wrapper.find( '.hb-details-adults' ).val( $booking_wrapper.find( '.hb-booking-nb-adults' ).html() );
		$booking_wrapper.find( '.hb-details-children' ).val( $booking_wrapper.find( '.hb-booking-nb-children' ).html() );
		$booking_wrapper.find( '.hb-details-accom-ids' ).val( accom_ids );
		$booking_wrapper.find( '.hb-details-is-admin' ).val( hb_booking_form_data.is_admin );
	}

	/* end details form info */

	/* ------------------------------------------------------------------------------------------- */

	/* details form validation */

	if ( ( typeof hb_booking_form_data !== 'undefined' ) && ( hb_booking_form_data.is_admin != 'yes' ) && $( '.hb-booking-details-form' ).length ) {
		$.validate({
			form: '.hb-booking-details-form',
			validateOnBlur: false,
			language: {
				badEmail: hb_text.invalid_email,
				requiredField: hb_text.required_field,
				requiredFields: hb_text.required_field,
				groupCheckedTooFewStart: hb_text.required_field + '<span style="display: none">',
				groupCheckedEnd: '</span>',
				badInt: hb_text.invalid_number
			},
			borderColorOnError: false,
			scrollToTopOnError: false,
			onError: function( $form ) {
				$form.find( '.hb-confirm-button input' ).blur();
				$( 'html, body' ).animate({	scrollTop: $( '.has-error' ).first().offset().top - page_padding_top }, 400 );
			},
			onSuccess: function( $form ) {
				submit_booking_details( $form );
				return false;
			}
		});
	}

	if ( ( typeof hb_booking_form_data !== 'undefined' ) && ( hb_booking_form_data.is_admin == 'yes' ) ) {
		$( '.hb-booking-details-form' ).submit( function() {
			$( this ).find( 'input[type="submit"]' ).blur();
			if ( $( '#hb-admin-customer-type-id:checked' ).length ) {
				if ( ! $( '#hb-add-resa-customer-id-list' ).val() ) {
					alert( hb_text.customer_not_selected );
					return false;
				}
			}
			submit_booking_details( $( this ) );
			return false;
		});
	}

	/* end details form validation */

	/* ------------------------------------------------------------------------------------------- */

	/* save reservation details */

	function submit_booking_details( $form ) {
		$form.find( '.hb-confirm-button input' ).blur();

		if ( $form.hasClass( 'submitted' ) ) {
			return false;
		}

		var policies_error = '';
		$form.find( '.hb-policies-error, .hb-confirm-error' ).hide();
		if ( $form.find( 'input[name="hb_terms_and_cond"]' ).length && ! $form.find( 'input[name="hb_terms_and_cond"]' ).prop( 'checked' ) ) {
			policies_error = hb_text.terms_and_cond_error;
		}
		if ( $form.find( 'input[name="hb_privacy_policy"]' ).length && ! $form.find( 'input[name="hb_privacy_policy"]' ).prop( 'checked' ) ) {
			if ( policies_error ) {
				policies_error += '<br/>';
			}
			policies_error += hb_text.privacy_policy_error;
		}
		if ( policies_error ) {
			$form.find( '.hb-policies-error' ).html( policies_error ).slideDown();
			$( 'html, body' ).animate({
				scrollTop: $form.find( '.hb-policies-area' ).offset().top - page_padding_top
			}, 1000 );
			return false;
		}

		var payment_type = $form.find( 'input[name="hb-payment-type"]:checked' ).val(),
			payment_processing = false;

		if ( payment_type == 'store_credit_card' || payment_type == 'deposit' || payment_type == 'full' ) {
			$form.find( '.hb-payment-flag' ).val( 'yes' );
			var gateway_id = $form.find( 'input[name="hb-payment-gateway"]:checked' ).val(),
				payment_process_function = 'hb_' + gateway_id + '_payment_process';
			if ( ! gateway_id ) {
				alert( 'Error: all payment gateways are inactive.' );
				return;
			}
			if ( typeof window[ payment_process_function ] == 'function' ) {
				disable_form_submission( $form );
				payment_processing = window[ payment_process_function ]( $form, save_resa_details );
				if ( ! payment_processing ) {
					enable_form_submission( $form );
					return;
				}
			}
		} else {
			$form.find( '.hb-payment-flag' ).val( '' );
		}

		if ( ! payment_processing ) {
			$form.find( '.hb-saving-resa' ).slideDown();
			save_resa_details( $form );
		}
	}

	function save_resa_details( $form ) {
		disable_form_submission( $form );
		var $options_form = $form.parents( '.hbook-wrapper' ).find( '.hb-options-form' ),
			$accom_num_form = $form.parents( '.hbook-wrapper' ).find( '.hb-select-accom-num-form' ),
			$forms;
		$forms = $form.add( $options_form );
		$forms = $forms.add( $accom_num_form );
		$.ajax({
			data: $forms.serialize(),
			success: function( response ) {
				after_form_details_submit( response, $form );
			},
			type : 'POST',
			timeout: hb_booking_form_data.ajax_timeout,
			url: hb_booking_form_data.ajax_url,
			error: function( jqXHR, textStatus, errorThrown ) {
				$form.find( '.hb-saving-resa, .hb-confirm-error, .hb-policies-error' ).slideUp();
				$form.find( '.hb-confirm-error' ).html( hb_text.connection_error ).slideDown();
				console.log( jqXHR );
				console.log( jqXHR.responseText );
				console.log( textStatus );
				console.log( errorThrown );
				enable_form_submission( $form );
			}
		});
	}

	function after_form_details_submit( response_text, $form ) {
		try {
			var response = JSON.parse( response_text );
		} catch ( e ) {
			enable_form_submission( $form );
			$form.find( '.hb-saving-resa' ).slideUp();
			$form.find( '.hb-confirm-error' ).html( response_text ).slideDown();
			return false;
		}
		if ( response['success'] ) {
			var payment_type = $form.find( 'input[name="hb-payment-type"]:checked' ).val(),
				payment_has_redirection = $form.find( 'input[name="hb-payment-gateway"]:checked' ).data( 'has-redirection' );
			if ( ( payment_type == 'deposit' || payment_type == 'full' ) && ( payment_has_redirection == 'yes' ) ) {
				var gateway_id = $form.find( 'input[name="hb-payment-gateway"]:checked' ).val(),
					payment_process_redirection = 'hb_' + gateway_id + '_payment_redirection';
				window[ payment_process_redirection ]( $form, response );
			} else {
				$form.find( '.hb-saving-resa' ).slideUp();
				if ( hb_booking_form_data.is_admin != 'yes' ) {
					var $thank_you_page_form = $form.parents( '.hbook-wrapper' ).find( '.hb-thank-you-page-form' );
					if ( $thank_you_page_form.length ) {
						$( 'body' ).addClass( 'no-hbook-unload' );
						$thank_you_page_form.find( '.hb-resa-id' ).val( response.resa_id );
						$thank_you_page_form.find( '.hb-resa-is-parent' ).val( response.resa_is_parent );
						$thank_you_page_form.find( '.hb-resa-payment-type' ).val( payment_type );
						$thank_you_page_form.submit();
						return;
					}
					$form.find( '.hb-resa-done-email' ).html( $form.find( 'input[name="hb_email"]' ).val() );
					$( 'html, body' ).animate({ scrollTop: $form.parents( '.hbook-wrapper' ).offset().top - page_padding_top }, 1000, function() {
						$form.parents( '.hbook-wrapper' ).find( '.hb-booking-search-form, .hb-accom-list, .hb-step-button, .hb-details-fields, .hb-coupons-area, .hb-confirm-area, .hb-policies-area, .hb-payment-info-wrapper, .hb-resa-summary' ).fadeOut( 1000, function() {
							if ( payment_type == 'deposit' || payment_type == 'full' ) {
								$form.find( '.hb-resa-payment-msg' ).show();
							} else {
								$form.find( '.hb-resa-done-msg' ).show();
							}
							$form.find( '.hb-resa-summary' ).slideDown();
						});
					});
					if ( typeof window['hbook_reservation_done'] == 'function' ) {
						window['hbook_reservation_done']();
					}
				} else {
					hb_new_admin_resas = response['resas'];
					enable_form_submission( $form );
					change_search( $form.parents( '.hbook-wrapper' ) );
					$( '#hb-process-new-admin-resa' ).submit();
				}
			}
		} else {
			if ( response['error_msg'] == 'payment_requires_action' ) {
				var gateway_id = $form.find( 'input[name="hb-payment-gateway"]:checked' ).val(),
					payment_requires_action = 'hb_' + gateway_id + '_payment_requires_action';
				window[ payment_requires_action ]( $form, response );
			} else {
				enable_form_submission( $form );
				$form.find( '.hb-saving-resa' ).slideUp();
				$form.find( '.hb-confirm-error' ).html( response['error_msg'] ).slideDown();
			}
		}
	}

	/* end save reservation details */

	/* ------------------------------------------------------------------------------------------- */

	/* external payment confirmation */

	if ( $( '#hb-resa-confirm-done' ).length ) {
		$( '#hb-resa-confirm-done' ).parent('.hb-accom-list-booking-form' ).show();
		hb_format_date();
		if ( typeof window['hbook_reservation_done'] == 'function' ) {
			window['hbook_reservation_done']();
		}
		$( 'html, body' ).animate({ scrollTop: $( '#hb-resa-confirm-done' ).offset().top - page_padding_top }, 1000 );
	}

	/* end external payment confirmation */

	/* ------------------------------------------------------------------------------------------- */

	/* payment type and method init */

	$( '.hb-booking-details-form' ).each( function() {
		$( this ).find( 'input[name="hb-payment-type"]' ).first().prop( 'checked', true );
		$( this ).find( 'input[name="hb-payment-gateway"]' ).first().prop( 'checked', true );
		hide_show_payment_explanation( $( this ) );
		hide_show_payment_gateway_choice( $( this ) );
		hide_show_payment_gateway_form( $( this ) );
		hide_show_bottom_area( $( this ) );
	});

	/* end payment type and method init */

	/* ------------------------------------------------------------------------------------------- */

	/* payment gateway choice */

	$( 'input[name="hb-payment-type"]' ).on( 'change', function() {
		hide_show_payment_explanation( $( this ).parents( 'form' ) );
		hide_show_payment_gateway_choice( $( this ).parents( 'form' ) );
		hide_show_bottom_area( $( this ).parents( 'form' ) );
	});

	$( 'input[name="hb-payment-gateway"]' ).on( 'change', function() {
		hide_show_payment_gateway_form( $( this ).parents( 'form' ) );
		hide_show_bottom_area( $( this ).parents( 'form' ) );
	});

	function hide_show_payment_explanation( $form ) {
		var payment_type = $form.find( 'input[name="hb-payment-type"]:checked' ).val();
		$form.find( '.hb-payment-type-explanation' ).hide();
		$form.find( '.hb-payment-type-explanation-' + payment_type ).slideDown();
	}

	function hide_show_payment_gateway_choice( $form ) {
		var payment_type = $form.find( 'input[name="hb-payment-type"]:checked' ).val();
		if ( payment_type == 'store_credit_card' || payment_type == 'deposit' || payment_type == 'full' ) {
			$form.find( '.hb-payment-method-wrapper' ).slideDown();
		} else {
			$form.find( '.hb-payment-method-wrapper' ).slideUp();
		}
		if ( payment_type == 'store_credit_card' ) {
			$form.find( '.hb-payment-method' ).slideUp();
			$form.find( 'input[name="hb-payment-gateway"][value="stripe"]' ).prop( 'checked', true );
			if ( $form.find( '.hb-payment-form-stripe' ).css( 'display' ) == 'none' ) {
				$form.find( '.hb-payment-form' ).slideUp();
				$form.find( '.hb-payment-form-stripe' ).slideDown();
			}
		} else {
			$form.find( '.hb-payment-method' ).slideDown();
		}
	}

	function hide_show_payment_gateway_form( $form ) {
		$form.find( '.hb-payment-form' ).slideUp();
		var gateway_id = $form.find( 'input[name="hb-payment-gateway"]:checked' ).val();
		$form.find( '.hb-payment-form-' + gateway_id ).slideDown();
	}

	function hide_show_bottom_area( $form ) {
		var payment_type = $form.find( 'input[name="hb-payment-type"]:checked' ).val();
		if ( payment_type == 'deposit' || payment_type == 'full' ) {
			var gateway_id = $form.find( 'input[name="hb-payment-gateway"]:checked' ).val();
			if ( $form.find( '.hb-bottom-area-content-' + gateway_id ).length ) {
				var bottom_area_content = $form.find( '.hb-bottom-area-content-' + gateway_id ).html();
				$form.find( '.hb-bottom-area' ).html( bottom_area_content ).slideDown();
			} else {
				$form.find( '.hb-bottom-area' ).slideUp();
			}
		} else {
			$form.find( '.hb-bottom-area' ).slideUp();
		}
	}

	/* end payment gateway choice */

	/* ------------------------------------------------------------------------------------------- */

	/* misc */

	function format_price( price ) {
		if ( hb_booking_form_data.price_precision == 'no_decimals' ) {
			var formatted_price = Math.round( price );
		} else {
			var formatted_price = parseFloat( price ).toFixed( 2 );
		}
		var price_parts = formatted_price.toString().split( '.' );
		if ( hb_booking_form_data.thousands_sep ) {
			price_parts[0] = price_parts[0].replace( /\B(?=(\d{3})+(?!\d))/g, hb_booking_form_data.thousands_sep );
		}
		return price_parts.join( hb_booking_form_data.decimal_point );
	}

	function disable_form_submission( $form ) {
		$form.addClass( 'submitted' );
		$form.find( 'input[type="submit"]' ).prop( 'disabled', true );
	}

	function enable_form_submission( $form ) {
		$form.removeClass( 'submitted' );
		$form.find( 'input[type="submit"]' ).prop( 'disabled', false );
	}

	function debouncer( func ) {
		var timeoutID,
			timeout = 50;
		return function () {
			var scope = this,
				args = arguments;
			clearTimeout( timeoutID );
			timeoutID = setTimeout( function () {
				func.apply( scope, Array.prototype.slice.call( args ) );
			}, timeout );
		}
	}

	function resize_forms() {
		$( '.hb-booking-search-form' ).each( function() {
			var body_class = '';
			if ( $( this ).attr( 'id' ) != '' ) {
				body_class = 'hb-' + $( this ).attr('id') + '-is-vertical';
			}
			if ( $( this ).width() < hb_booking_form_data.horizontal_form_min_width ) {
				$( this ).addClass( 'hb-vertical-search-form' );
				$( this ).removeClass( 'hb-horizontal-search-form' );
				$( 'body' ).addClass( body_class );
			} else {
				$( this ).removeClass( 'hb-vertical-search-form' );
				$( this ).addClass( 'hb-horizontal-search-form' );
				$( 'body' ).removeClass( body_class );
			}
			if ( $( this ).width() < 400 ) {
				$( this ).addClass( 'hb-narrow-search-form' );
			} else {
				$( this ).removeClass( 'hb-narrow-search-form' );
			}
		});
		$( '.hb-booking-details-form' ).each( function() {
			if ( $( this ).width() < hb_booking_form_data.details_form_stack_width ) {
				$( this ).addClass( 'hb-details-form-stacked' );
			} else {
				$( this ).removeClass( 'hb-details-form-stacked' );
			}
		});
	}

	function resize_price_caption() {
		$( '.hb-accom-list' ).each( function() {
			if ( $( this ).width() < 600 ) {
				$( this ).find( '.hb-accom-price-caption br' ).show();
				$( this ).find( '.hb-accom-price-caption-dash' ).hide();
				$( this ).find( '.hb-accom-price-caption' ).addClass( 'hb-accom-price-caption-small' );
			} else {
				$( this ).find( '.hb-accom-price-caption br' ).hide();
				$( this ).find( '.hb-accom-price-caption-dash' ).show();
				$( this ).find( '.hb-accom-price-caption' ).removeClass( 'hb-accom-price-caption-small' );
			}
		});
	}

	function reposition_people_popup() {
		var $people_popup = $( '.hb-people-popup-wrapper-visible' );
		if ( $people_popup.length ) {
			var $accom_people_selection = $people_popup.find( '.hb-multi-accom-people-selection-wrapper' );
			if ( hb_booking_form_data.is_admin == 'yes' ) {
				var $select = $( '.hb-booking-search-form .hb-accom-number' );
			} else {
				var form_num = $accom_people_selection.data( 'form-num' );
				var $select = $( '.hb-booking-search-form.hb-form-' + form_num + ' .hb-accom-number' );
			}
			var scroll_y = $( window ).scrollTop();
			var select_top = $select.offset().top;
			var select_bottom = select_top + $select.outerHeight( true );
			var select_left = $select.offset().left;
			var available_space_above = select_top - scroll_y;
			var available_space_below = $( window ).height() - select_bottom + scroll_y;
			var people_popup_height = $( '.hb-people-popup-wrapper' ).outerHeight( true );
			if ( available_space_below > people_popup_height ) {
				$( '.hb-people-popup-wrapper' ).css( 'top', select_bottom );
			} else if ( available_space_above > people_popup_height ) {
				$( '.hb-people-popup-wrapper' ).css( 'top', select_top - people_popup_height );
			} else {
				$( '.hb-people-popup-wrapper' ).css( 'top', scroll_y );
			}
			$( '.hb-people-popup-wrapper' ).css( 'left', select_left );
		}
	}

	$( window ).resize( debouncer ( function () {
		resize_forms();
		resize_price_caption();
		reposition_people_popup();
	})).resize();

	$( window ).on( 'beforeunload', function() {
		if ( ! $( 'body' ).hasClass( 'no-hbook-unload' ) ) {
			$( '.hb-accom-list, .hb-booking-details-form' ).each( function() {
				if ( $( this ).is( ':visible' ) ) {
					var scroll_top = $( window ).scrollTop();
					var offset = $( this ).offset().top;
					var height = $( this ).outerHeight( true );
					$( this ).hide();
					if ( scroll_top > offset + height ) {
						$( 'html, body' ).scrollTop( scroll_top - height );
					} else if ( scroll_top > offset ) {
						$( 'html, body' ).scrollTop( offset );
					}
				}
			});
		}
	});

	/* end misc */

	/* ------------------------------------------------------------------------------------------- */

	/* status processing */

	$( '.hbook-wrapper-booking-form' ).each( function() {
		var $booking_wrapper = $( this ),
			$search_form = $booking_wrapper.find( '.hb-booking-search-form' );
		if ( $search_form.find( '.hb-check-in-hidden' ).val() != '' ) {
			var check_in = hb_date_str_2_obj( $search_form.find( '.hb-check-in-hidden' ).val() ),
				check_out = hb_date_str_2_obj( $search_form.find( '.hb-check-out-hidden' ).val() );
			check_in = $.datepick.formatDate( hb_date_format, check_in );
			check_out = $.datepick.formatDate( hb_date_format, check_out );
			$search_form.find( '.hb-check-in-date' ).val( check_in );
			$search_form.find( '.hb-check-out-date' ).val( check_out );
			$search_form.find( 'select.hb-adults' ).val( $search_form.find( '.hb-adults-hidden' ).val() );
			$search_form.find( 'select.hb-children' ).val( $search_form.find( '.hb-children-hidden' ).val() );
		}
                
                var accom_people = '';
                if($( '.hb-accom-people' ).length > 0)
                    accom_people = $( '.hb-accom-people' ).val();
		if ( accom_people != '' ) {
			accom_people = accom_people.split( ',' );
			$search_form.find( 'select.hb-accom-number' ).val( accom_people.length );
			for ( var i = 0; i < accom_people.length; i++ ) {
				var multi_accom_num = i + 1;
				var accom_adults = accom_people[i].split( '-' )[0];
				var accom_children = accom_people[i].split( '-' )[1];
				$search_form.find( '.hb-multi-accom-people-selection-accom-' + multi_accom_num + ' .hb-multi-accom-adults' ).val( accom_adults );
				$search_form.find( '.hb-multi-accom-people-selection-accom-' + multi_accom_num + ' .hb-multi-accom-children' ).val( accom_children );
			}
			update_accom_people( $search_form );
		} else {
			setTimeout( function() {
				if ( $search_form.find( 'select.hb-accom-number option[value="-1"]' ).length ) {
					$search_form.find( 'select.hb-accom-number' ).val( -1 );
					$search_form.find( '.hb-accom-people' ).val( '' );
				}
			}, 1000 );
		}

		if ( $booking_wrapper.data( 'status' ) == 'search-accom' ) {
			$( 'html, body' ).animate({ scrollTop: $search_form.offset().top - page_padding_top }, function() {
				$search_form.submit();
			});
			return false;
		}

		if ( $booking_wrapper.data( 'status' ) == 'external-payment-cancel' ) {
			// $search_form.submit();
			// return false;
		}

		if ( $booking_wrapper.data( 'status' ) == 'external-payment-timeout' ) {
			alert( hb_text.timeout_error );
		}

		if ( $booking_wrapper.data( 'status' ) == 'external-payment-confirm-error' ) {
			alert( hb_payment_confirmation_error );
		}
	});

	/* end status processing */

	/* ------------------------------------------------------------------------------------------- */

});