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
 * Class Avatar
 *
 * @package ZiUltimate\WooElements
 */
class Avatar extends UltimateElements {
    public function get_type() {
		return 'zu_review_avatar';
	}

	public function get_name() {
		return __( 'Avatar', 'ziultimate' );
	}

	public function get_keywords() {
		return [ 'review avatar', 'avatar' ];
	}

	public function get_category() {
		return $this->zuwoo_reviews_elements_category();
	}

	public function get_element_icon() {
		return 'element-image';
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
			'size',
			[
				'title'			=> esc_html__( 'Size', 'zionbuilder' ),
				'type'			=> 'slider',
				'min'			=> 0,
				'max' 			=> 1000,
				'step' 			=> 1,
				'default' 		=> 60,
				'content'		=> 'px'
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

	/**
	 * Get style elements
	 *
	 * @return void
	 */
	public function on_register_styles() {
		$this->register_style_options_element(
			'avatar_styles',
			[
				'title'    => esc_html__( 'Avatar Styles', 'ziultimate' ),
				'selector' => '{{ELEMENT}} img.avatar',
			]
		);
	}

    public function render( $options ) {
        global $comment;

        echo get_avatar( $comment, $options->get_value( 'size', '60' ), '' );
    }
}