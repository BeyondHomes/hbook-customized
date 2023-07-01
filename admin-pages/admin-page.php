<?php
class HbAdminPage {

	private $page_id;
	private $common_text;
	protected $data;
	protected $hbdb;
	protected $utils;
	protected $options_utils;

	public function __construct( $page_id, $hbdb, $utils, $options_utils ) {
		$this->page_id = $page_id;
		$this->hbdb = $hbdb;
		$this->utils = $utils;
		$this->options_utils = $options_utils;
		$this->common_text = array(
			'all' => esc_html__( 'All', 'hbook-admin' ),
			'save' => esc_html__( 'Save', 'hbook-admin' ),
			'saving' => esc_html__( 'Saving...', 'hbook-admin' ),
			'confirm_delete' => esc_html__( 'Delete \'%setting_name\'?', 'hbook-admin' ),
			'confirm_delete_default' => esc_html__( 'Confirm deletion?', 'hbook-admin' ),
			'unsaved_warning' => esc_html__( 'It seems some changes have not been saved.', 'hbook-admin' ),
			'no_accom_selected' => esc_html__( 'No accommodation selected', 'hbook-admin' ),
		);
		$this->data['hb_text'] = array_merge( $this->common_text, $this->data['hb_text'] );
		$this->data['all_accom_ids'] = $this->hbdb->get_all_accom_ids();
		$ajax_timeout = intval( get_option( 'hb_admin_ajax_timeout' ) );
		if ( ! $ajax_timeout ) {
			$ajax_timeout = 20000;
		}
		$this->data['hb_ajax_settings'] = array( 'timeout' => $ajax_timeout );
		foreach ( $this->data as $key => $value ) {
			$this->utils->hb_script_var( 'hb-' . $this->page_id . '-script', $key, $value );
		}
		wp_nonce_field( 'hb_nonce_update_db', 'hb_nonce_update_db' );
	}

	protected function display_admin_action( $setting_type = '' ) {
	?>
		<?php if ( $setting_type == 'season' ) { ?>
		<a href="#" title="<?php esc_attr_e( 'Add season dates', 'hbook-admin' ); ?>" class="dashicons dashicons-plus" data-bind="click: $root.create_season_dates, visible: ! deleting() && ! adding_child()"></a>
		<?php }	?>
		<?php if ( $setting_type == 'option' ) { ?>
		<a href="#" title="<?php esc_attr_e( 'Add option choice', 'hbook-admin' ); ?>" class="dashicons dashicons-plus" data-bind="click: $root.create_option_choice, visible: option.choice_type() == 'multiple' && ! deleting() && ! adding_child()"></a>
		<?php }	?>
		<?php if ( ( $setting_type == 'season' ) || ( $setting_type == 'option' ) ) { ?>
		<span data-bind="visible: adding_child()" class="hb-ajaxing hb-adding-child">
			<span class="spinner"></span>
		</span>
		<?php }	?>
		<a href="#" title="<?php esc_attr_e( 'Edit', 'hbook-admin' ); ?>" class="dashicons dashicons-edit" data-bind="click: $root.edit_setting, visible: ! deleting()"></a>
		<?php if ( $setting_type == 'season_dates' ) { ?>
		<a href="#" title="<?php esc_attr_e( 'Delete', 'hbook-admin' ); ?>" class="dashicons dashicons-trash" data-bind="click: function( data, event ) { $root.remove( data, event, season ) }, visible: ! deleting()"></a>
		<?php } else if ( $setting_type == 'option_choice' ) { ?>
		<a href="#" title="<?php esc_attr_e( 'Delete', 'hbook-admin' ); ?>" class="dashicons dashicons-trash" data-bind="click: function( data, event ) { $root.remove( data, event, option ) }, visible: ! deleting()"></a>
		<?php } else { ?>
		<a href="#" title="<?php esc_attr_e( 'Delete', 'hbook-admin' ); ?>" class="dashicons dashicons-trash" data-bind="click: $root.remove, visible: ! deleting()"></a>
		<?php } ?>
		<span data-bind="visible: deleting" class="hb-ajaxing hb-deleting">
			<span class="spinner"></span>
			<span><?php esc_html_e( 'Deleting...', 'hbook-admin' ); ?></span>
		</span>
	<?php
	}

