<?php
/**
 * On the network plugins page, show which blogs have this plugin active.
 *
 * @since   2013-07-19
 * @version 2017-02-22
 * @package WordPress
 */

add_action( 'init', array( 'Multisite_Add_Plugin_List', 'init' ) );

/**
 * Class Multisite_Add_Plugin_List
 */
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
	 * @since   0.0.1
	 */
	public function __construct() {

		add_action( 'load-plugins.php', array( $this, 'development_helper' ) );

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
		$this->clear_plugins_site_transient();

		return TRUE;
	}

	/**
	 * Print Network Admin Notices to inform, that the transient are deleted.
	 *
	 * @since 2016-10-23
	 */
	public function notice_about_clear_cache() {

		$class   = 'notice notice-info';
		$message = esc_attr__( 'Multisite Enhancements: Delete site transients for the plugin usage to help on development, debugging. The constant WP_DEBUG is true.',
		                       'multisite-enhancements' );
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
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
			$columns[ 'active_blogs' ] = _x( '<nobr>Active in </nobr>', 'column name', 'multisite-enhancements' );
		}

		return $columns;
	}

	/**
	 * Get data for each row on each plugin.
	 * Echo the string.
	 *
	 * @since   0.0.1
	 *
	 * @param  String $column_name Name of the column.
	 * @param  String $plugin_file Path to the plugin file.
	 * @param  array  $plugin_data An array of plugin data.
	 *
	 * @return void
	 */
	public function manage_plugins_custom_column( $column_name, $plugin_file, $plugin_data ) {

		if ( 'active_blogs' !== $column_name ) {
			return NULL;
		}

		// Is this plugin network activated.
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		$active_on_network = is_plugin_active_for_network( $plugin_file );

		$output = '';

		if ( $active_on_network ) {
			// We don't need to check any further for network active plugins.
			// Translators: The plugin is network wide active, the string is for each plugin possible.
			$output .= __( '<nobr>Network Activated</nobr>', 'multisite-enhancements' );

			// List Blogs, there is activated.
		} else {
			// Is this plugin active on any blogs in this network.
			$active_on_blogs = $this->is_plugin_active_on_blogs( $plugin_file );

			if ( ! $active_on_blogs ) {
				// Translators: The plugin is not activated, the string is for each plugin possible.
				$output .= __( '<nobr>Not Activated</nobr>', 'multisite-enhancements' );
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
					$output .= '<nobr><a href="' . get_admin_url( $key ) . 'plugins.php">'
					           . $value[ 'name' ] . '</a>' . $hint . '</nobr></li>';
				}

				$output .= '</ul>';
			}
		}

		// Add indicator that the plugin is "Network Only".
		if ( $plugin_data[ 'Network' ] ) {
			$output .= '<br /><nobr class="submitbox"><span class="submitdelete">'
			           . esc_attr__( 'Network Only', 'multisite-enhancements' )
			           . '</span></nobr>';
		}

		echo wp_kses( $output, self::$wp_kses_allowed_html );
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
			if ( in_array( $plugin_file, $data[ 'active_plugins' ], TRUE ) ) {
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
		} elseif ( FALSE === ( $this->blogs_plugins = get_site_transient( self::$site_transient_blogs_plugins ) ) ) {

			// Cannot load data from transient, so load from DB and set transient.
			$this->blogs_plugins = array();

			$blogs = (array) Multisite_Core::get_blog_list( 0, $this->sites_limit );

			/**
			 * Data to each site of the network, blogs.
			 *
			 * @var array $blog
			 */
			foreach ( $blogs as $blog ) {

				// Convert object to array.
				$blog = (array) $blog;

				$this->blogs_plugins[ $blog[ 'blog_id' ] ]                     = $blog;
				$this->blogs_plugins[ $blog[ 'blog_id' ] ][ 'blogpath' ]       = get_blog_details(
					$blog[ 'blog_id' ]
				)->path;
				$this->blogs_plugins[ $blog[ 'blog_id' ] ][ 'blogname' ]       = get_blog_details(
					$blog[ 'blog_id' ]
				)->blogname;
				$this->blogs_plugins[ $blog[ 'blog_id' ] ][ 'active_plugins' ] = array();
				$plugins                                                       = (array) get_blog_option(
					$blog[ 'blog_id' ], 'active_plugins'
				);
				if ( $plugins ) {
					foreach ( $plugins as $plugin_file ) {
						$this->blogs_plugins[ $blog[ 'blog_id' ] ][ 'active_plugins' ][] = $plugin_file;
					}
				}
			}

			if ( ! $this->development_helper() ) {
				set_site_transient( self::$site_transient_blogs_plugins, $this->blogs_plugins );
			}
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
