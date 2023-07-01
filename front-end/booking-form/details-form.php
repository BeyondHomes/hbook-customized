<?php

class HbDetailsForm {

	private $hbdb;
	private $utils;
	private $hb_strings;
	private $form_fields;

	public function __construct( $hbdb, $utils, $hb_strings, $form_fields ) {
		$this->hbdb = $hbdb;
		$this->utils = $utils;
		$this->hb_strings = $hb_strings;
		$this->form_fields = $form_fields;
	}

	public function get_details_form_mark_up( $resa, $booking_form_num, $thank_you_page_url ) {
		$output =
			'<form class="hb-booking-details-form hb-step-wrapper">' .
				$this->utils->get_step_buttons( 'previous', 2, 'no' ) .
				$this->get_details_fields( $resa ) .
				$this->get_coupon_area() .
				$this->get_front_resa_summary() .
				$this->get_hidden_fields( $booking_form_num ) .
				$this->get_policies_area() .
				$this->get_payment_fields() .
				$this->get_confirm_area() .
			'</form><!-- end .hb-booking-details-form -->';
		if (
			$thank_you_page_url &&
			( $thank_you_page_url != get_permalink( get_the_ID() ) )
		) {
			$output .=
				'<form method="post" action="' . esc_url( $thank_you_page_url ) . '" class="hb-thank-you-page-form">' .
				'<input type="hidden" class="hb-resa-id" name="hb-resa-id" />' .
				'<input type="hidden" class="hb-resa-is-parent" name="hb-resa-is-parent" />' .
				'<input type="hidden" class="hb-resa-payment-type" name="hb-resa-payment-type" />' .
				'</form>';
		}
		$output = wp_kses( $output, $this->utils->hb_allowed_html_tags() );
		return $output;
	}

	public function get_admin_details_form_mark_up() {
		$output =
			'<form class="hb-booking-details-form hb-step-wrapper" novalidate>' .
				$this->utils->get_step_buttons( 'previous', 2, 'yes' ) .
				$this->get_global_discount_area() .
				$this->get_admin_resa_summary() .
				$this->get_admin_details_fields() .
				$this->get_hidden_fields( 0 ) .
				'<input type="hidden" name="hb-payment-flag" class="hb-payment-flag" />' .
				$this->get_admin_confirm_area() .
				'<div class="hb-accom-not-available-msg">' .
					esc_html__( 'The selected accommodation is not available at the chosen dates.', 'hbook-admin' ) .
				'</div>' .
			'</form><!-- end .hb-booking-details-form -->';
		$output = wp_kses( $output, $this->utils->hb_allowed_html_tags() );
		return $output;
	}

	public function get_details_fields( $resa = array() ) {
		$fields = $this->hbdb->get_details_form_fields();
		$output = '';
		$nb_columns = 0;
		$current_columns_wrapper = 0;
		$column_num = 0;
		foreach ( $fields as $field ) {
			if( $field['admin_only'] == 'no') {
				$output = apply_filters( 'hb_details_form_markup_before_field', $output, $field );
				if ( $field['displayed'] == 'yes' ) {
					if ( $field['column_width'] == 'half' ) {
						$nb_columns = 2;
					} else if ( $field['column_width'] == 'third' ) {
						$nb_columns = 3;
					} else {
						$nb_columns = 0;
					}
					if ( $nb_columns ) {
						if ( $column_num && ( $current_columns_wrapper != $nb_columns ) ) {
							$column_num = 0;
							$current_columns_wrapper = 0;
							$output .= '</div><!-- end .hb-clearfix -->';
						}
						if ( ! $column_num ) {
							$column_num = 1;
							$current_columns_wrapper = $nb_columns;
							$output .= '<div class="hb-clearfix">';
						} else {
							$column_num++;
						}
					} else if ( $column_num != 0 ) {
						$column_num = 0;
						$nb_columns = 0;
						$current_columns_wrapper = 0;
						$output .= '</div><!-- end .hb-clearfix -->';
					}

					$output .= $this->form_fields->get_field_mark_up( $field, $resa );

					if ( $current_columns_wrapper && ( $current_columns_wrapper == $column_num ) ) {
						$column_num = 0;
						$nb_columns = 0;
						$current_columns_wrapper = 0;
						$output .= '</div><!-- end .hb-clearfix -->';
					}
				}
				$output = apply_filters( 'hb_details_form_markup_after_field', $output, $field );
			}
		}
		if ( $current_columns_wrapper ) {
			$output .= '</div><!-- end .hb-clearfix -->';
		}
		$output = '<div class="hb-details-fields">' . $output . '</div>';
		$output = apply_filters( 'hb_details_form_markup', $output );
		return $output;
	}

