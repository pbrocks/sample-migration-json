<?php
/**
 * UDF Export File
 *
 * @package    WordPress
 * @subpackage sample_structured_export
 */

namespace Phoenix\Sample_Structured_Export;

use Phoenix\Sample_Structured_Export\Collection\Meta;
use Phoenix\Sample_Structured_Export\Collection\Primary_Media;
use Phoenix\Sample_Structured_Export\Collection\Taxonomy;
use Phoenix\Sample_Structured_Export\Component\Comments;
use Phoenix\Sample_Structured_Export\Image;
use Phoenix\Sample_Structured_Export\Component\Image_UDF;

use Phoenix\Sample_Structured_Export\Component\Stories;
use Phoenix\Sample_Structured_Export\Component\Swears;
use Phoenix\Sample_Structured_Export\Component\Products;
use Phoenix\Sample_Structured_Export\Component\Users;
use Phoenix\Sample_Structured_Export\Util\Generate_UUID;

use Phoenix\Sample_Structured_Export\Collection\Tout;
use Phoenix\Sample_Structured_Export\Component\Gallery;
use Phoenix\Sample_Structured_Export\Component\URL;
use Phoenix\Sample_Structured_Export\Component\Article;

/**
 * Class UDF Export
 */
class UDF_Export extends \WP_REST_Controller {


	/**
	 * REST Namespace
	 *
	 * @var string $namespace The namespace;
	 */
	public $namespace = 'structured/v2';

	/**
	 * Brand Info
	 *
	 * @var mixed|void
	 */
	public $brand;

	/**
	 * Constructor function.
	 */
	public function __construct() {
		$this->brand = apply_filters( 'sample_structured_export_brand_name', 'sample' );
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		add_filter( 'query_vars', array( $this, 'sample_structured_export_custom_query_vars' ), 10 );
		add_filter( 'posts_where', array( $this, 'modify_docs_query' ), 10, 2 );
		add_filter( 'terms_clauses', array( $this, 'modify_terms_query' ), 10, 3 );
		add_filter( 'pre_user_query', array( $this, 'modify_users_query' ), 10 );
	}

	/**
	 * Have to wrap the summary for galleries in a block html.
	 *
	 * @param string $summary The gallery image summary.
	 * @return string
	 */
	public function filter_gallery_summary( $summary ) {
		return wpautop( $summary );
	}

	/**
	 * Add a custom query var.
	 *
	 * @param array $vars The array of whitelisted query variable names.
	 *
	 * @return array $vars
	 */
	public function sample_structured_export_custom_query_vars( $vars ) {
		$vars[] = 'udf_query';

		return $vars;
	}

