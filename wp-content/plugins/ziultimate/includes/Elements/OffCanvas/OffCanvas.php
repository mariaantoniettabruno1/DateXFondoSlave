<?php

namespace ZiUltimate\Elements\OffCanvas;

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
 * Class OffCanvas
 *
 * @package ZiUltimate\Elements
 */
class OffCanvas extends UltimateElements {
	
	public function get_type() {
		return 'zu_off_canvas';
	}

	public function get_name() {
		return __( 'Off Canvas', 'ziultimate' );
	}

	public function get_keywords() {
		return [ 'off canvas', 'canvas', 'sliding panel', 'panel' ];
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

	public function is_wrapper() {
		return true;
	}

	/**
	 * Registering the options
	 * 
	 * @return void
	 */
	public function options( $options ) {

		if( ! License::has_valid_license() ) {
			$title = $this->get_name();
			$description = 'You can build the sliding off canvas panel.';
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
			'ocpreview',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> __('Disable Builder Preview', 'ziultimate'),
				'default' 	=> 'no',
				'options' 	=> [
					[
						'id' 	=> 'no',
						'name' 	=> esc_html__('No')
					],
					[
						'id' 	=> 'yes',
						'name' 	=> esc_html__('Yes')
					]
				]
			]
		);

		$options->add_option(
			'trigger_selector',
			[
				'type' 		=> 'text',
				'title' 	=> __('Trigger Selector', 'ziultimate'),
				'dynamic'	=> [
					'enabled' => true
				]
			]
		);

		$options->add_option(
			'ocp_position',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> __('Panel Position', 'ziultimate'),
				'default' 	=> 'right',
				'options' 	=> [
					[
						'name' 	=> esc_html__('Left'),
						'id' 	=> 'left'
					],
					[
						'name' 	=> esc_html__('Right'),
						'id' 	=> 'right'
					],
					[
						'name' 	=> esc_html__('Top'),
						'id' 	=> 'top'
					],
					[
						'name' 	=> esc_html__('Bottom'),
						'id' 	=> 'bottom'
					],
				]
			]
		);

		$options->add_option(
			'panel_height',
			[
				'type' 		=> 'number_unit',
				'units' 	=> StyleOptions::get_units(),
				'default' 	=> '300px',
				'title' 	=> __( 'Panel Height', 'ziultimate' ),
				'sync' 		=> '_styles.zu_off_canvas_panel.styles.%%RESPONSIVE_DEVICE%%.default.height',
				'dependency' => [
					[
						'option' 	=> 'ocp_position',
						'value' 	=> [ 'top', 'bottom' ]
					]
				]
			]
		);

		$options->add_option(
			'panel_td',
			[
				'type' 		=> 'slider',
				'content' 	=> 's',
				'default' 	=> .5,
				'min' 		=> 0,
				'max' 		=> 10,
				'step' 		=> 0.1,
				'title' 	=> __( 'Transition Duration' ),
				'css_style' => [
					[
						'selector' 	=> "{{ELEMENT}} .zu-off-canvas-panel",
						'value' 	=> 'transition-duration: {{VALUE}}s'
					]
				]
			]
		);

