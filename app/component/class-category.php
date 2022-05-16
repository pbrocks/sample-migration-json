<?php
/**
 * Category Class
 *
 * @package    WordPress
 * @subpackage sample_structured_export
 */

namespace Phoenix\Sample_Structured_Export\Component;

use Phoenix\Sample_Structured_Export\Util\Graph_Id;

/**
 * Class Category
 */
class Category {

	/**
	 * Export Category information.
	 *
	 * @param \WP_Term $term The Term.
	 * @param string   $brand The brand.
	 * @param array    $udf UDF array to return.
	 *
	 * @return array
	 */
	public static function export( $term, $brand, $udf ) {
		$graph_id = new Graph_Id();

		$data = new \stdClass();

		$data->udf = $udf;

		$link = home_url( '/' . get_category_parents( $term->term_id, false, '/', true ) );

		if ( 'genre' === $term->taxonomy ) {
			$link = wpcom_vip_get_term_link( $term, 'genre' );
		}

		$data->udf = array(
			'_type'   => 'node-category',
			'variant' => 'default',
			'title'   => $term->name,
			// TO DO: For People/EW, this url may be pointing to the aggregate layout instead of the category term.
			'href'    => $link,
		);

		// Set type to unsupported for non-whitelisted taxonomy to prevent migration of unsupported terms.
		if ( ! in_array( $term->taxonomy, array( 'post_tag', 'category', 'creative-work', 'genre' ), true ) ) {
			$data->udf['_type'] = 'unsupported_term';
		}

		// If we're working with a term, then set parent category to tag.
		if ( 'genre' === $term->taxonomy ) {
			$term_parent = get_term_by( 'slug', 'genre', 'category' ); //@codingStandardsIgnoreLine
		}

		if ( $term->parent ) {
			$term_parent = get_term( $term->parent );
		}

		if ( $term_parent ) {
			$data->metadata         = new \stdClass();
			$data->metadata->parent = get_term_meta( $term_parent->term_id, 'tempo_uuid_value', true );
		}

		return $data;
	}
}