	protected function display_admin_on_edit_action( $setting_type = '' ) {
	?>
		<input type="button" class="button-primary" data-bind="click: $root.save_setting, disable: saving, value: save_text" />
		<a href="#" class="button" data-bind="click: $root.cancel_edit_setting, visible: ! brand_new"><?php esc_html_e( 'Cancel', 'hbook-admin' ); ?></a>
		<?php if ( $setting_type == 'season_dates' ) { ?>
		<a href="#" class="button" data-bind="click: function( data, event ) { $root.remove( data, event, season ) }, visible: brand_new"><?php esc_html_e( 'Cancel', 'hbook-admin' ); ?></a>
		<?php } else if ( $setting_type == 'option_choice' ) { ?>
		<a href="#" class="button" data-bind="click: function( data, event ) { $root.remove( data, event, option ) }, visible: brand_new"><?php esc_html_e( 'Cancel', 'hbook-admin' ); ?></a>
		<?php } else { ?>
		<a href="#" class="button" data-bind="click: $root.remove, visible: brand_new"><?php esc_html_e( 'Cancel', 'hbook-admin' ); ?></a>
		<?php } ?>
	<?php
	}

	protected function display_select_days( $id ) {
		$days = $this->utils->days_full_name();
		foreach( $days as $i => $day ) {
		?>
			<input
				id="<?php echo( esc_attr( 'hb-' . $id . '-' . $day ) ); ?>"
				data-bind="<?php echo( esc_attr( 'checked: ' . $id ) ); ?>"
				type="checkbox"
				value="<?php echo( esc_attr( $i ) ); ?>"
			/>
			<label for="<?php echo( esc_attr( 'hb-' . $id . '-' . $day ) ); ?>"><?php echo( esc_html( $day ) ); ?></label><br/>
		<?php
		}
		?>
		<a data-bind="<?php echo( esc_attr( 'click: select_all_' . $id ) ); ?>" href="#"><?php esc_html_e( 'Select all', 'hbook-admin' ); ?></a> -
		<a data-bind="<?php echo( esc_attr( 'click: unselect_all_' . $id ) ); ?>" href="#"><?php esc_html_e( 'Unselect all', 'hbook-admin' ); ?></a>
		<?php
	}

	protected function display_checkbox_list( $data, $data_type, $display_check_all_box = true, $display_select_all_link = false, $display_unselect_all = true ) {
		if ( $display_check_all_box ) {
		?>
		<input data-bind="<?php echo( esc_attr( 'checked: all_' . $data_type ) ); ?>" type="checkbox" id="<?php echo( esc_attr( 'hb-checkbox-' . $data_type . '-all' ) ); ?>" />
		<label for="<?php echo( esc_attr( 'hb-checkbox-' . $data_type . '-all' ) ); ?>"><?php esc_html_e( 'All', 'hbook-admin' ); ?></label><br/>
		<?php
		}
		foreach ( $data as $id => $name ) {
			$data_bind = 'checked: ' . $data_type;
			if ( $display_check_all_box ) {
				$data_bind .= ', disable: all_' . $data_type;
			}
			?>
			<input
				id="<?php echo( esc_attr( 'hb-checkbox-' . $data_type . '-' . $id ) ); ?>"
				data-bind="<?php echo( esc_attr( $data_bind ) ); ?>"
				type="checkbox"
				value="<?php echo( esc_attr( $id ) ); ?>"
			/>
			<label for="<?php echo( esc_attr( 'hb-checkbox-' . $data_type . '-' . $id ) ); ?>"><?php echo( esc_html( $name ) ); ?></label><br/>
		<?php
		}
		if ( $display_select_all_link ) {
		?>
		<a data-bind="<?php echo( esc_attr ( 'click: select_all_' . $data_type ) ); ?>" href="#"><?php esc_html_e( 'Select all', 'hbook-admin' ); ?></a> -
		<?php
		}
		if ( $display_unselect_all ) {
		?>
		<a data-bind="<?php echo( esc_attr ( 'click: unselect_all_' . $data_type ) ); ?>" href="#"><?php esc_html_e( 'Unselect all', 'hbook-admin' ); ?></a>
		<?php
		}
	}

	protected function display_edit_amount_fixed_percent() {
		?>
		<p class="amount-type"><?php esc_html_e( 'Amount type', 'hbook-admin' ); ?></p>
		<input data-bind="checked: amount_type" id="hb-amount-type-fixed" type="radio" value="fixed" /><label for="hb-amount-type-fixed"><?php esc_html_e( 'Fixed', 'hbook-admin' ); ?> (<?php echo( $this->utils->get_currency_symbol() ); ?>)</label><br/>
		<input data-bind="checked: amount_type" id="hb-amount-type-percent" type="radio" value="percent" /><label for="hb-amount-type-percent"><?php esc_html_e( 'Percentage', 'hbook-admin' ); ?></label>
		<?php
	}

	protected function display_edit_rate_amount_fixed_percent() {
		?>
		<input data-bind="value: amount" type="text" class="hb-rate-amount" /><br/>
		<?php
		$this->display_edit_amount_fixed_percent();
	}

