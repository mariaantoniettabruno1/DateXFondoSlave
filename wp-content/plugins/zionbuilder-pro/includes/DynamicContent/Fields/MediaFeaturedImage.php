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
class MediaFeaturedImage extends BaseField {
	public function get_category() {
		return self::CATEGORY_IMAGE;
	}

	public function get_group() {
		return 'post';
	}

	public function get_id() {
		return 'media-featured-image';
	}

	public function get_name() {
		return esc_html__( 'Featured image', 'zionbuilder-pro' );
	}

	/**
	 * Get Content
	 *
	 * Render the selected post's custom field
	 *
	 * @param mixed $options
	 */
	public function render( $options ) {
		global $post;

		if ( $post && has_post_thumbnail( $post ) ) {
			echo get_the_post_thumbnail_url();
		}
	}

	/**
	 * Return the data for this field used in preview/editor
	 */
	public function get_data() {
		global $post;

		// Display the default value
		if ( $post && has_post_thumbnail( $post ) ) {
			return get_the_post_thumbnail_url();
		}

		return '';
	}
}
