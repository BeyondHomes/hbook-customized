<?php
class HbAdminPageEmails extends HbAdminPage {

	private $accom;
	private $email_actions;
	private $resa_status;
	private $resa_payment_status;
	private $email_tmpl_names;

	public function __construct( $page_id, $hbdb, $utils, $options_utils ) {
		$langs = $utils->get_langs();
		$hb_email_langs = array();
		foreach ( $langs as $locale => $lang_name ) {
			$email_lang = array(
				'lang_value' => $locale,
				'lang_name' => $lang_name
			);
			$hb_email_langs[] = $email_lang;
		}
		$hb_email_langs[] = array(
			'lang_value' => 'all',
			'lang_name' => esc_html__( 'All', 'hbook-admin' )
		);
		$email_tmpls = $hbdb->get_all_email_templates();
		$this->email_tmpl_names = array();
		$media_titles_list = array();
		foreach ( $email_tmpls as $email_tmpl ) {
			$this->email_tmpl_names[ $email_tmpl['id'] ] = $email_tmpl['name'];
			if ( $email_tmpl['media_attachments'] ) {
				$media_attachments = explode( ',', $email_tmpl['media_attachments'] );
				$media_titles = array();
				foreach ( $media_attachments as $media_id ) {
					$media_title = get_the_title( $media_id );
					if ( $media_title ) {
						$media_titles[] = $media_title;
					}
				}
				$media_titles_list[ $email_tmpl['media_attachments'] ] = implode( ', ', $media_titles );
			}
		}
		$this->email_actions = apply_filters(
			'hb_email_actions',
			array(
				array(
					'action_value' => 'new_resa',
					'action_text' =>
						esc_html__( 'New reservation', 'hbook-admin' ) .
						' (' .
						esc_html__( 'from customers', 'hbook-admin' ) .
						')'
				),
				array(
					'action_value' => 'new_resa_admin',
					'action_text' =>
					esc_html__( 'New reservation', 'hbook-admin' ) .
						' (' .
						esc_html__( 'from admin', 'hbook-admin' ) .
						')'
				),
				array(
					'action_value' => 'confirmation_resa',
					'action_text' => esc_html__( 'Reservation confirmation', 'hbook-admin' ),
				),
				array(
					'action_value' => 'cancellation_resa',
					'action_text' => esc_html__( 'Reservation cancellation', 'hbook-admin' ),
				),
			)
		);
		$this->resa_status = array(
			'new' => esc_html__( 'New', 'hbook-admin' ),
			'pending' => esc_html__( 'Pending', 'hbook-admin' ),
			'confirmed' => esc_html__( 'Confirmed', 'hbook-admin' ),
		);
		$this->resa_payment_status = array(
			'paid' => esc_html__( 'Paid', 'hbook-admin' ),
			'not_fully_paid' => esc_html__( 'Not fully paid', 'hbook-admin' ),
		);
		if ( get_option( 'hb_security_bond_online_payment' ) == 'yes' ) {
			$this->resa_payment_status['bond_not_paid'] = esc_html__( 'Bond not paid', 'hbook-admin' );
		}
		$this->resa_payment_status['unpaid'] = esc_html__( 'Unpaid', 'hbook-admin' );
		$this->accom = $hbdb->get_all_accom();
		$this->data = array(
			'hb_text' => array(
				'new_email_tmpl' => esc_html__( 'New email template', 'hbook-admin' ),
				'invalid_email_address' => esc_html__( 'This e-mail address does not seem valid.', 'hbook-admin' ),
				'invalid_multiple_address' => esc_html__( 'Please seperate multiple e-mail addresses with commas.', 'hbook-admin' ),
				'invalid_complete_address' => esc_html__( 'Please use a complete e-mail address eg. Your Name <email@domain.com>', 'hbook-admin' ),
				'select_attachments' => esc_html__( 'Select attachments', 'hbook-admin' ),
				'remove_all_attachments' => esc_html__( 'Remove all attachments?', 'hbook-admin' ),
				'message' => esc_html__( 'Message:', 'hbook-admin' ),
				'format' => esc_html__( 'Format:', 'hbook-admin' ),
				'attachments' => esc_html__( 'Attachments:', 'hbook-admin' ),
				'sending_type_event' => esc_html__( 'Upon event', 'hbook-admin' ),
				'sending_type_scheduled' => esc_html__( 'Scheduled', 'hbook-admin' ),
				'sending_type_manual' => esc_html__( 'Manually', 'hbook-admin' ),
				'schedule_day' => esc_html__( 'day', 'hbook-admin' ),
				'schedule_days' => esc_html__( 'days', 'hbook-admin' ),
				'schedule_before' => esc_html__( 'before', 'hbook-admin' ),
				'schedule_after' => esc_html__( 'after', 'hbook-admin' ),
				'schedule_in' => esc_html__( 'check-in', 'hbook-admin' ),
				'schedule_out' => esc_html__( 'check-out', 'hbook-admin' ),
				'schedule_check_in_day' => esc_html__( 'on check-in day', 'hbook-admin' ),
				'schedule_check_out_day' => esc_html__( 'on check-out day', 'hbook-admin' ),
				'schedule_invalid_days_number' => esc_html__( 'The number of days is not valid.', 'hbook-admin' ),
				'schedule_already_exists' => esc_html__( 'This schedule already exists.', 'hbook-admin' ),
				'delete_schedule' => esc_html__( 'Delete schedule?', 'hbook-admin' ),
				'email_never_sent_no_actions' => esc_html__( 'Select at least one Event.', 'hbook-admin' ),
				'email_never_sent_no_resa_status' => esc_html__( 'Select at least one Reservation status.', 'hbook-admin' ),
				'email_never_sent_no_resa_payment_status' => esc_html__( 'Select at least one Reservation payment status.', 'hbook-admin' ),
				'email_never_sent_no_accom' => esc_html__( 'Select at least one Accommodation or Multiple accommodation booking.', 'hbook-admin' ),
				'confirm_delete_email_logs' => esc_html__( 'Delete email logs?', 'hbook-admin' ),
			),
			'email_tmpls' => $email_tmpls,
			'hb_media_titles' => $media_titles_list,
			'hb_email_langs' => $hb_email_langs,
			'hb_email_actions' => $this->email_actions,
			'hb_resa_status' => $this->resa_status,
			'hb_resa_payment_status' => $this->resa_payment_status,
			'accom_list' => $this->accom,
		);
		parent::__construct( $page_id, $hbdb, $utils, $options_utils );
	}

