<?php

namespace ZionBuilderPro\Integrations\WooCommerce\Elements\CheckoutOrderReview;

use ZionBuilderPro\Integrations\WooCommerce\Elements\WooCommerceElement;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class CheckoutOrderReview
 *
 * @package ZionBuilderPro\Integrations\Elements
 */
class CheckoutOrderReview extends WooCommerceElement {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'woo-checkout-order-review';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Checkout order review', 'zionbuilder-pro' );
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
		return array( 'woocommerce' );
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

	public function options( $options ) {
		$options->add_option(
			'show_heading',
			[
				'type'      => 'checkbox_switch',
				'default'   => false,
				'title'     => esc_html__( 'Hide heading text', 'zionbuilder' ),
				'layout'    => 'inline',
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .order_review_heading',
						'value'    => 'display: none',
					],
				],
			]
		);
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
		do_action( 'woocommerce_checkout_before_order_review_heading' );
		?>
			<h3 id="order_review_heading"><?php esc_html_e( 'Your order', 'woocommerce' ); ?></h3>

				<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

			<div id="order_review" class="woocommerce-checkout-review-order">
				<?php do_action( 'woocommerce_checkout_order_review' ); ?>
			</div>

		<?php
		do_action( 'woocommerce_checkout_after_order_review' );
	}

	public function server_render( $config ) {
		parent::server_render( $config );

		$this->add_render_body_class( 'woocommerce-checkout' );
		$this->add_render_body_class( 'woocommerce-page' );
	}
}
