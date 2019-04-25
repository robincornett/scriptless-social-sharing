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

		$setting = $this->get_setting();
		$output  = '<div class="scriptlesssocialsharing">';
		if ( $heading ) {
			$output .= $this->heading( $setting['heading'] );
		}
		$output .= '<div class="' . esc_attr( $this->get_button_container_class( $setting ) ) . '">';
		foreach ( $buttons as $button ) {
			$output .= $this->build_link_markup( $button );
		}
		$output .= '</div>';
		$output .= '</div>';

		return wp_kses_post( $output );
	}

	/**
	 * Enqueue CSS files
	 */
	public function load_styles() {
		if ( false === $this->can_do_buttons() ) {
			return;
		}
		$enqueue = new ScriptlessSocialSharingEnqueue( $this->get_setting(), $this->get_available_buttons() );
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
		$buttons     = $this->get_all_buttons();
		$setting     = $this->get_setting();
		$set_buttons = $setting['buttons'];
		if ( $set_buttons ) {
			foreach ( $buttons as $key => $value ) {
				if ( ! isset( $set_buttons[ $value['name'] ] ) || ! $set_buttons[ $value['name'] ] ) {
					unset( $buttons[ $value['name'] ] );
				}
			}
		}
		$attributes = $this->get_attributes();
		if ( ! $attributes['image'] && ! $attributes['pinterest'] ) {
			unset( $buttons['pinterest'] );
		}

		$this->maybe_load_svg();
		$this->buttons = $buttons;

		/**
		 * Note: scriptlesssocialsharing_buttons filter should be used instead of this
		 * filter, due to potential errors with a button being in this array, but not
		 * actually selected for output.
		 */
		return apply_filters( 'scriptlesssocialsharing_default_buttons', $buttons, $attributes );
	}
}
