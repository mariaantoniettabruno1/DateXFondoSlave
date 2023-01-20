<?php
namespace ZiUltimate\WooElements\CartCounter;

use ZiUltimate\WooHelpers;
use ZiUltimate\UltimateElements;
use ZiUltimate\Utils;
use ZiUltimate\Admin\License;
use ZionBuilder\Options\BaseSchema;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class CartCounter
 *
 * @package ZiUltimate\WooElements
 */
class CartCounter extends UltimateElements {

    public function get_type() {
		return 'zu_cart_counter';
	}

	public function get_name() {
		return __( 'Cart Counter', 'ziultimate' );
	}

	public function get_keywords() {
		return [ 'cart', 'counter', 'count', 'cart link', 'cart total' ];
	}

	/*public function get_label() {
		return [
			'text'  => $this->get_label_text(),
			'color' => $this->get_label_color(),
		];
	}*/

	public function get_element_icon() {
		return 'element-woo-add-to-cart';
	}

	public function get_category() {
		return $this->zuwoo_elements_category();
	}

	public function is_wrapper() {
		return true;
	}

	public function options( $options ) {
		if( ! License::has_valid_license() ) {
			$title = $this->get_name();
			$description = 'Creates the cart counter button for WooCommerce site.';
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
			'visibility',
			[
				'type' 		=> 'custom_selector',
				'default' 	=> 'show',
				'title' 	=> esc_html__('Hide button when cart is empty?', 'ziultimate' ),
				'description' => esc_html__('It only works at frontend. Editor always show the button.', 'ziultimate'),
				'options' 	=> [
					[
						'name' 		=> esc_html__( 'No' ),
						'id' 		=> 'show'
					],
					[
						'name' 		=> esc_html__( 'Yes' ),
						'id' 		=> 'hide'
					]
				]
			]
		);

		$options->add_option(
			'cta',
			[
				'type' 		=> 'select',
				'default' 	=> 'none',
				'title' 	=> esc_html__('Call To Action', 'ziultimate' ),
				'options' 	=> [
					[
						'name' 		=> esc_html__( 'None', 'ziultimate' ),
						'id' 		=> 'none'
					],
					[
						'name' 		=> esc_html__( 'Show Dropdown(on hover/click)', 'ziultimate' ),
						'id' 		=> 'popup'
					],
					[
						'name' 		=> esc_html__( 'Link to URL', 'ziultimate' ),
						'id' 		=> 'link'
					]
				]
			]
		);

		$options->add_option(
			'page_link',
			[
				'type' 			=> 'link',
				'description' 	=> esc_html__( 'Set the url', 'ziultimate' ),
				'title' 		=> esc_html__( 'Link', 'ziultimate' ),
				'dependency' 	=> [
					[
						'option' 	=> 'cta',
						'value' 	=> [ 'link' ]
					]
				]
			]
		);

		$options->add_option(
			'trigger_event',
			[
				'type' 		=> 'select',
				'default' 	=> 'hover',
				'title' 	=> esc_html__('Show Dropdown On', 'ziultimate' ),
				'options' 	=> [
					[
						'name' 		=> esc_html__( 'Hover', 'ziultimate' ),
						'id' 		=> 'hover'
					],
					[
						'name' 		=> esc_html__( 'Click', 'ziultimate' ),
						'id' 		=> 'click'
					]
				],
				'dependency' 	=> [
					[
						'option' 	=> 'cta',
						'value' 	=> [ 'popup' ]
					]
				]
			]
		);

		$options->add_option(
			'aria_label',
			[
				'type' 		=> 'text',
				'default' 	=> 'Cart',
				'title' 	=> esc_html__('Aria Label', 'ziultimate' ),
				'dynamic' 	=> [
					'enabled' 	=> true
				],
				'dependency' 	=> [
					[
						'option' 	=> 'cta',
						'value' 	=> [ 'link' ],
						'type' 		=> 'not_in'
					]
				]
			]
		);

		$options->add_option(
			'tab_index',
			[
				'type' 			=> 'text',
				'default' 		=> 0,
				'title' 		=> esc_html__( 'Tab Index', 'ziultimate' ),
				'description' 	=> esc_html__('Enter integer value. Default is 0', 'ziultimate'),
				'dynamic' 	=> [
					'enabled' 	=> true
				],
			]
		);

		$options->add_option(
			'el_wcc',
			[
				'type' 		=> 'text',
				'default' 	=> 'zu' . self::elVal(),
				'css_class' => 'znpb-checkbox-switch-wrapper__checkbox'
			]
		);

		/**
		 * Button Content
		 */
		$btn = $options->add_group(
			'button_content',
			[
				'type' 		=> 'accordion_menu',
				'title' 	=> esc_html__( 'Button Text', 'ziultimate' )
			]
		);

		$btn->add_option(
			'content_source',
			[
				'type' 		=> 'custom_selector',
				'default' 	=> 'icon',
				'title' 	=> esc_html__('Type', 'ziultimate'),
				'options' 	=> [
					[
						'name' 		=> esc_html__( 'Text' ),
						'id' 		=> 'text'
					],
					[
						'name' 		=> esc_html__( 'Icon' ),
						'id' 		=> 'icon'
					],
					[
						'name' 		=> esc_html__( 'Icon + Text' ),
						'id' 		=> 'icontext'
					]
				]
			]
		);

		$btn->add_option(
			'text_config',
			[
				'type' 		=> 'html',
				'title' 	=> esc_html__('Text Settings', 'ziultimate'),
				'content' 	=> '<hr style="border-color: #e8e8e8;border-style:solid;"/>',
				'dependency' 	=> [
					[
						'option' 	=> 'content_source',
						'value' 	=> [ 'icon' ],
						'type' 		=> 'not_in'
					]
				]
			]
		);

		$btn->add_option(
			'button_text',
			[
				'type' 		=> 'text',
				'default' 	=> 'Cart',
				'title' 	=> esc_html__('Button Text', 'ziultimate' ),
				'dependency' 	=> [
					[
						'option' 	=> 'content_source',
						'value' 	=> [ 'icon' ],
						'type' 		=> 'not_in'
					]
				],
				'dynamic' 	=> [
					'enabled' => true
				]
			]
		);

		$btn->add_option(
			'text_color',
			[
				'type' 			=> 'colorpicker',
				'title' 		=> esc_html__( 'Color', 'ziultimate' ),
				'width' 		=> 50,
				'sync' 			=> '_styles.text_styles.styles.%%RESPONSIVE_DEVICE%%.default.color',
				'dependency' 	=> [
					[
						'option' 	=> 'content_source',
						'value' 	=> [ 'icon' ],
						'type' 		=> 'not_in'
					]
				]
			]
		);

		$btn->add_option(
			'text_hcolor',
			[
				'type' 			=> 'colorpicker',
				'title' 		=> esc_html__( 'Hover Color', 'ziultimate' ),
				'width' 		=> 50,
				'css_style' 	=> [
					[
						'selector'	=> '{{ELEMENT}} .zu-cart-counter-btn:hover .zu-cart-counter-btn-text',
						'value' 	=> 'color: {{VALUE}}'
					]
				],
				'dependency' 	=> [
					[
						'option' 	=> 'content_source',
						'value' 	=> [ 'icon' ],
						'type' 		=> 'not_in'
					]
				]
			]
		);

		$btn->add_option(
			'text_size',
			[
				'type' 			=> 'number_unit',
				'units' 		=> BaseSchema::get_units(),
				'min' 			=> 0,
				'title' 		=> esc_html__( 'Font Size', 'ziultimate' ),
				'sync' 			=> '_styles.text_styles.styles.%%RESPONSIVE_DEVICE%%.default.font-size',
				'dependency' 	=> [
					[
						'option' 	=> 'content_source',
						'value' 	=> [ 'icon' ],
						'type' 		=> 'not_in'
					]
				]
			]
		);

		$btn->add_option(
			'icon_config',
			[
				'type' 		=> 'html',
				'title' 	=> esc_html__('Icon Settings', 'ziultimate'),
				'content' 	=> '<hr style="border-color: #e8e8e8;border-style:solid;"/>',
				'dependency' 	=> [
					[
						'option' 	=> 'content_source',
						'value' 	=> [ 'text' ],
						'type' 		=> 'not_in'
					]
				]
			]
		);

		$btn->add_option(
			'cart_icon',
			[
				'type'       => 'icon_library',
				'id'         => 'cart_icon',
				'default'    => [
					'family'  => 'Font Awesome 5 Free Solid',
					'name'    => 'shopping-cart',
					'unicode' => 'uf07a',
				],
				'dependency' 	=> [
					[
						'option' 	=> 'content_source',
						'value' 	=> [ 'text' ],
						'type' 		=> 'not_in'
					]
				]
			]
		);

		$btn->add_option(
			'icon_position',
			[
				'type' 		=> 'custom_selector',
				'default' 	=> 'left',
				'title' 	=> esc_html__('Position', 'ziultimate' ),
				'options' 	=> [
					[
						'name' 		=> esc_html__( 'Left' ),
						'id' 		=> 'left'
					],
					[
						'name' 		=> esc_html__( 'Right' ),
						'id' 		=> 'right'
					]
				],
				'dependency' 	=> [
					[
						'option' 	=> 'content_source',
						'value' 	=> [ 'icontext' ]
					]
				]
			]
		);

		$btn->add_option(
			'icon_color',
			[
				'type' 			=> 'colorpicker',
				'title' 		=> esc_html__( 'Color', 'ziultimate' ),
				'width' 		=> 50,
				'sync' 			=> '_styles.cart_icon.styles.%%RESPONSIVE_DEVICE%%.default.color',
				'dependency' 	=> [
					[
						'option' 	=> 'content_source',
						'value' 	=> [ 'text' ],
						'type' 		=> 'not_in'
					]
				]
			]
		);

		$btn->add_option(
			'icon_hcolor',
			[
				'type' 			=> 'colorpicker',
				'title' 		=> esc_html__( 'Hover Color', 'ziultimate' ),
				'width' 		=> 50,
				'css_style' 	=> [
					[
						'selector'	=> '{{ELEMENT}} .zu-cart-counter-btn:hover .zu-cart-counter-btn-icon',
						'value' 	=> 'color: {{VALUE}}'
					]
				],
				'dependency' 	=> [
					[
						'option' 	=> 'content_source',
						'value' 	=> [ 'text' ],
						'type' 		=> 'not_in'
					]
				]
			]
		);

		$btn->add_option(
			'icon_size',
			[
				'type' 			=> 'number_unit',
				'units' 		=> BaseSchema::get_units(),
				'min' 			=> 0,
				'title' 		=> esc_html__( 'Size', 'ziultimate' ),
				'sync' 			=> '_styles.cart_icon.styles.%%RESPONSIVE_DEVICE%%.default.font-size',
				'dependency' 	=> [
					[
						'option' 	=> 'content_source',
						'value' 	=> [ 'text' ],
						'type' 		=> 'not_in'
					]
				]
			]
		);	

		/**
		 * Cart Counter
		 */
		$counter = $options->add_group(
			'cart_counter',
			[
				'type' 		=> 'accordion_menu',
				'title' 	=> esc_html__( 'Cart Counter', 'ziultimate' )
			]
		);

		$counter->add_option(
			'show_counter',
			[
				'type' 		=> 'custom_selector',
				'default' 	=> 'yes',
				'title' 	=> esc_html__('Display Cart Counter?', 'ziultimate' ),
				'options' 	=> [
					[
						'name' 		=> esc_html__( 'No' ),
						'id' 		=> 'no'
					],
					[
						'name' 		=> esc_html__( 'Yes' ),
						'id' 		=> 'yes'
					]
				]
			]
		);

		$counter->add_option(
			'bubble',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> esc_html__( 'Show in bubble?', 'ziultimate' ),
				'default' 	=> true,
				'layout' 	=> 'inline'
			]
		);

