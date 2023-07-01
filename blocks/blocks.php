<?php
class HBookBlocks {

	private $hbdb;
	private $utils;

	public function __construct( $hbdb, $utils ) {
		$this->hbdb = $hbdb;
		$this->utils = $utils;
	}

	public function add_blocks_category( $categories ) {
		return array_merge(
			$categories,
			array(
				array(
					'slug' => 'hbook-blocks',
					'title' => esc_html__( 'HBook Blocks', 'hbook-admin' ),
				),
			)
		);
	}

	public function block_editor_assets() {
		$blocks = array( 'booking-form', 'accom-list', 'availability', 'rates', 'resa-summary', 'starting-price' );
		foreach( $blocks as $block ) {
			if ( get_option( 'hbook_status' ) == 'dev' ) {
				$version = filemtime( $this->utils->plugin_directory . '/blocks/'.  $block . '-block.js' );
			} else {
				$version = $this->utils->plugin_version;
			}
			$deps = array( 'wp-blocks', 'wp-element' );
			global $pagenow;
			if ( $pagenow == 'widgets.php' ) {
				$deps[] = 'wp-edit-widgets';
			} else {
				$deps[] = 'wp-editor';
			}
			wp_enqueue_script(
				'hbook-' . $block . '-block',
				$this->utils->plugin_url . '/blocks/'.  $block . '-block.js',
				$deps,
				$version
			);
		}
		$text = array(
			'accom_id' => esc_html__( 'Accommodation id:', 'hbook-admin' ),

			'accom_list_title' => esc_html__( 'Accommodation list', 'hbook-admin' ),
			'accom_list_settings' => esc_html__( 'Accommodation list settings', 'hbook-admin' ),
			'accom_list_block' => esc_html__( 'Accommodation list block.', 'hbook-admin' ),
			'nb_columns' => esc_html__( 'Number of columns (maximum)', 'hbook-admin' ),
			'show_thumb' => esc_html__( 'Display thumbnail', 'hbook-admin' ),
			'link_thumb_to_accom' => esc_html__( 'Link thumbnail to accommodation', 'hbook-admin' ),
			'link_title_to_accom' => esc_html__( 'Link title to accommodation', 'hbook-admin' ),
			'thumb_width' => esc_html__( 'Thumbnail width (in px)', 'hbook-admin' ),
			'thumb_height' => esc_html__( 'Thumbnail height (in px)', 'hbook-admin' ),
			'view_button' => esc_html__( 'Display a button to view accommodation', 'hbook-admin' ),
			'book_button' => esc_html__( 'Display a button to book accommodation', 'hbook-admin' ),

			'availability_title' => esc_html__( 'Availability calendar', 'hbook-admin' ),
			'availability_settings' => esc_html__( 'Availability calendar settings', 'hbook-admin' ),
			'availability_block' => esc_html__( 'Availability calendar block.', 'hbook-admin' ),
			'accom' => esc_html__( 'Accommodation', 'hbook-admin' ),

			'rates_title' => esc_html__( 'Rates', 'hbook-admin' ),
			'rates_settings' => esc_html__( 'Rates settings', 'hbook-admin' ),
			'rates_block' => esc_html__( 'Rates block.', 'hbook-admin' ),
			'rates_type' => esc_html__( 'Rates type', 'hbook-admin' ),
			'rates_type_accom' => esc_html__( 'Accommodation', 'hbook-admin' ),
			'rates_type_adults' => esc_html__( 'Extra adults', 'hbook-admin' ),
			'rates_type_children' => esc_html__( 'Extra children', 'hbook-admin' ),
			'rates_sorting' => esc_html__( 'Rates sorting', 'hbook-admin' ),
			'rates_sorting_grouped' => esc_html__( 'Grouped', 'hbook-admin' ),
			'rates_sorting_chrono' => esc_html__( 'Chronological', 'hbook-admin' ),
			'rates_show_season_name' => esc_html__( 'Show season name', 'hbook-admin' ),
			'select_accom' => esc_html__( 'Please select an accommodation', 'hbook-admin' ),

			'booking_form_title' => esc_html__( 'Booking form', 'hbook-admin' ),
			'booking_form_settings' => esc_html__( 'Booking form settings', 'hbook-admin' ),
			'booking_form_block' => esc_html__( 'Booking form block.', 'hbook-admin' ),
			'search_only' => esc_html__( 'Search only', 'hbook-admin' ),
			'redirection_page' => esc_html__( 'Redirection', 'hbook-admin' ),
			'select_redirection_page' => esc_html__( 'Please select a redirection page', 'hbook-admin' ),
			'thank_you_page' => esc_html__( 'Thank you page', 'hbook-admin' ),

			'resa_summary_title' => esc_html__( 'Reservation summary', 'hbook-admin' ),
			'resa_summary_block' => esc_html__( 'Reservation summary block.', 'hbook-admin' ),

			'starting_price_title' => esc_html__( 'Starting price', 'hbook-admin' ),
			'starting_price_block' => esc_html__( 'Starting price block.', 'hbook-admin' ),
		);
		$this->utils->hb_script_var( 'hbook-booking-form-block', 'hb_blocks_text', $text );

		$accom_options = array();
		$accom_options_without_all = array();
		$accom_options_without_all[] = array(
			'value' => '',
			'label' => ''
		);
		$current_accom_id = '';
		$all_accom = $this->hbdb->get_all_accom();
		global $post;
		if ( isset( $post->ID ) && in_array( $post->ID, array_keys( $all_accom ) ) ) {
			$current_accom_id = $post->ID;
			$new_accom_option = array(
				'value' => $post->ID,
				'label' => esc_html__( 'Current', 'hbook-admin' )
			);
			$accom_options[] = $new_accom_option;
			$accom_options_without_all[] = $new_accom_option;
		}
		$accom_options[] = array(
			'value' => 'all',
			'label' => esc_html__( 'All', 'hbook-admin' )
		);
		foreach ( $all_accom as $accom_id => $accom_name ) {
			$new_accom_option = array(
				'value' => $accom_id,
				'label' => $accom_name
			);
			$accom_options[] = $new_accom_option;
			$accom_options_without_all[] = $new_accom_option;
		}

		$pages_options = array();
		$all_pages = $this->hbdb->get_all_pages();
		foreach ( $all_pages as $page ) {
			if ( ! isset( $post->ID ) || ( $page['ID'] != $post->ID ) ) {
				$pages_options[] = array(
					'value' => $page['ID'],
					'label' => $page['post_title']
				);
			}
		}
		if ( $pages_options ) {
			$option_none = array(
				'value' => 'none',
				'label' => esc_html__( 'None', 'hbook-admin' )
			);
			array_unshift( $pages_options, $option_none );
		}

		$columns_options = array(
			1 => esc_html__( 'One column', 'hbook-admin' ),
			2 => esc_html__( 'Two columnns', 'hbook-admin' ),
			3 => esc_html__( 'Three columns', 'hbook-admin' ),
			4 => esc_html__( 'Four columns', 'hbook-admin' )
		);
		$nb_columns_options = array();
		foreach( $columns_options as $value => $label ) {
			$nb_columns_options[] = array(
				'value' => $value,
				'label' => $label
			);
		}

		$data = array(
			'accom_options' => $accom_options,
			'accom_options_without_all' => $accom_options_without_all,
			'pages_options' => $pages_options,
			'current_accom_id' => $current_accom_id,
			'nb_columns_options' => $nb_columns_options
		);
		$this->utils->hb_script_var( 'hbook-booking-form-block', 'hb_blocks_data', $data );
	}

