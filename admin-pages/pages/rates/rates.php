<?php
class HbAdminPageRates extends HbAdminPage {

	private $seasons;
	private $accom;
	private $rules;
	private $rate_types;
	private $discounts;
	private $discount_rules;

	public function __construct( $page_id, $hbdb, $utils, $options_utils ) {
		$this->seasons = $hbdb->get_seasons_id_name();
		$this->accom = $hbdb->get_all_accom();
		$this->rules = $hbdb->get_all_rate_booking_rules();
		$this->discount_rules = $hbdb->get_all_discount_rules();
		$this->discounts = $hbdb->get_all_discounts();
		$this->coupon_rules = $hbdb->get_all_coupon_rules();
		$this->coupons = $hbdb->get_all_coupons();
		foreach ( $this->coupons as $i => $coupon ) {
			if ( $coupon['max_use_count'] == 0 ) {
				$this->coupons[ $i ]['max_use_count'] = '';
			}
		}
		$this->rate_types = array(
			array(
				'id' => 'accom',
				'name' => esc_html__( 'accommodation', 'hbook-admin' ),
				'title' => esc_html__( 'Accommodation rates', 'hbook-admin' ),
			),
			array(
				'id' => 'extra_adults',
				'name' => esc_html__( 'extra adults', 'hbook-admin' ),
				'title' => esc_html__( 'Price per adult above normal occupancy', 'hbook-admin' ),
			),
			array(
				'id' => 'extra_children',
				'name' => esc_html__( 'extra children', 'hbook-admin' ),
				'title' => esc_html__( 'Price per child above normal occupancy', 'hbook-admin' ),
			)
		);

		$this->data = array(
			'hb_text' => array(
				'invalid_amount' => esc_html__( 'Invalid amount.', 'hbook-admin' ),
				'no_seasons_selected' => esc_html__( 'No seasons selected', 'hbook-admin' ),
				'no_rules_selected' => '',
				'no_discounts_selected' => esc_html__( 'No discounts selected', 'hbook-admin' ),
				'per_night' => esc_html__( 'per night', 'hbook-admin' ),
				'for_nights' => esc_html__( 'for a %s-night stay', 'hbook-admin' ),
				'unset' => esc_html__( 'Unset', 'hbook-admin' ),
				'nights_more' => esc_html__( 'nights and more', 'hbook-admin' ),
				'no_max_use_count' => esc_html__( 'No maximum', 'hbook-admin' ),
				'no_date_limit' => esc_html__( 'No date limit', 'hbook-admin' ),
				'multiple_use_per_customer_yes' => esc_html__( 'Yes', 'hbook-admin' ),
				'multiple_use_per_customer_no' => esc_html__( 'No', 'hbook-admin' ),
				'discount_apply_to_accom' => esc_html__( 'Accommodation price', 'hbook-admin' ),
				'discount_apply_to_global' => esc_html__( 'Global price', 'hbook-admin' ),
			),
			'rates' => $hbdb->get_all_rates(),
			'accom_list' => $this->accom,
			'seasons_list' => $this->seasons,
			'rules_list' => $this->rules,
			'discount_rules_list' => $this->discount_rules,
			'discounts' => $this->discounts,
			'coupon_rules_list' => $this->coupon_rules,
			'coupons' => $this->coupons,
			'hb_price_precision' => get_option( 'hb_price_precision' ),
		);
		parent::__construct( $page_id, $hbdb, $utils, $options_utils );
	}

