<?php
/**
 * Theme List View
 *
 * @package    SMNTCS_Themes_List_View
 * @subpackage SMNTCS_Themes_List_View/includes
 * @since      1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( file_exists( plugin_dir_path( __FILE__ ) . '/includes/class-smntcs-themes-list-screen-options.php' ) ) {
	require_once plugin_dir_path( __FILE__ ) . '/includes/class-smntcs-themes-list-screen-options.php';
}

/**
 * Theme List View class.
 *
 * @since 1.0.0
 */
class SMNTCS_Themes_List_View {
	/**
	 * Initialize the class
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'plugin_action_links_' . plugin_basename( SMNTCS_THEME_LIST_VIEW_FILE ), array( $this, 'add_plugin_settings_link' ) );
		add_action( 'plugins_loaded', array( $this, 'load_required_files' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts_and_styles' ) );
		add_action( 'wp_ajax_wp_ajax_switch_theme', array( $this, 'handle_theme_switch_ajax_request' ) );
		add_action( 'admin_head', array( $this, 'add_theme_list_table_styles' ) );
		add_action( 'admin_menu', array( $this, 'add_custom_settings_page' ) );
	}

	/**
	 * Add settings link on plugin page
	 *
	 * @param array $url The original URL.
	 * @return array The updated URL.
	 */
	public function add_plugin_settings_link( $url ) {
		$admin_url     = admin_url( 'themes.php?page=smntcs-theme-list-view' );
		$settings_link = '<a href="' . esc_url( $admin_url ) . '">' . __( 'List view ', 'smntcs-theme-list-view' ) . '</a>';
		array_unshift( $url, $settings_link );

		return $url;
	}


	/**
	 * Load required files.
	 *
	 * @return void
	 */
	public function load_required_files() {
		if ( file_exists( plugin_dir_path( __FILE__ ) . 'class-smntcs-themes-list-table.php' ) ) {
			require_once 'class-smntcs-themes-list-table.php';
		}
	}

	/**
	 * Enqueue scripts and styles
	 *
	 * @param string $hook The current admin page.
	 * @return void
	 */
	public function enqueue_admin_scripts_and_styles( $hook ) {
		if ( 'appearance_page_smntcs-theme-list-view' !== $hook ) {
			return;
		}

		wp_enqueue_script( 'smntcs-theme-list-view-js', plugin_dir_url( SMNTCS_THEME_LIST_VIEW_FILE ) . 'assets/js/theme-activation.js', array( 'jquery' ), '1.0.0', true );
		wp_localize_script( 'smntcs-theme-list-view-js', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}

	/**
	 * Switch theme
	 *
	 * @return void
	 */
	public function handle_theme_switch_ajax_request() {
		check_ajax_referer( 'switch-theme_' . $_POST['stylesheet'], '_wpnonce', true );

		if ( ! current_user_can( 'switch_themes' ) ) {
			wp_send_json_error( array( 'message' => 'You do not have sufficient permissions to switch themes.' ) );
		}

		switch_theme( $_POST['stylesheet'] );
		wp_send_json_success( array( 'message' => 'Theme activated successfully.' ) );
	}

	/**
	 * Add settings page
	 *
	 * @return void
	 */
	public function add_custom_settings_page() {
		$hook = add_submenu_page(
			'themes.php',
			'List themes',
			'List view',
			'manage_options',
			'smntcs-theme-list-view',
			array( $this, 'add_custom_settings_page_content' ),
			1
		);

		$screen_options = new SMNTCS_Themes_List_Screen_Options();
		add_action( 'load-' . $hook, array( $screen_options, 'smntcs_theme_list_view_screen_options' ) );
	}

	/**
	 * Display the content of the settings page
	 *
	 * @return void
	 */
	public function add_custom_settings_page_content() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'smntcs-theme-list-view' ) );
		}

		if ( ! class_exists( 'SMNTCS_Themes_List_Table' ) ) {
			wp_die( __( 'The class Themes_List_Table could not be loaded.', 'smntcs-theme-list-view' ) );
		}

		$themes_list_table = new SMNTCS_Themes_List_Table();
		$themes_list_table->prepare_items();

		ob_start();
		$themes_list_table->display();
		$table_output = ob_get_clean();

		printf(
			'<div class="wrap">
				<h1>Themes</h1>
				<form id="themes-filter" method="post">%s</form>
			</div>',
			$table_output // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	}

	/**
	 * Add custom styles for the theme list table
	 *
	 * @return void
	 */
	public function add_theme_list_table_styles() {
		$css_styles = '
			/* Set the width of the Activate column to 10% */
			.wp-list-table .column-activate {
				width: 10%;
			}

			/* Prevent the Author column from having a width of 10% */
			.wp-list-table .column-author {
				width: auto;
			}

			.column-name img,
			.column-author img {
				height: 12px;
				width: 12px;
				float: none;
				padding-left: 3px;
			}
			';

		printf( '<style>%s</style>', esc_html( $css_styles ) );
	}
}

( new SMNTCS_Themes_List_View() );
