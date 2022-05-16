<?php
/**
 * Taxonomy Class
 *
 * @package    WordPress
 * @subpackage Phoenix_legacy_udf_export
 */

namespace Phoenix\Sample_Structured_Export\Collection;

use Phoenix\Sample_Structured_Export\Util\Graph_Id;
use Phoenix\Sample_Structured_Export\Component\Tag;

/**
 * Class Taxonomy
 */
class Taxonomy {




	/**
	 * Categories to not allow to be exported.
	 *
	 * @var array
	 */
	public static $filter_secondary_categories = array(
		'uncategorized',
		'article',
		'gallery',
		'babies',
	);

	/**
	 * Export Taxonomy.
	 *
	 * @param \WP_Post $post The Post.
	 * @param string   $brand The brand.
	 * @param array    $udf UDF array to return.
	 *
	 * @return array
	 */
	public static function export( $post, $brand, $udf ) {
		$graph_id         = new Graph_Id();
		$primary_category = (int) get_post_meta( $post->ID, 'primary_category', true );
		if ( empty( $primary_category ) ) {
			$primary_category = get_the_terms( $post->ID, 'category' );
		}
		if ( empty( $primary_category ) ) {
			return $udf;
		}

		$link = home_url( '/' . get_category_parents( (int) $primary_category, false, '/', true ) );

		$taxonomy = array(
			'_type'    => 'taxonomy',
			'category' => array(
				'id'     => (string) $primary_category,
				'title'  => $category->name,
				'href'   => $link,
				'entity' => array(
					'$id' => $graph_id->build_graph_id(
						array(
							'id'       => $primary_category,
							'brand'    => $brand,
							'instance' => 'sourcecms_terms',
						)
					),
				),
			),
		);

		$categories = (array) get_the_terms( $post->ID, 'category' );

		$categories = (array) get_the_terms( $post->ID, 'category' );
		// lets see if there is more than one category
		if ( count( $categories ) > 1 ) {
			foreach ( $categories as $key => $value ) {
				if ( $value->parent > 0 ) {
					$parents[] = $value->parent;
				}
				$taxonomy['category'] = array(
					'id'    => (string) $value->term_id,
					'title' => (string) $value->name,
					// 'parent' => (string) $value->parent,
					'href'  => (string) get_term_link( $value->term_id ),
				);
				$primary              = $value->term_id;
			}
			foreach ( $categories as $key => $value ) {
				if ( null === $parents || count( $categories ) > 2 ) {
					if ( $primary !== $value->term_id ) {
						$taxonomy['secondary_categories'][] = array(
							'id'    => (string) $value->term_id,
							'title' => (string) $value->name,
							// 'parent' => (string) $value->parent,
							'href'  => (string) get_term_link( $value->term_id ),
						);
						$second_cats[]                      = $value->term_id;
					}
				} elseif ( ! in_array( $value->term_id, $parents ) ) {
					// parent category
					$taxonomy['category'] = array(
						'id'    => (string) $value->term_id,
						'title' => (string) $value->name,
						// 'parent' => (string) $value->parent,
						'href'  => (string) get_term_link( $value->term_id ),
					);
				} else {
					// child category
					$taxonomy['secondary_categories'] = array(
						'id'    => (string) $value->term_id,
						'title' => (string) $value->name,
						// 'parent' => (string) $value->parent,
						'href'  => (string) get_term_link( $value->term_id ),
					);
				}
			}
		}

		// Get Tags for the content and load them into Taxonomy[tags] in UDF.
		$tags = wp_get_post_terms( $post->ID, 'post_tag' ) ?: [];  // @codingStandardsIgnoreLine

		if ( ! empty( $tags ) ) {
			$udf_tags         = array_map(
				function ( $tag ) use ( $brand ) {
					$udf          = array();
					$uuid         = get_term_meta( $tag->term_id, 'tempo_uuid_value', true );
					$hydrated_tag = Tag::export( $tag, $brand, $udf );
					$udf          = array(
						'id'     => (string) $tag->term_id,
						'title'  => $tag->name,
						'href'   => get_tag_link( $tag ), // @codingStandardsIgnoreLine
						'entity' => array(
							'$id' => $uuid ?: '',
						),
					);

					if ( ! empty( $hydrated_tag ) ) {
						$udf['entity']['udf'] = $hydrated_tag->udf;
					}

					return $udf;
				},
				$tags
			);
			$taxonomy['tags'] = $udf_tags;
		}

		$udf['taxonomy'] = $taxonomy;

		return $udf;
	}

	/**
	 * \WP_REST_Response
	 */
	public static function get_response_object( $request ) {
		return $request;
	}
}
