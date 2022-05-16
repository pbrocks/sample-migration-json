<?php
/**
 * Term Class
 *
 * @package    WordPress
 * @subpackage sample_structured_export
 */

namespace Phoenix\Sample_Structured_Export\Component;

/**
 * Class Tag
 */
class Tag {

	/**
	 * Export Tag information.
	 *
	 * @param \WP_Term $term The Term.
	 * @param string   $brand The brand.
	 * @param array    $udf UDF array to return.
	 *
	 * @return array
	 */
	public static function export( $term, $brand, $udf ) {
		$data = new \stdClass();
		$data->udf = $udf;

		$data->udf = array(
			'_type'  => 'node-tag',
			'uuid'   => get_term_meta( $term->term_id, 'tempo_uuid_value', true ),
			'cms_id' => (string) $term->term_id,
			'brand'  => $brand,
			'title'  => $term->name,
			'href'   => get_term_link( $term ), //@codingStandardsIgnoreLine. VIP says to use get_term_link instead as wpcom_vip_get_term_link is deprecated.
		);

		return $data;
	}
}
