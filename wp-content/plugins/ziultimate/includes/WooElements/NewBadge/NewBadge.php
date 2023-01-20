<?php
namespace ZiUltimate\WooElements\NewBadge;

use ZiUltimate\UltimateElements;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class NewBadge
 *
 * @package ZiUltimate\WooElements
 */
class NewBadge extends UltimateElements {

    public function get_type() {
		return 'zu_new_badge';
	}

	public function get_name() {
		return __( 'New Badge', 'ziultimate' );
	}

	public function get_keywords() {
		return [ 'new', 'badge' ];
	}

	public function get_category() {
		return $this->zuwoo_elements_category();
	}

	public function options( $options ) 
	{
		$options->add_option(
			'note',
			[
				'type' 		=> 'html',
				'title' 	=> esc_html__('Note', 'ziultimate'),
				'content' 	=> '<p class="description">' . esc_html__( 'Builder editor will show the demo value for editing. You will get correct data at frontend.', 'ziultimate' ) . '</p>',
			]
		);

		$options->add_option(
			'days',
			[
				'type' 		=> 'slider',
				'title' 	=> esc_html__('Days', 'ziultimate'),
				'description' => esc_html__('Show badge if product is less than .. days old', 'bricksultimate'),
				'content' 	=> 'days',
				'min' 		=> 0,
				'max' 		=> 1000,
				'step' 		=> 1,
				'default' 	=> 14,
				'dynamic' 	=> [
					'enabled' => true
				]
			]
		);

		$options->add_option(
			'badge',
			[
				'type' 		=> 'text',
				'title' 	=> esc_html__('Badge', 'ziultimate'),
				'default' 	=> esc_html__('New'),
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

		$options->add_option(
			'el_valid',
			[
				'type' 		=> 'text',
				'default' 	=> 'zu' . self::elVal(),
				'css_class' => 'znpb-checkbox-switch-wrapper__checkbox'
			]
		);

		$options->add_option(
			'product_id',
			[
				'type' 			=> 'text',
				'title' 		=> esc_html__('Product ID', 'ziultimate'),
				'description' 	=> esc_html__('Leave it blank if you are using on single product page or repeater.', 'ziultimate'),
				'dynamic' 		=> [
					'enabled' => true
				]
			]
		);
	}

	public function can_render() {
		if( self::isBuilderEditor() ) {
			return true;
		}

		return $this->isNewProduct( $this->options );
	}

	public function get_wrapper_tag( $options ) {
		return $options->get_value( 'tag', 'div' );
	}

	public function render( $options )
	{
		if( self::isBuilderEditor() ) {
			echo 'new';
			return true;
		}

		if ( $this->isNewProduct( $options ) ) {
			echo $options->get_value( 'badge', 'New' );
		}
	}

	public function isNewProduct( $options ) {
		global $product;

		$product_id = $options->get_value('product_id', false);

		if( empty( $product_id ) || $product_id === false )
			$product_id = get_the_ID();

		if( ! is_object( $product ) ) {
			$product = WC()->product_factory->get_product( $product_id );
		}

		if( $product === false )
			return;

		$newness_in_days 	= $options->get_value( 'days', 14 );
		$newness_timestamp 	= time() - ( 60 * 60 * 24 * $newness_in_days );
		$created 			= strtotime( $product->get_date_created() );
		$is_new 			= $newness_timestamp < $created;

		if ( $is_new )
			return true;
		else
			return false;
	}
}