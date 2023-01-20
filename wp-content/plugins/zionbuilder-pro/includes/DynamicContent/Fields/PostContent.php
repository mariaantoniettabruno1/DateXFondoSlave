<?php

namespace ZionBuilderPro\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class PostContent
 *
 * @package ZionBuilderPro\DynamicContent\Fields
 */
class PostContent extends BaseField {
	public function get_category() {
		return self::CATEGORY_TEXT;
	}

	public function get_group() {
		return 'post';
	}

	public function get_id() {
		return 'post-content';
	}

	public function get_name() {
		return esc_html__( 'Post content', 'zionbuilder-pro' );
	}

	/**
	 * Get Content
	 *
	 * Render the current post title
	 *
	 * @param mixed $config
	 */
	public function render( $config ) {
		the_content();
	}

	/**
	 * Return the data for this field used in preview/editor
	 */
	public function get_data() {
		return get_the_content();
	}
}
