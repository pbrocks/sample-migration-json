<?php
/**
 * Image_UDF.
 *
 * @package    WordPress
 * @subpackage sample_structured_export
 */

namespace Phoenix\Sample_Structured_Export\Component;

use Phoenix\Sample_Structured_Export\Component\Image;

/**
 * Class Image_UDF
 */
class Image_UDF {


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
	 * @return object
	 */
	public function rest_prepare_images() {
		return $this->image_udf();
	}

	/**
	 * UDF info for an image.
	 * This differs from the class-image.php as that is not a valid import type.
	 * This actually returns a UDF object.
	 */
	public function image_udf() {
		$object = new \stdClass();
		$data = ( new Image( $this->post ) )->rest_prepare_images();

		$data[0]['cms_id'] = $data[0]['id'];

		$uuid = get_post_meta( $this->post->ID, 'image_uuid', true );
		if ( ! empty( $uuid ) ) {
			$data[0]['uuid'] = $uuid;
		}

		$object->udf = $data[0];

		// Get the internal title.
		$internal_title = get_post_meta( $this->post->ID, 'internal_title', true );
		if ( ! empty( $internal_title ) ) {
			$object->udf['metadata']['internal_title'] = $internal_title;
		}

		// Get the metadata notes.
		$metadata_notes = wp_get_attachment_caption( $this->post->ID );
		if ( $metadata_notes ) {
			$object->udf['metadata_notes'] = $metadata_notes;
		}

		// Save the original image url.
		$object->udf['metadata']['original_src_url'] = $data[0]['original']['src'];

		// Get the uploaded on date.
		$object->udf['metadata']['uploaded_on'] = get_gmt_from_date( $this->post->post_date, 'Y-m-d\TH:i:s\Z' );

		// Get the image alias and update the url to the new alias.
		$image_alias = $this->get_image_alias();
		$object->udf['original']['src'] = $image_alias;

		return $object;
	}

	/**
	 * Get the image alias.
	 *
	 * @return string
	 */
	public function get_image_alias() {
		// Get option search and replace it.
		$image_search = esc_attr( get_option( 'sample_structured_export_image_alias_search' ) );
		$image_replace = esc_attr( get_option( 'sample_structured_export_image_alias_replace' ) );

		$image_url = wp_get_attachment_image_url( $this->post->ID, 'full' );

		// Only replace if there's an actual replace value in the option.
		if ( $image_replace ) {
			return str_replace( $image_search, $image_replace, $image_url );
		}

		// Nothing changed.
		return $image_url;
	}
}
