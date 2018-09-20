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
		$setting  = $this->get_setting();
		$defaults = array(
			'before'       => '<div class="scriptlesssocialsharing">',
			'after'        => '</div>',
			'inner_before' => '<div class="scriptlesssocialsharing-buttons">',
			'inner_after'  => '</div>',
			'heading'      => $setting['heading'],
			'buttons'      => '',
		);
		$atts     = shortcode_atts( $defaults, $atts, 'scriptless' );
		$buttons  = $this->get_available_buttons();
		$passed   = $atts['buttons'] ? explode( ',', $atts['buttons'] ) : array();
		$output   = $atts['before'];
		$output  .= $this->heading( $atts['heading'] );
		$output  .= $atts['inner_before'];
		foreach ( $buttons as $button ) {
			if ( empty( $passed ) || in_array( $button['name'], $passed, true ) ) {
				$output .= $this->build_link_markup( $button );
			}
		}
		$output .= $atts['inner_after'];
		$output .= $atts['after'];

		return $output;
	}
}