	private function get_coupon_area() {
		$output = '';
		if ( $this->hbdb->site_has_coupons() ) {
			$output .= '<div class="hb-coupons-area">';
			$output .= '<h3 class="hb-title hb-title-coupons">' . $this->hb_strings['coupons_section_title'] . '</h3>';
			$output .= '<input type="hidden" name="hb-pre-validated-coupon-id" class="hb-pre-validated-coupon-id" />';
			$output .= '<p>' . $this->hb_strings['coupons_text'] . '</p>';
			$output .= '<p class="hb-clearfix">';
			$output .= '<input type="text" class="hb-coupon-code" name="hb-coupon-code" />';
			$output .= '<input type="submit" class="hb-apply-coupon" value="' . $this->hb_strings['coupons_button'] . '" />';
			$output .= '<span class="hb-processing-coupon"></span>';
			$output .= '</p>';
			$output .= '<p class="hb-coupon-msg">&nbsp;</p>';
			$output .= '<p class="hb-coupon-error">&nbsp;</p>';
			$output .= '</div>';
		}
		return $output;
	}

	private function get_global_discount_area() {
		$output = '<div class="hb-global-discount-wrapper">';
		$output .= '<p class="hb-admin-add-resa-section-title">';
		$output .= esc_html__( 'Discount:', 'hbook-admin' );
		$output .= '</p>';
		$output .= '<label for="hb-global-discount-amount">';
		$output .= esc_html__( 'Amount:', 'hbook-admin' );
		$output .= '</label><br/>';
		$output .= '<input type="number" id="hb-global-discount-amount" name="hb-global-discount-amount"><br/>';
		$output .= '<label>';
		$output .= esc_html__( 'Amount type:', 'hbook-admin' );
		$output .= '</label><br/>';
		$output .= '<input type="radio" id="hb-global-discount-amount-type-fixed" name="hb-global-discount-amount-type" value="fixed">';
		$output .= '<label for="hb-global-discount-amount-type-fixed">';
		$output .= esc_html__( 'Fixed', 'hbook-admin' );
		$output .= ' (';
		$output .= $this->utils->get_currency_symbol();
		$output .= ')';
		$output .= '</label><br/>';
		$output .= '<input type="radio" id="hb-global-discount-amount-type-percent" name="hb-global-discount-amount-type" value="percent">';
		$output .= '<label for="hb-global-discount-amount-type-percent">';
		$output .= esc_html__( 'Percentage', 'hbook-admin' );
		$output .= '</label><br/>';
		$output .= '</div>';
		return $output;
	}

