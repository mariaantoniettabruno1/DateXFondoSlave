<?php
namespace ZiUltimate\WooElements\RatingsGraph;

use ZiUltimate\UltimateElements;
use ZiUltimate\Utils;
use ZionBuilder\Options\BaseSchema;
use ZionBuilderPro\Elements\WooCommerceElement;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class RatingsGraph
 *
 * @package ZiUltimate\WooElements
 */
class RatingsGraph extends UltimateElements {
    public function get_type() {
		return 'zu_ratings_graph';
	}

	public function get_name() {
		return __( 'Rating Graph', 'ziultimate' );
	}

	public function get_keywords() {
		return [ 'rating', 'graph', 'ratings graph' ];
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

	public function options( $options ) {
		$options->add_option(
			'product_id',
			[
				'type' 		=> 'text',
				'title' 	=> esc_html__('Product ID', 'ziultimate'),
				'description' => esc_html__('Keep it empty if you are using on single product page.', 'zilultimate'),
				'dynamic' 	=> [
					'enabled' => true
				]
			]
		);

		$label = $options->add_group(
			'labels',
			[
				'type' 	=> 'panel_accordion',
				'title' => esc_html__('Labels')
			]
		);

		$label->add_option(
			'values_type',
			[
				'type' 		=> 'select',
				'default' 	=> 'star_txt',
				'title' 	=> esc_html__('Label Type', 'ziultimate'),
				'options' 	=> [
					[
						'id' 	=> 'star_txt',
						'name' 	=> __('Star Text', "ziultimate"),
					],
					[
						'id' 	=> 'num_icon',
						'name' 	=> __('Num + Star Icon', "ziultimate")
					],
					[
						'id' 	=> 'icon',
						'name' 	=> __('Star Icons', "ziultimate")
					],
					[
						'id' 	=> 'text',
						'name' 	=> __('Custom Text', "ziultimate")
					]
				]
			]
		);

		$label->add_option(
			'label_icon',
			[
				'type'       => 'icon_library',
				'id'         => 'label_icon',
				'default'    => [
					'family'  => 'Font Awesome 5 Free Solid',
					'name'    => 'star',
					'unicode' => 'uf005',
				],
				'dependency' 	=> [
					[
						'option' 	=> 'values_type',
						'value' 	=> [ 'num_icon' ]
					]
				]
			]
		);

		$label->add_option(
			'label_icon_size',
			[
				'type' 			=> 'number_unit',
				'default' 		=> 16,
				'min' 			=> 0,
				'units' 		=> BaseSchema::get_units(),
				'title'			=> esc_html__( 'Icon Size', 'ziultimate' ),
				'css_style' 	=> [
					[
						'selector' 	=> '{{ELEMENT}} .zu-label-icon',
						'value' 	=> 'font-size: {{VALUE}}'
					]
				],
				'dependency' 	=> [
					[
						'option' 	=> 'values_type',
						'value' 	=> [ 'num_icon' ]
					]
				]
			]
		);

		$label->add_option(
			'text_5',
			[
				'type' 		=> 'text',
				'title' 	=> '',
				'default' 	=> __('Excellent', "ziultimate"),
				'dependency' 	=> [
					[
						'option' 	=> 'values_type',
						'value' 	=> [ 'text' ]
					]
				]
			]
		);

		$label->add_option(
			'text_4',
			[
				'type' 		=> 'text',
				'title' 	=> '',
				'default' 	=> __('Good', "ziultimate"),
				'dependency' 	=> [
					[
						'option' 	=> 'values_type',
						'value' 	=> [ 'text' ]
					]
				]
			]
		);

		$label->add_option(
			'text_3',
			[
				'type' 		=> 'text',
				'title' 	=> '',
				'default' 	=> __('Average', "ziultimate"),
				'dependency' 	=> [
					[
						'option' 	=> 'values_type',
						'value' 	=> [ 'text' ]
					]
				]
			]
		);

		$label->add_option(
			'text_2',
			[
				'type' 		=> 'text',
				'title' 	=> '',
				'default' 	=> __('Not Bad', "ziultimate"),
				'dependency' 	=> [
					[
						'option' 	=> 'values_type',
						'value' 	=> [ 'text' ]
					]
				]
			]
		);

		$label->add_option(
			'text_1',
			[
				'type' 		=> 'text',
				'title' 	=> '',
				'default' 	=> __('Very Poor', "ziultimate"),
				'dependency' 	=> [
					[
						'option' 	=> 'values_type',
						'value' 	=> [ 'text' ]
					]
				]
			]
		);

		$label->add_option(
			'label_width',
			[
				'type' 			=> 'number_unit',
				'default' 		=> '80px',
				'units' 		=> BaseSchema::get_units(),
				'title'			=> esc_html__( 'Width' ),
				'css_style' 	=> [
					[
						'selector' 	=> '{{ELEMENT}} .zuwoo-stars-value',
						'value' 	=> 'min-width: {{VALUE}}'
					]
				]
			]
		);


		/*************************
		 * Progress Bar
		 ************************/
		$pb = $options->add_group(
			'progress_bar',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> esc_html__('Progress Bar', 'ziultimate'),
				'collapsed' => true
			]
		);

		$pb->add_option(
			'bar_type',
			[
				'type' 		=> 'select',
				'default' 	=> 'bar',
				'title' 	=> esc_html__('Indicator Type', 'ziultimate'),
				'options' 	=> [
					[
						'id' 	=> 'bar',
						'name' 	=> __('Bar', "ziultimate"),
					],
					[
						'id' 	=> 'star',
						'name' 	=> __('Star', "ziultimate")
					],
				]
			]
		);

		$pb->add_option(
			'primary_color',
			[
				'type' 		=> 'colorpicker',
				'title'		=> __('Primary Color', 'ziultimate'),
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .rating-bar-wrap',
						'value' 	=> 'background-color: {{VALUE}}'
					]
				],
				'dependency' 	=> [
					[
						'option' 	=> 'bar_type',
						'value' 	=> [ 'bar' ]
					]
				]
			]
		);

		$pb->add_option(
			'secondary_color',
			[
				'type' 		=> 'colorpicker',
				'title'		=> __('Secondary Color', 'ziultimate'),
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .zuwoo-perc-rating',
						'value' 	=> 'background-color: {{VALUE}}'
					]
				],
				'dependency' 	=> [
					[
						'option' 	=> 'bar_type',
						'value' 	=> [ 'bar' ]
					]
				]
			]
		);

		$pb->add_option(
			'bar_height',
			[
				'type' 		=> 'slider',
				'content' 	=> 'px',
				'default' 	=> 21,
				'min' 		=> 0,
				'max' 		=> 100,
				'title'		=> __('Height', 'ziultimate'),
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .zuwoo-review-row > span',
						'value' 	=> 'height: {{VALUE}}px'
					],
					[
						'selector' 	=> '{{ELEMENT}} .zuwoo-review-row .rating-bar-wrap',
						'value' 	=> 'height: {{VALUE}}px'
					],
					[
						'selector' 	=> '{{ELEMENT}} .zuwoo-review-row .zuwoo-perc-rating',
						'value' 	=> 'height: {{VALUE}}px'
					],
					[
						'selector' 	=> '{{ELEMENT}} .zuwoo-review-row > span',
						'value' 	=> 'line-height: {{VALUE}}px'
					],
					[
						'selector' 	=> '{{ELEMENT}} .zuwoo-review-row .rating-bar-wrap',
						'value' 	=> 'line-height: {{VALUE}}px'
					],
					[
						'selector' 	=> '{{ELEMENT}} .zuwoo-review-row .zuwoo-perc-rating',
						'value' 	=> 'line-height: {{VALUE}}px'
					]
				],
				'dependency' 	=> [
					[
						'option' 	=> 'bar_type',
						'value' 	=> [ 'bar' ]
					]
				]
			]
		);

		$pb->add_option(
			'empty_stars_color',
			[
				'title' => esc_html__( 'Empty Stars Color', 'zionbuilder' ),
				'type'  => 'colorpicker',
				'css_style' 	=> [
					[
						'selector' 	=> '{{ELEMENT}} .star-rating::before',
						'value' 	=> 'color: {{VALUE}}'
					],
				],
				'dependency' 	=> [
					[
						'option' 	=> 'bar_type',
						'value' 	=> [ 'star' ]
					]
				]
			]
		);

		$pb->add_option(
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
						'selector' 	=> '{{ELEMENT}} .star-rating span',
						'value' 	=> 'color: {{VALUE}}'
					],
					[
						'selector' 	=> '{{ELEMENT}} .star-rating span:before',
						'value' 	=> 'color: {{VALUE}}'
					],
				],
				'dependency' 	=> [
					[
						'option' 	=> 'bar_type',
						'value' 	=> [ 'star' ]
					]
				]
			]
		);

		$pb->add_option(
			'stars_size',
			[
				'type' 			=> 'slider',
				'content' 		=> 'em',
				'default' 		=> 1,
				'min' 			=> 0,
				'max' 			=> 10,
				'step' 			=> 0.01,
				'title'			=> esc_html__( 'Stars Size', 'ziultimate' ),
				'css_style' 	=> [
					[
						'selector' 	=> '{{ELEMENT}} .star-rating',
						'value' 	=> 'font-size: {{VALUE}}em'
					]
				],
				'dependency' 	=> [
					[
						'option' 	=> 'bar_type',
						'value' 	=> [ 'star' ]
					]
				]
			]
		);

		$pb->add_option(
			'space_row',
			[
				'type' 		=> 'slider',
				'content' 	=> 'px',
				'default' 	=> 10,
				'min' 		=> 0,
				'max' 		=> 100,
				'title'		=> esc_html__('Gap Between Rows', 'ziultimate'),
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .zuwoo-review-row',
						'value' 	=> 'padding-bottom: {{VALUE}}px'
					],
				]
			]
		);		


		/*************************
		 * Number
		 ************************/
		$number = $options->add_group(
			'rg_number',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> esc_html__('Rating Points', 'ziultimate'),
				'collapsed' => true
			]
		);

		$number->add_option(
			'disable_num',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> __('Disable Rating Points?', 'ziultimate'),
				'default' 	=> false,
				'layout' 	=> 'inline'
			]
		);

		$number->add_option(
			'num_type',
			[
				'type' 		=> 'select',
				'default' 	=> 'num',
				'title' 	=> esc_html__('Type', 'ziultimate'),
				'options' 	=> [
					[
						'id' 	=> 'num',
						'name' 	=> __('Number', "ziultimate")
					],
					[
						'id' 	=> 'perc',
						'name' 	=> __('Percentage', "ziultimate")
					]
				],
				'dependency' 	=> [
					[
						'option' 	=> 'disable_num',
						'value' 	=> [ false ]
					]
				]
			]
		);

		$selector = '.zuwoo-num-reviews';

		$number->add_option(
			'num_wrap_width',
			[
				'type' 			=> 'number_unit',
				'default' 		=> '50px',
				'units' 		=> BaseSchema::get_units(),
				'title'			=> esc_html__( 'Width' ),
				'css_style' 	=> [
					[
						'selector' 	=> '{{ELEMENT}} ' . $selector,
						'value' 	=> 'min-width: {{VALUE}}'
					]
				],
				'dependency' 	=> [
					[
						'option' 	=> 'disable_num',
						'value' 	=> [ false ]
					]
				]
			]
		);

		$label_tg = $number->add_group(
			'hd_label',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> esc_html__('Typography'),
				'dependency' 	=> [
					[
						'option' 	=> 'disable_num',
						'value' 	=> [ false ]
					]
				]
			]
		);

		$this->attach_typography_options( $label_tg, 'num', '{{ELEMENT}} ' . $selector, ['text_decoration', 'text_transform'] );

		/*$num_pad = $number->add_group(
			'num_pad',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> esc_html__('Padding'),
				'dependency' 	=> [
					[
						'option' 	=> 'disable_num',
						'value' 	=> [ false ]
					]
				]
			]
		);

		$this->attach_padding_options( $num_pad, 'numpad', '{{ELEMENT}} ' . $selector );*/
	}

	/**
	 * Loaing the CSS
	 */
	public function enqueue_styles() {
		$this->enqueue_element_style( Utils::get_file_url( 'dist/css/elements/RatingsGraph/frontend.css' ) );
	}

	public function render( $options ) {
		global $wpdb;

		$product_id = $options->get_value('product_id', get_the_ID());
		$product 	= \WC()->product_factory->get_product( $product_id );

		if ( ! $product instanceof \WC_Product ) {
			return;
		}

		$total_reviews = $product->get_review_count();

		for($i = 5; $i >= 1; $i--) {

			$sql = "SELECT comment_post_ID, COUNT( {$wpdb->prefix}comments.comment_ID ) as num 
					FROM {$wpdb->prefix}comments 
					INNER JOIN {$wpdb->prefix}commentmeta 
						ON ( {$wpdb->prefix}comments.comment_ID = {$wpdb->prefix}commentmeta.comment_id ) 
					WHERE ( comment_approved = '1' ) 
						AND comment_post_ID = {$product->get_id()} 
						AND comment_type IN ('review') 
						AND ( ( 
							{$wpdb->prefix}commentmeta.meta_key = 'rating' 
							AND CAST({$wpdb->prefix}commentmeta.meta_value AS SIGNED) = '". absint( $i ). "' 
						) ) 
						AND comment_type != 'order_note' 
						AND  comment_type != 'webhook_delivery' 
					GROUP BY comment_post_ID 
					ORDER BY num DESC";

			$ratings = $wpdb->get_row( $sql );
			$rating_num = empty($ratings) ? 0 : $ratings->num;
			$perc = ($total_reviews == '0') ? 0 : floor( $rating_num / $total_reviews * 100 );

			$values_type = $options->get_value( 'values_type', 'star_txt' );
			$disable_num = $options->get_value( 'disable_num', false );
			$num_type = $options->get_value( 'num_type', 'num' );
			$bar_type = $options->get_value( 'bar_type', 'bar' );
		?>
            <div class="zuwoo-review-row display-<?php echo $bar_type; ?>">
                
				<span class="zuwoo-stars-value vtype-<?php echo $values_type; ?>">
					<?php if( $values_type == 'star_txt') { printf(_n('%s star', '%s stars', $i, 'ziultimate'), $i); } ?>
					<?php 
						if( $values_type == 'num_icon') {
							$label_icon = $options->get_value( 'label_icon' );
							echo $i . '&nbsp;';
							$this->attach_icon_attributes( 'label_icon', $label_icon );
							$this->render_attributes->add( 'label_icon', 'class', 'zu-label-icon' );
							$this->render_tag(
								'span', 
								'label_icon'
							);
						}
					?>
					<?php if( $values_type == 'icon') { echo wc_get_rating_html( $i ); } ?>
					<?php if( $values_type == 'text') { echo wp_kses_post( $options->get_value( 'text_' . $i ) ); } ?>
				</span>

				<?php if( ! $disable_num ): ?>
                	<span class="zuwoo-num-reviews"><?php if( $num_type == "num" ) { echo $rating_num; } ?><?php if( $num_type == "perc" ) { printf('%s %%', $perc); } ?></span>
				<?php endif; ?>

				<span class="zuwoo-rating-bar">
					<?php if( $bar_type == 'bar' ): ?>
						<span class="rating-bar-wrap">
							<span class="zuwoo-perc-rating" style="width: <?php echo $perc; ?>%;"></span>
						</span>
					<?php else: ?>
						<?php echo wc_get_rating_html( $i ); ?>
					<?php endif; ?>
				</span>

            </div>
        <?php
		}
	}
}