		$options->add_option(
			'disable_site_scroll',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> __('Disable Site Scroll', 'ziultimate'),
				'default' 	=> true,
				'layout' 	=> 'inline'
			]
		);

		$options->add_option(
			'push_content',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> __('Push Body Content', 'ziultimate'),
				'default' 	=> false,
				'layout' 	=> 'inline'
			]
		);

		$options->add_option(
			'reveal_panel',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> __('Reveal Panel', 'ziultimate'),
				'description' => __('Panel will automatically slide out on page load.', 'ziultimate'),
				'default' 	=> false,
				'layout' 	=> 'inline'
			]
		);

		$options->add_option(
			'delay',
			[
				'type' 		=> 'slider',
				'content' 	=> 'ms',
				'default' 	=> 1200,
				'min' 		=> 0,
				'max' 		=> 5000,
				'step' 		=> 50,
				'title' 	=> __( 'Delay', 'ziultimate' ),
				'dependency' => [
					[
						'option' 	=> 'reveal_panel',
						'value' 	=> [ true ]
					]
				]
			]
		);


		/**
		 * Group - Backdrop settings
		 */
		$backdrop = $options->add_group(
			'backdrop',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> esc_html__('Backdrop', 'ziultimate')
			]
		);

		$backdrop->add_option(
			'disable_backdrop',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> __('Disable Backdrop', 'ziultimate'),
				'default' 	=> false,
				'layout' 	=> 'inline'
			]
		);

		$backdrop->add_option(
			'backdrop_bg',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Background Color' ),
				'css_style' => [
					[
						'selector' 	=> "{{ELEMENT}} .zu-oc-backdrop",
						'value' 	=> 'background-color: {{VALUE}}'
					]
				]
			]
		);

		$backdrop->add_option(
			'backdrop_td',
			[
				'type' 		=> 'slider',
				'content' 	=> 's',
				'default' 	=> .5,
				'min' 		=> 0,
				'max' 		=> 10,
				'step' 		=> 0.1,
				'title' 	=> __( 'Fade Duration' ),
				'css_style' => [
					[
						'selector' 	=> "{{ELEMENT}} .zu-oc-backdrop",
						'value' 	=> 'transition-duration: {{VALUE}}s'
					]
				]
			]
		);

		/**
		 * Group - Scrollbar settings
		 */
		$sb = $options->add_group(
			'scrollbar',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> esc_html__('Scrollbar', 'ziultimate')
			]
		);

		$sb->add_option(
			'will_customize',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> __('Customize Scrollbar?', 'ziultimate'),
				'default' 	=> false,
				'layout' 	=> 'inline'
			]
		);

		$sb->add_option(
			'sb_width',
			[
				'type' 		=> 'slider',
				'content' 	=> 'px',
				'default' 	=> 6,
				'min' 		=> 0,
				'max' 		=> 20,
				'step' 		=> 1,
				'title' 	=> __( 'Size' ),
				'css_style' => [
					[
						'selector' 	=> "{{ELEMENT}} .zu-off-canvas-panel::-webkit-scrollbar",
						'value' 	=> 'width: {{VALUE}}px'
					]
				],
				'dependency' => [
					[
						'option' 	=> 'will_customize',
						'value' 	=> [ true ]
					]
				]
			]
		);

		$sb->add_option(
			'sb_color',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Bar Color' ),
				"default" 	=> 'rgba(0,0,0,0.3)',
				'css_style' => [
					[
						'selector' 	=> "{{ELEMENT}}",
						'value' 	=> '--sb-light-color: {{VALUE}}'
					]
				],
				'dependency' => [
					[
						'option' 	=> 'will_customize',
						'value' 	=> [ true ]
					]
				]
			]
		);

		$sb->add_option(
			'sb_alt_color',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> __( 'Bar Alt Color' ),
				"default" 	=> 'rgba(0,0,0,0.5)',
				'css_style' => [
					[
						'selector' 	=> "{{ELEMENT}}",
						'value' 	=> '--sb-dark-color: {{VALUE}}'
					]
				],
				'dependency' => [
					[
						'option' 	=> 'will_customize',
						'value' 	=> [ true ]
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
	}

	/**
	 * Registering the style elements
	 * 
	 * @return void
	 */
	public function get_style_elements_for_editor() {
		// Register element style options
		$this->on_register_styles();

		return $this->registered_style_options;
	}

	/**
	 * Loading the styles
	 * 
	 * @return void
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'offcanvas-style', Utils::get_file_url( 'dist/css/elements/OffCanvas/offcanvas.css' ), [], filemtime( Utils::get_file_path( 'dist/css/elements/OffCanvas/offcanvas.css' ) ), 'all' );
	}

	/**
	 * Loading the js files
	 * 
	 * @return void
	 */
	public function enqueue_scripts() {
		$this->enqueue_editor_script( Utils::get_file_url( 'dist/js/elements/OffCanvas/editor.js' ) );
		$this->enqueue_element_script( Utils::get_file_url( 'dist/js/elements/OffCanvas/frontend.js' ) );
	}

	/**
	 * Adding the extra attributes
	 * 
	 * @return void
	 */
	public function before_render( $options ) {
		$pos = $options->get_value( 'ocp_position', 'right' );
		$sb_will_customize = $options->get_value( 'will_customize', false );
		$disable_preview = $options->get_value('ocpreview', 'no');

		$disable_site_scroll = $options->get_value( 'disable_site_scroll', true );
		$this->render_attributes->add( 'wrapper', 'data-ocp-disable-scroll', ( $disable_site_scroll ? 'yes' : 'no' ) );

		$push_content = $options->get_value( 'push_content', false );
		if( $push_content ) {
			$this->render_attributes->add( 'wrapper', 'data-ocp-position', $pos );
			$classes[] = 'zu-push-content';
		}

		$panel_td = $options->get_value( 'panel_td', '0.5' );
		$this->render_attributes->add( 'wrapper', 'data-ocpanel-td', $panel_td * 1000 );

		//* Reveal Panel
		$reveal_panel = $options->get_value( 'reveal_panel', false );
		if( $reveal_panel ) {
			$delay = $options->get_value( 'delay', 1200 );
			$this->render_attributes->add( 'wrapper', 'data-ocp-reveal', 'yes' );
			$this->render_attributes->add( 'wrapper', 'data-ocp-delay-in', $delay );
		} else {
			$this->render_attributes->add( 'wrapper', 'data-ocp-reveal', 'no' );
		}

		//* Trigger Selector
		$trigger_selector = $options->get_value( 'trigger_selector', false );
		if( $trigger_selector ) {
			$this->render_attributes->add( 'wrapper', 'data-trigger-selector', $trigger_selector );
		}

		$classes[] = 'zu-off-canvas';
		$classes[] = 'zu-ocp-' . $pos;
		$classes[] = 'zu-hide-panel';

		if( $sb_will_customize ) {
			$classes[] = 'zu-customize-sb';
		}

		$this->render_attributes->add( 'wrapper', 'class', implode( ' ', $classes) );
	}

	/**
	 * Rendering the layout
	 * 
	 * @return void
	 */
	public function render( $options ) {
		$disable_backdrop = $options->get_value( 'disable_backdrop', false );

		if( ! $disable_backdrop )
			echo '<div class="zu-oc-backdrop"></div>';

		echo '<div class="zu-oc-inner-wrap zu-off-canvas-panel">';

		$this->render_children();

		echo '</div>';
	}

	/**
	 * Registering the styles
	 * 
	 * @return void
	 */
	public function on_register_styles() {
		$this->register_style_options_element(
			'zu_off_canvas_panel',
			[
				'title' 		=> esc_html__( 'Off Canvas Panel', 'ziultimate' ),
				'selector' 		=> '{{ELEMENT}} .zu-off-canvas-panel',
			]
		);
	}
}