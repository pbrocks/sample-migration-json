<?php
/**
 * Main Pull_Request_Reviews File
 *
 * @package    WordPress
 * @subpackage sample_structured_export
 */

namespace Phoenix\Sample_Structured_Export\Admin;

/**
 * Class Pull_Request_Reviews
 */
class Pull_Request_Reviews {




	public static function init() {
		// add_action( 'add_to_pull_request_notes', array( __CLASS__, 'pull_request_notes_callback' ) );
		add_action( 'add_to_pull_request_notes', array( __CLASS__, 'sample_structured_export_pr_4' ) );
		add_action( 'add_to_pull_request_notes', array( __CLASS__, 'some_pull_request_info_1' ) );
	}

	/**
	 * Add add_action() and add_filter() in this method.
	 */
	public static function notes_hook() {
		echo '<div class="add-to-sample-dash">';
		do_action( 'add_to_pull_request_notes' );
		echo '</div>';
	}

	/**
	 * Register and add settings
	 */
	public static function pull_request_notes_callback() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
		echo '<div>';
		self::show_sample_pull_request_notes();
		echo '</div>';
	}

	/**
	 * Register and add settings
	 */
	public static function sample_structured_export_pr_4() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		echo '<div>';
		self::show_sample_pull_req_notes();
		echo '</div>';
	}

	public static function show_sample_pull_req_notes() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		$pr_link = 'https://github.com/MeredithCorp/sample-structured-export/pull/4';

		$ticket = 'https://jira.meredith.com/browse/SWRB-10';
		echo 'Dev: Leo<br>';
		echo " <a href=\"$ticket\" target=\"_blank\">$ticket</a><br> ";
		echo " <a href=\"$pr_link\" target=\"_blank\">$pr_link</a> <br>";
		echo '<br>';
		echo 'Sample user_commerce_product_example';
		echo Admin_Page_Additions::user_commerce_product_example();
		echo '<br>';
	}

	public static function show_sample_pull_request_notes() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		echo '<p class="description">Purpose of this admin page is to display the important Post Types and their respective Taxonomies found in Sample.</p>';
		$args = array(
			'public'   => true,
			'_builtin' => false,
		);

		$pull_requests = get_post_types( $args );

		echo '<h3 style="color:green;">Current Filter/Action ' . current_filter() . '</h3>';

		if ( isset( $_REQUEST['action'] ) && 'show_sample_pull_request' === $_REQUEST['action'] ) {
			echo '<h4>To hide Sample Post Types <a href="' . esc_url( remove_query_arg( 'action' ) ) . '"><button>Click Here</button></a></h4>';
			echo '<pre>show_sample_pull_request ';

			if ( ! empty( $pull_requests ) ) : ?>
			<ul>
					<?php
					foreach ( $pull_requests as $pull_request ) {
						echo '<li>' . $pull_request . '</li>';
					}
					?>
			</ul>
				<?php
			endif;
			echo '</pre>';
		} else {
			echo '<h4>To show Sample Post Types <a href="' . esc_url( add_query_arg( 'action', 'show_sample_pull_request' ) ) . '"><button>Click Here</button></a></h4>';
		}
	}


	/**
	 * Register and add settings
	 */
	public static function some_pull_request_info_1() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		echo '<div>';
		self::show_stories_pull_request_notes();
		echo '</div>';
	}

	public static function show_stories_pull_request_notes() {
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . ' <span style="font-size:70%;color:salmon;">' . basename( __FILE__ ) . ' | ' . __LINE__ . '</span></h3>';
		echo '<h3>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h3>';
	}
}
Pull_Request_Reviews::init();
