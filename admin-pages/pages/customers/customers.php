<?php
class HbAdminPageCustomers extends HbAdminPage {

	public function __construct( $page_id, $hbdb, $utils, $options_utils ) {
		$customer_fields = $hbdb->get_customer_form_fields();
		$customer_fields_tmp = array();
		foreach ( $customer_fields as $field ) {
			$customer_fields_tmp[ $field['id'] ] = array(
				'name' => $field['name'],
				'type' => $field['type'],
			);
		}
		$customer_fields = $customer_fields_tmp;

		$this->data = array(
			'hb_text' => array(
				'confirm_delete_customer' => esc_html__( 'Delete customer?', 'hbook-admin' ),
				'no_export_data_selected' => esc_html__( 'Please select the data you want to export.', 'hbook-admin' ),
				'one_resa' => esc_html__( '1 reservation', 'hbook-admin' ),
				'several_resa' => esc_html__( '%s reservations', 'hbook-admin' ),
				'first_name' => esc_html__( 'First name', 'hbook-admin' ),
				'last_name' => esc_html__( 'Last name', 'hbook-admin' ),
				'email' => esc_html__( 'Email', 'hbook-admin' ),
			),
			'hb_customer_fields' => $customer_fields,
			'hb_customers' => $hbdb->get_all_customers(),
			'hb_resa_customer_page_url' => admin_url( 'admin.php?page=hb_reservations&customer_id=' ),
			'hb_saved_sorting' => get_option( 'hb_customers_saved_sorting', 'last_name_asc' ),
		);
		parent::__construct( $page_id, $hbdb, $utils, $options_utils );
	}

