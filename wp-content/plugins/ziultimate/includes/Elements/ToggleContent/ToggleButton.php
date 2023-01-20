<?php
namespace ZiUltimate\Elements\ToggleContent;

use ZiUltimate\UltimateElements;
use ZiUltimate\Utils;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class ToggleButton
 *
 * @package ZiUltimate\Elements
 */
class ToggleButton extends UltimateElements {
	
	public function get_type() {
		return 'zu_toggle_button';
	}

	public function get_name() {
		return __( 'Toggle Button', 'ziultimate' );
	}

	public function get_category() {
		return 'zu-toggle';
	}

	public function get_element_icon() {
		return 'element-button';
	}

	/**
	 * Creating the settings fields
	 * 
	 * @return void
	 */
	public function options( $options ) {

		$options->add_option(
			'icon',
			[
				'type'       => 'icon_library',
				'id'         => 'icon',
				'default'    => [
					'family'  => 'Font Awesome 5 Free Solid',
					'name'    => 'plus',
					'unicode' => 'uf067',
				]
			]
		);

		$options->add_option(
			'aria_label',
			[
				'type' 		=> 'text',
				'title' 	=> esc_html__('Aria Label', 'ziultimate'),
				'dynamic' 	=> [
					'enabled' 	=> true,
				]
			]
		);

		$options->add_option(
			'icon_anim',
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

		$options->add_option(
			'has_button',
			[
				'type' 		=> 'text',
				'default' 	=> 'zu' . self::elVal(),
				'css_class' => 'znpb-checkbox-switch-wrapper__checkbox'
			]
		);

		$options->add_option(
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
						'selector' 	=> ".zu-toggle-title--active {{ELEMENT}}.rotate .zu-toggle-button--icon",
						'value' 	=> 'transform: rotate({{VALUE}}deg)'
					]
				],
				'dependency' => [
					[
						'option' 	=> 'icon_anim',
						'value' 	=> [ 'rotate' ]
					]
				]
			]
		);

		$options->add_option(
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
						'selector' 	=> "{{ELEMENT}} .zu-toggle-button--icon",
						'value' 	=> 'transition-duration: {{VALUE}}s'
					]
				]
			]
		);
	}

	/**
	 * Registering the styles
	 */
	public function on_register_styles() {
		$this->register_style_options_element(
			'icon_styles',
			[
				'title'    => esc_html__( 'Icon Style', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .zu-toggle-button--icon'
			]
		);

		$this->register_style_options_element(
			'actv_icon_styles',
			[
				'title'    => esc_html__( 'Active Icon Style', 'ziultimate' ),
				'selector' => '{{ELEMENT}}.zu-toggle-button--active .zu-toggle-button--icon',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
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
		$this->render_attributes->add( 'wrapper', 'class', 'zu-toggle-button' );
		$this->render_attributes->add( 'wrapper', 'class', $options->get_value( 'icon_anim', 'rotate' ) );

		$arialabel = $options->get_value('aria_label', false);
		if( $arialabel ) {
			$this->render_attributes->add( 'wrapper', 'aria-label', esc_attr( $arialabel ) );
		}

		$this->render_attributes->add( 'wrapper', 'role', 'button' );
	}

	/**
	 * Rendering the layout
	 */
	public function render( $options ) {
		$icon = $options->get_value( 'icon' );
		$anim = $options->get_value( 'icon_anim', 'rotate' );
		$combined_icon_attr = $this->render_attributes->get_combined_attributes( 'icon_styles', [ 'class' => 'zu-toggle-button--icon' ] );

		$this->attach_icon_attributes( 'icon', $icon );
		$this->render_tag(
			'span',
			'icon',
			'',
			$combined_icon_attr
		);
	}
}