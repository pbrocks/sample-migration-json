<?php
/**
 * PHPUnit Test
 *
 * @package sample_structured_export
 */

/**
 * Plugin_Test
 */
final class Plugin_Test extends WP_UnitTestCase {



	/**
	 * Test onload
	 *
	 * @covers \Phoenix\Sample_Structured_Export\Plugin::onload
	 */
	public function test_onload() {
		$instance = new stdClass();

		$plugin   = new \Phoenix\Sample_Structured_Export\Plugin();
		$result   = $plugin->onload( $instance );

		$this->assertNull( $result );
	}

	/**
	 * Test init
	 *
	 * @covers \Phoenix\Sample_Structured_Export\Plugin::init
	 */
	public function test_init() {
		global $wp_actions;

		$plugin = new \Phoenix\Sample_Structured_Export\Plugin();
		$result = $plugin->init();

		$this->assertTrue( isset( $wp_actions['sample_structured_export_before_init'] ) );
		$this->assertTrue( isset( $wp_actions['sample_structured_export_after_init'] ) );

		$this->assertNull( $result );
	}

	/**
	 * Test authenticated_init when user is not logged in
	 *
	 * @covers \Phoenix\Sample_Structured_Export\Plugin::authenticated_init
	 */
	public function test_authenticated_init_when_user_is_not_logged_in() {
		$plugin             = new \Phoenix\Sample_Structured_Export\Plugin();
		$authenticated_init = $plugin->authenticated_init();

		$this->assertFalse( isset( $plugin->admin ) );
		$this->assertNull( $authenticated_init );
	}

	/**
	 * Test authenticated_init when user is logged in
	 *
	 * @covers \Phoenix\Sample_Structured_Export\Plugin::authenticated_init
	 */
	public function test_authenticated_init_when_user_is_logged_in() {
		$user_id = $this->factory->user->create();
		wp_set_current_user( $user_id );

		$plugin             = new \Phoenix\Sample_Structured_Export\Plugin();
		$authenticated_init = $plugin->authenticated_init();

		$this->assertTrue( isset( $plugin->admin ) );
		$this->assertNull( $authenticated_init );
	}
}
