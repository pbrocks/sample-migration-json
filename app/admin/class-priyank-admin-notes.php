<?php
/**
 * Main Priyank_Admin_Notes File
 *
 * @package    WordPress
 * @subpackage sample_structured_export
 */

namespace Phoenix\Sample_Structured_Export\Admin;

/**
 * Class Priyank_Admin_Notes
 */
class Priyank_Admin_Notes {



	public static function init() {
		add_action( 'init', array( __CLASS__, 'notes_hook' ), 9 );
		add_action( 'add_to_priyank_notes', array( __CLASS__, 'priyank_notes_callback' ), 11 );
		// add_action( 'add_to_priyank_notes', array( __CLASS__, 'priyank_more_info' ), 13 );
	}

	/**
	 * Add add_action() and add_filter() in this method.
	 */
	public static function notes_hook() {
		echo '<div class="add-to-sample-dash">';
		do_action( 'add_to_priyank_notes' );
		echo '</div>';
	}

	/**
	 * Register and add settings
	 */
	public static function priyank_notes_callback() {
		echo '<div class="priyank-notes">';
		// echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		// self::show_some_info();
		// self::priyank_more_info();
		self::show_users_objects();
		self::show_user_by_id_objects();
		echo '</div>';
	}

	public static function show_users_objects() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		if ( isset( $_REQUEST['action'] ) && 'show_sample_show_users' === $_REQUEST['action'] ) {
			echo '<h4>To hide Sample Users <a href="' . esc_url( remove_query_arg( 'action' ) ) . '"><button>Click Here</button></a></h4>';
			$args = array(
				'number'           => 5,
				'orderby'          => 'ID',
				'order'            => 'ASC',
			);
			$wp_user_query = new \WP_User_Query( $args );
			$users = $wp_user_query->get_results();

			echo '<pre>';
			print_r( $users );
			echo '</pre>';
		} else {
			echo '<h4>To show Sample Users <a href="' . esc_url( add_query_arg( 'action', 'show_sample_show_users' ) ) . '"><button>Click Here</button></a></h4>';
		}
	}

	public static function show_user_by_id_objects() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		if ( isset( $_REQUEST['action'] ) && 'show_sample_show_user_by_id' === $_REQUEST['action'] ) {
			$id = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : 35;
			echo '<h4>To hide Sample User by ID ' . $id . ' <a href="' . esc_url( remove_query_arg( 'action' ) ) . '"><button>Click Here</button></a></h4>';

			$user = get_userdata( $_REQUEST['id'] );

			echo '<pre>';
			print_r( $user );
			echo '</pre>';
		} else {
			echo '<h4>To show Sample User <a href="' . esc_url(
				add_query_arg(
					array(
						'action' => 'show_sample_show_user_by_id',
						'id' => 35,
					)
				)
			) . '"><button>Click Here</button></a></h4>';
		}
	}

	/**
	 * Add add_action() and add_filter() in this method.
	 */
	public static function show_some_info() {
		echo '<div class="priyank-notes">';
		echo '<h2>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h2>';
		echo '<h4>Located in ' . __FILE__ . '</h4>';
		echo '</div>';
	}

	/**
	 * Register and add settings
	 */
	public static function priyank_more_info() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		echo 'Showing some more info here';
		echo '<pre>';
		print_r( $user = get_userdata( 35 ) );
	}
}
Priyank_Admin_Notes::init();
