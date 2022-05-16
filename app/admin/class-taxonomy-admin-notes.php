<?php
/**
 * Main Taxonomy_Admin_Notes File
 *
 * @package    WordPress
 * @subpackage sample_structured_export
 */

namespace Phoenix\Sample_Structured_Export\Admin;

use Phoenix\Sample_Structured_Export\Util\Generate_UUID;

/**
 * Class Taxonomy_Admin_Notes
 */
class Taxonomy_Admin_Notes {



	public static function init() {
		add_action( 'add_to_taxonomy_notes', array( __CLASS__, 'taxonomy_notes_callback' ) );
		add_action( 'add_to_taxonomy_notes', array( __CLASS__, 'taxonomy_category_info' ) );
		add_action( 'add_to_taxonomy_notes', array( __CLASS__, 'taxonomy_post_tag_info' ) );
		add_action( 'add_to_taxonomy_notes', array( __CLASS__, 'taxonomy_user_badges_info' ) );
		add_action( 'add_to_taxonomy_notes', array( __CLASS__, 'taxonomy_faq_section_info' ) );
	}

	/**
	 * Add add_action() and add_filter() in this method.
	 */
	public static function show_some_info() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		echo '<div class="taxonomy-notes>';
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
		do_action( 'add_to_taxonomy_notes' );
		echo '</div>';
	}

	/**
	 * Register and add settings
	 */
	public static function taxonomy_notes_callback() {
		echo '<div class="taxonomy-notes>';
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		self::show_sample_taxonomies_for_taxonomy_notes();
		echo '</div>';
	}

	/**
	 * Register and add settings
	 */
	public static function taxonomy_post_tag_info() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		echo '<div class="taxonomy-notes>';
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		self::show_post_tag_for_taxonomy_notes();
		self::show_post_tag_taxonomy_terms();
		echo '</div>';
	}

	/**
	 * Register and add settings
	 */
	public static function taxonomy_user_badges_info() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		echo '<div class="taxonomy-notes>';
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		self::show_user_badges_for_taxonomy_notes();
		self::show_user_badges_taxonomy_terms();
		echo '</div>';
	}

	/**
	 * Register and add settings
	 */
	public static function taxonomy_faq_section_info() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		echo '<div class="taxonomy-notes>';
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		self::show_faq_section_for_taxonomy_notes();
		self::show_faq_section_taxonomy_terms();
		echo '</div>';
	}

	public static function show_sample_taxonomies_for_taxonomy_notes() {
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

	public static function show_wp_global_filters_for_taxonomy_notes() {
		global $wp_filter;
		$comment_filters = array();
		$h1  = '<h1>Current Comment Filters</h1>';
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

		print "$h1$toc</ul>$out";
	}

	/**
	 * Register and add settings
	 */
	public static function taxonomy_category_info() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		echo '<div class="taxonomy-notes>';
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		self::show_category_for_taxonomy_notes();
		echo '</div>';
	}


	public static function show_category_for_taxonomy_notes() {

		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		self::show_category_taxonomy_terms();

		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		$category = get_taxonomy( 'category' );

		if ( isset( $_REQUEST['action'] ) && 'show_category' === $_REQUEST['action'] ) {
			echo '<h4>To hide Categories Object <a href="' . esc_url( remove_query_arg( 'action' ) ) . '"><button>Click Here</button></a></h4>';
			echo '<pre>$category ';
			print_r( $category );
			echo '</pre>';
		} else {
			echo '<h4>To show Categories Object <a href="' . esc_url( add_query_arg( 'action', 'show_category' ) ) . '"><button>Click Here</button></a></h4>';
		}
	}

	public static function show_category_terms_for_taxonomy_terms() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		$terms = get_terms(
			array(
				'taxonomy' => 'category',
				'hide_empty' => false,
			)
		);
		if ( isset( $_REQUEST['action'] ) && 'show_category_terms' === $_REQUEST['action'] ) {
			echo '<h4>To hide Category Terms <a href="' . esc_url( remove_query_arg( 'action' ) ) . '"><button>Click Here</button></a></h4>';
			echo '<pre>show_category_terms ';

			if ( ! empty( $terms ) ) {
				print_r( $terms );
			} else {
				echo '<h3 style="color:salmon;">Empty Taxonomy Terms</h3>';
			}

			echo '</pre>';
		} else {
			echo '<h4>To show Category Terms <a href="' . esc_url( add_query_arg( 'action', 'show_category_terms' ) ) . '"><button>Click Here</button></a></h4>';
		}
	}

	public static function show_category_taxonomy_terms() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		$show_category_list = get_terms(
			array(
				'taxonomy' => 'category',
				'hide_empty' => false,
			)
		);
		if ( isset( $_REQUEST['action'] ) && 'show_category_list' === $_REQUEST['action'] ) {
			echo '<h4>To hide Categories List <a href="' . esc_url( remove_query_arg( 'action' ) ) . '"><button>Click Here</button></a></h4>';
			echo '<pre>$show_category_list ';
			foreach ( $show_category_list as $key => $value ) {
				echo '<br>' . $value->name . ' | ' . $value->slug . ' | ' . $value->term_id;
			}
			echo '</pre>';
		} else {
			echo '<h4>To show Categories List <a href="' . esc_url( add_query_arg( 'action', 'show_category_list' ) ) . '"><button>Click Here</button></a></h4>';
		}
	}


	public static function show_post_tag_for_taxonomy_notes() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		$post_tag = get_taxonomy( 'post_tag' );

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

	public static function show_user_badges_for_taxonomy_notes() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		$user_badges = get_taxonomy( 'user_badges' );

		if ( isset( $_REQUEST['action'] ) && 'show_user_badges' === $_REQUEST['action'] ) {
			echo '<h4>To hide User Badges Object <a href="' . esc_url( remove_query_arg( 'action' ) ) . '"><button>Click Here</button></a></h4>';
			echo '<pre>$user_badges ';
			print_r( $user_badges );
			echo '</pre>';
		} else {
			echo '<h4>To show User Badges Object <a href="' . esc_url( add_query_arg( 'action', 'show_user_badges' ) ) . '"><button>Click Here</button></a></h4>';
		}
	}

	public static function show_faq_section_for_taxonomy_notes() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		$faq_section = get_taxonomy( 'faq_section' );

		if ( isset( $_REQUEST['action'] ) && 'show_faq_section' === $_REQUEST['action'] ) {
			echo '<h4>To hide FAQ section Object <a href="' . esc_url( remove_query_arg( 'action' ) ) . '"><button>Click Here</button></a></h4>';
			echo '<pre>$faq_section ';
			print_r( $faq_section );
			echo '</pre>';
		} else {
			echo '<h4>To show FAQ section Object <a href="' . esc_url( add_query_arg( 'action', 'show_faq_section' ) ) . '"><button>Click Here</button></a></h4>';
		}
	}

	public static function show_user_badges_terms_for_taxonomy_terms() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		$terms = get_terms(
			array(
				'taxonomy' => 'user_badges',
				'hide_empty' => false,
			)
		);
		if ( isset( $_REQUEST['action'] ) && 'show_user_badges_terms' === $_REQUEST['action'] ) {
			echo '<h4>To hide User Badges Terms <a href="' . esc_url( remove_query_arg( 'action' ) ) . '"><button>Click Here</button></a></h4>';
			echo '<pre>show_user_badges_terms ';

			if ( ! empty( $terms ) ) {
				print_r( $terms );
			} else {
				echo '<h3 style="color:salmon;">Empty Taxonomy Terms</h3>';
			}

			echo '</pre>';
		} else {
			echo '<h4>To show User Badges Terms <a href="' . esc_url( add_query_arg( 'action', 'show_user_badges_terms' ) ) . '"><button>Click Here</button></a></h4>';
		}
	}

	public static function show_user_badges_taxonomy_terms() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		$show_user_badges_list = get_terms(
			array(
				'taxonomy' => 'user_badges',
				'hide_empty' => false,
			)
		);
		if ( isset( $_REQUEST['action'] ) && 'show_user_badges_list' === $_REQUEST['action'] ) {
			echo '<h4>To hide User Badges List <a href="' . esc_url( remove_query_arg( 'action' ) ) . '"><button>Click Here</button></a></h4>';
			echo '<pre>$show_user_badges_list ';
			foreach ( $show_user_badges_list as $key => $value ) {
				echo '<br>' . $value->name . ' | ' . $value->slug . ' | ' . $value->term_id;
			}
			echo '</pre>';
		} else {
			echo '<h4>To show User Badges List <a href="' . esc_url( add_query_arg( 'action', 'show_user_badges_list' ) ) . '"><button>Click Here</button></a></h4>';
		}
	}

	public static function show_faq_section_terms_for_taxonomy_terms() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		$terms = get_terms(
			array(
				'taxonomy' => 'faq_section',
				'hide_empty' => false,
			)
		);
		if ( isset( $_REQUEST['action'] ) && 'show_faq_section_terms' === $_REQUEST['action'] ) {
			echo '<h4>To hide FAQ Section Terms <a href="' . esc_url( remove_query_arg( 'action' ) ) . '"><button>Click Here</button></a></h4>';
			echo '<pre>show_faq_section_terms ';

			if ( ! empty( $terms ) ) {
				print_r( $terms );
			} else {
				echo '<h3 style="color:salmon;">Empty Taxonomy Terms</h3>';
			}

			echo '</pre>';
		} else {
			echo '<h4>To show FAQ Section Terms <a href="' . esc_url( add_query_arg( 'action', 'show_faq_section_terms' ) ) . '"><button>Click Here</button></a></h4>';
		}
	}

	public static function show_faq_section_taxonomy_terms() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		$show_faq_section_list = get_terms(
			array(
				'taxonomy' => 'faq_section',
				'hide_empty' => false,
			)
		);
		if ( isset( $_REQUEST['action'] ) && 'show_faq_section_list' === $_REQUEST['action'] ) {
			echo '<h4>To hide FAQ Section List <a href="' . esc_url( remove_query_arg( 'action' ) ) . '"><button>Click Here</button></a></h4>';
			echo '<pre>$show_faq_section_list ';
			foreach ( $show_faq_section_list as $key => $value ) {
				echo '<br>' . $value->name . ' | ' . $value->slug . ' | ' . $value->term_id;
			}
			echo '</pre>';
		} else {
			echo '<h4>To show FAQ Section List <a href="' . esc_url( add_query_arg( 'action', 'show_faq_section_list' ) ) . '"><button>Click Here</button></a></h4>';
		}
	}
}
Taxonomy_Admin_Notes::init();
