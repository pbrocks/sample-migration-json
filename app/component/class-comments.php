<?php
/**
 * Comments.
 *
 * @package    WordPress
 * @subpackage sample_structured_export
 */

namespace Phoenix\Sample_Structured_Export\Component;

/**
 * Class Comments
 */
class Comments {

	/**
	 * Current post object.
	 *
	 * @var \WP_Post;
	 */
	private $post;

	/**
	 * Comments constructor.
	 *
	 * @param \WP_Post $post WordPress post object.
	 */
	public function __construct( $post ) {
		$this->post = $post;
	}

	/**
	 * Get Comments component UDF.
	 *
	 * @return array
	 */
	public function get() {
		return array(
			'_type'  => 'comments',
			'status' => apply_filters( 'sample_structured_export_comments_setting', $this->get_comments_setting() ) ? 'enabled' : 'disabled',
		);
	}

	/**
	 * Check if comments are open for this post
	 *
	 * @return bool Default false.
	 */
	public function get_comments_setting() {
		$comment_status = get_post_meta( $this->post->ID, 'content_comment_status', true );
		switch ( $comment_status ) {
			case 'open':
				return true;
				break;
			case 'closed':
				return false;
				break;
			default:
				$primary_category = get_post_meta( $this->post->ID, 'primary_category', true );
				if ( $primary_category ) {
					if ( 'open' === get_term_meta( $primary_category, 'default_category_comment_status', true ) ) {
						return true;
					}
					return false;
				}
				break;
		}
		return false;
	}
}
