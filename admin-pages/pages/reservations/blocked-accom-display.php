<?php
class HbAdminPageReservationsBlockedAccom {

	private $accom;

	public function __construct( $accom ) {
		$this->accom = $accom;
	}

	public function display() {
	?>

		<div class="hb-resa-section">

			<h3 id="hb-block-accom-toggle" class="hb-resa-section-toggle">
				<?php esc_html_e( 'Block accommodation', 'hbook-admin' ); ?>
				<span class="dashicons dashicons-arrow-up"></span>
				<span class="dashicons dashicons-arrow-down"></span>
			</h3>

			<div id="hb-block-accom">

				<div class="stuffbox">
					<form data-bind="submit: add_blocked_accom">
						<p class="hb-add-blocked-accom-column">
							<label for="hb-select-blocked-accom-type"><?php esc_html_e( 'Accommodation type', 'hbook-admin' ); ?></label><br/>
							<select id="hb-select-blocked-accom-type">
								<option value="all"><?php esc_html_e( 'All', 'hbook-admin' ); ?></option>
								<?php foreach ( $this->accom as $accom_id => $accom_name ) { ?>
								<option value="<?php echo( esc_attr( $accom_id ) ); ?>"><?php echo( esc_html( $accom_name ) ); ?></option>
								<?php } ?>
							</select>
						</p>
						<p class="hb-add-blocked-accom-column">
							<label for="hb-select-blocked-accom-num"><?php esc_html_e( 'Accommodation number', 'hbook-admin' ); ?></label><br/>
							<select id="hb-select-blocked-accom-num"></select>
						</p>
						<p  class="hb-add-blocked-accom-column">
							<label for="hb-block-accom-from-date"><?php esc_html_e( 'Start date', 'hbook-admin' ); ?></label><br/>
							<input id="hb-block-accom-from-date" class="hb-input-date hb-block-accom-date" type="text" autocomplete="off" />
						</p>
						<p  class="hb-add-blocked-accom-column hb-add-blocked-accom-column-last">
							<label for="hb-block-accom-to-date"><?php esc_html_e( 'End date', 'hbook-admin' ); ?></label><br/>
							<input id="hb-block-accom-to-date" class="hb-input-date hb-block-accom-date" type="text" autocomplete="off" />
						</p>
						<p class="hb-add-blocked-accom-comment">
							<label for="hb-block-accom-comment"><?php esc_html_e( 'Comment', 'hbook-admin' ); ?></label><br/>
							<textarea id="hb-block-accom-comment" class="widefat" rows="5"></textarea>
						</p>
						<p class="hb-add-blocked-accom-submit">
							<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Block', 'hbook-admin' ); ?>" />
							<span class="hb-ajaxing">
								<span class="spinner"></span>
							</span>
						</p>
					</form>
				</div>

				<!-- ko if: blocked_accom().length == 0 -->
				<?php esc_html_e( 'There are no blocked accommodation at the moment', 'hbook-admin' ); ?>
				<!-- /ko -->

				<!-- ko if: blocked_accom().length > 0 -->
				<h4 class="hb-block-accom-title"><?php esc_html_e( 'Blocked accommodation', 'hbook-admin' ); ?></h4>

				<div class="hb-table hb-blocked-accom-table">

					<div class="hb-table-head hb-clearfix">
						<div class="hb-table-head-data hb-table-head-data-accom"><?php esc_html_e( 'Accommodation', 'hbook-admin' ); ?></div>
						<div class="hb-table-head-data"><?php esc_html_e( 'Start date', 'hbook-admin' ); ?></div>
						<div class="hb-table-head-data"><?php esc_html_e( 'End date', 'hbook-admin' ); ?></div>
						<div class="hb-table-head-data hb-table-head-data-comment"><?php esc_html_e( 'Comment', 'hbook-admin' ); ?></div>
						<div class="hb-table-head-data hb-table-head-data-action"><?php esc_html_e( 'Actions', 'hbook-admin' ); ?></div>
					</div>

					<!-- ko foreach: blocked_accom -->
					<div class="hb-table-row hb-clearfix">
						<div data-bind="attr: { class: anim_class }">
							<div class="hb-table-data hb-table-data-accom" data-bind="html: accom_name_num"></div>
							<div class="hb-table-data" data-bind="text: from_date_display"></div>
							<div class="hb-table-data" data-bind="text: to_date_display"></div>
							<div class="hb-table-data hb-table-data-comment" data-bind="text: comment"></div>
							<div class="hb-table-data hb-table-data-action">
								<a href="#" title="<?php esc_attr_e( 'Delete', 'hbook-admin' ); ?>" class="dashicons dashicons-trash" data-bind="visible: ! deleting(), click: $root.delete_blocked_accom"></a>
								<span data-bind="visible: deleting" class="hb-ajaxing hb-resa-updating">
									<span class="spinner"></span>
									<span><?php esc_html_e( 'Deleting...', 'hbook-admin' ); ?></span>
								</span>
							</div>
						</div>
					</div>
					<!-- /ko -->

				</div>
				<!-- /ko -->

			</div>

		</div><!-- end .hb-resa-section -->

		<hr/>

	<?php
	}

}