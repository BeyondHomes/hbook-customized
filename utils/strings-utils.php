<?php
class HbStringsUtils {

	private $hbdb;

	public function __construct( $hbdb ) {
		$this->hbdb = $hbdb;
	}

	public function get_search_form_txt() {
		return array(
			'default_form_title' => esc_html__( 'Default form title', 'hbook-admin' ),
			'accom_page_form_title' => esc_html__( 'Form title on accommodation page', 'hbook-admin' ),
			'check_in' => esc_html__( 'Check-in date', 'hbook-admin' ),
			'check_out' => esc_html__( 'Check-out date', 'hbook-admin' ),
			'adults' => esc_html__( 'Adults number', 'hbook-admin' ),
			'children' => esc_html__( 'Children number', 'hbook-admin' ),
			'accom_number' => esc_html__( 'Number of accommodation', 'hbook-admin' ),
			'accom_number_any' => esc_html__( 'Any (in number of accommodation)', 'hbook-admin' ),
			'chosen_check_in' => esc_html__( 'Chosen check-in date', 'hbook-admin' ),
			'chosen_check_out' => esc_html__( 'Chosen check-out date', 'hbook-admin' ),
			'chosen_accom_number' => esc_html__( 'Chosen number of accommodation', 'hbook-admin' ),
			'one_adult_chosen_in_accom_number' => esc_html__( '1 adult chosen (in number of accommodation)', 'hbook-admin' ),
			'chosen_adults_in_accom_number' => esc_html__( 'Chosen adults number (in number of accommodation)', 'hbook-admin' ),
			'chosen_persons_in_accom_number' => esc_html__( 'Chosen persons number (in number of accommodation)', 'hbook-admin' ),
			'chosen_adults' => esc_html__( 'Chosen adults number', 'hbook-admin' ),
			'chosen_children' => esc_html__( 'Chosen children number', 'hbook-admin' ),
			'search_button' => esc_html__( 'Search button', 'hbook-admin' ),
			'change_search_button' => esc_html__( 'Change search button', 'hbook-admin' ),
		);
	}

