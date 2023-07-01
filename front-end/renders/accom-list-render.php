<?php
class HBookAccomList extends HBookRender {

	public function render( $atts ) {
		$this->utils->load_jquery();
		$this->utils->load_front_end_script( 'accommodation-listing' );
		$output = '<div class="hb-accom-listing-shortcode-wrapper">';
		$accom = $this->hbdb->get_all_accom_ids();
		if ( $atts['accom_ids'] != '' ) {
			$accom_type_ids = explode( ',', $atts['accom_ids'] );
			foreach ( $accom as $key => $accom_id ) {
				if ( false === array_search( $accom_id, $accom_type_ids ) ) {
					unset( $accom[ $key ] );
				}
			}
		}
		$nb_columns = intval( $atts['nb_columns'] );
		$percent = round( ( 100 - ( 2 * $nb_columns ) ) / $nb_columns );
		$i = 1;
		$output .= '<div class="hb-accom-listing-row">';
		foreach( $accom as $accom_id ) {
			$output .= '<div class="hb-accom-listing-column" style="' . esc_attr( 'width: ' . $percent . '%; max-width: ' . $percent . '%; min-width: 300px;"' ) . '">';
			$output .= '<div class="' . esc_attr( 'hb-accom-listing-item hb-accom-listing-item-' . $accom_id ) . '">';
			$output .= '<div class="hb-accom-listing-desc-wrapper">';
			if ( $atts['show_thumb'] ) {
				$output .= '<div class="hb-accom-listing-desc">';
				$thumb_mark_up = $this->utils->get_thumb_mark_up( $accom_id, $atts['thumb_width'], $atts['thumb_height'], 'hb-accom-listing-thumb alignleft' );
				if ( $thumb_mark_up && $atts['link_thumb'] ) {
					$output .= '<a class="hb-thumbnail-link" href="' . esc_url( $this->utils->get_accom_link( $accom_id ) ) . '" target="_blank">' . wp_kses_post( $thumb_mark_up ) . '</a>';
				} else {
					$output .= $thumb_mark_up;
				}
			}
			if ( in_array( $atts['title_tag'], array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ) ) ) {
				$title_tag = $atts['title_tag'];
			} else {
				$title_tag = 'h2';
			}
			$accom_title = esc_html( $this->utils->get_accom_title ( $accom_id ) );
			$output .= '<' . esc_html( $title_tag ) . '>';
			if ( $atts['link_title'] ) {
				$output .= '<a href="' . esc_url( $this->utils->get_accom_link( $accom_id ) ) . '" target="_blank">' . $accom_title . '</a>';
			} else {
				$output .= $accom_title;
			}
			$output .= '</' . esc_html( $title_tag ) . '>';
			$starting_price = get_post_meta( $accom_id, 'accom_starting_price', true );
			if ( $starting_price ) {
				$starting_price_text = str_replace( '%price', $this->utils->price_with_symbol( $starting_price ), $this->strings['accom_starting_price'] );
				$starting_price_text .= ' ' . $this->strings['accom_starting_price_duration_unit'];
				$output .=  '<p><small>' . wp_kses_post( $starting_price_text ) . '</small></p>';
			}
			$output .= '<p>' . wp_kses_post( $this->utils->get_accom_list_desc( $accom_id ) ) . '</p>';
			$output .= '</div></div>';

			if ( $atts['view_button'] || $atts['book_button'] ) {
				$output .= '<div class="hb-accom-listing-actions-wrapper">';
				if ( $atts['view_button'] ){
					$output .= '<p class="hb-listing-view-accom">';
					$output .= '<input type="submit" data-accom-url="';
					$output .= esc_url( $this->utils->get_accom_link( $accom_id ) );
					$output .= '" value="' . esc_attr( $this->strings['view_accom_details_button'] ) . '" />';
					$output .= '</p>';
				}
				if ( $atts['book_button'] ) {
					$output .= '<p class="hb-listing-book-accom"><input type="submit" value="' . esc_attr( $this->strings['accom_book_now_button'] ) . '" /></p></div>';
					if ( $atts['redirection_url'] != '#' ) {
						$booking_form_shortcode = '[hb_booking_form accom_id="' . intval( $accom_id ) . '" redirection_url="' . esc_url( $atts['redirection_url'] ) . '"]';
					} else {
						$booking_form_shortcode = '[hb_booking_form accom_id="' . intval( $accom_id ) . '"]';
					}
					$output .= '<div class="hb-clearfix" /></div>';
					$output .= '<div class="' . esc_attr( 'hb-accom-listing-booking-form listing-booking-form-' . $accom_id ) . '">' . do_shortcode( $booking_form_shortcode ) . '</div>';
				} else {
					$output .= '</div><div class="hb-clearfix" /></div>';
				}
			}
			$output .= '</div></div>';

			if ( $i == count( $accom ) ) {
				$output .= '</div>';
			}
			$i++;
		}

		$output .= '</div>';

		$output = apply_filters( 'hb_accommodation_list_markup', $output );

		return $output;
	}
}