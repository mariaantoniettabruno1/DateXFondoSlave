<?php

namespace ZionBuilderPro;

use ZionBuilderPro\Plugin;
use ZionBuilderPro\Utils;
use ZionBuilderPro\License;

class Admin {
	public function __construct() {
		add_action( 'zionbuilder/admin/before_admin_scripts', [ $this, 'enqueue_scripts' ] );
	}

	public function enqueue_scripts() {
		wp_enqueue_style(
			'zion-pro-admin-styles',
			Utils::get_file_url( 'dist/admin.css' ),
			[],
			Plugin::instance()->get_version()
		);

		if ( is_rtl() ) {
			wp_enqueue_style(
				'zion-pro-editor-rtl-styles',
				Plugin::instance()->get_root_url() . 'dist/rtl-pro.css',
				[],
				Plugin::instance()->get_version()
			);
		};

		wp_enqueue_script(
			'zion-pro-admin-script',
			Utils::get_file_url( 'dist/admin.js' ),
			[
				'zb-admin',
			],
			Plugin::instance()->get_version(),
			true
		);

		wp_localize_script(
			'zion-pro-admin-script',
			'ZionProRestConfig',
			[
				'nonce'     => wp_create_nonce( 'wp_rest' ),
				'rest_root' => esc_url_raw( rest_url() ),
			]
		);

		wp_localize_script( 'zion-pro-admin-script', 'ZionBuilderProInitialData', $this->get_editor_initial_data() );
	}

	private function get_editor_initial_data() {
		return apply_filters('zionbuilderpro/admin/js_data', [
			'dynamic_fields_data' => Plugin::instance()->dynamic_content_manager->get_fields_data(),
			'dynamic_fields_info' => Plugin::instance()->dynamic_content_manager->get_fields_for_editor(),
			'license_details'     => License::get_license_details(),
			'license_key'         => License::get_license_key(),
			'schemas' => []
		]);
	}
}
