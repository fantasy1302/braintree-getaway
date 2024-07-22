<?php

return [
	[
		'namespace'           => 'braintree/v1',
		'route'               => '/getClientToken',
		'methods'             => 'GET',
		'callback'            => 'generate_braintree_client_token',
		'args'                => [],
		'permission_callback' => '__return_true',
	],
	[
		'namespace'           => 'braintree/v1',
		'route'               => '/handleNonce',
		'methods'             => 'POST',
		'callback'            => 'handle_nonce',
		'args'                => [],
		'permission_callback' => '__return_true',
	],
];
