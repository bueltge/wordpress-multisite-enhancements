<?php
/**
 * Add Favicon from theme folder to the admin area to easier identify the blog
 * Remove also the "W" logo and his sublinks in admin bar
 * Add Favicon to each blog on the Admin Bar Item "My Sites"
 *
 * Use the follow filter hooks for different changes
 *     Use the filter hook to add hooks, there will add the markup
 *     - Hook: multisite_enhancements_favicon
 *     - Default is: admin_head
 *     Use the filter hook to change style
 *     - Hook: multisite_enhancements_add_favicon
 *     Use the filter hook to change style
 *     - Hook: multisite_enhancements_add_admin_bar_favicon
 *     Use the filter hook to change the default to remove the "W" logo and his sublinks
 *     - Hook: multisite_enhancements_remove_wp_admin_bar
 *     - Default is: TRUE
 *
 * @since    07/23/2013
 * @version  02/03/2013
 */

add_action( 'init', array( 'Multisite_Add_Admin_Favicon', 'init' ) );

class Multisite_Add_Admin_Favicon {

	/**
	 * Define Hooks for add the favicon markup
	 *
	 * @since   0.0.2
	 * @var     Array
	 */
	static protected $favicon_hooks = array(
		'admin_head',
		'wp_head',
	);

	/**
	 * Filter to remove "W" logo incl. sublinks from admin bar
	 *
	 * @since  0.0.2
	 * @var    Boolean
	 */
	static protected $remove_wp_admin_bar = TRUE;

	public static function init() {

		$class = __CLASS__;
		if ( empty( $GLOBALS[ $class ] ) ) {
			$GLOBALS[ $class ] = new $class;
		}
	}

	/**
	 * Init function to register all used hooks
	 *
	 * Use the filter hook to add hooks, there will add the markup
	 *     Hook: multisite_enhancements_favicon
	 *
	 * @since   0.0.2
	 * @return \Multisite_Add_Admin_Favicon
	 */
	public function __construct() {

		// hooks for add favicon markup
		$hooks = apply_filters( 'multisite_enhancements_favicon', self::$favicon_hooks );

		foreach ( $hooks as $hook ) {
			add_action( esc_attr( $hook ), array( $this, 'set_favicon' ) );

			// add favicon from theme folder to each blog
			add_action( esc_attr( $hook ), array( $this, 'set_admin_bar_blog_icon' ) );
		}

		// remove admin bar item with "W" logo
		add_action( 'admin_bar_menu', array( $this, 'change_admin_bar_menu' ), 25 );
	}

	/**
	 * Create markup, if favicon is exist in active theme folder
	 *
	 * Use the filter hook to change style
	 *     Hook: multisite_enhancements_add_favicon
	 *
	 * @since   0.0.2
	 * @return  String
	 */
	public function set_favicon() {

		$stylesheet_dir_uri = get_stylesheet_directory_uri();
		$stylesheet_dir     = get_stylesheet_directory();
		$output             = '';

		if ( file_exists( $stylesheet_dir . $this->get_favicon_path() ) ) {
			$output .= '<link rel="shortcut icon" type="image/x-icon" href="'
				. esc_url( $stylesheet_dir_uri . $this->get_favicon_path() ) . '" />';
			$output .= '<style>';
			$output .= '#wpadminbar #wp-admin-bar-site-name>.ab-item:before { content: none !important;}';
			$output .= 'li#wp-admin-bar-site-name a { background: url( "'
				. $stylesheet_dir_uri . $this->get_favicon_path()
				. '" ) left center/20px no-repeat !important; padding-left: 21px !important; background-size: 20px !important; } li#wp-admin-bar-site-name { margin-left: 5px !important; } li#wp-admin-bar-site-name {} #wp-admin-bar-site-name div a { background: none !important; }' . "\n";
			$output .= '</style>';
		}

		// Use the filter hook to change style
		echo apply_filters( 'multisite_enhancements_add_favicon', $output );
	}

