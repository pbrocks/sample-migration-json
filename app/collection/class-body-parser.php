<?php
/**
 * Body Parser.
 *
 * @package    WordPress
 * @subpackage sample_structured_export
 */

namespace Phoenix\Sample_Structured_Export\Collection;

/**
 * Class Body Parser
 */
class Body_Parser {




	/**
	 * A Body_Parser instance.
	 *
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Body parser metrics.
	 *
	 * @var $metrics
	 */
	protected $metrics;

	/**
	 * Body_Parser constructor.
	 */
	private function __construct() {
	}

	/**
	 * Get body parser instance.
	 *
	 * @return Body_Parser A Body_Parser instance.
	 */
	public static function instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	/**
	 * Get the body parser stats.
	 *
	 * @return array
	 */
	public static function metrics() {
		return ( self::instance() )->metrics;
	}

	/**
	 * Export body.
	 *
	 * @param \WP_Post $new_post The Post.
	 * @param string   $brand The brand.
	 * @param array    $udf UDF array to return.
	 *
	 * @return array
	 */
	public static function export( $new_post, $brand, $udf ) {
		global $post;
		// @codingStandardsIgnoreStart
		$old_post = $post;
		$post = $new_post;

		// Disable pushtop video code for the export.
		remove_filter( 'the_content', 'ew_move_bottom_videos_to_top', 7 );

		$body = static::parse( $post->post_content, $brand );

		add_filter( 'the_content', 'ew_move_bottom_videos_to_top', 7 );
		$post = $old_post;
		// @codingStandardsIgnoreEnd

		if ( empty( $body ) ) {
			return $udf;
		}

		$udf['body'] = $body['body_parser_udf'];

		( self::instance() )->metrics = $body;

		return $udf;
	}

	/**
	 * Get UDF body content for post
	 *
	 * @param string $content Post content to parse.
	 * @param string $brand The brand.
	 *
	 * @return array $body
	 */
	private static function parse( $content, $brand ) {
		$parser_url = esc_url( get_option( 'sample_structured_export_body_parser_url', 'https://dev-udf-body-parser.cms.meredithcorp.io/' ) );

		// protected-iframe embed gets filtered incorrectly. so use original value.
		if ( function_exists( 'wpcom_vip_protected_embed_to_original' ) ) {
			$content = wpcom_vip_protected_embed_to_original( $content );
		}
		$content = apply_filters( 'the_content', $content );

		$cdn_host = home_url();
		if ( in_array( $cdn_host, array( 'https://sample.com', 'https://sampleenespanol.com', 'https://ew.com' ), true ) ) {
			switch ( $brand ) {
				case 'sample':
					$cdn_host = 'https://sampledotcom.files.wordpress.com';
					break;
				case 'ew':
					$cdn_host = 'https://ewedit.files.wordpress.com';
					break;
				case 'sampleenespanol':
					$cdn_host = 'https://pespdotcom.files.wordpress.com';
					break;
			}
		}

		$header = array(
			'content-type'  => 'text/plain',
			'report'        => 'true',
			'content-brand' => $brand,
			'default-host'  => home_url(),
			'default-cdn'   => $cdn_host,
		);

		$parser_response = wp_remote_post(
			$parser_url,
			array(
				'method'      => 'POST',
				'timeout'     => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking'    => true,
				'body'        => $content,
				'cookies'     => array(),
				'headers'     => $header,
			)
		);

		// If we have anything other than a 200 return an empty array.
		if ( is_wp_error( $parser_response ) || 200 !== absint( $parser_response['response']['code'] ) ) {
			return array();
		}

		$parser_body = wp_remote_retrieve_body( $parser_response );

		// If there is an error, make sure we return an empty array.
		if ( is_wp_error( $parser_body ) ) {
			return array();
		}

		return json_decode( $parser_body, true );
	}
}