	/**
	 * Setup endpoints for REST routes
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/documents',
			array(
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_documents' ),
				'args'     => $this->get_rest_params( 'documents' ),
			)
		);
		register_rest_route(
			$this->namespace,
			'/documents/(?P<post_id>\d+)',
			array(
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_document_by_id' ),
			)
		);
		register_rest_route(
			$this->namespace,
			'/terms',
			array(
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_terms_list' ),
				'args'     => $this->get_rest_params( 'terms' ),
			)
		);
		register_rest_route(
			$this->namespace,
			'/terms/(?P<term_id>\d+)',
			array(
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_term_by_id' ),
			)
		);
		register_rest_route(
			$this->namespace,
			'/images',
			array(
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_images' ),
				'args'     => $this->get_images_params(),
			)
		);
		register_rest_route(
			$this->namespace,
			'/image',
			array(
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_image' ),
				'args'     => $this->get_images_params(),
			)
		);
		register_rest_route(
			$this->namespace,
			'/users',
			array(
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_users' ),
				'args'     => $this->get_rest_params( 'users' ),
			)
		);
		register_rest_route(
			$this->namespace,
			'/users/(?P<user_id>\d+)',
			array(
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_user' ),
			)
		);
	}

	/**
	 * Get a list of UDF document/{cms_id} endpoints, default to post posttype.
	 * Excludes any longform post.
	 * Supports type and page query parameters.
	 *
	 * @param object $request Request Object.
	 *
	 * @return array
	 */
	public function get_documents( $request ) {
		$post_type = $request->get_param( 'type' );

		$args = array(
			'suppress_filters' => false,
			'posts_per_page'   => $request->get_param( 'limit' ),
			'post_type'        => $post_type,
			'post_status'      => 'publish',
			'orderby'          => 'ID',
			'order'            => 'ASC',
			'udf_query'        => $request->get_param( 'id' ),
		);

		$links = array();

		// removes wp_posts.menu_order from order by select and displays more results for products
		// and other post_types
		remove_all_filters( 'posts_orderby' );

		$query = new \WP_Query( $args );
		$posts = $query->posts;

		foreach ( $posts as $post ) {
			$links[ get_rest_url() . $this->namespace . '/documents/' . $post->ID ] = $this->get_content_type_from_post_type( $post->post_type );
			$last_id = $post->ID;
		}

		remove_filter( 'posts_where', array( $this, 'modify_docs_query' ), 10, 2 );

		$total_posts = get_posts(
			array(
				'post_type'   => $this->get_content_type_from_post_type( $post->post_type ),
				'post_status' => 'publish',
				'numberposts' => -1,
			)
		);

		$data                = array();
		$data['total_found'] = (string) count( $total_posts );
		$data['post_count']  = $query->post_count;
		$data['last_id']     = $last_id;

		$post_types = array_merge( ...array_values( $this->get_content_type_mapping() ) );

		$data['next'] = get_rest_url() . $this->namespace . '/documents?id=' . $last_id . '&limit=' . $request->get_param( 'limit' );

		if ( ! empty( array_diff( $post_types, $post_type ) ) ) {
			$data['next'] .= '&type=' . $this->get_content_type_from_post_type( $post->post_type );
		}

		$data['links'] = $links;

		return $data;
	}

	/**
	 * Get a list of UDF user endpoints
	 *
	 * @param object $request Request Object.
	 *
	 * @return array
	 */
	public function get_users( $request ) {
		$rest_url = get_rest_url() . $this->namespace;
		$limit    = $request->get_param( 'limit' );

		// fetch users by given limit and required fields
		$args          = array(
			'number'  => $limit,
			'orderby' => 'ID',
			'order'   => 'ASC',
			'fields'  => array( 'ID' ),
			'user_id' => $request->get_param( 'id' ),
		);
		$wp_user_query = new \WP_User_Query( $args );
		$users         = $wp_user_query->get_results();

		// prepare endpoint links array and get user last id
		$links = array();
		foreach ( $users as $user ) {
			$links[ $rest_url . '/users/' . $user->ID ] = 'user';
			$last_id                                    = $user->ID;
		}

		// use to modify WP_User_Query
		remove_filter( 'pre_user_query', 'modify_users_query', 10 );

		// get total user count
		$total_users = count_users();

		$data                = array();
		$data['total_found'] = (string) $total_users['total_users'];
		$data['user_count']  = count( $users );
		$data['last_id']     = $last_id;

		// next navigation link
		$data['next'] = add_query_arg(
			array(
				'limit' => $limit,
				'id'    => $last_id,
			),
			$rest_url . '/users'
		);

		$data['links'] = $links;

		// release variables from memory. Do we need?
		unset( $links, $rest_url, $limit, $users, $total_users, $last_id );

		return $data;
	}

	/**
	 * Filter the posts_where query.
	 *
	 * @param string   $where query string.
	 * @param WP_Query $query the query.
	 *
	 * @return string the modified query string.
	 */
	public function modify_docs_query( $where, $query ) {
		global $wpdb;

		$id = $query->get( 'udf_query', false );
		if ( $id ) {
			$where .= $wpdb->prepare( ' AND ID > %d ', (int) $id );
		}

		return $where;
	}

	/**
	 * Filter the user query.
	 *
	 * @param WP_User_Query $query the query.
	 *
	 * @return string the modified query string.
	 */
	public function modify_users_query( $query ) {
		global $wpdb;

		$id = $query->get( 'user_id', false );

		if ( $id ) {
			$query->query_where .= " AND {$wpdb->users}.ID > {$id} ";
		}
	}

