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
class MediaAuthorProfile extends BaseField {
	public function get_category() {
		return self::CATEGORY_IMAGE;
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
		return isset( $GLOBALS['post'] );
	}

	public function get_id() {
		return 'media-author-image';
	}

	public function get_name() {
		return esc_html__( 'Author Profile Image', 'zionbuilder-pro' );
	}

	/**
	 * Get Content
	 *
	 * Render the selected post's custom field
	 *
	 * @param mixed $options
	 */
	public function render( $options ) {
		$avatar = get_avatar_url( get_the_author_meta( 'ID' ) );

		if ( $avatar ) {
			echo $avatar;
		}
	}

	/**
	 * Return the data for this field used in preview/editor
	 */
	public function get_data() {
		// Display the default value
		$avatar = get_avatar_url( get_the_author_meta( 'ID' ) );

		if ( $avatar ) {
			return $avatar;
		}

		return '';
	}
}
