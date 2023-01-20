<?php
namespace ZiUltimate\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class GetQueryString
 *
 * @package ZiUltimate\DynamicContent\Fields
 */
class GetQueryString extends BaseField {
	public function get_category() {
		return self::CATEGORY_TEXT;
	}

	public function get_group() {
		return 'others';
	}

	public function get_id() {
		return 'get-query-string';
	}

	public function get_name() {
		return esc_html__( 'Get Query String Value', 'ziultimate' );
	}

	/**
	 * Get Content
	 *
	 * Returns the query string value
	 *
	 * @param mixed $options
	 */
	public function render( $options ) {
		$query_string = ( ! empty( $options['query_string'] ) ? $options['query_string'] : '' );
		
		if( ! empty( $query_string ) && isset( $_GET[ $query_string ] ) )
			echo wp_kses_post( $_GET[ $query_string ] );
	}

	/**
	 * @return array
	 */
	public function get_options() {
		return [
			'query_string' => [
				'type' 			=> 'text',
				'title' 		=> esc_html__('Query String'),
				'description' 	=> esc_html__( 'Enter query string name.', 'ziultimate' ),
			]
		];
	}
}