	public function get_accom_selection_txt() {
		return array(
			'one_type_of_accommodation_found' => esc_html__( 'One type of accommodation found', 'hbook-admin' ),
			'several_types_of_accommodation_found' => esc_html__( 'Several types of accommodation found', 'hbook-admin' ),
			'multi_accom_intro' => esc_html__( 'Multiple accommodation introduction text', 'hbook-admin' ),
			'accom_suggestion_for_single_accom_search' => esc_html__( 'Accommodation suggestion (single accommodation search)', 'hbook-admin' ),
			'accom_suggestion_for_multiple_accom_search' => esc_html__( 'Accommodation suggestion (multiple accommodation search)', 'hbook-admin' ),
			'search_specific_accom_number_link' => esc_html__( 'Link to search for a specific number of accommodation', 'hbook-admin' ),
			'select_accom_title' => esc_html__( 'Accommodation selection title', 'hbook-admin' ),
			'multi_accom_select_accom_n' => esc_html__( 'Accommodation selection title (multiple accommodation booking)', 'hbook-admin' ),
			'multi_accom_accom_n' => esc_html__( 'Accommodation title (multiple accommodation booking)', 'hbook-admin' ),
			'multi_accom_no_accom_selected' => esc_html__( 'Accommodation not selected (multiple accommodation booking)', 'hbook-admin' ),
			'accom_available_at_chosen_dates' => esc_html__( 'The accommodation is available at the chosen dates', 'hbook-admin' ),
			'price_for_1_night' => esc_html__( 'Price for 1 night', 'hbook-admin' ),
			'price_for_several_nights' => esc_html__( 'Price for several nights', 'hbook-admin' ),
			'view_price_breakdown' => esc_html__( 'View price breakdown link', 'hbook-admin' ),
			'hide_price_breakdown' => esc_html__( 'Hide price breakdown link', 'hbook-admin' ),
			'price_breakdown_nights_several' => esc_html__( 'Nights (several - in price breakdown)', 'hbook-admin' ),
			'price_breakdown_night_one' => esc_html__( 'Night (one - in price breakdown)', 'hbook-admin' ),
			'price_breakdown_multiple_nights' => esc_html__( 'Multiple nights (in price breakdown)', 'hbook-admin' ),
			'price_breakdown_accom_price' => esc_html__( 'Accommodation price (in price breakdown)', 'hbook-admin' ),
			'price_breakdown_extra_adults_several' => esc_html__( 'Extra adults (several - in price breakdown)', 'hbook-admin' ),
			'price_breakdown_extra_adult_one' => esc_html__( 'Extra adult (one - in price breakdown)', 'hbook-admin' ),
			'price_breakdown_adults_several' => esc_html__( 'Adults (several - in price breakdown)', 'hbook-admin' ),
			'price_breakdown_adult_one' => esc_html__( 'Adult (one - in price breakdown)', 'hbook-admin' ),
			'price_breakdown_extra_children_several' => esc_html__( 'Extra children (several - in price breakdown)', 'hbook-admin' ),
			'price_breakdown_extra_child_one' => esc_html__( 'Extra child (one - in price breakdown)', 'hbook-admin' ),
			'price_breakdown_children_several' => esc_html__( 'Children (several - in price breakdown)', 'hbook-admin' ),
			'price_breakdown_child_one' => esc_html__( 'Child (one - in price breakdown)', 'hbook-admin' ),
			'price_breakdown_dates' => esc_html__( 'Dates (in price breakdown)', 'hbook-admin' ),
			'price_breakdown_discount' => esc_html__( 'Discount (in price breakdown)', 'hbook-admin' ),
			'fee_details_adults_several' => esc_html__( 'Adults (several - in fee details)', 'hbook-admin' ),
			'fee_details_adult_one' => esc_html__( 'Adult (one - in fee details)', 'hbook-admin' ),
			'fee_details_children_several' => esc_html__( 'Children (several - in fee details)', 'hbook-admin' ),
			'fee_details_child_one' => esc_html__( 'Child (one - in fee details)', 'hbook-admin' ),
			'fee_details_persons' => esc_html__( 'Persons (in fee details)', 'hbook-admin' ),
			'select_accom_button' => esc_html__( 'Select accommodation button', 'hbook-admin' ),
			'previous_step_button' => esc_html__( 'Previous step button', 'hbook-admin' ),
			'next_step_button' => esc_html__( 'Next step button', 'hbook-admin' ),
			'view_accom_button' => esc_html__( 'View accommodation button', 'hbook-admin' ),
			'selected_accom' => esc_html__( 'Selected accommodation', 'hbook-admin' ),
			'accom_left' => esc_html__( 'Number of accommodation left', 'hbook-admin' ),
			'one_accom_left' => esc_html__( 'One accommodation left', 'hbook-admin' ),
			'no_accom_left' => esc_html__( 'No accommodation left', 'hbook-admin' ),
			'nb_accom_selected' => esc_html__( 'Number of accommodation selected', 'hbook-admin' ),
			'price_breakdown_fees' => esc_html__( 'Fees (in price breakdown)', 'hbook-admin' ),
			'select_accom_num_title' => esc_html__( 'Accommodation number selection title', 'hbook-admin' ),
			'select_accom_num_text' => esc_html__( 'Accommodation number selection explanation text', 'hbook-admin' ),
			'select_accom_num_select_title' => esc_html__( 'Accommodation number select field title', 'hbook-admin' ),
			'select_accom_num_label' => esc_html__( 'Accommodation number', 'hbook-admin' ),
		);
	}

