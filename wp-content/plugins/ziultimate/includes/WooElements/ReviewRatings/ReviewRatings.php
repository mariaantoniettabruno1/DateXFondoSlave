<?php
namespace ZiUltimate\WooElements\ReviewRatings;

use ZiUltimate\UltimateElements;
use ZiUltimate\Admin\License;
use ZiUltimate\Utils;
use ZionBuilderPro\Elements\WooCommerceElement;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class ReviewRatings
 *
 * @package ZiUltimate\WooElements
 */
class ReviewRatings extends UltimateElements {
    public function get_type() {
		return 'zu_product_ratings';
	}

	public function get_name() {
		return __( 'Reviews Rating', 'ziultimate' );
	}

	public function get_keywords() {
		return [ 'ratings', 'review ratings' ];
	}

	/*public function get_label() {
		return [
			'text'  => $this->get_label_text(),
			'color' => $this->get_label_color(),
		];
	}*/

	public function get_category() {
		return $this->zuwoo_elements_category();
	}

	public function get_element_icon() {
		return 'element-woo-product-rating';
	}

	/*public function is_wrapper() {
		return true;
	}*/

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
			'product_id',
			[
				'type' 			=> 'text',
				'title' 		=> esc_html__('Product ID', 'ziultimate'),
				'description' 	=> esc_html__('Keep it empty if you are using on single product page.', 'zilultimate'),
				'dynamic' 		=> [
					'enabled' => true
				]
			]
		);

		$options->add_option(
			'hide_reviews',
			[
				'type' 			=> 'text',
				'placeholder' 	=> 0,
				'title' 		=> esc_html__('Hide if number of reviews is less than', 'ziultimate'),
				'dynamic' 		=> [
					'enabled' => true
				]
			]
		);


		/*******************************
		 * Average Rating Controls
		 *******************************/
		$rating = $options->add_group(
			'rating',
			[
				'type' 		=> 'accordion_menu',
				'title' 	=> __('Average Rating Points', 'ziultimate')
			]
		);

		$rating->add_option(
			'show_rating_points',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> esc_html__('Display Rating Points?', 'ziultimate'),
				'default' 	=> true,
				'layout' 	=> 'inline'
			]
		);

		$rating->add_option(
			'average_rating_text',
			[
				'type' 		=> 'text',
				'default' 	=> '{average_rating} out of 5',
				'title' 	=> __('Text'),
				'dynamic' 	=> [
					'enabled' => true
				]
			]
		);

		$rating_points = $rating->add_group(
			'rating_points',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> __('Typography of Rating Points', 'ziultimate')
			]
		);

		$this->attach_typography_options( $rating_points, 'avgrp', '{{ELEMENT}} .average-rating' );


		/*******************************
		 * Rating Stars Controls
		 *******************************/
		$stars = $options->add_group(
			'stars',
			[
				'type' 		=> 'accordion_menu',
				'title' 	=> __('Stars', 'ziultimate')
			]
		);

		$stars->add_option(
			'show_stars',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> esc_html__('Display Stars?', 'ziultimate'),
				'default' 	=> true,
				'layout' 	=> 'inline'
			]
		);

		$stars->add_option(
			'empty_stars_color',
			[
				'title' => esc_html__( 'Empty Stars Color', 'zionbuilder' ),
				'type'  => 'colorpicker',
				'css_style' 	=> [
					[
						'selector' 	=> '{{ELEMENT}} .star-rating::before',
						'value' 	=> 'color: {{VALUE}}'
					],
				]
			]
		);

		$stars->add_option(
			'fill_stars_color',
			[
				'title' => esc_html__( 'Filled Stars Color', 'zionbuilder' ),
				'type'  => 'colorpicker',
				'css_style' 	=> [
					[
						'selector' 	=> '{{ELEMENT}} .star-rating',
						'value' 	=> 'color: {{VALUE}}'
					],
					[
						'selector' 	=> '{{ELEMENT}} .star-rating span:before',
						'value' 	=> 'color: {{VALUE}}'
					],
				]
			]
		);

		$stars->add_option(
			'stars_size',
			[
				'type' 			=> 'slider',
				'content' 		=> 'em',
				'default' 		=> 1,
				'min' 			=> 1,
				'max' 			=> 10,
				'step' 			=> 0.01,
				'title'			=> esc_html__( 'Size' ),
				'css_style' 	=> [
					[
						'selector' 	=> '{{ELEMENT}} .star-rating',
						'value' 	=> 'font-size: {{VALUE}}em'
					]
				]
			]
		);

		$stars->add_option(
			'spacing_label',
			[
				'type' 		=> 'html',
				'title' 	=> __('Spacing'),
				'content' 	=> '<hr style="border: 1px solid #e5e5e5">'
			]
		);

		$this->attach_margin_options( $stars, 'stars_sp', '{{ELEMENT}} .star-rating' );


		/*******************************
		 * Total Reviews Controls
		 *******************************/
		$total = $options->add_group(
			'total_reviews',
			[
				'type' 		=> 'accordion_menu',
				'title' 	=> __('Total Reviews', 'ziultimate')
			]
		);

		$total->add_option(
			'show_total_reviews',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> esc_html__('Display Total Reviews?', 'ziultimate'),
				'default' 	=> true,
				'layout' 	=> 'inline'
			]
		);

		$total->add_option(
			'total_reviews_text',
			[
				'type' 		=> 'text',
				'default' 	=> '{total_reviews} reviews',
				'title' 	=> __('Text'),
				'dynamic' 	=> [
					'enabled' => true
				]
			]
		);

		$count = $total->add_group(
			'count',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> __('Typography of Total Number', 'ziultimate')
			]
		);

		$this->attach_typography_options( $count, 'count', '{{ELEMENT}} .reviews-counts' );
    }

	/**
	 * Loaing the CSS
	 */
	public function enqueue_styles() {
		$this->enqueue_element_style( Utils::get_file_url( 'dist/css/elements/ReviewRatings/frontend.css' ) );
	}

	/**
	 * Loaing the Scripts
	 */
	public function enqueue_scripts() {
		$this->enqueue_editor_script( Utils::get_file_url( 'dist/js/elements/ReviewRatings/editor.js' ) );
	}

	/**
	 * Get style elements
	 *
	 * @return void
	 */
	public function on_register_styles() {
		$this->register_style_options_element(
			'rating_text_styles',
			[
				'title'    => esc_html__( 'Average Ratings Text', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .rating-points',
			]
		);

		$this->register_style_options_element(
			'total_reviews_text_styles',
			[
				'title'    => esc_html__( 'Total Reviews Text', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .total-reviews',
			]
		);
	}

    public function render( $options ) {
		$product_id = $options->get_value('product_id', get_the_ID());
		$product 	= \WC()->product_factory->get_product( $product_id );

		if ( ! $product instanceof \WC_Product ) {
			return;
		}

		$average_rating_text = wp_kses_post( $options->get_value('average_rating_text') );
		$total_reviews_text = wp_kses_post( $options->get_value('total_reviews_text') );
		$total_reviews = $product->get_review_count();
		$average_rating = wc_format_decimal( $product->get_average_rating(), 2 );
		$show_rating_points = $options->get_value('show_rating_points', true);
		$show_stars = $options->get_value('show_stars', true);
		$show_total_reviews = $options->get_value('show_total_reviews', true);

		$hide_reviews = $options->get_value( 'hide_reviews' );

		if( ! empty( $hide_reviews ) || $hide_reviews == '0' ) {
			$hide_reviews = $hide_reviews;
		} else {
			$hide_reviews = -1;
		}
		
		if( isset( $average_rating_text ) && $total_reviews > $hide_reviews && $show_rating_points ) : 
		?>
			<div class="rating-points">
				<?php
					echo str_replace( "{average_rating}", '<span class="average-rating">'. $average_rating .'</span>', $average_rating_text );
				?>
			</div>
		<?php endif; ?>
		<?php if( $total_reviews > $hide_reviews && $show_stars ) : ?>
			<?php echo wc_get_rating_html( $average_rating + 0.01 ); ?>
		<?php endif; ?>

		<?php if( isset( $total_reviews_text ) && $total_reviews > $hide_reviews && $show_total_reviews ) : ?>
			<div class="total-reviews">
				<?php
					echo str_replace( "{total_reviews}", '<span class="reviews-counts">'. $total_reviews .'</span>', $total_reviews_text );
				?>
			</div>
		<?php 
		
		endif;
    }
}