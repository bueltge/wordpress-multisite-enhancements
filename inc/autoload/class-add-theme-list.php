<?php
/**
 * On the network theme page, show which blog have the theme active.
 *
 * @since   2013-07-22
 * @version 2017-02-22
 * @package WordPress
 */

add_action( 'init', array( 'Multisite_Add_Theme_List', 'init' ) );

/**
 * On the network theme page, show which blog have the theme active.
 *
 * Class Multisite_Add_Theme_List
 */
class Multisite_Add_Theme_List {

	/**
	 * Value to get sites in the Network.
	 *
	 * @since 2015-02-26
	 * @var int
	 */
	private $sites_limit = 9999;

	/**
	 * Member variable to store data about active theme for each blog.
	 *
	 * @since    21/02/2015
	 * @var     array
	 */
	private $blogs_themes;

	/**
	 * String for the transient string, there save the blog themes.
	 *
	 * @since  2015-02-21
	 * @var    string
	 */
	static protected $site_transient_blogs_themes = 'blogs_themes';

	/**
	 * Define the allowed html tags for wp_kses.
	 *
	 * @var array
	 */
	static protected $wp_kses_allowed_html = array(
		'br'   => array(),
		'nobr' => array(
			'class' => array(),
		),
		'span' => array(
			'class' => array(),
		),
		'ul'   => array(),
		'li'   => array(
			'title' => array(),
		),
		'a'    => array(
			'href'  => array(),
			'title' => array(),
		),
	);

	/**
	 * Initialize the class.
	 */
	public static function init() {

		$class = __CLASS__;
		if ( empty( $GLOBALS[ $class ] ) ) {
			$GLOBALS[ $class ] = new $class;
		}
	}

	/**
	 * Init function to register all used hooks.
	 *
	 * @since   0.0.2
	 */
	public function __construct() {

		// Delete transient on themes page.
		add_action( 'load-themes.php', array( $this, 'development_helper' ) );

		// Fires after the theme is switched.
		add_action( 'switch_theme', array( $this, 'clear_themes_site_transient' ), 10, 1 );

		if ( ! is_network_admin() ) {
			return;
		}

		/**
		 * Filter to change the value for get sites inside the network.
		 *
		 * @since 2015-02-26
		 * @type integer
		 */
		$this->sites_limit = (int) apply_filters( 'multisite_enhancements_sites_limit', $this->sites_limit );

		add_filter( 'manage_themes-network_columns', array( $this, 'add_themes_column' ), 10, 1 );
		add_action( 'manage_themes_custom_column', array( $this, 'manage_themes_custom_column' ), 10, 3 );

		add_action( 'update_site_option_allowedthemes', array( $this, 'clear_themes_site_transient' ), 10, 1 );
	}

	/**
	 * Run helpers if the debug constant is true to help on development, debugging.
	 *
	 * @since  2016-10-23
	 * @return bool
	 */
	public function development_helper() {

		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			return FALSE;
		}

		add_action( 'network_admin_notices', array( $this, 'notice_about_clear_cache' ) );
		$this->clear_themes_site_transient();

