<?php
/**
 * Meta Class
 *
 * @package    WordPress
 * @subpackage sample_structured_export
 */

namespace Phoenix\Sample_Structured_Export\Collection;

use Phoenix\Sample_Structured_Export\Component\Image;

/**
 * Class Meta
 */
class Meta {




	/**
	 * Export Meta information.
	 *
	 * @param \WP_Post $post The Post.
	 * @param string   $brand The brand.
	 * @param array    $udf UDF array to return.
	 *
	 * @return array
	 */
	public static function export( $post, $brand, $udf ) {
		$do_not_syndicate = get_post_meta( $post->ID, 'sample_do_not_syndicate', true );
		if ( $do_not_syndicate ) {
			$syndicate = 'do-not-syndicate';
		} else {
			// determine whether we should syndicate content base of images' copyright setting, default to true.
			// https://github.com/TimeInc/tempo-core-plugins/blob/b0799cafc9f1202947ee92e1e641245a56fe01bc/tempo-pronto-1.5/controllers/posts.php#L213.
			$syndicate = apply_filters( 'pronto_should_syndicate', true, $post ) ? 'all' : 'do-not-syndicate';
		}

		$exclude_xml_sitemap = false;
		$exclude_xml = get_post_meta( $post->ID, 'caas_id', true );
		if ( $exclude_xml ) {
			$exclude_xml_sitemap = true;
		}

		$canonical = get_post_meta( $post->ID, 'canonical_url', true );
		if ( empty( $canonical ) ) {
			$canonical = get_permalink( $post );
		}

		$meta = array(
			'_type'              => 'meta',
			'canonical'          => $canonical,
			'content_syndicated' => $syndicate,
			'exclude_from_sitemap' => $exclude_xml_sitemap,
		);

		// Set the meta title.
		// Use Social Meta title tag otherwise use display headline.
		$title_tag = get_post_meta( $post->ID, '_meta_title', true );
		if ( ! empty( $title_tag ) ) {
			$meta['title'] = wp_strip_all_tags( $title_tag );
			$meta['social']['title'] = wp_strip_all_tags( $title_tag );
		} else {
			$display_headline = get_post_meta( $post->ID, 'rich_text_title', true );
			if ( ! empty( $display_headline ) ) {
				$meta['title'] = wp_strip_all_tags( $display_headline );
				$meta['social']['title'] = wp_strip_all_tags( $display_headline );
			}
		}

		// Set the Meta description.
		// NOTE: Currently configured for PESP. Use filter in PESP if changing this default.
		$meta_description = apply_filters( 'sample_structured_export_meta_description', get_post_meta( $post->ID, '_meta_description', true ) );
		if ( ! empty( $meta_description ) ) {
			$meta['description'] = wp_strip_all_tags( $meta_description );
			$meta['social']['description'] = wp_strip_all_tags( $meta_description );
		}

		// NOTE: This meta key will need to be changed if not used for ENT group.
		$open_graph = get_post_meta( $post->ID, 'sample_open_graph', true );

		if ( ! empty( $open_graph['title'] ) ) {
			$meta['og_title'] = wp_strip_all_tags( $open_graph['title'] );
			$meta['social']['title'] = wp_strip_all_tags( $open_graph['title'] );
		}

		if ( ! empty( $open_graph['image'] ) ) {
			$og_image = get_post( $open_graph['image'] );
			$meta['og_image'] = ( new Image( $og_image ) )->udf_image();
			$meta['social']['image'] = ( new Image( $og_image ) )->udf_image();
		}

		$external_source = get_post_meta( $post->ID, 'sample_external_source', true );
		if ( ! empty( $external_source['canonical_override'] ) ) {
			$meta['canonical'] = $external_source['canonical_override'];
		}
		if ( ! empty( $external_source['no_index'] ) && $external_source['no_index'] ) {
			$meta['robots'] = 'nofollow, noindex';
		}

		// Facebook news.
		// Set the value to false always for posts that didn't have the feature enabled.
		$meta['facebook_news_opinion'] = false;
		$meta['facebook_news_blacklist'] = false;

		$facebook_news = get_post_meta( $post->ID, 'sample_fm_facebook_news', true );

		if ( $facebook_news ) {
			$meta['facebook_news_opinion']   = 'yes' === $facebook_news['article_opinion'] ? true : false;
			$meta['facebook_news_blacklist'] = 'yes' === $facebook_news['blacklist_fb'] ? true : false;
		}

		// Affiliate Link Count.
		$post_content                    = do_shortcode( $post->post_content );
		$affiliate_product_count         = preg_match_all( '~data-tracking-affiliate-network-name~', $post_content, $matches );
		$meta['affiliate_product_count'] = (int) $affiliate_product_count;

		// Affiliate Disclaimer.
		$meta['affiliate_disclaimer'] = (bool) $affiliate_product_count;

		$udf['meta'] = $meta;

		return $udf;
	}
}
