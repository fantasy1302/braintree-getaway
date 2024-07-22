<?php
/**
 * Admin class
 *
 * @package Onepix\GatewayTemplate
 */

namespace Onepix\GatewayTemplate\Admin;

defined( 'ABSPATH' ) || exit();

/**
 * Class Admin
 *
 * @package Onepix\GatewayTemplate
 * @since   1.0.0
 */
class Main {
	/**
	 * Admin class instance
	 *
	 * @var Pages\Main
	 */
	private Pages\Main $pages;

	/**
	 * Admin constructor.
	 */
	public function __construct() {
		new Assets();
		new Metaboxes\Main();
		$this->pages = new Pages\Main();

		add_action( 'admin_body_class', [ $this, 'add_body_class_to_order_page' ] );
		add_filter( 'plugin_action_links_' . gateway_template()->get_option( 'plugin_file' ), [ $this, 'add_plugin_action_links' ], 1, 4 );
	}

	/**
	 * Add gateway body class on order page
	 *
	 * @param string $classes default classes.
	 *
	 * @return string
	 */
	public function add_body_class_to_order_page( string $classes ): string {
		$order_id = isset( $_GET['post'] ) ? wc_clean( wp_unslash( $_GET['post'] ) ) : '';

		if ( ! empty( $order_id ) &&
			gateway_template()->gateway()->paid_for_order( $order_id ) ) {
			$classes .= '  order-paid-via-woocommerce-gateway-gateway-template';
		}

		return $classes;
	}

	/**
	 * Filters the list of action links displayed for a specific plugin in the Plugins list table.
	 *
	 * The dynamic portion of the hook name, `$plugin_file`, refers to the path
	 * to the plugin file, relative to the plugins directory.
	 *
	 * @since 2.7.0
	 * @since 4.9.0 The 'Edit' link was removed from the list of action links.
	 *
	 * @param string[] $actions     An array of plugin action links. By default this can include
	 *                              'activate', 'deactivate', and 'delete'. With Multisite active
	 *                              this can also include 'network_active' and 'network_only' items.
	 * @param string   $plugin_file Path to the plugin file relative to the plugins directory.
	 * @param array    $plugin_data An array of plugin data. See get_plugin_data()
	 *                              and the {@see 'plugin_row_meta'} filter for the list
	 *                              of possible values.
	 * @param string   $context     The plugin context. By default this can include 'all',
	 *                              'active', 'inactive', 'recently_activated', 'upgrade',
	 *                              'mustuse', 'dropins', and 'search'.
	 *
	 * @return string[]
	 */
	public function add_plugin_action_links( array $actions, string $plugin_file, array $plugin_data, string $context ): array {
		$action_links = [
			'gateway_settings' => '<a href="' . gateway_template()->gateway()->get_settings_page_url() . '" aria-label="' . esc_attr__( 'View Plugin settings', 'woocommerce-gateway-gateway-template' ) . '">' . esc_html__( 'Gateway Settings', 'woocommerce-gateway-gateway-template' ) . '</a>',
			'page_settings'    => '<a href="' . gateway_template()->pages( 'settings' )->get_page_url() . '" aria-label="' . esc_attr__( 'View Plugin settings', 'woocommerce-gateway-gateway-template' ) . '">' . esc_html__( 'Page Settings', 'woocommerce-gateway-gateway-template' ) . '</a>',
		];

		return array_merge( $action_links, $actions );
	}

	/**
	 * Get Main Pages class
	 *
	 * @return Pages\Main
	 */
	public function pages(): Pages\Main {
		return $this->pages;
	}
}
