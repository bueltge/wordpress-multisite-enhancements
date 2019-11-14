<?php
/**
 * Add simple javascript to filter the theme list on network and single site theme page of WordPress back end.
 *
 * @since   2016-10-05
 * @package WordPress
 */

add_action( 'admin_init', array( 'Filtering_Themes', 'init' ) );

/**
 * Class Filtering_Themes
 */
class Filtering_Themes {

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

		add_action( 'admin_print_scripts-themes.php', array( $this, 'enqueue_script' ) );
	}

	/**
	 * Enqueue scripts.
	 */
	public function enqueue_script() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script(
			'me_filtering_plugins',
			plugins_url( '/inc/assets/js/filtering-themes' . $suffix . '.js', MULTISITE_ENHANCEMENT_BASE ),
			array( 'jquery' ),
			'2016-10-05',
			TRUE
		);
		wp_enqueue_script( 'me_filtering_plugins' );
	}
}
