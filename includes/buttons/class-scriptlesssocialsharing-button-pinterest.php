<?php

/**
 * Class to correctly build the Pinterest URL.
 * Class ScriptlessSocialSharingButtonPinterest
 *
 * @since 2.2.0
 */
class ScriptlessSocialSharingButtonPinterest extends ScriptlessSocialSharingButton {

	/**
	 * Get the button query args.
	 * @ since 3.0.0
	 *
	 * @return array
	 */
	protected function get_query_args() {
		$pinterest_img = $this->pinterest_image() ? $this->pinterest_image() : $this->attributes['image'];

		return array(
			'url'         => $this->get_permalink(),
			'media'       => esc_url( $this->get_image_url( $pinterest_img ) ),
			'description' => $this->get_pinterest_description(),
		);
	}

	/**
	 * Get the base part of the URL.
	 * @since 3.0.0
	 *
	 * @return mixed
	 */
	protected function get_url_base() {
		return 'https://pinterest.com/pin/create/button/';
	}

	/**
	 * Get the description for Pinterest.
	 *
	 * @since 2.2.0
	 *
	 * @return string
	 */
	protected function get_pinterest_description() {
		$pinterest_alt = get_post_meta( get_the_ID(), '_scriptlesssocialsharing_description', true );
		if ( $pinterest_alt ) {
			return $pinterest_alt;
		}
		$pinterest_img = $this->pinterest_image() ? $this->pinterest_image() : $this->attributes['image'];
		$pinterest_alt = get_post_meta( $pinterest_img, '_wp_attachment_image_alt', true );

		return $pinterest_alt ? $pinterest_alt : $this->attributes['title'];
	}

	/**
	 * Add Pinterest data pin attributes to the URL markup.
	 * @return string
	 * @since 2.0.0
	 */
	public function add_pinterest_data() {
		return 'data-pin-no-hover="true" data-pin-custom="true" data-pin-do="skip" data-pin-description="' . $this->get_pinterest_description() . '"';
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
		$url    = isset( $source[0] ) ? $source[0] : '';

		/**
		 * Allow the image URL to be changed with a filter.
		 *
		 * @param string       $url    The image URL.
		 * @param int          $id     The image ID.
		 * @param array|false  $source The array from wp_get_attachment_image_src.
		 */
		return apply_filters( 'scriptlesssocialsharing_pinterest_image_url', $url, $id, $source );
	}
}
