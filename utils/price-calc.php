<?php
class HbPriceCalc {

	private $rates;
	private $rates_nights;
	private $hbdb;
	private $utils;
	private $strings;

	public function __construct( $hbdb, $utils, $strings ) {

		$this->hbdb = $hbdb;
		$this->utils = $utils;
		$this->strings = $strings;

		$types = array( 'accom', 'extra_adults', 'extra_children' );
		$rules = $this->hbdb->get_all_rate_booking_rules();
		$rules_ids = array( 0 );
		foreach ( $rules as $rule_id => $rule ) {
			$rules_ids[] = $rule_id;
		}
		$accom_ids = $this->hbdb->get_all_accom_ids();
		$seasons = $this->hbdb->get_all( 'seasons' );
		$seasons_ids = array();
		foreach ( $seasons as $season ) {
			$seasons_ids[] = $season['id'];
		}
		$this->rates = array();
		foreach( $types as $type ) {
			foreach ( $rules_ids as $rule_id ) {
				foreach ( $accom_ids as $accom_id ) {
					foreach ( $seasons_ids as $season_id ) {
						$this->rates[ $type ][ $rule_id ][ $accom_id ][ $season_id ] = array();
					}
				}
			}
		}
		$db_rates = $hbdb->get_all_rates();
		foreach ( $db_rates as $rate ) {
			if ( $rate['rules'] == NULL ) {
				$rules = array( 0 );
			} else {
				$rules = explode( ',', $rate['rules'] );
			}
			if ( $rate['accom'] != NULL && $rate['seasons'] != NULL ) {
				$accom = explode( ',', $rate['accom'] );
				$seasons = explode( ',', $rate['seasons'] );
				foreach ( $rules as $rule_id ) {
					foreach ( $accom as $accom_id ) {
						foreach ( $seasons as $season_id ) {
							$this->rates[ $rate['type'] ][ $rule_id ][ $accom_id ][ $season_id ][] = array(
								'nb_nights' => $rate['nights'],
								'amount' => $rate['amount']
							);
						}
					}
				}
			}
		}
	}

