<?php

namespace ZionBuilderPro\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;
use ZionBuilder\Settings;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class GlobalGradient
 *
 * Will return a global color from the saved options
 *
 * @package ZionBuilderPro\DynamicContent\Fields
 */
class GlobalGradient extends BaseField {
	public function get_category() {
		return self::CATEGORY_HIDDEN;
	}

	public function get_group() {
		return false;
	}

	public function get_id() {
		return 'global-gradient';
	}

	public function get_name() {
		return esc_html__( 'Global Gradient', 'zionbuilder-pro' );
	}

	public function get_content( $config ) {
		$global_gradients   = Settings::get_global_gradients();
		$gradient_to_return = '';

		if ( is_array( $global_gradients ) ) {
			foreach ( $global_gradients as $key => $value ) {
				if ( isset( $value['id'] ) && $value['id'] === $config['gradient_id'] ) {
					$gradient_to_return = $value['config'];

					break;
				}
			}
		}

		return $gradient_to_return;
	}
}
