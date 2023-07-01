<?php
class HbAdminPageReservationsDisplayHelper {

	private $accom_list_info;
	private $email_templates;
	private $document_templates;
	private $is_site_multi_lang;
	private $site_langs;
	private $is_resa_customer;
	private $currency_symbol;
	private $site_has_extras;

	public function __construct( $accom_list_info, $email_templates, $document_templates, $is_site_multi_lang, $site_langs, $is_resa_customer, $currency_symbol, $site_has_extras ) {
		$this->accom_list_info = $accom_list_info;
		$this->email_templates = $email_templates;
		$this->document_templates = $document_templates;
		$this->is_site_multi_lang = $is_site_multi_lang;
		$this->site_langs = $site_langs;
		$this->is_resa_customer = $is_resa_customer;
		$this->currency_symbol = $currency_symbol;
		$this->site_has_extras = $site_has_extras;
	}

	public function display_resa_calendar() {
	?>

	<div class="hb-resa-section">

		<h3><?php esc_html_e( 'Calendar', 'hbook-admin' ); ?></h3>
		<div>
			<select id="hb-resa-cal-accommodation">
				<option value="all"><?php esc_html_e( 'All accommodations', 'hbook-admin' ); ?></option>
				<?php
				foreach( $this->accom_list_info as $accom_id => $accom_info ) { ?>
					<option value="<?php echo( esc_attr( $accom_id ) ); ?>"><?php echo( esc_html( $accom_info['name'] ) ); ?></option>
				<?php
				}
				?>
			</select>
		</div><br/>

		<div id="hb-resa-cal-wrapper">
			<div id="hb-resa-cal-scroller">
				<table id="hb-resa-accom-table" class="hb-resa-cal-table"></table>
				<table id="hb-resa-cal-table" class="hb-resa-cal-table"></table>
			</div>
		</div>

	</div><!-- end .hb-resa-section -->

	<hr/>

	<?php
	}

	public function display_resa_details() {
		if ( $this->user_can_view( 'read_resa_list' ) ) {
	?>

	<div class="hb-resa-section">

		<h3><?php esc_html_e( 'Reservation details', 'hbook-admin' ); ?></h3>

		<!-- ko if: selected_resa() == 0 -->
		<p><?php esc_html_e( 'Click on a number in the calendar to view the reservation details', 'hbook-admin' ); ?></p>
		<!-- /ko -->

		<!-- ko if: selected_resa() != 0 -->
		<p>
			<a href="#" data-bind="click: hide_selected_resa"><?php esc_html_e( 'Hide', 'hbook-admin' ); ?></a>
		</p>

		<table class="wp-list-table widefat hb-resa-table">

			<thead>
			<?php $this->display_resa_thead_tfoot(); ?>
			</thead>

			<tbody data-bind="foreach { data: resa_detailed }">
			<?php $this->display_resa_tr(); ?>
			</tbody>

		</table>
		<!-- /ko -->

	</div>

	<hr/>

	<?php
		}
	}

