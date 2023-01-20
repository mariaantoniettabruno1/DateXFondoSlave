<?php
namespace ZiUltimate\Elements\ToggleContent;

use ZiUltimate\UltimateElements;
use ZiUltimate\Utils;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Content
 *
 * @package ZiUltimate\Elements
 */
class Content extends UltimateElements {
	
	public function get_type() {
		return 'zu_toggle_content';
	}

	public function get_name() {
		return __( 'Toggle Content', 'ziultimate' );
	}

	public function get_category() {
		return 'zu-toggle';
	}

	public function get_element_icon() {
		return 'element-text';
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
			'preview',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> __( 'Builder Preview', 'zionbuilder' ),
				'default' 	=> 'block',
				'options' 	=>[
					[
						'id' 	=> 'block',
						'name' 	=> esc_html__('Expand')
					],
					[
						'id' 	=> 'none',
						'name' 	=> esc_html__('Collapse')
					]
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
			'speed',
			[
				'type' 				=> 'slider',
				'content' 			=> 'ms',
				'min' 				=> 0,
				'max' 				=> 10000,
				'step' 				=> 100,
				'default' 			=> 700,
				'title' 			=> esc_html__('Sliding Speed', 'ziultimate'),
			]
		);

		$options->add_option(
			'bg_color',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Background Color', 'zionbuilder' ),
				'sync' 		=> '_styles.content_styles.styles.%%RESPONSIVE_DEVICE%%.default.background-color'
			]
		);

		$options->add_option(
			'width',
			[
				'type'      => 'dynamic_slider',
				'title'     => __( 'Wrapper Width', 'zionbuilder' ),
				'sync' 		=> '_styles.content_styles.styles.%%RESPONSIVE_DEVICE%%.default.width',
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

		$options->add_group(
			'padding',
			[
				'type'                    => 'dimensions',
				'title'                   => __( 'Padding', 'zionbuilder' ),
				'min'                     => 0,
				'max'                     => 99999,
				'sync'                    => '_styles.content_styles.styles.%%RESPONSIVE_DEVICE%%.default',
				'show_responsive_buttons' => true,
				'default' 					=> [
					[
						'default' 	=> '25px'
					]
				],
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

		$options->add_option(
			'mrg_top',
			[
				'type' 		=> 'number_unit',
				'title' 	=> __( 'Margin Top', 'zionbuilder' ),
				'width' 	=> 50,
				'units' 	=> [
					'px',
					'rem',
					'pt',
					'vh',
					'%',
				],
				'sync' 		=> '_styles.content_styles.styles.%%RESPONSIVE_DEVICE%%.default.margin-top',
				'show_responsive_buttons' => true
			]
		);

		$options->add_option(
			'mrg_btm',
			[
				'type' 		=> 'number_unit',
				'title' 	=> __( 'Margin Bottom', 'zionbuilder' ),
				'width' 	=> 50,
				'units' 	=> [
					'px',
					'rem',
					'pt',
					'vh',
					'%',
				],
				'sync' 		=> '_styles.content_styles.styles.%%RESPONSIVE_DEVICE%%.default.margin-bottom',
				'show_responsive_buttons' => true
			]
		);
	}

	/**
	 * Registering the styles
	 */
	public function on_register_styles() {
		$this->register_style_options_element(
			'content_styles',
			[
				'title'    	=> esc_html__( 'Content Styles', 'ziultimate' ),
				'selector' 	=> '{{ELEMENT}} .zu-toggle-content--inner',
				'render_tag' => 'content_inner',
			]
		);
	}

	/**
	 * Loading the js files
	 * 
	 * @return void
	 */
	public function enqueue_scripts() {
		$this->enqueue_editor_script( Utils::get_file_url( 'dist/js/elements/ToggleContent/editor.js' ) );
	}

	/**
	 * Adding the extra attributes
	 * 
	 * @return void
	 */
	public function before_render( $options ) {
		$this->render_attributes->add( 'wrapper', 'class', 'zu-toggle-content' );
		$this->render_attributes->add( 'wrapper', 'data-toggle-speed', $options->get_value('speed', 700) );
	}

	/**
	 * Rendering the layout
	 */
	public function render( $options ) {
		$this->render_tag(
			'div',
			'content_inner',
			$this->get_children_for_render(),
			$this->render_attributes->get_combined_attributes( 'content_styles', [ 'class' => 'zu-toggle-content--inner' ] )
		);
	}
}