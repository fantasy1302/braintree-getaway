<?php
/**
 * Ajax Order class
 *
 * @package Onepix\GatewayTemplate
 */

namespace Onepix\GatewayTemplate\Ajax;

use Exception;

defined( 'ABSPATH' ) || exit();

/**
 * Class Ajax Order
 *
 * @package Onepix\GatewayTemplate
 * @since   1.0.0
 */
class Example extends Base {
	/**
	 * Prefix for actions
	 *
	 * @var string
	 */
	const PREFIX = 'example';

	/**
	 * Ajax for wc api (registration with prefix)
	 *
	 * @var array
	 */
	const ACTIONS = [
		'get_image',
	];

	/**
	 * Get order data
	 */
	public function get_order_data() {
		self::verify_nonce( __FUNCTION__ );

		$breed = sanitize_text_field( wp_unslash( $_GET['breed'] ?? '' ) );

		if ( empty( $breed ) ) {
			wp_send_json(
				[
					'success' => false,
					'message' => __( 'Wrong breed', 'woocommerce-gateway-gateway-template' ),
				]
			);
		}

		try {
			wp_send_json_success(
				[
					'success'      => true,
					'order_number' => gateway_template()->api( 'example' )->get_dog_image( $breed ),
				]
			);
		} catch ( Exception $e ) {
			wp_send_json(
				[
					'success' => false,
					'message' => $e->getMessage(),
				]
			);
		}
	}
}
