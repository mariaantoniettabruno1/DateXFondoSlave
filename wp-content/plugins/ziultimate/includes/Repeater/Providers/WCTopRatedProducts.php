<?php
namespace ZiUltimate\Repeater\Providers;

use ZionBuilderPro\Repeater\RepeaterProvider;
use ZionBuilder\Options\Options;

class WCTopRatedProducts extends RepeaterProvider {
	public static function get_id() {
		return 'wc_top_rated_product';
	}

	public static function get_name() {
		return esc_html__( 'Top Rated Products', 'ziultimate' );
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

		$query_args = apply_filters(
			'woocommerce_top_rated_products_args',
			array(
				'posts_per_page' => ! empty( $config['posts_per_page'] ) ? absint( $config['posts_per_page'] ) : 3,
				'post_status'    => 'publish',
				'post_type'      => 'product',
				'meta_key'       => '_wc_average_rating',
				'orderby'        => 'meta_value_num',
				'order'          => 'DESC',
				'tax_query'      => WC()->query->get_tax_query(),
			),
			$this
		); // WPCS: slow query ok.

		if( ! empty( $this->config['config']['category_ids'] ) ) {
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

		$this->query = self::perform_custom_query( array_merge( $query_args, $config ) );
	}

	public function get_schema() {
		$options_schema = new Options( 'zionbuilderpro/repeater_provider/wc_top_rated_product' );

		$options_schema->add_option(
			'posts_per_page',
			[
				'type' => 'number',
				'title' => __('Number of products to show', 'zionbuilder-pro' ),
				'default' => 3
			]
		);

		$options_schema->add_option(
			'category_ids',
			[
				'type' 			=> 'text',
				'title' 		=> __('Category(IDs)', 'zionbuilder-pro' ),
				'description' 	=> esc_html__('Comma separated category ids', 'ziultimate'),
			]
		);

		$options_schema->add_option(
			'cat_operator',
			[
				'type' 			=> 'custom_selector',
				'title' 		=> __('Category Operator', 'zionbuilder-pro' ),
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
					]
				]
			]
		);

		return $options_schema->get_schema();
	}
}