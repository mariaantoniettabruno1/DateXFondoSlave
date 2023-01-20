<?php

namespace ZionBuilderPro\Api;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class RestApiController
 *
 * @package ZionBuilderPro\Api
 */
class RestApiController extends \WP_REST_Controller {
	protected $namespace = 'zionbuilder/v1';
	protected $base      = '';

	public function get_controller_id() {
		return $this->namespace . '/' . $this->base;
	}

	/**
	 * Initialize the class' default functionality
	 */
	public function init() {
		$this->register_routes();
	}

	/**
	 * Register routes
	 */
	public function register_routes() {
	}

	// Sets up the proper HTTP status code for authorization.
	public function authorization_status_code() {
		$status = 401;

		if ( is_user_logged_in() ) {
			$status = 403;
		}

		return $status;
	}

	/**
	 * Check to see whether or not the current user has permissions to trigger the specified $action_name
	 *
	 * @param string $action_name
	 *
	 * @return bool
	 */
	public function userCan( $action_name = '' ) {
		// TODO: Implement the $action_name usage when the new options are implemented
		// Until then, we'll use the default settings
		return current_user_can( 'manage_options' );
	}
}
