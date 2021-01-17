<?php
/**
 * On the network theme page, show which blog have the theme active.
 *
 * @since   2013-07-22
 * @version 2021-01-17
 * @package multisite-enhancement
 */

add_action( 'init', array( 'Multisite_Add_Theme_List', 'init' ) );

/**
 * On the network theme page, show which blog have the theme active.
 *
 * Class Multisite_Add_Theme_List
 */
class Multisite_Add_Theme_List {

	/**
	 * String for the transient string, there save the blog themes.
	 *
	 * @since  2015-02-21
	 * @var    string
	 */
	protected static $site_transient_blogs_themes = 'blogs_themes';
	/**
	 * Define the allowed html tags for wp_kses.
	 *
	 * @var array
	 */
	protected static $wp_kses_allowed_html = array(
		'br'   => array(),
		'span' => array(
			'class' => array(),
		),
		'ul'   => array(
			'id'    => array(),
			'class' => array(),
		),
		'li'   => array(
			'title' => array(),
			'class' => array(),
		),
		'a'    => array(
			'href'    => array(),
			'onclick' => array(),
			'title'   => array(),
		),
		'p'    => array(),
	);
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
	 * Initialize the class.
	 */
	public static function init() {
		$class = __CLASS__;
		if ( empty( $GLOBALS[ $class ] ) ) {
			// phpcs:disable
			$GLOBALS[ $class ] = new $class();
		}
	}

	/**
	 * Print Network Admin Notices to inform, that the transient are deleted.
	 *
	 * @since 2016-10-23
	 */
	public function notice_about_clear_cache() {
		$class   = 'notice notice-info';
		$message = __(
			'Multisite Enhancements: Theme usage information is not cached while WP_DEBUG is true.',
			'multisite-enhancements'
		);
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, esc_attr( $message ) );
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
		$columns['active_blogs'] = '<span class="non-breaking">' . _x( 'Usage', 'column name', 'multisite-enhancements' ) . '</span>';