	public function get_options_selection_txt() {
		return array(
			'select_options_title' => esc_html__( 'Extra services selection title', 'hbook-admin' ),
			'global_options_title' => esc_html__( 'Global extra services title', 'hbook-admin' ),
			'chosen_options' => esc_html__( 'Chosen extra services title', 'hbook-admin' ),
			'accom_has_no_extras' => esc_html__( 'Accommodation has no extras', 'hbook-admin' ),
			'price_option' => esc_html__( 'Extra price and maximum', 'hbook-admin' ),
			'free_option' => esc_html__( 'Free extra', 'hbook-admin' ),
			'each_option' => esc_html__( 'Each (in extra price and maximum)', 'hbook-admin' ),
			'max_option' => esc_html__( 'Maximum (in extra price and maximum)', 'hbook-admin' ),
			'total_options_price' => esc_html__( 'Total extra services price', 'hbook-admin' ),
		);
	}

	public function get_coupons_txt() {
		return array(
			'coupons_section_title' => esc_html__( 'Title', 'hbook-admin' ),
			'coupons_text' => esc_html__( 'Message', 'hbook-admin' ),
			'coupons_button' => esc_html__( 'Apply coupon button', 'hbook-admin' ),
			'valid_coupon' => esc_html__( 'Valid coupon message', 'hbook-admin' ),
			'invalid_coupon' => esc_html__( 'Invalid coupon message', 'hbook-admin' ),
			'coupon_no_longer_valid' => esc_html__( 'Coupon no longer valid message', 'hbook-admin' ),
			'no_coupon' => esc_html__( 'No coupon message', 'hbook-admin' ),
		);
	}

	public function get_summary_txt() {
		return array(
			'summary_loading' => esc_html__( 'Loading', 'hbook-admin' ),
			'summary_title' => esc_html__( 'Title', 'hbook-admin' ),
			'number_of_nights' => esc_html__( 'Number of nights', 'hbook-admin' ),
			'summary_accommodation' => esc_html__( 'Accommodation', 'hbook-admin' ),
			'summary_accom_price' => esc_html__( 'Accommodation price', 'hbook-admin' ),
			'summary_options_price' => esc_html__( 'Options price', 'hbook-admin' ),
			'summary_global_options_price' => esc_html__( 'Global options price', 'hbook-admin' ),
			'summary_included_fees' => esc_html__( 'Included fees', 'hbook-admin' ),
			'summary_coupon_amount' => esc_html__( 'Coupon amount', 'hbook-admin' ),
			'summary_discount_amount' => esc_html__( 'Discount amount', 'hbook-admin' ),
			'summary_accom_total_price' => esc_html__( 'Accommodation total price', 'hbook-admin' ),
			'summary_price' => esc_html__( 'Total price', 'hbook-admin' ),
			'summary_deposit' => esc_html__( 'Deposit', 'hbook-admin' ),
			'summary_security_bond' => esc_html__( 'Security bond', 'hbook-admin' ),
			'summary_security_bond_explanation' => esc_html__( 'Security bond explanation', 'hbook-admin' ),
			'summary_bottom_text' => esc_html__( 'Text displayed at the bottom of the summary', 'hbook-admin' ),
			'thanks_message_1' => esc_html__( 'Thanks message (unpaid) - above summary', 'hbook-admin' ),
			'thanks_message_2' => esc_html__( 'Thanks message (unpaid)- below summary', 'hbook-admin' ),
			'thanks_message_payment_done_1' => esc_html__( 'Thanks message (payment made) - above summary', 'hbook-admin' ),
			'thanks_message_payment_done_2' => esc_html__( 'Thanks message (payment made) - below summary', 'hbook-admin' ),
		);
	}

