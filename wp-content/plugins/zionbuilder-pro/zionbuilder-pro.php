<?php
/*
Plugin Name: Zion Builder Pro
Plugin URI: https://zionbuilder.io/?utm_source=wp-plugins&utm_medium=plugin-code&utm_campaign=plugin-uri
Description: The superlative of ZionBuilder. All the power of the page builder unleashed.
Version: 3.5.0
Author: zionbuilder.io
Author URI: https://zionbuilder.io/?utm_source=wp-plugins&utm_medium=plugin-code&utm_campaign=author-uri
Text Domain: zionbuilder-pro
Domain Path: /languages

*/

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

require __DIR__ . '/vendor/autoload.php';
require dirname( __FILE__ ) . '/includes/Plugin.php';

new ZionBuilderPro\Plugin( __FILE__ );
