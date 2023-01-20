<?php

namespace ZionBuilderPro\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class CurrentUserMeta
 *
 * @package ZionBuilderPro\DynamicContent\Fields
 */
class CurrentUserMeta extends BaseField {
	public function get_category() {
		return self::CATEGORY_TEXT;
	}

	public function get_group() {
		return 'user';
	}

	public function get_id() {
		return 'current-user-meta';
	}

	public function get_name() {
		return esc_html__( 'Current user meta', 'zionbuilder-pro' );
	}

	/**
	 * Render the output for this field
	 *
	 * @param mixed $options
	 */
	public function render( $options ) {
		$field  = ( empty( $options['user_meta'] ) ? '' : $options['user_meta'] );
		$output = ( empty( $field ) ? '' : get_user_meta( wp_get_current_user()->ID, $field, true ) );
		echo wp_kses_post( $output );
	}


	/**
	 * @return array
	 */
	public function get_options() {
		$options = [];
		$keys    = array_keys( get_user_meta( wp_get_current_user()->ID ) );
		foreach ( $keys as $key ) {
			$options[] = [
				'id'   => $key,
				'name' => $key,
			];
		}

		return [
			'user_meta' => [
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
