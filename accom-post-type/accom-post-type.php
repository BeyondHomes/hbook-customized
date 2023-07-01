<?php
class HbAccommodation {

	private $hbdb;

	public function __construct( $hbdb ) {
		$this->hbdb = $hbdb;
	}

	public function create_accommodation_post_type() {
		register_post_type( 'hb_accommodation',
			apply_filters( 'hb_accommodation_cpt',
				array(
					'labels' => array(
						'name' => esc_html__( 'Accommodation', 'hbook-admin' ),
						'all_items' => esc_html__( 'All Accommodation', 'hbook-admin' ),
						'add_new_item' => esc_html__( 'Add New Accommodation', 'hbook-admin' ),
						'edit_item' => esc_html__( 'Edit Accommodation', 'hbook-admin' ),
						'new_item' => esc_html__( 'New Accommodation', 'hbook-admin' ),
						'view_item' => esc_html__( 'View Accommodation post', 'hbook-admin' ),
						'search_items' => esc_html__( 'Search Accommodation', 'hbook-admin' ),
						'not_found' => esc_html__( 'No accommodation found.', 'hbook-admin' ),
						'not_found_in_trash' => esc_html__( 'No accommodation post found in trash.', 'hbook-admin' ),
					),
					'public' => apply_filters( 'hb_accommodation_public', true ),
					'has_archive' => apply_filters( 'hb_accommodation_has_archive', true ),
					'supports' => apply_filters( 'hb_accommodation_supports', array( 'title', 'editor', 'thumbnail', 'revisions' ) ),
					'menu_icon' => 'dashicons-admin-home',
					'capabilities' => array(
						'delete_others_posts' => 'delete_others_accoms',
						'delete_posts' => 'delete_accoms',
						'delete_private_posts' => 'delete_private_accoms',
						'delete_published_posts' => 'delete_published_accoms',
						'edit_others_posts' => 'edit_other_accoms',
						'edit_posts' => 'edit_accoms',
						'edit_private_posts' => 'edit_private_accoms',
						'edit_published_posts' => 'edit_published_accoms',
						'publish_posts' => 'publish_accoms',
						'read_private_posts' => 'read_private_accoms',
					),
					'map_meta_cap' => true,
					'rewrite' => array( 'slug' => get_option( 'hb_accommodation_slug', 'hb_accommodation' ) ),
					'taxonomies' => apply_filters( 'hb_accommodation_taxonomies', array() ),
					'show_in_rest' => true,
				)
			)
		);
		if ( get_option( 'hb_flush_rewrite' ) != 'no_flush' ) {
			flush_rewrite_rules();
			update_option( 'hb_flush_rewrite', 'no_flush' );
		}
	}

	public function accommodation_meta_box() {
		add_meta_box( 'accommodation_meta_box', esc_html__( 'Accommodation settings', 'hbook-admin' ), array( $this, 'accommodation_meta_box_display' ), 'hb_accommodation', 'normal' );
	}

