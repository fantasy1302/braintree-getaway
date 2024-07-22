<?php
/**
 * Admin settings page class
 *
 * @package Onepix\GatewayTemplate
 */

namespace Onepix\GatewayTemplate\Admin\Pages;

defined( 'ABSPATH' ) || exit();

/**
 * Class Settings
 *
 * @package Onepix\GatewayTemplate
 * @since   1.0.0
 */
class Settings extends Base {
	/**
	 * Settings constructor.
	 */
	public function __construct() {
		$this->sections = [
			'section_name' => [
				'field_name' => __( 'Field name', 'woocommerce-gateway-gateway-template' ),
				'fields'     => [
					'key' => [
						'type'  => 'input',
						'title' => __( 'Field name title', 'woocommerce-gateway-gateway-template' ),
					],
				],
			],
		];

		parent::__construct();
	}

	/**
	 * Register submenu in tools menu
	 */
	public function create_menu() {
		add_menu_page(
			__( 'WooCommerce Gateway Template settings page', 'woocommerce-gateway-gateway-template' ),
			__( 'WooCommerce Gateway Template settings page', 'woocommerce-gateway-gateway-template' ),
			$this->capability,
			$this->menu_slug,
			[ $this, 'print_page' ],
			'dashicons-admin-settings',
			58
		);
	}
}
