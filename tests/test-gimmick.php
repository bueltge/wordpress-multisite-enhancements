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
	public function test_string() {

		// @see https://phpunit.de/manual/current/en/appendixes.assertions.html#appendixes.assertions.assertEquals
		$this->assertEquals( self::$expected, self::$actual );
	}

	/**
	 * Validate string, Reports an error
	 */
	public function test_string_2() {

		$actual = self::$actual;// . ' Now with error!';
		$this->assertEquals( self::$expected, $actual );
	}
}