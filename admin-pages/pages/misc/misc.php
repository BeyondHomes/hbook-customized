<?php
class HbAdminPageMisc extends HbAdminPage {

	private $display_import_settings_modify_id_option;

	public function __construct( $page_id, $hbdb, $utils, $options_utils ) {
		$this->data = array(
			'hb_text' => array(
				'form_saved' => esc_html__( 'Settings have been saved.', 'hbook-admin' ),
				'date_not_valid' => esc_html__( 'The date is not valid (use a yyyy-mm-dd format).', 'hbook-admin' ),
				'choose_file' => esc_html__( 'Choose a file to import.', 'hbook-admin' ),
				'import_confirm_text' => esc_html__( 'Importing a setting file will delete all current HBook settings (including Accommodation Posts and Reservations). Do you want to continue?', 'hbook-admin' ),
				'hb-reset-all_confirm_text' => esc_html__( 'This action will delete permanently all reservations, blocked dates and customers. Reservations and customers numbering will be reset. Do you wish to continue?', 'hbook-admin' ),
				'hb-reset-reservations-blocked-accom_confirm_text' => esc_html__( 'This action will delete permanently all reservations and blocked dates. Reservations numbering will be reset. Do you wish to continue?', 'hbook-admin' ),
				'hb-delete-external-resa_confirm_text' => esc_html__( 'This action will delete permanently all reservations that have been imported into HBook via the iCal synchronization. Do you wish to continue?', 'hbook-admin' ),
				'hb-delete-cancelled-resa_confirm_text' => esc_html__( 'This action will delete permanently all reservations that have the status "Cancelled". Do you wish to continue?', 'hbook-admin' ),
			)
		);
		$this->display_import_settings_modify_id_option = false;
		parent::__construct( $page_id, $hbdb, $utils, $options_utils );
	}

	public function display() {
	?>

	<div class="wrap">

		<form id="hb-settings-form" method="POST" enctype="multipart/form-data">

			<h1><?php esc_html_e( 'Miscellaneous', 'hbook-admin' ); ?></h1>

			<?php $this->display_right_menu(); ?>

			<?php
			$this->import_settings();
			foreach ( $this->options_utils->get_misc_settings() as $section_id => $section ) {
				$this->options_utils->display_section_title( $section['label'] );
				foreach ( $section['options'] as $id => $option ) {
					if ( $id == 'hb_front_end_date_settings' ) {
						$this->display_date_format_settings();
					} else if ( $id == 'hb_import_export_settings' ) {
						$this->display_import_export_settings();
					} else if ( $id == 'hb_reset_settings' ) {
						$this->display_reset_settings();
					} else {
						$function_to_call = 'display_' . $option['type'] . '_option';
						$this->options_utils->$function_to_call( $id, $option );
					}
					if ( $id == 'hb_multiple_accom_booking' ) {
						echo( '<div class="hb-multiple-accom-booking-options-wrapper">' );
					} else if ( $id == 'hb_multiple_accom_booking_avoid_singleton' ) {
						echo( '</div><!-- end .hb-multiple-accom-booking-options-wrapper -->' );
					}
					if ( $id == 'hb_resa_page_default_filter' ) {
					?>
						<div class="hb-toggle-default-filters hb-view-all-active">
							<small class="hb-toggle-default-filters-link hb-view-filters-default-values">
								<a href="#"><?php esc_html_e( 'View all filters default values', 'hbook-admin' ); ?></a>
							</small>
							<small class="hb-toggle-default-filters-link hb-hide-filters-default-values">
								<a href="#"><?php esc_html_e( 'Hide filters default values', 'hbook-admin' ); ?></a>
							</small>
						</div>
					<?php
					}
				}
				$this->options_utils->display_save_options_section();
			}
			?>

			<input type="hidden" name="action" value="hb_update_misc_settings" />
			<input id="hb-nonce" type="hidden" name="nonce" value="" />

			<?php wp_nonce_field( 'hb_nonce_update_db', 'hb_nonce_update_db' ); ?>

		</form>
	</div>

	<?php
	}

