<?php
/**
 * Uninstallation script
 * This file is called automatically by WP when the admin deletes the plugin from the network.
 */

! defined( 'WP_UNINSTALL_PLUGIN' ) && exit;

require_once __DIR__ . '/src/settings.php';
delete_site_option( Multisite_Enhancements_Settings::OPTION_NAME );
