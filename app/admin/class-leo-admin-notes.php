<?php
/**
 * Main Leo_Admin_Notes File
 *
 * @package    WordPress
 * @subpackage sample_structured_export
 */

namespace Phoenix\Sample_Structured_Export\Admin;

/**
 * Class Leo_Admin_Notes
 */
class Leo_Admin_Notes {



	public static function init() {
		add_action( 'add_to_leo_notes', array( __CLASS__, 'leo_notes_callback' ) );
		add_action( 'add_to_leo_notes', array( __CLASS__, 'leo_more_info' ) );
	}

	/**
	 * Add add_action() and add_filter() in this method.
	 */
	public static function show_some_info() {
		echo '<div class="leo-notes">';
		echo '<h2>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h2>';
		echo '<h4>Located in ' . __FILE__ . '</h4>';
		echo '</div>';
	}

	/**
	 * Add add_action() and add_filter() in this method.
	 */
	public static function notes_hook() {
		self::init();
		echo '<div class="add-to-sample-dash">';
		do_action( 'add_to_leo_notes' );
		echo '</div>';
	}

	/**
	 * Register and add settings
	 */
	public static function leo_notes_callback() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		self::show_some_info();
		self::leo_show_31945_product_object();
		self::leo_show_31945_product_meta();
		self::leo_show_products_acf_meta();
		self::leo_show_31945_product_terms();
	}

	/**
	 * Register and add settings
	 */
	public static function leo_more_info() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
	}


	/**
	 * Register and add settings
	 */
	public static function leo_show_31945_product_terms() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		$post = get_post( 31945 );

		// Returns Array of Term IDs for "category".
		$categories = wp_get_post_terms( $post->ID, 'category' );
		echo '<pre>$categories ';
		print_r( $categories );
		echo '</pre>';

		// Array of Term IDs for "post_tag".
		// $post_tags = get_the_terms( $post->ID, 'post_tag' );
		$post_tags = wp_get_post_terms( $post->ID, 'post_tag', array( 'fields' => 'ids' ) );
		echo '<pre>$post_tags ';
		print_r( $post_tags );
		echo '</pre>';

		// Returns Array of Term IDs for "category".
		/**
		WP_Term Object(
			[term_id] => 2
			[name] => Beauty
			[slug] => beauty
			[term_group] => 0
			[term_taxonomy_id] => 2
			[taxonomy] => category
			[description] =>
			[parent] => 0
			[count] => 2970
			[filter] => raw
			[term_order] => 1
		)
		 */
		$category_items = wp_get_post_terms( $post->ID, 'category' );
		echo 'category slug | term_id  | parent ';
		foreach ( $category_items as $key => $value ) {
			echo '<pre> ';
			echo $value->slug . ' | ' . $value->term_id . ' | ' . ( 0 !== $value->parent ? $value->parent : 'parent' );
			echo '</pre>';
		}
	}


	/**
	 * Register and add settings
	 */
	public static function leo_show_31945_product_object() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';

		echo '<div class="leo-notes">';
		if ( isset( $_REQUEST['action'] ) && 'show_sample_31945_product' === $_REQUEST['action'] ) {
			echo '<p>To hide Sample Product 31945 <a href="' . esc_url( remove_query_arg( 'action' ) ) . '"><button>Click Here</button></a></p>';

			$product = get_post( 31945 );

			echo '<pre>$product ';
			print_r( $product );
			echo '</pre>';
		} else {
			echo '<h4>To show Sample Product 31945 <a href="' . esc_url( add_query_arg( 'action', 'show_sample_31945_product' ) ) . '"><button>Click Here</button></a></h4>';
		}
		echo '</div>';
	}

	/**
	 * Register and add settings
	 */
	public static function leo_show_31945_product_meta() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		echo '<div class="leo-notes">';
		if ( isset( $_REQUEST['action'] ) && 'show_sample_31945_product_meta' === $_REQUEST['action'] ) {
			echo '<p>To hide Sample Product 31945 meta <a href="' . esc_url( remove_query_arg( 'action' ) ) . '"><button>Click Here</button></a></p>';

			$product = get_post_meta( 31945 );

			echo '<pre>$product ';
			print_r( $product );
			echo '</pre>';
		} else {
			echo '<h4>To show Sample Product 31945 meta <a href="' . esc_url( add_query_arg( 'action', 'show_sample_31945_product_meta' ) ) . '"><button>Click Here</button></a></h4>';
		}
		echo '</div>';
	}

	/**
	 * Register and add settings
	 */
	public static function leo_show_products_acf_meta() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		echo '<div class="leo-notes">';
		if ( isset( $_REQUEST['action'] ) && 'show_sample_show_products_acf' === $_REQUEST['action'] ) {
			echo '<h4>To hide Sample Product 31945 ACF fields <a href="' . esc_url( remove_query_arg( 'action' ) ) . '"><button>Click Here</button></a></h4>';
			echo '<pre>$product ';
			print_r( get_fields( 31945 ) );
			echo '</pre>';
		} else {
			echo '<h4>To show Sample Product 31945 ACF fields <a href="' . esc_url( add_query_arg( 'action', 'show_sample_show_products_acf' ) ) . '"><button>Click Here</button></a></h4>';
		}
		echo '</div>';
	}
}
