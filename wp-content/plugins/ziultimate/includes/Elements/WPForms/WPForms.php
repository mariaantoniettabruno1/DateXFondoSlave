<?php
namespace ZiUltimate\Elements\WPForms;

use ZiUltimate\UltimateElements;
use ZiUltimate\Utils;
use ZiUltimate\Admin\License;
use ZionBuilder\Options\BaseSchema;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class WPForms
 *
 * @package ZiUltimate\Elements
 */
class WPForms extends UltimateElements {
	
	public function get_type() {
		return 'zu_wp_forms';
	}

	public function get_name() {
		return __( 'WPForms Styler', 'ziultimate' );
	}

	public function get_keywords() {
		return [ 'wpforms', 'form', 'form', 'styler' ];
	}

	public function get_element_icon() {
		return 'element-form';
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
	 * Registering the options form
	 * 
	 * @return void
	 */
	public function options( $options ) {
		if( ! License::has_valid_license() ) {
			$title = $this->get_name();
			$description = "You can directly customize your form's field on builder editor.";
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
			'source_type',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> esc_html__( 'Source Type', 'ziultimate' ),
				'default' 	=> 'static',
				'options' 	=> [
					[
						'name' => 'Static',
						'id'   => 'static',
					],
					[
						'name' => 'Custom Field',
						'id'   => 'dynamic',
					]
				],
			]
		);

		$options->add_option(
			'wp_form',
			[
				'type' 			=> 'select',
				'default' 		=> -1,
				'title' 		=> __( 'Select WP Form', 'ziultimate' ),
				'description' 	=> __( "Make sure that you build at least one wpform.", 'ziultimate' ),
				'options' 		=> $this->zu_get_wpforms(),
				'dependency'	=> [
					[
						'option' 	=> 'source_type',
						'value' 	=> [ 'static' ]
					]
				]
			]
		);

		$options->add_option(
			'wpform_dymc',
			[
				'type' 			=> 'text',
				'title' 		=> __( 'Setup Form ID', 'ziultimate' ),
				'description' 	=> __( "Make sure that it returns the form ID.", 'ziultimate' ),
				'dependency'		=> [
					[
						'option' 	=> 'source_type',
						'value' 	=> [ 'dynamic' ]
					]
				],
				'dynamic'     	=> [
					'enabled' => true,
				]
			]
		);

		$options->add_option(
			'wpform_title',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> __('Show title?', 'ziultimate'),
				'default' 	=> true,
				'layout' 	=> 'inline',
			]
		);

