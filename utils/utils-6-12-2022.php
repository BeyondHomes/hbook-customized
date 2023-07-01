<?php
class HbUtils {

	private $hbdb;
	private $currencies;
	private $email_doc_locale;
	public $plugin_version;
	public $plugin_directory;
	public $plugin_url;
	public $strings_utils;

	public function __construct( $hbdb, $plugin_version ) {
		$this->hbdb = $hbdb;
		$this->plugin_directory = dirname( plugin_dir_path( __FILE__ ) );
		$this->plugin_url = dirname( plugin_dir_url( __FILE__ ) );
		require_once $this->plugin_directory . '/utils/currencies.php';
		$currencies = new HbCurrencies();
		$this->currencies = $currencies->currencies_list();
		require_once $this->plugin_directory . '/utils/hb-aq-resizer.php';
		require_once $this->plugin_directory . '/utils/strings-utils.php';
		$this->strings_utils = new HbStringsUtils( $hbdb );
		$this->plugin_version = $plugin_version;
		$this->email_doc_locale = '';
	}

	public function get_number_of_nights( $str_check_in, $str_check_out ) {
		$second_interval = strtotime( $str_check_out ) - strtotime( $str_check_in );
		$nb_nights = round( $second_interval / ( 3600 * 24 ) );
		return $nb_nights;
	}

	public function get_day_num( $str_date ) {
		$day = date( 'w', strtotime( $str_date ) );
		if ( $day == 0 ) {
			return 6;
		} else {
			return $day - 1;
		}
	}

	public function nb_accom() {
		$accom = $this->hbdb->get_all_accom_ids();
		return count( $accom );
	}

	public function get_currency_symbol( $currency = '' ) {
		if ( $currency == '' ) {
			$currency = get_option( 'hb_currency', 'USD' );
		}
		return $this->currencies[ $currency ]['symbol'];
	}

	public function currency_symbol_js() {
		?>
		<script type="text/javascript">
		/* <![CDATA[ */
		var hb_currency_symbol = '<?php echo( esc_html( $this->get_currency_symbol() ) ); ?>';
		/* ]]> */
		</script>
		<?php
	}

	public function currencies_code_name() {
		$currencies_code_name = array();
		foreach ( $this->currencies as $currency_code => $currency ) {
			if ( $currency_code != 'XXXX' ) {
				$currencies_code_name[ $currency_code ] = $currency['name'];
			}
		}
		return $currencies_code_name;
	}

	public function days_full_name() {
		$days = esc_html__( 'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday', 'hbook-admin' );
		$days = explode( ',', $days );
		return $days;
	}

	public function days_short_name() {
		$days = esc_html__( 'Mon,Tue,Wed,Thu,Fri,Sat,Sun', 'hbook-admin' );
		$days = explode( ',', $days );
		return $days;
	}

	public function round_price( $price ) {
		if ( get_option( 'hb_price_precision' ) != 'no_decimals' ) {
			return round( $price, 2 );
		} else {
			return round( $price );
		}
	}

	public function price_with_symbol( $price ) {
		if ( ! is_numeric( $price ) ) {
			return esc_html__( 'Error: price should be a numerical value.', 'hbook-admin' );
		}
		$negative_price_symbol = '';
		if ( $price < 0 ) {
			$negative_price_symbol = '-';
			$price = abs( $price );
		}
		if ( version_compare( PHP_VERSION, '5.4', '<' ) ) {
			global $wp_locale;
			$saved_thousands_sep = $wp_locale->number_format['thousands_sep'];
			if ( strlen( $saved_thousands_sep ) == 2 ) {
				$wp_locale->number_format['thousands_sep'] = ' ';
			}
		}
		if ( get_option( 'hb_price_precision' ) != 'no_decimals' ) {
			$price = number_format_i18n( $price, 2 );
		} else {
			if ( $price == round( $price ) ) {
				$price = number_format_i18n( round( $price ), 0 );
			} else {
				$price = number_format_i18n( $price, 2 );
			}
		}
		if ( version_compare( PHP_VERSION, '5.4', '<' ) ) {
			$wp_locale->number_format['thousands_sep'] = $saved_thousands_sep;
		}
		if ( get_option( 'hb_currency_position' ) == 'after' ) {
			return $negative_price_symbol . $price . ' ' . $this->get_currency_symbol();
		} else {
			return $negative_price_symbol . $this->get_currency_symbol() . $price;
		}
	}

	public function price_placeholder( $class = '' ) {
		if ( $class ) {
			$class = ' ' . $class;
		}
		$class = 'hb-price-placeholder' . $class;
		if ( get_option( 'hb_currency_position' ) == 'after' ) {
			return '<span class="' . $class . '"></span> ' . $this->get_currency_symbol();
		} else {
			return $this->get_currency_symbol() . '<span class="' . $class . '"></span>';
		}
	}

	public function price_with_currency_letters( $price ) {
		if ( get_option( 'hb_price_precision' ) != 'no_decimals' ) {
			$price = number_format_i18n( $price, 2 );
		} else {
			if ( $price == round( $price ) ) {
				$price = round( $price );
			} else {
				$price = number_format_i18n( $price, 2 );
			}
		}
		if ( get_option( 'hb_currency_position' ) == 'after' ) {
			return $price . ' ' . get_option( 'hb_currency', 'USD' );
		} else {
			return get_option( 'hb_currency', 'USD' ) . ' ' . $price;
		}
	}

	public function validate_date_and_people( $str_check_in, $str_check_out, $adults, $children ) {
		if ( ! is_numeric( $adults ) || ! is_numeric( $children ) ) {
			return array(
				'success' => false,
				'error_msg' => 'not num'
			);
		}
		if ( ( $str_check_in == '' ) || ( $str_check_out == '' ) ) {
			return array(
				'success' => false,
				'error_msg' => 'invalid dates'
			);
		}
		$check_in = date_create( $str_check_in );
		$check_out = date_create( $str_check_out );
		if ( ! $check_in || ! $check_out || ( $this->get_number_of_nights( $str_check_in, $str_check_out ) < 1 ) ) {
			return array(
				'success' => false,
				'error_msg' => 'invalid dates'
			);
		}
		return array(
			'success' => true
		);
	}

	public function load_jquery() {
		wp_enqueue_script( 'jquery' );
	}

	public function load_front_end_script( $script ) {
		if ( isset( $_GET['fl_builder'] ) ) {
			return;
		}
		switch ( $script ) {
			case 'utils' :
				$this->hb_enqueue_script( 'hb-front-end-utils-script', '/front-end/js/utils.js' );
				break;
			case 'availability' :
				$this->hb_enqueue_script( 'hb-availability-script', '/front-end/js/availability.js' );
				break;
			case 'accommodation-listing' :
				$this->hb_enqueue_script( 'hb-accommodation-list-script', '/front-end/js/accommodation-list.js' );
				break;
			case 'rates' :
				$this->hb_enqueue_script( 'hb-rates-script', '/front-end/js/rates.js' );
				break;
			case 'summary' :
				$this->hb_enqueue_script( 'hb-summary-script', '/front-end/js/summary.js' );
				break;
			case 'validate-form' :
				$this->hb_enqueue_script( 'hb-validate-form', '/front-end/js/jquery.form-validator.js' );
				break;
			case 'booking-form' :
				$this->hb_enqueue_script( 'hb-front-end-booking-form-script', '/front-end/js/booking-form.js' );
				break;
		}

	}

	public function load_datepicker() {
		static $datepicker_loaded;
		if ( ! $datepicker_loaded ) {
			$this->hb_enqueue_script( 'hb-datepicker-required-lib', '/utils/jq-datepick/js/jquery.plugin.min.js' );
			$this->hb_enqueue_script( 'hb-datepicker-script', '/utils/jq-datepick/js/jquery.datepick.min.js' );
			$this->hb_enqueue_script( 'hb-datepicker-launch', '/utils/jq-datepick/js/hb-datepick.js' );

			$this->hb_enqueue_style( 'hb-datepicker-style', '/utils/jq-datepick/css/hb-datepick.css' );

			$this->hb_script_var( 'hb-datepicker-script', 'hb_max_date', $this->get_max_date() );
			$this->hb_script_var( 'hb-datepicker-script', 'hb_min_date', $this->get_min_date() );

			$locale = $this->get_hb_known_locale();
			require_once $this->plugin_directory . '/utils/date-localization.php';
			$date_locale_info = new HbDateLocalization();

			$this->hb_script_var( 'hb-datepicker-script', 'hb_months_name', $date_locale_info->locale[ $locale ]['month_names'] );
			$this->hb_script_var( 'hb-datepicker-script', 'hb_day_names', $date_locale_info->locale[ $locale ]['day_names'] );
			$this->hb_script_var( 'hb-datepicker-script', 'hb_day_names_min', $date_locale_info->locale[ $locale ]['day_names_min'] );

			if ( is_admin() ) {
				$this->hb_enqueue_style( 'hb-datepicker-admin-style', '/utils/jq-datepick/css/hb-datepick-admin.css' );
				$date_format = 'yyyy-mm-dd';
				$first_day = '1';
			} else {
				$date_settings = json_decode( get_option( 'hb_front_end_date_settings' ), true );
				if ( isset( $date_settings[ get_locale() ] ) ) {
					$date_format = $date_settings[ get_locale() ]['date_format'];
					$first_day = $date_settings[ get_locale() ]['first_day'];
				} else {
					$date_format = $date_locale_info->locale[ $locale ]['date_format'];
					$first_day = $date_locale_info->locale[ $locale ]['first_day'];
				}
			}
			$this->hb_script_var( 'hb-datepicker-script', 'hb_date_format', $date_format );
			$this->hb_script_var( 'hb-datepicker-script', 'hb_first_day', $first_day );
			$this->hb_script_var( 'hb-datepicker-script', 'hb_is_rtl', $date_locale_info->locale[ $locale ]['is_rtl'] );

			$datepicker_loaded = true;
		}
	}

	public function hb_enqueue_script( $handle, $src ) {
		wp_enqueue_script( $handle, $this->plugin_url . $src, array(), $this->get_hb_file_version( $src ), true );
	}

	public function hb_enqueue_style( $handle, $src ) {
		wp_enqueue_style( $handle, $this->plugin_url . $src, array(), $this->get_hb_file_version( $src ) );
	}

	public function hb_script_var( $handle, $var, $data ) {
		if ( is_string( $data ) ) {
			$data = html_entity_decode( $data, ENT_QUOTES, 'UTF-8' );
			$data = '"' . $data . '"';
		} else if ( is_array( $data ) ) {
			foreach ( $data as $key => $value ) {
				if ( is_string( $value ) ) {
					$data[ $key ] = html_entity_decode( $value, ENT_QUOTES, 'UTF-8' );
				}
			}
			$data = json_encode( $data );
		}
		wp_add_inline_script( $handle, 'var ' . $var . ' = ' . $data . ';', 'before' );
	}

	private function get_hb_file_version( $file_src ) {
		if ( get_option( 'hbook_status' ) == 'dev' ) {
			return filemtime( $this->plugin_directory . $file_src );
		} else {
			return $this->plugin_version;
		}
	}

	public function hb_esc( $data ) {
		if ( is_array( $data ) ) {
			$returned_array = array();
			foreach ( $data as $key => $value ) {
				$returned_array[ $this->hb_esc( $key ) ] = $this->hb_esc( $value );
			}
			return $returned_array;
		} else {
			if ( strpos( $data, '<' ) > -1 ) {
				return wp_kses_post( $data );
			} else {
				return esc_html( $data );
			}
		}
	}

	public function hb_allowed_html_tags() {
		$allowed_html = array(
			'ul' => array(),
			'li' => array(),
			'form' => array(
				'action' => true,
				'data-search-only' => true,
				'method' => true,
				'novalidate' => true,
			),
			'h1' => array(),
			'h2' => array(),
			'h3' => array(),
			'h4' => array(),
			'h5' => array(),
			'h6' => array(),
			'div' => array(
				'data-accom-id' => true,
				'data-quantity' => true,
			),
			'p' => array(),
			'span' => array(),
			'input' => array(
				'autocomplete' => true,
				'checked' => true,
				'data-accom-url' => true,
				'data-bind'  => true,
				'data-has-redirection' => true,
				'data-price' => true,
				'data-validation' => true,
				'data-validation-qty' => true,
				'max' => true,
				'min' => true,
				'name' => true,
				'placeholder' => true,
				'type' => true,
				'value' => true,
			),
			'select' => array(
				'data-accom-id' => true,
				'data-bind' => true,
				'multiple' => true,
				'name' => true,
				'size' => true,
			),
			'option' => array(
				'disabled' => true,
				'selected' => true,
				'value' => true,
			),
			'textarea' => array(
				'data-validation' => true,
				'name' => true,
			),
			'label' => array(
				'for' => true,
			),
			'a' => array(
				'href' => true,
				'target' => true,
				'data-caption' => true,
				'data-width'=> true,
				'data-height' => true,
			),
			'b' => array(),
			'br' => array(),
			'hr' => array(),
			'img' => array(
				'alt' => true,
				'class' => true,
				'height' => true,
				'src' => true,
				'width' => true,
			),
			'style' => array(),
		);
		foreach ( $allowed_html as $key => $tag ) {
			$allowed_html[ $key ]['id'] = true;
			$allowed_html[ $key ]['class'] = true;
		}
		return $allowed_html;
	}

	public function get_hb_known_locale( $locale = '' ) {
		$known_locale = array(
			'af', 'am', 'ar_DZ', 'ar_EG', 'ar', 'az', 'bg', 'bs', 'ca', 'cs', 'cy', 'da', 'de_CH', 'de', 'el', 'en_AU', 'en_GB', 'en_NZ', 'eo', 'es_AR', 'es_PE', 'es', 'et', 'eu', 'fa', 'fi', 'fo', 'fr_CH', 'fr', 'gl', 'gu', 'he', 'hi_IN', 'hi', 'hr', 'hu', 'hy', 'id', 'is', 'it', 'ja', 'ka', 'km', 'ko', 'lt', 'lv', 'me_ME', 'me', 'mk', 'ml', 'ms', 'mt', 'nb_NO', 'nl_BE', 'nl', 'pl', 'pt_BR', 'pt', 'rm', 'ro', 'ru', 'sk', 'sl', 'sq', 'sr_SR', 'sr', 'sv', 'ta', 'th', 'tr', 'tt', 'uk', 'ur', 'vi', 'zh_CN', 'zh_HK', 'zh_TW'
		);
		if ( ! $locale ) {
			$locale = get_locale();
		}
		if ( ! in_array( $locale, $known_locale ) ) {
			$locale = substr( $locale, 0, 2 );
			if ( ! in_array( $locale, $known_locale ) ) {
				$locale = 'en_US';
			}
		}
		return $locale;
	}

	public function get_status_days( $accom_id, $minimum_stay = false ) {
		$taken_days_candidates = array();
		$taken_days = array();
		$max_date = $this->get_max_date();

		$future_resa = $this->hbdb->get_future_resa_dates( $accom_id );
		foreach ( $future_resa as $resa ) {
			if ( strtotime( $resa['check_in'] ) < strtotime( '-1 day' ) ) {
				$current_date = date( 'Y-m-d', strtotime( '-1 day' ) );
			} else {
				$current_date = $resa['check_in'];
			}
			if ( strtotime( $resa['check_out'] ) > strtotime( $max_date . ' +1 day' ) ) {
				$end_date = date( 'Y-m-d', strtotime( $max_date . ' +1 day' ) );
			} else {
				$end_date = $resa['check_out'];
			}
			while ( strtotime( $current_date ) < strtotime( $end_date ) ) {
				if ( ! in_array( $current_date, $taken_days_candidates ) ) {
					$taken_days_candidates[] = $current_date;
				}
				$current_date = date( 'Y-m-d', strtotime( $current_date . ' + 1 day' ) );
			}
		}

		if ( $accom_id == 'all' ) {
			$accom_ids = $this->hbdb->get_all_accom_ids();
			sort( $accom_ids );
		} else {
			$accom_ids = array( $accom_id );
		}

		$blocked_dates_all_num = array();
		$saved_future_blocked_dates = array();

		foreach ( $accom_ids as $blocked_accom_id ) {
			$accom_nums = $this->hbdb->get_accom_nums( $blocked_accom_id );
			$future_blocked_dates = $this->hbdb->get_future_blocked_dates( $blocked_accom_id );
			$saved_future_blocked_dates[ $blocked_accom_id ] = $future_blocked_dates;
			foreach ( $future_blocked_dates as $blocked_dates ) {
				$blocked_accom_nums = explode( ',', $blocked_dates['accom_nums'] );
				$all_num_key = $blocked_dates['from_date'] . '-' . $blocked_dates['to_date'];
				if ( ! isset( $blocked_dates_all_num[ $all_num_key ] ) ) {
					$blocked_dates_all_num[ $all_num_key ] = array();
				}
				if ( $blocked_accom_nums == $accom_nums ) {
					$blocked_dates_all_num[ $all_num_key ][] = $blocked_accom_id;
				}
			}
		}

		foreach ( $blocked_dates_all_num as $dates => $all_num_accom_ids ) {
			sort( $blocked_dates_all_num[ $dates ] );
		}

		foreach ( $accom_ids as $blocked_accom_id ) {
			foreach ( $saved_future_blocked_dates[ $blocked_accom_id ] as $blocked_dates ) {
				if ( strtotime( $blocked_dates['from_date'] ) < strtotime( '-1 day' ) ) {
					$current_date = date( 'Y-m-d', strtotime( '-1 day' ) );
				} else {
					$current_date = $blocked_dates['from_date'];
				}
				if ( strtotime( $blocked_dates['to_date'] ) > strtotime( $max_date . ' +1 day' ) ) {
					$end_date = date( 'Y-m-d', strtotime( $max_date . ' +1 day' ) );
				} else {
					$end_date = $blocked_dates['to_date'];
				}
				$all_num_key = $blocked_dates['from_date'] . '-' . $blocked_dates['to_date'];
				$all_num_accom_ids = $blocked_dates_all_num[ $all_num_key ];
				while ( strtotime( $current_date ) < strtotime( $end_date ) ) {
					if ( ! in_array( $current_date, $taken_days ) ) {
						if ( $all_num_accom_ids == $accom_ids ) {
							$taken_days[] = $current_date;
						} else if ( ! in_array( $current_date, $taken_days_candidates ) ) {
							$taken_days_candidates[] = $current_date;
						}
					}
					$current_date = date( 'Y-m-d', strtotime( $current_date . ' + 1 day' ) );
				}
			}
		}

		for ( $i = 0; $i < count( $taken_days_candidates ); $i++ ) {
			$taken_day = true;
			if ( ! in_array( $taken_days_candidates[ $i ], $taken_days ) ) {
				foreach ( $accom_ids as $tested_accom_id ) {
					if ( $this->hbdb->is_available_accom( $tested_accom_id, $taken_days_candidates[ $i ], date( 'Y-m-d', strtotime( $taken_days_candidates[ $i ] . ' + 1 day' ) ) ) ) {
						$taken_day = false;
						break;
					}
				}
				if ( $taken_day ) {
					$taken_days[] = $taken_days_candidates[ $i ];
				}
			}
		}

		if ( $accom_id == 'all' && $minimum_stay !== 0 ) {
			$minimum_stay = 1;
			$booking_rules = $this->hbdb->get_all_accom_booking_rules();
			foreach ( $booking_rules as $rule ) {
				if ( $rule['type'] == 'minimum_stay' && $rule['all_seasons'] && ( $rule['minimum_stay'] > $minimum_stay ) ) {
					$minimum_stay = $rule['minimum_stay'];
				}
			}
		} else if ( ! $minimum_stay ) {
			$minimum_stay = 1;
		}
		$status_days = array();
		for ( $i = 0; $i < count( $taken_days ); $i++ ) {
			if ( in_array( date( 'Y-m-d', strtotime( $taken_days[ $i ] . ' - 1 day' ) ), $taken_days ) ) {
				$status_days[ $taken_days[ $i ] ] = 'hb-day-fully-taken';
			} else {
				$status_days[ $taken_days[ $i ] ] = 'hb-day-taken-start';
				for ( $j = 1; $j < $minimum_stay; $j++ ) {
					$unavailable_check_in_date = date( 'Y-m-d', strtotime( $taken_days[ $i ] . ' - ' . $j . ' day' ) );
					if ( isset( $status_days[ $unavailable_check_in_date ] ) ) {
						$status_days[ $unavailable_check_in_date ] .= ' hb-day-no-check-in-min-stay';
					} else {
						$status_days[ $unavailable_check_in_date ] = 'hb-day-no-check-in-min-stay';
					}
				}
			}
			$day_taken_end_candidate = date( 'Y-m-d', strtotime( $taken_days[ $i ] . ' + 1 day' ) );
			if ( ! in_array( $day_taken_end_candidate, $taken_days ) ) {
				if ( isset( $status_days[ $day_taken_end_candidate ] ) ) {
					$status_days[ $day_taken_end_candidate ] .= ' hb-day-taken-end';
				} else {
					$status_days[ $day_taken_end_candidate ] = 'hb-day-taken-end';
				}
			}
		}

		if ( $max_date ) {
			for ( $i = 0; $i < $minimum_stay; $i++ ) {
				$unavailable_check_in_date = date( 'Y-m-d', strtotime( $max_date . ' - ' . $i . ' day' ) );
				if ( isset( $status_days[ $unavailable_check_in_date ] ) ) {
					$status_days[ $unavailable_check_in_date ] .= ' hb-day-no-check-in-min-stay';
				} else {
					$status_days[ $unavailable_check_in_date ] = 'hb-day-no-check-in-min-stay';
				}
			}
		}
		return $status_days;
	}

