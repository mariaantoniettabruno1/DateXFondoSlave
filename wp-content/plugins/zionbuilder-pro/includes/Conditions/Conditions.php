<?php

namespace ZionBuilderPro\Conditions;

use ZionBuilderPro\Utils;
use ZionBuilderPro\Plugin;
use ZionBuilderPro\Conditions\RestController;
use ZionBuilder\Templates as ZBFreeTemplates;

class Conditions {
	const OPTION_ID_SEPARATOR = ':ZNPB_SEP:';

	private $categories      = [];
	private $conditions      = null;
	private $conditions_data = [];

	public function __construct() {
		add_action( 'zionbuilder/rest_api/register_controllers', [ $this, 'register_rest_controller' ] );
	}

	public function register_rest_controller( $rest_manager ) {
		$rest_manager->register_controller( new RestController() );
	}

	/**
	 * Will return the registered conditions
	 *
	 * @return array
	 */
	public function get_conditions() {
		if ( null === $this->conditions ) {
			$this->register_default_categories();
			$this->register_default_conditions();

			do_action( 'zionbuilder-pro/conditions/get_conditions', $this );
		}

		return $this->conditions;
	}

	/**
	 * Returns all conditions without the validation parameter
	 *
	 * @return void
	 */
	public function get_conditions_for_admin() {
		$conditions = $this->get_conditions();

		foreach ( $conditions as &$condition_config ) {
			unset( $condition_config['validation'] );
		}

		return $conditions;
	}


	/**
	 * Will register the default categories
	 *
	 * @return void
	 */
	public function register_default_categories() {
		$this->register_category(
			[
				'name'     => esc_html__( 'General', 'zionbuilder-pro' ),
				'id'       => 'general',
				'priority' => 10,
			]
		);

		$this->register_category(
			[
				'name'     => esc_html__( 'Visitor related', 'zionbuilder-pro' ),
				'id'       => 'visitor',
				'priority' => 90,
			]
		);
	}

	/**
	 * Will return the conditions categories
	 *
	 * @return array
	 */
	public function get_categories() {
		return $this->categories;
	}

