<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace Multisite_Enhancements\Tests\Unit;

use Brain\Monkey\Functions;

/**
 * First Test
 */
class MultisiteEnhancementsTest extends TestCase {

	/**
	 * Test getting and setting objects
	 *
	 * @return void
	 */
	public function testContainer() {

		$object = \Multisite_Enhancements::get_object( 'TestClassName' );
		$this->assertNull( $object );

		$object       = new \stdClass();
		$std_object_0 = \Multisite_Enhancements::set_object( $object );
		$this->assertInstanceOf( \stdClass::class, $std_object_0 );
		$std_object_1 = \Multisite_Enhancements::get_object( \stdClass::class );
		$this->assertInstanceOf( \stdClass::class, $std_object_1 );

		$plugin = \Multisite_Enhancements::get_object();
		$this->assertInstanceOf( \Multisite_Enhancements::class, $plugin );
	}

	/**
	 * Test the load method
	 *
	 * @return void
	 */
	public function testLoad() {

		$mock_settings = \Mockery::mock( 'alias:' . \Multisite_Enhancements\Settings::class );
		$mock_settings->shouldReceive( 'is_feature_enabled' )->andReturn( true );

		Functions\when( 'is_multisite' )->justReturn( true );
		Functions\when( 'load_plugin_textdomain' )->justReturn( true );

		$plugin = \Multisite_Enhancements::get_object();
		$plugin->load();

		if ( defined( 'MULTISITE_ENHANCEMENT_BASE' ) ) {
			$this->assertEquals( realpath( __DIR__ . '/../../../src' ), MULTISITE_ENHANCEMENT_BASE );
		} else {
			$this->fail( 'Const MULTISITE_ENHANCEMENT_BASE not defined!' );
		}

		$settings_object = \Multisite_Enhancements::get_object( \Multisite_Enhancements\Settings::class );
		self::assertNotFalse( has_action( 'init', [ $settings_object, 'init' ] ) );

		$object = \Multisite_Enhancements::get_object( \Multisite_Enhancements\Add_Admin_Favicon::class );
		self::assertNotFalse( has_action( 'init', [ $object, 'init' ] ) );
		$object = \Multisite_Enhancements::get_object( \Multisite_Enhancements\Add_Blog_Id::class );
		self::assertNotFalse( has_action( 'init', [ $object, 'init' ] ) );
		$object = \Multisite_Enhancements::get_object( \Multisite_Enhancements\Filtering_Themes::class );
		self::assertNotFalse( has_action( 'admin_init', [ $object, 'init' ] ) );
	}

	/**
	 * Test the load of Translations
	 *
	 * @return void
	 */
	public function testLoadTranslations() {

		$plugin = \Multisite_Enhancements::get_object();

		Functions\expect( 'load_plugin_textdomain' )->once()->with( 'multisite-enhancements', false, basename( dirname( __DIR__, 3 ) ) . '/languages/' );

		$plugin->load_translation();
	}
}
