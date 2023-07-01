<?php
class HbResaSummary {

	private $hbdb;
	private $utils;
	private $strings;
	private $extras_included_fees;
	private $extras_final_fees;
	private $global_percent_final_fees;
	private $global_fixed_final_fees;
	private $has_fixed_coupon;
	private $fixed_fixed_coupon_amount_left;
	private $is_admin;

	public function __construct( $hbdb, $utils, $strings ) {
		$this->hbdb = $hbdb;
		$this->utils = $utils;
		$this->strings = $strings;
		$this->extras_included_fees = $this->hbdb->get_extras_included_fees();
		$this->extras_final_fees = $this->hbdb->get_extras_final_fees();
		$this->global_percent_final_fees = $this->hbdb->get_global_percent_final_fees();
		$this->global_fixed_final_fees = $this->hbdb->get_global_fixed_final_fees();
		$this->has_fixed_coupon = false;
		$this->fixed_coupon_amount_left = 0;
		$this->is_admin = false;
	}

	public function get_summary( $resa, $parent_resa = false, $customer_info = false, $payment_type = false ) {
		if ( isset( $_POST['hb-details-is-admin'] ) && ( $_POST['hb-details-is-admin'] == 'yes' ) ) {
			$this->is_admin = true;
			$this->strings['summary_title'] = esc_html__( 'Summary:', 'hbook-admin' );
			$this->strings['chosen_check_in'] = esc_html__( 'Check-in:', 'hbook-admin' );
			$this->strings['chosen_check_out'] = esc_html__( 'Check-out:', 'hbook-admin' );
			$this->strings['number_of_nights'] = esc_html__( 'Number of nights:', 'hbook-admin' );
			$this->strings['chosen_adults'] = esc_html__( 'Adults:', 'hbook-admin' );
			$this->strings['chosen_children'] = esc_html__( 'Children:', 'hbook-admin' );
			$this->strings['multi_accom_accom_n'] = esc_html__( 'Accommodation %n:', 'hbook-admin' );
			$this->strings['summary_accommodation'] = esc_html__( 'Accommodation:', 'hbook-admin' );
			$this->strings['summary_accommodation_number'] = esc_html__( 'Accommodation number:', 'hbook-admin' );
			$this->strings['summary_accom_total_price'] = esc_html__( 'Price:', 'hbook-admin' );
			$this->strings['summary_global_options_price'] = esc_html__( 'Global options price:', 'hbook-admin' );
			$this->strings['summary_deposit'] = esc_html__( 'Deposit amount:', 'hbook-admin' );
			$this->strings['summary_price'] = esc_html__( 'Total price:', 'hbook-admin' );
			$this->strings['summary_security_bond'] = esc_html__( 'Security bond amount:', 'hbook-admin' );
			$this->strings['summary_accom_price'] = esc_html__( 'Accommodation price:', 'hbook-admin' );
			$this->strings['summary_options_price'] = esc_html__( 'Options price:', 'hbook-admin' );
			$this->strings['summary_discount_amount'] = esc_html__( 'Discount amount:', 'hbook-admin' );
			$this->strings['summary_included_fees'] = esc_html__( 'Price includes:', 'hbook-admin' );
		}
		$output = '<div ';
		if ( ( $payment_type == 'deposit' ) || ( $payment_type == 'full' ) ) {
			$output .= 'id="hb-resa-confirm-done" ';
		}
		$output .= 'class="hb-resa-summary">';
		if ( $this->is_admin ) {
			$output .= '<p class="hb-admin-add-resa-section-title">';
			$output .= $this->strings['summary_title'];
			$output .= '</p>';
		} else {
			$output .= '<h3 class="hb-title hb-resa-summary-title">';
			$output .= $this->strings['summary_title'];
			$output .= '</h3>';
		}
		if ( $payment_type === false ) {
			$output .= '<p class="hb-resa-payment-msg">' . str_replace( '%customer_email', '<span class="hb-resa-done-email"></span>', $this->strings['thanks_message_payment_done_1'] ) . '</p>';
			$output .= '<p class="hb-resa-done-msg">' . str_replace( '%customer_email', '<span class="hb-resa-done-email"></span>', $this->strings['thanks_message_1'] ) . '</p>';
		} else {
			if ( ( $payment_type == 'deposit' ) || ( $payment_type == 'full' ) ) {
				$thanks_message = $this->strings['thanks_message_payment_done_1'];
			} else {
				$thanks_message = $this->strings['thanks_message_1'];
			}
			if ( isset( $customer_info['email'] ) ) {
				$email = $customer_info['email'];
			} else {
				$email = '';
			}
			$output .= '<p>' . str_replace( '%customer_email', $email, $thanks_message ) . '</p>';
		}

		$output .= '<div class="hb-resa-summary-content">';
		$output .= '<div>' . $this->strings['chosen_check_in'] . ' <span class="hb-format-date">' . $resa[0]['check_in'] . '</span></div>';
		$output .= '<div>' . $this->strings['chosen_check_out'] . ' <span class="hb-format-date">' . $resa[0]['check_out'] . '</span></div>';
		if ( get_option( 'hb_charge_per_day' ) == 'yes' ) {
			$nb_days = $this->utils->get_number_of_nights( $resa[0]['check_in'], $resa[0]['check_out'] ) + 1;
			$output .= '<div>' . $this->strings['number_of_nights'] . ' ' . $nb_days . '</div>';
		} else {
			$output .= '<div>' . $this->strings['number_of_nights'] . ' ' . $this->utils->get_number_of_nights( $resa[0]['check_in'], $resa[0]['check_out'] ) . '</div>';
		}
		$is_multiple_resa = false;
		if ( count( $resa ) > 1 ) {
			$is_multiple_resa = true;
		}

		if ( ! $is_multiple_resa ) {
			$output .= $this->get_adults_children_markup( $resa[0]['adults'], $resa[0]['children'] );
		}
		$output .= '<br/>';

		$total_price = 0;
		foreach ( $resa as $i => $resa_info ) {
			$multi_accom_num = $i + 1;
			if ( $is_multiple_resa ) {
				$output .= '<div class="hb-summary-multi-accom-accom hb-summary-multi-accom-accom-' . $multi_accom_num . '">';
				if (
					( get_option( 'hb_display_adults_field' ) == 'yes' ) ||
					( get_option( 'hb_display_children_field' ) == 'yes' ) ||
					( get_option( 'hb_display_price' ) == 'yes' ) ||
					$this->is_admin
				) {
					$output .= '<div class="hb-summary-multi-accom-title">';
					$output .= str_replace( '%n', $multi_accom_num, $this->strings['multi_accom_accom_n'] );
					$output .= '</div>';
				}
				$output .= '<div class="hb-summary-accom-content">';
			} else {
				$output .= '<div class="hb-summary-accom-wrapper">';
			}

			if ( $this->utils->nb_accom() > 1 ) {
				$output .= '<div class="hb-summary-accom-name">';
				if ( ! $is_multiple_resa ) {
					$output .= $this->strings['summary_accommodation'];
					$output .= ' ';
				}
				if ( $this->is_admin ) {
					$output .= $this->utils->get_admin_accom_title( $resa_info['accom_id'] );
				} else {
					$output .= $this->utils->get_accom_title( $resa_info['accom_id'] );
				}
				if ( $resa_info['accom_num'] ) {
					$output .= ' <span class="hb-summary-accom-num-name">';
					$output .= '(';
					$output .= $this->hbdb->get_accom_num_name_by_accom_num( $resa_info['accom_id'], $resa_info['accom_num'] );
					$output .= ')';
					$output .= '</span>';
				}
				$output .= '</div>';
			}

			if ( $is_multiple_resa ) {
				$output .= $this->get_adults_children_markup( $resa_info['adults'], $resa_info['children'] );
			}

			if ( $this->is_admin || ( get_option( 'hb_display_price' ) == 'yes' ) ) {
				$accom_total_price = 0;
				$global_discount_breakdown = array();
				$price_has_details = false;
				$output .= $this->get_price_details( $multi_accom_num, $resa_info, count( $resa ), $accom_total_price, $global_discount_breakdown, $price_has_details );
				if ( $is_multiple_resa ) {
					$output .= '<div class="hb-summary-accom-total-price">';
					$output .= $this->strings['summary_accom_total_price'];
					$output .= ' ';
					$output .= $this->utils->price_with_symbol( $accom_total_price );
					if ( $price_has_details && ! $this->is_admin ) {
						$output .= '<span class="hb-summary-price-breakdown-trigger-wrapper">';
						$output .= ' - <a class="hb-summary-view-price-breakdown" href="#">';
						$output .= '<span class="hb-summary-price-breakdown-show-text">';
						$output .= $this->strings['view_price_breakdown'];
						$output .= '</span>';
						$output .= '<span class="hb-summary-price-breakdown-hide-text">';
						$output .= $this->strings['hide_price_breakdown'];
						$output .= '</span>';
						$output .= '</a></span>';
					}
					$output .= '</div>';
				}
				$total_price += $accom_total_price;
			}
			$output .= '<br/>';
			if ( $is_multiple_resa ) {
				$output .= '</div><!-- end .hb-summary-accom-content" -->';
				$output .= '</div><!-- end .hb-summary-multi-accom-accom.hb-summary-multi-accom-accom-' . $multi_accom_num . '-->';
			} else {
				$output .= '</div><!-- end .hb-summary-accom-wrapper -->';
			}
		}

		if ( $this->is_admin || ( get_option( 'hb_display_price' ) == 'yes' ) ) {
			if ( $is_multiple_resa ) {
				$parent_resa_info = array(
					'accom_id' => 'parent_resa',
					'check_in' => $resa[0]['check_in'],
					'check_out' => $resa[0]['check_out'],
					'adults' => array_sum( array_column( $resa, 'adults' ) ),
					'children' => array_sum( array_column( $resa, 'children' ) ),
				);
				if ( $parent_resa ) {
					$parent_resa_info['options'] = $parent_resa['options'];
				}
				$options_price = $this->calculate_chosen_options_price( 'global', $parent_resa_info, count( $resa ) );
				if ( $options_price != 0 ) {
					$total_price += $options_price;
					$output .= '<div class="hb-summary-options-price">';
					$output .= $this->strings['summary_global_options_price'] . ' ';
					if ( $options_price < 0 ) {
						$output .= '-';
						$output .= $this->utils->price_with_symbol( $options_price * -1 );
					} else {
						$output .= $this->utils->price_with_symbol( $options_price );
					}
					$output .= '</div>';

					$extras_included_fees_markup = '';
					$options_price_before_included_fees = $this->utils->calculate_price_before_included_fees( array(), $options_price, $this->extras_included_fees );
					foreach ( $this->extras_included_fees as $fee ) {
						$fee_values = $this->utils->calculate_fees_extras_values( $parent_resa_info, $options_price_before_included_fees, $fee, $this->strings );
						if ( $fee_values['price'] > 0 ) {
							$fee_name = $this->get_fee_name( $fee );
							$extras_included_fees_markup .= $this->get_included_fee_markup( $fee_name, $fee_values );
						}
					}
					if ( $extras_included_fees_markup ) {
						$output .= $this->wrap_included_fees( 'extras', $extras_included_fees_markup );
					}
					$options_final_fees_price = 0;
					$extras_final_fees_markup = '';
					foreach ( $this->extras_final_fees as $fee ) {
						$fee_values = $this->utils->calculate_fees_extras_values( $parent_resa_info, $options_price, $fee, $this->strings );
						if ( $fee_values['price'] > 0 ) {
							$fee_name = $this->get_fee_name( $fee );
							$extras_final_fees_markup .= $this->get_final_fee_markup( $fee_name, $fee_values );
							$options_final_fees_price += $fee_values['price'];
						}
					}
					if ( $extras_final_fees_markup ) {
						$output .= $this->wrap_final_fees( 'extras', $extras_final_fees_markup );
					}
					$global_percent_final_fees_markup = '';
					foreach ( $this->global_percent_final_fees as $fee ) {
						$fee_values = $this->utils->calculate_fees_extras_values( $parent_resa_info, $options_price, $fee, $this->strings );
						if ( $fee_values['price'] > 0 ) {
							$fee_name = $this->get_fee_name( $fee );
							$global_percent_final_fees_markup .= $this->get_final_fee_markup( $fee_name, $fee_values );
							$options_final_fees_price += $fee_values['price'];
						}
					}
					if ( $global_percent_final_fees_markup ) {
						$output .= $this->wrap_final_fees( 'global', $global_percent_final_fees_markup );
					}
					$output .= '<br/>';
					$total_price += $options_final_fees_price;
				}
			}
			$global_fixed_final_fees_markup = '';
			foreach ( $this->global_fixed_final_fees as $fee ) {
				$fee_values = $this->utils->calculate_fees_extras_values( $resa[0], 0, $fee, $this->strings );
				if ( $fee_values['price'] > 0 ) {
					$fee_name = $this->get_fee_name( $fee );
					$global_fixed_final_fees_markup .= $this->get_final_fee_markup( $fee_name, $fee_values );
					$total_price += $fee_values['price'];
				}
			}
			if ( $global_fixed_final_fees_markup ) {
				$output .= $this->wrap_final_fees( 'global', $global_fixed_final_fees_markup );
				$output .= '<br/>';
			}

			$deposit = $this->utils->deposit( $resa[0]['check_in'], $resa[0]['check_out'], $total_price );
			if (
				$deposit &&
				( $deposit != $total_price ) &&
				( ! $payment_type || $payment_type == 'deposit' )
			) {
				$output .= '<div class="hb-summary-deposit">';
				$output .= $this->strings['summary_deposit'];
				$output .= ' ';
				$output .= $this->utils->price_with_symbol( $deposit );
				$output .= '</div>';
				if ( ! $payment_type ) {
					$output .= '<br/>';
				}
			}

			if ( ( $payment_type != 'deposit' ) || ( $deposit == $total_price ) ) {
				$output .= '<div class="hb-summary-total-price">';
				$output .= $this->strings['summary_price'];
				$output .= ' ';
				$output .= $this->utils->price_with_symbol( $total_price );
				$output .= '</div>';
			}

			$global_included_fees_markup = '';
			$global_included_fees = $this->hbdb->get_global_included_fees();
			$price_before_included_fees = $this->utils->calculate_price_before_included_fees( array(), $total_price, $global_included_fees );
			foreach ( $global_included_fees as $fee ) {
				$fee_values = $this->utils->calculate_fees_extras_values( $resa[0], $price_before_included_fees, $fee, $this->strings );
				if ( $fee_values['price'] > 0 ) {
					$fee_name = $this->get_fee_name( $fee );
					$global_included_fees_markup .= $this->get_included_fee_markup( $fee_name, $fee_values );
				}
			}
			if ( $global_included_fees_markup ) {
				$output .= $this->wrap_included_fees( 'global', $global_included_fees_markup );
			}

			if ( get_option( 'hb_security_bond' ) == 'yes' ) {
				$output .= '<br/>';
				$bond_amount = $this->utils->price_with_symbol( get_option( 'hb_security_bond_amount' ) );
				$bond_text = '<div class="hb-summary-bond">' . $this->strings['summary_security_bond'] . ' ' . $bond_amount . '</div>';
				if ( ! $this->is_admin ) {
					$bond_explanation = $this->strings['summary_security_bond_explanation'];
					if ( $bond_explanation ) {
						$bond_text .= '<div>' . $bond_explanation . '</div>';
					}
					$output .= $bond_text;
				}
			}

			$charged_total_price = $total_price;
			$charged_deposit = $deposit;
			$security_bond = get_option( 'hb_security_bond_amount' );
			if ( get_option( 'hb_security_bond_online_payment' ) == 'yes' ) {
				$charged_total_price += $security_bond;
				if ( get_option( 'hb_deposit_bond' ) == 'yes' ) {
					$charged_deposit += $security_bond;
				}
			}
			$charged_total_minus_deposit = $charged_total_price - $charged_deposit;
			$null_price = 'no';
			if ( ( $charged_total_price <= 0 ) && ( $charged_deposit <= 0 ) ) {
				$null_price = 'yes';
			}
			$output .= '<div class="hb-payment-data-summary" ';
			$output .= 'data-charged-total-price="' . $this->utils->price_with_symbol( $charged_total_price ) . '" ';
			$output .= 'data-charged-deposit="' . $this->utils->price_with_symbol( $charged_deposit ) . '" ';
			$output .= 'data-charged-total-minus-deposit="' . $this->utils->price_with_symbol( $charged_total_minus_deposit ) . '" ';
			$output .= 'data-null-price="' . $null_price . '" ';
			$output .= '></div>';
			if ( $global_discount_breakdown ) {
				$output .= '<div class="hb-discount-data-summary" ';
				$output .= 'data-discount-amount-type="' . esc_attr( $global_discount_breakdown['amount_type'] ) . '" ';
				$output .= 'data-discount-amount="' . esc_attr( $global_discount_breakdown['amount'] ) . '" ';
				$output .= '></div>';
			}
		}
		if ( ! $this->is_admin ) {
			$output .= '<br/>';
			$output .= '<div class="hb-summary-bottom-text">' . $this->strings['summary_bottom_text'] . '</div>';
		}
		$output .= '</div><!-- end .hb-resa-summary-content -->';
		if ( $payment_type === false ) {
			$output .= '<p class="hb-resa-done-msg">' . $this->strings['thanks_message_2'] . '</p>';
			$output .= '<p class="hb-resa-payment-msg">' . $this->strings['thanks_message_payment_done_2'] . '</p>';
		} else {
			if ( ( $payment_type == 'deposit' ) || ( $payment_type == 'full' ) ) {
				$output .= '<p>' . $this->strings['thanks_message_payment_done_2'] . '</p>';
			} else {
				$output .= '<p>' . $this->strings['thanks_message_2'] . '</p>';
			}
		}
		$output = apply_filters( 'hb_resa_summary_markup', $output );
		$output .= '</div><!-- end .hb-resa-summary -->';
		$allowed_html = wp_kses_allowed_html( 'post' );
		$allowed_html['div']['data-charged-total-price'] = true;
		$allowed_html['div']['data-charged-deposit'] = true;
		$allowed_html['div']['data-charged-total'] = true;
		$allowed_html['div']['data-null-price'] = true;
		$allowed_html['div']['data-discount-amount'] = true;
		$allowed_html['div']['data-discount-amount'] = true;
		$output = wp_kses( $output, $allowed_html );
		return $output;
	}

