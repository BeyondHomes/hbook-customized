<?php
class HbAdminPageOptions extends HbAdminPage {

	public function __construct( $page_id, $hbdb, $utils, $options_utils ) {
		$this->accom = $hbdb->get_all_accom();
		$this->data = array(
			'hb_text' => array(
				'new_option' => esc_html__( 'New extra', 'hbook-admin' ),
				'new_option_choice' => esc_html__( 'New extra choice', 'hbook-admin' ),
				'multiple_choice_yes' => esc_html__( 'Yes', 'hbook-admin' ),
				'multiple_choice_no' => esc_html__( 'No', 'hbook-admin' ),
				'invalid_amount' => esc_html__( 'Invalid amount.', 'hbook-admin' ),
				'adults' => esc_html__( 'Adults:', 'hbook-admin' ),
				'children' => esc_html__( 'Children:', 'hbook-admin' ),
				'link_accom' => esc_html__( 'For accommodation', 'hbook-admin' ),
				'link_booking' => esc_html__( 'For booking', 'hbook-admin' ),
			),
			'options' => $hbdb->get_all_options_with_choices(),
			'accom_list' => $this->accom,
			'hb_apply_to_types' => array(
				array(
					'option_value' => 'per-person',
					'option_text' => esc_html__( 'Per person', 'hbook-admin' )
				),
				array(
					'option_value' => 'per-person-per-day',
					'option_text' => esc_html__( 'Per person / per day', 'hbook-admin' )
				),
				array(
					'option_value' => 'per-accom',
					'option_text' => esc_html__( 'Once', 'hbook-admin' )
				),
				array(
					'option_value' => 'per-accom-per-day',
					'option_text' => esc_html__( 'Per day', 'hbook-admin' )
				),
				array(
					'option_value' => 'quantity',
					'option_text' => esc_html__( 'Quantity', 'hbook-admin' )
				),
				array(
					'option_value' => 'quantity-per-day',
					'option_text' => esc_html__( 'Quantity per day', 'hbook-admin' )
				),
			),
			'hb_apply_to_types_booking' => array(
				array(
					'option_value' => 'per-person',
					'option_text' => esc_html__( 'Per person', 'hbook-admin' )
				),
				array(
					'option_value' => 'per-accom-per-booking',
					'option_text' => esc_html__( 'Per accom.', 'hbook-admin' )
				),
				array(
					'option_value' => 'per-booking',
					'option_text' => esc_html__( 'Once', 'hbook-admin' )
				),
				array(
					'option_value' => 'quantity',
					'option_text' => esc_html__( 'Quantity', 'hbook-admin' )
				),
			),
			'hb_price_precision' => get_option('hb_price_precision' ),
		);
		parent::__construct( $page_id, $hbdb, $utils, $options_utils );
	}

