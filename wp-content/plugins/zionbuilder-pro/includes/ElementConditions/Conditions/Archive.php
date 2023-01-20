<?php

namespace ZionBuilderPro\ElementConditions\Conditions;

use ZionBuilderPro\ElementConditions\ConditionsBase;
use ZionBuilderPro\ElementConditions\ElementConditions;

class Archive extends ConditionsBase {
	public static function init_conditions() {
		self::register_groups();
		self::register_conditions();


	}

	public static function register_groups() {
		// Register groups
		ElementConditions::register_condition_group('archive', [
			'name' => esc_html__('Archive', 'zionbuilder-pro')
		]);
	}

	public static function register_conditions() {
		ElementConditions::register_condition('archive/archive', [
			'group' => 'archive',
			'name' => esc_html__('Archive', 'zionbuilder-pro'),
			'callback' => [get_class(), 'validate_archive'],
			'form' => [
				'operator' => [
					'type' => 'select',
					'options' => self::get_operators([
						'equals',
						'not_equals'
					]),
				],
				'archive' => [
					'type' => 'select',
					'rest' => 'v1/conditions/archive/archive',
					'filterable' => true
				]
			]
		]);


		ElementConditions::register_condition('archive/title', [
			'group' => 'archive',
			'name' => esc_html__('Archive title', 'zionbuilder-pro'),
			'callback' => [get_class(), 'validate_archive_title'],
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
				'text' => [
					'type' => 'text',
				]
			]
		]);


		ElementConditions::register_condition('archive/description', [
			'group' => 'archive',
			'name' => esc_html__('Archive description', 'zionbuilder-pro'),
			'callback' => [get_class(), 'validate_archive_description'],
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
				'text' => [
					'type' => 'text',
				]
			]
		]);

		ElementConditions::register_condition('archive/taxonomy_term', [
			'group' => 'archive',
			'name' => esc_html__('Archive taxonomy term', 'zionbuilder-pro'),
			'callback' => [get_class(), 'validate_taxonomy_term'],
			'form' => [
				'operator' => [
					'type' => 'select',
					'options' => self::get_operators([
						'equals',
						'not_equals',
					]),
				],
				'taxonomy' => [
					'type' => 'select',
					'rest' => 'v1/conditions/post/taxonomies',
					'filterable' => true
				],
				'term_id' => [
					'type' => 'select',
					'rest' => 'v1/conditions/post/terms?taxonomy=${taxonomy}',
					'filterable' => true,
					'requires' => 'taxonomy',
				]
			]
		]);


		ElementConditions::register_condition('archive/term_meta', [
			'group' => 'archive',
			'name' => esc_html__('Archive term meta', 'zionbuilder-pro'),
			'callback' => [get_class(), 'validate_custom_field'],
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


		ElementConditions::register_condition('archive/number_of_posts', [
			'group' => 'archive',
			'name' => esc_html__('Number of posts', 'zionbuilder-pro'),
			'callback' => [get_class(), 'validate_number_of_posts'],
			'form' => [
				'operator' => [
					'type' => 'select',
					'options' => self::get_operators([
						'equals',
						'not_equals',
						'greater',
						'lower',
						'greater_or_equal',
						'lower_or_equal',
					]),
				],
				'number' => [
					'type' => 'text'
				]
			]
		]);


	}


	public static function validate_archive($settings) {
		$groups = explode('/', $settings['archive']);
		$current_value = false;

		switch ($groups[0]) {
			case 'post_type':
				if ($groups[1] === 'post') {
					$current_value = is_home();
				} else {
					$current_value = is_post_type_archive( $groups[1] );
				}
				break;
			case 'taxonomy':
				if ($groups[1] === 'category') {
					$current_value = is_category();
				} elseif ($groups[1] === 'post_tag') {
					$current_value = is_tag();
				} else {
					$current_value = is_tax( $groups[1] );
				}


				break;
		}

		return $settings['operator'] === 'equals' ? $current_value : ! $current_value;
	}


	/**
	 * Validates if post title passed the configured conditions
	 *
	 * @param array $settings
	 *
	 * @return boolean
	 */
	public static function validate_archive_title($settings) {
		return self::validate([
			'operator' => $settings['operator'],
			'saved_value' => $settings['text'],
			'current_value' => get_the_archive_title()
		]);
	}

	/**
	 * Validates if archive description matches the query
	 *
	 * @param array $settings
	 *
	 * @return boolean
	 */
	public static function validate_archive_description($settings) {
		return self::validate([
			'operator' => $settings['operator'],
			'saved_value' => $settings['text'],
			'current_value' => get_the_archive_description()
		]);
	}

	/**
	 * Validates if archive description matches the query
	 *
	 * @param array $settings
	 *
	 * @return boolean
	 */
	public static function validate_taxonomy_term($settings) {
		switch ($settings['taxonomy']) {
			case 'category':
				$has_term = is_category($settings['term_id']);
				break;
			case 'post_tag':
				$has_term = is_tag($settings['term_id']);
				break;
			default:
				$has_term = is_tax($settings['taxonomy'], $settings['term_id']);
				break;
		}

		return 'equals' === $settings['operator'] ? $has_term : ! $has_term;
	}



	/**
	 * Validates if the post has a specific custom field
	 *
	 * @param array $settings
	 *
	 * @return boolean
	 */
	public static function validate_custom_field($settings) {
		$term_id = get_queried_object()->term_id;

		if ( ! $term_id) {
			return false;
		}

		return self::validate([
			'operator' => $settings['operator'],
			'saved_value' => $settings['value'],
			'current_value' => get_term_meta($term_id, $settings['key'], true),
		]);
	}


	/**
	 * Validates if the number of posts on archive page matches the saved value
	 *
	 * @param array $settings
	 *
	 * @return boolean
	 */
	public static function validate_number_of_posts($settings) {
		global $wp_query;

		if ( ! $wp_query ) {
			return false;
		}

		return self::validate([
			'operator' => $settings['operator'],
			'saved_value' => $settings['number'],
			'current_value' => $wp_query->post_count,
		]);
	}
}