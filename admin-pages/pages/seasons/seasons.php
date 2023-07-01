<?php
class HbAdminPageSeasons extends HbAdminPage {

	public function __construct( $page_id, $hbdb, $utils, $options_utils ) {
		$this->data = array(
			'hb_text' => array(
				'new_season' => esc_html__( 'New season', 'hbook-admin' ),
				'no_days_selected' => esc_html__( 'No days selected', 'hbook-admin' ),
			),
			'days_short_name' => $utils->days_short_name(),
			'seasons' => $hbdb->get_all_seasons_with_dates()
		);
		parent::__construct( $page_id, $hbdb, $utils, $options_utils );
	}

	public function display() {
	?>

	<div class="wrap">

		<h2>
			<?php esc_html_e( 'Seasons', 'hbook-admin' ); ?>
			<a href="#" class="add-new-h2" data-bind="click: create_season"><?php esc_html_e( 'Add new season', 'hbook-admin' ); ?></a>
			<span class="hb-add-new spinner"></span>
		</h2>

		<?php $this->display_right_menu(); ?>

		<br/>

		<!-- ko if: seasons().length == 0 -->
		<?php esc_html_e( 'No seasons set yet.', 'hbook-admin' ); ?>
		<!-- /ko -->

		<!-- ko if: seasons().length > 0 -->
		<div class="hb-table hb-season-table">

			<div class="hb-table-head hb-clearfix">
				<div class="hb-table-head-data"><?php esc_html_e( 'Season', 'hbook-admin' ); ?></div>
				<div class="hb-table-head-data"><?php esc_html_e( 'Start date', 'hbook-admin' ); ?></div>
				<div class="hb-table-head-data"><?php esc_html_e( 'End date', 'hbook-admin' ); ?></div>
				<div class="hb-table-head-data"><?php esc_html_e( 'Days', 'hbook-admin' ); ?></div>
				<div class="hb-table-head-data hb-table-head-data-action"><?php esc_html_e( 'Actions', 'hbook-admin' ); ?></div>
			</div>
			<div data-bind="template: { name: template_to_use, foreach: seasons, as: 'season', afterRender: season_render, beforeRemove: hide_setting }"></div>

			<script id="text_tmpl" type="text/html">
				<div class="hb-table-row hb-clearfix">
					<div class="hb-table-data">
						<span data-bind="text: name"></span>
						<!-- ko if: priority() == 'high' -->
						<span class="hb-season-priority hb-season-high-priority"><?php esc_html_e( 'Priority', 'hbook-admin' ); ?></span>
						<!-- /ko -->
						<!-- ko if: priority() == 'low' -->
						<span class="hb-season-priority hb-season-default"><?php esc_html_e( 'Default', 'hbook-admin' ); ?></span>
						<!-- /ko -->
					</div>
					<div class="hb-table-data"></div>
					<div class="hb-table-data"></div>
					<div class="hb-table-data"></div>
					<div class="hb-table-data hb-table-data-action"><?php $this->display_admin_action( 'season' ); ?></div>
				</div>
				<div data-bind="template: { name: $parent.child_template_to_use, foreach: dates, beforeRemove: $parent.hide_setting }"></div>
			</script>

			<script id="edit_tmpl" type="text/html">
				<div class="hb-table-row hb-clearfix">
					<div class="hb-table-data">
						<p class="hb-season-name">
							<?php esc_html_e( 'Name:', 'hbook-admin' ); ?><br/>
							<input data-bind="value: name" type="text" />
						</p>
						<p>
							<input id="hb-priority-season" type="radio" data-bind="checked: priority" name="season_priority" value="high" />
							<label for="hb-priority-season"><?php esc_html_e( 'Priority season', 'hbook-admin' ); ?></label><br/>
							<input id="hb-regular-season" type="radio" data-bind="checked: priority" name="season_priority" value="" />
							<label for="hb-regular-season"><?php esc_html_e( 'Regular season', 'hbook-admin' ); ?></label><br/>
							<input id="hb-default-season" type="radio" data-bind="checked: priority" name="season_priority" value="low" />
							<label for="hb-default-season"><?php esc_html_e( 'Default season', 'hbook-admin' ); ?></label>
						</p>
					</div>
					<div class="hb-table-data"></div>
					<div class="hb-table-data"></div>
					<div class="hb-table-data"></div>
					<div class="hb-table-data hb-table-data-action"><?php $this->display_admin_on_edit_action( 'season' ); ?></div>
				</div>
				<div data-bind="template: { name: $parent.child_template_to_use, foreach: dates, beforeRemove: $parent.hide_setting }"></div>
			</script>

			<script id="child_text_tmpl" type="text/html">
				<div class="hb-season-dates-row hb-clearfix">
					<div class="hb-table-data"></div>
					<div class="hb-table-data" data-bind="text: start_date_text"></div>
					<div class="hb-table-data" data-bind="text: end_date_text"></div>
					<div class="hb-table-data" data-bind="text: days_list"></div>
					<div class="hb-table-data hb-table-data-action"><?php $this->display_admin_action( 'season_dates' ); ?></div>
				</div>
			</script>

			<script id="child_edit_tmpl" type="text/html">
				<div class="hb-season-dates-row hb-clearfix">
					<div class="hb-table-data"></div>
					<div class="hb-table-data"><input data-bind="value: start_date" class="hb-season-date hb-season-date-start" type="text" /></div>
					<div class="hb-table-data"><input data-bind="value: end_date" class="hb-season-date hb-season-date-end" type="text" /></div>
					<div class="hb-table-data"><?php $this->display_select_days( 'days' ); ?></div>
					<div class="hb-table-data hb-table-data-action"><?php $this->display_admin_on_edit_action( 'season_dates' ); ?></div>
				</div>
			</script>

		</div>
		<!-- /ko -->

		<!-- ko if: nb_rows() > 10 -->
		<br/>
		<a data-bind="click: create_season" href="#" class="add-new-h2 add-new-below"><?php esc_html_e( 'Add new season', 'hbook-admin' ); ?></a>
		<span class="hb-add-new spinner"></span>
		<!-- /ko -->

	</div><!-- end .wrap -->

	<?php
	}
}