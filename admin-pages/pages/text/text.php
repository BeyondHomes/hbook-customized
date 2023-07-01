<?php
class HbAdminPageText extends HbAdminPage {

	private $sections;
	private $strings;
	private $langs;
	private $variables;

	public function __construct( $page_id, $hbdb, $utils, $options_utils ) {
		$this->data = array(
			'hb_text' => array(
				'form_saved' => esc_html__( 'All text has been saved.', 'hbook-admin' ),
			)
		);
		$this->sections = array(
			'search-form-txt' => array(
				'title' => esc_html__( 'Search form text', 'hbook-admin' ),
				'strings' => $utils->strings_utils->get_search_form_txt()
			),
			'search-form' => array(
				'title' => esc_html__( 'Search form messages', 'hbook-admin' ),
				'strings' => $utils->strings_utils->get_search_form_msg()
			),
			'accom-select' => array(
				'title' => esc_html__( 'Accommodation selection', 'hbook-admin' ),
				'strings' => array_merge( $utils->strings_utils->get_accom_selection_txt(), $hbdb->get_fee_names() )
			),
			'options-select' => array(
				'title' => esc_html__( 'Extra services selection', 'hbook-admin' ),
				'strings' => array_merge( $utils->strings_utils->get_options_selection_txt(), $hbdb->get_option_names() )
			),
			'details-form-txt' => array(
				'title' => esc_html__( 'Booking details form text', 'hbook-admin' ),
				'strings' => $hbdb->get_details_form_labels()
			),
			'details-form-msg' => array(
				'title' => esc_html__( 'Booking details form messages', 'hbook-admin' ),
				'strings' => $utils->strings_utils->get_details_form_msg()
			),
			'coupons' => array(
				'title' => esc_html__( 'Coupons', 'hbook-admin' ),
				'strings' => $utils->strings_utils->get_coupons_txt()
			),
			'summary' => array(
				'title' => esc_html__( 'Summary', 'hbook-admin' ),
				'strings' => $utils->strings_utils->get_summary_txt()
			),
			'payment-choice' => array(
				'title' => esc_html__( 'Payment choice', 'hbook-admin' ),
				'strings' => $utils->strings_utils->get_payment_type_choice()
			),
			'stripe' => array(
				'title' => esc_html__( 'Stripe payment', 'hbook-admin' ),
				'strings' => $utils->strings_utils->get_stripe_txt()
			),
			'paypal' => array(
				'title' => esc_html__( 'Paypal payment', 'hbook-admin' ),
				'strings' => $utils->strings_utils->get_paypal_txt()
			),
			'external-payment-desc' => array(
				'title' => esc_html__( 'External payment description', 'hbook-admin' ),
				'strings' => $utils->strings_utils->get_external_payment_desc_txt()
			),
			'book-now-area' => array(
				'title' => esc_html__( 'Book now area', 'hbook-admin' ),
				'strings' => $utils->strings_utils->get_book_now_area_txt()
			),
			'error-msg' => array(
				'title' => esc_html__( 'Error messages', 'hbook-admin' ),
				'strings' => $utils->strings_utils->get_error_form_msg()
			),
			'cal-legend' => array(
				'title' => esc_html__( 'Calendars legend', 'hbook-admin' ),
				'strings' => $utils->strings_utils->get_cal_legend_txt()
			),
			'rates-table' => array(
				'title' => esc_html__( 'Rates table', 'hbook-admin' ),
				'strings' => array_merge( $utils->strings_utils->get_rates_table_txt(), $hbdb->get_season_names() )
			),
			'invoice-table' => array(
				'title' => esc_html__( 'Invoice table', 'hbook-admin' ),
				'strings' => $utils->strings_utils->get_invoice_table_txt()
			),
			'accom-list' => array(
				'title' => esc_html__( 'Accommodation list', 'hbook-admin' ),
				'strings' => $utils->strings_utils->get_accom_list_txt()
			)
		);
		$this->sections = apply_filters( 'hb_strings', $this->sections );
		$this->strings = $hbdb->get_all_strings();
		$this->langs = $utils->get_langs();
		$this->variables = $utils->strings_utils->get_txt_variables();
		parent::__construct( $page_id, $hbdb, $utils, $options_utils );
	}

