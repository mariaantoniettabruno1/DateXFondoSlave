<?php
namespace ZiUltimate\Elements\UltimateTabs;

use ZionBuilder\Plugin;
use ZiUltimate\UltimateElements;
use ZiUltimate\Utils;
use ZionBuilder\Options\BaseSchema;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class UltimateTabs
 *
 * @package ZiUltimate\Elements
 */
class UltimateTabs extends UltimateElements {

	public function get_type() {
		return 'zu_tabs';
	}

	public function get_name() {
		return __( 'Ultimate Tabs', 'ziultimate' );
	}

	public function get_keywords() {
		return [ 'tab', 'folder', 'navigation', 'tabbar', 'steps' ];
	}

	public function get_category() {
		return $this->zu_elements_category();
	}

	public function get_element_icon() {
		return 'element-tabs';
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
			'zu_tabs',
			[
				'type'         => 'child_adder',
				'title'        => __( 'Tabs', 'zionbuilder' ),
				'child_type'   => 'zu_tabs_item',
				'item_name'    => 'title',
				'min'          => 1,
				'add_template' => [
					'element_type' => 'zu_tabs_item',
					'options'      => [
						'title' => __( 'Tab', 'zionbuilder' ),
					],
				],
				'default'      => [
					[
						'element_type' => 'zu_tabs_item',
						'options'      => [
							'title' => sprintf( '%s 1', __( 'Tab', 'zionbuilder' ) ),
						],
					],
					[
						'element_type' => 'zu_tabs_item',
						'options'      => [
							'title' => sprintf( '%s 2', __( 'Tab', 'zionbuilder' ) ),
						],
					],
					[
						'element_type' => 'zu_tabs_item',
						'options'      => [
							'title' => sprintf( '%s 3', __( 'Tab', 'zionbuilder' ) ),
						],
					],
				],
			]
		);