	public function format_date( $unformatted_date ) {
		$date_settings = json_decode( get_option( 'hb_front_end_date_settings' ), true );
		$locale = get_locale();
		if ( isset( $date_settings[ $locale ]['date_format'] ) ) {
			$date_format = $date_settings[ $locale ]['date_format'];
		} else {
			require_once $this->plugin_directory . '/utils/date-localization.php';
			$date_locale_info = new HbDateLocalization();
			$date_format = $date_locale_info->locale[ $this->get_hb_known_locale( $locale ) ]['date_format'];
		}
		$php_date_format = 'Y-m-d';
		$delimiters = array( '/', '.', '-' );
		foreach ( $delimiters as $delimiter ) {
			if ( strpos( $date_format, $delimiter ) ) {
				$date_format_elements = explode( $delimiter, $date_format );
				$php_date_format_elements = array();
				foreach ( $date_format_elements as $element ) {
					switch ( $element ) {
						case 'yyyy': $php_date_format_elements[] = 'Y'; break;
						case 'mm': $php_date_format_elements[] = 'm'; break;
						case 'dd': $php_date_format_elements[] = 'd'; break;
					}
					$php_date_format = implode( $delimiter, $php_date_format_elements );
				}
				break;
			}
		}
		return date( $php_date_format, strtotime( $unformatted_date ) );
	}

	public function email_doc_filter_locale() {
		return $this->email_doc_locale;
	}

	public function send_email( $action, $resa_id ) {
		if ( is_string( $resa_id ) && ( $resa_id[0] == '#' ) ) {
			$resa_id = substr( $resa_id, 1 );
			$resa = $this->hbdb->get_single( 'parents_resa', $resa_id );
			$resa['is_parent'] = 1;
			$children_resa = $this->hbdb->get_resa_by_parent_id( $resa_id );
			if ( ! $children_resa ) {
				return;
			} else {
				$resa['check_in'] = '';
				$resa['check_out'] = '';
				$resa['status'] = '';
				$children_resa_check_in = array_column( $children_resa, 'check_in' );
				$children_resa_check_out = array_column( $children_resa, 'check_out' );
				$children_resa_status = array_column( $children_resa, 'status' );
				if ( count( array_unique( $children_resa_check_in ) ) == 1 ) {
					$resa['check_in'] = $children_resa_check_in[0];
				}
				if ( count( array_unique( $children_resa_check_out ) ) == 1 ) {
					$resa['check_out'] = $children_resa_check_out[0];
				}
				if ( count( array_unique( $children_resa_status ) ) == 1 ) {
					$resa['status'] = $children_resa_status[0];
				}
			}
		} else {
			$resa = $this->hbdb->get_single( 'resa', $resa_id );
			$resa['is_parent'] = 0;
		}

		$this->email_doc_locale = $resa['lang'];
		remove_all_filters( 'locale' );
		add_filter( 'locale', array( $this, 'email_doc_filter_locale' ) );

		$email_templates = $this->hbdb->get_all_email_templates();
		foreach ( $email_templates as $key => $email_template ) {
			if (
				( ( $email_template['sending_type'] != 'event' ) && ( $email_template['sending_type'] != '' ) ) ||
				! in_array( $action, explode( ',', $email_template['action'] ) )
			 ) {
				unset( $email_templates[ $key ] );
			} else {
				$email_templates[ $key ]['resa_status'] = 'new,pending,confirmed,cancelled';
			}
		}

		if ( $email_templates ) {
			$email_templates = $this->filter_email_templates( $email_templates, $resa );
			if ( $email_templates ) {
				$this->send_email_generic( $resa_id, $resa['is_parent'], $email_templates, 'event', $action );
			}
		}

		remove_filter( 'locale', array( $this, 'email_doc_filter_locale' ) );
	}

	private function send_email_generic( $resa_id, $resa_is_parent, $email_tmpls, $trigger, $trigger_details ) {
		$emails_vars = array( 'to_address', 'reply_to_address', 'from_address', 'bcc_address', 'subject', 'message' );

		foreach ( $email_tmpls as $email_tmpl ) {
			if ( $email_tmpl['format'] == 'HTML' ) {
				$is_html_email = true;
			} else {
				$is_html_email = false;
			}

			$to_address_contains_customer_email_var = false;
			if ( strpos( $email_tmpl['to_address'], '[customer_email]' ) > -1 ) {
				$to_address_contains_customer_email_var = true;
			}

			foreach ( $emails_vars as $email_var ) {
				$$email_var = $this->replace_resa_vars_with_value( $resa_id, $is_html_email, $email_tmpl[ $email_var ], $resa_is_parent );
			}

			if ( strpos( $to_address, '[customer_email]' ) > -1 ) {
				$error_msg = esc_html__( '[customer_email] in "To address" could not be processed.', 'hbook-admin' );
				$this->hbdb->log_email( $trigger, $trigger_details, $resa_id, $resa_is_parent, $email_tmpl['id'], $error_msg );
				return;
			}
			if ( in_array( '', explode( ',', $to_address ) ) && $to_address_contains_customer_email_var ) {
				$error_msg = esc_html__( '[customer_email] in "To address" was empty.', 'hbook-admin' );
				$this->hbdb->log_email( $trigger, $trigger_details, $resa_id, $resa_is_parent, $email_tmpl['id'], $error_msg );
				return;
			}
			if ( $to_address == '' ) {
				if ( get_option( 'hb_email_default_address' ) ) {
					$to_address = get_option( 'hb_email_default_address' );
				} else {
					$to_address = get_option( 'admin_email' );
				}
			}

			$header = array();
			if ( $is_html_email ) {
				$header[] = 'Content-type: text/html';
			}

			if ( ! $from_address ) {
				$from_address = $this->get_default_from_address();
			}
			$header[] = 'From: ' . $from_address;

			if ( $reply_to_address ) {
				$header[] = 'Reply-To: ' . $reply_to_address;
			}

			if ( $bcc_address ) {
				$header[] = 'Bcc: ' . $bcc_address;
			}

			$attachment_ids = array();
			if ( $email_tmpl['media_attachments'] ) {
				$attachment_ids = explode( ',', $email_tmpl['media_attachments'] );
			}
			$attachments = $this->get_email_attachments( $attachment_ids );

			add_action(
				'wp_mail_failed',
				function ( $wp_mail_error ) use ( $trigger, $trigger_details, $resa_id, $resa_is_parent, $email_tmpl ) {
					$this->hbdb->log_email( $trigger, $trigger_details, $resa_id, $resa_is_parent, $email_tmpl['id'], $wp_mail_error );
				},
				10,
				1
			);

			$mail_sent = false;
			try {
				$mail_sent = wp_mail( $to_address, $subject, $message, $header, $attachments );
			} catch( phpmailerException $e ) {
			}

			if ( $mail_sent ) {
				$this->hbdb->log_email( $trigger, $trigger_details, $resa_id, $resa_is_parent, $email_tmpl['id'] );
			}
		}
	}

	public function send_not_automatic_email() {
		if ( is_string( $_POST['resa_id'] ) && ( $_POST['resa_id'][0] == '#' ) ) {
			$resa_is_parent = 1;
			$resa_id = intval( substr( $_POST['resa_id'], 1 ) );
			$resa = $this->hbdb->get_single( 'parents_resa', $resa_id );
		} else {
			$resa_is_parent = 0;
			$resa_id = intval( $_POST['resa_id'] );
			$resa = $this->hbdb->get_single( 'resa', $resa_id );
		}
		$customer = $this->hbdb->get_single( 'customers', $resa['customer_id'] );

		$email_tmpl = false;
		$is_html_email = false;
		$to_address = '';
		$from_address = '';
		$reply_to_address = '';
		$template_id = 0;

		if ( $_POST['email_template'] ) {
			$template_id = $_POST['email_template'];
			$this->email_doc_locale = $resa['lang'];
			remove_all_filters( 'locale' );
			add_filter( 'locale', array( $this, 'email_doc_filter_locale' ) );
			$email_tmpl = $this->hbdb->get_single( 'email_templates', $template_id );
			if ( $email_tmpl['format'] == 'HTML' ) {
				$is_html_email = true;
			}
			$from_address = $this->replace_resa_vars_with_value( $resa_id, $is_html_email, $email_tmpl['from_address'], $resa_is_parent );
			$reply_to_address = $this->replace_resa_vars_with_value( $resa_id, $is_html_email, $email_tmpl['reply_to_address'], $resa_is_parent );
		}

		$to_address = $this->replace_resa_vars_with_value( $resa_id, $is_html_email, stripslashes( $_POST['email_to_address'] ), $resa_is_parent );
		$subject = $this->replace_resa_vars_with_value( $resa_id, $is_html_email, stripslashes( $_POST['email_subject'] ), $resa_is_parent );
		$message = $this->replace_resa_vars_with_value( $resa_id, $is_html_email, stripslashes( $_POST['email_message'] ), $resa_is_parent );

		remove_filter( 'locale', array( $this, 'email_doc_filter_locale' ) );

		if ( ! $to_address ) {
			$to_address = $customer['email'];
		}

		$header = array();
		if ( $is_html_email ) {
			$header[] = 'Content-type: text/html';
		}

		if ( ! $from_address ) {
			$from_address = $this->get_default_from_address();
		}
		$header[] = 'From: ' . $from_address;

		if ( $reply_to_address ) {
			$header[] = 'Reply-To: ' . $reply_to_address;
		}

		$template_attachment_ids = array();
		$added_attachment_ids = array();
		if ( $email_tmpl && $email_tmpl['media_attachments'] ) {
			$template_attachment_ids = explode( ',', $email_tmpl['media_attachments'] );
		}
		if ( $_POST['email_attachments'] ) {
			$added_attachment_ids = explode( ',', $_POST['email_attachments'] );
		}
		$attachments = $this->get_email_attachments( array_merge( $template_attachment_ids, $added_attachment_ids ) );

		add_action(
			'wp_mail_failed',
			function ( $wp_mail_error ) use ( $resa_id, $resa_is_parent, $template_id ) {
				$this->hbdb->log_email( 'manual', '', $resa_id, $resa_is_parent, $template_id, $wp_mail_error );
			},
			10,
			1
		);

		$mail_sent = false;
		try {
			$mail_sent = wp_mail( $to_address, $subject, $message, $header, $attachments );
		} catch( phpmailerException $e ) {
		}

		if ( $mail_sent ) {
			$this->hbdb->log_email( 'manual', '', $resa_id, $resa_is_parent, $template_id );
			if ( $_POST['delete_attachments'] == 'true' ) {
				foreach ( $added_attachment_ids as $media_id ) {
					wp_delete_attachment( $media_id, true );
				}
			}
			return true;
		} else {
			return false;
		}
	}

	public function send_scheduled_emails() {
		$last_execution = get_option( 'hb_last_scheduled_emails_execution' );
		if ( $last_execution && ( time() - $last_execution < 60 ) ) {
			return;
		}
		update_option( 'hb_last_scheduled_emails_execution', time() );

		$email_templates = $this->hbdb->get_all_email_templates();
		foreach ( $email_templates as $key => $email_template ) {
			if ( $email_template['sending_type'] == 'scheduled' ) {
				$email_templates[ $key ]['schedules'] = explode( ',', $email_template['schedules'] );
			} else {
				unset( $email_templates[ $key ] );
			}
		}
		if ( ! $email_templates ) {
			return;
		}

		$resas = $this->hbdb->get_all( 'resa' );
		foreach ( $resas as $key => $resa ) {
			if ( $resa['parent_id'] != 0 ) {
				unset( $resas[ $key ] );
			} else {
				$resas[ $key ]['is_parent'] = 0;
			}
		}

		$parents_resa = $this->hbdb->get_all( 'parents_resa' );
		foreach ( $parents_resa as $key => $resa ) {
			$children_resa = $this->hbdb->get_resa_by_parent_id( $resa['id'] );
			if ( ! $children_resa ) {
				unset( $parents_resa[ $key ] );
			} else {
				$parents_resa[ $key ]['is_parent'] = 1;
				$parents_resa[ $key ]['check_in'] = '';
				$parents_resa[ $key ]['check_out'] = '';
				$parents_resa[ $key ]['status'] = '';
				$children_resa_check_in = array_column( $children_resa, 'check_in' );
				$children_resa_check_out = array_column( $children_resa, 'check_out' );
				$children_resa_status = array_column( $children_resa, 'status' );
				if ( count( array_unique( $children_resa_check_in ) ) == 1 ) {
					$parents_resa[ $key ]['check_in'] = $children_resa_check_in[0];
				}
				if ( count( array_unique( $children_resa_check_out ) ) == 1 ) {
					$parents_resa[ $key ]['check_out'] = $children_resa_check_out[0];
				}
				if ( count( array_unique( $children_resa_status ) ) == 1 ) {
					$parents_resa[ $key ]['status'] = $children_resa_status[0];
				}
			}
		}

		$resas = array_merge( $resas, $parents_resa );

		$today_email_logs = $this->hbdb->get_today_email_logs();
		$email_sent_today = array();
		foreach ( $today_email_logs as $log ) {
			$email_sent_today[] = $log['resa_id'] . '-' . $log['resa_is_parent'] . '-' . $log['template_id'];
		}

		$unfiltred_email_templates = $email_templates;
		foreach ( $resas as $resa ) {
			$email_templates = $this->filter_email_templates( $unfiltred_email_templates, $resa );
			if ( ! $email_templates ) {
				continue;
			}
			$today = substr( current_time( 'mysql', 1 ), 0, 10 );
			$check_in_pos = '';
			$check_out_pos = '';
			$check_in_diff_days = 0;
			$check_out_diff_days = 0;
			if ( $resa['check_in'] ) {
				$check_in_diff_days = $this->get_number_of_nights( $resa['check_in'], $today );
			}
			if ( $resa['check_out'] ) {
				$check_out_diff_days = $this->get_number_of_nights( $resa['check_out'], $today );
			}
			if ( $resa['check_in'] ) {
				if ( $check_in_diff_days == 0 ) {
					$check_in_pos = '0-in';
				} else if ( $check_in_diff_days < 0 ) {
					$check_in_pos = ( $check_in_diff_days * -1 ) . '-before-in';
				} else if ( $check_in_diff_days > 0 ) {
					$check_in_pos = $check_in_diff_days . '-after-in';
					if ( $check_out_diff_days >= 0 ) {
						$check_in_pos .= '-always';
					}
				}
			}
			if ( $resa['check_out'] ) {
				if ( $check_out_diff_days == 0 ) {
					$check_out_pos = '0-out';
				} else if ( $check_out_diff_days > 0 ) {
					$check_out_pos = $check_out_diff_days . '-after-out';
				} else if ( $check_out_diff_days < 0 ) {
					$check_out_pos = ( $check_out_diff_days * -1 ) . '-before-out';
					if ( $check_in_diff_days <= 0 ) {
						$check_out_pos .= '-always';
					}
				}
			}
			foreach ( $email_templates as $email_template ) {
				$trigger_details = '';
				if (
					$check_in_pos &&
					(
						in_array( $check_in_pos, $email_template['schedules'] ) ||
						in_array( $check_in_pos . '-always', $email_template['schedules'] )
					)
				) {
					$trigger_details = $check_in_pos;
				} else if (
					$check_out_pos &&
					(
						in_array( $check_out_pos, $email_template['schedules'] ) ||
						in_array( $check_out_pos . '-always', $email_template['schedules'] )
					)
				) {
					$trigger_details = $check_out_pos;
				}
				if (
					$trigger_details &&
					! in_array( $resa['id'] . '-' . $resa['is_parent'] . '-' . $email_template['id'], $email_sent_today )
				) {
					$this->send_email_generic( $resa['id'], $resa['is_parent'], array( $email_template ), 'scheduled', $trigger_details );
				}
			}
		}
	}

	private function filter_email_templates( $email_templates, $resa ) {
		if ( $resa['price'] == 0 ) {
			$resa_payment_status = 'paid';
		} else if ( $resa['paid'] == 0 ) {
			$resa_payment_status = 'unpaid';
		} else if ( get_option( 'hb_security_bond_online_payment' ) == 'yes' ) {
			if ( $resa['paid'] < $resa['price'] ) {
				$resa_payment_status = 'not_fully_paid';
			} else if (
				( $resa['paid'] < ( $resa['price'] + floatval( get_option( 'hb_security_bond_amount' ) ) ) ) &&
				( get_option( 'hb_security_bond_online_payment' ) == 'yes' ) &&
				( $check_out_diff_days < 0 )
			) {
				$resa_payment_status = 'bond_not_paid';
			} else {
				$resa_payment_status = 'paid';
			}
		} else {
			if ( $resa['paid'] >= $resa['price'] ) {
				$resa_payment_status = 'paid';
			} else {
				$resa_payment_status = 'not_fully_paid';
			}
		}
		foreach ( $email_templates as $key => $email_template ) {
			if (
				! in_array( $resa['status'], explode( ',', $email_template['resa_status'] ) ) ||
				! in_array( $resa_payment_status, explode( ',', $email_template['resa_payment_status'] ) ) ||
				( isset( $resa['accom_id'] ) && ! in_array( $resa['accom_id'], explode( ',', $email_template['accom'] ) ) ) ||
				( $resa['is_parent'] && ! $email_template['multiple_accom'] )
			) {
				unset( $email_templates[ $key ] );
			}
			if (
				$this->is_site_multi_lang() &&
				( $email_template['lang'] != 'all' ) &&
				( $email_template['lang'] != $resa['lang'] )
			) {
				unset( $email_templates[ $key ] );
			}
		}
		return $email_templates;
	}

	private function get_email_attachments( $media_attachments ) {
		$attachments = array();
		foreach ( $media_attachments as $media_id ) {
			$attached_file = get_attached_file( $media_id );
			if ( $attached_file ) {
				$attachments[] = $attached_file;
			}
		}
		return $attachments;
	}

	public function get_default_from_address() {
		$sitename = strtolower( $_SERVER['SERVER_NAME'] );
		if ( substr( $sitename, 0, 4 ) == 'www.' ) {
			$sitename = substr( $sitename, 4 );
		}
		if ( ! strpos( $sitename, '.' ) ) {
			$sitename .= '.com';
		}
		return get_option( 'blogname' ) . ' <no-reply@' . $sitename . '>';
	}

	public function get_email_logs_txt( $resa_id, $resa_is_parent ) {
		$email_logs = $this->hbdb->get_email_logs_by_resa_id( $resa_id, $resa_is_parent );
		$email_templates = $this->hbdb->get_all_email_templates();
		$email_template_names = array();
		foreach ( $email_templates as $email_template ) {
			$email_template_names[ $email_template['id'] ] = $email_template['name'];
		}
		$email_logs_txt = array();
		foreach ( $email_logs as $log ) {
			if ( ! $log['error_msg'] ) {
				$email_log = array(
					'sent_time' => $this->get_blog_datetime( $log['sent_on'] ),
					'template_name' => '',
					'trigger' => $this->get_email_log_trigger_txt( $log['trigger_by'], $log['trigger_by_details'] ),
				);
				if ( $log['template_id'] ) {
					if ( isset( $email_template_names[ $log['template_id'] ] ) ) {
						$email_log['template_name'] = $email_template_names[ $log['template_id'] ];
					}
				}
				$email_logs_txt[] = $email_log;
			}
		}
		return $email_logs_txt;
	}

	public function get_email_log_trigger_txt( $trigger_by, $trigger_by_details ) {
		$returned_txt = '';
		if ( $trigger_by == 'manual' ) {
			$returned_txt = esc_html__( 'Manually', 'hbook-admin' );
		} else if ( $trigger_by == 'event' ) {
			switch ( $trigger_by_details ) {
				case 'new_resa':
					$returned_txt = esc_html__( 'New reservation', 'hbook-admin' );
					$returned_txt .= ' (';
					$returned_txt .= esc_html__( 'from customers', 'hbook-admin' );
					$returned_txt .= ')';
					break;
				case 'new_resa_admin':
					$returned_txt = esc_html__( 'New reservation', 'hbook-admin' );
					$returned_txt .= ' (';
					$returned_txt .= esc_html__( 'from admin', 'hbook-admin' );
					$returned_txt .= ')';
					break;
				case 'confirmation_resa': $returned_txt = esc_html__( 'Reservation confirmation', 'hbook-admin' ); break;
				case 'cancellation_resa': $returned_txt = esc_html__( 'Reservation cancellation', 'hbook-admin' ); break;
			}
		} else if ( $trigger_by == 'scheduled' ) {
			$schedule = explode( '-', $trigger_by_details );
			if ( $schedule[0] == 0 ) {
				if ( $schedule[1] == 'in' ) {
					$returned_txt = esc_html__( 'Check-in day', 'hbook-admin' );
				} else {
					$returned_txt = esc_html__( 'Check-out day', 'hbook-admin' );
				}
			} else {
				$returned_txt = esc_html__( $schedule[0] );
				$returned_txt .= ' ';
				if ( $schedule[0] == 1 ) {
					$returned_txt .= esc_html__( 'day', 'hbook-admin' );
				} else {
					$returned_txt .= esc_html__( 'days', 'hbook-admin' );
				}
				$returned_txt .= ' ';
				if ( $schedule[1] == 'before' ) {
					$returned_txt .= esc_html__( 'before', 'hbook-admin' );
				} else {
					$returned_txt .= esc_html__( 'after', 'hbook-admin' );
				}
				$returned_txt .= ' ';
				if ( $schedule[2] == 'in' ) {
					$returned_txt .= esc_html__( 'check-in', 'hbook-admin' );
				} else {
					$returned_txt .= esc_html__( 'check-out', 'hbook-admin' );
				}
			}
		}
		return $returned_txt;
	}

