<?php
/**
 * Products Class
 *
 * @package    WordPress
 * @subpackage sample_structured_export
 */

namespace Phoenix\Sample_Structured_Export\Component;

use Phoenix\Sample_Structured_Export\Util\Graph_Id;
use Phoenix\Sample_Structured_Export\Component as Component;
use Phoenix\Sample_Structured_Export\Component\Image;

/**
 * Class Products
 */
class Products extends Component {

	/**
	 * Export Products information.
	 *
	 * @param \WP_Post $post The Post.
	 * @param string   $brand The brand.
	 * @param array    $udf UDF.
	 *
	 * @return \stdClass
	 */
	public static function export( $post, $brand, $udf = array() ) {
		$data = new \stdClass();

		$data->udf = $udf;

		$uuid = get_post_meta( $post->ID, 'tempo_uuid_value', true );

		$udf['_type'] = (string) 'node-commerce-sample-product';

		// collect all featured images if they exist
		$featured_images = array(
			get_post_thumbnail_id( $post->ID ),
			get_post_meta( $post->ID, 'featured_product_image_2', true ),
			get_post_meta( $post->ID, 'featured_product_image_3', true ),
		);

		// loop through all available images
		foreach ( $featured_images as $id ) {
			// get image metadata
			$image = get_post( $id );

			// check if image exists in database and add to array
			if ( ! empty( $image ) ) {
				$image_collection[] = ( new Image( $image ) )->udf_image();
			}
		}

		$data->udf['media'] = $image_collection;
		$data->udf['assigned_brands'] = array( $brand );
		$data->udf['description']  = (string) $post->post_content;
		$data->udf['product_name'] = (string) $post->post_title;
		$data->udf['slug']         = (string) $post->post_name;
		$data->udf['provided_by']  = (string) $brand;
		$data->udf['retailer']     = (string) 'n/a';
		$data->udf['size']         = (string) 'n/a';
		$data->udf['color']        = (string) 'n/a';

		$user_id = (int) $post->post_author;

		$hash_id = get_user_meta( $user_id, 'cam_hash_id', true );

		if ( ! empty( $hash_id ) ) {
			$data->udf['submitter_id'] = (string) 'user/' . $hash_id;
		}

		$data->udf['last_updated'] = (string) get_gmt_from_date( $post->post_modified, 'Y-m-d\TH:i:s\Z' );

		$data->udf['price']  = array(
			'value'    => (string) get_post_meta( $post->ID, 'product_price', true ),
			'currency' => (string) 'USD',
		);

		$data->udf['cta']      = (string) ( empty( get_post_meta( $post->ID, 'product_cta_text', true ) ) ) ? 'Buy Now' : get_post_meta( $post->ID, 'product_cta_text', true );

		$data->udf['price'] = array(
			'value' => (string) get_post_meta( $post->ID, 'product_price', true ),
			'currency' => (string) 'USD',
		);
		$data->udf['cta'] = (string) ( empty( get_post_meta( $post->ID, 'cta_text', true ) ) ) ? 'Buy Now' : get_post_meta( $post->ID, 'cta_text', true );
		$data->udf['in_stock'] = (bool) false;

		$data->udf = array_merge( $data->udf, $udf );

		return $data;
	}

	/**
	 * Prepare Products UDF and remove unnecessary default params
	 *
	 * @param object $post     Post object to be used
	 * @param string $brand    Associated brand to use
	 * @param array  $udf       UDF to manipulate
	 *
	 * @return array $udf      Modified UDF with removed properties
	 */
	public static function prepare_products_udf( $post, $brand, $udf ) {

		unset(
			$udf['brand'],
			$udf['summary'],
			$udf['legacy_cms_id'],
			$udf['url'],
			$udf['status'],
			$udf['headline'],
			$udf['display_date'],
			$udf['language'],
			$udf['uuid'],
			$udf['created_date'],
			$udf['publish_date'],
			$udf['comments'],
			$udf['last_updated'],
			$udf['last_optimized']
		);

		return $udf;
	}
}
