<?php

namespace ZionBuilderPro\Features\Connector\Api;

use ZionBuilderPro\Api\RestApiController;
use ZionBuilder\Settings;
use ZionBuilder\Plugin;
use ZionBuilderPro\Features\Connector\Connector;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class DataSets
 *
 * @package ZionBuilder\Api\RestControllers
 */
class Api extends RestApiController {
	/**
	 * Api endpoint namespace
	 *
	 * @var string
	 */
	protected $namespace = 'zionbuilder-pro/v1';

	/**
	 * Api endpoint
	 *
	 * @var string
	 */
	protected $base = 'connector';

	/**
	 * Register routes
	 *
	 * @return void
	 */
	public function register_routes() {
		/**
		 * Returns all sources configuration
		 */
		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/library/items-and-categories',
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_items_and_categories' ],
					'args'                => [],
					'permission_callback' => [ $this, 'get_items_permissions_callback' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/library/get-builder-data',
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_builder_data' ],
					'args'                => [],
					'permission_callback' => [ $this, 'get_items_permissions_callback' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);
	}

	public function get_items_and_categories( $request ) {
		$library = Plugin::instance()->library->get_source( 'local_library' );

		if ( ! $library ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'No library found that mathces your request.', 'zionbuilder' ), [ 'status' => $this->authorization_status_code() ] );
		}

		$items_and_categories = $library->get_items_and_categories();

		return rest_ensure_response( $library->get_items_and_categories() );
	}


	public function get_builder_data( $request ) {
		$template_id = (int) $request->get_param( 'template_id' );

		$library = Plugin::instance()->library->get_source( 'local_library' );

		if ( ! $library ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'No library found that mathces your request.', 'zionbuilder' ), [ 'status' => $this->authorization_status_code() ] );
		}

		return rest_ensure_response( $library->insert_item( $template_id ) );
	}

	public function get_items_permissions_callback( $request ) {
		$can_access = Connector::can_access_library($request->get_param( 'password' ));
		if ( is_wp_error( $can_access ) ) {
			$can_access->add_data( [ 'status' => $this->authorization_status_code() ] );
			return $can_access;
		}

		return $can_access;
	}

}
