<?php
/**
 * Tout Class
 *
 * @package    WordPress
 * @subpackage sample_structured_export
 */

namespace Phoenix\Sample_Structured_Export\Collection;

use Phoenix\Sample_Structured_Export\Component\Image;
use Phoenix\Sample_Structured_Export\Component as Component;

/**
 * Class Tout
 */
class Tout extends Component {




	/**
	 * Export tout information.
	 *
	 * @param \WP_Post $post The Post.
	 * @param string   $brand The brand.
	 * @param array    $udf UDF array to return.
	 *
	 * @return array
	 */
	public static function export( $post, $brand, $udf ) {
		$tout_information = get_post_meta( $post->ID, 'sample_tout_attributes', true );

		if ( ! empty( $tout_information['title'] ) ) {
			$udf['tout_headline'] = wp_kses( htmlspecialchars_decode( $tout_information['title'] ), self::$inline_html_tags );
		}

		if ( ! empty( $tout_information['description'] ) ) {
			$udf['tout_summary'] = wp_kses( htmlspecialchars_decode( $tout_information['description'] ), self::$inline_html_tags );
		}

		if ( ! empty( $tout_information['image'] ) ) {
			// Used to check if this is a valid image.
			$mime = get_post_mime_type( $tout_information['image'] );
			if ( ! empty( $mime ) ) {
				$udf['tout_image'] = ( new Image( get_post( $tout_information['image'] ) ) )->udf_image();
			}
		}

		return $udf;
	}
}
