<?php
namespace ZiUltimate\Repeater\Providers;

use ZionBuilderPro\Repeater\RepeaterProvider;
use ZionBuilder\Options\Options;

class ExtendedQueryBuilder extends RepeaterProvider {
	public static function get_id() {
		return 'extended_query_builder';
	}

	public static function get_name() {
		return esc_html__( 'Extended Query Builder', 'ziultimate' );
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
		$config = isset( $this->config['config'] ) ? $this->config['config'] : [];

		/**************************
		 * Include/exclude posts
		 **************************/
		if( isset( $config['incl_excl'] ) && $config['incl_excl'] == 'exclude' ) {
			$excl_posts = isset( $config['posts_not_in'] ) ? explode( ',', $config['posts_not_in'] ) : [];
			
			if( ! empty( $config['exclude_current_post'] ) && $config['exclude_current_post'] ) {
				array_push( $excl_posts, get_the_ID() );
			}

			$config['post__not_in'] = $excl_posts;
		} elseif( isset( $config['incl_excl'] ) && $config['incl_excl'] == 'include' ) {
			$config['post__in'] = isset( $config['posts_in'] ) ? explode( ',', $config['posts_in'] ) : [];
		} else {
			// do nothing
		}


		/************************
		 * Child posts/pages
		 ***********************/
		if( isset( $config['child_parent'] ) && $config['child_parent'] == 'top_level' ) {
			$config['post_parent'] = 0;
		} elseif( isset( $config['child_parent'] ) && $config['child_parent'] == 'current' ) {
			$config['post_parent'] = get_the_ID();
		} else {
			// do nothing
		}


		/********************
		 * Author params
		 ********************/
		if( isset( $config['author_param'] ) && $config['author_param'] == 'author_page' && is_author() ) {
			$author = get_queried_object();
			$config['author'] = absint( $author->ID );
		} elseif( isset( $config['author_param'] ) && $config['author_param'] == 'post_author' ) {
			$author_id = get_the_author_meta( 'ID' );
			$config['author'] = absint( $author_id );
		} elseif( isset( $config['author_param'] ) && $config['author_param'] == 'include' ) {
			$config['author__in'] = isset( $config['authors_in'] ) ? explode( ',', $config['authors_in'] ) : [];
		} elseif( isset( $config['author_param'] ) && $config['author_param'] == 'exclude' ) {
			$config['author__not_in'] = isset( $config['authors_not_in'] ) ? explode( ',', $config['authors_not_in'] ) : [];
		} else {
			// do nothing
		}

		$this->query = self::perform_custom_query( $config );
	}