	public function display() {
	?>

	<div class="wrap">

		<h2><?php esc_html_e( 'Rates', 'hbook-admin' ); ?></h2>
		<?php $this->display_right_menu(); ?>

		<hr/>

		<?php
		if ( $this->utils->nb_accom() == 0 ) {
			$accom_all_status = false;
			$accom_all_languages = true;
			$all_accom_in_all_languages = $this->hbdb->get_all_accom_ids( $accom_all_status, $accom_all_languages );
			if ( count( $all_accom_in_all_languages ) > 0 ) {
				if ( function_exists( 'pll_get_post' ) ) {
					echo( '<p>' );
					printf(
						esc_html__( 'It seems Polylang is not set properly. Please read %s', 'hbook-admin' ),
						'<a target="_blank" href="https://maestrel.com/knowledgebase/?article=39">' .
						esc_html__( 'HBook documentation about setting up Polylang and HBook', 'hbook-admin' ) .
						'</a>'
					);
					echo( '</p>' );
				} else if ( function_exists( 'icl_object_id' ) ) {
					echo( '<p>' );
					printf(
						esc_html__( 'It seems WPML is not set properly. Please read %s', 'hbook-admin' ),
						'<a target="_blank" href="https://maestrel.com/knowledgebase/?article=37">' .
						esc_html__( 'HBook documentation about setting up WPML and HBook', 'hbook-admin' ) .
						'</a>'
					);
					echo( '</p>' );
				} else {
					echo( '<p>' . esc_html__( 'At least one accommodation which uses the default website language must be created in order to set rates.', 'hbook-admin' ) . '</p>' );
				}
			} else {
				if ( count( $this->seasons ) == 0 ) {
					echo( '<p>' . esc_html__( 'At least one accommodation and one season must be created in order to set rates.', 'hbook-admin' ) . '</p>' );
				} else {
					echo( '<p>' . esc_html__( 'At least one accommodation must be created in order to set rates.', 'hbook-admin' ) . '</p>' );
				}
			}
		} else if ( count( $this->seasons ) == 0 ) {
			echo( '<p>' . esc_html__( 'At least one one season must be created in order to set rates.', 'hbook-admin' ) . '</p>' );
		} else {
			foreach ( $this->rate_types as $rate_type ) {
		?>

			<h3>
				<?php echo( esc_html( $rate_type['title'] ) ); ?>
				<?php $new_rate_data_bind = "click: function() { create_rate( '" . $rate_type['id'] . "' ) }"; ?>
				<a data-bind="<?php echo( esc_attr( $new_rate_data_bind ) ); ?>" href="#" class="add-new-h2"><?php esc_html_e( 'Add rate', 'hbook-admin' ); ?></a>
				<?php $new_rate_spinner = 'hb-add-new spinner hb-add-' . $rate_type['id'] . '-rate'; ?>
				<span class="<?php echo( esc_attr( $new_rate_spinner ) ); ?>"></span>
			</h3>

			<!-- ko if: nb_rates( '<?php echo( esc_html( $rate_type['id'] ) ); ?>' ) == 0 -->
			<p><?php printf( esc_html__( 'No %s rates have been defined yet.', 'hbook-admin' ), esc_html( $rate_type['name'] ) ); ?></p>
			<!-- /ko -->

			<!-- ko if: nb_rates( '<?php echo( esc_html( $rate_type['id'] ) ); ?>' ) > 0 -->
			<table class="wp-list-table widefat">

				<?php $this->table_rates_head(); ?>
				<?php $rate_table_data_bind = "template: { name: function( rate ) { return rate_template_to_use( rate, '" . $rate_type['id'] . "' ); }, foreach: rates, beforeRemove: hide_setting }"; ?>

				<tbody data-bind="<?php echo( esc_attr( $rate_table_data_bind ) ); ?>">
				</tbody>

			</table>
			<!-- /ko -->

			<!-- ko if: nb_rates( '<?php echo( esc_html( $rate_type['id'] ) ); ?>' ) > 10 -->
			<br/>
			<a data-bind="<?php echo( esc_attr( $new_rate_data_bind ) ); ?>" href="#" class="add-new-h2 add-new-below"><?php esc_html_e( 'Add rate', 'hbook-admin' ); ?></a>
			<?php $new_rate_spinner = 'hb-add-new spinner hb-add-' . $rate_type['id'] . '-rate'; ?>
			<span class="<?php echo( esc_attr( $new_rate_spinner ) ); ?>"></span>
			<br/>
			<!-- /ko -->

			<br/><hr/>

		<?php } ?>

		<h3>
			<?php esc_html_e( 'Discounts', 'hbook-admin' ); ?>
			<?php if ( $this->discount_rules ) { ?>
			<a data-bind="click: create_discount" href="#" class="add-new-h2"><?php esc_html_e( 'Add discount rate', 'hbook-admin' ); ?></a>
			<span class="hb-add-new spinner hb-add-discount"></span>
			<?php } ?>
		</h3>

		<?php if ( ! $this->discount_rules ) { ?>

		<p>
			<?php
			printf(
				esc_html__( 'No discount rules have been defined yet. You can define a discount rule on the %s.', 'hbook-admin' ),
				'<a href="' . esc_url( admin_url( 'admin.php?page=hb_rules' ) ) . '">' .
				esc_html__( 'Booking rules page', 'hbook-admin' ) .
				'</a>'
			);
			?>
		</p>

		<?php } else { ?>

		<!-- ko if: discounts().length == 0 -->
		<p><?php esc_html_e( 'No discount rates have been defined yet.', 'hbook-admin' ); ?></p>
		<!-- /ko -->

		<!-- ko if: discounts().length > 0 -->
		<table class="wp-list-table widefat">

			<thead>
				<tr>
					<th width="16.66%"><?php esc_html_e( 'Discounts', 'hbook-admin' ); ?></th>
					<th width="16.66%"><?php esc_html_e( 'Accommodation', 'hbook-admin' ); ?></th>
					<th width="16.66%"><?php esc_html_e( 'Seasons', 'hbook-admin' ); ?></th>
					<th width="16.66%"><?php esc_html_e( 'Applies on', 'hbook-admin' ); ?></th>
					<th width="16.66%"><?php esc_html_e( 'Amount', 'hbook-admin' ); ?></th>
					<th width="16.66%" class="hb-table-action"><?php esc_html_e( 'Actions', 'hbook-admin' ); ?></th>
				</tr>
			</thead>

			<tbody data-bind="template: { name: discount_template_to_use, foreach: discounts, beforeRemove: hide_setting }">
			</tbody>

		</table>
		<!-- /ko -->

		<!-- ko if: discounts().length > 10 -->
		<br/>
		<a data-bind="click: create_discount" href="#" class="add-new-h2 add-new-below"><?php esc_html_e( 'Add discount rate', 'hbook-admin' ); ?></a>
		<span class="hb-add-new spinner hb-add-discount"></span>
		<br/>
		<!-- /ko -->

		<?php } ?>

		<br/>
		<hr/>

		<h3>
			<?php esc_html_e( 'Coupons', 'hbook-admin' ); ?>
			<a data-bind="click: create_coupon" href="#" class="add-new-h2"><?php esc_html_e( 'Add coupon', 'hbook-admin' ); ?></a>
			<span class="hb-add-new spinner hb-add-coupon"></span>
		</h3>

		<!-- ko if: coupons().length == 0 -->
		<p><?php esc_html_e( 'No coupons have been defined yet.', 'hbook-admin' ); ?></p>
		<!-- /ko -->

		<!-- ko if: coupons().length > 0 -->
		<table class="wp-list-table widefat">

			<thead>
				<tr>
					<th width="11.11%"><?php esc_html_e( 'Code', 'hbook-admin' ); ?></th>
					<th width="11.11%"><?php esc_html_e( 'Accommodation', 'hbook-admin' ); ?></th>
					<th width="11.11%"><?php esc_html_e( 'Seasons', 'hbook-admin' ); ?></th>
					<th width="11.11%"><?php esc_html_e( 'Rule', 'hbook-admin' ); ?></th>
					<th width="11.11%"><?php esc_html_e( 'Use count', 'hbook-admin' ); ?></th>
					<th width="11.11%"><?php esc_html_e( 'Max use', 'hbook-admin' ); ?></th>
					<th width="11.11%"><?php esc_html_e( 'Date limit', 'hbook-admin' ); ?></th>
					<!-- <th width="10%"><?php esc_html_e( 'Multiple use per customer', 'hbook-admin' ); ?></th> -->
					<th width="11.11%"><?php esc_html_e( 'Amount', 'hbook-admin' ); ?></th>
					<th width="11.11%" class="hb-table-action"><?php esc_html_e( 'Actions', 'hbook-admin' ); ?></th>
				</tr>
			</thead>

			<tbody data-bind="template: { name: coupon_template_to_use, foreach: coupons, afterRender: coupon_render, beforeRemove: hide_setting }">
			</tbody>

		</table>
		<!-- /ko -->

		<!-- ko if: coupons().length > 10 -->
		<br/>
		<a data-bind="click: create_coupon" href="#" class="add-new-h2 add-new-below"><?php esc_html_e( 'Add coupon', 'hbook-admin' ); ?></a>
		<span class="hb-add-new spinner hb-add-coupon"></span>
		<!-- /ko -->

		<script id="text_tmpl" type="text/html">
			<tr>
				<td data-bind="text: accom_list"></td>
				<td data-bind="text: seasons_list"></td>
				<?php if ( count( $this->rules ) > 0 ) { ?>
				<td data-bind="text: rules_list"></td>
				<?php } ?>
				<td data-bind="html: amount_text"></td>
				<td class="hb-table-action"><?php $this->display_admin_action(); ?></td>
			</tr>
		</script>

		<script id="discount_text_tmpl" type="text/html">
			<tr>
				<td data-bind="text: rules_list"></td>
				<td data-bind="text: accom_list"></td>
				<td data-bind="text: seasons_list"></td>
				<td data-bind="html: apply_to_type_text"></td>
				<td data-bind="html: amount_text"></td>
				<td class="hb-table-action"><?php $this->display_admin_action(); ?></td>
			</tr>
		</script>

		<script id="coupon_text_tmpl" type="text/html">
			<tr>
				<td data-bind="text: code"></td>
				<td data-bind="text: accom_list"></td>
				<td data-bind="text: seasons_list"></td>
				<td data-bind="text: rule_name"></td>
				<td data-bind="text: use_count"></td>
				<td data-bind="text: max_use_count_text"></td>
				<td data-bind="text: date_limit_text"></td>
				<!-- <td data-bind="text: multiple_use_per_customer_text"></td> -->
				<td data-bind="html: amount_text"></td>
				<td class="hb-table-action"><?php $this->display_admin_action(); ?></td>
			</tr>
		</script>

		<script id="edit_tmpl" type="text/html">
			<tr>
				<td><?php $this->display_checkbox_list( $this->accom, 'accom' ); ?></td>
				<td><?php $this->display_seasons_checkbox_list(); ?></td>
				<?php if ( count( $this->rules ) > 0 ) { ?>
				<td>
				<?php
				$display_check_all_box = false;
				$display_select_all_link = true;
				$this->display_rules_checkbox_list();
				?>
				</td>
				<?php } ?>
				<td>
					<?php if ( get_option( 'hb_currency_position' ) == 'before' ) { ?>
					<span><?php echo( esc_html( $this->utils->get_currency_symbol() ) ); ?></span>
					<?php } ?>
					<input data-bind="value: amount" type="text" class="hb-rate-amount" />
					<?php if ( get_option( 'hb_currency_position' ) == 'after' ) { ?>
					<span><?php echo( esc_html( $this->utils->get_currency_symbol() ) ); ?></span>
					<?php } ?>
					<br/>
					<?php esc_html_e( 'for', 'hbook-admin' ); ?><br/>
					<input data-bind="value: nights" type="text" class="hb-rate-nights" />
					<span><?php esc_html_e( 'night(s)', 'hbook-admin' ); ?></span>
				</td>
				<td class="hb-table-action"><?php $this->display_admin_on_edit_action(); ?></td>
			</tr>
		</script>

		<script id="discount_edit_tmpl" type="text/html">
			<tr>
				<td>
				<?php
				$display_check_all_box = false;
				$display_select_all_link = true;
				$this->display_checkbox_list( $this->discount_rules, 'rules', $display_check_all_box, $display_select_all_link );
				?>
				</td>
				<td><?php $this->display_checkbox_list( $this->accom, 'accom' ); ?></td>
				<td><?php $this->display_seasons_checkbox_list(); ?></td>
				<td>
					<input data-bind="checked: apply_to_type" id="hb-discount-apply-to-type-accom" type="radio" value="accom" />
					<label for="hb-discount-apply-to-type-accom"><?php esc_html_e( 'Accommodation price', 'hbook-admin' ); ?></label>
					<br/>
					<input data-bind="checked: apply_to_type" id="hb-discount-apply-to-type-global" type="radio" value="global" />
					<label for="hb-discount-apply-to-type-global"><?php esc_html_e( 'Global price', 'hbook-admin' ); ?></label>
				</td>
				<td><?php $this->display_edit_rate_amount_fixed_percent(); ?></td>
				<td class="hb-table-action"><?php $this->display_admin_on_edit_action(); ?></td>
			</tr>
		</script>

		<script id="coupon_edit_tmpl" type="text/html">
			<tr>
				<td><input data-bind="value: code" type="text" class="hb-coupon-code" /></td>
				<td><?php $this->display_checkbox_list( $this->accom, 'accom' ); ?></td>
				<td><?php $this->display_seasons_checkbox_list(); ?></td>
				<td>
				<?php if ( $this->coupon_rules ) : ?>
				<input
					type="radio"
					data-bind="checked: rule"
					id="coupon-rule-none"
					name="coupon-rule"
					value=""
				/>
				<label for="coupon-rule-none"><?php esc_html_e( 'None', 'hbook-admin' ); ?></label>
				<br/>
				<?php
				endif;
				foreach ( $this->coupon_rules as $rule_id => $rule_name ) :
					$radio_input_id = 'coupon-rule-' . $rule_id;
					?>
				<input
					type="radio"
					data-bind="checked: rule"
					id="<?php echo( esc_attr( $radio_input_id ) ); ?>"
					name="coupon-rule"
					value="<?php echo( esc_attr( $rule_id ) ); ?>"
				/>
				<label for="<?php echo( esc_attr( $radio_input_id ) ); ?>"><?php echo( esc_html( $rule_name ) ); ?></label>
				<br/>
				<?php endforeach; ?>
				</td>
				<td data-bind="text: use_count"></td>
				<td><input data-bind="value: max_use_count" type="text" /></td>
				<td><input data-bind="value: date_limit" type="text" class="hb-coupon-limit-date" /></td>
				<!--
				<td>
					<input data-bind="checked: multiple_use_per_customer" name="multiple_use_per_customer" id="multiple_use_per_customer_yes" type="radio" value="yes" />
					<label for="multiple_use_per_customer_yes"><?php esc_html_e( 'Yes', 'hbook-admin' ); ?></label><br/>
					<input data-bind="checked: multiple_use_per_customer" name="multiple_use_per_customer" id="multiple_use_per_customer_no" type="radio" value="no" />
					<label for="multiple_use_per_customer_no"><?php esc_html_e( 'No', 'hbook-admin' ); ?></label>
				</td>
				-->
				<td><?php $this->display_edit_rate_amount_fixed_percent(); ?></td>
				<td class="hb-table-action"><?php $this->display_admin_on_edit_action(); ?></td>
			</tr>
		</script>

		<script id="empty_tmpl" type="text/html"></script>

		<?php } ?>

	</div><!-- end .wrap -->

<?php
	}

