<?php
namespace ZionBuilderPro\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class PostExcerpt
 * @package ZionBuilderPro\DynamicContent\Fields
 */
class PostExcerpt extends BaseField {
	public function get_category() {
		return self::CATEGORY_TEXT;
	}

	public function get_group() {
		return 'post';
	}

	public function get_id() {
		return 'post-excerpt';
	}

	public function get_name() {
		return esc_html__( 'Post excerpt', 'zionbuilder-pro' );
	}

	public static function start_excerpt_change() {
		add_filter( 'excerpt_length', [ __CLASS__, 'set_excerpt_length' ] );
	}

	public function set_excerpt_length( $length ) {

	}

	/**
	 * Get Content
	 *
	 * Render the current post title
	 *
	 * @since 3.1.0 Added ability to set custom length
	 *
	 * @param array $options The options set to configure this field
	 */
	public function render( $options ) {
		$new_length = isset( $options['excerpt_length'] ) ? $options['excerpt_length'] : null;

		// Check to see if we have a custom excerpt length
		if ( $new_length ) {
			$excerpt_more = apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );
			echo wp_trim_words( get_the_excerpt(), $new_length, $excerpt_more );
		} else {
			the_excerpt();
		}
	}

	/**
	 * @return array
	 */
	public function get_options() {
		return [
			'excerpt_length' => [
				'type'    => 'number',
				'title'   => esc_html__( 'Excerpt length', 'zionbuilder-pro' ),
				'dynamic' => false,
			],
		];
	}

}
