<?php
/**
 * Send notification of updates to posts.
 *
 * @package    WordPress
 * @subpackage sample_structured_export
 */

namespace Phoenix\Sample_Structured_Export;

use Phoenix\Sample_Structured_Export\UDF_Export;

/**
 * Class Notify
 */
class Notify {

	/**
	 * REST Namespace
	 *
	 * @var string $namespace The namespace;
	 */
	public $rest_namespace = 'structured/v2';

	/**
	 * Constructor function.
	 */
	public function __construct() {
		add_action( 'add_attachment', array( $this, 'should_send' ), 10, 1 );
		add_action( 'edit_attachment', array( $this, 'should_send' ), 10, 1 );
		add_action( 'publish_post', array( $this, 'should_send' ), 10, 1 );
		add_action( 'publish_recap', array( $this, 'should_send' ), 10, 1 );
		add_action( 'publish_gallery', array( $this, 'should_send' ), 10, 1 );
		add_action( 'edited_terms', array( $this, 'get_term_payload' ), 10, 1 );
		add_action( 'create_term', array( $this, 'get_term_payload' ), 10, 1 );
	}

	/**
	 * Determine if the post should trigger a hyper migration.
	 *
	 * @param int $post_id The ID of the post saved.
	 */
	public function should_send( $post_id ) {
		$post = get_post( $post_id );
		if ( 'attachment' === $post->post_type &&
			! ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) &&
			! wp_is_post_revision( $post_id ) ) {
				$this->get_post_payload( $post_id );
				return;
		}

		// Prevent migration during auto save or ajax calls or if there is no data.
		// Also prevent on non desired post types.
		if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ||
			( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ||
			wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Do not hypermigrate longform posts. Cannot use get_post_meta(), too slow.
		//@codingStandardsIgnoreStart
		if ( ! empty( $_POST['sample_longform_format'] ) &&
			sanitize_text_field( $_POST['sample_longform_format'] ) &&
			'longform' === $_POST['sample_longform_format'] ) {
			update_post_meta( $post_id, 'hyper_migration', 'migrations disabled for longform' );
			return;
		}
		//@codingStandardsIgnoreEnd
		$this->get_post_payload( $post_id );
	}

	/**
	 * Setup the content for a post object to deliver to migration.
	 *
	 * @param int $post_id The ID of the post saved.
	 */
	public function get_post_payload( $post_id ) {
		$post = get_post( $post_id );

		// Get the migration posttype mapped to WP post types.
		if ( 'attachment' !== $post->post_type ) {
			$post_map  = new UDF_Export();
			$post_type = $post_map->get_content_type_from_post_type( $post->post_type );
		}

		// The migrations config brand doesn't match filter.
		$brand = apply_filters( 'sample_structured_export_brand_name', 'sample' );

		$payload_shared = $this->shared_payload();
		if ( 'attachment' !== $post->post_type ) {
			$payload = wp_json_encode(
				array(
					'brand' => $payload_shared['brand'],
					'env'   => $payload_shared['env'],
					'links' => array(
						// Add cachebust to url.
						get_rest_url() . $this->rest_namespace . '/documents/' . $post_id . '?cb=' . time() => $post_type,
					),
				)
			);
		}

		if ( 'attachment' === $post->post_type ) {
			$image_url = wp_get_attachment_url( $post_id );

			if ( $image_url ) {
				$path = wp_parse_url( $image_url, PHP_URL_PATH );

				// This is a VIP GO environment.
				if ( defined( 'VIP_GO_ENV' ) && 'develop' === VIP_GO_ENV ) {
					// take everything from wp-content and on.
					preg_match( '/.*?((\/pesp|ew|sample)?\/wp-content.*)/', $image_url, $vip_matches );
					if ( $vip_matches ) {
						$path = $vip_matches[1];
					}
				}
			}

			// Different payload for the image UDF.
			$payload = wp_json_encode(
				array(
					'brand' => $payload_shared['brand'],
					'env'   => $payload_shared['env'],
					'links' => array(
						// Add cachebust to image path.
						get_rest_url() . $this->rest_namespace . '/image/?path=' . $path . '&cb=' . time() => 'image',
					),
				)
			);
		}

		$this->send_notification( $payload, $post_id );
	}

	/**
	 * Setup the content for a term object to deliver to migration.
	 *
	 * @param int $term_id The ID of the term.
	 */
	public function get_term_payload( $term_id ) {
		$term = get_term( $term_id );

		// No term found, early return.
		if ( empty( $term ) ) {
			return;
		}

		$payload_shared = $this->shared_payload();

		$payload = wp_json_encode(
			array(
				'brand' => $payload_shared['brand'],
				'env'   => $payload_shared['env'],
				'links' => array(
					// Add cachebust to url.
					get_rest_url() . $this->rest_namespace . '/terms/' . $term_id . '?cb=' . time() => $term->taxonomy,
				),
			)
		);

		$this->send_notification( $payload );
	}

	/**
	 * Setup the shared content for both terms and posts to deliver to migration.
	 */
	public function shared_payload() {
		// The migrations config brand doesn't match filter.
		$brand = apply_filters( 'sample_structured_export_brand_name', 'sample' );

		// Get hyper migration dev or prod env.
		$env = get_option( 'sample_structured_export_migration_env', 'hyper-dev' );
		$no_content_router = get_option( 'sample_structured_export_no_content_router', false );

		if ( $no_content_router ) {
			// This will use a migration pipeline config of AddDoNotRoute:true.
			$env = $env . '-do-not-route';
		}

		return array(
			'brand' => $brand,
			'env'   => $env,
		);
	}

	/**
	 * Send POST request upon save or update of posts and terms for hyper migration.
	 *
	 * @param array $payload The content to send.
	 * @param int   $post_id The id of the post saved.
	 */
	public function send_notification( $payload, $post_id = null ) {
		// Get the URL to send the POST request to.
		$url = esc_url( get_option( 'sample_structured_export_migration_rx_url' ) );
		$hyper_api_key = esc_attr( get_option( 'sample_structured_export_migration_api_key' ), '' );

		// Don't send any migrations if the url is not configured.
		if ( false === filter_var( $url, FILTER_VALIDATE_URL ) && ! empty( $post_id ) ) {
			update_post_meta( $post_id, 'hyper_migration', 'migrations disabled' );
			return;
		}

		// Send the POST request.
		// Try sending the POST request for 3 times, if it fails.
		$tries = 1;

		// Log the initial hyper request call time.
		if ( ! empty( $post_id ) ) {
			update_post_meta( $post_id, 'hyper_migration_start', gmdate( 'Y-m-d H:i:s' ) );
		}

		do {
			$response = wp_remote_post(
				$url,
				array(
					'method'      => 'POST',
					'timeout'     => 5,
					'redirection' => 5,
					'body'        => $payload,
					'headers'     => array(
						'content-type' => 'application/json',
						'x-api-key'    => $hyper_api_key,
					),
				)
			);

			$tries++;

			// Try again if there was an error until max loop times.
			if ( is_wp_error( $response ) ) {
				continue;
			}

			// Don't try again if POST request is successful.
			if ( ! empty( $response ) && 200 === $response['response']['code'] ) {
				break;
			}
		} while ( $tries <= 3 );

		// Log the error or the success if this is a post and not a term.
		if ( ! empty( $post_id ) ) {
			if ( is_wp_error( $response ) ) {
				update_post_meta( $post_id, 'hyper_migration', $response->get_error_message() );
			} else {
				update_post_meta( $post_id, 'hyper_migration', $response['body'] );
			}
		}
	}
}
