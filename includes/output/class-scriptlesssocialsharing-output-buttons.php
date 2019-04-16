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
		$this->maybe_load_svg();

		$setting = $this->get_setting();
		$output  = '<div class="scriptlesssocialsharing">';
		if ( $heading ) {
			$output .= $this->heading( $setting['heading'] );
		}
		$buttons_container = 'scriptlesssocialsharing-buttons';
		if ( 'svg' === $setting['icons'] ) {
			$buttons_container = ' scriptlesssocialsharing__buttons';
		}
		$output .= '<div class="' . esc_attr( $buttons_container ) . '">';
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
	 * If the SVG icons are enabled, load them in the footer.
	 * @since 2.4.0
	 */
	public function maybe_load_svg() {
		$setting = $this->get_setting();
		if ( 'svg' !== $setting['icons'] ) {
			return;
		}
		$svg = $this->svg();
		add_action( 'wp_footer', array( $svg, 'load_svg' ) );
		add_filter( 'wp_kses_allowed_html', array( $svg, 'filter_allowed_html' ), 10, 2 );
	}
}