	private function get_payment_fields() {
		$output = '';
		$display_payment_title = false;
		$payment_type = '';
		$payment_type_text = '';
		$payment_type_explanation = '';
		$amount_types = array( 'full_amount', 'deposit_amount', 'full_minus_deposit_amount' );
		$active_payment_types = array();
		if ( get_option( 'hb_resa_payment_multiple_choice' ) == 'yes' ) {
			$payment_types = apply_filters( 'hb_payment_types', array( 'offline', 'store_credit_card', 'deposit', 'full' ) );
			foreach ( $payment_types as $payment_type ) {
				if ( get_option( 'hb_resa_payment_' . $payment_type ) == 'yes' ) {
					$active_payment_types[] = $payment_type;
				}
			}
			if ( ! $active_payment_types ) {
				$active_payment_types[] = 'offline';
			}
		} else {
			$active_payment_types[] = get_option( 'hb_resa_payment' );
		}
		if ( count( $active_payment_types ) > 1 ) {
			$display_payment_title = true;
			$payment_choice_text = '<p class="hb-payment-type-multiple-choice">';
			$payment_choice_text .= '<b>' . $this->hb_strings['payment_type'] . '</b><br/>';
			foreach ( $active_payment_types as $payment_type ) {
				$payment_type_input_id_class = 'hb-payment-type-' . $payment_type;
				$payment_choice_text .= '<span class="hb-payment-type-wrapper ' . $payment_type_input_id_class . '-wrapper">';
				$payment_choice_text .= '<input type="radio" id="' . $payment_type_input_id_class . '" name="hb-payment-type" value="' . $payment_type . '" />';
				$payment_choice_text .= ' <label for="hb-payment-type-' . $payment_type . '">' . $this->hb_strings[ 'payment_type_' . $payment_type ] . '</label><br/>';
				$payment_choice_text .= '</span>';
				$explanation = '';
				if ( isset( $this->hb_strings[ 'payment_type_explanation_' . $payment_type ] ) && $this->hb_strings[ 'payment_type_explanation_' . $payment_type ] ) {
					$explanation = $this->hb_strings[ 'payment_type_explanation_' . $payment_type ];
					foreach ( $amount_types as $amount_type ) {
						$price_placeholder = '<span class="hb-payment-type-explanation-' . $amount_type . '">&nbsp;</span>';
						$explanation = str_replace( '%' . $amount_type, $price_placeholder, $explanation );
					}
					$explanation = '<p class="hb-payment-type-explanation hb-payment-type-explanation-' . $payment_type . '">' . $explanation . '</p>';
				}
				$payment_type_explanation .= $explanation;
			}
			$payment_choice_text .= '<span class="hb-payment-type-null-price-wrapper">';
			$payment_choice_text .= '<input class="hb-payment-type-null-price" type="radio" name="hb-payment-type" value="offline" />';
			$payment_choice_text .= '</span>';
			$payment_choice_text .= '</p>';
		} else {
			$payment_type = $active_payment_types[0];
			$payment_choice_text = '<span class="hb-payment-type-hidden">';
			$payment_choice_text .= '<input type="radio" name="hb-payment-type" value="' . $payment_type . '" />';
			$payment_choice_text .= '</span>';
			if ( $payment_type != 'offline' ) {
				$payment_choice_text .= '<span class="hb-payment-type-null-price-wrapper">';
				$payment_choice_text .= '<input class="hb-payment-type-null-price" type="radio" name="hb-payment-type" value="offline" />';
				$payment_choice_text .= '</span>';
			}
			if ( $payment_type == 'deposit' || $payment_type == 'full' ) {
				$display_payment_title = true;
			}
			if ( isset( $this->hb_strings['payment_type_explanation_' . $payment_type ] ) && $this->hb_strings['payment_type_explanation_' . $payment_type ] ) {
				$explanation = $this->hb_strings[ 'payment_type_explanation_' . $payment_type ];
				foreach ( $amount_types as $amount_type ) {
					$price_placeholder = '<span class="hb-payment-type-explanation-' . $amount_type . '">&nbsp;</span>';
					$explanation = str_replace( '%' . $amount_type, $price_placeholder, $explanation );
				}
				$payment_type_explanation = '<p class="hb-payment-type-explanation hb-payment-type-explanation-' . $payment_type . '">' . $explanation . '</p>';
			}
		}
		$output .= $payment_choice_text;
		$output .= $payment_type_explanation;

		$output .= '<div class="hb-payment-method-wrapper">';

		$payment_gateways = $this->utils->get_active_payment_gateways();
		$payment_gateways_text = esc_html__( 'There are no active payment gateways. Please activate at least one payment gateway in HBook settings (HBook > Payment).', 'hbook-admin' );
		if ( count( $payment_gateways ) == 1 ) {
			$payment_gateways_text = '<span class="hb-payment-method-hidden">';
			$payment_gateways_text .= '<input type="radio" name="hb-payment-gateway" value="' . $payment_gateways[0]->id . '" data-has-redirection="' . $payment_gateways[0]->has_redirection . '" />';
			$payment_gateways_text .= '</span>';
		} else if ( count( $payment_gateways ) > 1 ) {
			$payment_gateways_text = '<p class="hb-payment-method"><b>' . $this->hb_strings['payment_method'] . '</b><br/>';
			foreach ( $payment_gateways as $gateway ) {
				$payment_gateways_text .= '<input type="radio" id="hb-payment-gateway-' . $gateway->id . '" name="hb-payment-gateway" value="' . $gateway->id . '" data-has-redirection="' . $gateway->has_redirection . '" />';
				$payment_gateways_text .= ' <label class="hb-payment-gateway-label-' . $gateway->id . '" for="hb-payment-gateway-' . $gateway->id . '">' . $gateway->get_payment_method_label() . '</label><br/>';
			}
			$payment_gateways_text .= '</p>';
		}
		$output .= $payment_gateways_text;

		$payment_forms = '';
		$bottom_areas = '';
		foreach ( $payment_gateways as $gateway ) {
			if ( $gateway->payment_form() ) {
				$payment_forms .= '<div class="hb-payment-form hb-payment-form-' . $gateway->id . '">' . $gateway->payment_form() . '</div>';
			}
			if ( $gateway->bottom_area() ) {
				$bottom_areas .= '<div class="hb-bottom-area-content-' . $gateway->id . '">' . $gateway->bottom_area() . '</div>';
			}
		}
		$output .= $payment_forms;
		$output .= '<div class="hb-bottom-area-content">' . $bottom_areas . '</div>';

		$output .= '</div>';

		$output .= '<input type="hidden" name="hb-payment-flag" class="hb-payment-flag" />';

		if ( $display_payment_title ) {
			$payment_section_title = $this->hb_strings['payment_section_title'];
			if ( $payment_section_title ) {
				$output = '<h3 class="hb-title hb-title-payment">' . $payment_section_title . '</h3>' . $output;
			}
		}

		$output = '<div class="hb-payment-info-wrapper">' . $output . '</div>';
		return $output;
	}

