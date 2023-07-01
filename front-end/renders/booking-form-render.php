<?php
class HBookBookingForm extends HBookRender {

	public function render( $atts ) {
		require_once $this->utils->plugin_directory . '/front-end/booking-form/search-form.php';
		require_once $this->utils->plugin_directory . '/front-end/form-fields.php';
		require_once $this->utils->plugin_directory . '/front-end/booking-form/details-form.php';
		$search_form = new HbSearchForm( $this->hbdb, $this->utils, $this->strings );
		$form_fields = new HbFormFields( $this->strings );
		$details_form = new HbDetailsForm( $this->hbdb, $this->utils, $this->strings, $form_fields );

		$multi_resa = new HbMultiResa( $this->hbdb, $this->utils );

		$resa = array(
			'check_in' => '',
			'check_out' => '',
			'adults' => '',
			'children' => '',
			'accom_id' => '',
			'options' => '',
			'accom_num' => '',
			'search_accom_id' => '',
			'accom_people' => '',
		);

		static $booking_form_num = 0;
		$booking_form_num++;

		$status = '';
		if ( isset( $_GET['payment_confirm'] ) || isset( $_GET['payment_cancel'] ) ) {
			if ( ! isset( $_GET['payment_gateway'] ) ) {
				return 'Error: payment gateway is not defined.';
			}
			$payment_gateway = $this->utils->get_payment_gateway( $_GET['payment_gateway'] );
			$payment_token = $payment_gateway->get_payment_token();
			if ( ! $payment_token ) {
				return 'Error: no payment token.';
			}
			$db_resa = $this->hbdb->get_resa_by_payment_token( $payment_token );
			if ( $db_resa && ( $db_resa['booking_form_num'] == $booking_form_num ) ) {
				if ( isset( $_GET['payment_confirm'] ) ) {
					$payment_confirmation = $payment_gateway->confirm_payment();
					if ( $payment_confirmation['success'] ) {
						if ( $atts['thank_you_page_url'] ) {
							if ( $atts['thank_you_page_url'] == get_permalink( get_the_ID() ) ) {
								$status = 'external-payment-confirm-ok';
							} else {
								$this->utils->load_jquery();
								$this->utils->load_front_end_script( 'booking-form' );
								$output = '<form method="post" action="' . esc_url( $atts['thank_you_page_url'] ) . '" id="hb-resa-payment-thank-you-page-redirect" class="hb-thank-you-page-form">';
								$output .= '<input type="hidden" name="hb-resa-id" value="' . esc_attr( $db_resa['id'] ) . '" />';
								if ( ! isset( $db_resa['parent_id'] ) ) {
									$resa_is_parent = 1;
								} else {
									$resa_is_parent = 0;
								}
								$output .= '<input type="hidden" name="hb-resa-is-parent" value="' . esc_attr( $resa_is_parent ) . '" />';
								$output .= '<input type="hidden" name="hb-resa-payment-type" value="' . esc_attr( $db_resa['payment_type'] ) . '" />';
								$output .= '</form>';
								return $output;
							}
						} else {
							$status = 'external-payment-confirm-ok';
						}
					} else {
						$status = 'external-payment-confirm-error';
					}
				} else if ( isset( $_GET['payment_cancel'] ) ) {
					if ( $db_resa ) {
						/*
						$customer_info = $this->hbdb->get_customer_info( $resa['customer_id'] );
						if ( isset( $resa['additional_info'] ) ) {
							$additional_info = json_decode( $resa['additional_info'], true );
							if ( is_array( $additional_info ) ) {
								foreach ( $additional_info as $info_key => $info_value ) {
									$resa[ $info_key ] = $info_value;
								}
							}
						}
						$resa = array_merge( $customer_info, $resa );
						*/
						$status = 'external-payment-cancel';
						if ( ! isset( $db_resa['parent_id'] ) ) {
							$this->hbdb->delete_parent_resa( $db_resa['id'] );
						} else {
							$this->hbdb->delete_resa( $db_resa['id'] );
						}
					} else {
						$status = 'external-payment-timeout';
					}
				}
			}
		}

		$page_accom_id = '';
		$post_id = '';
		if ( $atts['is_admin'] != 'yes' ) {
			$post_id = $this->utils->get_default_lang_post_id( get_the_ID() );
			$all_accom = $this->hbdb->get_all_accom_ids();
			$all_linked_accom = $this->hbdb->get_all_linked_accom();
			if ( $atts['accom_id'] != '' ) {
				$page_accom_id = $atts['accom_id'];
			} else if ( $post_id && in_array( $post_id, $all_accom ) && ( $atts['all_accom'] != 'yes' ) ) {
				$page_accom_id = $post_id;
			} else if ( isset( $all_linked_accom[ $post_id ] ) && ( $atts['all_accom'] != 'yes' ) ) {
				$page_accom_id = $all_linked_accom[ $post_id ];
			} else if ( count( $all_accom ) == 1 ) {
				$page_accom_id = $all_accom[0];
			}
		}

		$this->utils->load_jquery();
		$this->utils->load_datepicker();
		$this->utils->load_front_end_script( 'utils' );
		$this->utils->load_front_end_script( 'validate-form' );
		$this->utils->load_front_end_script( 'booking-form' );

		if ( $status == 'external-payment-confirm-ok' ) {
			$page_padding_top = intval( get_option( 'hb_page_padding_top' ) );
			if ( ! $page_padding_top ) {
				$page_padding_top = '10';
			} else {
				$page_padding_top = strval( $page_padding_top );
			}
			$this->utils->hb_script_var( 'hb-front-end-booking-form-script', 'hb_payment_confirmation_padding_top', $page_padding_top );
			$customer_info = $this->hbdb->get_customer_info( $db_resa['customer_id'] );
			$payment_type = $db_resa['payment_type'];
			if ( ! isset( $db_resa['parent_id'] ) ) {
				$resas = $this->hbdb->get_resa_by_parent_id( $db_resa['id'] );
				$parent_resa = $db_resa;
			} else {
				$resas = array( $db_resa );
				$parent_resa = false;
			}
			require_once $this->utils->plugin_directory . '/utils/resa-summary.php';
			$summary = new HbResaSummary( $this->hbdb, $this->utils, $this->strings );
			return $summary->get_summary( $resas, $parent_resa, $customer_info, $payment_type );
		} else if ( $status == 'external-payment-confirm-error' ) {
			$this->utils->hb_script_var( 'hb-front-end-booking-form-script', 'hb_payment_confirmation_error', $payment_confirmation['error_msg'] );
		}

		if ( ( $atts['redirection_url'] == '#' ) && ( $atts['is_admin'] != 'yes' ) ) {
			foreach ( $this->utils->get_active_payment_gateways() as $gateway ) {
				foreach ( $gateway->js_scripts() as $js_script ) {
					wp_enqueue_script( $js_script['id'], $js_script['url'], array( 'jquery' ), $js_script['version'], true );
				}
				foreach ( $gateway->js_data() as $js_data_id => $js_data ) {
					if ( ! $js_data ) {
						$js_data = '0';
					} else {
						$js_data = strval( $js_data );
					}
					$this->utils->hb_script_var( 'hb-front-end-booking-form-script', $js_data_id, $js_data );
				}
				foreach ( $gateway->css_styles() as $css_style ) {
					wp_enqueue_style( $css_style['id'], $css_style['url'], array(), $css_style['version'] );
				}
			}
		}

		$this->load_script_data( $page_accom_id, $atts );

		if ( ! $status && isset( $_POST['hb-check-in-hidden'] ) ) {
			$status = 'search-accom';
			$resa['check_in'] = strip_tags( $_POST['hb-check-in-hidden'] );
			$resa['check_out'] = strip_tags( $_POST['hb-check-out-hidden'] );
			$resa['adults'] = strip_tags( $_POST['hb-adults'] );
			$resa['children'] = strip_tags( $_POST['hb-children'] );
			$resa['accom_people'] = strip_tags( $_POST['hb-accom-people'] );
		}

		$results_show_only_accom_id = '';
		if ( isset( $_POST['hb-results-show-only-accom-id'] ) ) {
			$results_show_only_accom_id = $_POST['hb-results-show-only-accom-id'];
		} else if ( $status == 'external-payment-cancel' ) {
			$results_show_only_accom_id = $resa['accom_id'];
		}

		$class_page_accom = '';
		if ( $page_accom_id != '' ) {
			$class_page_accom = ' hb-accom-page';
		}

		if ( $atts['redirection_url'] != '#' ) {
			$exists_main_booking_form = 'yes';
		} else {
			$exists_main_booking_form = 'no';
		}

		if ( $atts['search_form_placeholder'] == 'yes' || get_option( 'hb_search_form_placeholder' ) == 'yes' ) {
			$search_form_placeholder = true;
		} else {
			$search_form_placeholder = false;
		}

		$allowed_check_in_days = 'all';
		$allowed_check_out_days = 'all';
		$minimum_stay = 1;
		$maximum_stay = 9999;
		$conditional_booking_rules = array();
		$seasonal_allowed_check_in_days = array();
		$seasonal_allowed_check_out_days = array();
		$seasonal_minimum_stay = array();
		$seasonal_maximum_stay = array();
		if ( $atts['is_admin'] == 'yes' ) {
			$booking_rules = array();
		} else if ( $page_accom_id ) {
			$booking_rules = $this->hbdb->get_accom_booking_rules( $page_accom_id );
		} else {
			$booking_rules = $this->hbdb->get_all_accom_booking_rules();
		}
		foreach ( $booking_rules as $i => $rule ) {
			if ( $rule['type'] == 'check_in_days' ) {
				if ( $rule['all_seasons'] ) {
					$allowed_check_in_days = $rule['check_in_days'];
				} else {
					$rule_seasons = explode( ',', $rule['seasons'] );
					foreach ( $rule_seasons as $rule_season ) {
						$seasonal_allowed_check_in_days[ $rule_season ] = $rule['check_in_days'];
					}
				}
			} else if ( $rule['type'] == 'check_out_days' ) {
				if ( $rule['all_seasons'] ) {
					$allowed_check_out_days = $rule['check_out_days'];
				} else {
					$rule_seasons = explode( ',', $rule['seasons'] );
					foreach ( $rule_seasons as $rule_season ) {
						$seasonal_allowed_check_out_days[ $rule_season ] = $rule['check_out_days'];
					}
				}
			} else if ( $rule['type'] == 'minimum_stay' ) {
				if ( $rule['all_seasons'] ) {
					if ( $rule['minimum_stay'] > $minimum_stay ) {
						$minimum_stay = $rule['minimum_stay'];
					}
				} else {
					$rule_seasons = explode( ',', $rule['seasons'] );
					foreach ( $rule_seasons as $rule_season ) {
						$seasonal_minimum_stay[ $rule_season ] = $rule['minimum_stay'];
					}
				}
			} else if ( $rule['type'] == 'maximum_stay' ) {
				if ( $rule['all_seasons'] ) {
					if ( $rule['maximum_stay'] < $maximum_stay ) {
						$maximum_stay = $rule['maximum_stay'];
					}
				} else {
					$rule_seasons = explode( ',', $rule['seasons'] );
					foreach ( $rule_seasons as $rule_season ) {
						$seasonal_maximum_stay[ $rule_season ] = $rule['maximum_stay'];
					}
				}
			} else if ( $rule['type'] == 'conditional' && ( $rule['conditional_type'] == 'compulsory' || $rule['conditional_type'] == 'comp_and_rate' ) ) {
				$conditional_booking_rules[] = $rule;
			}
		}

		$form_booking_rules = array(
			'allowed_check_in_days' => $allowed_check_in_days,
			'allowed_check_out_days' => $allowed_check_out_days,
			'minimum_stay' => $minimum_stay,
			'maximum_stay' => $maximum_stay,
			'seasonal_allowed_check_in_days' => $seasonal_allowed_check_in_days,
			'seasonal_allowed_check_out_days' => $seasonal_allowed_check_out_days,
			'seasonal_minimum_stay' => $seasonal_minimum_stay,
			'seasonal_maximum_stay' => $seasonal_maximum_stay,
			'conditional_booking_rules' => $conditional_booking_rules,
		);

		$output = '
		<div id="' . esc_attr( 'hbook-booking-form-' . $booking_form_num ) . '"
			class="' . esc_attr( 'hbook-wrapper hbook-wrapper-booking-form' . $class_page_accom ) . '"
			data-status="' . esc_attr( $status ) . '"
			data-page-accom-id="' . esc_attr( $page_accom_id ) . '"
			data-current-page-id="' . esc_attr( $post_id ) . '"
			data-exists-main-booking-form="' . esc_attr( $exists_main_booking_form ) . '"
			data-force-display-thumb="' . esc_attr( $atts['force_display_thumb'] ) . '"
			data-force-display-desc="' . esc_attr( $atts['force_display_desc'] ) . '"
			data-results-show-only-accom-id="' . esc_attr( $results_show_only_accom_id ) . '"
			data-booking-rules=\'' . json_encode( $this->utils->hb_esc( $form_booking_rules ) ). '\'
		>';

		/* search form */
		$output .= $search_form->get_search_form_markup( $atts['form_id'], $atts['redirection_url'], $atts['search_only'], $search_form_placeholder, $atts['is_admin'], $resa['check_in'], $resa['check_out'], $resa['adults'], $resa['children'], $page_accom_id, $resa['options'], $resa['accom_num'], $resa['accom_people'], $atts );
		if ( $atts['search_only'] == 'yes' ) {
			return $output . '</div><!-- .hbook-wrapper -->';
		}

		/* details form */
		if ( $atts['is_admin'] == 'yes' ) {
			$output .= $details_form->get_admin_details_form_mark_up();
		} else {
			$output .= $details_form->get_details_form_mark_up( $resa, $booking_form_num, $atts['thank_you_page_url'] );
		}

		$output .= '</div><!-- end .hbook-wrapper -->';

		return $output;
	}

