<?php

/**
 * Class to correctly build the Pinterest URL.
 * Class ScriptlessSocialSharingButtonPinterest
 *
 * @since 2.2.0
 */
class ScriptlessSocialSharingButtonPinterest extends ScriptlessSocialSharingButton {

	/**
	 * ScriptlessSocialSharingButtonPinterest constructor.
	 *
	 * @param $button_name
	 * @param $attributes
	 * @param $setting
	 */
	public function __construct( $button_name, $attributes, $setting ) {
		add_filter( 'scriptlesssocialsharing_pinterest_data', array( $this, 'add_pinterest_data' ) );
		add_filter( 'the_content', array( $this, 'hide_pinterest_image' ), 99 );
		add_filter( 'wp_kses_allowed_html', array( $this, 'filter_allowed_html' ), 10, 2 );
		parent::__construct( $button_name, $attributes, $setting );
	}

	/**
	 * Get the button query args.
	 * @ since 3.0.0
	 *
	 * @param $attributes
	 *
	 * @return array
	 */
	protected function get_query_args() {
		$pinterest_img = $this->pinterest_image() ? $this->pinterest_image() : $this->attributes['image'];

		return array(
			'url'         => $this->get_permalink(),
			'media'       => esc_url( $this->get_image_url( $pinterest_img ) ),
			'description' => rawurlencode( rawurldecode( $this->get_pinterest_description() ) ),
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
	 * If a Pinterest specific image is set, add it to the content, but hidden.
	 *
	 * @param $content
	 *
	 * @return string
	 */
	public function hide_pinterest_image( $content ) {
		$pinterest_image  = $this->pinterest_image();
		$pinterest_button = $this->setting['buttons']['pinterest'];
		if ( ! $pinterest_button || ! $pinterest_image ) {
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
