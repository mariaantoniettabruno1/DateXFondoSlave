<?php

namespace ZionBuilderPro\Api;

use ZionBuilder\Permissions;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class RestApi
 *
 * @package ZionBuilder\Api
 */
class RestApi {

	/**
	 * Holds a reference to all rest controllers classes
	 *
	 * @var array
	 */
	private $controllers = [];


	/**
	 * RestApi constructor.
	 */
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'init_controllers' ] );
		add_filter( 'register_post_type_args', [ $this, 'allow_post_types_in_rest' ] );
		add_filter( 'register_taxonomy_args', [ $this, 'allow_taxonomies_in_rest' ] );

	}

	/**
	 * Allow all post types to show up in rest if the user has permission
	 *
	 * @param array $args
	 * @return array
	 */
	public function allow_post_types_in_rest( $args ) {
		if ( isset( $args['public'] ) && $args['public'] && Permissions::user_allowed_edit() ) {
			$args['show_in_rest'] = true;
		}

		return $args;
	}

	/**
	 * Allow all taxonomies to show up in rest if the user has permission
	 *
	 * @param array $args
	 * @return array
	 */
	public function allow_taxonomies_in_rest( $args ) {
		if ( Permissions::user_allowed_edit() ) {
			$args['show_in_rest'] = true;
		}

		return $args;
	}

	/**
	 * Initialize the registered controllers
	 *
	 * @hooked zionbuilder/rest_api/register_controllers
	 */
	public function init_controllers() {
		$this->register_default_controllers();

		do_action( 'zionbuilder/rest_api/register_controllers', $this );

		foreach ( $this->get_controllers() as $key => $controller ) {
			$controller->init();
		}
	}

	/**
	 * Registers a new Rest API controller
	 *
	 * @param RestApiController $controller_instance
	 *
	 * @return void
	 */
	public function register_controller( $controller_instance ) {
		$this->controllers[] = $controller_instance;
	}


	/**
	 * Get Controllers
	 *
	 * Returns all registers Rest Api controllers
	 *
	 * @return array
	 */
	public function get_controllers() {
		return $this->controllers;
	}

	/**
	 * Register the plugin's default controllers
	 */
	public function register_default_controllers() {
		$controllers = [
			'ZionBuilderPro\Api\RestControllers\AdobeFonts',
			'ZionBuilderPro\Api\RestControllers\Icons',
			'ZionBuilderPro\Api\RestControllers\ZionApi',
			'ZionBuilderPro\Api\RestControllers\WPPageSelector',
			'ZionBuilderPro\Api\RestControllers\WPTerms',
			'ZionBuilderPro\Api\RestControllers\MegaMenu',
		];

		foreach ( $controllers as $controller ) {
			$this->register_controller( new $controller() );
		}
	}
}
