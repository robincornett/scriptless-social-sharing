<?php

/**
 * Class ScriptlessSocialSharingOutputButtons
 * @since 2.2.0
 */
class ScriptlessSocialSharingOutputButtons extends ScriptlessSocialSharingOutput {

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
}