	public function get_payment_type_choice() {
		return array(
			'payment_section_title' => esc_html__( 'Payment section title', 'hbook-admin' ),
			'payment_type' => esc_html__( 'Select payment type', 'hbook-admin' ),
			'payment_type_offline' => esc_html__( 'Payment type offline', 'hbook-admin' ),
			'payment_type_store_credit_card' => esc_html__( 'Payment type store credit card', 'hbook-admin' ),
			'payment_type_deposit' => esc_html__( 'Payment type deposit', 'hbook-admin' ),
			'payment_type_full' => esc_html__( 'Payment type full', 'hbook-admin' ),
			'payment_type_explanation_offline' => esc_html__( 'Explanation text for offline payment', 'hbook-admin' ),
			'payment_type_explanation_store_credit_card' => esc_html__( 'Explanation text for stored credit card', 'hbook-admin' ),
			'payment_type_explanation_deposit' => esc_html__( 'Explanation text for deposit payment', 'hbook-admin' ),
			'payment_type_explanation_full' => esc_html__( 'Explanation text for full payment', 'hbook-admin' ),
			'payment_method' => esc_html__( 'Select payment method', 'hbook-admin' ),
		);
	}

	public function get_stripe_txt() {
		return array(
			'stripe_payment_method_label' => esc_html__( 'Payment method label', 'hbook-admin' ),
			'stripe_text_before_form' => esc_html__( 'Text before form', 'hbook-admin' ),
			'stripe_processing_error' => esc_html__( 'Processing error', 'hbook-admin' ),
			'stripe_text_bottom_form' => esc_html__( 'Text at the bottom of the form', 'hbook-admin' ),
		);
	}

	public function get_paypal_txt() {
		return array(
			'paypal_payment_method_label' => esc_html__( 'Payment method label', 'hbook-admin' ),
			'paypal_text_before_form' => esc_html__( 'Explanation text', 'hbook-admin' ),
			'paypal_bottom_text_line_1' => esc_html__( 'Bottom text line 1', 'hbook-admin' ),
			'paypal_bottom_text_line_2' => esc_html__( 'Bottom text line 2', 'hbook-admin' ),
		);
	}

	public function get_external_payment_desc_txt() {
		return array(
			'external_payment_txt_desc' => esc_html__( 'Description', 'hbook-admin' ),
			'external_payment_txt_deposit' => esc_html__( '%deposit_txt', 'hbook-admin' ),
			'external_payment_txt_one_night' => esc_html__( '%nights_txt (one)', 'hbook-admin' ),
			'external_payment_txt_several_nights' => esc_html__( '%nights_txt (several)', 'hbook-admin' ),
			'external_payment_txt_one_adult' => esc_html__('%adults_txt (one)', 'hbook-admin' ),
			'external_payment_txt_several_adults' => esc_html__( '%adults_txt (several)', 'hbook-admin' ),
			'external_payment_txt_one_child' => esc_html__( '%children_txt (one)', 'hbook-admin' ),
			'external_payment_txt_several_children' => esc_html__( '%children_txt (several)', 'hbook-admin' ),
		);
	}

