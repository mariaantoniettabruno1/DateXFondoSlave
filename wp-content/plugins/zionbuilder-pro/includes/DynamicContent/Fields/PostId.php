<?php

namespace ZionBuilderPro\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class PostId
 *
 * @package ZionBuilderPro\DynamicContent\Fields
 */
class PostId extends BaseField {
	public function get_category() {
		return self::CATEGORY_TEXT;
	}

	public function get_group() {
		return 'post';
	}

	public function get_id() {
		return 'post-id';
	}

	public function get_name() {
		return esc_html__( 'Post id', 'zionbuilder-pro' );
	}

	/**
	 * Get Content
	 *
	 * Render the current post title
	 *
	 * @param mixed $options
	 */
	public function render( $options ) {
		echo intval( get_the_ID() );
	}

	/**
	 * Return the data for this field used in preview/editor
	 */
	public function get_data() {
		return intval( get_the_ID() );
	}
}
