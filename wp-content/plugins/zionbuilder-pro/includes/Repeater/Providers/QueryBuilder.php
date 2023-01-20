<?php

namespace ZionBuilderPro\Repeater\Providers;

use ZionBuilderPro\Repeater\RepeaterProvider;
use ZionBuilder\Options\Options;

class QueryBuilder extends RepeaterProvider {
	public static function get_id() {
		return 'query_builder';
	}

	public static function get_name() {
		return esc_html__( 'Query builder', 'zionbuilder-pro' );
	}

	public function reset_item() {
		wp_reset_postdata();
	}

	public function the_item( $index = null ) {
		global $post;

		$current_item = $this->get_item_by_index( $index );

		if ( $current_item ) {
			$post = get_post( $current_item->ID );
			setup_postdata( $post );
		}
	}

	public function perform_query() {
		$config = isset( $this->config['config'] ) ? $this->config['config'] : [];
		$this->query = self::perform_custom_query( $config );
	}

	public function get_schema() {
		$options_schema = new Options( 'zionbuilderpro/repeater_provider/query_builder' );

		$post_types = get_post_types( [ 'public' => true ], 'objects' );

		$post_types_list = [];

		foreach ( $post_types as $name => $post_type ) {
			$post_types_list[] = [
				'id'   => $name,
				'name' => $post_type->label,
			];
		}

		$options_schema->add_option(
			'post_type',
			[
				'type' => 'select',
				'title' => __( 'Post type', 'zionbuilder-pro' ),
				'options' => $post_types_list,
				'multiple' => true,
				'placeholder' => __('Select post types', 'zionbuilder-pro' ),
				'filterable' => true
			]
		);

		$options_schema->add_option(
			'tax_query',
			[
				'title' => __('Taxonomy', 'zionbuilder-pro' ),
				'type' => 'query_builder_taxonomy'
			]
		);


		$options_schema->add_option(
			'posts_per_page',
			[
				'type' => 'number',
				'title' => __('Posts per page', 'zionbuilder-pro' ),
				'default' => 10
			]
		);

		$options_schema->add_option(
			'orderby',
			[
				'title' => __('Order by', 'zionbuilder-pro' ),
				'type' => 'select',
				'default' => 'date',
				'options' => [
					[
						'name' => __('none', 'zionbuilder-pro' ),
						'id' => 'none'
					],
					[
						'name' => __('ID', 'zionbuilder-pro' ),
						'id' => 'ID'
					],
					[
						'name' => __('author', 'zionbuilder-pro' ),
						'id' => 'author'
					],
					[
						'name' => __('title', 'zionbuilder-pro' ),
						'id' => 'title'
					],
					[
						'name' => __('name', 'zionbuilder-pro' ),
						'id' => 'name'
					],
					[
						'name' => __('type', 'zionbuilder-pro' ),
						'id' => 'type'
					],
					[
						'name' => __('date', 'zionbuilder-pro' ),
						'id' => 'date'
					],
					[
						'name' => __('Modified date', 'zionbuilder-pro' ),
						'id' => 'modified'
					],
					[
						'name' => __('parent', 'zionbuilder-pro' ),
						'id' => 'parent'
					],
					[
						'name' => __('Random', 'zionbuilder-pro' ),
						'id' => 'rand'
					],
					[
						'name' => __('Comment count', 'zionbuilder-pro' ),
						'id' => 'comment_count'
					],
				]
			]
		);

		$options_schema->add_option(
			'order',
			[
				'title' => 'Order',
				'type' => 'custom_selector',
				'default' => 'DESC',
				'options' => [
					[
						'name' => 'Ascending',
						'id' => 'ASC'
					],
					[
						'name' => 'Descending',
						'id' => 'DESC'
					],
				]
			]
		);

		$options_schema->add_option(
			'exclude_current_post',
			[
				'type'    => 'checkbox_switch',
				'default' => false,
				'layout'  => 'inline',
				'title'   => esc_html__( 'Exclude current post?', 'zionbuilder-pro' ),
			]
		);


		return $options_schema->get_schema();
	}
}