		$counter->add_option(
			'counter_position',
			[
				'type' 		=> 'custom_selector',
				'default' 	=> 'left',
				'title' 	=> esc_html__('Position', 'ziultimate' ),
				'options' 	=> [
					[
						'name' 		=> esc_html__( 'Left' ),
						'id' 		=> 'left'
					],
					[
						'name' 		=> esc_html__( 'Right' ),
						'id' 		=> 'right'
					]
				],
				'dependency' 	=> [
					[
						'option' 	=> 'bubble',
						'value' 	=> [ false ]
					]
				]
			]
		);

		$counter->add_option(
			'bubble_pos_top',
			[
				'type' 			=> 'number_unit',
				'units' 		=> [
					'px',
					'pt',
					'rem',
					'vh',
					'%',
					'auto',
				],
				'width' 		=> 50,
				'title' 		=> esc_html__( 'Position Top', 'ziultimate' ),
				'description' 	=> esc_html__( 'Default is 5px', 'ziultimate' ),
				'sync' 			=> '_styles.counter_styles.styles.%%RESPONSIVE_DEVICE%%.default.top',
				'dependency' 	=> [
					[
						'option' 	=> 'bubble',
						'value' 	=> [ true ]
					]
				]
			]
		);

		$counter->add_option(
			'bubble_pos_right',
			[
				'type' 			=> 'number_unit',
				'units' 		=> [
					'px',
					'pt',
					'rem',
					'vh',
					'%',
					'auto',
				],
				'width' 		=> 50,
				'title' 		=> esc_html__( 'Position Right', 'ziultimate' ),
				'description' 	=> esc_html__( 'Default is -4px', 'ziultimate' ),
				'sync' 			=> '_styles.counter_styles.styles.%%RESPONSIVE_DEVICE%%.default.right',
				'dependency' 	=> [
					[
						'option' 	=> 'bubble',
						'value' 	=> [ true ]
					]
				]
			]
		);

