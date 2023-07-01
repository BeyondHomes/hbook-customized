<?php
class HbFrontEndAjaxActions {

	private $hbdb;
	private $utils;

	public function __construct( $hbdb, $utils ) {
		$this->hbdb = $hbdb;
		$this->utils = $utils;
	}

	public function hb_get_available_accom() {
		require_once $this->utils->plugin_directory . '/front-end/booking-form/available-accom.php';
		require_once $this->utils->plugin_directory . '/utils/resa-options.php';
		require_once $this->utils->plugin_directory . '/utils/price-calc.php';
		$options_form = new HbOptionsForm( $this->hbdb, $this->utils );
		$strings = $this->hbdb->get_strings( $_POST['is_admin'] );
		$price_calc = new HbPriceCalc( $this->hbdb, $this->utils, $strings );
		$available_accom = new HbAvailableAccom( $this->hbdb, $this->utils, $strings, $price_calc, $options_form );
		$search_request = array(
			'check_in' => $_POST['check_in'],
			'check_out' => $_POST['check_out'],
			'adults' => $_POST['adults'],
			'children' => $_POST['children'],
			'page_accom_id' => $_POST['page_accom_id'],
			'current_page_id' => $_POST['current_page_id'],
			'exists_main_booking_form' => $_POST['exists_main_booking_form'],
			'force_display_thumb' => $_POST['force_display_thumb'],
			'force_display_desc' => $_POST['force_display_desc'],
			'is_admin' => $_POST['is_admin'],
			'admin_accom_id' => $_POST['admin_accom_id'],
			'admin_search_type' => $_POST['admin_search_type'],
			'accom_people' => $_POST['accom_people']
		);
		$response = $available_accom->get_available_accom( $search_request );
		echo( json_encode( $response ) );
		die;
	}

	public function hb_get_summary() {
		$strings = $this->hbdb->get_strings();
		require_once $this->utils->plugin_directory . '/utils/resa-summary.php';
		require_once $this->utils->plugin_directory . '/utils/price-calc.php';
		$summary = new HbResaSummary( $this->hbdb, $this->utils, $strings );
		$price_calc = new HbPriceCalc( $this->hbdb, $this->utils, $strings );
		$accom_ids = array_map( 'intval', explode( '-', $_POST['hb-details-accom-ids'] ) );
		$check_in = $_POST['hb-details-check-in'];
		$check_out = $_POST['hb-details-check-out'];
		$adults_per_accom = array_map( 'intval', explode( '-', $_POST['hb-details-adults'] ) );
		$children_per_accom = array_map( 'intval', explode( '-', $_POST['hb-details-children'] ) );
		$infos = array();
		foreach ( $accom_ids as $i => $accom_id ) {
			$info = array(
				'check_in' => $check_in,
				'check_out' => $check_out,
				'adults' => $adults_per_accom[ $i ],
				'children' => $children_per_accom[ $i ],
				'accom_id' => $accom_id,
			);
			$select_accom_num_name = 'hb-select-accom-num-accom-' . $accom_id . '-multi-accom-' . ( $i + 1 );
			if ( isset( $_POST[ $select_accom_num_name ] ) ) {
				$accom_num = intval( $_POST[ $select_accom_num_name ] );
			} else {
				$accom_num = 0;
			}
			$info['accom_num'] = $accom_num;
			$prices = $price_calc->get_price( $accom_id, $check_in, $check_out, $info['adults'], $info['children'] );
			if ( ! $prices['success'] ) {
				esc_html_e( 'Error. Could not calculate price.', 'hbook-admin' );
				die;
			} else {
				$info['accom_total_price'] = $prices['prices']['accom_total'];
			}
			$infos[] = $info;
		}
		echo( $summary->get_summary( $infos ) );
		die;
	}