	/**
	 * Add Favicon from each blog to Multsite Menu of "My Sites"
	 *
	 * Use the filter hook to change style
	 *     Hook: multisite_enhancements_add_admin_bar_favicon
	 *
	 * @since   0.0.2
	 * @return  String
	 */
	public function set_admin_bar_blog_icon() {

		if ( function_exists( 'wp_get_sites' ) ) {
			// Since 3.7 inside the Core
			$blogs = wp_get_sites();
		} else {
			// use alternative to core function get_blog_list()
			$blogs = Multisite_Core::get_blog_list( 0, 'all' );
		}

		$output = '';
		foreach ( (array) $blogs as $blog ) {

			$stylesheet = get_blog_option( $blog[ 'blog_id' ], 'stylesheet' );

			// get stylesheet directory uri
			$theme_root_uri     = get_theme_root_uri( $stylesheet );
			$stylesheet_dir_uri = "$theme_root_uri/$stylesheet";

			// get stylesheet directory
			$theme_root     = get_theme_root( $stylesheet );
			$stylesheet_dir = "$theme_root/$stylesheet";

			// create favicon directory and directory url locations
			$favicon_dir_uri = $this->get_favicon_path( $blog[ 'blog_id' ], $stylesheet_dir_uri, 'url' );
			$favicon_dir     = $this->get_favicon_path( $blog[ 'blog_id' ], $stylesheet_dir, 'dir' );

			if ( file_exists( $favicon_dir ) ) {
				$output .= '#wpadminbar .quicklinks li#wp-admin-bar-blog-' . $blog[ 'blog_id' ]
					. ' .blavatar { font-size: 0 !important; }';
				$output .= '#wp-admin-bar-blog-' . $blog[ 'blog_id' ]
					. ' div.blavatar { background: url( "' . $favicon_dir_uri
					. '" ) left bottom/16px no-repeat !important; background-size: 16px !important; margin: 0 2px 0 -2px; }' . "\n";
			}
		}

		if ( ! empty( $output ) ) {
			// Use the filter hook to change style
			echo apply_filters(
				'multisite_enhancements_add_admin_bar_favicon',
				'<style>' . $output . '</style>' . "\n"
			);
		}
	}

	/**
	 * Maybe removes the "W" logo incl. sublinks from the admin menu
	 *
	 * Use the filter hook to change the default to remove the "W" logo and his sublinks
	 *     Hook: multisite_enhancements_remove_wp_admin_bar
	 *
	 * @since   0.0.2
	 *
	 * @param   Object
	 *
	 * @return  Void
	 */
	public function change_admin_bar_menu( $admin_bar ) {

		// Use the filter hook to remove or not remove the first part in the admin bar
		if ( apply_filters(
			'multisite_enhancements_remove_wp_admin_bar',
			self::$remove_wp_admin_bar
		)
		) {
			$admin_bar->remove_node( 'wp-logo' );
		}
	}

	/**
	 * Get the path to the favicon file from the root of a theme.
	 *
	 * @since 1.0.5
	 *
	 * @param  integer ID of blog in network
	 * @param  string  Path to Favicon
	 * @param  string  Path type 'url' or 'dir'
	 *
	 * @return string File path to favicon file.
	 */
	protected function get_favicon_path( $blog_id = '', $path = '', $path_type = 'url' ) {

		if ( empty( $blog_id ) ) {
			$blog_id = get_current_blog_id();
		}

		/**
		 * Filter the file path to the favicon file.
		 *
		 * Default is '/favicon.ico' which assumes there's a .ico file in the theme root.
		 * This filter allows that path, file name, and file extension to be changed.
		 *
		 * @since 1.0.5
		 *
		 * @param string $favicon_file_path Path to favicon file.
		 *
		 * Optional parameters:
		 *
		 * When using a different directory than the stylesheet use the $blog_id and $path_type
		 *
		 * $path_type = 'url' -> use URL for the location as a URL
		 * $path_type = 'dir' -> use URL for the location in the server, used to check if the file exists
		 *
		 */

		return apply_filters( 'multisite_enhancements_favicon_path', $path . '/favicon.ico', $blog_id, $path_type );
	}

} // end class