	public function get_search_form_msg() {
		return array(
			'searching' => esc_html__( 'Searching', 'hbook-admin' ),
			'no_check_in_date' => esc_html__( 'No check-in date', 'hbook-admin' ),
			'no_check_out_date' => esc_html__( 'No check-out date', 'hbook-admin' ),
			'no_check_in_out_date' => esc_html__( 'No check-in date and no check-out date', 'hbook-admin' ),
			'no_adults' => esc_html__( 'No adults number', 'hbook-admin' ),
			'no_children' => esc_html__( 'No children number', 'hbook-admin' ),
			'no_adults_children' => esc_html__( 'No adults and children number', 'hbook-admin' ),
			'invalid_check_in_date' => esc_html__( 'Invalid check-in date', 'hbook-admin' ),
			'invalid_check_out_date' => esc_html__( 'Invalid check-out date', 'hbook-admin' ),
			'invalid_check_in_out_date' => esc_html__( 'Invalid check-in date and invalid check-out date', 'hbook-admin' ),
			'check_in_date_past' => esc_html__( 'Check-in date in the past', 'hbook-admin' ),
			'check_in_date_before_date' => esc_html__( 'Check-in date before specific date', 'hbook-admin' ),
			'check_out_date_after_date' => esc_html__( 'Check-out date after specific date', 'hbook-admin' ),
			'check_out_before_check_in' => esc_html__( 'Check-out date before check-in date', 'hbook-admin' ),
			'check_in_day_not_allowed' => esc_html__( 'Check-in date on a not allowed day', 'hbook-admin' ),
			'check_in_day_not_allowed_seasonal' => esc_html__( 'Check-in date on a not allowed day (seasonal)', 'hbook-admin' ),
			'check_out_day_not_allowed' => esc_html__( 'Check-out date on a not allowed day', 'hbook-admin' ),
			'check_out_day_not_allowed_seasonal' => esc_html__( 'Check-out date on a not allowed day (seasonal)', 'hbook-admin' ),
			'minimum_stay' => esc_html__( 'Minimum stay policy', 'hbook-admin' ),
			'minimum_stay_seasonal' => esc_html__( 'Minimum stay policy (seasonal)', 'hbook-admin' ),
			'maximum_stay' => esc_html__( 'Maximum stay policy', 'hbook-admin' ),
			'maximum_stay_seasonal' => esc_html__( 'Maximum stay policy (seasonal)', 'hbook-admin' ),
			'check_out_day_not_allowed_for_check_in_day' => esc_html__( 'Check-out date on a not allowed day for specific check-in day (conditional rule)', 'hbook-admin' ),
			'check_out_day_not_allowed_for_check_in_day_seasonal' => esc_html__( 'Check-out date on a not allowed day for specific check-in day (conditional rule - seasonal)', 'hbook-admin' ),
			'minimum_stay_for_check_in_day' => esc_html__( 'Minimum stay for specific check-in day (conditional rule)', 'hbook-admin' ),
			'minimum_stay_for_check_in_day_seasonal' => esc_html__( 'Minimum stay for specific check-in day (conditional rule - seasonal)', 'hbook-admin' ),
			'maximum_stay_for_check_in_day' => esc_html__( 'Maximum stay for specific check-in day (conditional rule)', 'hbook-admin' ),
			'maximum_stay_for_check_in_day_seasonal' => esc_html__( 'Maximum stay for specific check-in day (conditional rule - seasonal)', 'hbook-admin' ),
			'accom_can_not_suit_nb_people' => esc_html__( 'The accommodation can not suit the number of people', 'hbook-admin' ),
			'no_accom_can_suit_nb_people' => esc_html__( 'No accommodation can suit the number of people', 'hbook-admin' ),
			'view_accom_for_persons' => esc_html__( 'Link to all accommodation which suit the number of people', 'hbook-admin' ),
			'accom_can_not_suit_one_person' => esc_html__( 'The accommodation can not suit one person', 'hbook-admin' ),
			'no_accom_can_suit_one_person' => esc_html__( 'No accommodation can suit one person', 'hbook-admin' ),
			'view_accom_for_one_person' => esc_html__( 'Link to all accommodation which suit one person', 'hbook-admin' ),
			'no_accom_at_chosen_dates' => esc_html__( 'No accommodation available at the chosen dates', 'hbook-admin' ),
			'accom_not_available_at_chosen_dates' => esc_html__( 'The accommodation is not available at the chosen dates', 'hbook-admin' ),
			'only_x_accom' => esc_html__( 'Not enough accommodation', 'hbook-admin' ),
			'only_x_accom_available_at_chosen_dates' => esc_html__( 'Not enough accommodation available at the chosen dates', 'hbook-admin' ),
			'not_enough_accom_for_people' => esc_html__( 'Not enough accommodation for number of people', 'hbook-admin' ),
			'not_enough_accom_for_people_at_chosen_dates' => esc_html__( 'Not enough accommodation available for number of people at the chosen dates', 'hbook-admin' ),
			'accom_no_multiple_accom_booking' => esc_html__( 'Accommodation is excluded from multiple accommodation booking', 'hbook-admin' ),
			'view_accom_at_chosen_date' => esc_html__( 'Link to all accommodation available at the chosen dates', 'hbook-admin' ),
		);
	}