		$counter->add_option(
			'bubble_size',
			[
				'type' 			=> 'slider',
				'content' 		=> 'px',
				'min' 			=> 0,
				'max' 			=> 100,
				'step' 			=> 1,
				'title' 		=> esc_html__( 'Circle Size', 'ziultimate' ),
				'default' 		=> 18,
				'css_style' 	=> [
					[
						'selector' 	=> '{{ELEMENT}} .zu-cart-counter-in-bubble .zu-cart-counter',
						'value' 	=> 'width: {{VALUE}}px'
					],
					[
						'selector' 	=> '{{ELEMENT}} .zu-cart-counter-in-bubble .zu-cart-counter',
						'value' 	=> 'height: {{VALUE}}px'
					]
				],
				'dependency' 	=> [
					[
						'option' 	=> 'bubble',
						'value' 	=> [ true ]
					]
				]
			]
		);

		$counter->add_option(
			'counter_num_size',
			[
				'type' 			=> 'number_unit',
				'units' 		=> BaseSchema::get_units(),
				'min' 			=> 9,
				'title' 		=> esc_html__( 'Font Size', 'ziultimate' ),
				'sync' 			=> '_styles.counter_styles.styles.%%RESPONSIVE_DEVICE%%.default.font-size'
			]
		);

		$counter->add_option(
			'counter_bg_color',
			[
				'type' 			=> 'colorpicker',
				'title' 		=> esc_html__( 'Background', 'ziultimate' ),
				'width' 		=> 50,
				'sync' 			=> '_styles.counter_styles.styles.%%RESPONSIVE_DEVICE%%.default.background-color'
			]
		);

