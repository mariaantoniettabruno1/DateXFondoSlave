<?php
namespace ZiUltimate\Conditions;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

use ZionBuilderPro\ElementConditions\ConditionsBase;
use ZionBuilderPro\ElementConditions\ElementConditions;

class Browser extends ConditionsBase {

	public static function init_conditions() {
		self::register_groups();
		self::register_conditions();
	}

	public static function register_groups() {
		// Register groups
		ElementConditions::register_condition_group('browser', [
			'name' => esc_html__('Browser', 'ziultimate')
		]);
	}

	public static function register_conditions() {
		ElementConditions::register_condition('browser/requesturi', [
			'group' 	=> 'browser',
			'name' 		=> esc_html__('Request URI', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_request_uri'],
			'form' 		=> [
				'operator' 	=> [
					'type' 			=> 'select',
					'options' 		=> self::get_operators([
						'equals',
						'not_equals',
						'starts_with',
						'ends_with',
						'contains',
						'does_not_contain'
					]),
				],
				'request_uri' 	=> [
					'type' 			=> 'text'
				]
			]
		]);

		ElementConditions::register_condition('browser/referer', [
			'group' 	=> 'browser',
			'name' 		=> esc_html__('Referer', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_http_referer'],
			'form' 		=> [
				'operator' 	=> [
					'type' 			=> 'select',
					'options' 		=> self::get_operators([
						'equals',
						'not_equals',
						'starts_with',
						'ends_with',
						'contains',
						'does_not_contain'
					]),
				],
				'http_referer' 	=> [
					'type' 			=> 'text'
				]
			]
		]);
	}

	public static function validate_request_uri( $settings ) {
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')   
			$url = "https://";   
		else  
			$url = "http://";   
		// Append the host(domain name, ip) to the URL.   
		$url.= $_SERVER['HTTP_HOST'];   

		// Append the requested resource location to the URL   
		$url.= $_SERVER['REQUEST_URI'];

		return self::validate([
			'operator' 		=> $settings['operator'],
			'saved_value' 	=> $settings['request_uri'],
			'current_value' => $url
		]);
	}

	public static function validate_http_referer( $settings ) {
		return self::validate([
			'operator' 		=> $settings['operator'],
			'saved_value' 	=> $settings['http_referer'],
			'current_value' => isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : ''
		]);
	}
}