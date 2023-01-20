<?php
namespace ZionBuilderPro\Integrations\Metabox\Fields;

use ZionBuilderPro\DynamicContent\BaseField;
use ZionBuilderPro\Integrations\Metabox\Traits\Base;

class Image extends BaseField {
	use Base;

	public function get_category() {
		return [
			BaseField::CATEGORY_IMAGE,
		];
	}

	public function get_id() {
		return 'meta-box-image';
	}

	public function get_allowed_fields() {
		return [
			'image',
			'single_image',
			'file',
			'file_upload',
			'file_advanced',
		];
	}

	public function render( $options ) {
		if ( ! isset( $options['field_name'] ) ) {
			return '';
		}

		$field                                = $options['field_name'];
		list($object_type, $group, $field_id) = explode( self::$option_separator, $field );
		$value                                = '';

		switch ( $object_type ) {
			case 'post':
				$value = rwmb_get_value( $field_id );
				break;
			case 'setting':
				$value = rwmb_get_value( trim( $field_id ), [ 'object_type' => 'setting' ], $group );
				break;
			case 'term':
				$value = rwmb_get_value( trim( $field_id ), [ 'object_type' => 'term' ], get_queried_object_id() );
				break;
		}

		// Single image value
		$value = isset( $value['full_url'] ) ? [ $value ] : $value;

		// Create a list of full urls
		$value = array_values( wp_list_pluck( $value, 'full_url' ) );

		if ( isset( $value[0] ) ) {
			echo $value[0];
		}
	}
}