	public function display() {
	?>

	<div class="wrap">

		<form id="hb-admin-form">

			<input id="hb-nonce" type="hidden" name="nonce" value="" />
			<input id="hb-action" type="hidden" name="action" value="" />

			<div class="hb-clearfix">
				<h1><?php esc_html_e( 'HBook text', 'hbook-admin' ); ?></h1>
				<?php $this->display_right_menu(); ?>
				<div class="hb-options-save-beside-title">
					<div>
						<a href="#" class="hb-options-save button-primary"><?php esc_html_e( 'Save changes', 'hbook-admin' ); ?></a>
					</div>
					<div class="hb-ajaxing">
						<span class="spinner"></span>
						<span><?php esc_html_e( 'Saving...', 'hbook-admin' ); ?></span>
					</div>
					<div class="hb-saved"></div>
				</div>
			</div>
			<hr/>
			<?php esc_html_e( 'Here you can modify any front-end strings for any set language.', 'hbook-admin' ); ?>
			<h3><?php esc_html_e( 'Text sections' , 'hbook-admin' ); ?></h3>
			<ul>
				<?php foreach ( $this->sections as $section_id => $section ) { ?>
					<li><a href="<?php echo( esc_url( '#hb-text-section-' .  $section_id ) ); ?>"><?php echo( esc_html( $section['title'] ) ); ?></a></li>
				<?php } ?>
			</ul>
			<hr/>
			<?php
			foreach ( $this->sections as $section_id => $section ) {
			?>

			<h3 id="<?php echo( esc_attr( 'hb-text-section-' . $section_id ) ); ?>"><?php echo( esc_html( $section['title'] ) ); ?></h3>

			<?php if ( $section_id == 'details-form-txt' ) { ?>
			<p><i><?php esc_html_e( 'Leave the following fields blank to use their default name.', 'hbook-admin' ); ?></i></p>
			<?php } ?>

				<?php
				foreach ( $section['strings'] as $string_id => $string_name ) {
				?>

				<h4><?php echo( esc_html( $string_name ) ); ?></h4>
				<?php
				if ( isset( $this->variables[ $string_id ] ) ) {
				?>
					<small class="hb-variable-desc">
					<?php
					if ( count( $this->variables[ $string_id ] ) > 1 ) {
						esc_html_e( 'You can use these variables:', 'hbook-admin' );
					} else {
						esc_html_e( 'You can use this variable:', 'hbook-admin' );
					}
					echo( ' ' );
					echo( esc_html( implode( ', ', $this->variables[ $string_id ] ) ) );
					?>
					</small>
				<?php
				}
				?>
				<p>
				<?php
					foreach ( $this->langs as $locale => $lang_name ) {
						$translation = '';
						if ( isset( $this->strings[ $string_id ][ $locale ] ) ) {
							$translation = $this->strings[ $string_id ][ $locale ];
						}
						if ( count( $this->langs ) > 1 ) {
				?>
					<label class="hb-string-lang"><?php echo( esc_html( $lang_name ) ); ?><span> (<?php echo( esc_html( $locale ) );?>)</span></label><br/>
				<?php
						}
				?>
					<input type="text" name="<?php echo( esc_attr( 'string-id-' . $string_id . '-in-' . $locale ) ); ?>" value="<?php echo( esc_attr( $translation ) ); ?>" />
				</p>
				<?php
					}
				}
				?>

				<br class="hb-before-save-button" />
				<?php $this->options_utils->display_save_options_section(); ?>

			<?php
			}
			?>

		</form>

	</div><!-- end .wrap -->

	<?php
	}
}