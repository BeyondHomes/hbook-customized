<?php
class HbOptionsUtils {

	private $utils;
	private $hbdb;

	public function __construct( $hbdb = false, $utils = false ) {
		$this->hbdb = $hbdb;
		$this->utils = $utils;
		if ( $utils ) {
			$this->currencies = $utils->currencies_code_name();
		} else {
			$this->currencies = array();
		}
	}

	public function get_payment_settings() {
		return array(
			'payment_settings' => array(
				'label' => esc_html__( 'Booking payment settings', 'hbook-admin' ),
				'options' => array(
					'hb_resa_payment_multiple_choice' => array(
						'label' => esc_html__( 'Customers can choose between different payment options', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'no'
					),
					'hb_resa_payment' => array(
						'label' => esc_html__( 'Booking payment', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'offline' => esc_html__( 'Customers do not have to pay online to book an accommodation (payment on arrival or offline - e.g. bank wire)', 'hbook-admin' ),
							'store_credit_card' => esc_html__( 'Customers have to leave their credit card details to book an accommodation (this option is available only with Stripe)', 'hbook-admin' ),
							'deposit' => esc_html__( 'Customers have to pay a deposit online to book an accommodation', 'hbook-admin' ),
							'full' => esc_html__( 'Customers have to pay the full stay price online to book an accommodation', 'hbook-admin' )
						),
						'default' => 'offline',
						'wrapper-class' => 'hb-resa-payment-choice-multiple'
					),
					'hb_resa_payment_offline' => array(
						'label' => esc_html__( 'Customers can pay on arrival or offline - e.g.bank wire', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'yes',
						'wrapper-class' => 'hb-resa-payment-choice-single'
					),
					'hb_resa_payment_store_credit_card' => array(
						'label' => esc_html__( 'Customers can leave their credit card details for a later charge (this option is available only with Stripe)', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'no',
						'wrapper-class' => 'hb-resa-payment-choice-single'
					),
					'hb_resa_payment_deposit' => array(
						'label' => esc_html__( 'Customers can pay an online deposit', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'no',
						'wrapper-class' => 'hb-resa-payment-choice-single'
					),
					'hb_resa_payment_full' => array(
						'label' => esc_html__( 'Customers can pay the full amount online', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'no',
						'wrapper-class' => 'hb-resa-payment-choice-single'
					),
				)
			),

			'security_bond_settings' => array(
				'label' => esc_html__( 'Security bond settings', 'hbook-admin' ),
				'desc' => esc_html__( 'A security bond is a sum of money which is held during the length of the stay to cover the cost of any damages or loss caused by the customer.', 'hbook-admin' ),
				'options' => array(
					'hb_security_bond' => array(
						'label' => esc_html__( 'Security bond', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'no',
						'wrapper-class' => 'hb-security-bond-choice',
					),
					'hb_security_bond_online_payment' => array(
						'label' => esc_html__( 'Security bond has to be paid', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'no',
						'wrapper-class' => 'hb-security-bond-options hb-security-bond-payment',
					),
					'hb_security_bond_amount' => array(
						'label' => esc_html__( 'Security bond amount', 'hbook-admin' ),
						'type' => 'text',
						'class' => 'hb-small-field',
						'wrapper-class' => 'hb-security-bond-options',
					),
				)
			),

			'deposit_settings' => array(
				'label' => esc_html__( 'Deposit settings', 'hbook-admin' ),
				'desc' => esc_html__( 'A deposit corresponds to the part of the total price of the stay that is payed in advance by the customer to secure the booking.', 'hbook-admin' ),
				'options' => array(
					'hb_deposit_type' => array(
						'label' => esc_html__( 'Deposit type', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'none' => esc_html__( 'None', 'hbook-admin' ),
							'percentage' => esc_html__( 'Percentage', 'hbook-admin' ),
							'nb_night' => esc_html__( 'Number of nights', 'hbook-admin' ),
							'fixed' => esc_html__( 'Fixed', 'hbook-admin' ),
						),
						'default' => 'none',
						'wrapper-class' => 'hb-deposit-choice',
					),
					'hb_deposit_amount' => array(
						'label' => esc_html__( 'Deposit amount', 'hbook-admin' ),
						'type' => 'text',
						'class' => 'hb-small-field',
						'wrapper-class' => 'hb-deposit-options hb-deposit-amount',
					),
					'hb_deposit_check_in_min_days' => array(
						'label' => esc_html__( 'Deposit option must be replaced by full payment option if check-in is within', 'hbook-admin' ),
						'caption' => esc_html__( 'Enter a number of days from current date.', 'hbook-admin' ),
						'type' => 'text',
						'class' => 'hb-small-field',
						'wrapper-class' => 'hb-deposit-options',
					),
					'hb_deposit_bond' => array(
						'label' => esc_html__( 'Security bond must be paid along with deposit', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'no',
						'wrapper-class' => 'hb-deposit-bond',
					),
				)
			),

			'price_settings' => array(
				'label' => esc_html__( 'Price settings', 'hbook-admin' ),
				'options' => array(
					'hb_currency' => array(
						'label' => esc_html__( 'Payment currency', 'hbook-admin' ),
						'type' => 'select',
						'choice' => $this->currencies,
						'default' => 'USD'
					),
					'hb_currency_position' => array(
						'label' => esc_html__( 'Currency symbol position', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'before' => esc_html__( 'Before price', 'hbook-admin' ),
							'after' => esc_html__( 'After price', 'hbook-admin' ),
						),
						'default' => 'before'
					),
					'hb_price_precision' => array(
						'label' => esc_html__( 'Price precision', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'two_decimals' => esc_html__( 'Two decimals' ,'hbook-admin' ),
							'no_decimals' => esc_html__( 'No decimals' ,'hbook-admin' ),
						),
						'default' => 'two_decimals'
					),
				)
			)
		);
	}

	public function get_appearance_settings() {
		return array(
			'general_appearance_settings' => array(
				'label' => esc_html__( 'General settings', 'hbook-admin' ),
				'options' => array(
					'hb_search_form_max_width' => array(
						'label' => esc_html__( 'Search form maximum width', 'hbook-admin' ),
						'caption' => esc_html__( 'Leave blank for no maximum.', 'hbook-admin' ),
						'type' => 'text',
						'class' => 'hb-small-field',
						'default' => '',
					),
					'hb_accom_selection_form_max_width' => array(
						'label' => esc_html__( 'Accommodation selection form maximum width', 'hbook-admin' ),
						'caption' => esc_html__( 'Leave blank for no maximum.', 'hbook-admin' ),
						'type' => 'text',
						'class' => 'hb-small-field',
						'default' => '800',
					),
					'hb_details_form_max_width' => array(
						'label' => esc_html__( 'Details form maximum width', 'hbook-admin' ),
						'caption' => esc_html__( 'Leave blank for no maximum.', 'hbook-admin' ),
						'type' => 'text',
						'class' => 'hb-small-field',
						'default' => '800',
					),
					'hb_forms_position' => array(
						'label' => esc_html__( 'Forms position', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'left' => esc_html__( 'Left', 'hbook-admin' ),
							'center' => esc_html__( 'Center', 'hbook-admin' ),
						),
						'default' => 'center'
					),
					'hb_horizontal_form_min_width' => array(
						'label' => esc_html__( 'Minimum width required for displaying a horizontal search form', 'hbook-admin' ),
						'caption' => esc_html__( 'If the available space is less than the minimum width the form will be displayed vertically.', 'hbook-admin' ),
						'type' => 'text',
						'class' => 'hb-small-field',
						'default' => '500',
					),
					'hb_details_form_stack_width' => array(
						'label' => esc_html__( 'Minimum width required for displaying columns in the details form', 'hbook-admin' ),
						'caption' => esc_html__( 'If the available space is less than the minimum width the columns will be stacked.', 'hbook-admin' ),
						'type' => 'text',
						'class' => 'hb-small-field',
						'default' => '500',
					),
					'hb_page_padding_top' => array(
						'label' => esc_html__( 'Sticky header/menu height', 'hbook-admin' ),
						'caption' =>
									esc_html__( 'If your website has a sticky header insert its height in pixels below.' ) .
									'<br/>' .
									esc_html__( 'This will prevent HBook forms to appear under the header when automatic scrolling occurs.', 'hbook-admin' ),
						'type' => 'text',
						'class' => 'hb-small-field',
						'default' => '',
					),
				)
			),

			'buttons_appearance' => array(
				'label' => esc_html__( 'Buttons appearance', 'hbook-admin' ),
				'options' => array(
					'hb_buttons_style' => array(
						'label' => esc_html__( 'Buttons style', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'theme' => esc_html__( 'Use theme styles', 'hbook-admin' ),
							'custom' => esc_html__( 'Custom', 'hbook-admin' ),
						),
						'default' => 'theme'
					),
					'hb_buttons_css_options' => array(),
				)
			),

			'inputs_selects_appearance' => array(
				'label' => esc_html__( 'Inputs and selects appearance', 'hbook-admin' ),
				'options' => array(
					'hb_inputs_selects_style' => array(
						'label' => esc_html__( 'Inputs and selects style', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'theme' => esc_html__( 'Use theme styles', 'hbook-admin' ),
							'custom' => esc_html__( 'Custom', 'hbook-admin' ),
						),
						'default' => 'theme'
					),
					'hb_inputs_selects_css_options' => array(),
				)
			),

			'tables_appearance' => array(
				'label' => esc_html__( 'Rates tables appearance', 'hbook-admin' ),
				'options' => array(
					'hb_tables_style' => array(
						'label' => esc_html__( 'Rates tables style', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'theme' => esc_html__( 'Use theme styles', 'hbook-admin' ),
							'plugin' => esc_html__( 'Use plugin styles', 'hbook-admin' ),
						),
						'default' => 'theme'
					),
				)
			),

			'calendar_colors' => array(
				'label' => esc_html__( 'Calendars appearance', 'hbook-admin' ),
				'options' => array(
					'hb_calendar_colors' => array(),
					'hb_calendar_shadows' => array(
						'label' => esc_html__( 'Add a shadow to calendars', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'yes'
					),
				)
			),

			'people_popup_appearance' => array(
				'label' => esc_html__( 'Number of persons pop-up appearance', 'hbook-admin' ),
				'options' => array(
					'hb_people_popup_bg' => array(
						'label' => esc_html__( 'Background color', 'hbook-admin' ),
						'type' => 'text',
						'default' => '#ffffff',
						'class' => 'hb-color-option',
					),
					'hb_people_popup_shadows' => array(
						'label' => esc_html__( 'Add a shadow to pop-up', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'yes'
					),
				)
			),

			'custom_css_appearance_settings' => array(
				'label' => esc_html__( 'Custom CSS', 'hbook-admin' ),
				'options' => array(

					'hb_custom_css_frontend' => array(
						'label' => esc_html__( 'Custom CSS for the front-end pages', 'hbook-admin' ),
						'type' => 'textarea',
					),
					'hb_custom_css_backend' => array(
						'label' => esc_html__( 'Custom CSS for the admin pages', 'hbook-admin' ),
						'type' => 'textarea',
					),

				)
			)
		);
	}

	public function get_misc_settings() {
		$resa_page_filter_choice = array(
			'none' => esc_html__( 'No filter', 'hbook-admin' ),
			'resa_id' => esc_html__( 'Filter by Reservation id', 'hbook-admin' ),
			'resa_alphanum' => esc_html__( 'Filter by Reservation number', 'hbook-admin' ),
			'customer' => esc_html__( 'Filter by Customer', 'hbook-admin' ),
			'check_in_date' => esc_html__( 'Filter by Check-in date', 'hbook-admin' ),
			'check_out_date' => esc_html__( 'Filter by Check-out date', 'hbook-admin' ),
			'check_in_out_date' => esc_html__( 'Filter by Check-in and check-out dates', 'hbook-admin' ),
			'active_resa_date' => esc_html__( 'Filter by Active reservations', 'hbook-admin' ),
			'accom' => esc_html__( 'Filter by Accommodation', 'hbook-admin' ),
			'status' => esc_html__( 'Filter by Status', 'hbook-admin' ),
		);
		if ( get_option( 'hb_resa_alphanum' ) != 'yes' ) {
			unset( $resa_page_filter_choice['resa_alphanum'] );
		}
		$resa_page_default_filter_value_for_from_dates = array(
			'caption' => esc_html__( 'Filter default "From date" (enter a fixed date (yyyy-mm-dd format), a number of days (from current date), or leave empty for Today\'s date).', 'hbook-admin' ),
			'type' => 'text',
			'class' => 'hb-small-field',
			'default' => '',
		);
		$resa_page_default_filter_value_for_to_dates = array(
			'caption' => esc_html__( 'Filter default "To date" (enter a fixed date (yyyy-mm-dd format), a number of days (from current date), or leave empty for Today\'s date).', 'hbook-admin' ),
			'type' => 'text',
			'class' => 'hb-small-field',
			'default' => '',
		);
		$resa_page_filter_accom = array();
		if ( $this->hbdb ) {
			$resa_page_filter_accom = array( 'all' => esc_html__( 'All', 'hbook-admin' ) ) + $this->hbdb->get_all_accom();
		}
		return array(
			'multiple_accom_booking_settings' => array(
				'label' => esc_html__( 'Multiple accommodation booking', 'hbook-admin' ),
				'options' => array(
					'hb_multiple_accom_booking' => array(
						'label' => esc_html__( 'Enable multiple accommodation booking', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'enabled' => esc_html__( 'Yes', 'hbook-admin' ),
							'disabled' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'disabled'
					),
					'hb_multiple_accom_booking_front_end' => array(
						'label' => esc_html__( 'Enable multiple accommodation booking on front-end', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'enabled' => esc_html__( 'Yes', 'hbook-admin' ),
							'disabled' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'disabled',
					),
					'hb_multiple_accom_booking_suggest_occupancy' => array(
						'label' => esc_html__( 'Preferred type of occupancy when suggesting accommodation', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'normal' => esc_html__( 'Normal occupancy', 'hbook-admin' ),
							'max' => esc_html__( 'Maximum occupancy', 'hbook-admin' ),
						),
						'default' => 'normal',
					),
					'hb_multiple_accom_booking_avoid_singleton' => array(
						'label' => esc_html__( 'When possible use maximum occupancy to prevent singleton person in search results suggestion', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'yes',
						'wrapper-class' => 'hb-multiple-accom-booking-avoid-singleton'
					),
				)
			),

			'confirmation_settings' => array(
				'label' => esc_html__( 'Confirmation settings', 'hbook-admin' ),
				'options' => array(
					'hb_resa_unpaid_has_confirmation' => array(
						'label' => esc_html__( 'Unpaid reservations have to be confirmed before dates are blocked out', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'no'
					),
					'hb_resa_paid_has_confirmation' => array(
						'label' => esc_html__( 'Paid reservations have to be confirmed before dates are blocked out', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'no'
					),
				)
			),

			'reservations_status' => array(
				'label' => esc_html__( 'Reservations status', 'hbook-admin' ),
				'options' => array(
					'hb_resa_admin_status' => array(
						'label' => esc_html__( 'Reservations created from admin', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'new' => esc_html__( 'New', 'hbook-admin' ),
							'confirmed' => esc_html__( 'Confirmed', 'hbook-admin' ),
						),
						'default' => 'confirmed'
					),
					'hb_resa_website_status' => array(
						'label' => esc_html__( 'Reservations received from website', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'new' => esc_html__( 'New', 'hbook-admin' ),
							'confirmed' => esc_html__( 'Confirmed', 'hbook-admin' ),
						),
						'default' => 'new'
					),
					'hb_ical_import_resa_status' => array(
						'label' => esc_html__( 'Reservations imported from an external iCal calendar', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'new' => esc_html__( 'New', 'hbook-admin' ),
							'confirmed' => esc_html__( 'Confirmed', 'hbook-admin' ),
						),
						'default' => 'new'
					),
				)
			),

			'resa_id_numbers' => array(
				'label' => esc_html__( 'Invoice id and reservation number', 'hbook-admin' ),
				'options' => array(
					'hb_resa_invoice_id' => array(
						'label' => esc_html__( 'Invoice id structure', 'hbook-admin' ),
						'caption' => esc_html__( 'You can use the following variables: %year, %month, %counter', 'hbook-admin' ),
						'type' => 'text',
						'default' => '%year-%counter'
					),
					'hb_invoice_counter_next_value' => array(
						'label' => esc_html__( 'Invoice id counter next value', 'hbook-admin' ),
						'type' => 'text',
						'class' => 'hb-small-field',
						'default' => 1,
					),
					'hb_invoice_counter_reset_frequency' => array(
						'label' => esc_html__( 'Invoice id counter reset frequency', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yearly' => esc_html__( 'Yearly', 'hbook-admin' ),
							'monthly' => esc_html__( 'Monthly', 'hbook-admin' ),
							'never' => esc_html__( 'Never', 'hbook-admin' ),
						),
						'default' => 'yearly'
					),
					'hb_resa_alphanum' => array(
						'label' => esc_html__( 'Display an alphanumeric reservation number', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'no'
					),
				),
			),

			'opening_dates' => array(
				'label' => esc_html__( 'Opening dates', 'hbook-admin' ),
				'options' => array(
					'hb_min_date_days' => array(
						'label' => esc_html__( 'Minimum selectable date for a reservation', 'hbook-admin' ),
						'caption' => wp_kses( __( 'Enter a number of <u>days</u> from current date...', 'hbook-admin' ), array( 'u' => array() ) ),
						'type' => 'text',
						'class' => 'hb-small-field',
					),
					'hb_min_date_fixed' => array(
						'caption' => esc_html__( '...or enter a fixed date (yyyy-mm-dd format)', 'hbook-admin' ),
						'type' => 'text',
						'class' => 'hb-small-field',
					),
					'hb_max_date_months' => array(
						'label' => esc_html__( 'Maximum selectable date for a reservation', 'hbook-admin' ),
						'caption' => wp_kses( __( 'Enter the maximum number of <u>months</u> ahead...', 'hbook-admin' ), array( 'u' => array() ) ),
						'type' => 'text',
						'class' => 'hb-small-field',
						'default' => '12'
					),
					'hb_max_date_fixed' => array(
						'caption' => esc_html__( '...or enter a fixed date (yyyy-mm-dd format)', 'hbook-admin' ),
						'type' => 'text',
						'class' => 'hb-small-field',
					),
				),
			),

			'date_settings' => array(
				'label' => esc_html__( 'Dates settings', 'hbook-admin' ),
				'options' => array(
					'hb_front_end_date_settings' => array()
				)
			),

			'terms' => array(
				'label' => esc_html__( 'Terms and conditions, Privacy policy', 'hbook-admin' ),
				'options' => array(
					'hb_display_terms_and_cond' => array(
						'label' => esc_html__( 'Display a terms and conditions checkbox', 'hbook-admin' ),
						'caption' => sprintf( esc_html__( 'You can change the text of the terms and conditions checkbox on the %s Text page%s.', 'hbook-admin' ), '<a href="' . esc_url( admin_url( 'admin.php?page=hb_text#hb-text-section-book-now-area' ) ) . '">', '</a>' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'no'
					),
					'hb_display_privacy_policy' => array(
						'label' => esc_html__( 'Display a privacy policy checkbox', 'hbook-admin' ),
						'caption' => sprintf( esc_html__( 'You can change the text of the privacy policy checkbox on the %s Text page%s.', 'hbook-admin' ), '<a href="' . esc_url( admin_url( 'admin.php?page=hb_text#hb-text-section-book-now-area' ) ) . '">', '</a>' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'no'
					),
				),
			),

			'import_export' => array(
				'label' => esc_html__( 'Import / Export HBook settings', 'hbook-admin' ),
				'options' => array(
					'hb_import_export_settings' => array(),
				)
			),

			'reset' => array(
				'label' => esc_html__( 'HBook reset', 'hbook-admin' ),
				'options' => array(
					'hb_reset_settings' => array()
				),
			),

			'misc' => array(
				'label' => esc_html__( 'Misc', 'hbook-admin' ),
				'options' => array(
					'hb_admin_language' => array(
						'label' => esc_html__( 'HBook admin language', 'hbook-admin' ),
						'caption' => str_replace(
							'%s',
							'<a target="_blank" href="https://maestrel.com/knowledgebase/?article=17">' .
							esc_html__( 'knowledgebase', 'hbook-admin' ) .
							'</a>',
							esc_html__( 'You can check available languages on our %s.', 'hbook-admin' )
						),
						'type' => 'radio',
						'choice' => array(
							'en' => esc_html__( 'English', 'hbook-admin' ),
							'user' => esc_html__( 'User language (if available)', 'hbook-admin' ),
						),
						'default' => 'user'
					),
					'hb_resa_page_default_filter' => array(
						'label' => esc_html__( 'Reservations list default filter', 'hbook-admin' ),
						'type' => 'select',
						'choice' => $resa_page_filter_choice,
						'default' => 'none',
					),
					'hb_resa_page_default_filter_check_in_from' => array_merge(
						array(
							'label' => esc_html__( 'Reservations list "By Check-in date" filter default dates', 'hbook-admin' ),
							'wrapper-class' => 'hb-resa-page-default-filter-option hb-resa-page-default-filter-check-in-from',
						),
						$resa_page_default_filter_value_for_from_dates
					),
					'hb_resa_page_default_filter_check_in_to' => array_merge(
						array(
							'wrapper-class' => 'hb-resa-page-default-filter-option hb-resa-page-default-filter-check-in-to',
						),
						$resa_page_default_filter_value_for_to_dates
					),
					'hb_resa_page_default_filter_check_out_from' => array_merge(
						array(
							'label' => esc_html__( 'Reservations list "By Check-out date" filter default dates', 'hbook-admin' ),
							'wrapper-class' => 'hb-resa-page-default-filter-option hb-resa-page-default-filter-check-out-from',
						),
						$resa_page_default_filter_value_for_from_dates
					),
					'hb_resa_page_default_filter_check_out_to' => array_merge(
						array(
							'wrapper-class' => 'hb-resa-page-default-filter-option hb-resa-page-default-filter-check-out-to',
						),
						$resa_page_default_filter_value_for_to_dates
					),
					'hb_resa_page_default_filter_check_in_out_from' => array_merge(
						array(
							'label' => esc_html__( 'Reservations list "By Check-in and Check-out dates" filter default dates', 'hbook-admin' ),
							'wrapper-class' => 'hb-resa-page-default-filter-option hb-resa-page-default-filter-check-in-out-from',
						),
						$resa_page_default_filter_value_for_from_dates
					),
					'hb_resa_page_default_filter_check_in_out_to' => array_merge(
						array(
							'wrapper-class' => 'hb-resa-page-default-filter-option hb-resa-page-default-filter-check-in-out-to',
						),
						$resa_page_default_filter_value_for_to_dates
					),
					'hb_resa_page_default_filter_active_resa_from' => array_merge(
						array(
							'label' => esc_html__( 'Reservations list "By Active reservations" filter default dates', 'hbook-admin' ),
							'wrapper-class' => 'hb-resa-page-default-filter-option hb-resa-page-default-filter-active-resa-from',
						),
						$resa_page_default_filter_value_for_from_dates
					),
					'hb_resa_page_default_filter_active_resa_to' => array_merge(
						array(
							'wrapper-class' => 'hb-resa-page-default-filter-option hb-resa-page-default-filter-active-resa-to',
						),
						$resa_page_default_filter_value_for_to_dates
					),
					'hb_resa_page_default_filter_accom' => array(
						'wrapper-class' => 'hb-resa-page-default-filter-option hb-resa-page-default-filter-accom',
						'label' => esc_html__( 'Reservations list "By Accommodation" filter default value', 'hbook-admin' ),
						'type' => 'select',
						'choice' => $resa_page_filter_accom,
						'default' => 'all',
					),
					'hb_resa_page_default_filter_status' => array(
						'wrapper-class' => 'hb-resa-page-default-filter-option hb-resa-page-default-filter-status',
						'label' => esc_html__( 'Reservations list "By Status" filter default value', 'hbook-admin' ),
						'type' => 'select',
						'choice' => array(
							'confirmed' => esc_html__( 'Confirmed', 'hbook-admin' ),
							'pending' => esc_html__( 'Pending', 'hbook-admin' ),
							'new' => esc_html__( 'New', 'hbook-admin' ),
						),
						'default' => 'confirmed',
					),
					'hb_resa_archiving_delay' => array(
						'label' => esc_html__( 'Reservations archiving delay', 'hbook-admin' ),
						'caption' => esc_html__( 'Enter a number of months.', 'hbook-admin' ),
						'type' => 'text',
						'default' => 2,
					),
					'hb_email_logs_retention_period' => array(
						'label' => esc_html__( 'Email logs retention period', 'hbook-admin' ),
						'caption' => esc_html__( 'Enter a number of days.', 'hbook-admin' ),
						'type' => 'text',
						'default' => 30,
					),
					'hb_email_default_address' => array(
						'label' => esc_html__( 'Email default address', 'hbook-admin' ),
						'type' => 'text',
						'default' => '',
					),
					'hb_accommodation_slug' => array(
						'label' => esc_html__( 'Accommodation url slug', 'hbook-admin' ),
						'caption' => esc_html__( 'The url slug can not be blank. If you leave this field empty the slug will be set to "hb_accommodation".', 'hbook-admin' ),
						'type' => 'text',
						'default' => 'hb_accommodation',
					),
					'hb_uninstall_delete_all' => array(
						'label' => esc_html__( 'Delete all stored information on uninstall', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'no'
					),
					'hb_image_resizing' => array(
						'label' => esc_html__( 'Image resizing', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'dynamic' => esc_html__( 'Dynamic', 'hbook-admin' ),
							'static' => esc_html__( 'Static', 'hbook-admin' ),
						),
						'default' => 'dynamic'
					),
					'hb_ajax_timeout' => array(
						'label' => esc_html__( 'Delay before a timeout error occurs (in ms)', 'hbook-admin' ),
						'type' => 'text',
						'default' => '40000',
						'class' => 'hb-small-field',
					),
					'hb_admin_ajax_timeout' => array(
						'label' => esc_html__( 'Delay before a timeout error occurs (for admin pages - in ms)', 'hbook-admin' ),
						'type' => 'text',
						'default' => '40000',
						'class' => 'hb-small-field',
					),
				)
			)
		);
	}

	public function get_ical_settings() {
		$available_ical_vars = '';
		if ( $this->utils ) {
			$available_ical_vars = $this->utils->get_ical_email_document_available_vars();
		}
		return array(
			'ical_settings' => array(
				'label' => esc_html__( 'Notifications settings', 'hbook-admin' ),
				'options' => array(
					'hb_ical_record_sync_errors' => array(
						'label' => esc_html__( 'Keep records of sync errors', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'yes',
						'wrapper-class' => 'hb-ical-record-sync-errors'
					),
					'hb_ical_notification_option' => array(
						'label' => esc_html__( 'Show notification messages in Reservations page', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'yes',
						'wrapper-class' => 'hb-ical-notification-option'
					),
				)
			),
			'import_settings' => array(
				'label' => esc_html__( 'Import settings', 'hbook-admin' ),
				'options' => array(
					'hb_ical_frequency' => array(
						'label' => esc_html__( 'HBook synchronization frequency (in seconds)', 'hbook-admin' ),
						'type' => 'text',
						'default' => '3600',
					),
					'hb_ical_import_only_resa' => array(
						'label' => esc_html__( 'Import reservations only', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'yes',
						'wrapper-class' => 'hb-ical-no-display'
					),
					'hb_ical_update_resa_dates' => array(
						'label' => esc_html__( 'Update the dates of a reservation when it has been modified in the external calendar', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'no'
					),
					'hb_ical_update_status_resa' => array(
						'label' => esc_html__( 'Update the status of a reservation when it has been cancelled in the external calendar', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'no'
					),
					'hb_ical_import_booking_window' => array(
						'label' => esc_html__( 'Import window maximum date', 'hbook-admin' ),
						'type' => 'text',
						'default' => '',
						'caption' => esc_html__( 'Set the number of months ahead from current date. HBook will import only reservations with check-in within this import window.', 'hbook-admin' ),
					),
					'hb_ical_advanced_notice' => array(
						'label' => esc_html__( 'Import window minimum date', 'hbook-admin' ),
						'type' => 'text',
						'default' => '',
						'caption' => esc_html__( 'Set the number of nights from current date. HBook will not import any reservation with check-in within this advanced notice period of time.', 'hbook-admin' ),
					),
					'hb_ical_exclude_one_day_reservations' => array(
						'label' => esc_html__( 'Exclude one nights reservations from the import', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'no',
						'caption' => esc_html__( 'Set this to "Yes" if you do not accept one-night reservations in any of your external calendars.', 'hbook-admin' ),
					)
				)
			),
			'export_settings' => array(
				'label' => esc_html__( 'Export settings', 'hbook-admin' ),
				'options' => array(
					'hb_ical_export_cancelled_resa' => array(
						'label' => esc_html__( 'Include reservations with status Cancelled in the export', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'no',
						'wrapper-class' => 'hb-ical-no-display'
					),
					'hb_ical_export_blocked_dates' => array(
						'label' => esc_html__( 'Include blocked dates in the export', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'yes',
						'wrapper-class' => 'hb_ical_export_blocked_dates'
					),
					'hb_ical_export_preparation_time' => array(
						'label' => esc_html__( 'Include "Preparation time" blocked dates in the export', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'no',
						'wrapper-class' => 'hb_ical_export_preparation_time'
					),
					'hb_ical_export_only_confirmed' => array(
						'label' => esc_html__( 'Include only reservations with status "Confirmed" in the export', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'no'
					),

					'hb_ical_summary' => array(
						'label' => esc_html__( 'Summary of reservation in the export', 'hbook-admin' ),
						'type' => 'text',
						'default' => 'HBook reservation - [customer_first_name] [customer_last_name] - Resa id: [resa_id]'
					),
					'hb_ical_description' => array(
						'label' => esc_html__( 'Description of reservation in the export', 'hbook-admin' ),
						'type' => 'textarea',
						'default' => 'NAME: [customer_first_name] [customer_last_name]' . "\r\n" .
									'EMAIL: [customer_email]',
						'caption' => esc_html__( 'You can use the following variables in the "Summary" field and the "Description" field:', 'hbook-admin' ) .
									'<br/>' .
									$available_ical_vars
					),
				),
			),
		);
	}

	public function get_search_form_options() {
		return array(
			'search_form_options' => array(
				'options' => array(
					'hb_display_accom_number_field' => array(
						'label' => esc_html__( 'Display a field "Number of accommodation"', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'no',
						'wrapper-class' => 'hb-accom-number-option-wrapper',
					),
					'hb_maximum_accom_number' => array(
						'label' => esc_html__( '"Number of accommodation" field maximum number', 'hbook-admin' ),
						'type' => 'text',
						'class' => 'hb-small-field',
						'default' => '5',
						'wrapper-class' => 'hb-maximum-accom-number-option-wrapper',
					),
					'hb_display_adults_field' => array(
						'label' => esc_html__( 'Display a field "Adults"', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'yes'
					),
					'hb_display_children_field' => array(
						'label' => esc_html__( 'Display a field "Children"', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'yes'
					),
					'hb_minimum_adults' => array(
						'label' => esc_html__( '"Adults" field minimum number', 'hbook-admin' ),
						'type' => 'text',
						'class' => 'hb-small-field',
						'default' => '1',
						'wrapper-class' => 'hb-minimum-adults-option-wrapper',
					),
					'hb_maximum_adults' => array(
						'label' => esc_html__( '"Adults" field maximum number', 'hbook-admin' ),
						'type' => 'text',
						'class' => 'hb-small-field',
						'default' => '5',
						'wrapper-class' => 'hb-maximum-adults-option-wrapper',
					),
					'hb_maximum_children' => array(
						'label' => esc_html__( '"Children" field maximum number', 'hbook-admin' ),
						'type' => 'text',
						'class' => 'hb-small-field',
						'default' => '5',
						'wrapper-class' => 'hb-maximum-children-option-wrapper',
					),
					'hb_minimum_occupancy_search_fields_on_accom_page' => array(
						'label' => esc_html__( 'Use Accommodation minimum occupancy for Search form fields on Accommodation pages', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'yes',
						'wrapper-class' => 'hb-minimum-occupancy-search-fields-option-wrapper',
					),
					'hb_maximum_occupancy_search_fields_on_accom_page' => array(
						'label' => esc_html__( 'Use Accommodation maximum occupancy for Search form fields on Accommodation pages', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'yes',
						'wrapper-class' => 'hb-maximum-occupancy-search-fields-option-wrapper',
					),
					'hb_search_form_placeholder' => array(
						'label' => esc_html__( 'Display placeholders instead of labels', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'no'
					),
				)
			)
		);
	}

	public function get_accom_selection_options() {
		return array(
			'accom_selection_options' => array(
				'options' => array(
					'hb_select_accom_num' => array(
						'label' => esc_html__( 'Customers can choose the accommodation number', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'no',
						'caption' => esc_html__( 'You will enable this option if you wish to allow your customer to pick a specific accommodation if several of the same type are available.', 'hbook-admin' )
					),
					'hb_title_accom_link' => array(
						'label' => esc_html__( 'Link title towards accommodation pages', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'no'
					),
					'hb_thumb_display' => array(
						'label' => esc_html__( 'Display an accommodation thumbnail', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'yes'
					),
					'hb_thumb_accom_link' => array(
						'label' => esc_html__( 'Link thumbnail towards accommodation pages', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'no'
					),
					'hb_search_accom_thumb_width' => array(
						'label' => esc_html__( 'Thumbnail width (in px)', 'hbook-admin' ),
						'type' => 'text',
						'default' => '100',
						'class' => 'hb-small-field',
					),
					'hb_search_accom_thumb_height' => array(
						'label' => esc_html__( 'Thumbnail height (in px)', 'hbook-admin' ),
						'type' => 'text',
						'default' => '100',
						'class' => 'hb-small-field',
					),
					'hb_button_accom_link' => array(
						'label' => esc_html__( 'Display a button that links towards accommodation pages', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'no'
					),
					'hb_display_price' => array(
						'label' => esc_html__( 'Display price', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'yes'
					),
					'hb_display_price_breakdown' => array(
						'label' => esc_html__( 'Display price breakdown', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'yes'
					),
					'hb_price_breakdown_default_state' => array(
						'label' => esc_html__( 'Price breakdown default state', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'opened' => esc_html__( 'Opened', 'hbook-admin' ),
							'closed' => esc_html__( 'Closed', 'hbook-admin' ),
						),
						'default' => 'closed'
					),
					'hb_display_detailed_accom_price' => array(
						'label' => esc_html__( 'Display detailed accommodation price', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'yes'
					),
					'hb_display_accom_left' => array(
						'label' => esc_html__( 'Display number of available accommodation', 'hbook-admin' ),
						'type' => 'radio',
						'choice' => array(
							'yes' => esc_html__( 'Yes', 'hbook-admin' ),
							'no' => esc_html__( 'No', 'hbook-admin' ),
						),
						'default' => 'no'
					),
					'hb_display_accom_left_threshold' => array(
						'label' => esc_html__( 'Threshold to display number of available accommodation', 'hbook-admin' ),
						'type' => 'text',
						'default' => '10',
						'class' => 'hb-small-field',
						'wrapper-class' => 'hb-display-accom-left-threshold-wrapper',
					),
				)
			)
		);
	}

	public function init_options() {
		$options = array_merge(
			$this->get_misc_settings(),
			$this->get_ical_settings(),
			$this->get_payment_settings(),
			$this->get_appearance_settings(),
			$this->get_search_form_options(),
			$this->get_accom_selection_options()
		);
		foreach ( $options as $section ) {
			foreach ( $section['options'] as $id => $option ) {
				if ( ( get_option( $id ) === false ) && ( isset( $option['default'] ) ) ) {
					add_option( $id, $option['default'] );
				}
			}
		}
		if ( ( get_option( 'hb_stripe_mode' ) === false ) ) {
			update_option( 'hb_stripe_mode', 'test' );
		}
		if ( ( get_option( 'hb_paypal_mode' ) === false ) ) {
			update_option( 'hb_paypal_mode', 'sandbox' );
		}
		if ( ( get_option( 'hb_curl_set_timeout' ) === false ) ) {
			update_option( 'hb_curl_set_timeout', 'no' );
		}
		if ( ( get_option( 'hb_curl_set_ssl_version' ) === false ) ) {
			update_option( 'hb_curl_set_ssl_version', 'no' );
		}
		if ( get_option( 'hb_store_credit_card' ) === false ) {
			update_option( 'hb_store_credit_card', 'no' );
		}
		if ( get_option( 'hb_store_credit_card' ) === false ) {
			update_option( 'hb_store_credit_card', 'no' );
		}
		if ( get_option( 'hb_ical_exclude_one_day_reservations' ) === false ) {
			update_option( 'hb_ical_exclude_one_day_reservations', 'no' );
		}
		if ( get_option( 'hb_ical_advanced_notice' ) === false ) {
			update_option( 'hb_ical_advanced_notice', 0 );
		}
		if ( get_option( 'hb_ical_import_booking_window' ) === false ) {
			update_option( 'hb_ical_import_booking_window', 0 );
		}
		if ( get_option( 'hb_paypal_icons' ) === false ) {
			update_option( 'hb_paypal_icons', '["paypal"]' );
		}
		if ( get_option( 'hb_stripe_powered_by' ) === false ) {
			update_option( 'hb_stripe_powered_by', 'no' );
		}
		if ( get_option( 'hb_charge_per_day' ) === false ) {
			update_option( 'hb_charge_per_day', 'no' );
		}
	}

	public function get_non_standard_options() {
		return array(
			'non_standard' => array(
				'options' => array(
					'hb_stripe_active' => array(),
					'hb_paypal_active' => array(),
					'hb_stripe_test_secret_key' => array(),
					'hb_stripe_test_publishable_key' => array(),
					'hb_stripe_live_secret_key' => array(),
					'hb_stripe_live_publishable_key' => array(),
					'hb_paypal_api_sandbox_user' => array(),
					'hb_paypal_api_sandbox_psw' => array(),
					'hb_paypal_api_sandbox_signature' => array(),
					'hb_paypal_api_live_user' => array(),
					'hb_paypal_api_live_psw' => array(),
					'hb_paypal_api_live_signature' => array(),
					'hb_paypal_mode' => array(),
					'hb_stripe_mode' => array(),

					'hb_valid_purchase_code' => array(),
					'hb_purchase_code_error' => array(),
					'hb_purchase_code' => array(),
					'hb_last_synced' => array(),

					'hb_form_style' => array(),

					'hb_store_credit_card' => array(),

					'hb_curl_set_timeout' => array(),
					'hb_curl_set_ssl_version' => array(),
				)
			)
		);
	}

	public function get_former_options() {
		return array(
			'former' => array(
				'options' => array(
					'hb_notify_admin' => array(),
					'hb_admin_email_subject' => array(),
					'hb_admin_message_type' => array(),
					'hb_admin_email_message' => array(),
					'hb_ack_email' => array(),
					'hb_ack_email_subject' => array(),
					'hb_ack_message_type' => array(),
					'hb_ack_email_message' => array(),
					'hb_confirm_email' => array(),
					'hb_confirm_email_subject' => array(),
					'hb_confirm_message_type' => array(),
					'hb_confirm_email_message' => array(),
					'hb_admin_email' => array(),
					'hb_ack_email_from' => array(),
					'hb_confirm_email_from' => array(),
					'hb_admin_email_from' => array(),
					'hb_flush_rewrite' => array(),
					'hb_paypal_sandbox' => array(),
					'hb_paypal_api_user' => array(),
					'hb_paypal_api_psw' => array(),
					'hb_paypal_api_signature' => array(),
				)
			)
		);
	}

	public function delete_options() {
		$options = array_merge(
			$this->get_misc_settings(),
			$this->get_ical_settings(),
			$this->get_payment_settings(),
			$this->get_appearance_settings(),
			$this->get_search_form_options(),
			$this->get_accom_selection_options(),
			$this->get_non_standard_options(),
			$this->get_former_options()
		);
		foreach ( $options as $section ) {
			foreach ( $section['options'] as $id => $option ) {
				delete_option( $id );
			}
		}

		$non_standard_options = array(
			'hb_notify_admin',
			'hb_admin_email_subject',
			'hb_admin_message_type',
			'hb_admin_email_message',
			'hb_ack_email',
			'hb_ack_email_subject',
			'hb_ack_message_type',
			'hb_ack_email_message',
			'hb_confirm_email',
			'hb_confirm_email_subject',
			'hb_confirm_message_type',
			'hb_confirm_email_message',
			'hb_admin_email',
			'hb_ack_email_from',
			'hb_confirm_email_from',
			'hb_admin_email_from',
			'hb_flush_rewrite',
			'hb_paypal_sandbox',
			'hb_paypal_api_user',
			'hb_paypal_api_psw',
			'hb_paypal_api_signature',

			'hb_stripe_active',
			'hb_paypal_active',
			'hb_stripe_test_secret_key',
			'hb_stripe_test_publishable_key',
			'hb_stripe_live_secret_key',
			'hb_stripe_live_publishable_key',
			'hb_paypal_api_sandbox_user',
			'hb_paypal_api_sandbox_psw',
			'hb_paypal_api_sandbox_signature',
			'hb_paypal_api_live_user',
			'hb_paypal_api_live_psw',
			'hb_paypal_api_live_signature',
			'hb_paypal_mode',
			'hb_stripe_mode',

			'hb_valid_purchase_code',
			'hb_purchase_code_error',
			'hb_purchase_code',
			'hb_last_synced',

			'hb_form_style',

			'hb_store_credit_card',

			'hb_curl_set_timeout',
			'hb_curl_set_ssl_version',

			'hb_last_scheduled_emails_execution',
		);

		foreach ( $non_standard_options as $option_id ) {
			delete_option( $option_id );
		}
	}

	public function get_options_list( $options_name ) {
		$get_option_name_function = 'get_' . $options_name;
		$options_to_get = $this->$get_option_name_function();
		$options_list = array();
		foreach ( $options_to_get as $section ) {
			foreach ( $section['options'] as $id => $option ) {
				$options_list[] = $id;
			}
		}
		return $options_list;
	}

	public function display_section_title( $title ) {
	?>

		<hr/>
		<h3><?php echo( esc_html( $title ) ); ?></h3>

	<?php
	}

	public function display_section_desc( $desc ) {
	?>

		<small><?php echo( wp_kses_post( $desc ) ); ?></small>

	<?php
	}

	public function display_text_option( $id, $option ) {
		$class = '';
		$caption_class = '';
		$wrapper_class = '';
		if ( isset( $option['class'] ) ) {
			$class = $option['class'];
		}
		if ( isset( $option['wrapper-class'] ) ) {
			$wrapper_class = $option['wrapper-class'];
		}
		?>

		<p class="<?php echo( esc_attr( $wrapper_class ) ); ?>">
			<?php
			if ( isset( $option['label'] ) ) {
			?>
			<label for="<?php echo( esc_attr( $id ) ); ?>"><?php echo( esc_html( $option['label'] ) ); ?></label><br/>
			<?php
			} else {
				$caption_class = "hb-no-label";
			}
			?>
			<?php
			if ( isset( $option['caption'] ) ) {
			?>
			<small class="<?php echo( esc_attr( $caption_class ) ); ?>"><?php echo( wp_kses_post( $option['caption'] ) ); ?></small><br/>
			<?php
			}
			?>
			<input
				type="text"
				id="<?php echo( esc_attr( $id ) ); ?>"
				name="<?php echo( esc_attr( $id ) ); ?>"
				class="<?php echo( esc_attr( $class ) ); ?>"
				size="50"
				value="<?php echo( esc_attr( get_option( $id ) ) ); ?>"
			/>
		</p>
	<?php
	}

	public function display_textarea_option( $id, $option ) {
		$wrapper_class = '';
		if ( isset( $option['wrapper-class'] ) ) {
			$wrapper_class = $option['wrapper-class'];
		}
		?>

		<p class="<?php echo( esc_attr( $wrapper_class ) ); ?>">
			<label for="<?php echo( esc_attr( $id ) ); ?>"><?php echo( esc_html( $option['label'] ) ); ?></label><br/>
			<textarea id="<?php echo( esc_attr( $id ) ); ?>" name="<?php echo( esc_attr( $id ) ); ?>" rows="8" class="widefat"><?php echo( esc_textarea( get_option( $id ) ) ); ?></textarea><br/>
			<?php if ( isset( $option['caption'] ) && $option['caption'] != '' ) { ?>
			<small class="hb-textarea-caption"><?php echo( wp_kses_post( $option['caption'] ) ); ?></small>
			<?php } ?>
		</p>
	<?php
	}

	public function display_radio_option( $id, $option ) {
		$wrapper_class = '';
		if ( isset( $option['wrapper-class'] ) ) {
			$wrapper_class = $option['wrapper-class'];
		}
		?>

		<p class="<?php echo( esc_attr( $wrapper_class ) ); ?>">
			<?php
			if ( isset( $option['label'] ) ) {
			?>
			<label><?php echo( esc_html( $option['label'] ) ); ?></label><br/>
			<?php
			}
			if ( isset( $option['caption'] ) ) {
			?>
			<small><?php echo( wp_kses_post( $option['caption'] ) ); ?></small><br/>
			<?php
			}
			foreach ( $option['choice'] as $choice_id => $choice_label ) {
			?>
			<input type="radio" id="<?php echo( esc_attr( $id . '_' . $choice_id ) ); ?>" name="<?php echo( esc_attr( $id ) ); ?>" value="<?php echo( esc_attr( $choice_id ) ); ?>" <?php echo( get_option( $id ) == $choice_id ? 'checked' : '' ); ?> />
			<label for="<?php echo( esc_attr( $id . '_' . $choice_id ) ); ?>"><?php echo( esc_html( $choice_label ) ); ?></label>&nbsp;&nbsp;
				<?php
				if ( count ( $option['choice'] ) > 2 ) {
					echo( '<br/>' );
				}
			}
			?>
		</p>
	<?php
	}

	public function display_checkbox_option( $id, $option ) {
		$wrapper_class = '';
		if ( isset( $option['wrapper-class'] ) ) {
			$wrapper_class = $option['wrapper-class'];
		}
		$saved_choices = json_decode( get_option( $id ), true );
		if ( ! is_array( $saved_choices ) ) {
			$saved_choices = array();
		}
		?>

		<p class="<?php echo( esc_attr( $wrapper_class ) ); ?>">
			<label><?php echo( esc_html( $option['label'] ) ); ?></label><br/>
			<?php
			if ( isset( $option['caption'] ) ) {
			?>
			<small><?php echo( wp_kses_post( $option['caption'] ) ); ?></small><br/>
			<?php
			}
			foreach ( $option['choice'] as $choice_id => $choice_label ) {
			?>
			<input
				type="checkbox"
				id="<?php echo( esc_attr( $id . '_' . $choice_id ) ); ?>"
				name="<?php echo( esc_attr( $id ) ); ?>[]"
				value="<?php echo( esc_attr( $choice_id ) ); ?>"
				<?php echo( in_array( $choice_id, $saved_choices ) ? 'checked' : '' ); ?>
			/>
			<label for="<?php echo( esc_attr( $id . '_' . $choice_id ) ); ?>"><?php echo( esc_html( $choice_label ) ); ?></label>&nbsp;&nbsp;
				<?php
				if ( count ( $option['choice'] ) > 2 ) {
					echo( '<br/>' );
				}
			}
			?>
		</p>
	<?php
	}

	public function display_select_option( $id, $option ) {
		$wrapper_class = '';
		if ( isset( $option['wrapper-class'] ) ) {
			$wrapper_class = $option['wrapper-class'];
		}
		?>

		<p class="<?php echo( esc_attr( $wrapper_class ) ); ?>">
			<?php if ( isset( $option['label'] ) ) { ?>
			<label for="<?php echo( esc_attr( $id ) ); ?>"><?php echo( esc_html( $option['label'] ) ); ?></label><br/>
			<?php
			}
			if ( isset( $option['caption'] ) ) {
			?>
			<small class="<?php echo( esc_attr( $caption_class ) ); ?>"><?php echo( wp_kses_post( $option['caption'] ) ); ?></small><br/>
			<?php
			}
			$current_choice = get_option( $id );
			?>
			<select id="<?php echo( esc_attr( $id ) ); ?>" name="<?php echo( esc_attr( $id ) ); ?>">
				<?php foreach ( $option['choice'] as $choice_id => $choice_label ) { ?>
					<option value="<?php echo( esc_attr( $choice_id ) ); ?>" <?php if ( $choice_id == $current_choice ) { echo( 'selected' ); } ?>><?php echo( esc_html( $choice_label ) ); ?></option>
				<?php } ?>
			</select>
		</p>
	<?php
	}

	public function display_save_options_section() {
	?>
		<div class="hb-options-save-wrapper">

			<span class="hb-ajaxing">
				<span class="spinner"></span>
				<span><?php esc_html_e( 'Saving...', 'hbook-admin' ); ?></span>
			</span>

			<span class="hb-saved"></span>

			<p>
				<a href="#" class="hb-options-save button-primary"><?php esc_html_e( 'Save changes', 'hbook-admin' ); ?></a>
			</p>

		</div>
	<?php
	}

}