	public function replace_resa_vars_with_value( $resa_id, $is_html, $text, $resa_is_parent = false ) {
		$identical_check_dates = true;
		if ( $resa_is_parent ) {
			$resa = $this->hbdb->get_single( 'parents_resa', $resa_id );
			$children_resa = $this->hbdb->get_resa_by_parent_id( $resa_id );
			$adults = 0;
			$children = 0;
			$child_check_in = $children_resa[0]['check_in'];
			$child_check_out = $children_resa[0]['check_out'];
			$multi_resa_ids = array();
			$multi_accom_name = array();
			$multi_accom_num = array();
			$multi_accom_coupon = '';
			$multi_accom_coupon_value = 0;
			foreach ( $children_resa as $child_resa ) {
				$multi_resa_ids[] = $child_resa['id'];
				$adults += $child_resa['adults'];
				$children += $child_resa['children'];
				if (
					( $child_resa['check_in'] != $child_check_in ) ||
					( $child_resa['check_out'] != $child_check_out )
				) {
					$identical_check_dates = false;
				}
				$multi_accom_name[] = get_the_title( $this->get_translated_post_id_by_locale( $child_resa['accom_id'], get_locale() ) );
				if ( $child_resa['accom_num'] ) {
					$multi_accom_num[] = $this->hbdb->get_accom_num_name_by_accom_num( $child_resa['accom_id'], $child_resa['accom_num'] );
				} else {
					$multi_accom_num[] = 'X';
				}
				if ( isset( $resa['coupon'] ) && $resa['coupon'] ) {
					$multi_accom_coupon = $resa['coupon'];
					$multi_accom_coupon_value += $resa['coupon_value'];
				}
			}
			$resa['adults'] = $adults;
			$resa['children'] = $children;
			if ( $identical_check_dates ) {
				$resa['check_in'] = $children_resa[0]['check_in'];
				$resa['check_out'] = $children_resa[0]['check_out'];
			}
			$multi_resa_ids = implode( ',', $multi_resa_ids );
			$multi_accom_name = implode( ', ', $multi_accom_name );
			if ( ( reset( $multi_accom_num ) == 'X' ) && ( count( array_unique( $multi_accom_num ) ) == 1 ) ) {
				$multi_accom_num = '';
			} else {
				$multi_accom_num = implode( ', ', $multi_accom_num );
			}
			$resa['coupon'] = $multi_accom_coupon;
			$resa['coupon_value'] = $multi_accom_coupon_value;
		} else {
			$resa = $this->hbdb->get_single( 'resa', $resa_id );
		}

		$resa_info = array( 'alphanum_id', 'adults', 'children', 'admin_comment', 'payment_type', 'payment_gateway', 'origin' );
		foreach ( $resa_info as $info ) {
			$text = str_replace( '[resa_' . $info . ']', $resa[ $info ], $text );
		}

		$text = str_replace( '[resa_persons]', $resa['adults'] + $resa['children'], $text );
		$text = str_replace( '[resa_received_on]', $this->get_blog_datetime( $resa['received_on'] ), $text );
		$text = str_replace( '[resa_received_on_date]', $this->format_date( substr( $resa['received_on'], 0, 10 ) ), $text );
		$text = str_replace( '[resa_coupon_code]', $resa['coupon'], $text );

		if ( $identical_check_dates ) {
			$text = str_replace( '[resa_check_in]', $this->format_date( $resa['check_in'] ), $text );
			$text = str_replace( '[resa_check_out]', $this->format_date( $resa['check_out'] ), $text );
			$nb_nights = $this->get_number_of_nights( $resa['check_in'], $resa['check_out'] );
			$text = str_replace( '[resa_number_of_nights]', $nb_nights, $text );
		} else {
			$text = str_replace( '[resa_check_in]', '', $text );
			$text = str_replace( '[resa_check_out]', '', $text );
			$text = str_replace( '[resa_number_of_nights]', '', $text );
		}

		if ( $resa_is_parent ) {
			$text = str_replace( '[resa_ids]', $multi_resa_ids, $text );
			$text = str_replace( '[resa_accommodation]', $multi_accom_name, $text );
			$text = str_replace( '[resa_accommodation_num]', $multi_accom_num, $text );
		} else {
			$text = str_replace( '[resa_ids]', '', $text );
			$accom_name = get_the_title( $this->get_translated_post_id_by_locale( $resa['accom_id'], get_locale() ) );
			$text = str_replace( '[resa_accommodation]', $accom_name, $text );
			if ( $resa['accom_num'] ) {
				$text = str_replace( '[resa_accommodation_num]', $this->hbdb->get_accom_num_name_by_accom_num( $resa['accom_id'], $resa['accom_num'] ), $text );
			} else {
				$text = str_replace( '[resa_accommodation_num]', '', $text );
			}
		}

		$bond = floatval( get_option( 'hb_security_bond_amount' ) );
		if ( ! $resa_is_parent ) {
			$accom_list_price_per_night = round( $resa['accom_price'] / $nb_nights, 2 );
		}
		if ( $is_html ) {
			$text = str_replace( '[resa_paid]', $this->price_with_symbol( $resa['paid'] ), $text );
			$text = str_replace( '[resa_price]', $this->price_with_symbol( $resa['price'] ), $text );
			if ( $resa_is_parent ) {
				$text = str_replace( '[resa_accom_list_price]', '', $text );
				$text = str_replace( '[resa_accom_list_price_per_night]', '', $text );
			} else {
				$text = str_replace( '[resa_accom_list_price]', $this->price_with_symbol( $resa['accom_price'] ), $text );
				$text = str_replace( '[resa_accom_list_price_per_night]', $this->price_with_symbol( $accom_list_price_per_night ), $text );
			}
			$text = str_replace( '[resa_deposit]', $this->price_with_symbol( $resa['deposit'] ), $text );
			$text = str_replace( '[resa_price_minus_deposit]', $this->price_with_symbol( $resa['price'] - $resa['deposit'] ), $text );
			$text = str_replace( '[resa_remaining_balance]', $this->price_with_symbol( $resa['price'] - $resa['paid'] ), $text );
			$text = str_replace( '[resa_price_including_bond]', $this->price_with_symbol( $resa['price'] + $bond ), $text );
			$text = str_replace( '[resa_deposit_including_bond]', $this->price_with_symbol( $resa['deposit'] + $bond ), $text );
			$text = str_replace( '[resa_remaining_balance_including_bond]', $this->price_with_symbol( $resa['price'] + $bond - $resa['paid'] ), $text );
			$text = str_replace( '[resa_bond]', $this->price_with_symbol( $bond ), $text );
			$text = str_replace( '[resa_coupon_amount]', $this->price_with_symbol( $resa['coupon_value'] ), $text );
			if ( $resa_is_parent ) {
				$resa_extras = '';
				foreach ( $children_resa as $child_resa ) {
					$child_resa_extras = $this->resa_options_markup( $child_resa['options'] );
					if ( $child_resa_extras ) {
						$child_resa_accom_name = get_the_title( $this->get_translated_post_id_by_locale( $child_resa['accom_id'], get_locale() ) );
						$resa_extras .= $child_resa_accom_name . '<br/>' . $child_resa_extras . '<br/>';
					}
				}
				$parent_resa_extras = $this->resa_options_markup( $resa['options'] );
				if ( $parent_resa_extras ) {
					$resa_extras .= $this->hbdb->get_string( 'global_options_title', $this->email_doc_locale ) . '<br/>' . $parent_resa_extras . '<br/>';
				}
			} else {
				$resa_extras = $this->resa_options_markup( $resa['options'] );
			}
			if ( $resa_extras ) {
				$resa_extras = $this->hbdb->get_string( 'chosen_options', $this->email_doc_locale ) . '<br/>' . $resa_extras;
			}
			$text = str_replace( '[resa_options]', $resa_extras, $text ); // Backward compatibility
			$text = str_replace( '[resa_extras]', $resa_extras, $text );
		} else {
			$text = str_replace( '[resa_paid]', $this->price_with_currency_letters( $resa['paid'] ), $text );
			$text = str_replace( '[resa_price]', $this->price_with_currency_letters( $resa['price'] ), $text );
			if ( $resa_is_parent ) {
				$text = str_replace( '[resa_accom_list_price]', '', $text );
				$text = str_replace( '[resa_accom_list_price_per_night]', '', $text );
			} else {
				$text = str_replace( '[resa_accom_list_price]', $this->price_with_currency_letters( $resa['accom_price'] ), $text );
				$text = str_replace( '[resa_accom_list_price_per_night]', $this->price_with_currency_letters( $accom_list_price_per_night ), $text );
			}
			$text = str_replace( '[resa_deposit]', $this->price_with_currency_letters( $resa['deposit'] ), $text );
			$text = str_replace( '[resa_price_minus_deposit]', $this->price_with_currency_letters( $resa['price'] - $resa['deposit'] ), $text );
			$text = str_replace( '[resa_remaining_balance]', $this->price_with_currency_letters( $resa['price'] - $resa['paid'] ), $text );
			$text = str_replace( '[resa_price_including_bond]', $this->price_with_currency_letters( $resa['price'] + $bond ), $text );
			$text = str_replace( '[resa_deposit_including_bond]', $this->price_with_currency_letters( $resa['deposit'] + $bond ), $text );
			$text = str_replace( '[resa_remaining_balance_including_bond]', $this->price_with_currency_letters( $resa['price'] + $bond - $resa['paid'] ), $text );
			$text = str_replace( '[resa_bond]', $this->price_with_currency_letters( $bond ), $text );
			$text = str_replace( '[resa_coupon_amount]', $this->price_with_currency_letters( $resa['coupon_value'] ), $text );
			if ( $resa_is_parent ) {
				$resa_extras = '';
				foreach ( $children_resa as $child_resa ) {
					$child_resa_extras = $this->resa_options_text( $child_resa['options'] );
					if ( $child_resa_extras ) {
						$child_resa_accom_name = get_the_title( $this->get_translated_post_id_by_locale( $child_resa['accom_id'], get_locale() ) );
						$resa_extras .= $child_resa_accom_name . "\n" . $child_resa_extras . "\n";
					}
				}
				$parent_resa_extras = $this->resa_options_text( $resa['options'] );
				if ( $parent_resa_extras ) {
					$resa_extras .= $this->hbdb->get_string( 'global_options_title', $this->email_doc_locale ) . "\n" . $parent_resa_extras . "\n";
				}
			} else {
				$resa_extras = $this->resa_options_text( $resa['options'] );
			}
			if ( $resa_extras ) {
				$resa_extras = $this->hbdb->get_string( 'chosen_options', $this->email_doc_locale ) . "\n" . $resa_extras;
			}
			$text = str_replace( '[resa_options]', $resa_extras, $text ); // Backward compatibility
			$text = str_replace( '[resa_extras]', $resa_extras, $text );
		}

		$resa_additional_info = json_decode( $resa['additional_info'], true );
		if ( is_array( $resa_additional_info ) ) {
			$resa_additional_info_fields = $this->hbdb->get_additional_booking_info_form_fields();
			foreach ( $resa_additional_info_fields as $field ) {
				$resa_additional_info_for_field = '';
				if ( isset( $resa_additional_info[ $field['id'] ] ) ) {
					$resa_additional_info_for_field = $resa_additional_info[ $field['id'] ];
				}
				$text = str_replace( '[resa_' . $field['id'] . ']', $resa_additional_info_for_field, $text );
			}
		}

		$customer = $this->hbdb->get_single( 'customers', $resa['customer_id'] );
		if ( $customer ) {
			$text = str_replace( '[customer_id]', $customer['id'], $text );
			$customer_info = json_decode( $customer['info'], true );
			if ( is_array( $customer_info ) ) {
				$customer_fields = $this->hbdb->get_customer_form_fields();
				foreach ( $customer_fields as $field ) {
					$customer_info_for_field = '';
					if ( isset( $customer_info[ $field['id'] ] ) ) {
						$customer_info_for_field = $customer_info[ $field['id'] ];
					}
					$text = str_replace( '[customer_' . $field['id'] . ']', $customer_info_for_field, $text );
					$text = str_replace( '[' . $field['id'] . ']', $customer_info_for_field, $text );
				}
			}
		}

		if ( $resa_is_parent ) {
			$text = str_replace( '[resa_id]', '#' . $resa['id'], $text );
		} else {
			$text = str_replace( '[resa_id]', $resa['id'], $text );
		}

		$resa_invoice_id = get_option( 'hb_resa_invoice_id' );
		if ( $resa_invoice_id ) {
			$current_year = substr( current_time( 'mysql', 1 ), 0, 4 );
			$current_month = substr( current_time( 'mysql', 1 ), 5, 2 );
			$resa_invoice_id = str_replace( '%year', $current_year, $resa_invoice_id );
			$resa_invoice_id = str_replace( '%month', $current_month, $resa_invoice_id );
			$resa_invoice_id = str_replace( '%counter', $resa['invoice_counter'], $resa_invoice_id );
		}
		$text = str_replace( '[resa_invoice_id]', $resa_invoice_id, $text );

		$text = str_replace( '[today_date]', $this->format_date( current_time( 'mysql', 1 ) ), $text );
		$text_days = array( '_days_before_', '_days_after_' );
		$text_checks = array( 'check_in', 'check_out' );
		foreach ( $text_checks as $text_check ) {
			foreach ( $text_days as $text_day ) {
				while ( preg_match( '/\[resa_(\d+)' . $text_day . $text_check . '\]/', $text, $occurence ) ) {
					if ( strpos( $text_day, 'before' ) ) {
						$math_sign = '- ';
					} else {
						$math_sign = '+ ';
					}	
					$value_text = date( 'Y-m-d', strtotime( $math_sign . $occurence[1] . ' day', strtotime( $resa[ $text_check ] ) ) );
					$text = str_replace( $occurence[0], $this->format_date( $value_text ), $text );
				}
			}
		}

		if ( strpos( $text, '[resa_invoice_table]' ) !== false ) {
			$invoice_table = $this->get_invoice_table( $resa, $resa_is_parent );
			$text = str_replace( '[resa_invoice_table]', $invoice_table, $text );
		}

		return $text;
	}

