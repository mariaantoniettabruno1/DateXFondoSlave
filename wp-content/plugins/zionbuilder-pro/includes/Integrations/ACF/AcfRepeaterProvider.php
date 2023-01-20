<?php

namespace ZionBuilderPro\Integrations\ACF;

use ZionBuilderPro\Repeater\RepeaterProvider;
use ZionBuilder\Options\Options;

class AcfRepeaterProvider extends RepeaterProvider {
	public static function get_id() {
		return 'acf_repeater';
	}

	public static function get_name() {
		return esc_html__( 'Acf Repeater', 'zionbuilder-pro' );
	}

	public function the_item( $index = null ) {
		$current_item = $this->get_active_item();
		$config = isset( $this->config['config'] ) ? $this->config['config'] : [];
		if ( $current_item && isset( $config[ 'repeater_field' ] ) ) {
			$real_index = null === $index ? $this->get_real_index() : $index;
			\acf_update_loop( 'active', 'i', $real_index );
		}
	}

	public function perform_query() {
		$config = isset( $this->config['config'] ) ? $this->config['config'] : [];

		if ( isset( $config[ 'repeater_field' ] ) ) {
			$key_config = explode( ':', $config['repeater_field'] );
			$field_name = $key_config[0];
			$field_object = \get_field_object( $field_name );
			// $active_loop = acf_get_loop('active');

			if ( $field_object && $field_object['name'] ) {
				if (isset( $key_config[1] ) && $key_config[1] === 'repeater_child') {
					$rows = \get_sub_field( $field_object['name'] );
				} else {
					$rows = \get_field( $field_object['name'] );
				}

				// Set the query for ACF
				\have_rows( $field_name );
				\acf_update_loop('active', 'i', 0);

				$this->query = [
					'query' => [],
					'items' => is_array( $rows ) ? $rows : [],
				];

				return;
			}
		}

		$this->query = [
			'query' => null,
			'items' => [],
		];
	}

	public function reset_query() {
		\acf_remove_loop('active');
	}

	public function get_schema() {
		$options_schema = new Options( 'zionbuilderpro/repeater_provider/acf_repeater' );

		$options_schema->add_option(
			'repeater_field',
			[
				'type'        => 'select',
				'title'       => esc_html__( 'Repeater field', 'zionbuilder-pro' ),
				'placeholder' => esc_html__( 'Select repeater field', 'zionbuilder-pro' ),
				'options' => $this->get_repeater_options_for_select(),
				'filterable'  => true,
				'filter_id' => 'zionbuilderpro/repeater/acf/fields'
			]
		);

		return $options_schema->get_schema();
	}

	public function get_repeater_options_for_select() {
		$repeater_options = [];
		$field_groups     = \acf_get_field_groups();

		foreach ( $field_groups as $field_group ) {
			$fields           = \acf_get_fields( $field_group );
			$repeater_options = array_merge( $repeater_options, $this->get_repeater_options( $fields ) );
		}

		return $repeater_options;
	}

	public function get_repeater_options( $fields, $parent = false ) {
		$options = [];

		foreach ( $fields as $field ) {
			if ( $field[ 'type' ] === 'repeater' ) {
				$options = array_merge( $options, $this->get_repeater_childs( $field, $parent ) );
			}
		}

		return $options;
	}

	public function get_repeater_childs( $field, $parent = false ) {
		$options = [];

		if ( isset( $field[ 'sub_fields' ] ) ) {
			$options = array_merge( $options, $this->get_repeater_options( $field[ 'sub_fields' ], $field[ 'key' ] ) );
		}

		$options[] = [
			'id'   => $field[ 'key' ],
			'name' => $field[ 'label' ],
			'acf_parent' => $parent,
		];

		return $options;
	}
}