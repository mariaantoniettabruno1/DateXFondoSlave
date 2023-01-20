<?php

namespace ZionBuilderPro\ThemeBuilder;

use ZionBuilderPro\ThemeBuilder\Rule;

class WP_Request {
	private $cache = [];

	/**
	 * Holds data for the current request instance
	 *
	 * @var WP_Request|null
	 */
	public static $current_request_instance = null;

	public static function get_current() {
		if ( null === self::$current_request_instance ) {
			self::$current_request_instance = new self();
		}

		return self::$current_request_instance;
	}

	public function __construct( $post_id = null ) {
		$this->id     = get_queried_object_id();
		$this->object = get_queried_object();
	}

	public function validate( $query_data, $value ) {
		$rule_data = explode( Rule::OPTION_ID_SEPARATOR, $query_data );
		$type      = $rule_data[0];

		if ( method_exists( $this, $type ) ) {
			// Remove the type
			array_shift( $rule_data );

			return call_user_func( [ $this, $type ], $rule_data, $value );
		}

		return false;
	}

	public function is_homepage() {
		if ( ! isset( $this->cache['is_homepage'] ) ) {
			$this->cache['is_homepage'] = is_front_page();
		}

		return $this->cache['is_homepage'];
	}

	public function is_404() {
		if ( ! isset( $this->cache['is_404'] ) ) {
			$this->cache['is_404'] = is_404();
		}

		return $this->cache['is_404'];
	}

	public function is_search() {
		if ( ! isset( $this->cache['is_search'] ) ) {
			$this->cache['is_search'] = is_search();
		}

		return $this->cache['is_search'];
	}

	public function get_post_type() {
		if ( ! isset( $this->cache['get_post_type'] ) ) {
			$this->cache['get_post_type'] = get_post_type( $this->id );
		}

		return $this->cache['get_post_type'];
	}

	public function is_singular( $rule_data, $value ) {
		$post_type = null;

		// Check for post type - is_singular/%%ZB_POST_TYPE%%/product
		if ( ! empty( $rule_data[0] ) && $rule_data[0] === Rule::POST_TYPE_PLACEHOLDER ) {
			// Check for post type
			$post_type = $rule_data[1];
		}

		// Check for specific post id is_singular/%%ZB_POST_TYPE%%/product/40
		if ( ! empty( $rule_data[3] ) && is_numeric( $rule_data[3] ) ) {
			// Check for post type

			return is_singular( $post_type ) && in_array( $this->id, $value, true );
		}

		return is_singular( $post_type );
	}

	public function is_archive() {
		if ( ! isset( $this->cache['is_archive'] ) ) {
			$this->cache['is_archive'] = is_archive();
		}

		return $this->cache['is_archive'];
	}
}
