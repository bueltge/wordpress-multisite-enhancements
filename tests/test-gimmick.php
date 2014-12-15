<?php # -*- coding: utf-8 -*-

namespace Multisite_Enhancements\Tests;

class Test_Gimmick extends \PHPUnit_Framework_TestCase {

	/**
	 * Validate string
	 */
	public function test_string() {

		$string = 'I will learn UnitTesting';
		$this->assertEquals( 'I will learn UnitTesting, now', $string );
	}
}