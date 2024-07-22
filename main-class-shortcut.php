<?php
/**
 * Main class shortcut
 *
 * @package Onepix\GatewayTemplate
 */

use Onepix\GatewayTemplate\Main;

/**
 * Shortcut for getting Main class instance
 *
 * @return Main
 */
function gateway_template(): Main {
	return Main::get_instance();
}
