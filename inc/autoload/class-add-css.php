<?php
/**
 * On the network plugin and theme pages, add css to present the active column
 * If this class is loaded, modify the presentation of the column in order to
 * allow showing or hiding the list of sites that uses a theme or plugin
 *
 * Kudos to #n-goncalves for this solution.
 * @see https://github.com/bueltge/wordpress-multisite-enhancements/pull/44
 *
 * @since   2018-02-15
 * @version 2018-02-15
 * @package WordPress
 */

add_action( 'init', array( 'Enqueue_Column_Style', 'init' ) );

/**
 * On the network plugin and theme pages, add css to present the active column
 * If this class is loaded, modify the presentation of the column in order to
 * allow showing or hiding the list of sites that uses a theme or plugin
 *
 * Class Enqueue_Column_Style
 */
class Enqueue_Column_Style {

	/**
	 * Init function to register all used hooks.
	 *
	 */
	public function __construct() {

		add_action( 'admin_head-themes.php', array( $this, 'enqueue_style' ) );
		add_action( 'admin_head-plugins.php', array( $this, 'enqueue_style' ) );
	}

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
	 * Enqueue column style.
	 *
	 */
	public function enqueue_style() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_style(
			'admin_column_css',
			plugins_url( '/inc/assets/css/wordpress-multisite-enhancements' . $suffix . '.css', MULTISITE_ENHANCEMENT_BASE ),
			false );
		wp_enqueue_style( 'admin_column_css' );
	} // end enqueue_style()

} // end class
