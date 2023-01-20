<?php
namespace ZiUltimate\Repeater\Providers;

use ZionBuilderPro\Repeater\RepeaterProvider;
use ZionBuilder\Options\Options;
use ZionBuilderPro\ElementConditions\Rest;

class TermsQueryBuilder extends RepeaterProvider {

	private $temp_queried_object = null;

	public static function get_id() {
		return 'zu_terms_query_builder';
	}

	public static function get_name() {
		return esc_html__( 'Terms Query Builder', 'ziultimate' );
	}

	public function the_item( $index = null ) {
		$real_index = null === $index ? $this->get_real_index() : $index;
		$current_term = $this->get_item_by_index( $real_index );

		global $wp_query;
		$this->temp_queried_object = $wp_query->get_queried_object();
		
		if ( $current_term ) {
			$wp_query->queried_object = $current_term;
		}
	}

	public function reset_item() {
		global $wp_query;

		$wp_query->queried_object = $this->temp_queried_object;
		$this->temp_queried_object = null;
	}

	public function perform_query() {
		$config = isset( $this->config['config'] ) ? $this->config['config'] : [];

		$args = array(
			'taxonomy' 		=> 'category',
			'orderby' 		=> 'name',
			'order' 		=> 'ASC',
			'hide_empty' 	=> true,
			'number' 		=> false,
			'offset' 		=> '',
			'fields' 		=> 'all',
			'hierarchical' 	=> false,
			'pad_counts' 	=> true
		);

		if( isset( $config['child_of'] ) && $config['child_of'] == 'current_term' && is_tax() ) {
			$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
			$config['child_of'] = (! $term) ? false : $term->term_id;
		} elseif( isset( $config['child_of'] ) && $config['child_of'] == 'custom' ) {
			$config['child_of'] = isset( $config['parent_term_id'] ) ? intval( $config['parent_term_id'] ) : false;
		} else {
			$config['child_of'] = false;
		}

		$config['include'] = isset( $config['include_ids'] ) ? explode( ',', $config['include_ids'] ) : '';
		$config['exclude'] = isset( $config['exclude_ids'] ) ? explode( ',', $config['exclude_ids'] ) : '';

		// The Query
		$terms = get_terms( array_merge( $args, $config ) );

		if( $terms ) {
			$this->query = [
				'query' => [],
				'items' => is_array( $terms ) ? $terms : [],
			];
		} else {
			$this->query = [
				'query' => null,
				'items' => [],
			];
		}
	}

	public function get_schema() {
		$options_schema = new Options( 'ziultimate/repeater_provider/terms_query_builder' );

		$options_schema->add_option(
			'preview_note',
			[
				'type' 			=> 'html',
				'title' 		=> esc_html__( 'Notes:', 'ziultimate' ),
				'content' 		=> '<p class="description">' . esc_html__( 'Refresh the builder editor, if preview is not working properly.', 'ziultimate' ) .'</p>'
			]
		);

		$options_schema->add_option(
			'taxonomy',
			[
				'type' 			=> 'select',
				'title' 		=> esc_html__( 'Taxonomy', 'zionbuilder-pro' ),
				'placeholder' 	=> esc_html__( 'Select taxonomy', 'zionbuilder-pro' ),
				'filterable' 	=> true,
				'multiple' 		=> true,
				'options' 		=> ( new Rest() )->get_taxonomies(''),
			]
		);

		$options_schema->add_option(
			'include_ids',
			[
				'type' 			=> 'text',
				'title' 		=> esc_html__('Include terms', 'ziultimate'),
				'description'	=> esc_html__('Enter the term IDs. Apply comma separator for multiple IDs', 'ziultimate')
			]
		);

		$options_schema->add_option(
			'exclude_ids',
			[
				'type' 			=> 'text',
				'title' 		=> esc_html__('Exclude terms', 'ziultimate'),
				'description'	=> esc_html__('Enter the term ID. Apply comma separator for multiple IDs', 'ziultimate')
			]
		);

		$options_schema->add_option(
			'hide_empty',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> esc_html__('Hide empty terms', 'ziultimate'),
				'default' 	=> true,
				'layout' 	=> 'inline'
			]
		);

		$options_schema->add_option(
			'hierarchical',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> esc_html__('Hierarchical', 'ziultimate'),
				'default' 	=> false,
				'layout' 	=> 'inline'
			]
		);

		$options_schema->add_option(
			'parent',
			[
				'type' 			=> 'text',
				'title' 		=> esc_html__('Parent', 'ziultimate'),
				'placeholder'	=> esc_html__('Enter 0 for top level items', 'ziultimate')
			]
		);

		$options_schema->add_option(
			'child_of',
			[
				'type' 			=> 'select',
				'title' 		=> esc_html__('Child Of'),
				'description'	=> esc_html__('Show sub categories of parent category.', 'ziultimate'),
				'default' 		=> '',
				'options' 		=> [
					[
						'id' 	=> '',
						'name' 	=> esc_html__('None')
					],
					[
						'id' 	=> 'current_tax',
						'name' 	=> esc_html__('Current term(taxonomy archive page)', 'ziultimate')
					],
					[
						'id' 	=> 'custom',
						'name' 	=> esc_html__('Custom', 'ziultimate')
					]
				]
			]
		);

		$options_schema->add_option(
			'parent_term_id',
			[
				'type' 			=> 'text',
				'title' 		=> esc_html__('ID'),
				'placeholder'	=> esc_html__('Enter parent term ID.', 'ziultimate'),
				'dependency' 	=> [
					[
						'option' 	=> 'child_of',
						'value' 	=> [ 'custom' ]
					]
				]
			]
		);

		$options_schema->add_option(
			'number',
			[
				'type' 			=> 'text',
				'title' 		=> esc_html__('Number of terms to show', 'ziultimate'),
				'placeholder'	=> esc_html__('How many terms will show per page. Default is all.', 'ziultimate'),
			]
		);

		$options_schema->add_option(
			'offset',
			[
				'type' 			=> 'text',
				'title' 		=> esc_html__('Offset', 'ziultimate'),
				'placeholder'	=> esc_html__('Enter integer value.', 'ziultimate')
			]
		);

		$options_schema->add_option(
			'orderby',
			[
				'type' 			=> 'select',
				'title' 		=> esc_html__('Order by'),
				'default' 		=> 'name',
				'options' 		=> [
					[
						'name' 	=> 'Name',
						'id' 	=> 'name'
					],
					[
						'name' 	=> 'ID',
						'id' 	=> 'id'
					],
					[
						'name' 	=> 'Slug',
						'id' 	=> 'slug__in'
					],
					[
						'name' 	=> 'Include',
						'id' 	=> 'include'
					],
					[
						'name' 	=> 'Count',
						'id' 	=> 'count'
					],
					[
						'name' 	=> 'Parent',
						'id' 	=> 'parent'
					],
					[
						'name' 	=> 'Meta Value',
						'id' 	=> 'meta_value'
					],
					[
						'name' 	=> 'Meta Value Num',
						'id' 	=> 'meta_value_num'
					]
						
				]
			]
		);

		$options_schema->add_option(
			'order',
			[
				'type' 			=> 'select',
				'title' 		=> __('Order'),
				'default' 		=> 'ASC',
				'options' 		=> [
					[
						'name' 	=> 'ASC',
						'id' 	=> 'ASC'
					],
					[
						'name' 	=> 'DESC',
						'id' 	=> 'DESC'
					]
				]
			]
		);

		return $options_schema->get_schema();
	}
}