		$counter->add_option(
			'counter_bg_hcolor',
			[
				'type' 			=> 'colorpicker',
				'title' 		=> esc_html__( 'Hover Background', 'ziultimate' ),
				'width' 		=> 50,
				'css_style' 	=> [
					[
						'selector' 	=> '{{ELEMENT}} .zu-cart-counter-btn:hover .zu-cart-counter',
						'value' 	=> 'background-color: {{VALUE}}'
					]
				]
			]
		);

		$counter->add_option(
			'counter_color',
			[
				'type' 			=> 'colorpicker',
				'title' 		=> esc_html__( 'Color', 'ziultimate' ),
				'width' 		=> 50,
				'sync' 			=> '_styles.counter_styles.styles.%%RESPONSIVE_DEVICE%%.default.color'
			]
		);

		$counter->add_option(
			'counter_hcolor',
			[
				'type' 			=> 'colorpicker',
				'title' 		=> esc_html__( 'Hover Color', 'ziultimate' ),
				'width' 		=> 50,
				'css_style' 	=> [
					[
						'selector' 	=> '{{ELEMENT}} .zu-cart-counter-btn:hover .zu-cart-counter',
						'value' 	=> 'color: {{VALUE}}'
					]
				]
			]
		);

		/**
		 * Cart Price
		 */
		$price = $options->add_group(
			'cart_price',
			[
				'type' 		=> 'accordion_menu',
				'title' 	=> esc_html__( 'Cart Price', 'ziultimate' )
			]
		);

		$price->add_option(
			'show_totals',
			[
				'type' 		=> 'custom_selector',
				'default' 	=> 'no',
				'title' 	=> esc_html__('Show Price', 'ziultimate' ),
				'options' 	=> [
					[
						'name' 		=> esc_html__( 'No' ),
						'id' 		=> 'no'
					],
					[
						'name' 		=> esc_html__( 'Yes' ),
						'id' 		=> 'yes'
					]
				]
			]
		);

		$price->add_option(
			'price_alignment',
			[
				'type' 		=> 'custom_selector',
				'default' 	=> 'left',
				'title' 	=> esc_html__('Position', 'ziultimate' ),
				'options' 	=> [
					[
						'name' 		=> esc_html__( 'Left' ),
						'id' 		=> 'left'
					],
					[
						'name' 		=> esc_html__( 'Right' ),
						'id' 		=> 'right'
					]
				]
			]
		);

		$price->add_option(
			'gap_right',
			[
				'type' 					=> 'dynamic_slider',
				'default_step' 			=> 1,
				'default_shift_step' 	=> 1,
				'title' 				=> esc_html__( 'Padding Right', 'zionbuilder' ),
				'description' 			=> esc_html__( 'Default value is 6px', 'zionbuilder' ),
				'sync' 					=> '_styles.price_styles.styles.%%RESPONSIVE_DEVICE%%.default.padding-right',
				'default' 				=> [
					'default' => '6px',
				],
				'options' 				=> [
					[
						'min'        => 0,
						'max'        => 100,
						'step'       => 1,
						'shift_step' => 1,
						'unit'       => 'px',
					],
					[
						'min'        => 0,
						'max'        => 100,
						'step'       => 1,
						'shift_step' => 1,
						'unit'       => '%',
					]
				],
				'responsive_options' 	=> true,
				'dependency' 			=> [
					[
						'option' => 'price_alignment',
						'value'    => [ 'left' ],
					],
				],
			]
		);