	private function get_invoice_table( $resa, $resa_is_parent ) {
		if ( ! $resa_is_parent && ( floatval( $resa['accom_price'] ) < 0 ) ) {
			return '';
		}

		$style = '<style type="text/css">';
		$style .= '.invoice-table { border-collapse: collapse; font-size: 14px; line-height: 24px; margin-top: 31px; width: 100%; }';
		$style .= '.invoice-table th { background: #ddd; font-size: 22px; font-weight: bold; height: 46px; padding: 12px 16px; text-align: center; }';
		$style .= '.invoice-table th, .invoice-table td { border: 1px solid #ddd; }';
		$style .= '.invoice-table td { height: 48px; padding: 12px 16px; }';
		$style .= '.invoice-table td.empty { border: none; width: 50%; }';
		$style .= '.invoice-table td.multi-accom-empty { border: none; width: 48%; }';
		$style .= '.invoice-table td.multi-accom-indent { background: #eee; border-bottom: none; border-top: none; padding: 0; width: 2%; }';
		$style .= '.invoice-table .multi-accom-separator td { background: #ddd; height: 2px; padding: 0; }';
		$style .= '.invoice-table td.subpart-title { background: #eee; font-size: 18px; font-weight: bold; height: 46px; text-align: center; }';
		$style .= '.invoice-table td.subsubpart-title { background: #eee; font-weight: bold; }';
		$style .= '.total td { font-size: 18px; height: 46px; }';
		$style .= '.subtotal .amount, .total .amount { position: relative; }';
		$style .= '.subtotal .amount:before, .total .amount:before { background: #ddd; content: ""; display: block; left: 0; position: absolute; right: 0; top: 0; }';
		$style .= '.subtotal .amount:before { height: 3px; }';
		$style .= '.total .amount:before { height: 7px; }';
		$style .= '.total td { font-size: 16px; text-transform: uppercase; }';
		$style .= '.total td.multi-accom-total { font-size: 14px; }';
		$style .= '.desc-head { width: 75%; }';
		$style .= '.amount-head { width: 25%; }';
		$style .= '.amount, .fee-final, .fee-included, .subtotal-text, .coupon-text, .discount-text, .total-text { text-align: right; }';
		$style .= '.subtotal-text, .total-text, .subtotal .amount, .total .amount { font-weight: bold }';
		$style .= '</style>';

		$table = '<table class="invoice-table">';

		$table .= '<tr>';
		$table .= '<th colspan="3" class="desc-head">';
		$table .= $this->hbdb->get_string( 'table_invoice_head_description', $this->email_doc_locale );
		$table .= '</th>';
		$table .= '<th class="amount-head">';
		$table .= $this->hbdb->get_string( 'table_invoice_head_amount', $this->email_doc_locale );
		$table .= '</th>';
		$table .= '</tr>';

		$admin_fee_names = array();
		$fees = $this->hbdb->get_all( 'fees' );
		foreach ( $fees as $fee ) {
			$admin_fee_names[ $fee['id'] ] = $fee['name'];
		}

		$extras_and_fees_details_strings = array(
			'price_breakdown_night_one' => $this->hbdb->get_string( 'price_breakdown_night_one', $this->email_doc_locale ),
			'price_breakdown_nights_several' => $this->hbdb->get_string( 'price_breakdown_nights_several', $this->email_doc_locale ),
			'fee_details_adult_one' => $this->hbdb->get_string( 'fee_details_adult_one', $this->email_doc_locale ),
			'fee_details_adults_several' => $this->hbdb->get_string( 'fee_details_adults_several', $this->email_doc_locale ),
			'fee_details_child_one' => $this->hbdb->get_string( 'fee_details_child_one', $this->email_doc_locale ),
			'fee_details_children_several' => $this->hbdb->get_string( 'fee_details_children_several', $this->email_doc_locale ),
			'fee_details_persons'  => $this->hbdb->get_string( 'fee_details_persons', $this->email_doc_locale ),
		);

		$global_final_added_fees = array();
		$fees = json_decode( $resa['fees'], true );
		if ( $fees ) {
			foreach ( $fees as $fee ) {
				if ( ( $fee['apply_to_type'] == 'global-fixed' ) ) {
					$global_final_added_fees[] = $fee;
				}
			}
		}
		if ( $resa_is_parent ) {
			$children_resa = $this->hbdb->get_resa_by_parent_id( $resa['id'] );
			$resa['accom_id'] = 'parent_resa';
			$resa['accom_price'] = 0;
			$children_resa[] = $resa;
		} else {
			$children_resa = array( $resa );
		}

		$all_accom_total = 0;

		foreach ( $children_resa as $resa ) {
			$accom_added_fees = array();
			$accom_final_added_fees = array();
			$accom_included_fees = array();
			$extras_added_fees = array();
			$extras_final_added_fees = array();
			$extras_included_fees = array();
			$final_added_fees = array();
			$final_included_fees = array();
			$fees = json_decode( $resa['fees'], true );
			$fixed_fee_types = array( 'per-person', 'per-accom', 'per-person-per-day', 'per-accom-per-day' );
			if ( $fees ) {
				foreach ( $fees as $fee ) {
					if ( $fee['apply_to_type'] == 'accom-percentage' ) {
						if ( $fee['include_in_price'] == 0 ) {
							$accom_final_added_fees[] = $fee;
						} else if ( $fee['include_in_price'] == 1 ) {
							$accom_added_fees[] = $fee;
						} else if ( $fee['include_in_price'] == 2 ) {
							$accom_included_fees[] = $fee;
						}
					} else if ( $fee['apply_to_type'] == 'extras-percentage' ) {
						if ( $fee['include_in_price'] == 0 ) {
							$extras_final_added_fees[] = $fee;
						} else if ( $fee['include_in_price'] == 1 ) {
							$extras_added_fees[] = $fee;
						} else if ( $fee['include_in_price'] == 2 ) {
							$extras_included_fees[] = $fee;
						}
					} else if ( ( $fee['apply_to_type'] == 'global-percentage' ) ) {
						if ( $fee['include_in_price'] == 0 ) {
							$final_added_fees[] = $fee;
						} else if ( $fee['include_in_price'] == 1 ) {
							$accom_added_fees[] = $fee;
							$extras_added_fees[] = $fee;
						} else if ( $fee['include_in_price'] == 2 ) {
							$final_included_fees[] = $fee;
						}
					} else if ( in_array( $fee['apply_to_type'], $fixed_fee_types ) ) {
						if ( $fee['include_in_price'] == 0 ) {
							$final_added_fees[] = $fee;
						} else if ( $fee['include_in_price'] == 1 ) {
							$accom_added_fees[] = $fee;
						} else if ( $fee['include_in_price'] == 2 ) {
							$accom_included_fees[] = $fee;
						}
					}
				}
				if ( ! $resa_is_parent ) {
					$final_added_fees = array_merge( $final_added_fees, $global_final_added_fees );
				}
			}

			$extras_block = '';
			$admin_extra_names = $this->hbdb->get_option_names();
			$extras_price = 0;
			$extras_fees_total = 0;

			$nb_paid_extras = 0;
			$chosen_extras = json_decode( $resa['options'], true );
			if ( $chosen_extras ) {
				foreach ( $chosen_extras as $extra_id => $extra_info ) {
					$extra_calculated_values = $this->calculate_fees_extras_values( $resa, 0, $extra_info, $extras_and_fees_details_strings );
					if ( $extra_calculated_values['price'] != 0 ) {
						$extras_block .= '<tr>';
						if ( $resa_is_parent ) {
							$extras_block .= '<td class="multi-accom-indent"></td><td colspan="2">';
						} else {
							$extras_block .= '<td colspan="3">';
						}
						$extras_block .= $this->get_extra_name_for_invoice( $extra_id, $admin_extra_names, $extra_info );
						if ( $extra_calculated_values['details'] ) {
							$extras_block .= ' (' . $extra_calculated_values['details'] . ')';
						}
						$extras_block .= '</td>';
						$extras_block .= '<td class="amount">';
						$extras_block .= $this->price_with_symbol( $extra_calculated_values['price'] );
						$extras_block .= '</td>';
						$extras_block .= '</tr>';
						$extras_price += $extra_calculated_values['price'];
						$nb_paid_extras++;
					}
				}

				if ( $extras_price ) {
					if ( ( $nb_paid_extras > 1 ) && $extras_added_fees ) {
						$extras_block .= '<tr class="subtotal">';
						$extras_block .= '<td colspan="3" class="subtotal-text">';
						$extras_block .= $this->hbdb->get_string( 'table_invoice_extras_subtotal', $this->email_doc_locale );
						$extras_block .= '</td>';
						$extras_block .= '<td class="amount">';
						$extras_block .= $this->price_with_symbol( $extras_price );
						$extras_block .= '</td>';
						$extras_block .= '</tr>';
					}

					if ( $extras_added_fees ) {
						$extras_block .= $this->get_fee_rows_for_invoice( $resa, $resa_is_parent, $extras_added_fees, $admin_fee_names, $extras_and_fees_details_strings, $extras_price, false, $extras_fees_total );
					}

					if ( ( $nb_paid_extras > 1 ) || $extras_fees_total ) {
						$extras_block .= '<tr class="subtotal">';
						if ( $resa_is_parent ) {
							$extras_block .= '<td class="multi-accom-indent"></td><td colspan="2" class="subtotal-text">';
						} else {
							$extras_block .= '<td colspan="3" class="subtotal-text">';
						}
						$extras_block .= $this->hbdb->get_string( 'table_invoice_extras_total', $this->email_doc_locale );
						$extras_block .= '</td>';
						$extras_block .= '<td class="amount">';
						$extras_block .= $this->price_with_symbol( $extras_price + $extras_fees_total );
						$extras_block .= '</td>';
						$extras_block .= '</tr>';
					}
					if ( $extras_price ) {
						$extras_block .= $this->get_fee_rows_for_invoice( $resa, $resa_is_parent, $extras_included_fees, $admin_fee_names, $extras_and_fees_details_strings, $extras_price + $extras_fees_total );
					}

					if ( $resa_is_parent ) {
						if ( $resa['accom_id'] != 'parent_resa' ) {
							$extras_block = $this->get_subsubpart_title_for_invoice( $this->hbdb->get_string( 'table_invoice_extras_title', $this->email_doc_locale ), $resa_is_parent ) . $extras_block;
						}
					} else {
						$extras_block = $this->get_subpart_title_for_invoice( $this->hbdb->get_string( 'table_invoice_extras_title', $this->email_doc_locale ), $resa_is_parent ) . $extras_block;
					}
				}
			}

			if ( $resa['accom_id'] != 'parent_resa' ) {
				$accom_name = get_the_title( $this->get_translated_post_id_by_locale( $resa['accom_id'], $this->email_doc_locale ) );
			}

			if ( $resa_is_parent ) {
				if ( $resa['accom_id'] == 'parent_resa' ) {
					if ( $extras_price ) {
						$table .= $this->get_subpart_title_for_invoice( $this->hbdb->get_string( 'table_invoice_global_extras_title', $this->email_doc_locale ) );
					}
				} else {
					$accom_num = '';
					if ( $resa['accom_num'] ) {
						$accom_num .= ' (';
						$accom_num .= $this->hbdb->get_accom_num_name_by_accom_num( $resa['accom_id'], $resa['accom_num'] );
						$accom_num .= ')';
					}
					$table .= $this->get_subpart_title_for_invoice( $accom_name . $accom_num );
				}
			} else if ( $extras_price ) {
				$table .= $this->get_subpart_title_for_invoice( $this->hbdb->get_string( 'table_invoice_accom_title', $this->email_doc_locale ) );
			}

			$accom_discount_value = 0;
			$accom_fees_total = 0;
			if ( $resa['accom_id'] != 'parent_resa' ) {
				$table .= '<tr>';
				if ( $resa_is_parent ) {
					$table .= '<td class="multi-accom-indent"></td><td colspan="2">';
					$table .= $this->hbdb->get_string( 'table_invoice_accom_title', $this->email_doc_locale );
				} else {
					$table .= '<td colspan="3">';
					$table .= $accom_name;
					if ( $resa['accom_num'] ) {
						$table .= ' (';
						$table .= $this->hbdb->get_accom_num_name_by_accom_num( $resa['accom_id'], $resa['accom_num'] );
						$table .= ')';
					}
				}
				$table .= '</td>';
				$table .= '<td class="amount">';
				$table .= $this->price_with_symbol( $resa['accom_price'] );
				$table .= '</td>';
				$table .= '</tr>';

				$discount = json_decode( $resa['discount'], true );

				if ( $discount['accom'] && $discount['accom']['amount'] ) {
					$table .= $this->get_discount_row_for_invoice( $discount['accom'], $resa_is_parent, $accom_discount_value, $resa['accom_price'] );
				}
				$resa['accom_price'] -= $accom_discount_value;

				$accom_added_fees_block = $this->get_fee_rows_for_invoice( $resa, $resa_is_parent, $accom_added_fees, $admin_fee_names, $extras_and_fees_details_strings, $resa['accom_price'], false, $accom_fees_total );
				$accom_included_fees_block = $this->get_fee_rows_for_invoice( $resa, $resa_is_parent, $accom_included_fees, $admin_fee_names, $extras_and_fees_details_strings, $resa['accom_price'] + $accom_fees_total );

				if ( $accom_discount_value && $accom_fees_total ) {
					$table .= '<tr class="subtotal">';
					if ( $resa_is_parent ) {
						$table .= '<td class="multi-accom-indent"></td><td colspan="2" class="subtotal-text">';
					} else {
						$table .= '<td colspan="3" class="subtotal-text">';
					}
					$table .= $this->hbdb->get_string( 'table_invoice_accom_subtotal', $this->email_doc_locale );
					$table .= '</td>';
					$table .= '<td class="amount">';
					$table .= $this->price_with_symbol( $resa['accom_price'] );
					$table .= '</td>';
					$table .= '</tr>';
				}

				$table .= $accom_added_fees_block;

				if (
					$accom_fees_total ||
					$accom_included_fees_block ||
					$accom_discount_value ||
					( $nb_paid_extras > 1 ) ||
					$extras_fees_total
				) {
					$table .= '<tr class="subtotal">';
					if ( $resa_is_parent ) {
						$table .= '<td class="multi-accom-indent"></td><td colspan="2" class="subtotal-text">';
					} else {
						$table .= '<td colspan="3" class="subtotal-text">';
					}
					$table .= $this->hbdb->get_string( 'table_invoice_accom_total', $this->email_doc_locale );
					$table .= '</td>';
					$table .= '<td class="amount">';
					$table .= $this->price_with_symbol( $resa['accom_price'] + $accom_fees_total );
					$table .= '</td>';
					$table .= '</tr>';
				}
				$table .= $accom_included_fees_block;
			}

			$table .= $extras_block;

			$subtotal = $resa['accom_price'] + $accom_fees_total + $extras_price + $extras_fees_total;
			if ( $extras_block && $final_added_fees ) {
				$table .= '<tr class="subtotal">';
				if ( $resa_is_parent ) {
					$table .= '<td class="multi-accom-indent"></td><td class="multi-accom-empty">';
				} else {
					$table .= '<td colspan="2" class="empty">';
				}
				$table .= '</td>';
				$table .= '<td class="subtotal-text">';
				$table .= $this->hbdb->get_string( 'table_invoice_subtotal', $this->email_doc_locale );
				$table .= '</td>';
				$table .= '<td class="amount">';
				$table .= $this->price_with_symbol( $subtotal );
				$table .= '</td>';
				$table .= '</tr>';
			}

			if ( $resa['accom_id'] != 'parent_resa' ) {
				if ( $resa['coupon_value'] > 0 ) {
					$table .= '<tr>';
					if ( $final_added_fees ) {
						if ( $resa_is_parent ) {
							$table .= '<td class="multi-accom-indent"></td><td class="multi-accom-empty">';
						} else {
							$table .= '<td colspan="2" class="empty">';
						}
						$table .= '</td>';
						$table .= '<td class="coupon-text">';
					} else {
						if ( $resa_is_parent ) {
							$table .= '<td class="multi-accom-indent"></td><td colspan="2">';
						} else {
							$table .= '<td colspan="3">';
						}
					}
					$table .= $this->hbdb->get_string( 'table_invoice_coupon', $this->email_doc_locale );
					$table .= ' (' . $resa['coupon'] . ')';
					$table .= '</td>';
					$table .= '<td class="amount">&minus; ';
					$table .= $this->price_with_symbol( $resa['coupon_value'] );
					$table .= '</td>';
					$table .= '</tr>';
				}

				$global_discount_value = 0;
				if ( $discount['global'] && $discount['global']['amount'] ) {
					$table .= $this->get_discount_row_for_invoice( $discount['global'], $resa_is_parent, $global_discount_value, $subtotal, $final_added_fees );
				}

				$subtotal -= $resa['coupon_value'];
				$subtotal -= $global_discount_value;
			}

			$subtotal_for_fees = $subtotal;
			if ( $subtotal_for_fees < 0 ) {
				$subtotal_for_fees = 0;
			}

			$final_fees_total = 0;
			$accom_final_fees_total = 0;
			$extras_final_fees_total = 0;
			if ( $accom_final_added_fees ) {
				$table .= $this->get_fee_rows_for_invoice( $resa, $resa_is_parent, $accom_final_added_fees, $admin_fee_names, $extras_and_fees_details_strings, $resa['accom_price'] + $accom_fees_total, true, $accom_final_fees_total );
			}
			if ( $extras_final_added_fees ) {
				$table .= $this->get_fee_rows_for_invoice( $resa, $resa_is_parent, $extras_final_added_fees, $admin_fee_names, $extras_and_fees_details_strings, $extras_price + $extras_fees_total, true, $extras_final_fees_total );
			}
			if ( $final_added_fees ) {
				$table .= $this->get_fee_rows_for_invoice( $resa, $resa_is_parent, $final_added_fees, $admin_fee_names, $extras_and_fees_details_strings, $subtotal_for_fees, true, $final_fees_total );
			}

			$total = $subtotal + $accom_final_fees_total + $extras_final_fees_total + $final_fees_total;
			if ( ( $resa['accom_id'] != 'parent_resa' ) && ( $total < 0 ) ) {
				$total = 0;
			}

			if ( ( $resa['accom_id'] != 'parent_resa' ) || ( $total != 0 ) ) {
				$table .= '<tr class="total">';
				if ( $resa_is_parent ) {
					$table .= '<td class="multi-accom-indent"></td><td class="multi-accom-empty">';
				} else {
					$table .= '<td colspan="2" class="empty">';
				}
				$table .= '</td>';
				$table .= '<td class="total-text';
				if ( $resa_is_parent ) {
					$table .=' multi-accom-total">';
					if ( $resa['accom_id'] != 'parent_resa' ) {
						$table .= str_replace( '%accom_name', $accom_name, $this->hbdb->get_string( 'table_invoice_accom_extras_total', $this->email_doc_locale ) );
					} else {
						$table .= $this->hbdb->get_string( 'table_invoice_global_extras_total', $this->email_doc_locale );
					}
				} else {
					$table .='">';
					$table .= $this->hbdb->get_string( 'table_invoice_total', $this->email_doc_locale );
				}
				$table .= '</td>';
				$table .= '<td class="amount">';
				$table .= $this->price_with_symbol( $total );
				$table .= '</td>';
				$table .= '</tr>';
			}

			$table .= $this->get_fee_rows_for_invoice( $resa, $resa_is_parent, $final_included_fees, $admin_fee_names, $extras_and_fees_details_strings, $total, true, $final_fees_total );

			if ( $resa_is_parent ) {
				$table .= '<tr class="multi-accom-separator">';
				$table .= '<td colspan="4">';
				$table .= '</td>';
				$table .= '</tr>';
			}

			$all_accom_total += $total;
		}

		if ( $resa_is_parent ) {
			$global_final_fees_total = 0;
			if ( $global_final_added_fees ) {
				$table .= $this->get_fee_rows_for_invoice( $resa, false, $global_final_added_fees, $admin_fee_names, $extras_and_fees_details_strings, $all_accom_total, true, $global_final_fees_total );
			}
			$all_accom_total += $global_final_fees_total;

			$table .= '<tr class="total">';
			$table .= '<td colspan="2" class="empty">';
			$table .= '</td>';
			$table .= '<td class="total-text">';
			$table .= $this->hbdb->get_string( 'table_invoice_total', $this->email_doc_locale );
			$table .= '</td>';
			$table .= '<td class="amount">';
			$table .= $this->price_with_symbol( $all_accom_total );
			$table .= '</td>';
			$table .= '</tr>';

			$table .= $this->get_fee_rows_for_invoice( $resa, $resa_is_parent, $final_included_fees, $admin_fee_names, $extras_and_fees_details_strings, $total, true, $global_final_fees_total );
		}

		$table .= '</table>';

		$style = apply_filters( 'hb_invoice_table_style', $style, $resa );
		$table = apply_filters( 'hb_invoice_table_markup', $table, $resa );

		return $style . $table;
	}

	private function get_subpart_title_for_invoice( $title ) {
		$row = '<tr>';
		$row .= '<td colspan="3" class="subpart-title" style="border-right: none; border-bottom: none;">';
		$row .= $title;
		$row .= '</td>';
		$row .= '<td class="subpart-title" style="border-left: none;"></td>';
		$row .= '</tr>';
		return $row;
	}

	private function get_subsubpart_title_for_invoice( $title ) {
		$row = '<tr>';
		$row .= '<td class="multi-accom-indent"></td>';
		$row .= '<td colspan="3" class="subsubpart-title">';
		$row .= $title;
		$row .= '</td>';
		$row .= '</tr>';
		return $row;
	}

	private function get_discount_row_for_invoice( $discount, $sub_accom, &$discount_value, $price, $final_added_fees = false ) {
		$row = '<tr>';
		if ( $final_added_fees ) {
			if ( $sub_accom ) {
				$row .= '<td class="multi-accom-indent"></td><td class="multi-accom-empty">';
			} else {
				$row .= '<td colspan="2" class="empty">';
			}
			$row .= '</td>';
			$row .= '<td class="discount-text">';
		} else {
			if ( $sub_accom ) {
				$row .= '<td class="multi-accom-indent"></td><td colspan="2">';
			} else {
				$row .= '<td colspan="3">';
			}
		}
		if ( $discount['amount_type'] == 'fixed' ) {
			$discount_value = $discount['amount'];
		} else {
			$discount_value = $this->round_price( $discount['amount'] * $price / 100 );
		}
		if ( $discount_value > 0 ) {
			$row .= $this->hbdb->get_string( 'table_invoice_discount', $this->email_doc_locale );
		} else {
			$row .= $this->hbdb->get_string( 'table_invoice_surcharge', $this->email_doc_locale );
		}
		if ( $discount['amount_type'] == 'percent' ) {
			$row .= ' (';
			if ( $discount['amount'] > 0 ) {
				$row .= $discount['amount'];
			} else {
				$row .= $discount['amount'] * -1;
			}
			$row .= '%)';
		}
		$row .= '</td>';
		$row .= '<td class="amount">';
		if ( $discount_value > 0 ) {
			$row .= '&minus; ';
			$row .= $this->price_with_symbol( $discount_value );
		} else {
			$row .= $this->price_with_symbol( $discount_value * -1 );
		}
		$row .= '</td>';
		$row .= '</tr>';
		return $row;
	}

	private function get_extra_name_for_invoice( $extra_id, $admin_extra_names, $resa_extra_info ) {
		$extra_name = $this->hbdb->get_string( 'option_' . $extra_id, $this->email_doc_locale );
		if ( ! $extra_name && isset( $admin_extra_names[ 'option_' . $extra_id ] ) ) {
			$extra_name = $admin_extra_names[ 'option_' . $extra_id ];
		}
		if ( ! $extra_name ) {
			$extra_name = $resa_extra_info['name'];
		}
		if ( isset( $resa_extra_info['chosen'] ) ) {
			$extra_name .= ' (' . $this->get_extra_choice_name_for_invoice( $resa_extra_info['chosen'], $admin_extra_names, $resa_extra_info['choice_name'] ) . ')';
		}
		return $extra_name;
	}

	private function get_extra_choice_name_for_invoice( $extra_choice_id, $admin_extra_names, $resa_extra_choice_name ) {
		$extra_choice_name = $this->hbdb->get_string( 'option_choice_' . $extra_choice_id, $this->email_doc_locale );
		if ( ! $extra_choice_name && isset( $admin_extra_names[ 'option_choice_' . $extra_choice_id ] ) ) {
			$extra_choice_name = $admin_extra_names[ 'option_choice_' . $extra_choice_id ];
		}
		if ( ! $extra_choice_name ) {
			$extra_choice_name = $resa_extra_choice_name;
		}
		return $extra_choice_name;
	}

	private function get_fee_name_for_invoice( $fee_id, $admin_fee_names, $resa_fee_name ) {
		$fee_name = $this->hbdb->get_string( 'fee_' . $fee_id, $this->email_doc_locale );
		if ( ! $fee_name && isset( $admin_fee_names[ $fee_id ] ) ) {
			$fee_name = $admin_fee_names[ $fee_id ];
		}
		if ( ! $fee_name ) {
			$fee_name = $resa_fee_name;
		}
		return $fee_name;
	}

	private function get_fee_rows_for_invoice( $resa, $sub_accom, $fees, $admin_fee_names, $extras_and_fees_details_strings, $price, $half_row = false, &$fees_total = null ) {
		$rows = '';
		$price_before_included_fees = $this->calculate_price_before_included_fees( $resa, $price, $fees );
		foreach ( $fees as $fee ) {
			if ( $fee['include_in_price'] == 2 ) {
				$fee_values = $this->calculate_fees_extras_values( $resa, $price_before_included_fees, $fee, $extras_and_fees_details_strings );
			} else {
				$fee_values = $this->calculate_fees_extras_values( $resa, $price, $fee, $extras_and_fees_details_strings );
				$fees_total += $fee_values['price'];
			}
			if ( $fee_values['price'] > 0 ) {
				$rows .= '<tr>';
				if ( $sub_accom ) {
					$rows .= '<td class="multi-accom-indent"></td>';
					if ( $half_row ) {
						$rows .= '<td class="multi-accom-empty"></td><td ';
					} else {
						$rows .= '<td colspan="2" ';
					}
				} else {
					if ( $half_row ) {
						$rows .= '<td colspan="2" class="empty"></td><td ';
					} else {
						$rows .= '<td colspan="3" ';
					}
				}
				if ( $fee['include_in_price'] == 0 ) {
					$rows .= 'class="fee fee-final">';
				} else if ( $fee['include_in_price'] == 1 ) {
					$rows .= 'class="fee fee-added">';
				} else if ( $fee['include_in_price'] == 2 ) {
					$rows .= 'class="fee fee-included">';
					$rows .= $this->hbdb->get_string( 'table_invoice_included_fee', $this->email_doc_locale );
					$rows .= ' ';
				}
				$rows .= $this->get_fee_name_for_invoice( $fee['id'], $admin_fee_names, $fee['name'] );
				if ( $fee_values['details'] ) {
					$rows .= ' (' . $fee_values['details'] . ')';
				}
				$rows .= '</td>';
				$rows .= '<td class="amount">';
				$rows .= $this->price_with_symbol( $fee_values['price'] );
				$rows .= '</td>';
				$rows .= '</tr>';
			}
		}
		return $rows;
	}

	public function replace_fields_var_with_value( $vars, $values, $text ) {
		foreach ( $vars as $var ) {
			$value = '';
			if ( isset ( $values[ 'hb_' . $var ] ) ) {
				if ( is_array( $values[ 'hb_' . $var ] ) ) {
					$value =  strip_tags( stripslashes( implode( ', ', $values[ 'hb_' . $var ] ) ) );
				} else {
					$value =  strip_tags( stripslashes( $values[ 'hb_' . $var ] ) );
				}
			}
			$text = str_replace( '[' . $var . ']', $value, $text );
		}
		return $text;
	}