	/**
	 * Get the UDF for the document.
	 *
	 * @param object $request Request Object.
	 *
	 * @return array|\WP_Error
	 */
	public function get_document_by_id( $request ) {
		$id   = $request['post_id'];
		$data = new \stdClass();
		$post = get_post( $id );

		if ( empty( $post ) ) {
			return new \WP_Error( 'rest_post_invalid_id', __( 'Invalid post ID.' ), array( 'status' => 404 ) );
		}

		// Default UDF.
		$udf                  = array();
		$udf['_type']         = 'content-type-' . $post->post_type;
		$udf['headline']      = (string) $post->post_title;
		$udf['summary']       = (string) $post->post_excerpt;
		$udf['status']        = (string) 'publish' === $post->post_status ? 'published' : $post->post_status;
		$udf['id']            = (string) $post->ID;
		$udf['brand']         = $this->brand;
		$udf['cms_id']        = (string) $post->ID;

		$item_uuid = get_post_meta( $post->ID, 'tempo_uuid_value', true );
		if ( empty( $item_uuid ) ) {
			$item_uuid = Generate_UUID::fetch_uuid();
			update_post_meta( $post->ID, 'tempo_uuid_value', $item_uuid );
		}

		$udf['uuid']          = $item_uuid;
		$udf['legacy_cms_id'] = (string) $post->ID;

		$udf['url']           = ( new URL( $post ) )->get();
		$udf['created_date']  = get_gmt_from_date( $post->post_date, 'Y-m-d\TH:i:s\Z' );
		$udf['publish_date']  = get_gmt_from_date( $post->post_date, 'Y-m-d\TH:i:s\Z' );
		$udf['last_updated']  = get_gmt_from_date( $post->post_modified, 'Y-m-d\TH:i:s\Z' );
		$udf['last_optimized']  = get_gmt_from_date( $post->post_modified, 'Y-m-d\TH:i:s\Z' );
		$udf['comments']      = ( new Comments( $post ) )->get();

		$language = $this->get_language( $post );
		if ( $language ) {
			$udf['language'] = $language;
		}

		if ( metadata_exists( 'post', $post->ID, 'timestamp_override' ) ) {
			if ( empty( get_post_meta( $post->ID, 'timestamp_override', true ) ) ) {
				$udf['display_date'] = 'last-optimized';
			} else {
				$udf['display_date'] = 'first-published';
			}
		} else {
			// posts that were migrated into VIP.
			$udf['display_date'] = 'last-optimized';
		}

		// Get proper data per content type.
		if ( ! empty( $post ) ) {
			switch ( get_post_type( $post ) ) {
				case 'products':
					$udf  = Taxonomy::export( $post, $this->brand, $udf );
					$udf  = Products::prepare_products_udf( $post, $this->brand, $udf );
					$data = Products::export( $post, $this->brand, $udf );
					break;
				case 'swears':
					$udf = Tout::export( $post, $this->brand, $udf );
					$udf = Meta::export( $post, $this->brand, $udf );
					$udf = Component\Authors::export( $post, $this->brand, $udf );
					$udf  = Taxonomy::export( $post, $this->brand, $udf );
					$data = Swears::export( $post, $this->brand, $udf );
					$data->udf = Primary_Media::export( $post, $this->brand, $data->udf );
					break;
				case 'stories':
				default:
					$udf = Tout::export( $post, $this->brand, $udf );
					$udf = Meta::export( $post, $this->brand, $udf );
					$udf = Component\Authors::export( $post, $this->brand, $udf );
					$udf  = Taxonomy::export( $post, $this->brand, $udf );
					$udf  = Stories::prepare_longform_udf( $post, $this->brand, $udf );
					$data = Stories::export( $post, $this->brand, $udf );
					$data->udf = Primary_Media::export( $post, $this->brand, $data->udf );
					break;
			}
		}

		$data->udf = array_merge( $udf, $data->udf );
		$metrics = Collection\Body_Parser::metrics();
		if ( ! empty( $metrics ) ) {
			$data = array_merge( (array) $data, $metrics );
		}

		return $data;
	}

