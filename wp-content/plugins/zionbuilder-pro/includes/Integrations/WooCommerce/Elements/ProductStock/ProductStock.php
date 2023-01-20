<?php

namespace ZionBuilderPro\Integrations\WooCommerce\Elements\ProductStock;

use ZionBuilderPro\Integrations\WooCommerce\Elements\WooCommerceElement;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class ProductStock
 *
 * @package ZionBuilderPro\Integrations\WooCommerce\Elements
 */
class ProductStock extends WooCommerceElement {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'woo-product-stock';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Product stock', 'zionbuilder-pro' );
	}


	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array The list of element keywords
	 */
	public function get_keywords() {
		return [ 'stock', 'woocommerce' ];
	}


	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-woo-product-stock';
	}


	/**
	 * Get Category
	 *
	 * Will return the element category
	 *
	 * @return string
	 */
	public function get_category() {
		return 'woocommerce';
	}

	/**
	 * Render
	 *
	 * Will render the element based on options
	 *
	 * @param mixed $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		$product = \wc_get_product();

		if ( ! $product ) {
			return $this->render_placeholder_info(
				[
					'description' => esc_html__( 'The current page settings doesn\'t provide a valid product. You can set the active page query and select a product from Page options > Dynamic data preview source', 'zionbuilder-pro' ),
				]
			);
		}

		$stock_html = wc_get_stock_html( $product );

		if ( empty( $stock_html ) ) {
			return $this->render_placeholder_info(
				[
					'description' => esc_html__( 'Stock management is not available for this product. You can set the active page query and select a product from Page options > Dynamic data preview source', 'zionbuilder-pro' ),
				]
			);
		}

		echo $stock_html;
	}

}