	public function accommodation_meta_box_display( $post ) {
		if ( $this->is_accom_main_language( $post->ID ) ) {
		?>
		<?php
		$accom_quantity = intval( get_post_meta( $post->ID, 'accom_quantity', true ) );
		if ( ! $accom_quantity ) {
			$accom_quantity = 1;
		}
		$accom_num_name_index = intval( get_post_meta( $post->ID, 'accom_num_name_index', true ) );
		$accom_num_name = $this->hbdb->get_accom_num_name( $post->ID );
		?>

		<p>
			<label for="hb-accom-quantity" class="hb-accom-settings-label"><?php esc_html_e( 'Number of accommodation of this type', 'hbook-admin' ); ?></label>
			<input id="hb-accom-quantity" name="hb-accom-quantity" type="text" size="2" value="<?php echo( esc_attr( $accom_quantity ) ); ?>"/>
		</p>

		<p>
			<a href="#" class="hb-edit-accom-numbering"><?php esc_html_e( 'Edit accommodation numbering', 'hbook-admin' ); ?></a>
		</p>

		<div id="hb-accom-num-name-wrapper">

			<input type="hidden" id="hb-accom-num-name-json" name="hb-accom-num-name-json" />
			<input type="hidden" id="hb-accom-num-name-index" name="hb-accom-num-name-index" value="<?php echo( esc_attr( $accom_num_name_index ) ); ?>" />

			<?php foreach ( $accom_num_name as $num => $name ) { ?>

				<p class="hb-accom-num-name">
					<input data-id="<?php echo( esc_attr( $num ) ); ?>" type="text" value="<?php echo( esc_attr( $name ) ); ?>" />
					<a class="hb-accom-num-name-delete" href="#"><?php esc_html_e( 'Delete', 'hbook-admin' ); ?></a>
				</p>

			<?php } ?>
		</div>

		<hr/>

		<h4><?php esc_html_e( 'Occupancy settings', 'hbook-admin' ); ?></h4>
		<p>
			<label for="hb-accom-occupancy" class="hb-accom-settings-label">
				<?php esc_html_e( 'Normal occupancy', 'hbook-admin' ); ?>
				<br/>
				<small><?php esc_html_e( 'Extra people might pay a fee as defined in the "Rates" page.', 'hbook-admin' ); ?></small>
			</label>
			<input id="hb-accom-occupancy" name="hb-accom-occupancy" type="text" size="2" value="<?php echo( esc_attr( get_post_meta( $post->ID, 'accom_occupancy', true ) ) ); ?>"/>
		</p>
		<p>
			<label for="hb-accom-max-occupancy" class="hb-accom-settings-label"><?php esc_html_e( 'Maximum occupancy', 'hbook-admin' ); ?></label>
			<input id="hb-accom-max-occupancy" name="hb-accom-max-occupancy" type="text" size="2" value="<?php echo( esc_attr( get_post_meta( $post->ID, 'accom_max_occupancy', true ) ) ); ?>"/>
		</p>
		<p>
			<label for="hb-accom-min-occupancy" class="hb-accom-settings-label"><?php esc_html_e( 'Minimum occupancy', 'hbook-admin' ); ?></label>
			<input id="hb-accom-min-occupancy" name="hb-accom-min-occupancy" type="text" size="2" value="<?php echo( esc_attr( get_post_meta( $post->ID, 'accom_min_occupancy', true ) ) ); ?>"/>
		</p>

		<hr/>

		<?php } ?>

		<h4><?php esc_html_e( 'Descriptions', 'hbook-admin' ); ?></h4>
		<p>
			<label for="hb-accom-search-result-desc" class="hb-accom-settings-label"><?php esc_html_e( 'Description displayed in search results', 'hbook-admin' ); ?></label>
			<textarea id="hb-accom-search-result-desc" name="hb-accom-search-result-desc" class="widefat i18n-multilingual" rows="3"><?php echo( esc_textarea( get_post_meta( $post->ID, 'accom_search_result_desc', true ) ) ); ?></textarea>
		</p>
		<p>
			<label for="hb-accom-listing-desc" class="hb-accom-settings-label"><?php esc_html_e( 'Description displayed in Accommodation list', 'hbook-admin' ); ?></label>
			<textarea id="hb-accom-listing-desc" name="hb-accom-listing-desc" class="widefat i18n-multilingual" rows="3"><?php echo( esc_textarea( get_post_meta( $post->ID, 'accom_list_desc', true ) ) ); ?></textarea>
		</p>
		<?php if ( $this->is_accom_main_language( $post->ID ) ) { ?>
		<p>
			<label for="hb-accom-short-name" class="hb-accom-settings-label">
				<?php esc_html_e( 'Accommodation short name', 'hbook-admin' ); ?>
				<br/>
				<small><?php esc_html_e( 'If set this name will be used in the calendar of the Reservations page.', 'hbook-admin' ); ?></small>
			</label>
			<input id="hb-accom-short-name" name="hb-accom-short-name" type="text" value="<?php echo( esc_attr( get_post_meta( $post->ID, 'accom_short_name', true ) ) ); ?>" />
		</p>
		<?php } ?>

		<hr/>

		<h4><?php esc_html_e( 'Display options', 'hbook-admin' ); ?></h4>

		<?php if ( $this->is_accom_main_language( $post->ID ) ) { ?>
		<p>
			<label class="hb-accom-settings-label"><?php esc_html_e( 'Accommodation display', 'hbook-admin' ); ?></label>
			<?php
			$accom_default_page = get_post_meta( $post->ID, 'accom_default_page', true );
			if ( ! $accom_default_page ) {
				$accom_default_page = 'yes';
			}
			?>
			<input
				type="radio" id="hb-accom-default-page-yes" name="hb-accom-default-page" value="yes"
				<?php if ( $accom_default_page == 'yes' ) { echo( 'checked' ); } ?>
			/>
			<label  for="hb-accom-default-page-yes">
			<?php esc_html_e( 'Use this post to display the accommodation', 'hbook-admin' ); ?>
			</label>
			<br/>
			<input
				type="radio" id="hb-accom-default-page-no" name="hb-accom-default-page" value="no"
				<?php if ( $accom_default_page == 'no' ) { echo( 'checked' ); } ?>
			/>
			<label for="hb-accom-default-page-no">
			<?php esc_html_e( 'Use another post or page to display the accommodation', 'hbook-admin' ); ?>
			</label>
		</p>
		<p class="hb-accom-select-linked-page">
			<label for="hb-accom-linked-page"  class="hb-accom-settings-label"><?php esc_html_e( 'ID of the page used for displaying the accommodation', 'hbook-admin' ); ?></label>
			<input id="hb-accom-linked-page" name="hb-accom-linked-page" type="text" size="4" value="<?php echo( esc_attr( get_post_meta( $post->ID, 'accom_linked_page', true ) ) ); ?>"/>
		</p>

		<?php } else { ?>

		<input style="display: none" type="radio" name="hb-accom-default-page" value="yes" checked />

		<?php } ?>

		<p class="hb-accom-select-template">
			<label for="hb-accom-page-template" class="hb-accom-settings-label"><?php esc_html_e( 'Accommodation display template', 'hbook-admin' ); ?></label>
			<?php
			$post_page_templates = array(
				'post' => esc_html__( 'Post', 'hbook-admin' ),
				'page.php' => esc_html__( 'Page', 'hbook-admin' ),
			);
			$page_templates = wp_get_theme()->get_page_templates();
			$page_templates = array_merge( $post_page_templates, $page_templates );
			$current_template = get_post_meta( $post->ID, 'accom_page_template', true );
			?>
			<select id="hb-accom-page-template" name="hb-accom-page-template">
				<?php foreach ( $page_templates as $template_file => $template_name ) : ?>
				<option value="<?php echo( esc_attr( $template_file ) ); ?>"<?php if ( $template_file == $current_template ) : ?> selected<?php endif; ?>>
					<?php echo( esc_html( $template_name ) );?>
				</option>
				<?php endforeach; ?>
			</select>
		</p>

		<?php if ( $this->is_accom_main_language( $post->ID ) ) { ?>

		<p>
			<label for="hb-accom-starting-price" class="hb-accom-settings-label"><?php esc_html_e( 'Starting price', 'hbook-admin' ); ?></label>
			<?php
			$accom_starting_price = get_post_meta( $post->ID, 'accom_starting_price', true );
			if ( $accom_starting_price && ( get_option( 'hb_price_precision' ) != 'no_decimals' ) ) {
				$accom_starting_price = number_format( $accom_starting_price, 2, '.', '' );
			}
			?>
			<input id="hb-accom-starting-price" name="hb-accom-starting-price" type="text" size="4" value="<?php echo( esc_attr( $accom_starting_price ) ); ?>" />
		</p>

		<hr/>

		<h4><?php esc_html_e( 'Automatic blockings', 'hbook-admin' ); ?></h4>
		<p>
			<label for="hb-accom-preparation-time" class="hb-accom-settings-label">
				<?php esc_html_e( 'Preparation time', 'hbook-admin' ); ?>
				<br/>
				<small><?php esc_html_e( 'Enter the number of nights that should be blocked before and after a reservation.', 'hbook-admin' ); ?></small>
			</label>
			<input id="hb-accom-preparation-time" name="hb-accom-preparation-time" type="text" size="2" value="<?php echo( esc_attr( get_post_meta( $post->ID, 'accom_preparation_time', true ) ) ); ?>" />
		</p>
		<p>
			<label for="hb-accom-to-block" class="hb-accom-settings-label">
				<?php esc_html_e( 'Accommodation that need to be blocked when this type of accommodation is booked', 'hbook-admin' ); ?>
				<br/>
				<small><?php esc_html_e( 'Enter accommodation id. Separate multiple values with comma. You can indicate the number of accommodation that should be blocked between parentheses.', 'hbook-admin' ); ?></small>
			</label>
			<input id="hb-accom-to-block" name="hb-accom-to-block" type="text" value="<?php echo( esc_attr( get_post_meta( $post->ID, 'accom_to_block', true ) ) ); ?>" />
		</p>

		<?php if ( get_option( 'hb_multiple_accom_booking') == 'enabled' ) { ?>

		<hr/>

		<h4><?php esc_html_e( 'Multiple accommodation booking', 'hbook-admin' ); ?></h4>
		<p>
			<label class="hb-accom-settings-label">
				<?php esc_html_e( 'Exclude this accommodation from multiple accommodation bookings', 'hbook-admin' ); ?>
			</label>
			<?php
			$excluded_from_multiple_accom_booking = get_post_meta( $post->ID, 'excluded_from_multiple_accom_booking', true );
			if ( ! $excluded_from_multiple_accom_booking ) {
				$excluded_from_multiple_accom_booking = 'no';
			}
			?>
			<input
				type="radio" id="hb-excluded-from-multiple-accom-booking-yes" name="hb-excluded-from-multiple-accom-booking" value="yes"
				<?php if ( $excluded_from_multiple_accom_booking == 'yes' ) { echo( 'checked' ); } ?>
			/>
			<label  for="hb-excluded-from-multiple-accom-booking-yes">
			<?php esc_html_e( 'Always', 'hbook-admin' ); ?>
			</label>
			<br/>
			<input
				type="radio" id="hb-excluded-from-multiple-accom-booking-global-only" name="hb-excluded-from-multiple-accom-booking" value="global-only"
				<?php if ( $excluded_from_multiple_accom_booking == 'global-only' ) { echo( 'checked' ); } ?>
			/>
			<label for="hb-excluded-from-multiple-accom-booking-global-only">
			<?php esc_html_e( 'On global search only', 'hbook-admin' ); ?>
			</label>
			<br/>
			<input
				type="radio" id="hb-excluded-from-multiple-accom-booking-no" name="hb-excluded-from-multiple-accom-booking" value="no"
				<?php if ( $excluded_from_multiple_accom_booking == 'no' ) { echo( 'checked' ); } ?>
			/>
			<label for="hb-excluded-from-multiple-accom-booking-no">
			<?php esc_html_e( 'Never', 'hbook-admin' ); ?>
			</label>
		</p>

		<?php } else { ?>

		<input type="hidden" name="hb-excluded-from-multiple-accom-booking" value="no" />

		<?php } ?>

		<?php
		}
	}

