<?php
class HbStripe extends HbPaymentGateway {

	public function __construct( $hbdb, $version ) {
		$this->id = 'stripe';
		$this->name = 'Stripe';
		$this->has_redirection = 'no';
		$this->version = $version;
		$this->hbdb = $hbdb;

		add_filter( 'hbook_payment_gateways', array( $this, 'add_stripe_gateway_class' ) );
	}

	public function add_stripe_gateway_class( $hbook_gateways ) {
		$hbook_gateways[] = $this;
		return $hbook_gateways;
	}

	public function get_payment_method_label() {
		$output = $this->hbdb->get_string( 'stripe_payment_method_label' );
		$output .= $this->get_credit_cards_icons( 'hb-stripe-payment-gateway-img' );
		return apply_filters( 'hb_stripe_payment_method_label', $output );
	}

	public function admin_fields() {
		return array(
			'label' => esc_html__( 'Stripe settings', 'hbook-admin' ),
			'options' => array(

				'hb_stripe_mode' => array(
					'label' => esc_html__( 'Stripe mode', 'hbook-admin' ),
					'type' => 'radio',
					'choice' => array(
						'live' => esc_html__( 'Live', 'hbook-admin' ),
						'test' => esc_html__( 'Test', 'hbook-admin' ),
					),
					'default' => 'live'
				),
				'hb_stripe_test_publishable_key' => array(
					'label' => esc_html__( 'Test Publishable Key', 'hbook-admin' ),
					'type' => 'text',
					'wrapper-class' => 'hb-stripe-mode-test'
				),
				'hb_stripe_test_secret_key' => array(
					'label' => esc_html__( 'Test Secret Key', 'hbook-admin' ),
					'type' => 'text',
					'wrapper-class' => 'hb-stripe-mode-test',
				),
				'hb_stripe_live_publishable_key' => array(
					'label' => esc_html__( 'Live Publishable Key', 'hbook-admin' ),
					'type' => 'text',
					'wrapper-class' => 'hb-stripe-mode-live',
				),
				'hb_stripe_live_secret_key' => array(
					'label' => esc_html__( 'Live Secret Key', 'hbook-admin' ),
					'type' => 'text',
					'wrapper-class' => 'hb-stripe-mode-live',
				),
				'hb_store_credit_card' => array(
					'label' => esc_html__( 'Store credit card', 'hbook-admin' ),
					'type' => 'radio',
					'choice' => array(
						'yes' => esc_html__( 'Yes', 'hbook-admin' ),
						'no' => esc_html__( 'No', 'hbook-admin' ),
					),
					'default' => 'no'
				),
				'hb_stripe_powered_by' => array(
					'label' => esc_html__( 'Display a "Powered by Stripe" icon', 'hbook-admin' ),
					'type' => 'radio',
					'choice' => array(
						'yes' => esc_html__( 'Yes', 'hbook-admin' ),
						'no' => esc_html__( 'No', 'hbook-admin' ),
					),
					'default' => 'no'
				),

			)
		);
	}

	public function admin_js_scripts() {
		return array(
			array(
				'id' => 'hb-stripe-admin',
				'url' => plugin_dir_url( __FILE__ ) . 'stripe-admin.js',
				'version' => $this->version
			),
		);
	}

	public function js_scripts() {
		return array(
			array(
				'id' => 'stripejs',
				'url' => 'https://js.stripe.com/v3/',
				'version' => null
			),
			array(
				'id' => 'hbook-stripe',
				'url' => plugin_dir_url( __FILE__ ) . 'stripe.js',
				'version' => $this->version
			),
		);
	}

	public function js_data() {
		if ( get_option( 'hb_stripe_mode') == 'test' ) {
			$stripe_key = trim( get_option( 'hb_stripe_test_publishable_key' ) );
		} else {
			$stripe_key = trim( get_option( 'hb_stripe_live_publishable_key' ) );
		}
		$stripe_locale = 'auto';
		$available_locales = array( 'ar', 'da', 'de', 'en', 'es', 'fi', 'fr', 'he', 'it', 'ja', 'nl', 'pl', 'ru', 'sv', 'zh' );
		if ( get_locale() == 'nn_NO' ) {
			$stripe_locale = 'no';
		} else if ( in_array( substr( get_locale(), 0, 2 ), $available_locales ) ) {
			$stripe_locale = substr( get_locale(), 0, 2 );
		}
		return array(
			'hb_stripe_key' => $stripe_key,
			'hb_stripe_locale' => $stripe_locale,
		);
	}

