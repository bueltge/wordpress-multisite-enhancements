<?php
/**
 * Uninstallation script
 * This file is called automatically by WP when the admin deletes the plugin from the network.
 *
 * @package WordPress
 */

! defined( 'WP_UNINSTALL_PLUGIN' ) && exit;

require_once __DIR__ . '/vendor/autoload.php';
delete_site_option( Multisite_Enhancements\Settings::OPTION_NAME );
