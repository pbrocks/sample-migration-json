<?php
/**
 * Main Post_Type_Admin_Notes File
 *
 * @package    WordPress
 * @subpackage sample_structured_export
 */

namespace Phoenix\Sample_Structured_Export\Admin;

use Phoenix\Sample_Structured_Export\Util\Generate_UUID;

/**
 * Class Post_Type_Admin_Notes
 */
class Post_Type_Admin_Notes {



	public static function init() {
		add_action( 'add_to_post_type_notes', array( __CLASS__, 'post_type_notes_callback' ) );
		add_action( 'add_to_post_type_notes', array( __CLASS__, 'stories_post_type_info' ) );
		add_action( 'add_to_post_type_notes', array( __CLASS__, 'products_post_type_info' ) );
		add_action( 'add_to_post_type_notes', array( __CLASS__, 'swears_post_type_info' ) );
	}

	/**
	 * Add add_action() and add_filter() in this method.
	 */
	public static function show_some_info() {
		echo '<div class="post-type-notes">';
		echo '<h2>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h2>';
		$generated = Generate_UUID::fetch_uuid();
		echo '<pre>';
		print_r( $generated );
		echo '</pre>';
		echo '<h4>Located in ' . __FILE__ . '</h4>';
		echo '</div>';
	}

	/**
	 * Add add_action() and add_filter() in this method.
	 */
	public static function notes_hook() {
		echo '<div class="add-to-sample-dash">';
		do_action( 'add_to_post_type_notes' );
		echo '</div>';
	}

	/**
	 * Register and add settings
	 */
	public static function post_type_notes_callback() {
		echo '<div class="post-type-notes">';
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		self::show_sample_post_type_notes();
		echo '</div>';
	}

	/**
	 * Register and add settings
	 */
	public static function stories_post_type_info() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		echo '<div class="post-type-notes">';
		self::show_stories_post_type_notes();
		echo '</div>';
	}

	public static function show_sample_post_type_notes() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		echo '<p class="description">Purpose of this admin page is to display the important Post Types and their respective Taxonomies found in Sample.</p>';
		$args = array(
			'public'   => true,
			'_builtin' => false,
		);

		$post_types = get_post_types( $args );

		echo '<h3 style="color:green;">Current Filter/Action ' . current_filter() . '</h3>';

		if ( isset( $_REQUEST['action'] ) && 'show_sample_post_type' === $_REQUEST['action'] ) {
			echo '<h4>To hide Sample Post Types <a href="' . esc_url( remove_query_arg( 'action' ) ) . '"><button>Click Here</button></a></h4>';
			echo '<pre>show_sample_post_type ';

			if ( ! empty( $post_types ) ) : ?>
			<ul>
					<?php
					foreach ( $post_types as $post_type ) {
						echo '<li>' . $post_type . '</li>';
					}
					?>
			</ul>
				<?php
			endif;
			echo '</pre>';
		} else {
			echo '<h4>To show Sample Post Types <a href="' . esc_url( add_query_arg( 'action', 'show_sample_post_type' ) ) . '"><button>Click Here</button></a></h4>';
		}
	}


	public static function show_stories_post_type_notes() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		self::show_stories_post_type_taxos();

		$stories = get_post_type_object( 'stories' );

		if ( isset( $_REQUEST['action'] ) && 'show_stories' === $_REQUEST['action'] ) {
			echo '<h4>To hide Stories Object <a href="' . esc_url( remove_query_arg( 'action' ) ) . '"><button>Click Here</button></a></h4>';
			echo '<pre>$stories ';
			print_r( $stories );
			echo '</pre>';
		} else {
			echo '<h4>To show Stories Object <a href="' . esc_url( add_query_arg( 'action', 'show_stories' ) ) . '"><button>Click Here</button></a></h4>';
		}
	}

	public static function show_stories_post_type_taxos() {
		$stories = get_post_type_object( 'stories' );
		echo '<pre>$stories ';
		print_r( $stories->taxonomies );
		echo '</pre>';
	}

	/**
	 * Register and add settings
	 */
	public static function products_post_type_info() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		echo '<div class="post-type-notes">';
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		self::show_products_post_type_notes();
		echo '</div>';
	}

	public static function show_products_post_type_notes() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		self::show_products_post_type_taxos();
		$products = get_post_type_object( 'products' );

		if ( isset( $_REQUEST['action'] ) && 'show_products' === $_REQUEST['action'] ) {
			echo '<h4>To hide Products Object <a href="' . esc_url( remove_query_arg( 'action' ) ) . '"><button>Click Here</button></a></h4>';
			echo '<pre>$products ';
			print_r( $products );
			echo '</pre>';
		} else {
			echo '<h4>To show Products Object <a href="' . esc_url( add_query_arg( 'action', 'show_products' ) ) . '"><button>Click Here</button></a></h4>';
		}
	}

	public static function show_products_post_type_taxos() {
		$products = get_post_type_object( 'products' );
		echo '<pre>$products ';
		print_r( $products->taxonomies );
		echo '</pre>';
	}

	/**
	 * Register and add settings
	 */
	public static function swears_post_type_info() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		echo '<div class="post-type-notes">';
		self::show_swears_post_type_notes();
		echo '</div>';
	}

	public static function show_swears_post_type_notes() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		self::show_swears_post_type_taxos();
		$swears = get_post_type_object( 'swears' );

		if ( isset( $_REQUEST['action'] ) && 'show_swears' === $_REQUEST['action'] ) {
			echo '<h4>To hide Swears Object <a href="' . esc_url( remove_query_arg( 'action' ) ) . '"><button>Click Here</button></a></h4>';
			echo '<pre>$swears ';
			print_r( $swears );
			echo '</pre>';
		} else {
			echo '<h4>To show Swears Object <a href="' . esc_url( add_query_arg( 'action', 'show_swears' ) ) . '"><button>Click Here</button></a></h4>';
		}
	}

	public static function show_swears_post_type_taxos() {
		$swears = get_post_type_object( 'swears' );
		echo '<pre>$swears ';
		print_r( $swears->taxonomies );
		echo '</pre>';
	}
}
Post_Type_Admin_Notes::init();
