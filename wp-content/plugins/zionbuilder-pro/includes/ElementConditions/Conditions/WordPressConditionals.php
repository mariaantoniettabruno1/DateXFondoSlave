<?php

namespace ZionBuilderPro\ElementConditions\Conditions;

use ZionBuilderPro\ElementConditions\ConditionsBase;
use ZionBuilderPro\ElementConditions\ElementConditions;

class WordPressConditionals extends ConditionsBase {
	public static function init_conditions() {
		self::register_groups();
		self::register_conditions();
	}

	public static function register_groups() {
		// Register groups
		ElementConditions::register_condition_group(
			'wordpress',
			[
				'name' => esc_html__( 'WordPress Conditionals', 'zionbuilder-pro' ),
			]
		);
	}

	public static function register_conditions() {
		ElementConditions::register_condition(
			'wordpress/conditions',
			[
				'group'    => 'wordpress',
				'name'     => esc_html__( 'WordPress Condition', 'zionbuilder-pro' ),
				'callback' => [ __CLASS__, 'validate_wordpress_condition' ],
				'form'     => [
					'operator' => [
						'type'    => 'select',
						'options' => self::get_operators( [ 'is', 'is_not' ] ),
					],
					'value'    => [
						'type'    => 'select',
						'options' => [
							[
								'name' => '404 page',
								'id'   => 'is_404',
							],
							[
								'name' => 'Front page',
								'id'   => 'is_front_page',
							],
							[
								'name' => 'Home page',
								'id'   => 'is_home',
							],
							[
								'name' => 'Single post',
								'id'   => 'is_single',
							],
							[
								'name' => 'Sticky post',
								'id'   => 'is_sticky',
							],
							[
								'name' => 'Post type archive',
								'id'   => 'is_post_type_archive',
							],
							[
								'name' => 'With comments open',
								'id'   => 'has_comments_open',
							],
							[
								'name' => 'With pings open',
								'id'   => 'has_pings_open',
							],
							[
								'name' => 'Page',
								'id'   => 'is_page',
							],
							[
								'name' => 'Page template',
								'id'   => 'is_page_template',
							],
							[
								'name' => 'Category',
								'id'   => 'is_category',
							],
							[
								'name' => 'Tag',
								'id'   => 'is_tag',
							],
							[
								'name' => 'Tax',
								'id'   => 'is_tax',
							],
							[
								'name' => 'Author',
								'id'   => 'is_author',
							],
							[
								'name' => 'Date',
								'id'   => 'is_date',
							],
							[
								'name' => 'Archive',
								'id'   => 'is_archive',
							],
							[
								'name' => 'Search',
								'id'   => 'is_search',
							],
							[
								'name' => 'Paged',
								'id'   => 'is_paged',
							],
							[
								'name' => 'Singular',
								'id'   => 'is_singular',
							],
						],
					],
				],
			]
		);

		global $wp_registered_sidebars;
		$sidebar_options = [];
		foreach ( $wp_registered_sidebars as $sidebar ) {
			$sidebar_options[] = [
				'name' => $sidebar['name'],
				'id'   => $sidebar['id'],
			];
		}

		ElementConditions::register_condition(
			'wordpress/sidebar_active',
			[
				'group'    => 'wordpress',
				'name'     => esc_html__( 'Sidebar has widgets', 'zionbuilder-pro' ),
				'callback' => [ __CLASS__, 'validate_wordpress_sidebar_condition' ],
				'form'     => [
					'sidebar_id' => [
						'type'        => 'select',
						'options'     => $sidebar_options,
						'placeholder' => esc_html__( 'Select sidebar', 'zionbuilder-pro' ),
					],
				],
			]
		);
	}

	public static function validate_wordpress_sidebar_condition( $settings ) {
		if ( empty( $settings['sidebar_id'] ) ) {
			return false;
		}

		return is_active_sidebar( $settings['sidebar_id'] );
	}

	public static function validate_wordpress_condition( array $settings ) {
		if ( empty( $settings['value'] ) ) {
			return false;
		}

		$current_value = null;

		switch ( $settings['value'] ) {
			case 'is_404':
				$current_value = is_404();
				break;
			case 'is_front_page':
				$current_value = ( ! is_home() && is_front_page() );
				break;
			case 'is_home':
				$current_value = ( ! is_front_page() && is_home() );
				break;
			case 'is_single':
				$current_value = is_single();
				break;
			case 'is_sticky':
				$current_value = ( is_single() && is_sticky( get_the_ID() ) );
				break;
			case 'is_post_type_archive':
				$current_value = is_post_type_archive();
				break;
			case 'has_comments_open':
				$current_value = ( is_single() && comments_open() );
				break;
			case 'has_pings_open':
				$current_value = ( is_single() && pings_open() );
				break;
			case 'is_page':
				$current_value = is_page();
				break;
			case 'is_page_template':
				$current_value = is_page_template();
				break;
			case 'is_category':
				$current_value = is_category();
				break;
			case 'is_tag':
				$current_value = is_tag();
				break;
			case 'is_tax':
				$current_value = is_tax();
				break;
			case 'is_author':
				$current_value = is_author();
				break;
			case 'is_date':
				$current_value = is_date();
				break;
			case 'is_archive':
				$current_value = is_archive();
				break;
			case 'is_search':
				$current_value = is_search();
				break;
			case 'is_paged':
				$current_value = is_paged();
				break;
			case 'is_singular':
				$current_value = is_singular();
				break;
		}

		if ( is_null( $current_value ) ) {
			return false;
		}

		return self::validate(
			[
				'operator'      => $settings['operator'],
				'saved_value'   => true,
				'current_value' => $current_value,
			]
		);
	}

}
