<?php
namespace ZiUltimate\Conditions;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

use ZionBuilderPro\ElementConditions\ConditionsBase;
use ZionBuilderPro\ElementConditions\ElementConditions;
//use ZionBuilderPro\Plugin;

class Post extends ConditionsBase {

	public static function init_conditions() {
		self::register_conditions();
	}

	public static function register_conditions() {
		ElementConditions::register_condition('post/is_child', [
			'group' 	=> 'post',
			'name' 		=> esc_html__('Is child post?', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_is_child_post'],
			'form' 		=> [
				'is_child' => [
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

		/*ElementConditions::register_condition('post/adjacent_posts', [
			'group' 	=> 'post',
			'name' 		=> esc_html__('Adjacent Posts', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_has_adjacent_posts'],
			'form' 		=> [
				'has_adjacent_posts' => [
					'type' => 'select',
					'options' 	=> [
						[
							'id' 	=> 'prev_post',
							'name' 	=> esc_html__('Has previous post?', 'zionbuilder'),
						],
						[
							'id' 	=> 'next_post',
							'name' 	=> esc_html__('Has next post?', 'zionbuilder'),
						]
					]
				]
			]
		]);*/

		ElementConditions::register_condition('post/password_protected', [
			'group' 	=> 'post',
			'name' 		=> esc_html__('Password protected', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_is_password_protected'],
			'form' 		=> [
				'is_password_protected' => [
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

		ElementConditions::register_condition('post/one_entry', [
			'group' 	=> 'post',
			'name' 		=> esc_html__('Has at least 1 entry', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_has_one_entry'],
			'form' 		=> [
				'post_type' => [
					'type' => 'select',
					'rest' => 'v1/conditions/post/post_types',
					'filterable' => true
				]
			]
		]);

		ElementConditions::register_condition('post/search_result', [
			'group' 	=> 'post',
			'name' 		=> esc_html__('Has search results', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_has_search_results'],
			'form' 		=> [
				'has_search_result' => [
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

		//* Authors conditions
		ElementConditions::register_condition('author/login_status', [
			'group' 	=> 'author',
			'name' 		=> esc_html__('Author Login Status', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_author_login_status'],
			'form' 		=> [
				'auth_login_status' => [
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

		ElementConditions::register_condition('author/has_post', [
			'group' 	=> 'author',
			'name' 		=> esc_html__('Author has post', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_author_has_post'],
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

	public static function validate_is_child_post( $settings ) {
		$post = self::get_post();

		if (! $post) {
			return false;
		}

		return self::validate([
			'operator' 		=> 'equals',
			'saved_value' 	=> $settings['is_child'],
			'current_value' => ( $post->post_parent > 0 ) ? 'yes' : 'no'
		]);
	}

	public static function validate_is_password_protected( $settings ) {
		$post = self::get_post();

		return self::validate([
			'operator' 		=> 'equals',
			'saved_value' 	=> $settings['is_password_protected'] == 'yes' ? true : false,
			'current_value' => post_password_required( $post )
		]);
	}

	public static function validate_has_one_entry( $settings ) {
		return ( wp_count_posts( $settings['post_type'] )->publish > 0 );
	}

	public static function validate_has_search_results( $settings ) {
		global $wp_query;

		return self::validate([
			'operator' 		=> 'equals',
			'saved_value' 	=> $settings['has_search_result'],
			'current_value' => ( $wp_query->found_posts > 0 ) ? 'yes' : 'no'
		]);
	}

	public static function validate_author_login_status( $settings ) {
		$post = self::get_post();
		if ( $post ) {
			$author = $post->post_author;
			return self::validate([
				'operator' 		=> 'equals',
				'saved_value' 	=> $settings['auth_login_status'],
				'current_value' => get_current_user_id() == $author ? 'yes' : 'no',
			]);
		}
	}

	public static function validate_author_has_post( $settings ) {
		$post = self::get_post();

		if (! $post) {
			return false;
		}

		$hasPosts = count_user_posts( $post->post_author, $settings['post_type'] );

		return self::validate([
			'operator' 		=> 'equals',
			'saved_value' 	=> $settings['has_post'] == 'yes' ? true : false,
			'current_value' => ( $hasPosts > 0 ) ? true : false
		]);
	}
}