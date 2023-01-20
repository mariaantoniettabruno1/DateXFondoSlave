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
class LinkAuthorPage extends BaseField {
	public function get_category() {
		return self::CATEGORY_LINK;
	}

	public function get_group() {
		return 'post';
	}

	public function get_id() {
		return 'link-author-page';
	}

	public function get_name() {
		return esc_html__( 'Author Page Link', 'zionbuilder-pro' );
	}

	/**
	 * Get Content
	 *
	 * Render the selected post's custom field
	 *
	 * @param mixed $options
	 */
	public function render( $options ) {
		$link = get_author_posts_url( get_the_author_meta( 'ID' ) );

		if ( $link ) {
			echo $link;
		}
	}

	/**
	 * Return the data for this field used in preview/editor
	 */
	public function get_data() {
		// Display the default value
		$link = get_author_posts_url( get_the_author_meta( 'ID' ) );

		if ( $link ) {
			return $link;
		}

		return '';
	}
}
