<?php

namespace ZionBuilderPro\ElementConditions\Conditions;

use ZionBuilderPro\ElementConditions\ConditionsBase;
use ZionBuilderPro\ElementConditions\ElementConditions;

class User extends ConditionsBase {
	public static function init_conditions() {
		self::register_groups();
		self::register_conditions();
	}

	public static function register_groups() {
		// Register groups
		ElementConditions::register_condition_group(
			'user',
			[
				'name' => esc_html__( 'User', 'zionbuilder-pro' ),
			]
		);
	}

	public static function register_conditions() {
		ElementConditions::register_condition(
			'user/username',
			[
				'group'    => 'user',
				'name'     => esc_html__( 'Username', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_username' ],
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
					'username' => [
						'type' => 'text',
					],
				],
			]
		);

		ElementConditions::register_condition(
			'user/description',
			[
				'group'    => 'user',
				'name'     => esc_html__( 'User description', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_description' ],
				'form'     => [
					'operator'    => [
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
					'description' => [
						'type' => 'text',
					],
				],
			]
		);

		ElementConditions::register_condition(
			'user/meta',
			[
				'group'    => 'user',
				'name'     => esc_html__( 'User meta field', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_meta_field' ],
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

		ElementConditions::register_condition(
			'user/role',
			[
				'group'    => 'user',
				'name'     => esc_html__( 'User role', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_role' ],
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
					'role'     => [
						'type'       => 'select',
						'rest'       => 'v1/conditions/user/roles',
						'filterable' => true,
					],
				],
			]
		);

		ElementConditions::register_condition(
			'user/capability',
			[
				'group'    => 'user',
				'name'     => esc_html__( 'User capability', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_capability' ],
				'form'     => [
					'operator'   => [
						'type'    => 'select',
						'options' => self::get_operators(
							[
								'equals',
								'not_equals',
							]
						),
					],
					'capability' => [
						'type'       => 'select',
						'rest'       => 'v1/conditions/user/capabilities',
						'filterable' => true,
					],
				],
			]
		);

		ElementConditions::register_condition(
			'user/logged_in',
			[
				'group'    => 'user',
				'name'     => esc_html__( 'User logged in status', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_user_logged_in' ],
				'form'     => [
					'operator' => [
						'type'    => 'placeholder',
						'content' => esc_html__( 'equals', 'zionbuilder-pro' ),
					],

					'status'   => [
						'type'    => 'select',
						'options' => [
							[
								'id'   => true,
								'name' => esc_html__( 'Is logged in', 'zionbuilder-por' ),
							],
							[
								'id'   => false,
								'name' => esc_html__( 'Is not logged in', 'zionbuilder-por' ),
							],
						],
					],
				],
			]
		);
	}


	public static function validate_username( $settings ) {
		$user_id = get_current_user_id();

		if ( ! $user_id ) {
			return false;
		}

		return self::validate(
			[
				'operator'      => $settings['operator'],
				'saved_value'   => $settings['username'],
				'current_value' => get_the_author_meta( 'user_login', $user_id ),
			]
		);
	}

	public static function validate_description( $settings ) {
		$user_id = get_current_user_id();

		if ( ! $user_id ) {
			return false;
		}

		return self::validate(
			[
				'operator'      => $settings['operator'],
				'saved_value'   => $settings['description'],
				'current_value' => get_the_author_meta( 'description', $user_id ),
			]
		);
	}

	public static function validate_meta_field( $settings ) {
		$user_id = get_current_user_id();

		if ( ! $user_id ) {
			return false;
		}

		return self::validate(
			[
				'operator'      => $settings['operator'],
				'saved_value'   => $settings['value'],
				'current_value' => get_the_author_meta( $settings['key'], $user_id ),
			]
		);
	}

	public static function validate_role( $settings ) {
		$user_id = get_current_user_id();

		if ( ! $user_id ) {
			return false;
		}

		$user_data = get_userdata( $user_id );

		if ( empty( $user_data ) ) {
			return false;
		}

		$has_role = in_array( $settings['role'], $user_data->roles );
		return $settings['operator'] === 'equals' ? $has_role : ! $has_role;
	}


	public function validate_capability( $settings ) {
		$user_id = get_current_user_id();

		if ( ! $user_id ) {
			return false;
		}

		$user_can = user_can( $user_id, $settings['capability'] );
		return $settings['operator'] === 'equals' ? $user_can : ! $user_can;

	}

	public static function validate_user_logged_in( $settings ) {
		return self::validate(
			[
				'operator'      => 'equals',
				'saved_value'   => isset( $settings['status'] ) ? $settings['status'] : true,
				'current_value' => is_user_logged_in(),
			]
		);
	}

}
