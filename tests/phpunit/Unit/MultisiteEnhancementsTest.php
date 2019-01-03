<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace MultisiteEnhancements\Tests\Unit;

class MultisiteEnhancementsTest extends AbstractTestCase
{
	public function testBasicInstantiation()
    {
		$plugin = new Plugin(__DIR__);
		static::assertInstanceOf(Plugin::class, $plugin);
	}
}
