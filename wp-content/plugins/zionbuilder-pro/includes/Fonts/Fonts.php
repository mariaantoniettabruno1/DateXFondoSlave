<?php


namespace ZionBuilderPro\Fonts;

use ZionBuilderPro\Fonts\Providers\AdobeFontsProvider;
use ZionBuilderPro\Fonts\Providers\CustomFonts;
use ZionBuilder\Plugin;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

class Fonts {
	public function __construct() {
		Plugin::$instance->fonts_manager->register_font_provider( new AdobeFontsProvider() );
		Plugin::$instance->fonts_manager->register_font_provider( new CustomFonts() );
	}

}