	private function get_adults_children_markup( $adults, $children ) {
		$output = '';
		if ( get_option( 'hb_display_adults_field' ) == 'yes' ) {
			$output .= '<div class="hb-summary-adults-children-wrapper">';
			$output .= '<div class="hb-summary-adults">';
			$output .= $this->strings['chosen_adults'];
			$output .= ' ';
			$output .= $adults;
			$output .= '</div>';
			if ( ( get_option( 'hb_display_children_field' ) == 'yes' ) && $children ) {
				$output .= '<div class="hb-summary-children">';
				$output .= $this->strings['chosen_children'];
				$output .= ' ';
				$output .= $children;
				$output .= '</div>';
			}
			$output .= '</div>';
		}
		return $output;
	}

	private function get_price_details( $multi_accom_num, $resa_info, $nb_resa, &$total_price, &$global_discount_breakdown, &$price_has_details ) {
		$output = '<div class="hb-summary-price-details">';
		if ( isset( $resa_info['accom_total_price'] ) ) {
			$accom_price = $resa_info['accom_total_price'];
		} else {
			$accom_price = $resa_info['accom_price'];
			$discount = json_decode( $resa_info['discount'], true );
			$accom_discount_value = 0;
			if ( $discount['accom'] && $discount['accom']['amount'] ) {
				if ( $discount['accom']['amount_type'] == 'fixed' ) {
					$accom_discount_value = $discount['accom']['amount'];
				} else {
					$accom_discount_value = $this->utils->round_price( $discount['accom']['amount'] * $accom_price / 100 );
				}
			}
			$accom_price -= $accom_discount_value;
			if ( $accom_price < 0 ) {
				$accom_price = 0;
			}
			$accom_added_fees = $this->hbdb->get_accom_based_fees( $resa_info['accom_id'] );
			$accom_added_fees_price = 0;
			foreach ( $accom_added_fees as $fee ) {
				$fee_values = $this->utils->calculate_fees_extras_values( $resa_info, $accom_price, $fee );
				$accom_added_fees_price += $fee_values['price'];
				$fee_name = $this->get_fee_name( $fee );
			}
			$accom_price += $accom_added_fees_price;
		}

		$output .= '<div class="hb-summary-accom-price">';
		$output .= $this->strings['summary_accom_price'];
		$output .= ' ';
		$output .= $this->utils->price_with_symbol( $accom_price );
		$output .= '</div><!-- end .hb-summary-accom-price -->';

		$accom_included_fees = $this->hbdb->get_accom_included_fees( $resa_info['accom_id'] );
		$accom_included_fees_markup = '';
		$price_before_included_fees = $this->utils->calculate_price_before_included_fees( $resa_info, $accom_price, $accom_included_fees );
		foreach ( $accom_included_fees as $fee ) {
			$fee_values = $this->utils->calculate_fees_extras_values( $resa_info, $price_before_included_fees, $fee, $this->strings );
			if ( $fee_values['price'] > 0 ) {
				$fee_name = $this->get_fee_name( $fee );
				$accom_included_fees_markup .= $this->get_included_fee_markup( $fee_name, $fee_values );
			}
		}
		if ( $accom_included_fees_markup ) {
			$output .= $this->wrap_included_fees( 'accom', $accom_included_fees_markup );
		}

		$accom_final_fees_price = 0;
		$accom_final_fees_markup = '';
		$accom_final_fees = $this->hbdb->get_accom_final_fees( $resa_info['accom_id'] );
		foreach ( $accom_final_fees as $fee ) {
			$fee_values = $this->utils->calculate_fees_extras_values( $resa_info, $accom_price, $fee, $this->strings );
			if ( $fee_values['price'] > 0 ) {
				$fee_name = $this->get_fee_name( $fee );
				$accom_final_fees_markup .= $this->get_final_fee_markup( $fee_name, $fee_values );
				$accom_final_fees_price += $fee_values['price'];
			}
		}
		if ( $accom_final_fees_markup ) {
			$output .= $this->wrap_final_fees( 'accom', $accom_final_fees_markup );
		}

		$options_final_fees_price = 0;
		$options_price = $this->calculate_chosen_options_price( $multi_accom_num, $resa_info, $nb_resa );
		if ( $options_price != 0 ) {
			$total_price += $options_price;
			$output .= '<div class="hb-summary-options-price">';
			$output .= $this->strings['summary_options_price'] . ' ';
			if ( $options_price < 0 ) {
				$output .= '-';
				$output .= $this->utils->price_with_symbol( $options_price * -1 );
			} else {
				$output .= $this->utils->price_with_symbol( $options_price );
			}
			$output .= '</div>';

			if ( $options_price > 0 ) {
				$extras_included_fees_markup = '';
				$options_price_before_included_fees = $this->utils->calculate_price_before_included_fees( array(), $options_price, $this->extras_included_fees );
				foreach ( $this->extras_included_fees as $fee ) {
					$fee_values = $this->utils->calculate_fees_extras_values( $resa_info, $options_price_before_included_fees, $fee, $this->strings );
					if ( $fee_values['price'] > 0 ) {
						$fee_name = $this->get_fee_name( $fee );
						$extras_included_fees_markup .= $this->get_included_fee_markup( $fee_name, $fee_values );
					}
				}
				if ( $extras_included_fees_markup ) {
					$output .= $this->wrap_included_fees( 'extras', $extras_included_fees_markup );
				}
				$extras_final_fees_markup = '';
				foreach ( $this->extras_final_fees as $fee ) {
					$fee_values = $this->utils->calculate_fees_extras_values( $resa_info, $options_price, $fee, $this->strings );
					if ( $fee_values['price'] > 0 ) {
						$fee_name = $this->get_fee_name( $fee );
						$extras_final_fees_markup .= $this->get_final_fee_markup( $fee_name, $fee_values );
						$options_final_fees_price += $fee_values['price'];
					}
				}
				if ( $extras_final_fees_markup ) {
					$output .= $this->wrap_final_fees( 'extras', $extras_final_fees_markup );
				}
			}
		}

		$accom_total_price = $accom_price + $options_price;

		$coupon_id = '';
		$coupon_value = 0;
		if ( $this->has_fixed_coupon ) {
			if ( $this->fixed_coupon_amount_left ) {
				if ( $this->fixed_coupon_amount_left > $accom_total_price ) {
					$coupon_value = $accom_total_price;
					$this->fixed_coupon_amount_left -= $accom_total_price;
				} else {
					$coupon_value = $this->fixed_coupon_amount_left;
					$this->fixed_coupon_amount_left = 0;
				}
			}
		} else if ( isset( $_POST['hb-pre-validated-coupon-id'] ) ) {
			$coupon_id = $_POST['hb-pre-validated-coupon-id'];
			if ( $coupon_id ) {
				require_once $this->utils->plugin_directory . '/utils/resa-coupon.php';
				$coupon_info = $this->hbdb->get_coupon_info( $coupon_id );
				$coupon = new HbResaCoupon( $this->hbdb, $this->utils, $coupon_info );
				if ( $coupon->is_valid( array( $resa_info['accom_id'] ), $resa_info['check_in'], $resa_info['check_out'] ) ) {
					if ( $coupon_info['amount_type'] == 'percent' ) {
						$coupon_value = $this->utils->round_price( $accom_total_price * $coupon_info['amount'] / 100 );
					} else {
						$coupon_value = $coupon_info['amount'];
						$this->has_fixed_coupon = true;
						if ( $coupon_value > $accom_total_price ) {
							$this->fixed_coupon_amount_left = $coupon_value - $accom_total_price;
							$coupon_value = $accom_total_price;
						}
					}
				}
			}
		} else if ( isset( $resa_info['coupon_value'] ) && ( $resa_info['coupon_value'] > 0 ) ) {
			$coupon_value = $resa_info['coupon_value'];
		}
		if ( $coupon_value ) {
			$output .= '<div class="hb-summary-coupon-amount">';
			$output .= $this->strings['summary_coupon_amount'];
			$output .= ' ';
			$output .= $this->utils->price_with_symbol( $coupon_value );
			$output .= '</div>';
		}

		$total_discount_amount = 0;
		$global_discount_breakdown = array();
		if ( $this->is_admin ) {
			$discount_amount = round( floatval( $_POST['hb-global-discount-amount'] ), 2 );
			if ( $discount_amount ) {
				if ( $_POST['hb-global-discount-amount-type'] == 'fixed' ) {
					$total_discount_amount = $discount_amount;
				} else if ( $_POST['hb-global-discount-amount-type'] == 'percent' ) {
					$total_discount_amount = $this->utils->round_price( $discount_amount * $accom_total_price / 100 );
				}
			}
		}
		if ( ! $total_discount_amount ) {
			$discounts = $this->utils->get_global_discount( $resa_info['accom_id'], $resa_info['check_in'], $resa_info['check_out'], $accom_total_price );
			$total_discount_amount = $discounts['discount_amount'];
			if ( $this->is_admin ) {
				$global_discount_breakdown = $discounts['discount_breakdown'];
			}
		}
		if ( $total_discount_amount ) {
			$output .= '<div class="hb-summary-discount-amount">';
			if ( $this->is_admin ) {
				$output .= esc_html__( 'Discount amount:', 'hbook-admin' );
			} else {
				$output .= esc_html( $this->strings['summary_discount_amount'] );
			}
			$output .= ' ';
			$output .= $this->utils->price_with_symbol( $total_discount_amount );
			$output .= '</div>';
		}

		$accom_total_price -= $coupon_value;
		$accom_total_price -= $total_discount_amount;

		$global_final_fees_price = 0;
		$global_percent_final_fees_markup = '';
		$total_price_before_global_final_fees = $accom_total_price;
		if ( $total_price_before_global_final_fees < 0 ) {
			$total_price_before_global_final_fees = 0;
		}
		foreach ( $this->global_percent_final_fees as $fee ) {
			$fee_values = $this->utils->calculate_fees_extras_values( $resa_info, $total_price_before_global_final_fees, $fee, $this->strings );
			if ( $fee_values['price'] > 0 ) {
				$fee_name = $this->get_fee_name( $fee );
				$global_percent_final_fees_markup .= $this->get_final_fee_markup( $fee_name, $fee_values );
				$global_final_fees_price += $fee_values['price'];
			}
		}
		if ( $global_percent_final_fees_markup ) {
			$output .= $this->wrap_final_fees( 'global', $global_percent_final_fees_markup );
		}

		$total_price = $accom_total_price + $accom_final_fees_price + $options_final_fees_price + $global_final_fees_price;
		if ( $total_price < 0 ) {
			$total_price = 0;
		}
		if ( $options_price || $coupon_value || $total_discount_amount || $accom_final_fees_price || $global_final_fees_price ) {
			$price_has_details = true;
		}
		$output .= '</div><!-- end .hb-summary-price-details -->';
		return $output;
	}

