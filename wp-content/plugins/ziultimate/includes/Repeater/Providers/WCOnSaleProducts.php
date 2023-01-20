<?php
namespace ZiUltimate\Repeater\Providers;

use ZionBuilderPro\Repeater\RepeaterProvider;
use ZionBuilder\Options\Options;
use ZiUltimate\WooHelpers;

class WCOnSaleProducts extends RepeaterProvider {
	public static function get_id() {
		return 'zu_on_sale_products';
	}

	public static function get_name() {
		return esc_html__( 'On-sale Products', 'woocommerce' );
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
			'posts_per_page' 	=> ! empty( $config['posts_per_page'] ) ? absint( $config['posts_per_page'] ) : 3,
			'post_status' 		=> 'publish',
			'post_type' 		=> 'product',
			'no_found_rows' 	=> 1,
			'orderby' 			=> 'date',
			'order' 			=> ! empty( $config['order'] ) ? $config['order'] : 'DESC',
			'meta_query' 		=> array(),
			'tax_query' 		=> array(
				'relation' => 'AND',
			),
		); // WPCS: slow query ok.

		$product_ids_on_sale    = wc_get_product_ids_on_sale();
		$product_ids_on_sale[]  = 0;
		$query_args['post__in'] = $product_ids_on_sale;

		$query_args = WooHelpers::generate_wc_query_args( $query_args, $config );

		$this->query = self::perform_custom_query( $query_args );
	}

	public function get_schema() {
		$options_schema = new Options( 'zionbuilderpro/repeater_provider/zu_on_sale_products' );

		$options_schema->add_option(
			'posts_per_page',
			[
				'type' 		=> 'number',
				'title' 	=> __('Number of products to show', 'woocommerce' ),
				'default' 	=> 3
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

		$options_schema->add_option(
			'outofstock',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> __('Hide out of stock products', 'zionbuilder-pro' ),
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

		$options_schema->add_option(
			'order_by',
			[
				'title' 	=> __('Order by', 'woocommerce' ),
				'type' 		=> 'select',
				'default' 	=> 'date',
				'options' 	=> [
					[
						'name' 	=> __('None', 'zionbuilder-pro' ),
						'id' 	=> 'none'
					],
					[
						'name' 	=> __('Date', 'woocommerce' ),
						'id' 	=> 'date'
					],
					[
						'name' 	=> __('Price', 'woocommerce' ),
						'id' 	=> 'price'
					],
					[
						'name' 	=> __('Random', 'woocommerce' ),
						'id' 	=> 'rand'
					],
					[
						'name' 	=> __('Sales', 'woocommerce' ),
						'id' 	=> 'sales'
					],
				]
			]
		);

		$options_schema->add_option(
			'order',
			[
				'title' 	=> _x( 'Order', 'Sorting order', 'woocommerce' ),
				'type' 		=> 'custom_selector',
				'default' 	=> 'DESC',
				'options' 	=> [
					[
						'name' 	=> 'Ascending',
						'id' 	=> 'ASC'
					],
					[
						'name' 	=> 'Descending',
						'id' 	=> 'DESC'
					],
				]
			]
		);

		return $options_schema->get_schema();
	}
}