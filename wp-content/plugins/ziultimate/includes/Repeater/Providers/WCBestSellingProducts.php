<?php
namespace ZiUltimate\Repeater\Providers;

use ZionBuilderPro\Repeater\RepeaterProvider;
use ZionBuilder\Options\Options;

class WCBestSellingProducts extends RepeaterProvider {
	public static function get_id() {
		return 'zu_best_selling_product';
	}

	public static function get_name() {
		return esc_html__( 'Best Selling Products', 'ziultimate' );
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

		$query_args = array(
			'posts_per_page' => ! empty( $config['posts_per_page'] ) ? absint( $config['posts_per_page'] ) : 3,
			'post_status'    => 'publish',
			'post_type'      => 'product',
			'meta_key'       => 'total_sales',
			'orderby'        => 'meta_value_num',
			'order'          => 'DESC',
			'meta_query' 	 => WC()->query->get_meta_query(),
			'tax_query'      => WC()->query->get_tax_query(),
		); // WPCS: slow query ok.

		if( ! empty( $config['category_ids'] ) ) {
			$categories 	= explode( ',', $config['category_ids'] );
			$categories 	= array_map( 'absint', $categories );
			$cat_operator 	= ! empty( $config['cat_operator'] ) ? $config['cat_operator'] : 'IN';
			$query_args['tax_query'][] = [
				'taxonomy'         => 'product_cat',
				'terms'            => $categories,
				'field'            => 'term_taxonomy_id',
				'operator'         => $cat_operator,
				'include_children' => 'AND' === $cat_operator ? false : true,
			];
		}

		$product_visibility_term_ids = wc_get_product_visibility_term_ids();

		$outofstock 	= ! empty( $config['outofstock'] ) ? $config['outofstock'] : 'yes';
		$show_hidden 	= ! empty( $config['show_hidden'] ) ? $config['show_hidden'] : 'no';
		$hide_free 		= ! empty( $config['hide_free'] ) ? $config['hide_free'] : 'yes';

		if ( $show_hidden == 'no' ) {
			$query_args['tax_query'][] = array(
				'taxonomy' => 'product_visibility',
				'field'    => 'term_taxonomy_id',
				'terms'    => is_search() ? $product_visibility_term_ids['exclude-from-search'] : $product_visibility_term_ids['exclude-from-catalog'],
				'operator' => 'NOT IN',
			);
			$query_args['post_parent'] = 0;
		}

		if ( $hide_free == 'yes' ) {
			$query_args['meta_query'][] = array(
				'key'     => '_price',
				'value'   => 0,
				'compare' => '>',
				'type'    => 'DECIMAL',
			);
		}

		if ( 'yes' === $outofstock ) {
			$query_args['tax_query'][] = array(
				array(
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['outofstock'],
					'operator' => 'NOT IN',
				),
			); // WPCS: slow query ok.
		}

		$this->query = self::perform_custom_query( array_merge( $query_args, $config ) );
	}

	public function get_schema() {
		$options_schema = new Options( 'zionbuilderpro/repeater_provider/zu_best_selling_product' );

		$options_schema->add_option(
			'posts_per_page',
			[
				'type' => 'number',
				'title' => __('Number of products to show', 'woocomerce' ),
				'default' => 3
			]
		);

		$options_schema->add_option(
			'category_ids',
			[
				'type' 			=> 'text',
				'title' 		=> __('Category(IDs)', 'ziultimate' ),
				'description' 	=> esc_html__('Comma separated category ids', 'ziultimate'),
			]
		);

		$options_schema->add_option(
			'cat_operator',
			[
				'type' 			=> 'custom_selector',
				'title' 		=> __('Category Operator', 'ziultimate' ),
				'description' 	=> esc_html__('Operator to compare categories', 'ziultimate'),
				'default' 		=> 'IN',
				'options' 		=> [
					[
						'name' 	=> esc_html__( 'IN' ),
						'id' 	=> 'IN'
					],
					[
						'name' 	=> esc_html__( 'AND' ),
						'id' 	=> 'AND'
					],
					[
						'name' 	=> esc_html__( 'NOT IN' ),
						'id' 	=> 'NOT IN'
					]
				]
			]
		);

		$options_schema->add_option(
			'outofstock',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> __('Hide out of stock items', 'woocommerce' ),
				'default' 	=> 'yes',
				'options' 	=> [
					[
						'name' 	=> esc_html__( 'Yes'),
						'id' 	=> 'yes'
					],
					[
						'name' 	=> esc_html__( 'No'),
						'id' 	=> 'no'
					]
				]
			]
		);

		$options_schema->add_option(
			'show_hidden',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> __( 'Show hidden products', 'woocommerce' ),
				'default' 	=> 'no',
				'options' 	=> [
					[
						'name' 	=> esc_html__( 'Yes'),
						'id' 	=> 'yes'
					],
					[
						'name' 	=> esc_html__( 'No'),
						'id' 	=> 'no'
					]
				]
			]
		);

		$options_schema->add_option(
			'hide_free',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> __( 'Hide free products', 'woocommerce' ),
				'default' 	=> 'no',
				'options' 	=> [
					[
						'name' 	=> esc_html__( 'Yes'),
						'id' 	=> 'yes'
					],
					[
						'name' 	=> esc_html__( 'No'),
						'id' 	=> 'no'
					]
				]
			]
		);

		return $options_schema->get_schema();
	}
}