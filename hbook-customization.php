<?php

/**
 * Plugin Name: HBook Customization
 * Plugin URI: https://maestrel.com/hbook/
 * Description: Bookings made easy for hospitality businesses.
 * Version: 99999
 * Author: Maestrel
 * Author URI: https://maestrel.com/
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

if (!defined('WP_HBook_FILE')) {
    define('WP_HBook_FILE', plugin_dir_path(__FILE__));
}

if (!defined('WP_HBook_URL')) {
    define('WP_HBook_URL', plugin_dir_url(__FILE__));
}


class HBook {

	public $utils;

	private $version;
	private $hbdb;
	private $accommodation;
	private $resa;
	private $multi_resa;
	private $options_utils;
	private $plugin_check;
	private $plugin_id;
	private $admin_ajax_actions;
	private $front_end_ajax_actions;
	private $resa_ical;
	private $stripe;

	public function __construct() {
		$this->version = '2.0.6';

		require_once plugin_dir_path( __FILE__ ) . 'database-actions/database-actions.php';
		$this->hbdb = new HbDataBaseActions();

		require_once plugin_dir_path( __FILE__ ) . 'accom-post-type/accom-post-type.php';
		$this->accommodation = new HbAccommodation( $this->hbdb );

		require_once plugin_dir_path( __FILE__ ) . 'utils/utils.php';
		$this->utils = new HbUtils( $this->hbdb, $this->version );

		require_once plugin_dir_path( __FILE__ ) . 'utils/resa.php';
		$this->resa = new HbResa( $this->hbdb, $this->utils );

		require_once plugin_dir_path( __FILE__ ) . 'utils/multi-resa.php';
		$this->multi_resa = new HbMultiResa( $this->hbdb, $this->utils );

		require_once plugin_dir_path( __FILE__ ) . 'payment/payment-gateway.php';
		require_once plugin_dir_path( __FILE__ ) . 'payment/paypal/paypal.php';
		require_once plugin_dir_path( __FILE__ ) . 'payment/stripe/stripe.php';
		new HbPayPal( $this->hbdb, $this->version, $this->utils );
		$this->stripe = new HbStripe( $this->hbdb, $this->version );

		require_once plugin_dir_path( __FILE__ ) . 'utils/options-utils.php';
		$this->options_utils = new HbOptionsUtils( $this->hbdb, $this->utils );

		require_once plugin_dir_path( __FILE__ ) . 'utils/resa-ical.php';
		$this->resa_ical = new HbResaIcal( $this->hbdb, $this->utils );

		require_once plugin_dir_path( __FILE__ ) . 'utils/plugin-check.php';
		$plugin_check = new HbPluginCheck( $this->version );

		require_once plugin_dir_path( __FILE__ ) . 'admin-pages/admin-ajax-actions.php';
		$this->admin_ajax_actions = new HbAdminAjaxActions( $this->hbdb, $this->utils, $this->options_utils, $this->stripe, $this->resa, $this->multi_resa );

		$this->install_plugin();

		require_once plugin_dir_path( __FILE__ ) . 'front-end/front-end-ajax-actions.php';
		$this->front_end_ajax_actions = new HbFrontEndAjaxActions( $this->hbdb, $this->utils );

		require_once plugin_dir_path( __FILE__ ) . 'blocks/blocks.php';
		$this->blocks = new HBookBlocks( $this->hbdb, $this->utils );

		register_activation_hook( __FILE__, array( $this, 'plugin_activated' ) );

		register_deactivation_hook( __FILE__, array( $this, 'plugin_deactivated' ) );

		if ( get_option( 'hb_admin_language' ) == 'user' ) {
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		}

		add_action( 'init', array( $this, 'init_plugin' ) );
		add_action( 'init', array( $this->hbdb, 'delete_uncompleted_resa' ) );
		add_action( 'init', array( $this->hbdb, 'delete_old_email_logs' ) );
		add_action( 'init', array( $this->utils, 'export_lang_file' ) );
		add_action( 'init', array( $this->utils, 'export_settings' ) );
		add_action( 'init', array( $this->utils, 'export_resa' ) );
		add_action( 'init', array( $this->utils, 'export_customers' ) );
		add_action( 'init', array( $this->utils, 'open_document' ) );
		add_action( 'init', array( $this->utils, 'reset_resa_invoice_counter' ) );

		add_filter( 'template_include', array( $this->accommodation, 'filter_template_page' ) );
		add_action( 'wp_enqueue_scripts', array( $this->utils, 'frontend_css_files' ) );
		add_action( 'wp_head', array( $this->utils, 'frontend_basic_css' ) );
		add_action( 'wp_head', array( $this->utils, 'frontend_calendar_css' ) );
		add_action( 'wp_head', array( $this->utils, 'frontend_buttons_css' ) );
		add_action( 'wp_head', array( $this->utils, 'frontend_inputs_selects_css' ) );
		add_action( 'wp_head', array( $this->utils, 'frontend_custom_css' ) );
		add_action( 'admin_menu', array( $this, 'create_plugin_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_wp_admin_style' ) );
		add_action( 'admin_notices', array( $this, 'hbook_activation_notice' ) );
		add_action( 'hb_ical_synchronized', array( $this, 'ical_update_calendars' ) );
		add_filter( 'cron_schedules', array( $this, 'ical_custom_scheduled_frequency' ) );
		add_action( 'hb_send_scheduled_emails', array( $this->utils, 'send_scheduled_emails' ) );
		add_action( 'hb_check_plugin', array( $this->utils, 'check_plugin' ) );
		if ( get_option( 'hb_image_resizing') == 'static' ) {
			add_action( 'after_setup_theme', array( $this->utils, 'add_image_sizes' ) );
		}

		$hb_shortcodes = array(
			'hb_booking_form',
			'hb_accommodation_list',
			'hb_availability',
			'hb_rates',
			'hb_paypal_confirmation',
			'hb_reservation_summary',
			'hb_starting_price',
		);
		foreach ( $hb_shortcodes as $shortcode ) {
			add_shortcode( $shortcode, array( $this, 'hb_shortcodes' ) );
		}

		$front_end_ajax_action = array(
			'hb_get_available_accom',
			'hb_get_summary',
			'hb_create_resa',
			'hb_verify_coupon',
		);
		foreach( $front_end_ajax_action as $action ) {
			add_action( 'wp_ajax_' . $action, array( $this->front_end_ajax_actions, $action ) );
			add_action( 'wp_ajax_nopriv_' . $action, array( $this->front_end_ajax_actions, $action ) );
		}

		$admin_ajax_action = array(
			'hb_update_db',
			'hb_update_misc_settings',
			'hb_update_ical_settings',
			'hb_update_appearance_settings',
			'hb_update_payment_settings',
			'hb_update_forms_settings',
			'hb_update_details_form_settings',
			'hb_update_strings',
			'hb_update_rates',
			'hb_cancel_resas',
			'hb_confirm_resas',
			'hb_update_resa_info',
			'hb_edit_options_get_editor',
			'hb_update_resa_options',
			'hb_update_resa_comment',
			'hb_edit_accom_get_avai',
			'hb_update_resa_accom',
			'hb_resa_create_new_customer',
			'hb_save_selected_customer',
			'hb_update_customer',
			'hb_update_resa_price',
			'hb_update_resa_paid',
			'hb_update_resa_discount',
			'hb_resa_check_price',
			'hb_delete_resas',
			'hb_delete_customer',
			'hb_add_blocked_accom',
			'hb_delete_blocked_accom',
			'hb_update_booking_rules',
			'hb_delete_sync_errors',
			'hb_resa_charging',
			'hb_resa_refunding',
			'hb_update_resa_dates',
			'hb_send_email_customer',
			'hb_save_resa_sorting',
			'hb_save_customers_sorting',
		);
		foreach ( $admin_ajax_action as $action ) {
			add_action( 'wp_ajax_' . $action, array( $this->admin_ajax_actions, $action ) );
		}

		add_action( 'init', array( $this->accommodation, 'create_accommodation_post_type' ) );
		add_action( 'pre_get_posts', array( $this->accommodation, 'admin_accom_order' ) );
		add_action( 'edit_form_before_permalink', array( $this->accommodation, 'display_accom_id' ) );
		add_action( 'add_meta_boxes', array( $this->accommodation, 'accommodation_meta_box' ) );
		add_action( 'save_post_hb_accommodation', array( $this->accommodation, 'save_accommodation_meta' ) );
		add_action( 'delete_post', array( $this->hbdb, 'deleted_accom' ) );
		add_action( 'publish_hb_accommodation', array( $this->hbdb, 'published_accom' ) );
		add_action( 'enqueue_block_editor_assets', array( $this->blocks, 'block_editor_assets' ) );
		add_filter( 'block_categories_all', array( $this->blocks, 'add_blocks_category' ) );
		add_action( 'init', array( $this->blocks, 'register_blocks' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_plugin_page_settings_link' ) );
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'hbook-admin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/admin-language-files/' );
	}

	public function install_plugin() {
		$installed_version = get_option( 'hbook_installing_version' );
		if ( ! $installed_version ) {
			$installed_version = get_option( 'hbook_version' );
		}
		if ( ! $installed_version || $installed_version != $this->version ) {
			if ( get_option( 'hbook_version' ) ) {
				update_option( 'hbook_previous_version', get_option( 'hbook_version' ) );
			} else {
				update_option( 'hbook_previous_version', 'none' );
			}
			$installing = get_option( 'hbook_installing' );
			if ( $installing && $installing != 1 ) {
				$install_start_time = strtotime( substr( $installing, 0, 19 ) );
				$elapsed_time = time() - $install_start_time;
				if ( $elapsed_time < 300 ) {
					return;
				}
			}
			update_option( 'hbook_installing', current_time( 'mysql', 1 ) . '-' . get_option( 'hbook_version' ) . '-' . $this->version );
		} else {
			return;
		}

		require_once plugin_dir_path( __FILE__ ) . 'database-actions/database-creation.php';
		require_once plugin_dir_path( __FILE__ ) . 'database-actions/database-schema.php';
		$hbdb_schema = new HbDataBaseSchema( $this->hbdb );
		$hbdb_creation = new HbDataBaseCreation( $this->hbdb, $this->utils, $hbdb_schema );

		if ( $installed_version && $installed_version != $this->version ) {
			$hbdb_creation->alter_data_before_table_update( $installed_version );
		}
		if ( ! $installed_version || $installed_version != $this->version ) {
			$hbdb_creation->create_update_plugin_tables();
			$hbdb_creation->insert_strings( $installed_version );
		}
		if ( ! $installed_version ) {
			$hbdb_creation->insert_data();
			set_transient( 'hbook-activation-notice', true, 5 );
		} else if ( $installed_version != $this->version ) {
			$hbdb_creation->alter_data( $installed_version );
		}
		if ( ! $installed_version || $installed_version != $this->version ) {
			$this->options_utils->init_options();
			update_option( 'hbook_version', $this->version );
		}
		delete_option( 'hbook_installing' );
		delete_option( 'hbook_installing_version' );
	}

	public function plugin_activated() {
		update_option( 'hb_flush_rewrite', 'flush' );
		wp_schedule_event( time(), 'hb_ical_custom_frequency', 'hb_ical_synchronized' );
		wp_schedule_event( time(), 'hourly', 'hb_send_scheduled_emails' );
		wp_schedule_event( time(), 'daily', 'hb_check_plugin' );
		$roles_caps = $this->utils->get_hbook_roles_caps();
		foreach ( $roles_caps as $role => $info ) {
			$caps = array();
			$caps_list = apply_filters( $role . '_capabilities', $info['caps'] );
			foreach ( $caps_list as $key => $cap ) {
				$caps[ $cap ] = true;
			}
			add_role(
				$role,
				$info['label'],
				$caps
			);
		}
		$admin_role = get_role( 'administrator' );
		$hb_caps = $roles_caps['hb_hbook_manager']['caps'];
		foreach ( $hb_caps as $key => $cap ) {
			$admin_role->add_cap( $cap );
		}
	}

	public function plugin_deactivated() {
		wp_clear_scheduled_hook( 'hb_ical_synchronized' );
		wp_clear_scheduled_hook( 'hb_send_scheduled_emails' );
		wp_clear_scheduled_hook( 'hb_check_plugin' );
		$roles_caps = $this->utils->get_hbook_roles_caps();
		foreach ( $roles_caps as $role => $info ) {
			remove_role( $role );
		}
		$admin_role = get_role( 'administrator' );
		$hb_caps = $roles_caps['hb_hbook_manager']['caps'];
		foreach ( $hb_caps as $cap ) {
			$admin_role->remove_cap( $cap );
		}
	}

	public function ical_custom_scheduled_frequency( $schedules ) {
		$schedules['hb_ical_custom_frequency'] = array(
			'interval' => get_option( 'hb_ical_frequency' ),
			'display' => esc_html__( 'HBook custom frequency', 'hbook-admin' ) . ' (' . get_option( 'hb_ical_frequency' ) . 's)'
		);
		return $schedules;
	}

	public function ical_update_calendars() {
		$this->resa_ical->update_calendars();
	}

	public function add_plugin_page_settings_link( $links ) {
		$setting_link = '<a href="' . admin_url( 'admin.php?page=hb_menu' ) . '">';
		$setting_link .= esc_html__( 'Settings', 'hbook-admin' );
		$setting_link .= '</a>';
		array_unshift( $links, $setting_link );
		return $links;
	}

	public function hbook_activation_notice() {
		if( get_transient( 'hbook-activation-notice' ) ) {
			delete_transient( 'hbook-activation-notice' );
		?>

		<div class="updated">
			<p>
				<?php
				$thanks_msg = esc_html__( 'Thanks for using HBook plugin.', 'hbook-admin' );
				if ( strpos( $thanks_msg, 'HBook' ) ) {
					$thanks_msg = str_replace( 'HBook', '<b>HBook</b>', $thanks_msg );
				} else {
					$thanks_msg = 'Thanks for using <b>HBook</b> plugin.';
				}

				global $locale;
				if ( $locale == 'fr_FR' ) {
					$doc_lang = 'fr';
				} else {
					$doc_lang = 'en';
				}
				$doc_msg = esc_html__( 'Before setting the plugin up do not forget to have a look at the %s.', 'hbook-admin' );
				$doc_word = esc_html__( 'documentation', 'hbook-admin' );
				if ( strpos( $doc_msg, '%s' ) ) {
					$doc_msg = str_replace( '%s', '<a target="_blank" href="https://maestrel.com/documentation/hbook/' . $doc_lang . '/">'  . $doc_word . '</a>', $doc_msg );
				} else {
					$doc_msg .= ' (<a target="_blank" href="https://maestrel.com/documentation/hbook/' . $doc_lang . '/">'  . $doc_word . '</a>)';
				}
				$knowledge_msg = esc_html__( 'For any specific issue you can consult our %s.', 'hbook-admin' );
				$knowledge_word = esc_html__( 'knowledgebase', 'hbook-admin' );
				if ( strpos( $knowledge_msg, '%s' ) ) {
					$knowledge_msg = str_replace( '%s', '<a target="_blank" href="https://maestrel.com/knowledgebase/">' . $knowledge_word . '</a>', $knowledge_msg );
				} else {
					$knowledge_msg .= ' (<a target="_blank" href="https://maestrel.com/knowledgebase/">' . $knowledge_word . '</a>)';
				}
				?>
				<?php echo( wp_kses_post( $thanks_msg ) ); ?>
				<br/>
				<?php echo( wp_kses_post( $doc_msg ) ); ?>
				<br/>
				<?php
				echo( wp_kses_post( $knowledge_msg ) );
				$admin_lang = '';
				if ( $locale == 'fr_FR' ) {
					$admin_lang = 'French';
				} else if ( $locale == 'es_ES' ) {
					$admin_lang = 'Spanish';
				}
				if ( $admin_lang ) {
				?>
				<br/>
				HBook administration is available in <?php echo( esc_html( $admin_lang ) ); ?>.
				You can set HBook administration language in
				<a href="<?php echo( esc_url( admin_url( 'admin.php?page=hb_misc' ) ) ); ?>">HBook > Misc</a>.
				<?php
				}
				?>
			</p>
		</div>

		<?php
		}
	}

	public function init_plugin() {
		if (
			isset( $_POST['hb-purchase-code'] ) &&
			wp_verify_nonce( $_POST['hb_nonce_licence'], 'hb_nonce_licence' ) &&
			current_user_can( 'manage_hb_licence' )
		) {
			$this->utils->verify_purchase_code( wp_strip_all_tags( trim( $_POST['hb-purchase-code'] ) ) );
		}
		if (
			isset( $_POST['hb-addon-purchase-code'] ) &&
			wp_verify_nonce( $_POST['hb_addons_nonce_licence'], 'hb_addons_nonce_licence' ) &&
			current_user_can( 'manage_hb_licence' )
		) {
			$this->utils->verify_addon_purchase_code( wp_strip_all_tags( trim( $_POST['hb-addon-purchase-code'] ) ), wp_strip_all_tags( trim( $_POST['hb-addon-name'] ) ) );
		}
		add_filter( 'widget_text', 'do_shortcode' );
		add_feed( 'hbook-calendar.ics', array( $this->resa_ical, 'export_ical' ) );
		add_feed( 'hbook-all-calendars.ics', array( $this->resa_ical, 'export_all_icals' ) );
		if ( ! wp_next_scheduled( 'hb_ical_synchronized' ) ) {
			wp_schedule_event( time(), 'hb_ical_custom_frequency', 'hb_ical_synchronized' );
		}
		if ( ! wp_next_scheduled( 'hb_send_scheduled_emails' ) ) {
			wp_schedule_event( time(), 'hourly', 'hb_send_scheduled_emails' );
		}
		if ( ! wp_next_scheduled( 'hb_check_plugin' ) ) {
			wp_schedule_event( time(), 'daily', 'hb_check_plugin' );
		}
	}

	public function enqueue_wp_admin_style() {
		wp_enqueue_script( 'jquery' );
		$this->utils->hb_enqueue_script( 'hb-global-js', '/admin-pages/js/hb-global.js' );

		global $post_type;
		if ( $post_type == 'hb_accommodation' ) {
			$this->utils->hb_enqueue_style( 'hb-accom-style', '/accom-post-type/accom-post-type.css' );
			$this->utils->hb_enqueue_script( 'hb-accom-script', '/accom-post-type/accom-post-type.js' );
			$hb_accom_post_text = array(
				'delete_accom_num_name' => esc_html__( 'Delete', 'hbook-admin' ),
				'delete_accom_num_name_text' => esc_html__( 'Delete accommodation %s? Note that all reservations linked to the %s will also be deleted.', 'hbook-admin' ),
				'delete_accom_text' => esc_html__( 'Move this accommodation type to trash? Note that all reservations linked to this accommodtion will also be deleted when you will empty the trash.', 'hbook-admin' ),
				'starting_price_not_number' => esc_html__( 'Starting price should be a number (without currency symbol).', 'hbook-admin' ),
				'accom_number_zero' => esc_html__( 'There must be at least one accommodation.', 'hbook-admin' ),
			);
			$this->utils->hb_script_var( 'hb-accom-script', 'hb_accom_post_text', $hb_accom_post_text );
		}
	}

	public function enqueue_scripts( $hook ) {
		$this->utils->hb_enqueue_style( 'hb-admin-pages-style', '/admin-pages/css/hb-admin-pages-style.css' );

		wp_enqueue_script( 'jquery' );
		$this->utils->hb_enqueue_script( 'hb-settings', '/admin-pages/js/hb-settings.js' );

		$page_name = str_replace( 'hb_', '', $_GET['page'] );

		$knockout_pages = array(
			'customers',
			'emails',
			'documents',
			'fees',
			'details',
			'options',
			'rates',
			'reservations',
			'rules',
			'seasons',
		);
		if ( in_array( $page_name, $knockout_pages ) ) {
			$this->utils->hb_enqueue_script( 'hb-knockout', '/admin-pages/js/knockout-3.2.0.js' );
			$this->utils->hb_enqueue_script( 'hb-settings-knockout', '/admin-pages/js/hb-settings-knockout.js' );
		}

		$static_settings_pages = array(
			'appearance',
			'forms',
			'misc',
			'payment',
			'ical',
		);
		if ( in_array( $page_name, $static_settings_pages ) ) {
			$this->utils->hb_enqueue_script( 'hb-settings-static', '/admin-pages/js/hb-settings-static.js', array( 'jquery' ) );
		}

		if ( $page_name == 'fees' || $page_name == 'options' || $page_name == 'rates' ) {
			$this->utils->hb_enqueue_script( 'hb-options-and-fees-script', '/admin-pages/js/hb-options-and-fees.js' );
		}

		if ( $page_name == 'fees' || $page_name == 'options' || $page_name == 'rates' || $page_name == 'reservations' ) {
			$this->utils->hb_enqueue_script( 'hb-price-utils', '/admin-pages/js/hb-price-utils.js' );
			add_action( 'admin_footer', array( $this->utils, 'currency_symbol_js' ) );
			$this->utils->hb_script_var( 'hb-price-utils', 'hb_currency_pos', get_option( 'hb_currency_position' ) );
			$this->utils->hb_script_var( 'hb-price-utils', 'hb_currency', get_option( 'hb_currency' ) );
		}

		if ( ( $page_name == 'seasons' ) || ( $page_name == 'reservations' ) || ( $page_name == 'rates' ) ) {
			$this->utils->load_datepicker();
		}

		if ( $page_name == 'reservations' ) {
			wp_enqueue_script( 'jquery-ui-resizable' );
			$this->utils->hb_enqueue_script( 'hb-resa-utils', '/admin-pages/pages/reservations/resa-utils.js' );
			$this->utils->hb_enqueue_script( 'hb-resa-cal', '/admin-pages/pages/reservations/resa-cal.js' );
			$this->utils->hb_enqueue_script( 'hb-resa-export', '/admin-pages/pages/reservations/resa-export.js' );
			$this->utils->hb_enqueue_style( 'hb-resa-cal-style', '/admin-pages/pages/reservations/resa-cal.css' );
			$this->utils->hb_enqueue_style( 'hb-admin-add-resa-style', '/admin-pages/pages/reservations/admin-add-resa.css' );
		}

		if ( $page_name == 'details' ) {
			wp_enqueue_script( 'jquery-ui-sortable' );
			$this->utils->hb_enqueue_script( 'hb-knockout-sortable', '/admin-pages/js/knockout-sortable.min.js' );
		}

		if ( $page_name == 'appearance' ) {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
		}

		if ( ( $page_name == 'reservations' ) || ( $page_name == 'emails' ) ) {
			wp_enqueue_media();
			$this->utils->hb_enqueue_script( 'hb-media-attachments', '/admin-pages/js/hb-media-attachments.js' );
		}

		$this->utils->hb_enqueue_style( 'hb-' . $page_name . '-style', '/admin-pages/pages/' . $page_name . '/' . $page_name . '.css' );
		$this->utils->hb_enqueue_script( 'hb-' . $page_name . '-script', '/admin-pages/pages/' . $page_name . '/' . $page_name . '.js' );

		add_action( 'admin_head', array( $this->utils, 'admin_custom_css' ) );
	}

	public function create_plugin_admin_menu() {
		if ( get_option( 'hb_valid_purchase_code' ) == 'yes' || strpos( site_url(), '127.0.0.1' ) || strpos( site_url(), 'localhost' ) ) {
			$page = add_menu_page( esc_html__( 'Reservations', 'hbook-admin' ), esc_html__( 'Reservations', 'hbook-admin' ), 'read_resa', 'hb_reservations', array( $this, 'display_admin_page' ), 'dashicons-calendar-alt', '2.82' );
			add_action( 'admin_print_styles-' . $page, array( $this, 'enqueue_scripts' ) );
		}

		$page = add_menu_page(
			'HBook',
			'HBook',
			'manage_hb_menu',
			'hb_menu',
			array( $this, 'display_admin_page' ),
			'data:image/svg+xml;base64,' .
			base64_encode(
				'<svg version="1.0" xmlns="http://www.w3.org/2000/svg"
				width="64.000000pt" height="64.000000pt" viewBox="0 0 64.000000 64.000000"
				preserveAspectRatio="xMidYMid meet">
					<g transform="translate(0.000000,64.000000) scale(0.100000,-0.100000)"
					fill="#000000" stroke="none">
						<path d="M240 617 c-139 -39 -224 -152 -224 -297 0 -88 22 -145 79 -208 117
						-130 333 -130 450 0 57 63 79 120 79 208 0 88 -22 145 -79 208 -72 80 -203
						118 -305 89z m-70 -242 l0 -45 45 0 45 0 0 45 c0 43 1 45 30 45 l30 0 0 -115
						0 -115 -30 0 c-29 0 -30 1 -30 50 l0 50 -45 0 -45 0 0 -50 c0 -49 -1 -50 -30
						-50 l-30 0 0 115 0 115 30 0 c29 0 30 -2 30 -45z m344 29 c18 -18 21 -65 6
						-80 -8 -8 -6 -15 5 -24 20 -17 19 -69 -2 -92 -13 -14 -31 -18 -95 -18 l-78 0
						0 115 0 115 74 0 c54 0 79 -4 90 -16z" />
						<path d="M410 355 c0 -18 5 -25 19 -25 26 0 44 23 29 38 -22 22 -48 14 -48
						-13z" />
						<path d="M410 260 c0 -30 11 -37 44 -24 21 8 21 40 0 48 -33 13 -44 6 -44 -24z" />
					</g>
				</svg>'
			),
			'122.3'
		);
		add_action( 'admin_print_styles-' . $page, array( $this, 'enqueue_scripts' ) );

		$hbook_pages = $this->utils->get_hbook_pages();
		foreach ( $hbook_pages as $p ) {
			$page_cap = 'manage_' . $p['id'];
			$page = add_submenu_page( 'hb_menu', $p['name'], $p['name'], $page_cap, $p['id'], array( $this, 'display_admin_page' ) );
			add_action( 'admin_print_styles-' . $page, array( $this, 'enqueue_scripts' ) );
			if ( $p['id'] == 'hb_accommodation' ) {
				add_action( 'load-' . $page, array( $this->accommodation, 'redirect_hb_menu_accom_page' ) );
			}
		}
	}

	public function display_admin_page() {
		$hb_page = $_GET['page'];
		$page_id = str_replace( 'hb_', '', $hb_page );
		$page_cap = 'manage_' . $hb_page;
		if ( current_user_can( $page_cap ) || ( $page_id == 'reservations' && current_user_can( 'read_resa' ) ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'admin-pages/admin-page.php';
			require_once plugin_dir_path( __FILE__ ) . 'admin-pages/pages/' . $page_id . '/' . $page_id . '.php';
			$page_class = 'HbAdminPage' . ucfirst( $page_id );
			$admin_page = new $page_class( $page_id, $this->hbdb, $this->utils, $this->options_utils );
			$admin_page->display();
		}
	}

	public function hb_shortcodes( $atts, $content = '', $shortcode_name ) {
		if ( defined( 'REST_REQUEST' ) || is_admin() || $shortcode_name == 'hb_paypal_confirmation' ) {
			return;
		}
		require_once plugin_dir_path( __FILE__ ) . 'front-end/shortcodes.php';
		$shortcodes = new HBookShortcodes( $this->hbdb, $this->utils );
		$shortcode_function = str_replace( 'hb_', '', $shortcode_name );
		return $shortcodes->$shortcode_function( $atts );
	}
}

function hbook_is_active() {
}

$hbook = new HBook();