	public function get_price( $accom_id, $str_check_in, $str_check_out, $adults, $children, &$price_breakdown = NULL , $any_season = true ) {
		if ( get_option( 'hb_charge_per_day' ) == 'yes' ) {
			$str_check_out = date( 'Y-m-d', strtotime( $str_check_out . ' +1 day' ) );
		}
		$price_info = array(
			'accom' => array(
				'number' => 1,
				'label' => $this->strings['price_breakdown_accom_price'],
			),
			'extra_adults' => array(
				'number' => 0,
				'label' => ''
			),
			'extra_children' => array(
				'number' => 0,
				'label' => ''
			)
		);

		$nb_nights = $this->utils->get_number_of_nights( $str_check_in, $str_check_out );

		$accom_occupancy = get_post_meta( $accom_id, 'accom_occupancy', true );
		$accom_max_occupancy = get_post_meta( $accom_id, 'accom_max_occupancy', true );
		if ( $adults > $accom_occupancy ) {
			$price_info['extra_adults']['number'] = $adults - $accom_occupancy;
			$price_info['extra_children']['number'] = $children;
		} elseif ( $adults + $children > $accom_occupancy ) {
			$price_info['extra_children']['number'] = $adults + $children - $accom_occupancy;
		}

		if ( $price_info['extra_adults']['number'] > 1 ) {
			$price_info['extra_adults']['label'] = str_replace( '%nb_adults', '%d', $this->strings['price_breakdown_extra_adults_several'] );
			$price_info['extra_adults']['alt_label'] = str_replace( '%nb_adults', '%d', $this->strings['price_breakdown_adults_several'] );
		} else {
			$price_info['extra_adults']['label'] = $this->strings['price_breakdown_extra_adult_one'];
			$price_info['extra_adults']['alt_label'] = $this->strings['price_breakdown_adult_one'];
		}

		if ( $price_info['extra_children']['number'] > 1 ) {
			$price_info['extra_children']['label'] = str_replace( '%nb_children', '%d', $this->strings['price_breakdown_extra_children_several'] );
			$price_info['extra_children']['alt_label'] = str_replace( '%nb_children', '%d', $this->strings['price_breakdown_children_several'] );
		} else {
			$price_info['extra_children']['label'] = $this->strings['price_breakdown_extra_child_one'];
			$price_info['extra_children']['alt_label'] = $this->strings['price_breakdown_child_one'];
		}

		$nights = array();
		$current_night = date( 'Y-m-d', strtotime( $str_check_in ) );
		while ( strtotime( $str_check_out ) > strtotime( $current_night ) ) {
			$nights[] = $current_night;
			$current_night = date( 'Y-m-d', strtotime( $current_night . ' + 1 day' ) );
		}

		$rule_ids = array();
		$rules = $this->hbdb->get_rate_booking_rules();
		if ( $rules ) {
			$check_in_day = $this->utils->get_day_num( $str_check_in );
			$check_out_day = $this->utils->get_day_num( $str_check_out );
			foreach ( $rules as $rule ) {
				$allowed_check_in_days = explode( ',', $rule['check_in_days'] );
				$allowed_check_out_days = explode( ',', $rule['check_out_days'] );
				if (
					( in_array( $check_in_day, $allowed_check_in_days ) ) &&
					( in_array( $check_out_day, $allowed_check_out_days ) ) &&
					( $nb_nights >= $rule['minimum_stay'] ) &&
					( $nb_nights <= $rule['maximum_stay'] )
				) {
					$rule_ids[] = $rule['id'];
				}
			}
		}

		$price = 0;
		$accom_price = 0;
		$prices = array();
		$price_breakdown = '';

		foreach ( $price_info as $type => $p ) {
			if ( $p['number'] > 0 ) {
				$price_before = $price;
				$result = $this->get_price_per_type( $type, $p['number'], $rule_ids, $accom_id, $nights, $str_check_out, $price , $any_season );
				if ( $type == 'accom' ) {
					$accom_price = $price;
				}
				if ( ! is_array( $result ) ) {
					return array( 'success' => false, 'error' => $result );
				} else {
					if ( count( $result ) > 0 ) {
						$price_breakdown .= '<span class="hb-price-breakdown-' . $type . '">';
						$price_breakdown .= '<span class="hb-price-breakdown-title">';
						if ( $accom_price > 0 ) {
							$price_breakdown .= sprintf( $p['label'], $p['number'] );
						} else {
							$price_breakdown .= sprintf( $p['alt_label'], $p['number'] );
						}
						$price_breakdown .= ' <span class="hb-price-breakdown-amount">' . $this->utils->price_with_symbol( $price - $price_before );
						$price_breakdown .= '</span></span>';
						if ( get_option( 'hb_display_detailed_accom_price' ) != 'no' ) {
							foreach ( $result as $r ) {
								$number_of_nights = $this->utils->get_number_of_nights( $r['start_date'], $r['end_date'] );
								$sub_price = $number_of_nights * $r['price'] * $p['number'];
								if ( $p['number'] > 1 ) {
									$sub_number = ' ' . $p['number'] . ' x ';
								} else {
									$sub_number = '';
								}
								$price_breakdown_dates = $this->strings['price_breakdown_dates'];
								$price_breakdown_dates = str_replace( '%from_date', '<span class="hb-format-date">' . $r['start_date'] . '</span>', $price_breakdown_dates );
								if ( get_option( 'hb_charge_per_day' ) == 'yes' ) {
									$end_r = date( 'Y-m-d', strtotime( $r['end_date'] . ' -1 day' ) );
									$price_breakdown_dates = str_replace( '%to_date', '<span class="hb-format-date">' . $end_r . '</span>', $price_breakdown_dates );
								}
								$price_breakdown_dates = str_replace( '%to_date', '<span class="hb-format-date">' . $r['end_date'] . '</span>', $price_breakdown_dates );
								$price_breakdown_dates = str_replace( ':', '', $price_breakdown_dates );
								if ( $r['multiple_nights_rate'] && $number_of_nights % $r['multiple_nights_rate']['nb_nights'] == 0  ) {
									$stay_length = $number_of_nights / $r['multiple_nights_rate']['nb_nights'];
									$sub_sub_price = $r['multiple_nights_rate']['rate'];
									$stay_str = $this->strings['price_breakdown_multiple_nights'];
									$stay_str = str_replace( '%nb_nights', $r['multiple_nights_rate']['nb_nights'], $stay_str );
								} else {
									$stay_length = $number_of_nights;
									$sub_sub_price = $r['price'];
									if ( $stay_length == 1 ) {
										$stay_str = $this->strings['price_breakdown_night_one'];
									} else {
										$stay_str = $this->strings['price_breakdown_nights_several'];
									}
								}
								$price_breakdown .=
										'<span class="hb-price-breakdown-section">' .
										$price_breakdown_dates .
										' (' .
										$stay_length . ' ' . $stay_str .
										' x ' .
										$sub_number .
										$this->utils->price_with_symbol( $sub_sub_price ) .
										') : ' .
										$this->utils->price_with_symbol( $sub_price ) .
										'</span>';
							}
						}
						$price_breakdown .= '</span>';
					}
				}
			}
		}
		$price = $this->utils->round_price( $price );
		$prices['accom'] = $price;

		$discount_ids = array();
		$discounts = $this->hbdb->get_discounts_rules( $accom_id );
		$discount_ids = $this->utils->discounts_observe_rules( $discounts, $str_check_in, $str_check_out );

		$total_discount_amount = 0;
		$nb_discount = 0;
		$saved_discount_percent_value = 0;
		foreach ( $discount_ids as $discount_id ) {
			$discount_info = $this->hbdb->get_discount_info( $discount_id, $accom_id, $this->hbdb->get_season( $str_check_in ) );
			if ( $discount_info ) {
				if ( $discount_info['amount_type'] == 'fixed' ) {
					$discount_amount = $discount_info['amount'];
				} else {
					$discount_percent_value = 0;
					$nb_nights_for_discount = 0;
					$current_night = date( 'Y-m-d', strtotime( $str_check_in ) );
					while ( strtotime( $str_check_out ) > strtotime( $current_night ) ) {
						$discount_info_percent = $this->hbdb->get_discount_info( $discount_id, $accom_id, $this->hbdb->get_season( $current_night ) );
						if ( $discount_info_percent && ( $discount_info_percent['amount_type'] == 'percent' ) ) {
							$discount_percent_value += $discount_info_percent['amount'];
						}
						$current_night = date( 'Y-m-d', strtotime( $current_night . ' + 1 day' ) );
						$nb_nights_for_discount++;
					}
					$discount_percent_value = round( $discount_percent_value / $nb_nights_for_discount, 2 );
					$discount_amount = $this->utils->round_price( $discount_percent_value * $price / 100 );
				}
				if ( ( $discount_info['apply_to_type'] == 'global' ) && ( $discount_amount > 0 ) ) {
					$price_breakdown .= '<span class="hb-price-breakdown-global-discount">';
					$price_breakdown .= '<span class="hb-global-discount-amount">';
					if ( $discount_info['amount_type'] == 'fixed' ) {
						$price_breakdown .= $discount_amount;
					} else {
						$price_breakdown .= $discount_percent_value;
					}
					$price_breakdown .= '</span>';
					$price_breakdown .= '<span class="hb-global-discount-type">';
					$price_breakdown .= $discount_info['amount_type'];
					$price_breakdown .= '</span>';
					$price_breakdown .= '</span>';
				} else {
					$nb_discount++;
					$total_discount_amount += $discount_amount;
					if ( $discount_info['amount_type'] == 'percent' ) {
						$saved_discount_percent_value = $discount_percent_value;
					}
				}
			}
		}

		$returned_discount = array();
		if ( $total_discount_amount ) {
			$total_discount_amount = $this->utils->round_price( $total_discount_amount );
			$price_breakdown .= '<span class="hb-price-breakdown-discount">';
			$price_breakdown .= '<span class="hb-price-breakdown-title">';
			if ( ( $nb_discount == 1 ) && $saved_discount_percent_value ) {
				$price_breakdown .= str_replace( ':', '', $this->strings['price_breakdown_discount'] );
				$price_breakdown .=
					'<span class="hb-price-breakdown-percent-value">' .
					' (' .
					$saved_discount_percent_value .
					'%) ' .
					'</span>' .
					':';
				$returned_discount = array(
					'amount_type' => 'percent',
					'amount' => '' . $saved_discount_percent_value,
				);
			} else {
				$price_breakdown .= $this->strings['price_breakdown_discount'];
				$returned_discount = array(
					'amount_type' => 'fixed',
					'amount' => '' . $total_discount_amount,
				);
			}
			$price_breakdown .= ' <span class="hb-price-breakdown-amount">';
			$price_breakdown .= $this->utils->price_with_symbol( $total_discount_amount );
			$price_breakdown .= '</span>';
			$price_breakdown .= '</span>';
			$price_breakdown .= '</span>';

			$price = $price - $total_discount_amount;
			if ( $price < 0 ) {
				$price = 0;
			}
		}
		$prices['discount'] = $returned_discount;

		$fees = $this->hbdb->get_accom_based_fees( $accom_id );
		if ( $fees ) {
			$price_before_fees = $price;
			$fee_breakdown = '';
			$resa = array(
				'check_in' => $str_check_in,
				'check_out' => $str_check_out,
				'adults' => $adults,
				'children' => $children,
			);
			$this->apply_fees( $fees, $resa, $price, $fee_breakdown );
			$nb_added_fees = substr_count( $fee_breakdown, 'hb-fee-accom-added' );
			if ( $nb_added_fees > 0 ) {
				$price_breakdown .= '<span class="hb-price-breakdown-fees">';
				if ( $nb_added_fees > 1 ) {
					$price_breakdown .= '<span class="hb-price-breakdown-title">';
					$price_breakdown .= $this->strings['price_breakdown_fees'];
					$price_breakdown .= ' <span class="hb-price-breakdown-amount">';
					$price_breakdown .= $this->utils->price_with_symbol( $price - $price_before_fees );
					$price_breakdown .= '</span>';
					$price_breakdown .= '</span>';
					$price_breakdown .= $fee_breakdown;
				} else {
					$price_breakdown .= '<b>';
					$price_breakdown .= $fee_breakdown;
					$price_breakdown .= '</b>';
				}
				$price_breakdown .= '</span>';
			} else {
				$price_breakdown .= $fee_breakdown;
			}
		}

		$prices['accom_total'] = $this->utils->round_price( $price );

		return array( 'success' => true, 'prices' => $prices );
	}

