<?php

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
		$setting = scriptlesssocialsharing_get_setting();
		if ( ! $setting['buttons']['pinterest'] ) {
			return $content;
		}
		$pinterest_image = $this->pinterest_image();
		if ( ! $pinterest_image ) {
			return $content;
		}
		$alt_text = get_post_meta( $pinterest_image, '_wp_attachment_image_alt', true );

		return $content . wp_get_attachment_image(
			$pinterest_image,
			'large',
			false,
			array(
				'data-pin-media' => 'true',
				'style'          => 'display:none;',
				'alt'            => $alt_text ? $alt_text : the_title_attribute( 'echo=0' ),
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

		if ( 'post' === $context ) {
			$allowed['a']['data-pin-custom']      = true;
			$allowed['a']['data-pin-no-hover']    = true;
			$allowed['a']['data-pin-do']          = true;
			$allowed['a']['data-pin-description'] = true;
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
	 * Convert an image ID into a URL string.
	 *
	 * @param $id
	 *
	 * @return string
	 */
	protected function get_image_url( $id ) {
		$source = wp_get_attachment_image_src( $id, 'large', false );

		return apply_filters( 'scriptlesssocialsharing_image_url', isset( $source[0] ) ? $source[0] : '' );
	}
}
