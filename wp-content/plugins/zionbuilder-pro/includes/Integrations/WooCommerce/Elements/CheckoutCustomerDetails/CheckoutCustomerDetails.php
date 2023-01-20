<?php

namespace ZionBuilderPro\Integrations\WooCommerce\Elements\CheckoutCustomerDetails;

use ZionBuilderPro\Integrations\WooCommerce\Elements\WooCommerceElement;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class CheckoutCustomerDetails
 *
 * @package ZionBuilderPro\Integrations\Elements
 */
class CheckoutCustomerDetails extends WooCommerceElement {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'woo-checkout-customer-details';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Checkout customer details', 'zionbuilder-pro' );
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
		return array( 'woocommerce', 'billing details' );
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
						'selector' => '{{ELEMENT}} .woocommerce-billing-fields > h3',
						'value'    => 'display: none',
					],
				],
			]
		);

		$options->add_option(
			'use_placeholders',
			[
				'type'      => 'checkbox_switch',
				'default'   => false,
				'title'     => esc_html__( 'Use placeholders instead of labels', 'zionbuilder' ),
				'layout'    => 'inline',
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .form-row label',
						'value'    => 'display: none',
					],
				],
				'rerender'  => true,
			]
		);

		$options->add_option(
			'hide_additional_info',
			[
				'type'      => 'checkbox_switch',
				'default'   => false,
				'title'     => esc_html__( 'Hide additional information', 'zionbuilder' ),
				'layout'    => 'inline',
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .woocommerce-additional-fields',
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
		// Get checkout object.
		$checkout = WC()->checkout();

		if ( ! $checkout->get_checkout_fields() ) {
			return $this->render_placeholder_info(
				[
					'description' => esc_html__( 'Checkout fields are not defined.', 'zionbuilder-pro' ),
				]
			);
		}

		if ( $options->get_value( 'use_placeholders', false ) === true ) {
			add_filter( 'woocommerce_form_field_args', [ $this, 'change_labels_to_placeholders' ], 10, 3 );
		}

		?>
		<div class="col2-set" id="customer_details">
			<div class="col-1">
				<?php do_action( 'woocommerce_checkout_billing' ); ?>
			</div>

			<div class="col-2">
				<?php do_action( 'woocommerce_checkout_shipping' ); ?>
			</div>
		</div>

		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
		<?php

		// Cleanup
		remove_filter( 'woocommerce_form_field_args', [ $this, 'change_labels_to_placeholders' ], 10, 3 );

	}

	public function change_labels_to_placeholders( $args ) {
		if ( $this->options->get_value( 'use_placeholders', false ) === true ) {
			if ( empty( $args['placeholder'] ) ) {
				$args['placeholder'] = $args['label'];
			}
		}

		return $args;
	}

	public function on_register_styles() {
		$this->register_style_options_element(
			'heading',
			array(
				'title'                   => esc_html__( 'Heading', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .woocommerce-billing-fields > h3',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			)
		);

		$this->register_style_options_element(
			'inputs',
			array(
				'title'                   => esc_html__( 'Inputs', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .form-row input',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			)
		);

		$this->register_style_options_element(
			'labels',
			array(
				'title'                   => esc_html__( 'Labels', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .form-row label',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			)
		);

	}
}
