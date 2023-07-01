<?php
class HbAdminPageIcal extends HbAdminPage {

	private $resa_ical;

	public function __construct( $page_id, $hbdb, $utils, $options_utils ) {
		require_once $utils->plugin_directory . '/utils/resa-ical.php';
		$this->resa_ical = new HbResaIcal( $hbdb, $utils );

		$this->data = array(
			'hb_text' => array(
				'confirm_delete' => esc_html__( 'Stop synchronizing with this calendar?', 'hbook-admin' ),
				'ical_frequency_not_valid' => esc_html__( 'The frequency is not valid. Value should not be lower than 5 minutes (300s).', 'hbook-admin' ),
			)
		);
		parent::__construct( $page_id, $hbdb, $utils, $options_utils );
	}

	public function display() {
		?>

		<div class= "wrap" >

			<h1><?php esc_html_e( 'ICal synchronization and import/export', 'hbook-admin' ); ?></h1>

			<?php $this->display_right_menu(); ?>

			<hr/>

			<p>
				<?php printf( esc_html__( 'For iCal synchronization to work properly, we strongly recommend that %s is used as your main calendar. Synchronize each HBook calendar with the matching external calendars both ways. Do not sync your external calendars together.', 'hbook-admin' ), '<b>HBook</b>' ); ?>
			</p>
			<p>
				<?php printf( esc_html__( 'If there are dates that you need to block, you should set these unavailable dates using %s and do not add them manually in the external calendars.', 'hbook-admin' ), '<b>' . esc_html__( 'Reservations > Block accommodation', 'hbook-admin' ) . '</b>' ); ?>
				<?php esc_html_e( 'With the synchronization, they will be set automatically as unavailable in your other calendars.', 'hbook-admin' ); ?>
			</p>
			<br/>

		<?php
		if ( isset( $_POST['ical-upload-form-action'] ) && wp_verify_nonce( $_POST['hb_import_ical_file'], 'hb_import_ical_file' ) && current_user_can( 'manage_hbook' ) ) {
			$import_file = $_FILES['hb-import-ical-file']['tmp_name'];
			$calendar_name = wp_strip_all_tags( $_POST['hb-import-ical-calendar-name'] );
			$accom_id = intval( $_POST['accom_id'] );
			$accom_num = intval( $_POST['accom_num'] );
			$ics_file = file_get_contents( $import_file );
			$this->resa_ical->ical_parse( $ics_file, $accom_num, $accom_id, $calendar_name, '' );
		}

		if ( isset( $_POST['ical-url-form-action'] ) && wp_verify_nonce( $_POST['hb_import_url'], 'hb_import_url' ) && current_user_can( 'manage_hbook' ) ) {
			$calendar_name = wp_strip_all_tags( $_POST['hb-import-calendar-name'] );
			$synchro_url = wp_strip_all_tags( $_POST['hb-import-calendar-url'] );
			$webcal = strpos( $synchro_url, 'webcal' );
			if ( $webcal !== false ) {
				$webcal_secure = strpos( $synchro_url, 'webcals' );
				if ( $webcal_secure !== false ) {
					$synchro_url = str_replace( 'webcals', 'https', $synchro_url );
				} else {
					$synchro_url = str_replace( 'webcal', 'http', $synchro_url );
				}
			}
			$accom_id = intval( $_POST['accom_id'] );
			$accom_num = intval( $_POST['accom_num'] );
			/*
			if ( get_option( 'hb_ical_do_not_force_ssl_version' ) != 'yes' ) {
				add_action( 'http_api_curl', array( $this->utils, 'set_http_api_curl_ssl_version' ) );
			}
			*/
			$response = $this->utils->ical_sync_remote_post( $synchro_url );
			/*
			if ( get_option( 'hb_ical_do_not_force_ssl_version' ) != 'yes' ) {
				remove_action( 'http_api_curl', array( $this->utils, 'set_http_api_curl_ssl_version' ) );
			}
			*/
			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				?>
				<div class="error">
					<p><?php printf( esc_html__( 'There seems to be a problem : %s Check that you have entered a valid URL.', 'hbook-admin' ), $error_message );?>
				</div>
				<?php
			} else {
				if ( $_POST['ical-url-form-action'] == 'new-calendar' ) {
					$calendar_imported = $this->resa_ical->ical_parse( $response['body'], $accom_num, $accom_id, $calendar_name, $synchro_url );
					if ( $calendar_imported['success'] ) {
						$calendar_id = $calendar_imported['calendar_id'];
						$synchro_id = $calendar_imported['synchro_id'];
						$this->hbdb->add_ical_calendar( $accom_id, $accom_num, $synchro_url, $synchro_id, $calendar_id, $calendar_name );
					}
				} else if ( $_POST['ical-url-form-action'] == 'edit-calendar' ) {
					$db_calendar_id = intval( $_POST['edit-ical-calendar-id'] );
					$db_synchro_url = wp_strip_all_tags( $_POST['edit-ical-calendar-url'] );
					if ( $db_synchro_url != $synchro_url ) {
						$calendar_imported = $this->resa_ical->ical_parse( $response['body'], $accom_num, $accom_id, $calendar_name, $synchro_url );
						if ( $calendar_imported['success'] ) {
							$calendar_id = $calendar_imported['calendar_id'];
							$this->hbdb->update_ical_calendar( $synchro_url, $calendar_id, $calendar_name, $db_synchro_url );
						}
					} else {
						$this->hbdb->update_ical_calendar_name( $calendar_name, $db_synchro_url );
					}
				}
			}
		}

