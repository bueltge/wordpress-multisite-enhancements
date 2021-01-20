<?php
/**
 * On the network plugin and theme pages, add css to present the active column
 * If this class is loaded, modify the presentation of the column in order to
 * allow showing or hiding the list of sites that uses a theme or plugin
 *
 * Kudos to #n-goncalves for this solution.
 *
 * @see https://github.com/bueltge/wordpress-multisite-enhancements/pull/44
 *
 * @since   2018-02-15
 * @version 2021-11-20
 * @package multisite-enhancements
 */

add_action( 'init', array( 'Add_Css', 'init' ) );

/**
 * On the network plugin and theme pages, add css to present the active column
 * If this class is loaded, modify the presentation of the column in order to
 * allow showing or hiding the list of sites that uses a theme or plugin
 *
 * Class Add_Css
 */
class Add_Css {

	/**
	 * Init function to register all used hooks.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_style' ) );
	}

	/**
	 * Initialize the class.
	 */
	public static function init() {
		$class = __CLASS__;
		if ( empty( $GLOBALS[ $class ] ) ) {
			// phpcs:disable
			$GLOBALS[ $class ] = new $class();
			// phpcs:enable
		}
	}

	/**
	 * Enqueue column style.
	 *
	 * @param string $hook Hook name.
	 */
	public function enqueue_style( $hook ) {
		if ( is_admin() && ! in_array( $hook, array( 'themes.php', 'plugins.php' ), true ) ) {
			return;
		}
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_style(
			'admin_column_css',
			plugins_url( '/assets/css/wordpress-multisite-enhancements' . $suffix . '.css', MULTISITE_ENHANCEMENT_BASE ),
			false,
			'2021-11-20'
		);
		wp_enqueue_style( 'admin_column_css' );
	}

} // end class
