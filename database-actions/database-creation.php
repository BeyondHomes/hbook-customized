<?php
class HbDataBaseCreation {

	private $hbdb;
	private $utils;
	private $schema;
	private $versions;

	public function __construct( $hbdb, $utils, $schema ) {
		$this->versions = array(
			'1.0',
			'1.1',
			'1.2', '1.2.1', '1.2.2', '1.2.3', '1.2.4',
			'1.3', '1.3.1',
			'1.4', '1.4.1', '1.4.2', '1.4.3', '1.4.4',
			'1.5', '1.5.1', '1.5.2', '1.5.3', '1.5.4',
			'1.6', '1.6.1', '1.6.2', '1.6.3', '1.6.4', '1.6.5',
			'1.7', '1.7.1', '1.7.2', '1.7.3', '1.7.4', '1.7.5', '1.7.6', '1.7.7',
			'1.8', '1.8.1', '1.8.2', '1.8.3', '1.8.4', '1.8.5', '1.8.6', '1.8.7', '1.8.8', '1.8.9', '1.8.10',
			'1.9', '1.9.1', '1.9.2', '1.9.3', '1.9.4', '1.9.5',
			'2.0', '2.0.1', '2.0.2', '2.0.3', '2.0.4', '2.0.5', '2.0.6',
		);
		$this->hbdb = $hbdb;
		$this->utils = $utils;
		$this->schema = $schema;
	}

	public function alter_data_before_table_update( $installed_version ) {
		global $wpdb;
		if ( version_compare( '1.2', $installed_version ) > 0 ) {
			$query = "RENAME TABLE {$this->hbdb->rates_table} TO {$this->hbdb->rates_table}_old";
			$wpdb->query( $query );
		}
		if ( version_compare( '1.6.3', $installed_version ) > 0 ) {
			$this->update_synced_calendars();
		}
	}

