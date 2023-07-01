<?php
class HbPayPal extends HbPaymentGateway {

	public function __construct( $hbdb, $version, $utils ) {
		$this->id = 'paypal';
		$this->name = 'PayPal';
		$this->has_redirection = 'yes';
		$this->version = $version;
		$this->hbdb = $hbdb;
		$this->utils = $utils;

		add_filter( 'hbook_payment_gateways', array( $this, 'add_paypal_gateway_class' ) );
	}

	public function add_paypal_gateway_class( $hbook_gateways ) {
		$hbook_gateways[] = $this;
		return $hbook_gateways;
	}

	public function get_payment_method_label() {
		$output = $this->hbdb->get_string( 'paypal_payment_method_label' ) . '&nbsp;';
		$icons = json_decode( get_option( 'hb_paypal_icons' ) );
		if ( ! is_array( $icons ) ) {
			$icons = array();
		}
		if ( in_array( 'paypal', $icons ) ) {
			$output .= '&nbsp;';
			$output .= '<img class="hb-paypal-payment-gateway-label-img-paypal" alt="paypal" ';
			$output .= 'src="' . $this->utils->plugin_url . '/payment/img/paypal.png" ';
			$output .= 'alt="" />';
		}
		if ( in_array( 'credit_cards', $icons ) ) {
			$credit_cards_icons = array( 'visa', 'mastercard', 'discover', 'americanexpress' );
			foreach ( $credit_cards_icons as $icon ) {
				$output .= ' ';
				$output .= '<img class="hb-paypal-payment-gateway-label-img-' . $icon . '" alt="' . $icon . '" ';
				$output .= 'src="' . $this->utils->plugin_url . '/payment/img/' . $icon . '.png" ';
				$output .= 'alt="" />';
			}
		}
		return apply_filters( 'hb_paypal_payment_method_label', $output );
	}

	public function admin_fields() {
		return array(
			'label' => esc_html__( 'Paypal settings', 'hbook-admin' ),
			'options' => array(

				'hb_paypal_mode' => array(
					'label' => esc_html__( 'Paypal mode', 'hbook-admin' ),
					'type' => 'radio',
					'choice' => array(
						'live' => esc_html__( 'Live', 'hbook-admin' ),
						'sandbox' => esc_html__( 'Sandbox', 'hbook-admin' ),
					),
					'default' => 'live'
				),
				'hb_paypal_api_sandbox_user' => array(
					'label' => esc_html__( 'Sandbox API Username', 'hbook-admin' ),
					'type' => 'text',
					'wrapper-class' => 'hb-paypal-mode-sandbox',
				),
				'hb_paypal_api_sandbox_psw' => array(
					'label' => esc_html__( 'Sandbox API Password', 'hbook-admin' ),
					'type' => 'text',
					'wrapper-class' => 'hb-paypal-mode-sandbox',
				),
				'hb_paypal_api_sandbox_signature' => array(
					'label' => esc_html__( 'Sandbox API Signature', 'hbook-admin' ),
					'type' => 'text',
					'wrapper-class' => 'hb-paypal-mode-sandbox',
				),
				'hb_paypal_api_live_user' => array(
					'label' => esc_html__( 'Live API Username', 'hbook-admin' ),
					'type' => 'text',
					'wrapper-class' => 'hb-paypal-mode-live',
				),
				'hb_paypal_api_live_psw' => array(
					'label' => esc_html__( 'Live API Password', 'hbook-admin' ),
					'type' => 'text',
					'wrapper-class' => 'hb-paypal-mode-live',
				),
				'hb_paypal_api_live_signature' => array(
					'label' => esc_html__( 'Live API Signature', 'hbook-admin' ),
					'type' => 'text',
					'wrapper-class' => 'hb-paypal-mode-live',
				),
				'hb_paypal_icons' => array(
					'label' => esc_html__( 'Displayed icons', 'hbook-admin' ),
					'type' => 'checkbox',
					'choice' => array(
						'paypal' => esc_html__( 'PayPal', 'hbook-admin' ),
						'credit_cards' => esc_html__( 'Credit cards', 'hbook-admin' ),
					),
					'default' => '["paypal"]'
				),
			)
		);
	}

