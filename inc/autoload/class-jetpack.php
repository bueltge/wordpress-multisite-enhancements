<?php

//add_action( 'init', array( 'Multisite_Jetpack_Support', 'init' ) );

/**
 * Class Multisite_Jetpack_Support
 * Support features from Jetpack for a feature from this plugin
 *
 * @since    2014-12-12
 * @version  2015-02-26
 */
class Multisite_Jetpack_Support {

	public static function init() {

		$class = __CLASS__;
		if ( empty( $GLOBALS[ $class ] ) ) {
			$GLOBALS[ $class ] = new $class;
		}
	}

	/**
	 * Init function to register all used hooks
	 *
	 */
	public function __construct() {

		add_filter( 'multisite_enhancements_favicon_path', array( $this, 'set_favicon_path' ), 10, 3 );
	}

	public function set_favicon_path( $default, $blog_id, $path_type ) {

		// Set for network area
		if ( is_network_admin() ) {
			$favicon_path = $this->get_jetpack_icons( $blog_id );
		}

		// Set for single site
		if ( ! is_network_admin() && get_current_blog_id() === $blog_id ) {
			$favicon_path = $this->get_jetpack_icons( $blog_id );
		}

		$path = '';
		if ( ! empty( $favicon_path ) && 'url' === $path_type ) {
			$path = $favicon_path;
		} elseif ( ! empty( $favicon_path ) && 'dir' === $path_type ) {
			$path = WP_CONTENT_DIR . str_replace( WP_CONTENT_URL, '', $favicon_path );
		} else {
			$path = $default;
		}

		return $path;
	}

	protected function get_jetpack_icons( $blog_id ) {

		$path = FALSE;
		if ( is_network_admin() ) {
			switch_to_blog( $blog_id );
			$path = $this->get_jetpack_icon();
			restore_current_blog();
		} else {
			if ( get_current_blog_id() === $blog_id ) {
				$path = $this->get_jetpack_icon();
			}
		}

		return $path;
	}

	protected function get_jetpack_icon() {

		$url_32 = '';

		// Is plugin active
		if ( ! is_plugin_active( 'jetpack/jetpack.php' ) ) {
			return NULL;
		}

		// If the jetback plugin is active
		if ( ! class_exists( 'Jetpack' ) ) {
			require_once( WP_PLUGIN_DIR . '/jetpack/jetpack.php' );
		}

		// If the jetback plugin is active
		if ( ! class_exists( 'Jetpack' ) ) {
			return NULL;
		}

		// If the icon module is active
		if ( ! Jetpack::is_module_active( 'site-icon' ) ) {
			return NULL;
		}

		if ( ! function_exists( 'jetpack_has_site_icon' ) ) {
			require_once( WP_PLUGIN_DIR . '/jetpack/modules/site-icon/site-icon-functions.php' );
		}

		if ( ! function_exists( 'jetpack_has_site_icon' ) ) {
			return NULL;
		}

		// If set a icon via Jetpack module icon
		if ( ! jetpack_has_site_icon() ) {
			return NULL;
		}

		// Get the icon, size 32px
		$url_32 = jetpack_site_icon_url( NULL, 32 );

		return $url_32;
	}
}