		return $columns;
	}

	/**
	 * Get data for each row on each theme.
	 * Print the string about the usage.
	 *
	 * @param String $column_name Name of the column.
	 * @param String $theme_key Path to the theme file.
	 * @param array|\WP_Theme $theme_data An array of theme data.
	 *
	 * @return null
	 * @since   0.0.2
	 *
	 */
	public function manage_themes_custom_column( $column_name, $theme_key, \WP_Theme $theme_data ) {
		if ( 'active_blogs' !== $column_name ) {
			return null;
		}

		$output = '';

		$active_on_blogs = $this->is_theme_active_on_blogs( $theme_key );

		// Check, if is a child theme and return parent.
		$child_context = '';
		$is_child      = $this->is_child( $theme_data );
		if ( $is_child ) {
			$parent_name    = $theme_data->parent()->Name;
			$child_context .= '<br>' . sprintf(
				// Translators: The placeholder will be replaced by the name of the parent theme.
				esc_attr__( 'This is a child theme of %s.', 'multisite-enhancements' ),
				'<strong>' . esc_attr( $parent_name ) . '</strong>'
			);
		}

		// Check if used as a parent theme for a child.
		$parent_context = '';
		$used_as_parent = $this->is_parent( $theme_key );
		if ( count( $used_as_parent ) ) {
			$parent_context .= '<br>' . esc_attr__(
				'This is used as a parent theme by:',
				'multisite-enhancements'
			) . ' ';
			$parent_context .= implode( ', ', $used_as_parent );
		}

		if ( ! $active_on_blogs ) {
			// Translators: The theme is not activated, the string is for each plugin possible.
			$output .= __( '<span class="non-breaking">Not Activated</span>', 'multisite-enhancements' );
			$output .= $child_context;
			$output .= $parent_context;
		} else {
			$active_count = count( $active_on_blogs );
			$output      .= '<p>';

			$is_list_hidden = false;
			// Hide the list of sites if the class isn"t loaded or there's less or equal to 4 sites.
			if ( class_exists( 'Add_Css', false ) && $active_count > 4 ) {
				$output .= sprintf(
					// Translators: The placeholder will be replaced by the count and the toggle link of sites there use that themes.
					_n(
						'Active on %2$s %1$d site %3$s',
						'Active on %2$s %1$d sites %3$s',
						$active_count,
						'multisite-enhancements'
					),
					$active_count,
					"<a onclick=\"jQuery('ul[id*=\'siteslist_{$theme_key}\']').slideToggle('swing');\">",
					'</a>'
				);
			} else {
				$output .= sprintf(
					// Translators: The placeholder will be replaced by the count of sites there use that theme.
					_n( 'Active on %s site', 'Active on %s sites', $active_count, 'multisite-enhancements' ),
					$active_count
				);
				$is_list_hidden = true;
			}
			$output .= '</p>';
			$output .= '<ul id="siteslist_' . $theme_key;
			$output .= ( $is_list_hidden ) ? '">' : '" class="siteslist">';

			foreach ( $active_on_blogs as $key => $value ) {

				// Check the site for archived and deleted.
				$class = '';
				$hint = '';
				if ( $this->is_archived( $key ) ) {
					$class = ' class="site-archived"';
					$hint  = ', ' . esc_attr__( 'Archived' );
				}
				if ( $this->is_deleted( $key ) ) {
					$class = ' class="site-deleted"';
					$hint .= ', ' . esc_attr__( 'Deleted' );
				}

				$output .= '<li' . $class . ' title="Blog ID: ' . $key . $hint . '">';
				$output .= '<span class="non-breaking"><a href="' . get_admin_url( $key ) . 'themes.php">'
					. ( trim( $value['name'] ) ?: $value['path'] ) . '</a>' . $hint . '</span>';
				$output .= '</li>';
			}

			$output .= '</ul>';
			$output .= $child_context;
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
		$blogs_themes_data = $this->get_blogs_themes();

		$active_in_themes = array();

		foreach ( $blogs_themes_data as $blog_id => $data ) {
			if ( $data['stylesheet'] === $theme_key ) {
				$active_in_themes[ $blog_id ] = array(
					'name' => $data['blogname'],
					'path' => $data['blogpath'],
				);
			}
		}

		return $active_in_themes;
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
		} elseif ( false === ( $this->blogs_themes = get_site_transient( self::$site_transient_blogs_themes ) ) ) {

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
				// phpcs:disable
				$blog = (array) $blog;

				$this->blogs_themes[ $blog['blog_id'] ]               = $blog;
				$this->blogs_themes[ $blog['blog_id'] ]['blogpath']   = get_blog_details(
					$blog['blog_id']
				)->path;
				$this->blogs_themes[ $blog['blog_id'] ]['blogname']   = get_blog_details(
					$blog['blog_id']
				)->blogname;
				$this->blogs_themes[ $blog['blog_id'] ]['template']   = get_blog_option(
					$blog['blog_id'],
					'template'
				);
				$this->blogs_themes[ $blog['blog_id'] ]['stylesheet'] = get_blog_option(
					$blog['blog_id'],
					'stylesheet'
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
	 * Run helpers if the debug constant is true to help on development, debugging.
	 *
	 * @since  2016-10-23
	 * @return bool
	 */
	public function development_helper() {
		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			return false;
		}

		add_action( 'network_admin_notices', array( $this, 'notice_about_clear_cache' ) );
		$this->clear_themes_site_transient();

		return true;
	}

	/**
	 * Clears the $blogs_themes site transient when any themes are activated/deactivated.
	 *
	 * @since 2015-02-21
	 */
	public function clear_themes_site_transient() {
		delete_site_transient( self::$site_transient_blogs_themes );
	}

	/**
	 * Check, the current theme have a parent value and is a child theme.
	 *
	 * @param array|WP_Theme $theme_data An array of theme data.
	 *
	 * @return bool
	 */
	public function is_child( WP_Theme $theme_data ) {
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
		$blogs_themes_data = $this->get_blogs_themes();
		$parent_of    = array();

		/**
		 * Provide the data to the Theme of each site.
		 *
		 * @var array $data
		 */
		foreach ( $blogs_themes_data as $blog_id => $data ) {
			$template = false;
			if ( array_key_exists( 'template', $data ) ) {
				$template = $data['template'];
			}

			if ( $template === $theme_key && $template !== $data['stylesheet'] ) {
				$theme       = wp_get_theme( $data['stylesheet'] );
				$parent_of[] = $theme->get( 'Name' );
			}
		}

		return array_unique( $parent_of );
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
	 * Check, if the status of the site deleted.
	 *
	 * @param integer $site_id ID of the site.
	 *
	 * @return bool
	 */
	public function is_deleted( $site_id ) {
		$site_id = (int) $site_id;

		return (bool) get_blog_details( $site_id )->deleted;
	}

} // end class
