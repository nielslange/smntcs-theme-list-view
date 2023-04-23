<?php
/**
 * Theme List Table
 *
 * @package    SMNTCS_Themes_List_Screen_Options
 * @subpackage SMNTCS_Themes_List_Screen_Options/includes
 * @since      1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Theme List Table class.
 *
 * @since 1.0.0
 */
class SMNTCS_Themes_List_Screen_Options {
	/**
	 * Initialize the class
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_filter( 'set-screen-option', array( $this, 'set_screen_option' ), 5, 3 );
	}

	/**
	 * Register custom settings.
	 *
	 * @return void
	 */
	public function admin_init() {
		register_setting( 'options', 'themes_per_page' );
	}

	/**
	 * Set screen option.
	 *
	 * @param string $status The value to save instead of the option value. Default false (to skip saving the current option).
	 * @param string $option The option name.
	 * @param string $value  The option value.
	 * @return string The option value or the option status.
	 */
	public function set_screen_option( $status, $option, $value ) {
		return ( 'themes_per_page' === $option ) ? $value : $status;
	}

	/**
	 * Add screen options.
	 *
	 * @return void
	 */
	public function smntcs_theme_list_view_screen_options() {
		add_screen_option( 'per_page',
			array(
				'label'   => __( 'Number of items per page:' ), // phpcs:ignore WordPress.WP.I18n.MissingArgDomain
				'default' => get_option( 'themes_per_page', 23 ),
				'option'  => 'themes_per_page',
			)
		);
	}
}

( new SMNTCS_Themes_List_Screen_Options() );
