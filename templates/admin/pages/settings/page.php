<?php
/**
 * Settings page template
 *
 * @package Onepix\GatewayTemplate
 */

?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<form action="options.php" method="POST">
		<?php
		gateway_template()->pages( 'settings' )->do_settings_section();
		submit_button();
		?>
	</form>
</div>
