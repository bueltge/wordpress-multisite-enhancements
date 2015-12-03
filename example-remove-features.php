<?php
/**
 * Plugin Name: Multisite Enhancements Add-on to remove features
 * Description: Thhis plugin remove features from the Multisite Enhancement plugin.
 * Plugin URI:  https://github.com/bueltge/WordPress-Multisite-Enhancements
 * Version:     2015-12-03
 * Author:      Frank Bültge
 * Author URI:  http://bueltge.de
 * License:     GPLv2+
 * License URI: ./assets/LICENSE
 * Text Domain: multisite_enhancements
 * Domain Path: /languages
 * Network:     true
 */

! defined( 'ABSPATH' ) and exit;

add_filter( 'multisite_enhancements_autoload', function ( $files ) {

	// Unset the favicon-feature, file class-add-admin-favicon.php.
	unset( $files[ 0 ] );

	/**
	 * Current are this possible.
	 *
	 *  0 => inc/autoload/class-add-admin-favicon.php'
	 *  1 => inc/autoload/class-add-blog-id.php'
	 *  2 => inc/autoload/class-add-plugin-list.php'
	 *  3 => inc/autoload/class-add-site-status-labels.php'
	 *  4 => inc/autoload/class-add-theme-list.php'
	 *  5 => inc/autoload/class-admin-bar-tweaks.php'
	 *  6 => inc/autoload/class-change-footer-text.php'
	 *  7 => inc/autoload/class-core.php'
	 *  8 => inc/autoload/class-filtering-plugins.php'
	 *  9 => inc/autoload/class-multisite-add-new-plugin.php'
	 * 10 => inc/autoload/core.php'
	 */

	return $files;
} );
