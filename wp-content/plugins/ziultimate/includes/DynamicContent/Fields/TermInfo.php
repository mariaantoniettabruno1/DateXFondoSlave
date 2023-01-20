<?php
namespace ZiUltimate\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class TermInfo
 *
 * @package ZiUltimate\DynamicContent\Fields
 */
class TermInfo extends BaseField {
	public function get_category() {
		return [ self::CATEGORY_TEXT, self::CATEGORY_LINK ];
	}

	public function get_group() {
		return 'taxonomy';
	}

	public function get_id() {
		return 'term-info';
	}

	public function get_name() {
		return esc_html__( 'Term Info', 'ziultimate' );
	}

	/**
	 * Get Content
	 *
	 * Returns the current term title
	 *
	 * @param mixed $options
	 */
	public function render( $options ) {
		$term = get_queried_object();
 
		if ( ! $term ) {
			return;
		}

		$field = ( empty( $options['field'] ) ? 'name' : $options['field'] );

		if( $field == 'term_meta' ) {
			echo get_term_meta( $term->term_id, $options['meta_field'], true );
			return;
		}

		if( $field == 'link' ) {
			echo get_term_link( $term );
			return;
		}

		echo wp_kses_post( $term->{$field} );
	}

	/**
	 * Return the data for this field used in preview/editor
	 */
	/*public function get_data() {
		
	}*/

	/**
	 * @return array
	 */
	public function get_options() {
		return [
			'field' => [
				'type' 			=> 'select',
				'title' 		=> esc_html__( 'Info to display', 'zionbuilder-pro' ),
				'description' 	=> esc_html__( 'Select the desired info you want to display.', 'zionbuilder-pro' ),
				'dynamic' 		=> false,
				'options' 		=> [
					[
						'id'   => 'name',
						'name' => esc_html__( 'Name', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'description',
						'name' => esc_html__( 'Description', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'term_meta',
						'name' => esc_html__( 'Meta', 'ziultimate' ),
					],
					[
						'id'   => 'count',
						'name' => esc_html__( 'Count', 'ziultimate' ),
					],
					[
						'id'   => 'link',
						'name' => esc_html__( 'Link URL', 'ziultimate' ),
					]
				],
			],
			'meta_field' => [
				'type' 			=> 'text',
				'title' 		=> esc_html__( 'Meta field', 'zionbuilder-pro' ),
				'description' 	=> esc_html__( 'Enter the desired meta field for which you want to display the value.', 'ziultimate' ),
				'dependency' 	=> [
					[
						'option' 	=> 'field',
						'value' 	=> [ 'term_meta' ]
					]
				]
			]
		];
	}
}