<?php
class HBookShortcodes {

	private $hbdb;
	private $utils;
	private $strings;

	public function __construct( $hbdb, $utils ) {
		$this->hbdb = $hbdb;
		$this->utils = $utils;
	}

	public function accommodation_list( $atts ) {
		$atts = shortcode_atts(
			array(
				'show_thumbnail' => 'yes',
				'thumbnail_link' => 'yes',
				'title_link' => 'yes',
				'title_tag' => 'h2',
				'thumb_width' => 250,
				'thumb_height' => 200,
				'accom_ids' => '',
				'book_button' => 'yes',
				'view_button' => 'no',
				'redirection_url' => '#',
				'nb_columns' => 2,
			),
			$atts,
			'hb_accommodation_list'
		);

		$render_atts = $atts;
		if ( $atts['title_link'] == 'yes' ) {
			$render_atts['link_title'] = true;
		} else {
			$render_atts['link_title'] = false;
		}
		if ( $atts['show_thumbnail'] == 'yes' ) {
			$render_atts['show_thumb'] = true;
		} else {
			$render_atts['show_thumb'] = false;
		}
		if ( $atts['thumbnail_link'] == 'yes' ) {
			$render_atts['link_thumb'] = true;
		} else {
			$render_atts['link_thumb'] = false;
		}
		if ( $atts['book_button'] == 'yes' ) {
			$render_atts['book_button'] = true;
		} else {
			$render_atts['book_button'] = false;
		}
		if ( $atts['view_button'] == 'yes' ) {
			$render_atts['view_button'] = true;
		} else {
			$render_atts['view_button'] = false;
		}

		require_once $this->utils->plugin_directory . '/front-end/renders/hbook-render.php';
		require_once $this->utils->plugin_directory . '/front-end/renders/accom-list-render.php';
		$accom_list = new HBookAccomList( $this->hbdb, $this->utils );
		return $accom_list->render( $render_atts );
	}

	public function availability( $atts ) {
		$atts = shortcode_atts(
			array(
				'accom_id' => '',
				'calendar_sizes' => '2x1,1x1'
			),
			$atts,
			'hb_availability'
		);

		require_once $this->utils->plugin_directory . '/front-end/renders/hbook-render.php';
		require_once $this->utils->plugin_directory . '/front-end/renders/availability-render.php';
		$availability = new HBookAvailability( $this->hbdb, $this->utils );
		return $availability->render( $atts );
	}

	public function rates( $atts ) {
		$atts = shortcode_atts(
			array(
				'accom_id' => '',
				'type' => 'normal', // 'normal', 'adult', 'child'
				'days' => '',
				'season' => '',
				'seasons' => '',
				'rule' => '',
				'show_global_price' => 'no',
				'nights' => '0',
				'custom_text_after_amount' => '',
				'show_season_name' => 'yes',
				'chronological' => 'no',
			),
			$atts,
			'hb_rates'
		);

		$render_atts = $atts;
		$render_atts['type'] = 'accom';
		if ( $atts['type'] == 'adult' ) {
			$render_atts['type'] = 'extra_adults';
		} else if ( $atts['type'] == 'child' ) {
			$render_atts['type'] = 'extra_children';
		}
		if ( $atts['chronological'] == 'yes' ) {
			$render_atts['chrono'] = true;
		} else {
			$render_atts['chrono'] = false;
		}
		if ( $atts['show_season_name'] == 'yes') {
			$render_atts['show_season_name'] = true;
		} else {
			$render_atts['show_season_name'] = false;
		}
		if ( $atts['show_global_price'] == 'yes') {
			$render_atts['show_global_price'] = true;
		} else {
			$render_atts['show_global_price'] = false;
		}

		require_once $this->utils->plugin_directory . '/front-end/renders/hbook-render.php';
		require_once $this->utils->plugin_directory . '/front-end/renders/rates-render.php';
		$rates = new HBookRates( $this->hbdb, $this->utils );
		return $rates->render( $render_atts );
	}

	public function booking_form( $atts ) {
		$atts = shortcode_atts(
			array(
				'form_id' => '',
				'all_accom' => '', // '', 'yes'
				'search_only' => 'no',
				'search_form_placeholder' => 'no',
				'accom_id' => '',
				'redirection_url' => '#',
				'force_display_thumb' => 'no',
				'force_display_desc' => 'no',
				'thank_you_page_url' => '',
                                'region_filter' => 'no',
                                'price_filter' => 'no',
                                'bedrooms_filter' => 'no',
                                'bathrooms_filter' => 'no',
                                'beds_filter' => 'no',
                                'beachfront_filter' => 'no',
                                'show_banner' => 'no',
                                'extra_class' => ''
			),
			$atts,
			'hb_booking_form'
		);
		$atts['is_admin'] = 'no';

		require_once $this->utils->plugin_directory . '/front-end/renders/hbook-render.php';
		require_once $this->utils->plugin_directory . '/front-end/renders/booking-form-render.php';
		$booking_form = new HBookBookingForm( $this->hbdb, $this->utils );
		return $booking_form->render( $atts );
	}

	public function reservation_summary( $atts ) {
		require_once $this->utils->plugin_directory . '/front-end/renders/hbook-render.php';
		require_once $this->utils->plugin_directory . '/front-end/renders/resa-summary-render.php';
		$resa_summary = new HBookResaSummary( $this->hbdb, $this->utils );
		return $resa_summary->render();
	}

	public function starting_price( $atts ) {
		$atts = shortcode_atts(
			array(
				'accom_id' => '',
			),
			$atts,
			'hb_starting_price'
		);

		require_once $this->utils->plugin_directory . '/front-end/renders/hbook-render.php';
		require_once $this->utils->plugin_directory . '/front-end/renders/starting-price-render.php';
		$starting_price = new HBookStartingPrice( $this->hbdb, $this->utils );
		return $starting_price->render( $atts );
	}
}