	/**
	 * Will register default conditions
	 *
	 * @return void
	 */
	public function register_default_conditions() {
		$this->register_condition(
			[
				'id'         => 'entire_site',
				'name'       => esc_html__( 'Entire Site', 'zionbuilder-pro' ),
				'category'   => 'general',
				'validation' => '__return_true',
			]
		);

		$this->register_condition(
			[
				'id'         => 'is_homepage',
				'name'       => esc_html__( 'Homepage', 'zionbuilder-pro' ),
				'category'   => 'general',
				'validation' => [ 'ZionBuilderPro\Conditions\Validations', 'is_homepage' ],
			]
		);

		$this->register_condition(
			[
				'id'         => 'is_search',
				'name'       => esc_html__( 'All search pages', 'zionbuilder-pro' ),
				'category'   => 'general',
				'validation' => [ 'ZionBuilderPro\Conditions\Validations', 'is_search' ],
			]
		);

		$this->register_condition(
			[
				'id'         => 'is_404',
				'name'       => esc_html__( '404 page', 'zionbuilder-pro' ),
				'category'   => 'general',
				'validation' => [ 'ZionBuilderPro\Conditions\Validations', 'is_404' ],
			]
		);
		$this->register_condition(
			[
				'id'         => 'is_singular',
				'name'       => esc_html__( 'All single pages', 'zionbuilder-pro' ),
				'category'   => 'general',
				'validation' => [ 'ZionBuilderPro\Conditions\Validations', 'is_singular' ],
			]
		);
		$this->register_condition(
			[
				'id'         => 'is_archive',
				'name'       => esc_html__( 'All archive pages', 'zionbuilder-pro' ),
				'category'   => 'general',
				'validation' => [ 'ZionBuilderPro\Conditions\Validations', 'is_archive' ],
			]
		);

		$this->register_condition(
			[
				'id'         => 'is_author_archive',
				'name'       => esc_html__( 'All author archive pages', 'zionbuilder-pro' ),
				'category'   => 'general',
				'validation' => [ 'ZionBuilderPro\Conditions\Validations', 'is_author_archive' ],
			]
		);

		$this->register_condition(
			[
				'id'                 => 'is_author_archive_user',
				'name'               => esc_html__( 'Specific author archive page', 'zionbuilder-pro' ),
				'category'           => 'general',
				'validation'         => [ 'ZionBuilderPro\Conditions\Validations', 'is_author_archive_user' ],
				'options_callback'   => [
					'callback' => [ $this, 'get_users' ],
				],
				'searchable'         => true,
				'search_placeholder' => esc_html__( 'Search users', 'zionbuilder-pro' ),
				'not_found_text'     => esc_html__( 'No user found', 'zionbuilder-pro' ),
				'no_more_items_text' => esc_html__( 'No more user to load', 'zionbuilder-pro' ),
			]
		);

		$this->register_condition(
			[
				'id'         => 'logged_in',
				'name'       => esc_html__( 'All logged in users', 'zionbuilder-pro' ),
				'category'   => 'visitor',
				'validation' => [ 'ZionBuilderPro\Conditions\Validations', 'is_logged_in' ],
			]
		);

		$this->register_condition(
			[
				'id'         => 'logged_out',
				'name'       => esc_html__( 'All logged out users', 'zionbuilder-pro' ),
				'category'   => 'visitor',
				'validation' => [ 'ZionBuilderPro\Conditions\Validations', 'is_not_logged_in' ],
			]
		);

		// Register post type related
		$post_types = get_post_types(
			[
				'public' => true,
			],
			'objects'
		);

		$excluded_post_types = apply_filters( 'zionbuilderpro/conditions/exluded_post_types', [ ZBFreeTemplates::TEMPLATE_POST_TYPE ] );

		foreach ( $excluded_post_types as $excluded_post_type ) {
			if ( isset( $post_types[$excluded_post_type] ) ) {
				unset( $post_types[$excluded_post_type] );
			}
		}

		foreach ( $post_types as $post_type ) {
			$taxonomies = get_object_taxonomies( $post_type->name, 'objects' );

			$this->register_category(
				[
					'name' => $post_type->label,
					'id'   => $post_type->name,
				]
			);

			$this->register_condition(
				[
					'id'         => implode( self::OPTION_ID_SEPARATOR, [ 'singular', $post_type->name, 'all' ] ),
					'name'       => esc_html__( 'All', 'zionbuilder-pro' ) . ' ' . $post_type->label,
					'category'   => $post_type->name,
					'validation' => [ 'ZionBuilderPro\Conditions\Validations', 'in_single_post_type' ],
				]
			);

			// Check for archive
			if ( $post_type->name === 'post' || $post_type->has_archive ) {
				$this->register_condition(
					[
						'id'         => implode( self::OPTION_ID_SEPARATOR, [ 'archive', $post_type->name, 'all' ] ),
						'name'       => $post_type->label . ' ' . esc_html__( 'archive page', 'zionbuilder-pro' ),
						'category'   => $post_type->name,
						'validation' => [ 'ZionBuilderPro\Conditions\Validations', 'in_archive_post_type' ],
					]
				);

				$this->register_condition(
					[
						'id'         => implode( self::OPTION_ID_SEPARATOR, [ 'date_archive', $post_type->name, 'all' ] ),
						'name'       => $post_type->label . ' ' . esc_html__( 'date archive page', 'zionbuilder-pro' ),
						'category'   => $post_type->name,
						'validation' => [ 'ZionBuilderPro\Conditions\Validations', 'in_date_archive' ],
					]
				);
			}

			// Specific post type item
			$this->register_condition(
				[
					'id'                 => implode( self::OPTION_ID_SEPARATOR, [ 'singular', $post_type->name ] ),
					'name'               => esc_html__( 'Specific', 'zionbuilder-pro' ) . ' ' . $post_type->labels->name,
					'category'           => $post_type->name,
					'options_callback'   => [
						'callback'  => [ $this, 'get_post_type_items' ],
						'arguments' => [ 'post_type' => $post_type->name ],
					],
					'searchable'         => true,
					'search_placeholder' => sprintf( esc_html__( 'Search %s', 'zionbuilder-pro' ), $post_type->labels->name ),
					'not_found_text'     => sprintf( esc_html__( 'No %s found', 'zionbuilder-pro' ), $post_type->labels->name ),
					'no_more_items_text' => sprintf( esc_html__( 'No more %s to load', 'zionbuilder-pro' ), $post_type->labels->name ),
					'validation'         => [ 'ZionBuilderPro\Conditions\Validations', 'in_single_post_type' ],
				]
			);

			// TODO: implement custom search by post_type keyword
			// $this->register_condition(
			//  [
			//      'id'       => implode( self::OPTION_ID_SEPARATOR, [ 'search', $post_type->name ] ),
			//      'name'     => $post_type->label . ' ' . esc_html__( 'search page', 'zionbuilder-pro' ),
			//      'category' => $post_type->name,
			//  ]
			// );

			foreach ( $taxonomies as $taxonomy ) {
				if ( ! $taxonomy->show_ui ) {
					continue;
				}

				// Single in specific taxonomy
				$this->register_condition(
					[
						'id'                 => implode( self::OPTION_ID_SEPARATOR, [ 'singular', $post_type->name, 'taxonomy', $taxonomy->name ] ),
						'name'               => $post_type->labels->name . ' ' . esc_html__( 'in specific', 'zionbuilder-pro' ) . ' ' . $taxonomy->labels->name,
						'category'           => $post_type->name,
						'options_callback'   => [
							'callback'  => [ $this, 'get_post_type_taxonomies' ],
							'arguments' => [
								'post_type' => $post_type->name,
								'taxonomy'  => $taxonomy->name,
							],
						],
						'searchable'         => true,
						'search_placeholder' => sprintf( esc_html__( 'Search %s', 'zionbuilder-pro' ), $taxonomy->labels->name ),
						'not_found_text'     => sprintf( esc_html__( 'No %s found', 'zionbuilder-pro' ), $taxonomy->labels->name ),
						'no_more_items_text' => sprintf( esc_html__( 'No more %s to load', 'zionbuilder-pro' ), $taxonomy->labels->name ),
						'validation'         => [ 'ZionBuilderPro\Conditions\Validations', 'in_single_post_type_in_taxonomy' ],
					]
				);

				$this->register_condition(
					[
						'id'         => implode( self::OPTION_ID_SEPARATOR, [ 'archive', $taxonomy->name, 'all' ] ),
						'name'       => $taxonomy->label . ' ' . esc_html__( 'Archive', 'zionbuilder-pro' ),
						'category'   => $post_type->name,
						'validation' => [ 'ZionBuilderPro\Conditions\Validations', 'in_taxonomy_archive' ],
					]
				);

				// Specific post type item
				$this->register_condition(
					[
						'id'                 => implode( self::OPTION_ID_SEPARATOR, [ 'archive', $taxonomy->name ] ),
						'name'               => esc_html__( 'Specific', 'zionbuilder-pro' ) . ' ' . $taxonomy->labels->name,
						'category'           => $post_type->name,
						'options_callback'   => [
							'callback'  => [ $this, 'get_taxonomy_items' ],
							'arguments' => [ 'taxonomy' => $taxonomy->name ],
						],
						'searchable'         => true,
						'search_placeholder' => sprintf( esc_html__( 'Search %s', 'zionbuilder-pro' ), $taxonomy->labels->name ),
						'not_found_text'     => sprintf( esc_html__( 'No %s found', 'zionbuilder-pro' ), $taxonomy->labels->name ),
						'no_more_items_text' => sprintf( esc_html__( 'No more %s to load', 'zionbuilder-pro' ), $taxonomy->labels->name ),
						'validation'         => [ 'ZionBuilderPro\Conditions\Validations', 'in_taxonomy_archive' ],
					]
				);
			}
		}

	}

