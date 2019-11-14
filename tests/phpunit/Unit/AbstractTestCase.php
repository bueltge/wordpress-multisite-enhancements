<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace MultisiteEnhancements\Tests\Unit;

use Brain\Monkey;
use PHPUnit\Framework\TestCase;

/**
 * Class AbstractTestCase
 */
abstract class AbstractTestCase extends TestCase
{
    /**
	 * Sets up the environment.
	 *
	 * @return void
	 */
	protected function setUp()
    {
		parent::setUp();
		Monkey\setUp();
	}

	/**
	 * Tears down the environment.
	 *
	 * @return void
	 */
	protected function tearDown()
    {
		Monkey\tearDown();
		parent::tearDown();
	}
}
