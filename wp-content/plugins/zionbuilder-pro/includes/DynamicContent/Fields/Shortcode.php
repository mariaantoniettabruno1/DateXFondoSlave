<?php

namespace ZionBuilderPro\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Shortcode
 *
 * @package ZionBuilderPro\DynamicContent\Fields
 */
class Shortcode extends BaseField {
	public function get_category() {
		return self::CATEGORY_TEXT;
	}

	public function get_group() {
		return 'others';
	}

	public function get_id() {
		return 'shortcode';
	}

	public function get_name() {
		return esc_html__( 'Shortcode', 'zionbuilder-pro' );
	}

	/**
	 * Render the output for this field
	 *
	 * @param mixed $options
	 */
	public function render( $options ) {
		$shortcode = ( empty( $options['shortcode'] ) ? '' : $options['shortcode'] );
		$output    = ( empty( $shortcode ) ? '' : do_shortcode( $shortcode ) );
		echo $output;
	}

	/**
	 * @return array
	 */
	public function get_options() {
		return [
			'shortcode' => [
				'type'        => 'text',
				'title'       => esc_html__( 'Enter shortcode', 'zionbuilder-pro' ),
				'description' => esc_html__( 'Will display the value of a shortcode.', 'zionbuilder-pro' ),
				'default'     => ', ',
				'dynamic'     => false,
			],
		];
	}
}
