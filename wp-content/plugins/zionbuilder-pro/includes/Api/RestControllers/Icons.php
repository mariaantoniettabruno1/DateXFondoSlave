<?php

namespace ZionBuilderPro\Api\RestControllers;

use ZionBuilderPro\Api\RestApiController;
use ZionBuilderPro\Plugin;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Icons
 *
 * @package ZionBuilder\Api\RestControllers
 */
class Icons extends RestApiController {
	protected $namespace = 'zionbuilder-pro/v1';
	protected $base      = 'icons';

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
					'permission_callback' => [ $this, 'import_item_permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->base,
			[
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'import_item' ],
					'permission_callback' => [ $this, 'import_item_permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->base,
			[
				[
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => [ $this, 'delete_item' ],
					'permission_callback' => [ $this, 'import_item_permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/export',
			[
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'export_item' ],
					'permission_callback' => [ $this, 'import_item_permissions_check' ],
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
	public function import_item_permissions_check( $request ) {
		if ( ! $this->userCan( 'icons_import_item' ) ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'You do not have permissions to view this resource.', 'zionbuilder-pro' ), [ 'status' => $this->authorization_status_code() ] );
		}

		return true;
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_Error|\WP_REST_Response
	 */
	public function get_item( $request ) {
		$icons = Plugin::instance()->icons->get_icons_list();
		return rest_ensure_response( $icons );
	}

	/**
	 * This function will import a zip folder containing icons and return a notification message
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_Error|\WP_REST_Response|mixed
	 */
	public function import_item( $request ) {
		$files = $request->get_file_params();

		if ( empty( $files ) ) {
			return new \WP_Error( 'Import failed!', __( 'Your file is empty or invalid', 'zionbuilder-pro' ) );
		}

		$actual_file    = $files['zip'];
		$file_mime_type = $actual_file['type'];
		$accepted_types = [ 'application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed' ];

		if ( ! in_array( $file_mime_type, $accepted_types, true ) ) {
			return new \WP_Error( 'Import failed!', __( 'Your file is not a zip file.', 'zionbuilder-pro' ) );
		}

		$import = Plugin::instance()->icons->upload_icons_package( $files['zip']['tmp_name'] );

		if ( is_wp_error( $import ) ) {
			$import->add_data( [ 'status' => 400 ] );
		}

		return rest_ensure_response( $import );
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_Error|\WP_REST_Response
	 */
	public function delete_item( $request ) {
		$icon_package = $request->get_param( 'icon_package' );

		if ( empty( $icon_package ) ) {
			return new \WP_Error( 'export_failed!', __( 'Please specify the icons package name you want to delete.', 'zionbuilder-pro' ) );
		}

		$delete = Plugin::instance()->icons->delete_icons_package( $icon_package );

		if ( is_wp_error( $delete ) ) {
			$delete->add_data( [ 'status' => 500 ] );
			return $delete;
		}

		return rest_ensure_response( $delete );
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_Error|\WP_REST_Response
	 */
	public function export_item( $request ) {
		$icon_package = $request->get_param( 'icon_package' );

		if ( empty( $icon_package ) ) {
			return new \WP_Error( 'export_failed!', __( 'Please specify the icons package name you want to export.', 'zionbuilder-pro' ) );
		}

		$exported_package_name = Plugin::instance()->icons->create_icons_package( $icon_package );

		if ( is_wp_error( $exported_package_name ) ) {
			$exported_package_name->add_data( [ 'status' => 500 ] );
			return $exported_package_name;
		}

		$download = Plugin::instance()->icons->download_icons_package( $icon_package );

		if ( is_wp_error( $download ) ) {
			$download->add_data( [ 'status' => 500 ] );
			return $download;
		}

		return rest_ensure_response( $download );
	}
}
