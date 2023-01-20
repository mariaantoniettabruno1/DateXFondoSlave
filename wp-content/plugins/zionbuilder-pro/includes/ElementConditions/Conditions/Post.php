<?php

namespace ZionBuilderPro\ElementConditions\Conditions;

use ZionBuilderPro\ElementConditions\ConditionsBase;
use ZionBuilderPro\ElementConditions\ElementConditions;

class Post extends ConditionsBase {
	public static function init_conditions() {
		self::register_groups();
		self::register_conditions();
	}

	public static function register_groups() {
		// Register groups
		ElementConditions::register_condition_group(
			'post',
			[
				'name' => esc_html__( 'Post', 'zionbuilder-pro' ),
			]
		);
	}

	public static function register_conditions() {
		ElementConditions::register_condition(
			'post/post',
			[
				'group'    => 'post',
				'name'     => esc_html__( 'Post', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_post' ],
				'form'     => [
					'operator'  => [
						'type'    => 'select',
						'options' => self::get_operators( [ 'equals', 'not_equals' ] ),
					],
					'post_type' => [
						'type'       => 'select',
						'rest'       => 'v1/conditions/post/post_types',
						'filterable' => true,
					],
					'post_id'   => [
						'type'       => 'select',
						'rest'       => 'v1/conditions/post/post?post_type=${post_type}',
						'requires'   => 'post_type',
						'filterable' => true,
					],
				],
			]
		);

		ElementConditions::register_condition(
			'post/post_type',
			[
				'group'    => 'post',
				'name'     => esc_html__( 'Post type', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_post_type' ],
				'form'     => [
					'operator'  => [
						'type'    => 'select',
						'options' => self::get_operators( [ 'equals', 'not_equals' ] ),
					],
					'post_type' => [
						'type' => 'select',
						'rest' => 'v1/conditions/post/post_types',
					],
				],
			]
		);

		ElementConditions::register_condition(
			'post/post_parent',
			[
				'group'    => 'post',
				'name'     => esc_html__( 'Post parent', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_post_parent' ],
				'form'     => [
					'operator'  => [
						'type'    => 'select',
						'options' => self::get_operators( [ 'equals', 'not_equals' ] ),
					],
					'post_type' => [
						'type'       => 'select',
						'rest'       => 'v1/conditions/post/post_types?hierarchical=1',
						'filterable' => true,
					],
					'post_id'   => [
						'type'       => 'select',
						'rest'       => 'v1/conditions/post/post?post_type=${post_type}',
						'requires'   => 'post_type',
						'filterable' => true,
					],
				],
			]
		);

		ElementConditions::register_condition(
			'post/post_title',
			[
				'group'    => 'post',
				'name'     => esc_html__( 'Post title', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_post_title' ],
				'form'     => [
					'operator' => [
						'type'    => 'select',
						'options' => self::get_operators(
							[
								'is_set',
								'is_not_set',
								'equals',
								'not_equals',
								'starts_with',
								'ends_with',
								'contains',
								'does_not_contain',
							]
						),
					],
					'text'     => [
						'type'     => 'text',
						'requires' => [
							[
								'option_id' => 'operator',
								'operator'  => 'not_in',
								'value'     => [
									'is_set',
									'is_not_set',
								],
							],
						],
					],
				],
			]
		);

		ElementConditions::register_condition(
			'post/post_excerpt',
			[
				'group'    => 'post',
				'name'     => esc_html__( 'Post excerpt', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_post_excerpt' ],
				'form'     => [
					'operator' => [
						'type'    => 'select',
						'options' => self::get_operators(
							[
								'is_set',
								'is_not_set',
								'equals',
								'not_equals',
								'starts_with',
								'ends_with',
								'contains',
								'does_not_contain',
							]
						),
					],
					'text'     => [
						'type' => 'text',
					],
				],
			]
		);

		ElementConditions::register_condition(
			'post/post_content',
			[
				'group'    => 'post',
				'name'     => esc_html__( 'Post content', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_post_content' ],
				'form'     => [
					'operator' => [
						'type'    => 'select',
						'options' => self::get_operators(
							[
								'is_set',
								'is_not_set',
								'equals',
								'not_equals',
								'starts_with',
								'ends_with',
								'contains',
								'does_not_contain',
							]
						),
					],
					'text'     => [
						'type'     => 'text',
						'requires' => [
							[
								'option_id' => 'operator',
								'operator'  => 'not_in',
								'value'     => [
									'is_set',
									'is_not_set',
								],
							],
						],
					],
				],
			]
		);

		ElementConditions::register_condition(
			'post/post_status',
			[
				'group'    => 'post',
				'name'     => esc_html__( 'Post status', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_post_status' ],
				'form'     => [
					'operator'    => [
						'type'    => 'select',
						'options' => self::get_operators(
							[
								'equals',
								'not_equals',
							]
						),
					],
					'post_status' => [
						'type'       => 'select',
						'rest'       => 'v1/conditions/post/post_status',
						'filterable' => true,
					],
				],
			]
		);

		ElementConditions::register_condition(
			'post/post_featured_image',
			[
				'group'    => 'post',
				'name'     => esc_html__( 'Post featured image', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_post_featured_image' ],
				'form'     => [
					'operator' => [
						'type'    => 'select',
						'options' => self::get_operators(
							[
								'is_set',
								'is_not_set',
							]
						),
					],
				],
			]
		);

		ElementConditions::register_condition(
			'post/post_comment_number',
			[
				'group'    => 'post',
				'name'     => esc_html__( 'Post comment number', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_post_comment_number' ],
				'form'     => [
					'operator' => [
						'type'    => 'select',
						'options' => self::get_operators(
							[
								'equals',
								'not_equals',
								'greater',
								'lower',
								'greater_or_equal',
								'lower_or_equal',
							]
						),
					],
					'number'   => [
						'type' => 'text',
					],
				],
			]
		);

		ElementConditions::register_condition(
			'post/post_template',
			[
				'group'    => 'post',
				'name'     => esc_html__( 'Post template', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_post_template' ],
				'form'     => [
					'operator' => [
						'type'    => 'select',
						'options' => self::get_operators(
							[
								'equals',
								'not_equals',
							]
						),
					],
					'template' => [
						'type'       => 'select',
						'rest'       => 'v1/conditions/post/templates',
						'filterable' => true,
					],
				],
			]
		);

		ElementConditions::register_condition(
			'post/post_taxonomy_term',
			[
				'group'    => 'post',
				'name'     => esc_html__( 'Post taxonomy term', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_post_taxonomy_term' ],
				'form'     => [
					'operator' => [
						'type'    => 'select',
						'options' => self::get_operators(
							[
								'equals',
								'not_equals',
							]
						),
					],
					'taxonomy' => [
						'type'       => 'select',
						'rest'       => 'v1/conditions/post/taxonomies',
						'filterable' => true,
					],
					'term_id'  => [
						'type'       => 'select',
						'rest'       => 'v1/conditions/post/terms?taxonomy=${taxonomy}',
						'filterable' => true,
						'requires'   => 'taxonomy',
					],
				],
			]
		);

		ElementConditions::register_condition(
			'post/custom_field',
			[
				'group'    => 'post',
				'name'     => esc_html__( 'Post custom field', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_post_custom_field' ],
				'form'     => [
					'key'      => [
						'type'        => 'text',
						'placeholder' => esc_html__( 'field key', 'zionbuilder-pro' ),
					],
					'operator' => [
						'type'    => 'select',
						'options' => self::get_operators(
							[
								'equals',
								'not_equals',
								'starts_with',
								'ends_with',
								'contains',
								'does_not_contain',
								'is_set',
								'is_not_set',
								'greater',
								'lower',
								'greater_or_equal',
								'lower_or_equal',
							]
						),
					],

					'value'    => [
						'type'        => 'text',
						'placeholder' => esc_html__( 'field value', 'zionbuilder-pro' ),
						'requires'    => [
							[
								'option_id' => 'operator',
								'operator'  => 'not_in',
								'value'     => [
									'is_set',
									'is_not_set',
								],
							],
						],
					],
				],
			]
		);
	}


	public static function validate_post( $settings ) {
		$post = self::get_post();

		return self::validate(
			[
				'operator'      => $settings['operator'],
				'saved_value'   => isset( $settings['post_id'] ) ? intval( $settings['post_id'] ) : false,
				'current_value' => $post ? $post->ID : false,
			]
		);
	}


	/**
	 * Validates if the current post is of a specific post type
	 *
	 * @param array $settings
	 *
	 * @return boolean
	 */
	public static function validate_post_type( $settings ) {
		return self::validate(
			[
				'operator'      => $settings['operator'],
				'saved_value'   => $settings['post_type'],
				'current_value' => get_post_type(),
			]
		);
	}

	/**
	 * Validates if the post parent is eqaual to the saved value
	 *
	 * @param array $settings
	 *
	 * @return boolean
	 */
	public static function validate_post_parent( $settings ) {
		$post = self::get_post();

		if ( $post ) {
			$post_parent = \get_post_parent();

			return self::validate(
				[
					'operator'      => $settings['operator'],
					'saved_value'   => isset( $settings['post_id'] ) ? intval( $settings['post_id'] ) : false,
					'current_value' => $post_parent ? $post_parent->ID : false,
				]
			);
		}

		return false;
	}

	/**
	 * Validates if post title passed the configured conditions
	 *
	 * @param array $settings
	 *
	 * @return boolean
	 */
	public static function validate_post_title( $settings ) {
		$post = self::get_post();

		if ( $post ) {
			return self::validate(
				[
					'operator'      => $settings['operator'],
					'saved_value'   => $settings['text'],
					'current_value' => get_the_title( $post ),
				]
			);
		}

		return false;
	}

	/**
	 * Validates if post excerpt passed the configured conditions
	 *
	 * @param array $settings
	 *
	 * @return boolean
	 */
	public static function validate_post_excerpt( $settings ) {
		$post = self::get_post();

		if ( $post ) {
			return self::validate(
				[
					'operator'      => $settings['operator'],
					'saved_value'   => $settings['text'],
					'current_value' => get_the_excerpt( $post ),
				]
			);
		}

		return false;
	}


	/**
	 * Validates if post content passed the configured conditions
	 *
	 * @param array $settings
	 *
	 * @return boolean
	 */
	public static function validate_post_content( $settings ) {
		$post = self::get_post();

		if ( $post ) {
			return self::validate(
				[
					'operator'      => $settings['operator'],
					'saved_value'   => $settings['text'],
					'current_value' => get_the_content( $post ),
				]
			);
		}

		return false;
	}

	/**
	 * Validates if the post status matches the configure condition
	 *
	 * @param array $settings
	 * @return boolean
	 */
	public static function validate_post_status( $settings ) {
		$post = self::get_post();

		if ( $post ) {
			return self::validate(
				[
					'operator'      => $settings['operator'],
					'saved_value'   => $settings['post_status'],
					'current_value' => get_post_status( $post ),
				]
			);
		}

		return false;
	}

	/**
	 * Validates if the post status matches the configure condition
	 *
	 * @param array $settings
	 *
	 * @return boolean
	 */
	public static function validate_post_comment_number( $settings ) {
		$post = self::get_post();

		if ( $post ) {
			return self::validate(
				[
					'operator'      => $settings['operator'],
					'saved_value'   => isset( $settings['number'] ) ? intval( $settings['number'] ) : false,
					'current_value' => get_comments_number( $post ),
				]
			);
		}

		return false;
	}

	/**
	 * Validates if the post has a featured image or not
	 *
	 * @param array $settings
	 *
	 * @return boolean
	 */
	public static function validate_post_featured_image( $settings ) {
		$post = self::get_post();

		if ( $post ) {
			return self::validate(
				[
					'operator'      => $settings['operator'],
					'saved_value'   => true,
					'current_value' => \has_post_thumbnail( $post ),
				]
			);
		}

		return false;
	}

	/**
	 * Validates if the post has a specific template set
	 *
	 * @param array $settings
	 *
	 * @return boolean
	 */
	public static function validate_post_template( $settings ) {
		$post = self::get_post();

		if ( $post ) {
			return self::validate(
				[
					'operator'      => $settings['operator'],
					'saved_value'   => $settings['template'],
					'current_value' => get_page_template_slug( $post ),
				]
			);
		}

		return false;
	}

	/**
	 * Validates if the post has a specific taxonomy term
	 *
	 * @param array $settings
	 *
	 * @return boolean
	 */
	public static function validate_post_taxonomy_term( $settings ) {
		$post = self::get_post();

		if ( ! isset( $settings['term_id'] ) || ! isset( $settings['taxonomy'] ) ) {
			return;
		}

		if ( $post ) {
			$has_term = has_term( intval( $settings['term_id'] ), $settings['taxonomy'], $post->ID );
			return $settings['operator'] === 'equals' ? $has_term : ! $has_term;
		}

		return false;
	}

	/**
	 * Validates if the post has a specific custom field
	 *
	 * @param array $settings
	 *
	 * @return boolean
	 */
	public static function validate_post_custom_field( $settings ) {
		$post = self::get_post();

		if ( $post ) {
			$post_meta = get_post_meta( $post->ID, $settings['key'], true );

			return self::validate(
				[
					'operator'      => $settings['operator'],
					'saved_value'   => $settings['value'],
					'current_value' => $post_meta,
				]
			);

		}

		return false;
	}
}
