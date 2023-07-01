<?php
class HbAdminPageFees extends HbAdminPage {

	private $accom;
	private $apply_to_types_fixed;
	private $apply_to_types_percent;

	public function __construct( $page_id, $hbdb, $utils, $options_utils ) {
		$this->apply_to_types_fixed = array(
			array(
				'option_value' => 'per-person',
				'option_text' => esc_html__( 'Per person', 'hbook-admin' )
			),
			array(
				'option_value' => 'per-person-per-day',
				'option_text' => esc_html__( 'Per person / per day', 'hbook-admin' )
			),
			array(
				'option_value' => 'per-accom-per-day',
				'option_text' => esc_html__( 'Per accommodation / per day', 'hbook-admin' )
			),
			array(
				'option_value' => 'per-accom',
				'option_text' => esc_html__( 'Per accommodation', 'hbook-admin' )
			),
			array(
				'option_value' => 'global-fixed',
				'option_text' => esc_html__( 'Per booking', 'hbook-admin' )
			),
		);
		$this->apply_to_types_percent = array(
			array(
				'option_value' => 'accom-percentage',
				'option_text' => esc_html__( 'Percentage (on Accommodation)', 'hbook-admin' ),
				'option_label' => esc_html__( 'Accommodation price', 'hbook-admin' )
			),
			array(
				'option_value' => 'extras-percentage',
				'option_text' => esc_html__( 'Percentage (on Extra Services)', 'hbook-admin' ),
				'option_label' => esc_html__( 'Extra Services price', 'hbook-admin' )
			),
			array(
				'option_value' => 'global-percentage',
				'option_text' => esc_html__( 'Percentage (global)', 'hbook-admin' ),
				'option_label' => esc_html__( 'Global price', 'hbook-admin' )
			),
		);
		$this->accom = $hbdb->get_all_accom();
		$this->data = array(
			'hb_text' => array(
				'new_fee' => esc_html__( 'New fee', 'hbook-admin' ),
				'invalid_amount' => esc_html__( 'Invalid amount.', 'hbook-admin' ),
				'adults' => esc_html__( 'Adults:', 'hbook-admin' ),
				'children' => esc_html__( 'Children:', 'hbook-admin' ),
				'min_amount' => esc_html__( 'Minimum amount:', 'hbook-admin' ),
				'max_amount' => esc_html__( 'Maximum amount:', 'hbook-admin' ),
				'included_in_accom_price' => esc_html__( 'Included in Accommodation price', 'hbook-admin' ),
				'included_in_extras_price' => esc_html__( 'Included in Extra Services price', 'hbook-admin' ),
				'included_in_prices' => esc_html__( 'Included in prices', 'hbook-admin' ),
				'added_to_accom_price' => esc_html__( 'Added to Accommodation price', 'hbook-admin' ),
				'added_to_extras_price' => esc_html__( 'Added to Extra Services price', 'hbook-admin' ),
				'added_to_prices' => esc_html__( 'Added to prices', 'hbook-admin' ),
				'added_to_final_price' => esc_html__( 'Added to final price', 'hbook-admin' ),
				'multiply_per_nb_nights' => esc_html__( 'Number of nights', 'hbook-admin' ),
				'multiply_per_adults_children' => esc_html__( 'Number of adults and children', 'hbook-admin' ),
				'multiply_per_adults' => esc_html__( 'Number of adults', 'hbook-admin' ),
				'multiply_per_children' => esc_html__( 'Number of children', 'hbook-admin' ),
			),
			'fees' => $hbdb->get_all_fees(),
			'accom_list' => $this->accom,
			'hb_apply_to_types' => array_merge(
				$this->apply_to_types_fixed,
				$this->apply_to_types_percent
			),
			'hb_price_precision' => get_option( 'hb_price_precision' ),
		);
		parent::__construct( $page_id, $hbdb, $utils, $options_utils );
	}

