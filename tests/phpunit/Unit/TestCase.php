<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace Multisite_Enhancements\Tests\Unit;

use Brain\Monkey;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit_Framework_TestCase;

/**
 * Class AbstractTestCase
 */
class TestCase extends \PHPUnit\Framework\TestCase {
	// Adds Mockery expectations to the PHPUnit assertions count.
	use MockeryPHPUnitIntegration;

	/**
	 * Set up for tests
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	/**
	 * Tears down the environment.
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}
}
