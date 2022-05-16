<?php
/**
 * Category Class
 *
 * @package    WordPress
 * @subpackage sample_structured_export
 */

namespace Phoenix\Sample_Structured_Export\Component;

use Phoenix\Sample_Structured_Export\Util\Graph_Id;
use Phoenix\Sample_Structured_Export\Component\Tag;

/**
 * Class Category
 */
class Creative_Work {

	/**
	 * Export Category information.
	 *
	 * @param \WP_Term $cw_term The Term.
	 * @param string   $brand The brand.
	 * @param array    $udf UDF array to return.
	 *
	 * @return array
	 */
	public static function export( $cw_term, $brand, $udf ) {
		$data    = new \stdClass();
		$cw_id   = $cw_term->term_id;
		$cw_info = get_term_meta( $cw_id, 'sample_cw_settings', true ) ?: array();

		$udf = array(
			'_type'         => 'node-creative-work',
			'uuid'          => get_term_meta( $cw_id, 'tempo_uuid_value', true ),
			'title'         => $cw_term->name,
			'cms_id'        => (string) $cw_id,
			'legacy_cms_id' => (string) $cw_id,
			'description'   => $cw_term->description ? html_entity_decode( wp_strip_all_tags( $cw_term->description ), ENT_QUOTES ) : '',
		);

		$cw_info['type'] = $cw_info['type'] ?: 'TV Show';
		$allowed_cw_type = array( 'tv-show', 'movie', 'music', 'book', 'stage', 'video-games', 'web-series' );
		$cw_type         = strtolower( str_replace( ' ', '-', ew_format_cw_value( 'type', $cw_info['type'] ) ) );

		if ( in_array( $cw_type, $allowed_cw_type, true ) ) {
			$cw_type_id = Graph_Id::tempo_get_uuid( $cw_info['type'] );
			if ( ! empty( $cw_type_id ) ) {
				$udf['creative_work_type'] = array(
					'$id' => $cw_type_id,
					'udf' => array(
						'_type'  => 'node-tag',
						'uuid'   => $cw_type_id,
						'cms_id' => $cw_type_id,
						'brand'  => $brand,
						'title'  => $cw_info['type'],
					),
				);
			}
		} else {
			return $data;
		}

		if ( $cw_info['image'] ) {
			$udf['image'] = ( new Image( get_post( $cw_info['image'] ) ) )->udf_image();
		}

		$optional_dates = array(
			'release_date'         => 'release_date',
			'limited_release_date' => 'release_date_limited',
			'wide_release_date'    => 'release_date_wide',
			'publication_date'     => 'publication_date',
		);
		foreach ( $optional_dates as $id => $date_type ) {
			if ( ! empty( $cw_info[ $date_type ] ) ) {
				$udf[ $id ] = date( 'Y-m-d', $cw_info[ $date_type ] );
			}
		}

		if ( ! empty( $cw_info['run_date']['start'] ) ) {
			$udf['run_date_start'] = date( 'Y-m-d', $cw_info['run_date']['start'] );
			if ( ! empty( $cw_info['run_date']['end'] ) ) {
				$udf['run_date_end'] = date( 'Y-m-d', $cw_info['run_date']['end'] );
			}
		}

		$other_fields = array( 'runtime', 'seasons', 'episodes', 'tvpgr', 'mpaa', 'pages', 'television_status' );
		foreach ( $other_fields as $field ) {
			if ( in_array( $field, array( 'mpaa', 'tvpgr', 'television_status' ), true ) ) {
				if ( ! empty( $cw_info[ $field ] ) ) {
					$other_term_uuid = Graph_Id::tempo_get_uuid( $cw_info[ $field ] );
					if ( empty( $other_term_uuid ) ) {
						continue;
					}
					$udf[ $field ]   = array(
						'$id' => $other_term_uuid,
						'udf' => array(
							'_type'  => 'node-tag',
							'uuid'   => $other_term_uuid,
							'brand'  => $brand,
							'cms_id' => $other_term_uuid,
							'title'  => $cw_info[ $field ],
						),
					);
				}
			} else {
				if ( ! empty( $cw_info[ $field ] ) ) {
					$udf[ $field ] = $cw_info[ $field ];
				}
			}
		}

		if ( ! empty( $cw_info['air_day_time'] ) ) {
			$udf['air_day_time'] = ew_format_creative_work_value( 'air_day_time', $cw_info['air_day_time'] );
		}
		if ( ! empty( $cw_info['seasons'] ) ) {
			$udf['seasons'] = $cw_info['seasons'];
		}

		$stream_service      = get_term_meta( $cw_id, 'stream_service', true );
		$stream_service_link = get_term_meta( $cw_id, 'stream_service_link', true );
		if ( ! empty( $stream_service ) && ! empty( $stream_service_link ) ) {
			$udf['stream_service'] = array(
				array(
					'name' => $stream_service,
					'link' => $stream_service_link,
				),
			);
		}

		// handle sample taxonomies.
		if ( isset( $cw_info['genre_new'] ) ) {
			$cw_info['genre'] = $cw_info['genre_new'];
		}
		$cw_term_types = array( 'genre', 'director', 'network', 'producer_person', 'producer_group', 'music_group', 'performer', 'guest_performer', 'distributor', 'related', 'episode_type', 'creator', 'broadcaster', 'author', 'publisher', 'music_label' );

		foreach ( $cw_term_types as $cw_term_type ) {
			if ( ! empty( $cw_info[ $cw_term_type ] ) ) {
				$cw_terms = $cw_info[ $cw_term_type ];
				foreach ( $cw_terms as $term_id ) {
					$term = get_term( $term_id );

					if ( ! is_object( $term ) || is_wp_error( $term ) ) {
						continue;
					}

					$hydrated_tag = Tag::export( $term, $brand, $udf );
					$entity       = array(
						'entity' => array(
							'$id' => get_term_meta( $term_id, 'tempo_uuid_value', true ),
						),
					);

					if ( ! empty( $hydrated_tag ) ) {
						$entity['entity']['udf'] = $hydrated_tag->udf;
					}

					if ( 'music_group' === $cw_term_type ) {
						$udf[ $cw_term_type ] = $entity;
					} else {
						$udf[ $cw_term_type ][] = $entity;
					}
				}
			}
		}

		$data->udf = $udf;
		return $data;
	}
}
