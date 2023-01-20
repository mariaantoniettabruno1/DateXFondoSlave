<?php

namespace ZionBuilderPro\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class FeaturedImage
 *
 * @package ZionBuilderPro\DynamicContent\Fields
 */
class LinkHomePage extends BaseField {
	public function get_category() {
		return self::CATEGORY_LINK;
	}

	public function get_group() {
		return 'post';
	}

	/**
	 * Will load the field only if the global post is set
	 *
	 * @return boolean
	 */
	public function can_load() {
		return true;
	}

	public function get_id() {
		return 'link-homepage';
	}

	public function get_name() {
		return esc_html__( 'Homepage Link', 'zionbuilder-pro' );
	}

	/**
	 * Get Content
	 *
	 * Render the selected post's custom field
	 *
	 * @param mixed $options
	 */
	public function render( $options ) {
		echo get_home_url();
	}

	/**
	 * Return the data for this field used in preview/editor
	 */
	public function get_data() {
		return get_home_url();
	}
}