		$options->add_option(
			'layout',
			[
				'type'             => 'select',
				'default'          => '',
				'title'            => __( 'Layout', 'zionbuilder' ),
				'options'          => [
					[
						'name' => __( 'Horizontal', 'zionbuilder' ),
						'id'   => '',
					],
					[
						'name' => __( 'Vertical', 'zionbuilder' ),
						'id'   => 'vertical',
					]
				],
				'render_attribute' => [
					[
						'attribute' => 'class',
						'value'     => 'zu-el-tabs--{{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'tabs_width',
			[
				'type' 		=> 'number_unit',
				'default' 	=> '30%',
				'title' 	=> esc_html__( 'Tabs width', 'ziultimate' ),
				'units' 	=> BaseSchema::get_units(),
				'width' 	=> 50,
				'dependency' => [
					[
						'option' 	=> 'layout',
						'value' 	=> [ 'vertical' ]
					],
				],
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}}.zu-el-tabs--vertical .zu-el-tabs-nav',
						'value' 	=> 'width: {{VALUE}}'
					]
				],
				'show_responsive_buttons' => true
			]
		);

		$options->add_option(
			'content_width',
			[
				'type' 		=> 'number_unit',
				'default' 	=> '70%',
				'title' 	=> esc_html__( 'Content width', 'ziultimate' ),
				'units' 	=> BaseSchema::get_units(),
				'width' 	=> 50,
				'dependency' => [
					[
						'option' 	=> 'layout',
						'value' 	=> [ 'vertical' ]
					],
				],
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}}.zu-el-tabs--vertical .zu-el-tabs-content',
						'value' 	=> 'width: {{VALUE}}'
					]
				],
				'show_responsive_buttons' => true
			]
		);

		$options->add_option(
			'icon_pos',
			[
				'type'             => 'custom_selector',
				'default'          => 'row',
				'title'            => __( 'Tabs icon position', 'ziultimate' ),
				'options'          => [
					[
						'name' => __( 'Left', 'zionbuilder' ),
						'id'   => 'row',
					],
					[
						'name' => __( 'Right', 'zionbuilder' ),
						'id'   => 'row-reverse',
					],
					[
						'name' => __( 'Top', 'zionbuilder' ),
						'id'   => 'column',
					],
					[
						'name' => __( 'Bottom', 'zionbuilder' ),
						'id'   => 'column-reverse',
					]
				],
				'sync' 				=> '_styles.inner_content_styles_title.styles.%%RESPONSIVE_DEVICE%%.default.flex-direction'
			]
		);

		/**
		 * Accordion Settings
		 */
		$acrd = $options->add_group(
			'acrd',
			[
				'type' 		=> 'panel_accordion', 
				'title' 	=> esc_html__('Accordion Config', 'ziultimate'),
				'collapsed' => false
			]
		);

		$acrd->add_option(
			'collapse_breakpoint',
			[
				'type'        => 'slider',
				'title'       => esc_html__( 'Enable accordion effect at', 'zionbuilder' ),
				'description' => esc_html__( 'Tabs layout will be displayed before the specified value (in pixels).', 'zionbuilder' ),
				'min'         => 0,
				'max'         => 2560,
				'default'     => 768,
			]
		);

		$acrd->add_option(
			'active_by_default',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> esc_html__( 'First tab active by default?', 'zionbuilder' ),	
				'options' 	=> [
					[
						'id'	=> 'yes',
						'name' 	=> __('Yes', "zionbuilder"),
					],
					[
						'id'	=> 'no',
						'name' 	=> __('No', "zionbuilder"),
					]
				],
				'default' 	=> 'yes'
			]
		);

		$acrd->add_option(
			'td_acrd',
			[
				'type' 		=> 'slider',
				'content' 	=> 'ms',
				'min' 		=> 0,
				'max' 		=> 10000,
				'step' 		=> 50,
				'default' 	=> 700,
				'title' 	=> esc_html__('Transition Duration for Toggle')
			]
		);

		$acrd->add_option(
			'toggle_icon',
			[
				'type'       => 'icon_library',
				'id'         => 'toggle_icon',
				'title' 	 => esc_html__('Toggle action icon', 'ziultimate'),
				'default'    => [
					'family'  => 'Font Awesome 5 Free Solid',
					'name'    => 'plus',
					'unicode' => 'uf067',
				]
			]
		);

		$acrd->add_option(
			'arrow_icon_anim',
			[
				'type' 		=> 'select',
				'title' 	=> esc_html__('Animation', 'ziultimate'),
				'options' 	=> [
					[
						'name' 	=> esc_html__('Rotate'),
						'id' 	=> 'rotate'
					],

					[
						'name' 	=> esc_html__('Vertical Flip', 'ziultimate'),
						'id' 	=> 'flip'
					]
				],
				'default' 	=> 'rotate'
			]
		);

		$acrd->add_option(
			'anim_rotate',
			[
				'type' 		=> 'slider',
				'content' 	=> 'deg',
				'min' 		=> -180,
				'max' 		=> 180,
				'step' 		=> 5,
				'default' 	=> 45,
				'title' 	=> esc_html__('Rotate(Active State)', 'zionbuilder'),
				'css_style' => [
					[
						'selector' 	=> "{{ELEMENT}} .zu-el-tabs-nav--active.rotate .zu-el-acrd-toggle--icon:before",
						'value' 	=> 'transform: rotate({{VALUE}}deg)'
					]
				],
				'dependency' => [
					[
						'option' 	=> 'arrow_icon_anim',
						'value' 	=> [ 'rotate' ]
					]
				]
			]
		);

		$acrd->add_option(
			'anim_duration',
			[
				'type' 		=> 'slider',
				'content' 	=> 's',
				'min' 		=> 0,
				'max' 		=> 10,
				'step' 		=> 0.1,
				'default' 	=> 0.3,
				'title' 	=> esc_html__('Transition Duration', 'zionbuilder'),
				'css_style' => [
					[
						'selector' 	=> "{{ELEMENT}} .zu-el-acrd-toggle--icon:before",
						'value' 	=> 'transition-duration: {{VALUE}}s'
					]
				]
			]
		);
	}

	public function on_register_styles() {
		$this->register_style_options_element(
			'inner_content_styles_title',
			[
				'title'    => esc_html__( 'Tab title styles', 'zionbuilder' ),
				'selector' => '{{ELEMENT}} .zu-el-tabs-nav-title',
			]
		);

		$this->register_style_options_element(
			'inner_content_styles_active_title',
			[
				'title'                   => esc_html__( 'Active tab title styles', 'zionbuilder' ),
				'selector'                => '{{ELEMENT}} .zu-el-tabs-nav-title.zu-el-tabs-nav--active',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'inner_content_styles_sub_title',
			[
				'title'    => esc_html__( 'Tab sub title styles', 'zionbuilder' ),
				'selector' => '{{ELEMENT}} .tabs-sub-title',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'inner_content_styles_active_sub_title',
			[
				'title'                   => esc_html__( 'Active tab sub title styles', 'zionbuilder' ),
				'selector'                => '{{ELEMENT}} .zu-el-tabs-nav--active .tabs-sub-title',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'icon_styles',
			[
				'title'    => esc_html__( 'Tab icon styles', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .zu-el-tabs--icon'
			]
		);

		$this->register_style_options_element(
			'actv_icon_styles',
			[
				'title'    => esc_html__( 'Active tab icon styles', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .zu-el-tabs-nav--active .zu-el-tabs--icon',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'inner_content_styles_content_outer_wrapper',
			[
				'title'    => esc_html__( 'Content outer wrapper styles', 'zionbuilder' ),
				'selector' => '{{ELEMENT}} .zu-el-tabs-content',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'inner_content_styles_content',
			[
				'title'    => esc_html__( 'Content styles', 'zionbuilder' ),
				'selector' => '{{ELEMENT}} .zu-el-tabs--content',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'inner_content_styles_acrd_title',
			[
				'title'    => esc_html__( 'Title styles - Accordion', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .zu-el-acrds-btn-title',
			]
		);

		$this->register_style_options_element(
			'inner_content_styles_acrd_actv_title',
			[
				'title'    => esc_html__( 'Active title styles - Accordion', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .zu-el-acrds-btn-title.zu-el-tabs-nav--active',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'toggle_icon_styles',
			[
				'title'    => esc_html__( 'Toggle icon styles - Accordion', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .zu-el-acrd-toggle--icon'
			]
		);

		$this->register_style_options_element(
			'toggle_actv_icon_styles',
			[
				'title'    => esc_html__( 'Active toggle icon styles - Accordion', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .zu-el-tabs-nav--active .zu-el-acrd-toggle--icon',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);
	}

	/**
	 * Enqueue element scripts for both frontend and editor
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$this->enqueue_editor_script( Utils::get_file_url( 'dist/js/elements/UltimateTabs/editor.js' ) );
		$this->enqueue_element_script( Utils::get_file_url( 'dist/js/elements/UltimateTabs/frontend.js' ) );
		wp_enqueue_script( 'ultimate-tabs', Utils::get_file_url( 'dist/js/elements/UltimateTabs/ultimate.tabs.js' ), array('jquery'), '1.0.1', true );
	}

	public function enqueue_styles() {
		$this->enqueue_element_style( Utils::get_file_url( 'dist/css/elements/UltimateTabs/frontend.css' ) );
	}

	/**
	 * Renders the element based on options
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		$tabs               = $this->get_children();
		$tab_links          = $acrd_links = [];
		$title_attributes   = $this->render_attributes->get_attributes_as_string( 'inner_content_styles_title' );
		$acrd_title_attributes   = $this->render_attributes->get_attributes_as_string( 'inner_content_styles_acrd_title' );

		$toggle_icon = $options->get_value( 'toggle_icon', false );
		$anim = $options->get_value( 'arrow_icon_anim', 'rotate' );
		$combined_icon_attr = $this->render_attributes->get_combined_attributes( 'toggle_icon_styles', [ 'class' => 'zu-el-acrd-toggle--icon ' . $anim ] );
		$toggle_icon_html = '';
		if( ! empty( $toggle_icon ) ) {
			$this->attach_icon_attributes( 'toggle_icon', $toggle_icon );
			$toggle_icon_html = $this->get_render_tag(
				'span',
				'toggle_icon',
				'',
				$combined_icon_attr
			);
		}

		foreach ( $tabs as $key => $tab_data ) {
			$title    	= isset( $tab_data['options']['title'] ) ? $tab_data['options']['title'] : '';
			$sub_title   = isset( $tab_data['options']['subtitle'] ) ? '<span class="tabs-sub-title">' . $tab_data['options']['subtitle'] . '</span>' : '';
			$active   	= $key === 0 ? 'zu-el-tabs-nav--active' : '';
			$selected 	= $key === 0 ? 'aria-selected="true"' : '';
			$tabindex 	= $key === 0 ? 'tabindex="0"' : 'tabindex="-1"';

			$icon_html = '';
			$icon = isset( $tab_data['options']['icon'] ) ? $tab_data['options']['icon'] : '';
			$combined_icon_attr = $this->render_attributes->get_combined_attributes( 'icon_styles', [ 'class' => 'zu-el-tabs--icon' ] );
			if( ! empty( $icon ) ) {
				$this->attach_icon_attributes( 'icon', $icon );
				$icon_html = $this->get_render_tag(
					'span',
					'icon',
					'',
					$combined_icon_attr
				);
			}

			$tab_links[] = sprintf( '<li role="tab" %s %s class="zu-el-tabs-nav-title %s" %s>%s<span class="title-text">%s%s</span></li>', $selected, $tabindex, esc_attr( $active ), $title_attributes, $icon_html, wp_kses_post( $title ), wp_kses_post( $sub_title ) );
			$acrd_links[] = sprintf( '<div role="tab" %s %s class="zu-el-acrds-btn-title %s" %s>%s<span class="title-text" itemprop="heading">%s%s</span>%s</div>', $selected, $tabindex, esc_attr( $active ), $acrd_title_attributes, $icon_html, wp_kses_post( $title ), wp_kses_post( $sub_title ), $toggle_icon_html );
		} ?>
		<ul class="zu-el-tabs-nav" role="tablist">
			<?php
				// All output is already escaped
				echo implode( '', $tab_links );
			?>
		</ul>
		<div class='zu-el-tabs-content' <?php echo 'data-toggle-speed="'. $options->get_value('td_acrd', 700) . '"'; echo 'data-collapse-breakpoint="' . $options->get_value('collapse_breakpoint', 768) .'"'; ?>>
			<?php
			foreach ( $tabs as $index => $element_data ) {
					echo $acrd_links[ $index ];

				Plugin::$instance->renderer->render_element(
					$element_data,
					[
						'active' => $index === 0,
					]
				);
			}
			?>
		</div>
		<?php
	}
}