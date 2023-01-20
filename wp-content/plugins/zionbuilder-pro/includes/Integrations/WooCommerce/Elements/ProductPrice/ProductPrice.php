<?php

namespace ZionBuilderPro\Integrations\WooCommerce\Elements\ProductPrice;

use ZionBuilderPro\Integrations\WooCommerce\Elements\WooCommerceElement;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class ProductPrice
 *
 * @package ZionBuilderPro\Integrations\WooCommerce\Elements
 */
class ProductPrice extends WooCommerceElement {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'woo-price';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Product Price', 'zionbuilder-pro' );
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
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array The list of element keywords
	 */
	public function get_keywords() {
		return [ 'price', 'woocommerce' ];
	}

	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-woo-product-price';
	}


	public function options( $options ) {
		// TODO: add option to select the product
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

		echo $product->get_price_html();
	}

	public function on_register_styles() {
		$this->register_style_options_element(
			'symbol_styles',
			[
				'title'                   => esc_html__( 'Price symbol', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .woocommerce-Price-currencySymbol',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'price_ammount',
			[
				'title'                   => esc_html__( 'Price amount', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} ins',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'sale_ammount',
			[
				'title'                   => esc_html__( 'Sale price', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} del',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'tax_label',
			[
				'title'                   => esc_html__( 'Tax label', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .woocommerce-Price-taxLabel',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);
	}
}
