<?php
/**
 * Plugin Name: WooCommerce Braintree Gateway
 * Plugin URI: https://onepix.net
 * Description: Accept payments via Braintree.
 * Author: Onepix
 * Author URI: https://onepix.net
 * Text Domain: woocommerce-braintree-gateway
 * Domain Path: /languages
 * WC requires at least: 4.8
 * WC tested up to: 6.2.1
 * Requires at least: 5.7
 * Requires PHP: 7.4
 * Version: 1.0.0
 *
 * @package Onepix\BraintreeGetaway
 */

require __DIR__ . '/app/vendor/autoload.php';

add_filter( 'woocommerce_payment_gateways', 'add_gateway_class' );
/**
 * @param $gateways
 * @return mixed
 */

function add_gateway_class( $gateways ) {
	$gateways[] = 'WC_Braintree_Gateway';
	return $gateways;
}

add_action( 'plugins_loaded', 'init_gateway_class' );

function init_gateway_class() {
	class WC_Braintree_Gateway extends WC_Payment_Gateway {

		public function __construct() {
			$this->id                 = 'misha';
			$this->icon               = '';
			$this->has_fields         = true;
			$this->method_title       = 'Braintree Gateway';
			$this->method_description = 'Description of Braintree payment gateway';
			$this->supports           = [ 'products' ];
			$this->init_form_fields();
			$this->init_settings();
			$this->title           = $this->get_option( 'title' );
			$this->description     = $this->get_option( 'description' );
			$this->enabled         = $this->get_option( 'enabled' );
			$this->testmode        = 'yes' === $this->get_option( 'testmode' );
			$this->private_key     = $this->testmode ? $this->get_option( 'test_private_key' ) : $this->get_option( 'private_key' );
			$this->publishable_key = $this->testmode ? $this->get_option( 'test_publishable_key' ) : $this->get_option( 'publishable_key' );

			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ] );
			add_action( 'wp_enqueue_scripts', [ $this, 'payment_scripts' ] );
			add_action( 'woocommerce_checkout_order_processed', [ $this, 'process_payment' ], 10, 3 );
		}

		public function init_form_fields() {
			$this->form_fields = require 'includes/Data/BraintreeFields.php';
		}

		public function payment_scripts() {
			if ( ! is_cart() && ! is_checkout() && ! isset( $_GET['pay_for_order'] ) ) {
				return;
			}

			if ( 'no' === $this->enabled ) {
				return;
			}

			if ( empty( $this->private_key ) || empty( $this->publishable_key ) ) {
				return;
			}

			if ( ! $this->testmode && ! is_ssl() ) {
				return;
			}

			wp_enqueue_script( 'braintree_js', 'https://js.braintreegateway.com/web/dropin/1.30.0/js/dropin.min.js' );
			wp_register_script( 'woocommerce_scripts', plugins_url( 'index.js', __FILE__ ), [ 'jquery', 'braintree_js' ] );

			wp_localize_script(
				'woocommerce_scripts',
				'braintree_params',
				[
					'publishableKey' => $this->publishable_key,
					'ajax_url'       => admin_url( 'admin-ajax.php' ),
					'clientTokenUrl' => '/wp-json/braintree/v1/getClientToken',
				]
			);
		}

		public function process_payment( $order_id ) {
			$order = wc_get_order( $order_id );

			return [
				'result'  => 'Success',
				'message' => __( 'Success', 'woocommerce' ),
			];
		}

		public function generate_braintree_client_token() {
			$gateway = new Braintree\Gateway(
				[
					'environment' => 'sandbox',
					'merchantId'  => $this->get_option( 'merchant_id' ),
					'publicKey'   => $this->get_option( 'test_publishable_key' ),
					'privateKey'  => $this->get_option( 'test_private_key' ),
				]
			);

			$client_token = $gateway->clientToken()->generate();
			return new WP_REST_Response( [ 'clientToken' => $client_token ], 200 );
		}

		public function handle_nonce( WP_REST_Request $request ) {
				$nonce = $request->get_param( 'nonce' );

				$gateway = new Braintree\Gateway(
					[
						'environment' => 'sandbox',
						'merchantId'  => $this->get_option( 'merchant_id' ),
						'publicKey'   => $this->get_option( 'test_publishable_key' ),
						'privateKey'  => $this->get_option( 'test_private_key' ),
					]
				);

				$result = $gateway->transaction()->sale(
					[
						'amount'             => '10.00',
						'paymentMethodNonce' => $nonce,
						'options'            => [
							'submitForSettlement' => true,
						],
					]
				);

			if ( $result->success || ! is_null( $result->transaction ) ) {
				return new WP_REST_Response( [ 'success' => true ], 200 );
			} else {
				$error_string = '';
				foreach ( $result->errors->deepAll() as $error ) {
					$error_string .= 'Error: ' . $error->code . ': ' . $error->message . "\n";
				}
				return new WP_REST_Response(
					[
						'success' => false,
						'message' => $error_string,
					],
					400
				);
			}
		}

		public static function cart_checkout_blocks_compatibility() {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, false );
			}
		}

		public static function gateway_block_support() {
			if ( ! class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
				return;
			}

			require_once __DIR__ . '/includes/Getaway.php';

			add_action(
				'woocommerce_blocks_payment_method_type_registration',
				function ( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
					$payment_method_registry->register( new WC_Braintree_Gateway_Blocks_Support() );
				}
			);
		}
	}
}


add_action( 'before_woocommerce_init', 'WC_Braintree_Gateway::cart_checkout_blocks_compatibility' );


add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'braintree/v1',
			'/getClientToken',
			[
				'methods'  => 'GET',
				'callback' => [ new WC_Braintree_Gateway(), 'generate_braintree_client_token' ],
			]
		);
	}
);

add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'braintree/v1',
			'/handleNonce',
			[
				'methods'             => 'POST',
				'callback'            => [ new WC_Braintree_Gateway(), 'handle_nonce' ],
				'permission_callback' => '__return_true',
			]
		);
	}
);
add_action( 'woocommerce_blocks_loaded', 'WC_Braintree_Gateway::gateway_block_support' );
