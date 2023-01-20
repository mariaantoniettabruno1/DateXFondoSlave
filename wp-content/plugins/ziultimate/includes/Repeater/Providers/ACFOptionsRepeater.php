<?php
namespace ZiUltimate\Repeater\Providers;

use ZionBuilderPro\Repeater\RepeaterProvider;
use ZionBuilder\Options\Options;
use ZionBuilderPro\Integrations\ACF\AcfRepeaterProvider;

class ACFOptionsRepeater extends AcfRepeaterProvider {
	/**
	 * Altering the default ACF Repeater query
	 */
	public function perform_query() {
		$config = isset( $this->config['config'] ) ? $this->config['config'] : [];
		$options_page = isset( $config['options_page'] ) ? $config['options_page'] : false;

		if ( isset( $config[ 'repeater_field' ] ) ) {
			$key_config = explode( ':', $config['repeater_field'] );
			$field_name = $key_config[0];
			$field_object = \get_field_object( $field_name );

			if ( $field_object && $field_object['name'] ) {
				if (isset( $key_config[1] ) && $key_config[1] === 'repeater_child') {
					$rows = \get_sub_field( $field_object['name'] );
				} elseif( $options_page ) {
					$rows = \get_field( $field_object['name'], 'options' );
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
}