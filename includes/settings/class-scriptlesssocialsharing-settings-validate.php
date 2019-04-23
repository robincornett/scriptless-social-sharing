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
			if ( ! empty( $field['type'] ) ) {
				$new_value[ $field['id'] ] = $this->type_switcher( $new_value[ $field['id'] ], $field );
			} elseif ( ! empty( $field['callback'] ) ) {
				$new_value[ $field['id'] ] = $this->callback_switcher( $new_value[ $field['id'] ], $field );
			}
		}
		$new_value['location'] = false;

		return $new_value;
	}

	/**
	 * If a field type is defined, validate it accordingly.
	 * @since 3.0.0
	 *
	 * @param $new_value
	 * @param $field
	 *
	 * @return int|string|void
	 */
	protected function type_switcher( $new_value, $field ) {
		switch ( $field['type'] ) {
			case 'checkbox':
				$new_value = $this->one_zero( $new_value );
				break;

			case 'select':
				$new_value = esc_attr( $new_value );
				break;

			case 'number':
				$new_value = (int) $new_value;
				break;

			case 'checkbox_array':
				$choices = $field['choices'];
				if ( is_callable( $choices ) ) {
					$choices = call_user_func( $choices );
				}
				foreach ( $choices as $key => $label ) {
					$new_value[ $key ] = $this->one_zero( $new_value[ $key ] );
				}
				break;

			case 'radio':
				$new_value = is_numeric( $new_value ) ? (int) $new_value : esc_attr( $new_value );
				break;

			case 'textarea':
				$new_value = sanitize_textarea_field( $new_value );
				break;

			default:
				$new_value = esc_attr( $new_value );
				break;
		}

		return $new_value;
	}

	/**
	 * If a field has a custom callback, validate it accordingly.
	 * @since 3.0.0
	 *
	 * @param $new_value
	 * @param $field
	 *
	 * @return mixed
	 */
	protected function callback_switcher( $new_value, $field ) {
		switch ( $field['callback'] ) {
			case 'do_content_types':
				array_walk_recursive( $new_value, array( $this, 'validate_content_types' ) );
				break;

			case 'do_custom_order':
				array_walk_recursive( $new_value, array( $this, 'validate_order' ) );
				break;
		}

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

	/**
	 * Validate the custom button order.
	 *
	 * @since 2.3.0
	 * @param $new_value
	 * @param $key
	 */
	protected function validate_order( &$new_value, $key ) {
		$new_value = (int) $new_value;
	}
}
