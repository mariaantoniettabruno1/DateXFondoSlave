<?php
namespace ZiUltimate\Elements\SlidingMenu;

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
 * Class SlidingMenu
 *
 * @package ZiUltimate\Elements
 */
class SlidingMenu extends UltimateElements {

	public function get_type() {
		return 'zu_sliding_menu';
	}

	public function get_name() {
		return __( 'Sliding Menu', 'ziultimate' );
	}

	public function get_keywords() {
		return [ 'sliding menu', 'menu', 'slide' ];
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

	/**
	 * Creating the settings fields
	 * 
	 * @return void
	 */
	public function options( $options ) {
		if( ! License::has_valid_license() ) {
			$title = $this->get_name();
			$description = 'With this tool you can build the sliding menu.';
			$options->add_option(
				'el_notices',
				[
					'type' 		=> 'html',
					'content' 	=> self::getHTMLContent($title, $description)
				]
			);

			return;
		}

		$options->add_option(
			'sld_source_menu',
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
			'sld_menu',
			[
				'type' 		=> 'select',
				'title' 	=> __('Select a menu'),
				'options'	=> $this->getWPMenus(),
				'default' 	=> 'sel',
				'dependency'	=> [
					[
						'option' 	=> 'sld_source_menu',
						'value' 	=> [ 'wpmenu' ]
					]
				]
			]
		);

		$options->add_option(
			'sld_tax_name',
			[
				'type' 		=> 'select',
				'title' 	=> __('Select a taxonomy', 'ziultimate'),
				'options'	=> $this->getTaxonomies(),
				'default' 	=> 'category',
				'dependency'	=> [
					[
						'option' 	=> 'sld_source_menu',
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
						'option' 	=> 'sld_source_menu',
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
						'option' 	=> 'sld_source_menu',
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
						'option' 	=> 'sld_source_menu',
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
				'dependency'	=> [
					[
						'option' 	=> 'sld_source_menu',
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
				'dependency'	=> [
					[
						'option' 	=> 'sld_source_menu',
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
						'option' 	=> 'sld_source_menu',
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
						'option' 	=> 'sld_source_menu',
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
						'option' 	=> 'sld_source_menu',
						'value' 	=> [ 'wpmenu' ]
					]
				]
			]
		);

		$options->add_option(
			'menu_title_tag',
			[
				'type' 			=> 'select',
				'title' 		=> __('Tag'),
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
						'option' 	=> 'sld_source_menu',
						'value' 	=> [ 'wpmenu' ]
					]
				]
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

		$selector = '.zu-slide-menu-item-link';

		$menuItems->add_option(
			'link_bg_clr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Background Color', 'ziultimate' ),
				'width' 	=> 47,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} ' . $selector,
						'value'    => 'background-color: {{VALUE}}'
					],
				]
			]
		);

		$menuItems->add_option(
			'link_hv_bgclr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Hover Background Color', 'ziultimate' ),				
				'width' 	=> 53,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .zu-slide-menu-item:hover > ' . $selector,
						'value'    => 'background-color: {{VALUE}}'
					],
				]
			]
		);

		$menuItems->add_option(
			'cm_bgclr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Background Color of Active Link', 'ziultimate' ),
				'description' => __( 'It is for current menu item', 'ziultimate' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .current-menu-item > ' . $selector,
						'value'    => 'background-color: {{VALUE}}'
					],
					[
						'selector' => '{{ELEMENT}} .current-menu-ancestor > ' . $selector,
						'value'    => 'background-color: {{VALUE}}'
					]
				]
			]
		);

		$menuItems->add_option(
			'submenu_bg',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Background Color for Sub Menu Wrapper', 'ziultimate' ),
				'description' => __('You will setup it when auto height is disabled.', 'ziultimate'),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .sub-menu',
						'value'    => 'background-color: {{VALUE}}'
					],
				]
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
			'{{ELEMENT}} .zu-slide-menu-item-link', 
			['text_align', 'font_color', 'text_decoration']
		);

		$linkcolor = $menuItems->add_group(
			'links_color',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> __('Links Color'),
			]
		);

		$linkcolor->add_option(
			'link_clr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Link Color', 'zionbuilder' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} ' . $selector,
						'value'    => 'color: {{VALUE}}'
					]
				]
			]
		);

		$linkcolor->add_option(
			'link_hv_clr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Hover Color', 'zionbuilder' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .zu-slide-menu-item:hover > ' . $selector,
						'value'    => 'color: {{VALUE}}'
					]
				]
			]
		);

		$linkcolor->add_option(
			'cm_clr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Active Link Color', 'zionbuilder' ),
				'description' 	=> __( 'It is for current menu item', 'ziultimate' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .current-menu-item > ' . $selector,
						'value'    => 'color: {{VALUE}}'
					],
					[
						'selector' => '{{ELEMENT}} .current-menu-ancestor > ' . $selector,
						'value'    => 'color: {{VALUE}}'
					]
				]
			]
		);

		$border = $menuItems->add_group(
			'link_border',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> __('Border'),
			]
		);

		$border->add_option(
			'link_brdclr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Border Color' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .zu-slide-menu-item',
						'value'    => 'border-bottom-color: {{VALUE}}'
					],
				]
			]
		);

		$border->add_option(
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
						'selector' => '{{ELEMENT}} .zu-slide-menu-item',
						'value'    => 'border-bottom-width: {{VALUE}}'
					],
				]
			]
		);

		$border->add_option(
			'link_brd_hvclr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Hover Border Color' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .zu-slide-menu-item:hover',
						'value'    => 'border-bottom-color: {{VALUE}}!important'
					],
				]
			]
		);

		$border->add_option(
			'link_brd_atvclr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Active Border Color' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .current-menu-item.zu-slide-menu-item',
						'value'    => 'border-bottom-color: {{VALUE}}'
					],
					[
						'selector' => '{{ELEMENT}} .current-menu-ancestor.zu-slide-menu-item',
						'value'    => 'border-bottom-color: {{VALUE}}'
					],
				],
			]
		);

		$menu_pad = $menuItems->add_option(
			'menu_link_pad',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> __('Padding'),
			]
		);

		$this->attach_padding_options( $menu_pad, 'ml_pad', '{{ELEMENT}} ' . $selector );


		/**
		 * Animation Effect
		 */
		$slide_effect = $options->add_group(
			'sld_effect',
			[
				'type' 		=> 'accordion_menu',
				'title' 	=> __('Sliding Effect', 'ziultimate')
			]
		);

		$slide_effect->add_option(
			'sld_anim_type',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> __('Animation Type', 'ziultimate'),
				'options'	=> [
					[
						'id' 	=> 'overlay',
						'name' 	=> __('Overlay', "ziultimate"),
					],
					[
						'id' 	=> 'push',
						'name' 	=> __('Push', 'ziultimate'),
					]
				],
				'default' => 'overlay'
			]
		);

		$slide_effect->add_option(
			'anim_direction',
			[
				'type' 		=> 'select',
				'title' 	=> __('Direction', 'ziultimate'),
				'options'	=> [
					[
						'id' 	=> 'top',
						'name' 	=> __('Top'),
					],
					[
						'id' 	=> 'left',
						'name' 	=> __('Left'),
					],
					[
						'id' 	=> 'right',
						'name' 	=> __('Right'),
					]
				],
				'default' => 'left'
			]
		);

		$slide_effect->add_option(
			'sd_menu_height',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> __('Auto height', 'ziultimate'),
				'default' 	=> true,
				'layout' 	=> 'inline'
			]
		);

		$slide_effect->add_option(
			'sld_td',
			[
				'type' 		=> 'slider',
				'content' 	=> 's',
				'default' 	=> 0.5,
				'min' 		=> 0,
				'max' 		=> 10,
				'step' 		=> 0.1,
				'title' 	=> __( 'Transition Duration for Slide', 'ziultimate' ),
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .zu-slide-menu-el',
						'value' 	=> 'transition-duration: {{VALUE}}s'
					],
					[
						'selector' 	=> '{{ELEMENT}} .zu-slide-menu-sub-menu',
						'value' 	=> 'transition-duration: {{VALUE}}s'
					],
					[
						'selector' 	=> '{{ELEMENT}}',
						'value' 	=> '--zu-link-opacity: {{VALUE}}s'
					],					
				]
			]
		);


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
					'name'    => 'chevron-right',
					'unicode' => 'uf054',
				]
			]
		);

		$selector = '.zu-slide-menu-arrow';

		$arrow->add_option(
			'arrow_wrapper_width',
			[
				'type' 		=> 'number_unit',
				'default' 	=> '50px',
				'min' 		=> 30,
				'units' 	=> BaseSchema::get_units(),
				'title' 	=> esc_html__('Wrapper Width'),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} ' . $selector,
						'value' 	=> 'width: {{VALUE}}'
					]
				]
			]
		);

		$arrow->add_option(
			'icon_size',
			[
				'type' 		=> 'number_unit',
				'min' 		=> 15,
				'units' 	=> BaseSchema::get_units(),
				'title' 	=> esc_html__('Icon Size'),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} ' . $selector,
						'value' 	=> 'font-size: {{VALUE}}'
					]
				]
			]
		);


		/**
		 * Default Tab
		 */
		$default_state = $arrow->add_group(
			'default_tab',
			[
				'type'  => 'panel_accordion',
				'title' => esc_html__( 'Default State', 'ziultimate' ),
			]
		);

		$default_state->add_option(
			'df_arrow_bg',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Background Color', 'ziultimate' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} ' . $selector,
						'value'    => 'background-color: {{VALUE}}'
					]
				]
			]
		);

		$default_state->add_option(
			'df_icon_clr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Icon Color', 'ziultimate' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} ' . $selector,
						'value'    => 'color: {{VALUE}}'
					]
				]
			]
		);

		$default_state->add_option(
			'df_brd_clr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Border Color', 'ziultimate' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} ' . $selector,
						'value'    => 'border-left-color: {{VALUE}}'
					]
				]
			]
		);

		$default_state->add_option(
			'df_brdw',
			[
				'type' 		=> 'number_unit',
				'min' 		=> 0,
				'default' 	=> '1px',
				'units' 	=> BaseSchema::get_units(),
				'title' 	=> __( 'Border Width', 'ziultimate' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} ' . $selector,
						'value'    => 'border-left-width: {{VALUE}}'
					]
				]
			]
		);



		/**
		 * Hover Tab
		 */
		$hv_state = $arrow->add_group(
			'hover_tab',
			[
				'type'  => 'panel_accordion',
				'title' => esc_html__( 'Hover State' ),
			]
		);

		$hv_state->add_option(
			'hv_arrow_bg',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Background Color', 'ziultimate' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .zu-slide-menu-item:hover > ' . $selector,
						'value'    => 'background-color: {{VALUE}}'
					]
				]
			]
		);

		$hv_state->add_option(
			'hv_icon_clr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Icon Color', 'ziultimate' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .zu-slide-menu-item:hover > ' . $selector,
						'value'    => 'color: {{VALUE}}'
					]
				]
			]
		);

		$hv_state->add_option(
			'hv_brd_clr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Border Color', 'ziultimate' ),				
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .zu-slide-menu-item:hover > ' . $selector,
						'value'    => 'border-left-color: {{VALUE}}'
					]
				]
			]
		);


		/**
		 * Active Tab
		 */
		$active_state = $arrow->add_group(
			'active_tab',
			[
				'type'  => 'panel_accordion',
				'title' => esc_html__( 'Active State' ),
			]
		);

		$active_state->add_option(
			'cm_item_arrow_bg',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Background Color', 'ziultimate' ),
				'width' 	=> 50,
				'description' 	=> __( 'It is for current menu item', 'ziultimate' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .current-menu-item > ' . $selector,
						'value'    => 'background-color: {{VALUE}}'
					],
					[
						'selector' => '{{ELEMENT}} .current-menu-ancestor > ' . $selector,
						'value'    => 'background-color: {{VALUE}}'
					]
				]
			]
		);

		$active_state->add_option(
			'cm_item_icon_clr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Icon Color', 'ziultimate' ),
				'width' 	=> 50,
				'description' 	=> __( 'It is for current menu item', 'ziultimate' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .current-menu-item > ' . $selector,
						'value'    => 'color: {{VALUE}}'
					],
					[
						'selector' => '{{ELEMENT}} .current-menu-ancestor > ' . $selector,
						'value'    => 'color: {{VALUE}}'
					]
				]
			]
		);

		$active_state->add_option(
			'cm_brd_clr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Border Color', 'ziultimate' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .current-menu-item > ' . $selector,
						'value'    => 'border-left-color: {{VALUE}}'
					],
					[
						'selector' => '{{ELEMENT}} .current-menu-ancestor > ' . $selector,
						'value'    => 'border-left-color: {{VALUE}}'
					]
				]
			]
		);


		/**
		 * Back bar
		 */
		$back_bar = $options->add_group(
			'back_bar',
			[
				'type' 	=> 'accordion_menu',
				'title' => __('Back Button', 'ziultimate')
			]
		);

		$back_bar->add_option(
			'back_text',
			[
				'type' 	=> 'text',
				'title' => __('Back Text'),
				'default' => __('Back'),
				'description' => esc_html__('This text will show if a menu item has not title. It will work as a placeholder text.', 'ziultimate')
			]
		);

		$back_bar->add_option(
			'back_arrow_icon',
			[
				'type'       => 'icon_library',
				'id'         => 'icon',
				'title'      => esc_html__( 'Select Icon', 'zionbuilder' ),
				'default'    => [
					'family'  => 'Font Awesome 5 Free Solid',
					'name'    => 'chevron-left',
					'unicode' => 'uf053',
				]
			]
		);

		$selector = '.zu-slide-menu-back-arrow';

		$back_bar->add_option(
			'ba_wrapper_width',
			[
				'type' 		=> 'number_unit',
				'default' 	=> '50px',
				'min' 		=> 30,
				'units' 	=> BaseSchema::get_units(),
				'title' 	=> esc_html__('Wrapper Width'),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} ' . $selector,
						'value' 	=> 'width: {{VALUE}}'
					]
				]
			]
		);

		$back_bar->add_option(
			'baicon_size',
			[
				'type' 		=> 'number_unit',
				'min' 		=> 15,
				'units' 	=> BaseSchema::get_units(),
				'title' 	=> esc_html__('Icon Size'),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} ' . $selector,
						'value' 	=> 'font-size: {{VALUE}}'
					]
				]
			]
		);


		/**
		 * Default Tab
		 */
		$default_state = $back_bar->add_option(
			'ba_default_tab',
			[
				'type'  => 'panel_accordion',
				'title' => esc_html__( 'Default State', 'ziultimate' ),
			]
		);

		$default_state->add_option(
			'df_ba_bg',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Arrow Background Color', 'ziultimate' ),
				'width' 	=> 58,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} ' . $selector,
						'value'    => 'background-color: {{VALUE}}'
					]
				]
			]
		);

		$default_state->add_option(
			'df_baicon_clr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Icon Color', 'ziultimate' ),
				'width' 	=> 42,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} ' . $selector,
						'value'    => 'color: {{VALUE}}'
					]
				]
			]
		);

		$default_state->add_option(
			'df_ba_brdclr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Border Color', 'ziultimate' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} ' . $selector,
						'value'    => 'border-right-color: {{VALUE}}'
					]
				]
			]
		);

		$default_state->add_option(
			'df_ba_brdw',
			[
				'type' 		=> 'number_unit',
				'min' 		=> 0,
				'units' 	=> BaseSchema::get_units(),
				'default' 	=> '1px',
				'title' 	=> __( 'Border Width', 'ziultimate' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} ' . $selector,
						'value'    => 'border-right-width: {{VALUE}}'
					]
				]
			]
		);

		/**
		 * Hover Tab
		 */
		$hv_state = $back_bar->add_option(
			'ba_hover_tab',
			[
				'type'  => 'panel_accordion',
				'title' => esc_html__( 'Hover State', 'ziultimate' ),
			]
		);

		$hv_state->add_option(
			'hv_ba_bg',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Arrow Background Color', 'ziultimate' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .zu-slide-menu-item:hover > ' . $selector,
						'value'    => 'background-color: {{VALUE}}'
					]
				]
			]
		);

		$hv_state->add_option(
			'hv_baicon_clr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Icon Color', 'ziultimate' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .zu-slide-menu-item:hover > ' . $selector,
						'value'    => 'color: {{VALUE}}'
					]
				]
			]
		);

		$hv_state->add_option(
			'hv_ba_brdclr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Border Color', 'ziultimate' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .zu-slide-menu-item:hover > ' . $selector,
						'value'    => 'border-right-color: {{VALUE}}'
					]
				]
			]
		);


		/**
		 * Hover Animation Settings
		 */
		$animation = $options->add_group(
			'sld_hover_animation',
			[
				'type' 		=> 'accordion_menu',
				'title' 	=> __('Hover Animation', 'ziultimate'),
			]
		);

		$animation->add_option(
			'sld_hv_td',
			[
				'type' 		=> 'slider',
				'content' 	=> 's',
				'default' 	=> 0.4,
				'min' 		=> 0,
				'max' 		=> 10,
				'step' 		=> 0.1,
				'title' 	=> __( 'Transition Duration', 'ziultimate' ),
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .zu-slide-menu-arrow',
						'value' 	=> 'transition-duration: {{VALUE}}s'
					],
					[
						'selector' 	=> '{{ELEMENT}} .zu-slide-menu-item-link',
						'value' 	=> 'transition-duration: {{VALUE}}s'
					],
					[
						'selector' 	=> '{{ELEMENT}} .zu-slide-menu-back, {{ELEMENT}} .zu-menu-sub-item-back',
						'value' 	=> 'transition-duration: {{VALUE}}s'
					],
					[
						'selector' 	=> '{{ELEMENT}} .zu-slide-menu-back-arrow',
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
						'value' 	=> '--link-span-translatex: {{VALUE}}px'
					]
				]
			]
		);
	}

	/**
	 * Loaing the CSS
	 */
	public function enqueue_styles() {
		$this->enqueue_element_style( Utils::get_file_url('dist/css/elements/SlidingMenu/slidingmenu.css' ) );
	}

	/**
	 * Loading the scripts
	 */
	public function enqueue_scripts() {
		$this->enqueue_editor_script( Utils::get_file_url( 'dist/js/elements/SlidingMenu/editor.js' ) );
		$this->enqueue_element_script( Utils::get_file_url( 'dist/js/elements/SlidingMenu/frontend.js' ) );
	}

	/**
	 * Registering the styles
	 */
	public function on_register_styles() {
		$this->register_style_options_element(
			'sdmenu_title_styles',
			[
				'title'    => esc_html__( 'Menu Title', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .zu-sdmenu-title',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'back_bar_styles',
			[
				'title'    => esc_html__( 'Back Title', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .zu-menu-sub-item-back',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);
	}

	public function render( $options ) {
		//* Arrow Icon for menu items
		$icon = $options->get_value( 'arrow_icon' );

		$this->attach_icon_attributes( 'submenu_indicator', $icon );
		$this->render_attributes->add( 'submenu_indicator', 'class', 'zu-slide-menu-arrow' );
		$this->render_attributes->add( 'submenu_indicator', 'aria-expanded', "false" );
		$this->render_attributes->add( 'submenu_indicator', 'aria-pressed', "false" );

		$icon_html = $this->get_render_tag( 'span', 'submenu_indicator' );

		//* Back arrow
		$back_arrow_icon = $options->get_value( 'back_arrow_icon' );
		$this->attach_icon_attributes( 'back_arrow', $back_arrow_icon );
		$this->render_attributes->add( 'back_arrow', 'class', 'zu-slide-menu-back-arrow' );
		$back_arrow_html = $this->get_render_tag( 'span', 'back_arrow' );

		$effect = $options->get_value( 'sld_anim_type', 'overlay' );
		$direction = $options->get_value( 'anim_direction', 'left' );
		$classes = 'zu-sliding-menu-effect-' . $effect;
		$classes .= ' zu-sliding-menu-direction-' . $direction;

		$datattr = array();
		$datattr[] = 'data-back-text="' . $options->get_value('back_text') . '"';
		$datattr[] = 'data-nav-height="' . $options->get_value('sd_menu_height', true) . '"';

		$menuSource = $options->get_value('sld_source_menu', 'wpmenu');
		if( $menuSource == 'wpmenu' ) {
			$sld_menu = $options->get_value('sld_menu', 'sel');
			if( $sld_menu == 'sel' || $sld_menu == 'nomenu' ) {
				echo '<p class="nomenu">' . __('Select Menu', "ziultimate") . '</p>';
				return;
			}

			$display_title = $options->get_value('display_menu_title', false);
			if( $display_title ) {
				$tag = $options->get_value('menu_title_tag', 'h4');
				echo '<' . $tag . ' class="zu-sdmenu-title">'. wp_get_nav_menu_object($sld_menu)->name . '</' . $tag .'>';
			}

			$args = array(
				'echo'        => false,
				'menu'        => $sld_menu,
				'menu_class'  => 'zu-slide-menu-el',
				'menu_id'     => 'zu-menu-' . $this->uid,
				'fallback_cb' => '__return_empty_string',
				'before'      => $icon_html,
				'link_before' => '#MENUICON#<span itemprop="name">',
				'link_after'  => '</span>',
				'container'   => '',
				'walker' 	=> new ZU_Sliding_Nav_Walker,
				'back_arrow_icon' => $back_arrow_html
			);

			add_filter( 'walker_nav_menu_start_el', array( $this, 'zu_walker_nav_menu_start_el' ), 10, 4);
			add_filter( 'nav_menu_link_attributes', array( $this, 'zu_sdmenu_link_attributes' ), 10, 4 );
			add_filter( 'nav_menu_submenu_css_class', array( $this, 'zu_sdmenu_submenu_css_class' ) );
			add_filter( 'nav_menu_css_class', array( $this, 'zu_sdmenu_css_class' ) );

			$menu = '<nav class="'. $classes .'" itemscope="" itemtype="https://schema.org/SiteNavigationElement" '. implode(" ", $datattr) .'>';
			$menu .= wp_nav_menu( $args );
			$menu .= '</nav>';

			remove_filter( 'walker_nav_menu_start_el', array( $this, 'zu_walker_nav_menu_start_el' ), 10, 4);
			remove_filter( 'nav_menu_link_attributes', array( $this, 'zu_sdmenu_link_attributes' ), 10, 4 );
			remove_filter( 'nav_menu_submenu_css_class', array( $this, 'zu_sdmenu_submenu_css_class' ) );
			remove_filter( 'nav_menu_css_class', array( $this, 'zu_sdmenu_css_class' ) );

			echo $menu;
		}


		/**
		 * Terms list
		 */
		if( $menuSource == 'tax' ) {
			$taxonomy = $options->get_value('sld_tax_name', 'category');

			$args = array(
				'show_option_all'    => '',
				'style'              => 'list',
				'show_count'         => 0,
				'hide_empty'         => 1,
				'hierarchical'       => 1,
				'include'    		 => '',
				'exclude'    		 => '',
				'title_li'           => '',
				'show_option_none'   => '',
				'number'             => null,
				'echo'               => 0,
				'depth'              => 0,
				'current_category'   => 0,
				'pad_counts'         => 0,
				'taxonomy'           => $taxonomy,
				'walker'             => new ZU_Sliding_Category_Walker
			);

			$include_ids = $options->get_value('include_ids', false);
			if( $include_ids ) {
				$args['include'] = array_filter( array_map( 'trim', explode( ',', $include_ids ) ) );
			}

			$exclude_ids = $options->get_value('exclude_ids', false);
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

			$args['arrow_icon'] = $icon_html;
			$args['back_arrow_icon'] = $back_arrow_html;

			$args['hide_empty'] = $options->get_value('hide_empty', true);
			$args['orderby'] = $options->get_value('orderby', "name");
			$args['order'] = $options->get_value('order', "ASC");

			$sm_html = '<nav class="'. $classes .'" itemscope="" itemtype="https://schema.org/SiteNavigationElement" '. implode(" ", $datattr) .'>';
			$sm_html .= '<ul id="zu-menu-' . $this->uid .'" class="zu-slide-menu-el">';
			$sm_html .= wp_list_categories( $args );
			$sm_html .= '</ul></nav>';

			echo $sm_html;
		}
	}

	function zu_walker_nav_menu_start_el( $output, $item, $depth, $args ) {
		$mega_menu_data = MegaMenu::get_config_for_item($item->ID);
		$icon = '';

		if ($mega_menu_data['icon']) {
			$icon_attributes = $mega_menu_data['icon'] ? Icons::get_icon_attributes( $mega_menu_data['icon'] ) : [];
			$icon_attributes['class'] = ['zu-slide-menu-icon'];
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

	function zu_sdmenu_link_attributes( $atts, $item, $args, $depth ) {
		$classes = $depth ? 'zu-slide-menu-item-link zu-slide-menu-sub-item-link' : 'zu-slide-menu-item-link';

		if ( in_array( 'current-menu-item', $item->classes ) ) {
			$classes .= ' zu-slide-menu-item-link-current';
		}

		if ( empty( $atts['class'] ) ) {
			$atts['class'] = $classes;
		} else {
			$atts['class'] .= ' ' . $classes;
		}

		$atts['itemprop'] = 'url';
		if( isset( $item->title ) ) {
			$atts['data-title'] = esc_attr( $item->title );
		}

		return $atts;
	}

	function zu_sdmenu_submenu_css_class( $classes ) {
		$classes[] = 'zu-slide-menu-sub-menu';

		return $classes;
	}

	function zu_sdmenu_css_class( $classes ) {
		$classes[] = 'zu-slide-menu-item';

		if ( in_array( 'menu-item-has-children', $classes ) ) {
			$classes[] = 'zu-slide-menu-item-has-children';
		}

		if ( in_array( 'current-menu-item', $classes ) ) {
			$classes[] = 'zu-slide-menu-item-current';
		}

		return $classes;
	}
}


/**
 * Walker for Manu Items
 * Adding the Back button
 */
class ZU_Sliding_Nav_Walker extends \Walker_Nav_Menu {
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = str_repeat( $t, $depth );
 
        // Default class.
        $classes = array( 'sub-menu' );
 
        $class_names = implode( ' ', apply_filters( 'nav_menu_submenu_css_class', $classes, $args, $depth ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
 
		if( $args->walker->has_children ) { 
        	$output .= "{$n}{$indent}<ul$class_names>{$n}" . 
        				'<li class="menu-item zu-slide-menu-item zu-slide-menu-back" aria-expanded="false" aria-pressed="false" aria-hidden="true">' . 
						$args->back_arrow_icon . '<span class="zu-menu-sub-item-back" role="button" aria-label="hidden">Back</span></li>' . "\n"; 
		} else {
			$output .= "{$n}{$indent}<ul$class_names>{$n}";
		}
	}
}


/**
 * Walker for Category
 * Adding the Back button and managing the classes
 */
class ZU_Sliding_Category_Walker extends \Walker_Category {

	public function start_lvl( &$output, $depth = 0, $args = array() ) {
        if ( 'list' !== $args['style'] ) {
            return;
        }
        
        $output .= "<ul class='sub-menu zu-slide-menu-sub-menu'>\n";
		$output .= '<li class="menu-item zu-slide-menu-item zu-slide-menu-back" aria-expanded="false" aria-pressed="false" aria-hidden="true">' . 
					$args['back_arrow_icon'] . '<span class="zu-menu-sub-item-back" role="button" aria-label="hidden">Back</span></li>' . "\n";
    }

	public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
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
			$link = '<a href="' . esc_url( get_term_link( $category ) ) . '" class="zu-slide-menu-item-link" itemprop="url" data-title="' . esc_attr( $cat_name ) . '">';
			$link .= '<span itemprop="name">' . $cat_name . '</span></a>';

			$output .= "\t<li";
			$css_classes = array(
				'menu-item',
				'cat-item',
				'cat-item-' . $category->term_id,
				'zu-slide-menu-item'
			);

			$termchildren = get_term_children( $category->term_id, $category->taxonomy );

			if( count($termchildren) > 0 ) {
				$css_classes[] =  'menu-item-has-children';
				$css_classes[] =  'zu-slide-menu-item-has-children';
			}

			if ( ! empty( $args['current_category'] ) ) {
				$_current_category = get_term( $args['current_category'], $category->taxonomy );
				if ( $category->term_id == $args['current_category'] ) {
					$css_classes[] = 'current-menu-item zu-slide-menu-item-current';
				} elseif ( $category->term_id == $_current_category->parent ) {
					$css_classes[] = 'current-menu-ancestor';
				}
			}

			$css_classes = implode( ' ', apply_filters( 'sliding_category_css_class', $css_classes, $category, $depth, $args ) );

			$output .=  ' class="' . $css_classes . '"';
			$output .= ">" . $args['arrow_icon'] . "\n$link\n";
		} else {
            $output .= "\t$link<br />\n";
        }
	}
}