<?php
/**
 * Stories Class
 *
 * @package    WordPress
 * @subpackage sample_structured_export
 */

namespace Phoenix\Sample_Structured_Export\Component;

use Phoenix\Sample_Structured_Export\Component\Image;
use Phoenix\Sample_Structured_Export\Component as Component;
use Phoenix\Sample_Structured_Export\Util\Generate_UUID;

/**
 * Class Stories
 */
class Stories extends Component {

	/**
	 * Export Stories information.
	 *
	 * @param \WP_Post $post The Post.
	 * @param string   $brand The brand.
	 * @param array    $udf UDF.
	 *
	 * @return \stdClass
	 */
	public static function export( $post, $brand, $udf = array() ) {
		$data = new \stdClass();

		$data->cms_instance = 'sourcecms_posts';

		$data->udf = $udf;

		// top content
		$data->udf = array(
			'_type'        => 'content-type-longform',
			'headline'     => $post->post_title,
			'summary'      => wp_kses( htmlspecialchars_decode( $post->post_content ), self::$inline_html_tags ),
			'header_layout' => 'full-width',
			'status'       => 'publish' === $post->post_status ? 'published' : $post->post_status,
		);

		if ( in_array( $post->post_status, array( 'ready-for-edit', 'pending', 'hold-do-not-pub', 'hold-missing-assets', 'private' ), true ) ) {
			$data->udf['status'] = 'draft';
		}

		$item_uuid = get_post_meta( $post->ID, 'listicle_uuid_value', true );
		if ( empty( $item_uuid ) ) {
			$item_uuid = Generate_UUID::fetch_uuid();
			update_post_meta( $post->ID, 'listicle_uuid_value', $item_uuid );
		}

		// Body Swears
		$udf = array();
		$story_info = array(
			'_type' => 'listicle',
			'uuid' => $item_uuid,
			'variant' => 'separated',
		);
		$story_swears = get_field( 'story_swears', $post->ID );
		foreach ( $story_swears as $swears ) {
			$swear_image = get_post( $swears['swear_image']['ID'] );
			$swear_image = ( new Image( $swear_image ) )->udf_image();

			$product_id = get_post_meta( $swears['swear'], 'product-swear', true );
			$product_id = $product_id[0];

			$story_info['slides'][] = array(
				'type' => 'image',
				'headline' => $swears['swear_title'],
				'summary' => wp_kses( html_entity_decode( $swears['swear_text'] ), self::$block_html_tags ),
				'link' => get_permalink( $product_id ),
				'item_hash' => $swears['photo_credit'],
				'components' => array(
					$swear_image,
				),
			);
		}
		$udf['body'][] = $story_info;

		// intro image for story information
		$intro_image = get_field( 'story_intro_image', $post->ID );
		if ( '' != $intro_image && null != $intro_image ) {
			$intro_image = get_post( $intro_image['ID'] );
			$intro_image = ( new Image( $intro_image ) )->udf_image();
			$udf['body'][] = $intro_image;
		}

		// Who is this story written about
		$user_id = get_field( 'story_written_about', $post->ID );
		if ( '' != $user_id && null != $user_id ) {
			$user_obj = get_userdata( $user_id );
			$avatar_id = $user_obj->user_avatar;
			$about['_type'] = 'image-reference';
			$about['headline'] = $user_obj->display_name;
			$about['link'] = $avatar_id ? wp_get_attachment_url( $avatar_id ) : get_avatar_url( $user_id, array( 'size' => '240' ) );
			$udf['body'][] = $about;
		}

		// author details
		$author = new \WP_User( $post->post_author );
		if ( $author->exists() ) {
			$udf['byline'] = $author->display_name;
			$udf['bylines']['primary']['override'] = $author->display_name;
		}

		// Associated Editors
		$associated_editors = get_field( 'associated_editors', $post->ID );
		if ( is_array( $associated_editors ) ) {
			$editors = array();
			foreach ( $associated_editors as $editor ) {
				$user_obj = get_user_by( 'id', $editor );

				$editors[] = $user_obj->display_name;
			}
			$udf['bylines']['secondary']['override'] = implode( ', ', $editors );
		}

		$data->udf = array_merge( $data->udf, $udf );

		unset( $story_info, $story_swears, $author, $swear_image, $product_id, $user_id, $user_obj, $about, $intro_image, $associated_editors, $editors );

		return $data;
	}

	/**
	 * Longform Stories information.
	 *
	 * @param \WP_Post $post The Post.
	 * @param string   $brand The brand.
	 * @param array    $udf UDF.
	 *
	 * @return \stdClass
	 */
	public static function prepare_longform_udf( $post, $brand, $udf = array() ) {
		$url = ( new URL( $post ) )->get();

		// meta for SEO
		$udf_new['meta']['domain'] = $url['origin'];
		$udf_new['meta']['path'] = $url['path'];
		$udf_new['meta']['og_url'] = $url['origin'] . $url['path'];
		if ( get_field( 'seo_page_title', $post->ID ) ) {
			$udf_new['meta']['title'] = get_field( 'seo_page_title', $post->ID );
		}
		if ( get_field( 'seo_page_description', $post->ID ) ) {
			$udf_new['meta']['description'] = get_field( 'seo_page_description', $post->ID );
		}
		if ( get_field( 'seo_og_title', $post->ID ) ) {
			$udf_new['meta']['og_title'] = get_field( 'seo_og_title', $post->ID );
		}
		if ( get_field( 'seo_og_description', $post->ID ) ) {
			$udf_new['meta']['og_description'] = get_field( 'seo_og_description', $post->ID );
		}
		if ( get_field( 'seo_twitter_title', $post->ID ) ) {
			$udf_new['meta']['social']['title'] = get_field( 'seo_twitter_title', $post->ID );
		}
		if ( get_field( 'seo_twitter_description', $post->ID ) ) {
			$udf_new['meta']['social']['description'] = get_field( 'seo_twitter_description', $post->ID );
		}

		$udf['meta'] = array_merge( $udf['meta'], $udf_new['meta'] );

		unset( $udf['comments'], $udf['pronto_entity'], $udf_new );

		return $udf;
	}
}