	public static function get_condition_id_data( $condition_id ) {
		return explode( self::OPTION_ID_SEPARATOR, $condition_id );
	}

	/**
	 * Will register a condition
	 *
	 * @param array $condition
	 *
	 * @return void
	 */
	public function register_condition( $condition ) {
		if ( empty( $condition['name'] ) || empty( $condition['id'] ) || empty( $condition['category'] ) ) {
			throw new \Exception( 'Condition name, id and category are required.' );
		}

		$this->conditions[$condition['id']] = $condition;
	}


	/**
	 * Return a single condition config or false if it doesn't exists
	 *
	 * @param string $condition_id
	 *
	 * @return array|false
	 */
	public function get_condition( $condition_id ) {
		// Register conditions
		$this->get_conditions();

		if ( ! isset( $this->conditions[$condition_id] ) ) {
			return false;
		}

		return $this->conditions[$condition_id];
	}


	/**
	 * Will register a new conditions category
	 *
	 * @param array $category_config
	 *
	 * @return void
	 */
	public function register_category( $category_config ) {
		if ( empty( $category_config['name'] ) || empty( $category_config['id'] ) ) {
			throw new \Exception( 'Category name and id are required.' );
		}

		$this->categories[$category_config['id']] = $category_config;
	}

	/**
	 * Will validate the conditions
	 *
	 * @return boolean
	 */
	public function validate( $page_request, $config ) {
		if ( isset( $config['hide_on'] ) ) {
			foreach ( $config['hide_on'] as $condition_id => $condition_value ) {
				if ( $this->validate_condition( $page_request, $condition_id, $condition_value ) ) {
					return false;
				}
			}
		}

		// Check show on rules
		if ( isset( $config['show_on'] ) ) {
			foreach ( $config['show_on'] as $condition_id => $condition_value ) {
				if ( $this->validate_condition( $page_request, $condition_id, $condition_value ) ) {
					return true;
				}
			}
		}

		return false;
	}