	private function display_date_format_settings() {
		$langs = $this->utils->get_langs();
		$saved_settings = json_decode( get_option( 'hb_front_end_date_settings' ), true );
		require_once $this->utils->plugin_directory . '/utils/date-localization.php';
		$date_locale_info = new HbDateLocalization();
		$days = $date_locale_info->locale[ $this->utils->get_hb_known_locale() ]['day_names'];

		foreach ( $langs as $locale => $lang_name ) {
			$hb_known_locale = $this->utils->get_hb_known_locale( $locale );
			$default_first_day = $date_locale_info->locale[ $hb_known_locale ]['first_day'];
			if ( isset( $saved_settings[ $locale ]['first_day'] ) ) {
				$current_first_day = $saved_settings[ $locale ]['first_day'];
			} else {
				$current_first_day = $default_first_day;
			}

			$default_format = $date_locale_info->locale[ $hb_known_locale ]['date_format'];
			if ( isset( $saved_settings[ $locale ]['date_format'] ) ) {
				$current_format = $saved_settings[ $locale ]['date_format'];
			} else {
				$current_format = $default_format;
			}
			$days_select_options = '';
			foreach ( $days as $i => $day ) {
				if ( $i == $current_first_day ) {
					$selected = ' selected';
				} else {
					$selected = '';
				}
				$days_select_options .= '<option value="' . $i . '"' . $selected . '>' . $day . '</option>';
			}
			$formats = array(
				'mm/dd/yyyy',
				'dd/mm/yyyy',
				'dd.mm.yyyy',
				'dd-mm-yyyy',
				'yyyy/mm/dd',
				'dd-mm-yyyy',
				'dd.mm.yyyy',
				'yyyy-mm-dd',
			);
			$format_select_options = '';
			foreach ( $formats as $format ) {
				if ( $format == $current_format ) {
					$selected = ' selected';
				} else {
					$selected = '';
				}
				$format_select_options .= '<option' . $selected . '>' . $format . '</option>';
			}

			if ( sizeof( $langs ) > 1 ) {
			?>
				<h4><u><?php echo( esc_html( $lang_name ) ); ?></u> <small>(<?php echo( esc_html( $locale ) ); ?>)</small></h4>
				<small>
				<?php
				printf(
					esc_html__( 'Usual setting: first day is %s and date format is %s', 'hbook-admin' ),
					'<b>' . esc_html( $days[ $default_first_day ] ) . '</b>',
					'<b>' . esc_html( $default_format ) . '</b>'
				);
				?>
				</small>
			<?php
			}
			?>

			<div class="hb-lang-settings" data-locale="<?php echo( esc_attr( $locale ) ); ?>">

				<p>
					<label><?php esc_html_e( 'First day of the week', 'hbook-admin' ); ?></label><br/>
					<select class="hb-first-day">
						<?php
						echo( wp_kses(
							$days_select_options,
							array( 'option' => array( 'value' => array(), 'selected' => array() ) )
						) );
						?>
					</select>
				</p>

				<p>
					<label><?php esc_html_e( 'Date format', 'hbook-admin' ); ?></label><br/>
					<select class="hb-date-format">
						<?php
						echo( wp_kses(
							$format_select_options,
							array( 'option' => array( 'value' => array(), 'selected' => array() ) )
						) );
						?>
					</select>
				</p>

			</div>

			<?php
		}
		?>

		<input type="hidden" id="hb_front_end_date_settings" name="hb_front_end_date_settings" value="" />
		<p style="line-height: 0.7">&nbsp;</p>

		<?php
	}