	private function get_price_per_type( $type, $multi, $rule_ids, $accom_id, $nights, $str_check_out, &$price , $any_season =false) {
		$list_of_price = array();
		$current_night_price = -1;
		$current_count_nights = -1;
		$multiple_nights_rate = false;
		$night_groups = array();

		$previous_season_id = 0;
		$current_night_group = array();
		foreach ( $nights as $night ) {
			if($any_season){
				$season_id = $this->hbdb->get_season_with_considering_accom_rate( $night , $accom_id);
			}else{
				$season_id = $this->hbdb->get_season( $night );
			}

			if ( $season_id === false ) {
				return str_replace( '%night', $night, $this->strings['error_season_not_defined'] );
			}
			if ( empty( $current_night_group ) || ( $previous_season_id == $season_id ) ) {
				$current_night_group[] = $night;
			} else {
				$night_groups[] = array(
					'season_id' => $previous_season_id,
					'nights' => $current_night_group
				);
				$current_night_group = array( $night );
			}
			$previous_season_id = $season_id;
		}
		$night_groups[] = array(
			'season_id' => $previous_season_id,
			'nights' => $current_night_group
		);

		foreach ( $night_groups as $night_group ) {
			$season_id = $night_group['season_id'];
			$nights = $night_group['nights'];

			$rates = array();
			foreach ( $rule_ids as $rule_id ) {
				$rates = $this->rates[ $type ][ $rule_id ][ $accom_id ][ $season_id ];
				if ( $rates ) {
					break;
				}
			}
			if ( ! $rates ) {
				$rates = $this->rates[ $type ][0][ $accom_id ][ $season_id ];
			}
			if ( ! $rates && ( $type == 'extra_adults' || $type == 'extra_children' ) ) {
				$current_night_price = -1;
				if ( ( count( $list_of_price ) > 0 ) && ( ! $list_of_price[ count( $list_of_price ) - 1 ]['end_date'] ) ) {
					$list_of_price[ count( $list_of_price ) - 1 ]['end_date'] = $nights[0];
				}
				continue;
			}
			if ( ! $rates ) {
				$season = $this->hbdb->get_single( 'seasons', $season_id );
				$error_message = str_replace( '%season_name', '<b>' . $season['name'] . '</b>', $this->strings['error_rate_not_defined'] );
				$error_message = str_replace( '%accom_name', '<b>' . get_the_title( $accom_id ) . '</b>', $error_message );
				return $error_message;
			}

			$rate_nb_nights_value = array();
			foreach ( $rates as $rate ) {
				$rate_nb_nights_value[ $rate['nb_nights'] ] = $rate['amount'];
			}
			$available_rate_nb_nights = array_keys( $rate_nb_nights_value );
			sort( $available_rate_nb_nights );

			$night_sub_groups = array();
			$available_rate_nb_nights_pointer = count( $available_rate_nb_nights ) - 1;
			$rate_nb_nights = $available_rate_nb_nights[ $available_rate_nb_nights_pointer ];
			$new_night_sub_group = array();
			do {
				if ( count( $nights ) >= $rate_nb_nights ) {
					for ( $i = 0; $i < $rate_nb_nights; $i++ ) {
						$new_night_sub_group[] = array_shift( $nights );
					}
					$night_sub_groups[] = $new_night_sub_group;
					$new_night_sub_group = array();
				} else {
					$available_rate_nb_nights_pointer--;
					if ( $available_rate_nb_nights_pointer < 0 ) {
						$night_sub_groups[] = $nights;
						$nights = array();
					} else {
						$rate_nb_nights = $available_rate_nb_nights[ $available_rate_nb_nights_pointer ];
					}
				}
			} while ( count( $nights ) > 0 );

			foreach ( $night_sub_groups as $nights ) {
				$is_multiple_nights_rate = false;
				if ( isset( $rate_nb_nights_value[ count( $nights ) ] ) ) {
					if ( count( $nights ) > 1 ) {
						$is_multiple_nights_rate = true;
					}
					$rate_amount = $rate_nb_nights_value[ count( $nights ) ];
					$rate_nb_nights = count( $nights );
					$night_price = $rate_amount / $rate_nb_nights;
					$price += $rate_amount * $multi;
				} else {
					$rate_nb_nights = $available_rate_nb_nights[0];
					$rate_amount = $rate_nb_nights_value[ $rate_nb_nights ];
					$night_price = $rate_amount / $rate_nb_nights;
					$price += $night_price * count( $nights ) * $multi;
				}

				if ( $night_price != $current_night_price || count( $nights ) != $current_count_nights ) {
					if ( $is_multiple_nights_rate ) {
						$multiple_nights_rate = array(
							'nb_nights' => $rate_nb_nights,
							'rate' => $rate_amount
						);
					} else {
						$multiple_nights_rate = false;
					}
					if ( ( count( $list_of_price ) > 0 ) && ( ! $list_of_price[ count( $list_of_price ) - 1 ]['end_date'] ) ) {
						$list_of_price[ count( $list_of_price ) - 1 ]['end_date'] = $nights[0];
					}
					$new_price = array(
						'start_date' => $nights[0],
						'end_date' => '',
						'price' => $night_price,
						'multiple_nights_rate' => $multiple_nights_rate
					);
					if ( $new_price['price'] != 0 ) {
						$list_of_price[] = $new_price;
					}
					$current_night_price = $night_price;
					$current_count_nights = count( $nights );
				}
			}
		}

		if ( ( count( $list_of_price ) > 0 ) && ( ! $list_of_price[ count( $list_of_price ) - 1 ]['end_date'] ) ) {
			$list_of_price[ count( $list_of_price ) - 1 ]['end_date'] = date( 'Y-m-d', strtotime( $str_check_out ) );
		}
		return $list_of_price;
	}

