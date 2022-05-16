<?php
/**
 * PHPUnit Test
 *
 * @package sample_structured_export
 */

/**
 * App_Test
 */
final class App_Test extends WP_UnitTestCase {



	/**
	 * Test __construct
	 *
	 * @covers \Phoenix\Sample_Structured_Export\Admin\App::__construct
	 */
	public function test_construct() {
		$installed_dir = '/var/installed/dir';
		$installed_url = '/var/installed/url';
		$version       = 1.0;

		$app = new \Phoenix\Sample_Structured_Export\Admin\App( $installed_dir, $installed_url, $version );

		$this->assertEquals( $installed_dir, $app->installed_dir );
		$this->assertEquals( $installed_url, $app->installed_url );
		$this->assertEquals( $version, $app->version );
	}
}