	public function display() {
	?>

	<div class="wrap">

		<h1><?php esc_html_e( 'Customers', 'hbook-admin' ); ?></h1><hr/>

		<?php $this->display_right_menu(); ?>

		<div class="hb-customers-section">

			<h3><?php esc_html_e( 'Customer list', 'hbook-admin' ); ?></h3>

			<div id="hb-customers">

				<!-- ko if: customers_list().length == 0 -->
				<?php esc_html_e( 'There is no customers.', 'hbook-admin' ); ?>
				<!-- /ko -->

				<!-- ko if: customers_list().length > 0 -->

				<p class="hb-customers-filter">
					<input data-bind="value: filter_customer_search, valueUpdate: 'afterkeydown'" type="text" placeholder="<?php esc_attr_e( 'Search a customer...', 'hbook-admin' ); ?>" />
					<select data-bind="value: customer_sort">
						<option value="last_name_asc"><?php esc_html_e( 'Sort by Last name &rarr;', 'hbook-admin' ); ?></option>
						<option value="last_name_desc"><?php esc_html_e( 'Sort by Last name &larr;', 'hbook-admin' ); ?></option>
						<option value="id_asc"><?php esc_html_e( 'Sort by Id &rarr;', 'hbook-admin' ); ?></option>
						<option value="id_desc"><?php esc_html_e( 'Sort by Id &larr;', 'hbook-admin' ); ?></option>
					</select>
				</p>

				<?php $this->display_customers_pagination(); ?>

				<div class="hb-table hb-customers-table">

					<div class="hb-table-head hb-clearfix">
						<div class="hb-table-head-data hb-table-head-data-customer-id"><?php esc_html_e( 'Id', 'hbook-admin' ); ?></div>
						<div class="hb-table-head-data"><?php esc_html_e( 'First name', 'hbook-admin' ); ?></div>
						<div class="hb-table-head-data"><?php esc_html_e( 'Last name', 'hbook-admin' ); ?></div>
						<div class="hb-table-head-data"><?php esc_html_e( 'Email', 'hbook-admin' ); ?></div>
						<div class="hb-table-head-data"><?php esc_html_e( 'Other informations', 'hbook-admin' ); ?></div>
						<div class="hb-table-head-data hb-table-head-data-action"><?php esc_html_e( 'Actions', 'hbook-admin' ); ?></div>
					</div>

					<!-- ko foreach: customers_paginated -->
					<div data-bind="attr: { class: 'hb-table-row hb-clearfix ' + anim_class() }">
						<div class="hb-table-data hb-table-head-data-customer-id" data-bind="text: id"></div>
						<div class="hb-table-data" data-bind="text: first_name"></div>
						<div class="hb-table-data" data-bind="text: last_name"></div>
						<div class="hb-table-data" data-bind="text: email"></div>
						<div class="hb-table-data" data-bind="html: other_info"></div>
						<div class="hb-table-data hb-table-data-action">
							<a href="#" title="<?php esc_attr_e( 'Edit', 'hbook-admin' ); ?>" class="dashicons dashicons-edit" data-bind="visible: ! editing(), click: $root.edit_customer"></a>
							<a href="#" title="<?php esc_attr_e( 'Delete', 'hbook-admin' ); ?>" class="dashicons dashicons-trash" data-bind="visible: ! deleting(), click: $root.delete_customer"></a>
							<span data-bind="visible: deleting" class="hb-ajaxing hb-resa-updating">
								<span class="spinner"></span>
								<span><?php esc_html_e( 'Deleting...', 'hbook-admin' ); ?></span>
							</span>
						</div>
						<div data-bind="visible: editing()" class="hb-customer-edit-wrapper">
							<h4><?php esc_html_e( 'Customer details', 'hbook-admin' ) ?></h4>
							<a data-bind="click: $root.save_customer, visible: ! saving()" href="#" class="button-primary"><?php esc_html_e( 'Save', 'hbook-admin' ); ?></a>
							<input type="button" disabled data-bind="visible: saving()" href="#" class="button-primary" value="<?php esc_attr_e( 'Saving', 'hbook-admin' ); ?>" />
							<a data-bind="click: $root.cancel_edit_customer" href="#" class="button"><?php esc_html_e( 'Cancel', 'hbook-admin' ); ?></a>
							<hr/>
							<div data-bind="html: customer_info_editing_markup"></div>
							<hr/>
							<a data-bind="click: $root.save_customer, visible: ! saving()" href="#" class="button-primary"><?php esc_html_e( 'Save', 'hbook-admin' ); ?></a>
							<input type="button" disabled data-bind="visible: saving()" href="#" class="button-primary" value="<?php esc_attr_e( 'Saving', 'hbook-admin' ); ?>" />
							<a data-bind="click: $root.cancel_edit_customer" href="#" class="button"><?php esc_html_e( 'Cancel', 'hbook-admin' ); ?></a>
						</div>
					</div>
					<!-- /ko -->

				</div>

				<?php $this->display_customers_pagination(); ?>

				<!-- /ko -->

			</div>

		</div>

		<hr/>

		<div class="hb-customers-section">

			<h3 id="hb-export-customers-toggle" class="hb-customers-section-toggle">
				<?php esc_html_e( 'Export customers', 'hbook-admin' ); ?>
				<span class="dashicons dashicons-arrow-up"></span>
				<span class="dashicons dashicons-arrow-down"></span>
			</h3>

			<div id="hb-export-customers" class="stuffbox">

				<form id="hb-export-customers-form" method="POST">

					<h4><?php esc_html_e( 'Select data to be exported', 'hbook-admin' ); ?></h4>

					<p>
						<a id="hb-export-customers-select-all" href="#"><?php esc_html_e( 'Select all', 'hbook-admin' ); ?></a> -
						<a id="hb-export-customers-unselect-all" href="#"><?php esc_html_e( 'Unselect all', 'hbook-admin' ); ?></a>
					</p>

					<?php
					$exportable_customer_fields = $this->utils->get_exportable_customer_fields( 'customers' );
					foreach ( $exportable_customer_fields as $field_id => $field_name ) :
						$data_export_field_id = 'hb-customers-data-export-' . $field_id;
						?>

						<p>
							<input id="<?php echo( esc_attr( $data_export_field_id ) ); ?>" type="checkbox" name="hb-customers-data-export[]" value="<?php echo( esc_attr( $field_id ) ); ?>">
							<label for="<?php echo( esc_attr( $data_export_field_id ) ); ?>"><?php echo( esc_html( $field_name ) ); ?></label>
						</p>

					<?php
					endforeach;
					?>

					<p>
						<a href="#" id="hb-export-customers-download" class="button"><?php esc_html_e( 'Download file', 'hbook-admin' ); ?></a>
						&nbsp;&nbsp;
						<a href="#" id="hb-export-customers-cancel"><?php esc_html_e( 'Cancel', 'hbook-admin' ); ?></a>
					</p>

					<input type="hidden" name="hb-import-export-action" value="export-customers" />
					<?php wp_nonce_field( 'hb_import_export', 'hb_import_export' ); ?>

				</form>

			</div>

		</div>

		<hr/>

	</div>

	<?php
	}

	private function display_customers_pagination() {
	?>

	<!-- ko if: customers_total_pages() > 1 -->
	<p>
		<a href="#" class="button" data-bind="click: customers_first_page">&laquo;</a>
		<a href="#" class="button" data-bind="click: customers_previous_page">&lsaquo;</a>
		&nbsp;&nbsp;
		<?php
		printf(
			esc_html__( 'Viewing page %s of %s', 'hbook-admin' ),
			'<span data-bind="text: customers_current_page_number"></span>',
			'<span data-bind="text: customers_total_pages"></span>'
		);
		?>
		&nbsp;&nbsp;
		<a href="#" class="button" data-bind="click: customers_next_page">&rsaquo;</a>
		<a href="#" class="button" data-bind="click: customers_last_page">&raquo;</a>
	</p>
	<!-- /ko -->

	<?php
	}
}