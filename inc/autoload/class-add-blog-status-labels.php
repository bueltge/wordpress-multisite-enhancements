<?php
/**
 * Add status labels to blogs.
 *
 * @since   2015-07-14
 * @version 2015-07-15
 * @package WordPress
 */

add_action( 'init', array( 'Multisite_Add_Blog_Status_labels', 'init' ) );

/**
 * Add status labels to blogs.
 *
 * Class Multisite_Add_Blog_Status_labels
 */
class Multisite_Add_Blog_Status_labels {

	/**
	 * Store for color scheme of the user.
	 *
	 * @var bool
	 */
	private $admin_color_scheme = FALSE;

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
	 * @since  2015-07-14
	 */
	public function __construct() {

		add_action( 'admin_bar_menu', array( $this, 'print_admin_bar_blog_status_labels' ) );
	}

	/**
	 * Add status label from each blog to Multisite Menu of "My Sites".
	 *
	 * Use the filter hook to change style
	 *     Hook: multisite_enhancements_add_admin_bar_favicon
	 *
	 * @since   2015-07-14
	 *
	 * @param WP_Admin_Bar $admin_bar WP_Admin_Bar instance, passed by reference.
	 *
	 * @return void
	 */
	public function print_admin_bar_blog_status_labels( $admin_bar ) {

		if ( current_user_can( 'manage_network' ) ) {

			global $_wp_admin_css_colors;
			$this->admin_color_scheme = $_wp_admin_css_colors[ get_user_option( 'admin_color' ) ];

			foreach ( $admin_bar->user->blogs as $key => $blog ) {

				$prefix = '';

				$label  = 'ext-domain';
				$color  = 'inherit';

				if ( $this->admin_color_scheme->colors[ 3 ] ) {
					$color = $this->admin_color_scheme->colors[ 3 ];
				}

				if ( strpos(
						$blog->siteurl,
						str_replace( array( 'http://', 'https://', '//' ), '', $admin_bar->user->domain )
					) === FALSE
				) {
					$prefix .= '<span style="font-style: italic; font-weight: bold; line-height: 1; color: ' . $color . ';">[' . $label . ']</span> ';
				}

				$label = 'noindex';
				$color = 'inherit';
				if ( $this->admin_color_scheme->colors[ 2 ] ) {
					$color = $this->admin_color_scheme->colors[ 2 ];
				}

				$is_live = (int) get_blog_option( $blog->userblog_id, 'blog_public' );
				if ( 1 !== $is_live ) {
					$prefix .= '<span style="font-style: italic; font-weight: bold; line-height: 1; color: ' . $color . ';">[' . $label . ']</span> ';
				}

				$blog->blogname                           = $prefix . $blog->blogname;
				$admin_bar->user->blogs[ $key ]->blogname = $blog->blogname;
			}
		}

	}

} // end class
