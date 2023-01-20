<?php

namespace ZionBuilderPro;

use ZionBuilderPro\Utils;
use ZionBuilderPro\Plugin;
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

class Frontend {
	public function __construct() {
		if ( ! is_admin() ) {
			add_action( 'wp_enqueue_scripts', [ $this, 'on_enqueue_scripts' ], 1 );
		}

		add_action( 'wp_enqueue_scripts', [ $this, 'on_enqueue_scripts' ], 1 );
	}

	public function on_enqueue_scripts() {
		if ( is_rtl() ) {
			wp_enqueue_style(
				'zion-pro-frontend-rtl-styles',
				Plugin::instance()->get_root_url() . 'dist/rtl-pro.css',
				[],
				Plugin::instance()->get_version()
			);
		}
	}
}