	public function get_ical_email_document_available_vars() {
		$vars = array(
			'[resa_id]',
			'[resa_ids]',
			'[resa_alphanum_id]',
			'[resa_invoice_id]',
			'[resa_check_in]',
			'[resa_check_out]',
			'[resa_number_of_nights]',
			'[resa_accommodation]',
			'[resa_accommodation_num]',
			'[resa_adults]',
			'[resa_children]',
			'[resa_persons]',
			'[resa_admin_comment]',
			'[resa_extras]',
			'[resa_coupon_code]',
			'[resa_coupon_amount]',
			'[resa_invoice_table]',
			'[resa_price]',
			'[resa_accom_list_price]',
			'[resa_accom_list_price_per_night]',
			'[resa_deposit]',
			'[resa_price_minus_deposit]',
			'[resa_paid]',
			'[resa_remaining_balance]',
			'[resa_bond]',
			'[resa_price_including_bond]',
			'[resa_deposit_including_bond]',
			'[resa_remaining_balance_including_bond]',
			'[resa_payment_type]',
			'[resa_payment_gateway]',
			'[resa_origin]',
			'[resa_received_on]',
			'[resa_received_on_date]',
			'[resa_x_days_before_check_in]',
			'[resa_x_days_after_check_in]',
			'[resa_x_days_before_check_out]',
			'[resa_x_days_after_check_out]',
			'[today_date]',
		);

		$resa_additional_fields = $this->hbdb->get_additional_booking_info_form_fields();
		foreach ( $resa_additional_fields as $field ) {
			$vars[] = '[resa_' . $field['id'] . ']';
		}

		$vars[] = '[customer_id]';
		$customer_fields = $this->hbdb->get_customer_form_fields();
		foreach ( $customer_fields as $field ) {
			$vars[] = '[customer_' . $field['id'] . ']';
		}

		$vars = implode( ' &nbsp;-&nbsp; ', $vars );
		return $vars;
	}

	public function ical_sync_remote_post( $synchro_url ) {
		$args = array(
			'method' => 'GET',
		);
		if ( strpos( $synchro_url, 'expedia' ) ) {
			$args['user-agent'] = '';
		}
		$response = wp_remote_post( $synchro_url, $args );
		return $response;
	}

	public function calculate_fees_extras_values( $resa, $base, $data, $strings = false ) {
		$price = 0;
		$details = '';
		$adults = 0;
		$children = 0;
		if ( isset( $resa['adults'] ) ) {
			$adults = $resa['adults'];
		}
		if ( isset( $resa['children'] ) ) {
			$children = $resa['children'];
		}
		$adults_children = $adults + $children;
		if ( isset( $resa['check_in'] ) ) {
			$nb_nights = $this->get_number_of_nights( $resa['check_in'], $resa['check_out'] );
			if ( get_option( 'hb_charge_per_day' ) == 'yes' ) {
				$nb_nights++;
			}
		}
		$nb_nights_str = '';
		$adults_str = '';
		$children_str = '';
		$adults_children_str = '';
		$accommodation_str = '';
		if ( $strings ) {
			if ( $nb_nights == 1 ) {
				$nb_nights_str = $strings['price_breakdown_night_one'];
			} else {
				$nb_nights_str = $strings['price_breakdown_nights_several'];
			}
			if ( $adults == 1 ) {
				$adults_str = $strings['fee_details_adult_one'];
			} else {
				$adults_str = $strings['fee_details_adults_several'];
			}
			if ( $children == 1 ) {
				$children_str = $strings['fee_details_child_one'];
			} else {
				$children_str = $strings['fee_details_children_several'];
			}
			if ( ! $children ) {
				$adults_children_str = $adults_str;
			} else {
				$adults_children_str = $strings['fee_details_persons'];
			}
		}
		if ( is_array( $base ) ) {
			switch ( $data['apply_to_type'] ) {
				case 'accom-percentage' : $base = $base['accom_total']; break;
				case 'extras-percentage' : $base = $base['extras']; break;
				case 'global-percentage' : $base = $base['total']; break;
			}
		}

		switch ( $data['apply_to_type'] ) {
			case 'per-person' :
				$price = $data['amount'] * $adults + $data['amount_children'] * $children;
				$details = $adults . ' ' . $adults_str . ' x ' . $this->price_with_symbol( $data['amount'] );
				if ( ( $children > 0 ) && ( $data['amount_children'] > 0 ) ) {
					$details .= ' + ' . $children . ' ' . $children_str . ' x ' . $this->price_with_symbol( $data['amount_children'] );
				}
			break;

			case 'per-accom' :
			case 'global-fixed' :
			case 'per-booking' :
				$price = $data['amount'];
				$details =  '';
			break;

			case 'per-person-per-day' :
				$price = $data['amount'] * $adults * $nb_nights + $data['amount_children'] * $children * $nb_nights;
				$details = $nb_nights . ' ' . $nb_nights_str . ' x ' . $adults . ' ' . $adults_str . ' x ' . $this->price_with_symbol( $data['amount'] );
				if ( ( $children > 0 ) && ( $data['amount_children'] > 0 ) ) {
					$details .= ' + ' . $nb_nights . ' ' . $nb_nights_str . ' x ' . $children . ' ' . $children_str . ' x ' . $this->price_with_symbol( $data['amount_children'] );
				}
			break;

			case 'per-accom-per-day' :
				$price = $data['amount'] * $nb_nights;
				$details = $nb_nights . ' ' . $nb_nights_str . ' x ' . $this->price_with_symbol( $data['amount'] );
			break;

			case 'per-accom-per-booking' :
				$price = $data['amount'] * $resa['nb_accom'];
				$details = $resa['nb_accom'] . ' ' . $accommodation_str . ' x ' . $this->price_with_symbol( $data['amount'] );
			break;

			case 'quantity' :
				$price = $data['quantity'] * $data['amount'];
				$details = $data['quantity'] . ' x ' . $this->price_with_symbol( $data['amount'] );
			break;

			case 'quantity-per-day' :
				$price = $data['quantity'] * $data['amount'] * $nb_nights;
				$details = $data['quantity'] . ' x ' . $nb_nights . ' ' . $nb_nights_str . ' x ' . $this->price_with_symbol( $data['amount'] );
			break;

			case 'accom-percentage' :
				if ( $data['accom_price_per_person_per_night'] ) {
					if ( $data['include_in_price'] != 2 ) {
						$accom_price_per_person = $base / ( $adults + $children );
						$accom_price_pppn = $accom_price_per_person / $nb_nights;
						$price = $accom_price_pppn * $data['amount'] / 100;
						$details = '';
						if ( ( $data['minimum_amount'] > 0 ) && ( $price < $data['minimum_amount'] ) ) {
							$price = $data['minimum_amount'];
							$details .= $this->price_with_symbol( $price );
						} else if ( ( $data['maximum_amount'] > 0 ) && ( $price > $data['maximum_amount'] ) ) {
							$price = $data['maximum_amount'];
							$details .= $this->price_with_symbol( $price );
						} else {
							$details .= $data['amount'] . '% x ' . $this->price_with_symbol( $accom_price_pppn );
						}
						if ( $data['multiply_per'] ) {
							$multiply_per = explode( ',', $data['multiply_per'] );
							foreach ( $multiply_per as $multiplier ) {
								$price *= $$multiplier;
								$multiplier_str = $multiplier . '_str';
								$details .= ' x ' . $$multiplier . ' ' . $$multiplier_str;
							}
						}
					}
				} else {
					if ( $base > 0 ) {
						$price = round( $base * ( $data['amount'] / 100 ), 2 );
						$details = $data['amount'] . '% x ' . $this->price_with_symbol( $base );
					}
				}
			break;

			case 'extras-percentage' :
			case 'global-percentage' :
				if ( $base > 0 ) {
					$price = round( $base * ( $data['amount'] / 100 ), 2 );
					$details = $data['amount'] . '% x ' . $this->price_with_symbol( $base );
				}
			break;
		}
		return array(
			'price' => $price,
			'details' => $details,
			'apply_to_type' => $data['apply_to_type']
		);
	}

	public function calculate_price_before_included_fees( $resa, $price, $fees ) {
		$fixed_fee_types = array( 'per-person', 'per-accom', 'per-person-per-day', 'per-accom-per-day', 'global-fixed' );
		$included_percent_fees_rate = 0;
		$included_fixed_fees_amount = 0;
		$price_before_included_fees = $price;
		foreach ( $fees as $fee ) {
			if ( $fee['include_in_price'] == 2 ) {
				if ( in_array( $fee['apply_to_type'], $fixed_fee_types ) ) {
					$fee_values = $this->calculate_fees_extras_values( $resa, 0, $fee );
					$included_fixed_fees_amount += $fee_values['price'];
				} else {
					$included_percent_fees_rate += $fee['amount'];
				}
			}
		}
		$price_before_included_fees -= $included_fixed_fees_amount;
		$price_before_included_fees = $price_before_included_fees / ( 1 + ( $included_percent_fees_rate / 100 ) );
		return $price_before_included_fees;
	}

	public function calculate_options_price( $adults, $children, $nb_nights, $nb_accom, $options, $include_fee ) {
		$tmp_options = array();
		foreach ( $options as $option ) {
			if ( $option['choice_type'] == 'single' ) {
				$tmp_options[ 'option_' . $option['id'] ] = $option;
			} else{
				foreach( $option['choices'] as $option_choice ) {
					$tmp_options[ 'option_choice_' . $option_choice['id'] ] = array_merge( $option, $option_choice );
				}
			}
		}
		$options = $tmp_options;

		$extras_fees_rate = 1;
		if ( $include_fee ) {
			$extras_fees_percentages = $this->hbdb->get_extras_fees_percentages();
			foreach ( $extras_fees_percentages as $extras_fee_percentage ) {
				$extras_fees_rate += $extras_fee_percentage / 100;
			}
		}

		$price_options = array();
		foreach ( $options as $option_id => $option ) {
			if (
				( $option['apply_to_type'] == 'quantity' ) ||
				( $option['apply_to_type'] == 'per-accom' ) ||
				( $option['apply_to_type'] == 'per-booking' )
			) {
				$price_options[ $option_id ] = $this->round_price( $option['amount'] * $extras_fees_rate );
			} else if ( $option['apply_to_type'] == 'quantity-per-day' ) {
				$price_options[ $option_id ] = $this->round_price( $option['amount'] * $nb_nights * $extras_fees_rate );
			} else if ( $option['apply_to_type'] == 'per-person' ) {
				$price_options[ $option_id ] = $this->round_price( ( $option['amount'] * $adults + $option['amount_children'] * $children ) * $extras_fees_rate );
			} else if ( $option['apply_to_type'] == 'per-accom-per-day' ) {
				$price_options[ $option_id ] = $this->round_price( $option['amount'] * $nb_nights * $extras_fees_rate );
			} else if ( $option['apply_to_type'] == 'per-person-per-day' ) {
				$price_options[ $option_id ] = $this->round_price( ( $option['amount'] * $adults + $option['amount_children'] * $children ) * $nb_nights * $extras_fees_rate );
			} else if ( $option['apply_to_type'] == 'per-accom-per-booking' ) {
				$price_options[ $option_id ] = $this->round_price( $option['amount'] * $nb_accom * $extras_fees_rate );
			}
		}

		return $price_options;
	}

	public function discounts_observe_rules( $discounts, $str_check_in, $str_check_out ) {
		$returned_discount_ids = array();
		if ( $discounts ) {
			$check_in_day = $this->get_day_num( $str_check_in );
			$check_out_day = $this->get_day_num( $str_check_out );
			$nb_nights = $this->get_number_of_nights( $str_check_in, $str_check_out );
			foreach ( $discounts as $discount ) {
				$allowed_check_in_days = explode( ',', $discount['check_in_days'] );
				$allowed_check_out_days = explode( ',', $discount['check_out_days'] );
				if (
					( in_array( $check_in_day, $allowed_check_in_days ) ) &&
					( in_array( $check_out_day, $allowed_check_out_days ) ) &&
					( $nb_nights >= $discount['minimum_stay'] ) &&
					( $nb_nights <= $discount['maximum_stay'] )
				) {
					$returned_discount_ids[] = $discount['id'];
				}
			}
		}
		return $returned_discount_ids;
	}

	public function get_global_discount( $accom_id, $check_in, $check_out, $price ) {
		$total_discount_amount = 0;
		$discount_breakdown = array();
		$discounts = $this->hbdb->get_discounts_rules( $accom_id );
		$discount_ids = $this->discounts_observe_rules( $discounts, $check_in, $check_out );
		$nb_discount = 0;
		$saved_discount_percent_value = 0;
		foreach ( $discount_ids as $discount_id ) {
			$discount_info = $this->hbdb->get_discount_info( $discount_id, $accom_id, $this->hbdb->get_season( $check_in ) );
			if ( $discount_info && ( $discount_info['apply_to_type'] == 'global' ) ) {
				if ( $discount_info['amount_type'] == 'fixed' ) {
					$discount_amount = $discount_info['amount'];
				} else {
					$discount_percent_value = 0;
					$nb_nights_for_discount = 0;
					$current_night = date( 'Y-m-d', strtotime( $check_in ) );
					while ( strtotime( $check_out ) > strtotime( $current_night ) ) {
						$discount_info_percent = $this->hbdb->get_discount_info( $discount_id, $accom_id, $this->hbdb->get_season( $current_night ) );
						if ( $discount_info_percent && ( $discount_info_percent['amount_type'] == 'percent' ) ) {
							$discount_percent_value += $discount_info_percent['amount'];
						}
						$current_night = date( 'Y-m-d', strtotime( $current_night . ' + 1 day' ) );
						$nb_nights_for_discount++;
					}
					$discount_percent_value = round( $discount_percent_value / $nb_nights_for_discount, 2 );
					$saved_discount_percent_value = $discount_percent_value;
					$discount_amount = $this->round_price( $discount_percent_value * $price / 100 );
				}
				$nb_discount++;
				$total_discount_amount += $discount_amount;
			}
		}
		if ( $total_discount_amount ) {
			if ( ( $nb_discount == 1 ) && $saved_discount_percent_value ) {
				$discount_breakdown = array(
					'amount_type' => 'percent',
					'amount' => '' . $discount_percent_value,
				);
			} else {
				$discount_breakdown = array(
					'amount_type' => 'fixed',
					'amount' => '' . $total_discount_amount,
				);
			}
		}
		return array(
			'discount_amount' => $total_discount_amount,
			'discount_breakdown' => $discount_breakdown,
		);
	}

	public function resa_non_editable_info_markup( $resa ) {
		$payment_gateway = '';
		if ( isset( $resa['payment_gateway'] ) && $resa['payment_gateway'] ) {
			$payment_gateway = '<b><u>' . esc_html__( 'Payment method', 'hbook-admin' ) . '</u></b><br/>';
			$payment_gateway .= $resa['payment_gateway'] . '<br/>';
		}

		$coupon = '';
		if ( isset( $resa['coupon'] ) && $resa['coupon'] ) {
			if ( $resa['coupon_value'] > 0 ) {
				$coupon_txt = esc_html__( 'Code', 'hbook-admin' ) . ' ' . $resa['coupon'] . '<br/>';
				$coupon_txt .= esc_html__( 'Amount', 'hbook-admin' ) . ' ' . $this->price_with_symbol( $resa['coupon_value'] );
			} else {
				$coupon_txt = $resa['coupon'];
			}
			$coupon = '<b><u>' . esc_html__( 'Coupon', 'hbook-admin' ) . '</u></b><br/>';
			$coupon .= $coupon_txt . '<br/>';
		}

		$origin = '';
		if ( isset( $resa['origin'] ) && $resa['origin'] && $resa['origin'] != 'website' ) {
			$origin = '<b><u>' . esc_html__( 'Reservation origin', 'hbook-admin' ) . '</u></b><br/>';
			$origin .= $resa['origin'] . '<br/>';
		}

		$resa_info = $payment_gateway . $coupon . $origin;

		return $resa_info;
	}

	public function resa_options_markup_admin( $options ) {
		return $this->resa_options_generic( $options, true, true );
	}

	private function resa_options_markup( $options ) {
		return $this->resa_options_generic( $options, true, false );
	}

	private function resa_options_text( $options ) {
		return $this->resa_options_generic( $options, false, false );
	}

	private function resa_options_generic( $chosen_options, $is_markup, $is_admin ) {
		if ( ! is_array( $chosen_options ) ) {
			$chosen_options = json_decode( $chosen_options, true );
		}
		if ( ! $chosen_options ) {
			return '';
		}
		$options_text = '';
		$tmp_options = array();
		$options = $this->hbdb->get_all_options_with_choices();
		foreach ( $options as $option ) {
			$tmp_options[ $option['id'] ] = $option;
		}
		$options = $tmp_options;
		$options_choices = $this->hbdb->get_all( 'options_choices' );
		$choice_name = array();
		foreach ( $options_choices as $choice ) {
			$choice_name[ $choice['id'] ] = $choice['name'];
		}
		$bold_begin = '';
		$bold_end = '';
		$line_break = "\n";
		if ( $is_markup ) {
			if ( $is_admin ) {
				$bold_begin = '<b>';
				$bold_end = '</b>';
			}
			$line_break = '<br/>';
		}
		$bullet = '';
		if ( ! $is_admin ) {
			$bullet = '- ';
		}
		foreach ( $chosen_options as $option_id => $option_value ) {
			if ( isset( $options[ $option_id ] ) ) {
				$option_name = $this->hbdb->get_string( 'option_' . $option_id, $this->email_doc_locale );
				if ( $is_admin || ! $option_name ) {
					$option_name = $options[ $option_id ]['name'];
				}
				$new_option_text = '';
				$option_choice_name = '';
				if ( $options[ $option_id ]['apply_to_type'] == 'quantity' || $options[ $option_id ]['apply_to_type'] == 'quantity-per-day' ) {
					if ( is_array( $option_value ) ) {
						$quantity = $option_value['quantity'];
					} else {
						$quantity = $option_value;
					}
					if ( $quantity != 0 ) {
						$new_option_text = $bullet . $bold_begin . $option_name . ': ' . $bold_end . $quantity . $line_break;
					}
				} else if ( $options[ $option_id ]['choice_type'] == 'single' ) {
					$new_option_text = $bullet . $bold_begin . $option_name . $bold_end . $line_break;
				} else if ( $options[ $option_id ]['choice_type'] == 'multiple' ) {
					if ( is_array( $option_value ) ) {
						$chosen_option = $option_value['chosen'];
					} else {
						$chosen_option = $option_value;
					}
					$option_choice_name = $this->hbdb->get_string( 'option_choice_' . $chosen_option, $this->email_doc_locale );
					if ( $is_admin || ! $option_choice_name ) {
						if ( isset( $choice_name[ $chosen_option ] ) ) {
							$option_choice_name = $choice_name[ $chosen_option ];
						}
					}
					$new_option_text = $bullet . $bold_begin . $option_name . ': ' . $bold_end . $option_choice_name . $line_break;
				}
				if ( ! $is_admin ) {
					$new_option_text = apply_filters( 'hb_resa_extra_formatting', $new_option_text, $option_name, $option_value, $option_choice_name );
				}
				$options_text .= $new_option_text;
			}
		}
		return $options_text;
	}

	public function resa_max_refundable( $payment_info ) {
		$payment_info = json_decode( $payment_info, true );
		if ( ! $payment_info || ! isset( $payment_info['stripe_charges'] ) ) {
			return 0;
		}
		$stripe_charges = $payment_info['stripe_charges'];
		$max_refundable = 0;
		foreach ( $stripe_charges as $charge ) {
			$max_refundable += $charge['amount'];
		}
		return $max_refundable;
	}

	public function deposit( $check_in, $check_out, $total_price ) {
		$deposit_check_in_min_days = intval( get_option( 'hb_deposit_check_in_min_days' ) );
		if ( $deposit_check_in_min_days ) {
			$tmp_date = new DateTime();
			$tmp_date->modify( "+{$deposit_check_in_min_days} day" );
			$max_date_for_deposit = $tmp_date->format( 'Y-m-d' );
			if ( $max_date_for_deposit > $check_in ) {
				return $total_price;
			}
		}
		$deposit = 0;
		if ( get_option( 'hb_deposit_type' ) == 'nb_night' ) {
			$nb_nights = $this->get_number_of_nights( $check_in, $check_out );
			$deposit = ( $total_price / $nb_nights ) * get_option( 'hb_deposit_amount' );
		} else if ( get_option( 'hb_deposit_type' ) == 'fixed' ) {
			$deposit = get_option( 'hb_deposit_amount' );
		} else if ( get_option( 'hb_deposit_type' ) == 'percentage' ) {
			$deposit = $total_price * get_option( 'hb_deposit_amount' ) / 100;
		}
		if ( $deposit > $total_price ) {
			$deposit = $total_price;
		}
		return $deposit;
	}

	public function get_alphanum() {
		do {
			$alphanum = '';
			$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			for ( $i = 0; $i < 8; $i++ ) {
				$alphanum .= $characters[ rand(0, 35) ];
			}
		} while ( $this->hbdb->get_resa_by_alphanum( $alphanum ) );
		return $alphanum;
	}