	private function calculate_chosen_options_price( $multi_accom_num, $resa_info, $nb_resa ) {
		$options_price = 0;
		if ( $resa_info['accom_id'] == 'parent_resa' ) {
			$options = $this->hbdb->get_global_options_with_choices();
		} else {
			$options = $this->hbdb->get_options_with_choices( $resa_info['accom_id'] );
		}
		$nb_nights = $this->utils->get_number_of_nights( $resa_info['check_in'], $resa_info['check_out'] );
		if ( get_option( 'hb_charge_per_day' ) == 'yes' ) {
			$nb_nights++;
		}

		$price_options = $this->utils->calculate_options_price( $resa_info['adults'], $resa_info['children'], $nb_nights, $nb_resa, $options, false );
		$extras_fees_rate = 1;
		$extras_fees_percentages = $this->hbdb->get_extras_fees_percentages();
		foreach ( $extras_fees_percentages as $extras_fee_percentage ) {
			$extras_fees_rate += $extras_fee_percentage / 100;
		}
		$resa_info_options = array();
		if ( isset( $resa_info['options'] ) ) {
			$resa_info_options = json_decode( $resa_info['options'], true );
		}
		foreach ( $options as $option ) {
			if (
				( $nb_resa > 1 ) &&
				( $option['link'] == 'booking' ) &&
				( $multi_accom_num != 'global' )
			) {
				continue;
			}
			$option_markup_id = 'hb-option-' . $option['id'] . '-multi-accom-' . ( $multi_accom_num );
			if ( $option['apply_to_type'] == 'quantity' || $option['apply_to_type'] == 'quantity-per-day' ) {
				$option_price = $price_options[ 'option_' . $option['id'] ];
				$quantity = 0;
				if ( isset( $_POST['hb-has-options-form'] ) ) {
					if ( isset( $_POST[ $option_markup_id ] ) ) {
						$quantity = intval( $_POST[ $option_markup_id ] );
					}
				} else if ( isset( $resa_info_options[ $option['id'] ] ) ) {
					$quantity = $resa_info_options[ $option['id'] ]['quantity'];
				}
				if ( $quantity ) {
					$options_price += $this->utils->round_price( $quantity * $option_price * $extras_fees_rate );
				}
			} else if ( $option['choice_type'] == 'single' ) {
				$option_price = $price_options[ 'option_' . $option['id'] ];
				$chosen_option = false;
				if ( isset( $_POST['hb-has-options-form'] ) ) {
					if ( isset( $_POST[ $option_markup_id ] ) ) {
						$chosen_option = true;
					}
				} else if ( isset( $resa_info_options[ $option['id'] ] ) ) {
					$chosen_option = true;
				}
				if ( $chosen_option ) {
					$options_price += $this->utils->round_price( $option_price * $extras_fees_rate );
				}
			} else {
				$chosen_option = 0;
				if ( isset( $_POST['hb-has-options-form'] ) ) {
					if ( isset( $_POST[ $option_markup_id ] ) ) {
						$chosen_option = $_POST[ $option_markup_id ];
					}
				} else if ( isset( $resa_info_options[ $option['id'] ] ) ) {
					$chosen_option = $resa_info_options[ $option['id'] ]['chosen'];
				}
				foreach ( $option['choices'] as $choice ) {
					if ( $choice['id'] == $chosen_option ) {
						$option_price = $price_options[ 'option_choice_' . $chosen_option ];
						$options_price += $this->utils->round_price( $option_price * $extras_fees_rate );
					}
				}
			}
		}
		return $options_price;
	}

