<?php
use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

final class WC_Braintree_Gateway_Blocks_Support extends AbstractPaymentMethodType {


	private $gateway;

	protected $name = 'misha';

	public function initialize() {
		$this->settings = get_option( "woocommerce_{$this->name}_settings", [] );
	}

	public function is_active() {
		return ! empty( $this->settings['enabled'] ) && 'yes' === $this->settings['enabled'];
	}

	public function get_payment_method_script_handles() {
		wp_register_script(
			'wc-blocks-integration',
			plugin_dir_url( __DIR__ ) . 'build/index.js',
			[
				'wc-blocks-registry',
				'wc-settings',
				'wp-element',
				'wp-html-entities',
			],
			null,
			true
		);

		return [ 'wc-blocks-integration' ];
	}

	public function get_payment_method_data() {
		return [
			'title'       => $this->get_setting( 'title' ),
			'description' => $this->get_setting( 'description' ),
		];
	}
}