	public function get_book_now_area_txt() {
		return array(
			'terms_and_cond_title' => esc_html__( 'Policies title', 'hbook-admin' ),
			'terms_and_cond_text' => esc_html__( 'Terms and conditions text', 'hbook-admin' ),
			'terms_and_cond_error' => esc_html__( 'Terms and conditions error', 'hbook-admin' ),
			'privacy_policy_text' => esc_html__( 'Privacy policy text', 'hbook-admin' ),
			'privacy_policy_error' => esc_html__( 'Privacy policy error', 'hbook-admin' ),
			'txt_before_book_now_button' => esc_html__( 'Text before "Book now" button', 'hbook-admin' ),
			'book_now_button' => esc_html__( '"Book now" button', 'hbook-admin' ),
		);
	}

	public function get_details_form_msg() {
		return array(
			'accom_no_longer_available' => esc_html__( 'Selected accommodation no longer available', 'hbook-admin' ),
			'accom_num_no_longer_available' => esc_html__( 'Selected accommodation number no longer available', 'hbook-admin' ),
			'processing' => esc_html__( 'Processing', 'hbook-admin' ),
		);
	}

	public function get_error_form_msg() {
		return array(
			'required_field' => esc_html__( 'Required field', 'hbook-admin' ),
			'invalid_email' => esc_html__( 'Invalid email', 'hbook-admin' ),
			'invalid_number' => esc_html__( 'Invalid number', 'hbook-admin' ),
			'connection_error' => esc_html__( 'Connection error', 'hbook-admin' ),
			'timeout_error' => esc_html__( 'Timeout error', 'hbook-admin' ),
			'error_season_not_defined' => esc_html__( 'Season not defined error', 'hbook-admin' ),
			'error_rate_not_defined' => esc_html__( 'Rate not defined error', 'hbook-admin' ),
		);
	}

	public function get_cal_legend_txt() {
		return array(
			'legend_occupied' => esc_html__( 'Occupied', 'hbook-admin' ),

			'legend_past' => esc_html__( 'Past', 'hbook-admin' ),
			'legend_closed' => esc_html__( 'Closed', 'hbook-admin' ),
			'legend_available' => esc_html__( 'Available', 'hbook-admin' ),
			'legend_before_check_in' => esc_html__( 'Before check-in day', 'hbook-admin' ),
			'legend_no_check_in' => esc_html__( 'Not available for check-in', 'hbook-admin' ),
			'legend_no_check_out' => esc_html__( 'Not available for check-out', 'hbook-admin' ),
			'legend_check_in_only' => esc_html__( 'Available for check-in only', 'hbook-admin' ),
			'legend_check_out_only' => esc_html__( 'Available for check-out only', 'hbook-admin' ),
			'legend_no_check_out_min_stay' => esc_html__( 'Not available for check-out (due to minimum-stay requirement)', 'hbook-admin' ),
			'legend_no_check_out_max_stay' => esc_html__( 'Not available for check-out (due to maximum-stay requirement)', 'hbook-admin' ),
			'legend_check_in' => esc_html__( 'Chosen check-in day', 'hbook-admin' ),
			'legend_check_out' => esc_html__( 'Chosen check-out day', 'hbook-admin' ),
			'legend_select_check_in' => esc_html__( 'Select a check-in date', 'hbook-admin'),
			'legend_select_check_out' => esc_html__( 'Select a check-out date', 'hbook-admin'),
		);
	}

	public function get_rates_table_txt() {
		return array(
			'table_rates_season' => esc_html__( 'Season', 'hbook-admin' ),
			'table_rates_from' => esc_html__( 'From', 'hbook-admin' ),
			'table_rates_to' => esc_html__( 'To', 'hbook-admin' ),
			'table_rates_nights' => esc_html__( 'Nights', 'hbook-admin' ),
			'table_rates_price' => esc_html__( 'Price', 'hbook-admin' ),
			'table_rates_per_night' => esc_html__( 'Per night', 'hbook-admin' ),
			'table_rates_all_nights' => esc_html__( 'All nights', 'hbook-admin' ),
			'table_rates_for_night_stay' => esc_html__( 'For x-night stay', 'hbook-admin' ),
		);
	}

