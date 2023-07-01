<?php
class HbDataBaseActions {

	public $db;
	public $prefix;

	public $posts_table;
	public $postmeta_table;
	public $rates_table;
	public $rates_rules_table;
	public $rates_accom_table;
	public $rates_seasons_table;
	public $customers_table;
	public $resa_table;
	public $seasons_table;
	public $seasons_dates_table;
	public $discounts_table;
	public $discounts_rules_table;
	public $discounts_accom_table;
	public $discounts_seasons_table;
	public $coupons_table;
	public $coupons_rules_table;
	public $coupons_accom_table;
	public $coupons_seasons_table;
	public $options_table;
	public $options_choices_table;
	public $options_accom_table;
	public $fees_table;
	public $fees_accom_table;
	public $email_templates_table;
	public $email_templates_accom_table;
	public $document_templates_table;
	public $fields_table;
	public $fields_choices_table;
	public $labels_translation_table;
	public $msg_translation_table;
	public $strings_table;
	public $booking_rules_table;
	public $booking_rules_accom_table;
	public $booking_rules_seasons_table;
	public $accom_num_name_table;
	public $ical_table;
	public $parents_resa_table;
	public $email_logs_table;

	public function __construct() {
		global $wpdb;
		$this->db = $wpdb;
		$this->prefix = $this->db->prefix . 'hb_';

		$this->posts_table = $this->db->prefix . 'posts';
		$this->postmeta_table = $this->db->prefix . 'postmeta';
		$this->resa_table = $this->prefix . 'resa';
		$this->customers_table = $this->prefix . 'customers';
		$this->rates_table = $this->prefix . 'rates';
		$this->rates_rules_table = $this->prefix . 'rates_rules';
		$this->rates_accom_table = $this->prefix . 'rates_accom';
		$this->rates_seasons_table = $this->prefix . 'rates_seasons';
		$this->seasons_table = $this->prefix . 'seasons';
		$this->seasons_dates_table = $this->prefix . 'seasons_dates';
		$this->discounts_table = $this->prefix . 'discounts';
		$this->discounts_rules_table = $this->prefix . 'discounts_rules';
		$this->discounts_accom_table = $this->prefix . 'discounts_accom';
		$this->discounts_seasons_table = $this->prefix . 'discounts_seasons';
		$this->coupons_table = $this->prefix . 'coupons';
		$this->coupons_rules_table = $this->prefix . 'coupons_rules';
		$this->coupons_accom_table = $this->prefix . 'coupons_accom';
		$this->coupons_seasons_table = $this->prefix . 'coupons_seasons';
		$this->options_table = $this->prefix . 'options';
		$this->options_choices_table = $this->prefix . 'options_choices';
		$this->options_accom_table = $this->prefix . 'options_accom';
		$this->fees_table = $this->prefix . 'fees';
		$this->fees_accom_table = $this->prefix . 'fees_accom';
		$this->email_templates_table = $this->prefix . 'email_templates';
		$this->email_templates_accom_table = $this->prefix . 'email_templates_accom';
		$this->document_templates_table = $this->prefix . 'document_templates';
		$this->fields_table = $this->prefix . 'fields';
		$this->fields_choices_table = $this->prefix . 'fields_choices';
		$this->strings_table = $this->prefix . 'strings';
		$this->booking_rules_table = $this->prefix . 'booking_rules';
		$this->booking_rules_accom_table = $this->prefix . 'booking_rules_accom';
		$this->booking_rules_seasons_table = $this->prefix . 'booking_rules_seasons';
		$this->accom_num_name_table = $this->prefix . 'accom_num_name';
		$this->accom_blocked_table = $this->prefix . 'accom_blocked';
		$this->ical_table = $this->prefix . 'ical';
		$this->sync_errors_table = $this->prefix . 'sync_errors';
		$this->parents_resa_table = $this->prefix . 'parents_resa';
		$this->email_logs_table = $this->prefix . 'email_logs';
	}

	private function table_name( $setting_type ) {
		switch( $setting_type ) {
			case 'rate' : return $this->rates_table;
			case 'season' : return $this->seasons_table;
			case 'season_date' : return $this->seasons_dates_table;
			case 'discount' : return $this->discounts_table;
			case 'coupon' : return $this->coupons_table;
			case 'option' : return $this->options_table;
			case 'option_choice' : return $this->options_choices_table;
			case 'fee' : return $this->fees_table;
			case 'rule' : return $this->booking_rules_table;
			case 'email_template' : return $this->email_templates_table;
			case 'document_template' : return $this->document_templates_table;
		}
	}

	private function accom_junction_table_name( $setting_type ) {
		switch( $setting_type ) {
			case 'discount' : return $this->discounts_accom_table;
			case 'coupon' : return $this->coupons_accom_table;
			case 'option' : return $this->options_accom_table;
			case 'fee' : return $this->fees_accom_table;
			case 'rule' : return $this->booking_rules_accom_table;
			case 'rate' : return $this->rates_accom_table;
			case 'email_template' : return $this->email_templates_accom_table;
		}
	}

	public function delete_plugin_tables() {
		$this->db->query( "DROP TABLE IF EXISTS $this->customers_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->fields_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->fields_choices_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->rates_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->rates_accom_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->rates_seasons_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->rates_rules_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->discounts_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->discounts_accom_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->discounts_rules_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->coupons_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->coupons_rules_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->coupons_accom_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->coupons_seasons_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->fees_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->fees_accom_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->options_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->options_accom_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->options_choices_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->resa_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->seasons_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->seasons_dates_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->strings_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->booking_rules_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->booking_rules_accom_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->accom_num_name_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->email_templates_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->email_templates_accom_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->document_templates_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->accom_blocked_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->ical_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->sync_errors_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->booking_rules_seasons_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->discounts_seasons_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->parents_resa_table" );
		$this->db->query( "DROP TABLE IF EXISTS $this->email_logs_table" );
		$old_rate_table = $this->rates_table . '_old';
		$this->db->query( "DROP TABLE IF EXISTS $old_rate_table" );
		$old_language_table = $this->prefix . 'languages';
		$this->db->query( "DROP TABLE IF EXISTS $old_language_table" );
	}

	public function get_single( $data_type, $id ) {

		$single = $this->db->get_results(
			$this->db->prepare(
				"
				SELECT *
				FROM $this->prefix$data_type
				WHERE id = %s
				",
				$id
			)
		, ARRAY_A);
		if ( ( $single == NULL ) || ( count( $single ) == 0 ) ) {
			return false;
		} else {
			return $single[0];
		}

	}

	public function delete_single( $data_type, $id ) {
		$this->db->delete( $this->prefix . $data_type, array( 'id' => $id ) );
	}

	public function get_all( $data_type ) {

		return $this->db->get_results(
			"
			SELECT *
			FROM $this->prefix$data_type
			"
		, ARRAY_A);

	}

	private function create_row( $setting ) {
		$default_columns = array();
		$boolean_columns = array();

		switch ( $setting['type'] ) {

			case 'email_template':
				$default_columns = array( 'name', 'to_address', 'reply_to_address', 'from_address', 'bcc_address', 'subject', 'message', 'format', 'media_attachments', 'lang', 'sending_type', 'action', 'schedules', 'resa_status', 'resa_payment_status' );
				$boolean_columns = array( 'multiple_accom', 'all_accom' );
				break;

			case 'document_template':
				$default_columns = array( 'name', 'content', 'lang' );
				break;

			case 'season' :
				$default_columns = array( 'name', 'priority' );
				break;

			case 'season_date':
				$default_columns = array( 'season_id', 'start_date', 'end_date', 'days' );
				break;

			case 'fee':
				$default_columns = array( 'name', 'amount', 'amount_children', 'apply_to_type', 'minimum_amount', 'maximum_amount', 'multiply_per', 'include_in_price' );
				$boolean_columns = array( 'all_accom', 'global', 'accom_price_per_person_per_night' );
				break;

			case 'option':
				$default_columns = array( 'name', 'amount', 'amount_children', 'link', 'apply_to_type', 'choice_type', 'quantity_max_option', 'quantity_max', 'quantity_max_child' );
				$boolean_columns = array( 'all_accom' );
				break;

			case 'option_choice':
				$default_columns = array( 'option_id', 'name', 'amount', 'amount_children' );
				break;

			case 'rule':
				$default_columns = array( 'rule_type', 'name', 'check_in_days', 'check_out_days', 'minimum_stay', 'maximum_stay', 'conditional_type' );
				$boolean_columns = array( 'all_accom', 'all_seasons' );
				break;

			case 'rate':
				$default_columns = array( 'rate_type', 'amount', 'nights' );
				$boolean_columns = array( 'all_accom', 'all_seasons' );
				if ( intval( $setting['nights'] ) == 0 ) {
					$setting['nights'] = 1;
				}
				break;

			case 'discount':
				$default_columns = array( 'apply_to_type', 'amount', 'amount_type' );
				$boolean_columns = array( 'all_accom', 'all_seasons' );
				break;

			case 'coupon':
				$default_columns = array( 'code', 'max_use_count', 'date_limit', 'multiple_use_per_customer', 'amount', 'amount_type' );
				$boolean_columns = array( 'all_accom', 'all_seasons' );
				break;

		}

		$row = array();
		$email_columns_with_tags = array( 'to_address', 'reply_to_address', 'from_address', 'message' );
		$document_columns_with_tags = array( 'content' );
		foreach ( $default_columns as $column ) {
			if ( $column == 'rate_type' || $column == 'rule_type' ) {
				$row['type'] = wp_strip_all_tags( stripslashes( $setting[ $column ] ) );
			} else {
				if (
					( ( $setting['type'] == 'email_template' ) && in_array( $column, $email_columns_with_tags ) ) ||
					( ( $setting['type'] == 'document_template' ) && in_array( $column, $document_columns_with_tags ) )
				) {
					$row[ $column ] = stripslashes( $setting[ $column ] );
				} else {
					$row[ $column ] = wp_strip_all_tags( stripslashes( $setting[ $column ] ) );
				}
			}
		}
		foreach ( $boolean_columns as $column ) {
			if ( $setting[ $column ] == 'true' ) {
				$row[ $column ] = 1;
			} else {
				$row[ $column ] = 0;
			}
		}
		return $row;
	}

