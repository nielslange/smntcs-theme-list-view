<?php
/**
 * Theme List Table
 *
 * @package    SMNTCS_Themes_List_View
 * @subpackage SMNTCS_Themes_List_View/includes
 * @since      1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/*
 * Load the WP_List_Table class if it doesn't already exist.
 */
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Theme List Table class.
 *
 * @since 1.0.0
 */
class SMNTCS_Themes_List_Table extends WP_List_Table {
	/**
	 * Initialize the class
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => 'theme',
				'plural'   => 'themes',
				'ajax'     => false,
			)
		);
	}

	/**
	 * Display the nonce field.
	 *
	 * @return void
	 */
	public function display_nonce_field() {
		wp_nonce_field( 'smntcs_theme_list_view_action', 'smntcs_theme_list_view_nonce' );
	}


	/**
	 * Prepare the items for the table to process
	 *
	 * @return array The columns to be displayed.
	 */
	public function get_columns() {
		return array(
			'activate'    => '',
			'name'        => 'Name',
			'author'      => 'Author',
			'version'     => 'Version',
			'requiresWP'  => 'Requires WP',
			'requiresPHP' => 'Requires PHP',
		);
	}

	/**
	 * Prepare the items for the table to process
	 *
	 * @return array The columns to be sorted.
	 */
	public function get_sortable_columns() {
		return array(
			'name'        => array( 'name', false ),
			'author'      => array( 'author', false ),
			'version'     => array( 'version', false ),
			'requiresWP'  => array( 'requiresWP', false ),
			'requiresPHP' => array( 'requiresPHP', false ),
		);
	}

	/**
	 * Prepare the items for the table to process
	 *
	 * @param object|array $item The current item.
	 * @param string       $column_name The current column name.
	 * @return array The item to be displayed.
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'name':
			case 'author':
			case 'version':
			case 'requiresWP':
			case 'requiresPHP':
				return $item[ $column_name ];
			default:
				return $item;
		}
	}

	/**
	 * Prepare the items for the table to process
	 *
	 * @param string $item The current item.
	 * @return mixed A regular button, for inactive themes, or a disabled button, for the active theme.
	 */
	public function column_activate( $item ) {
		$current_theme = wp_get_theme();

		$activate_url = wp_nonce_url(
			add_query_arg(
				array(
					'action'     => 'switch-theme',
					'stylesheet' => rawurlencode( $item['stylesheet'] ),
				),
			admin_url( 'themes.php' )),
			'switch-theme_' . $item['stylesheet']
		);

		if ( 0 === strpos( $item['name'], $current_theme->get( 'Name' ) ) ) {
			return sprintf(
				'<button data-url="%s" class="button activate-theme" disabled>%s</button>',
				esc_url( $activate_url ),
				esc_html__( 'Active', 'smntcs-theme-list-view' )
			);
		}

		return sprintf(
			'<button data-url="%s" class="button activate-theme">%s</button>',
			esc_url( $activate_url ),
			esc_html__( 'Activate', 'smntcs-theme-list-view' )
		);
	}

	/**
	 * Prepare the items for the table to process
	 *
	 * @return void
	 */
	public function prepare_items() {
		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );

		$installed_themes = wp_get_themes();
		$themes_data      = array();
		$images_url       = plugins_url( '/', SMNTCS_THEME_LIST_VIEW_FILE ) . 'assets/images/';

		foreach ( $installed_themes as $stylesheet => $theme ) {
			$themes_data[] = array(
				'name'        => sprintf(
					'%1$s <a href="%2$s"><img src="%3$s" alt="%4$s" title="%4$s"></a>',
					$theme->get( 'Name' ),
					$theme->get( 'ThemeURI' ),
					$images_url . 'external-link-alt-solid.svg',
					sprintf(
						// translators: %s is the name of the theme.
						__( 'Open %s theme', 'smntcs-theme-list-view' ),
						$theme->get( 'Name' )
					)
				),
				'author'      => sprintf(
					'%1$s <a href="%2$s"><img src="%3$s" alt="%4$s" title="%4$s"></a>',
					$theme->get( 'Author' ),
					$theme->get( 'AuthorURI' ),
					$images_url . 'external-link-alt-solid.svg',
					sprintf(
						// translators: %s is the website of the author.
						__( 'Open website of %s', 'smntcs-theme-list-view' ),
						$theme->get( 'Author' )
					)
				),
				'version'     => $theme->get( 'Version' ) ? $theme->get( 'Version' ) : __( 'n/a', 'smntcs-theme-list-view' ),
				'requiresWP'  => $theme->get( 'RequiresWP' ) ? $theme->get( 'RequiresWP' ) : __( 'n/a', 'smntcs-theme-list-view' ),
				'requiresPHP' => $theme->get( 'RequiresPHP' ) ? $theme->get( 'RequiresPHP' ) : __( 'n/a', 'smntcs-theme-list-view' ),
				'stylesheet'  => $stylesheet,
			);
		}

		usort( $themes_data, array( $this, 'usort_reorder' ) );

		$current_page = $this->get_pagenum();
		$per_page     = $this->get_items_per_page( 'themes_per_page', 20 );
		$total_items  = count( $themes_data );

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
		) );

		$themes_data = array_slice( $themes_data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		$this->items = $themes_data;
	}

	/**
	 * Custom sorting function to reorder two elements based on the provided request parameters.
	 *
	 * @param  array $element1 First element to compare.
	 * @param  array $element2 Second element to compare.
	 * @return int Returns a positive value if the order is 'asc', a negative value if the order is 'desc'.
	 */
	public function usort_reorder( $element1, $element2 ) {
		$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'name';
		$order   = ( ! empty( $_REQUEST['order'] ) ) ? sanitize_text_field( $_REQUEST['order'] ) : 'asc';
		$result  = strcasecmp( $element1[ $orderby ], $element2[ $orderby ] );

		return ( 'asc' === $order ) ? $result : -$result;
	}
}
