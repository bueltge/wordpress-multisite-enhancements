<?php # -*- coding: utf-8 -*-

#namespace Multisite_Enhancements;

// Use default composer autoload
#require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// Activates this plugin in WordPress so it can be tested.
$GLOBALS[ 'wp_tests_options' ] = array(
	'active_plugins' => array( 'wordpress-multisite-enhancements/multisite-enhancements.php' ),
);

// If the wordpress-tests repo location has been customized (and specified
// with WP_TESTS_DIR), use that location. This will most commonly be the case
// when configured for use with Travis CI.
// Otherwise, we'll just assume that this plugin is installed in the WordPress
// SVN external checkout configured in the wordpress-tests repo.
if ( FALSE !== getenv( 'WP_TESTS_DIR' ) ) {
	require getenv( 'WP_TESTS_DIR' ) . '/includes/bootstrap.php';
} else {
	require '../../../../includes/bootstrap.php';
}