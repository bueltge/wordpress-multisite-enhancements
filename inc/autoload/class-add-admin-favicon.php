<?php
/**
 * Add Favicon from theme folder to the admin area to easier identify the blog.
 *
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
 * @since   2015-07-23
 * @version 2016-10-05
 * @package WordPress
 */

add_action( 'init', array( 'Multisite_Add_Admin_Favicon', 'init' ) );

/**
 * Add Favicon from theme folder to the admin area to easier identify the blog.
 *
 * Class Multisite_Add_Admin_Favicon
 */
class Multisite_Add_Admin_Favicon {

	/**
	 * Value to get sites in the Network.
	 *
	 * @since 2015-02-26
	 * @var int
	 */
	private $sites_limit = 9999;

	/**
	 * Define Hooks for add the favicon markup.
	 *
	 * @since 0.0.2
	 * @var   array
	 */
	static protected $favicon_hooks = array(
		'admin_head',
		'wp_head',
	);

	/**
	 * Filter to remove "W" logo incl. sublinks from admin bar.
	 *
	 * @since 0.0.2
	 * @var   Boolean
	 */
	static protected $remove_wp_admin_bar = TRUE;

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
	 * Use the filter hook to add hooks, there will add the markup
	 *     Hook: multisite_enhancements_favicon
	 *
	 * @since   0.0.2
	 */
	public function __construct() {

		/**
		 * Filter hook to change the value for get sites inside the network.
		 *
		 * @type integer
		 */
		$this->sites_limit = (int) apply_filters( 'multisite_enhancements_sites_limit', $this->sites_limit );

		/**
		 * Hooks for add favicon markup.
		 *
		 * @type array
		 */
		$hooks = (array) apply_filters( 'multisite_enhancements_favicon', self::$favicon_hooks );

		foreach ( $hooks as $hook ) {
			add_action( esc_attr( $hook ), array( $this, 'set_favicon' ) );

			// Add favicon from theme folder to each blog.
			add_action( esc_attr( $hook ), array( $this, 'set_admin_bar_blog_icon' ) );
		}

		// Remove admin bar item with "W" logo.
		add_action( 'admin_bar_menu', array( $this, 'change_admin_bar_menu' ), 25 );
	}

	/**
	 * Create markup, if favicon is exist in active theme folder.
	 *
	 * Use the filter hook to change style
	 *     Hook: multisite_enhancements_add_favicon
	 *
	 * @since   0.0.2
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

		/**
		 * Use the filter hook to change style.
		 *
		 * @type string
		 */
		echo apply_filters( 'multisite_enhancements_add_favicon', $output );
	}

	/**
	 * Add Favicon from each blog to Multisite Menu of "My Sites".
	 *
	 * Use the filter hook to change style
	 *     Hook: multisite_enhancements_add_admin_bar_favicon
	 *
	 * @since   0.0.2
	 */
	public function set_admin_bar_blog_icon() {

		// Only usable if the user is logged in and use the admin bar.
		if ( ! is_user_logged_in() || ! is_admin_bar_showing() ) {
			return;
		}

		$user_id    = get_current_user_id();
		$user_blogs = get_blogs_of_user( $user_id );

		$output = '';
		foreach ( (array) $user_blogs as $blog ) {

			$custom_icon = FALSE;

			// Validate, that we use nly int value.
			$blog_id    = (int) $blog->userblog_id;
			$stylesheet = get_blog_option( $blog_id, 'stylesheet' );

			// Get stylesheet directory uri.
			$theme_root_uri     = get_theme_root_uri( $stylesheet );
			$stylesheet_dir_uri = "$theme_root_uri/$stylesheet";

			// Get stylesheet directory.
			$theme_root     = get_theme_root( $stylesheet );
			$stylesheet_dir = "$theme_root/$stylesheet";

			// Create favicon directory and directory url locations.
			$favicon_dir_uri = $this->get_favicon_path( $blog_id, $stylesheet_dir_uri, 'url' );
			$favicon_dir     = $this->get_favicon_path( $blog_id, $stylesheet_dir, 'dir' );

			// Check if the user has manually added a site icon in WP (since WP 4.3).
			$site_icon_id = (int) get_blog_option( $blog_id, 'site_icon' );
			if ( 0 !== $site_icon_id ) {
				switch_to_blog( $blog_id );
				$url_data = wp_get_attachment_image_src( $site_icon_id, array( 32, 32 ) );
				$custom_icon = esc_url( $url_data[0] );
				restore_current_blog();
			} elseif ( file_exists( $favicon_dir ) ) {
				$custom_icon = $favicon_dir_uri;
			}

			if ( FALSE !== $custom_icon ) {
				$output .= '#wpadminbar .quicklinks li#wp-admin-bar-blog-' . $blog_id
					. ' .blavatar { font-size: 0 !important; }';
				$output .= '#wp-admin-bar-blog-' . $blog_id
					. ' div.blavatar { background: url( "' . $custom_icon
					. '" ) left bottom/16px no-repeat !important; background-size: 16px !important; margin: 0 2px 0 -2px; }' . "\n";
			}
		}

		if ( '' !== $output ) {
			/**
			 * Use the filter hook to change style.
			 *
			 * @type string
			 */
			echo apply_filters(
				'multisite_enhancements_add_admin_bar_favicon',
				"\n" . '<style>' . $output . '</style>' . "\n"
			);
		}
	}

	/**
	 * Maybe removes the "W" logo incl. sublinks from the admin menu.
	 *
	 * Use the filter hook to change the default to remove the "W" logo and his sublinks
	 *     Hook: multisite_enhancements_remove_wp_admin_bar
	 *
	 * @since   0.0.2
	 * @param   WP_Admin_Bar $admin_bar WP_Admin_Bar instance, passed by reference.
	 */
	public function change_admin_bar_menu( $admin_bar ) {

		/**
		 * Use the filter hook to remove or not remove the first part in the admin bar.
		 *
		 * @type bool
		 */
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
	 * @since    1.0.5
	 *
	 * @param int    $blog_id   Id of the blog in the network.
	 * @param string $path      Path to Favicon.
	 * @param string $path_type Type 'url' or 'dir'.
	 *
	 * @return string File path to favicon file.
	 * @internal param ID $integer of blog in network
	 * @internal param Path $string to Favicon
	 * @internal param Path $string type 'url' or 'dir'
	 */
	protected function get_favicon_path( $blog_id = 0, $path = '', $path_type = 'url' ) {

		if ( 0 === $blog_id ) {
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
		 * @param string $path Path to favicon file.
		 *
		 * Optional parameters:
		 *
		 * When using a different directory than the stylesheet use the $blog_id and $path_type
		 * integer $blog_id
		 *
		 * string $path_type = 'url' -> use URL for the location as a URL
		 * string $path_type = 'dir' -> use URL for the location in the server, used to check if the file exists
		 */

		return apply_filters( 'multisite_enhancements_favicon_path', $path . '/favicon.ico', $blog_id, $path_type );
	}

} // end class