	public function display_resa_list() {
		if ( $this->user_can_view( 'read_resa_list' ) ) {
	?>

	<div class="hb-resa-section">

		<h3><?php esc_html_e( 'Reservation list', 'hbook-admin' ); ?></h3>

		<!-- ko if: resa().length == 0 -->
		<?php esc_html_e( 'No reservations yet.', 'hbook-admin' ); ?>
		<!-- /ko -->

		<!-- ko if: resa().length != 0 -->

		<p>
			<select data-bind="value: resa_filter">
				<option value="none"><?php esc_html_e( 'No filter', 'hbook-admin' ); ?></option>
				<option value="resa_id"><?php esc_html_e( 'Filter by Reservation id', 'hbook-admin' ); ?></option>
				<?php if ( get_option( 'hb_resa_alphanum' ) == 'yes' ) { ?>
				<option value="resa_alphanum"><?php esc_html_e( 'Filter by Reservation number', 'hbook-admin' ); ?></option>
				<?php } ?>
				<option value="customer"><?php esc_html_e( 'Filter by Customer', 'hbook-admin' ); ?></option>
				<option value="check_in_date"><?php esc_html_e( 'Filter by Check-in date', 'hbook-admin' ); ?></option>
				<option value="check_out_date"><?php esc_html_e( 'Filter by Check-out date', 'hbook-admin' ); ?></option>
				<option value="check_in_out_date"><?php esc_html_e( 'Filter by Check-in and check-out dates', 'hbook-admin' ); ?></option>
				<option value="active_resa_date"><?php esc_html_e( 'Filter by Active reservations', 'hbook-admin' ); ?></option>
				<option value="accom"><?php esc_html_e( 'Filter by Accommodation', 'hbook-admin' ); ?></option>
				<option value="status"><?php esc_html_e( 'Filter by Status', 'hbook-admin' ); ?></option>
			</select>
			<select data-bind="value: resa_sort">
				<option value="received_date_desc"><?php esc_html_e( 'Sort by Received date &larr;', 'hbook-admin' ); ?></option>
				<option value="received_date_asc"><?php esc_html_e( 'Sort by Received date &rarr;', 'hbook-admin' ); ?></option>
				<option value="check_in_date_desc"><?php esc_html_e( 'Sort by Check-in date &larr;', 'hbook-admin' ); ?></option>
				<option value="check_in_date_asc"><?php esc_html_e( 'Sort by Check-in date &rarr;', 'hbook-admin' ); ?></option>
			</select>
		</p>
		<p class="hb-resa-filter-id" data-bind="visible: resa_filter() == 'resa_id'">
			<input type="text" data-bind="value: resa_filter_id, valueUpdate: 'afterkeydown'" placeholder="<?php esc_attr_e( 'Enter reservation id', 'hbook-admin' ); ?>" />
		</p>
		<p class="hb-resa-filter-alphanum" data-bind="visible: resa_filter() == 'resa_alphanum'">
			<input type="text" data-bind="value: resa_filter_alphanum, valueUpdate: 'afterkeydown'" placeholder="<?php esc_attr_e( 'Enter reservation number', 'hbook-admin' ); ?>" />
		</p>
		<p class="hb-resa-filter-customer" data-bind="visible: resa_filter() == 'customer'">
			<input type="text" data-bind="value: resa_filter_customer, valueUpdate: 'afterkeydown'" placeholder="<?php esc_attr_e( 'Enter customer name or email', 'hbook-admin' ); ?>" />
		</p>
		<p data-bind="visible: resa_filter() == 'status'">
			<select data-bind="value: resa_filter_status">
				<option value="confirmed"><?php esc_html_e( 'Confirmed', 'hbook-admin' ); ?></option>
				<option value="cancelled"><?php esc_html_e( 'Cancelled', 'hbook-admin' ); ?></option>
				<option value="pending"><?php esc_html_e( 'Pending', 'hbook-admin' ); ?></option>
				<option value="new"><?php esc_html_e( 'New', 'hbook-admin' ); ?></option>
			</select>
		</p>
		<p data-bind="visible: resa_filter() == 'accom'">
			<select data-bind="value: resa_filter_accom_id">
				<option value="all"><?php esc_html_e( 'All', 'hbook-admin' ); ?></option>
				<?php
				foreach( $this->accom_list_info as $accom_id => $accom_info ) { ?>
					<option value="<?php echo( esc_attr( $accom_id ) ); ?>"><?php echo( esc_html( $accom_info['name'] ) ); ?></option>
				<?php
				}
				?>
			</select>
			<select data-bind="
				visible: resa_filter_accom_id() != 'all',
				options: resa_filter_accom_num_name,
				optionsValue: 'num',
				optionsText: 'name',
				value: resa_filter_accom_num">
			</select>
		</p>
		<?php
		$resa_filter_dates_types = array( 'check_in', 'check_out', 'check_in_out', 'active_resa' );
		foreach ( $resa_filter_dates_types as $filter_type ) :
		?>
		<p class="hb-resa-filter-date" data-bind="<?php echo( esc_attr( "visible: resa_filter() == '" . $filter_type . "_date'" ) ); ?>">
			<?php esc_html_e( 'Between', 'hbook-admin' ); ?>&nbsp;
			<input type="text" data-bind="<?php echo( esc_attr( 'value: resa_filter_' . $filter_type . '_from' ) ); ?>" class="hb-input-date hb-filter-date-from" /><a href="#" class="hb-filter-clear-date dashicons dashicons-no" /></a>&nbsp;
			<?php esc_html_e( 'and', 'hbook-admin' ); ?>&nbsp;
			<input type="text" data-bind="<?php echo( esc_attr( 'value: resa_filter_' . $filter_type . '_to' ) ); ?>" class="hb-input-date hb-filter-date-to" /><a href="#" class="hb-filter-clear-date dashicons dashicons-no" /></a>
		</p>
		<?php endforeach; ?>

		<p class="hb-bulk-action-wrapper">
			<select class="hb-resa-bulk-action">
				<option value="no_action"><?php esc_html_e( 'Bulk actions', 'hbook-admin' ); ?></option>
				<option value="confirm"><?php esc_html_e( 'Confirm', 'hbook-admin' ); ?></option>
				<option value="cancel"><?php esc_html_e( 'Cancel', 'hbook-admin' ); ?></option>
				<option value="delete"><?php esc_html_e( 'Delete', 'hbook-admin' ); ?></option>
			</select>
			<input class="button" type="button" value="<?php esc_attr_e( 'Apply', 'hbook-admin' ); ?>" data-bind="click: do_bulk_action" />
			<span class="spinner"></span>
			<span id="hb-resa-bulk-action-msg"></span>
		</p>

		<?php $this->display_resa_pagination(); ?>

		<?php
		$resa_table_class = 'wp-list-table widefat hb-resa-table';
		if ( $this->site_has_extras ) {
			$resa_table_class .= ' hb-resa-table-has-extras';
		}
		?>
		<table class="<?php echo( esc_attr( $resa_table_class ) ); ?>">

			<thead>
			<?php $this->display_resa_thead_tfoot(); ?>
			</thead>

			<tbody data-bind="foreach { data: resa_paginated }">
			<?php $this->display_resa_tr(); //, beforeRemove: hide_resa, afterAdd: show_resa?>
			</tbody>

			<tfoot data-bind="visible: resa_paginated().length > 2">
			<?php $this->display_resa_thead_tfoot(); ?>
			</tfoot>

		</table>

		<p class="hb-bulk-action-wrapper">
			<select class="hb-resa-bulk-action">
				<option value="no_action"><?php esc_html_e( 'Bulk actions', 'hbook-admin' ); ?></option>
				<option value="confirm"><?php esc_html_e( 'Confirm', 'hbook-admin' ); ?></option>
				<option value="cancel"><?php esc_html_e( 'Cancel', 'hbook-admin' ); ?></option>
				<option value="delete"><?php esc_html_e( 'Delete', 'hbook-admin' ); ?></option>
			</select>
			<input class="button" type="button" value="<?php esc_attr_e( 'Apply', 'hbook-admin' ); ?>" data-bind="click: do_bulk_action" />
			<span class="spinner"></span>
			<span class="hb-resa-bulk-action-msg"></span>
		</p>

		<?php $this->display_resa_pagination(); ?>

		<!-- /ko -->

	</div>

	<hr/>

	<?php
		}
	}

