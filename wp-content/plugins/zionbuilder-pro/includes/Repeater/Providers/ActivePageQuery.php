<?php

namespace ZionBuilderPro\Repeater\Providers;

use ZionBuilderPro\Repeater\RepeaterProvider;

class ActivePageQuery extends RepeaterProvider {
	public static function get_id() {
		return 'active_page_query';
	}

	public static function get_name() {
		return esc_html__( 'Active Page Query', 'zionbuilder-pro' );
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
		global $wp_query;

		if ( $wp_query && isset( $wp_query->posts ) ) {
			$this->query = [
				'query' => $wp_query,
				'items' => $wp_query->posts,
			];
		}


	}

}