	private function get_front_resa_summary() {
		return $this->get_resa_summary( false );
	}

	private function get_admin_resa_summary() {
		return $this->get_resa_summary( true );
	}

	private function get_resa_summary( $is_admin ) {
		$output = '<p class="hb-loading-summary">';
		if ( $is_admin ) {
			$output .= '<span class="spinner"></span>';
			$output .= esc_html__( 'Loading summary...', 'hbook-admin' );
		} else {
			$output .= esc_html( $this->hb_strings['summary_loading'] );
		}
		$output .= '</p>';
		$output .= '<div class="hb-summary-wrapper">';
		$output .= '&nbsp;';
		$output .= '</div>';
		return $output;
	}

	private function get_hidden_fields( $booking_form_num ) {
		$output = '
			<input type="hidden" class="hb-details-check-in" name="hb-details-check-in" />
			<input type="hidden" class="hb-details-check-out" name="hb-details-check-out" />
			<input type="hidden" class="hb-details-adults" name="hb-details-adults" />
			<input type="hidden" class="hb-details-children" name="hb-details-children" />
			<input type="hidden" class="hb-details-accom-ids" name="hb-details-accom-ids" />
			<input type="hidden" class="hb-details-is-admin" name="hb-details-is-admin" />
			<input type="hidden" name="hb-details-booking-form-num" value="' . $booking_form_num . '"/>';
		return $output;
	}

	private function get_policies_area() {
		$policies = '';
		if ( get_option( 'hb_display_terms_and_cond' ) == 'yes' ) {
			$policies .=
				'<p>' .
					'<input type="checkbox" id="terms-and-cond" name="hb_terms_and_cond" />' .
					'<label for="terms-and-cond" class="hb-terms-and-cond"> ' . $this->hb_strings['terms_and_cond_text'] . '</label>' .
				'</p>';
		}
		if ( get_option( 'hb_display_privacy_policy' ) == 'yes' ) {
			$policies .=
				'<p>' .
					'<input type="checkbox" id="privacy-policy" name="hb_privacy_policy" />' .
					'<label for="privacy-policy" class="hb-privacy-policy"> ' . $this->hb_strings['privacy_policy_text'] . '</label>' .
				'</p>';
		}
		if ( $policies && ( $this->hb_strings['terms_and_cond_title'] ) ) {
			$policies =
				'<h3 class="hb-title hb-title-terms">' . $this->hb_strings['terms_and_cond_title'] . '</h3>' .
				$policies;
		}
		$output = '<div class="hb-policies-area">';
		$output .= $policies;
		$output .= '<p class="hb-policies-error"></p>';
		$output .= '</div>';
		return apply_filters( 'hb_policies_area_markup', $output );
	}

