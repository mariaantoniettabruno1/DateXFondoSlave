<?php
namespace ZiUltimate\Repeater\Providers;

use ZionBuilderPro\Repeater\RepeaterProvider;
use ZionBuilder\Options\Options;
use ZionBuilderPro\ElementConditions\Rest;

class AdjacentPosts extends RepeaterProvider {

	public static function get_id() {
		return 'zu_adjacent_posts_query_builder';
	}

	public static function get_name() {
		return esc_html__( 'Adjacent Posts Query Builder', 'ziultimate' );
	}

	public function the_item( $index = null ) {
		global $post;

		$current_item = $this->get_item_by_index( $index );

		if ( $current_item ) {
			$post = get_post( $current_item->ID );
			setup_postdata( $current_item );
		}
	}

	public function reset_item() {
		wp_reset_postdata();
	}

	public function perform_query() {
		$config = isset( $this->config['config'] ) ? $this->config['config'] : [];

		$prevpost 		= ( isset( $config['prev_next_post'] ) && $config['prev_next_post'] == 'next' ) ? false : true;
		$excl_terms 	= isset( $config['excluded_terms'] ) ? trim( $config['excluded_terms'] ) : '';
		$in_same_term 	= isset( $config['in_same_term'] ) ? $config['in_same_term'] : false;
		$taxonomy 		= isset( $config['taxonomy'] ) ? $config['taxonomy'] : 'category';

		$adjacent_post = get_adjacent_post( $in_same_term, $excl_terms, $prevpost, $taxonomy );

		return $this->query = [
			'query' => null,
			'items' => is_a( $adjacent_post, 'WP_Post' ) ? [ $adjacent_post ] : [],
		];
	}

	public function get_schema() {
		$options_schema = new Options( 'ziultimate/repeater_provider/adjacent_posts_query_builder' );

		$options_schema->add_option(
			'prev_next_post',
			[
				'type' 			=> 'select',
				'title' 		=> esc_html__('Showing', 'ziultimate' ),
				'options' 		=> [
					[
						'id' 	=> 'prev',
						'name' 	=> esc_html__('Previous Post', 'ziultimate')
					],
					[
						'id' 	=> 'next',
						'name' 	=> esc_html__('Next Post', 'ziultimate')
					]
				],
				'default' 		=> 'prev'
			]
		);

		$options_schema->add_option(
			'in_same_term',
			[
				'type' 			=> 'checkbox_switch',
				'title' 		=> esc_html__('In same term?', 'ziultimate' ),
				'description' 	=> esc_html__('Whether post should be in a same taxonomy term. Default is false.', 'ziultimate'),
				'default' 		=> false,
				'layout'		=> 'inline'
			]
		);

		$options_schema->add_option(
			'taxonomy',
			[
				'type' 			=> 'select',
				'title' 		=> esc_html__( 'Taxonomy', 'zionbuilder-pro' ),
				'placeholder' 	=> esc_html__( 'Select taxonomy', 'zionbuilder-pro' ),
				'filterable' 	=> true,
				'default' 		=> 'category',
				'multiple' 		=> false,
				'options' 		=> ( new Rest() )->get_taxonomies(''),
				'dependency' => [
					[
						'option' 	=> 'in_same_term',
						'value' 	=> [ true ]
					]
				]
			]
		);

		$options_schema->add_option(
			'excluded_terms',
			[
				'type' 			=> 'text',
				'title' 		=> esc_html__('Exclude terms', 'ziultimate'),
				'description'	=> esc_html__('comma-separated list of excluded term IDs', 'ziultimate')
			]
		);

		return $options_schema->get_schema();
	}
}