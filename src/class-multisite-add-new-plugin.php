<?php
/**
 * Enables an 'Add New' link under the Plugins menu for Network admins.
 *
 * @since   2013-07-19
 * @version 2016-01-15
 * @package multisite-enhancements
 */

namespace Multisite_Enhancements;

/**
 * Class Add_New_Plugin
 */
class Multisite_Add_New_Plugin {

	/**
	 * Init the class.
	 */
	public function init() {
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
			// phpcs:disable
			__( 'Add New' ),
			__( 'Add New' ),
			// phpcs:enable
			'manage_network',
			'plugin-install.php'
		);
	}
} // end class
