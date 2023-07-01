<?php
class HbOptionsForm {

	private $hbdb;
	private $utils;

	public function __construct( $hbdb, $utils ) {
		$this->hbdb = $hbdb;
		$this->utils = $utils;
	}

	public function get_options_form_markup_frontend( $accom_choices, $nb_nights ) {
		$chosen_options = array();
		/*
		if ( isset( $_POST['chosen_options'] ) ) {
			$chosen_options = json_decode( stripcslashes( $_POST['chosen_options'] ), true );
		}
		*/
		return $this->get_options_form_markup( $accom_choices, $nb_nights, false, $chosen_options );
	}

	public function get_options_form_markup_backend( $accom_choices, $nb_nights ) {
		return $this->get_options_form_markup( $accom_choices, $nb_nights, true, array() );
	}

	public function get_update_options_form_markup_backend( $resa_info, $nb_nights, $chosen_options ) {
		return $this->get_options_form_markup( $resa_info, $nb_nights, true, $chosen_options, true );
	}

	private function get_options_form_markup( $accom_choices, $nb_nights, $is_admin, $chosen_options, $is_update = false ) {
		if ( get_option( 'hb_charge_per_day' ) == 'yes' ) {
			$nb_nights++;
		}
		$output = '<form class="hb-options-form">';

		if ( $is_admin ) {
			$output .= '<p class="hb-admin-add-resa-section-title">';
			$output .= esc_html__( 'Extra services:', 'hbook-admin' );
			$output .= '</p>';
		} else {
			$output .= '<h3 class="hb-title hb-title-extra">';
			$output .= wp_kses_post( $this->hbdb->get_string( 'select_options_title' ) );
			$output .= '</h3>';
		}

		$output_head = $output;
		$options = $this->hbdb->get_all_options_with_choices();
		$nb_accom = count( $accom_choices );
		$is_update_child_resa = false;
		if ( $is_update ) {
			if ( $accom_choices[0]['resa_parental_status'] == 'child' ) {
				$is_update_child_resa = true;
			} else if ( $accom_choices[0]['resa_parental_status'] == 'parent' ) {
				$nb_accom = $accom_choices[0]['nb_accom'];
			}
		}
		if ( count( $accom_choices ) > 1 ) {
			if ( $is_admin ) {
				$multi_accom_title = esc_html__( 'Accommodation %n', 'hbook-admin' );
			} else {
				$multi_accom_title = $this->hbdb->get_string( 'multi_accom_accom_n' );
			}
			if ( in_array( 'booking', array_column( $options, 'link' ) ) ) {
				$accom_choices[] = array(
					'link' => 'booking',
					'adults' => array_sum( array_column( $accom_choices, 'adults' ) ),
					'children' => array_sum( array_column( $accom_choices, 'children' ) ),
				);
			}
		}
		if ( $is_admin ) {
			$accom_has_no_extras_text = esc_html__( 'This accommodation does not have any extra services.', 'hbook-admin' );
		} else {
			$accom_has_no_extras_text = $this->hbdb->get_string( 'accom_has_no_extras' );
		}
		$nb_accom_choices_without_options = 0;

		foreach ( $accom_choices as $accom_no => $accoms ) {
			if ( ( count( $accom_choices ) > 1 ) && isset( $accoms['link'] ) ) {
				if ( $nb_accom_choices_without_options == count( $accom_choices ) - 1 ) {
					$output = $output_head;
					$output .= '<div class="hb-options-multi-accom-global">';
				} else {
					$output .= '<div class="hb-options-multi-accom-global">';
					$output .= '<h4>';
					if ( $is_admin ) {
						$output .= esc_html__( 'Global options', 'hbook-admin' );
					} else {
						$output .= wp_kses_post( $this->hbdb->get_string( 'global_options_title' ) );
					}
					$output .= '</h4>';
				}
			} else {
				$output .= '<div class="' . esc_attr( 'hb-options-multi-accoms hb-options-multi-accom-' . ( $accom_no + 1 ) ) . '">';
				if ( count( $accom_choices ) > 1 ) {
					$output .= '<h4>' . esc_html( str_replace( '%n', $accom_no + 1, $multi_accom_title ) ) . '</h4>';
					$output .= '<p class="hb-option hb-no-options">' . esc_html( $accom_has_no_extras_text ) . '</p>';
				}
			}
			$output_options_quantity = '';
			$output_options_single = '';
			$output_options_multiple = '';
			$price_options = $this->utils->calculate_options_price( $accoms['adults'], $accoms['children'], $nb_nights, $nb_accom, $options, true );
			$accom_choices_has_options = false;

			foreach ( $options as $option ) {
				if ( isset( $accoms['link'] ) ) {
					if ( $option['link'] != 'booking' ) {
						continue;
					}
				} else if (
					( $option['link'] == 'booking' ) &&
					( ( count( $accom_choices ) > 1 ) || $is_update_child_resa )
				) {
					continue;
				} else if ( ! array_intersect( $accoms['ids'], explode( ',', $option['accom'] ) ) ) {
					continue;
				}
				$accom_choices_has_options = true;
				$accom = explode( ',', $option['accom'] );
				$option_classes = '';
				if ( ! isset( $accoms['link'] ) ) {
					foreach ( $accom as $accom_id ) {
						$option_classes .= ' hb-option-accom-' . $accom_id;
					}
				} else {
					$option_classes .= ' hb-option-global';
				}
				$option_classes .= ' hb-option';
				if ( ( count( $accom_choices ) > 1 ) && ( $option['link'] == 'booking' ) ) {
					$option_markup_id = 'hb-option-' . $option['id'] . '-multi-accom-global';
				} else {
					$option_markup_id = 'hb-option-' . $option['id'] . '-multi-accom-' . ( $accom_no + 1 );
				}
				if ( $option['apply_to_type'] == 'quantity' || $option['apply_to_type'] == 'quantity-per-day' ) {
					$option_max = -1;
					if ( $option['quantity_max_option'] == 'yes' ) {
						$option_max = $option['quantity_max'];
					} else if ( $option['quantity_max_option'] == 'yes-per-person' ) {
						$option_max = $option['quantity_max'] * $accoms['adults'] + $option['quantity_max_child'] * $accoms['children'];
					}
					if ( isset( $chosen_options[ $option['id'] ] ) ) {
						$option_value = intval( $chosen_options[ $option['id'] ]['quantity'] );
					} else {
						$option_value = 0;
					}
					$output_options_quantity .= '
						<div class="' . esc_attr( 'hb-quantity-option' . $option_classes ) . '">
							<label for="' . esc_attr( $option_markup_id ) . '">' . wp_kses_post( $this->get_option_display_name( $option, $is_admin, $price_options[ 'option_' . $option['id'] ], false, $option_max ) ) . '</label><br/>
							<input
								type="number"
								min="0"
								value="' . esc_attr( $option_value ) . '"
								data-price="' . esc_attr( $price_options[ 'option_' . $option['id'] ] ) . '"
								id="' . esc_attr( $option_markup_id ) . '"
								name="' . esc_attr( $option_markup_id ) . '"';
					if ( $option_max > -1 ) {
						$output_options_quantity .= ' max="' . esc_attr( $option_max ) . '"';
					}
					$output_options_quantity .= '
							/>
							<br/>
						</div>';
				} else if ( $option['choice_type'] == 'single' ) {
					$checked = '';
					if ( isset( $chosen_options[ $option['id'] ] ) ) {
						$checked = 'checked';
					}
					$output_options_single .= '
						<div class="' . esc_attr( 'hb-single-option' . $option_classes ) . '">
							<span class="hb-checkbox-wrapper">
								<input
									type="checkbox"
									data-price="' . esc_attr( $price_options[ 'option_' . $option['id'] ] ) . '"
									id="' . esc_attr( $option_markup_id ) . '"
									name="' . esc_attr( $option_markup_id ) . '" ' . esc_html( $checked ) . '
								/>
								<label for="' . esc_attr( $option_markup_id ) . '">' . wp_kses_post( $this->get_option_display_name( $option, $is_admin, $price_options[ 'option_' . $option['id'] ] ) ) . '</label>
							</span>
						</div>';
				} else {
					$output_options_multiple .= '
						<div class="' . esc_attr( 'hb-multiple-option' . $option_classes ) . '">' . wp_kses_post( $this->get_option_display_name( $option, $is_admin ) ) . '<br/>';
					$choices = $option['choices'];
					foreach ( $choices as $i => $choice ) {
						$option_choice_markup_id = 'hb-option-choice-' . $choice['id'] . '-multi-accom-' . ( $accom_no + 1 );
						$checked = '';
						if ( isset( $chosen_options[ $option['id'] ] ) ) {
							if ( $chosen_options[ $option['id'] ]['chosen'] == $choice['id'] ) {
								$checked = 'checked';
							}
						} else if ( $i == 0 ) {
							$checked = 'checked';
						}
						$output_options_multiple .= '
							<span class="hb-radio-wrapper">
								<input
									type="radio"
									data-price="' . esc_attr( $price_options[ 'option_choice_' . $choice['id'] ] ) . '"
									id="' . esc_attr( $option_choice_markup_id ) . '"
									name="' . esc_attr( $option_markup_id ) . '"
									value="' . esc_attr( $choice['id'] ) . '" ' . esc_html( $checked ) . ' />
								<label for="' . esc_attr( $option_choice_markup_id ) . '">' . esc_html( $this->get_choice_option_display_name( $choice, $is_admin, $price_options[ 'option_choice_' . $choice['id'] ] ) ) . '</label>
							</span>
							<br/>';
					}
					$output_options_multiple .= '</div><br class="hb-options-gap" />';
				}
			}

			$output .= $output_options_single;
			if ( $output_options_single != '' ) {
				$output .= '<br class="hb-options-gap" />';
			}
			if ( $output_options_quantity ) {
				$output .= $output_options_quantity;
				$output .= '<br class="hb-options-gap" />';
			}
			$output .= $output_options_multiple;

			$output .= '</div>';

			if ( ! $accom_choices_has_options ) {
				$nb_accom_choices_without_options++;
			}
		}
		if ( $is_admin || ( get_option( 'hb_display_price' ) != 'no' ) ) {
			$output .= '<p class="hb-options-total-price">';
			if ( $is_admin ) {
				$output .= esc_html__( 'Options total price:', 'hbook-admin' ) . ' ';
			} else {
				$output .= wp_kses_post( $this->hbdb->get_string( 'total_options_price' ) ) . ' ';
			}
			$output .= '<span class="hb-price-placeholder-minus">-</span>';
			$output .= wp_kses_post( $this->utils->price_placeholder() );
			$output .= '</p>';
		}
		$output .= '<input name="hb-has-options-form" type="hidden" value="yes" />';
		$output .= '</form>';
		if ( ! $is_admin ) {
			$output = apply_filters( 'hb_extras_form_markup', $output );
		}

		return $output;
	}

