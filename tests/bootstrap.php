<?php # -*- coding: utf-8 -*-

namespace Multisite_Enhancements;

// Use default composer autoload
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// Activates this plugin in WordPress so it can be tested.
$GLOBALS[ 'wp_tests_options' ] = array(
	'active_plugins' => array( 'wordpress-multisite-enhancements/multisite-enhancements.php' ),
);