	private function load_script_data( $page_accom_id, $atts ) {
		if ( $atts['is_admin'] == 'yes' ) {
			$this->utils->hb_script_var( 'hb-front-end-booking-form-script', 'hb_status_days_all', array() );
		} else {
			static $accom_status_days_loaded = array();
			if ( ! $page_accom_id ) {
				$status_days_accom_id = 'all';
			} else {
				$status_days_accom_id = $page_accom_id;
			}
			if ( ! isset( $accom_status_days_loaded[ $status_days_accom_id ] ) ) {
				$accom_status_days_loaded[ $status_days_accom_id ] = true;

				if ( $status_days_accom_id == 'all' ) {
					// $this->utils->hb_script_var( 'hb-front-end-booking-form-script', 'hb_status_days_all', $this->utils->get_status_days( 'all' ) );
					$this->utils->hb_script_var( 'hb-front-end-booking-form-script', 'hb_status_days_all', array() );
				} else {
					$minimum_stay = 1;
					$booking_rules = $this->hbdb->get_accom_booking_rules( $status_days_accom_id );
					foreach ( $booking_rules as $i => $rule ) {
						if ( $rule['all_seasons'] && $rule['type'] == 'minimum_stay' && $rule['minimum_stay'] > $minimum_stay ) {
							$minimum_stay = $rule['minimum_stay'];
						}
					}
					$status_days = $this->utils->get_status_days( $status_days_accom_id, $minimum_stay );
					$this->utils->hb_script_var( 'hb-front-end-booking-form-script', 'hb_status_days_' . intval( $status_days_accom_id ), $status_days );
				}
			}
		}

		static $booking_form_data_loaded;
		if ( ! $booking_form_data_loaded ) {
			$db_seasons = $this->hbdb->get_all_seasons_with_dates();
			$seasons = array();
			foreach ( $db_seasons as $season ) {
				foreach ( $season['dates'] as $season_dates ) {
					$season_dates['priority'] = $season['priority'];
					$seasons[] = $season_dates;
				}
			}

			$security_bond = 0;
			$security_bond_deposit = 'no';
			if ( get_option( 'hb_security_bond_online_payment' ) == 'yes' ) {
				$security_bond = get_option( 'hb_security_bond_amount' );
				if ( get_option( 'hb_deposit_bond' ) == 'yes' ) {
					$security_bond_deposit = 'yes';
				}
			}

			if ( $atts['is_admin'] == 'yes' ) {
				$page_padding_top = '10';
			} else {
				$page_padding_top = intval( get_option( 'hb_page_padding_top' ) );
				if ( ! $page_padding_top ) {
					$page_padding_top = '10';
				}
			}

			if ( $atts['is_admin'] == 'yes' ) {
				$horizontal_form_min_width = 700;
			} else {
				$horizontal_form_min_width = intval( get_option( 'hb_horizontal_form_min_width' ) );
				if ( ! $horizontal_form_min_width ) {
					$horizontal_form_min_width = 500;
				}
			}

			$details_form_stack_width = intval( get_option( 'hb_details_form_stack_width' ) );
			if ( ! $details_form_stack_width ) {
				$details_form_stack_width = 400;
			}

			$ajax_timeout = intval( get_option( 'hb_ajax_timeout' ) );
			if ( ! $ajax_timeout ) {
				$ajax_timeout = 40000;
			}

			global $wp_locale;
			$decimal_point = '.';
			$thousands_sep = '';
			if ( isset( $wp_locale->number_format['decimal_point'] ) ) {
				$decimal_point = $wp_locale->number_format['decimal_point'];
			}
			if ( isset( $wp_locale->number_format['thousands_sep'] ) ) {
				$thousands_sep = $wp_locale->number_format['thousands_sep'];
			}

			$display_accom_left_threshold = -1;
			if ( get_option( 'hb_display_accom_left' ) == 'yes' ) {
				$display_accom_left_threshold = get_option( 'hb_display_accom_left_threshold' );
				if ( ! is_numeric( $display_accom_left_threshold ) ) {
					$display_accom_left_threshold = 9999;
				}
			}
			$booking_form_data = array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'ajax_timeout' => $ajax_timeout,
				'seasons' => $seasons,
				'price_precision' => get_option( 'hb_price_precision' ),
				'decimal_point' => $decimal_point,
				'thousands_sep' => $thousands_sep,
				'deposit_type' => get_option( 'hb_deposit_type' ),
				'deposit_amount' => get_option( 'hb_deposit_amount' ),
				'security_bond' => $security_bond,
				'security_bond_deposit' => $security_bond_deposit,
				'page_padding_top' => $page_padding_top,
				'horizontal_form_min_width' => $horizontal_form_min_width,
				'details_form_stack_width' => $details_form_stack_width,
				'display_accom_left_threshold' => $display_accom_left_threshold,
				'is_admin' => $atts['is_admin'],
			);

			$this->utils->hb_script_var( 'hb-front-end-booking-form-script', 'hb_booking_form_data', $booking_form_data );
			if ( $atts['is_admin'] != 'yes' ) {
				$this->utils->hb_script_var( 'hb-front-end-booking-form-script', 'hb_text', $this->strings );
			}
			$booking_form_data_loaded = true;
		}
	}
}