	public function update_hb_setting( $action, $setting ) {
		$accom_junction_setting_types = array(
			'rate',
			'rule',
			'coupon',
			'discount',
			'option',
			'fee',
			'email_template',
		);
		if ( in_array( $setting['type'], $accom_junction_setting_types ) ) {
			if ( $this->db->delete( $this->accom_junction_table_name( $setting['type'] ), array( $setting['type'] . '_id' => $setting['id'] ) ) === false ) {
				return $this->db->last_query . $this->db->last_error;
			}
			if ( ( $setting['accom'] != '' ) && ( $action == 'update' ) ) {
				$accom = explode( ',', $setting['accom'] );
				foreach ( $accom as $a ) {
					if ( $this->db->insert( $this->accom_junction_table_name( $setting['type'] ), array( 'accom_id' => $a, $setting['type'] . '_id' => $setting['id'] ) ) === false ) {
						return $this->db->last_error;
					}
				}
			}
		}

		if ( $setting['type'] == 'rate' ) {
			$junctions = array( 'seasons', 'rules' );
			foreach( $junctions as $junction ) {
				$table = 'rates_' . $junction . '_table';
				if ( $this->db->delete( $this->$table, array( 'rate_id' => $setting['id'] ) ) === false ) {
					return $this->db->last_query . $this->db->last_error;
				}
				if ( $setting[ $junction ] != '' && $action == 'update' ) {
					$junction_setting = explode( ',', $setting[ $junction ] );
					foreach ( $junction_setting as $junction_id ) {
						if ( $this->db->insert( $this->$table, array( substr( $junction, 0, -1 ) . '_id' => $junction_id, 'rate_id' => $setting['id'] ) ) === false ) {
							return $this->db->last_error;
						}
					}
				}
			}
		}

		if ( $setting['type'] == 'rule' ) {
			if ( $this->db->delete( $this->booking_rules_seasons_table, array( 'rule_id' => $setting['id'] ) ) === false ) {
				return $this->db->last_query . $this->db->last_error;
			}
			if ( $setting['seasons'] != '' && $action == 'update' ) {
				$season_ids = explode( ',', $setting['seasons'] );
				foreach ( $season_ids as $season_id ) {
					if ( $this->db->insert( $this->booking_rules_seasons_table, array( 'season_id' => $season_id, 'rule_id' => $setting['id'] ) ) === false ) {
						return $this->db->last_error;
					}
				}
			}
			if ( trim( $setting['minimum_stay'] ) == '' ) {
				$setting['minimum_stay'] = -1;
			}
			if ( trim( $setting['maximum_stay'] ) == '' ) {
				$setting['maximum_stay'] = 9999;
			}

			if ( $action == 'update' ) {
				$current_rule = $this->db->get_row(
					$this->db->prepare(
						"
						SELECT *
						FROM $this->booking_rules_table
						WHERE id = %d
						", $setting['id']
					), ARRAY_A
				);
				if ( ( $current_rule['conditional_type'] == 'discount' ) && ( $setting['conditional_type'] != 'discount' ) ) {
					$this->db->delete( $this->discounts_rules_table, array( 'rule_id' => $setting['id'] ) );
				} else if ( ( $current_rule['conditional_type'] == 'coupon' ) && ( $setting['conditional_type'] != 'coupon' ) ) {
					$this->db->delete( $this->coupons_rules_table, array( 'rule_id' => $setting['id'] ) );
				} else if (
					(
						( $current_rule['conditional_type'] == 'special_rate' ) ||
						( $current_rule['conditional_type'] == 'comp_and_rate' )
					) && (
						( $setting['conditional_type'] != 'special_rate' ) &&
						( $setting['conditional_type'] != 'comp_and_rate' )
					)
				) {
					$this->db->delete( $this->rates_rules_table, array( 'rule_id' => $setting['id'] ) );
				}
			}
		}

		if ( $setting['type'] == 'discount' ) {
			if ( $this->db->delete( $this->discounts_rules_table, array( 'discount_id' => $setting['id'] ) ) === false ) {
				return $this->db->last_query . $this->db->last_error;
			}
			if ( $this->db->delete( $this->discounts_seasons_table, array( 'discount_id' => $setting['id'] ) ) === false ) {
				return $this->db->last_query . $this->db->last_error;
			}
			if ( $setting['rules'] != '' && $action == 'update' ) {
				$rules = explode( ',', $setting[ 'rules' ] );
				foreach ( $rules as $rule_id ) {
					if ( $this->db->insert( $this->discounts_rules_table, array( 'rule_id' => $rule_id, 'discount_id' => $setting['id'] ) ) === false ) {
						return $this->db->last_error;
					}
				}
			}
			if ( $setting['seasons'] != '' && $action == 'update' ) {
				$seasons = explode( ',', $setting[ 'seasons' ] );
				foreach ( $seasons as $season_id ) {
					if ( $this->db->insert( $this->discounts_seasons_table, array( 'season_id' => $season_id, 'discount_id' => $setting['id'] ) ) === false ) {
						return $this->db->last_error;
					}
				}
			}
		}

		if ( $setting['type'] == 'coupon' ) {
			if ( $this->db->delete( $this->coupons_rules_table, array( 'coupon_id' => $setting['id'] ) ) === false ) {
				return $this->db->last_query . $this->db->last_error;
			}
			if ( $this->db->delete( $this->coupons_seasons_table, array( 'coupon_id' => $setting['id'] ) ) === false ) {
				return $this->db->last_query . $this->db->last_error;
			}
			if ( $setting['rule'] != '' && $action == 'update' ) {
				if ( $this->db->insert( $this->coupons_rules_table, array( 'rule_id' => $setting['rule'], 'coupon_id' => $setting['id'] ) ) === false ) {
					return $this->db->last_error;
				}
			}
			if ( $setting['seasons'] != '' && $action == 'update' ) {
				$seasons = explode( ',', $setting[ 'seasons' ] );
				foreach ( $seasons as $season_id ) {
					if ( $this->db->insert( $this->coupons_seasons_table, array( 'season_id' => $season_id, 'coupon_id' => $setting['id'] ) ) === false ) {
						return $this->db->last_error;
					}
				}
			}
		}

		switch ( $action ) {

			case 'create':
				if ( $this->db->insert( $this->table_name( $setting['type'] ), $this->create_row( $setting ) ) !== false ) {
					$id = $this->db->insert_id;
					if ( $setting['type'] == 'season' ) {
						$rates_with_all_seasons = $this->db->get_results(
							"
							SELECT id
							FROM $this->rates_table
							WHERE all_seasons = 1
							"
						, ARRAY_A );
						foreach ( $rates_with_all_seasons as $rate ) {
							$this->db->insert( $this->rates_seasons_table, array( 'season_id' => $id, 'rate_id' => $rate['id'] ) );
						}
						$discounts_with_all_seasons = $this->db->get_results(
							"
							SELECT id
							FROM $this->discounts_table
							WHERE all_seasons = 1
							"
						, ARRAY_A );
						foreach ( $discounts_with_all_seasons as $discount ) {
							$this->db->insert( $this->discounts_seasons_table, array( 'season_id' => $id, 'discount_id' => $discount['id'] ) );
						}
						$coupons_with_all_seasons = $this->db->get_results(
							"
							SELECT id
							FROM $this->coupons_table
							WHERE all_seasons = 1
							"
						, ARRAY_A );
						foreach ( $coupons_with_all_seasons as $coupon ) {
							$this->db->insert( $this->coupons_seasons_table, array( 'season_id' => $id, 'coupon_id' => $coupon['id'] ) );
						}
						$rules_with_all_seasons = $this->db->get_results(
							"
							SELECT id
							FROM $this->booking_rules_table
							WHERE all_seasons = 1
							"
						, ARRAY_A );
						foreach ( $rules_with_all_seasons as $rule ) {
							$this->db->insert( $this->booking_rules_seasons_table, array( 'season_id' => $id, 'rule_id' => $rule['id'] ) );
						}
					}
					return $id;
				} else {
					return $this->db->last_error;
				}
			break;

			case 'update':
				if ( $this->db->update( $this->table_name( $setting['type'] ), $this->create_row( $setting ), array( 'id' => $setting['id'] ) ) !== false ) {
					return 1;
				} else {
					return $this->db->last_error;
				}
			break;

			case 'delete':
				if ( ( $setting['type'] == 'option' ) && ( $setting['choice_type'] == 'multiple' ) ) {
					if ( $this->db->delete( $this->strings_table, array( 'id' => 'option_' . $setting['id'] ) ) === false ) {
						return $this->db->last_error;
					}
					$option_choices = $this->get_all_option_choices( $setting['id'] );
					foreach ( $option_choices as $option_choice ) {
						if ( $this->db->delete( $this->strings_table, array( 'id' => 'option_choice_' . $option_choice['id'] ) ) === false ) {
							return $this->db->last_error;
						}
					}
					if ( $this->db->delete( $this->table_name( 'option_choice' ), array( 'option_id' => $setting['id'] ) ) === false ) {
						return $this->db->last_error;
					}
				}
				if ( $setting['type'] == 'option_choice' ) {
					if ( $this->db->delete( $this->strings_table, array( 'id' => 'option_choice_' . $setting['id'] ) ) === false ) {
						return $this->db->last_error;
					}
				}
				if ( $setting['type'] == 'fee' ) {
					if ( $this->db->delete( $this->strings_table, array( 'id' => 'fee_' . $setting['id'] ) ) === false ) {
						return $this->db->last_error;
					}
				}
				if ( $setting['type'] == 'rule' ) {
					if ( $this->db->delete( $this->rates_rules_table, array( 'rule_id' => $setting['id'] ) ) === false ) {
						return $this->db->last_error;
					}
					if ( $this->db->delete( $this->discounts_rules_table, array( 'rule_id' => $setting['id'] ) ) === false ) {
						return $this->db->last_error;
					}
					if ( $this->db->delete( $this->coupons_rules_table, array( 'rule_id' => $setting['id'] ) ) === false ) {
						return $this->db->last_error;
					}
				}
				if ( $setting['type'] == 'season' ) {
					if ( $this->db->delete( $this->rates_seasons_table, array( 'season_id' => $setting['id'] ) ) === false ) {
						return $this->db->last_error;
					}
					if ( $this->db->delete( $this->discounts_seasons_table, array( 'season_id' => $setting['id'] ) ) === false ) {
						return $this->db->last_error;
					}
					if ( $this->db->delete( $this->coupons_seasons_table, array( 'season_id' => $setting['id'] ) ) === false ) {
						return $this->db->last_error;
					}
					if ( $this->db->delete( $this->booking_rules_seasons_table, array( 'season_id' => $setting['id'] ) ) === false ) {
						return $this->db->last_error;
					}
					if ( $this->db->delete( $this->seasons_dates_table, array( 'season_id' => $setting['id'] ) ) === false ) {
						return $this->db->last_error;
					}
					if ( $this->db->delete( $this->strings_table, array( 'id' => 'season_' . $setting['id'] ) ) === false ) {
						return $this->db->last_error;
					}
				}
				if ( $this->db->delete( $this->table_name( $setting['type'] ), array( 'id' => $setting['id'] ) ) !== false ) {
					return 1;
				} else {
					return $this->db->last_error;
				}
			break;

		}
	}

	public function get_all_accom_ids( $all_status = false, $all_languages = false ) {
		$status_query_part = '';
		if ( ! $all_status ) {
			$status_query_part = " AND post_status = 'publish'";
		}

		$accom = $this->db->get_results(
			"
			SELECT ID
			FROM $this->posts_table
			WHERE post_type = 'hb_accommodation' $status_query_part
			ORDER BY post_date ASC
			"
		, ARRAY_A );

		$returned_accom = array();

		if ( $all_languages ) {
			foreach ( $accom as $a ) {
				$returned_accom[] = $a['ID'];
			}
			return $returned_accom;
		}

		if ( function_exists( 'pll_get_post' ) ) {
			$default_lang = pll_default_language( 'locale' );
		} else if ( function_exists( 'icl_object_id' ) ) {
			global $sitepress;
			$default_lang = $sitepress->get_locale( $sitepress->get_default_language() );
		}
		$returned_accom = array();
		foreach ( $accom as $a ) {
			if ( function_exists( 'pll_get_post' ) ) {
				if ( pll_get_post_language( $a['ID'], 'locale' ) == $default_lang ) {
					$returned_accom[] = $a['ID'];
				}
			} else if ( function_exists( 'icl_object_id' ) ) {
				$wpml_post_info = wpml_get_language_information( null, $a['ID'] );
				if ( is_wp_error( $wpml_post_info ) ) {
					$wpml_post_info = wpml_get_language_information( $a['ID'] );
				}
				if ( $wpml_post_info['locale'] == $default_lang ) {
					$returned_accom[] = $a['ID'];
				}
			} else {
				$returned_accom[] = $a['ID'];
			}
		}

		return $returned_accom;
	}

	public function get_all_linked_accom() {
		$all_linked_accom = array();
		$all_accom = $this->get_all_accom_ids();
		foreach ( $all_accom as $accom_id ) {
			if ( get_post_meta( $accom_id, 'accom_default_page', true ) == 'no' ) {
				$all_linked_accom[ get_post_meta( $accom_id, 'accom_linked_page', true ) ] = $accom_id;
			}
		}
		return $all_linked_accom;
	}

	public function get_all_accom() {
		$accom = $this->get_all_accom_ids();
		$returned_accom = array();
		foreach ( $accom as $accom_id ) {
			$returned_accom[ $accom_id ] = get_the_title( $accom_id );
		}
		return $returned_accom;
	}

	public function get_accom_per_occupancy( $occupancy ) {
		$accom = $this->get_all_accom_ids();
		$returned_accom = array();
		foreach ( $accom as $accom_id ) {
			if (
				( get_post_meta( $accom_id, 'accom_max_occupancy', true ) >= $occupancy ) &&
				( get_post_meta( $accom_id, 'accom_min_occupancy', true ) <= $occupancy )
			) {
				$returned_accom[] = $accom_id;
			}
		}
		return $returned_accom;
	}

	public function get_multi_accom_max_occupancy() {
		$multi_accom_max_occupancy = 0;
		$accom = $this->get_all_accom_ids();
		foreach ( $accom as $accom_id ) {
			$multi_accom_max_occupancy += get_post_meta( $accom_id, 'accom_max_occupancy', true ) * get_post_meta( $accom_id, 'accom_quantity', true );
		}
		return $multi_accom_max_occupancy;
	}

	public function get_unavailable_accom_num_per_date( $accom_id, $check_in, $check_out ) {
		$exclude_waiting_payment = '';
		if ( get_option( 'hb_resa_paid_has_confirmation' ) == 'yes' ) {
			$exclude_waiting_payment = "AND ( status != 'waiting_payment' )";
		}
		$request = $this->db->prepare(
			"
			SELECT accom_num
			FROM $this->resa_table
			WHERE accom_id = %d
			AND ( status != 'cancelled' )
			AND ( status != 'pending' )
			$exclude_waiting_payment
			AND (
			( '%s' >= check_in AND '%s' < check_out )
			OR
			( '%s' > check_in AND '%s' <= check_out )
			OR
			( '%s' <= check_in AND '%s' >= check_out )
			)
			GROUP BY accom_num
			",
			array( $accom_id, $check_in, $check_in, $check_out, $check_out, $check_in, $check_out )
		);
		$accom = $this->db->get_results( $request, ARRAY_A );
		$returned_accom = array();
		foreach ( $accom as $a ) {
			$returned_accom[] = $a['accom_num'];
		}
		$request = $this->db->prepare(
			"
			SELECT accom_num
			FROM $this->accom_blocked_table
			WHERE accom_id = %d
			AND (
			( '%s' >= from_date AND '%s' < to_date )
			OR
			( '%s' > from_date AND '%s' <= to_date )
			OR
			( '%s' <= from_date AND '%s' >= to_date )
			)
			GROUP BY accom_num
			",
			array( $accom_id, $check_in, $check_in, $check_out, $check_out, $check_in, $check_out )
		);
		$accom = $this->db->get_results( $request, ARRAY_A );
		foreach ( $accom as $a ) {
			if ( ! in_array( $a['accom_num'], $returned_accom ) ) {
				$returned_accom[] = $a['accom_num'];
			}
		}
		return $returned_accom;
	}

	public function get_future_blocked_dates( $accom_id ) {
		$yesterday = date( 'Y-m-d', strtotime( '-1 day', time() ) );
		$max_date = $this->get_max_date();
		return $this->db->get_results(
			$this->db->prepare(
				"
				SELECT from_date, to_date, GROUP_CONCAT(DISTINCT accom_num ORDER BY accom_num) AS accom_nums
				FROM $this->accom_blocked_table
				WHERE accom_id = %d
				AND to_date >= %s
				AND from_date <= %s
				GROUP BY from_date, to_date
				"
				, $accom_id, $yesterday, $max_date
			)
		, ARRAY_A );
	}

	public function get_future_blocked_dates_by_accom_num( $accom_id, $accom_num ) {
		$yesterday = date( 'Y-m-d', strtotime( '-1 day', time() ) );
		return $this->db->get_results(
			$this->db->prepare(
				"
				SELECT from_date, to_date, uid, comment, is_prepa_time
				FROM $this->accom_blocked_table
				WHERE accom_id = %d
				AND to_date >= %s
				AND accom_num = %d
				"
				, $accom_id, $yesterday, $accom_num
			)
		, ARRAY_A );
	}

	public function get_accom_quantity_left( $check_in, $check_out, $accom_id = false ) {
		$accom = $this->get_available_accom_occupancy_quantity_per_dates( $check_in, $check_out, $accom_id );
		$returned_accom = array();
		foreach ( $accom as $a ) {
			$returned_accom[ $a['id'] ] = $a['quantity'];
		}
		return $returned_accom;
	}

	public function get_available_accom_occupancy_quantity_per_dates( $check_in, $check_out, $accom_id = false ) {
		$accom = array();
		if ( $accom_id ) {
			$accom_ids = array( $accom_id );
		} else {
			$accom_ids = $this->get_all_accom_ids();
		}
		foreach ( $accom_ids as $accom_id ) {
			$accom_quantity = get_post_meta( $accom_id, 'accom_quantity', true );
			if ( $accom_quantity == '' ) {
				$accom_quantity = 1;
			}
			$unavailable_accom = $this->get_unavailable_accom_num_per_date( $accom_id, $check_in, $check_out );
			$nb_available_accom = $accom_quantity - count( $unavailable_accom );
			if ( $nb_available_accom > 0 ) {
				$accom[] = array(
					'id' => $accom_id,
					'occupancy' => get_post_meta( $accom_id, 'accom_occupancy', true ),
					'min_occupancy' => get_post_meta( $accom_id, 'accom_min_occupancy', true ),
					'max_occupancy' => get_post_meta( $accom_id, 'accom_max_occupancy', true ),
					'quantity' => $nb_available_accom
				);
			}
		}
		return $accom;
	}