	private function apply_fees( $fees, $resa, &$price, &$price_breakdown ) {
		$fee_details_strings = array(
			'price_breakdown_night_one' => $this->strings['price_breakdown_night_one'],
			'price_breakdown_nights_several' => $this->strings['price_breakdown_nights_several'],
			'fee_details_adult_one' => $this->strings['fee_details_adult_one'],
			'fee_details_adults_several' => $this->strings['fee_details_adults_several'],
			'fee_details_child_one' => $this->strings['fee_details_child_one'],
			'fee_details_children_several' => $this->strings['fee_details_children_several'],
			'fee_details_persons' => $this->strings['fee_details_persons'],
		);

		$price_before_fees = $price;
		$added_fees_amount = 0;
		foreach ( $fees as $fee ) {
			if ( $fee['include_in_price'] == 1 ) {
				$fee_values = $this->utils->calculate_fees_extras_values( $resa, $price_before_fees, $fee );
				$added_fees_amount += $fee_values['price'];
			}
		}
		$price_before_included_fees = $this->utils->calculate_price_before_included_fees( $resa, $price_before_fees + $added_fees_amount, $fees );

		foreach ( $fees as $fee ) {
			if ( $fee['include_in_price'] == 2 ) {
				$fee_values = $this->utils->calculate_fees_extras_values( $resa, $price_before_included_fees, $fee, $fee_details_strings );
			} else {
				$fee_values = $this->utils->calculate_fees_extras_values( $resa, $price_before_fees, $fee, $fee_details_strings );
			}
			if ( $fee_values['price'] > 0 ) {
				$fee_name = '';
				if ( isset( $this->strings['fee_' . $fee['id'] ] ) ) {
					$fee_name = $this->strings['fee_' . $fee['id'] ];
				}
				if ( ! $fee_name ) {
					$fee_name = $fee['name'];
				}
				$fee_name = str_replace( ':', '', $fee_name );
				$fee_class = 'hb-fee hb-price-breakdown-section';
				if ( $fee['include_in_price'] == 0 ) {
					$fee_class .= ' hb-fee-accom-final';
				} else if ( $fee['include_in_price'] == 1 ) {
					$fee_class .= ' hb-fee-accom-added';
				} else if ( $fee['include_in_price'] == 2 ) {
					$fee_class .= ' hb-fee-accom-included';
				}
				$fee_class .= ' hb-fee-apply-to-type-' . $fee_values['apply_to_type'];
				$fee_txt = '<span class="' . $fee_class . '">' . $fee_name;
				if ( $fee_values['details'] ) {
					$fee_txt .= '<span class="hb-price-breakdown-fee-details">';
					$fee_txt .= ' (' . $fee_values['details'] . ') ';
					$fee_txt .= '</span>';
				}
				$fee_txt .= ': ';
				$fee_txt .= $this->utils->price_with_symbol( $fee_values['price'] );

				if ( $fee['include_in_price'] == 0 ) {
					$fee_txt .= '<span data-price="' . $fee_values['price'] . '"></span>';
				} else if ( $fee['include_in_price'] == 1 ) {
					$price += $fee_values['price'];
				}
				$fee_txt .= '</span>';
				$price_breakdown .= $fee_txt;
			}
		}
	}

}