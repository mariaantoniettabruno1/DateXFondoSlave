<?php

namespace ZionBuilderPro\Integrations\WooCommerce\Elements\ProductReviewsForm;

use ZionBuilderPro\Integrations\WooCommerce\Elements\WooCommerceElement;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class ProductReviewsForm
 *
 * @package ZionBuilderPro\Integrations\Elements
 */
class ProductReviewsForm extends WooCommerceElement {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'woo-product-reviews-form';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Product reviews form', 'zionbuilder-pro' );
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

		comments_template();
	}

	public function on_register_styles() {
		$this->register_style_options_element(
			'empty_stars',
			array(
				'title'                   => esc_html__( 'Rating empty stars', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .star-rating:before, {{ELEMENT}} .stars a:before,{{ELEMENT}} .stars a:hover~a::before, {{ELEMENT}} .stars.selected a.active~a::before',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			)
		);

		$this->register_style_options_element(
			'rating_filled_stars',
			array(
				'title'                   => esc_html__( 'Rating filled stars', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .star-rating > span:before, {{ELEMENT}} .stars:hover a::before, {{ELEMENT}} .stars.selected a.active::before, {{ELEMENT}} .stars.selected a:not(.active)::before',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			)
		);

		$this->register_style_options_element(
			'submit_button',
			array(
				'title'                   => esc_html__( 'Submit button', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .form-submit .submit',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			)
		);

	}
}
