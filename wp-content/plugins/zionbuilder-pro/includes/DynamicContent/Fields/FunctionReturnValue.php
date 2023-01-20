<?php

namespace ZionBuilderPro\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class FunctionReturnValue
 *
 * @package ZionBuilderPro\DynamicContent\Fields
 */
class FunctionReturnValue extends BaseField {
	public function get_category() {
		return [
			self::CATEGORY_TEXT,
			self::CATEGORY_LINK,
			self::CATEGORY_IMAGE,
		];
	}

	public function get_group() {
		return 'others';
	}

	public function get_id() {
		return 'function-return-value';
	}

	public function get_name() {
		return esc_html__( 'Function return value', 'zionbuilder-pro' );
	}

	/**
	 * Render the output for this field
	 *
	 * @param mixed $options
	 */
	public function render( $options ) {
		if ( isset( $options['method'] ) ) {
			$arguments = isset( $options['arguments'] ) ? explode( ',', $options['arguments'] ) : [];
			if ( function_exists( $options['method'] ) ) {
				echo call_user_func_array( $options['method'], $arguments );
			} else {
				printf( 'Function %s does not exist', $options['method'] );
			}
		}
	}

	/**
	 * @return array
	 */
	public function get_options() {
		return [
			'method'    => [
				'type'        => 'text',
				'title'       => esc_html__( 'Function', 'zionbuilder-pro' ),
				'description' => esc_html__( 'The output of this function will be displayed. Please note that the function must be publicly available.', 'zionbuilder-pro' ),
				'dynamic'     => false,
				'placeholder' => esc_html__( 'Enter function', 'zionbuilder-pro' ),
			],
			'arguments' => [
				'type'        => 'text',
				'title'       => esc_html__( 'Function arguments', 'zionbuilder-pro' ),
				'description' => esc_html__( 'Enter the function arguments separated by comma.', 'zionbuilder-pro' ),
				'dynamic'     => false,
			],
		];
	}
}
