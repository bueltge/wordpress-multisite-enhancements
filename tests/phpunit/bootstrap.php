<?php # -*- coding: utf-8 -*-

if ( ! realpath( dirname( __DIR__, 2 ) . '/vendor/' ) ) {
	die( 'Please install via Composer before running the tests.' );
}

require_once dirname( __DIR__, 2 ) . '/vendor/autoload.php';
