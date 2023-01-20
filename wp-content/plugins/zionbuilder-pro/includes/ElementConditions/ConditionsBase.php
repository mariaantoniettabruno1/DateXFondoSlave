<?php

namespace ZionBuilderPro\ElementConditions;

use WP_Post;

class ConditionsBase {
	public static function get_post() {
		global $post;

		if ( $post instanceof WP_Post ) {
			return $post;
		}

		return false;
	}

	public static function get_operators( $operators = [] ) {
		$operators_map = [
			'equals'           => esc_html__( 'equals', 'zionbuilder-pro' ),
			'not_equals'       => esc_html__( 'does not equal', 'zionbuilder-pro' ),
			'starts_with'      => esc_html__( 'starts with', 'zionbuilder-pro' ),
			'ends_with'        => esc_html__( 'ends with', 'zionbuilder-pro' ),
			'contains'         => esc_html__( 'contains', 'zionbuilder-pro' ),
			'does_not_contain' => esc_html__( 'does not contain', 'zionbuilder-pro' ),
			'is_set'           => esc_html__( 'is set', 'zionbuilder-pro' ),
			'is_not_set'       => esc_html__( 'is not set', 'zionbuilder-pro' ),
			'greater'          => esc_html__( 'is greater than', 'zionbuilder-pro' ),
			'lower'            => esc_html__( 'is lower than', 'zionbuilder-pro' ),
			'greater_or_equal' => esc_html__( 'is greater or equal to', 'zionbuilder-pro' ),
			'lower_or_equal'   => esc_html__( 'is lower or equal to', 'zionbuilder-pro' ),
			'is'               => esc_html__( 'is', 'zionbuilder-pro' ),
			'is_not'           => esc_html__( 'is not', 'zionbuilder-pro' ),
			'is_empty'         => esc_html__( 'is empty', 'zionbuilder-pro' ),
			'is_not_empty'     => esc_html__( 'is not empty', 'zionbuilder-pro' ),
			'include'          => esc_html__( 'include', 'zionbuilder-pro' ),
			'does_not_include' => esc_html__( 'does not include', 'zionbuilder-pro' ),
		];

		$operators_options = [];

		foreach ( $operators as $operator ) {
			if ( ! isset( $operators_map[ $operator ] ) ) {
				continue;
			}

			$operators_options[] = [
				'id'   => $operator,
				'name' => $operators_map[ $operator ],
			];
		}

		return $operators_options;
	}

	public static function validate( $config ) {
		$operator      = $config['operator'];
		$saved_value   = $config['saved_value'];
		$current_value = $config['current_value'];

		switch ( $operator ) {
			case 'is_set':
			case 'is_not_empty':
				return ! empty( $current_value );
			case 'is_not_set':
			case 'is_empty':
				return empty( $current_value );
			case 'equals':
			case 'is':
				return $current_value === $saved_value;
			case 'not_equals':
			case 'is_not':
				return $current_value !== $saved_value;
			case 'greater':
				return intval( $current_value ) > intval( $saved_value );
			case 'lower':
				return intval( $current_value ) < intval( $saved_value );
			case 'greater_or_equal':
				return intval( $current_value ) >= intval( $saved_value );
			case 'lower_or_equal':
				return intval( $current_value ) <= intval( $saved_value );
			case 'starts_with':
				return substr( $current_value, 0, strlen( $saved_value ) ) === $saved_value;
			case 'ends_with':
				$string_length = strlen( $saved_value );

				return $string_length === 0 || substr( $current_value, - $string_length ) === $saved_value;
			case 'contains':
				return strstr( $current_value, $saved_value );
			case 'does_not_contain':
				return ! strstr( $current_value, $saved_value );
			case 'include':
				return in_array( $saved_value, $current_value );
			case 'does_not_include':
				return ! in_array( $saved_value, $current_value );
		}

		return false;
	}
}