	public function hb_create_resa() {
		$response = array();

		$is_admin = false;
		if ( $_POST['hb-details-is-admin'] == 'yes' ) {
			$is_admin = true;
		}
		$accom_ids = array_map( 'intval', explode( '-', $_POST['hb-details-accom-ids'] ) );
		$check_in = $_POST['hb-details-check-in'];
		$check_out = $_POST['hb-details-check-out'];
		$adults_per_accom = array_map( 'intval', explode( '-', $_POST['hb-details-adults'] ) );
		$children_per_accom = array_map( 'intval', explode( '-', $_POST['hb-details-children'] ) );

		$nb_accom = count( $accom_ids );
		$is_multiple_resa = false;
		if ( count( $accom_ids ) > 1 ) {
			$is_multiple_resa = true;
			$accom_ids[] = 'parent_resa';
		}
		$resa_ids = array();
		$saved_resas_info = array();
		$multiple_resa_price = 0;

		$customer_id = 0;
		if ( $is_admin && ( $_POST['hb-admin-customer-type'] == 'id' ) ) {
			$customer_id = intval( $_POST['hb-customer-id'] );
		}
		if ( ! $customer_id ) {
			$customer_info = $this->utils->get_posted_customer_info();
			$customer_email = '';
			if ( isset( $_POST['hb_email'] ) ) {
				$customer_email = stripslashes( strip_tags( $_POST['hb_email'] ) );
			}

			$customer_id = $this->hbdb->get_customer_id( $customer_email );
			if ( $customer_id ) {
				$customer_id = $this->hbdb->update_customer_on_resa_creation( $customer_id, $customer_email, $customer_info );
			} else {
				$customer_id = $this->hbdb->create_customer( $customer_email, $customer_info );
			}
		}
		if ( ! $customer_id ) {
			$response['success'] = false;
			if ( $is_admin ) {
				$response['error_msg'] = $this->hbdb->last_query();
			} else {
				$response['error_msg'] = esc_html__( 'Error. Could not create customer.', 'hbook-admin' );
			}
			echo( json_encode( $this->utils->hb_esc( $response ) ) );
			die;
		}

		$customer_info['id'] = $customer_id;

		require_once $this->utils->plugin_directory . '/utils/price-calc.php';
		$strings = $this->hbdb->get_strings();
		$price_calc = new HbPriceCalc( $this->hbdb, $this->utils, $strings );

		$has_fixed_coupon = false;
		$fixed_coupon_amount_left = 0;
		$coupon_value = 0;
		$coupon_code = '';
		$total_adults = 0;
		$total_children = 0;

		foreach ( $accom_ids as $accom_no => $accom_id ) {
			$resa_info = array(
				'check_in' => $check_in,
				'check_out' => $check_out,
				'lang' => get_locale(),
				'currency' => get_option( 'hb_currency' ),
				'origin' => 'website',
			);

			if ( $accom_id != 'parent_resa' ) {
				$accom_num = 0;
				$adults = $adults_per_accom[ $accom_no ];
				$children = $children_per_accom[ $accom_no ];
				$total_adults += $adults;
				$total_children += $children;
				$resa_info['adults'] = $adults;
				$resa_info['children'] = $children;
			}

			if ( $accom_id == 'parent_resa' ) {
				$accom_num = 'x';
			} else if ( $is_admin || ( get_option( 'hb_select_accom_num' ) == 'yes' ) ) {
				$select_accom_num_name = 'hb-select-accom-num-accom-' . $accom_id . '-multi-accom-' . ( $accom_no + 1 );
				$accom_num = intval( $_POST[ $select_accom_num_name ] );
				if ( $accom_num && ! $this->hbdb->is_available_accom_num( $accom_id, $accom_num, $check_in, $check_out ) ) {
					$response['success'] = false;
					$accom_num_name = $this->hbdb->get_accom_num_name( $accom_id );
					if ( $is_admin ) {
						$response['error_msg'] = sprintf(
							esc_html__( 'The %s (%s) is no longer available.', 'hbook-admin' ),
							$this->utils->get_admin_accom_title( $accom_id ),
							$accom_num_name[ $accom_num ]
						);
					} else {
						$this->hbdb->delete_multi_resa_pending( $resa_ids );
						$error_msg = $this->hbdb->get_string( 'accom_num_no_longer_available' );
						$error_msg = str_replace( '%accom_name', $this->utils->get_accom_title( $accom_id ), $error_msg );
						$error_msg = str_replace( '%accom_num', $accom_num_name[ $accom_num ], $error_msg );
						$response['error_msg'] = $error_msg;
					}
					echo( json_encode( $this->utils->hb_esc( $response ) ) );
					die;
				}
			}
			if ( ! $accom_num ) {
				$accom_num = $this->hbdb->get_first_available_accom_num( $accom_id, $check_in, $check_out );
				if ( ! $accom_num ) {
					$this->hbdb->delete_multi_resa_pending( $resa_ids );
					$response['success'] = false;
					$locale = '';
					if ( $is_admin ) {
						$locale = get_user_locale();
					}
					$response['error_msg'] = $this->hbdb->get_string( 'accom_no_longer_available', $locale );
					echo( json_encode( $this->utils->hb_esc( $response ) ) );
					die;
				}
				if (
					( ( $_POST['hb-payment-flag'] == 'yes' ) && ( get_option( 'hb_resa_paid_has_confirmation' ) == 'yes' ) ) ||
					( ( $_POST['hb-payment-flag'] != 'yes' ) && ( get_option( 'hb_resa_unpaid_has_confirmation' ) == 'yes' ) )
				) {
					$accom_num = 0;
				}
			}

			if ( $accom_id == 'parent_resa' ) {
				$prices = array();
				$prices['accom'] = 0;
				$prices['accom_total'] = 0;
				$prices['discount'] = 0;
			} else {
				$prices = $price_calc->get_price( $accom_id, $check_in, $check_out, $adults, $children );
				if ( ! $prices['success'] ) {
					$response['success'] = false;
					$response['error_msg'] = esc_html__( 'Error. Could not calculate price.', 'hbook-admin' );
					echo( json_encode( $this->utils->hb_esc( $response ) ) );
					die;
				} else {
					$prices = $prices['prices'];
				}
			}

			if ( $accom_id == 'parent_resa' ) {
				$options = $this->hbdb->get_global_options_with_choices();
				$adults = $total_adults;
				$children = $total_children;
			} else {
				$options = $this->hbdb->get_options_with_choices( $accom_id );
			}
			$options_choices = $this->hbdb->get_all( 'options_choices' );
			$choice_name = array();
			foreach ( $options_choices as $choice ) {
				$choice_name[ $choice['id'] ] = $choice['name'];
			}
			$nb_nights = $this->utils->get_number_of_nights( $check_in, $check_out );
			if ( get_option( 'hb_charge_per_day' ) == 'yes' ) {
				$nb_nights++;
			}
			$price_options = $this->utils->calculate_options_price( $adults, $children, $nb_nights, $nb_accom, $options, false );
			$options_total_price = 0;
			$chosen_options = array();
			$extras_fees_rate = 1;
			$extras_fees_percentages = $this->hbdb->get_extras_fees_percentages();
			foreach ( $extras_fees_percentages as $extras_fee_percentage ) {
				$extras_fees_rate += $extras_fee_percentage / 100;
			}
			foreach ( $options as $option ) {
				if (
					$is_multiple_resa &&
					( $option['link'] == 'booking' ) &&
					( $accom_id != 'parent_resa' )
				) {
					continue;
				}
				$option_price = 0;
				$chosen_option = array(
					'name' => $option['name'],
					'amount' => $option['amount'],
					'amount_children' => $option['amount_children'],
					'apply_to_type' => $option['apply_to_type'],
				);
				if ( $is_multiple_resa && ( $option['link'] == 'booking' ) ) {
					$option_markup_id = 'hb-option-' . $option['id'] . '-multi-accom-global';
				} else {
					$option_markup_id = 'hb-option-' . $option['id'] . '-multi-accom-' . ( $accom_no + 1 );
				}
				if ( $option['apply_to_type'] == 'quantity' || $option['apply_to_type'] == 'quantity-per-day' ) {
					$quantity = 0;
					if ( isset( $_POST[ $option_markup_id ] ) ) {
						$quantity = intval( $_POST[ $option_markup_id ] );
					}
					if ( $quantity ) {
						$option_price = $price_options[ 'option_' . $option['id'] ];
						$chosen_option['quantity'] = $quantity;
						$chosen_option['amount'] = $option['amount'];
						$options_total_price += $this->utils->round_price( $quantity * $option_price * $extras_fees_rate );
						$chosen_options[ $option['id'] ] = $chosen_option;
					}
				} else if ( $option['choice_type'] == 'single' ) {
					if ( isset( $_POST[ $option_markup_id ] ) ) {
						$option_price = $price_options[ 'option_' . $option['id'] ];
						$chosen_option['amount'] = $option['amount'];
						$chosen_option['amount_children'] = $option['amount_children'];
						$options_total_price += $this->utils->round_price( $option_price * $extras_fees_rate );
						$chosen_options[ $option['id'] ] = $chosen_option;
					}
				} else {
					foreach ( $option['choices'] as $choice ) {
						if ( isset( $_POST[ $option_markup_id ] ) && ( $_POST[ $option_markup_id ] == $choice['id'] ) ) {
							$option_price = $price_options[ 'option_choice_' . $choice['id'] ];
							$chosen_option['chosen'] = $choice['id'];
							$chosen_option['choice_name'] = $choice_name[ $choice['id'] ];
							$chosen_option['amount'] = $choice['amount'];
							$chosen_option['amount_children'] = $choice['amount_children'];
							$options_total_price += $this->utils->round_price( $option_price * $extras_fees_rate );
						}
					}
					$chosen_options[ $option['id'] ] = $chosen_option;
				}
			}
			$chosen_options = json_encode( $chosen_options );
			$price = $options_total_price + $prices['accom_total'];

			$coupon_id = '';
			$coupon_value = 0;
			if ( $has_fixed_coupon ) {
				if ( $fixed_coupon_amount_left ) {
					if ( $fixed_coupon_amount_left > $price ) {
						$coupon_value = $price;
						$fixed_coupon_amount_left -= $price;
					} else {
						$coupon_value = $fixed_coupon_amount_left;
						$fixed_coupon_amount_left = 0;
					}
				} else {
					$coupon_code = '';
				}
			} else if ( isset( $_POST['hb-pre-validated-coupon-id'] ) ) {
				$coupon_id = $_POST['hb-pre-validated-coupon-id'];
				if ( $coupon_id ) {
					require_once $this->utils->plugin_directory . '/utils/resa-coupon.php';
					$coupon_info = $this->hbdb->get_coupon_info( $coupon_id );
					$coupon = new HbResaCoupon( $this->hbdb, $this->utils, $coupon_info );
					if ( $coupon->is_valid( array( $accom_id ), $check_in, $check_out ) && $coupon->is_still_valid() ) {
						if ( $coupon_info['amount_type'] == 'percent' ) {
							$coupon_value = $this->utils->round_price( $price * $coupon_info['amount'] / 100 );
						} else {
							$coupon_value = $coupon_info['amount'];
							$has_fixed_coupon = true;
							if ( $coupon_value > $price ) {
								$fixed_coupon_amount_left = $coupon_value - $price;
								$coupon_value = $price;
							}
						}
						$coupon_code = $coupon_info['code'];
						$this->hbdb->increment_coupon_use( $coupon_id );
					}
				}
			}

			$total_discount_amount = 0;
			$global_discount = array();
			if ( $is_admin && is_admin() ) {
				$discount_amount = round( floatval( $_POST['hb-global-discount-amount'] ), 2 );
				if ( $discount_amount > 0 ) {
					if ( $_POST['hb-global-discount-amount-type'] == 'fixed' ) {
						$total_discount_amount = $discount_amount;
						$global_discount = array(
							'amount_type' => 'fixed',
							'amount' => '' . $total_discount_amount,
						);
					} else if ( $_POST['hb-global-discount-amount-type'] == 'percent' ) {
						$total_discount_amount = $this->utils->round_price( $discount_amount * $price / 100 );
						$global_discount = array(
							'amount_type' => 'percent',
							'amount' => '' . $discount_amount,
						);
					}
				}
			} else {
				$discounts = $this->utils->get_global_discount( $accom_id, $check_in, $check_out, $price );
				$global_discount = $discounts['discount_breakdown'];
				$total_discount_amount = $discounts['discount_amount'];
			}
			$discount = array(
				'accom' => $prices['discount'],
				'global' => $global_discount,
			);
			$discount_json = json_encode( $discount );

			$price -= $coupon_value;
			$price -= $total_discount_amount;

			$resa_info['accom_id'] = $accom_id;

			$fees = $this->hbdb->get_fees( $accom_id );
			$fees_value = 0;
			$prices['extras'] = $options_total_price;
			$prices['total'] = $price;
			if ( $prices['total'] < 0 ) {
				$prices['total'] = 0;
			}
			$resa_fees = array();
			foreach ( $fees as $fee ) {
				if (
					( $fee['apply_to_type'] == 'global-fixed' ) &&
					( $is_multiple_resa ) &&
					( $accom_id != 'parent_resa' )
				) {
					continue;
				}
				if ( $fee['include_in_price'] == 0 ) {
					$fee_values = $this->utils->calculate_fees_extras_values( $resa_info, $prices, $fee );
					$price += $fee_values['price'];
				}
				unset( $fee['all_accom'] );
				unset( $fee['global'] );
				unset( $fee['fee_id'] );
				unset( $fee['accom_id'] );
				$resa_fees[] = $fee;
			}
			$resa_fees = json_encode( $resa_fees );

			if ( $price < 0 ) {
				$price = 0;
			}

			$resa_info['accom_num'] = $accom_num;
			$resa_info['accom_price'] = $prices['accom'];
			$resa_info['discount'] = $discount_json;
			$resa_info['price'] = $price;
			$resa_info['customer_id'] = $customer_id;
			$resa_info['additional_info'] = array();
			$resa_info['options'] = $chosen_options;
			$resa_info['fees'] = $resa_fees;
			$resa_info['coupon'] = $coupon_code;
			$resa_info['coupon_value'] = $coupon_value;

			if ( $is_multiple_resa ) {
				$multiple_resa_price += $price;
				if ( $accom_id != 'parent_resa' ) {
					$resa_info['status'] = 'multi_resa_pending';
					$resa_id = $this->hbdb->create_resa( $resa_info );
					$resa_ids[] = $resa_id;
					$resa_info['resa_id'] = $resa_id;
					$saved_resas_info[] = $resa_info;
				}
			}
		}
		if ( $is_multiple_resa ) {
			$price = $multiple_resa_price;
			$resa_info['accom_ids'] = $accom_ids;
			$resa_info['check_in'] = $check_in;
			$resa_info['check_out'] = $check_out;
			$resa_info['adults'] = $total_adults;
			$resa_info['children'] = $total_children;
		}

		$deposit = $this->utils->deposit( $check_in, $check_out, $price );

		$security_bond = 0;
		$security_bond_deposit = 0;
		if ( get_option( 'hb_security_bond_online_payment' ) == 'yes' ) {
			$security_bond = get_option( 'hb_security_bond_amount' );
			if ( get_option( 'hb_deposit_bond' ) == 'yes' ) {
				$security_bond_deposit = get_option( 'hb_security_bond_amount' );
			}
		}

		$currency_to_round = array( 'HUF', 'JPY', 'TWD' );
		if ( in_array( get_option( 'hb_currency' ), $currency_to_round ) || ( get_option( 'hb_price_precision' ) == 'no_decimals' ) ) {
			$price = round( $price );
			$deposit = round( $deposit );
		} else {
			$price = round( $price, 2 );
			$deposit = round( $deposit, 2 );
		}

		$booking_form_num = 0;
		$amount_to_pay = 0;
		$payment_type = '';
		$gateway_custom_info = '';
		if ( ! $is_admin ) {
			$booking_form_num = $_POST['hb-details-booking-form-num'];

			if ( $_POST['hb-payment-type'] == 'store_credit_card' && ( get_option( 'hb_resa_payment_store_credit_card' ) == 'yes' || get_option( 'hb_resa_payment' ) == 'store_credit_card' ) ) {
				$payment_type = 'store_credit_card';
			} else if ( $_POST['hb-payment-type'] == 'deposit' && ( get_option( 'hb_resa_payment_deposit' ) == 'yes' || get_option( 'hb_resa_payment' ) == 'deposit' ) ) {
				$amount_to_pay = $deposit + $security_bond_deposit;
				$payment_type = 'deposit';
			} else if ( $_POST['hb-payment-type'] == 'full' && ( get_option( 'hb_resa_payment_full' ) == 'yes' || get_option( 'hb_resa_payment' ) == 'full' ) ) {
				$amount_to_pay = $price + $security_bond;
				$payment_type = 'full';
			} else {
				$amount_to_pay = $price + $security_bond;
				$payment_type = 'offline';
			}

			if ( isset( $_POST['hb-gateway-custom-info'] ) ) {
				$gateway_custom_info = $_POST['hb-gateway-custom-info'];
			}
		}

		$resa_info['booking_form_num'] = $booking_form_num;
		$resa_info['price'] = $price;
		$resa_info['deposit'] = $deposit;
		$resa_info['payment_type'] = $payment_type;
		$resa_info['paid'] = 0;
		$resa_info['additional_info'] = $this->utils->get_posted_additional_booking_info();
		$resa_info['payment_token'] = '';
		$resa_info['gateway_custom_info'] = $gateway_custom_info;
		$resa_info['payment_gateway'] = '';
		$resa_info['amount_to_pay'] = 0;
		$resa_info['payment_info'] = '';
		$resa_info['alphanum_id'] = $this->utils->get_alphanum();
		$resa_info['invoice_counter'] = get_option( 'hb_invoice_counter_next_value', 1 );
		update_option( 'hb_invoice_counter_next_value', $resa_info['invoice_counter'] + 1 );

		$status = '';
		if ( $is_admin ) {
			$status = get_option( 'hb_resa_admin_status' );
			$resa_info['admin_comment'] = $_POST['hb-admin-comment'];
			$resa_info['lang'] = $_POST['hb-resa-admin-lang'];
		} else {
			if ( $_POST['hb-payment-flag'] == 'yes' ) {
				$payment_gateway = $this->utils->get_payment_gateway( $_POST['hb-payment-gateway'] );
				if ( $payment_gateway ) {
					$resa_info['payment_gateway'] = $payment_gateway->name;
					$response = $payment_gateway->process_payment( $resa_info, $customer_info, $amount_to_pay );
				} else {
					$response['success'] = false;
					$response['error_msg'] = esc_html__( 'Error. Could not find payment gateway.', 'hbook-admin' );
				}
				if ( ! $response['success'] ) {
					if ( $is_multiple_resa ) {
						$this->hbdb->delete_multi_resa_pending( $resa_ids );
					}
					echo( json_encode( $this->utils->hb_esc( $response ) ) );
					die;
				}
				if ( isset( $response['payment_info'] ) ) {
					$resa_info['payment_info'] = $response['payment_info'];
				}
				if ( $payment_gateway->has_redirection == 'no' ) {
					if ( get_option( 'hb_resa_paid_has_confirmation' ) == 'no' ) {
						$status = get_option( 'hb_resa_website_status' );
					} else {
						$status = 'pending';
					}
					$resa_info['paid'] = $amount_to_pay;
				} else {
					$status = 'waiting_payment';
					$resa_info['payment_token'] = $response['payment_token'];
					$resa_info['amount_to_pay'] = $amount_to_pay;
				}
			} else {
				$resa_info['payment_gateway'] = '';
				if (
					( ( $price == 0 ) && ( get_option( 'hb_resa_paid_has_confirmation' ) == 'no' ) ) ||
					( get_option( 'hb_resa_unpaid_has_confirmation' ) == 'no' )
				) {
					$status = get_option( 'hb_resa_website_status' );
					if ( ! $accom_num ) {
						$resa_info['accom_num'] = $this->hbdb->get_first_available_accom_num( $accom_id, $check_in, $check_out );
					}
				} else {
					$status = 'pending';
				}
			}
		}

		$resa_info['status'] = $status;
		unset( $resa_info['gateway_custom_info'] );

		$parent_resa_id = 0;
		if ( $is_multiple_resa ) {
			$parent_resa_info = array();
			$parent_resa_info['options'] = $resa_info['options'];
			$parent_resa_info['fees'] = $resa_info['fees'];
			$parent_resa_info['price'] = $resa_info['price'];
			$parent_resa_info['deposit'] = $resa_info['deposit'];
			$parent_resa_info['paid'] = $resa_info['paid'];
			$parent_resa_info['additional_info'] = $resa_info['additional_info'];
			$parent_resa_info['payment_gateway'] = $resa_info['payment_gateway'];
			$parent_resa_info['currency'] = $resa_info['currency'];
			$parent_resa_info['customer_id'] = $resa_info['customer_id'];
			$parent_resa_info['payment_type'] = $resa_info['payment_type'];
			$parent_resa_info['payment_info'] = $resa_info['payment_info'];
			$parent_resa_info['lang'] = $resa_info['lang'];
			$parent_resa_info['payment_token'] = $resa_info['payment_token'];
			$parent_resa_info['amount_to_pay'] = $resa_info['amount_to_pay'];
			$parent_resa_info['booking_form_num'] = $resa_info['booking_form_num'];
			$parent_resa_info['alphanum_id'] = $resa_info['alphanum_id'];
			$parent_resa_info['invoice_counter'] = $resa_info['invoice_counter'];

			$parent_resa_id = $this->hbdb->create_parent_resa( $parent_resa_info );
			foreach ( $resa_ids as $child_resa_id ) {
				$this->hbdb->db->update(
					$this->hbdb->resa_table,
					array(
						'parent_id' => $parent_resa_id,
						'status' => $status,
					),
					array( 'id' => $child_resa_id )
				);
				if (
					( ! $is_admin ) &&
					( $status != 'waiting_payment' ) &&
					( ( $status == 'new' ) || ( $status == 'confirmed' ) )
				) {
					$resa_info_for_accom_block = $this->hbdb->get_single( 'resa', $child_resa_id );
					$this->hbdb->automatic_block_accom( $resa_info_for_accom_block['accom_id'], $resa_info_for_accom_block['accom_num'], $resa_info_for_accom_block['check_in'], $resa_info_for_accom_block['check_out'], $child_resa_id );
				}
			}
			$resa_id = $parent_resa_id;
		} else {
			$resa_id = $this->hbdb->create_resa( $resa_info );
		}

		if ( ! $resa_id && ! $resa_info['paid'] ) {
			$response['success'] = false;
			$response['error_msg'] = esc_html__( 'Error. Could not create reservation.', 'hbook-admin' );
			echo( json_encode( $this->utils->hb_esc( $response ) ) );
			die;
		} else {
			if ( $is_admin ) {
				$customer = $this->hbdb->get_single( 'customers', $customer_id );
				$customer_info = json_decode( $customer['info'], true );
				if ( $customer_info ) {
					$customer_info = json_encode( $this->utils->hb_esc( $customer_info ) );
				} else {
					$customer_info = '[]';
				}
				$resa_info['resa_id'] = $resa_id;
				if ( $parent_resa_id ) {
					$resa_id = '#' . $resa_id;
				}
				$this->utils->send_email( 'new_resa_admin', $resa_id );
				$saved_resas_info[] = $resa_info;
				$response = array();
				$response['resas'] = array();
				foreach ( $saved_resas_info as $saved_resa_info ) {
					$admin_resa_info = array(
						'resa_id' => esc_html( $saved_resa_info['resa_id'] ),
						'resa_parent_id' => esc_html( $parent_resa_id ),
						'price' => esc_html( $saved_resa_info['price'] ),
						'customer' => array(
							'id' => esc_html( $customer['id'] ),
							'info' => $customer_info,
						),
						'resa_alphanum_id' => $saved_resa_info['alphanum_id'],
						'options_info' => wp_kses_post( $this->utils->resa_options_markup_admin( $saved_resa_info['options'] ) ),
						'non_editable_info' => wp_kses_post( $this->utils->resa_non_editable_info_markup( $saved_resa_info ) ),
						'received_on' => esc_html( $this->utils->get_blog_datetime( current_time( 'mysql', 1 ) ) ),
						'additional_info' => json_encode( $this->utils->hb_esc( $saved_resa_info['additional_info'] ) ),
					);
					$admin_resa_info['automatic_blocked_accom'] = array();
					if ( $saved_resa_info['accom_id'] != 'parent_resa' ) {
						$admin_resa_info['accom_id'] = esc_html( $saved_resa_info['accom_id'] );
						$admin_resa_info['accom_num'] = esc_html( $saved_resa_info['accom_num'] );
						$admin_resa_info['adults'] = esc_html( $saved_resa_info['adults'] );
						$admin_resa_info['children'] = esc_html( $saved_resa_info['children'] );
						$admin_resa_info['automatic_blocked_accom'] = $this->utils->hb_esc( $this->hbdb->automatic_block_accom( $saved_resa_info['accom_id'], $saved_resa_info['accom_num'], $saved_resa_info['check_in'], $saved_resa_info['check_out'], $saved_resa_info['resa_id'] ) );
						$discount = json_decode( $saved_resa_info['discount'], true );
						if ( $discount['accom'] ) {
							$admin_resa_info['accom_discount_amount'] = esc_html( $discount['accom']['amount'] );
							$admin_resa_info['accom_discount_amount_type'] = esc_html( $discount['accom']['amount_type'] );
						}
						if ( $discount['global'] ) {
							$admin_resa_info['global_discount_amount'] = esc_html( $discount['global']['amount'] );
							$admin_resa_info['global_discount_amount_type'] = esc_html( $discount['global']['amount_type'] );
						}
						$admin_resa_info['email_logs'] = $this->utils->hb_esc( $this->utils->get_email_logs_txt( $saved_resa_info['resa_id'], 0 ) );
					} else {
						$admin_resa_info['email_logs'] = $this->utils->hb_esc( $this->utils->get_email_logs_txt( $saved_resa_info['resa_id'], 1 ) );
					}
					$response['resas'][] = $admin_resa_info;
				}
			} else {
				$response['resa_id'] = esc_html( $resa_id );
				if ( $parent_resa_id ) {
					$response['resa_is_parent'] = 1;
				} else {
					$response['resa_is_parent'] = 0;
				}
				if ( $status != 'waiting_payment' ) {
					if ( ! $parent_resa_id && ( ( $status == 'new' ) || ( $status == 'confirmed' ) ) ) {
						$this->hbdb->automatic_block_accom( $resa_info['accom_id'], $resa_info['accom_num'], $resa_info['check_in'], $resa_info['check_out'], $resa_id );
					}
					if ( $parent_resa_id ) {
						$resa_id = '#' . $resa_id;
					}
					$this->utils->send_email( 'new_resa', $resa_id );
				}
			}
		}

		$response['success'] = true;
		echo( json_encode( $response ) );
		die;
	}

