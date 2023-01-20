<?php

namespace ZionBuilderPro\Integrations\ACF\Fields;

use ZionBuilderPro\DynamicContent\BaseField;

/**
 * Class AcfFieldBase
 *
 * Base class for all Zion ACF fields
 *
 * @package ZionBuilderPro\DynamicContent\Fields
 */
class AcfFieldBase extends BaseField {
	const GROUP_NAME = 'ACF';

	public function get_category() {
		return self::CATEGORY_TEXT;
	}

	public function get_group() {
		return self::GROUP_NAME;
	}

	public function get_id() {
		return 'acf-field';
	}

	public function get_name() {
		return esc_html__( 'ACF Field', 'zionbuilder-pro' );
	}

	/**
	 * All derived classes MUST implement this method in order to register their supported types
	 * @return array
	 */
	public static function getSupportedFieldTypes() {
		return [];
	}

	public function get_options() {
		return [
			'field_name' => [
				'type'        => 'select',
				'title'       => esc_html__( 'Field to display', 'zionbuilder-pro' ),
				'description' => esc_html__( 'Select the desired field you want to display.', 'zionbuilder-pro' ),
				'placeholder' => esc_html__( 'Field to display', 'zionbuilder-pro' ),
				'dynamic'     => false,
				'filterable'  => true,
				'options'     => $this->get_acf_fields_options_by_type(),
				'filter_id'   => 'zionbuilderpro/dynamic_data/acf/options',
			],
		];
	}

	function get_acf_fields_options_by_type() {
		// Get ACF groups
		$acf_groups            = \acf_get_field_groups();
		$acf_options_group_ids = [];
		$options               = [];

		if ( function_exists( 'acf_options_page' ) ) {
			// Get a list of pages
			$pages = \acf_options_page()->get_pages();
			foreach ( $pages as $slug => $page ) {
				$groups_with_options_page = \acf_get_field_groups(
					[
						'options_page' => $slug,
					]
				);

				$acf_options_group_ids = array_map(
					function( $options_page_field ) {
						return $options_page_field['ID'];
					},
					$groups_with_options_page
				);
			}
		}

		foreach ( $acf_groups as $acf_group ) {
			$fields = \acf_get_fields( $acf_group['ID'] );

			if ( $fields && count( $fields ) > 0 ) {
				$group_options    = [];
				$has_options_page = in_array( $acf_group['ID'], $acf_options_group_ids );

				foreach ( $fields as $field ) {
					if ( $this->is_supported_field_type( $field['type'] ) ) {
						$option_name = ! empty( $field['label'] ) ? $field['label'] : $field['name'];
						$option_key  = $field['name'];

						$group_options[] = [
							'id'            => $option_key,
							'name'          => $option_name,
							'is_group_item' => true,
						];

						// Check if this is also on options page
						if ( $has_options_page ) {
							// Set the options key
							$option_key = implode( ':', [ $option_key, 'options' ] );

							$group_options[] = [
								'id'            => $option_key,
								'name'          => sprintf( '%s (%s)', $option_name, esc_html__( 'Options page', 'zionbuilder-pro' ) ),
								'is_group_item' => true,
							];
						}
					}
				}

				if ( count( $group_options ) > 0 ) {
					$options[] = [
						'name'     => $acf_group['title'],
						'is_label' => true,
					];

					$options = array_merge( $options, $group_options );
				}
			}
		}

		return $options;
	}

	/**
	 * Returns ACF field object
	 *
	 * If the option is from a post type, we return the post type saved field
	 * If this is from the options page, we return the options page
	 *
	 * @param string $saved_field_key
	 * @return void
	 */
	public function get_field_value_config( $saved_field_key ) {
		$key_config = explode( ':', $saved_field_key );
		$field_name = $key_config[0];

		if ( isset( $key_config[1] ) && $key_config[1] === 'options' ) {
			$field = \get_field_object( $field_name, 'options' );
		} elseif ( isset( $key_config[1] ) && $key_config[1] === 'repeater_child' ) {
			$field = \get_sub_field_object( $field_name );
		} else {
			$field = \get_field_object( $field_name );
		}

		return $field ? $field : false;
	}

	/**
	 * Make sure the provided type is supported
	 *
	 * @param string $type
	 *
	 * @return bool
	 */
	protected function is_supported_field_type( string $type ) {
		if ( in_array( $type, $this->getSupportedFieldTypes() ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Will load the field only if it passes the check
	 *
	 * @return boolean
	 */
	public function can_load() {
		global $post;

		return ( $post ? true : false );
	}
}
