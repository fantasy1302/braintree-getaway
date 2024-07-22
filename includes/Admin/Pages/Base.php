<?php
/**
 * Base Admin Pages class
 *
 * @package Onepix\GatewayTemplate
 */

namespace Onepix\GatewayTemplate\Admin\Pages;

defined( 'ABSPATH' ) || exit();

/**
 * Class Base Admin Pages
 *
 * @package Onepix\GatewayTemplate
 * @since   1.0.0
 */
abstract class Base {
	/**
	 * Option and menu slug. Must be overwritten in child class
	 *
	 * @var string
	 */
	protected string $slug = 'settings';

	/**
	 * Option name
	 *
	 * @var string
	 */
	protected string $option_name;

	/**
	 * Menu slug
	 *
	 * @var string
	 */
	protected string $menu_slug;

	/**
	 * Templates path
	 *
	 * @var string
	 */
	protected string $templates_path;

	/**
	 * Templates path
	 *
	 * @var string
	 */
	protected string $fields_path;

	/**
	 * Templates path
	 *
	 * @var string
	 */
	protected string $global_fields_path = 'admin/pages/global-fields/';

	/**
	 * User capability to see page.
	 *
	 * @var string
	 */
	protected string $capability = 'manage_options';

	/**
	 * Array of sections and fields to register
	 *
	 * @var array
	 */
	protected array $sections = [];

	/**
	 * Base constructor.
	 */
	public function __construct() {
		$this->option_name = gateway_template()->get_option( 'id' ) . '-' . $this->slug;

		$this->menu_slug      = gateway_template()->get_option( 'id' ) . '-' . $this->slug . '-page';
		$this->templates_path = "admin/pages/$this->slug/";
		$this->fields_path    = $this->templates_path . 'fields/';

		add_action( 'admin_menu', [ $this, 'create_menu' ], 10 );
		add_action( 'admin_init', [ $this, 'register_setting' ], 10 );
		add_action( 'admin_init', [ $this, 'create_settings_fields' ], 100 );
	}

	/**
	 * Register submenu in menu
	 *
	 * @see add_menu_page
	 * @see add_submenu_page
	 * @see print_page for $callback
	 * @see https://developer.wordpress.org/resource/dashicons for icons
	 */
	public function create_menu() {
	}

	/**
	 * Print page content
	 *
	 * @see get_admin_page_title
	 * @see do_settings_sections
	 * @see submit_button
	 */
	public function print_page() {
		gateway_template()->include_template( "{$this->templates_path}page.php" );
	}

	/**
	 * Register settings
	 *
	 * @see add_settings_section
	 * @see add_settings_field
	 */
	public function create_settings_fields() {
		foreach ( $this->sections as $section_id => $section ) {
			$this->add_settings_section(
				$section_id,
				$section['title'] ?? '',
				$section['callback'] ?? null,
				$section['args'] ?? []
			);

			foreach ( $section['fields'] ?? [] as $field_id => $field ) {
				$field['args']                = $field['args'] ?? [];
				$field['args']['default']     = $field['default'] ?? '';
				$field['args']['description'] = $field['description'] ?? '';

				$this->add_settings_field(
					"{$section_id}_{$field_id}",
					$field['type'] ?? 'text',
					$field['title'] ?? '',
					$section_id,
					$field['args'] ?? []
				);
			}
		}
	}

	/**
	 * Wrapper for register_setting
	 *
	 * @see \register_setting
	 *
	 * @see register_setting
	 */
	public function register_setting() {
		register_setting(
			$this->option_name,
			$this->option_name,
			[
				'sanitize_callback' => [ $this, 'sanitize_settings' ],
				'default'           => [],
			]
		);
	}

	/**
	 * Sanitize settings
	 *
	 * @param array $settings setting value to sanitize.
	 *
	 * @return array
	 */
	public function sanitize_settings( array $settings ): array {
		$sanitized_settings = [];

		foreach ( $this->sections as $section_id => $section ) {
			foreach ( $section['fields'] ?? [] as $field_id => $field ) {
				$field_id = "{$section_id}_{$field_id}";

				if ( isset( $settings[ $field_id ] ) ) {
					$sanitized_settings[ $field_id ] = $this->sanitize_setting(
						$settings[ $field_id ],
						$field_id,
						$field['type'] ?? 'text'
					);
				} else {
					$sanitized_settings[ $field_id ] = $field['default'] ?? '';
				}
			}
		}

		return $sanitized_settings;
	}

