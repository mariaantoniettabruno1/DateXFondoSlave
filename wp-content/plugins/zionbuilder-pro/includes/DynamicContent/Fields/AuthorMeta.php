<?php

namespace ZionBuilderPro\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class AuthorMeta
 *
 * @package ZionBuilderPro\DynamicContent\Fields
 */
class AuthorMeta extends BaseField {
	public function get_category() {
		return self::CATEGORY_TEXT;
	}

	public function get_group() {
		return 'author';
	}

	public function get_id() {
		return 'author-meta';
	}

	public function get_name() {
		return esc_html__( 'Author meta', 'zionbuilder-pro' );
	}

	/**
	 * Will load the field only if the global post is set
	 *
	 * @return boolean
	 */
	public function can_load() {
		return isset( $GLOBALS['post'] );
	}

	/**
	 * Render the output for this field
	 *
	 * @param mixed $options
	 */
	public function render( $options ) {
		global $post;

		$field  = ( empty( $options['author_meta'] ) ? '' : $options['author_meta'] );
		$output = ( empty( $field ) ? '' : get_user_meta( $post->post_author, $field, true ) );
		echo wp_kses_post( $output );
	}

	/**
	 * @return array
	 */
	public function get_options() {
		global $post;

		$options = [];
		$keys    = array_keys( get_user_meta( $post->post_author ) );
		foreach ( $keys as $key ) {
			$options[] = [
				'id'   => $key,
				'name' => $key,
			];
		}

		return [
			'author_meta' => [
				'type'        => 'select',
				'title'       => esc_html__( 'Meta field', 'zionbuilder-pro' ),
				'description' => esc_html__( 'Select the desired meta field for which you want to display the value.', 'zionbuilder-pro' ),
				'default'     => ( empty(  $options ) ? '' : $options[0]['id'] ),
				'dynamic'     => false,
				'options'     => $options,
			],
		];
	}
}
