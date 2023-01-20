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
 * Class Ratings
 *
 * @package ZiUltimate\WooElements
 */
class Ratings extends UltimateElements {
    public function get_type() {
		return 'zu_review_ratings';
	}

	public function get_name() {
		return __( 'Ratings', 'ziultimate' );
	}

	public function get_keywords() {
		return [ 'ratings', 'review ratings', 'rating' ];
	}

	public function get_category() {
		return $this->zuwoo_reviews_elements_category();
	}

	public function get_element_icon() {
		return 'element-woo-product-rating';
	}

    public function options( $options ) {
		if( ! License::has_valid_license() ) {
			$title = $this->get_name();
			$description = 'With this tool you can display the ratings.';
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
			'inactive_color',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__( 'Inactive Stars Color' ),
				'width' 	=> 50,
				'sync' 		=> '_styles.stars_styles.styles.%%RESPONSIVE_DEVICE%%.default.color'
			]
		);

		$options->add_option(
			'active_color',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__( 'Active Stars Color' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .star-rating span:before',
						'value' 	=> 'color: {{VALUE}}'
					]
				]
			]
		);

		$options->add_option(
			'font_size',
			[
				'title'			=> esc_html__( 'Size', 'ziultimate' ),
				'type'			=> 'number_unit',
				'min'			=> 0,
				'units'			=> BaseSchema::get_units(),
				'sync'			=> '_styles.stars_styles.styles.%%RESPONSIVE_DEVICE%%.default.font-size',
				'show_responsive_buttons' => true
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

		if ( ! \post_type_supports( 'product', 'comments' ) ) {
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
			'stars_styles',
			[
				'title'    => esc_html__( 'Stars Styles', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .star-rating',
			]
		);
	}

    public function render( $options ) {
        global $comment;

       if ( \post_type_supports( 'product', 'comments' ) ) {
			\wc_get_template( 'single-product/review-rating.php' );
		}
    }
}