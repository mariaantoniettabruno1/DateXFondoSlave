<?php

namespace ZionBuilderPro\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class PostTitle
 *
 * @package ZionBuilderPro\DynamicContent\Fields
 */
class PostTitle extends BaseField {
	public function get_category() {
		return self::CATEGORY_TEXT;
	}

	public function get_group() {
		return 'post';
	}

	public function get_id() {
		return 'post-title';
	}

	public function get_name() {
		return esc_html__( 'Post title', 'zionbuilder-pro' );
	}

	/**
	 * Get Content
	 *
	 * Render the current post title
	 *
	 * @param mixed $config
	 */
	public function render( $config ) {
		echo wp_kses_post( get_the_title() );
	}

	/**
	 * Return the data for this field used in preview/editor
	 */
	public function get_data() {
		return wp_kses_post( get_the_title() );
	}
}
