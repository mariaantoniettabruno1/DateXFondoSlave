<?php
namespace ZionBuilderPro\Integrations\Metabox\Traits;

trait Base {
	public static $option_separator = '%%ZBSEP%%';

	public function get_name() {
		return __( 'Meta Box Field', 'zionbuilder-pro' );
	}

	public function get_group() {
		return 'metabox';
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
				'options'     => $this->get_field_options(),
			],
		];
	}

	private function get_field_options() {
		$options           = [];
		$meta_box_registry = \rwmb_get_registry( 'field' );
		$separator         = self::$option_separator;
		$allowed_fields    = $this->get_allowed_fields();

		foreach ( [ 'post', 'term', 'user', 'setting' ] as $object_type ) {
			$mb_fields_registry = $meta_box_registry->get_by_object_type( $object_type );

			foreach ( $mb_fields_registry as $group => $fields ) {
				$groups_to_register = [];

				// Don't proceed if we don't have fields
				if ( ! is_array( $fields ) || count( $fields ) === 0 ) {
					continue;
				}

				// Get the group name
				if ( $object_type === 'post' ) {
					$post_type_object = get_post_type_object( $group );
					$group_name       = $post_type_object ? $post_type_object->labels->name : $group;
				} else {
					$group_name = ucfirst( $group );
				}

				// Add the group label
				$groups_to_register[] = [
					'name'     => $group_name,
					'id'       => "{$object_type}{$separator}{$group}{$separator}",
					'is_label' => true,
				];

				$fields_to_register = [];
				// Register fields
				foreach ( $fields as $field ) {
					if ( ! empty( $allowed_fields ) && ! in_array( $field['type'], $allowed_fields, true ) ) {
						continue;
					}

					$fields_to_register[] = [
						'name' => $field['name'] ? $field['name'] : $field['id'],
						'id'   => "{$object_type}{$separator}{$group}{$separator}{$field['id']}",
					];
				}

				if ( ! empty( $fields_to_register ) ) {
					$options = array_merge( $options, $groups_to_register, $fields_to_register );
				}
			}
		}

		return $options;
	}

	public function get_allowed_fields() {
		return [];
	}

	public function render( $options ) {
		if ( ! isset( $options['field_name'] ) ) {
			return '';
		}

		$field                                = $options['field_name'];
		list($object_type, $group, $field_id) = explode( self::$option_separator, $field );

		switch ( $object_type ) {
			case 'post':
				rwmb_the_value( $field_id );
				break;
			case 'setting':
				rwmb_the_value( trim( $field_id ), [ 'object_type' => 'setting' ], $group );
				break;
			case 'term':
				rwmb_the_value( trim( $field_id ), [ 'object_type' => 'term' ], get_queried_object_id() );
				break;
		}
	}
}
