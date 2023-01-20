<?php

namespace ZionBuilderPro\Integrations\WooCommerce\Elements\Notices;

use ZionBuilderPro\Integrations\WooCommerce\Elements\WooCommerceElement;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Notices
 *
 * @package ZionBuilderPro\Integrations\WooCommerce\Elements
 */
class Notices extends WooCommerceElement {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'woo-notices';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Store notices', 'zionbuilder-pro' );
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
		\woocommerce_output_all_notices();
	}

	public function on_register_styles() {
		$this->register_style_options_element(
			'notice_wraper',
			[
				'title'                   => esc_html__( 'Notices wrapper', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .woocommerce-notices-wrapper',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'notice_message',
			[
				'title'                   => esc_html__( 'Notice message wrapper', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .woocommerce-message ',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);
	}
}
