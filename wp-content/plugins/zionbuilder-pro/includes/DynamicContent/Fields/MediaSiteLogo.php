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
class MediaSiteLogo extends BaseField {
	public function get_category() {
		return self::CATEGORY_IMAGE;
	}

	public function get_group() {
		return 'site';
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
		return 'media-site-logo';
	}

	public function get_name() {
		return esc_html__( 'Site Logo', 'zionbuilder-pro' );
	}

	/**
	 * Get Content
	 *
	 * Render the selected post's custom field
	 *
	 * @param mixed $options
	 */
	public function render( $options ) {
		$logo  = get_theme_mod( 'custom_logo' );
		$image = wp_get_attachment_image_src( $logo, 'full' );

		if ( isset( $image[0] ) ) {
			echo $image[0];
		}
	}

	/**
	 * Return the data for this field used in preview/editor
	 */
	public function get_data() {
		$logo  = get_theme_mod( 'custom_logo' );
		$image = wp_get_attachment_image_src( $logo, 'full' );

		if ( isset( $image[0] ) ) {
			return $image[0];
		}

		return '';
	}
}
