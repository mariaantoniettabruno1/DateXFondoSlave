<?php

namespace ZionBuilderPro\Conditions;

use ZionBuilderPro\Api\RestApiController;
use ZionBuilderPro\Plugin;

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
	protected $base      = 'conditions';


	/**
	 * Register routes
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/get-rule-options',
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_condition_options' ],
					'args'                => [],
					'permission_callback' => [ $this, 'allowed_theme_builder_access' ],
				],
			]
		);
	}

	public function allowed_theme_builder_access() {
		if ( ! $this->userCan( 'use_theme_builder' ) ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'You do not have permissions to view this resource.', 'zionbuilder-pro' ), [ 'status' => $this->authorization_status_code() ] );
		}

		return true;
	}

	public function get_condition_options( \WP_REST_Request $request ) {
		$condition_id       = $request->get_param( 'condition_id' );
		$callback_arguments = json_decode( $request->get_param( 'callback_arguments' ), true );

		return Plugin::instance()->conditions->get_condition_options( $condition_id, $callback_arguments );
	}
}
