<?php

namespace ZionBuilderPro\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class AuthorInfo
 *
 * @package ZionBuilderPro\DynamicContent\Fields
 */
class AuthorInfo extends BaseField {
	public function get_category() {
		return self::CATEGORY_TEXT;
	}

	public function get_group() {
		return 'author';
	}

	public function get_id() {
		return 'author-info';
	}

	public function get_name() {
		return esc_html__( 'Author info', 'zionbuilder-pro' );
	}

	/**
	 * Render the output for this field
	 *
	 * @param mixed $options
	 */
	public function render( $options ) {
		$field = ( empty( $options['author_info'] ) ? 'display_name' : $options['author_info'] );

		echo wp_kses_post( get_the_author_meta( $field ) );
	}

	/**
	 * Return the data for this field used in preview/editor
	 */
	public function get_data() {
		return get_the_author_meta();
	}

	/**
	 * @return array
	 */
	public function get_options() {
		return [
			'author_info' => [
				'type'        => 'select',
				'title'       => esc_html__( 'Info to display', 'zionbuilder-pro' ),
				'description' => esc_html__( 'Select the desired info you want to display.', 'zionbuilder-pro' ),
				'default'     => 'display_name',
				'dynamic'     => false,
				'options'     => [
					[
						'id'   => 'display_name',
						'name' => esc_html__( 'Display name', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'first_name',
						'name' => esc_html__( 'First name', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'last_name',
						'name' => esc_html__( 'Last name', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'user_description',
						'name' => esc_html__( 'Description', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'ID',
						'name' => esc_html__( 'ID', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'user_nicename',
						'name' => esc_html__( 'Nickname', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'user_email',
						'name' => esc_html__( 'Email', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'user_url',
						'name' => esc_html__( 'Website', 'zionbuilder-pro' ),
					],
				],
			],

		];
	}
}
