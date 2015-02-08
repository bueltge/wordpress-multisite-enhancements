<?php

/**
 * Class Multisite_Jetpack_Support
 * Support features from Jetpack for a feature from this plugin
 *
 * @since    2014-12-12
 * @version  2014-12-12
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
	}

	/**
	 * @return null
	 */
	protected function get_jetpack_icon() {

		var_dump( get_option( 'jetpack_active_modules' ) );
		// If the jetback plugin active
		if ( ! class_exists( 'Jetpack' ) ) {
			return NULL;
		}

		// If the icon module active
		if ( ! Jetpack::is_module_active( 'Jetpack_Site_Icon' ) ) {
			return NULL;
		}

		// If set a icon via Jetpack module icon
		if ( ! jetpack_has_site_icon() ) {
			return NULL;
		}

		// get the icon, size 32px
		$url_32 = jetpack_site_icon_url( NULL, 32 );
	}
}