	/**
	 * Sanitize field by type
	 *
	 * @param string $value field value.
	 * @param string $field_id field id.
	 * @param string $type field type.
	 *
	 * @return string
	 */
	public function sanitize_setting( string $value, string $field_id, string $type ): string {
		if ( method_exists( $this, "sanitize_setting_$type" ) ) {
			return $this->{"sanitize_setting_$type"}( $value, $field_id );
		}

		return $this->sanitize_setting_text( $value );
	}

	/**
	 * Sanitize text field
	 *
	 * @param string $value field value.
	 *
	 * @return string
	 */
	public function sanitize_setting_text( string $value ): string {
		return wp_kses_post( trim( stripslashes( $value ) ) );
	}

	/**
	 * Wrapper for add_settings_section
	 *
	 * @see \add_settings_section
	 *
	 * @param string        $id             Slug-name to identify the section. Used in the 'id' attribute of tags.
	 * @param string        $title          Formatted title of the section. Shown as the heading for the section.
	 * @param callable|null $callback       Function that echos out any content at the top of the section (between heading and fields).
	 * @param array         $args           {
	 *         Arguments used to create the settings section.
	 *
	 *     @type string $before_section HTML content to prepend to the section's HTML output.
	 *                                  Receives the section's class name as `%s`. Default empty.
	 *     @type string $after_section  HTML content to append to the section's HTML output. Default empty.
	 *     @type string $section_class  The class name to use for the section. Default empty.
	 * }
	 */
	protected function add_settings_section( string $id, string $title = '', ?callable $callback = null, $args = [] ) {
		add_settings_section(
			$this->option_name . '-' . $id,
			$title,
			$callback,
			$this->menu_slug,
			$args
		);
	}

	/**
	 * Wrapper for add_settings_field
	 *
	 * @see \add_settings_field
	 *
	 * @param string $id      Slug-name to identify the field. Used in the 'id' attribute of tags.
	 * @param string $type    Type of setting field to render it from $template_path.
	 * @param string $title   Formatted title of the field. Shown as the label for the field during output.
	 * @param string $section The slug-name of the section of the settings page in which to show the box.
	 * @param array  $args    Optional. Extra arguments that get passed to the callback function.
	 */
	protected function add_settings_field( string $id, string $type, string $title, string $section, $args = [] ) {
		$field_id = $this->option_name . '-' . $section . '-' . $id;

		add_settings_field(
			$field_id,
			"<label for='$field_id'>$title</label>",
			function ( $args ) use ( $type ) {
				$this->setting_field_template( $type, $args );
			},
			$this->menu_slug,
			$this->option_name . '-' . $section,
			wp_parse_args(
				$args,
				[
					'id'    => $field_id,
					'name'  => "{$this->option_name}[$id]",
					'value' => $this->get_option( $id ) ?? ( $args['default'] ?? '' ),
				]
			)
		);
	}

	/**
	 * Wrapper for settings_fields and do_settings_sections
	 *
	 * @see \settings_fields
	 * @see \do_settings_sections
	 */
	public function do_settings_section() {
		settings_fields( $this->option_name );
		do_settings_sections( $this->menu_slug );
	}

	/**
	 * Print field template.
	 *
	 * @param string $type template name.
	 * @param array  $args template args.
	 */
	protected function setting_field_template( string $type, array $args ) {
		gateway_template()->include_template(
			gateway_template()->template_exists( "{$this->fields_path}{$type}.php" ) ?
				"{$this->fields_path}{$type}.php" :
				"{$this->global_fields_path}{$type}.php",
			$args,
		);
	}

	/**
	 * Get option(s)
	 *
	 * @param string $field Optional. Field name to get. Default settings array.
	 * @param string $section Optional. Field section name. May be specified in $field as "{$section}_{$field}".
	 *
	 * @return mixed
	 */
	public function get_option( string $field = '', string $section = '' ) {
		if ( ! empty( $section ) ) {
			$field = "{$section}_{$field}";
		}

		$option = get_option( $this->option_name );

		if ( empty( $field ) ) {
			return $option;
		}

		if ( ! is_array( $option ) ) {
			return null;
		}

		return $option[ $field ] ?? null;
	}

	/**
	 * Check if current page
	 *
	 * @return bool
	 */
	public function is_current_page(): bool {
		return isset( $_GET['page'] ) && $this->menu_slug === $_GET['page'];
	}

	/**
	 * Get page url
	 *
	 * @return string
	 */
	public function get_page_url(): string {
		return get_admin_url( null, "admin.php?page=$this->menu_slug" );
	}
}
