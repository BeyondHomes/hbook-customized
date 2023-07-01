<?php
class HbAdminPageForms extends HbAdminPage {

	public function __construct( $page_id, $hbdb, $utils, $options_utils ) {
		$this->data = array(
			'hb_text' => array(
				'form_saved' => esc_html__( 'Settings have been saved.', 'hbook-admin' ),
			)
		);
		parent::__construct( $page_id, $hbdb, $utils, $options_utils );
	}

	public function display() {
		$form_class = '';
		if ( get_option( 'hb_multiple_accom_booking_front_end' ) != 'enabled' ) {
			$form_class = 'hb-single-accom-booking';
		}
		?>

	<div class="wrap">

		<form id="hb-settings-form" class="<?php echo( esc_attr( $form_class ) ); ?>">

			<h1><?php esc_html_e( 'Search form and Accommodation selection', 'hbook-admin' ); ?></h1>
			<?php $this->display_right_menu(); ?>

			<hr/>

			<h3><?php esc_html_e( 'Search form', 'hbook-admin' ); ?></h3>

			<?php
			$search_form_options = $this->options_utils->get_search_form_options();
			foreach ( $search_form_options['search_form_options']['options'] as $id => $option ) {
				$function_to_call = 'display_' . $option['type'] . '_option';
				$this->options_utils->$function_to_call( $id, $option );
			}
			$this->options_utils->display_save_options_section();
			?>

			<hr/>

			<h3><?php esc_html_e( 'Accommodation selection', 'hbook-admin' ); ?></h3>

			<?php
			$accom_selection_options = $this->options_utils->get_accom_selection_options();
			foreach ( $accom_selection_options['accom_selection_options']['options'] as $id => $option ) {
				$function_to_call = 'display_' . $option['type'] . '_option';
				$this->options_utils->$function_to_call( $id, $option );
				if ( $id == 'hb_thumb_display' ) {
					echo( '<div class="hb-accom-thumb-options-wrapper">' );
				}
				if ( $id == 'hb_search_accom_thumb_height' ) {
					echo( '</div><!-- end .hb-accom-thumb-options-wrapper -->' );
				}
				if ( $id == 'hb_display_price' ) {
					echo( '<div class="hb-price-options-wrapper">' );
				}
				if ( $id == 'hb_display_detailed_accom_price' ) {
					echo( '</div><!-- end .hb-price-options-wrapper -->' );
				}
				if ( $id == 'hb_display_price_breakdown' ) {
					echo( '<div class="hb-price-breakdown-options-wrapper">' );
				}
				if ( $id == 'hb_display_detailed_accom_price' ) {
					echo( '</div><!-- end .hb-price-breakdown-options-wrapper -->' );
				}
			}
			$this->options_utils->display_save_options_section();
			?>

			<input type="hidden" name="action" value="hb_update_forms_settings" />
			<input id="hb-nonce" type="hidden" name="nonce" value="" />

		</form>

	</div><!-- end .wrap -->

	<?php
	}
}