<?php
namespace ZiUltimate\Elements\AccordionMenu;

use ZiUltimate\UltimateElements;
use ZiUltimate\Admin\License;
use ZiUltimate\Utils;
use ZionBuilder\Options\BaseSchema;
use ZionBuilderPro\MegaMenu;
use ZionBuilder\Icons;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class AccordionMenu
 *
 * @package ZiUltimate\Elements
 */
class AccordionMenu extends UltimateElements {
	
	public function get_type() {
		return 'zu_accordion_menu';
	}

	public function get_name() {
		return __( 'Accordion Menu', 'ziultimate' );
	}

	public function get_keywords() {
		return [ 'accordion menu', 'menu', 'accordion' ];
	}

	/*public function get_label() {
		return [
			'text'  => $this->get_label_text(),
			'color' => $this->get_label_color(),
		];
	}*/

	public function get_category() {
		return $this->zu_elements_category();
	}

	public function get_element_icon() {
		return 'element-accordion';
	}

	/**
	 * Creating the settings fields
	 * 
	 * @return void
	 */
	public function options( $options ) {
		if( ! License::has_valid_license() ) {
			$title = $this->get_name();
			$description = 'With this tool you can build the accordion effect.';
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
			'acrd_source_menu',
			[
				'type' 		=> 'select',
				'title' 	=> __('Source'),
				'options'	=> [
					[
						'id' 	=> 'wpmenu',
						'name' 	=> __('WP Menu', "ziultimate"),
					],
					[
						'id' 	=> 'tax',
						'name' 	=> __('Taxonomy'),
					]
				],
				'default' => 'wpmenu'
			]
		);

		$options->add_option(
			'acrd_menu',
			[
				'type' 		=> 'select',
				'title' 	=> __('Select a menu'),
				'options'	=> $this->getWPMenus(),
				'default' 	=> 'sel',
				'dependency'	=> [
					[
						'option' 	=> 'acrd_source_menu',
						'value' 	=> [ 'wpmenu' ]
					]
				]
			]
		);

		$options->add_option(
			'acrd_tax_name',
			[
				'type' 		=> 'select',
				'title' 	=> __('Select a taxonomy', 'ziultimate'),
				'options'	=> $this->getTaxonomies(),
				'default' 	=> 'category',
				'dependency'	=> [
					[
						'option' 	=> 'acrd_source_menu',
						'value' 	=> [ 'tax' ]
					]
				]
			]
		);

		$options->add_option(
			'include_ids',
			[
				'type' 			=> 'text',
				'title' 		=> __('Include Specific Categories', 'ziultimate'),
				'description'	=> __('Enter the category ID. Apply comma separator for multiple IDs', 'ziultimate'),
				'dependency'	=> [
					[
						'option' 	=> 'acrd_source_menu',
						'value' 	=> [ 'tax' ]
					]
				]
			]
		);

		$options->add_option(
			'exclude_ids',
			[
				'type' 			=> 'text',
				'title' 		=> __('Exclude Specific Categories', 'ziultimate'),
				'description'	=> __('Enter the category ID. Apply comma separator for multiple IDs', 'ziultimate'),
				'dependency'	=> [
					[
						'option' 	=> 'acrd_source_menu',
						'value' 	=> [ 'tax' ]
					]
				]
			]
		);

		$options->add_option(
			'hide_empty',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> __('Hide Empty Category', 'ziultimate'),
				'default' 	=> true,
				'layout' 	=> 'inline',
				'dependency'	=> [
					[
						'option' 	=> 'acrd_source_menu',
						'value' 	=> [ 'tax' ]
					]
				]
			]
		);

		$options->add_option(
			'child_of',
			[
				'type' 			=> 'text',
				'title' 		=> __('Child Of'),
				'width' 		=> 50,
				'dependency'	=> [
					[
						'option' 	=> 'acrd_source_menu',
						'value' 	=> [ 'tax' ]
					]
				]
			]
		);

