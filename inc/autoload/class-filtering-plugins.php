<?php
/**
 * Add simple javascript to filter the plugin list on network and single plugin page of WordPress back end.
 *
 * @since   2015-11-29
 * @package WordPress
 */

add_action( 'admin_init', array( 'Filtering_Plugins', 'init' ) );

/**
 * Class Filtering_Plugins
 */
class Filtering_Plugins {

	/**
	 * Init the class.
	 */
	public static function init() {

		$class = __CLASS__;
		if ( empty( $GLOBALS[ $class ] ) ) {
			$GLOBALS[ $class ] = new $class;
		}
	}

	/**
	 * Filtering_Plugins constructor.
	 */
	public function __construct() {

		add_action( 'admin_print_scripts-plugins.php', array( $this, 'enqueue_script' ) );
	}

	/**
	 * Enqueue scripts.
	 */
	public function enqueue_script() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script(
			'me_filtering_plugins',
			plugins_url( '/inc/assets/js/filtering-plugins' . $suffix . '.js', MULTISITE_ENHANCEMENT_BASE ),
			array( 'jquery' ),
			'2015-11-29',
			TRUE
		);
		wp_enqueue_script( 'me_filtering_plugins' );
	}
}
