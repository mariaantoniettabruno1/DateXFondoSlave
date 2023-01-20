<?php
namespace ZiUltimate\Repeater\Providers;

use ZionBuilderPro\Repeater\RepeaterProvider;
use ZionBuilder\Options\Options;

class AuthorBoxQueryBuilder extends RepeaterProvider {

	private $temp_queried_object = null;

	public static function get_id() {
		return 'zu_authbox_query_builder';
	}

	public static function get_name() {
		return esc_html__( 'Author Box Query Builder', 'ziultimate' );
	}

	public function the_item( $index = null ) {
		$real_index = null === $index ? $this->get_real_index() : $index;
		$current_term = $this->get_item_by_index( $real_index );

		global $wp_query;
		$this->temp_queried_object = $wp_query->get_queried_object();

		if ( $current_term ) {
			$wp_query->queried_object = $current_term;
		}
	}

	public function reset_query() {
		global $wp_query;

		$wp_query->queried_object = $this->temp_queried_object;
		$this->temp_queried_object = null;
	}

	public function perform_query() {
		global $post;

		if( ! is_a( $post, 'WP_Post') ) {
			return $this->query = [
				'query' => null,
				'items' => [],
			];
		}

		// The Query
		$post_author = get_users( array( 'include' => $post->post_auhtor, 'number' => 1 ) );
		return $this->query = [
			'query' => [],
			'items' => is_array( $post_author ) ? $post_author : [],
		];
	}
}