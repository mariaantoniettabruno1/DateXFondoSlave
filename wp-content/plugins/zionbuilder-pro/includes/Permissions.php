<?php
namespace ZionBuilderPro;

use ZionBuilder\Settings;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

class Permissions {
	public function __construct() {
		add_filter( 'zionbuilder/permissions/allow_edit', [ $this, 'check_if_user_can_edit' ], 10, 3 );
		add_filter( 'zionbuilder/permissions/schema', [ $this, 'add_permissions_options' ] );
		add_filter( 'zionbuilder/permissions', [ $this, 'add_user_permissions' ] );
	}

	public function add_user_permissions( $permissions ) {
		$users_permissions_settings = Settings::get_users_permissions_settings();
		$current_user               = wp_get_current_user();
		$defaults                   = [
			'only_content' => ! $permissions['allowed_access'],
		];

		if ( isset( $users_permissions_settings[ $current_user->ID ] ) ) {
			return wp_parse_args( $users_permissions_settings[ $current_user->ID ], $defaults );
		}

		return $permissions;

	}

	public function add_permissions_options( $schema ) {
		// remove the upgrade to pro
		$schema->remove_option( 'upgrade_message' );

		$schema->add_option(
			'only_content',
			[
				'type'       => 'checkbox_switch',
				'columns'    => 2,
				'title'      => esc_html__( 'Edit only content', 'zionbuilder' ),
				'default'    => false,
				'layout'     => 'inline',
				'dependency' => [
					[
						'option' => 'allowed_access',
						'value'  => [ true ],
					],
				],
			]
		);

		return $schema;
	}

	public function check_if_user_can_edit( $can_edit, $post_id, $post_type ) {
		$user_role_permissions_settings = Settings::get_user_role_permissions_settings();
		$users_permissions_settings     = Settings::get_users_permissions_settings();
		$current_user                   = wp_get_current_user();
		$user_roles                     = (array) $current_user->roles;

		// Check if specific user settings exists
		if ( isset( $users_permissions_settings[ $current_user->ID ] ) ) {
			if ( isset( $users_permissions_settings[ $current_user->ID ]['permissions']['post_types'] ) && in_array( $post_type, $users_permissions_settings[$current_user->ID]['permissions']['post_types'] ) ) {
				return true;
			}
		} else {
			// Check user roles for permissions
			foreach ( $user_roles as $role_id ) {
				if ( isset( $user_role_permissions_settings[$role_id]['permissions']['post_types'] ) && in_array( $post_type, $user_role_permissions_settings[$role_id]['permissions']['post_types'] ) ) {
					return true;
				}
			}
		}

		return $can_edit;
	}
}