	public function get_invoice_table_txt() {
		return array(
			'table_invoice_head_description' => esc_html__( 'Description table head', 'hbook-admin' ),
			'table_invoice_head_amount' => esc_html__( 'Amount table head', 'hbook-admin' ),
			'table_invoice_accom_title' => esc_html__( 'Accommodation section title' ),
			'table_invoice_discount' => esc_html__( 'Discount' ),
			'table_invoice_surcharge' => esc_html__( 'Surcharge' ),
			'table_invoice_accom_subtotal' => esc_html__( 'Accommodation subtotal' ),
			'table_invoice_accom_total' => esc_html__( 'Accommodation total' ),
			'table_invoice_accom_extras_total' => esc_html__( 'Accommodation and extras total' ),
			'table_invoice_global_extras_title' => esc_html__( 'Global extra services section title' ),
			'table_invoice_extras_title' => esc_html__( 'Extra services section title' ),
			'table_invoice_extras_subtotal' => esc_html__( 'Extra services subtotal' ),
			'table_invoice_extras_total' => esc_html__( 'Extra services total' ),
			'table_invoice_global_extras_total' => esc_html__( 'Global extra services total' ),
			'table_invoice_coupon' => esc_html__( 'Coupon' ),
			'table_invoice_subtotal' => esc_html__( 'Subtotal' ),
			'table_invoice_total' => esc_html__( 'Total' ),
			'table_invoice_included_fee' => esc_html__( 'Included fee text' ),
		);
	}

	public function get_accom_list_txt() {
		return array(
			'accom_starting_price' => esc_html__( 'Price starting at', 'hbook-admin' ),
			'accom_starting_price_duration_unit' => esc_html__( 'Starting price duration unit', 'hbook-admin' ),
			'accom_book_now_button' => esc_html__( 'Book now button', 'hbook-admin' ),
			'view_accom_details_button' => esc_html__( 'View details button', 'hbook-admin' ),
		);
	}

