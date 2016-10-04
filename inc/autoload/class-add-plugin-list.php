<?php
/**
 * On the network plugins page, show which blogs have this plugin active.
 *
 * @since   2013-07-19
 * @version 2016-01-15
 * @package WordPress
 */

add_action( 'init', array( 'Multisite_Add_Plugin_List', 'init' ) );

class Multisite_Add_Plugin_List {

	/**
	 * Value to get sites in the Network.
	 *
	 * @since 2015-02-26
	 * @var int
	 */
	private $sites_limit = 9999;

	/**
	 * On this plugin status will not show the not or activated status in the table of plugins.
	 *
	 * @since  01/03/2014
	 * @var    array
	 */
	static protected $excluded_plugin_status = array( 'dropins', 'mustuse' );

	/**
	 * Member variable to store data about active plugins for each blog.
	 *
	 * @since   2015-02-21
	 * @var     array
	 */
	private $blogs_plugins;

	/**
	 * String for the transient string, there save the blog plugins.
	 *
	 * @since  2015-02-21
	 * @var    string
	 */
	static protected $site_transient_blogs_plugins = 'blogs_plugins';

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
	 * @since   0.0.1
	 */
	public function __construct() {

		// Fires after a plugin has been activated; but not on silently activated, like update.
		add_action( 'activated_plugin', array( $this, 'clear_plugins_site_transient' ), 10, 2 );
		// Fires before a plugin is deactivate; but not on silently activated, like update.
		add_action( 'deactivated_plugin', array( $this, 'clear_plugins_site_transient' ), 10, 2 );

		if ( ! is_network_admin() ) {
			return;
		}

		/**
		 * Filter to change the value for get sites inside the network.
		 *
		 * @type integer
		 */
		$this->sites_limit = (int) apply_filters( 'multisite_enhancements_sites_limit', $this->sites_limit );

		add_filter( 'manage_plugins-network_columns', array( $this, 'add_plugins_column' ), 10, 1 );
		add_action( 'manage_plugins_custom_column', array( $this, 'manage_plugins_custom_column' ), 10, 3 );
	}

	/**
	 * Add in a column header.
	 *
	 * @since  0.0.1
	 *
	 * @param  array $columns An array of displayed site columns.
	 *
	 * @return array
	 */
	public function add_plugins_column( $columns ) {

		// If not set, then no changes on output.
		if ( ! array_key_exists( 'plugin_status', $_GET ) ) {
			$_GET[ 'plugin_status' ] = '';
		}

		// Not useful on different selections.
		if ( ! in_array( esc_attr( $_GET[ 'plugin_status' ] ), self::$excluded_plugin_status, FALSE ) ) {
			// Translators: Active in is the head of the table column on plugin list.
			$columns[ 'active_blogs' ] = _x( '<nobr>Active in </nobr>', 'column name' );
		}

		return $columns;
	}

	/**
	 * Get data for each row on each plugin.
	 *
	 * @since   0.0.1
	 *
	 * @param  String $column_name Name of the column.
	 * @param  String $plugin_file Path to the plugin file.
	 * @param  array  $plugin_data An array of plugin data.
	 *
	 * @return String
	 */
	public function manage_plugins_custom_column( $column_name, $plugin_file, $plugin_data ) {

		if ( 'active_blogs' !== $column_name ) {
			return NULL;
		}

		// Is this plugin network activated.
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}

		$active_on_network = is_plugin_active_for_network( $plugin_file );

		$output = '';

		if ( $active_on_network ) {
			// We don't need to check any further for network active plugins.
			// Translators: The plugin is network wide active, the string is for each plugin possible.
			$output .= __( '<nobr>Network Activated</nobr>', 'multisite_enhancements' );

			// List Blogs, there is activated.
		} else {
			// Is this plugin active on any blogs in this network.
			$active_on_blogs = $this->is_plugin_active_on_blogs( $plugin_file );

			if ( ! $active_on_blogs ) {
				// Translators: The plugin is not activated, the string is for each plugin possible.
				$output .= __( '<nobr>Not Activated</nobr>', 'multisite_enhancements' );
			} else {
				$output .= '<ul>';

				foreach ( $active_on_blogs as $key => $value ) {
					$output .= '<li title="Blog ID: ' . $key . '"><nobr><a href="' . get_admin_url(
							$key
						) . 'plugins.php">' . $value[ 'name' ] . '</a></nobr></li>';
				}

				$output .= '</ul>';
			}
		}