	public function validate_condition( $page_request, $condition_id, $condition_value ) {
		// Get the condition config
		$condition = $this->get_condition( $condition_id );
		if ( $condition && isset( $condition['validation'] ) && is_callable( $condition['validation'] ) ) {
			return call_user_func( $condition['validation'], $page_request, $condition_id, $condition_value );
		}

		return false;
	}

	public function get_condition_options_callback( $condition ) {
		if ( ! isset( $condition['options_callback'] ) ) {
			return false;
		}

		return $condition['options_callback'];
	}

	public function get_condition_options( $condition_id, $arguments = [], $search_keyword = '', $page = 1 ) {
		$condition = $this->get_condition( $condition_id );

		if ( $condition ) {
			$condition_items_callback = $this->get_condition_options_callback( $condition );

			if ( isset( $condition_items_callback['callback'] ) ) {
				$additional_arguments = ! empty( $condition_items_callback['arguments'] ) ? $condition_items_callback['arguments'] : null;
				return call_user_func( $condition_items_callback['callback'], $additional_arguments, $arguments );
			}
		}

		return [];
	}

	public function get_users( $condition_arguments, $extra_arguments, $saved_values = null ) {
		$items          = [];
		$page           = ! empty( $extra_arguments['page'] ) ? $extra_arguments['page'] : 1;
		$search_keyword = ! empty( $extra_arguments['search_keyword'] ) ? $extra_arguments['search_keyword'] : '';

		$users = get_users(
			[
				'paged'   => $page,
				'include' => $saved_values,
				'number'  => 25,
				'search'  => $search_keyword,
			]
		);

		// Normalize array
		if ( is_array( $users ) ) {
			foreach ( $users as $user ) {
				$items[] = [
					'id'    => $user->ID,
					'title' => $user->user_nicename,
				];
			}
		}

		return $items;
	}

