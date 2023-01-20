<?php
namespace ZiUltimate\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;
use ZionBuilderPro\Plugin;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class LoopCounter
 *
 * @package ZiUltimate\DynamicContent\Fields
 */
class LoopCounter extends BaseField {
	public function get_category() {
		return self::CATEGORY_TEXT;
	}

	public function get_group() {
		return 'post';
	}

	public function get_id() {
		return 'loop-counter';
	}

	public function get_name() {
		return esc_html__( 'Loop Counter', 'ziultimate' );
	}

	/**
	 * Get Content
	 *
	 * Returns the current post classs
	 *
	 * @param mixed $options
	 */
	public function render( $options ) {
		$number = (int) Plugin::instance()->repeater->get_active_provider()->get_real_index() + 1 ;
		$leadingZero = isset( $options['zero'] ) ? $options['zero'] : 'no';

		if( $leadingZero == 'yes' && $number < 10 )
			$number = '0' . $number;
		
		echo $number;
	}

	/**
	 * @return array
	 */
	public function get_options() {
		return [
			'zero' => [
				'type' 			=> 'custom_selector',
				'title' 		=> esc_html__( 'Leading Zero', 'ziultimate' ),
				'description' 	=> esc_html__( 'It will add the zero before the number.', 'ziultimate' ),
				'default' 		=> 'no',
				'options' 		=> [
					[
						'id' 	=> 'yes',
						'name' 	=> esc_html__('Yes')
					],
					[
						'id' 	=> 'no',
						'name' 	=> esc_html__('No')
					]
				]
			]
		];
	}
}