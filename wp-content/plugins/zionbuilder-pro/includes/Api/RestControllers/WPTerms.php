<?php

namespace ZionBuilderPro\Api\RestControllers;

use ZionBuilderPro\Api\RestApiController;
use ZionBuilder\Plugin;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class WPTerms
 *
 * @package ZionBuilderPro\Api\RestControllers
 */
class WPTerms extends RestApiController {
	protected $namespace = 'zionbuilder-pro/v1';
	protected $base      = 'terms';

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
		if ( ! $this->userCan( 'adobe_fonts_get_item' ) ) {
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
		$search   = $request->get_param( 'search' );
		$taxonomy = $request->get_param( 'taxonomy' );
		$page     = $request->get_param( 'page' );
		$page     = ! empty( $page ) ? $page : 1;
		$search   = $request->get_param( 'search' );
		$per_page = -1;

		$args = [
			'taxonomy' => $taxonomy,
			'search'   => $search,
			'number'   => 0,
 		];

		$terms           = get_terms( $args );
		$terms_to_return = [];
		foreach ( $terms as $key => $value ) {
			$terms_to_return[] = $value;
		}

		return rest_ensure_response( $terms_to_return );
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
