<?php

namespace ZionBuilderPro\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class SiteEmailAddress
 *
 * @package ZionBuilderPro\DynamicContent\Fields
 */
class SiteEmailAddress extends BaseField {
	public function get_category() {
		return self::CATEGORY_TEXT;
	}

	public function get_group() {
		return 'site';
	}

	public function get_id() {
		return 'site-email-address';
	}

	public function get_name() {
		return esc_html__( 'Site email address', 'zionbuilder-pro' );
	}

	/**
	 * Render the field's value
	 *
	 * @param mixed $options
	 */
	public function render( $options ) {
		echo wp_kses_post( get_bloginfo( 'admin_email' ) );
	}

	/**
	 * Return the data for this field used in preview/editor
	 */
	public function get_data() {
		return get_bloginfo( 'admin_email' );
	}
}