	public function get_available_accom_per_dates( $check_in, $check_out ) {
		$accom = $this->get_all_accom_ids();
		return $this->filter_available_accom_per_dates( $accom, $check_in, $check_out );
	}

	public function get_available_accom_per_people_dates( $people, $check_in, $check_out ) {
		$accom = $this->get_accom_per_occupancy( $people );
		return $this->filter_available_accom_per_dates( $accom, $check_in, $check_out );
	}

	public function filter_available_accom_per_dates( $accom, $check_in, $check_out ) {
		$available_accom = array();
		foreach ( $accom as $accom_id ) {
			$unavailable_accom = $this->get_unavailable_accom_num_per_date( $accom_id, $check_in, $check_out );
			$accom_quantity = get_post_meta( $accom_id, 'accom_quantity', true );
			if ( $accom_quantity == '' ) {
				$accom_quantity = 1;
			}
			if ( count( $unavailable_accom ) < $accom_quantity ) {
				$available_accom[] = $accom_id;
			}
		}
		return $available_accom;
	}

	public function get_first_available_accom_num( $accom_id, $check_in, $check_out ) {
		$unavailable_accom = $this->get_unavailable_accom_num_per_date( $accom_id, $check_in, $check_out );
		$accom_num_name = $this->get_accom_num_name( $accom_id );
		$result = false;
		foreach ( $accom_num_name as $accom_num => $num_name ) {
			if ( ! in_array( $accom_num, $unavailable_accom ) ) {
				$result = $accom_num;
				break;
			}
		}
		return apply_filters( 'hb_first_available_accom_num', $result, array( $accom_id, $check_in, $check_out, $unavailable_accom, $accom_num_name ) );
	}

	public function is_available_accom( $accom_id, $check_in, $check_out ) {
		$accom_quantity = get_post_meta( $accom_id, 'accom_quantity', true );
		if ( $accom_quantity == '' ) {
			$accom_quantity = 1;
		}
		$unavailable_accom = $this->get_unavailable_accom_num_per_date( $accom_id, $check_in, $check_out );
		if ( count( $unavailable_accom ) >= $accom_quantity ) {
			return false;
		} else {
			return true;
		}
	}

	public function is_available_accom_num( $accom_id, $accom_num, $check_in, $check_out ) {
		$unavailable_accom = $this->get_unavailable_accom_num_per_date( $accom_id, $check_in, $check_out );
		if ( in_array( $accom_num, $unavailable_accom ) ) {
			return false;
		} else {
			return true;
		}
	}

	public function deleted_accom( $id ) {
		if ( get_post_type( $id ) == 'hb_accommodation' ) {
			$this->db->delete( $this->resa_table, array( 'accom_id' => $id ) );
			$this->db->delete( $this->accom_blocked_table, array( 'accom_id' => $id ) );
			$this->db->delete( $this->accom_num_name_table, array( 'accom_id' => $id ) );
			$accom_junction_types = array( 'rule', 'rate', 'discount', 'coupon', 'fee', 'option', 'email_template' );
			foreach( $accom_junction_types as $accom_junction_type ) {
				$this->db->delete( $this->accom_junction_table_name( $accom_junction_type ), array( 'accom_id' => $id ) );
			}
			$synchro_ids = $this->get_ical_synchro_id_by_accom_id( $id );
			foreach ( $synchro_ids as $synchro_id ) {
				$this->db->delete( $this->ical_table, array( 'synchro_id' => $synchro_id ) );
			}
		}
	}

	public function published_accom( $id ) {
		$accom_junction_types = array( 'rule', 'rate', 'discount', 'fee', 'option', 'email_template' );
		foreach( $accom_junction_types as $accom_junction_type ) {
			$table_name = $this->table_name( $accom_junction_type );
			$settings = $this->db->get_results(
				"
				SELECT id
				FROM $table_name
				WHERE all_accom = 1
				"
			, ARRAY_A );
			foreach ( $settings as $setting ) {
				$accom_junction_table_name = $this->accom_junction_table_name( $accom_junction_type );
				$accom_junction_type_id = $accom_junction_type . '_id';
				$row = $this->db->get_row(
					$this->db->prepare(
						"
						SELECT * FROM $accom_junction_table_name
						WHERE accom_id = %d AND $accom_junction_type_id = %d
						",
						$id, $setting['id']
					)
				);
				if ( ! $row ) {
					$this->db->insert( $this->accom_junction_table_name( $accom_junction_type ), array( 'accom_id' => $id, $accom_junction_type_id => $setting['id'] ) );
				}
			}
		}
		$accom_all_ids_blocked = $this->db->get_results(
			"
			SELECT from_date, to_date
			FROM $this->accom_blocked_table
			WHERE accom_all_ids = 1
			GROUP BY from_date, to_date
			"
			, ARRAY_A
		);
		$accom_quantity = 0;
		if ( isset( $_REQUEST['hb-accom-quantity'] ) ) {
			$accom_quantity = intval( $_REQUEST['hb-accom-quantity'] );
		}
		if ( ! $accom_quantity ) {
			$accom_quantity = 1;
		}
		foreach ( $accom_all_ids_blocked as $blocked ) {
			for ( $i = 1; $i <= $accom_quantity; $i++ ) {
				$row = $this->db->get_row(
					$this->db->prepare(
						"
						SELECT * FROM $this->accom_blocked_table
						WHERE accom_id = %d AND accom_num = %d
						",
						$id, $i
					)
				);
				if ( ! $row ) {
					$this->db->insert(
						$this->accom_blocked_table,
						array(
							'accom_id' => $id,
							'accom_all_ids' => 1,
							'accom_num' => $i,
							'accom_all_num' => 1,
							'from_date' => $blocked['from_date'],
							'to_date' => $blocked['to_date'],
							'uid' => $this->get_uid()
						)
					);
				}
			}
		}
	}

	public function get_rate_and_nights( $type, $rule_id, $accom_id, $season_id, $nights ) {
		$nights = intval( $nights );
		$nights_condition = '';
		if ( $nights ) {
			$nights_condition = $this->db->prepare( ' AND nights = %d ', $nights );
		}
		if ( $rule_id == 0 ) {
			$rates_and_nights = $this->db->get_results(
				$this->db->prepare(
					"
					SELECT amount, nights
					FROM $this->rates_table AS rates
					INNER JOIN $this->rates_seasons_table AS rates_seasons
					ON rates.id = rates_seasons.rate_id
					INNER JOIN $this->rates_accom_table AS rates_accom
					ON rates.id = rates_accom.rate_id
					WHERE type = %s
					$nights_condition
					AND accom_id = %d
					AND season_id = %d
					AND rates.id NOT IN
						(SELECT rate_id FROM $this->rates_rules_table)
					",
					$type,
					$accom_id,
					$season_id
				),
				ARRAY_A
			);
		} else {
			$rates_and_nights = $this->db->get_results(
				$this->db->prepare(
					"
					SELECT amount, nights
					FROM $this->rates_table AS rates
					INNER JOIN $this->rates_rules_table AS rates_rules
					ON rates.id = rates_rules.rate_id
					INNER JOIN $this->rates_seasons_table AS rates_seasons
					ON rates.id = rates_seasons.rate_id
					INNER JOIN $this->rates_accom_table AS rates_accom
					ON rates.id = rates_accom.rate_id
					WHERE type = %s
					$nights_condition
					AND rule_id = %d
					AND accom_id = %d
					AND season_id = %d
					",
					$type,
					$rule_id,
					$accom_id,
					$season_id
				),
				ARRAY_A
			);
		}
		return $rates_and_nights;
	}

	public function update_rates( $type, $rates ) {
		$result = true;
		$this->db->delete( $this->rates_table, array( 'type' => $type ), array( '%s' ) );
		$seasons = $this->get_all( 'seasons' );
		$accom = $this->get_all_accom_ids();
		foreach ( $accom as $accom_id ) {
			foreach ( $seasons as $season ) {
				if ( isset( $rates[ $accom_id ][ $season['id'] ] ) ) {
					$rate = round( $rates[ $accom_id ][ $season['id'] ], 2 );
				} else {
					$rate = 0;
				}
				$r = $this->db->insert(
					$this->rates_table,
					array(
						'accom_id' => $accom_id,
						'season_id' => $season['id'],
						'type' => $type,
						'rate' => $rate
					),
					array(
						'%d',
						'%d',
						'%s',
						'%f'
					)
				);
				if ( ! $r ) {
					$result = false;
				}
			}
		}
		return $result;
	}

	public function get_all_options() {
		return $this->db->get_results(
			"
			SELECT id, name, amount, amount_children, choice_type, link, apply_to_type, all_accom, GROUP_CONCAT( accom_id ) as accom, quantity_max_option, quantity_max, quantity_max_child
			FROM $this->options_table
			LEFT JOIN $this->options_accom_table
			ON $this->options_table.id = $this->options_accom_table.option_id
			GROUP BY id
			ORDER BY id
			"
		, ARRAY_A);
	}

	public function get_all_options_with_choices() {
		$options = $this->get_all_options();
		return $this->options_with_choices( $options );
	}

	public function get_options_with_choices( $accom_id ) {
		$options = $this->db->get_results(
			$this->db->prepare(
				"
				SELECT *
				FROM $this->options_table
				INNER JOIN $this->options_accom_table
				ON $this->options_table.id = $this->options_accom_table.option_id
				WHERE $this->options_accom_table.accom_id = %d
				GROUP BY $this->options_table.id
				"
				, $accom_id
			)
		, ARRAY_A );
		return $this->options_with_choices( $options );
	}

	public function get_global_options_with_choices() {
		$options = $this->db->get_results(
			"
			SELECT *
			FROM $this->options_table
			WHERE link = 'booking'
			"
			, ARRAY_A
		);
		return $this->options_with_choices( $options );
	}

	private function options_with_choices( $options ) {
		foreach ( $options as $key => $option ) {
			$options[ $key ]['choices'] = $this->get_all_option_choices( $option['id'] );
		}
		return $options;
	}

	public function get_all_option_choices( $option_id ) {
		return $this->db->get_results(
			$this->db->prepare(
				"
				SELECT id, name, amount, amount_children
				FROM $this->options_choices_table
				WHERE option_id = %s
				ORDER BY id
				"
				, $option_id
			)
		, ARRAY_A );
	}

	public function site_has_extras() {
		$results = $this->db->get_results(
			"
			SELECT id FROM $this->options_table
			"
		, ARRAY_A );
		if ( $results ) {
			return true;
		} else {
			return false;
		}
	}

	public function remove_linked_booking_extra() {
		$request =
		"
		UPDATE $this->options_table
		SET apply_to_type = 'per-accom', link = 'accom'
		WHERE apply_to_type = 'per-accom-per-booking' OR apply_to_type = 'per-booking'
		";
		$this->db->query( $request );
	}

	public function get_all_fees() {
		return $this->db->get_results(
			"
			SELECT id, name, amount, amount_children, apply_to_type, accom_price_per_person_per_night,  all_accom, global, include_in_price, minimum_amount, maximum_amount, multiply_per, GROUP_CONCAT( accom_id ) as accom
			FROM $this->fees_table
			LEFT JOIN $this->fees_accom_table
			ON $this->fees_table.id = $this->fees_accom_table.fee_id
			GROUP BY id
			"
		, ARRAY_A );
	}

	public function get_all_seasons_with_dates() {
		$seasons = $this->get_all( 'seasons' );
		$returned_seasons = array();
		foreach ( $seasons as $season ) {
			$new_season = array(
				'id' => $season['id'],
				'name' => $season['name'],
				'priority' => $season['priority'],
				'dates' => $this->get_all_season_dates( $season['id'] )
			);
			$returned_seasons[] = $new_season;
		}
		return $returned_seasons;
	}
	
	public function get_accom_season_ids( $accom_id ) {
		$season_ids = $this->db->get_results(
			$this->db->prepare(
				"SELECT GROUP_CONCAT(season_id) AS season_ids 
				FROM $this->rates_accom_table AS RA
				JOIN $this->rates_seasons_table AS RS 
				ON RA.rate_id = RS.rate_id
				WHERE accom_id = %s
				"
				, $accom_id
			)
		, ARRAY_A );
		return $season_ids;
	}

	public function get_all_season_dates( $season_id ) {
		$season_dates = $this->db->get_results(
			$this->db->prepare(
				"
				SELECT id, season_id, start_date, end_date, days
				FROM $this->seasons_dates_table
				WHERE season_id = %s
				"
				, $season_id
			)
		, ARRAY_A );
		return $season_dates;
	}

	public function get_season( $night ) {
		$priorities = array( 'high', '', 'low' );
		foreach ( $priorities as $priority ) {
			$seasons = $this->db->get_results(
				$this->db->prepare(
					"
					SELECT season_id, days
					FROM $this->seasons_dates_table
					WHERE '%s' >= start_date AND '%s' <= end_date AND season_id IN
					(SELECT id FROM $this->seasons_table WHERE priority = '%s')
					"
					, $night, $night, $priority
				)
			, ARRAY_A );
			if ( count( $seasons ) > 0 ) {
				$night_number = strval( date( 'N', strtotime( $night ) ) - 1 );
				foreach ( $seasons as $season ) {
					if ( strpos( $season['days'], $night_number ) !== false ) {
						return $season['season_id'];
					}
				}
			}
		}
		return false;
	}

	public function get_season_with_considering_accom_rate( $night , $accom_id = 0) {
		$priorities = array( 'high', '', 'low' );
		foreach ( $priorities as $priority ) {
			$seasons = $this->db->get_results(
				$this->db->prepare(
					"	SELECT SD.season_id, days
						FROM $this->seasons_dates_table AS SD
						JOIN $this->seasons_table AS S ON S.id = SD.season_id and S.priority = '%s'
						JOIN $this->rates_seasons_table AS RS ON SD.season_id = RS.season_id
						JOIN  $this->rates_accom_table as RA ON RS.rate_id = RA.rate_id AND RA.accom_id = '%d'
						WHERE '%s' >= start_date AND '%s' <= end_date
					"
					, $priority , $accom_id ,$night, $night
				)
			, ARRAY_A );
			if ( count( $seasons ) > 0 ) {
				$night_number = strval( date( 'N', strtotime( $night ) ) - 1 );
				foreach ( $seasons as $season ) {
					if ( strpos( $season['days'], $night_number ) !== false ) {
						return $season['season_id'];
					}
				}
			}
		}
		return false;
	}

