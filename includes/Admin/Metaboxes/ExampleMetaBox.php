<?php
/**
 * Order capture metabox class
 *
 * @package Onepix\GatewayTemplate
 */

namespace Onepix\GatewayTemplate\Admin\Metaboxes;

use WP_Post;

defined( 'ABSPATH' ) || exit();

/**
 * Class OrderCapture
 *
 * @package Onepix\GatewayTemplate
 * @since   1.0.0
 */
class ExampleMetaBox {
	/**
	 * ExampleMetaBox constructor.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
	}

	/**
	 * Register meta boxes on order page
	 */
	public function add_meta_boxes() {
		global $post;

		$screen     = get_current_screen();
		$post_types = [ 'shop_order' ];

		if ( ! in_array( $screen->id, $post_types, true ) || ! in_array( $post->post_type, $post_types, true ) ) {
			return;
		}

		$order = wc_get_order( $post->ID );

		if ( empty( $order ) ) {
			return;
		}

		add_meta_box(
			gateway_template()->get_option( 'id' ) . '_capture_payment',
			__( 'Metabox name' ),
			[ $this, 'generate_meta_box' ],
			'shop_order',
			'side',
			'high'
		);
	}

	/**
	 * Show customer meta box content
	 *
	 * @param  WP_Post $post  current post object.
	 * @param  array   $args  additional arguments sent to add_meta_box function.
	 */
	public function generate_meta_box( WP_Post $post, array $args ) {
		$order = wc_get_order( $post );
	}
}
