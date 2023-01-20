<?php
namespace ZiUltimate\WooElements\Reviews;

use ZiUltimate\UltimateElements;
use ZiUltimate\Admin\License;
use ZionBuilder\Options\BaseSchema;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Author
 *
 * @package ZiUltimate\WooElements
 */
class Author extends UltimateElements {
    public function get_type() {
		return 'zu_review_author';
	}

	public function get_name() {
		return __( 'Author', 'ziultimate' );
	}

	public function get_keywords() {
		return [ 'review author' ];
	}

	public function get_category() {
		return $this->zuwoo_reviews_elements_category();
	}

	public function get_element_icon() {
		return 'element-user';
	}

    public function options( $options ) {
		if( ! License::has_valid_license() ) {
			$title = $this->get_name();
			$description = 'With this tool you can display the author name.';
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
			'font-family',
			[
				'title' 		=> esc_html__( 'Font Family', 'zionbuilder' ),
				'type'			=> 'select',
				'data_source'	=> 'fonts',
				'width' 		=> 50,
				'style_type' 	=> 'font-select',
				'sync'			=> '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.font-family',
			]
		);

		$options->add_option(
			'font-weight',
			[
				'title' 		=> esc_html__( 'Font Weight', 'zionbuilder' ),
				'description' 	=> esc_html__( 'Font weight allows you to set the text thickness.', 'zionbuilder' ),
				'type' 			=> 'select',
				'default' 		=> '400',
				'width' 		=> 50,
				'options' 		=> [
					[
						'id'   => '100',
						'name' => '100',
					],
					[
						'id'   => '200',
						'name' => '200',
					],
					[
						'id'   => '300',
						'name' => '300',
					],
					[
						'id'   => '400',
						'name' => '400',
					],
					[
						'id'   => '500',
						'name' => '500',
					],
					[
						'id'   => '600',
						'name' => '600',
					],
					[
						'id'   => '700',
						'name' => '700',
					],
					[
						'id'   => '800',
						'name' => '800',
					],
					[
						'id'   => '900',
						'name' => '900',
					],
					[
						'id'   => 'bolder',
						'name' => esc_html__( 'Bolder', 'zionbuilder' ),
					],
					[
						'id'   => 'lighter',
						'name' => esc_html__( 'Lighter', 'zionbuilder' ),
					],
					[
						'id'   => 'inherit',
						'name' => esc_html__( 'Inherit', 'zionbuilder' ),
					],
					[
						'id'   => 'initial',
						'name' => esc_html__( 'Initial', 'zionbuilder' ),
					],
					[
						'id'   => 'unset',
						'name' => esc_html__( 'Unset', 'zionbuilder' ),
					],
				],
				'sync'	=> '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.font-weight',
			]
		);

		$options->add_option(
			'font_size',
			[
				'title'			=> esc_html__( 'Font Size', 'ziultimate' ),
				'type'			=> 'number_unit',
				'min'			=> 0,
				'width' 		=> 50,
				'units'			=> BaseSchema::get_units(),
				'sync'			=> '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.font-size',
				'show_responsive_buttons' => true
			]
		);

		$options->add_option(
			'color',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__( 'Color' ),
				'width' 	=> 50,
				'sync' 		=> '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.color'
			]
		);

		$options->add_option(
			'text-transform',
			[
				'type'    => 'custom_selector',
				'title'	  => esc_html__( 'Text Transform', 'zionbuilder' ),
				'columns' => 3,
				'width'   => 50,
				'options' => [
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
				'sync' 		=> '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.text-transform'
			]
		);

		$options->add_group(
			'padding',
			[
				'type'                    => 'dimensions',
				'title'                   => __( 'Padding', 'zionbuilder' ),
				'description'             => __( 'Choose the desired padding for this element.', 'zionbuilder' ),
				'min'                     => 0,
				'max'                     => 99999,
				'sync'                    => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default',
				'show_responsive_buttons' => true,
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

		$options->add_group(
			'margin',
			[
				'type'                    => 'dimensions',
				'title'                   => __( 'Margin', 'zionbuilder' ),
				'description'             => __( 'Choose the desired margin for this element.', 'zionbuilder' ),
				'min'                     => -99999,
				'max'                     => 99999,
				'sync'                    => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default',
				'show_responsive_buttons' => true,
				'dimensions'              => [
					[
						'name' => 'top',
						'icon' => 'margin-top',
						'id'   => 'margin-top',
					],
					[
						'name' => 'right',
						'icon' => 'margin-right',
						'id'   => 'margin-right',
					],
					[
						'name' => 'bottom',
						'icon' => 'margin-bottom',
						'id'   => 'margin-bottom',
					],
					[
						'name' => 'left',
						'icon' => 'margin-left',
						'id'   => 'margin-left',
					],
				],
			]
		);
    }

    public function can_render() {
    	if( ! License::has_valid_license() ) {
			return false;
		}

		return true;
	}

    public function render( $options ) {
        global $comment;

    	echo comment_author( $comment->comment_ID );
    }
}