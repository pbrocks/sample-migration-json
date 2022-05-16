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
class Graph_Id {




	/**
	 * Build a content graph ID.
	 *
	 * @param array $args The array of arguments used to build the graph ID.
	 *
	 * @return string
	 */
	public function build_graph_id( array $args ) : string {
		// Build the arguments.
		$defaults = array(
			'id'       => '',
			'brand'    => '',
			'instance' => 'sourcecms_posts',
			'provider' => $this->get_udf_provider(),
		);

		$args = wp_parse_args( $args, $defaults );

		// Build the graph ID, given the arguments.
		$args     = apply_filters( 'tempo_sample_udf_build_graph_id', $args );
		$graph_id = sprintf( '%s/%s_%s_%s', $args['provider'], $args['instance'], $args['brand'], $args['id'] );
		return $graph_id;
	}

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
	 * Fetch the UUID by name.
	 *
	 * @param array $name term name.
	 *
	 * @return string
	 */
	public function tempo_get_uuid( $name ) {

		$pre_defined_uuid = array(
			'TV Show'     => '69e103a0-fa7b-11e9-8f0b-362b9e155667',
			'Movie'       => '69e1067a-fa7b-11e9-8f0b-362b9e155667',
			'Music'       => '69e107ce-fa7b-11e9-8f0b-362b9e155667',
			'Book'        => '69e108fa-fa7b-11e9-8f0b-362b9e155667',
			'Stage'       => '69e10a26-fa7b-11e9-8f0b-362b9e155667',
			'Video Games' => '69e10f44-fa7b-11e9-8f0b-362b9e155667',
			'Web Series'  => '69e110a2-fa7b-11e9-8f0b-362b9e155667',
			'NR'          => '2e5cc4f4-fa7b-11e9-8f0b-362b9e155667',
			'TV-Y'        => '2e5cc760-fa7b-11e9-8f0b-362b9e155667',
			'TV-Y7'       => '2e5cc8aa-fa7b-11e9-8f0b-362b9e155667',
			'TV-G'        => '2e5cccba-fa7b-11e9-8f0b-362b9e155667',
			'TV-14'       => '2e5cce2c-fa7b-11e9-8f0b-362b9e155667',
			'TV-MA'       => '2e5ccf58-fa7b-11e9-8f0b-362b9e155667',
			'Unrated'     => '40f8bf28-fa7b-11e9-aad5-362b9e155667',
			'G'           => '40f8c248-fa7b-11e9-aad5-362b9e155667',
			'PG'          => '40f8c3a6-fa7b-11e9-aad5-362b9e155667',
			'PG-13'       => '40f8c4dc-fa7b-11e9-aad5-362b9e155667',
			'R'           => '40f8c612-fa7b-11e9-aad5-362b9e155667',
			'NC-17'       => '40f8c73e-fa7b-11e9-aad5-362b9e155667',
			'X'           => '40f8cad6-fa7b-11e9-aad5-362b9e155667',
			'In Season'   => '7fd5c43a-fa7a-11e9-8f0b-362b9e155667',
			'On Hiatus'   => '7fd5cc00-fa7a-11e9-8f0b-362b9e155667',
			'Pending'     => '7fd5cd9a-fa7a-11e9-8f0b-362b9e155667',
			'Cancelled'   => '7fd5ceee-fa7a-11e9-8f0b-362b9e155667',
			'Off Air'     => '7fd5d01a-fa7a-11e9-8f0b-362b9e155667',
			// generated using https://uuid.meredithcorp.io/.
			'A+'          => '4148ecef-2a36-48e2-8324-7a38e9c8f3eb',
			'A'           => 'd0408993-0dfd-43d9-b94c-529ebc4fed69',
			'A-'          => '918e6f41-108e-4023-a89e-3101664573b0',
			'B+'          => '927a17bd-80d1-426c-b45a-101f9a824d53',
			'B'           => 'bdd6c0ec-f301-4996-a63f-707d56a3f52d',
			'B-'          => 'b9ed4139-69ed-48ca-a4ef-45eef33899dc',
			'C+'          => '6c260125-f051-4fa8-9cca-a4204890cb05',
			'C'           => '26018a97-8299-44ae-a10d-90ae6277844a',
			'C-'          => '1e848757-cbda-48a6-bb29-1ac4c2227150',
			'D+'          => '0c24a66b-541d-4e98-9231-7e51158b7c0c',
			'D'           => '23a18f3f-f90b-40f6-b10a-80ff53912cc9',
			'D-'          => 'e78155e2-4e94-4936-b028-f46c6290497d',
			'F'           => '6f24e59c-c581-4124-b2fa-161b7acfb1c4',
		);

		$term      = get_term_by( 'slug', $name, 'post_tag' ); // @codingStandardsIgnoreLine.

		return ( empty( $term ) || is_wp_error( $term ) ) ? $pre_defined_uuid[ $name ] : get_term_meta( $term->term_id, 'tempo_uuid_value', true );
	}
}