	public function admin_js_scripts() {
		return array(
			array(
				'id' => 'hb-paypal-admin',
				'url' => plugin_dir_url( __FILE__ ) . 'paypal-admin.js',
				'version' => $this->version
			),
		);
	}

	public function js_scripts() {
		return array(
			array(
				'id' => 'hb-paypal',
				'url' => plugin_dir_url( __FILE__ ) . 'paypal.js',
				'version' => $this->version
			),
		);
	}

	public function js_data() {
		if ( get_option( 'hb_paypal_mode' ) == 'sandbox' ) {
			$paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout';
		} else {
			$paypal_url = 'https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout';
		}
		return array(
			'hb_paypal_url' => $paypal_url,
		);
	}

	public function payment_form() {
		$paypal_txt = $this->hbdb->get_string( 'paypal_text_before_form' );
		if ( $paypal_txt ) {
			return '<p class="hb-paypal-payment-form-txt">' . $paypal_txt . '</p>';
		} else {
			return '';
		}
	}

	public function bottom_area() {
		$txt_1 = $this->hbdb->get_string( 'paypal_bottom_text_line_1' );
		$txt_2 = $this->hbdb->get_string( 'paypal_bottom_text_line_2' );
		$output = '';
		$icons = json_decode( get_option( 'hb_paypal_icons' ) );
		if ( ! is_array( $icons ) ) {
			$icons = array();
		}
		if ( $txt_1 && in_array( 'paypal', $icons ) ) {
			$output .= '<i><small>';
			$output .= '<span>' . $txt_1 . ' &nbsp;</span>';
			$output .= '<img class="hb-paypal-bottom-area-img-paypal" alt="paypal" ';
			$output .= 'src="' . $this->utils->plugin_url . '/payment/img/paypal.png" ';
			$output .= 'alt="" />';
			$output .= '</small></i>';
		}
		if ( $txt_2 && in_array( 'credit_cards', $icons ) ) {
			if ( $output ) {
				$output .= '<br/>';
			}
			$output .= '<i><small>';
			$output .= '<span>' . $txt_2 . ' &nbsp;</span>';
			$credit_cards_icons = array( 'visa', 'mastercard', 'discover', 'americanexpress' );
			foreach ( $credit_cards_icons as $icon ) {
				$output .= '<img class="hb-paypal-bottom-area-img-' . $icon . '" alt="' . $icon . '" ';
				$output .= 'src="' . $this->utils->plugin_url . '/payment/img/' . $icon . '.png" ';
				$output .= 'alt="" /> ';
			}
			$output .= '</small></i>';
		}
		return apply_filters( 'hb_paypal_bottom_area', $output );
	}

	public function process_payment( $resa_info, $customer_info, $amount_to_pay ) {
		$hb_strings = $this->hbdb->get_strings();


		$parameters_to_remove = array( 'token', 'PayerID' );
		$return_urls = $this->get_return_urls( $parameters_to_remove );

		$set_express_check_out_args = array(
			'METHOD' => 'SetExpressCheckout',
			'PAYMENTREQUEST_0_AMT' => $amount_to_pay,
			'PAYMENTREQUEST_0_CURRENCYCODE' => get_option( 'hb_currency' ),
			'PAYMENTREQUEST_0_DESC' => $this->get_external_payment_desc( $resa_info, $customer_info ),
			'NOSHIPPING' => '1',
			'RETURNURL' => $return_urls['payment_confirm'],
			'CANCELURL' => $return_urls['payment_cancel'],
			'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
			'SOLUTIONTYPE' => 'Sole',
			'LANDINGPAGE' => 'Billing'
		);
		$response = $this->remote_post_to_paypal( $set_express_check_out_args );
		if ( is_wp_error( $response ) ) {
			return array( 'success' => false, 'error_msg' => 'WP error: ' . $response->get_error_message() );
		}
		$paypal_response = '';
		parse_str( $response['body'], $paypal_response );
		if ( $paypal_response['ACK'] == 'Success' ) {
			return array( 'success' => true, 'payment_token' => $paypal_response['TOKEN'] );
		} else {
			return array( 'success' => false, 'error_msg' => 'PayPal error : '. $paypal_response['L_LONGMESSAGE0'] );
		}
	}

