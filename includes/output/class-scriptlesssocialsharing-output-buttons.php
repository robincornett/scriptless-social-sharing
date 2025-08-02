<?php

/**
 * Class ScriptlessSocialSharingOutputButtons
 * @since 2.2.0
 */
class ScriptlessSocialSharingOutputButtons extends ScriptlessSocialSharingOutput {

	/**
	 * The array of buttons.
	 *
	 * @var array
	 */
	protected $buttons;

	/**
	 * Return buttons
	 *
	 * @param string $output
	 * @param bool   $heading set the bool to false to output buttons with no heading
	 *
	 * @return string
	 */
	public function do_buttons( $output = '', $heading = true ) {

		if ( ! $this->can_do_buttons() ) {
			return $output;
		}

		$buttons = $this->get_available_buttons();

		if ( ! $buttons ) {
			return $output;
		}
		wp_print_styles( 'scriptlesssocialsharing' );
		$setting = $this->get_setting();
		$output  = '<div class="scriptlesssocialsharing">';
		if ( $heading ) {
			$output .= $this->heading( $setting['heading'] );
		}
		$div     = apply_filters( 'scriptlesssocialsharing_buttons_container_element', 'div' );
		$output .= '<' . esc_attr( $div ) . ' class="' . esc_attr( $this->get_button_container_class( $setting ) ) . '">';
		foreach ( $buttons as $button ) {
			$output .= $this->build_link_markup( $button );
		}
		$output .= '</' . esc_attr( $div ) . '>';
		$output .= '</div>';

		return wp_kses_post( $output );
	}

	/**
	 * Enqueue CSS files
	 */
	public function load_styles() {
		$enqueue = new ScriptlessSocialSharingEnqueue( $this->get_setting(), $this->get_available_buttons(), $this->can_do_buttons() );
		$enqueue->load_styles();
	}

	/**
	 * Create the default buttons
	 * @return array array of buttons/attributes
	 */
	protected function get_available_buttons() {
		if ( isset( $this->buttons ) && is_singular() ) {
			return $this->buttons;
		}
		$buttons     = (array)$this->get_all_buttons();
		$set_buttons = (array)$this->get_setting( 'buttons' );
		if ( $set_buttons ) {
			foreach ( $buttons as $key => $value ) {
				if ( empty( $set_buttons[ $value['name'] ] ) ) {
					unset( $buttons[ $value['name'] ] );
				}
			}
		}
		if ( ! $this->can_do_pinterest() ) {
			unset( $buttons['pinterest'] );
		}

		$this->buttons = $buttons;

		/**
		 * Note: scriptlesssocialsharing_buttons filter should be used instead of this
		 * filter, due to potential errors with a button being in this array, but not
		 * actually selected for output.
		 */
		return apply_filters( 'scriptlesssocialsharing_default_buttons', $buttons, $this->get_attributes() );
	}
}
