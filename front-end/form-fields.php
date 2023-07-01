<?php
class HBFormFields {

	private $hb_strings;

	public function __construct( $hb_strings ) {
		$this->hb_strings = $hb_strings;
	}

	public function get_field_mark_up( $field, $form_data = array(), $show_required = true, $display_column = true ) {
		if ( $field['type'] == 'column_break' ) {
			return '';
		}
		$output = '';
		$field_display_name = $this->get_field_display_name( $field );
		if ( $display_column && $field['column_width'] ) {
			$output .= '<div class="hb-column-' . $field['column_width'] . '">';
		}
		if ( ( $field['type'] == 'title' ) || ( $field['type'] == 'sub_title' ) || ( $field['type'] == 'explanation' ) || ( $field['type'] == 'separator' ) ) {
			if ( $field['type'] == 'title' ) {
				$output .= '<h3 class="hb-title">' . $field_display_name . '</h3>';
			} else if ( $field['type'] == 'sub_title' ) {
				$output .= '<h4>' . $field_display_name . '</h4>';
			} else if ( $field['type'] == 'explanation' ) {
				$output .= '<p class="hb-explanation">' . $field_display_name . '</p>';
			} else if ( $field['type'] == 'separator' ) {
				$output .= '<hr/>';
			}
			if ( $display_column && $field['column_width'] ) {
				$output .= '</div><!-- end .hb-column-' . $field['column_width'] . ' -->';
			}
			$output = apply_filters( 'hb_details_form_markup_field', $output, $field );
			return $output;
		}
		$required_text = '';
		if ( $show_required && $field['required'] == 'yes' ) {
			$required_text = '*';
		}
		$output .= '<p>';
		$output .= '<label for="' . $field['id'] . '">' . $field_display_name . $required_text . '</label>';
		$field_attributes = $this->get_field_attributes( $field );
		if ( $field['type'] == 'text' || $field['type'] == 'email' || $field['type'] == 'number' ) {
			$field_value = '';
			if ( isset( $form_data[ $field['id'] ] ) ) {
				$field_value = esc_attr( $form_data[ $field['id'] ] );
			}
			$output .= '<input ' . $field_attributes . ' type="text"  value="' . $field_value . '" />';
		} else if ( $field['type'] == 'textarea' ) {
			$field_value = '';
			if ( isset( $form_data[ $field['id'] ] ) ) {
				$field_value = esc_textarea( $form_data[ $field['id'] ] );
			}
			$output .= '<textarea ' . $field_attributes . '>';
			$output .= $field_value;
			$output .= '</textarea>';
		} else if ( $field['type'] == 'select' || $field['type'] == 'radio' || $field['type'] == 'checkbox' ) {
			$choices_mark_up = '';
			if ( ( $field['type'] == 'radio' ) || ( $field['type'] == 'checkbox' ) ) {
				if ( isset( $form_data[ $field['id'] ] ) && $form_data[ $field['id'] ] != '' ) {
					$checked_choices = array_map( 'trim' , explode( ',', $form_data[ $field['id'] ] ) );
				} else {
					$checked_choices = array();
				}
			}
			foreach ( $field['choices'] as $i => $choice ) {
				$choice_display_name = $this->get_field_display_name( $choice );
				if ( $field['type'] == 'select' ) {
					$choices_mark_up .= '<option value="' . $choice['name'] . '"';
					if ( isset( $form_data[ $field['id'] ] ) && $form_data[ $field['id'] ] == $choice['name'] ) {
						$choices_mark_up .= ' selected';
					}
					$choices_mark_up .= '>' . $choice_display_name . '</option>';
				} else if ( ( $field['type'] == 'radio' ) || ( $field['type'] == 'checkbox' ) ) {
					$choices_mark_up .= '<span class="hb-' . $field['type'] . '-wrapper">';
					$choices_mark_up .= '<input type="' . $field['type'] . '"';
					$field_name = 'hb_' . $field['id'];
					if ( $field['type'] == 'checkbox' ) {
						$field_name .= '[]';
						if ( $field['required'] == 'yes' ) {
							$choices_mark_up .= ' data-validation="checkbox_group" data-validation-qty="min1"';
						}
					}
					if ( in_array( $choice['name'], $checked_choices ) ) {
						$choices_mark_up .= ' checked';
					} else if ( $field['type'] == 'radio' && $i == 0 && count( $checked_choices ) == 0 ) {
						$choices_mark_up .= ' checked';
					}
					$choices_mark_up .= ' id="' . $field['id'] . '-' . $choice['id'] . '" name="' . $field_name . '" value="' . $choice['name'] . '">';
					$choices_mark_up .= '<label for="' . $field['id'] . '-' . $choice['id'] . '" class="hb-label-choice"> ' . $choice_display_name . '</label>';
					$choices_mark_up .= '</span>';
					$choices_mark_up .= '<br/>';
				}
			}
			if ( $field['type'] == 'select' ) {
				$output .= '<select ' . $field_attributes . '>';
				$output .= $choices_mark_up;
				$output .= '</select>';
			}
			if ( $field['type'] == 'radio' || $field['type'] == 'checkbox' ) {
				$output .= $choices_mark_up;
			}
		}
		$output .= '</p>';
		if ( $display_column && $field['column_width'] ) {
			$output .= '</div><!-- end .hb-column-' . $field['column_width'] . ' -->';
		}
		$output = apply_filters( 'hb_details_form_markup_field', $output, $field );
		return $output;
	}

	private function get_field_display_name( $field ) {
		$display_name = '';
		if ( isset( $this->hb_strings[ $field['id'] ] ) ) {
			$display_name = $this->hb_strings[ $field['id'] ];
		}
		if ( $display_name != '' ) {
			return $display_name;
		} else {
			return $field['name'];
		}
	}

	private function get_field_attributes( $field ) {
		$data_validation = '';
		if ( $field['required'] == 'yes' ) {
			$data_validation = 'required';
		}
		if ( $field['type'] == 'email' ) {
			$data_validation .= ' email';
		}
		if ( $field['type'] == 'number' ) {
			$data_validation .= ' number';
		}
		return 'id="' . $field['id'] . '" name="hb_' . $field['id'] . '" class="hb-detail-field" data-validation="' . $data_validation . '"';
	}

}