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

namespace Multisite_Enhancements;

/**
 * On the network plugin and theme pages, add css to present the active column
 * If this class is loaded, modify the presentation of the column in order to
 * allow showing or hiding the list of sites that uses a theme or plugin
 *
 * Class Add_Css
 */
class Add_Css {


	/**
	 * Initialize the class.
	 */
	public function init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_style' ) );
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