		$options->add_option(
			'limit',
			[
				'type' 			=> 'text',
				'title' 		=> __('Limit'),
				'width' 		=> 50,
				'dependency'	=> [
					[
						'option' 	=> 'acrd_source_menu',
						'value' 	=> [ 'tax' ]
					]
				]
			]
		);

		$options->add_option(
			'order',
			[
				'type' 			=> 'select',
				'title' 		=> __('Order'),
				'default' 		=> 'ASC',
				'width' 		=> 50,
				'options' 		=> [
					[
						'name' 	=> 'ASC',
						'id' 	=> 'ASC'
					],
					[
						'name' 	=> 'DESC',
						'id' 	=> 'DESC'
					]
				],
				'dependency'	=> [
					[
						'option' 	=> 'acrd_source_menu',
						'value' 	=> [ 'tax' ]
					]
				]
			]
		);

		$options->add_option(
			'orderby',
			[
				'type' 			=> 'select',
				'title' 		=> __('Order by'),
				'default' 		=> 'name',
				'width' 		=> 50,
				'options' 		=> [
					[
						'name' 	=> 'Name',
						'id' 	=> 'name'
					],
					[
						'name' 	=> 'ID',
						'id' 	=> 'id'
					],
					[
						'name' 	=> 'Slug',
						'id' 	=> 'slug'
					],
					[
						'name' 	=> 'Menu Order',
						'id' 	=> 'menu_order'
					],
					[
						'name' 	=> 'Include',
						'id' 	=> 'include'
					],
					[
						'name' 	=> 'Count',
						'id' 	=> 'count'
					]
				],
				'dependency'	=> [
					[
						'option' 	=> 'acrd_source_menu',
						'value' 	=> [ 'tax' ]
					]
				]
			]
		);

