<?php
/**
 * Actions class
 *
 * @package Onepix\GatewayTemplate
 */

namespace Onepix\GatewayTemplate\Actions;

defined( 'ABSPATH' ) || exit();

/**
 * Class Actions
 *
 * @package Onepix\GatewayTemplate
 * @since   1.0.0
 */
class Main {
	/**
	 * Actions constructor.
	 */
	public function __construct() {
		new Cart();
		new Order();
	}
}
