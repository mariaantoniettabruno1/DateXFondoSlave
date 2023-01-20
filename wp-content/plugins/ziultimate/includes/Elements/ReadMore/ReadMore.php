<?php

namespace ZiUltimate\Elements\ReadMore;

use ZiUltimate\UltimateElements;
use ZiUltimate\Utils;
use ZiUltimate\Admin\License;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class ReadMore
 *
 * @package ZiUltimate\Elements
 */
class ReadMore extends UltimateElements {
	public function get_type() {
		return 'zu_read_more';
	}

	public function get_name() {
		return __( 'Read More/Less', 'ziultimate' );
	}

	public function get_keywords() {
		return [ 'more', 'slide', 'more' ];
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

	public function options( $options ) {
		if( ! License::has_valid_license() ) {
			$title = $this->get_name();
			$description = 'With this tool you can toggle the folding content.';
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
			'builder_preview',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> __('Editing Content On Builder?', 'ziultimate'),
				'description' => __('You would enable it when you are updating the content', 'ziultimate'),
				'default' 	=> false,
				'layout' 	=> 'inline'
			]
		);

		$options->add_option(
			'more_text',
			[
				'type' 	=> 'text',
				'title' => __('More Link Text', 'ziultimate'),
				'placeholder' => esc_html__('Read More'),
				'default' => esc_html__('Read More'),
				'dynamic'     	=> [
					'enabled' => true,
				]
			]
		);

		$options->add_option(
			'less_text',
			[
				'type' 	=> 'text',
				'title' => __('Less Link Text', 'ziultimate'),
				'placeholder' => esc_html__('Read Less'),
				'default' => esc_html__('Read Less'),
				'dynamic'     	=> [
					'enabled' => true,
				]
			]
		);

		$options->add_option(
			'hide_less_btn',
			[
				'type' => 'checkbox_switch',
				'title' => esc_html__('Disable Less Button', 'ziultimate'),
				'default' => false,
				'layout' => 'inline'
			]
		);

		$options->add_option(
			'collapsed_height',
			[
				'type' 		=> 'slider',
				'content' 	=> 'px',
				'default' 	=> 150,
				'min' 		=> 0,
				'max' 		=> 500,
				'step' 		=> 10,
				'title' 	=> __( 'Height (collapsed state)', 'ziultimate' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .zu-rm-content',
						'value' 	=> 'height: {{VALUE}}px'
					]
				]
			]
		);

		$options->add_option(
			'transition_speed',
			[
				'type' 		=> 'slider',
				'content' 	=> 'ms',
				'default' 	=> 700,
				'min' 		=> 0,
				'max' 		=> 10000,
				'step' 		=> 50,
				'title' 	=> __( 'Transition Speed', 'ziultimate' )
			]
		);

		$micon = $options->add_group(
			'icon',
			[
				'type' 		=> 'accordion_menu',
				'title' 	=> __('Icon for More Button')
			]
		);

		$micon->add_option(
			'more_btn_icon',
			[
				'type'       => 'icon_library',
				'id'         => 'more_btn_icon',
				'default'    => [
					'family'  => 'Font Awesome 5 Free Solid',
					'name'    => 'chevron-down',
					'unicode' => 'uf078',
				]
			]
		);

