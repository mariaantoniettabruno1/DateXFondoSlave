<?php

namespace ZionBuilderPro\Integrations\ACF\Fields;

class AcfFieldTypeText extends AcfFieldBase {

	/**
	 * Holds the list of all supported ACF field types
	 * @var string[]
	 */
	protected static $supportedTypes = [
		'text',
		'textarea',
		'wysiwyg',
		'number',
		'range',
		'email',
		'password',
		'true_false',
		'file',
		'date_picker',
		'date_time_picker',
		'time_picker',
		'color_picker',
		'oembed',

		'select',
		'button_group',
		'checkbox',
		'radio',
		'taxonomy',
		'user',
	];

	/**
	 * Retrieve the list of all supported field types
	 * @return array|string[]
	 */
	public static function getSupportedFieldTypes() {
		return self::$supportedTypes;
	}

	public function get_category() {
		return self::CATEGORY_TEXT;
	}

	public function get_id() {
		return 'acf-field-text';
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
		if (!$field_object) {
			return;
		}

		$type = $field_object['type'];
		$value = $field_object['value'];

		//#! Display the output based on type
		switch ( $type ) {
			case 'select':
			case 'button_group':
			case 'checkbox':
				$values = (array) $value;

				foreach ($values as $key => $value) {
					if ( isset( $field_object[ 'choices' ] ) && isset( $field_object[ 'choices' ][ $value ] ) ) {
						$values[$key] = $field_object[ 'choices' ][ $value ];
					}
				}

				echo implode( ', ', $values );

				break;

			case 'radio':
				if ( $value && isset( $field_object[ 'choices' ] ) && isset( $field_object[ 'choices' ][ $value ] ) ) {
					echo $field_object[ 'choices' ][ $value ];
				}
				break;

			case 'taxonomy':
				$taxonomy = ( isset( $fieldObject[ 'taxonomy' ] ) ? esc_attr( $fieldObject[ 'taxonomy' ] ) : 'category' );

				//#! Single choice
				if ( is_scalar( $value ) ) {
					$term = get_term( intval( $value ), $taxonomy );
					if ( $term ) {
						echo $term->name;
					}
				} //#! Multiple choices
				else {
					foreach ( $value as $termID ) {
						$term = get_term( $termID, $taxonomy );
						if ( $term ) {
							echo wp_kses_post(
								sprintf( '<p>%s</p>', esc_html( $term->name ) )
							);
						}
					}
				}
				break;

			case 'user':
				echo esc_html( $this->__getUserName( $value ) );
				break;
			case 'oembed':
				// Return the saved value
				\the_field( $fieldObject[ 'field_name' ], null,  false );
				break;
			case 'file':
				if (is_array( $value )) {
					echo $value['title'];
				} else {
					echo $value;
				}
				echo '';
				break;
			default:
				echo $value;

		}
	}

	/**
	 * Utility method to retrieve the user's name
	 *
	 * @param $value
	 *
	 * @return string
	 */
	private function __getUserName( $value ) {
		//#! User object
		if ( $value instanceof \WP_User ) {
			//#! nothing to do here
		} //#! If array
		elseif ( is_array( $value ) ) {
			$value = new \WP_User( $value[ 'ID' ] );
		} //#! If ID
		else {
			$value = new \WP_User( $value );
		}
		$fn          = $value->first_name;
		$ln          = $value->last_name;
		$displayName = $value->display_name;
		$loginName   = $value->user_login;

		if ( ! empty( $fn ) && ! empty( $ln ) ) {
			return sprintf( '%s %s', $fn, $ln );
		}
		if ( ! empty( $displayName ) ) {
			return $displayName;
		}

		return $loginName;
	}

}
