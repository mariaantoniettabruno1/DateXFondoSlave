<?php

namespace ZionBuilderPro\Integrations\WooCommerce\Elements\CartProducts;

use ZionBuilderPro\Integrations\WooCommerce\Elements\WooCommerceElement;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class CartProducts
 *
 * @package ZionBuilderPro\Integrations\WooCommerce\Elements
 */
class CartProducts extends WooCommerceElement {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'woo-cart-products';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Cart products', 'zionbuilder-pro' );
	}


	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array The list of element keywords
	 */
	public function get_keywords() {
		return [ 'cart', 'products' ];
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
		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cart_totals', 10 );
		remove_action( 'woocommerce_before_cart', 'woocommerce_output_all_notices', 10 );
		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display', 10 );

		// Constants.
		wc_maybe_define_constant( 'WOOCOMMERCE_CART', true );

		$nonce_value = wc_get_var( $_REQUEST['woocommerce-shipping-calculator-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.

		// Update Shipping. Nonce check uses new value and old value (woocommerce-cart). @todo remove in 4.0.
		if ( ! empty( $_POST['calc_shipping'] ) && ( wp_verify_nonce( $nonce_value, 'woocommerce-shipping-calculator' ) || wp_verify_nonce( $nonce_value, 'woocommerce-cart' ) ) ) { // WPCS: input var ok.
			WC()->cart->calculate_shipping();
			WC()->cart->calculate_totals();
		}

		// Check cart items are valid.
		do_action( 'woocommerce_check_cart_items' );

		// Calc totals.
		WC()->cart->calculate_totals();

		if ( WC()->cart->is_empty() ) {
			wc_get_template( 'cart/cart-empty.php' );
		} else {
			wc_get_template( 'cart/cart.php' );
		}

		add_action( 'woocommerce_cart_collaterals', 'woocommerce_cart_totals', 10 );
		add_action( 'woocommerce_before_cart', 'woocommerce_output_all_notices', 10 );
		add_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display', 10 );

	}

	public function before_render( $options ) {
		$this->render_attributes->add( 'wrapper', 'class', 'woocommerce' );
		$this->render_attributes->add( 'wrapper', 'class', 'woocommerce-cart' );
	}


	public function on_register_styles() {
		$this->register_style_options_element(
			'update_action_buttons',
			array(
				'title'                   => esc_html__( 'Actions buttons', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .actions button',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			)
		);

		$this->register_style_options_element(
			'coupon_input',
			array(
				'title'                   => esc_html__( 'Coupon input', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .coupon #coupon_code',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			)
		);

		$this->register_style_options_element(
			'coupon_submit',
			array(
				'title'                   => esc_html__( 'Coupon submit button', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .coupon .button',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			)
		);

	}
}
