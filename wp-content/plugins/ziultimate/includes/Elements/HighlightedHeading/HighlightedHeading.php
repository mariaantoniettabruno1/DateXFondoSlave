<?php
namespace ZiUltimate\Elements\HighlightedHeading;

use ZiUltimate\UltimateElements;
use ZiUltimate\Admin\License;
use ZiUltimate\Utils;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class HighlightedHeading
 *
 * @package ZiUltimate\Elements
 */
class HighlightedHeading extends UltimateElements {
	
	public function get_type() {
		return 'zu_highlighted_heading';
	}

	public function get_name() {
		return __( 'Highlighted Heading', 'ziultimate' );
	}

	public function get_keywords() {
		return [ 'highlight', 'heading', 'highlighted heading' ];
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

	public function options( $options ) {
		if( ! License::has_valid_license() ) {
			$title = $this->get_name();
			$description = 'With this tool you can build the highlighted heading.';
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
			'heading_tag',
			[
				'type' 		=> 'select',
				'title' 	=> esc_html__('HTML Tag'),
				'default' 	=> 'h2',
				'options' 	=> [
					[
						'id' 	=> 'h1',
						'name' 	=> 'H1'
					],
					[
						'id' 	=> 'h2',
						'name' 	=> 'H2'
					],
					[
						'id' 	=> 'h3',
						'name' 	=> 'H3'
					],
					[
						'id' 	=> 'h4',
						'name' 	=> 'H4'
					],
					[
						'id' 	=> 'h5',
						'name' 	=> 'H5'
					],
					[
						'id' 	=> 'h6',
						'name' 	=> 'H6'
					]
				]
			]
		);

		$options->add_option(
            'before_text',
            [
                'type'          => 'text',
                'title'         => esc_html__('Before Text', 'ziultimate'),
                'description'   => __("This text will be placed before the highlighted text.", "ziultimate"),
				'dynamic' 		=> [
					'enabled' 	=> true
				]
            ]
        );

		$options->add_option(
            'highlighted_text',
            [
                'type'          => 'textarea',
                'title'         => esc_html__('Highlighted Heading Text', 'ziultimate'),
				"default" 		=> esc_html__("Highlighted", "ziultimate"),
				'dynamic' 		=> [
					'enabled' 	=> true
				]
            ]
		);

		$options->add_option(
            'after_text',
            [
                'type'          => 'text',
                'title'         => esc_html__('After Text', 'ziultimate'),
                'description'   => __("This text will be placed after the highlighted text.", "ziultimate"),
				'dynamic' 		=> [
					'enabled' 	=> true
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
			'shape',
			array(
				"type" 		=> 'select',
				"title" 	=> __('Shape', "ziultimate"),
				"default" 	=> 'circle',
				"options" 	=> array(
					[
						'id' 		=> 'circle',
						'name' 		=> __('Circle', 'ziultimate')
					],
					[
						'id' 		=> 'curly',
						'name'		=> __('Curly', 'ziultimate')
					],
					[
						'id' 		=> 'diagonal',
						'name'		=> __('Diagonal', 'ziultimate')
					],
					[
						'id' 		=> 'double',
						'name'		=> __('Double Underline', 'ziultimate')
					],
					[
						'id' 		=> 'doubleub',
						'name'		=> __('Double Underline Bottom', 'ziultimate')
					],
					[
						'id' 		=> 'strikethrough',
						'name'		=> __('Strikethrough', 'ziultimate')
					],
					[
						'id' 		=> 'underline',
						'name'		=> __('Underline', 'ziultimate')
					],
					[
						'id' 		=> 'underline_zigzag',
						'name'	=> __('Underline Zigzag', 'ziultimate')
					],
					[
						'id' 		=> 'underline_lr',
						'name' 		=> __('Underline-Left to Right', 'ziultimate')
					],
					[
						'id' 		=> 'underline_rl',
						'name' 		=> __('Underline-Right to Left', 'ziultimate')
					],
					[
						'id' 		=> 'underline_outwards',
						'name' 		=> __('Underline-Outwards', 'ziultimate')
					],
					[
						'id' 		=> 'underline_inwards',
						'name'		=> __('Underline-Inwards', 'ziultimate')
					],
				),
				
			)
		);

		$options->add_option(
			'shape_loop',
			array(
				"type" 		=> 'select',
				"title" 	=> __('Loop', "ziultimate"),
				"default" 	=> 'loop',
				"options" 	=> array(
					[
						'id' 	=> 'loop',
						'name' 	=> __('Yes', 'ziultimate')
					],
					[
						'id' 	=> 'noloop',
						'name' 	=> __('No', 'ziultimate')
					]
				),
				"dependency" => [
					[
						'option' 	=> 'shape',
						'value' 	=> ['underline_lr', 'underline_rl', 'underline_outwards', 'underline_inwards'],
						'type' 		=> 'not_in'
					]
				]
			)
		);

		$options->add_option(
			'shape_appear',
			array(
				"type" 		=> 'select',
				"title" 	=> __('Appearance', "ziultimate"),
				"default" 	=> 'offhover',
				"options" 	=> array(
					[
						'id' 	=> 'onhover',
						'name' 	=> __('Slide In on Hover', 'ziultimate')
					],
					[
						'id' 	=> 'offhover',
						'name' 	=> __('Slide Out on Hover', 'ziultimate')
					]
				),
				"dependency" => [
					[
						'option' 	=> 'shape',
						'value' 	=> ['underline_lr', 'underline_rl', 'underline_outwards', 'underline_inwards'],
					]
				]
			)
		);

		$options->add_option(
			'shape_color',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__('Shape Color', 'ziultimate'),
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .highlighted-text-wrapper svg path',
						'value' 	=> 'stroke: {{VALUE}}'
					]
				],
				"dependency" => [
					[
						'option' 	=> 'shape',
						'value' 	=> ['underline_lr', 'underline_rl', 'underline_outwards', 'underline_inwards'],
						'type' 		=> 'not_in'
					]
				]
			]
		);

		$options->add_option(
			'shape_width',
			[
				'type' 		=> 'slider',
				'content' 	=> ' ',
				'default' 	=> 9,
				'min' 		=> 0,
				'max' 		=> 100,
				'step' 		=> 1,
				'title' 	=> esc_html__('Shape Width', 'ziultimate'),
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .highlighted-text-wrapper svg path',
						'value' 	=> 'stroke-width: {{VALUE}}'
					]
				],
				"dependency" => [
					[
						'option' 	=> 'shape',
						'value' 	=> ['underline_lr', 'underline_rl', 'underline_outwards', 'underline_inwards'],
						'type' 		=> 'not_in'
					]
				]
			]
		);

		$options->add_option(
			'shape_td',
			[
				'type' 		=> 'slider',
				'content' 	=> 's',
				'default' 	=> 10,
				'min' 		=> 0,
				'max' 		=> 50,
				'step' 		=> 1,
				'title' 	=> esc_html__('Transition Duration', 'ziultimate'),
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}}',
						'value' 	=> '--animation-td: {{VALUE}}s'
					]
				],
				"dependency" => [
					[
						'option' 	=> 'shape',
						'value' 	=> ['underline_lr', 'underline_rl', 'underline_outwards', 'underline_inwards'],
						'type' 		=> 'not_in'
					]
				]
			]
		);

		$options->add_option(
			'shape_delay',
			[
				'type' 		=> 'slider',
				'content' 	=> 's',
				'default' 	=> .6,
				'min' 		=> 0,
				'max' 		=> 10,
				'step' 		=> 0.01,
				'title' 	=> esc_html__('Animation Delay(sec)', 'ziultimate'),
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}}',
						'value' 	=> '--animation-delay: {{VALUE}}s'
					]
				],
				"dependency" => [
					[
						'option' 	=> 'shape',
						'value' 	=> ['underline_lr', 'underline_rl', 'underline_outwards', 'underline_inwards'],
						'type' 		=> 'not_in'
					]
				]
			]
		);

		$options->add_option(
			'line_color',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__('Line Color', 'ziultimate'),
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .highlighted-text-wrapper .ul-anim:before',
						'value' 	=> 'background-color: {{VALUE}}'
					],
					[
						'selector' 	=> '{{ELEMENT}} .highlighted-text-wrapper .ul-anim:after',
						'value' 	=> 'background-color: {{VALUE}}'
					]
				],
				"dependency" => [
					[
						'option' 	=> 'shape',
						'value' 	=> ['underline_lr', 'underline_rl', 'underline_outwards', 'underline_inwards']
					]
				]
			]
		);

		$options->add_option(
			'line_width',
			[
				'type' 		=> 'slider',
				'content' 	=> 'px',
				'default' 	=> 5,
				'min' 		=> 0,
				'max' 		=> 30,
				'step' 		=> 1,
				'title' 	=> esc_html__('Line Width', 'ziultimate'),
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .highlighted-text-wrapper .ul-anim:before',
						'value' 	=> 'height: {{VALUE}}px'
					],
					[
						'selector' 	=> '{{ELEMENT}} .highlighted-text-wrapper .ul-anim:after',
						'value' 	=> 'height: {{VALUE}}px'
					]
				],
				"dependency" => [
					[
						'option' 	=> 'shape',
						'value' 	=> ['underline_lr', 'underline_rl', 'underline_outwards', 'underline_inwards']
					]
				]
			]
		);

		$options->add_option(
			'line_position',
			[
				'type' 		=> 'slider',
				'content' 	=> 'px',
				'default' 	=> -10,
				'min' 		=> -10,
				'max' 		=> 10,
				'step' 		=> 1,
				'title' 	=> esc_html__('Line Position', 'ziultimate'),
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .highlighted-text-wrapper .ul-anim:before',
						'value' 	=> 'bottom: {{VALUE}}px'
					],
					[
						'selector' 	=> '{{ELEMENT}} .highlighted-text-wrapper .ul-anim:after',
						'value' 	=> 'bottom: {{VALUE}}px'
					]
				],
				"dependency" => [
					[
						'option' 	=> 'shape',
						'value' 	=> ['underline_lr', 'underline_rl', 'underline_outwards', 'underline_inwards']
					]
				]
			]
		);

		$options->add_option(
			'line_td',
			[
				'type' 		=> 'slider',
				'content' 	=> 's',
				'default' 	=> .75,
				'min' 		=> 0,
				'max' 		=> 10,
				'step' 		=> 0.01,
				'title' 	=> esc_html__('Transition Duration for Line Shape', 'ziultimate'),
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .highlighted-text-wrapper .ul-anim:before',
						'value' 	=> 'transition-duration: {{VALUE}}s'
					],
					[
						'selector' 	=> '{{ELEMENT}} .highlighted-text-wrapper .ul-anim:after',
						'value' 	=> 'transition-duration: {{VALUE}}s'
					]
				],
				"dependency" => [
					[
						'option' 	=> 'shape',
						'value' 	=> ['underline_lr', 'underline_rl', 'underline_outwards', 'underline_inwards']
					]
				]
			]
		);
	}

	public function before_render( $options ) {
		$tag = $options->get_value( 'heading_tag', 'h2' );

		$this->set_wrapper_tag( $tag );
	}

	/**
	 * Loaing the CSS
	 */
	public function enqueue_styles() {
		$this->enqueue_element_style( Utils::get_file_url('dist/css/elements/HighlightedHeading/frontend.css' ) );
	}

	/**
	 * Loading the scripts
	 */
	public function enqueue_scripts() {
		$this->enqueue_editor_script( Utils::get_file_url( 'dist/js/elements/HighlightedHeading/editor.js' ) );
		//$this->enqueue_element_script( Utils::get_file_url( 'dist/js/elements/HighlightedHeading/frontend.js' ) );
	}

	public function on_register_styles() {
		$this->register_style_options_element(
			'highlighted_text_styles',
			[
				'title'    => esc_html__( 'Highlighted Text', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .highlighted-text',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);
	}

	public function render( $options ) {
		$shape = $options->get_value('shape', 'circle');
		$loop = 'zuhlh-headline-' . $options->get_value('shape_loop', 'loop');

		$before_text = $options->get_value('before_text');
		$after_text = $options->get_value('after_text');
		$highlighted_text = $options->get_value('highlighted_text', 'Highlighted');

		$svg = '';

		$ulAnim = '';
		$shape_underline = ['underline_lr', 'underline_rl', 'underline_outwards', 'underline_inwards'];
		if( in_array( $shape, $shape_underline ) )
		{
			$ulAnim = ' ul-anim ' . $shape . ' ' . $options->get_value('shape_appear', 'offhover' );
		} else {
			$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none" class="'. $shape .'">';
			$types = [ 
				'circle' 	=> [ 'M325,18C228.7-8.3,118.5,8.3,78,21C22.4,38.4,4.6,54.6,5.6,77.6c1.4,32.4,52.2,54,142.6,63.7 c66.2,7.1,212.2,7.5,273.5-8.3c64.4-16.6,104.3-57.6,33.8-98.2C386.7-4.9,179.4-1.4,126.3,20.7' ],
				'curly' 	=> [ 'M3,146.1c17.1-8.8,33.5-17.8,51.4-17.8c15.6,0,17.1,18.1,30.2,18.1c22.9,0,36-18.6,53.9-18.6 c17.1,0,21.3,18.5,37.5,18.5c21.3,0,31.8-18.6,49-18.6c22.1,0,18.8,18.8,36.8,18.8c18.8,0,37.5-18.6,49-18.6c20.4,0,17.1,19,36.8,19 c22.9,0,36.8-20.6,54.7-18.6c17.7,1.4,7.1,19.5,33.5,18.8c17.1,0,47.2-6.5,61.1-15.6' ],
				'diagonal' 	=> [ 'M13.5,15.5c131,13.7,289.3,55.5,475,125.5'],
				'double' 	=> [ 'M8.4,143.1c14.2-8,97.6-8.8,200.6-9.2c122.3-0.4,287.5,7.2,287.5,7.2', 'M8,19.4c72.3-5.3,162-7.8,216-7.8c54,0,136.2,0,267,7.8'],
				'doubleub' 	=> [ 'M5,125.4c30.5-3.8,137.9-7.6,177.3-7.6c117.2,0,252.2,4.7,312.7,7.6', 'M26.9,143.8c55.1-6.1,126-6.3,162.2-6.1c46.5,0.2,203.9,3.2,268.9,6.4'],
				'strikethrough' => [ 'M3,75h493.5'],
				'underline' 	=> [ 'M7.7,145.6C109,125,299.9,116.2,401,121.3c42.1,2.2,87.6,11.8,87.3,25.7' ],
				'underline_zigzag' => [ 'M9.3,127.3c49.3-3,150.7-7.6,199.7-7.4c121.9,0.4,189.9,0.4,282.3,7.2C380.1,129.6,181.2,130.6,70,139 c82.6-2.9,254.2-1,335.9,1.3c-56,1.4-137.2-0.3-197.1,9' ]
			];

			foreach( $types[ $shape ] as $path ) {
				$svg .= '<path d="' . $path . '" />';
			}

			$svg .= '</svg>';
		}
	?>
		<span class="zuhlh-shape <?php echo $loop;?>">
			<?php if( isset( $before_text ) ) : ?>
			<span class="headline-text"><?php echo $before_text;?></span> 
			<?php endif; ?>
			<?php if( isset( $highlighted_text ) ) : ?>
			<span class="highlighted-text-wrapper">
				<span class="highlighted-text<?php echo $ulAnim; ?>"><?php echo $highlighted_text . $svg;?></span>
			</span> 
			<?php endif; ?>
			<?php if( isset( $after_text ) ) : ?>
				<span class="headline-text"><?php echo $after_text;?></span>
			<?php endif; ?>
		</span>
	<?php
	}
}