		if ( isset(	$_POST['ical-synchro-deletion'] ) && wp_verify_nonce( $_POST['ical-synchro-deletion'], 'ical-synchro-deletion'  ) && current_user_can( 'manage_hbook' ) ) {
			$db_synchro_url = wp_strip_all_tags( $_POST['ical-calendar-url'] );
			$this->hbdb->delete_ical_calendar( $db_synchro_url );
		}

		if ( isset(	$_POST['update-sync-calendars'] ) && wp_verify_nonce( $_POST['update-sync-calendars'], 'update-sync-calendars' ) && current_user_can( 'manage_hbook' ) ) {
			$this->resa_ical->update_calendars();
		}

		if ( $this->utils->nb_accom() == 0 ) {
			echo( '<p>' );
			esc_html_e( 'At least one accommodation must be created in order to set iCal synchronization.', 'hbook-admin' );
			echo( '</p>' );
		} else {
		?>

			<form class="update-sync-calendars" method="post">
				<input type="hidden" name="update-sync-calendars" value="" />
				<?php wp_nonce_field( 'update-sync-calendars', 'update-sync-calendars' ); ?>
				<button type="submit" class="button-primary"><?php esc_html_e( 'Update calendars', 'hbook-admin' ); ?></button>
			</form>
			<p>
				<i>
					<?php
					esc_html_e( 'Last synchronized on: ', 'hbook-admin' );
					$last_synced = get_option( 'hb_last_synced' );
					$local_last_sync = $this->utils->get_blog_datetime( $last_synced );
					echo( esc_html( $local_last_sync ) );
					?>
				</i>
			</p>
			<table class="wp-list-table widefat hb-ical-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Accommodation type', 'hbook-admin' ); ?></th>
						<th><?php esc_html_e( 'Accommodation', 'hbook-admin' ); ?></th>
						<th><?php esc_html_e( 'Export calendars', 'hbook-admin' ); ?></th>
						<th><?php esc_html_e( 'Import calendars', 'hbook-admin' ); ?></th>
						<th><?php esc_html_e( 'Synchronized calendars', 'hbook-admin' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$accom_posts_ids = $this->hbdb->get_all_accom_ids();
					foreach ( $accom_posts_ids as $accom_post_id ) {
						$post = get_post( $accom_post_id );
						$accom_title = $post->post_title;
						$accom_quantity = get_post_meta( $accom_post_id, 'accom_quantity', true );
						if ( $accom_quantity ) {
							?>
							<tr>
								<td><b><?php echo( esc_html( $accom_title ) ); ?></b></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							<?php
							$accom_names = $this->hbdb->get_accom_num_name( $accom_post_id );
							foreach ( $accom_names as $accom_num => $accom_name ) {
								$random = rand( 0, 9999 );
								$href_url = site_url() . '/?feed=hbook-calendar.ics&accom_id=' . $accom_post_id . '&accom_num=' . $accom_num;
								?>
								<tr>
									<td> </td>
									<td><?php echo( esc_html( $accom_name ) );?></td>
									<td>
										<a class="ical-download" href="<?php echo( esc_url( $href_url . '&rand=' . $random ) ); ?>">
											<?php esc_html_e( 'Download .ics file', 'hbook-admin' ); ?>
										</a><br/>
										<a href="#" class="ical-export-url"><?php esc_html_e( 'Get export url', 'hbook-admin' ); ?></a><br/>
										<a href="#" class="ical-export-url-hide"><?php esc_html_e( 'Hide export url', 'hbook-admin' ); ?></a><br/>
										<b class="ical-export-url-value"><?php esc_html_e( 'URL to add to your external calendars', 'hbook-admin' ); ?></b>
										<input type="text" class="ical-export-url-value" value="<?php echo( esc_attr( $href_url ) );?>">
									</td>
									<td>
										<a class="ical-upload" href="#"><?php esc_html_e( 'Upload .ics file', 'hbook-admin' ); ?><br/></a>
										<form class="import-ical-form" method="post" enctype="multipart/form-data">
											<b><?php esc_html_e( 'Import a calendar file', 'hbook-admin' ); ?></b><br/>
											<?php esc_html_e( 'Calendar name: ', 'hbook-admin' ); ?><input type="text" max-length="40" id="hb-import-ical-calendar-name" name="hb-import-ical-calendar-name" value=""/>
											<input type="file" name="hb-import-ical-file" required="required"/>
											<input type="hidden" name="accom_id" value="<?php echo( esc_attr( $accom_post_id ) ); ?>"/>
											<input type="hidden" name="accom_num" value="<?php echo( esc_attr( $accom_num ) ); ?>"/>
											<input type="hidden" name="ical-upload-form-action" value=""/>
											<?php wp_nonce_field( 'hb_import_ical_file', 'hb_import_ical_file' );?>
											<button type="submit" class="button-primary"><?php esc_html_e( 'Import', 'hbook-admin' ); ?></button>
											<button type="button" class="ical-upload-cancel button-secondary"><?php esc_html_e( 'Cancel', 'hbook-admin' ); ?></button>
										</form>
										<a class="ical-synchro" href="#"><?php esc_html_e( 'Sync with a new calendar', 'hbook-admin' ); ?><br/></a>
										<form class="import-url-form" method="post">
											<b><?php esc_html_e( 'Add your external calendar', 'hbook-admin' ); ?></b><br/>
											<?php esc_html_e( 'Calendar name:', 'hbook-admin' ); ?><input type="text" max-length="40" class="hb-import-calendar-name" name="hb-import-calendar-name" value=""/>
											<?php esc_html_e( 'Calendar URL:', 'hbook-admin' ); ?><input type="text" class="hb-import-calendar-url" name="hb-import-calendar-url"/>
											<input type="hidden" name="accom_id" value="<?php echo( esc_attr( $accom_post_id ) ); ?>"/>
											<input type="hidden" name="accom_num" value="<?php echo( esc_attr( $accom_num ) ); ?>"/>
											<input type="hidden" class="ical-url-form-action" name="ical-url-form-action" value=""/>
											<input type="hidden" class="edit-calendar-id" name="edit-ical-calendar-id" value="" />
											<input type="hidden" class="edit-calendar-url" name="edit-ical-calendar-url" value="" />
											<?php wp_nonce_field( 'hb_import_url', 'hb_import_url' );?>
											<button type="submit" class="add-calendar button-primary"><?php esc_html_e( 'Sync', 'hbook-admin' ); ?></button>
											<button type="submit" class="save-changes button-primary"><?php esc_html_e( 'Save', 'hbook-admin' ); ?></button>
											<button type="button" class="ical-url-cancel button-secondary"><?php esc_html_e( 'Cancel', 'hbook-admin' ); ?></button>
										</form>
									</td>
									<td>
										<?php
										$results = $this->hbdb->get_ical_sync_by_accom_num( $accom_post_id, $accom_num );
										if ( $results ) {
											foreach ( $results as $result ) {
												?>
												<div>
													<form class="ical-edit-calendar" method="post">
														<span class="ical-synchro-calendar-name" ><?php echo( esc_html( $result['calendar_name'] ) ); ?></span>
														<input type="hidden" name="ical-calendar-url" class="ical-calendar-url" value="<?php echo( esc_attr( $result['synchro_url'] ) ); ?>" />
														<input type="hidden" name="ical-calendar-id" class="ical-calendar-id" value="<?php echo( esc_attr( $result['calendar_id'] ) ); ?>" />
														<input type="hidden" name="ical-synchro-deletion" value="Y" />
														<?php wp_nonce_field( 'ical-synchro-deletion', 'ical-synchro-deletion' ); ?>
														<a class="ical-synchro-delete" href="#"><span class="dashicons dashicons-trash"></span></a>
														<a class="ical-synchro-edit" href="#"><span class="dashicons dashicons-edit"></span></a>
													</form>
												</div>
												<?php
											}
										}
										?>
									</td>
								</tr>
								<?php
							}
							?>
						<?php
						}
					}
					?>
				</tbody>
			</table>
			<br/>
			<p>
				<?php
				$href_all_url = site_url() . '/?feed=hbook-all-calendars.ics';
				?>
				<a href="#" class="all-icals-export-url"><?php esc_html_e( 'Get url of the calendar containing all HBook calendars in one', 'hbook-admin' ); ?></a><br/>
				<input type="text" class="all-icals-export-url-value" value="<?php echo( esc_attr( $href_all_url ) );?>">
			</p>
			<br/>
			<form id="hb-settings-form">

				<?php
				foreach ( $this->options_utils->get_ical_settings() as $section_id => $section ) {
					$this->options_utils->display_section_title( $section['label'] );
					foreach ( $section['options'] as $id => $option ) {
						$function_to_call = 'display_' . $option['type'] . '_option';
						$this->options_utils->$function_to_call( $id, $option );
					}
					$this->options_utils->display_save_options_section();
				}
				?>

				<input type="hidden" name="action" value="hb_update_ical_settings" />
				<input id="hb-nonce" type="hidden" name="nonce" value="" />

				<?php wp_nonce_field( 'hb_nonce_update_db', 'hb_nonce_update_db' ); ?>

			</form>

		</div><!-- end .wrap -->
		<?php
		}
	}
}
