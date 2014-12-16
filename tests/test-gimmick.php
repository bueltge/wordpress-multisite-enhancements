<?php # -*- coding: utf-8 -*-

/**
 * Class Test_Gimmick
 * Two simple to test to demonstrate the test for failure and assertion
 *
 */
class Test_Gimmick extends PHPUnit_Framework_TestCase {

	static protected $expected = 'I will learn UnitTesting.';

	static protected $actual = 'I will learn UnitTesting.';

	/**
	 * Validate string
	 */
	public function right_test_string() {

		// @see https://phpunit.de/manual/current/en/appendixes.assertions.html#appendixes.assertions.assertEquals
		$this->assertEquals( self::$expected, self::$actual );
	}

	/**
	 * Reports an error identified by $string if the two variables $expected and $actual are not equal.
	 */
	public function wrong_test_string() {

		$actual = self::$actual . ' Now with error!';
		$this->assertEquals( self::$expected, $actual );
	}
}

/**
 * Class WP_Test_WordPress_Plugin_Tests
 * Tests to test that that testing framework is testing tests. Meta, huh?
 *
 */
class WP_Test_WordPress_Plugin_Tests extends WP_UnitTestCase {

	/**
	 * Run a simple test to ensure that the tests are running
	 */
	function test_tests() {

		$this->assertTrue( TRUE );
	}

}