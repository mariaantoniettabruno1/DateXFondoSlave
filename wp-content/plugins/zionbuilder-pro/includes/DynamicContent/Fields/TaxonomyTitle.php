<?php

namespace ZionBuilderPro\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class TaxonomyTitle
 *
 * @package ZionBuilderPro\DynamicContent\Fields
 */
class TaxonomyTitle extends BaseField {
	public function get_category() {
		return self::CATEGORY_TEXT;
	}

	public function get_group() {
		return 'taxonomy';
	}

	public function get_id() {
		return 'taxonomy-title';
	}

	public function get_name() {
		return esc_html__( 'Archive title', 'zionbuilder-pro' );
	}

	/**
	 * Get Content
	 *
	 * Returns the current post title
	 *
	 * @param mixed $options
	 */
	public function render( $options ) {
		the_archive_title();
	}

	/**
	 * Return the data for this field used in preview/editor
	 */
	public function get_data() {
		return get_the_archive_title();
	}
}