	public function display() {
	?>
	<div class="wrap">

		<h2>
			<?php esc_html_e( 'Extra services', 'hbook-admin' ); ?>
			<a href="#" class="add-new-h2" data-bind="click: create_option"><?php esc_html_e( 'Add new extra', 'hbook-admin' ); ?></a>
			<span class="hb-add-new spinner"></span>
		</h2>

		<?php $this->display_right_menu(); ?>

		<br/>

		<!-- ko if: options().length == 0 -->
		<?php esc_html_e( 'No extra services have been created yet.', 'hbook-admin' ); ?>
		<!-- /ko -->

		<!-- ko if: options().length > 0 -->
		<div class="hb-table hb-options-table">

			<div class="hb-table-head hb-clearfix">
				<div class="hb-table-head-data"><?php esc_html_e( 'Extra name', 'hbook-admin' ); ?></div>
				<div class="hb-table-head-data hb-table-data-choice"><?php esc_html_e( 'Multiple choice', 'hbook-admin' ); ?></div>
				<div class="hb-table-head-data"><?php esc_html_e( 'Type', 'hbook-admin' ); ?></div>
				<div class="hb-table-head-data"><?php esc_html_e( 'Amount', 'hbook-admin' ); ?></div>
				<div class="hb-table-head-data hb-table-data-accom"><?php esc_html_e( 'Accommodation', 'hbook-admin' ); ?></div>
				<div class="hb-table-head-data hb-table-head-data-action"><?php esc_html_e( 'Actions', 'hbook-admin' ); ?></div>
			</div>

			<div data-bind="template: { name: template_to_use, foreach: options, as: 'option', beforeRemove: hide_setting }"></div>

		</div>
		<!-- /ko -->

		<!-- ko if: options().length > 10 -->
		<br/>
		<a href="#" class="add-new-h2 add-new-below" data-bind="click: create_option"><?php esc_html_e( 'Add new extra', 'hbook-admin' ); ?></a>
		<span class="hb-add-new spinner"></span>
		<!-- /ko -->

		<script id="text_tmpl" type="text/html">
			<div class="hb-table-row hb-clearfix">

				<div class="hb-table-data" data-bind="text: name"></div>

				<div class="hb-table-data hb-table-data-choice" data-bind="text: choice_type_text"></div>

				<div class="hb-table-data">
					<?php if ( get_option( 'hb_multiple_accom_booking' ) == 'enabled' ) { ?>
					<span data-bind="text: link_text"></span> /
					<?php } ?>
					<span data-bind="text: apply_to_type_text"></span>
				</div>

				<!-- ko if: choice_type() == 'multiple' -->
				<div class="hb-table-data"></div>
				<!-- /ko -->
				<!-- ko if: choice_type() == 'single' -->
				<div class="hb-table-data" data-bind="html: amount_text"></div>
				<!-- /ko -->

				<div class="hb-table-data hb-table-data-accom">
					<!-- ko if: link() == 'booking' -->
					-
					<!-- /ko -->
					<!-- ko if: link() != 'booking' -->
					<div data-bind="text: accom_list"></div>
					<!-- /ko -->
				</div>

				<div class="hb-table-data hb-table-data-action"><?php $this->display_admin_action( 'option' ); ?></div>

			</div>
			<div data-bind="template: { name: $parent.child_template_to_use, if: option.choice_type() == 'multiple' && option.apply_to_type() != 'quantity' && option.apply_to_type() != 'quantity-per-day', foreach: choices, beforeRemove: $parent.hide_setting }"></div>
		</script>

		<script id="edit_tmpl" type="text/html">
			<div class="hb-table-row hb-clearfix">

				<div class="hb-table-data"><input data-bind="value: name" type="text" /></div>

				<div class="hb-table-data hb-table-data-choice">
					<div data-bind="visible: apply_to_type() != 'quantity' && apply_to_type() != 'quantity-per-day'">
						<input data-bind="checked: choice_type" name="option_choice" id="option_choice_multiple" type="radio" value="multiple" />
						<label for="option_choice_multiple"><?php esc_html_e( 'Yes', 'hbook-admin' ); ?></label>&nbsp;&nbsp;
						<input data-bind="checked: choice_type" name="option_choice" id="option_choice_single" type="radio" value="single" />
						<label for="option_choice_single"><?php esc_html_e( 'No', 'hbook-admin' ); ?></label><br/>
						<p data-bind="visible: choice_type() == 'multiple'"><?php esc_html_e( 'Save to add choices.', 'hbook-admin' ); ?></p>
					</div>
					<div data-bind="visible: apply_to_type() == 'quantity' || apply_to_type() == 'quantity-per-day'">-</div>
				</div>

				<div class="hb-table-data">

					<?php if ( get_option( 'hb_multiple_accom_booking' ) == 'enabled' ) { ?>
					<div class="hb-option-link">
						<p>
							<input data-bind="checked: link" name="link" id="link_accom" type="radio" value="accom" />
							<label for="link_accom"><?php esc_html_e( 'For accommodation', 'hbook-admin' ); ?></label>
						</p>
						<p>
							<input data-bind="checked: link" name="link" id="link_booking" type="radio" value="booking" />
							<label for="link_booking"><?php esc_html_e( 'For booking', 'hbook-admin' ); ?></label><br/>
						</p>
					</div>
					<?php } ?>

					<select
						data-bind="
							options: apply_to_types_options,
							optionsValue: 'option_value',
							optionsText: function( item ) {
								return hb_decode_entities( item.option_text );
							},
							value: apply_to_type
						"
					></select>
					<div data-bind="visible: apply_to_type() == 'quantity' || apply_to_type() == 'quantity-per-day'">
						<div><?php esc_html_e( 'Set a maximum quantity?', 'hbook-admin' ); ?></div>
						<input data-bind="checked: quantity_max_option" name="quantity_max_option_choice" id="quantity_max_option_no" type="radio" value="no" />
						<label for="quantity_max_option_no"><?php esc_html_e( 'No', 'hbook-admin' ); ?></label><br/>
						<input data-bind="checked: quantity_max_option" name="quantity_max_option_choice" id="quantity_max_option_yes" type="radio" value="yes" />
						<label for="quantity_max_option_yes"><?php esc_html_e( 'Yes', 'hbook-admin' ); ?></label><br/>
						<input data-bind="checked: quantity_max_option" name="quantity_max_option_choice" id="quantity_max_option_per_person" type="radio" value="yes-per-person" />
						<label for="quantity_max_option_per_person"><?php esc_html_e( 'Yes, per person', 'hbook-admin' ); ?></label><br/>
					</div>
					<div data-bind="visible: apply_to_type() == 'quantity' || apply_to_type() == 'quantity-per-day'">
						<div data-bind="visible: quantity_max_option() != 'no'">
							<?php esc_html_e( 'Maximum quantity', 'hbook-admin' ); ?>
							<span data-bind="visible: quantity_max_option() == 'yes-per-person'"> <?php esc_html_e( '(per adult)', 'hbook-admin' ); ?></span>:
							<br/>
							<input data-bind="value: quantity_max" type="text" size="5" /><br/>
						</div>
						<div data-bind="visible: quantity_max_option() == 'yes-per-person'">
							<?php esc_html_e( 'Maximum quantity (per child):', 'hbook-admin' ); ?><br/>
							<input data-bind="value: quantity_max_child" type="text" size="5" />
						</div>
					</div>
				</div>

				<!-- ko if: choice_type() == 'multiple' -->
				<div class="hb-table-data">-</div>
				<!-- /ko -->
				<!-- ko if: choice_type() == 'single' -->
				<div class="hb-table-data">
					<!-- ko if: apply_to_type() == 'per-person' || apply_to_type() == 'per-person-per-day' -->
					<?php esc_html_e( 'Adults:', 'hbook-admin' ); ?><br/>
					<!-- /ko -->
					<input data-bind="value: amount" type="text" size="5" /><br/>
					<!-- ko if: apply_to_type() == 'per-person' || apply_to_type() == 'per-person-per-day' -->
					<?php esc_html_e( 'Children:', 'hbook-admin' ); ?><br/>
					<input data-bind="value: amount_children" type="text" size="5" />
					<!-- /ko -->
				</div>
				<!-- /ko -->

				<div class="hb-table-data hb-table-data-accom">
					<!-- ko if: link() == 'booking' -->
					-
					<!-- /ko -->
					<!-- ko if: link() != 'booking' -->
					<?php $this->display_checkbox_list( $this->accom, 'accom' ); ?>
					<!-- /ko -->
				</div>

				<div class="hb-table-data hb-table-data-action"><?php $this->display_admin_on_edit_action( 'option' ); ?></div>

			</div>
			<div data-bind="template: { name: $parent.child_template_to_use, if: option.choice_type() == 'multiple' && option.apply_to_type() != 'quantity' && option.apply_to_type() != 'quantity-per-day', foreach: choices, beforeRemove: $parent.hide_setting }"></div>
		</script>

		<script id="child_text_tmpl" type="text/html">
			<div class="hb-option-choice-row hb-clearfix">
				<div class="hb-table-data hb-table-data-choice-name" data-bind="text: name"></div>
				<div class="hb-table-data hb-table-data-choice">-</div>
				<div class="hb-table-data">-</div>
				<div class="hb-table-data" data-bind="html: amount_text"></div>
				<div class="hb-table-data hb-table-data-accom">-</div>
				<div class="hb-table-data hb-table-data-action"><?php $this->display_admin_action( 'option_choice' ); ?></div>
			</div>
		</script>

		<script id="child_edit_tmpl" type="text/html">
			<div class="hb-option-choice-row hb-clearfix">

				<div class="hb-table-data"><input data-bind="value: name" type="text" /></div>

				<div class="hb-table-data hb-table-data-choice">-</div>

				<div class="hb-table-data">-</div>

				<div class="hb-table-data">
					<!-- ko if: apply_to_type() == 'per-person' || apply_to_type() == 'per-person-per-day' -->
					<?php esc_html_e( 'Adults:', 'hbook-admin' ); ?><br/>
					<!-- /ko -->
					<input data-bind="value: amount" type="text" size="5" /><br/>
					<!-- ko if: apply_to_type() == 'per-person' || apply_to_type() == 'per-person-per-day' -->
					<?php esc_html_e( 'Children:', 'hbook-admin' ); ?><br/>
					<input data-bind="value: amount_children" type="text" size="5" />
					<!-- /ko -->
				</div>

				<div class="hb-table-data hb-table-data-accom">-</div>

				<div class="hb-table-data hb-table-data-action"><?php $this->display_admin_on_edit_action( 'option_choice' ); ?></div>

			</div>
		</script>

	</div><!-- end .wrap -->

	<?php
	}

}