	public function get_schema() {
		$options_schema = new Options( 'zionbuilderpro/repeater_provider/extended_query_builder' );

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
				'type' 			=> 'select',
				'title' 		=> __( 'Post type', 'zionbuilder-pro' ),
				'options' 		=> $post_types_list,
				'multiple' 		=> true,
				'placeholder' 	=> __('Select post types', 'zionbuilder-pro' ),
				'filterable' 	=> true
			]
		);

		$options_schema->add_option(
			'post_status',
			[
				'type' 			=> 'select',
				'title' 		=> __( 'Post Status', 'ziultimate' ),
				'default' 		=> 'publish',
				'options' 		=> [
					[
						'name' 	=> esc_html__('publish'),
						'id' 	=> 'publish'
					],
					[
						'name' 	=> esc_html__('pending'),
						'id' 	=> 'pending'
					],
					[
						'name' 	=> esc_html__('draft'),
						'id' 	=> 'draft'
					],
					[
						'name' 	=> esc_html__('future'),
						'id' 	=> 'future'
					],
					[
						'name' 	=> esc_html__('private'),
						'id' 	=> 'private'
					],
					[
						'name' 	=> esc_html__('any'),
						'id' 	=> 'any'
					]
				],
				'multiple' 		=> true,
				'placeholder' 	=> __('Select post status', 'zionbuilder-pro' ),
				'filterable' 	=> true
			]
		);

		$options_schema->add_option(
			'tax_query',
			[
				'title' 	=> __('Taxonomy', 'zionbuilder-pro' ),
				'type' 		=> 'query_builder_taxonomy'
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
			'nopaging',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> __('Show All Posts', 'ziultimate' ),
				'default' 	=> false,
				'layout' 	=> 'inline',
			]
		);

		$options_schema->add_option(
			'posts_per_page',
			[
				'type' 		=> 'number',
				'title' 	=> __('Posts per page', 'zionbuilder-pro' ),
				'default' 	=> 10,
				'dependency' => [
					[
						'option' 	=> 'nopaging',
						'value' 	=> [ false ]
					]
				]
			]
		);

		$options_schema->add_option(
			'offset',
			[
				'type' 		=> 'number',
				'title' 	=> __('Offset', 'ziultimate' ),
				'default' 	=> 0,
				'dependency' => [
					[
						'option' 	=> 'nopaging',
						'value' 	=> [ false ]
					]
				]
			]
		);

		$options_schema->add_option(
			'incl_excl',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> __('Include/Exclude Posts', 'ziultimate' ),
				'default' 	=> 'no',
				'options' 	=> [
					[
						'name' 	=> esc_html__('None'),
						'id' 	=> 'no'
					],
					[
						'name' 	=> esc_html__('Include', 'ziultimate'),
						'id' 	=> 'include'
					],
					[
						'name' 	=> esc_html__('Exclude', 'ziultimate'),
						'id' 	=> 'exclude'
					],
				],
			]
		);

		$options_schema->add_option(
			'posts_in',
			[
				'type' 		=> 'text',
				'title' 	=> __('Include Specific Posts', 'zionbuilder-pro' ),
				'placeholder' => esc_html__(' Enter ids with comma like 2,34,56', 'ziultimate'),
				'dependency' => [
					[
						'option' 	=> 'incl_excl',
						'value' 	=> [ 'include' ]
					]
				]
			]
		);

		$options_schema->add_option(
			'posts_not_in',
			[
				'type' 		=> 'text',
				'title' 	=> __('Exclude Specific Posts', 'zionbuilder-pro' ),
				'placeholder' => esc_html__(' Enter ids with comma like 2,34,56', 'ziultimate'),
				'dependency' => [
					[
						'option' 	=> 'incl_excl',
						'value' 	=> [ 'exclude' ]
					]
				]
			]
		);

		$options_schema->add_option(
			'exclude_current_post',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> __('Exclude Current Post', 'ziultimate' ),
				'default' 	=> false,
				'layout' 	=> 'inline',
				'dependency' => [
					[
						'option' 	=> 'incl_excl',
						'value' 	=> [ 'exclude' ]
					]
				]
			]
		);

		$options_schema->add_option(
			'child_parent',
			[
				'type' 		=> 'select',
				'title' 	=> __('Child/Parent of Post/Page', 'ziultimate' ),
				'default' 	=> 'none',
				'options' 	=> [
					[
						'name' 	=> esc_html__('Default'),
						'id' 	=> 'none'
					],
					[
						'name' 	=> esc_html__('Only top-level entries', 'ziultimate'),
						'id' 	=> 'top_level'
					],
					[
						'name' 	=> esc_html__('Child of current post/page', 'ziultimate'),
						'id' 	=> 'current'
					],
					[
						'name' 	=> esc_html__('Child of specific post/page', 'ziultimate'),
						'id' 	=> 'sp_post'
					],
				],
			]
		);

		$options_schema->add_option(
			'post_parent',
			[
				'type' 		=> 'number',
				'title' 	=> __('Parent Post/Page ID', 'ziultimate' ),
				'dependency' => [
					[
						'option' 	=> 'child_parent',
						'value' 	=> [ 'sp_post' ]
					]
				]
			]
		);

		$options_schema->add_option(
			'author_param',
			[
				'type' 		=> 'select',
				'title' 	=> __('Author', 'ziultimate' ),
				'default' 	=> 'none',
				'options' 	=> [
					[
						'name' 	=> esc_html__('Default'),
						'id' 	=> 'none'
					],
					[
						'name' 	=> esc_html__("Author of current post", 'ziultimate'),
						'id' 	=> 'post_author'
					],
					[
						'name' 	=> esc_html__("Author page", 'ziultimate'),
						'id' 	=> 'author_page'
					],
					[
						'name' 	=> esc_html__('Include specific authors', 'ziultimate'),
						'id' 	=> 'include'
					],
					[
						'name' 	=> esc_html__('Exclude specific authors', 'ziultimate'),
						'id' 	=> 'exclude'
					],
				],
			]
		);

		$options_schema->add_option(
			'authors_in',
			[
				'type' 		=> 'text',
				'title' 	=> __('Author IDs', 'zionbuilder-pro' ),
				'placeholder' => esc_html__(' Enter ids with comma like 2,34,56', 'ziultimate'),
				'dependency' => [
					[
						'option' 	=> 'author_param',
						'value' 	=> [ 'include' ]
					]
				]
			]
		);

		$options_schema->add_option(
			'authors_not_in',
			[
				'type' 		=> 'text',
				'title' 	=> __('Author IDs', 'zionbuilder-pro' ),
				'placeholder' => esc_html__(' Enter ids with comma like 2,34,56', 'ziultimate'),
				'dependency' => [
					[
						'option' 	=> 'author_param',
						'value' 	=> [ 'exclude' ]
					]
				]
			]
		);

		$options_schema->add_option(
			'orderby',
			[
				'title' 	=> __('Order by', 'zionbuilder-pro' ),
				'type' 		=> 'select',
				'default' 	=> 'date',
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