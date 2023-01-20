<?php

namespace ZionBuilderPro\ElementConditions\Conditions;

use ZionBuilderPro\ElementConditions\ConditionsBase;
use ZionBuilderPro\ElementConditions\ElementConditions;

class AdvancedConditionals extends ConditionsBase {
	public static function init_conditions() {
		self::register_groups();
		self::register_conditions();
	}

	public static function register_groups() {
		// Register groups
		ElementConditions::register_condition_group(
			'advanced',
			[
				'name' => esc_html__( 'Advanced Conditionals', 'zionbuilder-pro' ),
			]
		);
	}

	public static function register_conditions() {
		//#! advanced/browser
		ElementConditions::register_condition(
			'advanced/browser',
			[
				'group'    => 'advanced',
				'name'     => esc_html__( 'Browser', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_browser' ],
				'form'     => [
					'operator' => [
						'type'    => 'select',
						'options' => self::get_operators( [ 'equals', 'not_equals', ] ),
					],
					'value'    => [
						'type'    => 'select',
						'options' => [
							[
								'name' => esc_html__( 'Internet Explorer', 'zionbuilder-pro' ),
								'id'   => 'internet_explorer',
							],
							[
								'name' => esc_html__( 'Mozilla FireFox', 'zionbuilder-pro' ),
								'id'   => 'mozilla_firefox',
							],
							[
								'name' => esc_html__( 'Google Chrome', 'zionbuilder-pro' ),
								'id'   => 'google_chrome',
							],
							[
								'name' => esc_html__( 'Opera', 'zionbuilder-pro' ),
								'id'   => 'opera',
							],
							[
								'name' => esc_html__( 'Safari', 'zionbuilder-pro' ),
								'id'   => 'safari',
							],
							[
								'name' => esc_html__( 'Microsoft Edge', 'zionbuilder-pro' ),
								'id'   => 'microsoft_edge',
							],
						],
					],
				],
			]
		);

		//#! advanced/cookie
		ElementConditions::register_condition(
			'advanced/cookie',
			[
				'group'    => 'advanced',
				'name'     => esc_html__( 'Cookie', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_cookie' ],
				'form'     => [
					'key'      => [
						'type' => 'text',
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

		//#! advanced/url_variable
		ElementConditions::register_condition(
			'advanced/url_variable',
			[
				'group'    => 'advanced',
				'name'     => esc_html__( 'URL variable', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_url_variable' ],
				'form'     => [
					'key'      => [
						'type' => 'text',
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

		//#! advanced/operating_system
		ElementConditions::register_condition(
			'advanced/operating_system',
			[
				'group'    => 'advanced',
				'name'     => esc_html__( 'Operating system', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_operating_system' ],
				'form'     => [
					'operator' => [
						'type'    => 'select',
						'options' => self::get_operators( [ 'equals', 'not_equals', ] ),
					],
					'value'    => [
						'type'    => 'select',
						'options' => [
							[
								'name' => esc_html__( 'Windows', 'zionbuilder-pro' ),
								'id'   => 'windows',
							],
							[
								'name' => esc_html__( 'Mac OS', 'zionbuilder-pro' ),
								'id'   => 'mac_os',
							],
							[
								'name' => esc_html__( 'Linux', 'zionbuilder-pro' ),
								'id'   => 'linux',
							],
							[
								'name' => esc_html__( 'Android', 'zionbuilder-pro' ),
								'id'   => 'android',
							],
							[
								'name' => esc_html__( 'iOS', 'zionbuilder-pro' ),
								'id'   => 'ios',
							],
						],
					],
				],
			]
		);

		//#! advanced/referrer
		ElementConditions::register_condition(
			'advanced/referrer',
			[
				'group'    => 'advanced',
				'name'     => esc_html__( 'Referrer', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_referrer' ],
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
					'value'    => [
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

		//#! advanced/function_return_value
		ElementConditions::register_condition(
			'advanced/function_return_value',
			[
				'group'    => 'advanced',
				'name'     => esc_html__( 'Function return value', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_function_return_value' ],
				'form'     => [
					'function'  => [
						'type' => 'text',
					],
					'arguments' => [
						'type' => 'text',
					],
				],
			]
		);
	}

	public static function validate_browser( array $settings ) {
		if ( empty( $settings[ 'value' ] ) ) {
			return false;
		}

		return self::validate(
			[
				'operator'      => $settings[ 'operator' ],
				'saved_value'   => true,
				'current_value' => self::isBrowser( $settings[ 'value' ] ),
			]
		);
	}

	public static function validate_cookie( array $settings ) {
		if ( empty( $settings[ 'key' ] ) ) {
			return false;
		}

		//#! Special case #1
		if ( 'is_set' == $settings[ 'operator' ] ) {
			return self::validate(
				[
					'operator'      => $settings[ 'operator' ],
					'saved_value'   => true,
					'current_value' => isset( $_COOKIE[ $settings[ 'key' ] ] ),
				]
			);
		}

		//#! Special case #2
		if ( 'is_not_set' == $settings[ 'operator' ] ) {
			return self::validate(
				[
					'operator'      => $settings[ 'operator' ],
					'saved_value'   => false,
					'current_value' => ! isset( $_COOKIE[ $settings[ 'key' ] ] ),
				]
			);
		}

		//#! String operations, require value
		if ( ! isset( $settings[ 'value' ] ) ) {
			return false;
		}
		$cookieValue = ( isset( $_COOKIE[ $settings[ 'key' ] ] ) ? $_COOKIE[ $settings[ 'key' ] ] : null );
		if ( is_null( $cookieValue ) ) {
			return false;
		}

		return self::validate(
			[
				'operator'      => $settings[ 'operator' ],
				'saved_value'   => $settings[ 'value' ],
				'current_value' => $cookieValue,
			]
		);
	}

	public static function validate_url_variable( array $settings ) {
		if ( empty( $settings[ 'key' ] ) ) {
			return false;
		}

		//#! Special case #1
		if ( 'is_set' == $settings[ 'operator' ] ) {
			return self::validate(
				[
					'operator'      => $settings[ 'operator' ],
					'saved_value'   => true,
					'current_value' => isset( $_REQUEST[ $settings[ 'key' ] ] ),
				]
			);
		}

		//#! Special case #2
		if ( 'is_not_set' == $settings[ 'operator' ] ) {
			return self::validate(
				[
					'operator'      => $settings[ 'operator' ],
					'saved_value'   => false,
					'current_value' => ! isset( $_REQUEST[ $settings[ 'key' ] ] ),
				]
			);
		}

		//#! String operations, require value
		if ( ! isset( $settings[ 'value' ] ) ) {
			return false;
		}
		$varValue = ( isset( $_REQUEST[ $settings[ 'key' ] ] ) ? $_REQUEST[ $settings[ 'key' ] ] : null );
		if ( is_null( $varValue ) ) {
			return false;
		}

		return self::validate(
			[
				'operator'      => $settings[ 'operator' ],
				'saved_value'   => $settings[ 'value' ],
				'current_value' => $varValue,
			]
		);
	}

	public static function validate_operating_system( array $settings ) {
		if ( empty( $settings[ 'value' ] ) ) {
			return false;
		}

		return self::validate(
			[
				'operator'      => $settings[ 'operator' ],
				'saved_value'   => true,
				'current_value' => self::isOS( $settings[ 'value' ] ),
			]
		);
	}

	public static function validate_referrer( array $settings ) {
		//#! Special case #1
		if ( 'is_set' == $settings[ 'operator' ] ) {
			return isset( $_SERVER[ 'HTTP_REFERER' ] );
		}
		//#! Special case #2
		if ( 'is_not_set' == $settings[ 'operator' ] ) {
			return ! isset( $_SERVER[ 'HTTP_REFERER' ] );
		}

		return self::validate(
			[
				'operator'      => $settings[ 'operator' ],
				'saved_value'   => $settings[ 'value' ],
				'current_value' => ( isset( $_SERVER[ 'HTTP_REFERER' ] ) ? $_SERVER[ 'HTTP_REFERER' ] : null ),
			]
		);
	}

	public static function validate_function_return_value( array $settings ) {
		$fn   = ( isset( $settings[ 'function' ] ) ? $settings[ 'function' ] : '' );
		$args = ( isset( $settings[ 'arguments' ] ) ? $settings[ 'arguments' ] : '' );

		if ( empty( $fn ) || ! is_callable( $fn ) ) {
			return false;
		}
		if ( ! empty( $args ) ) {
			$args = array_map( 'trim', explode( ',', $args ) );
		}

		return self::validate(
			[
				'operator'      => 'equals',
				'saved_value'   => true,
				'current_value' => call_user_func( $fn, ...$args ),
			]
		);
	}

	/**
	 * Check if browser's match
	 *
	 * @param string $browser
	 *
	 * @return bool
	 */
	private static function isBrowser( $browser ) {
		$regexes = [
			'internet_explorer' => '/msie/i',
			'mozilla_firefox'   => '/firefox/i',
			'safari'            => '/safari/i',
			'google_chrome'     => '/chrome/i',
			'microsoft_edge'    => '/\bEdg\b/i',
			'opera'             => '/\bOPR\b/i',
		];

		if ( ! isset( $regexes[ $browser ] ) ) {
			return false;
		}
		$userAgent = ( isset( $_SERVER[ 'HTTP_USER_AGENT' ] ) ? $_SERVER[ 'HTTP_USER_AGENT' ] : '' );
		if ( empty( $userAgent ) ) {
			return false;
		}

		return (bool) preg_match( $regexes[ $browser ], $userAgent );
	}

	/**
	 * Check if browser's match
	 *
	 * @param string $os
	 *
	 * @return bool
	 */
	private static function isOS( $os ) {
		$regexes = [
			'windows' => '/Windows/i',
			'mac_os'  => '/macintosh|mac os|mac_powerpc/i',
			'linux'   => '/linux|ubuntu|freebsd/i',
			'ios'     => '/iphone|ipad|ipod/i',
			'android' => '/android/i',
		];

		if ( ! isset( $regexes[ $os ] ) ) {
			return false;
		}
		$userAgent = ( isset( $_SERVER[ 'HTTP_USER_AGENT' ] ) ? $_SERVER[ 'HTTP_USER_AGENT' ] : '' );
		if ( empty( $userAgent ) ) {
			return false;
		}

		return (bool) preg_match( $regexes[ $os ], $userAgent );
	}
}
