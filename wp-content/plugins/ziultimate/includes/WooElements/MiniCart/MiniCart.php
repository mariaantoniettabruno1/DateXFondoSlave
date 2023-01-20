<?php
namespace ZiUltimate\WooElements\MiniCart;

use ZiUltimate\UltimateElements;
use ZiUltimate\Utils;
use ZiUltimate\Admin\License;
use ZionBuilder\Options\BaseSchema;
use ZionBuilder\Options\Schemas\StyleOptions;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class MiniCart
 *
 * @package ZiUltimate\WooElements
 */
class MiniCart extends UltimateElements {

    public function get_type() {
		return 'zu_mini_cart';
	}

	public function get_name() {
		return __( 'Mini Cart', 'ziultimate' );
	}

	public function get_keywords() {
		return [ 'cart', 'mini', 'off canvas cart' ];
	}

	/*public function get_label() {
		return [
			'text'  => $this->get_label_text(),
			'color' => $this->get_label_color(),
		];
	}*/

	public function get_category() {
		return $this->zuwoo_elements_category();
	}

	public function options( $options ) {
		if( ! License::has_valid_license() ) {
			$title = $this->get_name();
			$description = 'Creates the cart fragments.';
			$options->add_option(
				'el',
				[
					'type' 		=> 'html',
					'content' 	=> self::getHTMLContent($title, $description)
				]
			);

			return;
		}

		$options->add_option(
			'used_in_canvas',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> esc_html__('Using inside the off-canvas element?', 'ziultimate'),
				'default' 	=> 'no',
				'options' 	=> [
					[
						'name' 	=> esc_html__('No'),
						'id' 	=> 'no'
					],
					[
						'name' 	=> esc_html__('Yes'),
						'id' 	=> 'yes'
					]
				]
			]
		);

