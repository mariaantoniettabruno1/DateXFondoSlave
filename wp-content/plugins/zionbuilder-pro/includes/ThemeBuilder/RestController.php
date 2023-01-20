<?php

namespace ZionBuilderPro\ThemeBuilder;

use ZionBuilderPro\Api\RestApiController;
use ZionBuilderPro\ThemeBuilder\ThemeBuilder;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class AdobeFonts
 *
 * @package ZionBuilderPro\ThemeBuilder\RestControllers
 */
class RestController extends RestApiController {
	protected $namespace = 'zionbuilder-pro/v1';
	protected $base      = 'theme-builder';

	/**
	 * Register routes
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->base,
			[
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'update_item' ],
					'args'                => [],
					'permission_callback' => [ $this, 'update_item_permissions_check' ],
				],
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
	public function update_item_permissions_check( $request ) {
		if ( ! $this->userCan( 'update_site_templates' ) ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'You do not have permissions to view this resource.', 'zionbuilder-pro' ), [ 'status' => $this->authorization_status_code() ] );
		}

		return true;
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return array|mixed|\WP_Error|\WP_REST_Response
	 */
	public function update_item( $request ) {
		$default_template = $request->get_param( 'default_template' );
		$templates        = $request->get_param( 'templates' );

		$data = [
			'default_template' => ! empty( $default_template ) ? $default_template : null,
			'templates'        => ! empty( $templates ) ? $templates : [],
		];

		// Update the values
		ThemeBuilder::update_site_templates( $data );

		return rest_ensure_response( $data );
	}
}
