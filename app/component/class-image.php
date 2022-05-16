<?php
/**
 * Images.
 *
 * @package    WordPress
 * @subpackage sample_structured_export
 */

namespace Phoenix\Sample_Structured_Export\Component;

use WP_Post;

/**
 * Class Image
 */
class Image {

	/**
	 * Constructor function.
	 *
	 * @param object $post \WP_Post of the Image.
	 */
	public function __construct( $post ) {
		$this->post = $post;
	}

	/**
	 * UDF object for image post types.
	 *
	 * @return array
	 */
	public function rest_prepare_images() {
		return array(
			$this->udf_image(),
		);
	}

	/**
	 * Get Image data
	 *
	 * @return array image data.
	 */
	public function udf_image() {
		$data = array(
			'_type'    => 'image',
			'id'       => (string) $this->post->ID,
			'title'    => $this->post->post_title,
			'credit'   => $this->post->post_content,
		);

		$image_url = wp_get_attachment_image_url( $this->post->ID, 'full' );
		$image_src = wp_get_attachment_image_src( $this->post->ID, 'full' );

		if ( ! is_string( $image_url ) || empty( $image_src ) ) {
			return $data;
		}

		$data['original'] = array(
			'src'       => utf8_uri_encode( $image_url ),
			'width'     => $image_src[1],
			'height'    => $image_src[2],
			'mime_type' => get_post_mime_type( $this->post->ID ),
		);

		$filename = get_attached_file( $this->post->ID );
		if ( file_exists( $filename ) ) {
			$filesize = filesize( $filename );
			if ( $filesize ) {
				$data['original']['file_size'] = $filesize;
			}
		}

		$data['orientation'] = 'default';
		$data['filename']    = basename( $image_url );

		$alt = get_post_meta( $this->post->ID, '_wp_attachment_image_alt', true );
		// Set the alt to alt otherwise make it the title.
		$data['alt'] = $alt ?: $this->post->post_title;

		// For old People vignette images that do not have titles, set alt to Image.
		if ( ! $data['alt'] ) {
			$data['alt'] = 'Image';
		}

		$tempo_usage             = get_post_meta( $this->post->ID, 'tempo_usage', true );
		$data['rights']['usage'] = 'unknown'; // default to unkonwn.
		if ( ! empty( $tempo_usage ) && in_array( $tempo_usage, array( 'all-uses', 'time-inc-limited', 'brand-exclusive', 'unknown' ), true ) ) {
			switch ( $tempo_usage ) {
				case 'time-inc-limited':
					$data['rights']['usage'] = 'limited';
					break;
				default:
					$data['rights']['usage'] = $tempo_usage;
					break;
			}
		}
		$has_syndication_rights = get_post_meta( $this->post->ID, 'feed_has_syndication_rights', true );
		if ( 'false' === $has_syndication_rights ) {
			$data['rights']['usage'] = 'limited';
		}

		$licensor_name = get_post_meta( $this->post->ID, 'feed_licensor_name', true );
		if ( $licensor_name ) {
			$data['rights']['source'] = $licensor_name;
		}

		$license_id = get_post_meta( $this->post->ID, 'feed_license_id', true );
		if ( $license_id ) {
			$data['rights']['assignment_id'] = $licensor_name;
		}

		$asset_type = get_post_meta( $this->post->ID, 'tempo_asset_type', true );
		$data['rights']['asset_type'] = $asset_type;

		$override_crop = $this->get_override_crops( $this->post );
		if ( $override_crop ) {
			$data['crops'] = $override_crop;
		}
		return $data;
	}

	/**
	 * Figure out the crops and overrides for each parent image.
	 *
	 * @param WP_POST Object $post Parent image.
	 *
	 * @return array|null $crops.
	 */
	public function get_override_crops( $post ) {
		$overrides = get_post_meta( $post->ID, 'sample_thumbnail_override', true );

		// There are no override crop images.
		if ( ! $overrides ) {
			return;
		}

		// map the overrides keys to the aspect ratios.
		$rating1 = array(
			'large-square'      => 3,
			'medium-square'     => 2,
			'home-latest-story' => 1,
		);

		$rating2 = array(
			'more-stores'               => 2,
			'home-featured-four-across' => 1,
		);

		foreach ( $overrides as $key => $override_id ) {
			// Don't create crop for aspect_4x3 social share.
			if ( 'social-share' === $key ) {
				continue;
			}

			// Map the crop to the new UDF aspect ratio.
			switch ( $key ) {
				case 'large-square':
				case 'medium-square':
				case 'home-latest-story':
					$aspect = 'aspect_1x1';

					// Tracking whether or not to save the override image.
					if ( empty( $orig_saved['aspect_1x1'] ) ) {
						$orig_saved['aspect_1x1'] = $rating1[ $key ];
						$update = true;
					}

					// Is the current crop size larger than the one we saved?
					if ( $rating1[ $key ] > $orig_saved['aspect_1x1'] ) {
						$orig_saved['aspect_1x1'] = $rating1[ $key ];
						$update = true;
					} else {
						$update = false;
					}
					break;
				case 'more-stories':
				case 'home-featured-four-across':
					$aspect = 'aspect_3x2';

					// Tracking whether or not to save the override image.
					if ( empty( $orig_saved['aspect_3x2'] ) ) {
						$orig_saved['aspect_3x2'] = $rating2[ $key ];
						$update = true;
					}

					// Is the current crop size larger than the one we saved?
					if ( $rating2[ $key ] > $orig_saved['aspect_3x2'] ) {
						$orig_saved['aspect_3x2'] = $rating2[ $key ];
						$update = true;
					} else {
						$update = false;
					}
					break;
				case 'channel-tall':
					$aspect = 'aspect_1x2';
					break;
				case 'franchise-tall':
					$aspect = 'aspect_2x3';
					break;
			}

			$image_url     = wp_get_attachment_image_url( $override_id, 'full' );
			$override_post = get_post( $override_id );

			if ( ! empty( $image_url ) && ! empty( $override_post ) ) {
				$override_udf = ( new Image_UDF( $override_post ) )->rest_prepare_images();
				if ( empty( $crops[ $aspect ] ) ) {
					$crops[ $aspect ]  = array(
						'override_image' => array(
							'$id' => utf8_uri_encode( $image_url ),
							'udf' => $override_udf->udf,
						),
					);
				}

				// Only overwrite existing if larger version of the crop.
				if ( $update ) {
					$crops[ $aspect ]  = array(
						'override_image' => array(
							'$id' => utf8_uri_encode( $image_url ),
							'udf' => $override_udf->udf,
						),
					);
				}
			}
		}

		return $crops;
	}
}