	/**
	 * Get the UDF for the user.
	 *
	 * @param object $request Request Object.
	 *
	 * @return array|\WP_Error
	 */
	public function get_user( $request ) {
		$user_id = $request['user_id'];
		$user    = get_userdata( $user_id );

		if ( empty( $user ) ) {
			return new \WP_Error( 'rest_user_invalid_id', __( 'Invalid user ID.' ), array( 'status' => 404 ) );
		}

		$udf                       = array();
		$udf['email']              = (string) $user->user_email;
		$udf['accountStatus']      = 'active';
		$udf['brand']              = $this->brand;
		$udf['registrationSource'] = '9999';

		// User Meta information.
		$udf = Users::export( $user, $udf );

		// release variables from memory.
		unset( $user_id, $user );

		$data = new \stdClass();
		$data = $udf;

		return $data;
	}

	/**
	 * Get a list of UDF term links, default to category terms.
	 * Supports type and page query parameters.
	 *
	 * @param object $request Request Object.
	 *
	 * @return array
	 */
	public function get_terms_list( $request ) {
		$default_taxonomies = array( 'category', 'post_tag' );

		$type = $request->get_param( 'type' );

		if ( ! $type ) {
			$type = $default_taxonomies;
		}

		$args = array(
			'taxonomy'   => $type,
			'hide_empty' => false,
			'number'     => $request->get_param( 'limit' ),
			'orderby'    => 'term_id',
			'order'      => 'ASC',
			'id'         => $request->get_param( 'id' ),
		);

		$links = array();

		$taxonomies = get_terms( $args );
		if ( ! $taxonomies ) {
			$taxonomies = array();
			$last_id    = $request->get_param( 'id' ) - 1;
		}

		foreach ( $taxonomies as $taxonomy ) {
			$links[ get_rest_url() . $this->namespace . '/terms/' . $taxonomy->term_id ] = $taxonomy->taxonomy ?: 'category';
			$last_id = $taxonomy->term_id;
		}

		$data = array();

		$data['type']  = $type ?: 'category';
		$data['next']  = get_rest_url() . $this->namespace . '/terms?id=' . ( $last_id + 1 ) . '&type=' . ( is_array( $type ) ? 'category' : $type ) . '&limit=' . $request->get_param( 'limit' );
		$data['links'] = $links;

		return $data;
	}

	/**
	 * Filter the terms_clauses query.
	 *
	 * @param string $clauses query string.
	 * @param string $taxonomies query string.
	 * @param array  $args the query.
	 *
	 * @return string the modified query string.
	 */
	public function modify_terms_query( $clauses, $taxonomies, $args ) {
		global $wpdb;

		$id = '';

		if ( ! empty( $args['id'] ) ) {
			$id = $args['id'];
		}

		if ( $id ) {
			$clauses['where'] .= $wpdb->prepare( ' AND t.term_id >= %d ', (int) $id );
		}

		return $clauses;
	}
	/**
	 * Get the UDF for the term.
	 *
	 * @param object $request Request Object.
	 *
	 * @return \WP_REST_Response
	 */
	public function get_term_by_id( $request ) {
		$id   = $request['term_id'];
		$data = null;
		$term = get_term( $id );

		if ( ! $term ) {
			return new \WP_Error( 'rest_term_invalid_taxonomy', __( 'Invalid term.' ), array( 'status' => 404 ) );
		}

		$data = ( new Term( $term ) )->prepare();

		return $data;
	}