	public function payment_form() {
		$output = '';
		$stripe_text_before_form = $this->hbdb->get_string( 'stripe_text_before_form' );
		if ( $stripe_text_before_form ) {
			$output .= '<p class="hb-stripe-payment-form-txt-top">' . $stripe_text_before_form . '</p>';
		}
		$output .= '<div class="hb-stripe-card-element-wrapper">';
		$output .= '<input class="hb-stripe-card-element-bg" type="text" disabled />';
		$output .= '<div class="hb-stripe-card-element"></div>';
		$output .= '</div>';
		$output .= '<br/>';
		$output .= '<p class="hb-stripe-error">&nbsp;</p>';
		$stripe_text_bottom_form = $this->hbdb->get_string( 'stripe_text_bottom_form' );
		if ( $stripe_text_bottom_form ) {
			$output .= '<p class="hb-stripe-payment-form-txt-bottom"><small>';
			$output .= '<img class="hb-padlock-img" src="' . plugin_dir_url( __FILE__ ) . '../img/padlock.png" alt="" />';
			$output .= '<span>' . $stripe_text_bottom_form . '</span>';
			$output .= '<br/>';
			if ( get_option( 'hb_stripe_powered_by' ) == 'yes' ) {
				$output .= '<img class="hb-powered-by-stripe-img" src="' . plugin_dir_url( __FILE__ ) . '../img/powered_by_stripe.png" alt="" />';
			}
			$output .= $this->get_credit_cards_icons( 'hb-stripe-bottom-text-form-img' );
			$output .= '</small></p>';
		}
		return apply_filters( 'hb_stripe_payment_form', $output );
	}

	public function process_payment( $resa_info, $customer_info, $amount_to_pay ) {
		if ( isset( $_POST['hb-stripe-payment-intent-id'] ) ) {
			$response = $this->remote_post_to_stripe( 'https://api.stripe.com/v1/payment_intents/' . $_POST['hb-stripe-payment-intent-id'] . '/confirm', array() );
		} else if ( isset( $_POST['hb-stripe-setup-intent-id'] ) ) {
			$response = $this->remote_post_to_stripe( 'https://api.stripe.com/v1/setup_intents/' . $_POST['hb-stripe-setup-intent-id'], array() );
		} else {
			$customer_email = '';
			$customer_first_name = '';
			$customer_last_name = '';
			if ( isset( $customer_info['email'] ) ) {
				$customer_email = $customer_info['email'];
			}
			if ( isset( $customer_info['first_name'] ) ) {
				$customer_first_name = $customer_info['first_name'];
			}
			if ( isset( $customer_info['last_name'] ) ) {
				$customer_last_name = $customer_info['last_name'];
			}

			$sep = '';
			if ( $customer_first_name && $customer_last_name ) {
				$sep = ' ';
			}
			$customer_description = $customer_first_name . $sep . $customer_last_name;

			if ( $amount_to_pay == 0 || get_option( 'hb_store_credit_card' ) == 'yes' ) {
				$post_args = array(
					'description' => $customer_description,
					'email' => $customer_email,
					'name' => $customer_description,
				);
				$response = $this->remote_post_to_stripe( 'https://api.stripe.com/v1/customers', $post_args );
				if ( ! $response['success'] ) {
					return $response;
				}
				$info = json_decode( $response['info'], true );
				$customer_payment_id = $info['id'];

				$post_args = array(
					'customer' => $customer_payment_id
				);
				$response = $this->remote_post_to_stripe( 'https://api.stripe.com/v1/payment_methods/' . $_POST['hb-stripe-payment-method-id'] . '/attach', $post_args );
				if ( ! $response['success'] ) {
					return $response;
				}
			}

			$payment_description = $customer_email;
			if ( $customer_first_name || $customer_last_name ) {
				$payment_description .= ' (' . $customer_first_name . ' ' . $customer_last_name . ')';
			}
			if ( $payment_description ) {
				$payment_description .= ' - ';
			}
			$wptexturize = remove_filter( 'the_title', 'wptexturize' );
			$payment_description .= get_the_title( $resa_info['accom_id'] );
			if ( $wptexturize ) {
				add_filter( 'the_title', 'wptexturize' );
			}
			$payment_description .= ' (' . $resa_info['check_in'] . ' - ' . $resa_info['check_out'] . ')';

			if ( $amount_to_pay == 0 ) {
				$post_args = array(
					'customer' => $customer_payment_id,
					'description' => $payment_description,
					'payment_method' => $_POST['hb-stripe-payment-method-id'],
					'confirm' => 'true',
					'usage' => 'off_session',
				);
			} else {
				$post_args = array(
					'amount' => $amount_to_pay,
					'currency' => $resa_info['currency'],
					'description' => $payment_description,
					'receipt_email' => $customer_email,
					'payment_method' => $_POST['hb-stripe-payment-method-id'],
					'confirmation_method' => 'manual',
					'confirm' => 'true',
				);
				if ( get_option( 'hb_store_credit_card' ) == 'yes' ) {
					$post_args['customer'] = $customer_payment_id;
					$post_args['setup_future_usage'] = 'off_session';
				}
			}
			if ( $amount_to_pay > 0 ) {
				$response = $this->remote_post_to_stripe( 'https://api.stripe.com/v1/payment_intents', $post_args );
			} else {
				$response = $this->remote_post_to_stripe( 'https://api.stripe.com/v1/setup_intents', $post_args );
			}
		}

		if ( $response['success'] ) {
			$payment_info = json_decode( $response['info'], true );
			if ( $payment_info['status'] == 'succeeded' ) {
				if ( $amount_to_pay > 0 ) {
					$response['payment_info'] = json_encode( array(
						'stripe_charges' => array( array(
							'id' => $payment_info['charges']['data'][0]['id'],
							'amount' => $amount_to_pay,
						) )
					) );
				}
				if ( $amount_to_pay == 0 || get_option( 'hb_store_credit_card' ) == 'yes' ) {
					$stored_customer_payment_info = array(
						'customer_id' => $payment_info['customer'],
						'payment_method_id' => $payment_info['payment_method'],
					);
					$stored_customer_payment_info = json_encode( $stored_customer_payment_info );
					$this->hbdb->update_customer_payment_id( $customer_info['id'], $stored_customer_payment_info );
				}
			} else if ( ( $payment_info['status'] == 'requires_action' ) || ( $payment_info['status'] == 'requires_source_action' ) ) {
				$response = array(
					'success' => false,
					'error_msg' => 'payment_requires_action',
					'client_secret' => $payment_info['client_secret'],
				);
				if ( $amount_to_pay > 0 ) {
					$response['stripe_action'] = 'payment_intent';
				} else {
					$response['stripe_action'] = 'setup_intent';
				}
			} else {
				$response = array(
					'success' => false,
					'error_msg' => str_replace( '%error_msg', $payment_info['status'], $this->hbdb->get_string( 'stripe_processing_error' ) ),
				);
			}
		}
		return $response;
	}

