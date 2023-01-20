<?php
namespace ZiUltimate\Repeater\Providers;

use ZionBuilderPro\Repeater\RepeaterProvider;
use ZionBuilder\Options\Options;

class WCRelatedProducts extends RepeaterProvider {
	public static function get_id() {
		return 'wc_related_products';
	}

	public static function get_name() {
		return esc_html__( 'Related Products', 'ziultimate' );
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
		/*if( ! is_singular('product') )
			return $this->query;*/

		global $product;

		if ( ! $product ) {
			return $this->query;
		}

		$config = isset( $this->config['config'] ) ? $this->config['config'] : [];
		$posts_per_page = ! empty( $config['posts_per_page'] ) ? absint( $config['posts_per_page'] ) : 3;

		$query_args = array(
			'post__in' 			=> \wc_get_related_products( $product->get_id(), $posts_per_page, $product->get_upsell_ids() ),
			'posts_per_page' 	=> $posts_per_page,
			'post_status' 		=> 'publish',
			'post_type' 		=> 'product',
			'orderby' 			=> 'post__in',
			'order' 			=> ! empty( $config['order'] ) ? $config['order'] : 'DESC',
		);
			
		$this->query = self::perform_custom_query( array_merge( $query_args, $config ) );
	}

	public function get_schema() {
		$options_schema = new Options( 'zionbuilderpro/repeater_provider/wc_related_products' );

		$options_schema->add_option(
			'posts_per_page',
			[
				'type' => 'number',
				'title' => __('Number of products to show', 'zionbuilder-pro' ),
				'default' => 3
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