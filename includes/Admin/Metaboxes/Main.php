<?php
/**
 * Admin Metaboxes class
 *
 * @package Onepix\GatewayTemplate
 */

namespace Onepix\GatewayTemplate\Admin\Metaboxes;

defined( 'ABSPATH' ) || exit();

/**
 * Class Metaboxes
 *
 * @package Onepix\GatewayTemplate
 * @since   1.0.0
 */
class Main {
	/**
	 * Main constructor.
	 */
	public function __construct() {
		new ExampleMetaBox();
	}
}