		return TRUE;
	}

	/**
	 * Print Network Admin Notices to inform, that the transient are deleted.
	 *
	 * @since 2016-10-23
	 */
	public function notice_about_clear_cache() {

		$class   = 'notice notice-info';
		$message = esc_attr__( 'Multisite Enhancements: Delete site transients for the theme usage to help on development, debugging. The constant WP_DEBUG is true.',
		                       'multisite-enhancements' );
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
	}

	/**
	 * Add in a column header.
	 *
	 * @since   0.0.2
	 *
	 * @param array $columns An array of displayed site columns.
	 *
	 * @return array
	 */
	public function add_themes_column( $columns ) {

		$columns[ 'active_blogs' ] = _x( '<nobr>Active in </nobr>', 'column name', 'multisite-enhancements' );

		return $columns;
	}

	/**
	 * Get data for each row on each theme.
	 * Print the string about the usage.
	 *
	 * @since   0.0.2
	 *
	 * @param  String         $column_name Name of the column.
	 * @param  String         $theme_key   Path to the theme file.
	 * @param array|\WP_Theme $theme_data  An array of theme data.
	 *
	 * @return void
	 */
	public function manage_themes_custom_column( $column_name, $theme_key, \WP_Theme $theme_data ) {

		if ( 'active_blogs' !== $column_name ) {
			return NULL;
		}

		$output = '';

		$active_on_blogs = $this->is_theme_active_on_blogs( $theme_key );

		// Check, if is a child theme and return parent.
		$child_context = '';
		$is_child      = $this->is_child( $theme_data );
		if ( $is_child ) {
			$parent_name = $theme_data->parent()->Name;
			$child_context .= sprintf(
				'<br>' . esc_attr__( 'This is a child theme of %s.', 'multisite-enhancements' ),
				'<strong>' . esc_attr( $parent_name ) . '</strong>'
			);
		}

		// Check if used as a parent theme for a child.
		$parent_context = '';
		$used_as_parent = $this->is_parent( $theme_key );
		if ( count( $used_as_parent ) ) {
			$parent_context .= '<br>' . esc_attr__( 'This is used as a parent theme by:',
			                                        'multisite-enhancements' ) . ' ';
			$parent_context .= implode( ', ', $used_as_parent );
		}

		if ( ! $active_on_blogs ) {
			// Translators: The theme is not activated, the string is for each plugin possible.
			$output .= __( '<nobr>Not Activated</nobr>', 'multisite-enhancements' );
			$output .= $child_context;
			$output .= $parent_context;
		} else {
			$output .= '<ul>';

			foreach ( $active_on_blogs as $key => $value ) {

				// Check the site for archived.
				$class = $hint = '';
				if ( $this->is_archived( $key ) ) {
					$class = ' class="site-archived"';
					$hint  = esc_attr__( ', Archived site', 'multisite-enhancements' );
				}

				$output .= '<li' . $class . ' title="Blog ID: ' . $key . $hint . '">';
				$output .= '<nobr><a href="' . get_admin_url( $key ) . 'themes.php">'
				           . $value[ 'name' ] . '</a>' . $hint . '</nobr>';
				$output .= $child_context;
				$output .= '</li>';
			}

			$output .= '</ul>';
			$output .= $parent_context;
		}

		echo wp_kses( $output, self::$wp_kses_allowed_html );
	}

	/**
	 * Is theme active in blogs.
	 *
	 * Return array with values to each theme
	 *
	 * @since   0.0.2
	 *
	 * @param String $theme_key The key of each theme.
	 *
	 * @return array
	 */
	public function is_theme_active_on_blogs( $theme_key ) {

		$blogs_themes = $this->get_blogs_themes();

		$active_in_themes = array();

		foreach ( (array) $blogs_themes as $blog_id => $data ) {

			if ( $data[ 'stylesheet' ] === $theme_key ) {
				$active_in_themes[ $blog_id ] = array(
					'name' => $data[ 'blogname' ],
					'path' => $data[ 'blogpath' ],
				);
			}
		}

		return $active_in_themes;
	}

	/**
	 * Check, if the status of the site archived.
	 *
	 * @param integer $site_id ID of the site.
	 *
	 * @return bool
	 */
	public function is_archived( $site_id ) {

		$site_id = (int) $site_id;
		return (bool) get_blog_details( $site_id )->archived;
	}

	/**
	 * Check, the current theme have a parent value and is a child theme.
	 *
	 * @param array|\WP_Theme $theme_data An array of theme data.
	 *
	 * @return bool
	 */
	public function is_child( \WP_Theme $theme_data ) {

		return (bool) $theme_data->parent();
	}

	/**
	 * Gets an array of themes which have the selected one as parent.
	 *
	 * @since   21/02/2015
	 * @version 2017-02-22
	 *
	 * @param   string $theme_key The key of each theme.
	 *
	 * @return  array
	 */
	public function is_parent( $theme_key ) {

		$blogs_themes = $this->get_blogs_themes();
		$parent_of    = array();

		/**
		 * Provide the data to the Theme of each site.
		 *
		 * @var array $data
		 */
		foreach ( (array) $blogs_themes as $blog_id => $data ) {

			$template = FALSE;
			if ( array_key_exists( 'template', $data ) ) {
				$template = $data[ 'template' ];
			}

			if ( $template === $theme_key && $template !== $data[ 'stylesheet' ] ) {
				$theme       = wp_get_theme( $data[ 'stylesheet' ] );
				$parent_of[] = $theme->get( 'Name' );
			}
		}

		return array_unique( $parent_of );
	}

	/**
	 * Gets an array of blog data including active theme for each blog.
	 *
	 * @since  21/02/2015
	 *
	 * @return array
	 */
	public function get_blogs_themes() {

		// See if the data is present in the variable first.
		if ( $this->blogs_themes ) {
			return $this->blogs_themes;

			// If not, see if we can load data from the transient.
		} elseif ( FALSE === ( $this->blogs_themes = get_site_transient( self::$site_transient_blogs_themes ) ) ) {

			// Cannot load data from transient, so load from DB and set transient.
			$this->blogs_themes = array();

			$blogs = (array) Multisite_Core::get_blog_list( 0, $this->sites_limit );

			/**
			 * Data to each site of the network, blogs.
			 *
			 * @var array $blog
			 */
			foreach ( $blogs as $blog ) {

				// Convert object to array.
				$blog = (array) $blog;

				$this->blogs_themes[ $blog[ 'blog_id' ] ]                 = $blog;
				$this->blogs_themes[ $blog[ 'blog_id' ] ][ 'blogpath' ]   = get_blog_details(
					$blog[ 'blog_id' ]
				)->path;
				$this->blogs_themes[ $blog[ 'blog_id' ] ][ 'blogname' ]   = get_blog_details(
					$blog[ 'blog_id' ]
				)->blogname;
				$this->blogs_themes[ $blog[ 'blog_id' ] ][ 'template' ]   = get_blog_option(
					$blog[ 'blog_id' ], 'template'
				);
				$this->blogs_themes[ $blog[ 'blog_id' ] ][ 'stylesheet' ] = get_blog_option(
					$blog[ 'blog_id' ], 'stylesheet'
				);
			}

			if ( ! $this->development_helper() ) {
				set_site_transient( self::$site_transient_blogs_themes, $this->blogs_themes );
			}
		}

		// Data should be here, if loaded from transient or DB.
		return $this->blogs_themes;
	}

	/**
	 * Clears the $blogs_themes site transient when any themes are activated/deactivated.
	 *
	 * @since 2015-02-21
	 */
	public function clear_themes_site_transient() {

		delete_site_transient( self::$site_transient_blogs_themes );
	}

} // end class