	protected function display_form_builder() {
	?>
		<div class="hb-form-fields-container" data-bind="sortable: { data: fields, connectClass: 'hb-form-fields-container' }">

			<div class="hb-form-field" data-bind="css: { 'hb-standard-field': standard == 'yes' }, attr: { id: id }">
				<a class="hb-form-field-delete dashicons dashicons-no" href="#" data-bind="click: function( data, event ) { $root.remove_field( data, event ) }" title="<?php esc_attr_e( 'Remove field', 'hbook-admin' ); ?>"></a>
				<p data-bind="visible: type() != 'separator' && type() != 'column_break'" class="hb-form-field-name">
					<span
						data-bind="visible: ! editing_name() &&
						['details_form_title', 'first_name', 'last_name', 'email'].indexOf( id() ) == -1, text: name"
					></span>
					<span data-bind="visible: ['details_form_title', 'first_name', 'last_name', 'email'].indexOf( id() ) != -1,
					text: hb_text[ id() ]"></span>
					<input data-bind="visible: editing_name, value: name" type="text" class="hb-input-field-name" />
					<a data-bind="visible: ! editing_name(), click: $root.edit_field_name" class="dashicons dashicons-edit hb-form-field-edit-name" title="<?php esc_attr_e( 'Edit field name', 'hbook-admin' ); ?>" href="#"></a>
					<a data-bind="visible: editing_name, click: $root.stop_edit_field_name" class="button" href="#"><?php esc_html_e( 'OK', 'hbook-admin' ); ?></a>
				</p>
				<?php
				$field_types = array(
					'text' => esc_html__( 'Text', 'hbook-admin' ),
					'email' => esc_html__( 'Email', 'hbook-admin' ),
					'number' => esc_html__( 'Number', 'hbook-admin' ),
					'textarea' => esc_html__( 'Text area', 'hbook-admin' ),
					'select' => esc_html__( 'Select', 'hbook-admin' ),
					'radio' => esc_html__( 'Radio buttons', 'hbook-admin' ),
					'checkbox' => esc_html__( 'Check boxes', 'hbook-admin' ),
					'title' => esc_html__( 'Title', 'hbook-admin' ),
					'sub_title' => esc_html__( 'Sub-title', 'hbook-admin' ),
					'explanation' => esc_html__( 'Explanation', 'hbook-admin' ),
					'separator' => esc_html__( 'Separator', 'hbook-admin' ),
					'column_break' => esc_html__( 'Column break', 'hbook-admin' ),
				);
				?>
				<p data-bind="visible: type() != 'sub_title' && type() != 'explanation' && type() != 'separator' && type() != 'column_break'">
					<span class="hb-form-field-attribute"><?php esc_html_e( 'Displayed?', 'hbook-admin' ); ?></span>
					<input data-bind="checked: displayed, attr: { id: displayed_yes_input_id }" type="radio" value="yes" />
					<label data-bind="attr: { 'for': displayed_yes_input_id }"><?php esc_html_e( 'Yes', 'hbook-admin' ); ?></label>
					&nbsp;&nbsp;
					<input data-bind="checked: displayed, attr: { id: displayed_no_input_id }" type="radio" value="no" />
					<label data-bind="attr: { 'for': displayed_no_input_id }"><?php esc_html_e( 'No', 'hbook-admin' ); ?></label>
				</p>
				<div data-bind="slideVisible: displayed">
					<p data-bind="visible: type() != 'title' && type() != 'sub_title' && type() != 'explanation' && type() != 'separator' && type() != 'column_break'">
						<span class="hb-form-field-attribute"><?php esc_html_e( 'Admin only?', 'hbook-admin' ); ?></span>
						<input data-bind="checked: admin_only, attr: { id: admin_only_yes_input_id }" type="radio" value="yes" />
						<label data-bind="attr: { 'for': admin_only_yes_input_id }"><?php esc_html_e( 'Yes', 'hbook-admin' ); ?></label>
						&nbsp;&nbsp;
						<input data-bind="checked: admin_only, attr: { id: admin_only_no_input_id }" type="radio" value="no" />
						<label data-bind="attr: { 'for': admin_only_no_input_id }"><?php esc_html_e( 'No', 'hbook-admin' ); ?></label>
					</p>
					<div data-bind="slideHidden: admin_only">
						<p data-bind="visible: type() != 'title' && type() != 'sub_title' && type() != 'explanation' && type() != 'separator' && type() != 'column_break'">
							<span class="hb-form-field-attribute"><?php esc_html_e( 'Required?', 'hbook-admin' ); ?></span>
							<input data-bind="checked: required, attr: { id: required_yes_input_id }" type="radio" value="yes" />
							<label data-bind="attr: { 'for': required_yes_input_id }"><?php esc_html_e( 'Yes', 'hbook-admin' ); ?></label>
							&nbsp;&nbsp;
							<input data-bind="checked: required, attr: { id: required_no_input_id }" type="radio" value="no" />
							<label data-bind="attr: { 'for': required_no_input_id }"><?php esc_html_e( 'No', 'hbook-admin' ); ?></label>
						</p>
					</div>
					<div class="hb-form-additional-field-settings">
						<p>
							<span class="hb-form-field-attribute"><?php esc_html_e( 'Field type', 'hbook-admin' ); ?></span>
							<select class="hb-form-field-select" data-bind="value: type">
							<?php foreach ( $field_types as $ft_id => $ft_label ) { ?>
								<option value="<?php echo( esc_attr( $ft_id ) ); ?>"><?php echo( esc_html( $ft_label ) ); ?></option>
							<?php } ?>
							</select>
						</p>
						<div class="hb-form-field-choices" data-bind="visible: ( type() == 'checkbox' ) || ( type() == 'radio' ) || ( type() == 'select' )">
							<?php esc_html_e( 'Choices', 'hbook-admin' ); ?>
							<a data-bind="click: add_choice" class="dashicons dashicons-plus" title="<?php esc_attr_e( 'Add a choice', 'hbook-admin' ); ?>" href="#"></a>
							<ul class="hb-form-fields-choices-ul" data-bind="sortable: { data: choices, connectClass: 'hb-form-fields-choices-ul' }">
								<li>
									<span data-bind="visible: ! editing_choice(), text: name"></span>
									<input data-bind="visible: editing_choice, value: name" type="text" class="hb-input-choice-name" />
									<a data-bind="visible: ! editing_choice(), click: $parent.edit_choice_name" class="dashicons dashicons-edit hb-form-field-edit-choice" title="<?php esc_attr_e( 'Edit choice', 'hbook-admin' ); ?>" href="#"></a>
									<a data-bind="visible: editing_choice, click: $parent.stop_edit_choice_name" class="button" href="#"><?php esc_html_e( 'OK', 'hbook-admin' ); ?></a>
									<a data-bind="click: $parent.remove_choice" class="dashicons dashicons-no hb-form-field-remove-choice" title="<?php esc_attr_e( 'Remove choice', 'hbook-admin' ); ?>" href="#"></a>
								</li>
							</ul>
						</div>
					</div>
					<p data-bind="visible: type() != 'title' && type() != 'sub_title' &&
						type() != 'explanation' && type() != 'separator' && type() != 'column_break' &&
						['first_name', 'last_name', 'email'].indexOf( id() ) == -1"
					>
						<span class="hb-form-field-attribute"><?php esc_html_e( 'Data about?', 'hbook-admin' ); ?></span>
						<input data-bind="checked: data_about, attr: { id: data_about_customer_input_id }" type="radio" value="customer" />
						<label data-bind="attr: { 'for': data_about_customer_input_id }"><?php esc_html_e( 'Customer', 'hbook-admin' ); ?></label>
						&nbsp;&nbsp;
						<input data-bind="checked: data_about, attr: { id: data_about_booking_input_id }" type="radio" value="booking" />
						<label data-bind="attr: { 'for': data_about_booking_input_id }"><?php esc_html_e( 'Booking', 'hbook-admin' ); ?></label>
					</p>
					<p data-bind="visible: type() != 'title' && type() != 'sub_title' && type() != 'explanation' && type() != 'separator' && type() != 'column_break'">
						<span class="hb-form-field-attribute"><?php esc_html_e( 'Column width', 'hbook-admin' ); ?></span>
						<select class="hb-form-field-select" data-bind="value: column_width">
							<option value=""><?php esc_html_e( 'Full', 'hbook-admin' ); ?></option>
							<option value="half"><?php esc_html_e( 'One half', 'hbook-admin' ); ?></option>
							<option value="third"><?php esc_html_e( 'One third', 'hbook-admin' ); ?></option>
						</select>
					</p>
				</div>
			</div>

		</div>
	<?php
	}

	protected function display_right_menu() {
		$hbook_pages = $this->utils->get_hbook_pages();
		?>

		<a id="hb-admin-settings-link" href="<?php echo( esc_url( admin_url( 'admin.php?page=hb_menu' ) ) ); ?>"><?php esc_html_e( 'HBook settings', 'hbook-admin' ); ?> <span class="dashicons dashicons-arrow-down-alt2"></span></a>
		<ul id="hb-admin-right-menu">

		<?php foreach ( $hbook_pages as $page ) :
			if ( current_user_can( 'manage_' . $page['id'] ) ) :
				?>
				<li>
					<a <?php if ( $_GET['page'] == $page['id'] ) : ?>class="hb-admin-right-menu-current-item"<?php endif; ?> href="<?php echo( esc_url( admin_url( 'admin.php?page=' . $page['id'] ) ) ); ?>">
						<?php echo( esc_html( $page['name'] ) ); ?>
					</a>
				</li>
			<?php endif; ?>

		<?php endforeach; ?>

		</ul>

		<?php
	}

}