	private function display_resa_thead_tfoot() {
	?>

		<tr class="hb-resa-table-head-foot">
			<td class="hb-resa-id-column">
				<div><?php esc_html_e( 'Id', 'hbook-admin' ); ?></div>
				<div><input type="checkbox" data-bind="checked: select_unselect_all" /></div>
			</td>
			<td class="hb-resa-status-column">
				<?php
				if ( get_option( 'hb_resa_alphanum' ) == 'yes' ) {
					esc_html_e( 'Number', 'hbook-admin' );
					echo( ' / ' );
				}
				esc_html_e( 'Status', 'hbook-admin' );
				?>
			</td>
			<td class="hb-resa-check-in-out-column"><?php esc_html_e( 'Check-in / Check-out', 'hbook-admin' ); ?></td>
			<td class="hb-resa-accom-column">
				<?php esc_html_e( 'Accom. type', 'hbook-admin' ); ?>
				<small><?php esc_html_e( '(number)', 'hbook-admin' ); ?></small>
			</td>
			<td class="hb-resa-info-column"><?php esc_html_e( 'Information', 'hbook-admin' ); ?></td>
			<?php if ( $this->site_has_extras ) { ?>
			<td class="hb-resa-extras-column"><?php esc_html_e( 'Extras', 'hbook-admin' ); ?></td>
			<?php } ?>
			<td class="hb-resa-comment-column"><?php esc_html_e( 'Comment', 'hbook-admin' ); ?></td>
			<?php if ( $this->user_can_view( 'read_resa_customer' ) ) : ?>
			<td class="hb-resa-customer-column"><?php esc_html_e( 'Customer', 'hbook-admin' ); ?></td>
			<?php endif; ?>
			<?php if ( $this->user_can_view( 'read_resa_price' ) ) : ?>
			<td class="hb-resa-price-column"><?php esc_html_e( 'Price / Payment', 'hbook-admin' ); ?></td>
			<?php endif; ?>
			<td class="hb-resa-received-column"><?php esc_html_e( 'Received on / Emails sent', 'hbook-admin' ); ?></td>
			<td class="hb-resa-actions-column"></td>
		</tr>

	<?php
	}

