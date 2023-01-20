<?php
namespace ZiUltimate\Conditions;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

if( ! function_exists( 'acf' ) ) {
	return;
}

use ZionBuilderPro\ElementConditions\ConditionsBase;
use ZionBuilderPro\ElementConditions\ElementConditions;

class Acf extends ConditionsBase {

	public static function init_conditions() {
		self::register_conditions();
	}

	public static function register_conditions() {
		ElementConditions::register_condition('acf/options', [
			'group' 	=> 'advanced',
			'name' 		=> esc_html__('ACF options page', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_acf_options_data'],
			'form' 		=> [
				'key' => [
					'type' 			=> 'text',
					'placeholder' 	=> esc_html__('Enter key')
				],
				'operator'  => [
					'type'    => 'select',
					'options' => self::get_operators( [ 
						'equals', 
						'not_equals',
						'is_set',
						'is_not_set',
						'starts_with',
						'ends_with',
						'contains',
						'does_not_contain',
					] ),
				],
				'value'     => [
					'type'     => 'text',
					'placeholder' 	=> esc_html__('Enter value'),
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
			]
		]);
	}

	public static function validate_acf_options_data( $settings ) {
		if( ! isset( $settings['key'] ) )
			return false;

		return self::validate([
			'operator' 		=> $settings['operator'],
			'saved_value' 	=> $settings['value'],
			'current_value' => get_field( $settings['key'], 'option' )
		]);
	}
}