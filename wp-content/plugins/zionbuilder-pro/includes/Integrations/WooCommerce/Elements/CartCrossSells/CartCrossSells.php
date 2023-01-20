<?php

namespace ZionBuilderPro\Integrations\WooCommerce\Elements\CartCrossSells;

use ZionBuilderPro\Integrations\WooCommerce\Elements\WooCommerceElement;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class CartCrossSells
 *
 * @package ZionBuilderPro\Integrations\WooCommerce\Elements
 */
class CartCrossSells extends WooCommerceElement {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'woo-upsells';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Product cross sells', 'zionbuilder-pro' );
	}


	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array The list of element keywords
	 */
	public function get_keywords() {
		return [ 'cross sells', 'woocommerce' ];
	}


	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-woo-product-upsells';
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
			'number_of_posts',
			[
				'type'        => 'text',
				'default'     => '-1',
				'title'       => esc_html__( 'Number of products to show', 'zionbuilder-pro' ),
				'description' => esc_html__( 'Choose how many products you want to show.', 'zionbuilder-pro' ),
			]
		);

		$options->add_option(
			'number_of_columns',
			[
				'type'        => 'number',
				'default'     => 4,
				'title'       => esc_html__( 'Number of columns', 'zionbuilder-pro' ),
				'description' => esc_html__( 'Choose how many columns to use for displaying products.', 'zionbuilder-pro' ),
				'min'         => 1,
				'max'         => 12,
			]
		);

		$options->add_option(
			'orderby',
			[
				'type'    => 'select',
				'default' => 'rand',
				'title'   => esc_html__( 'Order By', 'zionbuilder-pro' ),
				'options' => [
					[
						'name' => esc_html__( 'Random', 'zionbuilder-pro' ),
						'id'   => 'rand',
					],
					[
						'name' => esc_html__( 'Title', 'zionbuilder-pro' ),
						'id'   => 'title',
					],
					[
						'name' => esc_html__( 'ID', 'zionbuilder-pro' ),
						'id'   => 'ID',
					],
					[
						'name' => esc_html__( 'date', 'zionbuilder-pro' ),
						'id'   => 'date',
					],
					[
						'name' => esc_html__( 'modified', 'zionbuilder-pro' ),
						'id'   => 'modified',
					],
					[
						'name' => esc_html__( 'Menu order', 'zionbuilder-pro' ),
						'id'   => 'menu_order',
					],
					[
						'name' => esc_html__( 'price', 'zionbuilder-pro' ),
						'id'   => 'price',
					],
				],
			]
		);

		$options->add_option(
			'order',
			[
				'type'    => 'custom_selector',
				'title'   => esc_html__( 'Order', 'zionbuilder-pro' ),
				'default' => 'desc',
				'options' => [
					[
						'name' => esc_html__( 'Ascending', 'zionbuilder-pro' ),
						'id'   => 'asc',
					],
					[
						'name' => esc_html__( 'Descending', 'zionbuilder-pro' ),
						'id'   => 'desc',
					],
				],
			]
		);
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
		$limit   = $options->get_value( 'number_of_posts', -1 );
		$columns = $options->get_value( 'number_of_columns', 4 );
		$orderby = $options->get_value( 'orderby', 'rand' );
		$order   = $options->get_value( 'order', 'desc' );

		woocommerce_cross_sell_display( $limit, $columns, $orderby, $order );
	}


	public function on_register_styles() {
		$this->register_style_options_element(
			'title_styles',
			[
				'title'                   => esc_html__( 'Title style', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .cross-sells > h2',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'list_item',
			[
				'title'                   => esc_html__( 'Product wrapper style', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .product',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'product_image',
			[
				'title'                   => esc_html__( 'Product image style', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .product .attachment-woocommerce_thumbnail',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'product_title',
			[
				'title'                   => esc_html__( 'Product title style', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .product .woocommerce-loop-product__title',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'product_price',
			[
				'title'                   => esc_html__( 'Product price style', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .product .woocommerce-Price-amount',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);
	}
}
