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
class LinkPostLink extends BaseField {
	public function get_category() {
		return self::CATEGORY_LINK;
	}

	public function get_group() {
		return 'post';
	}

	public function get_id() {
		return 'link-post-link';
	}

	public function get_name() {
		return esc_html__( 'Post/Page Link', 'zionbuilder-pro' );
	}

	/**
	 * Get Content
	 *
	 * Render the selected post's custom field
	 *
	 * @param mixed $options
	 */
	public function render( $options ) {
		$post_id = ! empty( $options['post_id'] ) ? $options['post_id'] : 0;
		$link    = get_permalink( $post_id );

		if ( $link ) {
			echo $link;
		}
	}

	/**
	 * Return the data for this field used in preview/editor
	 */
	public function get_data() {
		// Display the default value
		$link = get_permalink();

		if ( $link ) {
			return $link;
		}

		return '';
	}

	/**
	 * @return array
	 */
	public function get_options() {
		return [
			'post_id' => [
				'type'                   => 'select',
				'title'                  => esc_html__( 'Post/page', 'zionbuilder-pro' ),
				'description'            => esc_html__( 'Select the desired post/page you want to link to.', 'zionbuilder-pro' ),
				'default'                => '',
				'dynamic'                => false,
				'server_callback_method' => 'get_all_posts',
				'placeholder'            => esc_html__( 'Select text', 'zionbuilder-pro' ),
				'filterable'             => true,
				'addable'                => false,
				'options'                => [
					[
						'name' => esc_html__( 'Current post', 'zionbuilder-pro' ),
						'id'   => '',
					],
				],
			],
		];
	}
}
