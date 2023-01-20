<?php
namespace ZiUltimate\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class ParentTitle
 *
 * @package ZiUltimate\DynamicContent\Fields
 */
class ParentTitle extends BaseField {
	public function get_category() {
		return self::CATEGORY_TEXT;
	}

	public function get_group() {
		return 'post';
	}

	public function get_id() {
		return 'parent-title';
	}

	public function get_name() {
		return esc_html__( 'Parent title', 'ziultimate' );
	}

	/**
	 * Get Content
	 *
	 * Render the current post title
	 *
	 * @param mixed $config
	 */
	public function render( $config ) {
		global $post;

		if( empty( $post->post_parent ) || $post->post_parent <= 0 )
			return;

		echo wp_kses_post( get_the_title( $post->post_parent ) );
	}

	/**
	 * Return the data for this field used in preview/editor
	 */
	public function get_data() {
		global $post;

		if( empty( $post->post_parent ) || $post->post_parent <= 0 )
			return;

		return wp_kses_post( get_the_title( $post->post_parent ) );
	}
}