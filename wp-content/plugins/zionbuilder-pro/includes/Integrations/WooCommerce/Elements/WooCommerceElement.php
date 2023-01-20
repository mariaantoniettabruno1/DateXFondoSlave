<?php

namespace ZionBuilderPro\Integrations\WooCommerce\Elements;

use ZionBuilder\Elements\Element;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class WooCommerceElement
 *
 * This is the base class that will bring helper methods to all WooCommerce elements
 *
 * @package ZionBuilderPro\Integrations\WooCommerce\Elements
 */
class WooCommerceElement extends Element {
	public function server_render( $config ) {
		// Load template actions for frontend since they only load in frontend and wp_ajax actions
		// @see WooCommerce::includes()
		if ( function_exists( 'WC' ) ) {
			// Add the woocommerce css class to body inside editor
			$this->add_render_body_class( 'woocommerce' );

			\WC()->frontend_includes();
			\WC_Template_Loader::init();
			\wc_load_cart();
		}

		parent::server_render( $config );
	}
}
