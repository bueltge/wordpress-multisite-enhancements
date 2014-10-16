<?php
/**
 * On the network theme page, show which blog have the theme active
 *
 * @since    2013-07-22
 * @version  2014-09-13
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

		if ( 'active_blogs' !== $column_name ) {
			return NULL;
		}

		$output = '';

		$active_on_blogs = $this->is_theme_active_on_blogs( $theme_key );

		// Check, if is a child theme and return parent
		$child_context = '';
		$is_child      = $this->is_child( $theme_data );
		if ( $is_child ) {
			$parent_name = $theme_data->parent()->Name;
			$child_context .= sprintf(
				'<br>' . __( 'This is a child theme of %s.' ),
				'<strong>' . esc_attr( $parent_name ) . '</strong>'
			);
		}

		if ( empty( $active_on_blogs ) ) {
			$output .= __( '<nobr>Not Activated</nobr>', 'multisite_enhancements' );
			$output .= $child_context;
		} else {
			$output .= '<ul>';

			foreach ( $active_on_blogs as $key => $value ) {
				$output .= '<li title="Blog ID: ' . $key . '">';
				$output .= '<nobr><a href="' . get_admin_url(
						$key
					) . 'themes.php' . '">' . $value[ 'name' ] . '</a></nobr>';
				$output .= $child_context;
				$output .= '</li>';
			}

			$output .= '</ul>';
		}

		echo $output;
	}

	/**
	 * Is theme active in blogs
	 * Return Array with values to each theme
	 *
	 * @since   0.0.2
	 *
	 * @param   String
	 * @param   Array
	 *
	 * @return  Array
	 */
	public function is_theme_active_on_blogs( $theme_key ) {

		if ( function_exists( 'wp_get_sites' ) ) {
			// Since 3.7 inside the Core
			$blogs = wp_get_sites();
		} else {
			// use alternative to core function get_blog_list()
			$blogs = Multisite_Core::get_blog_list( 0, 'all' );
		}

		$active_in_themes = array();
		foreach ( (array) $blogs as $blog ) {

			$active_theme = get_blog_option( $blog[ 'blog_id' ], 'stylesheet' );
			if ( empty( $active_theme ) ) {
				continue;
			}

			if ( $active_theme === $theme_key ) {
				$blogname = get_blog_details( $blog[ 'blog_id' ] )->blogname;
				$blogpath = get_blog_details( $blog[ 'blog_id' ] )->path;
				$active_in_themes[ $blog[ 'blog_id' ] ] = array(
					'name' => $blogname,
					'path' => $blogpath
				);
			}

		}

		return $active_in_themes;
	}

	/**
	 * Check, the current theme have a parent value and is a child theme
	 *
	 * @param $theme_data
	 *
	 * @return bool
	 */
	public function is_child( $theme_data ) {

		// For limitation of empty() write in var
		$parent = $theme_data->parent();

		if ( ! empty( $parent ) ) {
			return TRUE;
		}

		return FALSE;
	}

} // end class