<?php
/**
 * Uninstallation script
 * This file is called automatically by WP when the admin deletes the plugin from the network
 */

// if not called by WordPress, die
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

class Multisite_Enhancements_Uninstall {

	public function __construct() {
		self::uninstall();
	}

	/**
	 * Delete database option when plugin is uninstalled
	 */
	public static function uninstall() {

		require_once __DIR__ . '/settings.php';

		if ( Multisite_Enhancements_Settings::is_feature_enabled( 'delete-settings' ) ) {
			delete_site_option( Multisite_Enhancements_Settings::OPTION_NAME );
		}
	}

}

new Multisite_Enhancements_Uninstall();
