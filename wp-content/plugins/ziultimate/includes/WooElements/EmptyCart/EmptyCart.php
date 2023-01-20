<?php
namespace ZiUltimate\WooElements\EmptyCart;

use ZiUltimate\UltimateElements;
use ZiUltimate\Utils;
use ZiUltimate\Admin\License;
use ZionBuilder\Options\BaseSchema;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class EmptyCart
 *
 * @package ZiUltimate\WooElements
 */
class EmptyCart extends UltimateElements {

    public function get_type() {
		return 'zu_empty_cart';
	}

	public function get_name() {
		return __( 'Empty Cart', 'ziultimate' );
	}

	public function get_keywords() {
		return [ 'cart', 'empty', 'empty cart' ];
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
		if( ! License::has_valid_license() ) {
			$title = $this->get_name();
			$description = 'You can clear the cart on single click.';
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
			'button_text',
			[
				'type' 		=> 'text',
				'title' 	=> esc_html__('Button Text', 'ziultimate'),
				'default' 	=> esc_html__('Empty Cart', 'ziultimate'),
				'dynamic' 	=> [
					'enabled' 	=> true
				],
			]
		);

		$options->add_option(
			'link_button',
			[
				'type' 			=> 'custom_selector',
				'title' 		=> esc_html__('Link or Button', 'ziultimate'),
				'default' 		=> 'button',
				'options' 		=> [
					[
						'name' 		=> esc_html__('Button'),
						'id' 		=> 'button'
					],
					[
						'name' 		=> esc_html__('Link'),
						'id' 		=> 'link'
					]
				]
			]
		);

		$options->add_option(
			'rel',
			[
				'type' 		=> 'select',
				'title' 	=> esc_html__('Rel', 'ziultimate'),
				'default' 	=> 'none',
				'width' 	=> 50,
				'options' 	=> [
					[
						'name' 	=> esc_html__('None', 'ziultimate'),
						'id' 	=> 'none'
					],
					[
						'name' 	=> esc_html__('nofollow', 'ziultimate'),
						'id' 	=> 'nofollow'
					],
					[
						'name' 	=> esc_html__('noopener', 'ziultimate'),
						'id' 	=> 'noopener'
					],
					[
						'name' 	=> esc_html__('noreferrer', 'ziultimate'),
						'id' 	=> 'noreferrer'
					]
				]
			]
		);

		$options->add_option(
			'redirect_link',
			[
				'type' 		=> 'select',
				'title' 	=> esc_html__('Redirection', 'ziultimate'),
				'description' => esc_html__( 'Redirect the customer after clearing the cart.', 'ziultimate' ),
				'default' 	=> 'none',
				'width' 	=> 50,
				'options' 	=> [
					[
						'name' 	=> esc_html__( 'None', 'ziultimate' ),
						'id' 	=> 'none'
					],
					[
						'name' 	=> esc_html__( 'Redirect URL', 'ziultimate' ),
						'id' 	=> 'custom'
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
						'option' 	=> 'redirect_link',
						'value' 	=> [ 'custom' ]
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
						'selector' => '{{ELEMENT}}',
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
				'title' 		=> esc_html__( 'Font size', 'zionbuilder' ),
				'description' 	=> esc_html__( 'The font size option sets the size of the font in various units', 'zionbuilder' ),
				'type' 			=> 'number_unit',
				'min' 			=> 0,
				'width' 		=> 50,
				'units' 		=> BaseSchema::get_units(),
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
						'selector' 	=> "{{ELEMENT}} .zu-empty-cart-button:hover .zu-empty-cart-button__icon",
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
						'value' 	=> 'zu-empty-cart-button--icon-{{VALUE}}'
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
		$this->enqueue_element_style( Utils::get_file_url( 'dist/css/elements/EmptyCart/frontend.css' ) );
	}

	/**
	 * Loading the scripts
	 * 
	 * @return void
	 */
	public function enqueue_scripts() {
		$this->enqueue_editor_script( Utils::get_file_url( 'dist/js/elements/EmptyCart/editor.js' ) );
		$this->enqueue_element_script( Utils::get_file_url( 'dist/js/elements/EmptyCart/frontend.js' ) );
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
				'selector'   => '{{ELEMENT}} .zu-empty-cart-button',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'icon_styles',
			[
				'title'      => esc_html__( 'Icon styles', 'ziultimate' ),
				'selector'   => '{{ELEMENT}} .zu-empty-cart-button__icon',
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
		$icon_html 			= '';
		$button_text_html 	= '';
		$button_text 		= $options->get_value('button_text', 'Empty Cart');
		$icon 				= $options->get_value( 'icon', false );
		$icon_pos 			= $options->get_value( 'icon_pos', false );
		$link 				= $options->get_value( 'page_link', false );
		$button 			= $options->get_value( 'link_button', 'button' );

		$url_args['zu_empty_cart'] = 'yes';
		$redirect_link = $options->get_value('redirect_link', 'none');

		if( $redirect_link == 'custom' && ! empty( $link['link'] ) )
		{
			$url_args['zu_redirect'] = 'yes';
			$url = esc_url( add_query_arg( $url_args, $link['link'] ) );
		} else {
			$url = esc_url( add_query_arg( $url_args ) );
		}

		$combined_button_attr = [
			'href' 	=> $url,
			'class' => "zu-empty-cart-button" . ( ( $button == 'button') ? ' button' : '' ) . ( empty( $icon_pos ) ? ' zu-empty-cart-button--icon-left' : '' ) .
						( (!self::isBuilderEditor() && !is_null( WC()->cart ) && WC()->cart->is_empty() ) ? ' hide-when-empty' : '' ),
			'role' 	=> 'button',
			'aria-label' => esc_attr( $button_text )
		];

		$rel = $options->get_value('rel', 'none' );
		if( $rel !== 'none' ) { $combined_button_attr['rel'] = $rel; }

		$combined_button_attr = $this->render_attributes->get_combined_attributes( 'button_styles', $combined_button_attr );
		$combined_icon_attr   = $this->render_attributes->get_combined_attributes( 'icon_styles', [ 'class' => 'zu-empty-cart-button__icon' ] );

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
					'class' => 'zu-empty-cart-button__text',
				]
			);
		}

		$this->render_tag(
			'a',
			'button',
			[ $icon_html, $button_text_html ],
			$combined_button_attr
		);
	}
}