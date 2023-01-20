<?php

namespace ZionBuilderPro\Api\RestControllers;

use ZionBuilderPro\Api\RestApiController;
use ZionBuilderPro\MegaMenu as MegaMenuMain;
use ZionBuilder\Plugin as FreePlugin;
use ZionBuilder\Templates;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class MegaMenu
 *
 * @package ZionBuilderPro\Api\RestControllers
 */
class MegaMenu extends RestApiController {
	protected $namespace = 'zionbuilder-pro/v1';
	protected $base      = 'mega-menu';

	/**
	 * Register routes
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->base . '/(?P<id>[\d]+)', [
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_item' ],
				'args'   => [
					'id' => [
						'description' => __( 'The id for the menu we want to retrieve the settings for.', 'zionbuilder' ),
						'type'        => 'integer',
					],
				],
				'permission_callback' => [ $this, 'get_item_permissions_check' ],
			],
			[
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'update_item' ],
				'args'   => [
					'id' => [
						'description' => __( 'The id for the menu we want to retrieve the settings for.', 'zionbuilder' ),
						'type'        => 'integer',
					],
				],
				'permission_callback' => [ $this, 'get_item_permissions_check' ],
			],
			'schema' => [ $this, 'get_public_item_schema' ],
		] );

		register_rest_route( $this->namespace, '/' . $this->base . '/(?P<id>[\d]+)/enable', [
			[
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'enable_mega_menu' ],
				'args'   => [
					'id' => [
						'description' => __( 'The id for the menu we want to retrieve the settings for.', 'zionbuilder' ),
						'type'        => 'integer',
					],
				],
				'permission_callback' => [ $this, 'get_item_permissions_check' ],
			],
			'schema' => [ $this, 'get_public_item_schema' ],
		] );
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
	public function get_item( $request ) {
		$menu_item_id = $request->get_param('id');
		return rest_ensure_response( MegaMenuMain::get_config_for_item($menu_item_id) );
	}

	public function update_item($request) {
		$menu_item_id = $request->get_param('id');
		$config = $request->get_param('config');
		return rest_ensure_response( MegaMenuMain::update_config_for_item($menu_item_id, $config) );
	}

	public function enable_mega_menu($request) {
		$menu_item_id = $request->get_param('id');
		$zb_template_id = MegaMenuMain::get_pagebuilder_template($menu_item_id);

		// Check to see if we have a mega menu id saved
		if(!$zb_template_id) {
			$post_title = sprintf('%s %s', esc_html__('Mega menu template for', 'zionbuilder-pro'), $menu_item_id);
			$template_config = [
				'template_type' => 'mega_menu_item',
			];

			// Create a new template
			$zb_template_id = Templates::create_template(
				$post_title,
				$template_config
			);

			// Check to see if the post was succesfully created
			if ( is_wp_error( $zb_template_id ) ) {
				if ( 'db_insert_error' === $zb_template_id->get_error_code() ) {
					$zb_template_id->add_data( [ 'status' => 500 ] );
				} else {
					$zb_template_id->add_data( [ 'status' => 400 ] );
				}

				return $zb_template_id;
			}

			// Save the template for this menu item
			MegaMenuMain::set_pagebuilder_template( $menu_item_id, $zb_template_id );
		}

		$template_instance = FreePlugin::instance()->post_manager->get_post_type_instance( $zb_template_id );
		$template_instance->set_builder_status( true );

		return rest_ensure_response( [
			'edit_url' => $template_instance->get_edit_url()
		] );

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