	public function hb_verify_coupon() {
		$response = array();
		$response['success'] = false;
		$response['msg'] = $this->hbdb->get_string( 'invalid_coupon' );
		$coupon_ids = $this->hbdb->get_coupon_ids_by_code( $_POST['coupon_code'] );
		if ( $coupon_ids ) {
			require_once $this->utils->plugin_directory . '/utils/resa-coupon.php';
			foreach ( $coupon_ids as $coupon_id ) {
				$coupon_info = $this->hbdb->get_coupon_info( $coupon_id );
				$coupon = new HbResaCoupon( $this->hbdb, $this->utils, $coupon_info );
				if ( $coupon->is_valid( explode( '-', $_POST['accom_ids'] ), $_POST['check_in'], $_POST['check_out'] ) ) {
					if ( $coupon->is_still_valid() ) {
						$coupon_amount = $coupon_info['amount'];
						if ( $coupon_info['amount_type'] == 'percent' ) {
							if ( floor( $coupon_amount ) == $coupon_amount ) {
								$coupon_amount = number_format( $coupon_amount );
							}
							$coupon_amount_text = $coupon_amount . '%';
						} else {
							$coupon_amount_text = 	$this->utils->price_with_symbol( $coupon_amount );
						}
						$response['success'] = true;
						$response['msg'] = str_replace( '%amount', $coupon_amount_text, $this->hbdb->get_string( 'valid_coupon' ) );
						$response['coupon_id'] = $coupon_id;
						$response['coupon_amount'] = $coupon_amount;
						$response['coupon_type'] = $coupon_info['amount_type'];
						$response['coupon_amount_text'] = $coupon_amount_text;
						break;
					} else {
						$response['msg'] = $this->hbdb->get_string( 'coupon_no_longer_valid' );
					}
				}
			}
		}
		echo( json_encode( $this->utils->hb_esc( $response ) ) );
		die;
	}
}