<?php
/**
 * WooCommerce Braintree Gateway Form Fields
 *
 * @package Onepix\BraintreeGetaway
 */

return [
	'enabled'              => [
		'title'       => 'Enable/Disable',
		'label'       => 'Enable Gateway',
		'type'        => 'checkbox',
		'description' => '',
		'default'     => 'no',
	],
	'title'                => [
		'title'       => 'Title',
		'type'        => 'text',
		'description' => 'This controls the title which the user sees during checkout.',
		'default'     => 'Credit Card',
		'desc_tip'    => true,
	],
	'description'          => [
		'title'       => 'Description',
		'type'        => 'textarea',
		'description' => 'This controls the description which the user sees during checkout.',
		'default'     => 'Pay with your credit card via our super-cool payment gateway.',
	],
	'testmode'             => [
		'title'       => 'Test mode',
		'label'       => 'Enable Test Mode',
		'type'        => 'checkbox',
		'description' => 'Place the payment gateway in test mode using test API keys.',
		'default'     => 'yes',
		'desc_tip'    => true,
	],
	'merchant_id'          => [
		'title' => 'Merchant ID',
		'type'  => 'text',
	],
	'test_publishable_key' => [
		'title' => 'Test Publishable Key',
		'type'  => 'text',
	],
	'test_private_key'     => [
		'title' => 'Test Private Key',
		'type'  => 'password',
	],
];
