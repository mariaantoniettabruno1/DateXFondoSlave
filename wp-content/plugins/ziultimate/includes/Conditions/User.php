<?php
namespace ZiUltimate\Conditions;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

use ZionBuilderPro\ElementConditions\ConditionsBase;
use ZionBuilderPro\ElementConditions\ElementConditions;

class User extends ConditionsBase {

	public static function init_conditions() {
		self::register_conditions();
	}

	public static function register_conditions() {
		ElementConditions::register_condition('user/logged_in', [
			'group' 	=> 'user',
			'name' 		=> esc_html__('User Login Status(ZU)', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_is_logged_in'],
			'form' 		=> [
				'is_logged_in' => [
					'type' => 'select',
					'options' 	=> [
						[
							'id' 	=> 'yes',
							'name' 	=> esc_html__('Logged In', 'zionbuilder'),
						],
						[
							'id' 	=> 'no',
							'name' 	=> esc_html__('Logged Out', 'zionbuilder'),
						]
					]
				]
			]
		]);

		ElementConditions::register_condition('user/has_post', [
			'group' 	=> 'user',
			'name' 		=> esc_html__('User has post(ZU)', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_has_post'],
			'form' 		=> [
				'post_type' => [
					'type'       => 'select',
					'rest'       => 'v1/conditions/post/post_types',
					'filterable' => true,
				],
				'has_post' => [
					'type' => 'select',
					'options' 	=> [
						[
							'id' 	=> 'yes',
							'name' 	=> esc_html__('Yes', 'zionbuilder'),
						],
						[
							'id' 	=> 'no',
							'name' 	=> esc_html__('No', 'zionbuilder'),
						]
					]
				]
			]
		]);
	}

	public static function validate_is_logged_in( $settings ) {
		return self::validate([
			'operator' 		=> 'equals',
			'saved_value' 	=> $settings['is_logged_in'] == 'yes' ? true : false,
			'current_value' => is_user_logged_in()
		]);
	}

	public static function validate_has_post( $settings ) {
		$user_id = get_current_user_id();

		if ( ! $user_id ) {
			return false;
		}

		$posts = count_user_posts( $user_id, $settings['post_type'] );

		return self::validate([
			'operator' 		=> 'equals',
			'saved_value' 	=> $settings['has_post'] == 'yes' ? true : false,
			'current_value' => ( $posts > 0 ) ? true : false
		]);
	}
}