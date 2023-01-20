<?php

namespace ZionBuilderPro;

use ZionBuilder\Permissions as FreeBuilderPermissions;
use ZionBuilderPro\Plugin;
use ZionBuilderPro\Utils;
use ZionBuilder\CommonJS;


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MegaMenu {
	const OPTIONS_META_FIELD      = 'zb_mega_menu_config';
	const PAGEBUILDER_TEMPLATE_ID = 'zb_mega_menu_template_id';

	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'on_admin_enqueue_scripts' ] );
		add_action( 'before_delete_post', [ $this, 'on_before_delete_post' ] );

		// add support for zb on nav menu items
		add_post_type_support( 'nav_menu_item', FreeBuilderPermissions::POST_TYPE_EDIT_PERMISSION );
	}

	public function on_before_delete_post( $post_id ) {
		$mega_menu_template = self::get_pagebuilder_template( $post_id );

		if ( $mega_menu_template ) {
			wp_delete_post( $mega_menu_template );
		}
	}

	public static function get_pagebuilder_template( $menu_item_id ) {
		return get_post_meta( $menu_item_id, self::PAGEBUILDER_TEMPLATE_ID, true );
	}

	public static function set_pagebuilder_template( $menu_item_id, $template_id ) {
		return update_post_meta( $menu_item_id, self::PAGEBUILDER_TEMPLATE_ID, $template_id );
	}

	public static function get_config_for_item( $menu_item_id ) {
		$mega_menu_config = get_post_meta( $menu_item_id, self::OPTIONS_META_FIELD, true );

		return json_decode( $mega_menu_config, true );
	}

	public static function update_config_for_item( $menu_item_id, $config ) {
		return update_post_meta( $menu_item_id, self::OPTIONS_META_FIELD, json_encode( $config ) );
	}

	public function on_admin_enqueue_scripts( $hook ) {
		// Check the hook so that the .css is only added to the .php file where we need it
		if ( 'nav-menus.php' !== $hook ) {
			return;
		}

		wp_enqueue_media();
		CommonJS::register_scripts();

		// Load roboto font
		wp_enqueue_style( 'znpb-roboto-font', 'https://fonts.googleapis.com/css?family=Roboto:400,400i,500,500i,700,700i&display=swap&subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese', [], Plugin::instance()->get_version() );

		// Load mega menu styles
		wp_enqueue_style(
			'zb-pro-mega-menu-style',
			Utils::get_file_url( 'dist/mega-menu.css' ),
			[],
			Plugin::instance()->get_version()
		);

		// Load mega menu scripts
		wp_enqueue_script(
			'zb-pro-mega-menu-script',
			Utils::get_file_url( 'dist/mega-menu.js' ),
			[
				'zb-vue',
			],
			Plugin::instance()->get_version(),
			true
		);

		CommonJS::localize_common_js_data( 'zb-pro-mega-menu-script' );

		wp_localize_script(
			'zb-pro-mega-menu-script',
			'ZionProRestConfig',
			[
				'nonce'          => wp_create_nonce( 'wp_rest' ),
				'rest_root'      => esc_url_raw( rest_url() ),
				'options_schema' => $this->get_options_schema(),
			]
		);
	}

	public function get_options_schema() {
		return [
			[
				'id'     => 'content',
				'title'  => esc_html__( 'Content', 'zionbuilder-pro' ),
				'depth'  => 'first',
				'schema' => [
					'content_enabled' => [
						'type'        => 'checkbox_switch',
						'default'     => false,
						'description' => esc_html__( 'Enable the mega menu content on this menu item', 'zionbuiulder-pro' ),
						'title'       => esc_html__( 'Enable mega menu content', 'zionbuiulder-pro' ),
						'layout'      => 'inline',
					],
				],
			],
			[
				'id'     => 'general',
				'title'  => esc_html__( 'General', 'zionbuilder-pro' ),
				'schema' => [
					'show_title' => [
						'type'        => 'checkbox_switch',
						'default'     => true,
						'description' => esc_html__( 'Show the item title or not', 'zionbuiulder-pro' ),
						'title'       => esc_html__( 'Show title', 'zionbuiulder-pro' ),
						'layout'      => 'inline',
					],
				],
			],
			[
				'id'     => 'display',
				'title'  => esc_html__( 'Display', 'zionbuilder-pro' ),
				'depth'  => 'first',
				'schema' => [
					'submenu_width'        => [
						'type'        => 'select',
						'default'     => 'default',
						'description' => 'Submenu width.',
						'title'       => 'Submenu width.',
						'options'     => [
							[
								'id'   => 'default',
								'name' => 'Default',
							],
							[
								'id'   => 'full',
								'name' => 'Full width',
							],
							[
								'id'   => 'container',
								'name' => esc_html__( 'Container width', 'zionbuilder-pro' ),
							],
							[
								'id'   => 'custom',
								'name' => 'Custom width',
							],
						],
					],
					'submenu_width_custom' => [
						'type'         => 'number_unit',
						'default'      => '',
						'title'        => esc_html__( 'Submenu custom width', 'zionbuilder-pro' ),
						'default_unit' => 'px',
						'dependency'   => [
							[
								'option' => 'submenu_width',
								'value'  => [ 'custom' ],
							],
						],
					],
					'submenu_position'     => [
						'type'    => 'custom_selector',
						'default' => 'relative',
						'title'   => esc_html__( 'Submenu position', 'zionbuilder-pro' ),
						'options' => [
							[
								'id'   => 'centered',
								'name' => 'Centered',
							],
							[
								'id'   => 'relative',
								'name' => 'Relative',
							],
						],
					],
				],
			],
			[
				'id'     => 'icon',
				'title'  => esc_html__( 'Icon', 'zionbuilder-pro' ),
				'schema' => [
					'icon'          => [
						'type'  => 'icon_library',
						'title' => esc_html__( 'Icon', 'zionbuilder-pro' ),
					],
					'icon_position' => [
						'type'    => 'select',
						'default' => 'left',
						'title'   => esc_html__( 'Icon position', 'zionbuilder-pro' ),
						'options' => [
							[
								'id'   => 'left',
								'name' => esc_html__( 'left', 'zionbuilder-pro' ),
							],
							[
								'id'   => 'right',
								'name' => esc_html__( 'right', 'zionbuilder-pro' ),
							],
							[
								'id'   => 'top',
								'name' => esc_html__( 'top', 'zionbuilder-pro' ),
							],
							[
								'id'   => 'bottom',
								'name' => esc_html__( 'bottom', 'zionbuilder-pro' ),
							],
						],
					],
				],
			],
			[
				'id'     => 'badge',
				'title'  => esc_html__( 'Badge', 'zionbuilder-pro' ),
				'schema' => [
					'badget_text' => [
						'type'  => 'text',
						'title' => esc_html__( 'Badge text', 'zionbuilder-pro' ),
					],
					'badge_color' => [
						'type'   => 'colorpicker',
						'title'  => esc_html__( 'Badge color', 'zionbuilder-pro' ),
						'layout' => 'inline',
					],
					'text_color'  => [
						'type'   => 'colorpicker',
						'title'  => esc_html__( 'Badge text color', 'zionbuilder-pro' ),
						'layout' => 'inline',
					],
				],
			],
		];
	}
}
