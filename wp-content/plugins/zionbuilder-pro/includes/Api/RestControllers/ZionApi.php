<?php

namespace ZionBuilderPro\Api\RestControllers;

use WP_Error;
use ZionBuilderPro\Api\RestApiController;
use ZionBuilderPro\License;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Icons
 *
 * @package ZionBuilder\Api\RestControllers
 */
class ZionApi extends RestApiController {
	protected $namespace = 'zionbuilder-pro/v1';
	protected $base      = 'license';

	public function __construct() {
		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/connect',
			[
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'add_api_key' ],
					'permission_callback' => [ $this, 'add_api_key_permissions_check' ],
				],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/disconnect',
			[
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'remove_api_key' ],
					'permission_callback' => [ $this, 'add_api_key_permissions_check' ],
				],
			]
		);
	}

	public function add_api_key_permissions_check() {
		if ( ! $this->userCan( 'icons_import_item' ) ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'You do not have permissions to view this resource.', 'zionbuilder-pro' ), [ 'status' => $this->authorization_status_code() ] );
		}

		return true;
	}

	public function add_api_key( $request ) {
		$api_key = $request->get_param( 'api_key' );

		$response = License::activate_license( $api_key );
		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'api_error', $response->get_error_message(), [ 'status' => 400 ] );
		}

		return $response;
	}

	public function remove_api_key() {
		$response = License::delete_license();

		if ( is_wp_error( $response ) ) {
			$response->add_data( [ 'status' => 400 ] );
		}

		return rest_ensure_response( $response );
	}
}
