<?php
namespace ZiUltimate\WooElements\ContinueShopping;

use ZiUltimate\UltimateElements;
use ZiUltimate\Utils;
use ZiUltimate\Admin\License;
use ZionBuilder\Options\BaseSchema;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class ContinueShopping
 *
 * @package ZiUltimate\WooElements
 */
class ContinueShopping extends UltimateElements {

    public function get_type() {
		return 'zu_continue_shopping';
	}

	public function get_name() {
		return __( 'Continue Shopping', 'ziultimate' );
	}

	public function get_keywords() {
		return [ 'continue', 'shopping' ];
	}

	/*public function get_label() {
		return [
			'text'  => $this->get_label_text(),
			'color' => $this->get_label_color(),
		];
	}*/

	public function get_element_icon() {
		return 'element-button';
	}

	public function get_category() {
		return $this->zuwoo_elements_category();
	}

	public function options( $options ) 
	{
		$options->add_option(
			'note',
			[
				'type' 		=> 'html',
				'title' 	=> esc_html__('Note:', 'ziultimate'),
				'content' 	=> '<p>' . __("Enter the CSS class name or ID of this element in the off-canvas's selector field and panel will close after clicking on this button.", 'ziultimate') . '</p>'
			]
		);

		$options->add_option(
			'button_text',
			[
				'type' 		=> 'text',
				'title' 	=> esc_html__('Button Text', 'ziultimate'),
				'default' 	=> esc_html__('Keep Shopping', 'ziultimate'),
				'dynamic' 	=> [
					'enabled' 	=> true
				],
			]
		);

		$options->add_option(
			'button_cta',
			[
				'type' 		=> 'select',
				'title' 	=> esc_html__('Button Action', 'ziultimate'),
				'default' 	=> 'none',
				'options' 	=> [
					[
						'name' 	=> esc_html__( 'None', 'ziultimate' ),
						'id' 	=> 'none'
					],
					[
						'name' 	=> esc_html__( 'Redirect to URL', 'ziultimate' ),
						'id' 	=> 'link'
					]
				]
			]
		);

		$options->add_option(
			'page_link',
			[
				'type' 			=> 'link',
				'title' 		=> esc_html__( 'Link', 'ziultimate' ),
				'attributes' => false,
				'dependency' 	=> [
					[
						'option' 	=> 'button_cta',
						'value' 	=> [ 'link' ]
					]
				]
			]
		);

		$styling = $options->add_group(
			'styling_group',
			[
				'type' 		=> 'accordion_menu',
				'title' 	=> esc_html__( 'Button Styling', 'ziultimate' ),
			]
		);

		$styling->add_option(
			'button_width',
			[
				'type' 					=> 'dynamic_slider',
				'default_step' 			=> 1,
				'default_shift_step' 	=> 5,
				'title' 				=> esc_html__( 'Width' ),
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
						'selector' => '{{ELEMENT}} .zu-continue-shopping-button',
						'value'    => 'width: {{VALUE}}',
					],
				],
			]
		);

		$styling->add_option(
			'button_bg',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__( 'Background' ),
				'width' 	=> 50,
				'sync' 		=> '_styles.button_styles.styles.%%RESPONSIVE_DEVICE%%.default.background-color'
			]
		);

		$styling->add_option(
			'button_hbg',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__( 'Hover Background' ),
				'width' 	=> 50,
				'sync' 		=> '_styles.button_styles.styles.%%RESPONSIVE_DEVICE%%.:hover.background-color'
			]
		);

		$styling->add_option(
			'text_color',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__( 'Text Color' ),
				'width' 	=> 50,
				'sync' 		=> '_styles.button_styles.styles.%%RESPONSIVE_DEVICE%%.default.color'
			]
		);

		$styling->add_option(
			'text_hcolor',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__( 'Text Hover Color' ),
				'width' 	=> 50,
				'sync' 		=> '_styles.button_styles.styles.%%RESPONSIVE_DEVICE%%.:hover.color'
			]
		);

		$styling->add_option(
			'font-size',
			[
				'title'       	=> esc_html__( 'Font size', 'zionbuilder' ),
				'description' 	=> esc_html__( 'The font size option sets the size of the font in various units', 'zionbuilder' ),
				'type'        	=> 'number_unit',
				'width' 		=> 50,
				'min'         	=> 0,
				'units'       	=> BaseSchema::get_units(),
				'sync' 			=> '_styles.button_styles.styles.%%RESPONSIVE_DEVICE%%.default.font-size'
			]
		);

		$styling->add_option(
			'text-transform',
			[
				'type' 			=> 'custom_selector',
				'title' 		=> esc_html__( 'Text Transform', 'zionbuilder' ),
				'columns' 		=> 3,
				'width' 		=> 50,
				'options' 		=> [
					[
						'id'   => 'uppercase',
						'icon' => 'uppercase',
						'name' => esc_html__( 'uppercase', 'zionbuilder' ),
					],
					[
						'id'   => 'lowercase',
						'icon' => 'lowercase',
						'name' => esc_html__( 'lowercase', 'zionbuilder' ),
					],
					[
						'id'   => 'capitalize',
						'icon' => 'capitalize',
						'name' => esc_html__( 'capitalize', 'zionbuilder' ),
					],
				],
				'sync' 			=> '_styles.button_styles.styles.%%RESPONSIVE_DEVICE%%.default.text-transform'
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

		$icon = $options->add_group(
			'icon_group',
			[
				'type' 		=> 'accordion_menu',
				'title' 	=> esc_html__( 'Icon Settings', 'ziultimate' ),
			]
		);

		$icon->add_option(
			'icon',
			[
				'type'        => 'icon_library',
				'title'       => __( 'Icon', 'zionbuilder' ),
				'description' => __( 'Choose an icon', 'zionbuilder' ),
			]
		);

		$icon->add_option(
			'icon_color',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__( 'Color' ),
				'width' 	=> 33.33,
				'sync' 		=> '_styles.icon_styles.styles.%%RESPONSIVE_DEVICE%%.default.color'
			]
		);

		$icon->add_option(
			'icon_hcolor',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__( 'Hover Color' ),
				'width' 	=> 33.33,
				'sync' 		=> '_styles.icon_styles.styles.%%RESPONSIVE_DEVICE%%.:hover.color',
				'css_style' => [
					[
						'selector' 	=> "{{ELEMENT}} .zu-continue-shopping-button:hover .zu-continue-shopping-button__icon",
						'value' 	=> 'color: {{VALUE}}'
					]
				]
			]
		);

		$icon->add_option(
			'icon_size',
			[
				'title' 	=> esc_html__( 'Size', 'zionbuilder' ),
				'type' 		=> 'number_unit',
				'width' 	=> 33.33,
				'units' 	=> BaseSchema::get_units(),
				'sync' 		=> '_styles.icon_styles.styles.%%RESPONSIVE_DEVICE%%.default.font-size',
			]
		);

		$icon->add_option(
			'icon_pos',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> esc_html__( 'Position', 'zionbuilder' ),
				'options' 	=> [
					[
						'name' 	=> esc_html__('Left', 'ziultimate'),
						'id' 	=> 'left'
					],
					[
						'name' 	=> esc_html__('Right', 'ziultimate'),
						'id' 	=> 'right'
					],
					[
						'name' 	=> esc_html__('Top', 'ziultimate'),
						'id' 	=> 'top'
					],
					[
						'name' 	=> esc_html__('Bottom', 'ziultimate'),
						'id' 	=> 'bottom'
					]
				],
				'render_attribute' => [
					[
						'tag_id' 	=> 'button_styles',
						'attribute' => 'class',
						'value' 	=> 'zu-continue-shopping-button--icon-{{VALUE}}'
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
		$this->enqueue_element_style( Utils::get_file_url( 'dist/css/elements/ContinueShopping/frontend.css' ) );
	}

	/**
	 * Loading the scripts
	 * 
	 * @return void
	 */
	public function enqueue_scripts() {
		$this->enqueue_editor_script( Utils::get_file_url( 'dist/js/elements/ContinueShopping/editor.js' ) );
		$this->enqueue_element_script( Utils::get_file_url( 'dist/js/elements/ContinueShopping/frontend.js' ) );
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
				'title'      => esc_html__( 'Button styles', 'ziultimate' ),
				'selector'   => '{{ELEMENT}} .zu-continue-shopping-button',
				'render_tag' => 'button_styles',
			]
		);

		$this->register_style_options_element(
			'icon_styles',
			[
				'title'      => esc_html__( 'Icon styles', 'ziultimate' ),
				'selector'   => '{{ELEMENT}} .zu-continue-shopping-button__icon',
				'render_tag' => 'icon',
			]
		);
	}

	protected function can_render() {
		if( ! License::has_valid_license() )
			return;

		return true;
	}

	public function render( $options )
	{
		$html_tag 			= 'button';
		$icon_html 			= '';
		$button_text_html 	= '';
		$button_text 		= $options->get_value( 'button_text', 'Keep Shopping' );
		$button_cta 		= $options->get_value('button_cta', 'none');
		$icon 				= $options->get_value( 'icon', false );
		$icon_pos 			= $options->get_value( 'icon_pos', false );
		$link 				= $options->get_value( 'page_link', false );

		$combined_button_attr = $this->render_attributes->get_combined_attributes( 'button_styles', [ 'class' => "zu-continue-shopping-button button" . ( empty( $icon_pos ) ? ' zu-continue-shopping-button--icon-left' : '' ) ] );
		$combined_icon_attr   = $this->render_attributes->get_combined_attributes( 'icon_styles', [ 'class' => 'zu-continue-shopping-button__icon' ] );

		if( $button_cta == 'link' && ! empty( $link['link'] ) ) {
			$this->attach_link_attributes( 'button', $link );
			$html_tag = 'a';
		}

		if ( ! empty( $icon ) ) {
			$this->attach_icon_attributes( 'icon', $icon );
			$icon_html = $this->get_render_tag(
				'span',
				'icon',
				'',
				$combined_icon_attr
			);
		}

		if ( ! empty( $button_text ) ) {
			$button_text_html = $this->get_render_tag(
				'span',
				'button_text',
				$button_text,
				[
					'class' => 'zu-continue-shopping-button__text',
				]
			);
		}

		$this->render_tag(
			$html_tag,
			'button',
			[ $icon_html, $button_text_html ],
			$combined_button_attr
		);
	}
}