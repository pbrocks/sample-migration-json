<?php
/**
 * Main Admin_Page_Additions File
 *
 * @package    WordPress
 * @subpackage sample_structured_export
 */

namespace Phoenix\Sample_Structured_Export\Admin;

/**
 * Class Admin_Page_Additions
 */
class Admin_Page_Additions {


	/**
	 * Add add_action() and add_filter() in this method.
	 */
	public static function init() {
	}

	/**
	 * Add css.
	 */
	public static function notes_pages_scripts() {
		wp_register_style( 'notes-page', plugins_url( '/', __FILE__ ), array(), time() );
		wp_enqueue_style( 'notes-page' );
	}

	/**
	 * Add add_action() and add_filter() in this method.
	 */
	public static function show_some_info() {
		echo '<h2>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h2>';
		echo '<h3>Using <span style="color:salmon;">' . self::get_current_branch_name() . '</span> branch</h3>';
		echo '<h3>and <span style="color:salmon;">' . self::get_current_git_commit() . '</span> is current hash</h3>';
		echo '<h4>Plugin file located in ' . plugin_dir_path( dirname( __DIR__ ) ) . '</h4>';
	}

	/**
	 * Add css.
	 */
	public static function user_commerce_product_example() {
		$sample = '{
  "_type": "user-commerce-product",
  "cms_id": "1234",
  "user": {
    "$id": "resound/user_2fd4e1c67a2d28fced849ee1bb76e7391b93eb12"
  },
  "publish_date": "2017-04-24T23:27:02Z",
  "last_updated": "2017-04-24T23:27:02Z",
  "product": {
    "$id": "products/product_12345"
  },
}
';
		return '<pre>' . $sample . '</pre>';
	}

	/**
	 * Get the hash of the current git HEAD
	 *
	 * @param str $current_branch The git branch to check
	 * @return mixed Either the hash or a boolean false
	 */
	public static function get_current_git_commit() {
		$current_branch = self::get_current_branch_name();
		if ( $hash = file_get_contents( sprintf( plugin_dir_path( dirname( __DIR__ ) ) . '/.git/refs/heads/%s', $current_branch ) ) ) {
			return $hash;
		} else {
			return 'false dunno';
		}
	}
	public static function get_current_branch_name() {
		$gitfile = plugin_dir_path( dirname( __DIR__ ) ) . '/.git/HEAD';
		preg_match( '#^ref:(.+)$#', file_get_contents( $gitfile ), $matches );
		$current_head = explode( '/', $matches[1] );
		return $current_head[2];
	}
}
