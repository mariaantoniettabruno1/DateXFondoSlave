<?php

namespace ZionBuilderPro\Integrations\WooCommerce\Elements\CheckoutThankYou;

use ZionBuilderPro\Integrations\WooCommerce\Elements\WooCommerceElement;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class CheckoutThankYou
 *
 * @since 3.5.0
 *
 * @package ZionBuilderPro\Integrations\WooCommerce\Elements
 */
class CheckoutThankYou extends WooCommerceElement {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'woo-checkout-thank-you';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Checkout thank you', 'zionbuilder-pro' );
	}


	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array The list of element keywords
	 */
	public function get_keywords() {
		return [ 'checkout' ];
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

	public function before_render( $options ) {
		$this->render_attributes->add( 'wrapper', 'class', 'woocommerce' );
	}


	public function options( $options ) {
		$options->add_option(
			'show_thank_you',
			[
				'type'    => 'checkbox_switch',
				'default' => true,
				'title'   => esc_html__( 'Show thank you message', 'zionbuilder' ),
				'layout'  => 'inline',
			]
		);

		$options->add_option(
			'thank_you_text',
			[
				'type'        => 'textarea',
				'placeholder' => esc_html__( 'Thank you. Your order has been received.', 'woocommerce' ),
				'title'       => esc_html__( 'Thank you text', 'zionbuilder' ),
				'dependency'  => [
					[
						'option' => 'show_thank_you',
						'value'  => [ true ],
					],
				],
			]
		);
	}


	/**
	 * Changes the thank you text or hides it
	 *
	 * @return void
	 */
	public function apply_filters() {
		$show_thank_you = $this->options->get_value( 'show_thank_you', true );
		$thank_you_text = $this->options->get_value( 'thank_you_text', '' );
		add_filter(
			'woocommerce_thankyou_order_received_text',
			function ( $message ) use ( $show_thank_you, $thank_you_text ) {
				if ( $show_thank_you === false ) {
					return '';
				}

				if ( ! empty( $thank_you_text ) ) {
					return $thank_you_text;
				}

				return $message;
			}
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
		$this->apply_filters();
		\WC_Shortcode_Checkout::output( [] );
	}

	public function server_render( $config ) {
		$this->apply_filters();
		parent::server_render( $config );

		$this->add_render_body_class( 'woocommerce-checkout' );
		$this->add_render_body_class( 'woocommerce-page' );

		$order  = false;
		$orders = \wc_get_orders(
			[
				'limit' => 1,
			]
		);

		if ( $orders && isset( $orders[0] ) ) {
			$order = $orders[0];
		}

		if ( ! $order ) {
				$this->render_placeholder_info(
					[
						'title'       => esc_html__( 'No valid order could be loaded for preview.', 'zionbuilder-pro' ),
						'description' => esc_html__( 'For best results, you should have at least one WooCommerce order processed in order to display it inside the editor.', 'zionbuilder-pro' ),
					]
				);
				return;
		}

		\wc_get_template( 'checkout/thankyou.php', array( 'order' => $order ) );

	}


	public function on_register_styles() {
		$this->register_style_options_element(
			'thank_you_message',
			array(
				'title'                   => esc_html__( 'Thank you message', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .woocommerce-thankyou-order-received',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			)
		);

		$this->register_style_options_element(
			'order_details_titles',
			array(
				'title'                   => esc_html__( 'Order details titles and values', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .woocommerce-thankyou-order-details li',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			)
		);

		$this->register_style_options_element(
			'order_details_value',
			array(
				'title'                   => esc_html__( 'Order details values', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .woocommerce-thankyou-order-details strong',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			)
		);

		$this->register_style_options_element(
			'order_details_wrapper',
			array(
				'title'                   => esc_html__( 'Order details wrapper', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .woocommerce-order-details',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			)
		);

		$this->register_style_options_element(
			'order_details_title',
			array(
				'title'                   => esc_html__( 'Order details title', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .woocommerce-order-details h2',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			)
		);

		$this->register_style_options_element(
			'customer_details_wrapper',
			array(
				'title'                   => esc_html__( 'Customer details wrapper', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .woocommerce-customer-details',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			)
		);

		$this->register_style_options_element(
			'customer_details_titles',
			array(
				'title'                   => esc_html__( 'Customer details titles', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .woocommerce-customer-details .woocommerce-column__title',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			)
		);

		$this->register_style_options_element(
			'customer_details_billing_address',
			array(
				'title'                   => esc_html__( 'Customer details billing address', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .woocommerce-customer-details .woocommerce-column--billing-address',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			)
		);

		$this->register_style_options_element(
			'customer_details_shipping_address',
			array(
				'title'                   => esc_html__( 'Customer details billing address', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .woocommerce-customer-details .woocommerce-column--shipping-address',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			)
		);
	}
}
