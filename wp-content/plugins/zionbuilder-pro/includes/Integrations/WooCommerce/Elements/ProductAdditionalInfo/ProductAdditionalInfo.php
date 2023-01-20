<?php

namespace ZionBuilderPro\Integrations\WooCommerce\Elements\ProductAdditionalInfo;

use ZionBuilderPro\Integrations\WooCommerce\Elements\WooCommerceElement;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class ProductAdditionalInfo
 *
 * @package ZionBuilderPro\Integrations\Elements
 */
class ProductAdditionalInfo extends WooCommerceElement {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'woo-product-additional-info';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Product additional info', 'zionbuilder-pro' );
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
			'show_heading',
			[
				'type'    => 'checkbox_switch',
				'default' => true,
				'title'   => esc_html__( 'Show heading text', 'zionbuilder' ),
				'layout'  => 'inline',
			]
		);

		$options->add_option(
			'heading_text',
			[
				'type'       => 'text',
				'title'      => esc_html__( 'Heading text', 'zionbuilder' ),
				'dependency' => [
					[
						'option' => 'show_heading',
						'value'  => [ true ],
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

		if ( ! $product ) {
			return $this->render_placeholder_info(
				[
					'description' => esc_html__( 'The current page settings doesn\'t provide a valid product. You can set the active page query and select a product from Page options > Dynamic data preview source', 'zionbuilder-pro' ),
				]
			);
		}

		// Check to see if we need to display the header
		add_filter( 'woocommerce_product_additional_information_heading', [ $this, 'maybe_change_header' ], 99 );

		\wc_get_template( 'single-product/tabs/additional-information.php' );

		remove_filter( 'woocommerce_product_additional_information_heading', [ $this, 'maybe_change_header' ] );
	}

	public function maybe_change_header( $heading ) {
		if ( ! $this->options->get_value( 'show_heading', true ) ) {
			return;
		}

		if ( null !== $this->options->get_value( 'heading_text' ) ) {
			return $this->options->get_value( 'heading_text' );
		}

		return $heading;
	}

	public function on_register_styles() {
		$this->register_style_options_element(
			'heading',
			array(
				'title'                   => esc_html__( 'Heading', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} h2',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			)
		);

		$this->register_style_options_element(
			'table',
			array(
				'title'                   => esc_html__( 'Table', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} table',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			)
		);

		$this->register_style_options_element(
			'table_row',
			array(
				'title'                   => esc_html__( 'Table row', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} tr',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			)
		);

		$this->register_style_options_element(
			'label',
			array(
				'title'                   => esc_html__( 'Label', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} th',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			)
		);

		$this->register_style_options_element(
			'value',
			array(
				'title'                   => esc_html__( 'Value', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} td',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			)
		);
	}
}
