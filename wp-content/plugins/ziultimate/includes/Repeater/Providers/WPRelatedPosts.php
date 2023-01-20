<?php
namespace ZiUltimate\Repeater\Providers;

use ZionBuilderPro\Repeater\RepeaterProvider;
use ZionBuilder\Options\Options;

class WPRelatedPosts extends RepeaterProvider {
	public static function get_id() {
		return 'wp_related_posts';
	}

	public static function get_name() {
		return esc_html__( 'Related Posts (ZU)', 'ziultimate' );
	}

	public function the_item( $index = null ) {
		global $post;

		$current_item = $this->get_item_by_index( $index );

		if ( $current_item ) {
			$post = get_post( $current_item->ID );
			setup_postdata( $post );
		}
	}

	public function reset_item() {
		wp_reset_postdata();
	}

	public function perform_query() {
		$config 			= isset( $this->config['config'] ) ? $this->config['config'] : [];
		$posts_per_page 	= ! empty( $config['posts_per_page'] ) ? absint( $config['posts_per_page'] ) : 3;
		$pickup 			= ! empty( $config['pick_up'] ) ? $config['pick_up'] : 'default';
		$config['orderby'] 	= ! empty( $config['orderby'] ) ? $config['orderby'] : 'rand';
		$post_type 			= get_post_type( get_the_ID() );

		$query_args = array(
			'post_status' 		=> 'publish',
			'post_type' 		=> $post_type,
			'post__not_in' 		=> [ get_the_ID() ],
			'posts_per_page' 	=> $posts_per_page,
		);

		if( $pickup == 'default' ) {
			$taxonomies = get_object_taxonomies( $post_type );
			$config['tax_query']['relation'] = 'OR';
			$i = 0;
			foreach( $taxonomies as $taxonomy ) {
				if( $taxonomy == 'post_format' )
					continue;

				array_push(
					$config['tax_query'],
					[
						'taxonomy' 	=> $taxonomy,
						'field' 	=> 'term_id',
						'terms' 	=> wp_get_post_terms( get_the_ID(), $taxonomy, array("fields" => "ids") )
					]
				);

				if( $i >= 6 )
					break;

				$i++;
			}
		} else {
			if( isset( $config['tax_query'][0]['taxonomy'] ) && empty( $config['tax_query'][0]['terms'] ) ) {
				$config['tax_query'][0]['terms'] = wp_get_post_terms( get_the_ID(), $config['tax_query'][0]['taxonomy'], array( 'fields' => 'ids' ) );
			}
		}

		$this->query = self::perform_custom_query( array_merge( $query_args, $config ) );
	}

	public function get_schema() {
		$options_schema = new Options( 'zionbuilderpro/repeater_provider/wp_related_posts' );

		$post_types = get_post_types( [ 'public' => true ], 'objects' );

		$post_types_list = [];

		foreach ( $post_types as $name => $post_type ) {
			$post_types_list[] = [
				'id'   => $name,
				'name' => $post_type->label,
			];
		}

		$options_schema->add_option(
			'pick_up',
			[
				'title' 	=> __('Pick up posts from', 'zionbuilder-pro' ),
				'type' 		=> 'select',
				'default' 	=> 'default',
				'options' 	=> [
					[
						'id' 	=> 'default',
						'name' 	=> esc_html__("Current Post Taxonomies"),
					],
					[
						'id' 	=> 'specific',
						'name' 	=> esc_html__("Select a Taxonomy"),
					]
				]
			]
		);

		$options_schema->add_option(
			'post_type',
			[
				'type' 			=> 'select',
				'title' 		=> __( 'Post type', 'zionbuilder-pro' ),
				'options' 		=> $post_types_list,
				'multiple' 		=> true,
				'placeholder' 	=> __('Select post types', 'zionbuilder-pro' ),
				'filterable' 	=> true,
				'dependency' => [
					[
						'option' 	=> 'pick_up',
						'value' 	=> [ 'specific' ]
					]
				]
			]
		);

		$options_schema->add_option(
			'tax_query',
			[
				'title' 	=> __('Taxonomy', 'zionbuilder-pro' ),
				'type' 		=> 'query_builder_taxonomy',
				'dependency' => [
					[
						'option' 	=> 'pick_up',
						'value' 	=> [ 'specific' ]
					]
				]
			]
		);

		$options_schema->add_option(
			'ignore_sticky_posts',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> __('Ignore Sticky Posts', 'ziultimate' ),
				'default' 	=> false,
				'layout' 	=> 'inline',
			]
		);

		$options_schema->add_option(
			'posts_per_page',
			[
				'type' => 'number',
				'title' => __('Number of products to show', 'zionbuilder-pro' ),
				'default' => 3
			]
		);

		$options_schema->add_option(
			'orderby',
			[
				'title' 	=> __('Order by', 'zionbuilder-pro' ),
				'type' 		=> 'select',
				'default' 	=> 'rand',
				'options' 	=> [
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
				'title' 	=> 'Order',
				'type' 		=> 'custom_selector',
				'default' 	=> 'DESC',
				'options' 	=> [
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

		return $options_schema->get_schema();
	}
}