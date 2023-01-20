<?php
namespace ZiUltimate\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class PostClass
 *
 * @package ZiUltimate\DynamicContent\Fields
 */
class PostClass extends BaseField {
	public function get_category() {
		return self::CATEGORY_TEXT;
	}

	public function get_group() {
		return 'post';
	}

	public function get_id() {
		return 'post-classes';
	}

	public function get_name() {
		return esc_html__( 'Post classes', 'ziultimate' );
	}

	/**
	 * Get Content
	 *
	 * Returns the current post classs
	 *
	 * @param mixed $options
	 */
	public function render( $options ) {
		$classes = isset( $options['classes'] ) ? $options['classes'] : '';
		echo esc_attr( implode( ' ', get_post_class( $classes ) ) );
	}

	/**
	 * Return the data for this field used in preview/editor
	 */
	public function get_data() {
		return esc_attr( implode( ' ', get_post_class() ) );
	}

	/**
	 * @return array
	 */
	public function get_options() {
		return [
			'classes' => [
				'type' 			=> 'text',
				'title' 		=> esc_html__('Classes'),
				'description' 	=> esc_html__( 'Enter space-separated string of class names.', 'ziultimate' ),
			]
		];
	}
}