		// Add indicator that the plugin is "Network Only".
		if ( $plugin_data[ 'Network' ] ) {
			$output .= '<br /><nobr class="submitbox"><span class="submitdelete">'
				. __( ' Network Only', 'multisite_enhancements' )
				. '</span></nobr>';
		}

		echo $output;
	}

	/**
	 * Is plugin active in blogs.
	 *
	 * @since    0.0.1
	 *
	 * @param    string $plugin_file An name of the plugin file.
	 *
	 * @internal param  $String
	 *
	 * @return array $active_in_plugins Which Blog ID and Name of Blog for each item in Array.
	 */
	public function is_plugin_active_on_blogs( $plugin_file ) {

		$blogs_plugins = $this->get_blogs_plugins();

		$active_in_plugins = array();

		foreach ( $blogs_plugins as $blog_id => $data ) {
			if ( in_array( $plugin_file, $data[ 'active_plugins' ], FALSE ) ) {
				$active_in_plugins[ $blog_id ] = array(
					'name' => $data[ 'blogname' ],
					'path' => $data[ 'blogpath' ],
				);
			}
		}

		return $active_in_plugins;
	}

	/**
	 * Gets an array of blog data including active plugins for each blog.
	 *
	 * @since  21/02/2015
	 *
	 * @return array
	 */
	public function get_blogs_plugins() {

		// See if the data is present in the variable first.
		if ( $this->blogs_plugins ) {
			return $this->blogs_plugins;

			// If not, see if we can load data from the transient.
		} else if ( FALSE === ( $this->blogs_plugins = get_site_transient( self::$site_transient_blogs_plugins ) ) ) {

			// Cannot load data from transient, so load from DB and set transient.
			$this->blogs_plugins = array();

			if ( function_exists( 'wp_get_sites' ) ) {
				// Since 3.7 inside the Core.
				$blogs = wp_get_sites(
					array(
						'limit' => $this->sites_limit,
					)
				);
			} else {
				// Use alternative to core function get_blog_list().
				$blogs = Multisite_Core::get_blog_list( 0, 'all' );
			}

			/** @var array $blog */
			foreach ( (array) $blogs as $blog ) {
				$this->blogs_plugins[ $blog[ 'blog_id' ] ]                     = $blog;
				$this->blogs_plugins[ $blog[ 'blog_id' ] ][ 'blogpath' ]       = get_blog_details(
					$blog[ 'blog_id' ]
				)->path;
				$this->blogs_plugins[ $blog[ 'blog_id' ] ][ 'blogname' ]       = get_blog_details(
					$blog[ 'blog_id' ]
				)->blogname;
				$this->blogs_plugins[ $blog[ 'blog_id' ] ][ 'active_plugins' ] = array();
				$plugins                                                       = get_blog_option(
					$blog[ 'blog_id' ], 'active_plugins'
				);
				if ( $plugins ) {
					foreach ( $plugins as $plugin_file ) {
						$this->blogs_plugins[ $blog[ 'blog_id' ] ][ 'active_plugins' ][] = $plugin_file;
					}
				}
			}

			set_site_transient( self::$site_transient_blogs_plugins, $this->blogs_plugins );
		}

		// Data should be here, if loaded from transient or DB.
		return $this->blogs_plugins;
	}

	/**
	 * Clears the $blogs_plugins site transient when any plugins are activated/deactivated.
	 *
	 * @since  2015-02-25
	 */
	public function clear_plugins_site_transient() {

		delete_site_transient( self::$site_transient_blogs_plugins );
	}

} // end class
