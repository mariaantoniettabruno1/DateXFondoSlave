<?php

namespace ZionBuilderPro\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class PostCustomField
 *
 * @package ZionBuilderPro\DynamicContent\Fields
 */
class PostCustomField extends BaseField {
	public function get_category() {
		return self::CATEGORY_TEXT;
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
		return 'post-custom-field';
	}

	public function get_name() {
		return esc_html__( 'Post Custom Field', 'zionbuilder-pro' );
	}

	/**
	 * Get Content
	 *
	 * Render the selected post's custom field
	 *
	 * @param mixed $options
	 */
	public function render( $options ) {
		$post        = $GLOBALS['post'];
		$customField = isset( $options['name'] ) ? $options['name'] : '';
		$value       = '';

		if ( ! empty( $customField ) ) {
			$value = get_post_meta( $post->ID, $customField, true );
			if ( is_array( $value ) ) {
				$value = implode( ' ', $value );
			}
		}
		echo wp_kses_post( $value );
	}

	/**
	 * @return array
	 */
	public function get_options() {
		return [
			'name' => [
				'type'                     => 'select',
				'title'                    => esc_html__( 'Name', 'zionbuilder-pro' ),
				'description'              => esc_html__( 'Select the custom field to show', 'zionbuilder-pro' ),
				'placeholder'              => esc_html__( 'Select custom field', 'zionbuilder-pro' ),
				'dynamic'                  => false,
				'server_callback_method'   => 'get_post_custom_fields',
				'filterable'               => true,
				'addable'                  => true,
				'server_callback_per_page' => -1,
			],
		];
	}
}
