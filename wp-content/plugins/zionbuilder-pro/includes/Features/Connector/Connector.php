<?php

namespace ZionBuilderPro\Features\Connector;

use ZionBuilder\Settings;
use ZionBuilderPro\Features\Connector\Sources\ExternalSource;
use ZionBuilderPro\Features\Connector\OptionsSchema;
use ZionBuilderPro\Features\Connector\Api\Api;

class Connector {
	public function __construct() {
		add_filter( 'zionbuilder/library/register_sources', [ $this, 'add_external_sources' ] );
		add_filter( 'zionbuilderpro/admin/js_data', [ $this, 'add_options_schema_in_admin' ] );
		add_action( 'zionbuilder/rest_api/register_controllers', [ $this, 'register_library_share_rest_controller' ] );

		// Check to see if the templates are public
		add_filter( 'zionbuilder/permissions/user_allowed/view_templates', [ $this, 'user_can_view_templates' ] );
	}

	public static function get_remote_sources() {
		return Settings::get_value( 'library_share.library_sources', [] );
	}

	public function add_external_sources( $library_instance ) {
		// Add remote library sources
		$remote_sources = self::get_remote_sources();

		foreach ( $remote_sources as $remote_source ) {
			if ( ! isset( $remote_source['name'] ) || ! isset( $remote_source['url'] ) || ! isset( $remote_source['id'] ) ) {
				continue;
			}

			$library_instance->register_source(
				new ExternalSource(
					[
						'name'              => $remote_source['name'],
						'id'                => $remote_source['id'],
						'external_url'      => $remote_source['url'],
						'external_password' => isset( $remote_source['password'] ) ? $remote_source['password'] : false,
					]
				)
			);

		}
	}

	/**
	 * Returns the remote api endpoint URL
	 *
	 * @param string $domain
	 *
	 * @return string
	 */
	public static function get_remote_source_url() {
		return get_rest_url( null, 'zionbuilder-pro/v1/connector/library/get-items-and-categories' );
	}

	/**
	 * Will add the library share options schema to the admin data so we can read it from JS
	 *
	 * @param array $data
	 * @return array
	 */
	public function add_options_schema_in_admin( $data ) {
		$data['schemas']['library_share'] = OptionsSchema::get_schema();

		return $data;
	}

	public function register_library_share_rest_controller( $rest_api_instance ) {
		$rest_api_instance->register_controller( new Api() );
	}

	public static function can_access_library( $user_password = null ) {
		$library_share_enabled = Settings::get_value( 'library_share.enable_library_share', false );

		if ( ! $library_share_enabled ) {
			return new \WP_Error( 'connector_forbidden', esc_html__( 'Library share is not enabled.', 'zionbuilder-pro' ), [ 'status' => $this->authorization_status_code() ] );
		}

		$db_password = Settings::get_value( 'library_share.password', '' );

		if ( ! empty( $db_password ) ) {
			if ( md5( $db_password ) !== $user_password ) {
				return new \WP_Error( 'connector_forbidden', esc_html__( 'You do not have access to the shared library.', 'zionbuilder-pro' ), [ 'status' => $this->authorization_status_code() ] );
			}
		}

		return true;
	}


	/**
	 * Checks to see if the user can view the templates
	 *
	 * This is needed so we can share the libraries between multiple instances of websites
	 *
	 * @param boolean $user_can Current visibility state
	 *
	 * @return boolean True if the current user can view templates
	 */
	public function user_can_view_templates( $user_can ) {
		// Check to see if the library is active
		if ( Settings::get_value( 'library_share.enable_library_share', false ) === true ) {
			$user_can = true;
		}

		return $user_can;
	}
}
