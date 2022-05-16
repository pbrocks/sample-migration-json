<?php
/**
 * Main Paul_Admin_Notes File
 *
 * @package    WordPress
 * @subpackage sample_structured_export
 */

namespace Phoenix\Sample_Structured_Export\Admin;

use Phoenix\Sample_Structured_Export\Util\Generate_UUID;

/**
 * Class Paul_Admin_Notes
 */
class Paul_Admin_Notes {




	public static function init() {
		add_action( 'add_to_paul_notes', array( __CLASS__, 'paul_notes_callback' ) );
		add_action( 'add_to_paul_notes', array( __CLASS__, 'paul_post_tag_info' ), 11 );
		add_action( 'add_to_paul_notes', array( __CLASS__, 'paul_arguments_info' ), 12 );
		// add_action( 'add_to_paul_notes', array( __CLASS__, 'print_wp_global_filters' ), 11 );
		// add_action( 'add_to_paul_notes', array( __CLASS__, 'print_filters_container' ), 11 );
	}

	/**
	 * Add add_action() and add_filter() in this method.
	 */
	public static function show_some_info() {
		echo '<div class="paul-notes">';
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
		do_action( 'add_to_paul_notes' );
		echo '</div>';
	}

	/**
	 * Register and add settings
	 */
	public static function paul_notes_callback() {
		echo '<div class="paul-notes">';
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		self::show_sample_taxonomies_for_paul_notes();
		echo '</div>';
	}

	/**
	 * Register and add settings
	 */
	public static function paul_arguments_info() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		echo '<div class="paul-notes">';
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		self::show_arguments_for_paul_notes();
		self::show_arguments_taxonomy_terms();
		echo '</div>';
	}

	public static function show_arguments_for_paul_notes() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		echo __FILE__;
		echo Admin_Page_Additions::user_commerce_product_example();
	}

	public static function show_arguments_taxonomy_terms() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		echo __FILE__;
		echo '<br>Good example with 4 https://devwpe.local/wp-json/structured/v2/documents/31934';
	}

	/**
	 * Register and add settings
	 */
	public static function paul_post_tag_info() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		echo '<div class="paul-notes"-container>';
		self::question_what_is_wrong_h3();
		echo '</div>';
	}

	public static function show_sample_taxonomies_for_paul_notes() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		$taxonomies = get_taxonomies();

		echo '<h3 style="color:green;">Current Filter/Action ' . current_filter() . '</h3>';

		if ( isset( $_REQUEST['action'] ) && 'show_sample_taxonomies' === $_REQUEST['action'] ) {
			echo '<h4>To hide Sample Taxonomies <a href="' . esc_url( remove_query_arg( 'action' ) ) . '"><button>Click Here</button></a></h4>';
			echo '<pre>show_sample_taxonomies ';

			if ( ! empty( $taxonomies ) ) : ?>
			<ul>
					<?php
					foreach ( $taxonomies as $taxonomy ) {
						echo '<li>' . $taxonomy . '</li>';
					}
					?>
			</ul>
				<?php
			endif;
			echo '</pre>';
		} else {
			echo '<h4>To show Sample Taxonomies <a href="' . esc_url( add_query_arg( 'action', 'show_sample_taxonomies' ) ) . '"><button>Click Here</button></a></h4>';
		}
	}

	public static function question_what_is_wrong_h3() {
		echo '<div>+++++++++++++++++' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' | ' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></div><br>';
		echo '<h3>+++++++++++++++++' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		echo '<h3>+++++++++++++++++' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		$post_tag = get_taxonomy( 'post_tag' );
		self::show_post_tag_taxonomy_terms();

		if ( isset( $_REQUEST['action'] ) && 'show_post_tag' === $_REQUEST['action'] ) {
			echo '<h4>To hide Post Tag Object <a href="' . esc_url( remove_query_arg( 'action' ) ) . '"><button>Click Here</button></a></h4>';
			echo '<pre>$post_tag ';
			print_r( $post_tag );
			echo '</pre>';
		} else {
			echo '<h4>To show Post Tag Object <a href="' . esc_url( add_query_arg( 'action', 'show_post_tag' ) ) . '"><button>Click Here</button></a></h4>';
		}
	}

	public static function show_post_tag_terms_for_taxonomy_terms() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		$terms = get_terms(
			array(
				'taxonomy' => 'post_tag',
				'hide_empty' => false,
			)
		);
		if ( isset( $_REQUEST['action'] ) && 'show_post_tag_terms' === $_REQUEST['action'] ) {
			echo '<h4>To hide Post Tag Terms <a href="' . esc_url( remove_query_arg( 'action' ) ) . '"><button>Click Here</button></a></h4>';
			echo '<pre>show_post_tag_terms ';

			if ( ! empty( $terms ) ) {
				print_r( $terms );
			} else {
				echo '<h3 style="color:salmon;">Empty Taxonomy Terms</h3>';
			}

			echo '</pre>';
		} else {
			echo '<h4>To show Post Tag Terms <a href="' . esc_url( add_query_arg( 'action', 'show_post_tag_terms' ) ) . '"><button>Click Here</button></a></h4>';
		}
	}

	public static function show_post_tag_taxonomy_terms() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		$show_post_tag_list = get_terms(
			array(
				'taxonomy' => 'post_tag',
				'hide_empty' => false,
			)
		);
		if ( isset( $_REQUEST['action'] ) && 'show_post_tag_list' === $_REQUEST['action'] ) {
			echo '<h4>To hide Tags List <a href="' . esc_url( remove_query_arg( 'action' ) ) . '"><button>Click Here</button></a></h4>';
			echo '<pre>$show_post_tag_list ';
			foreach ( $show_post_tag_list as $key => $value ) {
				echo '<br>' . $value->name . ' | ' . $value->slug . ' | ' . $value->term_id;
			}
			echo '</pre>';
		} else {
			echo '<h4>To show Tags List <a href="' . esc_url( add_query_arg( 'action', 'show_post_tag_list' ) ) . '"><button>Click Here</button></a></h4>';
		}
	}

	/**
	 * Register and add settings
	 */
	public static function print_filters_container() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		echo '<div class="paul-notes"-container>';
		self::print_wp_global_filters();
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		echo '</div>';
	}

	public static function print_wp_global_filters() {
		global $wp_filter;
		$comment_filters = array();
		$h1  = '<h2>Current Comment Filters</h2>';
		$out = '';
		$toc = '<ul>';

		foreach ( $wp_filter as $key => $val ) {
			if ( false !== strpos( $key, 'comment' ) ) {
				$comment_filters[ $key ][] = var_export( $val, true );
			}
		}

		foreach ( $comment_filters as $name => $arr_vals ) {
			$out .= "<h2 id=$name>$name</h2><pre>" . implode( "\n\n", $arr_vals ) . '</pre>';
			$toc .= "<li><a href='#$name'>$name</a></li>";
		}
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		// echo '<div class="paul-notes">';
		echo "$h1$toc</ul>$out";
		// echo '</div>';
	}
}
Paul_Admin_Notes::init();