		$micon->add_option(
			'icon_position',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> __('Position', 'ziultimate'),
				'default' 	=> 'row-reverse',
				'options' 	=> [
					[
						'name' 	=> esc_html__('Left'),
						'id' 	=> 'row'
					],
					[
						'name' 	=> esc_html__('Right'),
						'id' 	=> 'row-reverse'
					],
					[
						'name' 	=> esc_html__('Top'),
						'id' 	=> 'column'
					],
					[
						'name' 	=> esc_html__('Bottom'),
						'id' 	=> 'column-reverse'
					],
				],
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .rm-link-wrapper',
						'value' 	=> 'flex-direction: {{VALUE}}'
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

		$licon = $options->add_group(
			'licon',
			[
				'type' 		=> 'accordion_menu',
				'title' 	=> __('Icon for Less Button')
			]
		);

		$licon->add_option(
			'less_btn_icon',
			[
				'type'       => 'icon_library',
				'id'         => 'less_btn_icon',
				'default'    => [
					'family'  => 'Font Awesome 5 Free Solid',
					'name'    => 'chevron-up',
					'unicode' => 'uf077',
				]
			]
		);

		$licon->add_option(
			'licon_position',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> __('Position', 'ziultimate'),
				'default' 	=> 'row-reverse',
				'options' 	=> [
					[
						'name' 	=> esc_html__('Left'),
						'id' 	=> 'row'
					],
					[
						'name' 	=> esc_html__('Right'),
						'id' 	=> 'row-reverse'
					],
					[
						'name' 	=> esc_html__('Top'),
						'id' 	=> 'column'
					],
					[
						'name' 	=> esc_html__('Bottom'),
						'id' 	=> 'column-reverse'
					],
				],
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .zu-read-less-btn .rm-link-wrapper',
						'value' 	=> 'flex-direction: {{VALUE}}'
					]
				]
			]
		);

		$rm_tg = $options->add_group(
			'rm_tg',
			[
				'type' 		=> 'accordion_menu',
				'title' 	=> __('Typography of More/Less Text', 'ziultimate')
			]
		);

		$this->attach_typography_options( $rm_tg, 'rm_tg', '{{ELEMENT}} .zu-text', ['text_align', 'font_color', 'text_decoration']);

		$rm_tg->add_option(
			'more_text_clr',
			[
				'type' => 'colorpicker',
				'title' => esc_html__('More Text Color', 'ziultimate'),
				'width' => 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .zu-more-text',
						'value' 	=> 'color: {{VALUE}}'
					]
				]
			]
		);

		$rm_tg->add_option(
			'more_text_hclr',
			[
				'type' => 'colorpicker',
				'title' => esc_html__('Hover Color', 'ziultimate'),
				'width' => 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .zu-read-more-btn:hover .zu-more-text',
						'value' 	=> 'color: {{VALUE}}'
					]
				]
			]
		);

		$rm_tg->add_option(
			'less_text_clr',
			[
				'type' => 'colorpicker',
				'title' => esc_html__('Less Text Color', 'ziultimate'),
				'width' => 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .zu-less-text',
						'value' 	=> 'color: {{VALUE}}'
					]
				]
			]
		);

		$rm_tg->add_option(
			'less_text_hclr',
			[
				'type' => 'colorpicker',
				'title' => esc_html__('Hover Color', 'ziultimate'),
				'width' => 50,
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .zu-read-less-btn:hover .zu-less-text',
						'value' 	=> 'color: {{VALUE}}'
					]
				]
			]
		);
	}

	public function enqueue_scripts() {
		$this->enqueue_editor_script( Utils::get_file_url( 'dist/js/elements/ReadMore/editor.js' ) );
		$this->enqueue_element_script( Utils::get_file_url( 'dist/js/elements/ReadMore/frontend.js' ) );
	}

	public function enqueue_styles() {
		$this->enqueue_editor_style( Utils::get_file_url( 'dist/css/elements/ReadMore/readmore.css' ) );
		$this->enqueue_element_style( Utils::get_file_url( 'dist/css/elements/ReadMore/readmore.css' ) );
	}

	public function on_register_styles() {
		$this->register_style_options_element(
			'button_wrapper',
			[
				'title'    => esc_html__( 'More / Less Button Wrapper', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .zu-read-more-btns',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'icon_styles',
			[
				'title'    => esc_html__( 'More / Less Button Icon', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .zu-rm-icon'
			]
		);
	}

	public function render( $options ) {
		$show_shadow = $options->get_value('show_shadow', false);
		$data = [
			'height' 		=> absint($options->get_value('collapsed_height', 150)),
			'speed' 		=> absint($options->get_value('transition_speed', 700)),
			'show_shadow' 	=> $show_shadow
		];

		$this->render_tag(
			'div',
			'read_more_content',
			$this->get_children_for_render(),
			[
				'class' => "zu-rm-content zu-ru-overflow" . ($show_shadow ? ' zurm-show-shadow' : ''),
				'data-ru-config' => wp_json_encode( $data )
			]
		);

		$more_text = $options->get_value( 'more_text' );
		$less_text = $options->get_value( 'less_text' );
		$show_less_btn = $options->get_value( 'hide_less_btn', false );
		$more_btn_icon = $options->get_value( 'more_btn_icon' );
		$less_btn_icon = $options->get_value( 'less_btn_icon' );
		?>
			<span class="zu-read-more-btns">
				<span class="zu-read-more-btn" role="button" aria-label="<?php echo $more_text ? $more_text : ''; ?>">
					<span class="rm-link-wrapper">
						<?php 
							if( $more_btn_icon ) : 
								$this->attach_icon_attributes( 'more_btn_icon', $more_btn_icon );
								$this->render_attributes->add( 'more_btn_icon', 'class', 'more-btn-icon zu-rm-icon' );
								$this->render_tag(
									'span', 
									'more_btn_icon'
								);
							endif;
						?>
						<?php if( $more_text ) : ?>
							<span class="zu-more-text zu-text">
								<?php echo $more_text; ?>
							</span>
						<?php endif; ?>
					</span>
				</span>
				<?php if( ! $show_less_btn ) : ?>
					<span class="zu-read-less-btn zu-rm-link-toggle" role="button"  aria-label="<?php echo $less_text ? $less_text : ''; ?>">
						<span class="rm-link-wrapper">
							<?php 
							if( $less_btn_icon ) :
								$this->attach_icon_attributes( 'less_btn_icon', $less_btn_icon );
								$this->render_attributes->add( 'less_btn_icon', 'class', 'less-btn-icon zu-rm-icon' );
								$this->render_tag(
									'span', 
									'less_btn_icon'
								);
							endif;
						?>
							<?php if( $less_text ) : ?>
								<span class="zu-less-text zu-text">
									<?php echo $less_text; ?>
								</span>
							<?php endif; ?>
						</span>
					</span>
				<?php endif; ?>
			</span>
		<?php
	}
}