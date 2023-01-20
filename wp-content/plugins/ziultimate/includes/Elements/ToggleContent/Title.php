<?php
namespace ZiUltimate\Elements\ToggleContent;

use ZiUltimate\UltimateElements;
use ZiUltimate\Utils;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Title
 *
 * @package ZiUltimate\Elements
 */
class Title extends UltimateElements {
	
	public function get_type() {
		return 'zu_toggle_title';
	}

	public function get_name() {
		return __( 'Title', 'ziultimate' );
	}

	public function get_category() {
		return 'zu-toggle';
	}

	public function get_element_icon() {
		return 'element-accordion';
	}

	public function is_wrapper() {
		return true;
	}

	/**
	 * Creating the settings fields
	 * 
	 * @return void
	 */
	public function options( $options ) {

		$options->add_option(
			'active_by_default',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> esc_html__( 'Active by default?', 'zionbuilder' ),	
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
				'default' 	=> 'no'
			]
		);

		$options->add_option(
			'tabindex',
			[
				'type' 			=> 'text',
				'title' 		=> esc_html__('Tab Index'),
				'default' 		=> 0,
				'placeholder' 	=> 0,
				'dynamic' 		=> [
					'enabled' => true
				]
			]
		);

		$options->add_option(
			'el_valid',
			[
				'type' 		=> 'text',
				'default' 	=> 'zu' . self::elVal(),
				'css_class' => 'znpb-checkbox-switch-wrapper__checkbox'
			]
		);

		$options->add_option(
			'bg_color_section',
			[
				'type' 		=> 'html', 
				'title' 	=> esc_html__('Background Color', 'ziultimate'),
				'content' 	=> '<hr style="border-color: #e8e8e8;border-style:solid;" />'
			]
		);

		$options->add_option(
			'bg_color',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Default', 'zionbuilder' ),
				'sync' 		=> '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.background-color'
			]
		);

		$options->add_option(
			'bg_color_hover',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Hover', 'zionbuilder' ),
				'width' 	=> 50,
				'sync' 		=> '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.:hover.background-color'
			]
		);

		$options->add_option(
			'txt_color_hover',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Hover Text Color', 'zionbuilder' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}}.zu-toggle-title:hover *',
						'value' 	=> 'color: {{VALUE}}'
					]
				]
			]
		);

		$options->add_option(
			'bg_color_active',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Active BG Color', 'zionbuilder' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}}.zu-toggle-title--active',
						'value' 	=> 'background-color: {{VALUE}}'
					]
				]
			]
		);

		$options->add_option(
			'txt_color_active',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Active Text Color', 'zionbuilder' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}}.zu-toggle-title--active *',
						'value' 	=> 'color: {{VALUE}}'
					]
				]
			]
		);

		$options->add_option(
			'width',
			[
				'type'      => 'dynamic_slider',
				'title'     => __( 'Wrapper Width', 'zionbuilder' ),
				'sync' 		=> '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.width',
				'options'     => [
					[
						'unit' => 'px',
						'min'  => 0,
						'max'  => 10000,
						'step' => 10,
					],
					[
						'unit' => 'em',
						'min'  => 0,
						'max'  => 10000,
						'step' => 1,
					],
					[
						'unit' => 'rem',
						'min'  => 0,
						'max'  => 100,
						'step' => 10,
					],
					[
						'unit' => 'vh',
						'min'  => 0,
						'max'  => 100,
						'step' => 10,
					],
					[
						'unit' => '%',
						'min'  => 0,
						'max'  => 100,
						'step' => 10,
					],
					[
						'unit' => 'auto'
					],
					[
						'unit' => 'initial'
					]
				],
				'show_responsive_buttons' => true,
			]
		);

		$options->add_option(
			'hover_td',
			[
				'type' 				=> 'slider',
				'content' 			=> 's',
				'min' 				=> 0,
				'max' 				=> 5,
				'step' 				=> 0.01,
				'default' 			=> 0.2,
				'title' 			=> esc_html__('Hover Transition Duration', 'ziultimate'),
				'css_style' 		=> [
					[
						'selector' 		=> "{{ELEMENT}}.zu-toggle-title",
						'value' 		=> 'transition-duration: {{VALUE}}s'
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
		$this->enqueue_element_style( Utils::get_file_url( 'dist/css/elements/ToggleContent/frontend.css' ) );
	}

	/**
	 * Loading the js files
	 * 
	 * @return void
	 */
	public function enqueue_scripts() {
		$this->enqueue_editor_script( Utils::get_file_url( 'dist/js/elements/ToggleContent/editor.js' ) );
		$this->enqueue_element_script( Utils::get_file_url( 'dist/js/elements/ToggleContent/frontend.js' ) );
	}

	/**
	 * Adding the extra attributes
	 * 
	 * @return void
	 */
	public function before_render( $options ) {
		$ariaexpanded = $ariaselected = 'false';

		$this->render_attributes->add( 'wrapper', 'class', 'zu-toggle-title' );
		
		$active_by_default = $options->get_value('active_by_default', 'no');
		if( $active_by_default == 'yes' ) {
			$ariaexpanded = $ariaselected = 'true';
			$this->render_attributes->add( 'wrapper', 'class', 'zu-toggle-title--active zu-toggle--active' );
		}

		$this->render_attributes->add( 'wrapper', 'role', 'button' );

		$this->render_attributes->add( 'wrapper', 'tabindex', intval( $options->get_value('tabindex', 0) ) );
		$this->render_attributes->add( 'wrapper', 'aria-expanded', $ariaexpanded );
		$this->render_attributes->add( 'wrapper', 'aria-selected', $ariaselected );
	}

	/**
	 * Rendering the layout
	 */
	public function render( $options ) {
		$this->render_children();
	}
}