	public function get_season_by_name( $season_name ) {
		$result = $this->db->get_results(
			$this->db->prepare(
				"
				SELECT *
				FROM $this->seasons_table
				WHERE name = '%s'
				"
				, $season_name
			)
		, ARRAY_A );
		if ( ! $result ) {
			return false;
		} else {
			return $result[0];
		}
	}

	public function get_seasons_id_name() {
		$seasons = $this->db->get_results(
			"
			SELECT id, name
			FROM $this->seasons_table
			"
		, ARRAY_A );
		$returned_seasons = array();
		foreach ( $seasons as $season ) {
			$returned_seasons[ $season['id'] ] = $season['name'];
		}
		return $returned_seasons;
	}

	public function create_resa( $resa_info ) {
		if ( isset( $resa_info['additional_info'] ) ) {
			$resa_info['additional_info'] = json_encode( $resa_info['additional_info'] );
		}
		foreach ( $resa_info as $key => $info ) {
			$resa_info[ $key ] = strip_tags( $info );
		}
		if ( ! isset( $resa_info['received_on'] ) || ! $resa_info['received_on'] ) {
			$resa_info['received_on'] = current_time( 'mysql', 1 );
		}
		if ( ! isset( $resa_info['updated_on'] ) || ! $resa_info['updated_on'] ) {
			$resa_info['updated_on'] = current_time( 'mysql', 1 );
		}
		if ( ! isset( $resa_info['uid'] ) || ! $resa_info['uid'] ) {
			$resa_info['uid'] = $this->get_uid();
		}
		if ( ! isset( $resa_info['currency'] ) || ! $resa_info['currency'] ) {
			$resa_info['currency'] = get_option( 'hb_currency' );
		}
		if ( ! isset( $resa_info['lang'] ) || ! $resa_info['lang'] ) {
			$resa_info['lang'] = get_locale();
		}
		if ( $this->db->insert( $this->resa_table, $resa_info ) ) {
			$inserted_id = $this->db->insert_id;
			do_action( 'hb_create_reservation', $resa_info );
			do_action( 'hb_reservations_updated' );
			return $inserted_id;
		} else {
			return false;
		}
	}

	public function create_parent_resa( $resa_info ) {
		if ( isset( $resa_info['additional_info'] ) ) {
			$resa_info['additional_info'] = json_encode( $resa_info['additional_info'] );
		}
		foreach ( $resa_info as $key => $info ) {
			$resa_info[ $key ] = strip_tags( $info );
		}
		if ( ! isset( $resa_info['received_on'] ) || ! $resa_info['received_on'] ) {
			$resa_info['received_on'] = current_time( 'mysql', 1 );
		}
		if ( $this->db->insert( $this->parents_resa_table, $resa_info ) ) {
			$inserted_id = $this->db->insert_id;
			return $inserted_id;
		} else {
			return false;
		}
	}

	public function automatic_block_accom( $resa_accom_id, $resa_accom_num, $resa_check_in, $resa_check_out, $resa_id ) {
		$preparation_time = get_post_meta( $resa_accom_id, 'accom_preparation_time', true );
		$blocked_linked_accom = $this->block_linked_accom( $resa_accom_id, $resa_check_in, $resa_check_out, $resa_id, $preparation_time );
		$blocked_preparation_time = $this->block_preparation_time( $resa_accom_id, $resa_accom_num, $resa_check_in, $resa_check_out, $resa_id, $preparation_time );
		return array_merge( $blocked_preparation_time, $blocked_linked_accom );
	}

	public function block_linked_accom( $resa_accom_id, $resa_check_in, $resa_check_out, $resa_id, $preparation_time ) {
		$blocked_linked_accom = array();
		$accom_to_block = get_post_meta( $resa_accom_id, 'accom_to_block', true );
		if ( $accom_to_block ) {
			$accom_to_block = explode( ',', $accom_to_block );
			foreach ( $accom_to_block as $accom_id ) {
				$matches = array();
				$number_of_accom_to_block = 1;
				if ( preg_match( '/\((\d+)\)/', $accom_id, $matches ) ) {
					$number_of_accom_to_block = $matches[1];
					$accom_id = preg_replace( '/\(\d+\)/', '', $accom_id );
				}
				$accom_quantity = get_post_meta( $accom_id, 'accom_quantity', true );
				if ( $accom_quantity == '' ) {
					$accom_quantity = 1;
				}
				if ( $number_of_accom_to_block > $accom_quantity ) {
					$number_of_accom_to_block = $accom_quantity;
				}
				$unavailable_accom = $this->get_unavailable_accom_num_per_date( $accom_id, $resa_check_in, $resa_check_out );
				$nb_available_accom = $accom_quantity - count( $unavailable_accom );
				if ( $nb_available_accom >= $number_of_accom_to_block ) {
					for ( $i = 1; $i <= $number_of_accom_to_block; $i++ ) {
						$accom_num = $this->get_first_available_accom_num( $accom_id, $resa_check_in, $resa_check_out );
						if ( $accom_num ) {
							$this->add_blocked_accom( $accom_id, $accom_num, 0, 0, $resa_check_in, $resa_check_out, $resa_id . '. ' . esc_html__( 'Blocked automatically', 'hbook-admin' ), $resa_id );
							$blocked_linked_accom[] = array(
								'accom_id' => $accom_id,
								'accom_num' => $accom_num,
								'from_date' => $resa_check_in,
								'to_date' => $resa_check_out,
								'comment' => $resa_id . '. ' . esc_html__( 'Blocked automatically', 'hbook-admin' ),
								'linked_resa_id' => $resa_id,
								'is_prepa_time' => 0,
							);
							$blocked_preparation_time = $this->block_preparation_time( $accom_id, $accom_num, $resa_check_in, $resa_check_out, $resa_id, $preparation_time );
							$blocked_linked_accom = array_merge( $blocked_linked_accom, $blocked_preparation_time );
						}
					}
				} else {
					$accom_nums = $this->get_accom_nums( $accom_id );
					for ( $i = 1; $i <= $number_of_accom_to_block; $i++ ) {
						$this->add_blocked_accom( $accom_id, $accom_nums[ $i - 1 ], 0, 0, $resa_check_in, $resa_check_out, $resa_id . '. ' . esc_html__( 'Blocked automatically', 'hbook-admin' ), $resa_id );
						$blocked_linked_accom[] = array(
							'accom_id' => $accom_id,
							'accom_num' => $accom_nums[ $i - 1 ],
							'from_date' => $resa_check_in,
							'to_date' => $resa_check_out,
							'comment' => $resa_id . '. ' . esc_html__( 'Blocked automatically', 'hbook-admin' ),
							'linked_resa_id' => $resa_id,
							'is_prepa_time' => 0,
						);
						$blocked_preparation_time = $this->block_preparation_time( $accom_id, $accom_nums[ $i - 1 ], $resa_check_in, $resa_check_out, $resa_id, $preparation_time );
						$blocked_linked_accom = array_merge( $blocked_linked_accom, $blocked_preparation_time );
					}
				}
			}
		}
		return $blocked_linked_accom;
	}

	public function block_preparation_time( $resa_accom_id, $resa_accom_num, $resa_check_in, $resa_check_out, $resa_id, $preparation_time ) {
		$blocked_preparation_time = array();
		if ( $preparation_time ) {
			$start_date_block_before = new DateTime( $resa_check_in );
			$end_date_block_after = new DateTime( $resa_check_out );
			$start_date_block_before->modify( '- ' . $preparation_time . 'day');
			$end_date_block_after->modify( '+ ' . $preparation_time . 'day');
			$start_date_block_before = $start_date_block_before->format( 'Y-m-d' );
			$end_date_block_after = $end_date_block_after->format( 'Y-m-d' );
			$this->add_blocked_accom( $resa_accom_id, $resa_accom_num, 0, 0, $start_date_block_before, $resa_check_in, $resa_id . '. ' . esc_html__( 'Preparation time', 'hbook-admin' ), $resa_id, 1 );
			$blocked_preparation_time[] = array(
				'accom_id' => $resa_accom_id,
				'accom_num' => $resa_accom_num,
				'from_date' => $start_date_block_before,
				'to_date' => $resa_check_in,
				'comment' => $resa_id . '. ' . esc_html__( 'Preparation time', 'hbook-admin' ),
				'linked_resa_id' => $resa_id,
				'is_prepa_time' => 1,
			);
			$this->add_blocked_accom( $resa_accom_id, $resa_accom_num, 0, 0, $resa_check_out, $end_date_block_after, $resa_id . '. ' . esc_html__( 'Preparation time', 'hbook-admin' ), $resa_id, 1 );
			$blocked_preparation_time[] = array(
				'accom_id' => $resa_accom_id,
				'accom_num' => $resa_accom_num,
				'from_date' => $resa_check_out,
				'to_date' => $end_date_block_after,
				'comment' => $resa_id . '. ' . esc_html__( 'Preparation time', 'hbook-admin' ),
				'linked_resa_id' => $resa_id,
				'is_prepa_time' => 1,
			);
		}
		return $blocked_preparation_time;
	}

	public function delete_preparation_time_blocked_accom( $resa_id ) {
		$blocked_accom = $this->db->get_results(
			$this->db->prepare(
				"
				SELECT *
				FROM $this->accom_blocked_table
				WHERE linked_resa_id = %d
				AND is_prepa_time = 1
				",
				$resa_id
			)
		, ARRAY_A );
		$this->db->delete( $this->accom_blocked_table, array( 'linked_resa_id' => $resa_id, 'is_prepa_time' => 1 ) );
		return $blocked_accom;
	}

	public function update_resa_after_payment( $token, $payment_status, $payment_status_reason, $paid ) {
		if ( get_option( 'hb_resa_paid_has_confirmation' ) == 'no' ) {
			$status = get_option( 'hb_resa_website_status' );
		} else {
			$status = 'pending';
		}
		$new_values = array(
			'payment_status' => $payment_status,
			'payment_status_reason' => $payment_status_reason,
			'paid' => $paid
		);
		$resa = $this->get_resa_by_payment_token( $token );
		if ( ! $resa ) {
			return false;
		}
		$resa_id = $resa['id'];
		if ( ! isset( $resa['parent_id'] ) ) {
			$is_parent = true;
			$update = $this->db->update( $this->resa_table, array( 'status' => $status ), array( 'parent_id' => $resa['id'] ) );
			if ( ! $update ) {
				return false;
			}
			$update = $this->db->update( $this->parents_resa_table, $new_values, array( 'payment_token' => $token ) );
			$resas = $this->get_resa_by_parent_id( $resa_id );
		} else {
			$is_parent = false;
			$new_values['status'] = $status;
			$update = $this->db->update( $this->resa_table, $new_values, array( 'payment_token' => $token ) );
			$resas = array( $resa );
		}
		if ( $update ) {
			do_action( 'hb_reservations_updated' );
			if ( ( $status == 'new' ) || ( $status == 'confirmed' ) ) {
				foreach ( $resas as $resa ) {
					$this->automatic_block_accom( $resa['accom_id'], $resa['accom_num'], $resa['check_in'], $resa['check_out'], $resa['id'] );
				}
			}
			if ( $is_parent ) {
				do_action( 'hb_parent_reservation_updated', 'payment', $resa_id );
				return '#' . $resa_id;
			} else {
				do_action( 'hb_reservation_updated', 'payment', $resa_id );
				return $resa_id;
			}
		} else {
			return false;
		}
	}

	public function cancel_parent_resa( $id ) {
		$db_children_resas = $this->get_resa_by_parent_id( $id );
		$updated = true;
		foreach ( $db_children_resas as $db_child_resa ) {
			if ( ! $this->update_resa_status( $db_child_resa['id'], 'cancelled' ) ) {
				$updated = false;
			}
		}
		do_action( 'hb_reservations_updated' );
		return $updated;
	}

	public function update_resa_status( $id, $status ) {
		$updated = $this->db->update( $this->resa_table, array(
			'status' => $status,
			'updated_on' => current_time( 'mysql', 1 ),
		), array( 'id' => $id ) );
		if ( $updated ) {
			do_action( 'hb_reservation_updated', 'status', $id );
			do_action( 'hb_reservations_updated' );
			if ( $status == 'cancelled' ) {
				$this->db->delete( $this->accom_blocked_table, array( 'linked_resa_id' => $id ) );
			}
		}
		return $updated;
	}

	public function update_resa_accom( $id, $accom_id, $accom_num ) {
		$updated = $this->db->update( $this->resa_table, array(
			'accom_id' => $accom_id,
			'accom_num' => $accom_num,
			'uid' => $this->get_uid(),
		), array( 'id' => $id ) );
		if ( $updated ) {
			$this->db->delete( $this->accom_blocked_table, array( 'linked_resa_id' => $id ) );
			do_action( 'hb_reservation_updated', 'accommodation', $id );
			do_action( 'hb_reservations_updated' );
		}
		return $updated;
	}

	public function update_resa_info( $id, $adults, $children, $lang, $additional_info ) {
		$updated = $this->db->update( $this->resa_table, array(
			'adults' => $adults,
			'children' => $children,
			'lang' => $lang,
			'additional_info' => $additional_info
		), array( 'id' => $id ) );
		if ( $updated ) {
			do_action( 'hb_reservation_updated', 'info', $id );
			do_action( 'hb_reservations_updated' );
		}
		return $updated;
	}

	public function update_parent_resa_info( $id, $lang, $additional_info ) {
		$updated = $this->db->update( $this->parents_resa_table, array(
			'lang' => $lang,
			'additional_info' => $additional_info
		), array( 'id' => $id ) );
		if ( $updated ) {
			do_action( 'hb_reservation_updated', 'info', $id );
			do_action( 'hb_reservations_updated' );
		}
		return $updated;
	}

	public function update_resa_options( $id, $is_parent, $options ) {
		if ( $is_parent ) {
			$table = $this->parents_resa_table;
		} else {
			$table = $this->resa_table;
		}
		$updated = $this->db->update( $table, array( 'options' => $options ), array( 'id' => $id ) );
		if ( $updated ) {
			if ( $is_parent ) {
				do_action( 'hb_reservation_updated', 'extras', $id );
			} else {
				do_action( 'hb_parent_reservation_updated', 'extras', $id );
			}
			do_action( 'hb_reservations_updated' );
		}
		return $updated;
	}