	/**
	 * Get a list of UDF image links.
	 * Supports path and page query parameters.
	 *
	 * @param object $request Request Object.
	 *
	 * @return \WP_REST_Response
	 */
	public function get_images( $request ) {
		$list_images = false;

		$image_path = $request->get_param( 'path' );

		$data = new \stdClass();

		$data->type = 'images';

		// Set list_images to true if no path provided.
		if ( ! $image_path ) {
			$list_images = true;
		}

		if ( $list_images ) {
			$args = array(
				'posts_per_page' => $request->get_param( 'limit' ),
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'post_status'    => 'any',
				'orderby'        => 'ID',
				'order'          => 'ASC',
				'udf_query'      => $request->get_param( 'id' ),
			);

			$links = array();

			$query = new \WP_Query( $args );
			$posts = $query->posts;

			foreach ( $posts as $post ) {
				$path = wp_parse_url( wp_get_attachment_url( $post->ID ), PHP_URL_PATH );
				if ( isset( $_SERVER['HTTP_HOST'] ) && in_array( sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ), array( 'devwpe.local', 'devsampleapi.wpengine.com', 'testsampleapi.wpengine.com', 'api.sample.com', 'sample.com', 'www.sample.com' ), true ) ) {
					$path = str_replace( '/wp-content/', '', $path );
				}
				$links[ get_rest_url() . $this->namespace . '/image/?path=' . $path ] = 'image';
				$last_id = $post->ID;
			}

			$data->total_found = $query->found_posts;
			$data->post_count  = $query->post_count;
			$data->last_id     = $last_id;
			$data->next        = get_rest_url() . $this->namespace . '/images?id=' . $last_id . '&limit=' . $request->get_param( 'limit' );
			$data->links       = $links;

			return $data;
		}

		// If Path is provided, get image object.
		$meta_query = array(
			'posts_per_page' => 1,
			'post_type'      => 'attachment',
			'post_status'    => 'any',
			'meta_key' => '/' . $image_path, // @codingStandardsIgnoreLine This is not a slow query.
			'meta_compare'   => '=',
		);
		$query2     = new \WP_Query( $meta_query );

		$image = $query2->posts;
		// Only take the first entry.
		if ( $image ) {
			$post = get_post( $image[0]->ID );
		}

		$data = ( new Component\Image( $post ) )->rest_prepare_images();

