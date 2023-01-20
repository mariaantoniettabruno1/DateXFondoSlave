<?php

namespace ZionBuilderPro\Integrations\WooCommerce\Elements\ProductRating;

use ZionBuilderPro\Integrations\WooCommerce\Elements\WooCommerceElement;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class ProductRating
 *
 * @package ZionBuilderPro\Integrations\WooCommerce\Elements
 */
class ProductRating extends WooCommerceElement {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'woo-product-rating';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Product rating', 'zionbuilder-pro' );
	}

	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array The list of element keywords
	 */
	public function get_keywords() {
		return [ 'rating', 'woocommerce' ];
	}


	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-woo-product-rating';
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
	 * Registers the element options
	 *
	 * @param \ZionBuilder\Options\Options $options The Options instance
	 *
	 * @return void
	 */
	public function options( $options ) {
		$options->add_option(
			'no_ratings_text',
			[
				'type'  => 'text',
				'title' => __( 'No ratings text', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'stars_color',
			[
				'type'      => 'colorpicker',
				'title'     => __( 'Star color', 'zionbuilder' ),
				'layout'    => 'full',
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .star-rating span::before',
						'value'    => 'color: {{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'empty_stars_color',
			[
				'type'      => 'colorpicker',
				'title'     => __( 'Empty star color', 'zionbuilder' ),
				'layout'    => 'full',
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .star-rating::before',
						'value'    => 'color: {{VALUE}}',
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
		$product = \wc_get_product();

		$are_ratings_available = wc_review_ratings_enabled();

		// Don't proceed if we do not have a post
		if ( ! $are_ratings_available ) {
			return $this->render_placeholder_info(
				[
					'description' => esc_html__( 'Reviews are disabled for products', 'zionbuilder-pro' ),
				]
			);
		}

		// There is no product
		if ( ! $product ) {
			return $this->render_placeholder_info(
				[
					'description' => esc_html__( 'The current page settings doesn\'t provide a valid product. You can set the active page query and select a product from Page options > Dynamic data preview source', 'zionbuilder-pro' ),
				]
			);
		}

		$ratings         = $product->get_rating_count();
		$no_ratings_text = $options->get_value( 'no_ratings_text' );

		if ( ! $ratings ) {
			if ( ! empty( $no_ratings_text ) ) {
				echo $no_ratings_text;
				return;
			} else {
				return $this->render_placeholder_info(
					[
						'description' => esc_html__( 'This product doesn\'t have any ratings', 'zionbuilder-pro' ),
					]
				);
			}
		}

		// Render the ratings
		\woocommerce_template_single_rating();
	}

	public function on_register_styles() {
		$this->register_style_options_element(
			'ratings',
			[
				'title'                   => esc_html__( 'Rating star style', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .star-rating',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'review_link',
			[
				'title'                   => esc_html__( 'Review link styles', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .woocommerce-review-link',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);
	}
}