	public function reset_resa_invoice_counter() {
		$last_reset = get_option( 'hb_invoice_counter_last_reset' );
		$current_time = current_time( 'mysql', 1 );
		if ( ! $last_reset ) {
			update_option( 'hb_invoice_counter_next_value', 1 );
			update_option( 'hb_invoice_counter_last_reset', $current_time );
		} else if ( get_option( 'hb_invoice_counter_reset_frequency' ) == 'yearly' ) {
			$reset_year = substr( $last_reset, 0, 4 );
			$current_year = substr( $current_time, 0, 4 );
			if ( $reset_year < $current_year ) {
				update_option( 'hb_invoice_counter_next_value', 1 );
				update_option( 'hb_invoice_counter_last_reset', $current_time );
			}
		} else if ( get_option( 'hb_invoice_counter_reset_frequency' ) == 'monthly' ) {
			$reset_year_month = substr( $last_reset, 0, 7 );
			$current_year_month = substr( $current_time, 0, 7 );
			if ( $reset_year_month < $current_year_month ) {
				update_option( 'hb_invoice_counter_next_value', 1 );
				update_option( 'hb_invoice_counter_last_reset', $current_time );
			}
		}
	}

	public function get_min_date() {
		$min_date = get_option( 'hb_min_date_fixed' );
		if ( ! $min_date ) {
			$nb_days = intval( get_option( 'hb_min_date_days' ) );
			if ( $nb_days ) {
				$tmp_date = new DateTime();
				$tmp_date->modify( "+{$nb_days} day" );
				$min_date = $tmp_date->format( 'Y-m-d' );
			} else {
				$min_date = '0';
			}
		}
		return $min_date;
	}

	public function get_max_date() {
		return $this->hbdb->get_max_date();
	}

	public function get_default_lang_post_id( $accom_id ) {
		if ( function_exists( 'pll_get_post' ) ) {
			$accom_id = pll_get_post( $accom_id, pll_default_language() );
		} else if ( function_exists( 'icl_object_id' ) ) {
			global $sitepress;
			$default_lang = $sitepress->get_locale( $sitepress->get_default_language() );
			$default_lang = substr( $default_lang, 0, 2 );
			$accom_id = icl_object_id( $accom_id, 'hb_accommodation', true, $default_lang );
		}
		return $accom_id;
	}

	public function get_admin_translated_post_id( $accom_id ) {
		$trans_id = $this->get_translated_post_id_by_locale( $accom_id, get_user_locale() );
		if ( $trans_id ) {
			return $trans_id;
		} else {
			return $accom_id;
		}
	}

	public function get_translated_post_id( $accom_id ) {
		if ( function_exists( 'icl_object_id' ) && ! function_exists( 'pll_get_post' ) ) {
			$accom_id = icl_object_id( $accom_id, 'hb_accommodation', true );
		} else if ( function_exists( 'pll_get_post' ) ) {
			$trans_id = pll_get_post( $accom_id );
			if ( $trans_id ) {
				$accom_id = $trans_id;
			}
		}
		return $accom_id;
	}

	private function get_translated_post_id_by_locale( $accom_id, $current_locale ) {
		if ( function_exists( 'pll_get_post' ) ) {
			$locales = pll_languages_list( array( 'fields' => 'locale' ) );
			$slugs = pll_languages_list( array( 'fields' => 'slug' ) );
			$locale_slugs = array();
			foreach ( $locales as $i => $locale ) {
				$locale_slugs[ $locale ] = $slugs[ $i ];
			}
			$accom_id = pll_get_post( $accom_id, $locale_slugs[ $current_locale ] );
		} else if ( function_exists( 'icl_object_id' ) ) {
			$wpml_langs = icl_get_languages();
			$locale_slugs = array();
			foreach ( $wpml_langs as $lang_id => $wpml_lang ) {
				$locale_slugs[ $wpml_lang['default_locale'] ] = $wpml_lang[ 'code' ];
			}
			$accom_id = apply_filters( 'wpml_object_id', $accom_id, 'post', true, $locale_slugs[ $current_locale ] );
		} else if ( function_exists( 'qtranxf_getLanguage' ) ) {
			global $q_config;
			$locale_slugs = array();
			foreach ( $q_config['locale'] as $lang_code => $locale ) {
				if ( $locale == $current_locale ) {
					global $wpdb;
					$raw_title = $wpdb->get_var( $wpdb->prepare( "SELECT post_title FROM $wpdb->posts WHERE ID = %d", $accom_id ) );
					$re = "/\\[:" . $lang_code . "](.*)\\[:/U";
					if ( preg_match( $re, $raw_title, $matches ) ) {
						return $matches[1];
					}
				}
			}
		}
		return $accom_id;
	}

	public function get_accom_title( $accom_id ) {
		return get_the_title( $this->get_translated_post_id( $accom_id ) );
	}

	public function get_admin_accom_title( $accom_id ) {
		return get_the_title( $this->get_admin_translated_post_id( $accom_id ) );
	}

	public function get_accom_link( $accom_id ) {
		$accom_default_page = get_post_meta( $accom_id, 'accom_default_page', true );
		if ( $accom_default_page == 'no' ) {
			$accom_id = get_post_meta( $accom_id, 'accom_linked_page', true );
		}
		return get_permalink( $this->get_translated_post_id( $accom_id  ) );
	}

	public function get_accom_search_desc( $accom_id ) {
		return do_shortcode( get_post_meta( $this->get_translated_post_id( $accom_id  ), 'accom_search_result_desc', true ) );
	}

	public function get_accom_list_desc( $accom_id ) {
		return do_shortcode( get_post_meta( $this->get_translated_post_id( $accom_id  ), 'accom_list_desc', true ) );
	}

	public function add_image_sizes() {
		$retina_scale_factor = apply_filters( 'hb_retina_scale_factor', 1 );
		$sizes = array(
			array(
				'width' => 150 * $retina_scale_factor,
				'height' => 150 * $retina_scale_factor,
			),
			array(
				'width' => get_option( 'hb_search_accom_thumb_width' ) * $retina_scale_factor,
				'height' => get_option( 'hb_search_accom_thumb_height' ) * $retina_scale_factor,
			),
		);
		$sizes = apply_filters( 'hb_image_sizes', $sizes );
		foreach ( $sizes as $size ) {
			add_image_size( 'hbook-' . $size['width'] . 'x' . $size['height'], $size['width'], $size['height'], true );
		}
	}

	public function get_thumb_mark_up( $accom_id, $width, $height, $class = '' ) {
		$thumb_id = get_post_thumbnail_id( $accom_id );
		$thumb_alt = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
		$retina_scale_factor = apply_filters( 'hb_retina_scale_factor', 1 );
		if ( $thumb_id ) {
			if ( get_option( 'hb_image_resizing' ) == 'static' ) {
				$thumb_array = wp_get_attachment_image_src( $thumb_id, 'hbook-' . $width . 'x' . $height );
				$thumb_url = $thumb_array[0];
				if ( $thumb_url ) {
					return '<img src="' . $thumb_url . '" class="' . $class . '" width="' . $width . '" height="' . $height . '" alt="' . $thumb_alt . '" />';
				}
			} else {
				$thumb_array = wp_get_attachment_image_src( $thumb_id, 'full' );
				$thumb_url = $thumb_array[0];
				if ( $thumb_url ) {
					$resized_thumb = hbook_resize_image( $thumb_url, $width * $retina_scale_factor, $height * $retina_scale_factor );
					if ( $resized_thumb['success'] ) {
						return '<img src="' . $resized_thumb['url'] . '" class="' . $class . '" width="' . $width . '" height="' . $height . '" alt="' . $thumb_alt . '" />';
					} else {
						return '<p>' . $resized_thumb['error_msg'] . '</p>';
					}
				}
			}
		}
		return '';
	}

	public function get_step_buttons( $type, $n, $is_admin ) {
		$output = '<p class="hb-step-button hb-button-wrapper hb-' . $type . '-step hb-' . $type . '-step-' . $n . '">';
		if ( $is_admin == 'yes' ) {
			if ( $type == 'next' ) {
				$button_text = esc_html__( 'Next %arrow', 'hbook-admin' );
			} else {
				$button_text = esc_html__( '%arrow Previous', 'hbook-admin' );
			}
		} else {
			$button_text = $this->hbdb->get_string( $type . '_step_button' );
		}
		if ( $type == 'previous' ) {
			$button_text = str_replace( '%arrow', '&larr;', $button_text );
		} else {
			$button_text = str_replace( '%arrow', '&rarr;', $button_text );
		}
		$output .= '<input type="submit" value="' . esc_attr( $button_text ) . '"';
		if ( $is_admin == 'yes' ) {
			$output .= ' class="button"';
		}
		$output .= ' />';
		$output .= '</p>';
		return $output;
	}

