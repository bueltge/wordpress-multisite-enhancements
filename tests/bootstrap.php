<?php # -*- coding: utf-8 -*-

#namespace Multisite_Enhancements;

// Use default composer autoload
#require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// Activates this plugin in WordPress so it can be tested.
$GLOBALS[ 'wp_tests_options' ] = array(
	'active_plugins' => array( 'wordpress-multisite-enhancements/multisite-enhancements.php' ),
);

// If the develop repo location is defined (as WP_DEVELOP_DIR), use that
// location. Otherwise, we'll just assume that this plugin is installed in a
// WordPress develop SVN checkout.
if ( FALSE !== getenv( 'WP_DEVELOP_DIR' ) ) {
	require getenv( 'WP_DEVELOP_DIR' ) . '/tests/phpunit/includes/bootstrap.php';
} else {
	require '../../../../tests/phpunit/includes/bootstrap.php';
}