		$options->add_option(
			'wpform_desc',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> __('Show description?', 'ziultimate'),
				'default' 	=> false,
				'layout' 	=> 'inline',
			]
		);

		$options->add_option(
			'wpf_row_gap',
			[
				'type' 			=> 'number_unit',
				'min' 			=> 0,
				'default' 		=> '10px',
				'units' 		=> BaseSchema::get_units(),
				'title' 		=> __( 'Vertical Gap', 'ziultimate' ),
				'description' 	=> __('Gap between rows', 'ziultimate'),
				'responsive_options' => true,
				'css_style' 	=> [
					[
						'selector' => '{{ELEMENT}} .wpforms-form .wpforms-field',
						'value'    => 'margin-bottom: {{VALUE}}',
					],
				]
			]
		);

		$options->add_option(
			'wpf_textarea_h',
			[
				'type' 			=> 'number_unit',
				'units' 		=> BaseSchema::get_units(),
				'title' 		=> __( 'Textarea Height', 'ziultimate' ),
				'responsive_options' => true,
				'css_style' 	=> [
					[
						'selector' => '{{ELEMENT}} .wpforms-field-textarea textarea',
						'value'    => 'height: {{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'ast_color',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Asterisk Color', 'zionbuilder' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} div.wpforms-container-full .wpforms-form .wpforms-required-label',
						'value'    => 'color: {{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'ast_size',
			[
				'type' 		=> 'number_unit',
				'title'     => __( 'Asterisk Size', 'ziultimate' ),
				'width' 	=> 50,
				'units' 	=> BaseSchema::get_units(),
				'responsive_options' => true,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} div.wpforms-container-full .wpforms-form .wpforms-required-label',
						'value'    => 'font-size: {{VALUE}}',
					]
				],
			]
		);

		$options->add_option(
			'file_icon_color',
			[
				'type' 		=> 'colorpicker',
				'title'     => __( 'File Upload Field Icon Color', 'ziultimate' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} div.wpforms-uploader svg',
						'value'    => 'fill: {{VALUE}}',
					]
				],
			]
		);

		/**
		 * Radio & Checkboxes
		 */
		$cb_selector = '{{ELEMENT}} .wpforms-field input[type=checkbox]:after';
		$rd_selector = '{{ELEMENT}} .wpforms-field input[type=radio]:after';
		$cb_checked_selector = '{{ELEMENT}} .wpforms-field input:checked:after';
		$cb = $options->add_group(
			'wpf_cb',
			[
				'type' 	=> 'accordion_menu',
				'title' => esc_html__('Radio & Checkboxes', 'ziultimate')
			]
		);

		$cb->add_option(
			'cb_smart_ui',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> __('Enable Smart UI', 'ziultimate'),
				'default' 	=> false,
				'layout' 	=> 'inline'
			]
		);

		$cb->add_option(
			'cb_size',
			[
				'type' 		=> 'slider',
				'title' 	=> __( 'Size' ),
				'content' 	=> 'px',
				'default' 	=> 15,
				'min' 		=> 15,
				'max' 		=> 30,
				'step' 		=> 1,
				'css_style' => [
					[
						'selector' => $cb_selector,
						'value'    => 'width: {{VALUE}}px',
					],
					[
						'selector' => $cb_selector,
						'value'    => 'height: {{VALUE}}px',
					],
					[
						'selector' => $rd_selector,
						'value'    => 'width: {{VALUE}}px',
					],
					[
						'selector' => $rd_selector,
						'value'    => 'height: {{VALUE}}px',
					],
				],
				'dependency' 	=> [
					[
						'option' 	=> 'cb_smart_ui',
						'value' 	=> [ true ]
					]
				]
			]
		);

		$cb->add_option(
			'cb_brd_wd',
			[
				'type' 		=> 'number_unit',
				'title' 	=> __( 'Border Width' ),
				'default' 	=> '1px',
				'min' 		=> 0,
				'units' 	=> BaseSchema::get_units(),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => $cb_selector,
						'value'    => 'border-width: {{VALUE}}',
					],
					[
						'selector' => $rd_selector,
						'value'    => 'border-width: {{VALUE}}',
					],
				],
				'dependency' 	=> [
					[
						'option' 	=> 'cb_smart_ui',
						'value' 	=> [ true ]
					]
				]
			]
		);

		$cb->add_option(
			'cb_brd_rd',
			[
				'type' 		=> 'number_unit',
				'title' 	=> __( 'Border Radius' ),
				'description' => __('This is for checkbox only.', 'ziultimate'),
				'min' 		=> 0,
				'units' 	=> BaseSchema::get_units(),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => $cb_selector,
						'value'    => 'border-radius: {{VALUE}}',
					]
				],
				'dependency' 	=> [
					[
						'option' 	=> 'cb_smart_ui',
						'value' 	=> [ true ]
					]
				]
			]
		);

		$cb->add_option(
			'cb_brd_clr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Border Color' ),
				'css_style' => [
					[
						'selector' => $cb_selector,
						'value'    => 'border-color: {{VALUE}}',
					],
					[
						'selector' => $rd_selector,
						'value'    => 'border-color: {{VALUE}}',
					],
				],
				'dependency' 	=> [
					[
						'option' 	=> 'cb_smart_ui',
						'value' 	=> [ true ]
					]
				]
			]
		);

		$cb->add_option(
			'cb_bg_clr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Background Color' ),
				'css_style' => [
					[
						'selector' => $cb_selector,
						'value'    => 'background-color: {{VALUE}}',
					],
					[
						'selector' => $rd_selector,
						'value'    => 'background-color: {{VALUE}}',
					],
				],
				'dependency' 	=> [
					[
						'option' 	=> 'cb_smart_ui',
						'value' 	=> [ true ]
					]
				]
			]
		);

		$cb->add_option(
			'cb_chbrd_clr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Checked Border Color' ),
				'css_style' => [
					[
						'selector' => $cb_checked_selector,
						'value'    => 'border-color: {{VALUE}}',
					],
				],
				'dependency' 	=> [
					[
						'option' 	=> 'cb_smart_ui',
						'value' 	=> [ true ]
					]
				]
			]
		);

		$cb->add_option(
			'cb_chbg_clr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Checked Background Color' ),
				'css_style' => [
					[
						'selector' => $cb_checked_selector,
						'value'    => 'background-color: {{VALUE}}',
					],
				],
				'dependency' 	=> [
					[
						'option' 	=> 'cb_smart_ui',
						'value' 	=> [ true ]
					]
				]
			]
		);

		$cb->add_option(
			'cb_cb_size',
			[
				'type' 		=> 'slider',
				'title' 	=> __( 'White Check Mark Size', 'ziultimate' ),
				'content' 	=> 'px',
				'default' 	=> 9,
				'min' 		=> 9,
				'max' 		=> 30,
				'step' 		=> 1,
				'css_style' => [
					[
						'selector' => $cb_selector,
						'value'    => 'background-size: {{VALUE}}px',
					]
				],
				'dependency' 	=> [
					[
						'option' 	=> 'cb_smart_ui',
						'value' 	=> [ true ]
					]
				]
			]
		);

		$cb->add_option(
			'cb_bt_size',
			[
				'type' 		=> 'slider',
				'title' 	=> __( 'White Bullet Size (radio button)', 'ziultimate' ),
				'content' 	=> 'px',
				'default' 	=> 9,
				'min' 		=> 9,
				'max' 		=> 30,
				'step' 		=> 1,
				'css_style' => [
					[
						'selector' => $rd_selector,
						'value'    => 'background-size: {{VALUE}}px',
					]
				],
				'dependency' 	=> [
					[
						'option' 	=> 'cb_smart_ui',
						'value' 	=> [ true ]
					]
				]
			]
		);

		$cb_mrg = $cb->add_option(
			'cb_mrg',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> __( 'Margin' ),
				'dependency' 	=> [
					[
						'option' 	=> 'cb_smart_ui',
						'value' 	=> [ true ]
					]
				]
			]
		);

		$this->attach_margin_options( $cb_mrg, 'cb_mrg', '{{ELEMENT}} .zu-cbrb-label');

		$cb_tg = $cb->add_option(
			'cb_typography',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> __( 'Label Typography' ),
			]
		);

		$this->attach_typography_options( $cb_tg, 'cb_label_tg', '{{ELEMENT}} .zu-cbrb-label', ['text-align'] );

		/**
		 * Rating section
		 */
		$rating = $options->add_group(
			'rating_group',
			[
				'type' 		=> 'accordion_menu',
				'title' 	=> esc_html__('Rating', 'ziultimate')
			]
		);

		$rating->add_option(
			'star_size',
			[
				'type' 		=> 'slider',
				'content' 	=> 'px',
				'default' 	=> 28,
				'min' 		=> 10,
				'max' 		=> 100,
				'step' 		=> 1,
				'title'     => __( 'Stars Size', 'ziultimate' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .wpforms-form .wpforms-field-rating svg',
						'value'    => 'width: {{VALUE}}px!important',
					],
					[
						'selector' => '{{ELEMENT}} .wpforms-form .wpforms-field-rating svg',
						'value'    => 'height: {{VALUE}}px!important',
					]
				],
			]
		);

		$rating->add_option(
			'star_gap',
			[
				'type' 		=> 'slider',
				'content' 	=> 'px',
				'default' 	=> 5,
				'min' 		=> 0,
				'max' 		=> 20,
				'step' 		=> 1,
				'title'     => __( 'Gap', 'ziultimate' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .wpforms-form .wpforms-field-rating-item',
						'value'    => 'padding-right: {{VALUE}}px',
					]
				],
			]
		);

		$rating->add_option(
			'stars_color',
			[
				'type' 		=> 'colorpicker',
				'title'     => __( 'Color', 'ziultimate' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .wpforms-form .wpforms-field-rating svg',
						'value'    => 'fill: {{VALUE}}',
					]
				],
			]
		);

		$rating->add_option(
			'stars_hcolor',
			[
				'type' 		=> 'colorpicker',
				'title'     => __( 'Selected Color', 'ziultimate' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .wpforms-form .wpforms-field-rating-item.hover svg',
						'value'    => 'fill: {{VALUE}}',
					],
					[
						'selector' => '{{ELEMENT}} .wpforms-form .wpforms-field-rating-item.selected svg',
						'value'    => 'fill: {{VALUE}}',
					]
				],
			]
		);

		$rating->add_option(
			'star_opacity',
			[
				'type' 		=> 'slider',
				'content' 	=> '',
				'default' 	=> 0.6,
				'min' 		=> 0,
				'max' 		=> 1,
				'step' 		=> 0.1,
				'title'     => __( 'Default Opacity', 'ziultimate' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .wpforms-form .wpforms-field-rating svg',
						'value'    => 'opacity: {{VALUE}}',
					]
				],
			]
		);

		$rating->add_option(
			'star_opacity_hover',
			[
				'type' 		=> 'slider',
				'content' 	=> '',
				'default' 	=> 1,
				'min' 		=> 0,
				'max' 		=> 1,
				'step' 		=> 0.1,
				'title'     => __( 'Opacity for Hover/Selected State', 'ziultimate' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .wpforms-form .wpforms-field-rating-item.hover svg',
						'value'    => 'opacity: {{VALUE}}',
					],
					[
						'selector' => '{{ELEMENT}} .wpforms-form .wpforms-field-rating-item.selected svg',
						'value'    => 'opacity: {{VALUE}}',
					]
				],
			]
		);

		$rating->add_option(
			'star_sacle_hover',
			[
				'type' 		=> 'slider',
				'content' 	=> '',
				'default' 	=> 1.3,
				'min' 		=> 0,
				'max' 		=> 3,
				'step' 		=> 0.1,
				'title'     => __( 'Hover Transform Scale', 'ziultimate' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .wpforms-form .wpforms-field-rating-item.hover svg',
						'value'    => 'transform: scale({{VALUE}})',
					],
					[
						'selector' => '{{ELEMENT}} .wpforms-form .wpforms-field-rating-item.selected svg',
						'value'    => 'transform: scale({{VALUE}})',
					],
					[
						'selector' => '{{ELEMENT}} .wpforms-form .wpforms-field-rating-item.hover svg',
						'value'    => '-webkit-transform: scale({{VALUE}})',
					],
					[
						'selector' => '{{ELEMENT}} .wpforms-form .wpforms-field-rating-item.selected svg',
						'value'    => '-webkit-transform: scale({{VALUE}})',
					]
				],
			]
		);


		/**
		 * Image Checkboxes
		 */
		$imgcb = $options->add_group(
			'imgcb_group',
			[
				'type' 		=> 'accordion_menu',
				'title' 	=> esc_html__('Image Checkboxes', 'ziultimate')
			]
		);

		$imgcb->add_option(
			'label_bg_hcolor',
			[
				'type' 		=> 'colorpicker',
				'title'     => __( 'Background Color', 'ziultimate' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .wpforms-image-choices .wpforms-image-choices-item label',
						'value'    => 'background-color: {{VALUE}}',
					]
				],
			]
		);

		$imgcb->add_option(
			'label_hbg_hcolor',
			[
				'type' 		=> 'colorpicker',
				'title'     => __( 'Hover and Selected Background Color', 'ziultimate' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .wpforms-image-choices .wpforms-image-choices-item:hover label',
						'value'    => 'background-color: {{VALUE}}',
					],
					[
						'selector' => '{{ELEMENT}} .wpforms-image-choices .wpforms-selected label',
						'value'    => 'background-color: {{VALUE}}',
					]
				],
			]
		);

		$imgcb->add_option(
			'imgcb_colgap',
			[
				'type' 			=> 'number_unit',
				'min' 			=> 0,
				'default' 		=> '25px',
				'units' 		=> BaseSchema::get_units(),
				'title' 		=> __( 'Horizontal Gap', 'ziultimate' ),
				'description' 	=> __('Gap between columns', 'ziultimate'),
				'label-position' => 'left',
				'label-icon' 	=> 'horizontal',
				'label-title' 	=> esc_html__( 'Horizontal distance', 'zionbuilder' ),
				'width' 		=> 50,
				'responsive_options' => true,
				'css_style' 	=> [
					[
						'selector' => "{{ELEMENT}} div[class*='-columns'] .wpforms-image-choices-item",
						'value'    => 'padding-right: {{VALUE}}!important',
					],
				],
			]
		);

		$imgcb->add_option(
			'img_rowgap',
			[
				'type' 			=> 'number_unit',
				'min' 			=> 0,
				'default' 		=> '5px',
				'units' 		=> BaseSchema::get_units(),
				'title' 		=> __( 'Vertical Gap', 'ziultimate' ),
				'description' 	=> __('Gap between rows', 'ziultimate'),
				'label-position' => 'right',
				'label-icon' 	=> 'vertical',
				'label-title' 	=> esc_html__( 'Vertical distance', 'zionbuilder' ),
				'width' 		=> 50,
				'responsive_options' => true,
				'css_style' 	=> [
					[
						'selector' => '{{ELEMENT}} .wpforms-image-choices .wpforms-image-choices-item',
						'value'    => 'margin-bottom: {{VALUE}}!important',
					],
				],
			]
		);

		$imgcb->add_option(
			'custom_el_imgcb',
			[
				'type' 		=> 'html',
				'title' 	=> __('Border'),
				'content' 	=> '<hr style="border: 1px solid #e5e5e5"/>'
			]
		);

		$imgcb->add_option(
			'imgcb_brdclr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Color' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .wpforms-image-choices .wpforms-image-choices-item label',
						'value'    => 'border-color: {{VALUE}}',
					],
				],
			]
		);

		$imgcb->add_option(
			'imgcb_brdwd',
			[
				'type' 		=> 'number_unit',
				'default'	=> '2px',
				'min' 		=> 0,
				'units' 	=> BaseSchema::get_units(),
				'title' 	=> __( 'Width' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .wpforms-image-choices .wpforms-image-choices-item label',
						'value'    => 'border-width: {{VALUE}}',
					],
				],
			]
		);

		$imgcb->add_option(
			'sel_brd_clr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Hover and Selected Color' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .wpforms-image-choices .wpforms-image-choices-item:hover label',
						'value'    => 'border-color: {{VALUE}}',
					],
					[
						'selector' => '{{ELEMENT}} .wpforms-image-choices .wpforms-selected label',
						'value'    => 'border-color: {{VALUE}}',
					],
				],
			]
		);


		$imgcb->add_option(
			'imgcb_pad',
			[
				'type' 		=> 'html',
				'title' 	=> __('Wrapper Padding', 'ziultimate'),
				'content' 	=> '<hr style="border: 1px solid #e5e5e5"/>'
			]
		);

		$this->attach_padding_options( $imgcb, 'imgcb_pad', '{{ELEMENT}} .wpforms-image-choices-item label');

		$imgcb_tg = $imgcb->add_option(
			'imgcb_typography',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> __( 'Label Typography' ),
				'collapsed' => true,
			]
		);

		$this->attach_typography_options( $imgcb_tg, 'imgcb_tg', '{{ELEMENT}} .wpforms-image-choices-item .wpforms-image-choices-label' );		


		/**
		 * Page Break
		 */
		$pagebreak = $options->add_group(
			'pb_group',
			[
				'type' 		=> 'accordion_menu',
				'title' 	=> esc_html__('Page Break', 'ziultimate')
			]
		);

		$bar = $pagebreak->add_group(
			'pg_indicator',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> esc_html__('Progress Bar', 'ziultimate'),
				'collapsed' => true
			]
		);

		$bar->add_option(
			'pg_indicator_height',
			[
				'type' 		=> 'slider',
				'min' 		=> 0,
				'max' 		=> 50,
				'step' 		=> 1,
				'default' 	=> 18,
				'content' 	=> 'px',
				'title' 	=> esc_html__('Height', 'ziultimate'),
				'css_style' => [
					[
						'selector' 		=> '{{ELEMENT}} .wpforms-form .wpforms-page-indicator.progress .wpforms-page-indicator-page-progress-wrap',
						'value'    		=> 'height: {{VALUE}}px',
					],
					[
						'selector' 		=> "{{ELEMENT}} .wpforms-form .wpforms-page-indicator.progress .wpforms-page-indicator-page-progress",
						'value' 		=> "height: {{VALUE}}px"
					]
				]
			]
		);

		$bar->add_option(
			'pg_indicator_clr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Color' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .wpforms-form .wpforms-page-indicator.progress .wpforms-page-indicator-page-progress-wrap',
						'value'    => 'background-color: {{VALUE}}',
					],
				],
			]
		);

		$bar->add_option(
			'pg_indicator_aclr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__( 'Active Color', 'ziultimate' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .wpforms-form .wpforms-page-indicator.progress .wpforms-page-indicator-page-progress',
						'value'    => 'background-color: {{VALUE}}!important',
					],
				],
			]
		);


		/**
		 * Circle Indicator
		 */
		$pbcircle = $pagebreak->add_group(
			'pg_circle',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> esc_html__('Circles', 'ziultimate'),
				'collapsed' => true
			]
		);

		$pbcircle->add_option(
			'pb_circle_size',
			[
				'type' 		=> 'slider',
				'min' 		=> 0,
				'max' 		=> 100,
				'step' 		=> 1,
				'default' 	=> 40,
				'content' 	=> 'px',
				'title' 	=> esc_html__('Circle Size', 'ziultimate'),
				'css_style' => [
					[
						'selector' 		=> '{{ELEMENT}} .wpforms-form .wpforms-page-indicator.circles .wpforms-page-indicator-page-number',
						'value'    		=> 'width: {{VALUE}}px',
					],
					[
						'selector' 		=> "{{ELEMENT}} .wpforms-form .wpforms-page-indicator.circles .wpforms-page-indicator-page-number",
						'value' 		=> "height: {{VALUE}}px"
					],
					[
						'selector' 		=> "{{ELEMENT}} .wpforms-form .wpforms-page-indicator.circles .wpforms-page-indicator-page-number",
						'value' 		=> "line-height: {{VALUE}}px"
					]
				]
			]
		);

		$pbcircle->add_option(
			'pb_number_size',
			[
				'type' 		=> 'slider',
				'min' 		=> 0,
				'max' 		=> 100,
				'step' 		=> 1,
				'content' 	=> 'px',
				'title' 	=> esc_html__('Page Number Size', 'ziultimate'),
				'css_style' => [
					[
						'selector' 		=> '{{ELEMENT}} .wpforms-form .wpforms-page-indicator.circles .wpforms-page-indicator-page-number',
						'value'    		=> 'font-size: {{VALUE}}px',
					],
				]
			]
		);

		$pbcircle->add_option(
			'circle_color',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__( 'Circle Color', 'ziultimate' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .wpforms-form .wpforms-page-indicator.circles .wpforms-page-indicator-page-number',
						'value'    => 'background-color: {{VALUE}}'
					],
				],
			]
		);

		$pbcircle->add_option(
			'number_color',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__( 'Number Color', 'ziultimate' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .wpforms-form .wpforms-page-indicator.circles .wpforms-page-indicator-page-number',
						'value'    => 'color: {{VALUE}}'
					],
				],
			]
		);

		$pbcircle->add_option(
			'current_circle_color',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__( 'Current Circle Color', 'ziultimate' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .wpforms-form .wpforms-page-indicator.circles .active .wpforms-page-indicator-page-number',
						'value'    => 'background-color: {{VALUE}}!important'
					],
				],
			]
		);

		$pbcircle->add_option(
			'current_number_color',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__( 'Current Number Color', 'ziultimate' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .wpforms-form .wpforms-page-indicator.circles .active .wpforms-page-indicator-page-number',
						'value'    => 'color: {{VALUE}}'
					],
				],
			]
		);


		/**
		 * Circle Indicator
		 */
		$connector = $pagebreak->add_group(
			'pg_connector',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> esc_html__('Connector', 'ziultimate'),
				'collapsed' => true
			]
		);

		$connector->add_option(
			'pg_connector_height',
			[
				'type' 		=> 'slider',
				'min' 		=> 0,
				'max' 		=> 50,
				'step' 		=> 1,
				'default' 	=> 6,
				'content' 	=> 'px',
				'title' 	=> esc_html__('Height', 'ziultimate'),
				'css_style' => [
					[
						'selector' 		=> '{{ELEMENT}} .wpforms-form .wpforms-page-indicator.connector .wpforms-page-indicator-page-number',
						'value'    		=> 'height: {{VALUE}}px'
					],
				]
			]
		);

		$connector->add_option(
			'pg_connector_clr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Color' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .wpforms-form .wpforms-page-indicator.connector .wpforms-page-indicator-page-number',
						'value'    => 'background-color: {{VALUE}}',
					],
				],
			]
		);

		$connector->add_option(
			'pg_connector_aclr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__( 'Current Color', 'ziultimate' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .wpforms-form .wpforms-page-indicator.connector .active .wpforms-page-indicator-page-number',
						'value'    => 'background-color: {{VALUE}}!important',
					],
					[
						'selector' => '{{ELEMENT}} .wpforms-form .wpforms-page-indicator.connector .active .wpforms-page-indicator-page-triangle',
						'value'    => 'border-top-color: {{VALUE}}!important',
					]
				],
			]
		);
		

		/**
		 * Page Title
		 */
		$pagetitle = $pagebreak->add_group(
			'page_title',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> esc_html__('Page Title', 'ziultimate')
			]
		);

		$this->attach_typography_options( $pagetitle, 'pb_title', "{{ELEMENT}} .wpforms-page-indicator-page-title");

		$pagetitle->add_option(
			'current_title_color',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__( 'Current Title Color', 'ziultimate' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .wpforms-page-indicator-page.active .wpforms-page-indicator-page-title',
						'value'    => 'color: {{VALUE}}'
					],
				],
			]
		);

		$pagetitle->add_option(
			'pg_sep',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__( 'Separator Color', 'ziultimate' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .wpforms-page-indicator-page-title-sep',
						'value'    => 'color: {{VALUE}}'
					],
				],
			]
		);


		/**
		 * Total section
		 */
		$total = $options->add_group(
			'total_group',
			[
				'type' 		=> 'accordion_menu',
				'title' 	=> esc_html__('Total Price', 'ziultimate')
			]
		);

		$this->attach_typography_options( 
			$total, 
			'total_price', 
			'{{ELEMENT}} .wpforms-payment-total',
			['text_align', 'letter_spacing', 'line_height']
		);
    }

	/**
	 * Loaing the CSS
	 */
	public function enqueue_styles() {
		if ( class_exists( 'WPForms_Pro' ) || class_exists( 'WPForms_Lite' ) ) {
			$this->enqueue_editor_style( WPFORMS_PLUGIN_URL . 'pro/assets/css/dropzone.min.css' );
			$this->enqueue_editor_style( WPFORMS_PLUGIN_URL . 'assets/css/wpforms-full.css' );
		}

		$this->enqueue_editor_style( Utils::get_file_url('dist/css/elements/WPForms/editor.css' ) );

		$this->enqueue_element_style( Utils::get_file_url('dist/css/elements/WPForms/frontend.css' ) );
	}

	/**
	 * Loading the scripts
	 * 
	 * @return void
	 */
	public function enqueue_scripts() {
		$this->enqueue_editor_script( Utils::get_file_url( 'dist/js/elements/WPForms/editor.js' ) );
	}

	/**
	 * Adding some attributes
	 * 
	 * @return void
	 */
	public function before_render( $options ) {
		$cb_smart_ui = $options->get_value('cb_smart_ui', false );

		if( $cb_smart_ui ) {
			$this->render_attributes->add( 'wrapper', 'class', 'zu-wpf-cbui' );
		}
	}

	/**
	 * Render form
	 */
	public function render( $options ) {

		$source_type = $options->get_value('source_type', 'static');

		if( $source_type == 'dynamic' ) {
			$wpform = $options->get_value('wpform_dymc');
		} else {
			$wpform = $options->get_value('wp_form', false);
		}

		if( ! $wpform || $wpform <= 0 ) {
			echo '<h5 class="form-missing">' . __("Select a form", 'ziultimate') . '</h5>';

			return;
		}

		$title = $options->get_value('wpform_title', true) ? "true" : "false";
		$desc = $options->get_value('wpform_desc', false) ? "true" : "false";

		echo do_shortcode('[wpforms id='. $wpform .' title="' . $title . '" description="' . $desc . '"]' );
	}

	/**
	 * Fetch all wpforms
	 */
    public function zu_get_wpforms() {
		$options = [
			[
				'id'    => -1, 
				'name'  => esc_html__( 'Select a form', 'ziultimate' )
			]
		];

		if ( class_exists( 'WPForms_Pro' ) || class_exists( 'WPForms_Lite' ) ) {
			$forms = get_posts(array(
				'post_type'      => 'wpforms',
				'posts_per_page' => -1,
			));

			if ( $forms ) {
				foreach ( $forms as $key => $form ) {
					$options[ $key + 1 ]['id'] = $form->ID;
                    $options[ $key + 1 ]['name'] = $form->post_title;
				}
			}
		}

		return $options;
	}

	public function on_register_styles() {
		$this->register_style_options_element(
			'form_title_styles',
			[
				'title'                   => esc_html__( 'Form Title', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} .wpforms-title',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'form_desc_styles',
			[
				'title'                   => esc_html__( 'Form Description', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} .wpforms-description',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'field_label',
			[
				'title'                   => esc_html__( 'Field Label', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} .wpforms-field-label',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'field_sublabel',
			[
				'title'                   => esc_html__( 'Field Sub Label', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} .wpforms-field-sublabel',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'field_desc',
			[
				'title'                   => esc_html__( 'Field Description', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} .wpforms-field-description',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'input_field',
			[
				'title'                   => esc_html__( 'Input Field', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} .wpforms-field-textarea textarea, {{ELEMENT}} .wpforms-form .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]):not([type=range])',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'input_select_field',
			[
				'title'                   => esc_html__( 'Dropdown Field', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} .wpforms-form select',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'input_file_field',
			[
				'title'                   => esc_html__( 'File Upload Field', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} div.wpforms-uploader',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'submit_styles',
			[
				'title'                   => esc_html__( 'Submit Button', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} .wpforms-submit',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'page_button_styles',
			[
				'title'                   => esc_html__( 'Prev/Next Buttons', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} .wpforms-page-button',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'section_divider_styles',
			[
				'title'                   => esc_html__( 'Section Divider Wrapper', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} .wpforms-field-divider',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'section_title_styles',
			[
				'title'                   => esc_html__( 'Section Title', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} .wpforms-field-divider h3',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'section_desc_styles',
			[
				'title'                   => esc_html__( 'Section Description', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} .wpforms-field-divider .wpforms-field-description',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'error_styles',
			[
				'title'                   => esc_html__( 'Validation Error', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} .wpforms-error',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'success_message',
			[
				'title'                   => esc_html__( 'Success Message', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} .wpforms-confirmation-container-full',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);
	}
}