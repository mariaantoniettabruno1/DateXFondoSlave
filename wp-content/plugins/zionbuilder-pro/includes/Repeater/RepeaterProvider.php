<?php

namespace ZionBuilderPro\Repeater;

class RepeaterProvider {
	public static $current_index = 0;

	public $config = null;
	public $query  = [
		'items' => [],
		'query' => [],
	];

	// Looping helpers
	public $active_loops = [];
	public $active_loop  = [];

	public function __construct( $config = null ) {
		if ( $config !== null ) {
			$this->config = $config;

			// Preform the query
			$this->perform_query();
		}
	}

	public function get_query() {
		return $this->query;
	}

	public function perform_query() {
		return [
			'query' => [],
			'items' => [],
		];
	}

	public function get_active_consumer_data( $field = null ) {
		$active_consumer_data = (array) $this->get_active_item();
		if ( $field ) {
			return isset( $active_consumer_data[$field] ) ? $active_consumer_data[$field] : null;
		} else {
			return $active_consumer_data;
		}
	}

	public static function perform_custom_query( $config ) {
		global $wp_query;

		// Check to see if we are in page mode
		if ( is_front_page() && isset( $wp_query->query_vars['page'] ) ) {
			if ( isset( $wp_query->query_vars['paged'] ) ) {
				$config['paged'] = (int) $wp_query->query_vars['page'];
			}
		} elseif ( isset( $wp_query->query_vars['paged'] ) ) {
			$config['paged'] = (int) $wp_query->query_vars['paged'];
		}

		if ( isset( $config['exclude_current_post'] ) && $config['exclude_current_post'] ) {
			$config['post__not_in'] = [ get_the_ID() ];
			unset( $config['exclude_current_post'] );
		}

		$posts_query = new \WP_Query( $config );
		$items       = is_array( $posts_query->posts ) ? $posts_query->posts : [];

		return [
			'query' => $posts_query,
			'items' => $items,
		];

	}

	/**
	 * Get class name.
	 *
	 * Return the name of the current class.
	 * Used to instantiate elements with data.
	 *
	 * @return string The current class name
	 */
	final public function get_class_name() {
		return get_called_class();
	}

	public function get_schema() {
		return [];
	}


	public function start_loop( $loop_config = [] ) {
		$start = isset( $loop_config['start'] ) && $loop_config['start'] !== null ? $loop_config['start'] : 0;
		$end   = isset( $loop_config['end'] ) && $loop_config['end'] !== null ? $loop_config['end'] : count( $this->query['items'] );

		$length = $end - $start;

		$items       = array_slice( $this->query['items'], $start, $length );
		$active_loop = [
			'items' => $items,
			'index' => 0,
			'count' => count( $items ),
			'start' => $start,
			'end'   => $end,
		];

		// Save the last loop if we started a new ones
		if ( ! empty( $this->active_loop ) ) {
			$this->active_loops = $this->active_loop;
		}

		$this->active_loop = $active_loop;
	}

	public function stop_loop() {
		array_pop( $this->active_loops );
		$this->active_loop = end( $this->active_loops );
	}

	public function is_looping() {
		return ! empty( $this->active_loop );
	}

	public function reset_item() {
	}

	/**
	 * Returns the active loop config
	 *
	 * @return bool|array
	 */
	public function get_active_loop() {
		if ( ! empty( $this->active_loop ) ) {
			return $this->active_loop;
		}

		return false;
	}

	public function have_items() {
		return $this->get_active_loop() && $this->get_active_item();
	}

	public function next() {
		if ( $this->get_active_loop() ) {
			$this->active_loop['index'] += 1;
		}
	}

	public function reset_query() {
	}

	public function get_loop_index() {
		$active_loop = $this->get_active_loop();

		if ( $active_loop ) {
			return $active_loop['index'];
		}

		return false;
	}

	public function get_real_index() {
		$active_loop = $this->get_active_loop();

		if ( $active_loop ) {
			$current_index = $active_loop['index'];
			$start_index   = $active_loop['start'];
			return $start_index + $current_index;
		}

		return false;
	}

	public function get_active_item() {
		$active_loop = $this->get_active_loop();

		if ( $active_loop ) {
			$active_index = $active_loop['index'];
			if ( isset( $active_loop['items'][$active_index] ) ) {
				return $active_loop['items'][$active_index];
			}
		}

		return false;
	}

	public function get_item_by_index( $index = null ) {
		if ( null === $index ) {
			return $this->get_active_item();
		}

		$active_loop = $this->get_active_loop();

		if ( $active_loop ) {
			if ( isset( $active_loop['items'][$index] ) ) {
				return $active_loop['items'][$index];
			}
		}

		return false;
	}

}
