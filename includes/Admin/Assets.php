<?php
/**
 * Admin assets class
 *
 * @package Onepix\GatewayTemplate
 */

namespace Onepix\GatewayTemplate\Admin;

defined( 'ABSPATH' ) || exit();

/**
 * Class Assets
 *
 * @package Onepix\GatewayTemplate
 * @since   1.0.0
 */
class Assets {
	/**
	 * Assets constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
	}

	/**
	 * Enqueue plugin admin scripts
	 */
	public function enqueue_admin_scripts() {
		wp_enqueue_style(
			gateway_template()->get_option( 'id' ) . '/css/admin',
			gateway_template()->get_option( 'assets_url' ) . 'css/admin.min.css',
			[],
			gateway_template()->get_option( 'version' )
		);

		wp_enqueue_script(
			gateway_template()->get_option( 'id' ) . '/js/admin',
			gateway_template()->get_option( 'assets_url' ) . 'js/admin.min.js',
			[ 'jquery', 'jquery-blockui' ],
			gateway_template()->get_option( 'version' ),
			true
		);

		wp_localize_script(
			gateway_template()->get_option( 'id' ) . '/js/admin',
			'gatewayTemplateAdminData',
			[
				'urls' => [],
				'i18n' => [
					'request_error' => __( 'Request Error. Try Again.', 'woocommerce-gateway-gateway-template' ),
				],
			]
		);
	}
}
