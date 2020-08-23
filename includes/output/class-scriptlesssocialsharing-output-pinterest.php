<?php

/**
 * This class handles pinterest related functions which are not specific to the button output.
 * Class ScriptlessSocialSharingOutputPinterest
 * @since 3.0.0
 */
class ScriptlessSocialSharingOutputPinterest {

	/**
	 * If a Pinterest specific image is set, add it to the content, but hidden.
	 *
	 * @param $content
	 *
	 * @return string
	 */
	public function hide_pinterest_image( $content ) {
		if ( ! is_main_query() || ! is_singular() ) {
			return $content;
		}
		if ( ! $this->is_pinterest_button_enabled() ) {
			return $content;
		}
		$pinterest_image = $this->pinterest_image();
		if ( ! $pinterest_image ) {
			return $content;
		}

		return $content . wp_get_attachment_image(
			$pinterest_image,
			apply_filters( 'scriptlesssocialsharing_pinterest_image_size', 'large' ),
			false,
			$this->get_image_args( $pinterest_image )
		);
	}

	/**
	 * Get the Pinterest image attributes.
	 *
	 * @param string $pinterest_image
	 * @return array
	 * @since 3.1.0
	 */
	private function get_image_args( $pinterest_image ) {
		$alt_text = get_post_meta( $pinterest_image, '_wp_attachment_image_alt', true );

		return apply_filters(
			'scriptlesssocialsharing_pinterest_image_attributes',
			array(
				'data-pin-media' => 'true',
				'style'          => 'display:none;',
				'alt'            => $alt_text ? $alt_text : the_title_attribute( 'echo=0' ),
				'class'          => 'scriptless__pinterest-image',
			)
		);
	}

	/**
	 * Allow pinterest data attributes on our links.
	 *
	 * @param $allowed array
	 * @param $context string
	 *
	 * @return mixed
	 */
	public function filter_allowed_html( $allowed, $context ) {
		if ( ! $this->is_pinterest_button_enabled() ) {
			return $allowed;
		}

		if ( 'post' === $context ) {
			$allowed['a']['data-pin-custom']      = true;
			$allowed['a']['data-pin-no-hover']    = true;
			$allowed['a']['data-pin-do']          = true;
			$allowed['a']['data-pin-description'] = true;
			$allowed['img']['data-pin-id']        = true;
		}

		return $allowed;
	}

	/**
	 * If a pinterest specific image is set, get the ID.
	 * @return string
	 */
	protected function pinterest_image() {
		return get_post_meta( get_the_ID(), '_scriptlesssocialsharing_pinterest', true );
	}

	/**
	 * Is the Pinterest button enabled in the plugin settings?
	 * @return bool
	 * @since 3.0.0
	 */
	private function is_pinterest_button_enabled() {
		$setting = scriptlesssocialsharing_get_setting( 'buttons' );

		return ! empty( $setting['pinterest'] );
	}
}
