<?php
/**
 * Terms.
 *
 * @package    WordPress
 * @subpackage sample_structured_export
 */

namespace Phoenix\Sample_Structured_Export;

use Phoenix\Sample_Structured_Export\Util\Graph_Id;
use Phoenix\Sample_Structured_Export\Component\Category;
use Phoenix\Sample_Structured_Export\Component\Ad_Tag;
use Phoenix\Sample_Structured_Export\Component\Tag;
use Phoenix\Sample_Structured_Export\Component\Creative_Work;
use WP_Term;

/**
 * Class Term
 */
class Term {

	/**
	 * Term to get the data for.
	 *
	 * @var \WP_Term object
	 */
	public $term;

	/**
	 * The brand.
	 *
	 * @var string
	 */
	public $brand;

	/**
	 * Constructor function.
	 *
	 * @param object $term \WP_Term.
	 */
	public function __construct( $term ) {
		$this->brand = apply_filters( 'sample_structured_export_brand_name', 'sample' );
		$this->term = $term;
	}

	/**
	 * Get UDF data for term based on term type.
	 *
	 * @return object $data
	 */
	public function prepare() {
		$data = new \stdClass();

		// UDF data for single term.
		$udf           = array();
		$udf['uuid']   = get_term_meta( $this->term->term_id, 'tempo_uuid_value', true );
		$udf['brand']  = $this->brand;
		$udf['cms_id'] = (string) $this->term->term_id;

		switch ( $this->term->taxonomy ) {
			case 'creative-work':
				$data = Creative_Work::export( $this->term, $this->brand, $udf );
				break;
			case 'post_tag':
				$data = Tag::export( $this->term, $this->brand, $udf );
				break;
			case 'private_tag':
				$data = Ad_Tag::export( $this->term, $this->brand, $udf );
				break;
			default:
				$data = Category::export( $this->term, $this->brand, $udf );
				break;
		}

		$data->udf = array_merge( $udf, $data->udf );

		return $data;
	}
}