	public function get_payment_token() {
		return $_GET['token'];
	}

	public function confirm_payment() {
		$resa = $this->hbdb->get_resa_by_payment_token( $_GET['token'] );
		if ( ! $resa ) {
			$response = array(
				'success' => false,
				'error_msg' => $this->hbdb->get_string( 'timeout_error' )
			);
		} else {
			$customer = $this->hbdb->get_customer_info( $resa['customer_id'] );
			$payment_desc = $this->get_external_payment_desc( $resa, $customer );
			$do_express_check_out_args = array(
				'METHOD' => 'DoExpressCheckoutPayment',
				'TOKEN' => $_GET['token'],
				'PAYERID' => $_GET['PayerID'],
				'PAYMENTREQUEST_0_AMT' => $resa['amount_to_pay'],
				'PAYMENTREQUEST_0_ITEMAMT' => $resa['amount_to_pay'],
				'PAYMENTREQUEST_0_CURRENCYCODE' => get_option( 'hb_currency' ),
				'L_PAYMENTREQUEST_0_NAME0' => $payment_desc,
				'L_PAYMENTREQUEST_0_AMT0' => $resa['amount_to_pay'],
				'L_PAYMENTREQUEST_0_QTY0' => '1',
			);
			$response = $this->remote_post_to_paypal( $do_express_check_out_args );
			if ( is_wp_error( $response ) ) {
				return array( 'success' => false, 'error_msg' => 'WP error: ' . $response->get_error_message() );
			}
			$paypal_response = '';
			parse_str( $response['body'], $paypal_response );
			if ( strpos( $paypal_response['ACK'], 'Success' ) !== false ) {
				$payment_status = strip_tags( $paypal_response['PAYMENTINFO_0_PAYMENTSTATUS'] );
				$payment_status_reason = '';
				if ( $payment_status == 'Pending' ) {
					$payment_status_reason = strip_tags( $paypal_response['PAYMENTINFO_0_PENDINGREASON'] );
				}
				if ( $payment_status == 'Completed-Funds-Held' ) {
					$payment_status_reason = strip_tags( $paypal_response['PAYMENTINFO_0_HOLDDECISION'] );
				}
				$response = array(
					'success' => true,
					'payment_status_reason' => substr( $payment_status . ' - ' . $payment_status_reason, 0, 40 ),
				);
			} else {
				$response = array(
					'success' => false,
					'error_msg' => strip_tags( $paypal_response['ACK'] )
				);
			}

			if ( $response['success'] ) {
				if ( $resa['payment_status'] != 'paid' ) {
					$resa_id = $this->hbdb->update_resa_after_payment( $_GET['token'], 'paid', $response['payment_status_reason'], $resa['amount_to_pay'] );
					if ( ! $resa_id ) {
						$response = array(
							'success' => false,
							'error_msg' => 'Error (could not update reservation).'
						);
					} else {
						$this->utils->send_email( 'new_resa', $resa_id );
					}
				}
			}
		}
		return $response;
	}

	private function remote_post_to_paypal( $body_args ) {
		if ( get_option( 'hb_paypal_mode' ) == 'sandbox' ) {
			$paypal_api_url = 'https://api-3t.sandbox.paypal.com/nvp';
			$paypal_settings = array(
				'USER' => get_option( 'hb_paypal_api_sandbox_user' ),
				'PWD' => get_option( 'hb_paypal_api_sandbox_psw' ),
				'SIGNATURE' => get_option( 'hb_paypal_api_sandbox_signature' ),
			);
		} else {
			$paypal_api_url = 'https://api-3t.paypal.com/nvp';
			$paypal_settings = array(
				'USER' => get_option( 'hb_paypal_api_live_user' ),
				'PWD' => get_option( 'hb_paypal_api_live_psw' ),
				'SIGNATURE' => get_option( 'hb_paypal_api_live_signature' ),
			);
		}
		$paypal_settings['VERSION'] = '119.0';
		$body_args = array_merge( $paypal_settings, $body_args );
		$post_args = array(
			'body' => $body_args
		);
		$response = $this->hb_remote_post( $paypal_api_url, $post_args );
		return $response;
	}

}