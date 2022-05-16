<?php
/**
 * Authors Class
 *
 * @package Phoenix\Sample_Structured_Export\Component
 */

namespace Phoenix\Sample_Structured_Export\Component;

/**
 * Class Authors
 */
class Authors {

	/**
	 * WordPress post object.
	 *
	 * @var \WP_Post
	 */
	protected $post;

	/**
	 * Authors constructor.
	 *
	 * @param \WP_Post $post WordPress post object.
	 */
	public function __construct( \WP_Post $post ) {
		$this->post = $post;
	}

	/**
	 * Build UDF for authors component.
	 *
	 * @return array
	 */
	public function get() : array {

		if ( ! function_exists( 'get_coauthors' ) ) {
			return array();
		}

		$udf = array(
			'_type' => 'authors',
			'authors' => array(),
		);

		$coauthors = get_coauthors( $this->post->ID );
		if ( empty( $coauthors ) ) {
			return array();
		}

		foreach ( $coauthors as $coauthor ) {
			$udf['authors'][] = $this->author( $coauthor );
		}

		return $udf;
	}

	/**
	 * Build UDF for a single author.
	 *
	 * @param mixed $coauthor A Co-Author object.
	 *
	 * @return mixed Co-Author object.
	 */
	private function author( $coauthor ) {
		$udf['name'] = $coauthor->display_name;
		$udf['type'] = 'author';

		if ( ! empty( $coauthor->ID ) ) {
			$udf['id'] = (string) $coauthor->ID;
		}

		if ( ! empty( $coauthor->user_nicename ) ) {
			$udf['path'] = wp_parse_url( get_author_posts_url( $coauthor->ID, $coauthor->user_nicename ), PHP_URL_PATH );
		}

		if ( ! empty( $coauthor->website ) ) {
			$udf['website_url'] = $coauthor->website;
		}

		if ( ! empty( $coauthor->description ) ) {
			$udf['bio'] = wpautop( $coauthor->description );
		}

		if ( ! empty( $coauthor->twitter ) ) {
			$udf['twitter'] = $coauthor->twitter;
		}

		if ( ! empty( $coauthor->facebook ) ) {
			$udf['facebook'] = $coauthor->facebook;
		}

		if ( ! empty( $coauthor->google_plus ) ) {
			$udf['google'] = $coauthor->google_plus;
		}

		if ( ! empty( $coauthor->user_email ) ) {
			$udf['email'] = $coauthor->user_email;
		}

		if ( empty( $udf['thumbnail'] ) && has_post_thumbnail( $coauthor ) ) {
			$image = get_post( get_post_thumbnail_id( $coauthor ) );
			$udf['thumbnail'] = ( new Image( $image ) )->udf_image();
		}

		return $udf;
	}

	/**
	 * Get avatar URL for a specified user ID
	 * Check for guest author's avatar first, if it does not exist fallback to linked user.
	 *
	 * @param int   $author_id user ID.
	 * @param array $avatar_url_args args for the avatar URL.
	 *
	 * @return string if avatar exists, otherwise false
	 */
	public function get_author_avatar( $author_id, $avatar_url_args ) {
		if ( class_exists( 'coauthors_plus' ) ) {
			global $coauthors_plus;
			$author = $coauthors_plus->get_coauthor_by( 'id', $author_id );
			if ( ! empty( $author ) ) {
				if ( $this->author_gravatar_is_valid( $author->user_email ) ) {
					return get_avatar_url( $author->user_email, $avatar_url_args );
				}

				$linked_user = get_user_by( 'slug', $author->linked_account );
				if ( ! empty( $linked_user ) && $this->author_gravatar_is_valid( $linked_user->user_email ) ) {
					return get_avatar_url( $linked_user->user_email, $avatar_url_args );
				}
			}
		}

		return false;
	}
	/**
	 * Utility function to check if a gravatar exists for a given email
	 *
	 * @param string $email A user email.
	 *
	 * @return bool if the gravatar exists or not
	 * based on: https://gist.github.com/justinph/5197810
	 */
	public function author_gravatar_is_valid( $email ) {
		$hashkey = md5( strtolower( trim( $email ) ) );
		$uri     = 'http://www.gravatar.com/avatar/' . $hashkey . '?d=404';

		$data = wp_cache_get( $hashkey );
		if ( false === $data ) {
			$response = wp_remote_head( $uri );
			if ( is_wp_error( $response ) ) {
				$data = 'not200';
			} else {
				$data = $response['response']['code'];
			}
			wp_cache_set( $hashkey, $data, $group = '', $expire = 60 * 60 );
		}
		if ( 200 === $data ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Export Author information.
	 *
	 * @param \WP_Post $post The Post.
	 * @param string   $brand The brand.
	 * @param array    $udf UDF array to return.
	 *
	 * @return array
	 */
	public static function export( $post, $brand, $udf ) {
		$authors = ( new Authors( $post ) )->get();

		if ( ! empty( $authors ) ) {
			$udf['authors'] = $authors;
		}

		return $udf;
	}
}
