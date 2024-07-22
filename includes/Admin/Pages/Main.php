<?php
/**
 * Admin Pages class
 *
 * @package Onepix\GatewayTemplate
 */

namespace Onepix\GatewayTemplate\Admin\Pages;

defined( 'ABSPATH' ) || exit();

/**
 * Class Admin Pages
 *
 * @package Onepix\GatewayTemplate
 * @since   1.0.0
 */
class Main {
	/**
	 * Pages array
	 *
	 * @var Base[]
	 */
	private array $pages = [];

	/**
	 * Main constructor.
	 */
	public function __construct() {
		$this->pages['settings'] = new Settings();
	}

	/**
	 * Get page by slug
	 *
	 * @param string $page_slug page slug to get page.
	 *
	 * @return Base|null
	 */
	public function get( string $page_slug ): ?Base {
		return $this->pages[ $page_slug ] ?? null;
	}
}