	public function update_resa_comment( $id, $is_parent, $comment ) {
		if ( $is_parent ) {
			$table = $this->parents_resa_table;
		} else {
			$table = $this->resa_table;
		}
		$updated = $this->db->update( $table, array(
			'admin_comment' => $comment,
		), array( 'id' => $id ) );
		if ( $updated ) {
			if ( $is_parent ) {
				do_action( 'hb_reservation_updated', 'comment', $id );
			} else {
				do_action( 'hb_parent_reservation_updated', 'comment', $id );
			}
			do_action( 'hb_reservations_updated' );
		}
		return $updated;
	}

	public function update_resa_prices_info( $id, $accom_price, $discount, $deposit, $previous_price, $price ) {
		$to_update = array(
			'accom_price' => $accom_price,
			'discount' => $discount,
		);
		if ( $price != $previous_price ) {
			$to_update['price'] = $price;
			$to_update['previous_price'] = $previous_price;
			$to_update['deposit'] = $deposit;
		}
		$updated = $this->db->update( $this->resa_table, $to_update, array( 'id' => $id ) );
		if ( $updated && ( $price != $previous_price ) ) {
			do_action( 'hb_reservations_updated' );
			do_action( 'hb_reservation_updated', 'price', $id );
		}
		return $updated;
	}

	public function update_resa_price( $id, $previous_price, $price ) {
		$updated = $this->db->update( $this->resa_table, array(
			'price' => $price,
			'previous_price' => $previous_price,
		), array( 'id' => $id ) );
		if ( $updated ) {
			do_action( 'hb_reservation_updated', 'price', $id );
			do_action( 'hb_reservations_updated' );
		}
		return $updated;
	}

	public function update_parent_resa_price( $id, $previous_price, $price ) {
		$updated = $this->db->update( $this->parents_resa_table, array(
			'price' => $price,
			'previous_price' => $previous_price,
		), array( 'id' => $id ) );
		if ( $updated ) {
			do_action( 'hb_parent_reservation_updated', 'price', $id );
			do_action( 'hb_reservations_updated' );
		}
		return $updated;
	}

	public function update_resa_paid( $id, $is_parent, $paid ) {
		if ( $is_parent ) {
			$table = $this->parents_resa_table;
		} else {
			$table = $this->resa_table;
		}
		$updated = $this->db->update( $table, array(
			'paid' => $paid,
		), array( 'id' => $id ) );
		if ( $updated ) {
			if ( $is_parent ) {
				do_action( 'hb_reservation_updated', 'paid', $id );
			} else {
				do_action( 'hb_parent_reservation_updated', 'paid', $id );
			}
			do_action( 'hb_reservations_updated' );
		}
		return $updated;
	}

	public function update_resa_payment_info( $id, $is_parent, $payment_info ) {
		if ( $is_parent ) {
			$table = $this->parents_resa_table;
		} else {
			$table = $this->resa_table;
		}
		$updated = $this->db->update( $table, array( 'payment_info' => $payment_info ), array( 'id' => $id ) );
		if ( $updated ) {
			if ( $is_parent ) {
				do_action( 'hb_reservation_updated', 'payment_info', $id );
			} else {
				do_action( 'hb_parent_reservation_updated', 'payment_info', $id );
			}
			do_action( 'hb_reservations_updated' );
		}
		return $updated;
	}

	public function update_resa_dates( $id, $check_in, $check_out ) {
		$updated = $this->db->update( $this->resa_table, array(
			'check_in' => $check_in,
			'check_out' => $check_out,
			'updated_on' => current_time( 'mysql', 1 ),
		), array( 'id' => $id ) );
		if ( $updated ) {
			$this->db->delete( $this->accom_blocked_table, array( 'linked_resa_id' => $id ) );
			do_action( 'hb_reservation_updated', 'dates', $id );
			do_action( 'hb_reservations_updated' );
		}
		return $updated;
	}

	public function get_resa_by_parent_id( $parent_id ) {
		return $this->db->get_results(
			$this->db->prepare(
				"
				SELECT *
				FROM $this->resa_table
				WHERE parent_id = %s
				",
				$parent_id
			)
		, ARRAY_A );
	}

	public function get_resa_by_alphanum( $alphanum ) {
		$resa = $this->db->get_results(
			$this->db->prepare(
				"
				SELECT *
				FROM $this->resa_table
				WHERE alphanum_id = %s
				",
				$alphanum
			)
		, ARRAY_A );
		if ( $resa ) {
			return $resa[0];
		}
		$resa = $this->db->get_results(
			$this->db->prepare(
				"
				SELECT *
				FROM $this->parents_resa_table
				WHERE alphanum_id = %s
				",
				$alphanum
			)
		, ARRAY_A );
		if ( $resa ) {
			return $resa[0];
		}
		return false;
	}

	public function get_resa_by_payment_token( $token ) {
		$resa = $this->db->get_results(
			$this->db->prepare(
				"
				SELECT *
				FROM $this->resa_table
				WHERE payment_token = %s
				",
				$token
			)
		, ARRAY_A );
		if ( $resa ) {
			return $resa[0];
		} else {
			$resa = $this->db->get_results(
				$this->db->prepare(
					"
					SELECT *
					FROM $this->parents_resa_table
					WHERE payment_token = %s
					",
					$token
				)
			, ARRAY_A );
			if ( $resa ) {
				$resa_info = $resa[0];
				$resas = $this->get_resa_by_parent_id( $resa_info['id'] );
				if ( ! $resas ) {
					return false;
				} else {
					$resa_info['accom_ids'] = array();
					$resa_info['check_in'] = $resas[0]['check_in'];
					$resa_info['check_out'] = $resas[0]['check_out'];
					$resa_info['adults'] = 0;
					$resa_info['children'] = 0;
					foreach ( $resas as $resa ) {
						$resa_info['accom_ids'][] = $resa['accom_id'];
						$resa_info['adults'] += $resa['adults'];
						$resa_info['children'] += $resa['children'];
					}
				}
				return $resa_info;
			} else {
				return false;
			}
		}
	}

	public function get_future_resa_dates( $accom_id ) {
		$request = "SELECT check_in, check_out FROM $this->resa_table WHERE check_out >= %s AND check_in <= %s";

		$yesterday = date( 'Y-m-d', strtotime( '-1 day', time() ) );
		$max_date = $this->get_max_date();
		$args = array( $yesterday, $max_date );

		if ( $accom_id != 'all' ) {
			$request .= ' AND accom_id = %d';
			$args[] = $accom_id;
		}

		return $this->db->get_results( $this->db->prepare( $request, $args ), ARRAY_A );
	}

	public function get_max_date() {
		$max_date = get_option( 'hb_max_date_fixed' );
		if ( ! $max_date ) {
			$nb_months = intval( get_option( 'hb_max_date_months' ) );
			if ( ! $nb_months ) {
				$nb_months = 36;
			}
			$tmp_date = new DateTime();
			$tmp_date->modify( "+{$nb_months} month" );
			$max_date = $tmp_date->format( 'Y-m-t' );
		}
		return $max_date;
	}

	public function get_future_resa_by_accom_num( $accom_id, $accom_num ) {
		$yesterday = date( 'Y-m-d', strtotime( '-1 day', time() ) );
		$resa = $this->db->get_results(
			$this->db->prepare(
				"
				SELECT *
				FROM $this->resa_table
				WHERE accom_id = %d
				AND accom_num = %d
				AND check_out >= %s
				"
				, $accom_id, $accom_num, $yesterday
			)
		, ARRAY_A );
		return $resa;
	}

	public function get_all_blocked_accom() {
		$accom_nums = array();
		$returned_blocked_accom = array();

		$blocked_accom = $this->db->get_results(
			"
			SELECT from_date, to_date, accom_all_ids, accom_all_num, comment, linked_resa_id, is_prepa_time
			FROM $this->accom_blocked_table
			WHERE accom_all_ids = 1
			GROUP BY from_date, to_date
			ORDER BY from_date DESC
			",
			ARRAY_A
		);
		$returned_blocked_accom = $blocked_accom;

		$blocked_accom = $this->db->get_results(
			"
			SELECT accom_id, accom_all_num, from_date, to_date, comment, linked_resa_id, is_prepa_time
			FROM $this->accom_blocked_table
			WHERE accom_all_ids = 0 AND accom_all_num = 1
			GROUP BY accom_id, from_date, to_date
			ORDER BY from_date DESC
			",
			ARRAY_A
		);
		$returned_blocked_accom = array_merge( $returned_blocked_accom, $blocked_accom );

		$blocked_accom = $this->db->get_results(
			"
			SELECT accom_id, accom_num, accom_all_num, from_date, to_date, comment, linked_resa_id, is_prepa_time
			FROM $this->accom_blocked_table
			WHERE accom_all_ids = 0 AND accom_all_num = 0
			",
			ARRAY_A
		);
		$returned_blocked_accom = array_merge( $returned_blocked_accom, $blocked_accom );

		return $returned_blocked_accom;
	}

	public function add_blocked_accom( $accom_id, $accom_num, $accom_all_ids, $accom_all_num, $from_date, $to_date, $comment, $linked_resa_id = 0, $is_prepa_time = 0 ) {
		do_action( 'hb_blocked_accom_updated' );
		if ( $accom_all_ids == 1 ) {
			$accom_ids = $this->get_all_accom_ids();
			$accom_all_ids = 1;
			$accom_all_num = 1;
		} else {
			$accom_ids = array( $accom_id );
			$accom_all_ids = 0;
		}
		$inserted_ids = array();
		foreach ( $accom_ids as $accom_id ) {
			if ( $accom_all_num == 1 ) {
				$nums = array_keys( $this->get_accom_num_name( $accom_id ) );
			} else {
				$nums = array( $accom_num );
			}
			foreach ( $nums as $num ) {
				if ( ! $this->db->insert(
					$this->accom_blocked_table,
					array(
						'accom_id' => $accom_id,
						'accom_all_ids' => $accom_all_ids,
						'accom_num' => $num,
						'accom_all_num' => $accom_all_num,
						'from_date' => $from_date,
						'to_date' => $to_date,
						'comment' => $comment,
						'linked_resa_id' => $linked_resa_id,
						'is_prepa_time' => $is_prepa_time,
						'uid' => $this->get_uid(),
					)
				) ) {
					return false;
				}
			}
		}
		return true;
	}

	public function delete_blocked_accom( $date_from, $date_to, $accom_id, $accom_num, $accom_all_ids, $accom_all_num ) {
		do_action( 'hb_blocked_accom_updated' );
		$request = "
					DELETE
					FROM $this->accom_blocked_table
					WHERE from_date = %s AND to_date = %s
					";
		$request = $this->db->prepare( $request, $date_from, $date_to );
		if ( $accom_all_ids == 1 ) {
			$request .= " AND accom_all_ids = 1";
		} else {
			$request .= " AND accom_id = %d";
			$request = $this->db->prepare( $request, $accom_id );
			if ( $accom_all_num == 1 ) {
				$request .= " AND accom_all_num = 1";
			} else {
				$request .= " AND accom_num = %d";
				$request = $this->db->prepare( $request, $accom_num );
			}
		}
		return $this->db->query( $request );
	}

	public function delete_resa( $id ) {
		$deleted = $this->db->delete( $this->resa_table, array( 'id' => $id ) );
		if ( $deleted ) {
			$this->db->delete( $this->accom_blocked_table, array( 'linked_resa_id' => $id ) );
			$this->db->delete( $this->email_logs_table, array( 'resa_id' => $id, 'resa_is_parent' => 0 ) );
			do_action( 'hb_reservations_updated' );
		}
		return $deleted;
	}

	public function delete_parent_resa( $id ) {
		$db_children_resas = $this->get_resa_by_parent_id( $id );
		foreach ( $db_children_resas as $db_child_resa ) {
			$this->delete_resa( $db_child_resa['id'] );
		}
		$deleted = $this->db->delete( $this->parents_resa_table, array( 'id' => $id ) );
		$this->db->delete( $this->email_logs_table, array( 'resa_id' => $id, 'resa_is_parent' => 1 ) );
		do_action( 'hb_reservations_updated' );
		return $deleted;
	}

	public function delete_uncompleted_resa() {
		$delay = apply_filters( 'hb_uncompleted_resa_deletion_delay', '1 HOUR' );
		$delay_check = explode( ' ', $delay, 2 );
		$accepted_time_values = array( 'HOUR', 'MINUTE' );
		if ( ctype_digit( $delay_check[0] ) && ( in_array( $delay_check[1], $accepted_time_values ) ) ) {
			$valid_delay = $delay;
		} else {
			$valid_delay = '1 HOUR';
		}
		$deleted = $this->db->query( "DELETE FROM $this->resa_table WHERE ( status = 'waiting_payment' ) AND ( '" . current_time( 'mysql', 1 ) . "' - INTERVAL $valid_delay > received_on)" );
		if ( $deleted ) {
			do_action( 'hb_reservations_updated' );
		}
	}

	public function delete_multi_resa_pending( $ids ) {
		foreach ( $ids as $id ) {
			$this->db->delete( $this->resa_table, array( 'id' => $id ) );
		}
	}

	public function delete_old_email_logs() {
		$retention_period = intval( get_option( 'hb_email_logs_retention_period' ) );
		if ( $retention_period ) {
			$this->db->query( "DELETE FROM $this->email_logs_table WHERE DATE_SUB('" . current_time( 'Y-m-d', 1 ) . "', INTERVAL " . $retention_period . " DAY) > sent_on" );
		}
	}

	public function get_all_resa_by_date() {
		return $this->db->get_results(
			"
			SELECT *
			FROM $this->resa_table
			ORDER BY received_on DESC
			"
		, ARRAY_A );
	}

	public function get_all_resa_by_date_by_accom( $accom_id ) {
		return $this->db->get_results(
			$this->db->prepare(
				"
				SELECT *
				FROM $this->resa_table
				WHERE accom_id = %d
				ORDER BY received_on DESC
				",
				$accom_id
			)
		, ARRAY_A );
	}

	public function get_all_resa_by_date_by_customer( $customer_id ) {
		return $this->db->get_results(
			$this->db->prepare(
				"
				SELECT *
				FROM $this->resa_table
				WHERE customer_id = %d
				ORDER BY received_on DESC
				",
				$customer_id
			)
		, ARRAY_A );
	}

