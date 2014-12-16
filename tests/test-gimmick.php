<?php # -*- coding: utf-8 -*-

namespace Multisite_Enhancements\Tests;

/**
 * Class Test_Gimmick
 * Two simple to test to demonstrate the test for failure and assertion
 *
 * @package Multisite_Enhancements\Tests
 */
class Test_Gimmick extends \PHPUnit_Framework_TestCase {

	/**
	 * Validate string
	 */
	public function right_test_string() {

		$string = 'I will learn UnitTesting';
		$this->assertEquals( 'I will learn UnitTesting', $string );
	}

	public function wrong_test_string() {

		$string = 'I will learn UnitTesting';
		$this->assertEquals( 'I will learn UnitTesting, now', $string );
	}
}

/**
 * Class WP_Test_WordPress_Plugin_Tests
 * Tests to test that that testing framework is testing tests. Meta, huh?
 *
 * @package Multisite_Enhancements\Tests
 */
class WP_Test_WordPress_Plugin_Tests extends \WP_UnitTestCase {

	/**
	 * Run a simple test to ensure that the tests are running
	 */
	function test_tests() {

		$this->assertTrue( TRUE );
	}

}