<?php
class HbAdminPageRules extends HbAdminPage {

	private $accom;
	private $conditional_type;
	private $seasons;

	public function __construct( $page_id, $hbdb, $utils, $options_utils ) {
		$this->accom = $hbdb->get_all_accom();
		$this->seasons = $hbdb->get_seasons_id_name();
		$this->conditional_types = array(
			'compulsory' => esc_html__( 'Compulsory', 'hbook-admin' ),
			'special_rate' => esc_html__( 'Special rate', 'hbook-admin' ),
			'comp_and_rate' => esc_html__( 'Compulsory and special rate', 'hbook-admin' ),
			'discount' => esc_html__( 'Discount', 'hbook-admin' ),
			'coupon' => esc_html__( 'Coupon', 'hbook-admin' ),
		);
		$this->data = array(
			'hb_text' => array(
				'any' => esc_html__( 'Any', 'hbook-admin' ),
				'no_days_selected' => esc_html__( 'No days selected', 'hbook-admin' ),
				'new_rule' => esc_html__( 'New rule', 'hbook-admin' ),
				'yes' => esc_html__( 'Yes', 'hbook-admin' ),
				'no' => esc_html__( 'No', 'hbook-admin' ),
				'no_seasons_selected' => esc_html__( 'No seasons selected', 'hbook-admin' ),
			),
			'days_short_name' => $utils->days_short_name(),
			'rules' => $hbdb->get_all_booking_rules(),
			'accom_list' => $this->accom,
			'seasons_list' => $this->seasons,
			'conditional_types' => $this->conditional_types,
		);
		parent::__construct( $page_id, $hbdb, $utils, $options_utils );
	}