	private function get_admin_details_fields() {
		$output = '<div id="hb-resa-customer-details-wrap">';

		$output .= '<p class="hb-admin-add-resa-section-title">' . esc_html__( 'Customer:', 'hbook-admin' ) . '</p>';

		$output .= '<div>';
		$output .= '<input type="radio" id="hb-admin-customer-type-id" name="hb-admin-customer-type" value="id" checked />';
		$output .= '<label for="hb-admin-customer-type-id">';
		$output .= esc_html__( 'Select existing customer', 'hbook-admin' );
		$output .= '</label><br/>';
		$output .= '<input type="radio" id="hb-admin-customer-type-details" name="hb-admin-customer-type" value="details" />';
		$output .= '<label for="hb-admin-customer-type-details">';
		$output .= esc_html__( 'Enter customer details', 'hbook-admin' );
		$output .= '</label>';
		$output .= '</div>';

		$output .= '<div id="hb-resa-customer-id">';
		$output .= '<div class="hb-resa-filter-customer">';
		$output .= '<select ';
		$output .= 'id="hb-add-resa-customer-id-list" ';
		$output .= 'name="hb-customer-id" ';
		$output .= 'class="hb-customer-id-list" ';
		$output .= 'multiple size="6" ';
		$output .= 'data-bind="options: resa_customers_list, optionsValue: \'id\', optionsText: \'id_name\'" ';
		$output .= '></select>';
		$output .= '<br/>';
		$output .= '<input ';
		$output .= 'type="text" ';
		$output .= 'data-bind="value: resa_customers_list_filter, valueUpdate: \'afterkeydown\'" ';
		$output .= 'placeholder="' . esc_attr__( 'Search a customer...', 'hbook-admin' ) . '" ';
		$output .= '/><br/>';
		$output .= '</div><!-- end .hb-resa-filter-customer -->';
		$output .= '</div><!-- end #hb-resa-customer-id -->';

		$output .= '<div id="hb-resa-customer-details">';
		$customer_fields = $this->hbdb->get_customer_form_fields();
		foreach ( $customer_fields as $field ) {
			$output .= $this->form_fields->get_field_mark_up( $field, array(), false, false );
		}
		$output .= '</div><!-- end #hb-resa-customer-details -->';

		$booking_fields = $this->hbdb->get_additional_booking_info_form_fields();
		if ( $booking_fields ) {
			$output .= '<div id="hb-resa-additional-info">';
			$output .= '<p class="hb-admin-add-resa-section-title">' . esc_html__( 'Additional information:', 'hbook-admin' ) . '</p>';
			foreach ( $booking_fields as $field ) {
				$output .= $this->form_fields->get_field_mark_up( $field, array(), false, false );
			}
			$output .= '</div><!-- end #hb-resa-additional-info -->';
		}

		$output .= '<p class="hb-admin-add-resa-section-title">';
		$output .= '<label for="hb-admin-comment">';
		$output .= esc_html__( 'Comment:', 'hbook-admin' );
		$output .= '</label>';
		$output .= '</p>';

		$output .= '<div>';
		$output .= '<textarea id="hb-admin-comment" name="hb-admin-comment"></textarea>';
		$output .= '</div>';

		if ( $this->utils->is_site_multi_lang() ) {
			$output .= '<p class="hb-admin-add-resa-section-title">';
			$output .= '<label for="hb-resa-admin-lang">';
			$output .= esc_html__( 'Reservation language:', 'hbook-admin' );
			$output .= '</label>';
			$output .= '</p>';
			$output .= '<div>';
			$output .= '<select	id="hb-resa-admin-lang" name="hb-resa-admin-lang">';
			foreach ( $this->utils->get_langs() as $lang_value => $lang_name ) {
				$output .= '<option value="' . $lang_value . '"';
				if ( $lang_value == get_locale() ) {
					$output .= ' selected';
				}
				$output .= '>';
				$output .= $lang_name;
				$output .= '</option>';
			}
			$output .= '</select>';
			$output .= '</div>';
		} else {
			$output .= '<input type="hidden" name="hb-resa-admin-lang" value="' . get_locale() . '" />';
		}

		$output .= '</div><!-- end #hb-resa-customer-details-wrap -->';

		return $output;
	}

	private function get_confirm_area() {
		$txt_before_book_now_button = '';
		if ( $this->hb_strings['txt_before_book_now_button'] ) {
			$txt_before_book_now_button = '<p>' . $this->hb_strings['txt_before_book_now_button'] . '</p>';
		}
		$output =
		'<div class="hb-confirm-area">' .
			'<p class="hb-saving-resa">' . $this->hb_strings['processing'] . '</p>' .
			$txt_before_book_now_button .
			'<p class="hb-confirm-error"></p>' .
			'<p class="hb-confirm-button hb-button-wrapper"><input type="submit" value="' . $this->hb_strings['book_now_button'] . '" /></p>' .
		'</div>' .
		'<p class="hb-bottom-area">&nbsp;</p>' .
		'<input type="hidden" name="action" value="hb_create_resa" />';
		$output = apply_filters( 'hb_confirm_area_markup', $output );
		return $output;
	}

	private function get_admin_confirm_area() {
		$output = '<div class="hb-confirm-area">';
		$output .= '<p class="hb-confirm-button">';
		$output .= '<input type="submit" class="button-primary" value="';
		$output .= esc_html__( 'Create reservation', 'hbook-admin' );
		$output .= '" />';
		$output .= '</p>';
		$output .= '<p class="hb-saving-resa hb-ajaxing">';
		$output .= '<span class="spinner"></span> ';
		$output .= esc_html__( 'Updating database...', 'hbook-admin' );
		$output .= '</p>';
		$output .= '<p class="hb-confirm-error"></p>';
		$output .= '</div>';
		$output .= '<input type="hidden" name="action" value="hb_create_resa" />';
		return $output;
	}

}