		$options->add_option(
			'display_menu_title',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> __('Display Menu Title', 'ziultimate'),
				'default' 	=> false,
				'layout' 	=> 'inline',
				'dependency'	=> [
					[
						'option' 	=> 'acrd_source_menu',
						'value' 	=> [ 'wpmenu' ]
					]
				]
			]
		);

		$options->add_option(
			'menu_title_tag',
			[
				'type' 			=> 'select',
				'title' 		=> __('Tag of Menu Title'),
				'default' 		=> 'h4',
				'options' 		=> [
					[
						'name' 	=> 'H1',
						'id' 	=> 'h1'
					],
					[
						'name' 	=> 'H2',
						'id' 	=> 'h2'
					],
					[
						'name' 	=> 'H3',
						'id' 	=> 'h3'
					],
					[
						'name' 	=> 'H4',
						'id' 	=> 'h4'
					],
					[
						'name' 	=> 'H5',
						'id' 	=> 'h5'
					],
					[
						'name' 	=> 'H6',
						'id' 	=> 'h6'
					],
				],
				'dependency'	=> [
					[
						'option' 	=> 'display_menu_title',
						'value' 	=> [ true ]
					],
					[
						'option' 	=> 'acrd_source_menu',
						'value' 	=> [ 'wpmenu' ]
					]
				]
			]
		);

		$options->add_option(
			'isAccordion',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> __('Enable accordion effect', 'ziultimate'),
				'default' 	=> false,
				'layout' 	=> 'inline',
			]
		);

		$options->add_option(
			'collapseItems',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> __('Always collapsed at default state', 'ziultimate'),
				'default' 	=> false,
				'layout' 	=> 'inline'
			]
		);

		$options->add_option(
			'slide_toggle_td',
			[
				'type' 		=> 'slider',
				'content' 	=> 'ms',
				'default' 	=> 400,
				'min' 		=> 0,
				'max' 		=> 10000,
				'step' 		=> 50,
				'title' 	=> __( 'Transition Duration for Toggle', 'ziultimate' ),
			]
		);

		/**
		 * Group - Menu Items
		 */
		$menuItems = $options->add_group(
			'menu_items',
			[
				'type' 		=> 'accordion_menu',
				'title' 	=> __('Menu Items', 'ziultimate')
			]
		);

		$menuItems->add_option(
			'link_bg_clr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Background Color', 'ziultimate' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .menu-item a',
						'value'    => 'background-color: {{VALUE}}',
					],
				],
			]
		);

		$menuItems->add_option(
			'link_hv_bgclr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Hover Background Color', 'ziultimate' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .menu-item a:hover',
						'value'    => 'background-color: {{VALUE}}',
					],
				],
			]
		);

		$menuItems->add_option(
			'cm_bgclr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Background Color of Active Link', 'ziultimate' ),
				'description' 	=> __( 'It is for current menu item', 'ziultimate' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .current-menu-item > a',
						'value'    => 'background-color: {{VALUE}}'
					],
					[
						'selector' => '{{ELEMENT}} .current-menu-ancestor > a',
						'value'    => 'background-color: {{VALUE}}'
					]
				]
			]
		);

		$menuItems->add_option(
			'link_brdclr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Border Color' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .menu-item a',
						'value'    => 'border-bottom-color: {{VALUE}}',
					],
				],
			]
		);

		$menuItems->add_option(
			'link_brdwd',
			[
				'type' 		=> 'number_unit',
				'default'	=> '1px',
				'min' 		=> 0,
				'units' 	=> BaseSchema::get_units(),
				'title' 	=> __( 'Border Width' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .menu-item a',
						'value'    => 'border-bottom-width: {{VALUE}}',
					],
				],
			]
		);

		$ml_tg = $menuItems->add_option(
			'ml_tg',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> __('Typography'),
			]
		);

		$this->attach_typography_options( 
			$ml_tg, 
			'menu_link', 
			'{{ELEMENT}} .menu-item a .zu-menu-item-text', 
			['text_align', 'letter_spacing', 'line_height', 'text_decoration', 'text_transform']
		);

		$ml_tg->add_option(
			'link_hv_clr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Hover Color', 'zionbuilder' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .menu-item a:hover .zu-menu-item-text',
						'value'    => 'color: {{VALUE}}'
					]
				]
			]
		);

		$ml_tg->add_option(
			'cm_clr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Active Link Color', 'zionbuilder' ),
				'description' 	=> __( 'It is for current menu item', 'ziultimate' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .current-menu-item > a .zu-menu-item-text',
						'value'    => 'color: {{VALUE}}'
					],
					[
						'selector' => '{{ELEMENT}} .current-menu-ancestor > a .zu-menu-item-text',
						'value'    => 'color: {{VALUE}}'
					]
				]
			]
		);

		$this->attach_typography_options( 
			$ml_tg, 
			'menu_link', 
			'{{ELEMENT}} .menu-item a .zu-menu-item-text', 
			['text_align', 'font_family', 'font_weight', 'font_color', 'font_size', 'text_decoration']
		);

		$menu_pad = $menuItems->add_option(
			'menu_link_pad',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> __('Padding'),
			]
		);

		$this->attach_padding_options( $menu_pad, 'ml_pad', '{{ELEMENT}} .zu-menu-item-text' );

		/**
		 * Sub Menu Settings
		 */
		$sm = $options->add_group(
			'acrd_sub_menu',
			[
				'type' 		=> 'accordion_menu',
				'title' 	=> __('Sub Menu', 'ziultimate'),
			]
		);

		$sm->add_option(
			'sm_toggle',
			[
				'type' 		=> 'select',
				'default' 	=> 'none',
				'title' 	=> __( 'Enable Builder Preview', 'ziultimate' ),
				'description' => esc_html__('Enable this option when you will edit the sub menu items.', 'ziultimate'),
				'options' 	=> [ [ 'id' =>'block', 'name' => 'Yes'], [ 'id' => 'none', 'name' => 'No' ] ],
				'css_style' => [
					[
						'selector' => 'body.znpb-editor-preview {{ELEMENT}} .sub-menu',
						'value'    => 'display: {{VALUE}}'
					]
				]
			]
		);

		$sm->add_option(
			'sm_bg',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Background Color' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .sub-menu',
						'value'    => 'background-color: {{VALUE}}'
					]
				]
			]
		);

		$sm->add_option(
			'sm_hv_bg',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Hover Background Color', 'ziultimate' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .sub-menu .menu-item a:hover',
						'value'    => 'background-color: {{VALUE}}',
					],
				],
			]
		);

		$sm->add_option(
			'sm_txt_abg',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Active Background Color', 'ziultimate' ),
				'description' 	=> __( 'It is for current menu item', 'ziultimate' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .sub-menu .current-menu-item > a',
						'value'    => 'background-color: {{VALUE}}',
					],
					[
						'selector' => '{{ELEMENT}} .sub-menu .current-menu-ancestor > a',
						'value'    => 'background-color: {{VALUE}}',
					],
				],
			]
		);

		$sm->add_option(
			'sm_link_brdclr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Border Color' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .sub-menu .menu-item a',
						'value'    => 'border-bottom-color: {{VALUE}}',
					],
				],
			]
		);

		$sm->add_option(
			'sm_link_brdwd',
			[
				'type' 		=> 'number_unit',
				'default'	=> '1px',
				'min' 		=> 0,
				'units' 	=> BaseSchema::get_units(),
				'title' 	=> __( 'Border Width' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .sub-menu .menu-item a',
						'value'    => 'border-bottom-width: {{VALUE}}',
					],
				],
			]
		);

		$sm->add_option(
			'text_indent',
			[
				'type' 		=> 'slider',
				'content' 	=> '',
				'default' 	=> 10,
				'min' 		=> 0,
				'max' 		=> 50,
				'step' 		=> 1,
				'title' 	=> __( 'Link Text Indent', 'ziultimate' ),
			]
		);

		$sm_tg = $sm->add_option(
			'sm_tg',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> __('Typography'),
			]
		);

		$this->attach_typography_options( 
			$sm_tg, 
			'sm_tg', 
			'{{ELEMENT}} .sub-menu .zu-menu-item-text'
		);

		$sm->add_option(
			'sm_txt_hc',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Hover Color', 'ziultimate' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .sub-menu .menu-item a:hover .zu-menu-item-text',
						'value'    => 'color: {{VALUE}}',
					],
				],
			]
		);

		$sm->add_option(
			'sm_txt_ac',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Active Color', 'ziultimate' ),
				'description' 	=> __( 'It is for current menu item', 'ziultimate' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .sub-menu .current-menu-item > a .zu-menu-item-text',
						'value'    => 'color: {{VALUE}}',
					],
					[
						'selector' => '{{ELEMENT}} .sub-menu .current-menu-ancestor > a .zu-menu-item-text',
						'value'    => 'color: {{VALUE}}',
					],
				],
			]
		);

		$sm_pad = $sm->add_option(
			'sm_pad',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> __('Padding'),
			]
		);

		$this->attach_padding_options( $sm_pad, 'sm_pad', '{{ELEMENT}} .sub-menu .zu-menu-item-text' );

		/**
		 * Arrow Icon Settings
		 */
		$arrow = $options->add_group(
			'arrow_config',
			[
				'type' 		=> 'accordion_menu',
				'title' 	=> __('Arrow Icon', 'ziultimate')
			]
		);

		$arrow->add_option(
			'arrow_icon',
			[
				'type'       => 'icon_library',
				'id'         => 'icon',
				'title'      => esc_html__( 'Select Icon', 'zionbuilder' ),
				'default'    => [
					'family'  => 'Font Awesome 5 Free Solid',
					'name'    => 'chevron-down',
					'unicode' => 'uf078',
				]
			]
		);

		$arrow->add_option(
			'cm_item_arrow_bg',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Icon BG Color of Active Menu Item', 'ziultimate' ),
				'description' 	=> __( 'It is for current menu item', 'ziultimate' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .current-menu-item > a .zu-menu-items-arrow',
						'value'    => 'background-color: {{VALUE}}'
					],
					[
						'selector' => '{{ELEMENT}} .current-menu-ancestor > a .zu-menu-items-arrow',
						'value'    => 'background-color: {{VALUE}}'
					]
				]
			]
		);

		$arrow->add_option(
			'cm_item_icon_clr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Icon Color of Active Menu Item', 'ziultimate' ),
				'description' 	=> __( 'It is for current menu item', 'ziultimate' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .current-menu-item > a .zu-menu-items-arrow',
						'value'    => 'color: {{VALUE}}'
					],
					[
						'selector' => '{{ELEMENT}} .current-menu-ancestor > a .zu-menu-items-arrow',
						'value'    => 'color: {{VALUE}}'
					]
				]
			]
		);

		$arrow->add_option(
			'sm_arrow_bg',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Icon BG Color of Active Sub Menu Items', 'ziultimate' ),
				'description' 	=> __( 'It is for current menu item', 'ziultimate' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .sub-menu .current-menu-item > a .zu-menu-items-arrow',
						'value'    => 'background-color: {{VALUE}}'
					],
					[
						'selector' => '{{ELEMENT}} .sub-menu .current-menu-ancestor > a .zu-menu-items-arrow',
						'value'    => 'background-color: {{VALUE}}'
					]
				]
			]
		);

		$arrow->add_option(
			'sm_icon_clr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Icon Color of Active Sub Menu Item', 'ziultimate' ),
				'description' 	=> __( 'It is for current menu item', 'ziultimate' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .sub-menu .current-menu-item > a .zu-menu-items-arrow',
						'value'    => 'color: {{VALUE}}'
					],
					[
						'selector' => '{{ELEMENT}} .sub-menu .current-menu-ancestor > a .zu-menu-items-arrow',
						'value'    => 'color: {{VALUE}}'
					]
				]
			]
		);

		$effect = $arrow->add_group(
			'arrow_animation',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> __('Animation')
			]
		);

		$effect->add_option(
			'icon_rotate',
			[
				'type' 		=> 'slider',
				'content' 	=> 'deg',
				'default' 	=> 0,
				'min' 		=> -180,
				'max' 		=> 180,
				'step' 		=> 5,
				'title' 	=> __( 'Rotate (Initial State)', 'ziultimate' ),
				'css_style' => [
					[
						'selector' 	=> "{{ELEMENT}} .zu-menu-items-arrow::before",
						'value' 	=> 'transform: rotate({{VALUE}}deg)'
					]
				]
			]
		);

		$effect->add_option(
			'icon_anim_type',
			[
				'type' 		=> 'select',
				'title' 	=> __('Animation Type', 'ziultimate'),
				'options' 	=> [
					[
						'id' 	=> 'rotate',
						'name' 	=> __('Rotate')
					],
					[
						'id' 	=> 'flip',
						'name' 	=> __('Vertical Flip')
					]
				],
				'default' 	=> 'rotate'
			]
		);

		$effect->add_option(
			'icon_rotate_active',
			[
				'type' 		=> 'slider',
				'content' 	=> 'deg',
				'default' 	=> 180,
				'min' 		=> -180,
				'max' 		=> 180,
				'step' 		=> 5,
				'title' 	=> __( 'Rotate (Active State)', 'ziultimate' ),
				'description' 	=> __( 'when sub menu is sliding.', 'ziultimate' ),
				'css_style' => [
					[
						'selector' 	=> "{{ELEMENT}} .zu-menu-items-arrow.acrd-menu-open::before",
						'value' 	=> 'transform: rotate({{VALUE}}deg)'
					]
				],
				'dependency' => [
					[
						'option' 	=> 'icon_anim_type',
						'value' 	=> [ 'rotate' ] 
					]
				]
			]
		);

		$effect->add_option(
			'icon_anim_td',
			[
				'type' 		=> 'slider',
				'content' 	=> 's',
				'default' 	=> 0.4,
				'min' 		=> 0,
				'max' 		=> 10,
				'step' 		=> 0.1,
				'title' 	=> __( 'Transition Duration' ),
				'css_style' => [
					[
						'selector' 	=> "{{ELEMENT}} .zu-menu-items-arrow::before",
						'value' 	=> 'transition-duration: {{VALUE}}s'
					]
				]
			]
		);

		/**
		 * Hover Animation Settings
		 */
		$animation = $options->add_group(
			'acrd_hover_animation',
			[
				'type' 		=> 'accordion_menu',
				'title' 	=> __('Hover Animation', 'ziultimate'),
			]
		);

		$animation->add_option(
			'link_td',
			[
				'type' 		=> 'slider',
				'content' 	=> 's',
				'default' 	=> 0.4,
				'min' 		=> 0,
				'max' 		=> 10,
				'step' 		=> 0.1,
				'title' 	=> __( 'Transition Duration' ),
				'css_style' => [
					[
						'selector' 	=> "{{ELEMENT}} .menu-item a",
						'value' 	=> 'transition-duration: {{VALUE}}s'
					],
					[
						'selector' 	=> "{{ELEMENT}} .zu-menu-items-arrow",
						'value' 	=> 'transition-duration: {{VALUE}}s'
					],
					[
						'selector' 	=> "{{ELEMENT}} .zu-menu-item-text",
						'value' 	=> 'transition-duration: {{VALUE}}s'
					]
				]
			]
		);

		$animation->add_option(
			'slide_left',
			[
				'type' 		=> 'slider',
				'content' 	=> 'px',
				'default' 	=> 10,
				'min' 		=> 0,
				'max' 		=> 50,
				'step' 		=> 1,
				'title' 	=> __( 'How much slide from left', 'ziultimate' ),
				'css_style' => [
					[
						'selector' 	=> "{{ELEMENT}}",
						'value' 	=> '--menu-item-translatex: {{VALUE}}px'
					]
				]
			]
		);
	}

	/**
	 * Loading the CSS
	 */
	public function enqueue_styles() {
		$this->enqueue_element_style( Utils::get_file_url('dist/css/elements/AccordionMenu/accordionmenu.css' ) );
	}

	/**
	 * Loading the scripts
	 */
	public function enqueue_scripts() {
		$this->enqueue_editor_script( Utils::get_file_url( 'dist/js/elements/AccordionMenu/editor.js' ) );
		$this->enqueue_element_script( Utils::get_file_url( 'dist/js/elements/AccordionMenu/frontend.js' ) );
	}

	/**
	 * Registering the styles
	 */
	public function on_register_styles() {
		$this->register_style_options_element(
			'menu_title_styles',
			[
				'title'    => esc_html__( 'Menu Title', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .zu-acrd-menu-title',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'menu_items_wrapper_styles',
			[
				'title'    => esc_html__( 'Menu Items Wrapper', 'ziultimate' ),
				'selector' => '{{ELEMENT}} nav',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'arrow_icon_styles',
			[
				'title'    => esc_html__( 'Arrow Icon', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .zu-menu-items-arrow',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'sm_arrow_icon_styles',
			[
				'title'    => esc_html__( 'Arrow Icon of Sub Menu Items', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .sub-menu .zu-menu-items-arrow',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);
	}

	public function before_render( $options ) {
		$anim_type = $options->get_value( 'icon_anim_type' );
		if( $anim_type === 'flip' ) {
			$this->render_attributes->add( 'wrapper', 'class', 'zu-acrd-arrow-flip' );
		}
	}

	/**
	 * Rendering the layout
	 */
	public function render( $options ) {
		$datattr = array();

		$icon = $options->get_value( 'arrow_icon' );

		$this->attach_icon_attributes( 'submenu_indicator', $icon );
		$this->render_attributes->add( 'submenu_indicator', 'class', 'zu-menu-items-arrow' );
		$this->render_attributes->add( 'submenu_indicator', 'aria-expanded', "false" );
		$this->render_attributes->add( 'submenu_indicator', 'aria-pressed', "false" );
		$this->render_attributes->add( 'submenu_indicator', 'aria-hidden', "true");

		$icon_html = $this->get_render_tag( 'span', 'submenu_indicator' );
		$GLOBALS[ 'arrow_btn' ] = $icon_html;

		$text_indent = $options->get_value('text_indent', 10 );
		$toggleDuration = $options->get_value('slide_toggle_td', 400 );
		$isAccordion = $options->get_value('isAccordion', false);
		$collapseItems = $options->get_value('collapseItems', false);
		$datattr[] = 'data-acrd-toggle-duration="' . intval($toggleDuration) . '"';
		$datattr[] = 'data-acrd-effect="' . ( $isAccordion == 'yes' ? 'yes' : 'no' ) . '"';
		$datattr[] = 'data-acrd-collapsed="' . ( $collapseItems == 'yes' ? 'yes' : 'no' ) . '"';

		$menuSource = $options->get_value('acrd_source_menu', 'wpmenu');
		if( $menuSource == 'wpmenu' ) {
			$acrd_menu = $options->get_value('acrd_menu', 'sel');
			if( $acrd_menu == 'sel' || $acrd_menu == 'nomenu' ) {
				echo __('Select Menu', "ziultimate");
				return;
			}

			$display_title = $options->get_value('display_menu_title', false);
			if( $display_title == 'yes') {
				$tag = $options->get_value('menu_title_tag', 'h4');
				echo '<' . $tag . ' class="zu-acrd-menu-title">'. wp_get_nav_menu_object($acrd_menu)->name . '</' . $tag .'>';
			}

			$args = array(
				'echo'        => false,
				'menu'        => $acrd_menu,
				'menu_class'  => 'zu-acrd-menu-items',
				'menu_id'     => 'menu-' . $this->uid,
				'link_before' => '<span itemprop="name" class="zu-menu-item-text">#MENUICON#',
				'link_after'  => '</span>' . $icon_html,
				'container'   => '',
				'text_indent' => absint( $text_indent )
			);

			add_filter( 'nav_menu_item_args', array( $this, 'zu_acrd_menu_items_args'), 10, 3 );
			add_filter( 'nav_menu_link_attributes', array( $this, 'zu_acrdmenu_link_attributes' ), 10, 4 );
			add_filter( 'walker_nav_menu_start_el', array( $this, 'zu_acrd_walker_nav_menu_start_el' ), 10, 4);

			$menu = '<nav itemscope="" itemtype="https://schema.org/SiteNavigationElement" '. implode(" ", $datattr) .'>';
			$menu .= wp_nav_menu( $args );
			$menu .= '</nav>';

			echo $menu;

			remove_filter( 'nav_menu_link_attributes', array( $this, 'zu_acrdmenu_link_attributes' ), 10, 4 );
			remove_filter( 'nav_menu_item_args', array( $this, 'zu_acrd_menu_items_args'), 10, 3 );
			remove_filter( 'walker_nav_menu_start_el', array( $this, 'zu_acrd_walker_nav_menu_start_el' ), 10, 4);
		}

		/**
		 * Taxonomy
		 */
		if( $menuSource == 'tax' ) {
			$menu = '<nav itemscope="" itemtype="https://schema.org/SiteNavigationElement" '. implode(" ", $datattr) .'>';
			$menu .= '<ul id="menu-' . $this->uid .'" class="zu-acrd-menu-items">';

			$taxonomy = $options->get_value('acrd_tax_name', 'category');

			$args = array(
				'show_option_all'    => '',
				'style'              => 'list',
				'show_count'         => 0,
				'hide_empty'         => 1,
				'hierarchical'       => 1,
				'title_li'           => '',
				'show_option_none'   => '',
				'number'             => null,
				'echo'               => 0,
				'depth'              => 0,
				'current_category'   => 0,
				'pad_counts'         => 0,
				'taxonomy'           => $taxonomy,
				'text_indent' 		 => $text_indent,
				'walker'             => new ZUAccordionMenuWalker,
			);

			$include_ids = $options->get_value('include_ids', false );
			if( $include_ids ) {
				$args['include'] = array_filter( array_map( 'trim', explode( ',', $include_ids ) ) );
			}

			$exclude_ids = $options->get_value('exclude_ids', false );
			if( $exclude_ids ) {
				$args['exclude'] = array_filter( array_map( 'trim', explode( ',', $exclude_ids ) ) );
			}

			$child_of = $options->get_value('child_of', false );
			if( $child_of ) {
				$args['child_of'] = absint( $child_of );
			}

			$limit = $options->get_value('limit', false );
			if( $limit ) {
				$args['number'] = absint( $limit );
			}

			$args['hide_empty'] = $options->get_value('hide_empty', true);
			$args['orderby'] = $options->get_value('orderby', "name");
			$args['order'] = $options->get_value('order', "ASC");

			$menu .= wp_list_categories( $args );

			$menu .= '</ul></nav>';

			echo $menu;
		}
	}

	public function zu_acrd_menu_items_args( $args, $item, $depth ) {
		global $arrow_btn;

		$rp_item = '<span style="margin-left:' . ( $depth * $args->text_indent ). 'px;"';

		$args->link_before = str_replace( '<span', $rp_item, $args->link_before );

		return $args;
	}

	function zu_acrd_walker_nav_menu_start_el( $output, $item, $depth, $args ) {
		$mega_menu_data = MegaMenu::get_config_for_item($item->ID);
		$icon = '';

		if ( isset( $mega_menu_data['icon'] ) ) {
			$icon_attributes = $mega_menu_data['icon'] ? Icons::get_icon_attributes( $mega_menu_data['icon'] ) : [];
			$icon_attributes['class'] = ['zu-acrd-menu-icon'];
			$icon = sprintf('<span %s></span>', $this->implode_attributes($icon_attributes));
		}

		$output = str_replace( '#MENUICON#', $icon, $output );

		return $output;
	}

	function implode_attributes( $attributes ) {
		return implode(' ', array_map(
			function ($k, $v) {
				$value = is_array($v) ? implode(' ', $v) : $v;
				if (! empty($value)) {
					return sprintf('%s="%s"', esc_attr($k), esc_attr($value));
				}
			},
			array_keys($attributes), $attributes
		));
	}

	public function zu_acrdmenu_link_attributes( $atts, $item, $args, $depth ) {
		$atts['itemprop'] = 'url';
		if( isset( $item->title ) ) {
			$atts['data-title'] = esc_attr( $item->title );
		}

		return $atts;
	}
}

class ZUAccordionMenuWalker extends \Walker_Category {

	public function start_lvl( &$output, $depth = 0, $args = array() ) {
        if ( 'list' !== $args['style'] ) {
            return;
        }
 
        $output .= "<ul class='sub-menu'>\n";
    }

	public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		global $arrow_btn;

		$cat_name = apply_filters(
			'list_cats',
			esc_attr( $category->name ),
			$category
		);

		// Don't generate an element if the category name is empty.
		if ( ! $cat_name ) {
			return;
		}

		if ( 'list' == $args['style'] ) {
			$link = '<a href="' . esc_url( get_term_link( $category ) ) . '" itemprop="url" data-title="' . esc_attr( $cat_name ) . '">';
			$link .= '<span itemprop="name" class="zu-menu-item-text" style="margin-left:' . ( $depth * $args['text_indent'] ). 'px;">' . $cat_name . '</span>';

			$output .= "\t<li";
			$css_classes = array(
				'menu-item',
				'cat-item',
				'cat-item-' . $category->term_id
			);

			$termchildren = get_term_children( $category->term_id, $category->taxonomy );

			if( count($termchildren) > 0 ) {
				$css_classes[] = 'menu-item-has-children';
				$link .= $arrow_btn;
			}

			$link .= '</a>';

			if ( ! empty( $args['current_category'] ) ) {
				$_current_category = get_term( $args['current_category'], $category->taxonomy );
				if ( $category->term_id == $args['current_category'] ) {
					$css_classes[] = 'current-menu-item';
				} elseif ( $category->term_id == $_current_category->parent ) {
					$css_classes[] = 'current-menu-ancestor';
				}
			}

			$css_classes = implode( ' ', apply_filters( 'acrd_category_css_class', $css_classes, $category, $depth, $args ) );

			$output .= ' class="' . $css_classes . '"';
			$output .= ">$link\n";
		} else {
            $output .= "\t$link<br />\n";
        }
	}
}