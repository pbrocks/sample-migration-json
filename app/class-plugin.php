<?php
/**
 * Plugin Sample_Structured_Export
 *
 * @package WordPress
 * @subpackage sample_structured_export
 */

namespace Phoenix\Sample_Structured_Export;

use Phoenix\Sample_Structured_Export\Admin;
use WPAZ_Plugin_Base\V_2_6\Abstract_Plugin;

/**
 * Class Plugin
 */
class Plugin extends Abstract_Plugin {

	/**
	 * Use magic constant to tell abstract class current namespace as prefix for all other namespaces in the plugin.
	 *
	 * @var string $autoload_class_prefix magic constant
	 */
	public static $autoload_class_prefix = __NAMESPACE__;

	/**
	 * Action prefix is used to automatically and dynamically assign a prefix to all action hooks.
	 *
	 * @var string
	 */
	public static $action_prefix = 'sample_structured_export_';

	/**
	 * Magic constant trick that allows extended classes to pull actual server file location, copy into subclass.
	 *
	 * @var string $current_file
	 */
	protected static $current_file = __FILE__;

	/**
	 * Initialize the plugin - for public (front end)
	 *
	 * @param mixed $instance Parent instance passed through to child.
	 *
	 * @since   0.1
	 * @return  void
	 */
	public function onload( $instance ) {
		$this->udf_export = new UDF_Export();
	}

	/**
	 * Initialize public / shared functionality using new Class(), add_action() or add_filter().
	 *
	 * @return void
	 */
	public function init() {
		do_action( static::$action_prefix . 'before_init' );
		do_action( static::$action_prefix . 'after_init' );

		new Notify();
	}

	/**
	 * Initialize functionality only loaded for logged-in users.
	 *
	 * @return void
	 */
	public function authenticated_init() {
		if ( is_user_logged_in() ) {
			do_action( static::$action_prefix . 'before_authenticated_init' );
			// Object $this->admin is in the abstract plugin base class.
			$this->admin = new Admin\App(
				$this->installed_dir,
				$this->installed_url,
				$this->version
			);
			do_action( static::$action_prefix . 'after_authenticated_init' );
		}
	}

	/**
	 * Defines and Globals.
	 *
	 * @return void
	 */
	protected function defines_and_globals() {
	}
} // END class Plugin