	private function display_reset_settings() {
		$this->reset_settings();
		$reset_options = array(
			'hb-reset-all' => esc_html__( 'Reset HBook (customers, blocked dates and reservations)', 'hbook-admin' ),
			'hb-reset-reservations-blocked-accom' => esc_html__( 'Reset reservations and blocked dates', 'hbook-admin' ),
			'hb-delete-external-resa' => esc_html__( 'Delete only reservations imported from external calendars', 'hbook-admin' ),
			'hb-delete-cancelled-resa' => esc_html__( 'Delete only reservations with status "Cancelled"', 'hbook-admin' )
		);
		?>
		<small><?php esc_html_e( 'Any of the actions below can not be rolled back. Reservations, blocked dates or/and customers will be deleted permanently.', 'hbook-admin' ); ?></small>
		<div class="hb-reset-settings-wrapper">
			<?php foreach ( $reset_options as $id => $text ) { ?>
			<div id="<?php echo( esc_attr( $id ) ); ?>">
				<p>
					<a href="#" class="hb-reset-hbook button"><?php echo( esc_html( $text ) ); ?></a>
				</p>
			</div>
			<?php } ?>
			<input type="hidden" id="hb-reset-hbook-action" name="hb-reset-hbook-action" value="" />
			<?php wp_nonce_field( 'hb_reset_hbook', 'hb_reset_hbook' ); ?>
		</div>

	<?php
	}

	private function reset_settings() {
		if (
			isset( $_POST['hb-reset-hbook-action'] ) &&
			( $_POST['hb-reset-hbook-action'] != '' ) &&
			wp_verify_nonce( $_POST['hb_reset_hbook'], 'hb_reset_hbook' ) &&
			current_user_can( 'manage_hbook' )
		) {
			global $wpdb;
			$response = array();
			$action = $_POST['hb-reset-hbook-action'];
			switch ( $action ) {
				case 'hb-reset-all':
					$reset_customers = $wpdb->query( 'TRUNCATE TABLE ' . $wpdb->prefix . 'hb_customers' );
					$reset_blocked = $wpdb->query( 'TRUNCATE TABLE ' . $wpdb->prefix . 'hb_accom_blocked' );
					$reset_resa = $wpdb->query( 'TRUNCATE TABLE ' . $wpdb->prefix . 'hb_resa' );
					if ( $reset_customers && $reset_blocked && $reset_resa ) {
						$response = array(
							'success' => true,
							'msg' => esc_html__( 'All done! Reservations, customers and blocked dates have been reset.', 'hbook-admin' ),
						);
					} else {
						$response['success'] = false;
					}
				break;

				case 'hb-reset-reservations-blocked-accom':
					$reset_resa = $wpdb->query( 'TRUNCATE TABLE ' . $wpdb->prefix . 'hb_resa' );
					$reset_blocked = $wpdb->query( 'TRUNCATE TABLE ' . $wpdb->prefix . 'hb_accom_blocked' );
					if ( $reset_resa && $reset_blocked ) {
						$response = array(
							'success' => true,
							'msg' => esc_html__( 'All done! Blocked dates and reservations have been reset.', 'hbook-admin' ),
						);
					} else {
						$response['success'] = false;
					}
				break;

				case 'hb-delete-external-resa':
					$result = $wpdb->get_results( 'SELECT id FROM ' . $wpdb->prefix . 'hb_resa WHERE origin != "website"' );
					if ( false !== $result ) {
						$success = true;
						foreach( $result as $resa ) {
							$deleted = $this->hbdb-> delete_resa( $resa->id );
							if ( false === $deleted ) {
								$success = false;
							}
						}
						if ( $success ) {
							$response = array(
								'success' => true,
								'msg' => esc_html__( 'All done! Reservations imported from external calendars have been deleted.', 'hbook-admin' ),
							);
						} else {
							$response['success'] = false;
						}
					}
				break;

				case 'hb-delete-cancelled-resa':
					$result = $wpdb->delete( $wpdb->prefix . 'hb_resa', array( 'status' => 'cancelled' ) );
					if ( false !== $result ) {
						$response = array(
							'success' => true,
							'msg' => esc_html__( 'All done! Reservations with status "Cancelled" have been deleted.', 'hbook-admin' ),
						);
					} else {
						$response['success'] = false;
					}
				break;
			}
			if ( $response['success'] ) {
				echo( '<div class="notice notice-success">' );
				echo( '<p>' . esc_html( $response['msg'] ) . '</p></div>' );
			} else {
				echo( '<div class="error">' );
				echo( '<p>' );
				esc_html_e( 'An error occured. The action could not be completed.', 'hbook-admin' );
				echo( '</p></div>' );
			}
		}
	}

