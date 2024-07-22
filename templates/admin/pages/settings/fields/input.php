<?php
/**
 * Settings input template
 *
 * @package Onepix\GatewayTemplate
 *
 * @var string $id   input id
 * @var string $name input name
 * @var string $value option value
 */

?>

<input
		type="text"
		id="<?php esc_attr( $id ); ?>"
		name="<?php esc_attr( $name ); ?>"
		value="<?php esc_attr( $value ); ?>"
>