		$options->add_option(
			'slide_panel',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> esc_html__('Slide out the off-canvas panel?', 'ziultimate'),
				'description' => esc_html__('Off-canvas panel will automatically slide out when a product added to cart.', 'ziultimate'),
				'default' 	=> false,
				'layout' 	=> 'inline',
				'dependency' 	=> [
					[
						'option' 	=> 'used_in_canvas',
						'value' 	=> [ 'yes' ]
					]
				]
			]
		);

		$options->add_option(
			'delay',
			[
				'type' 		=> 'slider',
				'content' 	=> 'ms',
				'min' 		=> 0,
				'max' 		=> 100000,
				'step' 		=> 50,
				'default' 	=> 900,
				'title' 	=> esc_html__( 'Delay', 'ziultimate' ),
				'dependency' 	=> [
					[
						'option' 	=> 'used_in_canvas',
						'value' 	=> [ 'yes' ]
					]
				]
			]
		);

		$options->add_option(
			'duration',
			[
				'type' 		=> 'slider',
				'content' 	=> 'ms',
				'min' 		=> 0,
				'max' 		=> 100000,
				'step' 		=> 50,
				'default' 	=> 3500,
				'title' 	=> esc_html__( 'Duration', 'ziultimate' ),
				'dependency' 	=> [
					[
						'option' 	=> 'used_in_canvas',
						'value' 	=> [ 'yes' ]
					]
				]
			]
		);


		/**
		 * Quantity field group
		 */
		$qty = $options->add_group(
			'qty_group',
			[
				'type' 		=> 'accordion_menu',
				'title' 	=> esc_html__('Quantity Field', 'ziultimate')
			]
		);

		$qty->add_option(
			'enabled_qty',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> esc_html__( 'Show quantity field with -/+ button?', 'ziultimate' ),
				'default' 	=> 'no',
				'options' 	=> [
					[
						'name' 	=> esc_html__( 'No' ),
						'id' 	=> 'no'
					],
					[
						'name' 	=> esc_html__( 'Yes' ),
						'id' 	=> 'show'
					]
				],
				'render_attribute' => [
					[
						'tag_id' 	=> 'wrapper',
						'attribute' => 'class',
						'value' 	=> 'zu-mini-cart-{{VALUE}}-qty'
					]
				]
			]
		);

		$qty->add_option(
			'wrap_width',
			[
				'type' 		=> 'number_unit',
				'title' 	=> esc_html__('Width'),
				'units' 	=> [
					'px',
					'pt',
					'rem',
					'vh',
					'vw',
					'%',
				],
				'default' 	=> '80px',
				'width' 	=> 50,
				'responsive_options' => true,
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}}',
						'value' 	=> '--qty-wrap-width: {{VALUE}}'
					]
				],
				'dependency' 	=> [
					[
						'option' 	=> 'enabled_qty',
						'value' 	=> [ 'show' ]
					]
				]
			]
		);

		$qty->add_option(
			'wrap_height',
			[
				'type' 		=> 'number_unit',
				'title' 	=> esc_html__('Height'),
				'units' 	=> StyleOptions::get_units(),
				'default' 	=> '24px',
				'width' 	=> 50,
				'responsive_options' => true,
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}}',
						'value' 	=> '--qty-wrap-height: {{VALUE}}'
					]
				],
				'dependency' 	=> [
					[
						'option' 	=> 'enabled_qty',
						'value' 	=> [ 'show' ]
					]
				]
			]
		);

		$qty->add_option(
			'has_gap_pm',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> esc_html__('Add space between buttons & quantity field?'),
				'default' 	=> 'no',
				'options'	=> [
					[
						'name' 	=> esc_html__('Yes', 'ziultimate'),
						'id' 	=> 'yes'
					],
					[
						'name' 	=> esc_html__('No', 'ziultimate'),
						'id' 	=> 'no'
					]
				],
				'render_attribute' => [
					[
						'tag_id' 	=> 'wrapper',
						'attribute' => 'class',
						'value' 	=> 'has-gap-{{VALUE}}'
					]
				],
				'dependency' 	=> [
					[
						'option' 	=> 'enabled_qty',
						'value' 	=> [ 'show' ]
					]
				]
			]
		);

		$qty->add_option(
			'pm_gap',
			[
				'type' 		=> 'slider',
				'content' 	=> 'px',
				'title' 	=> esc_html__('Space between buttons & quantity field'),
				'min'		=> 0,
				'max' 		=> 100,
				'default' 	=> 3,
				'step' 		=> 1,
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .zu-mini-cart-qty-box input[type=number]',
						'value' 	=> 'margin-right: {{VALUE}}px'
					],
					[
						'selector' 	=> '{{ELEMENT}} .zu-mini-cart-qty-box input[type=number]',
						'value' 	=> 'margin-left: {{VALUE}}px'
					]
				],
				'dependency' 	=> [
					[
						'option' 	=> 'has_gap_pm',
						'value' 	=> [ 'yes' ]
					]
				]
			]
		);

		$qty->add_option(
			'pm_btns_borders',
			[
				'type' 	=> 'html',
				'title' => esc_html__('Borders'),
				'content' => '<hr style="border-color: #e8e8e8;border-style:solid;" />',
				'dependency' 	=> [
					[
						'option' 	=> 'enabled_qty',
						'value' 	=> [ 'show' ]
					]
				]
			]
		);

		$qty->add_option(
			'pm_brd_color',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__('Color', 'ziultimate'),
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .zu-mini-cart-qty-chng',
						'value' 	=> 'border-color: {{VALUE}}'
					],
					[
						'selector' 	=> '{{ELEMENT}} .zu-mini-cart-qty-box input[type=number]',
						'value' 	=> 'border-color: {{VALUE}}'
					]
				],
				'dependency' 	=> [
					[
						'option' 	=> 'enabled_qty',
						'value' 	=> [ 'show' ]
					]
				]
			]
		);

		$qty->add_option(
			'pm_brd_w',
			[
				'type' 		=> 'number_unit',
				'title' 	=> esc_html__('Width', 'ziultimate'),
				'units' 	=> BaseSchema::get_units(),
				'min' 		=> 0,
				'width' 	=> 50,
				'default' 	=> '1px',
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .zu-mini-cart-qty-chng',
						'value' 	=> 'border-width: {{VALUE}}'
					],
					[
						'selector' 	=> '{{ELEMENT}} .zu-mini-cart-qty-box input[type=number]',
						'value' 	=> 'border-width: {{VALUE}}'
					]
				],
				'dependency' 	=> [
					[
						'option' 	=> 'enabled_qty',
						'value' 	=> [ 'show' ]
					]
				]
			]
		);

		$qty->add_option(
			'pm_brd_style',
			[
				'type' 		=> 'select',
				'title' 	=> esc_html__('Style', 'ziultimate'),
				'width' 	=> 50,
				'default' 	=> 'solid',
				'options'	=> [
					[
						'name' 	=> esc_html__('None', 'ziultimate'),
						'id' 	=> 'none'
					],
					[
						'name' 	=> esc_html__('Solid', 'ziultimate'),
						'id' 	=> 'solid'
					],
					[
						'name' 	=> esc_html__('Dotted', 'ziultimate'),
						'id' 	=> 'dotted'
					],
					[
						'name' 	=> esc_html__('Dashed', 'ziultimate'),
						'id' 	=> 'dashed'
					],
					[
						'name' 	=> esc_html__('Double', 'ziultimate'),
						'id' 	=> 'double'
					]
				],
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .zu-mini-cart-qty-chng',
						'value' 	=> 'border-style: {{VALUE}}'
					],
					[
						'selector' 	=> '{{ELEMENT}} .zu-mini-cart-qty-box input[type=number]',
						'value' 	=> 'border-style: {{VALUE}}'
					]
				],
				'dependency' 	=> [
					[
						'option' 	=> 'enabled_qty',
						'value' 	=> [ 'show' ]
					]
				]
			]
		);

		$plusminus = $qty->add_group(
			'pm_group',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> esc_html__('Plus Minus Buttons', 'ziultimate'),
				'collapsed' => true,
				'dependency' 	=> [
					[
						'option' 	=> 'enabled_qty',
						'value' 	=> [ 'show' ]
					]
				]
			]
		);

		$plusminus->add_option(
			'pm_width',
			[
				'type' 		=> 'number_unit',
				'title' 	=> esc_html__('Width'),
				'units' 	=> [
					'px',
					'pt',
					'rem',
					'vh',
					'vw',
					'%',
				],
				'default' 	=> '24px',
				'responsive_options' => true,
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}}',
						'value' 	=> '--qty-button-width: {{VALUE}}'
					]
				]
			]
		);

		$plusminus->add_option(
			'pm_bg_color',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__('Background Color'),
				'width' 	=> 65,
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .zu-mini-cart-qty-box .zu-mini-cart-qty-chng',
						'value' 	=> 'background-color: {{VALUE}}'
					]
				]
			]
		);

		$plusminus->add_option(
			'pm_color',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__('Color'),
				'width' 	=> 35,
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .zu-mini-cart-qty-box .zu-mini-cart-qty-chng',
						'value' 	=> 'color: {{VALUE}}'
					]
				]
			]
		);

		$plusminus->add_option(
			'pm_hbg_color',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__('Hover Background Color', 'ziultimate'),
				'width' 	=> 65,
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .zu-mini-cart-qty-minus:hover',
						'value' 	=> 'background-color: {{VALUE}}'
					],
					[
						'selector' 	=> '{{ELEMENT}} .zu-mini-cart-qty-plus:hover',
						'value' 	=> 'background-color: {{VALUE}}'
					]
				]
			]
		);

		$plusminus->add_option(
			'pm_hcolor',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__('Hover Color', 'ziultimate'),
				'width' 	=> 35,
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .zu-mini-cart-qty-minus:hover',
						'value' 	=> 'color: {{VALUE}}'
					],
					[
						'selector' 	=> '{{ELEMENT}} .zu-mini-cart-qty-plus:hover',
						'value' 	=> 'color: {{VALUE}}'
					]
				]
			]
		);

		$plusminus->add_option(
			'pm_size',
			[
				'type' 		=> 'slider',
				'content' 	=> 'px',
				'title' 	=> esc_html__('Size'),
				'min'		=> 10,
				'max' 		=> 100,
				'step' 		=> 1,
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .zu-mini-cart-qty-chng',
						'value' 	=> 'font-size: {{VALUE}}px'
					]
				]
			]
		);

		$plusminus->add_option(
			'pm_brd_radius',
			[
				'type' 		=> 'slider',
				'content' 	=> 'px',
				'title' 	=> esc_html__('Border Radius'),
				'min'		=> 0,
				'max' 		=> 100,
				'step' 		=> 1,
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .zu-mini-cart-qty-chng',
						'value' 	=> 'border-radius: {{VALUE}}px'
					]
				]
			]
		);

		$inp = $qty->add_group(
			'qty_inp_group',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> esc_html__('Qty Input Field', 'ziultimate'),
				'collapsed' => true,
				'dependency' 	=> [
					[
						'option' 	=> 'enabled_qty',
						'value' 	=> [ 'show' ]
					]
				]
			]
		);

		$inp->add_option(
			'inp_bg_color',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__('Background Color'),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .zu-mini-cart-qty-box input[type=number]',
						'value' 	=> 'background-color: {{VALUE}}'
					]
				]
			]
		);

		$inp->add_option(
			'inp_color',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__('Color'),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .zu-mini-cart-qty-box input[type=number]',
						'value' 	=> 'color: {{VALUE}}'
					]
				]
			]
		);

		$inp->add_option(
			'inp_size',
			[
				'type' 		=> 'number_unit',
				'units' 	=> BaseSchema::get_units(),
				'title' 	=> esc_html__('Font Size'),
				'min'		=> 9,
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .zu-mini-cart-qty-box input[type=number]',
						'value' 	=> 'font-size: {{VALUE}}'
					]
				]
			]
		);


		/**
		 * Notices group
		 */
		$notices = $options->add_group(
			'notices_group',
			[
				'type' 		=> 'accordion_menu',
				'title' 	=> esc_html__('Alert Message', 'ziultimate')
			]
		);

		$notices->add_option(
			'enabled_notices',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> esc_html__( 'Show alert message?', 'ziultimate' ),
				'default' 	=> 'no',
				'options' 	=> [
					[
						'name' 	=> esc_html__( 'No' ),
						'id' 	=> 'no'
					],
					[
						'name' 	=> esc_html__( 'Yes' ),
						'id' 	=> 'show'
					]
				],
				'render_attribute' => [
					[
						'tag_id' 	=> 'wrapper',
						'attribute' => 'class',
						'value' 	=> 'zu-mini-cart-{{VALUE}}-alert'
					]
				]
			]
		);

		$notices->add_option(
			'notice_add',
			[
				'type' 		=> 'text',
				'title' 	=> esc_html__('Add Notice', 'ziultimate'),
				'default' 	=> esc_html__('Item added successfully', 'ziultimate'),
				'dependency' 	=> [
					[
						'option' 	=> 'enabled_notices',
						'value' 	=> [ 'show' ]
					]
				]
			]
		);

		$notices->add_option(
			'notice_update',
			[
				'type' 		=> 'text',
				'title' 	=> esc_html__('Update Notice', 'ziultimate'),
				'default' 	=> esc_html__('Item updated successfully', 'ziultimate'),
				'dependency' 	=> [
					[
						'option' 	=> 'enabled_notices',
						'value' 	=> [ 'show' ]
					]
				]
			]
		);

		$notices->add_option(
			'notice_remove',
			[
				'type' 		=> 'text',
				'title' 	=> esc_html__('Delete Notice', 'ziultimate'),
				'default' 	=> esc_html__('Item removed successfully', 'ziultimate'),
				'dependency' 	=> [
					[
						'option' 	=> 'enabled_notices',
						'value' 	=> [ 'show' ]
					]
				]
			]
		);

		$notices->add_option(
			'min_qty_msg',
			[
				'type' 		=> 'text',
				'title' 	=> esc_html__('Wrong Quantity Entering', 'ziultimate'),
				'default' 	=> esc_html__('You entered wrong value', 'ziultimate'),
				'dependency' 	=> [
					[
						'option' 	=> 'enabled_notices',
						'value' 	=> [ 'show' ]
					]
				]
			]
		);

		$notices->add_option(
			'max_qty_msg',
			[
				'type' 		=> 'text',
				'title' 	=> esc_html__('Meet Max Quantity Limit', 'ziultimate'),
				'default' 	=> esc_html__('No more products on stock', 'ziultimate'),
				'dependency' 	=> [
					[
						'option' 	=> 'enabled_notices',
						'value' 	=> [ 'show' ]
					]
				]
			]
		);

		$notices->add_option(
			'alert_duration',
			[
				'type' 		=> 'slider',
				'content' 	=> 's',
				'min' 		=> 0,
				'max' 		=> 10,
				'step' 		=> 0.1,
				'default' 	=> 0.5,
				'title' 	=> esc_html__( 'Transition Duration', 'ziultimate' ),
				'dependency' 	=> [
					[
						'option' 	=> 'enabled_notices',
						'value' 	=> [ 'show' ]
					]
				],
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .zu-mini-cart-notice',
						'value' 	=> 'transition-duration: {{VALUE}}s'
					]
				]
			]
		);

		$notices->add_option(
			'notice_timeout',
			[
				'type' 		=> 'slider',
				'content' 	=> 'ms',
				'min' 		=> 0,
				'max' 		=> 100000,
				'step' 		=> 50,
				'default' 	=> 2500,
				'title' 	=> esc_html__( 'Timeout after', 'ziultimate' ),
				'dependency' 	=> [
					[
						'option' 	=> 'enabled_notices',
						'value' 	=> [ 'show' ]
					]
				]
			]
		);
	}

	/**
	 * Loading the styles
	 * 
	 * @return void
	 */
	public function enqueue_styles() {
		$this->enqueue_element_style( Utils::get_file_url( 'dist/css/elements/MiniCart/frontend.css' ) );
	}

	/**
	 * Loading the scripts
	 * 
	 * @return void
	 */
	public function enqueue_scripts() {
		$this->enqueue_element_script( Utils::get_file_url( 'dist/js/elements/MiniCart/frontend.js' ) );
	}

	/**
	 * Get style elements
	 *
	 * Returns a list of elements/tags that for which you
	 * want to show style options
	 *
	 * @return void
	 */
	public function on_register_styles() {
		$this->register_style_options_element(
			'inner_content_styles',
			[
				'title'      => esc_html__( 'Inner Content', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .widget_shopping_cart_content',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'cart_items_styles',
			[
				'title'      => esc_html__( 'Product Rows', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .product_list_widget li.mini_cart_item',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'image_styles',
			[
				'title'      => esc_html__( 'Image Styles', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .woocommerce-mini-cart a img',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'title_styles',
			[
				'title'      => esc_html__( 'Title Styles', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .woocommerce-mini-cart .product-title',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'qty_styles',
			[
				'title'      => esc_html__( 'Quantity Text Styles', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .woocommerce-mini-cart .quantity',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'price_styles',
			[
				'title'      => esc_html__( 'Price Styles', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .woocommerce-mini-cart .quantity .woocommerce-Price-amount',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'alt_price_styles',
			[
				'title'      => esc_html__( '2nd Price Styles', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .zu-mini-cart-quantity .woocommerce-Price-amount',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'remove_styles',
			[
				'title'      => esc_html__( 'Remove Button Styles', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .woocommerce-mini-cart .remove',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'subtotal_wrap_styles',
			[
				'title'      => esc_html__( 'Subtotal Wrapper', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .woocommerce-mini-cart__total',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'subtotal_styles',
			[
				'title'      => esc_html__( 'Subtotal Text', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .woocommerce-mini-cart__total strong',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'subtotal_price_styles',
			[
				'title'      => esc_html__( 'Subtotal Price', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .woocommerce-mini-cart__total .woocommerce-Price-amount',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'buttons_wrapper_styles',
			[
				'title'      => esc_html__( 'Buttons Outer Wrapper', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .woocommerce-mini-cart__buttons',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'button_styles',
			[
				'title'      => esc_html__( 'Buttons', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .woocommerce-mini-cart__buttons a',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'checkout_button_styles',
			[
				'title'      => esc_html__( 'Checkout Button', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .woocommerce-mini-cart__buttons a.checkout',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'alert_box_styles',
			[
				'title'      => esc_html__( 'Alert Box', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .zu-mini-cart-notice',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);		
	}

	protected function can_render() {
		if( ! License::has_valid_license() )
			return;

		return true;
	}

	public function before_render( $options ) {
		$isUsedInCanvas = $options->get_value( 'used_in_canvas', 'no' );
		$is_slide_out 	= $options->get_value( 'slide_panel', false );
		$data = [ 
			'checkoutpage' 	=> is_checkout() ? 'yes' : 'no', 
			'using_canvas' 	=> $isUsedInCanvas, 
			'slide_out' 	=> ! empty( $is_slide_out ) ? true : false, 
		];

		if( $isUsedInCanvas == 'yes' && ! empty( $is_slide_out ) ) {
			$data = array_merge( $data, [
				'delay' 	=> $options->get_value( 'delay', 700 ),
				'duration' 	=> $options->get_value( 'duration', 3500 )
			]);
		}

		$show_alert = $options->get_value( 'enabled_notices', 'no' );
		if( $show_alert == 'show' ) {
			$data = array_merge( $data, [
				'notice_add' 		=> $options->get_value( 'notice_add' ),
				'notice_update' 	=> $options->get_value( 'notice_update' ),
				'notice_remove' 	=> $options->get_value( 'notice_remove' ),
				'notice_timeout' 	=> $options->get_value( 'notice_timeout' ),
				'min_qty_msg' 		=> $options->get_value( 'min_qty_msg' ),
				'max_qty_msg' 		=> $options->get_value( 'max_qty_msg' ),
			]);
		}

		$this->render_attributes->add( 'wrapper','data-mc-config', wp_json_encode( $data ) );

		if( function_exists( 'flatsome_setup' ) ) {
			$this->render_attributes->add( 'wrapper','class', 'zu-flatsome-theme' );
		}
	}

	public function render( $options ) {
		$show_alert = $options->get_value( 'enabled_notices', 'no' );
		if( $show_alert == 'show' ) {
			echo '<div class="zu-mini-cart-notice">
					<span class="wc-notice-text"></span>
				</div>';
		}

		echo '<div class="widget_shopping_cart_content">'; 
		woocommerce_mini_cart();
		echo '</div>';
	}

	public function server_render( $request ) {

		if ( function_exists( 'WC' ) ) {
			\WC()->frontend_includes();
			\WC_Template_Loader::init();
			\wc_load_cart();
		}

		parent::server_render( $request );
	}
}