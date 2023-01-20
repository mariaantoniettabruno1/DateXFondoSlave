<?php

namespace ZionBuilderPro\Integrations\WooCommerce\Elements\ProductDescription;

use ZionBuilderPro\Integrations\WooCommerce\Elements\WooCommerceElement;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class ProductDescription
 *
 * @package ZionBuilderPro\Integrations\WooCommerce\Elements
 */
class ProductDescription extends WooCommerceElement {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'woo-description';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Product description', 'zionbuilder-pro' );
	}


	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array The list of element keywords
	 */
	public function get_keywords() {
		return [ 'Product description', 'woocommerce' ];
	}


	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-woo-description';
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
		$options->add_option(
			'description_type',
			[
				'type'        => 'select',
				'default'     => 'short',
				'title'       => esc_html__( 'Description type', 'zionbuilder-pro' ),
				'description' => esc_html__( 'Choose what type of description for this product you want to show', 'zionbuilder-pro' ),
				'options'     => [
					[
						'name' => esc_html__( 'Short', 'zionbuilder-pro' ),
						'id'   => 'short',
					],
					[
						'name' => esc_html__( 'Full', 'zionbuilder-pro' ),
						'id'   => 'full',
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
		$description_type = $options->get_value( 'description_type', 'short' );
		$product          = \wc_get_product();

		if ( ! $product ) {
			return $this->render_placeholder_info(
				[
					'description' => esc_html__( 'The current page settings doesn\'t provide a valid product. You can set the active page query and select a product from Page options > Dynamic data preview source', 'zionbuilder-pro' ),
				]
			);
		}

		if ( 'short' === $description_type ) {
			\woocommerce_template_single_excerpt();
		} else {
			the_content();
		}
	}
}
