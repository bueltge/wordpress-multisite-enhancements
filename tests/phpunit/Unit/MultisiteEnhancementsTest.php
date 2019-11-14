<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace MultisiteEnhancements\Tests\Unit;

class MultisiteEnhancementsTest extends AbstractTestCase
{
	public function testBasicInstantiation()
    {
		$plugin = new \Multisite_Enhancements(__DIR__);
		static::assertInstanceOf(Multisite_Enhancements::class, $plugin);
	}
}
