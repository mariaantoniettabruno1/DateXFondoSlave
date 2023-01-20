<?php

namespace ZionBuilderPro\Integrations\ACF\Fields;

class AcfFieldTypeLink extends AcfFieldBase {

	/**
	 * Holds the list of all supported ACF field types
	 * @var string[]
	 */
	protected static $supportedTypes = [
		'url',
		'text',
		'link',
		'page_link',
	];

	/**
	 * Retrieve the list of all supported field types
	 * @return array|string[]
	 */
	public static function getSupportedFieldTypes() {
		return self::$supportedTypes;
	}

	public function get_category() {
		return self::CATEGORY_LINK;
	}

	public function get_id() {
		return 'acf-field-link';
	}

	/**
	 * Render the output for this field
	 *
	 * @param mixed $fieldObject
	 */
	public function render( $fieldObject ) {
		//#! Invalid entry, nothing to do here
		if ( empty( $fieldObject[ 'field_name' ] ) ) {
			return;
		}

		$field_object = $this->get_field_value_config( $fieldObject[ 'field_name' ] );
		if ( ! $field_object) {
			return;
		}

		$type = $field_object['type'];
		$value = $field_object['value'];

		//#! Display the output based on type
		switch ( $type ) {
			case 'link':
				if ( is_array( $value ) ) {
					$url    = ( isset( $value[ 'url' ] ) ? $value[ 'url' ] : '' );

					echo esc_url( $url );
				} else {
					echo esc_url( $value );
				}
				break;

			case 'url':
			case 'page_link':
				echo esc_url( $value );
				break;
			default:
				echo $value;
		}
	}
}
