<?php
namespace ZiUltimate\Repeater\Providers;

use ZionBuilderPro\Repeater\RepeaterProvider;
use ZionBuilder\Options\Options;
use ZiUltimate\WooHelpers;

class WCCrossSells extends RepeaterProvider {
	public static function get_id() {
		return 'wc_cross_sells';
	}

	public static function get_name() {
		return esc_html__( 'Woo Product Cross-sells', 'ziultimate' );
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
		$config['orderby'] 	= ! empty( $config['orderby'] ) ? $config['orderby'] : 'rand';
		$config['order'] = ! empty( $config['order'] ) ? $config['order'] : 'DESC';

		$query_args = array(
			'posts_per_page' 	=> ! empty( $config['posts_per_page'] ) ? absint( $config['posts_per_page'] ) : 99,
			'post_status' 		=> 'publish',
			'post_type' 		=> 'product',
			'orderby' 			=> $config['orderby'],
			'order' 			=> ! empty( $config['order'] ) ? $config['order'] : 'DESC',
		);

		if( \ZiUltimate\UltimateElements::isBuilderEditor() ) {
			$query_args['posts_per_page'] = ! empty( $config['posts_per_page'] ) ? absint( $config['posts_per_page'] ) : 3;
			$query_args = WooHelpers::generate_wc_query_args( $query_args, $config );
			$this->query = self::perform_custom_query( $query_args );
		} else {
			global $product;

			if ( is_singular( 'product' ) && $product !== false ) {
				$crosssells = array_map( 'absint', $product->get_cross_sell_ids() );
			} elseif( \is_cart() && ! \WC()->cart->is_empty() ) {
				$crosssells = array_map( 'absint', \WC()->cart->get_cross_sells() );
			} else {
				return $this->query;
			}

			$crossprds = wc_products_array_orderby( $crosssells, $config['orderby'], $config['order'] );

			if( $crossprds ) {
				$query_args['post__in'] = $crossprds;
				$query_args = WooHelpers::generate_wc_query_args( $query_args, $config );
				$this->query = self::perform_custom_query( $query_args );
			} else {
				return $this->query;
			}
		}
	}

	public function get_schema() {
		$options_schema = new Options( 'ziultimate/repeater_provider/wc_cross_sells' );

		$options_schema->add_option(
			'posts_per_page',
			[
				'type' 		=> 'number',
				'title' 	=> __('Number of products to show', 'zionbuilder-pro' ),
				'default' 	=> 3
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
						'name' => esc_html__( 'Random', 'zionbuilder-pro' ),
						'id'   => 'rand',
					],
					[
						'name' => esc_html__( 'Title', 'zionbuilder-pro' ),
						'id'   => 'title',
					],
					[
						'name' => esc_html__( 'ID', 'zionbuilder-pro' ),
						'id'   => 'post__in',
					],
					[
						'name' => esc_html__( 'date', 'zionbuilder-pro' ),
						'id'   => 'date',
					],
					[
						'name' => esc_html__( 'modified', 'zionbuilder-pro' ),
						'id'   => 'modified',
					],
					[
						'name' => esc_html__( 'Menu order', 'zionbuilder-pro' ),
						'id'   => 'menu_order',
					],
					[
						'name' => esc_html__( 'price', 'zionbuilder-pro' ),
						'id'   => 'price',
					],
				]
			]
		);

		$options_schema->add_option(
			'order',
			[
				'type'    => 'custom_selector',
				'title'   => esc_html__( 'Order', 'zionbuilder-pro' ),
				'default' => 'desc',
				'options' => [
					[
						'name' => esc_html__( 'Ascending', 'zionbuilder-pro' ),
						'id'   => 'asc',
					],
					[
						'name' => esc_html__( 'Descending', 'zionbuilder-pro' ),
						'id'   => 'desc',
					],
				],
			]
		);

		return $options_schema->get_schema();
	}
}