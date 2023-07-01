<?php
class HbAdminPageLicence extends HbAdminPage {

	public function __construct( $page_id, $hbdb, $utils, $options_utils ) {
		$this->data = array(
			'hb_text' => array(
				'remove_purchase_code' => esc_html__( 'Remove purchase code for this website?', 'hbook-admin' ),
			)
		);
		parent::__construct( $page_id, $hbdb, $utils, $options_utils );
	}

	public function display() {
	?>

	<div class="wrap">

		<h2><?php esc_html_e( 'Licence', 'hbook-admin' ); ?></h2>

		<hr/>

		<?php $this->display_right_menu(); ?>
		<p><a href="https://maestrel.com/knowledgebase/?article=34"><?php esc_html_e( 'Where can I find my purchase code?', 'hbook-admin' ); ?></a></p>

		<h3><?php esc_html_e( 'Activate your HBook licence', 'hbook-admin' ); ?></h2>
		<form id="hb-verify-licence" method="post">

			<?php
			$valid_purchase_code = get_option( 'hb_valid_purchase_code' );

			if ( $valid_purchase_code == 'error' ) :
				if ( get_option( 'hb_purchase_code_error' ) == 'no-online-validation' ) {
					$error_text = esc_html__( 'To activate your licence, you will need a validation code. Click on this link: https://maestrel.com/hbook-licence-validation. Follow the steps to obtain your validation code and paste it below. Check %s for step by step guidance.', 'hbook-admin' );
					if ( strpos( $error_text, 'https://maestrel.com/hbook-licence-validation' ) ) {
						$error_text = str_replace( 'https://maestrel.com/hbook-licence-validation', '<a target="_blank" href="https://maestrel.com/hbook-licence-validation">https://maestrel.com/hbook-licence-validation</a>', $error_text );
					} else {
						$error_text .= ' - <a target="_blank" href="https://maestrel.com/hbook-licence-validation">https://maestrel.com/hbook-licence-validation</a>';
					}
					if ( strpos( $error_text, '%s' ) ) {
						$error_text = str_replace(
							'%s',
							'<a target="_blank" href="https://maestrel.com/knowledgebase/?article=56">' .
							esc_html__( 'this article of the knowledgebase', 'hbook-admin' ) .
							'</a>', $error_text
						);
					} else {
						$error_text .= ' - <a target="_blank" href="https://maestrel.com/knowledgebase/?article=56">https://maestrel.com/knowledgebase/?article=56</a>';
					}
				} else if ( get_option( 'hb_purchase_code_error' ) == 'wrong-validation-code' ) {
					$error_text = esc_html__( 'The code you entered is not valid. Please try again. You can also contact %s if you need further help.', 'hbook-admin' );
					$hwp_support_string = esc_html__( 'Maestrel support', 'hbook-admin' );
					if ( strpos( $error_text, '%s' ) ) {
						$error_text = str_replace( '%s', '<a target="_blank" href="https://maestrel.com/contact-support/">' . $hwp_support_string . '</a>', $error_text );
					} else {
						$error_text .= ' - <a target="_blank" href="https://maestrel.com/contact-support/">Maestrel support</a>';
					}
				}
				?>

				<p><?php echo( wp_kses_post( $error_text ) ); ?></p>

				<p>
					<label><?php esc_html_e( 'Your website url:', 'hbook-admin' ); ?></label><br/>
					<input type="text" onclick="this.select()" onfocus="this.select()" readonly="readonly" value="<?php echo( esc_attr( site_url() ) ); ?>" />
				</p>
				<p>
					<label><?php esc_html_e( 'Enter validation code', 'hbook-admin' ); ?></label><br/>
					<input type="text" name="hb-licence-validation-code" />
				</p>
				<p style="display:none">
					<input type="text" name="hb-forced-licence-validation" />
					<?php echo( wp_kses_post( get_option( 'hb_purchase_code_error_text' ) ) ); ?>
				</p>

				<?php
				delete_option( 'hb_purchase_code_error' );
				delete_option( 'hb_purchase_code_error_text' );

			endif;
			?>

			<p>
				<label><?php esc_html_e( 'Enter your purchase code', 'hbook-admin' ); ?></label><br/>
				<input type="text" name="hb-purchase-code" size="50" value="<?php echo( esc_attr( get_option( 'hb_purchase_code' ) ) ); ?>" />
				<input type="submit" value="<?php esc_attr_e( 'Verify purchase code', 'hbook-admin' ) ?>" class="button-primary" />
			</p>

			<?php if ( in_array( $valid_purchase_code, array( 'yes', 'no', 'already', 'removed', 'pending' ) ) ) : ?>

			<p <?php if ( $valid_purchase_code == 'no' ) { echo( 'class="hb-purchase-code-not-valid-msg"'); } ?>>
				<?php
				if ( $valid_purchase_code == 'yes' ) {
					esc_html_e( 'Your purchase code is valid.', 'hbook-admin' );
				} else if ( $valid_purchase_code == 'no' ) {
					esc_html_e( 'Your purchase code is not valid.', 'hbook-admin' );
				} else if ( $valid_purchase_code == 'already' ) {
					esc_html_e( 'This purchase code is already in use.', 'hbook-admin' );
				} else if ( $valid_purchase_code == 'removed' ) {
					esc_html_e( 'The purchase code has been removed.', 'hbook-admin' );
					delete_option( 'hb_valid_purchase_code' );
				} else if ( $valid_purchase_code == 'pending' ) {
					esc_html_e( 'This purchase code has not been verified.', 'hbook-admin' );
				}
				?>
			</p>

			<?php
			endif;

			if ( $valid_purchase_code == 'error' ) {
				update_option( 'hb_valid_purchase_code', 'pending' );
			}

			if ( $valid_purchase_code == 'yes' ) : ?>
			<p>
				<a class="hb-remove-purchase-code" href="#"><?php esc_html_e( 'Remove purchase code for this website.', 'hbook-admin' ); ?></a>
			</p>
			<?php
			endif;

			wp_nonce_field( 'hb_nonce_licence', 'hb_nonce_licence' );
			?>

		</form>

		<?php
		$gateway_list = $this->utils->get_payment_gateways();
		foreach( $gateway_list as $gateway => $data ) {
			if ( ( 'paypal' != $data->id ) && ( 'stripe' != $data->id ) ) {
				?>
				<hr/>
				<h3><?php printf( esc_html__( 'Activate your %s licence', 'hbook-admin' ), esc_html( $data->name ) ); ?></h3>
				<?php
				$purchase_code_option = 'hb_' . $data->id . '_purchase_code';
				$valid_purchase_code_option = 'hb_' . $data->id . '_valid_purchase_code';
				$error_option = 'hb_' . $data->id . '_purchase_code_error';
				$error_text_option = 'hb_' . $data->id . '_purchase_code_error_text';
				?>
				<form class="hb-addons-activate-licence" method="post">

				<?php
				$valid_purchase_code = get_option( $valid_purchase_code_option );

				if ( $valid_purchase_code == 'error' ) :
					if ( get_option( $error_option ) == 'no-online-validation' ) {
						$error_text = esc_html__( 'There seems to be an error when we try to validate your licence for this add-on. Would you please contact %s so we can assist you with that?', 'hbook-admin' );
						$hwp_support_string = esc_html__( 'Maestrel support', 'hbook-admin' );
						if ( strpos( $error_text, '%s' ) ) {
							$error_text = str_replace( '%s', '<a target="_blank" href="https://maestrel.com/contact-support/">' . $hwp_support_string . '</a>', $error_text );
						} else {
							$error_text .= ' - <a target="_blank" href="https://maestrel.com/contact-support/">Maestrel support</a>';
						}
					} else if ( get_option( $error_option ) == 'wrong-validation-code' ) {
						$error_text = esc_html__( 'The code you entered is not valid. Please try again. You can also contact %s if you need further help.', 'hbook-admin' );
						$hwp_support_string = esc_html__( 'Maestrel support', 'hbook-admin' );
						if ( strpos( $error_text, '%s' ) ) {
							$error_text = str_replace( '%s', '<a target="_blank" href="https://maestrel.com/contact-support/">' . $hwp_support_string . '</a>', $error_text );
						} else {
							$error_text .= ' - <a target="_blank" href="https://maestrel.com/contact-support/">Maestrel support</a>';
						}
					}
					?>

					<p><?php echo( wp_kses_post( $error_text ) ); ?></p>

					<p style="display:none">
					<input type="text" name="hb-forced-licence-validation" />
						<?php echo( wp_kses_post( get_option( $error_text_option ) ) ); ?>
					</p>
					<?php
					delete_option( $error_option );
					delete_option( $error_text_option );

				endif;
				?>

				<p>
					<label><?php esc_html_e( 'Enter your purchase code', 'hbook-admin' ); ?></label><br/>
					<input type="text" name="hb-addon-purchase-code" size="50" value="<?php echo( esc_attr( get_option( $purchase_code_option ) ) ); ?>" />
					<input type="hidden" name ="hb-addon-name" value="<?php echo( esc_html( $data->id ) ); ?>" />
					<input type="submit" value="<?php esc_attr_e( 'Verify purchase code', 'hbook-admin' ) ?>" class="button-primary" />
				</p>

				<?php if ( in_array( $valid_purchase_code, array( 'yes', 'no', 'already', 'removed', 'pending' ) ) ) : ?>

				<p <?php if ( $valid_purchase_code == 'no' ) { echo( 'class="hb-purchase-code-not-valid-msg"'); } ?>>
					<?php
					if ( $valid_purchase_code == 'yes' ) {
						esc_html_e( 'Your purchase code is valid.', 'hbook-admin' );
					} else if ( $valid_purchase_code == 'no' ) {
						esc_html_e( 'Your purchase code is not valid.', 'hbook-admin' );
					} else if ( $valid_purchase_code == 'already' ) {
						esc_html_e( 'This purchase code is already in use.', 'hbook-admin' );
					} else if ( $valid_purchase_code == 'removed' ) {
						esc_html_e( 'The purchase code has been removed.', 'hbook-admin' );
						delete_option( $valid_purchase_code_option );
					} else if ( $valid_purchase_code == 'pending' ) {
						esc_html_e( 'This purchase code has not been verified.', 'hbook-admin' );
					}
					?>
				</p>

				<?php
				endif;

				if ( $valid_purchase_code == 'error' ) {
					update_option( $valid_purchase_code_option, 'pending' );
				}

				if ( $valid_purchase_code == 'yes' ) : ?>
				<p>
					<a class="hb-remove-addon-purchase-code" href="#"><?php esc_html_e( 'Remove purchase code for this website.', 'hbook-admin' ); ?></a>
				</p>
				<?php
				endif;

				wp_nonce_field( 'hb_addons_nonce_licence', 'hb_addons_nonce_licence' );
				?>

			</form>
			<?php
			}
		}
		?>

	</div><!-- end .wrap -->

	<?php
	}

}