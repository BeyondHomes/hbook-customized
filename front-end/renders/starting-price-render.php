<?php
class HBookStartingPrice extends HBookRender {

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
					return esc_html__( 'Invalid shortcode. Use: [hb_starting_price accom_id="ID"]', 'hbook-admin' );
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
		$output = '<div class="hb-starting-price-shortcode-wrapper">';
		$starting_price = get_post_meta( $accom_id, 'accom_starting_price', true );
		if ( $starting_price ) {
			$starting_price_text = str_replace( '%price', $this->utils->price_with_symbol( $starting_price ), $this->strings['accom_starting_price'] );
			$starting_price_text .= ' ' . $this->strings['accom_starting_price_duration_unit'];
			$output .=  '<p><small>' . wp_kses_post( $starting_price_text ) . '</small></p>';
		}
		$output .= '</div>';
		$output = apply_filters( 'hb_starting_price_markup', $output );

		return $output;
	}
}