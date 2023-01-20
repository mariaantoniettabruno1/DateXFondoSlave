<?php

namespace ZionBuilderPro\ElementConditions;

use ZionBuilderPro\ElementConditions\Conditions\AdvancedConditionals;
use ZionBuilderPro\ElementConditions\Conditions\Archive;
use ZionBuilderPro\ElementConditions\Conditions\Author;
use ZionBuilderPro\ElementConditions\Conditions\Post;
use ZionBuilderPro\ElementConditions\Conditions\User;
use ZionBuilderPro\ElementConditions\Conditions\WooCommerceConditionals;
use ZionBuilderPro\ElementConditions\Conditions\WordPressConditionals;

class ElementConditions {
	private static $registered_conditions       = [];
	private static $registered_condition_groups = [];

	/**
	 * Main class constructor
	 */
	public function __construct() {
		add_filter( 'zionbuilder/element/can_render', [ $this, 'check_conditions' ], 10, 2 );
		add_filter( 'zionbuilderpro/editor/initial_data', [ $this, 'add_data_to_editor' ] );
		add_action( 'zionbuilder/schema/advanced_options', [ $this, 'change_visibility_option' ] );
		add_action( 'zionbuilder/rest_api/register_controllers', [ $this, 'register_rest_api' ] );
		add_action( 'init', [ $this, 'init_conditions' ] );

	}

	public function init_conditions() {
		// Init the conditions
		Post::init_conditions();
		Archive::init_conditions();
		Author::init_conditions();
		User::init_conditions();
		WordPressConditionals::init_conditions();

		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			WooCommerceConditionals::init_conditions();
		}

		AdvancedConditionals::init_conditions();

		do_action( 'zionbuilder-pro/element-conditions/init' );

	}

	public function register_rest_api( $rest_api_instance ) {
		$rest_api_instance->register_controller( new Rest() );
	}

	/**
	 * Add advanced conditions to element visibility options
	 *
	 * @param $options
	 */
	public function change_visibility_option( $options ) {
		$general_options   = $options->get_option( 'general-group' );
		$visibility_option = $options->get_option( 'general-group._element_visibility' );

		$visibility_option->options[] = [
			'name' => __( 'Advanced conditions', 'zionbuilder-pro' ),
			'id'   => 'advanced',
		];

		$general_options->add_option(
			'_element_display_conditions',
			[
				'type'        => 'element_conditions',
				'description' => __( 'Set advanced conditions for displaying this element.', 'zionbuilder-pro' ),
				'title'       => __( 'Element Conditions', 'zionbuilder-pro' ),
				'dependency'  => [
					[
						'option' => '_element_visibility',
						'value'  => [ 'advanced' ],
					],
				],
			]
		);
	}

	/**
	 * Add element conditions data to editor JS
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function add_data_to_editor( $data ) {
		$conditions_data                      = [];
		$conditions_data['condition_options'] = self::get_conditions_options();
		$conditions_data['conditions']        = self::get_conditions();
		$conditions_data['groups']            = self::get_condition_groups();

		$data['element_conditions'] = $conditions_data;

		return $data;
	}

	/**
	 * Register single condition
	 *
	 * @param string $condition_id
	 * @param array $condition
	 *
	 * @return void
	 */
	public static function register_condition( $condition_id, $condition ) {
		self::$registered_conditions[ $condition_id ] = $condition;
	}

	public static function register_condition_group( $group_id, $group_details ) {
		self::$registered_condition_groups[ $group_id ] = $group_details;
	}

	/**
	 * Returns the list of registered conditions
	 *
	 * @return array
	 */
	public static function get_condition_groups() {
		return self::$registered_condition_groups;
	}

	public static function get_conditions_options() {
		$condition_options = [];

		foreach ( self::$registered_condition_groups as $group_id => $group_info ) {
			$conditions_in_group = [];

			foreach ( self::$registered_conditions as $condition_id => $condition ) {
				if ( $condition['group'] === $group_id ) {
					$conditions_in_group[] = [
						'id'   => $condition_id,
						'name' => $condition['name'],
					];
				}
			}

			if ( count( $conditions_in_group ) > 0 ) {
				$condition_options[] = [
					'name'     => $group_info['name'],
					'is_label' => true,
				];

				$condition_options = array_merge( $condition_options, $conditions_in_group );
			}
		}

		return $condition_options;
	}

	/**
	 * Returns the list of registered conditions
	 *
	 * @return array
	 */
	public static function get_conditions() {
		return self::$registered_conditions;
	}

	public static function get_condition( $condition_id ) {
		return isset( self::$registered_conditions[ $condition_id ] ) ? self::$registered_conditions[ $condition_id ] : false;
	}

	public function check_conditions( $can_render, $element_instance ) {
		$options = $element_instance->options->get_model();

		// CHeck if custom conditions are set
		if ( isset( $options['_advanced_options']['_element_visibility'] ) && $options['_advanced_options']['_element_visibility'] === 'advanced' && isset( $options['_advanced_options']['_element_display_conditions'] ) ) {
			return self::check_conditions_groups( $options['_advanced_options']['_element_display_conditions'] );
		}

		return $can_render;
	}

	public static function check_conditions_groups( $conditions_groups ) {
		if ( ! is_array( $conditions_groups ) ) {
			return true;
		}

		$passed = true;

		foreach ( $conditions_groups as $conditions ) {
			foreach ( $conditions as $condition_config ) {
				$passed = self::check_condition( $condition_config );

				if ( ! $passed ) {
					break;
				}
			}

			if ( $passed ) {
				return true;
			}
		}

		return $passed;
	}

	public static function get_condition_callback( $type ) {
		$condition = self::get_condition( $type );

		if ( $condition ) {
			return $condition['callback'];
		}

		return false;
	}

	public static function check_condition( $condition_config ) {
		$type     = $condition_config['type'];
		$callback = self::get_condition_callback( $type );

		if ( is_callable( $callback ) ) {
			return call_user_func( $callback, $condition_config );
		}

		return false;
	}
}