	public function display() {
	?>

	<div class="wrap">

		<h2><?php esc_html_e( 'Booking rules', 'hbook-admin' ); ?></h2>
		<?php $this->display_right_menu(); ?>

		<hr/>

		<!-- -------------------------------------------------------------------------- -->

		<!-- allowed check-in days end -->


		<h3>
			<?php esc_html_e( 'Check-in days', 'hbook-admin' ); ?>
			<a data-bind="click: function() { create_rule( 'check_in_days' ) }" href="#" class="add-new-h2"><?php esc_html_e( 'Add rule', 'hbook-admin' ); ?></a>
			<span class="hb-add-new spinner hb-add-check-in-days"></span>
		</h3>

		<!-- ko if: nb_rules( 'check_in_days' ) == 0 -->
		<p><?php esc_html_e( 'Customers can check-in any day.', 'hbook-admin' ); ?></p>
		<!-- /ko -->

		<!-- ko if: nb_rules( 'check_in_days' ) > 0 -->
		<table class="wp-list-table widefat hb-rule-table">

			<thead>
				<tr>
					<th><?php esc_html_e( 'Allowed check-in days', 'hbook-admin' ); ?></th>
					<th><?php esc_html_e( 'Accommodation', 'hbook-admin' ); ?></th>
					<th><?php esc_html_e( 'Seasons', 'hbook-admin' ); ?></th>
					<th class="hb-table-action"><?php esc_html_e( 'Actions', 'hbook-admin' ); ?></th>
				</tr>
			</thead>

			<tbody data-bind="template: { name: function( rule ) { return rule_template_to_use( rule, 'check_in_days' ); }, foreach: rules, beforeRemove: hide_setting }">
			</tbody>

		</table>
		<!-- /ko -->

		<script id="check_in_days_rule_text_tmpl" type="text/html">
			<tr>
				<td data-bind="text: check_in_days_list"></td>
				<td data-bind="text: accom_list"></td>
				<td data-bind="text: seasons_list"></td>
				<td class="hb-table-action"><?php $this->display_admin_action(); ?></td>
			</tr>
		</script>

		<script id="check_in_days_rule_edit_tmpl" type="text/html">
			<tr>
				<td><?php $this->display_select_days( 'check_in_days' ); ?></td>
				<td><?php $this->display_checkbox_list( $this->accom, 'accom' ); ?></td>
				<td><?php $this->display_checkbox_list( $this->seasons, 'seasons' ); ?></td>
				<td class="hb-table-action"><?php $this->display_admin_on_edit_action(); ?></td>
			</tr>
		</script>

		<br/><hr/>


		<!-- allowed check-in days end -->

		<!-- -------------------------------------------------------------------------- -->

		<!-- allowed check-out days begin -->


		<h3>
			<?php esc_html_e( 'Check-out days', 'hbook-admin' ); ?>
			<a data-bind="click: function() { create_rule( 'check_out_days' ) }" href="#" class="add-new-h2"><?php esc_html_e( 'Add rule', 'hbook-admin' ); ?></a>
			<span class="hb-add-new spinner hb-add-check-out-days"></span>
		</h3>

		<!-- ko if: nb_rules( 'check_out_days' ) == 0 -->
		<p><?php esc_html_e( 'Customers can check-out any day.', 'hbook-admin' ); ?></p>
		<!-- /ko -->

		<!-- ko if: nb_rules( 'check_out_days' ) > 0 -->
		<table class="wp-list-table widefat hb-rule-table">

			<thead>
				<tr>
					<th><?php esc_html_e( 'Allowed check-out days', 'hbook-admin' ); ?></th>
					<th><?php esc_html_e( 'Accommodation', 'hbook-admin' ); ?></th>
					<th><?php esc_html_e( 'Seasons', 'hbook-admin' ); ?></th>
					<th class="hb-table-action"><?php esc_html_e( 'Actions', 'hbook-admin' ); ?></th>
				</tr>
			</thead>

			<tbody data-bind="template: { name: function( rule ) { return rule_template_to_use( rule, 'check_out_days' ); }, foreach: rules, beforeRemove: hide_setting }">
			</tbody>

		</table>
		<!-- /ko -->

		<script id="check_out_days_rule_text_tmpl" type="text/html">
			<tr>
				<td data-bind="text: check_out_days_list"></td>
				<td data-bind="text: accom_list"></td>
				<td data-bind="text: seasons_list"></td>
				<td class="hb-table-action"><?php $this->display_admin_action(); ?></td>
			</tr>
		</script>

		<script id="check_out_days_rule_edit_tmpl" type="text/html">
			<tr>
				<td><?php $this->display_select_days( 'check_out_days' ); ?></td>
				<td><?php $this->display_checkbox_list( $this->accom, 'accom' ); ?></td>
				<td><?php $this->display_checkbox_list( $this->seasons, 'seasons' ); ?></td>
				<td class="hb-table-action"><?php $this->display_admin_on_edit_action(); ?></td>
			</tr>
		</script>

		<br/><hr/>


		<!-- allowed check-out days end -->

		<!-- -------------------------------------------------------------------------- -->

		<!-- minimum stay begin -->


		<h3>
			<?php esc_html_e( 'Minimum stay length', 'hbook-admin' ); ?>
			<a data-bind="click: function() { create_rule( 'minimum_stay' ) }" href="#" class="add-new-h2"><?php esc_html_e( 'Add rule', 'hbook-admin' ); ?></a>
			<span class="hb-add-new spinner hb-add-minimum-stay"></span>
		</h3>

		<!-- ko if: nb_rules( 'minimum_stay' ) == 0 -->
		<p><?php esc_html_e( 'There is no minimum stay rules.', 'hbook-admin' ); ?></p>
		<!-- /ko -->

		<!-- ko if: nb_rules( 'minimum_stay' ) > 0 -->
		<table class="wp-list-table widefat hb-rule-table">

			<thead>
				<tr>
					<th><?php esc_html_e( 'Minimum stay', 'hbook-admin' ); ?></th>
					<th><?php esc_html_e( 'Accommodation', 'hbook-admin' ); ?></th>
					<th><?php esc_html_e( 'Seasons', 'hbook-admin' ); ?></th>
					<th class="hb-table-action"><?php esc_html_e( 'Actions', 'hbook-admin' ); ?></th>
				</tr>
			</thead>

			<tbody data-bind="template: { name: function( rule ) { return rule_template_to_use( rule, 'minimum_stay' ); }, foreach: rules, beforeRemove: hide_setting }">
			</tbody>

		</table>
		<!-- /ko -->

		<script id="minimum_stay_rule_text_tmpl" type="text/html">
			<tr>
				<td><span data-bind="text: minimum_stay"></span> <?php esc_html_e( 'nights', 'hbook-admin' ); ?></td>
				<td data-bind="text: accom_list"></td>
				<td data-bind="text: seasons_list"></td>
				<td class="hb-table-action"><?php $this->display_admin_action(); ?></td>
			</tr>
		</script>

		<script id="minimum_stay_rule_edit_tmpl" type="text/html">
			<tr>
				<td><input data-bind="value: minimum_stay" class="hb-stay-length" type="text" /> <span><?php esc_html_e( 'nights', 'hbook-admin' ); ?></span></td>
				<td><?php $this->display_checkbox_list( $this->accom, 'accom' ); ?></td>
				<td><?php $this->display_checkbox_list( $this->seasons, 'seasons' ); ?></td>
				<td class="hb-table-action"><?php $this->display_admin_on_edit_action(); ?></td>
			</tr>
		</script>

		<br/><hr/>


		<!-- minimum stay end -->

		<!-- -------------------------------------------------------------------------- -->

		<!-- maximum stay begin -->


		<h3>
			<?php esc_html_e( 'Maximum stay length', 'hbook-admin' ); ?>
			<a data-bind="click: function() { create_rule( 'maximum_stay' ) }" href="#" class="add-new-h2"><?php esc_html_e( 'Add rule', 'hbook-admin' ); ?></a>
			<span class="hb-add-new spinner hb-add-maximum-stay"></span>
		</h3>

		<!-- ko if: nb_rules( 'maximum_stay' ) == 0 -->
		<p><?php esc_html_e( 'There is no maximum stay rules.', 'hbook-admin' ); ?></p>
		<!-- /ko -->

		<!-- ko if: nb_rules( 'maximum_stay' ) > 0 -->
		<table class="wp-list-table widefat hb-rule-table">

			<thead>
				<tr>
					<th><?php esc_html_e( 'Maximum stay', 'hbook-admin' ); ?></th>
					<th><?php esc_html_e( 'Accommodation', 'hbook-admin' ); ?></th>
					<th><?php esc_html_e( 'Seasons', 'hbook-admin' ); ?></th>
					<th class="hb-table-action"><?php esc_html_e( 'Actions', 'hbook-admin' ); ?></th>
				</tr>
			</thead>

			<tbody data-bind="template: { name: function( rule ) { return rule_template_to_use( rule, 'maximum_stay' ); }, foreach: rules, beforeRemove: hide_setting }">
			</tbody>

		</table>
		<!-- /ko -->

		<script id="maximum_stay_rule_text_tmpl" type="text/html">
			<tr>
				<td><span data-bind="text: maximum_stay"></span> <?php esc_html_e( 'nights', 'hbook-admin' ); ?></td>
				<td data-bind="text: accom_list"></td>
				<td data-bind="text: seasons_list"></td>
				<td class="hb-table-action"><?php $this->display_admin_action(); ?></td>
			</tr>
		</script>

		<script id="maximum_stay_rule_edit_tmpl" type="text/html">
			<tr>
				<td><input data-bind="value: maximum_stay" class="hb-stay-length" type="text" /> <span><?php esc_html_e( 'nights', 'hbook-admin' ); ?></span></td>
				<td><?php $this->display_checkbox_list( $this->accom, 'accom' ); ?></td>
				<td><?php $this->display_checkbox_list( $this->seasons, 'seasons' ); ?></td>
				<td class="hb-table-action"><?php $this->display_admin_on_edit_action(); ?></td>
			</tr>
		</script>

		<br/><hr/>


		<!-- maximum stay end -->

		<!-- -------------------------------------------------------------------------- -->

		<!-- conditional begin -->


		<h3 id="hb-additional-rules-title">
			<?php esc_html_e( 'Advanced rules', 'hbook-admin' ); ?>
			<a data-bind="click: function() { create_rule( 'conditional' ) }" href="#" class="add-new-h2"><?php esc_html_e( 'Add rule', 'hbook-admin' ); ?></a>
			<span class="hb-add-new spinner hb-add-conditional"></span>
		</h3>

		<p><?php esc_html_e( 'You may define advanced rules to create compulsory settings for stays, or to create special rates or discounts, or to add a rule for a coupon.', 'hbook-admin' ); ?></p>

		<!-- ko if: nb_rules( 'conditional' ) == 0 -->
		</p><?php esc_html_e( 'There is no advanced rules.', 'hbook-admin' ); ?></p>
		<!-- /ko -->

		<!-- ko if: nb_rules( 'conditional' ) > 0 -->
		<table class="wp-list-table widefat hb-conditional-rule-table">

			<thead>
				<tr>
					<th class="hb-conditional-rule-name hb-rule-box-with-border"><?php esc_html_e( 'Name', 'hbook-admin' ); ?></th>
					<th class="hb-rule-box-with-border" width="12%"><?php esc_html_e( 'Rule type', 'hbook-admin' ); ?></th>
					<th><?php esc_html_e( 'Check-in days', 'hbook-admin' ); ?></th>
					<th><?php esc_html_e( 'Check-out days', 'hbook-admin' ); ?></th>
					<th><?php esc_html_e( 'Minimum stay', 'hbook-admin' ); ?></th>
					<th><?php esc_html_e( 'Maximum stay', 'hbook-admin' ); ?></th>
					<th><?php esc_html_e( 'Accommodation', 'hbook-admin' ); ?></th>
					<th class="hb-rule-box-with-border" width="11%"><?php esc_html_e( 'Seasons', 'hbook-admin' ); ?></th>
					<th class="hb-table-action"><?php esc_html_e( 'Actions', 'hbook-admin' ); ?></th>
				</tr>
			</thead>

			<tbody data-bind="template: { name: function( rule ) { return rule_template_to_use( rule, 'conditional' ); }, foreach: rules, beforeRemove: hide_setting }">
			</tbody>

		</table>

		<script id="conditional_rule_text_tmpl" type="text/html">
			<tr>
				<td class="hb-rule-box-with-border" data-bind="text: name"></td>
				<td class="hb-rule-box-with-border" data-bind="text: conditional_type_display"></td>
				<td data-bind="text: check_in_days_list"></td>
				<td data-bind="text: check_out_days_list"></td>
				<td>
					<!-- ko if: minimum_stay() == '' -->
					---
					<!-- /ko -->
					<!-- ko if: minimum_stay() != '' -->
					<span data-bind="text: minimum_stay"></span> <?php esc_html_e( 'nights', 'hbook-admin' ); ?>
					<!-- /ko -->
				</td>
				<td>
					<!-- ko if: maximum_stay() == ''-->
					---
					<!-- /ko -->
					<!-- ko if: maximum_stay() != '' -->
					<span data-bind="text: maximum_stay"></span> <?php esc_html_e( 'nights', 'hbook-admin' ); ?>
					<!-- /ko -->
				</td>
				<td>
					<!-- ko if: conditional_type() == 'compulsory' || conditional_type() == 'comp_and_rate' -->
					<span data-bind="text: accom_list"></span>
					<!-- /ko -->
					<!-- ko if: conditional_type() != 'compulsory' && conditional_type() != 'comp_and_rate' -->
					---
					<!-- /ko -->
				</td>
				<td class="hb-rule-box-with-border">
					<!-- ko if: conditional_type() == 'compulsory' || conditional_type() == 'comp_and_rate' -->
					<span data-bind="text: seasons_list"></span>
					<!-- /ko -->
					<!-- ko if: conditional_type() != 'compulsory' && conditional_type() != 'comp_and_rate' -->
					---
					<!-- /ko -->
				</td>
				<td class="hb-table-action"><?php $this->display_admin_action(); ?></td>
			</tr>
		</script>

		<script id="conditional_rule_edit_tmpl" type="text/html">
			<tr>
				<td rowspan="2" class="hb-rule-box-with-border"><input data-bind="value: name" type="text" /></td>
				<td rowspan="2" class="hb-rule-box-with-border">
					<?php
					foreach ( $this->conditional_types as $cond_type => $cond_type_label ) {
						$cond_type_id = 'hb_' . $cond_type;
						?>
					<input data-bind="checked: conditional_type" id="<?php echo( esc_attr( $cond_type_id ) ); ?>" type="radio" value="<?php echo( esc_attr( $cond_type ) ); ?>" />
					<label for="<?php echo( esc_attr( $cond_type_id ) ); ?>"><?php echo( esc_html( $cond_type_label ) ); ?></label>
					<br/>
					<?php } ?>
				</td>
				<td>
					<!-- ko if: conditional_type() == 'compulsory' || conditional_type() == 'comp_and_rate' -->
					<?php esc_html_e( 'Guests who check-in on:', 'hbook-admin' ); ?>
					<!-- /ko -->
					<!-- ko if: conditional_type() == 'special_rate' -->
					<?php esc_html_e( 'Apply a special rate when check-in is on:', 'hbook-admin' ); ?>
					<!-- /ko -->
					<!-- ko if: conditional_type() == 'discount' -->
					<?php esc_html_e( 'Apply a discount when check-in is on:', 'hbook-admin' ); ?>
					<!-- /ko -->
					<!-- ko if: conditional_type() == 'coupon' -->
					<?php esc_html_e( 'Apply a coupon when check-in is on:', 'hbook-admin' ); ?>
					<!-- /ko -->
				</td>

				<!-- ko if: conditional_type() == 'compulsory' || conditional_type() == 'comp_and_rate' -->
				<td>
					<?php esc_html_e( '...must check-out on:', 'hbook-admin' ); ?>
				</td>
				<td>
					<?php esc_html_e( '...and must have a minimum stay of:', 'hbook-admin' ); ?>
				</td>
				<td>
					<?php esc_html_e( '...and must have a maximum stay of:', 'hbook-admin' ); ?>
				</td>
				<td>
					<?php esc_html_e( '...for accommodation:', 'hbook-admin' ); ?>
				</td>
				<td class="hb-rule-box-with-border">
					<?php esc_html_e( '...and for seasons:', 'hbook-admin' ); ?>
				</td>
				<!-- /ko -->

				<!-- ko if: conditional_type() != 'compulsory' && conditional_type() != 'comp_and_rate' -->
				<td>
					<?php esc_html_e( '...and check-out is on:', 'hbook-admin' ); ?>
				</td>
				<td>
					<?php esc_html_e( '...and minimum stay is:', 'hbook-admin' ); ?>
				</td>
				<td>
					<?php esc_html_e( '...and maximum stay is:', 'hbook-admin' ); ?>
				</td>
				<td colspan="2" class="hb-rule-box-with-border">
				<?php esc_html_e( 'Select accommodation and seasons in the Rates page.', 'hbook-admin' ); ?>
				</td>
				<!-- /ko -->

				<td rowspan="2" class="hb-table-action"><?php $this->display_admin_on_edit_action(); ?></td>
			</tr>
			<tr>
				<td class="hb-advanced-rule-condition">
					<?php $this->display_select_days( 'check_in_days' ); ?>
				</td>
				<td class="hb-advanced-rule-condition">
					<?php $this->display_select_days( 'check_out_days' ); ?>
				</td>
				<td class="hb-advanced-rule-condition">
					<input data-bind="value: minimum_stay" class="hb-stay-length" type="text" /> <span><?php esc_html_e( 'nights', 'hbook-admin' ); ?></span><br/>
					<span class="hb-rule-legend"><?php esc_html_e( '(leave empty for no minimum stay)', 'hbook-admin' ); ?></span>
				</td>
				<td class="hb-advanced-rule-condition">
					<input data-bind="value: maximum_stay" class="hb-stay-length" type="text" /> <span><?php esc_html_e( 'nights', 'hbook-admin' ); ?></span><br/>
					<span class="hb-rule-legend"><?php esc_html_e( '(leave empty for no maximum stay)', 'hbook-admin' ); ?></span>
				</td>
				<td class="hb-advanced-rule-condition">
					<!-- ko if: conditional_type() == 'compulsory' || conditional_type() == 'comp_and_rate' -->
					<?php $this->display_checkbox_list( $this->accom, 'accom' ); ?>
					<!-- /ko -->
				</td>
				<td class="hb-rule-box-with-border hb-advanced-rule-condition">
					<!-- ko if: conditional_type() == 'compulsory' || conditional_type() == 'comp_and_rate' -->
					<?php $this->display_checkbox_list( $this->seasons, 'seasons' ); ?>
					<!-- /ko -->
				</td>
			</tr>
		</script>

		<!-- /ko -->

		<div id="hb-rules-page-bottom"></div>

		<script id="empty_tmpl"></script>

	</div>

	<?php
	}
}