	private function display_resa_tr() {
	?>

		<tr data-bind="attr: { 'data-resa-num': id, class: anim_class() + ' ' + parent_child_class }">

			<td class="hb-resa-id-column">
				<!-- ko if: is_parent -->
				<div class="dashicons dashicons-admin-multisite" title="<?php esc_attr_e( 'Multiple reservation', 'hbook-admin' ); ?>"></div>
				<!-- /ko -->
				<!-- ko if: is_child -->
				<span class="hb-resa-parent-id" title="<?php esc_attr_e( 'Multiple reservation id', 'hbook-admin' ); ?>">
					#<span data-bind="text: parent_id"></span>
				</span>
				<br/>
				<!-- /ko -->
				<span title="<?php esc_attr_e( 'Reservation id', 'hbook-admin' ); ?>">
					<!-- ko if: is_parent -->#<!-- /ko --><span data-bind="text: id"></span>
				</span>
				<div><input type="checkbox" data-bind="checked: is_selected, disable: is_disabled" /></div>
			</td>

			<td>
				<?php if ( get_option( 'hb_resa_alphanum' ) == 'yes' ) { ?>
				<div class="hb-resa-alphanum" data-bind="text: alphanum_id"></div>
				<?php } ?>
				<div data-bind="html: status_markup"></div>
			</td>

			<td>
				<!-- ko if: ( check_in() != '' ) && ( check_out() != '' ) -->
				<span data-bind="text: check_in"></span><br/>
				<span data-bind="text: check_out"></span><br/>
				<span data-bind="text: nb_nights"></span>
				<span data-bind="visible: nb_nights() == 1"><?php esc_html_e( 'night', 'hbook-admin' ); ?></span>
				<span data-bind="visible: nb_nights() != 1"><?php esc_html_e( 'nights', 'hbook-admin' ); ?></span>
				<!-- /ko -->
				<!-- ko if: ! is_parent && ( status() != 'processing' ) -->
				<?php if ( $this->user_can_edit() ) : ?>
				<div>
					<a data-bind="visible: ! editing_dates() && ! saving_dates(), click: $root.edit_dates" href="#"><?php esc_html_e( 'Edit', 'hbook-admin' ); ?></a>
					<div data-bind="visible: editing_dates()">
						<?php esc_html_e( 'Check-in:', 'hbook-admin' ); ?>
						<br/>
						<input class="hb-input-edit-resa hb-input-edit-resa-dates hb-input-edit-resa-check-in" data-bind="value: check_in_tmp" type="text" />
						<?php esc_html_e( 'Check-out:', 'hbook-admin' ); ?>
						<br/>
						<input class="hb-input-edit-resa hb-input-edit-resa-dates hb-input-edit-resa-check-out" data-bind="value: check_out_tmp" type="text" />
						<span data-bind="text: nb_nights_tmp"></span>
						<span data-bind="visible: nb_nights_tmp() == 1"><?php esc_html_e( 'night', 'hbook-admin' ); ?><br/></span>
						<span data-bind="visible: nb_nights_tmp() > 1"><?php esc_html_e( 'nights', 'hbook-admin' ); ?><br/></span>
						<a data-bind="click: $root.save_dates, visible: ! saving_dates()" href="#" class="button-primary"><?php esc_html_e( 'Save', 'hbook-admin' ); ?></a>
						<input type="button" disabled data-bind="visible: saving_dates()" href="#" class="button-primary" value="<?php esc_attr_e( 'Saving', 'hbook-admin' ); ?>" />
						<a data-bind="click: $root.cancel_edit_dates" href="#" class="button"><?php esc_html_e( 'Cancel', 'hbook-admin' ); ?></a>
					</div>
				</div>
				<?php endif; ?>
				<!-- /ko -->
			</td>

			<td>
				<!-- ko if: ! is_parent -->
				<div data-bind="visible: editing_accom()"><b><?php esc_html_e( 'Current accom.:', 'hbook-admin' ); ?></b></div>
				<div data-bind="html: accom"></div>
				<!-- /ko -->
				<!-- ko if: ! is_parent && ( status() != 'processing' ) -->
				<?php if ( $this->user_can_edit() ) : ?>
				<a data-bind="visible: accom_num() != 0 && ! editing_accom() && ! fetching_accom() && ! editing_accom_no_accom() && ! saving_accom(), click: $root.edit_accom" href="#"><?php esc_html_e( 'Edit', 'hbook-admin' ); ?></a>
				<?php endif; ?>
				<div data-bind="visible: saving_accom()"><?php esc_html_e( 'Updating...', 'hbook-admin' ); ?></div>
				<div data-bind="visible: fetching_accom()"><?php esc_html_e( 'Fetching available accom...', 'hbook-admin' ); ?></div>
				<div data-bind="visible: editing_accom()">
					<br/>
					<div><b><?php esc_html_e( 'Select new accom.:', 'hbook-admin' ); ?></b></div>
					<div class="hb-accom-editor" data-bind="html: accom_editor"></div>
					<a data-bind="click: $root.save_accom" href="#" class="button-primary"><?php esc_html_e( 'Save', 'hbook-admin' ); ?></a>
					<a data-bind="click: $root.cancel_edit_accom" href="#" class="button"><?php esc_html_e( 'Cancel', 'hbook-admin' ); ?></a>
				</div>
				<div data-bind="visible: editing_accom_no_accom()">
					<?php esc_html_e( 'No other accommodation available!', 'hbook-admin' ); ?>
				</div>
				<!-- /ko -->
			</td>

			<td>
				<!-- ko if: status() != 'processing' -->
				<div data-bind="visible: ! editing_resa_info()">
					<div data-bind="html: resa_info_html"></div>
					<?php if ( $this->user_can_edit() ) : ?>
					<div data-bind="visible: hb_multi_lang_site == 'yes' || ! is_parent || ( is_parent && additional_info_editing_markup() )">
						<a data-bind="click: $root.edit_resa_info" href="#"><?php esc_html_e( 'Edit', 'hbook-admin' ); ?></a>
					</div>
					<?php endif; ?>
					<div data-bind="html: non_editable_info"></div>
				</div>
				<div data-bind="visible: editing_resa_info()">
					<!-- ko if: ! is_parent -->
					<?php esc_html_e( 'Adults:', 'hbook-admin' ); ?>
					<br/>
					<input class="hb-input-edit-resa" data-bind="value: adults_tmp" type="text" />
					<?php esc_html_e( 'Children:', 'hbook-admin' ); ?>
					<br/>
					<input class="hb-input-edit-resa" data-bind="value: children_tmp" type="text" />
					<!-- /ko -->
					<!-- ko if: ! is_child -->
					<?php if ( $this->is_site_multi_lang == 'yes' ) : ?>
						<?php esc_html_e( 'Reservation language:', 'hbook-admin' ); ?>
						<br/>
						<select	class="hb-select-edit-resa" data-bind=" value: lang_tmp">
						<?php foreach ( $this->site_langs as $lang_value => $lang_name ) : ?>
							<option value="<?php echo( esc_attr( $lang_value ) ); ?>"><?php echo( esc_html( $lang_name ) ); ?></option>
						<?php endforeach; ?>
						</select>
					<?php endif; ?>
					<div data-bind="html: additional_info_editing_markup"></div>
					<!-- /ko -->
					<a data-bind="click: $root.save_resa_info, visible: ! saving_resa_info()" href="#" class="button-primary"><?php esc_html_e( 'Save', 'hbook-admin' ); ?></a>
					<input type="button" disabled data-bind="visible: saving_resa_info()" href="#" class="button-primary" value="<?php esc_attr_e( 'Saving', 'hbook-admin' ); ?>" />
					<a data-bind="click: $root.cancel_edit_resa_info" href="#" class="button"><?php esc_html_e( 'Cancel', 'hbook-admin' ); ?></a>
				</div>
				<!-- /ko -->
			</td>

			<?php if ( $this->site_has_extras ) { ?>
			<td>
				<div data-bind="html: options_info, visible: ! editing_options()"></div>
				<!-- ko if: status() != 'processing' -->
				<?php if ( $this->user_can_edit() ) : ?>
					<div>
						<a data-bind="click: $root.edit_options, visible: ! editing_options()" href="#">
							<span data-bind="visible: options_info() == ''">
							<?php esc_html_e( 'Add', 'hbook-admin' ); ?>
							</span>
							<span data-bind="visible: options_info() != ''">
							<?php esc_html_e( 'Edit', 'hbook-admin' ); ?>
							</span>
						</a>
					</div>
				<?php endif; ?>
				<div data-bind="visible: editing_options()">
					<div data-bind="html: options_editor, attr: { 'class': options_editor_class, 'data-resa-id': id, 'data-accom-id': accom_id }"></div>
					<a data-bind="click: $root.save_options, visible: ! saving_options()" href="#" class="button-primary"><?php esc_html_e( 'Save', 'hbook-admin' ); ?></a>
					<input type="button" disabled data-bind="visible: saving_options()" href="#" class="button-primary" value="<?php esc_attr_e( 'Saving', 'hbook-admin' ); ?>" />
					<a data-bind="click: $root.cancel_edit_options" href="#" class="button"><?php esc_html_e( 'Cancel', 'hbook-admin' ); ?></a>
				</div>
				<!-- /ko -->
			</td>
			<?php } ?>

			<td>
				<!-- ko if: status() != 'processing' -->
				<div data-bind="visible: origin_url != ''">
					<a data-bind="attr: { href: origin_url }" target="_blank" class="hb-origin-url-link"><?php esc_html_e( 'View reservation', 'hbook-admin' ); ?></a>
				</div>
				<div data-bind="visible: ! editing_comment()">
					<div data-bind="html: admin_comment_html()"></div>
					<?php if ( $this->user_can_edit() ) : ?>
					<div><a data-bind="visible: admin_comment() == '', click: $root.edit_comment" href="#"><?php esc_html_e( 'Add a comment', 'hbook-admin' ); ?></a></div>
					<div><a data-bind="visible: admin_comment() != '', click: $root.edit_comment" href="#"><?php esc_html_e( 'Edit', 'hbook-admin' ); ?></a></div>
					<?php endif; ?>
				</div>
				<div data-bind="visible: editing_comment()">
					<textarea data-bind="value: admin_comment_tmp" rows="6" class="widefat"></textarea><br/>
					<a data-bind="click: $root.save_comment, visible: ! saving_comment()" href="#" class="button-primary"><?php esc_html_e( 'Save', 'hbook-admin' ); ?></a>
					<input type="button" disabled data-bind="visible: saving_comment()" href="#" class="button-primary" value="<?php esc_attr_e( 'Saving', 'hbook-admin' ); ?>" />
					<a data-bind="click: $root.cancel_edit_comment" href="#" class="button"><?php esc_html_e( 'Cancel', 'hbook-admin' ); ?></a>
				</div>
				<!-- /ko -->
			</td>
			<?php if ( $this->user_can_view( 'read_resa_customer' ) ) { ?>
			<td class="hb-resa-customer-column">
				<!-- ko if: status() != 'processing' && customer_id() != 0 && ! is_child -->
				<div data-bind="visible: ! editing_customer(), html: customer_info_markup"></div>
				<?php if ( $this->user_can_edit() ) : ?>
				<a data-bind="visible: ! editing_customer(), click: $root.edit_customer" href="#"><?php esc_html_e( 'Edit', 'hbook-admin' ); ?></a>
				<?php endif; ?>
				<?php if ( ! $this->is_resa_customer ) { ?>
				<!-- ko if: customer_has_multiple_resa() -->
				<a data-bind="text: customer_nb_resa, attr: { href: customer_multiple_resa_link() }" class="hb-resa-customer-resa-link"></a>
				<!-- /ko -->
				<?php } ?>
				<div data-bind="visible: editing_customer()">
					<div class="hb-customer-edit-wrapper">
						<h4><?php esc_html_e( 'Customer details', 'hbook-admin' ) ?></h4>
						<a data-bind="click: $root.save_customer, visible: ! saving_customer()" href="#" class="button-primary"><?php esc_html_e( 'Save', 'hbook-admin' ); ?></a>
						<input type="button" disabled data-bind="visible: saving_customer()" href="#" class="button-primary" value="<?php esc_attr_e( 'Saving', 'hbook-admin' ); ?>" />
						<a data-bind="click: $root.cancel_edit_customer" href="#" class="button"><?php esc_html_e( 'Cancel', 'hbook-admin' ); ?></a>
						<hr/>
						<div data-bind="html: customer_info_editing_markup"></div>
						<hr/>
						<a data-bind="click: $root.save_customer, visible: ! saving_customer()" href="#" class="button-primary"><?php esc_html_e( 'Save', 'hbook-admin' ); ?></a>
						<input type="button" disabled data-bind="visible: saving_customer()" href="#" class="button-primary" value="<?php esc_attr_e( 'Saving', 'hbook-admin' ); ?>" />
						<a data-bind="click: $root.cancel_edit_customer" href="#" class="button"><?php esc_html_e( 'Cancel', 'hbook-admin' ); ?></a>
					</div>
				</div>
				<!-- /ko -->
				<!-- ko if: status() != 'processing' && customer_id() == 0 && ! is_child -->
				<a data-bind="click: $root.create_customer, visible: ! creating_customer() && ! selecting_customer()" href="#"><?php esc_html_e( 'Create customer', 'hbook-admin' ); ?></a><br/>
				<a data-bind="click: $root.select_customer, visible: ! creating_customer() && ! selecting_customer()" href="#"><?php esc_html_e( 'Select existing customer', 'hbook-admin' ); ?></a>
				<div data-bind="visible: creating_customer()"><?php esc_html_e( 'Creating customer...', 'hbook-admin' ); ?></div>
				<div data-bind="visible: selecting_customer()">
					<div class="hb-resa-filter-customer">
						<select
							id="hb-select-customer-id-list"
							class="hb-customer-id-list"
							multiple size="6"
							data-bind="value: select_customer_id, options: $root.resa_customers_list, optionsValue: 'id', optionsText: 'id_name'">
						</select><br/>
						<input type="text" data-bind="value: $root.resa_customers_list_filter, valueUpdate: 'afterkeydown'" placeholder="<?php esc_attr_e( 'Search a customer...', 'hbook-admin' ); ?>" /><br/>
					</div>
					<a data-bind="click: $root.save_selected_customer, visible: ! saving_selected_customer()" href="#" class="button-primary"><?php esc_html_e( 'Save', 'hbook-admin' ); ?></a>
					<input type="button" disabled data-bind="visible: saving_selected_customer()" href="#" class="button-primary" value="<?php esc_attr_e( 'Saving', 'hbook-admin' ); ?>" />
					<a data-bind="click: $root.cancel_select_customer" href="#" class="button"><?php esc_html_e( 'Cancel', 'hbook-admin' ); ?></a>
				</div>
				<!-- /ko -->
			</td>
			<?php } ?>

			<?php if ( $this->user_can_view( 'read_resa_price' ) ) { ?>
			<td>
				<div data-bind="html: price_markup"></div>

				<!-- ko if: status() != 'processing' -->

				<div data-bind="visible: ! is_child, html: payment_status"></div>

				<?php if ( $this->user_can_edit() ) : ?>
				<!-- ko if: ! is_parent -->
				<div data-bind="visible: ! editing_price()">
					<a data-bind="click: $root.edit_price, visible: ! editing_price()" href="#">
						<?php esc_html_e( 'Edit price', 'hbook-admin' ); ?>
					</a>
				</div>
				<?php endif; ?>
				<div data-bind="visible: editing_price()">
					<?php esc_html_e( 'Price:', 'hbook-admin' ); ?>
					<br/>
					<input class="hb-input-edit-resa" data-bind="value: price_tmp" type="text" />
					<a data-bind="click: $root.save_price, visible: ! saving_price()" href="#" class="button-primary"><?php esc_html_e( 'Save', 'hbook-admin' ); ?></a>
					<input type="button" disabled data-bind="visible: saving_price()" href="#" class="button-primary" value="<?php esc_attr_e( 'Saving', 'hbook-admin' ); ?>" />
					<a data-bind="click: $root.cancel_edit_price" href="#" class="button"><?php esc_html_e( 'Cancel', 'hbook-admin' ); ?></a>
				</div>
				<!-- /ko -->

				<!-- ko if: paid() != -1 && ! is_child -->
				<?php if ( $this->user_can_edit() ) : ?>
				<a data-bind="click: $root.mark_paid, visible: mark_paid_visible()" href="#">
					<?php esc_html_e( 'Mark as paid', 'hbook-admin' ); ?>
				</a>
				<b data-bind="visible: marking_paid()"><?php esc_html_e( 'Marking as paid...', 'hbook-admin' ); ?></b>
				<?php endif; ?>

				<div class="hb-to-be-paid-details">
					<div data-bind="visible: ! editing_paid()">
						<div data-bind="visible: paid() != 0, html: price_details"></div>
						<?php if ( $this->user_can_edit() ) : ?>
						<a data-bind="click: $root.edit_paid" href="#"><?php esc_html_e( 'Edit payment', 'hbook-admin' ); ?></a>
						<?php endif; ?>
					</div>
					<div data-bind="visible: editing_paid()">
						<?php esc_html_e( 'Paid:', 'hbook-admin' ); ?>
						<br/>
						<input class="hb-input-edit-resa" data-bind="value: paid_tmp" type="text" />
						<a data-bind="click: $root.save_paid, visible: ! saving_paid()" href="#" class="button-primary"><?php esc_html_e( 'Save', 'hbook-admin' ); ?></a>
						<input type="button" disabled data-bind="visible: saving_paid()" href="#" class="button-primary" value="<?php esc_attr_e( 'Saving', 'hbook-admin' ); ?>" />
						<a data-bind="click: $root.cancel_edit_paid" href="#" class="button"><?php esc_html_e( 'Cancel', 'hbook-admin' ); ?></a>
					</div>
				</div>

				<?php if ( $this->user_can_edit() ) : ?>
				<a data-bind="visible: charge_action_visible, click: $root.edit_charge" href="#"><?php esc_html_e( 'Charge', 'hbook-admin' ); ?></a>
				<span data-bind="visible: charge_action_visible() && refund_action_visible()"> - </span>
				<a data-bind="visible: refund_action_visible, click: $root.edit_refund" href="#"><?php esc_html_e( 'Refund', 'hbook-admin' ); ?></a>
				<div class="hb-charge-details">
					<div data-bind="visible: editing_charge()">
						<?php esc_html_e( 'Charge amount:', 'hbook-admin' ); ?>
						<br/>
						<input class="hb-input-edit-resa" data-bind="value: charge_amount" type="text" />
						<a data-bind="click: $root.charge, visible: ! charging()" href="#" class="button-primary"><?php esc_html_e( 'Charge', 'hbook-admin' ); ?></a>
						<input type="button" disabled data-bind="visible: charging()" href="#" class="button-primary" value="<?php esc_attr_e( 'Charging', 'hbook-admin' ); ?>" />
						<a data-bind="click: $root.cancel_edit_charge" href="#" class="button"><?php esc_html_e( 'Cancel', 'hbook-admin' ); ?></a>
					</div>
				</div>
				<div class="hb-refund-details">
					<div data-bind="visible: editing_refund()">
						<?php esc_html_e( 'Refund amount:', 'hbook-admin' ); ?>
						<br/>
						<input class="hb-input-edit-resa" data-bind="value: refund_amount" type="text" />
						<a data-bind="click: $root.refund, visible: ! refunding()" href="#" class="button-primary"><?php esc_html_e( 'Refund', 'hbook-admin' ); ?></a>
						<input type="button" disabled data-bind="visible: refunding()" href="#" class="button-primary" value="<?php esc_attr_e( 'Refunding', 'hbook-admin' ); ?>" />
						<a data-bind="click: $root.cancel_edit_refund" href="#" class="button"><?php esc_html_e( 'Cancel', 'hbook-admin' ); ?></a>
					</div>
				</div>
				<?php endif; ?>
				<!-- /ko -->

				<!-- ko if: ! is_parent -->
				<div data-bind="html: discount_markup" class="hb-discount-wrapper"></div>
				<?php if ( $this->user_can_edit() ) : ?>
				<a data-bind="visible: ! editing_discount() && ( accom_price != -1 ), click: $root.edit_discount" href="#"><?php esc_html_e( 'Edit discount', 'hbook-admin' ); ?></a>
				<?php endif; ?>
				<div data-bind="visible: editing_discount" class="hb-discount-edit-wrapper">
					<p>
						<b><?php esc_html_e( 'Accom. discount:', 'hbook-admin' ); ?></b><br/>
						<input class="hb-input-edit-resa" data-bind="value: accom_discount_amount_tmp" type="text" />
						<?php esc_html_e( 'Amount type:', 'hbook-admin' ); ?><br/>
						<input
							data-bind="checked: accom_discount_amount_type_tmp, attr: { id: 'hb-accom-discount-amount-type-fixed-' + id }"
							type="radio"
							value="fixed"
						/>
						<label data-bind="attr: { for: 'hb-accom-discount-amount-type-fixed-' + id }">
							<?php esc_html_e( 'Fixed', 'hbook-admin' ); ?>
							(<?php echo( esc_html( $this->currency_symbol ) ); ?>)
						</label><br>
						<input
							data-bind="checked: accom_discount_amount_type_tmp, attr: { id: 'hb-accom-discount-amount-type-percent-' + id }"
							type="radio"
							value="percent"
						/>
						<label data-bind="attr: { for: 'hb-accom-discount-amount-type-percent-' + id }">
							<?php esc_html_e( 'Percent', 'hbook-admin' ); ?>
						</label>
					</p>
					<p>
						<b><?php esc_html_e( 'Global discount:', 'hbook-admin' ); ?></b><br/>
						<input class="hb-input-edit-resa" data-bind="value: global_discount_amount_tmp" type="text" />
						<?php esc_html_e( 'Amount type:', 'hbook-admin' ); ?><br/>
						<input
							data-bind="checked: global_discount_amount_type_tmp, attr: { id: 'hb-global-discount-amount-type-fixed-' + id }"
							type="radio"
							value="fixed"
						/>
						<label data-bind="attr: { for: 'hb-global-discount-amount-type-fixed-' + id }">
							<?php esc_html_e( 'Fixed', 'hbook-admin' ); ?>
							(<?php echo( esc_html( $this->currency_symbol ) ); ?>)
						</label><br>
						<input
							data-bind="checked: global_discount_amount_type_tmp, attr: { id: 'hb-global-discount-amount-type-percent-' + id }"
							type="radio"
							value="percent"
						/>
						<label data-bind="attr: { for: 'hb-global-discount-amount-type-percent-' + id }">
							<?php esc_html_e( 'Percent', 'hbook-admin' ); ?>
						</label>
					</p>
					<a data-bind="click: $root.save_discount, visible: ! saving_discount()" href="#" class="button-primary"><?php esc_html_e( 'Save', 'hbook-admin' ); ?></a>
					<input type="button" disabled data-bind="visible: saving_discount()" href="#" class="button-primary" value="<?php esc_attr_e( 'Saving', 'hbook-admin' ); ?>" />
					<a data-bind="click: $root.cancel_edit_discount" href="#" class="button"><?php esc_html_e( 'Cancel', 'hbook-admin' ); ?></a>
				</div>
				<!-- /ko -->
				<!-- /ko -->

			</td>
			<?php } ?>

			<td>
				<div data-bind="text: received_on"></div>
				<div data-bind="visible: emails_sent()"><a data-bind="text: emails_sent, click: $root.open_email_logs" href="#"></a></div>
				<div data-bind="visible: opening_email_logs()" class="hb-email-logs-visible">
					<div class="hb-email-logs-wrapper">
						<a href="#" class="dashicons dashicons-dismiss" data-bind="click: $root.open_email_logs"></a>
						<table class="hb-email-logs">
							<thead>
								<tr>
									<th><?php esc_html_e( 'Template', 'hbook-admin' ); ?></th>
									<th><?php esc_html_e( 'Sent on', 'hbook-admin' ); ?></th>
									<th><?php esc_html_e( 'Trigger', 'hbook-admin' ); ?></th>
								</tr>
							</thead>
							<tbody data-bind="foreach { data: email_logs }">
								<tr>
									<td class="hb-email-logs-td" data-bind="text: template_name"></td>
									<td class="hb-email-logs-td" data-bind="text: sent_time"></td>
									<td class="hb-email-logs-td" data-bind="text: trigger"></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</td>

			<td class="hb-resa-actions-column">
				<?php if ( $this->user_can_edit() ) : ?>
				<!-- ko if: status() != 'processing' -->
				<a href="#" title="<?php esc_attr_e( 'Confirm', 'hbook-admin' ); ?>" class="dashicons dashicons-yes" data-bind="click: $root.confirm_resa, visible: confirm_visible()"></a>
				<a href="#" title="<?php esc_attr_e( 'Cancel', 'hbook-admin' ); ?>" class="dashicons dashicons-no" data-bind="click: $root.cancel_resa, visible: ! action_processing() && status() != 'cancelled'"></a>
				<a href="#" title="<?php esc_attr_e( 'Email', 'hbook-admin' ); ?>" class="dashicons dashicons-email-alt" data-bind="click: $root.email_resa, visible: ! action_processing() && customer_id() != 0 && ! is_child"></a>
				<!-- ko if: documents_resa_nb() == 1 -->
				<a
					class="dashicons dashicons-media-default hb-open-document"
					data-bind="attr: { href: document_resa_link, title: document_resa_title }, visible: ! action_processing()"
					target="_blank"
				></a>
				<!-- /ko -->
				<!-- ko if: documents_resa_nb() > 1 -->
				<a
					href="#"
					title="<?php esc_attr_e( 'Open documents' ); ?>"
					class="dashicons dashicons-admin-page"
					data-bind="click: $root.open_multiple_documents_resa, visible: ! action_processing()"
				></a>
				<!-- /ko -->
				<a href="#" title="<?php esc_attr_e( 'Delete', 'hbook-admin' ); ?>" class="dashicons dashicons-trash" data-bind="click: $root.delete_resa, visible: ! action_processing()"></a>
				<div data-bind="visible: opening_documents" class="hb-open-documents-wrapper">
					<div data-bind="html: documents_resa_links"></div>
					<a href="#" class="dashicons dashicons-dismiss" data-bind="click: $root.open_multiple_documents_resa"></a>
				</div>
				<div data-bind="visible: action_processing()" class="hb-resa-action-spinner">
					<span class="spinner"></span>
				</div>
				<span data-bind="visible: email_sent"><?php esc_html_e( 'Email sent.', 'hbook-admin' ); ?></span>
				<!-- /ko -->
				<div data-bind="visible: preparing_email()" class="hb-sending-email-wrapper">
					<h4><?php esc_html_e( 'Send an email', 'hbook-admin' ); ?></h4>
					<a data-bind="click: $root.send_email_customer" href="#" class="button-primary"><?php esc_html_e( 'Send', 'hbook-admin' ); ?></a>
					<a data-bind="click: $root.cancel_email_resa" href="#" class="button"><?php esc_html_e( 'Cancel', 'hbook-admin' ); ?></a>
					<p>
						<select data-bind="value: email_customer_template, options: email_templates_options, optionsText: 'name', optionsValue: 'id'">
						</select>
					</p>
					<p class="hb-sending-email-label"><?php esc_html_e( 'To', 'hbook-admin' ); ?></p>
					<p>
						<input type="text" data-bind="value: email_customer_to_address" />
						<small><?php esc_html_e( 'If this field is blank the email will be sent to the customer.', 'hbook-admin' ); ?></small>
					</p>
					<p class="hb-sending-email-label"><?php esc_html_e( 'Subject', 'hbook-admin' ); ?></p>
					<p><input type="text" data-bind="value: email_customer_subject" /></p>
					<p class="hb-sending-email-label"><?php esc_html_e( 'Message', 'hbook-admin' ); ?></p>
					<p><textarea rows="5" data-bind="value: email_customer_message"></textarea></p>
					<p class="hb-add-attachment">
						<input data-bind="value: email_customer_attachment_ids" type="hidden" />
						<a href="#" class="hb-add-attachment-link"><?php esc_html_e( 'Add attachments', 'hbook-admin' ); ?></a><br/>
						<span data-bind="html: email_customer_attachment_titles"></span>
						<a href="#" data-bind="visible: email_customer_attachment_ids() != '', click: $root.remove_email_attachments">
							<small><?php esc_html_e( 'Remove all attachments', 'hbook-admin' ); ?></small>
						</a>
					</p>
					<p data-bind="visible: email_customer_attachment_ids() != ''">
						<input data-bind="checked: email_customer_delete_attachments_after" type="checkbox" />
						<span data-bind="click: $root.delete_email_attachments_after_label" class="hb-delete-attachment-link">
							<?php esc_html_e( 'Delete added attachment files from the Media Library after email has been sent', 'hbook-admin' ); ?>
						</span>
					</p>
					<a data-bind="click: $root.send_email_customer" href="#" class="button-primary"><?php esc_html_e( 'Send', 'hbook-admin' ); ?></a>
					<a data-bind="click: $root.cancel_email_resa" href="#" class="button"><?php esc_html_e( 'Cancel', 'hbook-admin' ); ?></a>
				</div>
				<?php endif ?>
			</td>

		</tr>

	<?php
	}