	public function remote_post_to_stripe( $url, $post_args ) {
		if ( isset( $post_args['amount'] ) ) {
			$zero_decimal_currencies = array( 'BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG', 'RWF', 'VND', 'VUV', 'XAF', 'XOF', 'XPF' );
			if ( ! in_array( $post_args['currency'], $zero_decimal_currencies ) ) {
				$post_args['amount'] = $post_args['amount'] * 100;
			}
		}
		if ( $url == 'https://api.stripe.com/v1/refunds' ) {
			unset( $post_args['currency'] );
		}

		if ( get_option( 'hb_stripe_mode') == 'test' ) {
			$stripe_key = trim( get_option( 'hb_stripe_test_secret_key' ) );
		} else {
			$stripe_key = trim( get_option( 'hb_stripe_live_secret_key' ) );
		}
		$post_args = array(
			'headers' => array( 'Authorization' => 'Bearer ' . $stripe_key ),
			'body' => $post_args
		);
		$response = $this->hb_remote_post( $url, $post_args );

		if ( is_wp_error( $response ) ) {
			return array( 'success' => false, 'error_msg' => 'WP error: ' . $response->get_error_message() );
		} else if ( $response['response']['code'] == 200 ) {
			return array(
				'success' => true,
				'info' => $response['body']
			);
		} else {
			$response = json_decode( $response['body'], true );
			$error_msg = str_replace( '%error_msg', $response['error']['message'], $this->hbdb->get_string( 'stripe_processing_error' ) );
			return array(
				'success' => false,
				'error_msg' => $error_msg
			);
		}
	}

	private function get_credit_cards_icons( $css_class ) {
		$output = '';
		$icons = apply_filters( 'hb_stripe_credit_cards_icons', array( 'mastercard', 'visa', 'americanexpress' ) );
		foreach ( $icons as $icon ) {
			$output .= ' ';
			$output .= '<img class="' . $css_class . '-' . $icon . '" ';
			$output .= 'src="' . plugin_dir_url( __FILE__ ) . '../img/' . $icon . '.png" alt="" />';
		}
		return $output;
	}

}