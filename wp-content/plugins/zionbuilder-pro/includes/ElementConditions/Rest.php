<?php

namespace ZionBuilderPro\ElementConditions;

use ZionBuilderPro\Api\RestApiController;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class DataSets
 *
 * @package ZionBuilder\Api\RestControllers
 */
class Rest extends RestApiController {
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
	protected $base = 'conditions';

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
			'/' . $this->base . '/post/post',
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_posts' ],
					'permission_callback' => [ $this, 'get_items_permissions_callback' ],
				]
			]
		);


		/**
		 * Returns all sources configuration
		 */
		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/post/post_types',
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_post_types' ],
					'permission_callback' => [ $this, 'get_items_permissions_callback' ],
				]
			]
		);

		/**
		 * Returns all sources configuration
		 */
		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/post/post_status',
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_post_statuses' ],
					'permission_callback' => [ $this, 'get_items_permissions_callback' ],
				]
			]
		);

		/**
		 * Returns all page templates
		 */
		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/post/templates',
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_post_templates' ],
					'permission_callback' => [ $this, 'get_items_permissions_callback' ],
				]
			]
		);

		/**
		 * Returns all taxonomies options
		 */
		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/post/taxonomies',
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_taxonomies' ],
					'permission_callback' => [ $this, 'get_items_permissions_callback' ],
				]
			]
		);

		/**
		 * Returns all taxonomies terms
		 */
		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/post/terms',
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_terms' ],
					'permission_callback' => [ $this, 'get_items_permissions_callback' ],
				]
			]
		);

		/**
		 * Returns all taxonomies terms
		 */
		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/archive/archive',
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_archives' ],
					'permission_callback' => [ $this, 'get_items_permissions_callback' ],
				]
			]
		);


		// Author
		/**
		 * Returns all user roles
		 */
		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/user/roles',
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_user_roles' ],
					'permission_callback' => [ $this, 'get_items_permissions_callback' ],
				]
			]
		);

		/**
		 * Returns all user capabilities
		 */
		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/user/capabilities',
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_user_capabilities' ],
					'permission_callback' => [ $this, 'get_items_permissions_callback' ],
				]
			]
		);
	}

	public function get_items_permissions_callback( $request ) {
		if ( ! $this->userCan( 'element-conditions' ) ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'You do not have permissions to view this resource.', 'zionbuilder-pro' ), [ 'status' => $this->authorization_status_code() ] );
		}

		return true;
	}


	public function get_post_types( $request ) {
		$hierarchical =  $request->get_param('hierarchical');
		$options = [];
		$args = [
			'public' => true,
		];

		if ($hierarchical) {
			$args['hierarchical'] = true;
		}

		$post_types = get_post_types($args, 'objects');

		foreach ( $post_types as $slug => $type ) {
			$options[] = array(
				'name' => $type->labels->singular_name,
				'id' => $slug,
			);
		}


		return rest_ensure_response( $options );
	}

	public function get_posts($request) {
		$options = [];
		$post_type = $request->get_param('post_type');

		$posts = get_posts([
			'post_type' => $post_type ? $post_type : 'post',
			'numberposts' => -1,
		]);

		foreach ($posts as $post) {
			$options[] = array(
				'name' => $post->post_title,
				'id' => $post->ID,
			);
		}

		return $options;
	}

	public function get_post_statuses($request) {
		$options = [];
		$post_statuses = get_post_stati([], 'objects');

		foreach ($post_statuses as $slug => $config) {
			$options[] = array(
				'name' => $config->label,
				'id' =>  $slug,
			);
		}

		return $options;
	}


	public function get_post_templates($request) {
		include_once ABSPATH . 'wp-admin/includes/theme.php';
		$options = [];
		$templates = \get_page_templates();

		foreach ( $templates as $template_name => $slug ) {
			$options[] = array(
				'name' => $template_name,
				'id' => $slug,
			);
		}

		return $options;
	}



	public function get_taxonomies($request) {
		$options = [];

		$post_types = get_post_types([
			'public' => true
		], 'objects');

		foreach ($post_types as $slug => $post_type_object) {
			$taxonomies = get_object_taxonomies($slug, 'objects');

			if (empty($taxonomies)) {
				continue;
			}

			// Add the post type label
			$options[] = [
				'name' => $post_type_object->labels->singular_name,
				'is_label' => true
			];

			foreach ($taxonomies as $slug => $taxonomy_object) {
				$options[] = [
					'name' => $taxonomy_object->labels->singular_name,
					'id' => $slug
				];
			}
		}

		return $options;
	}

	public function get_terms($request) {
		$options = [];
		$taxonomy = $request->get_param( 'taxonomy' );

		$args = [
			'hide_empty' => false,
		];

		if (! empty($taxonomy)) {
			$args['taxonomy'] = $taxonomy;
		}

		$terms = \get_terms($args);

		foreach ( $terms as $term ) {
			$options[] = array(
				'name' => $term->name,
				'id' => $term->term_id,
			);
		}

		return $options;
	}


	public function get_archives($request) {
		$options = [];

		$post_types = get_post_types([
			'public' => true
		], 'objects');

		foreach ($post_types as $slug => $post_type_object) {
			$taxonomies = get_object_taxonomies( $slug, 'objects' );
			$post_type_options = [];

			if (empty( $post_type_object->has_archive ) && empty($taxonomies)) {
				continue;
			}

			// Add general post type archive
			if ( ! empty( $post_type_object->has_archive ) || $slug === 'post' ) {
				$post_type_options[] = [
					'id' => 'post_type/' . $slug,
					'name' => sprintf( _x( '%s Archive', '%s post type singular name', 'zionbuilder-pro' ), $post_type_object->labels->singular_name )
				];
			}

			// Add taxonomy archive
			foreach ($taxonomies as $slug => $taxonomy_object) {
				if ( ! $taxonomy_object->public || ! $taxonomy_object->show_ui ) {
					continue;
				}

				$post_type_options[] = [
					'name' => $post_type_object->labels->singular_name . ' ' . $taxonomy_object->labels->singular_name,
					'id' => 'taxonomy/' . $slug
				];
			}

			// Check to see if we have options
			if ( ! empty( $post_type_options ) ) {
				// Add the label
				$options[] = [
					'name' => $post_type_object->labels->singular_name,
					'is_label' => true
				];

				$options = array_merge( $options, $post_type_options );
			}
		}

		return $options;
	}


	public function get_user_roles($request) {
		global $wp_roles;
		$options = [];

		foreach ( $wp_roles->role_names as $role_id => $name ) {
			$options[] = array(
				'name' => $name,
				'id' => $role_id,
			);
		}

		return $options;
	}

	public function get_user_capabilities($request) {
		global $wp_roles;
		$options = [];

		// Administrator should have all capabilities
		$all_caps = array_keys($wp_roles->roles['administrator']['capabilities']);

		foreach ( $all_caps as $capability ) {
			$options[] = array(
				'name' => $capability,
				'id' => $capability,
			);
		}

		return $options;
	}
}
