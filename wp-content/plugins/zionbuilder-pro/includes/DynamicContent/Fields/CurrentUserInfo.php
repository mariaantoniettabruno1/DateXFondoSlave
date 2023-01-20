<?php

namespace ZionBuilderPro\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class CurrentUserInfo
 *
 * @package ZionBuilderPro\DynamicContent\Fields
 */
class CurrentUserInfo extends BaseField {
	public function get_category() {
		return self::CATEGORY_TEXT;
	}

	public function get_group() {
		return 'user';
	}

	public function get_id() {
		return 'current-user-info';
	}

	public function get_name() {
		return esc_html__( 'Current user info', 'zionbuilder-pro' );
	}

	/**
	 * Render the output for this field
	 *
	 * @param mixed $options
	 */
	public function render( $options ) {
		$field    = ( empty( $options['user_info'] ) ? 'display_name' : $options['user_info'] );
		$userInfo = get_userdata( wp_get_current_user()->ID );
		echo wp_kses_post( $userInfo->{$field} );
	}

	/**
	 * @return array
	 */
	public function get_options() {
		return [
			'user_info' => [
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
