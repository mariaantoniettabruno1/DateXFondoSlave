<?php
namespace ZiUltimate\Repeater\Providers;

use ZionBuilder\Plugin;
use ZionBuilderPro\Repeater\RepeaterProvider;
use ZionBuilder\Options\Options;

class WCRecentlyViewed extends RepeaterProvider {
	public static function get_id() {
		return 'zu_recently_viewed_products';
	}

	public static function get_name() {
		return esc_html__( 'Recently Viewed Products list', 'woocommerce' );
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
		$viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', wp_unslash( $_COOKIE['woocommerce_recently_viewed'] ) ) : array();
		$viewed_products = array_reverse( array_filter( array_map( 'absint', $viewed_products ) ) );

		$config = isset( $this->config['config'] ) ? $this->config['config'] : [];

		if( ! self::isBuilderActive() && empty( $viewed_products ) ) {
			return [
				'query' => [],
				'items' => []
			];
		}

		$query_args = array(
			'posts_per_page' 	=> ! empty( $config['posts_per_page'] ) ? absint( $config['posts_per_page'] ) : 10,
			'no_found_rows' 	=> 1,
			'post_status' 		=> 'publish',
			'post_type' 		=> 'product',
			'post__in'			=> $viewed_products,
			'orderby'			=> 'post__in',
		);

		if( self::isBuilderActive() && empty( $viewed_products ) ) {
			unset( $query_args['post__in'] );
		}

		$hide_out_of_stock_items = ! empty( $config['hide_out_of_stock_items'] ) ? $config['hide_out_of_stock_items'] : 'yes';
		if ( 'yes' === $hide_out_of_stock_items ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => 'outofstock',
					'operator' => 'NOT IN',
				),
			); // WPCS: slow query ok.
		}

		$this->query = self::perform_custom_query( array_merge( $query_args, $config ) );
	}

	public function get_schema() {
		$options_schema = new Options( 'zionbuilderpro/repeater_provider/zu_recently_viewed_products' );

		$arrs = [];
		for( $i = 1; $i <= 15; $i++ ) {
			$arrs[$i - 1]['name'] 	= $i;
			$arrs[$i - 1]['id'] 	= $i;
		}

		$options_schema->add_option(
			'posts_per_page',
			[
				'type' 		=> 'select',
				'title' 	=> __('Number of products to show', 'zionbuilder-pro' ),
				'default' 	=> 10,
				'options' 	=> $arrs
			]
		);

		$options_schema->add_option(
			'hide_out_of_stock_items',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> __('Hide out of stock items', 'zionbuilder-pro' ),
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

		return $options_schema->get_schema();
	}

	/**
	 * Will check the builder editor
	 *
	 * @return boolean
	 */
	public static function isBuilderActive() {
		if( isset( $_SERVER['HTTP_REFERER'] ) && strstr( $_SERVER['HTTP_REFERER'], 'zion_builder_active' ) ) {
			return true;
		} elseif( Plugin::$instance->editor->preview->is_preview_mode() ) {
			return true;
		} else {
			return false;
		}
	}
}