		$price->add_option(
			'gap_left',
			[
				'type' 					=> 'dynamic_slider',
				'default_step' 			=> 1,
				'default_shift_step' 	=> 1,
				'title' 				=> esc_html__( 'Padding Left', 'zionbuilder' ),
				'description' 			=> esc_html__( 'Default value is 6px', 'zionbuilder' ),
				'sync' 					=> '_styles.price_styles.styles.%%RESPONSIVE_DEVICE%%.default.padding-left',
				'default' 				=> [
					'default' => '6px',
				],
				'options' 				=> [
					[
						'min'        => 0,
						'max'        => 100,
						'step'       => 1,
						'shift_step' => 1,
						'unit'       => 'px',
					],
					[
						'min'        => 0,
						'max'        => 100,
						'step'       => 1,
						'shift_step' => 1,
						'unit'       => '%',
					]
				],
				'responsive_options' 	=> true,
				'dependency' 			=> [
					[
						'option' => 'price_alignment',
						'value'    => [ 'right' ],
					],
				]
			]
		);

		$price->add_option(
			'price_font_size',
			[
				'type' 			=> 'number_unit',
				'units' 		=> BaseSchema::get_units(),
				'min' 			=> 9,
				'title' 		=> esc_html__( 'Font Size', 'ziultimate' ),
				'sync' 			=> '_styles.price_styles.styles.%%RESPONSIVE_DEVICE%%.default.font-size'
			]
		);

		$price->add_option(
			'price_color',
			[
				'type' 			=> 'colorpicker',
				'title' 		=> esc_html__( 'Color' ),
				'width' 		=> 50,
				'sync' 			=> '_styles.price_styles.styles.%%RESPONSIVE_DEVICE%%.default.color'
			]
		);

		$price->add_option(
			'price_hcolor',
			[
				'type' 			=> 'colorpicker',
				'title' 		=> esc_html__( 'Hover Color', 'ziultimate' ),
				'width' 		=> 50,
				'css_style' 	=> [
					[
						'selector' 	=> '{{ELEMENT}} .zu-cart-counter-btn:hover .zu-cart-price',
						'value' 	=> 'color: {{VALUE}}'
					]
				]
			]
		);

		//* Cart Items
		$box = $options->add_group(
			'dropdown_box',
			[
				'type' 	=> 'accordion_menu',
				'title' => esc_html__('Dropdown Box', 'ziultimate' ),
				'dependency' => [
					[
						'option' 	=> 'cta',
						'value' 	=> [ 'popup' ]
					]
				]
			]
		);

