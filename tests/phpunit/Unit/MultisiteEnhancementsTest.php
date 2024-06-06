<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace MultisiteEnhancements\Tests\Unit;

/**
 * First Test
 */
class MultisiteEnhancementsTest extends TestCase {
	/**
	 * First Test
	 *
	 * @return void
	 */
	public function testBasicInstantiation() {
		$plugin = new \Multisite_Enhancements();
		static::assertInstanceOf( \Multisite_Enhancements::class, $plugin );
	}
}
