<?php
/**
 * On the network plugins page, show which blogs have this plugin active
 *
 * @since    07/19/2013
 * @version  05/15/2014
 */

add_action( 'init', array( 'Multisite_Add_Plugin_List', 'init' ) );

class Multisite_Add_Plugin_List {

	/**
	 * On this plugin status will not show the not or activated status in the table of plugins
	 *
	 * @since  01/03/2014
	 * @var    Array
	 */
	static protected $excluded_plugin_status = array( 'dropins', 'mustuse' );

	public static function init() {

		$class = __CLASS__;
		if ( empty( $GLOBALS[ $class ] ) ) {
			$GLOBALS[ $class ] = new $class;
		}
	}

	/**
	 * Init function to register all used hooks
	 *
	 * @since   0.0.1
	 * @return \Multisite_Add_Plugin_List
	 */
	public function __construct() {

		if ( ! is_network_admin() ) {
			return NULL;
		}

		add_filter( 'manage_plugins-network_columns', array( $this, 'add_plugins_column' ), 10, 1 );
		add_action( 'manage_plugins_custom_column', array( $this, 'manage_plugins_custom_column' ), 10, 3 );
	}

	/**
	 * Add in a column header
	 *
	 * @since   0.0.1
	 *
	 * @param   Array
	 *
	 * @return  String
	 */
	public function add_plugins_column( $columns ) {

		// Not useful on different selections
		if ( ! in_array( esc_attr( $_GET[ 'plugin_status' ] ), self::$excluded_plugin_status ) ) {
			$columns[ 'active_blogs' ] = _x( '<nobr>Active in </nobr>', 'column name' );
		}

		return $columns;
	}

	/**
	 * Get data for each row on each plugin
	 *
	 * @since   0.0.1
	 *
	 * @param   String
	 * @param   String
	 * @param   Array
	 *
	 * @return  String
	 */
	public function manage_plugins_custom_column( $column_name, $plugin_file, $plugin_data ) {

		if ( 'active_blogs' !== $column_name ) {
			return NULL;
		}

		// Is this plugin network activated
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}

		$active_on_network = is_plugin_active_for_network( $plugin_file, $plugin_data );

		$output = '';

		if ( $active_on_network ) {
			// We don't need to check any further for network active plugins
			$output .= __( '<nobr>Network Activated</nobr>', 'multisite_enhancements' );

			// list Blogs, there is activated
		} else {
			// Is this plugin active on any blogs in this network
			$active_on_blogs = $this->is_plugin_active_on_blogs( $plugin_file );

			if ( empty( $active_on_blogs ) ) {
				$output .= __( '<nobr>Not Activated</nobr>', 'multisite_enhancements' );
			} else {
				$output .= '<ul>';

				foreach ( $active_on_blogs as $key => $value ) {
					$output .= '<li title="Blog ID: ' . $key . '"><nobr><a href="' . get_admin_url( $key ) . 'plugins.php' . '">' . $value[ 'name' ] . '</a></nobr></li>';
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
	 * Is plugin active in blogs
	 *
	 * @since    0.0.1
	 *
	 * @param      $plugin_file
	 * @param null $plugin_data
	 *
	 * @internal param $String
	 *
	 * @return  Array which Blog ID and Name of Blog for each item in Array
	 */
	public function is_plugin_active_on_blogs( $plugin_file, $plugin_data = NULL ) {

		if ( function_exists( 'wp_get_sites' ) ) {
			// Since 3.7 inside the Core
			$blogs = wp_get_sites();
		} else {
			// use alternative to core function get_blog_list()
			$blogs = Multisite_Core::get_blog_list( 0, 'all' );
		}

		$active_in_plugins = array();
		foreach ( (array) $blogs as $blog ) {

			$active_plugins = get_blog_option( $blog[ 'blog_id' ], 'active_plugins' );
			if ( empty( $active_plugins ) ) {
				continue;
			}

			foreach ( $active_plugins as $active_plugin ) {
				if ( $active_plugin === $plugin_file ) {
					$blogpath                                = get_blog_details( $blog[ 'blog_id' ] )->path;
					$blogname                                = get_blog_details( $blog[ 'blog_id' ] )->blogname;
					$active_in_plugins[ $blog[ 'blog_id' ] ] = array(
						'name' => $blogname,
						'path' => $blogpath,
					);
				}
			}

		}

		return $active_in_plugins;
	}

} // end class