	private function get_choice_option_display_name( $option, $is_admin, $price ) {
		return $this->get_option_display_name( $option, $is_admin, $price, true );
	}

	private function get_option_display_name( $option, $is_admin, $price = '', $is_choice = false, $max = -1 ) {
		$locale = '';
		if ( $is_admin ) {
			$locale = get_user_locale();
		}
		if ( $is_choice ) {
			$option_id = 'option_choice_' . $option['id'];
		} else {
			$option_id = 'option_' . $option['id'];
		}
		$display_name = $this->hbdb->get_string( $option_id, $locale );
		if ( $display_name ) {
			$display_name = str_replace( '%price', '', $display_name ); // Backward compatibility (there was a %price var in each option name)
		} else {
			$display_name = $option['name'];
		}

		if ( ! $is_choice ) {
			$display_name = '<b>' . $display_name . '</b>';
		}
		if ( $price !== '' ) {
			if ( $is_admin || ( get_option( 'hb_display_price' ) != 'no' ) ) {
				if ( $price == 0 ) {
					$display_price = $this->hbdb->get_string( 'free_option', $locale );
				} else {
					$display_price = str_replace( '%price', $this->utils->price_with_symbol( $price ), $this->hbdb->get_string( 'price_option', $locale ) );
					if ( isset( $option['apply_to_type'] ) && ( $option['apply_to_type'] == 'quantity' || $option['apply_to_type'] == 'quantity-per-day' ) ) {
						$display_price = str_replace( '%each', ' ' . trim( $this->hbdb->get_string( 'each_option', $locale ) ), $display_price );
					} else {
						$display_price = str_replace( '%each', '', $display_price );
					}
				}
			} else if ( $max != -1 ) {
				$display_price = str_replace( '%price', '', $this->hbdb->get_string( 'price_option', $locale ) );
				$display_price = str_replace( '%each', '', $display_price );
			} else {
				$display_price = '';
			}
			if ( $max != -1 ) {
				$display_price = str_replace( '%max', $this->hbdb->get_string( 'max_option', $locale ), $display_price );
				$display_price = str_replace( '%max_value', $max, $display_price );
			} else {
				$display_price = str_replace( '%max', '', $display_price );
			}
			$display_name = $display_name . ' ' . $display_price;
		}
		$display_name = apply_filters( 'hb_extra_name', $display_name, $option, $price, $max );
		return $display_name;
	}

}