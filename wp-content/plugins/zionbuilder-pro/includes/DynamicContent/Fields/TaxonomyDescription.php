<?php

namespace ZionBuilderPro\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class TaxonomyDescription
 *
 * @package ZionBuilderPro\DynamicContent\Fields
 */
class TaxonomyDescription extends BaseField {
	public function get_category() {
		return self::CATEGORY_TEXT;
	}

	public function get_group() {
		return 'taxonomy';
	}

	public function get_id() {
		return 'taxonomy-description';
	}

	public function get_name() {
		return esc_html__( 'Archive description', 'zionbuilder-pro' );
	}

	/**
	 * Get Content
	 *
	 * Returns the current post title
	 *
	 * @param mixed $options
	 */
	public function render( $options ) {
		the_archive_description();
	}

	/**
	 * Return the data for this field used in preview/editor
	 */
	public function get_data() {
		return get_the_archive_description();
	}
}