	public function register_blocks() {
		global $wp_version;
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		register_block_type(
			'hbook/booking-form',
			array(
				'render_callback' => array( $this, 'booking_form_render' ),
				'attributes' => array(
					'accom_id' => array(
						'type' => 'string',
						'default' => 'all'
					),
					'search_only' => array(
						'type' => 'boolean',
						'default' => false
					),
					'redirection_page_id' => array(
						'type' => 'string',
						'default' => 'none'
					),
					'thank_you_page_id' => array(
						'type' => 'string',
						'default' => 'none'
					),
				)
			)
		);
		register_block_type(
			'hbook/availability',
			array(
				'render_callback' => array( $this, 'availability_block_render' ),
				'attributes' => array(
					'accom_id' => array(
						'type' => 'string',
						'default' => 'all'
					)
				)
			)
		);
		register_block_type(
			'hbook/starting-price',
			array(
				'render_callback' => array( $this, 'starting_price_block_render' ),
				'attributes' => array(
					'accom_id' => array(
						'type' => 'string',
						'default' => '',
					)
				)
			)
		);
		register_block_type(
			'hbook/rates',
			array(
				'render_callback' => array( $this, 'rates_block_render' ),
				'attributes' => array(
					'accom_id' => array(
						'type' => 'string',
						'default' => ''
					),
					'type' => array(
						'type' => 'string',
						'default' => 'accom'
					),
					'sorting' => array(
						'type' => 'string',
						'default' => 'grouped'
					)
				)
			)
		);
		register_block_type(
			'hbook/accom-list',
			array(
				'render_callback' => array( $this, 'accom_list_block_render' ),
				'attributes' => array(
					'nb_columns' => array(
						'type' => 'string',
						'default' => '2',
					),
					'link_title' => array(
						'type' => 'boolean',
						'default'=> true
					),
					'show_thumb' => array(
						'type' => 'boolean',
						'default' => true
					),
					'link_thumb' => array(
						'type' => 'boolean',
						'default'=> true
					),
					'thumb_width' => array(
						'type' => 'number',
						'default' => 250
					),
					'thumb_height' => array(
						'type' => 'number',
						'default' => 200
					),
					'view_button' => array(
						'type' => 'boolean',
						'default'=> false
					),
					'book_button' => array(
						'type' => 'boolean',
						'default'=> true
					),
					'redirection_page_id' => array(
						'type' => 'string',
						'default' => 'none'
					),
				),
			)
		);
		register_block_type(
			'hbook/resa-summary',
			array(
				'render_callback' => array( $this, 'resa_summary_block_render' ),
				'attributes' => array(),
			)
		);
	}

