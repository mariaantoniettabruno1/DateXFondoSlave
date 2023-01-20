<?php
namespace ZiUltimate\WooElements\SaleOffer;

use ZiUltimate\UltimateElements;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class SaleOffer
 *
 * @package ZiUltimate\WooElements
 */
class SaleOffer extends UltimateElements {

    public function get_type() {
		return 'zu_sale_offer';
	}

	public function get_name() {
		return __( 'Sale Off Badge', 'ziultimate' );
	}

	public function get_keywords() {
		return [ 'discount', 'offer', 'sale' ];
	}

	public function get_category() {
		return $this->zuwoo_elements_category();
	}

	public function options( $options ) 
	{
		$options->add_option(
			'type',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> esc_html__('Calculate', 'ziultimate'),
				'default' 	=> 'percentage',
				'options' 	=> [
					[
						'name' 	=> esc_html__( 'Percentage', 'ziultimate' ),
						'id' 	=> 'percentage'
					],
					[
						'name' 	=> esc_html__( 'Fixed Rate', 'ziultimate' ),
						'id' 	=> 'fixed'
					]
				]
			]
		);

		$options->add_option(
			'math',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> esc_html__('Math Logic', 'ziultimate'),
				'default' 	=> 'round',
				'options' 	=> [
					[
						'name' 	=> esc_html__( 'Ceil', 'ziultimate' ),
						'id' 	=> 'ceil'
					],
					[
						'name' 	=> esc_html__( 'Floor', 'ziultimate' ),
						'id' 	=> 'floor'
					],
					[
						'name' 	=> esc_html__( 'Round', 'ziultimate' ),
						'id' 	=> 'round'
					]
				],
				'dependency' 	=> [
					[
						'option' 	=> 'type',
						'value' 	=> [ 'percentage' ]
					]
				]
			]
		);

		$options->add_option(
			'before',
			[
				'type' 		=> 'text',
				'title' 	=> esc_html__('Before', 'ziultimate'),
				'placeholder' => esc_html__('Enter before text', 'ziultimate'),
			]
		);

		$options->add_option(
			'after',
			[
				'type' 		=> 'text',
				'title' 	=> esc_html__('After', 'ziultimate'),
				'placeholder' => esc_html__('Enter after text', 'ziultimate'),
			]
		);

		$options->add_option(
			'product_id',
			[
				'type' 		=> 'text',
				'title' 	=> esc_html__('Product ID', 'ziultimate'),
				'description' => esc_html__('Leave it blank if you are using on single product page or repeater.', 'ziultimate'),
				'dynamic' 	=> [
					'enabled' => true
				]
			]
		);

		$options->add_option(
			'tag',
			[
				'type'        => 'select',
				'default'     => 'div',
				'description' => esc_html__( 'Select the HTML tag to use for this element. If you want to add a custom tag, make sure to only use letters and numbers', 'zionbuilder' ),
				'title'       => esc_html__( 'HTML tag', 'zionbuilder' ),
				'addable'     => true,
				'filterable'  => true,
				'options'     => [
					[
						'id'   => 'div',
						'name' => 'Div',
					],
					[
						'id'   => 'p',
						'name' => 'P',
					],
					[
						'id'   => 'span',
						'name' => 'Span',
					]
				],
			]
		);
	}

	public function get_wrapper_tag( $options ) {
		return $options->get_value( 'tag', 'div' );
	}

	public function render( $options )
	{
		global $product;

		$calculate = $options->get_value('type', 'percentage');
		$product_id = $options->get_value('product_id', false);

		if( empty( $product_id ) || $product_id === false )
			$product_id = get_the_ID();

		if( ! is_object( $product ) ) {
			$product = WC()->product_factory->get_product( $product_id );
		}

		if( $product === false )
			return;

		if( $product->get_type() === 'simple' || $product->get_type() === 'external' ) {
			$regular_price = (float) $product->get_regular_price();
			$sale_price = (float) $product->get_sale_price();
		} 

		if( $product->get_type() === 'variable' || $product->get_type() === 'variation' ) {
			$regular_price = (float) $product->get_variation_regular_price( 'min', true );
			$sale_price = (float) $product->get_variation_sale_price( 'min', true );
		}

		$before = $options->get_value('before', false);
		$after = $options->get_value('after', false);

		if( self::isBuilderEditor() && empty ( $sale_price ) ) {
			$sale_price = 10;
			if( $calculate == 'fixed' ) {
				$sales_off_price = wc_price( $sale_price );
			} else {
				$sales_off_price = $sale_price . '%';
			}

			echo ( $before ? wp_kses_post( $before ) . ' ' : '' ) . $sales_off_price . ( $after ? ' ' . wp_kses_post( $after ) : '' );

			return;
		}

		if( empty ( $sale_price ) )
			return;

		if( $calculate == 'fixed' ) {
			$sales_off_price = wc_price( $regular_price - $sale_price );
		} else {
			$math_fn = $options->get_value('math', 'ceil');
			if( $math_fn == 'ceil' )
				$sales_off_price = ceil( 100 - ( $sale_price / $regular_price * 100 ) ) . '%';
			elseif( $math_fn == 'round' )
				$sales_off_price = round( 100 - ( $sale_price / $regular_price  * 100 ) ) . '%';
			else
				$sales_off_price = floor( 100 - ( $sale_price / $regular_price * 100 ) ) . '%';
		}

		echo ( $before ? wp_kses_post( $before ) . ' ' : '' ) . $sales_off_price . ( $after ? ' ' . wp_kses_post( $after ) : '' );
	}
}