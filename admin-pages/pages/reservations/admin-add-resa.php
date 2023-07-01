<?php
class HbAdminPageReservationsAdminAddResa {

	public function __construct( $hbdb, $utils, $booking_form_render ) {
		$this->hbdb = $hbdb;
		$this->utils = $utils;
		$this->booking_form_render = $booking_form_render;
	}

	public function display() {
	?>

		<form id="hb-process-new-admin-resa" data-bind="submit: admin_add_resa"></form>

		<div id="hb-add-resa-section" class="hb-resa-section">

			<h3 id="hb-admin-add-resa-toggle" class="hb-resa-section-toggle">
				<?php esc_html_e( 'Add a reservation', 'hbook-admin' ); ?>
				<span class="dashicons dashicons-arrow-down"></span>
				<span class="dashicons dashicons-arrow-up"></span>
			</h3>

			<div id="hb-admin-add-resa" class="stuffbox">

				<?php
				$atts = array(
					'form_id' => '',
					'all_accom' => '', // '', 'yes'
					'search_only' => 'no',
					'search_form_placeholder' => 'no',
					'accom_id' => '',
					'redirection_url' => '#',
					'force_display_thumb' => 'no',
					'force_display_desc' => 'no',
					'is_admin' => 'yes',
				);
				echo( $this->booking_form_render->render( $atts ) );
				?>

			</div>

		</div>

		<hr/>

	<?php
	}

}