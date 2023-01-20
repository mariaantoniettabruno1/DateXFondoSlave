<?php
namespace ZiUltimate\Repeater\Providers;

use ZionBuilderPro\Repeater\RepeaterProvider;
use ZionBuilder\Options\Options;

class UltimateQueryBuilder extends RepeaterProvider {
	public static function get_id() {
		return 'zu_query_builder';
	}

	public static function get_name() {
		return esc_html__( 'Ultimate Query Builder', 'ziultimate' );
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
		$custom_query = [];

		if( ! empty( $config['custom_query'] ) ) {
			try {
				$custom_query = eval( ' ?>' . $config['custom_query'] );
			} catch ( \ParseError $e ) {
				echo $e->getMessage();
			}
		}

		$this->query = self::perform_custom_query( array_merge( $config, $custom_query ) );
	}

	public function get_schema() {
		$options_schema = new Options( 'zionbuilderpro/repeater_provider/zu_query_builder' );

		$options_schema->add_option(
			'custom_query',
			[
				'type' 			=> 'code',
				'title' 		=> esc_html__('WP Query Arguments', 'ziultimate' ),
				'description' 	=> esc_html__( 'You will enter the query arguments in array format for WP_Query class.', 'ziultimate' ),
				'mode' 			=> 'application/x-httpd-php',
			]
		);

		return $options_schema->get_schema();
	}
}