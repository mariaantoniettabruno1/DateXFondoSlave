<?php

namespace ZionBuilderPro\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class PostDate
 *
 * @package ZionBuilderPro\DynamicContent\Fields
 */
class PostDate extends BaseField {
	public function get_category() {
		return self::CATEGORY_TEXT;
	}

	public function get_group() {
		return 'post';
	}

	public function get_id() {
		return 'post-date';
	}

	public function get_name() {
		return esc_html__( 'Post Date', 'zionbuilder-pro' );
	}

	/**
	 * Get Content
	 *
	 * Returns the current post title
	 *
	 * @param mixed $options
	 */
	public function render( $options ) {
		$date_type = isset( $options['type'] ) ? $options['type'] : 'post_modified_gmt';
		$format    = isset( $options['format'] ) ? $options['format'] : '';
		$date      = '';

		if ( 'human_readable' === $format ) {
			/* translators: %s: Post time in readable format. */
			$date = sprintf( __( '%s ago', 'zionbuilder-pro' ), human_time_diff( strtotime( get_post()->{$date_type} ) ) );
		} else {
			if ( 'custom' === $format ) {
				$format = isset( $options['custom_format'] ) ? $options['custom_format'] : '';
			}

			if ( 'post_modified_gmt' === $date_type ) {
				$date = get_the_modified_date( $format );
			} else {
				$date = get_the_date( $format );
			}
		}

		echo wp_kses_post( $date );
	}

	/**
	 * Return the data for this field used in preview/editor
	 */
	public function get_data() {
		return wp_kses_post( get_the_date() );
	}

	/**
	 * @return array
	 */
	public function get_date_formats() {
		$default_date_formats = [
			[
				'id'   => '',
				'name' => esc_html__( 'Default', 'zionbuilder-pro' ),
			],
		];

		$date_formats = array_unique( apply_filters( 'date_formats', [ __( 'F j, Y' ), 'Y-m-d', 'm/d/Y', 'd/m/Y' ] ) );

		foreach ( $date_formats as $date_format ) {
			$default_date_formats[] = [
				'id'   => $date_format,
				'name' => $date_format,
			];
		}

		// Add human readable
		$default_date_formats[] = [
			'id'   => 'human_readable',
			'name' => esc_html__( 'Human readable', 'zionbuilder-pro' ),
		];

		// Add Custom format
		$default_date_formats[] = [
			'id'   => 'custom',
			'name' => esc_html__( 'Custom', 'zionbuilder-pro' ),
		];

		return $default_date_formats;
	}

	/**
	 * @return array
	 */
	public function get_options() {
		return [
			'type'          => [
				'type'        => 'select',
				'title'       => esc_html__( 'Type', 'zionbuilder-pro' ),
				'description' => esc_html__( 'Select the post date to show', 'zionbuilder-pro' ),
				'default'     => 'post_modified_gmt',
				'dynamic'     => false,
				'options'     => [
					[
						'name' => esc_html__( 'Post published', 'zionbuilder-pro' ),
						'id'   => 'post_date_gmt',
					],
					[
						'name' => esc_html__( 'Post modified', 'zionbuilder-pro' ),
						'id'   => 'post_modified_gmt',
					],
				],
			],
			'format'        => [
				'type'        => 'select',
				'title'       => esc_html__( 'Date format', 'zionbuilder-pro' ),
				'description' => esc_html__( 'Select the date format you want to use.', 'zionbuilder-pro' ),
				'default'     => '',
				'options'     => $this->get_date_formats(),
				'dynamic'     => false,
			],
			'custom_format' => [
				'type'        => 'text',
				'title'       => esc_html__( 'Custom date format', 'zionbuilder-pro' ),
				'description' => esc_html__( 'Enter the custom date format you want to use.', 'zionbuilder-pro' ),
				'default'     => '',
				'dynamic'     => false,
				'dependency'  => [
					[
						'option' => 'format',
						'value'  => [ 'custom' ],
					],
				],
			],
		];
	}
}