	public function get_langs() {
		$langs = array();
		if ( function_exists( 'icl_get_languages' ) && ! function_exists( 'pll_languages_list' ) ) {
			$wpml_langs = icl_get_languages( 'skip_missing=0&orderby=code' );
			foreach ( $wpml_langs as $lang_id => $wpml_lang ) {
				$langs[ $wpml_lang['default_locale'] ] = $wpml_lang[ 'native_name' ];
			}
		} else if ( function_exists( 'pll_languages_list' ) ) {
			$locales = pll_languages_list( array( 'fields' => 'locale' ) );
			$names = pll_languages_list( array( 'fields' => 'name' ) );
			foreach ( $locales as $i => $locale ) {
				$langs[ $locale ] = $names[ $i ];
			}
		} else if ( function_exists( 'qtranxf_getLanguage' ) ) {
			global $q_config;
			foreach ( $q_config['enabled_languages'] as $q_lang ) {
				$langs[ $q_config['locale'][ $q_lang ] ] = $q_config['language_name'][ $q_lang ];
			}
		} else {
			if ( get_locale() != 'en_US' ) {
				require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );
				$translations = wp_get_available_translations();
				$langs[ get_locale() ] = $translations[ get_locale() ]['native_name'];
			}
		}
		if ( ! array_key_exists( 'en_US', $langs ) ) {
			$langs = array_merge( array( 'en_US' => 'English' ), $langs );
		}
		$langs = apply_filters( 'hb_language_list', $langs );
		return $langs;
	}

	public function is_site_multi_lang() {
		$langs = array();
		$langs = apply_filters( 'hb_language_list', $langs );
		if ( $langs ||
			function_exists( 'pll_languages_list' ) ||
			function_exists( 'icl_get_languages' ) ||
			function_exists( 'qtranxf_getLanguage' )
		) {
			return true;
		} else {
			return false;
		}
	}

	public function get_payment_gateways() {
		$gateways = array();
		return apply_filters( 'hbook_payment_gateways', $gateways );
	}

	public function get_active_payment_gateways() {
		$gateways = $this->get_payment_gateways();
		$active_gateways = array();
		foreach ( $gateways as $gateway ) {
			if ( get_option( 'hb_' . $gateway->id . '_active' ) == 'yes' ) {
				$active_gateways[] = $gateway;
			}
		}
		return $active_gateways;
	}

	public function get_payment_gateway( $gateway_id ) {
		$gateways = $this->get_payment_gateways();
		foreach ( $gateways as $gateway ) {
			if ( $gateway->id == $gateway_id ) {
				return $gateway;
			}
		}
		return false;
	}

	public function admin_custom_css() {
		if ( get_option( 'hb_custom_css_backend' ) ) {
			echo( '<style type="text/css">' . wp_strip_all_tags( get_option( 'hb_custom_css_backend' ) ) . '</style>' );
		}
	}

	public function frontend_css_files() {
		if ( $this->load_css() ) {
			$this->hb_enqueue_style( 'hb-front-end-style', '/front-end/css/hbook.css' );
			if ( is_rtl() ) {
				$this->hb_enqueue_style( 'hb-front-end-style-rtl', '/front-end/css/hbook-rtl.css' );
			}
		}
	}

	public function frontend_basic_css() {
		if ( $this->load_css() ) {
			if ( get_option( 'hb_tables_style' ) == 'plugin' ) {
				?>

				<style type="text/css">
				.hb-rates-table {
					border-collapse: collapse;
					table-layout: fixed;
					width: 100%;
					word-wrap: break-word;
				}
				.hb-rates-table th,
				.hb-rates-table td {
					border: 1px solid #ddd;
					padding: 10px;
					text-align: center;
				}
				</style>

				<?php
			}
			if ( get_option( 'hb_price_breakdown_default_state' ) == 'opened' ) {
				?>

				<style type="text/css">
				.hb-accom .hb-price-breakdown {
					display: block;
				}
				.hb-accom .hb-price-bd-show-text {
					display: none;
				}
				.hb-accom .hb-price-bd-hide-text {
					display: inline;
				}
				</style>

				<?php
			}
			$search_form_max_width = intval( get_option( 'hb_search_form_max_width' ) );
			$accom_selection_form_max_width = intval( get_option( 'hb_accom_selection_form_max_width' ) );
			$details_form_max_width = intval( get_option( 'hb_details_form_max_width' ) );
			$forms_position = get_option( 'hb_forms_position' );
			if ( $search_form_max_width ) {
			?>
				<style type="text/css">.hb-booking-search-form { max-width: <?php echo( esc_html( $search_form_max_width ) ); ?>px; }</style>
			<?php
			}
			if ( $accom_selection_form_max_width ) {
			?>
				<style type="text/css">.hb-accom-list { max-width: <?php echo( esc_html( $accom_selection_form_max_width ) ); ?>px; }</style>
			<?php
			}
			if ( $details_form_max_width ) {
			?>
				<style type="text/css">.hb-booking-details-form { max-width: <?php echo( esc_html( $details_form_max_width ) ); ?>px; }</style>
			<?php
			}
			if ( $forms_position == 'center' ) {
			?>
				<style type="text/css">.hb-booking-search-form, .hb-accom-list, .hb-booking-details-form { margin: 0 auto; }</style>
			<?php
			}
		}
	}

	public function frontend_calendar_css() {
		if ( $this->load_css() ) {
			$calendar_color_css_rules = $this->calendar_color_css_rules();
			$calendar_color_values = json_decode( get_option( 'hb_calendar_colors' ), true );
			$css_rules = '';
			foreach ( $calendar_color_css_rules as $rule_id => $rule ) {
				if ( isset( $calendar_color_values[ $rule_id ] ) ) {
					$color_value = $calendar_color_values[ $rule_id ];
				} else {
					$color_value = $rule['default'];
				}
				$css_rules .= $rule['selector'] . ' { ' . $rule['property'] . ': ' . $color_value . '; }';
			}
			if ( get_option( 'hb_calendar_shadows' ) != 'no' ) {
				$css_rules .= '.hb-datepick-popup-wrapper { box-shadow: 0 0 30px rgba(0,0,0,0.33), 0 0 3px rgba(0,0,0,0.2); }';
				$css_rules .= '.hb-availability-calendar .hb-datepick-wrapper { box-shadow: 0 0 4px rgba(0,0,0,0.5); }';
			}
			if ( get_option( 'hb_people_popup_shadows' ) != 'no' ) {
				$css_rules .= '.hb-people-popup-wrapper { box-shadow: 0 0 30px rgba(0,0,0,0.33), 0 0 3px rgba(0,0,0,0.2); }';
			}
			$css_rules .= '.hb-people-popup-wrapper { background: ' . get_option( 'hb_people_popup_bg', '#fff' ) . '; }';
			echo( '<style type="text/css">' . wp_strip_all_tags( $css_rules ) . '</style>' );
		}
	}

	public function frontend_buttons_css() {
		if ( get_option( 'hb_buttons_style' ) == 'custom' && $this->load_css() ) {
			$buttons_css_rules = $this->buttons_css_rules();
			$buttons_css_options = json_decode( get_option( 'hb_buttons_css_options' ), true );
			$css_rules = '';
			foreach ( $buttons_css_rules as $rule_id => $rule ) {
				if ( $rule_id != 'bg_hover' ) {
					if ( isset( $buttons_css_options[ $rule_id ] ) ) {
						$rule_value = $buttons_css_options[ $rule_id ];
					} else {
						$rule_value = $rule['default'];
					}
					foreach ( $rule['property'] as $rule_property ) {
						$css_rules .= $rule_property . ' :' . $rule_value;
						if ( $rule['type'] == 'number' ) {
							$css_rules .= 'px';
						}
						$css_rules .= ' !important; ';
					}
				}
			}
			$css_rules = '.hb-accom-listing-shortcode-wrapper input[type="submit"], .hbook-wrapper input[type="submit"] { ' . $css_rules . '} ';
			if ( isset( $buttons_css_options['bg_hover'] ) ) {
				$rule_value = $buttons_css_options['bg_hover'];
			} else {
				$rule_value = $buttons_css_rules['bg_hover']['default'];
			}
			$css_rules .= '.hb-accom-listing-shortcode-wrapper input[type="submit"]:hover, .hbook-wrapper input[type="submit"]:hover { background: ' . $rule_value . ' !important; }';
			echo( '<style type="text/css">' );
			echo( '.hb-accom-listing-shortcode-wrapper input[type="submit"], .hbook-wrapper input[type="submit"] { border: none !important; cursor: pointer; transition: background 0.4s; } ' );
			echo( wp_strip_all_tags( $css_rules ) );
			echo( '</style>' );
		}
	}

	public function frontend_inputs_selects_css() {
		if ( get_option( 'hb_inputs_selects_style' ) == 'custom' && $this->load_css() ) {
			$inputs_selects_css_rules = $this->inputs_selects_css_rules();
			$inputs_selects_css_options = json_decode( get_option( 'hb_inputs_selects_css_options' ), true );
			$css_rules = '';
			foreach ( $inputs_selects_css_rules as $rule_id => $rule ) {
				if ( $rule_id != 'border_color_active' ) {
					if ( isset( $inputs_selects_css_options[ $rule_id ] ) ) {
						$rule_value = $inputs_selects_css_options[ $rule_id ];
					} else {
						$rule_value = $rule['default'];
					}
					foreach ( $rule['property'] as $rule_property ) {
						$css_rules .= $rule_property . ' :' . $rule_value;
						if ( $rule['type'] == 'number' ) {
							$css_rules .= 'px';
						}
						$css_rules .= ' !important; ';
					}
				}
			}
			$css_selector = array( '.hbook-wrapper input[type="text"]', '.hbook-wrapper input[type="number"]', '.hbook-wrapper select', '.hbook-wrapper textarea' );
			$css_selector_txt = implode( ', ', $css_selector );
			$css_rules = $css_selector_txt . '{ ' . $css_rules . '} ';
			if ( isset( $inputs_selects_css_options['border_color_active'] ) ) {
				$rule_value = $inputs_selects_css_options['border_color_active'];
			} else {
				$rule_value = $inputs_selects_css_rules['border_color_active']['default'];
			}
			$css_selector = preg_replace( '/$/', ':focus', $css_selector );
			$css_selector_txt = implode( ', ', $css_selector );
			$css_rules .= $css_selector_txt . ' { border-color: ' . $rule_value . ' !important; }';
			echo( '<style type="text/css">' );
			echo( '.hbook-wrapper input[type="text"], .hbook-wrapper input[type="number"], .hbook-wrapper select, .hbook-wrapper textarea { background: rgba(0,0,0,0); border-style: solid; outline: none; transition: border 0.4s; } ' );
			echo( wp_strip_all_tags( $css_rules ) );
			echo( '</style>' );
		}
	}

	public function frontend_custom_css() {
		if ( get_option( 'hb_custom_css_frontend' ) && $this->load_css() ) {
			echo( '<style type="text/css">' . wp_strip_all_tags( get_option( 'hb_custom_css_frontend' ) ) . '</style>' );
		}
	}

	public function load_css() {
		return apply_filters( 'hbook_load_css', true );
	}

	public function buttons_css_rules() {
		return array(
			'bg' => array(
				'name' => esc_html__( 'Background color', 'hbook-admin' ),
				'type' => 'color',
				'property' => array( 'background' ),
				'default' => '#2da1ca'
			),
			'bg_hover' => array(
				'name' => esc_html__( 'Background color on hover', 'hbook-admin' ),
				'type' => 'color',
				'property' => array( 'background' ),
				'default' => '#277895',
				'action' => 'hover'
			),
			'color' => array(
				'name' => esc_html__( 'Text color', 'hbook-admin' ),
				'type' => 'choice',
				'property' => array( 'color' ),
				'choices' => array(
					'#fff' => esc_html__( 'White', 'hbook-admin' ),
					'#333' => esc_html__( 'Black', 'hbook-admin' ),
				),
				'default' => '#fff'
			),
			'radius' => array(
				'name' => esc_html__( 'Border radius', 'hbook-admin' ),
				'type' => 'number',
				'property' => array( 'border-radius' ),
				'default' => '4'
			),
			'side_padding' => array(
				'name' => esc_html__( 'Side padding', 'hbook-admin' ),
				'type' => 'number',
				'property' => array( 'padding-left', 'padding-right' ),
				'default' => '20'
			),
			'height_padding' => array(
				'name' => esc_html__( 'Height padding', 'hbook-admin' ),
				'property' => array( 'padding-top', 'padding-bottom' ),
				'type' => 'number',
				'default' => '17'
			),
		);
	}

	public function inputs_selects_css_rules() {
		return array(
			'border_color' => array(
				'name' => esc_html__( 'Borders color', 'hbook-admin' ),
				'type' => 'color',
				'property' => array( 'border-color' ),
				'default' => '#999999'
			),
			'border_color_active' => array(
				'name' => esc_html__( 'Borders color when active', 'hbook-admin' ),
				'type' => 'color',
				'property' => array( 'border-color' ),
				'default' => '#277895'
			),
			'borders_width' => array(
				'name' => esc_html__( 'Borders width', 'hbook-admin' ),
				'type' => 'number',
				'property' => array( 'border-width' ),
				'default' => '1'
			),
			'borders_radius' => array(
				'name' => esc_html__( 'Borders radius', 'hbook-admin' ),
				'type' => 'number',
				'property' => array( 'border-radius' ),
				'default' => '4'
			),
			'height' => array(
				'name' => esc_html__( 'Height', 'hbook-admin' ),
				'type' => 'number',
				'property' => array( 'height' ),
				'default' => '50'
			),
			'side_padding' => array(
				'name' => esc_html__( 'Side padding', 'hbook-admin' ),
				'type' => 'number',
				'property' => array( 'padding-left', 'padding-right' ),
				'default' => '10'
			),
			'height_padding' => array(
				'name' => esc_html__( 'Height padding', 'hbook-admin' ),
				'property' => array( 'padding-top', 'padding-bottom' ),
				'type' => 'number',
				'default' => '10'
			),
		);
	}

	public function calendar_color_css_rules() {
		return array(
			'cal-bg' => array(
				'name' => esc_html__( 'Calendar background', 'hbook-admin' ),
				'selector' => '.hb-datepick-popup-wrapper, .hb-datepick-wrapper',
				'property' => 'background',
				'default' => '#ffffff'
			),
			'available-day-bg' => array(
				'name' => esc_html__( 'Available day background', 'hbook-admin' ),
				'selector' => '.hb-day-available, .hb-day-taken-start, .hb-day-taken-end, .hb-avail-caption-available',
				'property' => 'background',
				'default' => '#ffffff'
			),
			'not-selectable-day-bg' => array(
				'name' => esc_html__( 'Not selectable day background', 'hbook-admin' ),
				'selector' => '.hb-dp-day-past, .hb-dp-day-closed, .hb-dp-day-not-selectable, ' .
								'.hb-dp-day-past.hb-day-taken-start:before, .hb-dp-day-past.hb-day-taken-end:before, .hb-dp-day-past.hb-day-fully-taken,' .
								'.hb-dp-day-closed.hb-day-taken-start:before, .hb-dp-day-closed.hb-day-taken-end:before, .hb-dp-day-closed.hb-day-fully-taken',
				'property' => 'background',
				'default' => '#dddddd'
			),
			'not-selectable-day-text' => array(
				'name' => esc_html__( 'Not selectable day number', 'hbook-admin' ),
				'selector' => '.hb-dp-day-past, .hb-dp-day-closed, .hb-dp-day-not-selectable, .hb-dp-day-no-check-in',
				'property' => 'color',
				'default' => '#888888'
			),
			'selected-day-bg' => array(
				'name' => esc_html__( 'Selected day background', 'hbook-admin' ),
				'selector' => '.hb-dp-day-check-in, .hb-dp-day-check-out',
				'property' => 'background',
				'default' => '#ccf7cc'
			),
			'occupied-day-bg' => array(
				'name' => esc_html__( 'Occupied day background', 'hbook-admin' ),
				'selector' => '.hb-day-taken-start:before, .hb-day-taken-end:before, .hb-day-fully-taken, .hb-avail-caption-occupied',
				'property' => 'background',
				'default' => '#f7d7dc'
			),
			'cmd-buttons-bg' => array(
				'name' => esc_html__( 'Buttons background', 'hbook-admin' ),
				'selector' => '.hb-dp-cmd-wrapper a, .hb-dp-cmd-close, .hb-people-popup-wrapper-close',
				'property' => 'background',
				'default' => '#333333'
			),
			'cmd-buttons-bg-hover' => array(
				'name' => esc_html__( 'Buttons background on hover', 'hbook-admin' ),
				'selector' => '.hb-dp-cmd-wrapper a:hover, .hb-dp-cmd-close:hover, .hb-people-popup-wrapper-close:hover',
				'property' => 'background',
				'default' => '#6f6f6f'
			),
			'cmd-buttons-disabled-bg' => array(
				'name' => esc_html__( 'Disabled buttons background', 'hbook-admin' ),
				'selector' => '.hb-dp-cmd-wrapper a.hb-dp-disabled',
				'property' => 'background',
				'default' => '#aaaaaa'
			),
			'cmd-button-arrows' => array(
				'name' => esc_html__( 'Button arrows', 'hbook-admin' ),
				'selector' => '.hb-dp-cmd-wrapper a, .hb-dp-cmd-wrapper a:hover, a.hb-dp-cmd-close, a.hb-dp-cmd-close:hover, a.hb-people-popup-wrapper-close, a.hb-people-popup-wrapper-close:hover',
				'property' => 'color',
				'default' => '#ffffff'
			),
			'cal-borders' => array(
				'name' => esc_html__( 'Calendar inner borders', 'hbook-admin' ),
				'selector' => '.hb-dp-multi .hb-dp-month:not(.first), .hb-dp-month-row + .hb-dp-month-row, .hb-datepick-legend',
				'property' => 'border-color',
				'default' => '#cccccc'
			),
		);
	}

	public function confirm_pending_resa( $resa_id ) {
		$response = array();
		$resa = $this->hbdb->get_single( 'resa', $resa_id );
		if ( $resa['accom_num'] == 0 ) {
			$accom_num = $this->hbdb->get_first_available_accom_num( $resa['accom_id'], $resa['check_in'], $resa['check_out'] );
			if ( $accom_num ) {
				if ( $this->hbdb->update_resa_accom( $resa['id'], $resa['accom_id'], $accom_num ) === false ) {
					return array( 'status' => 'db_error' );
				}
			} else {
				$response['status'] = 'no_accom_available';
			}
		} else {
			if ( ! $this->hbdb->is_available_accom_num( $resa['accom_id'], $resa['accom_num'], $resa['check_in'], $resa['check_out'] ) ) {
				$response['status'] = 'accom_num_not_available';
				$accom_num = 0;
			} else {
				$accom_num = $resa['accom_num'];
			}
		}
		if ( $accom_num ) {
			if ( $this->hbdb->update_resa_status( $resa_id, 'confirmed' ) ) {
				$response['status'] = 'confirmed';
				$response['accom_num'] = $accom_num;
				$response['automatic_blocked_accom'] = $this->hbdb->automatic_block_accom( $resa['accom_id'], $accom_num, $resa['check_in'], $resa['check_out'], $resa['id'] );
			} else {
				$response['status'] = 'db_error';
			}
		}
		return $response;
	}

	public function can_update_resa_dates( $resa_id, $new_check_in, $new_check_out ) {
		$resa = $this->hbdb->get_single( 'resa', $resa_id );
		$new_check_in_time = strtotime( $new_check_in );
		$new_check_out_time = strtotime( $new_check_out );
		$check_in_time = strtotime( $resa['check_in'] );
		$check_out_time = strtotime( $resa['check_out'] );
		$can_update_resa = false;
		$check_availability_check_in = '';
		$check_availability_check_out = '';
		$double_check_availability = false;

		$deleted_preparation_time_blocked_accom = $this->hbdb->delete_preparation_time_blocked_accom( $resa_id );

		if ( $new_check_out_time <= $check_in_time || $new_check_in_time >= $check_out_time ) {
			$check_availability_check_in = $new_check_in;
			$check_availability_check_out = $new_check_out;
		} else {
			if ( $new_check_in_time >= $check_in_time ) {
				if ( $new_check_out_time <= $check_out_time ) {
					$can_update_resa = true;
				} else {
					$check_availability_check_in = $resa['check_out'];
					$check_availability_check_out = $new_check_out;
				}
			} else {
				$check_availability_check_in = $new_check_in;
				$check_availability_check_out = $resa['check_in'];
				if ( $new_check_out_time > $check_out_time ) {
					$double_check_availability = true;
				}
			}
		}

		if ( $check_availability_check_in ) {
			if ( $resa['accom_num'] ) {
				if ( $this->hbdb->is_available_accom_num( $resa['accom_id'], $resa['accom_num'], $check_availability_check_in, $check_availability_check_out ) ) {
					$can_update_resa = true;
				}
			} else {
				if ( $this->hbdb->is_available_accom( $resa['accom_id'], $check_availability_check_in, $check_availability_check_out ) ) {
					$can_update_resa = true;
				}
			}
		}

		if ( $double_check_availability ) {
			$check_availability_check_in = $resa['check_out'];
			$check_availability_check_out = $new_check_out;
			if ( $resa['accom_num'] ) {
				if ( ! $this->hbdb->is_available_accom_num( $resa['accom_id'], $resa['accom_num'], $check_availability_check_in, $check_availability_check_out ) ) {
					$can_update_resa = false;
				}
			} else {
				if ( ! $this->hbdb->is_available_accom( $resa['accom_id'], $check_availability_check_in, $check_availability_check_out ) ) {
					$can_update_resa = false;
				}
			}
		}

		if ( $deleted_preparation_time_blocked_accom ) {
			foreach ( $deleted_preparation_time_blocked_accom as $blocked_accom ) {
				$this->hbdb->add_blocked_accom(
					$blocked_accom['accom_id'],
					$blocked_accom['accom_num'],
					0,
					0,
					$blocked_accom['from_date'],
					$blocked_accom['to_date'],
					$blocked_accom['comment'],
					$blocked_accom['linked_resa_id'],
					1,
					$blocked_accom['uid']
				);
			}
		}

		return $can_update_resa;
	}

	public function get_string_list() {
		return $this->strings_utils->get_string_list();
	}

	public function export_lang_file() {
		if ( isset( $_POST['hb-import-export-action'] ) && ( $_POST['hb-import-export-action'] == 'export-lang' ) && wp_verify_nonce( $_POST['hb_import_export'], 'hb_import_export' ) && current_user_can( 'manage_hbook' ) ) {
			header( 'Content-Description: File Transfer' );
			header( 'Content-Disposition: attachment; filename=hbook-' . $_POST['hb-locale-export'] . '.txt' );
			header( 'Content-Type: text; charset=' . get_option( 'blog_charset' ) );
			$strings = $this->strings_utils->get_string_list();
			$strings_value = $this->hbdb->get_all_strings();
			foreach ( $strings as $string_id => $string_desc ) {
				if ( isset( $strings_value[ $string_id ]['en_US'] ) ) {
					echo( 'msgctxt "' . $strings_value[ $string_id ]['en_US'] . '"' . "\n" );
				}
				echo( 'msgid "' . $string_id . '"' . "\n" );
				if ( isset( $strings_value[ $string_id ][ $_POST['hb-locale-export'] ] ) ) {
					echo( 'msgstr "' . $strings_value[ $string_id ][ $_POST['hb-locale-export'] ] . '"' . "\n" );
				} else {
					echo( 'msgstr ""' . "\n" );
				}
				echo( "\n" );
			}
			die;
		}
	}

	public function export_settings() {
		if (
			isset( $_POST['hb-import-export-action'] ) &&
			( $_POST['hb-import-export-action'] == 'export-settings' ) &&
			wp_verify_nonce( $_POST['hb_import_export'], 'hb_import_export' ) &&
			current_user_can( 'manage_hbook' )
		) {
			header( 'Content-Description: File Transfer' );
			header( 'Content-Disposition: attachment; filename=hbook-settings.json' );
			header( 'Content-Type: text; charset=' . get_option( 'blog_charset' ) );

			$settings = array();

			$settings['accom'] = array();
			$accom_ids = $this->hbdb->get_all_accom_ids( false, true );
			foreach ( $accom_ids as $accom_id ) {
				$post_info = get_post( $accom_id, ARRAY_A );
				$post_info_to_remove = array( 'filter', 'ancestors', 'page_template', 'post_category', 'tags_input' );
				$post_info = array_diff_key( $post_info, array_flip( $post_info_to_remove ) );
				$post_meta = get_post_meta( $accom_id );
				foreach ( $post_meta as $meta_id => $meta_value ) {
					$post_meta[ $meta_id ] = $meta_value[0];
				}
				$settings['accom'][ $accom_id ] = array(
					'post_info' => $post_info,
					'post_meta' => $post_meta,
				);
			}

			$settings['tables'] = array();
			global $wpdb;
			$tables = array(
				'resa',
				'customers',
				'rates',
				'rates_rules',
				'rates_accom',
				'rates_seasons',
				'seasons',
				'seasons_dates',
				'discounts',
				'discounts_rules',
				'discounts_accom',
				'discounts_seasons',
				'coupons',
				'coupons_rules',
				'coupons_accom',
				'coupons_seasons',
				'options',
				'options_choices',
				'options_accom',
				'fees',
				'fees_accom',
				'email_templates',
				'email_templates_accom',
				'document_templates',
				'fields',
				'fields_choices',
				'strings',
				'booking_rules',
				'booking_rules_accom',
				'booking_rules_seasons',
				'accom_num_name',
				'accom_blocked',
				'ical',
				'sync_errors',
			);
			foreach ( $tables as $table_name ) {
				$db_table_name = $wpdb->prefix . 'hb_' . $table_name;
				$rows = $wpdb->get_results( "SELECT * FROM $db_table_name" , ARRAY_A );
				$settings['tables'][ $table_name ] = $rows;
			}

			$settings['options'] = array();
			$options_utils = new HbOptionsUtils();
			$options = array_merge(
				$options_utils->get_misc_settings(),
				$options_utils->get_ical_settings(),
				$options_utils->get_payment_settings(),
				$options_utils->get_appearance_settings(),
				$options_utils->get_search_form_options(),
				$options_utils->get_accom_selection_options(),
				$options_utils->get_non_standard_options()
			);
			$non_exportable_options = array(
				'hb_valid_purchase_code',
				'hb_purchase_code_error',
				'hb_purchase_code',
				'hb_last_synced',
				'hb_form_style',
				'hb_store_credit_card',
			);
			foreach ( $options as $section ) {
				foreach ( $section['options'] as $id => $option ) {
					if ( ! in_array( $id, $non_exportable_options ) ) {
						$settings['options'][ $id ] = get_option( $id );
					}
				}
			}
			echo( json_encode( $settings ) );
			die;
		}
	}

	public function export_resa() {

		if (
			isset( $_POST['hb-import-export-action'] ) &&
			( $_POST['hb-import-export-action'] == 'export-resa' ) &&
			wp_verify_nonce( $_POST['hb_import_export'], 'hb_import_export' ) &&
			( current_user_can( 'manage_hbook' ) || current_user_can( 'manage_resa' ) )
		) {
			header( 'Content-Description: File Transfer' );
			header( 'Content-Disposition: attachment; filename=hbook-reservations.csv' );
			header( 'Content-Type: text; charset=' . get_option( 'blog_charset' ) );
			echo( chr(0xEF) . chr(0xBB) . chr(0xBF) );

			$data_to_export = array_merge( $this->get_exportable_resa_fields(), $this->get_exportable_additional_info_fields(), $this->get_exportable_extra_services_fields(), $this->get_exportable_customer_fields() );
			$data_to_export_ids = $_POST['hb-resa-data-export'];
			$data_to_export_name = array();

			foreach ( $data_to_export_ids as $data_id ) {
				$data_to_export_name[] = $data_to_export[ $data_id ];
			}
			$header = implode( '","', $data_to_export_name );
			$header = '"' . $header . '"';
			echo( $header . "\n" );

			$accom = $this->hbdb->get_all_accom();
			$accom_tmp = array();
			foreach( $accom as $accom_id => $accom_name ) {
				$accom_num_name = $this->hbdb->get_accom_num_name( $accom_id );
				$accom_tmp[ $accom_id ] = array(
					'name' => $accom_name,
					'num_name' => $accom_num_name
				);
			}
			$accom = $accom_tmp;

			$extras = $this->hbdb->get_all( 'options' );
			$tmp_extras = array();
			foreach ( $extras as $ex ) {
				$tmp_extras[ $ex['id'] ] = $ex;
			}
			$extras = $tmp_extras;

			$extra_choices = $this->hbdb->get_all( 'options_choices' );
			$extra_name = array();
			foreach ( $extra_choices as $choice ) {
				$extra_name[ $choice['id'] ] = $choice['name'];
			}

			$resa = array();
			if ( $_POST['hb-export-resa-selection'] == 'all' ) {
				if ( ( count( $accom ) == 0 ) || ( count( $_POST['hb-export-resa-accom'] ) == count( $accom ) ) ) {
					$resa = $this->hbdb->get_all_resa_by_date();
				} else {
					foreach ( $_POST['hb-export-resa-accom'] as $accom_id ) {
						$resa = array_merge( $resa, $this->hbdb->get_all_resa_by_date_by_accom( $accom_id ) );
					}
					usort( $resa, function( $a, $b ) {
						return strcmp( $a['received_on'], $b['received_on'] );
					});
				}
			} else {
				if ( $_POST['hb-export-resa-selection'] == 'received-date' ) {
					$from_date = $_POST['hb-export-resa-selection-received-date-from'];
					$to_date = $_POST['hb-export-resa-selection-received-date-to'];
					$date_type = 'received_on';
				} else if ( $_POST['hb-export-resa-selection'] == 'check-in-date' ) {
					$from_date = $_POST['hb-export-resa-selection-check-in-date-from'];
					$to_date = $_POST['hb-export-resa-selection-check-in-date-to'];
					$date_type = 'check_in';
				} else if ( $_POST['hb-export-resa-selection'] == 'check-out-date' ) {
					$from_date = $_POST['hb-export-resa-selection-check-out-date-from'];
					$to_date = $_POST['hb-export-resa-selection-check-out-date-to'];
					$date_type = 'check_out';
				}
				if ( ! $from_date ) {
					$from_date = '2000-01-01';
				}
				if ( ! $to_date ) {
					$to_date = '2100-01-01';
				} else {
					$to_date .= ' 23:59:59';
				}
				if ( ( count( $accom ) == 0 ) || ( count( $_POST['hb-export-resa-accom'] ) == count( $accom ) ) ) {
					$resa = $this->hbdb->get_resa_between_dates( $date_type, $from_date, $to_date );
				} else {
					foreach ( $_POST['hb-export-resa-accom'] as $accom_id ) {
						$resa = array_merge(
							$resa,
							$this->hbdb->get_resa_between_dates_by_accom( $date_type, $from_date, $to_date, $accom_id )
						);
					}
					usort( $resa, function( $a, $b ) {
						return strcmp( $a[ $date_type ], $b[ $date_type ] );
					});
				}

			}


			foreach ( $resa as $resa_key => $resa_data ) {
				$resa[ $resa_key ]['resa_id'] = $resa_data['id'];

				if ( isset( $accom[ $resa_data['accom_id'] ] ) ) {
					$resa[ $resa_key ]['accom_type'] = $accom[ $resa_data['accom_id'] ]['name'];
					if ( isset( $accom[ $resa_data['accom_id'] ]['num_name'][ $resa_data['accom_num'] ] ) ) {
						$resa[ $resa_key ]['accom_num'] = $accom[ $resa_data['accom_id'] ]['num_name'][ $resa_data['accom_num'] ];
					} else {
						$resa[ $resa_key ]['accom_num'] = '';
					}
				} else {
					$resa[ $resa_key ]['accom_type'] = '';
				}

				$resa[ $resa_key ]['number_of_nights'] = $this->get_number_of_nights( $resa[ $resa_key ]['check_in'], $resa[ $resa_key ]['check_out'] );

				$customer_info = array();
				$customer = $this->hbdb->get_single( 'customers', $resa[ $resa_key ]['customer_id'] );
				if ( $customer ) {
					$customer_info = array(
						'customer_id' => $customer['id']
					);
					$customer_info_json = json_decode( $customer['info'], true );
					if ( is_array( $customer_info_json ) ) {
						foreach ( $customer_info_json as $info_id => $info_value ) {
							$customer_info[ $info_id ] = $info_value;
						}
					}
				}

				$optional_info = array();
				if ( isset( $resa_data['optional_info'] ) ) {
					$optional_info_json = json_decode( $resa_data['optional_info'], true );
					if ( is_array( $optional_info_json ) ) {
						foreach ( $optional_info_json as $op ) {
							$optional_info[ $op['info_id'] ] = $op['info_value'];
						}
					}
				}

				$resa_extra_services = array();
				if ( $resa_data['options'] ) {
					$resa_extra_services = json_decode( $resa_data['options'], true );
				}
				$extra_services = array();
				if ( is_array( $resa_extra_services ) ) {
					foreach ( $resa_extra_services as $resa_extra_id => $resa_extra ) {
						if ( isset( $extras[ $resa_extra_id ] ) ) {
							if (
								$extras[ $resa_extra_id ]['apply_to_type'] == 'quantity' ||
								$extras[ $resa_extra_id ]['apply_to_type'] == 'quantity-per-day'
							) {
								if ( is_array( $resa_extra ) ) {
									$extra_services[ 'extra_' . $resa_extra_id ] = $resa_extra['quantity'];
								} else {
									$extra_services[ 'extra_' . $resa_extra_id ] = $resa_extra;
								}
							} else if ( $extras[ $resa_extra_id ]['choice_type'] == 'single' ) {
								$extra_services[ 'extra_' . $resa_extra_id ] = 'X';
							} else if ( $extras[ $resa_extra_id ]['choice_type'] == 'multiple' ) {
								if ( is_array( $resa_extra ) ) {
									$chosen_extra = $resa_extra['chosen'];
								} else {
									$chosen_extra = $resa_extra;
								}
								if ( isset( $extra_name[ $chosen_extra ] ) ) {
									$extra_services[ 'extra_' . $resa_extra_id ] = $extra_name[ $chosen_extra ];
								}
							}
						}
					}
				}

				$resa_additional_info = array();
				if ( $resa_data['additional_info'] ) {
					$resa_additional_info = json_decode( $resa_data['additional_info'], true );
				}
				$additional_info = array();
				if ( is_array( $resa_additional_info ) ) {
					$additional_info = $resa_additional_info;
				}

				$resa[ $resa_key ] = array_merge( $resa[ $resa_key ], $extra_services, $optional_info, $customer_info, $additional_info );
			}

			foreach ( $resa as $resa_data ) {
				$row = array();
				foreach ( $data_to_export_ids as $data_id ) {
					if ( isset( $resa_data[ $data_id ] ) ) {
						$row[] = $resa_data[ $data_id ];
					} else {
						$row[] = '';
					}
				}
				$row = implode( '","', $row );
				$row = '"' . $row . '"' . "\n";
				echo( $row );
			}

			die;
		}
	}

	public function export_customers() {
		if (
			isset( $_POST['hb-import-export-action'] ) &&
			( $_POST['hb-import-export-action'] == 'export-customers' ) &&
			wp_verify_nonce( $_POST['hb_import_export'], 'hb_import_export' ) &&
			current_user_can( 'manage_hbook' )
		) {
			header( 'Content-Description: File Transfer' );
			header( 'Content-Disposition: attachment; filename=hbook-customers.csv' );
			header( 'Content-Type: text; charset=' . get_option( 'blog_charset' ) );
			echo( chr(0xEF) . chr(0xBB) . chr(0xBF) );

			$data_to_export = $this->get_exportable_customer_fields( 'customers' );
			$data_to_export_ids = $_POST['hb-customers-data-export'];
			$data_to_export_name = array();

			foreach ( $data_to_export_ids as $data_id ) {
				$data_to_export_name[] = $data_to_export[ $data_id ];
			}
			$header = implode( '","', $data_to_export_name );
			$header = '"' . $header . '"';
			echo( $header . "\n" );

			$customers = $this->hbdb->get_all( 'customers' );
			foreach ( $customers as $customer ) {
				$customer_info = array(
					'id' => $customer['id']
				);
				$customer_info_json = json_decode( $customer['info'], true );
				if ( is_array( $customer_info_json ) ) {
					foreach ( $customer_info_json as $info_id => $info_value ) {
						$customer_info[ $info_id ] = $info_value;
					}
				}

				$row = array();
				foreach ( $data_to_export_ids as $data_id ) {
					if ( isset( $customer_info[ $data_id ] ) ) {
						$row[] = $customer_info[ $data_id ];
					} else {
						$row[] = '';
					}
				}
				$row = implode( '","', $row );
				$row = '"' . $row . '"' . "\n";
				echo( $row );
			}
			die;
		}
	}

	public function open_document() {
		if (
			isset( $_GET['hbook_doc_id'] ) &&
			(
				current_user_can( 'read_resa' ) ||
				current_user_can( 'manage_resa' ) ||
				current_user_can( 'manage_hbook' )
			)
		) {
			$doc_id = intval( $_GET['hbook_doc_id'] );
			$resa_id = intval( $_GET['resa_id'] );
			$resa_is_parent = 0;
			if ( isset( $_GET['is_parent'] ) && ( $_GET['is_parent'] == 1 ) ) {
				$resa_is_parent = 1;
			}
			$doc = $this->hbdb->get_single( 'document_templates', $doc_id );
			if ( $doc ) {
				if ( $doc['lang'] ) {
					$this->email_doc_locale = $doc['lang'];
					remove_all_filters( 'locale' );
					add_filter( 'locale', array( $this, 'email_doc_filter_locale' ) );
				}
				$content  = $this->replace_resa_vars_with_value( $resa_id, true, $doc['content'], $resa_is_parent );
				echo( $content );
				if ( $doc['lang'] ) {
					remove_filter( 'locale', array( $this, 'email_doc_filter_locale' ) );
				}
			}
			die;
		}
	}

	public function get_exportable_resa_fields() {
		return array(
			'resa_id' => esc_html__( 'Num', 'hbook-admin' ),
			'check_in' => esc_html__( 'Check-in', 'hbook-admin' ),
			'check_out' => esc_html__( 'Check-out', 'hbook-admin' ),
			'number_of_nights' => esc_html__( 'Number of nights', 'hbook-admin' ),
			'accom_type' => esc_html__( 'Accommodation type', 'hbook-admin' ),
			'accom_num' => esc_html__( 'Accommodation number', 'hbook-admin' ),
			'adults' => esc_html__( 'Adults', 'hbook-admin' ),
			'children' => esc_html__( 'Children', 'hbook-admin' ),
			'price' => esc_html__( 'Price', 'hbook-admin' ),
			'paid' => esc_html__( 'Amount paid', 'hbook-admin'),
			'payment_type' => esc_html__( 'Payment type', 'hbook-admin'),
			'payment_gateway' => esc_html__( 'Payment Gateway', 'hbook-admin'),
			'coupon' => esc_html__( 'Coupon code', 'hbook-admin'),
			'coupon_value' => esc_html__( 'Coupon amount', 'hbook-admin'),
			'currency' => esc_html__( 'Currency', 'hbook-admin'),
			'status' => esc_html__( 'Status', 'hbook-admin'),
			'admin_comment' => esc_html__( 'Comment', 'hbook-admin'),
			'received_on' => esc_html__( 'Received on', 'hbook-admin'),
			'origin' => esc_html__( 'Origin', 'hbook-admin' )
		);
	}

	public function get_exportable_additional_info_fields() {
		$exportable_fields = array();
		$fields = $this->hbdb->get_additional_booking_info_form_fields();
		foreach ( $fields as $field ) {
			$exportable_fields[ $field['id'] ] = $field['name'];
		}
		return $exportable_fields;
	}

	public function get_exportable_customer_fields( $for = 'resa' ) {
		if ( $for == 'resa' ) {
			$exportable_fields = array(
				'customer_id' => esc_html__( 'Id', 'hbook-admin' )
			);
		} else {
			$exportable_fields = array(
				'id' => esc_html__( 'Id', 'hbook-admin' )
			);
		}
		$fields = $this->hbdb->get_customer_form_fields();
		foreach ( $fields as $field ) {
			$exportable_fields[ $field['id'] ] = $field['name'];
		}
		return $exportable_fields;
	}

	public function get_exportable_extra_services_fields() {
		$extras = $this->hbdb->get_all( 'options' );
		$exportable_extra = array();
		foreach ( $extras as $extra ) {
			$exportable_extra[ 'extra_' . $extra['id'] ] = $extra['name'];
		}
		return $exportable_extra;
	}

	public function get_posted_customer_info() {
		$customer_info = array();
		$customer_fields = $this->hbdb->get_customer_form_fields();
		foreach ( $customer_fields as $field ) {
			if ( $field['type'] == 'checkbox' ) {
				if ( isset( $_POST[ 'hb_' . $field['id'] ] ) ) {
					$info_value = implode( ', ', $_POST[ 'hb_' . $field['id'] ] );
				} else {
					$info_value = '';
				}
			} else {
				$info_value = $_POST[ 'hb_' . $field['id'] ];
			}
			$info_value = stripslashes( strip_tags( $info_value ) );
			if ( $info_value != '' ) {
				$customer_info[ $field['id'] ] = $info_value;
			}
		}
		return $customer_info;
	}

	public function get_posted_additional_booking_info() {
		$additional_info = array();
		$additional_fields = $this->hbdb->get_additional_booking_info_form_fields();
		foreach ( $additional_fields as $field ) {
			if ( isset( $_POST[ 'hb_' . $field['id'] ] ) ) {
				if ( $field['type'] == 'checkbox' ) {
					$info_value = implode( ', ', $_POST[ 'hb_' . $field['id'] ] );
				} else {
					$info_value = $_POST[ 'hb_' . $field['id'] ];
				}
			} else {
				$info_value = '';
			}
			$info_value = stripslashes( strip_tags( $info_value ) );
			if ( $info_value != '' ) {
				$additional_info[ $field['id'] ] = $info_value;
			}
		}
		return $additional_info;
	}

	public function check_plugin() {
		$body_args = array(
			'purchase_code' => get_option( 'hb_purchase_code' ),
		);
		$response = wp_remote_post( 'https://maestrel.com/scripts/verify-purchase.php', array( 'body' => $body_args ) );
		if ( ! is_wp_error( $response ) && $response['body'] == 'invalid' ) {
			update_option( 'hb_valid_purchase_code', 'no' );
		}

		$gateway_list = $this->get_payment_gateways();
		foreach ( $gateway_list as $gateway => $data ) {
			if ( ( 'paypal' != $data->id ) && ( 'stripe' != $data->id ) ) {
				$purchase_code_option = 'hb_' . $data->id . '_purchase_code';
				$valid_purchase_code_option = 'hb_' . $data->id . 'valid_purchase_code';
				$body_args = array(
					'purchase_code' => get_option( $purchase_code_option ),
				);
				$response = wp_remote_post( 'https://maestrel.com/scripts/verify-website-addons-purchase.php', array( 'body' => $body_args ) );
				if ( ! is_wp_error( $response ) && $response['body'] == 'invalid' ) {
					update_option( $valid_purchase_code_option, 'no' );
				}
			}
		}
	}

	public function set_http_api_curl_ssl_version( &$handle ) {
		curl_setopt( $handle, CURLOPT_SSLVERSION, 6 );
	}

	public function get_blog_datetime( $datetime ) {
		$tzstring = get_option( 'timezone_string' );
		$offset = get_option( 'gmt_offset' );
		if ( empty( $tzstring ) && 0 != $offset && floor( $offset ) == $offset ) {
			$offset_st = $offset > 0 ? "-$offset" : '+' . absint( $offset );
			$tzstring  = 'Etc/GMT' . $offset_st;
		}
		if ( empty( $tzstring ) ) {
			$tzstring = 'UTC';
		}

		$dt = new DateTime( $datetime, new DateTimeZone( 'UTC' ) );
		$dt->setTimezone( new DateTimeZone( $tzstring ) );
		return $dt->format('Y-m-d H:i:s');
	}

	public function verify_addon_purchase_code ( $new_purchase_code, $product ) {
		$old_purchase_code_option = 'hb_' . $product . '_purchase_code';
		$valid_purchase_code_option = 'hb_' . $product . '_valid_purchase_code';
		$error_option = 'hb_' . $product . '_purchase_code_error';
		$error_text_option = 'hb_' . $product . '_purchase_code_error_text';

		$old_purchase_code = get_option( $old_purchase_code_option );
		update_option( $old_purchase_code_option, $new_purchase_code );
		if ( isset( $_POST['hb-forced-licence-validation'] ) && $_POST['hb-forced-licence-validation'] == 'hb-forced' ) {
			update_option( $valid_purchase_code_option, 'yes' );
			return;
		}
		if ( isset( $_POST['hb-licence-validation-code'] ) ) {
			if ( $_POST['hb-licence-validation-code'] == md5( $new_purchase_code . '-' . site_url() ) ) {
				update_option( $valid_purchase_code_option, 'yes' );
			} else {
				update_option( $error_option, 'wrong-validation-code' );
				update_option( $valid_purchase_code_option, 'error' );
			}
			return;
		}

		$body_args = array(
			'hb_addon_purchase_code' => $new_purchase_code,
			'hb_addon_old_purchase_code' => $old_purchase_code,
			'site_url' => site_url(),
		);
		$response = wp_remote_post( 'https://maestrel.com/scripts/verify-website-addons-purchase.php', array( 'body' => $body_args ) );
		$error = '';
		if ( is_wp_error( $response ) ) {
			$error = $response->get_error_message();
		} else {
			$valid_response = array( 'yes', 'no', 'already', 'removed' );
			if ( in_array( $response['body'], $valid_response ) ) {
				update_option( $valid_purchase_code_option, $response['body'] );
			} else if ( $response['body'] == 'invalid' ) {
				update_option( $valid_purchase_code_option, 'no' );
			} else {
				$error = strip_tags( $response['body'] );
				if ( ! $error ) {
					$error = 'HBook Addon - Unknown error.';
				}
			}
		}
		if ( $error ) {
			update_option( $error_option, 'no-online-validation' );
			update_option( $error_text_option, $error );
			update_option( $valid_purchase_code_option, 'error' );
		}
	}

	public function verify_purchase_code( $new_purchase_code ) {
		$old_purchase_code = get_option( 'hb_purchase_code' );
		update_option( 'hb_purchase_code', $new_purchase_code );
		if ( isset( $_POST['hb-forced-licence-validation'] ) && $_POST['hb-forced-licence-validation'] == 'hb-forced' ) {
			update_option( 'hb_valid_purchase_code', 'yes' );
			return;
		}
		if ( isset( $_POST['hb-licence-validation-code'] ) ) {
			if ( $_POST['hb-licence-validation-code'] == md5( $new_purchase_code . '-' . site_url() ) ) {
				update_option( 'hb_valid_purchase_code', 'yes' );
			} else {
				update_option( 'hb_purchase_code_error', 'wrong-validation-code' );
				update_option( 'hb_valid_purchase_code', 'error' );
			}
			return;
		}

		$body_args = array(
			'purchase_code' => $new_purchase_code,
			'old_purchase_code' => $old_purchase_code,
			'site_url' => site_url(),
		);
		$response = wp_remote_post( 'https://maestrel.com/scripts/verify-purchase.php', array( 'body' => $body_args ) );
		$error = '';
		if ( is_wp_error( $response ) ) {
			$error = $response->get_error_message();
		} else {
			$valid_response = array( 'yes', 'no', 'already', 'removed' );
			if ( in_array( $response['body'], $valid_response ) ) {
				update_option( 'hb_valid_purchase_code', $response['body'] );
			} else if ( $response['body'] == 'invalid' ) {
				update_option( 'hb_valid_purchase_code', 'no' );
			} else {
				$error = strip_tags( $response['body'] );
				if ( ! $error ) {
					$error = 'HBook - Unknown error.';
				}
			}
		}
		if ( $error ) {
			update_option( 'hb_purchase_code_error', 'no-online-validation' );
			update_option( 'hb_purchase_code_error_text', $error );
			update_option( 'hb_valid_purchase_code', 'error' );
		}
	}

	public function get_hbook_roles_caps () {
		return array(
			'hb_resa_reader' => array(
				'label' => esc_html__( 'Reservation reader', 'hbook-admin' ),
				'caps' => array(
					'read_resa_list',
					'read_resa',
					'read'
				),
			),
			'hb_resa_manager' => array(
				'label' => esc_html__( 'Reservation manager', 'hbook-admin' ),
				'caps' => array(
					'manage_resa',
					'read_resa_customer',
					'read_resa_price',
					'read_resa_list',
					'read_resa',
					'read'
				),
			),
			'hb_pricing_manager' => array(
				'label' => esc_html__( 'Pricing manager', 'hbook-admin' ),
				'caps' => array(
					'delete_others_accoms',
					'delete_accoms',
					'delete_private_accoms',
					'delete_published_accoms',
					'edit_other_accoms',
					'edit_accoms',
					'edit_private_accoms',
					'edit_published_accoms',
					'publish_accoms',
					'read_private_accoms',
					'manage_hbook',
					'manage_hb_menu',
					'manage_hb_accommodation',
					'manage_hb_seasons',
					'manage_hb_rules',
					'manage_hb_rates',
					'manage_hb_options',
					'manage_hb_fees',
					'manage_hb_customers',
					'manage_hb_reservations',
					'manage_resa',
					'read_resa_customer',
					'read_resa_price',
					'read_resa_list',
					'read_resa',
					'read'
				),
			),
			'hb_hbook_manager' => array(
				'label' => esc_html__( 'HBook manager', 'hbook-admin' ),
				'caps' => array(
					'delete_others_accoms',
					'delete_accoms',
					'delete_private_accoms',
					'delete_published_accoms',
					'edit_other_accoms',
					'edit_accoms',
					'edit_private_accoms',
					'edit_published_accoms',
					'publish_accoms',
					'read_private_accoms',
					'manage_hbook',
					'manage_hb_menu',
					'manage_hb_accommodation',
					'manage_hb_seasons',
					'manage_hb_rules',
					'manage_hb_rates',
					'manage_hb_options',
					'manage_hb_fees',
					'manage_hb_forms',
					'manage_hb_details',
					'manage_hb_payment',
					'manage_hb_ical',
					'manage_hb_emails',
					'manage_hb_documents',
					'manage_hb_customers',
					'manage_hb_appearance',
					'manage_hb_text',
					'manage_hb_langfiles',
					'manage_hb_help',
					'manage_hb_misc',
					'manage_hb_licence',
					'manage_hb_reservations',
					'manage_resa',
					'read_resa_customer',
					'read_resa_price',
					'read_resa_list',
					'read_resa',
					'read'
				),
			),
		);
	}

	public function get_hbook_pages() {
		if ( get_option( 'hb_valid_purchase_code' ) == 'yes' || strpos( site_url(), '127.0.0.1' ) || strpos( site_url(), 'localhost' ) ) {
			return array(
				array(
					'id' => 'hb_reservations',
					'name' => esc_html__( 'Reservations', 'hbook-admin' ),
					'icon' => 'dashicons-calendar-alt',
				),
				array(
					'id' => 'hb_accommodation',
					'name' => esc_html__( 'Accommodation', 'hbook-admin' ),
					'icon' => 'dashicons-admin-home',
				),
				array(
					'id' => 'hb_seasons',
					'name' => esc_html__( 'Seasons', 'hbook-admin' ),
					'icon' => 'dashicons-calendar',
				),
				array(
					'id' => 'hb_rules',
					'name' => esc_html__( 'Booking rules', 'hbook-admin' ),
					'icon' => 'dashicons-admin-network',
				),
				array(
					'id' => 'hb_rates',
					'name' => esc_html__( 'Rates', 'hbook-admin' ),
					'icon' => 'dashicons-tag',
				),
				array(
					'id' => 'hb_options',
					'name' => esc_html__( 'Extra services', 'hbook-admin' ),
					'icon' => 'dashicons-forms',
				),
				array(
					'id' => 'hb_fees',
					'name' => esc_html__( 'Fees', 'hbook-admin' ),
					'icon' => 'dashicons-money',
				),
				array(
					'id' => 'hb_forms',
					'name' => esc_html__( 'Search form', 'hbook-admin' ),
					'icon' => 'dashicons-search',
				),
				array(
					'id' => 'hb_details',
					'name' => esc_html__( 'Details form', 'hbook-admin' ),
					'icon' => 'dashicons-admin-users',
				),
				array(
					'id' => 'hb_payment',
					'name' => esc_html__( 'Payment', 'hbook-admin' ),
					'icon' => 'dashicons-vault',
				),
				array(
					'id' => 'hb_ical',
					'name' => esc_html__( 'ICal sync', 'hbook-admin' ),
					'icon' => 'dashicons-update',
				),
				array(
					'id' => 'hb_emails',
					'name' => esc_html__( 'Emails', 'hbook-admin' ),
					'icon' => 'dashicons-email-alt',
				),
				array(
					'id' => 'hb_documents',
					'name' => esc_html__( 'Documents', 'hbook-admin' ),
					'icon' => 'dashicons-media-default',
				),
				array(
					'id' => 'hb_customers',
					'name' => esc_html__( 'Customers', 'hbook-admin' ),
					'icon' => 'dashicons-groups',
				),
				array(
					'id' => 'hb_appearance',
					'name' => esc_html__( 'Appearance', 'hbook-admin' ),
					'icon' => 'dashicons-admin-appearance',
				),
				array(
					'id' => 'hb_text',
					'name' => esc_html__( 'Text', 'hbook-admin' ),
					'icon' => 'dashicons-editor-paste-text',
				),
				array(
					'id' => 'hb_langfiles',
					'name' => esc_html__( 'Languages', 'hbook-admin' ),
					'icon' => 'dashicons-translation',
				),
				array(
					'id' => 'hb_misc',
					'name' => esc_html__( 'Misc', 'hbook-admin' ),
					'icon' => 'dashicons-admin-generic',
				),
				array(
					'id' => 'hb_licence',
					'name' => esc_html__( 'Licence', 'hbook-admin' ),
					'icon' => 'dashicons-welcome-write-blog',
				),
				array(
					'id' => 'hb_help',
					'name' => esc_html__( 'Help', 'hbook-admin' ),
					'icon' => 'dashicons-sos',
				),
			);
		} else {
			return array(
				array(
					'id' => 'hb_licence',
					'name' => esc_html__( 'Licence', 'hbook-admin' ),
					'icon' => 'dashicons-welcome-write-blog',
				),
				array(
					'id' => 'hb_help',
					'name' => esc_html__( 'Help', 'hbook-admin' ),
					'icon' => 'dashicons-sos',
				),
			);
		}
	}
}