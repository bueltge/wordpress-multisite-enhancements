<?php
/**
 * On the network theme page, show which blog have the theme active
 *
 * @since    2013-07-22
 * @version  2014-09-13
 */

add_action( 'init', array( 'Multisite_Add_Theme_List', 'init' ) );

class Multisite_Add_Theme_List {

	/**
	 * member variable to store data about active theme for each blog
	 *
	 * @since	21/02/2015
	 * @var     Array
	 */
	private $blogs_themes;

	/**
	 * member variable to store data about all themes
	 *
	 * @since	21/02/2015
	 * @var     Array
	 */
	private $all_themes;

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

		add_action( 'switch_theme', array( $this, 'clear_themes_site_transient'), 10, 1 );
		add_action( 'update_site_option_allowedthemes', array( $this, 'clear_themes_site_transient'), 10, 1 );
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

		// Check if used as a parent theme for a child
		$parent_context = '';
		$used_as_parent = $this->is_parent( $theme_key );
		if ( count( $used_as_parent ) ) {
			$parent_context .= '<br>' . __( 'This is used as a parent theme by: ', 'multisite_enhancements' );
			$parent_context .= implode( ", ", $used_as_parent );
		}


		if ( empty( $active_on_blogs ) ) {
			$output .= __( '<nobr>Not Activated</nobr>', 'multisite_enhancements' );
			$output .= $child_context;
			$output .= $parent_context;
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
			$output .= $parent_context;
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

		$blogs_themes = $this->get_blogs_themes();
		
		$active_in_themes = array();

		foreach ( (array) $blogs_themes as $blog_id => $data ) {

			if ( $data["stylesheet"] === $theme_key ) {
				$active_in_themes[ $blog_id ] = array(
					'name' => $data['blogname'],
					'path' => $data['blogpath']
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

	/**
	 * gets an array of themes which have the selected one as parent
	 *
	 * @since   21/02/2015
	 *
	 * @return  Array
	 */
	public function is_parent( $theme_key ) {

		$blogs_themes = $this->get_blogs_themes();
		$parent_of = array();

		foreach ( (array) $blogs_themes as $blog_id => $data ) {

			if ( isset($data["template"]) && $data["template"] !== $data["stylesheet"] && $data["template"] === $theme_key ) {
				$theme = wp_get_theme( $data["stylesheet"] );
				$parent_of[] = $theme->get( 'Name' );
			}
		}
		return $parent_of;

	}

	/**
	 * gets an array of blog data including active theme for each blog
	 *
	 * @since   21/02/2015
	 *
	 * @return  Array
	 */
	public function get_blogs_themes() {

		// see if the data is present in the variable first
		if ( $this->blogs_themes ) {
			return $this->blogs_themes;

		// if not, see if we can load data from the transient
		} else if ( false === ( $this->blogs_themes = get_site_transient( 'blogs_themes' ) ) ) {
			
			// cannot load data from transient, so load from DB and set transient
			$this->blogs_themes = array();
			
			if ( function_exists( 'wp_get_sites' ) ) {
				// Since 3.7 inside the Core
				$blogs = wp_get_sites();
			} else {
				// use alternative to core function get_blog_list()
				$blogs = Multisite_Core::get_blog_list( 0, 'all' );
			}
			
			foreach ( (array) $blogs as $blog ) {
				$this->blogs_themes[ $blog['blog_id'] ] = $blog;
				$this->blogs_themes[ $blog['blog_id'] ]['blogpath'] = get_blog_details( $blog['blog_id'] )->path;
				$this->blogs_themes[ $blog['blog_id'] ]['blogname'] = get_blog_details( $blog['blog_id'] )->blogname;
				$this->blogs_themes[ $blog['blog_id'] ]['template'] = get_blog_option( $blog[ 'blog_id' ], 'template' );
				$this->blogs_themes[ $blog['blog_id'] ]['stylesheet'] = get_blog_option( $blog[ 'blog_id' ], 'stylesheet' );
			}
			set_site_transient( 'blogs_themes', $this->blogs_themes );
		
		}
		// data should be here, if loaded from transient or DB
		return $this->blogs_themes;
	}

	/**
	 * clears the $blogs_themes site transient when any themes are activated/deactivated
	 */
	public function clear_themes_site_transient( $theme )
	{
		delete_site_transient( 'blogs_themes' );
	}


} // end class