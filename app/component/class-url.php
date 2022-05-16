<?php
/**
 * URL.
 *
 * @package    WordPress
 * @subpackage sample_structured_export
 */

namespace Phoenix\Sample_Structured_Export\Component;

/**
 * Class URL
 *
 * @package Phoenix\Sample_Structured_Export\Components
 */
class URL {

	/**
	 * Current post object.
	 *
	 * @var \WP_Post;
	 */
	private $post;

	/**
	 * URL constructor.
	 *
	 * @param \WP_Post $post WordPress post object.
	 */
	public function __construct( $post ) {
		$this->post = $post;
	}

	/**
	 * Get URL component UDF.
	 *
	 * @return array
	 */
	public function get() {
		$parts = wp_parse_url( get_permalink( $this->post ) );
		if ( false === $parts ) {
			return array();
		}

		return array(
			'_type'  => 'url',
			'origin' => $parts['scheme'] . '://' . $parts['host'],
			'path'   => $parts['path'],
		);
	}
}
