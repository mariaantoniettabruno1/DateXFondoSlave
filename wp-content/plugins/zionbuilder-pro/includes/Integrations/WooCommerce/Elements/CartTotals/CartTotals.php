<?php

namespace ZionBuilderPro\Integrations\WooCommerce\Elements\CartTotals;

use ZionBuilderPro\Integrations\WooCommerce\Elements\WooCommerceElement;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class CartTotals
 *
 * @package ZionBuilderPro\Integrations\WooCommerce\Elements
 */
class CartTotals extends WooCommerceElement {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'woo-cart-totals';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Cart totals', 'zionbuilder-pro' );
	}


	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array The list of element keywords
	 */
	public function get_keywords() {
		return [ 'cart', 'totals' ];
	}


	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-woo-add-to-cart';
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

	public function options( $options ) {

	}


	public function before_render( $options ) {
		$this->render_attributes->add( 'wrapper', 'class', 'woocommerce' );
		$this->render_attributes->add( 'wrapper', 'class', 'woocommerce-cart' );
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
		woocommerce_cart_totals();
	}


	public function on_register_styles() {
		$this->register_style_options_element(
			'title_styles',
			[
				'title'                   => esc_html__( 'Title style', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .cart_totals  > h2',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'list_item',
			[
				'title'                   => esc_html__( 'Subtotal title styles', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .cart-subtotal th',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'product_image',
			[
				'title'                   => esc_html__( 'Subtotal ammount styles', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .cart-subtotal .woocommerce-Price-amount amount',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'product_title',
			[
				'title'                   => esc_html__( 'Total title styles', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .order-total .th',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'product_price',
			[
				'title'                   => esc_html__( 'Total ammount styles', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .order-total .woocommerce-Price-amount',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'product_price',
			[
				'title'                   => esc_html__( 'Proceed to checkout styles', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .wc-proceed-to-checkout .checkout-button',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);
	}
}
