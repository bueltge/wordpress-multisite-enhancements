<?php
/**
 * Enables an 'Add New' link under the Plugins menu for Network admins.
 *
 * @since   2013-07-19
 * @version 2016-01-15
 * @package WordPress
 */

add_action( 'init', array( 'Multisite_Add_New_Plugin', 'init' ) );

/**
 * Class Multisite_Add_New_Plugin
 */
class Multisite_Add_New_Plugin {

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
	 * Init function to register all used hooks.
	 *
	 * @since   0.0.1
	 */
	public function __construct() {

		// Only on each blog, not network admin.
		if ( is_network_admin() ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'add_plugins_page' ) );
	}

	/**
	 * Add menu item.
	 *
	 * @since   0.0.1
	 */
	public function add_plugins_page() {

		add_plugins_page(
			__( 'Add New' ),
			__( 'Add New' ),
			'manage_network',
			'plugin-install.php'
		);
	}

} // end class
