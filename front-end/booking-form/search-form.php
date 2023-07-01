<?php
class HbSearchForm {

	private $utils;
	private $hbdb;
	private $hb_strings;

	public function __construct( $hbdb, $utils, $hb_strings ) {
		$this->utils = $utils;
		$this->hbdb = $hbdb;
		$this->hb_strings = $hb_strings;
	}

	public function get_search_form_markup( $form_id, $form_action, $search_only_data, $search_form_placeholder, $is_admin, $check_in = '' , $check_out = '', $adults = '', $children = '', $accom_id = '', $options = '', $accom_num = '', $accom_people = '', $atts = array() ) {
		if ( $is_admin == 'yes' ) {
			$this->hb_strings['accom_number_any'] = esc_html__( 'Any', 'hbook-admin' );
			$this->hb_strings['multi_accom_accom_n'] = esc_html__( 'Accommodation %n', 'hbook-admin' );
			$this->hb_strings['adults'] = esc_html__( 'Adults', 'hbook-admin' );
			$this->hb_strings['children'] = esc_html__( 'Children', 'hbook-admin' );
			$this->hb_strings['accom_number'] = esc_html__( 'Number of accommodation', 'hbook-admin' );
			$this->hb_strings['check_in'] = esc_html__( 'Check-in', 'hbook-admin' );
			$this->hb_strings['check_out'] = esc_html__( 'Check-out', 'hbook-admin' );
			$this->hb_strings['chosen_check_in'] = esc_html__( 'Check-in:', 'hbook-admin' );
			$this->hb_strings['chosen_check_out'] = esc_html__( 'Check-out:', 'hbook-admin' );
			$this->hb_strings['chosen_accom_number'] = esc_html__( 'Number of accommodation:', 'hbook-admin' );
			$this->hb_strings['chosen_adults'] = esc_html__( 'Adults:', 'hbook-admin' );
			$this->hb_strings['chosen_children'] = esc_html__( 'Children:', 'hbook-admin' );
			$this->hb_strings['change_search_button'] = esc_html__( 'Change search', 'hbook-admin' );
		}
                if((isset($atts['region_filter']) && $atts['region_filter'] == 'yes'))
                    $this->hb_strings['chosen_adults'] = esc_html__( 'Guests:', 'hbook-admin' );
		$people_selects = array(
			'adults' => '',
			'children' => ''
		);
		foreach ( $people_selects as $key => $markup ) {
			$loop_start = '';
			$loop_end = '';
			if ( $key == 'adults' ) {
				if (
					( get_option( 'hb_display_children_field' ) == 'no' ) &&
					( get_option( 'hb_minimum_occupancy_search_fields_on_accom_page' ) == 'yes' )
				) {
					$loop_start = get_post_meta( $accom_id, 'accom_min_occupancy', true );
				}
				if ( ! $loop_start ) {
					$loop_start = get_option( 'hb_minimum_adults' );
				}
				if ( $accom_id && ( get_option( 'hb_maximum_occupancy_search_fields_on_accom_page' ) == 'yes' ) ) {
					$loop_end = get_post_meta( $accom_id, 'accom_max_occupancy', true );
				}
				if ( ! $loop_end ) {
					$loop_end = get_option( 'hb_maximum_adults' );
				}
                                $selected_val = $adults;
			} else {
				$loop_start = 0;
				if ( $accom_id && ( get_option( 'hb_maximum_occupancy_search_fields_on_accom_page' ) == 'yes' ) ) {
					$loop_end = get_post_meta( $accom_id, 'accom_max_occupancy', true );
				}
				if ( ! $loop_end ) {
					$loop_end = get_option( 'hb_maximum_children' );
				} else {
					$loop_end = $loop_end - get_option( 'hb_minimum_adults' );
				}
                                $selected_val = $children;
			}
			$markup_options = '';
			if ( $search_form_placeholder && ( $is_admin != 'yes' ) ) {
				$markup_options = '<option selected disabled>' . $this->hb_strings[ $key ] . '</option>';
			}
			for ( $i = $loop_start; $i <= $loop_end; $i++ ) {
                                $selected_option = ($i == $selected_val) ? 'selected' : '';
				$markup_options .= '<option value="' . $i . '" ' . $selected_option . '>' . $i . '</option>';
			}
			$people_selects[ $key ] = '<select id="' . $key . '" name="hb-' . $key . '" class="hb-' . $key . '">' . $markup_options . '</select>';
		}

		$accom_number_select = '<select id="accom-number" name="hb-accom-number" class="hb-accom-number">';
		if ( $search_form_placeholder && ( $is_admin != 'yes' ) ) {
			$accom_number_select .= '<option selected disabled value="-1">' . $this->hb_strings['accom_number'] . '</option>';
		}
		if ( get_option( 'hb_display_adults_field' ) == 'yes' ) {
			$accom_number_select .= '<option disabled value="0"';
			if ( ! $search_form_placeholder ) {
				$accom_number_select .= ' selected';
			}
			$accom_number_select .= '></option>';
		}
		$accom_number_select .= '<option value="any">' . $this->hb_strings['accom_number_any'] . '</option>';
		if ( $is_admin == 'yes' ) {
			$maximum_accom_number = 10;
		} else {
			$maximum_accom_number = get_option( 'hb_maximum_accom_number' );
		}
		for ( $i = 1; $i <= $maximum_accom_number; $i++ ) {
			$accom_number_select .= '<option value="' . $i . '">' . $i . '</option>';
		}
		$accom_number_select .= '</select>';

		$accom_people_selection = '<div class="hb-multi-accom-people-selection-wrapper">';
		$accom_people_selection .= '<a class="hb-people-popup-wrapper-close" href="#">&times;</a>';
		for ( $i = 1; $i <= $maximum_accom_number; $i++ ) {
			$accom_people_selection .= '<div class="hb-multi-accom-people-selection hb-multi-accom-people-selection-accom-' . $i . ' hb-clearfix">';
			$accom_people_selection .= '<b class="hb-multi-accom-people-title">';
			$accom_people_selection .= str_replace( '%n', $i, $this->hb_strings['multi_accom_accom_n'] );
			$accom_people_selection .= '</b>';
			$accom_people_selection .= '<p class="hb-multi-accom-people-wrapper">';
			$accom_people_selection .= '<label for="hb-accom-' . $i . '-adults">' . $this->hb_strings['adults'] . '</label>';
			$accom_people_selection .= '<select id="hb-accom-' . $i . '-adults" class="hb-multi-accom-adults">';
			$loop_end = 0;
			if ( $accom_id && ( get_option( 'hb_maximum_occupancy_search_fields_on_accom_page' ) == 'yes' ) ) {
				$loop_end = get_post_meta( $accom_id, 'accom_max_occupancy', true );
			}
			if ( ! $loop_end ) {
				$loop_end = get_option( 'hb_maximum_adults' );
			}
			for ( $j = 1; $j <= $loop_end; $j++ ) {
				$accom_people_selection .= '<option>' . $j . '</option>';
			}
			$accom_people_selection .= '</select>';
			$accom_people_selection .= '</p>';
			$accom_people_selection .= '<p class="hb-multi-accom-people-wrapper hb-multi-accom-children-wrapper">';
			$accom_people_selection .= '<label for="hb-accom-' . $i . '-children">' . $this->hb_strings['children'] . '</label>';
			$accom_people_selection .= '<select id="hb-accom-' . $i . '-children" class="hb-multi-accom-children">';
			$loop_end = 0;
			if ( $accom_id && ( get_option( 'hb_maximum_occupancy_search_fields_on_accom_page' ) == 'yes' ) ) {
				$loop_end = get_post_meta( $accom_id, 'accom_max_occupancy', true );
			}
			if ( ! $loop_end ) {
				$loop_end = get_option( 'hb_maximum_children' );
			}
			for ( $j = 0; $j <= $loop_end; $j++ ) {
				$accom_people_selection .= '<option>' . $j . '</option>';
			}
			$accom_people_selection .= '</select>';
			$accom_people_selection .= '</p>';
			$accom_people_selection .= '</div>';
		}
		$accom_people_selection .= '</div>';

		if ( $accom_id ) {
			$form_title = str_replace( '%accom_name', $this->utils->get_accom_title( $accom_id ), $this->hb_strings['accom_page_form_title'] );
		} else {
			$form_title = $this->hb_strings['default_form_title'];
		}
		if ( $form_title != '' ) {
			$form_title = apply_filters( 'hb_search_form_title', '<h3 class="hb-title hb-title-search-form">' . $form_title . '</h3>' );
		}

		$form_class = 'hb-booking-search-form';
                $loader_hide_class = '';
		if (
			( ! ( $accom_id && ( get_post_meta( $accom_id, 'accom_quantity', true ) == 1 ) ) ) &&
			( get_option( 'hb_multiple_accom_booking_front_end' ) == 'enabled' ) &&
			( get_option( 'hb_display_accom_number_field' ) == 'yes' )
		) {
			$form_class .= ' hb-search-form-multiple-accom';
		}
		if ( get_option( 'hb_display_adults_field' ) == 'no' ) {
			$form_class .= ' hb-search-form-no-people';
		} else if ( get_option( 'hb_display_children_field' ) == 'no' ) {
			$form_class .= ' hb-search-form-no-children';
		}
		if ( ( $is_admin == 'yes' ) && ( get_option( 'hb_multiple_accom_booking' ) != 'enabled' ) ) {
			$form_class .= ' hb-search-form-no-admin-search-type';
		}
                if ((isset($atts['search_only']) && $atts['search_only'] == 'no' && isset($atts['redirection_url']) && !$atts['redirection_url'])) {
			$form_class .= ' hb-search-result-page';
		}else{
                        $loader_hide_class = 'hb-hide';
                }
                if (isset($atts['extra_class']) && $atts['extra_class']) {
			$form_class .= ' ' . $atts['extra_class'];
		}

		$form_markup = '
                        <div class="hbook-form-wrapper"> 
                            [banner_image]
                            <form [form_id] class="[form_class]" method="POST" data-search-only="[search_only_data]" action="[form_action]">
                                    [form_title]
                                    <div class="hb-searched-summary hb-clearfix hb-hide">
                                            [chosen_region_search]
                                            <p class="hb-check-dates-wrapper hb-chosen-check-in-date hb-field-item">[string_chosen_check_in] <span></span></p>
                                            <p class="hb-check-dates-wrapper hb-chosen-check-out-date hb-field-item">[string_chosen_check_out] <span></span></p>
                                            <p class="hb-admin-search-type-wrapper hb-chosen-admin-search-type hb-field-item">[string_chosen_admin_search_type] <span></span></p>
                                            <p class="hb-accom-number-wrapper hb-chosen-accom-number hb-field-item">[string_chosen_accom_number] <span></span></p>
                                            <p class="hb-people-wrapper hb-chosen-adults hb-field-item">[string_chosen_adults] <span></span></p>
                                            <p class="hb-people-wrapper hb-chosen-children hb-field-item">[string_chosen_children] <span></span></p>
                                            [chosen_price_filter]
                                            [chosen_bedrooms_filter]
                                            [chosen_bathrooms_filter]
                                            [chosen_beds_filter]
                                            [chosen_beachfront_filter]
                                            [chosen_admin_accommodation_type]
                                            <p class="hb-change-search-wrapper hb-search-button-wrapper hb-button-wrapper">
                                                    <input type="submit" value="[string_change_search_button]" />
                                            </p>
                                    </div><!-- .hb-searched-summary -->
                                    <div class="hb-search-fields-and-submit">
                                            <div class="hb-search-fields hb-clearfix">
                                                    [region_search]
                                                    [hb_search_fields_wrap_start] 
                                                        <p class="hb-check-dates-wrapper hb-field-item">
                                                                [check_in_label]
                                                                <input id="check-in-date" name="hb-check-in-date" class="hb-input-datepicker hb-check-in-date" type="text" placeholder="[check_in_placeholder]" autocomplete="off" />
                                                                <input class="hb-check-in-hidden" name="hb-check-in-hidden" type="hidden" value="[check_in]" />
                                                                <span class="hb-datepick-check-in-out-mobile-trigger hb-datepick-check-in-mobile-trigger"></span>
                                                                <span class="hb-datepick-check-in-out-trigger hb-datepick-check-in-trigger"></span>
                                                        </p>
                                                        <p class="hb-check-dates-wrapper hb-field-item">
                                                                [check_out_label]
                                                                <input id="check-out-date" name="hb-check-out-date" class="hb-input-datepicker hb-check-out-date" type="text" placeholder="[check_out_placeholder]" autocomplete="off" />
                                                                <input class="hb-check-out-hidden" name="hb-check-out-hidden" type="hidden" value="[check_out]" />
                                                                <span class="hb-datepick-check-in-out-mobile-trigger hb-datepick-check-out-mobile-trigger"></span>
                                                                <span class="hb-datepick-check-in-out-trigger hb-datepick-check-out-trigger"></span>
                                                        </p>
                                                        [admin_search_type]
                                                        <p class="hb-accom-number-wrapper hb-field-item">
                                                                [accom_number_label]
                                                                [accom_number_select]
                                                                <input class="hb-accom-people" name="hb-accom-people" type="hidden" value="[accom_people]" />
                                                                <input class="hb-accom-people-any" name="hb-accom-people-any" type="hidden" value="no" />
                                                        </p>
                                                        <p class="hb-people-wrapper hb-people-wrapper-adults hb-field-item">
                                                                [adults_label]
                                                                [people_selects_adults]
                                                                <input class="hb-adults-hidden" type="hidden" value="[adults]" />
                                                        </p>
                                                        <p class="hb-people-wrapper hb-people-wrapper-children hb-people-wrapper-last hb-field-item">
                                                                [children_label]
                                                                [people_selects_children]
                                                                <input class="hb-children-hidden" type="hidden" value="[children]" />
                                                        </p>
                                                        [price_filter]
                                                        [bedrooms_filter]
                                                        [bathrooms_filter]
                                                        [beds_filter]
                                                        [beachfront_filter]
                                                        [admin_accommodation_type]
                                                        <p class="hb-search-submit-wrapper hb-search-button-wrapper hb-button-wrapper">
                                                                [search_label]
                                                                <input type="submit" id="hb-search-form-submit" value="[string_search_button]" />
                                                        </p> 
                                                    [hb_search_fields_wrap_end]
                                            </div><!-- .hb-search-fields -->
                                            <p class="hb-search-error">&nbsp;</p>
                                            <p class="hb-search-no-result">&nbsp;</p>
                                    </div><!-- .hb-search-fields-and-submit -->
                                    <input type="hidden" class="hb-results-show-only-accom-id" name="hb-results-show-only-accom-id" />
                                    <input type="hidden" class="hb-chosen-options" name="hb-chosen-options" value=\'[options]\' />
                                    <input type="hidden" class="hb-chosen-accom-num" name="hb-chosen-accom-num" value=\'[accom_num]\' />
                                    <input type="hidden" class="hb-page-num" name="hb-page-num" value=\'1\' />
                                    [accom_people_selection]
                                    <div class="loader-wrapper ' . $loader_hide_class . '"><div class="loader"></div></div>
                            </form><!-- end #hb-booking-search-form -->
                        </div>
                        <div class="hb-accom-list">[list_all_villas]</div>
                        ';

		if ( $is_admin == 'yes' ) {
			$accom = $this->hbdb->get_all_accom();
			$accom_options_markup = '<option value="all">' . esc_html__( 'All', 'hbook-admin' ) . '</option>';
			foreach ( $accom as $accom_id => $accom_name ) {
				$accom_options_markup .= '<option value="' . $accom_id . '">' . $accom_name . '</option>';
			}
			$admin_accommodation_type = '<p class="hb-accom-wrapper">';
			$admin_accommodation_type .= '<label>';
			$admin_accommodation_type .= esc_html__( 'Accom. type', 'hbook-admin' );
			$admin_accommodation_type .= '</label>';
			$admin_accommodation_type .= '<br/>';
			$admin_accommodation_type .= '<select class="hb-accom">';
			$admin_accommodation_type .= $accom_options_markup;
			$admin_accommodation_type .= '</select>';
			$admin_accommodation_type .= '</p>';
			$chosen_admin_accommodation_type = '<p class="hb-accom-wrapper hb-chosen-accom">';
			$chosen_admin_accommodation_type .= esc_html__( 'Accom. type:', 'hbook-admin' );
			$chosen_admin_accommodation_type .= ' <span></span>';
			$chosen_admin_accommodation_type .= '</p>';
			$admin_search_type = '<p class="hb-admin-search-type-wrapper">';
			$admin_search_type .= '<label>';
			$admin_search_type .= esc_html__( 'Multi. accom.', 'hbook-admin' );
			$admin_search_type .= '</label>';
			$admin_search_type .= '<select class="hb-admin-search-type">';
			$admin_search_type .= '<option value="single_accom">' . esc_html__( 'No', 'hbook-admin' ) . '</option>';
			$admin_search_type .= '<option value="multiple_accom">' . esc_html__( 'Yes', 'hbook-admin' ) . '</option>';
			$admin_search_type .= '</select>';
			$admin_search_type .= '</p>';

			$form_markup = str_replace( 'input type="submit"', 'input type="submit" class="button-primary"', $form_markup );
			$form_markup = str_replace( '[string_search_button]', esc_attr__( 'Check price and availability', 'hbook-admin' ), $form_markup );
			$form_markup = str_replace( '[string_searching]', '<span class="spinner"></span> ' . esc_html__( 'Searching...', 'hbook-admin' ), $form_markup );
			$form_markup = str_replace( '[string_chosen_admin_search_type]', esc_html__( 'Multi. accom.:', 'hbook-admin' ), $form_markup );
			$form_markup = str_replace( '[admin_accommodation_type]', $admin_accommodation_type, $form_markup );
			$form_markup = str_replace( '[chosen_admin_accommodation_type]', $chosen_admin_accommodation_type, $form_markup );
			$form_markup = str_replace( '[admin_search_type]', $admin_search_type, $form_markup );
		} else {
			$form_markup = str_replace( '[admin_accommodation_type]', '', $form_markup );
			$form_markup = str_replace( '[chosen_admin_accommodation_type]', '', $form_markup );
			$form_markup = str_replace( '[admin_search_type]', '', $form_markup );
			$form_markup = apply_filters( 'hb_search_form_markup', $form_markup, $form_id );
		}

		if ( $search_form_placeholder && ( $is_admin != 'yes' ) ) {
			$form_markup = str_replace( '[check_in_placeholder]', $this->hb_strings['check_in'], $form_markup );
			$form_markup = str_replace( '[check_out_placeholder]', $this->hb_strings['check_out'], $form_markup );
			$form_markup = str_replace( '[check_in_label]', '', $form_markup );
			$form_markup = str_replace( '[check_out_label]', '', $form_markup );
			$form_markup = str_replace( '[accom_number_label]', '', $form_markup );
			$form_markup = str_replace( '[adults_label]', '', $form_markup );
			$form_markup = str_replace( '[children_label]', '', $form_markup );
			$form_markup = str_replace( '[search_label]', '', $form_markup );
		} else {
			$form_markup = str_replace( '[check_in_placeholder]', '', $form_markup );
			$form_markup = str_replace( '[check_out_placeholder]', '', $form_markup );
			$form_markup = str_replace( '[check_in_label]', '<label for="check-in-date">' . $this->hb_strings['check_in'] . '</label>', $form_markup );
			$form_markup = str_replace( '[check_out_label]', '<label for="check-out-date">' . $this->hb_strings['check_out'] . '</label>', $form_markup );
			$form_markup = str_replace( '[accom_number_label]', '<label for="accom-number">' . $this->hb_strings['accom_number'] . '</label>', $form_markup );
			$form_markup = str_replace( '[adults_label]', '<label for="adults">' . $this->hb_strings['adults'] . '</label>', $form_markup );
			$form_markup = str_replace( '[children_label]', '<label for="children">' . $this->hb_strings['children'] . '</label>', $form_markup );
			$form_markup = str_replace( '[search_label]', '<label for="hb-search-form-submit">&nbsp;</label>', $form_markup );
		}
		$form_markup = str_replace( '[accom_number_select]', $accom_number_select, $form_markup );
		$form_markup = str_replace( '[accom_people_selection]', $accom_people_selection, $form_markup );
		$form_markup = str_replace( '[people_selects_adults]', $people_selects['adults'], $form_markup );
		$form_markup = str_replace( '[people_selects_children]', $people_selects['children'], $form_markup );
                
		if ( $form_id ) {
			$form_id = 'id="' . $form_id . '"';
		}
		if ( ! $search_form_placeholder && ! $accom_people ) {
			$accom_people = '1-0';
		}
		$form_vars = array( 'form_id', 'form_class', 'search_only_data' , 'form_action', 'form_title', 'check_in', 'check_out', 'adults', 'children', 'accom_people', 'options', 'accom_num' );
		foreach ( $form_vars as $var ) {
			$form_markup = str_replace( "[$var]", $$var, $form_markup );
		}

		$form_strings = array(
			'chosen_check_in', 'chosen_check_out', 'chosen_accom_number', 'chosen_adults', 'chosen_children', 'change_search_button', 'check_in',
			'check_out', 'adults', 'children', 'search_button', 'searching'
		);
                
		foreach ( $form_strings as $string ) {
			$form_markup = str_replace( "[string_$string]", $this->hb_strings[ $string ], $form_markup );
		}

		$form_markup = wp_kses( $form_markup, $this->utils->hb_allowed_html_tags() );
                $form_markup = apply_filters( 'hb_search_form_markup_filter', $form_markup, $atts );

		return $form_markup;
	}
}