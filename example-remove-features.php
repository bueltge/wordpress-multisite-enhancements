<?php
/**
 * Plugin Name: Multisite Enhancements Add-on to remove features
 * Description: This plugin remove features from the Multisite Enhancement plugin.
 * Plugin URI:  https://github.com/bueltge/WordPress-Multisite-Enhancements
 * Version:     2017-11-26
 * Author:      Frank Bültge
 * Author URI:  http://bueltge.de
 * License:     GPLv2+
 * License URI: ./assets/LICENSE
 * Text Domain: multisite-enhancements
 * Domain Path: /languages
 * Network:     true
 *
 * @package WordPress
 */

! defined( 'ABSPATH' ) and exit;

add_filter( 'multisite_enhancements_autoload', function ( $files ) {

	// Unset features by unsetting the filename from the autoloader.
	// Uncomment to deactivate.
//	unset( $files[ 'class-add-admin-favicon.php' ] );
//	unset( $files[ 'class-add-blog-id.php' ] );
//	unset( $files[ 'class-add-css.php' ] );
//	unset( $files[ 'class-add-plugin-list.php' ] );
//	unset( $files[ 'class-add-site-status-labels.php' ] );
//	unset( $files[ 'class-add-ssl-identifier.php' ] );
//	unset( $files[ 'class-add-theme-list.php' ] );
//	unset( $files[ 'class-admin-bar-tweaks.php' ] );
//	unset( $files[ 'class-change-footer-text.php' ] );
//	unset( $files[ 'class-core.php' ] );
//	unset( $files[ 'class-filtering-themes.php' ] );
//	unset( $files[ 'class-multisite-add-new-plugins.php' ] );
//	unset( $files[ 'core.php' ] );

	return $files;
} );