	public function save_accommodation_meta( $post_id ) {
		if ( isset( $_REQUEST['hb-accom-quantity'] ) ) {
			$accom_quantity = intval( $_REQUEST['hb-accom-quantity'] );
			if ( ! $accom_quantity ) {
				$accom_quantity = 1;
			}
			update_post_meta( $post_id, 'accom_quantity', $accom_quantity );
		}
		if ( isset( $_REQUEST['hb-accom-occupancy'] ) ) {
			$accom_occupancy = intval( $_REQUEST['hb-accom-occupancy'] );
			update_post_meta( $post_id, 'accom_occupancy', $accom_occupancy );
		}
		if ( isset( $_REQUEST['hb-accom-max-occupancy'] ) ) {
			$accom_max_occupancy = intval( $_REQUEST['hb-accom-max-occupancy'] );
			if ( ! $accom_max_occupancy || $accom_max_occupancy < $accom_occupancy ) {
				$accom_max_occupancy = $accom_occupancy;
			}
			update_post_meta( $post_id, 'accom_max_occupancy', $accom_max_occupancy );
		}
		if ( isset( $_REQUEST['hb-accom-min-occupancy'] ) ) {
			$accom_min_occupancy = intval( $_REQUEST['hb-accom-min-occupancy'] );
			if ( ! $accom_min_occupancy ) {
				$accom_min_occupancy = 1;
			}
			update_post_meta( $post_id, 'accom_min_occupancy', $accom_min_occupancy );
		}
		if ( isset( $_REQUEST['hb-accom-search-result-desc'] ) ) {
			update_post_meta( $post_id, 'accom_search_result_desc', wp_filter_post_kses( $_REQUEST['hb-accom-search-result-desc'] ) );
		}
		if ( isset( $_REQUEST['hb-accom-listing-desc'] ) ) {
			update_post_meta( $post_id, 'accom_list_desc', wp_filter_post_kses( $_REQUEST['hb-accom-listing-desc'] ) );
		}
		if ( isset( $_REQUEST['hb-accom-starting-price'] ) ) {
			$starting_price = $_REQUEST['hb-accom-starting-price'];
			if ( $starting_price ) {
				if ( get_option( 'hb_price_precision' ) != 'no_decimals' ) {
					$starting_price = floatval( $starting_price );
				} else {
					$starting_price = intval( $starting_price );
				}
			}
			update_post_meta( $post_id, 'accom_starting_price', $starting_price );
		}
		if ( isset( $_REQUEST['hb-accom-num-name-index'] ) ) {
			$accom_num_name_index = intval( $_REQUEST['hb-accom-num-name-index'] );
			if ( ! $accom_num_name_index ) {
				$accom_num_name_index = 0;
			}
			update_post_meta( $post_id, 'accom_num_name_index', $accom_num_name_index );
		}
		if ( isset( $_REQUEST['hb-accom-num-name-json'] ) ) {
			$accom_num_name = json_decode( wp_strip_all_tags( stripslashes( $_REQUEST['hb-accom-num-name-json'] ) ), true );
			if ( ! $accom_num_name ) {
				$accom_num_name = array();
			}
			$this->hbdb->update_accom_num_name( $post_id, $accom_num_name );
		}
		if ( isset( $_REQUEST['hb-accom-preparation-time'] ) ) {
			$accom_preparation_time = intval( $_REQUEST['hb-accom-preparation-time'] );
			if ( ! $accom_preparation_time ) {
				$accom_preparation_time = 0;
			}
			update_post_meta( $post_id, 'accom_preparation_time', $accom_preparation_time );
		}
		if ( isset( $_REQUEST['hb-accom-to-block'] ) ) {
			$accom_to_block = trim( $_REQUEST['hb-accom-to-block'] );
			$validated_accom_to_block = array();
			if ( $accom_to_block ) {
				$accom_to_block = explode( ',', $accom_to_block );
				$all_accom_ids = $this->hbdb->get_all_accom_ids();
				$all_accom_ids = array_diff( $all_accom_ids, array( $post_id ) );
				foreach ( $accom_to_block as $accom_id ) {
					$matches = array();
					$number_of_accom_to_block = 0;
					if ( preg_match( '/\s*\(\s*(\d+)\s*\)\s*/', $accom_id, $matches ) ) {
						$number_of_accom_to_block = $matches[1];
						$accom_id = preg_replace( '/\s*\(\s*\d+\s*\)\s*/', '', $accom_id );
					}
					$accom_id = intval( $accom_id );
					if ( in_array( $accom_id, $all_accom_ids ) ) {
						if ( $number_of_accom_to_block ) {
							$accom_id = $accom_id . '(' . $number_of_accom_to_block . ')';
						}
						$validated_accom_to_block[] = $accom_id;
					}
				}
			}
			update_post_meta( $post_id, 'accom_to_block', implode( ',', $validated_accom_to_block ) );
		}

		if ( isset( $_REQUEST['hb-accom-default-page'] ) ) {
			update_post_meta( $post_id, 'accom_default_page', sanitize_text_field( $_REQUEST['hb-accom-default-page'] ) );
		}
		if ( isset( $_REQUEST['hb-accom-page-template'] ) ) {
			update_post_meta( $post_id, 'accom_page_template', sanitize_text_field( $_REQUEST['hb-accom-page-template'] ) );
		}
		if ( isset( $_REQUEST['hb-accom-linked-page'] ) ) {
			update_post_meta( $post_id, 'accom_linked_page', intval( $_REQUEST['hb-accom-linked-page'] ) );
		}
		if ( isset( $_REQUEST['hb-accom-short-name'] ) ) {
			update_post_meta( $post_id, 'accom_short_name', sanitize_text_field( $_REQUEST['hb-accom-short-name'] ) );
		}
		if ( isset( $_REQUEST['hb-excluded-from-multiple-accom-booking'] ) ) {
			update_post_meta( $post_id, 'excluded_from_multiple_accom_booking', sanitize_text_field( $_REQUEST['hb-excluded-from-multiple-accom-booking'] ) );
		}
	}

