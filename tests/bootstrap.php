<?php # -*- coding: utf-8 -*-
// Use default composer autoload for local test
require_once dirname( __DIR__ ) . '/vendor/autoload.php';
// Activates this plugin in WordPress so it can be tested.
$GLOBALS[ 'wp_tests_options' ] = array(
	'active_plugins' => array( 'wordpress-multisite-enhancements/multisite-enhancements.php' ),
);
/**
 * If the wordpress-tests repo location has been customized (and specified
 * with WP_TESTS_DIR), use that location. This will most commonly be the case
 * when configured for use with Travis CI.
 * Otherwise, we'll just assume that this plugin is installed in the WordPress
 * SVN external checkout configured in the wordpress-tests repo.
 *
 * @see SVN URL: http://develop.svn.wordpress.org/trunk/
 */
if ( FALSE !== getenv( 'WP_TESTS_DIR' ) ) {
	require getenv( 'WP_TESTS_DIR' ) . '/tests/phpunit/includes/bootstrap.php';
}