	private function table_rates_head() {
		if ( count( $this->rules ) > 0 ) {
			$col_width = '20%';
		} else {
			$col_width = '25%';
		}
		?>
			<thead>
				<tr>
					<th width="<?php echo( esc_attr( $col_width ) ); ?>"><?php esc_html_e( 'Accommodation', 'hbook-admin' ); ?></th>
					<th width="<?php echo( esc_attr( $col_width ) ); ?>"><?php esc_html_e( 'Seasons', 'hbook-admin' ); ?></th>
					<?php if ( count( $this->rules ) > 0 ) { ?>
					<th width="<?php echo( esc_attr( $col_width ) ); ?>"><?php esc_html_e( 'Special rates', 'hbook-admin' ); ?></th>
					<?php } ?>
					<th width="<?php echo( esc_attr( $col_width ) ); ?>"><?php esc_html_e( 'Amount', 'hbook-admin' ); ?></th>
					<th width="<?php echo( esc_attr( $col_width ) ); ?>" class="hb-table-action"><?php esc_html_e( 'Actions', 'hbook-admin' ); ?></th>
				</tr>
			</thead>
		<?php
	}

	private function display_seasons_checkbox_list() {
		?>
		<input data-bind="checked: all_seasons" type="checkbox" id="hb-checkbox-seasons-all" />
		<label for="hb-checkbox-seasons-all"><?php esc_html_e( 'All', 'hbook-admin' ); ?></label><br/>
		<?php
		foreach ( $this->seasons as $id => $season ) {
			$tag_id = 'hb-checkbox-seasons-' . $id;
			?>
			<input id="<?php echo( esc_attr( $tag_id ) ); ?>" data-bind="checked: seasons, disable: all_seasons" type="checkbox" value="<?php echo( esc_attr( $id ) ); ?>" />
			<label for="<?php echo( esc_attr( $tag_id ) ); ?>"><?php echo( esc_html( $season ) ); ?></label><br/>
		<?php
		}
		?>
		<a data-bind="click: unselect_all_seasons" href="#"><?php esc_html_e( 'Unselect all', 'hbook-admin' ); ?></a>
		<?php
	}

	private function display_rules_checkbox_list() {
		foreach ( $this->rules as $id => $rule ) {
			$tag_id = 'hb-checkbox-rules-' . $id;
			?>
			<input id="<?php echo( esc_attr( $tag_id ) ); ?>" data-bind="checked: rules, disable: all_rules" type="checkbox" value="<?php echo( esc_html( $id ) ); ?>" />
			<label for="<?php echo( esc_attr( $tag_id ) ); ?>"><?php echo( esc_html( $rule['name'] ) ); ?></label><br/>
		<?php
		}
		?>
		<a data-bind="click: select_all_rules" href="#"><?php esc_html_e( 'Select all', 'hbook-admin' ); ?></a> -
		<a data-bind="click: unselect_all_rules" href="#"><?php esc_html_e( 'Unselect all', 'hbook-admin' ); ?></a>
		<?php
	}

}