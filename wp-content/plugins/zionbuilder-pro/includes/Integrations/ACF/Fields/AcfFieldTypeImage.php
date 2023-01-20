<?php

namespace ZionBuilderPro\Integrations\ACF\Fields;

class AcfFieldTypeImage extends AcfFieldBase {

	/**
	 * Holds the list of all supported ACF field types
	 * @var string[]
	 */
	protected static $supportedTypes = [
		'image',
	];

	/**
	 * Retrieve the list of all supported field types
	 * @return array|string[]
	 */
	public static function getSupportedFieldTypes() {
		return self::$supportedTypes;
	}

	public function get_category() {
		return self::CATEGORY_IMAGE;
	}

	public function get_id() {
		return 'acf-field-image';
	}

	/**
	 * Render the output for this field
	 *
	 * @param mixed $field_config
	 */
	public function render( $field_config ) {
		//#! Invalid entry, nothing to do here
		if ( ! isset( $field_config['field_name'] ) ) {
			return;
		}

		$field_object = $this->get_field_value_config( $field_config['field_name'] );
		if ( ! $field_object ) {
			return;
		}

		$value = $field_object['value'];

		echo is_scalar( $value ) ? esc_url( $value ) : esc_url( $value['url'] );
	}
}