	public function get_resa_between_dates( $date_type, $from_date, $to_date ) {
		return $this->db->get_results(
			$this->db->prepare(
				"
				SELECT *
				FROM $this->resa_table
				WHERE ( $date_type BETWEEN '%s' AND '%s' )
				ORDER BY $date_type DESC
				",
				$from_date,
				$to_date
			)
		, ARRAY_A );
	}

	public function get_resa_between_dates_by_accom( $date_type, $from_date, $to_date, $accom_id ) {
		return $this->db->get_results(
			$this->db->prepare(
				"
				SELECT *
				FROM $this->resa_table
				WHERE ( $date_type BETWEEN '%s' AND '%s' ) AND accom_id = %d
				ORDER BY $date_type DESC
				",
				$from_date,
				$to_date,
				$accom_id
			)
		, ARRAY_A );
	}

	public function get_all_parents_resa() {
		$parents_resa = $this->db->get_results( "SELECT * FROM $this->parents_resa_table", ARRAY_A );
		$returned_parents_resa = array();
		foreach ( $parents_resa as $parent_resa ) {
			$returned_parents_resa[ $parent_resa['id'] ] = $parent_resa;
		}
		return $returned_parents_resa;
	}

	public function get_customer_id( $email ) {
		if ( ! $email ) {
			return false;
		} else {
			return $this->db->get_var(
				$this->db->prepare(
					"
					SELECT id
					FROM $this->customers_table
					WHERE email = %s
					",
					$email
				)
			);
		}
	}

	public function create_customer( $email, $info ) {
		if ( $this->db->insert( $this->customers_table, array( 'email' => $email, 'info' => json_encode( $info ) ) ) ) {
			$inserted_id = $this->db->insert_id;
			do_action( 'hb_create_customer', $info );
			return $inserted_id;
		} else {
			return false;
		}
	}

	public function delete_customer( $id ) {
		$deleted = $this->db->delete( $this->customers_table, array( 'id' => $id ) );
		if ( $deleted ) {
			$this->db->update( $this->resa_table, array( 'customer_id' => 0 ), array( 'customer_id' => $id ) );
			return true;
		} else {
			return false;
		}
	}

	public function update_customer_on_resa_creation( $id, $email, $new_info ) {
		$current_info = $this->get_customer_info( $id );
		$customer_fields = $this->get_customer_form_fields();
		$updated_info = array();
		foreach ( $customer_fields as $field ) {
			if ( isset( $new_info[ $field['id'] ] ) ) {
				$updated_info[ $field['id'] ] = $new_info[ $field['id'] ];
			} else if ( isset( $current_info[ $field['id'] ] ) ) {
				$updated_info[ $field['id'] ] = $current_info[ $field['id'] ];
			}
		}
		return $this->update_customer( $id, $email, $updated_info );
	}

	public function update_customer( $id, $email, $info ) {
		$info = json_encode( $info );
		$updated = $this->db->update( $this->customers_table, array(
			'email' => $email,
			'info' => $info,
		), array( 'id' => $id ) );
		if ( $updated !== false ) {
			return $id;
		} else {
			return false;
		}
	}

	public function update_customer_payment_id( $id, $payment_id ) {
		return $this->db->update( $this->customers_table, array( 'payment_id' => $payment_id ), array( 'id' => $id ) );
	}

	public function get_customer_payment_id( $customer_id ) {
		return $this->db->get_var(
			$this->db->prepare(
				"
				SELECT payment_id
				FROM $this->customers_table
				WHERE id = %d
				",
				$customer_id
			)
		);
	}

	public function resa_update_customer_id( $resa_id, $customer_id ) {
		return $this->db->update( $this->resa_table, array( 'customer_id' => $customer_id ), array( 'id' => $resa_id ) );
	}

	public function get_customer_info( $id ) {
		$customer_info = $this->db->get_var(
			$this->db->prepare(
				"
				SELECT info
				FROM $this->customers_table
				WHERE id = %d
				",
				$id
			)
		);
		$customer_info = json_decode( $customer_info, true );
		return $customer_info;
	}

	public function get_all_customers() {
		$customers = $this->db->get_results( "SELECT * FROM $this->customers_table", ARRAY_A );
		$returned_customers = array();
		foreach ( $customers as $customer ) {
			$returned_customers[ $customer['id'] ] = $customer;
			$nb_resa = $this->db->get_var( "SELECT COUNT(*) FROM $this->resa_table WHERE customer_id = {$customer['id']} AND parent_id = 0");
			$nb_parents_resa = $this->db->get_var( "SELECT COUNT(*) FROM $this->parents_resa_table WHERE customer_id = {$customer['id']}");
			$returned_customers[ $customer['id'] ]['nb_resa'] = $nb_resa + $nb_parents_resa;
		}
		return $returned_customers;
	}

	public function get_details_form_labels() {
		$fields = $this->db->get_results(
			"
			SELECT *
			FROM $this->fields_table
			ORDER BY order_num
			"
		, ARRAY_A );
		$labels = array();
		foreach ( $fields as $field ) {
			$labels[ $field['id'] ] = $field['name'];
			if ( $field['has_choices'] ) {
				$choices = $this->get_field_choices( $field['id'] );
				foreach ( $choices as $choice ) {
					$labels[ $choice['id'] ] = $choice['name'] . ' <small>(' . $field['name'] . ')</small>';
				}
			}
		}
		return $labels;
	}

	public function get_additional_booking_info_form_fields() {
		$fields = $this->get_details_form_fields( 'booking' );
		$returned_fields = array();
		$info_type = array( 'text', 'email', 'number', 'textarea', 'select', 'radio', 'checkbox' );
		foreach ( $fields as $field ) {
			if ( $field['displayed'] == 'yes' && in_array( $field['type'], $info_type ) ) {
				$returned_fields[] = $field;
			}
		}
		return $returned_fields;
	}

	public function get_customer_form_fields() {
		$fields = $this->get_details_form_fields( 'customer' );
		$returned_fields = array();
		$info_type = array( 'text', 'email', 'number', 'textarea', 'select', 'radio', 'checkbox' );
		foreach ( $fields as $field ) {
			if ( $field['displayed'] == 'yes' && in_array( $field['type'], $info_type ) ) {
				$returned_fields[] = $field;
			}
		}
		return $returned_fields;
	}

	public function get_customer_form_fields_ids() {
		$fields_ids = array();
		$result = $this->db->get_results(
			"
			SELECT id
			FROM $this->fields_table
			WHERE data_about = 'customer'
			"
		, ARRAY_A );
		foreach ( $result as $row ) {
			$fields_ids[] = $row['id'];
		}
		return $fields_ids;
	}

	public function get_details_form_fields( $data_about = '' ) {
		if ( $data_about == '' ){
			$fields = $this->db->get_results(
				"
				SELECT *
				FROM $this->fields_table
				ORDER BY order_num
				"
			, ARRAY_A );
		} else {
			$fields = $this->db->get_results(
				$this->db->prepare(
					"
					SELECT *
					FROM $this->fields_table
					WHERE data_about = %s
					ORDER BY order_num
					",
					$data_about
				)
			, ARRAY_A );
		}
		$returned_fields = array();
		foreach ( $fields as $field ) {
			$choices = array();
			if ( $field['has_choices'] ) {
				$choices = $this->get_field_choices( $field['id'] );
			}
			$standard = 'no';
			$displayed = 'no';
			$admin_only = 'no';
			$required = 'no';
			if ( $field['standard'] ) {
				$standard = 'yes';
			}
			if ( $field['displayed'] ) {
				$displayed = 'yes';
			}
			if ( $field['admin_only'] ) {
				$admin_only = 'yes';
			}
			if ( $field['required'] ) {
				$required = 'yes';
			}
			$returned_field = array(
				'id' => $field['id'],
				'name' => $field['name'],
				'standard' => $standard,
				'type' => $field['type'],
				'displayed' => $displayed,
				'admin_only' => $admin_only,
				'required' => $required,
				'choices' => $choices,
				'data_about' => $field['data_about'],
				'column_width' => $field['column_width'],
			);
			$returned_fields[] = $returned_field;
		}
		return $returned_fields;
	}

	public function get_field_choices( $field_id ) {
		return $this->db->get_results(
			$this->db->prepare(
				"
				SELECT *
				FROM $this->fields_choices_table
				WHERE field_id = %s
				",
				$field_id
			)
			, ARRAY_A
		);
	}

	public function update_fields( $fields_data ) {
		$fields_data = json_decode( $fields_data, true );
		if ( ! $fields_data ) {
			return;
		}
		$this->db->query(
			"
			TRUNCATE $this->fields_table
			"
		);
		$this->db->query(
			"
			TRUNCATE $this->fields_choices_table
			"
		);
		$order_num = 1;
		foreach ( $fields_data['fields'] as $field ) {
			$has_choices = 0;
			if ( count( $field['choices'] ) > 0 ) {
				$has_choices = 1;
				foreach ( $field['choices'] as $choice ) {
					$choice_info = array(
						'id' => $choice['id'],
						'field_id' => $field['id'],
						'name' => $choice['name']
					);
					$this->db->insert( $this->fields_choices_table, $choice_info );
				}
			}
			$standard = 0;
			$displayed = 0;
			$admin_only = 0;
			$required = 0;
			if ( $field['standard'] == 'yes' ) {
				$standard = 1;
			}
			if ( $field['displayed'] == 'yes' ) {
				$displayed = 1;
			}
			if ( $field['admin_only'] == 'yes' ) {
				$admin_only = 1;
			}
			if ( $field['required'] == 'yes' ) {
				$required = 1;
			}
			$field_info = array(
				'id' => $field['id'],
				'name' => $field['name'],
				'standard' => $standard,
				'displayed' => $displayed,
				'admin_only' => $admin_only,
				'required' => $required,
				'type' => $field['type'],
				'has_choices' => $has_choices,
				'order_num' => $order_num,
				'data_about' => $field['data_about'],
				'column_width' => $field['column_width'],
			);
			$this->db->insert( $this->fields_table, $field_info );
			$order_num++;
		}
	}

	public function get_all_strings() {
		$strings = $this->get_all( 'strings' );
		$returned_strings = array();
		foreach ( $strings as $string ) {
			$returned_strings[ $string['id'] ][ $string['locale'] ] = $string['value'];
		}
		return $returned_strings;
	}

	public function get_strings( $is_admin = false ) {
		$strings = $this->db->get_results(
			"
			SELECT *
			FROM $this->strings_table
			WHERE locale = 'en_US'
			"
			, ARRAY_A
		);
		$default_strings = array();
		foreach ( $strings as $string ) {
			$default_strings[ $string['id'] ] = $string['value'];
		}
		if ( $is_admin && ( $is_admin == 'yes' ) ) {
			$locale = get_user_locale();
		} else {
			$locale = get_locale();
		}
		if ( function_exists( 'icl_object_id' ) && ! function_exists( 'pll_get_post' ) ) {
			global $sitepress;
			$locale = $sitepress->get_locale( ICL_LANGUAGE_CODE );
		}
		if ( $locale == 'en' ) {
			$locale = 'en_US';
		}
		if ( $locale == 'en_US' ) {
			return $default_strings;
		} else {
			$strings = $this->db->get_results(
				$this->db->prepare(
					"
					SELECT *
					FROM $this->strings_table
					WHERE locale = %s
					",
					$locale
				)
				, ARRAY_A
			);
			$locale_strings = array();
			foreach ( $strings as $string ) {
				$locale_strings[ $string['id'] ] = $string['value'];
			}
			$returned_strings = array();
			foreach ( $default_strings as $string_id => $string_value ) {
				if ( isset( $locale_strings[ $string_id ] ) && $locale_strings[ $string_id ] != '' ) {
					$returned_strings[ $string_id ] = $locale_strings[ $string_id ];
				} else {
					$returned_strings[ $string_id ] = $string_value;
				}
			}
			return $returned_strings;
		}
	}

	public function get_strings_by_locale( $locale ) {
		$strings = $this->db->get_results(
			$this->db->prepare(
				"
				SELECT *
				FROM $this->strings_table
				WHERE locale = %s
				",
				$locale
			)
			, ARRAY_A
		);
		$returned_strings = array();
		foreach ( $strings as $string ) {
			$returned_strings[ $string['id'] ] = $string['value'];
		}
		return $returned_strings;
	}

	public function get_string( $id, $locale = '' ) {
		if ( ! $locale ) {
			$locale = get_locale();
			if ( function_exists( 'icl_object_id' ) && ! function_exists( 'pll_get_post' ) ) {
				global $sitepress;
				$locale = $sitepress->get_locale( ICL_LANGUAGE_CODE );
			}
			if ( $locale == 'en' ) {
				$locale = 'en_US';
			}
		}
		$string = $this->db->get_var(
			$this->db->prepare(
				"
				SELECT value
				FROM $this->strings_table
				WHERE id = %s AND locale = %s
				",
				$id,
				$locale
			)
		);
		if ( ! $string ) {
			$string = $this->db->get_var(
				$this->db->prepare(
					"
					SELECT value
					FROM $this->strings_table
					WHERE id = %s AND locale = 'en_US'
					",
					$id
				)
			);
		}
		return $string;
	}

	public function update_strings( $strings ) {
		foreach ( $strings as $string ) {
			$this->db->query(
				$this->db->prepare(
					"
					INSERT INTO $this->strings_table
					(id, locale, value)
					VALUES (%s, %s, %s)
					ON DUPLICATE KEY UPDATE value = %s
					",
					$string['id'],
					$string['locale'],
					$string['value'],
					$string['value']
				)
			);
		}
	}

	public function get_all_rates() {
		return $this->db->get_results(
			"
			SELECT id, type, GROUP_CONCAT( DISTINCT accom_id ) as accom, all_accom, GROUP_CONCAT( DISTINCT season_id ) as seasons, all_seasons, GROUP_CONCAT( DISTINCT rule_id ) as rules, amount, nights
			FROM $this->rates_table
			LEFT JOIN $this->rates_rules_table
			ON $this->rates_table.id = $this->rates_rules_table.rate_id
			LEFT JOIN $this->rates_accom_table
			ON $this->rates_table.id = $this->rates_accom_table.rate_id
			LEFT JOIN $this->rates_seasons_table
			ON $this->rates_table.id = $this->rates_seasons_table.rate_id
			GROUP BY id
			"
		, ARRAY_A );
	}

