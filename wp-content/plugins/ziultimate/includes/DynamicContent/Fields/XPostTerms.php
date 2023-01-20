<?php
namespace ZiUltimate\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class XPostTerms
 *
 * @package ZiUltimate\DynamicContent\Fields
 */
class XPostTerms extends BaseField {
	public function get_category() {
		return self::CATEGORY_TEXT;
	}

	public function get_group() {
		return 'post';
	}

	public function get_id() {
		return 'x-amount-terms';
	}

	public function get_name() {
		return esc_html__( 'X amount terms', 'ziultimate' );
	}

	/**
	 * Get Content
	 *
	 * Render the current post title
	 *
	 * @param mixed $config
	 */
	public function render( $options ) {
		// Default settings
		$defaults = [
			'taxonomy'  => 'category',
			'separator' => ', ',
			'link'      => true,
			'limit' 	=> 1
		];

		$post      = $GLOBALS['post'];
		$taxonomy  = isset( $options['taxonomy'] ) ? $options['taxonomy'] : $defaults['taxonomy'];
		$separator = isset( $options['separator'] ) ? $options['separator'] : $defaults['separator'];
		$link      = isset( $options['link'] ) ? ( $options['link'] == 'no' ? false : true ) : $defaults['link'];
		$limit     = isset( $options['limit'] ) ? intval( $options['limit'] ) : $options['limit'];

		$terms      = wp_get_object_terms(
			$post->ID,
			$taxonomy,
			[
				'orderby' => isset( $options['orderby'] ) ? $options['orderby'] : 'name',
				'order'   => isset( $options['order'] ) ? $options['order'] : 'ASC',
			]
		);
		$taxonomies = [];

		if ( false !== $terms && ! is_wp_error( $terms ) ) {
			$new_terms = array_splice( $terms, 0, $limit );
			foreach ( $new_terms as $term ) {
				if ( $link ) {
					$taxonomies[] = '<a href="' . esc_attr( get_term_link( $term->term_id, $taxonomy ) ) . '">' . esc_html( $term->name ) . '</a>';
				} else {
					$taxonomies[] = $term->name;
				}
			}
		}

		echo wp_kses_post( implode( $separator, $taxonomies ) );
	}

	/**
	 * @return array
	 */
	public function get_options() {
		return [
			'limit'  => [
				'type'                   => 'text',
				'title'                  => esc_html__( 'Number of terms to show', 'zionbuilder-pro' ),
				'description'            => esc_html__( 'How many terms will show', 'zionbuilder-pro' ),
				'placeholder'            => esc_html__( 'Enter integer value', 'zionbuilder-pro' ),
				'dynamic'                => false,
				'default' 				 => 1,
			],
			'taxonomy'  => [
				'type'                   => 'select',
				'title'                  => esc_html__( 'Taxonomy', 'zionbuilder-pro' ),
				'description'            => esc_html__( 'Select the post taxonomy to show', 'zionbuilder-pro' ),
				'placeholder'            => esc_html__( 'Select taxonomy', 'zionbuilder-pro' ),
				'dynamic'                => false,
				'server_callback_method' => 'get_post_taxonomies',
			],
			'separator' => [
				'type'        => 'text',
				'title'       => esc_html__( 'Taxonomies separator', 'zionbuilder-pro' ),
				'description' => esc_html__( 'Enter the custom separator you want to use.', 'zionbuilder-pro' ),
				'default'     => ', ',
				'dynamic'     => false,
			],
			'link'      => [
				'type'        => 'select',
				'title'       => esc_html__( 'Link taxonomies?', 'zionbuilder-pro' ),
				'description' => esc_html__( 'Specify whether or not we should link the taxonomies.', 'zionbuilder-pro' ),
				'default'     => 'yes',
				'options'     => [
					[
						'id'   => 'yes',
						'name' => esc_html__( 'Yes', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'no',
						'name' => esc_html__( 'No', 'zionbuilder-pro' ),
					],
				],
				'dynamic'     => false,
			],
			'orderby'   => [
				'type'    => 'select',
				'title'   => esc_html__( 'Order by', 'zionbuilder-pro' ),
				'default' => 'name',
				'options' => [
					[
						'id'   => 'name',
						'name' => esc_html__( 'Name', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'slug',
						'name' => esc_html__( 'Slug', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'term_group',
						'name' => esc_html__( 'Term group', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'term_id',
						'name' => esc_html__( 'Term id', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'id',
						'name' => esc_html__( 'ID', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'description',
						'name' => esc_html__( 'Description', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'parent',
						'name' => esc_html__( 'Parent', 'zionbuilder-pro' ),
					],
				],
				'dynamic' => false,
			],
			'order'     => [
				'type'    => 'select',
				'title'   => esc_html__( 'Order', 'zionbuilder-pro' ),
				'default' => 'ASC',
				'options' => [
					[
						'id'   => 'ASC',
						'name' => esc_html__( 'Ascending', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'DESC',
						'name' => esc_html__( 'Descending', 'zionbuilder-pro' ),
					],
				],
				'dynamic' => false,
			],
		];
	}
}