<?php

namespace ZionBuilderPro\Api\RestControllers;

use ZionBuilderPro\Api\RestApiController;
use ZionBuilder\Plugin;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class WPPageSelector
 *
 * @package ZionBuilderPro\Api\RestControllers
 */
class WPPageSelector extends RestApiController {
	protected $namespace = 'zionbuilder-pro/v1';
	protected $base      = 'page-selector-data';

	/**
	 * Register routes
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->base,
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_item' ],
					'args'                => [],
					'permission_callback' => [ $this, 'get_item_permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);
	}

	/**
	 * Checks if a given request has access to read a data set.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request full details about the request
	 *
	 * @return \WP_Error|bool true if the request has read access for the item, WP_Error object otherwise
	 */
	public function get_item_permissions_check( $request ) {
		if ( ! $this->userCan( 'page-selector-data' ) ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'You do not have permissions to view this resource.', 'zionbuilder-pro' ), [ 'status' => $this->authorization_status_code() ] );
		}

		return true;
	}

	/**
	 * @param \WP_REST_Request $request
	 * @param mixed            $use_cache
	 *
	 * @return array|mixed|\WP_Error|\WP_REST_Response
	 */
	public function get_item( $request, $use_cache = true ) {
		$type            = $request->get_param( 'type' );
		$subtype         = $request->get_param( 'subtype' );
		$page            = $request->get_param( 'page' );
		$search          = $request->get_param( 'search' );
		$items_to_return = [];

		switch ( $type ) {
			case 'single':
				$items = get_posts(
					[
						'post_status'    => 'any',
						'post_type'      => $subtype,
						'paged'          => $page,
						'posts_per_page' => 25,
						's'              => $search,
					]
				);

				foreach ( $items as $post ) {
					$items_to_return[] = [
						'id'    => $post->ID,
						'name' => $post->post_title,
					];
				}

				break;
			case 'taxonomy_archive':
				$terms = get_terms(
					[
						'post_status' => 'any',
						'taxonomy'    => $subtype,
						'paged'       => $page,
						'number'      => 25,
						'offset'      => ( $page - 1 ) * 25,
						'search'      => $search,
					]
				);

				// Normalize array
				if ( is_array( $terms ) ) {
					foreach ( $terms as $term ) {
						$items[] = [
							'id'    => $term->term_id,
							'name' => $term->name,
						];
					}
				}

				return $items;

				break;
			default:
				# code...
				break;
		}

		return rest_ensure_response( $items_to_return );
	}

	/**
	 * Retrieves the site setting schema, conforming to JSON Schema.
	 *
	 * @since 1.0.0
	 *
	 * @return array item schema data
	 */
	public function get_item_schema() {
		$schema = [
			'$schema' => 'http://json-schema.org/draft-04/schema#',
			'title'   => 'settings',
			'type'    => 'array',
			'items'   => [
				'type' => 'string',
			],
		];

		return $this->add_additional_fields_schema( $schema );
	}
}