	private function logs_reset() {
		if (
			isset( $_POST['hb-delete-email-logs'] ) &&
			wp_verify_nonce( $_POST['hb_delete_email_logs'], 'hb_delete_email_logs' ) &&
			current_user_can( 'manage_hbook' )
		) {
			if ( $this->hbdb->delete_email_logs() ) {
			?>

				<div class="notice notice-success">
					<p><?php esc_html_e( 'Email logs have been deleted.', 'hbook-admin' ); ?></p>
				</div>

			<?php } else { ?>

				<div class="error">
					<p><?php esc_html_e( 'An error occured. Email logs could not be deleted.', 'hbook-admin' ); ?></p>
				</div>

			<?php
			}
		}
	}

	public function display() {
	?>

	<div class="wrap">

		<h2>
			<?php esc_html_e( 'Email templates', 'hbook-admin' ); ?>
			<a href="#" class="add-new-h2" data-bind="click: create_email_tmpl"><?php esc_html_e( 'Add new email template', 'hbook-admin' ); ?></a>
			<span class="hb-add-new spinner"></span>
		</h2>

		<?php $this->display_right_menu(); ?>

		<br/>

		<?php $this->logs_reset(); ?>

		<!-- ko if: email_tmpls().length == 0 -->
		<?php esc_html_e( 'No email templates set yet.', 'hbook-admin' ); ?>
		<!-- /ko -->

		<!-- ko if: email_tmpls().length > 0 -->
		<?php
		$table_class = 'hb-table hb-email-tmpls-table';
		if ( $this->utils->is_site_multi_lang() ) {
			$table_class .= ' hb-email-multiple-lang';
		}
		?>

		<div class="<?php echo( esc_attr( $table_class ) ); ?>">

			<div class="hb-table-head hb-clearfix">
				<div class="hb-table-head-data"><?php esc_html_e( 'Name', 'hbook-admin' ); ?></div>
				<div class="hb-table-head-data hb-data-addresses"><?php esc_html_e( 'Addresses', 'hbook-admin' ); ?></div>
				<div class="hb-table-head-data"><?php esc_html_e( 'Subject', 'hbook-admin' ); ?></div>
				<div class="hb-table-head-data hb-data-message"><?php esc_html_e( 'Message', 'hbook-admin' ); ?></div>
				<div class="hb-table-head-data hb-data-sending"><?php esc_html_e( 'Sending', 'hbook-admin' ); ?></div>
				<div class="hb-table-head-data hb-table-head-data-action"><?php esc_html_e( 'Actions', 'hbook-admin' ); ?></div>
			</div>
			<div data-bind="template: { name: template_to_use, foreach: email_tmpls, beforeRemove: hide_setting }"></div>

			<script id="text_tmpl" type="text/html">
				<div class="hb-table-row hb-clearfix">
					<div class="hb-table-data" data-bind="text: name"></div>
					<div class="hb-table-data hb-data-addresses">
						<span class="hb-data-addresses-type-name"><?php esc_html_e( 'To:', 'hbook-admin' ); ?></span> <span data-bind="html: to_address_html"></span><br/>
						<span class="hb-data-addresses-type-name"><?php esc_html_e( 'Reply-to:', 'hbook-admin' ); ?></span> <span data-bind="html: reply_to_address_html"></span><br/>
						<span class="hb-data-addresses-type-name"><?php esc_html_e( 'From:', 'hbook-admin' ); ?></span> <span data-bind="html: from_address_html"></span><br/>
						<span class="hb-data-addresses-type-name"><?php esc_html_e( 'Bcc:', 'hbook-admin' ); ?></span> <span data-bind="html: bcc_address_html"></span>
					</div>
					<div class="hb-table-data" data-bind="text: subject"></div>
					<div class="hb-table-data hb-data-message" data-bind="html: message_html"></div>
					<div class="hb-table-data hb-data-sending">
						<p class="hb-template-email-manual" data-bind="visible: sending_type() == 'manual'"><?php esc_html_e( 'Manually', 'hbook-admin' ); ?></p>
						<p class="hb-email-actions" data-bind="visible: sending_type() == 'event', html: actions_text"></p>
						<p data-bind="visible: ( sending_type() == 'scheduled' ) && ( schedules().length == 0 )">
							<b class="hb-template-email-inactive-reason">
								<?php esc_html_e( 'Add at least one Schedule', 'hbook-admin' ); ?>
							</b>
						</p>
						<ul class="hb-email-schedules" data-bind="visible: sending_type() == 'scheduled', foreach: schedules">
							<li>
								&bull; <span data-bind="text: $parent.schedule_text( $data )"></span>
							</li>
						</ul>
						<p data-bind="visible: sending_type() == 'scheduled'">
							<b><?php esc_html_e( 'For reservation status:', 'hbook-admin' ); ?></b><br/>
							<span data-bind="html: resa_status_text"></span>
						</p>
						<p data-bind="visible: sending_type() == 'event' || sending_type() == 'scheduled'">
							<b><?php esc_html_e( 'For reservation payment status:', 'hbook-admin' ); ?></b><br/>
							<span data-bind="html: resa_payment_status_text"></span>
						</p>
						<p>
							<b><?php esc_html_e( 'For accommodation:', 'hbook-admin' ); ?></b><br/>
							<span data-bind="html: accom_list_for_email"></span>
							<?php if ( get_option( 'hb_multiple_accom_booking' ) == 'enabled' ) { ?>
							<span class="hb-multiple-accom-text" data-bind="visible: multiple_accom">
								<?php esc_html_e( 'Multiple accommodation booking', 'hbook-admin' ); ?>
							</span>
							<?php } ?>
						</p>
						<p class="hb-email-lang">
							<b><?php esc_html_e( 'For language:', 'hbook-admin' ); ?></b><br/>
							<span data-bind="text: lang_text"></span>
						</p>
					</div>
					<div class="hb-table-data hb-table-data-action"><?php $this->display_admin_action(); ?></div>
				</div>
			</script>

			<script id="edit_tmpl" type="text/html">
				<div class="hb-table-row hb-clearfix">
					<div class="hb-table-data"><input data-bind="value: name" type="text" /></div>
					<div class="hb-table-data hb-data-addresses">
						<label for="to-address"><?php esc_html_e( 'To:', 'hbook-admin' ); ?>&nbsp;&nbsp;</label>
						<input id="to-address" data-bind="value: to_address" type="text" /><br/>
						<label for="reply-to-address"><?php esc_html_e( 'Reply-to:', 'hbook-admin' ); ?>&nbsp;&nbsp;</label>
						<input id="reply-to-address" data-bind="value: reply_to_address" type="text" /><br/>
						<label for="from-address"><?php esc_html_e( 'From:', 'hbook-admin' ); ?>&nbsp;&nbsp;</label>
						<input id="from-address"data-bind="value: from_address" type="text" />
						<label for="bcc-address"><?php esc_html_e( 'Bcc:', 'hbook-admin' ); ?>&nbsp;&nbsp;</label>
						<input id="bcc-address"data-bind="value: bcc_address" type="text" />
					</div>
					<div class="hb-table-data"><input data-bind="value: subject" type="text" /></div>
					<div class="hb-table-data hb-data-message">
						<label for="email-message"><?php esc_html_e( 'Message:', 'hbook-admin' ); ?></label>
						<textarea id="email-message" class="hb-template-email-message" data-bind="value: message" /></textarea>
						<p>
							<?php esc_html_e( 'Format:', 'hbook-admin' ); ?><br/>
							<input data-bind="checked: format" name="format" id="format_text" type="radio" value="TEXT" />
							<label for="format_text"><?php esc_html_e( 'TEXT', 'hbook-admin' ); ?></label>
							&nbsp;&nbsp;
							<input data-bind="checked: format" name="format" id="format_html" type="radio" value="HTML" />
							<label for="format_html"><?php esc_html_e( 'HTML', 'hbook-admin' ); ?></label>
						</p>
						<p class="hb-add-attachment">
							<?php esc_html_e( 'Attachments:', 'hbook-admin' ); ?><br/>
							<span data-bind="text: media_attachments_list"></span>
							<a href="#" class="hb-add-attachment-link"><?php esc_html_e( 'Select', 'hbook-admin' ); ?></a>
							<a href="#" data-bind="visible: media_attachments() != '', click: remove_media_attachment" class="hb-remove-attachment-link"><?php esc_html_e( 'Remove all', 'hbook-admin' ); ?></a>
							<input data-bind="value: media_attachments" type="hidden" />
						</p>
					</div>
					<div class="hb-table-data hb-data-sending">
						<p class="hb-email-sending-type-edit">
							<input data-bind="checked: sending_type" name="sending_type" id="sending_type_event" type="radio" value="event" />
							<label for="sending_type_event"><?php esc_html_e( 'Upon event', 'hbook-admin' ); ?></label><br/>
							<input data-bind="checked: sending_type" name="sending_type" id="sending_type_scheduled" type="radio" value="scheduled" />
							<label for="sending_type_scheduled"><?php esc_html_e( 'Scheduled', 'hbook-admin' ); ?></label><br/>
							<input data-bind="checked: sending_type" name="sending_type" id="sending_type_manual" type="radio" value="manual" />
							<label for="sending_type_manual"><?php esc_html_e( 'Manually', 'hbook-admin' ); ?></label><br/>
						</p>

						<p data-bind="visible: sending_type() == 'event'">
							<?php
							$email_actions_id_name = array();
							foreach ( $this->email_actions as $action ) {
								$email_actions_id_name[ $action['action_value'] ] = $action['action_text'];
							}
							$this->display_checkbox_list( $email_actions_id_name, 'action', false, false, false ); ?>
						</p>

						<div data-bind="visible: sending_type() == 'scheduled'">
							<ul class="hb-email-schedules" data-bind="foreach: schedules">
								<li>
									&bull; <span data-bind="text: $parent.schedule_text( $data )"></span>
									<a
										href="#"
										class="dashicons dashicons-edit"
										data-bind="click: function( data, event ) { $parent.edit_schedule( data, event, $index() ) }">
									</a>
									<a
										href="#"
										class="dashicons dashicons-trash"
										data-bind="click: function( data, event ) { $parent.delete_schedule( data, event, $index() ) }">
									</a>
								</li>
							</ul>

							<a
								href="#"
								title="<?php esc_html_e( 'Add schedule', 'hbook-admin' ); ?>"
								class="dashicons dashicons-plus"
								data-bind="visible: ( editing_schedule() === '' ) && ( schedules().length > 0 ), click: add_schedule">
							</a>

							<p data-bind="visible: ( editing_schedule() !== '' ) || ( schedules().length == 0 )" class="hb-edit-schedule-wrapper">
								<input data-bind="value: edit_schedule_days" type="text" class="hb-email-schedule-days" />
								<?php esc_html_e( 'days', 'hbook-admin' ); ?><br/>

								<input data-bind="checked: edit_schedule_position" name="edit_schedule_position" id="edit_schedule_position_before" type="radio" value="before" />
								<label for="edit_schedule_position_before"><?php esc_html_e( 'before', 'hbook-admin' ); ?></label>
								&nbsp;&nbsp;
								<input data-bind="checked: edit_schedule_position" name="edit_schedule_position" id="edit_schedule_position_after" type="radio" value="after" />
								<label for="edit_schedule_position_after"><?php esc_html_e( 'after', 'hbook-admin' ); ?></label><br/>

								<input data-bind="checked: edit_schedule_check_in_out" name="edit_schedule_check_in_out" id="edit_schedule_check_in_out_in" type="radio" value="in" />
								<label for="edit_schedule_check_in_out_in"><?php esc_html_e( 'check-in', 'hbook-admin' ); ?></label>
								&nbsp;&nbsp;
								<input data-bind="checked: edit_schedule_check_in_out" name="edit_schedule_check_in_out" id="edit_schedule_check_in_out_out" type="radio" value="out" />
								<label for="edit_schedule_check_in_out_out"><?php esc_html_e( 'check-out', 'hbook-admin' ); ?></label><br/>

								<span data-bind="visible: ( edit_schedule_check_in_out() == 'in' ) && ( edit_schedule_position() == 'after' )">
									<input id="scheduled_after_in_allow_after_out" type="checkbox" data-bind="checked: edit_schedule_only_before_out" />
									<label for="scheduled_after_in_allow_after_out"><?php esc_html_e( 'Only if before check-out date', 'hbook-admin' ); ?></label><br/>
								</span>

								<span data-bind="visible: ( edit_schedule_check_in_out() == 'out' ) && ( edit_schedule_position() == 'before' )">
									<input id="scheduled_before_out_allow_before_in" type="checkbox" data-bind="checked: edit_schedule_only_after_in" />
									<label for="scheduled_before_out_allow_before_in"><?php esc_html_e( 'Only if after check-in date', 'hbook-admin' ); ?></label><br/>
								</span>

								<a href="#" class="button-primary" data-bind="visible: ( schedules().length == 0 ) || ( editing_schedule() == 'adding' ), click: confirm_edit_schedule"><?php esc_html_e( 'Add', 'hbook-admin' ); ?></a>
								<a href="#" class="button-primary" data-bind="visible: ( schedules().length > 0 ) && ( editing_schedule() != 'adding' ), click: confirm_edit_schedule"><?php esc_html_e( 'OK', 'hbook-admin' ); ?></a>
								<!-- ko if: schedules().length > 0 -->
								<a href="#" class="button" data-bind="click: cancel_edit_schedule"><?php esc_html_e( 'Cancel', 'hbook-admin' ); ?></a>
								<!-- /ko -->
							</p>

							<p>
								<label><?php esc_html_e( 'For reservation status:', 'hbook-admin' ); ?></label><br/>
								<?php $this->display_checkbox_list( $this->resa_status, 'resa_status', false, true, true ); ?>
							</p>
						</div>

						<p data-bind="visible: sending_type() == 'event' || sending_type() == 'scheduled'">
							<label><?php esc_html_e( 'For reservation payment status:', 'hbook-admin' ); ?></label><br/>
							<?php  $this->display_checkbox_list( $this->resa_payment_status, 'resa_payment_status', false, true, true ); ?>
						</p>

						<p>
							<label><?php esc_html_e( 'For accommodation:', 'hbook-admin' ); ?></label><br/>
							<?php $this->display_checkbox_list( $this->accom, 'accom' ); ?>
						</p>
						<?php if ( get_option( 'hb_multiple_accom_booking' ) == 'enabled' ) { ?>
						<p>
							<input id="hb-multiple-accom" type="checkbox" data-bind="checked: multiple_accom">
							<label for="hb-multiple-accom"><?php esc_html_e( 'Multiple accommodation booking', 'hbook-admin' ); ?></label>
						</p>
						<?php } ?>
						<p class="hb-email-lang">
							<label for="hb-email-lang-select"><?php esc_html_e( 'For language:', 'hbook-admin' ); ?></label><br/>
							<select id="hb-email-lang-select" data-bind="options: hb_email_langs, optionsValue: 'lang_value', optionsText: 'lang_name', value: lang">
							</select>
						</p>
					</div>
					<div class="hb-table-data hb-table-data-action"><?php $this->display_admin_on_edit_action(); ?></div>
				</div>
			</script>

		</div>

		<!-- ko if: email_tmpls().length > 5 -->
		<br/>
		<a href="#" class="add-new-h2 add-new-below" data-bind="click: create_email_tmpl"><?php esc_html_e( 'Add new email template', 'hbook-admin' ); ?></a>
		<span class="hb-add-new spinner"></span>
		<!-- /ko -->

		<h4><?php esc_html_e( '"To address", "Reply-To address", "From address", "Bcc address", "Subject" and "Message" fields:', 'hbook-admin' ); ?></h4>
		<p>
			<?php esc_html_e( 'You can use the following variables:', 'hbook-admin' ); ?><br/>
			<?php echo( esc_html( $this->utils->get_ical_email_document_available_vars() ) ); ?>
		</p>

		<h4><?php esc_html_e( '"To address" field:', 'hbook-admin' ); ?></h4>
		<p>
			<?php
			if ( get_option( 'hb_email_default_address') ) {
				printf(
					esc_html__( 'If the field is blank %s will be used.', 'hbook-admin' ),
					'<b>' . esc_html( get_option( 'hb_email_default_address' ) ) . '</b>'
				);
			} else {
				printf(
					esc_html__( 'If the field is blank the email address of the WordPress administrator (%s) will be used.', 'hbook-admin' ),
					'<b>' . esc_html( get_option( 'admin_email' ) ) . '</b>'
				);
			}
			?>
		</p>
		<p>
			<?php
			printf(
				esc_html__( 'Separate multiple e-mail addresses with commas, for example: %s', 'hbook-admin' ),
				'<b>' . esc_html__( 'email-1@domain.com,email-2@domain.com', 'hbook-admin' )  . '</b>'
			);
			?>
			<b><?php ; ?></b>
		</p>

		<h4><?php esc_html_e( '"Reply-To address", "From address" fields:', 'hbook-admin' ); ?></h4>
		<p>
			<?php
			printf(
				esc_html__( 'Insert a complete e-mail address (a name followed by an email address wrapped between %s and %s, for example: %s', 'hbook-admin' ),
				'<b style="font-weight:900; font-size: 15px">&lt;</b>',
				'<b style="font-weight:900; font-size: 15px">&gt;</b>',
				'<b>' . esc_html__( 'Your Name <your.email@domain.com>', 'hbook-admin' )  . '</b>'
			);
			?>
		</p>

		<h4><?php esc_html_e( '"From address" fields:', 'hbook-admin' ); ?></h4>
		<p>
			<?php
			printf(
				esc_html__( 'If the field is blank %s will be used.', 'hbook-admin' ),
				'<b>' . esc_html( $this->utils->get_default_from_address() ) . '</b>'
			);
			?>
		</p>

		<!-- /ko -->

	</div>

		<?php
		$logs = $this->hbdb->get_email_logs();
		if ( $logs ) {
		?>

		<div class="wrap"><br/><hr/></div>

		<div class="wrap">
			<h2><?php esc_html_e( 'Email logs', 'hbook-admin' ); ?></h2>
			<br/>

			<div class="hb-table hb-email-logs-table">

				<div class="hb-table-head hb-clearfix">
					<div class="hb-table-head-data"><?php esc_html_e( 'Sent on', 'hbook-admin' ); ?></div>
					<div class="hb-table-head-data hb-data-resa-id"><?php esc_html_e( 'Resa id', 'hbook-admin' ); ?></div>
					<div class="hb-table-head-data"><?php esc_html_e( 'Template', 'hbook-admin' ); ?></div>
					<div class="hb-table-head-data"><?php esc_html_e( 'Trigger', 'hbook-admin' ); ?></div>
					<div class="hb-table-head-data"><?php esc_html_e( 'Status', 'hbook-admin' ); ?></div>
				</div>

				<?php
				$logs = $this->hbdb->get_email_logs();
				foreach ( $logs as $log ) {
				?>

				<div class="hb-table-row hb-clearfix">
					<div class="hb-table-data"><?php echo( esc_html( $this->utils->get_blog_datetime( $log['sent_on'] ) ) ); ?></div>
					<div class="hb-table-data hb-data-resa-id">
						<?php if ( $log['resa_is_parent'] ) { echo( '#' ); } ?><?php echo( esc_html( $log['resa_id'] ) ); ?>
					</div>
					<div class="hb-table-data">
						<?php
						if ( $log['template_id'] && isset( $this->email_tmpl_names[ $log['template_id'] ] ) ) {
							echo( esc_html( $this->email_tmpl_names[ $log['template_id'] ] ) );
						}
						?>
					</div>
					<div class="hb-table-data">
						<?php echo( esc_html( $this->utils->get_email_log_trigger_txt( $log['trigger_by'], $log['trigger_by_details'] ) ) ); ?>
					</div>
					<div class="hb-table-data">
						<?php
						if ( ! $log['error_msg'] ) {
							esc_html_e( 'OK', 'hbook-admin' );
						} else {
							esc_html_e( 'Failed', 'hbook-admin' );
							echo( ' - ' );
							echo( esc_html__( $log['error_msg'] ) );
							echo( '<div class="hb-email-wp-error">' );
							echo( esc_html__( $log['wp_error'] ) );
							echo( '</div>' );
						}
						?>
					</div>

				</div>

				<?php } ?>

			</div>

			<?php if ( $logs ) { ?>

			<p>
				<a id="hb-delete-email-logs-submit" href="#"><?php esc_html_e( 'Delete email logs', 'hbook-admin' ); ?></a>
			</p>
			<form id="hb-delete-email-logs-form" action="<?php echo( esc_url( admin_url( 'admin.php?page=hb_emails' ) ) ); ?>" method="POST">
				<input type="hidden" name="hb-delete-email-logs" value="delete-email-logs" />
				<?php wp_nonce_field( 'hb_delete_email_logs', 'hb_delete_email_logs' ); ?>
			</form>

			<?php } ?>

		</div>

		<?php
		}
	}
}