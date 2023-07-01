<?php
class HbResaCoupon {

	private $hbdb;
	private $utils;
	private $coupon;

	public function __construct( $hbdb, $utils, $coupon ) {
		$this->hbdb = $hbdb;
		$this->utils = $utils;
		$this->coupon = $coupon;
	}

	public function is_valid( $accom_ids, $check_in, $check_out ) {
		return
			$this->is_valid_accom( $accom_ids ) &&
			$this->is_valid_season( $check_in, $check_out ) &&
			$this->is_valid_rule( $check_in, $check_out );
	}

	public function is_still_valid() {
		return
			$this->is_valid_use_count() &&
			$this->is_valid_date();
	}

	private function is_valid_accom( $accom_ids ) {
		return
			$this->coupon['all_accom'] ||
			( $this->coupon['accom'] && array_intersect( $accom_ids, explode( ',', $this->coupon['accom'] ) ) );
	}

	private function is_valid_season( $check_in, $check_out ) {
		if ( $this->coupon['all_seasons'] ) {
			return true;
		}
		if ( ! $this->coupon['seasons'] ) {
			return false;
		}
		$coupon_seasons = explode( ',', $this->coupon['seasons'] );
		$current_night = date( 'Y-m-d', strtotime( $check_in ) );
		while ( strtotime( $check_out ) > strtotime( $current_night ) ) {
			$season_id = $this->hbdb->get_season( $current_night );
			if ( ! in_array( $season_id, $coupon_seasons ) ) {
				return false;
			}
			$current_night = date( 'Y-m-d', strtotime( $current_night . ' + 1 day' ) );
		}
		return true;
	}

	private function is_valid_rule( $check_in, $check_out ) {
		if ( ! $this->coupon['rule'] ) {
			return true;
		} else {
			$rule = $this->hbdb->get_rule_by_id( $this->coupon['rule'] );
			$check_in_day = $this->utils->get_day_num( $check_in );
			$check_out_day = $this->utils->get_day_num( $check_out );
			$allowed_check_in_days = explode( ',', $rule['check_in_days'] );
			$allowed_check_out_days = explode( ',', $rule['check_out_days'] );
			$nb_nights = $this->utils->get_number_of_nights( $check_in, $check_out );
			if (
				in_array( $check_in_day, $allowed_check_in_days ) &&
				in_array( $check_out_day, $allowed_check_out_days ) &&
				$nb_nights >= $rule['minimum_stay'] &&
				$nb_nights <= $rule['maximum_stay']
			) {
				return true;
			} else{
				return false;
			}
		}
	}

	private function is_valid_use_count() {
		if ( ! $this->coupon['max_use_count'] ) {
			return true;
		} else {
			if ( $this->coupon['use_count'] < $this->coupon['max_use_count'] ) {
				return true;
			} else {
				return false;
			}
		}
	}

	private function is_valid_date() {
		if ( $this->coupon['date_limit'] == '0000-00-00' ) {
			return true;
		} else {
			if ( substr( $this->utils->get_blog_datetime( current_time( 'mysql', 1 ) ), 0, 10 ) <= $this->coupon['date_limit'] ) {
				return true;
			} else {
				return false;
			}
		}
	}
}