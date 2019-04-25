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
		$output  = $atts['before'];
		$output .= $this->heading( $atts['heading'] );
		$output .= $atts['inner_before'];
		$output .= $this->add_shortcode_buttons( $atts['buttons'] );
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

		return array(
			'before'       => '<div class="scriptlesssocialsharing">',
			'after'        => '</div>',
			'inner_before' => '<div class="' . $this->get_button_container_class( $setting ) . '">',
			'inner_after'  => '</div>',
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
	private function add_shortcode_buttons( $buttons ) {
		$all_buttons = $this->get_all_buttons();
		$passed      = $buttons ? explode( ',', $buttons ) : array();
		$output      = '';
		foreach ( $all_buttons as $button ) {
			if ( empty( $passed ) || in_array( $button['name'], $passed, true ) ) {
				$output .= $this->build_link_markup( $button );
			}
		}

		return $output;
	}
}