	public function get_all_booking_rules() {
		$rules = $this->db->get_results(
			"
			SELECT id, name, type, check_in_days, check_out_days, minimum_stay, maximum_stay, all_accom, GROUP_CONCAT( DISTINCT accom_id ) as accom, all_seasons, GROUP_CONCAT( DISTINCT season_id ) as seasons, conditional_type
			FROM $this->booking_rules_table
			LEFT JOIN $this->booking_rules_accom_table
			ON $this->booking_rules_table.id = $this->booking_rules_accom_table.rule_id
			LEFT JOIN $this->booking_rules_seasons_table
			ON $this->booking_rules_table.id = $this->booking_rules_seasons_table.rule_id
			GROUP BY id
			"
		, ARRAY_A );
		foreach ( $rules as $i => $rule ) {
			if ( $rule['minimum_stay'] == - 1 ) {
				$rules[ $i ]['minimum_stay'] = '';
			}
			if ( $rule['maximum_stay'] == 9999 ) {
				$rules[ $i ]['maximum_stay'] = '';
			}
		}
		return $rules;
	}

	public function get_all_accom_booking_rules() {
		return $this->db->get_results(
			"
			SELECT type, check_in_days, check_out_days, minimum_stay, maximum_stay, conditional_type, GROUP_CONCAT( DISTINCT season_id ) as seasons, all_seasons
			FROM $this->booking_rules_table
			INNER JOIN $this->booking_rules_seasons_table
			ON $this->booking_rules_table.id = $this->booking_rules_seasons_table.rule_id
			WHERE all_accom = 1
			GROUP BY id
			"
		, ARRAY_A );
	}

	public function get_accom_booking_rules( $accom_id ) {
		return $this->db->get_results(
			$this->db->prepare(
				"
				SELECT type, check_in_days, check_out_days, minimum_stay, maximum_stay, conditional_type, GROUP_CONCAT( DISTINCT season_id ) as seasons, all_seasons
				FROM $this->booking_rules_table
				INNER JOIN $this->booking_rules_accom_table
				ON $this->booking_rules_table.id = $this->booking_rules_accom_table.rule_id
				INNER JOIN $this->booking_rules_seasons_table
				ON $this->booking_rules_table.id = $this->booking_rules_seasons_table.rule_id
				WHERE $this->booking_rules_accom_table.accom_id = %d
				GROUP BY id
				",
				$accom_id
			)
		, ARRAY_A );
	}

	public function get_rate_booking_rules() {
		return $this->db->get_results(
			"
			SELECT id, check_in_days, check_out_days, minimum_stay, maximum_stay, GROUP_CONCAT( DISTINCT season_id ) as seasons, all_seasons
			FROM $this->booking_rules_table
			INNER JOIN $this->booking_rules_accom_table
			ON $this->booking_rules_table.id = $this->booking_rules_accom_table.rule_id
			INNER JOIN $this->booking_rules_seasons_table
			ON $this->booking_rules_table.id = $this->booking_rules_seasons_table.rule_id
			WHERE $this->booking_rules_table.conditional_type = 'special_rate' OR $this->booking_rules_table.conditional_type = 'comp_and_rate'
			GROUP BY id
			",
			ARRAY_A
		);
	}

	public function get_discounts_rules() {
		return $this->db->get_results(
			"
			SELECT id, check_in_days, check_out_days, minimum_stay, maximum_stay
			FROM $this->booking_rules_table
			WHERE $this->booking_rules_table.conditional_type = 'discount'
			GROUP BY id
			"
		, ARRAY_A );
	}

	public function get_all_rate_booking_rules() {
		$rules = $this->db->get_results(
			"
			SELECT id, name, GROUP_CONCAT( DISTINCT season_id ) as seasons, all_seasons
			FROM $this->booking_rules_table
			LEFT JOIN $this->booking_rules_seasons_table
			ON $this->booking_rules_table.id = $this->booking_rules_seasons_table.rule_id
			WHERE $this->booking_rules_table.conditional_type = 'special_rate'
			OR $this->booking_rules_table.conditional_type = 'comp_and_rate'
			GROUP BY id
			"
			, ARRAY_A );
		$returned_rules = array();
		foreach ( $rules as $rule ) {
			$returned_rules[ $rule['id'] ] = array(
				'name' => $rule['name'],
				'seasons' => $rule['seasons'],
				'all_seasons' => $rule['all_seasons'],
			);
		}
		return $returned_rules;
	}

	public function get_all_discount_rules() {
		$rules = $this->db->get_results(
			"
			SELECT id, name
			FROM $this->booking_rules_table
			WHERE $this->booking_rules_table.conditional_type = 'discount'
			"
			, ARRAY_A );
		$returned_rules = array();
		foreach ( $rules as $rule ) {
			$returned_rules[ $rule['id'] ] = $rule['name'];
		}
		return $returned_rules;
	}

	public function get_all_coupon_rules() {
		$rules = $this->db->get_results(
			"
			SELECT id, name
			FROM $this->booking_rules_table
			WHERE $this->booking_rules_table.conditional_type = 'coupon'
			"
			, ARRAY_A );
		$returned_rules = array();
		foreach ( $rules as $rule ) {
			$returned_rules[ $rule['id'] ] = $rule['name'];
		}
		return $returned_rules;
	}

	public function get_rule_by_name( $rule_name ) {
		return $this->db->get_var(
			$this->db->prepare(
				"
				SELECT id
				FROM $this->booking_rules_table
				WHERE name = '%s'
				",
				$rule_name
			)
		);
	}

	public function get_rule_by_id( $rule_id ) {
		$rule = $this->db->get_results(
			$this->db->prepare(
				"
				SELECT *
				FROM $this->booking_rules_table
				WHERE id = '%d'
				",
				$rule_id
			), ARRAY_A
		);
		if ( $rule ) {
			return $rule[0];
		} else {
			return false;
		}
	}

	public function get_all_discounts() {
		return $this->db->get_results(
			"
			SELECT id, GROUP_CONCAT( DISTINCT accom_id ) as accom, all_accom, GROUP_CONCAT( DISTINCT season_id ) as seasons, all_seasons, GROUP_CONCAT( DISTINCT rule_id ) as rules, apply_to_type, amount, amount_type
			FROM $this->discounts_table
			LEFT JOIN $this->discounts_rules_table
			ON $this->discounts_table.id = $this->discounts_rules_table.discount_id
			LEFT JOIN $this->discounts_accom_table
			ON $this->discounts_table.id = $this->discounts_accom_table.discount_id
			LEFT JOIN $this->discounts_seasons_table
			ON $this->discounts_table.id = $this->discounts_seasons_table.discount_id
			GROUP BY id
			"
		, ARRAY_A );
	}

	public function site_has_coupons() {
		$results = $this->db->get_results(
			"
			SELECT id FROM $this->coupons_table
			"
		, ARRAY_A );
		if ( $results ) {
			return true;
		} else {
			return false;
		}
	}

	public function get_all_coupons() {
		return $this->db->get_results(
			"
			SELECT id, code, GROUP_CONCAT( DISTINCT accom_id ) as accom, all_accom, GROUP_CONCAT( DISTINCT season_id ) as seasons, all_seasons, GROUP_CONCAT( DISTINCT rule_id ) as rules, amount, amount_type, use_count, max_use_count, date_limit, multiple_use_per_customer
			FROM $this->coupons_table
			LEFT JOIN $this->coupons_rules_table
			ON $this->coupons_table.id = $this->coupons_rules_table.coupon_id
			LEFT JOIN $this->coupons_accom_table
			ON $this->coupons_table.id = $this->coupons_accom_table.coupon_id
			LEFT JOIN $this->coupons_seasons_table
			ON $this->coupons_table.id = $this->coupons_seasons_table.coupon_id
			GROUP BY id
			"
		, ARRAY_A );
	}

	public function get_coupon_info( $id ) {
		$coupon = $this->db->get_results(
			$this->db->prepare(
				"
				SELECT code, GROUP_CONCAT( DISTINCT accom_id ) as accom, all_accom, GROUP_CONCAT( DISTINCT season_id ) as seasons, all_seasons, GROUP_CONCAT( DISTINCT rule_id ) as rule, use_count, max_use_count, date_limit, multiple_use_per_customer, amount, amount_type
				FROM $this->coupons_table
				LEFT JOIN $this->coupons_rules_table
				ON $this->coupons_table.id = $this->coupons_rules_table.coupon_id
				LEFT JOIN $this->coupons_accom_table
				ON $this->coupons_table.id = $this->coupons_accom_table.coupon_id
				LEFT JOIN $this->coupons_seasons_table
				ON $this->coupons_table.id = $this->coupons_seasons_table.coupon_id
				WHERE id = %d
				GROUP BY id
				",
				$id
			)
		, ARRAY_A );
		if ( $coupon ) {
			return $coupon[0];
		} else {
			return false;
		}
	}

	public function increment_coupon_use( $id ) {
		$this->db->query(
			$this->db->prepare(
				"
				UPDATE $this->coupons_table
				SET use_count = use_count + 1
				WHERE id = %d
				",
				$id
			)
		);
	}

	public function get_coupon_ids_by_code( $code ) {
		$ids_rows = $this->db->get_results(
			$this->db->prepare(
				"
				SELECT id
				FROM $this->coupons_table
				WHERE code = %s
				ORDER BY id DESC
				",
				$code
			)
		, ARRAY_A );
		if ( $ids_rows ) {
			$ids = array();
			foreach ( $ids_rows as $id_row ) {
				$ids[] = $id_row['id'];
			}
			return $ids;
		} else {
			return false;
		}
	}

	public function get_discount_info( $rule_id, $accom_id, $season_id ) {
		$discounts = $this->db->get_results(
			$this->db->prepare(
				"
				SELECT apply_to_type, amount, amount_type
				FROM $this->discounts_table
				LEFT JOIN $this->discounts_rules_table
				ON $this->discounts_table.id = $this->discounts_rules_table.discount_id
				LEFT JOIN $this->discounts_accom_table
				ON $this->discounts_table.id = $this->discounts_accom_table.discount_id
				LEFT JOIN $this->discounts_seasons_table
				ON $this->discounts_table.id = $this->discounts_seasons_table.discount_id
				WHERE rule_id = %d
				AND accom_id = %d
				AND season_id = %d
				",
				$rule_id,
				$accom_id,
				$season_id
			)
		, ARRAY_A );
		if ( $discounts ) {
			return $discounts[0];
		} else {
			return false;
		}
	}

	public function get_season_names() {
		$seasons = $this->get_all( 'seasons' );
		$season_names = array();
		foreach ( $seasons as $season ) {
			$season_names[ 'season_' . $season['id'] ] = $season['name'];
		}
		return $season_names;
	}

	public function get_option_names() {
		$options = $this->get_all_options_with_choices();
		$option_names = array();
		foreach ( $options as $option ) {
			$option_names[ 'option_' . $option['id'] ] = $option['name'];
			foreach ( $option['choices'] as $choice ) {
				$option_names[ 'option_choice_' . $choice['id'] ] = $choice['name'];
			}
		}
		return $option_names;
	}

	public function get_fee_names() {
		$fees = $this->get_all( 'fees' );
		$fee_names = array();
		foreach ( $fees as $fee ) {
			$fee_names[ 'fee_' . $fee['id'] ] = $fee['name'];
		}
		return $fee_names;
	}

	public function get_fees( $accom_id ) {
		if ( $accom_id == 'parent_resa' ) {
			return $this->db->get_results(
				"
				SELECT *
				FROM $this->fees_table
				WHERE ( apply_to_type = 'global-fixed' )
				OR ( apply_to_type = 'extras-percentage' )
				OR (
					( apply_to_type = 'global-percentage' ) AND
					( ( include_in_price = 0 ) OR ( include_in_price = 1 ) )
				)

				"
			, ARRAY_A );
		} else {
			return $this->db->get_results(
				$this->db->prepare(
					"
					SELECT *
					FROM $this->fees_table
					INNER JOIN $this->fees_accom_table
					ON $this->fees_table.id = $this->fees_accom_table.fee_id
					WHERE accom_id = %d
					",
					$accom_id
				)
			, ARRAY_A );
		}
	}

	public function get_accom_based_fees( $accom_id ) {
		return $this->db->get_results(
			$this->db->prepare(
				"
				SELECT *
				FROM $this->fees_table
				INNER JOIN $this->fees_accom_table
				ON $this->fees_table.id = $this->fees_accom_table.fee_id
				WHERE accom_id = %d
				AND include_in_price = 1
				AND (
					apply_to_type = 'per-person' OR
					apply_to_type = 'per-person-per-day' OR
					apply_to_type = 'per-accom-per-day' OR
					apply_to_type = 'per-accom' OR
					apply_to_type = 'accom-percentage' OR
					apply_to_type = 'global-percentage'
				)
				",
				$accom_id
			)
		, ARRAY_A );
	}

	public function get_accom_included_fees( $accom_id ) {
		return $this->db->get_results(
			$this->db->prepare(
				"
				SELECT *
				FROM $this->fees_table
				INNER JOIN $this->fees_accom_table
				ON $this->fees_table.id = $this->fees_accom_table.fee_id
				WHERE accom_id = %d
				AND include_in_price = 2
				AND (
					apply_to_type = 'per-person' OR
					apply_to_type = 'per-person-per-day' OR
					apply_to_type = 'per-accom-per-day' OR
					apply_to_type = 'per-accom' OR
					apply_to_type = 'accom-percentage'
				)
				",
				$accom_id
			)
		, ARRAY_A );
	}

	public function get_accom_final_fees( $accom_id ) {
		return $this->db->get_results(
			$this->db->prepare(
				"
				SELECT *
				FROM $this->fees_table
				INNER JOIN $this->fees_accom_table
				ON $this->fees_table.id = $this->fees_accom_table.fee_id
				WHERE accom_id = %d
				AND include_in_price = 0
				AND (
					apply_to_type = 'per-person' OR
					apply_to_type = 'per-person-per-day' OR
					apply_to_type = 'per-accom-per-day' OR
					apply_to_type = 'per-accom' OR
					apply_to_type = 'accom-percentage'
				)
				",
				$accom_id
			)
		, ARRAY_A );
	}

	public function get_extras_fees_percentages() {
		$fees = $this->db->get_results(
			"
			SELECT amount
			FROM $this->fees_table
			WHERE include_in_price = 1
			AND
			( apply_to_type = 'extras-percentage' OR apply_to_type = 'global-percentage' )
			"
			, ARRAY_A
		);
		$percentages = array();
		foreach ( $fees as $fee ) {
			$percentages[] = $fee['amount'];
		}
		return $percentages;
	}

