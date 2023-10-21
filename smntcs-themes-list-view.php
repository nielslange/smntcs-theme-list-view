<?php
/**
 * Plugin Name:         SMNTCS Theme List View
 * Plugin URI:          https://github.com/nielslange/smntcs-theme-list-view
 * Description:         Display installed themes in a list view.
 * Author:              Niels Lange
 * Author URI:          https://nielslange.de/
 * Text Domain:         smntcs-theme-list-view
 * Version:             1.3
 * Requires at least:   5.0
 * Requires PHP:        7.4
 * License:             GPL v2 or later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package SMNTCS_Theme_List_View
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Define plugin constants.
if ( ! defined( 'SMNTCS_THEME_LIST_VIEW_FILE' ) ) {
	define( 'SMNTCS_THEME_LIST_VIEW_FILE', __FILE__ );
}

// Initialize the plugin.
if ( file_exists( plugin_dir_path( __FILE__ ) . '/includes/class-smntcs-themes-list-view.php' ) ) {
	require_once plugin_dir_path( __FILE__ ) . '/includes/class-smntcs-themes-list-view.php';
}

if ( file_exists( plugin_dir_path( __FILE__ ) . '/includes/class-smntcs-themes-list-screen-options.php' ) ) {
	require_once plugin_dir_path( __FILE__ ) . '/includes/class-smntcs-themes-list-screen-options.php';
}