	private function display_import_export_settings() {
		?>

		<div class="hb-import-export-settings-wrapper">
			<p>
				<input id="hb-import-settings-file" type="file" name="hb-import-settings-file" />
			</p>
			<?php if ( $this->display_import_settings_modify_id_option ) { ?>
			<p>
				<input type="checkbox" id="hb-import-settings-modify-id" name="hb-import-settings-modify-id" />
				<label for="hb-import-settings-modify-id"><?php esc_html_e( 'Modify Accommodation Post ID when importing (you may have to update HBook shortcodes accordingly).', 'hbook-admin' ); ?></label>
			</p>
			<?php } ?>
			<p>
				<a href="#" class="hb-import-settings button-primary"><?php esc_html_e( 'Import settings file', 'hbook-admin' ); ?></a>
			</p>
			<p class="hb-import-settings-waiting-msg">
				<span class="spinner"></span>
				<?php esc_html_e( 'Importing all settings may take several minutes. Please do not refresh or exit this page before completion.', 'hbook-admin' ); ?>
			</p>
			<p>
				<a href="#" class="hb-export-settings button"><?php esc_html_e( 'Download settings file', 'hbook-admin' ); ?></a>
			</p>
			<input type="hidden" id="hb-import-export-action" name="hb-import-export-action" value="" />
			<?php wp_nonce_field( 'hb_import_export', 'hb_import_export' ); ?>
		</div>

	<?php
	}

