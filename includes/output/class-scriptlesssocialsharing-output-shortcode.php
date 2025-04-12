<?php

/**
 * Class ScriptlessSocialSharingOutputShortcode
 * @since 2.2.0
 */
class ScriptlessSocialSharingOutputShortcode extends ScriptlessSocialSharingOutput {

	/**
	 * Create a shortcode to insert sharing buttons within the post content.
	 *
	 * @param $atts
	 *
	 * @return string
	 */
	public function shortcode( $atts ) {
		$atts    = $this->update_attributes( $atts );
		$buttons = $this->get_shortcode_buttons( $atts['buttons'] );
		if ( ! $buttons ) {
			return '';
		}
		wp_print_styles( 'scriptlesssocialsharing' );
		$output  = wp_kses_post( $atts['before'] );
		$output .= $this->heading( $atts['heading'] );
		$output .= wp_kses_post( $atts['inner_before'] );
		$output .= $buttons;
		$output .= $atts['inner_after'];
		$output .= $atts['after'];

		return $output;
	}

	/**
	 * Update the shortcode attributes and merge with defaults.
	 * @since 3.0.0
	 *
	 * @param $atts
	 * @return array
	 */
	private function update_attributes( $atts ) {
		$defaults = $this->get_defaults();

		return shortcode_atts( $defaults, $atts, 'scriptless' );
	}

	/**
	 * Define the shortcode default attributes.
	 * @since 3.0.0
	 *
	 * @return array
	 */
	private function get_defaults() {
		$setting = $this->get_setting();
		$div     = apply_filters( 'scriptlesssocialsharing_buttons_container_element', 'div' );

		return array(
			'before'       => '<div class="scriptlesssocialsharing">',
			'after'        => '</div>',
			'inner_before' => '<' . esc_attr( $div ) . ' class="' . $this->get_button_container_class( $setting ) . '">',
			'inner_after'  => '</' . esc_attr( $div ) . '>',
			'heading'      => $setting['heading'],
			'buttons'      => '',
		);
	}

	/**
	 * Get the buttons to add to the shortcode output.
	 * @since 3.0.0
	 *
	 * @param $buttons
	 * @return string
	 *
	 */
	private function get_shortcode_buttons( $buttons ) {
		$all_buttons = $this->get_all_buttons();
		$passed      = $this->convert_buttons_to_array( $buttons );
		$output      = '';
		$setting     = $this->get_setting( 'buttons' );
		if ( ! $this->can_do_pinterest() ) {
			unset( $all_buttons['pinterest'] );
		}
		foreach ( $all_buttons as $button ) {
			if ( ( empty( $passed ) && ! empty( $setting[ $button['name'] ] ) ) || in_array( $button['name'], $passed, true ) ) {
				$output .= $this->build_link_markup( $button );
			}
		}

		return $output;
	}

	/**
	 * Convert shortcode string of buttons to an array. Block buttons will already be an array.
	 *
	 * @param $buttons
	 *
	 * @return array
	 * @since 3.0.0
	 */
	private function convert_buttons_to_array( $buttons ) {
		if ( ! $buttons ) {
			return array();
		}
		if ( is_array( $buttons ) ) {
			return $buttons;
		}

		return explode( ',', $buttons );
	}
}