		return $data;
	}

	/**
	 * Get image UDF.
	 *
	 * @param object $request Request Object.
	 *
	 * @return \WP_REST_Response
	 */
	public function get_image( $request ) {
		$data       = new \stdClass();
		$image_path = $request->get_param( 'path' );

		// If Path is provided, get image object.
		$meta_query = array(
			'posts_per_page' => 1,
			'post_type'      => 'attachment',
			'post_status'    => 'any',
			'meta_key' => '/' . $image_path, // @codingStandardsIgnoreLine This is not a slow query.
			'meta_compare'   => '=',
		);
		$query2     = new \WP_Query( $meta_query );

		$image = $query2->posts;
		// Only take the first entry.
		if ( $image ) {
			$post = get_post( $image[0]->ID );
		}

		$data = ( new Component\Image_UDF( $post ) )->rest_prepare_images();

		return $data;
	}

	/**
	 * Get the shared query params for collections.
	 *
	 * @param string $type the type of collection.
	 *
	 * @return array
	 */
	public function get_rest_params( $type ) {
		$params = array(
			'limit' => array(
				'description'       => 'The number of posts to return between 1 and 2000.',
				'default'           => 500,
				'minimum'           => 1,
				'maximum'           => 2000,
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			),
			'id'    => array(
				'description'       => 'Return posts whose ID is greater than $id.',
				'default'           => 0,
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			),
		);
		if ( 'documents' === $type ) {
			$type_params = $this->get_documents_params();
		}

		if ( 'terms' === $type ) {
			$type_params = $this->get_terms_params();
		}

		if ( 'users' === $type ) {
			$type_params = array();
		}

		$updated_params = array_merge( $params, $type_params );

		return $updated_params;
	}

	/**
	 * Get the query params for documents.
	 *
	 * @return array
	 */
	public function get_documents_params() {
		$params = array(
			'type' => array(
				'description'       => __( 'Specify a content type to query or defaults to all.' ),
				'default'           => array_keys( $this->get_content_type_mapping() ),
				'type'              => 'mixed', // query param will be string, otherwise default will be an array.
				'sanitize_callback' => function ( $value, $request, $param ) {
					// Map the common type to the WordPress post type.
					$mapping = $this->get_content_type_mapping();
					if ( is_array( $value ) ) {
						$value = array_merge( ...array_values( $mapping ) );
					} else {
						$value = $mapping[ $value ];
					}
					return rest_sanitize_request_arg( $value, $request, $param );
				},
				'validate_callback' => function ( $value ) {
					$types = array_keys( $this->get_content_type_mapping() );
					return in_array( $value, $types, true ) || empty( array_diff( $value, $types ) );
				},
			),
		);

		return $params;
	}

	/**
	 * Get the query params for terms.
	 *
	 * @return array
	 */
	public function get_terms_params() {
		$params = array(
			'type' => array(
				'description'       => __( 'Type of taxonomy.' ),
				'type'              => 'string',
				'sanitize_callback' => 'wp_filter_nohtml_kses',
				'validate_callback' => function ( $type ) {
					// Get all the registered taxonomies.
					$taxonomies = get_taxonomies();

					// If requested type is registered, return that. Otherwise it will use the default category taxonomy.
					return is_array( $taxonomies ) && in_array( $type, $taxonomies, true );
				},
			),
		);
		return $params;
	}

	/**
	 * Get the query params for images.
	 *
	 * @return array
	 */
	public function get_images_params() {
		$params = array(
			'path'  => array(
				'description'       => __( 'URL path of the post.' ),
				'type'              => 'string',
				'sanitize_callback' => function ( $path ) {
					// Remove surrounding slashes and sanitize path.
					return untrailingslashit( sanitize_text_field( ltrim( $path, '/' ) ) );
				},
				'validate_callback' => 'rest_validate_request_arg',
			),
			'limit' => array(
				'description'       => 'The number of posts to return between 1 and 2000.',
				'default'           => 500,
				'minimum'           => 1,
				'maximum'           => 2000,
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			),
			'id'    => array(
				'description'       => 'Return posts whose ID is greater than $id.',
				'default'           => 0,
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			),
		);
		return $params;
	}

	/**
	 * UDF data for redirect.
	 *
	 * @param object $post \WP_Post.
	 *
	 * @return \WP_REST_Response
	 */
	public function rest_prepare_redirect( $post ) {

		$data      = new \stdClass();
		$data->udf = array(
			'_type'  => 'content-type-redirect',
			'brand'  => $this->brand,
			'cms_id' => (string) $post->ID,
			'uuid'   => get_post_meta( $post->ID, 'tempo_uuid_value', true ),
		);

		$redirect_rule_status_code = intval( get_post_meta( $post->ID, '_redirect_rule_status_code', true ) );
		$redirect_rule_from        = esc_url( home_url( get_post_meta( $post->ID, '_redirect_rule_from', true ) ) );
		$redirect_rule_to          = esc_url( get_post_meta( $post->ID, '_redirect_rule_to', true ) );

		$redirect['status'] = ( 302 === $redirect_rule_status_code ? '302' : '301' );

		// Component of type 'url'.
		$url_components  = wp_parse_url( $redirect_rule_from );
		$redirect['url'] = array(
			'_type'  => 'url',
			'origin' => $url_components['scheme'] . '://' . $url_components['host'],
			'path'   => $url_components['path'],
		);
		// convert path to a url as needed.
		$redirect['to_url'] = ( 'http' === substr( $redirect_rule_to, 0, 4 ) ? $redirect_rule_to : home_url( $redirect_rule_to ) );

		$data->udf = array_merge( $data->udf, $redirect );

		return $data;
	}

	/**
	 * Map standard types used by the pipeline to WordPress post types.
	 *
	 * @return array
	 */
	public function get_content_type_mapping() {
		return apply_filters(
			'sample_structured_export_content_type_mapping',
			array(
				'users'    => array( 'users' ),
				'stories'  => array( 'stories' ),
				'swears'   => array( 'swears' ),
				'products' => array( 'products' ),
			)
		);
	}

	/**
	 * Given a post type, get the standard type.
	 *
	 * @param string $type WordPress post type.
	 *
	 * @return string
	 */
	public function get_content_type_from_post_type( $type ) {
		foreach ( $this->get_content_type_mapping() as $content_type => $post_types ) {
			if ( array_intersect( $post_types, (array) $type ) ) {
				return $content_type;
			}
		}

		return $type;
	}

	/**
	 * The the language associated with the content. Only return language
	 * if it is explicitly set.
	 *
	 * @param \WP_Post $post The Post.
	 * @return string
	 */
	public function get_language( $post ) {
		return 'en-us';
	}
}