		$box->add_option(
			'builder_preview',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> __('Check Preview on Builder?', 'ziultimate'),
				'default' 	=> 'yes',
				'options' 	=> [
					[
						'name' 		=> esc_html__( 'No' ),
						'id' 		=> 'no'
					],
					[
						'name' 		=> esc_html__( 'Yes' ),
						'id' 		=> 'yes'
					]
				]
			]
		);

		$box->add_option(
			'hide_popup',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> __('Hide dropdown when cart is empty', 'ziultimate'),
				'default' 	=> 'no',
				'options' 	=> [
					[
						'name' 		=> esc_html__( 'No' ),
						'id' 		=> 'no'
					],
					[
						'name' 		=> esc_html__( 'Yes' ),
						'id' 		=> 'yes'
					]
				]
			]
		);

		$box->add_option(
			'box_width',
			[
				'type' 					=> 'dynamic_slider',
				'default_step' 			=> 1,
				'default_shift_step' 	=> 5,
				'title' 				=> esc_html__( 'Width', 'zionbuilder' ),
				'description' 			=> esc_html__( 'Default width is 300px. You will adjust it on breakpoints.', 'ziultimate' ),
				'default' 				=> '300px',
				'options' 				=> [
					[
						'min'        => 0,
						'max'        => 900,
						'step'       => 1,
						'shift_step' => 25,
						'unit'       => 'px',
					],
					[
						'min'        => 0,
						'max'        => 100,
						'step'       => 1,
						'shift_step' => 5,
						'unit'       => '%',
					],
					[
						'min'        => 0,
						'max'        => 100,
						'step'       => 1,
						'shift_step' => 5,
						'unit'       => 'vw',
					],
					[
						'unit' => 'auto',
					],
				],
				'responsive_options' 	=> true,
				'css_style' 			=> [
					[
						'selector' => '{{ELEMENT}} .zu-cart-counter-items',
						'value'    => 'width: {{VALUE}}',
					],
				],
			]
		);

		$box->add_option(
			'box_bg_color',
			[
				'type' 			=> 'colorpicker',
				'title' 		=> esc_html__( 'Background' ),
				'sync' 			=> '_styles.dropdown_box_styles.styles.%%RESPONSIVE_DEVICE%%.default.background-color',
			]
		);

		$box_pos = $box->add_group(
			'box_pos',
			[
				'type'  	=> 'panel_accordion',
				'title' 	=> esc_html__( 'Position' ),
				'collapsed' => true
			]
		);

		$box_pos->add_option(
			'note',
			[
				'type' 		=> 'html',
				'title' 	=> esc_html__( 'Note', 'ziultimate' ),
				'content' 	=> '<p>Default position is right(0px). You will adjust the value if box is cutting on small devices.</p>
								<p><strong>1. Left Alignment:</strong> left = 0 & right = auto</p>
								<p><strong>2. Center Alignment:</strong> right = 50% or left = -50% with right = auto</p>'
			]
		);

		$box_pos->add_option(
			'box_pos_left',
			[
				'type' 					=> 'number_unit',
				'units' 				=> [
					'px',
					'pt',
					'rem',
					'vh',
					'%',
					'auto',
				],
				'width' 				=> 50,
				'title' 				=> esc_html__( 'Left' ),
				'show_responsive_buttons' 	=> true,
				'sync' 					=> '_styles.dropdown_box_styles.styles.%%RESPONSIVE_DEVICE%%.default.left'
			]
		);

		$box_pos->add_option(
			'box_pos_right',
			[
				'type' 					=> 'number_unit',
				'units' 				=> [
					'px',
					'pt',
					'rem',
					'vh',
					'%',
					'auto',
				],
				'width' 				=> 50,
				'title' 				=> esc_html__( 'Right' ),
				'sync' 					=> '_styles.dropdown_box_styles.styles.%%RESPONSIVE_DEVICE%%.default.right',
				'show_responsive_buttons' 	=> true,
			]
		);

		$spacing = $box->add_group(
			'dropdown_spacing',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> esc_html__('Spacing', 'ziultimate' ),
				'collapsed' => true
			]
		);

		$spacing->add_group(
			'padding',
			[
				'type'                    => 'dimensions',
				'title'                   => __( 'Padding', 'zionbuilder' ),
				'description'             => __( 'Choose the desired padding for this element.', 'zionbuilder' ),
				'min'                     => 0,
				'max'                     => 99999,
				'sync'                    => '_styles.dropdown_box_styles.styles.%%RESPONSIVE_DEVICE%%.default',
				'show_responsive_buttons' => true,
				'dimensions'              => [
					[
						'name' => 'top',
						'icon' => 'padding-top',
						'id'   => 'padding-top',
					],
					[
						'name' => 'right',
						'icon' => 'padding-right',
						'id'   => 'padding-right',
					],
					[
						'name' => 'bottom',
						'icon' => 'padding-bottom',
						'id'   => 'padding-bottom',
					],
					[
						'name' => 'left',
						'icon' => 'padding-left',
						'id'   => 'padding-left',
					],
				],
			]
		);

		$box_shadow = $box->add_group(
			'dropdown_shadow',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> esc_html__('Box Shadow Options', 'ziultimate' ),
				'collapsed' => true
			]
		);

		$box_shadow->add_option(
			'box-shadow',
			[
				'type'        => 'shadow',
				'title'       => __( 'Box Shadow', 'zionbuilder-pro' ),
				'description' => __( 'Set the desired box shadow.', 'zionbuilder-pro' ),
				'shadow_type' => 'box-shadow',
				'sync'        => '_styles.dropdown_box_styles.styles.%%RESPONSIVE_DEVICE%%.default.box-shadow',
			]
		);

		$fade = $box->add_group(
			'box_fade',
			[
				'type'  	=> 'panel_accordion',
				'title' 	=> esc_html__( 'Fade Animation', 'ziultimate' ),
				'collapsed' => true
			]
		);

		$fade->add_option(
			'box_pos_bottom',
			[
				'type' 					=> 'number_unit',
				'default' 				=> '-30px',
				'units' 				=> [
					'px',
					'pt',
					'rem',
					'vh',
					'%',
					'auto',
				],
				'title' 				=> esc_html__( 'Position before transition', 'ziultimate' ),
				'description' 			=> esc_html__( 'When box is hidden. Bottom position is -30px.', 'ziultimate' ),
				'responsive_options' 	=> true,
			]
		);

		$fade->add_option(
			'box_pos_abotm',
			[
				'type' 					=> 'number_unit',
				'default' 				=> '0px',
				'units' 				=> [
					'px',
					'pt',
					'rem',
					'vh',
					'%',
					'auto',
				],
				'title' 				=> esc_html__( 'Position after transition', 'ziultimate' ),
				'description' 			=> esc_html__( 'When box will show on hover or click. Bottom position is 0px.', 'ziultimate' ),
				'responsive_options' 	=> true,
			]
		);

		$fade->add_option(
			'transition_duration',
			[
				'type' 		=> 'slider',
				'content' 	=> 's',
				'min' 		=> 0,
				'max' 		=> 10,
				'step' 		=> 0.1,
				'default' 	=> 0.3,
				'title' 	=> esc_html__( 'Transition Duration' ),
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .zu-cart-counter-items',
						'value' 	=> 'transition-duration: {{VALUE}}s'
					]
				]
			]
		);

		$reveal = $box->add_group(
			'reveal_group',
			[
				'type'  	=> 'panel_accordion',
				'title' 	=> esc_html__( 'Reveal Dropdown', 'ziultimate' ),
				'collapsed' => true
			]
		);

		$reveal->add_option(
			'reveal',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> __('Enable Reveal Effect?', 'ziultimate'),
				'description' 	=> __('Dropdown popup when a product added to cart.', 'ziultimate'),
				'default' 	=> 'no',
				'options' 	=> [
					[
						'name' 		=> esc_html__( 'No' ),
						'id' 		=> 'no'
					],
					[
						'name' 		=> esc_html__( 'Yes' ),
						'id' 		=> 'yes'
					]
				]
			]
		);

		$reveal->add_option(
			'delay',
			[
				'type' 		=> 'slider',
				'content' 	=> 'ms',
				'min' 		=> 0,
				'max' 		=> 100000,
				'step' 		=> 50,
				'default' 	=> 1200,
				'title' 	=> esc_html__( 'Delay', 'ziultimate' )
			]
		);

		$reveal->add_option(
			'duration',
			[
				'type' 		=> 'slider',
				'content' 	=> 'ms',
				'min' 		=> 0,
				'max' 		=> 100000,
				'step' 		=> 50,
				'default' 	=> 4500,
				'title' 	=> esc_html__( 'Duration', 'ziultimate' )
			]
		);
	}

	/**
	 * Loading the styles
	 * 
	 * @return void
	 */
	public function enqueue_styles() {
		$this->enqueue_editor_style( Utils::get_file_url( 'dist/css/elements/CartCounter/editor.css' ) );
		$this->enqueue_element_style( Utils::get_file_url( 'dist/css/elements/CartCounter/frontend.css' ) );
	}

	/**
	 * Loading the scripts
	 * 
	 * @return void
	 */
	public function enqueue_scripts() {
		$this->enqueue_editor_script( Utils::get_file_url( 'dist/js/elements/CartCounter/editor.js' ) );
		$this->enqueue_element_script( Utils::get_file_url( 'dist/js/elements/CartCounter/frontend.js' ) );
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
			'button_styles',
			[
				'title'      => esc_html__( 'Button styles', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .zu-cart-counter-btn',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);
		$this->register_style_options_element(
			'counter_styles',
			[
				'title'      => esc_html__( 'Counter styles', 'ziultimate' ),
				'selector'   => '{{ELEMENT}} .zu-cart-counter',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);
		$this->register_style_options_element(
			'text_styles',
			[
				'title'      => esc_html__( 'Text styles', 'ziultimate' ),
				'selector'   => '{{ELEMENT}} .zu-cart-counter-btn-text',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);
		$this->register_style_options_element(
			'cart_icon',
			[
				'title'      => esc_html__( 'Icon styles', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .zu-cart-counter-btn-icon',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);
		$this->register_style_options_element(
			'price_styles',
			[
				'title'      => esc_html__( 'Price styles', 'ziultimate' ),
				'selector'   => '{{ELEMENT}} .zu-cart-price',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'dropdown_box_styles',
			[
				'title'      => esc_html__( 'Dropdown Box styles', 'ziultimate' ),
				'selector'   => '{{ELEMENT}} .zu-cart-counter-items',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);
	}

	protected function can_render() 
	{
		if( ! License::has_valid_license() )
			return;

		return true;
	}

	public function before_render( $options ) {
		$counter = is_object( WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
		$hide_button = $options->get_value( 'visibility', 'show' );

		if( $hide_button == 'hide' && absint( $counter ) <= 0 ) {
			if( self::isBuilderEditor() )
				$this->render_attributes->add( 'wrapper', 'class', 'znpb-builder-edit' );
			else
				$this->render_attributes->add( 'wrapper', 'class', 'zu-hide-cart-btn' );
		}
	}

	public function render( $options ) {
		$total_price 	= ! is_null( WC()->cart ) ? WC()->cart->get_cart_total() : wc_price( 0 );
		$visibility 	= $options->get_value( 'visibility', 'show' );
		$cta 			= $options->get_value( 'cta', 'none' );
		$show_price 	= $options->get_value( 'show_totals', 'no' );
		$show_counter 	= $options->get_value( 'show_counter', 'yes' );
		$showinbubble 	= $options->get_value( 'bubble', true );
		$button_label 	= $options->get_value( 'content_source', 'icon' );
		$icon 			= $options->get_value( 'cart_icon', false );
		$aria_label 	= $options->get_value( 'aria_label', 'Cart' );
		$counter_html 	= $price_html = $btn_label = '';
		$class 			= [ 'zu-cart-counter-btn', 'zu-cart-counter-cta-' . $cta ];

		//* icon html
		if( $button_label != 'text' && ! empty( $icon ) ) 
		{
			$this->attach_icon_attributes( 'cart_icon', $icon );
			$this->render_attributes->add( 'cart_icon', 'class', 'zu-cart-counter-btn-icon' );
			$btn_label .= $this->get_render_tag(
				'span', 
				'cart_icon'
			);
		}

		//* button text html
		if( $button_label != 'icon' ) 
		{
			$btn_label .= $this->get_render_tag(
				'span',
				'button_text',
				$options->get_value( 'button_text', __('Cart', 'woocommerce') ),
				[
					'class' => "zu-cart-counter-btn-text"
				]
			);
		}

		if( $button_label == 'icontext' ) 
		{
			$class[] = 'zu-cart-icon--' . $options->get_value( 'icon_position', 'left' );
			$btn_label = sprintf( '<span class="zu-cart-counter-it-wrapper">%s</span>', $btn_label );
		}

		//* price html
		if( $show_price == 'yes' ) 
		{
			$price_html = $this->get_render_tag(
				'span',
				'cart_price',
				$total_price,
				[
					'class' => "zu-cart-price"
				]
			);

			$class[] = 'zu-cart-price--' . $options->get_value( 'price_alignment', 'left' );
			$btn_label = sprintf( '<span class="zucc-btn-container">%s</span>', $price_html . $btn_label );
		}
		
		//* cart counter html
		if( $show_counter == 'yes' ) 
		{
			$counter = is_object( WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
			$counter_html = $this->get_render_tag(
				'span',
				'cart_counter',
				absint( $counter ),
				[
					'class' => "zu-cart-counter"
				]
			);

			if( ! empty( $showinbubble ) && $showinbubble === true ) { 
				$class[] = 'zu-cart-counter-in-bubble'; 
			} else {
				$class[] = 'zu-cart-counter--' . $options->get_value( 'counter_position', 'left' );
			}
		}

		//* generates link attributes
		$tab = $options->get_value( 'tab_index', 0 );
		$dataattr = [
			'checkoutpage' 	=> is_checkout() ? 'yes' : 'no',
			'showbtn' 		=> $visibility,
			'eventlistener' => $options->get_value( 'trigger_event', 'hover' ),
			'hide_popup' 	=> $options->get_value( 'hide_popup', 'no' ),
			'reveal' 		=> $options->get_value( 'reveal', 'no' ),
			'delay' 		=> $options->get_value( 'delay', 1200 ),
			'duration' 		=> $options->get_value( 'duration', 4500 ),
		];

		$attr = [ 
			'class' 			=> implode( ' ', $class ), 
			'role' 				=> 'button', 
			'tabindex' 			=> absint( $tab ), 
			'data-zucc-config' 	=> wp_json_encode( $dataattr )
		];

		if( $cta == 'link' ) 
		{
			$page_link = $options->get_value( 'page_link', false );
			$this->attach_link_attributes( 'button', $page_link );
			$combined_button_attr = $this->render_attributes->get_combined_attributes( 'button_styles', $attr );
		} 
		else 
		{
			$combined_button_attr = $this->render_attributes->get_combined_attributes( 
				'button_styles', 
				array_merge( 
					$attr,
					[
						'href' 	=> "JavaScript: void(0)",
						'aria-label' => esc_html__( $aria_label ),
					]
				)
			);
		}
		
		//* reneder the cart counter button
		$this->render_tag(
			'a',
			'button',
			[ $counter_html, $btn_label ],
			$combined_button_attr
		);

		//* cart items
		if( $cta == 'popup' ) 
		{
			echo '<div class="zu-cart-counter-items">';
			$this->render_children();
			echo '</div>';
		}
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