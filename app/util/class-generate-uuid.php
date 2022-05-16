<?php
/**
 * Content Graph.
 *
 * @package    WordPress
 * @subpackage sample_structured_export
 */

namespace Phoenix\Sample_Structured_Export\Util;

/**
 * Class Graph Id
 */
class Generate_UUID {



	/**
	 * Fetch the instance namespace.
	 *
	 * @return string
	 */
	public function get_udf_provider() : string {
		if ( in_array( get_home_url(), array( 'https://sample.com', 'https://sampleenespanol.com', 'https://ew.com' ), true ) ) {
			$environment = 'prod';
		} else {
			$environment = 'test';
		}

		if ( in_array( $environment, array( 'test', 'prod' ), true ) ) {
			return 'cms';
		}
		return "cms_{$environment}";
	}
	/**
	 * Create a UUID.
	 *
	 * http://www.seanbehan.com/how-to-generate-a-uuid-in-php/
	 *
	 * @param array $name term name.
	 *
	 * @return string
	 */
	public static function fetch_uuid() {
		$request = wp_remote_get( 'https://uuid.meredithcorp.io' );
		$response = wp_remote_retrieve_body( $request );
		$generated = json_decode( $response );
		return $generated->uuid;
	}

	/**
	 * Create a UUID.
	 *
	 * http://www.seanbehan.com/how-to-generate-a-uuid-in-php/
	 *
	 * @param array $name term name.
	 *
	 * @return string
	 */
	public static function create_uuid() {
		$data = random_bytes( 16 );
		$data[6] = chr( ord( $data[6] ) & 0x0f | 0x40 );
		$data[8] = chr( ord( $data[8] ) & 0x3f | 0x80 );
		return vsprintf( '%s%s-%s-%s-%s-%s%s%s', str_split( bin2hex( $data ), 4 ) );
	}
}
