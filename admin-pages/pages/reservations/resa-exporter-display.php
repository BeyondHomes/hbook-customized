<?php
class HbAdminPageReservationsExport {

	private $utils;
	private $accom;

	public function __construct( $utils, $accom ) {
		$this->utils = $utils;
		$this->accom = $accom;
	}

	public function display() {
	?>

	<h3 id="hb-export-resa-toggle" class="hb-resa-section-toggle">
		<?php esc_html_e( 'Export reservations', 'hbook-admin' ); ?>
		<span class="dashicons dashicons-arrow-down"></span>
		<span class="dashicons dashicons-arrow-up"></span>
	</h3>

	<div id="hb-export-resa" class="stuffbox">

		<form id="hb-export-resa-form" method="POST">

			<h4><?php esc_html_e( 'Select reservations to be exported:', 'hbook-admin' ); ?></h4>

			<p class="hb-export-date-selection">
				<input id="hb-export-resa-selection-all" name="hb-export-resa-selection" type="radio" value="all" checked />
				<label for="hb-export-resa-selection-all"><?php esc_html_e( 'All dates', 'hbook-admin' ); ?><br/>
			</p>
			<p class="hb-export-date-selection">
				<input id="hb-export-resa-selection-received-date" name="hb-export-resa-selection" type="radio" value="received-date" />
				<label for="hb-export-resa-selection-received-date"><?php esc_html_e( 'Received between', 'hbook-admin' ); ?></label>
				<input id="hb-export-resa-selection-received-date-from" name="hb-export-resa-selection-received-date-from" class="hb-input-date hb-export-resa-date" type="text" />
				<?php esc_html_e( 'and', 'hbook-admin' ); ?>
				<input id="hb-export-resa-selection-received-date-to" name="hb-export-resa-selection-received-date-to" class="hb-input-date hb-export-resa-date" type="text" /><br/>
			</p>
			<p class="hb-export-date-selection">
				<input id="hb-export-resa-selection-check-in-date" name="hb-export-resa-selection" type="radio" value="check-in-date" />
				<label for="hb-export-resa-selection-check-in-date"><?php esc_html_e( 'Check-in between', 'hbook-admin' ); ?></label>
				<input id="hb-export-resa-selection-check-in-date-from" name="hb-export-resa-selection-check-in-date-from" class="hb-input-date hb-export-resa-date" type="text" />
				<?php esc_html_e( 'and', 'hbook-admin' ); ?>
				<input id="hb-export-resa-selection-check-in-date-to" name="hb-export-resa-selection-check-in-date-to" class="hb-input-date hb-export-resa-date" type="text" /><br/>
			</p>
			<p class="hb-export-date-selection">
				<input id="hb-export-resa-selection-check-out-date" name="hb-export-resa-selection" type="radio" value="check-out-date" />
				<label for="hb-export-resa-selection-check-out-date"><?php esc_html_e( 'Check-out between', 'hbook-admin' ); ?></label>
				<input id="hb-export-resa-selection-check-out-date-from" name="hb-export-resa-selection-check-out-date-from" class="hb-input-date hb-export-resa-date" type="text" />
				<?php esc_html_e( 'and', 'hbook-admin' ); ?>
				<input id="hb-export-resa-selection-check-out-date-to" name="hb-export-resa-selection-check-out-date-to" class="hb-input-date hb-export-resa-date" type="text" />
			</p>

			<?php
			$exported_accom_selection_class = '';
			if ( count( $this->accom ) <= 1 ) {
				$exported_accom_selection_class = 'hb-export-resa-no-accom-choice';
			}
			?>

			<div class="<?php echo( esc_attr( $exported_accom_selection_class ) ); ?>">

				<h4><?php esc_html_e( 'Accommodation:', 'hbook-admin' ); ?></h4>
				<p>
					<a id="hb-export-resa-select-all-accom" href="#"><?php esc_html_e( 'Select all', 'hbook-admin' ); ?></a> -
					<a id="hb-export-resa-unselect-all-accom" href="#"><?php esc_html_e( 'Unselect all', 'hbook-admin' ); ?></a>
				</p>

				<?php
				foreach ( $this->accom as $accom_id => $accom_name ) {
					$input_id = 'hb-export-resa-accom-' . $accom_id;
					?>

					<p>
						<input
							id="<?php echo( esc_attr( $input_id ) ); ?>"
							type="checkbox"
							name="hb-export-resa-accom[]"
							value="<?php echo( esc_attr( $accom_id ) ); ?>"
							checked
						/>
						<label for="<?php echo( esc_attr( $input_id ) ); ?>">
							<?php echo( esc_html( $accom_name ) ); ?>
						</label>
					</p>

				<?php } ?>

			</div>

			<h4><?php esc_html_e( 'Select data to be exported:', 'hbook-admin' ); ?></h4>

			<p>
				<a id="hb-export-resa-select-all-data" href="#"><?php esc_html_e( 'Select all', 'hbook-admin' ); ?></a> -
				<a id="hb-export-resa-unselect-all-data" href="#"><?php esc_html_e( 'Unselect all', 'hbook-admin' ); ?></a>
			</p>

			<?php
			$exportable_resa_fields = $this->utils->get_exportable_resa_fields();
			$exportable_additional_info_fields = $this->utils->get_exportable_additional_info_fields();
			$exportable_customer_fields = $this->utils->get_exportable_customer_fields();
			$exportable_extra_services_fields = $this->utils->get_exportable_extra_services_fields();

			$exportable_fields = array_merge(
				$exportable_resa_fields,
				$exportable_additional_info_fields,
				$exportable_extra_services_fields,
				array( 'customer_info' => 'customer_info' ),
				$exportable_customer_fields
			);
			foreach ( $exportable_fields as $field_id => $field_name ) :
				if ( $field_id == 'customer_info' ) :
				?>

					<p><?php esc_html_e( 'Customer information:', 'hbook-admin' );?></p>

				<?php else : ?>

					<p>
						<?php $input_id = 'hb-resa-data-export-' . $field_id; ?>
						<input
							id="<?php echo( esc_attr( $input_id ) ); ?>"
							type="checkbox"
							name="hb-resa-data-export[]"
							value="<?php echo( esc_attr( $field_id ) ); ?>"
							checked
						/>
						<label for="<?php echo( esc_attr( $input_id ) ); ?>">
						<?php
						$non_editable_fields = array(
							'first_name' => esc_html__( 'First name', 'hbook-admin' ),
							'last_name' => esc_html__( 'Last name', 'hbook-admin' ),
							'email' => esc_html__( 'Email', 'hbook-admin' ),
						);
						if ( in_array( $field_id, array_keys( $non_editable_fields ) ) ) {
							echo( esc_html( $non_editable_fields[ $field_id ] ) );
						} else {
							echo( esc_html( $field_name ) );
						}
						?>
						</label>
					</p>

				<?php
				endif;
			endforeach;
			?>

			<p>
				<a href="#" id="hb-export-resa-download" class="button"><?php esc_html_e( 'Download file', 'hbook-admin' ); ?></a>
				&nbsp;&nbsp;
				<a href="#" id="hb-export-resa-cancel"><?php esc_html_e( 'Cancel', 'hbook-admin' ); ?></a>
			</p>

			<input type="hidden" name="hb-import-export-action" value="export-resa" />
			<?php wp_nonce_field( 'hb_import_export', 'hb_import_export' ); ?>

		</form>

	</div>

	<hr/>

	<?php
	}
}