<?php

namespace ZionBuilderPro\ElementConditions\Conditions;

use ZionBuilderPro\ElementConditions\ConditionsBase;
use ZionBuilderPro\ElementConditions\ElementConditions;

class Author extends ConditionsBase {
	public static function init_conditions() {
		self::register_groups();
		self::register_conditions();
	}

	public static function register_groups() {
		// Register groups
		ElementConditions::register_condition_group('author', [
			'name' => esc_html__('Author', 'zionbuilder-pro')
		]);
	}

	public static function register_conditions() {
		ElementConditions::register_condition('author/username', [
			'group' => 'author',
			'name' => esc_html__('Author username', 'zionbuilder-pro'),
			'callback' => [get_class(), 'validate_username'],
			'form' => [
				'operator' => [
					'type' => 'select',
					'options' => self::get_operators([
						'equals',
						'not_equals'
					]),
				],
				'username' => [
					'type' => 'text'
				]
			]
		]);

		ElementConditions::register_condition('author/description', [
			'group' => 'author',
			'name' => esc_html__('Author description', 'zionbuilder-pro'),
			'callback' => [get_class(), 'validate_description'],
			'form' => [
				'operator' => [
					'type' => 'select',
					'options' => self::get_operators([
						'is_set',
						'is_not_set',
						'equals',
						'not_equals',
						'starts_with',
						'ends_with',
						'contains',
						'does_not_contain',
					]),
				],
				'description' => [
					'type' => 'text'
				]
			]
		]);

		ElementConditions::register_condition('author/meta', [
			'group' => 'author',
			'name' => esc_html__('Author meta field', 'zionbuilder-pro'),
			'callback' => [get_class(), 'validate_meta_field'],
			'form' => [
				'key' => [
					'type' => 'text',
					'placeholder' => esc_html__('field key', 'zionbuilder-pro')
				],
				'operator' => [
					'type' => 'select',
					'options' => self::get_operators([
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
					]),
				],

				'value' => [
					'type' => 'text',
					'placeholder' => esc_html__('field value', 'zionbuilder-pro'),
					'requires' => [
						[
							'option_id' => 'operator',
							'operator' => 'not_in',
							'value' => [
								'is_set',
								'is_not_set'
							]
						]
					]
				],
			]
		]);

		ElementConditions::register_condition('author/role', [
			'group' => 'author',
			'name' => esc_html__('Author role', 'zionbuilder-pro'),
			'callback' => [get_class(), 'validate_role'],
			'form' => [
				'operator' => [
					'type' => 'select',
					'options' => self::get_operators([
						'equals',
						'not_equals',
					]),
				],
				'role' => [
					'type' => 'select',
					'rest' => 'v1/conditions/user/roles',
					'filterable' => true
				]
			]
		]);


		ElementConditions::register_condition('author/capability', [
			'group' => 'author',
			'name' => esc_html__('Author capability', 'zionbuilder-pro'),
			'callback' => [get_class(), 'validate_capability'],
			'form' => [
				'operator' => [
					'type' => 'select',
					'options' => self::get_operators([
						'equals',
						'not_equals',
					]),
				],
				'capability' => [
					'type' => 'select',
					'rest' => 'v1/conditions/user/capabilities',
					'filterable' => true
				]
			]
		]);
	}


	public static function validate_username($settings) {
		$post = self::get_post();

		if (! $post) {
			return false;
		}

		return self::validate([
			'operator' => $settings['operator'],
			'saved_value' => $settings['username'],
			'current_value' => get_the_author_meta( 'user_login', $post->post_author )
		]);
	}

	public static function validate_description($settings) {
		$post = self::get_post();

		if (! $post) {
			return false;
		}

		return self::validate([
			'operator' => $settings['operator'],
			'saved_value' => $settings['description'],
			'current_value' => get_the_author_meta( 'description', $post->post_author )
		]);
	}

	public static function validate_meta_field($settings) {
		$post = self::get_post();

		if (! $post) {
			return false;
		}

		return self::validate([
			'operator' => $settings['operator'],
			'saved_value' => $settings['value'],
			'current_value' => get_the_author_meta( $settings['key'], $post->post_author )
		]);
	}

	public static function validate_role($settings) {
		$post = self::get_post();

		if ( ! $post) {
			return false;
		}

		$user_id = $post->post_author;
		$user_data = get_userdata( $user_id );

		if (empty( $user_data )) {
			return false;
		}

		$has_role = in_array( $settings['role'], $user_data->roles );
		return $settings['operator'] === 'equals' ? $has_role : ! $has_role;
	}


	public function validate_capability($settings) {
		$post = self::get_post();

		if ( ! $post) {
			return false;
		}

		$user_id = $post->post_author;
		$user_can = user_can($user_id, $settings['capability']);
		return $settings['operator'] === 'equals' ? $user_can : ! $user_can;

	}

}