	public function get_final_fees() {
		$result = $this->db->get_results(
			"
			SELECT id, name, amount, apply_to_type, include_in_price, all_accom, GROUP_CONCAT( accom_id ) as accom
			FROM $this->fees_table
			LEFT JOIN $this->fees_accom_table
			ON $this->fees_table.id = $this->fees_accom_table.fee_id
			WHERE include_in_price = 0
			AND
			( apply_to_type = 'accom-percentage' OR
			apply_to_type = 'extras-percentage' OR
			apply_to_type = 'global-percentage' OR
			apply_to_type = 'global-fixed' )
			GROUP BY id
			"
		, ARRAY_A );
		return $result;
	}

	public function get_extras_included_fees() {
		$result = $this->db->get_results(
			"
			SELECT *
			FROM $this->fees_table
			WHERE include_in_price = 2
			AND apply_to_type = 'extras-percentage'
			"
		, ARRAY_A );
		return $result;
	}

	public function get_extras_final_fees() {
		$result = $this->db->get_results(
			"
			SELECT *
			FROM $this->fees_table
			WHERE include_in_price = 0
			AND apply_to_type = 'extras-percentage'
			"
		, ARRAY_A );
		return $result;
	}

	public function get_global_percent_final_fees() {
		$result = $this->db->get_results(
			"
			SELECT *
			FROM $this->fees_table
			WHERE include_in_price = 0 AND apply_to_type = 'global-percentage'
			"
		, ARRAY_A );
		return $result;
	}

	public function get_global_fixed_final_fees() {
		$result = $this->db->get_results(
			"
			SELECT *
			FROM $this->fees_table
			WHERE include_in_price = 0 AND apply_to_type = 'global-fixed'
			"
		, ARRAY_A );
		return $result;
	}

	public function get_global_included_fees() {
		$result = $this->db->get_results(
			"
			SELECT *
			FROM $this->fees_table
			WHERE include_in_price = 2
			AND ( apply_to_type = 'global-fixed' OR apply_to_type = 'global-percentage' )
			"
		, ARRAY_A );
		return $result;
	}

	public function get_accom_nums( $accom_id ) {
		$accom_num_name_list = $this->db->get_results(
			$this->db->prepare(
				"
				SELECT *
				FROM $this->accom_num_name_table
				WHERE accom_id = %d
				ORDER BY accom_num
				",
				$accom_id
			)
		, ARRAY_A );
		$accom_nums = array();
		foreach ( $accom_num_name_list as $accom_num_name ) {
			$accom_nums[] = $accom_num_name['accom_num'];
		}
		return $accom_nums;
	}

	public function get_accom_num_name( $accom_id ) {
		$accom_num_name_list = $this->db->get_results(
			$this->db->prepare(
				"
				SELECT *
				FROM $this->accom_num_name_table
				WHERE accom_id = %d
				ORDER BY accom_num
				",
				$accom_id
			)
		, ARRAY_A );
		$returned_accom_num_name = array();
		foreach ( $accom_num_name_list as $accom_num_name ) {
			$returned_accom_num_name[ $accom_num_name['accom_num'] ] = $accom_num_name[ 'num_name' ];
		}
		return $returned_accom_num_name;
	}

	public function get_accom_num_name_by_accom_num( $accom_id, $accom_num ) {
		$accom_num_name = $this->get_accom_num_name( $accom_id );
		return $accom_num_name[ $accom_num ];
	}

	public function update_accom_num_name( $accom_id, $new_accom_num_name ) {
		$previous_accom_num_name = $this->get_accom_num_name( $accom_id );
		foreach ( $previous_accom_num_name as $accom_num => $num_name ) {
			if ( ! array_key_exists( $accom_num, $new_accom_num_name ) ) {
				$this->db->delete( $this->resa_table, array( 'accom_id' => $accom_id, 'accom_num' => $accom_num ) );
				$this->db->delete( $this->accom_blocked_table, array( 'accom_id' => $accom_id, 'accom_num' => $accom_num ) );
				$this->db->delete( $this->accom_num_name_table, array( 'accom_id' => $accom_id, 'accom_num' => $accom_num ) );
				$this->db->delete( $this->ical_table, array( 'accom_id' => $accom_id, 'accom_num' => $accom_num ) );
			}
		}
		foreach ( $new_accom_num_name as $accom_num => $num_name ) {
			if ( array_key_exists( $accom_num, $previous_accom_num_name ) ) {
				if ( $num_name != $previous_accom_num_name[ $accom_num ] ) {
					$this->db->update(
						$this->accom_num_name_table,
						array(
							'num_name' => $num_name
						),
						array(
							'accom_id' => $accom_id,
							'accom_num' => $accom_num,
						)
					);
				}
			} else {
				$this->db->insert(
					$this->accom_num_name_table,
					array(
						'accom_id' => $accom_id,
						'accom_num' => $accom_num,
						'num_name' => $num_name
					)
				);

				$accom_all_num_blocked = $this->db->get_results(
					$this->db->prepare(
						"
						SELECT from_date, to_date, accom_all_ids
						FROM $this->accom_blocked_table
						WHERE accom_all_num = 1 AND accom_id = %d
						GROUP BY from_date, to_date, accom_all_ids
						",
						$accom_id
					)
				, ARRAY_A );
				foreach ( $accom_all_num_blocked as $blocked ) {
					$this->db->insert(
						$this->accom_blocked_table,
						array(
							'accom_id' => $accom_id,
							'accom_all_ids' => $blocked['accom_all_ids'],
							'accom_num' => $accom_num,
							'accom_all_num' => 1,
							'from_date' => $blocked['from_date'],
							'to_date' => $blocked['to_date'],
							'uid' => $this->get_uid()
						)
					);
				}
			}
		}
	}

	public function get_all_email_templates() {
		return $this->db->get_results(
			"
			SELECT id, name, to_address, reply_to_address, from_address, bcc_address, subject, message, format, media_attachments, lang, sending_type, action, schedules, resa_status, resa_payment_status, multiple_accom, all_accom, GROUP_CONCAT( accom_id ) as accom
			FROM $this->email_templates_table
			LEFT JOIN $this->email_templates_accom_table
			ON $this->email_templates_table.id = $this->email_templates_accom_table.email_template_id
			GROUP BY id
			"
		, ARRAY_A );
	}

	public function get_email_templates( $action, $accom_id, $lang ) {
		$lang_condition = '';
		if ( $lang != 'any' ) {
			$lang_condition = $this->db->prepare( ' AND lang = %s ', $lang );
		}
		$request =
		"
		SELECT * FROM $this->email_templates_table
		INNER JOIN $this->email_templates_accom_table
		ON $this->email_templates_table.id = $this->email_templates_accom_table.email_template_id
		WHERE accom_id = %d
		" . $lang_condition . "
		ORDER BY id DESC
		";
		$request = $this->db->prepare( $request, $accom_id );
		$results = $this->db->get_results( $request, ARRAY_A );
		$returned_email_templates = array();
		foreach ( $results as $row ) {
			$actions = explode( ',', $row['action'] );
			if ( in_array( $action, $actions ) ) {
				$returned_email_templates[] = $row;
			}
		}
		return $returned_email_templates;
	}

	public function get_document_templates() {
		return $this->db->get_results( "SELECT id, name, lang FROM $this->document_templates_table", ARRAY_A );
	}

	public function get_ical_sync() {
		return $this->db->get_results(
			"
			SELECT *
			FROM $this->ical_table
			"
	, ARRAY_A );
	}

	public function get_resa_id_by_uid( $uid ) {
		return $this->db->get_var(
			$this->db->prepare(
				"
				SELECT id
				FROM $this->resa_table
				WHERE uid = %s
				",
				$uid
			)
		);
	}

	public function get_resa_by_uid( $uid) {
		return $this->db->get_row(
			$this->db->prepare(
				"
				SELECT *
				FROM $this->resa_table
				WHERE uid = %s
				",
				$uid
		)
		, ARRAY_A );
	}

	public function get_resa_by_uid_by_accom_num( $uid, $accom_id, $accom_num) {
		return $this->db->get_row(
			$this->db->prepare(
				"
				SELECT *
				FROM $this->resa_table
				WHERE uid = %s
				AND accom_id = %d
				AND accom_num = %d
				",
				$uid, $accom_id, $accom_num
			)
		, ARRAY_A );
	}

	public function get_ical_synchro_id_by_accom_id( $accom_id ) {
		return $this->db->get_results(
			$this->db->prepare(
				"
				SELECT synchro_id
				FROM $this->ical_table
				WHERE accom_id = %d
				",
				$accom_id
			)
		, ARRAY_A );
	}

	public function get_ical_sync_by_accom_num( $accom_id, $accom_num ) {
		return $this->db->get_results(
			$this->db->prepare(
				"
				SELECT *
				FROM $this->ical_table
				WHERE accom_id = %d
				AND accom_num = %d
				",
				$accom_id, $accom_num
		)
		, ARRAY_A );
	}

	public function get_uids_by_synchro_id( $synchro_id ) {
		$uids= array();
		$today = current_time( 'Y-m-d' );
		$db_uids = $this->db->get_results(
			$this->db->prepare(
				"
				SELECT uid
				FROM $this->resa_table
				WHERE synchro_id = %s
				AND check_out >= %s
				",
				$synchro_id, $today
		)
		, ARRAY_A );
		foreach ( $db_uids as $db_uid ) {
			$uids[] = $db_uid['uid'];
		}
		return $uids;
	}

	public function add_ical_calendar( $accom_id, $accom_num, $synchro_url, $synchro_id, $calendar_id, $calendar_name ) {
		$this->db->insert( $this->ical_table, array(
			'accom_id' => $accom_id,
			'accom_num' => $accom_num,
			'synchro_url' => $synchro_url,
			'synchro_id' => $synchro_id,
			'calendar_id' => $calendar_id,
			'calendar_name' => $calendar_name, )
		);
	}

	public function update_ical_calendar( $synchro_url, $calendar_id, $calendar_name, $db_synchro_url ) {
		$this->db->update( $this->ical_table, array(
				'synchro_url' => $synchro_url,
				'calendar_id' => $calendar_id,
				'calendar_name' => $calendar_name,
			),
			array(
				'synchro_url' => $db_synchro_url,
			)
		);
	}

	public function update_ical_calendar_name( $calendar_name, $db_synchro_url ) {
		$this->db->update( $this->ical_table, array(
				'calendar_name' => $calendar_name,
			),
			array(
				'synchro_url' => $db_synchro_url,
			)
		);
	}

	public function delete_ical_calendar( $db_synchro_url ) {
		$this->db->delete( $this->ical_table, array(
				'synchro_url' => $db_synchro_url,
			)
		);
	}

	public function add_ical_sync_error( $error_type, $synchro_url, $uid, $calendar_name, $accom_id, $accom_num, $check_in, $check_out, $created_on ) {
		$this->db->insert( $this->sync_errors_table, array(
				'error_type' => $error_type,
				'synchro_url' => $synchro_url,
				'uid' => $uid,
				'calendar_name' => $calendar_name,
				'accom_id' => $accom_id,
				'accom_num' => $accom_num,
				'check_in' => $check_in,
				'check_out' => $check_out,
				'created_on' => $created_on,
			)
		);
	}

	public function get_sync_errors() {
		return $this->db->get_results(
			"
			SELECT *
			FROM $this->sync_errors_table
			ORDER BY created_on
			"
		, ARRAY_A );
	}

	public function get_ical_sync_error_by_uid( $uid ) {
		return $this->db->get_results(
			$this->db->prepare(
				"
				SELECT *
				FROM $this->sync_errors_table
				WHERE uid = %s
				",
				$uid
		)
		, ARRAY_A );
	}

	/*
	public function exist_invalid_url_sync_error( $synchro_url ) {
		return $this->db->get_results(
			$this->db->prepare(
				"
				SELECT *
				FROM $this->sync_errors_table
				WHERE synchro_url = %s
				AND error_type = 'invalid_url'
				",
				$calendar_id
		)
		, ARRAY_A );
	}
	*/

	public function delete_sync_errors() {
		$this->db->query(
			"
			TRUNCATE $this->sync_errors_table
			"
		);
	}

	public function log_email( $trigger_by, $trigger_by_details, $resa_id, $resa_is_parent, $template_id, $error = false ) {
		$log = array(
			'trigger_by' => $trigger_by,
			'trigger_by_details' => $trigger_by_details,
			'resa_id' => $resa_id,
			'resa_is_parent' => $resa_is_parent,
			'template_id' => $template_id,
			'sent_on' => current_time( 'mysql', 1 ),
		);
		if ( is_wp_error( $error ) ) {
			$log['error_msg'] = $error->get_error_message();
			$log['wp_error'] = var_export( $error, true );
		} else {
			$log['error_msg'] = $error;
		}
		$this->db->insert( $this->email_logs_table, $log );
	}

	public function get_email_logs() {
		return $this->db->get_results( "SELECT * FROM $this->email_logs_table ORDER BY sent_on DESC", ARRAY_A );
	}

	public function get_email_logs_by_resa_id( $resa_id, $resa_is_parent ) {
		return $this->db->get_results(
			$this->db->prepare(
				"
				SELECT *
				FROM $this->email_logs_table
				WHERE resa_id = %d AND
				resa_is_parent = %d
				ORDER BY sent_on DESC
				",
				$resa_id,
				$resa_is_parent
		)
		, ARRAY_A );
	}

	public function get_today_email_logs() {
		$today = substr( current_time( 'mysql', 1 ), 0, 10 );
		return $this->db->get_results( "SELECT * FROM $this->email_logs_table WHERE date( sent_on ) = '$today'", ARRAY_A );
	}

	public function delete_email_logs() {
		return $this->db->query(
			"
			TRUNCATE $this->email_logs_table
			"
		);
	}

	public function get_uid() {
		$uid = '';
		$uid .= 'D' . date( 'Y-m-d', current_time( 'timestamp', 1 ) );
		$uid .= 'T' . date( 'H:i:s', current_time( 'timestamp', 1 ) );
		$uid .= 'U' . uniqid();
		$site_url = site_url();
		if ( strlen( $site_url ) > 35 ) {
			$site_url = substr( $site_url, -35 );
		}
		$uid .= '@' . $site_url;
		return $uid;
	}

	public function get_all_pages() {
		return $this->db->get_results(
			"
			SELECT ID, post_title
			FROM $this->posts_table
			WHERE post_type = 'page' AND post_status = 'publish'
			ORDER BY post_title
			"
		, ARRAY_A );
	}

	public function last_query() {
		return $this->db->last_query;
	}

	public function last_error() {
		return $this->db->last_error;
	}

}