	public function get_txt_variables() {
		return array(
			'accom_available_at_chosen_dates' => array( '%accom_name' ),
			'accom_can_not_suit_nb_people' => array( '%accom_name', '%persons_nb' ),
			'accom_can_not_suit_one_person' => array( '%accom_name' ),
			'accom_not_available_at_chosen_dates' => array( '%accom_name' ),
			'only_x_accom' => array( '%accom_name', '%available_accom_nb' ),
			'only_x_accom_available_at_chosen_dates' => array( '%accom_name', '%available_accom_nb' ),
			'accom_no_multiple_accom_booking' => array( '%accom_name' ),
			'not_enough_accom_for_people' => array( '%accom_name', '%persons_nb' ),
			'not_enough_accom_for_people_at_chosen_dates' => array( '%accom_name', '%persons_nb' ),
			'accom_page_form_title' => array( '%accom_name' ),
			'accom_starting_price' => array( '%price' ),
			'check_in_date_before_date' => array( '%date' ),
			'check_out_date_after_date' => array( '%date' ),
			'no_accom_can_suit_nb_people' => array( '%persons_nb' ),
			'external_payment_txt_desc' => array( '%accom_name', '%deposit_txt', '%nights_txt', '%check_in_date', '%check_out_date', '%adults_txt', '%children_txt' ),
			'external_payment_txt_several_adults' => array( '%nb_adults' ),
			'external_payment_txt_several_children' => array( '%nb_children' ),
			'external_payment_txt_several_nights' => array( '%nb_nights'),
			'price_breakdown_adults_several' => array( '%nb_adults' ),
			'price_breakdown_children_several' => array( '%nb_children' ),
			'price_breakdown_dates' => array( '%from_date', '%to_date' ),
			'price_breakdown_extra_adults_several' => array( '%nb_adults' ),
			'price_breakdown_extra_children_several' => array( '%nb_children' ),
			'price_breakdown_multiple_nights' => array( '%nb_nights' ),
			'price_for_several_nights' => array( '%nb_nights' ),
			'selected_accom' => array( '%accom_name' ),
			'accom_left' => array( '%accom_name', '%available_accom_nb' ),
			'one_accom_left' => array( '%accom_name' ),
			'no_accom_left' => array( '%accom_name' ),
			'nb_accom_selected' => array( '%accom_name', '%selected_accom_nb' ),
			'select_accom_num_label' => array( '%accom_name', '%accom_num' ),
			'accom_num_no_longer_available' => array( '%accom_name', '%accom_num' ),
			'several_types_of_accommodation_found' => array( '%nb_types' ),
			'thanks_message_1' => array( '%customer_email' ),
			'thanks_message_payment_done_1' => array( '%customer_email' ),
			'view_accom_for_persons' => array( '%persons_nb' ),
			'check_in_day_not_allowed' => array( '%check_in_days' ),
			'check_out_day_not_allowed' => array( '%check_out_days' ),
			'minimum_stay' => array( '%nb_nights' ),
			'maximum_stay' => array( '%nb_nights' ),
			'check_out_day_not_allowed_for_check_in_day' => array( '%check_in_day', '%check_out_days' ),
			'minimum_stay_for_check_in_day' => array( '%nb_nights', '%check_in_day' ),
			'maximum_stay_for_check_in_day' => array( '%nb_nights', '%check_in_day' ),
			'table_rates_for_night_stay' => array( '%nb_nights' ),
			'price_option' => array( '%price', '%each', '%max' ),
			'free_option' => array( '%max' ),
			'max_option' => array( '%max_value' ),
			'legend_no_check_in_min_stay' => array( '%nb_nights' ),
			'legend_no_check_out_min_stay' => array( '%nb_nights' ),
			'legend_no_check_out_max_stay' => array( '%nb_nights' ),
			'error_season_not_defined' => array( '%night' ),
			'error_rate_not_defined' => array( '%accom_name', '%season_name' ),
			'stripe_processing_error' => array( '%error_msg' ),
			'valid_coupon' => array( '%amount' ),
			'payment_type_explanation_offline' => array( '%full_amount', '%deposit_amount', '%full_minus_deposit_amount' ),
			'payment_type_explanation_store_credit_card' => array( '%full_amount', '%deposit_amount', '%full_minus_deposit_amount' ),
			'payment_type_explanation_deposit' => array( '%full_amount', '%deposit_amount', '%full_minus_deposit_amount' ),
			'payment_type_explanation_full' => array( '%full_amount' ),
			'chosen_adults_in_accom_number' => array( '%nb_adults' ),
			'chosen_persons_in_accom_number' => array( '%nb_persons' ),
			'multi_accom_select_accom_n' => array( '%n' ),
			'multi_accom_accom_n' => array( '%n' ),
			'previous_step_button' => array( '%arrow' ),
			'next_step_button' => array( '%arrow' ),
			'table_invoice_accom_extras_total' => array( '%accom_name' ),
			'select_accom_num_select_title' => array( '%accom_name' ),
		);
	}

	public function get_string_list() {
		return array_merge(
			$this->get_search_form_txt(),
			$this->get_search_form_msg(),
			$this->get_accom_selection_txt(),
			$this->get_options_selection_txt(),
			$this->hbdb->get_fee_names(),
			$this->hbdb->get_option_names(),
			$this->hbdb->get_details_form_labels(),
			$this->get_book_now_area_txt(),
			$this->get_details_form_msg(),
			$this->get_coupons_txt(),
			$this->get_summary_txt(),
			$this->get_payment_type_choice(),
			$this->get_paypal_txt(),
			$this->get_external_payment_desc_txt(),
			$this->get_stripe_txt(),
			$this->get_error_form_msg(),
			$this->get_cal_legend_txt(),
			$this->get_rates_table_txt(),
			$this->get_invoice_table_txt(),
			$this->hbdb->get_season_names(),
			$this->get_accom_list_txt()
		);
	}
}