	public function create_update_plugin_tables() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $this->schema->get_schema() );
	}

	public function alter_data( $installed_version ) {
		global $wpdb;
		if ( version_compare( '1.2', $installed_version ) > 0 ) {
			$this->from_rates_1_1_to_rates_1_2();
			update_option( 'hbook_installing_version', '1.2' );
		}
		if ( version_compare( '1.4', $installed_version ) > 0 ) {
			$query = "UPDATE {$this->hbdb->resa_table} SET paid = -1";
			$wpdb->query( $query );
			update_option( 'hbook_installing_version', '1.4' );
		}
		if ( version_compare( '1.5', $installed_version ) > 0 ) {
			$this->insert_accom_num_name_v_1_5();
			$this->from_email_settings_to_email_templates_v_1_5();
			update_option( 'hbook_installing_version', '1.5' );
		}
		if ( version_compare( '1.5.1', $installed_version ) > 0 ) {
			if ( get_option( 'hb_min_date' ) ) {
				update_option( 'hb_min_date_fixed', get_option( 'hb_min_date' ) );
			}
			update_option( 'hbook_installing_version', '1.5.1' );
		}

		if ( version_compare( '1.6', $installed_version ) > 0 ) {
			$this->alter_data_v_1_6();
			$this->update_paypal_options();
			if ( get_option( 'hb_purchase_code' ) ) {
				$this->utils->verify_purchase_code( get_option( 'hb_purchase_code' ) );
			}
			update_option( 'hbook_installing_version', '1.6' );
		}
		if ( version_compare( '1.6.2', $installed_version ) > 0 ) {
			$this->alter_data_v_1_6_2();
			update_option( 'hbook_installing_version', '1.6.2' );
		}
		if ( version_compare( '1.6.4', $installed_version ) > 0 ) {
			$query = "UPDATE {$this->hbdb->fields_table} SET data_about = 'customer'";
			$wpdb->query( $query );
			update_option( 'hbook_installing_version', '1.6.4' );
		}
		if ( version_compare( '1.7', $installed_version ) > 0 ) {
			$this->alter_data_v_1_7();
			$this->update_options_v_1_7();
			update_option( 'hbook_installing_version', '1.7' );
		}
		if ( version_compare( '1.8', $installed_version ) > 0 ) {
			$this->alter_data_v_1_8();
			$this->update_options_v_1_8();
			update_option( 'hbook_installing_version', '1.8' );
		}
		if ( version_compare( '1.8.7', $installed_version ) > 0 ) {
			$this->alter_data_v_1_8_7();
			$this->update_options_v_1_8_7();
			update_option( 'hbook_installing_version', '1.8.7' );
		}
		if ( version_compare( '1.9', $installed_version ) > 0 ) {
			$this->alter_data_v_1_9();
			update_option( 'hbook_installing_version', '1.9' );
		}
		if ( version_compare( '1.9.1', $installed_version ) > 0 ) {
			$this->alter_data_v_1_9_1();
			update_option( 'hbook_installing_version', '1.9.1' );
		}
		if ( version_compare( '2.0.3', $installed_version ) > 0 ) {
			$this->alter_data_v_2_0_3();
			update_option( 'hbook_installing_version', '2.0.3' );
		}
		if ( version_compare( '2.0.4', $installed_version ) > 0 ) {
			$this->alter_data_v_2_0_4();
			update_option( 'hbook_installing_version', '2.0.4' );
		}
		if ( version_compare( '2.0.6', $installed_version ) > 0 ) {
			$this->alter_data_v_2_0_6();
			update_option( 'hbook_installing_version', '2.0.6' );
		}
	}

	private function from_rates_1_1_to_rates_1_2() {
		global $wpdb;
		$old_rate_table = $this->hbdb->rates_table . '_old';
		$new_rate_table = $this->hbdb->rates_table;
		$rates = $wpdb->get_results(
			"
			SELECT *
			FROM $old_rate_table
			"
			, ARRAY_A
		);
		foreach ( $rates as $old_rate ) {
			$new_rate = array(
				'all_accom' => 0,
				'all_seasons' => 0,
				'amount' => $old_rate['rate'],
				'nights' => 1
			);
			switch ( $old_rate['type'] ) {
				case 'normal' : $new_rate['type'] = 'accom'; break;
				case 'adult' : $new_rate['type'] = 'extra_adults'; break;
				case 'child' : $new_rate['type'] = 'extra_children'; break;
			}
			$wpdb->insert( $new_rate_table, $new_rate );
			$rate_id = $wpdb->insert_id;
			$wpdb->insert( $this->hbdb->rates_accom_table, array( 'rate_id' => $rate_id, 'accom_id' => $old_rate['accom_id'] ) );
			$wpdb->insert( $this->hbdb->rates_seasons_table, array( 'rate_id' => $rate_id, 'season_id' => $old_rate['season_id'] ) );
		}
	}

	private function insert_accom_num_name_v_1_5() {
		global $wpdb;
		$accom_ids = $this->hbdb->get_all_accom_ids();
		foreach ( $accom_ids as $accom_id ) {
			$accom_quantity = get_post_meta( $accom_id, 'accom_quantity', true );
			if ( $accom_quantity == '' ) {
				$accom_quantity = 1;
			}
			$accom_num_name = array();
			for ( $i = 1; $i <= $accom_quantity; $i++ ) {
				$wpdb->insert(
					$this->hbdb->accom_num_name_table,
					array(
						'accom_id' => $accom_id,
						'accom_num' => $i,
						'num_name' => $i
					)
				);
			}
			update_post_meta( $accom_id, 'accom_num_name_index', $i - 1 );
		}
	}

	public function from_email_settings_to_email_templates_v_1_5() {
		global $wpdb;
		$table_name = $this->hbdb->email_templates_table;

		$email_templates = array();

		$email_templates['admin']['name'] = esc_html__( 'Admin notification', 'hbook-admin' );
		$email_templates['ack']['name'] = esc_html__( 'Customer notification', 'hbook-admin' );
		$email_templates['confirm']['name'] = esc_html__( 'Reservation confirmation', 'hbook-admin' );

		$email_templates['admin']['to_address'] = get_option( 'hb_admin_email', '' );
		$email_templates['ack']['to_address'] = '[customer_email]';
		$email_templates['confirm']['to_address'] = '[customer_email]';

		$settings = array(
			'email_from' => 'from_address',
			'email_subject' => 'subject',
			'email_message' => 'message',
			'message_type' => 'format',
		);
		$email_types = array( 'admin', 'ack', 'confirm' );

		foreach ( $email_types as $email_type ) {
			foreach ( $settings as $old_setting => $new_setting ) {
				$email_templates[ $email_type ][ $new_setting ] = get_option( 'hb_' . $email_type . '_' . $old_setting );
			}

			$email_templates[ $email_type ]['lang'] = 'all';
			$email_templates[ $email_type ]['format'] = strtoupper( $email_templates[ $email_type ]['format'] );
		}

		if ( get_option( 'hb_notify_admin' ) == 'yes' ) {
			$email_templates['admin']['action'] = 'new_resa';
		} else {
			$email_templates['admin']['action'] = 'not_automatic';
		}
		if ( get_option( 'hb_ack_email' ) == 'yes' ) {
			$email_templates['ack']['action'] = 'new_resa';
		} else {
			$email_templates['ack']['action'] = 'not_automatic';
		}
		if ( get_option( 'hb_confirm_email' ) == 'yes' ) {
			$email_templates['confirm']['action'] = 'confirmation_resa';
		} else {
			$email_templates['confirm']['action'] = 'not_automatic';
		}

		foreach ( $email_templates as $email_tmpl ) {
			$values = "'" . implode( "', '", $email_tmpl ) . "'";
			$query =
				"
				INSERT INTO $table_name
				(name, to_address, from_address, subject, message, format, lang, action)
				VALUES
				( $values )
				";
			$wpdb->query( $query );
		}
	}

	private function alter_data_v_1_6() {
		global $wpdb;

		$gmt_offset = get_option( 'gmt_offset' );
		if ( ! $gmt_offset ) {
			$gmt_offset = 0;
		}
		$query = "UPDATE {$this->hbdb->resa_table} SET received_on = received_on - INTERVAL $gmt_offset HOUR";
		$wpdb->query( $query );

		$site_url = site_url();
		$query = "UPDATE {$this->hbdb->resa_table} SET updated_on = received_on, origin = 'website', uid = CONCAT( 'D', SUBSTRING( received_on, 1 ,10 ), 'T', SUBSTRING( received_on, 11 ,19 ), 'R', FLOOR( 10000 + ( RAND() * 89999 ) ), '@$site_url' )";
		$wpdb->query( $query );

		$all_accom_blocked = $wpdb->get_results( "SELECT * FROM {$this->hbdb->accom_blocked_table}", ARRAY_A );
		foreach ( $all_accom_blocked as $blocked ) {
			$wpdb->update(
				$this->hbdb->accom_blocked_table,
				array(
					'uid' => $this->hbdb->get_uid(),
				),
				array(
					'id' => $blocked['id']
				)
			);
		}

		$all_accom_all_num_blocked = $wpdb->get_results(
			"
			SELECT *
			FROM {$this->hbdb->accom_blocked_table}
			WHERE accom_all_num = 1 AND accom_num = 0
			", ARRAY_A
		);
		foreach ( $all_accom_all_num_blocked as $blocked ) {
			$accom_num_name = $this->hbdb->get_accom_num_name( $blocked['accom_id'] );
			foreach ( $accom_num_name as $accom_num => $accom_name ) {
				$wpdb->insert(
					$this->hbdb->accom_blocked_table,
					array(
						'accom_id' => $blocked['accom_id'],
						'accom_all_ids' => 0,
						'accom_num' => $accom_num,
						'accom_all_num' => 1,
						'from_date' => $blocked['from_date'],
						'to_date' => $blocked['to_date'],
						'uid' => $this->hbdb->get_uid(),
					)
				);
			}
			$wpdb->delete( $this->hbdb->accom_blocked_table, array( 'id' => $blocked['id'] ) );
		}

		$customer_info = array( 'first_name', 'last_name', 'email', 'address_1', 'address_2', 'city', 'state_province', 'zip_code', 'country', 'phone' );
		$customers = $this->hbdb->get_all( 'customers' );
		foreach ( $customers as $customer ) {
			$customer_data = array();
			foreach ( $customer_info as $info ) {
				if ( $customer[ $info ] != '' ) {
					$customer_data[ $info ] = $customer[ $info ];
				}
			}
			$wpdb->update(
				$this->hbdb->customers_table,
				array(
					'info' => json_encode( $customer_data ),
				),
				array(
					'id' => $customer['id']
				)
			);
		}
	}

	private function update_paypal_options() {
		if ( get_option( 'hb_paypal_sandbox' ) == 'yes' ) {
			update_option( 'hb_paypal_mode', 'sandbox' );
			update_option( 'hb_paypal_api_sandbox_user', get_option( 'hb_paypal_api_user' ) );
			update_option( 'hb_paypal_api_sandbox_psw', get_option( 'hb_paypal_api_psw' ) );
			update_option( 'hb_paypal_api_sandbox_signature', get_option( 'hb_paypal_api_signature' ) );
		} else {
			update_option( 'hb_paypal_mode', 'live' );
			update_option( 'hb_paypal_api_live_user', get_option( 'hb_paypal_api_user' ) );
			update_option( 'hb_paypal_api_live_psw', get_option( 'hb_paypal_api_psw' ) );
			update_option( 'hb_paypal_api_live_signature', get_option( 'hb_paypal_api_signature' ) );
		}
	}

	private function alter_data_v_1_6_2() {
		global $wpdb;

		$query = "UPDATE {$this->hbdb->booking_rules_table} SET all_seasons = 1";
		$wpdb->query( $query );

		$query = "UPDATE {$this->hbdb->discounts_table} SET all_seasons = 1";
		$wpdb->query( $query );

		$seasons = $this->hbdb->get_all( 'seasons' );
		$rules = $this->hbdb->get_all( 'booking_rules' );
		$values = array();
		foreach ( $seasons as $season ) {
			foreach ( $rules as $rule ) {
				$values[] = '(' . intval( $rule['id'] ) . ',' . intval( $season['id'] ) . ')';
			}
		}
		if ( $values ) {
			$values = implode( ',', $values );
			$query =
				"
				INSERT INTO {$this->hbdb->booking_rules_seasons_table} (rule_id, season_id)
				VALUES $values
				";
			$wpdb->query( $query );
		}

		$seasons = $this->hbdb->get_all( 'seasons' );
		$discounts = $this->hbdb->get_all( 'discounts' );
		$values = array();
		foreach ( $seasons as $season ) {
			foreach ( $discounts as $discount ) {
				$values[] = '(' . intval( $discount['id'] ) . ',' . intval( $season['id'] ) . ')';
			}
		}
		if ( $values ) {
			$values = implode( ',', $values );
			$query =
				"
				INSERT INTO {$this->hbdb->discounts_seasons_table} (discount_id, season_id)
				VALUES $values
				";
			$wpdb->query( $query );
		}
	}

	private function update_synced_calendars() {
		global $wpdb;
		if ( $wpdb->get_results( "SHOW TABLES LIKE '{$this->hbdb->ical_table}'", ARRAY_A ) ) {
			$query = "ALTER TABLE {$this->hbdb->ical_table} ADD synchro_id varchar(128) NOT NULL;";
			$wpdb->query( $query );
			$synced_calendars = $this->hbdb->get_ical_sync();
			foreach ( $synced_calendars as $synced_calendar ) {
				$synchro_id = uniqid( '', true );
				$synchro_url = $synced_calendar['synchro_url'];
				$wpdb->update(
					$this->hbdb->ical_table,
					array(
						'synchro_id' => $synchro_id,
					),
					array(
						'synchro_url' => $synchro_url,
					)
				);
			}
			$query = "ALTER TABLE {$this->hbdb->ical_table} ADD PRIMARY KEY(synchro_id);";
			$wpdb->query( $query );
		}
	}

	private function alter_data_v_1_7() {
		global $wpdb;

		$query = "UPDATE {$this->hbdb->options_table} SET quantity_max_option = 'no'";
		$wpdb->query( $query );

		$paypal_desc_vars = array(
			'txt_deposit',
			'txt_desc',
			'txt_one_adult',
			'txt_one_child',
			'txt_one_night',
			'txt_several_adults',
			'txt_several_children',
			'txt_several_nights',
		);
		foreach ( $paypal_desc_vars as $paypal_desc_var ) {
			$old_id = 'paypal_' . $paypal_desc_var;
			$new_id = 'external_payment_' . $paypal_desc_var;
			$query = "UPDATE {$this->hbdb->strings_table} SET id = '$new_id' WHERE id = '$old_id'";
			$wpdb->query( $query );
		}
	}

	private function update_options_v_1_7() {
		if ( get_option( 'hb_form_style' ) == 'plugin' ) {
			update_option( 'hb_buttons_style', 'custom' );
			update_option( 'hb_inputs_selects_style', 'custom' );
		}
		if ( get_option( 'hb_resa_payment' ) == 'none' ) {
			update_option( 'hb_resa_payment', 'offline' );
		}
	}

	public function alter_data_v_1_8() {
		global $wpdb;
		$email_templates = $this->hbdb->get_all( 'email_templates' );
		foreach ( $email_templates as $template ) {
			if ( $template['format'] == 'HTML' ) {
				$msg = nl2br( $template['message'] );
				$wpdb->update(
					$this->hbdb->email_templates_table,
					array( 'message' => $msg ),
					array( 'id' => $template['id'] )
				);
			}
		}
	}

	private function update_options_v_1_8() {
		$security_bond_option = get_option( 'hb_security_bond_type' );
		if ( $security_bond_option ) {
			delete_option( 'hb_security_bond_type' );
			if ( $security_bond_option == 'none' ) {
				update_option( 'hb_security_bond', 'no' );
			} else {
				update_option( 'hb_security_bond', 'yes' );
			}
		}
	}

	public function alter_data_v_1_8_7() {
		global $wpdb;
		$wpdb->query(
			"
			UPDATE {$this->hbdb->fees_table}
			SET include_in_price = 1
			WHERE global = 0
			"
		);
	}

	private function update_options_v_1_8_7() {
		$deposit_type_option = get_option( 'hb_deposit_type' );
		if ( $deposit_type_option == 'one_night' ) {
			update_option( 'hb_deposit_type', 'nb_night' );
		}
	}

	public function alter_data_v_1_9() {
		global $wpdb;
		$wpdb->query(
			"
			UPDATE {$this->hbdb->resa_table}
			SET accom_price = -1
			"
		);
		$wpdb->query(
			"
			UPDATE {$this->hbdb->discounts_table}
			SET apply_to_type = 'accom'
			"
		);
		$wpdb->query(
			"
			UPDATE {$this->hbdb->email_templates_table}
			SET all_accom = 1
			"
		);
		$accom_ids = $this->hbdb->get_all_accom_ids();
		$emails = $wpdb->get_results(
			"
			SELECT id
			FROM {$this->hbdb->email_templates_table}
			"
			, ARRAY_A
		);
		foreach ( $accom_ids as $accom_id ) {
			foreach ( $emails as $email ) {
				$wpdb->insert(
					$this->hbdb->email_templates_accom_table,
					array(
						'email_template_id' => $email['id'],
						'accom_id' => $accom_id,
					)
				);
			}
		}
		$this->insert_document_template();
	}

	public function alter_data_v_1_9_1() {
		global $wpdb;
		$wpdb->query(
			"
			UPDATE {$this->hbdb->resa_table}
			SET coupon = ''
			WHERE coupon = '[]'
			"
		);
		$resa_with_coupon = $wpdb->get_results(
			"
			SELECT id, coupon
			FROM {$this->hbdb->resa_table}
			WHERE coupon != ''
			", ARRAY_A
		);
		foreach ( $resa_with_coupon as $resa ) {
			$coupon_info = json_decode( $resa['coupon'], true );
			if ( isset( $coupon_info['code'] ) && isset( $coupon_info['value'] ) ) {
				$wpdb->update(
					$this->hbdb->resa_table,
					array(
						'coupon' => $coupon_info['code'],
						'coupon_value' => $coupon_info['value'],
					),
					array(
						'id' => $resa['id']
					)
				);
			}
		}
	}

	public function alter_data_v_2_0_3() {
		global $wpdb;
		$email_templates = $this->hbdb->get_all( 'email_templates' );
		foreach ( $email_templates as $template ) {
			if ( ! $template['resa_payment_status'] ) {
				$wpdb->update(
					$this->hbdb->email_templates_table,
					array( 'resa_payment_status' => 'paid,not_fully_paid,bond_not_paid,unpaid' ),
					array( 'id' => $template['id'] )
				);
			}
		}
	}

	public function alter_data_v_2_0_4() {
		global $wpdb;
		$email_templates = $this->hbdb->get_all( 'email_templates' );
		foreach ( $email_templates as $template ) {
			if ( ! $template['sending_type'] ) {
				$wpdb->update(
					$this->hbdb->email_templates_table,
					array( 'sending_type' => 'event' ),
					array( 'id' => $template['id'] )
				);
			}
		}
	}

	public function alter_data_v_2_0_6() {
		global $wpdb;
		$roles_caps = $this->utils->get_hbook_roles_caps();
		$admin_role = get_role( 'administrator' );
		$hb_caps = $roles_caps['hb_hbook_manager']['caps'];
		foreach ( $hb_caps as $key => $cap ) {
			$admin_role->add_cap( $cap );
		}
	}


	public function insert_data() {
		$this->insert_fields();
		$this->insert_email_templates();
		$this->insert_document_template();
	}

	private function insert_fields() {
		global $wpdb;
		$query = "SELECT COUNT(*) FROM {$this->hbdb->fields_table}";
		$nb_fields = $wpdb->get_var( $query );
		if ( $nb_fields == 0 ) {
			$query = "
				INSERT INTO {$this->hbdb->fields_table}
				(id, name, standard, displayed, required, type, order_num, data_about)
				VALUES
				('details_form_title', 'Form title', 1, 1, 0, 'title', 1, 'customer'),
				('first_name', 'First name', 1, 1, 1, 'text', 2, 'customer'),
				('last_name', 'Last name', 1, 1, 1, 'text', 3, 'customer'),
				('email', 'Email', 1, 1, 1, 'email', 4, 'customer'),
				('phone', 'Phone', 0, 1, 0, 'text', 5, 'customer'),
				('address_1', 'Address line 1', 0, 1, 0, 'text', 6, 'customer'),
				('address_2', 'Address line 2', 0, 1, 0, 'text', 7, 'customer'),
				('city', 'City', 0, 1, 0, 'text', 8, 'customer'),
				('state_province', 'State / province', 0, 1, 0, 'text', 9, 'customer'),
				('zip_code', 'Zip code', 0, 1, 0, 'text', 10, 'customer'),
				('country', 'Country', 0, 1, 0, 'text', 11, 'customer')
				";
			$wpdb->query( $query );
		}
	}

	private function insert_email_templates() {
		global $wpdb;
		$query = "SELECT COUNT(*) FROM {$this->hbdb->email_templates_table}";
		$nb_email_templates = $wpdb->get_var( $query );
		if ( $nb_email_templates == 0 ) {
			$email_templates = array(
				array(
					'name' => esc_html__( 'Admin notification', 'hbook-admin' ),
					'to_address' => '',
					'from_address' => '',
					'reply_to_address' => '',
					'subject' => esc_html__( 'New reservation', 'hbook-admin' ),
					'message' =>
						esc_html__( 'New reservation:', 'hbook-admin' ) . "\n\n" .
						esc_html__( '- Customer first name:', 'hbook-admin' ) . ' [customer_first_name]' . "\n" .
						esc_html__( '- Customer last name:', 'hbook-admin' ) . ' [customer_last_name]' . "\n" .
						esc_html__( '- Customer email:', 'hbook-admin' ) . ' [customer_email]' . "\n" .
						esc_html__( '- Check-in date:', 'hbook-admin' ) . ' [resa_check_in]' . "\n" .
						esc_html__( '- Check-out date:', 'hbook-admin' ) . ' [resa_check_out]' . "\n" .
						esc_html__( '- Number of adults:', 'hbook-admin' ) . ' [resa_adults]' . "\n" .
						esc_html__( '- Number of children:', 'hbook-admin' ) . ' [resa_children]' . "\n" .
						esc_html__( '- Accommodation:', 'hbook-admin' ) . ' [resa_accommodation]' . "\n" .
						esc_html__( '- Price:', 'hbook-admin' ) . ' [resa_price]',
					'format' => 'TEXT',
					'lang' => 'all',
					'action' => 'new_resa',
					'resa_payment_status' => 'paid,not_fully_paid,bond_not_paid,unpaid',
					'all_accom' => 1,
				),
				array(
					'name' => esc_html__( 'Customer notification', 'hbook-admin' ),
					'to_address' => '[customer_email]',
					'reply_to_address' => '',
					'from_address' => '',
					'subject' => esc_html__( 'Your reservation', 'hbook-admin' ),
					'message' =>
						esc_html__( 'Hello', 'hbook-admin' ) . ' [customer_first_name],' . "\n\n" .
						esc_html__( 'Thank you for choosing to stay with us! We are pleased to confirm your reservation as follows:', 'hbook-admin' ) . "\n\n" .
						esc_html__( 'Check-in date:', 'hbook-admin' ) . ' [resa_check_in]' . "\n" .
						esc_html__( 'Check-out date:', 'hbook-admin' ) . ' [resa_check_out]' . "\n" .
						esc_html__( 'Number of adults:', 'hbook-admin' ) . ' [resa_adults]' . "\n" .
						esc_html__( 'Number of children:', 'hbook-admin' ) . ' [resa_children]' . "\n" .
						esc_html__( 'Accommodation:', 'hbook-admin' ) . ' [resa_accommodation]' . "\n" .
						esc_html__( 'Price:', 'hbook-admin' ) . ' [resa_price]' . "\n\n" .
						esc_html__( 'See you soon!', 'hbook-admin' ),
					'format' => 'TEXT',
					'lang' => 'all',
					'action' => 'new_resa',
					'resa_payment_status' => 'paid,not_fully_paid,bond_not_paid,unpaid',
					'all_accom' => 1,
				),
				array(
					'name' => esc_html__( 'Reservation confirmation', 'hbook-admin' ),
					'to_address' => '[customer_email]',
					'reply_to_address' => '',
					'from_address' => '',
					'subject' => esc_html__( 'Your reservation', 'hbook-admin' ),
					'message' =>
						esc_html__( 'Hello', 'hbook-admin' ) . ' [customer_first_name],' . "\n\n" .
						esc_html__( 'Thank you for choosing to stay with us! We are pleased to confirm your reservation as follows:', 'hbook-admin' ) . "\n\n" .
						esc_html__( 'Check-in date:', 'hbook-admin' ) . ' [resa_check_in]' . "\n" .
						esc_html__( 'Check-out date:', 'hbook-admin' ) . ' [resa_check_out]' . "\n" .
						esc_html__( 'Number of adults:', 'hbook-admin' ) . ' [resa_adults]' . "\n" .
						esc_html__( 'Number of children:', 'hbook-admin' ) . ' [resa_children]' . "\n" .
						esc_html__( 'Accommodation:', 'hbook-admin' ) . ' [resa_accommodation]' . "\n" .
						esc_html__( 'Price:', 'hbook-admin' ) . ' [resa_price]' . "\n\n" .
						esc_html__( 'See you soon!', 'hbook-admin' ),
					'format' => 'TEXT',
					'lang' => 'all',
					'action' => 'confirmation_resa',
					'resa_payment_status' => 'paid,not_fully_paid,bond_not_paid,unpaid',
					'all_accom' => 1,
				)
			);

			foreach ( $email_templates as $email_tmpl ) {
				$values = "'" . implode( "', '", $email_tmpl ) . "'";
				$query =
					"
					INSERT INTO {$this->hbdb->email_templates_table}
					(name, to_address, reply_to_address, from_address, subject, message, format, lang, action, resa_payment_status, all_accom)
					VALUES
					( $values )
					";
				$wpdb->query( $query );
			}
		}
	}

	private function insert_document_template() {
		global $wpdb;
		$template = array(
			'name' => 'Invoice',
			'content' =>
"<html>\n" .
"<head>\n" .
"\n" .
"<title>Invoice - [resa_id]</title>\n" .
"\n" .
"<style type=\"text/css\">\n" .
"html, body, h1, h2, h3, h4, h5, h6 {\n" .
"margin: 0;\n" .
"padding: 0;\n" .
"}\n" .
"body {\n" .
"font-family: sans-serif;\n" .
"font-size: 14px;\n" .
"line-height: 26px;\n" .
"padding: 42px;\n" .
"}\n" .
"h1 {\n" .
"font-size: 48px;\n" .
"font-weight: bold;\n" .
"line-height: 52px;\n" .
"margin: 28px 0 22px;\n" .
"}\n" .
"h2 {\n" .
"font-size: 36px;\n" .
"line-height: 39px;\n" .
"margin: 26px 0;\n" .
"}\n" .
"h3 {\n" .
"font-size: 18px;\n" .
"margin: 26px 0 13px;\n" .
"}\n" .
"h4, h5, h6 {\n" .
"font-size: 16px;\n" .
"line-height: 26px;\n" .
"}\n" .
"p, h4, h5, h6 {\n" .
"margin: 0 0 26px;\n" .
"}\n" .
"#invoice-meta {\n" .
"position: absolute;\n" .
"right: 42px;\n" .
"text-align: right;\n" .
"top: 42px;\n" .
"width: 250px;\n" .
"}\n" .
"h1 {\n" .
"text-transform: uppercase;\n" .
"}\n" .
"#invoice-meta span {\n" .
"float: left;\n" .
"}\n" .
"footer {\n" .
"font-size: 12px;\n" .
"margin-top: 61px;\n" .
"text-align: center;\n" .
"}\n" .
"</style>\n" .
"\n" .
"</head>\n" .
"\n" .
"<body>\n" .
"\n" .
"<div id=\"invoice-meta\">\n" .
"<h1>Invoice</h1>\n" .
"<span>Invoice Number:</span>[resa_id]<br>\n" .
"<span>Invoice date:</span>[resa_received_on_date]\n" .
"</div>\n" .
"\n" .
"<h2>Hotel name</h2>\n" .
"<p>\n" .
"Address: your address here<br/>\n" .
"Phone number: your phone number here<br/>\n" .
"Email: your email here\n" .
"</p>\n" .
"\n" .
"<h3>Bill to:</h3>\n" .
"<p>\n" .
"[customer_first_name] [customer_last_name]<br/>\n" .
"[customer_address_1] [customer_address_2]<br/>\n" .
"[customer_city] [customer_zip_code]<br/>\n" .
"[customer_country]\n" .
"</p>\n" .
"\n" .
"<p>\n" .
"Arrival date: [resa_check_in]<br/>\n" .
"Departure date: [resa_check_out]\n" .
"</p>\n" .
"\n" .
"[resa_invoice_table]\n" .
"\n" .
"<footer>Hotel name, Address, Country</footer>\n" .
"\n" .
"</body>\n" .
"</html>\n"
		);
		$wpdb->insert( $this->hbdb->document_templates_table, $template );
	}

	public function insert_strings( $installed_version ) {
		foreach ( $this->versions as $version ) {
			if ( version_compare( $version, $installed_version ) > 0 ) {
				$insert_strings_method = 'insert_strings_v_' . str_replace( '.', '_', $version );
				if ( method_exists( $this, $insert_strings_method ) ) {
					$this->$insert_strings_method();
				}
			}
		}
	}

	private function insert_strings_v_1_0() {
		global $wpdb;
		$query =
			"
			INSERT INTO {$this->hbdb->strings_table}
			(id, locale, value)
			VALUES
			('accom_available_at_chosen_dates', 'en_US', 'The %accom_name is available at the chosen dates.'),
			('accom_book_now_button', 'en_US', 'Book now!'),
			('accom_can_not_suit_nb_people', 'en_US', 'Unfortunately the %accom_name can not suit %persons_nb persons.'),
			('accom_deposit_amount', 'en_US', 'Deposit amount:'),
			('accom_not_available_at_chosen_dates', 'en_US', 'The %accom_name is not available at the chosen dates.'),
			('accom_no_longer_available', 'en_US', 'Unfortunately the accommodation you have selected is no longer available. Please select another accommodation or start searching again.'),
			('accom_page_form_title', 'en_US', 'Check price and availability for the %accom_name'),
			('accom_starting_price', 'en_US', 'price starting at %price' ),
			('accom_starting_price_duration_unit', 'en_US', 'per night' ),
			('adults', 'en_US', 'Adults'),
			('book_now_button', 'en_US', 'Book now!'),
			('change_search_button', 'en_US', 'Change search'),
			('check_in', 'en_US', 'Check-in'),
			('check_in_date_before_date', 'en_US', 'The check-in date can not be before the %date.'),
			('check_in_date_past', 'en_US', 'The check-in date can not be in the past.'),
			('check_out', 'en_US', 'Check-out'),
			('check_out_before_check_in', 'en_US', 'The check-out date must be after the check-in date.'),
			('check_out_date_after_date', 'en_US', 'The check-out date can not be after the %date.'),
			('children', 'en_US', 'Children'),
			('chosen_adults', 'en_US', 'Adults:'),
			('chosen_check_in', 'en_US', 'Check-in:'),
			('chosen_check_out', 'en_US', 'Check-out:'),
			('chosen_children', 'en_US', 'Children:'),
			('connection_error', 'en_US', 'There was a connection error. Please try again.'),
			('default_form_title', 'en_US', 'Search'),
			('details_form_title', 'en_US', 'Enter your details'),
			('hide_price_breakdown', 'en_US', 'Hide price breakdown'),
			('invalid_check_in_date', 'en_US', 'The check-in date is not valid.'),
			('invalid_check_in_out_date', 'en_US', 'The check-in date and the check-out date are not valid.'),
			('invalid_check_out_date', 'en_US', 'The check-out date is not valid.'),
			('invalid_email', 'en_US', 'Invalid email.'),
			('invalid_number', 'en_US', 'This field can only contain numbers.'),
			('legend_occupied', 'en_US', 'Occupied'),
			('no_accom_at_chosen_dates', 'en_US', 'Unfortunately we could not find accommodation for the dates you entered.'),
			('no_accom_can_suit_nb_people', 'en_US', 'Unfortunately we could not find accommodation to suit %persons_nb persons.'),
			('no_check_in_date', 'en_US', 'Please enter a check-in date.'),
			('no_check_in_out_date', 'en_US', 'Please enter a check-in date and a check-out date.'),
			('no_check_out_date', 'en_US', 'Please enter a check-out date.'),
			('number_of_nights', 'en_US', 'Number of nights:'),
			('one_type_of_accommodation_found', 'en_US', 'We have found 1 type of accommodation that suit your needs.'),
			('external_payment_txt_deposit', 'en_US', ' (deposit)'),
			('external_payment_txt_desc', 'en_US', '%accom_name%deposit_txt - %nights_txt (from %check_in_date to %check_out_date) - %adults_txt%children_txt'),
			('external_payment_txt_one_adult', 'en_US', '1 adult'),
			('external_payment_txt_one_child', 'en_US', ' - 1 child'),
			('external_payment_txt_one_night', 'en_US', '1 night'),
			('external_payment_txt_several_adults', 'en_US', '%nb_adults adults'),
			('external_payment_txt_several_children', 'en_US', ' - %nb_children children'),
			('external_payment_txt_several_nights', 'en_US', '%nb_nights nights'),
			('price_breakdown_accom_price', 'en_US', 'Accommodation price:'),
			('price_breakdown_adults_several', 'en_US', 'Adults (%nb_adults):'),
			('price_breakdown_adult_one', 'en_US', 'Adult:'),
			('price_breakdown_children_several', 'en_US', 'Children (%nb_children):'),
			('price_breakdown_child_one', 'en_US', 'Child:'),
			('price_breakdown_dates', 'en_US', 'From %from_date to %to_date'),
			('price_breakdown_extra_adults_several', 'en_US', 'Extra adults (%nb_adults):'),
			('price_breakdown_extra_adult_one', 'en_US', 'Extra adult:'),
			('price_breakdown_extra_children_several', 'en_US', 'Extra children (%nb_children):'),
			('price_breakdown_extra_child_one', 'en_US', 'Extra child:'),
			('price_for_1_night', 'en_US', 'price for 1 night'),
			('price_for_several_nights', 'en_US', 'price for %nb_nights nights'),
			('required_field', 'en_US', 'Required field.'),
			('searching', 'en_US', 'Searching...'),
			('search_button', 'en_US', 'Search'),
			('selected_accom', 'en_US', 'This accommodation is selected.'),
			('select_accom_button', 'en_US', 'Select this accommodation'),
			('select_accom_title', 'en_US', 'Select your accommodation'),
			('several_types_of_accommodation_found', 'en_US', 'We have found %nb_types types of accommodation that suit your needs.'),
			('summary_accommodation', 'en_US', 'Accommodation:'),
			('summary_deposit', 'en_US', 'Deposit amount:'),
			('summary_price', 'en_US', 'Total price:'),
			('summary_title', 'en_US', 'Reservation summary'),
			('table_rates_from', 'en_US', 'From'),
			('table_rates_nights', 'en_US', 'Nights'),
			('table_rates_all_nights', 'en_US', 'All nights'),
			('table_rates_price', 'en_US', 'Price'),
			('table_rates_to', 'en_US', 'To'),
			('thanks_message_1', 'en_US', 'Thanks for your reservation! We have just sent you a confirmation email at %customer_email with the following details:'),
			('thanks_message_2', 'en_US', 'See you soon!'),
			('timeout_error', 'en_US', 'Your session has timed out. Please start a new booking request.'),
			('view_accom_at_chosen_date', 'en_US', 'View all available accommodation at the chosen dates.'),
			('view_accom_button', 'en_US', 'View this accommodation'),
			('view_accom_for_persons', 'en_US', 'View all available accommodation for %persons_nb persons.'),
			('view_price_breakdown', 'en_US', 'View price breakdown'),

			('payment_type_explanation_deposit', 'en_US', 'You will be charged %deposit_amount'),
			('payment_type_explanation_full', 'en_US', 'You will be charged %full_amount'),

			('stripe_text_before_form', 'en_US', 'Please enter your credit cards details below.'),
			('payment_type_explanation_offline', 'en_US', 'You will pay for your stay at a later stage. The total price is %full_amount'),
			('payment_type_explanation_store_credit_card', 'en_US', 'We ask for your credit card details but we will not charge it now.')
			";
		$wpdb->query( $query );
	}

	private function insert_strings_v_1_2() {
		global $wpdb;
		$query =
			"
			INSERT INTO {$this->hbdb->strings_table}
			(id, locale, value)
			VALUES

			('check_in_day_not_allowed', 'en_US', 'Check-in is allowed only on specific days (%check_in_days).'),
			('check_out_day_not_allowed', 'en_US', 'Check-out is allowed only on specific days (%check_out_days).'),
			('minimum_stay', 'en_US', 'A %nb_nights-nights minimum stay policy applies. Please modify your dates.'),
			('maximum_stay', 'en_US', 'A %nb_nights-nights maximum stay policy applies. Please modify your dates.'),
			('check_out_day_not_allowed_for_check_in_day', 'en_US', 'Guests check-in on %check_in_day must check-out on specific days (%check_out_days).'),
			('minimum_stay_for_check_in_day', 'en_US', 'A %nb_nights-night minimum stay is required for booking starting on %check_in_day.'),
			('maximum_stay_for_check_in_day', 'en_US', 'A %nb_nights-night maximum stay applies for booking starting on %check_in_day.'),
			('table_rates_per_night', 'en_US', 'per night'),
			('table_rates_for_night_stay', 'en_US', 'for a %nb_nights-night stay'),
			('price_breakdown_discount', 'en_US', 'Discount:')
			";
		$wpdb->query( $query );
	}

	private function insert_strings_v_1_3() {
		global $wpdb;
		$query =
			"
			INSERT INTO {$this->hbdb->strings_table}
			(id, locale, value)
			VALUES

			('price_breakdown_fees', 'en_US', 'Fees:'),
			('price_breakdown_nights_several', 'en_US', 'nights'),
			('price_breakdown_night_one', 'en_US', 'night'),
			('price_breakdown_multiple_nights', 'en_US', 'x %nb_nights-night'),
			('fee_details_adults_several', 'en_US', 'adults'),
			('fee_details_adult_one', 'en_US', 'adult'),
			('fee_details_children_several', 'en_US', 'children'),
			('fee_details_child_one', 'en_US', 'child'),
			('no_adults', 'en_US', 'Please select the number of adults.'),
			('no_adults_children', 'en_US', 'Please select the number of adults and the number of children.'),
			('no_children', 'en_US', 'Please select the number of children.'),
			('select_options_title', 'en_US', 'Select your options'),
			('free_option', 'en_US', '(free)'),
			('each_option', 'en_US', 'each'),
			('summary_accom_price', 'en_US', 'Accommodation price:'),
			('total_options_price', 'en_US', 'Options total price:'),
			('summary_options_price', 'en_US', 'Options price:')
			";
		$wpdb->query( $query );
	}

	private function insert_strings_v_1_4() {
		global $wpdb;
		$query =
			"
			INSERT INTO {$this->hbdb->strings_table}
			(id, locale, value)
			VALUES

			('payment_type', 'en_US', 'Select your payment type:'),
			('payment_type_offline', 'en_US', 'On arrival or offline payment'),
			('payment_type_deposit', 'en_US', 'Pay deposit now'),
			('payment_type_full', 'en_US', 'Pay total price now')
			";
		$wpdb->query( $query );
	}

	private function insert_strings_v_1_5() {
		global $wpdb;
		$query =
			"
			INSERT INTO {$this->hbdb->strings_table}
			(id, locale, value)
			VALUES
			('txt_before_book_now_button', 'en_US', 'Please double check your reservation details before clicking on \"Book now\".')
			";
		$wpdb->query( $query );
	}

	private function insert_strings_v_1_5_1() {
		global $wpdb;
		$query =
			"
			INSERT INTO {$this->hbdb->strings_table}
			(id, locale, value)
			VALUES
			('chosen_options', 'en_US', 'Extra services:'),
			('price_option', 'en_US', '(%price%each%max)')
			";
		$wpdb->query( $query );
	}

	private function insert_strings_v_1_5_3() {
		global $wpdb;
		$query =
			"
			INSERT INTO {$this->hbdb->strings_table}
			(id, locale, value)
			VALUES
			('legend_past', 'en_US', 'Past' ),
			('legend_closed', 'en_US', 'Closed' ),
			('legend_available', 'en_US', 'Available' ),
			('legend_before_check_in', 'en_US', 'Before check-in day' ),
			('legend_no_check_in', 'en_US', 'Not available for check-in' ),
			('legend_no_check_out', 'en_US', 'Not available for check-out' ),
			('legend_check_in_only', 'en_US', 'Available for check-in only' ),
			('legend_check_out_only', 'en_US', 'Available for check-out only' ),
			('legend_no_check_out_min_stay', 'en_US', 'Not available for check-out (due to the %nb_nights-night minimum-stay requirement)' ),
			('legend_no_check_out_max_stay', 'en_US', 'Not available for check-out (due to the %nb_nights-night maximum-stay requirement)' ),
			('legend_check_in', 'en_US', 'Chosen check-in day' ),
			('legend_check_out', 'en_US', 'Chosen check-out day' ),
			('legend_select_check_in', 'en_US', 'Select a check-in date.'),
			('legend_select_check_out', 'en_US', 'Select a check-out date.')
			";
		$wpdb->query( $query );
	}

	private function insert_strings_v_1_6() {
		global $wpdb;
		$query =
			"
			INSERT INTO {$this->hbdb->strings_table}
			(id, locale, value)
			VALUES
			('processing', 'en_US', 'Processing...'),
			('payment_method', 'en_US', 'Select your payment method:' ),
			('stripe_card_number', 'en_US', 'Card number:' ),
			('stripe_expiration', 'en_US', 'Expiration date:' ),
			('stripe_cvc', 'en_US', 'CVC:' ),
			('stripe_payment_method_label', 'en_US', 'Credit or debit card' ),
			('paypal_payment_method_label', 'en_US', 'PayPal' ),
			('paypal_text_before_form', 'en_US', 'Click on Book Now to confirm your reservation. You will be redirected to PayPal to complete your payment.' ),
			('terms_and_cond_title', 'en_US', 'Accept our policies for this reservation' ),
			('terms_and_cond_text', 'en_US', 'I have read and I accept the terms and conditions for this booking.' ),
			('terms_and_cond_error', 'en_US', 'Please confirm you have read and you accept the terms and conditions by checking the box.' )
			";
		$wpdb->query( $query );
	}

	private function insert_strings_v_1_6_2() {
		global $wpdb;
		$query =
			"
			INSERT INTO {$this->hbdb->strings_table}
			(id, locale, value)
			VALUES
			('check_in_day_not_allowed_seasonal', 'en_US', 'Check-in is allowed only on certain days. Please change your check-in date.'),
			('check_out_day_not_allowed_seasonal', 'en_US', 'Check-out is allowed only on certain days. Please change your check-out date.'),
			('minimum_stay_seasonal', 'en_US', 'A minimum stay policy applies. Please modify your dates to extend your stay.'),
			('maximum_stay_seasonal', 'en_US', 'A maximum stay policy applies. Please modify your dates to shorten your stay.'),
			('check_out_day_not_allowed_for_check_in_day_seasonal', 'en_US', 'With this check-in date check-out is allowed only on certain days. Please change your check-in or check-out dates.'),
			('minimum_stay_for_check_in_day_seasonal', 'en_US', 'With this check-in date a minimum stay policy applies.'),
			('maximum_stay_for_check_in_day_seasonal', 'en_US', 'With this check-in date a maximum stay policy applies.')
			";
		$wpdb->query( $query );
	}

	private function insert_strings_v_1_6_3() {
		global $wpdb;
		$query =
			"
			INSERT INTO {$this->hbdb->strings_table}
			(id, locale, value)
			VALUES
			('payment_section_title', 'en_US', 'Payment')
			";
		$wpdb->query( $query );
	}

	private function insert_strings_v_1_7() {
		global $wpdb;
		$query =
			"
			INSERT INTO {$this->hbdb->strings_table}
			(id, locale, value)
			VALUES
			('max_option', 'en_US', ' - Maximum: %max_value'),
			('thanks_message_payment_done_1', 'en_US', 'Thanks for your reservation! We confirm that we have received your payment and have just sent you a confirmation email at %customer_email with the following details:'),
			('thanks_message_payment_done_2', 'en_US', 'See you soon!'),
			('error_season_not_defined', 'en_US', 'Error: season is not defined for %night. Please add a season which starts before %night and ends after %night.'),
			('error_rate_not_defined', 'en_US', 'Error: please add a rate for accommodation named %accom_name and season named %season_name.'),
			('payment_type_store_credit_card', 'en_US', 'Leave credit card details (the card will not be charged now)'),
			('stripe_processing_error', 'en_US', 'Could not process the payment (%error_msg).' )
			";
		$wpdb->query( $query );
	}

	private function insert_strings_v_1_7_2() {
		global $wpdb;
		$query =
			"
			INSERT INTO {$this->hbdb->strings_table}
			(id, locale, value)
			VALUES
			('accom_can_not_suit_one_person', 'en_US', 'Unfortunately the %accom_name can not suit one person only.' ),
			('no_accom_can_suit_one_person', 'en_US', 'Unfortunately we could not find any accommodation that would suit one person only.' ),
			('view_accom_for_one_person', 'en_US', 'View all available accommodation for one person.' )
			";
		$wpdb->query( $query );
	}

	private function insert_strings_v_1_7_6() {
		global $wpdb;
		$query =
			"
			INSERT INTO {$this->hbdb->strings_table}
			(id, locale, value)
			VALUES
			('summary_security_bond', 'en_US', 'Security bond amount:' ),
			('summary_security_bond_explanation', 'en_US', 'This will be refunded after your stay minus possible damage costs.' )
			";
		$wpdb->query( $query );
	}

	private function insert_strings_v_1_7_7() {
		global $wpdb;
		$query =
			"
			INSERT INTO {$this->hbdb->strings_table}
			(id, locale, value)
			VALUES
			('summary_coupon_amount', 'en_US', 'Coupon amount:' ),
			('coupons_section_title', 'en_US', 'Coupon' ),
			('coupons_text', 'en_US', 'If you have a coupon code enter it in the field below and click on apply.' ),
			('coupons_button', 'en_US', 'Apply' ),
			('valid_coupon', 'en_US', 'Your coupon code is valid! We have applied a %amount discount!' ),
			('invalid_coupon', 'en_US', 'This coupon code is not valid.' ),
			('no_coupon', 'en_US', 'Please enter a coupon code.' )
			";
		$wpdb->query( $query );
	}

	private function insert_strings_v_1_8_3() {
		global $wpdb;
		$query =
			"
			INSERT INTO {$this->hbdb->strings_table}
			(id, locale, value)
			VALUES
			('table_rates_season', 'en_US', 'Season' ),
			('privacy_policy_text', 'en_US', 'I have read and I accept the privacy policy and I give my consent for the information above to be collected.' ),
			('privacy_policy_error', 'en_US', 'Please confirm you have read and you accept the privacy policy and you give your consent for the information above to be collected by checking the box.' )
			";
		$wpdb->query( $query );
	}

	private function insert_strings_v_1_8_6() {
		global $wpdb;
		$query =
			"
			INSERT INTO {$this->hbdb->strings_table}
			(id, locale, value)
			VALUES
			('stripe_text_bottom_form', 'en_US', 'Safe and secure payments SSL encrypted'),
			('paypal_bottom_text_line_1', 'en_US', 'Secured payments by'),
			('paypal_bottom_text_line_2', 'en_US', 'You can pay with your credit card')
			";
		$wpdb->query( $query );
	}

	private function insert_strings_v_1_8_7() {
		global $wpdb;
		$query =
			"
			INSERT INTO {$this->hbdb->strings_table}
			(id, locale, value)
			VALUES
			('fee_details_persons', 'en_US', 'persons')
			";
		$wpdb->query( $query );
	}

	private function insert_strings_v_1_9() {
		global $wpdb;
		$query =
			"
			INSERT INTO {$this->hbdb->strings_table}
			(id, locale, value)
			VALUES
			('select_accom_num_title', 'en_US', 'Select your accommodation number'),
			('select_accom_num_label', 'en_US', '%accom_name number %accom_num'),
			('accom_num_no_longer_available', 'en_US', 'The %accom_name number %accom_num is no longer available. Please select another accommodation number.'),
			('coupon_no_longer_valid', 'en_US', 'This coupon code is no longer valid.'),
			('summary_discount_amount', 'en_US', 'Discount amount:' ),
			('summary_included_fees', 'en_US', 'Price includes:'),
			('table_invoice_head_description', 'en_US', 'Description'),
			('table_invoice_head_amount', 'en_US', 'Amount'),
			('table_invoice_accom_title', 'en_US', 'Accommodation'),
			('table_invoice_discount', 'en_US', 'Discount'),
			('table_invoice_surcharge', 'en_US', 'Surcharge'),
			('table_invoice_accom_subtotal', 'en_US', 'Accommodation subtotal'),
			('table_invoice_accom_total', 'en_US', 'Accommodation total'),
			('table_invoice_extras_title', 'en_US', 'Extra services'),
			('table_invoice_extras_subtotal', 'en_US', 'Extra services subtotal'),
			('table_invoice_extras_total', 'en_US', 'Extra services total'),
			('table_invoice_coupon', 'en_US', 'Coupon'),
			('table_invoice_subtotal', 'en_US', 'Subtotal'),
			('table_invoice_total', 'en_US', 'Total'),
			('table_invoice_included_fee', 'en_US', 'Including')
			";
		$wpdb->query( $query );
	}

	private function insert_strings_v_2_0() {
		global $wpdb;
		$query =
			"
			INSERT INTO {$this->hbdb->strings_table}
			(id, locale, value)
			VALUES
			('multi_accom_intro', 'en_US', 'We have found the following accommodation.'),
			('accom_suggestion_for_single_accom_search', 'en_US', 'More than one accommodation is needed to accommodate your stay. Please check our suggestion below.'),
			('accom_suggestion_for_multiple_accom_search', 'en_US', 'We could not find any result matching your search criteria. You can change your search or check our suggestion below.'),
			('multi_accom_select_accom_n', 'en_US', 'Select accommodation %n'),
			('multi_accom_accom_n', 'en_US', 'Accommodation %n'),
			('multi_accom_no_accom_selected', 'en_US', 'Please select an accommodation before continuing.'),
			('search_specific_accom_number_link', 'en_US', 'Search for a specific number of accommodation'),
			('chosen_accom_number', 'en_US', 'Number of accommodation:'),
			('one_adult_chosen_in_accom_number', 'en_US', '(1 adult)' ),
			('chosen_adults_in_accom_number', 'en_US', '(%nb_adults adults)' ),
			('chosen_persons_in_accom_number', 'en_US', '(%nb_persons persons)' ),
			('accom_number', 'en_US', 'Number of accommodation' ),
			('accom_number_any', 'en_US', 'Any' ),
			('accom_has_no_extras', 'en_US', 'This accommodation does not have any extra services.' ),
			('summary_loading', 'en_US', 'Loading summary...' ),
			('next_step_button', 'en_US', 'Next %arrow' ),
			('previous_step_button', 'en_US', '%arrow Previous' ),
			('summary_accom_total_price', 'en_US', 'Price:' ),
			('view_accom_details_button', 'en_US', 'Check details' ),
			('global_options_title', 'en_US', 'Global options' ),
			('summary_global_options_price', 'en_US', 'Global options price:'),
			('table_invoice_global_extras_title', 'en_US', 'Global extra services'),
			('table_invoice_global_extras_total', 'en_US', 'Global extra services total'),
			('table_invoice_accom_extras_total', 'en_US', '%accom_name total'),
			('select_accom_num_select_title', 'en_US', 'Select accommodation number')
			";
		$wpdb->query( $query );
	}

	private function insert_strings_v_2_0_5() {
		global $wpdb;
		$query =
			"
			INSERT INTO {$this->hbdb->strings_table}
			(id, locale, value)
			VALUES
			('only_x_accom', 'en_US', 'We only have %available_accom_nb %accom_name.'),
			('only_x_accom_available_at_chosen_dates', 'en_US', 'There are only %available_accom_nb %accom_name available at the chosen dates.'),
			('not_enough_accom_for_people', 'en_US', 'We do not have enough %accom_name to suit %persons_nb persons.'),
			('not_enough_accom_for_people_at_chosen_dates', 'en_US', 'We do not have enough %accom_name to suit %persons_nb persons at the chosen dates.'),
			('accom_left', 'en_US', 'There are only %available_accom_nb %accom_name left!'),
			('one_accom_left', 'en_US', 'There is only one %accom_name left!'),
			('no_accom_left', 'en_US', 'There are no %accom_name left!'),
			('nb_accom_selected', 'en_US', 'You have already selected %selected_accom_nb %accom_name.')
			";
		$wpdb->query( $query );
	}

	private function insert_strings_v_2_0_6() {
		global $wpdb;
		$query =
			"
			INSERT INTO {$this->hbdb->strings_table}
			(id, locale, value)
			VALUES
			('accom_no_multiple_accom_booking', 'en_US', 'You can not book multiple %accom_name in one reservation.'),
			('summary_bottom_text', 'en_US', '')
			";
		$wpdb->query( $query );
	}
}