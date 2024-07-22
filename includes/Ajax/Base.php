<?php
/**
 * Base Ajax class
 *
 * @package Onepix\GatewayTemplate
 */

namespace Onepix\GatewayTemplate\Ajax;

use Exception;

defined( 'ABSPATH' ) || exit();

/**
 * Base Ajax
 *
 * @package Onepix\GatewayTemplate
 * @since   1.0.0
 */
abstract class Base {
	/**
	 * Prefix for actions
	 *
	 * @var string
	 */
	const PREFIX = '';

	/**
	 * Actions for wc api (registration with prefix)
	 *
	 * @var array
	 */
	const ACTIONS = [];

	/**
	 * Ajax Account constructor
	 */
	public function __construct() {
		foreach ( static::ACTIONS as $action ) {
			add_action( 'woocommerce_api_' . static::get_action_name( $action ), [ $this, 'pre_action' ], 10, 0 );
			add_action( 'woocommerce_api_' . static::get_action_name( $action ), [ $this, $action ], 100, 0 );
		}
	}

	/**
	 * Return array of action urls
	 *
	 * @param  bool  $add_nonce add nonce to url.
	 *
	 * @return array
	 */
	public static function get_action_urls( bool $add_nonce = true ): array {
		$actions = [];

		foreach ( static::ACTIONS as $action ) {
			$actions[ $action ] = static::get_action_url( $action, [], $add_nonce );
		}

		return $actions;
	}

	/**
	 * Returns wc api url by short action name
	 *
	 * @param string $short_name      short action name.
	 * @param array  $additional_args additional url arguments.
	 * @param bool   $add_nonce       add nonce to url.
	 *
	 * @return false|string
	 */
	public static function get_action_url( string $short_name, array $additional_args = [], bool $add_nonce = true ) {
		$action = static::get_action_name( $short_name );

		if ( empty( $action ) ) {
			return false;
		}

		$request_url = WC()->api_request_url( $action );

		if ( ! empty( $additional_args ) ) {
			$request_url = add_query_arg(
				$additional_args,
				$request_url
			);
		}

		if ( $add_nonce ) {
			$request_url = add_query_arg(
				[
					'_wpnonce' => wp_create_nonce( static::get_action_name( $short_name ) ),
				],
				$request_url
			);
		}

		return $request_url;
	}

	/**
	 * Returns wc api action name by short action name
	 *
	 * @param  string $short_name  short action name.
	 *
	 * @return false|string
	 */
	public static function get_action_name( string $short_name ) {
		if ( in_array( $short_name, static::ACTIONS, true ) ) {
			return gateway_template()->get_option( 'id' ) . '_' . static::PREFIX . '_' . $short_name;
		}

		return false;
	}

	/**
	 * Verify nonce in $_GET array
	 *
	 * @param  string $function_name  function (action) name to verify. Use __FUNCTION__ to get right function name.
	 * @param  bool   $must_be_logged_in must the user be logged in.
	 *
	 * @return void
	 */
	public static function verify_nonce( string $function_name = '', bool $must_be_logged_in = false ) {
		if ( $must_be_logged_in && ! is_user_logged_in() ) {
			wp_die( esc_html__( 'You must be logged in', 'woocommerce-gateway-gateway-template' ) );
		}

		$nonce = sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ?? '' ) );

		$verified = wp_verify_nonce( $nonce, static::get_action_name( $function_name ) );

		if ( ! $verified ) {
			wp_die( esc_html__( 'Action failed. Please try again.', 'woocommerce-gateway-gateway-template' ) );
		}
	}

	/**
	 * If request content type is application/json and $_POST is empty decode data and put it to $_POST
	 */
	public static function set_json_to_post() {
		if ( empty( $_POST ) && isset( $_SERVER['CONTENT_TYPE'] ) && 'application/json' === $_SERVER['CONTENT_TYPE'] ) {
			$_POST = json_decode( file_get_contents( 'php://input' ), true );
		}
	}

	/**
	 * Action firing before main action
	 */
	public function pre_action() {
		try {
			static::set_json_to_post();
		} catch ( Exception $e ) {
			wp_send_json_error();
		}
	}
}