	public function get_post_type_items( $condition_arguments, $extra_arguments, $saved_values = null ) {
		$items          = [];
		$post_type      = $condition_arguments['post_type'];
		$page           = ! empty( $extra_arguments['page'] ) ? $extra_arguments['page'] : 1;
		$search_keyword = ! empty( $extra_arguments['search_keyword'] ) ? $extra_arguments['search_keyword'] : '';

		$posts = get_posts(
			[
				'post_status'    => 'any',
				'post_type'      => $post_type,
				'paged'          => $page,
				'post__in'       => $saved_values,
				'posts_per_page' => ! empty( $saved_values ) ? -1 : 25,
				's'              => $search_keyword,
			]
		);

		// Normalize array
		if ( is_array( $posts ) ) {
			foreach ( $posts as $post ) {
				$items[] = [
					'id'    => $post->ID,
					'title' => $post->post_title,
				];
			}
		}

		return $items;
	}

	public function get_post_type_taxonomies( $condition_arguments, $extra_arguments, $saved_values = null ) {
		$items = [];

		$taxonomy       = $condition_arguments['taxonomy'];
		$page           = ! empty( $extra_arguments['page'] ) ? $extra_arguments['page'] : 1;
		$search_keyword = ! empty( $extra_arguments['search_keyword'] ) ? $extra_arguments['search_keyword'] : '';
		$per_page       = 25;

		$terms = get_terms(
			[
				'post_status' => 'any',
				'taxonomy'    => $taxonomy,
				'paged'       => $page,
				'include'     => $saved_values,
				'number'      => ! empty( $saved_values ) ? 0 : 25,
				'offset'      => ! empty( $saved_values ) ? 0 : ( $page - 1 ) * $per_page,
				'search'      => $search_keyword,
			]
		);

		// Normalize array
		if ( is_array( $terms ) ) {
			foreach ( $terms as $term ) {
				$items[] = [
					'id'    => $term->term_id,
					'title' => $term->name,
				];
			}
		}

		return $items;
	}

	public function get_taxonomy_items( $condition_arguments, $extra_arguments, $saved_values = null ) {
		$items          = [];
		$taxonomy       = $condition_arguments['taxonomy'];
		$page           = ! empty( $extra_arguments['page'] ) ? $extra_arguments['page'] : 1;
		$search_keyword = ! empty( $extra_arguments['search_keyword'] ) ? $extra_arguments['search_keyword'] : '';
		$per_page       = 25;

		$terms = get_terms(
			[
				'post_status' => 'any',
				'taxonomy'    => $taxonomy,
				'paged'       => $page,
				'include'     => $saved_values,
				'number'      => ! empty( $saved_values ) ? 0 : 25,
				'offset'      => ! empty( $saved_values ) ? 0 : ( $page - 1 ) * $per_page,
				'search'      => $search_keyword,
			]
		);

		// Normalize array
		if ( is_array( $terms ) ) {
			foreach ( $terms as $term ) {
				$items[] = [
					'id'    => $term->term_id,
					'title' => $term->name,
				];
			}
		}

		return $items;
	}

	public function register_conditions_data( $conditions_value ) {
		if ( is_array( $conditions_value ) ) {
			foreach ( $conditions_value as $values ) {
				if ( is_array( $values ) ) {
					foreach ( $values as $condition_id => $value ) {
						$condition = $this->get_condition( $condition_id );

						if ( $condition && isset( $condition['options_callback'] ) && is_array( $value ) ) {
							if ( ! isset( $this->conditions_data[$condition_id] ) || ! is_array( $this->conditions_data[$condition_id] ) ) {
								$this->conditions_data[$condition_id] = [];
							}

							$this->conditions_data[$condition_id] = array_merge( $this->conditions_data[$condition_id], $value );
						}
					}
				}
			}
		}
	}

	public function get_conditions_saved_data() {
		do_action( 'zionbuilderpro/conditions/register_saved_data', $this );

		return $this->get_saved_values_data();
	}

	public function get_saved_values_data() {
		$data = [];

		foreach ( $this->conditions_data as $condition_id => $condition_value ) {
			$condition                = $this->get_condition( $condition_id );
			$condition_items_callback = $this->get_condition_options_callback( $condition );
			$additional_arguments     = ! empty( $condition_items_callback['arguments'] ) ? $condition_items_callback['arguments'] : null;

			$data[$condition_id] = call_user_func( $condition_items_callback['callback'], $additional_arguments, [], $condition_value );
		}

		return $data;
	}
}
