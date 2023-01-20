<?php
namespace ZiUltimate\Elements\ImageScroller;

use ZiUltimate\UltimateElements;
use ZiUltimate\Utils;
use ZiUltimate\Admin\License;
use ZionBuilder\Options\BaseSchema;
use ZionBuilder\WPMedia;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class ImageScroller
 *
 * @package ZiUltimate\Elements
 */
class ImageScroller extends UltimateElements {
	
	public function get_type() {
		return 'zu_image_scroller';
	}

	public function get_name() {
		return __( 'Image Scroller', 'ziultimate' );
	}

	public function get_keywords() {
		return [ 'photo', 'image', 'scroller', 'media' ];
	}

	public function get_element_icon() {
		return 'element-image';
	}

	public function get_category() {
		return $this->zu_elements_category();
	}

	public function options( $options ) {
		if( ! License::has_valid_license() ) {
			$title = $this->get_name();
			$description = 'You can quickly create the auto image scroller.';
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
			'image',
			[
				'type'        => 'image',
				'id'          => 'image',
				'description' => 'Choose the desired image.',
				'title'       => esc_html__( 'Image', 'zionbuilder' ),
				'show_size'   => true,
				'default'     => [
					'image' => Utils::get_file_url( 'assets/img/no-image.jpg' ),
				],
				'dynamic'     => [
					'enabled' => true,
				]
			]
		);

		$options->add_option(
			'direction',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> esc_html__( 'Direction', 'ziultimate' ),
				'default' 	=> 'vertical',
				'options' 	=> [
					[
						'name' => 'Horizontal',
						'id'   => 'horizontal',
					],
					[
						'name' => 'Vertical',
						'id'   => 'vertical',
					]
				]
			]
		);

		$options->add_option(
			'height',
			[
				'type' 						=> 'dynamic_slider',
				'title' 					=> esc_html__( 'Container Height', 'zionbuilder' ),
				'description' 				=> esc_html__( 'Select the desired height.', 'zionbuilder' ),
				'sync' 						=> '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.height',
				'show_responsive_buttons' 	=> true,
				'default' 					=> [
					'default' => '300px'
				],
				'options' 					=> [
					[
						'unit' => 'px',
						'min'  => 0,
						'max'  => 999,
						'step' => 1,
					],
					[
						'unit' => '%',
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
					[
						'unit' => 'pt',
						'min'  => 0,
						'max'  => 999,
						'step' => 1,
					],
					[
						'unit' => 'em',
						'min'  => 0,
						'max'  => 999,
						'step' => 1,
					],
					[
						'unit' => 'rem',
						'min'  => 0,
						'max'  => 999,
						'step' => 1,
					],
					[
						'unit' => 'vh',
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
			]
		);

		$options->add_option(
			'zuel_imgscrl',
			[
				'type' 		=> 'text',
				'default' 	=> 'zu' . self::elVal(),
				'css_class' => 'znpb-checkbox-switch-wrapper__checkbox'
			]
		);

		$options->add_option(
			'duration',
			[
				'type'      => 'slider',
				'title'     => __( 'Scroll Duration', 'ziultimate' ),
				'default'   => 1000,
				'min'       => 100,
				'max'       => 10000,
				'step'      => 100,
				'content'   => 'ms',
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} img',
						'value' 	=> 'transition-duration: {{VALUE}}ms'
					]
				]
			]
		);

		$options->add_option(
			'cursor',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> esc_html__( 'Change Cursor', 'ziultimate' ),
				'default' 	=> 'no',
				'options' 	=> [
					[
						'name' => 'Yes',
						'id'   => 'yes',
					],
					[
						'name' => 'No',
						'id'   => 'no',
					]
				],
				'render_attribute' => [
					[
						'tag_id'    => 'wrapper',
						'attribute' => 'class',
						'value'     => 'change-cursor--{{VALUE}}',
					],
				]
			]
		);
	}

	/**
	 * Loading the CSS files
	 * 
	 * @return void
	 */
	public function enqueue_styles() {
		$this->enqueue_element_style( Utils::get_file_url( 'dist/css/elements/ImageScroller/frontend.css' ) );
	}

	/**
	 * Loading the js files
	 * 
	 * @return void
	 */
	public function enqueue_scripts() {
		$this->enqueue_editor_script( Utils::get_file_url( 'dist/js/elements/ImageScroller/editor.js' ) );
		$this->enqueue_element_script( Utils::get_file_url( 'dist/js/elements/ImageScroller/frontend.js' ) );
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
			'image_styles',
			[
				'title'      => esc_html__( 'Image styles', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} img',
				'render_tag' => 'image_styles',
			]
		);
	}

	public function before_render( $options ) {
		$class = 'zu-image-scroller ';
		$class .= 'zu-image-scroller-' . $options->get_value('direction', 'vertical');
		$this->render_attributes->add( 'wrapper', 'class', $class );
	}

	public function render( $options ) {
		$image  = $options->get_value(
			'image',
			[
				'image' => Utils::get_file_url( 'assets/img/no-image.jpg' ),
			]
		);

		// Don't proceed if we don't have an image
		if ( empty( $image['image'] ) ) {
			return;
		}

		$attachment_id 	= attachment_url_to_postid( esc_attr( $image['image'] ) );
		$combined_image_attr = $this->render_attributes->get_combined_attributes_as_key_value( 'image_styles', [ 'class' => 'wp-image-' . $attachment_id ] );

		echo WPMedia::get_imge(
			$image,
			$combined_image_attr
		);
	}
}