<?php

/**
 * Class ScriptlessSocialSharingSettingsValidate
 */
class ScriptlessSocialSharingSettingsValidate {

	/**
	 * Validate all of the settings fields.
	 * @param $fields
	 * @param $new_value
	 *
	 * @return mixed
	 */
	public function validate( $fields, $new_value ) {
		foreach ( $fields as $field ) {
			switch ( $field['callback'] ) {
				case 'do_checkbox':
					$new_value[ $field['id'] ] = $this->one_zero( $new_value[ $field['id'] ] );
					break;

				case 'do_select':
					$new_value[ $field['id'] ] = esc_attr( $new_value[ $field['id'] ] );
					break;

				case 'do_number':
					$new_value[ $field['id'] ] = (int) $new_value[ $field['id'] ];
					break;

				case 'do_checkbox_array':
					foreach ( $field['choices'] as $key => $label ) {
						$new_value[ $field['id'] ][ $key ] = $this->one_zero( $new_value[ $field['id'] ][ $key ] );
					}
					break;

				case 'do_radio_buttons':
					$new_value[ $field['id'] ] = is_numeric( $new_value[ $field['id'] ] ) ? (int) $new_value[ $field['id'] ] : esc_attr( $new_value[ $field['id'] ] );
					break;

				case 'do_content_types':
					array_walk_recursive( $new_value[ $field['id'] ], array( $this, 'validate_content_types' ) );
					break;

				case 'do_textarea_field':
					$new_value[ $field['id'] ] = sanitize_textarea_field( $new_value[ $field['id'] ] );
					break;

				default:
					$new_value[ $field['id'] ] = esc_attr( $new_value[ $field['id'] ] );
			}
		}
		$new_value['location'] = false;

		return $new_value;
	}

	/**
	 * Returns a 1 or 0, for all truthy / falsy values.
	 *
	 * Uses double casting. First, we cast to bool, then to integer.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $new_value Should ideally be a 1 or 0 integer passed in
	 *
	 * @return integer 1 or 0.
	 */
	protected function one_zero( $new_value ) {
		return (int) (bool) $new_value;
	}

	/**
	 * Validate multidimensional arrays.
	 * @param $new_value
	 * @param $key
	 */
	protected function validate_content_types( &$new_value, $key ) {
		$new_value = $this->one_zero( $new_value );
	}
}
