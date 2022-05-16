<?php
/**
 * Primary Media.
 *
 * @package    WordPress
 * @subpackage sample_structured_export
 */

namespace Phoenix\Sample_Structured_Export\Collection;

use Phoenix\Sample_Structured_Export\Component\Video;
use Phoenix\Sample_Structured_Export\Component\Image;

/**
 * Class Primary_Media
 */
class Primary_Media {



	/**
	 * Export featured image.
	 *
	 * @param \WP_Post $post The Post.
	 * @param string   $brand The brand.
	 * @param array    $udf UDF array to return.
	 *
	 * @return array
	 */
	public static function export( $post, $brand, $udf ) {
		if ( 'gallery' === $post->post_type ) {
			// If gallery, use intro_slide_image as Primary media.
			$featured_image_id = get_post_meta( $post->ID, 'intro_slide_image', true );
		} else {
			// Otherwise use Featured Image as Primary media.
			$featured_image_id = get_post_meta( $post->ID, '_thumbnail_id', true );
		}

		if ( $featured_image_id ) {
			$featured_image = get_post( $featured_image_id );

			if ( $featured_image ) {
				$udf['primary_media'] = ( new Image( $featured_image ) )->udf_image();

				return $udf;
			}
		}

		// If the first thing in the post content is a video promote
		// that video to primary_media.
		$video = static::get_primary_video( $udf );
		if ( ! empty( $video ) ) {
			$udf['primary_media'] = $video;

			if ( 'recap' !== $post->post_type && 'gallery' !== $post->post_type ) {
				$udf['variant'] = 'video';
			}

			unset( $udf['body'][0] );
			$udf['body'] = array_values( $udf['body'] );
		}

		// Override video variant if there is a creative work. Wins over all other variants.
		$creative_works = get_the_terms( $post->ID, 'creative-work' );

		// Do not set variant on galleries.
		if ( ! is_wp_error( $creative_works ) && ! empty( $creative_works ) && 'gallery' !== $post->post_type ) {
			$udf['variant'] = 'creative-work';
		}

		return $udf;
	}

	/**
	 * See if there is a video first.
	 *
	 * @param array $udf Post content.
	 * @return array $video
	 */
	protected static function get_primary_video( $udf ) {

		if ( 'video' === $udf['body'][0]['_type'] ) {
			return $udf['body'][0];
		}

		return array();
	}
}
