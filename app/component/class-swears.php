<?php
/**
 * Swears Class
 *
 * @package    WordPress
 * @subpackage sample_structured_export
 */

namespace Phoenix\Sample_Structured_Export\Component;

use Phoenix\Sample_Structured_Export\Util\Graph_Id;
use Phoenix\Sample_Structured_Export\Collection\Body_Parser;
use Phoenix\Sample_Structured_Export\Component as Component;

/**
 * Class Swears
 */
class Swears extends Component {

	/**
	 * Export Swears information.
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

		$data->udf['_type'] = 'user-sample';

		$data->udf = array_merge( $data->udf, $udf );

		return $data;
	}
}
