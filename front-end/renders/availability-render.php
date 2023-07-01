<?php
class HBookAvailability extends HBookRender {

	public function render( $atts ) {
		$accom_id = $atts['accom_id'];
		if ( $accom_id != 'all' ) {
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
					return esc_html__( 'Invalid shortcode. Use: [hb_availability accom_id="ID"]', 'hbook-admin' );
				} else if ( get_post_type( $accom_id ) == 'hb_accommodation' ) {
					return esc_html__( 'Invalid shortcode. Please use the id of an accommodation which is set in the website default language.', 'hbook-admin' );
				} else {
					return sprintf( esc_html__( 'Invalid shortcode. Could not find an accommodation whose id is %s.', 'hbook-admin' ), esc_html( $accom_id ) );
				}
			}
		}

		$this->utils->load_jquery();
		$this->utils->load_datepicker();
		$this->utils->load_front_end_script( 'utils' );
		$this->utils->load_front_end_script( 'availability' );

		$calendar_sizes_cols = array();
		$calendar_sizes_rows = array();
		$calendar_sizes = explode( ',', $atts['calendar_sizes'] );
		foreach ( $calendar_sizes as $size ) {
			$size = trim( $size );
			$cols_rows = explode( 'x', $size );
			$calendar_sizes_cols[] = intval( $cols_rows[0] );
			$calendar_sizes_rows[ intval( $cols_rows[0] ) ] = intval( $cols_rows[1] );
		}
		rsort( $calendar_sizes_cols );
		$calendar_sizes = array();
		foreach ( $calendar_sizes_cols as $col ) {
			$calendar_sizes[] = array(
				'cols' => $col,
				'rows' => $calendar_sizes_rows[ $col ]
			);
		}

		static $availability_front_data_loaded;
		if ( ! $availability_front_data_loaded ) {
			$availability_front_data_loaded = true;
			$availability_text = array(
				'legend_past' => $this->strings['legend_past'],
				'legend_closed' => $this->strings['legend_closed'],
				'legend_occupied' => $this->strings['legend_occupied'],
				'legend_check_out_only' => $this->strings['legend_check_out_only'],
				'legend_check_in_only' => $this->strings['legend_check_in_only'],
				'legend_available' => $this->strings['legend_available'],
			);

			$this->utils->hb_script_var( 'hb-availability-script', 'hb_availability_text', $availability_text );
		}

		$status_days = $this->utils->get_status_days( $accom_id, 0 );
		$output = '' .
			'<div class="hb-availability-calendar-wrapper">' .
				'<div class="hb-availability-calendar-centered">' .
					'<div ' .
						'class="hb-availability-calendar" ' .
						"data-calendar-sizes='" . json_encode( $this->utils->hb_esc( $calendar_sizes ) ) . "' " .
						"data-status-days='" . json_encode( $this->utils->hb_esc( $status_days ) ) . "'" .
					'>' .
					'</div>';
		$legend_available = $this->strings['legend_available'];
		$legend_occupied = $this->strings['legend_occupied'];
		if ( $legend_available || $legend_occupied ) {
			$output .= '' .
					'<p class="hb-avail-caption-wrapper hb-dp-clearfix">';
			if ( $legend_available ) {
				$calendar_color_values = json_decode( get_option( 'hb_calendar_colors' ), true );
				if (
					isset( $calendar_color_values[ 'cal-bg' ] ) &&
					isset( $calendar_color_values[ 'available-day-bg' ] ) &&
					( $calendar_color_values[ 'cal-bg' ] != $calendar_color_values[ 'available-day-bg' ] )
				) {
				$output .= '' .
						'<span class="hb-avail-caption hb-avail-caption-available"></span>' .
						'<span class="hb-avail-caption-text hb-avail-caption-text-available">' . $legend_available . '</span>' .
						'<br class="hb-avail-line-break" />';
				}
			}
			if ( $legend_occupied ) {
				$output .= '' .
						'<span class="hb-avail-caption hb-avail-caption-occupied"></span>' .
						'<span class="hb-avail-caption-text hb-avail-caption-text-occupied">' . $legend_occupied . '</span>';
			}
			$output .= '' .
					'</p>';
		}
		$output .= '' .
				'</div>' .
			'</div>';

		return $output;
	}
}