	private function display_resa_pagination() {
	?>

	<!-- ko if: resa_total_pages() > 1 -->
	<p>
		<a href="#" class="button" data-bind="click: resa_first_page">&laquo;</a>
		<a href="#" class="button" data-bind="click: resa_previous_page">&lsaquo;</a>
		&nbsp;&nbsp;
		<?php
		printf(
			esc_html__( 'Viewing page %s of %s', 'hbook-admin' ),
			'<span data-bind="text: resa_current_page_number"></span>',
			'<span data-bind="text: resa_total_pages"></span>'
		);
		?>
		&nbsp;&nbsp;
		<a href="#" class="button" data-bind="click: resa_next_page">&rsaquo;</a>
		<a href="#" class="button" data-bind="click: resa_last_page">&raquo;</a>
	</p>
	<!-- /ko -->

	<?php
	}

	public function display_resa_archives_links( $past_months_with_checkout, $is_resa_archive, $month_full_name ) {
		if ( $past_months_with_checkout ) {
		?>

		<div id="hb-archived-resa-section" class="hb-resa-section<?php if ( ! $is_resa_archive ) { ?> hb-resa-section-has-toggle<?php } ?>">

			<h3 id="hb-archived-resa-toggle" class="hb-resa-section-toggle">
				<?php esc_html_e( 'Archived reservations', 'hbook-admin' ); ?>
				<?php if ( ! $is_resa_archive ) { ?>
				<span class="dashicons dashicons-arrow-down"></span>
				<span class="dashicons dashicons-arrow-up"></span>
				<?php } ?>
			</h3>

			<div id="hb-archived-resa">
				<p>
				<?php
				foreach ( $past_months_with_checkout as $year_month ) {
					$year = substr( $year_month, 0, 4 );
					$month = substr( $year_month, 5, 2 );
					?>

					<a
						class="hb-resa-follow-link"
						href="<?php echo( esc_url( admin_url( 'admin.php?page=hb_reservations&year_month=' . $year_month ) ) ); ?>"
					><?php echo( esc_html( $month_full_name[ $month - 1 ] . ' ' . $year ) ); ?></a><br/>

				<?php } ?>
				</p>
			</div>

		</div>

		<hr/>

		<?php
		}
	}

	private function user_can_edit() {
		if ( current_user_can( 'manage_resa' ) || current_user_can( 'manage_hbook' ) ) {
			return true;
		} else {
			return false;
		}
	}

	private function user_can_view( $cap ) {
		if ( current_user_can( 'manage_resa' ) || current_user_can( $cap ) ) {
			return true;
		} else {
			return false;
		}
	}

}