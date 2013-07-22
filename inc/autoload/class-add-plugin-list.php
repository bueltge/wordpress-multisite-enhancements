<?php
/**
 * On the network plugins page, show which blogs have this plugin active
 * 
 * @since   07/19/2013
 */

add_action( 'init', array( 'Multisite_Add_Plugin_List', 'init' ) );
class Multisite_Add_Plugin_List {
	
	public static function init() {
		
		$class = __CLASS__ ;
		if ( empty( $GLOBALS[ $class ] ) )
			$GLOBALS[ $class ] = new $class;
	}
	
	/**
	 * Init function to register all used hooks
	 * 
	 * @since   0.0.1
	 * @return  void
	 */
	public function __construct() {
		
		add_filter( 'manage_plugins-network_columns', array( $this, 'add_plugins_column' ), 10, 1);
		add_action( 'manage_plugins_custom_column', array( $this, 'manage_plugins_custom_column' ), 10, 3);
	}
	
	/**
	 * Add in a column header
	 * 
	 * @since   0.0.1
	 * @param   Array
	 * @return  String
	 */
	public function add_plugins_column( $columns ) {
		
		$columns['active_blogs'] = _x( '<nobr>Active in </nobr>', 'column name' );
		
		return $columns;
	}
	
	/**
	 * Get data for each row on each plugin
	 * 
	 * @param  String
	 * @param  String
	 * @param  Array
	 */
	public function manage_plugins_custom_column( $column_name, $plugin_file, $plugin_data ) {
		
		if ( $column_name !== 'active_blogs' )
			return NULL;
		
		// Is this plugin network activated
		if ( ! function_exists( 'is_plugin_active_for_network' ) )
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		
		$active_on_network = is_plugin_active_for_network( $plugin_file );
		
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
				
				foreach( $active_on_blogs as $key => $value )
					$output .= '<li title="Blog ID: ' . $key . '"><nobr><a href="' . get_admin_url( $key ) . 'plugins.php' . '">' . $value['name'] . '</a></nobr></li>';
				
				$output .= '</ul>';
			}
		}
		
		echo $output;
	}
	
	/**
	 * Is plugin active in blogs
	 * 
	 * @since   0.0.1
	 * @param   String
	 * @return  Array which Blog ID and Name of Blog for each item in Array
	 */
	public function is_plugin_active_on_blogs( $plugin_file ) {
		
		$blogs = get_blog_list( 0, 'all' );
		
		$active_in_plugins = array();
		foreach( (array) $blogs as $blog ) {
			
			$active_plugins = get_blog_option( $blog['blog_id'], 'active_plugins' );
			
			foreach( $active_plugins as $active_plugin ) {
				if ( $active_plugin === $plugin_file ) {
					$blogname = get_blog_details( $blog['blog_id'] )->blogname;
					$blogpath = get_blog_details( $blog['blog_id'] )->path;
					$active_in_plugins[ $blog['blog_id'] ] = array(
						'name' => $blogname,
						'path' => $blogpath
					);
				}
			}
			
		}
		
		return $active_in_plugins;
	}
	
} // end class
