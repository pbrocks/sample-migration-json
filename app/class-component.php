<?php
/**
 * Component
 *
 * @package    WordPress
 * @subpackage sample_structured_export
 */

namespace Phoenix\Sample_Structured_Export;

/**
 * Class Component
 */
class Component {


	/**
	 * Inline HTML Tags allowed
	 *
	 * @var array
	 */
	public static $inline_html_tags = array(
		'em'     => array(),
		'i'      => array(),
		'strong' => array(),
		'b'      => array(),
		'a'      => array(
			'href'  => array(),
			'rel'   => array(),
			'title' => array(),
		),
	);

	/**
	 * Block HTML Tags allowed
	 *
	 * @var array
	 */
	public static $block_html_tags = array(
		'em'     => array(),
		'i'      => array(),
		'strong' => array(),
		'b'      => array(),
		'a'      => array(
			'href'  => array(),
			'rel'   => array(),
			'title' => array(),
		),
		'h2' => array(),
		'h3' => array(),
		'h4' => array(),
		'h5' => array(),
		'h6' => array(),
		'p'  => array(),
		'ul' => array(),
		'ol'  => array(),
		'li' => array(),
		'br' => array(),
		'hr' => array(),
	);
}