	public function redirect_hb_menu_accom_page() {
		wp_redirect( admin_url( 'edit.php?post_type=hb_accommodation' ) );
		exit;
	}

	public function display_accom_id( $post ) {
		if ( in_array( $post->ID, $this->hbdb->get_all_accom_ids() ) ) {
		?>
		<div style="padding: 10px 10px 0; color: #666">
			<strong><?php esc_html_e( 'Accommodation id: ', 'hbook-admin' ); ?></strong>
			<?php echo( esc_html( $post->ID ) ); ?>
		</div>
		<?php
		}
	}

	public function filter_template_page( $default_template ) {
		global $post;
		if ( $post && $post->post_type == 'hb_accommodation' && get_post_meta( $post->ID, 'accom_default_page', true ) != 'no' ) {
			$accom_page_template = get_post_meta( $post->ID, 'accom_page_template', true );
			if ( $accom_page_template && $accom_page_template != 'post' ) {
				$template = get_stylesheet_directory() . '/' . $accom_page_template;
				if ( file_exists( $template ) ) {
					return $template;
				}
				$template = get_template_directory() . '/' . $accom_page_template;
				if ( file_exists( $template ) ) {
					return $template;
				}
			}
		}
		return $default_template;
	}

	public function admin_accom_order( $query ) {
		if( is_admin() && $query->get( 'post_type' ) == 'hb_accommodation' ) {
			$query_orderby = $query->get( 'orderby' );
			if ( ! $query_orderby ) {
				$query->set( 'order', 'ASC' );
			}
		}
	}

	private function is_accom_main_language( $accom_id ) {
		$all_status = true;
		$accom_ids = $this->hbdb->get_all_accom_ids( $all_status );
		return in_array( $accom_id, $accom_ids );
	}

}