	public function display() {
	?>

	<div class="wrap">

		<h2>
			<?php esc_html_e( 'Fees', 'hbook-admin' ); ?>
			<a href="#" class="add-new-h2" data-bind="click: create_fee"><?php esc_html_e( 'Add new fee', 'hbook-admin' ); ?></a>
			<span class="hb-add-new spinner"></span>
		</h2>

		<?php $this->display_right_menu(); ?>

		<br/>

		<!-- ko if: fees().length == 0 -->
		<?php esc_html_e( 'No fees have been created yet.', 'hbook-admin' ); ?>
		<!-- /ko -->

		<!-- ko if: fees().length > 0 -->
		<div class="hb-table hb-fees-table">

			<div class="hb-table-head hb-clearfix">
				<div class="hb-table-head-data"><?php esc_html_e( 'Fee name', 'hbook-admin' ); ?></div>
				<div class="hb-table-head-data"><?php esc_html_e( 'Type', 'hbook-admin' ); ?></div>
				<div class="hb-table-head-data"><?php esc_html_e( 'Amount', 'hbook-admin' ); ?></div>
				<div class="hb-table-head-data"><?php esc_html_e( 'Accommodation', 'hbook-admin' ); ?></div>
				<div class="hb-table-head-data hb-table-head-data-action"><?php esc_html_e( 'Actions', 'hbook-admin' ); ?></div>
			</div>

			<div data-bind="template: { name: template_to_use, foreach: fees, beforeRemove: hide_setting }"></div>

		</div>
		<!-- /ko -->

		<!-- ko if: fees().length > 10 -->
		<br/>
		<a href="#" class="add-new-h2 add-new-below" data-bind="click: create_fee"><?php esc_html_e( 'Add new fee', 'hbook-admin' ); ?></a>
		<span class="hb-add-new spinner"></span>
		<!-- /ko -->

		<script id="text_tmpl" type="text/html">
			<div class="hb-table-row hb-clearfix">
				<div class="hb-table-data" data-bind="text: name"></div>
				<div class="hb-table-data">
					<div data-bind="text: apply_to_type_text"></div>
					<div data-bind="text: include_in_price_text"></div>
				</div>
				<div class="hb-table-data">
					<div data-bind="html: amount_text"></div>
					<div data-bind="html: amount_limits_text"></div>
					<div data-bind="html: multiply_per_text"></div>
				</div>
				<div data-bind="visible: ! global(), text: accom_list" class="hb-table-data"></div>
				<div data-bind="visible: global()" class="hb-table-data">-</div>
				<div class="hb-table-data hb-table-data-action"><?php $this->display_admin_action(); ?></div>
			</div>
		</script>

		<script id="edit_tmpl" type="text/html">
			<div class="hb-table-row hb-clearfix">
				<div class="hb-table-data"><input data-bind="value: name" type="text" /></div>
				<div class="hb-table-data">
					<?php $this->display_edit_amount_fixed_percent(); ?>
					<div data-bind="visible: amount_type() == 'fixed'">
						<p class="fee-type"><?php esc_html_e( 'Fee is:', 'hbook-admin' ); ?></p>
						<?php foreach ( $this->apply_to_types_fixed as $type ) {
							$input_id = 'hb-fee-apply-to-type-' . $type['option_value'];
							?>
							<input
								id="<?php echo( esc_attr( $input_id ) ); ?>"
								data-bind="checked: apply_to_type"
								type="radio"
								value="<?php echo( esc_attr( $type['option_value'] ) ); ?>"
							/>
							<label for="<?php echo( esc_attr( $input_id ) ); ?>">
								<?php echo( esc_html( $type['option_text'] ) ); ?>
							</label>
							<br>
						<?php } ?>
					</div>
					<div data-bind="visible: amount_type() == 'percent'">
						<p class="fee-type"><?php esc_html_e( 'Fee applies on:', 'hbook-admin' ); ?></p>
						<?php foreach ( $this->apply_to_types_percent as $type ) {
							$input_id = 'hb-fee-apply-to-type-' . $type['option_value'];
							?>
							<input
								id="<?php echo( esc_attr( $input_id ) ); ?>"
								data-bind="checked: apply_to_type"
								type="radio"
								value="<?php echo( esc_attr( $type['option_value'] ) ); ?>"
							/>
							<label for="<?php echo( esc_attr( $input_id ) ); ?>">
								<?php echo( esc_html( $type['option_label'] ) ); ?>
							</label>
							<br>
						<?php } ?>
						<div data-bind="visible: apply_to_type() == 'accom-percentage'">
							<p class="fee-type"><?php esc_html_e( 'Apply on Accommodation per person per night price?', 'hbook-admin' ); ?></p>
							<input data-bind="checked: accom_price_per_person_per_night, checkedValue: false" id="hb-fee-accom-per-person-per-night-percentage-no" type="radio" />
							<label for="hb-fee-accom-per-person-per-night-percentage-no"><?php esc_html_e( 'No', 'hbook-admin' ); ?></label><br>
							<input data-bind="checked: accom_price_per_person_per_night, checkedValue: true" id="hb-fee-accom-per-person-per-night-percentage-yes" type="radio" />
							<label for="hb-fee-accom-per-person-per-night-percentage-yes"><?php esc_html_e( 'Yes', 'hbook-admin' ); ?></label>
						</div>
					</div>
					<p class="fee-type"><?php esc_html_e( 'Fee is:', 'hbook-admin' ); ?></p>
					<input data-bind="disable: apply_to_type() == 'global-fixed', checked: include_in_price" id="hb-fee-final-price" type="radio" value="0" />
					<label for="hb-fee-final-price"><?php esc_html_e( 'Added to final price', 'hbook-admin' ); ?></label>
					<br/>
					<input data-bind="disable: apply_to_type() == 'global-fixed', checked: include_in_price" id="hb-fee-added-to-price" type="radio" value="1" />
					<label data-bind="text: added_to_price_label" for="hb-fee-added-to-price"></label>
					<br/>
					<input data-bind="disable: apply_to_type() == 'global-fixed' || accom_price_per_person_per_night(), checked: include_in_price" id="hb-fee-included-in-price" type="radio" value="2" />
					<label data-bind="text: included_in_price_label" for="hb-fee-included-in-price"></label>
				</div>
				<div class="hb-table-data">
					<!-- ko if: amount_type() == 'percent' -->
					<?php esc_html_e( 'Percentage:', 'hbook-admin' ); ?><br/>
					<!-- /ko -->
					<!-- ko if: apply_to_type() == 'per-person' || apply_to_type() == 'per-person-per-day' -->
					<?php esc_html_e( 'Adults:', 'hbook-admin' ); ?><br/>
					<!-- /ko -->
					<input data-bind="value: amount" type="text" size="5" /><br/>
					<!-- ko if: apply_to_type() == 'per-person' || apply_to_type() == 'per-person-per-day' -->
					<?php esc_html_e( 'Children:', 'hbook-admin' ); ?><br/>
					<input data-bind="value: amount_children" type="text" size="5" />
					<!-- /ko -->
					<!-- ko if: accom_price_per_person_per_night() -->
					<p class="fee-type">
						<?php esc_html_e( 'Minimum amount:', 'hbook-admin' ); ?><br/>
						<input data-bind="value: minimum_amount" type="text" size="5">
						<?php esc_html_e( 'Maximum amount:', 'hbook-admin' ); ?><br/>
						<input data-bind="value: maximum_amount" type="text" size="5">
					</p>
					<p class="fee-type">
						<?php esc_html_e( 'Multiply per:', 'hbook-admin' ); ?><br/>
						<input id="multiply-per-nights" data-bind="checked: multiply_per" type="checkbox" value="nb_nights">
						<label for="multiply-per-nights"><?php esc_html_e( 'Number of nights', 'hbook-admin' ); ?></label><br/>
						<input id="multiply-per-adults-children" data-bind="checked: multiply_per" type="checkbox" value="adults_children">
						<label for="multiply-per-adults-children"><?php esc_html_e( 'Number of adults and children', 'hbook-admin' ); ?></label><br/>
						<input id="multiply-per-adults" data-bind="disable: multiply_per.indexOf( 'adults_children' ) != -1 || multiply_per.indexOf( 'children' ) != -1, checked: multiply_per" type="checkbox" value="adults">
						<label for="multiply-per-adults"><?php esc_html_e( 'Number of adults', 'hbook-admin' ); ?></label><br/>
						<input id="multiply-per-children" data-bind="disable: multiply_per.indexOf( 'adults_children' ) != -1 || multiply_per.indexOf( 'adults' ) != -1, checked: multiply_per" type="checkbox" value="children">
						<label for="multiply-per-children"><?php esc_html_e( 'Number of children', 'hbook-admin' ); ?></label>
					</p>
					<!-- /ko -->
				</div>
				<div data-bind="visible: ! global()" class="hb-table-data"><?php $this->display_checkbox_list( $this->accom, 'accom' ); ?></div>
				<div data-bind="visible: global()" class="hb-table-data">-</div>
				<div class="hb-table-data hb-table-data-action"><?php $this->display_admin_on_edit_action(); ?></div>
			</div>
		</script>

	</div><!-- end .wrap -->

	<?php
	}

}