	private function import_settings() {
		if (
			isset( $_POST['hb-import-export-action'] ) &&
			( $_POST['hb-import-export-action'] == 'import-settings' ) &&
			wp_verify_nonce( $_POST['hb_import_export'], 'hb_import_export' ) &&
			current_user_can( 'manage_hbook' )
		) {
			if ( $_FILES['hb-import-settings-file']['error'] ) {
				?>

				<div class="error">
					<p>
						<?php esc_html_e( 'Settings could not be imported.', 'hbook-admin' ); ?>
						<span style="display: none">Error (<?php echo( esc_html( $_FILES['hb-import-settings-file']['error'] ) ); ?>)</span>
					</p>
				</div>

				<?php
				return;
			}
			$import_file = $_FILES['hb-import-settings-file']['tmp_name'];
			$file_content = file_get_contents( $import_file );
			$settings = json_decode( $file_content, true );
			if ( ! $settings ) {
				?>

				<div class="error">
					<p>
						<b><?php esc_html_e( 'Settings could not be imported.', 'hbook-admin' ); ?></b>
						<br/>
						<?php printf( esc_html__( 'The file %s is not a correct HBook settings file.', 'hbook-admin' ), '<b>' . esc_html( $_FILES['hb-import-settings-file']['name'] ) . '</b>' ); ?>
					</p>
				</div>

				<?php
			} else {
				$accom_ids = array_keys( $settings['accom'] );

				$existing_posts = array();
				foreach ( $accom_ids as $post_id ) {
					$existing_post = get_post( $post_id, ARRAY_A );
					if ( $existing_post && ( $existing_post['post_type'] != 'hb_accommodation' ) ) {
						$existing_posts[ $post_id ] = array(
							'id' => $post_id,
							'title' => $existing_post['post_title'],
							'type' => $existing_post['post_type'],
							'name' => $existing_post['post_name'],
						);
					}
				}
				if ( $existing_posts && ! isset( $_POST['hb-import-settings-modify-id'] ) ) {
					$this->display_import_settings_modify_id_option = true;
					?>

					<div class="error">
						<p class="hb-import-settings-error">
							<b><?php esc_html_e( 'Settings could not be imported.', 'hbook-admin' ); ?></b>
						</p>
						<p>
							<?php
							esc_html_e( 'The following posts/pages/attachments prevented HBook from importing the settings:', 'hbook-admin' );
							echo( '<ul class="hb-import-existing-posts">' );
							foreach ( $existing_posts as $existing_post ) {
								echo( '<li>' );
								echo( esc_html( $existing_post['title'] . ' (ID: ' . $existing_post['id'] . ') - ' ) );
								echo( esc_html( '(' . $existing_post['name'] . ' / ' . $existing_post['type'] . ')' ) );
								echo( '</li>' );
							}
							echo( '</ul>' );
							esc_html_e( 'You can either delete these posts/pages/attachments (which is the recommanded method) or start importing again with the "Modify Accommodation Post ID" option activated.', 'hbook-admin' );
							?>
						</p>
					</div>

					<?php
				} else {
					global $wpdb;

					$wpdb->delete( $wpdb->posts, array( 'post_type' => 'hb_accommodation' ) );
					foreach ( $accom_ids as $accom_id ) {
						$post_to_insert = $settings['accom'][ $accom_id ];
						if ( in_array( $accom_id, array_keys( $existing_posts ) ) ) {
							unset( $post_to_insert['post_info']['ID'] );
						}
						$wpdb->insert(
							$wpdb->posts,
							$post_to_insert['post_info']
						);
						$accom_ids_map[ $accom_id ] = $wpdb->insert_id;
						foreach ( $post_to_insert['post_meta'] as $meta_id => $meta_value ) {
							update_post_meta( $accom_ids_map[ $accom_id ], $meta_id, $meta_value );
						}
					}

					foreach ( $settings['tables'] as $table_name => $rows ) {
						$table_name = $wpdb->prefix . 'hb_' . $table_name;
						$wpdb->query( 'TRUNCATE TABLE ' . $table_name );
						foreach ( $rows as $row ) {
							if ( isset( $row['accom_id'] ) ) {
								$row['accom_id'] = $accom_ids_map[ $row['accom_id'] ];
							}
							$wpdb->insert(
								$table_name,
								$row
							);
						}
					}

					$options = array_merge(
						$this->options_utils->get_misc_settings(),
						$this->options_utils->get_ical_settings(),
						$this->options_utils->get_payment_settings(),
						$this->options_utils->get_appearance_settings(),
						$this->options_utils->get_search_form_options(),
						$this->options_utils->get_accom_selection_options(),
						$this->options_utils->get_non_standard_options()
					);
					foreach ( $options as $section ) {
						foreach ( $section['options'] as $option_id => $option ) {
							if ( isset( $settings['options'][ $option_id ] ) ) {
								update_option( $option_id, $settings['options'][ $option_id ] );
							}
						}
					}

					echo( '<div class="notice notice-success">' );
					echo( '<p>' );
					esc_html_e( 'HBook settings have been imported.', 'hbook-admin' );
					echo( '</p>' );
					if ( $existing_posts ) {
						echo( '<p>' );
						esc_html_e( 'Please take note that the following Accommodation Post have new IDs:', 'hbook-admin' );
						echo( '<ul class="hb-import-existing-posts">' );
						foreach ( $existing_posts as $existing_post ) {
							echo( '<li><span>' );
							echo( esc_html( $settings['accom'][ $accom_id ]['post_info']['post_title'] ) );
							echo( ' (ID: ' . $existing_post['id'] . ' => ' . $accom_ids_map[ $existing_post['id'] ] . ')' );
							echo( '</span></li>' );
						}
						echo( '</ul>' );
						esc_html_e( 'You may have to update HBook shortcodes accordingly.', 'hbook-admin' );
						echo( '</p>' );
					}
					echo( '</div>' );
				}
			}
		}
	}
}