	public function accom_list_block_render( $attributes ) {
		if ( defined( 'REST_REQUEST' ) || is_admin() ) {
			return;
		}
		require_once $this->utils->plugin_directory . '/front-end/renders/hbook-render.php';
		require_once $this->utils->plugin_directory . '/front-end/renders/accom-list-render.php';
		$accom_list = new HBookAccomList( $this->hbdb, $this->utils );
		$attributes['title_tag'] = 'h2';
		$attributes['accom_ids'] = '';
		$attributes['redirection_url'] = '#';
		if ( $attributes['redirection_page_id'] != 'none' ) {
			$attributes['redirection_url'] = get_permalink( $attributes['redirection_page_id'] );
		}
		return $accom_list->render( $attributes );
	}

	public function resa_summary_block_render( $attributes ) {
		if ( defined( 'REST_REQUEST' ) || is_admin() ) {
			return;
		}
		require_once $this->utils->plugin_directory . '/front-end/renders/hbook-render.php';
		require_once $this->utils->plugin_directory . '/front-end/renders/resa-summary-render.php';
		$resa_summary = new HBookResaSummary( $this->hbdb, $this->utils );
		$attributes = array();
		return $resa_summary->render( $attributes );
	}

	public function availability_block_render( $attributes ) {
		if ( defined( 'REST_REQUEST' ) || is_admin() ) {
			return;
		}
		require_once $this->utils->plugin_directory . '/front-end/renders/hbook-render.php';
		require_once $this->utils->plugin_directory . '/front-end/renders/availability-render.php';
		$availability = new HBookAvailability( $this->hbdb, $this->utils );
		$attributes['calendar_sizes'] = '2x1,1x1';
		return $availability->render( $attributes );
	}

	public function starting_price_block_render( $attributes ) {
		if ( defined( 'REST_REQUEST' ) || is_admin() ) {
			return;
		}
		require_once $this->utils->plugin_directory . '/front-end/renders/hbook-render.php';
		require_once $this->utils->plugin_directory . '/front-end/renders/starting-price-render.php';
		$starting_price = new HBookStartingPrice( $this->hbdb, $this->utils );
		return $starting_price->render( $attributes );
	}

	public function rates_block_render( $attributes ) {
		if ( defined( 'REST_REQUEST' ) || is_admin() ) {
			return;
		}
		if ( $attributes['accom_id'] == '' ) {
			return esc_html__( 'Please select an Accommodation in the Rates settings.', 'hbook-admin' );
		}
		require_once $this->utils->plugin_directory . '/front-end/renders/hbook-render.php';
		require_once $this->utils->plugin_directory . '/front-end/renders/rates-render.php';
		$rates = new HBookRates( $this->hbdb, $this->utils );
		if ( $attributes['sorting'] == 'chrono' ) {
			$attributes['chrono'] = true;
		} else {
			$attributes['chrono'] = false;
		}
		$attributes['rule'] = '';
		$attributes['season'] = '';
		$attributes['seasons'] = '';
		$attributes['days'] = '';
		$attributes['custom_text_after_amount'] = '';
		$attributes['show_season_name'] = true;
		$attributes['nights'] = 0;
		$attributes['show_global_price'] = false;

		return $rates->render( $attributes );
	}

	public function booking_form_render( $attributes ) {
		if ( defined( 'REST_REQUEST' ) || is_admin() ) {
			return;
		}
		require_once $this->utils->plugin_directory . '/front-end/renders/hbook-render.php';
		require_once $this->utils->plugin_directory . '/front-end/renders/booking-form-render.php';
		$booking_form = new HBookBookingForm( $this->hbdb, $this->utils );
		$attributes['search_form_placeholder'] = 'no';
		$attributes['force_display_thumb'] = 'no';
		$attributes['force_display_desc'] = 'no';
		$attributes['form_id'] = '';
		$attributes['is_admin'] = 'no';
		$attributes['redirection_url'] = '#';
		if ( $attributes['redirection_page_id'] != 'none' ) {
			$attributes['redirection_url'] = get_permalink( $attributes['redirection_page_id'] );
		}
		$attributes['thank_you_page_url'] = '';
		if ( $attributes['thank_you_page_id'] != 'none' ) {
			$attributes['thank_you_page_url'] = get_permalink( $attributes['thank_you_page_id'] );
		}
		if ( $attributes['search_only'] ) {
			$attributes['search_only'] = 'yes';
		} else {
			$attributes['search_only'] = 'no';
		}
		if ( $attributes['accom_id'] == 'all' ) {
			$attributes['all_accom'] = 'yes';
			$attributes['accom_id'] = '';
		} else {
			$attributes['all_accom'] = 'no';
		}
		return $booking_form->render( $attributes );
	}

}