<?php

namespace ZionBuilderPro\Integrations\WooCommerce\Elements\ProductTitle;

use ZionBuilderPro\Integrations\WooCommerce\Elements\WooCommerceElement;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class ProductTitle
 *
 * @package ZionBuilderPro\Integrations\Elements
 */
class ProductTitle extends WooCommerceElement {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'woo-product-title';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Product title', 'zionbuilder-pro' );
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
		return 'element-woo-product-meta';
	}


	public function options( $options ) {
		$options->add_option(
			'tag',
			[
				'type'       => 'select',
				'title'      => esc_html__( 'HTML tag', 'zionbuilder' ),
				'default'    => 'h1',
				'addable'    => true,
				'filterable' => true,
				'options'    => [
					[
						'id'   => 'h1',
						'name' => 'h1',
					],
					[
						'id'   => 'h2',
						'name' => 'h2',
					],
					[
						'id'   => 'h3',
						'name' => 'h3',
					],
					[
						'id'   => 'h4',
						'name' => 'h4',
					],
					[
						'id'   => 'h5',
						'name' => 'h5',
					],
					[
						'id'   => 'h6',
						'name' => 'h6',
					],
					[
						'id'   => 'div',
						'name' => 'div',
					],

					[
						'id'   => 'span',
						'name' => 'span',
					],
				],
			]
		);
	}

	public function get_wrapper_tag( $options ) {
		return $options->get_value( 'tag', 'h1' );
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

		the_title();
	}

	public function server_render( $config ) {
		$tag = isset( $config['element_data']['options']['tag'] ) ? $config['element_data']['options']['tag'] : 'h1';

		echo "<{$tag}>";
		$this->render( $this->options );
		echo "</{$tag}>";
	}
}
