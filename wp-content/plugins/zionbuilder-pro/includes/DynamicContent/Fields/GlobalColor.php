<?php

namespace ZionBuilderPro\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;
use ZionBuilder\Settings;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class GlobalColor
 *
 * Will return a global color from the saved options
 *
 * @package ZionBuilderPro\DynamicContent\Fields
 */
class GlobalColor extends BaseField {
	public function get_category() {
		return self::CATEGORY_HIDDEN;
	}

	public function get_group() {
		return false;
	}

	public function get_id() {
		return 'global-color';
	}

	public function get_name() {
		return esc_html__( 'Global Color', 'zionbuilder-pro' );
	}

	public function get_content( $config ) {
		$global_colors   = Settings::get_global_colors();
		$color_to_return = '';

		if ( is_array( $global_colors ) ) {
			foreach ( $global_colors as $key => $value ) {
				if ( $value['id'] === $config['color_id'] ) {
					$color_to_return = $value['color'];

					break;
				}
			}
		}

		return $color_to_return;
	}

	/**
	 * Return the data for this field used in preview/editor
	 */
	public function get_data() {
		return Settings::get_global_colors();
	}
}
