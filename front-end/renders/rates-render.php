<?php
class HBookRates extends HBookRender {

	public function render( $atts ) {
		$accom_id = $atts['accom_id'];
		if ( $accom_id == '' ) {
			$accom_id = $this->utils->get_default_lang_post_id( get_the_ID() );
		}
		$all_linked_accom = $this->hbdb->get_all_linked_accom();
		if ( isset( $all_linked_accom[ $accom_id ] ) ) {
			$accom_id = $all_linked_accom[ $accom_id ];
		}
		$all_accom = $this->hbdb->get_all_accom_ids();
		if ( ! in_array( $accom_id, $all_accom ) ) {
			if ( $atts['accom_id'] == '' ) {
				return esc_html__( 'Invalid shortcode. Use: [hb_rates accom_id="ID"]', 'hbook-admin' );
			} else if ( get_post_type( $accom_id ) == 'hb_accommodation' ) {
				return esc_html__( 'Invalid shortcode. Please use the id of an accommodation which is set in the website default language.', 'hbook-admin' );
			} else {
				return sprintf( esc_html__( 'Invalid shortcode. Could not find an accommodation whose id is %s.', 'hbook-admin' ), esc_html( $accom_id ) );
			}
		}

		$rule = 0;
		if ( $atts['rule'] != '' ) {
			$rule = $this->hbdb->get_rule_by_name( $atts['rule'] );
			if ( ! $rule ) {
				return 'Invalid shortcode. The rule "' . esc_html( $atts['rule'] ) . '" does not exist.';
			}
		}

		if ( ( $atts['season'] != '' ) && ( $atts['seasons'] == '' ) ) {
			$atts['seasons'] = $atts['season'];
		}

		$all_seasons_dates = array();
		if ( $atts['seasons'] != '' ) {
			$season_names = explode( ',', $atts['seasons'] );
			$seasons = array();
			$seasons_dates = array();
			foreach ( $season_names as $season_name ) {
				$season = $this->hbdb->get_season_by_name( trim( $season_name ) );
				if ( ! $season ) {
					return 'Invalid shortcode. The season "' . esc_html( $season_name ) . '" does not exist.';
				}
				$season_dates = $this->hbdb->get_all_season_dates( $season['id'] );
				if ( $season_dates ) {
					usort( $season_dates, array( $this, 'hb_compare_season_dates' ) );
					$season['dates'] = $season_dates;
					$seasons[] = $season;
					$all_seasons_dates = array_merge( $all_seasons_dates, $season_dates );
				}
			}
		} else if ( $atts['chrono'] ) {
			$seasons = array();
			$all_seasons_dates = $this->hbdb->get_all( 'seasons_dates' );
			usort( $all_seasons_dates, array( $this, 'hb_compare_season_dates' ) );
			foreach ( $all_seasons_dates as $season_dates ) {
				$season = $this->hbdb->get_single( 'seasons', $season_dates['season_id'] );
				$season['dates'] = array( $season_dates );
				$seasons[] = $season;
			}
		} else {
			$seasons = $this->hbdb->get_all( 'seasons' );
			$seasons_dates = array();
			foreach ( $seasons as $i => $season ) {
				$season_dates = $this->hbdb->get_all_season_dates( $season['id'] );
				if ( $season_dates ) {
					$all_seasons_dates = array_merge( $all_seasons_dates, $season_dates );
					usort( $season_dates, array( $this, 'hb_compare_season_dates' ) );
					$seasons[ $i ]['start_date'] = $season_dates[0]['start_date'];
					$seasons[ $i ]['dates'] = $season_dates;
				} else {
					unset( $seasons[ $i ] );
				}
			}
			usort( $seasons, array( $this, 'hb_compare_season_dates' ) );
		}

		$price_per_night = false;
		if ( $atts['days'] == '' ) {
			foreach ( $all_seasons_dates as $dates ) {
				if ( $dates['days'] != '0,1,2,3,4,5,6' ) {
					$price_per_night = true;
					break;
				}
			}
		}

		if ( $atts['show_season_name'] && $price_per_night ) {
			$width = '20%';
		} else if ( $atts['show_season_name'] || $price_per_night ) {
			$width = '25%';
		} else {
			$width = '33%';
		}

		$this->utils->load_jquery();
		$this->utils->load_datepicker();
		$this->utils->load_front_end_script( 'utils' );
		$this->utils->load_front_end_script( 'rates' );

		$output = '
			<table class="hb-rates-table">
				<thead>
					<tr>';
			if ( $atts['show_season_name'] ) {
				$output .= '
						<th width="' . $width . '">' . $this->strings['table_rates_season'] . '</th>';
			}
			$output .= '
						<th class="hb-rate-date" width="' . $width . '">' . $this->strings['table_rates_from'] . '</th>
						<th class="hb-rate-date" width="' . $width . '">' . $this->strings['table_rates_to'] . '</th>';
			if ( $price_per_night ) {
				$output .= '
						<th width="' . $width . '">' . $this->strings['table_rates_nights'] . '</th>';
			}
			$output .= '
						<th width="' . $width . '">' . $this->strings['table_rates_price'] . '</th>
					</tr>
				</thead>';

		if ( $atts['days'] != '' ) {
			$days = explode( ',', $atts['days'] );
		} else {
			$days = false;
		}
		$output .= '<tbody>';
		foreach ( $seasons as $season ) {
			$output_dates = array();
			if ( $days ) {
				foreach( $season['dates'] as $dates ) {
					$dates_days = explode( ',', $dates['days'] );
					$tmp = array_intersect( $dates_days, $days );
					if ( ! empty( $tmp ) ) {
						$output_dates[] = $dates;
					}
				}
			} else {
				$output_dates = $season['dates'];
			}
			foreach ( $output_dates as $j => $dates ) {
				$output .= '
					<tr class="hb-tr-season-' . $season['id'] . '">';
				if ( ( $j == 0 ) && $atts['show_season_name'] ) {
					$season_name = '';
					if ( isset( $this->strings['season_' . $season['id'] ] ) ) {
						$season_name = $this->strings['season_' . $season['id'] ];
					}
					if ( ! $season_name ) {
						$season_name = $season['name'];
					}
					$output .= '
						<td rowspan="' . count( $output_dates ) . '">' . $season_name . '</td>';
				}
				$output .= '
						<td class="hb-rate-date hb-format-date">' . $dates['start_date'] . '</td>
						<td class="hb-rate-date hb-format-date">' . $dates['end_date'] . '</td>';
				if ( $price_per_night ) {
					if ( $dates['days'] == '0,1,2,3,4,5,6' ) {
						$output .= '
							<td>' . $this->strings['table_rates_all_nights'] . '</td>';
					} else {
						$output .= '
							<td class="hb-rate-days">' . $dates['days'] . '</td>';
					}
				}
				if ( $j == 0 ) {
					$output .= '
						<td rowspan="' . count( $output_dates ) . '" class="hb-rate-price">';
					$rates_and_nights = $this->hbdb->get_rate_and_nights( $atts['type'], $rule, $accom_id, $season['id'], $atts['nights'] );
					if ( count( $rates_and_nights ) > 1 ) {
						$output .= '<table class="hb-rate-sub-prices" style="border: 0; margin: 0;">';
					}
					foreach ( $rates_and_nights as $k => $rate_and_nights ) {
						if ( count( $rates_and_nights ) > 1 ) {
							$output .= '<tr style="border: 0;">';
							if ( $k == 0 ) {
								$output .= '<td style="border: 0;">';
							} else {
								$output .= '<td style="border-bottom: 0; border-left:0; border-right: 0;">';
							}
						}
						if ( ! $atts['show_global_price'] ) {
							$output .= $this->utils->price_with_symbol( $rate_and_nights['amount'] / $rate_and_nights['nights'] );
							$output .= $atts['custom_text_after_amount'];
						} else {
							$output .= $this->utils->price_with_symbol( $rate_and_nights['amount'] );
							$output .= ' ';
							if ( $atts['custom_text_after_amount'] != '' ) {
								$output .= $atts['custom_text_after_amount'];
							} else {
								if ( $rate_and_nights['nights'] > 1 ) {
									$output .= str_replace( '%nb_nights', $rate_and_nights['nights'], $this->strings['table_rates_for_night_stay'] );
								} else {
									$output .= $this->strings['table_rates_per_night'];
								}
							}
						}
						if ( count( $rates_and_nights ) > 1 ) {
							$output .= '</td></tr>';
						}
					}
					if ( count( $rates_and_nights ) > 1 ) {
						$output .= '</table>';
					}
					$output .=
						'</td>';
				}
				$output .= '
					</tr>';
			}
		}
		$output .= '
				</tbody>
			</table>';

		$output = apply_filters( 'hb_rates_markup', $output );

		$output = wp_kses_post( $output );

		return $output;
	}

	private function hb_compare_season_dates( $season_dates_a, $season_dates_b ) {
		if ( $season_dates_a['start_date'] == $season_dates_b['start_date'] ) {
			if ( isset( $season_dates_a['days'] ) ) {
				$days_a = $season_dates_a['days'];
				$days_b = $season_dates_b['days'];
			} else {
				$days_a = $season_dates_a['dates'][0]['days'];
				$days_b = $season_dates_b['dates'][0]['days'];
			}
			if ( $days_a > $days_b ) {
				return 1;
			} else {
				return -1;
			}
		} else if ( $season_dates_a['start_date'] > $season_dates_b['start_date'] ) {
			return 1;
		} else {
			return -1;
		}
	}
}