<?php

namespace ZionBuilderPro\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class SiteTitle
 *
 * @package ZionBuilderPro\DynamicContent\Fields
 */
class SiteTitle extends BaseField {
	public function get_category() {
		return self::CATEGORY_TEXT;
	}

	public function get_group() {
		return 'site';
	}

	public function get_id() {
		return 'site-title';
	}

	public function get_name() {
		return esc_html__( 'Site title', 'zionbuilder-pro' );
	}

	/**
	 * Render the field's value
	 *
	 * @param mixed $options
	 */
	public function render( $options ) {
		echo wp_kses_post( get_bloginfo( 'title' ) );
	}

	/**
	 * Return the data for this field used in preview/editor
	 */
	public function get_data() {
		return get_bloginfo( 'title' );
	}
}
