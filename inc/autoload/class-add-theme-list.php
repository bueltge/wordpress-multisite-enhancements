<?php
/**
 * On the network theme page, show which blog have the theme active
 *
 * @since    07/22/2013
 * @version  10/27/2013
 */

add_action( 'init', array( 'Multisite_Add_Theme_List', 'init' ) );

class Multisite_Add_Theme_List {

	public static function init() {

		$class = __CLASS__;
		if ( empty( $GLOBALS[ $class ] ) ) {
			$GLOBALS[ $class ] = new $class;
		}
	}

	/**
	 * Init function to register all used hooks
	 *
	 * @since   0.0.2
	 * @return \Multisite_Add_Theme_List
	 */
	public function __construct() {

		if ( ! is_network_admin() ) {
			return NULL;
		}

		add_filter( 'manage_themes-network_columns', array( $this, 'add_themes_column' ), 10, 1 );
		add_action( 'manage_themes_custom_column', array( $this, 'manage_themes_custom_column' ), 10, 3 );
	}

	/**
	 * Add in a column header
	 *
	 * @since   0.0.2
	 *
	 * @param   Array
	 *
	 * @return  String
	 */
	public function add_themes_column( $columns ) {

		$columns[ 'active_blogs' ] = _x( '<nobr>Active in </nobr>', 'column name' );

		return $columns;
	}

	/**
	 * Get data for each row on each theme
	 *
	 * @since   0.0.2
	 *
	 * @param   String
	 * @param   String
	 * @param   Array
	 *
	 * @return  String
	 */
	public function manage_themes_custom_column( $column_name, $theme_key, $theme_data ) {

		if ( $column_name !== 'active_blogs' ) {
			return NULL;
		}

		$output = '';

		$active_on_blogs = $this->is_theme_active_on_blogs( $theme_key, $theme_data );

		if ( empty( $active_on_blogs ) ) {
			$output .= __( '<nobr>Not Activated</nobr>', 'multisite_enhancements' );
		}
		else {
			$output .= '<ul>';

			foreach ( $active_on_blogs as $key => $value ) {
				$output .= '<li title="Blog ID: ' . $key . '"><nobr><a href="' . get_admin_url( $key ) . 'themes.php' . '">' . $value[ 'name' ] . '</a></nobr></li>';
			}

			$output .= '</ul>';
		}

		echo $output;
	}

	/**
	 * Is theme active in blogs
	 * Return Array with vlaues to each theme
	 *
	 * @since   0.0.2
	 *
	 * @param   String
	 * @param   Array
	 *
	 * @return  Array
	 */
	public function is_theme_active_on_blogs( $theme_key, $theme_data ) {

		if ( function_exists( 'wp_get_sites' ) ) {
			// Since 3.7 inside the Core
			$blogs = wp_get_sites();
		}
		else {
			// use alternative to core function get_blog_list()
			$blogs = Multisite_Core::get_blog_list( 0, 'all' );
		}

		$active_in_themes = array();
		foreach ( (array) $blogs as $blog ) {

			$active_theme = get_blog_option( $blog[ 'blog_id' ], 'stylesheet' );

			if ( $active_theme === $theme_key ) {
				$blogname                               = get_blog_details( $blog[ 'blog_id' ] )->blogname;
				$blogpath                               = get_blog_details( $blog[ 'blog_id' ] )->path;
				$active_in_themes[ $blog[ 'blog_id' ] ] = array(
					'name' => $blogname,
					'path' => $blogpath
				);
			}

		}

		return $active_in_themes;
	}

} // end class