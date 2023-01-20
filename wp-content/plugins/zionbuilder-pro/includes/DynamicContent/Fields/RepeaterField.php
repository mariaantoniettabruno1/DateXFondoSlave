<?php

namespace ZionBuilderPro\DynamicContent\Fields;

use ZionBuilderPro\Plugin;
use ZionBuilderPro\DynamicContent\BaseField;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class RepeaterField
 *
 * @package ZionBuilderPro\DynamicContent\Fields
 */
class RepeaterField extends BaseField {
	public function get_category() {
		return self::CATEGORY_TEXT;
	}

	public function get_group() {
		return 'others';
	}

	public function get_id() {
		return 'repeater-field-text';
	}

	public function get_name() {
		return esc_html__( 'Repeater Field', 'zionbuilder-pro' );
	}

	/**
	 * Get Content
	 *
	 * Returns the data for the current repeater item
	 *
	 * @param mixed $options
	 */
	public function render( $options ) {
		$repeater_field = isset($options['repeater_field']) ? $options['repeater_field'] : null;

		if ( $repeater_field ) {
			$active_repeater_provider = Plugin::instance()->repeater->get_active_provider();

			if ($active_repeater_provider) {
				echo $active_repeater_provider->get_active_consumer_data( $repeater_field );
			}
		}
	}


	/**
	 * @return array
	 */
	public function get_options() {
		return [
			'repeater_field' => [
				'type'        => 'select',
				'title'       => esc_html__( 'Repeater field', 'zionbuilder-pro' ),
				'description' => esc_html__( 'Select the desired repeater field to display.', 'zionbuilder-pro' ),
				'local_callback_method' => 'zb_get_repeater_fields_as_options',
				'placeholder' => esc_html__( 'Select field', 'zionbuilder-pro' ),
				'filterable' => true,
				'addable' => false,
				'options'     => [],
			],
		];
	}
}