	private function get_fee_name( $fee ) {
		$fee_name = '';
		if ( isset( $this->strings[ 'fee_' . $fee['id'] ] ) ) {
			$fee_name = $this->strings[ 'fee_' . $fee['id'] ];
		}
		if ( $this->is_admin || ! $fee_name ) {
			$fee_name = $fee['name'];
		}
		$fee_name = str_replace( ':', '', $fee_name );
		return $fee_name;
	}

	private function get_included_fee_markup( $name, $values ) {
		return $this->get_fee_markup( $name, $values, true );
	}

	private function get_final_fee_markup( $name, $values ) {
		return $this->get_fee_markup( $name, $values, false );
	}

	private function get_fee_markup( $name, $values, $included ) {
		$output = '<div class="hb-summary-fee">';
		if ( $included ) {
			$output .= '<small>';
		}
		$output .= $name;
		if ( $values['details'] ) {
			$output .= '<span class="hb-summary-fee-details">';
			$output .= ' (' . $values['details'] . ')';
			$output .= '</span>';
		}
		$output .= ': ';
		$output .= $this->utils->price_with_symbol( $values['price'] );
		if ( $included ) {
			$output .= '</small>';
		}
		$output .= '</div>';
		return $output;
	}

	private function wrap_included_fees( $type, $markup ) {
		return $this->wrap_fees( $type, $markup, true );
	}

	private function wrap_final_fees( $type, $markup ) {
		return $this->wrap_fees( $type, $markup, false );
	}

	private function wrap_fees( $type, $markup, $included ) {
		$output = '';
		if ( $included ) {
			$output .= '<div class="hb-included-fees-wrapper hb-included-fees-wrapper-' . $type . '">';
			$output .= $this->get_included_fees_intro_markup( $type );
		} else {
			$output .= '<div class="hb-final-fees-wrapper hb-final-fees-wrapper-' . $type . '">';
		}
		$output .= $markup;
		$output .= '</div>';
		return $output;
	}

	private function get_included_fees_intro_markup( $type ) {
		$output = '<div class="hb-included-fees-title hb-included-fees-title-' . $type . '">';
		$output .= '<small>';
		$output .= $this->strings['summary_included_fees'];
		$output .= '</small>';
		$output .= '</div>';
		return $output;
	}
}
